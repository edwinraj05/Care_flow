<?php
session_start();
include("../config/db.php");
$orders = mysqli_query($conn,"SELECT o.*,p.name FROM pharmacy_orders o JOIN patients p ON o.patient_id=p.patient_id WHERE o.status!='Collected' OR DATE(o.created_at)=CURDATE() ORDER BY o.created_at DESC LIMIT 20");
$html = '';
while ($row = mysqli_fetch_assoc($orders)) {
    $statusClass = 'status-' . strtolower($row['status']);
    $meds = explode(',', $row['medicines']);
    $html .= '<div class="order-card">';
    $html .= '<div class="order-header">';
    $html .= '<span class="order-token">Token '.$row['token_id'].'</span>';
    $html .= '<span class="badge '.$statusClass.'">'.$row['status'].'</span>';
    $html .= '</div>';
    $html .= '<div class="order-patient">'.htmlspecialchars($row['name']).' · '.$row['patient_id'].'</div>';
    $html .= '<div class="order-meds">';
    foreach ($meds as $med) {
        $html .= '<span class="med-tag">'.htmlspecialchars(trim($med)).'</span>';
    }
    $html .= '</div>';
    $html .= '<div class="order-actions">';
    if ($row['status'] == 'Pending') {
        $html .= '<button class="action-btn btn-ready" onclick="updateStatus('.$row['order_id'].', \'Ready\')">Mark Ready</button>';
    }
    if ($row['status'] == 'Ready') {
        $html .= '<button class="action-btn btn-collected" onclick="updateStatus('.$row['order_id'].', \'Collected\')">Collected ✓</button>';
    }
    $html .= '</div></div>';
}
echo $html ?: '<div style="text-align:center;padding:40px;color:#94a3b8;font-size:14px">No pending orders 🎉</div>';
