<?php
require_once '../auth.php'; require_staff();
?>
<!DOCTYPE html>
<html lang="id">

<head>
<link rel="stylesheet" href="../style.css">
    <meta charset="UTF-8">
    <title>Tambah Data Penerbit</title>
</head>

<body>
<div class="page-head"><div><h1>📝 Form Penerbit</h1><p class="muted">Lengkapi data dengan benar.</p></div><a class="btn btn-light" href="<?='../dashboard.'.($_SESSION['role']??'').'.php'?>">← Kembali ke Dashboard</a></div>

    <h1>Tambah Data Penerbit</h1>

    <form action="tambah.penerbit.php" method="POST">

        <label>ID PENERBIT</label>
        <br>
        <input type="number" name="id_penerbit" required>

        <br><br>

        <label>Akun Pengelola</label><br><input value="<?= htmlspecialchars(current_user_label()) ?>" readonly><br><br>

        <label>KODE PENERBIT</label>
        <br>
        <input type="text" name="kode_penerbit" required>

        <br><br>

        <label>NAMA PENERBIT</label>
        <br>
        <input type="text" name="nama_penerbit" required>

        <br><br>

        <button type="submit">
            Simpan
        </button>

        <button type="reset">
            Reset
        </button>

        <a href="tampil.penerbit.php">
            <button type="button">
                Kembali
            </button>
        </a>

    </form>

</body>

</html>