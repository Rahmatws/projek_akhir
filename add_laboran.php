<?php
require_once 'db_connect.php';

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

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into users table
        $stmt_users = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt_users->bind_param("sss", $username, $hashed_password, $role);
        $stmt_users->execute();

        // Insert into tb_laboran_details table
        $stmt_details = $conn->prepare("INSERT INTO tb_laboran_details (username, nama, gender, alamat, hp) VALUES (?, ?, ?, ?, ?)");
        $stmt_details->bind_param("sssss", $username, $nama, $gender, $alamat, $hp);
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