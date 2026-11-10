<?php

require_once '../auth.php';
require_login();

include '../connection.php';


$id = (int) (
    $_GET['id_buku'] ?? 0
);


if ($id <= 0) {

    header(
        'Location: tampil.buku.php'
    );

    exit;

}


$stmt = $conn->prepare("
    SELECT
        b.*,

        a.nama_admin,

        k.nama_kategori,

        d.id_detail,
        d.pengarang,
        d.tahun_terbit,
        d.jumlah_halaman,
        d.jumlah_stok,
        d.isbn,

        pe.nama_penerbit

    FROM buku b

    LEFT JOIN admin a
        ON b.id_admin = a.id_admin

    LEFT JOIN kategori k
        ON b.id_kategori = k.id_kategori

    LEFT JOIN detail_buku d
        ON b.id_buku = d.id_buku

    LEFT JOIN penerbit pe
        ON d.id_penerbit = pe.id_penerbit

    WHERE b.id_buku = ?

    LIMIT 1
");


$stmt->bind_param(
    'i',
    $id
);

$stmt->execute();

$b =
    $stmt
        ->get_result()
        ->fetch_assoc();


$stmt->close();


if (!$b) {

    die(
        '<div class="error">
            Buku tidak ditemukan.
        </div>'
    );

}


$role =
    $_SESSION['role'] ?? '';

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Detail Buku
    </title>

    <link
        rel="stylesheet"
        href="../style.css"
    >

    <style>

        /*
        |--------------------------------------------------------------------------
        | GAMBAR TIDAK CROP
        |--------------------------------------------------------------------------
        */

        .book-cover-wrap {

            width: 100%;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 15px;

        }


        .book-cover {

            max-width: 100%;

            max-height: 500px;

            width: auto;

            height: auto;

            object-fit: contain;

            object-position: center;

            display: block;

            border-radius: 8px;

        }

    </style>

</head>


<body>


<div class="page-head">

    <div>

        <h1>

            📖
            <?= htmlspecialchars(
                $b['judul']
            ); ?>

        </h1>

        <p class="muted">
            Detail lengkap buku
        </p>

    </div>


    <a
        class="btn btn-light"
        href="tampil.buku.php"
    >
        ← Kembali ke Daftar Buku
    </a>

</div>


<div class="card detail-card">


    <?php if (!empty($b['gambar'])): ?>

        <div class="book-cover-wrap">

            <img
                class="book-cover"
                src="gambar.php?id_buku=<?= (int) $b['id_buku']; ?>"
                alt="<?= htmlspecialchars(
                    $b['judul'],
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>"
            >

        </div>

    <?php endif; ?>


    <div class="stats">


        <div class="stat">

            <small>
                ID Buku
            </small>

            <strong>
                <?= htmlspecialchars(
                    $b['id_buku']
                ); ?>
            </strong>

        </div>


        <div class="stat">

            <small>
                Kategori
            </small>

            <strong class="detail-stat">

                <?= htmlspecialchars(
                    $b['nama_kategori'] ?? '-'
                ); ?>

            </strong>

        </div>


        <div class="stat">

            <small>
                Admin
            </small>

            <strong class="detail-stat">

                <?= htmlspecialchars(
                    $b['nama_admin'] ?? '-'
                ); ?>

            </strong>

        </div>


        <div class="stat">

            <small>
                Stok
            </small>

            <strong>

                <?= htmlspecialchars(
                    $b['jumlah_stok'] ?? '-'
                ); ?>

            </strong>

        </div>


    </div>


    <table>

        <tr>

            <th>
                Pengarang
            </th>

            <td>
                <?= htmlspecialchars(
                    $b['pengarang'] ?? '-'
                ); ?>
            </td>


            <th>
                Penerbit
            </th>

            <td>
                <?= htmlspecialchars(
                    $b['nama_penerbit'] ?? '-'
                ); ?>
            </td>

        </tr>


        <tr>

            <th>
                Tahun Terbit
            </th>

            <td>
                <?= htmlspecialchars(
                    $b['tahun_terbit'] ?? '-'
                ); ?>
            </td>


            <th>
                Jumlah Halaman
            </th>

            <td>
                <?= htmlspecialchars(
                    $b['jumlah_halaman'] ?? '-'
                ); ?>
            </td>

        </tr>


        <tr>

            <th>
                ISBN
            </th>

            <td colspan="3">

                <?= htmlspecialchars(
                    $b['isbn'] ?? '-'
                ); ?>

            </td>

        </tr>

    </table>


    <h3 class="section-title">
        Sinopsis
    </h3>


    <p>

        <?= nl2br(
            htmlspecialchars(
                $b['sinopsis']
                ??
                'Belum ada sinopsis.'
            )
        ); ?>

    </p>


    <div class="form-actions">


        <?php if ($role === 'anggota'): ?>

            <a
                class="btn"
                href="../peminjaman/form.pengajuan.php?id_buku=<?= (int) $b['id_buku']; ?>"
            >
                📚 Pinjam Buku
            </a>

        <?php endif; ?>


        <?php if ($role === 'admin'): ?>
            <?php if (!empty($b['id_detail'])): ?>
                <a class="btn" href="../detail_buku/edit.detail.php?id_detail=<?= (int)$b['id_detail']; ?>">
                    ✏ Edit Detail Buku
                </a>
            <?php else: ?>
                <a class="btn" href="../detail_buku/form.detail.php?id_buku=<?= (int)$b['id_buku']; ?>">
                    + Tambah Detail Buku
                </a>
            <?php endif; ?>
            <a class="btn btn-light" href="edit.buku.php?id_buku=<?= (int)$b['id_buku']; ?>">
                Edit Informasi Buku
            </a>
        <?php endif; ?>


        <a
            class="btn btn-light"
            href="../dashboard.<?= htmlspecialchars(
                $role
            ); ?>.php"
        >
            ← Kembali ke Dashboard
        </a>


    </div>


</div>


</body>

</html>