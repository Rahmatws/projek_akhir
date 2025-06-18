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
                <li><a href="dashboard.html"><i class="icon">🏠</i> Dashboard</a></li>
                <li><a href="jadwal_praktikum.php"><i class="icon">🗓️</i> Jadwal Praktikum</a></li>
                <li><a href="kelas.php"><i class="icon">🏫</i> Kelas</a></li>
                <li><a href="praktikan.php" class="active"><i class="icon">✍️</i> Praktikan</a></li>
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
                    <h2>Daftar Praktikan</h2>
                    <span class="breadcrumb">Data Master Praktikan, Menampilkan Data Praktikan</span>
                </div>
                <div class="user-info">
                    <span class="user-name">Uchiha Atep</span>
                    <img src="user.png" alt="User" class="user-avatar">
                </div>
            </div>
            <div class="praktikan-box">
                <div class="praktikan-header-bar">
                    <h3>Daftar Praktikan</h3>
                </div>
                <div class="praktikan-actions-bar">
                    <button class="btn-green" id="show-add-form">+ Tambah Praktikan</button>
                    <button class="btn-purple">Cetak</button>
                </div>
                <!-- Tabel Daftar Praktikan -->
                <div class="praktikan-table-section" id="praktikan-table-section">
                    <div class="praktikan-table-controls">
                        <div class="praktikan-table-left">
                            <label>Show
                                <select>
                                    <option>10</option>
                                    <option>25</option>
                                    <option>50</option>
                                    <option>100</option>
                                </select>
                                entries
                            </label>
                        </div>
                        <div class="praktikan-table-right">
                            <div class="table-actions-group">
                                <button class="btn-orange">Edit</button>
                                <button class="btn-red">Hapus</button>
                            </div>
                            <div class="search-box">
                                <label>Search: <input type="text" placeholder="Cari..."></label>
                            </div>
                        </div>
                    </div>
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
                                <tr>
                                    <td>1</td>
                                    <td>301210010</td>
                                    <td>Alwi Nurmalik Ibrahim</td>
                                    <td>Majalaya</td>
                                    <td>11-07-2001</td>
                                    <td>Teknik Informatika</td>
                                    <td><input type="checkbox" class="row-checkbox"></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>301210009</td>
                                    <td>Riska Nurhayan</td>
                                    <td>Baleendah</td>
                                    <td>23-12-2001</td>
                                    <td>Teknik Informatika</td>
                                    <td><input type="checkbox" class="row-checkbox"></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>301210008</td>
                                    <td>Rabiatul Islami</td>
                                    <td>Ciparay</td>
                                    <td>11-05-2002</td>
                                    <td>Teknik Informatika</td>
                                    <td><input type="checkbox" class="row-checkbox"></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>301210007</td>
                                    <td>Mohammad Anwar Saepuddin</td>
                                    <td>Buah Batu</td>
                                    <td>04-04-2002</td>
                                    <td>Teknik Informatika</td>
                                    <td><input type="checkbox" class="row-checkbox"></td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>301210006</td>
                                    <td>Moch Rivel Aghiya M.</td>
                                    <td>Majalaya</td>
                                    <td>06-03-2002</td>
                                    <td>Teknik Informatika</td>
                                    <td><input type="checkbox" class="row-checkbox"></td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>301210005</td>
                                    <td>Lorenza Shela Tansyah</td>
                                    <td>Ciparay</td>
                                    <td>11-07-2000</td>
                                    <td>Teknik Informatika</td>
                                    <td><input type="checkbox" class="row-checkbox"></td>
                                </tr>
                                <tr>
                                    <td>7</td>
                                    <td>301210004</td>
                                    <td>Agus Suryana</td>
                                    <td>Baleendah</td>
                                    <td>11-10-2001</td>
                                    <td>Teknik Informatika</td>
                                    <td><input type="checkbox" class="row-checkbox"></td>
                                </tr>
                                <tr>
                                    <td>8</td>
                                    <td>301210003</td>
                                    <td>Syahrizal Suhavi Alam</td>
                                    <td>Rancaekek</td>
                                    <td>12-11-2001</td>
                                    <td>Teknik Informatika</td>
                                    <td><input type="checkbox" class="row-checkbox"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <script>
                    // Select All Checkbox Functionality
                    const selectAll = document.getElementById('select-all');
                    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
                    selectAll.addEventListener('change', function() {
                        rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
                    });
                    rowCheckboxes.forEach(cb => {
                        cb.addEventListener('change', function() {
                            if (!cb.checked) {
                                selectAll.checked = false;
                            } else {
                                // Jika semua baris dicentang, header juga ikut centang
                                if ([...rowCheckboxes].every(c => c.checked)) {
                                    selectAll.checked = true;
                                }
                            }
                        });
                    });
                    </script>
                    <div class="praktikan-pagination">
                        <button class="btn-page">Previous</button>
                        <button class="btn-page active">1</button>
                        <button class="btn-page">2</button>
                        <button class="btn-page">Next</button>
                    </div>
                </div>
                <!-- Form Tambah Multi Praktikan -->
                <div class="praktikan-add-section" id="praktikan-add-section" style="display:none;">
                    <div class="praktikan-add-header">
                        <span class="add-icon">➕</span> <span class="add-title">Tambah Multi Praktikan</span>
                    </div>
                    <form id="form-multi-praktikan" autocomplete="off">
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
                                    <td><button type="button" class="btn-del-row">✖</button></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="praktikan-add-footer">
                            <button type="button" class="btn-purple" id="add-row">+ Baris Baru</button>
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
                        <td><button type="button" class="btn-del-row">✖</button></td>`;
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
                </script>
            </div>
        </div>
    </div>
</body>
</html> 