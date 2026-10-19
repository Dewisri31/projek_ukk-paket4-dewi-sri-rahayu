<?php

require_once '../connection.php';

$id = isset($_GET['id_buku'])
    ? (int) $_GET['id_buku']
    : 0;

if ($id <= 0) {
    http_response_code(404);
    exit;
}

$stmt = $conn->prepare("
    SELECT gambar
    FROM buku
    WHERE id_buku = ?
    LIMIT 1
");

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$data = $result->fetch_assoc();

$stmt->close();

if (!$data || empty($data['gambar'])) {
    http_response_code(404);
    exit;
}

$gambar = $data['gambar'];


/*
|--------------------------------------------------------------------------
| DETEKSI FORMAT GAMBAR ASLI
|--------------------------------------------------------------------------
*/

$finfo = new finfo(FILEINFO_MIME_TYPE);

$mime = $finfo->buffer($gambar);

$allowed = [
    'image/jpeg',
    'image/png',
    'image/webp'
];

if (!in_array($mime, $allowed, true)) {
    http_response_code(415);
    exit;
}


/*
|--------------------------------------------------------------------------
| OUTPUT
|--------------------------------------------------------------------------
*/

header(
    'Content-Type: ' . $mime
);

header(
    'Content-Length: ' . strlen($gambar)
);

header(
    'Cache-Control: no-cache, must-revalidate'
);

echo $gambar;

exit;