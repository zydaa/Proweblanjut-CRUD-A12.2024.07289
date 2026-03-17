<?php
include 'koneksi.php';


if (isset($_GET['id'])) {
    $id = $_GET['id'];

    
    $stmt_select = $koneksi->prepare("SELECT foto FROM inventaris WHERE id_barang = ?");
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $foto_lama = $row['foto'];
        $path_foto = "upload/" . $foto_lama;
        
        
        if (file_exists($path_foto)) {
            unlink($path_foto);
        }
    }
    $stmt_select->close();

    
    $stmt_delete = $koneksi->prepare("DELETE FROM inventaris WHERE id_barang = ?");
    $stmt_delete->bind_param("i", $id);
    
    if ($stmt_delete->execute()) {
        echo "<script>alert('Data berhasil dihapus!'); window.location='tampil_data.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!'); window.location='tampil_data.php';</script>";
    }
    $stmt_delete->close();
} else {
    header("Location: tampil_data.php");
}
?>