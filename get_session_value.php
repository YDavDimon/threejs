<?php
session_start();

// Получение значения переменной сессии
$sessionValue = $_SESSION['user']['full_name'];

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => $sessionValue]);
?>
