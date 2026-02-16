<!-- New Enrollment Modal -->
<style>
    /* Scoped light-theme overrides for the New Enrollment modal inputs */
    #newEnrollmentModal .input,
    #newEnrollmentModal input[type="date"],
    #newEnrollmentModal .select,
    #newEnrollmentModal select,
    #newEnrollmentModal .textarea,
    #newEnrollmentModal .form-control .label-text {
        background-color: #ffffff !important;
        color: #000000 !important;
        border-color: #d1d5db !important;
        /* tailwind gray-300 */
    }

    #newEnrollmentModal .input,
    #newEnrollmentModal select,
    #newEnrollmentModal .select select {
        box-shadow: none !important;
        outline: none !important;
    }

    #newEnrollmentModal .label-text {
        color: #374151 !important;
        /* gray-700 */
    }

    #newEnrollmentModal .benefit-checkbox+div,
    #newEnrollmentModal .label {
        color: #111827 !important;
        /* gray-900 for labels */
    }

    #newEnrollmentModal .checkbox {
        accent-color: #2563eb;
        /* blue-600 for checkbox accent */
    }

    #newEnrollmentModal .modal-box {
        background-color: #ffffff !important;
        color: #000000 !important;
    }
</style>

<dialog id="newEnrollmentModal" class="modal-bottom modal sm:modal-middle">
    <div class="bg-white shadow-lg border border-gray-200 max-w-4xl text-black modal-box">
        <form method="dialog" class="bg-white text-black">
            <button class="top-2 right-2 absolute btn btn-sm btn-circle btn-ghost">✕</button>
        </form>
        <h3 class="mb-6 font-bold text-blue-700 text-lg">New Employee Enrollment</h3>
        <form action="API/create_enrollment.php" method="POST">
            <div class="gap-6 grid grid-cols-1 md:grid-cols-2">
                <!-- Employee Information -->
                <div class="space-y-4">
                    <h4 class="pb-2 border-b font-semibold text-gray-700">Employee Information</h4>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Select Employee</span>
                        </label>
                        <select id="employeeSelect" class="w-full select-bordered select" name="employee_id" onchange="updateEmployeeInfo()">
                            <option disabled selected>Choose employee</option>
                            <?php foreach ($employees as $employee) : ?>
                                <?php $dept = htmlspecialchars($employee->department_name ?? $employee->department ?? ''); ?>
                                <option value="<?= $employee->id ?>" data-name="<?= htmlspecialchars($employee->first_name . ' ' . $employee->last_name) ?>" data-department="<?= $dept ?>" data-code="<?= htmlspecialchars($employee->employee_code) ?>"><?= htmlspecialchars($employee->first_name . ' ' . $employee->last_name) ?> {<?= htmlspecialchars($employee->employee_code) ?>}</option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Employee ID</span>
                        </label>
                        <input id="employeeId" type="text" class="input input-bordered" value="" readonly>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Department</span>
                        </label>
                        <input id="employeeDept" type="text" class="input input-bordered" value="" readonly>
                    </div>
                </div>

                <!-- Enrollment Details -->
                <div class="space-y-4">
                    <h4 class="pb-2 border-b font-semibold text-gray-700">Enrollment Details</h4>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Coverage Type</span>
                        </label>
                        <select id="coverageType" class="w-full select-bordered select" name="coverage_type" onchange="calculateCosts()">
                            <option disabled selected>Select coverage type</option>
                            <option value="employee_only">Employee Only</option>
                            <option value="employee_family">Employee + Family</option>
                            <option value="not_applicable">Not Applicable</option>
                        </select>
                    </div>

                    <div class="gap-4 grid grid-cols-2">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Start Date</span>
                            </label>
                            <input id="startDate" name="start_date" type="date" min="<?= date('Y-m-d') ?>" class="input input-bordered" value="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">End Date</span>
                            </label>
                            <input id="endDate" name="end_date" type="date" min="<?= date('Y-m-d') ?>" class="input input-bordered">
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Payroll Frequency</span>
                        </label>
                        <select id="payrollFrequency" class="w-full select-bordered select" name="payroll_frequency">
                            <option disabled selected>Select frequency</option>
                            <option value="monthly">Monthly</option>
                            <option value="weekly">Weekly</option>
                            <option value="one_time">One Time</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Benefits Selection -->
            <div class="mt-8">
                <h4 class="mb-4 pb-2 border-b font-semibold text-gray-700">Select Benefits</h4>

                <div class="space-y-3 p-2 max-h-60 overflow-y-auto">
                    <?php foreach (array_slice($benefits, 0, 5) as $benefit): ?>
                        <div class="form-control">
                            <label class="justify-start gap-3 hover:bg-gray-50 p-2 rounded cursor-pointer label">
                                <input type="checkbox"
                                    class="benefit-checkbox checkbox checkbox-primary"
                                    data-employee-cost="<?= $benefit->employee_cost_value ?? 0 ?>"
                                    data-company-cost="<?= $benefit->company_cost_value ?? 0 ?>"
                                    onchange="updateCostsFromBenefits()"
                                    value="<?= $benefit->id ?>"
                                    name="benefit_id[]">
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

            <!-- Status & Options -->
            <div class="gap-4 grid grid-cols-1 md:grid-cols-3 mt-6">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Enrollment Status</span>
                    </label>
                    <select id="status" class="w-full select-bordered select" name="status">
                        <option selected disabled>Select status</option>
                        <option value="pending">Pending</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="justify-start gap-2 cursor-pointer label">
                        <input id="payrollDeductible" type="checkbox" class="checkbox checkbox-primary" name="payroll_deductible" checked>
                        <span class="label-text">Payroll Deductible</span>
                    </label>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-ghost">Cancel</button>
                </form>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save" class="mr-2 w-4 h-4"></i>
                    Save Enrollment
                </button>
            </div>
        </form>
    </div>
</dialog>