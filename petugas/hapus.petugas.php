<?php

include("../connection.php");

$id = $_GET['id_petugas'];

$sql = "DELETE FROM petugas WHERE id_petugas = '$id'";

if ($conn->query($sql) === TRUE) {

    header("Location: tampil.petugas.php");
    exit;

} else {

    echo " Gagal menghapus data: " . $conn->error;

}

$conn->close();

?>