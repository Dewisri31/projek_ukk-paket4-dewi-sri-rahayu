<?php
require_once '../auth.php'; require_anggota();
include '../connection.php';
if($_SERVER['REQUEST_METHOD']!=='POST') { header('Location: form.pengajuan.php'); exit; }
$id_buku=(int)($_POST['id_buku']??0); $tanggal=trim($_POST['tanggal_pinjam']??''); $today=date('Y-m-d');
if(!$id_buku || !$tanggal) die("Data pengajuan belum lengkap. <a href='form.pengajuan.php'>Kembali</a>");
if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$tanggal) || $tanggal<$today) die("Tanggal peminjaman hanya boleh hari ini atau setelahnya. <a href='form.pengajuan.php'>Kembali</a>");
$st=$conn->prepare("SELECT id_buku FROM buku WHERE id_buku=?"); $st->bind_param('i',$id_buku); $st->execute(); if(!$st->get_result()->num_rows) die("Buku tidak ditemukan.");
$st=$conn->prepare("SELECT COUNT(*) jml FROM peminjaman WHERE id_anggota=? AND status IN ('menunggu','dipinjam')"); $st->bind_param('i',$_SESSION['id_anggota']); $st->execute(); $j=$st->get_result()->fetch_assoc()['jml']; if($j>=3) die("Maksimal 3 peminjaman aktif/menunggu. <a href='../dashboard.anggota.php'>Kembali</a>");
$st=$conn->prepare("INSERT INTO peminjaman(id_anggota,id_petugas,id_admin,id_buku,tanggal_pinjam,tanggal_jatuh_tempo,status,alasan_penolakan) VALUES(?,NULL,NULL,?,?,NULL,'menunggu',NULL)"); $st->bind_param('iis',$_SESSION['id_anggota'],$id_buku,$tanggal);
if(!$st->execute()) die('Pengajuan gagal: '.htmlspecialchars($st->error));
$id=$conn->insert_id;
$st=$conn->prepare("INSERT INTO histori(id_admin,id_peminjaman,id_buku,jumlah,denda) VALUES(NULL,?,?,1,0)"); $st->bind_param('ii',$id,$id_buku); $st->execute();
header('Location: ../dashboard.anggota.php?pesan=diajukan'); exit;
?>