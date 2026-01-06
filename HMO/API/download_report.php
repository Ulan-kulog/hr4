<?php
require_once '../DB.php';
session_start();

// if (!isset($_SESSION['user_id'])) {
//     http_response_code(401);
//     echo 'Unauthorized';
//     exit;
// }

$reportId = $_GET['id'] ?? 0;
$userId = $_SESSION['user_id'];

if (!$reportId) {
    http_response_code(400);
    echo 'Report ID required';
    exit;
}

try {
    // Get report details
    $query = "
        SELECT gr.*, CONCAT(e.first_name, ' ', e.last_name) as generated_by_name
        FROM generated_reports gr
        LEFT JOIN employees e ON gr.generated_by = e.id
        WHERE gr.id = ? AND gr.generated_by = ? AND gr.status = 'generated'
    ";

    $reports = Database::fetchAll($query, [$reportId, $userId]);

    if (empty($reports)) {
        throw new Exception('Report not found or access denied');
    }

    $report = $reports[0];

    if (!file_exists($report->file_path)) {
        throw new Exception('Report file not found');
    }

    // Update download count
    Database::execute(
        "UPDATE generated_reports SET download_count = download_count + 1 WHERE id = ?",
        [$reportId]
    );

    // Set headers for download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $report->file_name . '"');
    header('Content-Length: ' . $report->file_size);
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Clear output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Read and output file
    readfile($report->file_path);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo 'Error: ' . $e->getMessage();
}
