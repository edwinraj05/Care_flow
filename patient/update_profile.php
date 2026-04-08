<?php
session_start();
include("../config/db.php");

$pid = $_SESSION['user'];

$gender = $_POST['gender'];
$blood = $_POST['blood_group'];
$address = $_POST['address'];
$emergency = $_POST['emergency_contact'];

// IMAGE UPLOAD
$image_name = "";

if ($_FILES['profile_image']['name'] != "") {

    $image_name = time() . "_" . $_FILES['profile_image']['name'];
    $target = "../uploads/" . $image_name;

    move_uploaded_file($_FILES['profile_image']['tmp_name'], $target);

    mysqli_query(
        $conn,
        "UPDATE patients SET 
    gender='$gender',
    blood_group='$blood',
    address='$address',
    emergency_contact='$emergency',
    profile_image='$image_name'
    WHERE patient_id='$pid'"
    );
} else {

    mysqli_query(
        $conn,
        "UPDATE patients SET 
    gender='$gender',
    blood_group='$blood',
    address='$address',
    emergency_contact='$emergency'
    WHERE patient_id='$pid'"
    );
}

header("Location: patient_dashboard.php");
?>