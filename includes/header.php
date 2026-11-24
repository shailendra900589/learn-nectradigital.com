<?php
require_once __DIR__ . '/security.php';
secure_session_start();
if (function_exists('start_clean_html_buffer')) {
    start_clean_html_buffer();
}

if (!function_exists('current_lang')) {
    require_once __DIR__ . '/i18n.php';
}

// Auto-SEO Defaults
$activeLanguage = current_lang();
$languageMeta = supported_languages()[$activeLanguage];
$langSuffix = $activeLanguage === 'en' ? '' : ' · ' . $languageMeta['native'];
$titleMax = ($activeLanguage === 'en' ? 68 : 120) - seo_length($langSuffix);
$descriptionMax = ($activeLanguage === 'en' ? 155 : 220) - seo_length($langSuffix);
$seo_title = seo_title_text($seo_title ?? 'Free Programming Tutorials', 'Nectra Digital', max(32, $titleMax)) . $langSuffix;
$seo_description = seo_description_text($seo_description ?? 'Master programming with step-by-step tutorials, structured lessons, course search, and progress tracking.', 'Nectra Digital', max(80, $descriptionMax)) . $langSuffix;
$seo_keywords = $seo_keywords ?? 'programming tutorials, web development, html, css, javascript, react, python, php, sql, full stack developer, coding courses';
$seo_canonical = localize_absolute($seo_canonical ?? current_url());
$seo_image = $seo_image ?? '';
$seo_type = $seo_type ?? 'website';
$seo_robots = $seo_robots ?? 'index, follow';
$seo_schema = $seo_schema ?? [];
$languageAlternates = language_alternates($seo_canonical);
if (!headers_sent()) {
    header('Content-Language: ' . $languageMeta['html']);
}
$adsense_client_id = getenv('ADSENSE_CLIENT_ID') ?: '';
$load_ads = isset($pdo) ? should_show_ads($pdo) : true;
$default_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => 'Learn.Nectra',
    'url' => localized_url(''),
    'inLanguage' => $languageMeta['html'],
    'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => localized_url('search') . '?q={search_term_string}',
        'query-input' => 'required name=search_term_string',
    ],
];
$seo_schema = array_merge([$default_schema], $seo_schema);
?>
<!DOCTYPE html>
<html lang="<?php echo h($languageMeta['html']); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo h(site_base_url()); ?>">
    <title><?php echo h($seo_title); ?></title>
    <meta name="description" content="<?php echo h($seo_description); ?>">
    <meta name="keywords" content="<?php echo h($seo_keywords); ?>">
    <meta name="robots" content="<?php echo h($seo_robots); ?>">
    <link rel="canonical" href="<?php echo h($seo_canonical); ?>">
    <?php foreach ($languageAlternates as $alternate): ?>
    <link rel="alternate" hreflang="<?php echo h($alternate['html']); ?>" href="<?php echo h($alternate['url']); ?>">
    <?php endforeach; ?>
    <?php if ($languageAlternates): ?>
    <link rel="alternate" hreflang="x-default" href="<?php echo h($languageAlternates[0]['url']); ?>">
    <?php endif; ?>
    <link rel="alternate" type="application/rss+xml" title="Learn.Nectra RSS Feed" href="<?php echo h(rtrim(site_base_url(), '/')); ?>/feed.xml">
    <link rel="search" type="application/opensearchdescription+xml" title="Learn.Nectra Search" href="<?php echo h(rtrim(site_base_url(), '/')); ?>/opensearch.xml">
    <meta property="og:title" content="<?php echo h($seo_title); ?>">
    <meta property="og:description" content="<?php echo h($seo_description); ?>">
    <meta property="og:type" content="<?php echo h($seo_type); ?>">
    <meta property="og:url" content="<?php echo h($seo_canonical); ?>">
    <meta property="og:locale" content="<?php echo h($languageMeta['locale']); ?>">
    <?php foreach ($languageAlternates as $alternate): if ($alternate['code'] === $activeLanguage) continue; ?>
    <meta property="og:locale:alternate" content="<?php echo h($alternate['locale']); ?>">
    <?php endforeach; ?>
    <?php if($seo_image): ?><meta property="og:image" content="<?php echo h($seo_image); ?>"><?php endif; ?>
    <meta name="twitter:card" content="<?php echo $seo_image ? 'summary_large_image' : 'summary'; ?>">
    <meta name="twitter:title" content="<?php echo h($seo_title); ?>">
    <meta name="twitter:description" content="<?php echo h($seo_description); ?>">
    <?php if($seo_image): ?><meta name="twitter:image" content="<?php echo h($seo_image); ?>"><?php endif; ?>
    <?php if($adsense_client_id && $load_ads): ?>
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?php echo h($adsense_client_id); ?>" crossorigin="anonymous"></script>
    <?php endif; ?>
    <?php foreach($seo_schema as $schema): ?><?php echo schema_script($schema); ?><?php endforeach; ?>
    <script>window.NECTRA_LANG=<?php echo json_encode($activeLanguage, JSON_UNESCAPED_UNICODE); ?>;window.NECTRA_LANG_PREFIX=<?php echo json_encode(public_lang_prefix(), JSON_UNESCAPED_UNICODE); ?>;</script>
    <script src="<?php echo h(protected_asset_url('assets/js/head-init.js')); ?>"></script>
    <script src="https://cdn.tailwindcss.com" onload="window.__applyTailwindConfig&&window.__applyTailwindConfig()"></script>
    <link rel="stylesheet" href="<?php echo h(protected_asset_url('assets/css/tailwind.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo h(protected_asset_url('assets/css/style.css')); ?>">
    <?php if (($pageBodyClass ?? '') === 'dm-studio'): ?>
    <link rel="stylesheet" href="<?php echo h(protected_asset_url('assets/css/marketing-ui.css')); ?>">
    <?php elseif (($pageBodyClass ?? '') === 'seo-studio'): ?>
    <link rel="stylesheet" href="<?php echo h(protected_asset_url('assets/css/seo-ui.css')); ?>">
    <?php elseif (($pageBodyClass ?? '') === 'xl-studio'): ?>
    <link rel="stylesheet" href="<?php echo h(protected_asset_url('assets/css/excel-ui.css')); ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="<?php echo h(protected_asset_url('assets/css/nectra-ui.css')); ?>">
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased flex flex-col min-h-screen<?php echo ($pageBodyClass ?? '') === 'dm-studio' ? ' dm-studio' : ((($pageBodyClass ?? '') === 'seo-studio') ? ' seo-studio' : ((($pageBodyClass ?? '') === 'xl-studio') ? ' xl-studio' : '')); ?>">
    <div id="progressBar"></div>

    <a class="skip-link" href="<?php echo h(explode('#', $_SERVER['REQUEST_URI'] ?? 'index', 2)[0]); ?>#main-content"><?php echo h(t('nav.skip')); ?></a>

    <header id="siteHeader" class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200 transition-all duration-300 reveal">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center header-inner transition-all duration-300">
                <div class="flex-shrink-0 flex items-center reveal">
                    <a href="<?php echo h(lang_href('index')); ?>" class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span class="header-logo-icon text-white shadow-sm" style="width:36px;height:36px;min-width:36px;min-height:36px;display:inline-flex;align-items:center;justify-content:center;">
                            <svg class="w-5 h-5 text-white" width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/></svg>
                        </span>
                        <span>Learn<span class="brand-accent">.Nectra</span></span>
                    </a>
                </div>

                <div class="header-search hidden lg:flex flex-1 min-w-0 items-center justify-center relative">
                    <div class="search-field w-full max-w-lg relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" id="searchInput" aria-label="<?php echo h(t('search.aria')); ?>" autocomplete="off" class="block w-full pl-10 pr-16 py-2 border border-slate-200 rounded-lg leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition-all" placeholder="<?php echo h(t('search.placeholder')); ?>">
                        <div class="kbd-slot absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none">
                            <span class="kbd-badge" id="searchKbd">Ctrl K</span>
                        </div>
                        <div id="searchResults" class="hidden absolute z-50 mt-2 w-full bg-white shadow-2xl rounded-xl border border-gray-100 overflow-hidden backdrop-blur-lg"></div>
                    </div>
                </div>

                <nav class="header-nav hidden lg:flex items-center text-sm font-bold">
                    <?php echo language_switcher('lang-desktop', $seo_canonical); ?>
                    <a href="<?php echo h(lang_href('index')); ?>" class="text-slate-600 hover:text-emerald-600 transition"><?php echo h(t('nav.home')); ?></a>
                    <a href="<?php echo h(lang_href('index#courses')); ?>" class="text-slate-600 hover:text-emerald-600 transition"><?php echo h(t('nav.library')); ?></a>
                    <a href="<?php echo h(lang_href('contact')); ?>" class="text-slate-600 hover:text-emerald-600 transition"><?php echo h(t('nav.support')); ?></a>

                    <?php if(isset($_SESSION['student_logged_in'])): ?>
                        <div class="relative group">
                            <button class="flex items-center gap-2 text-slate-800 font-bold bg-white border border-gray-200 hover:bg-gray-50 px-4 py-2 rounded-full transition shadow-sm cursor-pointer">
                                <span class="w-7 h-7 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full flex items-center justify-center text-xs shadow-inner">
                                    <?php echo h(strtoupper(substr($_SESSION['student_name'], 0, 1))); ?>
                                </span>
                                <?php echo h(explode(' ', trim($_SESSION['student_name']))[0]); ?>
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div class="user-dropdown absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden">
                                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                                    <p class="text-sm font-bold text-gray-900 truncate"><?php echo h($_SESSION['student_name']); ?></p>
                                    <p class="text-xs text-blue-600 font-medium truncate mt-0.5"><?php echo h(t('account.student')); ?></p>
                                </div>
                                <a href="<?php echo h(lang_href('student')); ?>" class="block px-5 py-3 text-sm text-slate-700 hover:bg-blue-50 font-bold transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"></path></svg>
                                    <?php echo h(t('account.panel')); ?>
                                </a>
                                <a href="<?php echo h(lang_href('profile')); ?>" class="block px-5 py-3 text-sm text-slate-700 hover:bg-blue-50 font-bold transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 7a3 3 0 11-6 0 3 3 0 016 0zM5 21a7 7 0 0114 0M17 11h4m-2-2v4"></path></svg>
                                    <?php echo h(t('account.profile')); ?>
                                </a>
                                <a href="<?php echo h(lang_href('billing')); ?>" class="block px-5 py-3 text-sm text-slate-700 hover:bg-blue-50 font-bold transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h5M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"></path></svg>
                                    <?php echo h(t('account.plans')); ?>
                                </a>
                                <form method="post" action="<?php echo h(app_path('logout')); ?>" class="m-0">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="block w-full text-left px-5 py-3 text-sm text-red-600 hover:bg-red-50 font-bold transition flex items-center gap-2 bg-transparent border-0 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        <?php echo h(t('account.signout')); ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo h(lang_href('login')); ?>" class="edu-primary-btn py-2.5 px-7 transition transform hover:-translate-y-0.5"><?php echo h(t('nav.login')); ?></a>
                    <?php endif; ?>
                </nav>

                <button type="button" id="mobileMenuToggle" class="header-menu-btn lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-200 bg-white text-slate-700 shadow-sm" aria-label="<?php echo h(t('nav.menu_open')); ?>" aria-controls="mobileMenu" aria-expanded="false">
                    <svg id="mobileMenuOpenIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"></path></svg>
                    <svg id="mobileMenuCloseIcon" class="hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
    </header>

    <div id="mobileMenuBackdrop" class="fixed inset-0 hidden" aria-hidden="true"></div>

    <aside id="mobileMenu" class="fixed top-0 right-0 h-full w-full sm:w-[360px] sm:max-w-sm bg-white translate-x-full transition-transform duration-300 ease-in-out lg:hidden flex flex-col shadow-2xl" aria-label="<?php echo h(t('nav.mobile')); ?>" inert>
        <div class="h-16 px-4 sm:px-5 flex items-center justify-between border-b border-slate-100 bg-slate-50/60 flex-shrink-0">
            <a href="<?php echo h(lang_href('index')); ?>" class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <span class="header-logo-icon text-white shadow-sm" style="width:32px;height:32px;min-width:32px;min-height:32px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;">
                    <svg class="w-4 h-4 text-white" width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/></svg>
                </span>
                <span>Learn<span class="brand-accent">.Nectra</span></span>
            </a>
            <button type="button" id="mobileMenuCloseButton" class="w-10 h-10 rounded-lg border border-slate-200 hover:bg-slate-100 flex items-center justify-center text-slate-600 hover:text-slate-900 transition flex-shrink-0 cursor-pointer ml-3" aria-label="<?php echo h(t('nav.menu_close')); ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-4 border-b border-slate-100 bg-white flex-shrink-0">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" id="mobileSearchInput" aria-label="<?php echo h(t('search.aria')); ?>" autocomplete="off" class="block w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition" placeholder="<?php echo h(t('search.mobile_placeholder')); ?>">
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-1">
            <?php echo language_switcher('lang-mobile', $seo_canonical, 'lang-switch lang-switch-wide'); ?>
            <div class="text-[10px] font-black uppercase tracking-wider text-slate-400 px-3 py-1"><?php echo h(t('nav.navigation')); ?></div>
            <a href="<?php echo h(lang_href('index')); ?>" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"></path></svg>
                <?php echo h(t('nav.home')); ?>
            </a>
            <a href="<?php echo h(lang_href('index#courses')); ?>" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <?php echo h(t('nav.all_courses')); ?>
            </a>
            <a href="<?php echo h(lang_href('search')); ?>" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <?php echo h(t('nav.search')); ?>
            </a>
            <a href="<?php echo h(lang_href('contact')); ?>" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                <?php echo h(t('nav.contact')); ?>
            </a>

            <div class="pt-3">
                <?php if(isset($_SESSION['student_logged_in'])): ?>
                    <div class="text-[10px] font-black uppercase tracking-wider text-slate-400 px-3 py-1"><?php echo h(t('account.student')); ?></div>
                    <a href="<?php echo h(lang_href('student')); ?>" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-bold text-emerald-800 bg-emerald-50 border border-emerald-200">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"></path></svg>
                        <?php echo h(t('account.dashboard')); ?>
                    </a>
                    <a href="<?php echo h(lang_href('profile')); ?>" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-bold text-slate-700 hover:bg-slate-50 transition">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 7a3 3 0 11-6 0 3 3 0 016 0zM5 21a7 7 0 0114 0"></path></svg>
                        <?php echo h(t('account.certificate')); ?>
                    </a>
                    <a href="<?php echo h(lang_href('billing')); ?>" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-bold text-slate-700 hover:bg-slate-50 transition">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h5M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"></path></svg>
                        <?php echo h(t('account.plans')); ?>
                    </a>
                    <form method="post" action="<?php echo h(app_path('logout')); ?>" class="m-0">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 font-bold text-red-600 hover:bg-red-50 transition bg-transparent border-0 cursor-pointer text-left">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            <?php echo h(t('account.signout')); ?>
                        </button>
                    </form>
                <?php else: ?>
                    <a href="<?php echo h(lang_href('login')); ?>" class="edu-primary-btn w-full justify-center py-3 text-center flex items-center gap-2 mt-2 shadow-md shadow-emerald-900/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        <span><?php echo h(t('account.login_register')); ?></span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </aside>

    <main id="main-content" class="flex-grow" tabindex="-1">
