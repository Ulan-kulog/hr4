<?php
require_once '../DB.php';
session_start();

// Check authentication
// if (!isset($_SESSION['user_id'])) {
//     http_response_code(401);
//     echo json_encode(['error' => 'Unauthorized']);
//     exit;
// }

$userId = $_SESSION['user_id'];
$limit = $_GET['limit'] ?? 50;
$offset = $_GET['offset'] ?? 0;
$search = $_GET['search'] ?? '';
$reportType = $_GET['report_type'] ?? '';

try {
    // Build query
    $query = "
        SELECT 
            gr.*,
            CONCAT(e.first_name, ' ', e.last_name) as generated_by_name,
            e.employee_code as generated_by_code
        FROM generated_reports gr
        LEFT JOIN employees e ON gr.generated_by = e.id
        WHERE gr.status = 'generated'
    ";

    $params = [];

    // Add filters
    if ($search) {
        $query .= " AND (gr.report_name LIKE ? OR gr.file_name LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($reportType) {
        $query .= " AND gr.report_type = ?";
        $params[] = $reportType;
    }

    // Only show user's reports unless they're admin
    // You might want to adjust this based on your user roles
    $query .= " AND gr.generated_by = ?";
    $params[] = $userId;

    // Order and limit
    $query .= " ORDER BY gr.generated_at DESC LIMIT ? OFFSET ?";
    $params[] = (int)$limit;
    $params[] = (int)$offset;

    $reports = Database::fetchAll($query, $params);

    // Get total count for pagination
    $countQuery = "
        SELECT COUNT(*) as total 
        FROM generated_reports gr
        WHERE gr.status = 'generated' AND gr.generated_by = ?
    ";

    $countParams = [$userId];

    if ($search) {
        $countQuery .= " AND (gr.report_name LIKE ? OR gr.file_name LIKE ?)";
        $countParams[] = "%$search%";
        $countParams[] = "%$search%";
    }

    if ($reportType) {
        $countQuery .= " AND gr.report_type = ?";
        $countParams[] = $reportType;
    }

    $totalResult = Database::fetchAll($countQuery, $countParams);
    $total = $totalResult[0]->total ?? 0;

    echo json_encode([
        'success' => true,
        'reports' => $reports,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
