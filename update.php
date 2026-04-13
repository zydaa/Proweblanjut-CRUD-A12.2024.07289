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

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $koneksi->prepare("SELECT * FROM inventaris WHERE id_barang = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    
    if(!$data) {
        die("Data tidak ditemukan!");
    }
}

if (isset($_POST['update'])) {
    $id = $_POST['id_barang'];
    $kode_barang = mysqli_real_escape_string($koneksi, trim($_POST['kode_barang']));
    $nama_barang = mysqli_real_escape_string($koneksi, trim($_POST['nama_barang']));
    $kategori = mysqli_real_escape_string($koneksi, trim($_POST['kategori']));
    $merk = mysqli_real_escape_string($koneksi, trim($_POST['merk']));
    $jumlah = (int) $_POST['jumlah'];
    $kondisi = mysqli_real_escape_string($koneksi, trim($_POST['kondisi']));
    
    $filepath_lama = $_POST['filepath_lama'];
    $thumbpath_lama = $_POST['thumbpath_lama'];

    if (preg_match('/[0-9]/', $nama_barang)) {
        $pesan_error = "Error: Nama barang tidak boleh mengandung angka!";
    } else {
        $filepath_baru = $filepath_lama; 
        $thumbpath_baru = $thumbpath_lama;

        if ($_FILES['foto']['name'] != "") {
            $nama_foto = $_FILES['foto']['name'];
            $tmp_foto = $_FILES['foto']['tmp_name'];
            $x = explode('.', $nama_foto);
            $ekstensi = strtolower(end($x));
            $ext_diizinkan = array('png', 'jpg', 'jpeg');

            if (in_array($ekstensi, $ext_diizinkan)) {
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
                imagedestroy($thumb);
                imagedestroy($source_image);

                $filepath_baru = $target_asli;
                $thumbpath_baru = $target_thumb;

                if (!empty($filepath_lama) && file_exists($filepath_lama)) {
                    unlink($filepath_lama);
                }
                if (!empty($thumbpath_lama) && file_exists($thumbpath_lama)) {
                    unlink($thumbpath_lama);
                }
            } else {
                $pesan_error = "Ekstensi foto tidak valid!";
            }
        }

        if ($pesan_error == "") {
            $stmt_update = $koneksi->prepare("UPDATE inventaris SET kode_barang=?, nama_barang=?, kategori=?, merk=?, jumlah=?, kondisi=?, filepath=?, thumbpath=? WHERE id_barang=?");
            
            
            $stmt_update->bind_param("ssssisssi", $kode_barang, $nama_barang, $kategori, $merk, $jumlah, $kondisi, $filepath_baru, $thumbpath_baru, $id);
            
            if ($stmt_update->execute()) {
                echo "<script>alert('Data berhasil diupdate!'); window.location='tampil_data.php';</script>";
            } else {
                $pesan_error = "Gagal update: " . $stmt_update->error;
            }
            $stmt_update->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Inventaris</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        
        <div class="page-header">
            <h2>Edit Data: <?= htmlspecialchars($data['nama_barang']); ?></h2>
            <div class="header-buttons">
                <a href="tampil_data.php" class="btn-secondary">⬅ Batal & Kembali</a>
            </div>
        </div>

        <?php if($pesan_error != "") echo "<div style='color: #ef4444; margin-bottom: 15px;'>$pesan_error</div>"; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_barang" value="<?= $data['id_barang']; ?>">
            <input type="hidden" name="filepath_lama" value="<?= htmlspecialchars($data['filepath'] ?? ''); ?>">
            <input type="hidden" name="thumbpath_lama" value="<?= htmlspecialchars($data['thumbpath'] ?? ''); ?>">

            <div class="form-group">
                <label>Kode Barang:</label>
                <input type="text" name="kode_barang" value="<?= htmlspecialchars($data['kode_barang']); ?>" required>
            </div>
            <div class="form-group">
                <label>Nama Barang:</label>
                <input type="text" name="nama_barang" value="<?= htmlspecialchars($data['nama_barang']); ?>" required>
            </div>
            <div class="form-group">
                <label>Kategori:</label>
                <select name="kategori" required>
                    <option value="PC" <?= ($data['kategori'] == 'PC') ? 'selected' : ''; ?>>PC / Komputer</option>
                    <option value="Periferal" <?= ($data['kategori'] == 'Periferal') ? 'selected' : ''; ?>>Periferal (Mouse, Keyboard, dll)</option>
                    <option value="Jaringan" <?= ($data['kategori'] == 'Jaringan') ? 'selected' : ''; ?>>Jaringan (Router, Kabel)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Merk:</label>
                <input type="text" name="merk" value="<?= htmlspecialchars($data['merk']); ?>" required>
            </div>
            <div class="form-group">
                <label>Jumlah:</label>
                <input type="number" name="jumlah" value="<?= htmlspecialchars($data['jumlah']); ?>" required>
            </div>
            <div class="form-group">
                <label>Kondisi:</label>
                <select name="kondisi" required>
                    <option value="Baik" <?= ($data['kondisi'] == 'Baik') ? 'selected' : ''; ?>>Baik</option>
                    <option value="Rusak Ringan" <?= ($data['kondisi'] == 'Rusak Ringan') ? 'selected' : ''; ?>>Rusak Ringan</option>
                    <option value="Rusak Berat" <?= ($data['kondisi'] == 'Rusak Berat') ? 'selected' : ''; ?>>Rusak Berat</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Foto Saat Ini:</label><br>
                <div style="display: flex; align-items: center; gap: 15px; margin-top: 10px;">
                    <?php if(!empty($data['thumbpath'])): ?>
                        <img src="<?= htmlspecialchars($data['thumbpath']); ?>" style="border-radius: 8px; border: 2px solid #4c51bf; max-width: 150px;">
                        <a href="<?= htmlspecialchars($data['filepath']); ?>" target="_blank" class="btn" style="background-color: #4c51bf; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px;">Lihat Full</a>
                    <?php else: ?>
                        <span style="color: #ef4444; font-style: italic;">Foto kosong/telah dihapus. Silakan upload ulang.</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Ganti Foto (Biarkan kosong jika tidak ingin ganti):</label>
                <input type="file" name="foto" accept="image/*">
            </div>
            
            <button type="submit" name="update" class="btn btn-primary">Update Data</button>
        </form>

    </div> 
</body>
</html>