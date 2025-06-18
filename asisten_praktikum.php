<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Asisten Praktikum</title>
    <link rel="stylesheet" href="asisten_praktikum.css">
    <link rel="stylesheet" href="dashboard.css"> <!-- Untuk sidebar dan general styling -->
    <meta http-equiv="Expires" content="0">
</head>
<body>
    <?php
    require_once 'db_connect.php'; // Sertakan file koneksi database

    // --- Pagination Logic --- 
    $limit_per_page = isset($_GET['entries']) ? (int)$_GET['entries'] : 10; // Jumlah entri per halaman
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Halaman saat ini
    $offset = ($current_page - 1) * $limit_per_page; // Offset untuk query SQL

    // --- Search Logic --- 
    $search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $where_clause = '';
    if (!empty($search_query)) {
        $where_clause = " WHERE nidn LIKE '%$search_query%' OR nama_asisten LIKE '%$search_query%' OR nama_prodi LIKE '%$search_query%'";
    }

    // Query untuk mendapatkan total records (dengan atau tanpa pencarian)
    $total_records_sql = "SELECT COUNT(id) AS total FROM asisten_praktikum" . $where_clause;
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

    // Query untuk mendapatkan data asisten praktikum (dengan pencarian dan pagination)
    $sql = "SELECT id, nidn, nama_asisten, nama_prodi FROM asisten_praktikum" . $where_clause . " ORDER BY id ASC LIMIT $limit_per_page OFFSET $offset";
    $result = $conn->query($sql);
    ?>
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
                <li><a href="absensi_kehadiran.html"><i class="icon">✅</i> Absensi Kehadiran</a></li>
                <li><a href="mata_praktikum.html"><i class="icon">📚</i> Mata Praktikum</a></li>
                <li><a href="asisten_praktikum.php" class="active"><i class="icon">🧑‍🏫</i> Asisten Praktikum</a></li>
                <li><a href="ruang_laboratorium.html"><i class="icon">🔬</i> Ruang Laboratorium</a></li>
                <li><a href="laboran.php"><i class="icon">📄</i> Laboran</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="top-bar">
                <div class="title-breadcrumb">
                    <h2>Daftar Asisten Praktikum</h2>
                    <span class="breadcrumb" id="main-breadcrumb-text">Data Master Asisten Praktikum, Menampilkan data Asisten Praktikum</span>
                </div>
                <div class="user-info">
                    <span class="user-name">Uchiha Atep</span>
                    <img src="user.png" alt="User" class="user-avatar">
                </div>
            </div>

            <div class="asisten-list-container" id="asisten-list-container">
                <div class="asisten-list-header">
                    <h2><span class="header-icon">🧑‍🏫</span> Daftar Asisten Praktikum</h2>
                </div>
                <div class="asisten-actions">
                    <button class="add-asisten-button" id="show-add-form-button">+ Tambah Asisten Praktikum</button>
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
                                <th><span class="sort-icon">⬆️</span> NIDN</th>
                                <th><span class="sort-icon">⬆️</span> Nama</th>
                                <th><span class="sort-icon">⬆️</span> Prodi</th>
                                <th>Pilihan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                $no = $offset + 1; // Sesuaikan nomor urut dengan offset
                                // Output data setiap baris
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $no++ . "</td>";
                                    echo "<td>" . htmlspecialchars($row["nidn"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["nama_asisten"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["nama_prodi"]) . "</td>";
                                    echo "<td>";
                                    echo "<button class=\"action-button view-button\">📊</button>";
                                    echo "<button class=\"action-button edit-button\" data-id=\"" . htmlspecialchars($row["id"]) . "\">📝</button>";
                                    echo "<button class=\"action-button delete-button\" data-id=\"" . htmlspecialchars($row["id"]) . "\">🗑️</button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan=\"5\">Tidak ada data asisten praktikum yang ditemukan.</td></tr>";
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

            <!-- Form Tambah Asisten Praktikum (Hidden by default) -->
            <div class="asisten-add-form-container" id="asisten-add-form-container" style="display: none;">
                <div class="form-header">
                    <h2><span class="header-icon">+</span> Tambah Asisten Praktikum</h2>
                </div>
                <div class="form-content">
                    <form action="add_asisten_praktikum.php" method="POST">
                        <div class="form-group">
                            <label for="nidn">NIDN</label>
                            <input type="text" id="nidn" name="nidn" placeholder="NIDN" required>
                        </div>
                        <div class="form-group">
                            <label for="nama_asisten">Nama Asisten Praktikum</label>
                            <input type="text" id="nama_asisten" name="nama_asisten" placeholder="Nama Asisten Praktikum" required>
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <input type="text" id="alamat" name="alamat" placeholder="Alamat Asisten Praktikum">
                        </div>
                        <div class="form-group">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            <div class="date-input-container">
                                <input type="text" id="tanggal_lahir" name="tanggal_lahir" placeholder="dd/mm/yyyy">
                                <span class="calendar-icon">🗓️</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nama_prodi">Nama Prodi</label>
                            <select id="nama_prodi" name="nama_prodi" required>
                                <option value="">- Pilih Prodi -</option>
                                <option value="Teknik Informatika">Teknik Informatika</option>
                                <option value="Sistem Informasi">Sistem Informasi</option>
                                <!-- Add more options as needed -->
                            </select>
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
                    Menambah Data Asisten Praktikum, isi form diatas untuk menambahkan data Asisten Praktikum.
                </div>
            </div>

            <!-- Form Edit Asisten Praktikum (Hidden by default) -->
            <div class="asisten-edit-form-container" id="asisten-edit-form-container" style="display: none;">
                <div class="form-header">
                    <h2><span class="header-icon">📝</span> Edit Asisten Praktikum</h2>
                </div>
                <div class="form-content">
                    <form action="#" method="POST">
                        <div class="form-group">
                            <label for="edit_nidn">NIDN</label>
                            <input type="text" id="edit_nidn" name="edit_nidn" placeholder="NIDN" value="3013200231">
                        </div>
                        <div class="form-group">
                            <label for="edit_nama_asisten">Nama Asisten Praktikum</label>
                            <input type="text" id="edit_nama_asisten" name="edit_nama_asisten" placeholder="Nama Asisten Praktikum" value="Ahmad Faqjan M, S. Kom,">
                        </div>
                        <div class="form-group">
                            <label for="edit_alamat">Alamat</label>
                            <input type="text" id="edit_alamat" name="edit_alamat" placeholder="Alamat Asisten Praktikum" value="Paceti">
                        </div>
                        <div class="form-group">
                            <label for="edit_tanggal_lahir">Tanggal Lahir</label>
                            <div class="date-input-container">
                                <input type="text" id="edit_tanggal_lahir" name="edit_tanggal_lahir" placeholder="hh/bb/tttt" value="22/03/1994">
                                <span class="calendar-icon">🗓️</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit_nama_prodi">Nama Prodi</label>
                            <select id="edit_nama_prodi" name="edit_nama_prodi">
                                <option value="">- Prodi -</option>
                                <option value="Teknik Informatika" selected>Teknik Informatika</option>
                                <option value="Sistem Informasi">Sistem Informasi</option>
                                <!-- Add more options as needed -->
                            </select>
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
                    Update Data Asisten Praktikum, edit form diatas untuk mengubah data Asisten Praktikum.
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const showAddFormButton = document.getElementById('show-add-form-button');
            const hideAddFormButton = document.getElementById('hide-add-form-button');
            const asistenListContainer = document.getElementById('asisten-list-container');
            const asistenAddFormContainer = document.getElementById('asisten-add-form-container');
            const asistenEditFormContainer = document.getElementById('asisten-edit-form-container');

            const mainBreadcrumbText = document.getElementById('main-breadcrumb-text');

            // Edit buttons in the table
            const editButtons = document.querySelectorAll('.action-button.edit-button');
            const hideEditFormButton = document.getElementById('hide-edit-form-button');

            function setActiveTab(activeTab) {
                // Update main breadcrumb text based on which container is visible
                if (mainBreadcrumbText) {
                    if (asistenListContainer.style.display !== 'none') {
                        mainBreadcrumbText.textContent = 'Data Master Asisten Praktikum, Menampilkan data Asisten Praktikum';
                    } else if (asistenAddFormContainer.style.display !== 'none') {
                        mainBreadcrumbText.textContent = 'Form untuk menambahkan data Asisten Praktikum';
                    } else if (asistenEditFormContainer.style.display !== 'none') {
                        mainBreadcrumbText.textContent = 'Form untuk melakukan edit data Asisten Praktikum';
                    }
                }
            }

            showAddFormButton.addEventListener('click', function() {
                asistenListContainer.style.display = 'none';
                asistenEditFormContainer.style.display = 'none'; // Hide edit form
                asistenAddFormContainer.style.display = 'block';
                setActiveTab(null); // No active header tab when adding form
            });

            hideAddFormButton.addEventListener('click', function() {
                asistenAddFormContainer.style.display = 'none';
                asistenListContainer.style.display = 'block';
                setActiveTab(null); // No active header tab when showing list
            });

            // Handle Edit button clicks
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    asistenListContainer.style.display = 'none';
                    asistenAddFormContainer.style.display = 'none'; // Hide add form
                    asistenEditFormContainer.style.display = 'block';
                    setActiveTab(null); // No active header tab when editing form

                    // Optionally, populate the form fields here with data from the clicked row
                    const row = this.closest('tr');
                    const nidn = row.children[1].textContent;
                    const nama = row.children[2].textContent;
                    const prodi = row.children[3].textContent;

                    // For now, hardcode values as per image, but in a real app, you'd use the extracted data
                    document.getElementById('edit_nidn').value = nidn;
                    document.getElementById('edit_nama_asisten').value = nama;
                    // For address and tanggal_lahir, you'd need actual data or fetch from backend
                    document.getElementById('edit_alamat').value = ""; // Placeholder
                    document.getElementById('edit_tanggal_lahir').value = ""; // Placeholder
                    // Set selected option for prodi
                    const prodiSelect = document.getElementById('edit_nama_prodi');
                    for (let i = 0; i < prodiSelect.options.length; i++) {
                        if (prodiSelect.options[i].value === prodi) {
                            prodiSelect.selectedIndex = i;
                            break;
                        }
                    }
                });
            });

            // Handle Hide Edit Form button click
            if (hideEditFormButton) {
                hideEditFormButton.addEventListener('click', function() {
                    asistenEditFormContainer.style.display = 'none';
                    asistenListContainer.style.display = 'block';
                    setActiveTab(null); // No active header tab when showing list
                });
            }

            // Set initial active tab on page load
            // Initial call to set the correct state based on which container is visible
            setActiveTab(null); // Call setActiveTab initially to set correct breadcrumb text

            // --- Pagination and Search Logic --- Start
            const entriesSelect = document.getElementById('entries');
            const manualEntriesInput = document.getElementById('manual_entries_input');
            const searchInput = document.getElementById('search');
            const paginationContainer = document.querySelector('.pagination');
            
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
                if (entriesSelect.value === 'manual_input') {
                    manualEntriesInput.style.display = 'inline-block';
                    // Set focus to the manual input if it's new
                    if (manualEntriesInput.value === '') {
                        manualEntriesInput.focus();
                    }
                } else {
                    manualEntriesInput.style.display = 'none';
                }
            }

            // Initial check on page load
            toggleManualEntriesInput();

            // Event listener for 'Show entries' dropdown
            if (entriesSelect) {
                entriesSelect.addEventListener('change', function() {
                    toggleManualEntriesInput(); // Call toggle function first

                    let valueToSet = this.value;
                    if (valueToSet === 'manual_input') {
                        // If 'Lainnya' is selected, don't update URL yet, wait for manual input
                        return;
                    } else {
                        updateUrlParameter('entries', valueToSet);
                    }
                });
            }

            // New Event listener for manual entries input
            if (manualEntriesInput) {
                let manualEntriesTimeout;
                manualEntriesInput.addEventListener('input', function() {
                    clearTimeout(manualEntriesTimeout);
                    manualEntriesTimeout = setTimeout(() => {
                        const value = parseInt(this.value);
                        if (!isNaN(value) && value > 0) {
                            updateUrlParameter('entries', value);
                        } else if (this.value === '') {
                            // If input is cleared, revert to default entries (e.g., 10)
                            updateUrlParameter('entries', entriesSelect.options[0].value);
                        }
                    }, 500); // Debounce for 500ms
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

            // Event listener for 'Search' input with debounce for automatic search
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        updateUrlParameter('search', this.value);
                    }, 500); // Debounce for 500ms (adjust as needed)
                });

                // Also allow search on Enter key press without debounce delay
                searchInput.addEventListener('keypress', function(event) {
                    if (event.key === 'Enter') {
                        clearTimeout(searchTimeout); // Clear debounce if Enter is pressed
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
            // --- Pagination and Search Logic --- End

        });
    </script>
</body>
</html> 