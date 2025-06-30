<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_ruang = $_POST['nama_ruang'];
    $lokasi = $_POST['lokasi'];

    $sql = "INSERT INTO ruang_laboratorium (nama_ruang, lokasi) VALUES (?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $nama_ruang, $lokasi);
        
        if ($stmt->execute()) {
            // Redirect kembali ke halaman daftar setelah berhasil
            header("location: ruang_laboratorium.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
    $conn->close();
}
?> 