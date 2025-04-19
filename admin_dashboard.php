<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminstrator.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="adminstrator.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .dashboard-menu {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .nav-links {
            margin: 20px 0;
        }
        .nav-links a {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }
        .nav-links a:hover {
            background: #2980b9;
        }
        .welcome-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="welcome-section">
            <h1>Welcome, <?php echo $_SESSION['admin_name']; ?></h1>
            <p>Manage your volunteers from this dashboard.</p>
        </div>
        
        <div class="dashboard-menu">
            <h2>Admin Controls</h2>
            <div class="nav-links">
                <a href="manage_volunteers.php">Manage Volunteers</a>
                <a href="admin_logout.php">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
