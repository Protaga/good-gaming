<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        $title = isset($_COOKIE['login']) && $_COOKIE['login'] != '' ? 'Profile' : 'Authorization';
        require 'blocks/head.php'; 
    ?>
</head>

<body>
    <?php require 'blocks/header.php'; ?>

    <main class="d-flex align-items-start justify-content-center min-vh-100">
        <div class="container d-flex align-items-start">
                <div class="col-md-3">
                
                </div>

            <div class="col-md-4 col-sm-6 col-10">
                <?php if (!isset($_COOKIE['login']) || $_COOKIE['login'] == ''): ?>
                    <h4 class="mb-4 text-center">Authorization form</h4>
                    <form class="text-center">
                        <div class="mb-3">
                            <label for="login" class="form-label">Login</label>
                            <input type="text" name="login" id="login" class="form-control mx-auto" maxlength="50" style="max-width: 300px;" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control mx-auto" maxlength="16" style="max-width: 300px;" required>
                        </div>

                        <div class="alert alert-danger mt-2 d-none" id="errorBlock" style="max-width: 300px; margin: 0 auto;"></div>

                        <button type="button" id="auth_user" class="btn btn-success mt-3" style="width: 300px;">Sign in</button>
                    </form> 

                <?php else: ?>
                    <h4 class="mb-4">Profile</h4>
                    <div class="d-flex align-items-start">
                    <?php 
                        $avatar = htmlspecialchars($_COOKIE['avatar']); 
                    ?>
                    <img src="<?= $avatar ?>" alt="Avatar" class="rounded-circle mb-3 me-5" style="width: 150px; height: 150px; object-fit: cover;">

                        <div>
                            <p><b>Login:</b> <?= htmlspecialchars($_COOKIE['login']) ?></p>

                            <?php if (isset($_COOKIE['email'])): ?>
                                <p><b>Email:</b> <?= htmlspecialchars($_COOKIE['email']) ?></p>
                            <?php endif; ?>

                            <?php if (isset($_COOKIE['date_reg'])): ?>
                                <?php 
                                    $timestamp = strtotime($_COOKIE['date_reg']); 
                                    $formattedDate = date('d F Y', $timestamp);   
                                ?>
                                <p><b>Registered:</b> <?= htmlspecialchars($formattedDate) ?></p>
                            <?php endif; ?>

                            <form id="updateProfileForm" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="new_login" class="form-label mt-3">New Login</label>
                                    <input type="text" name="new_login" id="new_login" class="form-control" maxlength="50">
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" name="new_password" id="new_password" class="form-control" maxlength="16">
                                </div>

                                <div class="mb-3">
                                    <label for="new_avatar" class="form-label">Upload New Avatar</label>
                                    <input type="file" name="new_avatar" id="new_avatar" class="form-control">
                                </div>

                                <div id="errorBlock" class="alert alert-danger mt-3 d-none" role="alert"></div>

                                <button type="button" id="updateProfileBtn" class="btn btn-success mt-3 mb-5" style="width: 100px;">Update</button>
                            </form>

                            <button type="button" id="exit_btn" class="btn btn-danger mt-3" style="width: 100px;">Sign out</button>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <?php require 'blocks/aside.php'; ?>
        </div>
    </main>

    <?php require 'blocks/footer.php'; ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        $('#auth_user').click(function () {
            var login = $('#login').val();
            var password = $('#password').val();

            $.ajax({
                url: 'ajax/auth.php',
                type: 'POST',
                cache: false,
                data: {'login': login, 'password': password},
                dataType: 'html',
                success: function(data) {
                    if(data == 'Ready') {
                        $('#auth_user').text('Everything is ready');
                        $('#errorBlock').hide();
                        document.location.reload(true);
                    } else {
                        $('#errorBlock').removeClass('d-none').show();
                        $('#errorBlock').text(data);
                    }
                }
            });
        });

        $('#exit_btn').click(function () {
            $.ajax({
                url: 'ajax/exit.php',
                type: 'POST',
                cache: false,
                data: {},
                dataType: 'html',
                success: function(data) {
                    document.location.reload(true);
                }
            });
        });

        $('#updateProfileBtn').click(function () {
            var formData = new FormData($('#updateProfileForm')[0]);

            $.ajax({
                url: 'ajax/update_profile.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data == 'Success') {
                        $('#errorBlock').hide();
                        document.location.reload(true);  
                    } else {
                        $('#errorBlock').removeClass('d-none').show();
                        $('#errorBlock').text(data);
                    }
                }
            });
        });
    </script>
</body>
</html>
