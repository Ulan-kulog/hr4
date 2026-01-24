<?php
// API/compensation_api.php
require_once '../DB.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'submit_compensation_request':
    case 'save_draft':
        handleCompensationRequest();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function handleCompensationRequest()
{
    global $db;

    try {
        // Validate required fields
        $required = ['employee_id', 'request_type', 'requested_amount', 'frequency', 'effective_date', 'reason', 'justification'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Required field '$field' is missing");
            }
        }

        // Handle file uploads
        $supporting_docs = [];
        if (!empty($_FILES['supporting_docs'])) {
            $uploadDir = '../uploads/compensation/' . date('Y/m/');
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach ($_FILES['supporting_docs']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['supporting_docs']['error'][$key] === UPLOAD_ERR_OK) {
                    $fileName = uniqid() . '_' . basename($_FILES['supporting_docs']['name'][$key]);
                    $filePath = $uploadDir . $fileName;

                    if (move_uploaded_file($tmp_name, $filePath)) {
                        $supporting_docs[] = $filePath;
                    }
                }
            }
        }

        // Prepare data for compensation_requests table
        $requestData = [
            'employee_id' => $_POST['employee_id'],
            'request_type' => $_POST['request_type'],
            'current_amount' => $_POST['current_amount'] ?? 0,
            'requested_amount' => $_POST['requested_amount'],
            'currency' => $_POST['currency'] ?? 'PHP',
            'frequency' => $_POST['frequency'],
            'effective_date' => $_POST['effective_date'],
            'reason' => $_POST['reason'],
            'justification' => $_POST['justification'],
            'supporting_docs' => !empty($supporting_docs) ? json_encode($supporting_docs) : null,
            'status' => $_POST['action'] === 'save_draft' ? 'draft' : 'submitted',
            'submitted_at' => $_POST['action'] === 'save_draft' ? null : date('Y-m-d H:i:s')
        ];

        // Insert into compensation_requests
        $sql = "INSERT INTO compensation_requests 
                (employee_id, request_type, current_amount, requested_amount, currency, 
                 frequency, effective_date, reason, justification, supporting_docs, 
                 status, submitted_at, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $params = [
            $requestData['employee_id'],
            $requestData['request_type'],
            $requestData['current_amount'],
            $requestData['requested_amount'],
            $requestData['currency'],
            $requestData['frequency'],
            $requestData['effective_date'],
            $requestData['reason'],
            $requestData['justification'],
            $requestData['supporting_docs'],
            $requestData['status'],
            $requestData['submitted_at']
        ];

        $requestId = Database::insert($sql, $params);

        // If it's a submission (not draft), also create a pending compensation record
        if ($_POST['action'] === 'submit_compensation_request') {
            $compData = [
                'employee_id' => $_POST['employee_id'],
                'request_id' => $requestId,
                'compensation_type' => $_POST['request_type'],
                'amount' => $_POST['requested_amount'],
                'currency' => $_POST['currency'] ?? 'PHP',
                'frequency' => $_POST['frequency'],
                'effective_date' => $_POST['effective_date'],
                'reason' => $_POST['reason'],
                'justification' => $_POST['justification'],
                'supporting_docs' => $requestData['supporting_docs'],
                'status' => 'pending'
            ];

            $compSql = "INSERT INTO compensations 
                       (employee_id, request_id, compensation_type, amount, currency, 
                        frequency, effective_date, reason, justification, supporting_docs, status) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            Database::insert($compSql, [
                $compData['employee_id'],
                $compData['request_id'],
                $compData['compensation_type'],
                $compData['amount'],
                $compData['currency'],
                $compData['frequency'],
                $compData['effective_date'],
                $compData['reason'],
                $compData['justification'],
                $compData['supporting_docs'],
                $compData['status']
            ]);

            // TODO: Send notification to HR/Manager
            // TODO: Send confirmation email to employee
        }

        echo json_encode([
            'success' => true,
            'message' => $_POST['action'] === 'save_draft' ? 'Draft saved successfully' : 'Request submitted successfully',
            'request_id' => $requestId
        ]);
    } catch (Exception $e) {
        error_log("Compensation request error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error processing request: ' . $e->getMessage()
        ]);
    }
}
