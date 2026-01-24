<?php
// compensation_history.php
require_once 'DB.php';

session_start();
$user_id = $_SESSION['user_id'] ?? 1;
$user_role = $_SESSION['user_role'] ?? 'employee';

// Get compensation history
$sql = "SELECT c.*, ch.*, 
               CONCAT(e.first_name, ' ', e.last_name) as employee_name,
               e.employee_code
        FROM compensation_history ch
        LEFT JOIN compensations c ON ch.compensation_id = c.id
        LEFT JOIN employees e ON ch.employee_id = e.id
        WHERE ch.employee_id = ?
        ORDER BY ch.created_at DESC";

$history = Database::fetchAll($sql, [$user_id]);
?>

<!-- Similar HTML structure as above, with a table showing history -->

<div class="overflow-x-auto">
    <table class="table table-zebra w-full">
        <thead>
            <tr>
                <th>Date</th>
                <th>Action</th>
                <th>Old Amount</th>
                <th>New Amount</th>
                <th>Status Change</th>
                <th>Changed By</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $record): ?>
                <tr class="hover">
                    <td><?= date('M d, Y H:i', strtotime($record->created_at)) ?></td>
                    <td>
                        <span class="badge badge-<?= getActionColor($record->action_type) ?>">
                            <?= ucfirst($record->action_type) ?>
                        </span>
                    </td>
                    <td>₱<?= number_format($record->old_amount ?? 0, 2) ?></td>
                    <td>₱<?= number_format($record->new_amount ?? 0, 2) ?></td>
                    <td>
                        <?php if ($record->old_status && $record->new_status): ?>
                            <div class="flex items-center">
                                <span class="badge-outline badge"><?= $record->old_status ?></span>
                                <i class="fa-arrow-right mx-2 text-gray-400 fas"></i>
                                <span class="badge badge-<?= getStatusColor($record->new_status) ?>">
                                    <?= $record->new_status ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($record->changed_by_name ?? 'System') ?></td>
                    <td><?= htmlspecialchars($record->notes ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
function getActionColor($action)
{
    $colors = [
        'created' => 'success',
        'updated' => 'info',
        'approved' => 'success',
        'rejected' => 'error',
        'cancelled' => 'warning'
    ];
    return $colors[$action] ?? 'neutral';
}

function getStatusColor($status)
{
    $colors = [
        'pending' => 'warning',
        'approved' => 'success',
        'rejected' => 'error',
        'active' => 'success',
        'inactive' => 'neutral',
        'under_review' => 'info'
    ];
    return $colors[$status] ?? 'neutral';
}
?>