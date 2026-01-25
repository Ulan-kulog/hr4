<!-- View Benefit Details Modal -->
<dialog id="viewBenefitModal" class="modal">
    <div class="bg-white/90 p-0 max-w-4xl overflow-visible text-black modal-box">
        <!-- Modal Header -->
        <div class="bg-[#011f55] p-6 rounded-t-lg text-primary-content">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-2xl">Benefit Details</h3>
                <form method="dialog">
                    <button class="text-white btn btn-sm btn-circle btn-ghost">✕</button>
                </form>
            </div>
        </div>

        <!-- Details Content -->
        <div class="space-y-6 p-6">
            <!-- Basic Information -->
            <div class="gap-6 grid grid-cols-1 md:grid-cols-2">
                <div class="space-y-4">
                    <!-- Benefit Code -->
                    <div>
                        <label class="label">
                            <span class="font-semibold text-gray-500 label-text">Benefit Code</span>
                        </label>
                        <p class="font-medium text-lg" id="view-benefit-code">-</p>
                    </div>

                    <!-- Benefit Name -->
                    <div>
                        <label class="label">
                            <span class="font-semibold text-gray-500 label-text">Benefit Name</span>
                        </label>
                        <p class="font-medium text-lg" id="view-benefit-name">-</p>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="label">
                            <span class="font-semibold text-gray-500 label-text">Category</span>
                        </label>
                        <div id="view-category" class="badge badge-neutral">-</div>
                    </div>

                    <div>
                        <label class="label">
                            <span class="font-semibold text-gray-500 label-text">Policy</span>
                        </label>
                        <div id="view-policy" class="badge-outline badge">-</div>
                    </div>

                    <!-- Provider -->
                    <div>
                        <label class="label">
                            <span class="font-semibold text-gray-500 label-text">Provider</span>
                        </label>
                        <p class="text-lg" id="view-provider">-</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Benefit Type -->
                    <div>
                        <label class="label">
                            <span class="font-semibold text-gray-500 label-text">Benefit Type</span>
                        </label>
                        <div id="view-benefit-type" class="badge badge-info">-</div>
                    </div>

                    <!-- Value -->
                    <div>
                        <label class="label">
                            <span class="font-semibold text-gray-500 label-text">Value</span>
                        </label>
                        <p class="font-bold text-primary text-2xl" id="view-value">-</p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="label">
                            <span class="font-semibold text-gray-500 label-text">Status</span>
                        </label>
                        <div id="view-status" class="badge badge-success">-</div>
                    </div>

                    <!-- Taxable -->
                    <div>
                        <label class="label">
                            <span class="font-semibold text-gray-500 label-text">Taxable</span>
                        </label>
                        <div id="view-taxable" class="badge badge-warning">-</div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="pt-6 border-t">
                <label class="label">
                    <span class="font-semibold text-gray-500 label-text">Description</span>
                </label>
                <div class="bg-base-200 p-4 rounded-lg">
                    <p class="whitespace-pre-line" id="view-description">No description provided.</p>
                </div>
            </div>

            <!-- Cost Details -->
            <div class="gap-6 grid grid-cols-1 md:grid-cols-2 pt-6 border-t">
                <!-- Company Cost -->
                <div class="bg-base-200 p-4 rounded-lg">
                    <h4 class="mb-4 font-semibold text-gray-700">Company Cost</h4>
                    <div class="gap-4 grid grid-cols-2">
                        <div>
                            <label class="label">
                                <span class="label-text">Type</span>
                            </label>
                            <p id="view-company-cost-type">-</p>
                        </div>
                        <div>
                            <label class="label">
                                <span class="label-text">Value</span>
                            </label>
                            <p id="view-company-cost-value">-</p>
                        </div>
                    </div>
                </div>

                <!-- Employee Cost -->
                <div class="bg-base-200 p-4 rounded-lg">
                    <h4 class="mb-4 font-semibold text-gray-700">Employee Cost</h4>
                    <div class="gap-4 grid grid-cols-2">
                        <div>
                            <label class="label">
                                <span class="label-text">Type</span>
                            </label>
                            <p id="view-employee-cost-type">-</p>
                        </div>
                        <div>
                            <label class="label">
                                <span class="label-text">Value</span>
                            </label>
                            <p id="view-employee-cost-value">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Created/Updated Dates -->
            <div class="pt-6 border-t">
                <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                    <div>
                        <label class="label">
                            <span class="font-semibold text-gray-500 label-text">Created At</span>
                        </label>
                        <p id="view-created-at">-</p>
                    </div>
                    <div>
                        <label class="label">
                            <span class="font-semibold text-gray-500 label-text">Last Updated</span>
                        </label>
                        <p id="view-updated-at">-</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Actions -->
        <div class="p-6 border-t modal-action">
            <form method="dialog">
                <button class="btn btn-ghost">Close</button>
            </form>
            <button class="btn btn-primary" onclick="editCurrentBenefit()">
                <i data-lucide="edit" class="mr-2 w-4 h-4"></i>
                Edit Benefit
            </button>
        </div>
    </div>

    <!-- Backdrop -->
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>