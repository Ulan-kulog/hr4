<?php
// employees_api.php - REST API for Employee Accounts with LEFT JOIN but no null values
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

// Function to get all employee accounts with LEFT JOIN to employees table, ensuring no nulls in key fields
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
        $whereConditions[] = "ea.employee_id = ?";
        $queryParams[] = $params['employee_id'];
        $types .= 's';
    }

    if (!empty($params['employee_code'])) {
        $whereConditions[] = "ea.employee_code = ?";
        $queryParams[] = $params['employee_code'];
        $types .= 's';
    }

    if (!empty($params['job_title'])) {
        $whereConditions[] = "ea.job_title = ?";
        $queryParams[] = $params['job_title'];
        $types .= 's';
    }

    if (!empty($params['search'])) {
        $whereConditions[] = "(ea.first_name LIKE ? OR ea.last_name LIKE ? OR ea.email LIKE ? OR ea.employee_id LIKE ? OR ea.employee_code LIKE ? OR e.phone_number LIKE ? OR e.address LIKE ? OR COALESCE(e.first_name, ea.first_name) LIKE ? OR COALESCE(e.last_name, ea.last_name) LIKE ?)";
        $queryParams[] = "%" . $params['search'] . "%";
        $queryParams[] = "%" . $params['search'] . "%";
        $queryParams[] = "%" . $params['search'] . "%";
        $queryParams[] = "%" . $params['search'] . "%";
        $queryParams[] = "%" . $params['search'] . "%";
        $queryParams[] = "%" . $params['search'] . "%";
        $queryParams[] = "%" . $params['search'] . "%";
        $queryParams[] = "%" . $params['search'] . "%";
        $queryParams[] = "%" . $params['search'] . "%";
        $types .= 'sssssssss';
    }

    if (!empty($params['employment_status'])) {
        $whereConditions[] = "COALESCE(e.employment_status, 'Unknown') = ?";
        $queryParams[] = $params['employment_status'];
        $types .= 's';
    }

    if (!empty($params['department_id'])) {
        $whereConditions[] = "e.department_id = ?";
        $queryParams[] = $params['department_id'];
        $types .= 's';
    }

    if (!empty($params['work_status'])) {
        $whereConditions[] = "COALESCE(e.work_status, 'Unknown') = ?";
        $queryParams[] = $params['work_status'];
        $types .= 's';
    }

    // Build WHERE clause
    $whereClause = '';
    if (!empty($whereConditions)) {
        $whereClause = "WHERE " . implode(" AND ", $whereConditions);
    }

    // Count total records for pagination
    $countSql = "SELECT COUNT(*) as total 
                FROM employee_accounts ea 
                LEFT JOIN employees e ON ea.employee_id = e.id 
                $whereClause";
    
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

    // Main query with LEFT JOIN to employees table and pagination
    // Using COALESCE to ensure no null values in key fields
    $sql = "SELECT 
                -- Account fields (always present)
                ea.id,
                ea.employee_id,
                ea.employee_code,
                ea.first_name as account_first_name,
                ea.last_name as account_last_name,
                CONCAT(ea.first_name, ' ', ea.last_name) as account_full_name,
                ea.email,
                ea.job_title,
                DATE_FORMAT(ea.created_at, '%Y-%m-%d %H:%i:%s') as account_created_at_formatted,
                DATE_FORMAT(ea.updated_at, '%Y-%m-%d %H:%i:%s') as account_updated_at_formatted,
                ea.created_at as account_created_at,
                ea.updated_at as account_updated_at,
                
                -- Employee table fields with COALESCE for no nulls
                COALESCE(e.id, ea.employee_id) as emp_id,
                COALESCE(e.employee_code, ea.employee_code) as emp_employee_code,
                e.applicant_id,
                COALESCE(e.first_name, ea.first_name) as emp_first_name,
                e.middle_name as emp_middle_name,
                COALESCE(e.last_name, ea.last_name) as emp_last_name,
                CONCAT(
                    COALESCE(e.first_name, ea.first_name), 
                    ' ', 
                    IFNULL(e.middle_name, ''), 
                    ' ', 
                    COALESCE(e.last_name, ea.last_name)
                ) as emp_full_name,
                COALESCE(e.job, ea.job_title) as emp_job,
                DATE_FORMAT(e.date_of_birth, '%Y-%m-%d') as emp_date_of_birth,
                COALESCE(e.phone_number, 'Not Available') as emp_phone_number,
                COALESCE(e.email, ea.email) as emp_email,
                COALESCE(e.address, 'Not Available') as emp_address,
                COALESCE(e.gender, 'Not Specified') as emp_gender,
                COALESCE(e.emergency_contact_name, 'Not Available') as emergency_contact_name,
                COALESCE(e.emergency_contact_number, 'Not Available') as emergency_contact_number,
                COALESCE(e.emergency_contact_relationship, 'Not Available') as emergency_contact_relationship,
                e.mentors,
                DATE_FORMAT(e.hire_date, '%Y-%m-%d') as emp_hire_date,
                COALESCE(e.salary, 0) as salary,
                COALESCE(e.employment_status, 'Unknown') as employment_status,
                COALESCE(e.work_status, 'Unknown') as work_status,
                COALESCE(e.separation_status, 'Not Specified') as separation_status,
                e.sub_department_id,
                e.task_id,
                COALESCE(e.has_contract, 0) as has_contract,
                DATE_FORMAT(e.created_at, '%Y-%m-%d %H:%i:%s') as emp_created_at_formatted,
                DATE_FORMAT(e.updated_at, '%Y-%m-%d %H:%i:%s') as emp_updated_at_formatted,
                e.created_at as emp_created_at,
                e.updated_at as emp_updated_at,
                COALESCE(e.basic_salary, 0) as basic_salary,
                e.department_id,
                
                -- Additional calculated fields
                CASE 
                    WHEN e.id IS NOT NULL THEN 'Both Account and Employee Record'
                    ELSE 'Account Only (No Employee Record)'
                END as record_status,
                
                -- Age calculation (if date_of_birth exists)
                CASE 
                    WHEN e.date_of_birth IS NOT NULL THEN TIMESTAMPDIFF(YEAR, e.date_of_birth, CURDATE())
                    ELSE NULL
                END as emp_age,
                
                -- Employment duration (if hire_date exists)
                CASE 
                    WHEN e.hire_date IS NOT NULL THEN TIMESTAMPDIFF(YEAR, e.hire_date, CURDATE())
                    ELSE NULL
                END as employment_years,
                CASE 
                    WHEN e.hire_date IS NOT NULL THEN TIMESTAMPDIFF(MONTH, e.hire_date, CURDATE()) % 12
                    ELSE NULL
                END as employment_months
                
            FROM employee_accounts ea 
            LEFT JOIN employees e ON ea.employee_id = e.id 
            $whereClause
            ORDER BY ea.created_at DESC
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
            
            // Format timestamps for account
            if (!empty($row['account_created_at'])) {
                $row['account_created_ago'] = time_elapsed_string($row['account_created_at']);
            }
            
            if (!empty($row['account_updated_at'])) {
                $row['account_updated_ago'] = time_elapsed_string($row['account_updated_at']);
            }
            
            // Format timestamps for employee (if exists)
            if (!empty($row['emp_created_at'])) {
                $row['emp_created_ago'] = time_elapsed_string($row['emp_created_at']);
            }
            
            if (!empty($row['emp_updated_at'])) {
                $row['emp_updated_ago'] = time_elapsed_string($row['emp_updated_at']);
            }
            
            // Calculate employment duration string (if data exists)
            if (!empty($row['employment_years'])) {
                $duration = [];
                if ($row['employment_years'] > 0) {
                    $duration[] = $row['employment_years'] . ' year' . ($row['employment_years'] > 1 ? 's' : '');
                }
                if ($row['employment_months'] > 0) {
                    $duration[] = $row['employment_months'] . ' month' . ($row['employment_months'] > 1 ? 's' : '');
                }
                $row['employment_duration'] = implode(', ', $duration);
            } else if (!empty($row['employment_months'])) {
                $row['employment_duration'] = $row['employment_months'] . ' month' . ($row['employment_months'] > 1 ? 's' : '');
            } else {
                $row['employment_duration'] = 'Not Available';
            }
            
            // Format salary fields
            if (!empty($row['salary']) && $row['salary'] > 0) {
                $row['salary_formatted'] = '₱' . number_format($row['salary'], 2);
            } else {
                $row['salary_formatted'] = 'Not Available';
                $row['salary'] = 0;
            }
            
            if (!empty($row['basic_salary']) && $row['basic_salary'] > 0) {
                $row['basic_salary_formatted'] = '₱' . number_format($row['basic_salary'], 2);
            } else {
                $row['basic_salary_formatted'] = 'Not Available';
                $row['basic_salary'] = 0;
            }
            
            // Format phone number
            if (!empty($row['emp_phone_number']) && $row['emp_phone_number'] !== 'Not Available') {
                $row['emp_phone_formatted'] = preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', $row['emp_phone_number']);
            } else {
                $row['emp_phone_formatted'] = 'Not Available';
            }
            
            if (!empty($row['emergency_contact_number']) && $row['emergency_contact_number'] !== 'Not Available') {
                $row['emergency_contact_formatted'] = preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', $row['emergency_contact_number']);
            } else {
                $row['emergency_contact_formatted'] = 'Not Available';
            }
            
            // Set default values for null fields
            if (empty($row['emp_age'])) {
                $row['emp_age'] = 'Not Available';
            }
            
            if (empty($row['emp_date_of_birth'])) {
                $row['emp_date_of_birth'] = 'Not Available';
            }
            
            if (empty($row['emp_hire_date'])) {
                $row['emp_hire_date'] = 'Not Available';
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

// Function to get employee account by ID with LEFT JOIN but no nulls
function getEmployeeAccountById($conn, $id)
{
    $sql = "SELECT 
                -- Account fields (always present)
                ea.id,
                ea.employee_id,
                ea.employee_code,
                ea.first_name as account_first_name,
                ea.last_name as account_last_name,
                CONCAT(ea.first_name, ' ', ea.last_name) as account_full_name,
                ea.email,
                ea.job_title,
                DATE_FORMAT(ea.created_at, '%Y-%m-%d %H:%i:%s') as account_created_at_formatted,
                DATE_FORMAT(ea.updated_at, '%Y-%m-%d %H:%i:%s') as account_updated_at_formatted,
                ea.created_at as account_created_at,
                ea.updated_at as account_updated_at,
                
                -- Employee table fields with COALESCE for no nulls
                COALESCE(e.id, ea.employee_id) as emp_id,
                COALESCE(e.employee_code, ea.employee_code) as emp_employee_code,
                e.applicant_id,
                COALESCE(e.first_name, ea.first_name) as emp_first_name,
                e.middle_name as emp_middle_name,
                COALESCE(e.last_name, ea.last_name) as emp_last_name,
                CONCAT(
                    COALESCE(e.first_name, ea.first_name), 
                    ' ', 
                    IFNULL(e.middle_name, ''), 
                    ' ', 
                    COALESCE(e.last_name, ea.last_name)
                ) as emp_full_name,
                COALESCE(e.job, ea.job_title) as emp_job,
                DATE_FORMAT(e.date_of_birth, '%Y-%m-%d') as emp_date_of_birth,
                COALESCE(e.phone_number, 'Not Available') as emp_phone_number,
                COALESCE(e.email, ea.email) as emp_email,
                COALESCE(e.address, 'Not Available') as emp_address,
                COALESCE(e.gender, 'Not Specified') as emp_gender,
                COALESCE(e.emergency_contact_name, 'Not Available') as emergency_contact_name,
                COALESCE(e.emergency_contact_number, 'Not Available') as emergency_contact_number,
                COALESCE(e.emergency_contact_relationship, 'Not Available') as emergency_contact_relationship,
                e.mentors,
                DATE_FORMAT(e.hire_date, '%Y-%m-%d') as emp_hire_date,
                COALESCE(e.salary, 0) as salary,
                COALESCE(e.employment_status, 'Unknown') as employment_status,
                COALESCE(e.work_status, 'Unknown') as work_status,
                COALESCE(e.separation_status, 'Not Specified') as separation_status,
                e.sub_department_id,
                e.task_id,
                COALESCE(e.has_contract, 0) as has_contract,
                DATE_FORMAT(e.created_at, '%Y-%m-%d %H:%i:%s') as emp_created_at_formatted,
                DATE_FORMAT(e.updated_at, '%Y-%m-%d %H:%i:%s') as emp_updated_at_formatted,
                e.created_at as emp_created_at,
                e.updated_at as emp_updated_at,
                COALESCE(e.basic_salary, 0) as basic_salary,
                e.department_id,
                
                -- Additional calculated fields
                CASE 
                    WHEN e.id IS NOT NULL THEN 'Both Account and Employee Record'
                    ELSE 'Account Only (No Employee Record)'
                END as record_status,
                
                -- Age calculation (if date_of_birth exists)
                CASE 
                    WHEN e.date_of_birth IS NOT NULL THEN TIMESTAMPDIFF(YEAR, e.date_of_birth, CURDATE())
                    ELSE NULL
                END as emp_age,
                
                -- Employment duration (if hire_date exists)
                CASE 
                    WHEN e.hire_date IS NOT NULL THEN TIMESTAMPDIFF(YEAR, e.hire_date, CURDATE())
                    ELSE NULL
                END as employment_years,
                CASE 
                    WHEN e.hire_date IS NOT NULL THEN TIMESTAMPDIFF(MONTH, e.hire_date, CURDATE()) % 12
                    ELSE NULL
                END as employment_months
                
            FROM employee_accounts ea 
            LEFT JOIN employees e ON ea.employee_id = e.id 
            WHERE (ea.id = ? OR ea.employee_id = ? OR ea.employee_code = ? 
               OR e.id = ? OR e.employee_code = ?)
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $id, $id, $id, $id, $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Remove password from response for security
        if (isset($row['password'])) {
            unset($row['password']);
        }
        
        // Format timestamps for account
        if (!empty($row['account_created_at'])) {
            $row['account_created_ago'] = time_elapsed_string($row['account_created_at']);
        }
        
        if (!empty($row['account_updated_at'])) {
            $row['account_updated_ago'] = time_elapsed_string($row['account_updated_at']);
        }
        
        // Format timestamps for employee (if exists)
        if (!empty($row['emp_created_at'])) {
            $row['emp_created_ago'] = time_elapsed_string($row['emp_created_at']);
        }
        
        if (!empty($row['emp_updated_at'])) {
            $row['emp_updated_ago'] = time_elapsed_string($row['emp_updated_at']);
        }
        
        // Calculate employment duration string (if data exists)
        if (!empty($row['employment_years'])) {
            $duration = [];
            if ($row['employment_years'] > 0) {
                $duration[] = $row['employment_years'] . ' year' . ($row['employment_years'] > 1 ? 's' : '');
            }
            if ($row['employment_months'] > 0) {
                $duration[] = $row['employment_months'] . ' month' . ($row['employment_months'] > 1 ? 's' : '');
            }
            $row['employment_duration'] = implode(', ', $duration);
        } else if (!empty($row['employment_months'])) {
            $row['employment_duration'] = $row['employment_months'] . ' month' . ($row['employment_months'] > 1 ? 's' : '');
        } else {
            $row['employment_duration'] = 'Not Available';
        }
        
        // Format salary fields
        if (!empty($row['salary']) && $row['salary'] > 0) {
            $row['salary_formatted'] = '₱' . number_format($row['salary'], 2);
        } else {
            $row['salary_formatted'] = 'Not Available';
            $row['salary'] = 0;
        }
        
        if (!empty($row['basic_salary']) && $row['basic_salary'] > 0) {
            $row['basic_salary_formatted'] = '₱' . number_format($row['basic_salary'], 2);
        } else {
            $row['basic_salary_formatted'] = 'Not Available';
            $row['basic_salary'] = 0;
        }
        
        // Format phone number
        if (!empty($row['emp_phone_number']) && $row['emp_phone_number'] !== 'Not Available') {
            $row['emp_phone_formatted'] = preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', $row['emp_phone_number']);
        } else {
            $row['emp_phone_formatted'] = 'Not Available';
        }
        
        if (!empty($row['emergency_contact_number']) && $row['emergency_contact_number'] !== 'Not Available') {
            $row['emergency_contact_formatted'] = preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', $row['emergency_contact_number']);
        } else {
            $row['emergency_contact_formatted'] = 'Not Available';
        }
        
        // Set default values for null fields
        if (empty($row['emp_age'])) {
            $row['emp_age'] = 'Not Available';
        }
        
        if (empty($row['emp_date_of_birth'])) {
            $row['emp_date_of_birth'] = 'Not Available';
        }
        
        if (empty($row['emp_hire_date'])) {
            $row['emp_hire_date'] = 'Not Available';
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
    // Get distinct job titles from employee_accounts
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

// Function to get employment statuses
function getEmploymentStatuses($conn)
{
    // Get distinct employment statuses from employees table
    $sql = "SELECT DISTINCT employment_status FROM employees WHERE employment_status IS NOT NULL AND employment_status != '' 
            UNION 
            SELECT 'Unknown' as employment_status
            ORDER BY employment_status";
    $result = $conn->query($sql);

    $statusList = [];
    while ($row = $result->fetch_assoc()) {
        $statusList[] = $row['employment_status'];
    }

    return [
        'success' => true,
        'data' => [
            'employment_statuses' => $statusList
        ]
    ];
}

// Function to get work statuses
function getWorkStatuses($conn)
{
    // Get distinct work statuses from employees table
    $sql = "SELECT DISTINCT work_status FROM employees WHERE work_status IS NOT NULL AND work_status != '' 
            UNION 
            SELECT 'Unknown' as work_status
            ORDER BY work_status";
    $result = $conn->query($sql);

    $statusList = [];
    while ($row = $result->fetch_assoc()) {
        $statusList[] = $row['work_status'];
    }

    return [
        'success' => true,
        'data' => [
            'work_statuses' => $statusList
        ]
    ];
}

// Function to get department list
function getDepartments($conn)
{
    // Get distinct department IDs from employees
    $sql = "SELECT DISTINCT department_id FROM employees WHERE department_id IS NOT NULL ORDER BY department_id";
    $result = $conn->query($sql);

    $deptList = [];
    while ($row = $result->fetch_assoc()) {
        $deptList[] = $row['department_id'];
    }

    return [
        'success' => true,
        'data' => [
            'departments' => $deptList
        ]
    ];
}

// Function to get employee account statistics
function getEmployeeStats($conn)
{
    $stats = [];
    
    // Total accounts
    $sql = "SELECT COUNT(*) as total_accounts FROM employee_accounts";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $stats['total_accounts'] = $row['total_accounts'];
    
    // Accounts with matching employee records
    $sql = "SELECT COUNT(DISTINCT ea.id) as accounts_with_employee 
            FROM employee_accounts ea 
            INNER JOIN employees e ON ea.employee_id = e.id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $stats['accounts_with_employee'] = $row['accounts_with_employee'];
    
    // Accounts without matching employee records
    $stats['accounts_without_employee'] = $stats['total_accounts'] - $stats['accounts_with_employee'];
    
    // Accounts by job title
    $sql = "SELECT COALESCE(ea.job_title, 'Not Specified') as job_title, COUNT(*) as count 
            FROM employee_accounts ea 
            LEFT JOIN employees e ON ea.employee_id = e.id
            GROUP BY COALESCE(ea.job_title, 'Not Specified')
            ORDER BY count DESC";
    $result = $conn->query($sql);
    $stats['by_job_title'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['by_job_title'][] = $row;
    }
    
    // Accounts by employment status
    $sql = "SELECT COALESCE(e.employment_status, 'Unknown') as employment_status, COUNT(*) as count 
            FROM employee_accounts ea 
            LEFT JOIN employees e ON ea.employee_id = e.id
            GROUP BY COALESCE(e.employment_status, 'Unknown')
            ORDER BY count DESC";
    $result = $conn->query($sql);
    $stats['by_employment_status'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['by_employment_status'][] = $row;
    }
    
    // Recent accounts (last 30 days)
    $sql = "SELECT COUNT(*) as recent_accounts 
            FROM employee_accounts ea 
            LEFT JOIN employees e ON ea.employee_id = e.id
            WHERE ea.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $stats['recent_accounts_30_days'] = $row['recent_accounts'];
    
    // Record status breakdown
    $sql = "SELECT 
                CASE 
                    WHEN e.id IS NOT NULL THEN 'Both Account and Employee Record'
                    ELSE 'Account Only (No Employee Record)'
                END as record_type,
                COUNT(*) as count
            FROM employee_accounts ea 
            LEFT JOIN employees e ON ea.employee_id = e.id
            GROUP BY record_type";
    $result = $conn->query($sql);
    $stats['by_record_type'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['by_record_type'][] = $row;
    }
    
    return [
        'success' => true,
        'data' => $stats
    ];
}

// Function to get employees without accounts (for reference)
function getEmployeesWithoutAccounts($conn, $params = [])
{
    // Default values
    $page = max(1, (int)($params['page'] ?? 1));
    $limit = max(1, min(100, (int)($params['limit'] ?? 25)));
    $offset = ($page - 1) * $limit;

    $sql = "SELECT 
                e.id,
                e.employee_code,
                e.first_name,
                e.middle_name,
                e.last_name,
                CONCAT(e.first_name, ' ', IFNULL(e.middle_name, ''), ' ', e.last_name) as full_name,
                e.job,
                COALESCE(e.phone_number, 'Not Available') as phone_number,
                COALESCE(e.email, 'Not Available') as email,
                COALESCE(e.employment_status, 'Unknown') as employment_status,
                e.department_id,
                DATE_FORMAT(e.hire_date, '%Y-%m-%d') as hire_date,
                COALESCE(e.salary, 0) as salary,
                DATE_FORMAT(e.created_at, '%Y-%m-%d %H:%i:%s') as created_at_formatted
            FROM employees e
            WHERE NOT EXISTS (
                SELECT 1 FROM employee_accounts ea WHERE ea.employee_id = e.id
            )
            ORDER BY e.created_at DESC
            LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $employees = [];
    while ($row = $result->fetch_assoc()) {
        // Format salary
        if (!empty($row['salary']) && $row['salary'] > 0) {
            $row['salary_formatted'] = '₱' . number_format($row['salary'], 2);
        } else {
            $row['salary_formatted'] = 'Not Available';
        }
        
        // Format phone number
        if (!empty($row['phone_number']) && $row['phone_number'] !== 'Not Available') {
            $row['phone_formatted'] = preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', $row['phone_number']);
        } else {
            $row['phone_formatted'] = 'Not Available';
        }
        
        $employees[] = $row;
    }

    // Get total count for pagination
    $countSql = "SELECT COUNT(*) as total 
                FROM employees e
                WHERE NOT EXISTS (
                    SELECT 1 FROM employee_accounts ea WHERE ea.employee_id = e.id
                )";
    $countResult = $conn->query($countSql);
    $totalRow = $countResult->fetch_assoc();
    $totalRecords = $totalRow['total'] ?? 0;
    $totalPages = ceil($totalRecords / $limit);

    return [
        'success' => true,
        'data' => $employees,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $totalRecords,
            'limit' => $limit,
            'offset' => $offset
        ]
    ];
}

// Main request handler
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Handle GET requests
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'jobtitles':
                    $response = getJobTitles($conn);
                    break;
                case 'statuses':
                    $response = getEmploymentStatuses($conn);
                    break;
                case 'workstatuses':
                    $response = getWorkStatuses($conn);
                    break;
                case 'departments':
                    $response = getDepartments($conn);
                    break;
                case 'stats':
                    $response = getEmployeeStats($conn);
                    break;
                case 'without-accounts':
                    $filters = [
                        'page' => $_GET['page'] ?? 1,
                        'limit' => $_GET['limit'] ?? 25
                    ];
                    $response = getEmployeesWithoutAccounts($conn, $filters);
                    break;
                default:
                    $response = [
                        'success' => false,
                        'message' => 'Invalid action specified'
                    ];
                    break;
            }
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
                'employment_status' => $_GET['employment_status'] ?? null,
                'work_status' => $_GET['work_status'] ?? null,
                'department_id' => $_GET['department_id'] ?? null,
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
?>