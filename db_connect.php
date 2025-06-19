<?php

$host = 'localhost';     // Host database, biasanya localhost untuk XAMPP
$username = 'root';      // Username default XAMPP
$password = '';         // Password default XAMPP kosong
$database = 'projek_akhir'; // Nama database yang Anda buat

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set karakter encoding
$conn->set_charset("utf8");

// Opsional: Anda bisa menambahkan echo "Koneksi berhasil"; untuk pengujian awal

?> 