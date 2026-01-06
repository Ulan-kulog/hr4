<?php
header('Content-Type: application/json');

require_once '../DB.php';

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid ID'
    ]);
    exit();
}

$updated = Database::updateTable('benefits', ['status' => 'active'], 'id = ?', [$id]);

if (!$updated) {
    echo json_encode([
        'success' => false,
        'message' => 'Benefit not found'
    ]);
    exit();
} else {
    echo json_encode([
        'success' => true,
        'message' => 'Benefit approved successfully'
    ]);
    exit();
}
