<?php
require_once 'db_connect.php';
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
if($role !== 'admin') {
    header('Location: ruang_laboratorium.php?error=akses');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    $sql = "DELETE FROM ruang_laboratorium WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        
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