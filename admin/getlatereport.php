<?php
require_once '../config.php';

$today = date("Y-m-d");

// SQL to get only 'Late' arrivals
$sql = "
SELECT 
    at.*,  
    e.name,
    CASE 
        WHEN (HOUR(at.check_in) * 60 + MINUTE(at.check_in)) >= (HOUR(e.shift_start_time) * 60 + MINUTE(e.shift_start_time) + 30) 
        THEN 'Late'
        WHEN (HOUR(at.check_in) * 60 + MINUTE(at.check_in)) >= (HOUR(e.shift_start_time) * 60 + MINUTE(e.shift_start_time)) 
             AND (HOUR(at.check_in) * 60 + MINUTE(at.check_in)) < (HOUR(e.shift_start_time) * 60 + MINUTE(e.shift_start_time) + 30) 
        THEN 'On Time'
        ELSE 'Undertime'
    END AS status
FROM 
    attendances at  
JOIN 
    employee e ON at.employee_id = e.employee_id
WHERE 
    DATE(at.updated_at) = '$today'
HAVING 
    status = 'Late'
";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
