<?php

require_once '../auth.php'; require_staff();
include '../connection.php';


// Mengambil ID kategori dari URL
$id = $_GET['id'] ?? '';


// Mengecek ID
if ($id == '') {
    die("ID kategori tidak ditemukan.");
}


// Mengambil data kategori
$query = mysqli_query($conn, "
    SELECT *
    FROM kategori
    WHERE id_kategori = '$id'
");


// Mengubah hasil menjadi array
$data = mysqli_fetch_assoc($query);


// Mengecek data
if (!$data) {
    die("Data kategori tidak ditemukan.");
}


// Jika tombol update ditekan
if (isset($_POST['update'])) {

    // Mengambil nama kategori baru
    $nama_kategori=trim($_POST['nama_kategori']??'');
    $id_admin=role()==='admin'?current_user_id():null; $id_petugas=role()==='petugas'?current_user_id():null;
    $nama_kategori=mysqli_real_escape_string($conn,$nama_kategori);
    $adminSql=$id_admin===null?'NULL':(string)$id_admin; $petugasSql=$id_petugas===null?'NULL':(string)$id_petugas;


    // Update kategori
    $update = mysqli_query($conn, "
        UPDATE kategori
        SET id_admin=$adminSql, id_petugas=$petugasSql, nama_kategori='$nama_kategori'
        WHERE id_kategori = '$id'
    ");


    // Mengecek hasil update
    if (!$update) {

        die(
            "Gagal mengubah kategori: "
            . mysqli_error($conn)
        );
    }


    // Kembali ke halaman kategori
    header("Location: tampil.kategori.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../style.css">
    <title>Edit Kategori</title>
</head>

<body>
<div class="page-head"><div><h1>📝 Edit Kategori</h1><p class="muted">Lengkapi data dengan benar.</p></div><a class="btn btn-light" href="<?='../dashboard.'.($_SESSION['role']??'').'.php'?>">← Kembali ke Dashboard</a></div>

<h2>Edit Kategori</h2>

<form method="POST">

    <label>Nama Kategori</label><br>

    <input
        type="text"
        name="nama_kategori"
        value="<?php echo $data['nama_kategori']; ?>"
        required
    >

    <br><br>

    <button type="submit" name="update">
        Update
    </button>

    <a href="tampil.kategori.php">
        Kembali
    </a>

</form>

</body>
</html>