<?php
require_once '../auth.php'; require_petugas();
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: form.pengembalian.php'); exit; }

$id=(int)($_POST['id_peminjaman']??0);
$tanggal=trim($_POST['tanggal_kembali']??date('Y-m-d'));
$keterangan=trim($_POST['keterangan']??'');
$tarif=1000;

if(!$id) die('Peminjaman tidak valid.');
if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$tanggal) || $tanggal>date('Y-m-d')) die('Tanggal kembali tidak valid.');

$conn->begin_transaction();
try {
    $st=$conn->prepare("SELECT p.id_peminjaman,p.id_buku,p.id_anggota,p.tanggal_pinjam,p.tanggal_jatuh_tempo,p.status,b.judul,a.nama_anggota FROM peminjaman p JOIN buku b ON b.id_buku=p.id_buku JOIN anggota a ON a.id_anggota=p.id_anggota WHERE p.id_peminjaman=? FOR UPDATE");
    $st->bind_param('i',$id); $st->execute(); $pin=$st->get_result()->fetch_assoc();
    if(!$pin) throw new Exception('Data peminjaman tidak ditemukan.');
    if($pin['status']!=='dipinjam') throw new Exception('Peminjaman belum disetujui atau sudah dikembalikan.');
    if(empty($pin['tanggal_jatuh_tempo'])) throw new Exception('Tanggal jatuh tempo belum ditentukan.');

    $cek=$conn->prepare("SELECT id_pengembalian FROM pengembalian WHERE id_peminjaman=? LIMIT 1");
    $cek->bind_param('i',$id); $cek->execute();
    if($cek->get_result()->num_rows>0) throw new Exception('Pengembalian untuk peminjaman ini sudah tercatat.');

    $jatuh=new DateTime($pin['tanggal_jatuh_tempo']); $kembali=new DateTime($tanggal);
    $terlambat=$kembali>$jatuh ? $jatuh->diff($kembali)->days : 0;
    $total=$terlambat*$tarif;

    $st=$conn->prepare("INSERT INTO pengembalian(id_peminjaman,id_petugas,tanggal_kembali,terlambat,denda,keterangan) VALUES(?,?,?,?,?,?)");
    $petugas=(int)$_SESSION['id_petugas'];
    $st->bind_param('iisiis',$id,$petugas,$tanggal,$terlambat,$total,$keterangan);
    if(!$st->execute()) throw new Exception('Gagal menyimpan pengembalian: '.$st->error);
    $id_pengembalian=$conn->insert_id;

    // Denda hanya dibuat jika benar-benar terlambat. Tidak ada tagihan Rp0.
    if($terlambat>0){
        $jenis='terlambat';
        $d=$conn->prepare("INSERT INTO denda(id_petugas,id_peminjaman,id_pengembalian,jenis_denda,terlambat,tarif_per_hari,total_denda) VALUES(?,?,?,?,?,?,?)");
        $d->bind_param('iiisiii',$petugas,$id,$id_pengembalian,$jenis,$terlambat,$tarif,$total);
        if(!$d->execute()) throw new Exception('Gagal membuat denda otomatis: '.$d->error);
    }

    $up=$conn->prepare("UPDATE peminjaman SET status='selesai' WHERE id_peminjaman=? AND status='dipinjam'");
    $up->bind_param('i',$id); if(!$up->execute() || $up->affected_rows!==1) throw new Exception('Status peminjaman gagal diperbarui.');

    $h=$conn->prepare("UPDATE histori SET denda=? WHERE id_peminjaman=?");
    $h->bind_param('ii',$total,$id); if(!$h->execute()) throw new Exception('Histori gagal diperbarui: '.$h->error);

    $conn->commit();
    $msg=$terlambat>0 ? "Pengembalian berhasil. Denda otomatis Rp ".number_format($total,0,',','.') : 'Pengembalian berhasil tanpa denda.';
    header('Location: tampil.pengembalian.php?pesan='.urlencode($msg)); exit;
} catch(Throwable $e){ $conn->rollback(); die('<div class="error">Pengembalian gagal: '.htmlspecialchars($e->getMessage()).' <a href="form.pengembalian.php">Kembali</a></div>'); }
?>