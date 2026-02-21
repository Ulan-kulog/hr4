<!-- Edit Enrollment Modal -->
<style>
    /* Scoped light-theme overrides for the Edit Enrollment modal */
    #editEnrollmentModal .modal-box {
        background-color: #ffffff !important;
        color: #000000 !important;
        border: 1px solid #e5e7eb !important;
        /* gray-200 */
        box-shadow: 0 10px 25px rgba(16, 24, 40, 0.08) !important;
    }

    #editEnrollmentModal .input,
    #editEnrollmentModal input[type="date"],
    #editEnrollmentModal .select,
    #editEnrollmentModal select,
    #editEnrollmentModal .textarea,
    #editEnrollmentModal .label-text {
        background-color: #ffffff !important;
        color: #000000 !important;
        border-color: #d1d5db !important;
        /* gray-300 */
    }

    #editEnrollmentModal .input,
    #editEnrollmentModal select {
        box-shadow: none !important;
        outline: none !important;
    }

    #editEnrollmentModal .label-text {
        color: #374151 !important;
        /* gray-700 */
    }

    #editEnrollmentModal .badge-primary {
        background-color: #2563eb !important;
        /* blue-600 */
        color: #fff !important;
    }
</style>

<dialog id="editEnrollmentModal" class="modal">
    <div class="bg-white shadow-lg border border-gray-200 max-w-4xl text-black modal-box">
        <!-- Modal Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-bold text-2xl">Edit Employee Enrollment</h3>
                <p class="mt-1 text-gray-500 text-sm">Update enrollment details and settings</p>
            </div>
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </form>
        </div>

        <form id="editEnrollmentForm" class="space-y-6" method="POST" action="API/update_enrollment.php">
            <!-- Hidden Fields -->
            <input type="hidden" id="edit_employee_id" name="employee_id" x-model="selected.employee_id">
            <input type="hidden" id="edit_benefit_enrollment_id" name="benefit_enrollment_id" x-model="selected.benefit_enrollment_id">

            <!-- Employee Information -->
            <div class="bg-[#000a19]/5 p-6 border border-[#000a19]/10 rounded-xl">
                <h4 class="flex items-center gap-2 mb-4 font-bold text-lg">
                    <i data-lucide="user" class="w-5 h-5"></i>
                    Employee Information
                </h4>

                <div class="gap-4 grid grid-cols-2">
                    <!-- Employee Code -->
                    <div class="form-control">
                        <label class="label">
                            <span class="font-semibold label-text">Employee Code</span>
                        </label>
                        <input type="text" id="edit_employee_code" class="bg-gray-100 text-black input input-bordered" readonly x-bind:value="selected ? selected.employee_code : ''">
                    </div>

                    <!-- Employee Name -->
                    <div class="form-control">
                        <label class="label">
                            <span class="font-semibold label-text">Full Name</span>
                        </label>
                        <input type="text" id="edit_full_name" class="bg-gray-100 text-black input input-bordered" readonly x-bind:value="selected ? ( (selected.first_name||'') + ' ' + (selected.last_name||'') ) : ''">
                    </div>

                    <!-- Department -->
                    <div class="form-control">
                        <label class="label">
                            <span class="font-semibold label-text">Department</span>
                        </label>
                        <input type="text" id="edit_department" class="bg-gray-100 text-black input input-bordered" readonly x-bind:value="selected ? (selected.department_name ? (selected.department_name + (selected.sub_department_name ? ' ('+selected.sub_department_name+')' : '') ) : '') : ''">
                    </div>

                    <!-- Email -->
                    <div class="form-control">
                        <label class="label">
                            <span class="font-semibold label-text">Email</span>
                        </label>
                        <input type="email" id="edit_email" class="bg-gray-100 text-black input input-bordered" readonly x-bind:value="selected ? selected.email : ''">
                    </div>
                </div>
            </div>

            <!-- Enrollment Details -->
            <div class="bg-[#000a19]/5 p-6 border border-[#000a19]/10 rounded-xl">
                <h4 class="flex items-center gap-2 mb-4 font-bold text-lg">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                    Enrollment Details
                </h4>

                <div class="gap-4 grid grid-cols-2">
                    <!-- Benefit Enrollment ID -->
                    <div class="form-control">
                        <label class="label">
                            <span class="font-semibold label-text">Enrollment ID</span>
                        </label>
                        <input type="text" id="edit_benefit_enrollment_id_display" class="bg-gray-100 text-black input input-bordered" readonly x-bind:value="selected && selected.benefit_enrollment_id ? ('ENR-'+ String(selected.benefit_enrollment_id).padStart(3,'0')) : 'N/A'">
                    </div>

                    <!-- Status -->
                    <div class="form-control">
                        <label class="label">
                            <span class="font-semibold label-text">Status</span>
                        </label>
                        <select id="edit_status" name="status" class="select-bordered select" x-model="selected.status">
                            <?php if (!empty($statuses)): ?>
                                <?php foreach ($statuses as $st): ?>
                                    <option value="<?php echo htmlspecialchars($st); ?>"><?php echo htmlspecialchars(ucfirst($st)); ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="expired">Expired</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Start Date -->
                    <div class="form-control">
                        <label class="label">
                            <span class="font-semibold label-text">Start Date</span>
                        </label>
                        <input type="date" id="edit_start_date" name="start_date" class="input input-bordered" required x-model="selected.start_date">
                    </div>

                    <!-- End Date -->
                    <div class="form-control">
                        <label class="label">
                            <span class="font-semibold label-text">End Date</span>
                        </label>
                        <input type="date" id="edit_end_date" name="end_date" class="input input-bordered" x-model="selected.end_date">
                    </div>

                    <!-- Payroll Frequency -->
                    <div class="form-control">
                        <label class="label">
                            <span class="font-semibold label-text">Payroll Frequency</span>
                        </label>
                        <select id="edit_payroll_frequency" name="payroll_frequency" class="select-bordered select" x-model="selected.payroll_frequency">
                            <option value="weekly">Weekly</option>
                            <option value="bi-weekly">Bi-Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>

                    <!-- Payroll Deductible -->
                    <div class="form-control">
                        <label class="label">
                            <span class="font-semibold label-text">Payroll Deductible</span>
                        </label>
                        <div class="flex items-center mt-2">
                            <input type="hidden" name="payroll_deductible" value="0">
                            <input type="checkbox" id="edit_payroll_deductible" name="payroll_deductible" value="1" class="checkbox checkbox-primary" x-model="selected.payroll_deductible">
                            <span class="ml-3 font-medium">Yes, deduct from payroll</span>
                        </div>
                    </div>

                    <!-- Last Updated -->
                    <div class="col-span-2 form-control">
                        <label class="label">
                            <span class="font-semibold label-text">Last Updated</span>
                        </label>
                        <input type="text" id="edit_updated_at" class="bg-gray-100 text-black input input-bordered" readonly x-bind:value="selected ? selected.updated_at : ''">
                    </div>
                </div>
            </div>

            <!-- Enrolled Benefits -->
            <div class="bg-[#000a19]/5 p-6 border border-[#000a19]/10 rounded-xl">
                <h4 class="flex items-center gap-2 mb-4 font-bold text-lg">
                    <i data-lucide="package" class="w-5 h-5"></i>
                    Enrolled Benefits
                </h4>

                <div id="edit_benefits_list" class="space-y-3 pr-2 max-h-60 overflow-y-auto">
                    <?php foreach ($benefits as $benefit): ?>
                        <?php if (isset($benefit->status) && $benefit->status === 'pending') continue; ?>
                        <div class="form-control">
                            <label class="justify-start gap-3 hover:bg-gray-50 p-2 rounded cursor-pointer label">
                                <input type="checkbox"
                                    class="benefit-checkbox checkbox checkbox-primary"
                                    data-employee-cost="<?= $benefit->employee_cost_value ?? 0 ?>"
                                    data-company-cost="<?= $benefit->company_cost_value ?? 0 ?>"
                                    value="<?= $benefit->id ?>"
                                    name="benefit_id[]"
                                    x-bind:checked="selected && selected.benefits && selected.benefits.some(b => b.benefit_id == <?= $benefit->id ?>)">
                                <div class="flex-1">
                                    <span class="font-medium"><?= htmlspecialchars($benefit->benefit_name) ?></span>
                                    <div class="flex gap-3 mt-1 text-gray-500 text-sm">
                                        <span><?= htmlspecialchars($benefit->provider_name ?? 'N/A') ?></span>
                                        <span>•</span>
                                        <span>Employee: $<?= number_format($benefit->employee_cost_value ?? 0, 2) ?>/mo</span>
                                        <span>•</span>
                                        <span>Company: $<?= number_format($benefit->company_cost_value ?? 0, 2) ?>/mo</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="modal-action">
                <button type="button" onclick="document.getElementById('editEnrollmentModal').close()" class="btn btn-ghost">
                    <i data-lucide="x" class="mr-2 w-4 h-4"></i>
                    Cancel
                </button>
                <button type="submit" class="bg-[#000a19] hover:bg-[#000a19]/90 border-[#000a19] btn btn-primary">
                    <i data-lucide="save" class="mr-2 w-4 h-4"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</dialog>