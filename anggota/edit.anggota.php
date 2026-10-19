
<?php
require_once '../auth.php';
require_staff();
require_once '../connection.php';

// Ambil dan validasi ID anggota
$id = filter_input(INPUT_GET, 'id_anggota', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    die("ID anggota tidak valid.");
}

$pesan_error = "";

// Proses penyimpanan perubahan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {

    $nama_anggota  = trim($_POST['nama_anggota'] ?? '');
    $jenis_kelamin = trim($_POST['jenis_kelamin'] ?? '');
    $tempat_lahir  = trim($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
    $telpon        = trim($_POST['telpon'] ?? '');
    $alamat        = trim($_POST['alamat'] ?? '');
    $username      = trim($_POST['username'] ?? '');
    $password      = $_POST['password'] ?? '';

    // Validasi input
    if (
        $nama_anggota === '' ||
        !in_array($jenis_kelamin, ['pria', 'wanita'], true) ||
        $tempat_lahir === '' ||
        $tanggal_lahir === '' ||
        $telpon === '' ||
        $alamat === '' ||
        $username === '' ||
        $password === ''
    ) {
        $pesan_error = "Semua kolom wajib diisi dengan benar.";
    } else {

        // Ambil ID pengelola sesuai sesi login
        $id_admin = null;
        $id_petugas = null;

        if (role() === 'admin') {
            $id_admin = (int) current_user_id();
        } elseif (role() === 'petugas') {
            $id_petugas = (int) current_user_id();
        }

        /*
         * Sesuaikan kolom pengelola berdasarkan role.
         * Kolom yang bukan milik role aktif dibuat NULL.
         */
        if ($id_admin !== null) {

            $stmt = $conn->prepare("
                UPDATE anggota
                SET id_admin = ?,
                    id_petugas = NULL,
                    nama_anggota = ?,
                    jenis_kelamin = ?,
                    tempat_lahir = ?,
                    tanggal_lahir = ?,
                    telpon = ?,
                    alamat = ?,
                    username = ?,
                    password = ?
                WHERE id_anggota = ?
            ");

            if (!$stmt) {
                die("Gagal menyiapkan query: " . $conn->error);
            }

            $stmt->bind_param(
                "issssssssi",
                $id_admin,
                $nama_anggota,
                $jenis_kelamin,
                $tempat_lahir,
                $tanggal_lahir,
                $telpon,
                $alamat,
                $username,
                $password,
                $id
            );

        } elseif ($id_petugas !== null) {

            $stmt = $conn->prepare("
                UPDATE anggota
                SET id_admin = NULL,
                    id_petugas = ?,
                    nama_anggota = ?,
                    jenis_kelamin = ?,
                    tempat_lahir = ?,
                    tanggal_lahir = ?,
                    telpon = ?,
                    alamat = ?,
                    username = ?,
                    password = ?
                WHERE id_anggota = ?
            ");

            if (!$stmt) {
                die("Gagal menyiapkan query: " . $conn->error);
            }

            $stmt->bind_param(
                "issssssssi",
                $id_petugas,
                $nama_anggota,
                $jenis_kelamin,
                $tempat_lahir,
                $tanggal_lahir,
                $telpon,
                $alamat,
                $username,
                $password,
                $id
            );

        } else {
            die("Role pengelola tidak valid.");
        }

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: tampil.anggota.php?success=edit");
            exit;
        } else {
            $pesan_error = "Gagal mengubah data: " . $stmt->error;
            $stmt->close();
        }
    }
}

// Ambil data anggota untuk ditampilkan di form
$stmtAnggota = $conn->prepare("
    SELECT *
    FROM anggota
    WHERE id_anggota = ?
    LIMIT 1
");

$stmtAnggota->bind_param("i", $id);
$stmtAnggota->execute();

$result = $stmtAnggota->get_result();
$anggota = $result->fetch_assoc();

$stmtAnggota->close();

if (!$anggota) {
    die("Data anggota tidak ditemukan.");
}

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Anggota</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="page-head">
    <div>
        <h1>✏️ Edit Data Anggota</h1>
        <p class="muted">Perbarui data anggota dengan benar.</p>
    </div>

    <a class="btn btn-light"
       href="../dashboard.<?= e($_SESSION['role'] ?? 'admin') ?>.php">
        ← Kembali ke Dashboard
    </a>
</div>

<?php if ($pesan_error !== ''): ?>
    <div class="alert alert-danger">
        <?= e($pesan_error) ?>
    </div>
<?php endif; ?>

<form method="POST"
      action="edit.anggota.php?id_anggota=<?= (int)$id ?>">

    <label>ID ANGGOTA</label><br>
    <input
        type="number"
        value="<?= (int)$anggota['id_anggota'] ?>"
        readonly
    >
    <br><br>

    <label>Akun Pengelola</label><br>
    <input
        type="text"
        value="<?= e(current_user_label()) ?>"
        readonly
    >
    <br><br>

    <label>NAMA ANGGOTA</label><br>
    <input
        type="text"
        name="nama_anggota"
        value="<?= e($anggota['nama_anggota']) ?>"
        maxlength="100"
        required
    >
    <br><br>

    <label>JENIS KELAMIN</label><br>
    <select name="jenis_kelamin" required>
        <option value="">-- Pilih Jenis Kelamin --</option>
        <option value="pria"
            <?= strtolower($anggota['jenis_kelamin'] ?? '') === 'pria' ? 'selected' : '' ?>>
            Pria
        </option>
        <option value="wanita"
            <?= strtolower($anggota['jenis_kelamin'] ?? '') === 'wanita' ? 'selected' : '' ?>>
            Wanita
        </option>
    </select>
    <br><br>

    <label>TEMPAT LAHIR</label><br>
    <input
        type="text"
        name="tempat_lahir"
        value="<?= e($anggota['tempat_lahir']) ?>"
        required
    >
    <br><br>

    <label>TANGGAL LAHIR</label><br>
    <input
        type="date"
        name="tanggal_lahir"
        value="<?= e($anggota['tanggal_lahir']) ?>"
        required
    >
    <br><br>

    <label>TELPON</label><br>
    <input
        type="text"
        name="telpon"
        value="<?= e($anggota['telpon']) ?>"
        required
    >
    <br><br>

    <label>ALAMAT</label><br>
    <input
        type="text"
        name="alamat"
        value="<?= e($anggota['alamat']) ?>"
        required
    >
    <br><br>

    <label>USERNAME LOGIN</label><br>
    <input
        type="text"
        name="username"
        value="<?= e($anggota['username']) ?>"
        required
    >
    <br><br>

    <label>PASSWORD LOGIN</label><br>
    <input
        type="password"
        name="password"
        value="<?= e($anggota['password']) ?>"
        required
    >
    <br><br>

    <button type="submit" name="update">
        Simpan Perubahan
    </button>

    <a class="btn btn-light" href="tampil.anggota.php">
        Batal
    </a>

</form>

</body>
</html>
