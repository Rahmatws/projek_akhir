<?php
    require_once 'db_connect.php'; // Sertakan file koneksi database

    // --- Pagination Logic ---
    $limit_per_page = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($current_page - 1) * $limit_per_page;

    // --- Search Logic ---
    $search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $where_clause = '';
    if (!empty($search_query)) {
        $where_clause = " WHERE nama_ruang LIKE '%$search_query%' OR lokasi LIKE '%$search_query%'";
    }

    // Query untuk mendapatkan total records
    $total_records_sql = "SELECT COUNT(id) AS total FROM ruang_laboratorium" . $where_clause;
    $total_records_result = $conn->query($total_records_sql);
    $total_records = $total_records_result->fetch_assoc()['total'];
    $total_pages = ceil($total_records / $limit_per_page);

    // Query untuk mendapatkan data ruang laboratorium
    $sql = "SELECT id, nama_ruang, lokasi FROM ruang_laboratorium" . $where_clause . " ORDER BY id ASC LIMIT $limit_per_page OFFSET $offset";
    $result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Laboratorium</title>
    <link rel="stylesheet" href="ruang_laboratorium.css">
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
                 <li><a href="dashboard.php"><i class="icon">🏠</i> Dashboard</a></li>
                <li><a href="jadwal_praktikum.php"><i class="icon">🗓️</i> Jadwal Praktikum</a></li>
                <li><a href="kelas.php"><i class="icon">🏫</i> Kelas</a></li>
                <li><a href="praktikan.php"><i class="icon">✍️</i> Praktikan</a></li>
                <li><a href="laporan_absensi.php"><i class="icon">✅</i> Absensi Kehadiran</a></li>
                <li><a href="mata_praktikum.php"><i class="icon">📚</i> Mata Praktikum</a></li>
                <li><a href="asisten_praktikum.php"><i class="icon">🧑‍🏫</i> Asisten Praktikum</a></li>
                <li><a href="ruang_laboratorium.php" class="active"><i class="icon">🔬</i> Ruang Laboratorium</a></li>
                <li><a href="laboran.php"><i class="icon">📄</i> Laboran</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="top-bar">
                <div class="title-breadcrumb">
                    <h2>Daftar Ruang Laboratorium</h2>
                    <span class="breadcrumb">Data Master Ruang Lab, Menampilkan Data Ruang Laboratorium</span>
                </div>
                <div class="user-info">
                    <span class="user-name">Uchiha Atep</span>
                    <img src="user.png" alt="User" class="user-avatar">
                </div>
            </div>

            <!-- Container untuk Daftar Ruangan -->
            <div class="lab-list-container" id="lab-list-container">
                <div class="lab-list-header">
                    <h2><span class="header-icon">🔬</span>Daftar Ruang Lab Praktikum</h2>
                </div>
                <div class="lab-actions">
                    <button class="add-lab-button" id="show-add-form-button">+ Tambah Ruang Laboratorium</button>
                </div>

                <div class="data-table">
                     <!-- Kontrol untuk entri dan pencarian akan ditambahkan di sini via JS jika diperlukan -->
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Ruang</th>
                                <th>Lokasi</th>
                                <th>Pilihan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result && $result->num_rows > 0) {
                                $no = $offset + 1;
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $no++ . "</td>";
                                    echo "<td>" . htmlspecialchars($row["nama_ruang"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["lokasi"]) . "</td>";
                                    echo "<td>";
                                    echo "<button class='action-button edit-button' data-id='" . $row["id"] . "' data-nama='" . htmlspecialchars($row["nama_ruang"]) . "' data-lokasi='" . htmlspecialchars($row["lokasi"]) . "'>📝</button>";
                                    echo "<button class='action-button delete-button' data-id='" . $row["id"] . "'>🗑️</button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>Tidak ada data ruang laboratorium.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                     <!-- Footer tabel dengan info dan pagination akan ditambahkan di sini via JS jika diperlukan -->
                </div>
            </div>

            <!-- Container untuk Form Tambah Ruangan (tersembunyi) -->
            <div class="add-lab-form-container" id="add-lab-form-container" style="display: none;">
                <div class="add-lab-form-header">
                    <h2><span class="header-icon">➕</span> Tambah Ruang Laboratorium</h2>
                </div>
                <div class="add-lab-form-content">
                    <form action="add_ruang_laboratorium.php" method="POST">
                        <div class="form-group">
                            <label for="namaRuang">Ruang Lab</label>
                            <input type="text" id="namaRuang" name="nama_ruang" placeholder="Nama Ruang" required>
                        </div>
                        <div class="form-group">
                            <label for="lokasi">Lokasi</label>
                            <input type="text" id="lokasi" name="lokasi" placeholder="Lokasi" required>
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

            <!-- Container untuk Form Edit Ruangan (tersembunyi) -->
            <div class="edit-lab-form-container" id="edit-lab-form-container" style="display: none;">
                <div class="edit-lab-form-header">
                    <h2><span class="header-icon">📝</span> Edit Ruang Laboratorium</h2>
                </div>
                <div class="edit-lab-form-content">
                    <form action="update_ruang_laboratorium.php" method="POST">
                        <input type="hidden" id="editLabId" name="id">
                        <div class="form-group">
                            <label for="editNamaRuang">Nama Ruang</label>
                            <input type="text" id="editNamaRuang" name="nama_ruang" placeholder="Nama Ruang" required>
                        </div>
                        <div class="form-group">
                            <label for="editLokasi">Lokasi</label>
                            <input type="text" id="editLokasi" name="lokasi" placeholder="Lokasi" required>
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
        // Selektor elemen
        const labListContainer = document.getElementById('lab-list-container');
        const addLabFormContainer = document.getElementById('add-lab-form-container');
        const editLabFormContainer = document.getElementById('edit-lab-form-container');

        // Tombol
        const showAddFormBtn = document.getElementById('show-add-form-button');
        const backToListBtn = document.getElementById('back-to-list-button');
        const backFromEditBtn = document.getElementById('back-from-edit-button');

        // --- Logika untuk menampilkan/menyembunyikan form ---

        // Tampilkan form Tambah
        showAddFormBtn.addEventListener('click', function() {
            labListContainer.style.display = 'none';
            addLabFormContainer.style.display = 'block';
        });

        // Kembali ke daftar dari form Tambah
        backToListBtn.addEventListener('click', function() {
            addLabFormContainer.style.display = 'none';
            labListContainer.style.display = 'block';
        });
        
        // Kembali ke daftar dari form Edit
        backFromEditBtn.addEventListener('click', function() {
            editLabFormContainer.style.display = 'none';
            labListContainer.style.display = 'block';
        });

        // --- Logika untuk Tombol Edit ---
        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', function() {
                // Ambil data dari atribut data-*
                const id = this.dataset.id;
                const nama = this.dataset.nama;
                const lokasi = this.dataset.lokasi;

                // Isi form edit dengan data yang ada
                document.getElementById('editLabId').value = id;
                document.getElementById('editNamaRuang').value = nama;
                document.getElementById('editLokasi').value = lokasi;

                // Tampilkan form edit
                labListContainer.style.display = 'none';
                editLabFormContainer.style.display = 'block';
            });
        });

        // --- Logika untuk Tombol Hapus ---
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const confirmation = confirm('Apakah Anda yakin ingin menghapus data ini?');

                if (confirmation) {
                    // Buat form sementara untuk mengirim data ID via POST
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'delete_ruang_laboratorium.php';

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
    });
    </script>
</body>
</html> 