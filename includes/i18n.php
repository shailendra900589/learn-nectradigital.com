<?php

function supported_languages(): array
{
    return [
        'en' => ['native' => 'English', 'html' => 'en', 'locale' => 'en_US'],
        'hi' => ['native' => 'हिन्दी', 'html' => 'hi', 'locale' => 'hi_IN'],
        'bn' => ['native' => 'বাংলা', 'html' => 'bn', 'locale' => 'bn_IN'],
        'ta' => ['native' => 'தமிழ்', 'html' => 'ta', 'locale' => 'ta_IN'],
        'te' => ['native' => 'తెలుగు', 'html' => 'te', 'locale' => 'te_IN'],
        'mr' => ['native' => 'मराठी', 'html' => 'mr', 'locale' => 'mr_IN'],
        'gu' => ['native' => 'ગુજરાતી', 'html' => 'gu', 'locale' => 'gu_IN'],
        'pa' => ['native' => 'ਪੰਜਾਬੀ', 'html' => 'pa', 'locale' => 'pa_IN'],
    ];
}

function prefixed_language_codes(): array
{
    return array_values(array_filter(array_keys(supported_languages()), static function ($code) {
        return $code !== 'en';
    }));
}

function current_lang(): string
{
    static $lang = null;
    if ($lang !== null) {
        return $lang;
    }
    $raw = strtolower(trim((string)($_GET['lang'] ?? '')));
    $lang = isset(supported_languages()[$raw]) ? $raw : 'en';
    return $lang;
}

function public_lang_prefix(): string
{
    $lang = current_lang();
    return $lang === 'en' ? '' : $lang . '/';
}

function lang_href(string $path): string
{
    $path = ltrim($path, '/');
    if ($path === 'index' || str_starts_with($path, 'index#') || str_starts_with($path, 'index?')) {
        $suffix = substr($path, strlen('index'));
        $prefix = public_lang_prefix();
        return ($prefix === '' ? './' : $prefix) . $suffix;
    }
    return public_lang_prefix() . $path;
}

function ui_catalog(): array
{
    static $catalog = null;
    if ($catalog !== null) {
        return $catalog;
    }
    $catalog = [];
    foreach (supported_languages() as $code => $meta) {
        $file = __DIR__ . '/lang/' . $code . '.php';
        $strings = is_file($file) ? require $file : [];
        $catalog[$code] = is_array($strings) ? $strings : [];
    }
    return $catalog;
}

function t(string $key): string
{
    $catalog = ui_catalog();
    $lang = current_lang();
    if (isset($catalog[$lang][$key]) && $catalog[$lang][$key] !== '') {
        return (string)$catalog[$lang][$key];
    }
    return (string)($catalog['en'][$key] ?? $key);
}

function split_public_url(string $url): ?array
{
    $base = rtrim(site_base_url(), '/');
    if ($base === '' || !str_starts_with($url, $base)) {
        return null;
    }
    $tail = substr($url, strlen($base));
    $hash = '';
    $query = '';
    if (($hashPos = strpos($tail, '#')) !== false) {
        $hash = substr($tail, $hashPos);
        $tail = substr($tail, 0, $hashPos);
    }
    if (($queryPos = strpos($tail, '?')) !== false) {
        $query = substr($tail, $queryPos);
        $tail = substr($tail, 0, $queryPos);
    }
    $tail = '/' . trim($tail, '/');
    if ($tail === '/') {
        $tail = '';
    }
    $codes = implode('|', array_map(static function ($code) {
        return preg_quote($code, '#');
    }, prefixed_language_codes()));
    if ($codes !== '') {
        $tail = preg_replace('#^/(' . $codes . ')$#', '', $tail) ?? $tail;
        $tail = preg_replace('#^/(' . $codes . ')/#', '/', $tail) ?? $tail;
    }
    if ($tail === '/') {
        $tail = '';
    }
    return ['path' => $tail, 'query' => $query, 'hash' => $hash];
}

function url_for_language(string $path, string $query, string $hash, string $lang): string
{
    $base = rtrim(site_base_url(), '/');
    if (!isset(supported_languages()[$lang])) {
        $lang = 'en';
    }
    if ($lang === 'en') {
        return $base . ($path === '' ? '/' : $path) . $query . $hash;
    }
    return $base . '/' . $lang . ($path === '' ? '/' : $path) . $query . $hash;
}

function localize_absolute(string $url): string
{
    $parts = split_public_url($url);
    if ($parts === null) {
        return $url;
    }
    return url_for_language($parts['path'], $parts['query'], $parts['hash'], current_lang());
}

function localized_url(string $path): string
{
    return localize_absolute(absolute_url($path));
}

function language_alternates(string $canonical): array
{
    $parts = split_public_url($canonical);
    if ($parts === null) {
        return [];
    }
    $alternates = [];
    foreach (supported_languages() as $code => $meta) {
        $alternates[] = [
            'code' => $code,
            'html' => $meta['html'],
            'locale' => $meta['locale'],
            'native' => $meta['native'],
            'url' => url_for_language($parts['path'], $parts['query'], $parts['hash'], $code),
        ];
    }
    return $alternates;
}

function page_language_urls(string $relative): array
{
    $relative = ltrim($relative, '/');
    $path = $relative === '' ? '' : '/' . $relative;
    $urls = [];
    foreach (supported_languages() as $code => $meta) {
        $urls[$code] = url_for_language($path, '', '', $code);
    }
    return $urls;
}

function language_switcher(string $id, string $canonical, string $class = 'lang-switch'): string
{
    $current = current_lang();
    $html = '<label class="lang-label" for="' . h($id) . '">' . h(t('nav.language')) . '</label>';
    $html .= '<select id="' . h($id) . '" class="' . h($class) . '" onchange="if (this.value) window.location.href = this.value">';
    foreach (language_alternates($canonical) as $alt) {
        $html .= '<option value="' . h($alt['url']) . '"' . ($alt['code'] === $current ? ' selected' : '') . '>' . h($alt['native']) . '</option>';
    }
    $html .= '</select>';
    return $html;
}
