<!-- Edit Policy Modal -->
<dialog id="editPolicyModal" class="modal">
    <div class="bg-white/90 shadow-xl max-w-4xl text-black modal-box">
        <form method="dialog">
            <button class="top-2 right-2 absolute btn btn-sm btn-circle btn-ghost">✕</button>
        </form>

        <h3 class="mb-2 font-bold text-gray-800 text-lg">Edit Benefit Policy</h3>
        <p class="mb-6 text-gray-500 text-sm">Update policy details and settings</p>

        <form id="editPolicyForm" method="POST" action="API/update_policy.php" class="space-y-4">
            <!-- Hidden ID field -->
            <input type="hidden" id="editPolicyId" name="policy_id">

            <!-- Policy Code & Name -->
            <div class="">
                <div class="form-control">
                    <label class="label">
                        <span class="font-medium text-gray-700 label-text">Policy Name <span class="text-red-500">*</span></span>
                    </label>
                    <input type="text" id="editPolicyName" name="policy_name"
                        class="w-full input input-bordered focus:input-primary"
                        placeholder="Health Insurance Enrollment Policy" required>
                </div>
            </div>

            <!-- Description -->
            <div class="form-control">
                <label class="label">
                    <span class="font-medium text-gray-700 label-text">Description <span class="text-red-500">*</span></span>
                </label>
                <textarea id="editDescription" name="description"
                    class="w-full h-32 textarea textarea-bordered focus:textarea-primary"
                    placeholder="Detailed description of the policy..." required></textarea>
            </div>

            <!-- Policy Type & Applies To -->
            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div class="form-control">
                    <label class="label">
                        <span class="font-medium text-gray-700 label-text">Applies To <span class="text-red-500">*</span></span>
                    </label>
                    <select id="editAppliesTo" name="applies_to"
                        class="w-full select-bordered focus:select-primary select" required>
                        <option value="" disabled>Select Applicable Group</option>
                        <option value="all employees">All Employees</option>
                        <option value="probationary">Probationary</option>
                        <option value="regular">Regular</option>
                        <option value="manager">Manager</option>
                        <option value="executive">Executive</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="font-medium text-gray-700 label-text">Status <span class="text-red-500">*</span></span>
                    </label>
                    <select id="editStatus" name="status"
                        class="w-full select-bordered focus:select-primary select" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Dates -->
            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div class="form-control">
                    <label class="label">
                        <span class="font-medium text-gray-700 label-text">Effective Date <span class="text-red-500">*</span></span>
                    </label>
                    <input type="date" id="editEffectiveDate" name="effective_date"
                        class="w-full input input-bordered focus:input-primary" required>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="font-medium text-gray-700 label-text">Expiration Date</span>
                        <span class="label-text-alt text-gray-400">(Optional)</span>
                    </label>
                    <input type="date" id="editExpirationDate" name="expiration_date"
                        class="w-full input input-bordered focus:input-primary">
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="modal-action">
                <button type="button" onclick="closeEditPolicyModal()"
                    class="hover:bg-gray-100 btn btn-ghost">
                    Cancel
                </button>
                <button type="submit"
                    class="hover:bg-blue-700 text-white btn btn-primary">
                    <i data-lucide="save" class="mr-2 w-4 h-4"></i>
                    Update Policy
                </button>
            </div>
        </form>
    </div>

    <!-- Click outside to close -->
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>