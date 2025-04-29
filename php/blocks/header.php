<div class="navbar navbar-expand-lg navbar-light bg-primary py-3 mb-4">
    <div class="container-fluid">
        <a class="navbar-brand text-white font-weight-bold" href="/" style="margin-left: 2rem;">
            <span class="fs-4">Good gaming</span>
        </a>

        <div class="collapse navbar-collapse justify-content-center">
            <ul class="navbar-nav fs-5">
                <li class="nav-item">
                    <a class="nav-link text-white me-3" href="../index.php">Main</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white me-3" href="">News</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white me-3" href="">Bestsellers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white me-3" href="./contacts.php">Contacts</a>
                </li>
            </ul>
        </div>

        <div class="d-flex">
            <?php if (!isset($_COOKIE['login']) || $_COOKIE['login'] == ''): ?>
                <a class="btn btn-outline-light ms-3 me-3" href="../auth.php">Sign in</a>
                <a class="btn btn-outline-light me-2" href="../reg.php">Sign up</a>
            <?php elseif ($_COOKIE['role'] == 'user'): ?>
                <a class="btn btn-outline-light ms-3 me-3" href="../library.php">Library</a>
                <a class="btn btn-outline-light me-2" href="../auth.php">Profile</a>
            <?php elseif ($_COOKIE['role'] == 'dev'): ?>
                <a class="btn btn-outline-light ms-3 me-3" href="../add_game.php">Add game</a>
                <a class="btn btn-outline-light me-3" href="../library.php">My games</a>
                <a class="btn btn-outline-light me-3" href="../auth.php">Profile</a>
            <?php endif; ?>
        </div>
    </div>
</div>
