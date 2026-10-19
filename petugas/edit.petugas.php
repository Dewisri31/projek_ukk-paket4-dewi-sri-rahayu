<?php
require_once '../auth.php'; require_admin();
?>
<?php

// Menghubungkan dengan database
include("../connection.php");

// Mengambil ID petugas dari URL
$id_petugas = $_GET['id_petugas'] ?? null;

// Jika form disubmit (proses update data)
if (isset($_POST['update'])) {

    $id_petugas   = $_POST['id_petugas'];
    $id_admin=current_user_id();
    $nama_petugas = $_POST['nama_petugas'];
    $username     = $_POST['username'];
    $password     = $_POST['password'];

    // Update data ke database
    $conn->query("
        UPDATE petugas 
        SET 
            id_admin = '$id_admin',
            nama_petugas = '$nama_petugas',
            username = '$username',
            password = '$password'
        WHERE id_petugas = '$id_petugas'
    ");

    // Redirect kembali ke halaman daftar petugas
    header("Location: tampil.petugas.php");
    exit;

}

// Mengambil data petugas berdasarkan ID
$query = $conn->query("
    SELECT
        petugas.id_petugas,
        petugas.id_admin,
        admin.nama_admin,
        petugas.nama_petugas,
        petugas.username,
        petugas.password

    FROM petugas

    LEFT JOIN admin
        ON petugas.id_admin = admin.id_admin

    WHERE petugas.id_petugas = '$id_petugas'
");

$petugas = $query->fetch_assoc();

// Mengambil semua data admin untuk pilihan dropdown
$query_admin = $conn->query("SELECT * FROM admin");
$admins = $query_admin->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>
<link rel="stylesheet" href="../style.css">

    <meta charset="UTF-8">

    <title>Edit Data Petugas</title>

</head>

<body>
<div class="page-head"><div><h1>📝 Edit Petugas</h1><p class="muted">Lengkapi data dengan benar.</p></div><a class="btn btn-light" href="<?='../dashboard.'.($_SESSION['role']??'').'.php'?>">← Kembali ke Dashboard</a></div>

    <h1>EDIT DATA PETUGAS</h1>

    <a href="tampil.petugas.php">
        &laquo; Kembali
    </a>

    <br><br>

    <form action="" method="POST">

        <table border="0" cellpadding="8" cellspacing="0">

            <tr>
                <td>ID PETUGAS</td>
                <td>:</td>
                <td>
                    <input type="text" value="<?= $petugas['id_petugas']; ?>" disabled>
                    <input type="hidden" name="id_petugas" value="<?= $petugas['id_petugas']; ?>">
                </td>
            </tr>

            <tr><td>AKUN PENGELOLA</td><td>:</td><td><input value="<?= htmlspecialchars(current_user_label()) ?>" readonly></td></tr>

            <tr>
                <td>NAMA PETUGAS</td>
                <td>:</td>
                <td>
                    <input type="text" name="nama_petugas" value="<?= $petugas['nama_petugas']; ?>" required>
                </td>
            </tr>

            <tr>
                <td>USERNAME</td>
                <td>:</td>
                <td>
                    <input type="text" name="username" value="<?= $petugas['username']; ?>" required>
                </td>
            </tr>

            <tr>
                <td>PASSWORD</td>
                <td>:</td>
                <td>
                    <input type="text" name="password" value="<?= $petugas['password']; ?>" required>
                </td>
            </tr>

            <tr>
                <td></td>
                <td></td>
                <td>
                    <button type="submit" name="update">
                        Simpan Perubahan
                    </button>
                </td>
            </tr>

        </table>

    </form>

</body>

</html>