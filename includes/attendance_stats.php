<?php
/**
 * Attendance Statistics Functions
 * 
 * This file contains functions to calculate attendance statistics:
 * - Total records for current month
 * - Present count for today with percentage
 * - Late arrivals count with percentage
 * - Absences count with percentage
 */

/**
 * Get the total attendance records for the current month
 * 
 * @param object $conn Database connection object
 * @return int Total number of records for current month
 */
function getTotalMonthlyRecords($conn) {
    $currentMonth = date('m');
    $currentYear = date('Y');
    
    $query = "SELECT COUNT(*) as total FROM attendances 
              WHERE MONTH(date) = ? AND YEAR(date) = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $currentMonth, $currentYear);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total'];
}

/**
 * Get the count of present employees for today
 * 
 * @param object $conn Database connection object
 * @return array Present count and percentage
 */
function getPresentToday($conn) {
    $today = date('Y-m-d');
    
    // Get total employees
    $totalEmployeesQuery = "SELECT COUNT(*) as total FROM employee";
    $totalResult = $conn->query($totalEmployeesQuery);
    $totalEmployees = $totalResult->fetch_assoc()['total'];
    
    // Get present employees (employees who checked in today)
    $presentQuery = "SELECT COUNT(DISTINCT employee_id) as present FROM attendances 
                    WHERE date = ?";
    
    $stmt = $conn->prepare($presentQuery);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $present = $result->fetch_assoc()['present'];
    
    // Calculate percentage
    $percentage = ($totalEmployees > 0) ? round(($present / $totalEmployees) * 100, 1) : 0;
    
    return [
        'count' => $present,
        'percentage' => $percentage
    ];
}

/**
 * Get the count of late arrivals for today
 * 
 * @param object $conn Database connection object
 * @return array Late arrivals count and percentage
 */
function getLateArrivalsToday($conn) {
    $today = date('Y-m-d');
    
    // Get total employees present today
    $presentQuery = "SELECT COUNT(DISTINCT employee_id) as present FROM attendances 
                    WHERE date = ?";
    
    $stmt = $conn->prepare($presentQuery);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $presentToday = $result->fetch_assoc()['present'];
    
    // Get late arrivals
    $lateQuery = "SELECT COUNT(*) as late FROM attendances at
                  JOIN employee e ON at.employee_id = e.employee_id
                  WHERE at.date = ? AND
                  (HOUR(at.check_in) * 60 + MINUTE(at.check_in)) >= 
                  (HOUR(e.shift_start_time) * 60 + MINUTE(e.shift_start_time) + 30)";
    
    $stmt = $conn->prepare($lateQuery);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $late = $result->fetch_assoc()['late'];
    
    // Calculate percentage
    $percentage = ($presentToday > 0) ? round(($late / $presentToday) * 100, 1) : 0;
    
    return [
        'count' => $late,
        'percentage' => $percentage
    ];
}

/**
 * Get the count of absences for today
 * 
 * @param object $conn Database connection object
 * @return array Absences count and percentage
 */
function getAbsencesToday($conn) {
    $today = date('Y-m-d');
    
    // Get total employees
    $totalEmployeesQuery = "SELECT COUNT(*) as total FROM employee";
    $totalResult = $conn->query($totalEmployeesQuery);
    $totalEmployees = $totalResult->fetch_assoc()['total'];
    
    // Get present employees
    $presentQuery = "SELECT COUNT(DISTINCT employee_id) as present FROM attendances 
                    WHERE date = ?";
    
    $stmt = $conn->prepare($presentQuery);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $present = $result->fetch_assoc()['present'];
    
    // Calculate absences
    $absent = $totalEmployees - $present;
    
    // Calculate percentage
    $percentage = ($totalEmployees > 0) ? round(($absent / $totalEmployees) * 100, 1) : 0;
    
    return [
        'count' => $absent,
        'percentage' => $percentage
    ];
}

/**
 * Get all attendance statistics in a single array
 * 
 * @param object $conn Database connection object
 * @return array All attendance statistics
 */
function getAllAttendanceStats($conn) {
    return [
        'monthly_records' => getTotalMonthlyRecords($conn),
        'present' => getPresentToday($conn),
        'late' => getLateArrivalsToday($conn),
        'absent' => getAbsencesToday($conn)
    ];
}
?>
