<?php
session_start();
require_once('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'admin') {
    header('Location: ../login.php');
    exit();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile - WorkPulse</title>
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

        .profile-container {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
        }

        .profile-picture {
            background: #111;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            height: fit-content;
        }

        .picture-container {
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
            position: relative;
            border-radius: 50%;
            overflow: hidden;
            background: #222;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .picture-container .material-icons {
            font-size: 80px;
            color: #444;
        }

        .upload-btn {
            background: #222;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .profile-info {
            background: #111;
            border-radius: 12px;
            padding: 25px;
        }

        .profile-tabs {
            display: flex;
            gap: 2px;
            margin-bottom: 30px;
            background: #0a0a0a;
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
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

        .employee-badge {
            background: #222;
            color: #888;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 10px;
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
        <a href="attendance.php" class="nav-item">
            <span class="material-icons">event_available</span>
            Attendance
        </a>
        <a href="profile.php" class="nav-item active">
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
                    John Smith
                    <span class="material-icons">expand_more</span>
                </div>
                <div class="account-dropdown-content">
                    <a href="profile.php"><span class="material-icons">person</span>Profile</a>
                    <a href="../login.php"><span class="material-icons">logout</span>Logout</a>
                </div>
            </div>
        </div>

        <div class="header">
            <h1>My Profile</h1>
            <button class="save-btn">
                <span class="material-icons">save</span>
                Save Changes
            </button>
        </div>

        <div class="profile-container">
            <div class="profile-picture">
                <div class="picture-container">
                    <span class="material-icons">person</span>
                </div>
                <h3>John Smith</h3>
                <p style="color: #888; margin: 5px 0;">Software Developer</p>
                <div class="employee-badge">
                    <span class="material-icons" style="font-size: 16px;">badge</span>
                    Employee
                </div>
                <button class="upload-btn">
                    <span class="material-icons">upload</span>
                    Upload New Picture
                </button>
            </div>

            <div class="profile-info">
                <div class="profile-tabs">
                    <button class="tab-btn active">Personal Information</button>
                    <button class="tab-btn">Contact Details</button>
                    <button class="tab-btn">Security</button>
                </div>

                <form>
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" class="form-control" value="John">
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" class="form-control" value="Smith">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Employee ID</label>
                        <input type="text" class="form-control" value="EMP-101" readonly>
                    </div>

                    <div class="form-group">
                        <label>Department</label>
                        <input type="text" class="form-control" value="IT" readonly>
                    </div>

                    <div class="form-group">
                        <label>Position</label>
                        <input type="text" class="form-control" value="Software Developer" readonly>
                    </div>

                    <div class="form-group">
                        <label>Join Date</label>
                        <input type="text" class="form-control" value="2022-01-15" readonly>
                    </div>

                    <div class="form-group">
                        <label>Bio</label>
                        <textarea class="form-control" rows="4">Experienced software developer with a passion for building user-friendly applications.</textarea>
                    </div>
                </form>
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