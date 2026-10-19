<?php

// Menghubungkan dengan database
include("../connection.php");

// Mengambil ID detail dari URL
$id = $_GET['id_detail'];

// Query untuk menghapus data histori
$sql = "DELETE FROM histori
        WHERE id_detail = '$id'";

// Menjalankan query
if (mysqli_query($conn, $sql)) {

    echo "<script>
            alert('Data histori berhasil dihapus');
            window.location='tampil.histori.php';
          </script>";

} else {

    echo "Gagal menghapus data: " . mysqli_error($conn);

}

?>