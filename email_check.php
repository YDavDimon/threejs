<?php
session_start();
require_once './connect.php';


$sql = "SELECT * FROM `users` WHERE `email` = '$email'";
$check_email = $conn->query($sql);

if(mysqli_num_rows($check_email)>0) {
    $_SESSION['message'] = 'Почта занята';
    header('Location:./reg.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['message'] = 'некорректный email';
    header('Location:./reg.php');
    
}
?>
