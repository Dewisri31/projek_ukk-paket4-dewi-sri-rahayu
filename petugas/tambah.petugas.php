<?php

include("../connection.php");

$id_petugas = $_POST["id_petugas"];
$nama_petugas = $_POST["nama_petugas"];
$username = $_POST["username"];
$password = $_POST["password"];

$sql = "INSERT INTO petugas (id_petugas, nama_petugas, username, password)
        VALUES ('$id_petugas', '$nama_petugas', '$username', '$password')";

if (mysqli_query($conn, $sql)) {
    echo "<script>
            alert('Data berhasil ditambahkan');
            window.location='tampil.petugas.php';
          </script>";
} else {
    echo "Gagal menambahkan data: " . mysqli_error($conn);
}

?>