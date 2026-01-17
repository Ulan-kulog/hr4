<?php
// get_claim.php - API to fetch a specific claim record by ID
require_once '../../COMPENSATION/DB.php';
header('Content-Type: application/json');

// Only allow GET or POST
$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'GET' && $method !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

// Get claim ID from query or POST
$claim_id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$claim_id || !is_numeric($claim_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing or invalid claim ID']);
    exit();
}

// Fetch claim
$sql = "SELECT ec.*, e.first_name, e.last_name, e.employee_code, e.department
        FROM employee_claims ec
        JOIN employees e ON ec.employee_id = e.id
        WHERE ec.id = ?";
$claim = Database::fetch($sql, [$claim_id]);

if ($claim) {
    echo json_encode(['success' => true, 'data' => $claim]);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Claim not found']);
}
