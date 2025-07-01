<?php
require_once 'db_connect.php';
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','laboran'])) {
    header('Location: kelas.php?error=akses');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $conn->real_escape_string($_POST['id']);
    $nama_kelas = $conn->real_escape_string($_POST['nama_kelas']);
    $semester = $conn->real_escape_string($_POST['semester']);

    // Check if the combination of nama_kelas and semester already exists for another ID
    $check_sql = "SELECT COUNT(*) AS count FROM kelas WHERE nama_kelas = '$nama_kelas' AND semester = '$semester' AND id != '$id'";
    $check_result = $conn->query($check_sql);
    $row = $check_result->fetch_assoc();

    if ($row['count'] > 0) {
        $status = 'error';
        $message = 'Gagal memperbarui: Data kelas dengan nama dan semester yang sama sudah ada untuk kelas lain.';
    } else {
        $sql = "UPDATE kelas SET nama_kelas = '$nama_kelas', semester = '$semester' WHERE id = '$id'";

        if ($conn->query($sql) === TRUE) {
            $status = 'success';
            $message = 'Data kelas berhasil diperbarui.';
        } else {
            $status = 'error';
            $message = 'Error: ' . $sql . '\n' . $conn->error;
        }
    }
    
    $conn->close();

    // Redirect back to kelas.php with status message
    header('Location: kelas.php?status=' . $status . '&message=' . urlencode($message));
    exit();
}
?> 