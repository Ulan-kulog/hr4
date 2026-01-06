<?php
require_once '../DB.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(null);
    exit;
}

$id = $_GET['id'];
$benefit = Database::fetch('SELECT * FROM benefits WHERE id = ?', [$id]);

echo json_encode($benefit);
