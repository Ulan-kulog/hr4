// Edit Enrollment Functions
            const editEnrollmentModal = document.getElementById('editEnrollmentModal');

            // Handle form submission
            document.getElementById('editEnrollmentForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                // Show loading state
                submitBtn.innerHTML = '<i data-lucide="loader-2" class="mr-2 w-4 h-4 animate-spin"></i>Saving...';
                submitBtn.disabled = true;

                // Prepare data
                const data = {
                    employee_id: document.getElementById('edit_employee_id').value,
                    benefit_enrollment_id: document.getElementById('edit_benefit_enrollment_id').value,
                    start_date: document.getElementById('edit_start_date').value,
                    end_date: document.getElementById('edit_end_date').value || null,
                    status: document.getElementById('edit_status').value,
                    payroll_frequency: document.getElementById('edit_payroll_frequency').value,
                    payroll_deductible: document.getElementById('edit_payroll_deductible').checked ? 1 : 0
                };

                try {
                    const response = await fetch('API/update_enrollment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    // Restore button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;

                    if (result.success) {
                        // Close modal
                        document.getElementById('editEnrollmentModal').close();

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: result.message || 'Enrollment updated successfully',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            // Reload page to show updated data
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: result.message || 'Failed to update enrollment',
                            confirmButtonColor: '#dc2626'
                        });
                    }
                } catch (error) {
                    // Restore button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;

                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while updating the enrollment',
                        confirmButtonColor: '#dc2626'
                    });
                }
            });

            // Delete Employee Enrollment with AJAX
            function deleteEmployeeEnrollment(benefitEnrollmentId, employeeId, employeeName) {
                Swal.fire({
                    title: 'Delete Enrollment?',
                    html: `Are you sure you want to delete the enrollment for <strong>${employeeName}</strong>?<br><br>
               This will remove all benefit enrollments for this employee.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    backdrop: true,
                    allowOutsideClick: false,
                    allowEscapeKey: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Deleting...',
                            text: 'Please wait while we delete the enrollment',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Send AJAX request
                        const formData = new FormData();
                        formData.append('benefit_enrollment_id', benefitEnrollmentId);
                        formData.append('employee_id', employeeId);

                        fetch('API/delete_enrollment.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                Swal.close();

                                if (data.success) {
                                    // Show success message
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: data.message || 'Enrollment deleted successfully',
                                        showConfirmButton: false,
                                        timer: 2000,
                                        timerProgressBar: true
                                    }).then(() => {
                                        // Remove the row from the table
                                        const row = document.querySelector(`button[onclick*="deleteEmployeeEnrollment(${benefitEnrollmentId}"]`).closest('tr');
                                        if (row) {
                                            row.style.opacity = '0';
                                            row.style.transition = 'opacity 0.3s ease';
                                            setTimeout(() => {
                                                row.remove();
                                                // Update the "Showing X of X enrollments" count
                                                updateEnrollmentCount();
                                            }, 300);
                                        } else {
                                            // If row not found, reload the page
                                            location.reload();
                                        }
                                    });
                                } else {
                                    // Show error message
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: data.message || 'Failed to delete enrollment',
                                        confirmButtonColor: '#dc2626'
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.close();
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'An error occurred while deleting the enrollment',
                                    confirmButtonColor: '#dc2626'
                                });
                            });
                    }
                });
            }

            // Function to update enrollment count after deletion
            function updateEnrollmentCount() {
                const table = document.querySelector('.bg-white.shadow-sm.mb-6.p-6.border.border-gray-100.rounded-xl table tbody');
                if (table) {
                    const rowCount = table.querySelectorAll('tr').length;
                    const countElement = document.querySelector('.flex.justify-between.items-center.mt-6.pt-6.border-gray-200.border-t .text-gray-500.text-sm');
                    if (countElement) {
                        countElement.textContent = `Showing ${rowCount} of ${rowCount} enrollments`;
                    }
                }
            }

            function updateEmployeeInfo() {
                const select = document.getElementById('employeeSelect');
                const selectedOption = select.options[select.selectedIndex];
                if (!selectedOption || !selectedOption.value) {
                    document.getElementById('employeeId').value = '';
                    document.getElementById('employeeDept').value = '';
                    return;
                }

                document.getElementById('employeeId').value =
                    selectedOption.getAttribute('data-code') || '';

                document.getElementById('employeeDept').value =
                    selectedOption.getAttribute('data-department') || '';
            }