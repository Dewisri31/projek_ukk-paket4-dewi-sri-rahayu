# 📚 Sistem Informasi Perpustakaan Digital (UKK Paket 4)

Aplikasi Web **Sistem Informasi Perpustakaan** berbasis PHP dan MySQL yang dirancang untuk mengelola data data buku, peminjaman, pengembalian, denda, serta data pengguna berdasarkan hak akses masing-masing.

---

## 🛠️ Fitur Utama

- **Multi-Role Authentication**: Mendukung 3 tingkatan hak akses (Admin, Petugas, dan Anggota).
- **Manajemen Data Utama (CRUD)**:
  - Data Buku, Detail Buku, Kategori, & Penerbit
  - Data Pengguna (Admin, Petugas, & Anggota)
- **Sistem Transaksi Perpustakaan**:
  - Pengajuan & Persetujuan (Approval) Peminjaman Buku
  - Transaksi Pengembalian Buku
  - Perhitungan & Pengelolaan Denda Otomatis
  - Histori Transaksi Peminjaman & Pengembalian

---

## 🔑 Akun Demo / Hak Akses

Berikut adalah kredensial bawaan yang dapat digunakan untuk menguji aplikasi:

| Role | Username | Password |
| :--- | :--- | :--- |
| **Admin** | `dewi` | `123456` |
| **Petugas** | `Nikitt` | `1212` |
| **Anggota** | `sangkuriang` | `sangkuriang` |

---

## 📁 Struktur Direktori Proyek

```text
projek_ukk-paket4-dewi-sri-rahayu/
├── admin/            # Manajemen CRUD Admin
├── anggota/          # Manajemen CRUD Anggota
├── buku/             # Manajemen CRUD & Katalog Buku
├── database/         # Script SQL Database & Otomatisasi (Trigger/Procedure)
├── denda/            # Pengelolaan Denda
├── detail_buku/      # Rincian Data Buku
├── histori/          # Catatan Histori Transaksi
├── kategori/         # Pengelolaan Kategori Buku
├── peminjaman/       # Pengajuan & Acc Peminjaman
├── penerbit/         # Pengelolaan Data Penerbit
├── pengembalian/     # Pengelolaan Pengembalian Buku
├── petugas/          # Manajemen CRUD Petugas
├── auth.php          # Validasi Autentikasi & Session
├── connection.php    # Koneksi Database MySQL
├── dashboard.*.php   # Halaman Dashboard per Role
├── login.php         # Halaman Login Utama
├── register.php      # Pendaftaran Anggota Baru
└── style.css         # Styling CSS Utama
```

---

## 🚀 Cara Instalasi & Menjalankan Proyek

1. **Clone / Download Repositori**
   Unduh proyek ini ke dalam folder server lokal Anda (misalnya `htdocs` untuk XAMPP).

2. **Import Database**
   - Buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
   - Buat database baru dengan nama `perpustakaan_ukk_dewi`.
   - Import file SQL yang berada di dalam folder `/database/`:
     - `perpustakaan_ukk_dewi.sql`
     - *(Opsional)* Jalankan script revisi/otomatisasi `revisi_denda_otomatis.sql` dan `update_otomatis_akun_login.sql` jika diperlukan.

3. **Konfigurasi Koneksi**
   - Buka file `connection.php`.
   - Sesuaikan konfigurasi host, username, password, dan nama database MySQL Anda.

4. **Jalankan Aplikasi**
   - Buka browser dan akses `http://localhost/projek_ukk-paket4-dewi-sri-rahayu-main/login.php`.
   - Gunakan akun demo di atas untuk mulai mencoba.

---

## 👤 Pembuat
**Dewi Sri Rahayu**  
*Projek UKK Paket 4 - Sistem Informasi Perpustakaan*
