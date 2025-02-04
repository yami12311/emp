<?php
session_start();
include 'db_config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Generate a unique form token
if (empty($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}
$form_token = $_SESSION['form_token'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check for form token validity
    if (!isset($_POST['form_token']) || $_POST['form_token'] !== $_SESSION['form_token']) {
        $error = "Invalid form submission. Please try again.";
    } else {
        // Invalidate the token to prevent reuse
        unset($_SESSION['form_token']);
        
        // Collect and validate input
        $name = trim($_POST['name']);
        $department = trim($_POST['department']);
        $position = trim($_POST['position']);
        $salary = trim($_POST['salary']);
        $date_joined= $_POST['date_joined'];

        if (empty($name) || empty($department) || empty($position) || empty($salary) || empty($date_joined)) {
            $error = "All fields are required.";
        } else {
            // Insert employee into the database
            $query = $conn->prepare("INSERT INTO employees (name, department, position, salary, date_joined) VALUES (?, ?, ?, ?, ?)");
            $query->bind_param("sssds", $name, $department, $position, $salary, $date_joined);

            if ($query->execute()) {
                $success = "Employee added successfully!";
            } else {
                $error = "Error adding employee: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
    <link rel="stylesheet" href="add_employee.css">
</head>
<body>
    <div class="form-container">
        <h2>Add New Employee</h2>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <?php if (isset($success)) { echo "<p class='success'>$success</p>"; } ?>
        <form action="add_employee.php" method="POST">
            <input type="hidden" name="form_token" value="<?php echo $form_token; ?>">

            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required>

            <label for="department">Department:</label>
            <input type="text" name="department" id="department" required>

            <label for="position">Position:</label>
            <input type="text" name="position" id="position" required>

            <label for="salary">Salary:</label>
            <input type="number" name="salary" id="salary" required>
            
            <label for="date">Join Date:</label>
            <input type="date" name="date_joined" id="date_joined" required>

            <button type="submit">Add Employee</button>
        </form>
        <a href="manage_employee.php" class="back-link">Back to Manage Employees</a>
    </div>

    <script>
        // Disable submit button after form submission
        document.querySelector('form').addEventListener('submit', function (event) {
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Submitting...';
        });
    </script>
</body>
</html>
