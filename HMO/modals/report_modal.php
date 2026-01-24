<!-- Report Modal -->
<div id="reportModal" class="hidden z-50 fixed inset-0 overflow-y-auto">
    <div class="sm:block flex justify-center items-center sm:p-0 px-4 pt-4 pb-20 min-h-screen text-center">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeReportModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block bg-white shadow-xl sm:my-8 rounded-lg sm:w-full sm:max-w-lg overflow-hidden text-left sm:align-middle align-bottom transition-all transform">
            <div class="bg-white sm:p-6 px-4 pt-5 pb-4 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 sm:mt-0 sm:ml-4 w-full sm:text-left text-center">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold text-gray-900 text-lg">Generate Report</h3>
                            <button onclick="closeReportModal()" class="text-gray-400 hover:text-gray-500">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <form id="reportForm">
                            <div class="space-y-4">
                                <!-- Report Type -->
                                <div>
                                    <label class="block mb-2 font-medium text-gray-700 text-sm">Report Type</label>
                                    <div class="gap-3 grid grid-cols-2">
                                        <label class="flex items-center hover:bg-gray-50 p-3 border border-gray-200 rounded-lg cursor-pointer">
                                            <input type="radio" name="report_type" value="enrollment_summary" class="mr-2" checked>
                                            <div>
                                                <div class="font-medium text-gray-800">Enrollment Summary</div>
                                                <div class="text-gray-500 text-xs">Department enrollment overview</div>
                                            </div>
                                        </label>

                                        <label class="flex items-center hover:bg-gray-50 p-3 border border-gray-200 rounded-lg cursor-pointer">
                                            <input type="radio" name="report_type" value="benefit_utilization" class="mr-2">
                                            <div>
                                                <div class="font-medium text-gray-800">Benefit Utilization</div>
                                                <div class="text-gray-500 text-xs">Usage analysis of benefits</div>
                                            </div>
                                        </label>

                                        <label class="flex items-center hover:bg-gray-50 p-3 border border-gray-200 rounded-lg cursor-pointer">
                                            <input type="radio" name="report_type" value="cost_analysis" class="mr-2">
                                            <div>
                                                <div class="font-medium text-gray-800">Cost Analysis</div>
                                                <div class="text-gray-500 text-xs">Cost breakdown by department</div>
                                            </div>
                                        </label>

                                        <label class="flex items-center hover:bg-gray-50 p-3 border border-gray-200 rounded-lg cursor-pointer">
                                            <input type="radio" name="report_type" value="employee_benefit_statement" class="mr-2">
                                            <div>
                                                <div class="font-medium text-gray-800">Employee Statement</div>
                                                <div class="text-gray-500 text-xs">Individual benefit statements</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Employee Selection (for benefit statement) -->
                                <div id="employeeSelection" class="hidden">
                                    <label class="block mb-2 font-medium text-gray-700 text-sm">Select Employee</label>
                                    <select name="employee_id" class="px-3 py-2 border border-gray-300 rounded-lg w-full">
                                        <option value="">Select Employee</option>
                                        <?php foreach ($employees as $employee): ?>
                                            <option value="<?= $employee->id ?>">
                                                <?= htmlspecialchars($employee->first_name . ' ' . $employee->last_name) ?>
                                                (<?= htmlspecialchars($employee->employee_code) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Date Range -->
                                <div class="gap-4 grid grid-cols-2">
                                    <div>
                                        <label class="block mb-1 font-medium text-gray-700 text-sm">Start Date</label>
                                        <input type="date" name="date_from" class="px-3 py-2 border border-gray-300 rounded-lg w-full">
                                    </div>
                                    <div>
                                        <label class="block mb-1 font-medium text-gray-700 text-sm">End Date</label>
                                        <input type="date" name="date_to" class="px-3 py-2 border border-gray-300 rounded-lg w-full">
                                    </div>
                                </div>

                                <!-- Department Filter -->
                                <div>
                                    <label class="block mb-1 font-medium text-gray-700 text-sm">Department (Optional)</label>
                                    <select name="department" class="px-3 py-2 border border-gray-300 rounded-lg w-full">
                                        <option value="">All Departments</option>
                                        <?php
                                        $departments = [];
                                        foreach ($employees as $employee) {
                                            $deptVal = $employee->department_name ?? $employee->department ?? '';
                                            if ($deptVal !== '') {
                                                $departments[$deptVal] = $deptVal;
                                            }
                                        }
                                        sort($departments);
                                        foreach ($departments as $dept): ?>
                                            <option value="<?= htmlspecialchars($dept) ?>"><?= htmlspecialchars($dept) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Format Selection -->
                                <div>
                                    <label class="block mb-2 font-medium text-gray-700 text-sm">Output Format</label>
                                    <div class="flex space-x-4">
                                        <label class="flex items-center">
                                            <input type="radio" name="format" value="csv" class="mr-2" checked>
                                            <span class="text-gray-700">CSV File</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="format" value="pdf" class="mr-2">
                                            <span class="text-gray-700">PDF Document</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="sm:flex sm:flex-row-reverse bg-gray-50 px-4 sm:px-6 py-3">
                <button onclick="submitReportForm()" class="inline-flex justify-center bg-blue-600 hover:bg-blue-700 shadow-sm sm:ml-3 px-4 py-2 border border-transparent rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 w-full sm:w-auto font-medium text-white sm:text-sm text-base">
                    <i data-lucide="download" class="mr-2 w-4 h-4"></i>
                    Generate Report
                </button>
                <button onclick="closeReportModal()" class="inline-flex justify-center bg-white hover:bg-gray-50 shadow-sm mt-3 sm:mt-0 sm:ml-3 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 w-full sm:w-auto font-medium text-gray-700 sm:text-sm text-base">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>