<!-- Edit Enrollment Modal -->
<dialog id="editEnrollmentModal" class="modal">
    <div class="bg-white/90 max-w-4xl text-black modal-box">
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
            <input type="hidden" id="edit_employee_id" name="employee_id">
            <input type="hidden" id="edit_benefit_enrollment_id" name="benefit_enrollment_id">

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
                        <input type="text" id="edit_employee_code" class="bg-gray-100 text-black input input-bordered" readonly>
                    </div>

                    <!-- Employee Name -->
                    <div class="form-control">
                        <label class="label">
                            <span class="font-semibold label-text">Full Name</span>
                        </label>
                        <input type="text" id="edit_full_name" class="bg-gray-100 text-black input input-bordered" readonly>
                    </div>

                    <!-- Department -->
                    <div class="form-control">
                        <label class="label">
                            <span class="font-semibold label-text">Department</span>
                        </label>
                        <input type="text" id="edit_department" class="bg-gray-100 text-black input input-bordered" readonly>
                    </div>

                    <!-- Email -->
                    <div class="form-control">
                        <label class="label">
                            <span class="font-semibold label-text">Email</span>
                        </label>
                        <input type="email" id="edit_email" class="bg-gray-100 text-black input input-bordered" readonly>
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
                        <input type="text" id="edit_benefit_enrollment_id_display" class="bg-gray-100 text-black input input-bordered" readonly>
                    </div>

                    <!-- Status -->
                    <div class="form-control">
                        <label class="label">
                            <span class="font-semibold label-text">Status</span>
                        </label>
                        <select id="edit_status" name="status" class="select-bordered select">
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>

                    <!-- Start Date -->
                    <div class="form-control">
                        <label class="label">
                            <span class="font-semibold label-text">Start Date</span>
                        </label>
                        <input type="date" id="edit_start_date" name="start_date" class="input input-bordered" required>
                    </div>

                    <!-- End Date -->
                    <div class="form-control">
                        <label class="label">
                            <span class="font-semibold label-text">End Date</span>
                        </label>
                        <input type="date" id="edit_end_date" name="end_date" class="input input-bordered">
                    </div>

                    <!-- Payroll Frequency -->
                    <div class="form-control">
                        <label class="label">
                            <span class="font-semibold label-text">Payroll Frequency</span>
                        </label>
                        <select id="edit_payroll_frequency" name="payroll_frequency" class="select-bordered select">
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
                            <input type="checkbox" id="edit_payroll_deductible" name="payroll_deductible" value="1" class="checkbox checkbox-primary">
                            <span class="ml-3 font-medium">Yes, deduct from payroll</span>
                        </div>
                    </div>

                    <!-- Last Updated -->
                    <div class="col-span-2 form-control">
                        <label class="label">
                            <span class="font-semibold label-text">Last Updated</span>
                        </label>
                        <input type="text" id="edit_updated_at" class="bg-gray-100 text-black input input-bordered" readonly>
                    </div>
                </div>
            </div>

            <!-- Enrolled Benefits -->
            <div class="bg-[#000a19]/5 p-6 border border-[#000a19]/10 rounded-xl">
                <h4 class="flex items-center gap-2 mb-4 font-bold text-lg">
                    <i data-lucide="package" class="w-5 h-5"></i>
                    Enrolled Benefits
                    <span id="edit_benefits_count" class="ml-2 badge badge-primary">0</span>
                </h4>

                <div id="edit_benefits_list" class="space-y-3 pr-2 max-h-60 overflow-y-auto">
                    <!-- Benefits will be populated here -->
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