<?php
    session_start();

    if($_SESSION['user']) {
        header('Location:./profile.php');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Авторризация</title>
    <link rel="stylesheet" href="./css/auth.css">
</head>
<body>
    
<a href="./index.php" class = "back">Назад</a>

    <form action="./signin.php" method="post">

        <label>Логин</label>
        <input type="text" name="login" placeholder="Введите логин">
        <label>Пароль</label>
        <input type="password" name="password" placeholder="Введите пароль">
        <input type="submit" value="Войти">
        <p>
        <a href="./reg.php"> Создать</a> аккаунт
        </p>
        
        <?php
            if ($_SESSION['message']) {
                echo '<p class = "message">'.$_SESSION['message']. '</p>';     
            }
            
            unset($_SESSION['message']);            
        ?>


    </form>

</body>
</html>
