# Sistem Informasi Laboratorium - Projek Akhir

Sistem informasi laboratorium ini dibuat menggunakan PHP, MySQL, HTML, dan CSS. Aplikasi ini digunakan untuk mengelola data laboratorium, seperti praktikan, laboran, kelas, jadwal praktikum, absensi, dan lain-lain.

## Fitur Utama

- **Login Multi-Role**: Autentikasi user dengan role (admin, kepala lab, laboran) dan dashboard sesuai hak akses.
- **Dashboard Dinamis**: Statistik dan grafik interaktif (Chart.js) untuk data praktikan, kelas, mata praktikum, dan asisten, langsung dari database.
- **Manajemen Praktikan**: Tambah, edit, hapus, pencarian, show entries, pagination, dan select all data praktikan.
- **Manajemen Laboran & Asisten Praktikum**: CRUD data laboran dan asisten (khusus admin).
- **Manajemen Kelas & Mata Praktikum**: CRUD data kelas dan mata praktikum.
- **Jadwal Praktikum**: Pengelolaan jadwal, perubahan jadwal, dan absensi kehadiran.
- **Absensi Kehadiran**: Pencatatan kehadiran praktikan dan asisten.
- **Pencarian & Pagination**: Fitur pencarian real-time, show entries, dan pagination pada tabel data.
- **Cetak Data**: Fitur print/cetak pada halaman jadwal, perubahan jadwal, dan praktikan.
- **Hak Akses Menu**: Menu sidebar otomatis menyesuaikan role (misal: laboran tidak bisa akses menu laboran, kepala lab tidak bisa akses menu laboran, dsb).

## Struktur Folder & File

- `index.html` : Halaman utama/landing page.
- `dashboard.php`, `dashboard.css` : Dashboard utama (admin).
- `kepala_lab_dashboard.php` : Dashboard kepala laboratorium (dinamis, Chart.js, tanpa menu laboran).
- `laboran_dashboard.php` : Dashboard laboran (dinamis, Chart.js, tanpa menu ruang laboratorium & laboran).
- `db_connect.php` : Koneksi ke database MySQL.
- `praktikan.php`, `praktikan.css`, `add_praktikan.php`, `edit_praktikan.php`, `update_praktikan.php`, `delete_praktikan.php` : Manajemen data praktikan.
- `laboran.php`, `laboran.css`, `add_laboran.php`, `update_laboran.php`, `delete_laboran.php` : Manajemen data laboran (khusus admin).
- `asisten_praktikum.php`, `asisten_praktikum.css`, `add_asisten_praktikum.php` : Manajemen asisten praktikum.
- `kelas.php`, `kelas.css`, `add_kelas.php`, `update_kelas.php`, `delete_kelas.php` : Manajemen kelas.
- `mata_praktikum.php`, `mata_praktikum.css`, `add_mata_praktikum.php`, `edit_mata_praktikum.php`, `update_mata_praktikum.php`, `delete_mata_praktikum.php` : Manajemen mata praktikum.
- `jadwal_praktikum.php`, `jadwal_praktikum.css`, `add_jadwal_praktikum.php`, `update_jadwal_praktikum.php`, `delete_jadwal_praktikum.php` : Manajemen jadwal praktikum.
- `perubahan_jadwal.php`, `delete_perubahan_jadwal.php` : Fitur perubahan jadwal.
- `absensi_kehadiran.php`, `absensi_kehadiran_asisten.php`, `absensi_kehadiran.css` : Fitur absensi kehadiran.
- `ruang_laboratorium.html`, `ruang_laboratorium.css` : Data ruang laboratorium (khusus admin/kepala lab).
- `style.css` : CSS global.
- `unibba-logo.png` : Logo institusi.
- `create_table_*.sql` : File SQL untuk membuat tabel database.

## Cara Instalasi & Menjalankan

1. **Clone/download** proyek ini ke folder server lokal Anda (misal: `htdocs` di XAMPP).
2. **Import database**:  
   - Gunakan file SQL (`create_table_*.sql`) untuk membuat tabel yang diperlukan di MySQL.
3. **Konfigurasi koneksi database**:  
   - Edit file `db_connect.php` sesuai dengan konfigurasi database Anda (host, user, password, nama database).
4. **Jalankan aplikasi**:  
   - Buka `index.html` untuk login, lalu akan diarahkan ke dashboard sesuai role.

## Hak Akses & Navigasi

- **Admin**: Akses penuh ke semua menu, dashboard di `dashboard.php`.
- **Kepala Lab**: Dashboard di `kepala_lab_dashboard.php`, tidak ada menu laboran.
- **Laboran**: Dashboard di `laboran_dashboard.php`, tidak ada menu ruang laboratorium & laboran.
- Setiap menu sidebar otomatis menyesuaikan role user yang login.

## Catatan

- Pastikan server Apache dan MySQL sudah berjalan (misal menggunakan XAMPP).
- Semua fitur CRUD, search, pagination, select all, dan cetak sudah terintegrasi database.
- Tampilan sudah responsif dan konsisten sesuai desain referensi skripsi.
- Statistik dashboard menggunakan Chart.js dan data real-time dari database.