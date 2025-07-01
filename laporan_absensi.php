<?php
require_once 'db_connect.php';

// Query untuk mengambil semua data absensi yang tersimpan
// Kita gabungkan (JOIN) dengan tabel lain untuk mendapatkan data yang lengkap
$where = [];
if (isset($_GET['tanggal_dari']) && $_GET['tanggal_dari']) {
    $where[] = "a.tanggal_absensi >= '".$conn->real_escape_string($_GET['tanggal_dari'])."'";
}
if (isset($_GET['tanggal_sampai']) && $_GET['tanggal_sampai']) {
    $where[] = "a.tanggal_absensi <= '".$conn->real_escape_string($_GET['tanggal_sampai'])."'";
}
if (isset($_GET['kelas']) && $_GET['kelas']) {
    $where[] = "j.kelas = '".$conn->real_escape_string($_GET['kelas'])."'";
}
if (isset($_GET['mata_kuliah']) && $_GET['mata_kuliah']) {
    $where[] = "j.nama_mata_kuliah = '".$conn->real_escape_string($_GET['mata_kuliah'])."'";
}
$where_sql = count($where) ? ('WHERE '.implode(' AND ', $where)) : '';
$sql = "SELECT a.id, a.tanggal_absensi, a.pertemuan_ke, a.status, p.nim, p.nama_lengkap, j.nama_mata_kuliah, j.kelas FROM absensi a JOIN praktikan p ON a.nim_praktikan = p.nim JOIN jadwal_praktikum j ON a.id_jadwal = j.id $where_sql ORDER BY a.tanggal_absensi DESC, j.nama_mata_kuliah ASC";
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
                <button class="print-button" id="btn-cetak"><i class="icon">🖨️</i> Cetak Laporan</button>
                <button class="btn-green" id="btn-filter" style="margin-left:12px;">Filter Data</button>
            </div>

            <!-- Modal Filter -->
            <div id="filter-modal" class="modal" style="display:none;position:fixed;z-index:1000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);align-items:center;justify-content:center;">
                <div style="background:#fff;padding:32px 28px 24px 28px;border-radius:12px;min-width:320px;max-width:90vw;box-shadow:0 2px 16px rgba(0,0,0,0.15);position:relative;">
                    <h3 style="margin-top:0;margin-bottom:18px;">Filter Laporan Absensi</h3>
                    <form method="get" id="filterForm">
                        <div style="margin-bottom:12px;">
                            <label>Dari Tanggal: <input type="date" name="tanggal_dari" value="<?php echo isset($_GET['tanggal_dari']) ? htmlspecialchars($_GET['tanggal_dari']) : ''; ?>"></label>
                            <label style="margin-left:12px;">Sampai: <input type="date" name="tanggal_sampai" value="<?php echo isset($_GET['tanggal_sampai']) ? htmlspecialchars($_GET['tanggal_sampai']) : ''; ?>"></label>
                        </div>
                        <div style="margin-bottom:12px;">
                            <label>Kelas:
                                <select name="kelas">
                                    <option value="">Semua</option>
                                    <?php
                                    $kelas_opt = $conn->query("SELECT DISTINCT kelas FROM jadwal_praktikum ORDER BY kelas ASC");
                                    while($row = $kelas_opt->fetch_assoc()) {
                                        $selected = (isset($_GET['kelas']) && $_GET['kelas'] == $row['kelas']) ? 'selected' : '';
                                        echo '<option value="'.htmlspecialchars($row['kelas']).'" '.$selected.'>'.htmlspecialchars($row['kelas']).'</option>';
                                    }
                                    ?>
                                </select>
                            </label>
                            <label style="margin-left:12px;">Mata Kuliah:
                                <select name="mata_kuliah">
                                    <option value="">Semua</option>
                                    <?php
                                    $matkul_opt = $conn->query("SELECT DISTINCT nama_mata_kuliah FROM jadwal_praktikum ORDER BY nama_mata_kuliah ASC");
                                    while($row = $matkul_opt->fetch_assoc()) {
                                        $selected = (isset($_GET['mata_kuliah']) && $_GET['mata_kuliah'] == $row['nama_mata_kuliah']) ? 'selected' : '';
                                        echo '<option value="'.htmlspecialchars($row['nama_mata_kuliah']).'" '.$selected.'>'.htmlspecialchars($row['nama_mata_kuliah']).'</option>';
                                    }
                                    ?>
                                </select>
                            </label>
                        </div>
                        <div style="text-align:right;margin-top:18px;">
                            <button type="button" onclick="document.getElementById('filter-modal').style.display='none'" style="margin-right:10px;">Batal</button>
                            <button type="submit" class="btn-green">Terapkan Filter</button>
                        </div>
                    </form>
                </div>
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
    <!-- AREA CETAK KHUSUS PRINT -->
    <div id="print-area" class="print-area">
        <div style="display: flex; align-items: center; margin-bottom: 8px;">
            <img src="unibba-logo.png" alt="Logo Unibba" style="height: 60px; margin-right: 16px;">
            <div style="flex:1; text-align: center;">
                <div style="font-size: 1.2em; font-weight: bold;">LAPORAN ABSENSI KEHADIRAN</div>
                <div style="font-size: 1.1em;">Fakultas Teknologi Informasi Universitas Bale Bandung</div>
            </div>
        </div>
        <table border="1" cellspacing="0" cellpadding="6" style="width:100%; border-collapse:collapse; font-size:0.95em;">
            <thead>
                <tr style="background:#f0f0f0;">
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
                <?php
                // Query ulang tanpa limit agar semua data tampil saat print
                require 'db_connect.php';
                $where = [];
                if (isset($_GET['tanggal_dari']) && $_GET['tanggal_dari']) {
                    $where[] = "a.tanggal_absensi >= '".$conn->real_escape_string($_GET['tanggal_dari'])."'";
                }
                if (isset($_GET['tanggal_sampai']) && $_GET['tanggal_sampai']) {
                    $where[] = "a.tanggal_absensi <= '".$conn->real_escape_string($_GET['tanggal_sampai'])."'";
                }
                if (isset($_GET['kelas']) && $_GET['kelas']) {
                    $where[] = "j.kelas = '".$conn->real_escape_string($_GET['kelas'])."'";
                }
                if (isset($_GET['mata_kuliah']) && $_GET['mata_kuliah']) {
                    $where[] = "j.nama_mata_kuliah = '".$conn->real_escape_string($_GET['mata_kuliah'])."'";
                }
                $where_sql = count($where) ? ('WHERE '.implode(' AND ', $where)) : '';
                $sql_print = "SELECT a.tanggal_absensi, a.pertemuan_ke, a.status, p.nim, p.nama_lengkap, j.nama_mata_kuliah, j.kelas FROM absensi a JOIN praktikan p ON a.nim_praktikan = p.nim JOIN jadwal_praktikum j ON a.id_jadwal = j.id $where_sql ORDER BY a.tanggal_absensi DESC, j.nama_mata_kuliah ASC";
                $result_print = $conn->query($sql_print);
                if ($result_print && $result_print->num_rows > 0) {
                    $no = 1;
                    while($row = $result_print->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . htmlspecialchars(date('d-m-Y', strtotime($row['tanggal_absensi']))) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama_mata_kuliah']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['kelas']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['pertemuan_ke']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nim']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama_lengkap']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' style='text-align:center;'>Belum ada data absensi yang tersimpan.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <style>
    .print-area { display: none; }
    @media print {
        body, html { background: #fff !important; }
        .dashboard-container, .main-content, .sidebar, .page-title-bar, .schedule-header, .schedule-actions, .data-table, .add-schedule-form, .edit-schedule-form, .table-footer, .pagination, .search-box, .btn, button, .action-buttons-wrapper, .table-info-text { display: none !important; }
        .print-area { display: block !important; margin: 0; padding: 0; }
        .print-area table { page-break-inside: auto; }
        .print-area th, .print-area td { font-size: 1em; }
        #filter-modal { display: none !important; }
    }
    .modal { display: flex; }
    </style>
    <script>
    document.getElementById('btn-cetak').onclick = function(e) {
        e.preventDefault();
        if (confirm('Apakah Anda yakin ingin mencetak atau export PDF laporan absensi kehadiran?')) {
            window.print();
        }
    };
    document.getElementById('btn-filter').onclick = function(e) {
        e.preventDefault();
        document.getElementById('filter-modal').style.display = 'flex';
    };
    // Tutup modal jika klik di luar area modal
    window.onclick = function(event) {
        var modal = document.getElementById('filter-modal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
    </script>
</body>
</html>
<?php
$conn->close();
?> 