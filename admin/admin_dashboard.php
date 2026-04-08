<?php
session_start();
if ($_SESSION['role'] != "admin") { header("Location: ../staff/login.html"); exit; }
include("../config/db.php");
$p  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM patients"))['t'];
$d  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM staff WHERE role='doctor'"))['t'];
$ph = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM staff WHERE role='pharmacist'"))['t'];
$r  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM staff WHERE role='reception'"))['t'];
$data = mysqli_query($conn,"SELECT DATE(created_at) as day, COUNT(*) as total FROM tokens GROUP BY day ORDER BY day DESC LIMIT 7");
$days=[]; $counts=[];
while($row=mysqli_fetch_assoc($data)){ $days[]=$row['day']; $counts[]=$row['total']; }
$days=array_reverse($days); $counts=array_reverse($counts);
$recent_staff = mysqli_query($conn,"SELECT * FROM staff ORDER BY created_at DESC LIMIT 6");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Admin Dashboard | CareFlow</title>
  <link rel="stylesheet" href="../public/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { display:flex; min-height:100vh; }
    .sidebar { width:200px; background:#0a1628; display:flex; flex-direction:column; padding:20px 0; flex-shrink:0; min-height:100vh; position:fixed; top:0; left:0; bottom:0; }
    .sidebar-logo { font-family:'DM Serif Display',serif; font-size:20px; color:white; padding:0 18px 20px; border-bottom:1px solid rgba(255,255,255,0.1); margin-bottom:10px; }
    .sidebar-logo span { color:#60a5fa; }
    .sidebar a { padding:10px 18px; font-size:13px; color:rgba(255,255,255,0.6); cursor:pointer; display:flex; align-items:center; gap:8px; text-decoration:none; }
    .sidebar a:hover { background:rgba(255,255,255,0.06); color:white; }
    .sidebar a.active { background:rgba(59,130,246,0.2); color:white; border-right:2px solid #3b82f6; }
    .sidebar .spacer { flex:1; }
    .main { margin-left:200px; flex:1; padding:24px; background:#f0f4ff; min-height:100vh; }
    .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
    .page-header h1 { font-family:'DM Serif Display',serif; font-size:26px; color:#0f172a; }
    .page-header .date { font-size:12px; color:#94a3b8; }
    .kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:20px; }
    .kpi { background:white; border-radius:14px; padding:18px; border:1px solid #e0e9ff; }
    .kpi-icon { font-size:22px; margin-bottom:10px; }
    .kpi-num { font-family:'DM Serif Display',serif; font-size:32px; color:#0f172a; }
    .kpi-lbl { font-size:12px; color:#94a3b8; margin-top:2px; }
    .charts-row { display:grid; grid-template-columns:1.5fr 1fr; gap:16px; margin-bottom:20px; }
    .chart-card { background:white; border-radius:14px; padding:18px; border:1px solid #e0e9ff; }
    .chart-card h3 { font-size:13px; color:#64748b; font-weight:500; margin-bottom:14px; }
    .admin-table { width:100%; border-collapse:collapse; font-size:13px; }
    .admin-table th { text-align:left; color:#94a3b8; font-weight:500; padding:0 12px 10px 0; border-bottom:1px solid #f1f5f9; }
    .admin-table td { padding:10px 12px 10px 0; border-bottom:1px solid #f8fafc; color:#334155; }
    .role-badge { font-size:11px; padding:2px 8px; border-radius:20px; font-weight:600; }
    .role-doctor { background:#dbeafe; color:#1d4ed8; }
    .role-pharmacist { background:#fef3c7; color:#b45309; }
    .role-reception { background:#dcfce7; color:#15803d; }
    .table-card { background:white; border-radius:14px; padding:18px; border:1px solid #e0e9ff; }
    .table-card h3 { font-size:13px; color:#64748b; font-weight:500; margin-bottom:14px; }
    .add-btn { background:#3b82f6; color:white; border:none; padding:7px 16px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-block; float:right; margin-top:-4px; }
    .add-btn:hover { background:#2563eb; }
  </style>
</head>
<body>

<div class="sidebar">
  <div class="sidebar-logo">Care<span>Flow</span></div>
  <a href="admin_dashboard.php" class="active">📊 Dashboard</a>
  <a href="patients.php">👥 Patients</a>
  <a href="doctors.php">🩺 Doctors</a>
  <a href="pharmacist.php">💊 Pharmacy</a>
  <a href="receptionist.php">🏥 Reception</a>
  <div class="spacer"></div>
  <a href="../auth/logout.php">🚪 Logout</a>
</div>

<div class="main">
  <div class="page-header">
    <h1>Admin Dashboard</h1>
    <div class="date"><?= date('l, d M Y') ?></div>
  </div>

  <div class="kpi-grid">
    <div class="kpi"><div class="kpi-icon">👥</div><div class="kpi-num"><?= $p ?></div><div class="kpi-lbl">Total Patients</div></div>
    <div class="kpi"><div class="kpi-icon">🩺</div><div class="kpi-num"><?= $d ?></div><div class="kpi-lbl">Doctors</div></div>
    <div class="kpi"><div class="kpi-icon">💊</div><div class="kpi-num"><?= $ph ?></div><div class="kpi-lbl">Pharmacists</div></div>
    <div class="kpi"><div class="kpi-icon">🏥</div><div class="kpi-num"><?= $r ?></div><div class="kpi-lbl">Receptionists</div></div>
  </div>

  <div class="charts-row">
    <div class="chart-card">
      <h3>Consultations — Last 7 Days</h3>
      <canvas id="lineChart" height="120"></canvas>
    </div>
    <div class="chart-card">
      <h3>Staff Distribution</h3>
      <canvas id="pieChart" height="120"></canvas>
    </div>
  </div>

  <div class="table-card">
    <h3>Recent Staff Members <a href="add_staff.php" class="add-btn">+ Add Staff</a></h3>
    <table class="admin-table">
      <thead><tr><th>ID</th><th>Name</th><th>Role</th><th>Department</th><th>Status</th></tr></thead>
      <tbody>
      <?php while($s = mysqli_fetch_assoc($recent_staff)): ?>
        <tr>
          <td><?= $s['staff_id'] ?></td>
          <td><?= htmlspecialchars($s['name']) ?></td>
          <td><span class="role-badge role-<?= $s['role'] ?>"><?= ucfirst($s['role']) ?></span></td>
          <td><?= htmlspecialchars($s['department']) ?></td>
          <td style="color:#3b82f6">Active</td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
new Chart(document.getElementById('lineChart'), {
  type: 'line',
  data: {
    labels: <?= json_encode($days) ?>,
    datasets: [{
      data: <?= json_encode($counts) ?>,
      borderColor: '#3b82f6',
      backgroundColor: 'rgba(59,130,246,0.08)',
      tension: 0.4, fill: true,
      pointBackgroundColor: '#3b82f6', pointRadius: 4
    }]
  },
  options: { plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,grid:{color:'#f1f5f9'}},x:{grid:{display:false}}}, responsive:true }
});

new Chart(document.getElementById('pieChart'), {
  type: 'doughnut',
  data: {
    labels: ['Doctors','Pharmacists','Reception'],
    datasets: [{
      data: [<?= $d ?>, <?= $ph ?>, <?= $r ?>],
      backgroundColor: ['#3b82f6','#f59e0b','#22c55e'],
      borderWidth: 0
    }]
  },
  options: { plugins:{legend:{position:'bottom',labels:{font:{size:12},padding:12}}}, cutout:'65%', responsive:true }
});
</script>
</body>
</html>
