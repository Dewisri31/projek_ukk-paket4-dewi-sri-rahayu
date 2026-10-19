<?php
require_once '../auth.php'; require_petugas(); include '../connection.php';
$anggota=$conn->query("SELECT id_anggota,nama_anggota FROM anggota ORDER BY nama_anggota");
$buku=$conn->query("SELECT id_buku,judul FROM buku ORDER BY judul");
$today=date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Peminjaman Manual</title><link rel="stylesheet" href="../style.css"></head>
<body>
<div class="page-head"><div><h1>📚 Peminjaman Manual</h1><p class="muted">Peminjaman langsung diproses oleh Petugas.</p></div><a class="btn btn-light" href="../dashboard.petugas.php">← Kembali ke Dashboard</a></div>
<form action="tambah.peminjaman.php" method="post">
<label>Anggota</label><select name="id_anggota" required><option value="">-- Pilih Anggota --</option><?php while($r=$anggota->fetch_assoc()): ?><option value="<?=$r['id_anggota']?>"><?=htmlspecialchars($r['nama_anggota'])?></option><?php endwhile; ?></select>
<label>Buku</label><select name="id_buku" required><option value="">-- Pilih Buku --</option><?php while($r=$buku->fetch_assoc()): ?><option value="<?=$r['id_buku']?>"><?=htmlspecialchars($r['judul'])?></option><?php endwhile; ?></select>
<label>Tanggal Pinjam</label><input type="date" name="tanggal_pinjam" min="<?=$today?>" value="<?=$today?>" required>
<label>Tanggal Jatuh Tempo</label><input type="date" name="tanggal_jatuh_tempo" min="<?=$today?>" required>
<div class="form-actions"><button type="submit">✓ Simpan Peminjaman</button><a class="btn btn-light" href="tampil.peminjaman.php">Batal</a></div>
</form>
</body></html>
