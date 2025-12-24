<div id="editAllowanceModal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
    <div class="bg-white mx-4 p-6 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-800 text-lg">Edit Allowance</h3>
            <button id="closeEditAllowanceModal" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="editAllowanceForm" class="space-y-4" method="POST" action="API/update_allowance.php">
            <input type="hidden" id="edit_alw_id" name="id">

            <div>
                <label class="block mb-1 font-medium text-gray-700 text-sm">Allowance Type *</label>
                <input id="edit_alw_type" name="allowance_type" type="text" required class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full">
            </div>

            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Department</label>
                    <select id="edit_alw_dept" name="department" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full">
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
                    <select id="edit_alw_freq" name="frequency" required class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="annual">Annual</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block mb-1 font-medium text-gray-700 text-sm">Amount (₱) *</label>
                <input id="edit_alw_amount" name="amount" type="number" step="0.01" min="0" required class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full">
            </div>

            <div>
                <label class="block mb-1 font-medium text-gray-700 text-sm">Eligibility Criteria</label>
                <textarea id="edit_alw_criteria" name="eligibility_criteria" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full h-20"></textarea>
            </div>

            <div>
                <label class="block mb-1 font-medium text-gray-700 text-sm">Status</label>
                <select id="edit_alw_status" name="status" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" id="cancelEditAllowance" class="hover:bg-gray-50 px-4 py-2 border border-gray-300 rounded-lg text-gray-700">Cancel</button>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg text-white">Save Changes</button>
            </div>
        </form>
    </div>
</div>