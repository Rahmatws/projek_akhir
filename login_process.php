<?php
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get username and password from the form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Define the correct username and password
    $correct_username = "admin";
    $correct_password = "unibba2025";

    // Validate the credentials
    if ($username === $correct_username && $password === $correct_password) {
        // If credentials are correct, redirect to the dashboard
        header("Location: dashboard.html");
        exit(); // Stop further script execution
    } else {
        // If credentials are incorrect, redirect back to the login page (or show an error)
        // For simplicity, this example redirects back with an error parameter
        header("Location: index.html?error=invalid_credentials");
        exit();
    }
} else {
    // If accessed directly without POST method, redirect to login page
    header("Location: index.html");
    exit();
}
?> 