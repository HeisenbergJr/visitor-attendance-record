<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Attendance Form</title>
    <link rel="stylesheet" href="admin_style.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        
        .form-title {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 15px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        .button-group {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-top: 25px;
        }
        
        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        
        @media (max-width: 768px) {
            .button-group {
                flex-direction: column;
            }
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
            <h2>Visitor Attendance Form</h2>
            
            <?php
            // Display success message if available
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

        <div class="form-container">
            <h3 class="form-title">Visitor Time In</h3>
            
            <form action="submit_attendance.php" method="POST">
                <div class="form-group">
                    <label for="full_name">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>
                
                <div class="form-group">
                    <label for="contact">Contact:</label>
                    <input type="text" id="contact" name="contact" required>
                </div>
                
                <div class="form-group">
                    <label for="whom_visiting">Whom Visiting:</label>
                    <input type="text" id="whom_visiting" name="whom_visiting" required>
                </div>
                
                <div class="form-group">
                    <label for="department">Department:</label>
                    <select id="department" name="department" required>
                        <option value="">Select Department</option>
                        <option value="Human Resources">Human Resources</option>
                        <option value="Finance">Finance</option>
                        <option value="IT">IT</option>
                        <option value="Administration">Administration</option>
                        <option value="Security">Security</option>
                        <option value="Management">Management</option>
                        <option value="Dispatch">Dispatch</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="time_in">Time In:</label>
                    <input type="datetime-local" id="time_in" name="time_in" required>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn btn-primary">Submit Time In</button>
                    <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Set default value for time_in to current datetime
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            // Format to YYYY-MM-DDTHH:MM (datetime-local format)
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            const currentDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
            document.getElementById('time_in').value = currentDateTime;
        });
    </script>
</body>
</html>