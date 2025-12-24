<div id="allowanceModal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
    <div class="bg-white mx-4 p-6 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-800 text-lg">Create Allowance</h3>
            <button id="closeAllowanceModal" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="createAllowanceForm" class="space-y-4 create-form" method="POST" action="API/create_allowance.php">
            <div>
                <label class="block mb-1 font-medium text-gray-700 text-sm">Allowance Type *</label>
                <input name="allowance_type" type="text" required class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="e.g., Transportation, Meal, Uniform">
            </div>

            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Department</label>
                    <select name="department" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                        <option value="All">All Departments</option>
                        <option value="hotel">Hotel Department</option>
                        <option value="restaurant">Restaurant Department</option>
                        <option value="hr">HR Department</option>
                        <option value="logistic">Logistic Department</option>
                        <option value="administrative">Administrative</option>
                        <option value="financial">Financial</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Frequency *</label>
                    <select name="frequency" required class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly" selected>Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="annual">Annual</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block mb-1 font-medium text-gray-700 text-sm">Amount (₱) *</label>
                <input name="amount" type="number" step="0.01" min="0" required class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="2000">
            </div>

            <div>
                <label class="block mb-1 font-medium text-gray-700 text-sm">Eligibility Criteria</label>
                <textarea name="eligibility_criteria" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full h-20" placeholder="Describe eligibility requirements (optional)"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" id="cancelAllowance" class="hover:bg-gray-50 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg text-white transition-colors">
                    Create Allowance
                </button>
            </div>
        </form>
    </div>
</div>