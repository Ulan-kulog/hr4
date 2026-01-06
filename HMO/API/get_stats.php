<?php
require_once '../DB.php';
header('Content-Type: application/json');

try {
    // Calculate all stats
    $benefits_count = Database::fetchAll('SELECT COUNT(*) as count FROM benefits');
    $enrolled_employees = Database::fetchAll('SELECT COUNT(DISTINCT employee_id) as count FROM employee_benefits');
    $active_policies = Database::fetchAll("SELECT COUNT(*) as count FROM policies WHERE status = 'active'");
    $total_employees = Database::fetchAll('SELECT COUNT(*) as count FROM employees');
    $active_benefits = Database::fetchAll("SELECT COUNT(*) as count FROM benefits WHERE status = 'active'");
    $pending_enrollments = Database::fetchAll("SELECT COUNT(*) as count FROM benefit_enrollment WHERE status = 'pending'");
    $upcoming_renewals = Database::fetchAll("SELECT COUNT(*) as count FROM benefit_enrollment WHERE end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND status = 'active'");

    // Calculate coverage rate
    $coverage_rate = $total_employees[0]->count > 0 ?
        round(($enrolled_employees[0]->count / $total_employees[0]->count) * 100) : 0;

    echo json_encode([
        'success' => true,
        'total_benefits' => $benefits_count[0]->count,
        'enrolled_employees' => $enrolled_employees[0]->count,
        'active_policies' => $active_policies[0]->count,
        'total_employees' => $total_employees[0]->count,
        'coverage_rate' => $coverage_rate,
        'active_benefits' => $active_benefits[0]->count,
        'pending_enrollments' => $pending_enrollments[0]->count,
        'upcoming_renewals' => $upcoming_renewals[0]->count
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching stats: ' . $e->getMessage()
    ]);
}
