<?php
require_once 'db_connect.php';
session_start();
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'User';
$foto = isset($_SESSION['foto']) && $_SESSION['foto'] ? 'uploads/laboran/' . $_SESSION['foto'] : 'user.png';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
// Ambil id kelas dari URL
$id_kelas = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_kelas <= 0) {
    die('Kelas tidak ditemukan.');
}
// Ambil info kelas
$sql_kelas = "SELECT * FROM kelas WHERE id = $id_kelas";
$result_kelas = $conn->query($sql_kelas);
if (!$result_kelas || $result_kelas->num_rows == 0) {
    die('Kelas tidak ditemukan.');
}
$kelas = $result_kelas->fetch_assoc();
$nama_kelas = $kelas['nama_kelas'];
$semester = $kelas['semester'];
// Pagination & Search
$limit_per_page = isset($_GET['entries']) ? (($_GET['entries']==='manual_input' && isset($_GET['manual_entries'])) ? intval($_GET['manual_entries']) : intval($_GET['entries'])) : 10;
if ($limit_per_page <= 0) $limit_per_page = 10;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $limit_per_page;
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$where = "WHERE kelas = '" . $conn->real_escape_string($nama_kelas) . "' AND semester = '" . $conn->real_escape_string($semester) . "'";
if (!empty($search_query)) {
    $where .= " AND (nim LIKE '%$search_query%' OR nama_lengkap LIKE '%$search_query%')";
}
$total_sql = "SELECT COUNT(nim) AS total FROM praktikan $where";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit_per_page);
$sql = "SELECT * FROM praktikan $where ORDER BY nim ASC LIMIT $limit_per_page OFFSET $offset";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Detail Kelas <?php echo htmlspecialchars($nama_kelas); ?></title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="praktikan.css">
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
            <li><a href="kelas.php" class="active"><i class="icon">🏫</i> Kelas</a></li>
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
            <div class="title-breadcrumb">
                <h2>Daftar Detail Kelas <?php echo htmlspecialchars($nama_kelas); ?></h2>
                <span class="breadcrumb">Data Master Detail Kelas, Menampilkan Data Detail Kelas</span>
            </div>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($nama); ?></span>
                <img src="<?php echo htmlspecialchars($foto); ?>" alt="User" class="user-avatar">
            </div>
        </div>
        <div class="praktikan-box">
            <div class="praktikan-header-bar">
                <h3>Daftar Detail Kelas <?php echo htmlspecialchars($nama_kelas); ?> (Semester <?php echo htmlspecialchars($semester); ?>)</h3>
            </div>
            <div class="praktikan-actions-bar">
                <?php if($role !== 'kepala'): ?>
                <button class="btn-green" id="show-add-form">+ Tambah Praktikan</button>
                <?php endif; ?>
                <button class="btn-purple" onclick="window.print()">Cetak</button>
            </div>

            <!-- FORM TAMBAH PRAKTIKAN KE KELAS -->
            <div class="praktikan-add-section" id="praktikan-add-section" style="display: none;">
                <div class="praktikan-add-header" style="background:#28a745;color:#fff;padding:10px 20px;border-radius:6px 6px 0 0;">
                    <span class="add-icon">➕</span> <span class="add-title">Tambah Praktikan ke Kelas <?php echo htmlspecialchars($nama_kelas); ?></span>
                </div>
                <form id="form-add-detail-kelas" method="post" action="detail_kelas.php?id=<?php echo $id_kelas; ?>&add=1" autocomplete="off">
                    <input type="hidden" name="semester" value="<?php echo htmlspecialchars($semester); ?>">
                    <table class="praktikan-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Praktikan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="add-detailkelas-tbody">
                            <!-- Baris pertama -->
                            <tr>
                                <td>1</td>
                                <td>
                                    <select name="praktikan[]" required>
                                        <option value="">- Pilih Praktikan -</option>
                                        <?php
                                        // Dropdown: semua praktikan yang belum punya kelas
                                        $praktikan_sql = "SELECT nim, nama_lengkap FROM praktikan WHERE kelas IS NULL OR kelas = '' ORDER BY nama_lengkap ASC";
                                        $praktikan_res = $conn->query($praktikan_sql);
                                        while($row = $praktikan_res->fetch_assoc()) {
                                            echo '<option value="'.htmlspecialchars($row['nim']).'">'.htmlspecialchars($row['nama_lengkap']).' ('.$row['nim'].')</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td><button type="button" class="btn-del-row" style="background:#e74c3c;color:#fff;border:none;padding:6px 10px;border-radius:4px;">✖</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="praktikan-add-footer" style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;">
                        <button type="button" class="btn-purple" id="add-row-tambah">+ Baris Baru</button>
                        <div class="footer-right">
                            <button type="reset" class="btn-reset">Reset</button>
                            <button type="submit" class="btn-green">Simpan</button>
                            <button type="button" class="btn-back" id="hide-add-form">Back</button>
                        </div>
                    </div>
                </form>
            </div>

            <?php if (isset($_GET['edit']) && $_GET['edit'] == '1'): ?>
            <!-- FORM EDIT MULTI DETAIL KELAS -->
            <div class="praktikan-add-section" id="praktikan-edit-section">
                <div class="praktikan-add-header" style="background:#a259e6;color:#fff;padding:10px 20px;border-radius:6px 6px 0 0;">
                    <span class="add-icon">✏️</span> <span class="add-title">Edit Detail Kelas</span>
                </div>
                <form id="form-edit-detail-kelas" method="post" action="detail_kelas.php?id=<?php echo $id_kelas; ?>&edit=1" autocomplete="off">
                    <table class="praktikan-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kelas</th>
                                <th>Nama Praktikan</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="edit-detailkelas-tbody">
                            <?php
                            $praktikan_kelas_sql = "SELECT nim, nama_lengkap FROM praktikan WHERE kelas = '".$conn->real_escape_string($nama_kelas)."' AND semester = '".$conn->real_escape_string($semester)."' ORDER BY nim ASC";
                            $praktikan_kelas_res = $conn->query($praktikan_kelas_sql);
                            $no = 1;
                            while($p = $praktikan_kelas_res->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>'.$no.'</td>';
                                echo '<td><input type="text" name="kelas[]" value="'.htmlspecialchars($nama_kelas).'" readonly></td>';
                                echo '<td><select name="praktikan[]" required>';
                                // Dropdown: semua praktikan yang belum masuk kelas ini + yang sedang di baris ini
                                $praktikan_sql = "SELECT nim, nama_lengkap FROM praktikan WHERE kelas IS NULL OR kelas='' OR kelas='".$conn->real_escape_string($nama_kelas)."' ORDER BY nama_lengkap ASC";
                                $praktikan_res = $conn->query($praktikan_sql);
                                while($row = $praktikan_res->fetch_assoc()) {
                                    $selected = ($row['nim'] == $p['nim']) ? 'selected' : '';
                                    echo '<option value="'.htmlspecialchars($row['nim']).'" '.$selected.'>'.htmlspecialchars($row['nama_lengkap']).' ('.$row['nim'].')</option>';
                                }
                                echo '</select></td>';
                                echo '<td><button type="button" class="btn-del-row" style="background:#e74c3c;color:#fff;border:none;padding:6px 10px;border-radius:4px;">✖</button></td>';
                                echo '</tr>';
                                $no++;
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="praktikan-add-footer" style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;">
                        <button type="button" class="btn-purple" id="add-row-edit">+ Baris Baru</button>
                        <div class="footer-right">
                            <button type="reset" class="btn-reset">Reset</button>
                            <button type="submit" class="btn-green">Simpan</button>
                            <a href="detail_kelas.php?id=<?php echo $id_kelas; ?>" class="btn-back">Back</a>
                        </div>
                    </div>
                </form>
                <div style="margin-top:10px;color:#555;font-size:0.95em;">Edit Data Praktikan, isi form diatas untuk mengubah data Praktikan di kelas ini.</div>
            </div>
            <script>
            document.getElementById('add-row-edit').onclick = function() {
                const tbody = document.getElementById('edit-detailkelas-tbody');
                const rowCount = tbody.rows.length + 1;
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${rowCount}</td>
                    <td><input type=\"text\" name=\"kelas[]\" value=\"<?php echo htmlspecialchars($nama_kelas); ?>\" readonly></td>
                    <td><select name=\"praktikan[]\" required><?php
                    $praktikan_sql = "SELECT nim, nama_lengkap FROM praktikan WHERE kelas IS NULL OR kelas='' OR kelas='".$conn->real_escape_string($nama_kelas)."' ORDER BY nama_lengkap ASC";
                    $praktikan_res = $conn->query($praktikan_sql);
                    while($row = $praktikan_res->fetch_assoc()) {
                        echo '<option value=\\"'.htmlspecialchars($row['nim']).'\\">'.htmlspecialchars($row['nama_lengkap']).' ('.$row['nim'].')</option>';
                    }
                    ?></select></td>
                    <td><button type=\"button\" class=\"btn-del-row\" style=\"background:#e74c3c;color:#fff;border:none;padding:6px 10px;border-radius:4px;\">✖</button></td>`;
                tbody.appendChild(tr);
                updateRowNumbers('edit-detailkelas-tbody');
            };
            document.getElementById('edit-detailkelas-tbody').onclick = function(e) {
                if (e.target.classList.contains('btn-del-row')) {
                    const row = e.target.closest('tr');
                    row.parentNode.removeChild(row);
                    updateRowNumbers('edit-detailkelas-tbody');
                }
            };
            function updateRowNumbers(tbodyId) {
                const rows = document.querySelectorAll(`#${tbodyId} tr`);
                rows.forEach((tr, i) => tr.children[0].textContent = i + 1);
            }
            </script>
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['edit']) && $_GET['edit'] == '1' && isset($_POST['praktikan'])) {
                if($role === 'kepala') {
                    header('Location: detail_kelas.php?id='.$id_kelas.'&error=akses');
                    exit;
                }
                // Reset semua praktikan di kelas ini
                $conn->query("UPDATE praktikan SET kelas=NULL, semester=NULL WHERE kelas = '".$conn->real_escape_string($nama_kelas)."' AND semester = '".$conn->real_escape_string($semester)."'");
                // Update praktikan baru
                $praktikan_arr = $_POST['praktikan'];
                foreach ($praktikan_arr as $nim) {
                    $sql_update = "UPDATE praktikan SET kelas = '".$conn->real_escape_string($nama_kelas)."', semester = '".$conn->real_escape_string($semester)."' WHERE nim = '".$conn->real_escape_string($nim)."'";
                    $conn->query($sql_update);
                }
                echo "<script>alert('Data berhasil diupdate!');window.location='detail_kelas.php?id=$id_kelas';</script>";
                exit;
            }
            ?>
            <?php else: ?>
            <!-- TABEL UTAMA DETAIL KELAS -->
            <div class="praktikan-table-section" id="praktikan-table-section">
                <form id="filterForm" method="get" style="margin-bottom:0;">
                    <input type="hidden" name="id" value="<?php echo $id_kelas; ?>">
                    <div class="praktikan-table-controls">
                        <div class="praktikan-table-left">
                            <label>Show
                                <select name="entries" id="entriesSelect">
                                    <option value="10" <?php if($limit_per_page==10) echo 'selected'; ?>>10</option>
                                    <option value="25" <?php if($limit_per_page==25) echo 'selected'; ?>>25</option>
                                    <option value="50" <?php if($limit_per_page==50) echo 'selected'; ?>>50</option>
                                    <option value="100" <?php if($limit_per_page==100) echo 'selected'; ?>>100</option>
                                    <option value="manual_input" <?php if(!in_array($limit_per_page,[10,25,50,100])) echo 'selected'; ?>>Lainnya</option>
                                </select>
                                <input type="number" id="manualEntriesInput" name="manual_entries" min="1" style="width: 80px; display:<?php echo (!in_array($limit_per_page,[10,25,50,100])) ? '' : 'none'; ?>; margin-left:5px;" placeholder="Jumlah" value="<?php echo (!in_array($limit_per_page,[10,25,50,100])) ? $limit_per_page : ''; ?>">
                                entries
                            </label>
                        </div>
                        <div class="praktikan-table-right">
                            <div class="table-actions-group">
                                <button class="btn-warning" id="btn-edit" <?php if($role==='kepala') echo 'disabled style="opacity:0.6;pointer-events:none;"'; ?>>Edit</button>
                                <button class="btn-danger" id="btn-hapus" <?php if($role==='kepala') echo 'disabled style="opacity:0.6;pointer-events:none;"'; ?>>Hapus</button>
                            </div>
                            <div class="search-box">
                                <label>Search: <input type="text" name="search" id="searchInput" placeholder="Cari..." value="<?php echo htmlspecialchars($search_query); ?>"></label>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="praktikan-table-wrapper">
                    <table class="praktikan-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kelas</th>
                                <th>Semester</th>
                                <th>NIM</th>
                                <th>Nama Praktikan</th>
                                <th><input type="checkbox" id="select-all" title="Pilih Semua"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            $no = $offset + 1;
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $no . "</td>";
                                echo "<td>" . htmlspecialchars($nama_kelas) . "</td>";
                                echo "<td>" . htmlspecialchars($semester) . "</td>";
                                echo "<td>" . htmlspecialchars($row['nim']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['nama_lengkap']) . "</td>";
                                echo "<td><input type='checkbox' class='row-checkbox'></td>";
                                echo "</tr>";
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align: center;'>Tidak ada data praktikan di kelas ini</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="praktikan-pagination">
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
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- AREA CETAK KHUSUS PRINT -->
<div id="print-area" class="print-area">
    <div style="display: flex; align-items: center; margin-bottom: 8px;">
        <img src="unibba-logo.png" alt="Logo Unibba" style="height: 60px; margin-right: 16px;">
        <div style="flex:1; text-align: center;">
            <div style="font-size: 1.2em; font-weight: bold;">DAFTAR DETAIL KELAS <?php echo htmlspecialchars($nama_kelas); ?> (Semester <?php echo htmlspecialchars($semester); ?>)</div>
            <div style="font-size: 1.1em;">Fakultas Teknologi Informasi Universitas Bale Bandung</div>
        </div>
    </div>
    <table border="1" cellspacing="0" cellpadding="6" style="width:100%; border-collapse:collapse; font-size:0.95em;">
        <thead>
            <tr style="background:#f0f0f0;">
                <th>No</th>
                <th>Nama Kelas</th>
                <th>Semester</th>
                <th>NIM</th>
                <th>Nama Praktikan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql_print = "SELECT * FROM praktikan WHERE kelas = '".$conn->real_escape_string($nama_kelas)."' AND semester = '".$conn->real_escape_string($semester)."' ORDER BY nim ASC";
            $result_print = $conn->query($sql_print);
            if ($result_print && $result_print->num_rows > 0) {
                $no = 1;
                while($row = $result_print->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($nama_kelas) . "</td>";
                    echo "<td>" . htmlspecialchars($semester) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nim']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_lengkap']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>Tidak ada data praktikan di kelas ini</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<style>
.print-area { display: none; }
@media print {
    body, html { background: #fff !important; }
    .dashboard-container, .main-content, .sidebar, .top-bar, .praktikan-box, .praktikan-header-bar, .praktikan-actions-bar, .praktikan-table-section, .praktikan-table-controls, .praktikan-table-wrapper, .praktikan-pagination, .search-box, .btn, button, .table-info-text, .breadcrumb, .user-info, .praktikan-add-section { display: none !important; }
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
document.addEventListener('DOMContentLoaded', function() {
    const showAddFormBtn = document.getElementById('show-add-form');
    const hideAddFormBtn = document.getElementById('hide-add-form');
    const addSection = document.getElementById('praktikan-add-section');
    const editSection = document.getElementById('praktikan-edit-section');
    const tableSection = document.getElementById('praktikan-table-section');
    
    // Tombol + Tambah Praktikan
    if(showAddFormBtn) {
        showAddFormBtn.addEventListener('click', function() {
            tableSection.style.display = 'none';
            if(editSection) editSection.style.display = 'none';
            addSection.style.display = 'block';
        });
    }

    // Tombol Back di form Tambah
    if(hideAddFormBtn) {
        hideAddFormBtn.addEventListener('click', function() {
            addSection.style.display = 'none';
            tableSection.style.display = 'block';
        });
    }

    // --- Logika untuk Form Edit ---
    const showEditFormBtn = document.getElementById('btn-edit');
    if(showEditFormBtn) {
        showEditFormBtn.addEventListener('click', function() {
            window.location.href = '?id=<?php echo $id_kelas; ?>&edit=1';
        });
    }

    // --- Logika untuk Tambah Baris di Form Tambah ---
    const addRowTambahBtn = document.getElementById('add-row-tambah');
    if (addRowTambahBtn) {
        addRowTambahBtn.onclick = function() {
            const tbody = document.getElementById('add-detailkelas-tbody');
            const rowCount = tbody.rows.length + 1;
            const tr = document.createElement('tr');
            
            // Kloning dropdown dari baris pertama untuk efisiensi
            const firstRowSelect = tbody.querySelector('select');
            const newSelect = firstRowSelect.cloneNode(true);
            
            tr.innerHTML = `<td>${rowCount}</td>
                <td></td>
                <td><button type="button" class="btn-del-row" style="background:#e74c3c;color:#fff;border:none;padding:6px 10px;border-radius:4px;">✖</button></td>`;
            
            // Masukkan select ke dalam sel kedua
            tr.cells[1].appendChild(newSelect);
            tbody.appendChild(tr);
        };
    }

    // Hapus baris di form Tambah
    document.getElementById('add-detailkelas-tbody').onclick = function(e) {
        if (e.target.classList.contains('btn-del-row')) {
            const row = e.target.closest('tr');
            if (document.querySelectorAll('#add-detailkelas-tbody tr').length > 1) {
                row.parentNode.removeChild(row);
                updateRowNumbers('add-detailkelas-tbody');
            } else {
                alert('Minimal harus ada satu baris.');
            }
        }
    };
});
</script>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['delete']) && $_GET['delete'] == '1' && isset($_POST['selected_ids'])) {
    if($role === 'kepala') {
        header('Location: detail_kelas.php?id='.$id_kelas.'&error=akses');
        exit;
    }
    foreach ($_POST['selected_ids'] as $nim) {
        $sql = "UPDATE praktikan SET kelas=NULL, semester=NULL WHERE nim = '".$conn->real_escape_string($nim)."'";
        $conn->query($sql);
    }
    echo "<script>alert('Data berhasil dihapus dari kelas!');window.location='detail_kelas.php?id=$id_kelas';</script>";
    exit;
}
// Proses form TAMBAH
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['add']) && $_GET['add'] == '1' && isset($_POST['praktikan'])) {
    if($role === 'kepala') {
        header('Location: detail_kelas.php?id='.$id_kelas.'&error=akses');
        exit;
    }
    $praktikan_arr = array_unique($_POST['praktikan']);
    $semester_to_add = isset($_POST['semester']) ? intval($_POST['semester']) : 0; // Ambil semester dari form

    if ($semester_to_add > 0) {
        foreach ($praktikan_arr as $nim) {
            if (!empty($nim)) {
                $sql_update = "UPDATE praktikan SET kelas = ?, semester = ? WHERE nim = ?";
                $stmt = $conn->prepare($sql_update);
                $stmt->bind_param("sis", $nama_kelas, $semester_to_add, $nim);
                $stmt->execute();
            }
        }
        echo "<script>alert('Praktikan berhasil ditambahkan ke kelas!');window.location='detail_kelas.php?id=$id_kelas';</script>";
    } else {
        echo "<script>alert('Error: Semester tidak valid.');window.location='detail_kelas.php?id=$id_kelas';</script>";
    }
    exit;
}
?>
</body>
</html> 