<?php
include "koneksi.php";
$pesan = "";

if (isset($_POST["register"])) {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); 

    $stmt = $koneksi->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        $pesan = "<div style='color: #10b981; margin-bottom: 15px;'>Registrasi berhasil! Silakan login.</div>";
    } else {
        $pesan = "<div style='color: #ef4444; margin-bottom: 15px;'>Gagal! Username mungkin sudah ada.</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Admin Warnet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 400px; margin: 100px auto; text-align: center;">
        <h2 style="color: #818cf8; margin-bottom: 20px;">Daftar Akun Admin</h2>
        <?= $pesan; ?>
        <form method="POST" style="text-align: left;">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required style="width: 100%; padding: 12px; background-color: #1a1a24; border: 1px solid #35354d; color: #ffffff; border-radius: 8px; outline: none;">
            </div>
            <button type="submit" name="register" class="btn btn-primary" style="width: 100%;">Daftar</button>
        </form>
        <p style="margin-top: 20px; font-size: 14px;">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</body>
</html>