/**
 * script.js — NEXUM
 * Comportamentos de UI: header ao rolar, menu mobile e validação leve de formulários.
 */
(function () {
    'use strict';

    /* Header muda de aparência ao rolar a página */
    const header = document.getElementById('siteHeader');
    if (header) {
        const onScroll = () => {
            header.classList.toggle('is-scrolled', window.scrollY > 12);
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    /* Menu mobile (hambúrguer) */
    const navToggle = document.getElementById('navToggle');
    const siteNav = document.getElementById('siteNav');

    if (navToggle && siteNav) {
        navToggle.addEventListener('click', () => {
            const isOpen = siteNav.classList.toggle('is-open');
            navToggle.setAttribute('aria-expanded', String(isOpen));
        });

        siteNav.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                siteNav.classList.remove('is-open');
                navToggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    /* Validação client-side leve nos formulários (a validação real acontece em PHP) */
    document.querySelectorAll('.form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const requiredFields = form.querySelectorAll('input[required], input[type="email"], textarea');
            let hasEmpty = false;

            requiredFields.forEach((field) => {
                if (field.hasAttribute('required') && !field.value.trim()) {
                    hasEmpty = true;
                }
            });

            if (hasEmpty) {
                // Deixa o PHP tratar a mensagem de erro real; aqui é só um sinal visual rápido.
                event.target.querySelectorAll('input, textarea').forEach((el) => {
                    if (!el.value.trim() && el.hasAttribute('required')) {
                        el.style.borderColor = '#ff5a36';
                    }
                });
            }
        });
    });

    /* =========================================================================
       Gestão de Tema (Modo Escuro / Modo Claro)
       ========================================================================= */
    window.setTheme = function(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('nexum_theme', theme);
        document.cookie = "nexum_theme=" + theme + ";path=/;max-age=" + (365 * 86400);
        updateThemeUI(theme);
    };

    window.toggleTheme = function() {
        var current = document.documentElement.getAttribute('data-theme') || 'dark';
        var next = current === 'dark' ? 'light' : 'dark';
        window.setTheme(next);
    };

    function updateThemeUI(theme) {
        var icon = document.getElementById('themeToggleIcon');
        if (icon) {
            icon.textContent = theme === 'dark' ? '🌙' : '☀️';
        }
        var toggleBtn = document.getElementById('themeToggleBtn');
        if (toggleBtn) {
            toggleBtn.setAttribute('title', theme === 'dark' ? 'Alternar para Modo Claro' : 'Alternar para Modo Escuro');
        }
        // Se estiver na tela de configurações, atualiza botões
        document.querySelectorAll('[data-theme-choice]').forEach(function(el) {
            el.classList.toggle('is-selected', el.getAttribute('data-theme-choice') === theme);
        });
    }

    // Inicialização ao carregar
    var currentTheme = localStorage.getItem('nexum_theme') || 'dark';
    updateThemeUI(currentTheme);
})();
