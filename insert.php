<?php
session_start();
require_once "./connect.php";

$user_id = $_SESSION['user']['id'];



   
    $object_id = $_POST['id'];
    $current_position = $_POST['current_position'];
    $previous_position = $_POST['previous_position'];

    

    // Подготовка и выполнение запроса на вставку данных
    $sql = "INSERT INTO location (object_id, user_id, current_location, previous_location) VALUES (NULL, $user_id, '$current_position', '$previous_position')";
    $conn->query($sql);
    




?>
