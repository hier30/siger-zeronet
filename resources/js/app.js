// Sidebar Toggle
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle');
    const mainContent = document.getElementById('main-content');
    const sidebarTexts = document.querySelectorAll('.sidebar-text');
    const sidebarLogo = document.getElementById('sidebar-logo');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');

            if (sidebar.classList.contains('collapsed')) {
                sidebar.style.width = '70px';
                if (mainContent) mainContent.style.marginLeft = '70px';
                sidebarTexts.forEach(el => el.style.display = 'none');
                if (sidebarLogo) sidebarLogo.style.display = 'none';
            } else {
                sidebar.style.width = '260px';
                if (mainContent) mainContent.style.marginLeft = '260px';
                sidebarTexts.forEach(el => el.style.display = 'inline');
                if (sidebarLogo) sidebarLogo.style.display = 'block';
            }
        });
    }
});

// Number formatter
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(num);
}
