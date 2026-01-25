<!-- Policy View Modal -->
<dialog id="policy_modal" class="modal">
    <div class="bg-white/90 max-w-4xl text-black modal-box">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-2xl">Policy Details</h3>
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost">✕</button>
            </form>
        </div>

        <!-- Policy Information Grid -->
        <div class="space-y-6">
            <!-- Basic Info Row -->
            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div class="form-control">
                    <label class="label">
                        <span class="font-semibold label-text">Policy Code</span>
                    </label>
                    <div id="modalPolicyCode" class="bg-base-200 input input-bordered">
                        Loading...
                    </div>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="font-semibold label-text">Policy Name</span>
                    </label>
                    <div id="modalPolicyName" class="bg-base-200 input input-bordered">
                        Loading...
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="form-control">
                <label class="label">
                    <span class="font-semibold label-text">Description</span>
                </label>
                <div id="modalDescription" class="bg-base-200 min-h-[100px] textarea textarea-bordered">
                    Loading...
                </div>
            </div>

            <!-- Policy Details -->
            <div class="gap-4 grid grid-cols-1 md:grid-cols-3">
                <div class="form-control">
                    <label class="label">
                        <span class="font-semibold label-text">Applies To</span>
                    </label>
                    <div id="modalAppliesTo" class="bg-base-200 input input-bordered">
                        Loading...
                    </div>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="font-semibold label-text">Status</span>
                    </label>
                    <div id="modalStatus" class="bg-base-200 input input-bordered">
                        <span class="badge">Loading...</span>
                    </div>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="font-semibold label-text">Created At</span>
                    </label>
                    <div id="modalCreatedAt" class="bg-base-200 input input-bordered">
                        Loading...
                    </div>
                </div>
            </div>

            <!-- Date Range -->
            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div class="form-control">
                    <label class="label">
                        <span class="font-semibold label-text">Effective Date</span>
                    </label>
                    <div id="modalEffectiveDate" class="bg-base-200 input input-bordered">
                        Loading...
                    </div>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="font-semibold label-text">Expiration Date</span>
                    </label>
                    <div id="modalExpirationDate" class="bg-base-200 input input-bordered">
                        Loading...
                    </div>
                </div>
            </div>

            <!-- User Information -->
            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div class="form-control">
                    <label class="label">
                        <span class="font-semibold label-text">Created By (ID)</span>
                    </label>
                    <div id="modalCreatedBy" class="bg-base-200 input input-bordered">
                        Loading...
                    </div>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="font-semibold label-text">Updated By (ID)</span>
                    </label>
                    <div id="modalUpdatedBy" class="bg-base-200 input input-bordered">
                        Loading...
                    </div>
                </div>
            </div>

            <!-- Last Updated -->
            <div class="form-control">
                <label class="label">
                    <span class="font-semibold label-text">Last Updated</span>
                </label>
                <div id="modalUpdatedAt" class="bg-base-200 input input-bordered">
                    Loading...
                </div>
            </div>
        </div>

        <!-- Modal Actions -->
        <div class="modal-action">
            <form method="dialog">
                <button class="btn">Close</button>
            </form>
        </div>
    </div>

    <!-- Backdrop Click to Close -->
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>