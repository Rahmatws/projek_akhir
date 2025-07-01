<?php
require_once 'db_connect.php';
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','laboran'])) {
    header('Location: kelas.php?error=akses');
    exit();
}

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);

    $sql = "DELETE FROM kelas WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        $status = 'success';
        $message = 'Data kelas berhasil dihapus.';
    } else {
        $status = 'error';
        $message = 'Error: ' . $conn->error;
    }
    
    $conn->close();

    // Redirect back to kelas.php with status message
    header('Location: kelas.php?status=' . $status . '&message=' . urlencode($message));
    exit();
}
?> 