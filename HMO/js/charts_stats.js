// Function to initialize all charts
            

            // Initialize charts when DOM is loaded
            document.addEventListener('DOMContentLoaded', function() {
                initializeCharts();
            });

            // Filter functions for charts
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize charts
                initializeCharts();

                // Add event listeners for chart filters
                document.getElementById('planTimeFilter')?.addEventListener('change', function() {
                    filterChartData('plan', this.value);
                });

                document.getElementById('enrollmentTimeFilter')?.addEventListener('change', function() {
                    filterChartData('enrollment', this.value);
                });

                document.getElementById('trendTimeFilter')?.addEventListener('change', function() {
                    filterChartData('trend', this.value);
                });

                document.getElementById('deptFilter')?.addEventListener('change', function() {
                    filterChartData('dept', this.value);
                });
            });

            // Function to filter chart data via AJAX
            function filterChartData(chartType, filterValue) {
                // Show loading state
                const loadingSwal = Swal.fire({
                    title: 'Loading...',
                    text: 'Updating chart data',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send AJAX request
                fetch('API/get_chart_data.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            chart_type: chartType,
                            filter: filterValue
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();

                        if (data.success) {
                            updateChart(chartType, data.data);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message || 'Failed to update chart data',
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
                            text: 'An error occurred while updating the chart',
                            confirmButtonColor: '#dc2626'
                        });
                    });
            }

            // Function to update specific chart with new data
            function updateChart(chartType, data) {
                switch (chartType) {
                    case 'plan':
                        if (planDistributionChart) {
                            planDistributionChart.data.labels = data.labels;
                            planDistributionChart.data.datasets[0].data = data.data;
                            planDistributionChart.update();
                        }
                        break;

                    case 'enrollment':
                        if (enrollmentStatusChart) {
                            enrollmentStatusChart.data.labels = data.labels;
                            enrollmentStatusChart.data.datasets[0].data = data.data;
                            enrollmentStatusChart.update();
                        }
                        break;

                    case 'trend':
                        if (monthlyTrendChart) {
                            monthlyTrendChart.data.labels = data.labels;
                            monthlyTrendChart.data.datasets[0].data = data.data;
                            monthlyTrendChart.update();
                        }
                        break;

                    case 'dept':
                        if (departmentChart) {
                            departmentChart.data.labels = data.labels;
                            departmentChart.data.datasets[0].data = data.data;
                            departmentChart.update();
                        }
                        break;
                }
            }

            // Function to refresh all charts
            function refreshCharts() {
                initializeCharts();
            }

            // Auto-refresh charts every 2 minutes
            setInterval(refreshCharts, 120000);

            // Also refresh charts when certain actions are performed
            document.addEventListener('DOMContentLoaded', function() {
                // Refresh charts after modal actions
                const modals = ['editEnrollmentModal', 'createBenefitModal', 'createPolicyModal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.addEventListener('close', refreshCharts);
                    }
                });
            });

            // Function to refresh stats without page reload
            function refreshStats() {
                fetch('API/get_stats.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update all stats cards
                            document.querySelector('h3:contains("Total Benefits")').nextElementSibling.textContent = data.total_benefits;
                            document.querySelector('h3:contains("Enrolled Employees")').nextElementSibling.textContent = data.enrolled_employees;
                            document.querySelector('h3:contains("Active Policies")').nextElementSibling.textContent = data.active_policies;
                            document.querySelector('h3:contains("Coverage Rate")').nextElementSibling.textContent = data.coverage_rate + '%';

                            // Update additional stats
                            document.querySelector('h3:contains("Total Employees")').nextElementSibling.textContent = data.total_employees;
                            document.querySelector('h3:contains("Pending Approvals")').nextElementSibling.textContent = data.pending_enrollments;
                            document.querySelector('h3:contains("Upcoming Renewals")').nextElementSibling.textContent = data.upcoming_renewals;

                            // Optional: Show notification
                        } else {
                            console.error('Failed to refresh stats:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error refreshing stats:', error);
                    });
            }

            // Auto-refresh stats every 5 minutes
            setInterval(refreshStats, 300000);

            // Also refresh stats when certain actions are performed
            document.addEventListener('DOMContentLoaded', function() {
                // Refresh stats after modal actions
                const modals = ['editEnrollmentModal', 'createBenefitModal', 'createPolicyModal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.addEventListener('close', refreshStats);
                    }
                });
            });

            // Add filtering functionality
            document.addEventListener('DOMContentLoaded', function() {
                // Benefit type filter
                const benefitTypeFilter = document.getElementById('benefitTypeFilter');
                const taxableFilter = document.getElementById('taxableFilter');
                const searchInput = document.getElementById('searchBenefits');
                const benefitRows = document.querySelectorAll('.benefit-row');

                function filterBenefits() {
                    const selectedType = benefitTypeFilter.value;
                    const selectedTaxable = taxableFilter.value;
                    const searchTerm = searchInput.value.toLowerCase();

                    benefitRows.forEach(row => {
                        const type = row.getAttribute('data-type');
                        const taxable = row.getAttribute('data-taxable');
                        const text = row.textContent.toLowerCase();

                        const typeMatch = !selectedType || type === selectedType;
                        const taxableMatch = !selectedTaxable || taxable === selectedTaxable;
                        const searchMatch = !searchTerm || text.includes(searchTerm);

                        row.style.display = (typeMatch && taxableMatch && searchMatch) ? '' : 'none';
                    });
                }

                benefitTypeFilter.addEventListener('change', filterBenefits);
                taxableFilter.addEventListener('change', filterBenefits);
                searchInput.addEventListener('input', filterBenefits);
            });

            // Search functionality
            document.addEventListener('DOMContentLoaded', function() {
                const searchInputs = document.querySelectorAll('input[placeholder*="Search"]');
                searchInputs.forEach(input => {
                    input.addEventListener('input', function(e) {
                        const searchTerm = e.target.value.toLowerCase();
                        // In real implementation, this would filter the table rows
                    });
                });
            });