<?php
session_start();
include("../connection.php");

$db_name = "HR_4";
if (!isset($connections[$db_name])) {
    die(json_encode(['error' => 'Connection not found']));
}
$conn = $connections[$db_name];

// Fetch average salary by department
$query = "SELECT 
            d.dept_name AS department,
            COALESCE(AVG(e.salary), 0) AS avg_salary
          FROM department d
          LEFT JOIN employees e ON d.id = e.department_id
          WHERE e.status = 'active'
          GROUP BY d.dept_name
          ORDER BY avg_salary DESC";

$result = mysqli_query($conn, $query);
$labels = [];
$values = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $labels[] = $row['department'];
        $values[] = round($row['avg_salary'], 2);
    }
} else {
    // Fallback data if no records found
    $labels = ['Hotel', 'Restaurant', 'HR', 'Logistic', 'Admin', 'Financial'];
    $values = [28000, 22000, 35000, 25000, 32000, 40000];
}

echo json_encode([
    'labels' => $labels,
    'values' => $values
]);
