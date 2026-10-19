<?php
require_once '../auth.php'; require_staff();
include '../connection.php';
if($_SERVER['REQUEST_METHOD']!=='POST') die("Form belum dikirim.");
$id_admin=role()==='admin'?current_user_id():null; $id_petugas=role()==='petugas'?current_user_id():null;$kode=trim($_POST['kode_penerbit']??'');$nama=trim($_POST['nama_penerbit']??'');
if(($id_admin===null&&$id_petugas===null)||$nama==='') die("Data penerbit belum lengkap.");
$s=$conn->prepare("INSERT INTO penerbit(id_admin,id_petugas,kode_penerbit,nama_penerbit) VALUES(?,?,?,?)");$s->bind_param("iiss",$id_admin,$id_petugas,$kode,$nama);
if(!$s->execute()) die("Gagal menambahkan penerbit: ".htmlspecialchars($s->error));
header("Location: tampil.penerbit.php");exit;
?>
