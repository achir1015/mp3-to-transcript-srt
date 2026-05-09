-- ============================================================
-- transcript_setup.sql
-- 語音轉文字功能 - 資料表初始化
-- 使用方式：在 phpMyAdmin 選擇 fan_members 資料庫後執行
-- ============================================================

-- 若資料表已存在則跳過（安全執行）
CREATE TABLE IF NOT EXISTS `transcripts` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY     COMMENT '記錄 ID',
    `member_id`     INT NOT NULL                                COMMENT '上傳者 members.id',
    `title`         VARCHAR(255) NOT NULL                       COMMENT '標題',
    `description`   TEXT                                        COMMENT '描述（選填）',
    `orig_filename` VARCHAR(255) NOT NULL DEFAULT ''            COMMENT '原始上傳檔名',
    `save_filename` VARCHAR(255) NOT NULL DEFAULT ''            COMMENT '磁碟儲存檔名（隨機命名）',
    `file_size`     INT UNSIGNED DEFAULT 0                      COMMENT '音檔大小（bytes）',
    `srt_content`   LONGTEXT                                    COMMENT 'SRT 字幕格式（含時間軸）',
    `plain_content` LONGTEXT                                    COMMENT '純文字格式（不含時間軸）',
    `status`        ENUM('pending','processing','done','error')
                    DEFAULT 'pending'                           COMMENT '轉錄狀態',
    `error_msg`     TEXT                                        COMMENT '錯誤訊息（status=error 時）',
    `views`         INT UNSIGNED DEFAULT 0                      COMMENT '瀏覽次數',
    `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP          COMMENT '建立時間',
    `updated_at`    DATETIME DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP                 COMMENT '更新時間',

    INDEX `idx_member`  (`member_id`),
    INDEX `idx_status`  (`status`),
    INDEX `idx_created` (`created_at`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='語音轉文字記錄表';

-- ============================================================
-- 驗證建立結果
-- ============================================================
SHOW COLUMNS FROM `transcripts`;
