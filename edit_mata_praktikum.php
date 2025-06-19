<?php
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mata Praktikum</title>
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
                <li><a href="dashboard.html"><i class="icon">🏠</i> Dashboard</a></li>
                <li><a href="jadwal_praktikum.php"><i class="icon">🗓️</i> Jadwal Praktikum</a></li>
                <li><a href="kelas.php"><i class="icon">🏫</i> Kelas</a></li>
                <li><a href="praktikan.php"><i class="icon">✍️</i> Praktikan</a></li>
                <li><a href="absensi_kehadiran.html"><i class="icon">✅</i> Absensi Kehadiran</a></li>
                <li><a href="mata_praktikum.php" class="active"><i class="icon">📚</i> Mata Praktikum</a></li>
                <li><a href="asisten_praktikum.php"><i class="icon">🧑‍🏫</i> Asisten Praktikum</a></li>
                <li><a href="ruang_laboratorium.html"><i class="icon">🔬</i> Ruang Laboratorium</a></li>
                <li><a href="laboran.php"><i class="icon">📄</i> Laboran</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="top-bar">
                <div class="title-breadcrumb">
                    <h2>Edit Mata Praktikum</h2>
                    <span class="breadcrumb">Form untuk melakukan edit data Mata Praktikum</span>
                </div>
                <div class="user-info">
                    <span class="user-name">Uchiha Atep</span>
                    <img src="user.png" alt="User" class="user-avatar">
                </div>
            </div>
            <div class="mata-praktikum-box">
                <div class="mata-praktikum-header-bar">
                    <h3>Edit Mata Praktikum</h3>
                </div>
                <form method="post" action="update_mata_praktikum.php">
                    <table class="mata-praktikum-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Mata Kuliah</th>
                                <th>Nama Mata Kuliah</th>
                                <th>SKS</th>
                                <th>Semester</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (isset($_POST['selected_kode'])) {
                            $kodes = $_POST['selected_kode'];
                            $no = 1;
                            foreach ($kodes as $kode) {
                                $sql = "SELECT * FROM mata_praktikum WHERE kode_matkul = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("s", $kode);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                if ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>$no</td>
                                        <td><input type='text' name='kode_matkul[]' value='".htmlspecialchars($row['kode_matkul'])."' required>
                                            <input type='hidden' name='original_kode[]' value='".htmlspecialchars($row['kode_matkul'])."'></td>
                                        <td><input type='text' name='nama_matkul[]' value='".htmlspecialchars($row['nama_matkul'])."' required></td>
                                        <td><input type='number' name='sks[]' value='".htmlspecialchars($row['sks'])."' required min='1'></td>
                                        <td><input type='number' name='semester[]' value='".htmlspecialchars($row['semester'])."' required min='1'></td>
                                    </tr>";
                                    $no++;
                                }
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                    <div style="margin-top:16px; text-align:right;">
                        <button type="submit" class="btn-green">Simpan</button>
                        <a href="mata_praktikum.php" class="btn-back">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 