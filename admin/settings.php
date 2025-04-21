<?php
session_start();
require_once '../config.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
// Make sure admin_ID is set in session
if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized. Admin not logged in.";
    exit;
  }
  $admin_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Settings</title>
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

        .save-btn {
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

        .settings-tabs {
            display: flex;
            gap: 2px;
            margin-bottom: 30px;
            background: #111;
            padding: 5px;
            border-radius: 8px;
            width: fit-content;
        }

        .tab-btn {
            padding: 10px 20px;
            background: none;
            border: none;
            color: #888;
            cursor: pointer;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .tab-btn.active {
            background: #222;
            color: #fff;
        }

        .settings-section {
            background: #111;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .section-header {
            margin-bottom: 20px;
        }

        .section-header h2 {
            font-size: 1.2rem;
            margin-bottom: 8px;
        }

        .section-header p {
            color: #888;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #fff;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            background: #0a0a0a;
            border: 1px solid #222;
            border-radius: 8px;
            color: #fff;
            font-size: 0.9rem;
        }

        .form-control:focus {
            outline: none;
            border-color: #2196F3;
        }

        .radio-group {
            display: flex;
            gap: 20px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .system-info {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #222;
        }

        .system-info:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #888;
        }

        .info-value {
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
        <a href="device.php" class="nav-item">
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
        <a href="settings.php" class="nav-item active">
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
        </div>

        <div class="header">
            <h1>Settings</h1>
            <button class="save-btn">
                <span class="material-icons">save</span>
                Save Changes
            </button>
        </div>

        <div class="settings-tabs">
            <button class="tab-btn active">General</button>
            <button class="tab-btn">Attendance</button>
            <button class="tab-btn">Devices</button>
            <button class="tab-btn">Notifications</button>
            <button class="tab-btn">Security</button>
        </div>

        <div class="settings-section">
            <div class="section-header">
                <h2>General Settings</h2>
                <p>Manage your organization and system settings</p>
            </div>

            <form>
                <div class="form-group">
                    <label>Company Name</label>
                    <input type="text" class="form-control" value="Acme Corporation">
                </div>

                <div class="form-group">
                    <label>Admin Email</label>
                    <input type="email" class="form-control" value="admin@example.com">
                </div>

                <div class="form-group">
                    <label>Timezone</label>
                    <select class="form-control">
                        <option>Eastern Time (UTC-5)</option>
                        <option>Pacific Time (UTC-8)</option>
                        <option>UTC</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Date Format</label>
                    <select class="form-control">
                        <option>MM/DD/YYYY</option>
                        <option>DD/MM/YYYY</option>
                        <option>YYYY-MM-DD</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Time Format</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="timeFormat" checked> 12-hour (AM/PM)
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="timeFormat"> 24-hour
                        </label>
                    </div>
                </div>
            </form>
        </div>

        <div class="settings-section">
            <div class="section-header">
                <h2>System Information</h2>
                <p>View system information and database settings</p>
            </div>

            <div class="system-info">
                <span class="info-label">System Version</span>
                <span class="info-value">v2.5.3</span>
            </div>

            <div class="system-info">
                <span class="info-label">Database Type</span>
                <span class="info-value">MySQL</span>
            </div>
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

            // Tab switching
            $('.tab-btn').click(function() {
                $('.tab-btn').removeClass('active');
                $(this).addClass('active');
            });
        });
    </script>
</body>
</html>