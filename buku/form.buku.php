
<?php
require_once '../auth.php';
require_staff();
require_once '../connection.php';

$query_kategori = $conn->query("
    SELECT id_kategori, nama_kategori
    FROM kategori
    ORDER BY nama_kategori ASC
");

if (!$query_kategori) {
    die("Gagal mengambil data kategori: " . htmlspecialchars($conn->error));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Buku</title>
    <link rel="stylesheet" href="../style.css">

 
<style>
    :root {
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --border: #dbe2ea;
        --focus: rgba(79, 70, 229, 0.15);
    }

    .form-group {
        margin-bottom: 22px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--text-main);
    }

    .form-group input:not([type="file"]),
    .form-group select,
    .form-group textarea {
        width: 100%;
        box-sizing: border-box;
        padding: 12px 14px;
        font: inherit;
        color: var(--text-main);
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 10px;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--focus);
    }

    .form-group input[readonly] {
        background: #f1f5f9;
        color: #475569;
    }

    .form-group input[type="file"] {
        width: 100%;
        box-sizing: border-box;
        padding: 10px;
        color: var(--text-main);
        background: transparent;
        border: 1px dashed var(--border);
        border-radius: 10px;
    }

    .form-group input[type="file"]::file-selector-button {
        padding: 8px 12px;
        margin-right: 10px;
        color: #ffffff;
        background: var(--primary);
        border: none;
        border-radius: 7px;
        cursor: pointer;
    }

    .form-group input[type="file"]::file-selector-button:hover {
        background: var(--primary-hover);
    }

    .form-group small {
        display: block;
        margin-top: 7px;
        color: var(--text-muted);
        font-size: 12px;
    }

    .preview-box {
        width: 220px;
        height: 300px;
        max-width: 100%;
        box-sizing: border-box;
        border: 2px dashed var(--border);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-top: 12px;
        background: transparent;
    }

    .preview-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .preview-text {
        color: var(--text-muted);
        text-align: center;
        padding: 20px;
    }

    .form-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 24px;
    }

    .form-actions .btn {
        border-radius: 9px;
        transition: background .2s, color .2s;
    }

    .form-actions button[type="submit"] {
        color: #ffffff;
        background: var(--primary);
        border-color: var(--primary);
    }

    .form-actions button[type="submit"]:hover {
        background: var(--primary-hover);
    }

    .form-actions .btn-light {
        color: #334155;
        background: #f1f5f9;
        border-color: #e2e8f0;
    }

    .form-actions .btn-light:hover {
        background: #e2e8f0;
    }

    @media (max-width: 600px) {
        .preview-box {
            max-width: 220px;
        }

        .form-actions {
            gap: 8px;
        }
    }
</style>
</head>


<body>

<div class="page-head">
    <div>
        <h1>📚 Tambah Buku</h1>
        <p class="muted">Tambahkan data buku baru.</p>
    </div>

    <a class="btn btn-light"
       href="../dashboard.<?= htmlspecialchars($_SESSION['role'], ENT_QUOTES, 'UTF-8') ?>.php">
        ← Dashboard
    </a>
</div>

<div class="card">

    <form action="tambah.buku.php"
          method="POST"
          enctype="multipart/form-data">

        <!-- ID BUKU OTOMATIS -->
        <div class="form-group">
            <label for="id_buku">ID Buku</label>

            <input
                type="text"
                id="id_buku"
                value="Otomatis oleh sistem"
                readonly
            >

            <small>
                ID Buku akan dibuat otomatis saat data disimpan.
            </small>
        </div>

        <!-- JUDUL -->
        <div class="form-group">
            <label for="judul">Judul Buku</label>

            <input
                type="text"
                id="judul"
                name="judul"
                required
                maxlength="100"
                placeholder="Masukkan judul buku"
            >
        </div>

        <!-- KATEGORI -->
        <div class="form-group">
            <label for="id_kategori">Kategori</label>

            <select id="id_kategori" name="id_kategori" required>
                <option value="">-- Pilih Kategori --</option>

                <?php while ($kategori = $query_kategori->fetch_assoc()): ?>
                    <option value="<?= (int)$kategori['id_kategori'] ?>">
                        <?= htmlspecialchars(
                            $kategori['nama_kategori'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- GAMBAR -->
        <div class="form-group">
            <label for="gambar">Gambar / Sampul Buku</label>

            <input
                type="file"
                name="gambar"
                id="gambar"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                required
            >

            <small>
                Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.
            </small>

            <div class="preview-box" id="previewBox">
                <div class="preview-text">
                    Preview gambar akan muncul di sini
                </div>
            </div>
        </div>

        <!-- SINOPSIS -->
        <div class="form-group">
            <label for="sinopsis">Sinopsis</label>

            <textarea
                id="sinopsis"
                name="sinopsis"
                rows="7"
                required
                placeholder="Masukkan sinopsis buku..."
            ></textarea>
        </div>

     <div class="form-group">
    <label>Akun Pengelola</label>

    <input
        type="text"
        value="<?= htmlspecialchars(current_user_label()) ?>"
        readonly
    >

    <small>
        Pengelola otomatis mengikuti akun yang sedang login.
    </small>
</div>

        <!-- TOMBOL -->
        <div class="form-actions">
            <button type="submit" class="btn">
                💾 Simpan Buku
            </button>

            <button type="reset" class="btn btn-light">
                🔄 Reset
            </button>

            <a href="tampil.buku.php" class="btn btn-light">
                Batal
            </a>
        </div>

    </form>

</div>

<script>
const inputGambar = document.getElementById('gambar');
const previewBox = document.getElementById('previewBox');

const previewAwal = `
    <div class="preview-text">
        Preview gambar akan muncul di sini
    </div>
`;

inputGambar.addEventListener('change', function () {
    const file = this.files[0];

    if (!file) {
        resetPreview();
        return;
    }

    const allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ];

    if (!allowedTypes.includes(file.type)) {
        alert('Format gambar harus JPG, JPEG, PNG, atau WEBP.');
        this.value = '';
        resetPreview();
        return;
    }

    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran gambar maksimal 2 MB.');
        this.value = '';
        resetPreview();
        return;
    }

    const reader = new FileReader();

    reader.onload = function (event) {
        const img = document.createElement('img');

        img.src = event.target.result;
        img.alt = 'Preview Sampul Buku';

        previewBox.replaceChildren(img);
    };

    reader.onerror = function () {
        alert('Gagal membaca gambar.');
        resetPreview();
    };

    reader.readAsDataURL(file);
});

function resetPreview() {
    previewBox.innerHTML = previewAwal;
}

document.querySelector('form').addEventListener('reset', function () {
    setTimeout(resetPreview, 0);
});
</script>

</body>
</html>
