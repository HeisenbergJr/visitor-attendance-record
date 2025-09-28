<?php
session_start();

// Include database connection
require_once 'db_connection.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $username = trim($conn->real_escape_string($_POST["username"]));
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $full_name = trim($conn->real_escape_string($_POST["full_name"]));

    // Validation
    $errors = [];

    // Check username length
    if (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters long.";
    }

    // Check full name
    if (strlen($full_name) < 2) {
        $errors[] = "Please enter your full name.";
    }

    // Check password match
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check password strength
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    // Check if username exists
    $check_user_query = "SELECT * FROM admin_users WHERE username = ?";
    $stmt = $conn->prepare($check_user_query);
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Username already exists. Please choose another.";
        }
        $stmt->close();
    } else {
        $errors[] = "Database error: " . $conn->error;
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare insert statement
        $insert_query = "INSERT INTO admin_users (username, password, full_name, role) VALUES (?, ?, ?, 'admin')";
        $stmt = $conn->prepare($insert_query);
        
        if ($stmt) {
            $stmt->bind_param("sss", $username, $hashed_password, $full_name);

            // Execute the statement
            if ($stmt->execute()) {
                // Set session variables
                $_SESSION["admin_logged_in"] = true;
                $_SESSION["username"] = $username;
                $_SESSION["full_name"] = $full_name;
                $_SESSION["role"] = 'admin';

                // Redirect to dashboard with welcome message
                header("Location: admin_dashboard.php?welcome=1");
                exit();
            } else {
                $errors[] = "Error creating account: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errors[] = "Database error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="admin_style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f7fb;
            margin: 0;
            padding: 20px;
        }
        .register-container {
            width: 100%;
            max-width: 400px;
        }
        .register-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .register-form h2 {
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
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        .btn-register {
            width: 100%;
            padding: 12px;
            background-color: #2ecc71;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-register:hover {
            background-color: #27ae60;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #dc3545;
        }
        .login-link {
            text-align: center;
            display: block;
            margin-top: 15px;
            color: #3498db;
            text-decoration: none;
        }
        .login-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <form class="register-form" method="POST" action="">
            <h2>Admin Registration</h2>
            
            <?php
            // Display errors
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    echo "<div class='error-message'>$error</div>";
                }
            }
            ?>
            
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required 
                       value="<?php echo isset($full_name) ? htmlspecialchars($full_name) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Choose a username" required 
                       value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Choose a password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
            </div>
            
            <button type="submit" class="btn-register">Create Account</button>
            
            <a href="admin_login.php" class="login-link">Already have an account? Login here</a>
        </form>
    </div>
</body>
</html>