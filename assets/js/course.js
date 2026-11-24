/**
 * Learn.Nectra — Course & Chapter Reader Controller
 * Synchronously hydrates syllabus & drives zero-blink chapter navigation, quizzes, and progress.
 */
(function () {
    'use strict';

    const appEl = document.getElementById('course-app');
    if (!appEl || !appEl.dataset.course) return;

    let boot = {};
    try {
        boot = JSON.parse(appEl.dataset.course);
        appEl.removeAttribute('data-course');
    } catch (e) {
        console.error('Course config parse error:', e);
        return;
    }

    const courseId = Number(boot.courseId || 0);
    const courseSlug = String(boot.courseSlug || '');
    const coursePath = String(boot.coursePath || '');
    const siteBaseUrl = String(boot.siteBaseUrl || '/');
    const courseUi = boot.ui || {};
    function withLang(url) {
        const lang = window.NECTRA_LANG;
        if (!lang || lang === 'en') return url;
        return url + (url.indexOf('?') === -1 ? '?' : '&') + 'lang=' + encodeURIComponent(lang);
    }
    const csrfToken = String(boot.csrfToken || '');
    let currentChapterId = Number(boot.activeChapterId || 0);
    let chapterRequest;
    let retryChapterId;
    let retryInitialLoad;
    let completedCount = Number(boot.completedCount || 0);
    const totalChapters = Number(boot.totalChapters || 0);
    const sections = Array.isArray(boot.sections) ? boot.sections : [];
    const items = Array.isArray(boot.items) ? boot.items : [];

    // Expose courseSlug globally for Try-It language detection
    window.courseSlug = courseSlug;

    const chapters = [];
    const chapterSlugs = {};

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function slugifyChapter(value) {
        const clean = String(value || '')
            .replace(/<[^>]*>/g, '')
            .replace(/&amp;/gi, '&')
            .replace(/&lt;/gi, '<')
            .replace(/&gt;/gi, '>')
            .replace(/&quot;/gi, '"')
            .replace(/&#0*39;|&apos;/gi, "'")
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        return clean || 'chapter';
    }

    const syllabusToggle = document.getElementById('syllabus-toggle');
    const chapterSidebar = document.getElementById('chapter-sidebar');

    function setSyllabusExpanded(expanded) {
        chapterSidebar.classList.toggle('hidden', !expanded);
        chapterSidebar.classList.toggle('flex', expanded);
        syllabusToggle.setAttribute('aria-expanded', String(expanded));
    }

    syllabusToggle?.addEventListener('click', () => {
        setSyllabusExpanded(syllabusToggle.getAttribute('aria-expanded') !== 'true');
    });

    // Synchronously hydrate syllabus list before first paint
    const chapterListEl = document.getElementById('chapter-list');
    if (chapterListEl && items.length > 0) {
        let html = '';
        let lastSectionIdx = -1;
        const doneIconSvg = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>';

        for (let i = 0; i < items.length; i++) {
            const row = items[i];
            const id = Number(row[0]);
            const orderIdx = Number(row[1]);
            const name = String(row[2] || '');
            const secIdx = Number(row[3] || 0);
            const isDone = Number(row[4] || 0) === 1;
            const englishName = String(row[5] || name);
            const isActive = id === currentChapterId;

            chapters.push(id);
            chapterSlugs[id] = slugifyChapter(englishName);

            if (secIdx !== lastSectionIdx) {
                lastSectionIdx = secIdx;
                const secTitle = sections[secIdx] || 'Tutorial';
                html += '<li class="px-4 py-2 bg-slate-100/90 text-[10px] font-black uppercase tracking-wider text-slate-500 border-t border-b border-slate-200/60 sticky top-0 z-10 topic-section-header">' +
                    escapeHtml(secTitle) +
                    '</li>';
            }

            const btnCls = isActive
                ? 'bg-emerald-50/70 border-l-4 border-emerald-600 text-emerald-900 font-bold shadow-inner'
                : 'border-l-4 border-transparent text-gray-600 hover:bg-slate-50 hover:text-emerald-700';

            const badgeCls = (isDone || isActive)
                ? 'bg-emerald-600 text-white border-transparent'
                : 'bg-slate-100 text-slate-400 border border-slate-200';

            html += '<li class="w-full chapter-list-item">' +
                '<button type="button" data-id="' + id + '" class="w-full text-left px-4 py-2.5 text-sm transition-all flex gap-3 chapter-btn ' + btnCls + '">' +
                '<span id="badge-' + id + '" class="flex-shrink-0 rounded-full flex items-center justify-center text-xs font-bold chapter-number transition-colors ' + badgeCls + '">' +
                (isDone ? doneIconSvg : orderIdx) +
                '</span>' +
                '<span class="block flex-1 chapter-title" data-en="' + escapeHtml(englishName) + '">' + escapeHtml(name) + '</span>' +
                '</button></li>';
        }

        chapterListEl.innerHTML = html;

        chapterListEl.addEventListener('click', function (e) {
            const btn = e.target.closest('.chapter-btn');
            if (!btn) return;
            const id = parseInt(btn.dataset.id, 10);
            if (!id) return;
            loadChapter(id, btn, false, true);
        });
    }

    function copyToClipboard() {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            const btn = document.getElementById('shareBtn');
            if (!btn) return;
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> <span>' + (courseUi.copied || 'Copied!') + '</span>';
            btn.classList.add('border-green-500', 'text-green-400');
            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.classList.remove('border-green-500', 'text-green-400');
            }, 2000);
        });
    }

    function highlightCodeInContainer(container) {
        if (!window.Prism) return;
        try {
            if (Prism.plugins && Prism.plugins.autoloader) {
                Prism.plugins.autoloader.languages_aliases = Object.assign(
                    Prism.plugins.autoloader.languages_aliases || {},
                    {
                        flutter: 'dart',
                        next: 'tsx',
                        react: 'jsx',
                        angular: 'javascript',
                        laravel: 'php',
                        codeigniter: 'php',
                        blade: 'php',
                        mongodb: 'javascript',
                        mongosh: 'javascript',
                        postgresql: 'sql',
                        pgsql: 'sql',
                        mysql: 'sql',
                        cs: 'csharp',
                        nodejs: 'javascript',
                        ts: 'typescript',
                        sh: 'bash',
                        shell: 'bash',
                        dockerfile: 'docker'
                    }
                );
            }
            if (container && typeof Prism.highlightAllUnder === 'function') {
                Prism.highlightAllUnder(container);
            } else if (typeof Prism.highlightAll === 'function') {
                Prism.highlightAll();
            }
        } catch (prismErr) {
            console.warn('Prism highlighting non-fatal note:', prismErr);
        }
    }

    const adLoaders = new Map();

    async function activateLessonAds(container) {
        for (const slot of container.querySelectorAll('.ad-slot-frame')) {
            for (const script of slot.querySelectorAll('script')) {
                if (!script.isConnected) break;
                const type = (script.getAttribute('type') || '').trim().toLowerCase();
                if (type && !['text/javascript', 'application/javascript', 'module'].includes(type)) continue;

                const replacement = document.createElement('script');
                for (const attribute of script.attributes) {
                    replacement.setAttribute(attribute.name, attribute.value);
                }
                replacement.textContent = script.textContent;

                try {
                    if (!replacement.src) {
                        script.replaceWith(replacement);
                        continue;
                    }

                    const url = new URL(replacement.src);
                    const sharedLoader = url.hostname === 'pagead2.googlesyndication.com'
                        && url.pathname === '/pagead/js/adsbygoogle.js';
                    if (sharedLoader && adLoaders.has(url.href)) {
                        script.remove();
                        await adLoaders.get(url.href);
                        continue;
                    }

                    const loading = new Promise((resolve, reject) => {
                        replacement.addEventListener('load', resolve, { once: true });
                        replacement.addEventListener('error', reject, { once: true });
                    });
                    if (sharedLoader) {
                        adLoaders.set(url.href, loading);
                        script.remove();
                        document.head.append(replacement);
                    } else {
                        script.replaceWith(replacement);
                    }
                    await loading;
                } catch {
                    if (adLoaders.get(replacement.src)) adLoaders.delete(replacement.src);
                    console.warn('An advertisement script could not be loaded.');
                }
            }
        }
    }

    function loadChapter(chapterId, buttonElement, isInitialLoad = false, fromSyllabus = false) {
        if (!chapterId || chapters.length === 0) return;
        chapterRequest?.abort();
        const request = new AbortController();
        chapterRequest = request;
        const loadError = document.getElementById('lesson-load-error');
        loadError.hidden = true;

        const dynamicArea = document.getElementById('dynamic-area');
        dynamicArea?.setAttribute('aria-busy', 'true');

        fetch(withLang(`${siteBaseUrl}course.php?slug=${encodeURIComponent(courseSlug)}&ajax_chapter_id=${chapterId}&course_id=${courseId}`), { signal: request.signal })
            .then(response => {
                if (!response.ok) throw new Error(`Lesson request returned ${response.status}`);
                return response.text();
            })
            .then(html => {
                if (request.signal.aborted) return;
                currentChapterId = chapterId;

                document.querySelectorAll('.chapter-btn').forEach(btn => {
                    btn.removeAttribute('aria-current');
                    btn.classList.remove('bg-emerald-50', 'bg-emerald-50/70', 'bg-blue-50/50', 'border-emerald-600', 'border-blue-600', 'text-emerald-900', 'text-blue-800', 'font-bold', 'shadow-inner');
                    btn.classList.add('border-transparent', 'text-gray-600');

                    let numBadge = btn.querySelector('.chapter-number');
                    if (numBadge && !numBadge.classList.contains('bg-green-500') && !numBadge.querySelector('svg')) {
                        numBadge.classList.remove('bg-emerald-600', 'bg-blue-600', 'text-white', 'border-transparent');
                        numBadge.classList.add('bg-slate-100', 'text-slate-400', 'border', 'border-slate-200');
                    }
                });

                if (buttonElement) {
                    buttonElement.setAttribute('aria-current', 'step');
                    buttonElement.classList.add('bg-emerald-50', 'border-emerald-600', 'text-emerald-900', 'font-bold', 'shadow-inner');
                    buttonElement.classList.remove('border-transparent', 'text-gray-600');

                    let numBadge = buttonElement.querySelector('.chapter-number');
                    if (numBadge && !numBadge.classList.contains('bg-green-500')) {
                        numBadge.classList.add('bg-emerald-600', 'text-white', 'border-transparent');
                        numBadge.classList.remove('bg-slate-100', 'text-slate-400', 'border', 'border-slate-200');
                    }
                }

                if (dynamicArea) {
                    dynamicArea.innerHTML = html;
                    void activateLessonAds(dynamicArea);
                    enhanceCodeBlocks(dynamicArea);
                    if (window.enhanceExcelSheets) window.enhanceExcelSheets(dynamicArea);
                    if (window.Prism) {
                        highlightCodeInContainer(dynamicArea);
                    } else {
                        window.addEventListener('DOMContentLoaded', function () {
                            highlightCodeInContainer(dynamicArea);
                        }, { once: true });
                        window.addEventListener('load', function () {
                            highlightCodeInContainer(dynamicArea);
                        }, { once: true });
                    }
                    dynamicArea.setAttribute('aria-busy', 'false');
                }

                if (fromSyllabus && window.innerWidth < 1024) {
                    setSyllabusExpanded(false);
                    syllabusToggle.focus({ preventScroll: true });
                }

                if (!isInitialLoad) {
                    window.history.pushState({ chapterId: chapterId }, '', `${coursePath}/${chapterId}-${chapterSlugs[chapterId] || 'chapter'}`);
                    const tutorialBox = document.getElementById('tutorial-content');
                    const top = tutorialBox?.getBoundingClientRect().top;
                    if (top !== undefined && (top < 68 || top > window.innerHeight * .55)) tutorialBox.scrollIntoView({
                        block: 'start',
                        behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'
                    });
                }

                const lessonLabel = document.getElementById('course-overview-lesson');
                const lessonTitle = buttonElement?.querySelector('.chapter-title');
                if (lessonLabel && lessonTitle) lessonLabel.textContent = lessonTitle.textContent;

                const readerLink = document.getElementById('course-overview-reader');
                if (readerLink) readerLink.href = window.location.pathname + window.location.search + '#tutorial-content';

                updateNavButtons();
            })
            .catch(err => {
                if (request.signal.aborted) return;
                console.warn('Chapter load handled error:', err);
                retryChapterId = chapterId;
                retryInitialLoad = isInitialLoad;
                loadError.hidden = false;
                dynamicArea?.setAttribute('aria-busy', 'false');
            });
    }

    function markChapterComplete(chapterId, cId) {
        const btn = document.getElementById(`btn-complete-${chapterId}`);
        if (!btn) return;
        btn.innerHTML = courseUi.saving || 'Saving...';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('action', 'mark_complete');
        formData.append('chapter_id', chapterId);
        formData.append('course_id', cId);
        formData.append('csrf_token', csrfToken);

        fetch(withLang(`${siteBaseUrl}course.php?slug=${encodeURIComponent(courseSlug)}`), {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                btn.className = "flex items-center gap-2 bg-green-50 text-green-700 font-bold py-3.5 px-8 rounded-xl cursor-default border border-green-200 shadow-sm";
                btn.innerHTML = '<svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> ' + (courseUi.completed || 'Completed');

                const badge = document.getElementById(`badge-${chapterId}`);
                if (badge) {
                    badge.className = "flex-shrink-0 rounded-full flex items-center justify-center text-xs font-bold chapter-number bg-green-500 text-white transition-colors border-transparent";
                    badge.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>';
                }

                completedCount++;
                let newPercentage = Math.round((completedCount / totalChapters) * 100);
                const progressBar = document.getElementById('progress-bar');
                const progressText = document.getElementById('progress-text');

                if (progressBar && progressText) {
                    progressBar.style.width = newPercentage + '%';
                    progressText.innerText = newPercentage + '%';
                }
            } else {
                alert(data.message);
                btn.innerHTML = courseUi.mark || 'Mark as Complete';
                btn.disabled = false;
            }
        });
    }

    function submitChapterQuiz(chapterId) {
        const form = document.getElementById(`quiz-form-${chapterId}`);
        const result = document.getElementById(`quiz-result-${chapterId}`);
        if (!form || !result) return;

        const formData = new FormData(form);
        fetch(withLang(`${siteBaseUrl}course.php?slug=${encodeURIComponent(courseSlug)}`), {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            result.classList.remove('hidden', 'bg-red-50', 'text-red-700', 'border-red-100', 'bg-green-50', 'text-green-700', 'border-green-100', 'bg-amber-50', 'text-amber-700', 'border-amber-100');
            result.classList.add('border');
            if (data.status === 'success') {
                result.classList.add(data.passed ? 'bg-green-50' : 'bg-amber-50', data.passed ? 'text-green-700' : 'text-amber-700', data.passed ? 'border-green-100' : 'border-amber-100');
                result.innerText = `${data.correct}/${data.total} correct - ${data.score}% - ${data.message}`;
                setTimeout(() => loadChapter(chapterId, document.querySelector(`button[data-id="${chapterId}"]`), true), data.passed ? 900 : 1400);
            } else {
                result.classList.add('bg-red-50', 'text-red-700', 'border-red-100');
                result.innerText = data.message || 'Quiz could not be submitted.';
            }
        })
        .catch(() => {
            result.classList.remove('hidden');
            result.className = 'rounded-lg p-4 text-sm font-bold border bg-red-50 text-red-700 border-red-100';
            result.innerText = 'Quiz could not be submitted.';
        });
    }

    function updateNavButtons() {
        if (chapters.length === 0) return;
        const currentIndex = chapters.indexOf(currentChapterId);
        const btnPrev = document.getElementById('btn-prev');
        const btnNext = document.getElementById('btn-next');

        if (btnPrev) btnPrev.classList.toggle('hidden', currentIndex <= 0);
        if (btnNext) btnNext.classList.toggle('hidden', currentIndex >= chapters.length - 1);
    }

    function navigateChapter(direction) {
        if (chapters.length === 0) return;
        const currentIndex = chapters.indexOf(currentChapterId);
        let nextId = null;

        if (direction === 'prev' && currentIndex > 0) nextId = chapters[currentIndex - 1];
        else if (direction === 'next' && currentIndex < chapters.length - 1) nextId = chapters[currentIndex + 1];

        if (nextId) {
            loadChapter(nextId, document.querySelector(`button[data-id="${nextId}"]`));
        }
    }

    window.addEventListener('popstate', function (event) {
        if (event.state && event.state.chapterId) {
            loadChapter(event.state.chapterId, document.querySelector(`button[data-id="${event.state.chapterId}"]`), true);
        }
    });

    function filterSidebarTopics() {
        const query = (document.getElementById('sidebarTopicFilter')?.value || '').toLowerCase().trim();
        const listItems = document.querySelectorAll('.chapter-list-item');
        const headers = document.querySelectorAll('.topic-section-header');

        listItems.forEach(item => {
            const titleEl = item.querySelector('.chapter-title');
            const title = (titleEl?.innerText || '').toLowerCase();
            const english = (titleEl?.getAttribute('data-en') || '').toLowerCase();
            item.style.display = (title.includes(query) || english.includes(query)) ? '' : 'none';
        });

        headers.forEach(header => {
            header.style.display = query ? 'none' : '';
        });
    }

    function enhanceCodeBlocks(container) {
        if (!container) return;
        const pres = container.querySelectorAll('pre');
        pres.forEach(pre => {
            if (pre.dataset.enhanced) return;
            pre.dataset.enhanced = 'true';

            const codeEl = pre.querySelector('code');
            const getCode = () => {
                const text = (codeEl ? (codeEl.textContent || codeEl.innerText) : (pre.textContent || pre.innerText)) || '';
                return text.trim();
            };
            if (!getCode()) return;

            const rawCode = getCode();
            const courseRunner = pre.dataset.courseRunner;
            const isTree = (typeof window.isDirectoryTree === 'function')
                ? window.isDirectoryTree(rawCode)
                : (rawCode.includes('├──') || rawCode.includes('└──') || rawCode.includes('│') || /[\u2500-\u257F]/.test(rawCode) || (/^\s*[a-zA-Z0-9_.-]+\/\s*$/m.test(rawCode) && (rawCode.includes('app/') || rawCode.includes('src/') || rawCode.includes('package.json'))));
            const isTerminalCmd = (typeof window.isTerminalCommand === 'function')
                ? window.isTerminalCommand(rawCode)
                : (!isTree && (/^\s*(?:npm|npx|pnpm|yarn|composer|flutter|dart)\s+(?![=:[{])\S+/m.test(rawCode) || /^\s*(?:cd|git|docker|mkdir|curl|pip3?|python3?|node)\s+(?![=:[{.(])\S+/m.test(rawCode) || /^\s*\$\s+\S/m.test(rawCode)));

            let lang = 'php';
            if (isTree) {
                lang = 'tree';
            } else if (isTerminalCmd) {
                lang = 'bash';
            } else if (courseSlug.includes('laravel')) {
                lang = 'laravel';
            } else if (courseSlug.includes('codeigniter')) {
                lang = 'codeigniter';
            } else if (courseSlug.includes('mongo')) {
                lang = 'mongodb';
            } else if (courseSlug.includes('postgres')) {
                lang = 'postgresql';
            } else if (courseSlug.includes('mysql')) {
                lang = 'mysql';
            } else if (courseSlug.includes('csharp') || courseSlug === 'c-sharp-tutorial') {
                lang = 'csharp';
            } else if (courseSlug === 'c-tutorial' || courseSlug.startsWith('c-programming')) {
                lang = 'c';
            } else if (courseSlug.includes('typescript')) {
                lang = 'typescript';
            } else if (courseSlug.includes('node') || courseSlug.includes('express')) {
                lang = 'nodejs';
            } else if (courseSlug.includes('dotnet') || courseSlug.includes('aspnet')) {
                lang = 'csharp';
            } else if (courseSlug.includes('ai-') || courseSlug.includes('dsa-python') || courseSlug.includes('python')) {
                lang = 'python';
            } else if (courseSlug.includes('seo') || courseSlug.includes('marketing') || courseSlug.includes('excel')) {
                lang = 'html';
            } else if (courseSlug.includes('devops')) {
                lang = 'bash';
            } else if (courseSlug.includes('next')) {
                lang = 'next';
            } else if (courseSlug.includes('react')) {
                lang = 'react';
            } else if (courseSlug.includes('angular')) {
                lang = 'angular';
            } else if (courseSlug.includes('javascript') || courseSlug.includes('js')) {
                lang = 'javascript';
            } else if (courseSlug.includes('html')) {
                lang = 'html';
            } else if (courseSlug.includes('css')) {
                lang = 'css';
            } else if (courseSlug.includes('sql')) {
                lang = 'sql';
            } else if (courseSlug.includes('flutter') || courseSlug.includes('dart')) {
                lang = 'flutter';
            }

            const classStr = ((codeEl ? codeEl.className : '') + ' ' + (pre.className || '')).toLowerCase();
            if (!isTree && !isTerminalCmd) {
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
                else if (classStr.includes('json')) lang = rawCode.includes('"@context"') ? 'html' : 'javascript';
                else if (classStr.includes('javascript') || /\blanguage-js\b/.test(classStr)) lang = (courseSlug.includes('react') || courseSlug.includes('next')) ? 'react' : (courseSlug.includes('node') ? 'nodejs' : 'javascript');
                else if (classStr.includes('html') || classStr.includes('xml')) lang = 'html';
                else if (classStr.includes('css')) lang = 'css';
                else if (classStr.includes('sql')) lang = courseSlug.includes('postgres') ? 'postgresql' : (courseSlug.includes('mysql') ? 'mysql' : 'sql');
                else if (classStr.includes('php')) lang = courseSlug.includes('laravel') ? 'laravel' : (courseSlug.includes('codeigniter') ? 'codeigniter' : 'php');
            }

            const box = pre.closest('.w3-example-box, .tutorial-example');
            const hasExistingRow = box
                ? box.querySelector('.try-act-row')
                : (pre.nextElementSibling && pre.nextElementSibling.classList && pre.nextElementSibling.classList.contains('try-act-row'));
            if (hasExistingRow) return;

            const actRow = document.createElement('div');
            actRow.className = 'try-act-row flex items-center gap-2 mt-3';

            const tryBtn = document.createElement('button');
            tryBtn.type = 'button';
            tryBtn.className = 'w3-btn-try cursor-pointer';
            if (courseRunner) {
                tryBtn.textContent = courseUi.try || 'Try it Yourself';
            } else if (isTree) {
                tryBtn.innerHTML = '<svg class="w-4 h-4 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg> ' + (courseUi.explore || 'Explore Structure') + ' &raquo;';
            } else if (lang === 'bash') {
                tryBtn.innerHTML = '<svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> ' + (courseUi.terminal || 'Run in Terminal') + ' &raquo;';
            } else {
                tryBtn.innerHTML = '<svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> ' + (courseUi.try || 'Try it Yourself') + ' &raquo;';
            }
            tryBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                const latest = getCode();
                const classes = ((codeEl ? codeEl.className : '') + ' ' + (pre.className || '')).toLowerCase();
                const detected = typeof window.detectTryItLang === 'function'
                    ? window.detectTryItLang(latest, classes, courseSlug)
                    : lang;
                if (typeof window.openTryIt === 'function') {
                    window.openTryIt(latest, courseRunner === 'terminal-local' ? 'powershell' : courseRunner ? 'python' : detected, courseRunner || null);
                }
            });

            const copyBtn = document.createElement('button');
            copyBtn.type = 'button';
            copyBtn.className = 'w3-btn-copy cursor-pointer';
            const defaultCopyLabel = isTree ? (courseUi.copyTree || 'Copy Tree') : (lang === 'bash' ? (courseUi.copyCommand || 'Copy Command') : (courseUi.copy || 'Copy Code'));
            copyBtn.textContent = defaultCopyLabel;
            copyBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                const text = getCode();
                navigator.clipboard.writeText(text).then(() => {
                    copyBtn.textContent = courseUi.copied || 'Copied!';
                    setTimeout(() => { copyBtn.textContent = defaultCopyLabel; }, 1800);
                }).catch(() => {
                    copyBtn.textContent = courseUi.copied || 'Copied!';
                    setTimeout(() => { copyBtn.textContent = defaultCopyLabel; }, 1800);
                });
            });

            actRow.appendChild(tryBtn);
            actRow.appendChild(copyBtn);

            if (box) {
                box.appendChild(actRow);
            } else {
                pre.insertAdjacentElement('afterend', actRow);
            }
        });
        wirePromptCopy(container);
    }

    function wirePromptCopy(container) {
        if (!container) return;
        container.querySelectorAll('.prompt-copy').forEach(function (btn) {
            if (btn.dataset.wired) return;
            btn.dataset.wired = '1';
            var idle = btn.textContent;
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var id = btn.getAttribute('data-prompt-id');
                var node = id ? document.getElementById(id) : null;
                var text = node ? (node.innerText || node.textContent || '') : '';
                text = text.replace(/\n{3,}/g, '\n\n').trim();
                var done = function () {
                    btn.textContent = (typeof courseUi !== 'undefined' && courseUi.copied) ? courseUi.copied : 'Copied!';
                    setTimeout(function () { btn.textContent = idle; }, 1800);
                };
                if (navigator.clipboard && text) {
                    navigator.clipboard.writeText(text).then(done).catch(done);
                } else {
                    done();
                }
            });
        });
    }

    window.copyToClipboard = copyToClipboard;
    window.highlightCodeInContainer = highlightCodeInContainer;
    window.loadChapter = loadChapter;
    document.getElementById('retry-lesson')?.addEventListener('click', () => {
        loadChapter(retryChapterId, document.querySelector(`button[data-id="${retryChapterId}"]`), retryInitialLoad);
    });
    window.markChapterComplete = markChapterComplete;
    window.submitChapterQuiz = submitChapterQuiz;
    window.updateNavButtons = updateNavButtons;
    window.navigateChapter = navigateChapter;
    window.filterSidebarTopics = filterSidebarTopics;
    window.enhanceCodeBlocks = enhanceCodeBlocks;

    function bootInitialChapter() {
        if (totalChapters > 0 && currentChapterId > 0) {
            window.history.replaceState({ chapterId: currentChapterId }, '');
            loadChapter(currentChapterId, document.querySelector(`button[data-id="${currentChapterId}"]`), true);
        }
    }

    // Start fetching initial chapter immediately in parallel with deferred scripts
    bootInitialChapter();
})();
