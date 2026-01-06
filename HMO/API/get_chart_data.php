<?php
require_once '../DB.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $chartType = $input['chart_type'] ?? 'plan';
    $filter = $input['filter'] ?? 'all';

    $response = [
        'success' => true,
        'data' => []
    ];

    switch ($chartType) {
        case 'plan':
            // Plan Distribution by Type
            $query = 'SELECT benefit_type, COUNT(*) as count FROM benefits WHERE status = "active"';

            // Add time filter if needed
            if ($filter === 'month') {
                $query .= ' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)';
            } elseif ($filter === 'quarter') {
                $query .= ' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)';
            } elseif ($filter === 'year') {
                $query .= ' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)';
            }

            $query .= ' GROUP BY benefit_type ORDER BY count DESC';

            $result = Database::fetchAll($query);

            $labels = [];
            $data = [];
            foreach ($result as $row) {
                $labels[] = ucfirst($row->benefit_type);
                $data[] = $row->count;
            }

            $response['data'] = [
                'labels' => $labels,
                'data' => $data
            ];
            break;

        case 'enrollment':
            // Enrollment Status by Benefit
            $query = 'SELECT 
                b.benefit_name,
                COUNT(DISTINCT eb.employee_id) as enrolled_count,
                (SELECT COUNT(*) FROM employees) as total_employees
            FROM benefits b
            LEFT JOIN employee_benefits eb ON b.id = eb.benefit_id
            WHERE b.status = "active"';

            // Add time filter if needed
            if ($filter === 'month') {
                $query .= ' AND eb.created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)';
            } elseif ($filter === 'quarter') {
                $query .= ' AND eb.created_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)';
            } elseif ($filter === 'year') {
                $query .= ' AND eb.created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)';
            }

            $query .= ' GROUP BY b.id, b.benefit_name ORDER BY enrolled_count DESC LIMIT 5';

            $result = Database::fetchAll($query);

            $labels = [];
            $data = [];
            foreach ($result as $row) {
                $labels[] = $row->benefit_name;
                $rate = ($row->total_employees > 0) ? round(($row->enrolled_count / $row->total_employees) * 100) : 0;
                $data[] = $rate;
            }

            $response['data'] = [
                'labels' => $labels,
                'data' => $data
            ];
            break;

        case 'trend':
            // Monthly Enrollment Trend
            $months = intval($filter) ?: 6;

            $query = "SELECT 
                DATE_FORMAT(be.created_at, '%Y-%m') as month,
                COUNT(DISTINCT be.id) as enrollment_count
            FROM benefit_enrollment be
            WHERE be.created_at >= DATE_SUB(CURDATE(), INTERVAL $months MONTH)
            GROUP BY DATE_FORMAT(be.created_at, '%Y-%m')
            ORDER BY month";

            $result = Database::fetchAll($query);

            $labels = [];
            $data = [];
            foreach ($result as $row) {
                $date = new DateTime($row->month . '-01');
                $labels[] = $date->format('M y');
                $data[] = $row->enrollment_count;
            }

            $response['data'] = [
                'labels' => $labels,
                'data' => $data
            ];
            break;

        case 'dept':
            // Department-wise Enrollment
            $query = 'SELECT 
                e.department,
                COUNT(DISTINCT eb.employee_id) as enrolled_count,
                COUNT(DISTINCT e.id) as total_employees
            FROM employees e
            LEFT JOIN employee_benefits eb ON e.id = eb.employee_id
            GROUP BY e.department
            ORDER BY enrolled_count DESC';

            if ($filter === 'top5') {
                $query .= ' LIMIT 5';
            }

            $result = Database::fetchAll($query);

            $labels = [];
            $data = [];
            foreach ($result as $row) {
                $labels[] = $row->department ?: 'Unknown';
                $rate = ($row->total_employees > 0) ?
                    round(($row->enrolled_count / $row->total_employees) * 100) : 0;
                $data[] = $rate;
            }

            $response['data'] = [
                'labels' => $labels,
                'data' => $data
            ];
            break;
    }

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching chart data: ' . $e->getMessage()
    ]);
}
