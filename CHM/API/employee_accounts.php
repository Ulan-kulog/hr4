<?php
// employees_api.php - REST API for Employee Accounts
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

// Helper function to format time elapsed (simplified version)
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    // Convert to array for easier handling
    $diffArray = [
        'y' => $diff->y,
        'm' => $diff->m,
        'd' => $diff->d,
        'h' => $diff->h,
        'i' => $diff->i,
        's' => $diff->s,
    ];
    
    // Calculate weeks
    $weeks = floor($diff->d / 7);
    $days = $diff->d % 7;
    
    $string = [];
    
    // Add weeks if needed
    if ($weeks > 0) {
        $string['w'] = $weeks . ' week' . ($weeks > 1 ? 's' : '');
    }
    
    // Add remaining days
    if ($days > 0) {
        $string['d'] = $days . ' day' . ($days > 1 ? 's' : '');
    }
    
    // Add other time units
    if ($diff->y > 0) {
        $string['y'] = $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
    }
    
    if ($diff->m > 0) {
        $string['m'] = $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
    }
    
    if ($diff->h > 0) {
        $string['h'] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
    }
    
    if ($diff->i > 0) {
        $string['i'] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
    }
    
    if ($diff->s > 0) {
        $string['s'] = $diff->s . ' second' . ($diff->s > 1 ? 's' : '');
    }
    
    // If no time has passed
    if (empty($string)) {
        return 'just now';
    }
    
    if (!$full) {
        // Return only the largest unit
        return reset($string) . ' ago';
    }
    
    return implode(', ', $string) . ' ago';
}

// Function to get all employee accounts with pagination and filters
function getEmployeeAccounts($conn, $params = [])
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
    if (!empty($params['employee_id'])) {
        $whereConditions[] = "employee_id = ?";
        $queryParams[] = $params['employee_id'];
        $types .= 's';
    }

    if (!empty($params['employee_code'])) {
        $whereConditions[] = "employee_code = ?";
        $queryParams[] = $params['employee_code'];
        $types .= 's';
    }

    if (!empty($params['job_title'])) {
        $whereConditions[] = "job_title = ?";
        $queryParams[] = $params['job_title'];
        $types .= 's';
    }

    if (!empty($params['search'])) {
        $whereConditions[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR employee_id LIKE ? OR employee_code LIKE ?)";
        $queryParams[] = "%" . $params['search'] . "%";
        $queryParams[] = "%" . $params['search'] . "%";
        $queryParams[] = "%" . $params['search'] . "%";
        $queryParams[] = "%" . $params['search'] . "%";
        $queryParams[] = "%" . $params['search'] . "%";
        $types .= 'sssss';
    }

    // Build WHERE clause
    $whereClause = '';
    if (!empty($whereConditions)) {
        $whereClause = "WHERE " . implode(" AND ", $whereConditions);
    }

    // Count total records for pagination
    $countSql = "SELECT COUNT(*) as total FROM employee_accounts $whereClause";
    if (!empty($queryParams)) {
        $countStmt = $conn->prepare($countSql);
        $countStmt->bind_param($types, ...$queryParams);
        $countStmt->execute();
        $totalResult = $countStmt->get_result();
    } else {
        $totalResult = $conn->query($countSql);
    }
    
    $totalRow = $totalResult->fetch_assoc();
    $totalRecords = $totalRow['total'] ?? 0;
    $totalPages = ceil($totalRecords / $limit);

    // Main query with pagination
    $sql = "SELECT 
                id,
                employee_id,
                employee_code,
                first_name,
                last_name,
                CONCAT(first_name, ' ', last_name) as full_name,
                email,
                job_title,
                DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at_formatted,
                DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at_formatted,
                created_at,
                updated_at
            FROM employee_accounts 
            $whereClause
            ORDER BY created_at DESC
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
            // Remove password from response for security
            if (isset($row['password'])) {
                unset($row['password']);
            }
            
            // Format timestamps
            if (!empty($row['created_at'])) {
                $row['created_ago'] = time_elapsed_string($row['created_at']);
            }
            
            if (!empty($row['updated_at'])) {
                $row['updated_ago'] = time_elapsed_string($row['updated_at']);
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

// Function to get employee account by ID
function getEmployeeAccountById($conn, $id)
{
    $sql = "SELECT 
                id,
                employee_id,
                employee_code,
                first_name,
                last_name,
                CONCAT(first_name, ' ', last_name) as full_name,
                email,
                job_title,
                DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at_formatted,
                DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') as updated_at_formatted,
                created_at,
                updated_at
            FROM employee_accounts 
            WHERE id = ? OR employee_id = ? OR employee_code = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $id, $id, $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Remove password from response for security
        if (isset($row['password'])) {
            unset($row['password']);
        }
        
        // Format timestamps
        if (!empty($row['created_at'])) {
            $row['created_ago'] = time_elapsed_string($row['created_at']);
        }
        
        if (!empty($row['updated_at'])) {
            $row['updated_ago'] = time_elapsed_string($row['updated_at']);
        }

        return [
            'success' => true,
            'data' => $row
        ];
    }

    return [
        'success' => false,
        'message' => 'Employee account not found'
    ];
}

// Function to get unique job titles
function getJobTitles($conn)
{
    // Get distinct job titles
    $sql = "SELECT DISTINCT job_title FROM employee_accounts WHERE job_title IS NOT NULL AND job_title != '' ORDER BY job_title";
    $result = $conn->query($sql);

    $titleList = [];
    while ($row = $result->fetch_assoc()) {
        $titleList[] = $row['job_title'];
    }

    return [
        'success' => true,
        'data' => [
            'job_titles' => $titleList
        ]
    ];
}

// Main request handler
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Handle GET requests
        if (isset($_GET['action']) && $_GET['action'] === 'jobtitles') {
            // Get job titles
            $response = getJobTitles($conn);
        } elseif (isset($_GET['id'])) {
            // Get single employee account by ID, employee_id, or employee_code
            $id = $_GET['id'];
            $response = getEmployeeAccountById($conn, $id);
        } else {
            // Get all employee accounts with filters
            $filters = [
                'page' => $_GET['page'] ?? 1,
                'limit' => $_GET['limit'] ?? 25,
                'employee_id' => $_GET['employee_id'] ?? null,
                'employee_code' => $_GET['employee_code'] ?? null,
                'job_title' => $_GET['job_title'] ?? null,
                'search' => $_GET['search'] ?? null
            ];
            $response = getEmployeeAccounts($conn, $filters);
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

// Convert top-level numeric array into an object map keyed by id
if (is_array($output)) {
    if (count($output) === 0) {
        $output = (object)[];
    } else {
        $isList = array_keys($output) === range(0, count($output) - 1);
        if ($isList) {
            // Try to find a suitable key for mapping
            $idCandidates = ['id', 'employee_id', 'employee_code'];
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