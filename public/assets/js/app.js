document.addEventListener('DOMContentLoaded', () => {
    const firstInvalid = document.querySelector('.is-invalid');

    if (firstInvalid instanceof HTMLElement) {
        firstInvalid.focus();
    }

    document.querySelectorAll('.sidebar-group-panel').forEach((panel) => {
        panel.addEventListener('shown.bs.collapse', () => {
            document.querySelector(`[data-bs-target="#${panel.id}"]`)?.classList.remove('collapsed');
        });

        panel.addEventListener('hidden.bs.collapse', () => {
            document.querySelector(`[data-bs-target="#${panel.id}"]`)?.classList.add('collapsed');
        });
    });
});
