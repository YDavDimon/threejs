<?php
    session_start();
    require_once './connect.php';

    $login = $_POST['login'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE login = '$login'";
    $check = $conn->query($sql);


    if (mysqli_num_rows($check) > 0) {
        $user = mysqli_fetch_assoc($check);
        if ($user['password'] == hash("sha256", $password . $user['salt'])) {
        
            $_SESSION['user'] = [
                "id" => $user['id'],
                "full_name" => $user['name'],
                "email" => $user['email'],
                "status" => $user['status']
            ];
            

            header('Location:./profile.php');
        }
        
        else {
            $_SESSION['message'] = 'ошибка при вводе данных';
            header('Location:./auth.php');
        }
    }
    else {
        $_SESSION['message'] = 'ошибка при вводе данных';
        header('Location:./auth.php');
    }
    
?>
