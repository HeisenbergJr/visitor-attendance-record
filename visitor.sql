CREATE DATABASE IF NOT EXISTS visitor_attendance;
USE visitor_attendance;

-- Create visitors table
CREATE TABLE IF NOT EXISTS visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    contact VARCHAR(15) NOT NULL,
    whom_visiting VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    time_in DATETIME NOT NULL,
    time_out DATETIME NULL,
    is_checked_out BOOLEAN DEFAULT 0
);

-- Create index for better performance
CREATE INDEX idx_visitor_lookup ON visitors (id, full_name);

-- Drop the old users table if it exists (with incorrect structure)
DROP TABLE IF EXISTS users;

-- Create the correct admin_users table (matches your PHP code)
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- For password_hash() which produces longer hashes
    role VARCHAR(20) NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE admin_users ADD COLUMN full_name VARCHAR(100) NOT NULL AFTER password;

-- Insert a default admin account with properly hashed password
-- The password "admin123" has been hashed using password_hash()
INSERT INTO admin_users (username, password, role) VALUES 
('admin', '$2y$10$r3BpV5B7W6qkKJh8TzM6E.AcX9YbN2CvD1fE7G8H9I0JkL1M2N3O4P', 'admin');

-- Display table structure for verification
DESCRIBE visitors;
DESCRIBE admin_users;