<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        $title = 'GG - Add game';
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
                <h4 class="mb-4 text-center">Adding form</h4>
                <form id="addGameForm" class="text-center">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control mx-auto" maxlength="50" style="max-width: 300px;" required>
                    </div>

                    <div class="mb-3">
                        <label for="cost" class="form-label">Cost</label>
                        <input type="number" name="cost" id="cost" class="form-control mx-auto" maxlength="100" style="max-width: 300px;" required>
                    </div>

                    <div class="mb-3">
                        <label for="genre" class="form-label">Genre</label>
                        <input type="text" name="genre" id="genre" class="form-control mx-auto" maxlength="100" style="max-width: 300px;" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control mx-auto" rows="4" maxlength="500" style="max-width: 300px;" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="date_create" class="form-label">Date of create</label>
                        <input type="date" name="date_create" id="date_create" class="form-control mx-auto" maxlength="100" style="max-width: 300px;" required>
                    </div>

                    <div class="mb-3">
                        <label for="icon" class="form-label">Icon</label>
                        <input type="file" name="icon" id="icon" class="form-control mx-auto" maxlength="100" style="max-width: 300px;" required>
                    </div>

                    <div class="mb-3">
                        <label for="download" class="form-label">File to download</label>
                        <input type="file" name="download" id="download" class="form-control mx-auto" maxlength="100" style="max-width: 300px;" required>
                    </div>

                    <div class="alert alert-danger mt-2 d-none" id="errorBlock" style="max-width: 300px; margin: 0 auto;"></div>

                    <button type="button" id="add_game" class="btn btn-success mt-3" style="width: 300px;">Add</button>
                </form> 
            </div>

            <?php require 'blocks/aside.php'; ?>
            
        </div>
    </main>

    <?php require 'blocks/footer.php'; ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        document.getElementById('cost').addEventListener('input', function(e) {
            var value = e.target.value;
            e.target.value = value.replace(/[^0-9\.]/g, ''); 
        });

        $('#add_game').click(function () {
            var formData = new FormData($('#addGameForm')[0]);

            $.ajax({
                url: 'ajax/add_game.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    if(data == 'Ready') {
                        $('#errorBlock').hide();
                        window.location.href = '/add_game.php';
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
