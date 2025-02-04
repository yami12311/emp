<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    var_dump($_POST); // Debugging: See if data is submitted

    // Trim inputs
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = $_POST['role'];

    // Validate input
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: create_user.php");
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: create_user.php");
        exit;
    }

    if (!in_array($role, ['user', 'admin'])) {
        $_SESSION['error'] = "Invalid role.";
        header("Location: create_user.php");
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Check if username already exists
        $query = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $query->bind_param("s", $username);
        $query->execute();
        $query->store_result();

        if ($query->num_rows > 0) {
            $_SESSION['error'] = "Username already exists.";
        } else {
            // Insert new user into database
            $insert_query = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $insert_query->bind_param("sss", $username, $hashed_password, $role);

            if ($insert_query->execute()) {
                $_SESSION['success'] = "User created successfully!";
            } else {
                $_SESSION['error'] = "Database error: " . $conn->error;
            }
        }
    } catch (mysqli_sql_exception $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

    header("Location: create_user.php");
    exit;
}

// Debug session variables
var_dump($_SESSION);
?>
