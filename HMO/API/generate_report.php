<?php
require_once '../DB.php';
session_start();

// Check authentication
// if (!isset($_SESSION['user_id'])) {
//     http_response_code(401);
//     echo json_encode(['error' => 'Unauthorized']);
//     exit;
// }

// Get report parameters
$data = json_decode(file_get_contents('php://input'), true);
$reportType = $data['report_type'] ?? '';
$dateFrom = $data['date_from'] ?? null;
$dateTo = $data['date_to'] ?? null;
$department = $data['department'] ?? null;
$employeeId = $data['employee_id'] ?? null;
$userId = $_SESSION['user_id'];

// Validate report type
$validReportTypes = [
    'enrollment_summary',
    'benefit_utilization',
    'cost_analysis',
    'department_coverage',
    'enrollment_trends',
    'employee_benefit_statement'
];

if (!in_array($reportType, $validReportTypes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid report type']);
    exit;
}

try {
    // Generate report data
    $reportData = generateReportData($reportType, $dateFrom, $dateTo, $department, $employeeId);

    // Generate CSV content
    $csvContent = generateCSVContent($reportData, $reportType);

    // Create filename
    $fileName = generateFileName($reportType);

    // Store report in database
    $reportId = storeReportInDatabase($reportType, $fileName, $csvContent, $userId, [
        'date_from' => $dateFrom,
        'date_to' => $dateTo,
        'department' => $department,
        'employee_id' => $employeeId
    ]);

    // Return CSV data for download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Content-Length: ' . strlen($csvContent));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo $csvContent;
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function generateReportData($reportType, $dateFrom, $dateTo, $department, $employeeId)
{
    switch ($reportType) {
        case 'enrollment_summary':
            return generateEnrollmentSummary($dateFrom, $dateTo, $department);
        case 'benefit_utilization':
            return generateBenefitUtilization($dateFrom, $dateTo);
        case 'cost_analysis':
            return generateCostAnalysis($dateFrom, $dateTo, $department);
        case 'department_coverage':
            return generateDepartmentCoverage();
        case 'enrollment_trends':
            return generateEnrollmentTrends($dateFrom, $dateTo);
        case 'employee_benefit_statement':
            if (!$employeeId) throw new Exception('Employee ID required');
            return generateEmployeeBenefitStatement($employeeId);
        default:
            throw new Exception('Unknown report type');
    }
}

function generateEnrollmentSummary($dateFrom = null, $dateTo = null, $department = null)
{
    $query = "
        SELECT 
            e.department,
            COUNT(DISTINCT e.id) as total_employees,
            COUNT(DISTINCT eb.employee_id) as enrolled_employees,
            ROUND((COUNT(DISTINCT eb.employee_id) * 100.0 / COUNT(DISTINCT e.id)), 2) as enrollment_rate,
            COUNT(DISTINCT CASE WHEN be.status = 'active' THEN eb.id END) as active_enrollments,
            COUNT(DISTINCT CASE WHEN be.status = 'pending' THEN eb.id END) as pending_enrollments
        FROM employees e
        LEFT JOIN employee_benefits eb ON e.id = eb.employee_id
        LEFT JOIN benefit_enrollment be ON be.id = eb.benefit_enrollment_id
        WHERE 1=1
    ";

    $params = [];

    if ($dateFrom) {
        $query .= " AND be.start_date >= ?";
        $params[] = $dateFrom;
    }

    if ($dateTo) {
        $query .= " AND be.end_date <= ?";
        $params[] = $dateTo;
    }

    if ($department) {
        $query .= " AND e.department = ?";
        $params[] = $department;
    }

    $query .= " GROUP BY e.department";

    return Database::fetchAll($query, $params);
}

function generateBenefitUtilization($dateFrom = null, $dateTo = null)
{
    $query = "
        SELECT 
            b.benefit_name,
            b.benefit_type,
            p.name as provider_name,
            COUNT(DISTINCT eb.employee_id) as enrolled_count,
            (SELECT COUNT(*) FROM employees) as total_employees,
            ROUND((COUNT(DISTINCT eb.employee_id) * 100.0 / (SELECT COUNT(*) FROM employees)), 2) as utilization_rate
        FROM benefits b
        LEFT JOIN employee_benefits eb ON b.id = eb.benefit_id
        LEFT JOIN providers p ON b.provider_id = p.id
        WHERE b.status = 'active'
    ";

    $params = [];

    if ($dateFrom || $dateTo) {
        $query .= " AND EXISTS (
            SELECT 1 FROM benefit_enrollment be 
            WHERE be.id = eb.benefit_enrollment_id
        ";
        if ($dateFrom) {
            $query .= " AND be.start_date >= ?";
            $params[] = $dateFrom;
        }
        if ($dateTo) {
            $query .= " AND be.end_date <= ?";
            $params[] = $dateTo;
        }
        $query .= ")";
    }

    $query .= " GROUP BY b.id, b.benefit_name, b.benefit_type, p.name
                ORDER BY utilization_rate DESC";

    return Database::fetchAll($query, $params);
}

function generateCostAnalysis($dateFrom = null, $dateTo = null, $department = null)
{
    $query = "
        SELECT 
            e.department,
            b.benefit_name,
            b.benefit_type,
            COUNT(DISTINCT eb.employee_id) as employee_count,
            SUM(CASE 
                WHEN b.benefit_type = 'fixed' THEN b.value 
                ELSE 0 
            END) as estimated_monthly_cost
        FROM employee_benefits eb
        JOIN employees e ON e.id = eb.employee_id
        JOIN benefits b ON b.id = eb.benefit_id
        JOIN benefit_enrollment be ON be.id = eb.benefit_enrollment_id
        WHERE be.status = 'active'
        AND b.status = 'active'
    ";

    $params = [];

    if ($dateFrom) {
        $query .= " AND be.start_date >= ?";
        $params[] = $dateFrom;
    }

    if ($dateTo) {
        $query .= " AND be.end_date <= ?";
        $params[] = $dateTo;
    }

    if ($department) {
        $query .= " AND e.department = ?";
        $params[] = $department;
    }

    $query .= " GROUP BY e.department, b.benefit_name, b.benefit_type
                ORDER BY e.department, estimated_monthly_cost DESC";

    return Database::fetchAll($query, $params);
}

function generateDepartmentCoverage()
{
    $query = "
        SELECT 
            e.department,
            COUNT(DISTINCT e.id) as total_employees,
            COUNT(DISTINCT eb.employee_id) as enrolled_employees,
            ROUND((COUNT(DISTINCT eb.employee_id) * 100.0 / COUNT(DISTINCT e.id)), 2) as coverage_rate,
            GROUP_CONCAT(DISTINCT b.benefit_name ORDER BY b.benefit_name SEPARATOR ', ') as benefits_offered
        FROM employees e
        LEFT JOIN employee_benefits eb ON e.id = eb.employee_id
        LEFT JOIN benefits b ON b.id = eb.benefit_id
        GROUP BY e.department
        ORDER BY coverage_rate DESC
    ";

    return Database::fetchAll($query);
}

function generateEnrollmentTrends($dateFrom = null, $dateTo = null)
{
    $defaultFrom = date('Y-m-01', strtotime('-11 months'));
    $defaultTo = date('Y-m-t');

    $dateFrom = $dateFrom ?: $defaultFrom;
    $dateTo = $dateTo ?: $defaultTo;

    $query = "
        SELECT 
            DATE_FORMAT(be.created_at, '%Y-%m') as month,
            COUNT(DISTINCT be.id) as new_enrollments,
            COUNT(DISTINCT CASE WHEN be.status = 'active' THEN be.id END) as active_enrollments,
            COUNT(DISTINCT CASE WHEN be.status = 'pending' THEN be.id END) as pending_enrollments
        FROM benefit_enrollment be
        WHERE be.created_at BETWEEN ? AND LAST_DAY(?)
        GROUP BY DATE_FORMAT(be.created_at, '%Y-%m')
        ORDER BY month
    ";

    return Database::fetchAll($query, [$dateFrom, $dateTo]);
}

function generateEmployeeBenefitStatement($employeeId)
{
    $query = "
        SELECT 
            e.employee_code,
            CONCAT(e.first_name, ' ', e.last_name) as employee_name,
            e.department,
            e.email,
            b.benefit_name,
            b.benefit_type,
            p.name as provider_name,
            be.start_date,
            be.end_date,
            be.status as enrollment_status,
            CASE 
                WHEN b.benefit_type = 'fixed' THEN CONCAT('$', FORMAT(b.value, 2))
                WHEN b.benefit_type = 'percentage' THEN CONCAT(b.value, '%')
                ELSE b.value
            END as benefit_value,
            CASE 
                WHEN b.is_taxable = 1 THEN 'Taxable'
                ELSE 'Non-Taxable'
            END as taxable_status
        FROM employees e
        JOIN employee_benefits eb ON e.id = eb.employee_id
        JOIN benefits b ON b.id = eb.benefit_id
        LEFT JOIN providers p ON b.provider_id = p.id
        LEFT JOIN benefit_enrollment be ON be.id = eb.benefit_enrollment_id
        WHERE e.id = ?
        ORDER BY b.benefit_name
    ";

    $data = Database::fetchAll($query, [$employeeId]);

    if (empty($data)) {
        throw new Exception('No benefits found for this employee');
    }

    return [
        'employee_info' => [
            'employee_code' => $data[0]->employee_code,
            'employee_name' => $data[0]->employee_name,
            'department' => $data[0]->department,
            'email' => $data[0]->email
        ],
        'benefits' => $data
    ];
}

function generateCSVContent($reportData, $reportType)
{
    // Start output buffering to capture CSV content
    ob_start();
    $output = fopen('php://output', 'w');

    // Add report header
    fputcsv($output, [strtoupper(str_replace('_', ' ', $reportType)) . ' REPORT']);
    fputcsv($output, ['Generated on: ' . date('Y-m-d H:i:s')]);
    fputcsv($output, []); // Empty row

    // Add data based on report type
    switch ($reportType) {
        case 'enrollment_summary':
            fputcsv($output, ['Department', 'Total Employees', 'Enrolled Employees', 'Enrollment Rate (%)', 'Active Enrollments', 'Pending Enrollments']);
            foreach ($reportData as $row) {
                fputcsv($output, [
                    $row->department ?? 'Unknown',
                    $row->total_employees ?? 0,
                    $row->enrolled_employees ?? 0,
                    $row->enrollment_rate ?? 0,
                    $row->active_enrollments ?? 0,
                    $row->pending_enrollments ?? 0
                ]);
            }
            break;

        case 'benefit_utilization':
            fputcsv($output, ['Benefit Name', 'Benefit Type', 'Provider', 'Enrolled Count', 'Total Employees', 'Utilization Rate (%)']);
            foreach ($reportData as $row) {
                fputcsv($output, [
                    $row->benefit_name ?? 'Unknown',
                    $row->benefit_type ?? 'N/A',
                    $row->provider_name ?? 'N/A',
                    $row->enrolled_count ?? 0,
                    $row->total_employees ?? 0,
                    $row->utilization_rate ?? 0
                ]);
            }
            break;

        case 'cost_analysis':
            fputcsv($output, ['Department', 'Benefit Name', 'Benefit Type', 'Employee Count', 'Estimated Monthly Cost']);
            foreach ($reportData as $row) {
                fputcsv($output, [
                    $row->department ?? 'Unknown',
                    $row->benefit_name ?? 'Unknown',
                    $row->benefit_type ?? 'N/A',
                    $row->employee_count ?? 0,
                    $row->estimated_monthly_cost ?? 0
                ]);
            }
            break;

        case 'department_coverage':
            fputcsv($output, ['Department', 'Total Employees', 'Enrolled Employees', 'Coverage Rate (%)', 'Benefits Offered']);
            foreach ($reportData as $row) {
                fputcsv($output, [
                    $row->department ?? 'Unknown',
                    $row->total_employees ?? 0,
                    $row->enrolled_employees ?? 0,
                    $row->coverage_rate ?? 0,
                    $row->benefits_offered ?? 'None'
                ]);
            }
            break;

        case 'enrollment_trends':
            fputcsv($output, ['Month', 'New Enrollments', 'Active Enrollments', 'Pending Enrollments']);
            foreach ($reportData as $row) {
                fputcsv($output, [
                    $row->month ?? 'Unknown',
                    $row->new_enrollments ?? 0,
                    $row->active_enrollments ?? 0,
                    $row->pending_enrollments ?? 0
                ]);
            }
            break;

        case 'employee_benefit_statement':
            // Employee Info
            fputcsv($output, ['EMPLOYEE INFORMATION']);
            fputcsv($output, ['Employee Code:', $reportData['employee_info']['employee_code']]);
            fputcsv($output, ['Employee Name:', $reportData['employee_info']['employee_name']]);
            fputcsv($output, ['Department:', $reportData['employee_info']['department']]);
            fputcsv($output, ['Email:', $reportData['employee_info']['email']]);
            fputcsv($output, ['Statement Period:', date('F Y')]);
            fputcsv($output, []); // Empty row

            // Benefits
            fputcsv($output, ['BENEFITS ENROLLED']);
            fputcsv($output, ['Benefit Name', 'Benefit Type', 'Provider', 'Start Date', 'End Date', 'Status', 'Benefit Value', 'Taxable']);
            foreach ($reportData['benefits'] as $row) {
                fputcsv($output, [
                    $row->benefit_name ?? 'Unknown',
                    $row->benefit_type ?? 'N/A',
                    $row->provider_name ?? 'N/A',
                    $row->start_date ?? 'N/A',
                    $row->end_date ?? 'N/A',
                    $row->enrollment_status ?? 'N/A',
                    $row->benefit_value ?? 'N/A',
                    $row->taxable_status ?? 'N/A'
                ]);
            }
            break;
    }

    fclose($output);
    return ob_get_clean();
}

function generateFileName($reportType)
{
    $typeNames = [
        'enrollment_summary' => 'Enrollment_Summary',
        'benefit_utilization' => 'Benefit_Utilization',
        'cost_analysis' => 'Cost_Analysis',
        'department_coverage' => 'Department_Coverage',
        'enrollment_trends' => 'Enrollment_Trends',
        'employee_benefit_statement' => 'Employee_Benefit_Statement'
    ];

    $typeName = $typeNames[$reportType] ?? $reportType;
    $timestamp = date('Ymd_His');

    return "{$typeName}_{$timestamp}.csv";
}

function storeReportInDatabase($reportType, $fileName, $csvContent, $userId, $parameters)
{
    // Create reports directory if it doesn't exist
    $reportsDir = __DIR__ . '/../reports';
    if (!is_dir($reportsDir)) {
        mkdir($reportsDir, 0755, true);
    }

    // Save file to disk
    $filePath = $reportsDir . '/' . $fileName;
    file_put_contents($filePath, $csvContent);

    // Get file size
    $fileSize = filesize($filePath);

    // Format report name
    $reportName = ucwords(str_replace('_', ' ', $reportType)) . ' Report';

    // Insert into database
    $query = "
        INSERT INTO generated_reports 
        (report_name, report_type, file_name, file_path, file_size, generated_by, parameters) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ";

    Database::execute($query, [
        $reportName,
        $reportType,
        $fileName,
        $filePath,
        $fileSize,
        $userId,
        json_encode($parameters)
    ]);

    return Database::lastInsertId();
}
