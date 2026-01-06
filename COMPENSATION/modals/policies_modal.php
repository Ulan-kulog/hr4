<!-- Create/Edit Policy Modal -->
<div id="policyModal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
    <div class="bg-white mx-4 p-6 rounded-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-800 text-lg" id="modalTitle">Create Compensation Policy</h3>
            <button id="closePolicyModal" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="policyForm" class="space-y-4">
            <input type="hidden" id="policyId" name="id">

            <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Policy Name *</label>
                    <input type="text" id="policyName" name="policy_name" required
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Policy Type *</label>
                    <select id="policyType" name="policy_type" required
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                        <option value="">Select Type</option>
                        <option value="bonus">Bonus Policy</option>
                        <option value="commission">Commission Policy</option>
                        <option value="merit">Merit Increase Policy</option>
                        <option value="equity">Pay Equity Policy</option>
                        <option value="allowance">Allowance Policy</option>
                    </select>
                </div>
            </div>

            <div class="gap-4 grid grid-cols-1 md:grid-cols-3">
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Version *</label>
                    <input type="text" id="version" name="version" required
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
                        placeholder="e.g., v1.0">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Effective Date *</label>
                    <input type="date" id="effectiveDate" name="effective_date" required
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700 text-sm">Compliance Rate (%)</label>
                    <input type="number" id="complianceRate" name="compliance_rate" min="0" max="100" step="0.1"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                </div>
            </div>

            <div>
                <label class="block mb-1 font-medium text-gray-700 text-sm">Status</label>
                <select id="status" name="status"
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                    <option value="draft">Draft</option>
                    <option value="under_review">Under Review</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div>
                <label class="block mb-1 font-medium text-gray-700 text-sm">Description *</label>
                <textarea id="description" name="description" rows="4" required
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
                    placeholder="Describe the policy details and guidelines..."></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="button" id="cancelPolicy"
                    class="hover:bg-gray-50 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" id="savePolicyBtn"
                    class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg text-white transition-colors">
                    Save Policy
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Policy Modal -->
<div id="viewPolicyModal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
    <div class="bg-white mx-4 p-6 rounded-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-800 text-lg" id="viewPolicyTitle"></h3>
            <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div id="policyDetails" class="space-y-4"></div>
    </div>
</div>

<!-- Document Upload Modal -->
<div id="uploadDocumentModal" class="hidden z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
    <div class="bg-white mx-4 p-6 rounded-xl w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-800 text-lg">Upload Policy Document</h3>
            <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="documentUploadForm" enctype="multipart/form-data">
            <input type="hidden" id="uploadPolicyId">
            <div class="mb-4">
                <label class="block mb-1 font-medium text-gray-700 text-sm">Select Document</label>
                <input type="file" id="policyDocument" name="document" accept=".pdf,.doc,.docx,.xls,.xlsx" required
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                <p class="mt-1 text-gray-500 text-xs">Accepted formats: PDF, DOC, DOCX, XLS, XLSX (Max 10MB)</p>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeUploadModal()"
                    class="hover:bg-gray-50 px-4 py-2 border border-gray-300 rounded-lg text-gray-700">
                    Cancel
                </button>
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-lg text-white">
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>