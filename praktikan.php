<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Praktikan</title>
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
                <li><a href="kelas.php"><i class="icon">🏫</i> Kelas</a></li>
                <li><a href="praktikan.php" class="active"><i class="icon">✍️</i> Praktikan</a></li>
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
                    <h2>Daftar Praktikan</h2>
                    <span class="breadcrumb">Data Master Praktikan, Menampilkan Data Praktikan</span>
                </div>
                <div class="user-info">
                    <?php
                    session_start();
                    $nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'User';
                    $foto = isset($_SESSION['foto']) && $_SESSION['foto'] ? 'uploads/laboran/' . $_SESSION['foto'] : 'user.png';
                    $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
                    ?>
                    <span class="user-name"><?php echo htmlspecialchars($nama); ?></span>
                    <img src="<?php echo htmlspecialchars($foto); ?>" alt="User" class="user-avatar">
                </div>
            </div>
            <div class="praktikan-box">
                <div class="praktikan-header-bar">
                    <h3>Daftar Praktikan</h3>
                </div>
                <div class="praktikan-actions-bar">
                    <button class="btn-green" id="show-add-form" <?php if($role==='kepala') echo 'disabled style="opacity:0.6;pointer-events:none;"'; ?>>+ Tambah Praktikan</button>
                    <button class="btn-purple">Cetak</button>
                </div>
                <!-- Tabel Daftar Praktikan -->
                <div class="praktikan-table-section" id="praktikan-table-section">
                    <form id="filterForm" method="get" style="margin-bottom:0;">
                    <div class="praktikan-table-controls">
                        <div class="praktikan-table-left">
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
                        <div class="praktikan-table-right">
                            <div class="table-actions-group">
                                <button type="button" class="btn-orange" id="btn-edit" <?php if($role==='kepala') echo 'disabled style="opacity:0.6;pointer-events:none;"'; ?>>Edit</button>
                                <button type="button" class="btn-red" id="btn-hapus" <?php if($role==='kepala') echo 'disabled style="opacity:0.6;pointer-events:none;"'; ?>>Hapus</button>
                            </div>
                            <div class="search-box">
                                <label>Search: <input type="text" name="search" id="searchInput" placeholder="Cari..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"></label>
                            </div>
                        </div>
                    </div>
                    </form>
                    <div class="praktikan-table-wrapper">
                        <table class="praktikan-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Nama Lengkap</th>
                                    <th>Alamat</th>
                                    <th>Tgl Lahir</th>
                                    <th>Prodi</th>
                                    <th><input type="checkbox" id="select-all" title="Pilih Semua"></th>
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
                                    $where_clause = " WHERE nim LIKE '%$search_query%' OR nama_lengkap LIKE '%$search_query%' OR alamat LIKE '%$search_query%' OR prodi LIKE '%$search_query%'";
                                }
                                $total_sql = "SELECT COUNT(nim) AS total FROM praktikan" . $where_clause;
                                $total_result = $conn->query($total_sql);
                                $total_row = $total_result->fetch_assoc();
                                $total_records = $total_row['total'];
                                $total_pages = ceil($total_records / $limit_per_page);
                                $sql = "SELECT * FROM praktikan" . $where_clause . " ORDER BY nim ASC LIMIT $limit_per_page OFFSET $offset";
                                $result = $conn->query($sql);
                                if ($result && $result->num_rows > 0) {
                                    $no = $offset + 1;
                                    while($row = $result->fetch_assoc()) {
                                        $tgl = $row['tgl_lahir'];
                                        $tgl_display = ($tgl && $tgl !== '0000-00-00' && $tgl !== '1970-01-01') ? date('d-m-Y', strtotime($tgl)) : '-';
                                        echo "<tr>
                                            <td>" . $no . "</td>
                                            <td>" . htmlspecialchars($row['nim']) . "</td>
                                            <td>" . htmlspecialchars($row['nama_lengkap']) . "</td>
                                            <td>" . htmlspecialchars($row['alamat']) . "</td>
                                            <td>" . $tgl_display . "</td>
                                            <td>" . htmlspecialchars($row['prodi']) . "</td>
                                            <td><input type='checkbox' class='row-checkbox'></td>
                                        </tr>";
                                        $no++;
                                    }
                                } else {
                                    echo "<tr><td colspan='7' style='text-align: center;'>Tidak ada data praktikan</td></tr>";
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
                <!-- Form Tambah Multi Praktikan -->
                <div class="praktikan-add-section" id="praktikan-add-section" style="display:none;">
                    <div class="praktikan-add-header">
                        <span class="add-icon">➕</span> <span class="add-title">Tambah Multi Praktikan</span>
                    </div>
                    <form id="form-multi-praktikan" method="post" action="add_praktikan.php" autocomplete="off">
                        <table class="praktikan-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Nama Lengkap</th>
                                    <th>Alamat</th>
                                    <th>Tgl Lahir</th>
                                    <th>Prodi</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="add-praktikan-tbody">
                                <tr>
                                    <td>1</td>
                                    <td><input type="text" name="nim[]" placeholder="NIM" required></td>
                                    <td><input type="text" name="nama[]" placeholder="Nama Lengkap" required></td>
                                    <td><input type="text" name="alamat[]" placeholder="Alamat" required></td>
                                    <td><input type="text" name="tgl_lahir[]" placeholder="hh/bb/tttt" required></td>
                                    <td>
                                        <select name="prodi[]" required>
                                            <option value="">- Prodi -</option>
                                            <option value="Teknik Informatika">Teknik Informatika</option>
                                            <option value="Sistem Informasi">Sistem Informasi</option>
                                        </select>
                                    </td>
                                    <td><button type="button" class="btn-del-row" <?php if($role==='kepala') echo 'disabled style="opacity:0.6;pointer-events:none;"'; ?>>✖</button></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="praktikan-add-footer">
                            <button type="button" class="btn-purple" id="add-row" <?php if($role==='kepala') echo 'disabled style="opacity:0.6;pointer-events:none;"'; ?>>+ Baris Baru</button>
                            <div class="footer-right">
                                <button type="reset" class="btn-reset">Reset</button>
                                <button type="submit" class="btn-green">Simpan</button>
                                <button type="button" class="btn-back" id="back-to-table">Back</button>
                            </div>
                        </div>
                    </form>
                </div>
                <script>
                // Show/Hide Add Praktikan Form
                document.getElementById('show-add-form').onclick = function() {
                    document.getElementById('praktikan-table-section').style.display = 'none';
                    document.getElementById('praktikan-add-section').style.display = 'block';
                };
                document.getElementById('back-to-table').onclick = function() {
                    document.getElementById('praktikan-add-section').style.display = 'none';
                    document.getElementById('praktikan-table-section').style.display = 'block';
                };
                // Add Row
                document.getElementById('add-row').onclick = function() {
                    const tbody = document.getElementById('add-praktikan-tbody');
                    const rowCount = tbody.rows.length + 1;
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td>${rowCount}</td>
                        <td><input type="text" name="nim[]" placeholder="NIM" required></td>
                        <td><input type="text" name="nama[]" placeholder="Nama Lengkap" required></td>
                        <td><input type="text" name="alamat[]" placeholder="Alamat" required></td>
                        <td><input type="text" name="tgl_lahir[]" placeholder="hh/bb/tttt" required></td>
                        <td><select name="prodi[]" required><option value="">- Prodi -</option><option value="Teknik Informatika">Teknik Informatika</option><option value="Sistem Informasi">Sistem Informasi</option></select></td>
                        <td><button type="button" class="btn-del-row" <?php if($role==='kepala') echo 'disabled style="opacity:0.6;pointer-events:none;"'; ?>>✖</button></td>`;
                    tbody.appendChild(tr);
                    updateRowNumbers();
                };
                // Delete Row
                document.getElementById('add-praktikan-tbody').onclick = function(e) {
                    if (e.target.classList.contains('btn-del-row')) {
                        const row = e.target.closest('tr');
                        row.parentNode.removeChild(row);
                        updateRowNumbers();
                    }
                };
                function updateRowNumbers() {
                    const rows = document.querySelectorAll('#add-praktikan-tbody tr');
                    rows.forEach((tr, i) => tr.children[0].textContent = i + 1);
                }
                // Show Entries & Search Otomatis
                const entriesSelect = document.getElementById('entriesSelect');
                const manualEntriesInput = document.getElementById('manualEntriesInput');
                const searchInput = document.getElementById('searchInput');
                // Set value select dan input manual sesuai GET
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
                        // Jika kosong, kembali ke default 10
                        entriesSelect.value = '10';
                        manualEntriesInput.style.display = 'none';
                        manualEntriesInput.value = '';
                        // Hapus manual_entries dari URL dan submit dengan entries=10
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
                // Button Edit
                const btnEdit = document.querySelector('.btn-orange');
                if (btnEdit) {
                    btnEdit.addEventListener('click', function() {
                        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                        if (checkedBoxes.length === 0) {
                            alert('Silakan pilih praktikan yang akan diedit terlebih dahulu!');
                            return;
                        }
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = 'edit_praktikan.php';
                        checkedBoxes.forEach(checkbox => {
                            const row = checkbox.closest('tr');
                            const nim = row.cells[1].textContent;
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'selected_ids[]';
                            input.value = nim;
                            form.appendChild(input);
                        });
                        document.body.appendChild(form);
                        form.submit();
                    });
                }
                // Button Hapus
                const btnHapus = document.querySelector('.btn-red');
                if (btnHapus) {
                    btnHapus.addEventListener('click', function() {
                        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                        if (checkedBoxes.length === 0) {
                            alert('Silakan pilih praktikan yang akan dihapus terlebih dahulu!');
                            return;
                        }
                        if (!confirm('Apakah Anda yakin ingin menghapus data yang dipilih?')) {
                            return;
                        }
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = 'delete_praktikan.php';
                        checkedBoxes.forEach(checkbox => {
                            const row = checkbox.closest('tr');
                            const nim = row.cells[1].textContent;
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'selected_ids[]';
                            input.value = nim;
                            form.appendChild(input);
                        });
                        document.body.appendChild(form);
                        form.submit();
                    });
                }
                </script>
            </div>
        </div>
    </div>
    <!-- AREA CETAK KHUSUS PRINT -->
    <div id="print-area" class="print-area">
        <div style="display: flex; align-items: center; margin-bottom: 8px;">
            <img src="unibba-logo.png" alt="Logo Unibba" style="height: 60px; margin-right: 16px;">
            <div style="flex:1; text-align: center;">
                <div style="font-size: 1.2em; font-weight: bold;">DAFTAR PRAKTIKAN</div>
                <div style="font-size: 1.1em;">Fakultas Teknologi Informasi Universitas Bale Bandung</div>
            </div>
        </div>
        <table border="1" cellspacing="0" cellpadding="6" style="width:100%; border-collapse:collapse; font-size:0.95em;">
            <thead>
                <tr style="background:#f0f0f0;">
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama Lengkap</th>
                    <th>Alamat</th>
                    <th>Tgl Lahir</th>
                    <th>Prodi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'db_connect.php';
                $search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
                $where_clause = '';
                if (!empty($search_query)) {
                    $where_clause = " WHERE nim LIKE '%$search_query%' OR nama_lengkap LIKE '%$search_query%' OR alamat LIKE '%$search_query%' OR prodi LIKE '%$search_query%'";
                }
                $sql_print = "SELECT * FROM praktikan" . $where_clause . " ORDER BY nim ASC";
                $result_print = $conn->query($sql_print);
                if ($result_print && $result_print->num_rows > 0) {
                    $no = 1;
                    while($row = $result_print->fetch_assoc()) {
                        $tgl = $row['tgl_lahir'];
                        $tgl_display = ($tgl && $tgl !== '0000-00-00' && $tgl !== '1970-01-01') ? date('d-m-Y', strtotime($tgl)) : '-';
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['nim']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama_lengkap']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['alamat']) . "</td>";
                        echo "<td>" . $tgl_display . "</td>";
                        echo "<td>" . htmlspecialchars($row['prodi']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center;'>Tidak ada data praktikan</td></tr>";
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
    // ... existing code ...
    // Pastikan tombol Cetak memanggil window.print()
    document.querySelector('.btn-purple').onclick = function() { window.print(); };
    // ... existing code ...
    </script>
</body>
</html> 