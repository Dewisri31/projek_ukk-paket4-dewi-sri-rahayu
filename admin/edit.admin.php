<?php
require_once '../auth.php'; require_admin();
?>
<?php

// Menghubungkan ke database
include("../connection.php");

// Mengambil ID admin dari URL
$id = $_GET['id_admin'];

// Mengambil data admin berdasarkan ID
$query = $conn->query(
    "SELECT * FROM admin WHERE id_admin = '$id'"
);

// Mengubah hasil query menjadi array
$admin = $query->fetch_assoc();

// Jika tombol update ditekan
if (isset($_POST['update'])) {

    // Mengambil data dari form
    $nama_admin = $_POST['nama_admin'];
    $username   = $_POST['username'];
    $password   = $_POST['password'];

    // Query untuk mengubah data
    $sql = "UPDATE admin SET
            nama_admin = '$nama_admin',
            username = '$username',
            password = '$password'
            WHERE id_admin = '$id'";

    // Menjalankan query
    if ($conn->query($sql) === TRUE) {

        echo "<script>
                alert('Data admin berhasil diubah');
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
<link rel="stylesheet" href="../style.css">
    <meta charset="UTF-8">
    <title>Edit Data Admin</title>
</head>

<body>

    <h1>Edit Data Admin</h1>

    <div class="page-head"><div></div><a class="btn btn-light" href="../dashboard.<?= $_SESSION['role'] ?>.php">← Kembali ke Dashboard</a></div>
<form method="POST">

        <label>ID ADMIN</label>
        <br>

        <input type="number"
               value="<?= $admin['id_admin']; ?>"
               readonly>

        <br><br>

        <label>NAMA ADMIN</label>
        <br>

        <input type="text"
               name="nama_admin"
               value="<?= $admin['nama_admin']; ?>"
               required>

        <br><br>

        <label>USERNAME</label>
        <br>

        <input type="text"
               name="username"
               value="<?= $admin['username']; ?>"
               required>

        <br><br>

        <label>PASSWORD</label>
        <br>

        <input type="text"
               name="password"
               value="<?= $admin['password']; ?>"
               required>

        <br><br>

        <button type="submit" name="update">
            Simpan Perubahan
        </button>

        <a href="tampil.admin.php">
            Batal
        </a>

    </form>

</body>

</html>