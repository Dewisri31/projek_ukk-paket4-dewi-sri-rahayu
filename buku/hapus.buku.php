<?php

// Menghubungkan ke database
include("../connection.php");

// Mengambil ID buku dari URL
$id = $_GET['id_buku'];

// Query untuk menghapus data buku
$sql = "DELETE FROM buku
        WHERE id_buku = '$id'";

// Menjalankan query
if ($conn->query($sql) === TRUE) {

    // Kembali ke halaman tampil buku
    header("Location: tampil.buku.php");
    exit;

} else {

    echo "Gagal menghapus data: " . $conn->error;

}

// Menutup koneksi database
$conn->close();

?>