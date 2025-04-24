
<?php
require_once '../config.php';

// Get current year and month
$currentYear = date("Y");
$currentMonth = date("m");

// Fetch records for the current month
$query = "
    SELECT * 
    FROM attendances 
    WHERE YEAR(updated_at) = '$currentYear' 
      AND MONTH(updated_at) = '$currentMonth'
";

$result = $conn->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
