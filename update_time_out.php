<?php
session_start();
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

// Variable to store visitor details
$visitor_name = "";
$visitor_found = false;

// Process visitor ID lookup
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    
    // Find visitor with no time out
    $sql = "SELECT full_name FROM visitors WHERE id = '$id' AND time_out IS NULL";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $visitor_name = $row['full_name'];
        $visitor_found = true;
    } else {
        $error_message = "No active visitor found with this ID or visitor already checked out.";
    }
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $conn->real_escape_string($_POST['id']);
    $time_out = $conn->real_escape_string($_POST['time_out']);
    
    // Update time out
    $update_sql = "UPDATE visitors 
                   SET time_out = '$time_out' 
                   WHERE id = '$id' AND time_out IS NULL";

    if ($conn->query($update_sql) === TRUE) {
        // Redirect to admin dashboard with success message
        header("Location: admin_dashboard.php?success=1");
        exit();
    } else {
        $error_message = "Error updating time out: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="admin_style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Time Out</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 400px;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .visitor-info {
            background-color: #f0f0f0;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update Visitor Time Out</h2>
        
        <?php
        // Display any error messages
        if (isset($error_message)) {
            echo "<div class='error'>" . htmlspecialchars($error_message) . "</div>";
        }
        ?>
        
        <?php if (!$visitor_found): ?>
            <!-- Initial ID lookup form -->
            <form method="GET" action="">
                <label>Enter Visitor ID:</label>
                <input type="number" name="id" required placeholder="Enter Visitor ID">
                <button type="submit">Look Up Visitor</button>
            </form>
        <?php else: ?>
            <!-- Time Out Update Form -->
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
                
                <div class="visitor-info">
                    <strong>Visitor Name:</strong> 
                    <?php echo htmlspecialchars($visitor_name); ?>
                </div>
                
                <label>Time Out:</label>
                <input type="datetime-local" name="time_out" required>
                
                <button type="submit">Update Time Out</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>