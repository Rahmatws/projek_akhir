<?php
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