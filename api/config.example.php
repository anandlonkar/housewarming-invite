<?php
/**
 * Database Configuration File - EXAMPLE
 * 
 * Copy this file to config.php and update with your actual credentials
 */

// Database credentials - UPDATE THESE VALUES
define('DB_HOST', 'your-database-host.com');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');

// Email configuration - UPDATE THIS VALUE
define('ADMIN_EMAIL', 'your-email@example.com');

// Admin panel password - UPDATE THIS VALUE
define('ADMIN_PASSWORD', 'your_secure_admin_password');

// Timezone (optional - adjust as needed)
date_default_timezone_set('America/Chicago');

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

/**
 * Database Connection Function
 */
function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            error_log("Database connection failed: " . $conn->connect_error);
            return null;
        }
        
        $conn->set_charset("utf8mb4");
        return $conn;
        
    } catch (Exception $e) {
        error_log("Database connection error: " . $e->getMessage());
        return null;
    }
}

/**
 * Close Database Connection
 */
function closeDBConnection($conn) {
    if ($conn && !$conn->connect_error) {
        $conn->close();
    }
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}
?>
