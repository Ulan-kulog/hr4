// Enrollment JS (non-AJAX): form submissions are handled by native HTML forms.
// Keep helper to update employee info in forms.
function updateEmployeeInfo() {
    const select = document.getElementById('employeeSelect');
    if (!select) return;
    const selectedOption = select.options[select.selectedIndex];
    if (!selectedOption || !selectedOption.value) {
        const idEl = document.getElementById('employeeId'); if (idEl) idEl.value = '';
        const deptEl = document.getElementById('employeeDept'); if (deptEl) deptEl.value = '';
        return;
    }

    const idEl = document.getElementById('employeeId'); if (idEl) idEl.value = selectedOption.getAttribute('data-code') || '';
    const deptEl = document.getElementById('employeeDept'); if (deptEl) deptEl.value = selectedOption.getAttribute('data-department') || '';
}