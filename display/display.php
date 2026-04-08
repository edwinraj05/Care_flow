<?php include("../config/db.php"); ?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
<title>Queue Display | CareFlow</title>
<link rel="stylesheet" href="../public/style.css">
<meta http-equiv="refresh" content="5">
<style>
body{display:flex;justify-content:center;align-items:center;min-height:100vh;background:#0f1e4a;flex-direction:column;gap:30px}
.display-logo{font-family:'DM Serif Display',serif;font-size:28px;color:white}
.display-logo span{color:#60a5fa}
.display-card{background:rgba(255,255,255,0.08);border-radius:20px;padding:40px 60px;text-align:center;color:white;min-width:320px;border:1px solid rgba(255,255,255,0.15)}
.display-label{font-size:14px;opacity:.7;margin-bottom:10px}
.display-token{font-family:'DM Serif Display',serif;font-size:80px;line-height:1;color:#60a5fa}
.display-waiting{font-size:18px;opacity:.8;margin-top:10px}
.waiting-grid{display:flex;gap:14px;flex-wrap:wrap;justify-content:center}
.wait-item{background:rgba(255,255,255,0.06);border-radius:12px;padding:14px 20px;text-align:center;border:1px solid rgba(255,255,255,0.1)}
.wait-token{font-family:'DM Serif Display',serif;font-size:24px;color:white}
.wait-lbl{font-size:11px;opacity:.6;margin-top:4px}
</style></head><body>
<div class="display-logo">Care<span>Flow</span> · Queue Display</div>
<?php
$now=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM tokens WHERE status='NowServing' LIMIT 1"));
$waiting=mysqli_query($conn,"SELECT * FROM tokens WHERE status='Waiting' ORDER BY created_at ASC LIMIT 6");
?>
<div class="display-card">
  <div class="display-label">Now Serving</div>
  <div class="display-token"><?= $now ? $now['token_id'] : '—' ?></div>
  <?php if($now): ?><div class="display-waiting">Please proceed to the doctor's room</div><?php endif; ?>
</div>
<div class="waiting-grid">
<?php $i=1; while($row=mysqli_fetch_assoc($waiting)): ?>
<div class="wait-item"><div class="wait-token"><?=$row['token_id']?></div><div class="wait-lbl">Position #<?=$i?></div></div>
<?php $i++; endwhile; ?>
</div>
</body></html>
