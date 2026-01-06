let currentViewBenefitId = null;
// Function to open view modal
function viewBenefitDetails(benefitId) {
    currentViewBenefitId = benefitId;

    // Fetch benefit data
    fetch(`API/get_benefit.php?id=${benefitId}`)
        .then(response => response.json())
        .then(benefit => {
            // Populate modal with benefit data
            document.getElementById('view-benefit-code').textContent = benefit.benefit_code || '-';
            document.getElementById('view-benefit-name').textContent = benefit.benefit_name || '-';
            document.getElementById('view-category').textContent = benefit.category_name || '-';
            document.getElementById('view-policy').textContent = benefit.policy_name || '-';
            document.getElementById('view-provider').textContent = benefit.provider_name || 'Company';

            // Benefit Type with badge styling
            const benefitTypeEl = document.getElementById('view-benefit-type');
            benefitTypeEl.textContent = benefit.benefit_type ? benefit.benefit_type.charAt(0).toUpperCase() + benefit.benefit_type.slice(1) : '-';
            benefitTypeEl.className = 'badge ' + getBenefitTypeBadge(benefit.benefit_type);

            // Value with formatting
            const value = parseFloat(benefit.value) || 0;
            const unit = benefit.unit || 'amount';
            document.getElementById('view-value').textContent = unit === 'percentage' ? `${value}%` : `$${value.toFixed(2)}`;

            // Status with badge styling
            const statusEl = document.getElementById('view-status');
            statusEl.textContent = benefit.status ? benefit.status.charAt(0).toUpperCase() + benefit.status.slice(1) : '-';
            statusEl.className = 'badge ' + getStatusBadge(benefit.status);

            // Taxable with badge styling
            const taxableEl = document.getElementById('view-taxable');
            taxableEl.textContent = benefit.is_taxable == 1 ? 'Yes' : 'No';
            taxableEl.className = 'badge ' + (benefit.is_taxable == 1 ? 'badge-warning' : 'badge-neutral');

            // Description
            document.getElementById('view-description').textContent = benefit.description || 'No description provided.';

            // Company Cost
            document.getElementById('view-company-cost-type').textContent = benefit.company_cost_type || '-';
            const companyCost = parseFloat(benefit.company_cost_value) || 0;
            document.getElementById('view-company-cost-value').textContent =
                benefit.company_cost_type === 'percentage' ? `${companyCost}%` : `$${companyCost.toFixed(2)}`;

            // Employee Cost
            document.getElementById('view-employee-cost-type').textContent = benefit.employee_cost_type || '-';
            const employeeCost = parseFloat(benefit.employee_cost_value) || 0;
            document.getElementById('view-employee-cost-value').textContent =
                benefit.employee_cost_type === 'percentage' ? `${employeeCost}%` : `$${employeeCost.toFixed(2)}`;

            // Dates
            document.getElementById('view-created-at').textContent = benefit.created_at ? formatDate(benefit.created_at) : '-';
            document.getElementById('view-updated-at').textContent = benefit.updated_at ? formatDate(benefit.updated_at) : '-';

            // Show modal
            document.getElementById('viewBenefitModal').showModal();
        })
        .catch(error => {
            console.error('Error fetching benefit:', error);
            Swal.fire('Error', 'Failed to load benefit details', 'error');
        });
}

// Helper function for status badge styling
function getStatusBadge(status) {
    switch (status) {
        case 'active':
            return 'badge-success';
        case 'pending':
            return 'badge-warning';
        case 'inactive':
            return 'badge-error';
        default:
            return 'badge-neutral';
    }
}

// Helper function for benefit type badge styling
function getBenefitTypeBadge(type) {
    switch (type) {
        case 'fixed':
            return 'badge-primary';
        case 'percentage':
            return 'badge-secondary';
        case 'condition-based':
            return 'badge-accent';
        default:
            return 'badge-neutral';
    }
}

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

// Function to edit current benefit from view modal
function editCurrentBenefit() {
    if (currentViewBenefitId) {
        document.getElementById('viewBenefitModal').close();
        setTimeout(() => {
            editBenefit(currentViewBenefitId);
        }, 300);
    }
}

// Update existing view button in your table
function updateViewButtons() {
    document.querySelectorAll('button[onclick^="viewBenefitDetails"]').forEach(button => {
        const oldOnClick = button.getAttribute('onclick');
        const benefitId = oldOnClick.match(/viewBenefitDetails\((\d+)\)/)[1];
        button.onclick = () => viewBenefitDetails(benefitId);
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', updateViewButtons);

function openCreateBenefitModal() {
    document.getElementById('createBenefitModal').showModal();
}

function deleteBenefit(id) {
    Swal.fire({
        icon: 'info',
        title: 'Delete Benefit',
        text: 'Are you sure you want to delete this benefit?',
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        // timer: 3000,
        customClass: {
            confirmButton: 'swal-btn btn-danger',
            cancelButton: 'swal-btn btn-cancel',
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('API/delete_benefit.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id=' + id
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to delete benefit');
                    }
                });
        }
    });
}

function approveBenefit(id) {
    Swal.fire({
        icon: 'info',
        title: 'Approve Benefit',
        text: 'Are you sure you want to approve this benefit?',
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: 'Yes, Approve',
        cancelButtonText: 'Cancel',
        // timer: 3000,
        customClass: {
            confirmButton: 'swal-btn btn-confirm',
            cancelButton: 'swal-btn btn-cancel',
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('API/approve_benefit.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id=' + id
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to approve benefit');
                    }
                });
        }
    });
}

// Edit Benefit Function
async function editBenefit(benefitId) {
    try {
        // Fetch benefit data
        const response = await fetch(`API/get_benefit.php?id=${benefitId}`);
        const benefit = await response.json();

        if (!benefit) {
            Swal.fire('Error', 'Benefit not found', 'error');
            return;
        }

        // Populate form fields
        document.getElementById('edit_benefit_id').value = benefit.id;
        document.getElementById('edit_benefit_name').value = benefit.benefit_name || '';
        document.getElementById('edit_description').value = benefit.description || '';

        // Load providers
        await loadProviders();
        if (benefit.provider_id) {
            document.getElementById('edit_provider_id').value = benefit.provider_id;
        }

        // Set dropdowns
        document.getElementById('edit_benefit_type').value = benefit.benefit_type || '';
        document.getElementById('edit_status').value = benefit.status || '';

        // Set numeric fields
        document.getElementById('edit_value').value = benefit.value || 12;
        let unit = document.getElementById('edit_unit');
        unit.value = benefit.unit;
        if (unit.value === 'amount') {
            unit.innerHTML = '$';
        } else if (unit.value === 'percentage') {
            unit.innerHTML = '%';
        }
        document.getElementById('edit_company_cost_value').value = benefit.company_cost_value || 0;
        let companyCost = document.getElementById('edit_company_cost_type');
        companyCost.value = benefit.company_cost_type;
        (companyCost.value === 'fixed') ? companyCost.innerHTML = 'fixed': companyCost.innerHTML = '%';
        document.getElementById('edit_employee_cost_value').value = benefit.employee_cost_value || 0;
        employeeCost = document.getElementById('edit_employee_cost_type').value = benefit.employee_cost_type || 'dollar';
        (employeeCost.value === 'fixed') ? employeeCost.innerHTML = 'fixed': employeeCost.innerHTML = '%';
        // Set checkbox
        document.getElementById('edit_is_taxable').checked = benefit.is_taxable == 1;

        // Show DaisyUI modal
        const modal = document.getElementById('editBenefitModal');
        modal.showModal();

    } catch (error) {
        console.error('Error loading benefit:', error);
        Swal.fire('Error', 'Failed to load benefit data', 'error');
    }
}

// Load providers for dropdown
async function loadProviders() {
    try {
        const response = await fetch('API/get_providers.php');
        const providers = await response.json();

        const providerSelect = document.getElementById('edit_provider_id');
        providerSelect.innerHTML = '<option value="">Select Provider</option>';

        providers.forEach(provider => {
            const option = document.createElement('option');
            option.value = provider.id;
            option.textContent = provider.name;
            providerSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Error loading providers:', error);
    }
}

// Close modal
function closeEditModal() {
    const modal = document.getElementById('editBenefitModal');
    modal.close();
    document.getElementById('editBenefitForm').reset();
}