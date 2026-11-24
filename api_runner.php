<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/security.php';

header('Cache-Control: no-store');
json_response([
    'status' => 'error',
    'output' => 'Server-side code execution is disabled. Run this example in a local development environment.',
], 410);
