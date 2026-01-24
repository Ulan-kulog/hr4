<?php

/**
 * Fetch remote employees JSON and upsert into local `employees` table.
 */
require_once __DIR__ . '/../COMPENSATION/DB.php';

$apiUrl = 'https://hr1.soliera-hotel-restaurant.com/api/employees';

try {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    $curlErrNo = curl_errno($ch);

    if ($curlErrNo) {
        // If the error is a common Windows cURL SSL issuer problem, retry without verification
        if (stripos($curlErr, 'SSL certificate problem') !== false || stripos($curlErr, 'unable to get local issuer certificate') !== false) {
            // Retry with SSL verification disabled (not recommended for production)
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (curl_errno($ch)) {
                $err = curl_error($ch);
                curl_close($ch);
                throw new Exception('cURL error: ' . $err);
            }
            // Log a warning so the issue can be fixed properly by installing a CA bundle
            error_log('Warning: SSL verification disabled for ' . $apiUrl);
        } else {
            curl_close($ch);
            throw new Exception('cURL error: ' . $curlErr);
        }
    }

    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception('Unexpected response code: ' . $httpCode);
    }

    $items = json_decode($response, true);
    if (!is_array($items)) {
        throw new Exception('Invalid JSON response');
    }

    $inserted = 0;
    $updated = 0;

    foreach ($items as $item) {
        if (!isset($item['employee_id'])) {
            continue;
        }

        $employee_id = $item['employee_id'];

        $row = [
            'id' => $employee_id,
            'department_id' => $item['department_id'] ?? null,
            'sub_department_id' => $item['sub_department_id'] ?? null,
            'employee_code' => $item['employee_code'] ?? null,
            'applicant_id' => $item['application_id'] ?? null,
            'first_name' => $item['first_name'] ?? null,
            'middle_name' => $item['middle_name'] ?? null,
            'last_name' => $item['last_name'] ?? null,
            'job' => $item['job'] ?? null,
            'date_of_birth' => $item['date_of_birth'] ?? null,
            'phone_number' => $item['phone_number'] ?? null,
            'email' => $item['email'] ?? null,
            'address' => $item['address'] ?? null,
            'gender' => $item['gender'] ?? null,
            'emergency_contact_name' => $item['emergency_contact_name'] ?? null,
            'emergency_contact_number' => $item['emergency_contact_number'] ?? null,
            'emergency_contact_relationship' => $item['emergency_contact_relationship'] ?? null,
            'mentors' => isset($item['mentors']) && !is_string($item['mentors']) ? json_encode($item['mentors']) : ($item['mentors'] ?? null),
            'hire_date' => $item['hire_date'] ?? null,
            'salary' => $item['salary'] ?? null,
            'basic_salary' => $item['basic_salary'] ?? null,
            'employment_status' => $item['employment_status'] ?? null,
            'work_status' => $item['work_status'] ?? null,
            'separation_status' => $item['separation_status'] ?? null,
            'task_id' => $item['task_id'] ?? null,
            'has_contract' => $item['has_contract'] ?? null,
            'created_at' => $item['created_at'] ?? null,
            'updated_at' => $item['updated_at'] ?? null,
        ];

        if (Database::exists('SELECT COUNT(*) FROM employees WHERE id = ?', [$employee_id])) {
            $affected = Database::updateTable('employees', $row, 'id = ?', [$employee_id]);
            if ($affected > 0) {
                $updated++;
            }
        } else {
            Database::insertInto('employees', $row);
            $inserted++;
        }
    }

    echo json_encode(['status' => 'ok', 'inserted' => $inserted, 'updated' => $updated]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
