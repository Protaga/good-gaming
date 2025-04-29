<?php
require '../mysql_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_game'])) {
    $gameId = (int)$_POST['game_id'];  // ID гри
    $userId = (int)$_COOKIE['id'];  // ID користувача, який здійснює покупку

    // Перевірка, чи цей користувач вже купив гру
    $stmt = $pdo->prepare("SELECT id FROM purchases WHERE user = ? AND game = ?");
    $stmt->execute([$userId, $gameId]);
    if ($stmt->fetch()) {
        echo "You have already purchased this game.";
        exit();
    }

    // Отримання ціни гри
    $stmt = $pdo->prepare("SELECT cost FROM games WHERE id = ?");
    $stmt->execute([$gameId]);
    $cost = $stmt->fetchColumn();

    $cost = number_format($cost, 2, '.', ''); // Форматуємо до 2 знаків після коми

    if (!is_numeric($cost)) {
        echo "Invalid cost value.";
        exit();
    }

    // Вставка нового запису в таблицю purchases
    $stmt = $pdo->prepare("INSERT INTO purchases (user, game, cost) VALUES (?, ?, ?)");
    if ($stmt->execute([$userId, $gameId, $cost])) {
        echo "success";  // Виведення "успішно" для AJAX
    } else {
        echo "Error processing purchase.";
    }
}
?>
