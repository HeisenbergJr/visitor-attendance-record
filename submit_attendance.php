<?php
session_start();

// Include database connection
require_once 'db_connection.php';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $conn->real_escape_string($_POST["full_name"]);
    $contact = $conn->real_escape_string($_POST["contact"]);
    $whom_visiting = $conn->real_escape_string($_POST["whom_visiting"]);
    $department = $conn->real_escape_string($_POST["department"]);
    $time_in = $conn->real_escape_string($_POST["time_in"]);

    // Insert into the database
    $sql = "INSERT INTO visitors (full_name, contact, whom_visiting, department, time_in) 
            VALUES ('$full_name', '$contact', '$whom_visiting', '$department', '$time_in')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to admin dashboard with success message
        header("Location: admin_dashboard.php?success=1");
        exit();
    } else {
        // If insertion fails, you might want to handle this case
        header("Location: admin_dashboard.php?error=1");
        exit();
    }

    $conn->close();
}