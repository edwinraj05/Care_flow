<?php
session_start();
include("../config/db.php");

$doctor_id = $_SESSION['user'];

$diagnosis = $_POST['diagnosis'];
$medicines = $_POST['medicines'];

// Get current consulting token
$current = mysqli_query(
    $conn,
    "SELECT * FROM tokens 
 WHERE status='NowServing' 
 LIMIT 1"
);

if (mysqli_num_rows($current) == 0) {
    die("No active patient");
}

$row = mysqli_fetch_assoc($current);

$token_id = $row['token_id'];
$patient_id = $row['patient_id'];

// Insert Prescription
mysqli_query(
    $conn,
    "INSERT INTO prescriptions
(token_id, patient_id, doctor_id, diagnosis, medicines)
VALUES
('$token_id','$patient_id','$doctor_id','$diagnosis','$medicines')"
);

// Insert Medical History
mysqli_query(
    $conn,
    "INSERT INTO medical_history
(patient_id, doctor_id, diagnosis, medicines)
VALUES
('$patient_id','$doctor_id','$diagnosis','$medicines')"
);

// Insert Pharmacy Order
mysqli_query(
    $conn,
    "INSERT INTO pharmacy_orders
(token_id, patient_id, medicines)
VALUES
('$token_id','$patient_id','$medicines')"
);

header("Location: doctor_dashboard.php");
?>