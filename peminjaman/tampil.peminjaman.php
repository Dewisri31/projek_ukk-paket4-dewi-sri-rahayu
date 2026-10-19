<?php
/**
 * =========================================================
 * DATA PEMINJAMAN
 * =========================================================
 * Role:
 * - Admin    : tidak dapat mengakses halaman ini
 * - Petugas  : melihat dan mengelola peminjaman
 * - Anggota  : hanya melihat status peminjaman miliknya
 * =========================================================
 */

require_once '../auth.php';
require_login();
include '../connection.php';

$role = $_SESSION['role'] ?? '';

/*
|--------------------------------------------------------------------------
| Admin tidak menggunakan halaman peminjaman
|--------------------------------------------------------------------------
*/
if (!in_array($role, ['admin', 'petugas', 'anggota'], true)) {
    go_home('login.php');
}


/*
|--------------------------------------------------------------------------
| QUERY DATA PEMINJAMAN
|--------------------------------------------------------------------------
| Sengaja TIDAK menggunakan:
| - pengembalian.id_petugas
| - JOIN petugas dari tabel pengembalian
|
| Supaya kompatibel dengan database yang belum mempunyai
| kolom id_petugas pada tabel pengembalian.
|--------------------------------------------------------------------------
*/

if ($role === 'anggota') {

    /*
    |--------------------------------------------------------------------------
    | ANGGOTA
    |--------------------------------------------------------------------------
    | Hanya melihat peminjaman miliknya sendiri.
    */
    $id_anggota = (int)($_SESSION['id_anggota'] ?? 0);

    if ($id_anggota <= 0) {
        die('ID Anggota tidak ditemukan pada session. Silakan logout dan login kembali.');
    }

    $sql = "
        SELECT
            p.id_peminjaman,
            p.id_anggota,
            p.id_petugas,
            p.id_admin,
            p.id_buku,
            p.tanggal_pinjam,
            p.tanggal_jatuh_tempo,
            p.status,
            p.alasan_penolakan,

            a.nama_anggota,

            b.judul,

            pt.nama_petugas,

            ad.nama_admin

        FROM peminjaman p

        LEFT JOIN anggota a
            ON p.id_anggota = a.id_anggota

        LEFT JOIN buku b
            ON p.id_buku = b.id_buku

        LEFT JOIN petugas pt
            ON p.id_petugas = pt.id_petugas

        LEFT JOIN admin ad
            ON p.id_admin = ad.id_admin

        WHERE p.id_anggota = ?

        ORDER BY p.id_peminjaman DESC
    ";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die(
            'Gagal menyiapkan query peminjaman anggota.<br>' .
            'Error MySQL: ' .
            htmlspecialchars($conn->error)
        );
    }

    $stmt->bind_param('i', $id_anggota);

    if (!$stmt->execute()) {
        die(
            'Gagal mengambil data peminjaman.<br>' .
            'Error MySQL: ' .
            htmlspecialchars($stmt->error)
        );
    }

    $data = $stmt->get_result();

} else {

    /*
    |--------------------------------------------------------------------------
    | ADMIN / PETUGAS
    |--------------------------------------------------------------------------
    | Admin dan Petugas melihat seluruh data peminjaman.
    */
    $sql = "
        SELECT
            p.id_peminjaman,
            p.id_anggota,
            p.id_petugas,
            p.id_admin,
            p.id_buku,
            p.tanggal_pinjam,
            p.tanggal_jatuh_tempo,
            p.status,
            p.alasan_penolakan,

            a.nama_anggota,

            b.judul,

            pt.nama_petugas,

            ad.nama_admin

        FROM peminjaman p

        LEFT JOIN anggota a
            ON p.id_anggota = a.id_anggota

        LEFT JOIN buku b
            ON p.id_buku = b.id_buku

        LEFT JOIN petugas pt
            ON p.id_petugas = pt.id_petugas

        LEFT JOIN admin ad
            ON p.id_admin = ad.id_admin

        ORDER BY p.id_peminjaman DESC
    ";

    $data = $conn->query($sql);

    if ($data === false) {
        die(
            'Gagal mengambil data peminjaman.<br>' .
            'Error MySQL: ' .
            htmlspecialchars($conn->error)
        );
    }
}


/*
|--------------------------------------------------------------------------
| FUNGSI STATUS
|--------------------------------------------------------------------------
*/
function badge_status(string $status): string
{
    $status = strtolower(trim($status));

    $map = [
        'menunggu' => 'badge-warning',
        'dipinjam' => 'badge-info',
        'selesai'  => 'badge-success',
        'ditolak'  => 'badge-danger'
    ];

    $class = $map[$status] ?? 'badge-info';

    /*
    | Tulisan yang ditampilkan lebih mudah dipahami.
    */
    $label = [
        'menunggu' => 'MENUNGGU PERSETUJUAN',
        'dipinjam' => 'SEDANG DIPINJAM',
        'selesai'  => 'SELESAI / SUDAH DIKEMBALIKAN',
        'ditolak'  => 'DITOLAK'
    ];

    $text = $label[$status] ?? strtoupper($status);

    return '<span class="badge ' . $class . '">' .
        htmlspecialchars($text) .
        '</span>';
}


/*
|--------------------------------------------------------------------------
| JUMLAH DATA
|--------------------------------------------------------------------------
*/
$total_data = $data->num_rows;

?>
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= $role === 'anggota'
            ? 'Status Peminjaman Saya'
            : 'Data Peminjaman'
        ?>
    </title>

    <link
        rel="stylesheet"
        href="../style.css"
    >

</head>

<body>


<!-- =========================================================
     HEADER
========================================================= -->

<div class="page-head">

    <div>

        <h1>
            <?= $role === 'anggota'
                ? 'Status Peminjaman Saya'
                : 'Data Peminjaman'
            ?>
        </h1>

        <?php if ($role === 'anggota'): ?>

            <p class="muted">
                Lihat status pengajuan dan peminjaman buku Anda.
            </p>

        <?php else: ?>

            <p class="muted">
                Kelola proses peminjaman buku anggota.
            </p>

        <?php endif; ?>

    </div>


    <a
        class="btn btn-light"
        href="../dashboard.<?= htmlspecialchars($role) ?>.php"
    >
        ← Kembali ke Dashboard
    </a>

</div>


<!-- =========================================================
     CONTAINER
========================================================= -->

<div class="container">


    <?php if (isset($_GET['pesan'])): ?>

        <div class="success">
            <?= htmlspecialchars($_GET['pesan']) ?>
        </div>

    <?php endif; ?>


    <!-- =====================================================
         INFORMASI UNTUK ANGGOTA
    ====================================================== -->

    <?php if ($role === 'anggota'): ?>

        <div class="info">

            <strong>Status Peminjaman:</strong>

            <br>

            <span>
                Menunggu = pengajuan sedang diproses.
            </span>

            <br>

            <span>
                Sedang Dipinjam = peminjaman sudah disetujui.
            </span>

            <br>

            <span>
                Selesai / Sudah Dikembalikan = buku sudah dikembalikan.
            </span>

        </div>


        <div class="page-actions">

            <a
                class="btn"
                href="form.pengajuan.php"
            >
                + Ajukan Peminjaman
            </a>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         AKSI PETUGAS
    ====================================================== -->

    <?php if (in_array($role, ['admin', 'petugas'], true)): ?>

        <div class="page-actions">

            <a
                class="btn"
                href="form.peminjaman.php"
            >
                + Tambah Peminjaman Manual
            </a>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         TABEL
    ====================================================== -->

    <div class="table-wrap">

        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Anggota</th>

                    <th>Buku</th>

                    <th>Tanggal Pinjam</th>

                    <th>Jatuh Tempo</th>

                    <th>Status</th>

                    <th>Petugas</th>

                    <?php if (in_array($role, ['admin', 'petugas'], true)): ?>

                        <th>Aksi</th>

                    <?php endif; ?>

                </tr>

            </thead>


            <tbody>


            <?php if ($total_data === 0): ?>

                <tr>

                    <td
                        colspan="<?= $role === 'petugas' ? '8' : '7' ?>"
                        class="empty-state"
                    >
                        Belum ada data peminjaman.
                    </td>

                </tr>


            <?php else: ?>


                <?php while ($r = $data->fetch_assoc()): ?>

                    <tr>


                        <!-- ID -->

                        <td>

                            <?= (int)$r['id_peminjaman'] ?>

                        </td>


                        <!-- ANGGOTA -->

                        <td>

                            <?= htmlspecialchars(
                                $r['nama_anggota'] ?? '-'
                            ) ?>

                        </td>


                        <!-- BUKU -->

                        <td>

                            <?= htmlspecialchars(
                                $r['judul'] ?? '-'
                            ) ?>

                        </td>


                        <!-- TANGGAL PINJAM -->

                        <td>

                            <?= htmlspecialchars(
                                $r['tanggal_pinjam'] ?? '-'
                            ) ?>

                        </td>


                        <!-- JATUH TEMPO -->

                        <td>

                            <?= !empty($r['tanggal_jatuh_tempo'])
                                ? htmlspecialchars($r['tanggal_jatuh_tempo'])
                                : '-'
                            ?>

                        </td>


                        <!-- STATUS -->

                        <td>

                            <?= badge_status(
                                $r['status'] ?? ''
                            ) ?>


                            <?php if (
                                ($r['status'] ?? '') === 'ditolak'
                                &&
                                !empty($r['alasan_penolakan'])
                            ): ?>

                                <br>

                                <small class="muted">

                                    Alasan:
                                    <?= htmlspecialchars(
                                        $r['alasan_penolakan']
                                    ) ?>

                                </small>

                            <?php endif; ?>


                        </td>


                        <!-- PETUGAS -->

                        <td>

                            <?php

                            if (!empty($r['nama_petugas'])) {

                                echo htmlspecialchars(
                                    $r['nama_petugas']
                                );

                            } elseif (($r['status'] ?? '') === 'menunggu') {

                                echo 'Menunggu';

                            } else {

                                echo '-';

                            }

                            ?>

                        </td>


                        <!-- AKSI PETUGAS -->

                        <?php if (in_array($role, ['admin', 'petugas'], true)): ?>

                            <td>

                                <div class="action-group">


                                    <!-- ACC / TOLAK -->

                                    <?php if (
                                        $role === 'petugas' && ($r['status'] ?? '') === 'menunggu'
                                    ): ?>

                                        <a
                                            class="btn btn-warning btn-sm"
                                            href="approval.peminjaman.php?id=<?= (int)$r['id_peminjaman'] ?>"
                                        >
                                            ACC / Tolak
                                        </a>

                                    <?php endif; ?>


                                    <!-- KEMBALIKAN -->

                                    <?php if (
                                        $role === 'petugas' && ($r['status'] ?? '') === 'dipinjam'
                                    ): ?>

                                        <a
                                            class="btn btn-sm"
                                            href="../pengembalian/form.pengembalian.php?id_peminjaman=<?= (int)$r['id_peminjaman'] ?>"
                                        >
                                            ↩ Kembalikan
                                        </a>

                                    <?php endif; ?>


                                    <!-- EDIT -->

                                    <?php if (
                                        $role === 'petugas' &&
                                        ($r['status'] ?? '') !== 'selesai'
                                        &&
                                        ($r['status'] ?? '') !== 'menunggu'
                                    ): ?>

                                        <a
                                            class="btn btn-light btn-sm"
                                            href="edit.peminjaman.php?id=<?= (int)$r['id_peminjaman'] ?>"
                                        >
                                            Edit
                                        </a>

                                    <?php endif; ?>


                                    <!-- HAPUS -->

                                    <?php if (
                                        $role === 'petugas' &&
                                        ($r['status'] ?? '') !== 'dipinjam'
                                        &&
                                        ($r['status'] ?? '') !== 'menunggu'
                                    ): ?>

                                        <a
                                            class="btn btn-danger btn-sm"
                                            href="hapus.peminjaman.php?id=<?= (int)$r['id_peminjaman'] ?>"
                                            onclick="return confirm('Yakin ingin menghapus data peminjaman ini?')"
                                        >
                                            Hapus
                                        </a>

                                    <?php endif; ?>


                                </div>

                            </td>

                        <?php endif; ?>


                    </tr>

                <?php endwhile; ?>


            <?php endif; ?>


            </tbody>

        </table>

    </div>


</div>


</body>

</html>