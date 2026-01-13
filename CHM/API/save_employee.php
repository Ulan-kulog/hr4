<?php
// job_positions_api.php - REST API for Job Positions
session_start();
include("../../connection.php");

// Connect to hr_4 database
$db_name = "hr4_hr_4";
$conn = $connections[$db_name] ?? die("❌ Connection not found for $db_name");

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow cross-origin requests
header('Access-Control-Allow-Methods: GET, POST, OPTIONS'); // Allow specific methods
header('Access-Control-Allow-Headers: Content-Type'); // Allow specific headers

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Function to get all job positions
function getJobPositions($conn, $params = []) {
    // Build query with optional filters
    $whereConditions = [];
    $queryParams = [];
    $types = '';
    
    // Check for filters
    if (!empty($params['status'])) {
        $whereConditions[] = "status = ?";
        $queryParams[] = $params['status'];
        $types .= 's';
    }
    
    if (!empty($params['department_id'])) {
        $whereConditions[] = "department_id = ?";
        $queryParams[] = $params['department_id'];
        $types .= 'i';
    }
    
    if (!empty($params['type'])) {
        $whereConditions[] = "type = ?";
        $queryParams[] = $params['type'];
        $types .= 's';
    }
    
    if (!empty($params['search'])) {
        $whereConditions[] = "(title LIKE ? OR description LIKE ?)";
        $queryParams[] = "%" . $params['search'] . "%";
        $queryParams[] = "%" . $params['search'] . "%";
        $types .= 'ss';
    }
    
    // Build WHERE clause
    $whereClause = '';
    if (!empty($whereConditions)) {
        $whereClause = "WHERE " . implode(" AND ", $whereConditions);
    }
    
    // (pagination removed) — return all matching job listings
    
    // Main query with department name join (build without LIMIT/OFFSET initially)
    $sql = "SELECT 
                jl.*,
                d.name as department_name,
                DATE_FORMAT(jl.created_at, '%Y-%m-%d %H:%i:%s') as created_at_formatted,
                DATE_FORMAT(jl.updated_at, '%Y-%m-%d %H:%i:%s') as updated_at_formatted,
                CASE 
                    WHEN jl.job_period_days IS NOT NULL THEN 
                        DATE_FORMAT(DATE_ADD(jl.created_at, INTERVAL jl.job_period_days DAY), '%Y-%m-%d')
                    ELSE NULL 
                END as job_end_date
            FROM job_listing jl
            LEFT JOIN departments d ON jl.department_id = d.id
            $whereClause
            ORDER BY jl.created_at DESC";

    // no pagination — fetch all matching rows
    
    // Prepare and execute query
    if (!empty($queryParams)) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$queryParams);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($sql);
    }
    
    $positions = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Format salary range
            $row['salary_range'] = '₱' . number_format($row['salary_min'], 2) . ' - ₱' . number_format($row['salary_max'], 2);
            
            // Add days remaining if job_end_date exists
            if ($row['job_end_date']) {
                $endDate = new DateTime($row['job_end_date']);
                $today = new DateTime();
                $interval = $today->diff($endDate);
                $row['days_remaining'] = $interval->days;
                $row['is_expired'] = $today > $endDate;
            }
            
            $positions[] = $row;
        }
    }
    
    return [
        'success' => true,
        'data' => $positions,
        // 'pagination' => [
        //     'page' => $page,
        //     'limit' => $limit,
        //     'total' => $totalCount,
        //     'total_pages' => ceil($totalCount / $limit)
        // ],
        'meta' => [
            'count' => count($positions),
            'filters' => $params
        ]
    ];
}

// Function to get single job position by ID
function getJobPositionById($conn, $id) {
    $sql = "SELECT 
                jl.*,
                d.name as department_name,
                DATE_FORMAT(jl.created_at, '%Y-%m-%d %H:%i:%s') as created_at_formatted,
                DATE_FORMAT(jl.updated_at, '%Y-%m-%d %H:%i:%s') as updated_at_formatted,
                CASE 
                    WHEN jl.job_period_days IS NOT NULL THEN 
                        DATE_FORMAT(DATE_ADD(jl.created_at, INTERVAL jl.job_period_days DAY), '%Y-%m-%d')
                    ELSE NULL 
                END as job_end_date
            FROM job_listing jl
            LEFT JOIN departments d ON jl.department_id = d.id
            WHERE jl.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Format salary range
        $row['salary_range'] = '₱' . number_format($row['salary_min'], 2) . ' - ₱' . number_format($row['salary_max'], 2);
        
        // Add days remaining if job_end_date exists
        if ($row['job_end_date']) {
            $endDate = new DateTime($row['job_end_date']);
            $today = new DateTime();
            $interval = $today->diff($endDate);
            $row['days_remaining'] = $interval->days;
            $row['is_expired'] = $today > $endDate;
        }
        
        return [
            'success' => true,
            'data' => $row
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Job position not found'
    ];
}

// Function to create new job position (POST)
function createJobPosition($conn, $data) {
    // Validate required fields
    $requiredFields = ['department_id', 'title', 'type', 'salary_min', 'salary_max', 'vacancies', 'status'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            return [
                'success' => false,
                'message' => "Missing required field: $field"
            ];
        }
    }
    
    // Validate type value
    $valid_types = ['full_time', 'part_time', 'contract', 'internship'];
    if (!in_array($data['type'], $valid_types)) {
        return [
            'success' => false,
            'message' => 'Invalid employment type'
        ];
    }
    
    // Validate salary range
    if ($data['salary_min'] > $data['salary_max']) {
        return [
            'success' => false,
            'message' => 'Minimum salary cannot be greater than maximum salary'
        ];
    }
    
    // Validate vacancies
    if ($data['vacancies'] < 1) {
        return [
            'success' => false,
            'message' => 'Number of vacancies must be at least 1'
        ];
    }
    
    // Validate job period
    if (isset($data['job_period_days']) && $data['job_period_days'] < 1) {
        return [
            'success' => false,
            'message' => 'Job posting duration must be at least 1 day'
        ];
    }
    
    // Validate status
    $valid_statuses = ['draft', 'open', 'closed'];
    if (!in_array($data['status'], $valid_statuses)) {
        return [
            'success' => false,
            'message' => 'Invalid status value'
        ];
    }
    
    // Default values
    $exam_required = isset($data['exam_required']) ? (int)$data['exam_required'] : 0;
    $job_period_days = $data['job_period_days'] ?? 30;
    
    try {
        $sql = "INSERT INTO job_listing (
                    department_id, 
                    title, 
                    type, 
                    salary_min, 
                    salary_max, 
                    vacancies, 
                    exam_required, 
                    status, 
                    job_period_days,
                    created_at,
                    updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->bind_param(
            "issddiisi",
            $data['department_id'],
            $data['title'],
            $data['type'],
            $data['salary_min'],
            $data['salary_max'],
            $data['vacancies'],
            $exam_required,
            $data['status'],
            $job_period_days
        );
        
        if ($stmt->execute()) {
            $id = $conn->insert_id;
            
            // Get the created record
            $createdJob = getJobPositionById($conn, $id);
            
            return [
                'success' => true,
                'message' => 'Job position created successfully',
                'id' => $id,
                'data' => $createdJob['data'] ?? null
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Database error: ' . $stmt->error
            ];
        }
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}

// Main request handler
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Handle GET requests
        if (isset($_GET['id'])) {
            // Get single job position by ID
            $id = (int)$_GET['id'];
            $response = getJobPositionById($conn, $id);
        } else {
            // Get all job positions with filters
            $filters = [
                'status' => $_GET['status'] ?? null,
                'department_id' => $_GET['department_id'] ?? null,
                'type' => $_GET['type'] ?? null,
                'search' => $_GET['search'] ?? null
            ];
            $response = getJobPositions($conn, $filters);
        }
        break;
        
    case 'POST':
        // Handle POST requests (create new job position)
        // Get input data from either form-data or JSON
        $inputData = [];
        
        if (!empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            // JSON input
            $json = file_get_contents('php://input');
            $inputData = json_decode($json, true);
        } else {
            // Form data
            $inputData = $_POST;
        }
        
        // Convert string numbers to actual numbers
        if (isset($inputData['department_id'])) $inputData['department_id'] = (int)$inputData['department_id'];
        if (isset($inputData['salary_min'])) $inputData['salary_min'] = (float)$inputData['salary_min'];
        if (isset($inputData['salary_max'])) $inputData['salary_max'] = (float)$inputData['salary_max'];
        if (isset($inputData['vacancies'])) $inputData['vacancies'] = (int)$inputData['vacancies'];
        if (isset($inputData['job_period_days'])) $inputData['job_period_days'] = (int)$inputData['job_period_days'];
        
        $response = createJobPosition($conn, $inputData);
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
?>