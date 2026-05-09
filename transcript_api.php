<?php
// ============================================================
// transcript_api.php  語音轉繁體中文 CRUD API
// 放在 /volume3/web/ 根目錄
// ============================================================
if (session_status() === PHP_SESSION_NONE) session_start();
ob_start();
require_once __DIR__ . '/fan_members/config/config.php';
ob_end_clean();
require_once __DIR__ . '/fan_members/includes/Auth.php';
require_once __DIR__ . '/fan_members/includes/Database.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

// 大檔案轉錄最多等 10 分鐘
set_time_limit(600);
ini_set('memory_limit', '256M');

$user    = Auth::currentUser();
$isLogin = Auth::isLoggedIn();
$isAdmin = $user && $user['role'] === 'admin';
$db      = Database::getInstance();
$action  = $_POST['action'] ?? $_GET['action'] ?? '';

// ── 輔助函式 ────────────────────────────────────────────────

/** 清理純文字輸入（標題、描述等） */
function clean(string $v): string {
    return htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8');
}

/** 判斷是否有修改/刪除權限（本人或 admin） */
function canModify(?array $user, int $ownerId): bool {
    return $user !== null && ((int)$user['id'] === $ownerId || $user['role'] === 'admin');
}

/**
 * 將秒數（浮點）格式化為 SRT 時間碼
 * 例：65.5 → 00:01:05,500
 */
function toSrtTime(float $sec): string {
    $h  = (int)($sec / 3600);
    $m  = (int)(($sec % 3600) / 60);
    $s  = (int)($sec % 60);
    $ms = (int)(round($sec - floor($sec), 3) * 1000);
    return sprintf('%02d:%02d:%02d,%03d', $h, $m, $s, $ms);
}

/**
 * 將 Whisper verbose_json 的 segments 陣列轉成 SRT 字串
 * 格式：
 *   1
 *   00:00:00,000 --> 00:00:05,200
 *   段落文字
 *
 *   2
 *   ...
 */
function buildSRT(array $segments): string {
    $blocks = [];
    foreach ($segments as $i => $seg) {
        $blocks[] = implode("\n", [
            $i + 1,
            toSrtTime((float)$seg['start']) . ' --> ' . toSrtTime((float)$seg['end']),
            trim($seg['text']),
        ]);
    }
    return implode("\n\n", $blocks);
}

/**
 * 將 segments 合併成純文字（保留 Whisper 產生的標點符號）
 */
function buildPlain(array $segments): string {
    return implode('', array_map(fn($s) => trim($s['text']), $segments));
}

/**
 * 呼叫 OpenAI Whisper API
 * 回傳 verbose_json（含 segments 時間軸）；出錯時回傳 ['_error' => '訊息']
 *
 * config.php 需加入：define('OPENAI_API_KEY', 'sk-...');
 */
function callWhisper(string $filePath, string $origName, string $mime): array {
    if (!defined('OPENAI_API_KEY') || !OPENAI_API_KEY) {
        return ['_error' => '未設定 OPENAI_API_KEY，請在 fan_members/config/config.php 加入：define("OPENAI_API_KEY", "sk-...");'];
    }

    // 使用 CURLFile 上傳音檔
    $cfile = new CURLFile($filePath, $mime, $origName);
    $ch    = curl_init('https://api.openai.com/v1/audio/transcriptions');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => [
            'file'                      => $cfile,
            'model'                     => 'whisper-1',
            'language'                  => 'zh',          // 指定中文
            'response_format'           => 'verbose_json', // 含時間軸
            'timestamp_granularities[]' => 'segment',      // segment 層級時間戳
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 300,   // Whisper 最多等 5 分鐘
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . OPENAI_API_KEY],
    ]);

    $resp    = curl_exec($ch);
    $curlErr = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($curlErr) return ['_error' => 'cURL 連線失敗：' . $curlErr];
    $data = json_decode($resp, true);
    if (!$data)              return ['_error' => 'API 回應無法解析（HTTP ' . $httpCode . '）'];
    if (isset($data['error'])) return ['_error' => $data['error']['message'] ?? 'OpenAI API 錯誤'];

    return $data;
}

// ── 主邏輯 ──────────────────────────────────────────────────
try {

// ════════════════════════════════════════════════════════════
// 取得清單（公開，不需登入）
// ════════════════════════════════════════════════════════════
if ($action === 'list') {
    $page   = max(1, (int)($_GET['page'] ?? 1));
    $limit  = 12;
    $offset = ($page - 1) * $limit;

    $total = (int)$db->query('SELECT COUNT(*) FROM transcripts WHERE status="done"')->fetchColumn();
    $stmt  = $db->prepare(
        'SELECT t.id, t.title, t.description, t.orig_filename, t.file_size,
                t.views, t.created_at, t.member_id,
                m.display_name AS author_name
         FROM transcripts t
         LEFT JOIN members m ON m.id = t.member_id
         WHERE t.status = "done"
         ORDER BY t.created_at DESC
         LIMIT ? OFFSET ?'
    );
    $stmt->execute([$limit, $offset]);

    echo json_encode([
        'success'       => true,
        'data'          => $stmt->fetchAll(PDO::FETCH_ASSOC),
        'total'         => $total,
        'page'          => $page,
        'pages'         => max(1, (int)ceil($total / $limit)),
        'canUpload'     => $isLogin,
        'isAdmin'       => $isAdmin,
        'currentUserId' => $user ? (int)$user['id'] : null,
    ]);
}

// ════════════════════════════════════════════════════════════
// 取得單筆（公開，不需登入）
// ════════════════════════════════════════════════════════════
elseif ($action === 'get') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) { echo json_encode(['error' => '缺少 id 參數']); exit; }

    // 累計瀏覽次數
    $db->prepare('UPDATE transcripts SET views = views + 1 WHERE id = ?')->execute([$id]);

    $stmt = $db->prepare(
        'SELECT t.*, m.display_name AS author_name
         FROM transcripts t
         LEFT JOIN members m ON m.id = t.member_id
         WHERE t.id = ?'
    );
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) { echo json_encode(['error' => '找不到此筆記錄']); exit; }

    // 非登入者隱藏音檔實際路徑（安全考量）
    if (!$isLogin) unset($row['save_filename']);
    $row['canModify'] = canModify($user, (int)$row['member_id']);

    echo json_encode(['success' => true, 'data' => $row]);
}

// ════════════════════════════════════════════════════════════
// 上傳音檔並轉錄（需登入）
// ════════════════════════════════════════════════════════════
elseif ($action === 'upload') {
    if (!$isLogin) { echo json_encode(['error' => '請先登入後再上傳']); exit; }

    $title = clean($_POST['title'] ?? '');
    $desc  = clean($_POST['description'] ?? '');
    if (!$title) { echo json_encode(['error' => '請輸入標題']); exit; }
    if (empty($_FILES['audio']['name'])) { echo json_encode(['error' => '請選擇音訊檔案']); exit; }

    $file     = $_FILES['audio'];
    $origName = $file['name'];
    $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

    // 允許的音訊格式 → MIME 對應
    $mimeMap = [
        'mp3'  => 'audio/mpeg',
        'mp4'  => 'audio/mp4',
        'm4a'  => 'audio/mp4',
        'wav'  => 'audio/wav',
        'ogg'  => 'audio/ogg',
        'webm' => 'audio/webm',
        'flac' => 'audio/flac',
    ];
    if (!isset($mimeMap[$ext])) {
        echo json_encode(['error' => '僅支援：' . implode(', ', array_keys($mimeMap))]); exit;
    }
    // Whisper API 單檔上限 25 MB
    if ($file['size'] > 25 * 1024 * 1024) {
        echo json_encode(['error' => '檔案超過 25MB（OpenAI Whisper API 上限）']); exit;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['error' => '上傳錯誤代碼：' . $file['error']]); exit;
    }

    // 建立上傳目錄（若不存在）
    $uploadDir = __DIR__ . '/uploads/transcripts/';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        echo json_encode(['error' => '無法建立上傳目錄，請檢查伺服器寫入權限']); exit;
    }

    // 隨機檔名，防止猜測
    $saveName = 'tr_' . date('Ymd') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $savePath = $uploadDir . $saveName;

    if (!move_uploaded_file($file['tmp_name'], $savePath)) {
        echo json_encode(['error' => '移動檔案失敗，請確認目錄權限（chmod 755 uploads/transcripts）']); exit;
    }

    // 先寫入資料庫（processing 狀態），取得 ID
    $db->prepare(
        'INSERT INTO transcripts
         (member_id, title, description, orig_filename, save_filename, file_size, status)
         VALUES (?, ?, ?, ?, ?, ?, "processing")'
    )->execute([$user['id'], $title, $desc, $origName, $saveName, $file['size']]);
    $newId = (int)$db->lastInsertId();

    // 呼叫 Whisper API 進行 AI 轉錄
    $result = callWhisper($savePath, $origName, $mimeMap[$ext]);

    if (isset($result['_error'])) {
        // 轉錄失敗：記錄錯誤，但保留音檔
        $db->prepare('UPDATE transcripts SET status = "error", error_msg = ? WHERE id = ?')
           ->execute([$result['_error'], $newId]);
        echo json_encode(['error' => $result['_error'], 'id' => $newId]); exit;
    }

    // 將 segments 轉換為兩種格式
    $segments = $result['segments'] ?? [];
    $fallback = $result['text'] ?? '';          // 無 segments 時的備援
    $srt   = $segments ? buildSRT($segments)   : $fallback;
    $plain = $segments ? buildPlain($segments) : $fallback;

    $db->prepare(
        'UPDATE transcripts SET srt_content = ?, plain_content = ?, status = "done" WHERE id = ?'
    )->execute([$srt, $plain, $newId]);

    echo json_encode(['success' => true, 'id' => $newId]);
}

// ════════════════════════════════════════════════════════════
// 更新標題/描述/轉錄內容（需登入 + 本人或 admin）
// ════════════════════════════════════════════════════════════
elseif ($action === 'update') {
    if (!$isLogin) { echo json_encode(['error' => '請先登入']); exit; }
    $id = (int)($_POST['id'] ?? 0);

    $chk = $db->prepare('SELECT member_id FROM transcripts WHERE id = ?');
    $chk->execute([$id]);
    $rec = $chk->fetch(PDO::FETCH_ASSOC);
    if (!$rec)                                        { echo json_encode(['error' => '找不到此記錄']); exit; }
    if (!canModify($user, (int)$rec['member_id']))    { echo json_encode(['error' => '您沒有修改此筆記錄的權限']); exit; }

    $title = clean($_POST['title'] ?? '');
    $desc  = clean($_POST['description'] ?? '');
    $srt   = $_POST['srt_content']   ?? null;   // 允許手動空白（清空內容）
    $plain = $_POST['plain_content'] ?? null;
    if (!$title) { echo json_encode(['error' => '標題不可空白']); exit; }

    $db->prepare(
        'UPDATE transcripts SET title = ?, description = ?, srt_content = ?, plain_content = ?, updated_at = NOW() WHERE id = ?'
    )->execute([$title, $desc, $srt, $plain, $id]);

    echo json_encode(['success' => true]);
}

// ════════════════════════════════════════════════════════════
// 刪除記錄（需登入 + 本人或 admin）
// ════════════════════════════════════════════════════════════
elseif ($action === 'delete') {
    if (!$isLogin) { echo json_encode(['error' => '請先登入']); exit; }
    $id = (int)($_POST['id'] ?? 0);

    $chk = $db->prepare('SELECT member_id, save_filename FROM transcripts WHERE id = ?');
    $chk->execute([$id]);
    $rec = $chk->fetch(PDO::FETCH_ASSOC);
    if (!$rec)                                        { echo json_encode(['error' => '找不到此記錄']); exit; }
    if (!canModify($user, (int)$rec['member_id']))    { echo json_encode(['error' => '您沒有刪除此筆記錄的權限']); exit; }

    // 刪除磁碟上的音檔
    $filePath = __DIR__ . '/uploads/transcripts/' . $rec['save_filename'];
    if (file_exists($filePath)) @unlink($filePath);

    $db->prepare('DELETE FROM transcripts WHERE id = ?')->execute([$id]);
    echo json_encode(['success' => true]);
}

// ════════════════════════════════════════════════════════════
// 重新轉錄（需登入 + 本人或 admin）
// ════════════════════════════════════════════════════════════
elseif ($action === 'retranscribe') {
    if (!$isLogin) { echo json_encode(['error' => '請先登入']); exit; }
    $id = (int)($_POST['id'] ?? 0);

    $stmt = $db->prepare('SELECT * FROM transcripts WHERE id = ?');
    $stmt->execute([$id]);
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$rec)                                        { echo json_encode(['error' => '找不到此記錄']); exit; }
    if (!canModify($user, (int)$rec['member_id']))    { echo json_encode(['error' => '您沒有操作此筆記錄的權限']); exit; }

    $filePath = __DIR__ . '/uploads/transcripts/' . $rec['save_filename'];
    if (!file_exists($filePath)) { echo json_encode(['error' => '原始音檔已不存在，無法重新轉錄']); exit; }

    $db->prepare('UPDATE transcripts SET status = "processing", error_msg = NULL WHERE id = ?')->execute([$id]);

    // 偵測 MIME 類型
    $ext     = strtolower(pathinfo($rec['save_filename'], PATHINFO_EXTENSION));
    $mimeMap = ['mp3'=>'audio/mpeg','mp4'=>'audio/mp4','m4a'=>'audio/mp4',
                'wav'=>'audio/wav','ogg'=>'audio/ogg','webm'=>'audio/webm','flac'=>'audio/flac'];
    $mime    = $mimeMap[$ext] ?? 'audio/mpeg';
    $result  = callWhisper($filePath, $rec['orig_filename'], $mime);

    if (isset($result['_error'])) {
        $db->prepare('UPDATE transcripts SET status = "error", error_msg = ? WHERE id = ?')
           ->execute([$result['_error'], $id]);
        echo json_encode(['error' => $result['_error']]); exit;
    }

    $segments = $result['segments'] ?? [];
    $fallback = $result['text'] ?? '';
    $srt      = $segments ? buildSRT($segments)   : $fallback;
    $plain    = $segments ? buildPlain($segments) : $fallback;

    $db->prepare(
        'UPDATE transcripts SET srt_content = ?, plain_content = ?, status = "done", updated_at = NOW() WHERE id = ?'
    )->execute([$srt, $plain, $id]);

    echo json_encode(['success' => true]);
}

// ════════════════════════════════════════════════════════════
// 初始化資料表（admin only）
// 可在 phpMyAdmin 執行 transcript_setup.sql，或由 admin 點按「初始化」
// ════════════════════════════════════════════════════════════
elseif ($action === 'setup') {
    if (!$isAdmin) { echo json_encode(['error' => '需要管理員權限']); exit; }

    $db->exec("
        CREATE TABLE IF NOT EXISTS `transcripts` (
            `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `member_id`     INT NOT NULL                    COMMENT '上傳者 members.id',
            `title`         VARCHAR(255) NOT NULL           COMMENT '標題',
            `description`   TEXT                            COMMENT '描述（選填）',
            `orig_filename` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '原始檔名',
            `save_filename` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '磁碟儲存檔名（隨機）',
            `file_size`     INT UNSIGNED DEFAULT 0          COMMENT '音檔大小（bytes）',
            `srt_content`   LONGTEXT                        COMMENT 'SRT 格式（含時間軸）',
            `plain_content` LONGTEXT                        COMMENT '純文字（不含時間軸）',
            `status`        ENUM('pending','processing','done','error') DEFAULT 'pending',
            `error_msg`     TEXT                            COMMENT '錯誤訊息',
            `views`         INT UNSIGNED DEFAULT 0          COMMENT '瀏覽次數',
            `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_member`  (`member_id`),
            INDEX `idx_status`  (`status`),
            INDEX `idx_created` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
          COMMENT='語音轉文字記錄表'
    ");

    echo json_encode(['success' => true, 'msg' => '資料表 transcripts 建立完成']);
}

else {
    echo json_encode(['error' => '未知的 action：' . htmlspecialchars($action)]);
}

} catch (PDOException $e) {
    error_log('[transcript_api] DB 錯誤: ' . $e->getMessage());
    // 若是資料表不存在，給出更明確的提示
    $msg = str_contains($e->getMessage(), "doesn't exist")
        ? '資料表尚未建立，請用管理員帳號點按「初始化資料表」'
        : '資料庫錯誤，請稍後再試';
    echo json_encode(['error' => $msg]);
} catch (Exception $e) {
    error_log('[transcript_api] 錯誤: ' . $e->getMessage());
    echo json_encode(['error' => '伺服器錯誤：' . $e->getMessage()]);
}
