<?php
require_once '../config.php';

$stmt = $mysqli->prepare("
    SELECT 
        attendances.*,
        users.first_name, 
        users.last_name,
        (SELECT COUNT(*) FROM users) as total_users,
        (SELECT COUNT(*) FROM attendances WHERE date = CURDATE()) as present_users,
        shifts.start_time
    FROM users 
    JOIN attendances 
        ON attendances.user_id = users.user_id 
    JOIN shifts
        ON shifts.shift_id = users.shift_id
    WHERE attendances.date = CURDATE() 
    ORDER BY updated_at 
    LIMIT 6
");
$stmt->execute();
$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    // Convert times to comparable format
    $check_in_time = strtotime($row["check_in"]);
    $shift_start_time = strtotime($row["start_time"]);
    
    // If check-in time is later than shift start time, it's a late arrival
    $is_late = $check_in_time > $shift_start_time;
    $late_arrivals_today = $is_late ? 1 : 0;
    
    if($row["recent_punch_type"] == "check_in"){
        $type = "Checked in";
        $time = $row["check_in"];                
    }elseif($row["recent_punch_type"] == "check_out"){
        $type = "Checked out";
        $time = $row["check_out"];
    }elseif($row["recent_punch_type"] == "break_in"){
        $type = "Break in";
        $time = $row["break_in"]; 
    }elseif($row["recent_punch_type"] == "break_out"){
        $type = "Break out";
        $time = $row["break_out"];
    }elseif($row["recent_punch_type"] == "ot_in"){
        $type = "OT in";
        $time = $row["ot_in"];
    }elseif($row["recent_punch_type"] == "ot_out"){
        $type = "OT out";
        $time = $row["ot_out"]; 
    }else{
        $type = "Not punched";
        $time = "Not punched"; 
    }
    
    $time = date('g:i A', strtotime($time));
    $total_users = $row['total_users'];
    $present_users = $row['present_users'];
    $absent_users = $total_users - $present_users;
    $attendance_rate = ($present_users / $total_users) * 100;
    $absence_rate = ($absent_users / $total_users) * 100;
    


    $data[] = [
        'type' => $type,
        'time' => $time,
        'name' => $row['first_name'] . ' ' . $row['last_name'],
    ];

}
$dashboard_data = [
    'attendance_rate' => $attendance_rate,
    'absent_users' => $absent_users,
    'total_users' => $total_users,
    'present_users' => $present_users,
    'data' => $data
];
header("Content-Type: application/json");
// Optional: Convert to JSON if needed
echo json_encode($dashboard_data);
?>