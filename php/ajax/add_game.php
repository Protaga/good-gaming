<?php
require '../mysql_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = '';

    if ($_COOKIE['role'] !== 'dev') {
        echo $error = "Access denied. You must be a developer to add a game.";
        exit();
    }

    $name = trim($_POST['name']);
    $cost = trim($_POST['cost']);
    $genre = trim($_POST['genre']);
    $description = trim($_POST['description']);
    $date_create = trim($_POST['date_create']);

    if (strtotime($date_create) > strtotime(date('Y-m-d'))) {
        echo $error = "The creation date cannot be in the future";
        exit();
    }

    if (empty($name) || $cost === '' || empty($genre) || empty($description) || empty($date_create)) {
        echo $error = "All fields must be filled";
        exit();
    }

    if (!isset($_COOKIE['id']) || empty($_COOKIE['id'])) {
        echo $error = "Developer is not authenticated";
        exit();
    }

    $developerId = (int)$_COOKIE['id'];

    $stmt = $pdo->prepare("SELECT id FROM games WHERE name = ?");
    $stmt->execute([$name]);
    if ($stmt->fetch()) {
        echo $error = "Game with this name already exists";
        exit();
    }

    $iconDir = '../uploads/icons/';
    $fileDir = '../uploads/files/';

    if (!is_dir($iconDir)) {
        mkdir($iconDir, 0777, true);
    }
    if (!is_dir($fileDir)) {
        mkdir($fileDir, 0777, true);
    }

    if (isset($_FILES['icon']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
        $iconName = time() . '_' . basename($_FILES['icon']['name']);
        $iconPath = $iconDir . $iconName;
        if (!move_uploaded_file($_FILES['icon']['tmp_name'], $iconPath)) {
            echo $error = "Error uploading icon";
            exit();
        }
    } else {
        echo $error = "Icon file is missing or corrupted";
        exit();
    }

    if (isset($_FILES['download']) && $_FILES['download']['error'] === UPLOAD_ERR_OK) {
        $fileName = time() . '_' . basename($_FILES['download']['name']);
        $gameFilePath = $fileDir . $fileName;
        if (!move_uploaded_file($_FILES['download']['tmp_name'], $gameFilePath)) {
            echo $error = "Error uploading game file";
            exit();
        }
    } else {
        echo $error = "Game file is missing or corrupted";
        exit();
    }

    $iconPath = str_replace('../', '', $iconPath);
    $gameFilePath = str_replace('../', '', $gameFilePath);

    $stmt = $pdo->prepare("INSERT INTO games (name, cost, genre, description, date_create, img_path, file_path, developer) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([$name, (float)$cost, $genre, $description, $date_create, $iconPath, $gameFilePath, $developerId]);

    echo 'Ready';

    if ($result) {
        // Отримуємо останній вставлений id гри
        $gameId = $pdo->lastInsertId();

        // Тепер можна використовувати $gameId для генерації профілю гри
        $stmt = $pdo->prepare("SELECT developer FROM games WHERE id = ?");
        $stmt->execute([$gameId]);
        $gameDeveloperId = $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT login FROM users WHERE id = ?");
        $stmt->execute([$gameDeveloperId]);
        $developerLogin = $stmt->fetchColumn();

        if (!$developerLogin) {
            echo "Developer not found";
            exit();
        }

        $gameProfilePath = "../games/" . str_replace(' ', '-', $name) . ".php";
        $gameFileUrl = '../uploads/files/' . htmlspecialchars($fileName);

        // Генерація контенту для профілю гри
        $gameProfileContent = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <?php 
                \$title = '" . htmlspecialchars($name) . "'; 
                \$gameId = " . $gameId . ";
                \$gameFileUrl = '" . $gameFileUrl . "';
                require '../blocks/head.php'; 
            ?>
        </head>

        <body>
            <?php require '../blocks/header.php'; ?>

            <main class='d-flex align-items-start justify-content-center min-vh-100'>
                <div class='container d-flex align-items-start'>
                    <div class='col-md-3'>
                
                    </div>

                    <div class='col-md-4 col-sm-6 col-10'>
                        <h4 class='mb-4'>Game - " . htmlspecialchars($name) . "</h4>
                        <img src='../" . htmlspecialchars($iconPath) . "' alt='" . htmlspecialchars($name) . " Icon' class='mb-3' style='width: 400px; object-fit: cover;'>

                        <p class='mt-4'><b>Cost:</b> $" . htmlspecialchars($cost) . "</p>
                        <p><b>Genre:</b> " . htmlspecialchars($genre) . "</p>
                        <p><b>Developer:</b> " . htmlspecialchars($developerLogin) . "</p> 
                        <p><b>Release Date:</b> " . htmlspecialchars($date_create) . "</p>

                        <?php 
                            \$gameDeveloperId = " . $gameDeveloperId . "; 
                            \$is_dev = isset(\$_COOKIE['role']) && \$_COOKIE['role'] === 'dev';
                            \$user_id = isset(\$_COOKIE['id']) ? (int)\$_COOKIE['id'] : null;
                            \$is_owner = (\$gameDeveloperId === \$user_id);
                            \$is_authenticated = isset(\$_COOKIE['id']) && !empty(\$_COOKIE['id']);
                        ?>

                        <div id='game_actions'>
                            <?php 
                            require '../mysql_connect.php';
                            if (\$is_dev && !\$is_owner) {
                                echo '<p>You are a developer and cannot buy or download this game.</p>';
                            } elseif (\$is_owner) {
                                echo '<a href=\"" . $gameFileUrl . "\" download class=\"btn btn-success mt-3\" style=\"width: 150px;\">Download</a>';
                            } elseif (\$is_authenticated) {
                                // Перевірка, чи користувач вже купив гру
                                \$stmt = \$pdo->prepare(\"SELECT id FROM purchases WHERE user = ? AND game = ?\");
                                \$stmt->execute([\$user_id, \$gameId]);
                                if (\$stmt->fetch()) {
                                    // Якщо користувач вже купив гру, показуємо кнопку 'Download'
                                    echo '<a href=\"' . \$gameFileUrl . '\" download class=\"btn btn-success mt-3\" style=\"width: 150px;\">Download</a>';
                                } else {
                                    // Якщо користувач ще не купив гру, показуємо кнопку 'Buy'
                                    echo '<button type=\"button\" id=\"buy_game\" class=\"btn btn-primary mt-3\" style=\"width: 150px;\">Buy</button>';
                                }
                            } else {
                                echo '<p>You need to log in to buy or download this game.</p>';
                            }
                            ?>
                        </div>
                        <p class='mt-5'><br><b>Description:</b> " . htmlspecialchars($description) . "</p>

                        <?php if (\$is_authenticated): ?>
                            <!-- Поле для коментарів -->
                            <?php require '../blocks/comments.php'; ?>
                        <?php else: ?>
                            <p>To comment, please log in first!</p>
                            <p><a href=\"/auth.php\" class=\"btn btn-primary mt-3\">Sign in</a></p>
                        <?php endif; ?>
                    </div>

                    <?php require '../blocks/aside.php'; ?>
                </div>
            </main>

            <?php require '../blocks/footer.php'; ?>
            <div id=\"gameId\" data-game-id=\"" . $gameId . "\"></div>
            <script src=\"/js/game.js\"></script>
        </body>
        </html>
        ";

        // Записуємо в файл
        file_put_contents($gameProfilePath, $gameProfileContent);
    } else {
        echo "Error saving game to database";
    }

} else {
    echo "Invalid request method";
}
?>
