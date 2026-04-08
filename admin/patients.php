<?php
session_start();
if ($_SESSION['role'] != "admin") { header("Location: ../staff/login.html"); exit; }
include("../config/db.php");
$rows = mysqli_query($conn,"SELECT * FROM patients ORDER BY created_at DESC");
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Patients | CareFlow Admin</title>
<link rel="stylesheet" href="../public/style.css">
<style>
body{display:flex;min-height:100vh}.sidebar{width:200px;background:#0a1628;display:flex;flex-direction:column;padding:20px 0;flex-shrink:0;min-height:100vh;position:fixed;top:0;left:0;bottom:0}.sidebar-logo{font-family:'DM Serif Display',serif;font-size:20px;color:white;padding:0 18px 20px;border-bottom:1px solid rgba(255,255,255,0.1);margin-bottom:10px}.sidebar-logo span{color:#60a5fa}.sidebar a{padding:10px 18px;font-size:13px;color:rgba(255,255,255,0.6);cursor:pointer;display:flex;align-items:center;gap:8px;text-decoration:none}.sidebar a:hover{background:rgba(255,255,255,0.06);color:white}.sidebar a.active{background:rgba(59,130,246,0.2);color:white;border-right:2px solid #3b82f6}.sidebar .spacer{flex:1}.main{margin-left:200px;flex:1;padding:24px;background:#f0f4ff}.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}.page-header h1{font-family:'DM Serif Display',serif;font-size:26px;color:#0f172a}.table-card{background:white;border-radius:14px;padding:18px;border:1px solid #e0e9ff}.admin-table{width:100%;border-collapse:collapse;font-size:13px}.admin-table th{text-align:left;color:#94a3b8;font-weight:500;padding:0 12px 10px 0;border-bottom:1px solid #f1f5f9}.admin-table td{padding:10px 12px 10px 0;border-bottom:1px solid #f8fafc;color:#334155}.del-btn{background:#fee2e2;color:#b91c1c;border:none;padding:4px 10px;border-radius:6px;font-size:11px;cursor:pointer;text-decoration:none}
</style></head><body>
<div class="sidebar"><div class="sidebar-logo">Care<span>Flow</span></div>
<a href="admin_dashboard.php">📊 Dashboard</a><a href="patients.php" class="active">👥 Patients</a><a href="doctors.php">🩺 Doctors</a><a href="pharmacist.php">💊 Pharmacy</a><a href="receptionist.php">🏥 Reception</a><div class="spacer"></div><a href="../auth/logout.php">🚪 Logout</a></div>
<div class="main">
<div class="page-header"><h1>Patients</h1></div>
<div class="table-card">
<table class="admin-table"><thead><tr><th>Patient ID</th><th>Name</th><th>DOB</th><th>Phone</th><th>Email</th><th>Action</th></tr></thead><tbody>
<?php while($row=mysqli_fetch_assoc($rows)): ?>
<tr><td><?=$row['patient_id']?></td><td><?=htmlspecialchars($row['name'])?></td><td><?=$row['dob']?></td><td><?=htmlspecialchars($row['phone'])?></td><td><?=htmlspecialchars($row['email'])?></td>
<td><a href="delete_patient.php?id=<?=$row['patient_id']?>" class="del-btn" onclick="return confirm('Delete this patient?')">Delete</a></td></tr>
<?php endwhile; ?>
</tbody></table></div></div></body></html>
