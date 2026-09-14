<?php

include("../connection.php");

$id = $_GET['id_admin'];

$query = $conn->query("SELECT * FROM admin WHERE id_admin = '$id'");

$admin = $query->fetch_assoc();

if (isset($_POST['update'])) {

    $nama_admin = $_POST['nama_admin'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "UPDATE admin SET
            nama_admin = '$nama_admin',
            username = '$username',
            password = '$password'
            WHERE id_admin = '$id'";

    if ($conn->query($sql) === TRUE) {

        echo "<script>
                alert('Data berhasil diubah');
                window.location='tampil.admin.php';
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
    <title>Edit Data Admin</title>
</head>

<body>

<h1>Edit Data Admin</h1>

<form method="POST">

    <label>ID Admin</label>
    <br>
    <input type="text"
           value="<?= $admin['id_admin']; ?>"
           readonly>

    <br><br>

    <label>Nama Admin</label>
    <br>
    <input type="text"
           name="nama_admin"
           value="<?= $admin['nama_admin']; ?>"
           required>

    <br><br>

    <label>Username</label>
    <br>
    <input type="text"
           name="username"
           value="<?= $admin['username']; ?>"
           required>

    <br><br>

    <label>Password</label>
    <br>
    <input type="text"
           name="password"
           value="<?= $admin['password']; ?>"
           required>

    <br><br>

    <button type="submit" name="update">
        Simpan Perubahan
    </button>

    <a href="tampil.admin.php">Batal</a>

</form>

</body>
</html>