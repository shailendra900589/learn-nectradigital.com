<?php
declare(strict_types=1);

function bquiz(string $q, string $a, string $b, string $c, string $d): array
{
    return ['question' => $q, 'answer' => $a, 'options' => [$a, $b, $c, $d]];
}

function blesson(array $c, string $lang): array
{
    $examples = [];
    foreach ($c['examples'] as $ex) {
        $examples[] = [
            'title' => $ex[0],
            'file' => $ex[1],
            'lang' => $lang,
            'desc' => $ex[2],
            'code' => $ex[3],
        ];
    }
    return [
        'section' => $c['section'],
        'title' => $c['title'],
        'keywords' => $c['keywords'],
        'lead' => $c['lead'],
        'sections' => $c['sections'],
        'visual_html' => $c['visual'] ?? '',
        'table' => [
            'title' => $c['title'] . ' — every small piece',
            'headers' => ['Piece', 'What it means in plain words', 'The tiny detail beginners miss'],
            'rows' => $c['rows'],
        ],
        'examples' => $examples,
        'micro_points' => $c['micro'],
        'quiz' => $c['quiz'],
        'practice' => $c['practice'],
    ];
}
