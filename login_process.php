<?php
require_once 'db_connect.php'; // Sertakan file koneksi database

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get username and password from the form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Melindungi dari SQL injection
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    // Query untuk mencari user di database (ambil hash password dan role)
    $sql = "SELECT password, role FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];
        $role = $row['role'];

        // Verifikasi password: hash (baru) atau plaintext (lama)
        if (password_verify($_POST['password'], $hashed_password) || $_POST['password'] === $hashed_password) {
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
                // Jika role tidak dikenali
                header("Location: index.html?error=unknown_role");
                exit();
            }
        } else {
            // Password salah
            header("Location: index.html?error=invalid_credentials");
            exit();
        }
    } else {
        // User tidak ditemukan
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