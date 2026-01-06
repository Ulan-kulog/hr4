<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$reportsDir = '../reports/';
$reports = [];

if (is_dir($reportsDir)) {
    $files = scandir($reportsDir, SCANDIR_SORT_DESCENDING);

    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $filePath = $reportsDir . $file;
            $fileInfo = [
                'name' => $file,
                'size' => filesize($filePath),
                'modified' => date('Y-m-d H:i:s', filemtime($filePath)),
                'download_url' => 'API/download_report.php?file=' . urlencode($file)
            ];
            $reports[] = $fileInfo;
        }
    }
}

echo json_encode([
    'success' => true,
    'reports' => array_slice($reports, 0, 10) // Show latest 10
]);
