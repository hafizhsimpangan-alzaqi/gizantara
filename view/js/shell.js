document.addEventListener('DOMContentLoaded', function () {
    const shell = document.querySelector('.layout-private');
    const sidebar = document.getElementById('private-sidebar');
    const toggle = document.querySelector('[data-sidebar-toggle]');
    const tablet = window.matchMedia('(min-width: 768px) and (max-width: 1023px)');
    let returnFocus = null;

    function syncSidebar() {
        if (!shell || !sidebar || !toggle) return;
        const open = shell.classList.contains('is-sidebar-open');
        sidebar.inert = tablet.matches && !open;
        toggle.setAttribute('aria-expanded', tablet.matches ? String(open) : String(!shell.classList.contains('is-sidebar-collapsed')));
        toggle.setAttribute('aria-label', tablet.matches ? (open ? 'Tutup menu' : 'Buka menu') : (shell.classList.contains('is-sidebar-collapsed') ? 'Perluas menu' : 'Ringkas menu'));
    }

    function closeSidebar() {
        if (!shell) return;
        shell.classList.remove('is-sidebar-open');
        syncSidebar();
        if (returnFocus) returnFocus.focus();
    }

    if (shell && sidebar && toggle) {
        syncSidebar();
        toggle.addEventListener('click', function () {
            if (tablet.matches) {
                returnFocus = toggle;
                shell.classList.add('is-sidebar-open');
                syncSidebar();
                sidebar.querySelector('.sidebar-close').focus();
            } else {
                shell.classList.toggle('is-sidebar-collapsed');
                syncSidebar();
            }
        });
        document.querySelectorAll('[data-sidebar-close]').forEach(function (button) {
            button.addEventListener('click', closeSidebar);
        });
        document.addEventListener('keydown', function (event) {
            if (!tablet.matches || !shell.classList.contains('is-sidebar-open')) return;
            if (event.key === 'Escape') { closeSidebar(); return; }
            if (event.key !== 'Tab') return;
            const items = Array.from(sidebar.querySelectorAll('a, button')).filter(function (item) { return !item.disabled; });
            const first = items[0];
            const last = items[items.length - 1];
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        });
        window.addEventListener('resize', function () {
            shell.classList.remove('is-sidebar-open');
            syncSidebar();
        });
    }

    const region = document.getElementById('toast-region');
    if (!region) return;
    document.querySelectorAll('[data-flash]:not([data-persist="true"])').forEach(function (message) {
        const kind = ['success', 'error', 'warning', 'info'].includes(message.dataset.kind) ? message.dataset.kind : 'info';
        if (kind === 'error' && document.querySelector('form.form-card')) return;
        const toast = document.createElement('div');
        const body = document.createElement('span');
        const close = document.createElement('button');
        let timer;
        toast.className = 'toast toast-' + kind;
        toast.setAttribute('role', kind === 'error' ? 'alert' : 'status');
        body.textContent = message.textContent.trim();
        close.type = 'button';
        close.className = 'toast-close';
        close.setAttribute('aria-label', 'Tutup notifikasi');
        close.textContent = '×';
        function dismiss() {
            clearTimeout(timer);
            toast.classList.add('is-leaving');
            toast.addEventListener('animationend', function () { toast.remove(); }, { once: true });
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) toast.remove();
        }
        close.addEventListener('click', dismiss);
        toast.append(body, close);
        region.append(toast);
        message.remove();
        timer = window.setTimeout(dismiss, 5000);
    });
});
