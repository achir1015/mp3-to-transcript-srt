<?php
// transcript.php - 語音轉繁體中文頁面
// 放在 /volume3/web/ 根目錄
// 遊客可瀏覽讀取，登入會員可新增/修改/刪除
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="優力好資訊 - 語音轉繁體中文，支援 SRT 字幕格式">
<title>語音轉文字 - 優力好資訊</title>
<link rel="shortcut icon" href="http://ipmos.tw/files/License-2.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap" rel="stylesheet">
<script src="/member_widget.js" defer></script>
<style>
/* ── CSS 變數（與全站一致） ─────────────────────────────── */
:root {
    --primary: #2D7D90; --primary-light: #4AA3B5; --primary-dark: #1D5A68;
    --accent: #E8A54B;  --accent-light: #F5C882;
    --bg: #FDF8F3; --bg-white: #FFFFFF;
    --text-dark: #2C3E50; --text-light: #6B7C8A;
    --shadow: 0 4px 20px rgba(45,125,144,.12);
    --shadow-hover: 0 8px 30px rgba(45,125,144,.22);
    --radius: 16px; --transition: all .3s cubic-bezier(.4,0,.2,1);
}
* { margin:0; padding:0; box-sizing:border-box; }
html { scroll-behavior:smooth; }
body { font-family:'Noto Sans TC',sans-serif; background:var(--bg); color:var(--text-dark); line-height:1.7; }

/* ── Header ─────────────────────────────────────────────── */
header { background:linear-gradient(135deg,var(--primary),var(--primary-dark)); position:sticky; top:0; z-index:100; box-shadow:0 2px 20px rgba(0,0,0,.1); }
.hc { max-width:1400px; margin:0 auto; padding:1rem 2rem; display:flex; justify-content:space-between; align-items:center; }
.logo { display:flex; align-items:center; gap:12px; text-decoration:none; color:white; }
.logo-icon { width:50px; height:50px; background:var(--accent); border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:24px; }
.logo-text h1 { font-size:1.4rem; font-weight:700; }
.logo-text span { font-size:.85rem; opacity:.9; }
nav { display:flex; gap:6px; align-items:center; flex-wrap:wrap; }
nav a { color:white; text-decoration:none; padding:8px 16px; border-radius:25px; font-weight:500; transition:var(--transition); font-size:.9rem; }
nav a:hover { background:rgba(255,255,255,.2); }
nav a.active { background:var(--accent); }
nav a.fb { background:#1877F2; }
.mob-btn { display:none; background:none; border:none; color:white; font-size:1.5rem; cursor:pointer; padding:8px; }

/* ── Hero ───────────────────────────────────────────────── */
.hero { background:linear-gradient(135deg,var(--primary),var(--primary-dark)); padding:3.5rem 2rem; text-align:center; color:white; position:relative; overflow:hidden; }
.hero::before { content:''; position:absolute; inset:0; background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E"); }
.hero-inner { position:relative; z-index:1; }
.hero h2 { font-size:2.2rem; margin-bottom:.6rem; }
.hero p  { opacity:.9; font-size:1.1rem; max-width:620px; margin:0 auto 1rem; }
.hero-tags { display:flex; gap:8px; justify-content:center; flex-wrap:wrap; }
.hero-tag { background:rgba(255,255,255,.18); padding:4px 14px; border-radius:20px; font-size:.85rem; }

/* ── Main ───────────────────────────────────────────────── */
main { max-width:1200px; margin:0 auto; padding:2rem; }

/* ── Controls Bar ───────────────────────────────────────── */
.ctrl { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem; }
.ctrl-left { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
.total-info { font-size:.85rem; color:var(--text-light); }

/* ── 按鈕 ───────────────────────────────────────────────── */
.btn { display:inline-flex; align-items:center; gap:7px; padding:10px 22px; border-radius:25px; border:none; cursor:pointer; font-family:inherit; font-size:.9rem; font-weight:600; transition:var(--transition); text-decoration:none; }
.btn-primary  { background:var(--primary); color:white; }
.btn-primary:hover  { background:var(--primary-dark); transform:translateY(-2px); box-shadow:var(--shadow); }
.btn-outline  { background:white; color:var(--primary); border:2px solid var(--primary); }
.btn-outline:hover  { background:var(--primary); color:white; }
.btn-danger   { background:#dc3545; color:white; }
.btn-danger:hover   { background:#c82333; }
.btn-warn     { background:#fd7e14; color:white; }
.btn-warn:hover     { background:#e66a00; }
.btn-sm { padding:6px 14px; font-size:.82rem; border-radius:20px; }
.btn:disabled { opacity:.5; cursor:not-allowed; transform:none !important; }

/* ── 警示條（需初始化或設定 API Key） ────────────────────── */
.alert-bar { border-radius:12px; padding:1rem 1.5rem; margin-bottom:1.5rem; display:flex; align-items:flex-start; gap:12px; }
.alert-warn { background:#fff3cd; border:1px solid #ffc107; }
.alert-bar p { flex:1; font-size:.88rem; }

/* ── Cards Grid ─────────────────────────────────────────── */
.grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:1.2rem; }
.card { background:white; border-radius:var(--radius); padding:1.4rem; box-shadow:var(--shadow); transition:var(--transition); display:flex; flex-direction:column; gap:.8rem; border-left:4px solid var(--primary); }
.card:hover { transform:translateY(-4px); box-shadow:var(--shadow-hover); }
.card-head { display:flex; justify-content:space-between; align-items:flex-start; gap:.5rem; }
.card-title { font-size:1rem; font-weight:700; color:var(--text-dark); line-height:1.4; flex:1; }
.card-meta  { font-size:.8rem; color:var(--text-light); display:flex; flex-wrap:wrap; gap:8px; }
.card-desc  { font-size:.88rem; color:var(--text-light); line-height:1.6; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.card-actions { display:flex; gap:6px; flex-wrap:wrap; margin-top:auto; }
.badge { display:inline-block; padding:2px 10px; border-radius:10px; font-size:.75rem; font-weight:600; }
.badge-done { background:#d4edda; color:#155724; }
.badge-size { background:#e8f4fd; color:#0c5460; }

/* ── Empty State ────────────────────────────────────────── */
.empty { text-align:center; padding:4rem 2rem; color:var(--text-light); background:white; border-radius:var(--radius); box-shadow:var(--shadow); }
.empty .icon { font-size:4rem; margin-bottom:1rem; }
.empty h3 { font-size:1.3rem; color:var(--text-dark); margin-bottom:.5rem; }

/* ── Pagination ─────────────────────────────────────────── */
.pager { display:flex; justify-content:center; gap:8px; margin-top:1.5rem; flex-wrap:wrap; }
.page-btn { padding:8px 16px; border-radius:20px; border:none; cursor:pointer; font-family:inherit; font-size:.9rem; background:white; color:var(--text-dark); box-shadow:var(--shadow); transition:var(--transition); }
.page-btn:hover, .page-btn.active { background:var(--primary); color:white; }
.page-btn:disabled { opacity:.4; cursor:not-allowed; }

/* ── Modal ──────────────────────────────────────────────── */
.overlay { position:fixed; inset:0; background:rgba(29,90,104,.78); display:none; align-items:center; justify-content:center; z-index:1000; backdrop-filter:blur(6px); padding:1rem; }
.overlay.show { display:flex; }
.modal { background:white; border-radius:20px; width:100%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.3); animation:modalIn .25s ease; }
@keyframes modalIn { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
.modal-sm { max-width:480px; }
.modal-md { max-width:680px; }
.modal-lg { max-width:880px; }
.mhd { display:flex; justify-content:space-between; align-items:center; padding:1.3rem 1.8rem; border-bottom:1px solid #f0f0f0; position:sticky; top:0; background:white; z-index:1; border-radius:20px 20px 0 0; }
.mhd h3 { font-size:1.15rem; color:var(--primary-dark); }
.mclose { background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-light); line-height:1; padding:4px; }
.mclose:hover { color:var(--text-dark); }
.mbody { padding:1.4rem 1.8rem; }
.mfoot { padding:1.2rem 1.8rem; border-top:1px solid #f0f0f0; display:flex; justify-content:flex-end; gap:10px; }

/* ── Form ───────────────────────────────────────────────── */
.fg { margin-bottom:1.2rem; }
.fg label { display:block; font-size:.88rem; font-weight:600; color:var(--text-dark); margin-bottom:.4rem; }
.fg input, .fg textarea {
    width:100%; padding:10px 14px; border:2px solid #e8e8e8; border-radius:10px;
    font-family:inherit; font-size:.9rem; color:var(--text-dark); background:#fafafa;
    transition:border-color .2s;
}
.fg input:focus, .fg textarea:focus { outline:none; border-color:var(--primary); background:white; }
.fg textarea { resize:vertical; line-height:1.6; }
.fhint { font-size:.78rem; color:var(--text-light); margin-top:.3rem; }

/* ── 拖放上傳區 ─────────────────────────────────────────── */
.dropzone { border:2px dashed var(--primary-light); border-radius:12px; padding:2rem; text-align:center; cursor:pointer; transition:var(--transition); background:#f8fcfd; }
.dropzone:hover, .dropzone.over { background:#e0f2f6; border-color:var(--primary); }
.dropzone .dz-icon { font-size:2.5rem; margin-bottom:.5rem; }
.dropzone p { color:var(--text-light); font-size:.9rem; }
.dz-file { margin-top:.8rem; color:var(--primary); font-weight:600; font-size:.9rem; }
#fileInput { display:none; }

/* ── 進度條 ─────────────────────────────────────────────── */
.prog-wrap { margin-top:1rem; display:none; }
.prog-bar  { height:8px; border-radius:4px; background:#e8e8e8; overflow:hidden; }
.prog-fill { height:100%; background:linear-gradient(90deg,var(--primary),var(--primary-light)); border-radius:4px; transition:width .4s; }
.prog-text { font-size:.85rem; color:var(--text-light); margin-top:.4rem; text-align:center; }

/* ── Tabs ───────────────────────────────────────────────── */
.tabs { display:flex; gap:4px; margin-bottom:1rem; background:#f0f0f0; padding:4px; border-radius:12px; }
.tab-btn { flex:1; padding:8px 16px; border-radius:8px; border:none; cursor:pointer; font-family:inherit; font-size:.9rem; font-weight:500; transition:var(--transition); background:none; color:var(--text-light); }
.tab-btn.active { background:white; color:var(--primary); box-shadow:0 2px 8px rgba(0,0,0,.08); }
.tab-pane { display:none; }
.tab-pane.active { display:block; }

/* ── 音訊播放器 ─────────────────────────────────────────── */
.audio-player {
    background: linear-gradient(135deg,#e8f4f8,#f0f8fb);
    border: 1px solid var(--primary-light);
    border-radius: 12px; padding: 1rem 1.2rem; margin-bottom: 1rem;
    display: none; /* 預設隱藏，有音檔才顯示 */
}
.audio-player .ap-label {
    font-size: .82rem; font-weight: 600; color: var(--primary-dark);
    margin-bottom: .5rem; display: flex; align-items: center; gap: 6px;
}
.audio-player audio {
    width: 100%; height: 40px; border-radius: 8px; outline: none;
}
/* 播放時間對照提示 */
.audio-player .ap-hint {
    font-size: .76rem; color: var(--text-light); margin-top: .4rem;
    text-align: center;
}

/* ── 同步字幕顯示區 ──────────────────────────────────────── */
.subtitle-display {
    background: #1a1a2e;
    border-radius: 10px;
    padding: .8rem 1rem;
    margin-top: .8rem;
    min-height: 90px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 4px;
    position: relative;
    overflow: hidden;
}
.subtitle-display .sub-prev,
.subtitle-display .sub-next {
    font-size: .85rem;
    color: rgba(255,255,255,.35);
    text-align: center;
    line-height: 1.5;
    max-width: 100%;
    transition: all .3s ease;
    min-height: 1.3em;
    white-space: pre-wrap;
}
.subtitle-display .sub-current {
    font-size: 1.05rem;
    font-weight: 600;
    color: #FFD700;
    text-align: center;
    line-height: 1.6;
    max-width: 100%;
    transition: all .25s ease;
    padding: 2px 8px;
    background: rgba(255,215,0,.08);
    border-radius: 6px;
    min-height: 1.5em;
    white-space: pre-wrap;
}
.subtitle-display .sub-time {
    position: absolute;
    top: 6px; right: 10px;
    font-size: .72rem;
    color: rgba(255,255,255,.3);
    font-family: monospace;
}
.subtitle-display .sub-idle {
    font-size: .9rem;
    color: rgba(255,255,255,.3);
    font-style: italic;
}

/* ── 轉錄文字框 ─────────────────────────────────────────── */
.tbox { background:#f8f9fa; border:1px solid #e8e8e8; border-radius:10px; padding:1.2rem; font-size:.88rem; line-height:1.9; max-height:380px; overflow-y:auto; white-space:pre-wrap; font-family:'Noto Sans TC',monospace; color:var(--text-dark); }
.copy-row { display:flex; justify-content:flex-end; gap:8px; margin-bottom:.5rem; }

/* ── Loading ─────────────────────────────────────────────── */
.spinner { display:inline-block; width:20px; height:20px; border:3px solid #e8e8e8; border-top-color:var(--primary); border-radius:50%; animation:spin .8s linear infinite; }
@keyframes spin { to{transform:rotate(360deg)} }
.loading { text-align:center; padding:3rem; color:var(--text-light); }

/* ── Toast ──────────────────────────────────────────────── */
#toastBox { position:fixed; top:80px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
.toast { background:white; border-radius:10px; padding:11px 16px; box-shadow:0 4px 20px rgba(0,0,0,.15); font-size:.88rem; border-left:4px solid; max-width:300px; display:flex; align-items:center; gap:9px; animation:slideIn .25s ease; }
.toast-ok  { border-color:#28a745; }
.toast-err { border-color:#dc3545; }
.toast-inf { border-color:var(--primary); }
@keyframes slideIn { from{transform:translateX(100%);opacity:0} to{transform:none;opacity:1} }

/* ── Footer ─────────────────────────────────────────────── */
footer { background:linear-gradient(135deg,var(--primary-dark),#1a4a54); color:white; padding:2rem; margin-top:3rem; text-align:center; }
footer p { opacity:.8; }
footer a { color:var(--accent-light); text-decoration:none; }

/* ── RWD ─────────────────────────────────────────────────── */
@media(max-width:768px){
    .hc { padding:1rem; }
    nav { display:none; position:absolute; top:100%; left:0; right:0; background:var(--primary-dark); flex-direction:column; padding:1rem; }
    nav.open { display:flex; }
    nav a { padding:12px 16px; border-radius:8px; }
    .mob-btn { display:block; }
    .hero h2 { font-size:1.7rem; }
    main { padding:1rem; }
    .grid { grid-template-columns:1fr; }
    .mbody { padding:1.2rem 1rem; }
    .mhd  { padding:1.1rem 1rem; }
    .mfoot { padding:1rem; }
}
</style>
</head>
<body>

<!-- ══════════════════════════════════════════
     Header
══════════════════════════════════════════ -->
<header>
  <div class="hc">
    <a href="index.html" class="logo">
      <div class="logo-icon">🌿</div>
      <div class="logo-text"><h1>優力好資訊</h1><span>長照知識分享平台</span></div>
    </a>
    <button class="mob-btn" onclick="document.getElementById('mainNav').classList.toggle('open')">☰</button>
    <nav id="mainNav">
      <a href="index.html">首頁</a>
      <a href="forum.php">討論區</a>
      <a href="care-knowledge.php">知識庫</a>
      <a href="quiz.html">學科測驗</a>
      <a href="resources.php">資源中心</a>
      <a href="guide.php">🛠 工具說明</a>
      <a href="transcript.php" class="active">🎙️ 語音轉文字</a>
      <a href="about.html">關於我們</a>
      <a href="https://www.facebook.com/groups/1817991231770115?locale=zh_TW" target="_blank" class="fb">FB社團</a>
    </nav>
  </div>
</header>

<!-- ══════════════════════════════════════════
     Hero
══════════════════════════════════════════ -->
<section class="hero">
  <div class="hero-inner">
    <h2>🎙️ 語音轉文字</h2>
    <p>上傳 MP3 / WAV 等音檔，AI 自動轉錄為繁體中文（含標點），同時產生 SRT 字幕格式與純文字格式</p>
    <div class="hero-tags">
      <span class="hero-tag">🤖 OpenAI Whisper</span>
      <span class="hero-tag">🇹🇼 繁體中文</span>
      <span class="hero-tag">📄 SRT 字幕</span>
      <span class="hero-tag">📝 純文字</span>
      <span class="hero-tag">☁️ 雲端儲存</span>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════
     Main
══════════════════════════════════════════ -->
<main>
  <!-- 控制列 -->
  <div class="ctrl">
    <div class="ctrl-left">
      <button id="btnUpload" class="btn btn-primary" onclick="openUploadModal()" style="display:none">🎙️ 上傳音檔</button>
      <button id="btnSetup"  class="btn btn-outline btn-sm" onclick="runSetup()" style="display:none">⚙️ 初始化資料表</button>
    </div>
    <div class="total-info" id="totalInfo">載入中…</div>
  </div>

  <!-- 內容區 -->
  <div id="mainArea">
    <div class="loading"><div class="spinner"></div><p style="margin-top:1rem">載入中…</p></div>
  </div>

  <!-- 分頁 -->
  <div class="pager" id="pager"></div>
</main>

<footer>
  <p>© 2024 優力好資訊 版權所有 | <a href="https://www.facebook.com/groups/1817991231770115?locale=zh_TW" target="_blank">Facebook 社團</a></p>
</footer>

<!-- ══════════════════════════════════════════
     Modal：上傳音檔
══════════════════════════════════════════ -->
<div class="overlay" id="mUpload" onclick="bgClose(event,'mUpload')">
  <div class="modal modal-md">
    <div class="mhd">
      <h3>🎙️ 上傳音檔並轉錄</h3>
      <button class="mclose" onclick="closeModal('mUpload')">✕</button>
    </div>
    <div class="mbody">
      <div class="fg">
        <label>標題 *</label>
        <input type="text" id="uTitle" placeholder="例：2024-01 家庭會議錄音">
      </div>
      <div class="fg">
        <label>描述（選填）</label>
        <input type="text" id="uDesc" placeholder="簡短說明此音檔的內容">
      </div>
      <div class="fg">
        <label>音訊檔案 *</label>
        <div class="dropzone" id="dropZone" onclick="document.getElementById('fileInput').click()">
          <div class="dz-icon">🎵</div>
          <p>點此選擇，或將音檔拖放到此處</p>
          <p style="font-size:.78rem;margin-top:.3rem;color:#999">
            支援 MP3、MP4、M4A、WAV、OGG、WEBM、FLAC &nbsp;｜&nbsp; 上限 25 MB
          </p>
          <div class="dz-file" id="dzFile"></div>
        </div>
        <input type="file" id="fileInput" accept=".mp3,.mp4,.m4a,.wav,.ogg,.webm,.flac">
      </div>
      <!-- 進度條 -->
      <div class="prog-wrap" id="progWrap">
        <div class="prog-bar"><div class="prog-fill" id="progFill" style="width:0%"></div></div>
        <div class="prog-text" id="progText">準備中…</div>
      </div>
    </div>
    <div class="mfoot">
      <button class="btn btn-outline" onclick="closeModal('mUpload')">取消</button>
      <button class="btn btn-primary" id="btnDoUpload" onclick="doUpload()">🚀 上傳並轉錄</button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     Modal：查看轉錄結果
══════════════════════════════════════════ -->
<div class="overlay" id="mView" onclick="bgClose(event,'mView')">
  <div class="modal modal-lg">
    <div class="mhd">
      <h3 id="vTitle">轉錄結果</h3>
      <button class="mclose" onclick="closeModal('mView')">✕</button>
    </div>
    <div class="mbody">
      <div id="vMeta" style="font-size:.82rem;color:var(--text-light);display:flex;gap:14px;flex-wrap:wrap;margin-bottom:.8rem"></div>
      <div id="vDesc" style="display:none;font-size:.9rem;padding:10px 14px;background:#f8f9fa;border-radius:8px;margin-bottom:1rem"></div>
      <!-- 音訊播放器（登入者才顯示） -->
      <div class="audio-player" id="vAudioPlayer">
        <div class="ap-label">🎵 聆聽原始音檔（可邊聽邊對照文字）</div>
        <audio id="vAudio" controls preload="metadata">
          <source id="vAudioSrc" src="" type="audio/mpeg">
          您的瀏覽器不支援 HTML5 音訊播放。
        </audio>
        <!-- 同步字幕顯示區 -->
        <div class="subtitle-display" id="subDisplay">
          <span class="sub-idle">▶ 按下播放，字幕將同步顯示於此</span>
        </div>
        <div class="ap-hint" style="margin-top:.5rem">💡 播放時此處自動顯示對應字幕，可對照下方 SRT 核對</div>
      </div>
      <!-- 兩種格式 Tab -->
      <div class="tabs">
        <button class="tab-btn active" onclick="switchTab(this,'tSRT')">📄 SRT 格式（含時間軸）</button>
        <button class="tab-btn"        onclick="switchTab(this,'tPlain')">📝 純文字（不含時間軸）</button>
      </div>
      <div class="tab-pane active" id="tSRT">
        <div class="copy-row">
          <button class="btn btn-outline btn-sm" onclick="copyEl('vSRT')">📋 複製</button>
          <button class="btn btn-outline btn-sm" onclick="dlEl('vSRT','srt')">⬇️ 下載 .srt</button>
        </div>
        <pre class="tbox" id="vSRT"></pre>
      </div>
      <div class="tab-pane" id="tPlain">
        <div class="copy-row">
          <button class="btn btn-outline btn-sm" onclick="copyEl('vPlain')">📋 複製</button>
          <button class="btn btn-outline btn-sm" onclick="dlEl('vPlain','txt')">⬇️ 下載 .txt</button>
        </div>
        <pre class="tbox" id="vPlain"></pre>
      </div>
    </div>
    <div class="mfoot" id="vActions">
      <button class="btn btn-outline" onclick="closeModal('mView')">關閉</button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     Modal：編輯
══════════════════════════════════════════ -->
<div class="overlay" id="mEdit" onclick="bgClose(event,'mEdit')">
  <div class="modal modal-lg">
    <div class="mhd">
      <h3>✏️ 編輯轉錄記錄</h3>
      <button class="mclose" onclick="closeModal('mEdit')">✕</button>
    </div>
    <div class="mbody">
      <input type="hidden" id="eId">
      <div class="fg">
        <label>標題 *</label>
        <input type="text" id="eTitle">
      </div>
      <div class="fg">
        <label>描述</label>
        <input type="text" id="eDesc">
      </div>
      <div class="tabs">
        <button class="tab-btn active" onclick="switchTab(this,'etSRT')">📄 SRT 內容</button>
        <button class="tab-btn"        onclick="switchTab(this,'etPlain')">📝 純文字內容</button>
      </div>
      <div class="tab-pane active" id="etSRT">
        <p class="fhint" style="margin-bottom:.5rem">SRT 格式（含時間軸），可直接修正識別錯誤</p>
        <textarea class="fg" id="eSRT" rows="11" style="width:100%;padding:10px 14px;border:2px solid #e8e8e8;border-radius:10px;font-family:inherit;font-size:.88rem;line-height:1.7;resize:vertical;background:#fafafa"></textarea>
      </div>
      <div class="tab-pane" id="etPlain">
        <p class="fhint" style="margin-bottom:.5rem">純文字（不含時間軸）</p>
        <textarea class="fg" id="ePlain" rows="11" style="width:100%;padding:10px 14px;border:2px solid #e8e8e8;border-radius:10px;font-family:inherit;font-size:.88rem;line-height:1.7;resize:vertical;background:#fafafa"></textarea>
      </div>
    </div>
    <div class="mfoot">
      <button class="btn btn-outline" onclick="closeModal('mEdit')">取消</button>
      <button class="btn btn-primary" onclick="doEdit()">💾 儲存變更</button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     Modal：確認刪除
══════════════════════════════════════════ -->
<div class="overlay" id="mDel" onclick="bgClose(event,'mDel')">
  <div class="modal modal-sm">
    <div class="mhd">
      <h3>🗑️ 確認刪除</h3>
      <button class="mclose" onclick="closeModal('mDel')">✕</button>
    </div>
    <div class="mbody" style="text-align:center;padding:1.5rem">
      <p style="font-size:1.1rem;margin-bottom:.8rem">確定刪除此轉錄記錄？</p>
      <p style="color:#dc3545;font-size:.9rem">⚠️ 音檔與所有內容將一併移除，無法復原。</p>
      <input type="hidden" id="delId">
    </div>
    <div class="mfoot">
      <button class="btn btn-outline" onclick="closeModal('mDel')">取消</button>
      <button class="btn btn-danger"  onclick="doDelete()">確認刪除</button>
    </div>
  </div>
</div>

<!-- Toast 容器 -->
<div id="toastBox"></div>

<!-- ══════════════════════════════════════════
     JavaScript
══════════════════════════════════════════ -->
<script>
'use strict';

// ── 狀態 ────────────────────────────────────────────────────
const S = { page:1, pages:1, canUpload:false, isAdmin:false, uid:null, viewTitle:'' };
let selectedFile = null;
let progTimer    = null;

// ── API 呼叫 ────────────────────────────────────────────────
async function api(action, body=null) {
    try {
        if (!body) {
            // GET 請求（list、get）
            const r = await fetch(`/transcript_api.php?action=${action}`, { credentials:'same-origin' });
            return await r.json();
        }
        // POST 請求
        let fd;
        if (body instanceof FormData) { fd = body; fd.set('action', action); }
        else {
            fd = new FormData();
            fd.append('action', action);
            Object.entries(body).forEach(([k,v]) => fd.append(k, v ?? ''));
        }
        const r = await fetch('/transcript_api.php', { method:'POST', credentials:'same-origin', body:fd });
        return await r.json();
    } catch(e) { return { error:'網路錯誤：' + e.message }; }
}

// ── Toast ────────────────────────────────────────────────────
function toast(msg, type='inf') {
    const icons = { ok:'✅', err:'❌', inf:'ℹ️' };
    const el = document.createElement('div');
    el.className = `toast toast-${type}`;
    el.innerHTML = `<span>${icons[type]||'ℹ️'}</span><span>${msg}</span>`;
    document.getElementById('toastBox').appendChild(el);
    setTimeout(() => el.remove(), 3800);
}

// ── Modal ────────────────────────────────────────────────────
function openModal(id) {
    document.getElementById(id).classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.remove('show');
    document.body.style.overflow = '';
}
function bgClose(e, id) { if (e.target === e.currentTarget) closeModal(id); }

// ── Tab 切換 ────────────────────────────────────────────────
function switchTab(btn, panelId) {
    const root = btn.closest('.modal') || btn.closest('section') || document.body;
    btn.closest('.tabs').querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    root.querySelectorAll('.tab-pane').forEach(p => p.classList.toggle('active', p.id === panelId));
}

// ── 工具 ─────────────────────────────────────────────────────
const fmtDate = s => { if(!s) return ''; const d=new Date(s); return `${d.getFullYear()}/${String(d.getMonth()+1).padStart(2,'0')}/${String(d.getDate()).padStart(2,'0')}`; };
const fmtSize = b => { if(!b) return ''; if(b<1024) return b+'B'; if(b<1048576) return (b/1024).toFixed(1)+'KB'; return (b/1048576).toFixed(1)+'MB'; };

// ── 載入清單 ─────────────────────────────────────────────────
async function loadList(page=1) {
    S.page = page;
    document.getElementById('mainArea').innerHTML = `<div class="loading"><div class="spinner"></div><p style="margin-top:1rem">載入中…</p></div>`;

    const d = await api(`list&page=${page}`);
    if (d.error) {
        const needSetup = d.error.includes('資料表');
        document.getElementById('mainArea').innerHTML = `
            <div class="empty">
              <div class="icon">⚠️</div>
              <h3>${d.error}</h3>
              ${needSetup ? '<p>請用管理員帳號點按右上方「初始化資料表」</p>' : ''}
            </div>`;
        // 仍需顯示 setup 按鈕
        document.getElementById('btnSetup').style.display = S.isAdmin ? 'inline-flex' : 'none';
        return;
    }

    S.pages = d.pages; S.canUpload = d.canUpload; S.isAdmin = d.isAdmin; S.uid = d.currentUserId;
    document.getElementById('btnUpload').style.display = S.canUpload ? 'inline-flex' : 'none';
    document.getElementById('btnSetup').style.display  = S.isAdmin   ? 'inline-flex' : 'none';
    document.getElementById('totalInfo').textContent   = `共 ${d.total} 筆轉錄記錄`;

    if (!d.data?.length) {
        document.getElementById('mainArea').innerHTML = `
            <div class="empty">
              <div class="icon">🎙️</div>
              <h3>目前還沒有轉錄記錄</h3>
              <p>${S.canUpload ? '點擊「上傳音檔」開始第一筆轉錄' : '請登入後才能上傳音檔'}</p>
            </div>`;
        renderPager(d.pages, page); return;
    }

    const grid = document.createElement('div');
    grid.className = 'grid';
    d.data.forEach(item => grid.appendChild(mkCard(item)));
    document.getElementById('mainArea').innerHTML = '';
    document.getElementById('mainArea').appendChild(grid);
    renderPager(d.pages, page);
}

// 建立卡片
function mkCard(it) {
    const canMod = (S.uid && it.member_id == S.uid) || S.isAdmin;
    const el = document.createElement('div');
    el.className = 'card';
    el.innerHTML = `
        <div class="card-head">
          <div class="card-title">${esc(it.title)}</div>
          <span class="badge badge-done">完成</span>
        </div>
        <div class="card-meta">
          <span>👤 ${esc(it.author_name||'未知')}</span>
          <span>📅 ${fmtDate(it.created_at)}</span>
          <span>👁 ${it.views||0}</span>
          ${it.file_size?`<span class="badge badge-size">${fmtSize(it.file_size)}</span>`:''}
        </div>
        ${it.description?`<div class="card-desc">${esc(it.description)}</div>`:''}
        <div class="card-actions">
          <button class="btn btn-primary btn-sm" onclick="viewItem(${it.id})">📖 查看</button>
          ${canMod?`
          <button class="btn btn-outline btn-sm" onclick="editItem(${it.id})">✏️ 編輯</button>
          <button class="btn btn-warn btn-sm"    onclick="retranscribe(${it.id})">🔄 重新轉錄</button>
          <button class="btn btn-danger btn-sm"  onclick="confirmDel(${it.id})">🗑️</button>
          `:''}
        </div>`;
    return el;
}

// ── 查看 ────────────────────────────────────────────────────
async function viewItem(id) {
    S.viewTitle = '';
    openModal('mView');
    document.getElementById('vTitle').textContent = '載入中…';
    document.getElementById('vSRT').textContent   = '';
    document.getElementById('vPlain').textContent = '';
    // 重置音訊播放器
    const playerEl = document.getElementById('vAudioPlayer');
    const audioEl  = document.getElementById('vAudio');
    audioEl.pause();
    playerEl.style.display = 'none';

    const d = await api(`get&id=${id}`);
    if (d.error) { toast(d.error,'err'); closeModal('mView'); return; }

    const t = d.data;
    S.viewTitle = t.title;
    document.getElementById('vTitle').textContent = t.title;
    document.getElementById('vMeta').innerHTML = `
        <span>👤 ${esc(t.author_name||'未知')}</span>
        <span>📅 ${fmtDate(t.created_at)}</span>
        <span>📁 ${esc(t.orig_filename)}</span>
        ${t.file_size?`<span>${fmtSize(t.file_size)}</span>`:''}
        <span>👁 ${t.views} 次</span>`;

    const dEl = document.getElementById('vDesc');
    if (t.description) { dEl.textContent = t.description; dEl.style.display='block'; }
    else dEl.style.display='none';

    document.getElementById('vSRT').textContent   = t.srt_content   || '（無 SRT 內容）';
    document.getElementById('vPlain').textContent = t.plain_content || '（無純文字內容）';

    // ── 音訊播放器（有 save_filename 表示已登入，可播放）──
    if (t.save_filename) {
        const audioUrl = '/uploads/transcripts/' + t.save_filename;
        const srcEl    = document.getElementById('vAudioSrc');
        // 依副檔名設定 MIME type
        const ext  = t.save_filename.split('.').pop().toLowerCase();
        const mime = {mp3:'audio/mpeg',mp4:'audio/mp4',m4a:'audio/mp4',
                      wav:'audio/wav',ogg:'audio/ogg',webm:'audio/webm',flac:'audio/flac'}[ext] || 'audio/mpeg';
        srcEl.src  = audioUrl;
        srcEl.type = mime;
        audioEl.load();   // 重新載入新來源
        playerEl.style.display = 'block';

        // 解析 SRT 並綁定字幕同步
        const srtSegs = parseSRT(t.srt_content || '');
        bindSubtitles(audioEl, srtSegs);
    }

    // 操作按鈕（可修改者才顯示）
    document.getElementById('vActions').innerHTML = `
        <button class="btn btn-outline" onclick="closeModal('mView')">關閉</button>
        ${t.canModify?`
        <button class="btn btn-outline btn-sm" onclick="closeModal('mView');editItem(${id})">✏️ 編輯</button>
        <button class="btn btn-warn btn-sm"    onclick="closeModal('mView');retranscribe(${id})">🔄 重新轉錄</button>
        <button class="btn btn-danger btn-sm"  onclick="closeModal('mView');confirmDel(${id})">🗑️ 刪除</button>
        `:''}`;

    // 切回 SRT tab
    switchToFirst('mView');
}

// ── 上傳 ────────────────────────────────────────────────────
function openUploadModal() {
    selectedFile = null;
    document.getElementById('uTitle').value = '';
    document.getElementById('uDesc').value  = '';
    document.getElementById('dzFile').textContent = '';
    document.getElementById('progWrap').style.display = 'none';
    document.getElementById('progFill').style.width = '0%';
    document.getElementById('progFill').style.background = '';
    document.getElementById('btnDoUpload').disabled = false;
    openModal('mUpload');
}

// 拖放事件
const dz = document.getElementById('dropZone');
dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('over'); });
dz.addEventListener('dragleave', () => dz.classList.remove('over'));
dz.addEventListener('drop', e => { e.preventDefault(); dz.classList.remove('over'); if(e.dataTransfer.files[0]) setFile(e.dataTransfer.files[0]); });
document.getElementById('fileInput').addEventListener('change', e => { if(e.target.files[0]) setFile(e.target.files[0]); });

function setFile(f) {
    selectedFile = f;
    document.getElementById('dzFile').textContent = `✅ ${f.name}  (${fmtSize(f.size)})`;
    // 自動填標題（去副檔名）
    if (!document.getElementById('uTitle').value)
        document.getElementById('uTitle').value = f.name.replace(/\.[^.]+$/,'');
}

async function doUpload() {
    const title = document.getElementById('uTitle').value.trim();
    const desc  = document.getElementById('uDesc').value.trim();
    if (!title) { toast('請輸入標題','err'); return; }
    if (!selectedFile) { toast('請選擇音訊檔案','err'); return; }

    const pw = document.getElementById('progWrap');
    const pf = document.getElementById('progFill');
    const pt = document.getElementById('progText');
    pw.style.display = 'block';
    document.getElementById('btnDoUpload').disabled = true;

    // 模擬進度動畫（Whisper API 可能需 1-3 分鐘）
    let p = 10;
    pf.style.width = p + '%';
    pt.textContent = '上傳音檔中…';
    progTimer = setInterval(() => {
        if (p < 82) {
            p += (p < 40) ? 4 : (p < 65) ? 1.5 : 0.6;
            pf.style.width = p.toFixed(1) + '%';
            if (p > 35) pt.textContent = '🤖 AI 轉錄中，請稍候（約需 1~3 分鐘）…';
        }
    }, 1200);

    try {
        const fd = new FormData();
        fd.append('audio', selectedFile, selectedFile.name);
        fd.append('title', title);
        fd.append('description', desc);
        const d = await api('upload', fd);

        clearInterval(progTimer);
        if (d.error) {
            pf.style.width = '100%'; pf.style.background = '#dc3545';
            pt.textContent = '❌ ' + d.error;
            toast(d.error, 'err');
        } else {
            pf.style.width = '100%';
            pt.textContent = '✅ 轉錄完成！';
            toast('轉錄成功！', 'ok');
            setTimeout(() => { closeModal('mUpload'); loadList(1); }, 900);
        }
    } catch(e) {
        clearInterval(progTimer);
        toast('上傳失敗：' + e.message, 'err');
    } finally {
        document.getElementById('btnDoUpload').disabled = false;
    }
}

// ── 編輯 ────────────────────────────────────────────────────
async function editItem(id) {
    openModal('mEdit');
    document.getElementById('eTitle').value = '載入中…';

    const d = await api(`get&id=${id}`);
    if (d.error) { toast(d.error,'err'); closeModal('mEdit'); return; }

    const t = d.data;
    document.getElementById('eId').value      = t.id;
    document.getElementById('eTitle').value   = t.title;
    document.getElementById('eDesc').value    = t.description || '';
    document.getElementById('eSRT').value     = t.srt_content   || '';
    document.getElementById('ePlain').value   = t.plain_content || '';
    switchToFirst('mEdit');
}

async function doEdit() {
    const id    = document.getElementById('eId').value;
    const title = document.getElementById('eTitle').value.trim();
    const desc  = document.getElementById('eDesc').value.trim();
    const srt   = document.getElementById('eSRT').value;
    const plain = document.getElementById('ePlain').value;
    if (!title) { toast('標題不可空白','err'); return; }

    const d = await api('update', { id, title, description:desc, srt_content:srt, plain_content:plain });
    if (d.error) { toast(d.error,'err'); return; }
    toast('已儲存！', 'ok');
    closeModal('mEdit');
    loadList(S.page);
}

// ── 刪除 ────────────────────────────────────────────────────
function confirmDel(id) { document.getElementById('delId').value = id; openModal('mDel'); }

async function doDelete() {
    const id = document.getElementById('delId').value;
    const d  = await api('delete', { id });
    if (d.error) { toast(d.error,'err'); return; }
    toast('已刪除！', 'ok');
    closeModal('mDel');
    loadList(S.page > 1 && document.querySelectorAll('.card').length === 1 ? S.page - 1 : S.page);
}

// ── 重新轉錄 ────────────────────────────────────────────────
async function retranscribe(id) {
    if (!confirm('確定重新轉錄？現有內容將被覆蓋。')) return;
    toast('重新轉錄中，請稍候…', 'inf');
    const d = await api('retranscribe', { id });
    if (d.error) { toast(d.error,'err'); return; }
    toast('重新轉錄完成！', 'ok');
    loadList(S.page);
}

// ── 初始化資料表（admin）───────────────────────────────────
async function runSetup() {
    const d = await api('setup', {});
    if (d.error) { toast(d.error,'err'); return; }
    toast(d.msg || '初始化完成！', 'ok');
    loadList(1);
}

// ── 分頁 ────────────────────────────────────────────────────
function renderPager(total, cur) {
    const el = document.getElementById('pager');
    if (total <= 1) { el.innerHTML = ''; return; }
    let h = `<button class="page-btn" onclick="loadList(${cur-1})" ${cur<=1?'disabled':''}>‹</button>`;
    for (let i=1; i<=total; i++) {
        if (i===1 || i===total || Math.abs(i-cur)<=1) h += `<button class="page-btn ${i===cur?'active':''}" onclick="loadList(${i})">${i}</button>`;
        else if (Math.abs(i-cur)===2)                  h += `<span style="padding:8px 4px;color:var(--text-light)">…</span>`;
    }
    h += `<button class="page-btn" onclick="loadList(${cur+1})" ${cur>=total?'disabled':''}>›</button>`;
    el.innerHTML = h;
}

// ── 複製 / 下載 ──────────────────────────────────────────────
function copyEl(elId) {
    navigator.clipboard.writeText(document.getElementById(elId).textContent)
        .then(() => toast('已複製到剪貼簿！','ok'))
        .catch(() => toast('複製失敗','err'));
}
function dlEl(elId, ext) {
    const text = document.getElementById(elId).textContent;
    const fname = (S.viewTitle||'transcript') + '.' + ext;
    const blob  = new Blob([text], { type: ext==='srt'?'text/srt;charset=utf-8':'text/plain;charset=utf-8' });
    const a = Object.assign(document.createElement('a'), { href:URL.createObjectURL(blob), download:fname });
    a.click(); URL.revokeObjectURL(a.href);
}

// ── 輔助 ─────────────────────────────────────────────────────
function esc(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function switchToFirst(modalId) {
    const modal = document.getElementById(modalId);
    modal.querySelectorAll('.tab-btn').forEach((b,i)  => b.classList.toggle('active', i===0));
    modal.querySelectorAll('.tab-pane').forEach((p,i) => p.classList.toggle('active', i===0));
}

// ════════════════════════════════════════════════════════════
// SRT 解析與字幕同步
// ════════════════════════════════════════════════════════════

/**
 * 將 SRT 字串解析為 [{start, end, text}, ...] 陣列
 */
function parseSRT(srt) {
    if (!srt || !srt.trim()) return [];
    const segs   = [];
    const blocks = srt.trim().split(/\n\n+/);
    blocks.forEach(function(block) {
        const lines = block.trim().split('\n');
        if (lines.length < 2) return;
        // 找時間軸行（格式：00:00:00,000 --> 00:00:05,200）
        var timeLine = '';
        var textLines = [];
        for (var i = 0; i < lines.length; i++) {
            if (lines[i].indexOf(' --> ') !== -1) {
                timeLine = lines[i];
                textLines = lines.slice(i + 1);
                break;
            }
        }
        if (!timeLine) return;
        var parts = timeLine.split(' --> ');
        if (parts.length < 2) return;
        segs.push({
            start: srtTimeToSec(parts[0].trim()),
            end:   srtTimeToSec(parts[1].trim()),
            text:  textLines.join('\n').trim(),
        });
    });
    return segs;
}

/** SRT 時間碼轉秒數（00:01:05,500 → 65.5） */
function srtTimeToSec(t) {
    t = t.replace(',', '.');
    var p = t.split(':');
    if (p.length < 3) return 0;
    return parseInt(p[0]) * 3600 + parseInt(p[1]) * 60 + parseFloat(p[2]);
}

/** 根據當前時間找到對應的字幕 index */
function findSegIdx(segs, currentTime) {
    for (var i = 0; i < segs.length; i++) {
        if (currentTime >= segs[i].start && currentTime <= segs[i].end) return i;
    }
    // 找最近的即將出現的字幕
    for (var i = 0; i < segs.length; i++) {
        if (segs[i].start > currentTime) return -1; // 空白段
    }
    return -1;
}

/**
 * 綁定字幕同步到 audio 元素
 * 每次 timeupdate 時更新字幕顯示區
 */
function bindSubtitles(audioEl, segs) {
    var display = document.getElementById('subDisplay');
    if (!display) return;

    // 移除舊的 listener（避免重複綁定）
    if (audioEl._subHandler) {
        audioEl.removeEventListener('timeupdate', audioEl._subHandler);
    }

    // 無 SRT 資料
    if (!segs || segs.length === 0) {
        display.innerHTML = '<span class="sub-idle">（此筆記錄無 SRT 字幕資料）</span>';
        return;
    }

    // 重置顯示
    display.innerHTML = '<span class="sub-idle">▶ 按下播放，字幕將同步顯示於此</span>';

    var lastIdx = -2; // 強制初次更新

    audioEl._subHandler = function() {
        var t   = audioEl.currentTime;
        var idx = findSegIdx(segs, t);

        if (idx === lastIdx) return; // 無變化，跳過重繪
        lastIdx = idx;

        var prevText = (idx > 0)                 ? segs[idx - 1].text : '';
        var currText = (idx >= 0)                ? segs[idx].text     : '';
        var nextText = (idx >= 0 && idx < segs.length - 1) ? segs[idx + 1].text : '';

        // 找下一段的開始時間（空白段也顯示下一句預告）
        if (idx < 0) {
            for (var i = 0; i < segs.length; i++) {
                if (segs[i].start > t) { nextText = segs[i].text; break; }
            }
        }

        var fmtSec = function(s) {
            var h = Math.floor(s/3600), m = Math.floor((s%3600)/60), sec = Math.floor(s%60);
            return (h?h+':':'') + String(m).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
        };
        var timeLabel = idx >= 0
            ? fmtSec(segs[idx].start) + ' → ' + fmtSec(segs[idx].end)
            : fmtSec(t);

        display.innerHTML =
            '<span class="sub-time">' + timeLabel + '</span>' +
            '<div class="sub-prev">'    + esc(prevText) + '</div>' +
            '<div class="sub-current">' + esc(currText || '　') + '</div>' +
            '<div class="sub-next">'    + esc(nextText) + '</div>';
    };

    audioEl.addEventListener('timeupdate', audioEl._subHandler);

    // 播放/暫停時更新顯示
    audioEl.addEventListener('play', function() {
        if (lastIdx === -2) {
            display.innerHTML = '<span class="sub-idle">⏳ 載入字幕中…</span>';
        }
    });
    audioEl.addEventListener('ended', function() {
        display.innerHTML = '<span class="sub-idle">▶ 播放結束</span>';
        lastIdx = -2;
    });
    // 拖動進度條時立即更新
    audioEl.addEventListener('seeked', audioEl._subHandler);
}

// ── 初始載入 ─────────────────────────────────────────────────
loadList(1);
</script>
</body>
</html>
