<?php
require_once '../auth.php'; require_staff();
?>
<?php

// Menghubungkan dengan database
include("../connection.php");

// Mengambil data anggota dan nama admin
$query = $conn->query("
    SELECT
        anggota.id_anggota,
        anggota.id_admin,
        anggota.nama_anggota,
        anggota.jenis_kelamin,
        anggota.tempat_lahir,
        anggota.tanggal_lahir,
        anggota.telpon,
        anggota.alamat,
        admin.nama_admin
    FROM anggota
    LEFT JOIN admin
        ON anggota.id_admin = admin.id_admin
");

// Mengubah hasil query menjadi array
$result = $query->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>
<link rel="stylesheet" href="../style.css">
    <meta charset="UTF-8">
    <title>Data Anggota</title>
</head>

<body>
<div class="page-head"><div></div><a class="btn btn-light" href="../dashboard.<?= $_SESSION['role'] ?>.php">← Kembali ke Dashboard</a></div>

    <h1>DATA ANGGOTA</h1>

    <?php if (($_GET['hapus'] ?? '') === 'terkait'): ?>
        <div class="error action-message">
            Anggota tidak dapat dihapus karena sudah memiliki riwayat peminjaman.
            Data dipertahankan agar histori transaksi dan relasi database tetap aman.
        </div>
    <?php elseif (($_GET['hapus'] ?? '') === 'berhasil'): ?>
        <div class="success action-message">Data anggota berhasil dihapus.</div>
    <?php elseif (($_GET['hapus'] ?? '') === 'gagal'): ?>
        <div class="error action-message">Penghapusan gagal. Silakan periksa relasi data anggota.</div>
    <?php elseif (($_GET['hapus'] ?? '') === 'invalid'): ?>
        <div class="error action-message">ID anggota tidak valid.</div>
    <?php endif; ?>

    <a href="form.anggota.html">
        + Tambah Data
    </a>

    <br><br>

    <table border="1" cellpadding="8" cellspacing="0">

        <thead>
            <tr>
                <th>ID ANGGOTA</th>
                <th>ID ADMIN</th>
                <th>NAMA ADMIN</th>
                <th>NAMA ANGGOTA</th>
                <th>JENIS KELAMIN</th>
                <th>TEMPAT LAHIR</th>
                <th>TANGGAL LAHIR</th>
                <th>TELPON</th>
                <th>ALAMAT</th>
                <th>AKSI</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach ($result as $a): ?>

                <tr>

                    <td>
                        <?= $a['id_anggota']; ?>
                    </td>

                    <td>
                        <?= $a['id_admin']; ?>
                    </td>

                    <td>
                        <?= $a['nama_admin']; ?>
                    </td>

                    <td>
                        <?= $a['nama_anggota']; ?>
                    </td>

                    <td>
                        <?= $a['jenis_kelamin']; ?>
                    </td>

                    <td>
                        <?= $a['tempat_lahir']; ?>
                    </td>

                    <td>
                        <?= $a['tanggal_lahir']; ?>
                    </td>

                    <td>
                        <?= $a['telpon']; ?>
                    </td>

                    <td>
                        <?= $a['alamat']; ?>
                    </td>

                    <td>

                        <a href="edit.anggota.php?id_anggota=<?= $a['id_anggota']; ?>">
                            Edit
                        </a>

                        |

                        <a
                            href="hapus.anggota.php?id_anggota=<?= $a['id_anggota']; ?>"
                            onclick="return confirm('Yakin ingin menghapus data ini?')"
                        >
                            Hapus
                        </a>

                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

</body>

</html>