<?php
require_once 'db_connect.php'; // Sertakan file koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form dan sanitasi
    $tahun_ajaran = $conn->real_escape_string($_POST['tahun_ajaran']);
    $nama_mata_kuliah = $conn->real_escape_string($_POST['nama_mata_kuliah']);
    $asisten_praktikum = $conn->real_escape_string($_POST['asisten_praktikum']);
    $ruang_lab = $conn->real_escape_string($_POST['ruang_lab']);
    $kelas = $conn->real_escape_string($_POST['kelas']);
    $hari = $conn->real_escape_string($_POST['hari']);
    $waktu_mulai = $conn->real_escape_string($_POST['waktu_mulai']);
    $waktu_selesai = $conn->real_escape_string($_POST['waktu_selesai']);

    // Cek bentrok jadwal (ruang, laboran, atau kelas pada hari dan waktu yang sama)
    $sql_bentrok = "SELECT * FROM jadwal_praktikum WHERE hari = '$hari' AND ((ruang_lab = '$ruang_lab') OR (asisten_praktikum = '$asisten_praktikum') OR (kelas = '$kelas')) AND ((waktu_mulai < '$waktu_selesai' AND waktu_selesai > '$waktu_mulai'))";
    $result_bentrok = $conn->query($sql_bentrok);
    if ($result_bentrok && $result_bentrok->num_rows > 0) {
        // Jadwal bentrok
        echo "<script>alert('Jadwal bentrok! Ruang, laboran, atau kelas sudah terpakai pada hari dan jam tersebut.'); window.location.href='jadwal_praktikum.php?status=bentrok';</script>";
        $conn->close();
        exit();
    }

    // Query untuk menyimpan data ke database
    $sql = "INSERT INTO jadwal_praktikum (tahun_ajaran, nama_mata_kuliah, asisten_praktikum, ruang_lab, kelas, hari, waktu_mulai, waktu_selesai) VALUES ('$tahun_ajaran', '$nama_mata_kuliah', '$asisten_praktikum', '$ruang_lab', '$kelas', '$hari', '$waktu_mulai', '$waktu_selesai')";

    if ($conn->query($sql) === TRUE) {
        // Jika berhasil, redirect kembali ke halaman daftar jadwal praktikum
        header("Location: jadwal_praktikum.php?status=success_add");
        exit();
    } else {
        // Jika gagal, tampilkan error
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close(); // Tutup koneksi database
?> 