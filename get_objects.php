<?php
session_start();
require_once "./connect.php";

    $user_id = $_SESSION['user']['id'];

    $sql = "SELECT object_id, real_current_location, current_location FROM location WHERE user_id = '$user_id'";
    $result = $conn->query($sql);

    if ($result) {
        // Обработка полученных данных
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                'object_id' => $row['object_id'],
                'real_current_location' => $row['real_current_location'],
                'current_location' => $row['current_location']
            );
        }
    
        // Возвращаем данные в формате JSON
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        // Обработка ошибки выполнения запроса
        echo json_encode(['success' => false, 'message' => 'Ошибка выполнения запроса']);
    }