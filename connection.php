<?php
// main_connection.php

$dbHost = "127.0.0.1";
$dbUser = "3206_CENTRALIZED_DATABASE";
$dbPass = "1234";

// ✅ List only the databases you want to connect to
$targetDatabases = [
    "hr4_hr_4",
];

$connections = [];
$errors = [];

foreach ($targetDatabases as $dbName) {
    $conn = @mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

    if ($conn) {
        $connections[$dbName] = $conn;
    } else {
        $errors[] = "❌ Failed to connect to <strong>$dbName</strong>: " . mysqli_connect_error();
    }
}

// Log connection errors instead of printing them (prevents headers/output issues)
if (!empty($errors)) {
    foreach ($errors as $error) {
        error_log("DB Connection Error: " . strip_tags($error));
    }
}

function dd($data)
{
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
    exit;
}
