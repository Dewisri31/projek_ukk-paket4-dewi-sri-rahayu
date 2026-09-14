<?php

include("../connection.php");

$id = $_GET['id_petugas'];

$query = $conn->query("SELECT * FROM petugas WHERE id_petugas = '$id'");

$petugas = $query->fetch_assoc();

if (isset($_POST['update'])) {

    $nama_petugas = $_POST['nama_petugas'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "UPDATE petugas SET
            nama_petugas = '$nama_petugas',
            username = '$username',
            password = '$password'
            WHERE id_petugas = '$id'";

    if ($conn->query($sql) === TRUE) {

        echo "<script>
                alert('Data berhasil diubah');
                window.location='tampil.petugas.php';
              </script>";
        exit;

    } else {

        echo "Gagal mengubah data: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Data Petugas</title>
</head>

<body>

<h1>Edit Data Petugas</h1>

<form method="POST">

    <label>ID Petugas</label>
    <br>
    <input type="text"
           value="<?= $petugas['id_petugas']; ?>"
           readonly>

    <br><br>

    <label>Nama Petugas</label>
    <br>
    <input type="text"
           name="nama_petugas"
           value="<?= $petugas['nama_petugas']; ?>"
           required>

    <br><br>

    <label>Username</label>
    <br>
    <input type="text"
           name="username"
           value="<?= $petugas['username']; ?>"
           required>

    <br><br>

    <label>Password</label>
    <br>
    <input type="text"
           name="password"
           value="<?= $petugas['password']; ?>"
           required>

    <br><br>

    <button type="submit" name="update">
        Simpan Perubahan
    </button>

    <a href="tampil.petugas.php">Batal</a>

</form>

</body>
</html>