<?php
require_once '../auth.php'; require_staff();
?>
<?php

// Menghubungkan dengan database
include("../connection.php");

// Mengambil ID penerbit dari URL
$id = $_GET['id_penerbit'];

// Query untuk menghapus data
$sql = "DELETE FROM penerbit
        WHERE id_penerbit = '$id'";

// Menjalankan query
if (mysqli_query($conn, $sql)) {

    echo "<script>
            alert('Data penerbit berhasil dihapus');
            window.location='tampil.penerbit.php';
          </script>";

} else {

    echo "Gagal menghapus data: " . mysqli_error($conn);

}

?>