<?php
session_start();
include("../config/db.php");

if (!isset($_POST['userid'])) {
    header("Location: ../public/login.html");
    exit();
}

$id = mysqli_real_escape_string($conn, $_POST['userid']);
$pass = mysqli_real_escape_string($conn, $_POST['password']);
$role = isset($_POST['role']) ? $_POST['role'] : '';

// ── PATIENT ──
if ($role == "patient") {
    $res = mysqli_query(
        $conn,
        "SELECT * FROM patients
         WHERE (patient_id='$id' OR email='$id')
         AND password='$pass' AND is_active=1"
    );
    if (mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_assoc($res);
        $_SESSION['user'] = $row['patient_id'];
        $_SESSION['role'] = "patient";
        header("Location: ../patient/patient_dashboard.php");
        exit();
    }
    header("Location: ../public/login.html?error=1");
    exit();
}

// ── ADMIN ──
if ($role == "admin") {
    $res = mysqli_query(
        $conn,
        "SELECT * FROM admin
         WHERE (admin_id='$id' OR email='$id')
         AND password='$pass' AND is_active=1"
    );
    if (mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_assoc($res);
        $_SESSION['user'] = $row['admin_id'];
        $_SESSION['role'] = "admin";
        header("Location: ../admin/admin_dashboard.php");
        exit();
    }
    header("Location: ../admin/login.php?error=1");
    exit();
}

// ── STAFF (doctor / pharmacist / reception) ──
if (in_array($role, ['doctor', 'pharmacist', 'reception'])) {
    $res = mysqli_query(
        $conn,
        "SELECT * FROM staff
         WHERE (staff_id='$id' OR email='$id')
         AND password='$pass' AND role='$role' AND is_active=1"
    );
    if (mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_assoc($res);
        $_SESSION['user'] = $row['staff_id'];
        $_SESSION['role'] = $row['role'];
        if ($role == "doctor")
            header("Location: ../doctor/doctor_dashboard.php");
        elseif ($role == "pharmacist")
            header("Location: ../pharmacy/pharmacy_dashboard.php");
        elseif ($role == "reception")
            header("Location: ../reception/reception_dashboard.php");
        exit();
    }
    header("Location: ../staff/login.html?error=1");
    exit();
}

// ── INVALID ROLE ──
header("Location: ../public/login.html?error=1");
exit();
?>