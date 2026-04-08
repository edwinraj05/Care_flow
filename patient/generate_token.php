<?php
session_start();
include("../config/db.php");

$pid = $_SESSION['user'];

// Check if patient already has active token
$check = mysqli_query(
    $conn,
    "SELECT * FROM tokens 
 WHERE patient_id='$pid' 
 AND status IN ('Waiting','NowServing')"
);

if (mysqli_num_rows($check) > 0) {
    header("Location: patient_dashboard.php");
    exit();
}

// Generate new token
$r = mysqli_query($conn, "SELECT COUNT(*) as total FROM tokens");
$row = mysqli_fetch_assoc($r);

$num = $row['total'] + 1;
$token = "C1-" . str_pad($num, 3, "0", STR_PAD_LEFT);

// Insert token
mysqli_query(
    $conn,
    "INSERT INTO tokens(token_id, patient_id) 
 VALUES('$token','$pid')"
);

header("Location: patient_dashboard.php");
?>