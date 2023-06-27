<?php
session_start();
require_once "./connect.php";

    $user_id = $_SESSION['user']['id'];



    $object_id = $_POST['object_id'];
   
    

    // Подготовка и выполнение запроса на вставку данных
    $sql = "DELETE FROM location WHERE object_id = '$object_id'";
    $conn->query($sql);
    




?>
