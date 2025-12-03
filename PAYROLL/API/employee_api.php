<?php
session_start();
include("../connection.php");

// Database connection
$db_name = "HR_4";
if (!isset($connections[$db_name])) {
    die(json_encode(['success' => false, 'message' => 'Database connection not found']));
}
$conn = $connections[$db_name];

header('Content-Type: application/json');

// Get action
$action = $_POST['action'] ?? '';

switch($action) {
    case 'update_employee_status':
        updateEmployeeStatus();
        break;
    case 'bulk_update':
        bulkUpdate();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function updateEmployeeStatus() {
    global $conn;
    
    $employee_id = intval($_POST['employee_id']);
    $status = $_POST['status'];
    $notes = $_POST['notes'] ?? '';
    $user_id = $_SESSION['user_id'] ?? 1;
    
    // Validate status
    $valid_statuses = ['Active', 'Under Review', 'Notice Period', 'AWOL', 'Floating'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        return;
    }
    
    try {
        // Update employee status
        $sql = "UPDATE employees SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $employee_id);
        
        if ($stmt->execute()) {
            // Log the action
            $log_sql = "INSERT INTO employee_history (employee_id, action, performed_by, notes) VALUES (?, ?, ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $action_text = "Status changed to $status";
            $log_stmt->bind_param("isis", $employee_id, $action_text, $user_id, $notes);
            $log_stmt->execute();
            
            echo json_encode([
                'success' => true,
                'message' => "Employee status updated to $status successfully"
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update employee status'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

function bulkUpdate() {
    global $conn;
    
    $employee_ids = $_POST['employee_ids'];
    $type = $_POST['type'];
    $notes = $_POST['notes'] ?? '';
    $user_id = $_SESSION['user_id'] ?? 1;
    
    if (empty($employee_ids)) {
        echo json_encode(['success' => false, 'message' => 'No employees selected']);
        return;
    }
    
    $ids_array = explode(',', $employee_ids);
    $placeholders = implode(',', array_fill(0, count($ids_array), '?'));
    
    try {
        // Determine action based on type
        $action_text = '';
        $status_change = null;
        
        switch($type) {
            case 'bulk_budget':
                $action_text = 'Budget requested';
                break;
            case 'bulk_compliance':
                $action_text = 'Compliance requirements assigned';
                break;
            case 'bulk_regularize':
                $action_text = 'Employees regularized';
                $status_change = 'Active';
                break;
        }
        
        // Update status if needed
        if ($status_change) {
            $update_sql = "UPDATE employees SET status = ? WHERE id IN ($placeholders)";
            $stmt = $conn->prepare($update_sql);
            $params = array_merge([$status_change], $ids_array);
            $stmt->execute($params);
        }
        
        // Log bulk action
        foreach($ids_array as $emp_id) {
            $log_sql = "INSERT INTO employee_history (employee_id, action, performed_by, notes) VALUES (?, ?, ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->bind_param("isis", $emp_id, $action_text, $user_id, $notes);
            $log_stmt->execute();
        }
        
        echo json_encode([
            'success' => true,
            'message' => "Bulk action completed for " . count($ids_array) . " employee(s)"
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}
?>