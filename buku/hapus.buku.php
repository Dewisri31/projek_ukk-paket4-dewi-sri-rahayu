<?php

include("../connection.php");

$id = $_GET['id_buku'];

$sql = "DELETE FROM buku WHERE id_buku = '$id'";

if ($conn->query($sql) === TRUE) {

    header("Location: tampil.buku.php");
    exit;

} else {

    echo " Gagal menghapus data: " . $conn->error;

}

$conn->close();

?>