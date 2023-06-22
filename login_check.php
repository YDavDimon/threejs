<?php
session_start();
require_once './connect.php';



$sql = "SELECT * FROM `users` WHERE `login` = '$login'";
$login = $conn->query($sql);

if(mysqli_num_rows($login) > 0){
    $_SESSION['message'] = 'Логин занят';
    header('Location:./reg.php');
}
?>
