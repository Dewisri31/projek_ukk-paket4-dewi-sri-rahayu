<?php

include("../connection.php");

$id = $_GET['id_anggota'];

if (isset($_POST['update'])) {

    $id_detailb     = $_POST["id_detailb"];
    $id_kategori    = $_POST["id_kategori"];
    $id_penerbit    = $_POST["id_penerbit"];
    $pengarang      = $_POST["pengarang"];
    $tahun_terbit   = $_POST["tahun_terbit"];
    $jumlah_halaman = $_POST["jumlah_halaman"];
    $jumlah_stok    = $_POST["jumlah_stok"];
    $isbn           = $_POST["isbn"];
    $sql = "UPDATE anggota SET
            nama_anggota = '$nama_anggota',
            jenis_kelamin = '$jenis_kelamin',
            tempat_lahir = '$tempat_lahir',
            tanggal_lahir = '$tanggal_lahir',
            telpon = '$telpon',
            alamat = '$alamat'
            WHERE id_detailb = '$id'";

    if ($conn->query($sql) === TRUE) {

        header("Location: tampil.detail.php");
        exit;

    } else {

        echo "Gagal mengubah data: " . $conn->error;
    }
}

$query = $conn->query(
    "SELECT * FROM detailb WHERE id_detailb = '$id'"
);

$anggota = $query->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Anggota</title>
</head>

<body>

    <h1>Edit Data Anggota</h1>

    <form method="POST">

        <label>ID Anggota</label>
        <br>

        <input
            type="text"
            name="id_anggota"
            value="<?= $anggota['id_anggota']; ?>"
            readonly
        >

        <br><br>

        <label>Nama Anggota</label>
        <br>

        <input
            type="text"
            name="nama_anggota"
            value="<?= $anggota['nama_anggota']; ?>"
            required
        >

        <br><br>

        <label>Jenis Kelamin</label>
        <br>

        <select name="jenis_kelamin" required>

            <option value="pria"
                <?= $anggota['jenis_kelamin'] == 'pria' ? 'selected' : ''; ?>>
                Pria
            </option>

            <option value="wanita"
                <?= $anggota['jenis_kelamin'] == 'wanita' ? 'selected' : ''; ?>>
                Wanita
            </option>

        </select>

        <br><br>

        <label>Tempat Lahir</label>
        <br>

        <input
            type="text"
            name="tempat_lahir"
            value="<?= $anggota['tempat_lahir']; ?>"
            required
        >

        <br><br>

        <label>Tanggal Lahir</label>
        <br>

        <input
            type="date"
            name="tanggal_lahir"
            value="<?= $anggota['tanggal_lahir']; ?>"
            required
        >

        <br><br>

        <label>Telpon</label>
        <br>

        <input
            type="text"
            name="telpon"
            value="<?= $anggota['telpon']; ?>"
            required
        >

        <br><br>

        <label>Alamat</label>
        <br>

        <textarea
            name="alamat"
            rows="5"
            required
        ><?= $anggota['alamat']; ?></textarea>

        <br><br>

        <button type="submit" name="update">
            Simpan Perubahan
        </button>

        <a href="tampil.anggota.php">
            Batal
        </a>

    </form>

</body>

</html>