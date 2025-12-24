<div id="salaryStructureModal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
    <div class="bg-white mx-4 p-6 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-800 text-lg">Create Salary Structure</h3>
            <button id="closeSalaryModal" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="createSalaryForm" class="space-y-4 create-form" method="POST" action="API/create_salary_grade.php">
            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Grade Level</label>
                    <input name="grade_name" type="text" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="e.g., G1">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Position Title</label>
                    <input name="position" type="text" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="e.g., Junior Staff">
                </div>
            </div>
            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Minimum Salary</label>
                    <input name="min_salary" type="number" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="15000">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Maximum Salary</label>
                    <input name="max_salary" type="number" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="25000">
                </div>
            </div>
            <div>
                <label class="block mb-1 font-medium text-gray-700 text-sm">Department</label>
                <select name="department" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                    <option value="">Select Department</option>
                    <option value="hotel">Hotel Department</option>
                    <option value="restaurant">Restaurant Department</option>
                    <option value="hr">HR Department</option>
                    <option value="logistic">Logistic Department</option>
                    <option value="all">All Departments</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" id="cancelSalary" class="hover:bg-gray-50 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white transition-colors">
                    Create Structure
                </button>
            </div>
        </form>
    </div>
</div>