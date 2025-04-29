<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['new_login']) || !empty($_POST['new_password']) || (isset($_FILES['new_avatar']) && $_FILES['new_avatar']['error'] === UPLOAD_ERR_OK)) {
        $newLogin = $_POST['new_login'] ?? null;
        $newPassword = $_POST['new_password'] ?? null;

        if ($newPassword && strlen($newPassword) < 8) {
            echo 'Password must be at least 8 characters long';
            exit();
        }

        if ($newPassword) {
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        if (isset($_FILES['new_avatar']) && $_FILES['new_avatar']['error'] === UPLOAD_ERR_OK) {
            $avatarPath = '../img/uploads/' . $_FILES['new_avatar']['name'];
            move_uploaded_file($_FILES['new_avatar']['tmp_name'], $avatarPath);
        } else {
            $avatarPath = null; 
        }

        require_once '../mysql_connect.php';

        $userId = $_COOKIE['id']; 

        if ($newLogin) {
            $query = "SELECT id FROM users WHERE login = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$newLogin]);
            $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingUser) {
                echo 'This login is already taken';
                exit();
            }
        }

        $updateFields = [];
        $params = [];

        if ($newLogin) {
            $updateFields[] = "login = ?";
            $params[] = $newLogin;
        }

        if ($newPassword) {
            $updateFields[] = "pass = ?";
            $params[] = $newPasswordHash;
        }

        if ($avatarPath) {
            $updateFields[] = "avatar_path = ?";
            $params[] = $avatarPath;
        }

        $sql = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $params[] = $userId;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        if ($newLogin) {
            setcookie('login', $newLogin, time() + 3600*24*7, '/');
        }
        
        if ($avatarPath) {
            setcookie('avatar', $avatarPath, time() + 3600*24*7, '/');
        }

        echo 'Success';
    } else {
        echo 'Please fill in at least one field (login, password, or avatar)';
        exit();
    }
}
?>
