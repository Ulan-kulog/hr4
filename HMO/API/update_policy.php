<?php
require_once '../DB.php';
session_start();

// header('Content-Type: application/json');

// Check if it's an update action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $policy_id = $_POST['policy_id'] ?? null;
        $policy_name = $_POST['policy_name'] ?? null;
        $description = $_POST['description'] ?? null;
        $applies_to = $_POST['applies_to'] ?? 'all employees';
        $effective_date = $_POST['effective_date'] ?? null;
        $expiration_date = $_POST['expiration_date'] ?? null;
        $status = $_POST['status'] ?? 'active';

        if (!$policy_id || !$policy_name || !$description || !$effective_date) {
            throw new Exception('Required fields are missing');
        }

        // Prepare update data
        $update_data = [
            'policy_name' => $policy_name,
            'description' => $description,
            'applies_to' => $applies_to,
            'effective_date' => $effective_date,
            'expiration_date' => $expiration_date ?: null,
            'status' => $status,
            'updated_by' => $_SESSION['user_id'] ?? null
        ];

        // Update policy in database
        $result = Database::updateTable('policies', $update_data, 'id = ?', [$policy_id]);

        if ($result) {
            header('Location: ../benefits_enrollment.php');
        } else {
            header('Location: ../benefits_enrollment.php');
            throw new Exception('Failed to update policy in database');
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
