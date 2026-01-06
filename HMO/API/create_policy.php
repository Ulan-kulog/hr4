<?php

require_once '../DB.php';

// dd($_POST);
function generatePolicyCode(string $policyName): string
{
    // Words to ignore
    $ignore = ['policy', 'policies', 'benefit', 'benefits', 'the', 'and', 'of', 'for'];

    // Normalize
    $policyName = strtoupper(trim($policyName));

    // Split words
    $words = preg_split('/\s+/', $policyName);

    // Build abbreviation
    $abbr = '';
    foreach ($words as $word) {
        if (!in_array(strtolower($word), $ignore)) {
            $abbr .= substr($word, 0, 1);
        }
    }

    // Ensure at least 3 letters
    $abbr = str_pad($abbr, 3, 'X');

    // Generate random 5-digit number
    $random = random_int(10000, 99999);

    return "POL-{$abbr}-{$random}";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $policy_name = $_POST['policy_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $effective_date = $_POST['effective_date'] ?? '';
    $expiration_date = $_POST['expiration_date'] ?? null;
    $code = generatePolicyCode($policy_name);
    $result = Database::insertInto('policies', [
        'policy_code' => $code,
        'policy_name' => $policy_name,
        'description' => $description,
        'status' => 'inactive',
        'effective_date' => $effective_date,
        'expiration_date' => $expiration_date ?: null,
        'created_at' => date('Y-m-d H:i:s'),
        'created_by' => $_SESSION['user_id'] ?? null
    ]);

    try {
        if ($result) {
            header('Location: ../benefits_enrollment.php');
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to create policy: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
