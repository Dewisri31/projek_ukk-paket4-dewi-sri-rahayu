<?php require_once '../auth.php'; require_master_staff(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
<link rel="stylesheet" href="../style.css">
    <meta charset="UTF-8">
    <title>Tambah Data Histori</title>
</head>

<body>

    <h1>Tambah Data Histori</h1>

    <form action="tambah.histori.php" method="POST">

        <label>ID DETAIL</label>
        <br>
        <input type="number" name="id_detail" required>

        <br><br>

        <label>ID ADMIN</label>
        <br>
        <input value="<?= htmlspecialchars(current_user_label()) ?>" readonly>

        <br><br>

        <label>ID PEMINJAMAN</label>
        <br>
        <input type="number" name="id_peminjaman" required>

        <br><br>

        <label>ID BUKU</label>
        <br>
        <input type="number" name="id_buku" required>

        <br><br>

        <label>JUMLAH</label>
        <br>
        <input type="number" name="jumlah" required>

        <br><br>

        <label>DENDA</label>
        <br>
        <input type="number" name="denda" value="0" required>

        <br><br>

        <button type="submit">
            Simpan
        </button>

        <button type="reset">
            Reset
        </button>

        <a href="tampil.histori.php">
            <button type="button">
                Kembali
            </button>
        </a>

    </form>

</body>

</html>