<?php
session_start();
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate passwords
    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Check if the email or username already exists
    $checkQuery = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $checkQuery->bind_param("ss", $email, $username);
    $checkQuery->execute();
    $result = $checkQuery->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Username or email already exists.'); window.history.back();</script>";
        exit;
    }

    $checkQuery->close();

    // Insert user into the database with "pending" status
    $insertQuery = $conn->prepare("INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, 'user', 'pending')");
    $insertQuery->bind_param("sss", $username, $email, $hashedPassword);

    if ($insertQuery->execute()) {
        echo "<script>alert('Registration successful! Wait for admin approval.'); window.location.href = 'login.php';</script>";
    } else {
        echo "<script>alert('Error during registration.'); window.history.back();</script>";
    }

    $insertQuery->close();
}

$conn->close();
?>
