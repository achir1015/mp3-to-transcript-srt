<?php
// ============================================================
// 系統核心設定檔  config/config.php
// 修正版：解決 Synology Session 問題
// ============================================================

// --- 網站基本資訊 ---  
// ⚠️ 因為檔案放在 /fan_members/ 子目錄，這裡要加上路徑
define('SITE_URL',  'https://ipmos.ngrok.app/fan_members');
define('SITE_NAME', '粉絲會員中心');
define('OPENAI_API_KEY', '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'); //Save your key 

// --- 資料庫連線設定 ---
define('DB_HOST',    '127.0.0.1');
define('DB_PORT',    '3307');
define('DB_NAME',    'fan_members');
define('DB_USER',    'root');
define('DB_PASS',    'Athena120083530@');
define('DB_CHARSET', 'utf8mb4');


// --- Google OAuth 2.0 設定 ---
define('GOOGLE_CLIENT_ID',     '296426745832-dhmgl03sn5kocr7ajcu3l1cgpm9k48bt.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');

// ⚠️ Redirect URI 要和 Google Console 裡填的完全一樣
define('GOOGLE_REDIRECT_URI',  'https://ipmos.ngrok.app/fan_members/google_callback.php');

// --- 安全設定 ---
define('BCRYPT_COST',        12);
define('SESSION_LIFETIME',   86400);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION',   900);

// --- Session 初始化（Synology 相容版）---
if (session_status() === PHP_SESSION_NONE) {
    // 移除過於嚴格的設定，改用相容 Synology 的寫法
    ini_set('session.cookie_httponly', 1);
    // ⚠️ Synology 上不強制 secure，避免 Session 遺失
    ini_set('session.cookie_secure',   0);
    ini_set('session.use_strict_mode', 1);
    // 延長 Session 存活時間
    ini_set('session.gc_maxlifetime',  SESSION_LIFETIME);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/',          // 根路徑，讓整個網站共用 Session
        'secure'   => false,        // Synology ngrok 環境設 false
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
