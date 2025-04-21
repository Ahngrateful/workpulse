<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['employee_id'])) {
    $employee_id = $_GET['employee_id'];

    $stmt = $conn->prepare("SELECT employee_id, name, fingerprint_id, hire_date, shift_start_time, shift_end_time, org FROM employee WHERE employee_id = ?");
    $stmt->bind_param("s", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode(['status' => 'success', 'data' => $row]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Employee not found.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
