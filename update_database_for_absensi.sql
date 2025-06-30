-- Menambahkan kolom 'kelas' ke tabel 'praktikan'
-- Kolom ini akan menyimpan nama kelas tempat praktikan terdaftar, contoh: 'IF Pagi 6'
ALTER TABLE `praktikan` ADD `kelas` VARCHAR(50) NOT NULL AFTER `prodi`;

-- Membuat tabel baru untuk menyimpan data absensi kehadiran
CREATE TABLE `absensi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_jadwal` int(11) NOT NULL,
  `id_praktikan` varchar(10) NOT NULL,
  `pertemuan_ke` int(2) NOT NULL,
  `tanggal_absensi` date NOT NULL,
  `status_kehadiran` enum('Hadir','Izin','Sakit','Alpha') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unik_absensi` (`id_jadwal`,`id_praktikan`,`pertemuan_ke`),
  KEY `id_jadwal` (`id_jadwal`),
  KEY `id_praktikan` (`id_praktikan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Catatan:
-- 'unik_absensi' memastikan seorang mahasiswa hanya bisa diabsen satu kali per jadwal per pertemuan.
-- Anda perlu menjalankan query ini di database 'projek_akhir' Anda melalui phpMyAdmin atau tools lainnya. 