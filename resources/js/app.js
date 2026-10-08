import './bootstrap';

function initMobileBottomSheets() {
    document.querySelectorAll('[data-bottom-sheet]').forEach((trigger) => {
        const targetId = trigger.getAttribute('data-bottom-sheet');
        const sheet = document.getElementById(targetId);
        const backdrop = sheet?.querySelector('.bottom-sheet-backdrop');
        const closeBtn = sheet?.querySelector('[data-close-sheet]');

        if (!sheet) return;

        const open = () => {
            sheet.classList.add('open');
            backdrop?.classList.add('open');
            document.body.style.overflow = 'hidden';
        };

        const close = () => {
            sheet.classList.remove('open');
            backdrop?.classList.remove('open');
            document.body.style.overflow = '';
        };

        trigger.addEventListener('click', open);
        closeBtn?.addEventListener('click', close);
        backdrop?.addEventListener('click', close);
    });
}

function initFormAnimations() {
    document.querySelectorAll('form').forEach((form) => {
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach((input, index) => {
            input.style.animationDelay = `${index * 0.05}s`;
            input.classList.add('animate-slide-up');
        });
    });
}

function initCustomSelects() {
    console.log('[custom-select] initCustomSelects running');
    const wrappers = document.querySelectorAll('[data-custom-select]');
    console.log('[custom-select] found wrappers:', wrappers.length);
    wrappers.forEach((wrapper) => {
        const trigger = wrapper.querySelector('.custom-select-trigger');
        const dropdown = wrapper.querySelector('.custom-select-dropdown');
        const options = wrapper.querySelectorAll('.custom-select-option');
        const hiddenInput = wrapper.querySelector('input[type="hidden"]');
        const valueDisplay = wrapper.querySelector('.custom-select-value');
        const arrow = wrapper.querySelector('.custom-select-arrow');

        console.log('[custom-select] wrapper found', { trigger: !!trigger, dropdown: !!dropdown, hiddenInput: !!hiddenInput, options: options.length });

        if (!trigger || !dropdown || !hiddenInput) return;

        const closeAll = () => {
            wrapper.classList.remove('custom-select-open');
            trigger.setAttribute('aria-expanded', 'false');
            if (arrow) arrow.classList.remove('rotate-180');
        };

        const toggle = () => {
            const isOpen = wrapper.classList.contains('custom-select-open');
            closeAll();
            if (!isOpen) {
                wrapper.classList.add('custom-select-open');
                trigger.setAttribute('aria-expanded', 'true');
                if (arrow) arrow.classList.add('rotate-180');
            }
        };

        const select = (value, label) => {
            hiddenInput.value = value;
            if (valueDisplay) valueDisplay.textContent = label || value;
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));

            options.forEach((opt) => {
                const isSelected = opt.getAttribute('data-value') === String(value);
                opt.classList.toggle('bg-[var(--accent-subtle)]', isSelected);
                opt.classList.toggle('text-[var(--accent)]', isSelected);
                opt.classList.toggle('font-medium', isSelected);
                opt.classList.toggle('text-[var(--text)]', !isSelected);
            });

            closeAll();
        };

        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            toggle();
        });

        options.forEach((option) => {
            option.addEventListener('click', () => {
                const value = option.getAttribute('data-value');
                const label = option.textContent.trim();
                select(value, label);
            });
        });

        wrapper.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) {
                closeAll();
            }
        });

        const currentValue = hiddenInput.value;
        if (currentValue) {
            options.forEach((opt) => {
                const isSelected = opt.getAttribute('data-value') === String(currentValue);
                if (isSelected) {
                    opt.classList.add('bg-[var(--accent-subtle)]', 'text-[var(--accent)]', 'font-medium');
                }
            });
        }
    });
}

const initApp = () => {
    initMobileBottomSheets();
    initFormAnimations();
    initCustomSelects();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    initApp();
}

export {};
