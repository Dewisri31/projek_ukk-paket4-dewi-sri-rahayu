<?php
require_once '../auth.php'; require_admin();
include '../connection.php';
$id=(int)($_GET['id_admin']??$_GET['id']??0);
if(!$id){die("ID admin tidak ditemukan.");}

$checks=[
    "anggota"=>"SELECT COUNT(*) n FROM anggota WHERE id_admin=?",
    "buku"=>"SELECT COUNT(*) n FROM buku WHERE id_admin=?",
    "peminjaman"=>"SELECT COUNT(*) n FROM peminjaman WHERE id_admin=?",
    "petugas"=>"SELECT COUNT(*) n FROM petugas WHERE id_admin=?",
    "kategori"=>"SELECT COUNT(*) n FROM kategori WHERE id_admin=?",
    "penerbit"=>"SELECT COUNT(*) n FROM penerbit WHERE id_admin=?",
    "histori"=>"SELECT COUNT(*) n FROM histori WHERE id_admin=?"
];
$total=0;
foreach($checks as $nama=>$sql){$s=$conn->prepare($sql);$s->bind_param("i",$id);$s->execute();$total+=(int)$s->get_result()->fetch_assoc()['n'];}
if($total>0){
    die("<div class='error action-message'><h2>Admin tidak dapat dihapus</h2><p>Admin masih digunakan oleh data lain (anggota, buku, peminjaman, petugas, kategori, penerbit, atau histori).</p><p>Silakan pindahkan relasi ke admin lain terlebih dahulu.</p><a class='btn btn-light' href='tampil.admin.php'>← Kembali</a></div>");
}
$s=$conn->prepare("DELETE FROM admin WHERE id_admin=?");$s->bind_param("i",$id);
if(!$s->execute()) die("Gagal menghapus admin: ".htmlspecialchars($s->error));
header("Location: tampil.admin.php?pesan=hapus");exit;
?>
