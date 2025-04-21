<?php
session_start();
require_once '../config.php';
// Make sure admin_ID is set in session
if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized. Admin not logged in.";
    exit;
}
$admin_id = $_SESSION['user_id'];

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get date range from GET parameters (or default values)
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '2025-01-01';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '2025-12-31';

// Optional: Validate date format (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
    die("Invalid date format.");
}
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20; // Show 20 rows per page
$offset = ($page - 1) * $limit;

// Assume you already have a connection to the database
// Count total records
$totalQuery = "SELECT COUNT(*) as total FROM attendances";
$totalResult = $conn->query($totalQuery);
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch paginated results
$query = "SELECT * FROM attendances LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

$startRow = $offset + 1;
$endRow = min($offset + $limit, $totalRows);
// SQL query with date filter
$sql = "
SELECT 
    at.*,  
    e.name,
    CASE 
        WHEN (HOUR(at.check_in) * 60 + MINUTE(at.check_in)) >= (HOUR(e.shift_start_time) * 60 + MINUTE(e.shift_start_time) + 30) 
        THEN 'Late'
        WHEN (HOUR(at.check_in) * 60 + MINUTE(at.check_in)) >= (HOUR(e.shift_start_time) * 60 + MINUTE(e.shift_start_time)) 
             AND (HOUR(at.check_in) * 60 + MINUTE(at.check_in)) < (HOUR(e.shift_start_time) * 60 + MINUTE(e.shift_start_time) + 30) 
        THEN 'On Time'
        ELSE 'Undertime'
    END AS status
FROM 
    attendances at  
JOIN 
    employee e ON at.employee_id = e.employee_id
WHERE 
    at.date BETWEEN ? AND ?
";


$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();


$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Attendance Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #0a0a0a;
            color: #fff;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #111;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 40px;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            color: #888;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }

        .nav-item:hover, .nav-item.active {
            background: #222;
            color: #fff;
        }

        .main-content {
            flex: 1;
            padding: 30px;
            position: relative;
            padding-top: 80px;
        }

        .header-right {
            position: absolute;
            top: 10px;
            right: 30px;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 0 12px;
        }

        .header h1 {
            font-size: 2.2rem;
            font-weight: bold;
        }

        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .add-attendance-btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .date-picker {
            background: #111;
            border: 1px solid #222;
            color: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
        }

        .filter-btn {
            background: #111;
            border: 1px solid #222;
            color: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .metric-card {
            background: #111;
            padding: 20px;
            border-radius: 12px;
        }

        .metric-label {
            color: #888;
            font-size: 0.9rem;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0;
        }

        .metric-subtext {
            color: #888;
            font-size: 0.8rem;
        }

        .attendance-records {
            background: #111;
            border-radius: 12px;
            padding: 20px;
        }

        .records-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .records-title {
            font-size: 1.2rem;
            font-weight: 500;
        }

        .records-subtitle {
            color: #888;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .records-nav {
            display: flex;
            gap: 10px;
        }

        .nav-btn {
            background: none;
            border: none;
            color: #888;
            cursor: pointer;
            padding: 8px;
            border-radius: 4px;
        }

        .nav-btn:hover {
            background: #222;
            color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px 12px;
            color: #888;
            font-weight: normal;
            border-bottom: 1px solid #222;
        }

        td {
            padding: 15px 12px;
            border-bottom: 1px solid #222;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .statusBtn {
        font-size: 0.85rem;
        font-weight: bold;
        padding: 4px 10px;
        border-radius: 20px;
        }

        .statusBtn.btn-success {
        background-color: #BCCFB9;
        border: none;
        }

        .statusBtn.btn-warning {
        background-color: #C29B99;
        border: none;
        }

        .account-dropdown {
            position: relative;
            display: inline-block;
            background: #111;
            border-radius: 8px;
            padding: 8px 16px;
            cursor: pointer;
            min-width: 200px;
            border: 1px solid #222;
        }

        .account-dropdown-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #fff;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .account-dropdown-btn .material-icons:last-child {
            margin-left: auto;
            font-size: 1.2rem;
            opacity: 0.7;
        }

        .account-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background: #111;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            margin-top: 5px;
            z-index: 1000;
            border: 1px solid #222;
        }

        .account-dropdown-content.show {
            display: block;
        }

        .account-dropdown-content a {
            color: #fff;
            padding: 12px 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .account-dropdown-content a:hover {
            background: #222;
        }

        .logout {
            margin-top: auto;
            color: #888;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="#" class="logo">
            <span class="material-icons">fingerprint</span>
            WorkPulse
        </a>
        <a href="dashboard.php" class="nav-item">
            <span class="material-icons">dashboard</span>
            Dashboard
        </a>
        <a href="employee.php" class="nav-item">
            <span class="material-icons">people</span>
            Employees
        </a>
        <a href="attendance.php" class="nav-item active">
            <span class="material-icons">event_available</span>
            Attendance
        </a>
        <a href="reports.php" class="nav-item">
            <span class="material-icons">assessment</span>
            Reports
        </a>
        <a href="devices.php" class="nav-item">
            <span class="material-icons">devices</span>
            Devices
        </a>
        <a href="notifications.php" class="nav-item">
            <span class="material-icons">notifications</span>
            Notifications
        </a>
        <a href="manage-admins.php" class="nav-item">
            <span class="material-icons">admin_panel_settings</span>
            Manage Admins
        </a>
        <a href="settings.php" class="nav-item">
            <span class="material-icons">settings</span>
            Settings
        </a>
        <a href="../login.php" class="logout">
            <span class="material-icons">logout</span>
            Logout
        </a>
    </div>

    <div class="main-content">
        <div class="header-right">
            <div class="account-dropdown">
                <div class="account-dropdown-btn">
                    <span class="material-icons">account_circle</span>
                    My Account
                    <span class="material-icons">expand_more</span>
                </div>
                <div class="account-dropdown-content">
                    <a href="#"><span class="material-icons">person</span>Profile</a>
                    <a href="#"><span class="material-icons">settings</span>Settings</a>
                    <a href="../login.php"><span class="material-icons">logout</span>Logout</a>
                </div>
            </div>
        </div>

        <div class="header">
            <h1>Attendance Management</h1>
            <div class="header-actions">
                <button class="add-attendance-btn" onclick="window.location.href='emp-time-in.php';">
                    <span class="material-icons">person_add</span>
                    Add Attendance
                </button>
                <button class="date-picker">April 2025</button>
                <button class="filter-btn">
                    <span class="material-icons">filter_list</span>
                    Filter
                </button>
            </div>
        </div>

        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-label">Total Records</div>
                <div class="metric-value">70</div>
                <div class="metric-subtext">For April 2025</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Present</div>
                <div class="metric-value">53</div>
                <div class="metric-subtext">75.7% of total</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Late Arrivals</div>
                <div class="metric-value">9</div>
                <div class="metric-subtext">12.9% of total</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Absences</div>
                <div class="metric-value">6</div>
                <div class="metric-subtext">8.6% of total</div>
            </div>
        </div>

        <div class="attendance-records">
            <div class="records-header">
                <div>
                    <h2 class="records-title">Attendance Records</h2>
                    <p class="records-subtitle">Employee attendance for April 2025</p>
                </div>
                <div class="records-nav">
                    <button class="nav-btn">
                        <span class="material-icons">chevron_left</span>
                    </button>
                    <button class="nav-btn">
                        <span class="material-icons">chevron_right</span>
                    </button>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Date</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Break In</th>
                        <th>Break Out</th>
                        <th>Overtime In</th>
                        <th>Overtime Out</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Updated at</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                    if (isset($result) && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                    echo"<tr>";
                    echo "<td>". htmlspecialchars($row['name']) . "</td>";
                    echo "<td>". htmlspecialchars($row['date']) . "</td>";
                    echo "<td>". htmlspecialchars($row['check_in']) . "</td>";
                    echo "<td>". htmlspecialchars($row['check_out']) . "</td>";
                    echo "<td>". htmlspecialchars($row['break_in']) . "</td>";
                    echo "<td>". htmlspecialchars($row['break_out']) . "</td>";
                    echo "<td>". htmlspecialchars($row['ot_in']) . "</td>";
                    echo "<td>". htmlspecialchars($row['ot_out']) . "</td>";
                    echo "<td>" . ' ' . "</td>";
                    $statusClass = ($row['status'] === 'Late') ? 'btn-warning' : 'btn-success';
                    echo '<td><button class="statusBtn btn ' . $statusClass . '">' . htmlspecialchars($row['status']) . '</button></td>';
                    echo "<td>". htmlspecialchars($row['updated_at']) . "</td>";
                    echo "</tr>";
                                    }
                                } else {
                                echo "<tr><td colspan='6'>No employees found.</td></tr>";
                                }
                            ?>    
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Dropdown click handler
            $('.account-dropdown-btn').click(function(e) {
                e.stopPropagation();
                $('.account-dropdown-content').toggleClass('show');
            });
    
            // Close dropdown when clicking outside
            $(document).click(function() {
                $('.account-dropdown-content').removeClass('show');
            });
    
            // Prevent dropdown from closing when clicking inside it
            $('.account-dropdown-content').click(function(e) {
                e.stopPropagation();
            });
        });
    </script>
</body>
</html>