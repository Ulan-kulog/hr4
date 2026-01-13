<?php
require_once '../DB.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if user is logged in
// if (!isset($_SESSION['user_id'])) {
//     echo json_encode(['success' => false, 'message' => 'Not authenticated']);
//     exit;
// }

// $user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

try {
    // Get form data
    // $employee_id = $_POST['employee_id'] ?? $user_id;
    $employee_id = 1;
    $claim_type = $_POST['claim_type'] ?? '';
    $claim_category = $_POST['claim_category'] ?? '';
    $description = $_POST['description'] ?? '';
    $amount_requested = floatval($_POST['amount_requested'] ?? 0);
    $filed_date = $_POST['filed_date'] ?? date('Y-m-d');
    $status = $_POST['status'] ?? 'draft';
    $provider_name = $_POST['provider_name'] ?? null;
    $incident_date = $_POST['incident_date'] ?? null;

    // Server-side validation: incident_date required only for 'accident' claims
    if ($claim_type === 'accident') {
        if (empty($incident_date)) {
            echo json_encode(['success' => false, 'message' => 'Incident date is required for accident claims']);
            exit;
        }

        // Validate date format YYYY-MM-DD
        $d = DateTime::createFromFormat('Y-m-d', $incident_date);
        if (!($d && $d->format('Y-m-d') === $incident_date)) {
            echo json_encode(['success' => false, 'message' => 'Invalid incident_date format. Expected YYYY-MM-DD']);
            exit;
        }
    } else {
        // Ensure non-accident claims do not store an incident date
        $incident_date = null;
    }

    // Generate claim number
    $timestamp = time();
    $claim_number = "CLM-" . strtoupper($claim_type) . "-" . $timestamp;

    // Start transaction
    Database::beginTransaction();

    // Insert claim - removed provider_id
    $claim_id = Database::insertInto('employee_claims', [
        'employee_id' => $employee_id,
        'department_id' => null,
        'claim_number' => $claim_number,
        'claim_type' => $claim_type,
        'claim_category' => $claim_category,
        'provider_id' => null, // Set to null since we removed provider_id
        'description' => $description,
        'amount_requested' => $amount_requested,
        'amount_approved' => 0.00,
        'incident_date' => $incident_date,
        'filed_date' => $filed_date,
        'status' => $status
    ]);

    // Handle file uploads
    if (isset($_FILES['attachments']) && is_array($_FILES['attachments']['name'])) {
        $upload_dir = '../uploads/claims/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {
            if ($_FILES['attachments']['error'][$i] === UPLOAD_ERR_OK) {
                $file_name = time() . '_' . basename($_FILES['attachments']['name'][$i]);
                $file_path = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['attachments']['tmp_name'][$i], $file_path)) {
                    // Insert attachment record
                    Database::insertInto('claim_attachments', [
                        'claim_id' => $claim_id,
                        'attachment_type' => 'receipt',
                        'file_name' => $_FILES['attachments']['name'][$i],
                        'file_path' => $file_path,
                        'uploaded_by' => 1
                    ]);
                }
            }
        }
    }

    // Log initial status
    // Database::insertInto('claim_status_history', [
    //     'claim_id' => $claim_id,
    //     'old_status' => null,
    //     'new_status' => $status,
    //     'changed_by' => $user_id,
    //     'remarks' => 'Claim created'
    // ]);

    // Commit transaction
    Database::commit();

    echo json_encode([
        'success' => true,
        'message' => 'Claim ' . ($status === 'draft' ? 'saved as draft' : 'submitted') . ' successfully!',
        'claim_id' => $claim_id,
        'claim_number' => $claim_number
    ]);
} catch (Exception $e) {
    Database::rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
