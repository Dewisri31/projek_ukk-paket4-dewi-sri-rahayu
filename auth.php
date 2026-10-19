<?php
session_start();

$BASE_URL = '/' . basename(__DIR__);

function go_home(string $file): void {
    global $BASE_URL;
    header('Location: ' . $BASE_URL . '/' . $file);
    exit;
}

function require_login(): void {
    if (empty($_SESSION['login'])) {
        go_home('login.php');
    }
}

function role(): string { return $_SESSION['role'] ?? ''; }
function current_user_id(): int { return match (role()) { 'admin'=>(int)($_SESSION['id_admin']??0), 'petugas'=>(int)($_SESSION['id_petugas']??0), 'anggota'=>(int)($_SESSION['id_anggota']??0), default=>0 }; }
function current_user_name(): string { return (string)($_SESSION['nama'] ?? ''); }
function current_user_label(): string { return ucfirst(role()) . ' — ' . current_user_name(); }


function dashboard_file(): string {
    return match (role()) {
        'admin' => 'dashboard.admin.php',
        'petugas' => 'dashboard.petugas.php',
        'anggota' => 'dashboard.anggota.php',
        default => 'login.php'
    };
}

// Admin hanya mengelola beberapa master data.
function require_admin(): void {
    require_login();
    if (role() !== 'admin') {
        go_home(dashboard_file());
    }
}

// Petugas menjadi pelaksana utama seluruh kegiatan operasional.
function require_petugas(): void {
    require_login();
    if (role() !== 'petugas') {
        go_home(dashboard_file());
    }
}

// Admin dan Petugas sama-sama dapat memproses persetujuan peminjaman.
function require_approval_staff(): void {
    // Persetujuan peminjaman hanya menjadi tugas Petugas.
    require_petugas();
}

// Halaman master yang boleh dikelola Admin maupun Petugas.
function require_master_staff(): void {
    require_login();
    if (!in_array(role(), ['admin', 'petugas'], true)) {
        go_home(dashboard_file());
    }
}

function require_detail_petugas(): void {
    require_petugas();
}

// Alias lama agar file yang belum diubah tetap aman.
function require_staff(): void {
    require_master_staff();
}

function require_anggota(): void {
    require_login();
    if (role() !== 'anggota') {
        go_home(dashboard_file());
    }
}

function require_book_view(): void {
    require_login();
}
?>
