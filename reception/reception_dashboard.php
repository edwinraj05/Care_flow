<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "reception") {
    header("Location: ../staff/login.html"); exit;
}
include("../config/db.php");
$staff_id = $_SESSION['user'];
$res   = mysqli_query($conn, "SELECT * FROM staff WHERE staff_id='$staff_id'");
$staff = mysqli_fetch_assoc($res);
$parts    = explode(' ', $staff['name']);
$initials = strtoupper(substr($parts[0],0,1)) . (isset($parts[1]) ? strtoupper(substr($parts[1],0,1)) : '');

$today_tokens = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM tokens WHERE DATE(created_at)=CURDATE()"))['t'];
$waiting      = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM tokens WHERE status='Waiting'"))['t'];
$total_patients = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM patients"))['t'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reception | CareFlow</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'DM Sans', Arial, sans-serif; background: #f0f4ff; color: #0f172a; }

.topbar { background: #0f1e4a; display: flex; align-items: center; justify-content: space-between; padding: 12px 20px; position: sticky; top: 0; z-index: 200; }
.topbar-logo { font-family: 'DM Serif Display', Georgia, serif; font-size: 20px; color: #fff; text-decoration: none; }
.topbar-logo span { color: #60a5fa; }
.topbar-right { display: flex; align-items: center; gap: 10px; }
.topbar-avatar { width: 34px; height: 34px; border-radius: 50%; background: #3b82f6; color: #fff; font-weight: 600; font-size: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.topbar-name { color: #e2e8f0; font-size: 13px; }
.topbar-badge { background: #3b82f6; color: #fff; font-size: 10px; padding: 2px 8px; border-radius: 20px; font-weight: 600; }
.topbar-logout { color: #94a3b8; font-size: 12px; border: 1px solid rgba(255,255,255,0.25); padding: 4px 10px; border-radius: 6px; text-decoration: none; }
.topbar-logout:hover { background: rgba(255,255,255,0.08); color: #fff; }

.bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; border-top: 1px solid #e0e9ff; display: flex; justify-content: space-around; padding: 8px 0 12px; z-index: 200; }
.nav-item { display: flex; flex-direction: column; align-items: center; gap: 3px; cursor: pointer; padding: 6px 22px; border-radius: 10px; border: none; background: none; outline: none; -webkit-tap-highlight-color: transparent; font-family: 'DM Sans', Arial, sans-serif; text-decoration: none; }
.nav-item.active { background: #eff6ff; }
.nav-icon { font-size: 22px; line-height: 1; }
.nav-label { font-size: 11px; color: #94a3b8; font-weight: 500; }
.nav-item.active .nav-label { color: #3b82f6; }

.screen { display: none; padding: 16px; padding-bottom: 90px; }
.screen.active { display: block; }

.greeting { margin-bottom: 14px; }
.greeting h2 { font-family: 'DM Serif Display', Georgia, serif; font-size: 22px; color: #0f172a; font-weight: 400; margin-bottom: 3px; }
.greeting p { font-size: 13px; color: #64748b; }

.stats-row { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; margin-bottom: 14px; }
.stat-box { background: #fff; border-radius: 12px; padding: 12px; text-align: center; border: 1px solid #e0e9ff; }
.stat-num { font-family: 'DM Serif Display', Georgia, serif; font-size: 26px; color: #3b82f6; }
.stat-lbl { font-size: 10px; color: #94a3b8; margin-top: 2px; }

.card { background: #fff; border-radius: 14px; padding: 18px; border: 1px solid #e0e9ff; margin-bottom: 14px; }
.card-title { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .6px; margin-bottom: 14px; }

.form-group { margin-bottom: 12px; }
.form-label { font-size: 12px; color: #64748b; font-weight: 500; display: block; margin-bottom: 5px; }
.form-input { width: 100%; border: 1.5px solid #e0e9ff; border-radius: 10px; padding: 11px 12px; font-size: 14px; color: #0f172a; outline: none; font-family: 'DM Sans', Arial, sans-serif; background: #fff; }
.form-input:focus { border-color: #3b82f6; }
.submit-btn { background: #3b82f6; color: #fff; border: none; padding: 13px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; width: 100%; font-family: 'DM Sans', Arial, sans-serif; margin-top: 4px; }
.submit-btn:hover { background: #2563eb; }

.success-box { background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 12px; padding: 18px; text-align: center; margin-bottom: 14px; display: none; }
.success-box.show { display: block; }

.token-result { background: linear-gradient(135deg,#0f1e4a,#1e40af); border-radius: 14px; padding: 20px; color: #fff; text-align: center; margin-bottom: 14px; display: none; }
.token-result.show { display: block; }
.token-result-num { font-family: 'DM Serif Display', Georgia, serif; font-size: 48px; }

.rec-hero { background: linear-gradient(135deg,#0f1e4a,#1e40af); border-radius: 16px; padding: 24px; color: #fff; text-align: center; margin-bottom: 14px; }
.rec-avatar { width: 68px; height: 68px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; margin: 0 auto 10px; border: 3px solid rgba(255,255,255,0.3); }
.rec-name { font-family: 'DM Serif Display', Georgia, serif; font-size: 22px; font-weight: 400; }
.rec-dept { font-size: 12px; opacity: .8; margin-top: 4px; }
.rec-id { font-size: 11px; background: rgba(255,255,255,0.15); padding: 3px 10px; border-radius: 20px; display: inline-block; margin-top: 6px; }
.profile-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
.profile-row:last-child { border-bottom: none; }
.profile-row .lbl { color: #94a3b8; }
.profile-row .val { color: #0f172a; font-weight: 500; }
</style>
</head>
<body>

<div class="topbar">
  <a href="#" class="topbar-logo" onclick="switchTab('register');return false;">Care<span>Flow</span></a>
  <div class="topbar-right">
    <div class="topbar-avatar"><?= $initials ?></div>
    <span class="topbar-name"><?= htmlspecialchars($staff['name']) ?></span>
    <span class="topbar-badge">Reception</span>
    <a href="../auth/logout.php" class="topbar-logout">Logout</a>
  </div>
</div>

<!-- REGISTER SCREEN -->
<div class="screen active" id="screen-register">
  <div class="greeting">
    <h2>Reception Desk 👩‍💼</h2>
    <p>Register patients and manage tokens</p>
  </div>

  <div class="stats-row">
    <div class="stat-box"><div class="stat-num"><?= $today_tokens ?></div><div class="stat-lbl">Tokens Today</div></div>
    <div class="stat-box"><div class="stat-num"><?= $waiting ?></div><div class="stat-lbl">Waiting</div></div>
    <div class="stat-box"><div class="stat-num"><?= $total_patients ?></div><div class="stat-lbl">Total Patients</div></div>
  </div>

  <div id="reg-success" class="success-box">
    <div style="font-size:32px;margin-bottom:8px">✅</div>
    <p style="font-size:14px;font-weight:600;color:#1d4ed8">Patient registered successfully!</p>
    <button onclick="document.getElementById('reg-success').classList.remove('show')" style="margin-top:10px;background:#3b82f6;color:#fff;border:none;padding:8px 20px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:'DM Sans',Arial,sans-serif;">Register Another</button>
  </div>

  <div class="card">
    <div class="card-title">Register Walk-in Patient</div>
    <form action="register_patient.php" method="POST" onsubmit="showSuccess('reg-success')">
      <div class="form-group">
        <label class="form-label">Full Name</label>
        <input class="form-input" name="name" placeholder="Patient full name" required>
      </div>
      <div class="form-group">
        <label class="form-label">Date of Birth</label>
        <input class="form-input" type="date" name="dob" required>
      </div>
      <div class="form-group">
        <label class="form-label">Phone Number</label>
        <input class="form-input" name="phone" placeholder="+91 98765 43210" required>
      </div>
      <div class="form-group">
        <label class="form-label">Email (optional)</label>
        <input class="form-input" name="email" placeholder="patient@email.com">
      </div>
      <button type="submit" class="submit-btn">Register Patient →</button>
    </form>
  </div>
</div>

<!-- TOKEN SCREEN -->
<div class="screen" id="screen-token">
  <div class="greeting">
    <h2>Generate Token 🎫</h2>
    <p>Issue a consultation token for a patient</p>
  </div>

  <div id="token-result" class="token-result">
    <div style="font-size:11px;opacity:.75;margin-bottom:4px">Token Generated</div>
    <div class="token-result-num" id="token-result-num">—</div>
    <div style="font-size:13px;opacity:.8;margin-top:8px">Patient added to the queue</div>
  </div>

  <div class="card">
    <div class="card-title">Issue Token</div>
    <form action="generate_token.php" method="POST">
      <div class="form-group">
        <label class="form-label">Patient ID</label>
        <input class="form-input" name="patient_id" placeholder="e.g. PID-0042" required>
      </div>
      <button type="submit" class="submit-btn">Generate Token →</button>
    </form>
  </div>
</div>

<!-- PROFILE SCREEN -->
<div class="screen" id="screen-profile">
  <div class="rec-hero">
    <div class="rec-avatar"><?= $initials ?></div>
    <div class="rec-name"><?= htmlspecialchars($staff['name']) ?></div>
    <div class="rec-dept"><?= htmlspecialchars($staff['department']) ?></div>
    <div class="rec-id"><?= $staff['staff_id'] ?></div>
  </div>
  <div class="card">
    <div class="card-title">Staff Info</div>
    <div class="profile-row"><span class="lbl">Staff ID</span><span class="val"><?= $staff['staff_id'] ?></span></div>
    <div class="profile-row"><span class="lbl">Department</span><span class="val"><?= htmlspecialchars($staff['department']) ?></span></div>
    <div class="profile-row"><span class="lbl">Email</span><span class="val"><?= htmlspecialchars($staff['email']) ?></span></div>
    <div class="profile-row"><span class="lbl">Role</span><span class="val">Receptionist</span></div>
    <div class="profile-row"><span class="lbl">Status</span><span class="val" style="color:#3b82f6">Active</span></div>
  </div>
</div>

<!-- BOTTOM NAV -->
<div class="bottom-nav">
  <button class="nav-item active" id="tab-register" onclick="switchTab('register')">
    <span class="nav-icon">📋</span><span class="nav-label">Register</span>
  </button>
  <button class="nav-item" id="tab-token" onclick="switchTab('token')">
    <span class="nav-icon">🎫</span><span class="nav-label">Token</span>
  </button>
  <button class="nav-item" id="tab-profile" onclick="switchTab('profile')">
    <span class="nav-icon">👤</span><span class="nav-label">Profile</span>
  </button>
</div>

<script>
function switchTab(tab) {
  document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('screen-' + tab).classList.add('active');
  document.getElementById('tab-' + tab).classList.add('active');
  window.scrollTo(0, 0);
}
function showSuccess(id) {
  document.getElementById(id).classList.add('show');
  window.scrollTo(0, 0);
}
</script>
</body>
</html>
