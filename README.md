# Sistem Informasi Laboratorium - Projek Akhir

Sistem informasi laboratorium ini dibuat menggunakan PHP, MySQL, HTML, dan CSS. Aplikasi ini digunakan untuk mengelola data laboratorium, seperti praktikan, laboran, kelas, jadwal praktikum, absensi, dan lain-lain.

## Fitur Utama

- **Login & Dashboard**: Autentikasi user dan tampilan dashboard sesuai peran (kepala lab, laboran, dsb).
- **Manajemen Praktikan**: Tambah, edit, hapus, dan pencarian data praktikan.
- **Manajemen Laboran & Asisten Praktikum**: CRUD data laboran dan asisten.
- **Manajemen Kelas & Mata Praktikum**: CRUD data kelas dan mata praktikum.
- **Jadwal Praktikum**: Pengelolaan jadwal, perubahan jadwal, dan absensi kehadiran.
- **Absensi Kehadiran**: Pencatatan kehadiran praktikan.
- **Pencarian & Pagination**: Fitur pencarian real-time, show entries, dan pagination pada tabel data.
- **Checkbox Select All**: Memudahkan pemilihan banyak data sekaligus.

## Struktur Folder & File

- `index.html` : Halaman utama/landing page.
- `dashboard.html`, `dashboard.css` : Dashboard utama.
- `db_connect.php` : Koneksi ke database MySQL.
- `praktikan.php`, `praktikan.css`, `add_praktikan.php`, `edit_praktikan.php`, `update_praktikan.php`, `delete_praktikan.php` : Manajemen data praktikan.
- `laboran.php`, `laboran.css`, `add_laboran.php`, `update_laboran.php`, `delete_laboran.php` : Manajemen data laboran.
- `asisten_praktikum.php`, `asisten_praktikum.css`, `add_asisten_praktikum.php` : Manajemen asisten praktikum.
- `kelas.php`, `kelas.css`, `add_kelas.php`, `update_kelas.php`, `delete_kelas.php` : Manajemen kelas.
- `mata_praktikum.html`, `mata_praktikum.php`, `mata_praktikum.css`, `add_mata_praktikum.php`, `edit_mata_praktikum.php`, `update_mata_praktikum.php`, `delete_mata_praktikum.php` : Manajemen mata praktikum.
- `jadwal_praktikum.php`, `jadwal_praktikum.css`, `add_jadwal_praktikum.php`, `update_jadwal_praktikum.php`, `delete_jadwal_praktikum.php` : Manajemen jadwal praktikum.
- `perubahan_jadwal.php`, `delete_perubahan_jadwal.php` : Fitur perubahan jadwal.
- `absensi_kehadiran.html`, `absensi_kehadiran.css` : Fitur absensi kehadiran.
- `ruang_laboratorium.html`, `ruang_laboratorium.css` : Data ruang laboratorium.
- `kepala_lab_dashboard.html`, `kepala_lab_dashboard.css` : Dashboard kepala laboratorium.
- `laboran_dashboard.html`, `laboran_dashboard.css` : Dashboard laboran.
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
   - Buka `index.html` atau `dashboard.html` melalui browser.

## Catatan

- Pastikan server Apache dan MySQL sudah berjalan (misal menggunakan XAMPP).
- Semua fitur CRUD, search, pagination, dan select all sudah terintegrasi database.
- Tampilan sudah responsif dan konsisten sesuai desain awal.