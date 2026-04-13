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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Inventaris Warnet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        
        <div class="page-header">
            <h2>Daftar Inventaris Warnet Gaming</h2>
            <div class="header-buttons">
                <a href="index.php" class="btn-secondary">⬅ Kembali</a>
                <a href="tambah_data.php" class="btn btn-primary">+ Tambah Data</a>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Merk</th>
                        <th>Jumlah</th>
                        <th>Kondisi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $query = "SELECT * FROM inventaris ORDER BY id_barang DESC";
                    $result = mysqli_query($koneksi, $query);

                    while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        
                        <td style="text-align: center;">
                            <?php if (!empty($row['thumbpath'])) { ?>
                                <a href="<?= htmlspecialchars($row['filepath']); ?>" target="_blank" title="Klik untuk lihat ukuran penuh">
                                    <img src="<?= htmlspecialchars($row['thumbpath']); ?>" alt="Foto" style="max-width: 70px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.2); transition: transform 0.2s;">
                                </a>
                                <br>
                                <a href="<?= htmlspecialchars($row['filepath']); ?>" target="_blank" style="font-size: 11px; color: #818cf8; text-decoration: none;">Lihat Asli</a>
                            <?php } else { ?>
                                <span style="color: #9ca3af; font-size: 12px;">Kosong</span>
                            <?php } ?>
                        </td>
                        <td><?= htmlspecialchars($row['kode_barang']); ?></td>
                        <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                        <td><?= htmlspecialchars($row['kategori']); ?></td>
                        <td><?= htmlspecialchars($row['merk']); ?></td>
                        <td><?= htmlspecialchars($row['jumlah']); ?> Unit</td>
                        <td><?= htmlspecialchars($row['kondisi']); ?></td>
                        <td>
                            <a href="update.php?id=<?= $row['id_barang']; ?>" class="btn btn-primary" style="padding: 6px 10px; font-size: 12px;">Edit</a>
                            <a href="hapus.php?id=<?= $row['id_barang']; ?>" class="btn btn-danger" style="padding: 6px 10px; font-size: 12px;" onclick="return confirm('Yakin ingin menghapus data ini?');">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div> 
</body>
</html>