<?php
session_start();
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
            // Ambil nama dan foto dari tb_laboran_details
            $stmt = $conn->prepare("SELECT nama, foto FROM tb_laboran_details WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result_detail = $stmt->get_result();
            $data_detail = $result_detail->fetch_assoc();
            $stmt->close();

            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            $_SESSION['nama'] = isset($data_detail['nama']) ? $data_detail['nama'] : $username;
            $_SESSION['foto'] = isset($data_detail['foto']) ? $data_detail['foto'] : '';

            // Arahkan ke dashboard yang sesuai berdasarkan peran
            if ($role === "admin") {
                header("Location: dashboard.php");
                exit();
            } elseif ($role === "kepala") {
                header("Location: kepala_lab_dashboard.php");
                exit();
            } elseif ($role === "laboran") {
                header("Location: laboran_dashboard.php");
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