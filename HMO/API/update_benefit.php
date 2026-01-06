<?php
require_once '../DB.php';
session_start();

// header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {

    // Get POST data
    $data = $_POST;

    // Log for debugging
    file_put_contents('debug.log', date('Y-m-d H:i:s') . " - Update Request: " . print_r($data, true) . "\n", FILE_APPEND);

    // Validate required fields
    if (!isset($data['id']) || empty($data['id'])) {
        throw new Exception("Benefit ID is required");
    }

    $benefit_id = intval($data['id']);

    // Prepare update data
    $update_data = [
        'benefit_code' => $data['benefit_code'] ?? '',
        'benefit_name' => $data['benefit_name'] ?? '',
        'description' => $data['description'] ?? '',
        'provider_id' => !empty($data['provider_id']) ? $data['provider_id'] : null,
        'benefit_type' => $data['benefit_type'] ?? 'fixed',
        'value' => isset($data['value']) ? floatval($data['value']) : 0,
        'unit' => $data['unit'] ?? 'dollar',
        'company_cost_value' => isset($data['company_cost_value']) ? floatval($data['company_cost_value']) : 0,
        'company_cost_type' => $data['company_cost_type'] ?? 'dollar',
        'employee_cost_value' => isset($data['employee_cost_value']) ? floatval($data['employee_cost_value']) : 0,
        'employee_cost_type' => $data['employee_cost_type'] ?? 'dollar',
        'is_taxable' => isset($data['is_taxable']) && $data['is_taxable'] == '1' ? 1 : 0,
        'status' => $data['status'] ?? 'pending',
    ];

    // Convert empty string provider_id to NULL
    if ($update_data['provider_id'] === '') {
        $update_data['provider_id'] = null;
    }

    // Update benefit in database
    // $pdo = Database::getInstance();

    // Build SQL
    $set_parts = [];
    $params = [];

    foreach ($update_data as $key => $value) {
        $set_parts[] = "$key = :$key";
        $params[":$key"] = $value;
    }

    $params[':id'] = $benefit_id;

    $sql = "UPDATE benefits SET " . implode(', ', $set_parts) . " WHERE id = :id";

    file_put_contents('debug.log', date('Y-m-d H:i:s') . " - SQL: $sql\n", FILE_APPEND);
    file_put_contents('debug.log', date('Y-m-d H:i:s') . " - Params: " . print_r($params, true) . "\n", FILE_APPEND);

    $result = Database::execute($sql, $params);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Benefit updated successfully',
        ]);
        $_SESSION['success'] = 'Benefit updated successfully!';
        header('Location: ../benefits_enrollment.php');
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No changes made or benefit not found'
        ]);
        $_SESSION['error'] = 'Failed to create benefit';
        header('Location: ../benefits_enrollment.php');
    }
} catch (Exception $e) {
    file_put_contents('debug.log', date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n", FILE_APPEND);

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
