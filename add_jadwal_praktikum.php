<?php
require_once 'db_connect.php'; // Sertakan file koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil semua data dari form
    $tahun_ajaran = $_POST['tahun_ajaran'];
    $nama_mata_kuliah = $_POST['nama_mata_kuliah'];
    $asisten_praktikum = $_POST['asisten_praktikum'];
    $ruang_lab = $_POST['ruang_lab'];
    $kelas = $_POST['kelas'];
    $semester = $_POST['semester']; // Data semester baru
    $hari = $_POST['hari'];
    $waktu_mulai = $_POST['waktu_mulai'];
    $waktu_selesai = $_POST['waktu_selesai'];

    // Validasi sederhana
    if (empty($semester)) {
        die("Error: Semester tidak boleh kosong. Pastikan memilih kelas dari dropdown.");
    }

    // Cek jadwal bentrok (contoh query, sesuaikan jika perlu)
    $check_sql = "SELECT * FROM jadwal_praktikum WHERE hari = ? AND ruang_lab = ? AND ((waktu_mulai < ? AND waktu_selesai > ?) OR (waktu_mulai >= ? AND waktu_mulai < ?))";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("ssssss", $hari, $ruang_lab, $waktu_selesai, $waktu_mulai, $waktu_mulai, $waktu_selesai);
    $stmt_check->execute();
    $check_result = $stmt_check->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Jadwal bentrok! Silakan periksa kembali hari, ruang, dan waktu.'); window.history.back();</script>";
    } else {
        // Tidak ada jadwal bentrok, lanjutkan insert
        $insert_sql = "INSERT INTO jadwal_praktikum (tahun_ajaran, nama_mata_kuliah, asisten_praktikum, ruang_lab, kelas, semester, hari, waktu_mulai, waktu_selesai) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_sql);
        // Tipe data: s=string, i=integer. Total 9 parameter.
        $stmt_insert->bind_param("sssssisss", $tahun_ajaran, $nama_mata_kuliah, $asisten_praktikum, $ruang_lab, $kelas, $semester, $hari, $waktu_mulai, $waktu_selesai);

        if ($stmt_insert->execute()) {
            echo "<script>alert('Jadwal berhasil ditambahkan!'); window.location.href='jadwal_praktikum.php';</script>";
        } else {
            echo "Error: " . $stmt_insert->error;
    }
        $stmt_insert->close();
    }
    $stmt_check->close();
    $conn->close();
}
?> 