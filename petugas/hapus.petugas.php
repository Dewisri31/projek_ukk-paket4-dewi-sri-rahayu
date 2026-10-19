<?php
require_once '../auth.php'; require_admin();
?>
<?php

// Menghubungkan dengan database
include("../connection.php");

// Mengambil ID petugas dari URL
$id = $_GET['id_petugas'];

// Query untuk menghapus data petugas
$sql = "DELETE FROM petugas
        WHERE id_petugas = '$id'";

// Menjalankan query
if ($conn->query($sql) === TRUE) {

    // Kembali ke halaman tampil petugas
    header("Location: tampil.petugas.php");
    exit;

} else {

    echo "Gagal menghapus data: " . $conn->error;

}

// Menutup koneksi database
$conn->close();

?>