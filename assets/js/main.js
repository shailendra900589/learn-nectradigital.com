/**
 * Learn.Nectra — Main JavaScript
 * Professional interaction layer: scroll animations, counters, toast system,
 * dark mode, keyboard shortcuts, parallax, lazy loading, and more.
 *
 * (c) 2026 Learn.Nectra  |  Nectra Digital
 */

(function () {
  'use strict';

  /* ═══════════════════════════════════════════════════════════════════════
     0. UTILITY HELPERS
     ═══════════════════════════════════════════════════════════════════════ */

  /** Throttle helper — fires at most once per `limit` ms */
  function throttle(fn, limit) {
    let last = 0;
    return function () {
      const now = Date.now();
      if (now - last >= limit) {
        last = now;
        fn.apply(this, arguments);
      }
    };
  }

  /** Debounce helper */
  function debounce(fn, delay) {
    let timer;
    return function () {
      clearTimeout(timer);
      const ctx = this, args = arguments;
      timer = setTimeout(function () { fn.apply(ctx, args); }, delay);
    };
  }

  /** Shorthand for querySelector */
  function qs(sel, root) { return (root || document).querySelector(sel); }
  function qsa(sel, root) { return (root || document).querySelectorAll(sel); }

  /* ═══════════════════════════════════════════════════════════════════════
     1. PAGE LOAD PROGRESS BAR
     ═══════════════════════════════════════════════════════════════════════ */

  (function progressBar() {
    var bar = document.createElement('div');
    bar.id = 'nectra-progress-bar';
    Object.assign(bar.style, {
      position: 'fixed',
      top: '0',
      left: '0',
      height: '3px',
      width: '0%',
      background: 'linear-gradient(90deg, #10b981, #06b6d4)',
      zIndex: '99999',
      transition: 'width .3s ease',
      boxShadow: '0 0 8px rgba(16,185,129,.5)',
      pointerEvents: 'none'
    });
    document.documentElement.appendChild(bar);

    // Animate to ~90 % quickly
    var progress = 0;
    var iv = setInterval(function () {
      progress += (90 - progress) * 0.1;
      bar.style.width = progress + '%';
      if (progress > 88) clearInterval(iv);
    }, 80);

    window.addEventListener('load', function () {
      clearInterval(iv);
      bar.style.width = '100%';
      setTimeout(function () {
        bar.style.opacity = '0';
        setTimeout(function () { bar.remove(); }, 400);
      }, 300);
    });
  })();

  /* ═══════════════════════════════════════════════════════════════════════
     DOM-READY WRAPPER — Everything below runs after DOMContentLoaded
     ═══════════════════════════════════════════════════════════════════════ */

  document.addEventListener('DOMContentLoaded', function () {

    /* ═════════════════════════════════════════════════════════════════════
       2. SCROLL-REVEAL ANIMATION (IntersectionObserver)
       ═════════════════════════════════════════════════════════════════════ */

    (function scrollReveal() {
      var reveals = qsa('.reveal');
      if (!reveals.length) return;

      if (!('IntersectionObserver' in window)) {
        // Fallback: show everything immediately
        reveals.forEach(function (el) { el.classList.add('animate-in'); });
        return;
      }

      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('animate-in', 'revealed');
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

      reveals.forEach(function (el) { observer.observe(el); });
    })();

    /* ═════════════════════════════════════════════════════════════════════
       3. SMOOTH SCROLL TO ANCHOR LINKS
       ═════════════════════════════════════════════════════════════════════ */

    document.addEventListener('click', function (e) {
      var link = e.target.closest('a[href*="#"]');
      if (!link) return;

      var href = link.getAttribute('href');
      // Only handle same-page anchors
      var hashIndex = href.indexOf('#');
      if (hashIndex === -1) return;

      var path = href.substring(0, hashIndex);
      // Allow empty path or same page
      if (path && path !== '' && !location.href.includes(path)) return;

      var hash = href.substring(hashIndex);
      if (hash.length <= 1) return;

      var target;
      try { target = qs(hash); } catch (_) { return; }
      if (!target) return;

      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      // Update URL without jump
      history.pushState(null, '', hash);
    });

    /* ═════════════════════════════════════════════════════════════════════
       4. HEADER SHRINK ON SCROLL
       ═════════════════════════════════════════════════════════════════════ */

    (function headerShrink() {
      var header = qs('header');
      if (!header) return;

      var SHRINK_CLASS = 'header-scrolled';
      // Inject shrink styles once
      var style = document.createElement('style');
      style.textContent =
        'header.header-scrolled{box-shadow:0 1px 12px rgba(0,0,0,.08)!important;}' +
        'header.header-scrolled .flex.justify-between{height:3.25rem!important;}';
      document.head.appendChild(style);

      window.addEventListener('scroll', throttle(function () {
        header.classList.toggle(SHRINK_CLASS, window.scrollY > 60);
      }, 100), { passive: true });
    })();

    /* ═════════════════════════════════════════════════════════════════════
       5. TYPING EFFECT
       ═════════════════════════════════════════════════════════════════════ */

    (function typingEffect() {
      var el = qs('.typing-effect');
      if (!el) return;

      var text = el.textContent;
      el.textContent = '';
      el.style.visibility = 'visible';

      var i = 0;
      var speed = 55; // ms per char

      function type() {
        if (i < text.length) {
          el.textContent += text.charAt(i);
          i++;
          setTimeout(type, speed);
        }
      }

      // Start after a small delay so the user sees the beginning
      setTimeout(type, 400);
    })();

    /* ═════════════════════════════════════════════════════════════════════
       6. COUNTER ANIMATION (count-up)
       ═════════════════════════════════════════════════════════════════════ */

    (function counterAnimation() {
      var counters = qsa('.count-up');
      if (!counters.length) return;

      function animateCounter(el) {
        var target = parseInt(el.getAttribute('data-target') || el.textContent, 10);
        if (isNaN(target)) return;

        var duration = 2000; // ms
        var startTime = null;

        el.textContent = '0';

        function step(timestamp) {
          if (!startTime) startTime = timestamp;
          var progress = Math.min((timestamp - startTime) / duration, 1);
          // Ease-out quad
          var ease = 1 - (1 - progress) * (1 - progress);
          el.textContent = Math.floor(ease * target).toLocaleString();
          if (progress < 1) requestAnimationFrame(step);
          else el.textContent = target.toLocaleString();
        }

        requestAnimationFrame(step);
      }

      if (!('IntersectionObserver' in window)) {
        counters.forEach(animateCounter);
        return;
      }

      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            animateCounter(entry.target);
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.3 });

      counters.forEach(function (el) { observer.observe(el); });
    })();

    /* ═════════════════════════════════════════════════════════════════════
       7. PARALLAX EFFECT FOR HERO SECTIONS
       ═════════════════════════════════════════════════════════════════════ */

    (function parallax() {
      var hero = qs('.bg-slate-900.text-white'); // main hero section
      if (!hero) return;

      // Also match any .site-hero-light sections
      var heroes = [hero].concat(Array.from(qsa('.site-hero-light')));

      window.addEventListener('scroll', throttle(function () {
        var scrollY = window.pageYOffset;
        heroes.forEach(function (section) {
          var rect = section.getBoundingClientRect();
          if (rect.bottom < 0 || rect.top > window.innerHeight) return;
          var offset = scrollY * 0.3;
          section.style.backgroundPositionY = offset + 'px';
        });
      }, 16), { passive: true });
    })();

    /* ═════════════════════════════════════════════════════════════════════
       8. BACK TO TOP BUTTON
       ═════════════════════════════════════════════════════════════════════ */

    (function backToTop() {
      var btn = document.createElement('button');
      btn.id = 'backToTopBtn';
      btn.setAttribute('aria-label', 'Back to top');
      btn.innerHTML = '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>';
      Object.assign(btn.style, {
        position: 'fixed',
        bottom: '28px',
        right: '28px',
        zIndex: '9990',
        width: '44px',
        height: '44px',
        borderRadius: '12px',
        border: '1px solid rgba(226,232,240,.8)',
        background: 'rgba(255,255,255,.92)',
        backdropFilter: 'blur(8px)',
        color: '#334155',
        cursor: 'pointer',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        boxShadow: '0 4px 16px rgba(0,0,0,.08)',
        opacity: '0',
        transform: 'translateY(16px)',
        transition: 'opacity .3s, transform .3s',
        pointerEvents: 'none'
      });

      document.body.appendChild(btn);

      btn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });

      window.addEventListener('scroll', throttle(function () {
        var show = window.scrollY > 400;
        btn.style.opacity = show ? '1' : '0';
        btn.style.transform = show ? 'translateY(0)' : 'translateY(16px)';
        btn.style.pointerEvents = show ? 'auto' : 'none';
      }, 150), { passive: true });
    })();

    /* ═════════════════════════════════════════════════════════════════════
       9. IMAGE LAZY LOADING ENHANCEMENT
       ═════════════════════════════════════════════════════════════════════ */

    (function lazyImages() {
      // Add native lazy loading to all images that don't have it
      qsa('img:not([loading])').forEach(function (img) {
        img.setAttribute('loading', 'lazy');
        img.setAttribute('decoding', 'async');
      });

      // Fade-in effect on load
      qsa('img[loading="lazy"]').forEach(function (img) {
        if (img.complete) return;
        img.style.opacity = '0';
        img.style.transition = 'opacity .4s ease';
        img.addEventListener('load', function () {
          img.style.opacity = '1';
        }, { once: true });
      });
    })();

    /* ═════════════════════════════════════════════════════════════════════
       11. TOAST NOTIFICATION SYSTEM
       ═════════════════════════════════════════════════════════════════════ */

    (function toastSystem() {
      // Create container
      var container = document.createElement('div');
      container.id = 'nectra-toast-container';
      Object.assign(container.style, {
        position: 'fixed',
        top: '24px',
        right: '24px',
        zIndex: '99999',
        display: 'flex',
        flexDirection: 'column',
        gap: '10px',
        pointerEvents: 'none',
        maxWidth: '380px',
        width: '100%'
      });
      document.body.appendChild(container);

      // Inject toast styles
      var style = document.createElement('style');
      style.textContent =
        '.nectra-toast{pointer-events:auto;display:flex;align-items:center;gap:10px;padding:14px 18px;' +
        'border-radius:12px;font-size:14px;font-weight:600;color:#fff;backdrop-filter:blur(8px);' +
        'box-shadow:0 8px 24px rgba(0,0,0,.12);opacity:0;transform:translateX(40px);' +
        'transition:opacity .35s,transform .35s;border:1px solid rgba(255,255,255,.12);}' +
        '.nectra-toast.show{opacity:1;transform:translateX(0);}' +
        '.nectra-toast.success{background:rgba(5,150,105,.92);}' +
        '.nectra-toast.error{background:rgba(220,38,38,.92);}' +
        '.nectra-toast.info{background:rgba(37,99,235,.92);}' +
        '.nectra-toast.warning{background:rgba(217,119,6,.92);}' +
        '.nectra-toast .toast-icon{flex-shrink:0;width:20px;height:20px;}' +
        '.nectra-toast .toast-close{margin-left:auto;cursor:pointer;opacity:.7;font-size:16px;' +
        'background:none;border:none;color:inherit;padding:0 0 0 8px;}' +
        '.nectra-toast .toast-close:hover{opacity:1;}';
      document.head.appendChild(style);

      var icons = {
        success: '<svg class="toast-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>',
        error: '<svg class="toast-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M15 9l-6 6M9 9l6 6"/></svg>',
        info: '<svg class="toast-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 16v-4M12 8h.01"/></svg>',
        warning: '<svg class="toast-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86l-8.6 14.86A1 1 0 002.56 20h18.88a1 1 0 00.87-1.28l-8.6-14.86a1 1 0 00-1.72 0z"/></svg>'
      };

      /**
       * Show a toast notification.
       * @param {string} message - Text to display.
       * @param {string} [type='success'] - success | error | info | warning
       * @param {number} [duration=4000] - ms before auto-dismiss
       */
      window.showToast = function (message, type, duration) {
        type = type || 'success';
        duration = duration || 4000;

        var toast = document.createElement('div');
        toast.className = 'nectra-toast ' + type;
        toast.innerHTML = (icons[type] || '') +
          '<span>' + message + '</span>' +
          '<button class="toast-close" aria-label="Close">&times;</button>';

        container.appendChild(toast);

        // Trigger animation
        requestAnimationFrame(function () {
          requestAnimationFrame(function () { toast.classList.add('show'); });
        });

        function dismiss() {
          toast.classList.remove('show');
          setTimeout(function () { toast.remove(); }, 350);
        }

        toast.querySelector('.toast-close').addEventListener('click', dismiss);
        setTimeout(dismiss, duration);
      };
    })();

    /* ═════════════════════════════════════════════════════════════════════
       12. NAVBAR ACTIVE LINK HIGHLIGHT
       ═════════════════════════════════════════════════════════════════════ */

    (function navActiveHighlight() {
      var currentPath = location.pathname.replace(/\/$/, '').split('/').pop() || 'index';

      qsa('header nav a, #mobileMenu a').forEach(function (link) {
        var href = link.getAttribute('href');
        if (!href) return;
        var linkPath = href.replace(/\/$/, '').split('/').pop().split('#')[0] || 'index';

        if (linkPath === currentPath) {
          link.classList.add('text-emerald-600');
          link.classList.remove('text-slate-600', 'text-slate-700');
        }
      });
    })();

    /* ═════════════════════════════════════════════════════════════════════
       13. CODE BLOCK COPY BUTTON INJECTION
       ═════════════════════════════════════════════════════════════════════ */

    (function codeBlockCopy() {
      // Inject styles
      var style = document.createElement('style');
      style.textContent =
        '.nectra-code-wrap{position:relative;}' +
        '.nectra-copy-btn{position:absolute;top:8px;right:8px;padding:4px 10px;' +
        'font-size:11px;font-weight:700;color:#94a3b8;background:rgba(30,41,59,.8);' +
        'border:1px solid rgba(100,116,139,.3);border-radius:6px;cursor:pointer;' +
        'opacity:0;transition:opacity .2s;z-index:5;backdrop-filter:blur(4px);}' +
        '.nectra-code-wrap:hover .nectra-copy-btn{opacity:1;}' +
        '.nectra-copy-btn:hover{color:#fff;background:rgba(16,185,129,.7);border-color:transparent;}' +
        '.nectra-copy-btn.copied{color:#fff;background:rgba(16,185,129,.85);}';
      document.head.appendChild(style);

      qsa('pre code').forEach(function (codeEl) {
        var pre = codeEl.closest('pre') || codeEl;
        // Skip if inside Try It modal or already wrapped
        if (pre.closest('#tryItModal')) return;
        if (pre.parentElement && pre.parentElement.classList.contains('nectra-code-wrap')) return;

        var wrapper = document.createElement('div');
        wrapper.className = 'nectra-code-wrap';
        pre.parentNode.insertBefore(wrapper, pre);
        wrapper.appendChild(pre);

        var btn = document.createElement('button');
        btn.className = 'nectra-copy-btn';
        btn.textContent = 'Copy';
        btn.setAttribute('aria-label', 'Copy code to clipboard');
        wrapper.appendChild(btn);

        btn.addEventListener('click', function () {
          var text = codeEl.textContent;
          navigator.clipboard.writeText(text).then(function () {
            btn.textContent = 'Copied!';
            btn.classList.add('copied');
            setTimeout(function () {
              btn.textContent = 'Copy';
              btn.classList.remove('copied');
            }, 2000);
          }).catch(function () {
            // Fallback
            var textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.cssText = 'position:fixed;opacity:0';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            textarea.remove();
            btn.textContent = 'Copied!';
            btn.classList.add('copied');
            setTimeout(function () {
              btn.textContent = 'Copy';
              btn.classList.remove('copied');
            }, 2000);
          });
        });
      });
    })();

    /* ═════════════════════════════════════════════════════════════════════
       14. DARK MODE TOGGLE (localStorage-persisted)
       ═════════════════════════════════════════════════════════════════════ */

    (function darkMode() {
      var STORAGE_KEY = 'nectra-theme';

      // Tailwind opacity variants (bg-slate-50/70) and hover: variants are separate classes,
      // so match class tokens by prefix and keep light-only islands untouched.
      var KEEP_LIGHT = ':not(:where(figure.bg-white,.nd-visual,#mobileMenu,.prompt-lab,.prompt-keep,.prompt-models,.prompt-pieces,.prompt-bubble) *)';
      function cls(token) {
        return ['[class~="' + token + '"]', '[class^="' + token + '/"]', '[class*=" ' + token + '/"]'].map(function (s) {
          return 'body.dark-mode ' + s + KEEP_LIGHT;
        }).join(',');
      }
      function rule(tokens, decl) {
        return tokens.map(cls).join(',') + '{' + decl + '}';
      }
      function hoverRule(tokens, decl) {
        return tokens.map(function (t) {
          return 'body.dark-mode [class*="hover:' + t + '"]:hover' + KEEP_LIGHT;
        }).join(',') + '{' + decl + '}';
      }
      var tints = {
        red: ['239,68,68', '#fca5a5'], orange: ['249,115,22', '#fdba74'], amber: ['245,158,11', '#fcd34d'],
        yellow: ['234,179,8', '#fde047'], lime: ['132,204,22', '#bef264'], green: ['34,197,94', '#86efac'],
        emerald: ['16,185,129', '#6ee7b7'], teal: ['20,184,166', '#5eead4'], cyan: ['6,182,212', '#67e8f9'],
        sky: ['14,165,233', '#7dd3fc'], blue: ['59,130,246', '#93c5fd'], indigo: ['99,102,241', '#a5b4fc'],
        violet: ['139,92,246', '#c4b5fd'], purple: ['168,85,247', '#d8b4fe'], fuchsia: ['217,70,239', '#f0abfc'],
        pink: ['236,72,153', '#f9a8d4'], rose: ['244,63,94', '#fda4af']
      };
      var tintCss = '';
      Object.keys(tints).forEach(function (c) {
        var rgb = tints[c][0], light = tints[c][1];
        tintCss +=
          rule(['bg-' + c + '-50', 'bg-' + c + '-100'], 'background-color:rgba(' + rgb + ',.14)!important;') +
          rule(['bg-' + c + '-200'], 'background-color:rgba(' + rgb + ',.24)!important;') +
          rule(['border-' + c + '-100', 'border-' + c + '-200', 'border-' + c + '-300'], 'border-color:rgba(' + rgb + ',.35)!important;') +
          rule(['text-' + c + '-600', 'text-' + c + '-700', 'text-' + c + '-800', 'text-' + c + '-900', 'text-' + c + '-950'], 'color:' + light + '!important;') +
          hoverRule(['bg-' + c + '-50', 'bg-' + c + '-100'], 'background-color:rgba(' + rgb + ',.18)!important;') +
          hoverRule(['text-' + c + '-6', 'text-' + c + '-7', 'text-' + c + '-8', 'text-' + c + '-9'], 'color:' + light + '!important;');
      });

      // Inject dark-mode stylesheet
      var style = document.createElement('style');
      style.id = 'nectra-dark-styles';
      style.textContent =
        'body.dark-mode{background:#0f172a!important;color:#e2e8f0!important;}' +
        rule(['bg-white', 'bg-gray-50', 'bg-slate-100', 'bg-gray-100'], 'background-color:#1e293b!important;') +
        rule(['bg-slate-50'], 'background-color:#0f172a!important;') +
        rule(['bg-slate-200', 'bg-gray-200'], 'background-color:#334155!important;') +
        rule(['text-slate-900', 'text-gray-900', 'text-black'], 'color:#f1f5f9!important;') +
        rule(['text-slate-800', 'text-gray-800'], 'color:#e2e8f0!important;') +
        rule(['text-slate-700', 'text-gray-700'], 'color:#cbd5e1!important;') +
        rule(['text-slate-600', 'text-gray-600', 'text-slate-500', 'text-gray-500'], 'color:#a3b1c6!important;') +
        rule(['border-slate-100', 'border-gray-100', 'border-slate-200', 'border-gray-200', 'border-slate-300', 'border-gray-300'], 'border-color:#334155!important;') +
        'body.dark-mode [class*="divide-slate-"]>*+*,body.dark-mode [class*="divide-gray-"]>*+*{border-color:#334155!important;}' +
        hoverRule(['bg-white', 'bg-slate-50', 'bg-gray-50', 'bg-slate-100', 'bg-gray-100'], 'background-color:#273449!important;') +
        hoverRule(['text-slate-9', 'text-slate-8', 'text-gray-9', 'text-gray-8'], 'color:#f8fafc!important;') +
        tintCss +
        'body.dark-mode header{background:rgba(15,23,42,.95)!important;border-color:#1e293b!important;}' +
        'body.dark-mode figure.bg-white{background:#ffffff!important;color:#0f172a!important;}' +
        'body.dark-mode footer{background:#0f172a!important;border-color:#1e293b!important;}' +
        'body.dark-mode input,body.dark-mode textarea{background:#1e293b!important;color:#e2e8f0!important;border-color:#334155!important;}' +
        'body.dark-mode .edu-soft-card{background:#1e293b!important;border-color:#334155!important;}' +
        'body.dark-mode .learning-surface{background:#0f172a!important;}' +
        'body.dark-mode #backToTopBtn{background:rgba(30,41,59,.92)!important;color:#e2e8f0!important;border-color:#334155!important;}' +
        'body.dark-mode .nd-visual{background:#ffffff!important;color:#0f172a!important;}' +
        'body.dark-mode .nd-visual input,body.dark-mode .nd-visual textarea,body.dark-mode .nd-visual select{background:#ffffff!important;color:#0f172a!important;border-color:#cbd5e1!important;}';
      document.head.appendChild(style);

      // Create toggle button
      var btn = document.createElement('button');
      btn.id = 'darkModeToggle';
      btn.setAttribute('aria-label', 'Toggle dark mode');
      btn.innerHTML =
        '<svg class="dm-sun" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>' +
        '<svg class="dm-moon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>';
      Object.assign(btn.style, {
        position: 'fixed',
        bottom: '28px',
        right: '82px',
        zIndex: '9990',
        width: '44px',
        height: '44px',
        borderRadius: '12px',
        border: '1px solid rgba(226,232,240,.8)',
        background: 'rgba(255,255,255,.92)',
        backdropFilter: 'blur(8px)',
        color: '#334155',
        cursor: 'pointer',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        boxShadow: '0 4px 16px rgba(0,0,0,.08)',
        transition: 'background .3s, color .3s'
      });

      document.body.appendChild(btn);

      function applyMode(dark) {
        document.body.classList.toggle('dark-mode', dark);
        var sun = btn.querySelector('.dm-sun');
        var moon = btn.querySelector('.dm-moon');
        if (sun) sun.style.display = dark ? 'none' : 'block';
        if (moon) moon.style.display = dark ? 'block' : 'none';

        if (dark) {
          btn.style.background = 'rgba(30,41,59,.92)';
          btn.style.color = '#e2e8f0';
          btn.style.borderColor = '#334155';
        } else {
          btn.style.background = 'rgba(255,255,255,.92)';
          btn.style.color = '#334155';
          btn.style.borderColor = 'rgba(226,232,240,.8)';
        }
      }

      // Light by default; dark only after the visitor picks it.
      try { localStorage.removeItem('nectra-dark-mode'); } catch (e) {}
      var stored = null;
      try { stored = localStorage.getItem(STORAGE_KEY); } catch (e) {}
      applyMode(stored === 'dark');

      btn.addEventListener('click', function () {
        var isDark = !document.body.classList.contains('dark-mode');
        applyMode(isDark);
        try { localStorage.setItem(STORAGE_KEY, isDark ? 'dark' : 'light'); } catch (e) {}
      });
    })();

    /* ═════════════════════════════════════════════════════════════════════
       15. SEARCH INPUT ENHANCEMENT
       ═════════════════════════════════════════════════════════════════════ */

    (function searchEnhancement() {
      var input = qs('#searchInput');
      if (!input) return;

      var wrapper = input.closest('.search-field') || input.parentElement;
      var badge = document.getElementById('searchKbd');
      if (badge && /Mac|iPhone|iPad/.test(navigator.platform || navigator.userAgent)) {
        badge.textContent = '⌘K';
      }

      // --- Clear button ---
      var clearBtn = document.createElement('button');
      clearBtn.type = 'button';
      clearBtn.setAttribute('aria-label', 'Clear search');
      clearBtn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M18 6L6 18M6 6l12 12"/></svg>';
      Object.assign(clearBtn.style, {
        position: 'absolute',
        right: '10px',
        top: '50%',
        transform: 'translateY(-50%)',
        background: 'none',
        border: 'none',
        cursor: 'pointer',
        color: '#94a3b8',
        padding: '4px',
        display: 'none',
        zIndex: '3'
      });
      wrapper.appendChild(clearBtn);

      clearBtn.addEventListener('click', function () {
        input.value = '';
        input.focus();
        clearBtn.style.display = 'none';
        if (badge) badge.style.display = '';
        // Hide search results
        var results = qs('#searchResults');
        if (results) { results.classList.add('hidden'); results.innerHTML = ''; }
      });

      input.addEventListener('input', function () {
        var hasVal = input.value.length > 0;
        clearBtn.style.display = hasVal ? 'block' : 'none';
        if (badge) badge.style.display = hasVal ? 'none' : '';
      });

      input.addEventListener('focus', function () {
        if (input.value.length > 0) {
          clearBtn.style.display = 'block';
          if (badge) badge.style.display = 'none';
        }
        // Subtle glow — class can be styled in CSS
        wrapper.classList.add('search-focused');
      });

      input.addEventListener('blur', function () {
        wrapper.classList.remove('search-focused');
        if (input.value.length === 0) {
          clearBtn.style.display = 'none';
          if (badge) badge.style.display = '';
        }
      });
    })();

    /* ═════════════════════════════════════════════════════════════════════
       16. KEYBOARD SHORTCUTS — Ctrl+K for search focus
       ═════════════════════════════════════════════════════════════════════ */

    (function keyboardShortcuts() {
      document.addEventListener('keydown', function (e) {
        // Ctrl+K / Cmd+K → focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
          e.preventDefault();
          var searchInput = qs('#searchInput');
          var desktopVisible = searchInput && searchInput.getClientRects().length > 0;
          if (desktopVisible) {
            searchInput.focus();
            searchInput.select();
            return;
          }
          var mobileSearch = qs('#mobileSearchInput');
          if (mobileSearch && typeof window.setMobileMenu === 'function') {
            window.setMobileMenu(true);
            mobileSearch.focus();
          }
        }
      });
    })();

    const catalogQuery = qs('#catalog-query');
    if (catalogQuery) {
      const cards = qsa('.catalog-card');
      const empty = qs('#catalog-empty');
      const count = qs('#catalog-count');
      catalogQuery.closest('.catalog-filter').hidden = false;
      catalogQuery.addEventListener('input', function () {
        const query = catalogQuery.value.trim().toLowerCase();
        let visible = 0;

        cards.forEach(function (card) {
          card.hidden = !card.textContent.toLowerCase().includes(query);
          if (!card.hidden) visible++;
        });

        empty.hidden = visible > 0;
        count.textContent = visible + (visible === 1 ? ' course shown' : ' courses shown');
      });
    }

    qs('[data-library-example]')?.addEventListener('click', function () {
      window.openTryIt('const skills = ["Read", "Practice", "Build"];\nskills.forEach(skill => console.log(skill));', 'javascript');
    });

  }); // end DOMContentLoaded

})();
