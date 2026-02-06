<?php
session_start();
$connection = mysqli_connect("127.0.0.1", "root", "", "school_registration", 3309);

if(isset($_POST['update_pass'])) {
    $admin_user = $_SESSION['username'];
    $new_password = mysqli_real_escape_string($connection, $_POST['new_pass']);
    
    // Update Query
    $query = "UPDATE admin_users SET password='$new_password' WHERE username='$admin_user'";
    
    if(mysqli_query($connection, $query)) {
        echo "<script>alert('Password updated successfully!'); window.location.href='admin.php?page=settings';</script>";
    } else {
        echo "Error updating record: " . mysqli_error($connection);
    }
}
?>