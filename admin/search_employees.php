<?php
require_once '../config.php';

$query = $_GET['query'] ?? '';

$sql = "SELECT * FROM employee WHERE employee_id LIKE ? OR name LIKE ?";
$stmt = $conn->prepare($sql);
$searchParam = "%$query%";
$stmt->bind_param("ss", $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();

$output = '';
while ($row = $result->fetch_assoc()) {
    $output .= "<tr>";
    $output .= "<td>" . htmlspecialchars($row['employee_ID']) . "</td>";
    $output .= "<td><img src='" . htmlspecialchars($row['photo']) . "' class='img-thumbnail' style='width: 60px; height: 60px;' onerror=\"this.src='placeholder.jpg'\" /></td>";
    $output .= "<td>" . htmlspecialchars($row['name']) . "</td>";
    $output .= "<td>" . htmlspecialchars($row['fingerprint_ID']) . "</td>";
    $output .= "<td>" . date("H:i", strtotime($row['shift_start_time'])) . " AM - " . date("H:i", strtotime($row['shift_end_time'])) . " PM</td>";
    $output .= "<td>" . htmlspecialchars($row['org']) . "</td>";
    $output .= "<td>" . htmlspecialchars($row['hire_date']) . "</td>";
    $output .= "<td><button
                                  class='action-btn openEditModal'
                                  title='Edit'
                                  data-id='" . htmlspecialchars($row['employee_ID']) . "'>
                                  <span class='material-icons'>edit</span>
                              </button>

                              <form action='delete_employee.php' method='get' style='display:inline;' onsubmit=\"return confirm('Are you sure you want to delete this employee?')\">
                                  <input type='hidden' name='id' value='" . htmlspecialchars($row['employee_ID']) . "'>
                                  <button type='submit' class='action-btn' title='Delete'>
                                      <span class='material-icons'>delete</span>
                                  </button>
                              </form></td>";
    $output .= "</tr>";
}

echo $output ?: "<tr><td colspan='8'>No results found.</td></tr>";
?>

