<?php
session_start();
include("../config/db.php");

$name  = mysqli_real_escape_string($conn, $_POST['name']);
$dob   = mysqli_real_escape_string($conn, $_POST['dob']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$phone = mysqli_real_escape_string($conn, $_POST['phone']);
$pass  = mysqli_real_escape_string($conn, $_POST['password']);

// Check if email already exists
$check = mysqli_query($conn, "SELECT patient_id FROM patients WHERE email='$email'");
if (mysqli_num_rows($check) > 0) {
    header("Location: signup.html?error=email");
    exit();
}

// Generate Patient ID
$r   = mysqli_query($conn, "SELECT COUNT(*) AS total FROM patients");
$row = mysqli_fetch_assoc($r);
$num = $row['total'] + 1;
$pid = "PID-" . str_pad($num, 4, "0", STR_PAD_LEFT);

// Insert patient
$ok = mysqli_query($conn,
    "INSERT INTO patients (patient_id, name, dob, email, phone, password, is_active, created_at)
     VALUES ('$pid','$name','$dob','$email','$phone','$pass',1,NOW())"
);

if ($ok) {
    // Auto-login and redirect straight to dashboard
    $_SESSION['user'] = $pid;
    $_SESSION['role'] = "patient";
    header("Location: ../patient/patient_dashboard.php");
    exit();
} else {
    header("Location: signup.html?error=db");
    exit();
}
?>
