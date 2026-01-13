<?php
session_start();
include("../../connection.php");

// Database connection
$db_name = "hr4_hr_4";
if (!isset($connections[$db_name])) {
    die(json_encode(['success' => false, 'message' => 'Database connection not found']));
}
$conn = $connections[$db_name];

header('Content-Type: application/json');

// Get action
$action = $_POST['action'] ?? '';

switch($action) {
    case 'update_status':
        updatePayrollStatus();
        break;
    case 'get_payroll_details':
        getPayrollDetails();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function updatePayrollStatus() {
    global $conn;
    
    $payroll_id = intval($_POST['payroll_id']);
    $status = $_POST['status'];
    $notes = $_POST['notes'] ?? '';
    $user_id = $_SESSION['user_id'] ?? 1; // Default to admin if not logged in
    
    // Validate status
    $valid_statuses = ['Pending', 'Approved', 'Hold', 'Declined', 'Paid'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        return;
    }
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Update payroll status
        $update_sql = "UPDATE payroll SET status = ?, notes = ?, processed_by = ?, processed_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssii", $status, $notes, $user_id, $payroll_id);
        $stmt->execute();
        
        // Add to history
        $history_sql = "INSERT INTO payroll_history (payroll_id, action, performed_by, notes) VALUES (?, ?, ?, ?)";
        $stmt2 = $conn->prepare($history_sql);
        $stmt2->bind_param("isis", $payroll_id, $status, $user_id, $notes);
        $stmt2->execute();
        
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => "Payroll status updated to $status successfully"
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'message' => 'Error updating payroll: ' . $e->getMessage()
        ]);
    }
}

function getPayrollDetails() {
    global $conn;
    
    $payroll_id = intval($_POST['payroll_id']);
    
    $sql = "SELECT p.*, e.full_name, e.employee_id, e.department, e.position 
            FROM payroll p 
            JOIN employees e ON p.employee_id = e.id 
            WHERE p.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $payroll_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Payroll not found']);
    }
}
?>