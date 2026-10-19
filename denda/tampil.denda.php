<?php
require_once '../auth.php';
require_login();
include '../connection.php';

$role = role();

if (!in_array($role, ['admin', 'petugas', 'anggota'], true)) {
    go_home(dashboard_file());
}

if ($role === 'anggota') {
    $stmt = $conn->prepare("
        SELECT d.id_denda, d.id_peminjaman, d.id_pengembalian,
               d.jenis_denda, d.terlambat, d.tarif_per_hari, d.total_denda, d.id_admin, d.id_petugas,
               p.tanggal_pinjam, p.tanggal_jatuh_tempo,
               pg.tanggal_kembali, a.nama_anggota, b.judul,
               ad.nama_admin, pt.nama_petugas
        FROM denda d
        INNER JOIN peminjaman p ON d.id_peminjaman = p.id_peminjaman
        INNER JOIN pengembalian pg ON d.id_pengembalian = pg.id_pengembalian
        INNER JOIN anggota a ON p.id_anggota = a.id_anggota
        INNER JOIN buku b ON p.id_buku = b.id_buku
        LEFT JOIN admin ad ON d.id_admin=ad.id_admin
        LEFT JOIN petugas pt ON d.id_petugas=pt.id_petugas
        WHERE p.id_anggota = ?
        ORDER BY d.id_denda DESC
    ");
    $stmt->bind_param('i', $_SESSION['id_anggota']);
    $stmt->execute();
    $data = $stmt->get_result();
} else {
    $data = $conn->query("
        SELECT d.id_denda, d.id_peminjaman, d.id_pengembalian,
               d.jenis_denda, d.terlambat, d.tarif_per_hari, d.total_denda, d.id_admin, d.id_petugas,
               p.tanggal_pinjam, p.tanggal_jatuh_tempo,
               pg.tanggal_kembali, a.nama_anggota, b.judul,
               ad.nama_admin, pt.nama_petugas
        FROM denda d
        INNER JOIN peminjaman p ON d.id_peminjaman = p.id_peminjaman
        INNER JOIN pengembalian pg ON d.id_pengembalian = pg.id_pengembalian
        INNER JOIN anggota a ON p.id_anggota = a.id_anggota
        INNER JOIN buku b ON p.id_buku = b.id_buku
        LEFT JOIN admin ad ON d.id_admin=ad.id_admin
        LEFT JOIN petugas pt ON d.id_petugas=pt.id_petugas
        ORDER BY d.id_denda DESC
    ");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Data Denda</title>
<link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="page-head">
    <div>
        <h1>Data Denda</h1>
        <p class="muted">
            <?php if ($role === 'anggota'): ?>
                Anda hanya dapat melihat denda milik akun Anda.
            <?php else: ?>
                Denda dibuat otomatis oleh sistem saat Petugas memproses pengembalian.
            <?php endif; ?>
        </p>
        <span class="badge-role"><?= htmlspecialchars(strtoupper($role)) ?></span>
    </div>
    <a class="btn btn-light" href="../<?= htmlspecialchars(dashboard_file()) ?>">← Kembali ke Dashboard</a>
</div>

<?php if (isset($_GET['pesan'])): ?>
<div class="info"><?= htmlspecialchars($_GET['pesan']) ?></div>
<?php endif; ?>

<div class="denda-toolbar">
    <div><strong>Total Data:</strong> <?= (int)$data->num_rows ?></div>
    <?php if ($role === 'petugas'): ?><a class="btn btn-success" href="../pengembalian/form.pengembalian.php">↩ Proses Pengembalian</a><?php endif; ?>
</div>

<div class="table-wrap">
<table>
<thead>
<tr>
    <th>ID</th>
    <th>Peminjaman</th>
    <th>Anggota</th>
    <th>Buku</th>
    <th>Jatuh Tempo</th>
    <th>Tanggal Kembali</th>
    <th>Jenis Denda</th>
    <th>Jumlah Hari</th>
    <th>Tarif/Hari</th>
    <th>Total Denda</th>
    <?php if ($role !== 'anggota'): ?><th>Diproses Oleh</th><?php endif; ?>
</tr>
</thead>
<tbody>
<?php if ($data->num_rows === 0): ?>
<tr><td colspan="<?= $role !== 'anggota' ? 11 : 10 ?>" class="empty">Belum ada data denda.</td></tr>
<?php else: ?>
<?php while ($r = $data->fetch_assoc()): ?>
<tr>
    <td><?= (int)$r['id_denda'] ?></td>
    <td>#<?= (int)$r['id_peminjaman'] ?></td>
    <td><?= htmlspecialchars($r['nama_anggota']) ?></td>
    <td><?= htmlspecialchars($r['judul']) ?></td>
    <td><?= htmlspecialchars($r['tanggal_jatuh_tempo'] ?? '-') ?></td>
    <td><?= htmlspecialchars($r['tanggal_kembali'] ?? '-') ?></td>
    <td><?= htmlspecialchars(ucfirst($r['jenis_denda'] ?? 'terlambat')) ?></td>
    <td><?= (int)$r['terlambat'] ?> hari</td>
    <td>Rp <?= number_format((int)$r['tarif_per_hari'],0,',','.') ?></td>
    <td><strong>Rp <?= number_format((int)$r['total_denda'],0,',','.') ?></strong></td>
    <?php if ($role !== 'anggota'): ?><td><?= htmlspecialchars($r['nama_petugas'] ? 'Petugas — '.$r['nama_petugas'] : ($r['nama_admin'] ? 'Admin — '.$r['nama_admin'] : '-')) ?></td><?php endif; ?>
</tr>
<?php endwhile; ?>
<?php endif; ?>
</tbody>
</table>
</div>
</body>
</html>
