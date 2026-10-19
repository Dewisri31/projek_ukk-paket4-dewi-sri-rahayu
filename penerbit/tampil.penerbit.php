<?php
require_once '../auth.php'; require_staff();
?>
<?php

// Menghubungkan dengan database
include("../connection.php");

// Mengambil data penerbit
// sekaligus mengambil nama admin
$query = $conn->query("
    SELECT
        penerbit.id_penerbit,
        penerbit.id_admin,
        penerbit.id_petugas,
        admin.nama_admin,
        petugas.nama_petugas,
        COALESCE(admin.nama_admin, petugas.nama_petugas, '-') AS nama_pengelola,
        penerbit.kode_penerbit,
        penerbit.nama_penerbit

    FROM penerbit

    LEFT JOIN admin ON penerbit.id_admin = admin.id_admin
    LEFT JOIN petugas ON penerbit.id_petugas = petugas.id_petugas
");

// Mengubah hasil query menjadi array
$result = $query->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>
<link rel="stylesheet" href="../style.css">

    <meta charset="UTF-8">

    <title>Data Penerbit</title>

</head>

<body>
<div class="page-head"><div></div><a class="btn btn-light" href="../dashboard.<?= $_SESSION['role'] ?>.php">← Kembali ke Dashboard</a></div>

    <h1>PENERBIT</h1>

    <a href="form.penerbit.php">
        + Tambah Data
    </a>

    <br><br>

    <table border="1" cellpadding="8" cellspacing="0">

        <thead>

            <tr>

                <th>ID PENERBIT</th>
                <th>ID ADMIN</th>
                <th>PENGELOLA</th>
                <th>KODE PENERBIT</th>
                <th>NAMA PENERBIT</th>
                <th>AKSI</th>

            </tr>

        </thead>

        <tbody>

            <?php foreach ($result as $p): ?>

                <tr>

                    <td>
                        <?= $p['id_penerbit']; ?>
                    </td>

                    <td>
                        <?= $p['id_admin']; ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($p['nama_pengelola']); ?>
                    </td>

                    <td>
                        <?= $p['kode_penerbit']; ?>
                    </td>

                    <td>
                        <?= $p['nama_penerbit']; ?>
                    </td>

                    <td>

                        <a href="edit.penerbit.php?id_penerbit=<?= $p['id_penerbit']; ?>">
                            Edit
                        </a>

                        |

                        <a
                            href="hapus.penerbit.php?id_penerbit=<?= $p['id_penerbit']; ?>"
                            onclick="return confirm('Yakin ingin menghapus data ini?')"
                        >
                            Hapus
                        </a>

                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

    <br>

    <div>
        DEWI SRI RAHAYU
    </div>

</body>

</html>