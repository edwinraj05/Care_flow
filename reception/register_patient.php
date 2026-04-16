<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "reception") {
    header("Location: ../staff/login.html"); exit;
}
include("../config/db.php");

$message      = "";
$message_type = "";
$generated_pid = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name  = trim($_POST['name']  ?? '');
    $dob   = trim($_POST['dob']   ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name && $dob && $phone && $email) {

        // Check duplicate email
        $check = mysqli_query($conn, "SELECT * FROM patients WHERE email='".mysqli_real_escape_string($conn,$email)."'");

        if (mysqli_num_rows($check) > 0) {
            $message      = "Email already registered. Please use a different email.";
            $message_type = "error";
        } else {

            // Generate Patient ID
            $r   = mysqli_query($conn, "SELECT COUNT(*) AS total FROM patients");
            $row = mysqli_fetch_assoc($r);
            $num = $row['total'] + 1;
            $pid = "PID-" . str_pad($num, 4, "0", STR_PAD_LEFT);

            // Insert patient
            mysqli_query($conn,
                "INSERT INTO patients (patient_id, name, dob, email, phone, password, is_active, created_at)
                 VALUES (
                    '".mysqli_real_escape_string($conn,$pid)."',
                    '".mysqli_real_escape_string($conn,$name)."',
                    '".mysqli_real_escape_string($conn,$dob)."',
                    '".mysqli_real_escape_string($conn,$email)."',
                    '".mysqli_real_escape_string($conn,$phone)."',
                    '1234', 1, NOW()
                 )"
            );

            $generated_pid = $pid;
            $message       = "Patient registered successfully!";
            $message_type  = "success";
        }

    } else {
        $message      = "All fields are required.";
        $message_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register Patient | CareFlow</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'DM Sans', Arial, sans-serif; background: #f0f4ff; color: #0f172a; min-height: 100vh; }

/* TOPBAR */
.topbar { background: #0f1e4a; display: flex; align-items: center; justify-content: space-between; padding: 12px 20px; position: sticky; top: 0; z-index: 200; }
.topbar-logo { font-family: 'DM Serif Display', Georgia, serif; font-size: 20px; color: #fff; text-decoration: none; }
.topbar-logo span { color: #60a5fa; }
.back-btn { color: #94a3b8; font-size: 13px; border: 1px solid rgba(255,255,255,0.25); padding: 5px 12px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; gap: 5px; }
.back-btn:hover { background: rgba(255,255,255,0.08); color: #fff; }

/* PAGE */
.page { padding: 20px 16px 40px; max-width: 480px; margin: 0 auto; }

.page-header { margin-bottom: 20px; }
.page-header h2 { font-family: 'DM Serif Display', Georgia, serif; font-size: 24px; font-weight: 400; color: #0f172a; margin-bottom: 4px; }
.page-header p { font-size: 13px; color: #64748b; }

/* ALERT BANNERS */
.alert { border-radius: 12px; padding: 14px 16px; margin-bottom: 18px; font-size: 13px; font-weight: 500; display: flex; align-items: flex-start; gap: 10px; }
.alert-success { background: #eff6ff; border: 1.5px solid #bfdbfe; color: #1d4ed8; }
.alert-error   { background: #fff1f2; border: 1.5px solid #fecdd3; color: #be123c; }
.alert-icon { font-size: 18px; flex-shrink: 0; line-height: 1.2; }

/* SUCCESS CARD */
.success-card { background: linear-gradient(135deg,#0f1e4a,#1e40af); border-radius: 16px; padding: 28px 20px; color: #fff; text-align: center; margin-bottom: 18px; }
.success-card .check { font-size: 40px; margin-bottom: 10px; }
.success-card h3 { font-family: 'DM Serif Display', Georgia, serif; font-size: 20px; font-weight: 400; margin-bottom: 6px; }
.success-card p { font-size: 13px; opacity: .8; margin-bottom: 16px; }
.pid-badge { background: rgba(255,255,255,0.15); border: 1.5px solid rgba(255,255,255,0.3); border-radius: 10px; padding: 10px 20px; display: inline-block; }
.pid-badge .pid-label { font-size: 10px; opacity: .7; text-transform: uppercase; letter-spacing: .6px; margin-bottom: 4px; }
.pid-badge .pid-value { font-family: 'DM Serif Display', Georgia, serif; font-size: 28px; letter-spacing: 1px; }

/* FORM CARD */
.card { background: #fff; border-radius: 14px; padding: 20px; border: 1px solid #e0e9ff; margin-bottom: 14px; }
.card-title { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .6px; margin-bottom: 18px; }

.form-group { margin-bottom: 14px; }
.form-label { font-size: 12px; color: #64748b; font-weight: 600; display: block; margin-bottom: 6px; }
.form-label span { color: #ef4444; margin-left: 2px; }
.form-input { width: 100%; border: 1.5px solid #e0e9ff; border-radius: 10px; padding: 12px 14px; font-size: 14px; color: #0f172a; outline: none; font-family: 'DM Sans', Arial, sans-serif; background: #fff; transition: border-color .2s; }
.form-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.08); }
.form-input::placeholder { color: #cbd5e1; }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

.submit-btn { background: #3b82f6; color: #fff; border: none; padding: 14px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; width: 100%; font-family: 'DM Sans', Arial, sans-serif; margin-top: 6px; transition: background .2s; }
.submit-btn:hover { background: #2563eb; }

.register-another { background: #fff; color: #3b82f6; border: 1.5px solid #bfdbfe; padding: 13px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; width: 100%; font-family: 'DM Sans', Arial, sans-serif; text-align: center; text-decoration: none; display: block; margin-top: 10px; }
.register-another:hover { background: #eff6ff; }

.hint { font-size: 11px; color: #94a3b8; margin-top: 5px; }
</style>
</head>
<body>

<div class="topbar">
  <a href="reception.php" class="topbar-logo">Care<span>Flow</span></a>
  <a href="reception.php" class="back-btn">← Back</a>
</div>

<div class="page">

  <div class="page-header">
    <h2>Register Patient 🧑‍⚕️</h2>
    <p>Fill in the details to register a new walk-in patient</p>
  </div>

  <?php if ($message_type === 'success'): ?>

    <!-- SUCCESS STATE -->
    <div class="success-card">
      <div class="check">✅</div>
      <h3>Patient Registered!</h3>
      <p>The patient has been added to the system.</p>
      <div class="pid-badge">
        <div class="pid-label">Patient ID</div>
        <div class="pid-value"><?= htmlspecialchars($generated_pid) ?></div>
      </div>
    </div>

    <a href="reception_patient.php" class="register-another">+ Register Another Patient</a>
    <a href="reception.php" class="register-another" style="margin-top:8px;color:#64748b;border-color:#e0e9ff;">← Back to Reception</a>

  <?php else: ?>

    <?php if ($message_type === 'error'): ?>
      <div class="alert alert-error">
        <span class="alert-icon">⚠️</span>
        <span><?= htmlspecialchars($message) ?></span>
      </div>
    <?php endif; ?>

    <!-- REGISTER FORM -->
    <div class="card">
      <div class="card-title">Patient Details</div>
      <form method="POST">

        <div class="form-group">
          <label class="form-label">Full Name <span>*</span></label>
          <input class="form-input" type="text" name="name" placeholder="e.g. Rahul Sharma"
                 value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Date of Birth <span>*</span></label>
            <input class="form-input" type="date" name="dob"
                   value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Phone Number <span>*</span></label>
            <input class="form-input" type="text" name="phone" placeholder="+91 98765 43210"
                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Email Address <span>*</span></label>
          <input class="form-input" type="email" name="email" placeholder="patient@email.com"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
          <p class="hint">Used as the patient's login email</p>
        </div>

        <button type="submit" class="submit-btn">Register Patient →</button>

      </form>
    </div>

  <?php endif; ?>

</div>
</body>
</html>