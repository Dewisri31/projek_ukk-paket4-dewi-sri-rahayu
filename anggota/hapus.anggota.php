<?php
require_once '../auth.php';
require_staff();
require_once '../connection.php';

// Validasi ID agar URL tidak dapat menjalankan query dengan nilai sembarang.
$id = filter_input(INPUT_GET, 'id_anggota', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    header('Location: tampil.anggota.php?hapus=invalid');
    exit;
}

// Jangan menghapus anggota yang sudah mempunyai transaksi peminjaman.
// Riwayat tersebut harus tetap tersimpan untuk laporan dan relasi database.
$cek = $conn->prepare('SELECT COUNT(*) AS total FROM peminjaman WHERE id_anggota = ?');
if (!$cek) {
    die('Gagal memeriksa riwayat anggota: ' . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8'));
}
$cek->bind_param('i', $id);
$cek->execute();
$total = (int)($cek->get_result()->fetch_assoc()['total'] ?? 0);
$cek->close();

if ($total > 0) {
    header('Location: tampil.anggota.php?hapus=terkait');
    exit;
}

// Hapus hanya jika belum pernah direferensikan oleh peminjaman.
$hapus = $conn->prepare('DELETE FROM anggota WHERE id_anggota = ?');
if (!$hapus) {
    die('Gagal menyiapkan penghapusan: ' . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8'));
}
$hapus->bind_param('i', $id);
if (!$hapus->execute()) {
    $hapus->close();
    header('Location: tampil.anggota.php?hapus=gagal');
    exit;
}
$hapus->close();

header('Location: tampil.anggota.php?hapus=berhasil');
exit;
?>
