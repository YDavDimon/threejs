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
    <title>Регистрация</title>
    <link rel="stylesheet" href="./css/reg.css">
    
</head>
<body>
    
<a href="./index.php" class = "back">Назад</a>

    <form action="./signup.php" method="post">
    
        <label>Полное имя</label>
        <input type="text" name="full_name" placeholder="Введите полное имя">
        <label>Логин</label>
        <input type="text" name="login" placeholder="Введите логин">
        <label>email</label>
        <input type="email" name="email" placeholder="Введите адрес почты">
        <label>Пароль</label>
        <input type="password" name="password" placeholder="Введите пароль">
        <label>Подтверждение пароля</label>
        <input type="password" name="confirm" placeholder="Подтвердите пароль">
        <input type="submit" value="Зарегистрироваться">
        <p>
           Есть аккаунт? <a href="./auth.php">Войти</a>
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
