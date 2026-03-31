<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    if (isset($_COOKIE["ingat_user"])) {
        $_SESSION["user_id"] = $_COOKIE["ingat_user"];
        $_SESSION["username"] = $_COOKIE["ingat_nama"];
    } else {
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard </title>
    <link rel="stylesheet" href="style.css">
    <a href="logout.php" class="btn btn-danger">Logout</a>
    

    
    <style>
        .dash-wrapper {
            max-width: 900px;
            margin: 50px auto;
            background-color: #232333;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            color: white;
            font-family: 'Segoe UI', sans-serif;
            border: 1px solid #35354d;
        }

        .dash-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .dash-header h2 {
            color: #818cf8;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .card-container {
            display: flex;
            justify-content: space-between;
            gap: 30px;
        }

        .dash-card {
            flex: 1;
            background-color: #1f1f2e;
            padding: 30px;
            border-radius: 12px;
            border: 1px solid #35354d;
            text-align: center;
            transition: 0.3s;
        }

        .dash-card:hover {
            transform: translateY(-5px);
            border-color: #818cf8;
            box-shadow: 0 5px 15px rgba(129, 140, 248, 0.2);
        }

        .dash-card h3 {
            color: #ffffff;
            margin-bottom: 15px;
            font-size: 22px;
        }

        .dash-card p {
            color: #a5b4fc;
            margin-bottom: 25px;
            font-size: 15px;
            line-height: 1.5;
        }

        .btn-dash {
            display: inline-block;
            background-color: #4f46e5;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s;
        }

        .btn-dash:hover {
            background-color: #4338ca;
        }
    </style>
</head>
<body style="background-color: #1a1a24; margin: 0; padding: 20px;">

    <div class="dash-wrapper">
        <div class="dash-header">
            <h2>Sistem Inventaris Warnet Gaming</h2>
            <p style="color: #94a3b8;">Selamat datang, Silakan pilih menu di bawah ini.</p>
        </div>

        <div class="card-container">
            
            <div class="dash-card">
                <h3>📦 Data Barang</h3>
                <p>Lihat keseluruhan data, edit ketersediaan, atau hapus inventaris yang sudah tidak layak.</p>
                <a href="tampil_data.php" class="btn-dash">Buka Data Barang</a>
            </div>

            <div class="dash-card">
                <h3>➕ Tambah Barang</h3>
                <p>Input data PC baru, Periferal (Mouse, Keyboard, Headset), atau perlengkapan jaringan lainnya.</p>
                <a href="tambah_data.php" class="btn-dash">+ Tambah Data</a>
            </div>

        </div>
    </div>

</body>
</html>