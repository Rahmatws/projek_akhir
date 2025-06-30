<?php
require_once 'db_connect.php';
// Praktikan statistik
$praktikan_if = $conn->query("SELECT COUNT(*) FROM praktikan WHERE prodi='Teknik Informatika'")->fetch_row()[0];
$praktikan_si = $conn->query("SELECT COUNT(*) FROM praktikan WHERE prodi='Sistem Informasi'")->fetch_row()[0];
$praktikan_fti = $praktikan_if + $praktikan_si;
$praktikan_total = $conn->query("SELECT COUNT(*) FROM praktikan")->fetch_row()[0];
// Kelas statistik
$kelas_if = $conn->query("SELECT COUNT(*) FROM kelas WHERE nama_kelas LIKE '%IF%'")->fetch_row()[0];
$kelas_si = $conn->query("SELECT COUNT(*) FROM kelas WHERE nama_kelas LIKE '%SI%'")->fetch_row()[0];
$kelas_fti = $kelas_if + $kelas_si;
// Mata Praktikum statistik
$mata_praktikum = $conn->query("SELECT semester, COUNT(*) as jumlah FROM mata_praktikum GROUP BY semester");
$mata_labels = [];
$mata_data = [];
while($row = $mata_praktikum->fetch_assoc()) {
    $mata_labels[] = 'Semester ' . $row['semester'];
    $mata_data[] = $row['jumlah'];
}
// Asisten statistik
$asisten_if = $conn->query("SELECT COUNT(*) FROM asisten_praktikum WHERE nama_prodi='Teknik Informatika'")->fetch_row()[0];
$asisten_si = $conn->query("SELECT COUNT(*) FROM asisten_praktikum WHERE nama_prodi='Sistem Informasi'")->fetch_row()[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="unibba-logo.png" alt="Logo" class="sidebar-logo">
                <h3>DAFTAR MENU PRAKTIKUM</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active"><i class="icon">🏠</i> Dashboard</a></li>
                <li><a href="jadwal_praktikum.php"><i class="icon">🗓️</i> Jadwal Praktikum</a></li>
                <li><a href="kelas.php"><i class="icon">🏫</i> Kelas</a></li>
                <li><a href="praktikan.php"><i class="icon">✍️</i> Praktikan</a></li>
                <li><a href="absensi_kehadiran.php"><i class="icon">✅</i> Absensi Kehadiran</a></li>
                <li><a href="mata_praktikum.php"><i class="icon">📚</i> Mata Praktikum</a></li>
                <li><a href="asisten_praktikum.php"><i class="icon">🧑‍🏫</i> Asisten Praktikum</a></li>
                <li><a href="ruang_laboratorium.php"><i class="icon">🔬</i> Ruang Laboratorium</a></li>
                <li><a href="laboran.php"><i class="icon">📄</i> Laboran</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="top-bar">
                <h2>ADMIN DASHBOARD</h2>
                <div class="datetime-info">
                    <div class="time">Waktu: <span id="clock"></span></div>
                    <div class="date">Tanggal: <span id="calendar"></span></div>
                </div>
            </div>
            <div class="data-cards">
                <div class="card">
                    <div class="card-header">DATA PRAKTIKAN</div>
                    <div class="card-content">
                        <canvas id="praktikanChart" height="80"></canvas>
                        <div class="data-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Total</th>
                                        <th>Praktikan IF</th>
                                        <th>Praktikan SI</th>
                                        <th>Praktikan FTI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo $praktikan_total; ?></td>
                                        <td><?php echo $praktikan_if; ?></td>
                                        <td><?php echo $praktikan_si; ?></td>
                                        <td><?php echo $praktikan_fti; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">DATA KELAS</div>
                    <div class="card-content">
                        <canvas id="kelasChart" height="80"></canvas>
                        <div class="data-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Kelas IF</th>
                                        <th>Kelas SI</th>
                                        <th>Kelas FTI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo $kelas_if; ?></td>
                                        <td><?php echo $kelas_si; ?></td>
                                        <td><?php echo $kelas_fti; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">DATA MATA PRAKTIKUM</div>
                    <div class="card-content">
                        <canvas id="matkumChart" height="80" style="max-width:100%;display:block;margin:0 auto;"></canvas>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">DATA ASISTEN PRAKTIKUM</div>
                    <div class="card-content">
                        <canvas id="asistenChart" height="80" style="max-width:100%;display:block;margin:0 auto;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    // Jam dan tanggal
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent = now.toLocaleTimeString();
        document.getElementById('calendar').textContent = now.toLocaleDateString();
    }
    setInterval(updateClock, 1000); updateClock();
    // Praktikan Chart
    new Chart(document.getElementById('praktikanChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Teknik Informatika', 'Sistem Informasi'],
            datasets: [{
                label: 'Jumlah Praktikan',
                data: [<?php echo $praktikan_if; ?>, <?php echo $praktikan_si; ?>],
                backgroundColor: ['#1976d2', '#43a047']
            }]
        },
        options: {responsive:true, plugins:{legend:{display:false}}}
    });
    // Kelas Chart
    new Chart(document.getElementById('kelasChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Kelas IF', 'Kelas SI'],
            datasets: [{
                label: 'Jumlah Kelas',
                data: [<?php echo $kelas_if; ?>, <?php echo $kelas_si; ?>],
                backgroundColor: ['#ffa000', '#8e24aa']
            }]
        },
        options: {responsive:true, plugins:{legend:{display:false}}}
    });
    // Mata Praktikum Chart
    new Chart(document.getElementById('matkumChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($mata_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($mata_data); ?>,
                backgroundColor: ['#1976d2','#43a047','#ffa000','#8e24aa','#e53935','#00897b']
            }]
        },
        options: {responsive:true}
    });
    // Asisten Chart
    new Chart(document.getElementById('asistenChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Teknik Informatika', 'Sistem Informasi'],
            datasets: [{
                data: [<?php echo $asisten_if; ?>, <?php echo $asisten_si; ?>],
                backgroundColor: ['#1976d2','#43a047']
            }]
        },
        options: {responsive:true}
    });
    </script>
</body>
</html> 