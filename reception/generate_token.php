<?php
include("../config/db.php");

$pid = $_POST['patient_id'];

$r = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tokens");
$row = mysqli_fetch_assoc($r);

$num = $row['total'] + 1;
$token = "C1-" . str_pad($num, 3, "0", STR_PAD_LEFT);

mysqli_query(
    $conn,
    "INSERT INTO tokens(token_id,patient_id) VALUES('$token','$pid')"
);

echo "Token Generated: $token";
?>