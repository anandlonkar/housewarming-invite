<?php
/**
 * Admin Panel to View RSVPs
 * Password-protected page to view all RSVP responses
 */

session_start();

// Include database configuration
require_once '../api/config.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $password = $_POST['password'] ?? '';
    
    if ($password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        $isLoggedIn = true;
    } else {
        $loginError = 'Invalid password';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: view_rsvps.php');
    exit;
}

// If not logged in, show login form
if (!$isLoggedIn) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login - Housewarming RSVPs</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
            }
            .login-container {
                background: white;
                padding: 40px;
                border-radius: 10px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                max-width: 400px;
                width: 100%;
            }
            h1 { color: #333; text-align: center; margin-bottom: 30px; }
            .form-group { margin-bottom: 20px; }
            label { display: block; margin-bottom: 5px; color: #555; font-weight: bold; }
            input[type="password"] {
                width: 100%;
                padding: 12px;
                border: 1px solid #ddd;
                border-radius: 5px;
                font-size: 16px;
                box-sizing: border-box;
            }
            button {
                width: 100%;
                padding: 12px;
                background: #667eea;
                color: white;
                border: none;
                border-radius: 5px;
                font-size: 16px;
                cursor: pointer;
            }
            button:hover { background: #5568d3; }
            .error { color: #e74c3c; margin-bottom: 15px; text-align: center; }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h1>Admin Login</h1>
            <?php if (isset($loginError)): ?>
                <div class="error"><?php echo $loginError; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autofocus>
                </div>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// User is logged in, show RSVPs
$conn = getDBConnection();
if (!$conn) {
    die('Database connection failed');
}

// Get filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build SQL query
$sql = "SELECT * FROM rsvps WHERE 1=1";
$params = array();
$types = '';

if ($filter === 'accept') {
    $sql .= " AND response = 'accept'";
} elseif ($filter === 'decline') {
    $sql .= " AND response = 'decline'";
}

if (!empty($searchQuery)) {
    $sql .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $searchParam = '%' . $searchQuery . '%';
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'sss';
}

$sql .= " ORDER BY created_at DESC";

// Prepare and execute query
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get statistics
$stats = $conn->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN response = 'accept' THEN 1 ELSE 0 END) as accepted,
        SUM(CASE WHEN response = 'decline' THEN 1 ELSE 0 END) as declined,
        SUM(CASE WHEN response = 'accept' THEN guests ELSE 0 END) as total_guests
    FROM rsvps
")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View RSVPs - Admin Panel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        h1 { color: #2c3e50; }
        .logout { color: #e74c3c; text-decoration: none; font-weight: bold; }
        .logout:hover { text-decoration: underline; }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .stat-number { font-size: 2.5rem; font-weight: bold; margin: 10px 0; }
        .stat-label { color: #777; font-size: 0.9rem; }
        .stat-card.total .stat-number { color: #3498db; }
        .stat-card.accepted .stat-number { color: #27ae60; }
        .stat-card.declined .stat-number { color: #e74c3c; }
        .stat-card.guests .stat-number { color: #9b59b6; }
        .filters {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .filter-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-btn {
            padding: 10px 20px;
            border: 1px solid #ddd;
            background: white;
            color: #555;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .filter-btn.active { background: #3498db; color: white; border-color: #3498db; }
        .filter-btn:hover { background: #ecf0f1; }
        .filter-btn.active:hover { background: #2980b9; }
        .search-box {
            flex: 1;
            min-width: 250px;
        }
        .search-box input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .rsvp-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        tr:hover { background: #f8f9fa; }
        .badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: bold;
        }
        .badge.accept { background: #d4edda; color: #155724; }
        .badge.decline { background: #f8d7da; color: #721c24; }
        .no-data {
            padding: 40px;
            text-align: center;
            color: #999;
        }
        .export-btn {
            padding: 10px 20px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .export-btn:hover { background: #229954; }
        @media (max-width: 768px) {
            .header { flex-direction: column; gap: 15px; text-align: center; }
            .stats { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Housewarming RSVPs</h1>
            <a href="?logout=1" class="logout">Logout</a>
        </div>

        <div class="stats">
            <div class="stat-card total">
                <div class="stat-label">Total RSVPs</div>
                <div class="stat-number"><?php echo $stats['total']; ?></div>
            </div>
            <div class="stat-card accepted">
                <div class="stat-label">Accepted</div>
                <div class="stat-number"><?php echo $stats['accepted']; ?></div>
            </div>
            <div class="stat-card declined">
                <div class="stat-label">Declined</div>
                <div class="stat-number"><?php echo $stats['declined']; ?></div>
            </div>
            <div class="stat-card guests">
                <div class="stat-label">Total Guests</div>
                <div class="stat-number"><?php echo $stats['total_guests']; ?></div>
            </div>
        </div>

        <div class="filters">
            <form method="GET" class="filter-group">
                <a href="?filter=all" class="filter-btn <?php echo $filter === 'all' ? 'active' : ''; ?>">All</a>
                <a href="?filter=accept" class="filter-btn <?php echo $filter === 'accept' ? 'active' : ''; ?>">Accepted</a>
                <a href="?filter=decline" class="filter-btn <?php echo $filter === 'decline' ? 'active' : ''; ?>">Declined</a>
                <div class="search-box">
                    <input type="text" name="search" placeholder="Search by name, email, or phone..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                </div>
            </form>
        </div>

        <div class="rsvp-table">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Response</th>
                            <th>Guests</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Comments</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                                <td>
                                    <span class="badge <?php echo $row['response']; ?>">
                                        <?php echo ucfirst($row['response']); ?>
                                    </span>
                                </td>
                                <td><?php echo $row['guests']; ?></td>
                                <td><?php echo htmlspecialchars($row['email']) ?: '-'; ?></td>
                                <td><?php echo htmlspecialchars($row['phone']) ?: '-'; ?></td>
                                <td><?php echo htmlspecialchars($row['comments']) ?: '-'; ?></td>
                                <td><?php echo date('M j, Y g:i A', strtotime($row['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">No RSVPs found.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php
$stmt->close();
closeDBConnection($conn);
?>
