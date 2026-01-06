<?php
header('Content-Type: application/json');
require_once '../DB.php';

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['id'])) {
    try {
        // Check if claim exists
        $claim = Database::fetch("SELECT * FROM claims WHERE id = ?", [$input['id']]);

        if (!$claim) {
            echo json_encode(['success' => false, 'message' => 'Claim not found']);
            exit;
        }

        // Soft delete (update status) or hard delete
        // Option 1: Soft delete
        // Database::execute("UPDATE claims SET status = 'Deleted' WHERE id = ?", [$input['id']]);

        // Option 2: Hard delete
        $result = Database::execute("DELETE FROM claims WHERE id = ?", [$input['id']]);

        echo json_encode([
            'success' => $result > 0,
            'message' => $result > 0 ? 'Claim deleted successfully' : 'Failed to delete claim'
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No ID provided']);
}
