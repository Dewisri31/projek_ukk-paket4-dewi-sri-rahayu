<?php

session_start();
include 'connection.php';

/*
|--------------------------------------------------------------------------
| Jika sudah login, langsung ke dashboard sesuai role
|--------------------------------------------------------------------------
*/

if (!empty($_SESSION['login'])) {

    $role = $_SESSION['role'] ?? '';

    if ($role === 'admin') {
        header('Location: dashboard.admin.php');
        exit;
    }

    if ($role === 'petugas') {
        header('Location: dashboard.petugas.php');
        exit;
    }

    if ($role === 'anggota') {
        header('Location: dashboard.anggota.php');
        exit;
    }
}

$error = '';

/*
|--------------------------------------------------------------------------
| PROSES REGISTRASI ANGGOTA
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = trim($_POST['nama_anggota'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    $jk = $_POST['jenis_kelamin'] ?? '';

    $tempat = trim($_POST['tempat_lahir'] ?? '');
    $tgl = $_POST['tanggal_lahir'] ?? '';

    $tel = trim($_POST['telpon'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');


    /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */

    if (
        $nama === '' ||
        $username === '' ||
        $password === '' ||
        $password2 === '' ||
        $jk === '' ||
        $tgl === '' ||
        $tel === '' ||
        $alamat === ''
    ) {

        $error = 'Semua data wajib diisi.';

    } elseif (!in_array($jk, ['pria', 'wanita'], true)) {

        $error = 'Jenis kelamin tidak valid.';

    } elseif (
        !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $tgl) ||
        !checkdate((int)substr($tgl, 5, 2), (int)substr($tgl, 8, 2), (int)substr($tgl, 0, 4)) ||
        $tgl > '2011-12-31'
    ) {

        $error = 'Tanggal lahir maksimal 31 Desember 2011.';

    } elseif ($password !== $password2) {

        $error = 'Konfirmasi password tidak sama.';

    } elseif (strlen($password) < 6) {

        $error = 'Password minimal 6 karakter.';

    } else {


        /*
        |--------------------------------------------------------------------------
        | CEK USERNAME
        |--------------------------------------------------------------------------
        |
        | Username tidak boleh sama dengan akun:
        | - anggota
        | - admin
        | - petugas
        |
        */

        $cek = $conn->prepare("
            SELECT username
            FROM anggota
            WHERE username = ?

            UNION ALL

            SELECT username
            FROM admin
            WHERE username = ?

            UNION ALL

            SELECT username
            FROM petugas
            WHERE username = ?

            LIMIT 1
        ");

        if (!$cek) {
            die(
                'Gagal memeriksa username: ' .
                htmlspecialchars($conn->error)
            );
        }

        $cek->bind_param(
            'sss',
            $username,
            $username,
            $username
        );

        $cek->execute();

        $hasilCek = $cek->get_result();

        /*
        |--------------------------------------------------------------------------
        | USERNAME SUDAH ADA
        |--------------------------------------------------------------------------
        */

        if ($hasilCek->num_rows > 0) {

            $error =
                'Username sudah digunakan. ' .
                'Silakan gunakan username lain.';

        } else {


            /*
            |--------------------------------------------------------------------------
            | SIMPAN ANGGOTA
            |--------------------------------------------------------------------------
            |
            | id_admin  = NULL
            | id_petugas = NULL
            |
            | Karena akun dibuat langsung oleh anggota.
            |
            */

            $stmt = $conn->prepare("
                INSERT INTO anggota
                (
                    id_admin,
                    id_petugas,
                    nama_anggota,
                    username,
                    password,
                    jenis_kelamin,
                    tempat_lahir,
                    tanggal_lahir,
                    telpon,
                    alamat
                )
                VALUES
                (
                    NULL,
                    NULL,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                )
            ");

            if (!$stmt) {
                die(
                    'Gagal menyiapkan registrasi: ' .
                    htmlspecialchars($conn->error)
                );
            }

            $stmt->bind_param(
                'ssssssss',
                $nama,
                $username,
                $password,
                $jk,
                $tempat,
                $tgl,
                $tel,
                $alamat
            );


            /*
            |--------------------------------------------------------------------------
            | EKSEKUSI REGISTRASI
            |--------------------------------------------------------------------------
            */

            if ($stmt->execute()) {

                /*
                |--------------------------------------------------------------------------
                | AMBIL ID ANGGOTA YANG BARU DIBUAT
                |--------------------------------------------------------------------------
                */

                $id_anggota = $conn->insert_id;


                /*
                |--------------------------------------------------------------------------
                | LANGSUNG LOGIN SEBAGAI ANGGOTA
                |--------------------------------------------------------------------------
                */

                $_SESSION = [
                    'login' => true,
                    'role' => 'anggota',
                    'id_anggota' => $id_anggota,
                    'nama' => $nama,
                    'username' => $username
                ];


                /*
                |--------------------------------------------------------------------------
                | LANGSUNG KE DASHBOARD ANGGOTA
                |--------------------------------------------------------------------------
                */

                header(
                    'Location: dashboard.anggota.php'
                );

                exit;

            } else {

                $error =
                    'Registrasi gagal: ' .
                    htmlspecialchars($stmt->error);
            }

            $stmt->close();
        }

        $cek->close();
    }
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

    <title>Registrasi Anggota</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>

<body>

<div class="register-box">

    <div class="page-head register-head">

        <div>

            <h1>📚 Registrasi Anggota</h1>

            <p class="muted">
                Buat akun anggota perpustakaan.
            </p>

        </div>

    </div>


    <?php if ($error): ?>

        <div class="error">

            <?= htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            ); ?>

        </div>

    <?php endif; ?>


    <form
        method="POST"
        autocomplete="off"
    >

        <div class="register-grid">


            <!-- NAMA -->

            <div class="full">

                <label>

                    Nama Lengkap

                    <input
                        type="text"
                        name="nama_anggota"
                        value="<?= htmlspecialchars(
                            $_POST['nama_anggota'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        required
                    >

                </label>

            </div>


            <!-- USERNAME -->

            <div>

                <label>

                    Username

                    <input
                        type="text"
                        name="username"
                        value="<?= htmlspecialchars(
                            $_POST['username'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        autocomplete="username"
                        required
                    >

                </label>

            </div>


            <!-- JENIS KELAMIN -->

            <div>

                <label>

                    Jenis Kelamin

                    <select
                        name="jenis_kelamin"
                        required
                    >

                        <option value="">
                            -- Pilih --
                        </option>

                        <option
                            value="pria"
                            <?= (
                                ($_POST['jenis_kelamin'] ?? '')
                                === 'pria'
                            )
                                ? 'selected'
                                : ''
                            ?>
                        >
                            Pria
                        </option>

                        <option
                            value="wanita"
                            <?= (
                                ($_POST['jenis_kelamin'] ?? '')
                                === 'wanita'
                            )
                                ? 'selected'
                                : ''
                            ?>
                        >
                            Wanita
                        </option>

                    </select>

                </label>

            </div>


            <!-- PASSWORD -->

            <div>

                <label>

                    Password

                    <input
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        required
                    >

                </label>

                <div class="hint">
                    Minimal 6 karakter.
                </div>

            </div>


            <!-- KONFIRMASI PASSWORD -->

            <div>

                <label>

                    Konfirmasi Password

                    <input
                        type="password"
                        name="password2"
                        autocomplete="new-password"
                        required
                    >

                </label>

            </div>


            <!-- TEMPAT LAHIR -->

            <div>

                <label>

                    Tempat Lahir

                    <input
                        type="text"
                        name="tempat_lahir"
                        value="<?= htmlspecialchars(
                            $_POST['tempat_lahir'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        required
                    >

                </label>

            </div>


            <!-- TANGGAL LAHIR -->

            <div>

                <label>

                    Tanggal Lahir

                    <input
                        type="date"
                        name="tanggal_lahir"
                        max="2011-12-31"
                        value="<?= htmlspecialchars(
                            $_POST['tanggal_lahir'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        required
                    >

                </label>

            </div>


            <!-- TELEPON -->

            <div>

                <label>

                    No. Telepon

                    <input
                        type="text"
                        name="telpon"
                        value="<?= htmlspecialchars(
                            $_POST['telpon'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        required
                    >

                </label>

            </div>


            <!-- ALAMAT -->

            <div class="full">

                <label>

                    Alamat

                    <textarea
                        name="alamat"
                        rows="4"
                        required
                    ><?= htmlspecialchars(
                        $_POST['alamat'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?></textarea>

                </label>

            </div>

        </div>


        <!-- BUTTON -->

        <div class="form-actions">

            <button
                class="btn-register"
                type="submit"
            >
                ✓ Daftar & Masuk ke Dashboard
            </button>

            <a
                class="btn btn-light"
                href="index.php"
            >
                Sudah punya akun? Login
            </a>

        </div>

    </form>

</div>

</body>

</html>