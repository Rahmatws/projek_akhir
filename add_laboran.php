<?php
require_once 'db_connect.php';
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
if($role !== 'admin') {
    header('Location: laboran.php?error=akses');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama = $_POST['nama'];
    $gender = $_POST['gender'];
    $alamat = $_POST['alamat'];
    $role = $_POST['role'];
    $hp = $_POST['hp'];

    // Hash password sebelum menyimpan ke database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $foto_name = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = 'uploads/laboran/';
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $foto_name = uniqid() . '_' . basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], $target_dir . $foto_name);
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into users table
        $stmt_users = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt_users->bind_param("sss", $username, $hashed_password, $role);
        $stmt_users->execute();

        // Insert into tb_laboran_details table
        $stmt_details = $conn->prepare("INSERT INTO tb_laboran_details (username, nama, gender, alamat, hp, foto) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_details->bind_param("ssssss", $username, $nama, $gender, $alamat, $hp, $foto_name);
        $stmt_details->execute();

        // Commit transaction
        $conn->commit();
        
        // Redirect back to laboran.php with success message
        header("Location: laboran.php?status=success_add");
        exit();

    } catch (mysqli_sql_exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        
        // Handle specific error for duplicate username
        if ($e->getCode() == 1062) { // MySQL error code for duplicate entry
            header("Location: laboran.php?status=error_duplicate_username");
        } else {
            // Generic error for other issues
            header("Location: laboran.php?status=error&message=" . urlencode($e->getMessage()));
        }
        exit();

    } finally {
        if (isset($stmt_users)) {
            $stmt_users->close();
        }
        if (isset($stmt_details)) {
            $stmt_details->close();
        }
        $conn->close();
    }
} else {
    // If accessed directly without POST method
    header("Location: laboran.php");
    exit();
}
?> 