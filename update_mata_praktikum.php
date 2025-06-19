<?php
include 'db_connect.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_lama = $_POST['kode_lama'] ?? '';
    $kode = trim($_POST['edit_kode_matkul'] ?? '');
    $nama = trim($_POST['edit_nama_matkul'] ?? '');
    $sks = intval($_POST['edit_sks'] ?? 0);
    $semester = intval($_POST['edit_semester'] ?? 0);
    if ($kode && $nama && $sks && $semester && $kode_lama) {
        $stmt = $conn->prepare("UPDATE mata_praktikum SET kode_matkul=?, nama_matkul=?, sks=?, semester=? WHERE kode_matkul=?");
        $stmt->bind_param("ssiis", $kode, $nama, $sks, $semester, $kode_lama);
        if ($stmt->execute()) {
            echo json_encode(['success'=>true]);
            exit();
        }
    }
    echo json_encode(['success'=>false]);
    exit();
}
echo json_encode(['success'=>false]);
exit(); 