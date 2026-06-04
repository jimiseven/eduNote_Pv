document.addEventListener('DOMContentLoaded', () => {
    const firstInvalid = document.querySelector('.is-invalid');

    if (firstInvalid instanceof HTMLElement) {
        firstInvalid.focus();
    }

    const sidebarPanels = Array.from(document.querySelectorAll('.sidebar-group-panel'));
    const sidebarButtons = Array.from(document.querySelectorAll('[data-sidebar-toggle]'));

    sidebarButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-sidebar-toggle');
            const target = targetId ? document.getElementById(targetId) : null;

            if (!target) {
                return;
            }

            const shouldOpen = !target.classList.contains('show');

            sidebarPanels.forEach((panel) => {
                const relatedButton = document.querySelector(`[data-sidebar-toggle="${panel.id}"]`);
                const relatedGroup = panel.closest('.sidebar-group');

                panel.classList.remove('show');
                relatedButton?.classList.add('collapsed');
                relatedButton?.setAttribute('aria-expanded', 'false');
                relatedGroup?.classList.remove('open');
            });

            if (shouldOpen) {
                target.classList.add('show');
                button.classList.remove('collapsed');
                button.setAttribute('aria-expanded', 'true');
                target.closest('.sidebar-group')?.classList.add('open');
            }
        });
    });
});
