<?php
require_once '../auth.php'; require_master_staff();
include('../connection.php');

$id_detail=(int)($_POST['id_detail']??0);
$id_admin=role()==='admin'?current_user_id():null;
$id_petugas=role()==='petugas'?current_user_id():null;
$id_peminjaman=(int)($_POST['id_peminjaman']??0);
$id_buku=(int)($_POST['id_buku']??0);
$jumlah=max(1,(int)($_POST['jumlah']??1));
$denda=max(0,(int)($_POST['denda']??0));
if(!$id_peminjaman||!$id_buku) die('Peminjaman dan buku wajib diisi.');
$st=$conn->prepare("INSERT INTO histori(id_detail,id_admin,id_petugas,id_peminjaman,id_buku,jumlah,denda) VALUES(?,?,?,?,?,?,?)");
$st->bind_param('iiiiiii',$id_detail,$id_admin,$id_petugas,$id_peminjaman,$id_buku,$jumlah,$denda);
if(!$st->execute()) die('Gagal menambahkan histori: '.htmlspecialchars($st->error));
header('Location: tampil.histori.php'); exit;
?>
