<?php
session_start();
require_once "./connect.php";

    $user_id = $_SESSION['user']['id'];


    $object_id = $_POST['object_id'];
    $current_location = $_POST['current_location'];
    $real_current_location = $_POST['real_current_location'];
    $time = $_POST['time'];
    

   

    $sql = "UPDATE location SET current_location = '$current_location', real_current_location = '$real_current_location' WHERE object_id = '$object_id'";
    $conn->query($sql);
    




?>