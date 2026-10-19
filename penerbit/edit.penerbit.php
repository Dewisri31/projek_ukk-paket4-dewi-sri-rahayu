<?php
require_once '../auth.php'; require_staff();
?>
<?php

// Menghubungkan dengan database
include("../connection.php");

// Mengambil ID penerbit dari URL
$id = $_GET['id_penerbit'];

// Mengecek apakah tombol update ditekan
if (isset($_POST['update'])) {

    // Mengambil data dari form
    $id_admin=role()==='admin'?current_user_id():null; $id_petugas=role()==='petugas'?current_user_id():null;
    $kode_penerbit = $_POST['kode_penerbit'];
    $nama_penerbit = $_POST['nama_penerbit'];

    $adminSql=$id_admin===null?'NULL':(string)$id_admin;
    $petugasSql=$id_petugas===null?'NULL':(string)$id_petugas;
    $kode_penerbit=$conn->real_escape_string($kode_penerbit);
    $nama_penerbit=$conn->real_escape_string($nama_penerbit);
    $sql = "UPDATE penerbit SET id_admin=$adminSql, id_petugas=$petugasSql, kode_penerbit='$kode_penerbit', nama_penerbit='$nama_penerbit' WHERE id_penerbit=".(int)$id;

    // Menjalankan query
    if ($conn->query($sql) === TRUE) {

        echo "<script>
                alert('Data penerbit berhasil diubah');
                window.location='tampil.penerbit.php';
              </script>";

        exit;

    } else {

        echo "Gagal mengubah data: " . $conn->error;

    }
}

// Mengambil data penerbit berdasarkan ID
$query = $conn->query(
    "SELECT * FROM penerbit
     WHERE id_penerbit = '$id'"
);

// Mengubah hasil query menjadi array
$penerbit = $query->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="id">

<head>
<link rel="stylesheet" href="../style.css">

    <meta charset="UTF-8">

    <title>Edit Data Penerbit</title>

</head>

<body>

    <h1>Edit Data Penerbit</h1>

    <div class="page-head"><div></div><a class="btn btn-light" href="../dashboard.<?= $_SESSION['role'] ?>.php">← Kembali ke Dashboard</a></div>
<form method="POST">

        <label>ID PENERBIT</label>
        <br>

        <input
            type="number"
            value="<?= $penerbit['id_penerbit']; ?>"
            readonly
        >

        <br><br>


        <label>Akun Pengelola</label><br><input value="<?= htmlspecialchars(current_user_label()) ?>" readonly><br><br>


        <label>KODE PENERBIT</label>
        <br>

        <input
            type="text"
            name="kode_penerbit"
            value="<?= $penerbit['kode_penerbit']; ?>"
            required
        >

        <br><br>


        <label>NAMA PENERBIT</label>
        <br>

        <input
            type="text"
            name="nama_penerbit"
            value="<?= $penerbit['nama_penerbit']; ?>"
            required
        >

        <br><br>


        <button type="submit" name="update">
            Simpan Perubahan
        </button>

        <a href="tampil.penerbit.php">
            Batal
        </a>

    </form>

</body>

</html>