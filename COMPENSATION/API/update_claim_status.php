<?php
header('Content-Type: application/json');
require_once '../DB.php';

session_start();

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['id']) && isset($input['status'])) {
    try {
        // Get current claim
        $claim = Database::fetch("SELECT * FROM claims WHERE id = ?", [$input['id']]);

        if (!$claim) {
            echo json_encode(['success' => false, 'message' => 'Claim not found']);
            exit;
        }

        // Prepare update data
        $updateData = [
            'status' => $input['status'],
            'review_date' => date('Y-m-d'),
            'reviewed_by' => $_SESSION['user_name'] ?? 'System Admin'
        ];

        // If approving, set approved amount
        if ($input['status'] === 'Approved' || $input['status'] === 'Paid') {
            $updateData['approved_amount'] = $claim->amount;
        }

        // Update claim
        $result = Database::updateTable('claims', $updateData, 'id = ?', [$input['id']]);

        echo json_encode([
            'success' => $result > 0,
            'message' => 'Status updated successfully!'
        ]);

        // Optional: Send email notification
        // sendStatusNotification($claim->employee_id, $input['status']);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
}
