<?php
require_once 'db_connect.php';
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
if($role !== 'admin') {
    header('Location: laboran.php?error=akses');
    exit();
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_user = $_GET['id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get username from users table using id to delete from tb_laboran_details
        $stmt_get_username = $conn->prepare("SELECT username FROM users WHERE id = ?");
        $stmt_get_username->bind_param("i", $id_user);
        $stmt_get_username->execute();
        $result_username = $stmt_get_username->get_result();
        $user_row = $result_username->fetch_assoc();
        $username = $user_row['username'];
        $stmt_get_username->close();

        // Delete from tb_laboran_details first (due to foreign key constraint)
        $stmt_details = $conn->prepare("DELETE FROM tb_laboran_details WHERE username = ?");
        $stmt_details->bind_param("s", $username);
        $stmt_details->execute();
        $stmt_details->close();

        // Delete from users table
        $stmt_users = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt_users->bind_param("i", $id_user);
        $stmt_users->execute();
        $stmt_users->close();

        // Commit transaction
        $conn->commit();
        
        // Redirect back to laboran.php with success message
        header("Location: laboran.php?status=success_delete");
        exit();

    } catch (mysqli_sql_exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        
        // Generic error for other issues
        header("Location: laboran.php?status=error&message=" . urlencode($e->getMessage()));
        exit();

    } finally {
        $conn->close();
    }
} else {
    // If id is not provided or empty
    header("Location: laboran.php?status=error_no_id");
    exit();
}
?> 