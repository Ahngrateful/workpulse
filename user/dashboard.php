<?php
session_start();
require_once '../config.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'admin') {
    header('Location: ../login.php');
    exit();
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
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

        .header .date-display {
            color: #888;
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
            color: #888;
            font-size: 0.9rem;
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
            width: 100%;    /* Match parent width */
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

        /* Remove this entire rule below */
        /* .account-dropdown:hover .account-dropdown-content {
            display: block;
        } */

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
            position: relative;
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
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .metric-info {
            font-size: 0.85rem;
            color: #666;
        }

        .status-present {
            color: #4caf50;
        }

        .time-filter {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .time-filter button {
            background: #111;
            border: none;
            color: #888;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .time-filter button.active {
            background: #222;
            color: #fff;
        }

        .recent-attendance {
            background: #111;
            border-radius: 12px;
            padding: 20px;
        }

        .attendance-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        .attendance-item {
            background: #1a1a1a;
            padding: 15px 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .attendance-date {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .day {
            color: #fff;
            font-size: 0.95rem;
        }

        .check-in {
            color: #4caf50;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .hours-worked {
            color: #888;
            font-size: 0.9rem;
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
        <a href="#" class="nav-item active">
            <span class="material-icons">dashboard</span>
            Dashboard
        </a>
        <a href="attendance.php" class="nav-item">
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
            <h1>My Dashboard</h1>
            <div class="date-display">Today: <?php echo date('n/j/Y'); ?></div>
        </div>

        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-label">Today's Status</div>
                <div class="metric-value status-present">Present</div>
                <div class="metric-info">Checked in at 8:45 AM</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">This Month</div>
                <div class="metric-value">21/23</div>
                <div class="metric-info">Days present</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Late Arrivals</div>
                <div class="metric-value">2</div>
                <div class="metric-info">This month</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Overtime</div>
                <div class="metric-value">4.5h</div>
                <div class="metric-info">This month</div>
            </div>
        </div>

        <div class="time-filter">
            <button class="active">Today</button>
            <button>This Week</button>
            <button>This Month</button>
        </div>

        <div class="recent-attendance">
            <h2>Recent Activity</h2>
            <p style="color: #888; margin-top: 5px;">Your attendance records for the past week</p>
            
            <div class="attendance-list">
                <div class="attendance-item">
                    <div class="attendance-date">
                        <div class="day">Monday, Apr 14</div>
                        <div class="check-in">Checked in at 8:30 AM</div>
                    </div>
                    <div class="hours-worked">8 hours</div>
                </div>
                <div class="attendance-item">
                    <div class="attendance-date">
                        <div class="day">Sunday, Apr 13</div>
                        <div class="check-in">Checked in at 8:30 AM</div>
                    </div>
                    <div class="hours-worked">8 hours</div>
                </div>
                <div class="attendance-item">
                    <div class="attendance-date">
                        <div class="day">Saturday, Apr 12</div>
                        <div class="check-in">Checked in at 9:30 AM</div>
                    </div>
                    <div class="hours-worked">9 hours</div>
                </div>
                <div class="attendance-item">
                    <div class="attendance-date">
                        <div class="day">Friday, Apr 11</div>
                        <div class="check-in">Checked in at 8:00 AM</div>
                    </div>
                    <div class="hours-worked">8 hours</div>
                </div>
                <div class="attendance-item">
                    <div class="attendance-date">
                        <div class="day">Thursday, Apr 10</div>
                        <div class="check-in">Checked in at 8:00 AM</div>
                    </div>
                    <div class="hours-worked">9 hours</div>
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