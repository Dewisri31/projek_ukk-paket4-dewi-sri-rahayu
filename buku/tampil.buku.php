<?php

require_once '../auth.php';
require_login();

include('../connection.php');


/*
|--------------------------------------------------------------------------
| Ambil data buku
|--------------------------------------------------------------------------
*/

$query = $conn->query("
    SELECT
        buku.id_buku,
        buku.id_admin,
        admin.nama_admin,
        buku.judul,
        buku.id_kategori,
        kategori.nama_kategori,
        buku.gambar,
        buku.sinopsis

    FROM buku

    LEFT JOIN admin
        ON buku.id_admin = admin.id_admin

    LEFT JOIN kategori
        ON buku.id_kategori = kategori.id_kategori

    ORDER BY buku.id_buku DESC
");

if (!$query) {

    die(
        "Gagal mengambil data buku: " .
        htmlspecialchars($conn->error)
    );

}

$result = $query->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <link
        rel="stylesheet"
        href="../style.css"
    >

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Data Buku</title>


    <style>

        /*
        |--------------------------------------------------------------------------
        | TETAP UKURAN KOLOM SEPERTI SEBELUMNYA
        | TAPI GAMBAR TIDAK DI-CROP
        |--------------------------------------------------------------------------
        */

        .book-image {

            width: 100px;
            height: 120px;

            object-fit: contain;
            object-position: center;

            display: block;

            background: #f5f5f5;

            border-radius: 6px;

        }

    </style>

</head>

<body>


<h1>DATA BUKU</h1>


<?php if ($_SESSION['role'] !== 'anggota'): ?>

    <a
        class="btn"
        href="form.buku.php"
    >
        + Tambah Data
    </a>

<?php endif; ?>


<a
    class="btn btn-light"
    href="../dashboard.<?= htmlspecialchars(
        $_SESSION['role'],
        ENT_QUOTES,
        'UTF-8'
    ); ?>.php"
>
    ← Kembali ke Dashboard
</a>


<br><br>


<?php if (isset($_GET['updated'])): ?>

    <div class="success-message">
        Data buku berhasil diperbarui.
    </div>

<?php endif; ?>


<table
    border="1"
    cellpadding="8"
    cellspacing="0"
>

    <thead>

        <tr>

            <th>ID BUKU</th>

            <th>PENGELOLA</th>

            <th>JUDUL</th>

            <th>KATEGORI</th>

            <th>GAMBAR</th>

            <th>SINOPSIS</th>

            <th>AKSI</th>

        </tr>

    </thead>


    <tbody>


        <?php foreach ($result as $b): ?>

            <tr>


                <td>

                    <?= (int) $b['id_buku']; ?>

                </td>


                <td>

                    <?= htmlspecialchars(
                        $b['nama_admin'] ?? '-',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </td>


                <td>

                    <?= htmlspecialchars(
                        $b['judul'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </td>


                <td>

                    <?= htmlspecialchars(
                        $b['nama_kategori'] ?? '-',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </td>


                <td>

                    <?php if (!empty($b['gambar'])): ?>

                        <img
                            class="book-image"
                            src="gambar.php?id_buku=<?= (int) $b['id_buku']; ?>"
                            alt="<?= htmlspecialchars(
                                $b['judul'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                        >

                    <?php else: ?>

                        Tidak ada gambar

                    <?php endif; ?>

                </td>


                <td>

                    <?= nl2br(
                        htmlspecialchars(
                            $b['sinopsis'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ); ?>

                </td>


                <td>


                    <?php if ($_SESSION['role'] !== 'anggota'): ?>


                        <a
                            class="btn-light"
                            href="detail.buku.php?id_buku=<?= (int) $b['id_buku']; ?>"
                        >
                            🔎 Detail
                        </a>


                        <a
                            href="edit.buku.php?id_buku=<?= (int) $b['id_buku']; ?>"
                        >
                            ✏ Edit
                        </a>


                        <a
                            class="btn-danger"
                            href="hapus.buku.php?id_buku=<?= (int) $b['id_buku']; ?>"
                            onclick="return confirm('Yakin ingin menghapus data ini?')"
                        >
                            🗑 Hapus
                        </a>


                    <?php else: ?>


                        <a
                            class="btn"
                            href="../peminjaman/form.pengajuan.php?id_buku=<?= (int) $b['id_buku']; ?>"
                        >
                            📚 Pinjam
                        </a>


                        <a
                            class="btn-light"
                            href="detail.buku.php?id_buku=<?= (int) $b['id_buku']; ?>"
                        >
                            🔎 Detail
                        </a>


                    <?php endif; ?>


                </td>


            </tr>

        <?php endforeach; ?>


    </tbody>

</table>


</body>

</html>