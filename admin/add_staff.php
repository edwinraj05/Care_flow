<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../staff/login.html"); exit;
}
include("../config/db.php");

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $role  = mysqli_real_escape_string($conn, $_POST['role']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $dept  = mysqli_real_escape_string($conn, $_POST['department']);
    $pass  = mysqli_real_escape_string($conn, $_POST['password']);

    // Generate Staff ID based on role
    $prefix = ['doctor'=>'DID','reception'=>'RID','pharmacist'=>'PHID'];
    $pfx    = $prefix[$role] ?? 'SID';
    $cnt    = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM staff WHERE role='$role'"))['t'];
    $sid    = $pfx . '-' . str_pad($cnt + 1, 4, '0', STR_PAD_LEFT);

    // Check duplicate email
    $chk = mysqli_query($conn,"SELECT staff_id FROM staff WHERE email='$email'");
    if (mysqli_num_rows($chk) > 0) {
        $error = 'This email is already registered.';
    } else {
        $ok = mysqli_query($conn,
            "INSERT INTO staff (staff_id, name, role, email, password, department, is_active, created_at)
             VALUES ('$sid','$name','$role','$email','$pass','$dept',1,NOW())"
        );
        if ($ok) {
            $success = "Staff added! ID: <strong>$sid</strong> · Password: <strong>" . htmlspecialchars($_POST['password']) . "</strong>";
        } else {
            $error = 'Database error. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Staff | CareFlow Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'DM Sans', Arial, sans-serif; background: #f0f4ff; color: #0f172a; display: flex; min-height: 100vh; }
.sidebar { width: 210px; background: #0a1628; display: flex; flex-direction: column; padding: 20px 0; flex-shrink: 0; min-height: 100vh; position: fixed; top: 0; left: 0; bottom: 0; z-index: 100; }
.sidebar-logo { font-family: 'DM Serif Display', Georgia, serif; font-size: 20px; color: #fff; padding: 0 18px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 10px; text-decoration: none; display: block; }
.sidebar-logo span { color: #60a5fa; }
.sidebar a { padding: 11px 18px; font-size: 13px; color: rgba(255,255,255,0.6); display: flex; align-items: center; gap: 9px; text-decoration: none; }
.sidebar a:hover { background: rgba(255,255,255,0.06); color: #fff; }
.sidebar a.active { background: rgba(59,130,246,0.2); color: #fff; border-right: 3px solid #3b82f6; }
.sidebar .spacer { flex: 1; }
.main { margin-left: 210px; flex: 1; padding: 28px; }
.page-header { margin-bottom: 22px; }
.page-header h1 { font-family: 'DM Serif Display', Georgia, serif; font-size: 26px; color: #0f172a; font-weight: 400; }
.form-card { background: #fff; border-radius: 16px; padding: 28px; border: 1px solid #e0e9ff; max-width: 500px; }
.form-group { margin-bottom: 14px; }
.form-label { font-size: 12px; color: #64748b; font-weight: 600; display: block; margin-bottom: 5px; text-transform: uppercase; letter-spacing: .4px; }
.form-input { width: 100%; border: 1.5px solid #e0e9ff; border-radius: 10px; padding: 11px 12px; font-size: 14px; color: #0f172a; outline: none; font-family: 'DM Sans', Arial, sans-serif; background: #fff; }
.form-input:focus { border-color: #3b82f6; }
.submit-btn { background: #3b82f6; color: #fff; border: none; padding: 13px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; width: 100%; font-family: 'DM Sans', Arial, sans-serif; margin-top: 6px; }
.submit-btn:hover { background: #2563eb; }
.success-box { background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 10px; padding: 14px; font-size: 13px; color: #15803d; margin-bottom: 16px; }
.error-box { background: #fee2e2; border: 1.5px solid #fca5a5; border-radius: 10px; padding: 14px; font-size: 13px; color: #b91c1c; margin-bottom: 16px; }
</style>
</head>
<body>
<div class="sidebar">
  <a href="admin_dashboard.php" class="sidebar-logo">Care<span>Flow</span></a>
  <a href="admin_dashboard.php">📊 Dashboard</a>
  <a href="patients.php">👥 Patients</a>
  <a href="doctors.php">🩺 Doctors</a>
  <a href="pharmacist.php">💊 Pharmacy</a>
  <a href="receptionist.php">🏥 Reception</a>
  <div class="spacer"></div>
  <a href="add_staff.php" class="active">➕ Add Staff</a>
  <a href="../auth/logout.php">🚪 Logout</a>
</div>
<div class="main">
  <div class="page-header">
    <h1>Add New Staff</h1>
    <p style="font-size:13px;color:#64748b;margin-top:4px">Staff ID will be auto-generated based on role</p>
  </div>
  <div class="form-card">
    <?php if ($success): ?>
    <div class="success-box">✅ <?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="error-box">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label class="form-label">Full Name</label>
        <input class="form-input" name="name" placeholder="e.g. Dr. Priya Sharma" required>
      </div>
      <div class="form-group">
        <label class="form-label">Role</label>
        <select class="form-input" name="role" required>
          <option value="">Select role</option>
          <option value="doctor">Doctor</option>
          <option value="pharmacist">Pharmacist</option>
          <option value="reception">Receptionist</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Department</label>
        <input class="form-input" name="department" placeholder="e.g. General Medicine">
      </div>
      <div class="form-group">
        <label class="form-label">Email</label>
        <input class="form-input" type="email" name="email" placeholder="staff@careflow.com" required>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input class="form-input" type="text" name="password" placeholder="Set a password" required>
      </div>
      <button type="submit" class="submit-btn">Add Staff Member →</button>
    </form>
  </div>
</div>
</body>
</html>
