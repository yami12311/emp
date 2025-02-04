<?php
session_start();
include 'db_config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch active employees with data from both tables
$sql = "
    SELECT 
        employees.id AS emp_id, 
        users.username, 
        users.email, 
        users.role, 
        users.status, 
        employees.department, 
        employees.position, 
        employees.date_joined, 
        employees.salary 
    FROM 
        employees 
    INNER JOIN 
        users 
    ON 
        employees.user_id = users.id 
    WHERE 
        users.status = 'active'";

$result = $conn->query($sql);

// Check for query errors
if (!$result) {
    die("Error fetching employee data: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Employees</title>
    <link rel="stylesheet" href="manage_employees.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h2>Admin Dashboard</h2>
            <ul>
                <li><a href="home.html">Home</a></li>
                <li><a href="manage_employee.php">Manage Employee</a></li>
                <li><a href="create_user.html">Create User</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h1>Manage Employees</h1>
            <table border="1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Date Joined</th>
                        <th>Salary</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['emp_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['role']); ?></td>


                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td><?php echo htmlspecialchars($row['department']); ?></td>
                                <td><?php echo htmlspecialchars($row['position']); ?></td>
                                <td><?php echo htmlspecialchars($row['date_joined']); ?></td>
                                <td><?php echo htmlspecialchars($row['salary']); ?></td>
                                <td>
                                    <a href="edit_employee.php?id=<?php echo $row['emp_id']; ?>">Edit</a> |
                                    <a href="delete_employee.php?id=<?php echo $row['emp_id']; ?>" onclick="return confirm('Are you sure you want to delete this employee?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10">No active employees found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
