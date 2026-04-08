<?php
session_start();
if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    header("Location: admin_dashboard.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | CareFlow</title>
  <link rel="stylesheet" href="../public/style.css">
</head>
<body>
<div class="center">
  <div class="form-card">
    <div style="font-size:32px;margin-bottom:12px">🔐</div>
    <h2>Admin Login</h2>
    <p class="subtitle">CareFlow administration panel</p>

    <?php if (isset($_GET['error'])): ?>
    <div style="background:#fee2e2;color:#b91c1c;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:14px;">
      Invalid credentials. Please try again.
    </div>
    <?php endif; ?>

    <form action="../auth/login.php" method="POST">
      <input type="hidden" name="role" value="admin">
      <div class="form-group">
        <label class="form-label">Admin ID or Email</label>
        <input class="form-input" type="text" name="userid" placeholder="e.g. AID-0001" required>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input class="form-input" type="password" name="password" placeholder="Admin password" required>
      </div>
      <button type="submit" class="form-btn">Login →</button>
    </form>
    <p class="form-footer"><a href="../staff/login.html">← Staff Login</a></p>
  </div>
</div>
</body>
</html>
