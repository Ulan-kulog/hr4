<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

include("../../connection.php");

$db_name = "hr4_hr_4";
if (!isset($connections[$db_name])) {
    echo json_encode(['success' => false, 'message' => 'Database connection not found']);
    exit;
}
$conn = $connections[$db_name];

$data = json_decode(file_get_contents('php://input'), true);
$employee_id = $data['employee_id'] ?? 0;
$status = $data['employment_status'] ?? '';
$comment = $data['comment'] ?? '';

if (!$employee_id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Missing employee_id or status']);
    exit;
}

$allowed = ['regular', 'probationary', 'for_compliance', 'terminated', 'resigned'];
if (!in_array($status, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status value']);
    exit;
}

$conn->begin_transaction();

try {
    // Update employees table
    $stmt = $conn->prepare("UPDATE employees SET employment_status = ? WHERE id = ?");
    if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
    $stmt->bind_param("si", $status, $employee_id);
    if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
    $stmt->close();

    // If status is 'for_compliance' and comment provided, insert into compliance_notes
    if ($status === 'for_compliance' && !empty($comment)) {
        // Ensure compliance_notes table exists (you may create it manually)
        $conn->query("
            CREATE TABLE IF NOT EXISTS compliance_notes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                employee_id INT NOT NULL,
                note TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (employee_id)
            )
        ");

        $note_stmt = $conn->prepare("INSERT INTO compliance_notes (employee_id, note) VALUES (?, ?)");
        if (!$note_stmt) throw new Exception("Note prepare failed: " . $conn->error);
        $note_stmt->bind_param("is", $employee_id, $comment);
        if (!$note_stmt->execute()) throw new Exception("Note execute failed: " . $note_stmt->error);
        $note_stmt->close();
    }

    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}