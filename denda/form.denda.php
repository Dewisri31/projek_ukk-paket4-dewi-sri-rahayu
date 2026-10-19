<?php
require_once '../auth.php';
require_login();
$role=role();
if(!in_array($role,['admin','petugas','anggota'],true)){go_home(dashboard_file());}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Denda Otomatis</title><link rel="stylesheet" href="../style.css"></head><body>
<div class="page-head"><div><h1>💰 Denda Otomatis</h1><p class="muted">Denda tidak dibuat manual. Sistem menghitung dan mencatat denda ketika Petugas memproses pengembalian.</p></div><a class="btn btn-light" href="tampil.denda.php">← Kembali</a></div>
<div class="container"><div class="info"><b>Alur:</b> Peminjaman disetujui → buku dikembalikan → sistem membandingkan tanggal kembali dengan jatuh tempo → jumlah hari terlambat × tarif Rp1.000/hari → denda tersimpan otomatis.</div>
<div class="card"><h3>Tarif Denda Keterlambatan</h3><p>Rp 1.000 per hari keterlambatan.</p><p class="muted">Jika tidak terlambat, nilai denda otomatis Rp 0 dan tidak dibuat sebagai tagihan denda.</p><a class="btn" href="../pengembalian/form.pengembalian.php">↩ Proses Pengembalian</a></div></div></body></html>