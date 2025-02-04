<?php
session_start();
include 'db_config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Handle deletion request
if (isset($_GET['delete_id'])) {
    $employee_id = $_GET['delete_id'];

    // Validate the employee ID
    if (!is_numeric($employee_id)) {
        $error = "Invalid employee ID.";
    } else {
        // Prepare the delete query with a placeholder
        $query = $conn->prepare("DELETE FROM employees WHERE id = ?");
        // Bind the parameter (integer)
        $query->bind_param("i", $employee_id);

        // Execute the query
        if ($query->execute()) {
            // Redirect to manage employee page with success message
            header("Location: manage_employee.php?success=Employee deleted successfully!");
            exit;
        } else {
            // Log error and set error message
            $error = "Error deleting employee: " . $conn->error;
            error_log("Delete failed: " . $conn->error); // Log error message for debugging
            error_log("Failed SQL Query: DELETE FROM employees WHERE id = $employee_id"); // Log query for debugging
        }
    }
}

// If no delete_id, redirect back to manage employee page
header("Location: manage_employee.php");
exit;
?>
