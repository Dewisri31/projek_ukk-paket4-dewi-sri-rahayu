<?php

include("../connection.php");

$id = $_GET['id_anggota'];

$sql = "DELETE FROM anggota WHERE id_anggota = '$id'";

if ($conn->query($sql) === TRUE) {

    header("Location: tampil.anggota.php");
    exit;

} else {

    echo " Gagal menghapus data: " . $conn->error;

}

$conn->close();

?>