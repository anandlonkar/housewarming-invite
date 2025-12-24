<?php
// Test database connection
$host = 'anandlonkar.ipagemysql.com';
$dbname = 'housewarming_rsvp';
$username = 'warmhouseadmin';
$password = 'Just4WarmHouse!';

echo "Testing database connection...\n\n";
echo "Host: $host\n";
echo "Database: $dbname\n";
echo "User: $username\n\n";

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        echo "❌ Connection FAILED: " . $conn->connect_error . "\n";
        exit(1);
    }
    
    echo "✅ Connection SUCCESSFUL!\n\n";
    
    // Check if tables exist
    $result = $conn->query("SHOW TABLES");
    if ($result) {
        echo "Tables in database:\n";
        while ($row = $result->fetch_array()) {
            echo "  - " . $row[0] . "\n";
        }
    }
    
    $conn->close();
    echo "\n✅ Database connection test completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
