<?php

require_once '../auth.php';
require_staff();

include("../connection.php");

/*
|--------------------------------------------------------------------------
| AMBIL ID BUKU
|--------------------------------------------------------------------------
*/

$id = isset($_GET['id_buku'])
    ? (int) $_GET['id_buku']
    : 0;

if ($id <= 0) {
    die("ID buku tidak valid.");
}

/*
|--------------------------------------------------------------------------
| PROSES UPDATE
|--------------------------------------------------------------------------
*/

if (isset($_POST['update'])) {

    $judul = trim($_POST['judul'] ?? '');
    $id_kategori = (int) ($_POST['id_kategori'] ?? 0);
    $sinopsis = trim($_POST['sinopsis'] ?? '');

    /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */

    if ($judul === '') {
        die("Judul buku wajib diisi.");
    }

    if ($id_kategori <= 0) {
        die("Kategori wajib dipilih.");
    }

    if ($sinopsis === '') {
        die("Sinopsis wajib diisi.");
    }

    /*
    |--------------------------------------------------------------------------
    | CEK GAMBAR BARU
    |--------------------------------------------------------------------------
    */

    $adaGambarBaru = (
        isset($_FILES['gambar']) &&
        $_FILES['gambar']['error'] === UPLOAD_ERR_OK &&
        $_FILES['gambar']['size'] > 0
    );


    /*
    |--------------------------------------------------------------------------
    | UPDATE JIKA ADA GAMBAR BARU
    |--------------------------------------------------------------------------
    */

    if ($adaGambarBaru) {

        $file = $_FILES['gambar'];

        /*
        | Maksimal 2 MB
        */

        if ($file['size'] > 2 * 1024 * 1024) {
            die("Ukuran gambar maksimal 2 MB.");
        }

        $tmp_name = $file['tmp_name'];

        /*
        |--------------------------------------------------------------------------
        | CEK MIME ASLI
        |--------------------------------------------------------------------------
        */

        $finfo = new finfo(FILEINFO_MIME_TYPE);

        $mime = $finfo->file($tmp_name);

        $allowedMime = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!in_array($mime, $allowedMime, true)) {
            die(
                "Format gambar tidak diperbolehkan. " .
                "Gunakan JPG, JPEG, PNG, atau WEBP."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PASTIKAN FILE BENAR-BENAR GAMBAR
        |--------------------------------------------------------------------------
        */

        if (@getimagesize($tmp_name) === false) {
            die("File yang dipilih bukan gambar yang valid.");
        }

        /*
        |--------------------------------------------------------------------------
        | BACA BINARY GAMBAR
        |--------------------------------------------------------------------------
        */

        $gambar = file_get_contents($tmp_name);

        if ($gambar === false) {
            die("Gagal membaca file gambar.");
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE DENGAN GAMBAR
        |--------------------------------------------------------------------------
        |
        | Jangan gunakan mysqli_real_escape_string()
        | terhadap binary gambar.
        |
        | Gunakan prepared statement + send_long_data().
        |
        */

        $sql = "
            UPDATE buku
            SET
                judul = ?,
                id_kategori = ?,
                gambar = ?,
                sinopsis = ?
            WHERE id_buku = ?
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die(
                "Prepare gagal: " .
                htmlspecialchars($conn->error)
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Parameter
        |--------------------------------------------------------------------------
        |
        | s = judul
        | i = kategori
        | b = gambar
        | s = sinopsis
        | i = id buku
        |
        */

        $null = null;

        $stmt->bind_param(
            "sibsi",
            $judul,
            $id_kategori,
            $null,
            $sinopsis,
            $id
        );


        /*
        |--------------------------------------------------------------------------
        | KIRIM BINARY PNG/JPG/WEBP
        |--------------------------------------------------------------------------
        */

        $stmt->send_long_data(
            2,
            $gambar
        );


        /*
        |--------------------------------------------------------------------------
        | EXECUTE
        |--------------------------------------------------------------------------
        */

        if (!$stmt->execute()) {

            die(
                "Gagal mengubah data buku:<br><br>" .
                htmlspecialchars($stmt->error)
            );
        }

        $stmt->close();

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE TANPA MENGGANTI GAMBAR
    |--------------------------------------------------------------------------
    */

    else {

        $sql = "
            UPDATE buku
            SET
                judul = ?,
                id_kategori = ?,
                sinopsis = ?
            WHERE id_buku = ?
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die(
                "Prepare gagal: " .
                htmlspecialchars($conn->error)
            );
        }

        $stmt->bind_param(
            "sisi",
            $judul,
            $id_kategori,
            $sinopsis,
            $id
        );


        if (!$stmt->execute()) {

            die(
                "Gagal mengubah data buku:<br><br>" .
                htmlspecialchars($stmt->error)
            );
        }

        $stmt->close();
    }


    /*
    |--------------------------------------------------------------------------
    | BERHASIL
    |--------------------------------------------------------------------------
    */

    header(
        "Location: tampil.buku.php?updated=1"
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| AMBIL DATA BUKU
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        id_buku,
        id_admin,
        id_petugas,
        judul,
        id_kategori,
        gambar,
        sinopsis
    FROM buku
    WHERE id_buku = ?
    LIMIT 1
");

if (!$stmt) {
    die(
        "Prepare SELECT gagal: " .
        htmlspecialchars($conn->error)
    );
}

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$result = $stmt->get_result();

$buku = $result->fetch_assoc();

$stmt->close();


if (!$buku) {
    die("Data buku tidak ditemukan.");
}


/*
|--------------------------------------------------------------------------
| AMBIL DATA KATEGORI
|--------------------------------------------------------------------------
*/

$query_kategori = $conn->query("
    SELECT
        id_kategori,
        nama_kategori
    FROM kategori
    ORDER BY nama_kategori ASC
");

if (!$query_kategori) {
    die(
        "Gagal mengambil kategori: " .
        htmlspecialchars($conn->error)
    );
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Data Buku</title>

    <link
        rel="stylesheet"
        href="../style.css"
    >

    <style>

        /*
        |--------------------------------------------------------------------------
        | GAMBAR LAMA
        |--------------------------------------------------------------------------
        |
        | Tidak crop.
        | Ukuran mengikuti rasio asli.
        |
        */

        .book-image-preview {

            max-width: 220px;
            max-height: 300px;

            width: auto;
            height: auto;

            object-fit: contain;
            object-position: center;

            display: block;

            border-radius: 8px;

            border: 1px solid #ddd;

            background: #f5f5f5;

        }


        /*
        |--------------------------------------------------------------------------
        | PREVIEW GAMBAR BARU
        |--------------------------------------------------------------------------
        */

        .new-image-preview {

            display: none;

            width: 220px;
            min-height: 180px;
            max-height: 320px;

            margin-top: 10px;

            align-items: center;
            justify-content: center;

            overflow: hidden;

            border: 2px dashed #bbb;

            border-radius: 8px;

            background: #f5f5f5;

        }


        .new-image-preview img {

            max-width: 100%;
            max-height: 300px;

            width: auto;
            height: auto;

            object-fit: contain;

            display: block;

        }


        .success-message {

            padding: 12px 15px;

            margin: 15px 0;

            border-radius: 8px;

            background: #e8f7e8;

            color: #267026;

        }

    </style>

</head>


<body>


<h1>Edit Data Buku</h1>


<div class="page-head">

    <div></div>

    <a
        class="btn btn-light"
        href="../dashboard.<?= htmlspecialchars(
            $_SESSION['role'] ?? '',
            ENT_QUOTES,
            'UTF-8'
        ); ?>.php"
    >
        ← Kembali ke Dashboard
    </a>

</div>


<?php if (isset($_GET['updated'])): ?>

    <div class="success-message">

        Data buku berhasil diperbarui.

    </div>

<?php endif; ?>


<form
    method="POST"
    enctype="multipart/form-data"
>


    <!-- ID BUKU -->

    <label>ID Buku</label>

    <br>

    <input
        type="number"
        value="<?= (int) $buku['id_buku']; ?>"
        readonly
    >

    <br><br>


    <!-- AKUN LOGIN -->

    <label>Akun Pengelola</label>

    <br>

    <input
        type="text"
        value="<?= htmlspecialchars(
            current_user_label(),
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
        readonly
    >

    <br><br>


    <!-- JUDUL -->

    <label>Judul Buku</label>

    <br>

    <input
        type="text"
        name="judul"
        value="<?= htmlspecialchars(
            $buku['judul'] ?? '',
            ENT_QUOTES,
            'UTF-8'
        ); ?>"
        required
    >

    <br><br>


    <!-- KATEGORI -->

    <label>Kategori</label>

    <br>

    <select
        name="id_kategori"
        required
    >

        <option value="">
            -- Pilih Kategori --
        </option>


        <?php while (
            $kategori =
            $query_kategori->fetch_assoc()
        ): ?>

            <option
                value="<?= (int) $kategori['id_kategori']; ?>"
                <?= (
                    (int) $kategori['id_kategori']
                    ===
                    (int) $buku['id_kategori']
                )
                    ? 'selected'
                    : ''
                ?>
            >

                <?= htmlspecialchars(
                    $kategori['nama_kategori'],
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>

            </option>

        <?php endwhile; ?>

    </select>

    <br><br>


    <!-- GAMBAR LAMA -->

    <label>Gambar Buku Saat Ini</label>

    <br><br>


    <?php if (!empty($buku['gambar'])): ?>

        <img
            class="book-image-preview"
            src="gambar.php?id_buku=<?= (int) $buku['id_buku']; ?>"
            alt="Gambar Buku"
        >

    <?php else: ?>

        <p>
            Tidak ada gambar.
        </p>

    <?php endif; ?>


    <br>


    <!-- GAMBAR BARU -->

    <label>Ganti Gambar</label>

    <br>

    <input
        type="file"
        name="gambar"
        id="gambar"
        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
    >

    <br>

    <small>
        Kosongkan jika tidak ingin mengganti gambar.
        Maksimal 2 MB.
    </small>


    <div
        class="new-image-preview"
        id="newImagePreview"
    ></div>


    <br><br>


    <!-- SINOPSIS -->

    <label>Sinopsis</label>

    <br>

    <textarea
        name="sinopsis"
        rows="5"
        required
    ><?= htmlspecialchars(
        $buku['sinopsis'] ?? '',
        ENT_QUOTES,
        'UTF-8'
    ); ?></textarea>


    <br><br>


    <!-- BUTTON -->

    <button
        type="submit"
        name="update"
        class="btn"
    >
        💾 Simpan Perubahan
    </button>


    <a
        href="tampil.buku.php"
        class="btn btn-light"
    >
        Batal
    </a>


</form>


<script>

const gambarInput =
    document.getElementById('gambar');

const preview =
    document.getElementById('newImagePreview');


gambarInput.addEventListener(
    'change',
    function () {

        const file =
            this.files[0];


        if (!file) {

            preview.innerHTML = '';

            preview.style.display = 'none';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | UKURAN
        |--------------------------------------------------------------------------
        */

        if (
            file.size >
            2 * 1024 * 1024
        ) {

            alert(
                'Ukuran gambar maksimal 2 MB.'
            );

            this.value = '';

            preview.innerHTML = '';

            preview.style.display = 'none';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | FORMAT
        |--------------------------------------------------------------------------
        */

        const allowed = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];


        if (!allowed.includes(file.type)) {

            alert(
                'Format gambar harus JPG, JPEG, PNG, atau WEBP.'
            );

            this.value = '';

            preview.innerHTML = '';

            preview.style.display = 'none';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | PREVIEW
        |--------------------------------------------------------------------------
        */

        const reader =
            new FileReader();


        reader.onload =
            function (event) {

                preview.innerHTML = `
                    <img
                        src="${event.target.result}"
                        alt="Preview gambar baru"
                    >
                `;

                preview.style.display =
                    'flex';

            };


        reader.readAsDataURL(file);

    }
);

</script>


</body>

</html>