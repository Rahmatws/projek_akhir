<?php
require_once 'db_connect.php'; // Sertakan file koneksi database

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get username and password from the form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Melindungi dari SQL injection (meskipun untuk latihan ini kita tidak pakai prepared statements dulu)
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    // Query untuk mencari user di database
    $sql = "SELECT role FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User ditemukan, ambil peran (role)
        $row = $result->fetch_assoc();
        $role = $row['role'];

        // Arahkan ke dashboard yang sesuai berdasarkan peran
        if ($role === "admin") {
            header("Location: dashboard.html");
            exit();
        } elseif ($role === "kepala") {
            header("Location: kepala_lab_dashboard.html");
            exit();
        } elseif ($role === "laboran") {
            header("Location: laboran_dashboard.html");
            exit();
        } else {
            // Jika role tidak dikenali (ini seharusnya tidak terjadi jika data database benar)
            header("Location: index.html?error=unknown_role");
            exit();
        }
    } else {
        // User tidak ditemukan atau password salah
        header("Location: index.html?error=invalid_credentials");
        exit();
    }
} else {
    // If accessed directly without POST method, redirect to login page
    header("Location: index.html");
    exit();
}

$conn->close(); // Tutup koneksi database setelah selesai
?> 