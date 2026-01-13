<?php
session_start();

// Disable error display but enable logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Include connection
include("../../connection.php");

// Database connection
$db_name = "hr4_hr_4";
if (!isset($connections[$db_name])) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '❌ Database connection not found']);
    exit;
}
$conn = $connections[$db_name];

// Set proper headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Check if input is valid
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '❌ Invalid JSON data: ' . json_last_error_msg()]);
    exit;
}

if (!$input || empty($input)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '❌ No data received']);
    exit;
}

// Validate required fields
$required_fields = [
    'job_position', 'department', 'job_description', 'number_of_openings',
    'employment_type', 'salary_range', 'start_date', 'end_date',
    'required_education', 'required_experience', 'required_skills'
];

$missing_fields = [];
foreach ($required_fields as $field) {
    if (empty(trim($input[$field] ?? ''))) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '❌ Missing required fields: ' . implode(', ', $missing_fields)]);
    exit;
}

try {
    // Prepare SQL statement
    $sql = "INSERT INTO job_positions (
        job_position, department, job_description, number_of_openings, 
        employment_type, salary_range, start_date, end_date,
        required_education, required_experience, required_skills,
        preferred_qualifications, certifications, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'under review')";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }
    
    // Clean and validate data
    $job_position = trim($input['job_position']);
    $department = trim($input['department']);
    $job_description = trim($input['job_description']);
    $number_of_openings = intval($input['number_of_openings']);
    $employment_type = trim($input['employment_type']);
    $salary_range = trim($input['salary_range']);
    $start_date = trim($input['start_date']);
    $end_date = trim($input['end_date']);
    $required_education = trim($input['required_education']);
    $required_experience = trim($input['required_experience']);
    $required_skills = trim($input['required_skills']);
    $preferred_qualifications = !empty($input['preferred_qualifications']) ? trim($input['preferred_qualifications']) : null;
    $certifications = !empty($input['certifications']) ? trim($input['certifications']) : null;
    
    // Bind parameters
    $stmt->bind_param(
        "sssisssssssss",
        $job_position,
        $department,
        $job_description,
        $number_of_openings,
        $employment_type,
        $salary_range,
        $start_date,
        $end_date,
        $required_education,
        $required_experience,
        $required_skills,
        $preferred_qualifications,
        $certifications
    );
    
    // Execute the statement
    if ($stmt->execute()) {
        $new_id = $stmt->insert_id;
        
        http_response_code(201);
        echo json_encode([
            'success' => true, 
            'message' => '✅ Job position created successfully!',
            'id' => $new_id
        ]);
    } else {
        throw new Exception("Failed to execute statement: " . $stmt->error);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    error_log("Error creating job position: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => '❌ Error creating job position: ' . $e->getMessage()
    ]);
}

$conn->close();
?>