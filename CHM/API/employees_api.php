<?php
// employees_api.php - REST API for Employees
session_start();
include("../../connection.php");

// Connect to hr4_hr_4 database
$db_name = "hr4_hr_4";
$conn = $connections[$db_name] ?? die(json_encode(['success' => false, 'message' => "Connection not found for $db_name"]));

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Function to get all employees with pagination and filters
function getEmployees($conn, $params = [])
{
    // Default values
    $page = max(1, (int)($params['page'] ?? 1));
    $limit = max(1, min(100, (int)($params['limit'] ?? 25)));
    $offset = ($page - 1) * $limit;

    // Build query with optional filters
    $whereConditions = [];
    $queryParams = [];
    $types = '';

    // Check for filters
    if (!empty($params['department'])) {
        $whereConditions[] = "department = ?";
        $queryParams[] = $params['department'];
        $types .= 's';
    }

    if (!empty($params['sub_department'])) {
        $whereConditions[] = "sub_department = ?";
        $queryParams[] = $params['sub_department'];
        $types .= 's';
    }

    if (!empty($params['employment_status'])) {
        $whereConditions[] = "employment_status = ?";
        $queryParams[] = $params['employment_status'];
        $types .= 's';
    }

    if (!empty($params['work_status'])) {
        $whereConditions[] = "work_status = ?";
        $queryParams[] = $params['work_status'];
        $types .= 's';
    }

    // Check if searching by employee ID - use the 'id' column
    if (!empty($params['employee_id'])) {
        $whereConditions[] = "id = ?";
        $queryParams[] = $params['employee_id'];
        $types .= 's';
    }

    if (!empty($params['search'])) {
        $whereConditions[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone_number LIKE ?)";
        $queryParams[] = "%" . $params['search'] . "%";
        $queryParams[] = "%" . $params['search'] . "%";
        $queryParams[] = "%" . $params['search'] . "%";
        $queryParams[] = "%" . $params['search'] . "%";
        $types .= 'ssss';
    }

    // Build WHERE clause
    $whereClause = '';
    if (!empty($whereConditions)) {
        $whereClause = "WHERE " . implode(" AND ", $whereConditions);
    }

    // Count total records for pagination
    $countSql = "SELECT COUNT(*) as total FROM employees $whereClause";
    $totalResult = $conn->query($countSql);
    if (!empty($queryParams)) {
        $countStmt = $conn->prepare($countSql);
        $countStmt->bind_param($types, ...$queryParams);
        $countStmt->execute();
        $totalResult = $countStmt->get_result();
    }
    $totalRow = $totalResult->fetch_assoc();
    $totalRecords = $totalRow['total'] ?? 0;
    $totalPages = ceil($totalRecords / $limit);

    // Main query with pagination - REMOVED employee_id column
    $sql = "SELECT 
                id,
                first_name,
                middle_name,
                last_name,
                CONCAT(first_name, ' ', last_name) as full_name,
                job,
                date_of_birth,
                phone_number,
                email,
                address,
                gender,
                emergency_contact_name,
                emergency_contact_number,
                emergency_contact_relationship,
                mentors,
                hire_date,
                salary,
                employment_status,
                work_status,
                separation_status,
                has_contract,
                basic_salary,
                department_id,
                sub_department_id,
                DATE_FORMAT(hire_date, '%Y-%m-%d') as hire_date_formatted,
                DATE_FORMAT(date_of_birth, '%Y-%m-%d') as date_of_birth_formatted,
                CASE 
                    WHEN employment_status = 'active' THEN 'bg-green-100 text-green-800'
                    WHEN employment_status = 'terminated' THEN 'bg-red-100 text-red-800'
                    WHEN employment_status = 'resigned' THEN 'bg-yellow-100 text-yellow-800'
                    WHEN employment_status = 'pending' THEN 'bg-blue-100 text-blue-800'
                    ELSE 'bg-gray-100 text-gray-800'
                END as status_class
            FROM employees 
            $whereClause
            ORDER BY hire_date DESC
            LIMIT ? OFFSET ?";

    // Add pagination parameters to query
    $queryParams[] = $limit;
    $queryParams[] = $offset;
    $types .= 'ii';

    // Prepare and execute query
    $employees = [];
    if (!empty($queryParams)) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$queryParams);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($sql);
    }

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Calculate age from date_of_birth
            if ($row['date_of_birth']) {
                $birthDate = new DateTime($row['date_of_birth']);
                $today = new DateTime();
                $age = $today->diff($birthDate)->y;
                $row['age'] = $age;
            }

            // Format salary
            if ($row['salary']) {
                $row['salary_formatted'] = '₱' . number_format($row['salary'], 2);
            }

            $employees[] = $row;
        }
    }

    return [
        'success' => true,
        'data' => $employees,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $totalRecords,
            'limit' => $limit,
            'offset' => $offset
        ],
        'filters' => $params
    ];
}

// Function to get employee by ID
function getEmployeeById($conn, $id)
{
    $sql = "SELECT 
                *,
                DATE_FORMAT(hire_date, '%Y-%m-%d') as hire_date_formatted,
                DATE_FORMAT(date_of_birth, '%Y-%m-%d') as date_of_birth_formatted
            FROM employees 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Calculate age
        if ($row['date_of_birth']) {
            $birthDate = new DateTime($row['date_of_birth']);
            $today = new DateTime();
            $age = $today->diff($birthDate)->y;
            $row['age'] = $age;
        }

        // Format salary
        if ($row['salary']) {
            $row['salary_formatted'] = '₱' . number_format($row['salary'], 2);
        }

        return [
            'success' => true,
            'data' => $row
        ];
    }

    return [
        'success' => false,
        'message' => 'Employee not found'
    ];
}

// Function to get unique departments and sub-departments
function getDepartments($conn)
{
    $departments = [];

    // Get distinct departments
    $deptSql = "SELECT DISTINCT department FROM employees WHERE department IS NOT NULL AND department != '' ORDER BY department";
    $deptResult = $conn->query($deptSql);

    $deptList = [];
    while ($row = $deptResult->fetch_assoc()) {
        $deptList[] = $row['department'];
    }

    // Get distinct sub-departments
    $subDeptSql = "SELECT DISTINCT sub_department FROM employees WHERE sub_department IS NOT NULL AND sub_department != '' ORDER BY sub_department";
    $subDeptResult = $conn->query($subDeptSql);

    $subDeptList = [];
    while ($row = $subDeptResult->fetch_assoc()) {
        $subDeptList[] = $row['sub_department'];
    }

    return [
        'success' => true,
        'data' => [
            'departments' => $deptList,
            'sub_departments' => $subDeptList
        ]
    ];
}

// Main request handler
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Handle GET requests
        if (isset($_GET['action']) && $_GET['action'] === 'departments') {
            // Get departments and sub-departments
            $response = getDepartments($conn);
        } elseif (isset($_GET['id'])) {
            // Get single employee by ID
            $id = $_GET['id'];
            $response = getEmployeeById($conn, $id);
        } else {
            // Get all employees with filters
            $filters = [
                'page' => $_GET['page'] ?? 1,
                'limit' => $_GET['limit'] ?? 25,
                'department' => $_GET['department'] ?? null,
                'sub_department' => $_GET['sub_department'] ?? null,
                'employment_status' => $_GET['employment_status'] ?? null,
                'work_status' => $_GET['work_status'] ?? null,
                'employee_id' => $_GET['employee_id'] ?? null,
                'search' => $_GET['search'] ?? null
            ];
            $response = getEmployees($conn, $filters);
        }
        break;

    default:
        $response = [
            'success' => false,
            'message' => 'Method not allowed'
        ];
        http_response_code(405);
        break;
}

// Send response
echo json_encode($response, JSON_PRETTY_PRINT);

$conn->close();
