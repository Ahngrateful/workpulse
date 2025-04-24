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
?>
<!DOCTYPE html>
<html>
<head>
    <title>Device Management</title>
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

        .add-device-btn {
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
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .metric-icon {
            background: rgba(255, 255, 255, 0.1);
            padding: 12px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .metric-info {
            flex: 1;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .metric-label {
            color: #888;
            font-size: 0.9rem;
        }

        .device-list {
            background: #111;
            border-radius: 12px;
            padding: 25px;
        }

        .list-header {
            margin-bottom: 20px;
        }

        .list-header h2 {
            font-size: 1.2rem;
            margin-bottom: 8px;
        }

        .list-header p {
            color: #888;
            font-size: 0.9rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px;
            color: #888;
            font-weight: normal;
            border-bottom: 1px solid #222;
            font-size: 0.9rem;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #222;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-online {
            background: rgba(76, 175, 80, 0.1);
            color: #4CAF50;
        }

        .status-offline {
            background: rgba(158, 158, 158, 0.1);
            color: #9E9E9E;
        }

        .status-error {
            background: rgba(244, 67, 54, 0.1);
            color: #F44336;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            background: none;
            border: none;
            color: #888;
            cursor: pointer;
            padding: 5px;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .action-btn:hover {
            background: #222;
            color: #fff;
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
        <a href="attendance.php" class="nav-item">
            <span class="material-icons">event_available</span>
            Attendance
        </a>
        <a href="reports.php" class="nav-item">
            <span class="material-icons">assessment</span>
            Reports
        </a>
        <a href="devices.php" class="nav-item active">
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
                    <a href="profile.php"><span class="material-icons">person</span>Profile</a>
                    <a href="settings.php"><span class="material-icons">settings</span>Settings</a>
                    <a href="../login.php"><span class="material-icons">logout</span>Logout</a>
                </div>
            </div>
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
            <h1>Device Management</h1>
            <button class="add-device-btn">
                <span class="material-icons">add</span>
                Add Device
            </button>
        </div>

        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-icon">
                    <span class="material-icons">devices</span>
                </div>
                <div class="metric-info">
                    <div class="metric-value">0</div>
                    <div class="metric-label">Total Devices</div>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-icon">
                    <span class="material-icons">check_circle</span>
                </div>
                <div class="metric-info">
                    <div class="metric-value">0</div>
                    <div class="metric-label">Online</div>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-icon">
                    <span class="material-icons">offline_bolt</span>
                </div>
                <div class="metric-info">
                    <div class="metric-value">0</div>
                    <div class="metric-label">Offline</div>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-icon">
                    <span class="material-icons">error</span>
                </div>
                <div class="metric-info">
                    <div class="metric-value">0</div>
                    <div class="metric-label">Error</div>
                </div>
            </div>
        </div>

        <div class="device-list">
            <div class="list-header">
                <h2>Device List</h2>
                <p>Manage your ZKTeco devices and their connection status</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Serial Number</th>
                        <th>IP Address</th>
                        <th>Port</th>
                        <th>Model</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Last Sync</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Device 001</td>
                        <td>ZK2023001</td>
                        <td>192.168.1.100</td>
                        <td>4370</td>
                        <td>K40</td>
                        <td>Main Entrance</td>
                        <td><span class="status-badge status-online">Online</span></td>
                        <td>2024-02-20 10:30 AM</td>
                        <td>
                            <div class="actions">
                                <button class="action-btn" title="Edit">
                                    <span class="material-icons">edit</span>
                                </button>
                                <button class="action-btn" title="Sync">
                                    <span class="material-icons">sync</span>
                                </button>
                                <button class="action-btn" title="Delete">
                                    <span class="material-icons">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Device 002</td>
                        <td>ZK2023002</td>
                        <td>192.168.1.101</td>
                        <td>4370</td>
                        <td>K40</td>
                        <td>Back Entrance</td>
                        <td><span class="status-badge status-offline">Offline</span></td>
                        <td>2024-02-20 09:15 AM</td>
                        <td>
                            <div class="actions">
                                <button class="action-btn" title="Edit">
                                    <span class="material-icons">edit</span>
                                </button>
                                <button class="action-btn" title="Sync">
                                    <span class="material-icons">sync</span>
                                </button>
                                <button class="action-btn" title="Delete">
                                    <span class="material-icons">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Device 003</td>
                        <td>ZK2023003</td>
                        <td>192.168.1.102</td>
                        <td>4370</td>
                        <td>K40</td>
                        <td>Side Entrance</td>
                        <td><span class="status-badge status-error">Error</span></td>
                        <td>2024-02-20 08:45 AM</td>
                        <td>
                            <div class="actions">
                                <button class="action-btn" title="Edit">
                                    <span class="material-icons">edit</span>
                                </button>
                                <button class="action-btn" title="Sync">
                                    <span class="material-icons">sync</span>
                                </button>
                                <button class="action-btn" title="Delete">
                                    <span class="material-icons">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
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