<?php
// Подключение к базе данных
$servername = "localhost";
$username = "admin";
$password = "2001";
$dbname = "locations";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);

}


?>