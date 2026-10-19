<?php
require_once '../auth.php'; require_petugas();
include '../connection.php';
$id=(int)($_GET['id']??0);
if(!$id) die("ID pengembalian tidak ditemukan.");
$s=$conn->prepare("SELECT id_peminjaman,id_petugas FROM pengembalian WHERE id_pengembalian=?");$s->bind_param("i",$id);$s->execute();$row=$s->get_result()->fetch_assoc();
if(!$row) die("Data pengembalian tidak ditemukan.");
if((int)$row['id_petugas'] !== (int)$_SESSION['id_petugas']) die("Pengembalian ini diproses oleh petugas lain. Gunakan akun petugas yang sesuai.");
$pin=(int)$row['id_peminjaman'];
$conn->begin_transaction();
try{
    $s=$conn->prepare("DELETE FROM denda WHERE id_pengembalian=?");$s->bind_param("i",$id);if(!$s->execute())throw new Exception($s->error);
    $s=$conn->prepare("DELETE FROM pengembalian WHERE id_pengembalian=?");$s->bind_param("i",$id);if(!$s->execute())throw new Exception($s->error);
    $s=$conn->prepare("UPDATE peminjaman SET status='dipinjam' WHERE id_peminjaman=?");$s->bind_param("i",$pin);if(!$s->execute())throw new Exception($s->error);
    $zero=0;$s=$conn->prepare("UPDATE histori SET denda=? WHERE id_peminjaman=?");$s->bind_param("ii",$zero,$pin);if(!$s->execute())throw new Exception($s->error);
    $conn->commit();header("Location: tampil.pengembalian.php?pesan=hapus");exit;
}catch(Throwable $e){$conn->rollback();die("Gagal menghapus pengembalian: ".htmlspecialchars($e->getMessage()));}
?>
