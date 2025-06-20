<?php
    require_once 'db_connect.php'; // Sertakan file koneksi database

    // --- Pagination Logic --- 
    $limit_per_page = isset($_GET['entries']) ? (int)$_GET['entries'] : 10; // Jumlah entri per halaman
    if ($limit_per_page <= 0) {
        $limit_per_page = 10; // Pastikan limit_per_page tidak nol atau negatif
    }
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Halaman saat ini
    $offset = ($current_page - 1) * $limit_per_page; // Offset untuk query SQL

    // --- Search Logic --- 
    $search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $where_clause = '';
    if (!empty($search_query)) {
        $where_clause = " WHERE nama_kelas LIKE '%$search_query%' OR semester LIKE '%$search_query%'";
    }

    // Query untuk mendapatkan total records (dengan atau tanpa pencarian)
    $total_records_sql = "SELECT COUNT(id) AS total FROM kelas" . $where_clause;
    $total_records_result = $conn->query($total_records_sql);
    $total_records_row = $total_records_result->fetch_assoc();
    $total_records = $total_records_row['total'];

    // Hitung total halaman
    $total_pages = ceil($total_records / $limit_per_page);

    // Pastikan current_page tidak lebih dari total_pages atau kurang dari 1, dan handle total_pages = 0
    if ($total_pages == 0) {
        $current_page = 1;
        $offset = 0;
    } elseif ($current_page > $total_pages) {
        $current_page = $total_pages; // Set ke halaman terakhir jika melebihi total halaman
        $offset = ($current_page - 1) * $limit_per_page;
    } elseif ($current_page < 1) {
        $current_page = 1; // Set ke halaman pertama jika kurang dari 1
        $offset = 0;
    }

    // Query untuk mendapatkan data kelas (dengan pencarian dan pagination)
    $sql = "SELECT id, nama_kelas, semester FROM kelas" . $where_clause . " ORDER BY id ASC LIMIT $limit_per_page OFFSET $offset";
    $result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kelas Praktikum</title>
    <link rel="stylesheet" href="kelas.css">
    <link rel="stylesheet" href="dashboard.css"> <!-- Untuk sidebar dan general styling -->
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
                <li><a href="kelas.php" class="active"><i class="icon">🏫</i> Kelas</a></li>
                <li><a href="praktikan.php"><i class="icon">✍️</i> Praktikan</a></li>
                <li><a href="absensi_kehadiran.html"><i class="icon">✅</i> Absensi Kehadiran</a></li>
                <li><a href="mata_praktikum.html"><i class="icon">📚</i> Mata Praktikum</a></li>
                <li><a href="asisten_praktikum.php"><i class="icon">🧑‍🏫</i> Asisten Praktikum</a></li>
                <li><a href="ruang_laboratorium.html"><i class="icon">🔬</i> Ruang Laboratorium</a></li>
                <li><a href="laboran.php"><i class="icon">📄</i> Laboran</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="top-bar">
                <div class="title-breadcrumb">
                    <h2>Daftar Kelas Praktikum</h2>
                    <span class="breadcrumb">Data Master Kelas, Menampilkan data Kelas Praktikum</span>
                </div>
                <div class="user-info">
                    <span class="user-name">Uchiha Atep</span>
                    <img src="user.png" alt="User" class="user-avatar">
                </div>
            </div>

            <div class="kelas-list-container" id="kelas-list-container">
                <div class="kelas-list-header">
                    <h2><span class="header-icon">🏫</span> Daftar Kelas Praktikum</h2>
                </div>
                <div class="kelas-actions">
                    <button class="add-kelas-button" id="show-add-form-button">+ Tambah Kelas</button>
                </div>

                <div class="data-table">
                    <div class="entries-selector">
                        <label for="entries">Show</label>
                        <select name="entries" id="entries">
                            <option value="10" <?php echo ($limit_per_page == 10) ? 'selected' : ''; ?>>10</option>
                            <option value="25" <?php echo ($limit_per_page == 25) ? 'selected' : ''; ?>>25</option>
                            <option value="50" <?php echo ($limit_per_page == 50) ? 'selected' : ''; ?>>50</option>
                            <option value="100" <?php echo ($limit_per_page == 100) ? 'selected' : ''; ?>>100</option>
                            <option value="manual_input" <?php echo (!in_array($limit_per_page, [10, 25, 50, 100]) && $limit_per_page > 0) ? 'selected' : ''; ?>>Lainnya</option>
                        </select> entries
                        <input type="number" id="manual_entries_input" name="manual_entries" style="width: 60px; padding: 5px; border: 1px solid #ccc; border-radius: 4px; margin-left: 5px; display: none;" min="1" value="<?php echo (!in_array($limit_per_page, [10, 25, 50, 100]) && $limit_per_page > 0) ? $limit_per_page : ''; ?>">
                        <div class="search-box">
                            <label for="search">Search:</label>
                            <input type="text" id="search" value="<?php echo htmlspecialchars($search_query); ?>">
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th><span class="sort-icon">⬆️</span> Nama Kelas</th>
                                <th><span class="sort-icon">⬆️</span> Semester</th>
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
                                    echo "<td>" . htmlspecialchars($row["nama_kelas"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["semester"]) . "</td>";
                                    echo "<td>";
                                    echo "<div class=\"action-buttons-wrapper\">";
                                    echo "<a href='detail_kelas.php?id=" . $row["id"] . "' class='action-button view-button' title='Detail Kelas'>📊</a>";
                                    echo "<button class=\"action-button edit-button\" data-id=\"" . htmlspecialchars($row["id"]) . "\">📝</button>";
                                    echo "<button class=\"action-button delete-button\" data-id=\"" . htmlspecialchars($row["id"]) . "\">🗑️</button>";
                                    echo "</div>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan=\"4\">Tidak ada data kelas yang ditemukan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="table-footer">
                        <span>Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit_per_page, $total_records); ?> of <?php echo $total_records; ?> entries</span>
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

            <div class="add-kelas-form-container" id="add-kelas-form-container" style="display: none;">
                <div class="add-kelas-form-header">
                    <h2><span class="header-icon">➕</span> Tambah Kelas</h2>
                </div>
                <div class="add-kelas-form-content">
                    <form action="add_kelas.php" method="POST">
                        <div class="form-group">
                            <label for="namaKelas">Nama Kelas</label>
                            <input type="text" id="namaKelas" name="nama_kelas" placeholder="Nama Kelas" required>
                        </div>
                        <div class="form-group">
                            <label for="semester">Semester</label>
                            <input type="text" id="semester" name="semester" placeholder="Semester" required>
                        </div>
                        <div class="button-group">
                            <button type="reset" class="reset-button">♻️ Reset</button>
                            <button type="submit" class="save-button">💾 Simpan</button>
                        </div>
                    </form>
                </div>
                <div class="form-back-button-container">
                    <button class="back-button" id="back-to-list-button">← Back</button>
                </div>
            </div>

            <div class="edit-kelas-form-container" id="edit-kelas-form-container" style="display: none;">
                <div class="edit-kelas-form-header">
                    <h2><span class="header-icon">📝</span> Edit Kelas</h2>
                </div>
                <div class="edit-kelas-form-content">
                    <form action="update_kelas.php" method="POST">
                        <input type="hidden" id="editKelasId" name="id">
                        <div class="form-group">
                            <label for="editNamaKelas">Nama Kelas</label>
                            <input type="text" id="editNamaKelas" name="nama_kelas" placeholder="Nama Kelas" required>
                        </div>
                        <div class="form-group">
                            <label for="editSemester">Semester</label>
                            <input type="text" id="editSemester" name="semester" placeholder="Semester" required>
                        </div>
                        <div class="button-group">
                            <button type="reset" class="reset-button">♻️ Reset</button>
                            <button type="submit" class="update-button">✅ Update</button>
                        </div>
                    </form>
                </div>
                <div class="form-back-button-container">
                    <button class="back-button" id="back-from-edit-button">← Back</button>
                </div>
            </div>

        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const showAddFormBtn = document.getElementById('show-add-form-button');
            const backToListBtn = document.getElementById('back-to-list-button');
            const kelasListContainer = document.getElementById('kelas-list-container');
            const addKelasFormContainer = document.getElementById('add-kelas-form-container');
            const editKelasFormContainer = document.getElementById('edit-kelas-form-container');
            const headerTitle = document.querySelector('.top-bar h2');
            const breadcrumb = document.querySelector('.top-bar .breadcrumb');
            const searchInput = document.getElementById('search');
            const entriesSelect = document.getElementById('entries');
            const manualEntriesInput = document.getElementById('manual_entries_input');

            // Function to update URL and reload page for pagination/search
            function updateUrlAndReload() {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('entries', entriesSelect.value === 'manual_input' ? manualEntriesInput.value : entriesSelect.value);
                currentUrl.searchParams.set('search', searchInput.value);
                currentUrl.searchParams.set('page', 1); // Reset to first page on search or entries change
                window.location.href = currentUrl.toString();
            }

            // Event listeners for pagination and search
            entriesSelect.addEventListener('change', function() {
                if (this.value === 'manual_input') {
                    manualEntriesInput.style.display = 'inline-block';
                    manualEntriesInput.focus();
                } else {
                    manualEntriesInput.style.display = 'none';
                    updateUrlAndReload();
                }
            });

            manualEntriesInput.addEventListener('keyup', updateUrlAndReload);
            searchInput.addEventListener('keyup', function(event) {
                updateUrlAndReload();
            });

            document.querySelectorAll('.pagination .page-button').forEach(button => {
                button.addEventListener('click', function() {
                    const page = this.dataset.page;
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('page', page);
                    currentUrl.searchParams.set('entries', entriesSelect.value === 'manual_input' ? manualEntriesInput.value : entriesSelect.value);
                    currentUrl.searchParams.set('search', searchInput.value);
                    window.location.href = currentUrl.toString();
                });
            });

            document.querySelector('.pagination .prev-button').addEventListener('click', function() {
                const prevPage = <?php echo $current_page - 1; ?>;
                if (prevPage >= 1) {
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('page', prevPage);
                    currentUrl.searchParams.set('entries', entriesSelect.value === 'manual_input' ? manualEntriesInput.value : entriesSelect.value);
                    currentUrl.searchParams.set('search', searchInput.value);
                    window.location.href = currentUrl.toString();
                }
            });

            document.querySelector('.pagination .next-button').addEventListener('click', function() {
                const nextPage = <?php echo $current_page + 1; ?>;
                if (nextPage <= <?php echo $total_pages; ?>) {
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('page', nextPage);
                    currentUrl.searchParams.set('entries', entriesSelect.value === 'manual_input' ? manualEntriesInput.value : entriesSelect.value);
                    currentUrl.searchParams.set('search', searchInput.value);
                    window.location.href = currentUrl.toString();
                }
            });

            // Initial display for manual entries input
            if (entriesSelect.value === 'manual_input') {
                manualEntriesInput.style.display = 'inline-block';
            }

            showAddFormBtn.addEventListener('click', function() {
                kelasListContainer.style.display = 'none';
                addKelasFormContainer.style.display = 'block';
                editKelasFormContainer.style.display = 'none'; // Ensure edit form is hidden
                headerTitle.textContent = 'Tambah Kelas';
                breadcrumb.textContent = 'Data Master Kelas, Menambahkan Data Kelas Praktikum';
            });

            backToListBtn.addEventListener('click', function() {
                addKelasFormContainer.style.display = 'none';
                editKelasFormContainer.style.display = 'none';
                kelasListContainer.style.display = 'block';
                headerTitle.textContent = 'Daftar Kelas Praktikum';
                breadcrumb.textContent = 'Data Master Kelas, Menampilkan data Kelas Praktikum';
            });

            const editButtons = document.querySelectorAll('.action-button.edit-button');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    kelasListContainer.style.display = 'none';
                    addKelasFormContainer.style.display = 'none'; // Hide add form if open
                    editKelasFormContainer.style.display = 'block';

                    // Populate form with data from the clicked row
                    const row = this.closest('tr');
                    const kelasId = this.dataset.id;
                    const namaKelas = row.cells[1].textContent;
                    const semester = row.cells[2].textContent;

                    document.getElementById('editKelasId').value = kelasId;
                    document.getElementById('editNamaKelas').value = namaKelas;
                    document.getElementById('editSemester').value = semester;

                    headerTitle.textContent = 'Edit Kelas';
                    breadcrumb.textContent = 'Data Master Kelas, Mengubah Data Kelas Praktikum';
                });
            });

            const backFromEditBtn = document.getElementById('back-from-edit-button');
            backFromEditBtn.addEventListener('click', function() {
                editKelasFormContainer.style.display = 'none';
                kelasListContainer.style.display = 'block';
                headerTitle.textContent = 'Daftar Kelas Praktikum';
                breadcrumb.textContent = 'Data Master Kelas, Menampilkan data Kelas Praktikum';
            });

            // Add event listener for delete buttons
            document.querySelectorAll('.action-button.delete-button').forEach(button => {
                button.addEventListener('click', function() {
                    const kelasId = this.dataset.id;
                    if (confirm('Apakah Anda yakin ingin menghapus data kelas ini?')) {
                        window.location.href = 'delete_kelas.php?id=' + kelasId;
                    }
                });
            });

            // Handle status messages from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const message = urlParams.get('message');
            const tableInfoText = document.querySelector('.table-info-text');

            if (status && message) {
                tableInfoText.textContent = decodeURIComponent(message);
                tableInfoText.classList.add(status);
                // Clear URL parameters after displaying message
                setTimeout(() => {
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.delete('status');
                    currentUrl.searchParams.delete('message');
                    history.replaceState(null, '', currentUrl.toString());
                    tableInfoText.textContent = '';
                    tableInfoText.classList.remove(status);
                }, 5000);
            }
        });
    </script>
</body>
</html> 