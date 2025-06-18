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
    // Sesuaikan kolom pencarian dengan kolom di tabel `users` dan `tb_laboran_details`
    $where_clause = " WHERE u.username LIKE '%$search_query%' OR u.role LIKE '%$search_query%' OR tld.nama LIKE '%$search_query%' OR tld.alamat LIKE '%$search_query%' OR tld.hp LIKE '%$search_query%'";
}

// Query untuk mendapatkan total records (dengan atau tanpa pencarian)
// Menggunakan JOIN untuk menghitung total user yang memiliki detail laboran
$total_records_sql = "SELECT COUNT(u.id) AS total FROM users u JOIN tb_laboran_details tld ON u.username = tld.username" . $where_clause;
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

// Query untuk mendapatkan data laboran dari tabel users dan tb_laboran_details
// Mengambil id dari tabel users sebagai identifikasi unik untuk operasi CRUD
$sql = "SELECT u.id, u.username, u.role, tld.nama, tld.gender, tld.alamat, tld.hp
        FROM users u
        JOIN tb_laboran_details tld ON u.username = tld.username"
        . $where_clause .
        " ORDER BY u.username ASC LIMIT $limit_per_page OFFSET $offset";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Laboran</title>
    <link rel="stylesheet" href="laboran.css">
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
                <li><a href="kelas.php"><i class="icon">🏫</i> Kelas</a></li>
                <li><a href="praktikan.php"><i class="icon">✍️</i> Praktikan</a></li>
                <li><a href="absensi_kehadiran.html"><i class="icon">✅</i> Absensi Kehadiran</a></li>
                <li><a href="mata_praktikum.html"><i class="icon">📚</i> Mata Praktikum</a></li>
                <li><a href="asisten_praktikum.php"><i class="icon">🧑‍🏫</i> Asisten Praktikum</a></li>
                <li><a href="ruang_laboratorium.html"><i class="icon">🔬</i> Ruang Laboratorium</a></li>
                <li><a href="laboran.php" class="active"><i class="icon">📄</i> Laboran</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="top-bar">
                <div class="title-breadcrumb">
                    <h2>Daftar Laboran</h2>
                    <span class="breadcrumb">Data Master Laboran, Menampilkan data Laboran Laboratorium FTI</span>
                </div>
                <div class="user-info">
                    <span class="user-name">Uchiha Atep</span>
                    <img src="user.png" alt="User" class="user-avatar">
                </div>
            </div>

            <div class="laboran-list-container" id="laboran-list-container">
                <div class="laboran-list-header">
                    <h2><span class="header-icon">📄</span> Daftar Laboran</h2>
                </div>
                <div class="laboran-actions">
                    <button class="add-laboran-button" id="show-add-form-button">+ Tambah Laboran</button>
                </div>

                <!-- Area untuk menampilkan pesan status -->
                <div id="status-message" class="status-message" style="display: none;"></div>

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
                                <th><span class="sort-icon">⬆️</span> ID Laboran</th>
                                <th><span class="sort-icon">⬆️</span> Status</th>
                                <th><span class="sort-icon">⬆️</span> Nama</th>
                                <th><span class="sort-icon">⬆️</span> Alamat</th>
                                <th><span class="sort-icon">⬆️</span> Hp</th>
                                <th><span class="sort-icon">⬆️</span> Gender</th>
                                <th>Pilihan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                $no = $offset + 1; // Sesuaikan nomor urut dengan offset
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $no++ . "</td>";
                                    echo "<td>" . htmlspecialchars($row["username"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["role"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["nama"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["alamat"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["hp"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["gender"]) . "</td>";
                                    // Menggunakan id dari tabel users sebagai data-id
                                    echo "<td>";
                                    echo "<div class=\"action-buttons-wrapper\">"; // Wrapper baru
                                    echo "<button class=\"action-button edit-button\" data-id=\"" . htmlspecialchars($row["id"]) . "\" data-username=\"" . htmlspecialchars($row["username"]) . "\">📝</button>";
                                    echo "<button class=\"action-button delete-button\" data-id=\"" . htmlspecialchars($row["id"]) . "\">🗑️</button>";
                                    echo "</div>"; // Tutup wrapper
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan=\"8\">Tidak ada data laboran yang ditemukan.</td></tr>";
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

            <div class="add-laboran-form-container" id="add-laboran-form-container" style="display: none;">
                <div class="add-laboran-form-header">
                    <h2><span class="header-icon">➕</span> Tambah Laboran</h2>
                </div>
                <div class="add-laboran-form-content">
                    <form action="add_laboran.php" method="POST"> <!-- Arahkan ke file proses tambah baru -->
                        <div class="form-group">
                            <label for="idLaboran">ID Laboran</label>
                            <input type="text" id="idLaboran" name="username" placeholder="ID Petugas (sebagai Username login)" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Password (sebagai password login)" required>
                        </div>
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" id="nama" name="nama" placeholder="Nama" required>
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <div class="radio-group">
                                <input type="radio" id="lakiLaki" name="gender" value="Laki-Laki" required>
                                <label for="lakiLaki">Laki - Laki</label>
                                <input type="radio" id="perempuan" name="gender" value="Perempuan">
                                <label for="perempuan">Perempuan</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <textarea id="alamat" name="alamat" placeholder="Alamat"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Status Laboran</label>
                            <div class="radio-group">
                                <input type="radio" id="statusAdmin" name="role" value="admin" required> <!-- name="role" karena dari tabel users -->
                                <label for="statusAdmin">Admin</label>
                                <input type="radio" id="statusLaboran" name="role" value="laboran">
                                <label for="statusLaboran">Laboran</label>
                                <input type="radio" id="statusKepalaLab" name="role" value="kepala">
                                <label for="statusKepalaLab">Kepala Lab</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="noHp">NO HP</label>
                            <input type="text" id="noHp" name="hp" placeholder="No Hp">
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

            <div class="edit-laboran-form-container" id="edit-laboran-form-container" style="display: none;">
                <div class="edit-laboran-form-header">
                    <h2><span class="header-icon">📝</span> Edit Laboran</h2>
                </div>
                <div class="edit-laboran-form-content">
                    <form action="update_laboran.php" method="POST"> <!-- Arahkan ke file proses update baru -->
                        <input type="hidden" id="editIdUser" name="id"> <!-- Menyimpan ID user dari tabel users -->
                        <div class="form-group">
                            <label for="editIdLaboran">ID Laboran</label>
                            <input type="text" id="editIdLaboran" name="username" placeholder="ID Petugas (sebagai Username login)" readonly>
                        </div>
                        <div class="form-group">
                            <label for="editPassword">Password</label>
                            <input type="password" id="editPassword" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
                        </div>
                        <div class="form-group">
                            <label for="editNama">Nama</label>
                            <input type="text" id="editNama" name="nama" placeholder="Nama" required>
                        </div>
                        <div class="form-group">
                            <label for="editAlamat">Alamat</label>
                            <textarea id="editAlamat" name="alamat" placeholder="Alamat"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <div class="radio-group">
                                <input type="radio" id="editLakiLaki" name="gender" value="Laki-Laki" required>
                                <label for="editLakiLaki">Laki - Laki</label>
                                <input type="radio" id="editPerempuan" name="gender" value="Perempuan">
                                <label for="editPerempuan">Perempuan</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Status Laboran</label>
                            <div class="radio-group">
                                <input type="radio" id="editStatusAdmin" name="role" value="admin" required>
                                <label for="editStatusAdmin">Admin</label>
                                <input type="radio" id="editStatusLaboran" name="role" value="laboran">
                                <label for="editStatusLaboran">Laboran</label>
                                <input type="radio" id="editStatusKepalaLab" name="role" value="kepala">
                                <label for="editStatusKepalaLab">Kepala Lab</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="editNoHp">NO HP</label>
                            <input type="text" id="editNoHp" name="hp" placeholder="No Hp">
                        </div>
                        <div class="form-group">
                            <label>Foto</label>
                            <div class="photo-upload-group">
                                <img src="user.png" alt="Foto Laboran" class="laboran-photo" id="editLaboranPhoto">
                                <input type="file" id="editFoto" name="editFoto" accept="image/*">
                            </div>
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
            const laboranListContainer = document.getElementById('laboran-list-container');
            const addLaboranFormContainer = document.getElementById('add-laboran-form-container');
            const headerTitle = document.querySelector('.top-bar h2');
            const breadcrumb = document.querySelector('.top-bar .breadcrumb');

            showAddFormBtn.addEventListener('click', function() {
                laboranListContainer.style.display = 'none';
                addLaboranFormContainer.style.display = 'block';
                headerTitle.textContent = 'Tambah Laboran';
                breadcrumb.textContent = 'Data Master Laboran, Menambahkan Data Laboran Laboratorium FTI';
            });

            backToListBtn.addEventListener('click', function() {
                addLaboranFormContainer.style.display = 'none';
                laboranListContainer.style.display = 'block';
                headerTitle.textContent = 'Daftar Laboran';
                breadcrumb.textContent = 'Data Master Laboran, Menampilkan data Laboran Laboratorium FTI';
            });

            const editButtons = document.querySelectorAll('.action-button.edit-button');
            const editLaboranFormContainer = document.getElementById('edit-laboran-form-container');
            const backFromEditBtn = document.getElementById('back-from-edit-button');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    laboranListContainer.style.display = 'none';
                    addLaboranFormContainer.style.display = 'none'; // Hide add form if open
                    editLaboranFormContainer.style.display = 'block';

                    // Populate form with data from the clicked row
                    const row = this.closest('tr');
                    const userId = button.dataset.id; // Ambil ID user
                    const username = row.cells[1].textContent; // ID Laboran (username)
                    const role = row.cells[2].textContent;     // Status (role)
                    const nama = row.cells[3].textContent;     // Nama
                    const alamat = row.cells[4].textContent;   // Alamat
                    const hp = row.cells[5].textContent;       // No Hp
                    const gender = row.cells[6].textContent;    // Gender

                    document.getElementById('editIdUser').value = userId; // Set ID user
                    document.getElementById('editIdLaboran').value = username;
                    document.getElementById('editNama').value = nama;
                    document.getElementById('editAlamat').value = alamat;
                    document.getElementById('editNoHp').value = hp;

                    // Set Gender radio button
                    if (gender === 'Laki-Laki') {
                        document.getElementById('editLakiLaki').checked = true;
                    } else if (gender === 'Perempuan') {
                        document.getElementById('editPerempuan').checked = true;
                    }

                    // Set Status Laboran radio button
                    if (role.includes('admin')) {
                        document.getElementById('editStatusAdmin').checked = true;
                    } else if (role.includes('laboran')) {
                        document.getElementById('editStatusLaboran').checked = true;
                    } else if (role.includes('kepala')) {
                        document.getElementById('editStatusKepalaLab').checked = true;
                    }
                    
                    // Photo placeholder (real implementation would involve dynamic loading)
                    document.getElementById('editLaboranPhoto').src = 'user.png'; // Placeholder

                    headerTitle.textContent = 'Edit Laboran';
                    breadcrumb.textContent = 'Data Master Laboran, Mengubah Data Laboran Laboratorium FTI';
                });
            });

            backFromEditBtn.addEventListener('click', function() {
                editLaboranFormContainer.style.display = 'none';
                laboranListContainer.style.display = 'block';
                headerTitle.textContent = 'Daftar Laboran';
                breadcrumb.textContent = 'Data Master Laboran, Menampilkan data Laboran Laboratorium FTI';
            });

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
                        return; // If 'Lainnya' is selected, don't update URL yet, wait for manual input
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

            // --- Delete Button Logic --- Start
            const deleteButtons = document.querySelectorAll('.action-button.delete-button');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault(); // Mencegah aksi default (misalnya, submit form)
                    const laboranId = this.dataset.id; // Ambil ID laboran dari data-id atribut
                    
                    if (confirm('Apakah Anda yakin ingin menghapus laboran ini?\nID Laboran: ' + laboranId)) {
                        // Redirect ke skrip PHP untuk menghapus
                        window.location.href = 'delete_laboran.php?id=' + laboranId;
                    }
                });
            });
            // --- Delete Button Logic --- End

            // --- Status Message Display Logic --- Start
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const messageDiv = document.getElementById('status-message');

            if (status) {
                let message = '';
                let messageType = 'info'; // default

                switch(status) {
                    case 'success_add':
                        message = 'Data laboran berhasil ditambahkan!';
                        messageType = 'success';
                        break;
                    case 'success_update':
                        message = 'Data laboran berhasil diperbarui!';
                        messageType = 'success';
                        break;
                    case 'success_delete':
                        message = 'Data laboran berhasil dihapus!';
                        messageType = 'success';
                        break;
                    case 'error_duplicate_username':
                        message = 'Gagal menambahkan/memperbarui data: Username sudah ada. Mohon gunakan username lain.';
                        messageType = 'error';
                        break;
                    case 'error_duplicate_username_update':
                        message = 'Gagal memperbarui data: Username sudah digunakan oleh user lain. Mohon gunakan username lain.';
                        messageType = 'error';
                        break;
                    case 'error_no_id':
                        message = 'Gagal menghapus data: ID laboran tidak ditemukan.';
                        messageType = 'error';
                        break;
                    case 'error':
                        const errorMessage = urlParams.get('message') || 'Terjadi kesalahan tak terduga.';
                        message = 'Terjadi kesalahan: ' + decodeURIComponent(errorMessage);
                        messageType = 'error';
                        break;
                    default:
                        message = 'Pesan status tidak dikenal.';
                        messageType = 'info';
                }

                messageDiv.textContent = message;
                messageDiv.className = 'status-message ' + messageType; // Add class for styling
                messageDiv.style.display = 'block';

                // Hapus pesan setelah beberapa detik
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                    // Hapus parameter status dari URL agar tidak muncul lagi saat refresh
                    const newUrl = new URL(window.location.href);
                    newUrl.searchParams.delete('status');
                    newUrl.searchParams.delete('message');
                    window.history.replaceState({}, document.title, newUrl.toString());
                }, 5000); // Pesan akan hilang setelah 5 detik
            }
            // --- Status Message Display Logic --- End

        });
    </script>
</body>
</html> 