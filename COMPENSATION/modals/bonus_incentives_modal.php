<div id="bonusModal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
    <div class="bg-white mx-4 p-6 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-800 text-lg">Create Bonus & Incentive Plan</h3>
            <button id="closeBonusModal" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="createBonusForm" class="space-y-4 create-form" method="POST" action="API/create_bonus_plan.php">
            <div>
                <label class="block mb-1 font-medium text-gray-700 text-sm">Plan Name *</label>
                <input name="plan_name" type="text" required class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="e.g., Sales Commission Plan">
            </div>

            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Bonus Type *</label>
                    <select name="bonus_type" required class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                        <option value="">Select Type</option>
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
                    <input name="amount_or_percentage" type="text" required class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" placeholder="e.g., 5% or ₱2,000">
                </div>
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
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Status</label>
                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Start Date</label>
                    <input name="start_date" type="date" min="<?= $today ?>" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">End Date</label>
                    <input name="end_date" type="date" min="<?= $today ?>" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                </div>
            </div>

            <div>
                <label class="block mb-1 font-medium text-gray-700 text-sm">Eligibility Criteria</label>
                <textarea name="eligibility_criteria" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full h-20" placeholder="Describe eligibility requirements..."></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" id="cancelBonus" class="hover:bg-gray-50 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white transition-colors">
                    Create Plan
                </button>
            </div>
        </form>
    </div>
</div>