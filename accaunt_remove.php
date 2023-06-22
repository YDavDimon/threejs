<?php
session_start();
require_once './connect.php';

$id = $_SESSION['user']['id'];
$status = $_SESSION['user']['status'];
if ($status != 'admin') {

   

    
    $sql = "DELETE FROM location WHERE user_id='$id'";
    $conn->query($sql);

    $sql = "DELETE FROM users WHERE `id`='$id'";
    $conn->query($sql);

    
    require_once 'logout.php';
}
else {
    $_SESSION['message'] = 'невозможно удалить';
    header('Location:./profile.php');
}
