<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
if($role === 'kepala') {
    header('Location: mata_praktikum.php?error=akses');
    exit();
}
include 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = trim($_POST['kode_matkul']);
    $nama = trim($_POST['nama_matkul']);
    $sks = intval($_POST['sks']);
    $semester = intval($_POST['semester']);
    if ($kode && $nama && $sks && $semester) {
        $stmt = $conn->prepare("INSERT INTO mata_praktikum (kode_matkul, nama_matkul, sks, semester) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $kode, $nama, $sks, $semester);
        if ($stmt->execute()) {
            header('Location: mata_praktikum.php?message=success');
            exit();
        } else {
            header('Location: mata_praktikum.php?message=error');
            exit();
        }
    } else {
        header('Location: mata_praktikum.php?message=error');
        exit();
    }
} else {
    header('Location: mata_praktikum.php');
    exit();
} 