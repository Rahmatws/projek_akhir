<?php
require_once 'db_connect.php';

// Memastikan request adalah POST dan ID ada
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    
    $id = intval($_POST['id']);

    // Siapkan statement untuk keamanan
    $sql = "DELETE FROM jadwal_praktikum WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Bind ID ke parameter
        $stmt->bind_param("i", $id);
        
        // Eksekusi statement
        if ($stmt->execute()) {
            // Jika berhasil, kirim pengguna kembali ke halaman daftar jadwal
            echo "<script>
                    alert('Jadwal berhasil dihapus!');
                    window.location.href = 'jadwal_praktikum.php';
                  </script>";
            exit();
        } else {
            // Jika eksekusi gagal
            echo "Error saat menghapus data: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Jika persiapan statement gagal
        echo "Error: " . $conn->error;
    }
    
    $conn->close();

} else {
    // Jika akses langsung atau tanpa ID, redirect ke halaman utama
    header("Location: index.html?error=invalid_request");
    exit();
}
?> 