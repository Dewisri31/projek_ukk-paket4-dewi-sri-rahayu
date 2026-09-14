<?php

include("../connection.php");

$id_anggota     = $_POST["id_anggota"];
$nama_anggota   = $_POST["nama_anggota"];
$jenis_kelamin  = $_POST["jenis_kelamin"];
$tempat_lahir   = $_POST["tempat_lahir"];
$tanggal_lahir  = $_POST["tanggal_lahir"];
$telpon         = $_POST["telpon"];
$alamat         = $_POST["alamat"];

$sql = "INSERT INTO anggota 
        (id_anggota, nama_anggota, jenis_kelamin, tempat_lahir, tanggal_lahir, telpon, alamat)
        VALUES 
        ('$id_anggota', '$nama_anggota', '$jenis_kelamin', '$tempat_lahir', '$tanggal_lahir', '$telpon', '$alamat')";

if (mysqli_query($conn, $sql)) {
    echo "<script>
            alert('Data anggota berhasil ditambahkan');
            window.location='tampil.anggota.php';
          </script>";
} else {
    echo "Gagal menambahkan data: " . mysqli_error($conn);
}

?>