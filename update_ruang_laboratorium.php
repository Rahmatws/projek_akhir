<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nama_ruang = $_POST['nama_ruang'];
    $lokasi = $_POST['lokasi'];

    $sql = "UPDATE ruang_laboratorium SET nama_ruang = ?, lokasi = ? WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssi", $nama_ruang, $lokasi, $id);
        
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