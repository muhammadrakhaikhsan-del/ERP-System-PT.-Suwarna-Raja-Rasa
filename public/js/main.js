// ============ SIDEBAR TOGGLE (Expand/Collapse) ============
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const overlay = document.getElementById('mobileOverlay');
    const toggleIcon = document.getElementById('toggleIcon');

    if (!sidebar) return;

    // Mobile
    if (window.innerWidth < 1024) {
        sidebar.classList.toggle('mobile-open');
        if (overlay) overlay.classList.toggle('show');
        return;
    }

    // Desktop: toggle .collapsed class
    const isCollapsed = sidebar.classList.toggle('collapsed');

    // Update toggle icon direction
    if (toggleIcon) {
        toggleIcon.className = isCollapsed ? 'bi bi-chevron-right' : 'bi bi-chevron-left';
    }

    // Adjust main content margin
    if (mainContent) {
        mainContent.style.marginLeft = isCollapsed ? 'calc(72px + 32px)' : 'calc(260px + 32px)';
    }

    // Close all submenus when collapsing
    if (isCollapsed) {
        document.querySelectorAll('.sidebar-submenu.open').forEach(m => m.classList.remove('open'));
        document.querySelectorAll('.sidebar-chevron.open').forEach(c => c.classList.remove('open'));
    }

    localStorage.setItem('sidebarState', isCollapsed ? 'collapsed' : 'expanded');
}

// ============ SIDEBAR DROPDOWN (Expanded mode) ============
function toggleSidebarDropdown(menuId, btn) {
    var sidebar = document.getElementById('sidebar');
    if (!sidebar) return;

    // If collapsed, don't toggle dropdown
    if (sidebar.classList.contains('collapsed')) return;

    var menu = document.getElementById(menuId);
    if (!menu) return;

    var chevron = btn.querySelector('.sidebar-chevron');
    var isOpen = menu.classList.toggle('open');
    if (chevron) chevron.classList.toggle('open', isOpen);
}

// ============ RESTORE SIDEBAR STATE ON LOAD ============
window.addEventListener('DOMContentLoaded', function() {
    var sidebar = document.getElementById('sidebar');
    var mainContent = document.getElementById('mainContent');
    var toggleIcon = document.getElementById('toggleIcon');

    if (sidebar && window.innerWidth >= 1024) {
        var savedState = localStorage.getItem('sidebarState');
        if (savedState === 'collapsed') {
            sidebar.classList.add('collapsed');
            if (toggleIcon) toggleIcon.className = 'bi bi-chevron-right';
            if (mainContent) mainContent.style.marginLeft = 'calc(72px + 32px)';
        } else {
            if (mainContent) mainContent.style.marginLeft = 'calc(260px + 32px)';
        }
    }

    // Auto-open dropdown if a child is active
    document.querySelectorAll('.sidebar-submenu').forEach(function(submenu) {
        if (submenu.querySelector('.active')) {
            submenu.classList.add('open');
            var btn = submenu.previousElementSibling;
            if (btn) {
                var chevron = btn.querySelector('.sidebar-chevron');
                if (chevron) chevron.classList.add('open');
            }
        }
    });

    // ============ GLOBAL TOAST NOTIFICATION ============
    var toast = document.getElementById('globalToast');
    if (toast) {
        // Slide in
        setTimeout(function() {
            toast.classList.remove('translate-x-[150%]', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        }, 100);

        // Auto dismiss after 5s
        setTimeout(function() {
            closeToast();
        }, 5000);
    }
});

function closeToast() {
    var toast = document.getElementById('globalToast');
    if (toast) {
        toast.classList.remove('translate-x-0', 'opacity-100');
        toast.classList.add('translate-x-[150%]', 'opacity-0');
        setTimeout(() => toast.remove(), 500);
        // Clean up session via history state if possible
        if (window.history.replaceState) {
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    }
}

// ============ LEGACY: toggleDropdown (for non-sidebar dropdowns) ============
function toggleDropdown(menuId, btn) {
    var menu = document.getElementById(menuId);
    if (!menu) return;

    var icon = btn.querySelector('.bi-chevron-down');

    if (menu.classList.contains('max-h-0')) {
        menu.classList.remove('max-h-0', 'opacity-0');
        menu.classList.add('max-h-96', 'opacity-100');
        if (icon) icon.classList.add('rotate-180');
    } else {
        menu.classList.remove('max-h-96', 'opacity-100');
        menu.classList.add('max-h-0', 'opacity-0');
        if (icon) icon.classList.remove('rotate-180');
    }
}

// ============ PASSWORD TOGGLE (Login) ============
function togglePassword() {
    var input = document.getElementById('password');
    var eyeOpen = document.getElementById('eyeOpen');
    var eyeClosed = document.getElementById('eyeClosed');

    if (eyeOpen && eyeClosed) {
        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
        return;
    }

    var eyeIcon = document.getElementById('eyeIcon');
    if (!input || !eyeIcon) return;

    if (input.type === 'password') {
        input.type = 'text';
        eyeIcon.classList.remove('bi-eye');
        eyeIcon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        eyeIcon.classList.remove('bi-eye-slash');
        eyeIcon.classList.add('bi-eye');
    }
}

// ============ TOPBAR PROFILE PILL TOGGLE ============
function toggleProfilePill() {
    var textDiv = document.getElementById('profilePillText');
    var pill = document.getElementById('profilePill');
    
    if (textDiv) {
        // Toggle the visibility classes for tailwind
        if (textDiv.classList.contains('sm:block')) {
            textDiv.classList.remove('sm:block');
            textDiv.classList.add('hidden');
            // Remove gap and side padding to make it a perfect circle
            pill.classList.remove('px-5', 'gap-3');
            pill.classList.add('px-2', 'gap-0');
        } else {
            textDiv.classList.add('sm:block');
            textDiv.classList.remove('hidden');
            // Restore gap and padding
            pill.classList.add('px-5', 'gap-3');
            pill.classList.remove('px-2', 'gap-0');
        }
    }
}