<?php
declare(strict_types=1);

/**
 * Reusable Reference-Grade Curriculum Builder & Visual/Graph Renderer
 * Ensures every chapter across all Learn.Nectra courses has:
 *  - Clear first-principles lead & architectural deep dive
 *  - Granular micro-detail breakdown table (.beginner-breakdown)
 *  - Inline SVG Architecture Diagrams / Data Graphs / Visual Hints
 *  - Multiple runnable .w3-example-box code blocks
 *  - Senior-level edge cases & micro-points callout box
 *  - 4-question interactive self-assessment quiz_json + practice_content
 */

function sh_esc(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function sh_inline(string $text): string
{
    $escaped = sh_esc($text);
    $escaped = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $escaped);
    $escaped = preg_replace('/`([^`]+)`/', '<code>$1</code>', $escaped);
    return $escaped;
}

/**
 * Renders an inline SVG Pipeline / Architecture Flow Diagram
 */
function svg_pipeline_diagram(string $title, array $steps, string $accent = '#059669'): string
{
    $count = count($steps);
    if ($count === 0) return '';
    $boxW = 148;
    $gap = 26;
    $padX = 20;
    $totalW = max(680, ($count * $boxW) + (($count - 1) * $gap) + ($padX * 2));
    $svg = '<figure class="my-7 bg-slate-900 rounded-2xl p-5 border border-slate-800 shadow-sm overflow-x-auto">';
    $svg .= '<div class="text-xs font-black uppercase tracking-wider text-emerald-400 mb-3 flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-400"></span>' . sh_esc($title) . '</div>';
    $svg .= '<svg viewBox="0 0 ' . $totalW . ' 108" class="w-full min-w-[620px] h-auto" role="img" aria-label="' . sh_esc($title) . '">';
    $svg .= '<defs><marker id="arr" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse"><path d="M 0 1 L 10 5 L 0 9 z" fill="' . sh_esc($accent) . '"/></marker></defs>';

    foreach ($steps as $i => $step) {
        $x = $padX + $i * ($boxW + $gap);
        $label = is_array($step) ? ($step[0] ?? '') : (string)$step;
        $sub = is_array($step) ? ($step[1] ?? '') : '';
        $svg .= '<rect x="' . $x . '" y="16" width="' . $boxW . '" height="74" rx="12" fill="#1e293b" stroke="' . sh_esc($accent) . '" stroke-width="1.6"/>';
        $svg .= '<text x="' . ($x + $boxW / 2) . '" y="46" text-anchor="middle" fill="#f8fafc" font-size="12" font-weight="700" font-family="system-ui,sans-serif">' . sh_esc($label) . '</text>';
        if ($sub !== '') {
            $svg .= '<text x="' . ($x + $boxW / 2) . '" y="68" text-anchor="middle" fill="#94a3b8" font-size="10.5" font-family="monospace">' . sh_esc($sub) . '</text>';
        }
        if ($i < $count - 1) {
            $x1 = $x + $boxW + 3;
            $x2 = $x + $boxW + $gap - 5;
            $svg .= '<line x1="' . $x1 . '" y1="53" x2="' . $x2 . '" y2="53" stroke="' . sh_esc($accent) . '" stroke-width="2" marker-end="url(#arr)"/>';
        }
    }
    $svg .= '</svg></figure>';
    return $svg;
}

/**
 * Renders an inline SVG Bar / Metric Comparison Chart (ideal for SEO, Marketing, Benchmarks, DB & AI)
 */
function svg_bar_chart(string $title, array $bars, string $unit = '', string $caption = ''): string
{
    if (!$bars) return '';
    $maxVal = 0.0001;
    foreach ($bars as $b) {
        $maxVal = max($maxVal, (float)($b['value'] ?? 0));
    }
    $rowH = 38;
    $topPad = 18;
    $h = $topPad + count($bars) * $rowH + 16;
    $out = '<figure class="my-7 bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">';
    $out .= '<div class="flex items-center justify-between gap-2 mb-3"><span class="text-xs font-black uppercase tracking-wider text-slate-800">' . sh_esc($title) . '</span><span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full">Data Graph</span></div>';
    $out .= '<svg viewBox="0 0 680 ' . $h . '" class="w-full h-auto" role="img" aria-label="' . sh_esc($title) . '">';
    foreach ($bars as $idx => $b) {
        $y = $topPad + $idx * $rowH;
        $label = (string)($b['label'] ?? '');
        $val = (float)($b['value'] ?? 0);
        $color = (string)($b['color'] ?? '#059669');
        $w = max(8, (int)round(($val / $maxVal) * 390));
        $out .= '<text x="10" y="' . ($y + 18) . '" fill="#334155" font-size="12" font-weight="600" font-family="system-ui,sans-serif">' . sh_esc($label) . '</text>';
        $out .= '<rect x="195" y="' . ($y + 4) . '" width="400" height="20" rx="6" fill="#f1f5f9"/>';
        $out .= '<rect x="195" y="' . ($y + 4) . '" width="' . $w . '" height="20" rx="6" fill="' . sh_esc($color) . '"/>';
        $out .= '<text x="' . (205 + $w) . '" y="' . ($y + 18) . '" fill="#0f172a" font-size="11.5" font-weight="700" font-family="monospace">' . sh_esc($b['display'] ?? ($val . $unit)) . '</text>';
    }
    $out .= '</svg>';
    if ($caption !== '') {
        $out .= '<figcaption class="text-xs text-slate-500 mt-2 border-t border-slate-100 pt-2">' . sh_inline($caption) . '</figcaption>';
    }
    $out .= '</figure>';
    return $out;
}

/**
 * Renders an inline SVG Line / Trend Graph (ideal for SEO CTR curves, Traffic Growth, Loss Curves, Complexity)
 */
function svg_line_graph(string $title, array $xLabels, array $values, string $yUnit = '', string $color = '#059669', string $caption = ''): string
{
    $n = count($values);
    if ($n < 2) return '';
    $maxV = max(array_map('floatval', $values));
    $minV = min(0.0, min(array_map('floatval', $values)));
    $range = max(0.0001, $maxV - $minV);

    $left = 58;
    $right = 650;
    $top = 24;
    $bottom = 178;
    $plotW = $right - $left;
    $plotH = $bottom - $top;

    $pts = [];
    $areaPts = [$left . ',' . $bottom];
    foreach ($values as $i => $v) {
        $x = $left + ($i / ($n - 1)) * $plotW;
        $y = $bottom - (((float)$v - $minV) / $range) * $plotH;
        $pts[] = round($x, 1) . ',' . round($y, 1);
        $areaPts[] = round($x, 1) . ',' . round($y, 1);
    }
    $areaPts[] = $right . ',' . $bottom;

    $out = '<figure class="my-7 bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">';
    $out .= '<div class="flex items-center justify-between gap-2 mb-3"><span class="text-xs font-black uppercase tracking-wider text-slate-800">' . sh_esc($title) . '</span><span class="text-[11px] font-semibold text-sky-700 bg-sky-50 border border-sky-200 px-2.5 py-0.5 rounded-full">Trend Curve</span></div>';
    $out .= '<svg viewBox="0 0 680 215" class="w-full h-auto" role="img" aria-label="' . sh_esc($title) . '">';
    for ($g = 0; $g <= 4; $g++) {
        $gy = $top + ($g / 4) * $plotH;
        $gv = round($maxV - ($g / 4) * $range, 1);
        $out .= '<line x1="' . $left . '" y1="' . $gy . '" x2="' . $right . '" y2="' . $gy . '" stroke="#e2e8f0" stroke-dasharray="4 4"/>';
        $out .= '<text x="' . ($left - 8) . '" y="' . ($gy + 4) . '" text-anchor="end" fill="#64748b" font-size="10" font-family="monospace">' . sh_esc($gv . $yUnit) . '</text>';
    }
    $out .= '<polygon points="' . implode(' ', $areaPts) . '" fill="' . sh_esc($color) . '" fill-opacity="0.12"/>';
    $out .= '<polyline points="' . implode(' ', $pts) . '" fill="none" stroke="' . sh_esc($color) . '" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>';
    foreach ($values as $i => $v) {
        $x = $left + ($i / ($n - 1)) * $plotW;
        $y = $bottom - (((float)$v - $minV) / $range) * $plotH;
        $out .= '<circle cx="' . round($x, 1) . '" cy="' . round($y, 1) . '" r="4.5" fill="#ffffff" stroke="' . sh_esc($color) . '" stroke-width="2.5"/>';
        $out .= '<text x="' . round($x, 1) . '" y="' . max(14, round($y - 9, 1)) . '" text-anchor="middle" fill="#0f172a" font-size="10" font-weight="700" font-family="monospace">' . sh_esc($v . $yUnit) . '</text>';
        $lbl = $xLabels[$i] ?? '';
        $out .= '<text x="' . round($x, 1) . '" y="' . ($bottom + 18) . '" text-anchor="middle" fill="#475569" font-size="10.5" font-weight="600" font-family="system-ui,sans-serif">' . sh_esc((string)$lbl) . '</text>';
    }
    $out .= '</svg>';
    if ($caption !== '') {
        $out .= '<figcaption class="text-xs text-slate-500 mt-2 border-t border-slate-100 pt-2">' . sh_inline($caption) . '</figcaption>';
    }
    $out .= '</figure>';
    return $out;
}

/**
 * Renders a Funnel / Visual Hint Card (ideal for Digital Marketing Visual Hints, Ad Mockups, Landing Page Wireframes)
 */
function visual_hint_card(string $badge, string $title, string $svgOrWireframeHtml, string $hintNotes): string
{
    $out = '<figure class="my-7 rounded-2xl border-2 border-indigo-100 bg-gradient-to-br from-slate-50 via-white to-indigo-50/40 p-5 shadow-sm">';
    $out .= '<div class="flex flex-wrap items-center justify-between gap-2 mb-3">';
    $out .= '<span class="inline-flex items-center gap-1.5 text-[11px] font-black uppercase tracking-wider text-indigo-700 bg-indigo-100/80 px-3 py-1 rounded-full">📸 ' . sh_esc($badge) . '</span>';
    $out .= '<span class="text-xs font-bold text-slate-700">' . sh_esc($title) . '</span>';
    $out .= '</div>';
    $out .= '<div class="rounded-xl border border-slate-200 bg-white p-4 overflow-x-auto">' . $svgOrWireframeHtml . '</div>';
    $out .= '<figcaption class="mt-3 text-xs text-slate-600 leading-relaxed bg-amber-50/70 border border-amber-200/80 rounded-lg px-3.5 py-2.5"><strong>Visual Reference Hint:</strong> ' . sh_inline($hintNotes) . '</figcaption>';
    $out .= '</figure>';
    return $out;
}

/**
 * Builds a complete, cohesive, reference-grade HTML chapter from structured specification
 */
function build_rich_chapter_html(array $spec): string
{
    $html = '';

    // 1. Lead Paragraph
    if (!empty($spec['lead'])) {
        $html .= '<p class="lead text-lg font-medium text-slate-700">' . sh_inline($spec['lead']) . '</p>' . "\n\n";
    }

    // 2. Deep Explanation Sections
    if (!empty($spec['sections']) && is_array($spec['sections'])) {
        foreach ($spec['sections'] as $sec) {
            $html .= '<h3 class="text-2xl font-black text-slate-800 mt-8 mb-3">' . sh_esc($sec['heading']) . '</h3>' . "\n";
            foreach ((array)($sec['paragraphs'] ?? []) as $p) {
                $html .= '<p class="text-slate-600 mb-4 leading-relaxed">' . sh_inline($p) . '</p>' . "\n";
            }
            if (!empty($sec['bullets'])) {
                $html .= '<ul class="list-disc pl-6 space-y-2 text-slate-600 mb-5">' . "\n";
                foreach ($sec['bullets'] as $b) {
                    $html .= '  <li>' . sh_inline($b) . '</li>' . "\n";
                }
                $html .= '</ul>' . "\n";
            }
        }
    }

    // 3. Optional Visual / SVG Graph / Image Hint
    if (!empty($spec['visual_html'])) {
        $html .= $spec['visual_html'] . "\n\n";
    }

    // 4. Granular Breakdown Table (.beginner-breakdown)
    if (!empty($spec['table']) && !empty($spec['table']['rows'])) {
        $t = $spec['table'];
        $headers = $t['headers'] ?? ['Concept / Syntax', 'Technical Meaning', 'Practical Behavior & Edge Cases'];
        $html .= '<div class="beginner-breakdown mb-8">' . "\n";
        $html .= '  <div class="beginner-breakdown-title">' . sh_esc($t['title'] ?? 'Complete Technical Reference & Micro-Details') . '</div>' . "\n";
        $html .= '  <table class="beginner-breakdown-table">' . "\n";
        $html .= '    <thead><tr>';
        foreach ($headers as $th) {
            $html .= '<th>' . sh_esc($th) . '</th>';
        }
        $html .= '</tr></thead>' . "\n    <tbody>\n";
        foreach ($t['rows'] as $row) {
            $html .= '      <tr>';
            foreach ($row as $ci => $cell) {
                $cls = ($ci === 0) ? ' class="code-cell"' : '';
                $html .= '<td' . $cls . '>' . sh_inline((string)$cell) . '</td>';
            }
            $html .= "</tr>\n";
        }
        $html .= "    </tbody>\n  </table>\n</div>\n\n";
    }

    // 5. Runnable Examples (.w3-example-box)
    if (!empty($spec['examples']) && is_array($spec['examples'])) {
        foreach ($spec['examples'] as $idx => $ex) {
            $num = $idx + 1;
            $exTitle = $ex['title'] ?? ("Example $num: Practical Implementation");
            $exFile = $ex['file'] ?? '';
            $exDesc = $ex['desc'] ?? '';
            $exLang = $ex['lang'] ?? 'php';
            $exCode = trim((string)($ex['code'] ?? ''));

            $html .= '<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">' . sh_esc($exTitle) . '</h3>' . "\n";
            if ($exDesc !== '') {
                $html .= '<p class="text-slate-600 mb-3">' . sh_inline($exDesc) . '</p>' . "\n";
            }
            if ($exLang === 'visual') {
                $html .= sh_visual_stage($exCode) . "\n\n";
                continue;
            }
            $html .= '<div class="w3-example-box my-6">' . "\n";
            $html .= '  <div class="w3-example-header"><span>' . sh_esc($exTitle) . '</span>';
            if ($exFile !== '') {
                $html .= '<span class="text-xs text-slate-400 font-mono">' . sh_esc($exFile) . '</span>';
            }
            $html .= "</div>\n";
            $html .= '  <pre><code class="language-' . sh_esc($exLang) . '">' . sh_esc($exCode) . '</code></pre>' . "\n";
            $html .= "</div>\n\n";
        }
    }

    // 6. Micro-Points, Edge Cases & Best Practices Callout ("Chhote se chhote points")
    if (!empty($spec['micro_points']) && is_array($spec['micro_points'])) {
        $html .= '<div class="my-8 rounded-2xl border border-emerald-200 bg-emerald-50/60 p-6">' . "\n";
        $html .= '  <h4 class="text-base font-black text-emerald-950 uppercase tracking-wider mb-3">Micro-Details, Edge Cases &amp; Production Best Practices</h4>' . "\n";
        $html .= '  <ul class="space-y-2 text-sm text-slate-700 list-disc pl-5">' . "\n";
        foreach ($spec['micro_points'] as $mp) {
            $html .= '    <li>' . sh_inline($mp) . '</li>' . "\n";
        }
        $html .= "  </ul>\n</div>\n";
    }

    return $html;
}

/**
 * A marketing example is a screen, not a program. Every screen is the same
 * studio: a campaign bar, a dark rail, and the room the lesson is about.
 */
function sh_dm_rail(string $brand, string $sub, array $items, string $active, string $accent = '#ea580c', string $brandColor = '#fb923c', string $foot = ''): string
{
    if ($foot === '') {
        $foot = 'Household list<br>Offer Rs 499 a year';
    }
    $html = '<aside class="nd-rail" style="width:172px;flex:0 0 172px;background:#111827;color:#e5e7eb;padding:16px 12px;font-family:sans-serif;display:flex;flex-direction:column;">'
        . '<div class="nd-brand" style="font-size:10px;letter-spacing:.16em;font-weight:800;color:' . $brandColor . ';">' . sh_esc($brand) . '</div>'
        . '<div class="nd-muted" style="margin:4px 0 14px;font-size:12px;line-height:1.35;color:#9ca3af;">' . sh_esc($sub) . '</div>';
    foreach ($items as $item) {
        $on = $item === $active;
        $html .= '<div class="' . ($on ? 'nd-on' : 'nd-item') . '" style="margin:0 0 4px;padding:9px 10px;border-radius:10px;font-size:13px;font-weight:'
            . ($on ? '800' : '600') . ';'
            . ($on ? 'background:' . $accent . ';color:#ffffff;' : 'color:#d1d5db;')
            . '">' . sh_esc($item) . '</div>';
    }
    $html .= '<div class="nd-muted" style="margin-top:18px;padding-top:12px;border-top:1px solid #1f2937;font-size:11px;line-height:1.45;color:#9ca3af;">' . $foot . '</div>';
    return $html . '</aside>';
}

function sh_dm_first_heading(string $inner, string $fallback): string
{
    if (preg_match('/<h1\b[^>]*>(.*?)<\/h1>/is', $inner, $m)) {
        $text = trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES));
        if ($text !== '') {
            return $text;
        }
    }
    return $fallback;
}

function sh_seo_stage(string $kind, string $inner): array
{
    if ($kind === 'seo-serp') {
        return [
            'Search',
            'The result a person sees',
            sh_dm_rail('SEARCH STUDIO', 'One query, one page', ['Queries', 'Pages', 'Titles', 'Results'], 'Results', '#0f766e', '#5eead4'),
            sh_seo_serp($inner),
        ];
    }
    if ($kind === 'seo-console') {
        return [
            'Search Console',
            'Queries and pages',
            sh_dm_rail('SEARCH STUDIO', 'What search shows you', ['Queries', 'Pages', 'Coverage', 'Links'], 'Queries', '#0f766e', '#5eead4'),
            sh_seo_console($inner),
        ];
    }
    if ($kind === 'seo-index') {
        return [
            'The index',
            'What may be kept',
            sh_dm_rail('SEARCH STUDIO', 'Reachable and worth keeping', ['Coverage', 'Sitemaps', 'Reasons', 'Pages'], 'Coverage', '#0f766e', '#5eead4'),
            sh_seo_index($inner),
        ];
    }
    if ($kind === 'seo-links') {
        return [
            'Links',
            'The next useful page',
            sh_dm_rail('SEARCH STUDIO', 'Links you mean', ['Internal', 'External', 'Anchor', 'Orphans'], 'Internal', '#0f766e', '#5eead4'),
            sh_seo_links($inner),
        ];
    }
    return [
        'The page',
        'What a person sees',
        sh_dm_rail('SEARCH STUDIO', 'milklist.example', ['Page', 'Title', 'Headings', 'Links'], 'Page', '#0f766e', '#5eead4'),
        sh_dm_site($inner),
    ];
}

function sh_seo_serp(string $inner): string
{
    return '<div style="padding:14px;font-family:sans-serif;">'
        . '<div style="font-size:22px;font-weight:800;letter-spacing:-0.03em;color:#1c1917;margin-bottom:10px;">Search</div>'
        . '<div style="display:flex;align-items:center;gap:8px;background:#ffffff;border:1px solid #e2e8f0;border-radius:999px;padding:10px 14px;margin-bottom:10px;font-size:14px;color:#334155;">family shopping list</div>'
        . '<div style="display:flex;gap:14px;font-size:12px;font-weight:800;margin-bottom:12px;"><span style="color:#0f766e;border-bottom:2px solid #0f766e;padding-bottom:4px;">All</span><span style="color:#64748b;">Pages</span></div>'
        . $inner
        . '</div>';
}

function sh_seo_console(string $inner): string
{
    $chips = '';
    foreach (['Queries', 'Pages', 'Coverage', 'Links'] as $chip) {
        $chips .= '<span style="background:#ffffff;border:1px solid #e2e8f0;border-radius:999px;padding:5px 10px;font-size:11px;font-weight:800;color:#0f172a;">' . $chip . '</span>';
    }
    return '<div style="padding:12px;font-family:sans-serif;">'
        . '<div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px;">' . $chips . '</div>'
        . '<div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;padding:8px 8px 4px;">'
        . '<div class="nd-muted" style="padding:6px 8px 0;font-size:10px;letter-spacing:.1em;font-weight:800;color:#64748b;">SEARCH CONSOLE · READ THE ROWS</div>'
        . $inner
        . '</div></div>';
}

function sh_seo_index(string $inner): string
{
    $chips = '';
    foreach ([['Indexed', 'Kept'], ['Excluded', 'A reason'], ['Sitemap', 'A list']] as $pair) {
        $chips .= '<div style="flex:1 1 120px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;padding:8px 10px;">'
            . '<div class="nd-muted" style="font-size:10px;letter-spacing:.08em;font-weight:800;color:#64748b;">' . $pair[0] . '</div>'
            . '<div style="margin-top:2px;font-size:13px;font-weight:800;color:#0f172a;">' . $pair[1] . '</div></div>';
    }
    return '<div style="padding:12px;font-family:sans-serif;">'
        . '<div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:8px;">' . $chips . '</div>'
        . $inner
        . '</div>';
}

function sh_seo_links(string $inner): string
{
    return '<div style="padding:12px;font-family:sans-serif;">'
        . '<div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:8px;">'
        . '<span style="background:#ffffff;border:1px solid #e2e8f0;border-radius:999px;padding:5px 10px;font-size:11px;font-weight:800;color:#0f172a;">From this page</span>'
        . '<span style="background:#ffffff;border:1px solid #ccfbf1;border-radius:999px;padding:5px 10px;font-size:11px;font-weight:800;color:#0f766e;">To the next useful page</span>'
        . '</div>'
        . $inner
        . '</div>';
}

function sh_dm_kind(string $code, string $inner): string
{
    if (preg_match('/data-room="(seo-[a-z]+)"/', $code, $roomMatch)) {
        return $roomMatch[1];
    }
    if (preg_match('/data-room="(xl-[a-z]+)"/', $code, $roomMatch)) {
        return $roomMatch[1];
    }
    if (stripos($code, 'AD SETUP') !== false) {
        return 'ads';
    }
    $docTitle = '';
    if (preg_match('/<title>(.*?)<\/title>/is', $code, $titleMatch)) {
        $docTitle = trim(html_entity_decode(strip_tags($titleMatch[1]), ENT_QUOTES));
    }
    $mailTitles = ['Welcome', 'A shout', 'One letter', 'A menu letter', 'Inbox', 'A trick', 'Two notes', 'Fake segments', 'An easy leave', 'A maze', 'Ask for the letter', 'A purchased file'];
    if (in_array($docTitle, $mailTitles, true) || stripos($inner, 'From Milk List') !== false || stripos($inner, 'Leave this note') !== false || stripos($inner, 'Send me the note') !== false) {
        return 'mail';
    }
    foreach (['utm_', 'Shown.', 'Starts this week', 'Starts:', 'Query.', 'newsletter / email', 'google / cpc', 'Week report'] as $mark) {
        if (stripos($inner, $mark) !== false) {
            return 'report';
        }
    }
    foreach (['ON SCREEN', 'link in bio', 'Where Milk List will show', 'Milk List · Sponsored', '>SEE<'] as $mark) {
        if (stripos($inner, $mark) !== false) {
            return 'social';
        }
    }
    return 'page';
}

function sh_dm_screen(string $id, string $room, string $label, string $rail, string $body, string $barBg = '#fff7ed', string $barLine = '#fed7aa', string $barInk = '#9a3412'): string
{
    $bar = '<div class="nd-top" style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 14px;background:' . $barBg . ';border-bottom:1px solid ' . $barLine . ';font-family:sans-serif;">'
        . '<div style="display:flex;align-items:center;gap:8px;min-width:0;">'
        . '<span style="width:8px;height:8px;border-radius:99px;display:inline-block;background:#10b981;flex:0 0 8px;"></span>'
        . '<span style="font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:' . $barInk . ';">' . sh_esc($room) . '</span>'
        . '</div>'
        . '<span style="font-size:12px;font-weight:700;color:' . $barInk . ';text-align:right;">' . sh_esc($label) . '</span>'
        . '</div>';
    return '<div id="' . $id . '" class="nd-visual" style="all:initial;display:block;box-sizing:border-box;width:100%;font-family:sans-serif;color:#0f172a;background:#ffffff;border:1px solid ' . $barLine . ';border-radius:16px;overflow:hidden;margin:16px 0;line-height:1.45;">'
        . $bar
        . '<div style="display:flex;align-items:stretch;background:#f8fafc;overflow-x:auto;">'
        . $rail
        . '<div style="flex:1;min-width:280px;background:#f8fafc;">' . $body . '</div>'
        . '</div></div>';
}

function sh_dm_site(string $inner): string
{
    return '<div style="background:#e7e5e4;font-family:sans-serif;">'
        . '<div style="display:flex;align-items:center;gap:8px;padding:10px 12px;">'
        . '<span style="width:9px;height:9px;border-radius:99px;background:#fb7185;display:inline-block;"></span>'
        . '<span style="width:9px;height:9px;border-radius:99px;background:#fbbf24;display:inline-block;"></span>'
        . '<span style="width:9px;height:9px;border-radius:99px;background:#34d399;display:inline-block;"></span>'
        . '<span style="flex:1;background:#ffffff;border:1px solid #d6d3d1;border-radius:8px;padding:5px 10px;font-size:12px;color:#57534e;">https://milklist.example/shared-list</span>'
        . '</div>'
        . '<div style="display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 14px;background:#ffffff;border-top:1px solid #e7e5e4;border-bottom:1px solid #e7e5e4;">'
        . '<div style="display:flex;align-items:center;gap:8px;">'
        . '<span style="width:28px;height:28px;border-radius:8px;background:#ea580c;color:#ffffff;font-weight:800;font-size:14px;display:inline-flex;align-items:center;justify-content:center;">M</span>'
        . '<span style="font-weight:800;color:#0f172a;">Milk List</span></div>'
        . '<div style="display:flex;gap:12px;font-size:12px;font-weight:800;color:#57534e;">'
        . '<span>Home</span><span style="color:#ea580c;">Offer</span><span>Price</span></div>'
        . '<span style="background:#059669;color:#ffffff;border-radius:8px;padding:6px 10px;font-size:12px;font-weight:800;">Start</span>'
        . '</div>'
        . '<div style="padding:4px 4px 8px;">' . $inner . '</div>'
        . '<div class="nd-on-dark" style="display:flex;flex-wrap:wrap;justify-content:space-between;gap:8px;padding:10px 14px;background:#111827;color:#e5e7eb;font-size:11px;font-weight:700;">'
        . '<span style="color:#e5e7eb;">Rs 499 a year</span>'
        . '<span style="color:#e5e7eb;">One shared list</span>'
        . '<span style="color:#e5e7eb;">Leave anytime</span></div>'
        . '</div>';
}

function sh_dm_mail(string $inner): string
{
    $subject = sh_dm_first_heading($inner, 'A note from Milk List');
    return '<div style="padding:12px;font-family:sans-serif;">'
        . '<div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;">'
        . '<div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:space-between;padding:12px 14px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">'
        . '<div><div class="nd-muted" style="font-size:10px;letter-spacing:.08em;font-weight:800;color:#64748b;">FROM</div><div style="font-weight:800;color:#0f172a;">Milk List</div></div>'
        . '<div><div class="nd-muted" style="font-size:10px;letter-spacing:.08em;font-weight:800;color:#64748b;">TO</div><div style="font-weight:800;color:#0f172a;">Asha</div></div>'
        . '<div style="min-width:140px;"><div class="nd-muted" style="font-size:10px;letter-spacing:.08em;font-weight:800;color:#64748b;">SUBJECT</div><div style="font-weight:800;color:#0f172a;">' . sh_esc($subject) . '</div></div>'
        . '</div>'
        . '<div style="padding:6px;">' . $inner . '</div>'
        . '</div></div>';
}

function sh_dm_social(string $inner): string
{
    if (preg_match('/<main\b[^>]*>(.*)<\/main>/is', $inner, $m)) {
        $inner = $m[1];
    }
    return '<div style="padding:14px;background:#e7e5e4;font-family:sans-serif;">'
        . '<div style="max-width:480px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">'
        . '<div style="display:flex;align-items:center;gap:10px;padding:12px 14px;">'
        . '<span style="width:36px;height:36px;border-radius:99px;background:#ea580c;color:#ffffff;font-weight:800;display:inline-flex;align-items:center;justify-content:center;">M</span>'
        . '<div style="flex:1;"><div style="font-weight:800;color:#0f172a;font-size:14px;">Milk List</div>'
        . '<div class="nd-muted" style="font-size:12px;color:#64748b;">Sponsored · one job · the offer page</div></div></div>'
        . '<div style="padding:0 14px 12px;color:#0f172a;">' . $inner . '</div>'
        . '<div style="display:flex;border-top:1px solid #e2e8f0;font-size:12px;font-weight:800;">'
        . '<span style="flex:1;text-align:center;padding:10px;color:#475569;">See</span>'
        . '<span style="flex:1;text-align:center;padding:10px;color:#475569;">Reply</span>'
        . '<span style="flex:1;text-align:center;padding:10px;color:#059669;">Open the page</span>'
        . '</div></div></div>';
}

function sh_dm_report(string $inner): string
{
    $chips = '';
    foreach (['Pages', 'Queries', 'Starts', 'Sources'] as $chip) {
        $chips .= '<span style="background:#ffffff;border:1px solid #e2e8f0;border-radius:999px;padding:5px 10px;font-size:11px;font-weight:800;color:#0f172a;">' . $chip . '</span>';
    }
    return '<div style="padding:12px;font-family:sans-serif;">'
        . '<div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px;">' . $chips . '</div>'
        . '<div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;padding:8px 8px 4px;">'
        . '<div class="nd-muted" style="padding:6px 8px 0;font-size:10px;letter-spacing:.1em;font-weight:800;color:#64748b;">THIS WEEK · READ THE ROWS ON THE CARD</div>'
        . $inner
        . '</div></div>';
}

function sh_dm_ads(string $inner): string
{
    $chips = '';
    foreach ([['Objective', 'A start you can count'], ['Ceiling', 'Written on the card'], ['Match', 'Ad and page agree']] as $pair) {
        $chips .= '<div style="flex:1 1 140px;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;padding:8px 10px;">'
            . '<div class="nd-muted" style="font-size:10px;letter-spacing:.08em;font-weight:800;color:#64748b;">' . sh_esc($pair[0]) . '</div>'
            . '<div style="margin-top:2px;font-size:13px;font-weight:800;color:#0f172a;">' . sh_esc($pair[1]) . '</div></div>';
    }
    return '<div style="padding:12px 12px 4px;font-family:sans-serif;">'
        . '<div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:8px;">' . $chips . '</div>'
        . $inner
        . '</div>';
}

function sh_xl_attr(string $inner, string $name, string $fallback): string
{
    if (preg_match('/data-' . $name . '="([^"]*)"/', $inner, $m)) {
        return htmlspecialchars(html_entity_decode($m[1], ENT_QUOTES), ENT_QUOTES);
    }
    return htmlspecialchars($fallback, ENT_QUOTES);
}

function sh_xl_rail(string $active): string
{
    return sh_dm_rail('EXCEL STUDIO', 'April household spend', ['Grid', 'Formulas', 'Charts', 'Data'], $active, '#107c41', '#86efac', 'April spend<br>Household of four');
}

function sh_xl_chrome(string $inner): string
{
    $cell = sh_xl_attr($inner, 'cell', 'A1');
    $fx = sh_xl_attr($inner, 'fx', '');
    $sheet = sh_xl_attr($inner, 'sheet', 'Spend');
    return '<div style="padding:12px;font-family:sans-serif;">'
        . '<div style="display:flex;gap:8px;align-items:center;margin-bottom:8px;">'
        . '<div style="min-width:58px;background:#ffffff;border:1px solid #86efac;border-radius:8px;padding:6px 8px;font-weight:800;font-size:12px;color:#14532d;text-align:center;">' . $cell . '</div>'
        . '<div style="width:28px;height:28px;border-radius:8px;background:#107c41;color:#ffffff;font-weight:800;display:flex;align-items:center;justify-content:center;font-size:12px;flex:0 0 28px;">fx</div>'
        . '<div style="flex:1;background:#ffffff;border:1px solid #d1d5db;border-radius:8px;padding:6px 10px;font-size:13px;font-weight:700;color:#111827;">' . $fx . '</div>'
        . '</div>'
        . $inner
        . '<div style="margin-top:8px;display:flex;gap:6px;">'
        . '<span style="background:#ffffff;border:1px solid #86efac;border-bottom:3px solid #107c41;border-radius:8px 8px 0 0;padding:5px 12px;font-size:12px;font-weight:800;color:#166534;">' . $sheet . '</span>'
        . '<span style="background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px 8px 0 0;padding:5px 12px;font-size:12px;font-weight:700;color:#6b7280;">Prices</span>'
        . '</div></div>';
}

function sh_xl_stage(string $kind, string $inner): array
{
    if ($kind === 'xl-chart') {
        return ['Chart', 'One question, one picture', sh_xl_rail('Charts'), sh_xl_chrome($inner)];
    }
    if ($kind === 'xl-pivot') {
        return ['Pivot', 'A summary of the list', sh_xl_rail('Data'), sh_xl_chrome($inner)];
    }
    if ($kind === 'xl-query') {
        return ['Query', 'Steps you can replay', sh_xl_rail('Data'), sh_xl_chrome($inner)];
    }
    if ($kind === 'xl-dash') {
        return ['Workbook', 'What the file answers', sh_xl_rail('Grid'), sh_xl_chrome($inner)];
    }
    return ['Sheet', 'The cell and the formula', sh_xl_rail('Formulas'), sh_xl_chrome($inner)];
}

function sh_visual_stage(string $code): string
{
    $code = trim($code);
    if (!preg_match('/<body\b/i', $code)) {
        return sh_serp_card($code);
    }
    $inner = $code;
    if (preg_match('/(<main\b[^>]*>.*<\/main>)/is', $code, $m)) {
        $inner = $m[1];
    } elseif (preg_match('/<body\b[^>]*>(.*)<\/body>/is', $code, $m)) {
        $inner = $m[1];
    }
    $id = 'ndv' . substr(md5($code), 0, 8);
    $inner = preg_replace('/\shref\s*=\s*"[^"]*"/i', ' href="#' . $id . '"', $inner) ?? $inner;
    $inner = preg_replace('/\shref\s*=\s*\'[^\']*\'/i', ' href="#' . $id . '"', $inner) ?? $inner;
    $inner = preg_replace('/<form\b/i', '<div', $inner) ?? $inner;
    $inner = str_ireplace('</form>', '</div>', $inner);

    $kind = sh_dm_kind($code, $inner);
    $barBg = '#fff7ed';
    $barLine = '#fed7aa';
    $barInk = '#9a3412';
    if ($kind === 'ads') {
        $room = 'Ads manager';
        $label = 'How the ad is set';
        $rail = sh_dm_rail('MILK LIST ADS', 'Campaign, audience, ad', ['Campaigns', 'Audiences', 'Ads', 'Results'], 'Ads');
        $body = sh_dm_ads($inner);
    } elseif ($kind === 'mail') {
        $room = 'Inbox';
        $label = 'The letter they open';
        $rail = sh_dm_rail('MILK LIST MAIL', 'Asha asked for this', ['Inbox', 'Sent', 'This letter', 'Leave'], 'This letter');
        $body = sh_dm_mail($inner);
    } elseif ($kind === 'report') {
        $room = 'Measurement';
        $label = 'What the report shows';
        $rail = sh_dm_rail('MILK LIST REPORT', 'This week only', ['Pages', 'Queries', 'Starts', 'Sources'], 'Starts');
        $body = sh_dm_report($inner);
    } elseif ($kind === 'social') {
        $room = 'The feed';
        $label = 'What they see';
        $rail = sh_dm_rail('MILK LIST SOCIAL', 'One post, one job', ['Post', 'Comments', 'Video', 'Room'], 'Post');
        $body = sh_dm_social($inner);
    } elseif (strncmp($kind, 'seo-', 4) === 0) {
        [$room, $label, $rail, $body] = sh_seo_stage($kind, $inner);
    } elseif (strncmp($kind, 'xl-', 3) === 0) {
        [$room, $label, $rail, $body] = sh_xl_stage($kind, $inner);
        $barBg = '#ecfdf5';
        $barLine = '#86efac';
        $barInk = '#166534';
    } else {
        $room = 'The website';
        $label = 'What a person sees';
        $rail = sh_dm_rail('MILK LIST SITE', 'milklist.example', ['Home', 'Offer', 'Price', 'Start'], 'Offer');
        $body = sh_dm_site($inner);
    }

    return sh_dm_screen($id, $room, $label, $rail, $body, $barBg, $barLine, $barInk);
}

function sh_serp_card(string $code): string
{
    $title = '';
    $desc = '';
    $url = 'milklist.example/shared-list';
    if (preg_match('/<title>(.*?)<\/title>/is', $code, $m)) {
        $title = trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES));
    }
    if (preg_match('/name="description"\s+content="(.*?)"/is', $code, $m)) {
        $desc = trim(html_entity_decode($m[1], ENT_QUOTES));
    }
    if (preg_match('/rel="canonical"\s+href="https?:\/\/(.*?)"/is', $code, $m)) {
        $url = rtrim($m[1], '/');
    }
    $tLen = strlen($title);
    $dLen = strlen($desc);
    $tOk = $tLen >= 50 && $tLen <= 60;
    $dOk = $dLen >= 120 && $dLen <= 160;
    $pill = static function (string $label, bool $ok) {
        $cls = $ok
            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
            : 'bg-amber-50 text-amber-800 border-amber-200';
        return '<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-bold ' . $cls . '">' . sh_esc($label) . '</span>';
    };
    $checks = '<div style="flex:0 1 210px;background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;padding:12px;">'
        . '<div class="nd-muted" style="font-size:10px;letter-spacing:.1em;font-weight:800;color:#64748b;">CHECKS</div>'
        . '<div style="margin-top:10px;">' . $pill('Title ' . $tLen . ' characters', $tOk) . '</div>'
        . '<div style="margin-top:8px;">' . $pill('Description ' . $dLen . ' characters', $dOk) . '</div>'
        . '<div class="nd-muted" style="margin-top:12px;font-size:12px;line-height:1.45;color:#64748b;">Title about 50 to 60. Description about 120 to 160. The line still has to be true.</div>'
        . '</div>';
    $body = '<div style="display:flex;flex-wrap:wrap;gap:12px;padding:14px;font-family:sans-serif;">'
        . '<div style="flex:1 1 280px;min-width:240px;">'
        . '<div style="font-size:22px;font-weight:800;letter-spacing:-0.03em;color:#1c1917;margin-bottom:10px;">Search</div>'
        . '<div style="display:flex;align-items:center;gap:8px;background:#ffffff;border:1px solid #e2e8f0;border-radius:999px;padding:10px 14px;margin-bottom:10px;font-size:14px;color:#334155;">family shopping list</div>'
        . '<div style="display:flex;gap:14px;font-size:12px;font-weight:800;margin-bottom:12px;"><span style="color:#ea580c;border-bottom:2px solid #ea580c;padding-bottom:4px;">All</span><span style="color:#64748b;">Pages</span></div>'
        . '<div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;padding:16px;">'
        . '<div style="font-size:13px;color:#059669;">' . sh_esc($url) . '</div>'
        . '<div style="margin-top:4px;font-size:20px;line-height:1.3;color:#1a0dab;font-weight:500;">' . sh_esc($title) . '</div>'
        . '<div style="margin-top:6px;font-size:14px;line-height:1.5;color:#44403c;">' . sh_esc($desc) . '</div>'
        . '</div></div>'
        . $checks
        . '</div>';
    return sh_dm_screen(
        'ndserp' . substr(md5($title . $desc), 0, 8),
        'Search',
        'The result on the page',
        sh_dm_rail('MILK LIST SEARCH', 'One query, one page', ['Queries', 'Pages', 'Titles', 'Results'], 'Results'),
        $body
    );
}

/**
 * Upserts a course and cleanly replaces its chapters in a single transaction
 */
function upsert_course_with_chapters(PDO $pdo, array $courseMeta, array $chapters): int
{
    $slug = $courseMeta['slug'];
    $title = $courseMeta['title'];
    $desc = $courseMeta['description'];
    $keywords = $courseMeta['keywords'];

    $stmt = $pdo->prepare("SELECT id FROM courses WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    $pdo->beginTransaction();
    try {
        if ($existing) {
            $courseId = (int)$existing['id'];
            $upd = $pdo->prepare("UPDATE courses SET title = ?, description = ?, keywords = ? WHERE id = ?");
            $upd->execute([$title, $desc, $keywords, $courseId]);
        } else {
            $ins = $pdo->prepare("INSERT INTO courses (title, slug, description, keywords, created_at) VALUES (?, ?, ?, ?, NOW())");
            $ins->execute([$title, $slug, $desc, $keywords]);
            $courseId = (int)$pdo->lastInsertId();
        }

        $pdo->prepare("DELETE FROM chapters WHERE course_id = ?")->execute([$courseId]);

        $chapStmt = $pdo->prepare("
            INSERT INTO chapters (course_id, order_index, section_name, chapter_name, content, keywords, quiz_json, practice_content, is_premium, require_quiz_pass)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0)
        ");

        foreach ($chapters as $idx => $ch) {
            $order = $idx + 1;
            $htmlContent = isset($ch['content_html']) ? $ch['content_html'] : build_rich_chapter_html($ch);
            $quizJson = !empty($ch['quiz']) ? json_encode($ch['quiz'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;
            $practice = $ch['practice'] ?? null;
            $chapStmt->execute([
                $courseId,
                $order,
                $ch['section'],
                $ch['title'],
                $htmlContent,
                $ch['keywords'],
                $quizJson,
                $practice,
            ]);
        }

        $pdo->commit();
        echo sprintf("[SEEDED] %-36s (ID=%d, slug=%s) -> %d chapters\n", $title, $courseId, $slug, count($chapters));
        return $courseId;
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}
