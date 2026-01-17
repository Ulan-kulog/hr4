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

    // Attach department and sub-department names for returned employees
    $deptIds = [];
    $subDeptIds = [];
    foreach ($employees as $e) {
        if (!empty($e['department_id'])) $deptIds[] = (int)$e['department_id'];
        if (!empty($e['sub_department_id'])) $subDeptIds[] = (int)$e['sub_department_id'];
    }
    $deptIds = array_values(array_unique(array_filter($deptIds)));
    $subDeptIds = array_values(array_unique(array_filter($subDeptIds)));

    $deptMap = [];
    if (!empty($deptIds)) {
        $ids = implode(',', array_map('intval', $deptIds));
        $dsql = "SELECT id, name FROM departments WHERE id IN ($ids)";
        $dres = $conn->query($dsql);
        if ($dres) {
            while ($dr = $dres->fetch_assoc()) {
                $deptMap[(int)$dr['id']] = $dr['name'];
            }
        }
    }

    $subDeptMap = [];
    if (!empty($subDeptIds)) {
        // Try sub_departments table first
        $exists = @$conn->query("SELECT 1 FROM sub_departments LIMIT 1");
        $ids = implode(',', array_map('intval', $subDeptIds));
        if ($exists !== false) {
            $sSql = "SELECT id, name FROM sub_departments WHERE id IN ($ids)";
            $sRes = $conn->query($sSql);
            if ($sRes) {
                while ($sr = $sRes->fetch_assoc()) {
                    $subDeptMap[(int)$sr['id']] = $sr['name'];
                }
            }
        } else {
            // Fallback to departments table (some schemas store sub-departments in same table)
            $sSql = "SELECT id, name FROM departments WHERE id IN ($ids)";
            $sRes = $conn->query($sSql);
            if ($sRes) {
                while ($sr = $sRes->fetch_assoc()) {
                    $subDeptMap[(int)$sr['id']] = $sr['name'];
                }
            }
        }
    }

    // Inject names into employee rows
    foreach ($employees as &$er) {
        $er['department'] = null;
        $er['sub_department'] = null;
        if (!empty($er['department_id'])) {
            $key = (int)$er['department_id'];
            if (isset($deptMap[$key])) $er['department'] = $deptMap[$key];
        }
        if (!empty($er['sub_department_id'])) {
            $skey = (int)$er['sub_department_id'];
            if (isset($subDeptMap[$skey])) $er['sub_department'] = $subDeptMap[$skey];
        }
    }
    unset($er);

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

// Send response: emit only the `data` payload (exclude `success`, `pagination`, etc.)
$output = null;
if (is_array($response) && array_key_exists('data', $response)) {
    $output = $response['data'];
} else {
    $output = (object)[];
}

// Convert top-level numeric array into an object map keyed by id (remove top-level array)
if (is_array($output)) {
    if (count($output) === 0) {
        $output = (object)[];
    } else {
        $isList = array_keys($output) === range(0, count($output) - 1);
        if ($isList) {
            $idCandidates = ['id', 'employee_id', 'emp_id', 'user_id'];
            $sample = reset($output);
            $idKey = null;
            if (is_array($sample)) {
                foreach ($idCandidates as $k) {
                    if (array_key_exists($k, $sample)) {
                        $idKey = $k;
                        break;
                    }
                }
            } elseif (is_object($sample)) {
                foreach ($idCandidates as $k) {
                    if (property_exists($sample, $k)) {
                        $idKey = $k;
                        break;
                    }
                }
            }

            if ($idKey) {
                $map = [];
                foreach ($output as $item) {
                    if (is_array($item) && array_key_exists($idKey, $item)) {
                        $map[$item[$idKey]] = $item;
                    } elseif (is_object($item) && isset($item->$idKey)) {
                        $map[$item->$idKey] = $item;
                    } else {
                        $map[] = $item;
                    }
                }
                $output = $map;
            }
        }
    }
}

// If the response indicates failure, set a 400 status code
if (is_array($response) && array_key_exists('success', $response) && $response['success'] === false) {
    http_response_code(400);
}

echo json_encode($output, JSON_PRETTY_PRINT);

$conn->close();
