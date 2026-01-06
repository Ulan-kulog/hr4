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

$deleted = Database::execute(
    "DELETE FROM benefits WHERE id = ?",
    [$id]
);

if (!$deleted) {
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
