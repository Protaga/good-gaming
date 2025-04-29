<?php
require_once 'mysql_connect.php';

$developers = [];
$genres = [];

// Тепер робимо JOIN на users, бо developer — це id юзера
$stmt = $pdo->query("
    SELECT DISTINCT u.id, u.login
    FROM games g
    JOIN users u ON g.developer = u.id
");

while ($row = $stmt->fetch()) {
    $developers[] = [
        'id' => $row['id'],
        'name' => htmlspecialchars($row['login'])
    ];
}

// Жанри — без змін
$stmt = $pdo->query("SELECT DISTINCT genre FROM games");
while ($row = $stmt->fetch()) {
    $genres[] = htmlspecialchars($row['genre']);
}

echo json_encode([
    'developers' => $developers,
    'genres' => $genres
]);
?>
