<?php
session_start();
include("../config/db.php");

$name  = mysqli_real_escape_string($conn, $_POST['name']);
$dob   = mysqli_real_escape_string($conn, $_POST['dob']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$phone = mysqli_real_escape_string($conn, $_POST['phone']);
$pass  = mysqli_real_escape_string($conn, $_POST['password']);

<<<<<<< HEAD
=======
// Check if email already exists
$check = mysqli_query($conn, "SELECT patient_id FROM patients WHERE email='$email'");
if (mysqli_num_rows($check) > 0) {
    header("Location: signup.html?error=email");
    exit();
}

>>>>>>> 41036b6 (first commit)
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
<<<<<<< HEAD
    // Auto-login and go straight to dashboard
=======
    // Auto-login and redirect straight to dashboard
>>>>>>> 41036b6 (first commit)
    $_SESSION['user'] = $pid;
    $_SESSION['role'] = "patient";
    header("Location: ../patient/patient_dashboard.php");
    exit();
} else {
<<<<<<< HEAD
    header("Location: signup.html?error=1");
=======
    header("Location: signup.html?error=db");
>>>>>>> 41036b6 (first commit)
    exit();
}
?>
