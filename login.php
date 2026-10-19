<?php

session_start();

include 'connection.php';

$error = '';

/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

if (isset($_POST['login'])) {

    $username = trim(
        $_POST['username'] ?? ''
    );

    $password = $_POST['password'] ?? '';


    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        SELECT *
        FROM admin
        WHERE username = ?
        AND password = ?
        LIMIT 1
    ");

    $stmt->bind_param(
        'ss',
        $username,
        $password
    );

    $stmt->execute();

    $data = $stmt
        ->get_result()
        ->fetch_assoc();


    if ($data) {

        $_SESSION = [
            'login' => true,
            'role' => 'admin',
            'id_admin' => $data['id_admin'],
            'nama' => $data['nama_admin'],
            'username' => $data['username']
        ];

        header(
            'Location: dashboard.admin.php'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | PETUGAS
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        SELECT *
        FROM petugas
        WHERE username = ?
        AND password = ?
        LIMIT 1
    ");

    $stmt->bind_param(
        'ss',
        $username,
        $password
    );

    $stmt->execute();

    $data = $stmt
        ->get_result()
        ->fetch_assoc();


    if ($data) {

        $_SESSION = [
            'login' => true,
            'role' => 'petugas',
            'id_petugas' => $data['id_petugas'],
            'nama' => $data['nama_petugas'],
            'username' => $data['username']
        ];

        header(
            'Location: dashboard.petugas.php'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | ANGGOTA
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        SELECT *
        FROM anggota
        WHERE username = ?
        AND password = ?
        LIMIT 1
    ");

    $stmt->bind_param(
        'ss',
        $username,
        $password
    );

    $stmt->execute();

    $data = $stmt
        ->get_result()
        ->fetch_assoc();


    if ($data) {

        $_SESSION = [
            'login' => true,
            'role' => 'anggota',
            'id_anggota' => $data['id_anggota'],
            'nama' => $data['nama_anggota'],
            'username' => $data['username']
        ];

        header(
            'Location: dashboard.anggota.php'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | GAGAL LOGIN
    |--------------------------------------------------------------------------
    */

    $error = 'Username atau password salah.';
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

    <title>Login Perpustakaan</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>

<body class="login-page">

<div class="login-box">

    <div class="login-icon">
        📚
    </div>

    <h1>Perpustakaan</h1>

    <p class="muted">
        Silakan masuk sesuai akun Anda
    </p>


    <?php if ($error): ?>

        <div class="error">

            <?= htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            ); ?>

        </div>

    <?php endif; ?>


    <form method="post">

        <label>
            Username
        </label>

        <input
            type="text"
            name="username"
            autocomplete="username"
            required
            autofocus
        >


        <label>
            Password
        </label>

        <input
            type="password"
            name="password"
            autocomplete="current-password"
            required
        >


        <button
            class="btn-full"
            type="submit"
            name="login"
        >
            Masuk
        </button>

    </form>


    <div class="login-register-link">

        <a
            class="back-dashboard"
            href="register.php"
        >
            Belum punya akun? Registrasi Anggota
        </a>

    </div>

</div>

</body>

</html>