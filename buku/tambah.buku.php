
<?php

require_once '../auth.php';
require_staff();
require_once '../connection.php';

/*
|--------------------------------------------------------------------------
| 1. Validasi request
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: form.buku.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| 2. Ambil data form
|--------------------------------------------------------------------------
*/

$judul = trim($_POST['judul'] ?? '');
$id_kategori = (int)($_POST['id_kategori'] ?? 0);
$sinopsis = trim($_POST['sinopsis'] ?? '');

/*
|--------------------------------------------------------------------------
| 3. Validasi data form
|--------------------------------------------------------------------------
*/

if ($judul === '') {
    die("Judul buku wajib diisi.");
}

if (mb_strlen($judul) > 100) {
    die("Judul buku maksimal 100 karakter.");
}

if ($id_kategori <= 0) {
    die("Kategori wajib dipilih.");
}

if ($sinopsis === '') {
    die("Sinopsis wajib diisi.");
}

/*
|--------------------------------------------------------------------------
| 4. Ambil akun pengelola yang login
|--------------------------------------------------------------------------
| Mengikuti session dari index.php:
| Admin   : id_admin
| Petugas : id_petugas
|
| Tabel buku menggunakan kolom id_admin.
|--------------------------------------------------------------------------
*/

$role = $_SESSION['role'] ?? '';
$id_admin = 0;

if ($role === 'admin') {

    // Gunakan ID admin yang sedang login.
    $id_admin = (int)($_SESSION['id_admin'] ?? 0);

} elseif ($role === 'petugas') {

    /*
     * Database versi awal menggunakan id_admin
     * pada tabel buku, bukan id_petugas.
     *
     * Karena itu, gunakan admin pertama yang tersedia
     * sebagai pengelola cadangan untuk petugas.
     */

    $qAdmin = $conn->query("
        SELECT id_admin
        FROM admin
        ORDER BY id_admin ASC
        LIMIT 1
    ");

    if ($qAdmin && $rowAdmin = $qAdmin->fetch_assoc()) {
        $id_admin = (int)$rowAdmin['id_admin'];
    }

} else {
    die("Akses ditolak. Hanya admin atau petugas yang dapat menambah buku.");
}

if ($id_admin <= 0) {
    die(
        "Admin pengelola tidak ditemukan. " .
        "Pastikan tabel admin memiliki data."
    );
}

/*
|--------------------------------------------------------------------------
| 5. Validasi kategori
|--------------------------------------------------------------------------
*/

$stmtKategori = $conn->prepare("
    SELECT id_kategori
    FROM kategori
    WHERE id_kategori = ?
    LIMIT 1
");

if (!$stmtKategori) {
    die(
        "Gagal memeriksa kategori: " .
        htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8')
    );
}

$stmtKategori->bind_param("i", $id_kategori);
$stmtKategori->execute();

$resultKategori = $stmtKategori->get_result();
$kategoriAda = $resultKategori->num_rows > 0;

$stmtKategori->close();

if (!$kategoriAda) {
    die("Kategori tidak ditemukan.");
}

/*
|--------------------------------------------------------------------------
| 6. Validasi gambar
|--------------------------------------------------------------------------
*/

if (!isset($_FILES['gambar'])) {
    die("Gambar buku wajib dipilih.");
}

$file = $_FILES['gambar'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    die("Gagal mengunggah gambar buku.");
}

if ($file['size'] <= 0 || $file['size'] > 2 * 1024 * 1024) {
    die("Ukuran gambar harus lebih dari 0 byte dan maksimal 2 MB.");
}

$tmp = $file['tmp_name'];

/*
|--------------------------------------------------------------------------
| 7. Validasi MIME gambar
|--------------------------------------------------------------------------
*/

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($tmp);

$allowed = [
    'image/jpeg',
    'image/png',
    'image/webp'
];

if (!in_array($mime, $allowed, true)) {
    die("Format gambar harus JPG, JPEG, PNG, atau WEBP.");
}

/*
|--------------------------------------------------------------------------
| 8. Validasi file gambar
|--------------------------------------------------------------------------
*/

if (@getimagesize($tmp) === false) {
    die("File yang dipilih bukan gambar yang valid.");
}

/*
|--------------------------------------------------------------------------
| 9. Baca gambar
|--------------------------------------------------------------------------
| Gambar disimpan langsung ke kolom buku.gambar.
|--------------------------------------------------------------------------
*/

$gambar = file_get_contents($tmp);

if ($gambar === false) {
    die("Gagal membaca gambar.");
}

/*
|--------------------------------------------------------------------------
| 10. INSERT data buku
|--------------------------------------------------------------------------
| id_buku tidak dimasukkan karena dibuat otomatis
| oleh AUTO_INCREMENT pada database.
|--------------------------------------------------------------------------
*/

$sql = "
    INSERT INTO buku (
        id_admin,
        judul,
        id_kategori,
        gambar,
        sinopsis
    )
    VALUES (?, ?, ?, ?, ?)
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(
        "Prepare INSERT gagal: " .
        htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8')
    );
}

/*
|--------------------------------------------------------------------------
| 11. Bind parameter
|--------------------------------------------------------------------------
*/

$null = null;

$stmt->bind_param(
    "isibs",
    $id_admin,
    $judul,
    $id_kategori,
    $null,
    $sinopsis
);

/*
|--------------------------------------------------------------------------
| 12. Kirim data gambar binary
|--------------------------------------------------------------------------
| Parameter gambar berada pada indeks 3.
|--------------------------------------------------------------------------
*/

$stmt->send_long_data(3, $gambar);

/*
|--------------------------------------------------------------------------
| 13. Simpan data
|--------------------------------------------------------------------------
*/

if (!$stmt->execute()) {
    $error = $stmt->error;
    $stmt->close();

    die(
        "Gagal menambahkan buku:<br><br>" .
        htmlspecialchars($error, ENT_QUOTES, 'UTF-8')
    );
}

$stmt->close();

/*
|--------------------------------------------------------------------------
| 14. Redirect setelah berhasil
|--------------------------------------------------------------------------
*/

header("Location: tampil.buku.php?success=1");
exit;
