<?php
require_once '../auth.php';
require_detail_petugas();
require_once '../connection.php';

$id_detail = (int)($_GET['id_detail'] ?? 0);
if ($id_detail <= 0) {
    header('Location: tampil.detail.php');
    exit;
}

$error = '';

// Ambil data lengkap detail buku + data buku + penerbit + kategori + admin.
$stmt = $conn->prepare("SELECT
    d.id_detail,
    d.id_buku,
    d.id_admin AS detail_admin,
    d.id_kategori,
    d.id_penerbit,
    d.pengarang,
    d.tahun_terbit,
    d.jumlah_halaman,
    d.jumlah_stok,
    d.isbn,
    b.judul,
    b.id_admin AS buku_admin,
    b.gambar,
    b.sinopsis
FROM detail_buku d
LEFT JOIN buku b ON b.id_buku = d.id_buku
WHERE d.id_detail = ?
LIMIT 1");
$stmt->bind_param('i', $id_detail);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    die('<div class="action-message error"><h2>Data tidak ditemukan</h2><p>Detail buku yang dipilih tidak tersedia.</p><a class="btn" href="tampil.detail.php">Kembali</a></div>');
}

// Proses simpan seluruh data yang berhubungan dengan detail buku.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id_admin=role()==='admin'?current_user_id():null; $id_petugas=role()==='petugas'?current_user_id():null;
    $id_kategori    = (int)($_POST['id_kategori'] ?? 0);
    $id_penerbit    = (int)($_POST['id_penerbit'] ?? 0);
    $judul          = trim($_POST['judul'] ?? '');
    $pengarang      = trim($_POST['pengarang'] ?? '');
    $tahun_terbit   = (int)($_POST['tahun_terbit'] ?? 0);
    $jumlah_halaman = (int)($_POST['jumlah_halaman'] ?? 0);
    $jumlah_stok    = (int)($_POST['jumlah_stok'] ?? 0);
    $isbn           = trim($_POST['isbn'] ?? '');
    $sinopsis       = trim($_POST['sinopsis'] ?? '');

    if (!$id_kategori || !$id_penerbit || $judul === '' || $pengarang === '') {
        $error = 'Semua data utama buku wajib diisi.';
    } elseif ($tahun_terbit < 0 || $jumlah_halaman < 0 || $jumlah_stok < 0) {
        $error = 'Tahun, jumlah halaman, dan stok tidak boleh bernilai negatif.';
    } else {
        $conn->begin_transaction();

        try {
            // Update tabel buku: admin, judul, kategori, sinopsis, dan gambar.
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception('Gambar gagal diunggah.');
                }

                $mime = mime_content_type($_FILES['gambar']['tmp_name']);
                $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                if (!in_array($mime, $allowed, true)) {
                    throw new Exception('Format gambar harus JPG, PNG, WEBP, atau GIF.');
                }

                $gambar = file_get_contents($_FILES['gambar']['tmp_name']);
                $stmtBuku = $conn->prepare("UPDATE buku SET id_admin=?, id_petugas=?, judul=?, id_kategori=?, gambar=?, sinopsis=? WHERE id_buku=?");
                $stmtBuku->bind_param('iisibsi', $id_admin, $id_petugas, $judul, $id_kategori, $gambar, $sinopsis, $data['id_buku']);
            } else {
                $stmtBuku = $conn->prepare("UPDATE buku SET id_admin=?, id_petugas=?, judul=?, id_kategori=?, sinopsis=? WHERE id_buku=?");
                $stmtBuku->bind_param('iisisi', $id_admin, $id_petugas, $judul, $id_kategori, $sinopsis, $data['id_buku']);
            }

            if (!$stmtBuku->execute()) {
                throw new Exception('Gagal mengubah data buku: ' . $stmtBuku->error);
            }
            $stmtBuku->close();

            // Update seluruh data detail buku, termasuk penerbit.
            $stmtDetail = $conn->prepare("UPDATE detail_buku SET id_admin=?, id_petugas=?, id_kategori=?, id_penerbit=?, pengarang=?, tahun_terbit=?, jumlah_halaman=?, jumlah_stok=?, isbn=? WHERE id_detail=?");
            $stmtDetail->bind_param('iiiisiiisi', $id_admin, $id_petugas, $id_kategori, $id_penerbit, $pengarang, $tahun_terbit, $jumlah_halaman, $jumlah_stok, $isbn, $id_detail);

            if (!$stmtDetail->execute()) {
                throw new Exception('Gagal mengubah detail buku: ' . $stmtDetail->error);
            }
            $stmtDetail->close();

            $conn->commit();
            header('Location: tampil.detail.php?updated=1');
            exit;
        } catch (Throwable $e) {
            $conn->rollback();
            $error = $e->getMessage();
        }
    }

    // Jika gagal, nilai form mengikuti input terakhir agar tidak hilang.
    $data['id_admin'] = $id_admin;
    $data['id_kategori'] = $id_kategori;
    $data['id_penerbit'] = $id_penerbit;
    $data['judul'] = $judul;
    $data['pengarang'] = $pengarang;
    $data['tahun_terbit'] = $tahun_terbit;
    $data['jumlah_halaman'] = $jumlah_halaman;
    $data['jumlah_stok'] = $jumlah_stok;
    $data['isbn'] = $isbn;
    $data['sinopsis'] = $sinopsis;
}

$admins = $conn->query("SELECT id_admin, nama_admin FROM admin ORDER BY nama_admin ASC");
$kategoris = $conn->query("SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori ASC");
$penerbits = $conn->query("SELECT id_penerbit, kode_penerbit, nama_penerbit FROM penerbit ORDER BY nama_penerbit ASC");

$role = htmlspecialchars($_SESSION['role'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Detail Buku</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="page-head">
    <div>
        <h1>✏️ Edit Detail Buku</h1>
        <p class="muted">Edit seluruh informasi buku dalam satu form.</p>
    </div>
    <a class="btn" href="../dashboard.<?= $role ?>.php">← Dashboard</a>
</div>

<?php if ($error): ?>
    <div class="error action-message"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="form-card">
    <div class="form-section">
        <h2>📚 Informasi Buku</h2>
        <p class="muted">Data dari tabel buku yang dapat diperbarui.</p>

        <div class="form-grid">
            <div class="field">
                <label>ID Detail</label>
                <input type="text" value="<?= htmlspecialchars($data['id_detail']) ?>" readonly>
            </div>

            <div class="field">
                <label>ID Buku</label>
                <input type="text" value="<?= htmlspecialchars($data['id_buku']) ?>" readonly>
            </div>

            <div class="field field-full">
                <label>Judul Buku *</label>
                <input type="text" name="judul" value="<?= htmlspecialchars($data['judul'] ?? '') ?>" required>
            </div>

            <div class="field"><label>Akun Pengelola</label><input value="<?= htmlspecialchars(current_user_label()) ?>" readonly></div>

            <div class="field">
                <label>Kategori *</label>
                <select name="id_kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php while ($kategori = $kategoris->fetch_assoc()): ?>
                        <option value="<?= $kategori['id_kategori'] ?>" <?= ((int)$kategori['id_kategori'] === (int)$data['id_kategori']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kategori['nama_kategori']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="field field-full">
                <label>Gambar Buku</label>
                <?php if (!empty($data['gambar'])): ?>
                    <div class="image-preview">
                        <img src="data:image/jpeg;base64,<?= base64_encode($data['gambar']) ?>" alt="Sampul buku">
                    </div>
                <?php endif; ?>
                <input type="file" name="gambar" accept="image/jpeg,image/png,image/webp,image/gif">
                <small class="muted">Kosongkan jika tidak ingin mengganti gambar.</small>
            </div>

            <div class="field field-full">
                <label>Sinopsis</label>
                <textarea name="sinopsis" rows="6"><?= htmlspecialchars($data['sinopsis'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h2>📖 Data Detail & Penerbit</h2>
        <p class="muted">Semua data detail, termasuk penerbit, dapat diubah dari sini.</p>

        <div class="form-grid">
            <div class="field field-full">
                <label>Penerbit *</label>
                <select name="id_penerbit" required>
                    <option value="">-- Pilih Penerbit --</option>
                    <?php while ($penerbit = $penerbits->fetch_assoc()): ?>
                        <option value="<?= $penerbit['id_penerbit'] ?>" <?= ((int)$penerbit['id_penerbit'] === (int)$data['id_penerbit']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($penerbit['nama_penerbit']) ?><?= !empty($penerbit['kode_penerbit']) ? ' — ' . htmlspecialchars($penerbit['kode_penerbit']) : '' ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="field">
                <label>Pengarang *</label>
                <input type="text" name="pengarang" value="<?= htmlspecialchars($data['pengarang'] ?? '') ?>" required>
            </div>

            <div class="field">
                <label>Tahun Terbit *</label>
                <input type="number" name="tahun_terbit" min="0" max="2100" value="<?= htmlspecialchars($data['tahun_terbit'] ?? '') ?>" required>
            </div>

            <div class="field">
                <label>Jumlah Halaman *</label>
                <input type="number" name="jumlah_halaman" min="0" value="<?= htmlspecialchars($data['jumlah_halaman'] ?? '') ?>" required>
            </div>

            <div class="field">
                <label>Jumlah Stok *</label>
                <input type="number" name="jumlah_stok" min="0" value="<?= htmlspecialchars($data['jumlah_stok'] ?? '') ?>" required>
            </div>

            <div class="field field-full">
                <label>ISBN *</label>
                <input type="text" name="isbn" value="<?= htmlspecialchars($data['isbn'] ?? '') ?>" required>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" name="update" class="btn">💾 Simpan Semua Perubahan</button>
        <a class="btn" href="tampil.detail.php">Batal</a>
        <a class="btn" href="../dashboard.<?= $role ?>.php">← Dashboard</a>
    </div>
</form>

</body>
</html>
