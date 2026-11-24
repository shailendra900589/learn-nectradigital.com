<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/track_seed.php';
require_once __DIR__ . '/includes/teach_figures.php';

function excel_meta(): array
{
    return [
        'title' => 'Excel Tutorial',
        'slug' => 'excel-tutorial',
        'description' => 'Excel from the first cell through formulas, lookups, pivots, Power Query, and dynamic arrays, ending in one April spend file you can explain.',
        'keywords' => 'excel tutorial, formulas, xlookup, pivot table, power query, charts, data validation, dynamic arrays',
    ];
}

function xl_doc(string $room, string $cell, string $fx, string $sheet, string $inner): string
{
    return '<!DOCTYPE html>' . "\n"
        . '<html><head><meta charset="utf-8"><title>April spend</title></head>' . "\n"
        . '<body data-room="' . htmlspecialchars($room, ENT_QUOTES) . '" style="margin:0;background:#f8fafc;color:#0f172a;font-family:sans-serif;">' . "\n"
        . '<main data-cell="' . htmlspecialchars($cell, ENT_QUOTES) . '" data-fx="' . htmlspecialchars($fx, ENT_QUOTES) . '" data-sheet="' . htmlspecialchars($sheet, ENT_QUOTES) . '" style="margin:0;">' . "\n"
        . $inner . "\n"
        . '</main></body></html>';
}

function xl_table(array $headers, array $body, array $hot = []): string
{
    $letters = range('A', 'H');
    $n = count($headers);
    $html = '<table style="border-collapse:collapse;width:100%;background:#ffffff;font-size:13px;font-family:sans-serif;">';
    $html .= '<tr><th style="width:32px;background:#f3f4f6;border:1px solid #e5e7eb;"></th>';
    for ($i = 0; $i < $n; $i++) {
        $html .= '<th style="background:#f3f4f6;border:1px solid #e5e7eb;padding:4px 8px;color:#4b5563;font-weight:700;">' . $letters[$i] . '</th>';
    }
    $html .= '</tr>';
    $html .= '<tr><th style="background:#f3f4f6;border:1px solid #e5e7eb;color:#6b7280;">1</th>';
    foreach ($headers as $h) {
        $html .= '<td style="border:1px solid #e5e7eb;padding:6px 8px;font-weight:800;background:#f0fdf4;color:#14532d;">' . htmlspecialchars((string)$h, ENT_QUOTES) . '</td>';
    }
    $html .= '</tr>';
    $r = 2;
    foreach ($body as $row) {
        $html .= '<tr><th style="background:#f3f4f6;border:1px solid #e5e7eb;color:#6b7280;">' . $r . '</th>';
        foreach ($row as $c => $val) {
            $addr = $letters[$c] . $r;
            $on = in_array($addr, $hot, true);
            $html .= '<td style="border:' . ($on ? '2px solid #107c41' : '1px solid #e5e7eb')
                . ';padding:6px 8px;background:' . ($on ? '#dcfce7' : '#ffffff')
                . ';font-weight:' . ($on ? '800' : '500')
                . ';color:#111827;">' . htmlspecialchars((string)$val, ENT_QUOTES) . '</td>';
        }
        $html .= '</tr>';
        $r++;
    }
    return $html . '</table>';
}

function xl_grid(string $cell, string $fx, array $headers, array $body, array $hot = [], string $sheet = 'Spend', string $note = ''): string
{
    $html = xl_table($headers, $body, $hot);
    if ($note !== '') {
        $html .= '<p style="margin:8px 0 0;font-size:12px;font-weight:700;color:#166534;">' . htmlspecialchars($note, ENT_QUOTES) . '</p>';
    }
    return xl_doc('xl-sheet', $cell, $fx, $sheet, $html);
}

function xl_bars(string $cell, string $fx, string $title, array $bars, string $note = ''): string
{
    $rows = '';
    foreach ($bars as $bar) {
        $rows .= '<div style="display:flex;align-items:center;gap:8px;margin:7px 0;">'
            . '<div style="width:78px;font-size:12px;font-weight:800;color:#14532d;">' . htmlspecialchars((string)$bar[0], ENT_QUOTES) . '</div>'
            . '<div style="flex:1;background:#f3f4f6;border-radius:6px;height:14px;"><div style="width:' . (int)$bar[1] . '%;height:14px;background:#107c41;border-radius:6px;"></div></div>'
            . '<div style="width:72px;text-align:right;font-size:12px;font-weight:800;color:#111827;">' . htmlspecialchars((string)$bar[2], ENT_QUOTES) . '</div></div>';
    }
    $html = '<div style="background:#ffffff;border:1px solid #d1d5db;border-radius:12px;padding:12px;">'
        . '<div style="font-size:13px;font-weight:800;color:#166534;margin-bottom:6px;">' . htmlspecialchars($title, ENT_QUOTES) . '</div>'
        . $rows . '</div>';
    if ($note !== '') {
        $html .= '<p style="margin:8px 0 0;font-size:12px;font-weight:700;color:#166534;">' . htmlspecialchars($note, ENT_QUOTES) . '</p>';
    }
    return xl_doc('xl-chart', $cell, $fx, 'Chart', $html);
}

function xl_pivot(string $cell, string $fx, array $headers, array $body, array $hot = [], string $note = ''): string
{
    $html = xl_table($headers, $body, $hot);
    if ($note !== '') {
        $html .= '<p style="margin:8px 0 0;font-size:12px;font-weight:700;color:#166534;">' . htmlspecialchars($note, ENT_QUOTES) . '</p>';
    }
    return xl_doc('xl-pivot', $cell, $fx, 'Pivot', $html);
}

function xl_steps(string $cell, string $fx, array $steps, string $note = ''): string
{
    $html = '<ol style="margin:0;padding:12px 12px 12px 32px;background:#ffffff;border:1px solid #d1d5db;border-radius:12px;">';
    foreach ($steps as $step) {
        $html .= '<li style="margin:6px 0;font-size:13px;font-weight:700;color:#14532d;">' . htmlspecialchars($step, ENT_QUOTES) . '</li>';
    }
    $html .= '</ol>';
    if ($note !== '') {
        $html .= '<p style="margin:8px 0 0;font-size:12px;font-weight:700;color:#166534;">' . htmlspecialchars($note, ENT_QUOTES) . '</p>';
    }
    return xl_doc('xl-query', $cell, $fx, 'Query', $html);
}

function xl_tiles(string $cell, string $fx, array $tiles, string $note = ''): string
{
    $html = '<div style="display:flex;flex-wrap:wrap;gap:8px;">';
    foreach ($tiles as $tile) {
        $html .= '<div style="flex:1 1 120px;background:#ffffff;border:1px solid #86efac;border-radius:12px;padding:10px;">'
            . '<div style="font-size:10px;letter-spacing:.08em;font-weight:800;color:#166534;">' . htmlspecialchars((string)$tile[0], ENT_QUOTES) . '</div>'
            . '<div style="margin-top:4px;font-size:18px;font-weight:800;color:#14532d;">' . htmlspecialchars((string)$tile[1], ENT_QUOTES) . '</div>'
            . '<div style="margin-top:2px;font-size:12px;color:#3f6212;">' . htmlspecialchars((string)$tile[2], ENT_QUOTES) . '</div></div>';
    }
    $html .= '</div>';
    if ($note !== '') {
        $html .= '<p style="margin:8px 0 0;font-size:12px;font-weight:700;color:#166534;">' . htmlspecialchars($note, ENT_QUOTES) . '</p>';
    }
    return xl_doc('xl-dash', $cell, $fx, 'Summary', $html);
}

function xl_ch(
    string $section,
    string $title,
    string $keywords,
    string $lead,
    string $heading,
    string $para,
    array $bullets,
    string $visual,
    array $rows,
    array $e1,
    array $e2,
    array $micro,
    array $quiz,
    string $practice
): array {
    return track_lesson('visual', '', [
        'section' => $section,
        'title' => $title,
        'keywords' => $keywords,
        'lead' => $lead,
        'heading' => $heading,
        'para' => $para,
        'bullets' => $bullets,
        'visual' => $visual,
        'rows' => $rows,
        'e1' => $e1,
        'e2' => $e2,
        'micro' => $micro,
        'quiz' => $quiz,
        'practice' => $practice,
    ]);
}

function xl_q(string $question, string $ok, string $a, string $b, string $c): array
{
    return bquiz($question, $ok, $a, $b, $c);
}

function excel_chapters(): array
{
    require_once __DIR__ . '/seed_excel_a.php';
    require_once __DIR__ . '/seed_excel_b.php';
    require_once __DIR__ . '/seed_excel_c.php';
    require_once __DIR__ . '/seed_excel_d.php';
    $all = array_merge(excel_chapters_a(), excel_chapters_b(), excel_chapters_c(), excel_chapters_d());
    $project = [];
    $rest = [];
    foreach ($all as $ch) {
        if (str_starts_with($ch['title'], 'Project:')) {
            $project[] = $ch;
            continue;
        }
        $rest[] = $ch;
    }
    return array_merge($rest, $project);
}

if (PHP_SAPI === 'cli' && isset($_SERVER['argv'][0]) && realpath((string)$_SERVER['argv'][0]) === realpath(__FILE__)) {
    require_once __DIR__ . '/includes/db.php';
    $chapters = excel_chapters();
    if (count($chapters) !== 86) {
        fwrite(STDERR, 'expected 86 excel chapters, got ' . count($chapters) . "\n");
        exit(1);
    }
    $seen = [];
    foreach ($chapters as $ch) {
        if (isset($seen[$ch['title']])) {
            fwrite(STDERR, "duplicate title {$ch['title']}\n");
            exit(1);
        }
        $seen[$ch['title']] = true;
        $html = build_rich_chapter_html($ch);
        if (stripos($html, '<pre') !== false || stripos($html, 'language-') !== false) {
            fwrite(STDERR, "code block in {$ch['title']}\n");
            exit(1);
        }
        if (stripos($html, 'EXCEL STUDIO') === false) {
            fwrite(STDERR, "missing studio in {$ch['title']}\n");
            exit(1);
        }
    }
    track_seed($pdo, excel_meta(), $chapters);
    track_refresh($pdo, 'excel-tutorial', $chapters);
    track_place($pdo, 'excel-tutorial', array_map(static fn(array $ch): string => $ch['title'], $chapters));
    track_sections($pdo, 'excel-tutorial', [
        6 => 'The grid',
        12 => 'The list',
        18 => 'The look',
        26 => 'Formulas',
        34 => 'Everyday functions',
        40 => 'Words and dates',
        48 => 'Lookup',
        54 => 'Conditional totals',
        58 => 'Tables',
        62 => 'Rules',
        66 => 'Charts',
        70 => 'Pivot tables',
        73 => 'What-if',
        77 => 'Power Query',
        82 => 'Dynamic arrays',
        85 => 'Trust',
        86 => 'The project',
    ]);
}
