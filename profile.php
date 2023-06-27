<?php session_start();
    if(!$_SESSION['user']) {
        header('Location:./auth.php');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>profile</title>
    <link rel="stylesheet" href="./css/profile.css">
</head>
<body>

<a href="./index.php" class = "back">Назад</a>

<div>
    Информация об аккаунте
    <br>
    <br>
    <?php 
    
    echo "логин: ".$_SESSION['user']['login']. "<br>";
    echo "Полное имя: ".$_SESSION['user']['full_name']. "<br>";
    echo "email: ".$_SESSION['user']['email']. "<br>";
    echo "status: ".$_SESSION['user']['status'];
    ?>
    <a class="logout" href="./accaunt_remove.php" onclick="return confirm('Вы точно хотите удалить аккаунт?')">Удалить аккаунт</a>
    <a href="./logout.php" class = "logout">Выйти</a>
    <?php
            if ($_SESSION['message']) {
                echo '<p class = "message">'.$_SESSION['message']. '</p>';     
            }
            
            unset($_SESSION['message']);            
        ?>
</div>
    
</body>
</html>
