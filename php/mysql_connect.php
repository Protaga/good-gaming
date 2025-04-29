<?php
    $user = 'root';
    $pass = 'root';
    $db = 'gg';
    $host = 'localhost';

    $dsn = 'mysql:host='.$host.';dbname='.$db;
    $pdo = new PDO($dsn, $user, $pass);
?>