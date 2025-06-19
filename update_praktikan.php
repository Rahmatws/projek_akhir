<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $original_nims = $_POST['original_nim'];
    $nims = $_POST['nim'];
    $names = $_POST['nama'];
    $addresses = $_POST['alamat'];
    $birth_dates = $_POST['tgl_lahir'];
    $prodis = $_POST['prodi'];
    
    $success = true;
    $conn->begin_transaction();
    
    try {
        for ($i = 0; $i < count($original_nims); $i++) {
            $sql = "UPDATE praktikan SET 
                    nim = ?,
                    nama_lengkap = ?,
                    alamat = ?,
                    tgl_lahir = ?,
                    prodi = ?
                    WHERE nim = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", 
                $nims[$i],
                $names[$i],
                $addresses[$i],
                $birth_dates[$i],
                $prodis[$i],
                $original_nims[$i]
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Error updating record: " . $conn->error);
            }
        }
        
        $conn->commit();
        header("Location: praktikan.php?message=success");
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