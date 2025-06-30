<?php
require_once 'db_connect.php';

// Query untuk mengambil semua data absensi yang tersimpan
// Kita gabungkan (JOIN) dengan tabel lain untuk mendapatkan data yang lengkap
$sql = "SELECT 
            a.id,
            a.tanggal_absensi,
            a.pertemuan_ke,
            a.status,
            p.nim,
            p.nama_lengkap,
            j.nama_mata_kuliah,
            j.kelas
        FROM absensi a
        JOIN praktikan p ON a.nim_praktikan = p.nim
        JOIN jadwal_praktikum j ON a.id_jadwal = j.id
        ORDER BY a.tanggal_absensi DESC, j.nama_mata_kuliah ASC";

$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi Kehadiran</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="jadwal_praktikum.css"> <!-- Kita bisa pakai ulang CSS yang mirip -->
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
                <li><a href="jadwal_praktikum.php"><i class="icon">🗓️</i> Jadwal Praktikum</a></li>
                <li><a href="kelas.php"><i class="icon">🏫</i> Kelas</a></li>
                <li><a href="praktikan.php"><i class="icon">✍️</i> Praktikan</a></li>
                <li><a href="laporan_absensi.php" class="active"><i class="icon">✅</i> Absensi Kehadiran</a></li>
                <li><a href="mata_praktikum.php"><i class="icon">📚</i> Mata Praktikum</a></li>
                <li><a href="asisten_praktikum.php"><i class="icon">🧑‍🏫</i> Asisten Praktikum</a></li>
                <li><a href="ruang_laboratorium.php"><i class="icon">🔬</i> Ruang Laboratorium</a></li>
                <li><a href="laboran.php"><i class="icon">📄</i> Laboran</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="page-title-bar">
                <h2>Laporan Absensi Kehadiran</h2>
                <span class="page-description">Menampilkan seluruh data absensi praktikum yang telah tersimpan.</span>
            </div>

            <div class="schedule-header">
                <h2><span class="header-icon">📋</span>Daftar Absensi Kehadiran</h2>
            </div>
            
            <div class="schedule-actions">
                <button class="print-button" onclick="window.print()"><i class="icon">🖨️</i> Cetak Laporan</button>
            </div>

            <div class="data-table">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Mata Kuliah</th>
                            <th>Kelas</th>
                            <th>Pertemuan Ke</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($row['tanggal_absensi']))); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_mata_kuliah']); ?></td>
                                    <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                                    <td><?php echo htmlspecialchars($row['pertemuan_ke']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nim']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">Belum ada data absensi yang tersimpan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?> 