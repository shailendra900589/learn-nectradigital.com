<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/track_seed.php';
require_once __DIR__ . '/seed_prompt_lessons.php';

function pe_meta(): array
{
    return [
        'title' => 'Prompt Engineering',
        'slug' => 'prompt-engineering',
        'description' => 'Prompt engineering from the first paste through ChatGPT, Claude, and Gemini, then reusable prompts and human-checked automation. Every lesson has a prompt you can copy.',
        'keywords' => 'prompt engineering, chatgpt prompts, claude prompts, gemini prompts, ai automation, prompt templates, few-shot, output checks',
    ];
}

function pe_flow(string $title, array $steps, string $caption): string
{
    $steps = array_values($steps);
    $n = max(1, min(4, count($steps)));
    $boxW = 148;
    $gap = 36;
    $pad = 16;
    $w = $pad * 2 + $n * $boxW + ($n - 1) * $gap;
    $colors = ['#6d28d9', '#0f766e', '#b45309', '#1d4ed8'];
    $body = '';
    for ($i = 0; $i < $n; $i++) {
        $label = (string)$steps[$i];
        $x = $pad + $i * ($boxW + $gap);
        $c = $colors[$i];
        $body .= '<rect x="' . $x . '" y="28" width="' . $boxW . '" height="70" rx="12" fill="#faf8ff" stroke="' . $c . '" stroke-width="2"/>';
        $short = function_exists('mb_substr') ? mb_substr($label, 0, 16) : substr($label, 0, 16);
        $body .= '<text x="' . ($x + (int)($boxW / 2)) . '" y="68" text-anchor="middle" fill="#1e1b4b" font-size="13" font-weight="700" font-family="sans-serif">' . sh_esc($short) . '</text>';
        if ($i < $n - 1) {
            $x1 = $x + $boxW + 4;
            $x2 = $x + $boxW + $gap - 10;
            $body .= '<line x1="' . $x1 . '" y1="63" x2="' . $x2 . '" y2="63" stroke="#94a3b8" stroke-width="2"/>';
            $body .= '<polygon points="' . $x2 . ',63 ' . ($x2 - 8) . ',58 ' . ($x2 - 8) . ',68" fill="#94a3b8"/>';
        }
    }
    $svg = '<svg viewBox="0 0 ' . max(680, $w) . ' 118" class="w-full h-auto" role="img" aria-label="' . sh_esc($title) . '">' . $body . '</svg>';
    return '<figure class="my-7 bg-white rounded-2xl p-5 border border-slate-200 shadow-sm overflow-x-auto" data-nectra-figure="1">'
        . '<div class="flex items-center justify-between gap-2 mb-3"><span class="text-xs font-black uppercase tracking-wider text-slate-800">' . sh_esc($title) . '</span><span class="text-[11px] font-semibold text-violet-700 bg-violet-50 border border-violet-200 px-2.5 py-0.5 rounded-full">Picture</span></div>'
        . $svg
        . '<figcaption class="text-xs text-slate-500 mt-2 border-t border-slate-100 pt-2">' . sh_inline($caption) . '</figcaption>'
        . '</figure>';
}

function pe_scene(string $section): array
{
    return match ($section) {
        'The parts of a prompt' => ['Task', 'Context', 'Format', 'Limit'],
        'Everyday moves' => ['Input', 'Move', 'Draft', 'Review'],
        'ChatGPT, Claude, and Gemini' => ['ChatGPT', 'Claude', 'Gemini', 'Compare'],
        'Work you can paste' => ['Brief', 'Paste', 'Draft', 'Edit'],
        'Automation' => ['Row', 'Prompt', 'Check', 'Human'],
        'Trust and a library' => ['Test', 'Golden', 'Change', 'Keep'],
        default => ['You', 'Words', 'Model', 'Check'],
    };
}

function pe_prompt_html(string $section, string $prompt, string $tip, string $sample): string
{
    $full = "Use this in ChatGPT, Claude, or Gemini.\nReplace every [bracket] with your own text. Do not include passwords, private keys, or another person's private data.\n\n" . trim($prompt);
    $id = 'p' . substr(md5($full), 0, 12);
    $firstAsk = trim((string)strtok(trim($prompt), "\n"));
    $html = pe_flow('How this paste moves', pe_scene($section), 'Same path in every tool. The words in the box are what you paste.');
    $html .= '<p class="prompt-reply-kicker">What the chat may reply</p>';
    $html .= '<div class="prompt-reply">'
        . '<div class="prompt-bubble you"><strong>You</strong>' . sh_esc($firstAsk) . '</div>'
        . '<div class="prompt-bubble model"><strong>Reply</strong>' . sh_esc(trim($sample)) . '</div>'
        . '</div>';
    $html .= '<div class="prompt-lab">'
        . '<div class="prompt-lab-top"><span>Copy, then paste into a new chat</span>'
        . '<button type="button" class="prompt-copy" data-prompt-id="' . $id . '">Copy prompt</button></div>'
        . '<div class="prompt-keep" id="' . $id . '">' . sh_esc($full) . '</div>'
        . '<ul class="prompt-models">'
        . '<li><strong>ChatGPT.</strong> New chat. Leave custom instructions off for this test. ' . sh_esc($tip) . '</li>'
        . '<li><strong>Claude.</strong> New chat. Do not attach a project yet. Paste the same prompt. ' . sh_esc($tip) . '</li>'
        . '<li><strong>Gemini.</strong> New chat. Do not add a Drive file yet. Paste the same prompt. ' . sh_esc($tip) . '</li>'
        . '</ul></div>';
    return $html;
}

function pe_pieces(array $rows): string
{
    $html = '<table class="prompt-pieces"><thead><tr><th>Piece</th><th>Plain meaning</th><th>What people skip</th></tr></thead><tbody>';
    foreach ($rows as $row) {
        $html .= '<tr><td>' . sh_esc((string)($row[0] ?? '')) . '</td><td>' . sh_esc((string)($row[1] ?? '')) . '</td><td>' . sh_esc((string)($row[2] ?? '')) . '</td></tr>';
    }
    return $html . '</tbody></table>';
}

function pe_row(
    string $section,
    string $title,
    string $lead,
    string $heading,
    string $para,
    array $bullets,
    array $rows,
    string $prompt,
    string $sample,
    string $tip,
    string $practice,
    string $q,
    string $a,
    string $b,
    string $c,
    string $d
): array {
    return [
        'section' => $section,
        'title' => $title,
        'keywords' => strtolower($title) . ', prompt engineering, chatgpt, claude, gemini',
        'lead' => $lead,
        'sections' => [[
            'heading' => $heading,
            'paragraphs' => [$para],
            'bullets' => $bullets,
        ]],
        'visual_html' => pe_prompt_html($section, $prompt, $tip, $sample) . pe_pieces($rows),
        'table' => ['title' => '', 'headers' => [], 'rows' => []],
        'micro_points' => [
            'Run it once in ChatGPT, once in Claude, and once in Gemini before you trust a difference.',
            'If the answer uses a fact you did not type, mark that line and ask where it came from.',
            'Save the prompt text, the date, and one line about what you changed.',
        ],
        'quiz' => [[
            'question' => $q,
            'answer' => $a,
            'options' => [$a, $b, $c, $d],
        ]],
        'practice' => $practice,
    ];
}

$chapters = [];
foreach (pe_lesson_rows() as $row) {
    $chapters[] = pe_row(...$row);
}

foreach ($chapters as $i => $ch) {
    $n = $i + 1;
    if (trim($ch['lead']) === '' || trim(strip_tags($ch['visual_html'])) === '' || count($ch['sections'][0]['bullets']) < 4) {
        fwrite(STDERR, "Thin lesson {$n}: {$ch['title']}\n");
        exit(1);
    }
    if (!str_contains($ch['visual_html'], 'prompt-keep') || !str_contains($ch['visual_html'], 'class="prompt-copy"')) {
        fwrite(STDERR, "Missing prompt box {$n}: {$ch['title']}\n");
        exit(1);
    }
    if (str_contains($ch['visual_html'], '<pre') || str_contains($ch['visual_html'], 'language-') || str_contains($ch['visual_html'], 'tutorial-output')) {
        fwrite(STDERR, "Code-style result in lesson {$n}: {$ch['title']}\n");
        exit(1);
    }
}

if (count($chapters) < 100) {
    fwrite(STDERR, 'Need at least 100 lessons, got ' . count($chapters) . "\n");
    exit(1);
}

$titles = array_map(static fn(array $ch): string => $ch['title'], $chapters);
if (count($titles) !== count(array_unique($titles))) {
    fwrite(STDERR, "Duplicate lesson titles\n");
    exit(1);
}

track_seed($pdo, pe_meta(), $chapters);
track_refresh($pdo, 'prompt-engineering', $chapters);
track_place($pdo, 'prompt-engineering', $titles);
track_sections($pdo, 'prompt-engineering', [
    10 => 'Start here',
    22 => 'The parts of a prompt',
    40 => 'Everyday moves',
    52 => 'ChatGPT, Claude, and Gemini',
    74 => 'Work you can paste',
    96 => 'Automation',
    108 => 'Trust and a library',
]);
echo '[PROMPT] lessons ' . count($chapters) . "\n";
