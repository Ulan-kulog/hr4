<?php
// API endpoint: HMO/API/export_analytics.php
// Accepts JSON payload with `enrollment` and `department` data and returns a CSV download.

header_remove();
// Only POST with JSON allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed';
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo 'Invalid JSON payload';
    exit;
}

$enrollment = $data['enrollment'] ?? null;
$department = $data['department'] ?? null;

// Prepare CSV in memory
$fh = fopen('php://temp', 'w+');

// Section: Top Benefits Enrollment
fputcsv($fh, ['Top Benefits Enrollment'], ',', '"', "\\");
fputcsv($fh, ['Benefit', 'Enrolled Employees'], ',', '"', "\\");
if (is_array($enrollment) && isset($enrollment['labels']) && isset($enrollment['data'])) {
    $labels = $enrollment['labels'];
    $values = $enrollment['data'];
    for ($i = 0; $i < count($labels); $i++) {
        $label = $labels[$i] ?? '';
        $val = $values[$i] ?? '';
        fputcsv($fh, [$label, $val], ',', '"', "\\");
    }
}
// Add summary row if present
if (is_array($enrollment) && isset($enrollment['total_employees'])) {
    fputcsv($fh, [], ',', '"', "\\");
    fputcsv($fh, ['Total Employees', $enrollment['total_employees']], ',', '"', "\\");
}

fputcsv($fh, [], ',', '"', "\\");

// Section: Department Enrollment
fputcsv($fh, ['Department Enrollment'], ',', '"', "\\");
fputcsv($fh, ['Department', 'Enrolled Employees'], ',', '"', "\\");
if (is_array($department)) {
    foreach ($department as $d) {
        // Support both associative arrays and objects decoded as arrays
        $name = isset($d['department_name']) ? $d['department_name'] : (isset($d->department_name) ? $d->department_name : '');
        $count = isset($d['enrolled_count']) ? $d['enrolled_count'] : (isset($d->enrolled_count) ? $d->enrolled_count : '');
        fputcsv($fh, [$name, $count], ',', '"', "\\");
    }
}

// Rewind and output
rewind($fh);
$csv = stream_get_contents($fh);
fclose($fh);

$filename = 'analytics_export_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($csv));
echo $csv;
exit;
