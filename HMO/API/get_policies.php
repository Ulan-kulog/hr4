<?php

header('Content-Type: application/json');
require_once '../DB.php';
require_once '../DB.php';
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'Invalid policy ID']);
    exit;
}

$policyId = intval($_GET['id']);

$policy = Database::fetch('SELECT * FROM policies WHERE id = ?', [$policyId]);

if ($policy) {
    header('Content-Type: application/json');
    echo json_encode($policy);
} else {
    echo json_encode(['error' => 'Policy not found']);
}
