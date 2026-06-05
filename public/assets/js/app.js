document.addEventListener('DOMContentLoaded', () => {
    const firstInvalid = document.querySelector('.is-invalid');

    if (firstInvalid instanceof HTMLElement) {
        firstInvalid.focus();
    }

    const sidebarPanels = Array.from(document.querySelectorAll('.sidebar-group-panel'));
    const sidebarButtons = Array.from(document.querySelectorAll('[data-sidebar-toggle]'));
    const sidebarNav = document.getElementById('sidebar-accordion');
    const appContent = document.getElementById('app-content');
    const appPageTitle = document.getElementById('app-page-title');
    let sidebarOpenTimer = null;
    let sidebarSwitchTimer = null;
    let pageAbortController = null;

    const normalizeUrl = (url) => {
        const parsed = new URL(url, window.location.origin);

        return `${parsed.pathname.replace(/\/$/, '') || '/'}${parsed.search}`;
    };

    const setPanelState = (panel, open) => {
        const relatedButton = document.querySelector(`[data-sidebar-toggle="${panel.id}"]`);
        const relatedGroup = panel.closest('.sidebar-group');

        relatedButton?.classList.toggle('collapsed', !open);
        relatedButton?.setAttribute('aria-expanded', open ? 'true' : 'false');
        relatedGroup?.classList.toggle('open', open);
    };

    const openPanel = (panel) => {
        panel.classList.add('show');
        panel.style.maxHeight = '0px';
        setPanelState(panel, true);

        requestAnimationFrame(() => {
            panel.style.maxHeight = `${panel.scrollHeight}px`;
        });
    };

    const closePanel = (panel) => {
        panel.style.maxHeight = `${panel.scrollHeight}px`;
        panel.classList.remove('show');
        setPanelState(panel, false);

        requestAnimationFrame(() => {
            panel.style.maxHeight = '0px';
        });
    };

    const setActiveSidebarLink = (url) => {
        const targetUrl = normalizeUrl(url);
        let activePanel = null;

        sidebarNav?.querySelectorAll('a').forEach((link) => {
            const isActive = normalizeUrl(link.href) === targetUrl;
            link.classList.toggle('active', isActive);

            if (isActive) {
                activePanel = link.closest('.sidebar-group-panel');
            }
        });

        sidebarPanels.forEach((panel) => {
            if (panel === activePanel) {
                if (!panel.classList.contains('show')) {
                    openPanel(panel);
                }
                return;
            }

            if (panel.classList.contains('show')) {
                closePanel(panel);
            }
        });
    };

    const replacePage = (html, url, pushState = true) => {
        const parser = new DOMParser();
        const nextDocument = parser.parseFromString(html, 'text/html');
        const nextContent = nextDocument.getElementById('app-content');
        const nextTitle = nextDocument.getElementById('app-page-title');

        if (!nextContent || !appContent) {
            window.location.href = url;
            return;
        }

        appContent.innerHTML = nextContent.innerHTML;

        if (nextTitle && appPageTitle) {
            appPageTitle.textContent = nextTitle.textContent;
        }

        document.title = nextDocument.title || document.title;
        setActiveSidebarLink(url);

        if (pushState) {
            window.history.pushState({ partial: true }, document.title, url);
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const loadPagePartially = async (url, pushState = true) => {
        if (!appContent) {
            window.location.href = url;
            return;
        }

        pageAbortController?.abort();
        pageAbortController = new AbortController();
        appContent.classList.add('is-loading');

        try {
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'fetch' },
                signal: pageAbortController.signal,
            });

            const contentType = response.headers.get('content-type') || '';
            if (!response.ok || !contentType.includes('text/html')) {
                window.location.href = url;
                return;
            }

            replacePage(await response.text(), url, pushState);
        } catch (error) {
            if (error.name !== 'AbortError') {
                window.location.href = url;
            }
        } finally {
            appContent.classList.remove('is-loading');
        }
    };

    sidebarPanels.forEach((panel) => {
        panel.style.maxHeight = panel.classList.contains('show') ? `${panel.scrollHeight}px` : '0px';
        setPanelState(panel, panel.classList.contains('show'));
    });

    sidebarButtons.forEach((button) => {
        button.addEventListener('click', () => {
            window.clearTimeout(sidebarOpenTimer);
            window.clearTimeout(sidebarSwitchTimer);

            const targetId = button.getAttribute('data-sidebar-toggle');
            const target = targetId ? document.getElementById(targetId) : null;

            if (!target) {
                return;
            }

            const shouldOpen = !target.classList.contains('show');
            const closingAnotherPanel = shouldOpen && sidebarPanels.some((panel) => panel !== target && panel.classList.contains('show'));

            sidebarNav?.classList.add('is-switching');

            sidebarPanels.forEach((panel) => {
                if (panel.classList.contains('show')) {
                    closePanel(panel);
                }
            });

            if (shouldOpen) {
                sidebarOpenTimer = window.setTimeout(() => openPanel(target), closingAnotherPanel ? 170 : 0);
            }

            sidebarSwitchTimer = window.setTimeout(() => {
                sidebarNav?.classList.remove('is-switching');
            }, closingAnotherPanel ? 430 : 260);
        });
    });

    sidebarNav?.addEventListener('click', (event) => {
        const link = event.target instanceof Element ? event.target.closest('a') : null;

        if (!(link instanceof HTMLAnchorElement)) {
            return;
        }

        if (sidebarNav.classList.contains('is-switching')) {
            event.preventDefault();
            return;
        }

        if (normalizeUrl(link.href) === normalizeUrl(window.location.href)) {
            event.preventDefault();
            return;
        }

        event.preventDefault();
        loadPagePartially(link.href);
    });

    window.addEventListener('popstate', () => {
        loadPagePartially(window.location.href, false);
    });

    window.addEventListener('resize', () => {
        sidebarPanels.forEach((panel) => {
            if (panel.classList.contains('show')) {
                panel.style.maxHeight = `${panel.scrollHeight}px`;
            }
        });
    });
});
