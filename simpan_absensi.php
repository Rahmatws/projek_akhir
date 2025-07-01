<?php
require_once 'db_connect.php';
session_start();

// Memeriksa apakah request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: jadwal_praktikum.php');
    exit();
}

// Validasi input dasar
$id_jadwal = isset($_POST['id_jadwal']) ? intval($_POST['id_jadwal']) : 0;
$pertemuan_ke = isset($_POST['pertemuan_ke']) ? intval($_POST['pertemuan_ke']) : 0;
$tanggal_absensi = isset($_POST['tanggal_absensi']) ? $_POST['tanggal_absensi'] : '';
$nims = isset($_POST['nim']) ? $_POST['nim'] : [];
$statuses = isset($_POST['status']) ? $_POST['status'] : [];

if ($id_jadwal === 0 || $pertemuan_ke === 0 || empty($tanggal_absensi) || empty($nims)) {
    // Set pesan error di session dan redirect
    $_SESSION['error_message'] = "Data tidak lengkap. Pastikan mengisi pertemuan dan tanggal.";
    header('Location: absensi_kehadiran.php?id_jadwal=' . $id_jadwal);
    exit();
}

// Siapkan statement SQL untuk insert atau update.
// ON DUPLICATE KEY UPDATE memerlukan UNIQUE key pada (id_jadwal, nim_praktikan, pertemuan_ke)
$stmt = $conn->prepare("
    INSERT INTO absensi (id_jadwal, nim_praktikan, pertemuan_ke, tanggal_absensi, status) 
    VALUES (?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE 
    tanggal_absensi = VALUES(tanggal_absensi), status = VALUES(status)
");

if ($stmt === false) {
    // Ini adalah error development, jadi kita bisa die()
    die("Error preparing statement: " . $conn->error);
}

// Mulai transaksi
$conn->begin_transaction();
$sukses = true;

// Loop melalui setiap mahasiswa yang datanya dikirim
foreach ($nims as $nim) {
    if (isset($statuses[$nim])) {
        $status = $statuses[$nim];
        
        // Bind parameter ke statement yang sudah disiapkan
        $stmt->bind_param("isiss", $id_jadwal, $nim, $pertemuan_ke, $tanggal_absensi, $status);
        
        // Eksekusi statement
        if (!$stmt->execute()) {
            // Jika ada satu saja yang gagal, tandai gagal dan hentikan loop
            $sukses = false;
            $_SESSION['error_message'] = "Gagal menyimpan data untuk NIM " . htmlspecialchars($nim) . ". Error: " . $stmt->error;
            break; 
        }
    }
}

// Selesaikan transaksi
if ($sukses) {
    // Jika semua berhasil, commit transaksi
    $conn->commit();
    $_SESSION['success_message'] = "Absensi untuk pertemuan ke-$pertemuan_ke berhasil disimpan!";
    header('Location: laporan_absensi.php');
    exit();
} else {
    // Jika ada yang gagal, batalkan semua perubahan
    $conn->rollback();
    // Pesan error sudah di-set di dalam loop
}

$stmt->close();
$conn->close();

// Arahkan pengguna kembali ke halaman form absensi
header('Location: absensi_kehadiran.php?id_jadwal=' . $id_jadwal);
exit();
?> 