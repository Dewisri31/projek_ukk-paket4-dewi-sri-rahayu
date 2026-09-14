<?php

include("../connection.php");

$id = $_GET['id_detailb'];

$sql = "DELETE FROM detail_buku WHERE id_detailb = '$id'";

if ($conn->query($sql) === TRUE) {

    header("Location: tampil.detail.php");
    exit;

} else {

    echo " Gagal menghapus data: " . $conn->error;

}

$conn->close();

?>