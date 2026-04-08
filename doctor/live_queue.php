<?php
session_start();
include("../config/db.php");

$current = mysqli_fetch_assoc(mysqli_query($conn,"SELECT t.*,p.name FROM tokens t JOIN patients p ON t.patient_id=p.patient_id WHERE t.status='NowServing' LIMIT 1"));
$waiting_list = mysqli_query($conn,"SELECT t.*,p.name FROM tokens t JOIN patients p ON t.patient_id=p.patient_id WHERE t.status='Waiting' ORDER BY t.created_at ASC LIMIT 5");

if (isset($_GET['current'])) {
    header('Content-Type: application/json');
    if ($current) {
        $n = $current['name'];
        $parts = explode(' ', $n);
        $initials = strtoupper(substr($parts[0],0,1)) . (isset($parts[1]) ? strtoupper(substr($parts[1],0,1)) : '');
        echo json_encode(['name'=>$n,'pid'=>$current['patient_id'],'token'=>$current['token_id'],'initials'=>$initials]);
    } else {
        echo json_encode(['name'=>null]);
    }
    exit;
}

if (isset($_GET['list'])) {
    $colors = ['#dbeafe|#1d4ed8','#e0e7ff|#4338ca','#f1f5f9|#64748b','#f1f5f9|#64748b','#f1f5f9|#64748b'];
    $i = 0;
    $html = '';
    while ($row = mysqli_fetch_assoc($waiting_list)) {
        list($bg,$fg) = explode('|', $colors[$i] ?? '#f1f5f9|#64748b');
        $html .= '<div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f1f5f9">';
        $html .= '<div style="width:36px;height:36px;border-radius:10px;background:'.$bg.';color:'.$fg.';display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0">'.$row['token_id'].'</div>';
        $html .= '<div style="flex:1"><div style="font-size:13px;font-weight:500;color:#0f172a">'.htmlspecialchars($row['name']).'</div><div style="font-size:11px;color:#94a3b8">Est. '.( ($i+1)*7).' min</div></div>';
        $html .= '<div style="font-size:12px;color:#94a3b8">#'.($i+1).'</div></div>';
        $i++;
    }
    echo $html ?: '<div style="text-align:center;padding:20px;color:#94a3b8;font-size:13px">No patients waiting 🎉</div>';
    exit;
}

// Default: now-serving card inner content
if ($current) {
    echo '<div class="ns-token">'.$current['token_id'].'</div>';
    echo '<div class="ns-name">'.htmlspecialchars($current['name']).'</div>';
    echo '<div class="ns-meta">'.htmlspecialchars($current['patient_id']).' · Currently consulting</div>';
    echo '<div class="ns-actions">';
    echo '<form action="call_next.php" method="POST" style="flex:1"><button type="submit" class="btn-call">⏭ Call Next</button></form>';
    echo '<button class="btn-complete" onclick="goToPrescribe()">✅ Complete &amp; Prescribe</button>';
    echo '</div>';
} else {
    echo '<div style="text-align:center;padding:10px;color:rgba(255,255,255,0.8);font-size:14px">No patient currently being served</div>';
    echo '<div class="ns-actions"><form action="call_next.php" method="POST" style="flex:1"><button type="submit" class="btn-call">⏭ Call Next Patient</button></form></div>';
}
