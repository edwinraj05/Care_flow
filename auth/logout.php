<?php
session_start();
$role = $_SESSION['role'] ?? "";
session_unset();
session_destroy();
if ($role == "patient") {
    header("Location: ../public/login.html");
} else {
    header("Location: ../staff/login.html");
}
exit();
?>