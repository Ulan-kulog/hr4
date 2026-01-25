<!-- View Employee Enrollment Modal -->
<dialog id="viewEmployeeModal" class="modal">
    <div class="bg-white/90 max-w-4xl text-black modal-box">
        <h3 class="mb-6 font-bold text-xl">Employee Enrollment Details</h3>

        <!-- Employee Information Section -->
        <div class="mb-6">
            <h4 class="mb-4 pb-2 border-gray-200 border-b font-semibold text-lg">Employee Information</h4>
            <div class="gap-4 grid grid-cols-2">
                <!-- Employee ID -->
                <div>
                    <label class="label">
                        <span class="font-semibold label-text">Employee ID</span>
                    </label>
                    <input type="text" id="view_employee_code" class="bg-gray-100 w-full text-black input input-bordered" readonly />
                </div>

                <!-- Full Name -->
                <div>
                    <label class="label">
                        <span class="font-semibold label-text">Full Name</span>
                    </label>
                    <input type="text" id="view_full_name" class="bg-gray-100 w-full text-black input input-bordered" readonly />
                </div>

                <!-- Department -->
                <div>
                    <label class="label">
                        <span class="font-semibold label-text">Department</span>
                    </label>
                    <input type="text" id="view_department" class="bg-gray-100 w-full text-black input input-bordered" readonly />
                </div>

                <!-- Email -->
                <div>
                    <label class="label">
                        <span class="font-semibold label-text">Email</span>
                    </label>
                    <input type="text" id="view_email" class="bg-gray-100 w-full text-black input input-bordered" readonly />
                </div>
            </div>
        </div>

        <!-- Enrollment Information Section -->
        <div class="mb-6">
            <h4 class="mb-4 pb-2 border-gray-200 border-b font-semibold text-lg">Enrollment Information</h4>
            <div class="gap-4 grid grid-cols-2">
                <!-- Benefit Enrollment ID -->
                <div>
                    <label class="label">
                        <span class="font-semibold label-text">Enrollment ID</span>
                    </label>
                    <input type="text" id="view_benefit_enrollment_id" class="bg-gray-100 w-full text-black input input-bordered" readonly />
                </div>

                <!-- Status -->
                <div>
                    <label class="label">
                        <span class="font-semibold label-text">Status</span>
                    </label>
                    <span id="view_status" class="inline-block px-3 py-2 rounded-lg font-medium text-sm"></span>
                </div>

                <!-- Start Date -->
                <div>
                    <label class="label">
                        <span class="font-semibold label-text">Start Date</span>
                    </label>
                    <input type="text" id="view_start_date" class="bg-gray-100 w-full text-black input input-bordered" readonly />
                </div>

                <!-- End Date -->
                <div>
                    <label class="label">
                        <span class="font-semibold label-text">End Date</span>
                    </label>
                    <input type="text" id="view_end_date" class="bg-gray-100 w-full text-black input input-bordered" readonly />
                </div>

                <!-- Payroll Frequency -->
                <div>
                    <label class="label">
                        <span class="font-semibold label-text">Payroll Frequency</span>
                    </label>
                    <input type="text" id="view_payroll_frequency" class="bg-gray-100 w-full text-black input input-bordered" readonly />
                </div>

                <!-- Payroll Deductible -->
                <div>
                    <label class="label">
                        <span class="font-semibold label-text">Payroll Deductible</span>
                    </label>
                    <input type="text" id="view_payroll_deductible" class="bg-gray-100 w-full text-black input input-bordered" readonly />
                </div>

                <!-- Last Updated -->
                <div class="col-span-2">
                    <label class="label">
                        <span class="font-semibold label-text">Last Updated</span>
                    </label>
                    <input type="text" id="view_updated_at" class="bg-gray-100 w-full text-black input input-bordered" readonly />
                </div>
            </div>
        </div>

        <!-- Benefits Section -->
        <div class="mb-6">
            <h4 class="mb-4 pb-2 border-gray-200 border-b font-semibold text-lg">Enrolled Benefits</h4>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-gray-200 border-b">
                            <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Benefit Name</th>
                            <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Type</th>
                            <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Value</th>
                            <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Taxable</th>
                            <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Status</th>
                            <th class="px-4 py-3 font-medium text-gray-600 text-sm text-left">Enrollment ID</th>
                        </tr>
                    </thead>
                    <tbody id="benefitsTableBody">
                        <!-- Benefits will be populated here by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal-action">
            <form method="dialog">
                <button class="btn">Close</button>
            </form>
        </div>
    </div>
</dialog>