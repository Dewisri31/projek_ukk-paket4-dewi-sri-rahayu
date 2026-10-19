<?php
require_once 'auth.php'; require_anggota(); include 'connection.php'; $id=(int)$_SESSION['id_anggota'];
function one($conn,$sql,$id){$s=$conn->prepare($sql);$s->bind_param('i',$id);$s->execute();return $s->get_result()->fetch_assoc()['total']??0;}
$total=one($conn,"SELECT COUNT(*) total FROM peminjaman WHERE id_anggota=?",$id); $aktif=one($conn,"SELECT COUNT(*) total FROM peminjaman WHERE id_anggota=? AND status='dipinjam'",$id); $pending=one($conn,"SELECT COUNT(*) total FROM peminjaman WHERE id_anggota=? AND status='menunggu'",$id); $selesai=one($conn,"SELECT COUNT(*) total FROM peminjaman WHERE id_anggota=? AND status='selesai'",$id);
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Dashboard Anggota</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="topbar"><strong>📚 Perpustakaan <span class="role-badge">ANGGOTA</span></strong><a class="btn btn-light" href="logout.php">Keluar</a></div>
<div class="container">
<div class="page-head"><div><h1>Dashboard Anggota</h1><p class="muted">Selamat datang, <b><?=htmlspecialchars($_SESSION['nama'])?></b>.</p></div></div>
<div class="stats"><div class="stat"><small>Total Pengajuan</small><strong><?=$total?></strong></div><div class="stat"><small>Menunggu ACC</small><strong><?=$pending?></strong></div><div class="stat"><small>Sedang Dipinjam</small><strong><?=$aktif?></strong></div><div class="stat"><small>Selesai</small><strong><?=$selesai?></strong></div></div>
<div class="card-grid">
<div class="card"><div class="menu-icon">📚</div><h3>Daftar Buku</h3><p>Lihat koleksi dan detail buku. Pada bagian aksi tersedia tombol Pinjam dan Detail.</p><a class="btn" href="buku/tampil.buku.php">Lihat Buku</a></div>
<div class="card"><div class="menu-icon">📖</div><h3>Peminjaman</h3><p>Ajukan buku. Tanggal pinjam hanya mulai hari ini dan seterusnya.</p><a class="btn" href="peminjaman/form.pengajuan.php">Ajukan Peminjaman</a><a class="btn btn-light" href="peminjaman/tampil.peminjaman.php">Status Saya</a></div>
<div class="card"><div class="menu-icon">🕘</div><h3>Histori</h3><p>Lihat riwayat peminjaman milik Anda.</p><a class="btn" href="histori/tampil.histori.php">Lihat Histori</a></div>
<div class="card"><div class="menu-icon">💰</div><h3>Denda</h3><p>Lihat denda otomatis setelah pengembalian diproses Petugas.</p><a class="btn" href="denda/tampil.denda.php">Lihat Denda</a></div>
</div>
<div class="info">📌 <b>Alur:</b> pilih buku → Pinjam → menunggu ACC Petugas → Petugas menentukan jatuh tempo → buku dipinjam → kembalikan ke Petugas → status selesai.</div>
</div></body></html>
