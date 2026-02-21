<?php
session_start();
header('Content-Type: application/json');
include("../../connection.php");

$db_name = "hr4_hr_4";
$conn = $connections[$db_name] ?? die(json_encode(['success' => false, 'message' => 'Connection failed']));

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$emp_status_filter = isset($_GET['emp_status']) ? $_GET['emp_status'] : '';
$department_filter = isset($_GET['department']) ? $_GET['department'] : '';
$sub_department_filter = isset($_GET['sub_department']) ? $_GET['sub_department'] : '';
$employee_id_filter = isset($_GET['employee_id']) ? trim($_GET['employee_id']) : '';

$conditions = [];
$params = [];
$types = '';

if (!empty($employee_id_filter)) {
    $conditions[] = "e.id LIKE ?";
    $params[] = "%$employee_id_filter%";
    $types .= 's';
}
if (!empty($search)) {
    $conditions[] = "(e.first_name LIKE ? OR e.last_name LIKE ? OR e.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'sss';
}
if (!empty($emp_status_filter)) {
    $conditions[] = "e.employment_status = ?";
    $params[] = $emp_status_filter;
    $types .= 's';
}
if (!empty($department_filter)) {
    $conditions[] = "e.department_id = ?";
    $params[] = $department_filter;
    $types .= 'i';
}
if (!empty($sub_department_filter)) {
    $conditions[] = "e.sub_department_id = ?";
    $params[] = $sub_department_filter;
    $types .= 'i';
}

$where_clause = '';
if (!empty($conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $conditions);
}

// Count total
$count_query = "SELECT COUNT(*) as total FROM employees e $where_clause";
if (!empty($params)) {
    $stmt = $conn->prepare($count_query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $count_result = $stmt->get_result();
} else {
    $count_result = $conn->query($count_query);
}
$total_records = $count_result->fetch_assoc()['total'] ?? 0;
$total_pages = ceil($total_records / $limit);

// Fetch employees
$query = "SELECT e.id, e.department_id, e.sub_department_id, e.employee_code, 
                 e.first_name, e.middle_name, e.last_name, e.job, e.email, 
                 e.employment_status, e.work_status, e.hire_date,
                 d.name as department_name, sd.name as sub_department_name,
                 CONCAT(e.first_name, ' ', e.last_name) as full_name,
                 DATE_FORMAT(e.hire_date, '%M %d, %Y') as formatted_hire_date
          FROM employees e 
          LEFT JOIN departments d ON e.department_id = d.id 
          LEFT JOIN sub_departments sd ON e.sub_department_id = sd.id
          $where_clause 
          ORDER BY e.hire_date DESC 
          LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}

$employees = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'employees' => $employees,
    'total_pages' => $total_pages,
    'current_page' => $page,
    'total_records' => $total_records
]);
?>