function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("mainContent");
    const isMobile = window.innerWidth <= 768;

    if (!sidebar) return;

    // Mobile behavior
    if (isMobile) {
        sidebar.classList.toggle("mobile-active");
        document.body.classList.toggle("sidebar-open");
        handleOverlay();
    } else {
        sidebar.classList.toggle("hidden");
        if (sidebar.classList.contains("hidden")) {
            if (mainContent) mainContent.style.marginLeft = "0";
        } else {
            if (mainContent) mainContent.style.marginLeft = "220px";
        }
        removeOverlay();
    }
}

function handleOverlay() {
    let overlay = document.getElementById('sidebar-overlay');
    const sidebar = document.getElementById("sidebar");
    if (sidebar.classList.contains("mobile-active")) {
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'sidebar-overlay';
            overlay.onclick = toggleSidebar;
            document.body.appendChild(overlay);
        }
        overlay.style.display = 'block';
    } else {
        removeOverlay();
    }
}

function removeOverlay() {
    const overlay = document.getElementById('sidebar-overlay');
    if (overlay) overlay.remove();
}

// Initialize sidebar closed
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("mainContent");
    if (sidebar) {
        sidebar.classList.add('hidden');
        sidebar.classList.remove('mobile-active');
    }
    if (mainContent) mainContent.style.marginLeft = "0";
    document.body.classList.remove('sidebar-open');
    removeOverlay();
});

// Resize handler
window.addEventListener('resize', function() {
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("mainContent");
    if (window.innerWidth > 768) {
        if (sidebar) sidebar.classList.remove('mobile-active');
        document.body.classList.remove('sidebar-open');
        removeOverlay();
    } else {
        if (sidebar && !sidebar.classList.contains("mobile-active")) {
            sidebar.classList.add('hidden');
            if (mainContent) mainContent.style.marginLeft = "0";
        }
    }
});
