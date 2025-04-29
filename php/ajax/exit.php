<?php 
    setcookie('login', "", time() - 3600*24*7, '/');
    setcookie('email', "", time() - 3600*24*7, '/');
    setcookie('avatar', "", time() - 3600*24*7, '/');
    setcookie('date_reg', "", time() - 3600*24*7, '/');
    setcookie('role', "", time() - 3600*24*7, '/');
    setcookie('id', "", time() - 3600*24*7, '/');

    unset($_COOKIE['login']);
    unset($_COOKIE['email']);
    unset($_COOKIE['avatar']);
    unset($_COOKIE['date_reg']);
    unset($_COOKIE['role']);
    unset($_COOKIE['id']);

    echo true;
?>
