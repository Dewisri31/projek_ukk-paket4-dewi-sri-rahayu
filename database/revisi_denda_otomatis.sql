-- Revisi denda otomatis
-- Jalankan pada database perpustakaan_ukk_dewi yang sudah ada.

ALTER TABLE denda ADD COLUMN IF NOT EXISTS id_admin INT(11) NULL AFTER id_denda;
ALTER TABLE denda ADD COLUMN IF NOT EXISTS id_petugas INT(11) NULL AFTER id_admin;

-- Denda Rp0 tidak lagi diperlukan karena denda hanya dibuat jika terlambat.
DELETE FROM denda WHERE total_denda = 0;

-- Pastikan tabel denda tetap dapat menyimpan jenis denda yang digunakan aplikasi.
ALTER TABLE denda MODIFY jenis_denda ENUM(''terlambat'',''rusak'',''hilang'') NOT NULL DEFAULT ''terlambat'';
ALTER TABLE denda MODIFY terlambat INT(11) NOT NULL DEFAULT 0;
ALTER TABLE denda MODIFY tarif_per_hari INT(11) NOT NULL DEFAULT 1000;
ALTER TABLE denda MODIFY total_denda INT(11) NOT NULL DEFAULT 0;
