<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Attendance - WorkPulse</title>
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
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
            z-index: 1000;
        }

        .account-dropdown {
            position: relative;
            display: inline-block;
            background: #111;
            border-radius: 8px;
            padding: 10px 16px;
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
            color: #fff;
        }

        .month-selector {
            background: #111;
            border: none;
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .metric-card {
            background: #111;
            padding: 20px;
            border-radius: 12px;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0;
            color: #fff;
        }

        .metric-label {
            color: #888;
            font-size: 0.9rem;
        }

        .metric-subtext {
            color: #666;
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

        .records-header h2 {
            font-size: 1.2rem;
            font-weight: 500;
        }

        .records-header p {
            color: #888;
            font-size: 0.9rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #222;
        }

        th {
            color: #888;
            font-weight: normal;
            font-size: 0.9rem;
        }

        td {
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .status-present {
            background: #1a472a;
            color: #2ecc71;
        }

        .status-absent {
            background: #4a1a1a;
            color: #e74c3c;
        }

        .status-late {
            background: #4a3a1a;
            color: #f1c40f;
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
        <a href="attendance.php" class="nav-item active">
            <span class="material-icons">event_available</span>
            Attendance
        </a>
        <a href="profile.php" class="nav-item">
            <span class="material-icons">person</span>
            Profile
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
            <h1>My Attendance</h1>
            <select class="month-selector" id="monthSelector">
                <option value="4">April 2025</option>
                <option value="3">March 2025</option>
                <option value="2">February 2025</option>
            </select>
        </div>

        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-label">Present Days</div>
                <div class="metric-value" id="presentDays">9</div>
                <div class="metric-subtext">Out of 10 working days</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Absent Days</div>
                <div class="metric-value" id="absentDays">1</div>
                <div class="metric-subtext">This month</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Late Arrivals</div>
                <div class="metric-value" id="lateArrivals">0</div>
                <div class="metric-subtext">This month</div>
            </div>
        </div>

        <div class="attendance-records">
            <div class="records-header">
                <h2>Attendance Records</h2>
                <p>Your attendance history for April 2025</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody id="attendanceTableBody">
                    <!-- Table content will be populated by JavaScript -->
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

            // Sample data - Replace with actual API call
            const attendanceData = [
                {
                    date: 'Mon, Apr 14, 2025',
                    checkIn: '08:22',
                    checkOut: '18:31',
                    duration: '10h 9m',
                    status: 'Present',
                    location: 'Main Building'
                },
                // Add more records as needed
            ];

            function updateAttendanceTable(data) {
                const tbody = $('#attendanceTableBody');
                tbody.empty();

                data.forEach(record => {
                    const statusClass = record.status.toLowerCase();
                    tbody.append(`
                        <tr>
                            <td>${record.date}</td>
                            <td>${record.checkIn}</td>
                            <td>${record.checkOut}</td>
                            <td>${record.duration}</td>
                            <td><span class="status-badge status-${statusClass}">${record.status}</span></td>
                            <td>${record.location}</td>
                        </tr>
                    `);
                });
            }

            // Initial table population
            updateAttendanceTable(attendanceData);

            // Month selector change handler
            $('#monthSelector').change(function() {
                const selectedMonth = $(this).val();
                // Add API call here to fetch data for selected month
                // For now, just update with sample data
                updateAttendanceTable(attendanceData);
            });
        });
    </script>
</body>
</html>