<?php

// header('Content-Type: application/json');
session_start();
require_once '../DB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    $result = Database::execute('DELETE FROM policies WHERE id = ?', [$id]);
    if ($result) {
        header('Location: ../benefits_enrollment.php');
    } else {
        header('Location: ../benefits_enrollment.php');
    }
} else {
    header('Location: ../benefits_enrollment.php');
    $_SESSION['message'] = 'an error occured.';
    http_response_code(405);
}
