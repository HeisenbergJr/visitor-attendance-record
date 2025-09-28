<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
require_once 'db_connection.php';

// Initialize variables for search
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Fetch visitor data grouped by date and visitor
$sql = "SELECT DATE(time_in) as visit_date, full_name, COUNT(*) as visit_count 
        FROM visitors 
        WHERE 1";

// Add date range filter if provided
if (!empty($start_date) && !empty($end_date)) {
    $sql .= " AND DATE(time_in) BETWEEN '$start_date' AND '$end_date'";
}

$sql .= " GROUP BY visit_date, full_name ORDER BY visit_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="admin_style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .sidebar {
            width: 250px;
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            box-sizing: border-box;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: rgba(255,255,255,0.2);
        }
        .content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        .header {
            background: #fff;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 12px;
        }
        table th {
            background-color: #007bff;
            color: white;
            text-transform: uppercase;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #f5f5f5;
        }
        .search-container {
            display: flex;
            margin-bottom: 20px;
            gap: 10px;
        }
        .search-container input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .search-container button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Menu</h2>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="attendance_form.php">New Attendance</a>
        <a href="update_time_out.php">Update Time Out</a>
        <a href="view_visitors.php">Visitor Reports</a>
        <a href="report.php">General Report</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="header">
            <h1>General Report</h1>
            
            <!-- Search by Date Range -->
            <form method="GET" action="">
                <div class="search-container">
                    <input type="date" name="start_date" value ="<?php echo htmlspecialchars($start_date); ?>" placeholder="Start Date" required>
                    <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" placeholder="End Date" required>
                    <button type="submit">Search</button>
                </div>
            </form>
        </div>

        <!-- Visitor Data Table -->
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Visitor Name</th>
                    <th>Visit Count</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['visit_date']) . "</td>
                                <td>" . htmlspecialchars($row['full_name']) . "</td>
                                <td>" . htmlspecialchars($row['visit_count']) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No visitors found for the selected date range.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>