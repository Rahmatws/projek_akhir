<?php
require_once 'db_connect.php'; // Sertakan file koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form dan sanitasi
    $nidn = $conn->real_escape_string($_POST['nidn']);
    $nama_asisten = $conn->real_escape_string($_POST['nama_asisten']);
    $alamat = $conn->real_escape_string($_POST['alamat']);
    $tanggal_lahir_input = $conn->real_escape_string($_POST['tanggal_lahir']);
    $nama_prodi = $conn->real_escape_string($_POST['nama_prodi']);

    // Konversi format tanggal dari dd/mm/yyyy atau dd-mm-yyyy ke YYYY-MM-DD untuk MySQL
    if (preg_match('/^(\d{2})[-/](\d{2})[-/](\d{4})$/', $tanggal_lahir_input, $matches)) {
        $tanggal_lahir = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
    } else {
        $tanggal_lahir = NULL; // Jika format tanggal salah, simpan sebagai NULL
        // Atau Anda bisa menambahkan error handling yang lebih baik di sini
    }

    // Query untuk menyimpan data ke database
    $sql = "INSERT INTO asisten_praktikum (nidn, nama_asisten, alamat, tanggal_lahir, nama_prodi) VALUES ('$nidn', '$nama_asisten', '$alamat', '$tanggal_lahir', '$nama_prodi')";

    if ($conn->query($sql) === TRUE) {
        // Jika berhasil, redirect kembali ke halaman daftar asisten
        header("Location: asisten_praktikum.html?status=success_add");
        exit();
    } else {
        // Jika gagal, tampilkan error
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close(); // Tutup koneksi database
?> 