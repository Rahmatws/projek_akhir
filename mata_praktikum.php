<?php
include 'db_connect.php';
// --- Pagination Logic --- 
$limit_per_page = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;
if (isset($_GET['entries']) && $_GET['entries'] === 'manual_input' && isset($_GET['manual_entries']) && intval($_GET['manual_entries']) > 0) {
    $limit_per_page = intval($_GET['manual_entries']);
}
if ($limit_per_page <= 0) $limit_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $limit_per_page;
// --- Search Logic --- 
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$where_clause = '';
if (!empty($search_query)) {
    $where_clause = " WHERE kode_matkul LIKE '%$search_query%' OR nama_matkul LIKE '%$search_query%' OR sks LIKE '%$search_query%' OR semester LIKE '%$search_query%'";
}
// Query total records
$total_records_sql = "SELECT COUNT(id) AS total FROM mata_praktikum" . $where_clause;
$total_records_result = $conn->query($total_records_sql);
$total_records_row = $total_records_result->fetch_assoc();
$total_records = $total_records_row['total'];
// Hitung total halaman
$total_pages = ceil($total_records / $limit_per_page);
if ($total_pages == 0) {
    $current_page = 1;
    $offset = 0;
} elseif ($current_page > $total_pages) {
    $current_page = $total_pages;
    $offset = ($current_page - 1) * $limit_per_page;
} elseif ($current_page < 1) {
    $current_page = 1;
    $offset = 0;
}
// Query data
$sql = "SELECT * FROM mata_praktikum" . $where_clause . " ORDER BY id ASC LIMIT $limit_per_page OFFSET $offset";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mata Praktikum</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="mata_praktikum.css">
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
                <li><a href="mata_praktikum.php" class="active"><i class="icon">📚</i> Mata Praktikum</a></li>
                <li><a href="asisten_praktikum.php"><i class="icon">🧑‍🏫</i> Asisten Praktikum</a></li>
                <li><a href="ruang_laboratorium.html"><i class="icon">🔬</i> Ruang Laboratorium</a></li>
                <li><a href="laboran.php"><i class="icon">📄</i> Laboran</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="top-bar">
                <div class="title-breadcrumb">
                    <h2>Daftar Mata Praktikum</h2>
                    <span class="breadcrumb" id="main-breadcrumb-text">Data Master Mata Kuliah, Menampilkan data Mata Praktikum Laboratorium FTI UNIBBA</span>
                </div>
                <div class="user-info">
                    <span class="user-name">Uchiha Atep</span>
                    <img src="user.png" alt="User" class="user-avatar">
                </div>
            </div>

            <div class="mata-praktikum-list-container" id="mata-praktikum-list-container">
                <div class="mata-praktikum-list-header">
                    <h2><span class="header-icon">📚</span> Daftar Mata Praktikum</h2>
                </div>
                <div class="mata-praktikum-actions">
                    <button class="add-mata-praktikum-button" id="show-add-form-button">+ Tambah Mata Praktikum</button>
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
                        <input type="text" id="search" value="<?php echo htmlspecialchars($search_query); ?>">
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th><span class="sort-icon">⇅</span> Kode Matkum</th>
                                <th><span class="sort-icon">⇅</span> Nama Matkum</th>
                                <th><span class="sort-icon">⇅</span> SKS</th>
                                <th><span class="sort-icon">⇅</span> Semester</th>
                                <th>Pilihan</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            $no = $offset + 1;
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>" . $no . "</td>
                                    <td>" . htmlspecialchars($row['kode_matkul']) . "</td>
                                    <td>" . htmlspecialchars($row['nama_matkul']) . "</td>
                                    <td>" . htmlspecialchars($row['sks']) . "</td>
                                    <td>" . htmlspecialchars($row['semester']) . "</td>
                                    <td>
                                        <button class='action-button edit-button'>📝</button>
                                        <button class='action-button delete-button'>🗑️</button>
                                    </td>
                                </tr>";
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align: center;'>Tidak ada data mata praktikum</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                    <div class="table-footer">
                        <span>Showing <?php echo ($total_records > 0) ? ($offset + 1) : 0; ?> to <?php echo min($offset + $limit_per_page, $total_records); ?> of <?php echo $total_records; ?> entries</span>
                        <div class="pagination">
                            <button class="prev-button" <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>>Previous</button>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <button class="page-button <?php echo ($i == $current_page) ? 'active' : ''; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></button>
                            <?php endfor; ?>
                            <button class="next-button" <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>>Next</button>
                        </div>
                    </div>
                    <p class="table-info-text"></p>
                </div>
            </div>

            <!-- Form Tambah Mata Praktikum (Hidden by default) -->
            <div class="mata-praktikum-add-form-container" id="mata-praktikum-add-form-container" style="display: none;">
                <div class="form-header">
                    <h2><span class="header-icon">+</span> Tambah Mata Praktikum</h2>
                </div>
                <div class="form-content">
                    <form action="add_mata_praktikum.php" method="POST">
                        <div class="form-group">
                            <label for="kode_matkul">Kode Mata Praktikum</label>
                            <input type="text" id="kode_matkul" name="kode_matkul" placeholder="Kode Mata Praktikum">
                        </div>
                        <div class="form-group">
                            <label for="nama_matkul">Nama Mata Kuliah</label>
                            <input type="text" id="nama_matkul" name="nama_matkul" placeholder="Nama Mata Kuliah">
                        </div>
                        <div class="form-group">
                            <label for="sks">SKS</label>
                            <input type="number" id="sks" name="sks" placeholder="SKS">
                        </div>
                        <div class="form-group">
                            <label for="semester">Semester</label>
                            <input type="number" id="semester" name="semester" placeholder="Semester">
                        </div>
                        <div class="form-group">
                            <div class="form-buttons-wrapper">
                                <button type="reset" class="btn btn-reset">Reset</button>
                                <button type="submit" class="btn btn-simpan">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="form-back-button-container">
                    <button class="btn btn-back" id="hide-add-form-button">← Back</button>
                </div>
                <div class="form-footer-text">
                    Menambah Data Mata Praktikum, isi form diatas untuk menambahkan data Mata Praktikum.
                </div>
            </div>

            <!-- Form Edit Mata Praktikum (Hidden by default) -->
            <div class="mata-praktikum-edit-form-container" id="mata-praktikum-edit-form-container" style="display: none;">
                <div class="form-header">
                    <h2><span class="header-icon">📝</span> Edit Mata Praktikum</h2>
                </div>
                <div class="form-content">
                    <form id="edit-matkum-form" action="update_mata_praktikum.php" method="POST">
                        <input type="hidden" id="kode_lama" name="kode_lama" value="">
                        <div class="form-group">
                            <label for="edit_kode_matkul">Kode Mata Praktikum</label>
                            <input type="text" id="edit_kode_matkul" name="edit_kode_matkul" placeholder="Kode Mata Praktikum" value="">
                        </div>
                        <div class="form-group">
                            <label for="edit_nama_matkul">Nama Mata Praktikum</label>
                            <input type="text" id="edit_nama_matkul" name="edit_nama_matkul" placeholder="Nama Mata Praktikum" value="">
                        </div>
                        <div class="form-group">
                            <label for="edit_sks">SKS</label>
                            <input type="number" id="edit_sks" name="edit_sks" placeholder="SKS" value="">
                        </div>
                        <div class="form-group">
                            <label for="edit_semester">Semester</label>
                            <input type="number" id="edit_semester" name="edit_semester" placeholder="Semester" value="">
                        </div>
                        <div class="form-group">
                            <div class="form-buttons-wrapper">
                                <button type="reset" class="btn btn-reset">Reset</button>
                                <button type="submit" class="btn btn-simpan">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="form-back-button-container">
                    <button class="btn btn-back" id="hide-edit-form-button">← Back</button>
                </div>
                <div class="form-footer-text">
                    Update Data Mata Praktikum, edit form diatas untuk mengubah data Mata Praktikum.
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Show/Hide Add Form ---
            const showAddFormButton = document.getElementById('show-add-form-button');
            const hideAddFormButton = document.getElementById('hide-add-form-button');
            const mataPraktikumListContainer = document.getElementById('mata-praktikum-list-container');
            const mataPraktikumAddFormContainer = document.getElementById('mata-praktikum-add-form-container');
            const mataPraktikumEditFormContainer = document.getElementById('mata-praktikum-edit-form-container');
            const mainBreadcrumbText = document.getElementById('main-breadcrumb-text');

            function updateBreadcrumbText() {
                if (mainBreadcrumbText) {
                    if (mataPraktikumListContainer.style.display !== 'none') {
                        mainBreadcrumbText.textContent = 'Data Master Mata Kuliah, Menampilkan data Mata Praktikum Laboratorium FTI UNIBBA';
                    } else if (mataPraktikumAddFormContainer.style.display !== 'none') {
                        mainBreadcrumbText.textContent = 'Form untuk menambahkan data Mata Praktikum';
                    } else if (mataPraktikumEditFormContainer.style.display !== 'none') {
                        mainBreadcrumbText.textContent = 'Form untuk melakukan edit data Mata Praktikum';
                    }
                }
            }
            if (showAddFormButton) {
                showAddFormButton.addEventListener('click', function() {
                    mataPraktikumListContainer.style.display = 'none';
                    mataPraktikumEditFormContainer.style.display = 'none';
                    mataPraktikumAddFormContainer.style.display = 'block';
                    updateBreadcrumbText();
                });
            }
            if (hideAddFormButton) {
                hideAddFormButton.addEventListener('click', function() {
                    mataPraktikumAddFormContainer.style.display = 'none';
                    mataPraktikumListContainer.style.display = 'block';
                    updateBreadcrumbText();
                });
            }

            // --- Event Delegation for Edit & Delete ---
            const tableBody = document.querySelector('.data-table tbody');
            // Edit form fields
            const editKode = document.getElementById('edit_kode_matkul');
            const editNama = document.getElementById('edit_nama_matkul');
            const editSks = document.getElementById('edit_sks');
            const editSemester = document.getElementById('edit_semester');
            const kodeLamaInput = document.getElementById('kode_lama');
            let kodeLama = '';
            if (tableBody) {
                tableBody.addEventListener('click', function(e) {
                    const target = e.target;
                    if (target.classList.contains('edit-button')) {
                        const row = target.closest('tr');
                        const kode = row.children[1].textContent.trim();
                        const nama = row.children[2].textContent.trim();
                        const sks = row.children[3].textContent.trim();
                        const semester = row.children[4].textContent.trim();
                        editKode.value = kode;
                        editNama.value = nama;
                        editSks.value = sks;
                        editSemester.value = semester;
                        kodeLama = kode;
                        kodeLamaInput.value = kode;
                        mataPraktikumListContainer.style.display = 'none';
                        mataPraktikumAddFormContainer.style.display = 'none';
                        mataPraktikumEditFormContainer.style.display = 'block';
                        updateBreadcrumbText();
                    } else if (target.classList.contains('delete-button')) {
                        const row = target.closest('tr');
                        const kode = row.children[1].textContent.trim();
                        if (confirm('Yakin ingin menghapus data ini?')) {
                            fetch('delete_mata_praktikum.php', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                body: 'kode_matkul=' + encodeURIComponent(kode)
                            }).then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.location.href = 'mata_praktikum.php?message=success';
                                } else {
                                    alert('Gagal menghapus data!');
                                }
                            }).catch(() => alert('Gagal menghapus data!'));
                        }
                    }
                });
            }
            // Edit form submit
            const editForm = mataPraktikumEditFormContainer.querySelector('form');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(editForm);
                    formData.append('kode_lama', kodeLama);
                    fetch('update_mata_praktikum.php', {
                        method: 'POST',
                        body: formData
                    }).then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = 'mata_praktikum.php?message=success';
                        } else {
                            alert('Gagal update data!');
                        }
                    }).catch(() => alert('Gagal update data!'));
                });
            }

            const hideEditFormButton = document.getElementById('hide-edit-form-button');
            if (hideEditFormButton) {
                hideEditFormButton.addEventListener('click', function() {
                    mataPraktikumEditFormContainer.style.display = 'none';
                    mataPraktikumListContainer.style.display = 'block';
                    updateBreadcrumbText();
                });
            }

            // --- Pagination, Search, Show Entries Logic ---
            const entriesSelect = document.getElementById('entries');
            const manualEntriesInput = document.getElementById('manual_entries_input');
            const searchInput = document.getElementById('search');
            const paginationContainer = document.querySelector('.pagination');
            function updateUrlParameter(param, value) {
                const url = new URL(window.location.href);
                url.searchParams.set(param, value);
                if (param === 'entries' || param === 'search') {
                    url.searchParams.set('page', 1);
                }
                window.location.href = url.toString();
            }
            function toggleManualEntriesInput() {
                if (entriesSelect.value === 'manual_input') {
                    manualEntriesInput.style.display = 'inline-block';
                    if (manualEntriesInput.value === '') {
                        manualEntriesInput.focus();
                    }
                } else {
                    manualEntriesInput.style.display = 'none';
                }
            }
            toggleManualEntriesInput();
            entriesSelect.addEventListener('change', function() {
                toggleManualEntriesInput();
                if (this.value !== 'manual_input') {
                    updateUrlParameter('entries', this.value);
                }
            });
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
            if (searchInput) {
                let searchTimeout;
                searchInput.value = '<?php echo htmlspecialchars($search_query); ?>';
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
            if (paginationContainer) {
                paginationContainer.addEventListener('click', function(event) {
                    if (event.target.classList.contains('prev-button') && !event.target.disabled) {
                        updateUrlParameter('page', <?php echo $current_page - 1; ?>);
                    } else if (event.target.classList.contains('next-button') && !event.target.disabled) {
                        updateUrlParameter('page', <?php echo $current_page + 1; ?>);
                    } else if (event.target.classList.contains('page-button')) {
                        const pageNum = event.target.dataset.page;
                        updateUrlParameter('page', pageNum);
                    }
                });
            }
        });
    </script>

    <?php if (isset($_GET['message'])): ?>
        <div style="margin: 16px 40px 0 40px;">
            <?php if ($_GET['message'] === 'success'): ?>
                <div style="background:#d4edda;color:#155724;padding:12px 20px;border-radius:6px;">Data berhasil ditambahkan!</div>
            <?php elseif ($_GET['message'] === 'error'): ?>
                <div style="background:#f8d7da;color:#721c24;padding:12px 20px;border-radius:6px;">Gagal menambahkan data. Cek kembali inputan Anda.</div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</body>
</html> 