<?php
require_once 'db_connect.php'; // Sertakan file koneksi database
session_start();
$nama = isset($_SESSION['nama']) && $_SESSION['nama'] ? $_SESSION['nama'] : 'User';
$foto = (isset($_SESSION['foto']) && $_SESSION['foto']) ? 'uploads/laboran/' . $_SESSION['foto'] : 'user.png';

// --- Data Fetching for Dropdowns ---
// 1. Mata Praktikum
$mata_praktikum_list = $conn->query("SELECT nama_matkul FROM mata_praktikum ORDER BY nama_matkul ASC")->fetch_all(MYSQLI_ASSOC);
// 2. Asisten Praktikum
$asisten_list = $conn->query("SELECT nama_asisten FROM asisten_praktikum ORDER BY nama_asisten ASC")->fetch_all(MYSQLI_ASSOC);
// 3. Ruang Laboratorium
$ruang_lab_list = $conn->query("SELECT nama_ruang FROM ruang_laboratorium ORDER BY nama_ruang ASC")->fetch_all(MYSQLI_ASSOC);
// 4. Kelas
$kelas_list = $conn->query("SELECT id, nama_kelas, semester FROM kelas ORDER BY nama_kelas, semester ASC")->fetch_all(MYSQLI_ASSOC);


// --- Pagination Logic (Adapted for jadwal_praktikum) --- 
$limit_per_page = isset($_GET['entries']) ? (int)$_GET['entries'] : 10; // Jumlah entri per halaman
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Halaman saat ini
$offset = ($current_page - 1) * $limit_per_page; // Offset untuk query SQL

// --- Search Logic (Adapted for jadwal_praktikum) --- 
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$where_clause = '';
if (!empty($search_query)) {
    $where_clause = " WHERE tahun_ajaran LIKE '%$search_query%' OR nama_mata_kuliah LIKE '%$search_query%' OR asisten_praktikum LIKE '%$search_query%' OR ruang_lab LIKE '%$search_query%' OR kelas LIKE '%$search_query%' OR hari LIKE '%$search_query%' OR waktu_mulai LIKE '%$search_query%' OR waktu_selesai LIKE '%$search_query%'";
}

// Query untuk mendapatkan total records (dengan atau tanpa pencarian)
$total_records_sql = "SELECT COUNT(id) AS total FROM jadwal_praktikum" . $where_clause;
$total_records_result = $conn->query($total_records_sql);
$total_records_row = $total_records_result->fetch_assoc();
$total_records = $total_records_row['total'];

// Hitung total halaman
$total_pages = ceil($total_records / $limit_per_page);

// Pastikan current_page tidak lebih dari total_pages atau kurang dari 1
if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages; // Set ke halaman terakhir jika melebihi total halaman
    $offset = ($current_page - 1) * $limit_per_page;
} elseif ($current_page < 1) {
    $current_page = 1; // Set ke halaman pertama jika kurang dari 1
    $offset = 0;
}

// Query untuk mendapatkan data jadwal praktikum (dengan pencarian dan pagination)
$sql = "SELECT id, tahun_ajaran, nama_mata_kuliah, asisten_praktikum, ruang_lab, kelas, semester, hari, waktu_mulai, waktu_selesai FROM jadwal_praktikum" . $where_clause . " ORDER BY id ASC LIMIT $limit_per_page OFFSET $offset";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Praktikum</title>
    <link rel="stylesheet" href="jadwal_praktikum.css">
    <link rel="stylesheet" href="dashboard.css"> <!-- Link to dashboard.css for sidebar styling -->
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <!-- Placeholder for logo/title -->
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
            <div class="page-title-bar">
                <h2>Jadwal Praktikum</h2>
                <span class="page-description">Data Matkum, Jadwal Praktikum, Menampilkan Data Jadwal Praktikum</span>
            </div>
            <div class="schedule-header" id="schedule-header">
                <h2><span class="header-icon" id="header-icon">📋</span>Daftar Jadwal Praktikum</h2>
            </div>
            <div class="schedule-actions" id="schedule-actions">
                <button class="add-schedule-button">+ Tambah Jadwal Praktikum</button>
                <button class="btn-purple" onclick="window.location.href='perubahan_jadwal.php'">Data Ubah Jadwal</button>
                <button class="print-button"><i class="icon">🖨️</i> Cetak</button>
            </div>

            <div class="add-schedule-form" style="display: none;"> <!-- Initially hidden -->
                <form action="add_jadwal_praktikum.php" method="POST">
                    <div class="form-group">
                        <label for="tahun_ajaran">Tahun Ajaran</label>
                        <input type="text" id="tahun_ajaran" name="tahun_ajaran" value="2022-2023" required>
                    </div>

                    <div class="form-group">
                        <label for="nama_mata_kuliah">Nama Mata Kuliah</label>
                        <select id="nama_mata_kuliah" name="nama_mata_kuliah" required>
                            <option value="">Pilih Mata Kuliah</option>
                            <?php foreach ($mata_praktikum_list as $item): ?>
                                <option value="<?php echo htmlspecialchars($item['nama_matkul']); ?>"><?php echo htmlspecialchars($item['nama_matkul']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="asisten_praktikum">Asisten Praktikum</label>
                        <select id="asisten_praktikum" name="asisten_praktikum" required>
                            <option value="">Pilih Asisten Praktikum</option>
                            <?php foreach ($asisten_list as $item): ?>
                                <option value="<?php echo htmlspecialchars($item['nama_asisten']); ?>"><?php echo htmlspecialchars($item['nama_asisten']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ruang_lab">Ruang Lab</label>
                        <select id="ruang_lab" name="ruang_lab" required>
                            <option value="">Pilih Ruang Lab</option>
                            <?php foreach ($ruang_lab_list as $item): ?>
                                <option value="<?php echo htmlspecialchars($item['nama_ruang']); ?>"><?php echo htmlspecialchars($item['nama_ruang']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="kelas">Kelas</label>
                        <select id="kelas" name="kelas" required>
                            <option value="" data-semester="">Pilih Kelas</option>
                            <?php foreach ($kelas_list as $item): ?>
                                <option value="<?php echo htmlspecialchars($item['nama_kelas']); ?>" data-semester="<?php echo htmlspecialchars($item['semester']); ?>">
                                    <?php echo htmlspecialchars($item['nama_kelas']) . ' (Semester ' . htmlspecialchars($item['semester']) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="semester">Semester</label>
                        <input type="text" id="semester" name="semester" placeholder="Pilih kelas untuk mengisi semester" readonly required>
                    </div>

                    <div class="form-group">
                        <label>Hari</label>
                        <div class="radio-group">
                            <input type="radio" id="senin" name="hari" value="Senin">
                            <label for="senin">Senin</label>
                            <input type="radio" id="selasa" name="hari" value="Selasa" checked>
                            <label for="selasa">Selasa</label>
                            <input type="radio" id="rabu" name="hari" value="Rabu">
                            <label for="rabu">Rabu</label>
                            <input type="radio" id="kamis" name="hari" value="Kamis">
                            <label for="kamis">Kamis</label>
                            <input type="radio" id="jumat" name="hari" value="Jum'at">
                            <label for="jumat">Jum'at</label>
                            <input type="radio" id="sabtu" name="hari" value="Sabtu">
                            <label for="sabtu">Sabtu</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Waktu</label>
                        <div class="time-inputs">
                            <input type="text" placeholder="13.00" id="waktu_mulai" name="waktu_mulai" value="13.00" required>
                            <span class="separator">-</span>
                            <input type="text" placeholder="15.00" id="waktu_selesai" name="waktu_selesai" value="15.00" required>
                        </div>
                    </div>

                    <!-- Add submit button for the form here -->
                    <button type="submit" class="submit-schedule-button">Simpan Jadwal</button>
                </form>
            </div>

            <div class="edit-schedule-form" style="display: none;"> <!-- Initially hidden -->
                <form action="update_jadwal_praktikum.php" method="POST">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="form-group">
                        <label for="tahun_ajaran_edit">Tahun Ajaran</label>
                        <input type="text" id="tahun_ajaran_edit" name="tahun_ajaran" value="2022-2023" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_mata_kuliah_edit">Nama Mata Kuliah</label>
                        <select id="nama_mata_kuliah_edit" name="nama_mata_kuliah" required>
                            <option value="">Pilih Mata Kuliah</option>
                            <?php foreach ($mata_praktikum_list as $item): ?>
                                <option value="<?php echo htmlspecialchars($item['nama_matkul']); ?>"><?php echo htmlspecialchars($item['nama_matkul']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="asisten_praktikum_edit">Asisten Praktikum</label>
                        <select id="asisten_praktikum_edit" name="asisten_praktikum" required>
                            <option value="">Pilih Asisten Praktikum</option>
                            <?php foreach ($asisten_list as $item): ?>
                                <option value="<?php echo htmlspecialchars($item['nama_asisten']); ?>"><?php echo htmlspecialchars($item['nama_asisten']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ruang_lab_edit">Ruang Lab</label>
                        <select id="ruang_lab_edit" name="ruang_lab" required>
                            <option value="">Pilih Ruang Lab</option>
                             <?php foreach ($ruang_lab_list as $item): ?>
                                <option value="<?php echo htmlspecialchars($item['nama_ruang']); ?>"><?php echo htmlspecialchars($item['nama_ruang']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kelas_edit">Kelas</label>
                        <select id="kelas_edit" name="kelas" required>
                            <option value="" data-semester="">Pilih Kelas</option>
                             <?php foreach ($kelas_list as $item): ?>
                                <option value="<?php echo htmlspecialchars($item['nama_kelas']); ?>" data-semester="<?php echo htmlspecialchars($item['semester']); ?>">
                                    <?php echo htmlspecialchars($item['nama_kelas']) . ' (Semester ' . htmlspecialchars($item['semester']) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                     <div class="form-group">
                        <label for="semester_edit">Semester</label>
                        <input type="text" id="semester_edit" name="semester" placeholder="Pilih kelas untuk mengisi semester" readonly required>
                    </div>
                    <div class="form-group">
                        <label>Hari</label>
                        <div class="radio-group">
                            <input type="radio" id="senin_edit" name="hari" value="Senin">
                            <label for="senin_edit">Senin</label>
                            <input type="radio" id="selasa_edit" name="hari" value="Selasa" checked>
                            <label for="selasa_edit">Selasa</label>
                            <input type="radio" id="rabu_edit" name="hari" value="Rabu">
                            <label for="rabu_edit">Rabu</label>
                            <input type="radio" id="kamis_edit" name="hari" value="Kamis">
                            <label for="kamis_edit">Kamis</label>
                            <input type="radio" id="jumat_edit" name="hari" value="Jum'at">
                            <label for="jumat_edit">Jum'at</label>
                            <input type="radio" id="sabtu_edit" name="hari" value="Sabtu">
                            <label for="sabtu_edit">Sabtu</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Waktu</label>
                        <div class="time-inputs">
                            <input type="text" placeholder="13.00" id="waktu_mulai_edit" name="waktu_mulai" required>
                            <span class="separator">-</span>
                            <input type="text" placeholder="15.00" id="waktu_selesai_edit" name="waktu_selesai" required>
                        </div>
                    </div>
                    <div class="button-group">
                        <button type="reset" class="reset-schedule-button"><span class="icon">🔄</span> Reset</button>
                        <button type="submit" class="submit-schedule-button"><span class="icon">✅</span> Update</button>
                    </div>
                </form>
            </div>

            <div class="data-table">
                <label for="entries">Show</label>
                <select name="entries" id="entries">
                    <option value="10" <?php echo ($limit_per_page == 10) ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?php echo ($limit_per_page == 25) ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?php echo ($limit_per_page == 50) ? 'selected' : ''; ?>>50</option>
                    <option value="100" <?php echo ($limit_per_page == 100) ? 'selected' : ''; ?>>100</option>
                    <option value="manual_input" <?php echo (!in_array($limit_per_page, [10, 25, 50, 100]) && $limit_per_page > 0) ? 'selected' : ''; ?>>Lainnya</option>
                </select>
                <input type="number" id="manual_entries_input" name="manual_entries" style="width: 60px; padding: 5px; border: 1px solid #ccc; border-radius: 4px; margin-left: 5px; display: none;" min="1" value="<?php echo (!in_array($limit_per_page, [10, 25, 50, 100]) && $limit_per_page > 0) ? $limit_per_page : ''; ?>"> entries
                <div class="search-box">
                    <label for="search">Search:</label>
                    <input type="text" id="search">
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tahun</th>
                            <th>Matkum</th>
                            <th>Asisten Praktikum</th>
                            <th>Ruangan</th>
                            <th>Kelas</th>
                            <th>Hari</th>
                            <th>Waktu</th>
                            <th>Pilihan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            $no = 1; // Nomor urut
                            // Output data setiap baris
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . htmlspecialchars($row["tahun_ajaran"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["nama_mata_kuliah"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["asisten_praktikum"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["ruang_lab"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["kelas"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["hari"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["waktu_mulai"]) . " - " . htmlspecialchars($row["waktu_selesai"]) . "</td>";
                                echo "<td>";
                                echo "<div class=\"action-buttons-wrapper\">";
                                echo "<a href=\"absensi_kehadiran.php?id_jadwal=" . $row["id"] . "\" class=\"action-button view-button\" title=\"Input Absensi Kehadiran\">📊</a>";
                                echo "<button class=\"action-button edit-button\" 
                                        data-id=\"" . $row["id"] . "\"
                                        data-tahun_ajaran=\"" . htmlspecialchars($row["tahun_ajaran"]) . "\"
                                        data-nama_mata_kuliah=\"" . htmlspecialchars($row["nama_mata_kuliah"]) . "\"
                                        data-asisten_praktikum=\"" . htmlspecialchars($row["asisten_praktikum"]) . "\"
                                        data-ruang_lab=\"" . htmlspecialchars($row["ruang_lab"]) . "\"
                                        data-kelas=\"" . htmlspecialchars($row["kelas"]) . "\"
                                        data-semester=\"" . htmlspecialchars($row["semester"] ?? '') . "\"
                                        data-hari=\"" . htmlspecialchars($row["hari"]) . "\"
                                        data-waktu_mulai=\"" . htmlspecialchars($row["waktu_mulai"]) . "\"
                                        data-waktu_selesai=\"" . htmlspecialchars($row["waktu_selesai"]) . "\">📝</button>";
                                echo "<button class=\"action-button delete-button\" data-id=\"" . $row["id"] . "\">🗑️</button>";
                                echo "</div>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan=\"9\">Tidak ada data jadwal praktikum yang ditemukan.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <div class="table-footer">
                    <span>Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit_per_page, $total_records); ?> of <?php echo $total_records; ?> entries</span>
                    <div class="pagination">
                        <button class="prev-button" <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>>Previous</button>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <button class="page-button <?php echo ($i == $current_page) ? 'active' : ''; ?>"><?php echo $i; ?></button>
                        <?php endfor; ?>
                        <button class="next-button" <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>>Next</button>
                    </div>
                </div>
                <p class="table-info-text"></p>
            </div>
        </div>
    </div>

    <!-- AREA CETAK KHUSUS PRINT -->
    <div id="print-area" class="print-area">
        <div style="display: flex; align-items: center; margin-bottom: 8px;">
            <img src="unibba-logo.png" alt="Logo Unibba" style="height: 60px; margin-right: 16px;">
            <div style="flex:1; text-align: center;">
                <div style="font-size: 1.2em; font-weight: bold;">DAFTAR JADWAL PRAKTIKUM</div>
                <div style="font-size: 1.1em;">Fakultas Teknologi Informasi Universitas Bale Bandung</div>
            </div>
        </div>
        <table border="1" cellspacing="0" cellpadding="6" style="width:100%; border-collapse:collapse; font-size:0.95em;">
            <thead>
                <tr style="background:#f0f0f0;">
                    <th>No</th>
                    <th>Tahun</th>
                    <th>Matkum</th>
                    <th>Asisten Praktikum</th>
                    <th>Ruangan</th>
                    <th>Kelas</th>
                    <th>Hari</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query ulang tanpa limit agar semua data tampil saat print
                $sql_print = "SELECT tahun_ajaran, nama_mata_kuliah, asisten_praktikum, ruang_lab, kelas, hari, waktu_mulai, waktu_selesai FROM jadwal_praktikum" . $where_clause . " ORDER BY id ASC";
                $result_print = $conn->query($sql_print);
                if ($result_print && $result_print->num_rows > 0) {
                    $no = 1;
                    while($row = $result_print->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . htmlspecialchars($row["tahun_ajaran"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["nama_mata_kuliah"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["asisten_praktikum"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["ruang_lab"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["kelas"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["hari"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["waktu_mulai"]) . " - " . htmlspecialchars($row["waktu_selesai"]) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' style='text-align:center;'>Tidak ada data jadwal praktikum</td></tr>";
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
    }
    .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
        position: absolute;
        top: 20px;
        right: 40px;
        z-index: 10;
        background: #fff;
        padding: 4px 12px;
        border-radius: 24px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    }
    .user-info .user-name {
        font-weight: bold;
        color: #555;
    }
    .user-info .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    }
    </style>
    <div class="user-info">
        <span class="user-name"><?php echo htmlspecialchars($nama); ?></span>
        <img src="<?php echo htmlspecialchars($foto); ?>" alt="User" class="user-avatar">
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const addScheduleButton = document.querySelector('.add-schedule-button');
        const addScheduleForm = document.querySelector('.add-schedule-form');
        const editScheduleButton = document.querySelector('.edit-schedule-button');
        const editScheduleForm = document.querySelector('.edit-schedule-form');
        const mainContent = document.querySelector('.main-content');
        const scheduleHeader = document.getElementById('schedule-header');
            const scheduleHeaderTitle = scheduleHeader ? scheduleHeader.querySelector('h2') : null;
        const scheduleActions = document.getElementById('schedule-actions');
        const headerIcon = document.getElementById('header-icon');

            if (addScheduleButton && addScheduleForm && scheduleHeader && scheduleHeaderTitle) {
        addScheduleButton.addEventListener('click', () => {
            addScheduleForm.style.display = (addScheduleForm.style.display === 'none' || addScheduleForm.style.display === '') ? 'block' : 'none';
                    if (editScheduleForm) editScheduleForm.style.display = 'none';
            mainContent.classList.remove('editing');
            scheduleHeader.classList.remove('edit-mode');
            if (addScheduleForm.style.display === 'block') {
                mainContent.classList.add('adding');
                scheduleHeader.classList.add('edit-mode');
                scheduleHeaderTitle.innerHTML = '<span class="header-icon">➕</span>Tambah Jadwal Praktikum';
            } else {
                mainContent.classList.remove('adding');
                scheduleHeader.classList.remove('edit-mode');
                scheduleHeaderTitle.innerHTML = '<span class="header-icon">📋</span>Daftar Jadwal Praktikum';
            }
        });
            }

            if (editScheduleButton && editScheduleForm && scheduleHeader && scheduleHeaderTitle) {
        editScheduleButton.addEventListener('click', () => {
            editScheduleForm.style.display = (editScheduleForm.style.display === 'none' || editScheduleForm.style.display === '') ? 'block' : 'none';
                    if (addScheduleForm) addScheduleForm.style.display = 'none';
            if (editScheduleForm.style.display === 'block') {
                mainContent.classList.add('editing');
                mainContent.classList.remove('adding');
                scheduleHeader.classList.add('edit-mode');
                scheduleHeaderTitle.innerHTML = '<span class="header-icon">📝</span>Edit Jadwal';
            } else {
                mainContent.classList.remove('editing');
                mainContent.classList.remove('adding');
                scheduleHeader.classList.remove('edit-mode');
                scheduleHeaderTitle.innerHTML = '<span class="header-icon">📋</span>Daftar Jadwal Praktikum';
                }
            });
            }

            // --- Logika untuk Tombol Hapus ---
            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const confirmation = confirm('Apakah Anda yakin ingin menghapus data jadwal ini?');

                    if (confirmation) {
                        // Buat form sementara untuk mengirim data ID via POST
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = 'delete_jadwal_praktikum.php'; // Pastikan action mengarah ke file yang benar

                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'id';
                        hiddenInput.value = id;

                        form.appendChild(hiddenInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });

            // Add event listener for edit buttons in the table
            document.querySelectorAll('.action-button.edit-button').forEach(editBtn => {
                editBtn.addEventListener('click', function() {
                    // Populate form fields from data attributes
                    const id = this.dataset.id;
                    document.getElementById('edit_id').value = id;
                    document.getElementById('tahun_ajaran_edit').value = this.dataset.tahun_ajaran;
                    document.getElementById('nama_mata_kuliah_edit').value = this.dataset.nama_mata_kuliah;
                    document.getElementById('asisten_praktikum_edit').value = this.dataset.asisten_praktikum;
                    document.getElementById('ruang_lab_edit').value = this.dataset.ruang_lab;
                    document.getElementById('kelas_edit').value = this.dataset.kelas;
                    document.getElementById('semester_edit').value = this.dataset.semester;

                    // Set radio button for 'hari'
                    const hari = this.dataset.hari;
                    document.querySelector(`input[name='hari'][value='${hari}']#${hari.toLowerCase()}_edit`).checked = true;

                    document.getElementById('waktu_mulai_edit').value = this.dataset.waktu_mulai;
                    document.getElementById('waktu_selesai_edit').value = this.dataset.waktu_selesai;

                    // Show the edit form
                    editScheduleForm.style.display = 'block';
                    scheduleHeader.style.display = 'none';
                    scheduleActions.style.display = 'none';
                });
            });

            // Add event listener for the print button
            const printButton = document.querySelector('.print-button');
            if (printButton) {
                printButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('Apakah Anda yakin ingin mencetak atau export PDF data jadwal praktikum?')) {
                    window.print();
                    }
                });
            }

            // --- Pagination and Search Logic --- Start
            const entriesSelect = document.getElementById('entries');
            const manualEntriesInput = document.getElementById('manual_entries_input'); // Assuming you have this input for 'Lainnya'
            const searchInput = document.getElementById('search');
            const paginationContainer = document.querySelector('.pagination'); // Assuming you have pagination buttons

            // Helper function to update URL parameters
            function updateUrlParameter(param, value) {
                const url = new URL(window.location.href);
                url.searchParams.set(param, value);
                // When changing entries or search, reset page to 1
                if (param === 'entries' || param === 'search') {
                    url.searchParams.set('page', 1);
                }
                window.location.href = url.toString();
            }

            // Function to check and show/hide manual input
            function toggleManualEntriesInput() {
                if (entriesSelect && manualEntriesInput) {
                    if (entriesSelect.value === 'manual_input') {
                        manualEntriesInput.style.display = 'inline-block';
                        if (manualEntriesInput.value === '') {
                            manualEntriesInput.focus();
                        }
                    } else {
                        manualEntriesInput.style.display = 'none';
                    }
                }
            }

            // Initial check on page load
            toggleManualEntriesInput();

            // Event listener for 'Show entries' dropdown
            if (entriesSelect) {
                entriesSelect.addEventListener('change', function() {
                    toggleManualEntriesInput();
                    let valueToSet = this.value;
                    if (valueToSet === 'manual_input') {
                        return; // Wait for manual input
                    } else {
                        updateUrlParameter('entries', valueToSet);
                    }
                });
            }

            // Event listener for manual entries input
            if (manualEntriesInput) {
                let manualEntriesTimeout;
                manualEntriesInput.addEventListener('input', function() {
                    clearTimeout(manualEntriesTimeout);
                    manualEntriesTimeout = setTimeout(() => {
                        const value = parseInt(this.value);
                        if (!isNaN(value) && value > 0) {
                            updateUrlParameter('entries', value);
                        } else if (this.value === '') {
                            updateUrlParameter('entries', entriesSelect.options[0].value);
                        }
                    }, 500);
                });

                manualEntriesInput.addEventListener('keypress', function(event) {
                    if (event.key === 'Enter') {
                        clearTimeout(manualEntriesTimeout);
                        const value = parseInt(this.value);
                        if (!isNaN(value) && value > 0) {
                            updateUrlParameter('entries', value);
                        } else if (this.value === '') {
                            updateUrlParameter('entries', entriesSelect.options[0].value);
                        }
                    }
                });
            }

            // Event listener for 'Search' input with debounce
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        updateUrlParameter('search', this.value);
                    }, 500);
                });

                searchInput.addEventListener('keypress', function(event) {
                    if (event.key === 'Enter') {
                        clearTimeout(searchTimeout);
                        updateUrlParameter('search', this.value);
                    }
                });
            }

            // Event listeners for pagination buttons (delegation for dynamic buttons)
            if (paginationContainer) {
                paginationContainer.addEventListener('click', function(event) {
                    if (event.target.classList.contains('prev-button') && !event.target.disabled) {
                        const currentPage = parseInt(new URL(window.location.href).searchParams.get('page') || '1');
                        updateUrlParameter('page', currentPage - 1);
                    } else if (event.target.classList.contains('next-button') && !event.target.disabled) {
                        const currentPage = parseInt(new URL(window.location.href).searchParams.get('page') || '1');
                        updateUrlParameter('page', currentPage + 1);
                    } else if (event.target.classList.contains('page-button')) {
                        const pageNum = parseInt(event.target.textContent);
                        updateUrlParameter('page', pageNum);
                    }
                });
            }

            // Handle auto-populate semester for Add Form
            document.getElementById('kelas').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                document.getElementById('semester').value = selectedOption.dataset.semester || '';
            });

            // Handle auto-populate semester for Edit Form
            document.getElementById('kelas_edit').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                document.getElementById('semester_edit').value = selectedOption.dataset.semester || '';
            });
        });
    </script>
</body>
</html> 