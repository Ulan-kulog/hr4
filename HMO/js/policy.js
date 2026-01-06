let currentPolicyId = null;

function deletePolicy(policyId) {

    Swal.fire({
        icon: 'warning',
        title: 'Delete Policy',
        text: 'are you sure you want to delete this policy? This action cannot be undone',
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'btn swal-btn-danger',
            cancelButton: 'btn swal-btn-cancel',
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deletePolicyId').value = policyId;
            document.getElementById('deletePolicyForm').submit();
        }
    });
}
async function viewPolicyDetails(policyId) {
    currentPolicyId = policyId;

    try {
        // Fetch policy details from your API
        const response = await fetch(`API/get_policies.php?id=${policyId}`);
        const policy = await response.json();

        // Populate modal with policy data
        document.getElementById('modalPolicyCode').textContent = policy.policy_code || 'N/A';
        document.getElementById('modalPolicyName').textContent = policy.policy_name || 'N/A';
        document.getElementById('modalDescription').textContent = policy.description || 'No description provided';
        document.getElementById('modalAppliesTo').textContent = policy.applies_to || 'N/A';
        document.getElementById('modalEffectiveDate').textContent = policy.effective_date || 'N/A';
        document.getElementById('modalExpirationDate').textContent = policy.expiration_date || 'No expiration';
        document.getElementById('modalCreatedAt').textContent = formatDate(policy.created_at) || 'N/A';
        document.getElementById('modalUpdatedAt').textContent = policy.updated_at ? formatDate(policy.updated_at) : 'Not updated';
        document.getElementById('modalCreatedBy').textContent = policy.created_by || 'Not specified';
        document.getElementById('modalUpdatedBy').textContent = policy.updated_by || 'Not specified';

        // Set status badge
        const statusBadge = document.querySelector('#modalStatus span');
        statusBadge.textContent = policy.status ? policy.status.charAt(0).toUpperCase() + policy.status.slice(1) : 'N/A';
        statusBadge.className = `badge ${policy.status === 'active' ? 'badge-success' : 'badge-error'}`;

        document.getElementById('policy_modal').showModal();

        // Refresh Lucide icons
        if (window.lucide) {
            lucide.createIcons();
        }

    } catch (error) {
        console.error('Error fetching policy details:', error);
    }
}

// Function to edit current policy from view modal
function editCurrentPolicy() {
    if (currentPolicyId) {
        document.getElementById('policy_modal').close();
        setTimeout(() => {
            editPolicy(currentPolicyId);
        }, 300);
    }
}
// Edit Policy Function
async function editPolicy(policyId) {
    try {
        // Show loading state
        const editBtn = document.querySelector(`button[onclick="editPolicy(${policyId})"]`);
        const originalHTML = editBtn.innerHTML;
        editBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>';
        editBtn.disabled = true;

        // Fetch policy details
        const response = await fetch(`API/get_policies.php?id=${policyId}`);
        const policy = await response.json();

        // Reset button state
        editBtn.innerHTML = originalHTML;
        editBtn.disabled = false;
        lucide.createIcons();

        if (policy.error) {
            throw new Error(policy.error);
        }

        // Populate form fields
        document.getElementById('editPolicyId').value = policy.id;
        // document.getElementById('editPolicyCode').value = policy.policy_code || '';  
        document.getElementById('editPolicyName').value = policy.policy_name || '';
        document.getElementById('editDescription').value = policy.description || '';
        document.getElementById('editAppliesTo').value = policy.applies_to || 'all_employees';
        document.getElementById('editEffectiveDate').value = policy.effective_date || '';
        document.getElementById('editExpirationDate').value = policy.expiration_date || '';
        document.getElementById('editStatus').value = policy.status || 'active';

        // Open modal
        document.getElementById('editPolicyModal').showModal();

        // Refresh Lucide icons
        lucide.createIcons();

    } catch (error) {
        console.error('Error loading policy for edit:', error);
        alert('Failed to load policy details. Please try again.');
    }
}

// Close Edit Modal
function closeEditPolicyModal() {
    document.getElementById('editPolicyModal').close();
}

// Function to close modal
function closePolicyModal() {
    document.getElementById('policyModal').close();
}

// Close modal on ESC key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && document.getElementById('policyModal').open) {
        closePolicyModal();
    }
});

// Helper function to format date
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
// Add CSS for custom badges (compatible with your existing styles)
const style = document.createElement('style');
style.textContent = `
.policy-type-badge {
@apply inline-flex items-center px-3 py-1 rounded-full font-medium text-xs;
}
.policy-type-enrollment { @apply bg-blue-100 text-blue-800; }
.policy-type-compliance { @apply bg-green-100 text-green-800; }
.policy-type-security { @apply bg-red-100 text-red-800; }
.policy-type-privacy { @apply bg-purple-100 text-purple-800; }
.policy-type-default { @apply bg-gray-100 text-gray-800; }
`;
document.head.appendChild(style);
// Modal open functions
function openNewEnrollmentModal() {
    document.getElementById('newEnrollmentModal').showModal();
}

function openCreatePolicyModal() {
    document.getElementById('createPolicyModal').showModal();
}