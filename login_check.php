<?php
session_start();
require_once './connect.php';



$sql = "SELECT * FROM `users` WHERE `login` = '$login'";
$log = $conn->query($sql);

if(mysqli_num_rows($log) > 0){
    $_SESSION['message'] = 'Логин занят';
    header('Location:./reg.php');
}
?>
