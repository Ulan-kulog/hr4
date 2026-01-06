<?php
session_start();
require_once '../connection.php';

class CompensationPolicy
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Create new policy
    public function createPolicy($data)
    {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO compensation_policies 
                (policy_name, policy_type, version, effective_date, description, status, compliance_rate, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "ssssssdi",
                $data['policy_name'],
                $data['policy_type'],
                $data['version'],
                $data['effective_date'],
                $data['description'],
                $data['status'],
                $data['compliance_rate'],
                $_SESSION['user_id']
            );

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Create Policy Error: " . $e->getMessage());
            return false;
        }
    }

    // Get all policies
    public function getAllPolicies()
    {
        $query = "
            SELECT cp.*, 
                   u.employee_name as created_by_name,
                   COUNT(DISTINCT pd.id) as document_count,
                   COUNT(DISTINCT pa.id) as approval_count
            FROM compensation_policies cp
            LEFT JOIN department_accounts u ON cp.created_by = u.id
            LEFT JOIN policy_documents pd ON cp.id = pd.policy_id
            LEFT JOIN policy_approvals pa ON cp.id = pa.policy_id
            GROUP BY cp.id
            ORDER BY cp.created_at DESC
        ";

        $result = $this->conn->query($query);

        // Check if query failed
        if (!$result) {
            error_log("SQL Error in getAllPolicies: " . $this->conn->error);
            error_log("Query: " . $query);
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get single policy by ID
    public function getPolicyById($id)
    {
        $stmt = $this->conn->prepare("
            SELECT cp.*, u.username as created_by_name
            FROM compensation_policies cp
            LEFT JOIN users u ON cp.created_by = u.id
            WHERE cp.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Update policy
    public function updatePolicy($id, $data)
    {
        try {
            $stmt = $this->conn->prepare("
                UPDATE compensation_policies 
                SET policy_name = ?, policy_type = ?, version = ?, 
                    effective_date = ?, description = ?, status = ?, 
                    compliance_rate = ?
                WHERE id = ?
            ");

            $stmt->bind_param(
                "ssssssdi",
                $data['policy_name'],
                $data['policy_type'],
                $data['version'],
                $data['effective_date'],
                $data['description'],
                $data['status'],
                $data['compliance_rate'],
                $id
            );

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Update Policy Error: " . $e->getMessage());
            return false;
        }
    }

    // Delete policy
    public function deletePolicy($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM compensation_policies WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Upload document for policy
    public function uploadDocument($policy_id, $file_data)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO policy_documents 
            (policy_id, file_name, file_path, file_type, file_size, uploaded_by) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isssii",
            $policy_id,
            $file_data['name'],
            $file_data['path'],
            $file_data['type'],
            $file_data['size'],
            $_SESSION['user_id']
        );

        return $stmt->execute();
    }

    // Get policy documents
    public function getPolicyDocuments($policy_id)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM policy_documents 
            WHERE policy_id = ? 
            ORDER BY uploaded_at DESC
        ");
        $stmt->bind_param("i", $policy_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Update policy status
    public function updateStatus($policy_id, $status)
    {
        $stmt = $this->conn->prepare("
            UPDATE compensation_policies 
            SET status = ? 
            WHERE id = ?
        ");
        $stmt->bind_param("si", $status, $policy_id);
        return $stmt->execute();
    }

    // Get policy statistics
    public function getPolicyStats()
    {
        $query = "
            SELECT 
                COUNT(*) as total_policies,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_policies,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft_policies,
                SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) as review_policies,
                AVG(compliance_rate) as avg_compliance
            FROM compensation_policies
        ";

        $result = $this->conn->query($query);

        // Check if query failed
        if (!$result) {
            error_log("SQL Error in getPolicyStats: " . $this->conn->error);
            error_log("Query: " . $query);
            return [
                'total_policies' => 0,
                'active_policies' => 0,
                'draft_policies' => 0,
                'review_policies' => 0,
                'avg_compliance' => 0
            ];
        }

        return $result->fetch_assoc();
    }
}

// Initialize class
$policyManager = new CompensationPolicy($conn);

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $response = ['success' => false, 'message' => ''];

    switch ($action) {
        case 'create':
            $data = [
                'policy_name' => $_POST['policy_name'],
                'policy_type' => $_POST['policy_type'],
                'version' => $_POST['version'],
                'effective_date' => $_POST['effective_date'],
                'description' => $_POST['description'],
                'status' => $_POST['status'] ?? 'draft',
                'compliance_rate' => $_POST['compliance_rate'] ?? 0
            ];

            if ($policyManager->createPolicy($data)) {
                $response = ['success' => true, 'message' => 'Policy created successfully!'];
            } else {
                $response['message'] = 'Failed to create policy.';
            }
            break;

        case 'update':
            $id = $_POST['id'];
            $data = [
                'policy_name' => $_POST['policy_name'],
                'policy_type' => $_POST['policy_type'],
                'version' => $_POST['version'],
                'effective_date' => $_POST['effective_date'],
                'description' => $_POST['description'],
                'status' => $_POST['status'],
                'compliance_rate' => $_POST['compliance_rate']
            ];

            if ($policyManager->updatePolicy($id, $data)) {
                $response = ['success' => true, 'message' => 'Policy updated successfully!'];
            } else {
                $response['message'] = 'Failed to update policy.';
            }
            break;

        case 'delete':
            $id = $_POST['id'];
            if ($policyManager->deletePolicy($id)) {
                $response = ['success' => true, 'message' => 'Policy deleted successfully!'];
            } else {
                $response['message'] = 'Failed to delete policy.';
            }
            break;

        case 'upload_document':
            $policy_id = $_POST['policy_id'];
            $upload_dir = '../uploads/policy_documents/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (isset($_FILES['document'])) {
                $file = $_FILES['document'];
                $file_name = time() . '_' . basename($file['name']);
                $file_path = $upload_dir . $file_name;

                if (move_uploaded_file($file['tmp_name'], $file_path)) {
                    $file_data = [
                        'name' => $file['name'],
                        'path' => $file_path,
                        'type' => $file['type'],
                        'size' => $file['size']
                    ];

                    if ($policyManager->uploadDocument($policy_id, $file_data)) {
                        $response = ['success' => true, 'message' => 'Document uploaded successfully!'];
                    }
                }
            }
            break;

        case 'update_status':
            $policy_id = $_POST['policy_id'];
            $status = $_POST['status'];

            if ($policyManager->updateStatus($policy_id, $status)) {
                $response = ['success' => true, 'message' => 'Status updated successfully!'];
            }
            break;
        case 'get_policy':
            $id = $_GET['id'];
            $policy = $policyManager->getPolicyById($id);
            if ($policy) {
                echo json_encode($policy);
            } else {
                echo json_encode(['error' => 'Policy not found']);
            }
            exit;

        case 'get_policies':
            $policies = $policyManager->getAllPolicies();
            echo json_encode($policies);
            exit;
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Get data for display
$policies = $policyManager->getAllPolicies();
$stats = $policyManager->getPolicyStats();
