<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['volunteer_id'])) {
    header('Location: volunteer.html');
    exit();
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $issue_id = $_POST['issue_id'];
    $new_status = $_POST['new_status'];
    $remarks = $_POST['remarks'];
    
    $sql = "UPDATE issues SET status = ?, assigned_volunteer_id = ?, remarks = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisi", $new_status, $_SESSION['volunteer_id'], $remarks, $issue_id);
    $stmt->execute();
}

// Get statistics
$stats_sql = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved
FROM issues WHERE assigned_volunteer_id = ?";
$stats_stmt = $conn->prepare($stats_sql);
$stats_stmt->bind_param("i", $_SESSION['volunteer_id']);
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc();

// Fetch issues
$sql = "SELECT * FROM issues ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f6fa;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .welcome-banner {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .issue-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .issue-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-in_progress { background: #cce5ff; color: #004085; }
        .status-resolved { background: #d4edda; color: #155724; }
        .issue-content {
            margin-bottom: 15px;
        }
        .issue-image {
            max-width: 200px;
            border-radius: 8px;
            margin-top: 10px;
            cursor: pointer;
        }
        .update-form {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        select, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #3498db;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #2980b9;
        }
        .home-link {
            display: inline-block;
            padding: 10px 20px;
            background: #2ecc71;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .date-info {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.html" class="home-link">Home</a>

        <div class="welcome-banner">
            <h1>Welcome, <?php echo $_SESSION['volunteer_name']; ?></h1>
            <p>View and manage reported issues below</p>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="issue-card">
                    <div class="issue-header">
                        <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                        <span class="status-badge status-<?php echo $row['status']; ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </span>
                    </div>
                    
                    <div class="issue-content">
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($row['address']); ?></p>
                        <p><strong>Issue:</strong> <?php echo htmlspecialchars($row['issue_description']); ?></p>
                        
                        <?php if($row['image_path']): ?>
                            <img src="<?php echo htmlspecialchars($row['image_path']); ?>" 
                                 class="issue-image"
                                 onclick="window.open(this.src)" 
                                 alt="Issue Image">
                        <?php endif; ?>
                        
                        <p class="date-info">Posted on: <?php echo date('F j, Y, g:i a', strtotime($row['created_at'])); ?></p>
                    </div>

                    <form class="update-form" method="POST">
                        <input type="hidden" name="issue_id" value="<?php echo $row['id']; ?>">
                        <select name="new_status">
                            <option value="pending" <?php echo $row['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="in_progress" <?php echo $row['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                            <option value="resolved" <?php echo $row['status'] == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                        </select>
                        <textarea name="remarks" placeholder="Add your remarks here"><?php echo htmlspecialchars($row['remarks'] ?? ''); ?></textarea>
                        <button type="submit" name="update_status">Update Status</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="issue-card">
                <p>No issues reported yet.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
