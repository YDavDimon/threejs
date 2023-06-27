<?php
session_start();
require_once "./connect.php";

    $user_id = $_SESSION['user']['id'];



    $current_location = $_POST['current_location'];
    $real_current_location = $_POST['real_current_location'];
    $time = $_POST['time'];
    

    // Подготовка и выполнение запроса на вставку данных
    $sql = "INSERT INTO location (object_id, user_id, current_location, real_current_location, time) VALUES (NULL, $user_id, '$current_location', '$real_current_location', '$time')";
    $conn->query($sql);
    




?>
