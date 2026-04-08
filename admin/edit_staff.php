<?php
include("../config/db.php");

$id = $_GET['id'];

if (isset($_POST['update'])) {
    mysqli_query($conn, "
    UPDATE staff SET 
    name='$_POST[name]',
    email='$_POST[email]'
    WHERE staff_id='$id'");

    header("Location: doctors.php");
}

$res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM staff WHERE staff_id='$id'"));
?>

<form method="POST">
    <input name="name" value="<?php echo $res['name']; ?>">
    <input name="email" value="<?php echo $res['email']; ?>">
    <button name="update">Update</button>
</form>