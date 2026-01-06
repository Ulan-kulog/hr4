<?php
require_once '../DB.php';
header('Content-Type: application/json');

$providers = Database::fetchAll('SELECT id, name FROM providers WHERE status = "active" ORDER BY name ASC');

echo json_encode($providers);
