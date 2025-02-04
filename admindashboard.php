<?php
session_start();
include 'db_config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch analytics data
$totalEmployees = $conn->query("SELECT COUNT(*) AS count FROM employees")->fetch_assoc()['count'] ?? 0;
$totalDepartments = $conn->query("SELECT COUNT(DISTINCT department) AS count FROM employees")->fetch_assoc()['count'] ?? 0;
$totalPositions = $conn->query("SELECT COUNT(DISTINCT position) AS count FROM employees")->fetch_assoc()['count'] ?? 0;

// Fetch pending users
$pendingUsers = $conn->query("SELECT id, username FROM users WHERE status = 'pending' AND role != 'admin'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admindashboard.css">
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
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <div class="analytics">
                <div class="card">
                    <h2>Total Employees</h2>
                    <p><?php echo $totalEmployees; ?></p>
                </div>
                <div class="card">
                    <h2>Departments</h2>
                    <p><?php echo $totalDepartments; ?></p>
                </div>
                <div class="card">
                    <h2>Positions</h2>
                    <p><?php echo $totalPositions; ?></p>
                </div>
            </div>

            <!-- Pending Users Section -->
            <div class="pending-users">
                <h2>Pending User Approvals</h2>
                <?php if ($pendingUsers->num_rows > 0): ?>
                    <ul>
                        <?php while ($user = $pendingUsers->fetch_assoc()): ?>
                            <li>
                                <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                <form action="approve_user.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit">Approve</button>
                                </form>
                                <form action="reject_user.php" method="POST" style="display:inline;">
    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
    <input type="text" name="rejected_reason" placeholder="Reason for rejection" required>
    <button type="submit" style="color: red;">Reject</button>
</form>

                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No pending users for approval.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
