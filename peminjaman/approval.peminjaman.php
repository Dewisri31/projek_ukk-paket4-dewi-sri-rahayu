<?php
require_once '../auth.php';
require_approval_staff();
include '../connection.php';

$id = (int)($_GET['id'] ?? 0);
$st = $conn->prepare("SELECT p.*, a.nama_anggota, b.judul FROM peminjaman p LEFT JOIN anggota a ON p.id_anggota=a.id_anggota LEFT JOIN buku b ON p.id_buku=b.id_buku WHERE p.id_peminjaman=? AND p.status='menunggu'");
$st->bind_param('i', $id);
$st->execute();
$p = $st->get_result()->fetch_assoc();
if (!$p) die('<div class="error">Pengajuan tidak ditemukan. <a href="tampil.peminjaman.php">Kembali</a></div>');
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Proses Peminjaman</title><link rel="stylesheet" href="../style.css"></head>
<body>
<div class="page-head"><div><h1>📋 Proses Peminjaman</h1><p class="muted">Petugas menentukan tanggal jatuh tempo dan memproses pengajuan anggota.</p></div><a class="btn btn-light" href="tampil.peminjaman.php">← Kembali</a></div>
<div class="card form-card">
    <div class="info"><b>Diproses oleh:</b> <?=htmlspecialchars($_SESSION['nama'])?> (<?=htmlspecialchars($_SESSION['role'])?>)</div>
    <p><b>Anggota:</b> <?=htmlspecialchars($p['nama_anggota'])?></p>
    <p><b>Buku:</b> <?=htmlspecialchars($p['judul'])?></p>
    <p><b>Tanggal Pinjam:</b> <?=htmlspecialchars($p['tanggal_pinjam'])?></p>
    <form action="acc.peminjaman.php" method="post">
        <input type="hidden" name="id_peminjaman" value="<?=$id?>">
        <label>Tanggal Jatuh Tempo</label>
        <input type="date" name="tanggal_jatuh_tempo" min="<?=htmlspecialchars($p['tanggal_pinjam'])?>" required>
        <label>Alasan Penolakan</label>
        <textarea name="alasan_penolakan" maxlength="255" placeholder="Isi jika pengajuan ditolak"></textarea>
        <div class="form-actions">
            <button name="aksi" value="setujui" type="submit" class="btn">✓ Setujui & Tentukan Jatuh Tempo</button>
            <button name="aksi" value="tolak" type="submit" class="btn-danger" onclick="return confirm('Tolak pengajuan ini?')">✕ Tolak</button>
            <a class="btn btn-light" href="tampil.peminjaman.php">Batal</a>
        </div>
    </form>
</div>
</body></html>
