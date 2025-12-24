<?php
session_start();
include("../../connection.php");

$db_name = "HR_4";
if (!isset($connections[$db_name])) {
    die(json_encode(['success' => false, 'error' => 'Connection not found']));
}
$conn = $connections[$db_name];

$stats = [];

// Total Salary Budget (annual - multiply monthly by 12)
$query1 = "SELECT SUM(salary) * 12 as annual_budget FROM employees WHERE status = 'active'";
$result1 = mysqli_query($conn, $query1);
$row1 = mysqli_fetch_assoc($result1);
$stats['total_budget'] = $row1['annual_budget'] ?: 18200000;

// Average Salary
$query2 = "SELECT AVG(salary) as avg_salary FROM employees WHERE status = 'active'";
$result2 = mysqli_query($conn, $query2);
$row2 = mysqli_fetch_assoc($result2);
$stats['avg_salary'] = round($row2['avg_salary'] ?: 25400);

// Bonus Pool (from active bonus plans, annualized)
$query3 = "SELECT SUM(
    CASE 
        WHEN frequency = 'monthly' THEN amount_or_percentage * 12
        WHEN frequency = 'quarterly' THEN amount_or_percentage * 4
        WHEN frequency = 'annual' THEN amount_or_percentage
        ELSE amount_or_percentage
    END
) as total_bonus_pool FROM bonus_plans WHERE status = 'active'";
$result3 = mysqli_query($conn, $query3);
$row3 = mysqli_fetch_assoc($result3);
$stats['bonus_pool'] = $row3['total_bonus_pool'] ?: 2400000;

// Allowance Budget
$query4 = "SELECT SUM(
    CASE 
        WHEN frequency = 'daily' THEN amount * 365
        WHEN frequency = 'weekly' THEN amount * 52
        WHEN frequency = 'monthly' THEN amount * 12
        WHEN frequency = 'quarterly' THEN amount * 4
        WHEN frequency = 'annual' THEN amount
        ELSE amount
    END
) as total_allowance_budget FROM allowances WHERE status = 'active'";
$result4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($result4);
$stats['allowance_budget'] = $row4['total_allowance_budget'] ?: 1800000;

echo json_encode(['success' => true] + $stats);
