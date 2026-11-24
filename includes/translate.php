<?php

if (!function_exists('current_lang')) {
    require_once __DIR__ . '/i18n.php';
}

function translation_memory_reset(): void
{
    $memory = &translation_memory();
    $memory = [];
}

function &translation_memory(): array
{
    static $memory = [];
    return $memory;
}

function lesson_text_needs_translation(string $text): bool
{
    $trim = trim($text);
    if ($trim === '' || !preg_match('/\p{L}/u', $trim)) {
        return false;
    }
    if (preg_match('/^=/u', $trim)) {
        return false;
    }
    if (preg_match('/^[A-Za-z]{1,3}\d{1,5}(?::[A-Za-z]{1,3}\d{1,5})?$/u', $trim)) {
        return false;
    }
    if (preg_match('/^[A-Z]$/u', $trim)) {
        return false;
    }
    if (preg_match('/^(?:https?:\/\/|mailto:)\S+$/iu', $trim)) {
        return false;
    }
    if (preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/iu', $trim)) {
        return false;
    }
    return true;
}

function localize_text(string $text, ?string $lang = null): string
{
    $done = localize_texts([$text], $lang);
    return $done[0] ?? $text;
}

function localize_texts(array $texts, ?string $lang = null): array
{
    $lang = $lang ?: current_lang();
    $texts = array_values($texts);
    if ($lang === 'en' || !isset(supported_languages()[$lang])) {
        return $texts;
    }

    $wanted = [];
    foreach ($texts as $index => $text) {
        if (is_string($text) && lesson_text_needs_translation($text)) {
            $wanted[$index] = $text;
        }
    }
    if (!$wanted) {
        return $texts;
    }

    $resolved = translation_resolve($lang, array_values($wanted));
    foreach ($wanted as $index => $text) {
        if (isset($resolved[$text]) && $resolved[$text] !== '') {
            $texts[$index] = $resolved[$text];
        }
    }
    return $texts;
}

function translation_resolve(string $lang, array $texts): array
{
    $unique = [];
    foreach ($texts as $text) {
        if (is_string($text) && $text !== '') {
            $unique[$text] = true;
        }
    }
    $texts = array_keys($unique);
    $memory = &translation_memory();
    $resolved = [];
    $missing = [];
    foreach ($texts as $text) {
        if (isset($memory[$lang][$text])) {
            $resolved[$text] = $memory[$lang][$text];
        } else {
            $missing[] = $text;
        }
    }
    if (!$missing) {
        return $resolved;
    }

    $ready = translation_ready();
    if ($ready) {
        foreach (translation_cache_get($lang, $missing) as $source => $translated) {
            $memory[$lang][$source] = $translated;
            $resolved[$source] = $translated;
        }
    }

    $uncached = [];
    foreach ($missing as $text) {
        if (!isset($resolved[$text])) {
            $uncached[] = $text;
        }
    }
    if (!$uncached) {
        return $resolved;
    }

    $fresh = translation_fetch_remote($lang, $uncached);
    if ($ready && $fresh) {
        translation_cache_put($lang, $fresh);
    }
    foreach ($fresh as $source => $translated) {
        $memory[$lang][$source] = $translated;
        $resolved[$source] = $translated;
    }
    return $resolved;
}

function translation_ready(): bool
{
    static $ready = null;
    if ($ready !== null) {
        return $ready;
    }
    global $pdo;
    if (!isset($pdo) || !$pdo instanceof PDO) {
        $ready = false;
        return false;
    }
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS content_translations (
            lang VARCHAR(8) NOT NULL,
            src_hash CHAR(40) NOT NULL,
            translated MEDIUMTEXT NOT NULL,
            PRIMARY KEY (lang, src_hash)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $ready = true;
    } catch (Throwable $e) {
        error_log('content translation cache unavailable');
        $ready = false;
    }
    return $ready;
}

function translation_cache_get(string $lang, array $texts): array
{
    global $pdo;
    $pairs = [];
    foreach ($texts as $text) {
        $pairs[sha1($text)] = $text;
    }
    $found = [];
    foreach (array_chunk(array_keys($pairs), 80) as $hashes) {
        $marks = implode(',', array_fill(0, count($hashes), '?'));
        $stmt = $pdo->prepare("SELECT src_hash, translated FROM content_translations WHERE lang = ? AND src_hash IN ($marks)");
        $stmt->execute(array_merge([$lang], $hashes));
        foreach ($stmt->fetchAll() as $row) {
            $source = $pairs[$row['src_hash']] ?? null;
            if ($source !== null) {
                $found[$source] = (string)$row['translated'];
            }
        }
    }
    return $found;
}

function translation_cache_put(string $lang, array $map): void
{
    if (!$map) {
        return;
    }
    global $pdo;
    $stmt = $pdo->prepare('INSERT IGNORE INTO content_translations (lang, src_hash, translated) VALUES (?, ?, ?)');
    $pdo->beginTransaction();
    try {
        foreach ($map as $source => $translated) {
            if (!is_string($source) || !is_string($translated) || $translated === '') {
                continue;
            }
            $stmt->execute([$lang, sha1($source), $translated]);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
    }
}

function translation_fetch_remote(string $lang, array $texts): array
{
    if (PHP_SAPI !== 'cli' && function_exists('set_time_limit')) {
        @set_time_limit(180);
    }
    $resolved = [];
    $bucket = [];
    $size = 0;
    $flush = function () use (&$bucket, &$size, &$resolved, $lang): void {
        if (!$bucket) {
            return;
        }
        foreach (translation_fetch_chunk($lang, $bucket) as $source => $translated) {
            $resolved[$source] = $translated;
        }
        $bucket = [];
        $size = 0;
        $pace = (int)($GLOBALS['nectra_translation_pace_us'] ?? 0);
        if ($pace > 0) {
            usleep($pace);
        }
    };
    foreach ($texts as $text) {
        $length = strlen($text);
        if ($bucket && ($size + $length > 3400 || count($bucket) >= 20)) {
            $flush();
        }
        $bucket[] = $text;
        $size += $length + 16;
    }
    $flush();
    return $resolved;
}

function translation_fetch_chunk(string $lang, array $texts): array
{
    if (count($texts) === 1) {
        $single = translation_google($lang, $texts[0], 2);
        return ($single === null || $single === '') ? [] : [$texts[0] => $single];
    }

    $lines = [];
    foreach ($texts as $index => $text) {
        if ($index > 0) {
            $lines[] = '⟦' . $index . '⟧';
        }
        $lines[] = $text;
    }
    $joined = translation_google($lang, implode("\n", $lines), 2);
    $parts = $joined === null ? null : translation_split_marked($joined, count($texts));
    if (is_array($parts)) {
        $resolved = [];
        foreach ($texts as $index => $text) {
            if (isset($parts[$index]) && $parts[$index] !== '') {
                $resolved[$text] = $parts[$index];
            }
        }
        if (count($resolved) === count($texts)) {
            return $resolved;
        }
    }

    $resolved = [];
    foreach ($texts as $text) {
        $single = translation_google($lang, $text, 1);
        if ($single !== null && $single !== '') {
            $resolved[$text] = $single;
        }
    }
    return $resolved;
}

function translation_split_marked(string $translated, int $count): ?array
{
    $translated = preg_replace_callback('/⟦\s*([CTct])\s*(\d+)\s*⟧/u', static function (array $match): string {
        return '⟦' . strtoupper($match[1]) . $match[2] . '⟧';
    }, $translated) ?? $translated;
    $bits = preg_split('/\n?⟦\s*(\d+)\s*⟧\n?/u', $translated);
    if (!is_array($bits) || count($bits) !== $count) {
        return null;
    }
    $parts = [];
    foreach ($bits as $index => $bit) {
        $parts[$index] = trim((string)$bit);
    }
    return $parts;
}

function translation_google(string $lang, string $text, int $attempts): ?string
{
    if (!function_exists('curl_init') || $text === '') {
        return null;
    }
    $url = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=' . rawurlencode($lang) . '&dt=t';
    for ($try = 1; $try <= $attempts; $try++) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => 'q=' . rawurlencode($text),
            CURLOPT_TIMEOUT => 25,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_USERAGENT => 'Mozilla/5.0',
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ]);
        $body = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code === 200 && is_string($body) && $body !== '' && $body[0] === '[') {
            $decoded = translation_decode_google($body);
            if ($decoded !== null && $decoded !== '') {
                return $decoded;
            }
        }
        if ($try < $attempts) {
            usleep(350000 * $try);
        }
    }
    return translation_mymemory($lang, $text);
}

function translation_decode_google(string $body): ?string
{
    $json = json_decode($body, true);
    if (!is_array($json) || !isset($json[0]) || !is_array($json[0])) {
        return null;
    }
    $text = '';
    foreach ($json[0] as $part) {
        if (is_array($part) && isset($part[0]) && is_string($part[0])) {
            $text .= $part[0];
        }
    }
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return $text === '' ? null : $text;
}

function translation_mymemory(string $lang, string $text): ?string
{
    if (!function_exists('curl_init') || strlen($text) > 450 || str_contains($text, '⟦')) {
        return null;
    }
    $url = 'https://api.mymemory.translated.net/get?q=' . rawurlencode($text) . '&langpair=' . rawurlencode('en|' . $lang);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_USERAGENT => 'Mozilla/5.0',
    ]);
    $body = curl_exec($ch);
    curl_close($ch);
    $json = is_string($body) ? json_decode($body, true) : null;
    $translated = is_array($json) ? (string)($json['responseData']['translatedText'] ?? '') : '';
    if ($translated === '' || str_contains($translated, 'MYMEMORY WARNING') || (int)($json['responseStatus'] ?? 0) !== 200) {
        return null;
    }
    return html_entity_decode($translated, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function localize_lesson_html(string $html, ?string $lang = null): string
{
    $lang = $lang ?: current_lang();
    if ($lang === 'en' || $html === '' || !isset(supported_languages()[$lang])) {
        return $html;
    }

    $parts = preg_split('/(<!--.*?-->|<[^>]+>)/s', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
    if (!is_array($parts)) {
        return $html;
    }

    $inline = array_flip(['a', 'abbr', 'b', 'bdi', 'bdo', 'br', 'cite', 'code', 'data', 'dfn', 'em', 'i', 'kbd', 'mark', 'q', 's', 'samp', 'small', 'span', 'strong', 'sub', 'sup', 'time', 'u', 'var', 'wbr', 'font', 'tt']);
    $skipTags = array_flip(['pre', 'script', 'style', 'textarea', 'noscript']);
    $void = array_flip(['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr']);
    $pieces = [];
    $buffer = '';
    $skipStack = [];
    $attrMap = [];
    $flush = static function () use (&$pieces, &$buffer): void {
        if ($buffer === '') {
            return;
        }
        $pieces[] = ['text' => $buffer];
        $buffer = '';
    };

    for ($i = 0, $count = count($parts); $i < $count; $i++) {
        $part = $parts[$i];
        if ($part === '') {
            continue;
        }
        $isTag = $part[0] === '<' && (preg_match('/^<\/?[a-zA-Z]/', $part) || str_starts_with($part, '<!--') || str_starts_with($part, '<!'));
        if (!$isTag) {
            if ($skipStack) {
                $pieces[] = ['raw' => $part];
            } else {
                $buffer .= $part;
            }
            continue;
        }
        if (str_starts_with($part, '<!--') || str_starts_with($part, '<!')) {
            $flush();
            $pieces[] = ['raw' => $part];
            continue;
        }

        $name = lesson_tag_name($part);
        $closing = lesson_is_closing($part);
        $self = lesson_is_self_closing($part) || isset($void[$name]);
        if ($skipStack) {
            $pieces[] = ['raw' => $part];
            if ($closing && $name === $skipStack[count($skipStack) - 1]) {
                array_pop($skipStack);
            }
            continue;
        }
        if (!$closing && !$self && isset($skipTags[$name])) {
            $flush();
            $pieces[] = ['raw' => $part];
            $skipStack[] = $name;
            continue;
        }
        if (!$closing && !$self && $name === 'div' && preg_match('/\b(?:tutorial-output|prompt-keep|prompt-reply|nd-visual)\b/', $part)) {
            [$block, $end] = lesson_capture_div($parts, $i);
            $keepOriginal = str_contains($part, 'tutorial-output') || str_contains($part, 'prompt-keep') || str_contains($part, 'prompt-reply') || str_contains($block, 'EXCEL STUDIO');
            if ($keepOriginal) {
                $flush();
                $pieces[] = ['raw' => $block];
                $i = $end;
                continue;
            }
        }
        if (isset($inline[$name])) {
            $buffer .= $part;
            continue;
        }
        $flush();
        $pieces[] = ['raw' => $closing ? $part : lesson_mark_attributes($part, $attrMap)];
    }
    $flush();

    $jobs = [];
    $coreAt = [];
    foreach ($pieces as $index => $piece) {
        if (!isset($piece['text'])) {
            continue;
        }
        $prepared = lesson_prepare_segment($piece['text'], $attrMap);
        if ($prepared === null) {
            continue;
        }
        $pieces[$index]['prep'] = $prepared;
        $coreAt[$index] = count($jobs);
        $jobs[] = $prepared['core'];
    }
    $attrAt = [];
    foreach ($attrMap as $id => $source) {
        $attrAt[$id] = count($jobs);
        $jobs[] = $source;
    }
    $done = $jobs ? localize_texts($jobs, $lang) : [];
    $attrDone = [];
    foreach ($attrAt as $id => $jobIndex) {
        $attrDone[$id] = $done[$jobIndex] ?? $attrMap[$id];
    }

    $htmlOut = '';
    foreach ($pieces as $index => $piece) {
        if (isset($piece['raw'])) {
            $htmlOut .= lesson_fill_attributes($piece['raw'], $attrDone);
            continue;
        }
        if (!isset($piece['prep'], $coreAt[$index])) {
            $htmlOut .= $piece['text'];
            continue;
        }
        $translated = $done[$coreAt[$index]] ?? '';
        $htmlOut .= $translated === ''
            ? $piece['text']
            : lesson_apply_translation($piece['text'], $piece['prep'], $translated, $attrDone);
    }
    return $htmlOut;
}

function lesson_tag_name(string $tag): string
{
    if (!preg_match('/^<\s*\/?\s*([a-zA-Z0-9:-]+)/', $tag, $match)) {
        return '';
    }
    return strtolower($match[1]);
}

function lesson_is_closing(string $tag): bool
{
    return (bool)preg_match('/^<\s*\//', $tag);
}

function lesson_is_self_closing(string $tag): bool
{
    return (bool)preg_match('/\/\s*>$/', $tag);
}

function lesson_capture_div(array $parts, int $start): array
{
    $raw = '';
    $depth = 0;
    $last = $start;
    $count = count($parts);
    for ($i = $start; $i < $count; $i++) {
        $part = $parts[$i];
        $raw .= $part;
        $last = $i;
        if ($part === '' || $part[0] !== '<' || !preg_match('/^<\/?[a-zA-Z]/', $part)) {
            continue;
        }
        if (lesson_tag_name($part) !== 'div') {
            continue;
        }
        if (!lesson_is_closing($part) && !lesson_is_self_closing($part)) {
            $depth++;
        } elseif (lesson_is_closing($part)) {
            $depth--;
            if ($depth === 0) {
                break;
            }
        }
    }
    return [$raw, $last];
}

function lesson_mark_attributes(string $tag, array &$attrMap): string
{
    $marked = preg_replace_callback('/\s(alt|aria-label|title|placeholder)\s*=\s*("|\')(.*?)\2/iu', static function (array $match) use (&$attrMap): string {
        $decoded = html_entity_decode($match[3], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (!lesson_text_needs_translation($decoded)) {
            return $match[0];
        }
        $id = 'A' . count($attrMap);
        $attrMap[$id] = $decoded;
        return ' ' . $match[1] . '=' . $match[2] . '⟦' . $id . '⟧' . $match[2];
    }, $tag);
    return is_string($marked) ? $marked : $tag;
}

function lesson_prepare_segment(string $segment, array &$attrMap): ?array
{
    if (str_contains($segment, '⟦')) {
        return null;
    }
    $vault = [];
    $protected = preg_replace_callback('/<(code|kbd|samp|var)\b[^>]*>.*?<\/\1>/is', static function (array $match) use (&$vault): string {
        $id = 'C' . count($vault);
        $vault[$id] = $match[0];
        return '⟦' . $id . '⟧';
    }, $segment);
    if (!is_string($protected)) {
        return null;
    }
    $protected = preg_replace_callback('/<[^>]+>/', static function (array $match) use (&$vault, &$attrMap): string {
        $id = 'T' . count($vault);
        $vault[$id] = lesson_mark_attributes($match[0], $attrMap);
        return '⟦' . $id . '⟧';
    }, $protected);
    if (!is_string($protected)) {
        return null;
    }
    $protected = html_entity_decode($protected, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $plain = trim(preg_replace('/⟦[CT]\d+⟧/u', '', $protected) ?? $protected);
    if (!lesson_text_needs_translation($plain)) {
        return null;
    }
    $core = trim($protected);
    if ($core === '') {
        return null;
    }
    preg_match('/^\s*/u', $protected, $lead);
    preg_match('/\s*$/u', $protected, $tail);
    return [
        'core' => $core,
        'lead' => $lead[0] ?? '',
        'tail' => $tail[0] ?? '',
        'vault' => $vault,
    ];
}

function lesson_apply_translation(string $original, array $prepared, string $translated, array $attrDone): string
{
    $translated = preg_replace_callback('/⟦\s*([CTct])\s*(\d+)\s*⟧/u', static function (array $match): string {
        return '⟦' . strtoupper($match[1]) . $match[2] . '⟧';
    }, $translated) ?? $translated;
    preg_match_all('/⟦([CT]\d+)⟧/u', $prepared['core'], $needed);
    preg_match_all('/⟦([CT]\d+)⟧/u', $translated, $found);
    if (array_diff($needed[1] ?? [], $found[1] ?? [])) {
        return $original;
    }
    $escaped = htmlspecialchars($translated, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $restored = preg_replace_callback('/⟦([CT]\d+)⟧/u', static function (array $match) use ($prepared): string {
        return $prepared['vault'][$match[1]] ?? '';
    }, $escaped);
    if (!is_string($restored)) {
        return $original;
    }
    return $prepared['lead'] . lesson_fill_attributes($restored, $attrDone) . $prepared['tail'];
}

function lesson_fill_attributes(string $html, array $attrDone): string
{
    if (!str_contains($html, '⟦A')) {
        return $html;
    }
    $filled = preg_replace_callback('/⟦(A\d+)⟧/u', static function (array $match) use ($attrDone): string {
        return htmlspecialchars((string)($attrDone[$match[1]] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }, $html);
    return is_string($filled) ? $filled : $html;
}
