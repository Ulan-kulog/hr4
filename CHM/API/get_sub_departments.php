<?php
header('Content-Type: application/json');
require_once '../../connection.php';

if (!isset($connections['hr4_hr_4'])) {
    http_response_code(500);
    echo json_encode([]);
    exit;
}

$conn = $connections['hr4_hr_4'];

$candidates = [
    'sub_departments',
    'subdepartments',
    'sub_department',
    'sub-departments',
    'sub_depts',
    'sub_dept',
    'department_subs',
    'department_sub'
];

foreach ($candidates as $tbl) {
    $escaped = mysqli_real_escape_string($conn, $tbl);
    $q = @mysqli_query($conn, "SELECT * FROM `" . $escaped . "` ORDER BY name ASC");
    if ($q !== false) {
        $rows = [];
        while ($r = mysqli_fetch_assoc($q)) {
            $rows[] = $r;
        }
        mysqli_free_result($q);
        if ($conn) mysqli_close($conn);
        echo json_encode($rows);
        exit;
    }
}

// Fallback: check for hierarchical parent column in departments
$q = @mysqli_query($conn, "SELECT * FROM `departments` LIMIT 1");
if ($q !== false) {
    $sample = mysqli_fetch_assoc($q);
    mysqli_free_result($q);
    if (is_array($sample)) {
        $parentKeys = ['parent_id', 'parent', 'parent_department_id', 'parent_dept_id'];
        $parentKey = null;
        foreach ($parentKeys as $k) {
            if (array_key_exists($k, $sample)) {
                $parentKey = $k;
                break;
            }
        }

        if ($parentKey) {
            $escapedKey = mysqli_real_escape_string($conn, $parentKey);
            $q2 = @mysqli_query($conn, "SELECT * FROM `departments` WHERE `" . $escapedKey . "` IS NOT NULL AND `" . $escapedKey . "` != '' AND `" . $escapedKey . "` != '0' ORDER BY name ASC");
            if ($q2 !== false) {
                $rows = [];
                while ($r = mysqli_fetch_assoc($q2)) {
                    $rows[] = $r;
                }
                mysqli_free_result($q2);
                if ($conn) mysqli_close($conn);
                echo json_encode($rows);
                exit;
            }
        }
    }
}

if ($conn) mysqli_close($conn);
echo json_encode([]);
