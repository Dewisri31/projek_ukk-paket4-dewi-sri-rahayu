<?php
require_once '../auth.php';
require_approval_staff();
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tampil.peminjaman.php');
    exit;
}

$id = (int)($_POST['id_peminjaman'] ?? 0);
$aksi = $_POST['aksi'] ?? '';
$tempo = trim($_POST['tanggal_jatuh_tempo'] ?? '');
$alasan = trim($_POST['alasan_penolakan'] ?? '');

if (!$id) die('ID pengajuan tidak valid. <a href="tampil.peminjaman.php">Kembali</a>');

$conn->begin_transaction();
try {
    $st = $conn->prepare("SELECT id_peminjaman,id_anggota,id_buku,tanggal_pinjam,status FROM peminjaman WHERE id_peminjaman=? FOR UPDATE");
    $st->bind_param('i', $id);
    $st->execute();
    $p = $st->get_result()->fetch_assoc();

    if (!$p || $p['status'] !== 'menunggu') {
        throw new Exception('Pengajuan tidak ditemukan atau sudah diproses.');
    }

    $isAdmin = (($_SESSION['role'] ?? '') === 'admin');
    $staffAdminId = $isAdmin ? (int)$_SESSION['id_admin'] : null;
    $staffPetugasId = !$isAdmin ? (int)$_SESSION['id_petugas'] : null;

    if ($aksi === 'setujui') {
        if (!$tempo || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tempo) || $tempo < $p['tanggal_pinjam']) {
            throw new Exception('Tanggal jatuh tempo wajib diisi dan tidak boleh sebelum tanggal pinjam.');
        }

        $cek = $conn->prepare("SELECT COUNT(*) jml FROM peminjaman WHERE id_buku=? AND status='dipinjam'");
        $cek->bind_param('i', $p['id_buku']);
        $cek->execute();
        if ((int)$cek->get_result()->fetch_assoc()['jml'] > 0) {
            throw new Exception('Buku sedang dipinjam sehingga pengajuan belum dapat disetujui.');
        }

        if ($isAdmin) {
            $up = $conn->prepare("UPDATE peminjaman SET id_petugas=NULL, id_admin=?, tanggal_jatuh_tempo=?, status='dipinjam', alasan_penolakan=NULL WHERE id_peminjaman=? AND status='menunggu'");
            $up->bind_param('isi', $staffAdminId, $tempo, $id);
        } else {
            $up = $conn->prepare("UPDATE peminjaman SET id_petugas=?, id_admin=NULL, tanggal_jatuh_tempo=?, status='dipinjam', alasan_penolakan=NULL WHERE id_peminjaman=? AND status='menunggu'");
            $up->bind_param('isi', $staffPetugasId, $tempo, $id);
        }
        if (!$up->execute() || $up->affected_rows !== 1) throw new Exception('ACC peminjaman gagal.');

        if ($isAdmin) {
            $h = $conn->prepare("UPDATE histori SET id_admin=? WHERE id_peminjaman=?");
            $h->bind_param('ii', $staffAdminId, $id);
        } else {
            $h = $conn->prepare("UPDATE histori SET id_admin=NULL WHERE id_peminjaman=?");
            $h->bind_param('i', $id);
        }
        $h->execute();
    } elseif ($aksi === 'tolak') {
        if ($alasan === '') $alasan = 'Pengajuan ditolak oleh Petugas.';

        if ($isAdmin) {
            $up = $conn->prepare("UPDATE peminjaman SET id_petugas=NULL, id_admin=?, status='ditolak', alasan_penolakan=? WHERE id_peminjaman=? AND status='menunggu'");
            $up->bind_param('isi', $staffAdminId, $alasan, $id);
        } else {
            $up = $conn->prepare("UPDATE peminjaman SET id_petugas=?, id_admin=NULL, status='ditolak', alasan_penolakan=? WHERE id_peminjaman=? AND status='menunggu'");
            $up->bind_param('isi', $staffPetugasId, $alasan, $id);
        }
        if (!$up->execute() || $up->affected_rows !== 1) throw new Exception('Penolakan gagal.');
    } else {
        throw new Exception('Aksi tidak valid.');
    }

    $conn->commit();
    header('Location: tampil.peminjaman.php?pesan=berhasil');
    exit;
} catch (Throwable $e) {
    $conn->rollback();
    die('<div class="error">'.htmlspecialchars($e->getMessage()).' <a href="tampil.peminjaman.php">Kembali</a></div>');
}
?>
