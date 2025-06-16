<?php

$servername = "localhost"; // Biasanya localhost jika Anda menggunakan XAMPP/WAMP/MAMP
$username = "root";       // Username default untuk MySQL di XAMPP/WAMP/MAMP
$password = "";           // Password default kosong di XAMPP/WAMP/MAMP
$dbname = "projek_akhir"; // Nama database yang sudah Anda buat

// Membuat koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
// Opsional: Anda bisa menambahkan echo "Koneksi berhasil"; untuk pengujian awal

?> 