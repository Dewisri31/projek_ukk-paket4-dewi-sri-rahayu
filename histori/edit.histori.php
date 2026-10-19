<?php require_once '../auth.php'; require_master_staff(); ?>
<?php

// Menghubungkan dengan database
include("../connection.php");

// Mengambil ID detail dari URL
$id = $_GET['id_detail'];

// Mengecek apakah tombol update ditekan
if (isset($_POST['update'])) {

    // Mengambil data dari form
    $id_admin=role()==='admin'?current_user_id():null; $id_petugas=role()==='petugas'?current_user_id():null;
    $id_peminjaman = $_POST["id_peminjaman"];
    $id_buku       = $_POST["id_buku"];
    $jumlah        = $_POST["jumlah"];
    $denda         = $_POST["denda"];

    // Query untuk mengubah data histori
    $sql = "UPDATE histori SET

            id_admin = ' . ($id_admin===null ? 'NULL' : (int)$id_admin) . ', id_petugas = ' . ($id_petugas===null ? 'NULL' : (int)$id_petugas) . ',
            id_peminjaman = '$id_peminjaman',
            id_buku = '$id_buku',
            jumlah = '$jumlah',
            denda = '$denda'

            WHERE id_detail = '$id'";

    // Menjalankan query
    if ($conn->query($sql) === TRUE) {

        echo "<script>
                alert('Data histori berhasil diubah');
                window.location='tampil.histori.php';
              </script>";

        exit;

    } else {

        echo "Gagal mengubah data: " . $conn->error;

    }
}

// Mengambil data histori berdasarkan ID
$query = $conn->query(
    "SELECT * FROM histori
     WHERE id_detail = '$id'"
);

// Mengubah hasil query menjadi array
$histori = $query->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="id">

<head>
<link rel="stylesheet" href="../style.css">

    <meta charset="UTF-8">

    <title>Edit Data Histori</title>

</head>

<body>

    <h1>Edit Data Histori</h1>

    <div class="page-head"><div></div><a class="btn btn-light" href="../dashboard.<?= $_SESSION['role'] ?>.php">← Kembali ke Dashboard</a></div>
<form method="POST">

        <label>ID DETAIL</label>
        <br>

        <input
            type="number"
            value="<?= $histori['id_detail']; ?>"
            readonly
        >

        <br><br>


        <label>Akun Pengelola</label><br><input value="<?= htmlspecialchars(current_user_label()) ?>" readonly><br><br>


        <label>ID PEMINJAMAN</label>
        <br>

        <input
            type="number"
            name="id_peminjaman"
            value="<?= $histori['id_peminjaman']; ?>"
            required
        >

        <br><br>


        <label>ID BUKU</label>
        <br>

        <input
            type="number"
            name="id_buku"
            value="<?= $histori['id_buku']; ?>"
            required
        >

        <br><br>


        <label>JUMLAH</label>
        <br>

        <input
            type="number"
            name="jumlah"
            value="<?= $histori['jumlah']; ?>"
            required
        >

        <br><br>


        <label>DENDA</label>
        <br>

        <input
            type="number"
            name="denda"
            value="<?= $histori['denda']; ?>"
            required
        >

        <br><br>


        <button type="submit" name="update">
            Simpan Perubahan
        </button>

        <a href="tampil.histori.php">
            Batal
        </a>

    </form>

</body>

</html>