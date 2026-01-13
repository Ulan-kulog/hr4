<?php
header('Content-Type: application/json');
require_once '../../connection.php'; // Adjust path as necessary

$response = [];

if (isset($connections['HR_4'])) {
    $conn = $connections['HR_4'];

    $sql = "SELECT * FROM departments ORDER BY name ASC";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        if (empty($rows)) {
            $response = ['status' => 'success', 'data' => []];
        } else {
            // Determine department id column in departments table
            $sample = $rows[0];
            $deptIdCandidates = ['id', 'department_id', 'dept_id', 'deptid', 'departmentid'];
            $deptIdKey = null;
            foreach ($deptIdCandidates as $k) {
                if (array_key_exists($k, $sample)) {
                    $deptIdKey = $k;
                    break;
                }
            }

            // Attempt to find a sub-departments table (common variations)
            $subTableCandidates = ['sub_departments', 'subdepartments', 'sub_department', 'sub-departments', 'sub_depts', 'sub_dept'];
            $subRows = [];
            $foundSubTable = null;
            foreach ($subTableCandidates as $tbl) {
                $escaped = mysqli_real_escape_string($conn, $tbl);
                $q = @mysqli_query($conn, "SELECT * FROM `" . $escaped . "` LIMIT 1000");
                if ($q !== false) {
                    while ($sr = mysqli_fetch_assoc($q)) {
                        $subRows[] = $sr;
                    }
                    mysqli_free_result($q);
                    if (!empty($subRows)) {
                        $foundSubTable = $tbl;
                        break;
                    }
                }
            }

            if ($foundSubTable && $deptIdKey) {
                // Determine foreign key name in sub table that references departments
                $fkCandidates = ['department_id', 'departmentid', 'dept_id', 'deptid', 'parent_id', 'parent_department_id'];
                $fkKey = null;
                if (!empty($subRows)) {
                    $sSample = $subRows[0];
                    foreach ($fkCandidates as $fk) {
                        if (array_key_exists($fk, $sSample)) {
                            $fkKey = $fk;
                            break;
                        }
                    }
                }

                // Build department map
                $map = [];
                foreach ($rows as $r) {
                    $r['sub_departments'] = [];
                    $map[$r[$deptIdKey]] = $r;
                }

                // Attach sub-departments
                if ($fkKey) {
                    foreach ($subRows as $sr) {
                        $parentId = $sr[$fkKey];
                        if (isset($map[$parentId])) {
                            $map[$parentId]['sub_departments'][] = $sr;
                        }
                    }
                }

                // Re-index results preserving original order of departments
                $departmentsWithSubs = [];
                foreach ($rows as $r) {
                    $departmentsWithSubs[] = $map[$r[$deptIdKey]];
                }

                $response = ['status' => 'success', 'data' => $departmentsWithSubs];
            } else {
                // No dedicated sub-departments table found — fall back to hierarchical parent column in departments
                $idKeys = ['id', 'department_id', 'dept_id', 'deptid', 'departmentid'];
                $parentKeys = ['parent_id', 'parent', 'parent_department_id', 'parent_dept_id', 'parent_department'];

                $idKey = null;
                foreach ($idKeys as $k) {
                    if (array_key_exists($k, $sample)) {
                        $idKey = $k;
                        break;
                    }
                }

                $parentKey = null;
                foreach ($parentKeys as $k) {
                    if (array_key_exists($k, $sample)) {
                        $parentKey = $k;
                        break;
                    }
                }

                if (!$parentKey || !$idKey) {
                    $response = ['status' => 'success', 'data' => $rows];
                } else {
                    $map = [];
                    foreach ($rows as $r) {
                        $r['children'] = [];
                        $map[$r[$idKey]] = $r;
                    }

                    $tree = [];
                    foreach ($map as $id => $node) {
                        $parentVal = $node[$parentKey];
                        if ($parentVal === null || $parentVal === '' || $parentVal === 0 || $parentVal === '0') {
                            $tree[] = &$map[$id];
                        } else if (isset($map[$parentVal])) {
                            $map[$parentVal]['children'][] = &$map[$id];
                        } else {
                            $tree[] = &$map[$id];
                        }
                    }

                    $response = ['status' => 'success', 'data' => $tree];
                }
            }
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Failed to fetch departments: ' . mysqli_error($conn)];
    }
} else {
    $response = ['status' => 'error', 'message' => 'HR_4 database connection not established.'];
}

echo json_encode($response);

if (isset($conn) && $conn) {
    mysqli_close($conn); // Close the connection when done
}
