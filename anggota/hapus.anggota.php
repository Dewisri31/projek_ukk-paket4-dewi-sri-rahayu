<?php
require_once '../auth.php'; require_staff();
?>
<?php

// Menghubungkan dengan database
include("../connection.php");

// Mengambil ID anggota dari URL
$id = $_GET['id_anggota'];

// Query untuk menghapus anggota
$sql = "DELETE FROM anggota
        WHERE id_anggota = '$id'";

// Menjalankan query
if ($conn->query($sql) === TRUE) {

    // Kembali ke halaman data anggota
    header("Location: tampil.anggota.php");
    exit;

} else {

    echo "Gagal menghapus data: " . $conn->error;

}

// Menutup koneksi database
$conn->close();

?>