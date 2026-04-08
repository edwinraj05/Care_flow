<?php
session_start();
<<<<<<< HEAD
if ($_SESSION['role'] != "admin") { header("Location: ../staff/login.html"); exit; }
include("../config/db.php");
=======
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../staff/login.html"); exit;
}
include("../config/db.php");
$rows = mysqli_query($conn, "SELECT * FROM staff WHERE role='doctor' ORDER BY created_at DESC");
$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM staff WHERE role='doctor'"))['t'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Doctors | CareFlow Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'DM Sans', Arial, sans-serif; background: #f0f4ff; color: #0f172a; display: flex; min-height: 100vh; }

/* SIDEBAR */
.sidebar { width: 210px; background: #0a1628; display: flex; flex-direction: column; padding: 20px 0; flex-shrink: 0; min-height: 100vh; position: fixed; top: 0; left: 0; bottom: 0; z-index: 100; }
.sidebar-logo { font-family: 'DM Serif Display', Georgia, serif; font-size: 20px; color: #fff; padding: 0 18px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 10px; text-decoration: none; display: block; }
.sidebar-logo span { color: #60a5fa; }
.sidebar a { padding: 11px 18px; font-size: 13px; color: rgba(255,255,255,0.6); display: flex; align-items: center; gap: 9px; text-decoration: none; transition: all .15s; }
.sidebar a:hover { background: rgba(255,255,255,0.06); color: #fff; }
.sidebar a.active { background: rgba(59,130,246,0.2); color: #fff; border-right: 3px solid #3b82f6; }
.sidebar .spacer { flex: 1; }

/* MAIN */
.main { margin-left: 210px; flex: 1; padding: 28px; background: #f0f4ff; min-height: 100vh; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; }
.page-header h1 { font-family: 'DM Serif Display', Georgia, serif; font-size: 26px; color: #0f172a; font-weight: 400; }

/* STATS */
.stat-pill { background: #dbeafe; color: #1d4ed8; font-size: 13px; font-weight: 600; padding: 5px 14px; border-radius: 20px; }

/* ADD BUTTON */
.add-btn { background: #3b82f6; color: #fff; border: none; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-family: 'DM Sans', Arial, sans-serif; }
.add-btn:hover { background: #2563eb; }

/* TABLE CARD */
.table-card { background: #fff; border-radius: 16px; padding: 20px; border: 1px solid #e0e9ff; }

/* STAFF GRID (card style) */
.staff-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 14px; }
.staff-card { background: #f8faff; border-radius: 14px; padding: 16px; border: 1px solid #e0e9ff; display: flex; flex-direction: column; gap: 10px; }
.staff-card-header { display: flex; align-items: center; gap: 12px; }
.staff-avatar { width: 44px; height: 44px; border-radius: 50%; background: #3b82f6; color: #fff; font-size: 15px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.staff-name { font-size: 15px; font-weight: 600; color: #0f172a; }
.staff-id { font-size: 11px; color: #94a3b8; margin-top: 1px; }
.staff-row { display: flex; justify-content: space-between; font-size: 12px; }
.staff-row .lbl { color: #94a3b8; }
.staff-row .val { color: #334155; font-weight: 500; }
.staff-card-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 8px; border-top: 1px solid #e0e9ff; }
.status-dot { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; color: #059669; font-weight: 500; }
.status-dot::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: #059669; display: inline-block; }
.del-btn { background: #fee2e2; color: #b91c1c; border: none; padding: 5px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; cursor: pointer; text-decoration: none; font-family: 'DM Sans', Arial, sans-serif; }
.del-btn:hover { background: #fecaca; }

.empty-state { text-align: center; padding: 60px 20px; color: #94a3b8; }
.empty-state div { font-size: 48px; margin-bottom: 12px; }
</style>
</head>
<body>
<div class="sidebar">
  <a href="admin_dashboard.php" class="sidebar-logo">Care<span>Flow</span></a>
  <a href="admin_dashboard.php">📊 Dashboard</a>
  <a href="patients.php">👥 Patients</a>
  <a href="doctors.php" class="active">🩺 Doctors</a>
  <a href="pharmacist.php">💊 Pharmacy</a>
  <a href="receptionist.php">🏥 Reception</a>
  <div class="spacer"></div>
  <a href="add_staff.php">➕ Add Staff</a>
  <a href="../auth/logout.php">🚪 Logout</a>
</div>

<div class="main">
  <div class="page-header">
    <div style="display:flex;align-items:center;gap:12px">
      <h1>Doctors</h1>
      <span class="stat-pill"><?= $total ?> total</span>
    </div>
    <a href="add_staff.php" class="add-btn">➕ Add Doctor</a>
  </div>

  <div class="table-card">
    <?php if ($total == 0): ?>
    <div class="empty-state"><div>🩺</div>No doctors added yet.</div>
    <?php else: ?>
    <div class="staff-grid">
      <?php while ($s = mysqli_fetch_assoc($rows)):
        $parts = explode(' ', $s['name']);
        $initials = strtoupper(substr($parts[0],0,1)) . (isset($parts[1]) ? strtoupper(substr($parts[1],0,1)) : '');
      ?>
      <div class="staff-card">
        <div class="staff-card-header">
          <div class="staff-avatar"><?= $initials ?></div>
          <div>
            <div class="staff-name"><?= htmlspecialchars($s['name']) ?></div>
            <div class="staff-id"><?= $s['staff_id'] ?></div>
          </div>
        </div>
        <div class="staff-row"><span class="lbl">Department</span><span class="val"><?= htmlspecialchars($s['department'] ?: '—') ?></span></div>
        <div class="staff-row"><span class="lbl">Email</span><span class="val"><?= htmlspecialchars($s['email']) ?></span></div>
        <div class="staff-row"><span class="lbl">Joined</span><span class="val"><?= date('d M Y', strtotime($s['created_at'])) ?></span></div>
        <div class="staff-card-footer">
          <span class="status-dot">Active</span>
          <a href="delete_staff.php?id=<?= $s['staff_id'] ?>" class="del-btn" onclick="return confirm('Delete <?= htmlspecialchars($s['name']) ?>?')">Delete</a>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
>>>>>>> 41036b6 (first commit)
