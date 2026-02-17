<?php
session_start();
include("../../connection.php");

header('Content-Type: application/json');

// Database connection
$db_name = "hr4_hr_4";
if (!isset($connections[$db_name])) {
    die(json_encode(['success' => false, 'message' => 'Database connection not found']));
}
$conn = $connections[$db_name];

// Helper function to safely get string value
function getStringValue($value, $default = '') {
    if (is_array($value)) {
        if (is_string($value[0] ?? null)) {
            return $value[0];
        }
        return implode(' ', $value);
    }
    return (string) $value ?: (string) $default;
}

// Get action from request
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Handle different actions
switch ($action) {
    case 'sync_employees':
        syncEmployees($conn);
        break;
        
    case 'get_employee_details':
        getEmployeeDetails($conn);
        break;
        
    case 'update_salary_status':
        updateSalaryStatus($conn);
        break;
        
    case 'update_work_status':
        updateWorkStatus($conn);
        break;
        
    case 'create_payroll':
        createPayroll($conn);
        break;
        
    case 'update_payroll':
        updatePayroll($conn);
        break;
        
    case 'delete_payroll':
        deletePayroll($conn);
        break;
        
    case 'get_payroll_history':
        getPayrollHistory($conn);
        break;
        
    case 'get_payroll_details':
        getPayrollDetails($conn);
        break;
        
    case 'get_current_payroll':
        getCurrentPayroll($conn);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

// Sync employees from API
function syncEmployees($conn) {
    $api_url = "https://hr1.soliera-hotel-restaurant.com/api/employees";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        $data = json_decode($response, true);
        if (is_array($data)) {
            $count = 0;
            foreach ($data as $api_employee) {
                // Safely extract values from API
                $employee_code = getStringValue($api_employee['employee_code'] ?? $api_employee['id'] ?? '');
                
                // Build name components
                $first_name = $conn->real_escape_string(getStringValue($api_employee['first_name'] ?? ''));
                $middle_name = $conn->real_escape_string(getStringValue($api_employee['middle_name'] ?? ''));
                $last_name = $conn->real_escape_string(getStringValue($api_employee['last_name'] ?? ''));
                
                // If API provides full_name, try to parse it
                if (empty($first_name) && isset($api_employee['full_name'])) {
                    $full_name = getStringValue($api_employee['full_name']);
                    $name_parts = explode(' ', $full_name, 3);
                    $first_name = $conn->real_escape_string($name_parts[0] ?? '');
                    if (isset($name_parts[1])) {
                        if (isset($name_parts[2])) {
                            $middle_name = $conn->real_escape_string($name_parts[1]);
                            $last_name = $conn->real_escape_string($name_parts[2]);
                        } else {
                            $last_name = $conn->real_escape_string($name_parts[1]);
                        }
                    }
                }
                
                $email = $conn->real_escape_string(getStringValue($api_employee['email'] ?? ''));
                $phone_number = $conn->real_escape_string(getStringValue($api_employee['phone'] ?? $api_employee['phone_number'] ?? ''));
                $job = $conn->real_escape_string(getStringValue($api_employee['job'] ?? $api_employee['position'] ?? 'N/A'));
                $department_id = $api_employee['department_id'] ?? null;
                
                // Handle salary
                $salary_value = $api_employee['expected_salary'] ?? $api_employee['basic_salary'] ?? $api_employee['salary'] ?? 0;
                $salary = is_numeric($salary_value) ? $salary_value : 0;
                $basic_salary = $api_employee['basic_salary'] ?? $salary;
                
                $work_status = $conn->real_escape_string(getStringValue($api_employee['work_status'] ?? 'Active'));
                $employment_status = $conn->real_escape_string(getStringValue($api_employee['employment_status'] ?? 'Active'));
                
                // Check if employee exists by employee_code
                $check_sql = "SELECT id FROM employees WHERE employee_code = '$employee_code'";
                $result = $conn->query($check_sql);
                
                if ($result && $result->num_rows > 0) {
                    // Update existing employee
                    $update_sql = "UPDATE employees SET 
                        first_name = '$first_name',
                        middle_name = '$middle_name',
                        last_name = '$last_name',
                        email = '$email',
                        phone_number = '$phone_number',
                        job = '$job',
                        salary = '$salary',
                        basic_salary = '$basic_salary',
                        work_status = '$work_status',
                        employment_status = '$employment_status',
                        updated_at = CURRENT_TIMESTAMP";
                    
                    if ($department_id !== null) {
                        $update_sql .= ", department_id = '$department_id'";
                    }
                    
                    $update_sql .= " WHERE employee_code = '$employee_code'";
                    
                    $conn->query($update_sql);
                    
                    // Update salary_status to 'Under review' if not already set
                    $conn->query("UPDATE employees SET salary_status = 'Under review' WHERE employee_code = '$employee_code' AND (salary_status IS NULL OR salary_status = '')");
                } else {
                    // Insert new employee
                    $insert_sql = "INSERT INTO employees (
                        employee_code,
                        first_name,
                        middle_name,
                        last_name,
                        email,
                        phone_number,
                        job,
                        salary,
                        basic_salary,
                        work_status,
                        employment_status,
                        salary_status
                    ) VALUES (
                        '$employee_code',
                        '$first_name',
                        '$middle_name',
                        '$last_name',
                        '$email',
                        '$phone_number',
                        '$job',
                        '$salary',
                        '$basic_salary',
                        '$work_status',
                        '$employment_status',
                        'Under review'
                    )";
                    
                    $conn->query($insert_sql);
                }
                $count++;
            }
            
            echo json_encode([
                'success' => true,
                'message' => "Successfully synced $count employees from API",
                'count' => $count
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid data format from API']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Failed to fetch from API. HTTP Code: $http_code"]);
    }
}

// Get employee details
function getEmployeeDetails($conn) {
    $id = $_POST['id'] ?? 0;
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Employee ID is required']);
        return;
    }
    
    $sql = "SELECT e.*, 
            p.id as payroll_id, 
            p.basic_salary as payroll_basic_salary,
            p.overtime_hours,
            p.overtime_rate,
            p.overtime_pay,
            p.allowances,
            p.deductions,
            p.net_pay,
            p.period,
            p.status as payroll_status,
            p.notes as payroll_notes,
            p.created_at as payroll_created_at
            FROM employees e 
            LEFT JOIN payroll p ON e.id = p.employee_id 
            WHERE e.id = '$id'
            ORDER BY p.created_at DESC
            LIMIT 1";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        $data = [
            'id' => $row['id'],
            'employee_code' => $row['employee_code'],
            'first_name' => $row['first_name'],
            'middle_name' => $row['middle_name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'],
            'phone_number' => $row['phone_number'],
            'job' => $row['job'],
            'department_id' => $row['department_id'],
            'salary' => $row['salary'],
            'basic_salary' => $row['basic_salary'],
            'work_status' => $row['work_status'],
            'employment_status' => $row['employment_status'],
            'salary_status' => $row['salary_status'],
            'salary_reason' => $row['salary_reason'],
            'date_of_birth' => $row['date_of_birth'],
            'hire_date' => $row['hire_date'],
            'created_at' => $row['created_at']
        ];
        
        if ($row['payroll_id']) {
            $data['payroll'] = [
                'id' => $row['payroll_id'],
                'basic_salary' => $row['payroll_basic_salary'],
                'overtime_hours' => $row['overtime_hours'],
                'overtime_rate' => $row['overtime_rate'],
                'overtime_pay' => $row['overtime_pay'],
                'allowances' => $row['allowances'],
                'deductions' => $row['deductions'],
                'net_pay' => $row['net_pay'],
                'period' => $row['period'],
                'status' => $row['payroll_status'],
                'notes' => $row['payroll_notes'],
                'created_at' => $row['payroll_created_at']
            ];
        }
        
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Employee not found']);
    }
}

// Update salary status
function updateSalaryStatus($conn) {
    $id = $_POST['id'] ?? 0;
    $status = $_POST['status'] ?? '';
    $reason = $_POST['reason'] ?? '';
    
    if (!$id || !$status) {
        echo json_encode(['success' => false, 'message' => 'Employee ID and status are required']);
        return;
    }
    
    $allowed_statuses = ['Under review', 'For financing', 'Denied financing', 'For compliance'];
    if (!in_array($status, $allowed_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        return;
    }
    
    $reason_escaped = $conn->real_escape_string($reason);
    
    $sql = "UPDATE employees SET 
            salary_status = '$status',
            salary_reason = '$reason_escaped',
            updated_at = CURRENT_TIMESTAMP
            WHERE id = '$id'";
    
    if ($conn->query($sql)) {
        echo json_encode([
            'success' => true,
            'message' => "Salary status updated to '$status'",
            'employee_id' => $id,
            'status' => $status,
            'reason' => $reason
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update: ' . $conn->error]);
    }
}

// Update work status
function updateWorkStatus($conn) {
    $id = $_POST['id'] ?? 0;
    $status = $_POST['status'] ?? '';
    
    if (!$id || !$status) {
        echo json_encode(['success' => false, 'message' => 'Employee ID and status are required']);
        return;
    }
    
    $allowed_statuses = ['Active', 'Inactive', 'On Leave', 'Under Review'];
    if (!in_array($status, $allowed_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid work status']);
        return;
    }
    
    $sql = "UPDATE employees SET 
            work_status = '$status',
            updated_at = CURRENT_TIMESTAMP
            WHERE id = '$id'";
    
    if ($conn->query($sql)) {
        echo json_encode([
            'success' => true,
            'message' => "Work status updated to '$status'",
            'employee_id' => $id,
            'status' => $status
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update: ' . $conn->error]);
    }
}

// Create payroll
function createPayroll($conn) {
    $employee_id = $_POST['employee_id'] ?? 0;
    $basic_salary = $_POST['basic_salary'] ?? 0;
    $overtime_hours = $_POST['overtime_hours'] ?? 0;
    $overtime_rate = $_POST['overtime_rate'] ?? 0;
    $overtime_pay = $_POST['overtime_pay'] ?? 0;
    $allowances = $_POST['allowances'] ?? 0;
    $deductions = $_POST['deductions'] ?? 0;
    $net_pay = $_POST['net_pay'] ?? 0;
    $period = $_POST['period'] ?? date('Y-m');
    $status = $_POST['status'] ?? 'Draft';
    $notes = $_POST['notes'] ?? '';
    
    if (!$employee_id) {
        echo json_encode(['success' => false, 'message' => 'Employee ID is required']);
        return;
    }
    
    // Get employee code
    $emp_sql = "SELECT employee_code FROM employees WHERE id = '$employee_id'";
    $emp_result = $conn->query($emp_sql);
    if (!$emp_result || $emp_result->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Employee not found']);
        return;
    }
    $emp_row = $emp_result->fetch_assoc();
    $employee_code = $emp_row['employee_code'];
    
    // Check if payroll already exists for this period
    $check_sql = "SELECT id FROM payroll WHERE employee_id = '$employee_id' AND period = '$period'";
    $result = $conn->query($check_sql);
    
    if ($result && $result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Payroll already exists for this period']);
        return;
    }
    
    $processed_by = $_SESSION['username'] ?? 'System';
    $notes_escaped = $conn->real_escape_string($notes);
    
    $sql = "INSERT INTO payroll (
            employee_id,
            employee_code,
            basic_salary,
            overtime_hours,
            overtime_rate,
            overtime_pay,
            allowances,
            deductions,
            net_pay,
            period,
            status,
            notes,
            processed_by,
            processed_at
            ) VALUES (
            '$employee_id',
            '$employee_code',
            '$basic_salary',
            '$overtime_hours',
            '$overtime_rate',
            '$overtime_pay',
            '$allowances',
            '$deductions',
            '$net_pay',
            '$period',
            '$status',
            '$notes_escaped',
            '$processed_by',
            CURRENT_TIMESTAMP
            )";
    
    if ($conn->query($sql)) {
        $payroll_id = $conn->insert_id;
        
        echo json_encode([
            'success' => true,
            'message' => 'Payroll created successfully',
            'payroll_id' => $payroll_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create payroll: ' . $conn->error]);
    }
}

// Update payroll
function updatePayroll($conn) {
    $payroll_id = $_POST['payroll_id'] ?? 0;
    $basic_salary = $_POST['basic_salary'] ?? 0;
    $overtime_hours = $_POST['overtime_hours'] ?? 0;
    $overtime_rate = $_POST['overtime_rate'] ?? 0;
    $overtime_pay = $_POST['overtime_pay'] ?? 0;
    $allowances = $_POST['allowances'] ?? 0;
    $deductions = $_POST['deductions'] ?? 0;
    $net_pay = $_POST['net_pay'] ?? 0;
    $period = $_POST['period'] ?? date('Y-m');
    $status = $_POST['status'] ?? 'Draft';
    $notes = $_POST['notes'] ?? '';
    
    if (!$payroll_id) {
        echo json_encode(['success' => false, 'message' => 'Payroll ID is required']);
        return;
    }
    
    $notes_escaped = $conn->real_escape_string($notes);
    
    $sql = "UPDATE payroll SET 
            basic_salary = '$basic_salary',
            overtime_hours = '$overtime_hours',
            overtime_rate = '$overtime_rate',
            overtime_pay = '$overtime_pay',
            allowances = '$allowances',
            deductions = '$deductions',
            net_pay = '$net_pay',
            period = '$period',
            status = '$status',
            notes = '$notes_escaped',
            updated_at = CURRENT_TIMESTAMP
            WHERE id = '$payroll_id'";
    
    if ($conn->query($sql)) {
        echo json_encode([
            'success' => true,
            'message' => 'Payroll updated successfully',
            'payroll_id' => $payroll_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update payroll: ' . $conn->error]);
    }
}

// Get payroll history
function getPayrollHistory($conn) {
    $employee_id = $_POST['employee_id'] ?? 0;
    
    if (!$employee_id) {
        echo json_encode(['success' => false, 'message' => 'Employee ID is required']);
        return;
    }
    
    $sql = "SELECT p.*, 
            e.first_name, 
            e.last_name,
            e.employee_code
            FROM payroll p
            JOIN employees e ON p.employee_id = e.id
            WHERE p.employee_id = '$employee_id'
            ORDER BY p.period DESC, p.created_at DESC";
    
    $result = $conn->query($sql);
    
    if ($result) {
        $history = [];
        while($row = $result->fetch_assoc()) {
            $history[] = [
                'id' => $row['id'],
                'period' => $row['period'],
                'basic_salary' => $row['basic_salary'],
                'overtime_hours' => $row['overtime_hours'],
                'overtime_rate' => $row['overtime_rate'],
                'overtime_pay' => $row['overtime_pay'],
                'allowances' => $row['allowances'],
                'deductions' => $row['deductions'],
                'net_pay' => $row['net_pay'],
                'status' => $row['status'],
                'notes' => $row['notes'],
                'processed_by' => $row['processed_by'],
                'processed_at' => $row['processed_at'],
                'employee_name' => $row['first_name'] . ' ' . $row['last_name'],
                'employee_code' => $row['employee_code']
            ];
        }
        
        echo json_encode([
            'success' => true,
            'history' => $history,
            'count' => count($history)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch payroll history: ' . $conn->error]);
    }
}

// Get payroll details
function getPayrollDetails($conn) {
    $payroll_id = $_POST['payroll_id'] ?? 0;
    
    if (!$payroll_id) {
        echo json_encode(['success' => false, 'message' => 'Payroll ID is required']);
        return;
    }
    
    $sql = "SELECT p.*, 
            e.first_name, 
            e.last_name,
            e.employee_code,
            e.job
            FROM payroll p
            JOIN employees e ON p.employee_id = e.id
            WHERE p.id = '$payroll_id'";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        $payroll = [
            'id' => $row['id'],
            'period' => $row['period'],
            'basic_salary' => $row['basic_salary'],
            'overtime_hours' => $row['overtime_hours'],
            'overtime_rate' => $row['overtime_rate'],
            'overtime_pay' => $row['overtime_pay'],
            'allowances' => $row['allowances'],
            'deductions' => $row['deductions'],
            'net_pay' => $row['net_pay'],
            'status' => $row['status'],
            'notes' => $row['notes'],
            'processed_by' => $row['processed_by'],
            'processed_at' => $row['processed_at'],
            'employee_name' => $row['first_name'] . ' ' . $row['last_name'],
            'employee_code' => $row['employee_code'],
            'job' => $row['job']
        ];
        
        echo json_encode([
            'success' => true,
            'payroll' => $payroll
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Payroll not found']);
    }
}

// Get current payroll for editing
function getCurrentPayroll($conn) {
    $employee_id = $_POST['employee_id'] ?? 0;
    $period = date('Y-m');
    
    if (!$employee_id) {
        echo json_encode(['success' => false, 'message' => 'Employee ID is required']);
        return;
    }
    
    $sql = "SELECT * FROM payroll 
            WHERE employee_id = '$employee_id' 
            AND period = '$period'
            ORDER BY created_at DESC
            LIMIT 1";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        $payroll = [
            'id' => $row['id'],
            'period' => $row['period'],
            'basic_salary' => $row['basic_salary'],
            'overtime_hours' => $row['overtime_hours'],
            'overtime_rate' => $row['overtime_rate'],
            'overtime_pay' => $row['overtime_pay'],
            'allowances' => $row['allowances'],
            'deductions' => $row['deductions'],
            'net_pay' => $row['net_pay'],
            'status' => $row['status'],
            'notes' => $row['notes']
        ];
        
        echo json_encode([
            'success' => true,
            'payroll' => $payroll
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No current payroll found']);
    }
}

// Delete payroll
function deletePayroll($conn) {
    $payroll_id = $_POST['payroll_id'] ?? 0;
    
    if (!$payroll_id) {
        echo json_encode(['success' => false, 'message' => 'Payroll ID is required']);
        return;
    }
    
    $sql = "DELETE FROM payroll WHERE id = '$payroll_id'";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Payroll deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete payroll: ' . $conn->error]);
    }
}
?>