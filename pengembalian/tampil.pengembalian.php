
<?php
require_once '../auth.php';
require_login();
require_once '../connection.php';

$role = role();

if (!in_array($role, ['admin', 'petugas', 'anggota'], true)) {
    go_home(dashboard_file());
}

$is_anggota = ($role === 'anggota');
$id_anggota = (int)($_SESSION['id_anggota'] ?? 0);

if ($is_anggota && $id_anggota <= 0) {
    go_home('index.php');
}

/*
|--------------------------------------------------------------------------
| Query pengembalian
|--------------------------------------------------------------------------
| Tidak menggunakan pg.id_petugas karena kolom tersebut belum tersedia.
| Nama petugas diambil dari tabel peminjaman.
*/
$sql = "
    SELECT
        pg.id_pengembalian,
        pg.id_peminjaman,
        a.nama_anggota,
        b.judul,
        p.tanggal_pinjam,
        p.tanggal_jatuh_tempo,
        p.status AS status_peminjaman,
        pg.tanggal_kembali,
        pg.terlambat,
        pg.denda,
        pg.keterangan,
        pt.nama_petugas
    FROM pengembalian pg
    INNER JOIN peminjaman p
        ON pg.id_peminjaman = p.id_peminjaman
    LEFT JOIN anggota a
        ON p.id_anggota = a.id_anggota
    LEFT JOIN buku b
        ON p.id_buku = b.id_buku
    LEFT JOIN petugas pt
        ON p.id_petugas = pt.id_petugas
";

if ($is_anggota) {
    $sql .= " WHERE p.id_anggota = ? ";
}

$sql .= " ORDER BY pg.id_pengembalian DESC";

if ($is_anggota) {
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die(
            'Gagal menyiapkan data pengembalian: ' .
            htmlspecialchars($conn->error)
        );
    }

    $stmt->bind_param('i', $id_anggota);

    if (!$stmt->execute()) {
        die(
            'Gagal mengambil data pengembalian: ' .
            htmlspecialchars($stmt->error)
        );
    }

    $data = $stmt->get_result();
} else {
    $data = $conn->query($sql);

    if (!$data) {
        die(
            'Gagal mengambil data pengembalian: ' .
            htmlspecialchars($conn->error)
        );
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= $is_anggota ? 'Pengembalian Saya' : 'Data Pengembalian' ?>
    </title>

    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="page-head">
    <div>
        <h1>
            <?= $is_anggota ? 'Pengembalian Saya' : 'Data Pengembalian' ?>
        </h1>

        <p class="muted">
            <?= $is_anggota
                ? 'Lihat status dan riwayat pengembalian buku milik Anda.'
                : 'Kelola data pengembalian buku.'
            ?>
        </p>
    </div>

    <a
        class="btn btn-light"
        href="../dashboard.<?= htmlspecialchars($role) ?>.php"
    >
        ← Kembali ke Dashboard
    </a>
</div>

<div class="container">

    <?php if ($is_anggota): ?>
        <div class="info">
            Anda hanya dapat melihat data pengembalian milik sendiri.
            Data tidak dapat ditambah, diubah, atau dihapus oleh Anggota.
        </div>
    <?php else: ?>
        <div class="page-actions">
            <a class="btn" href="form.pengembalian.php">
                + Tambah Pengembalian
            </a>
        </div>
    <?php endif; ?>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID Pengembalian</th>
                    <th>ID Peminjaman</th>
                    <th>Nama Anggota</th>
                    <th>Judul Buku</th>
                    <th>Jatuh Tempo</th>
                    <th>Tanggal Kembali</th>
                    <th>Status</th>
                    <th>Terlambat</th>
                    <th>Denda</th>
                    <th>Petugas</th>
                    <th>Keterangan</th>

                    <?php if (!$is_anggota): ?>
                        <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>

            <tbody>
            <?php if ($data && $data->num_rows > 0): ?>

                <?php while ($row = $data->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?= (int)$row['id_pengembalian'] ?>
                        </td>

                        <td>
                            <?= (int)$row['id_peminjaman'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['nama_anggota'] ?? '-') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['judul'] ?? '-') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $row['tanggal_jatuh_tempo'] ?? '-'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $row['tanggal_kembali'] ?? '-'
                            ) ?>
                        </td>

                        <td>
                            <span class="badge badge-success">
                                SELESAI
                            </span>
                        </td>

                        <td>
                            <?= (int)($row['terlambat'] ?? 0) ?> hari
                        </td>

                        <td>
                            Rp <?= number_format(
                                (int)($row['denda'] ?? 0),
                                0,
                                ',',
                                '.'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $row['nama_petugas'] ?? '-'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $row['keterangan'] ?: '-'
                            ) ?>
                        </td>

                        <?php if (!$is_anggota): ?>
                            <td>
                                <a
                                    class="btn btn-sm"
                                    href="edit.pengembalian.php?id=<?= (int)$row['id_pengembalian'] ?>"
                                >
                                    Edit
                                </a>

                                <a
                                    class="btn btn-sm btn-danger"
                                    href="hapus.pengembalian.php?id=<?= (int)$row['id_pengembalian'] ?>"
                                    onclick="return confirm('Yakin ingin menghapus data ini?')"
                                >
                                    Hapus
                                </a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>

            <?php else: ?>
                <tr>
                    <td
                        colspan="<?= $is_anggota ? 11 : 12 ?>"
                        class="empty-state"
                    >
                        Belum ada data pengembalian.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>