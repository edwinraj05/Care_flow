<?php
include("../config/db.php");

$name = $_POST['name'];
$dob = $_POST['dob'];
$phone = $_POST['phone'];
$email = $_POST['email'];

$r = mysqli_query($conn, "SELECT COUNT(*) AS total FROM patients");
$row = mysqli_fetch_assoc($r);

$num = $row['total'] + 1;
$pid = "PID-" . str_pad($num, 4, "0", STR_PAD_LEFT);

mysqli_query(
    $conn,
    "INSERT INTO patients VALUES('$pid','$name','$dob','$email','$phone','1234',1,NOW())"
);

echo "Patient Registered. PID: $pid";
?>