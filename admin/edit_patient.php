<?php
include("../config/db.php");

$id = $_GET['id'];

if (isset($_POST['update'])) {
    mysqli_query($conn, "
    UPDATE patients SET 
    name='$_POST[name]',
    phone='$_POST[phone]'
    WHERE patient_id='$id'");

    header("Location: patients.php");
}

$res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM patients WHERE patient_id='$id'"));
?>

<form method="POST">
    <input name="name" value="<?php echo $res['name']; ?>">
    <input name="phone" value="<?php echo $res['phone']; ?>">
    <button name="update">Update</button>
</form>