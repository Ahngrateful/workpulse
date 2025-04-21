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
    <title>Reports</title>
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
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .print-btn {
            background: #111;
            border: 1px solid #222;
            color: #fff;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .report-tabs {
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

        .report-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .report-card {
            background: #111;
            border-radius: 12px;
            padding: 25px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .report-card:hover {
            background: #181818;
        }

        .report-title {
            font-size: 1.2rem;
            font-weight: 500;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .report-description {
            color: #888;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .report-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
            cursor: pointer;
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
        <a href="reports.php" class="nav-item active">
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
                    <a href="profile.php"><span class="material-icons">person</span>Profile</a>
                    <a href="settings.php"><span class="material-icons">settings</span>Settings</a>
                    <a href="../login.php"><span class="material-icons">logout</span>Logout</a>
                </div>
            </div>
        </div>

        <div class="header">
            <h1>Reports</h1>
            <div class="header-actions">
                <button class="date-picker">April 2025</button>
                <button class="filter-btn">
                    <span class="material-icons">filter_list</span>
                </button>
                <button class="print-btn">
                    <span class="material-icons">print</span>
                </button>
                <button class="print-btn">
                    <span class="material-icons">download</span>
                </button>
            </div>
        </div>

        <div class="report-tabs">
            <button class="tab-btn active">Attendance Reports</button>
            <button class="tab-btn">Employee Reports</button>
            <button class="tab-btn">Device Reports</button>
        </div>

        <div class="report-grid">
            <div class="report-card">
                <div class="report-title">
                    <span class="material-icons">description</span>
                    Daily Attendance Summary
                </div>
                <p class="report-description">
                    Summary of daily attendance for all employees
                </p>
                <p class="report-description">
                    This report shows the daily attendance status of all employees, including check-in and check-out times.
                </p>
                <div class="report-actions">
                    <button class="action-btn" onclick="exportdaily()">
                        <span class="material-icons">download</span>
                        Export
                    </button>
                    <button class="action-btn">
                        <span class="material-icons">print</span>
                        Print
                    </button>
                </div>
            </div>

            <div class="report-card">
                <div class="report-title">
                    <span class="material-icons">description</span>
                    Monthly Attendance Report
                </div>
                <p class="report-description">
                    Monthly attendance statistics by department
                </p>
                <p class="report-description">
                    This report provides a monthly overview of attendance statistics broken down by department.
                </p>
                <div class="report-actions">
                    <button class="action-btn" onclick="exportmonthly()">
                        <span class="material-icons">download</span>
                        Export
                    </button>
                    <button class="action-btn">
                        <span class="material-icons">print</span>
                        Print
                    </button>
                </div>
            </div>

            <div class="report-card">
                <div class="report-title">
                    <span class="material-icons">description</span>
                    Late Arrival Report
                </div>
                <p class="report-description">
                    Report of employee's arriving late
                </p>
                <p class="report-description">
                    This report identifies employees who have arrived late.
                </p>
                <div class="report-actions">
                    <button class="action-btn" onclick="exportlate()">
                        <span class="material-icons">download</span>
                        Export
                    </button>
                    <button class="action-btn">
                        <span class="material-icons">print</span>
                        Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

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

    function exportdaily() {
    fetch('getdailyreport.php')
    .then(response => response.json())
    .then(data => {
        if (data.length === 0) {
            alert("No attendance available for today.");
            return;
        }

        const worksheet = XLSX.utils.json_to_sheet(data);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Daily_Attendance");

        XLSX.writeFile(workbook, "Daily_Attendance_Report.xlsx");
    })
    .catch(error => console.error('Error exporting to Excel:', error));
}

function exportmonthly() {
    fetch('getmonthlyreport.php')
    .then(response => response.json())
    .then(data => {
        if (data.length === 0) {
            alert("No attendance available for the month");
            return;
        }

        const worksheet = XLSX.utils.json_to_sheet(data);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Monthly_Attendance");

        XLSX.writeFile(workbook, "Monthly_Attendance_Report.xlsx");
    })
    .catch(error => console.error('Error exporting to Excel:', error));
}

function exportlate() {
    fetch('getlatereport.php')
    .then(response => response.json())
    .then(data => {
        if (data.length === 0) {
            alert("No employees late for today.");
            return;
        }

        const worksheet = XLSX.utils.json_to_sheet(data);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Late_Report");

        XLSX.writeFile(workbook, "Late_Emlployees_Report.xlsx");
    })
    .catch(error => console.error('Error exporting to Excel:', error));
}
    </script>
</body>
</html>