<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Praktikan</title>
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
                <li><a href="mata_praktikum.php"><i class="icon">📚</i> Mata Praktikum</a></li>
                <li><a href="asisten_praktikum.php"><i class="icon">🧑‍🏫</i> Asisten Praktikum</a></li>
                <li><a href="ruang_laboratorium.html"><i class="icon">🔬</i> Ruang Laboratorium</a></li>
                <li><a href="laboran.php"><i class="icon">📄</i> Laboran</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="top-bar">
                <div class="title-breadcrumb">
                    <h2>Edit Praktikan</h2>
                    <span class="breadcrumb">Form untuk melakukan edit data Praktikan</span>
                </div>
                <div class="user-info">
                    <?php
                    session_start();
                    $nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'User';
                    $foto = isset($_SESSION['foto']) && $_SESSION['foto'] ? 'uploads/laboran/' . $_SESSION['foto'] : 'user.png';
                    $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
                    if($role === 'kepala') {
                        header('Location: praktikan.php?error=akses');
                        exit();
                    }
                    ?>
                    <span class="user-name"><?php echo htmlspecialchars($nama); ?></span>
                    <img src="<?php echo htmlspecialchars($foto); ?>" alt="User" class="user-avatar">
                </div>
            </div>
            <div class="praktikan-box">
                <div class="praktikan-header-bar">
                    <h3>Edit Praktikan</h3>
                </div>
                <div class="praktikan-edit-section">
                    <div class="praktikan-edit-header">
                        <span class="edit-icon">✏️</span> <span class="edit-title">Edit Praktikan</span>
                    </div>
                    <form id="form-edit-praktikan" method="post" action="update_praktikan.php">
                        <table class="praktikan-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Nama Lengkap</th>
                                    <th>Alamat</th>
                                    <th>Tgl Lahir</th>
                                    <th>Prodi</th>
                                </tr>
                            </thead>
                            <tbody id="edit-praktikan-tbody">
                            <?php
                            include 'db_connect.php';
                            
                            if(isset($_POST['selected_ids'])) {
                                $ids = $_POST['selected_ids'];
                                $counter = 1;
                                
                                foreach($ids as $nim) {
                                    $sql = "SELECT * FROM praktikan WHERE nim = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("s", $nim);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    
                                    if($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                            <td>{$counter}</td>
                                            <td>
                                                <input type='text' name='nim[]' value='{$row['nim']}' required>
                                                <input type='hidden' name='original_nim[]' value='{$row['nim']}'>
                                            </td>
                                            <td><input type='text' name='nama[]' value='{$row['nama_lengkap']}' required></td>
                                            <td><input type='text' name='alamat[]' value='{$row['alamat']}' required></td>
                                            <td><input type='date' name='tgl_lahir[]' value='{$row['tgl_lahir']}' required></td>
                                            <td>
                                                <select name='prodi[]' required>
                                                    <option value='Teknik Informatika' " . ($row['prodi'] == 'Teknik Informatika' ? 'selected' : '') . ">Teknik Informatika</option>
                                                    <option value='Sistem Informasi' " . ($row['prodi'] == 'Sistem Informasi' ? 'selected' : '') . ">Sistem Informasi</option>
                                                </select>
                                            </td>
                                        </tr>";
                                        $counter++;
                                    }
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                        <div class="praktikan-edit-footer">
                            <div class="footer-right">
                                <button type="submit" class="btn-green">Simpan</button>
                                <a href="praktikan.php" class="btn-back">Kembali</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 