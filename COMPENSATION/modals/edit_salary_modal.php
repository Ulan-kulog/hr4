<div id="editSalaryModal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
    <div class="bg-white mx-4 p-6 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-800 text-lg">Edit Salary Structure</h3>
            <button id="closeEditModal" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="editSalaryForm" class="space-y-4" method="POST" action="API/update_salary_grade.php">
            <input type="hidden" id="edit_id" name="id">
            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Grade Level</label>
                    <input id="edit_grade" name="grade" type="text" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full" placeholder="e.g., G1">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Position Title</label>
                    <input id="edit_position" name="position" type="text" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full" placeholder="e.g., Junior Staff">
                </div>
            </div>
            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Minimum Salary</label>
                    <input id="edit_min" name="min_salary" type="number" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full" placeholder="15000">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Maximum Salary</label>
                    <input id="edit_max" name="max_salary" type="number" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full" placeholder="25000">
                </div>
            </div>
            <!-- Department removed from edit modal per request -->
            <div>
                <label class="block mb-1 font-medium text-gray-700 text-sm">Status</label>
                <select id="edit_status" name="status" class="bg-white px-3 py-2 border border-gray-300 rounded-lg w-full">
                    <option value="Active">Active</option>
                    <option value="Pending Review">Pending Review</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" id="cancelEdit" class="hover:bg-gray-50 px-4 py-2 border border-gray-300 rounded-lg text-gray-700">Cancel</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white">Save Changes</button>
            </div>
        </form>
    </div>
</div>