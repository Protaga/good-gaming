<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        $title = 'GG - Library';
        require 'blocks/head.php'; 
    ?>
</head>

<body>
    <?php require 'blocks/header.php'; ?>

    <main class="d-flex align-items-start justify-content-center min-vh-100">
        <div class="container d-flex align-items-start">
            <?php require 'blocks/genres.php'; ?>

            <div class="col-md-12 col-sm-12 col-12" style="max-width: 750px;">
                <h4 class="mb-5">My Library</h4>

                <div class="row flex-wrap">
                    <?php 
                        require_once 'mysql_connect.php'; 

                        // Перевіряємо, чи користувач авторизований через кукі
                        if (isset($_COOKIE['id']) && isset($_COOKIE['role'])) {
                            $userId = (int)$_COOKIE['id'];
                            $userRole = strtolower($_COOKIE['role']);

                            if ($userRole === 'dev') {
                                // Якщо користувач — розробник, виводимо ігри, які він розробив
                                $stmt = $pdo->prepare("SELECT name, img_path FROM games WHERE developer = ?");
                                $stmt->execute([$userId]);
                            } else {
                                // Якщо користувач не розробник — виводимо тільки куплені ігри
                                $stmt = $pdo->prepare("
                                    SELECT g.name, g.img_path
                                    FROM games g
                                    INNER JOIN purchases p ON g.id = p.game
                                    WHERE p.user = ?
                                ");
                                $stmt->execute([$userId]);
                            }

                            $games = $stmt->fetchAll();

                            if (count($games) > 0) {
                                foreach ($games as $game) {
                                    $game_name = htmlspecialchars($game['name']);
                                    $url_name = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $game_name), '-')); 

                                    echo '
                                    <div class="col-lg-4 col-md-4 col-sm-6 mb-4 me-5 text-center game-item" style="width: 200px;">
                                        <a href="games/' . $url_name . '.php" class="text-decoration-none text-dark">
                                            <img src="' . htmlspecialchars($game['img_path']) . '" alt="' . $game_name . '" 
                                                style="width: 100%; height: 300px; object-fit: cover; border-radius: 8px;">
                                            <h5 class="mt-2">' . $game_name . '</h5>
                                        </a>
                                    </div>';
                                }
                            } else {
                                // Якщо у користувача немає ігор
                                echo '
                                <div class="col-12 text-center mt-5">
                                    <h3 class="text-muted">You don\'t have any games purchased yet 😢</h3>
                                    <p><a href="/index.php" class="btn btn-primary mt-3">Перейти до магазину</a></p>
                                </div>';
                            }
                        } else {
                            // Якщо користувач взагалі не авторизований (немає кукі)
                            echo '
                            <div class="col-12 text-center mt-5">
                                <h3 class="text-muted">Please log in to see your library 🎮</h3>
                                <p><a href="/auth.php" class="btn btn-primary mt-3">Log in</a></p>
                            </div>';
                        }
                    ?>
                </div>
            </div>

            <aside class="ml-3">
                <div class="p-3 mb-3 bg-warning rounded text-center" style="max-width: 250px;">
                    <h4><b>Facts</b></h4>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                    <img class="img-thumbnail mb-3" src="./img/icon.jpg" alt="" style="width: 100%; height: auto;">
                    <img class="img-thumbnail mb-3" src="./img/icon.jpg" alt="" style="width: 100%; height: auto;">
                </div>
            </aside>
        </div>
    </main>

    <?php require 'blocks/footer.php'; ?>
</body>

</html>
