<?php

include("../connection.php");

$id = $_GET['id_admin'];

$sql = "DELETE FROM admin WHERE id_admin = '$id'";

if ($conn->query($sql) === TRUE) {

    header("Location: tampil.admin.php");
    exit;

} else {

    echo " Gagal menghapus data: " . $conn->error;

}

$conn->close();

?>