<?php
    $login = trim(filter_var($_POST['login'], FILTER_SANITIZE_STRING));
    $password = trim(filter_var($_POST['password'], FILTER_SANITIZE_STRING));

    $error = '';
    if (empty($login)) {
        $error = 'Enter a login';
    } else if (empty($password)) {
        $error = 'Enter a password';
    }

    if ($error != '') {
        echo $error;
        exit();
    }

    require_once '../mysql_connect.php';

    $sql = 'SELECT id, pass, email, avatar_path, date_reg, role FROM users WHERE `login` = :login';
    $query = $pdo->prepare($sql);
    $query->execute(['login' => $login]);
    $user = $query->fetch(PDO::FETCH_OBJ);

    if (!$user) {
        echo 'User not found';
        exit();
    }

    // Перевірка пароля через password_verify
    if (!password_verify($password, $user->pass)) {
        echo 'Incorrect password';
        exit();
    }

    // Авторизація успішна — ставимо куки
    setcookie('login', $login, time() + 3600*24*7, '/');
    setcookie('email', $user->email, time() + 3600*24*7, '/');
    setcookie('avatar', $user->avatar_path ?: 'img/icon.jpg', time() + 3600*24*7, '/');
    setcookie('date_reg', $user->date_reg, time() + 3600*24*7, '/');
    setcookie('role', $user->role, time() + 3600*24*7, '/');
    setcookie('id', $user->id, time() + 3600*24*7, '/');

    echo 'Ready';
?>
