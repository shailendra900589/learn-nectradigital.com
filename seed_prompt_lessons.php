<?php
declare(strict_types=1);

require_once __DIR__ . '/seed_prompt_part1.php';
require_once __DIR__ . '/seed_prompt_part2.php';
require_once __DIR__ . '/seed_prompt_part3.php';

function pe_lesson_rows(): array
{
    $raw = trim(pe_part1() . "\n" . pe_part2() . "\n" . pe_part3());
    $raw = preg_replace('/^@@\r?\n/', '', $raw) ?? $raw;
    $blocks = preg_split("/\r?\n@@\r?\n/", $raw) ?: [];
    $rows = [];
    foreach ($blocks as $block) {
        $block = trim($block);
        if ($block === '') {
            continue;
        }
        $rows[] = pe_parse_block($block);
    }
    return $rows;
}

function pe_parse_block(string $block): array
{
    $lines = preg_split("/\r?\n/", $block) ?: [];
    $section = trim((string)array_shift($lines));
    $title = trim((string)array_shift($lines));
    $mode = 'lead';
    $bag = [
        'lead' => [],
        'head' => [],
        'para' => [],
        'bullets' => [],
        'rows' => [],
        'prompt' => [],
        'sample' => [],
        'tip' => [],
        'practice' => [],
        'q' => [],
        'a' => [],
        'b' => [],
        'c' => [],
        'd' => [],
    ];
    $map = [
        'LEAD' => 'lead',
        'HEAD' => 'head',
        'PARA' => 'para',
        'BULLETS' => 'bullets',
        'ROWS' => 'rows',
        'PROMPT' => 'prompt',
        'SAMPLE' => 'sample',
        'TIP' => 'tip',
        'PRACTICE' => 'practice',
        'Q' => 'q',
        'A' => 'a',
        'B' => 'b',
        'C' => 'c',
        'D' => 'd',
    ];
    foreach ($lines as $line) {
        $tag = trim($line);
        if (isset($map[$tag])) {
            $mode = $map[$tag];
            continue;
        }
        if ($mode === 'bullets') {
            $bag['bullets'][] = ltrim($line, '- ');
            continue;
        }
        if ($mode === 'rows') {
            $parts = array_map('trim', explode('|', $line));
            $bag['rows'][] = [$parts[0] ?? '', $parts[1] ?? '', $parts[2] ?? ''];
            continue;
        }
        $bag[$mode][] = $line;
    }
    $join = static fn(array $bits): string => trim(implode("\n", $bits));
    return [
        $section,
        $title,
        $join($bag['lead']),
        $join($bag['head']),
        $join($bag['para']),
        $bag['bullets'],
        $bag['rows'],
        $join($bag['prompt']),
        $join($bag['sample']),
        $join($bag['tip']),
        $join($bag['practice']),
        $join($bag['q']),
        $join($bag['a']),
        $join($bag['b']),
        $join($bag['c']),
        $join($bag['d']),
    ];
}
