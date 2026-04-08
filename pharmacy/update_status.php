<?php
include("../config/db.php");

// Debug: show errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    echo "Missing Parameters";
    exit();
}

$order_id = intval($_GET['id']);
$new_status = $_GET['status'];

// Check order exists
$res = mysqli_query($conn, "SELECT status FROM pharmacy_orders WHERE order_id = $order_id");

if (!$res || mysqli_num_rows($res) == 0) {
    echo "Order Not Found";
    exit();
}

$row = mysqli_fetch_assoc($res);
$current_status = $row['status'];

// Allow only valid transitions
if ($new_status == "Ready" && $current_status == "Pending") {

    $update = mysqli_query(
        $conn,
        "UPDATE pharmacy_orders SET status='Ready' WHERE order_id=$order_id"
    );

} elseif ($new_status == "Collected" && $current_status == "Ready") {

    $update = mysqli_query(
        $conn,
        "UPDATE pharmacy_orders SET status='Collected' WHERE order_id=$order_id"
    );

} else {
    echo "Invalid Transition";
    exit();
}

// Check update success
if ($update) {
    echo "Success";
} else {
    echo "DB Update Failed";
}
?>