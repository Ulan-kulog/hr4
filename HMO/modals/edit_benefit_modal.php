<!-- Edit Benefit Modal (DaisyUI Modal) -->
<dialog id="editBenefitModal" class="modal">
    <div class="max-w-4xl modal-box">
        <form method="dialog">
            <button class="top-2 right-2 absolute btn btn-sm btn-circle btn-ghost">✕</button>
        </form>

        <h3 class="font-bold text-lg">Edit Benefit</h3>
        <p class="py-2">Update benefit information and configuration</p>

        <form id="editBenefitForm" method="POST" action="API/update_benefit.php" class="space-y-4">
            <input type="hidden" id="edit_benefit_id" name="id">

            <!-- Benefit Name and Code -->
            <div class="">
                <div class="flex-1 form-control">
                    <label class="label">
                        <span class="label-text">Benefit Name</span>
                        <span class="label-text-alt text-error">*</span>
                    </label>
                    <input type="text" id="edit_benefit_name" name="benefit_name" required
                        class="w-full input input-bordered">
                </div>
            </div>

            <!-- Provider and Type -->
            <div class="flex md:flex-row flex-col gap-4">
                <div class="flex-1 form-control">
                    <label class="label">
                        <span class="label-text">Provider</span>
                        <span class="label-text-alt text-error">*</span>
                    </label>
                    <select id="edit_provider_id" name="provider_id" required
                        class="w-full select-bordered select">
                        <option value="">Select Provider</option>
                    </select>
                </div>

                <div class="flex-1 form-control">
                    <label class="label">
                        <span class="label-text">Benefit Type</span>
                        <span class="label-text-alt text-error">*</span>
                    </label>
                    <select id="edit_benefit_type" name="benefit_type" required
                        class="w-full select-bordered select">
                        <option value="">Select Type</option>
                        <option value="fixed">Fixed</option>
                        <option value="percentage">Percentage</option>
                        <!-- <option value="condition-based">Condition Based</option> -->
                    </select>
                </div>

                <div class="flex-1 form-control">
                    <label class="label">
                        <span class="label-text">Status</span>
                        <span class="label-text-alt text-error">*</span>
                    </label>
                    <select id="edit_status" name="status" required
                        class="w-full select-bordered select">
                        <option value="">Select Status</option>
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Value Section -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Benefit Value</span>
                    <span class="label-text-alt text-error">*</span>
                </label>
                <div class="w-full join">
                    <input type="number" step="0.01" id="edit_value" name="value" required
                        class="w-full input input-bordered join-item">
                    <select name="unit"
                        class="select-bordered select join-item">
                        <option id="edit_unit"></option>
                        <option value="percentage">%</option>
                    </select>
                </div>
            </div>

            <!-- Cost Information -->
            <div class="flex md:flex-row flex-col gap-4">
                <div class="flex-1 form-control">
                    <label class="label">
                        <span class="label-text">Company Cost</span>
                    </label>
                    <div class="w-full join">
                        <input type="number" step="0.01" id="edit_company_cost_value" name="company_cost_value"
                            class="w-full input input-bordered join-item">
                        <select name="company_cost_type"
                            class="select-bordered select join-item">
                            <option id="edit_company_cost_type" value="dollar">$</option>
                            <option value="percentage">%</option>
                        </select>
                    </div>
                </div>

                <div class="flex-1 form-control">
                    <label class="label">
                        <span class="label-text">Employee Cost</span>
                    </label>
                    <div class="w-full join">
                        <input type="number" step="0.01" id="edit_employee_cost_value" name="employee_cost_value"
                            class="w-full input input-bordered join-item">
                        <select name="employee_cost_type"
                            class="select-bordered select join-item">
                            <option id="edit_employee_cost_type" value="dollar">$</option>
                            <option value="percentage">%</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Taxable and Frequency -->
            <div class="flex md:flex-row flex-col items-center gap-4">
                <div class="form-control">
                    <label class="cursor-pointer label">
                        <input type="checkbox" id="edit_is_taxable" name="is_taxable" value="1"
                            class="checkbox checkbox-primary">
                        <span class="ml-2 label-text">Taxable Benefit</span>
                    </label>
                </div>
            </div>

            <!-- Description -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Description</span>
                </label>
                <textarea id="edit_description" name="description" rows="2"
                    class="textarea textarea-bordered"></textarea>
            </div>

            <!-- Modal Actions -->
            <div class="modal-action">
                <button type="button" onclick="closeEditModal()" class="btn btn-ghost">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Modal Backdrop -->
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>