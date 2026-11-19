initNavigation();
initNavContrast();
initServiceTabs();
initTouchFlip();
initContactEmail();
initScrollFocus();

/**
 * Brings page blocks into focus as they enter the viewport.
 */
function initScrollFocus() {
    const nodes = [];

    /**
     * Marks one element so it can fade in on scroll.
     */
    function add(node, delay) {
        if (!(node instanceof HTMLElement) || node.classList.contains('reveal')) {
            return;
        }

        node.classList.add('reveal');
        node.style.setProperty('--reveal-delay', delay);
        nodes.push(node);
    }

    document.querySelectorAll('.facts li, .flip-card, .timeline li').forEach((node) => {
        const siblings = [...node.parentElement.children];
        add(node, `${siblings.indexOf(node) * 90}ms`);
    });

    document.querySelectorAll(
        '.page-intro, .work-head, .clients, .about-copy, .about-close, .contact-form-wrap, .contact-aside, .service-tabs, .footer-brand'
    ).forEach((node) => add(node, '0ms'));

    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduce || !('IntersectionObserver' in window)) {
        nodes.forEach((node) => node.classList.add('is-inview'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('is-inview');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.08, rootMargin: '0px 0px 4% 0px' });

    nodes.forEach((node) => {
        const box = node.getBoundingClientRect();
        const alreadyVisible = box.top < window.innerHeight * 0.92 && box.bottom > 0;

        if (alreadyVisible) {
            node.classList.add('is-inview');
            return;
        }

        observer.observe(node);
    });

    document.documentElement.classList.add('reveal-ready');
}

/**
 * Opens and closes the navigation on small screens.
 */
function initNavigation() {
    const toggle = document.querySelector('.nav-toggle');
    const links = document.querySelector('.nav-links');

    if (!(toggle instanceof HTMLButtonElement) || !links) {
        return;
    }

    toggle.addEventListener('click', () => {
        const open = links.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    links.addEventListener('click', (event) => {
        if (event.target instanceof HTMLAnchorElement) {
            links.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
}

/**
 * Switches the home navigation to the paper colour once the hero passes behind it.
 */
function initNavContrast() {
    const nav = document.querySelector('.site-nav');
    const hero = document.querySelector('.hero');

    if (!nav || !hero) {
        return;
    }

    /**
     * Paints the navigation as soon as the hero no longer fills the area behind it.
     */
    function updateNavContrast() {
        const bottom = hero.getBoundingClientRect().bottom;
        nav.classList.toggle('is-solid', bottom < nav.offsetHeight + 12);
    }

    updateNavContrast();
    window.addEventListener('scroll', updateNavContrast, { passive: true });
    window.addEventListener('resize', updateNavContrast);
}

/**
 * Shows one service panel at a time and follows the hash from the home-page cards.
 */
function initServiceTabs() {
    const root = document.querySelector('[data-tabs]');

    if (!root) {
        return;
    }

    const tabs = [...root.querySelectorAll('[role="tab"]')];
    const panels = [...root.querySelectorAll('[role="tabpanel"]')];
    const slugs = tabs.map((tab) => tab.dataset.slug || '');

    /**
     * Selects a service tab and optionally writes its slug into the page address.
     */
    function activate(slug, updateHash) {
        tabs.forEach((tab) => {
            const selected = tab.dataset.slug === slug;
            tab.setAttribute('aria-selected', selected ? 'true' : 'false');
            tab.tabIndex = selected ? 0 : -1;
        });

        panels.forEach((panel) => {
            panel.hidden = panel.id !== 'panel-' + slug;
        });

        if (updateHash) {
            history.replaceState(null, '', '#' + slug);
        }
    }

    const fromHash = window.location.hash.replace('#', '');
    activate(slugs.includes(fromHash) ? fromHash : slugs[0], false);

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => activate(tab.dataset.slug || '', true));
    });

    root.addEventListener('keydown', (event) => {
        if (event.key !== 'ArrowRight' && event.key !== 'ArrowLeft') {
            return;
        }

        const current = tabs.findIndex((tab) => tab.getAttribute('aria-selected') === 'true');
        const direction = event.key === 'ArrowRight' ? 1 : -1;
        const next = (current + direction + tabs.length) % tabs.length;
        event.preventDefault();
        tabs[next].focus();
        activate(tabs[next].dataset.slug || '', true);
    });

    window.addEventListener('hashchange', () => {
        const slug = window.location.hash.replace('#', '');
        if (slugs.includes(slug)) {
            activate(slug, false);
        }
    });
}

/**
 * Requires the contact e-mail and checks that it is a real address before sending.
 */
function initContactEmail() {
    const form = document.querySelector('.contact-form');
    const input = document.querySelector('#email');
    const error = document.querySelector('#email-error');

    if (!(form instanceof HTMLFormElement) || !(input instanceof HTMLInputElement) || !error) {
        return;
    }

    /**
     * Shows a message when the address is missing or not shaped like an e-mail.
     */
    function validateEmail() {
        const value = input.value.trim();
        let message = '';

        if (value === '') {
            message = 'Upišite adresu e-pošte.';
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value)) {
            message = 'Upišite ispravnu adresu e-pošte.';
        }

        error.textContent = message;
        error.hidden = message === '';
        input.classList.toggle('is-invalid', message !== '');
        input.setAttribute('aria-invalid', message === '' ? 'false' : 'true');

        return message === '';
    }

    input.addEventListener('input', () => {
        if (input.classList.contains('is-invalid')) {
            validateEmail();
        }
    });

    form.addEventListener('submit', (event) => {
<<<<<<< HEAD
        event.preventDefault();

        if (!validateEmail()) {
            input.focus();
            return;
        }

        const name = document.querySelector('#ime');
        const phone = document.querySelector('#telefon');
        const message = document.querySelector('#poruka');
        const body = [
            name instanceof HTMLInputElement ? 'Ime: ' + name.value.trim() : '',
            'E-pošta: ' + input.value.trim(),
            phone instanceof HTMLInputElement && phone.value.trim() !== '' ? 'Telefon: ' + phone.value.trim() : '',
            '',
            message instanceof HTMLTextAreaElement ? message.value.trim() : '',
        ].filter((line, index, lines) => line !== '' || lines[index - 1] !== '').join('\n');

        window.location.href = 'mailto:info@rijekaprojekt-energetika.hr?subject='
            + encodeURIComponent('Poruka s web stranice')
            + '&body='
            + encodeURIComponent(body);
=======
        if (!validateEmail()) {
            event.preventDefault();
            input.focus();
        }
>>>>>>> b6ee07cb0f2f1ff38ebb2656eaeb49fda62a7b66
    });
}

/**
 * On touch screens, the first tap flips a card and the next tap opens the service.
 */
function initTouchFlip() {
    if (window.matchMedia('(hover: hover)').matches) {
        return;
    }

    document.querySelectorAll('.flip-card').forEach((card) => {
        card.addEventListener('click', (event) => {
            if (!card.classList.contains('is-flipped')) {
                event.preventDefault();
                document.querySelectorAll('.flip-card.is-flipped').forEach((other) => {
                    other.classList.remove('is-flipped');
                });
                card.classList.add('is-flipped');
            }
        });
    });
}
