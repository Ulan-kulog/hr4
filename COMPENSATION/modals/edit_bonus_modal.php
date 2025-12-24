<div id="editBonusModal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
    <div class="bg-white mx-4 p-6 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-800 text-lg">Edit Bonus & Incentive Plan</h3>
            <button id="closeEditBonusModal" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="editBonusForm" class="space-y-4" method="POST" action="API/update_bonus_plan.php">
            <input type="hidden" id="edit_bonus_id" name="id">

            <div>
                <label class="block mb-1 font-medium text-gray-700 text-sm">Plan Name *</label>
                <input id="edit_bonus_name" name="plan_name" type="text" required class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full">
            </div>

            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Bonus Type *</label>
                    <select id="edit_bonus_type" name="bonus_type" required class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full">
                        <option value="commission">Commission</option>
                        <option value="performance">Performance Bonus</option>
                        <option value="referral">Referral Bonus</option>
                        <option value="seasonal">Seasonal Incentive</option>
                        <option value="attendance">Attendance Bonus</option>
                        <option value="profit_sharing">Profit Sharing</option>
                        <option value="year_end">Year-end Bonus</option>
                        <option value="retention">Retention Bonus</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Amount/Percentage *</label>
                    <input id="edit_bonus_amount" name="amount_or_percentage" type="text" required class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full">
                </div>
            </div>

            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Department</label>
                    <select id="edit_bonus_dept" name="department" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full">
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
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Status</label>
                    <select id="edit_bonus_status" name="status" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Start Date</label>
                    <input id="edit_bonus_start" name="start_date" type="date" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">End Date</label>
                    <input id="edit_bonus_end" name="end_date" type="date" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full">
                </div>
            </div>

            <div>
                <label class="block mb-1 font-medium text-gray-700 text-sm">Eligibility Criteria</label>
                <textarea id="edit_bonus_criteria" name="eligibility_criteria" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full h-20"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" id="cancelEditBonus" class="hover:bg-gray-50 px-4 py-2 border border-gray-300 rounded-lg text-gray-700">
                    Cancel
                </button>
                <button type="submit" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>