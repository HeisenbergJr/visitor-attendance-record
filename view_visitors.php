<?php
// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "visitor_attendance";

$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all visitor records
$sql = "SELECT * FROM visitors ORDER BY time_in DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="admin_style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Visitors</title>
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
        }
        .search-container input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
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
            <h1>Visitors Report</h1>
            
            <!-- Optional: Search functionality -->
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search visitors...">
            </div>
        </div>

        <?php
        // Display success message if redirected from update_time_out.php
        if (isset($_GET['success']) && $_GET['success'] == 1) {
            echo "<p style='color: green; font-weight: bold;'>Time Out updated successfully!</p>";
        }
        ?>

        <table id="visitorsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Contact</th>
                    <th>Visiting</th>
                    <th>Department</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['full_name']}</td>
                                <td>{$row['contact']}</td>
                                <td>{$row['whom_visiting']}</td>
                                <td>{$row['department']}</td>
                                <td>{$row['time_in']}</td>
                                <td>" . ($row['time_out'] ? $row['time_out'] : "N/A") . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No visitors found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Optional: Simple Search Functionality -->
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toUpperCase();
            let table = document.getElementById('visitorsTable');
            let tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                let display = false;
                let td = tr[i].getElementsByTagName('td');
                
                for (let j = 0; j < td.length; j++) {
                    if (td[j]) {
                        let txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            display = true;
                            break;
                        }
                    }
                }
                
                tr[i].style.display = display ? "" : "none";
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>