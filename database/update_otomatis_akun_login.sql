-- MIGRASI UNTUK DATABASE YANG SUDAH TERPASANG
-- Jalankan file ini jika database perpustakaan_ukk_dewi sudah ada dan tidak ingin import ulang seluruh SQL.

ALTER TABLE anggota ADD COLUMN IF NOT EXISTS id_petugas INT(11) DEFAULT NULL;
ALTER TABLE buku ADD COLUMN IF NOT EXISTS id_petugas INT(11) DEFAULT NULL;
ALTER TABLE detail_buku ADD COLUMN IF NOT EXISTS id_petugas INT(11) DEFAULT NULL;
ALTER TABLE kategori ADD COLUMN IF NOT EXISTS id_petugas INT(11) DEFAULT NULL;
ALTER TABLE penerbit ADD COLUMN IF NOT EXISTS id_petugas INT(11) DEFAULT NULL;
ALTER TABLE histori ADD COLUMN IF NOT EXISTS id_petugas INT(11) DEFAULT NULL;
ALTER TABLE denda ADD COLUMN IF NOT EXISTS id_admin INT(11) DEFAULT NULL;
ALTER TABLE denda ADD COLUMN IF NOT EXISTS id_petugas INT(11) DEFAULT NULL;

-- Kolom petugas/admin sekarang diisi otomatis dari session akun yang login.
-- Tidak perlu mengisi id_admin atau id_petugas secara manual dari form.
