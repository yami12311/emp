<?php
session_start();
include 'db_config.php';

// Check if user is an admin and a POST request is received
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin' || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: login.php");
    exit;
}

if (isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']); // Sanitize user input

    // Corrected SQL query
    $update_query = "UPDATE users SET status = 'active', role = 'employee' WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        // Check if corresponding employee record exists
        $check_employee_query = "SELECT id FROM user WHERE user_id = ?";
        $stmt2 = $conn->prepare($check_employee_query);
        $stmt2->bind_param("i", $user_id);
        $stmt2->execute();
        $stmt2->store_result();

        if ($stmt2->num_rows == 0) {
            // Insert default employee record if not exists
            $insert_employee_query = "INSERT INTO user (user_id, department, position, date_joined, salary) VALUES (?, 'Unknown', 'Unknown', NOW(), 0)";
            $stmt3 = $conn->prepare($insert_employee_query);
            $stmt3->bind_param("i", $user_id);
            $stmt3->execute();
            $stmt3->close();
        }
        $stmt2->close();
        echo "User approved successfully.";
    } else {
        echo "Error updating user status: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
