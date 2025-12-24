<?php
session_start();
include("../../connection.php");

$db_name = "HR_4";
if (!isset($connections[$db_name])) {
    die(json_encode(['error' => 'Connection not found']));
}
$conn = $connections[$db_name];

// Fetch compensation mix data
$data = [];

// Base Salary (from active employees)
$query1 = "SELECT SUM(salary) as total_base FROM employees WHERE status = 'active'";
$result1 = mysqli_query($conn, $query1);
$row1 = mysqli_fetch_assoc($result1);
$total_base = $row1['total_base'] ?: 0;

// Bonuses (estimated or from bonus_plans table)
$query2 = "SELECT SUM(amount_or_percentage) as total_bonus FROM bonus_plans WHERE status = 'active'";
$result2 = mysqli_query($conn, $query2);
$row2 = mysqli_fetch_assoc($result2);
$total_bonus = $row2['total_bonus'] ?: ($total_base * 0.15); // Estimate 15% of base

// Allowances
$query3 = "SELECT SUM(amount) as total_allowance FROM allowances WHERE status = 'active'";
$result3 = mysqli_query($conn, $query3);
$row3 = mysqli_fetch_assoc($result3);
$total_allowance = $row3['total_allowance'] ?: ($total_base * 0.12); // Estimate 12% of base

// Benefits (estimate as 8% of base)
$total_benefits = $total_base * 0.08;

$total_compensation = $total_base + $total_bonus + $total_allowance + $total_benefits;

if ($total_compensation > 0) {
    $data['labels'] = ['Base Salary', 'Bonuses', 'Allowances', 'Benefits'];
    $data['values'] = [
        round(($total_base / $total_compensation) * 100, 2),
        round(($total_bonus / $total_compensation) * 100, 2),
        round(($total_allowance / $total_compensation) * 100, 2),
        round(($total_benefits / $total_compensation) * 100, 2)
    ];
} else {
    // Fallback data
    $data['labels'] = ['Base Salary', 'Bonuses', 'Allowances', 'Benefits'];
    $data['values'] = [65, 15, 12, 8];
}

echo json_encode($data);
