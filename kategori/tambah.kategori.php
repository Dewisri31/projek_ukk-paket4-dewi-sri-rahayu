<?php

require_once '../auth.php'; require_staff();
include '../connection.php';


// Mengecek apakah tombol simpan ditekan
if (isset($_POST['simpan'])) {

    // Mengambil nama kategori dari form
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');
    $id_admin = role()==='admin'?current_user_id():null; $id_petugas=role()==='petugas'?current_user_id():null;


    // Menambahkan kategori ke database
    $nama_kategori = mysqli_real_escape_string($conn, $nama_kategori);
    $adminSql = $id_admin===null ? 'NULL' : (string)$id_admin;
    $petugasSql = $id_petugas===null ? 'NULL' : (string)$id_petugas;
    $query = mysqli_query($conn, "INSERT INTO kategori (id_admin,id_petugas,nama_kategori) VALUES ($adminSql,$petugasSql,'$nama_kategori')");


    // Mengecek hasil query
    if (!$query) {

        die(
            "Gagal menambahkan kategori: "
            . mysqli_error($conn)
        );
    }


    // Kembali ke halaman kategori
    header("Location: tampil.kategori.php");
    exit;
}

?>