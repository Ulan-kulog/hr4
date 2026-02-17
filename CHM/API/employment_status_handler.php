<?php
session_start();
include("../../connection.php");

header('Content-Type: application/json');

$db_name = "hr4_hr_4";
if (!isset($connections[$db_name])) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}
$conn = $connections[$db_name];

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['employee_id'], $input['action'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request: missing employee_id or action']);
        exit;
    }

    $employee_id = intval($input['employee_id']); // primary key
    $action = $input['action'];
    $days = isset($input['days']) ? intval($input['days']) : null;
    $reason = isset($input['reason']) ? trim($input['reason']) : '';

    // Map actions to employment status values
    $status_map = [
        'suspended' => 'suspended',
        'terminate' => 'terminated',
        'probationary' => 'probationary',
        'awol' => 'awol',
        'for_compliance' => 'for_compliance'
    ];

    if (!array_key_exists($action, $status_map)) {
        echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);
        exit;
    }

    $new_status = $status_map[$action];

    // Build a note to append to compliance_notes
    $note_parts = [];
    $note_parts[] = "[" . date('Y-m-d H:i:s') . "] Status changed to: $new_status";
    if ($days) {
        $note_parts[] = "Duration: $days days";
    }
    if (!empty($reason)) {
        $note_parts[] = "Reason: $reason";
    }
    $appended_note = implode(' | ', $note_parts);

    // Fetch current compliance_notes
    $fetch_sql = "SELECT compliance_notes FROM employees WHERE id = ?";
    $fetch_stmt = $conn->prepare($fetch_sql);
    $fetch_stmt->bind_param('i', $employee_id);
    $fetch_stmt->execute();
    $fetch_result = $fetch_stmt->get_result();
    $current_notes = '';
    if ($row = $fetch_result->fetch_assoc()) {
        $current_notes = $row['compliance_notes'];
    }
    $fetch_stmt->close();

    // Append new note (with a separator)
    if (!empty($current_notes)) {
        $updated_notes = $current_notes . "\n" . $appended_note;
    } else {
        $updated_notes = $appended_note;
    }

    // Update employee record
    $update_sql = "UPDATE employees SET employment_status = ?, compliance_notes = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('ssi', $new_status, $updated_notes, $employee_id);

    if ($update_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Employee status updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }

    $update_stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>