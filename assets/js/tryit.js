/**
 * Learn.Nectra — Navigation, Live Search & Interactive Try-It Playground Engine
 * Externalized & lazily mounted to keep View-Source & initial DOM ultra-lean.
 */
(function () {
    'use strict';

    function ensureTryItModal() {
        var existing = document.getElementById('tryItModal');
        if (existing) return existing;

        var modal = document.createElement('div');
        modal.id = 'tryItModal';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('aria-labelledby', 'tryitTitle');
        modal.style.display = 'none';
        modal.innerHTML =
            '<div class="tryit-container">' +
                '<div class="tryit-header">' +
                    '<div class="flex items-center gap-2 sm:gap-3 flex-wrap">' +
                        '<span class="w-3 h-3 rounded-full tryit-indicator inline-block shadow-sm animate-pulse"></span>' +
                        '<span id="tryitTitle" class="tryit-title-text font-black text-xs sm:text-sm tracking-wide text-white">TRY IT YOURSELF</span>' +
                        '<span id="tryitLangBadge" class="bg-slate-800 tryit-accent text-[11px] sm:text-xs px-2.5 py-0.5 rounded font-mono font-bold uppercase border border-slate-700">PHP</span>' +
                        '<span class="hidden sm:inline-flex items-center gap-1 text-[11px] font-semibold tryit-sandbox-badge border px-2 py-0.5 rounded">' +
                            '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>' +
                            'Editable Sandbox' +
                        '</span>' +
                    '</div>' +
                    '<div class="tryit-tabs-mobile">' +
                        '<button type="button" id="tabEditorBtn" onclick="switchTryItMobileTab(\'editor\')" class="tryit-tab-btn active">&lt;/&gt; Code</button>' +
                        '<button type="button" id="tabOutputBtn" onclick="switchTryItMobileTab(\'output\')" class="tryit-tab-btn">&gt; Result</button>' +
                    '</div>' +
                    '<div class="flex items-center gap-2">' +
                        '<button type="button" id="tryitRunBtn" onclick="executeTryItCode()" title="Run code (Ctrl + Enter)" class="tryit-run-button text-white font-black px-3.5 sm:px-4 py-1.5 rounded-lg text-xs tracking-wider uppercase transition flex items-center gap-1.5 shadow-md cursor-pointer">' +
                            '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>' +
                            '<span>Run Code &raquo;</span>' +
                        '</button>' +
                        '<button type="button" id="tryitCopyBtn" onclick="copyTryItCode()" title="Copy current editor code" class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold px-2.5 sm:px-3 py-1.5 rounded-lg text-xs transition flex items-center gap-1 cursor-pointer">' +
                            '<svg class="w-3.5 h-3.5 tryit-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>' +
                            '<span class="hidden sm:inline">Copy Code</span>' +
                        '</button>' +
                        '<button type="button" id="tryitResetBtn" onclick="resetTryItCode()" title="Restore original lesson snippet" class="bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold px-2.5 sm:px-3 py-1.5 rounded-lg text-xs transition flex items-center gap-1 cursor-pointer">' +
                            '<svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>' +
                            '<span class="hidden sm:inline">Reset</span>' +
                        '</button>' +
                        '<button type="button" onclick="closeTryItModal()" title="Close (Esc)" class="text-slate-400 hover:text-white bg-slate-800/80 hover:bg-slate-700 w-8 h-8 rounded-lg flex items-center justify-center text-lg font-bold transition ml-1 cursor-pointer">&times;</button>' +
                    '</div>' +
                '</div>' +
                '<div class="tryit-body">' +
                    '<div class="tryit-editor-pane">' +
                        '<div class="tryit-editor-header">' +
                            '<div class="flex items-center gap-2">' +
                                '<span class="text-white font-bold">Source Code Editor</span>' +
                                '<span class="text-[11px] text-slate-400">(Editable)</span>' +
                            '</div>' +
                            '<div class="flex items-center gap-2">' +
                                '<span class="tryit-run-hint text-[11px] text-slate-400 hidden sm:inline">Ctrl + Enter to run</span>' +
                                '<button type="button" onclick="clearTryItCode()" class="text-[11px] text-slate-400 hover:text-amber-400 transition cursor-pointer">Clear</button>' +
                            '</div>' +
                        '</div>' +
                        '<div class="tryit-editor-wrap">' +
                            '<textarea id="tryitEditor" class="tryit-textarea" spellcheck="false" placeholder="Write or edit code here..."></textarea>' +
                        '</div>' +
                    '</div>' +
                    '<div class="tryit-output-pane">' +
                        '<div class="tryit-output-header">' +
                            '<div class="flex items-center gap-1 bg-slate-900/90 p-1 rounded-lg border border-slate-700/80">' +
                                '<button type="button" id="tryitTabPreviewBtn" onclick="switchTryItOutputView(\'preview\')" class="tryit-view-btn active text-white px-2.5 py-1 rounded text-xs font-bold transition flex items-center gap-1.5 shadow-sm cursor-pointer">' +
                                    '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>' +
                                    '<span>UI Preview</span>' +
                                '</button>' +
                                '<button type="button" id="tryitTabConsoleBtn" onclick="switchTryItOutputView(\'console\')" class="tryit-view-btn text-slate-400 hover:text-slate-200 px-2.5 py-1 rounded text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">' +
                                    '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>' +
                                    '<span>Console <span id="tryitConsoleCount" class="hidden ml-0.5 px-1.5 py-0.2 bg-slate-700 text-[10px] tryit-accent rounded-full font-mono">0</span></span>' +
                                '</button>' +
                                '<button type="button" id="tryitTabSplitBtn" onclick="switchTryItOutputView(\'split\')" class="hidden sm:inline-flex tryit-view-btn text-slate-400 hover:text-slate-200 px-2 py-1 rounded text-xs font-bold transition items-center gap-1 cursor-pointer" title="Split view (UI on top, Console on bottom)">' +
                                    '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>' +
                                    '<span>Split</span>' +
                                '</button>' +
                            '</div>' +
                            '<div class="flex items-center gap-2">' +
                                '<span class="text-xs tryit-accent font-bold hidden md:inline truncate max-w-[160px]" id="tryitStatus" role="status">Ready</span>' +
                                '<button type="button" id="tryitCopyOutputBtn" onclick="copyTryItOutput()" class="text-[11px] text-slate-300 tryit-accent-hover transition flex items-center gap-1 cursor-pointer bg-slate-800 hover:bg-slate-700 px-2 py-1 rounded border border-slate-700">' +
                                    '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>' +
                                    '<span>Copy</span>' +
                                '</button>' +
                            '</div>' +
                        '</div>' +
                        '<div id="tryitOutputContent" class="tryit-output-content flex-1 flex flex-col min-h-0 relative">' +
                            '<iframe id="tryitFrame" class="tryit-output-frame"></iframe>' +
                            '<pre id="tryitConsole" class="tryit-console hidden"></pre>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

        document.body.appendChild(modal);

        window.addEventListener('resize', function () {
            var open = document.getElementById('tryItModal');
            if (!open || !open.classList.contains('active')) return;
            var editorPane = document.querySelector('.tryit-editor-pane');
            var outputPane = document.querySelector('.tryit-output-pane');
            if (window.innerWidth <= 768) {
                var editorHidden = editorPane && editorPane.classList.contains('mobile-hidden');
                var outputHidden = outputPane && outputPane.classList.contains('mobile-hidden');
                if (!editorHidden && !outputHidden) switchTryItMobileTab('editor');
            } else {
                editorPane && editorPane.classList.remove('mobile-hidden');
                outputPane && outputPane.classList.remove('mobile-hidden');
            }
        });

        var tryitEditor = document.getElementById('tryitEditor');
        if (tryitEditor) {
            tryitEditor.addEventListener('keydown', function (e) {
                if (e.key === 'Tab') {
                    e.preventDefault();
                    var start = this.selectionStart;
                    var end = this.selectionEnd;
                    this.value = this.value.substring(0, start) + '    ' + this.value.substring(end);
                    this.selectionStart = this.selectionEnd = start + 4;
                } else if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    e.preventDefault();
                    executeTryItCode();
                }
            });
        }

        // Force layout reflow so initial CSS transition (.active) animates smoothly
        void modal.offsetWidth;
        return modal;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileBackdrop = document.getElementById('mobileMenuBackdrop');
        const mobileToggle = document.getElementById('mobileMenuToggle');
        const mobileClose = document.getElementById('mobileMenuCloseButton');
        const mobileOpenIcon = document.getElementById('mobileMenuOpenIcon');
        const mobileCloseIcon = document.getElementById('mobileMenuCloseIcon');
        const mobileSearchInput = document.getElementById('mobileSearchInput');
        let timeout = null;

        function setMobileMenu(open) {
            if (!mobileMenu || !mobileBackdrop || !mobileToggle) return;
            const wasOpen = mobileToggle.getAttribute('aria-expanded') === 'true';
            mobileMenu.inert = !open;
            for (const selector of ['#siteHeader', '#main-content', '.nectra-footer', '.skip-link']) {
                const element = document.querySelector(selector);
                if (element) element.inert = open;
            }

            if (open) {
                mobileBackdrop.classList.remove('hidden');
                void mobileMenu.offsetWidth;
                mobileMenu.classList.remove('translate-x-full');
                document.body.style.overflow = 'hidden';
            } else {
                mobileMenu.classList.add('translate-x-full');
                mobileBackdrop.classList.add('hidden');
                document.body.style.overflow = '';
            }
            mobileToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            mobileOpenIcon?.classList.toggle('hidden', open);
            mobileCloseIcon?.classList.toggle('hidden', !open);
            if (open) mobileClose?.focus();
            else if (wasOpen && mobileToggle.getClientRects().length) mobileToggle.focus();

        }
        window.setMobileMenu = setMobileMenu;
        const desktopMenu = window.matchMedia('(min-width: 1024px)');
        desktopMenu.addEventListener('change', (event) => {
            if (event.matches && mobileToggle?.getAttribute('aria-expanded') === 'true') setMobileMenu(false);
        });
        mobileMenu?.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => setMobileMenu(false));
        });

        mobileToggle?.addEventListener('click', () => setMobileMenu(mobileMenu.classList.contains('translate-x-full')));
        mobileClose?.addEventListener('click', () => setMobileMenu(false));
        mobileBackdrop?.addEventListener('click', () => setMobileMenu(false));

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && mobileMenu && !mobileMenu.classList.contains('translate-x-full')) {
                setMobileMenu(false);
            }
        });

        if (mobileSearchInput) {
            mobileSearchInput.addEventListener('keyup', function (e) {
                if (e.key === 'Enter' && this.value.trim().length > 0) {
                    window.location.href = `${window.NECTRA_LANG_PREFIX || ''}search?q=${encodeURIComponent(this.value.trim())}`;
                }
            });
        }

        if (searchInput && searchResults) {
            searchInput.addEventListener('keyup', function (e) {
                if (e.key === 'Enter' && this.value.trim().length > 0) {
                    window.location.href = `${window.NECTRA_LANG_PREFIX || ''}search?q=${encodeURIComponent(this.value.trim())}`;
                    return;
                }
                clearTimeout(timeout);
                const query = this.value.trim();

                if (query.length > 0) {
                    timeout = setTimeout(() => {
                        fetch(`search_ajax?q=${encodeURIComponent(query)}${window.NECTRA_LANG && window.NECTRA_LANG !== 'en' ? '&lang=' + encodeURIComponent(window.NECTRA_LANG) : ''}`)
                            .then(response => response.text())
                            .then(html => {
                                searchResults.innerHTML = html;
                                searchResults.classList.remove('hidden');
                            });
                    }, 300);
                } else {
                    searchResults.classList.add('hidden');
                    searchResults.innerHTML = '';
                }
            });

            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add('hidden');
                }
            });

            searchInput.addEventListener('focus', function () {
                if (this.value.trim().length > 0 && searchResults.innerHTML.trim() !== '') {
                    searchResults.classList.remove('hidden');
                }
            });
        }

        const header = document.getElementById('siteHeader');
        if (header) {
            window.addEventListener('scroll', function () {
                if (window.scrollY > 20) {
                    header.classList.add('header-scrolled');
                } else {
                    header.classList.remove('header-scrolled');
                }
            }, { passive: true });
        }

        const progressBar = document.getElementById('progressBar');
        if (progressBar) {
            window.addEventListener('scroll', function () {
                const scrollTop = window.scrollY;
                const docHeight = document.documentElement.scrollHeight - window.innerHeight;
                if (docHeight > 0) {
                    progressBar.style.width = (scrollTop / docHeight * 100) + '%';
                }
            }, { passive: true });
        }
    });

    // Global Try-It Playground Functions & Enhanced Controls
    let currentTryItLang = 'php';
    window.tryItOriginalCode = '';

    function decodeTryItEntities(str) {
        if (!str || typeof str !== 'string') return '';
        if (!str.includes('&')) return str;
        const txt = document.createElement('textarea');
        txt.innerHTML = str;
        return txt.value;
    }

    function isDirectoryTree(text) {
        if (!text || typeof text !== 'string') return false;
        return text.includes('├──') || text.includes('└──') || text.includes('│') ||
               text.includes('├─') || text.includes('└─') || text.includes('├') || text.includes('└') ||
               /[\u2500-\u257F]/.test(text) ||
               (/^\s*[a-zA-Z0-9_.-]+\/\s*$/m.test(text) && (text.includes('app/') || text.includes('src/') || text.includes('lib/') || text.includes('package.json') || text.includes('pubspec.yaml')));
    }
    window.isDirectoryTree = isDirectoryTree;

    function isDockerfile(text) {
        return /^\s*FROM\s+\S+/m.test(text) &&
               /^\s*(?:WORKDIR|RUN|COPY|ADD|EXPOSE|ENTRYPOINT|CMD|ENV|ARG)\s+/m.test(text);
    }

    function looksLikeProgram(text) {
        if (!text) return false;
        return /^\s*(?:import\s+|from\s+\S+\s+import\s+|def\s+|class\s+|async\s+def\s+|function\s+|const\s+|let\s+|var\s+|export\s+|public\s+|private\s+|protected\s+|internal\s+|#include\b|using\s+|package\s+\S|fn\s+|func\s+|Console\.|<\?php|<\?xml|<html\b|<script\b|print\s*\(|echo\s+|printf\s*\()/m.test(text) ||
               /^\s*(?:int|double|float|char|bool|string|void|decimal|long)\s+[A-Za-z_*]/m.test(text);
    }

    function isRunnableProgramLang(lang) {
        return /^(?:python|php|javascript|typescript|c|csharp|cs|sql|mysql|postgresql|postgres|mongodb|laravel|codeigniter|html|css|react|jsx|angular|angularjs|next|nextjs|nodejs|flutter|dart)$/.test(String(lang || '').toLowerCase());
    }

    function isTerminalCommand(text) {
        if (!text || typeof text !== 'string') return false;
        if (isDirectoryTree(text)) return false;
        if (isDockerfile(text)) return true;
        // A program that mentions a tool in a string or assigns a variable
        // named node/python/cd is not a shell script. "node = ready" is Python.
        if (looksLikeProgram(text)) return false;
        if (/^\s*(SELECT|WITH|INSERT|UPDATE|DELETE|CREATE|ALTER|DROP|EXPLAIN|SHOW|PRAGMA|GRANT|REVOKE)\b/im.test(text)) return false;
        if (/^\s*(?:db\.\w+|use\s+\w+)/m.test(text)) return false;
        if (/^\s*\$\s+\S/m.test(text)) return true;
        if (/^\s*(?:datasource\s+db\s*\{|generator\s+client\s*\{|model\s+[A-Z][a-zA-Z0-9_]*\s*\{)/m.test(text)) return true;
        // The word must be a command plus an argument, not an assignment or a field.
        return /^\s*(?:npm|npx|pnpm|yarn|composer|flutter|dart)\s+(?![=:[{])\S+/m.test(text) ||
               /^\s*(?:cd|git|docker|mkdir|curl|pip3?|python3?|node)\s+(?![=:[{.(])\S+/m.test(text);
    }
    window.isTerminalCommand = isTerminalCommand;

    function detectTryItLang(rawCode, className, activeCourseSlug) {
        const code = String(rawCode || '');
        const slug = String(activeCourseSlug || '');
        if (isDirectoryTree(code)) return 'tree';
        const isReactEnv = (slug && (slug.includes('react') || slug.includes('next'))) ||
            window.location.pathname.includes('react') || window.location.pathname.includes('next');
        let lang = isReactEnv ? 'react' : 'php';
        if (slug) {
            if (slug.includes('flutter') || slug.includes('dart')) lang = 'flutter';
            else if (slug.includes('laravel')) lang = 'laravel';
            else if (slug.includes('codeigniter')) lang = 'codeigniter';
            else if (slug.includes('mongo')) lang = 'mongodb';
            else if (slug.includes('postgres')) lang = 'postgresql';
            else if (slug.includes('mysql')) lang = 'mysql';
            else if (slug.includes('csharp') || slug === 'c-sharp-tutorial') lang = 'csharp';
            else if (slug === 'c-tutorial' || slug.startsWith('c-programming')) lang = 'c';
            else if (slug.includes('typescript')) lang = 'typescript';
            else if (slug.includes('node') || slug.includes('express')) lang = 'nodejs';
            else if (slug.includes('dotnet') || slug.includes('aspnet')) lang = 'csharp';
            else if (slug.includes('ai-') || slug.includes('dsa-python') || slug.includes('python')) lang = 'python';
            else if (slug.includes('seo') || slug.includes('marketing') || slug.includes('excel')) lang = 'html';
            else if (slug.includes('devops')) lang = 'bash';
            else if (slug.includes('next')) lang = 'next';
            else if (slug.includes('react')) lang = 'react';
            else if (slug.includes('angular')) lang = 'angular';
            else if (slug.includes('javascript') || slug.includes('js')) lang = 'javascript';
            else if (slug.includes('html')) lang = 'html';
            else if (slug.includes('css')) lang = 'css';
            else if (slug.includes('sql')) lang = 'sql';
        }
        const classStr = String(className || '').toLowerCase();
        if (classStr.includes('flutter') || classStr.includes('dart')) lang = 'flutter';
        else if (classStr.includes('laravel')) lang = 'laravel';
        else if (classStr.includes('codeigniter')) lang = 'codeigniter';
        else if (classStr.includes('mongodb') || classStr.includes('mongosh')) lang = 'mongodb';
        else if (classStr.includes('postgres') || classStr.includes('pgsql')) lang = 'postgresql';
        else if (classStr.includes('mysql')) lang = 'mysql';
        else if (classStr.includes('language-csharp') || /\blanguage-cs\b/.test(classStr)) lang = 'csharp';
        else if (/\blanguage-c\b/.test(classStr)) lang = 'c';
        else if (classStr.includes('typescript') || /\blanguage-ts\b/.test(classStr)) lang = 'typescript';
        else if (classStr.includes('nodejs')) lang = 'nodejs';
        else if (classStr.includes('next')) lang = 'next';
        else if (classStr.includes('react') || classStr.includes('jsx')) lang = 'react';
        else if (classStr.includes('angular')) lang = 'angular';
        else if (/\blanguage-(?:bash|sh|shell|docker|dockerfile|ya?ml)\b/.test(classStr)) lang = 'bash';
        else if (classStr.includes('python')) lang = 'python';
        else if (classStr.includes('json')) lang = code.includes('"@context"') ? 'html' : 'javascript';
        else if (classStr.includes('javascript') || /\blanguage-js\b/.test(classStr)) lang = isReactEnv ? 'react' : (slug.includes('node') ? 'nodejs' : 'javascript');
        else if (classStr.includes('html') || classStr.includes('xml')) lang = 'html';
        else if (classStr.includes('css')) lang = 'css';
        else if (classStr.includes('sql')) lang = slug.includes('postgres') ? 'postgresql' : (slug.includes('mysql') ? 'mysql' : 'sql');
        else if (classStr.includes('php')) lang = slug.includes('laravel') ? 'laravel' : (slug.includes('codeigniter') ? 'codeigniter' : 'php');
        if (isTerminalCommand(code) && !looksLikeProgram(code)) {
            const classIsProgram = /\blanguage-(?:python|php|javascript|typescript|csharp|sql|mysql|postgres|pgsql|mongodb|laravel|codeigniter|html|css|react|jsx|angular|next|nodejs|flutter|dart)\b/.test(classStr)
                || /\blanguage-c\b/.test(classStr);
            if (!classIsProgram) lang = 'bash';
        }
        return lang;
    }
    window.detectTryItLang = detectTryItLang;

    function shouldFilterLog(str) {
        if (!str) return true;
        var s = String(str).trim();
        var sCompact = s.replace(/\s+/g, '');
        return s === "" ||
               sCompact === "{}" ||
               sCompact === "[]" ||
               sCompact === "null" ||
               sCompact === "undefined" ||
               s === "[object Object]" ||
               s === "Script error." ||
               s.toLowerCase().includes("script error") ||
               s.indexOf("ResizeObserver") !== -1 ||
               s.indexOf("tailwindcss.com") !== -1 ||
               s.indexOf("Babel transformer") !== -1 ||
               s.indexOf("in-browser Babel") !== -1 ||
               s.indexOf("Failed to execute 'write' on 'Document'") !== -1 ||
               s.indexOf("already been declared") !== -1 ||
               s.indexOf("Floto Widget") !== -1 ||
               s.indexOf("jgnhj6hn8") !== -1;
    }
    window.shouldFilterLog = shouldFilterLog;

    let lastTryItScrollPosition = 0;
    let tryitConsoleLogs = [];
    window.currentTryItOutputView = 'preview';

    function addTryItLog(level, message) {
        const msg = String(message || '');
        if (shouldFilterLog(msg)) {
            return;
        }
        const timestamp = new Date().toLocaleTimeString();
        const prefix = level === 'error' ? '❌ [ERROR]' : (level === 'warn' ? '⚠️ [WARN]' : 'ℹ️ [LOG]');
        const logLine = `${prefix} [${timestamp}] ${msg}`;
        tryitConsoleLogs.push(logLine);

        const consoleBox = document.getElementById('tryitConsole');
        if (consoleBox) {
            consoleBox.textContent = tryitConsoleLogs.join('\n\n');
            consoleBox.scrollTop = consoleBox.scrollHeight;
        }

        const countBadge = document.getElementById('tryitConsoleCount');
        if (countBadge) {
            countBadge.innerText = tryitConsoleLogs.length;
            countBadge.classList.remove('hidden');
        }
    }

    window.addEventListener('message', function (e) {
        const activeFrame = document.getElementById('tryitFrame');
        if (e.data?.type === 'TRYIT_LOG' && (e.source === activeFrame?.contentWindow || e.source === pendingPreview?.contentWindow)) {
            const text = String(e.data.text || '');
            if (shouldFilterLog(text)) {
                return;
            }
            addTryItLog(e.data.logType || 'log', text);
        }
    });

    function escapePreviewHtml(value) {
        return String(value == null ? '' : value).replace(/[&<>"']/g, function (ch) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[ch];
        });
    }

    function parseAsciiTable(block) {
        const lines = String(block).split('\n');
        const sep = /^\+[-+]+\+$/;
        if (!lines.some(function (line) { return sep.test(line.trim()); })) return null;
        const cells = lines
            .map(function (line) { return line.trim(); })
            .filter(function (line) { return line.charAt(0) === '|' && line.charAt(line.length - 1) === '|'; })
            .map(function (line) {
                return line.slice(1, -1).split('|').map(function (cell) { return cell.trim(); });
            });
        if (!cells.length || !cells[0].length) return null;
        return { headers: cells[0], rows: cells.slice(1) };
    }

    function buildExecutionPreview(output, langLabel, state) {
        const text = String(output || '').trim() || 'Code executed successfully. No output was produced.';
        const blocks = text.split(/\n{2,}/);
        let body = '';
        blocks.forEach(function (block) {
            const table = parseAsciiTable(block);
            if (table) {
                const head = table.headers.map(function (h) { return '<th>' + escapePreviewHtml(h) + '</th>'; }).join('');
                const rows = table.rows.length
                    ? table.rows.map(function (row) {
                        return '<tr>' + table.headers.map(function (_, i) {
                            return '<td>' + escapePreviewHtml(row[i] == null ? '' : row[i]) + '</td>';
                        }).join('') + '</tr>';
                    }).join('')
                    : '<tr><td class="empty" colspan="' + table.headers.length + '">No matching rows</td></tr>';
                body += '<section class="card"><div class="meta">' + table.rows.length + ' row' + (table.rows.length === 1 ? '' : 's') + ' returned</div><div class="scroll"><table><thead><tr>' + head + '</tr></thead><tbody>' + rows + '</tbody></table></div></section>';
            } else {
                const kind = /^\s*[\[{]/.test(block) ? 'json' : 'plain';
                body += '<pre class="' + kind + '">' + escapePreviewHtml(block) + '</pre>';
            }
        });
        const tone = state === 'error' ? 'bad' : (state === 'run' ? 'run' : 'ok');
        const title = state === 'error' ? 'Execution error' : (state === 'run' ? 'Running sandbox' : state === 'info' ? 'Instructions' : state === 'unavailable' ? 'Runtime unavailable' : 'Live result');
        return '<!DOCTYPE html><html><head><meta charset="utf-8"><style>' +
            'body{margin:0;background:#f7f5fb;color:#1c1931;font-family:Segoe UI,system-ui,sans-serif}' +
            '.wrap{padding:16px 18px 22px}' +
            '.bar{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:12px}' +
            '.badge{font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;border-radius:999px;padding:4px 10px;border:1px solid #cfc1ea;background:#eee8fa;color:#5834b4}' +
            '.badge.bad{color:#b91c1c;background:#fef2f2;border-color:#fecaca}' +
            '.badge.run{color:#1d4ed8;background:#eff6ff;border-color:#bfdbfe}' +
            '.lang{font-size:11px;font-weight:700;color:#475569}' +
            '.card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,.05);margin-bottom:12px}' +
            '.meta{padding:8px 12px;font-size:12px;font-weight:700;color:#64748b;border-bottom:1px solid #e2e8f0;background:#f8fafc}' +
            '.scroll{overflow:auto;max-height:420px}' +
            'table{border-collapse:collapse;width:100%;font-size:13px}' +
            'th{text-align:left;background:#0f172a;color:#e2e8f0;padding:8px 10px;font-weight:700;white-space:nowrap;position:sticky;top:0}' +
            'td{padding:8px 10px;border-bottom:1px solid #e2e8f0;white-space:nowrap}' +
            'tbody tr:nth-child(even) td{background:#f8fafc}' +
            'td.empty{text-align:center;color:#64748b;padding:28px}' +
            'pre{margin:0 0 12px;padding:14px;border-radius:12px;font-size:12.5px;line-height:1.55;white-space:pre-wrap;overflow:auto}' +
            'pre.plain{background:#0f172a;color:#e2e8f0}' +
            'pre.json{background:#082f49;color:#a5f3fc}' +
            '</style></head><body><div class="wrap"><div class="bar"><span class="badge ' + tone + '">' + title + '</span><span class="lang">' + escapePreviewHtml(langLabel || 'Sandbox') + '</span></div>' + body + '</div></body></html>';
    }

    let pendingPreview = null;

    function discardPendingPreview() {
        pendingPreview?.remove();
        pendingPreview = null;
    }

    function setTryItPreview(html) {
        const oldFrame = document.getElementById('tryitFrame');
        if (!oldFrame) return;
        discardPendingPreview();

        const frame = document.createElement('iframe');
        frame.className = 'tryit-output-frame tryit-preview-pending';
        frame.title = 'UI Preview';
        frame.setAttribute('sandbox', 'allow-scripts');
        frame.setAttribute('aria-hidden', 'true');
        frame.tabIndex = -1;
        frame.addEventListener('load', () => {
            if (pendingPreview !== frame) return;
            pendingPreview = null;
            const hidden = oldFrame.classList.contains('hidden');
            oldFrame.remove();
            frame.id = 'tryitFrame';
            frame.classList.remove('tryit-preview-pending');
            frame.classList.toggle('hidden', hidden);
            frame.removeAttribute('aria-hidden');
            frame.removeAttribute('tabindex');
            window.__tryitPreviewHtml = html;
        }, { once: true });
        pendingPreview = frame;
        frame.srcdoc = html;
        oldFrame.before(frame);
    }

    function previewLangLabel() {
        const badge = document.getElementById('tryitLangBadge');
        return (badge && badge.innerText) ? badge.innerText : String(currentTryItLang || 'sandbox').toUpperCase();
    }

    function switchTryItOutputView(view) {
        window.currentTryItOutputView = view;
        const frame = document.getElementById('tryitFrame');
        const consoleBox = document.getElementById('tryitConsole');
        const outputContent = document.getElementById('tryitOutputContent');
        const btnPreview = document.getElementById('tryitTabPreviewBtn');
        const btnConsole = document.getElementById('tryitTabConsoleBtn');
        const btnSplit = document.getElementById('tryitTabSplitBtn');

        [btnPreview, btnConsole, btnSplit].forEach(b => {
            if (!b) return;
            b.classList.remove('active', 'text-white', 'shadow-sm');
            b.classList.add('text-slate-400');
        });

        if (view === 'preview') {
            outputContent?.classList.remove('split-mode');
            frame?.classList.remove('hidden');
            consoleBox?.classList.add('hidden');
            btnPreview?.classList.add('active', 'text-white', 'shadow-sm');
            btnPreview?.classList.remove('text-slate-400');
        } else if (view === 'console') {
            outputContent?.classList.remove('split-mode');
            frame?.classList.add('hidden');
            consoleBox?.classList.remove('hidden');
            btnConsole?.classList.add('active', 'text-white', 'shadow-sm');
            btnConsole?.classList.remove('text-slate-400');
        } else if (view === 'split') {
            outputContent?.classList.add('split-mode');
            frame?.classList.remove('hidden');
            consoleBox?.classList.remove('hidden');
            btnSplit?.classList.add('active', 'text-white', 'shadow-sm');
            btnSplit?.classList.remove('text-slate-400');
        }
    }
    window.switchTryItOutputView = switchTryItOutputView;

    function renderFlutterSimulatorHtml(code) {
        let appTitle = 'Flutter Demo App';
        const titleMatch = code.match(/AppBar\s*\(\s*title:\s*(?:const\s*)?Text\s*\(\s*['"](.*?)['"]\s*\)/i) ||
                           code.match(/title:\s*const\s*Text\s*\(\s*['"](.*?)['"]\s*\)/i) ||
                           code.match(/title:\s*['"](.*?)['"]/i);
        if (titleMatch) {
            appTitle = titleMatch[1];
        }

        let primaryColor = '#0284c7';
        if (code.includes('Colors.indigo') || code.includes('0xFF6366F1')) primaryColor = '#4f46e5';
        else if (code.includes('Colors.deepPurple')) primaryColor = '#7c3aed';
        else if (code.includes('Colors.emerald') || code.includes('Colors.green')) primaryColor = '#059669';
        else if (code.includes('Colors.orange')) primaryColor = '#ea580c';
        else if (code.includes('Colors.red')) primaryColor = '#dc2626';

        const hasForm = code.includes('TextFormField') || code.includes('TextField') || code.includes('Form(');
        const hasCatalog = code.includes('catalog') || code.includes('cart') || code.includes('StoreMainScreen') || code.includes('E-Commerce') || code.includes('NectraStore');
        const hasList = code.includes('ListView') || code.includes('transactions') || code.includes('ListTile') || code.includes('SliverList');
        const hasCustomPaint = code.includes('CustomPaint') || code.includes('CustomPainter') || code.includes('ProgressRingPainter');
        const hasAnimation = code.includes('AnimatedContainer') || code.includes('AnimationController') || code.includes('SingleTickerProviderStateMixin');

        let bodyContent = '';
        let showFab = !hasForm && !hasCustomPaint && !hasAnimation;

        if (hasCatalog) {
            bodyContent = `
                <div class="p-3">
                    <div class="flex items-center justify-between mb-3 px-1">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Store Catalog</span>
                        <span class="text-[11px] font-bold text-sky-600 bg-sky-50 px-2 py-0.5 rounded-full">4 Items In Stock</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3" id="productGrid">
                        <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="h-20 bg-slate-50 rounded-xl flex items-center justify-center text-sky-600 mb-2">
                                    <span class="material-symbols-outlined text-3xl">laptop_mac</span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Laptops</div>
                                <div class="text-xs font-bold text-slate-800 truncate">MacBook Pro M3</div>
                            </div>
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-50">
                                <span class="text-xs font-black text-sky-600">$2,499</span>
                                <button onclick="addToCart('MacBook Pro M3')" class="w-7 h-7 rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-600 hover:text-white flex items-center justify-center transition">
                                    <span class="material-symbols-outlined text-sm">add</span>
                                </button>
                            </div>
                        </div>
                        <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="h-20 bg-slate-50 rounded-xl flex items-center justify-center text-sky-600 mb-2">
                                    <span class="material-symbols-outlined text-3xl">headphones</span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Audio</div>
                                <div class="text-xs font-bold text-slate-800 truncate">Studio Headphones</div>
                            </div>
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-50">
                                <span class="text-xs font-black text-sky-600">$349</span>
                                <button onclick="addToCart('Studio Headphones')" class="w-7 h-7 rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-600 hover:text-white flex items-center justify-center transition">
                                    <span class="material-symbols-outlined text-sm">add</span>
                                </button>
                            </div>
                        </div>
                        <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="h-20 bg-slate-50 rounded-xl flex items-center justify-center text-sky-600 mb-2">
                                    <span class="material-symbols-outlined text-3xl">tv</span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Monitors</div>
                                <div class="text-xs font-bold text-slate-800 truncate">4K OLED Display</div>
                            </div>
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-50">
                                <span class="text-xs font-black text-sky-600">$799</span>
                                <button onclick="addToCart('4K OLED Display')" class="w-7 h-7 rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-600 hover:text-white flex items-center justify-center transition">
                                    <span class="material-symbols-outlined text-sm">add</span>
                                </button>
                            </div>
                        </div>
                        <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="h-20 bg-slate-50 rounded-xl flex items-center justify-center text-sky-600 mb-2">
                                    <span class="material-symbols-outlined text-3xl">keyboard</span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Accessories</div>
                                <div class="text-xs font-bold text-slate-800 truncate">Mechanical USB-C</div>
                            </div>
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-50">
                                <span class="text-xs font-black text-sky-600">$129</span>
                                <button onclick="addToCart('Mechanical USB-C')" class="w-7 h-7 rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-600 hover:text-white flex items-center justify-center transition">
                                    <span class="material-symbols-outlined text-sm">add</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else if (hasForm) {
            bodyContent = `
                <div class="p-5 flex flex-col justify-center min-h-[420px]">
                    <div class="mb-6 text-center">
                        <div class="w-14 h-14 bg-sky-50 text-sky-600 rounded-2xl mx-auto flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-3xl">lock</span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-lg">Secure Account Login</h3>
                        <p class="text-xs text-slate-500 mt-1">Form validation powered by GlobalKey&lt;FormState&gt;</p>
                    </div>
                    <form onsubmit="handleFormSubmit(event)" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Email Address</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-lg">mail</span>
                                <input type="email" id="formEmail" value="sophia.vance@nectradigital.com" required class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none" placeholder="name@domain.com" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Password</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-lg">key</span>
                                <input type="password" id="formPassword" value="••••••••••••" required class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none" placeholder="Enter password" />
                            </div>
                        </div>
                        <div id="formFeedback" class="hidden text-xs text-emerald-600 font-bold bg-emerald-50 p-2.5 rounded-lg border border-emerald-200 text-center"></div>
                        <button type="submit" class="w-full py-3 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl text-xs shadow-md shadow-sky-600/20 transition active:scale-98">
                            Validate & Sign In
                        </button>
                    </form>
                </div>
            `;
        } else if (hasCustomPaint) {
            bodyContent = `
                <div class="p-6 flex flex-col items-center justify-center min-h-[420px]">
                    <div class="relative w-48 h-48 flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90">
                            <circle cx="96" cy="96" r="80" stroke="#e2e8f0" stroke-width="14" fill="transparent" />
                            <circle id="progressArc" cx="96" cy="96" r="80" stroke="#0284c7" stroke-width="14" stroke-linecap="round" fill="transparent" stroke-dasharray="502.6" stroke-dashoffset="125.6" class="transition-all duration-700" />
                        </svg>
                        <div class="absolute flex flex-col items-center">
                            <span id="progressText" class="text-3xl font-black text-sky-600">75%</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Completed</span>
                        </div>
                    </div>
                    <div class="mt-8 flex gap-3">
                        <button onclick="updateProgress(0.25)" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700">25%</button>
                        <button onclick="updateProgress(0.50)" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700">50%</button>
                        <button onclick="updateProgress(0.75)" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-sky-600 text-white">75%</button>
                        <button onclick="updateProgress(1.00)" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700">100%</button>
                    </div>
                </div>
            `;
        } else if (hasAnimation) {
            bodyContent = `
                <div class="p-6 flex flex-col items-center justify-center min-h-[420px]">
                    <div id="animatedBox" class="w-32 h-32 bg-indigo-600 rounded-2xl shadow-xl flex items-center justify-center text-white transition-all duration-500 ease-in-out cursor-pointer" onclick="toggleAnimation()">
                        <span class="material-symbols-outlined text-4xl animate-pulse">flutter_dash</span>
                    </div>
                    <div class="mt-8 text-center">
                        <div class="text-xs font-bold text-slate-700" id="animLabel">Default State (120x120)</div>
                        <button onclick="toggleAnimation()" class="mt-4 px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl shadow transition">
                            Trigger Animation
                        </button>
                    </div>
                </div>
            `;
        } else if (hasList) {
            bodyContent = `
                <div class="divide-y divide-slate-100">
                    <div class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-lg">receipt_long</span>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">Pro Developer Subscription</div>
                                <div class="text-[10px] text-slate-400">Monthly renewal • Stripe API</div>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-emerald-600">+$29.00</span>
                    </div>
                    <div class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-lg">cloud_done</span>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">AWS Cloud Compute & Storage</div>
                                <div class="text-[10px] text-slate-400">EC2 & S3 storage cluster</div>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-slate-600">-$45.20</span>
                    </div>
                    <div class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-lg">payments</span>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">Direct Client Payout</div>
                                <div class="text-[10px] text-slate-400">Mobile app milestone 2</div>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-emerald-600">+$1,240.00</span>
                    </div>
                    <div class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-lg">vpn_key</span>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">Apple Developer Program</div>
                                <div class="text-[10px] text-slate-400">Annual enterprise license</div>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-slate-600">-$99.00</span>
                    </div>
                </div>
            `;
        } else {
            bodyContent = `
                <div class="p-6 flex flex-col items-center justify-center min-h-[420px] text-center">
                    <div class="w-16 h-16 bg-sky-50 text-sky-600 rounded-2xl flex items-center justify-center mb-4 shadow-sm">
                        <span class="material-symbols-outlined text-3xl">flutter_dash</span>
                    </div>
                    <h4 class="text-xs font-semibold text-slate-500 max-w-[220px]">You have pushed the button this many times:</h4>
                    <div id="counterValue" class="text-6xl font-black text-sky-600 my-4 tracking-tight transition-transform duration-200 scale-100">0</div>
                    <div class="flex gap-2 mt-2">
                        <button onclick="decrementCounter()" class="px-3.5 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 active:scale-95 transition">- Decrement</button>
                        <button onclick="resetCounter()" class="px-3.5 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 active:scale-95 transition">Reset</button>
                    </div>
                </div>
            `;
        }

        return '<!DOCTYPE html>' +
'<html>' +
'<head>' +
'    <meta charset="utf-8">' +
'    <meta name="viewport" content="width=device-width, initial-scale=1.0">' +
'    <title>' + appTitle + '</title>' +
'    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />' +
'    <script src="https://cdn.tailwindcss.com"><' + '/script>' +
'    <style>' +
'        body { font-family: "Roboto", sans-serif; background: #0b1120; margin: 0; padding: 24px 12px; display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; }' +
'        .material-symbols-outlined { font-variation-settings: "FILL" 0, "wght" 400, "GRAD" 0, "opsz" 24; }' +
'        .phone-mockup { width: 360px; max-width: 100%; border: 8px solid #1e293b; border-radius: 40px; background: #ffffff; box-shadow: 0 25px 60px -15px rgba(0,0,0,0.7); overflow: hidden; display: flex; flex-direction: column; position: relative; }' +
'    </style>' +
'</head>' +
'<body>' +
'    <div class="phone-mockup">' +
'        <div class="bg-white px-5 pt-3 pb-1 flex items-center justify-between border-b border-slate-100 select-none">' +
'            <span class="text-xs font-bold text-slate-800">9:41</span>' +
'            <div class="w-16 h-3.5 bg-slate-900 rounded-full flex items-center justify-end px-1.5 gap-1">' +
'                <span class="w-1.5 h-1.5 rounded-full bg-slate-700"></span>' +
'            </div>' +
'            <div class="flex items-center gap-1.5 text-slate-700">' +
'                <span class="material-symbols-outlined text-[13px]">signal_cellular_alt</span>' +
'                <span class="material-symbols-outlined text-[13px]">wifi</span>' +
'                <span class="material-symbols-outlined text-[15px]">battery_full</span>' +
'            </div>' +
'        </div>' +
'        <div class="px-4 py-3 bg-[' + primaryColor + '] text-white flex items-center justify-between shadow-sm select-none">' +
'            <div class="flex items-center gap-3">' +
'                <span class="material-symbols-outlined text-xl">menu</span>' +
'                <h1 class="text-sm font-bold truncate max-w-[200px]">' + appTitle + '</h1>' +
'            </div>' +
'            <div class="flex items-center gap-2">' +
'                <span class="material-symbols-outlined text-xl cursor-pointer">search</span>' +
'                <div class="relative cursor-pointer">' +
'                    <span class="material-symbols-outlined text-xl">shopping_bag</span>' +
'                    <span id="headerCartBadge" class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-red-500 text-white rounded-full text-[9px] font-bold flex items-center justify-center">0</span>' +
'                </div>' +
'            </div>' +
'        </div>' +
'        <div class="flex-1 bg-slate-50/50 overflow-y-auto max-h-[500px]">' +
'            ' + bodyContent +
'        </div>' +
(showFab ? '        <button onclick="incrementCounter()" class="absolute bottom-16 right-4 w-12 h-12 bg-[' + primaryColor + '] hover:opacity-90 active:scale-95 text-white rounded-2xl shadow-lg flex items-center justify-center transition-all z-10">' +
'            <span class="material-symbols-outlined text-2xl">add</span>' +
'        </button>' : '') +
'        <div class="bg-white border-t border-slate-100 px-6 py-2 flex items-center justify-around select-none">' +
'            <div class="flex flex-col items-center text-[' + primaryColor + '] cursor-pointer">' +
'                <div class="px-3 py-0.5 rounded-full bg-sky-50 mb-0.5">' +
'                    <span class="material-symbols-outlined text-lg">home</span>' +
'                </div>' +
'                <span class="text-[10px] font-bold">Home</span>' +
'            </div>' +
'            <div class="flex flex-col items-center text-slate-400 hover:text-slate-600 cursor-pointer">' +
'                <span class="material-symbols-outlined text-lg mb-0.5">school</span>' +
'                <span class="text-[10px]">Courses</span>' +
'            </div>' +
'            <div class="flex flex-col items-center text-slate-400 hover:text-slate-600 cursor-pointer">' +
'                <span class="material-symbols-outlined text-lg mb-0.5">chat_bubble</span>' +
'                <span class="text-[10px]">Chat</span>' +
'            </div>' +
'            <div class="flex flex-col items-center text-slate-400 hover:text-slate-600 cursor-pointer">' +
'                <span class="material-symbols-outlined text-lg mb-0.5">person</span>' +
'                <span class="text-[10px]">Profile</span>' +
'            </div>' +
'        </div>' +
'        <div class="bg-white pb-2 flex justify-center">' +
'            <div class="w-28 h-1 bg-slate-300 rounded-full"></div>' +
'        </div>' +
'    </div>' +
'    <script>' +
'        var counter = 0;' +
'        var cartCount = 0;' +
'        function logToParent(msg) {' +
'            try { window.parent.postMessage({ type: "TRYIT_LOG", logType: "log", text: "[Flutter Runtime] " + msg }, "*"); } catch(e) {}' +
'        }' +
'        function incrementCounter() {' +
'            counter++;' +
'            var el = document.getElementById("counterValue");' +
'            if (el) {' +
'                el.innerText = counter;' +
'                el.classList.add("scale-110", "text-sky-500");' +
'                setTimeout(function() { el.classList.remove("scale-110", "text-sky-500"); }, 150);' +
'            }' +
'            logToParent("setState(() => _counter = " + counter + ");");' +
'        }' +
'        function decrementCounter() {' +
'            if (counter > 0) counter--;' +
'            var el = document.getElementById("counterValue");' +
'            if (el) el.innerText = counter;' +
'            logToParent("setState(() => _counter = " + counter + ");");' +
'        }' +
'        function resetCounter() {' +
'            counter = 0;' +
'            var el = document.getElementById("counterValue");' +
'            if (el) el.innerText = 0;' +
'            logToParent("setState(() => _counter = 0);");' +
'        }' +
'        function addToCart(itemName) {' +
'            cartCount++;' +
'            var badge = document.getElementById("headerCartBadge");' +
'            if (badge) badge.innerText = cartCount;' +
'            logToParent("Added to cart: " + itemName + " | Total items: " + cartCount);' +
'        }' +
'        function handleFormSubmit(e) {' +
'            e.preventDefault();' +
'            var feedback = document.getElementById("formFeedback");' +
'            if (feedback) {' +
'                feedback.innerText = "✓ Form Validated! Logging in...";' +
'                feedback.classList.remove("hidden");' +
'            }' +
'            logToParent("formKey.currentState.validate() returned true.");' +
'        }' +
'        function updateProgress(val) {' +
'            var arc = document.getElementById("progressArc");' +
'            var text = document.getElementById("progressText");' +
'            if (arc) arc.style.strokeDashoffset = (502.6 * (1 - val)) + "px";' +
'            if (text) text.innerText = Math.round(val * 100) + "%";' +
'            logToParent("CustomPainter repainted with progress: " + (val * 100) + "%");' +
'        }' +
'        var isExpanded = false;' +
'        function toggleAnimation() {' +
'            isExpanded = !isExpanded;' +
'            var box = document.getElementById("animatedBox");' +
'            var label = document.getElementById("animLabel");' +
'            if (box) {' +
'                if (isExpanded) {' +
'                    box.classList.remove("w-32", "h-32", "bg-indigo-600", "rounded-2xl");' +
'                    box.classList.add("w-44", "h-44", "bg-sky-600", "rounded-full");' +
'                    if (label) label.innerText = "Expanded Circular State (176x176)";' +
'                } else {' +
'                    box.classList.remove("w-44", "h-44", "bg-sky-600", "rounded-full");' +
'                    box.classList.add("w-32", "h-32", "bg-indigo-600", "rounded-2xl");' +
'                    if (label) label.innerText = "Default State (120x120)";' +
'                }' +
'            }' +
'            logToParent("AnimationController state toggled. isExpanded=" + isExpanded);' +
'        }' +
'        logToParent("Widget tree mounted on Impeller Engine Surface at 120 FPS.");' +
'    <' + '/script>' +
'</body>' +
'</html>';
    }

    function executeDartConsoleScript(code, consoleBox, status) {
        const logs = [];
        const printMatches = code.matchAll(/print\s*\(\s*(?:['"](.*?)['"]|([^\)]+))\s*\);/gi);
        for (const m of printMatches) {
            const text = (m[1] !== undefined ? m[1] : m[2] || '').trim();
            logs.push(text);
            addTryItLog('log', text);
        }

        if (logs.length === 0) {
            logs.push('✓ Dart program evaluated successfully with 0 errors.');
            logs.push('Flutter Impeller Engine Pipeline Active.');
            addTryItLog('log', logs[0]);
        }

        const dartText = logs.join('\n');
        if (consoleBox) consoleBox.textContent = dartText;
        setTryItPreview(buildExecutionPreview(dartText, 'DART', 'ok'));
        if (status) {
            status.innerText = 'Execution Finished (Dart 3.x Engine)';
            status.className = 'text-xs text-sky-400 font-bold';
        }
    }

    // Sandboxed browser runtimes. User code never runs on the page that holds the site cookies.
    function pythonFrameMain() {
        const indexURL = 'https://cdn.jsdelivr.net/pyodide/v0.29.4/full/';
        const streamSetup = [
            'import sys',
            'from js import _nectraWrite',
            'class _NectraStream:',
            '    def write(self, data):',
            '        _nectraWrite(data)',
            '        return len(data)',
            '    def flush(self):',
            '        return None',
            'sys.stdout = _NectraStream()',
            'sys.stderr = _NectraStream()'
        ].join('\n');
        let pyodide = null;
        let booting = null;
        let running = false;
        let activeJob = null;
        let queue = [];

        function writeText(value) {
            const chunk = typeof value === 'number' ? String.fromCharCode(value) : String(value);
            const job = activeJob;
            if (job && job.output.length < 32000) job.output += chunk.slice(0, 32000 - job.output.length);
        }

        function boot() {
            if (booting) return booting;
            booting = (async function () {
                const imported = await import(indexURL + 'pyodide.mjs');
                pyodide = await imported.loadPyodide({ indexURL: indexURL });
                globalThis._nectraWrite = writeText;
                await pyodide.runPythonAsync(streamSetup);
                parent.postMessage({ type: 'NECTRA_PY', ready: true }, '*');
            })();
            return booting;
        }

        async function pump() {
            if (running) return;
            running = true;
            while (queue.length) {
                const job = queue.shift();
                activeJob = job;
                try {
                    await boot();
                    parent.postMessage({ type: 'NECTRA_PY', id: job.id, progress: 'Loading the libraries used by this example…' }, '*');
                    if (/\.\s*(?:to_parquet|read_parquet)\s*\(/.test(job.code)) await pyodide.loadPackage('pyarrow');
                    await pyodide.loadPackagesFromImports(job.code);
                    await pyodide.runPythonAsync(streamSetup);
                    if (pyodide.loadedPackages && pyodide.loadedPackages.pandas) {
                        await pyodide.runPythonAsync('import pandas as pd\npd.options.mode.copy_on_write = True\ndel pd');
                    }
                    job.output = '';
                    parent.postMessage({ type: 'NECTRA_PY', id: job.id, progress: 'Running your Python code…' }, '*');
                    await pyodide.runPythonAsync(job.code);
                    parent.postMessage({ type: 'NECTRA_PY', id: job.id, ok: true, output: job.output }, '*');
                } catch (error) {
                    const message = error && error.message ? error.message : String(error);
                    const printed = job.output ? job.output + '\n' : '';
                    parent.postMessage({ type: 'NECTRA_PY', id: job.id, ok: false, output: (printed + message).slice(0, 40000) }, '*');
                }
                if (activeJob === job) activeJob = null;
            }
            running = false;
        }

        window.addEventListener('message', function (event) {
            const data = event.data;
            if (!data || data.type !== 'NECTRA_PY_RUN') return;
            queue = [{ id: data.id, code: String(data.code || ''), output: '' }];
            pump();
        });

        boot().catch(function (error) {
            parent.postMessage({ type: 'NECTRA_PY', ready: false, error: error && error.message ? error.message : String(error) }, '*');
        });
    }

    function phpFrameMain() {
        // A sandboxed frame can leave the browser lock pending forever, so use the in-page queue.
        try {
            const locks = navigator.locks;
            if (locks && typeof locks.request === 'function') {
                const direct = function (name, options, callback) {
                    const work = typeof options === 'function' ? options : callback;
                    return Promise.resolve().then(work);
                };
                try { locks.request = direct; }
                catch (assignError) { Object.defineProperty(locks, 'request', { configurable: true, value: direct }); }
            }
        } catch (lockError) { /* the runtime still has its own in-page queue */ }
        let php = null;
        let booting = null;
        let running = false;
        let queue = [];

        function boot() {
            if (booting) return booting;
            booting = (async function () {
                const imported = await import('https://cdn.jsdelivr.net/npm/php-wasm@0.2.0/PhpWeb.mjs');
                php = new imported.PhpWeb({
                    version: '8.3',
                    autoTransaction: false,
                    ini: 'allow_url_fopen=0\ndisable_functions=exec,system,shell_exec,passthru,popen,proc_open\n'
                });
                await new Promise(function (resolve, reject) {
                    const timer = setTimeout(function () { reject(new Error('PHP runtime timed out')); }, 120000);
                    php.addEventListener('ready', function () { clearTimeout(timer); resolve(); }, { once: true });
                });
                parent.postMessage({ type: 'NECTRA_PHP', ready: true }, '*');
            })();
            return booting;
        }

        function prepare(code) {
            const text = String(code || '').replace(/^\uFEFF/, '').trim();
            if (/^<\?php(?=[\s]|$)/i.test(text)) return text;
            if (/<\?/.test(text)) return '<?php\n?>' + text;
            return '<?php\n' + text;
        }

        async function pump() {
            if (running) return;
            running = true;
            while (queue.length) {
                const job = queue.shift();
                let output = '';
                const take = function (phpEvent) {
                    const detail = phpEvent && phpEvent.detail;
                    const text = Array.isArray(detail) ? detail.join('') : (detail == null ? '' : String(detail));
                    if (output.length < 24000) output += text.slice(0, 24000 - output.length);
                };
                try {
                    await boot();
                    parent.postMessage({ type: 'NECTRA_PHP', id: job.id, progress: 'Running your PHP code…' }, '*');
                    php.addEventListener('output', take);
                    php.addEventListener('error', take);
                    try {
                        await php.run(prepare(job.code));
                    } finally {
                        php.removeEventListener('output', take);
                        php.removeEventListener('error', take);
                    }
                    const failed = /(?:Fatal error|Parse error|Uncaught)/i.test(output);
                    parent.postMessage({ type: 'NECTRA_PHP', id: job.id, ok: !failed, output: output }, '*');
                } catch (error) {
                    const message = error && error.message ? error.message : String(error);
                    parent.postMessage({ type: 'NECTRA_PHP', id: job.id, ok: false, output: (output + message).slice(0, 24000) }, '*');
                }
            }
            running = false;
        }

        window.addEventListener('message', function (event) {
            const data = event.data;
            if (!data || data.type !== 'NECTRA_PHP_RUN') return;
            queue = [{ id: data.id, code: String(data.code || '') }];
            pump();
        });

        boot().catch(function (error) {
            parent.postMessage({ type: 'NECTRA_PHP', ready: false, error: error && error.message ? error.message : String(error) }, '*');
        });
    }

    function mountRuntime(prefix, createFrame) {
        let host = null;
        let serial = 0;

        function detach(target) {
            if (!target || target.gone) return;
            target.gone = true;
            window.removeEventListener('message', target.listen);
            if (target.reject) {
                const reject = target.reject;
                target.resolve = null;
                target.reject = null;
                reject(new Error('The runtime stopped.'));
            }
            target.frame.remove();
            if (host === target) host = null;
        }

        function ensure() {
            if (host && !host.gone) return host;
            const frame = createFrame();
            const runtime = { frame: frame, pending: null, listen: null, ready: null, resolve: null, reject: null, gone: false };
            runtime.ready = new Promise(function (resolve, reject) {
                runtime.resolve = resolve;
                runtime.reject = reject;
            });
            runtime.listen = function (event) {
                if (runtime.gone || event.source !== runtime.frame.contentWindow) return;
                const data = event.data;
                if (!data || data.type !== prefix) return;
                if (data.ready === true && runtime.resolve) {
                    const resolve = runtime.resolve;
                    runtime.resolve = null;
                    runtime.reject = null;
                    resolve();
                }
                if (data.ready === false && runtime.reject) {
                    const reject = runtime.reject;
                    runtime.resolve = null;
                    runtime.reject = null;
                    reject(new Error(data.error || 'The runtime did not start'));
                }
                const pending = runtime.pending;
                if (!pending || data.id !== pending.id) return;
                if (data.progress) {
                    pending.onProgress(data.progress);
                    return;
                }
                if (data.ok !== undefined) pending.onDone(!!data.ok, data.output || '');
            };
            window.addEventListener('message', runtime.listen);
            runtime.ready.catch(function () { detach(runtime); });
            host = runtime;
            document.body.appendChild(frame);
            return runtime;
        }

        function run(code, finish, options) {
            const runtime = ensure();
            const id = ++serial;
            let settled = false;
            let timer = setTimeout(function () { giveUp('boot'); }, options.bootMs);

            function giveUp(stage) {
                if (settled || id !== serial) return;
                settled = true;
                clearTimeout(timer);
                if (runtime.pending && runtime.pending.id === id) runtime.pending = null;
                finish(stage === 'boot' ? 'unavailable' : 'error', stage === 'boot' ? options.bootMessage : options.runMessage);
                detach(runtime);
            }

            runtime.pending = {
                id: id,
                onProgress: function (text) {
                    if (settled || id !== serial) return;
                    finish('run', text);
                    if (text === options.runningText) {
                        clearTimeout(timer);
                        timer = setTimeout(function () { giveUp('hung'); }, options.runMs);
                    }
                },
                onDone: function (ok, output) {
                    if (settled || id !== serial) return;
                    settled = true;
                    clearTimeout(timer);
                    if (runtime.pending && runtime.pending.id === id) runtime.pending = null;
                    const text = output && String(output).trim() ? output : 'Code finished without printing output.';
                    finish(ok ? 'ok' : 'error', text);
                }
            };

            runtime.ready.then(function () {
                if (settled || id !== serial || runtime.gone || !runtime.frame.contentWindow) return;
                runtime.frame.contentWindow.postMessage({ type: prefix + '_RUN', id: id, code: code }, '*');
            }).catch(function (error) {
                if (settled || id !== serial) return;
                settled = true;
                clearTimeout(timer);
                const detail = error && error.message ? error.message : String(error);
                finish('unavailable', options.bootMessage + (detail && detail !== 'The runtime stopped.' ? '\n\n' + detail : ''));
            });
        }

        function cancel() {
            serial += 1;
            if (host) host.pending = null;
        }

        return { run: run, cancel: cancel };
    }

    function runtimeFrame(main, csp) {
        const frame = document.createElement('iframe');
        frame.hidden = true;
        frame.setAttribute('sandbox', 'allow-scripts');
        frame.title = 'Practice runtime';
        frame.srcdoc = '<!doctype html><meta charset="utf-8"><meta http-equiv="Content-Security-Policy" content="' + csp + '"><script>(' + main.toString() + ')()<' + '/script>';
        return frame;
    }

    const pythonRuntime = mountRuntime('NECTRA_PY', function () {
        return runtimeFrame(pythonFrameMain, "default-src 'none'; script-src 'unsafe-inline' 'unsafe-eval' 'wasm-unsafe-eval' blob: https://cdn.jsdelivr.net; connect-src blob: data: https://cdn.jsdelivr.net; worker-src blob: https://cdn.jsdelivr.net");
    });
    const phpRuntime = mountRuntime('NECTRA_PHP', function () {
        return runtimeFrame(phpFrameMain, "default-src 'none'; script-src 'unsafe-inline' 'unsafe-eval' 'wasm-unsafe-eval' https://cdn.jsdelivr.net; connect-src https://cdn.jsdelivr.net");
    });

    let toolHost = null;
    let toolSerial = 0;

    function toolFrameDocument() {
        if (typeof window.NectraRunnersInstall !== 'function') throw new Error('Practice runners did not load');
        const safe = window.NectraRunnersInstall.toString().replace(/<\/script/gi, '<\\/script');
        const csp = "default-src 'none'; script-src 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net";
        return '<!doctype html><meta charset="utf-8"><meta http-equiv="Content-Security-Policy" content="' + csp + '">' +
            '<script>(' + safe + ')();<' + '/script>' +
            '<script src="https://cdn.jsdelivr.net/npm/alasql@4.6.6/dist/alasql.min.js"><' + '/script>' +
            '<script>' +
            'window.addEventListener("message", async function (event) {' +
            '  var data = event.data || {};' +
            '  if (data.type !== "NECTRA_TOOL_RUN") return;' +
            '  try {' +
            '    var result;' +
            '    if (data.kind === "sql") {' +
            '      var engine = typeof alasql === "function" ? alasql : null;' +
            '      if (!engine) throw new Error("The SQL practice engine did not load. Retry Run Code.");' +
            '      result = globalThis.NectraRunners.runSqlWith(engine, data.code);' +
            '    } else if (data.kind === "node") {' +
            '      result = await globalThis.NectraRunners.executeNodeSample(data.code);' +
            '    } else {' +
            '      result = globalThis.NectraRunners.executeMongoSample(data.code);' +
            '    }' +
            '    parent.postMessage({ type: "NECTRA_TOOL", id: data.id, ok: !!result.ok, output: result.output || "" }, "*");' +
            '  } catch (error) {' +
            '    parent.postMessage({ type: "NECTRA_TOOL", id: data.id, ok: false, output: error && error.message ? error.message : String(error) }, "*");' +
            '  }' +
            '});' +
            'parent.postMessage({ type: "NECTRA_TOOL", ready: true }, "*");' +
            '<' + '/script>';
    }

    function detachToolHost(target) {
        if (!target || target.gone) return;
        target.gone = true;
        window.removeEventListener('message', target.listen);
        if (target.reject) {
            const reject = target.reject;
            target.resolve = null;
            target.reject = null;
            reject(new Error('The runtime stopped.'));
        }
        target.frame.remove();
        if (toolHost === target) toolHost = null;
    }

    function ensureToolHost() {
        if (toolHost && !toolHost.gone) return toolHost.ready;
        const frame = document.createElement('iframe');
        frame.hidden = true;
        frame.setAttribute('sandbox', 'allow-scripts');
        frame.title = 'Practice runtime';
        const runtime = { frame: frame, pending: null, listen: null, ready: null, resolve: null, reject: null, gone: false };
        runtime.ready = new Promise(function (resolve, reject) {
            runtime.resolve = resolve;
            runtime.reject = reject;
        });
        runtime.listen = function (event) {
            if (runtime.gone || event.source !== frame.contentWindow) return;
            const data = event.data;
            if (!data || data.type !== 'NECTRA_TOOL') return;
            if (data.ready === true && runtime.resolve) {
                const resolve = runtime.resolve;
                runtime.resolve = null;
                runtime.reject = null;
                resolve();
            }
            const pending = runtime.pending;
            if (!pending || data.id !== pending.id) return;
            pending.onDone(!!data.ok, data.output || '');
        };
        window.addEventListener('message', runtime.listen);
        runtime.ready.catch(function () { detachToolHost(runtime); });
        toolHost = runtime;
        try {
            frame.srcdoc = toolFrameDocument();
        } catch (error) {
            detachToolHost(runtime);
            return Promise.reject(error);
        }
        document.body.appendChild(frame);
        return runtime.ready;
    }

    function runToolSample(kind, code, finish) {
        const id = ++toolSerial;
        let settled = false;
        const runtimePromise = ensureToolHost();
        const timer = setTimeout(function () {
            if (settled || id !== toolSerial) return;
            settled = true;
            finish('error', 'The practice runtime took too long. Retry Run Code.');
            if (toolHost) detachToolHost(toolHost);
        }, 40000);
        runtimePromise.then(function () {
            if (settled || id !== toolSerial || !toolHost || toolHost.gone || !toolHost.frame.contentWindow) return;
            toolHost.pending = {
                id: id,
                onDone: function (ok, output) {
                    if (settled || id !== toolSerial) return;
                    settled = true;
                    clearTimeout(timer);
                    if (toolHost && toolHost.pending && toolHost.pending.id === id) toolHost.pending = null;
                    finish(ok ? 'ok' : 'error', output && String(output).trim() ? output : 'Code finished without printing output.');
                }
            };
            toolHost.frame.contentWindow.postMessage({ type: 'NECTRA_TOOL_RUN', id: id, kind: kind, code: code }, '*');
        }).catch(function (error) {
            if (settled || id !== toolSerial) return;
            settled = true;
            clearTimeout(timer);
            finish('unavailable', 'The practice runtime did not load. Retry Run Code.\n\n' + (error && error.message ? error.message : String(error)));
        });
    }

    function runBrowserPython(code, finish) {
        pythonRuntime.run(code, finish, {
            bootMs: 180000,
            runMs: 45000,
            bootMessage: 'The browser could not initialize Python or its libraries. Retry Run Code, or copy the example into lesson.py and run it locally.',
            runMessage: 'The Python example exceeded its 45-second browser execution limit.',
            runningText: 'Running your Python code…'
        });
    }

    function runBrowserPhp(code, finish) {
        phpRuntime.run(code, finish, {
            bootMs: 150000,
            runMs: 60000,
            bootMessage: 'The browser could not initialize PHP. Retry Run Code, or copy the example into lesson.php and run it locally.',
            runMessage: 'The PHP example exceeded its 30-second browser execution limit.',
            runningText: 'Running your PHP code…'
        });
    }

    function runPageInterpreter(kind, code) {
        const runners = window.NectraRunners;
        if (!runners || typeof runners.runC !== 'function' || typeof runners.runCSharp !== 'function') {
            showCourseRunOutput('unavailable', 'The practice runner did not load. Refresh the page and select Run Code again.');
            return;
        }
        const result = kind === 'csharp' ? runners.runCSharp(code) : runners.runC(code);
        const output = result && result.output ? result.output : '';
        showCourseRunOutput(result && result.ok ? 'ok' : 'error', String(output).trim() ? output : 'Code finished without printing output.');
    }

    function cancelPythonRun() {
        pythonRuntime.cancel();
        phpRuntime.cancel();
        toolSerial += 1;
        if (toolHost) toolHost.pending = null;
    }

    let courseRunner = null;

    function openTryIt(code, lang = 'php', runner = null) {
        cancelPythonRun?.();
        discardPendingPreview();
        courseRunner = runner;
        lastTryItScrollPosition = window.pageYOffset || document.documentElement.scrollTop || window.scrollY || 0;
        window.lastTryItScrollPosition = lastTryItScrollPosition;

        currentTryItLang = (lang || 'php').toLowerCase();
        const modal = ensureTryItModal();
        const editor = document.getElementById('tryitEditor');
        const badge = document.getElementById('tryitLangBadge');
        const consoleBox = document.getElementById('tryitConsole');
        const status = document.getElementById('tryitStatus');
        const countBadge = document.getElementById('tryitConsoleCount');

        if (!modal || !editor) {
            console.warn('tryItModal or tryitEditor not found in DOM.');
            return;
        }

        const cleanCode = runner ? code.trim() : decodeTryItEntities(code).replace(/\\+(\$[a-zA-Z0-9_]*)/g, '$1').trim();
        window.tryItOriginalCode = cleanCode;
        editor.value = cleanCode;

        tryitConsoleLogs = [];
        if (consoleBox) consoleBox.textContent = '';
        if (countBadge) {
            countBadge.innerText = '0';
            countBadge.classList.add('hidden');
        }

        const isTree = currentTryItLang === 'tree' || isDirectoryTree(cleanCode);
        const isBash = !isTree && !isRunnableProgramLang(currentTryItLang) && (currentTryItLang === 'bash' || currentTryItLang === 'sh' || currentTryItLang === 'shell' || isTerminalCommand(cleanCode));

        const isFlutterMode = !isTree && !isBash && (
            currentTryItLang === 'flutter' || currentTryItLang === 'dart' ||
            cleanCode.includes('package:flutter') || cleanCode.includes('StatelessWidget') ||
            cleanCode.includes('StatefulWidget') || cleanCode.includes('BuildContext') ||
            cleanCode.includes('Widget build(') || cleanCode.includes('MaterialApp(') ||
            cleanCode.includes('Scaffold(') || cleanCode.includes('runApp(')
        );

        const isNextMode = !isTree && !isBash && !isFlutterMode && (currentTryItLang === 'next' || currentTryItLang === 'nextjs' ||
            cleanCode.includes('next/image') || cleanCode.includes('next/link') || cleanCode.includes('next/navigation') ||
            cleanCode.includes('next/font') || cleanCode.includes("'use server'") || cleanCode.includes('"use server"') ||
            cleanCode.includes("'use client'") || cleanCode.includes('"use client"') || cleanCode.includes('CloudMetricsDashboard') ||
            cleanCode.includes('SessionViewer') || cleanCode.includes('RbacGate') || cleanCode.includes('SanitizationDemo') ||
            cleanCode.includes('SocialPreview') || cleanCode.includes('HeroBanner'));

        const isReactMode = !isTree && !isBash && !isFlutterMode && (isNextMode || currentTryItLang === 'react' || currentTryItLang === 'jsx' ||
            cleanCode.includes('import React') || cleanCode.includes('useState(') || cleanCode.includes('useReducer(') ||
            cleanCode.includes('createRoot(') || cleanCode.includes('ReactDOM.') || /<\s*[A-Z][A-Za-z0-9_]*\s*[\/>]/.test(cleanCode) ||
            cleanCode.includes('React.'));

        const isAngularMode = !isTree && !isBash && !isFlutterMode && !isReactMode && (
            currentTryItLang === 'angular' || currentTryItLang === 'angularjs' ||
            cleanCode.includes('ng-app') || cleanCode.includes('angular.module') ||
            cleanCode.includes('ng-controller') || cleanCode.includes('ng-model') ||
            cleanCode.includes('ng-repeat') || cleanCode.includes('angular.')
        );

        if (isTree) {
            currentTryItLang = 'tree';
            if (badge) badge.innerText = 'PROJECT STRUCTURE';
            switchTryItOutputView('preview');
        } else if (isBash) {
            currentTryItLang = 'bash';
            if (badge) badge.innerText = 'TERMINAL / BASH';
            switchTryItOutputView('split');
        } else if (isFlutterMode) {
            currentTryItLang = 'flutter';
            if (badge) badge.innerText = 'FLUTTER & DART (3.x)';
            const isUI = cleanCode.includes('Widget') || cleanCode.includes('Scaffold') || cleanCode.includes('MaterialApp') || cleanCode.includes('Container') || cleanCode.includes('StatefulWidget') || cleanCode.includes('StatelessWidget') || cleanCode.includes('Column') || cleanCode.includes('Row');
            switchTryItOutputView(isUI ? 'preview' : 'console');
        } else if (isNextMode) {
            currentTryItLang = 'react';
            if (badge) badge.innerText = 'NEXT.JS (APP ROUTER)';
            switchTryItOutputView('preview');
        } else if (isReactMode) {
            currentTryItLang = 'react';
            if (badge) badge.innerText = 'REACT (JSX)';
            switchTryItOutputView('preview');
        } else if (isAngularMode) {
            currentTryItLang = 'angular';
            if (badge) badge.innerText = 'ANGULARJS (1.8)';
            switchTryItOutputView('preview');
        } else if (currentTryItLang === 'html' || currentTryItLang === 'css') {
            if (badge) badge.innerText = currentTryItLang.toUpperCase();
            switchTryItOutputView('preview');
        } else if (currentTryItLang === 'javascript') {
            if (badge) badge.innerText = 'JAVASCRIPT';
            if (cleanCode.includes('<html') || cleanCode.includes('<body') || cleanCode.includes('<script')) {
                switchTryItOutputView('preview');
            } else {
                switchTryItOutputView('console');
            }
        } else {
            const badgeMap = {
                laravel: 'LARAVEL 11 (PHP 8)',
                codeigniter: 'CODEIGNITER 4',
                mongodb: 'MONGODB (MONGOSH)',
                postgresql: 'POSTGRESQL 16',
                mysql: 'MYSQL 8.0',
                c: 'C (ISO C17)',
                csharp: 'C# (.NET 8)',
                typescript: 'TYPESCRIPT 5',
                nodejs: 'NODE.JS 22',
                python: 'PYTHON 3'
            };
            if (badge) badge.innerText = badgeMap[currentTryItLang] || currentTryItLang.toUpperCase();
            switchTryItOutputView('preview');
        }

        if (status) {
            status.innerText = 'Ready';
            status.className = 'text-xs tryit-accent font-bold';
        }

        if (!modal.classList.contains('active')) modal.getBoundingClientRect();
        modal.classList.add('active');

        if (window.innerWidth <= 768) {
            switchTryItMobileTab('editor');
        }

        const runButtonLabel = document.querySelector('#tryitRunBtn span');
        if (runButtonLabel) runButtonLabel.textContent =
            runner && runner !== 'python-browser' ? 'Run instructions' : 'Run Code';
        if (runner && runner !== 'python-browser') {
            showCourseRunOutput('info', localRunInstructions());
            return;
        }

        try {
            executeTryItCode();
        } catch (err) {
            console.error('Error during code execution:', err);
        }
    }

    function closeTryItModal() {
        cancelPythonRun?.();
        discardPendingPreview();
        const modal = document.getElementById('tryItModal');
        if (modal) {
            modal.classList.remove('active');
        }
        if (typeof window.lastTryItScrollPosition === 'number') {
            window.scrollTo({ top: window.lastTryItScrollPosition, behavior: 'instant' });
        }
    }

    function resetTryItCode() {
        const editor = document.getElementById('tryitEditor');
        const btn = document.getElementById('tryitResetBtn');
        if (editor && typeof window.tryItOriginalCode === 'string') {
            editor.value = window.tryItOriginalCode;
            executeTryItCode();
            if (btn) {
                const origHtml = btn.innerHTML;
                btn.innerHTML = '<span class="text-amber-400 font-bold">✓ Reset!</span>';
                setTimeout(() => { btn.innerHTML = origHtml; }, 1200);
            }
        }
    }

    function clearTryItCode() {
        const editor = document.getElementById('tryitEditor');
        if (editor) {
            editor.value = '';
            editor.focus();
        }
    }

    function copyTryItCode() {
        const editor = document.getElementById('tryitEditor');
        const btn = document.getElementById('tryitCopyBtn');
        if (!editor) return;
        navigator.clipboard.writeText(editor.value).then(() => {
            if (btn) {
                const origHtml = btn.innerHTML;
                btn.innerHTML = '<svg class="w-3.5 h-3.5 tryit-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> <span class="hidden sm:inline">Copied!</span>';
                btn.classList.add('tryit-accent', 'tryit-accent-border');
                setTimeout(() => {
                    btn.innerHTML = origHtml;
                    btn.classList.remove('tryit-accent', 'tryit-accent-border');
                }, 1800);
            }
        });
    }

    function copyTryItOutput() {
        const consoleBox = document.getElementById('tryitConsole');
        const btn = document.getElementById('tryitCopyOutputBtn');
        if (!consoleBox) return;
        const textToCopy = consoleBox.textContent;
        navigator.clipboard.writeText(textToCopy).then(() => {
            if (btn) {
                const origHtml = btn.innerHTML;
                btn.innerHTML = '<span class="tryit-accent font-bold">✓ Result Copied!</span>';
                setTimeout(() => { btn.innerHTML = origHtml; }, 1800);
            }
        });
    }

    function switchTryItMobileTab(tab) {
        const editorPane = document.querySelector('.tryit-editor-pane');
        const outputPane = document.querySelector('.tryit-output-pane');
        const tabEditor = document.getElementById('tabEditorBtn');
        const tabOutput = document.getElementById('tabOutputBtn');

        if (tab === 'editor') {
            editorPane?.classList.remove('mobile-hidden');
            outputPane?.classList.add('mobile-hidden');
            tabEditor?.classList.add('active');
            tabOutput?.classList.remove('active');
        } else {
            editorPane?.classList.add('mobile-hidden');
            outputPane?.classList.remove('mobile-hidden');
            tabEditor?.classList.remove('active');
            tabOutput?.classList.add('active');
        }
    }

    function localRunInstructions() {
        return courseRunner === 'terminal-local'
            ? 'Copy these commands into PowerShell in your project folder. Run them in order to set up the course environment.'
            : 'This example needs a local Python environment with PyTorch installed. Copy your edited code into lesson.py, then run python lesson.py. For distributed training, use Linux or WSL.';
    }

    function showCourseRunOutput(state, output) {
        const status = document.getElementById('tryitStatus');
        const consoleBox = document.getElementById('tryitConsole');
        if (state !== 'run') {
            if (consoleBox) consoleBox.textContent = output;
            setTryItPreview(buildExecutionPreview(output, previewLangLabel(), state));
        }
        if (!status) return;
        status.innerText = state === 'run' ? output : { ok: 'Execution finished', error: 'Execution failed', info: 'Ready', unavailable: 'Runtime unavailable' }[state];
        status.className = 'text-xs font-bold ' + (state === 'error' ? 'text-red-400' : 'tryit-accent');
    }

    function executeTryItCode() {
        const editor = document.getElementById('tryitEditor');
        const consoleBox = document.getElementById('tryitConsole');
        const status = document.getElementById('tryitStatus');
        if (!editor) return;

        const code = editor.value;
        discardPendingPreview();
        if (status) {
            status.innerText = 'Executing...';
            status.className = 'text-xs text-amber-300 font-bold';
        }

        if (window.innerWidth <= 768) {
            switchTryItMobileTab('output');
        }

        if (courseRunner) {
            if (courseRunner === 'python-browser') {
                showCourseRunOutput('run', 'Starting the browser Python runtime…');
                runBrowserPython(code, showCourseRunOutput);
            } else {
                showCourseRunOutput('info', localRunInstructions());
            }
            return;
        }

        const isTreeSnippet = currentTryItLang === 'tree' || isDirectoryTree(code);

        if (isTreeSnippet) {
            switchTryItOutputView(window.currentTryItOutputView === 'console' ? 'console' : 'preview');
            const treeLines = code.split('\n');
            let parsedItems = [];
            treeLines.forEach(l => {
                const line = l.trim();
                if (!line) return;
                const cleanName = line.replace(/^[│├─└\s\u2500-\u257F|+-]+/, '').replace(/#.*$/, '').trim();
                const comment = line.includes('#') ? line.split('#')[1].trim() : '';
                const isDir = cleanName.endsWith('/') || ((line.includes('├──') || line.includes('└──')) && cleanName.indexOf('.') === -1 && !cleanName.endsWith('.json'));
                const depth = Math.max(0, (line.match(/[│├└\u2500-\u257F|]/g) || []).length);
                if (cleanName) {
                    parsedItems.push({ raw: line, name: cleanName, comment: comment, isDir: isDir, depth: depth });
                }
            });

            const htmlPayload = '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">' +
                '<script src="https://cdn.tailwindcss.com"><' + '/script>' +
                '<style>body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; padding: 20px; background: #0f172a; color: #f8fafc; margin: 0; }</style>' +
                '</head><body class="p-4 sm:p-6">' +
                '<div class="max-w-2xl mx-auto bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-2xl">' +
                '  <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">' +
                '    <div class="flex items-center gap-2">' +
                '      <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>' +
                '      <span class="font-black text-white text-base">Project Architecture Explorer</span>' +
                '    </div>' +
                '    <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-slate-800 text-emerald-400 border border-slate-700">Production Directory Manifest</span>' +
                '  </div>' +
                '  <div class="space-y-1 font-mono text-xs">' +
                parsedItems.map(item => {
                    const icon = item.isDir ? '📁' : (item.name.endsWith('.tsx') || item.name.endsWith('.jsx') ? '⚛️' : (item.name.endsWith('.ts') || item.name.endsWith('.js') ? '📜' : (item.name.endsWith('.json') ? '⚙️' : '📄')));
                    const indentPx = Math.max(0, (item.depth - 1) * 20);
                    const nameColor = item.isDir ? 'text-indigo-400 font-bold' : (item.name.endsWith('.tsx') ? 'text-cyan-300 font-semibold' : 'text-slate-200');
                    return '<div class="flex items-baseline justify-between p-1.5 rounded hover:bg-slate-800/60 transition" style="padding-left: ' + indentPx + 'px;">' +
                        '  <div class="flex items-center gap-2">' +
                        '    <span>' + icon + '</span>' +
                        '    <span class="' + nameColor + '">' + item.name + '</span>' +
                        '  </div>' +
                        (item.comment ? '  <span class="text-slate-500 text-[11px] font-sans truncate max-w-xs">' + item.comment + '</span>' : '') +
                        '</div>';
                }).join('') +
                '  </div>' +
                '  <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between text-slate-400 text-xs font-sans">' +
                '    <span>Total Manifest Items: ' + parsedItems.length + '</span>' +
                '    <span class="text-emerald-400 font-bold">✓ Production Structure Verified</span>' +
                '  </div>' +
                '</div>' +
                '</body></html>';

            setTryItPreview(htmlPayload);
            if (consoleBox) consoleBox.textContent = code;
            if (status) {
                status.innerText = 'Project Structure Verified';
                status.className = 'text-xs tryit-accent font-bold';
            }
            return;
        }

        const isBashSnippet = !isTreeSnippet && !isRunnableProgramLang(currentTryItLang) && (currentTryItLang === 'bash' || currentTryItLang === 'sh' || currentTryItLang === 'shell' || isTerminalCommand(code));

        if (isBashSnippet) {
            switchTryItOutputView(window.currentTryItOutputView === 'preview' ? 'preview' : 'split');
            const lines = code.split('\n');
            let termOut = [];
            lines.forEach(l => {
                const line = l.trim();
                if (!line) return;
                if (line.startsWith('#')) {
                    termOut.push(line);
                } else {
                    const cleanLine = line.replace(/^\$\s*/, '');
                    termOut.push('$ ' + cleanLine);
                    if (cleanLine.startsWith('cd ')) {
                        const dir = cleanLine.replace('cd ', '').trim();
                        termOut.push(`  Changed directory to ./${dir}`);
                    } else if (cleanLine.includes('create-next-app')) {
                        termOut.push('  ✔ Creating a new Next.js app in ./my-app\n  ✔ Using TypeScript\n  ✔ Using ESLint\n  ✔ Using Tailwind CSS\n  ✔ Using `src/` directory\n  ✔ Using App Router\n  ✔ Initialized git repository\n  Success! Created my-app at ./my-app');
                    } else if (cleanLine.includes('create vite')) {
                        termOut.push('  ✔ Scaffolding project in ./my-react-app...\n  Done. Now run:\n  cd my-react-app\n  npm install\n  npm run dev');
                    } else if (cleanLine.includes('npm install') || cleanLine.includes('npm i')) {
                        termOut.push('  added 184 packages in 2.8s\n  found 0 vulnerabilities');
                    } else if (cleanLine.includes('npm run dev') || cleanLine.includes('next dev')) {
                        if (cleanLine.includes('--turbo') || cleanLine.includes('turbo')) {
                            termOut.push('  ▲ Next.js 14.2.0 (Turbopack)\n  - Local:        http://localhost:3000\n  - Environments: .env\n  ✓ Ready in 780ms (Turbopack engine active)');
                        } else {
                            termOut.push('  ▲ Next.js 14.2.0\n  - Local:        http://localhost:3000\n  - Environments: .env\n  ✓ Ready in 1340ms');
                        }
                    } else if (cleanLine.includes('npm run build') || cleanLine.includes('next build')) {
                        termOut.push('  ▲ Next.js 14.2.0\n  ✓ Compiled successfully in 3.4s\n  ✓ Generating static pages (47/47)\n  ✓ Finalizing page optimization\n  ○  (Static)   prerendered as static content\n  ●  (SSG)      prerendered as static HTML\n  λ  (Dynamic)  server-rendered on demand using Node.js');
                    } else if (cleanLine.includes('git init') || cleanLine.includes('git commit')) {
                        termOut.push('  Initialized empty Git repository\n  [main (root-commit) a1b2c3d] Initial Next.js production scaffold');
                    } else if (cleanLine.startsWith('FROM ') || cleanLine.startsWith('WORKDIR ') || cleanLine.startsWith('RUN ') || cleanLine.startsWith('COPY ')) {
                        termOut.push('  [+] Building 2.1s (4/4) FINISHED\n  => [internal] load build definition from Dockerfile\n  => => transferring dockerfile: 480B done\n  => [base 1/1] FROM docker.io/library/node:20-alpine\n  => CACHED [deps 1/2] RUN apk add --no-cache libc6-compat\n  => exporting to image: sha256:4a8b9c... tagged my-next-app:latest');
                    } else if (cleanLine.startsWith('datasource') || cleanLine.startsWith('generator') || cleanLine.startsWith('model')) {
                        termOut.push('  Prisma schema loaded from schema.prisma\n  Datasource "db": PostgreSQL database connection valid\n  ✔ 5 models validated successfully (User, Project, Metric, Subscription, AuditLog)\n  Environment variables loaded from .env');
                    } else if (cleanLine.includes('flutter create')) {
                        const proj = cleanLine.replace(/.*flutter\s+create\s+([a-zA-Z0-9_-]+).*/, '$1') || 'my_app';
                        termOut.push('  Creating flutter project...\n  Resolving dependencies...\n  Got dependencies!\n  Wrote 127 files.\n  All done! In order to run your application, type:\n  $ cd ' + proj + '\n  $ flutter run');
                    } else if (cleanLine.includes('flutter pub get')) {
                        termOut.push('  Resolving dependencies...\n  + provider 6.1.1\n  + go_router 13.2.0\n  + http 1.2.0\n  Changed 3 dependencies!\n  1 package has newer version available.');
                    } else if (cleanLine.includes('flutter run')) {
                        termOut.push('  Launching lib/main.dart on Pixel 8 Pro in debug mode...\n  Running Gradle task \'assembleDebug\'...\n  ✓ Built build/app/outputs/flutter-apk/app-debug.apk\n  Syncing files to device Pixel 8 Pro...\n  Flutter run key commands:\n  r Hot reload. 🔥\n  R Hot restart.\n  h List all available controls.\n  q Quit.\n  Observatory debugger active at: http://127.0.0.1:58291/');
                    } else if (cleanLine.includes('flutter test')) {
                        termOut.push('  00:01 +1: All tests passed! [Impeller Engine verified]');
                    } else if (cleanLine.includes('flutter build')) {
                        termOut.push('  ✓ Built build/app/outputs/flutter-apk/app-release.apk (18.4MB)');
                    } else if (cleanLine.includes('dart analyze')) {
                        termOut.push('  Analyzing .\n  No issues found! (ran in 1.2s)');
                    } else if (!line.startsWith('#')) {
                        termOut.push('  ✓ Command accepted by the sandbox shell.');
                    }
                }
            });
            const terminalText = termOut.join('\n') || '$ (empty)\n  Nothing to execute.';
            if (consoleBox) consoleBox.textContent = terminalText;
            setTryItPreview(buildExecutionPreview(terminalText, 'TERMINAL / BASH', 'ok'));
            if (status) {
                status.innerText = 'Terminal Execution Simulated';
                status.className = 'text-xs tryit-accent font-bold';
            }
            return;
        }

        const isFlutterSnippet = currentTryItLang === 'flutter' || currentTryItLang === 'dart' ||
            code.includes('package:flutter') || code.includes('StatelessWidget') || code.includes('StatefulWidget') ||
            code.includes('BuildContext') || code.includes('Widget build(') || code.includes('MaterialApp(') || code.includes('Scaffold(');

        if (isFlutterSnippet) {
            const isUI = code.includes('Widget') || code.includes('Scaffold') || code.includes('MaterialApp') || code.includes('Container') || code.includes('StatefulWidget') || code.includes('StatelessWidget') || code.includes('Column') || code.includes('Row');
            if (isUI) {
                switchTryItOutputView(window.currentTryItOutputView === 'split' ? 'split' : 'preview');
                try {
                    const fullPage = renderFlutterSimulatorHtml(code);
                    setTryItPreview(fullPage);
                } catch (e) {
                    console.error('Flutter Frame error:', e);
                }
                if (status) {
                    status.innerText = 'Rendered Live Flutter Mobile Component';
                    status.className = 'text-xs text-sky-400 font-bold';
                }
            } else {
                switchTryItOutputView(window.currentTryItOutputView === 'split' ? 'split' : 'preview');
                executeDartConsoleScript(code, consoleBox, status);
            }
            return;
        }

        if (code.includes('test(') && code.includes('expect(')) {
            const testMatch = code.match(/test\(['"](.*?)['"]/);
            const testDesc = testMatch ? testMatch[1] : 'renders component and verifies assertions';
            const testReport = ` PASS  src/__tests__/Component.test.jsx\n  ✓ ${testDesc} (34 ms)\n\nTest Suites: 1 passed, 1 total\nTests:       1 passed, 1 total\nSnapshots:   0 total\nTime:        0.724 s\nRan all test suites.`;
            if (consoleBox) consoleBox.textContent = testReport;
            setTryItPreview(buildExecutionPreview(testReport, 'JEST', 'ok'));
            switchTryItOutputView(window.currentTryItOutputView === 'console' ? 'console' : 'preview');
            if (status) {
                status.innerText = 'Jest / RTL Test Suite Passed';
                status.className = 'text-xs tryit-accent font-bold';
            }
            return;
        }

        if (currentTryItLang === 'html') {
            switchTryItOutputView(window.currentTryItOutputView === 'split' ? 'split' : 'preview');
            try {
                const trimmed = code.trim();
                const isRobotsTxt = /^user-agent\s*:/im.test(trimmed);
                const isPureJsonLd = trimmed.startsWith('{') && trimmed.includes('"@context"');
                const isXmlSitemap = trimmed.startsWith('<?xml') || trimmed.startsWith('<urlset');
                const hasVisibleBody = /<body[\s>]/i.test(trimmed) || /<(h[1-6]|div|section|form|table|main|canvas|svg|p|button)\b/i.test(trimmed.replace(/<head[\s\S]*?<\/head>/gi, ''));

                if (isRobotsTxt || isPureJsonLd || isXmlSitemap || (!hasVisibleBody && (/<title>|<meta\b|application\/ld\+json/i.test(trimmed)))) {
                    const titleMatch = trimmed.match(/<title[^>]*>([\s\S]*?)<\/title>/i);
                    const descMatch = trimmed.match(/<meta[^>]+name=["']description["'][^>]+content=["']([^"']*)["']/i) || trimmed.match(/<meta[^>]+content=["']([^"']*)["'][^>]+name=["']description["']/i);
                    const canonMatch = trimmed.match(/<link[^>]+rel=["']canonical["'][^>]+href=["']([^"']*)["']/i);
                    const jsonLdMatch = trimmed.match(/<script[^>]+application\/ld\+json[^>]*>([\s\S]*?)<\/script>/i);
                    const rawJson = isPureJsonLd ? trimmed : (jsonLdMatch ? jsonLdMatch[1].trim() : '');
                    let schemaBadge = '';
                    if (rawJson) {
                        try {
                            const parsed = JSON.parse(rawJson);
                            const t = parsed['@type'] || 'StructuredData';
                            schemaBadge = `<div style="margin-top:12px;padding:10px 14px;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:10px;font-size:12px;color:#065f46;"><strong>✓ Valid Schema.org JSON-LD Detected:</strong> <code>@type: ${String(t)}</code> — Eligible for Google Rich Results</div>`;
                        } catch (je) {
                            schemaBadge = `<div style="margin-top:12px;padding:10px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;font-size:12px;color:#991b1b;"><strong>✗ JSON-LD Syntax Error:</strong> ${je.message}</div>`;
                        }
                    }
                    const seoTitle = titleMatch ? titleMatch[1].trim() : (isRobotsTxt ? 'robots.txt Crawl Directives Inspector' : (isXmlSitemap ? 'XML Sitemap Validator' : 'Structured Data Rich Result Preview'));
                    const seoDesc = descMatch ? descMatch[1].trim() : 'Edit the title, meta description, canonical URL, or JSON-LD structured data on the left and click Run to test SERP rendering and length limits.';
                    const seoUrl = canonMatch ? canonMatch[1].trim() : 'https://learning.nectradigital.com/seo-practical-demo';
                    const tLen = seoTitle.length;
                    const dLen = seoDesc.length;
                    setTryItPreview(`<!DOCTYPE html><html><head><meta charset="UTF-8"><style>body{font-family:Arial,sans-serif;background:#f8fafc;margin:0;padding:24px;color:#1e293b}.serp{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:22px;box-shadow:0 4px 16px rgba(15,23,42,.05);max-width:650px}.crumb{font-size:12px;color:#475569;margin-bottom:4px}.t{font-size:20px;color:#1a0dab;line-height:1.3;margin:0 0 6px;font-weight:500}.d{font-size:14px;color:#4d5156;line-height:1.55;margin:0}.meters{display:flex;gap:12px;margin-top:16px;flex-wrap:wrap}.pill{font-size:12px;padding:6px 12px;border-radius:999px;font-weight:700;background:#f1f5f9;border:1px solid #cbd5e1}pre{margin-top:14px;background:#0f172a;color:#e2e8f0;padding:14px;border-radius:10px;font-size:12px;overflow:auto}</style></head><body><div class="serp"><div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#059669;margin-bottom:10px">Live Google SERP & Technical SEO Inspector</div><div class="crumb">${seoUrl}</div><div class="t">${seoTitle}</div><p class="d">${seoDesc}</p><div class="meters"><span class="pill">Title: ${tLen} chars ${tLen >= 30 && tLen <= 60 ? '✓ Optimal' : '(Target 50–60)'}</span><span class="pill">Meta Desc: ${dLen} chars ${dLen >= 120 && dLen <= 160 ? '✓ Optimal' : '(Target 140–160)'}</span></div>${schemaBadge}${(isRobotsTxt || isXmlSitemap) ? '<pre>' + trimmed.replace(/</g, '&lt;') + '</pre>' : ''}</div></body></html>`);
                } else {
                    setTryItPreview(code);
                }
            } catch (e) {
                console.error('Frame error:', e);
            }
            if (status) {
                status.innerText = 'Rendered in Viewport';
                status.className = 'text-xs tryit-accent font-bold';
            }
        } else if (currentTryItLang === 'css') {
            switchTryItOutputView(window.currentTryItOutputView === 'split' ? 'split' : 'preview');
            try {
                const cssContent = code.includes('<style>')
                    ? code
                    : `<style>${code}</style><div style="padding:28px; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; color:#1e293b;"><h1>CSS Preview Rendering</h1><p>Your styles are actively applied to this preview document.</p><div class="card" style="margin-top:15px; padding:15px; border:1px solid #cbd5e1; border-radius:8px; background:white;">Sample Component Box</div></div>`;
                setTryItPreview(cssContent);
            } catch (e) {
                console.error('Frame error:', e);
            }
            if (status) {
                status.innerText = 'Rendered in Viewport';
                status.className = 'text-xs tryit-accent font-bold';
            }
        } else if (currentTryItLang === 'react' || currentTryItLang === 'jsx' || currentTryItLang === 'next' || currentTryItLang === 'nextjs' || code.includes('import React') || code.includes('useState(') || code.includes('useReducer(') || code.includes('createRoot(') || /<\s*[A-Z][A-Za-z0-9_]*\s*[\/>]/.test(code)) {
            switchTryItOutputView(window.currentTryItOutputView || 'preview');
            try {
                let compCode = code
                    .replace(/import\s+.*?from\s+['"][^'"]+['"];?/g, '')
                    .replace(/export\s+default\s+/g, '')
                    .replace(/export\s+(const|let|var|function|class)\s+/g, '$1 ')
                    .replace(/['"]use\s+client['"];?/g, '')
                    .replace(/['"]use\s+server['"];?/g, '')
                    .replace(/<!--([\s\S]*?)-->/g, '{/*$1*/}');

                const funcMatches = compCode.match(/(?:function|class)\s+([A-Z][A-Za-z0-9_]*)/g) || [];
                const constMatches = compCode.match(/(?:const|let|var)\s+([A-Z][A-Za-z0-9_]*)\s*=/g) || [];
                const foundNames = [];
                funcMatches.forEach(m => {
                    const n = m.replace(/(?:function|class)\s+/, '').trim();
                    if (n && !foundNames.includes(n)) foundNames.push(n);
                });
                constMatches.forEach(m => {
                    const n = m.replace(/(?:const|let|var)\s+/, '').replace(/\s*=/, '').trim();
                    if (n && !foundNames.includes(n)) foundNames.push(n);
                });

                const commonNames = [
                    'CloudMetricsDashboard', 'SessionViewer', 'RbacGate', 'SanitizationDemo', 'SocialPreview', 'HeroBanner',
                    'Page', 'Dashboard', 'ServerActionDemo', 'OptimisticTodoList', 'AnalyticsDashboard', 'ZodRegistrationForm',
                    'StreamingDashboard', 'ParallelSlotDemo', 'InterceptedPhotoModal', 'PreloadMetrics', 'FormStatusButton', 'CacheInspector',
                    'App', 'Counter', 'ThemeCard', 'LiveClock', 'ConverterApp', 'RegistrationForm', 'TooltipMeasurement', 'ParentDashboard',
                    'AccessibleFormField', 'Tabs', 'IndustrialForm', 'CartSummary', 'ReduxDashboard', 'QuerySimulation', 'ProtectedRouteApp',
                    'VirtualizedListDemo', 'FeedbackForm', 'NextJsPostPage', 'SecurityDemo', 'PerformanceAuditCalculator', 'CustomSearchInput',
                    'UserProfileWithLoading', 'UserCard', 'TodoList'
                ];
                commonNames.forEach(n => {
                    if (!foundNames.includes(n)) foundNames.push(n);
                });

                const mountCandidatesChecks = foundNames.map(n => `if (typeof ${n} !== 'undefined') mountTarget = ${n};`).join('\n');
                const hasManualMount = compCode.includes('createRoot') || compCode.includes('.render(');

                const wrappedCode = `
(function() {
  const { useState, useEffect, useContext, useReducer, useCallback, useMemo, useRef, useTransition, useDeferredValue, useId, useLayoutEffect, useImperativeHandle, createContext } = React;

  const cache = (typeof React !== 'undefined' && React.cache) ? React.cache : function(fn) { return fn; };
  const db = {
    user: {
      findUnique: async function(args) {
        const u = { id: args?.where?.id || "usr_101", name: "Instructor NectraDigital", email: "admin@nectradigital.com", role: "ADMIN" };
        console.log("[Cached Database Query]:", JSON.stringify(u));
        return u;
      },
      findMany: async function() { return [{ id: "usr_101", name: "Instructor NectraDigital", email: "admin@nectradigital.com" }]; }
    }
  };
  const cookies = function() { return { get: function(k) { return { value: "session_token_nectra" }; }, set: function() {} }; };
  const headers = function() { return new Headers({ "user-agent": "Next.js Simulator" }); };
  const revalidatePath = function(p) { console.log("[Revalidated Path]: " + p); };
  const revalidateTag = function(t) { console.log("[Revalidated Tag]: " + t); };
  const notFound = function() { console.log("[Triggered notFound()]"); };
  const redirect = function(url) { console.log("[Triggered redirect() -> " + url + "]"); };

  const Link = function Link(props) {
    const p = Object.assign({}, props, {
      onClick: function(e) {
        if (props.onClick) props.onClick(e);
        e.preventDefault();
      }
    });
    return React.createElement("a", p, props.children);
  };
  const Image = function Image(props) {
    const p = Object.assign({}, props, { alt: props.alt || "Next.js Image" });
    return React.createElement("img", p);
  };
  const useRouter = function() {
    return {
      push: function(u) { console.log("router.push -> " + u); },
      replace: function(u) { console.log("router.replace -> " + u); },
      back: function() { console.log("router.back"); }
    };
  };
  const usePathname = function() { return "/dashboard"; };
  const useSearchParams = function() { return new URLSearchParams(); };

  try {
${compCode}

    let mountTarget = null;
${mountCandidatesChecks}

    if (!${hasManualMount ? 'true' : 'false'} && mountTarget && document.getElementById("root")) {
      const root = ReactDOM.createRoot(document.getElementById("root"));
      root.render(React.createElement(mountTarget));
    } else if (!${hasManualMount ? 'true' : 'false'} && !mountTarget && document.getElementById("root")) {
      const rootEl = document.getElementById("root");
      if (rootEl) {
        rootEl.innerHTML = '<div style="max-width:540px;margin:20px auto;background:#0f172a;border:1px solid #1e293b;border-radius:12px;padding:24px;color:#f8fafc;font-family:system-ui,-apple-system,sans-serif;box-shadow:0 10px 25px -5px rgba(0,0,0,0.3);">' +
          '<div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">' +
            '<div style="width:34px;height:34px;border-radius:8px;background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);display:flex;align-items:center;justify-content:center;color:#34d399;font-weight:900;font-size:16px;">▲</div>' +
            '<div>' +
              '<div style="font-weight:700;font-size:14px;color:#f1f5f9;">Next.js Server Module Evaluated</div>' +
              '<div style="font-size:11px;color:#34d399;font-weight:600;">Backend Caching & Data Logic Active</div>' +
            '</div>' +
          '</div>' +
          '<p style="font-size:12px;color:#94a3b8;line-height:1.6;margin:0 0 16px 0;">This module defines server-side functions (such as request memoization, database queries, or Server Actions). Check the <strong>Console</strong> tab to view live execution logs and returned data.</p>' +
          '<div style="background:#020617;border-radius:8px;padding:12px;border:1px solid #1e293b;font-family:monospace;font-size:11px;color:#cbd5e1;">' +
            '<div style="color:#64748b;margin-bottom:4px;">// Module Status</div>' +
            '<div style="color:#34d399;">✓ Server module evaluated successfully with 0 errors</div>' +
          '</div>' +
        '</div>';
      }
      if (typeof getCurrentUser === 'function') {
        getCurrentUser("usr_101").then(function(res) {
          console.log("[Execution Result]:", JSON.stringify(res));
        }).catch(function(e) { console.error(e.message); });
      }
    }
  } catch (runErr) {
    console.error("Runtime Exception:", runErr.message);
    const rootEl = document.getElementById("root");
    if (rootEl) {
      rootEl.innerHTML = '<div style="color:#b91c1c;background:#fef2f2;border:1px solid #fecaca;padding:16px;border-radius:8px;font-family:monospace;font-size:13px;line-height:1.5;"><strong style="display:block;margin-bottom:6px;">⚠️ React Runtime Error:</strong>' + runErr.message + '</div>';
    }
  }
})();
`;
                const safeCodeJson = JSON.stringify(wrappedCode).replace(/<\/script/gi, '<\\/script');

                const htmlPayload = '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">' +
                    '<script>' +
                    '(function() {' +
                    '  var origLog = console.log, origWarn = console.warn, origError = console.error;' +
                    '  function shouldFilter(str) {' +
                    '    if (!str) return true;' +
                    '    var s = String(str).trim();' +
                    '    return s === "" || s === "{}" || s === "Script error." || s.indexOf("Script error") !== -1 || ' +
                    '           s.indexOf("ResizeObserver") !== -1 || s.indexOf("tailwindcss.com") !== -1 || ' +
                    '           s.indexOf("Babel transformer") !== -1 || s.indexOf("in-browser Babel") !== -1 || ' +
                    '           s.indexOf("Failed to execute \'write\' on \'Document\'") !== -1 || ' +
                    '           s.indexOf("already been declared") !== -1 || s.indexOf("Floto Widget") !== -1 || s.indexOf("jgnhj6hn8") !== -1;' +
                    '  }' +
                    '  function sendParent(level, args) {' +
                    '    var text = Array.from(args).map(function(a) {' +
                    '      if (typeof a === "object") {' +
                    '        if (a === null) return "null";' +
                    '        try {' +
                    '          if (Object.keys(a).length === 0 && Object.getOwnPropertyNames(a).length === 0) return "";' +
                    '          return JSON.stringify(a, null, 2);' +
                    '        } catch(e) { return String(a); }' +
                    '      }' +
                    '      return String(a);' +
                    '    }).filter(Boolean).join(" ");' +
                    '    if (shouldFilter(text)) return;' +
                    '    try { window.parent.postMessage({ type: "TRYIT_LOG", logType: level, text: text }, "*"); } catch(e) {}' +
                    '  }' +
                    '  console.warn = function() {' +
                    '    for (var i = 0; i < arguments.length; i++) {' +
                    '      if (shouldFilter(arguments[i])) return;' +
                    '    }' +
                    '    origWarn.apply(console, arguments);' +
                    '    sendParent("warn", arguments);' +
                    '  };' +
                    '  console.log = function() {' +
                    '    for (var i = 0; i < arguments.length; i++) {' +
                    '      if (shouldFilter(arguments[i])) return;' +
                    '    }' +
                    '    origLog.apply(console, arguments);' +
                    '    sendParent("log", arguments);' +
                    '  };' +
                    '  console.error = function() {' +
                    '    for (var i = 0; i < arguments.length; i++) {' +
                    '      if (shouldFilter(arguments[i])) return;' +
                    '    }' +
                    '    origError.apply(console, arguments);' +
                    '    sendParent("error", arguments);' +
                    '  };' +
                    '  window.onerror = function(msg, src, line) {' +
                    '    var s = String(msg || "");' +
                    '    if (shouldFilter(s)) return;' +
                    '    sendParent("error", [s + (line ? " (Line " + line + ")" : "")]);' +
                    '  };' +
                    '})();' +
                    '<' + '/script>' +
                    '<script src="https://cdnjs.cloudflare.com/ajax/libs/react/18.2.0/umd/react.production.min.js"><' + '/script>' +
                    '<script src="https://cdnjs.cloudflare.com/ajax/libs/react-dom/18.2.0/umd/react-dom.production.min.js"><' + '/script>' +
                    '<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-standalone/7.23.10/babel.min.js"><' + '/script>' +
                    '<script src="https://cdn.tailwindcss.com"><' + '/script>' +
                    '<style>body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; padding: 20px; background: #f8fafc; color: #0f172a; margin: 0; } @keyframes tryit-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>' +
                    '</head><body>' +
                    '<div id="root">' +
                    '  <div id="tryitLoader" style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:180px; color:#64748b; font-family:system-ui, sans-serif;">' +
                    '    <div style="width:30px; height:30px; border:3px solid #e2e8f0; border-top-color:#5834b4; border-radius:50%; animation:tryit-spin 0.8s linear infinite; margin-bottom:12px;"></div>' +
                    '    <div style="font-size:13px; font-weight:600; color:#334155;">Compiling & Mounting Component...</div>' +
                    '  </div>' +
                    '</div>' +
                    '<script>' +
                    '(function() {' +
                    '  if (window.__bootReactStarted) return;' +
                    '  window.__bootReactStarted = true;' +
                    '  var snippetCode = ' + safeCodeJson + ';' +
                    '  function bootReact() {' +
                    '    if (typeof window.React === "undefined" || typeof window.ReactDOM === "undefined" || typeof window.Babel === "undefined") {' +
                    '      setTimeout(bootReact, 30); return;' +
                    '    }' +
                    '    try {' +
                    '      var compiled = Babel.transform(snippetCode, { presets: [["typescript", { isTSX: true, allExtensions: true }], "react", "env"], filename: "tryit.tsx" }).code;' +
                    '      var loader = document.getElementById("tryitLoader"); if (loader) loader.remove();' +
                    '      var fn = new Function("React", "ReactDOM", compiled);' +
                    '      fn(window.React, window.ReactDOM);' +
                    '      console.log("Component mounted successfully in Live Viewport.");' +
                    '    } catch(compileErr) {' +
                    '      console.error("Compilation Error:", compileErr.message);' +
                    '      var rootEl = document.getElementById("root");' +
                    '      if (rootEl) {' +
                    '        rootEl.innerHTML = "<div style=\\"color:#b91c1c;background:#fef2f2;border:1px solid #fecaca;padding:16px;border-radius:8px;font-family:monospace;font-size:13px;line-height:1.5;\\"><strong style=\\"display:block;margin-bottom:6px;\\">⚠️ Compilation Error:</strong>" + compileErr.message + "</div>";' +
                    '      }' +
                    '    }' +
                    '  }' +
                    '  if (document.readyState === "complete" || document.readyState === "interactive") { bootReact(); } else { window.addEventListener("DOMContentLoaded", bootReact); }' +
                    '})();' +
                    '<' + '/script>' +
                    '</body></html>';

                setTryItPreview(htmlPayload);
            } catch (e) {
                console.error('Frame error:', e);
            }
            if (status) {
                status.innerText = 'Rendered Live Component';
                status.className = 'text-xs tryit-accent font-bold';
            }
        } else if (currentTryItLang === 'angular' || code.includes('ng-app') || code.includes('angular.module') || code.includes('ng-controller') || code.includes('ng-model')) {
            switchTryItOutputView(window.currentTryItOutputView || 'preview');
            try {
                let angularSnippet = code.replace(/\\(\$[a-zA-Z0-9_]*)/g, function (m, p1) { return p1; });

                const consoleShim = '<script>' +
                    '(function() {' +
                    '  var origLog = console.log, origWarn = console.warn, origError = console.error;' +
                    '  function shouldFilter(str) {' +
                    '    if (!str) return true;' +
                    '    var s = String(str).trim();' +
                    '    return s === "" || s === "{}" || s === "Script error." || s.indexOf("Script error") !== -1 || ' +
                    '           s.indexOf("ResizeObserver") !== -1 || s.indexOf("tailwindcss.com") !== -1 || ' +
                    '           s.indexOf("Babel transformer") !== -1 || s.indexOf("in-browser Babel") !== -1 || ' +
                    '           s.indexOf("Failed to execute \'write\' on \'Document\'") !== -1 || ' +
                    '           s.indexOf("already been declared") !== -1 || s.indexOf("Floto Widget") !== -1 || s.indexOf("jgnhj6hn8") !== -1;' +
                    '  }' +
                    '  function sendParent(level, args) {' +
                    '    var text = Array.from(args).map(function(a) {' +
                    '      if (typeof a === "object") {' +
                    '        if (a === null) return "null";' +
                    '        try { return JSON.stringify(a, null, 2); } catch(e) { return String(a); }' +
                    '      }' +
                    '      return String(a);' +
                    '    }).join(" ");' +
                    '    if (shouldFilter(text)) return;' +
                    '    try { window.parent.postMessage({ type: "TRYIT_LOG", logType: level, text: text }, "*"); } catch(e) {}' +
                    '  }' +
                    '  console.warn = function() {' +
                    '    for (var i = 0; i < arguments.length; i++) {' +
                    '      if (shouldFilter(arguments[i])) return;' +
                    '    }' +
                    '    origWarn.apply(console, arguments); sendParent("warn", arguments);' +
                    '  };' +
                    '  console.log = function() {' +
                    '    for (var i = 0; i < arguments.length; i++) {' +
                    '      if (shouldFilter(arguments[i])) return;' +
                    '    }' +
                    '    origLog.apply(console, arguments); sendParent("log", arguments);' +
                    '  };' +
                    '  console.error = function() {' +
                    '    for (var i = 0; i < arguments.length; i++) {' +
                    '      if (shouldFilter(arguments[i])) return;' +
                    '    }' +
                    '    origError.apply(console, arguments); sendParent("error", arguments);' +
                    '  };' +
                    '  window.onerror = function(msg, src, line) {' +
                    '    var s = String(msg || "");' +
                    '    if (shouldFilter(s)) return;' +
                    '    sendParent("error", [s + (line ? " (Line " + line + ")" : "")]);' +
                    '  };' +
                    '})();' +
                    '<' + '/script>';

                let fullPage = '';
                if (angularSnippet.includes('<html') || angularSnippet.includes('<!DOCTYPE')) {
                    if (!angularSnippet.includes('angular.js') && !angularSnippet.includes('angular.min.js')) {
                        angularSnippet = angularSnippet.replace('<head>', '<head><script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.8.3/angular.min.js"><\/script>');
                    }
                    if (!angularSnippet.includes('tailwindcss.com')) {
                        angularSnippet = angularSnippet.replace('<head>', '<head><script src="https://cdn.tailwindcss.com"><\/script>');
                    }
                    angularSnippet = angularSnippet.replace('<head>', '<head>' + consoleShim);
                    fullPage = angularSnippet;
                } else {
                    fullPage = '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">' +
                        consoleShim +
                        '<script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.8.3/angular.min.js"><\/script>' +
                        '<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-sanitize/1.8.3/angular-sanitize.min.js"><\/script>' +
                        '<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-animate/1.8.3/angular-animate.min.js"><\/script>' +
                        '<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-route/1.8.3/angular-route.min.js"><\/script>' +
                        '<script src="https://cdn.tailwindcss.com"><\/script>' +
                        '<style>body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; padding: 20px; background: #f8fafc; color: #0f172a; margin: 0; }</style>' +
                        '</head><body>' +
                        angularSnippet +
                        '</body></html>';
                }

                setTryItPreview(fullPage);
            } catch (e) {
                console.error('Frame error:', e);
            }
            if (status) {
                status.innerText = 'Rendered Live AngularJS Component';
                status.className = 'text-xs tryit-accent font-bold';
            }
        } else if (currentTryItLang === 'javascript') {
            if (code.includes('<html') || code.includes('<body') || code.includes('<script')) {
                switchTryItOutputView(window.currentTryItOutputView === 'split' ? 'split' : 'preview');
                try {
                    setTryItPreview(code);
                } catch (e) {
                    console.error('Frame error:', e);
                }
                if (status) {
                    status.innerText = 'Rendered in Viewport';
                    status.className = 'text-xs tryit-accent font-bold';
                }
            } else {
                switchTryItOutputView(window.currentTryItOutputView === 'split' ? 'split' : 'preview');
                const serialized = JSON.stringify(code).replace(/</g, '\\u003c');
                setTryItPreview(`<!doctype html><meta charset="utf-8"><pre id="output"></pre><script>
                    const output = document.getElementById('output');
                    function log(...values) {
                        const message = values.map(String).join(' ').slice(0, 4000);
                        if (output.textContent.length < 32000) output.textContent += message + '\\n';
                        parent.postMessage({ type: 'TRYIT_LOG', logType: 'log', text: message }, '*');
                    }
                    console.log = console.info = console.warn = console.error = log;
                    try { new Function(${serialized})(); }
                    catch (error) { log(error.name + ': ' + error.message); }
                <` + '/script>');
                if (status) status.innerText = 'Browser preview';
            }
        } else if (currentTryItLang === 'python') {
            showCourseRunOutput('run', 'Starting the browser Python runtime…');
            runBrowserPython(code, showCourseRunOutput);
        } else if (currentTryItLang === 'c') {
            runPageInterpreter('c', code);
        } else if (currentTryItLang === 'csharp') {
            runPageInterpreter('csharp', code);
        } else if (currentTryItLang === 'php' || currentTryItLang === 'laravel' || currentTryItLang === 'codeigniter') {
            showCourseRunOutput('run', 'Starting the browser PHP runtime…');
            runBrowserPhp(code, showCourseRunOutput);
        } else if (currentTryItLang === 'sql' || currentTryItLang === 'mysql' || currentTryItLang === 'postgresql') {
            showCourseRunOutput('run', 'Running the SQL example…');
            runToolSample('sql', code, showCourseRunOutput);
        } else if (currentTryItLang === 'mongodb') {
            showCourseRunOutput('run', 'Running the MongoDB example…');
            runToolSample('mongo', code, showCourseRunOutput);
        } else if (currentTryItLang === 'nodejs' || currentTryItLang === 'typescript') {
            showCourseRunOutput('run', 'Running the Node.js example…');
            runToolSample('node', code, showCourseRunOutput);
        } else {
            showCourseRunOutput('info', 'Run this example locally. Copy the code into your development environment and use the commands in the lesson.');
            if (status) status.innerText = 'Local execution';
        }
    }

    window.openTryIt = openTryIt;
    window.closeTryItModal = closeTryItModal;
    window.executeTryItCode = executeTryItCode;
    window.resetTryItCode = resetTryItCode;
    window.clearTryItCode = clearTryItCode;
    window.copyTryItCode = copyTryItCode;
    window.copyTryItOutput = copyTryItOutput;
    window.switchTryItMobileTab = switchTryItMobileTab;

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeTryItModal();
    });

    // Global Event Delegation for all "Try it Yourself" and "Copy Code" buttons
    document.addEventListener('click', function (e) {
        const tryBtn = e.target.closest('.w3-btn-try');
        if (tryBtn) {
            if (tryBtn.getAttribute('onclick') && tryBtn.getAttribute('onclick').includes('openTryIt')) {
                return;
            }
            e.preventDefault();
            e.stopPropagation();

            let pre = null;
            const box = tryBtn.closest('.w3-example-box, .tutorial-example, .nectra-code-wrap');
            if (box) {
                pre = box.querySelector('pre');
            }
            if (!pre) {
                let cursor = tryBtn.closest('.try-act-row') || tryBtn;
                let prev = cursor.previousElementSibling;
                while (prev) {
                    if (prev.tagName === 'PRE') {
                        pre = prev;
                        break;
                    }
                    if (prev.querySelector && prev.querySelector('pre')) {
                        pre = prev.querySelector('pre');
                        break;
                    }
                    prev = prev.previousElementSibling;
                }
            }

            let rawCode = '';
            let codeEl = null;
            if (pre) {
                codeEl = pre.querySelector('code') || pre;
                rawCode = (codeEl.textContent || codeEl.innerText).trim();
            }

            if (rawCode) {
                const activeCourseSlug = typeof window.courseSlug === 'string' ? window.courseSlug : '';
                const classStr = ((codeEl ? codeEl.className : '') + ' ' + (pre ? pre.className : '')).toLowerCase();
                openTryIt(rawCode, detectTryItLang(rawCode, classStr, activeCourseSlug));
            }
        }

        const copyBtn = e.target.closest('.w3-btn-copy');
        if (copyBtn && !copyBtn.id.includes('tryit')) {
            e.preventDefault();
            e.stopPropagation();

            let pre = null;
            const box = copyBtn.closest('.w3-example-box, .tutorial-example, .nectra-code-wrap');
            if (box) {
                pre = box.querySelector('pre');
            }
            if (!pre) {
                let cursor = copyBtn.closest('.try-act-row') || copyBtn;
                let prev = cursor.previousElementSibling;
                while (prev) {
                    if (prev.tagName === 'PRE') {
                        pre = prev;
                        break;
                    }
                    if (prev.querySelector && prev.querySelector('pre')) {
                        pre = prev.querySelector('pre');
                        break;
                    }
                    prev = prev.previousElementSibling;
                }
            }

            if (pre) {
                const codeEl = pre.querySelector('code') || pre;
                const rawCode = (codeEl.textContent || codeEl.innerText).trim();
                if (rawCode) {
                    navigator.clipboard.writeText(rawCode).then(() => {
                        const orig = copyBtn.textContent;
                        copyBtn.textContent = 'Copied!';
                        setTimeout(() => { copyBtn.textContent = orig; }, 1800);
                    });
                }
            }
        }
    });
})();
