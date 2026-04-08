<?php
include("../config/db.php");

// Only complete current patient
mysqli_query(
    $conn,
    "UPDATE tokens 
 SET status='Completed' 
 WHERE status='NowServing'"
);

// DO NOT call next → system paused

header("Location: doctor_dashboard.php");
?>