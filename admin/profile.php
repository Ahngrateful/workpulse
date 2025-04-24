<?php
session_start();
require_once '../config.php';

//  Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$admin_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT photo, first_name, last_name, username, role, bio FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $firstname = $_POST['fname'];
    $lastname = $_POST['lname'];
    $username = $_POST['uname'];
    $role = $_POST['role'];
    $bio = $_POST['bio'];

    // Correct table name is `users` not `user`
    $update = "UPDATE users SET first_name = ?, last_name = ?, username = ?, role = ?, bio = ? WHERE user_id = ?";
    $stmt = $conn->prepare($update);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssssi", $firstname, $lastname, $username, $role, $bio, $admin_id);

    if ($stmt->execute()) {
        echo "<script>alert('Admin updated successfully.'); window.location.href=window.location.href;</script>";
    } else {
        echo "<script>alert('Error updating admin: " . $stmt->error . "');</script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save-pass'])) {
    $current_password = $_POST['current-password'];
    $new_password = $_POST['new-password'];
    $confirm_password = $_POST['confirm-password'];

    // Fetch current hashed password from database
    $sql = "SELECT password FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->bind_result($db_password);
    $stmt->fetch();
    $stmt->close();

    // Verify the current password
    if (!password_verify($current_password, $db_password)) {
        echo "<script>alert('Current password is incorrect.');</script>";
    } elseif ($new_password !== $confirm_password) {
        echo "<script>alert('New passwords do not match.');</script>";
    } else {
        // Hash the new password before storing
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $admin_id);

        if ($stmt->execute()) {
            echo "<script>alert('Password changed successfully.'); window.location.href=window.location.href;</script>";
        } else {
            echo "<script>alert('Error changing password.');</script>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile</title>
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

        .admin-badge {
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
            <h1>Admin Profile</h1>
        </div>

        <div class="profile-container">
            <div class="profile-picture">
                <div class="picture-container">
                    <?php if (!empty($user['photo'])): ?>
                        <img src="<?php echo htmlspecialchars($user['photo']); ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%;">
                    <?php else: ?>
                        <span class="material-icons">person</span>
                    <?php endif; ?>
                </div>
                <h3><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                <p style="color: #888; margin: 5px 0;"><?php echo htmlspecialchars($user['role']); ?></p>
                <div class="admin-badge">
                    <span class="material-icons" style="font-size: 16px;">verified</span>
                    Admin
                </div>
            </div>

            <div class="profile-info">
                <div class="profile-tabs">
                    <button type="button" class="tab-btn active" onclick="showTab('personal')">Personal Information</button>
                    <button type="button" class="tab-btn" onclick="showTab('security')">Security</button>
                </div>

                <!-- Personal Info Tab -->
                <form method="POST" action="">
                <div id="personal-info-tab" class="tab-content" style="display: block;">
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="fname" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="lname" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="uname" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Role</label>
                        <input type="text" name="role" class="form-control" value="<?php echo htmlspecialchars($user['role']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Bio</label>
                        <textarea name="bio" class="form-control" rows="4"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                    </div>
                    <button type="submit" name="save" class="save-btn">
                        <span class="material-icons">save</span>
                        Save Changes
                    </button>
                </div>
            </form>
                <!-- Security Tab -->
                <form method="POST" action="">
                <div id="security-tab" class="tab-content" style="display: none;">
                    
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" class="form-control" name="current-password" id="current-password" placeholder="Enter current password" required />
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" class="form-control" name="new-password" id="new-password" placeholder="Enter new password" required />
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" class="form-control" name="confirm-password" id="confirm-password" placeholder="Confirm new password" required />
                        </div>
                        <button type="submit" name="save-pass" class="save-btn">
                            <span class="material-icons">save</span>
                            Save Changes
                        </button>

                </div>
            </div>
        </div>
    </form>
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

        // Tab toggle function
    function showTab(tab) {
        const buttons = document.querySelectorAll('.tab-btn');
        const personalTab = document.getElementById('personal-info-tab');
        const securityTab = document.getElementById('security-tab');

        buttons.forEach(btn => btn.classList.remove('active'));

        if (tab === 'personal') {
            buttons[0].classList.add('active');
            personalTab.style.display = 'block';
            securityTab.style.display = 'none';
        } else {
            buttons[1].classList.add('active');
            personalTab.style.display = 'none';
            securityTab.style.display = 'block';
        }
    }
    </script>
</body>
</html>