<?php
require_once 'DB.php';

// Helper: mask currency amounts for display (show only last 3 integer digits)
function mask_amount($amount, $visible_digits = 3)
{
    if (!is_numeric($amount)) return $amount;
    $dec = number_format((float)$amount, 2, '.', ',');
    $parts = explode('.', $dec);
    $int = $parts[0];
    $frac = $parts[1] ?? '00';
    // remove non-digits
    $digits = preg_replace('/\D/', '', $int);
    $len = strlen($digits);
    // Always mask values — even small amounts. If the integer length is
    // less than or equal to visible_digits, show a fixed number of stars
    // instead of the real digits to avoid revealing small amounts.
    if ($len <= $visible_digits) {
        $masked_prefix = str_repeat('*', $visible_digits);
    } else {
        $visible = substr($digits, -$visible_digits);
        $masked_prefix = str_repeat('*', $len - $visible_digits) . $visible;
    }
    // add commas to masked prefix from right
    $rev = strrev($masked_prefix);
    $chunks = str_split($rev, 3);
    $with_commas = implode(',', $chunks);
    $int_masked = strrev($with_commas);
    return '₱' . $int_masked . '.' . $frac;
}

// 1. Fetch employees for the dropdown
$employeeList = Database::fetchAll("SELECT id, first_name, last_name FROM employees ORDER BY last_name ASC");

// --- PHP HANDLERS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_request'])) {
        $id = filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);
        $emp_id = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $req_type = $_POST['request_type'] ?? '';
        $curr_amt = filter_input(INPUT_POST, 'current_amount', FILTER_VALIDATE_FLOAT);
        $req_amt = filter_input(INPUT_POST, 'requested_amount', FILTER_VALIDATE_FLOAT);
        $eff_date = $_POST['effective_date'] ?? '';
        $just = trim($_POST['justification'] ?? '');

        if ($id) {
            $sql = "UPDATE compensation_requests SET 
                    requested_by = ?, request_type = ?, current_amount = ?, 
                    requested_amount = ?, effective_date = ?, justification = ? 
                    WHERE id = ?";
            Database::query($sql, [$emp_id, $req_type, $curr_amt, $req_amt, $eff_date, $just, $id]);
            $status = "updated";
        } else {
            $data = [
                'requested_by'     => $emp_id,
                'request_type'     => $req_type,
                'current_amount'   => $curr_amt,
                'requested_amount' => $req_amt,
                'effective_date'   => $eff_date,
                'justification'    => $just,
                'status'           => 'pending',
                'created_at'       => date('Y-m-d H:i:s')
            ];
            Database::insertInto('compensation_requests', $data);
            $status = "success";
        }
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=$status");
        exit;
    }

    if (isset($_POST['delete_id'])) {
        $id = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);
        Database::query("DELETE FROM compensation_requests WHERE id = ?", [$id]);
        echo json_encode(['success' => true]);
        exit;
    }
}

// 2. Fetch all requests
$requests = Database::fetchAll("SELECT c.*, e.first_name, e.last_name 
                                  FROM compensation_requests c 
                                  LEFT JOIN employees e ON c.requested_by = e.id 
                                  ORDER BY c.created_at DESC");

// 3. CALCULATE STATISTICS
$totalValue = 0;
$totalRequests = count($requests);

foreach ($requests as $r) {
    $totalValue += $r->requested_amount;
}
$avgValue = $totalRequests > 0 ? $totalValue / $totalRequests : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Compensation Management</title>
    <?php include '../INCLUDES/header.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .modal-active {
            overflow: hidden;
        }

        #compModal {
            transition: all 0.2s ease-out;
        }

        .colored-toast.swal2-icon-success {
            background-color: #2563eb !important;
        }

        .colored-toast .swal2-title {
            color: white;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="flex h-screen">
        <?php include '../INCLUDES/sidebar.php'; ?>
        <div class="flex flex-col flex-1 overflow-auto">
            <?php include '../INCLUDES/navbar.php'; ?>

            <main class="flex-1 p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="font-bold text-gray-800 text-2xl">Compensation Overview</h1>
                        <p class="text-gray-500 text-sm">Manage salary adjustments in PHP.</p>
                    </div>
                    <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 shadow-md px-6 py-2.5 rounded-xl font-bold text-white active:scale-95 transition-all">
                        + New Request
                    </button>
                </div>

                <div class="gap-6 grid grid-cols-1 md:grid-cols-3 mb-8">
                    <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-2xl">
                        <p class="font-bold text-gray-400 text-xs uppercase tracking-wider">Total Volume</p>
                        <h2 class="mt-1 font-black text-gray-800 text-3xl">₱<?= number_format($totalValue, 2) ?></h2>
                        <p class="mt-2 font-medium text-blue-500 text-xs">Cumulative requested amount</p>
                    </div>
                    <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-2xl">
                        <p class="font-bold text-gray-400 text-xs uppercase tracking-wider">Active Requests</p>
                        <h2 class="mt-1 font-black text-gray-800 text-3xl"><?= $totalRequests ?></h2>
                        <p class="mt-2 font-medium text-gray-500 text-xs">Total submissions found</p>
                    </div>
                    <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-2xl">
                        <p class="font-bold text-gray-400 text-xs uppercase tracking-wider">Avg. Adjustment</p>
                        <h2 class="mt-1 font-black text-gray-800 text-3xl">₱<?= number_format($avgValue, 2) ?></h2>
                        <p class="mt-2 font-medium text-green-500 text-xs">Mean request value</p>
                    </div>
                </div>

                <div class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50 border-b font-bold text-[10px] text-gray-400 uppercase tracking-widest">
                            <tr>
                                <th class="p-5">Employee Details</th>
                                <th class="p-5">Adjustment Type</th>
                                <th class="p-5">Amount</th>
                                <th class="p-5 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y text-sm">
                            <?php foreach ($requests as $row): ?>
                                <tr class="hover:bg-blue-50/30 transition-colors" id="row-<?= $row->id ?>">
                                    <td class="p-5">
                                        <div class="font-bold text-gray-900"><?= htmlspecialchars($row->first_name . ' ' . $row->last_name) ?></div>
                                        <div class="text-gray-400 text-xs">ID: #EMP-<?= $row->requested_by ?></div>
                                    </td>
                                    <td class="p-5">
                                        <span class="bg-gray-100 px-3 py-1 rounded-full font-bold text-gray-600 text-xs uppercase tracking-tighter">
                                            <?= str_replace('_', ' ', $row->request_type) ?>
                                        </span>
                                    </td>
                                    <td class="p-5 font-black text-blue-600">
                                        <?php $rawFormatted = '₱' . number_format($row->requested_amount, 2); ?>
                                        <span id="amount-<?= $row->id ?>" data-raw-formatted="<?= htmlspecialchars($rawFormatted) ?>" data-masked="<?= htmlspecialchars(mask_amount($row->requested_amount)) ?>" data-shown="0"><?= htmlspecialchars(mask_amount($row->requested_amount)) ?></span>
                                        <button id="amount-btn-<?= $row->id ?>" onclick="toggleAmount(<?= $row->id ?>)" class="bg-gray-100 ml-3 px-2 py-1 rounded text-xs">Show</button>
                                    </td>
                                    <td class="flex justify-center gap-4 p-5">
                                        <?php
                                        $row_for_view = (array) $row;
                                        $row_for_view['masked_requested_amount'] = mask_amount($row->requested_amount);
                                        $row_for_view['masked_current_amount'] = mask_amount($row->current_amount);
                                        ?>
                                        <button onclick='viewRequest(<?= json_encode($row_for_view) ?>)' class="text-gray-400 hover:text-gray-600 transition">View</button>
                                        <button onclick='editRequest(<?= json_encode($row) ?>)' class="font-bold text-blue-600 hover:text-blue-800">Edit</button>
                                        <button onclick="confirmDelete(<?= $row->id ?>)" class="text-red-400 hover:text-red-600 transition">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <div id="compModal" class="hidden z-50 fixed inset-0 overflow-y-auto">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal()"></div>
        <div class="flex justify-center items-center p-4 min-h-screen">
            <div class="relative bg-white opacity-0 shadow-2xl p-8 rounded-2xl w-full max-w-2xl scale-95 transition-all transform" id="modalContainer">
                <form action="" method="POST" id="requestForm" class="space-y-4">
                    <input type="hidden" name="request_id" id="request_id">
                    <h3 id="modalTitle" class="mb-2 font-extrabold text-gray-800 text-2xl">New Request</h3>
                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                        <div class="space-y-1">
                            <label class="font-bold text-gray-400 text-xs uppercase">Employee</label>
                            <select name="employee_id" id="form_emp" required class="bg-gray-50 focus:bg-white p-2.5 border rounded-lg outline-none w-full text-sm transition">
                                <option value="">Select...</option>
                                <?php foreach ($employeeList as $emp): ?>
                                    <option value="<?= $emp->id ?>"><?= htmlspecialchars($emp->last_name . ', ' . $emp->first_name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-gray-400 text-xs uppercase">Type</label>
                            <select name="request_type" id="form_type" required class="bg-white p-2.5 border rounded-lg outline-none w-full text-sm">
                                <option value="salary_adjustment">Salary Adjustment</option>
                                <option value="allowance">Allowance</option>
                                <option value="bonus">Bonus</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-gray-400 text-xs uppercase">Current Amount (₱)</label>
                            <input type="number" step="0.01" name="current_amount" id="form_curr" placeholder="0.00" class="bg-gray-50 p-2.5 border rounded-lg outline-none w-full text-sm">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-gray-400 text-xs uppercase">Requested Amount (₱)</label>
                            <input type="number" step="0.01" name="requested_amount" id="form_req" required placeholder="0.00" class="bg-blue-50/30 p-2.5 border border-blue-200 rounded-lg outline-none w-full font-bold text-blue-700 text-sm">
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="font-bold text-gray-400 text-xs uppercase">Effective Date</label>
                        <input type="date" name="effective_date" id="form_date" required class="bg-white p-2.5 border rounded-lg outline-none w-full text-sm">
                    </div>
                    <div class="space-y-1">
                        <label class="font-bold text-gray-400 text-xs uppercase">Justification</label>
                        <textarea name="justification" id="form_just" rows="3" required class="bg-white p-2.5 border rounded-lg outline-none focus:ring-2 focus:ring-blue-100 w-full text-sm"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" onclick="closeModal()" class="px-4 font-medium text-gray-500 hover:text-gray-700 transition">Cancel</button>
                        <button type="submit" name="submit_request" id="submitBtn" class="bg-blue-600 hover:bg-blue-700 shadow-lg px-8 py-2.5 rounded-xl font-bold text-white active:scale-95 transition-all">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'colored-toast'
            }
        });

        function openModal() {
            document.getElementById('requestForm').reset();
            document.getElementById('request_id').value = '';
            document.getElementById('modalTitle').innerText = 'New Compensation Request';
            document.getElementById('compModal').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('modalContainer').classList.remove('scale-95', 'opacity-0');
                document.getElementById('modalContainer').classList.add('scale-100', 'opacity-100');
            }, 50);
        }

        function closeModal() {
            document.getElementById('modalContainer').classList.remove('scale-100', 'opacity-100');
            setTimeout(() => document.getElementById('compModal').classList.add('hidden'), 200);
        }

        // Updated for Philippine Currency formatting in JS
        function viewRequest(data) {
            const pesoFormat = new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP',
            });

            Swal.fire({
                title: 'Request Details',
                html: `<div class="space-y-3 text-sm text-left">
                    <p><strong>Employee:</strong> ${data.first_name} ${data.last_name}</p>
                    <p><strong>Type:</strong> ${data.request_type.replace('_', ' ')}</p>
                    <p><strong>Amount:</strong> <span id="swal-amount" data-masked="${data.masked_requested_amount ? data.masked_requested_amount : ''}">${data.masked_requested_amount ? data.masked_requested_amount : ''}</span>
                        <button id="swal-show-btn" onclick="showRealInSwal(${data.requested_amount})" class="bg-gray-100 ml-2 px-2 py-1 rounded text-xs">Show</button>
                    </p>
                    <hr><p class="text-gray-500 italic">"${data.justification}"</p>
                </div>`,
                confirmButtonColor: '#2563eb'
            });
        }

        // Toggle real/masked amount inside SweetAlert modal
        function showRealInSwal(amount) {
            const el = document.getElementById('swal-amount');
            const btn = document.getElementById('swal-show-btn');
            if (!el || !btn) return;
            if (el.dataset.realShown === '1') {
                el.textContent = el.dataset.masked || el.textContent;
                el.dataset.realShown = '0';
                btn.textContent = 'Show';
            } else {
                const formatted = new Intl.NumberFormat('en-PH', {
                    style: 'currency',
                    currency: 'PHP'
                }).format(amount);
                el.textContent = formatted;
                el.dataset.realShown = '1';
                btn.textContent = 'Hide';
            }
        }

        // Toggle amount in table row between masked and real
        function toggleAmount(id) {
            const el = document.getElementById('amount-' + id);
            const btn = document.getElementById('amount-btn-' + id);
            if (!el || !btn) return;
            if (el.dataset.shown === '1') {
                el.textContent = el.dataset.masked;
                el.dataset.shown = '0';
                btn.textContent = 'Show';
            } else {
                el.textContent = el.dataset.rawFormatted;
                el.dataset.shown = '1';
                btn.textContent = 'Hide';
            }
        }

        function editRequest(data) {
            openModal();
            document.getElementById('modalTitle').innerText = 'Edit Request';
            document.getElementById('request_id').value = data.id;
            document.getElementById('form_emp').value = data.requested_by;
            document.getElementById('form_type').value = data.request_type;
            document.getElementById('form_curr').value = data.current_amount;
            document.getElementById('form_req').value = data.requested_amount;
            document.getElementById('form_date').value = data.effective_date;
            document.getElementById('form_just').value = data.justification;
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Delete request?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('delete_id', id);
                    fetch(window.location.href, {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                document.getElementById(`row-${id}`).remove();
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Deleted successfully'
                                });
                            }
                        });
                }
            });
        }

        window.onload = () => {
            const status = new URLSearchParams(window.location.search).get('status');
            if (status) Toast.fire({
                icon: 'success',
                title: 'Action Successful'
            });
        };
    </script>
</body>

</html>