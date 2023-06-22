<?php
    session_start();
    require_once './connect.php';
    $full_name = $_POST['full_name'];
    $login = $_POST['login'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    require_once 'login_check.php';
    require_once 'email_check.php';

    if ($password === $confirm) {
        $rndm = rand(); 

        $password = hash("sha256",$password.$rndm);
        
        $insertSql = "INSERT INTO users (id, name, login, email, password, status, salt) VALUES (NULL, '$full_name', '$login', '$email', '$password', 'user', '$rndm')";
        $conn->query($insertSql);

        $_SESSION['message'] = 'успешная регистрация';
        
        header('Location:./auth.php');

    }
    else {
        $_SESSION['message'] = 'ошибка при вводе пароля';
        header('Location:./reg.php');
        
    }

?>
