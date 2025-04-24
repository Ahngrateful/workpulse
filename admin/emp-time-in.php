<?php
require_once '../config.php';

if (isset($_POST['submit'])) {
    $employee_id = $_POST['emp-id'];
    $time = $_POST['time'];
    $type = $_POST['type']; // 'time_in' or 'time_out'
    $date = $_POST['date'];

    // Convert to DateTime
    $datetime = DateTime::createFromFormat('Y-m-d H:i:s', "$date $time");

    if (!$datetime) {
        echo "<script>alert('Invalid date or time format!');</script>";
        exit;
    }

    $formatted_date = $datetime->format('Y-m-d');
    $formatted_time = $datetime->format('H:i:s');

    // Check if employee exists
    $check_emp = $conn->prepare("SELECT * FROM employee WHERE employee_ID = ?");
    $check_emp->bind_param("s", $employee_id);
    $check_emp->execute();
    $emp_result = $check_emp->get_result();
    $emp_exists = $emp_result->fetch_object();

    if ($emp_exists) {
        if ($type == 'check_in') {
            // Check if already timed in
            $check = $conn->prepare("SELECT * FROM attendances WHERE employee_ID = ? AND date = ?");
            $check->bind_param("ss", $employee_id, $formatted_date);
            $check->execute();
            $result = $check->get_result();
            $data = $result->fetch_object();

            if ($data && $data->check_in) {
                echo "<script>alert('You have already timed in for today!');</script>";
            } else {
                $insert = $conn->prepare("INSERT INTO attendances (employee_ID, date, check_in) VALUES (?, ?, ?)");
                $insert->bind_param("sss", $employee_id, $formatted_date, $formatted_time);
                $insert->execute();
                echo "<script>alert('You have successfully timed in!');</script>";
            }
        } else { // time_out
            // Check attendance
            $check_att = $conn->prepare("SELECT * FROM attendances WHERE employee_ID = ? AND date = ?");
            $check_att->bind_param("ss", $employee_id, $formatted_date);
            $check_att->execute();
            $att_result = $check_att->get_result();
            $attendance = $att_result->fetch_object();

            if ($attendance && $attendance->check_in) {
                if ($emp_exists->shift_end_time > $formatted_time) {
                    $update = $conn->prepare("UPDATE attendances SET check_out = ? WHERE employee_ID = ? AND date = ?");
                    $update->bind_param("sss", $formatted_time, $employee_id, $formatted_date);
                    $update->execute();
                    echo "<script>alert('You can time out but you have not reached your shift end time!');</script>";
                } else {
                    $update = $conn->prepare("UPDATE attendances SET check_out = ? WHERE employee_ID = ? AND date = ?");
                    $update->bind_param("sss", $formatted_time, $employee_id, $formatted_date);
                    $update->execute();
                    echo "<script>alert('You have successfully timed out!');</script>";
                }
            } else {
                echo "<script>alert('You have not timed in yet!');</script>";
            }
        }
    } else {
        echo "<script>alert('Employee ID does not exist!');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RCGI | TIME IN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" href="pics/rcgiph_logo.jpg" type="image/x-icon">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 15px;
            background: #F3F4F6;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .timein-container {
            background: #E5E0D8;
            border: 2px solid #E5E0D8;
            box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .logo {
            display: block;
            margin: 0 auto 10px;
            width: 60px;
        }

        h4 {
            font-size: 18px;
            font-style: italic;
            margin-bottom: 10px;
        }

        #time {
            font-size: 18px;
            background: #DCE7D8;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
            font-weight: bold;
        }

        .clock-icon {
            font-size: 40px;
            margin: 15px 0;
            color: black;
        }

        select,
        input {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input::placeholder {
            text-align: center;
        }

        .enter-button {
            width: 100%;
            padding: 10px;
            background-color: #CC9D61;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 15px;
        }

        .enter-button:hover {
            background-color: #E0BE92;
        }
    </style>
</head>

<body>

    <div class="timein-container">
        <img src="pics/rcgiph_logo.jpg" alt="Company Logo" class="logo">
        <form action="emp-time-in.php" method="POST">
            <h4 name="date" id="date"></h4>
            <span name="time" id="time">--:--:--</span>
            <div class="clock-icon"><i class="fas fa-clock"></i></div>

            <select name="type" class="form-control">
                <option value="check_in" selected>TIME IN</option>
                <option value="check_out">TIME OUT</option>
            </select>

            <input name="emp-id" type="text" class="form-control" placeholder="Employee ID" required>
            <input name="date" type="hidden" id="hidden-date">
            <input name="time" type="hidden" id="hidden-time">

            <button name="submit" type="submit" class="enter-button">ENTER</button>

        </form>
    </div>

    <script>
        function updateTime() {
            const now = new Date();
            const dateOptions = {
                weekday: 'long',
                month: 'long',
                day: '2-digit',
                year: 'numeric'
            };
            const timeOptions = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };

            document.getElementById('date').textContent = now.toLocaleDateString('en-US', dateOptions);
            document.getElementById('time').textContent = now.toLocaleTimeString('en-US', timeOptions);
            document.getElementById('hidden-date').value = now.toISOString().split('T')[0];
            document.getElementById('hidden-time').value = now.toTimeString().split(' ')[0];
        }

        setInterval(updateTime, 1000);
        updateTime();
    </script>

</body>

</html>