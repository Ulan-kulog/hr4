<!-- Create Benefit Modal -->
<style>
    /* Scoped light-theme overrides for the Create Benefit modal inputs */
    #createBenefitModal .input,
    #createBenefitModal input[type="number"],
    #createBenefitModal .select,
    #createBenefitModal select,
    #createBenefitModal .textarea,
    #createBenefitModal .label-text {
        background-color: #ffffff !important;
        color: #000000 !important;
        border-color: #d1d5db !important;
        /* tailwind gray-300 */
    }

    #createBenefitModal .input,
    #createBenefitModal select,
    #createBenefitModal .select select {
        box-shadow: none !important;
        outline: none !important;
    }

    #createBenefitModal .label-text {
        color: #374151 !important;
        /* gray-700 */
    }

    #createBenefitModal .textarea {
        background-color: #ffffff !important;
        color: #111827 !important;
    }

    #createBenefitModal .toggle,
    #createBenefitModal .checkbox {
        accent-color: #16a34a;
        /* green-600 for toggles/checkboxes */
    }

    #createBenefitModal .modal-box {
        background-color: #ffffff !important;
        color: #000000 !important;
    }
</style>

<dialog id="createBenefitModal" class="modal-bottom modal sm:modal-middle">
    <div class="bg-white shadow-lg border border-gray-200 max-w-3xl text-black modal-box">
        <form method="dialog">
            <button class="top-2 right-2 absolute btn btn-sm btn-circle btn-ghost">✕</button>
        </form>
        <h3 class="mb-6 font-bold text-green-700 text-lg">Create New Benefit</h3>

        <form id="benefitForm" method="POST" action="API/create_benefit.php" class="space-y-6">
            <!-- Basic Information -->
            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Benefit Name *</span>
                    </label>
                    <input type="text" name="benefit_name" class="input input-bordered" placeholder="Premium Health Insurance" required>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Category *</span>
                    </label>
                    <select name="category_id" class="select-bordered select" required>
                        <option value="">Select Category</option>
                        <?php foreach ($benefit_categories as $category): ?>
                            <option value="<?= htmlspecialchars($category->id) ?>"><?= htmlspecialchars($category->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Policy *</span>
                </label>
                <select name="policy_id" class="select-bordered select" required>
                    <option value="">Select Policy</option>
                    <?php foreach ($policies as $policy): ?>
                        <option value="<?= $policy->id ?>">
                            <?= htmlspecialchars($policy->policy_code) ?> — <?= htmlspecialchars($policy->policy_name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Description</span>
                </label>
                <textarea name="description" class="h-20 textarea textarea-bordered" placeholder="Describe this benefit..."></textarea>
            </div>

            <!-- Provider & Type -->
            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Provider</span>
                    </label>
                    <select name="provider_id" class="select-bordered select" id="providerSelect">
                        <option value="">Select Provider</option>
                        <?php foreach ($providers as $provider): ?>
                            <option value="<?= $provider->id ?>"><?= htmlspecialchars($provider->name) ?></option>
                        <?php endforeach; ?>
                        <option value="0">company</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Benefit Type *</span>
                    </label>
                    <select name="benefit_type" class="select-bordered select" required>
                        <option value="">Select Type</option>
                        <option value="fixed">Fixed</option>
                        <option value="percentage">Percentage</option>
                    </select>
                </div>
            </div>

            <!-- Financial Details -->
            <div class="space-y-4">
                <h4 class="pb-2 border-b font-semibold text-gray-700">Financial Details</h4>

                <div class="gap-4 grid grid-cols-1 md:grid-cols-3">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Benefit Value</span>
                        </label>
                        <div class="w-full join">
                            <input type="number" name="value" class="w-full input input-bordered join-item" placeholder="0.00" step="0.01">
                            <select name="unit" class="w-full select-bordered select join-item">
                                <option value="amount">Amount</option>
                                <option value="percentage">Percentage</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Company Cost</span>
                        </label>
                        <div class="join">
                            <input type="number" name="company_cost_value" class="w-full input input-bordered join-item" placeholder="0.00" step="0.01">
                            <select name="company_cost_type" class="select-bordered select join-item">
                                <option value="percentage">%</option>
                                <option value="fixed">fixed</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Employee Cost</span>
                        </label>
                        <div class="join">
                            <input type="number" name="employee_cost_value" class="w-full input input-bordered join-item" placeholder="0.00" step="0.01">
                            <select name="employee_cost_type" class="select-bordered select join-item">
                                <option value="percentage">%</option>
                                <option value="fixed">fixed</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Details -->
            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">

                <div class="form-control">
                    <label class="cursor-pointer label">
                        <span class="label-text">Is Taxable?</span>
                        <input type="checkbox" name="is_taxable" class="toggle toggle-primary">
                    </label>
                </div>

            </div>

            <!-- Modal Actions -->
            <div class="modal-action">
                <button type="button" class="btn btn-ghost" onclick="createBenefitModal.close()">Cancel</button>
                <button type="submit" class="btn btn-success">
                    <i data-lucide="save" class="mr-2 w-4 h-4"></i>
                    Create Benefit
                </button>
            </div>
        </form>
    </div>
</dialog>