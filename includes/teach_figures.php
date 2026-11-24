<?php
declare(strict_types=1);

require_once __DIR__ . '/seed_helper.php';

/**
 * Teaching pictures for chapter pages. Each figure explains one idea.
 * teach_figure() picks the picture from the chapter title.
 */

function tf_clip(string $s, int $n = 18): string
{
    $s = trim($s);
    if (strlen($s) <= $n) {
        return $s;
    }
    return substr($s, 0, $n - 1) . '…';
}

function tf_figure(string $kind, string $title, string $svg, string $caption): string
{
    $out = '<figure class="my-7 bg-white rounded-2xl p-5 border border-slate-200 shadow-sm overflow-x-auto" data-nectra-figure="1" data-figure-kind="' . sh_esc($kind) . '">';
    $out .= '<div class="flex items-center justify-between gap-2 mb-3"><span class="text-xs font-black uppercase tracking-wider text-slate-800">' . sh_esc($title) . '</span><span class="text-[11px] font-semibold text-sky-700 bg-sky-50 border border-sky-200 px-2.5 py-0.5 rounded-full">Picture</span></div>';
    $out .= $svg;
    $out .= '<figcaption class="text-xs text-slate-500 mt-2 border-t border-slate-100 pt-2">' . sh_inline($caption) . '</figcaption>';
    $out .= '</figure>';
    return $out;
}

function tf_svg(int $w, int $h, string $label, string $body): string
{
    return '<svg viewBox="0 0 ' . $w . ' ' . $h . '" class="w-full h-auto" role="img" aria-label="' . sh_esc($label) . '">' . $body . '</svg>';
}

function tf_mark(string $html, string $kind): string
{
    return preg_replace('/<figure /', '<figure data-nectra-figure="1" data-figure-kind="' . sh_esc($kind) . '" ', $html, 1) ?? $html;
}

function tf_flow(string $kind, string $title, array $steps, string $caption): string
{
    $n = max(1, count($steps));
    $boxW = 148;
    $gap = 34;
    $pad = 12;
    $w = $pad * 2 + $n * $boxW + ($n - 1) * $gap;
    $colors = ['#059669', '#0284c7', '#d97706', '#7c3aed', '#e11d48'];
    $body = '';
    foreach ($steps as $i => $step) {
        $label = is_array($step) ? (string)$step[0] : (string)$step;
        $sub = is_array($step) ? (string)($step[1] ?? '') : '';
        $x = $pad + $i * ($boxW + $gap);
        $c = $colors[$i % 5];
        $body .= '<rect x="' . $x . '" y="24" width="' . $boxW . '" height="74" rx="12" fill="#f8fafc" stroke="' . $c . '" stroke-width="2"/>';
        $body .= '<text x="' . ($x + $boxW / 2) . '" y="' . ($sub === '' ? 66 : 56) . '" text-anchor="middle" fill="#0f172a" font-size="13" font-weight="700" font-family="system-ui,sans-serif">' . sh_esc(tf_clip($label, 16)) . '</text>';
        if ($sub !== '') {
            $body .= '<text x="' . ($x + $boxW / 2) . '" y="78" text-anchor="middle" fill="#64748b" font-size="11" font-family="system-ui,sans-serif">' . sh_esc(tf_clip($sub, 20)) . '</text>';
        }
        if ($i < $n - 1) {
            $x1 = $x + $boxW + 3;
            $x2 = $x + $boxW + $gap - 8;
            $body .= '<line x1="' . $x1 . '" y1="61" x2="' . $x2 . '" y2="61" stroke="#94a3b8" stroke-width="2"/>';
            $body .= '<polygon points="' . $x2 . ',61 ' . ($x2 - 8) . ',56 ' . ($x2 - 8) . ',66" fill="#94a3b8"/>';
        }
    }
    return tf_figure($kind, $title, tf_svg(max(680, $w), 118, $title, $body), $caption);
}

function tf_cells(string $kind, string $title, array $cells, string $caption, array $hot = [], string $note = ''): string
{
    $n = max(1, count($cells));
    $cw = 62;
    $gap = 10;
    $pad = 20;
    $w = max(680, $pad * 2 + $n * $cw + ($n - 1) * $gap);
    $body = '';
    if ($note !== '') {
        $body .= '<text x="20" y="20" fill="#475569" font-size="12" font-weight="600" font-family="system-ui,sans-serif">' . sh_esc($note) . '</text>';
    }
    foreach ($cells as $i => $cell) {
        $idx = is_array($cell) ? (string)$cell[0] : (string)$i;
        $val = is_array($cell) ? (string)$cell[1] : (string)$cell;
        $x = $pad + $i * ($cw + $gap);
        $on = in_array($i, $hot, true);
        $body .= '<text x="' . ($x + $cw / 2) . '" y="40" text-anchor="middle" fill="#94a3b8" font-size="11" font-family="monospace">' . sh_esc($idx) . '</text>';
        $body .= '<rect x="' . $x . '" y="48" width="' . $cw . '" height="50" rx="10" fill="' . ($on ? '#d1fae5' : '#f8fafc') . '" stroke="' . ($on ? '#059669' : '#cbd5e1') . '" stroke-width="2"/>';
        $body .= '<text x="' . ($x + $cw / 2) . '" y="79" text-anchor="middle" fill="#0f172a" font-size="15" font-weight="700" font-family="monospace">' . sh_esc(tf_clip($val, 5)) . '</text>';
    }
    return tf_figure($kind, $title, tf_svg($w, 116, $title, $body), $caption);
}

function tf_split(string $kind, string $title, string $question, string $yes, string $yesSub, string $no, string $noSub, string $caption): string
{
    $body = '<rect x="230" y="12" width="220" height="44" rx="10" fill="#fff7ed" stroke="#d97706" stroke-width="2"/>';
    $body .= '<text x="340" y="40" text-anchor="middle" fill="#9a3412" font-size="13" font-weight="700" font-family="system-ui,sans-serif">' . sh_esc(tf_clip($question, 24)) . '</text>';
    $body .= '<line x1="340" y1="56" x2="170" y2="86" stroke="#94a3b8" stroke-width="2"/>';
    $body .= '<line x1="340" y1="56" x2="510" y2="86" stroke="#94a3b8" stroke-width="2"/>';
    $body .= '<rect x="60" y="88" width="220" height="64" rx="12" fill="#ecfdf5" stroke="#059669" stroke-width="2"/>';
    $body .= '<text x="170" y="114" text-anchor="middle" fill="#065f46" font-size="14" font-weight="700" font-family="system-ui,sans-serif">' . sh_esc(tf_clip($yes, 18)) . '</text>';
    $body .= '<text x="170" y="134" text-anchor="middle" fill="#047857" font-size="11" font-family="system-ui,sans-serif">' . sh_esc(tf_clip($yesSub, 24)) . '</text>';
    $body .= '<rect x="400" y="88" width="220" height="64" rx="12" fill="#fff1f2" stroke="#e11d48" stroke-width="2"/>';
    $body .= '<text x="510" y="114" text-anchor="middle" fill="#9f1239" font-size="14" font-weight="700" font-family="system-ui,sans-serif">' . sh_esc(tf_clip($no, 18)) . '</text>';
    $body .= '<text x="510" y="134" text-anchor="middle" fill="#be123c" font-size="11" font-family="system-ui,sans-serif">' . sh_esc(tf_clip($noSub, 24)) . '</text>';
    return tf_figure($kind, $title, tf_svg(680, 168, $title, $body), $caption);
}

function tf_panels(string $kind, string $title, string $left, array $leftLines, string $right, array $rightLines, string $caption): string
{
    $body = '';
    foreach ([[18, '#ecfdf5', '#059669', $left, $leftLines], [350, '#eff6ff', '#0284c7', $right, $rightLines]] as $p) {
        [$x, $fill, $stroke, $head, $lines] = $p;
        $body .= '<rect x="' . $x . '" y="16" width="312" height="150" rx="14" fill="' . $fill . '" stroke="' . $stroke . '" stroke-width="2"/>';
        $body .= '<text x="' . ($x + 156) . '" y="42" text-anchor="middle" fill="#0f172a" font-size="14" font-weight="800" font-family="system-ui,sans-serif">' . sh_esc(tf_clip($head, 22)) . '</text>';
        $y = 68;
        foreach ($lines as $line) {
            $body .= '<text x="' . ($x + 20) . '" y="' . $y . '" fill="#334155" font-size="13" font-family="ui-monospace,monospace">' . sh_esc($line) . '</text>';
            $y += 22;
        }
    }
    return tf_figure($kind, $title, tf_svg(680, 182, $title, $body), $caption);
}

function tf_layers(string $kind, string $title, array $bands, string $caption): string
{
    $n = max(1, count($bands));
    $row = 42;
    $h = 12 + $n * $row + 6;
    $body = '';
    foreach ($bands as $i => $band) {
        $y = 10 + $i * $row;
        $x = 16 + $i * 28;
        $w = 664 - $x;
        $body .= '<rect x="' . $x . '" y="' . $y . '" width="' . $w . '" height="32" rx="8" fill="' . $band[1] . '" stroke="' . $band[2] . '" stroke-width="2"/>';
        $body .= '<text x="' . ($x + 14) . '" y="' . ($y + 21) . '" fill="#0f172a" font-size="13" font-weight="700" font-family="system-ui,sans-serif">' . sh_esc(tf_clip((string)$band[0], 48)) . '</text>';
    }
    return tf_figure($kind, $title, tf_svg(680, $h, $title, $body), $caption);
}

function tf_grid(string $kind, string $title, array $cols, array $rows, array $cells, string $caption, array $hot = []): string
{
    $cN = max(1, count($cols));
    $rN = max(1, count($rows));
    $cw = 130;
    $rh = 42;
    $lw = 130;
    $body = '';
    foreach ($cols as $i => $col) {
        $x = $lw + $i * $cw;
        $body .= '<text x="' . ($x + $cw / 2) . '" y="28" text-anchor="middle" fill="#475569" font-size="12" font-weight="700" font-family="system-ui,sans-serif">' . sh_esc(tf_clip($col, 16)) . '</text>';
    }
    foreach ($rows as $r => $row) {
        $y = 40 + $r * $rh;
        $body .= '<text x="12" y="' . ($y + 26) . '" fill="#475569" font-size="12" font-weight="700" font-family="system-ui,sans-serif">' . sh_esc(tf_clip($row, 16)) . '</text>';
        for ($c = 0; $c < $cN; $c++) {
            $x = $lw + $c * $cw;
            $on = in_array($r * $cN + $c, $hot, true);
            $body .= '<rect x="' . $x . '" y="' . $y . '" width="' . ($cw - 8) . '" height="' . ($rh - 6) . '" rx="8" fill="' . ($on ? '#d1fae5' : '#f8fafc') . '" stroke="' . ($on ? '#059669' : '#e2e8f0') . '"/>';
            $body .= '<text x="' . ($x + ($cw - 8) / 2) . '" y="' . ($y + 24) . '" text-anchor="middle" fill="#0f172a" font-size="13" font-weight="700" font-family="system-ui,sans-serif">' . sh_esc((string)($cells[$r * $cN + $c] ?? '')) . '</text>';
        }
    }
    $h = 52 + $rN * $rh;
    return tf_figure($kind, $title, tf_svg(max(680, $lw + $cN * $cw + 10), $h, $title, $body), $caption);
}

function tf_tree(string $kind, string $title, array $nodes, string $caption): string
{
    $pos = [[340, 28], [180, 88], [500, 88], [90, 156], [250, 156], [430, 156], [590, 156]];
    $edges = [[0, 1], [0, 2], [1, 3], [1, 4], [2, 5], [2, 6]];
    $body = '';
    foreach ($edges as [$a, $b]) {
        if (($nodes[$a] ?? '') === '' || ($nodes[$b] ?? '') === '') {
            continue;
        }
        $body .= '<line x1="' . $pos[$a][0] . '" y1="' . ($pos[$a][1] + 16) . '" x2="' . $pos[$b][0] . '" y2="' . ($pos[$b][1] - 16) . '" stroke="#94a3b8" stroke-width="2"/>';
    }
    foreach ($nodes as $i => $label) {
        if ($label === '' || $label === null) {
            continue;
        }
        [$x, $y] = $pos[$i];
        $body .= '<circle cx="' . $x . '" cy="' . $y . '" r="20" fill="#eff6ff" stroke="#0284c7" stroke-width="2"/>';
        $body .= '<text x="' . $x . '" y="' . ($y + 4) . '" text-anchor="middle" fill="#0f172a" font-size="12" font-weight="700" font-family="monospace">' . sh_esc(tf_clip((string)$label, 4)) . '</text>';
    }
    return tf_figure($kind, $title, tf_svg(680, 196, $title, $body), $caption);
}

function tf_chain(string $kind, string $title, array $nodes, string $caption): string
{
    $body = '';
    $n = count($nodes);
    foreach ($nodes as $i => $label) {
        $x = 24 + $i * 120;
        $body .= '<rect x="' . $x . '" y="48" width="78" height="48" rx="10" fill="#f8fafc" stroke="#0284c7" stroke-width="2"/>';
        $body .= '<text x="' . ($x + 39) . '" y="77" text-anchor="middle" fill="#0f172a" font-size="14" font-weight="700" font-family="monospace">' . sh_esc(tf_clip((string)$label, 6)) . '</text>';
        if ($i < $n - 1) {
            $body .= '<line x1="' . ($x + 82) . '" y1="72" x2="' . ($x + 108) . '" y2="72" stroke="#64748b" stroke-width="2"/>';
            $body .= '<polygon points="' . ($x + 112) . ',72 ' . ($x + 102) . ',67 ' . ($x + 102) . ',77" fill="#64748b"/>';
        }
    }
    $body .= '<text x="' . (24 + ($n - 1) * 120 + 39) . '" y="116" text-anchor="middle" fill="#94a3b8" font-size="11" font-family="system-ui,sans-serif">null</text>';
    return tf_figure($kind, $title, tf_svg(max(680, 40 + $n * 120), 136, $title, $body), $caption);
}

function tf_pile(string $kind, string $title, array $items, string $caption): string
{
    $body = '<text x="250" y="22" fill="#64748b" font-size="12" font-weight="700" font-family="system-ui,sans-serif">top</text>';
    $y = 32;
    foreach ($items as $i => $item) {
        $fill = $i === 0 ? '#d1fae5' : '#f8fafc';
        $body .= '<rect x="250" y="' . $y . '" width="180" height="36" rx="8" fill="' . $fill . '" stroke="#059669" stroke-width="2"/>';
        $body .= '<text x="340" y="' . ($y + 23) . '" text-anchor="middle" fill="#0f172a" font-size="14" font-weight="700" font-family="monospace">' . sh_esc($item) . '</text>';
        $y += 42;
    }
    $body .= '<text x="250" y="' . ($y + 8) . '" fill="#64748b" font-size="12" font-weight="700" font-family="system-ui,sans-serif">bottom</text>';
    return tf_figure($kind, $title, tf_svg(680, $y + 24, $title, $body), $caption);
}

function tf_graph(string $kind, string $title, array $nodes, array $edges, string $caption): string
{
    $body = '';
    $at = [];
    foreach ($nodes as $node) {
        $at[$node[0]] = [$node[1], $node[2]];
    }
    foreach ($edges as $e) {
        [$a, $b] = $e;
        $body .= '<line x1="' . $at[$a][0] . '" y1="' . $at[$a][1] . '" x2="' . $at[$b][0] . '" y2="' . $at[$b][1] . '" stroke="#94a3b8" stroke-width="2"/>';
        if (isset($e[2]) && $e[2] !== '') {
            $mx = ($at[$a][0] + $at[$b][0]) / 2;
            $my = ($at[$a][1] + $at[$b][1]) / 2 - 6;
            $body .= '<text x="' . $mx . '" y="' . $my . '" text-anchor="middle" fill="#b45309" font-size="12" font-weight="700" font-family="monospace">' . sh_esc((string)$e[2]) . '</text>';
        }
    }
    foreach ($nodes as $node) {
        $body .= '<circle cx="' . $node[1] . '" cy="' . $node[2] . '" r="22" fill="#eff6ff" stroke="#0284c7" stroke-width="2"/>';
        $body .= '<text x="' . $node[1] . '" y="' . ($node[2] + 4) . '" text-anchor="middle" fill="#0f172a" font-size="13" font-weight="800" font-family="system-ui,sans-serif">' . sh_esc($node[0]) . '</text>';
    }
    return tf_figure($kind, $title, tf_svg(680, 200, $title, $body), $caption);
}

function tf_dots(string $kind, string $title, array $points, string $caption): string
{
    $body = '<line x1="40" y1="160" x2="640" y2="160" stroke="#e2e8f0"/>';
    $body .= '<line x1="40" y1="20" x2="40" y2="160" stroke="#e2e8f0"/>';
    foreach ($points as $p) {
        $body .= '<circle cx="' . $p[0] . '" cy="' . $p[1] . '" r="7" fill="' . $p[2] . '"/>';
        if (($p[3] ?? '') !== '') {
            $body .= '<text x="' . ($p[0] + 10) . '" y="' . ($p[1] - 8) . '" fill="#334155" font-size="11" font-family="system-ui,sans-serif">' . sh_esc($p[3]) . '</text>';
        }
    }
    return tf_figure($kind, $title, tf_svg(680, 190, $title, $body), $caption);
}

function tf_object(string $kind, string $title, string $name, array $fields, string $caption): string
{
    $h = 56 + count($fields) * 28;
    $body = '<rect x="180" y="16" width="320" height="' . ($h - 8) . '" rx="14" fill="#f8fafc" stroke="#7c3aed" stroke-width="2"/>';
    $body .= '<text x="340" y="42" text-anchor="middle" fill="#5b21b6" font-size="14" font-weight="800" font-family="system-ui,sans-serif">' . sh_esc($name) . '</text>';
    $y = 68;
    foreach ($fields as $field) {
        $body .= '<text x="206" y="' . $y . '" fill="#0f172a" font-size="13" font-family="ui-monospace,monospace">' . sh_esc($field) . '</text>';
        $y += 26;
    }
    return tf_figure($kind, $title, tf_svg(680, max(150, $h + 16), $title, $body), $caption);
}

function tf_bars(string $kind, string $title, array $bars, string $caption): string
{
    return tf_mark(svg_bar_chart($title, $bars, '', $caption), $kind);
}

function tf_line(string $kind, string $title, array $labels, array $values, string $caption, string $color = '#059669'): string
{
    return tf_mark(svg_line_graph($title, $labels, $values, '', $color, $caption), $kind);
}

function tf_key_hits(string $title, string $key): bool
{
    $t = strtolower($title);
    $len = strlen($key);
    $offset = 0;
    while (($i = strpos($t, $key, $offset)) !== false) {
        $coreStart = $i;
        $coreEnd = $i + $len;
        while ($coreStart < $coreEnd && !ctype_alpha($t[$coreStart])) {
            $coreStart++;
        }
        while ($coreEnd > $coreStart && !ctype_alpha($t[$coreEnd - 1])) {
            $coreEnd--;
        }
        $before = $coreStart > 0 ? $t[$coreStart - 1] : ' ';
        $after = $coreEnd < strlen($t) ? $t[$coreEnd] : ' ';
        $rest = substr($t, $coreEnd);
        $afterOk = !ctype_alpha($after) || $after === 's' || (bool)preg_match('/^al(ly)?(?![a-z])/', $rest);
        if (!ctype_alpha($before) && $afterOk) {
            return true;
        }
        $offset = $i + 1;
    }
    return false;
}

function teach_figure(string $slug, string $title): string
{
    foreach (teach_rules() as $rule) {
        foreach ($rule[1] as $key) {
            if (tf_key_hits($title, $key)) {
                return $rule[2]();
            }
        }
    }
    return teach_fallback($slug, $title);
}

function teach_rules(): array
{
    $g = static function (string $kind, array $keys, callable $make): array {
        return [$kind, $keys, $make];
    };
    return [
        $g('assistant', ['note assistant'], fn() => tf_flow('assistant', 'A note assistant you can explain', [['question', 'what was asked'], ['note', 'the source'], ['reply', 'a quote or a refusal']], 'The reply is allowed to quote the note or say it does not know. Every sentence has to point at one of those two.')),
        $g('filters', ['built-in filter', 'pipe chaining', 'orderby'], fn() => tf_flow('filters', 'A filter reshapes a value for display', [['value', '21.5'], ['currency', 'the filter'], ['screen', '$21.50']], 'The stored value stays a number. The filter only changes what the person sees. Chaining filters runs the next one on the previous result.')),
        $g('bytes', ['typedarray', 'arraybuffer', 'dataview'], fn() => tf_cells('bytes', 'A buffer is bytes, a view reads them as numbers', [['byte', '4'], ['byte', '0'], ['uint16', '4']], 'The buffer is the raw row. A view decides how many bytes make one number. The same bytes can be read as a different width.', [0, 1])),
        $g('clock', ['asynchronous timer', 'math, dates'], fn() => tf_flow('clock', 'Math, a calendar, and a timer are three tools', [['math', 'round, max'], ['date', 'the moment'], ['timer', 'call me later']], 'A date is a moment on a calendar. A timer is a callback in the future. Math does not wait, and a timer does not know what day it is.')),
        $g('boundary', ['componentdidcatch', 'fallback ui'], fn() => tf_flow('boundary', 'A boundary catches a child that throws', [['child', 'throws'], ['boundary', 'catches'], ['fallback', 'the spare UI']], 'The rest of the page stays up. The boundary shows the fallback instead of the broken child. It does not fix the child. It contains it.')),
        $g('bigint', ['bigint'], fn() => tf_panels('bigint', 'A bigger integer, still an integer', 'number', ['about 15 safe digits', 'then it rounds'], 'bigint', ['as many digits as you need', 'no fraction', 'not mixed with number'], 'A bigint does not round the way a number does. You cannot add a bigint and a number until you choose one type.')),
        $g('imageopt', ['next/image', 'image optimization'], fn() => tf_flow('imageopt', 'A big file becomes the size the screen needs', [['file', 'the original'], ['resize', 'the width asked'], ['browser', 'a smaller picture']], 'The page asks for a width. The server can send a smaller file and a modern format. The layout box stays the size you reserved, so the page does not jump.')),
        $g('rls', ['row-level'], fn() => tf_split('rls', 'The same query, two people, two sets of rows', 'is this their row?', 'Show', 'the policy allows it', 'Hide', 'the policy drops it', 'The query does not change. The policy adds a filter the person cannot see. A row can exist and still be invisible to them.')),
        $g('guard', ['route protection'], fn() => tf_flow('guard', 'The check runs before the page', [['request', 'the URL'], ['guard', 'the role'], ['page', 'only if allowed']], 'A person without the role never reaches the page. The redirect happens in the guard. The page can assume the check already passed.')),
        $g('learned', ['learned rule', 'and a model', 'learned from examples', 'rule learned'], fn() => tf_flow('learned', 'Three different kinds of rule', [['written', 'you typed it'], ['learned', 'from examples'], ['model', 'the thing you run']], 'A written rule is if-statements. A learned rule is weights chosen from examples. The model is what you run after that choice.')),
        $g('rows', ['rows, features', 'features, and labels'], fn() => tf_grid('rows', 'A table of examples', ['pages', 'urgent', 'label'], ['note 1', 'note 2'], ['2', '0', 'calm', '9', '1', 'urgent'], 'Each row is one example. The columns you may use are features. The column you are trying to guess is the label, and it must not be copied into a feature.', [2, 5])),
        $g('extra-list', ['extra list'], fn() => tf_panels('extra-list', 'The extra list is space you chose', 'input', ['milk, tea, bread', 'already paid for', 'not your extra space'], 'your copy', ['a second row', 'you allocated it', 'this is the extra space'], 'The input does not count as extra space. A new list the same length does. A handful of counters does not.')),
        $g('three-sort', ['three numbers in order'], fn() => tf_cells('three-sort', 'Three numbers, put into order', [['', '9'], ['', '2'], ['', '5'], ['done', '2'], ['done', '5'], ['done', '9']], 'Compare a pair, swap if they are out of order, and do not forget the pair you have not touched yet.', [3, 4, 5])),
        $g('corrupt', ['corrupt a program'], fn() => tf_panels('corrupt', 'A mistake that writes past the row', 'the row', ['slots 0 1 2', 'length 3', 'index 2 is the last'], 'the mistake', ['write index 3', 'that is not a slot', 'the next value is damaged'], 'The language will not always stop you. The picture is the slot that was never yours.')),
        $g('notebook', ['reading notebook'], fn() => tf_object('notebook', 'One note in the notebook', 'note', ['title: milk', 'pages: 4', 'urgent: no'], 'The project is a list of these bundles. Adding a note appends one. Finding a note walks the list until the title matches.')),
        $g('urgent-project', ['note urgent', 'is this note'], fn() => tf_flow('urgent-project', 'From a note to a yes or no you can explain', [['note', 'the words'], ['features', 'counts you chose'], ['score', 'urgent?']], 'Every number in the score has to point back at a step you can say out loud. A hidden library call is not an explanation.')),
        $g('defend', ['score you can defend', 'you can defend'], fn() => tf_grid('defend', 'The score is a table, not a vibe', ['hit', 'miss'], ['called urgent', 'called calm'], ['right yes', 'false alarm', 'missed urgent', 'right calm'], 'You defend the score by showing these four counts, the baseline, and the pile the rows came from.', [0, 2])),
        $g('listeners', ['event listener', 'event handling', 'synthetic event'], fn() => tf_flow('listeners', 'A person acts, a listener runs', [['click', 'the event'], ['listener', 'your function'], ['page', 'what changes']], 'The listener is registered on an element. The event object says what happened. The function decides what the page does next.')),
        $g('weakmap', ['weakmap', 'map and weak', 'key-value store'], fn() => tf_panels('weakmap', 'A map keeps pairs, an object keeps string keys', 'map', ['any key', 'a note object', 'size is known'], 'object', ['string keys', 'prototype sits behind', 'easy to collide'], 'A WeakMap holds objects as keys and does not keep them alive by itself. A plain object turns every key into a string.')),
        $g('select', ['select & distinct', 'select and distinct'], fn() => tf_grid('select', 'SELECT picks columns, DISTINCT drops repeated rows', ['title', 'price'], ['row', 'same again', 'kept once'], ['milk', '2', 'milk', '2', '—', '—'], 'SELECT * returns every column. Naming columns returns only those. DISTINCT compares the whole chosen row.', [0, 1])),
        $g('indent', ['indentation'], fn() => tf_panels('indent', 'The indent is the block', 'this block', ['if ready:', '    print yes', 'the print belongs to the if'], 'a bug', ['if ready:', 'print yes', 'the print always runs'], 'Python does not use braces for this. The spaces are the structure. Mix tabs and spaces and the block is a different one than you see.')),
        $g('echo', ['echo and print', 'echo / print'], fn() => tf_flow('echo', 'A value becomes text on the page', [['value', 'the note'], ['echo', 'write it'], ['page', 'the reader sees it']], 'echo and print both write. The thing you pass is turned into text. HTML in that text is still text unless you mark it as raw on purpose.')),
        $g('numbers', ['numbers, math', 'numbers & math', 'int, float'], fn() => tf_panels('numbers', 'Whole numbers and fractions are different types', 'int', ['7 / 2 = 3', 'no fraction', 'the leftover is 1'], 'float', ['7 / 2 = 3.5', 'a fraction', 'the leftover is kept'], 'A float is not “more precise” in every sense. It cannot hold every decimal. Money often wants a decimal type, not a float.')),
        $g('layout', ['one layout', 'nested layout', 'layout.tsx'], fn() => tf_flow('layout', 'One chrome, many pages', [['layout', 'nav, footer'], ['page', 'this screen'], ['browser', 'both together']], 'The layout stays while the page inside it changes. You do not copy the nav into every page.')),
        $g('json-api', ['json api', 'small json'], fn() => tf_flow('json-api', 'A JSON API is a path that returns a shape', [['GET /notes/4', 'the call'], ['handler', 'the note'], ['JSON', '{ title }']], 'The browser or another program reads the shape. It is not a page of HTML. The status still says whether the note existed.')),
        $g('note-test', ['test the note', 'put the app on'], fn() => tf_panels('note-test', 'A rule you can fail on purpose', 'rule', ['title required', 'price not negative', 'owner only'], 'check', ['empty title', 'expect a refusal', 'no row saved'], 'The test names the rule and the bad input. If the app saves the bad note, the test fails. Then you deploy the app that passed.')),
        $g('hooks', ['custom hooks', 'stateful logic'], fn() => tf_flow('hooks', 'A hook is a named bundle of state', [['useNotes()', 'the hook'], ['state', 'the list'], ['screen', 'calls the hook']], 'The hook hides the state and the effects. The screen stays a reader of the values the hook returns.')),
        $g('portal', ['portal'], fn() => tf_layers('portal', 'A portal draws outside the parent box', [
            ['page root', '#f8fafc', '#64748b'],
            ['a modal on the body', '#dbeafe', '#0284c7'],
        ], 'The component lives in your tree. The pixels are painted somewhere else, so a parent with overflow hidden does not clip the menu.')),
        $g('recipes', ['service vs factory', '5 service recipes', 'factory vs provider'], fn() => tf_flow('recipes', 'Five ways to hand out one object', ['value', 'factory', 'service', 'provider'], 'A value is the object. A factory is a function that returns it. A service is that function called with new. A provider can be configured before any of them run.')),
        $g('fulltext', ['full-text', 'full text', 'tsvector', 'match ... against'], fn() => tf_cells('fulltext', 'Search looks at words, not the whole string', [['lab', 'lab'], ['closes', 'closes'], ['at', 'at'], ['9', '9']], 'The column is split into words. A query asks which rows contain those words. It is not the same as LIKE, which looks for a character pattern.', [], 'the note')),
        $g('waterfall', ['waterfall', 'parallel vs sequential'], fn() => tf_panels('waterfall', 'Wait in a line, or wait together', 'sequential', ['fetch user', 'then fetch notes', 'the second waits'], 'parallel', ['fetch user', 'fetch notes', 'both at once'], 'Start the calls that do not need each other together. A waterfall is a later call that sat idle until an earlier one finished.')),
        $g('comments', ['syntax & comments', 'comments'], fn() => tf_panels('comments', 'The computer skips the note to yourself', 'code', ['count = 2', 'this runs'], 'comment', ['// why 2', 'a person reads it', 'the computer skips it'], 'A comment does not change the result. It is for the next person, who may be you. A comment that repeats the code is noise.')),
        $g('slot-name', ['name stores one', 'one value of a type'], fn() => tf_object('slot-name', 'A name is a slot of one type', 'pages', ['type: int', 'value: 4', 'one slot, one value'], 'The name is how the rest of the program talks about the slot. The type says an int lives there. Storing text in it is a different kind of mistake than storing 4.')),
        $g('scope-bus', ['root scope', '$emit', 'event bus'], fn() => tf_flow('scope-bus', 'A child can call up, a parent can call down', [['$emit', 'up to parents'], ['$broadcast', 'down to children'], ['$on', 'someone listens']], '$rootScope is the top of that tree. An event is not a value you return. It is a shout that listeners chose to hear.')),
        $g('controller-as', ['controller as', 'vm pattern'], fn() => tf_object('controller-as', 'vm is the controller, not the scope soup', 'vm', ['vm.title', 'vm.save()', 'the template says vm.title'], 'Controller-as puts the fields on the controller object. The template names that object. You are not hoping the field exists somewhere on a parent scope.')),
        $g('image-lib', ['resize, crop', 'thumbnail', 'watermark'], fn() => tf_flow('image-lib', 'A picture goes through steps', [['file', 'the upload'], ['crop', 'the frame'], ['thumb', 'a small copy']], 'The original file can stay. The thumbnail is a new file. Resize changes pixels. Crop throws the outside away.')),
        $g('request-in', ['incomingrequest', 'baseresponse', 'initcontroller'], fn() => tf_flow('request-in', 'A request arrives, a response leaves', [['URL', 'the path'], ['controller', 'your method'], ['response', 'status and body']], 'The controller reads the request. It does not print by accident. It returns a response the framework sends.')),
        $g('flash', ['flash message', 'friendly failures'], fn() => tf_flow('flash', 'A message that survives one redirect', [['save', 'the note'], ['flash', 'Saved'], ['next page', 'shows it once']], 'The save responds with a redirect. The flash is stored for that next request only. Refreshing should not show it again.')),
        $g('before-laravel', ['before laravel', 'php you must'], fn() => tf_flow('before-laravel', 'The PHP the framework is made of', [['variables', 'a name'], ['arrays', 'a list'], ['classes', 'a bundle']], 'Laravel calls these. If a name, a list, and a class are solid, the framework words have somewhere to land.')),
        $g('pool', ['pooling', 'pgbouncer', 'connection pool'], fn() => tf_flow('pool', 'Many requests, few database connections', [['requests', 'many'], ['pool', 'a handful'], ['database', 'the real connections']], 'Opening a connection is expensive. The pool lends one and takes it back. The database never sees one connection per click.')),
        $g('mongo-insert', ['insertone', 'insertmany', 'bulk write'], fn() => tf_flow('mongo-insert', 'One document, or many', [['insertOne', 'a single note'], ['insertMany', 'a list'], ['ordered', 'stop on first error']], 'Ordered bulk stops at the first refusal. Unordered tries the rest. Each document is one JSON object, not a row of columns.')),
        $g('mongo-update', ['replaceone', 'updateone', 'deleting documents'], fn() => tf_panels('mongo-update', 'Replace the whole document, or change fields', 'replaceOne', ['the new document', 'fields you omit are gone'], 'updateOne', ['$set title', 'other fields stay', 'a precise edit'], 'Delete removes the document. Update without a filter is how you edit every document. The filter is the WHERE.')),
        $g('fonts', ['next/font', 'font & script', 'next/script'], fn() => tf_flow('fonts', 'A font and a script have a loading plan', [['file', 'the font or script'], ['strategy', 'when it loads'], ['page', 'no jump, no block']], 'next/font keeps the text from swapping late. next/script decides whether a script waits, or loads after the page is usable.')),
        $g('vitals', ['core web vitals', 'bundle-analyzer'], fn() => tf_bars('vitals', 'A big bundle delays the first useful paint', [
            ['label' => 'app code', 'value' => 40, 'display' => '40kb', 'color' => '#059669'],
            ['label' => 'a library', 'value' => 90, 'display' => '90kb', 'color' => '#e11d48'],
        ], 'The analyser shows who owns the bytes. A vital is a measurement of the page a person actually gets, not a feeling that it seems fast.')),
        $g('upgrade', ['ngupgrade', 'dual-boot'], fn() => tf_panels('upgrade', 'Two frameworks, one page, on purpose', 'AngularJS', ['the old tree', 'still running'], 'modern Angular', ['the new tree', 'takes routes over time'], 'Dual-boot means both are alive. You move a screen when it is ready. You do not rewrite the whole product in one step.')),
        $g('symbols', ['well-known symbols', 'symbol.'], fn() => tf_cells('symbols', 'A symbol is a key nobody else can spell', [['id', 'sym'], ['name', 'sym'], ['string', 'no']], 'Two symbols with the same description are still different keys. That is the point. A string key can collide. A symbol key does not.', [0, 1])),
        $g('observer', ['intersection observer', 'mutation observer', 'resize observer'], fn() => tf_flow('observer', 'Watch, then call you when it happens', [['watch', 'the element'], ['change', 'it enters view'], ['you', 'load the next page']], 'You do not poll. The observer calls you when the element crosses the screen, changes size, or gains a child.')),
        $g('workers', ['web workers', 'worker'], fn() => tf_panels('workers', 'Heavy work leaves the page thread', 'page thread', ['clicks', 'painting', 'must stay quick'], 'worker', ['the long loop', 'a copy of the data', 'posts the result back'], 'The worker does not touch the document. You send it data. It sends a result. The page stays able to scroll.')),
        $g('state-mgmt', ['state management', 'reactivity core', 'vanilla reactivity'], fn() => tf_flow('state-mgmt', 'A store, then everyone who reads it', [['store', 'the value'], ['change', 'one write'], ['views', 'they update']], 'The screens do not each keep a private copy. They read the store. One write tells the readers.')),
        $g('react19', ['useoptimistic', 'useactionstate', 'react 19'], fn() => tf_flow('react19', 'Show the hope, then the server answer', [['click', 'save'], ['optimistic', 'show it now'], ['server', 'confirm or undo']], 'The optimistic value is a guess you paint immediately. If the server refuses, you put the real value back.')),
        $g('coords', ['getboundingclientrect', 'coordinates', 'smooth scrolling'], fn() => tf_object('coords', 'A box has a position and a size', 'rect', ['x, y on the screen', 'width, height', 'scrollIntoView moves it'], 'The rectangle is where the element is painted right now. Scrolling changes y. A sticky header is why in view is not the same as at the top.')),
        $g('api-contract', ['api contract'], fn() => tf_flow('api-contract', 'One call has four parts', ['Method', 'Path', 'Headers', 'Body'], 'The method says the action, the path says the thing, headers say the extra rules, and the body carries the payload.')),
        $g('http-methods', ['every http method', 'http method'], fn() => tf_panels('http-methods', 'Safe to retry, or not', 'GET / HEAD', ['Read only', 'Same call, same result', 'A retry is harmless'], 'POST / PATCH', ['Creates or changes', 'A second call can double', 'Send an idempotency key'], 'GET asks. POST changes. Retrying a change without a key can save the note twice.')),
        $g('paths', ['query string', 'paths, query', 'names that stay'], fn() => tf_flow('paths', 'A path, then a query', [['/notes/4', 'the thing'], ['?sort=new', 'how to show it']], 'The path names a stable thing. The query only changes the view: sort, page, or filter.')),
        $g('status', ['status code'], fn() => tf_grid('status', 'The number says what happened', ['Meaning'], ['200', '201', '400', '401', '404', '500'], ['OK', 'Created', 'Your request', 'Not signed in', 'No such thing', 'Server fault'], 'Read the first digit. 2 means it worked, 4 means the caller must change something, 5 means the server failed.')),
        $g('headers', ['headers that change', 'headers,'], fn() => tf_cells('headers', 'Headers ride beside the body', [['Auth', 'token'], ['Type', 'json'], ['Accept', 'json']], 'They are not the note itself. They tell the server who you are and how to read the body.', [0])),
        $g('json', ['json shape', 'json parsing', 'json handling', 'json vs', 'json &'], fn() => tf_object('json', 'A JSON value is a shape', 'note', ['title: "milk"', 'urgent: true', 'pages: 4'], 'An object is named fields. A list is ordered values. A string is not a number, even when it looks like one.')),
        $g('crud', ['create, read, update', 'crud'], fn() => tf_flow('crud', 'Four actions on one note', ['Create', 'Read', 'Update', 'Delete'], 'Create makes a row. Read does not change it. Update replaces fields. Delete removes the row.')),
        $g('page', ['page size', 'paginate', 'pagination', 'limit & offset', 'cursors'], fn() => tf_cells('page', 'A long list is shown in pages', [['page 1', '1–10'], ['page 2', '11–20'], ['page 3', '21–30']], 'The caller asks for a window, not the whole table. The next page starts where this one stopped.', [1], 'window')),
        $g('auth', ['401', 'who owns', 'log in', 'authentication', 'shield auth', 'jwt'], fn() => tf_split('auth', 'Who is allowed to touch this note', 'Token?', 'Allow', 'owner matches', 'Refuse', '401 or 403', '401 means we do not know you. 403 means we know you, and this note is not yours.')),
        $g('cors', ['cors'], fn() => tf_panels('cors', 'Why the browser blocks a call curl allows', 'curl', ['No web page origin', 'Sends the request', 'Shows the body'], 'Browser', ['Page is on site A', 'API is on site B', 'Needs an allow header'], 'The browser adds an origin check. curl does not. The API must say which sites may read the response.')),
        $g('upload', ['upload', 'webhook', 'idempotency'], fn() => tf_flow('upload', 'A side effect needs a key', [['File or event', 'arrives'], ['Check', 'size and type'], ['Store once', 'same key']], 'The same webhook or upload can arrive twice. The key lets you keep the first and ignore the copy.')),
        $g('version', ['versioning', 'rate limit'], fn() => tf_bars('version', 'A limit is a count, not a feeling', [
            ['label' => 'Allowed', 'value' => 60, 'display' => '60 / min', 'color' => '#059669'],
            ['label' => 'This minute', 'value' => 60, 'display' => '60 used', 'color' => '#d97706'],
            ['label' => 'Next call', 'value' => 12, 'display' => '429 wait', 'color' => '#e11d48'],
        ], 'Version the path when the shape changes. Rate-limit the caller so one loop cannot take the server.')),
        $g('box', ['box model'], fn() => tf_layers('box', 'The box, from the outside in', [
            ['margin — space outside, not clickable', '#fef3c7', '#d97706'],
            ['border — the line', '#dbeafe', '#0284c7'],
            ['padding — space inside the border', '#d1fae5', '#059669'],
            ['content — the words', '#ffffff', '#64748b'],
        ], 'Margin pushes neighbours away. Padding pushes the words in from the border. They are not the same gap.')),
        $g('flex', ['flexbox', 'flex '], fn() => tf_cells('flex', 'Flex lays children in one row', [['1', 'A'], ['gap', 'B'], ['3', 'C']], 'Main axis is the row. Gap sits between items. A wrap sends the next item to the next row.', [1], 'main axis →')),
        $g('css-grid', ['css grid', 'grid masterclass', 'two-dimensional'], fn() => tf_grid('css-grid', 'Grid has rows and columns together', ['col 1', 'col 2'], ['row 1', 'row 2'], ['A', 'B', 'C', 'D'], 'Flex is one direction. Grid gives you both, so a cell can sit in row 2, column 1 on purpose.')),
        $g('position', ['positioning', 'z-index'], fn() => tf_panels('position', 'In the flow, or taken out of it', 'relative', ['Stays in its slot', 'Offset is extra', 'Neighbours keep the gap'], 'absolute', ['Looks for a positioned parent', 'Leaves the flow', 'The next item moves up'], 'Fixed stays on the screen while the page scrolls. z-index only matters among positioned boxes that overlap.')),
        $g('selector', ['selector'], fn() => tf_tree('selector', 'A selector walks the tree', ['div', 'p', 'span', 'a', '', '', ''], 'p is every paragraph inside. .note is a class, #id is one element. The more specific path wins when two rules touch the same box.')),
        $g('display', ['display property', 'inline-block'], fn() => tf_panels('display', 'Block takes a row, inline stays in the line', 'block', ['Starts on a new line', 'Width fills the parent', 'div, p, h1'], 'inline', ['Sits in the sentence', 'Width hugs the words', 'span, a'], 'none removes the box from the layout. Visibility hidden keeps the empty gap.')),
        $g('responsive', ['media quer', 'responsive web'], fn() => tf_panels('responsive', 'Same page, two widths', 'narrow', ['One column', 'Nav stacks', 'Picture shrinks'], 'wide', ['A side column', 'Nav in a row', 'Max width stops the line'], 'The query asks about the screen. The styles inside it replace only what you write again.')),
        $g('color', ['colors, hex', 'rgb', 'gradients'], fn() => tf_cells('color', 'Three ways to name one colour', [['hex', '#0284c7'], ['rgb', '2,132,199'], ['hsl', '199 98%'],], 'Hex, rgb, and hsl can be the same colour. A gradient is two colours with a direction between them.', [0])),
        $g('motion', ['transition', 'keyframe', 'timing function', 'usetransition', 'usedeferredvalue', 'concurrent rendering'], fn() => tf_line('motion', 'A transition is the in-between', ['0s', '0.1s', '0.2s', '0.3s'], [0, 40, 75, 100], 'The property starts at one value and ends at another. The curve is how fast the middle moves. A keyframe names more than two stops.')),
        $g('pseudo', ['pseudo'], fn() => tf_cells('pseudo', 'A pseudo picks a state or an extra box', [['link', 'a'], ['hover', 'a'], ['before', '+'], ['nth', '3']], ':hover is a state of a real element. ::before is an extra box you did not write in the HTML.', [1])),
        $g('shadow', ['shadow'], fn() => tf_layers('shadow', 'A shadow sits around the box', [
            ['soft shadow, offset down', '#e2e8f0', '#94a3b8'],
            ['the box itself', '#ffffff', '#0284c7'],
        ], 'x and y move the shadow. Blur spreads it. Spread grows it. A text shadow uses the same idea on the letters.')),
        $g('overflow', ['overflow', 'truncat'], fn() => tf_panels('overflow', 'The words are wider than the box', 'visible', ['Text spills out', 'The page grows', 'Nothing is cut'], 'hidden + ellipsis', ['The box stays', 'Extra letters are cut', '… shows there is more'], 'Scroll keeps the extra and adds a bar. Hidden throws the extra away visually. The text is still in the DOM.')),
        $g('glass', ['glass', 'backdrop', 'css filter'], fn() => tf_layers('glass', 'A filter changes the painted pixels', [
            ['backdrop behind the box', '#dbeafe', '#0284c7'],
            ['the glassy panel', '#f8fafc', '#64748b'],
        ], 'blur on the backdrop is why the panel looks like glass. A filter on the element itself blurs its own content, which is a different picture.')),
        $g('units', ['rem, em', 'css units', 'clamp'], fn() => tf_bars('units', 'The same 1em is not the same pixel', [
            ['label' => 'root 16px', 'value' => 16, 'display' => '1rem = 16', 'color' => '#0284c7'],
            ['label' => 'parent 20px', 'value' => 20, 'display' => '1em = 20', 'color' => '#d97706'],
            ['label' => 'half screen', 'value' => 32, 'display' => '50vw', 'color' => '#059669'],
        ], 'rem follows the root. em follows the parent. vw follows the screen. clamp sets a preferred size between a min and a max.')),
        $g('css-var', ['custom properties', 'dark mode'], fn() => tf_panels('css-var', 'One name, two themes', 'light', ['--ink: #0f172a', '--paper: #ffffff', 'color: var(--ink)'], 'dark', ['--ink: #f8fafc', '--paper: #0f172a', 'same var(), new value'], 'The rule uses the name. The theme changes the value. You do not hunt every color declaration.')),
        $g('type', ['typography', 'web font'], fn() => tf_cells('type', 'A line of text has a size and a line box', [['size', '18'], ['line', '28'], ['weight', '700']], 'Font-size is the letters. Line-height is the box around them. Weight is how dark the stroke is.', [1])),
        $g('skeleton', ['document skeleton', 'html5 introduction'], fn() => tf_tree('skeleton', 'A document is a tree', ['html', 'head', 'body', 'meta', 'title', 'h1', 'p'], 'head holds information about the page. body holds what the person sees. Every later tag sits inside one of these.')),
        $g('semantic', ['semantic'], fn() => tf_flow('semantic', 'A page with named regions', ['header', 'nav', 'main', 'footer'], 'div would draw the same boxes. header and main tell a reader and a search engine what the region is for.')),
        $g('html-table', ['html tables', '<table>'], fn() => tf_grid('html-table', 'A table is rows of cells', ['col A', 'col B'], ['row 1', 'row 2'], ['name', 'milk', 'price', '2'], 'thead is the labels. tbody is the data. colspan makes one cell cover two columns.')),
        $g('html-form', ['html form', 'form validation', 'dropdown', 'datalist'], fn() => tf_flow('html-form', 'A form collects named answers', [['label', 'what to type'], ['input', 'the value'], ['button', 'send']], 'The name on the input is the key that arrives on the server. The label is for the human.')),
        $g('link', ['hyperlink', 'html links', '<a>'], fn() => tf_flow('link', 'A link is an address you can open', [['this page', 'you are here'], ['href', 'the next URL'], ['that page', 'the browser goes']], 'The words the person reads and the address they travel to are different. The href is the address.')),
        $g('image', ['html images', 'picture element', '<img>', 'responsive image'], fn() => tf_flow('image', 'The file and the tag are different', [['photo.jpg', 'the file'], ['img', 'the tag'], ['screen', 'the painted box']], 'src points at the file. alt is the words if the file cannot be shown. Width and height reserve the box so the page does not jump.')),
        $g('html-list', ['html lists'], fn() => tf_pile('html-list', 'A list is items in order', ['1  milk', '2  tea', '3  bread'], 'ul is bullets, ol is numbers, and each li is one item. The number is the order, not a value you store.')),
        $g('heading', ['headings, paragraphs'], fn() => tf_tree('heading', 'Headings nest by rank, not by size', ['h1', 'h2', 'h2', 'p', 'p', 'p', ''], 'One h1 names the page. h2 names a section under it. A paragraph belongs under the heading it follows.')),
        $g('av', ['audio', 'video embedding'], fn() => tf_flow('av', 'A player is a box with a file', [['source', 'the file'], ['controls', 'play and pause'], ['page', 'the box']], 'The browser plays the file. controls draws the buttons. Without a source, the box is empty.')),
        $g('canvas', ['canvas'], fn() => tf_panels('canvas', 'Canvas is pixels you draw with code', 'the tag', ['A blank rectangle', 'No children inside', 'One element'], 'the script', ['fillRect paints', 'Each call draws', 'The picture is not HTML'], 'You do not put tags inside a canvas. You call drawing commands, and the pixels change.')),
        $g('iframe', ['iframe'], fn() => tf_layers('iframe', 'A page inside a page', [
            ['your page', '#f8fafc', '#64748b'],
            ['iframe rectangle', '#dbeafe', '#0284c7'],
            ['the other site', '#ffffff', '#0284c7'],
        ], 'The iframe is a window. The address inside it is a different page. Your CSS does not style that inner page.')),
        $g('dialog', ['dialog', 'accordion', 'details'], fn() => tf_split('dialog', 'Closed until the person asks', 'open?', 'Shown', 'dialog or details', 'Hidden', 'still in the HTML', 'The content exists in the file either way. Opening it only changes what is painted.')),
        $g('meta', ['meta tag', 'open graph'], fn() => tf_panels('meta', 'The page, and the card a share makes', 'browser tab', ['title in head', 'the page itself', 'people on your site'], 'share card', ['og:title', 'og:image', 'people on another site'], 'Open Graph tags are for the preview another site builds. They do not change the page body.')),
        $g('a11y', ['accessibility', 'aria', 'a11y'], fn() => tf_flow('a11y', 'What a name reader hears', [['button', 'the role'], ['Buy milk', 'the name'], ['pressed', 'the state']], 'If the picture has no text, the name has to be written on purpose. The role says what the thing is.')),
        $g('data-attr', ['data-*', 'data attribute'], fn() => tf_object('data-attr', 'data-* is a label, not a style', 'button', ['data-id="4"', 'data-urgent="yes"', 'visible text: Milk'], 'The page can look the same. The attribute is there so script can find the note id without hiding it in a class name.')),
        $g('join', ['join'], fn() => tf_panels('join', 'Two tables meet on a shared key', 'notes', ['id 4', 'title milk', 'user_id 7'], 'users', ['id 7', 'name Ana', 'the match'], 'INNER keeps only rows that match. LEFT keeps every note, and leaves the user side empty when there is no match.')),
        $g('group', ['group by', 'grouping', 'rollup'], fn() => tf_bars('group', 'GROUP BY turns many rows into one per key', [
            ['label' => 'tea rows', 'value' => 3, 'display' => '3 rows → 1', 'color' => '#0284c7'],
            ['label' => 'milk rows', 'value' => 1, 'display' => '1 row → 1', 'color' => '#059669'],
        ], 'WHERE filters rows before the groups. HAVING filters the groups after the count or sum.')),
        $g('window-fn', ['window function', 'row_number', 'partition', 'rank,'], fn() => tf_cells('window-fn', 'A window looks at neighbours and keeps every row', [['tea', '1'], ['tea', '2'], ['milk', '1']], 'GROUP BY would collapse tea into one row. ROW_NUMBER writes a number on each row and leaves the rows in place.', [0, 1], 'partition tea | milk')),
        $g('where', ['where clause', 'comparison operator'], fn() => tf_split('where', 'WHERE keeps some rows', 'price < 5?', 'Keep', 'the row stays', 'Drop', 'the row is out', 'The comparison runs once per row. AND means both sides must pass. OR means either side is enough.')),
        $g('null', ['null value', 'is null', 'three-valued', 'null is not', 'not an empty'], fn() => tf_panels('null', 'Empty and missing are different', 'empty string', ['""', 'A value is there', 'Length is 0'], 'null', ['no value', 'Unknown, not blank', 'IS NULL, not ='], 'A missing note is null. A note with a blank title is an empty string. They fail different checks.')),
        $g('txn', ['transaction', 'acid', 'commit', 'rollback'], fn() => tf_flow('txn', 'Several writes, one outcome', ['BEGIN', 'Write', 'Write', 'COMMIT'], 'Until COMMIT, the other readers still see the old rows. ROLLBACK throws the writes away as a group.')),
        $g('index', ['indexes', 'b-tree', 'explain ', 'explain(', 'indexing'], fn() => tf_tree('index', 'An index is a tree that skips rows', ['9', '4', '14', '2', '7', '12', ''], 'The table is still the rows. The index is a sorted side path so a lookup does not read every row. EXPLAIN tells you whether that path was used.')),
        $g('order', ['order by'], fn() => tf_panels('order', 'ORDER BY sorts the answer, not the table', 'stored', ['id 3 bread', 'id 1 milk', 'id 2 tea'], 'shown', ['bread', 'milk', 'tea'], 'The rows on disk stay where they are. The result is a sorted view. LIMIT then keeps only the first few of that view.')),
        $g('insert', ['insert into', 'insert '], fn() => tf_flow('insert', 'INSERT adds a row', [['columns', 'title, price'], ['values', 'milk, 2'], ['table', 'one new row']], 'The column list and the value list are in the same order. A missing required column is a refusal, not a blank guess.')),
        $g('update', ['update & delete', 'update and delete', 'soft delete'], fn() => tf_split('update', 'UPDATE changes, DELETE removes', 'WHERE id = 4', 'Update', 'title becomes tea', 'Delete', 'the row is gone', 'Without WHERE, every row is hit. A soft delete sets a flag and leaves the row so history still exists.')),
        $g('agg', ['aggregate', 'count, sum', 'count('], fn() => tf_bars('agg', 'Aggregates collapse a column', [
            ['label' => 'rows', 'value' => 4, 'display' => '4 notes', 'color' => '#94a3b8'],
            ['label' => 'SUM', 'value' => 10, 'display' => 'price 10', 'color' => '#059669'],
            ['label' => 'MAX', 'value' => 5, 'display' => 'price 5', 'color' => '#0284c7'],
        ], 'COUNT counts rows. SUM and AVG read the numbers. MAX is the largest value, not the largest row.')),
        $g('case', ['case expression', 'case '], fn() => tf_split('case', 'CASE picks one label per row', 'price > 3?', 'high', 'one result', 'low', 'the other', 'It is a value, not a branch that skips the rest of the query. The first true WHEN wins.')),
        $g('union', ['union all', 'union &', 'set operation'], fn() => tf_panels('union', 'UNION stacks two results', 'query A', ['milk', 'tea'], 'query B', ['tea', 'bread'], 'UNION removes the repeated tea. UNION ALL keeps both teas. The column counts must match.')),
        $g('subquery', ['subquer', 'exists'], fn() => tf_flow('subquery', 'The inner query runs for the outer row', [['outer row', 'a note'], ['inner', 'is there a user?'], ['keep?', 'yes or no']], 'EXISTS asks whether the inner query found anything. It does not need the inner columns in the result.')),
        $g('cte', ['common table', 'with clause', 'with recursive', 'ctes'], fn() => tf_flow('cte', 'WITH names a result you can use', [['WITH step', 'a named result'], ['main query', 'reads that name']], 'The name is not a saved table. It lives for this statement. Recursive WITH repeats the step until the new rows stop.')),
        $g('view', [' view', 'views'], fn() => tf_flow('view', 'A view is a saved SELECT', [['SELECT', 'the query'], ['view name', 'notes_open'], ['reader', 'selects the name']], 'The view does not copy the rows. Reading it runs the query. A check option can refuse a write the view itself could not see.')),
        $g('constraint', ['constraint', 'primary key', 'foreign key', 'unique'], fn() => tf_split('constraint', 'A constraint refuses a bad row', 'unique title?', 'Accept', 'first milk', 'Refuse', 'second milk', 'A primary key identifies the row. A foreign key says the user id must exist. A check says the price is not negative.')),
        $g('ddl', ['create, alter', 'ddl', 'truncate'], fn() => tf_flow('ddl', 'DDL changes the shape, not one note', ['CREATE', 'ALTER', 'DROP'], 'CREATE makes a table. ALTER adds a column. DROP removes the table. TRUNCATE empties the rows and keeps the shape.')),
        $g('like', ['like &', 'wildcard', 'regexp', 'regular expression'], fn() => tf_cells('like', 'A pattern matches part of the text', [['milk', 'yes'], ['mil', 'no'], ['m%', 'yes'], ['%lk', 'yes']], '% means any tail. _ means one character. A regular expression is a richer pattern with the same job: yes or no per string.', [0, 2, 3])),
        $g('bigo', ['big-o', 'cost of a step'], fn() => tf_bars('bigo', 'Steps for 8 items', [
            ['label' => 'constant', 'value' => 1, 'display' => '1', 'color' => '#059669'],
            ['label' => 'linear', 'value' => 8, 'display' => '8', 'color' => '#0284c7'],
            ['label' => 'n log n', 'value' => 24, 'display' => '24', 'color' => '#d97706'],
            ['label' => 'n squared', 'value' => 64, 'display' => '64', 'color' => '#e11d48'],
        ], 'The picture is the shape as the input grows. Constant stays flat. Squared explodes. The exact constant factors are a later argument.')),
        $g('front', ['front of a list'], fn() => tf_bars('front', 'Inserting at the front moves everyone', [
            ['label' => 'insert at end', 'value' => 1, 'display' => '1 write', 'color' => '#059669'],
            ['label' => 'insert at front', 'value' => 8, 'display' => '8 move', 'color' => '#e11d48'],
        ], 'A Python list keeps items in a row. Putting something at index 0 slides every later item one slot to the right.')),
        $g('doubles', ['doubles when'], fn() => tf_bars('doubles', 'Capacity jumps, then there is room', [
            ['label' => 'full', 'value' => 4, 'display' => '4 used', 'color' => '#e11d48'],
            ['label' => 'after grow', 'value' => 8, 'display' => '8 slots', 'color' => '#059669'],
        ], 'One append is usually cheap. The append that finds the row full pays for a bigger row, then the next ones are cheap again.')),
        $g('stack', ['stack:', 'undo pile', 'stack slots', 'a line and a pile'], fn() => tf_pile('stack', 'Last in, first out', ['coat  ← top', 'shirt', 'socks'], 'Push puts a new item on top. Pop takes that same item. The bottom one waits until everything above it is gone.')),
        $g('brackets', ['bracket', 'opener'], fn() => tf_cells('brackets', 'Each opener waits for its closer', [['(', '('], ['[', '['], [']', ']'], [')', ')']], 'The latest opener must close first. That is why a stack fits: the top of the pile is the opener you just saw.', [0, 3])),
        $g('greater', ['next greater'], fn() => tf_cells('greater', 'The next greater item to the right', [['2', '2'], ['1', '1'], ['2', '2'], ['4', '4']], 'For 2 the answer is 4, because 1 and the next 2 are not greater. A stack remembers the values still waiting for a greater neighbour.', [0, 3], 'waiting →')),
        $g('queue', ['queue', 'deque', 'fair line'], fn() => tf_cells('queue', 'First in, first out', [['front', 'socks'], ['2', 'shirt'], ['back', 'coat']], 'People join at the back and leave from the front. A deque can also add or remove at the front, which a plain list makes expensive.', [0], 'leave ←    join →')),
        $g('linked', ['linked list', 'boxes with arrows', 'point backward'], fn() => tf_chain('linked', 'Each box holds a value and the next address', ['milk', 'tea', 'bread'], 'The row is not sitting in neighbouring memory. To reach bread you follow the arrows from milk. Reversing the list turns those arrows around.')),
        $g('floyd', ['slow step', 'fast step'], fn() => tf_cells('floyd', 'The fast pointer takes two steps', [['s', 'A'], ['', 'B'], ['f', 'C'], ['', 'D']], 'Slow moves one box, fast moves two. If there is a loop, fast laps slow. If there is an end, fast finds it first.', [0, 2])),
        $g('window', ['sliding window', 'two pointers'], fn() => tf_cells('window', 'The window is a stretch, not the whole row', [['0', 'a'], ['1', 'b'], ['2', 'c'], ['3', 'd'], ['4', 'e']], 'Left and right mark the stretch you care about. Moving right grows it. Moving left shrinks it. You do not restart from zero.', [1, 2, 3], 'left at b, right at d')),
        $g('prefix', ['range total', 'one subtraction'], fn() => tf_panels('prefix', 'A prefix turns a range into a subtraction', 'values', ['2, 1, 3, 4', 'sum of [1..2]', '1 + 3 = 4'], 'prefixes', ['0, 2, 3, 6, 10', 'prefix[3] − prefix[1]', '6 − 2 = 4'], 'Build the running total once. Any later range is the later total minus the total before the start.')),
        $g('twosum', ['two sum', 'partner you still need'], fn() => tf_cells('twosum', 'Remember the partner that would finish the sum', [['2', '2'], ['7', '7'], ['11', '11'], ['15', '15']], 'Target 9. When you see 7, you ask whether 2 is already in the map. It is, so the pair is the first and second slots.', [0, 1])),
        $g('set', [' set:', 'membership in one step', 'keeps each value once', 'weakset', 'python sets'], fn() => tf_cells('set', 'A set keeps the first copy only', [['keep', 'milk'], ['drop', 'milk'], ['keep', 'tea']], 'Asking “is tea here?” is one step. The second milk does not get a second slot. Order is not the promise.', [0, 2])),
        $g('hash', ['hashing', 'what collides', 'dictionary', 'hash map'], fn() => tf_flow('hash', 'A key becomes a slot', [['key', 'milk'], ['hash', 'a number'], ['slot', 'the value']], 'Two different keys can land in the same slot. That is a collision. The dictionary still returns the value that belongs to the key you asked for.')),
        $g('bits', ['mask of bits', 'bitwise', 'side by side', 'and, or'], fn() => tf_cells('bits', 'Flags packed in one integer', [['1', 'ready'], ['2', 'hot'], ['4', 'off'], ['&', 'test']], 'Each flag is a bit. OR turns a bit on. AND with that bit answers whether it is on. A shift moves every bit one place.', [0, 1])),
        $g('gcd', ['divisor', 'small primes', 'sieve'], fn() => tf_flow('gcd', 'The shared divisor shrinks the pair', [['48, 18', 'the pair'], ['18, 12', 'remainder'], ['6, 0', 'the divisor']], 'Replace the larger by the remainder. When the remainder is 0, the other number divides both originals. The sieve then crosses out multiples.')),
        $g('recursion', ['recursion', 'base case'], fn() => tf_flow('recursion', 'The same rule, on a smaller input', [['fib(4)', 'calls'], ['fib(3)', 'smaller'], ['fib(1)', 'stops']], 'Every call must get smaller, and one size must return without calling again. That stopping size is the base case.')),
        $g('linear', ['linear search', 'stopping early'], fn() => tf_cells('linear', 'Look at each slot until the value shows up', [['0', '4'], ['1', '1'], ['2', '9'], ['3', '2']], '9 is at index 2. The search stops there. It does not promise the row is sorted, so it cannot jump to the middle.', [2])),
        $g('strstr', ['sliding it', 'search for a word'], fn() => tf_cells('strstr', 'Slide the word along the text', [['m', 'm'], ['i', 'i'], ['l', 'l'], ['k', 'k'], ['y', 'y']], 'At each start, compare the word one character at a time. A mismatch moves the start forward by one.', [0, 1, 2, 3], 'does milk start here?')),
        $g('bs-answer', ['size of the answer'], fn() => tf_line('bs-answer', 'Guess the size, then test it', ['too small', 'try', 'fits', 'too big'], [1, 2, 4, 8], 'You are not searching a list of items. You are searching the possible answers. If this size works, try a smaller one. If it fails, try a larger one.', '#0284c7')),
        $g('binsearch', ['binary search', 'middle that must move'], fn() => tf_cells('binsearch', 'The middle has to move', [['lo', '2'], ['', '5'], ['mid', '9'], ['', '12'], ['hi', '20']], '9 is the middle. If the target is greater, the new low is just to the right of mid. Leaving mid in the range repeats the same guess.', [2], 'lo ········· hi')),
        $g('perm', ['order matters, or it does not', 'permutation'], fn() => tf_panels('perm', 'Order is part of the answer, or it is not', 'order matters', ['milk then tea', 'tea then milk', 'two different results'], 'order does not', ['{milk, tea}', 'same pair', 'one result'], 'A permutation counts arrangements. A combination counts groups. The same two items are one combination and two permutations.')),
        $g('bubble', ['bubble'], fn() => tf_cells('bubble', 'Neighbours swap until the largest reaches the end', [['', '1'], ['', '3'], ['', '2'], ['done', '4']], '4 bubbled to the end by swapping with each neighbour. The next pass can ignore that last slot.', [3], '[4, 1, 3, 2] after one pass')),
        $g('selection', ['selection sort', 'smallest remaining'], fn() => tf_cells('selection', 'The smallest remaining item moves to the front', [['now', '1'], ['', '4'], ['', '3'], ['', '2']], 'Scan the unsorted tail, then swap once into the next open slot. Selection swaps less often than bubble and still takes the same number of looks.', [0])),
        $g('insertion', ['insertion sort', 'hand of cards'], fn() => tf_cells('insertion', 'The new card slides left until it fits', [['ok', '1'], ['ok', '3'], ['new', '2'], ['', '4']], '1 and 3 are already a sorted hand. 2 walks left past 3 and stops after 1.', [2])),
        $g('mergesort', ['merge sort', 'split, sort, stitch'], fn() => tf_panels('mergesort', 'Two sorted halves become one sorted row', 'halves', ['1  4', '2  3', 'already sorted'], 'stitched', ['1', '2', '3  4'], 'Take the smaller head of the two halves, again and again. The work is the stitching, not the split.')),
        $g('quick', ['quick sort', 'already-sorted trap'], fn() => tf_cells('quick', 'The pivot ends in its final place', [['<', '1'], ['<', '2'], ['pivot', '3'], ['>', '4']], 'Everything left of the pivot is smaller, everything right is larger. If the pivot is always the first item of a sorted row, one side stays empty.', [2])),
        $g('stable', ['equal keys', 'order they arrived'], fn() => tf_panels('stable', 'Equal keys can keep their arrival order', 'stable', ['milk A, then milk B', 'A stays before B', 'bubble, merge'], 'not stable', ['milk B can pass A', 'same key, new order', 'selection'], 'Stability matters when the key is only part of the item, and the earlier order still means something.')),
        $g('heap', ['heap:', 'most important item'], fn() => tf_tree('heap', 'The parent is the more important one', ['1', '4', '3', '8', '5', '7', ''], 'The smallest (or largest) sits at the root. The left and right children are not sorted against each other. Taking the root costs a walk down, not a scan of the whole row.')),
        $g('counting', ['counting sort'], fn() => tf_bars('counting', 'Count each small number, then write them back', [
            ['label' => 'value 1', 'value' => 2, 'display' => 'two 1s', 'color' => '#0284c7'],
            ['label' => 'value 2', 'value' => 1, 'display' => 'one 2', 'color' => '#059669'],
            ['label' => 'value 4', 'value' => 3, 'display' => 'three 4s', 'color' => '#d97706'],
        ], 'This only fits when the numbers are small. You count, then you emit 1, 1, 2, 4, 4, 4. No comparisons.')),
        $g('stick', ['stick-shaped'], fn() => tf_tree('stick', 'A tree that is really a line', ['1', '2', '', '3', '', '', ''], 'Every node has only a right child, or only a left child. The height is the number of nodes, so the “fast” tree walks are a slow scan.')),
        $g('trie', ['trie:'], fn() => tf_tree('trie', 'Each step is one letter', ['c', 'a', 'o', 't', 'r', 'w', ''], 'cat and car share the c and the a. The word ends at a mark, not merely at a node, because car is a prefix of card.')),
        $g('bst', ['search tree', 'left is smaller'], fn() => tf_tree('bst', 'Left is smaller, right is larger', ['8', '3', '10', '1', '6', '', '14'], 'Finding 6 starts at 8, goes left to 3, then right. A stick-shaped tree still follows the rule and still takes a long walk.')),
        $g('treewalk', ['tree walk', 'binary tree'], fn() => tf_tree('treewalk', 'The same tree, three reading orders', ['8', '3', '10', '1', '6', '', '14'], 'In-order reads left, node, right: 1, 3, 6, 8, 10, 14. Pre-order reads the node before its children. Post-order reads it after.')),
        $g('flood', ['flood'], fn() => tf_grid('flood', 'Paint spreads to the touching cells', ['c1', 'c2', 'c3'], ['r1', 'r2', 'r3'], ['paint', 'paint', 'wall', 'paint', 'wall', 'wall', 'paint', 'paint', 'wall'], 'Start at a cell and visit the neighbours that share its colour. A wall stops that direction. The visited set stops you painting the same cell twice.', [0, 1, 3, 6, 7])),
        $g('bipartite', ['two colours', 'two colors', 'graph that refuses'], fn() => tf_panels('bipartite', 'Neighbours must be different colours', 'works', ['A blue — B gold', 'B gold — C blue', 'no edge inside a colour'], 'refuses', ['A — B — C — A', 'odd cycle', 'the third edge clashes'], 'Try to 2-colour the graph. The moment a neighbour already has your colour, the graph is not bipartite.')),
        $g('dijkstra', ['dijkstra', 'cheapest path', 'prices are positive'], fn() => tf_graph('dijkstra', 'Always expand the cheapest unsettled node', [['A', 80, 100], ['B', 250, 40], ['C', 250, 160], ['D', 460, 100]], [['A', 'B', '1'], ['A', 'C', '4'], ['B', 'D', '2'], ['C', 'D', '1']], 'A to D via B costs 3, via C costs 5. Negative prices break the rule, because a later edge could make an old path cheaper.')),
        $g('negative', ['negative price', 'negative edge'], fn() => tf_graph('negative', 'A negative edge can improve an old path', [['A', 120, 100], ['B', 340, 50], ['C', 340, 150]], [['A', 'B', '2'], ['B', 'C', '-5'], ['A', 'C', '1']], 'A to C looked like 1. Going through B costs 2 + (−5) = −3, which is cheaper. One pass is not enough once prices can be negative.')),
        $g('topo', ['topological'], fn() => tf_flow('topo', 'A task waits for the tasks it needs', ['socks', 'shirt', 'shoes', 'coat'], 'Shoes wait for socks. Coat waits for shirt. An arrow from A to B means A must finish first. A cycle means nobody can start.')),
        $g('union', ['union-find', 'same group'], fn() => tf_panels('union', 'Two people, one group or two', 'before', ['Ana — Ben', 'Cia alone', 'two groups'], 'after union', ['Ana — Ben — Cia', 'one parent', 'same group'], 'Find walks to the parent. Union makes one parent point at the other. Asking “same group?” is “same parent?”')),
        $g('kruskal', ['cheap edges', 'do not close a loop'], fn() => tf_graph('kruskal', 'Take the cheapest edge that joins two groups', [['A', 80, 100], ['B', 250, 40], ['C', 430, 40], ['D', 250, 160]], [['A', 'B', '1'], ['B', 'C', '2'], ['A', 'D', '4'], ['B', 'D', '9']], 'Sort edges by price. Add one only when its ends are in different groups. The third edge that would close a triangle is skipped.')),
        $g('greedy', ['greedy', 'local choice'], fn() => tf_flow('greedy', 'The local choice you can prove', [['now', 'the best step'], ['proof', 'it never regrets'], ['done', 'the answer']], 'Greedy is not “pick what looks nice”. It is a choice you can show will still be part of a best answer.')),
        $g('meetings', ['meeting', 'overlap become'], fn() => tf_bars('meetings', 'Overlapping meetings become one block', [
            ['label' => '9–10', 'value' => 2, 'display' => 'meet', 'color' => '#0284c7'],
            ['label' => '9:30–11', 'value' => 3, 'display' => 'overlaps', 'color' => '#0284c7'],
            ['label' => 'merged', 'value' => 4, 'display' => '9–11', 'color' => '#059669'],
            ['label' => '13–14', 'value' => 2, 'display' => 'separate', 'color' => '#d97706'],
        ], 'Sort by start time. If the next meeting starts before the current block ends, extend the block. A later start opens a new block.')),
        $g('memo', ['remember the wish', 'fill the table', 'write the answer down'], fn() => tf_panels('memo', 'Same small question, two ways to remember', 'memo', ['Ask fib(4)', 'Save the answer', 'Reuse it later'], 'table', ['Start at fib(1)', 'Fill the next cell', 'Read the end'], 'Both refuse to recompute a size you already know. Memo fills on demand. The table fills from the small end on purpose.')),
        $g('kadane', ['contiguous stretch', 'best contiguous'], fn() => tf_cells('kadane', 'The best stretch can start over', [['', '-2'], ['', '1'], ['', '-3'], ['yes', '4'], ['yes', '-1'], ['yes', '2'], ['yes', '1']], 'A running sum that goes negative is abandoned. The stretch 4, −1, 2, 1 sums to 6, which beats every shorter piece inside it.', [3, 4, 5, 6])),
        $g('knapsack', ['knapsack'], fn() => tf_grid('knapsack', 'Each item is take or skip, for this capacity', ['cap 1', 'cap 2', 'cap 3'], ['item A', 'item B'], ['0', 'A', 'A', '0', 'A', 'A+B'], 'The cell is the best value using the first few items and a weight limit. It reads the cell above (skip) and the cell one weight to the left (take).', [5])),
        $g('coins', ['coin'], fn() => tf_cells('coins', 'One amount at a time', [['0', '0'], ['1', '1'], ['2', '2'], ['3', '1'], ['4', '2']], 'Coins 1 and 3. Amount 3 takes a single 3, so the cell is 1. Amount 4 is the 3 plus one more coin.', [3], 'fewest coins')),
        $g('backtrack', ['backtracking', 'try, then undo'], fn() => tf_tree('backtrack', 'Try a choice, then take it back', ['', 'A', 'B', 'Aa', 'Ab', '', ''], 'You place A, explore, then remove A before you place B. The undo is what makes the next branch honest.')),
        $g('campus', ['campus route', 'route finder'], fn() => tf_graph('campus', 'A campus is nodes and priced roads', [['gate', 80, 110], ['lab', 250, 40], ['cafe', 250, 170], ['hall', 460, 110]], [['gate', 'lab', '2'], ['gate', 'cafe', '5'], ['lab', 'hall', '3'], ['cafe', 'hall', '1']], 'The finder keeps the cheapest known cost to each place and refuses to walk a road twice in the same path.')),
        $g('graph', ['graph', 'breadth-first', 'depth-first', 'nearest first', 'visited set'], fn() => tf_graph('graph', 'Near edges first, or one path to the end', [['S', 80, 100], ['A', 230, 40], ['B', 230, 160], ['C', 420, 100]], [['S', 'A', ''], ['S', 'B', ''], ['A', 'C', ''], ['B', 'C', '']], 'Breadth-first visits every neighbour of S before it walks on to C. Depth-first follows one road until it stops, then comes back. Both need a visited set or they loop.')),
        $g('baseline', ['boring guess', 'baseline'], fn() => tf_bars('baseline', 'The boring guess is the score to beat', [
            ['label' => 'always no', 'value' => 8, 'display' => '8 of 10', 'color' => '#94a3b8'],
            ['label' => 'noisy model', 'value' => 7, 'display' => '7 of 10', 'color' => '#e11d48'],
        ], 'If most notes are not urgent, always saying “no” is right most of the time. A model that scores worse than that has not earned its complexity.')),
        $g('scale-leak', ['training rows have seen', 'scale only'], fn() => tf_panels('scale-leak', 'Scale from the training rows only', 'train', ['mean and spread', 'fit here', 'transform these rows'], 'test', ['use that same mean', 'do not recompute', 'transform with it'], 'If the test rows help you compute the mean, a fact from the exam leaked into the ruler.')),
        $g('third', ['third pile', 'do not touch'], fn() => tf_cells('third', 'Three piles, and one of them stays closed', [['train', '60'], ['check', '20'], ['locked', '20']], 'Train fits the rule. The check pile tells you when to stop. The locked pile is opened once, at the end, so it can still surprise you.', [2])),
        $g('lr', ['too large', 'jumps past', 'learning rate'], fn() => tf_line('lr', 'A step that is too large jumps past the valley', ['start', '1', '2', '3', '4'], [8, 2, 6, 1.5, 4], 'The loss should fall. A huge step leaps over the bottom and climbs the other side. A smaller step is slower and actually arrives.', '#e11d48')),
        $g('slopes', ['two slopes', 'two inputs'], fn() => tf_object('slopes', 'Two inputs, two slopes, one intercept', 'price', ['0.4 × pages', '1.1 × urgent', '+ 0.2 intercept'], 'Each input gets its own weight. Changing pages does not have to change the urgent weight. The prediction is the weighted sum plus the intercept.')),
        $g('onehot', ['category is not', 'bigger number', 'one-hot', 'one hot'], fn() => tf_panels('onehot', 'A city is not a bigger number', 'wrong', ['Delhi = 1', 'Mumbai = 2', 'Mumbai “greater”'], 'one column each', ['Delhi 1 0', 'Mumbai 0 1', 'no order invented'], 'Numbers imply order and distance. A category has neither, so it becomes a column that is on or off.')),
        $g('cutoff', ['cutoff', 'move the cutoff'], fn() => tf_split('cutoff', 'The same scores, a different cutoff', 'score ≥ cutoff?', 'Call urgent', 'more recalls, more false alarms', 'Call calm', 'fewer false alarms, more misses', 'Moving the cutoff does not retrain the model. It only changes which probability you treat as a yes.')),
        $g('folds', ['which fold', 'k-fold', 'rotate'], fn() => tf_cells('folds', 'Each fold takes a turn as the test', [['test', 'A'], ['train', 'B'], ['train', 'C'], ['train', 'D']], 'Next round, B is the test and A joins the training. The score is the average of the rounds, not the luck of one split.', [0])),
        $g('tree2', ['second question after'], fn() => tf_tree('tree2', 'The second question is only for one branch', ['pages > 2?', 'yes: urgent?', 'no: calm', '', '', '', ''], 'The first question splits the rows. The second question is asked only on the side that still mixes both labels.')),
        $g('vote', ['can vote', 'weak questions'], fn() => tf_flow('vote', 'Three weak votes, one decision', [['stump A', 'yes'], ['stump B', 'yes'], ['stump C', 'no'], ['majority', 'yes']], 'Each stump is only a little better than a guess. The majority can be right when no single stump is sure.')),
        $g('bayes', ['words in each class', 'count the words'], fn() => tf_bars('bayes', 'How often the word showed up in each class', [
            ['label' => 'urgent: down', 'value' => 5, 'display' => '5', 'color' => '#e11d48'],
            ['label' => 'calm: down', 'value' => 1, 'display' => '1', 'color' => '#059669'],
        ], '“down” is more common in urgent notes. Add one to every count so a word you have never seen does not wipe the whole score to zero.')),
        $g('leak', ['leaked', 'leakage'], fn() => tf_panels('leak', 'The label hid inside a feature', 'honest', ['pages: 4', 'hour: 9', 'label stays out'], 'leaked', ['pages: 4', 'is_urgent: 1', 'the answer is a column'], 'If a column was built from the label, the model is copying, not learning. Drop that column before the split.')),
        $g('imbalance', ['hides inside accuracy', 'rare urgent'], fn() => tf_bars('imbalance', 'Accuracy can look kind while missing the rare class', [
            ['label' => 'calm notes', 'value' => 95, 'display' => '95', 'color' => '#94a3b8'],
            ['label' => 'urgent notes', 'value' => 5, 'display' => '5', 'color' => '#e11d48'],
        ], 'Always saying calm scores 95%. It also never finds an urgent note. Recall on the rare class is the number that tells on it.')),
        $g('poly', ['square lets', 'straight fit bend'], fn() => tf_line('poly', 'A square column lets a straight fit bend', ['x', 'x² grows'], [1, 1, 2, 4, 8], 'The model is still a weighted sum. The new column is x squared, so the sum can fall and then rise.', '#7c3aed')),
        $g('elbow', ['how many clusters', 'leftover distance'], fn() => tf_line('elbow', 'More clusters, less leftover distance', ['k=1', 'k=2', 'k=3', 'k=4', 'k=5'], [10, 4, 3.2, 3, 2.9], 'The sharp bend is the useful k. After that, extra clusters only shave a little distance and start splitting one real group.', '#0284c7')),
        $g('accuracy', ['accuracy can lie'], fn() => tf_bars('accuracy', '90% can still be the wrong score to read', [
            ['label' => 'correct calm', 'value' => 90, 'display' => '90', 'color' => '#059669'],
            ['label' => 'missed urgent', 'value' => 10, 'display' => '10 misses', 'color' => '#e11d48'],
        ], 'Accuracy counts every correct cell together. If the miss you care about is rare, that count hides it.')),
        $g('prf', ['precision', 'recall', 'four boxes'], fn() => tf_grid('prf', 'The four boxes', ['predicted yes', 'predicted no'], ['actually yes', 'actually no'], ['hit', 'miss', 'false alarm', 'true calm'], 'Precision asks, of the yes calls, how many were real. Recall asks, of the real yes notes, how many you caught.', [0, 1])),
        $g('stump', ['stump', 'one question'], fn() => tf_split('stump', 'One question, two leaves', 'pages > 3?', 'urgent', 'the yes leaf', 'calm', 'the no leaf', 'A stump does not ask a second question. Every row on a side shares one prediction.')),
        $g('kmeans', ['k-means', 'groups with no labels', 'no labels'], fn() => tf_dots('kmeans', 'Two groups, no labels given', [[120, 70, '#0284c7', ''], [150, 90, '#0284c7', ''], [180, 60, '#0284c7', 'center'], [460, 120, '#d97706', ''], [500, 140, '#d97706', ''], [430, 130, '#d97706', 'center']], 'Each point joins the nearest center. The center then moves to the middle of its points. Repeat until the memberships stop changing.')),
        $g('penalty', ['penalty', 'huge weights'], fn() => tf_bars('penalty', 'A huge weight pays a penalty', [
            ['label' => 'small weight', 'value' => 2, 'display' => 'penalty 2', 'color' => '#059669'],
            ['label' => 'huge weight', 'value' => 9, 'display' => 'penalty 9', 'color' => '#e11d48'],
        ], 'The fit on the training rows can get a little worse. The penalty stops one column from dominating just because it memorised those rows.')),
        $g('overfit', ['overfit', 'underfit'], fn() => tf_panels('overfit', 'Too stiff, or a line that memorised the noise', 'underfit', ['one flat guess', 'misses the bend', 'train and test both bad'], 'overfit', ['hits every train dot', 'wild between them', 'test falls apart'], 'A useful fit is wrong a little on the training rows and similarly wrong on rows it did not see.')),
        $g('logistic', ['logistic', 'probability between'], fn() => tf_line('logistic', 'A probability stays between 0 and 1', ['low', '', 'mid', '', 'high'], [0.05, 0.2, 0.5, 0.8, 0.95], 'The weighted sum can be any number. The squash squeezes it into a probability. 0.9 means “likely”, not “the label is the number 0.9”.')),
        $g('loss', ['the loss', 'the error, the'], fn() => tf_bars('loss', 'Each miss adds to the loss', [
            ['label' => 'row A miss', 'value' => 1, 'display' => '1', 'color' => '#0284c7'],
            ['label' => 'row B miss', 'value' => 4, 'display' => '4', 'color' => '#d97706'],
            ['label' => 'average', 'value' => 2.5, 'display' => '2.5', 'color' => '#059669'],
        ], 'One row’s error is the gap. The loss is those gaps combined, usually the average of the squares, so a big miss counts more.')),
        $g('gd', ['gradient'], fn() => tf_line('gd', 'One step walks downhill', ['start', 'step 1', 'step 2', 'step 3'], [8, 5, 3.2, 2.4], 'The slope says which way is down. The step size says how far you move. You recompute the slope where you land.')),
        $g('linefit', ['straight line', 'slope, and intercept', 'regression'], fn() => tf_line('linefit', 'A line is a slope and a height', ['0', '1', '2', '3', '4'], [1, 3, 5, 7, 9], 'At x = 0 the line is the intercept, here 1. Each extra x adds the slope, here 2. The prediction is intercept + slope × x.')),
        $g('train', ['train and test', 'memorising'], fn() => tf_cells('train', 'Learn on one pile, score on the other', [['learn', '1'], ['learn', '2'], ['learn', '3'], ['score', '4'], ['score', '5']], 'The training rows may be memorised. The test rows were not used to choose the rule, so a good score there means the rule travelled.', [3, 4])),
        $g('neighbour', ['nearest neighbour', 'nearest'], fn() => tf_dots('neighbour', 'The closest training point votes', [[160, 80, '#0284c7', 'train'], [220, 110, '#0284c7', 'train'], [300, 70, '#059669', 'new'], [480, 130, '#d97706', 'train']], 'Measure the distance to each stored row. The nearest neighbour’s label is the guess. Scaling matters, or a huge column owns the distance.')),
        $g('scaling', ['mean, spread', 'scaling'], fn() => tf_bars('scaling', 'A big column drowns a small one', [
            ['label' => 'pages', 'value' => 12, 'display' => '0–12', 'color' => '#0284c7'],
            ['label' => 'price', 'value' => 80, 'display' => '0–400', 'color' => '#e11d48'],
        ], 'Subtract the mean and divide by the spread, and both columns sit on a similar scale. Do that with the training mean only.')),
        $g('next-token', ['counted most', 'next token'], fn() => tf_bars('next-token', 'After “tea”, which word was counted most', [
            ['label' => 'milk', 'value' => 3, 'display' => '3', 'color' => '#059669'],
            ['label' => 'sugar', 'value' => 1, 'display' => '1', 'color' => '#94a3b8'],
        ], 'The prediction is the follower you saw most often. A tie needs a rule you chose, not whatever the dictionary returns first.')),
        $g('bag', ['bag of words', 'forgets the order'], fn() => tf_panels('bag', 'The bag keeps counts and drops order', 'sentence', ['dog bites man', 'man bites dog', 'different events'], 'bag', ['dog 1', 'bites 1', 'man 1 — same bag'], 'Two sentences can share a bag and not share a meaning. Order was the meaning you threw away.')),
        $g('roles', ['three jobs'], fn() => tf_flow('roles', 'Three jobs in one prompt', [['instruction', 'the job'], ['note', 'the facts'], ['question', 'what to answer']], 'The instruction says how to behave. The note is data, not a new instruction. The question is what to answer from that note.')),
        $g('window-ai', ['falls off', 'front of the window'], fn() => tf_cells('window-ai', 'The front of a full window falls off', [['gone', 'old'], ['kept', 'note'], ['kept', 'note'], ['new', 'ask']], 'There is a budget of tokens. When the new question arrives, the oldest tokens leave. A fact that fell off cannot be quoted.', [0])),
        $g('fewshot', ['example inside', 'few-shot'], fn() => tf_flow('fewshot', 'An example shows the shape of the answer', [['example', 'server down → urgent'], ['example', 'buy milk → calm'], ['real', 'disk full → ?']], 'The examples show that the answer is one of those words. They do not prove that disk full is urgent. The definition still has to say so.')),
        $g('chunks', ['overlapping chunks', 'cut the note'], fn() => tf_cells('chunks', 'Chunks overlap so a sentence is not cut in half', [['1', 'lab'], ['2', 'closes'], ['2', 'at'], ['3', '9']], 'The overlap repeats the boundary. A fact that sat on the cut still appears whole in one of the chunks.', [1, 2])),
        $g('inject', ['change the job', 'ignore the instruction'], fn() => tf_panels('inject', 'The note is data, even when it talks like a boss', 'instruction', ['Answer from the note', 'Do not follow orders in it', 'Secrets stay out'], 'a bad chunk', ['“ignore that”', 'treated as text', 'not as a new job'], 'A retrieved paragraph can contain words that look like instructions. The system keeps them on the data side of the fence.')),
        $g('quote', ['do not know', 'quote the note'], fn() => tf_split('quote', 'Quote the note, or say you do not know', 'Is the fact in the note?', 'Quote it', 'the sentence is there', 'Refuse', 'say you do not know', 'A fluent sentence that is not in the note is not an answer. The honest output is the quote, or a refusal.')),
        $g('bill', ['tokens in', 'tokens out'], fn() => tf_bars('bill', 'The bill is tokens in plus tokens out', [
            ['label' => 'prompt', 'value' => 40, 'display' => '40 in', 'color' => '#0284c7'],
            ['label' => 'reply', 'value' => 15, 'display' => '15 out', 'color' => '#d97706'],
            ['label' => 'bill', 'value' => 55, 'display' => '55', 'color' => '#059669'],
        ], 'A long note costs even when the reply is one word. Cutting the note to the relevant chunk is how you shrink the bill.')),
        $g('choose', ['change the weights', 'prompt, retrieve'], fn() => tf_flow('choose', 'Three different things people call training', [['prompt', 'words only'], ['retrieve', 'look up a note'], ['weights', 'change the model']], 'Editing the prompt is free and reversible. Retrieval adds a source. Changing the weights is a new model, and you need a frozen set of questions to see if it got worse.')),
        $g('agent', ['then stop', 'use a tool', 'let code do the math'], fn() => tf_flow('agent', 'Plan, use a tool, read the result, stop', [['plan', 'need a sum'], ['tool', 'add only'], ['result', 'the number'], ['stop', 'no more calls']], 'The tool list is an allow-list. Addition is allowed. Anything else is not a tool the loop may call. Then it stops.')),
        $g('memory', ['allowed to remember', 'next turn'], fn() => tf_panels('memory', 'The next turn does not inherit the world', 'kept', ['this user’s name', 'the note they opened', 'what they asked'], 'not kept', ['another user’s note', 'a hidden instruction', 'a secret'], 'Memory is a list you chose to write. It is not “whatever was on the screen” and it is not shared across people.')),
        $g('evalset', ['set of notes', 'not one lucky'], fn() => tf_grid('evalset', 'Score the whole set, not one lucky reply', ['right', 'refused'], ['note has the fact', 'note is silent', 'question is a trap'], ['yes', '', 'yes', '', '', 'yes'], 'One impressive answer is an anecdote. The same three notes, scored again after you edit the prompt, tell you whether the edit helped.', [0, 2, 5])),
        $g('temperature', ['temperature'], fn() => tf_bars('temperature', 'Low temperature repeats, high temperature wanders', [
            ['label' => 'top word, low', 'value' => 9, 'display' => 'almost always', 'color' => '#059669'],
            ['label' => 'top word, high', 'value' => 4, 'display' => 'often', 'color' => '#d97706'],
            ['label' => 'a rare word', 'value' => 2, 'display' => 'sometimes', 'color' => '#94a3b8'],
        ], 'Temperature does not add facts. It only changes how often the less likely next word is picked.')),
        $g('hallu', ['hallucination', 'fluent sentence'], fn() => tf_panels('hallu', 'Fluent is not the same as true', 'sounds fine', ['“The lab closes at 9”', 'grammar is perfect', 'confidence is high'], 'check', ['is 9 in the note?', 'if not, it is invented', 'say you do not know'], 'The failure is a sentence a person would believe. The check is whether the note actually said it.')),
        $g('retrieve', ['look up a note', 'retrieval'], fn() => tf_flow('retrieve', 'Look up, then answer', [['question', 'closing time?'], ['search', 'the notes'], ['quote', 'the sentence']], 'The model does not have to remember the closing time. The search finds the note, and the answer is allowed to quote only that.')),
        $g('score', ['gold note', 'score an answer'], fn() => tf_panels('score', 'Your reply against the reply you wrote as correct', 'gold', ['“closes at 9”', 'the expected words'], 'reply', ['“closes at 9”', 'match', 'or a miss'], 'The gold note is written before you see the model’s answer. Changing the gold after a failure is moving the target.')),
        $g('attention', ['attention', 'which earlier token'], fn() => tf_bars('attention', 'Which earlier token gets the weight', [
            ['label' => 'lab', 'value' => 1, 'display' => '0.1', 'color' => '#94a3b8'],
            ['label' => 'closes', 'value' => 2, 'display' => '0.2', 'color' => '#0284c7'],
            ['label' => '9', 'value' => 7, 'display' => '0.7', 'color' => '#059669'],
        ], 'The current token looks back. The weights add up to 1. Most of the attention here sits on the token that actually answers the question.')),
        $g('neuron', ['neuron', 'squash'], fn() => tf_flow('neuron', 'Weighted sum, then a squash', [['inputs', '2, 1'], ['sum', '0.5×2 + 1×1'], ['squash', 'between 0 and 1']], 'The weights say how much each input matters. The sum is one number. The squash keeps the output in a range the next layer can use.')),
        $g('embed-doc', ['embedding vs', 'referencing'], fn() => tf_panels('embed-doc', 'Put the child inside, or point at it', 'embed', ['the address lives on the user', 'one read gets both', 'duplicated if shared'], 'reference', ['store the user id', 'a second read joins', 'one copy of the user'], 'Embed when the child belongs to one parent and you always read them together. Reference when many parents share the child.')),
        $g('embed', ['embeddings'], fn() => tf_dots('embed', 'Similar notes land near each other', [[160, 80, '#059669', 'tea'], [190, 100, '#059669', 'milk'], [480, 130, '#e11d48', 'server down']], 'An embedding is a list of numbers you can plot as a direction. Notes about drinks sit together. A note about an outage sits somewhere else.')),
        $g('similarity', ['dot product', 'similarity'], fn() => tf_bars('similarity', 'Same direction scores higher', [
            ['label' => 'tea · milk', 'value' => 8, 'display' => 'high', 'color' => '#059669'],
            ['label' => 'tea · outage', 'value' => 1, 'display' => 'low', 'color' => '#94a3b8'],
        ], 'The dot product is large when two arrows point the same way. That is the similarity. It is not a proof they mean the same thing.')),
        $g('vector', ['vector is a direction', 'a vector'], fn() => tf_dots('vector', 'A vector is a direction and a length', [[80, 140, '#0284c7', 'start'], [360, 50, '#0284c7', 'end']], 'From the start to the end is the arrow. Two numbers, x and y, are enough to draw it. Adding vectors stacks the arrows.')),
        $g('tokens', ['token'], fn() => tf_cells('tokens', 'A sentence cut into pieces', [['1', 'lab'], ['2', 'closes'], ['3', 'at'], ['4', '9']], 'The pieces are the tokens. Punctuation can be its own piece. The model never sees the sentence except as this row.', [], '“lab closes at 9”')),
        $g('prompt', ['prompt'], fn() => tf_flow('prompt', 'Instruction, context, question', [['instruction', 'how to answer'], ['context', 'the note'], ['question', 'what is asked']], 'Mixing them into one blob makes the note look like an order. Keep the three jobs in three boxes.')),
        $g('char', ['small integer', 'character is', 'one character is not'], fn() => tf_bars('char', 'The letter and the code are the same slot', [
            ['label' => 'A', 'value' => 65, 'display' => '65', 'color' => '#0284c7'],
            ['label' => 'B', 'value' => 66, 'display' => '66', 'color' => '#059669'],
            ['label' => 'C', 'value' => 67, 'display' => '67', 'color' => '#7c3aed'],
        ], 'A char is a small integer we agreed to draw as a letter. The digit character 7 is code 55, not the integer 7.')),
        $g('short', ['second half', 'stop before'], fn() => tf_split('short', 'AND can skip the second question', 'left side false?', 'Skip right', 'do not touch it', 'Check right', 'only if left was true', 'The skip is the point. The right side might be a call you cannot afford, or a value that does not exist.')),
        $g('switch', ['named cases', 'switch statement', 'switch'], fn() => tf_cells('switch', 'The value picks one case', [['200', 'ok'], ['404', 'missing'], ['500', 'fault'], ['else', 'default']], 'One value, one case. default is the leftover. Cases do not fall into each other in this picture: you meant one result.', [1])),
        $g('shadow-name', ['hides the outer', 'inner name'], fn() => tf_layers('shadow-name', 'The inner name hides the outer one', [
            ['outer price = 5', '#dbeafe', '#0284c7'],
            ['inner price = 9', '#fef3c7', '#d97706'],
        ], 'Inside the inner block, price means 9. After the block ends, that name is gone and price means 5 again.')),
        $g('const', ['promise not to store'], fn() => tf_object('const', 'A promise not to store again', 'rate', ['const rate = 5', 'a later store is refused', 'the reader can trust it'], 'const is a promise to the next reader. The value can still be used. It cannot be assigned a second time.')),
        $g('cast', ['drops the fraction', 'cast keeps'], fn() => tf_panels('cast', 'When the fraction disappears', '7 / 2 as int', ['integer division', 'result 3', 'the .5 is already gone'], '7.0 / 2', ['a real division', 'result 3.5', 'the cast happened first'], 'Casting after the integer division only turns 3 into 3.0. The fraction was lost in the division.')),
        $g('wrap', ['whole number wraps', 'wraps'], fn() => tf_cells('wrap', 'A byte wraps after 255', [['254', '254'], ['255', '255'], ['+1', '0'], ['+1', '1']], 'There is no 256 in an 8-bit slot. The next value is 0. A signed byte can also jump to a negative, which is the same bits read differently.', [2])),
        $g('stride', ['next slot', 'address of the next'], fn() => tf_cells('stride', 'Plus one slot moves by the size of the item', [['p', '10'], ['+4', '20'], ['+4', '30'], ['+4', '40']], 'An int pointer steps by 4 bytes in this picture, not by 1. p + 1 is the next int. p + 1 as a byte pointer would be a different address.', [0, 1], 'bytes between ints')),
        $g('decay', ['address of the first', 'name of the row'], fn() => tf_flow('decay', 'The name of the row is the first address', [['row', '10, 20, 30'], ['name', 'address of 10'], ['length', 'passed separately']], 'The function receives the address, not a copy of every slot, and not the length. You pass the length as a second argument.')),
        $g('zerobyte', ['zero byte', 'walk until'], fn() => tf_cells('zerobyte', 'Walk until the zero byte', [['m', 'm'], ['i', 'i'], ['l', 'l'], ['k', 'k'], ['0', '0']], 'The length is 4. The zero is not one of the letters. It is the sign that the walk must stop. The buffer is one slot longer than the word.', [4])),
        $g('strcmp', ['one character at a time', 'two words'], fn() => tf_cells('strcmp', 'Compare until a character differs', [['m', 'm'], ['i', 'i'], ['l', 'l'], ['k', 'k'], ['s', 's']], 'milk and milks share a prefix. The shorter one ends first, so they are not equal. Equal pointers would be a different question: same address.', [4])),
        $g('twod', ['one row, then the next', 'next row'], fn() => tf_grid('twod', 'Row, then column', ['c0', 'c1', 'c2'], ['r0', 'r1'], ['1', '2', '3', '4', '5', '6'], 'Index (1, 2) is row 1, column 2, the value 6. The flat position is row × width + column.', [5])),
        $g('pointer', ['pointer', 'address of a slot'], fn() => tf_flow('pointer', 'A pointer stores an address', [['price', 'slot holds 40'], ['p', 'holds the address'], ['*p', 'reads 40']], 'p is not 40. p is where 40 lives. Writing through *p changes the slot, so price changes too.')),
        $g('struct', ['a struct', 'struct is', 'bundle of fields'], fn() => tf_object('struct', 'A struct is one bundle', 'note', ['char title[16]', 'int pages', 'int urgent'], 'The fields sit together under one name. note.pages is the pages slot of that bundle, not a free variable called pages.')),
        $g('heapmem', ['heap picture'], fn() => tf_panels('heapmem', 'The call stack, and storage that outlives the call', 'stack', ['lives for the call', 'gone when it returns', 'local slots'], 'heap', ['you ask for a block', 'it stays', 'you must know when to end it'], 'A pointer to a local slot is a lie after the function returns. The slot has been given back.')),
        $g('var', ['var does not'], fn() => tf_panels('var', 'var infers the type, it does not leave it open', 'var count = 2', ['the type is int', 'count = "milk" is refused', 'the name keeps the type'], 'a new value', ['count = 3', 'same type', 'allowed'], 'var saves you writing the type once. The next line still has to match the type that was inferred.')),
        $g('interp', ['holes in a string', 'interpolation'], fn() => tf_flow('interp', 'The holes fill from names you have', [['name', 'milk'], ['count', '2'], ['result', 'note milk x2']], 'The dollar sign turns the braces into holes. Without it, the braces and the word name are printed as text.')),
        $g('elseif', ['first answer is no', 'else if'], fn() => tf_split('elseif', 'The second question waits for a no', 'score ≥ 85?', 'merit', 'stop here', 'else if ≥ 60', 'pass', '90 is merit. It must not also become a plain pass. The second test runs only when the first one failed.')),
        $g('property', ['door to a field', 'property is'], fn() => tf_flow('property', 'The caller knocks, the field stays inside', [['caller', 'wants pages'], ['property', 'the door'], ['field', 'the slot']], 'A negative write can be refused at the door. The field never sees it. Reading goes through the same door.')),
        $g('ctor', ['constructor fills', 'constructor'], fn() => tf_flow('ctor', 'new runs the constructor before you hold the object', [['new Note', 'ask'], ['constructor', 'fill title'], ['you', 'a filled object']], 'A half-built object is not handed to you. The constructor is the only door that runs at birth.')),
        $g('static', ['static belongs'], fn() => tf_panels('static', 'static belongs to the type', 'one object', ['note A pages = 4', 'its own fields', 'changing A keeps B'], 'static count', ['shared by the type', 'A and B see the same', 'one slot, not two'], 'An instance field is per object. A static field is one slot with the type’s name on it.')),
        $g('interface', ['interface', 'list of promises'], fn() => tf_flow('interface', 'A list of promises, not a parent', [['CanSave', 'Save()'], ['Note', 'has Save'], ['caller', 'calls Save']], 'The interface does not give you fields. It says any object you accept here must have these methods. The object can be a note or a photo.')),
        $g('enum', ['enum'], fn() => tf_cells('enum', 'Names for whole numbers, starting at 0', [['0', 'Draft'], ['1', 'Sent'], ['2', 'Read']], 'Draft is 0 unless you set it. An unset enum field is therefore Draft, not “no status”. Name the zero on purpose.', [0])),
        $g('generic', ['only holds one type', 'generic'], fn() => tf_object('generic', 'The box is allowed one type', 'Box<Note>', ['holds a Note', 'a string is refused', 'the check is at compile time'], 'A box of notes will not accept a string. You find out when you write the call, not when a user opens the page.')),
        $g('inherit', ['parent fields', 'inheritance', 'child starts'], fn() => tf_tree('inherit', 'The child starts with the parent fields', ['Note', 'Page', '', 'title', 'body', '', ''], 'Page is a Note plus a body. It does not copy title into a second slot. A Page can be used where a Note is asked for.')),
        $g('tuple', ['without a new class', 'tuple'], fn() => tf_cells('tuple', 'Two results, no new type', [['name', 'ok'], ['code', '200']], 'A method can hand back a pair. The names are local to the pair. You do not need a class when you will never add behaviour.', [0, 1])),
        $g('expected', ['expected versus', 'expected vs'], fn() => tf_panels('expected', 'The expected value is chosen, not copied from the code', 'expected', ['4', 'you wrote this', 'before running'], 'actual', ['2 + 2', 'the code produced it', 'compare the two'], 'If expected is computed by the same buggy formula, the test agrees with the bug. Write the expected number yourself.')),
        $g('linq', ['where and select'], fn() => tf_flow('linq', 'Where keeps, Select reshapes', [['rows', 'all notes'], ['where', 'urgent only'], ['select', 'just titles']], 'Both are loops you could have written. Where drops rows. Select changes what each remaining row looks like. The source list is not edited.')),
        $g('check', ['failed check', 'a result, not'], fn() => tf_split('check', 'A failed check is a result', 'pages ≥ 0?', 'Ok', 'the value', 'Error', 'a message, not a crash', 'The caller has to look at the result. An exception is for a surprise. A bad page count is a result you expected to handle.')),
        $g('route-status', ['path and a status'], fn() => tf_flow('route-status', 'A route is a path plus a status', [['path', '/notes/4'], ['handler', 'the method'], ['status', '200 or 404']], 'The path selects the handler. The handler returns a status. A missing note is 404 from that handler, not a missing route.')),
        $g('event-loop', ['event loop', 'microtask', 'macrotask'], fn() => tf_pile('event-loop', 'The call stack, then the queued tasks', ['stack: your function', 'microtasks next', 'timers after that'], 'The function you called runs to the end. Promises that already resolved run before the next timer. A timer is not “in a moment” if the stack is still busy.')),
        $g('promise', ['promise', 'async', 'await'], fn() => tf_flow('promise', 'Pending, then a result', [['call', 'fetch'], ['pending', 'not yet'], ['then', 'the body']], 'await pauses that function, not the whole page. Other events can run before the result arrives. The result and the failure are the two endings.')),
        $g('closure', ['closure', 'hoisting', 'scope,'], fn() => tf_layers('closure', 'The inner function keeps the outer slot', [
            ['outer count = 0', '#dbeafe', '#0284c7'],
            ['inner function', '#d1fae5', '#059669'],
        ], 'The inner function can still see count after the outer call has returned. That kept slot is the closure. A var in the same function is one shared slot, not one per loop turn.')),
        $g('prototype', ['prototype'], fn() => tf_chain('prototype', 'Look on the object, then on its prototype', ['note', 'Note.proto', 'Object'], 'Reading title finds it on the note. Reading toString does not, so the walk continues to the prototype. The chain ends.')),
        $g('dom', ['dom manipulation', 'dom traversal', 'dom element', 'the dom'], fn() => tf_tree('dom', 'The document is a tree you can walk', ['body', 'header', 'main', 'a', '', 'h1', 'p'], 'parent walks up. children walks down. closest walks up until a selector matches. The tree is the page, not your JavaScript variables.')),
        $g('twoway', ['two-way', 'ng-model', 'mvvm'], fn() => tf_flow('twoway', 'The box and the variable stay in step', [['input', 'what you type'], ['model', 'the variable'], ['page', 'what you see']], 'Typing changes the variable. Changing the variable changes the box. A one-way binding does only one of those.')),
        $g('digest', ['digest', '$watch', '$apply'], fn() => tf_flow('digest', 'Change, then check the watchers', [['edit', 'a value'], ['digest', 'run watchers'], ['paint', 'the page']], 'A watcher is a question: has this expression changed? The digest asks them until the answers settle, or until it gives up.')),
        $g('di', ['dependency injection', 'ng-strict-di'], fn() => tf_flow('di', 'Ask for a thing by name, receive the object', [['need', 'NotesApi'], ['injector', 'knows who builds it'], ['you', 'the object']], 'The function does not construct its helpers. It names them. The injector has one place that knows how they are built.')),
        $g('directive', ['directive', 'compile', 'transclu'], fn() => tf_flow('directive', 'A directive is a custom behaviour on a tag', [['tag', 'the HTML'], ['directive', 'the behaviour'], ['scope', 'the data it sees']], 'Restrict says whether it is an element or an attribute. An isolate scope takes only what you pass, instead of inheriting everything.')),
        $g('ssr', ['server component', 'use client', 'ssr', 'ssg', 'isr', 'csr', 'static generation', 'force-dynamic'], fn() => tf_flow('ssr', 'Who builds the HTML', [['request', 'the URL'], ['server', 'HTML or JSON'], ['browser', 'paints and hydrates']], 'A server component runs on the server and can read data there. A client component runs in the browser and can listen to clicks. The line between them is the point of the chapter.')),
        $g('suspense', ['suspense', 'streaming', 'skeleton'], fn() => tf_flow('suspense', 'Show a placeholder, then the real block', [['wait', 'data'], ['skeleton', 'the placeholder'], ['ready', 'the content']], 'The rest of the page can paint. The slow block has a fallback until its data arrives. The fallback is not an error.')),
        $g('cache', ['caching', 'cache', 'revalidatepath', 'revalidatetag', 'revalidation', 'revalidate'], fn() => tf_layers('cache', 'Several caches, not one', [
            ['router cache — this visit', '#fef3c7', '#d97706'],
            ['full route cache — the HTML', '#dbeafe', '#0284c7'],
            ['data cache — the fetch', '#d1fae5', '#059669'],
        ], 'A fresh HTML page can still be showing a cached fetch. Revalidate the layer that actually went stale.')),
        $g('middleware', ['middleware', 'controller filter', 'guards'], fn() => tf_flow('middleware', 'The check runs before the handler', [['request', 'arrives'], ['guard', 'auth, role'], ['handler', 'the action']], 'A guard that refuses never calls the handler. The handler can assume the check already passed.')),
        $g('migration', ['migration'], fn() => tf_flow('migration', 'A migration changes the shape in order', [['empty', 'no table'], ['up', 'create notes'], ['next', 'add a column']], 'Each file is one step. Running them in order rebuilds the shape. Down undoes one step. The data and the shape are different problems.')),
        $g('validation', ['validation', 'reject a bad', 'zod'], fn() => tf_split('validation', 'Reject a bad note before you save it', 'title present?', 'Save', 'the row is written', 'Reject', 'errors, no row', 'The check happens before the database. A saved row has already passed the rules you wrote.')),
        $g('blade', ['blade'], fn() => tf_flow('blade', 'A layout, then a page inside it', [['layout', 'nav and footer'], ['section', 'this page'], ['browser', 'one HTML']], '{{ }} prints a value with escaping. A raw print is how a note becomes a script. The layout is the chrome you do not copy onto every page.')),
        $g('hasmany', ['has many', 'relationship'], fn() => tf_panels('hasmany', 'A user has many notes', 'users', ['id 7 Ana', 'one row'], 'notes', ['user_id 7 milk', 'user_id 7 tea', 'many rows'], 'The note stores the user id. The user does not store a list of note ids. Loading the user can then load the notes that point at them.')),
        $g('component', ['component'], fn() => tf_tree('component', 'A component is a function of its inputs', ['App', 'List', 'Form', 'Item', 'Item', '', ''], 'The parent passes props down. The child does not reach up and edit the parent. State lives in the component that owns the change.')),
        $g('props', ['props', 'children'], fn() => tf_flow('props', 'Props travel from parent to child', [['parent', 'owns the note'], ['props', 'title, id'], ['child', 'only displays']], 'The child receives values. It does not own them. A callback prop is how the child asks the parent to change something.')),
        $g('usestate', ['usestate', 'state in react', 'state &', 'state and'], fn() => tf_panels('usestate', 'State is the value that re-renders', 'before click', ['count = 0', 'the screen shows 0'], 'after setCount', ['count = 1', 'React paints again'], 'Assigning a variable does not paint. The setter schedules a render with the next value. The next value should be a new one, not a mutated old object.')),
        $g('useeffect', ['useeffect', 'lifecycle'], fn() => tf_flow('useeffect', 'Render, then the effect, then a cleanup', [['render', 'the screen'], ['effect', 'subscribe'], ['cleanup', 'unsubscribe']], 'The effect runs after paint. The cleanup runs before the next effect and when the component leaves. The dependency list is the values the effect reads.')),
        $g('context', ['context api', 'usecontext', 'prop drilling'], fn() => tf_tree('context', 'Context skips the components in the middle', ['Provider', 'List', 'Item', '', '', 'reads it', ''], 'Without context, every middle component must pass the value along. The provider holds it. The reader takes it. The middle ones stay quiet.')),
        $g('reducer', ['usereducer', 'reducer'], fn() => tf_flow('reducer', 'An action goes in, the next state comes out', [['state', 'count 0'], ['action', 'increment'], ['next', 'count 1']], 'The reducer is a pure function. The same state and the same action always give the same next state. The component dispatches. It does not edit the state itself.')),
        $g('memo-hook', ['usememo', 'usecallback'], fn() => tf_split('memo-hook', 'Recompute only when the inputs change', 'inputs same?', 'Reuse', 'the saved value', 'Compute', 'a new value', 'useMemo remembers a result. useCallback remembers a function. Both are about skipping work, not about making a value global.')),
        $g('ref', ['useref'], fn() => tf_panels('ref', 'A ref survives the render and does not paint', 'state', ['changing it re-renders', 'the screen updates'], 'ref', ['changing it does not paint', 'the same object next time'], 'A ref is the right box for a timer id or a DOM node. It is the wrong box for a title you want the screen to show.')),
        $g('router', ['router', 'routing', 'route '], fn() => tf_flow('router', 'The URL picks the screen', [['URL', '/notes/4'], ['router', 'the table'], ['screen', 'Note 4']], 'The path is the source of truth for which screen is open. A click changes the path. The router renders the screen that matches.')),
        $g('form', [' forms', ' form', 'form ', 'ng-form', 'formcontroller', 'form handling', 'submitting actions'], fn() => tf_flow('form', 'Controlled: the state is the value', [['state', 'title'], ['input', 'shows title'], ['submit', 'reads state']], 'A controlled input shows the state and writes back to it on every change. An uncontrolled input keeps the value in the DOM until you ask for it.')),
        $g('loop', ['loop', 'while ', 'foreach', 'for...'], fn() => tf_cells('loop', 'The same body, one pass at a time', [['pass 1', '0'], ['pass 2', '1'], ['pass 3', '2'], ['stop', '3']], 'The counter changes at the end of the pass. The check happens before the next pass. When the check fails, the body does not run again.', [], 'i = 0, 1, 2')),
        $g('condition', [' if', 'if ', 'if.', 'else', 'condition', 'decision', 'ternary', 'branch'], fn() => tf_split('condition', 'A comparison picks one branch', 'score ≥ 60?', 'pass', 'this block runs', 'fail', 'the other block', 'One of the two blocks runs. = stores a value. == compares. The test is a yes or no, not the assignment.')),
        $g('array', ['array', 'numbered slots', 'lists', 'list '], fn() => tf_cells('array', 'Slots in a row, numbered from zero', [['0', 'milk'], ['1', 'tea'], ['2', 'bread']], 'The last index is length − 1, not the length. Index 3 does not exist in a row of three. A list can grow. A fixed array cannot.', [2], 'length 3')),
        $g('string', ['string'], fn() => tf_cells('string', 'A string is characters in order', [['0', 'm'], ['1', 'i'], ['2', 'l'], ['3', 'k']], 'Length is 4. The first character is index 0. A slice is a new string. The original letters stay where they were.', [], 'milk')),
        $g('variable', ['variable', 'data type', 'types:', 'let, const', 'type casting'], fn() => tf_object('variable', 'A name, a type, and a value', 'count', ['name: count', 'type: int', 'value: 2'], 'The name is how you refer to the slot. The type says what may be stored. The value is what is stored now.')),
        $g('operator', ['operator', 'arithmetic', 'integer division', 'expressions'], fn() => tf_panels('operator', 'Integer division drops the fraction', '7 / 2', ['both integers', 'result 3', 'remainder 1'], '7.0 / 2', ['a real number', 'result 3.5', 'the fraction survives'], 'The remainder operator is the leftover of that integer division. 7 = 2 × 3 + 1.')),
        $g('error', ['error', 'exception', 'try, catch', 'try/except', 'try '], fn() => tf_split('error', 'Try the risky line, catch the failure', 'did it throw?', 'catch', 'you handle it', 'continue', 'no error', 'The catch runs only when the try fails. finally runs either way. A caught error does not vanish unless you decide what the page should do next.')),
        $g('module', ['modules', 'import ', 'export', 'packages'], fn() => tf_flow('module', 'One file uses a name from another', [['a.js', 'exports add'], ['import', 'the name'], ['b.js', 'calls add']], 'The export is the public door. Names that are not exported stay in the file. The import is how the other file receives the door.')),
        $g('file', ['file handling', 'file '], fn() => tf_flow('file', 'Open, use, close', [['open', 'the path'], ['read', 'the bytes'], ['close', 'give it back']], 'A with-block or a using-block closes the file even when the read fails. A path is a string. The file is the thing the operating system opens.')),
        $g('session', ['session', 'cookie', 'localstorage'], fn() => tf_panels('session', 'Where the “logged in” fact lives', 'cookie', ['sent on each request', 'the browser stores it', 'small'], 'server session', ['the id is in the cookie', 'the data stays on the server', 'the note list is not in the cookie'], 'localStorage stays in the browser and is not sent automatically. A cookie is. Do not put a secret in either one on the page.')),
        $g('date', ['datetime', 'date &', 'dates', 'time &', 'time function', 'timezone'], fn() => tf_cells('date', 'A moment has a date, a time, and a zone', [['date', '21 Nov'], ['time', '17:00'], ['zone', '+5:30']], 'The same moment is a different clock time in another zone. Store the zone, or store UTC, or you will sort two cities incorrectly.', [])),
        $g('function', ['function', 'method', 'arrow'], fn() => tf_flow('function', 'A name for a rule with inputs and an output', [['args', 'pages, urgent'], ['body', 'the rule'], ['return', 'one value']], 'The call supplies the arguments. The return is what the caller receives. Code after a return does not run.')),
        $g('class', ['class ', 'oop', 'object-oriented', '__init__', 'constructor'], fn() => tf_object('class', 'A class is a shape, an object is one filled-in copy', 'Note', ['title = milk', 'pages = 4', 'save()'], 'The class describes the fields and methods. Each object has its own field values. Two notes do not share a title unless you meant them to.')),
        $g('security', ['security', 'xss', 'csrf', 'csp'], fn() => tf_split('security', 'Text from a user is data, not code', 'insert into the page?', 'Escape', 'shows as text', 'Raw', 'can become a script', 'A title that contains a tag must be shown as text. A form that changes data needs a token so another site cannot submit it for you.')),
        $g('testing', ['testing', 'phpunit', 'jest', 'playwright'], fn() => tf_panels('testing', 'Expected is written by you', 'expected', ['the answer you want', '4', 'chosen first'], 'actual', ['what the code returned', '2 + 2', 'compared after'], 'A test that builds the expected value with the same code as the function will agree with a bug. Write the expected result down.')),
        $g('intro', ['introduction', 'getting started', 'how code runs', 'how to apply', 'what is '], fn() => tf_flow('intro', 'Write it, run it, read the result', [['you', 'the source'], ['runtime', 'runs it'], ['screen', 'the result']], 'The source is text. Running it is a separate step. The result is what you check. The rest of the course names the pieces inside that source.')),
        $g('install', ['installing', 'install', 'setup', 'environment', 'create-next-app', 'composer'], fn() => tf_flow('install', 'Install, create, run', [['tools', 'on the machine'], ['project', 'the folder'], ['run', 'the first page']], 'The tools are installed once. The project is a folder with a known shape. Running it is how you know the tools and the folder agree.')),
    ];
}

function teach_fallback(string $slug, string $title): string
{
    $clean = preg_replace('/\b(JavaScript|AngularJS|Next\.js|React\.js|React|Python|PHP|HTML5|HTML|CSS3|CSS|SQL|MySQL|PostgreSQL|MongoDB|Laravel|CodeIgniter|Flutter|Masterclass|Tutorial)\b/i', '', $title) ?? $title;
    $parts = preg_split('/\s*,\s*|\s+&\s+|\s+ and \s+|\s+vs\s+|\s+\/\s+/i', $clean) ?: [];
    $parts = array_values(array_filter(array_map(static function ($p) {
        $p = trim($p, " \t\n\r\0\x0B()[]");
        return $p === '' ? '' : $p;
    }, $parts)));
    $parts = array_slice($parts, 0, 4);
    if (count($parts) < 2) {
        $parts = [$title, 'the small example', 'the check'];
    }
    return tf_flow('outline', 'The pieces this chapter separates', $parts, 'Read the boxes left to right. Each box is one piece the chapter keeps apart, and the text under the picture says what that piece does.');
}
