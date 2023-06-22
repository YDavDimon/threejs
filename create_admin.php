<?php
session_start();
require_once "./connect.php";

// Проверка наличия администратора
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Администратор уже существует
    
} else {
    $adminEmail = "admin@mail.ru";
    $rndm = rand(); 
    $adminPassword = "admin";
    $adminPassword = hash("sha256",$adminPassword.$rndm);
    $adminName = "admin";
    $adminLogin = "admin";
    $adminStatus = "admin";


    $insertSql = "INSERT INTO users (id, name, login, email, password, status, salt) VALUES (NULL, '$adminName', '$adminLogin', '$adminEmail','$adminPassword', '$adminStatus', '$rndm')";
    $conn->query($insertSql);
}

// Закрытие соединения с базой данных
$conn->close();
?>
