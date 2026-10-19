<?php

require_once '../auth.php';
require_login();

include '../connection.php';

$role = $_SESSION['role'] ?? '';

/*
|--------------------------------------------------------------------------
| AKSES
|--------------------------------------------------------------------------
| Histori hanya bisa dilihat oleh:
| - Anggota
| - Petugas
|--------------------------------------------------------------------------
*/

if (!in_array($role, ['anggota', 'petugas'], true)) {
    go_home('dashboard.admin.php');
}


/*
|--------------------------------------------------------------------------
| SIAPKAN DATA
|--------------------------------------------------------------------------
*/

$result = [];


/*
|--------------------------------------------------------------------------
| HISTORI ANGGOTA
|--------------------------------------------------------------------------
| Anggota hanya boleh melihat histori miliknya sendiri.
|--------------------------------------------------------------------------
*/

if ($role === 'anggota') {

    $id_anggota = (int) ($_SESSION['id_anggota'] ?? 0);

    if ($id_anggota > 0) {

        $sql = "
            SELECT
                h.id_detail,
                h.id_admin,
                ad.nama_admin,
                h.id_peminjaman,
                h.id_buku,
                b.judul,
                h.jumlah,
                h.denda
            FROM histori h

            INNER JOIN peminjaman p
                ON p.id_peminjaman = h.id_peminjaman

            LEFT JOIN admin ad
                ON ad.id_admin = h.id_admin

            LEFT JOIN buku b
                ON b.id_buku = h.id_buku

            WHERE p.id_anggota = ?

            ORDER BY h.id_detail DESC
        ";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "i",
                $id_anggota
            );

            mysqli_stmt_execute($stmt);

            $query = mysqli_stmt_get_result($stmt);

            if ($query) {
                while ($row = mysqli_fetch_assoc($query)) {
                    $result[] = $row;
                }
            }

            mysqli_stmt_close($stmt);
        }

    }


/*
|--------------------------------------------------------------------------
| HISTORI PETUGAS
|--------------------------------------------------------------------------
| Petugas dapat melihat seluruh histori.
|--------------------------------------------------------------------------
*/

} elseif ($role === 'petugas') {

    $sql = "
        SELECT
            h.id_detail,
            h.id_admin,
            ad.nama_admin,
            h.id_peminjaman,
            h.id_buku,
            b.judul,
            h.jumlah,
            h.denda
        FROM histori h

        LEFT JOIN admin ad
            ON ad.id_admin = h.id_admin

        LEFT JOIN buku b
            ON b.id_buku = h.id_buku

        ORDER BY h.id_detail DESC
    ";

    $query = mysqli_query($conn, $sql);

    if ($query) {

        while ($row = mysqli_fetch_assoc($query)) {
            $result[] = $row;
        }

    }

}


/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

$dashboard = "../dashboard." . $role . ".php";

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Histori Peminjaman</title>

    <link
        rel="stylesheet"
        href="../style.css"
    >

</head>


<body>

<div class="container">

    <!-- =========================================================
         HEADER
    ========================================================== -->

    <div class="page-head">

        <div>

            <h1>
                Histori
                <?= $role === 'anggota' ? 'Saya' : 'Peminjaman' ?>
            </h1>

            <p class="muted">
                Riwayat peminjaman yang tersimpan secara otomatis.
            </p>

        </div>


        <a
            href="<?= htmlspecialchars($dashboard) ?>"
            class="btn"
        >
            ← Kembali ke Dashboard
        </a>

    </div>


    <!-- =========================================================
         CARD HISTORI
    ========================================================== -->

    <div class="card">

        <div class="card-head">

            <div>

                <h2>
                    Data Histori
                </h2>

                <p class="muted">
                    <?= count($result) ?> data histori
                </p>

            </div>

        </div>


        <!-- =====================================================
             TABLE
        ====================================================== -->

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>

                        <th>ID Detail</th>

                        <?php if ($role === 'petugas'): ?>

                            <th>ID Admin</th>

                            <th>Nama Admin</th>

                        <?php endif; ?>

                        <th>ID Peminjaman</th>

                        <th>ID Buku</th>

                        <th>Judul Buku</th>

                        <th>Jumlah</th>

                        <th>Denda</th>

                        <th>Status</th>

                    </tr>

                </thead>


                <tbody>

                <?php if (empty($result)): ?>

                    <tr>

                        <td
                            colspan="<?= $role === 'petugas' ? '9' : '7' ?>"
                            class="text-center"
                        >

                            <div class="empty-state">

                                <h3>
                                    Belum Ada Histori
                                </h3>

                                <p class="muted">
                                    Histori peminjaman akan muncul
                                    otomatis setelah proses peminjaman.
                                </p>

                            </div>

                        </td>

                    </tr>

                <?php else: ?>


                    <?php foreach ($result as $h): ?>

                        <tr>

                            <!-- ID DETAIL -->

                            <td>
                                <?= htmlspecialchars(
                                    (string)($h['id_detail'] ?? '-')
                                ) ?>
                            </td>


                            <!-- ADMIN -->

                            <?php if ($role === 'petugas'): ?>

                                <td>
                                    <?= htmlspecialchars(
                                        (string)($h['id_admin'] ?? '-')
                                    ) ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $h['nama_admin'] ?? '-'
                                    ) ?>
                                </td>

                            <?php endif; ?>


                            <!-- PEMINJAMAN -->

                            <td>
                                #<?= htmlspecialchars(
                                    (string)($h['id_peminjaman'] ?? '-')
                                ) ?>
                            </td>


                            <!-- BUKU -->

                            <td>
                                <?= htmlspecialchars(
                                    (string)($h['id_buku'] ?? '-')
                                ) ?>
                            </td>


                            <!-- JUDUL -->

                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        $h['judul'] ?? 'Buku tidak ditemukan'
                                    ) ?>
                                </strong>

                            </td>


                            <!-- JUMLAH -->

                            <td>
                                <?= htmlspecialchars(
                                    (string)($h['jumlah'] ?? '0')
                                ) ?>
                            </td>


                            <!-- DENDA -->

                            <td>

                                <?php
                                $denda = (int)($h['denda'] ?? 0);
                                ?>

                                <?php if ($denda > 0): ?>

                                    <span class="badge badge-danger">

                                        Rp
                                        <?= number_format(
                                            $denda,
                                            0,
                                            ',',
                                            '.'
                                        ) ?>

                                    </span>

                                <?php else: ?>

                                    <span class="badge badge-success">
                                        Tidak Ada Denda
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <span class="badge badge-success">
                                    Selesai
                                </span>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>


    <!-- =========================================================
         FOOTER
    ========================================================== -->

    <div class="footer">

        DEWI SRI RAHAYU

    </div>

</div>


</body>

</html>