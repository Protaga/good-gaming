<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        $title = 'GG - Registration';
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
                <h4 class="mb-4 text-center">Registration form</h4>
                <form class="text-center">
                    <div class="mb-3">
                        <label for="login" class="form-label">Login</label>
                        <input type="text" name="login" id="login" class="form-control mx-auto" maxlength="50" style="max-width: 300px;" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control mx-auto" maxlength="100" style="max-width: 300px;" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password (8-16)</label>
                        <input type="password" name="password" id="password" class="form-control mx-auto" maxlength="16" style="max-width: 300px;" required>
                    </div>

                    <div class="alert alert-danger mt-2 d-none" id="errorBlock" style="max-width: 300px; margin: 0 auto;"></div>

                    <button type="button" id="reg_user" class="btn btn-success mt-3" style="width: 300px;">Sign up</button>
                </form> 
            </div>

            <?php require 'blocks/aside.php'; ?>
            
        </div>
    </main>

    <?php require 'blocks/footer.php'; ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        $('#reg_user').click(function () {
            var login = $('#login').val();
            var email = $('#email').val();
            var password = $('#password').val();

            $.ajax({
                url: 'ajax/reg.php',
                type: 'POST',
                cache: false,
                data: {'login': login, 'email': email, 'password': password},
                dataType: 'html',
                success: function(data) {
                    if(data == 'Ready') {
                        $('#errorBlock').hide();
                        window.location.href = '/auth.php';
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
