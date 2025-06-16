<?php
require_once 'db_connect.php'; // Sertakan file koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form dan sanitasi
    $id = $conn->real_escape_string($_POST['id']);
    $tahun_ajaran = $conn->real_escape_string($_POST['tahun_ajaran']);
    $nama_mata_kuliah = $conn->real_escape_string($_POST['nama_mata_kuliah']);
    $asisten_praktikum = $conn->real_escape_string($_POST['asisten_praktikum']);
    $ruang_lab = $conn->real_escape_string($_POST['ruang_lab']);
    $kelas = $conn->real_escape_string($_POST['kelas']);
    $hari = $conn->real_escape_string($_POST['hari']);
    $waktu_mulai = $conn->real_escape_string($_POST['waktu_mulai']);
    $waktu_selesai = $conn->real_escape_string($_POST['waktu_selesai']);

    // Query untuk mengupdate data di database
    $sql = "UPDATE jadwal_praktikum SET 
            tahun_ajaran = '$tahun_ajaran',
            nama_mata_kuliah = '$nama_mata_kuliah',
            asisten_praktikum = '$asisten_praktikum',
            ruang_lab = '$ruang_lab',
            kelas = '$kelas',
            hari = '$hari',
            waktu_mulai = '$waktu_mulai',
            waktu_selesai = '$waktu_selesai'
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        // Jika berhasil, redirect kembali ke halaman daftar jadwal praktikum
        header("Location: jadwal_praktikum.php?status=success_update");
        exit();
    } else {
        // Jika gagal, tampilkan error
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close(); // Tutup koneksi database
?> 