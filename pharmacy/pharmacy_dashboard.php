<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "pharmacist") {
    header("Location: ../staff/login.html"); exit;
}
include("../config/db.php");
$staff_id = $_SESSION['user'];
$res   = mysqli_query($conn, "SELECT * FROM staff WHERE staff_id='$staff_id'");
$staff = mysqli_fetch_assoc($res);
$parts    = explode(' ', $staff['name']);
$initials = strtoupper(substr($parts[0],0,1)) . (isset($parts[1]) ? strtoupper(substr($parts[1],0,1)) : '');

$pending   = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM pharmacy_orders WHERE status='Pending'"))['t'];
$ready     = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM pharmacy_orders WHERE status='Ready'"))['t'];
$collected = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM pharmacy_orders WHERE status='Collected' AND DATE(created_at)=CURDATE()"))['t'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pharmacy Dashboard | CareFlow</title>
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
.stat-num { font-family: 'DM Serif Display', Georgia, serif; font-size: 26px; }
.stat-lbl { font-size: 10px; color: #94a3b8; margin-top: 2px; }

.order-card { background: #fff; border-radius: 14px; padding: 16px; border: 1px solid #e0e9ff; margin-bottom: 10px; }
.order-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
.order-token { font-family: 'DM Serif Display', Georgia, serif; font-size: 18px; color: #0f172a; }
.order-patient { font-size: 12px; color: #94a3b8; margin-bottom: 8px; }
.med-tag { display: inline-block; background: #f0f9ff; color: #0284c7; font-size: 11px; padding: 2px 8px; border-radius: 20px; margin: 2px 2px 0 0; border: 1px solid #bae6fd; }
.order-actions { display: flex; gap: 8px; margin-top: 12px; flex-wrap: wrap; }
.badge { font-size: 11px; padding: 3px 10px; border-radius: 20px; font-weight: 600; }
.badge-pending  { background: #fef3c7; color: #b45309; }
.badge-ready    { background: #dbeafe; color: #1d4ed8; }
.badge-collected{ background: #dcfce7; color: #15803d; }
.action-btn { border: none; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: 'DM Sans', Arial, sans-serif; }
.btn-ready { background: #3b82f6; color: #fff; }
.btn-ready:hover { background: #2563eb; }
.btn-collected { background: #059669; color: #fff; }
.btn-collected:hover { background: #047857; }

.pharm-hero { background: linear-gradient(135deg,#0f1e4a,#1e40af); border-radius: 16px; padding: 24px; color: #fff; text-align: center; margin-bottom: 14px; }
.ph-avatar { width: 68px; height: 68px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; margin: 0 auto 10px; border: 3px solid rgba(255,255,255,0.3); }
.ph-name { font-family: 'DM Serif Display', Georgia, serif; font-size: 22px; font-weight: 400; }
.ph-dept { font-size: 12px; opacity: .8; margin-top: 4px; }
.ph-id { font-size: 11px; background: rgba(255,255,255,0.15); padding: 3px 10px; border-radius: 20px; display: inline-block; margin-top: 6px; }
.card { background: #fff; border-radius: 14px; padding: 16px; border: 1px solid #e0e9ff; margin-bottom: 14px; }
.card-title { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .6px; margin-bottom: 12px; }
.profile-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
.profile-row:last-child { border-bottom: none; }
.profile-row .lbl { color: #94a3b8; }
.profile-row .val { color: #0f172a; font-weight: 500; }
.empty-state { text-align: center; padding: 40px 20px; color: #94a3b8; font-size: 14px; }
</style>
</head>
<body>

<div class="topbar">
  <a href="#" class="topbar-logo" onclick="switchTab('orders');return false;">Care<span>Flow</span></a>
  <div class="topbar-right">
    <div class="topbar-avatar"><?= $initials ?></div>
    <span class="topbar-name"><?= htmlspecialchars($staff['name']) ?></span>
    <span class="topbar-badge">Pharmacy</span>
    <a href="../auth/logout.php" class="topbar-logout">Logout</a>
  </div>
</div>

<!-- ORDERS SCREEN -->
<div class="screen active" id="screen-orders">
  <div class="greeting">
    <h2>Pharmacy Orders 💊</h2>
    <p>Live prescription orders from doctors</p>
  </div>
  <div class="stats-row">
    <div class="stat-box"><div class="stat-num" style="color:#f59e0b"><?= $pending ?></div><div class="stat-lbl">Pending</div></div>
    <div class="stat-box"><div class="stat-num" style="color:#3b82f6"><?= $ready ?></div><div class="stat-lbl">Ready</div></div>
    <div class="stat-box"><div class="stat-num" style="color:#059669"><?= $collected ?></div><div class="stat-lbl">Collected</div></div>
  </div>
  <div id="ordersArea">Loading orders...</div>
</div>

<!-- PROFILE SCREEN -->
<div class="screen" id="screen-profile">
  <div class="pharm-hero">
    <div class="ph-avatar"><?= $initials ?></div>
    <div class="ph-name"><?= htmlspecialchars($staff['name']) ?></div>
    <div class="ph-dept"><?= htmlspecialchars($staff['department']) ?></div>
    <div class="ph-id"><?= $staff['staff_id'] ?></div>
  </div>
  <div class="card">
    <div class="card-title">Staff Info</div>
    <div class="profile-row"><span class="lbl">Staff ID</span><span class="val"><?= $staff['staff_id'] ?></span></div>
    <div class="profile-row"><span class="lbl">Department</span><span class="val"><?= htmlspecialchars($staff['department']) ?></span></div>
    <div class="profile-row"><span class="lbl">Email</span><span class="val"><?= htmlspecialchars($staff['email']) ?></span></div>
    <div class="profile-row"><span class="lbl">Role</span><span class="val">Pharmacist</span></div>
    <div class="profile-row"><span class="lbl">Status</span><span class="val" style="color:#3b82f6">Active</span></div>
  </div>
</div>

<!-- BOTTOM NAV -->
<div class="bottom-nav">
  <button class="nav-item active" id="tab-orders" onclick="switchTab('orders')">
    <span class="nav-icon">💊</span><span class="nav-label">Orders</span>
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
function loadOrders() {
  fetch('live_orders.php').then(r => r.text()).then(html => {
    document.getElementById('ordersArea').innerHTML = html;
  });
}
function updateStatus(id, status) {
  fetch('update_status.php?id=' + id + '&status=' + status).then(() => loadOrders());
}
setInterval(loadOrders, 5000);
window.onload = loadOrders;
</script>
</body>
</html>
