<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perubahan Jadwal Praktikum</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="jadwal_praktikum.css">
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
                <li><a href="absensi_kehadiran.php"><i class="icon">✅</i> Absensi Kehadiran</a></li>
                <li><a href="mata_praktikum.php"><i class="icon">📚</i> Mata Praktikum</a></li>
                <li><a href="asisten_praktikum.php"><i class="icon">🧑‍🏫</i> Asisten Praktikum</a></li>
                <li><a href="ruang_laboratorium.html"><i class="icon">🔬</i> Ruang Laboratorium</a></li>
                <li><a href="laboran.php"><i class="icon">📄</i> Laboran</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="top-bar">
                <div class="title-breadcrumb">
                    <h2>Perubahan Jadwal Praktikum</h2>
                    <span class="breadcrumb">Data Master Perubahan Jadwal Praktikum, Menampilkan dan perubahan jadwal praktikum</span>
                </div>
                <div class="user-info">
                    <span class="user-name">Uchiha Atep</span>
                    <img src="user.png" alt="User" class="user-avatar">
                </div>
            </div>
            <div class="jadwal-box">
                <div class="jadwal-header-bar">
                    <h3>Data Perubahan Jadwal</h3>
                </div>
                <div class="jadwal-actions-bar">
                    <button class="btn-purple" onclick="window.print()">Cetak</button>
                </div>
                <div class="jadwal-table-section" id="jadwal-table-section">
                    <form id="filterForm" method="get" style="margin-bottom:0;">
                    <div class="jadwal-table-controls">
                        <div class="jadwal-table-left">
                            <label>Show
                                <select name="entries" id="entriesSelect">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="manual_input">Lainnya</option>
                                </select>
                                <input type="number" id="manualEntriesInput" name="manual_entries" min="1" style="width: 80px; display:none; margin-left:5px;" placeholder="Jumlah">
                                entries
                            </label>
                        </div>
                        <div class="jadwal-table-right">
                            <div class="search-box">
                                <label>Search: <input type="text" name="search" id="searchInput" placeholder="Cari..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"></label>
                            </div>
                        </div>
                    </div>
                    </form>
                    <div class="jadwal-table-wrapper">
                        <table class="jadwal-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Ubah</th>
                                    <th>Petugas</th>
                                    <th>Askum</th>
                                    <th>Kelas</th>
                                    <th>Matkul</th>
                                    <th>Hari</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include 'db_connect.php';
                                $limit_per_page = 10;
                                if (isset($_GET['entries'])) {
                                    if ($_GET['entries'] === 'manual_input' && isset($_GET['manual_entries']) && intval($_GET['manual_entries']) > 0) {
                                        $limit_per_page = intval($_GET['manual_entries']);
                                    } else if (in_array($_GET['entries'], ['10','25','50','100'])) {
                                        $limit_per_page = intval($_GET['entries']);
                                    }
                                }
                                $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                                $offset = ($current_page - 1) * $limit_per_page;
                                $search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
                                $where_clause = '';
                                if (!empty($search_query)) {
                                    $where_clause = " WHERE petugas LIKE '%$search_query%' OR askum LIKE '%$search_query%' OR kelas LIKE '%$search_query%' OR matkul LIKE '%$search_query%' OR hari LIKE '%$search_query%' OR waktu LIKE '%$search_query%'";
                                }
                                $total_sql = "SELECT COUNT(id) AS total FROM perubahan_jadwal" . $where_clause;
                                $total_result = $conn->query($total_sql);
                                $total_row = $total_result->fetch_assoc();
                                $total_records = $total_row['total'];
                                $total_pages = ceil($total_records / $limit_per_page);
                                $sql = "SELECT * FROM perubahan_jadwal" . $where_clause . " ORDER BY tanggal_ubah DESC, id DESC LIMIT $limit_per_page OFFSET $offset";
                                $result = $conn->query($sql);
                                if ($result && $result->num_rows > 0) {
                                    $no = $offset + 1;
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                            <td>" . $no . "</td>
                                            <td>" . htmlspecialchars(date('d-m-Y', strtotime($row['tanggal_ubah']))) . "</td>
                                            <td>" . htmlspecialchars($row['petugas']) . "</td>
                                            <td>" . htmlspecialchars($row['askum']) . "</td>
                                            <td>" . htmlspecialchars($row['kelas']) . "</td>
                                            <td>" . htmlspecialchars($row['matkul']) . "</td>
                                            <td>" . htmlspecialchars($row['hari']) . "</td>
                                            <td>" . htmlspecialchars($row['waktu']) . "</td>
                                            <td><form method='post' action='delete_perubahan_jadwal.php' style='display:inline;'><input type='hidden' name='id' value='".$row['id']."'><button type='submit' class='btn-red' onclick='return confirm(\'Yakin hapus data ini?\')'>🗑️</button></form></td>
                                        </tr>";
                                        $no++;
                                    }
                                } else {
                                    echo "<tr><td colspan='9' style='text-align: center;'>Tidak ada data perubahan jadwal</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="jadwal-pagination">
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
                        ?>" class="btn-page" <?php if($current_page>=$total_pages) echo 'style=\"pointer-events:none;opacity:0.5;\"'; ?>>Next</a>
                    </div>
                </div>
                <script>
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
                        document.getElementById('filterForm').submit();
                    }
                });
                manualEntriesInput.addEventListener('input', function(){
                    if(this.value && parseInt(this.value) > 0){
                        document.getElementById('filterForm').submit();
                    } else if(this.value === '' || parseInt(this.value) < 1) {
                        entriesSelect.value = '10';
                        manualEntriesInput.style.display = 'none';
                        manualEntriesInput.value = '';
                        const url = new URL(window.location.href);
                        url.searchParams.set('entries', '10');
                        url.searchParams.delete('manual_entries');
                        url.searchParams.set('page', '1');
                        window.location.href = url.toString();
                    }
                });
                searchInput.addEventListener('input', function(){
                    document.getElementById('filterForm').submit();
                });
                </script>
            </div>
        </div>
    </div>
    <!-- AREA CETAK KHUSUS PRINT -->
    <div id="print-area" class="print-area">
        <div style="display: flex; align-items: center; margin-bottom: 8px;">
            <img src="unibba-logo.png" alt="Logo Unibba" style="height: 60px; margin-right: 16px;">
            <div style="flex:1; text-align: center;">
                <div style="font-size: 1.2em; font-weight: bold;">DATA PERUBAHAN JADWAL PRAKTIKUM</div>
                <div style="font-size: 1.1em;">Fakultas Teknologi Informasi Universitas Bale Bandung</div>
            </div>
        </div>
        <table border="1" cellspacing="0" cellpadding="6" style="width:100%; border-collapse:collapse; font-size:0.95em;">
            <thead>
                <tr style="background:#f0f0f0;">
                    <th>No</th>
                    <th>Tanggal Ubah</th>
                    <th>Petugas</th>
                    <th>Askum</th>
                    <th>Kelas</th>
                    <th>Matkul</th>
                    <th>Hari</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'db_connect.php';
                $search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
                $where_clause = '';
                if (!empty($search_query)) {
                    $where_clause = " WHERE petugas LIKE '%$search_query%' OR askum LIKE '%$search_query%' OR kelas LIKE '%$search_query%' OR matkul LIKE '%$search_query%' OR hari LIKE '%$search_query%' OR waktu LIKE '%$search_query%'";
                }
                $sql_print = "SELECT * FROM perubahan_jadwal" . $where_clause . " ORDER BY tanggal_ubah DESC, id DESC";
                $result_print = $conn->query($sql_print);
                if ($result_print && $result_print->num_rows > 0) {
                    $no = 1;
                    while($row = $result_print->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . htmlspecialchars(date('d-m-Y', strtotime($row['tanggal_ubah']))) . "</td>";
                        echo "<td>" . htmlspecialchars($row['petugas']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['askum']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['kelas']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['matkul']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['hari']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['waktu']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' style='text-align:center;'>Tidak ada data perubahan jadwal</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <style>
    .print-area { display: none; }
    @media print {
        body, html { background: #fff !important; }
        .dashboard-container, .main-content, .sidebar, .top-bar, .jadwal-box, .jadwal-header-bar, .jadwal-actions-bar, .jadwal-table-section, .jadwal-table-controls, .jadwal-table-wrapper, .jadwal-pagination, .search-box, .btn, button, .table-info-text, .breadcrumb, .user-info { display: none !important; }
        .print-area { display: block !important; margin: 0; padding: 0; }
        .print-area table { page-break-inside: auto; }
        .print-area th, .print-area td { font-size: 1em; }
    }
    </style>
</body>
</html> 