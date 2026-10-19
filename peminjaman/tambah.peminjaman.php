<?php
require_once '../auth.php'; require_petugas(); include '../connection.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: form.peminjaman.php');exit;}
$ia=(int)($_POST['id_anggota']??0); $ib=(int)($_POST['id_buku']??0); $pin=trim($_POST['tanggal_pinjam']??''); $tempo=trim($_POST['tanggal_jatuh_tempo']??''); $today=date('Y-m-d');
if(!$ia||!$ib||!$pin||!$tempo) die('Data belum lengkap. <a href="form.peminjaman.php">Kembali</a>');
if($pin<$today||$tempo<$pin) die('Tanggal tidak valid. <a href="form.peminjaman.php">Kembali</a>');
$cek=$conn->prepare("SELECT COUNT(*) jml FROM peminjaman WHERE id_buku=? AND status='dipinjam'"); $cek->bind_param('i',$ib); $cek->execute(); if((int)$cek->get_result()->fetch_assoc()['jml']>0) die('Buku sedang dipinjam. <a href="form.peminjaman.php">Kembali</a>');
$st=$conn->prepare("INSERT INTO peminjaman(id_anggota,id_petugas,id_admin,id_buku,tanggal_pinjam,tanggal_jatuh_tempo,status,alasan_penolakan) VALUES(?,?,NULL,?,?,?,'dipinjam',NULL)"); $st->bind_param('iiiss', $ia,$_SESSION['id_petugas'],$ib,$pin,$tempo);
if(!$st->execute()) die('Gagal: '.htmlspecialchars($st->error));
$id=$conn->insert_id;
$st=$conn->prepare("INSERT INTO histori(id_admin,id_peminjaman,id_buku,jumlah,denda) VALUES(NULL,?,?,?,0)"); $jumlah=1; $st->bind_param('iii',$id,$ib,$jumlah); $st->execute();
header('Location: tampil.peminjaman.php?pesan=berhasil'); exit;
?>
