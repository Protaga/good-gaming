<?php
    $login = trim(filter_var($_POST['login'], FILTER_SANITIZE_STRING));
    $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
    $password = trim(filter_var($_POST['password'], FILTER_SANITIZE_STRING));

    $error = '';
    if (empty($login)) {
        $error = 'Enter a login';
    } else if (empty($email)) {
        $error = 'Enter an email';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter the email in the correct format (mail@gmail.com f.e.)';
    } else if (empty($password)) {
        $error = 'Enter a password';
    } else if (strlen($password) < 8) {
        $error = 'Password must be between 8 and 16 characters';
    }

    require_once '../mysql_connect.php';

    $stmt = $pdo->prepare('SELECT 1 FROM users WHERE login = :login LIMIT 1');
    $stmt->execute(['login' => $login]);
    if ($stmt->fetch()) {
        $error = 'A user with this login already exists';
    }

    $stmt = $pdo->prepare('SELECT 1 FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        $error = 'A user with this email already exists';
    }

    if ($error != '') {
        echo $error;
        exit();
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    $sql = 'INSERT INTO users(login, email, pass) VALUES(?, ?, ?)';
    $query = $pdo->prepare($sql);
    $query->execute([$login, $email, $password]);

    echo 'Ready';
?>
