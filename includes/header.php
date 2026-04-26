<?php
require_once __DIR__ . '/security.php';
secure_session_start();

// Auto-SEO Defaults
$seo_title = seo_title_text($seo_title ?? 'Free Programming Tutorials');
$seo_description = seo_description_text($seo_description ?? 'Master programming with step-by-step tutorials, structured lessons, course search, and progress tracking.');
$seo_canonical = $seo_canonical ?? current_url();
$seo_image = $seo_image ?? '';
$seo_type = $seo_type ?? 'website';
$seo_robots = $seo_robots ?? 'index, follow';
$seo_schema = $seo_schema ?? [];
$adsense_client_id = getenv('ADSENSE_CLIENT_ID') ?: '';
$load_ads = isset($pdo) ? should_show_ads($pdo) : true;
$default_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => 'Learn.Nectra',
    'url' => site_base_url(),
    'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => site_base_url() . 'search?q={search_term_string}',
        'query-input' => 'required name=search_term_string',
    ],
];
$seo_schema = array_merge([$default_schema], $seo_schema);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo h(site_base_url()); ?>">
    
    <title><?php echo h($seo_title); ?></title>
    <meta name="description" content="<?php echo h($seo_description); ?>">
    <meta name="robots" content="<?php echo h($seo_robots); ?>">
    <link rel="canonical" href="<?php echo h($seo_canonical); ?>">
    <link rel="alternate" type="application/rss+xml" title="Learn.Nectra RSS Feed" href="<?php echo h(rtrim(site_base_url(), '/')); ?>/feed.xml">
    <link rel="search" type="application/opensearchdescription+xml" title="Learn.Nectra Search" href="<?php echo h(rtrim(site_base_url(), '/')); ?>/opensearch.xml">
    <meta property="og:title" content="<?php echo h($seo_title); ?>">
    <meta property="og:description" content="<?php echo h($seo_description); ?>">
    <meta property="og:type" content="<?php echo h($seo_type); ?>">
    <meta property="og:url" content="<?php echo h($seo_canonical); ?>">
    <?php if($seo_image): ?><meta property="og:image" content="<?php echo h($seo_image); ?>"><?php endif; ?>
    <meta name="twitter:card" content="<?php echo $seo_image ? 'summary_large_image' : 'summary'; ?>">
    <meta name="twitter:title" content="<?php echo h($seo_title); ?>">
    <meta name="twitter:description" content="<?php echo h($seo_description); ?>">
    <?php if($seo_image): ?><meta name="twitter:image" content="<?php echo h($seo_image); ?>"><?php endif; ?>
    <?php if($adsense_client_id && $load_ads): ?>
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?php echo h($adsense_client_id); ?>" crossorigin="anonymous"></script>
    <?php endif; ?>
    <?php foreach($seo_schema as $schema): ?>
        <?php echo schema_script($schema); ?>
    <?php endforeach; ?>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        html { scroll-behavior: smooth; }
        /* Professional sleek scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Sidebar specific scrollbar hiding for cleaner look */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased flex flex-col min-h-screen">

    <header class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-blue-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                
                <div class="flex-shrink-0 flex items-center">
                    <a href="index" class="text-2xl font-black text-blue-700 tracking-tight flex items-center gap-2">
                        <span class="w-9 h-9 rounded-lg bg-blue-600 text-white flex items-center justify-center shadow-sm">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/></svg>
                        </span>
                        Learn<span class="text-slate-900">.Nectra</span>
                    </a>
                </div>
                
                <div class="hidden md:flex flex-1 items-center justify-center px-8 relative">
                    <div class="w-full max-w-lg relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" id="searchInput" autocomplete="off" class="block w-full pl-11 pr-4 py-2.5 border border-blue-100 rounded-full leading-5 bg-blue-50/60 placeholder-slate-500 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-all" placeholder="Search lessons, topics, or courses...">
                        
                        <div id="searchResults" class="hidden absolute z-50 mt-2 w-full bg-white shadow-2xl rounded-xl border border-gray-100 overflow-hidden backdrop-blur-lg"></div>
                    </div>
                </div>

                <nav class="hidden md:flex space-x-6 items-center">
                    <a href="index" class="text-gray-600 hover:text-blue-600 font-bold transition">Home</a>
                    <a href="index#courses" class="text-gray-600 hover:text-blue-600 font-bold transition">Courses</a>
                    <a href="contact" class="text-gray-600 hover:text-blue-600 font-bold transition">Contact</a>
                    
                    <?php if(isset($_SESSION['student_logged_in'])): ?>
                        <div class="relative group">
                            <button class="flex items-center gap-2 text-slate-800 font-bold bg-white border border-gray-200 hover:bg-gray-50 px-4 py-2 rounded-full transition shadow-sm cursor-pointer">
                                <span class="w-7 h-7 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full flex items-center justify-center text-xs shadow-inner">
                                    <?php echo h(strtoupper(substr($_SESSION['student_name'], 0, 1))); ?>
                                </span>
                                <?php echo h(explode(' ', trim($_SESSION['student_name']))[0]); ?>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 overflow-hidden transform origin-top-right scale-95 group-hover:scale-100">
                                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                                    <p class="text-sm font-bold text-gray-900 truncate"><?php echo h($_SESSION['student_name']); ?></p>
                                    <p class="text-xs text-blue-600 font-medium truncate mt-0.5">Student Account</p>
                                </div>
                                <a href="student" class="block px-5 py-3 text-sm text-slate-700 hover:bg-blue-50 font-bold transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"></path></svg>
                                    My Learning Panel
                                </a>
                                <a href="profile" class="block px-5 py-3 text-sm text-slate-700 hover:bg-blue-50 font-bold transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 7a3 3 0 11-6 0 3 3 0 016 0zM5 21a7 7 0 0114 0M17 11h4m-2-2v4"></path></svg>
                                    Profile & QR Card
                                </a>
                                <a href="billing" class="block px-5 py-3 text-sm text-slate-700 hover:bg-blue-50 font-bold transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h5M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"></path></svg>
                                    Plans & Payments
                                </a>
                                <a href="logout" class="block px-5 py-3 text-sm text-red-600 hover:bg-red-50 font-bold transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Sign Out
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                    <a href="login" class="edu-primary-btn py-2.5 px-7 transition transform hover:-translate-y-0.5">Log In</a>
                    <?php endif; ?>
                </nav>

                <button type="button" id="mobileMenuToggle" class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-200 bg-white text-slate-700 shadow-sm" aria-label="Open menu" aria-expanded="false">
                    <svg id="mobileMenuOpenIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"></path></svg>
                    <svg id="mobileMenuCloseIcon" class="hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
        <div id="mobileMenuBackdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden md:hidden"></div>
        <aside id="mobileMenu" class="fixed top-0 right-0 h-full w-80 max-w-[86vw] bg-white z-50 shadow-2xl translate-x-full transition-transform duration-200 md:hidden">
            <div class="h-16 px-5 flex items-center justify-between border-b border-slate-100">
                <span class="text-lg font-black text-slate-900">Menu</span>
                <button type="button" id="mobileMenuCloseButton" class="w-9 h-9 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600" aria-label="Close menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-5 space-y-3">
                <a href="index" class="block rounded-lg px-4 py-3 font-bold text-slate-700 hover:bg-slate-50">Home</a>
                <a href="index#courses" class="block rounded-lg px-4 py-3 font-bold text-slate-700 hover:bg-slate-50">Courses</a>
                <a href="search" class="block rounded-lg px-4 py-3 font-bold text-slate-700 hover:bg-slate-50">Search</a>
                <a href="contact" class="block rounded-lg px-4 py-3 font-bold text-slate-700 hover:bg-slate-50">Contact</a>
                <?php if(isset($_SESSION['student_logged_in'])): ?>
                    <a href="student" class="block rounded-lg px-4 py-3 font-bold text-blue-700 bg-blue-50">My Learning Panel</a>
                    <a href="profile" class="block rounded-lg px-4 py-3 font-bold text-slate-700 hover:bg-slate-50">Profile & QR Card</a>
                    <a href="billing" class="block rounded-lg px-4 py-3 font-bold text-slate-700 hover:bg-slate-50">Plans & Payments</a>
                    <a href="logout" class="block rounded-lg px-4 py-3 font-bold text-red-600 hover:bg-red-50">Sign Out</a>
                <?php else: ?>
                    <a href="login" class="block rounded-lg px-4 py-3 font-bold text-white bg-blue-600 text-center shadow-sm">Log In</a>
                <?php endif; ?>
            </div>
        </aside>
    </header>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchResults = document.getElementById('searchResults');
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileBackdrop = document.getElementById('mobileMenuBackdrop');
            const mobileToggle = document.getElementById('mobileMenuToggle');
            const mobileClose = document.getElementById('mobileMenuCloseButton');
            const mobileOpenIcon = document.getElementById('mobileMenuOpenIcon');
            const mobileCloseIcon = document.getElementById('mobileMenuCloseIcon');
            let timeout = null;

            function setMobileMenu(open) {
                if (!mobileMenu || !mobileBackdrop || !mobileToggle) return;
                mobileMenu.classList.toggle('translate-x-full', !open);
                mobileBackdrop.classList.toggle('hidden', !open);
                mobileToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                mobileOpenIcon?.classList.toggle('hidden', open);
                mobileCloseIcon?.classList.toggle('hidden', !open);
                document.body.classList.toggle('overflow-hidden', open);
            }

            mobileToggle?.addEventListener('click', () => setMobileMenu(mobileMenu.classList.contains('translate-x-full')));
            mobileClose?.addEventListener('click', () => setMobileMenu(false));
            mobileBackdrop?.addEventListener('click', () => setMobileMenu(false));

            if(searchInput) {
                searchInput.addEventListener('keyup', function(e) {
                    if (e.key === 'Enter' && this.value.trim().length > 0) {
                        window.location.href = `search?q=${encodeURIComponent(this.value.trim())}`;
                        return;
                    }
                    clearTimeout(timeout);
                    const query = this.value.trim();

                    if (query.length > 0) {
                        timeout = setTimeout(() => {
                            fetch(`search_ajax?q=${encodeURIComponent(query)}`)
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

                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.classList.add('hidden');
                    }
                });
                
                searchInput.addEventListener('focus', function() {
                    if (this.value.trim().length > 0 && searchResults.innerHTML.trim() !== '') {
                        searchResults.classList.remove('hidden');
                    }
                });
            }
        });
    </script>
    
    <main class="flex-grow">
