<?php
require_once '../config.php';
// Connect to DB


// Get the latest employee_ID
$result = $conn->query("SELECT MAX(employee_id) AS last_id FROM employee");
$row = $result->fetch_assoc();
$nextID = $row['last_id'] ? $row['last_id'] + 1 : 202507;

echo json_encode(["next_id" => $nextID]);
$conn->close();
?>