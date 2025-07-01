<?php
require_once 'db_connect.php';
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
if($role !== 'admin') {
    header('Location: ruang_laboratorium.php?error=akses');
    exit();
}

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