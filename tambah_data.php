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

include 'koneksi.php';

$pesan_error = "";
$pesan_sukses = "";

if (isset($_POST['simpan'])) {
    $kode_barang = mysqli_real_escape_string($koneksi, trim($_POST['kode_barang']));
    $nama_barang = mysqli_real_escape_string($koneksi, trim($_POST['nama_barang']));
    $kategori = mysqli_real_escape_string($koneksi, trim($_POST['kategori']));
    $merk = mysqli_real_escape_string($koneksi, trim($_POST['merk']));
    $jumlah = (int) $_POST['jumlah'];
    $kondisi = mysqli_real_escape_string($koneksi, trim($_POST['kondisi']));

    if (preg_match('/[0-9]/', $nama_barang)) {
        $pesan_error = "Error: Nama barang tidak boleh mengandung angka!";
    } else {
        $nama_foto = $_FILES['foto']['name'];
        $tmp_foto = $_FILES['foto']['tmp_name'];
        $ukuran_foto = $_FILES['foto']['size'];
        $ext_diizinkan = array('png', 'jpg', 'jpeg');
        $x = explode('.', $nama_foto);
        $ekstensi = strtolower(end($x));

        if (in_array($ekstensi, $ext_diizinkan) === true) {
            $foto_baru = time() . '_' . $nama_foto; 
            $target_asli = 'uploads/' . $foto_baru;
            $target_thumb = 'thumbnails/' . $foto_baru;

            move_uploaded_file($tmp_foto, $target_asli);

            list($width, $height) = getimagesize($target_asli);
            $new_width = 150; 
            $new_height = floor($height * ($new_width / $width)); 

            $thumb = imagecreatetruecolor($new_width, $new_height);

            if ($ekstensi == 'png') {
                $source_image = imagecreatefrompng($target_asli);
                imagealphablending($thumb, false);
                imagesavealpha($thumb, true);
                imagecopyresampled($thumb, $source_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagepng($thumb, $target_thumb); 
            } else {
                $source_image = imagecreatefromjpeg($target_asli);
                imagecopyresampled($thumb, $source_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagejpeg($thumb, $target_thumb, 80); 
            }

            // Bersihkan memori server
            imagedestroy($thumb);
            imagedestroy($source_image);
            

            
            $stmt = $koneksi->prepare("INSERT INTO inventaris (kode_barang, nama_barang, kategori, merk, jumlah, kondisi, filepath, thumbpath) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            
            
            $stmt->bind_param("ssssisss", $kode_barang, $nama_barang, $kategori, $merk, $jumlah, $kondisi, $target_asli, $target_thumb);

            if ($stmt->execute()) {
                $pesan_sukses = "Data barang berhasil ditambahkan!";
            } else {
                $pesan_error = "Gagal menyimpan data: " . $stmt->error;
            }
            $stmt->close();
        } 
        else {
            $pesan_error = "Ekstensi foto tidak diperbolehkan. Harus JPG, JPEG, atau PNG.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Data Inventaris</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        
        <div class="page-header">
            <h2>Tambah Data Barang Baru</h2>
            <div class="header-buttons">
                <a href="index.php" class="btn-secondary">⬅ Batal & Kembali</a>
            </div>
        </div>

        <?php if($pesan_error != "") echo "<div style='color: #ef4444; margin-bottom: 15px;'>$pesan_error</div>"; ?>
        <?php if($pesan_sukses != "") echo "<div style='color: #10b981; margin-bottom: 15px;'>$pesan_sukses</div>"; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Kode Barang (Unik):</label>
                <input type="text" name="kode_barang" required>
            </div>
            <div class="form-group">
                <label>Nama Barang (Hanya Huruf):</label>
                <input type="text" name="nama_barang" required placeholder="Contoh: Keyboard Mechanical">
            </div>
            <div class="form-group">
                <label>Kategori:</label>
                <select name="kategori" required>
                    <option value="PC">PC / Komputer</option>
                    <option value="Periferal">Periferal (Mouse, Keyboard, dll)</option>
                    <option value="Jaringan">Jaringan (Router, Kabel)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Merk:</label>
                <input type="text" name="merk" required>
            </div>
            <div class="form-group">
                <label>Jumlah:</label>
                <input type="number" name="jumlah" required>
            </div>
            <div class="form-group">
                <label>Kondisi:</label>
                <select name="kondisi" required>
                    <option value="Baik">Baik</option>
                    <option value="Rusak Ringan">Rusak Ringan</option>
                    <option value="Rusak Berat">Rusak Berat</option>
                </select>
            </div>
            <div class="form-group">
                <label>Unggah Foto Barang (Wajib):</label>
                <input type="file" name="foto" accept="image/*" required>
            </div>
            <button type="submit" name="simpan" class="btn btn-primary">Simpan Data</button>
        </form>

    </div> 
</body>
</html>