<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = "localhost";
$db_username = "root";
$db_password = "";
$database = "visitor_attendance";

// Create connection
$conn = mysqli_connect($host, $db_username, $db_password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}