<?php
require_once 'db_connect.php'; // Sertakan file koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil semua data dari form
    $id = $_POST['id'];
    $tahun_ajaran = $_POST['tahun_ajaran'];
    $nama_mata_kuliah = $_POST['nama_mata_kuliah'];
    $asisten_praktikum = $_POST['asisten_praktikum'];
    $ruang_lab = $_POST['ruang_lab'];
    $kelas = $_POST['kelas'];
    $semester = $_POST['semester']; // Data semester baru
    $hari = $_POST['hari'];
    $waktu_mulai = $_POST['waktu_mulai'];
    $waktu_selesai = $_POST['waktu_selesai'];
    $waktu = $waktu_mulai . ' - ' . $waktu_selesai;

    // Validasi sederhana
    if (empty($id) || empty($semester)) {
        die("Error: ID atau Semester tidak boleh kosong.");
    }

    // Cek bentrok jadwal (ruang, laboran, atau kelas pada hari dan waktu yang sama, kecuali id yang sedang diedit)
    $sql_bentrok = "SELECT * FROM jadwal_praktikum WHERE id != '$id' AND hari = '$hari' AND ((ruang_lab = '$ruang_lab') OR (asisten_praktikum = '$asisten_praktikum') OR (kelas = '$kelas')) AND ((waktu_mulai < '$waktu_selesai' AND waktu_selesai > '$waktu_mulai'))";
    $result_bentrok = $conn->query($sql_bentrok);
    if ($result_bentrok && $result_bentrok->num_rows > 0) {
        // Jadwal bentrok
        echo "<script>alert('Jadwal bentrok! Ruang, laboran, atau kelas sudah terpakai pada hari dan jam tersebut.'); window.location.href='jadwal_praktikum.php?status=bentrok';</script>";
        $conn->close();
        exit();
    }

    // Query untuk update data
    $sql = "UPDATE jadwal_praktikum SET 
                tahun_ajaran = ?, 
                nama_mata_kuliah = ?, 
                asisten_praktikum = ?, 
                ruang_lab = ?, 
                kelas = ?, 
                semester = ?, 
                hari = ?, 
                waktu_mulai = ?, 
                waktu_selesai = ? 
            WHERE id = ?";
            
    $stmt = $conn->prepare($sql);
    // Tipe data: s=string, i=integer. Total 10 parameter (9 untuk set, 1 untuk where).
    $stmt->bind_param("sssssisssi", $tahun_ajaran, $nama_mata_kuliah, $asisten_praktikum, $ruang_lab, $kelas, $semester, $hari, $waktu_mulai, $waktu_selesai, $id);

    if ($stmt->execute()) {
        // Catat ke perubahan_jadwal
        $petugas = isset($_SESSION['username']) ? $_SESSION['username'] : 'Petugas';
        $tanggal_ubah = date('Y-m-d');
        $sql2 = "INSERT INTO perubahan_jadwal (tanggal_ubah, petugas, askum, kelas, matkul, hari, waktu) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("sssssss", $tanggal_ubah, $petugas, $asisten_praktikum, $kelas, $nama_mata_kuliah, $hari, $waktu);
        $stmt2->execute();
        // Redirect kembali ke halaman daftar setelah berhasil
        header("location: jadwal_praktikum.php?status=updated");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?> 