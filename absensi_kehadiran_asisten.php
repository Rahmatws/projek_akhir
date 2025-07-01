<?php
// File ini hasil duplikasi dari absensi_kehadiran.php versi modifikasi terakhir
require_once 'db_connect.php';
$id_asisten = isset($_GET['id_asisten']) ? intval($_GET['id_asisten']) : 0;
$asisten = [
    'nama_asisten' => '-',
    'nama_prodi' => '-',
];
if ($id_asisten > 0) {
    $sql = "SELECT * FROM asisten_praktikum WHERE id = $id_asisten";
    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        $asisten = $res->fetch_assoc();
    }
}
$jadwal = [
    'tahun_ajaran' => '2021-2023',
    'hari' => 'Senin',
    'waktu' => '09:00 - 11:00',
    'ruang' => 'Lab 1',
    'kelas' => 'IF Pagi 2',
    'mata_kuliah' => 'Praktikum Algoritma dan Pemrogramman',
    'semester' => '2',
];
$data_absensi = [
    ['tanggal' => '15-07-2022', 'nim' => '301210003', 'nama' => 'Adhityas Syahrul Alam', 'status' => 'Hadir'],
    ['tanggal' => '15-07-2022', 'nim' => '301210004', 'nama' => 'Agus Suryana', 'status' => 'Hadir'],
    ['tanggal' => '15-07-2022', 'nim' => '301210005', 'nama' => 'Lorenza Sheila Tansyah', 'status' => 'Hadir'],
    ['tanggal' => '15-07-2022', 'nim' => '3012800022', 'nama' => 'Wawan', 'status' => 'Hadir'],
];
$limit_per_page = isset($_GET['entries']) ? (($_GET['entries']==='manual_input' && isset($_GET['manual_entries'])) ? intval($_GET['manual_entries']) : intval($_GET['entries'])) : 10;
if ($limit_per_page <= 0) $limit_per_page = 10;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $limit_per_page;
$search_query = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$filtered = $data_absensi;
if ($search_query !== '') {
    $filtered = array_filter($filtered, function($row) use ($search_query) {
        return strpos(strtolower($row['nim']), $search_query) !== false || strpos(strtolower($row['nama']), $search_query) !== false;
    });
}
$total_records = count($filtered);
$total_pages = ceil($total_records / $limit_per_page);
$filtered = array_slice(array_values($filtered), $offset, $limit_per_page);
session_start();
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'User';
$foto = isset($_SESSION['foto']) && $_SESSION['foto'] ? 'uploads/laboran/' . $_SESSION['foto'] : 'user.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Absensi Kehadiran</title>
    <link rel="stylesheet" href="absensi_kehadiran.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="unibba-logo.png" alt="Logo" class="sidebar-logo">
            <h3>DAFTAR MENU PRAKTIKUM</h3>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.html"><i class="icon">🏠</i> Dashboard</a></li>
            <li><a href="jadwal_praktikum.php"><i class="icon">🗓️</i> Jadwal Praktikum</a></li>
            <li><a href="kelas.php"><i class="icon">🏫</i> Kelas</a></li>
            <li><a href="praktikan.php"><i class="icon">✍️</i> Praktikan</a></li>
            <li><a href="laporan_absensi.php" class="active"><i class="icon">✅</i> Absensi Kehadiran</a></li>
            <li><a href="mata_praktikum.html"><i class="icon">📚</i> Mata Praktikum</a></li>
            <li><a href="asisten_praktikum.php"><i class="icon">🧑‍🏫</i> Asisten Praktikum</a></li>
            <li><a href="ruang_laboratorium.html"><i class="icon">🔬</i> Ruang Laboratorium</a></li>
            <li><a href="laboran.php"><i class="icon">📄</i> Laboran</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="top-bar">
            <div class="title-breadcrumb">
                <h2>Daftar Absensi Kehadiran</h2>
                <span class="breadcrumb">Data Master Absensi Kehadiran, Menampilkan Laba absensi kehadiran</span>
            </div>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($nama); ?></span>
                <img src="<?php echo htmlspecialchars($foto); ?>" alt="User" class="user-avatar">
            </div>
        </div>
        <div class="attendance-container">
            <div class="attendance-header" style="background:#22a32e;color:#fff;padding:16px 24px 8px 24px;border-radius:12px 12px 0 0;display:flex;align-items:center;justify-content:space-between;">
                <div style="font-size:1.6em;font-weight:bold;display:flex;align-items:center;gap:10px;">
                    <span class="header-icon" style="font-size:1.2em;">📋</span> Daftar Absensi Kehadiran
                </div>
                <button class="btn-purple" id="btn-cetak" style="margin-left:16px;">Cetak</button>
            </div>
            <div class="attendance-details-grid">
                <div><b>Tahun Ajaran :</b> <?php echo $jadwal['tahun_ajaran']; ?></div>
                <div><b>Nama Ruang :</b> <?php echo $jadwal['ruang']; ?></div>
                <div><b>Nama Asisten Praktikum :</b> <?php echo htmlspecialchars($asisten['nama_asisten']); ?></div>
                <div><b>Nama Kelas :</b> <?php echo $jadwal['kelas']; ?></div>
                <div><b>Hari :</b> <?php echo $jadwal['hari']; ?></div>
                <div><b>Nama Mata Kuliah :</b> <?php echo $jadwal['mata_kuliah']; ?></div>
                <div><b>Waktu :</b> <?php echo $jadwal['waktu']; ?></div>
                <div><b>Semester :</b> <?php echo $jadwal['semester']; ?></div>
            </div>
            <div class="data-table" style="background:#fff;padding:0 24px 24px 24px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                    <div>
                        <label>Show
                            <select id="entriesSelect">
                                <option value="10" <?php if($limit_per_page==10) echo 'selected'; ?>>10</option>
                                <option value="25" <?php if($limit_per_page==25) echo 'selected'; ?>>25</option>
                                <option value="50" <?php if($limit_per_page==50) echo 'selected'; ?>>50</option>
                                <option value="100" <?php if($limit_per_page==100) echo 'selected'; ?>>100</option>
                                <option value="manual_input" <?php if(!in_array($limit_per_page,[10,25,50,100])) echo 'selected'; ?>>Lainnya</option>
                            </select>
                            <input type="number" id="manualEntriesInput" min="1" style="width: 60px; display:<?php echo (!in_array($limit_per_page,[10,25,50,100])) ? '' : 'none'; ?>; margin-left:5px;" placeholder="Jumlah" value="<?php echo (!in_array($limit_per_page,[10,25,50,100])) ? $limit_per_page : ''; ?>">
                            entries
                        </label>
                    </div>
                    <div>
                        <label>Search: <input type="text" id="searchInput" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Cari..."></label>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = $offset + 1;
                        foreach ($filtered as $row) {
                            echo '<tr>';
                            echo '<td>'.$no++.'</td>';
                            echo '<td>'.htmlspecialchars($row['tanggal']).'</td>';
                            echo '<td>'.htmlspecialchars($row['nim']).'</td>';
                            echo '<td>'.htmlspecialchars($row['nama']).'</td>';
                            echo '<td>'.htmlspecialchars($row['status']).'</td>';
                            echo '</tr>';
                        }
                        if (count($filtered) == 0) {
                            echo '<tr><td colspan="5" style="text-align:center;">Tidak ada data absensi</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
                <div class="praktikan-pagination" style="margin-top:8px;">
                    <a href="?<?php
                        $params = $_GET;
                        $params['page'] = max(1, $current_page-1);
                        echo http_build_query($params);
                    ?>" class="btn-page" <?php if($current_page<=1) echo 'style="pointer-events:none;opacity:0.5;"'; ?>>Previous</a>
                    <?php for($i=1;$i<=$total_pages;$i++): ?>
                        <a href="?<?php
                            $params = $_GET;
                            $params['page'] = $i;
                            echo http_build_query($params);
                        ?>" class="btn-page<?php if($i==$current_page) echo ' active'; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <a href="?<?php
                        $params = $_GET;
                        $params['page'] = min($total_pages, $current_page+1);
                        echo http_build_query($params);
                    ?>" class="btn-page" <?php if($current_page>=$total_pages) echo 'style="pointer-events:none;opacity:0.5;"'; ?>>Next</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- AREA CETAK KHUSUS PRINT -->
<div id="print-area" class="print-area">
    <div style="display: flex; align-items: center; margin-bottom: 8px;">
        <img src="unibba-logo.png" alt="Logo Unibba" style="height: 60px; margin-right: 16px;">
        <div style="flex:1; text-align: center;">
            <div style="font-size: 1.2em; font-weight: bold;">DAFTAR ABSENSI KEHADIRAN</div>
            <div style="font-size: 1.1em;">Fakultas Teknologi Informasi Universitas Bale Bandung</div>
        </div>
    </div>
    <div style="margin-bottom:10px;">
        <span style="margin-right:24px;">Tahun Ajaran: <?php echo $jadwal['tahun_ajaran']; ?></span>
        <span style="margin-right:24px;">Nama Asisten: <?php echo htmlspecialchars($asisten['nama_asisten']); ?></span>
        <span style="margin-right:24px;">Hari: <?php echo $jadwal['hari']; ?></span>
        <span style="margin-right:24px;">Waktu: <?php echo $jadwal['waktu']; ?></span>
        <span style="margin-right:24px;">Ruang: <?php echo $jadwal['ruang']; ?></span>
        <span style="margin-right:24px;">Kelas: <?php echo $jadwal['kelas']; ?></span>
        <span style="margin-right:24px;">Mata Kuliah: <?php echo $jadwal['mata_kuliah']; ?></span>
        <span style="margin-right:24px;">Semester: <?php echo $jadwal['semester']; ?></span>
    </div>
    <table border="1" cellspacing="0" cellpadding="6" style="width:100%; border-collapse:collapse; font-size:0.95em;">
        <thead>
            <tr style="background:#f0f0f0;">
                <th>No</th>
                <th>Tanggal</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($data_absensi as $row) {
                echo '<tr>';
                echo '<td>'.$no++.'</td>';
                echo '<td>'.htmlspecialchars($row['tanggal']).'</td>';
                echo '<td>'.htmlspecialchars($row['nim']).'</td>';
                echo '<td>'.htmlspecialchars($row['nama']).'</td>';
                echo '<td>'.htmlspecialchars($row['status']).'</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>
<style>
.print-area { display: none; }
@media print {
    body, html { background: #fff !important; }
    .dashboard-container, .main-content, .sidebar, .top-bar, .attendance-header, .attendance-details, .praktikan-pagination, .search-box, .btn, button, .table-info-text, .breadcrumb, .user-info, .data-table { display: none !important; }
    .print-area { display: block !important; margin: 0; padding: 0; }
    .print-area table { page-break-inside: auto; }
    .print-area th, .print-area td { font-size: 1em; }
}
.attendance-details-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  grid-template-rows: repeat(2, auto);
  gap: 8px 24px;
  margin-bottom: 16px;
  background: #fff;
  padding: 16px 24px 0 24px;
  font-size: 1.08em;
}
.attendance-details-grid div {
  font-weight: 500;
}
.attendance-details-grid b {
  font-weight: bold;
}
.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
    position: absolute;
    top: 20px;
    right: 40px;
    z-index: 10;
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
<script>
document.getElementById('btn-cetak').onclick = function() { window.print(); };
// Show Entries & Search Otomatis
const entriesSelect = document.getElementById('entriesSelect');
const manualEntriesInput = document.getElementById('manualEntriesInput');
const searchInput = document.getElementById('searchInput');
(function(){
    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.get('entries') === 'manual_input'){
        entriesSelect.value = 'manual_input';
        manualEntriesInput.style.display = '';
        manualEntriesInput.value = urlParams.get('manual_entries') || '';
    } else {
        entriesSelect.value = urlParams.get('entries') || '10';
        manualEntriesInput.style.display = 'none';
    }
})();
entriesSelect.addEventListener('change', function(){
    if(this.value === 'manual_input'){
        manualEntriesInput.style.display = '';
        manualEntriesInput.focus();
    } else {
        manualEntriesInput.style.display = 'none';
        const url = new URL(window.location.href);
        url.searchParams.set('entries', this.value);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }
});
manualEntriesInput.addEventListener('input', function(){
    if(this.value && parseInt(this.value) > 0){
        const url = new URL(window.location.href);
        url.searchParams.set('entries', 'manual_input');
        url.searchParams.set('manual_entries', this.value);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }
});
searchInput.addEventListener('input', function(){
    const url = new URL(window.location.href);
    url.searchParams.set('search', this.value);
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
});
</script>
</body>
</html> 