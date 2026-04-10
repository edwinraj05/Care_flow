<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != "doctor") {
    header("Location: ../staff/login.html");
    exit;
}

include("../config/db.php");

$staff_id = $_SESSION['user'] ?? '';

$res = mysqli_query($conn, "SELECT * FROM staff WHERE staff_id='$staff_id'");
$staff = mysqli_fetch_assoc($res);

$parts = explode(' ', $staff['name']);
$initials = strtoupper(substr($parts[0],0,1)) . (isset($parts[1]) ? strtoupper(substr($parts[1],0,1)) : '');
$fname = htmlspecialchars($parts[0]);

$waiting = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM tokens WHERE status='Waiting'"))['t'];
$done = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM tokens WHERE status='Completed' AND DATE(created_at)=CURDATE()"))['t'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Doctor Dashboard | CareFlow</title>
  <link rel="stylesheet" href="../public/style.css">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'DM Sans', Arial, sans-serif; background: #f0f4ff; padding-bottom: 80px; }
.topbar-logo { text-decoration: none !important; }
.nav-item { display: flex; flex-direction: column; align-items: center; gap: 3px; cursor: pointer; padding: 4px 20px; border-radius: 10px; text-decoration: none !important; border: none; background: none; outline: none; -webkit-tap-highlight-color: transparent; font-family: 'DM Sans', Arial, sans-serif; }
.nav-item.active { background: #eff6ff; }
.nav-label { font-size: 11px; color: #94a3b8; font-weight: 500; text-decoration: none !important; }
.nav-item.active .nav-label { color: #3b82f6; }
.nav-icon { font-size: 22px; line-height: 1; }
.bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; border-top: 1px solid #e0e9ff; display: flex; justify-content: space-around; padding: 8px 0 10px; z-index: 200; }
.topbar { position: sticky; top: 0; z-index: 200; }
</style>
  <style>
    body { padding-bottom: 80px; }
    .now-serving { background:linear-gradient(135deg,#0f1e4a 0%,#1e40af 100%); border-radius:16px; padding:18px; color:white; margin-bottom:14px; position:relative; overflow:hidden; }
    .now-serving::after { content:''; position:absolute; right:-20px; top:-20px; width:100px; height:100px; background:rgba(255,255,255,0.05); border-radius:50%; }
    .ns-label { font-size:11px; opacity:.75; margin-bottom:4px; }
    .ns-token { font-family:'DM Serif Display',serif; font-size:40px; line-height:1; margin-bottom:8px; }
    .ns-name { font-size:15px; font-weight:600; margin-bottom:2px; }
    .ns-meta { font-size:12px; opacity:.75; }
    .ns-actions { display:flex; gap:10px; margin-top:14px; }
    .btn-call { flex:1; background:white; color:#1e40af; border:none; padding:10px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; }
    .btn-call:hover { background:#eff6ff; }
    .btn-complete { flex:1; background:rgba(255,255,255,0.15); color:white; border:2px solid rgba(255,255,255,0.4); padding:10px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; }
    .btn-complete:hover { background:rgba(255,255,255,0.25); }
    .queue-item { display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid #f1f5f9; }
    .queue-item:last-child { border-bottom:none; }
    .q-badge { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; flex-shrink:0; }
    .q-info { flex:1; }
    .q-name { font-size:13px; font-weight:500; color:#0f172a; }
    .q-time { font-size:11px; color:#94a3b8; }
    .rx-banner { background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:12px 14px; display:flex; align-items:center; gap:10px; margin-bottom:12px; }
    .rx-icon { width:36px; height:36px; border-radius:50%; background:#3b82f6; display:flex; align-items:center; justify-content:center; color:white; font-weight:600; font-size:13px; flex-shrink:0; }
    .rx-pname { font-size:14px; font-weight:600; color:#1e3a8a; }
    .rx-pid { font-size:11px; color:#3b82f6; }
    .rx-textarea { width:100%; border:1.5px solid #e0e9ff; border-radius:10px; padding:10px 12px; font-size:13px; color:#0f172a; outline:none; font-family:'DM Sans',sans-serif; resize:none; }
    .rx-textarea:focus { border-color:#3b82f6; }
    .submit-btn { background:#3b82f6; color:white; border:none; padding:13px; border-radius:12px; font-size:14px; font-weight:600; cursor:pointer; width:100%; margin-top:4px; }
    .submit-btn:hover { background:#2563eb; }
    .doctor-hero { background:linear-gradient(135deg,#0f1e4a,#1e40af); border-radius:16px; padding:22px; color:white; text-align:center; margin-bottom:14px; }
    .d-avatar { width:68px; height:68px; border-radius:50%; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:700; margin:0 auto 10px; border:3px solid rgba(255,255,255,0.3); }
    .d-name { font-family:'DM Serif Display',serif; font-size:22px; }
    .d-dept { font-size:12px; opacity:.8; margin-top:4px; }
    .d-id { font-size:11px; background:rgba(255,255,255,0.15); padding:3px 10px; border-radius:20px; display:inline-block; margin-top:6px; }
    .screen { display:none; padding:16px; }
    .screen.active { display:block; }
    .stats-row { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:14px; }
    .stat-box { background:white; border-radius:12px; padding:12px; text-align:center; border:1px solid #e0e9ff; }
    .stat-num { font-family:'DM Serif Display',serif; font-size:26px; }
    .stat-lbl { font-size:10px; color:#94a3b8; margin-top:2px; }
    .pulse { width:8px; height:8px; border-radius:50%; background:#60a5fa; animation:pulse 1.5s infinite; display:inline-block; margin-right:6px; }
    @keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(1.3)}}
  </style>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Doctor Dashboard | CareFlow</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'DM Sans', Arial, sans-serif; background: #f0f4ff; color: #0f172a; }

/* TOPBAR */
.topbar { background: #0f1e4a; display: flex; align-items: center; justify-content: space-between; padding: 12px 20px; position: sticky; top: 0; z-index: 200; }
.topbar-logo { font-family: 'DM Serif Display', Georgia, serif; font-size: 20px; color: #fff; text-decoration: none; }
.topbar-logo span { color: #60a5fa; }
.topbar-right { display: flex; align-items: center; gap: 10px; }
.topbar-avatar { width: 34px; height: 34px; border-radius: 50%; background: #3b82f6; color: #fff; font-weight: 600; font-size: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.topbar-name { color: #e2e8f0; font-size: 13px; }
.topbar-badge { background: #3b82f6; color: #fff; font-size: 10px; padding: 2px 8px; border-radius: 20px; font-weight: 600; }
.topbar-logout { color: #94a3b8; font-size: 12px; border: 1px solid rgba(255,255,255,0.25); padding: 4px 10px; border-radius: 6px; text-decoration: none; }
.topbar-logout:hover { background: rgba(255,255,255,0.08); color: #fff; }

/* BOTTOM NAV */
.bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; border-top: 1px solid #e0e9ff; display: flex; justify-content: space-around; padding: 8px 0 12px; z-index: 200; }
.nav-item { display: flex; flex-direction: column; align-items: center; gap: 3px; cursor: pointer; padding: 6px 22px; border-radius: 10px; border: none; background: none; outline: none; -webkit-tap-highlight-color: transparent; font-family: 'DM Sans', Arial, sans-serif; text-decoration: none; }
.nav-item.active { background: #eff6ff; }
.nav-icon { font-size: 22px; line-height: 1; }
.nav-label { font-size: 11px; color: #94a3b8; font-weight: 500; }
.nav-item.active .nav-label { color: #3b82f6; }

/* SCREENS */
.screen { display: none; padding: 16px; padding-bottom: 90px; }
.screen.active { display: block; }

/* GREETING */
.greeting { margin-bottom: 14px; }
.greeting h2 { font-family: 'DM Serif Display', Georgia, serif; font-size: 22px; color: #0f172a; font-weight: 400; margin-bottom: 3px; }
.greeting p { font-size: 13px; color: #64748b; }

/* STATS */
.stats-row { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; margin-bottom: 14px; }
.stat-box { background: #fff; border-radius: 12px; padding: 12px; text-align: center; border: 1px solid #e0e9ff; }
.stat-num { font-family: 'DM Serif Display', Georgia, serif; font-size: 26px; }
.stat-lbl { font-size: 10px; color: #94a3b8; margin-top: 2px; }

/* NOW SERVING */
.now-serving { background: linear-gradient(135deg,#0f1e4a 0%,#1e40af 100%); border-radius: 16px; padding: 18px; color: #fff; margin-bottom: 14px; position: relative; overflow: hidden; }
.now-serving::after { content:''; position:absolute; right:-20px; top:-20px; width:110px; height:110px; background:rgba(255,255,255,0.05); border-radius:50%; }
.ns-label { font-size: 11px; opacity: .75; margin-bottom: 4px; display:flex; align-items:center; gap:6px; }
.pulse-dot { width:8px; height:8px; border-radius:50%; background:#60a5fa; animation:pulsate 1.5s ease-in-out infinite; }
@keyframes pulsate { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.35)} }
.ns-token { font-family: 'DM Serif Display', Georgia, serif; font-size: 40px; line-height: 1; margin-bottom: 8px; }
.ns-name { font-size: 15px; font-weight: 600; margin-bottom: 2px; }
.ns-meta { font-size: 12px; opacity: .75; }
.ns-actions { display: flex; gap: 10px; margin-top: 14px; }
.btn-call { flex:1; background:#fff; color:#1e40af; border:none; padding:11px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; font-family:'DM Sans',Arial,sans-serif; }
.btn-call:hover { background:#eff6ff; }
.btn-complete { flex:2; background:rgba(255,255,255,0.15); color:#fff; border:2px solid rgba(255,255,255,0.4); padding:11px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; font-family:'DM Sans',Arial,sans-serif; }
.btn-complete:hover { background:rgba(255,255,255,0.25); }

/* QUEUE LIST */
.card { background:#fff; border-radius:14px; padding:16px; border:1px solid #e0e9ff; margin-bottom:14px; }
.card-title { font-size:11px; color:#94a3b8; font-weight:600; text-transform:uppercase; letter-spacing:.6px; margin-bottom:12px; }
.queue-item { display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid #f1f5f9; }
.queue-item:last-child { border-bottom:none; }
.q-badge { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; flex-shrink:0; }
.q-name { font-size:13px; font-weight:500; color:#0f172a; }
.q-time { font-size:11px; color:#94a3b8; }
.q-pos { font-size:12px; color:#94a3b8; font-weight:500; margin-left:auto; }

/* PRESCRIPTION FORM */
.rx-banner { background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:12px 14px; display:flex; align-items:center; gap:10px; margin-bottom:14px; }
.rx-icon { width:38px; height:38px; border-radius:50%; background:#3b82f6; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:600; font-size:13px; flex-shrink:0; }
.rx-pname { font-size:14px; font-weight:600; color:#1e3a8a; }
.rx-pid { font-size:11px; color:#3b82f6; }
.form-group { margin-bottom:12px; }
.form-label { font-size:12px; color:#64748b; font-weight:500; display:block; margin-bottom:5px; }
.form-textarea { width:100%; border:1.5px solid #e0e9ff; border-radius:10px; padding:10px 12px; font-size:13px; color:#0f172a; outline:none; font-family:'DM Sans',Arial,sans-serif; resize:none; background:#fff; }
.form-textarea:focus { border-color:#3b82f6; }
.submit-btn { background:#3b82f6; color:#fff; border:none; padding:13px; border-radius:12px; font-size:14px; font-weight:600; cursor:pointer; width:100%; font-family:'DM Sans',Arial,sans-serif; }
.submit-btn:hover { background:#2563eb; }
.success-box { background:#eff6ff; border:1.5px solid #bfdbfe; border-radius:12px; padding:20px; text-align:center; margin-bottom:14px; display:none; }
.success-box.show { display:block; }

/* PROFILE */
.doctor-hero { background:linear-gradient(135deg,#0f1e4a,#1e40af); border-radius:16px; padding:24px; color:#fff; text-align:center; margin-bottom:14px; }
.d-avatar { width:68px; height:68px; border-radius:50%; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:700; margin:0 auto 10px; border:3px solid rgba(255,255,255,0.3); }
.d-name { font-family:'DM Serif Display',Georgia,serif; font-size:22px; font-weight:400; }
.d-dept { font-size:12px; opacity:.8; margin-top:4px; }
.d-id { font-size:11px; background:rgba(255,255,255,0.15); padding:3px 10px; border-radius:20px; display:inline-block; margin-top:6px; }
.profile-row { display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #f1f5f9; font-size:13px; }
.profile-row:last-child { border-bottom:none; }
.profile-row .lbl { color:#94a3b8; }
.profile-row .val { color:#0f172a; font-weight:500; }
.today-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:14px; }
.today-stat { background:#fff; border-radius:12px; padding:14px; border:1px solid #e0e9ff; }
.today-num { font-family:'DM Serif Display',Georgia,serif; font-size:28px; color:#3b82f6; }
.today-lbl { font-size:11px; color:#94a3b8; margin-top:2px; }
</style>
</head>
<body>

<div class="topbar">
  <a href="#" class="topbar-logo" onclick="switchTab('queue');return false;">
    Care<span>Flow</span>
  </a>

  <div class="topbar-right">
    <div class="topbar-avatar"><?= $initials ?></div>
    <span class="topbar-name"><?= htmlspecialchars($staff['name']) ?></span>
    <span class="topbar-badge">Doctor</span>
    <a href="../auth/logout.php" class="topbar-logout">Logout</a>
  </div>
</div>
<!-- QUEUE SCREEN -->
<div class="screen active" id="screen-queue">
  <div class="greeting">
    <h2>Good day, Doctor 👨‍⚕️</h2>
    <p><?= htmlspecialchars($staff['department']) ?></p>
  </div>

  <?php
  $waiting = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM tokens WHERE status='Waiting'"))['t'];
  $done = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM tokens WHERE status='Completed' AND DATE(created_at)=CURDATE()"))['t'];
  ?>
  <div class="stats-row">
    <div class="stat-box"><div class="stat-num" style="color:#3b82f6"><?= $waiting ?></div><div class="stat-lbl">Waiting</div></div>
    <div class="stat-box"><div class="stat-num" style="color:#f59e0b"><?= $done ?></div><div class="stat-lbl">Completed</div></div>
    <div class="stat-box"><div class="stat-num" style="color:#1e40af"><?= $waiting + $done ?></div><div class="stat-lbl">Total Today</div></div>
  </div>

  <div class="now-serving">
    <div class="ns-label"><span class="pulse"></span>Now consulting</div>
    <div id="queueArea">Loading queue...</div>
    <div class="ns-label"><div class="pulse-dot"></div> Now consulting</div>
    <div id="queueArea">Loading...</div>
  </div>

  <div class="card">
    <div class="card-title">Waiting queue</div>
    <div id="waitingList">Loading...</div>
  </div>
</div>

<!-- PRESCRIPTION SCREEN -->
<div class="screen" id="screen-prescription">
  <div class="greeting">
    <h2>Write Prescription ✍️</h2>
    <p>Fill in the consultation details</p>
  </div>
  <form action="submit_prescription.php" method="POST">
    <div id="rx-patient-info" class="rx-banner">

  <div id="success-box" class="success-box">
    <div style="font-size:32px;margin-bottom:8px">✅</div>
    <p style="font-size:14px;font-weight:600;color:#1d4ed8">Prescription submitted!</p>
    <p style="font-size:12px;color:#64748b;margin-top:4px">Sent to pharmacy automatically.</p>
    <button onclick="switchTab('queue')" style="margin-top:14px;background:#3b82f6;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:'DM Sans',Arial,sans-serif;">Back to Queue</button>
  </div>

  <div id="rx-form-wrap">
    <div class="rx-banner">
      <div class="rx-icon" id="rx-icon">?</div>
      <div>
        <div class="rx-pname" id="rx-pname">Loading patient...</div>
        <div class="rx-pid" id="rx-pid"></div>
      </div>
    </div>
    <div class="form-group" style="margin-bottom:12px">
      <label class="form-label">Diagnosis</label>
      <textarea class="rx-textarea" name="diagnosis" rows="3" placeholder="e.g. Acute viral fever with mild cough..." required></textarea>
    </div>
    <div class="form-group" style="margin-bottom:12px">
      <label class="form-label">Medicines prescribed</label>
      <textarea class="rx-textarea" name="medicines" rows="3" placeholder="e.g. Paracetamol 500mg — 1×3 daily for 5 days" required></textarea>
    </div>
    <div class="form-group" style="margin-bottom:12px">
      <label class="form-label">Notes / Instructions</label>
      <textarea class="rx-textarea" name="notes" rows="2" placeholder="e.g. Rest for 3 days. Avoid cold drinks."></textarea>
    </div>
    <button type="submit" class="submit-btn">Submit Prescription → Pharmacy</button>
  </form>
  </div>
</div>

<!-- PROFILE SCREEN -->
<div class="screen" id="screen-profile">
  <div class="doctor-hero">
    <div class="d-avatar"><?= $initials ?></div>
    <div class="d-name"><?= htmlspecialchars($staff['name']) ?></div>
    <div class="d-dept"><?= htmlspecialchars($staff['department']) ?></div>
    <div class="d-id"><?= $staff['staff_id'] ?></div>
  </div>
  <div class="today-grid">
    <div class="today-stat"><div class="today-num"><?= $waiting + $done ?></div><div class="today-lbl">Patients today</div></div>
    <div class="today-stat"><div class="today-num"><?= $done ?></div><div class="today-lbl">Done so far</div></div>
  </div>
  <div class="card">
    <div class="card-title">Staff Info</div>
    <div class="profile-row"><span class="lbl">Staff ID</span><span class="val"><?= $staff['staff_id'] ?></span></div>
    <div class="profile-row"><span class="lbl">Department</span><span class="val"><?= htmlspecialchars($staff['department']) ?></span></div>
    <div class="profile-row"><span class="lbl">Email</span><span class="val"><?= htmlspecialchars($staff['email']) ?></span></div>
    <div class="profile-row"><span class="lbl">Role</span><span class="val">Doctor</span></div>
    <div class="profile-row"><span class="lbl">Status</span><span class="val" style="color:#3b82f6">Active</span></div>
  </div>
</div>

<!-- BOTTOM NAV -->
<div class="bottom-nav">
  <button class="nav-item active" id="tab-queue" onclick="switchTab('queue')" href="#">
    <div class="nav-icon">🧾</div><div class="nav-label">Queue</div>
  </button>
  <button class="nav-item" id="tab-prescription" onclick="switchTab('prescription')" href="#">
    <div class="nav-icon">📝</div><div class="nav-label">Prescribe</div>
  </button>
  <button class="nav-item" id="tab-profile" onclick="switchTab('profile')" href="#">
    <div class="nav-icon">👨‍⚕️</div><div class="nav-label">Profile</div>
  <button class="nav-item active" id="tab-queue" onclick="switchTab('queue')">
    <span class="nav-icon">🧾</span><span class="nav-label">Queue</span>
  </button>
  <button class="nav-item" id="tab-prescription" onclick="switchTab('prescription')">
    <span class="nav-icon">📝</span><span class="nav-label">Prescribe</span>
  </button>
  <button class="nav-item" id="tab-profile" onclick="switchTab('profile')">
    <span class="nav-icon">👨‍⚕️</span><span class="nav-label">Profile</span>
  </button>
</div>

<script>
var currentPatient = { name:'', pid:'', token:'', initials:'' };

function switchTab(tab) {
  document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('screen-' + tab).classList.add('active');
  document.getElementById('tab-' + tab).classList.add('active');
  return false;
}

function loadQueue() {
  fetch('live_queue.php').then(r => r.text()).then(data => {
    document.getElementById('queueArea').innerHTML = data;
  });
  fetch('live_queue.php?list=1').then(r => r.text()).then(data => {
    document.getElementById('waitingList').innerHTML = data;
  });
}

function goToPrescribe() {
  fetch('live_queue.php?current=1').then(r => r.json()).then(p => {
    if (p.name) {
      document.getElementById('rx-pname').textContent = p.name;
      document.getElementById('rx-pid').textContent = p.pid + ' · Token ' + p.token;
      document.getElementById('rx-icon').textContent = p.initials;
    }
    switchTab('prescription');
  });
  window.scrollTo(0,0);
}
function handleRxSubmit(e) {
  // Let form submit normally, then show success
  setTimeout(function() {
    document.getElementById('success-box').classList.add('show');
    document.getElementById('rx-form-wrap').style.display = 'none';
  }, 300);
}

setInterval(loadQueue, 5000);
window.onload = loadQueue;
</script>
</body>
</html>
