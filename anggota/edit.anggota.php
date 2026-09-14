<?php

include("../connection.php");

$id = $_GET['id_anggota'];

if (isset($_POST['update'])) {

    $nama_anggota  = $_POST['nama_anggota'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tempat_lahir  = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $telpon        = $_POST['telpon'];
    $alamat        = $_POST['alamat'];

    $sql = "UPDATE anggota SET
            nama_anggota = '$nama_anggota',
            jenis_kelamin = '$jenis_kelamin',
            tempat_lahir = '$tempat_lahir',
            tanggal_lahir = '$tanggal_lahir',
            telpon = '$telpon',
            alamat = '$alamat'
            WHERE id_anggota = '$id'";

    if ($conn->query($sql) === TRUE) {

        header("Location: tampil.anggota.php");
        exit;

    } else {

        echo "Gagal mengubah data: " . $conn->error;
    }
}

$query = $conn->query(
    "SELECT * FROM anggota WHERE id_anggota = '$id'"
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