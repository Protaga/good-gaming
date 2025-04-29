<?php
require '../mysql_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request";
    exit();
}

if (!isset($_COOKIE['id']) || !isset($_POST['gameName'])) {
    echo "Authentication error or game not specified";
    exit();
}

$userId = (int)$_COOKIE['id'];
$rating = (int)$_POST['rating'];
$text = trim($_POST['commentText']);
$gameName = trim($_POST['gameName']);

// Get the game ID
$stmt = $pdo->prepare("SELECT id FROM games WHERE name = ?");
$stmt->execute([$gameName]);
$gameId = $stmt->fetchColumn();

if (!$gameId) {
    echo "Game not found";
    exit();
}

// Check if user has already commented
$stmt = $pdo->prepare("SELECT id FROM reviews WHERE user = ? AND game = ?");
$stmt->execute([$userId, $gameId]);
if ($stmt->fetch()) {
    echo "You have already left a comment";
    exit();
}

// Add new comment
$stmt = $pdo->prepare("INSERT INTO reviews (rating, user, game, text) VALUES (?, ?, ?, ?)");
$stmt->execute([$rating, $userId, $gameId, $text]);

// Update the average rating
$stmt = $pdo->prepare("SELECT AVG(rating) FROM reviews WHERE game = ?");
$stmt->execute([$gameId]);
$newRating = round($stmt->fetchColumn(), 1);

echo "Ready";
?>
