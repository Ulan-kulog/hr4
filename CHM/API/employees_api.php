<?php
// Minimal API exposing employees table only
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

function safe_prepare($conn, $sql)
{
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;
    return $stmt;
}

// GET single or list
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Single by id
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT * FROM employees WHERE id = ? LIMIT 1";
        $stmt = safe_prepare($conn, $sql);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode([]);
            if ($conn) $conn->close();
            exit;
        }
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        if ($res && method_exists($res, 'free')) $res->free();
        if ($stmt) $stmt->close();
        if ($conn) $conn->close();
        echo json_encode($row ?: (object)[]);
        exit;
    }

    // List: optional search on first_name/last_name/email/employee_code, optional limit/offset
    $limit = max(1, min(100, (int)($_GET['limit'] ?? 100)));
    $offset = max(0, (int)($_GET['offset'] ?? 0));

    $where = [];
    $params = [];
    $types = '';

    if (!empty($_GET['search'])) {
        $s = '%' . $_GET['search'] . '%';
        $where[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR employee_code LIKE ?)";
        $params = [$s, $s, $s, $s];
        $types = 'ssss';
    }

    $whereSql = '';
    if (!empty($where)) $whereSql = 'WHERE ' . implode(' AND ', $where);

    $sql = "SELECT * FROM employees $whereSql ORDER BY id ASC LIMIT ? OFFSET ?";
    $stmt = safe_prepare($conn, $sql);
    $rows = [];
    if ($stmt) {
        if (!empty($params)) {
            // bind dynamic params + ii
            $bindTypes = $types . 'ii';
            $bindValues = array_merge($params, [$limit, $offset]);
            // bind_param requires references
            $refs = [];
            foreach ($bindValues as $k => $v) $refs[$k] = &$bindValues[$k];
            array_unshift($refs, $bindTypes);
            call_user_func_array([$stmt, 'bind_param'], $refs);
        } else {
            $stmt->bind_param('ii', $limit, $offset);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            while ($r = $res->fetch_assoc()) $rows[] = $r;
            if (method_exists($res, 'free')) $res->free();
        }
        $stmt->close();
    } else {
        // fallback
        $q = $conn->query($sql);
        if ($q) {
            while ($r = $q->fetch_assoc()) $rows[] = $r;
            if (method_exists($q, 'free')) $q->free();
        }
    }

    if ($conn) $conn->close();
    if (empty($rows)) echo json_encode((object)[]);
    else echo json_encode($rows, JSON_PRETTY_PRINT);
    exit;
}

http_response_code(405);
echo json_encode([]);
