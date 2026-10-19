<?php
require_once 'auth.php'; require_petugas(); include 'connection.php';
$pending=$conn->query("SELECT COUNT(*) total FROM peminjaman WHERE status='menunggu'")->fetch_assoc()['total'];
$aktif=$conn->query("SELECT COUNT(*) total FROM peminjaman WHERE status='dipinjam'")->fetch_assoc()['total'];
$kembali=$conn->query("SELECT COUNT(*) total FROM pengembalian WHERE tanggal_kembali=CURDATE()")->fetch_assoc()['total'];
$terlambat=$conn->query("SELECT COUNT(*) total FROM peminjaman WHERE status='dipinjam' AND tanggal_jatuh_tempo<CURDATE()")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Dashboard Petugas</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="topbar"><strong>📚 Perpustakaan <span class="role-badge">PETUGAS</span></strong><a class="btn btn-light" href="logout.php">Keluar</a></div>
<div class="container">
<div class="page-head"><div><h1>Dashboard Petugas</h1><p class="muted">Selamat datang, <b><?=htmlspecialchars($_SESSION['nama'])?></b>. Semua proses operasional dilakukan dari sini.</p></div></div>
<div class="stats"><div class="stat"><small>Menunggu ACC</small><strong><?=$pending?></strong></div><div class="stat"><small>Sedang Dipinjam</small><strong><?=$aktif?></strong></div><div class="stat"><small>Kembali Hari Ini</small><strong><?=$kembali?></strong></div><div class="stat"><small>Terlambat</small><strong><?=$terlambat?></strong></div></div>
<div class="card-grid">
<div class="card"><div class="menu-icon">📋</div><h3>Peminjaman</h3><p>ACC pengajuan, tentukan jatuh tempo, dan proses peminjaman manual.</p><a class="btn" href="peminjaman/tampil.peminjaman.php">Kelola Peminjaman</a></div>
<div class="card"><div class="menu-icon">↩️</div><h3>Pengembalian</h3><p>Proses buku kembali dan hitung keterlambatan otomatis.</p><a class="btn" href="pengembalian/tampil.pengembalian.php">Kelola Pengembalian</a></div>
<div class="card"><div class="menu-icon">💰</div><h3>Denda</h3><p>Denda dibuat otomatis saat pengembalian dan dihitung dari keterlambatan.</p><a class="btn" href="denda/tampil.denda.php">Kelola Denda</a></div>
<div class="card"><div class="menu-icon">🕘</div><h3>Histori</h3><p>Lihat seluruh riwayat peminjaman.</p><a class="btn" href="histori/tampil.histori.php">Lihat Histori</a></div>
<div class="card"><div class="menu-icon">📚</div><h3>Data Buku</h3><p>Kelola data buku dan detail buku (CRUD).</p><a class="btn" href="buku/tampil.buku.php">Buku</a><a class="btn" href="detail_buku/tampil.detail.php">Detail Buku</a></div>
<div class="card"><div class="menu-icon">👥</div><h3>Data Anggota</h3><p>Lihat dan kelola anggota yang dilayani.</p><a class="btn" href="anggota/tampil.anggota.php">Anggota</a></div>
</div>
</div></body></html>
