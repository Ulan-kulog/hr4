<?php
require_once '../DB.php';
session_start();

header('Content-Type: application/json');

// Accept JSON body or form-encoded POST
$raw = file_get_contents('php://input');
$data = null;
if ($raw) {
    $json = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
        $data = $json;
    }
}
if ($data === null) {
    // fallback to regular POST
    $data = $_POST;
}

// Require enrollment id
if (empty($data['benefit_enrollment_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing benefit_enrollment_id']);
    exit;
}

try {
    $benefit_enrollment_id = $data['benefit_enrollment_id'];
    $employee_id = $data['employee_id'] ?? null;

    // Normalize values
    $start_date = array_key_exists('start_date', $data) ? ($data['start_date'] === '' ? null : $data['start_date']) : null;
    $end_date = array_key_exists('end_date', $data) ? ($data['end_date'] === '' ? null : $data['end_date']) : null;
    $status = array_key_exists('status', $data) ? $data['status'] : null; // allow empty string
    $payroll_frequency = array_key_exists('payroll_frequency', $data) ? $data['payroll_frequency'] : null;
    if (array_key_exists('payroll_deductible', $data)) {
        $payroll_deductible = (int) $data['payroll_deductible'];
    } else {
        $payroll_deductible = null;
    }

    // Build dynamic update SQL based on provided fields
    $updates = [];
    $params = [];
    if ($start_date !== null) {
        $updates[] = 'start_date = ?';
        $params[] = $start_date;
    }
    if ($end_date !== null) {
        $updates[] = 'end_date = ?';
        $params[] = $end_date;
    }
    if ($status !== null) {
        $updates[] = 'status = ?';
        $params[] = $status;
    }
    if ($payroll_frequency !== null) {
        $updates[] = 'payroll_frequency = ?';
        $params[] = $payroll_frequency;
    }
    if ($payroll_deductible !== null) {
        $updates[] = 'payroll_deductible = ?';
        $params[] = $payroll_deductible;
    }

    if (count($updates) === 0) {
        echo json_encode(['success' => false, 'message' => 'No updatable fields provided']);
        exit;
    }

    $updates[] = 'updated_at = NOW()';

    $sql = 'UPDATE benefit_enrollment SET ' . implode(', ', $updates) . ' WHERE id = ?';
    $params_with_id = array_merge($params, [$benefit_enrollment_id]);

    // If employee_id provided, include it in WHERE to be restrictive
    if ($employee_id) {
        $sql .= ' AND employee_id = ?';
        $params_with_id[] = $employee_id;
    }

    try {
        $updated = Database::execute($sql, $params_with_id);
    } catch (Exception $e) {
        error_log('[update_enrollment] Primary update failed: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Primary update error', 'error' => $e->getMessage(), 'sql' => $sql, 'params' => $params_with_id]);
        exit;
    }

    // If no rows affected and we used employee_id in WHERE, try id-only update
    if ($updated === 0 && $employee_id) {
        $sql2 = 'UPDATE benefit_enrollment SET ' . implode(', ', $updates) . ' WHERE id = ?';
        $params2 = array_merge($params, [$benefit_enrollment_id]);
        try {
            $updated = Database::execute($sql2, $params2);
        } catch (Exception $e) {
            error_log('[update_enrollment] Secondary update failed: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Secondary update error', 'error' => $e->getMessage(), 'sql' => $sql2, 'params' => $params2]);
            exit;
        }
    }

    if ($updated > 0) {
        echo json_encode(['success' => true, 'message' => 'Enrollment updated successfully', 'updated_rows' => $updated]);
        exit;
    }

    // No rows updated — fetch current row
    try {
        $current = Database::fetch('SELECT * FROM benefit_enrollment WHERE id = ?', [$benefit_enrollment_id]);
    } catch (Exception $e) {
        error_log('[update_enrollment] Fetch current failed: ' . $e->getMessage());
        $current = null;
    }

    // If status provided and differs, try forcing status-only update
    $current_status = $current->status ?? null;
    if ($status !== null && $current_status !== $status) {
        try {
            $forceUpdated = Database::execute('UPDATE benefit_enrollment SET status = ? WHERE id = ?', [$status, $benefit_enrollment_id]);
            $current = Database::fetch('SELECT * FROM benefit_enrollment WHERE id = ?', [$benefit_enrollment_id]);
            echo json_encode([
                'success' => $forceUpdated > 0,
                'message' => $forceUpdated > 0 ? 'Status forced update applied' : 'Status force update failed',
                'updated_rows' => $updated,
                'force_updated_rows' => $forceUpdated,
                'current_record' => $current
            ]);
            exit;
        } catch (Exception $e) {
            error_log('[update_enrollment] Force status update failed: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Force status update error', 'error' => $e->getMessage(), 'current_record' => $current]);
            exit;
        }
    }

    echo json_encode(['success' => false, 'message' => 'No changes were made or record not found', 'current_record' => $current]);
    exit;
} catch (Exception $e) {
    error_log('[update_enrollment] Unexpected error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Unexpected error', 'error' => $e->getMessage()]);
    exit;
}
