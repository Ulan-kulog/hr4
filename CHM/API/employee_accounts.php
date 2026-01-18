<?php
// Minimal API exposing employee_accounts table only
session_start();
include("../../connection.php");

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$db_name = 'hr4_hr_4';
$conn = $connections[$db_name] ?? null;
if (!$conn) {
    http_response_code(500);
    echo json_encode([]);
    exit;
}

// Helper: safe prepare wrapper
function safe_prepare($conn, $sql)
{
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;
    return $stmt;
}

// GET single by id or list
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Single record by id, employee_id, or employee_code
    if (isset($_GET['id']) || isset($_GET['employee_id']) || isset($_GET['employee_code'])) {
        $id = $_GET['id'] ?? $_GET['employee_id'] ?? $_GET['employee_code'];
        $sql = "SELECT * FROM employee_accounts WHERE id = ? OR employee_id = ? OR employee_code = ? LIMIT 1";
        $stmt = safe_prepare($conn, $sql);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode([]);
            if ($conn) $conn->close();
            exit;
        }
        $stmt->bind_param('sss', $id, $id, $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        if ($res && method_exists($res, 'free')) $res->free();
        if ($stmt) $stmt->close();
        if ($conn) $conn->close();
        echo json_encode($row ?: (object)[]);
        exit;
    }

    // List with optional pagination and search (only on employee_accounts fields)
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = max(1, min(100, (int)($_GET['limit'] ?? 25)));
    $offset = ($page - 1) * $limit;

    $where = [];
    $params = [];
    $types = '';

    if (!empty($_GET['search'])) {
        $s = '%' . $_GET['search'] . '%';
        $where[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR employee_id LIKE ? OR employee_code LIKE ? OR job_title LIKE ? )";
        $params = array_merge($params, [$s, $s, $s, $s, $s, $s]);
        $types .= 'ssssss';
    }

    $whereSql = '';
    if (!empty($where)) $whereSql = 'WHERE ' . implode(' AND ', $where);

    // Count total
    $countSql = "SELECT COUNT(*) AS total FROM employee_accounts $whereSql";
    if (!empty($params)) {
        $countStmt = safe_prepare($conn, $countSql);
        if ($countStmt) {
            $countStmt->bind_param($types, ...$params);
            $countStmt->execute();
            $countRes = $countStmt->get_result();
            $totalRow = $countRes ? $countRes->fetch_assoc() : null;
            $total = $totalRow['total'] ?? 0;
            if ($countRes && method_exists($countRes, 'free')) $countRes->free();
            $countStmt->close();
        } else {
            $total = 0;
        }
    } else {
        $r = $conn->query($countSql);
        $row = $r ? $r->fetch_assoc() : null;
        $total = $row['total'] ?? 0;
        if ($r && method_exists($r, 'free')) $r->free();
    }

    $sql = "SELECT * FROM employee_accounts $whereSql ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = safe_prepare($conn, $sql);
    $results = [];
    if ($stmt) {
        // bind params then pagination ints
        if (!empty($params)) {
            // build types with ii
            $bindTypes = $types . 'ii';
            $stmt->bind_param($bindTypes, ...array_merge($params, [$limit, $offset]));
        } else {
            $stmt->bind_param('ii', $limit, $offset);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $results[] = $r;
            }
            if (method_exists($res, 'free')) $res->free();
        }
        $stmt->close();
    } else {
        // fallback without prepared statement
        $q = $conn->query($sql);
        if ($q) {
            while ($r = $q->fetch_assoc()) {
                $results[] = $r;
            }
            if (method_exists($q, 'free')) $q->free();
        }
    }

    if ($conn) $conn->close();

    if (empty($results)) {
        echo json_encode((object)[]);
        exit;
    }

    // Convert numeric array to object map keyed by `id`
    $map = [];
    foreach ($results as $item) {
        if (is_array($item) && array_key_exists('id', $item)) {
            $map[$item['id']] = $item;
        } elseif (is_object($item) && property_exists($item, 'id')) {
            $map[$item->id] = $item;
        } else {
            // fallback: push item without key
            $map[] = $item;
        }
    }

    echo json_encode($map, JSON_PRETTY_PRINT);
    exit;
}

// Method not allowed
http_response_code(405);
echo json_encode([]);
