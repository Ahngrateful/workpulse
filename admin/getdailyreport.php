<?php
// Database connection
require_once '../config.php';

// Get today's date (format: YYYY-MM-DD)
$today = date("Y-m-d");

// Fetch records only for today
$query = "SELECT * FROM attendances WHERE DATE(updated_at) = '$today'";
$result = $conn->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
