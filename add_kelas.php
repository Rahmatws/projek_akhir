<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kelas = $conn->real_escape_string($_POST['nama_kelas']);
    $semester = $conn->real_escape_string($_POST['semester']);

    // Check if the combination of nama_kelas and semester already exists
    $check_sql = "SELECT COUNT(*) AS count FROM kelas WHERE nama_kelas = '$nama_kelas' AND semester = '$semester'";
    $check_result = $conn->query($check_sql);
    $row = $check_result->fetch_assoc();

    if ($row['count'] > 0) {
        $status = 'error';
        $message = 'Data kelas dengan nama dan semester yang sama sudah ada.';
    } else {
        $sql = "INSERT INTO kelas (nama_kelas, semester) VALUES ('$nama_kelas', '$semester')";

        if ($conn->query($sql) === TRUE) {
            $status = 'success';
            $message = 'Data kelas berhasil ditambahkan.';
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