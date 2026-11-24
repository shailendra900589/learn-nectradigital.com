// Suppress benign third-party CDN dev warnings (Tailwind/Babel) from polluting developer console
(function () {
    ['warn', 'error', 'info', 'log'].forEach(function (m) {
        var orig = console[m];
        if (!orig) return;
        console[m] = function () {
            for (var i = 0; i < arguments.length; i++) {
                var s = String(arguments[i] || '');
                if (
                    s.indexOf('tailwindcss.com') !== -1 ||
                    s.indexOf('Babel transformer') !== -1 ||
                    s.indexOf('in-browser Babel') !== -1
                ) {
                    return;
                }
            }
            orig.apply(console, arguments);
        };
    });

    window.__applyTailwindConfig = function () {
        if (!window.tailwind || window.__twConfigured) return;
        window.__twConfigured = true;
        window.tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        slate: {
                            50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0', 300: '#cbd5e1',
                            400: '#94a3b8', 500: '#64748b', 600: '#475569', 700: '#334155',
                            800: '#1e293b', 900: '#0f172a', 950: '#020617'
                        },
                        emerald: {
                            50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0', 300: '#6ee7b7',
                            400: '#34d399', 500: '#10b981', 600: '#059669', 700: '#047857',
                            800: '#065f46', 900: '#064e3b', 950: '#022c22'
                        }
                    }
                }
            }
        };
    };

    document.addEventListener('DOMContentLoaded', function () {
        window.__applyTailwindConfig();
    });
})();
