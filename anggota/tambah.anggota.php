<?php
require_once '../auth.php'; require_staff();
include '../connection.php';
if($_SERVER['REQUEST_METHOD']!=='POST') die("Form belum dikirim.");
$id_admin=role()==='admin'?current_user_id():null; $id_petugas=role()==='petugas'?current_user_id():null;$nama=trim($_POST['nama_anggota']??'');$jk=$_POST['jenis_kelamin']??'';$tempat=trim($_POST['tempat_lahir']??'');$tgl=$_POST['tanggal_lahir']??'';$tel=trim($_POST['telpon']??'');$alamat=trim($_POST['alamat']??'');$username=trim($_POST['username']??'');$password=$_POST['password']??'';
if(($id_admin===null&&$id_petugas===null)||$nama===''||!in_array($jk,['pria','wanita'],true)||$tgl===''||$username===''||$password==='') die("Data anggota belum lengkap.");
$s=$conn->prepare("INSERT INTO anggota(id_admin,id_petugas,nama_anggota,jenis_kelamin,tempat_lahir,tanggal_lahir,telpon,alamat,username,password) VALUES(?,?,?,?,?,?,?,?,?,?)");$s->bind_param("iissssssss",$id_admin,$id_petugas,$nama,$jk,$tempat,$tgl,$tel,$alamat,$username,$password);
if(!$s->execute()) die("Gagal menambahkan anggota: ".htmlspecialchars($s->error));
header("Location: tampil.anggota.php");exit;
?>
