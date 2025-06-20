<?php
require_once 'db_connect.php'; // Sertakan file koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form dan sanitasi
    $id = $conn->real_escape_string($_POST['id']);
    $tahun_ajaran = $conn->real_escape_string($_POST['tahun_ajaran']);
    $nama_mata_kuliah = $conn->real_escape_string($_POST['nama_mata_kuliah']);
    $asisten_praktikum = $conn->real_escape_string($_POST['asisten_praktikum']);
    $ruang_lab = $conn->real_escape_string($_POST['ruang_lab']);
    $kelas = $conn->real_escape_string($_POST['kelas']);
    $hari = $conn->real_escape_string($_POST['hari']);
    $waktu_mulai = $conn->real_escape_string($_POST['waktu_mulai']);
    $waktu_selesai = $conn->real_escape_string($_POST['waktu_selesai']);
    $waktu = $waktu_mulai . ' - ' . $waktu_selesai;

    // Cek bentrok jadwal (ruang, laboran, atau kelas pada hari dan waktu yang sama, kecuali id yang sedang diedit)
    $sql_bentrok = "SELECT * FROM jadwal_praktikum WHERE id != '$id' AND hari = '$hari' AND ((ruang_lab = '$ruang_lab') OR (asisten_praktikum = '$asisten_praktikum') OR (kelas = '$kelas')) AND ((waktu_mulai < '$waktu_selesai' AND waktu_selesai > '$waktu_mulai'))";
    $result_bentrok = $conn->query($sql_bentrok);
    if ($result_bentrok && $result_bentrok->num_rows > 0) {
        // Jadwal bentrok
        echo "<script>alert('Jadwal bentrok! Ruang, laboran, atau kelas sudah terpakai pada hari dan jam tersebut.'); window.location.href='jadwal_praktikum.php?status=bentrok';</script>";
        $conn->close();
        exit();
    }

    // Query untuk mengupdate data di database
    $sql = "UPDATE jadwal_praktikum SET 
            tahun_ajaran = '$tahun_ajaran',
            nama_mata_kuliah = '$nama_mata_kuliah',
            asisten_praktikum = '$asisten_praktikum',
            ruang_lab = '$ruang_lab',
            kelas = '$kelas',
            hari = '$hari',
            waktu_mulai = '$waktu_mulai',
            waktu_selesai = '$waktu_selesai'
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        // Catat ke perubahan_jadwal
        $petugas = isset($_SESSION['username']) ? $_SESSION['username'] : 'Petugas';
        $tanggal_ubah = date('Y-m-d');
        $sql2 = "INSERT INTO perubahan_jadwal (tanggal_ubah, petugas, askum, kelas, matkul, hari, waktu) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("sssssss", $tanggal_ubah, $petugas, $asisten_praktikum, $kelas, $nama_mata_kuliah, $hari, $waktu);
        $stmt2->execute();
        // Jika berhasil, redirect kembali ke halaman daftar jadwal praktikum
        header("Location: jadwal_praktikum.php?status=success_update");
        exit();
    } else {
        // Jika gagal, tampilkan error
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close(); // Tutup koneksi database
?> 