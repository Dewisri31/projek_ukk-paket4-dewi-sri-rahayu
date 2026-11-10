<?php
require_once '../auth.php'; require_detail_petugas();
?>
<?php

// Menghubungkan ke database
include("../connection.php");

// Mengambil data detail buku
// sekaligus mengambil data dari tabel yang berelasi
$query = $conn->query("
    SELECT
        detail_buku.id_detail,
        detail_buku.id_admin,
        detail_buku.id_petugas,
        admin.nama_admin,
        petugas.nama_petugas,
        COALESCE(admin.nama_admin, petugas.nama_petugas, '-') AS nama_pengelola,
        detail_buku.id_buku,
        buku.judul,
        detail_buku.id_kategori,
        kategori.nama_kategori,
        detail_buku.id_penerbit,
        penerbit.nama_penerbit,
        detail_buku.pengarang,
        detail_buku.tahun_terbit,
        detail_buku.jumlah_halaman,
        detail_buku.jumlah_stok,
        detail_buku.isbn

    FROM detail_buku

    LEFT JOIN admin ON detail_buku.id_admin = admin.id_admin
    LEFT JOIN petugas ON detail_buku.id_petugas = petugas.id_petugas

    LEFT JOIN buku
        ON detail_buku.id_buku = buku.id_buku

    LEFT JOIN kategori
        ON detail_buku.id_kategori = kategori.id_kategori

    LEFT JOIN penerbit
        ON detail_buku.id_penerbit = penerbit.id_penerbit
");

// Mengubah hasil query menjadi array
$result = $query->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>
<link rel="stylesheet" href="../style.css">

    <meta charset="UTF-8">

    <title>Detail Buku</title>

</head>

<body>
<div class="page-head"><div></div><a class="btn btn-light" href="../dashboard.<?= $_SESSION['role'] ?>.php">← Kembali ke Dashboard</a></div>

    <h1>DETAIL BUKU</h1>
    <p class="muted">Admin dapat mengelola detail buku. Petugas hanya memiliki akses melihat data.</p>
    <?php if (isset($_GET['updated'])): ?>
        <div class="success">Detail buku berhasil diperbarui.</div>
    <?php endif; ?>
    <?php if (isset($_GET['pesan'])): ?>
        <div class="success"><?= htmlspecialchars($_GET['pesan']) ?></div>
    <?php endif; ?>

    <?php if (role() === 'admin'): ?>
        <a class="btn" href="form.detail.php">+ Tambah Detail Buku</a>
    <?php endif; ?>

    <br><br>

    <table border="1" cellpadding="8" cellspacing="0">

        <thead>

            <tr>

                <th>ID DETAIL</th>
                <th>PENGELOLA</th>
                <th>BUKU</th>
                <th>KATEGORI</th>
                <th>PENERBIT</th>
                <th>PENGARANG</th>
                <th>TAHUN TERBIT</th>
                <th>JUMLAH HALAMAN</th>
                <th>JUMLAH STOK</th>
                <th>ISBN</th>
                <th>AKSI</th>

            </tr>

        </thead>

        <tbody>

            <?php foreach ($result as $d): ?>

                <tr>

                    <td>
                        <?= $d['id_detail']; ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($d['nama_pengelola']); ?>
                    </td>

                    <td>
                        <?= $d['judul']; ?>
                    </td>

                    <td>
                        <?= $d['nama_kategori']; ?>
                    </td>

                    <td>
                        <?= $d['nama_penerbit']; ?>
                    </td>

                    <td>
                        <?= $d['pengarang']; ?>
                    </td>

                    <td>
                        <?= $d['tahun_terbit']; ?>
                    </td>

                    <td>
                        <?= $d['jumlah_halaman']; ?>
                    </td>

                    <td>
                        <?= $d['jumlah_stok']; ?>
                    </td>

                    <td>
                        <?= $d['isbn']; ?>
                    </td>

                    <td>
                        <?php if (role() === 'admin'): ?>
                            <a class="btn btn-sm" href="edit.detail.php?id_detail=<?= (int)$d['id_detail']; ?>" style="display:inline-block;margin:2px 4px 2px 0;">✏ Edit Detail</a>
                            <a class="btn btn-sm btn-danger" href="hapus.detail.php?id_detail=<?= (int)$d['id_detail']; ?>"
                               onclick="return confirm('Yakin ingin menghapus detail buku ini?')">Hapus</a>
                        <?php else: ?>
                            <span class="muted">Hanya lihat</span>
                        <?php endif; ?>
                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

</body>

</html>