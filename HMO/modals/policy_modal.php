<!-- Create Policy Modal -->
<dialog id="createPolicyModal" class="modal-bottom modal sm:modal-middle">
    <div class="bg-white/90 max-w-4xl text-black modal-box">
        <form method="dialog">
            <button class="top-2 right-2 absolute btn btn-sm btn-circle btn-ghost">✕</button>
        </form>
        <h3 class="mb-6 font-bold text-purple-700 text-lg">Create New Policy</h3>

        <form id="policyForm" method="POST" action="API/create_policy.php" class="space-y-6">
            <!-- Policy Information -->
            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Policy Name *</span>
                    </label>
                    <input type="text" name="policy_name" class="input input-bordered" placeholder="Health Insurance Enrollment Policy" required>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Applies to*</span>
                    </label>
                    <select name="policy_type" class="select-bordered select" required>
                        <option selected disabled>Select an option</option>
                        <option value="all employees">All employees</option>
                        <option value="regular">Regular employees</option>
                        <option value="probationary">Probationary</option>
                        <option value="manager">Manager</option>
                        <option value="executive">Executive</option>
                    </select>
                </div>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Policy Description</span>
                </label>
                <textarea name="description" class="h-24 textarea textarea-bordered" placeholder="Describe the purpose and scope of this policy..."></textarea>
            </div>

            <!-- Impact & Priority -->
            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Effective Date *</span>
                    </label>
                    <input type="date" min="<?= date('Y-m-d') ?>" name="effective_date" class="input input-bordered" required>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Expiration Date</span>
                    </label>
                    <input type="date" min="<?= date('Y-m-d') ?>" name="expiration_date" class="input input-bordered">
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="modal-action">
                <button type="button" class="btn btn-ghost" onclick="createPolicyModal.close()">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save" class="mr-2 w-4 h-4"></i>
                    Save Policy
                </button>
            </div>
        </form>
    </div>
</dialog>