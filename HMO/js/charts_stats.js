// charts_stats.js — cleaned up: only enrollment & department chart handlers remain

document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts defined on the page (if present)
    if (typeof initializeCharts === 'function') initializeCharts();

    // Filter controls
    document.getElementById('enrollmentTimeFilter')?.addEventListener('change', function() {
        filterChartData('enrollment', this.value);
    });
    document.getElementById('deptFilter')?.addEventListener('change', function() {
        filterChartData('dept', this.value);
    });

    // Refresh charts after modal close actions
    const modals = ['editEnrollmentModal', 'createBenefitModal', 'createPolicyModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) modal.addEventListener('close', () => { if (typeof initializeCharts === 'function') initializeCharts(); });
    });
});

function filterChartData(chartType, filterValue) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({ title: 'Loading...', text: 'Updating chart data', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
    }

    fetch('API/get_chart_data.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ chart_type: chartType, filter: filterValue })
        })
        .then(resp => resp.json())
        .then(json => {
            if (typeof Swal !== 'undefined') Swal.close();
            if (json.success) updateChart(chartType, json.data);
            else if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error!', text: json.message || 'Failed to update chart data' });
        })
        .catch(err => {
            if (typeof Swal !== 'undefined') Swal.close();
            console.error('Error fetching chart data', err);
            if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error!', text: 'An error occurred while updating the chart' });
        });
}

function updateChart(chartType, data) {
    try {
        if (chartType === 'enrollment') {
            const canvas = document.getElementById('enrollmentStatusChart');
            const chart = canvas ? Chart.getChart(canvas) : null;
            if (chart) { chart.data.labels = data.labels; chart.data.datasets[0].data = data.data; chart.update(); }
        } else if (chartType === 'dept') {
            const canvas = document.getElementById('departmentChart');
            const chart = canvas ? Chart.getChart(canvas) : null;
            if (chart) { chart.data.labels = data.labels; chart.data.datasets[0].data = data.data; chart.update(); }
        }
    } catch (e) {
        console.error('updateChart error', e);
    }
}

// Auto-refresh charts periodically
setInterval(() => { if (typeof initializeCharts === 'function') initializeCharts(); }, 120000);

// Auto-refresh stats (kept minimal)
setInterval(() => {
    fetch('API/get_stats.php').then(r => r.json()).then(d => { if (d.success) {/* optional DOM updates can be implemented */} }).catch(() => {});
}, 300000);
