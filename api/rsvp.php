<?php
/**
 * RSVP Processing Script
 * Handles RSVP form submissions, stores data in database, and sends email notifications
 */

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Set response header
header('Content-Type: application/json');

// Include database configuration
require_once 'config.php';

// Initialize response
$response = array(
    'success' => false,
    'message' => ''
);

try {
    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validate and sanitize input data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $guests = isset($_POST['guests']) ? intval($_POST['guests']) : 1;
    $comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';
    $rsvp_response = isset($_POST['response']) ? trim($_POST['response']) : '';

    // Validate required fields
    if (empty($name)) {
        throw new Exception('Name is required');
    }

    if (empty($rsvp_response) || !in_array($rsvp_response, ['accept', 'decline'])) {
        throw new Exception('Please select Accept or Decline');
    }

    // Validate email format if provided
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Sanitize inputs
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
    $comments = htmlspecialchars($comments, ENT_QUOTES, 'UTF-8');

    // Get database connection
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception('Database connection failed. Please try again later.');
    }

    // Check for duplicate submissions (optional - based on email or phone)
    if (!empty($email)) {
        $check_stmt = $conn->prepare("SELECT id FROM rsvps WHERE email = ? AND email != ''");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Update existing RSVP instead of creating duplicate
            $stmt = $conn->prepare("UPDATE rsvps SET name = ?, phone = ?, guests = ?, response = ?, comments = ?, created_at = NOW() WHERE email = ?");
            $stmt->bind_param("ssisss", $name, $phone, $guests, $rsvp_response, $comments, $email);
        } else {
            // Insert new RSVP
            $stmt = $conn->prepare("INSERT INTO rsvps (name, email, phone, guests, response, comments, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssiss", $name, $email, $phone, $guests, $rsvp_response, $comments);
        }
        $check_stmt->close();
    } else {
        // No email provided, just insert
        $stmt = $conn->prepare("INSERT INTO rsvps (name, email, phone, guests, response, comments, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssiss", $name, $email, $phone, $guests, $rsvp_response, $comments);
    }

    // Execute query
    if (!$stmt->execute()) {
        throw new Exception('Failed to save RSVP. Please try again.');
    }

    $stmt->close();

    // Send email notification
    $response_text = ($rsvp_response === 'accept') ? 'ACCEPTED' : 'DECLINED';
    $email_subject = "New RSVP: {$name} has {$response_text}";
    
    $email_body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #3498db; color: white; padding: 20px; text-align: center; }
            .content { background: #f8f9fa; padding: 20px; margin-top: 20px; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #2c3e50; }
            .value { color: #555; }
            .response { font-size: 24px; font-weight: bold; padding: 15px; text-align: center; margin: 20px 0; }
            .accept { background: #27ae60; color: white; }
            .decline { background: #e74c3c; color: white; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Housewarming RSVP</h2>
            </div>
            <div class='content'>
                <div class='response " . ($rsvp_response === 'accept' ? 'accept' : 'decline') . "'>
                    {$response_text}
                </div>
                
                <div class='field'>
                    <span class='label'>Name:</span>
                    <span class='value'>{$name}</span>
                </div>
                
                <div class='field'>
                    <span class='label'>Email:</span>
                    <span class='value'>" . ($email ?: 'Not provided') . "</span>
                </div>
                
                <div class='field'>
                    <span class='label'>Phone:</span>
                    <span class='value'>" . ($phone ?: 'Not provided') . "</span>
                </div>
                
                <div class='field'>
                    <span class='label'>Number of Guests:</span>
                    <span class='value'>{$guests}</span>
                </div>
                
                " . (!empty($comments) ? "
                <div class='field'>
                    <span class='label'>Comments:</span>
                    <div class='value'>{$comments}</div>
                </div>
                " : "") . "
                
                <div class='field'>
                    <span class='label'>Submitted:</span>
                    <span class='value'>" . date('F j, Y g:i A') . "</span>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";

    sendEmailNotification($email_subject, $email_body);

    // Close database connection
    closeDBConnection($conn);

    // Success response
    $response['success'] = true;
    if ($rsvp_response === 'accept') {
        $response['message'] = 'Thank you for your RSVP! We look forward to seeing you at our housewarming party!';
    } else {
        $response['message'] = 'Thank you for letting us know. We\'ll miss you!';
    }

} catch (Exception $e) {
    // Error response
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log("RSVP Error: " . $e->getMessage());
}

// Send JSON response
echo json_encode($response);
?>
