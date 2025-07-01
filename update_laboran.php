<?php
require_once 'db_connect.php';
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
if($role !== 'admin') {
    header('Location: laboran.php?error=akses');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = $_POST['id']; // ID dari tabel users
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama = $_POST['nama'];
    $gender = $_POST['gender'];
    $alamat = $_POST['alamat'];
    $role = $_POST['role'];
    $hp = $_POST['hp'];
    $foto_name = null;

    if (isset($_FILES['editFoto']) && $_FILES['editFoto']['error'] == 0) {
        $target_dir = 'uploads/laboran/';
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $foto_name = uniqid() . '_' . basename($_FILES['editFoto']['name']);
        move_uploaded_file($_FILES['editFoto']['tmp_name'], $target_dir . $foto_name);
        // Update kolom foto di tb_laboran_details
        $stmt_foto = $conn->prepare("UPDATE tb_laboran_details SET foto = ? WHERE username = ?");
        $stmt_foto->bind_param("ss", $foto_name, $username);
        $stmt_foto->execute();
        $stmt_foto->close();
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update users table
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt_users = $conn->prepare("UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?");
            $stmt_users->bind_param("sssi", $username, $hashed_password, $role, $id_user);
        } else {
            $stmt_users = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
            $stmt_users->bind_param("ssi", $username, $role, $id_user);
        }
        $stmt_users->execute();

        // Update tb_laboran_details table
        $stmt_details = $conn->prepare("UPDATE tb_laboran_details SET nama = ?, gender = ?, alamat = ?, hp = ? WHERE username = ?");
        $stmt_details->bind_param("sssss", $nama, $gender, $alamat, $hp, $username);
        $stmt_details->execute();

        // Commit transaction
        $conn->commit();
        
        // Redirect back to laboran.php with success message
        header("Location: laboran.php?status=success_update");
        exit();

    } catch (mysqli_sql_exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        
        // Handle specific error for duplicate username (if username is also unique in users table)
        if ($e->getCode() == 1062) { // MySQL error code for duplicate entry
            header("Location: laboran.php?status=error_duplicate_username_update");
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