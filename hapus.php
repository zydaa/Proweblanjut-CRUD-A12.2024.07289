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

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt_select = $koneksi->prepare("SELECT filepath, thumbpath FROM inventaris WHERE id_barang = ?");
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $filepath = $row['filepath'];
        $thumbpath = $row['thumbpath'];
        
        if (!empty($filepath) && file_exists($filepath)) {
            unlink($filepath);
        }
        
        if (!empty($thumbpath) && file_exists($thumbpath)) {
            unlink($thumbpath);
        }
    }
    $stmt_select->close();

    $stmt_delete = $koneksi->prepare("DELETE FROM inventaris WHERE id_barang = ?");
    $stmt_delete->bind_param("i", $id);
    
    if ($stmt_delete->execute()) {
        echo "<script>alert('Data dan Gambar berhasil dihapus!'); window.location='tampil_data.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!'); window.location='tampil_data.php';</script>";
    }
    $stmt_delete->close();
} else {
    header("Location: tampil_data.php");
}
?>