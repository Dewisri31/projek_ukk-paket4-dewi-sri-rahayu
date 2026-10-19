<?php
require_once '../auth.php'; require_admin();
?>
<?php

// Menghubungkan dengan database
include("../connection.php");

// Mengambil data petugas dan nama admin
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
");

// Mengubah hasil query menjadi array
$result = $query->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>
<link rel="stylesheet" href="../style.css">

    <meta charset="UTF-8">

    <title>Data Petugas</title>

</head>

<body>
<div class="page-head"><div></div><a class="btn btn-light" href="../dashboard.<?= $_SESSION['role'] ?>.php">← Kembali ke Dashboard</a></div>

    <h1>PETUGAS</h1>

    <a href="form.petugas.html">
        + Tambah Data
    </a>

    <br><br>


    <table border="1" cellpadding="8" cellspacing="0">

        <thead>

            <tr>

                <th>ID PETUGAS</th>
                <th>ID ADMIN</th>
                <th>NAMA ADMIN</th>
                <th>NAMA PETUGAS</th>
                <th>USERNAME</th>
                <th>PASSWORD</th>
                <th>AKSI</th>

            </tr>

        </thead>


        <tbody>

            <?php foreach ($result as $p): ?>

                <tr>

                    <td>
                        <?= $p['id_petugas']; ?>
                    </td>

                    <td>
                        <?= $p['id_admin']; ?>
                    </td>

                    <td>
                        <?= $p['nama_admin']; ?>
                    </td>

                    <td>
                        <?= $p['nama_petugas']; ?>
                    </td>

                    <td>
                        <?= $p['username']; ?>
                    </td>

                    <td>
                        <?= $p['password']; ?>
                    </td>

                    <td>

      <a href="edit.petugas.php?id_petugas=<?= $p['id_petugas']; ?>">
    <button type="button">Edit</button>
</a>


                        <a
                            href="hapus.petugas.php?id_petugas=<?= $p['id_petugas']; ?>"
                            onclick="return confirm('Yakin ingin menghapus data ini?')"
                        >
                            <button>
                                Hapus
                            </button>
                        </a>

                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

</body>

</html>