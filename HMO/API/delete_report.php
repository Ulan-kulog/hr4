<?php
require_once '../DB.php';
session_start();

// if (!isset($_SESSION['user_id'])) {
//     http_response_code(401);
//     echo json_encode(['error' => 'Unauthorized']);
//     exit;
// }

$data = json_decode(file_get_contents('php://input'), true);
$reportId = $data['report_id'] ?? 0;
$userId = $_SESSION['user_id'];

if (!$reportId) {
    http_response_code(400);
    echo json_encode(['error' => 'Report ID required']);
    exit;
}

try {
    // Get report details
    $query = "SELECT * FROM generated_reports WHERE id = ? AND generated_by = ?";
    $reports = Database::fetchAll($query, [$reportId, $userId]);

    if (empty($reports)) {
        throw new Exception('Report not found or access denied');
    }

    $report = $reports[0];

    // Delete file if exists
    if (file_exists($report->file_path)) {
        unlink($report->file_path);
    }

    // Update status to deleted
    Database::execute(
        "UPDATE generated_reports SET status = 'deleted' WHERE id = ?",
        [$reportId]
    );

    echo json_encode([
        'success' => true,
        'message' => 'Report deleted successfully'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
