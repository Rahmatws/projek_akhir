<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nims = $_POST['nim'];
    $names = $_POST['nama'];
    $addresses = $_POST['alamat'];
    $birth_dates = $_POST['tgl_lahir'];
    $prodis = $_POST['prodi'];
    
    $success = true;
    $conn->begin_transaction();
    
    try {
        for ($i = 0; $i < count($nims); $i++) {
            // Validasi data tidak boleh kosong
            if (empty($nims[$i]) || empty($names[$i]) || empty($addresses[$i]) || empty($birth_dates[$i]) || empty($prodis[$i])) {
                throw new Exception("Semua field harus diisi!");
            }
            
            // Konversi tgl_lahir ke format YYYY-MM-DD jika perlu
            $date = str_replace('/', '-', $birth_dates[$i]);
            if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $date, $matches)) {
                $birth_dates[$i] = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            }
            
            // Cek apakah NIM sudah ada
            $check_sql = "SELECT nim FROM praktikan WHERE nim = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("s", $nims[$i]);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                throw new Exception("NIM " . $nims[$i] . " sudah terdaftar!");
            }
            
            // Insert data baru
            $sql = "INSERT INTO praktikan (nim, nama_lengkap, alamat, tgl_lahir, prodi) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", 
                $nims[$i],
                $names[$i],
                $addresses[$i],
                $birth_dates[$i],
                $prodis[$i]
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Error inserting record: " . $conn->error);
            }
        }
        
        $conn->commit();
        header("Location: praktikan.php?message=success_add");
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