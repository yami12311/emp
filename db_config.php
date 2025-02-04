<?php
$servername = "localhost"; // Server name
$username = "root";        // Default username for XAMPP
$password = "";            // Default password for XAMPP (empty)
$dbname = "project_emp"; // Replace with your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
