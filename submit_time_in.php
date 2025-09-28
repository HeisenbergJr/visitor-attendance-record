<?php
// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "visitor_attendance";

$conn = mysqli_connect($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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
        header("Location: admin_dashboard.php");
        echo "Visitor's time in recorded successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
