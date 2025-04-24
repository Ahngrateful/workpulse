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
    <title>Notifications</title>
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
        }

        .header-btn {
            background: #111;
            border: 1px solid #222;
            color: #fff;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .notification-tabs {
            display: flex;
            gap: 2px;
            margin-bottom: 20px;
            background: #111;
            padding: 5px;
            border-radius: 8px;
            width: fit-content;
        }

        .tab-btn {
            padding: 8px 16px;
            background: none;
            border: none;
            color: #888;
            cursor: pointer;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
        }

        .tab-btn.active {
            background: #222;
            color: #fff;
        }

        .notification-list {
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

        .notification-item {
            padding: 20px;
            border-radius: 8px;
            background: #0a0a0a;
            margin-bottom: 10px;
            display: flex;
            align-items: flex-start;
            gap: 15px;
            transition: all 0.3s;
        }

        .notification-item:hover {
            background: #151515;
        }

        .notification-icon {
            background: rgba(255, 255, 255, 0.1);
            padding: 10px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-size: 1rem;
            margin-bottom: 5px;
        }

        .notification-message {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .notification-time {
            color: #666;
            font-size: 0.8rem;
        }

        .notification-actions {
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
        <a href="devices.php" class="nav-item">
            <span class="material-icons">devices</span>
            Devices
        </a>
        <a href="notifications.php" class="nav-item active">
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
        </div>

        <div class="header">
            <h1>Notifications</h1>
            <div class="header-actions">
                <button class="header-btn">
                    <span class="material-icons">done_all</span>
                    Mark All as Read
                </button>
                <button class="header-btn">
                    <span class="material-icons">delete_sweep</span>
                    Clear All
                </button>
            </div>
        </div>

        <div class="notification-tabs">
            <button class="tab-btn active">
                All
                <span style="background: #222; padding: 2px 8px; border-radius: 10px; font-size: 0.8rem;">8</span>
            </button>
            <button class="tab-btn">
                Unread
                <span style="background: #222; padding: 2px 8px; border-radius: 10px; font-size: 0.8rem;">4</span>
            </button>
            <button class="tab-btn">System</button>
            <button class="tab-btn">Devices</button>
            <button class="tab-btn">Attendance</button>
            <button class="tab-btn">Employees</button>
        </div>

        <div class="notification-list">
            <div class="list-header">
                <h2>Notification List</h2>
                <p>Manage your system notifications</p>
            </div>

            <div class="notification-item">
                <div class="notification-icon">
                    <span class="material-icons">warning</span>
                </div>
                <div class="notification-content">
                    <div class="notification-title">Device Offline</div>
                    <div class="notification-message">IT Department device is offline for more than 1 hour.</div>
                    <div class="notification-time">10 minutes ago</div>
                </div>
                <div class="notification-actions">
                    <button class="action-btn" title="Mark as Read">
                        <span class="material-icons">done</span>
                    </button>
                    <button class="action-btn" title="Delete">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
            </div>

            <div class="notification-item">
                <div class="notification-icon">
                    <span class="material-icons">person_add</span>
                </div>
                <div class="notification-content">
                    <div class="notification-title">New Employee Added</div>
                    <div class="notification-message">Sarah Johnson has been added to the system.</div>
                    <div class="notification-time">1 hour ago</div>
                </div>
                <div class="notification-actions">
                    <button class="action-btn" title="Mark as Read">
                        <span class="material-icons">done</span>
                    </button>
                    <button class="action-btn" title="Delete">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
            </div>

            <div class="notification-item">
                <div class="notification-icon">
                    <span class="material-icons">description</span>
                </div>
                <div class="notification-content">
                    <div class="notification-title">Attendance Report Ready</div>
                    <div class="notification-message">Monthly attendance report for March is ready to view.</div>
                    <div class="notification-time">3 hours ago</div>
                </div>
                <div class="notification-actions">
                    <button class="action-btn" title="Mark as Read">
                        <span class="material-icons">done</span>
                    </button>
                    <button class="action-btn" title="Delete">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
            </div>

            <div class="notification-item">
                <div class="notification-icon">
                    <span class="material-icons">system_update</span>
                </div>
                <div class="notification-content">
                    <div class="notification-title">System Update</div>
                    <div class="notification-message">System will be updated tonight at 2:00 AM.</div>
                    <div class="notification-time">Yesterday</div>
                </div>
                <div class="notification-actions">
                    <button class="action-btn" title="Mark as Read">
                        <span class="material-icons">done</span>
                    </button>
                    <button class="action-btn" title="Delete">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
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