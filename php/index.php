<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        $title = 'GG - Main';
        require 'blocks/head.php'; 
    ?>
</head>

<body>
    <?php require 'blocks/header.php'; ?>

    <main class="d-flex align-items-start justify-content-center min-vh-100">
        <div class="container d-flex align-items-start">
            <?php require 'blocks/genres.php'; ?>

            <div class="col-md-12 col-sm-12 col-12" style="max-width: 750px;">
                <h4 class="mb-5">Main</h4>

                <div class="row flex-wrap">
                <?php
                    require_once 'mysql_connect.php'; 

                    $stmt = $pdo->query("SELECT name, img_path FROM games");
                    while ($game = $stmt->fetch()) {
                        $game_name = htmlspecialchars($game['name']);
                        $url_name = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $game_name), '-')); // Заміна пробілів і непотрібних символів на дефіси

                        echo '
                        <div class="col-lg-4 col-md-4 col-sm-6 mb-4 me-5 text-center game-item" style="width: 200px;">
                            <a href="games/' . $url_name . '.php" class="text-decoration-none text-dark">
                                <img src="' . htmlspecialchars($game['img_path']) . '" alt="' . $game_name . '" 
                                    style="width: 100%; height: 300px; object-fit: cover; border-radius: 8px;">
                                <h5 class="mt-2">' . $game_name . '</h5>
                            </a>
                        </div>';
                    }
                ?>
                </div>
            </div>

            <aside class="">
                <div class="p-3 mb-3 bg-warning rounded text-center" style="max-width: 250px; margin-left: 25px;">
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