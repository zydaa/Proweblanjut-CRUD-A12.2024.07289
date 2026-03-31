<?php
session_start();
include "koneksi.php";

$pesan_error = "";

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
} elseif (isset($_COOKIE["ingat_user"])) {
    $_SESSION["user_id"] = $_COOKIE["ingat_user"];
    $_SESSION["username"] = $_COOKIE["ingat_nama"];
    header("Location: index.php");
    exit();
}

if (isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $koneksi->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];

            if (isset($_POST["remember"])) {
                setcookie("ingat_user", $user["id"], time() + (86400 * 7), "/");
                setcookie("ingat_nama", $user["username"], time() + (86400 * 7), "/");
            }

            header("Location: index.php");
            exit();
        } else {
            $pesan_error = "Password salah kocak!";
        }
    } else {
        $pesan_error = "Username tidak ditemukan!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin Warnet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 400px; margin: 100px auto; text-align: center;">
        <h2 style="color: #818cf8; margin-bottom: 20px;">Login Admin Warnet</h2>
        
        <?php if($pesan_error != "") echo "<div style='color: #ef4444; margin-bottom: 15px;'>$pesan_error</div>"; ?>

        <form method="POST" style="text-align: left;">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required style="width: 100%; padding: 12px; background-color: #1a1a24; border: 1px solid #35354d; color: #ffffff; border-radius: 8px; outline: none;">
            </div>
            
            <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" name="remember" id="remember" style="width: auto;">
                <label for="remember" style="margin: 0; font-weight: normal;">Ingat Saya</label>
            </div>

            <button type="submit" name="login" class="btn btn-primary" style="width: 100%;">Masuk / Login</button>
        </form>
        <p style="margin-top: 20px; font-size: 14px;">Belum punya akun? <a href="register.php">Daftar dulu</a></p>
    </div>
</body>
</html>