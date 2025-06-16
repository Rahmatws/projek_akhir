<?php
require_once 'db_connect.php'; // Sertakan file koneksi database

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);

    // Query untuk menghapus data dari database
    $sql = "DELETE FROM jadwal_praktikum WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        // Jika berhasil, redirect kembali ke halaman daftar jadwal praktikum
        header("Location: jadwal_praktikum.php?status=success_delete");
        exit();
    } else {
        // Jika gagal, tampilkan error
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    // Jika ID tidak disediakan, redirect kembali ke halaman daftar jadwal praktikum
    header("Location: jadwal_praktikum.php?status=error_no_id");
    exit();
}

$conn->close(); // Tutup koneksi database
?> 