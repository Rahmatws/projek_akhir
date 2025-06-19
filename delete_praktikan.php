<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selected_ids'])) {
    $nims = $_POST['selected_ids'];
    $success = true;
    $conn->begin_transaction();
    try {
        foreach ($nims as $nim) {
            $sql = "DELETE FROM praktikan WHERE nim = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $nim);
            if (!$stmt->execute()) {
                throw new Exception("Error deleting record: " . $conn->error);
            }
        }
        $conn->commit();
        header("Location: praktikan.php?message=success_delete");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: praktikan.php?message=error&error=" . urlencode($e->getMessage()));
        exit();
    }
}
header("Location: praktikan.php");
exit();
?> 