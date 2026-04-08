<?php
include("../config/db.php");

// 1️⃣ Complete current (if exists)
mysqli_query(
    $conn,
    "UPDATE tokens 
 SET status='Completed' 
 WHERE status='NowServing'"
);

// 2️⃣ Get next waiting
$q = mysqli_query(
    $conn,
    "SELECT * FROM tokens 
 WHERE status='Waiting' 
 ORDER BY created_at ASC 
 LIMIT 1"
);

$row = mysqli_fetch_assoc($q);

// 3️⃣ Make next NowServing
if ($row) {
    $id = $row['token_id'];

    mysqli_query(
        $conn,
        "UPDATE tokens 
     SET status='NowServing' 
     WHERE token_id='$id'"
    );
}

// 4️⃣ Redirect
header("Location: doctor_dashboard.php");
?>