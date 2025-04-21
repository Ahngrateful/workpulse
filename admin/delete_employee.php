<?php
// Database connection
require_once '../config.php';

// Delete employee
if (isset($_GET['id'])) {
    $employee_id = intval($_GET['id']); 

    $sql = "DELETE FROM employee WHERE employee_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employee_id); 
    if ($stmt->execute()) {
        header("Location: employee.php");
        exit;
    } else {
        echo "Error deleting employee: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
