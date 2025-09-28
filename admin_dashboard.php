<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "visitor_attendance";

$conn = mysqli_connect($host, $username, $password, $database);

// Fetch data for the bar chart
$days_query = "SELECT DAYNAME(time_in) as day, COUNT(*) as count 
FROM visitors 
GROUP BY DAYNAME(time_in) 
ORDER BY FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
$days_result = mysqli_query($conn, $days_query);

$days = [];
$visitor_counts = [];

while ($row = mysqli_fetch_assoc($days_result)) {
    $days[] = $row['day'];
    $visitor_counts[] = $row['count'];
}

// Fetch data for different grouping options
$days_data_query = "SELECT DATE(time_in) as date, COUNT(*) as count 
                   FROM visitors 
                   GROUP BY DATE(time_in) 
                   ORDER BY DATE(time_in) DESC 
                   LIMIT 7";
$weeks_query = "SELECT CONCAT('Week ', WEEK(time_in)) as week, COUNT(*) as count 
                FROM visitors 
                GROUP BY WEEK(time_in) 
                ORDER BY WEEK(time_in) DESC 
                LIMIT 8";
$months_query = "SELECT DATE_FORMAT(time_in, '%M %Y') as month, COUNT(*) as count 
                 FROM visitors 
                 GROUP BY DATE_FORMAT(time_in, '%Y-%m') 
                 ORDER BY DATE_FORMAT(time_in, '%Y-%m') DESC 
                 LIMIT 6";
$years_query = "SELECT YEAR(time_in) as year, COUNT(*) as count 
                FROM visitors 
                GROUP BY YEAR(time_in) 
                ORDER BY YEAR(time_in) DESC 
                LIMIT 5";

$days_data_result = mysqli_query($conn, $days_data_query);
$weeks_result = mysqli_query($conn, $weeks_query);
$months_result = mysqli_query($conn, $months_query);
$years_result = mysqli_query($conn, $years_query);

$days_data = [];
$weeks_data = [];
$months_data = [];
$years_data = [];

$days_counts = [];
$weeks_counts = [];
$months_counts = [];
$years_counts = [];

while ($row = mysqli_fetch_assoc($days_data_result)) {
    $days_data[] = date('M j', strtotime($row['date']));
    $days_counts[] = $row['count'];
}

while ($row = mysqli_fetch_assoc($weeks_result)) {
    $weeks_data[] = $row['week'];
    $weeks_counts[] = $row['count'];
}

while ($row = mysqli_fetch_assoc($months_result)) {
    $months_data[] = $row['month'];
    $months_counts[] = $row['count'];
}

while ($row = mysqli_fetch_assoc($years_result)) {
    $years_data[] = $row['year'];
    $years_counts[] = $row['count'];
}

// Reverse the arrays to show in chronological order
$days_data = array_reverse($days_data);
$days_counts = array_reverse($days_counts);
$weeks_data = array_reverse($weeks_data);
$weeks_counts = array_reverse($weeks_counts);
$months_data = array_reverse($months_data);
$months_counts = array_reverse($months_counts);
$years_data = array_reverse($years_data);
$years_counts = array_reverse($years_counts);

// Total Visitors
$total_visitors_query = "SELECT COUNT(*) as total FROM visitors";
$total_visitors_result = mysqli_query($conn, $total_visitors_query);
$total_visitors = mysqli_fetch_assoc($total_visitors_result)['total'];

// Visitors Today
$today_visitors_query = "SELECT COUNT(*) as today FROM visitors WHERE DATE(time_in) = CURDATE()";
$today_visitors_result = mysqli_query($conn, $today_visitors_query);
$today_visitors = mysqli_fetch_assoc($today_visitors_result)['today'];

// Active Visitors (Not checked out)
$active_visitors_query = "SELECT COUNT(*) as active FROM visitors WHERE time_out IS NULL";
$active_visitors_result = mysqli_query($conn, $active_visitors_query);
$active_visitors = mysqli_fetch_assoc($active_visitors_result)['active'];

// Checked Out Visitors
$checked_out_query = "SELECT COUNT(*) as checked_out FROM visitors WHERE time_out IS NOT NULL";
$checked_out_result = mysqli_query($conn, $checked_out_query);
$checked_out_visitors = mysqli_fetch_assoc($checked_out_result)['checked_out'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="admin_style.css">
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
            <h2>Welcome, <?php echo $_SESSION["username"]; ?></h2>
            
            <?php
            // Display success message for different actions
            if (isset($_GET['success'])) {
                if ($_GET['success'] == 1) {
                    echo "<div class='success-message'>Visitor attendance recorded successfully!</div>";
                }
            }

            // Display error message if any
            if (isset($_GET['error'])) {
                if ($_GET['error'] == 1) {
                    echo "<div class='error-message'>Error recording visitor attendance. Please try again.</div>";
                }
            }
            ?>
        </div>

        <!-- Dashboard Statistics -->
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Visitors</h3>
                <p><?php echo $total_visitors; ?></p>
            </div>
            <div class="stat-card">
                <h3>Visitors Today</h3>
                <p><?php echo $today_visitors; ?></p>
            </div>
            <div class="stat-card">
                <h3>Active Visitors</h3>
                <p><?php echo $active_visitors; ?></p>
            </div>
            <div class="stat-card">
                <h3>Checked Out</h3>
                <p><?php echo $checked_out_visitors; ?></p>
            </div>
        </div>
        
        <!-- Bar Chart Section -->
        <div class="chart-container">
            <div class="chart-header">
                <h3>Visitor Statistics</h3>
                <select id="groupBy" onchange="updateChart()">
                    <option value="day">Last 7 Days</option>
                    <option value="week">By Week</option>
                    <option value="month">By Month</option>
                    <option value="year">By Year</option>
                </select>
            </div>
            <div class="chart-wrapper">
                <canvas id="visitorsChart"></canvas>
            </div>
        </div>
    </div>

    <script>
    // Pass PHP data to JavaScript
    const daysData = <?php echo json_encode($days_data); ?>;
    const weeksData = <?php echo json_encode($weeks_data); ?>;
    const monthsData = <?php echo json_encode($months_data); ?>;
    const yearsData = <?php echo json_encode($years_data); ?>;

    const daysCounts = <?php echo json_encode($days_counts); ?>;
    const weeksCounts = <?php echo json_encode($weeks_counts); ?>;
    const monthsCounts = <?php echo json_encode($months_counts); ?>;
    const yearsCounts = <?php echo json_encode($years_counts); ?>;
    </script>
    
    <script src="attendance.js"></script>
    <script>
    // Initialize the chart with the data
    document.addEventListener('DOMContentLoaded', function() {
        initAttendanceChart(daysData, weeksData, monthsData, yearsData, 
                           daysCounts, weeksCounts, monthsCounts, yearsCounts);
    });
    </script>
</body>
</html>