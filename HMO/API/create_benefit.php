<?php
require_once '../DB.php';
session_start();

function generateBenefitCode($benefitName, $prefix = 'BEN')
{
    $code = trim($benefitName);

    $code = preg_replace('/[^a-zA-Z0-9\s]/', '', $code);

    $code = preg_replace('/\s+/', '-', $code);

    $code = strtoupper($code);

    $date = date('Ymd');

    return "{$prefix}-{$code}-{$date}";
}

$benefit_code = generateBenefitCode($_POST['benefit_name'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'benefit_code' => $benefit_code,
        'benefit_name' => $_POST['benefit_name'] ?? '',
        'category_id' => $_POST['category_id'] ?? '',
        'policy_id' => $_POST['policy_id'] ?? '',
        'description' => $_POST['description'] ?? '',
        'provider_id' => !empty($_POST['provider_id']) ? $_POST['provider_id'] : null,
        'benefit_type' => $_POST['benefit_type'] ?? '',
        'value' => $_POST['value'] ?? 0,
        'unit' => $_POST['unit'] ?? 'amount',
        'company_cost_value' => $_POST['company_cost_value'] ?? 0,
        'company_cost_type' => $_POST['company_cost_type'] ?? 'percentage',
        'employee_cost_value' => $_POST['employee_cost_value'] ?? 0,
        'employee_cost_type' => $_POST['employee_cost_type'] ?? 'percentage',
        'is_taxable' => isset($_POST['is_taxable']) ? 1 : 0,
        'status' => 'pending'
    ];
    // dd($data);

    $result = Database::insert('INSERT INTO benefits (
    benefit_code,
    benefit_name,
    category_id,
    policy_id,
    description,
    provider_id,
    benefit_type,
    value,
    unit,
    company_cost_value,
    company_cost_type,
    employee_cost_value,
    employee_cost_type,
    is_taxable,
    status
) VALUES (
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ,?
)', array_values($data));

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Benefit created successfully']);
        $_SESSION['success'] = 'Benefit created successfully!';
        header('Location: ../benefits_enrollment.php');
        exit;
    } else {
        $_SESSION['error'] = 'Failed to create benefit';
        header('Location: index.php');
        exit;
    }
}
