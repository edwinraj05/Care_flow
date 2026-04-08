<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "patient") {
    header("Location: ../public/login.html"); exit;
}
include("../config/db.php");
$pid = $_SESSION['user'];

$res     = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id='$pid'");
$patient = mysqli_fetch_assoc($res);
if (!$patient) { session_destroy(); header("Location: ../public/login.html"); exit; }

$token_q  = mysqli_query($conn, "SELECT * FROM tokens WHERE patient_id='$pid' AND status IN ('Waiting','NowServing') ORDER BY created_at ASC LIMIT 1");
$token    = mysqli_fetch_assoc($token_q);

$ns_q     = mysqli_query($conn, "SELECT * FROM tokens WHERE status='NowServing' LIMIT 1");
$ns       = mysqli_fetch_assoc($ns_q);

$position = 0; $wait = 0;
if ($token) {
<<<<<<< HEAD
    $t = $token['created_at'];
=======
    $t  = $token['created_at'];
>>>>>>> 41036b6 (first commit)
    $cq = mysqli_query($conn, "SELECT COUNT(*) as c FROM tokens WHERE status='Waiting' AND created_at < '$t'");
    $position = mysqli_fetch_assoc($cq)['c'];
    $wait = $position * 7;
}

<<<<<<< HEAD
$history = mysqli_query($conn, "SELECT * FROM medical_history WHERE patient_id='$pid' ORDER BY created_at DESC");
=======
$history  = mysqli_query($conn, "SELECT * FROM medical_history WHERE patient_id='$pid' ORDER BY created_at DESC");
>>>>>>> 41036b6 (first commit)
$latest_q = mysqli_query($conn, "SELECT * FROM medical_history WHERE patient_id='$pid' ORDER BY created_at DESC LIMIT 1");
$latest   = mysqli_fetch_assoc($latest_q);

$name     = htmlspecialchars($patient['name']);
$fname    = htmlspecialchars(explode(' ', $patient['name'])[0]);
$parts    = explode(' ', $patient['name']);
$initials = strtoupper(substr($parts[0],0,1)) . (isset($parts[1]) ? strtoupper(substr($parts[1],0,1)) : '');
$ns_token = $ns ? $ns['token_id'] : 'Not started';
$fill     = $token ? max(10, min(90, 100 - $position * 12)) : 10;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | CareFlow</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
<<<<<<< HEAD
/* ── RESET ── */
=======
>>>>>>> 41036b6 (first commit)
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; }
body { font-family: 'DM Sans', Arial, sans-serif; background: #f0f4ff; color: #0f172a; }

<<<<<<< HEAD
/* ── TOPBAR ── */
=======
/* TOPBAR */
>>>>>>> 41036b6 (first commit)
.topbar {
  background: #0f1e4a;
  display: flex; align-items: center; justify-content: space-between;
  padding: 12px 20px;
  position: sticky; top: 0; z-index: 200;
}
<<<<<<< HEAD
.topbar-logo {
  font-family: 'DM Serif Display', Georgia, serif;
  font-size: 20px; color: #fff;
  text-decoration: none; font-weight: 400;
}
.topbar-logo span { color: #60a5fa; }
.topbar-right { display: flex; align-items: center; gap: 10px; }
.topbar-avatar {
  width: 34px; height: 34px; border-radius: 50%;
  background: #3b82f6; color: #fff;
  font-weight: 600; font-size: 13px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.topbar-name { color: #e2e8f0; font-size: 13px; }
.topbar-logout {
  color: #94a3b8; font-size: 12px;
  border: 1px solid rgba(255,255,255,0.25);
  padding: 4px 10px; border-radius: 6px;
  text-decoration: none;
}
.topbar-logout:hover { background: rgba(255,255,255,0.08); color: #fff; }

/* ── SCREENS ── */
.screen { display: none; padding: 16px; padding-bottom: 80px; }
.screen.active { display: block; }

/* ── BOTTOM NAV ── */
.bottom-nav {
  position: fixed; bottom: 0; left: 0; right: 0;
  background: #fff;
  border-top: 1px solid #e0e9ff;
  display: flex; justify-content: space-around;
  padding: 8px 0 10px; z-index: 200;
}
.nav-item {
  display: flex; flex-direction: column; align-items: center;
  gap: 3px; cursor: pointer; padding: 4px 20px;
  border-radius: 10px; text-decoration: none !important;
  border: none; background: none; outline: none;
  -webkit-tap-highlight-color: transparent;
}
.nav-item.active { background: #eff6ff; }
.nav-icon { font-size: 22px; line-height: 1; }
.nav-label {
  font-size: 11px; color: #94a3b8; font-weight: 500;
  font-family: 'DM Sans', Arial, sans-serif;
  text-decoration: none !important;
}
.nav-item.active .nav-label { color: #3b82f6; }

/* ── GREETING ── */
.greeting { margin-bottom: 14px; }
.greeting h2 {
  font-family: 'DM Serif Display', Georgia, serif;
  font-size: 22px; color: #0f172a; font-weight: 400;
  margin-bottom: 3px;
}
.greeting p { font-size: 13px; color: #64748b; }

/* ── TOKEN HERO ── */
=======
.topbar-logo { font-family: 'DM Serif Display', Georgia, serif; font-size: 20px; color: #fff; text-decoration: none; }
.topbar-logo span { color: #60a5fa; }
.topbar-right { display: flex; align-items: center; gap: 10px; }
.topbar-avatar { width: 34px; height: 34px; border-radius: 50%; background: #3b82f6; color: #fff; font-weight: 600; font-size: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.topbar-name { color: #e2e8f0; font-size: 13px; }
.topbar-logout { color: #94a3b8; font-size: 12px; border: 1px solid rgba(255,255,255,0.25); padding: 4px 10px; border-radius: 6px; text-decoration: none; }
.topbar-logout:hover { background: rgba(255,255,255,0.08); color: #fff; }

/* SCREENS */
.screen { display: none; padding: 16px; padding-bottom: 90px; }
.screen.active { display: block; }

/* BOTTOM NAV */
.bottom-nav {
  position: fixed; bottom: 0; left: 0; right: 0;
  background: #fff; border-top: 1px solid #e0e9ff;
  display: flex; justify-content: space-around;
  padding: 8px 0 12px; z-index: 200;
}
.nav-item {
  display: flex; flex-direction: column; align-items: center;
  gap: 3px; cursor: pointer; padding: 6px 22px;
  border-radius: 10px; text-decoration: none !important;
  border: none; background: none; outline: none;
  -webkit-tap-highlight-color: transparent;
  font-family: 'DM Sans', Arial, sans-serif;
}
.nav-item.active { background: #eff6ff; }
.nav-icon { font-size: 22px; line-height: 1; }
.nav-label { font-size: 11px; color: #94a3b8; font-weight: 500; text-decoration: none !important; }
.nav-item.active .nav-label { color: #3b82f6; }

/* GREETING */
.greeting { margin-bottom: 14px; }
.greeting h2 { font-family: 'DM Serif Display', Georgia, serif; font-size: 22px; color: #0f172a; font-weight: 400; margin-bottom: 3px; }
.greeting p { font-size: 13px; color: #64748b; }

/* TOKEN HERO */
>>>>>>> 41036b6 (first commit)
.token-hero {
  background: linear-gradient(135deg, #0f1e4a 0%, #1e40af 100%);
  border-radius: 16px; padding: 20px; color: #fff;
  margin-bottom: 14px; position: relative; overflow: hidden;
}
<<<<<<< HEAD
.token-hero::after {
  content: ''; position: absolute; right: -20px; top: -20px;
  width: 110px; height: 110px;
  background: rgba(255,255,255,0.05); border-radius: 50%;
}
.token-label { font-size: 11px; opacity: .75; margin-bottom: 4px; }
.token-num {
  font-family: 'DM Serif Display', Georgia, serif;
  font-size: 48px; line-height: 1; margin-bottom: 14px;
}
.token-pill {
  display: inline-flex; align-items: center; gap: 7px;
  background: rgba(255,255,255,0.15); border-radius: 20px;
  padding: 6px 14px; font-size: 12px;
}
.pulse-dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: #4ade80; flex-shrink: 0;
  animation: pulsate 1.5s ease-in-out infinite;
}
@keyframes pulsate {
  0%,100% { opacity:1; transform:scale(1); }
  50%      { opacity:.5; transform:scale(1.35); }
}

/* ── QUEUE STATS ── */
.queue-stats {
  display: grid; grid-template-columns: repeat(3, 1fr);
  gap: 10px; margin-bottom: 14px;
}
.qstat {
  background: #fff; border-radius: 12px;
  padding: 12px 8px; text-align: center;
  border: 1px solid #e0e9ff;
}
.qstat-num {
  font-family: 'DM Serif Display', Georgia, serif;
  font-size: 22px; color: #1a2e6e;
  word-break: break-all;
}
.qstat-lbl { font-size: 10px; color: #94a3b8; margin-top: 3px; }

/* ── CARD ── */
.card {
  background: #fff; border-radius: 14px;
  padding: 16px; border: 1px solid #e0e9ff;
  margin-bottom: 14px;
}
.card-title {
  font-size: 11px; color: #94a3b8; font-weight: 600;
  text-transform: uppercase; letter-spacing: .6px;
  margin-bottom: 12px;
}

/* ── PROGRESS ── */
.progress-labels {
  display: flex; justify-content: space-between;
  font-size: 11px; color: #94a3b8; margin-bottom: 6px;
}
.progress-bar {
  height: 6px; background: #e0e9ff;
  border-radius: 10px; overflow: hidden;
}
.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #3b82f6, #60a5fa);
  border-radius: 10px; transition: width .5s;
}

/* ── NO TOKEN ── */
.no-token {
  background: #fff; border-radius: 16px;
  padding: 28px 20px; text-align: center;
  border: 2px dashed #bfdbfe; margin-bottom: 14px;
}
.no-token p { color: #64748b; font-size: 14px; line-height: 1.6; margin-bottom: 16px; }
.gen-btn {
  background: #3b82f6; color: #fff; border: none;
  padding: 13px; border-radius: 10px;
  font-size: 14px; font-weight: 600; cursor: pointer;
  width: 100%; font-family: 'DM Sans', Arial, sans-serif;
}
.gen-btn:hover { background: #2563eb; }

/* ── MED TAG ── */
.med-tag {
  display: inline-block;
  background: #f0f9ff; color: #0284c7;
  font-size: 11px; padding: 2px 8px;
  border-radius: 20px; margin: 2px 2px 0 0;
  border: 1px solid #bae6fd;
}

/* ── QUICK LINKS ── */
.quick-links { display: flex; gap: 10px; margin-bottom: 14px; }
.quick-link {
  flex: 1; background: #fff; border-radius: 12px;
  padding: 14px; border: 1px solid #e0e9ff;
  text-align: center; cursor: pointer;
  text-decoration: none !important; color: inherit;
  display: block;
}
.quick-link:hover { background: #f8faff; }
.quick-link .ql-icon { font-size: 24px; margin-bottom: 5px; }
.quick-link .ql-label { font-size: 12px; color: #334155; font-weight: 500; }

/* ── PROFILE HERO ── */
.profile-hero {
  background: linear-gradient(135deg, #0f1e4a, #1e40af);
  border-radius: 16px; padding: 24px;
  color: #fff; text-align: center; margin-bottom: 14px;
}
.profile-avatar {
  width: 68px; height: 68px; border-radius: 50%;
  background: rgba(255,255,255,0.2);
  display: flex; align-items: center; justify-content: center;
  font-size: 24px; font-weight: 700; margin: 0 auto 10px;
  border: 3px solid rgba(255,255,255,0.3);
}
.profile-name {
  font-family: 'DM Serif Display', Georgia, serif;
  font-size: 22px; font-weight: 400;
}
.profile-pid {
  font-size: 11px; background: rgba(255,255,255,0.15);
  padding: 3px 10px; border-radius: 20px;
  display: inline-block; margin-top: 6px;
}

/* ── PROFILE ROWS ── */
.profile-row {
  display: flex; justify-content: space-between;
  align-items: center; padding: 10px 0;
  border-bottom: 1px solid #f1f5f9; font-size: 13px;
}
=======
.token-hero::after { content: ''; position: absolute; right: -20px; top: -20px; width: 110px; height: 110px; background: rgba(255,255,255,0.05); border-radius: 50%; }
.token-label { font-size: 11px; opacity: .75; margin-bottom: 4px; }
.token-num { font-family: 'DM Serif Display', Georgia, serif; font-size: 48px; line-height: 1; margin-bottom: 14px; }
.token-pill { display: inline-flex; align-items: center; gap: 7px; background: rgba(255,255,255,0.15); border-radius: 20px; padding: 6px 14px; font-size: 12px; }
.pulse-dot { width: 8px; height: 8px; border-radius: 50%; background: #4ade80; flex-shrink: 0; animation: pulsate 1.5s ease-in-out infinite; }
@keyframes pulsate { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.35)} }

/* QUEUE STATS */
.queue-stats { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; margin-bottom: 14px; }
.qstat { background: #fff; border-radius: 12px; padding: 12px 8px; text-align: center; border: 1px solid #e0e9ff; }
.qstat-num { font-family: 'DM Serif Display', Georgia, serif; font-size: 22px; color: #1a2e6e; word-break: break-all; }
.qstat-lbl { font-size: 10px; color: #94a3b8; margin-top: 3px; }

/* CARD */
.card { background: #fff; border-radius: 14px; padding: 16px; border: 1px solid #e0e9ff; margin-bottom: 14px; }
.card-title { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .6px; margin-bottom: 12px; }

/* PROGRESS */
.progress-labels { display: flex; justify-content: space-between; font-size: 11px; color: #94a3b8; margin-bottom: 6px; }
.progress-bar { height: 6px; background: #e0e9ff; border-radius: 10px; overflow: hidden; }
.progress-fill { height: 100%; background: linear-gradient(90deg,#3b82f6,#60a5fa); border-radius: 10px; transition: width .5s; }

/* NO TOKEN */
.no-token { background: #fff; border-radius: 16px; padding: 28px 20px; text-align: center; border: 2px dashed #bfdbfe; margin-bottom: 14px; }
.no-token p { color: #64748b; font-size: 14px; line-height: 1.6; margin-bottom: 16px; }
.gen-btn { background: #3b82f6; color: #fff; border: none; padding: 13px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; width: 100%; font-family: 'DM Sans', Arial, sans-serif; }
.gen-btn:hover { background: #2563eb; }

/* MED TAG */
.med-tag { display: inline-block; background: #f0f9ff; color: #0284c7; font-size: 11px; padding: 2px 8px; border-radius: 20px; margin: 2px 2px 0 0; border: 1px solid #bae6fd; }

/* QUICK LINKS */
.quick-links { display: flex; gap: 10px; margin-bottom: 14px; }
.quick-link { flex: 1; background: #fff; border-radius: 12px; padding: 14px; border: 1px solid #e0e9ff; text-align: center; cursor: pointer; display: block; text-decoration: none !important; color: inherit; }
.quick-link:hover { background: #f8faff; }
.ql-icon { font-size: 24px; margin-bottom: 5px; }
.ql-label { font-size: 12px; color: #334155; font-weight: 500; }

/* PROFILE HERO */
.profile-hero { background: linear-gradient(135deg,#0f1e4a,#1e40af); border-radius: 16px; padding: 24px; color: #fff; text-align: center; margin-bottom: 14px; }
.profile-avatar { width: 68px; height: 68px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; margin: 0 auto 10px; border: 3px solid rgba(255,255,255,0.3); }
.profile-name { font-family: 'DM Serif Display', Georgia, serif; font-size: 22px; font-weight: 400; }
.profile-pid { font-size: 11px; background: rgba(255,255,255,0.15); padding: 3px 10px; border-radius: 20px; display: inline-block; margin-top: 6px; }

/* PROFILE ROWS */
.profile-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
>>>>>>> 41036b6 (first commit)
.profile-row:last-child { border-bottom: none; }
.profile-row .lbl { color: #94a3b8; }
.profile-row .val { color: #0f172a; font-weight: 500; }

<<<<<<< HEAD
/* ── HISTORY ── */
.history-item {
  background: #fff; border-radius: 14px;
  padding: 14px; border: 1px solid #e0e9ff;
  margin-bottom: 10px;
}
.history-header {
  display: flex; justify-content: space-between;
  margin-bottom: 6px;
}
.history-date { font-size: 11px; color: #94a3b8; }
.history-doc { font-size: 11px; color: #3b82f6; font-weight: 500; }
.history-diagnosis {
  font-size: 14px; font-weight: 600;
  color: #0f172a; margin-bottom: 6px;
}
.empty-state {
  text-align: center; padding: 40px 20px;
  color: #94a3b8; font-size: 14px;
}
=======
/* HISTORY */
.history-item { background: #fff; border-radius: 14px; padding: 14px; border: 1px solid #e0e9ff; margin-bottom: 10px; }
.history-header { display: flex; justify-content: space-between; margin-bottom: 6px; }
.history-date { font-size: 11px; color: #94a3b8; }
.history-doc { font-size: 11px; color: #3b82f6; font-weight: 500; }
.history-diagnosis { font-size: 14px; font-weight: 600; color: #0f172a; margin-bottom: 6px; }
.empty-state { text-align: center; padding: 40px 20px; color: #94a3b8; font-size: 14px; }

/* LIVE QUEUE BADGE inside home */
.live-badge { display:inline-flex; align-items:center; gap:5px; background:#eff6ff; color:#3b82f6; font-size:11px; font-weight:600; padding:3px 10px; border-radius:20px; margin-bottom:10px; }
>>>>>>> 41036b6 (first commit)
</style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
  <a href="#" class="topbar-logo" onclick="switchTab('home');return false;">Care<span>Flow</span></a>
  <div class="topbar-right">
    <div class="topbar-avatar"><?= $initials ?></div>
    <span class="topbar-name"><?= $fname ?></span>
    <a href="../auth/logout.php" class="topbar-logout">Logout</a>
  </div>
</div>

<<<<<<< HEAD
<!-- ══ HOME SCREEN ══ -->
<div class="screen active" id="screen-home">

=======
<!-- ══ HOME ══ -->
<div class="screen active" id="screen-home">
>>>>>>> 41036b6 (first commit)
  <div class="greeting">
    <h2>Hello, <?= $fname ?> 👋</h2>
    <p><?= $token ? 'You have an active consultation token' : 'No active token — generate one to join the queue' ?></p>
  </div>

  <?php if ($token): ?>
<<<<<<< HEAD
    <!-- Token active -->
    <div class="token-hero">
      <div class="token-label">Your consultation token</div>
      <div class="token-num"><?= htmlspecialchars($token['token_id']) ?></div>
      <div class="token-pill">
        <div class="pulse-dot"></div>
        Now serving: <?= $ns_token ?> &nbsp;·&nbsp; <?= $position ?> ahead of you
      </div>
    </div>

    <div class="queue-stats">
      <div class="qstat">
        <div class="qstat-num"><?= $ns_token ?></div>
        <div class="qstat-lbl">Now Serving</div>
      </div>
      <div class="qstat">
        <div class="qstat-num"><?= $position ?></div>
        <div class="qstat-lbl">Ahead of you</div>
      </div>
      <div class="qstat">
        <div class="qstat-num"><?= $wait ?></div>
        <div class="qstat-lbl">Est. wait (min)</div>
      </div>
    </div>

    <div class="card">
      <div class="card-title">Queue Progress</div>
      <div class="progress-labels">
        <span>Queue start</span>
        <span>Your token</span>
      </div>
      <div class="progress-bar">
        <div class="progress-fill" style="width:<?= $fill ?>%"></div>
      </div>
    </div>

  <?php else: ?>
    <!-- No token -->
    <div class="no-token">
      <div style="font-size:44px;margin-bottom:12px">🏥</div>
      <p>You don't have an active token.<br>Generate one to join the consultation queue.</p>
      <form action="generate_token.php" method="POST">
        <button type="submit" class="gen-btn">Generate Consultation Token</button>
      </form>
    </div>
=======
  <div class="token-hero">
    <div class="token-label">Your consultation token</div>
    <div class="token-num"><?= htmlspecialchars($token['token_id']) ?></div>
    <div class="token-pill">
      <div class="pulse-dot"></div>
      Now serving: <?= $ns_token ?> &nbsp;·&nbsp; <?= $position ?> ahead of you
    </div>
  </div>

  <div class="queue-stats">
    <div class="qstat"><div class="qstat-num"><?= $ns_token ?></div><div class="qstat-lbl">Now Serving</div></div>
    <div class="qstat"><div class="qstat-num"><?= $position ?></div><div class="qstat-lbl">Ahead of you</div></div>
    <div class="qstat"><div class="qstat-num"><?= $wait ?></div><div class="qstat-lbl">Est. wait (min)</div></div>
  </div>

  <div class="card">
    <div class="card-title">Queue Progress</div>
    <div class="progress-labels"><span>Queue start</span><span>Your token</span></div>
    <div class="progress-bar"><div class="progress-fill" style="width:<?= $fill ?>%"></div></div>
  </div>

  <?php else: ?>
  <div class="no-token">
    <div style="font-size:44px;margin-bottom:12px">🏥</div>
    <p>You don't have an active token.<br>Generate one to join the consultation queue.</p>
    <form action="generate_token.php" method="POST">
      <button type="submit" class="gen-btn">Generate Consultation Token</button>
    </form>
  </div>
>>>>>>> 41036b6 (first commit)
  <?php endif; ?>

  <?php if ($latest): ?>
  <div class="card">
    <div class="card-title">Latest Prescription</div>
    <div class="history-diagnosis"><?= htmlspecialchars($latest['diagnosis']) ?></div>
    <div style="font-size:12px;color:#94a3b8;margin:3px 0 8px">
      <?= date('d M Y', strtotime($latest['created_at'])) ?> · <?= htmlspecialchars($latest['doctor_id']) ?>
    </div>
    <?php foreach (explode(',', $latest['medicines']) as $m): ?>
      <span class="med-tag"><?= htmlspecialchars(trim($m)) ?></span>
    <?php endforeach; ?>
    <div style="margin-top:12px;font-size:12px;color:#3b82f6;cursor:pointer"
         onclick="switchTab('history')">View full history →</div>
  </div>
  <?php endif; ?>

  <div class="quick-links">
    <a class="quick-link" onclick="switchTab('history');return false;" href="#">
<<<<<<< HEAD
      <div class="ql-icon">📋</div>
      <div class="ql-label">Medical History</div>
    </a>
    <a class="quick-link" onclick="switchTab('profile');return false;" href="#">
      <div class="ql-icon">👤</div>
      <div class="ql-label">My Profile</div>
    </a>
  </div>

</div><!-- /home -->

<!-- ══ HISTORY SCREEN ══ -->
=======
      <div class="ql-icon">📋</div><div class="ql-label">Medical History</div>
    </a>
    <a class="quick-link" onclick="switchTab('profile');return false;" href="#">
      <div class="ql-icon">👤</div><div class="ql-label">My Profile</div>
    </a>
  </div>
</div>

<!-- ══ HISTORY ══ -->
>>>>>>> 41036b6 (first commit)
<div class="screen" id="screen-history">
  <div class="greeting">
    <h2>Medical History</h2>
    <p>All your past consultations</p>
  </div>
<<<<<<< HEAD

=======
>>>>>>> 41036b6 (first commit)
  <?php
  $count = 0;
  while ($row = mysqli_fetch_assoc($history)):
    $count++;
    $meds = explode(',', $row['medicines']);
  ?>
  <div class="history-item">
    <div class="history-header">
      <span class="history-date"><?= date('d M Y', strtotime($row['created_at'])) ?></span>
      <span class="history-doc"><?= htmlspecialchars($row['doctor_id']) ?></span>
    </div>
    <div class="history-diagnosis"><?= htmlspecialchars($row['diagnosis']) ?></div>
    <?php foreach ($meds as $m): ?>
      <span class="med-tag"><?= htmlspecialchars(trim($m)) ?></span>
    <?php endforeach; ?>
  </div>
  <?php endwhile; ?>
<<<<<<< HEAD

=======
>>>>>>> 41036b6 (first commit)
  <?php if ($count === 0): ?>
  <div class="empty-state">
    <div style="font-size:40px;margin-bottom:10px">📋</div>
    No medical history yet.
  </div>
  <?php endif; ?>
<<<<<<< HEAD
</div><!-- /history -->

<!-- ══ PROFILE SCREEN ══ -->
=======
</div>

<!-- ══ PROFILE ══ -->
>>>>>>> 41036b6 (first commit)
<div class="screen" id="screen-profile">
  <div class="profile-hero">
    <div class="profile-avatar"><?= $initials ?></div>
    <div class="profile-name"><?= $name ?></div>
    <div class="profile-pid"><?= $patient['patient_id'] ?></div>
  </div>
  <div class="card">
    <div class="card-title">Personal Info</div>
    <div class="profile-row"><span class="lbl">Full Name</span><span class="val"><?= $name ?></span></div>
    <div class="profile-row"><span class="lbl">Date of Birth</span><span class="val"><?= htmlspecialchars($patient['dob']) ?></span></div>
    <div class="profile-row"><span class="lbl">Phone</span><span class="val"><?= htmlspecialchars($patient['phone']) ?></span></div>
    <div class="profile-row"><span class="lbl">Email</span><span class="val"><?= htmlspecialchars($patient['email']) ?></span></div>
    <div class="profile-row"><span class="lbl">Patient ID</span><span class="val"><?= $patient['patient_id'] ?></span></div>
    <div class="profile-row"><span class="lbl">Status</span><span class="val" style="color:#3b82f6">Active</span></div>
  </div>
<<<<<<< HEAD
</div><!-- /profile -->

<!-- ══ BOTTOM NAV ══ -->
=======
</div>

<!-- BOTTOM NAV -->
>>>>>>> 41036b6 (first commit)
<div class="bottom-nav">
  <button class="nav-item active" id="tab-home" onclick="switchTab('home')">
    <span class="nav-icon">🏠</span>
    <span class="nav-label">Home</span>
  </button>
  <button class="nav-item" id="tab-history" onclick="switchTab('history')">
    <span class="nav-icon">📋</span>
    <span class="nav-label">History</span>
  </button>
  <button class="nav-item" id="tab-profile" onclick="switchTab('profile')">
    <span class="nav-icon">👤</span>
    <span class="nav-label">Profile</span>
  </button>
</div>

<script>
<<<<<<< HEAD
=======
// ── Tab switcher ──
>>>>>>> 41036b6 (first commit)
function switchTab(tab) {
  document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('screen-' + tab).classList.add('active');
  document.getElementById('tab-' + tab).classList.add('active');
  window.scrollTo(0, 0);
<<<<<<< HEAD
}
// Auto-refresh every 6 seconds to update queue position
setInterval(() => location.reload(), 6000);
=======
  // Remember active tab so reload comes back to same tab
  localStorage.setItem('cf_tab', tab);
}

// ── Restore tab after reload ──
window.addEventListener('DOMContentLoaded', function () {
  var saved = localStorage.getItem('cf_tab');
  if (saved && document.getElementById('screen-' + saved)) {
    switchTab(saved);
  }
});

// ── Smart auto-refresh: ONLY refreshes when on Home tab ──
setInterval(function () {
  var active = localStorage.getItem('cf_tab') || 'home';
  if (active === 'home') {
    location.reload();
  }
}, 8000);
>>>>>>> 41036b6 (first commit)
</script>
</body>
</html>
