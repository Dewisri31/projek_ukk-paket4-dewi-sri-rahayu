<?php

require_once '../auth.php'; require_staff();
include '../connection.php';

?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../style.css">
    <title>Tambah Kategori</title>
</head>

<body>
<div class="page-head"><div><h1>📝 Form Kategori</h1><p class="muted">Lengkapi data dengan benar.</p></div><a class="btn btn-light" href="<?='../dashboard.'.($_SESSION['role']??'').'.php'?>">← Kembali ke Dashboard</a></div>

<h2>Tambah Kategori</h2>

<form action="tambah.kategori.php" method="POST">

    <label>Nama Kategori</label><br>

    <input
        type="text"
        name="nama_kategori"
        placeholder="Masukkan nama kategori"
        required
    >

    <br><br>

    <button type="submit" name="simpan">
        Simpan
    </button>

    <a href="tampil.kategori.php">
        Kembali
    </a>

</form>

</body>
</html>