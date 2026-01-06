<?php
header('Content-Type: application/json');
require_once '../DB.php';

if (isset($_GET['id'])) {
    try {
        $claim = Database::fetch("SELECT * FROM claims WHERE id = ?", [$_GET['id']]);

        if ($claim) {
            // Convert to array for JSON response
            $claimArray = [];
            foreach ($claim as $key => $value) {
                $claimArray[$key] = $value;
            }
            echo json_encode($claimArray);
        } else {
            echo json_encode(['error' => 'Claim not found']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'No ID provided']);
}
