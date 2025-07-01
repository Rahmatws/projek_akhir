<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
if($role === 'kepala') {
    header('Location: asisten_praktikum.php?error=akses');
    exit();
}
// ... existing code ... 