<?php
require_once 'db_connect.php';
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
                <h2>Daftar Detail Kelas <?php echo htmlspecialchars($nama_kelas); ?></h2>
                <span class="breadcrumb">Data Master Detail Kelas, Menampilkan Data Detail Kelas</span>
            </div>
            <div class="user-info">
                <span class="user-name">Uchiha Atep</span>
                <img src="user.png" alt="User" class="user-avatar">
            </div>
        </div>
        <div class="praktikan-box">
            <div class="praktikan-header-bar">
                <h3>Daftar Detail Kelas <?php echo htmlspecialchars($nama_kelas); ?> (Semester <?php echo htmlspecialchars($semester); ?>)</h3>
            </div>
            <div class="praktikan-actions-bar">
                <button class="btn-green" id="show-add-form">+ Tambah Praktikan</button>
                <button class="btn-purple">Cetak</button>
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
                updateRowNumbers();
            };
            document.getElementById('edit-detailkelas-tbody').onclick = function(e) {
                if (e.target.classList.contains('btn-del-row')) {
                    const row = e.target.closest('tr');
                    row.parentNode.removeChild(row);
                    updateRowNumbers();
                }
            };
            function updateRowNumbers() {
                const rows = document.querySelectorAll('#edit-detailkelas-tbody tr');
                rows.forEach((tr, i) => tr.children[0].textContent = i + 1);
            }
            </script>
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['edit']) && $_GET['edit'] == '1' && isset($_POST['praktikan'])) {
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
                                <button type="button" class="btn-orange" id="show-edit-form">Edit</button>
                                <button type="button" class="btn-red" id="delete-selected">Hapus</button>
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
</style>
<script>
// Show/Hide Add Praktikan Form (dummy, bisa dikembangkan)
document.getElementById('show-add-form').onclick = function() {
    window.location.href = 'detail_kelas.php?id=<?php echo $id_kelas; ?>&add=1';
};
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
// Checkbox Select All
const selectAll = document.getElementById('select-all');
const rowCheckboxes = document.querySelectorAll('.row-checkbox');
if (selectAll) {
    selectAll.addEventListener('change', function() {
        rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
    });
    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            if (!cb.checked) {
                selectAll.checked = false;
            } else {
                if ([...rowCheckboxes].every(c => c.checked)) {
                    selectAll.checked = true;
                }
            }
        });
    });
}
// Tombol Edit
const btnEdit = document.getElementById('show-edit-form');
if (btnEdit) {
    btnEdit.onclick = function() {
        window.location.href = 'detail_kelas.php?id=<?php echo $id_kelas; ?>&edit=1';
    };
}
// Tombol Hapus
document.getElementById('delete-selected').onclick = function() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Silakan pilih data yang akan dihapus dari kelas!');
        return;
    }
    if (!confirm('Apakah Anda yakin ingin menghapus data yang dipilih dari kelas ini?')) {
        return;
    }
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'detail_kelas.php?id=<?php echo $id_kelas; ?>&delete=1';
    checkedBoxes.forEach(checkbox => {
        const row = checkbox.closest('tr');
        const nim = row.cells[3].textContent;
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected_ids[]';
        input.value = nim;
        form.appendChild(input);
    });
    document.body.appendChild(form);
    form.submit();
};
// Pastikan tombol Cetak hanya mencetak print-area
const btnCetak = document.querySelector('.btn-purple');
if (btnCetak) btnCetak.onclick = function() { window.print(); };
</script>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['delete']) && $_GET['delete'] == '1' && isset($_POST['selected_ids'])) {
    foreach ($_POST['selected_ids'] as $nim) {
        $sql = "UPDATE praktikan SET kelas=NULL, semester=NULL WHERE nim = '".$conn->real_escape_string($nim)."'";
        $conn->query($sql);
    }
    echo "<script>alert('Data berhasil dihapus dari kelas!');window.location='detail_kelas.php?id=$id_kelas';</script>";
    exit;
}
?>
</body>
</html> 