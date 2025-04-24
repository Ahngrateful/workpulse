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
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0a0a;
            --bg-secondary: #111;
            --bg-hover: #222;
            --text-primary: #fff;
            --text-secondary: #888;
            --border-color: #222;
        }

        :root[data-theme="light"] {
            --bg-primary: #f5f5f5;
            --bg-secondary: #ffffff;
            --bg-hover: #e5e5e5;
            --text-primary: #111;
            --text-secondary: #555;
            --border-color: #ddd;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: var(--bg-secondary);
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 40px;
            color: var(--text-primary);
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
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }

        .nav-item:hover, .nav-item.active {
            background: var(--bg-hover);
            color: var(--text-primary);
        }

        .main-content {
            flex: 1;
            padding: 30px;
            position: relative;
            padding-top: 80px;
        }

        .header {
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-left: 12px;
            padding-right: 12px;
        }

        .header h1 {
            font-size: 2.2rem;
            font-weight: bold;
            color: var(--text-primary);
        }

        .header .date-display {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .header-right {
            position: absolute;
            top: 10px;
            right: 30px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
            z-index: 1000;
        }

        .date-display {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        .account-dropdown {
            position: relative;
            display: inline-block;
            background: var(--bg-secondary);
            border-radius: 8px;
            padding: 10px 16px;
            cursor: pointer;
            min-width: 200px;
            border: 1px solid var(--border-color);
        }

        .account-dropdown-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-primary);
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
            background: var(--bg-secondary);
            width: 100%;    /* Match parent width */
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-top: 5px;
            z-index: 1000;
            border: 1px solid var(--border-color);
        }

        .account-dropdown-content.show {
            display: block;
        }

        .account-dropdown-content a {
            color: var(--text-primary);
            padding: 12px 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .account-dropdown-content a:hover {
            background: var(--bg-hover);
        }

        .theme-toggle {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .theme-toggle:hover {
            background: var(--bg-hover);
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .metric-card {
            background: var(--bg-secondary);
            padding: 20px;
            border-radius: 12px;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0;
        }

        .metric-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .metric-change {
            font-size: 0.8rem;
        }

        .positive-change {
            color: #4caf50;
        }

        .negative-change {
            color: #f44336;
        }

        .time-filter {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .time-filter button {
            background: var(--bg-secondary);
            border: none;
            color: var(--text-secondary);
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .time-filter button.active {
            background: var(--bg-hover);
            color: var(--text-primary);
        }

        .recent-attendance {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: 20px;
        }

        .attendance-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .attendance-card {
            background: var(--bg-hover);
            padding: 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-icon {
            background: var(--bg-secondary);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .attendance-info {
            flex: 1;
        }

        .attendance-time {
            color: #4caf50;
            font-size: 0.8rem;
        }

        .logout {
            margin-top: auto;
            color: var(--text-secondary);
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
        <a href="#" class="nav-item active">
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
            <div class="date-display"><?php echo date('l, F j, Y'); ?></div>
            <button class="theme-toggle">
                <span class="material-icons theme-icon">dark_mode</span>
                <span class="theme-text">Dark Mode</span>
            </button>
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
            <h1>Admin Dashboard</h1>
        </div>

        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-label">Total Employees</div>
                <div id="total-employee" class="metric-value"><?php ?></div>
                <div class="metric-change positive-change">↑ % from last month</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Present Today</div>
                <div id="present-today" class="metric-value"></div>
                <div id="attendance-rate" class="metric-change positive-change">↑ % attendance rate</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Absent Today</div>
                <div class="metric-value"></div>
                <div class="metric-change negative-change">↓ % absence rate</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Late Arrivals</div>
                <div class="metric-value">7</div>
                <div class="metric-change positive-change">↑ 14.2% from last week</div>
            </div>
        </div>

        <div class="time-filter">
            <button class="active">Today</button>
            <button>This Week</button>
            <button>This Month</button>
        </div>

        <div class="recent-attendance">
            <h2>Recent Attendance</h2>
            <p style="color: var(--text-secondary); margin-top: 5px;">Showing the latest attendance records for today</p>
            
            <div class="attendance-grid">
                <div class="attendance-card">
                    <div class="user-icon">
                        <span class="material-icons">person</span>
                    </div>
                    <div class="attendance-info">
                        <div>
                        <!-- name -->
                        </div>  
                        <div class="attendance-time">
                            <span class="material-icons" style="font-size: 0.8rem;">check_circle</span>
                            Checked in at
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Theme toggle functionality
        const themeToggle = document.querySelector('.theme-toggle');
        const themeIcon = themeToggle.querySelector('.theme-icon');
        const themeText = themeToggle.querySelector('.theme-text');

        // Check for saved theme preference, otherwise use system preference
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.documentElement.setAttribute('data-theme', savedTheme);
            updateThemeButton(savedTheme);
        } else {
            const systemTheme = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', systemTheme);
            updateThemeButton(systemTheme);
        }

        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeButton(newTheme);
        });

        function updateThemeButton(theme) {
            themeIcon.textContent = theme === 'light' ? 'light_mode' : 'dark_mode';
            themeText.textContent = theme === 'light' ? 'Light Mode' : 'Dark Mode';
        }

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

        $.ajax({
            url: '../api/dashboard.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                // Update metrics
                $('#total-employee').text(response.total_users);
                $('#present-today').text(response.present_users);
                $('.metric-card:eq(2) .metric-value').text(response.absent_users);
                
                // Update attendance rate
                $('#attendance-rate').text('↑ ' + response.attendance_rate.toFixed(1) + '% attendance rate');
                
                // Clear existing attendance cards
                $('.attendance-grid').empty();
                
                // Add new attendance cards
                response.data.forEach(function(attendance) {
                    $('.attendance-grid').append(`
                        <div class="attendance-card">
                            <div class="user-icon">
                                <span class="material-icons">person</span>
                            </div>
                            <div class="attendance-info">
                                <div>${attendance.name}</div>
                                <div class="attendance-time">
                                    <span class="material-icons" style="font-size: 0.8rem;">check_circle</span>
                                    ${attendance.type} at ${attendance.time}
                                </div>
                            </div>
                        </div>
                    `);
                });
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    });
</script>

    
</body>
</html>