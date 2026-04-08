<?php
session_start();
include("../config/db.php");

<<<<<<< HEAD
// Prevent direct access
if (!isset($_POST['userid'])) {
    header("Location: ../public/login.html");
    exit();
}

$id = $_POST['userid'];
$pass = $_POST['password'];
$role = $_POST['role'];


// ================= PATIENT LOGIN =================
if ($role == "patient") {

    $q = "SELECT * FROM patients
          WHERE (patient_id='$id' OR email='$id')
          AND password='$pass'
          AND is_active=1";

    $res = mysqli_query($conn, $q);

    if (mysqli_num_rows($res) == 1) {

        $row = mysqli_fetch_assoc($res);

        // 🔥 IMPORTANT FIX → store REAL patient_id in session
        $_SESSION['user'] = $row['patient_id'];
        $_SESSION['role'] = "patient";

        header("Location: ../patient/patient_dashboard.php");
        exit();
    }
}


// ================= STAFF LOGIN =================
else {

    $q = "SELECT * FROM staff
          WHERE (staff_id='$id' OR email='$id')
          AND password='$pass'
          AND role='$role'
          AND is_active=1";

    $res = mysqli_query($conn, $q);

    if (mysqli_num_rows($res) == 1) {

        $row = mysqli_fetch_assoc($res);

        $_SESSION['user'] = $row['staff_id'];
        $_SESSION['role'] = $row['role'];

        // ===== STAFF REDIRECT FIX =====

        if ($row['role'] == "doctor") {
            header("Location: ../doctor/doctor_dashboard.php");
        } elseif ($row['role'] == "pharmacist") {
            header("Location: ../pharmacy/pharmacy_dashboard.php");
        } elseif ($row['role'] == "reception") {
            header("Location: ../reception/reception_dashboard.php");
        } else {
            echo "Invalid staff role";
        }

        exit();

        exit();
    }
}


// ================= INVALID LOGIN =================
echo "<h3>Invalid Login Credentials</h3>";
echo "<a href='../public/login.html'>Try Again</a>";
?>
=======
if (!isset($_POST['userid'])) {
    header("Location: ../public/login.html"); exit();
}

$id   = mysqli_real_escape_string($conn, $_POST['userid']);
$pass = mysqli_real_escape_string($conn, $_POST['password']);
$role = isset($_POST['role']) ? $_POST['role'] : '';

// ── PATIENT ──
if ($role == "patient") {
    $res = mysqli_query($conn,
        "SELECT * FROM patients
         WHERE (patient_id='$id' OR email='$id')
         AND password='$pass' AND is_active=1");
    if (mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_assoc($res);
        $_SESSION['user'] = $row['patient_id'];
        $_SESSION['role'] = "patient";
        header("Location: ../patient/patient_dashboard.php"); exit();
    }
    header("Location: ../public/login.html?error=1"); exit();
}

// ── ADMIN ──
if ($role == "admin") {
    $res = mysqli_query($conn,
        "SELECT * FROM admin
         WHERE (admin_id='$id' OR email='$id')
         AND password='$pass' AND is_active=1");
    if (mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_assoc($res);
        $_SESSION['user'] = $row['admin_id'];
        $_SESSION['role'] = "admin";
        header("Location: ../admin/admin_dashboard.php"); exit();
    }
    header("Location: ../admin/login.php?error=1"); exit();
}

// ── STAFF (doctor / pharmacist / reception) ──
if (in_array($role, ['doctor','pharmacist','reception'])) {
    $res = mysqli_query($conn,
        "SELECT * FROM staff
         WHERE (staff_id='$id' OR email='$id')
         AND password='$pass' AND role='$role' AND is_active=1");
    if (mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_assoc($res);
        $_SESSION['user'] = $row['staff_id'];
        $_SESSION['role'] = $row['role'];
        if ($role == "doctor")      header("Location: ../doctor/doctor_dashboard.php");
        elseif ($role == "pharmacist") header("Location: ../pharmacy/pharmacy_dashboard.php");
        elseif ($role == "reception")  header("Location: ../reception/reception_dashboard.php");
        exit();
    }
    header("Location: ../staff/login.html?error=1"); exit();
}

// ── INVALID ROLE ──
header("Location: ../public/login.html?error=1"); exit();
?>
>>>>>>> 41036b6 (first commit)
