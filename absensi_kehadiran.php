<?php
require_once 'db_connect.php';

// 1. Ambil ID Jadwal dari URL
if (!isset($_GET['id_jadwal']) || !is_numeric($_GET['id_jadwal'])) {
    header('Location: laporan_absensi.php');
    exit();
}
$id_jadwal = intval($_GET['id_jadwal']);

// 2. Ambil Detail Jadwal dari Database (Query disederhanakan untuk menghindari error JOIN)
$stmt_jadwal = $conn->prepare("SELECT * FROM jadwal_praktikum WHERE id = ?");
$stmt_jadwal->bind_param("i", $id_jadwal);
$stmt_jadwal->execute();
$result_jadwal = $stmt_jadwal->get_result();
if ($result_jadwal->num_rows === 0) {
    die("Error: Jadwal dengan ID " . $id_jadwal . " tidak ditemukan.");
}
$jadwal = $result_jadwal->fetch_assoc();

$nama_kelas_jadwal = $jadwal['kelas'];

// --- Logika Baru untuk menentukan Semester (Lebih Tangguh) ---
// Prioritaskan kolom 'semester' dari DB jika ada, jika tidak, tebak dari nama kelas.
if (isset($jadwal['semester']) && !empty($jadwal['semester'])) {
    $semester_jadwal = (int)$jadwal['semester'];
} else {
    // Tebak semester dari angka pertama yang ditemukan di nama kelas
    preg_match('/[0-9]+/', $nama_kelas_jadwal, $matches);
    $semester_jadwal = !empty($matches) ? (int)$matches[0] : 0;
}

// Jika semester masih 0 setelah mencoba menebak, proses tidak bisa lanjut.
if ($semester_jadwal === 0) {
    die("CRITICAL ERROR: Semester untuk jadwal ini tidak bisa ditentukan. <br>Pastikan nama kelas di jadwal (cth: 'IF Pagi 6A') mengandung angka semester, <br>ATAU (lebih baik) jalankan perintah SQL ini: <br><b>ALTER TABLE `jadwal_praktikum` ADD `semester` INT(2) NOT NULL AFTER `kelas`;</b>");
}

// --- Logika Baru untuk menentukan Nama Kelas Dasar ---
// Hapus angka semester dan spasi di sekitarnya untuk mendapatkan nama kelas dasar.
// Contoh: "IF Pagi 6A" dengan semester 6 -> menjadi "IF Pagi A"
$base_class_name = trim(preg_replace('/\s*\b' . $semester_jadwal . '\b\s*A\b|\s*\b' . $semester_jadwal . '\b\s*B\b/i', '', $nama_kelas_jadwal));
if(empty(trim($base_class_name))) { // Fallback untuk nama kelas seperti "IF 6A"
    $base_class_name = trim(preg_replace('/[0-9]+[A-Z]?/i', '', $nama_kelas_jadwal));
}

// 3. Ambil Daftar Praktikan dari tabel praktikan secara langsung
$stmt_praktikan = $conn->prepare("
    SELECT nim, nama_lengkap 
    FROM praktikan
    WHERE kelas = ? AND semester = ?
    ORDER BY nim ASC
");
// Menggunakan $base_class_name yang sudah diolah
$stmt_praktikan->bind_param("si", $base_class_name, $semester_jadwal);
$stmt_praktikan->execute();
$result_praktikan = $stmt_praktikan->get_result();
$praktikan_list = $result_praktikan->fetch_all(MYSQLI_ASSOC);

// Memulai session untuk menampilkan pesan notifikasi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Absensi Kehadiran</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="absensi_kehadiran.css"> 
</head>
<body>
<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="unibba-logo.png" alt="Logo" class="sidebar-logo">
            <h3>DAFTAR MENU PRAKTIKUM</h3>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="icon">🏠</i> Dashboard</a></li>
            <li><a href="jadwal_praktikum.php" class="active"><i class="icon">🗓️</i> Jadwal Praktikum</a></li>
            <li><a href="kelas.php"><i class="icon">🏫</i> Kelas</a></li>
            <li><a href="praktikan.php"><i class="icon">✍️</i> Praktikan</a></li>
            <li><a href="laporan_absensi.php"><i class="icon">✅</i> Absensi Kehadiran</a></li>
            <li><a href="mata_praktikum.php"><i class="icon">📚</i> Mata Praktikum</a></li>
            <li><a href="asisten_praktikum.php"><i class="icon">🧑‍🏫</i> Asisten Praktikum</a></li>
            <li><a href="ruang_laboratorium.php"><i class="icon">🔬</i> Ruang Laboratorium</a></li>
            <li><a href="laboran.php"><i class="icon">📄</i> Laboran</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h2>Tambah Absensi Kehadiran</h2>
            <div>
                <a href="absensi_kehadiran_list.php">Daftar Absensi Kehadiran</a>
            </div>
        </div>

        <?php
        // Menampilkan pesan sukses atau error dari session
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']); // Hapus pesan setelah ditampilkan
        }
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']); // Hapus pesan setelah ditampilkan
        }
        ?>

        <form action="simpan_absensi.php" method="POST" class="attendance-form">
            <input type="hidden" name="id_jadwal" value="<?php echo $id_jadwal; ?>">
            
            <div class="form-header">
                <h3>Absensi</h3>
            </div>

            <div class="schedule-details">
                <div class="detail-item">
                    <span>Tahun Ajaran:</span>
                    <strong><?php echo htmlspecialchars($jadwal['tahun_ajaran']); ?></strong>
                </div>
                <div class="detail-item">
                    <span>Nama Asisten Praktikum:</span>
                    <strong><?php echo htmlspecialchars($jadwal['asisten_praktikum']); ?></strong>
                </div>
                <div class="detail-item">
                    <span>Hari:</span>
                    <strong><?php echo htmlspecialchars($jadwal['hari']); ?></strong>
                </div>
                <div class="detail-item">
                    <span>Semester:</span>
                    <strong><?php echo htmlspecialchars($semester_jadwal); ?></strong>
                </div>
                 <div class="detail-item">
                    <span>Nama Ruang:</span>
                    <strong><?php echo htmlspecialchars($jadwal['ruang_lab']); ?></strong>
                </div>
                 <div class="detail-item">
                    <span>Waktu:</span>
                    <strong><?php echo htmlspecialchars($jadwal['waktu_mulai']) . ' - ' . htmlspecialchars($jadwal['waktu_selesai']); ?></strong>
                </div>
                <div class="detail-item">
                    <span>Nama Mata Kuliah:</span>
                    <strong><?php echo htmlspecialchars($jadwal['nama_mata_kuliah']); ?></strong>
                </div>
                <div class="detail-item">
                    <span>Nama Kelas:</span>
                    <strong><?php echo htmlspecialchars($jadwal['kelas']); ?></strong>
                </div>
            </div>

            <div class="attendance-input-group">
                <div class="form-group">
                    <label for="pertemuan_ke">Pertemuan Ke:</label>
                    <input type="number" id="pertemuan_ke" name="pertemuan_ke" min="1" max="16" required>
                </div>
                <div class="form-group">
                    <label for="tanggal_absensi">Tanggal:</label>
                    <input type="date" id="tanggal_absensi" name="tanggal_absensi" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kelas</th>
                            <th>Semester</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($praktikan_list)): ?>
                            <tr>
                                <td colspan="6">Tidak ada data praktikan ditemukan untuk kelas ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($praktikan_list as $praktikan): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($base_class_name); ?></td>
                                <td><?php echo htmlspecialchars($semester_jadwal); ?></td>
                                <td><?php echo htmlspecialchars($praktikan['nim']); ?></td>
                                <td><?php echo htmlspecialchars($praktikan['nama_lengkap']); ?></td>
                                <td>
                                    <input type="hidden" name="nim[]" value="<?php echo htmlspecialchars($praktikan['nim']); ?>">
                                    <select name="status[<?php echo htmlspecialchars($praktikan['nim']); ?>]" required>
                                        <option value="Hadir">Hadir</option>
                                        <option value="Izin">Izin</option>
                                        <option value="Sakit">Sakit</option>
                                        <option value="Alpha">Alpha</option>
                                    </select>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($praktikan_list)): ?>
            <div class="form-actions">
                <button type="submit" class="btn-simpan">Simpan</button>
                <button type="reset" class="btn-reset">Reset</button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>
</body>
</html> 