<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
if($role === 'kepala') {
    header('Location: mata_praktikum.php?error=akses');
    exit();
}
include 'db_connect.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kode_matkul'])) {
    $kode = $_POST['kode_matkul'];
    $stmt = $conn->prepare("DELETE FROM mata_praktikum WHERE kode_matkul = ?");
    $stmt->bind_param("s", $kode);
    if ($stmt->execute()) {
        echo json_encode(['success'=>true]);
        exit();
    }
    echo json_encode(['success'=>false]);
    exit();
}
echo json_encode(['success'=>false]);
exit(); 