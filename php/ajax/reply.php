<?php
require '../mysql_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request";
    exit();
}

if (!isset($_COOKIE['id']) || !isset($_POST['commentId']) || !isset($_POST['replyText'])) {
    echo "Authentication error or missing data";
    exit();
}

$userId = (int)$_COOKIE['id'];
$commentId = (int)$_POST['commentId'];
$replyText = trim($_POST['replyText']);

// Перевірка, чи розробник має доступ до відповіді на цей коментар
$stmt = $pdo->prepare("SELECT games.developer FROM reviews
                       JOIN games ON reviews.game = games.id
                       WHERE reviews.id = ?");
$stmt->execute([$commentId]);
$developerId = $stmt->fetchColumn();

if ($developerId !== $userId) {
    echo "You are not authorized to reply to this comment";
    exit();
}

// Перевірка, чи вже є відповідь на коментар
$stmt = $pdo->prepare("SELECT id FROM replies WHERE comment_id = ?");
$stmt->execute([$commentId]);

if ($stmt->fetch()) {
    echo "You have already replied to this comment";
    exit();
}

$developerId = $userId;

// Додавання відповіді на коментар
$stmt = $pdo->prepare("INSERT INTO replies (comment_id, reply_text, developer_id) VALUES (?, ?, ?)");
$stmt->execute([$commentId, $replyText, $developerId]);

echo "Ready";
?>
