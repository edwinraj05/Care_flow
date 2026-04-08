<?php
include("../config/db.php");

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM staff WHERE staff_id='$id'");

header("Location: " . $_SERVER['HTTP_REFERER']);
?>