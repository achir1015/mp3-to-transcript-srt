# mp3-to-transcript-srt 
語音轉文字功能  (https://ipmos.ngrok.app/transcript.php)
<img width="1797" height="816" alt="image" src="https://github.com/user-attachments/assets/068a8559-5a2f-4cb5-869b-32b947206730" />

本次開發總結
整個語音轉文字功能從零到完成，共經歷了：
技術層面解決了 9 個問題：
PHP 版本相容 → Warning 輸出 → 目錄權限 → Proxy 阻擋 → OpenAI 帳號設定 → SQL 語法 → nginx 大小限制 → 簡繁轉換 → 字幕同步
最終產出 3 個核心檔案：
檔案功能transcript.php完整前端：上傳、查看、播放器、同步字幕、CRUDtranscript_api.php後端 API：Whisper 轉錄、GPT 繁體轉換、資料庫操作transcript_setup.sql一鍵建立資料表
系統特色：

🎙️ AI 自動轉錄繁體中文（含標點）
📄 同時產生 SRT 字幕 + 純文字兩種格式
🎵 邊聽邊看，卡拉OK式同步字幕對照
🔐 完整權限控管（遊客/會員/管理員）
🇹🇼 Whisper + GPT-4o-mini 雙重確保繁體中文輸出
