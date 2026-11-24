<?php
declare(strict_types=1);

/**
 * Appends an advanced API track onto existing programming courses.
 * Never deletes a chapter. A title that already exists is skipped.
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/seed_helper.php';
require_once __DIR__ . '/includes/beginner_lesson.php';

function api_append(PDO $pdo, string $slug, array $chapters): int
{
    $course = $pdo->prepare('SELECT id, description FROM courses WHERE slug = ? LIMIT 1');
    $course->execute([$slug]);
    $row = $course->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        echo "[SKIP] missing course {$slug}\n";
        return 0;
    }
    $courseId = (int)$row['id'];
    $have = $pdo->prepare('SELECT chapter_name FROM chapters WHERE course_id = ?');
    $have->execute([$courseId]);
    $names = array_flip(array_column($have->fetchAll(PDO::FETCH_ASSOC), 'chapter_name'));
    $max = (int)$pdo->query('SELECT COALESCE(MAX(order_index), 0) FROM chapters WHERE course_id = ' . $courseId)->fetchColumn();

    $ins = $pdo->prepare('INSERT INTO chapters (course_id, order_index, section_name, chapter_name, content, keywords, quiz_json, practice_content, is_premium, require_quiz_pass) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0)');
    $added = 0;
    foreach ($chapters as $ch) {
        if (isset($names[$ch['title']])) {
            continue;
        }
        $max++;
        $html = build_rich_chapter_html($ch);
        $quiz = !empty($ch['quiz']) ? json_encode($ch['quiz'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;
        $ins->execute([$courseId, $max, $ch['section'], $ch['title'], $html, $ch['keywords'], $quiz, $ch['practice'] ?? null]);
        $added++;
    }

    $suffix = ' Advanced track kept in place after the original lessons: HTTP APIs, status codes, headers, JSON errors, CRUD, pagination, tokens, CORS, uploads, webhooks, idempotency, versioning, and production checks. No earlier chapter was removed.';
    if ($added > 0 && !str_contains((string)$row['description'], 'No earlier chapter was removed')) {
        $upd = $pdo->prepare('UPDATE courses SET description = CONCAT(description, ?) WHERE id = ?');
        $upd->execute([$suffix, $courseId]);
    }
    echo "[APPEND] {$slug} +{$added} (course {$courseId})\n";
    return $added;
}

function api_pack(string $kind): array
{
    $jsx = static function (string $body): string {
        $open = chr(123);
        $close = chr(125);
        return "function App() \x7b\n  return (\n    <main style=" . $open . $open
            . "fontFamily:'sans-serif',padding:16" . $close . $close . ">\n      "
            . $body . "\n    </main>\n  );\n\x7d\nconst root = ReactDOM.createRoot(document.getElementById('root'));\nroot.render(<App />);";
    };
    $ng = static function (string $logic, string $view): string {
        $view = str_replace(['[[', ']]'], [chr(123).chr(123), chr(125).chr(125)], $view);
        return "<div ng-app=\"apiApp\" ng-controller=\"ApiCtrl\">\n" . $view
            . "\n</div>\n<script>\nangular.module('apiApp', []).controller('ApiCtrl', function(\$scope) \x7b\n"
            . $logic . "\n\x7d);\n</script>";
    };

    return [
        'contract' => match ($kind) {
            'php' => [
                ["<?php\n\$request = ['method' => 'POST', 'path' => '/api/v1/notes', 'content_type' => 'application/json'];\n\$body = ['title' => 'Buy milk'];\necho \$request['method'] . ' ' . \$request['path'] . \"\\n\";\necho 'Header Content-Type: ' . \$request['content_type'] . \"\\n\";\necho 'JSON body: ' . json_encode(\$body);", "<?php\nfunction answer(string \$method, string \$path): string \x7b\n    if (\$method === 'GET' && \$path === '/api/v1/notes/4') \x7b\n        return \"200\\n\" . json_encode(['id' => 4, 'title' => 'Buy milk']);\n    \x7d\n    return '404 no route';\n\x7d\necho answer('GET', '/api/v1/notes/4') . \"\\n\";\necho answer('GET', '/api/v1/note');"],
            ],
            'py' => [
                ["import json\nrequest = \x7b'method': 'POST', 'path': '/api/v1/notes', 'content_type': 'application/json'\x7d\nbody = \x7b'title': 'Buy milk'\x7d\nprint(request['method'], request['path'])\nprint('Header Content-Type:', request['content_type'])\nprint('JSON body:', json.dumps(body))", "import json\ndef answer(method, path):\n    if method == 'GET' and path == '/api/v1/notes/4':\n        return '200\\n' + json.dumps(\x7b'id': 4, 'title': 'Buy milk'\x7d)\n    return '404 no route'\nprint(answer('GET', '/api/v1/notes/4'))\nprint(answer('GET', '/api/v1/note'))"],
            ],
            'js' => [
                ["const request = \x7b method: 'POST', path: '/api/v1/notes', contentType: 'application/json' \x7d;\nconst body = \x7b title: 'Buy milk' \x7d;\nconsole.log(request.method + ' ' + request.path);\nconsole.log('Header Content-Type: ' + request.contentType);\nconsole.log('JSON body: ' + JSON.stringify(body));", "function answer(method, path) \x7b\n  if (method === 'GET' && path === '/api/v1/notes/4') \x7b\n    return '200\\n' + JSON.stringify(\x7b id: 4, title: 'Buy milk' \x7d);\n  \x7d\n  return '404 no route';\n\x7d\nconsole.log(answer('GET', '/api/v1/notes/4'));\nconsole.log(answer('GET', '/api/v1/note'));"],
            ],
            'jsx' => [
                [$jsx("<h1>POST /api/v1/notes</h1>\n      <p>Content-Type: application/json</p>\n      <p>Body title: Buy milk</p>\n      <p>The page is HTML. The API answer is JSON.</p>"), $jsx("<h1>Route match</h1>\n      <p>GET /api/v1/notes/4 → 200 Buy milk</p>\n      <p>GET /api/v1/note → 404 no route</p>\n      <p>One missing letter is a different path.</p>")],
            ],
            'ng' => [
                [$ng("\$scope.method = 'POST';\n\$scope.path = '/api/v1/notes';\n\$scope.type = 'application/json';\n\$scope.title = 'Buy milk';", "  <h1>[[ method ]] [[ path ]]</h1>\n  <p>Content-Type: [[ type ]]</p>\n  <p>Body title: [[ title ]]</p>"), $ng("function answer(method, path) \x7b\n  if (method === 'GET' && path === '/api/v1/notes/4') return '200 Buy milk';\n  return '404 no route';\n\x7d\n\$scope.hit = answer('GET', '/api/v1/notes/4');\n\$scope.miss = answer('GET', '/api/v1/note');", "  <p>[[ hit ]]</p>\n  <p>[[ miss ]]</p>")],
            ],
            'dart' => [
                ["print('POST /api/v1/notes');\nprint('Header Content-Type: application/json');\nprint('JSON body title: Buy milk');", "print('GET /api/v1/notes/4 -> 200 Buy milk');\nprint('GET /api/v1/note -> 404 no route');"],
            ],
        },
        'methods' => match ($kind) {
            'php' => [
                ["<?php\n\$rules = [\n    'GET' => 'safe to retry, must not save',\n    'POST' => 'creates, a retry can create a second row',\n    'PUT' => 'replaces the whole resource, retry is safe',\n    'PATCH' => 'changes only the fields you send',\n    'DELETE' => 'removes, a second delete finds nothing',\n];\nforeach (\$rules as \$verb => \$rule) \x7b\n    echo \$verb . ': ' . \$rule . \"\\n\";\n\x7d", "<?php\nfunction saveTitle(string \$method, array \$note, ?string \$title): array \x7b\n    if (\$method === 'GET') return \$note;\n    if (\$method === 'PUT') return ['id' => \$note['id'], 'title' => \$title ?? '', 'body' => ''];\n    if (\$method === 'PATCH' && \$title !== null) \$note['title'] = \$title;\n    return \$note;\n\x7d\n\$note = ['id' => 4, 'title' => 'Buy milk', 'body' => '2 litres'];\necho json_encode(saveTitle('PATCH', \$note, 'Call mom')) . \"\\n\";\necho json_encode(saveTitle('PUT', \$note, 'Call mom'));"],
            ],
            'py' => [
                ["rules = \x7b\n    'GET': 'safe to retry, must not save',\n    'POST': 'creates, a retry can create a second row',\n    'PUT': 'replaces the whole resource, retry is safe',\n    'PATCH': 'changes only the fields you send',\n    'DELETE': 'removes, a second delete finds nothing',\n\x7d\nfor verb, rule in rules.items():\n    print(f'\x7bverb\x7d: \x7brule\x7d')", "def save_title(method, note, title):\n    if method == 'GET':\n        return note\n    if method == 'PUT':\n        return \x7b'id': note['id'], 'title': title or '', 'body': ''\x7d\n    if method == 'PATCH' and title is not None:\n        note = dict(note)\n        note['title'] = title\n    return note\nnote = \x7b'id': 4, 'title': 'Buy milk', 'body': '2 litres'\x7d\nprint(save_title('PATCH', note, 'Call mom'))\nprint(save_title('PUT', note, 'Call mom'))"],
            ],
            'js' => [
                ["const rules = \x7b\n  GET: 'safe to retry, must not save',\n  POST: 'creates, a retry can create a second row',\n  PUT: 'replaces the whole resource, retry is safe',\n  PATCH: 'changes only the fields you send',\n  DELETE: 'removes, a second delete finds nothing'\n\x7d;\nObject.keys(rules).forEach(function (verb) \x7b\n  console.log(verb + ': ' + rules[verb]);\n\x7d);", "function saveTitle(method, note, title) \x7b\n  if (method === 'GET') return note;\n  if (method === 'PUT') return \x7b id: note.id, title: title || '', body: '' \x7d;\n  if (method === 'PATCH' && title != null) return Object.assign(\x7b\x7d, note, \x7b title: title \x7d);\n  return note;\n\x7d\nconst note = \x7b id: 4, title: 'Buy milk', body: '2 litres' \x7d;\nconsole.log(JSON.stringify(saveTitle('PATCH', note, 'Call mom')));\nconsole.log(JSON.stringify(saveTitle('PUT', note, 'Call mom')));"],
            ],
            'jsx' => [
                [$jsx("<h1>Verbs</h1>\n      <ul>\n        <li>GET — show, retry is safe</li>\n        <li>POST — create, retry can duplicate</li>\n        <li>PUT — replace every field</li>\n        <li>PATCH — change only title</li>\n        <li>DELETE — remove, never from a link</li>\n      </ul>"), $jsx("<h1>PATCH keeps the body</h1>\n      <p>Before: title Buy milk, body 2 litres</p>\n      <p>PATCH title → Call mom, body still 2 litres</p>\n      <p>PUT title → Call mom, body becomes empty</p>")],
            ],
            'ng' => [
                [$ng("\$scope.rows = [\n  \x7b verb: 'GET', rule: 'safe to retry, must not save' \x7d,\n  \x7b verb: 'POST', rule: 'retry can create a second row' \x7d,\n  \x7b verb: 'PUT', rule: 'replaces the whole resource' \x7d,\n  \x7b verb: 'PATCH', rule: 'changes only sent fields' \x7d,\n  \x7b verb: 'DELETE', rule: 'a link must never do this' \x7d\n];", "  <p ng-repeat=\"row in rows\">[[ row.verb ]] — [[ row.rule ]]</p>"), $ng("\$scope.patch = \x7b title: 'Call mom', body: '2 litres' \x7d;\n\$scope.put = \x7b title: 'Call mom', body: '' \x7d;", "  <p>PATCH keeps body: [[ patch.body ]]</p>\n  <p>PUT clears body: '[[ put.body ]]'</p>")],
            ],
            'dart' => [
                ["print('GET: safe to retry, must not save');\nprint('POST: a retry can create a second row');\nprint('PUT: replaces the whole resource');\nprint('PATCH: changes only the fields you send');\nprint('DELETE: never fire this from a link');", "print('PATCH title -> Call mom, body stays 2 litres');\nprint('PUT title -> Call mom, body becomes empty');"],
            ],
        },
        'urls' => match ($kind) {
            'php' => [
                ["<?php\n\$good = ['/api/v1/notes', '/api/v1/notes/4', '/api/v1/notes?status=open&limit=20'];\n\$bad = ['/api/v1/getNote?id=4', '/api/v1/notes/4?token=secret'];\necho \"Use nouns:\\n- \" . implode(\"\\n- \", \$good) . \"\\n\";\necho \"Avoid:\\n- \" . implode(\"\\n- \", \$bad);", "<?php\n\$allowed = ['created_at', 'title'];\n\$asked = 'title; DROP TABLE notes';\n\$column = in_array(\$asked, \$allowed, true) ? \$asked : 'created_at';\necho 'Sort column used: ' . \$column . \"\\n\";\necho 'The semicolon text was not a column, so it was rejected.';"],
            ],
            'py' => [
                ["good = ['/api/v1/notes', '/api/v1/notes/4', '/api/v1/notes?status=open&limit=20']\nbad = ['/api/v1/getNote?id=4', '/api/v1/notes/4?token=secret']\nprint('Use nouns:')\nfor item in good:\n    print('-', item)\nprint('Avoid:')\nfor item in bad:\n    print('-', item)", "allowed = \x7b'created_at', 'title'\x7d\nasked = 'title; DROP TABLE notes'\ncolumn = asked if asked in allowed else 'created_at'\nprint('Sort column used:', column)\nprint('The semicolon text was not a column, so it was rejected.')"],
            ],
            'js' => [
                ["const good = ['/api/v1/notes', '/api/v1/notes/4', '/api/v1/notes?status=open&limit=20'];\nconst bad = ['/api/v1/getNote?id=4', '/api/v1/notes/4?token=secret'];\nconsole.log('Use nouns:\\n- ' + good.join('\\n- '));\nconsole.log('Avoid:\\n- ' + bad.join('\\n- '));", "const allowed = ['created_at', 'title'];\nconst asked = 'title; DROP TABLE notes';\nconst column = allowed.indexOf(asked) >= 0 ? asked : 'created_at';\nconsole.log('Sort column used: ' + column);\nconsole.log('The semicolon text was not a column, so it was rejected.');"],
            ],
            'jsx' => [
                [$jsx("<h1>Nouns, not verbs</h1>\n      <p>/api/v1/notes/4</p>\n      <p>Query is only a filter: ?status=open&amp;limit=20</p>\n      <p>A token in the query string gets copied into logs.</p>"), $jsx("<h1>Sort whitelist</h1>\n      <p>Asked: title; DROP TABLE notes</p>\n      <p>Used: created_at</p>\n      <p>Unknown sort fields are ignored.</p>")],
            ],
            'ng' => [
                [$ng("\$scope.good = '/api/v1/notes/4';\n\$scope.query = '?status=open&limit=20';", "  <p>Path: [[ good ]]</p>\n  <p>Filter: [[ query ]]</p>"), $ng("var allowed = ['created_at', 'title'];\nvar asked = 'title; DROP TABLE notes';\n\$scope.column = allowed.indexOf(asked) >= 0 ? asked : 'created_at';", "  <p>Sort column used: [[ column ]]</p>")],
            ],
            'dart' => [
                ["print('Use /api/v1/notes/4');\nprint('Filter with ?status=open&limit=20');\nprint('Do not put a token in the query string');", "print('Asked sort: title; DROP TABLE notes');\nprint('Sort column used: created_at');"],
            ],
        },
        'status' => match ($kind) {
            'php' => [
                ["<?php\nfunction statusFor(string \$method, bool \$valid, bool \$found, bool \$mine): int \x7b\n    if (!\$valid) return 422;\n    if (\$method === 'POST') return 201;\n    if (!\$found) return 404;\n    if (!\$mine) return 403;\n    if (\$method === 'DELETE') return 204;\n    return 200;\n\x7d\necho 'blank title POST ' . statusFor('POST', false, false, true) . \"\\n\";\necho 'new note POST ' . statusFor('POST', true, false, true) . \"\\n\";\necho 'missing GET ' . statusFor('GET', true, false, true) . \"\\n\";\necho 'someone else PATCH ' . statusFor('PATCH', true, true, false) . \"\\n\";\necho 'delete mine ' . statusFor('DELETE', true, true, true);", "<?php\n\$code = 500;\n\$public = \$code >= 500 ? ['error' => 'Something went wrong', 'request_id' => 'req_9f2'] : ['error' => 'Not found'];\necho json_encode(\$public) . \"\\n\";\necho 'The stack trace stays in the server log, next to request_id.';"],
            ],
            'py' => [
                ["def status_for(method, valid, found, mine):\n    if not valid:\n        return 422\n    if method == 'POST':\n        return 201\n    if not found:\n        return 404\n    if not mine:\n        return 403\n    if method == 'DELETE':\n        return 204\n    return 200\nprint('blank title POST', status_for('POST', False, False, True))\nprint('new note POST', status_for('POST', True, False, True))\nprint('missing GET', status_for('GET', True, False, True))\nprint('someone else PATCH', status_for('PATCH', True, True, False))\nprint('delete mine', status_for('DELETE', True, True, True))", "public = \x7b'error': 'Something went wrong', 'request_id': 'req_9f2'\x7d\nprint(public)\nprint('The stack trace stays in the server log, next to request_id.')"],
            ],
            'js' => [
                ["function statusFor(method, valid, found, mine) \x7b\n  if (!valid) return 422;\n  if (method === 'POST') return 201;\n  if (!found) return 404;\n  if (!mine) return 403;\n  if (method === 'DELETE') return 204;\n  return 200;\n\x7d\nconsole.log('blank title POST ' + statusFor('POST', false, false, true));\nconsole.log('new note POST ' + statusFor('POST', true, false, true));\nconsole.log('missing GET ' + statusFor('GET', true, false, true));\nconsole.log('someone else PATCH ' + statusFor('PATCH', true, true, false));\nconsole.log('delete mine ' + statusFor('DELETE', true, true, true));", "const publicBody = \x7b error: 'Something went wrong', request_id: 'req_9f2' \x7d;\nconsole.log(JSON.stringify(publicBody));\nconsole.log('The stack trace stays in the server log, next to request_id.');"],
            ],
            'jsx' => [
                [$jsx("<h1>Pick the status</h1>\n      <p>Blank title → 422</p>\n      <p>Created → 201</p>\n      <p>Missing → 404</p>\n      <p>Not your note → 403</p>\n      <p>Deleted → 204 with an empty body</p>"), $jsx("<h1>500 stays vague</h1>\n      <p>Client sees: Something went wrong</p>\n      <p>request_id: req_9f2</p>\n      <p>The file path and SQL stay in the log.</p>")],
            ],
            'ng' => [
                [$ng("function statusFor(method, valid, found, mine) \x7b\n  if (!valid) return 422;\n  if (method === 'POST') return 201;\n  if (!found) return 404;\n  if (!mine) return 403;\n  if (method === 'DELETE') return 204;\n  return 200;\n\x7d\n\$scope.blank = statusFor('POST', false, false, true);\n\$scope.created = statusFor('POST', true, false, true);\n\$scope.missing = statusFor('GET', true, false, true);\n\$scope.forbidden = statusFor('PATCH', true, true, false);\n\$scope.deleted = statusFor('DELETE', true, true, true);", "  <p>Blank [[ blank ]] · Created [[ created ]] · Missing [[ missing ]]</p>\n  <p>Not yours [[ forbidden ]] · Deleted [[ deleted ]]</p>"), $ng("\$scope.error = 'Something went wrong';\n\$scope.requestId = 'req_9f2';", "  <p>[[ error ]]</p>\n  <p>request_id [[ requestId ]]</p>")],
            ],
            'dart' => [
                ["print('blank title POST 422');\nprint('new note POST 201');\nprint('missing GET 404');\nprint('someone else PATCH 403');\nprint('delete mine 204');", "print('Client sees: Something went wrong');\nprint('request_id: req_9f2');"],
            ],
        },
        'headers' => match ($kind) {
            'php' => [
                ["<?php\n\$headers = [\n    'Content-Type' => 'application/json; charset=utf-8',\n    'Accept' => 'application/json',\n    'Authorization' => 'Bearer note_tok_31',\n    'Idempotency-Key' => 'create-note-8f3a',\n];\nforeach (\$headers as \$name => \$value) echo \$name . ': ' . \$value . \"\\n\";", "<?php\n\$etag = '\"note-4-v2\"';\n\$ifNoneMatch = '\"note-4-v2\"';\nif (\$ifNoneMatch === \$etag) \x7b\n    echo \"304\\n\";\n    echo 'Body is empty. The client keeps its cached note.';\n\x7d else \x7b\n    echo \"200\\n\";\n    echo json_encode(['id' => 4, 'title' => 'Buy milk']);\n\x7d"],
            ],
            'py' => [
                ["headers = \x7b\n    'Content-Type': 'application/json; charset=utf-8',\n    'Accept': 'application/json',\n    'Authorization': 'Bearer note_tok_31',\n    'Idempotency-Key': 'create-note-8f3a',\n\x7d\nfor name, value in headers.items():\n    print(f'\x7bname\x7d: \x7bvalue\x7d')", "etag = '\"note-4-v2\"'\nif_none_match = '\"note-4-v2\"'\nif if_none_match == etag:\n    print(304)\n    print('Body is empty. The client keeps its cached note.')\nelse:\n    print(200)"],
            ],
            'js' => [
                ["const headers = \x7b\n  'Content-Type': 'application/json; charset=utf-8',\n  Accept: 'application/json',\n  Authorization: 'Bearer note_tok_31',\n  'Idempotency-Key': 'create-note-8f3a'\n\x7d;\nObject.keys(headers).forEach(function (name) \x7b\n  console.log(name + ': ' + headers[name]);\n\x7d);", "const etag = '\"note-4-v2\"';\nconst ifNoneMatch = '\"note-4-v2\"';\nif (ifNoneMatch === etag) \x7b\n  console.log('304');\n  console.log('Body is empty. The client keeps its cached note.');\n\x7d else \x7b\n  console.log('200');\n\x7d"],
            ],
            'jsx' => [
                [$jsx("<h1>Headers on the create call</h1>\n      <p>Content-Type: application/json; charset=utf-8</p>\n      <p>Accept: application/json</p>\n      <p>Authorization: Bearer note_tok_31</p>\n      <p>Idempotency-Key: create-note-8f3a</p>"), $jsx("<h1>Same ETag</h1>\n      <p>If-None-Match matches \"note-4-v2\"</p>\n      <p>Answer: 304 and an empty body</p>")],
            ],
            'ng' => [
                [$ng("\$scope.rows = [\n  'Content-Type: application/json; charset=utf-8',\n  'Accept: application/json',\n  'Authorization: Bearer note_tok_31',\n  'Idempotency-Key: create-note-8f3a'\n];", "  <p ng-repeat=\"row in rows\">[[ row ]]</p>"), $ng("\$scope.code = 304;\n\$scope.note = 'cached copy stays';", "  <p>HTTP [[ code ]] — [[ note ]]</p>")],
            ],
            'dart' => [
                ["print('Content-Type: application/json; charset=utf-8');\nprint('Accept: application/json');\nprint('Authorization: Bearer note_tok_31');\nprint('Idempotency-Key: create-note-8f3a');", "print('If-None-Match matches the ETag');\nprint('304 and an empty body');"],
            ],
        },
        'json' => match ($kind) {
            'php' => [
                ["<?php\n\$raw = '\x7b\"title\":\"Buy milk\",\"done\":false,\"tags\":[\"shop\"]\x7d';\n\$data = json_decode(\$raw, true);\necho 'title is a ' . gettype(\$data['title']) . \"\\n\";\necho 'done is a ' . gettype(\$data['done']) . ' and the value is ' . (\$data['done'] ? 'true' : 'false') . \"\\n\";\necho 'tags[0] is ' . \$data['tags'][0];", "<?php\nfunction validate(array \$input): array \x7b\n    \$errors = [];\n    \$title = trim((string)(\$input['title'] ?? ''));\n    if (\$title === '') \$errors[] = ['field' => 'title', 'message' => 'A title is required.'];\n    if (strlen(\$title) > 120) \$errors[] = ['field' => 'title', 'message' => 'Use 120 characters or fewer.'];\n    if (array_key_exists('is_admin', \$input)) \$errors[] = ['field' => 'is_admin', 'message' => 'You cannot set this field.'];\n    return \$errors;\n\x7d\necho json_encode(validate(['title' => '  ', 'is_admin' => true]), JSON_PRETTY_PRINT);"],
            ],
            'py' => [
                ["import json\nraw = '\x7b\"title\":\"Buy milk\",\"done\":false,\"tags\":[\"shop\"]\x7d'\ndata = json.loads(raw)\nprint('title is', type(data['title']).__name__)\nprint('done is', type(data['done']).__name__, 'and the value is', data['done'])\nprint('tags[0] is', data['tags'][0])", "def validate(payload):\n    errors = []\n    title = str(payload.get('title', '')).strip()\n    if title == '':\n        errors.append(\x7b'field': 'title', 'message': 'A title is required.'\x7d)\n    if len(title) > 120:\n        errors.append(\x7b'field': 'title', 'message': 'Use 120 characters or fewer.'\x7d)\n    if 'is_admin' in payload:\n        errors.append(\x7b'field': 'is_admin', 'message': 'You cannot set this field.'\x7d)\n    return errors\nprint(validate(\x7b'title': '  ', 'is_admin': True\x7d))"],
            ],
            'js' => [
                ["const raw = '\x7b\"title\":\"Buy milk\",\"done\":false,\"tags\":[\"shop\"]\x7d';\nconst data = JSON.parse(raw);\nconsole.log('title is a ' + typeof data.title);\nconsole.log('done is a ' + typeof data.done + ' and the value is ' + data.done);\nconsole.log('tags[0] is ' + data.tags[0]);", "function validate(input) \x7b\n  const errors = [];\n  const title = String(input.title || '').trim();\n  if (title === '') errors.push(\x7b field: 'title', message: 'A title is required.' \x7d);\n  if (title.length > 120) errors.push(\x7b field: 'title', message: 'Use 120 characters or fewer.' \x7d);\n  if (Object.prototype.hasOwnProperty.call(input, 'is_admin')) \x7b\n    errors.push(\x7b field: 'is_admin', message: 'You cannot set this field.' \x7d);\n  \x7d\n  return errors;\n\x7d\nconsole.log(JSON.stringify(validate(\x7b title: '  ', is_admin: true \x7d), null, 2));"],
            ],
            'jsx' => [
                [$jsx("<h1>Parsed JSON</h1>\n      <p>title is a string: Buy milk</p>\n      <p>done is a boolean: false — not the text \"false\"</p>\n      <p>tags[0] is shop</p>"), $jsx("<h1>422 field errors</h1>\n      <p>title: A title is required.</p>\n      <p>is_admin: You cannot set this field.</p>")],
            ],
            'ng' => [
                [$ng("var data = angular.fromJson('\x7b\"title\":\"Buy milk\",\"done\":false,\"tags\":[\"shop\"]\x7d');\n\$scope.title = data.title;\n\$scope.done = data.done;\n\$scope.tag = data.tags[0];", "  <p>title [[ title ]]</p>\n  <p>done boolean [[ done ]]</p>\n  <p>tag [[ tag ]]</p>"), $ng("\$scope.errors = [\n  \x7b field: 'title', message: 'A title is required.' \x7d,\n  \x7b field: 'is_admin', message: 'You cannot set this field.' \x7d\n];", "  <p ng-repeat=\"err in errors\">[[ err.field ]]: [[ err.message ]]</p>")],
            ],
            'dart' => [
                ["print('title is a string: Buy milk');\nprint('done is a boolean false, not the text false');\nprint('tags first item: shop');", "print('422 title: A title is required.');\nprint('422 is_admin: You cannot set this field.');"],
            ],
        },
        'crud' => match ($kind) {
            'php' => [
                ["<?php\n\$notes = [];\n\$next = 1;\nfunction create(array &\$notes, int &\$next, string \$title): array \x7b\n    \$note = ['id' => \$next++, 'title' => \$title, 'body' => ''];\n    \$notes[] = \$note;\n    return [201, \$note];\n\x7d\n[\$code, \$note] = create(\$notes, \$next, 'Buy milk');\necho \$code . ' ' . json_encode(\$note) . \"\\n\";\necho 'list ' . json_encode(\$notes);", "<?php\n\$notes = [4 => ['id' => 4, 'title' => 'Buy milk', 'body' => '2 litres']];\nfunction patch(array \$notes, int \$id, string \$title): array \x7b\n    if (!isset(\$notes[\$id])) return [404, ['error' => 'Note not found']];\n    \$notes[\$id]['title'] = \$title;\n    return [200, \$notes[\$id]];\n\x7d\n[\$code, \$body] = patch(\$notes, 4, 'Call mom');\necho \$code . ' ' . json_encode(\$body) . \"\\n\";\n[\$missing] = patch(\$notes, 9, 'Nope');\necho \$missing . ' missing id';"],
            ],
            'py' => [
                ["notes = []\ncounter = \x7b'n': 1\x7d\ndef create(title):\n    note = \x7b'id': counter['n'], 'title': title, 'body': ''\x7d\n    counter['n'] += 1\n    notes.append(note)\n    return 201, note\ncode, note = create('Buy milk')\nprint(code, note)\nprint('list', notes)", "notes = \x7b4: \x7b'id': 4, 'title': 'Buy milk', 'body': '2 litres'\x7d\x7d\ndef patch(note_id, title):\n    if note_id not in notes:\n        return 404, \x7b'error': 'Note not found'\x7d\n    notes[note_id]['title'] = title\n    return 200, notes[note_id]\nprint(patch(4, 'Call mom'))\nprint(patch(9, 'Nope')[0], 'missing id')"],
            ],
            'js' => [
                ["const notes = [];\nlet next = 1;\nfunction create(title) \x7b\n  const note = \x7b id: next++, title: title, body: '' \x7d;\n  notes.push(note);\n  return [201, note];\n\x7d\nconst created = create('Buy milk');\nconsole.log(created[0] + ' ' + JSON.stringify(created[1]));\nconsole.log('list ' + JSON.stringify(notes));", "const notes = \x7b 4: \x7b id: 4, title: 'Buy milk', body: '2 litres' \x7d \x7d;\nfunction patch(id, title) \x7b\n  if (!notes[id]) return [404, \x7b error: 'Note not found' \x7d];\n  notes[id].title = title;\n  return [200, notes[id]];\n\x7d\nconst updated = patch(4, 'Call mom');\nconsole.log(updated[0] + ' ' + JSON.stringify(updated[1]));\nconsole.log(patch(9, 'Nope')[0] + ' missing id');"],
            ],
            'jsx' => [
                [$jsx("<h1>201 Created</h1>\n      <p>id 1 · Buy milk · body empty</p>\n      <p>The list now has that one note.</p>"), $jsx("<h1>PATCH then a miss</h1>\n      <p>200 · id 4 · Call mom · body still 2 litres</p>\n      <p>id 9 → 404 Note not found</p>")],
            ],
            'ng' => [
                [$ng("\$scope.code = 201;\n\$scope.note = \x7b id: 1, title: 'Buy milk', body: '' \x7d;", "  <p>HTTP [[ code ]]</p>\n  <p>[[ note.id ]] · [[ note.title ]]</p>"), $ng("\$scope.updated = '200 Call mom, body still 2 litres';\n\$scope.missing = '404 Note not found';", "  <p>[[ updated ]]</p>\n  <p>[[ missing ]]</p>")],
            ],
            'dart' => [
                ["print('201 \x7bid: 1, title: Buy milk, body: empty\x7d');\nprint('list now contains that one note');", "print('200 id 4 title Call mom body still 2 litres');\nprint('404 Note not found for id 9');"],
            ],
        },
        'list' => match ($kind) {
            'php' => [
                ["<?php\n\$all = [\n    ['id' => 1, 'title' => 'Buy milk', 'done' => false],\n    ['id' => 2, 'title' => 'Call mom', 'done' => true],\n    ['id' => 3, 'title' => 'Read chapter', 'done' => false],\n];\n\$open = array_values(array_filter(\$all, fn(\$n) => \$n['done'] === false));\n\$limit = 1;\n\$page = array_slice(\$open, 0, \$limit);\necho json_encode(['data' => \$page, 'limit' => \$limit, 'next' => 2]) ;", "<?php\n\$asked = 500;\n\$max = 50;\n\$limit = max(1, min(\$max, \$asked));\necho 'asked ' . \$asked . ' used ' . \$limit . \"\\n\";\n\$empty = [];\necho 'empty filter is 200 ' . json_encode(\$empty) . ' not 404';"],
            ],
            'py' => [
                ["all_notes = [\n    \x7b'id': 1, 'title': 'Buy milk', 'done': False\x7d,\n    \x7b'id': 2, 'title': 'Call mom', 'done': True\x7d,\n    \x7b'id': 3, 'title': 'Read chapter', 'done': False\x7d,\n]\nopen_notes = [n for n in all_notes if n['done'] is False]\npage = open_notes[:1]\nprint(\x7b'data': page, 'limit': 1, 'next': page[-1]['id'] + 1 if page else None\x7d)", "asked = 500\nlimit = max(1, min(50, asked))\nprint('asked', asked, 'used', limit)\nprint('empty filter is 200', [])"],
            ],
            'js' => [
                ["const all = [\n  \x7b id: 1, title: 'Buy milk', done: false \x7d,\n  \x7b id: 2, title: 'Call mom', done: true \x7d,\n  \x7b id: 3, title: 'Read chapter', done: false \x7d\n];\nconst open = all.filter(function (n) \x7b return n.done === false; \x7d);\nconst page = open.slice(0, 1);\nconsole.log(JSON.stringify(\x7b data: page, limit: 1, next: 3 \x7d));", "const asked = 500;\nconst limit = Math.max(1, Math.min(50, asked));\nconsole.log('asked ' + asked + ' used ' + limit);\nconsole.log('empty filter is 200 [] not 404');"],
            ],
            'jsx' => [
                [$jsx("<h1>Open notes, first page</h1>\n      <p>Buy milk</p>\n      <p>limit 1 · next cursor 3</p>\n      <p>Call mom is done, so it is filtered out.</p>"), $jsx("<h1>Caps</h1>\n      <p>Client asked limit=500</p>\n      <p>Server used 50</p>\n      <p>An empty filter is 200 and an empty list.</p>")],
            ],
            'ng' => [
                [$ng("\$scope.page = [\x7b title: 'Buy milk' \x7d];\n\$scope.next = 3;", "  <p ng-repeat=\"note in page\">[[ note.title ]]</p>\n  <p>next [[ next ]]</p>"), $ng("\$scope.used = 50;\n\$scope.empty = '200 []';", "  <p>limit used [[ used ]]</p>\n  <p>empty filter [[ empty ]]</p>")],
            ],
            'dart' => [
                ["print('page: Buy milk');\nprint('limit 1, next cursor 3');\nprint('done notes were filtered out');", "print('asked limit 500, used 50');\nprint('empty filter is 200 and an empty list');"],
            ],
        },
        'auth' => match ($kind) {
            'php' => [
                ["<?php\nfunction bearer(?string \$header): ?string \x7b\n    if (\$header === null || !str_starts_with(\$header, 'Bearer ')) return null;\n    \$token = trim(substr(\$header, 7));\n    return \$token === '' ? null : \$token;\n\x7d\n\$tokens = ['note_tok_31' => 7];\n\$token = bearer('Bearer note_tok_31');\n\$user = \$token !== null && isset(\$tokens[\$token]) ? \$tokens[\$token] : null;\necho \$user === null ? '401 missing or unknown token' : ('200 user ' . \$user);", "<?php\n\$owner = 7;\n\$actor = 7;\n\$other = 9;\nfunction gate(int \$actor, int \$owner): int \x7b\n    return \$actor === \$owner ? 200 : 403;\n\x7d\necho 'owner ' . gate(\$actor, \$owner) . \"\\n\";\necho 'other ' . gate(\$other, \$owner) . \"\\n\";\necho 'Password stored: hash only, never the typed password.';"],
            ],
            'py' => [
                ["def bearer(header):\n    if not header or not header.startswith('Bearer '):\n        return None\n    token = header[7:].strip()\n    return token or None\ntokens = \x7b'note_tok_31': 7\x7d\ntoken = bearer('Bearer note_tok_31')\nuser = tokens.get(token) if token else None\nprint('401 missing or unknown token' if user is None else f'200 user \x7buser\x7d')", "def gate(actor, owner):\n    return 200 if actor == owner else 403\nprint('owner', gate(7, 7))\nprint('other', gate(9, 7))\nprint('Password stored: hash only, never the typed password.')"],
            ],
            'js' => [
                ["function bearer(header) \x7b\n  if (!header || header.indexOf('Bearer ') !== 0) return null;\n  const token = header.slice(7).trim();\n  return token === '' ? null : token;\n\x7d\nconst tokens = \x7b note_tok_31: 7 \x7d;\nconst token = bearer('Bearer note_tok_31');\nconst user = token && tokens[token] ? tokens[token] : null;\nconsole.log(user === null ? '401 missing or unknown token' : '200 user ' + user);", "function gate(actor, owner) \x7b\n  return actor === owner ? 200 : 403;\n\x7d\nconsole.log('owner ' + gate(7, 7));\nconsole.log('other ' + gate(9, 7));\nconsole.log('Password stored: hash only, never the typed password.');"],
            ],
            'jsx' => [
                [$jsx("<h1>Bearer note_tok_31</h1>\n      <p>200 · user 7</p>\n      <p>Missing header → 401, not 403</p>"), $jsx("<h1>Same token, wrong note</h1>\n      <p>Owner edit → 200</p>\n      <p>Another user → 403</p>\n      <p>The password is stored as a hash.</p>")],
            ],
            'ng' => [
                [$ng("function bearer(header) \x7b\n  if (!header || header.indexOf('Bearer ') !== 0) return null;\n  return header.slice(7).trim();\n\x7d\n\$scope.user = bearer('Bearer note_tok_31') ? 7 : null;\n\$scope.line = '200 user ' + \$scope.user;", "  <p>[[ line ]]</p>"), $ng("\$scope.owner = 200;\n\$scope.other = 403;", "  <p>Owner [[ owner ]]</p>\n  <p>Other user [[ other ]]</p>")],
            ],
            'dart' => [
                ["print('Authorization: Bearer note_tok_31');\nprint('200 user 7');\nprint('Missing header is 401, not 403');", "print('Owner edit -> 200');\nprint('Another user -> 403');\nprint('Store a password hash, never the typed password');"],
            ],
        },
        'cors' => match ($kind) {
            'php' => [
                ["<?php\n\$origin = 'https://notes.example';\n\$allowed = ['https://notes.example', 'http://localhost:5173'];\n\$ok = in_array(\$origin, \$allowed, true);\necho \$ok\n    ? \"Access-Control-Allow-Origin: \x7b\$origin\x7d\\nAccess-Control-Allow-Credentials: true\"\n    : 'No CORS header. The browser will hide the JSON.';", "<?php\n\$method = 'OPTIONS';\n\$ask = 'DELETE';\n\$allow = ['GET', 'POST', 'PATCH', 'DELETE'];\nif (\$method === 'OPTIONS' && in_array(\$ask, \$allow, true)) \x7b\n    echo \"204\\nAccess-Control-Allow-Methods: GET, POST, PATCH, DELETE\\n\";\n    echo 'Preflight has no note body. The real DELETE comes next.';\n\x7d"],
            ],
            'py' => [
                ["origin = 'https://notes.example'\nallowed = \x7b'https://notes.example', 'http://localhost:5173'\x7d\nif origin in allowed:\n    print('Access-Control-Allow-Origin:', origin)\n    print('Access-Control-Allow-Credentials: true')\nelse:\n    print('No CORS header. The browser will hide the JSON.')", "print('OPTIONS 204')\nprint('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE')\nprint('Preflight has no note body. The real DELETE comes next.')"],
            ],
            'js' => [
                ["const origin = 'https://notes.example';\nconst allowed = ['https://notes.example', 'http://localhost:5173'];\nif (allowed.indexOf(origin) >= 0) \x7b\n  console.log('Access-Control-Allow-Origin: ' + origin);\n  console.log('Access-Control-Allow-Credentials: true');\n\x7d else \x7b\n  console.log('No CORS header. The browser will hide the JSON.');\n\x7d", "console.log('OPTIONS 204');\nconsole.log('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE');\nconsole.log('Preflight has no note body. The real DELETE comes next.');"],
            ],
            'jsx' => [
                [$jsx("<h1>Allowed origin</h1>\n      <p>Access-Control-Allow-Origin: https://notes.example</p>\n      <p>Credentials: true — so the star origin is not used</p>"), $jsx("<h1>Preflight</h1>\n      <p>Browser sends OPTIONS first</p>\n      <p>204, then the real DELETE</p>\n      <p>curl never needs this step</p>")],
            ],
            'ng' => [
                [$ng("\$scope.origin = 'https://notes.example';\n\$scope.credentials = true;", "  <p>Allow-Origin [[ origin ]]</p>\n  <p>Credentials [[ credentials ]]</p>"), $ng("\$scope.preflight = 'OPTIONS 204, then DELETE';", "  <p>[[ preflight ]]</p>")],
            ],
            'dart' => [
                ["print('Access-Control-Allow-Origin: https://notes.example');\nprint('Access-Control-Allow-Credentials: true');", "print('OPTIONS 204');\nprint('Then the real DELETE');\nprint('A phone app does not use CORS; the browser does');"],
            ],
        },
        'edge' => match ($kind) {
            'php' => [
                ["<?php\n\$seen = [];\nfunction createOnce(array &\$seen, string \$key, string \$title): array \x7b\n    if (isset(\$seen[\$key])) return [200, \$seen[\$key], 'replay'];\n    \$note = ['id' => 8, 'title' => \$title];\n    \$seen[\$key] = \$note;\n    return [201, \$note, 'first'];\n\x7d\n\$key = 'create-note-8f3a';\n[\$a, \$note, \$tag] = createOnce(\$seen, \$key, 'Buy milk');\necho \$a . ' ' . \$tag . ' ' . json_encode(\$note) . \"\\n\";\n[\$b, \$again, \$tag2] = createOnce(\$seen, \$key, 'Buy milk');\necho \$b . ' ' . \$tag2 . ' id ' . \$again['id'];", "<?php\n\$maxBytes = 2_000_000;\n\$size = 2_400_000;\n\$mime = 'image/jpeg';\n\$allowed = ['image/jpeg', 'image/png', 'image/webp'];\nif (\$size > \$maxBytes) echo \"413 file is larger than 2 MB\\n\";\nelseif (!in_array(\$mime, \$allowed, true)) echo \"415 type not allowed\\n\";\nelse echo \"201 cover stored outside the public folder\\n\";\necho 'Webhook: check the signature, answer 204 fast, do the work after.';"],
            ],
            'py' => [
                ["seen = \x7b\x7d\ndef create_once(key, title):\n    if key in seen:\n        return 200, seen[key], 'replay'\n    note = \x7b'id': 8, 'title': title\x7d\n    seen[key] = note\n    return 201, note, 'first'\nkey = 'create-note-8f3a'\nprint(create_once(key, 'Buy milk'))\nprint(create_once(key, 'Buy milk'))", "size = 2400000\nmime = 'image/jpeg'\nif size > 2000000:\n    print('413 file is larger than 2 MB')\nelif mime not in \x7b'image/jpeg', 'image/png', 'image/webp'\x7d:\n    print('415 type not allowed')\nelse:\n    print('201 cover stored outside the public folder')\nprint('Webhook: check the signature, answer 204 fast, do the work after.')"],
            ],
            'js' => [
                ["const seen = \x7b\x7d;\nfunction createOnce(key, title) \x7b\n  if (seen[key]) return [200, seen[key], 'replay'];\n  const note = \x7b id: 8, title: title \x7d;\n  seen[key] = note;\n  return [201, note, 'first'];\n\x7d\nconst key = 'create-note-8f3a';\nconsole.log(JSON.stringify(createOnce(key, 'Buy milk')));\nconsole.log(JSON.stringify(createOnce(key, 'Buy milk')));", "const size = 2400000;\nconst mime = 'image/jpeg';\nif (size > 2000000) console.log('413 file is larger than 2 MB');\nelse if (['image/jpeg', 'image/png', 'image/webp'].indexOf(mime) < 0) console.log('415 type not allowed');\nelse console.log('201 cover stored outside the public folder');\nconsole.log('Webhook: check the signature, answer 204 fast, do the work after.');"],
            ],
            'jsx' => [
                [$jsx("<h1>Same Idempotency-Key</h1>\n      <p>First call: 201 id 8</p>\n      <p>Retry: 200 id 8, not a second note</p>"), $jsx("<h1>Cover upload</h1>\n      <p>2.4 MB jpeg → 413</p>\n      <p>Allowed types: jpeg, png, webp</p>\n      <p>Webhook answers 204 only after the signature matches</p>")],
            ],
            'ng' => [
                [$ng("\$scope.first = '201 id 8';\n\$scope.retry = '200 id 8 replay';", "  <p>First [[ first ]]</p>\n  <p>Retry [[ retry ]]</p>"), $ng("\$scope.upload = '413 file is larger than 2 MB';\n\$scope.hook = '204 after the signature matches';", "  <p>[[ upload ]]</p>\n  <p>[[ hook ]]</p>")],
            ],
            'dart' => [
                ["print('First call: 201 id 8');\nprint('Retry with the same key: 200 id 8');", "print('413 file is larger than 2 MB');\nprint('Webhook: 204 only after the signature matches');"],
            ],
        },
        'ship' => match ($kind) {
            'php' => [
                ["<?php\n\$checks = [\n    'Paths start with /api/v1' => true,\n    'Unknown sort columns are rejected' => true,\n    'limit is capped at 50' => true,\n    '401 and 403 are different' => true,\n    '500 body has request_id and no stack trace' => true,\n    'CORS origin is a list, not a star, when cookies are used' => true,\n    'Create retries reuse Idempotency-Key' => true,\n];\nforeach (\$checks as \$label => \$ok) \x7b\n    echo (\$ok ? '[PASS] ' : '[FAIL] ') . \$label . \"\\n\";\n\x7d", "<?php\nfunction waitSeconds(int \$attempt): int \x7b\n    return min(32, 2 ** \$attempt);\n\x7d\necho 'retry 0 wait ' . waitSeconds(0) . \"s\\n\";\necho 'retry 1 wait ' . waitSeconds(1) . \"s\\n\";\necho 'retry 5 wait ' . waitSeconds(5) . \"s\\n\";\necho 'Do not retry a POST unless it carried an Idempotency-Key.';"],
            ],
            'py' => [
                ["checks = [\n    'Paths start with /api/v1',\n    'Unknown sort columns are rejected',\n    'limit is capped at 50',\n    '401 and 403 are different',\n    '500 body has request_id and no stack trace',\n    'CORS origin is a list, not a star, when cookies are used',\n    'Create retries reuse Idempotency-Key',\n]\nfor label in checks:\n    print('[PASS]', label)", "def wait_seconds(attempt):\n    return min(32, 2 ** attempt)\nprint('retry 0 wait', wait_seconds(0), 's')\nprint('retry 1 wait', wait_seconds(1), 's')\nprint('retry 5 wait', wait_seconds(5), 's')\nprint('Do not retry a POST unless it carried an Idempotency-Key.')"],
            ],
            'js' => [
                ["const checks = [\n  'Paths start with /api/v1',\n  'Unknown sort columns are rejected',\n  'limit is capped at 50',\n  '401 and 403 are different',\n  '500 body has request_id and no stack trace',\n  'CORS origin is a list, not a star, when cookies are used',\n  'Create retries reuse Idempotency-Key'\n];\nchecks.forEach(function (label) \x7b console.log('[PASS] ' + label); \x7d);", "function waitSeconds(attempt) \x7b\n  return Math.min(32, Math.pow(2, attempt));\n\x7d\nconsole.log('retry 0 wait ' + waitSeconds(0) + 's');\nconsole.log('retry 1 wait ' + waitSeconds(1) + 's');\nconsole.log('retry 5 wait ' + waitSeconds(5) + 's');\nconsole.log('Do not retry a POST unless it carried an Idempotency-Key.');"],
            ],
            'jsx' => [
                [$jsx("<h1>Ship checklist</h1>\n      <ul>\n        <li>/api/v1 on every path</li>\n        <li>Sort columns whitelisted</li>\n        <li>limit capped at 50</li>\n        <li>401 is not 403</li>\n        <li>500 hides the stack trace</li>\n        <li>Idempotency-Key on create</li>\n      </ul>"), $jsx("<h1>Backoff</h1>\n      <p>Try 0 waits 1s</p>\n      <p>Try 1 waits 2s</p>\n      <p>Try 5 waits 32s</p>\n      <p>A POST without an idempotency key is not retried.</p>")],
            ],
            'ng' => [
                [$ng("\$scope.checks = [\n  '/api/v1 on every path',\n  'Sort columns whitelisted',\n  'limit capped at 50',\n  '401 is not 403',\n  '500 hides the stack trace'\n];", "  <p ng-repeat=\"item in checks\">[PASS] [[ item ]]</p>"), $ng("\$scope.waits = [1, 2, 32];", "  <p>Waits: [[ waits.join(', ') ]] seconds</p>")],
            ],
            'dart' => [
                ["print('[PASS] Paths start with /api/v1');\nprint('[PASS] Unknown sort columns are rejected');\nprint('[PASS] limit is capped at 50');\nprint('[PASS] 401 and 403 are different');\nprint('[PASS] 500 hides the stack trace');", "print('retry 0 wait 1s');\nprint('retry 1 wait 2s');\nprint('retry 5 wait 32s');\nprint('Do not retry a POST unless it carried an Idempotency-Key');"],
            ],
        },
    ];
}

function api_lessons(string $playground): array
{
    $kind = match ($playground) {
        'python' => 'py',
        'javascript' => 'js',
        'react', 'next' => 'jsx',
        'angular' => 'ng',
        'flutter' => 'dart',
        default => 'php',
    };
    $file = match ($kind) {
        'py' => 'notes_api.py',
        'js' => 'notes_api.js',
        'jsx' => 'NotesApi.jsx',
        'ng' => 'notes-api.html',
        'dart' => 'notes_api.dart',
        default => 'notes_api.php',
    };
    $pack = api_pack($kind);
    $section = 'Advanced APIs and beyond';
    $specs = [
        ['contract', 'The API contract: method, path, headers, and body', 'http api contract, request response json, endpoint vs webpage',
            'An **API** is a promise. The client sends a **method**, a **path**, **headers**, and sometimes a **body**. The server answers with a **status code**, headers, and a body. A website returns HTML for a human. This track returns **JSON** for another program. Every chapter you already finished stays above this one.',
            [['The four parts of a request', 'Open any notes call and you can point at four things.', ['The **method** is the verb: GET shows, POST creates, PATCH changes a piece, DELETE removes.', 'The **path** is the resource: `/api/v1/notes/4` is note 4. `/api/v1/note` is a different path and should 404.', '**Headers** describe the message. `Content-Type: application/json` means the body is JSON. `Authorization` carries the token.', 'The **body** is the JSON document. GET and DELETE usually have none. A missing body on POST is not “an empty note”; it is an incomplete request.']]],
            [['`method` + `path`', 'Which action, which resource', 'The server matches both. POST `/notes/4` is not GET `/notes/4`.'], ['`Content-Type`', 'How to read the body', 'If you send JSON but say `text/plain`, many servers refuse it with 415.'], ['`Accept`', 'What the client can read', 'Ask for `application/json`. An HTML error page in the body means you hit the website, not the API.'], ['`Authorization`', 'Who is calling', 'The token is not part of the JSON body and not part of the URL.'], ['Status + body', 'What happened, and the data', 'Read the status before you parse. A 422 body is an error list, not a note.'], ['Base URL', 'Scheme, host, and prefix', '`https://api.example` + `/api/v1` is the prefix. Paths in the app start after that.']],
        ],
        ['methods', 'Every HTTP method, and which retries are safe', 'http methods get post put patch delete head options idempotent',
            'The method is not decoration. **GET** must not save. **POST** can create a second row if it is sent twice. **PUT** replaces the whole note. **PATCH** changes only the fields in the body. **DELETE** removes. **HEAD** is GET without a body. **OPTIONS** is the browser asking permission before a “non-simple” call.',
            [['Safe, idempotent, and the double-click', 'Two words prevent the usual data bugs.', ['**Safe** means the call does not change data. GET and HEAD are safe. A link and a refresh may send them.', '**Idempotent** means doing it twice leaves the same result as once. PUT and DELETE are idempotent. POST is not, unless you add an idempotency key in a later chapter.', 'A browser refresh on a POST page resubmits. After a successful create, the API client should stop, and a website should redirect to a GET.', '**PUT** with only `title` replaces the resource. Fields you left out are cleared. **PATCH** with only `title` leaves `body` alone. Sending `"body": null` on PATCH is a clear, not a skip.']]],
            [['`GET`', 'Show a note or a list', 'No JSON body. A cache and a refresh are allowed. Never delete from a GET link.'], ['`POST`', 'Create a note', 'Not idempotent. Two clicks can insert two rows.'], ['`PUT`', 'Replace every field', 'The client sends the full note. Missing fields become empty.'], ['`PATCH`', 'Change listed fields', 'Omitted fields stay. `null` means “store null”, which is different from omitting the key.'], ['`DELETE`', 'Remove the note', 'The second call should 404 or 204, and it must not delete a different row.'], ['`HEAD` / `OPTIONS`', 'Metadata, then permission', 'HEAD has no body. OPTIONS is the CORS preflight, not your delete.']],
        ],
        ['urls', 'Paths, query strings, and names that stay stable', 'rest url design, query string, resource naming, sort whitelist',
            'The path names the thing. Use nouns and plurals: `/api/v1/notes` and `/api/v1/notes/4`. Filters, sort, and page size live in the **query string**. A token, a password, or a secret must not. The version `/v1` is part of the path so you can add `/v2` later without breaking old apps.',
            [['Small rules that break clients', 'URLs get copied into logs, bookmarks, and support tickets.', ['Spaces and reserved characters are encoded. A space is `%20`. Do not build a path by gluing an unsanitized title.', 'A trailing slash can be a different route. Pick one form and redirect the other. Do not make both create data.', '`/notes/4` and a body that says `"id": 9` disagree. Reject that with 409, or ignore the body id and trust the path. Pick one and test it.', 'Sort and filter column names are data from the client. Compare them to a whitelist. Do not paste them into a query string you execute.']]],
            [['`/api/v1/notes`', 'The collection', 'Plural. The verb is the HTTP method, so the path is not `/getNotes`.'], ['`/api/v1/notes/4`', 'One note', 'The id is in the path. `GET /notes?id=4` is harder to cache and easier to mix up with a list.'], ['`?status=open&limit=20`', 'Filter and page size', 'The path is still the collection. The query does not choose a different resource.'], ['`?q=milk`', 'Search text', 'Search is not the same as `status=`. An empty `q` should mean “no search”, not “match everything named empty”.'], ['Encoding', '`%20`, `%26`', 'A raw `&` in a value splits the query. Encode it.'], ['Secrets', 'Not in the query', 'Servers and proxies log URLs. Put the token in `Authorization`.']],
        ],
        ['status', 'Status codes, including the ones beginners swap', 'http status codes 200 201 204 401 403 404 409 422 429 500',
            'The status is the result. The JSON explains it. **200** is a successful read or update. **201** is a successful create, and the body is the new note. **204** is success with an empty body, used after delete. **422** is “I understood the JSON and the fields are wrong.” **401** is “I do not know who you are.” **403** is “I know who you are and this note is not yours.”',
            [['Do not collapse these', 'Each code tells the client what to do next.', ['**400** means the message is broken: invalid JSON, a trailing comma, or the wrong content type. **422** means the JSON parsed and a field failed a rule.', '**404** means this path or this id is not there. An empty list is not 404. Return **200** and `[]`.', '**409** is a conflict: the id in the path does not match the body, or a unique email already exists. **429** means slow down. The `Retry-After` header says how long.', '**500** is your bug. The client sees a short message and a `request_id`. The file path, SQL, and stack trace stay in the log. **502**, **503**, and **504** mean a gateway or a dependency failed, not that the note was invalid.']]],
            [['`200`', 'Read or update worked', 'A list with zero rows is still 200.'], ['`201`', 'Create worked', 'Body is the new note, including the id the server chose. Optional `Location` header points at the new path.'], ['`204`', 'Success, empty body', 'Do not send `'.chr(123).chr(125).'`. Clients that parse JSON will fail.'], ['`401` vs `403`', 'Who you are, vs what you may do', 'A missing token is 401. A valid token on someone else’s note is 403.'], ['`404` vs `405`', 'Unknown path, vs known path and wrong verb', 'POST on a path that only allows GET is 405, not 404.'], ['`422` vs `400`', 'Bad fields, vs unreadable message', 'Show 422 next to the input. Do not call a blank title a 500.'], ['`429`', 'Too many calls', 'Back off. Do not loop immediately.'], ['`500`', 'Server fault', 'Hide internals. Log them with the request id.']],
        ],
        ['headers', 'Headers that change what the call means', 'http headers content-type authorization etag idempotency-key request-id',
            'Headers are instructions beside the body. `Content-Type` tells the server how to parse. `Accept` tells it what you can read. `Authorization: Bearer …` is the caller. `Idempotency-Key` makes one create safe to retry. `ETag` and `If-None-Match` let a client skip a download it already has.',
            [['The ones that get forgotten', 'A correct JSON body can still fail because a header is wrong.', ['Charset is part of the type: `application/json; charset=utf-8`. Emoji in a title needs UTF-8 on both sides.', 'A **304** answer has an empty body. The client keeps the copy it stored with that ETag. If you also send the note, you have not sent a 304.', '`X-Request-Id` (or `request_id` in the JSON) is how support finds one failed call in the log. Generate it if the client did not send one.', 'Hop-by-hop headers such as `Connection` are for the proxy. Do not build app logic on them. Custom `X-` headers are fine, but document them. An unknown header is ignored, it is not an error.']]],
            [['`Content-Type`', 'Parse rules for the body', 'JSON create calls send `application/json`. A file uses `multipart/form-data`.'], ['`Accept`', 'The response shape you want', 'If you cannot produce it, say 406 rather than sending HTML.'], ['`Authorization`', 'Bearer token', 'One space after the word Bearer. An empty token is still “no token”.'], ['`Idempotency-Key`', 'Identity of this create attempt', 'The same key and the same user return the first result. A different body with the same key is 409.'], ['`ETag` / `If-None-Match`', 'Cache check', 'Quotes are part of the tag. Compare the whole value.'], ['`Cache-Control`', 'Who may store the answer', 'Private notes use `private, no-store`. A public list can be cached. Do not cache a response that includes a token.'], ['`Retry-After`', 'When to try again', 'Send it with 429 and with 503.']],
        ],
        ['json', 'JSON shapes, types, and field errors', 'json api errors, validation 422, mass assignment, iso8601 null',
            'JSON has types. `"false"` is text. `false` is a boolean. `"4"` is text. `4` is a number. `null` is present and empty. A missing key was not sent. Your API should use one error shape everywhere: a list of `'.chr(123).'field, message'.chr(125).'` and status **422**.',
            [['Fields the client must not set', 'The body is untrusted input, the same as a form.', ['Ignore or reject `id`, `created_at`, `is_admin`, and `owner_id` on create. The server sets them. Accepting `is_admin: true` from the client is how a notes app becomes an admin app.', 'Trim text. A title of three spaces is empty. Cap the length. Store dates as **ISO-8601** in UTC (`2026-04-02T09:30:00Z`), not as “yesterday”.', 'A trailing comma, a single quote, or a comment makes invalid JSON. That is **400**, before validation runs.', 'Very large integers are safer as strings if a JavaScript client must read them. Document that. Do not sometimes send a number and sometimes a string for the same field.']]],
            [['object', 'One note', 'Use the same keys in create, read, and update responses.'], ['array', 'A list', 'A list of notes is `[...]`. An empty list is `[]`, not `null`, unless you documented null.'], ['`null` vs missing', 'Clear vs leave', 'On PATCH, missing means unchanged. `null` means store null. Write that in the contract.'], ['`errors[]`', 'Field messages', '`field` is `title`, not a paragraph. The client binds the message to the input.'], ['Extra keys', 'Mass assignment', 'Unknown keys are ignored or rejected. They are never copied onto the row automatically.'], ['Dates', 'ISO-8601 UTC', 'Do not return a locale string such as `02/04/26`. Clients will guess the month.'], ['Encoding', 'UTF-8, no BOM', 'A byte-order mark at the start makes the first key look wrong.']],
        ],
        ['crud', 'Create, read, update, and delete one note', 'rest crud json api create read update delete',
            'One resource, four actions. **POST** `/api/v1/notes` creates and returns **201** with the id the server generated. **GET** `/api/v1/notes/4` returns **200** or **404**. **PATCH** changes listed fields and returns the note after the change. **DELETE** returns **204** with no body. The id in the database is not chosen by the client.',
            [['Order of checks', 'Do the cheap, safe checks in the same order every time.', ['Read the token first. Unknown caller is **401** before you look up the note. A private missing note can stay **404** so you do not reveal ids, but then you must do that for every private route. Document the choice.', 'Then load the row. Missing is **404**. Present but not owned is **403**. Then validate. Then write.', 'After a write, return the saved row, not the request body. The saved title is trimmed. The saved `updated_at` is the server clock.', 'DELETE is not “set a hidden flag” unless you said so. If you soft-delete, GET by id returns 404 for normal clients, and you need a separate admin path to see it.']]],
            [['`POST /notes`', 'Insert and return 201', 'The response id is the one to use next. Do not ask the client to invent it.'], ['`GET /notes/4`', 'One row or 404', 'Do not return 200 with `null` for a missing id.'], ['`PATCH /notes/4`', 'Partial change', 'Return the full note after the save so the client can refresh its screen.'], ['`PUT /notes/4`', 'Full replace', 'Only offer PUT if clients really send every field. PATCH is the one to teach first.'], ['`DELETE /notes/4`', '204 empty', 'A second delete is 404 if the row is gone. It must not delete id 5.'], ['Server fields', '`created_at`, owner', 'The client does not send these. The response includes them.']],
        ],
        ['list', 'Lists: filter, sort, page size, and cursors', 'api pagination cursor limit offset filter sort whitelist',
            'A list endpoint is **GET** `/api/v1/notes`. It always has a cap. Default `limit` is small (20). The maximum is 50 even if the client asks for 500. **Offset** (`page=3`) is easy and gets slow and wrong if rows are inserted while someone pages. A **cursor** (`next` = the last id you returned) stays stable. An empty result is **200** and `[]`.',
            [['Filters are exact until you say otherwise', 'Write down what each query key does.', ['`status=open` is exact. `q=milk` is a search and needs a defined rule: case, spaces, and which columns.', 'Sort keys are a whitelist: `created_at`, `title`. A leading `-` can mean descending (`-created_at`) if you document it. Anything else falls back or returns 400. It never becomes SQL text.', '`limit=0`, negative limits, and missing limits become the default. They do not mean “all rows”.', 'If you also return `total`, say that it is optional. Counting every row on each page is expensive. A cursor response can omit total and still be correct.']]],
            [['`limit`', 'Page size', 'Default 20, max 50. Values above the max are clamped or rejected. Say which.'], ['`offset` / `page`', 'Skip ahead', 'Simple. Duplicates or skips appear if a row is inserted during paging.'], ['cursor `next`', 'Last id from this page', 'The next call sends that id and receives rows after it. No overlaps.'], ['`status`', 'Exact filter', 'Unknown status is 400, not a silent empty list, if the client mistyped.'], ['`q`', 'Search', 'Define case and columns. Do not search the password hash or the token table.'], ['`sort`', 'Whitelist only', 'Reject column names that are not on the list.'], ['Empty', '200 and `[]`', '404 is for one missing id, not for zero search hits.']],
        ],
        ['auth', 'Tokens, 401, 403, and who owns the note', 'bearer token authentication, 401 vs 403, password hash, api authorization',
            'The caller proves who they are, then the server checks whether that person owns the note. **Login** checks the password against a **hash** and returns a token. Later calls send `Authorization: Bearer note_tok_31`. No header, a bad token, or an expired token is **401**. A good token looking at someone else’s note is **403**.',
            [['Where the token lives', 'The browser and the phone are different.', ['A phone app can keep the token in secure storage. A browser page that keeps it in `localStorage` can lose it to any script injection. An `HttpOnly` cookie is not readable by JavaScript. If you use a cookie, you also need CSRF protection on cookie-authenticated writes.', 'Send the token only to your API host, and only over HTTPS. Never put it in a query string.', 'Logout deletes or expires the server-side token if you store one. A signed token that you cannot revoke needs a short lifetime and a refresh step you document.', 'The password is never returned, never logged, and never stored as the user typed it. Store a hash. A token is not a password. It is a key you can revoke.']]],
            [['`Bearer`', 'The scheme word', 'The header is `Bearer`, a space, then the token. `Token` or a raw string will not match.'], ['`401`', 'Not signed in', 'Missing, expired, or unknown. The client should send the user to login.'], ['`403`', 'Signed in, not allowed', 'Do not send them to login again. They need a different note or a different role.'], ['Owner check', 'After the token', 'Load the user from the token, then compare `note.owner_id`. Do not trust `owner_id` in the JSON.'], ['Hash', 'Stored password', 'The login request sends the password once, over HTTPS. The database stores the hash.'], ['Expiry', 'Token lifetime', 'An expired token is 401 even if the user id inside it looks valid.']],
        ],
        ['cors', 'CORS, cookies, and why curl works when the browser does not', 'cors preflight access-control-allow-origin credentials options',
            '**CORS** is a browser rule. The browser asks your API whether `https://notes.example` may read the response. A phone app, a PHP server, and curl do not ask. If curl shows JSON and the browser shows a CORS error, the API route ran or was blocked by the browser afterwards. The fix is response headers, not a change to the JSON.',
            [['Preflight is a separate request', 'DELETE, PATCH, and a JSON `Content-Type` are not “simple”.', ['The browser first sends **OPTIONS** with `Access-Control-Request-Method`. You answer **204** (or 200) and list the methods and headers you allow. No note is deleted by OPTIONS.', '`Access-Control-Allow-Origin: *` cannot be used together with credentials. If you send cookies, echo the exact origin from a whitelist and set `Access-Control-Allow-Credentials: true`.', 'Allow the `Authorization` and `Content-Type` and `Idempotency-Key` headers, or the browser will not attach them.', 'A 401 JSON body is hidden from JavaScript if the CORS headers are missing on that error too. Put the CORS headers on errors, not only on 200.']]],
            [['Browser only', 'CORS does not apply to curl', 'A server-to-server webhook has no preflight.'], ['`Allow-Origin`', 'One exact site, or a star', 'Star is for public data with no cookies and no tokens you hope to keep private.'], ['Credentials', 'Cookies on the call', 'Requires a specific origin, not `*`.'], ['`OPTIONS`', 'Preflight', 'Must not create, update, or delete. Answer 204.'], ['Allow-Headers', 'Authorization and Content-Type', 'If `Authorization` is missing from the list, the browser drops the token.'], ['Errors too', '401 still needs CORS', 'Otherwise the page cannot read the 401 body and looks like a network failure.']],
        ],
        ['edge', 'Uploads, webhooks, and idempotency keys', 'multipart upload limit, webhook signature, idempotency key retry',
            'Three calls are not ordinary JSON. A **file** arrives as `multipart/form-data` with a size cap and an allowed type list. A **webhook** is another server calling you, so you check a signature and answer fast. An **idempotency key** lets the client retry a create and get the original note instead of a duplicate.',
            [['The small failures', 'These are the bugs that show up on the second try.', ['Check size before you store. Over the cap is **413**. A type that is not jpeg, png, or webp is **415**. Trust the detected type, not only the filename. Store the file outside the public folder and serve it through a checked route.', 'The same `Idempotency-Key` for the same user returns the first response, including the first status and the first id. Keep that record. A retry with the same key and a different title is **409**.', 'A webhook answers **204** after the signature matches, then does slow work. A slow answer makes the sender retry and you process the event twice. Save the event id so the second delivery is a no-op.', 'Compare signatures with a constant-time compare. Reject a timestamp that is too old so a captured call cannot be replayed days later.']]],
            [['`multipart/form-data`', 'File plus fields', 'This is not `application/json`. The file and the title are parts.'], ['`413` / `415`', 'Too big, or wrong type', 'Cap bytes and whitelist types. Do not use the file extension alone.'], ['Storage', 'Outside the web root', 'The public URL is one you generate, not the raw temp path.'], ['`Idempotency-Key`', 'One create', 'Same key, same user, same body → first response. Same key, different body → 409.'], ['Webhook', 'Their POST to you', 'Verify the signature. Respond 204. Process after. Store the event id.'], ['Replay', 'Old timestamp', 'Refuse events outside a short window, such as five minutes.']],
        ],
        ['ship', 'Versioning, rate limits, retries, and the ship list', 'api versioning rate limit retry backoff openapi production checklist',
            'Ship `/api/v1` and leave it stable. A new required field or a renamed key is `/api/v2`, not a silent change. Limit how many calls one token can make. Retry only calls that are safe: GET, PUT, DELETE, and a POST that sent an idempotency key. Wait longer each time, and stop.',
            [['What “done” means', 'The list is the contract for this track.', ['Write the paths, methods, statuses, and error shape in one document (OpenAPI or a markdown table). The code and the document should match.', 'Timeouts exist on both sides. A client that waits forever will pile up. A server route that waits forever on a database will pile up too.', 'Log the method, path, status, and request id. Do not log the Authorization header or the password or the raw file bytes.', 'Health check is `GET /api/v1/health` and returns 200 with no secrets. It is not a list of notes.', 'Add a breaking change only under a new version. Adding an optional JSON field is usually safe. Removing one is not.']]],
            [['`/api/v1`', 'This contract', 'Old apps keep calling v1 after v2 exists.'], ['Optional field', 'Usually safe to add', 'Old clients ignore unknown keys if you told them to.'], ['Removed field', 'Breaking', 'That is a new version, not a Friday edit.'], ['Rate limit', 'Per token', '429 plus `Retry-After`. A login route has its own tighter limit.'], ['Backoff', '1s, 2s, 4s, stop', 'Add a little random jitter so every client does not retry on the same second. Cap the wait.'], ['No blind POST retry', 'Duplicates', 'Retry POST only with the same idempotency key.'], ['Logs', 'method, path, status, request id', 'Never the token, the password, or the cookie.'], ['Health', '`GET /health`', '200, short body, no note data.']],
        ],
    ];

    $chapters = [];
    foreach ($specs as $spec) {
        [$id, $title, $keywords, $lead, $sections, $rows] = $spec;
        $codes = $pack[$id][0];
        $chapters[] = blesson([
            'section' => $section,
            'title' => $title,
            'keywords' => $keywords,
            'lead' => $lead,
            'sections' => array_map(static function (array $sec): array {
                return ['heading' => $sec[0], 'paragraphs' => [$sec[1]], 'bullets' => $sec[2]];
            }, $sections),
            'rows' => $rows,
            'examples' => [
                ['Example 1 — run the small case', $file, 'This is the rule on one note. Run it, then read the status or the sentence it prints.', $codes[0]],
                ['Example 2 — the case beginners miss', $file, 'The second call is the edge: a bad path, a second try, a wrong owner, or a cap.', $codes[1]],
            ],
            'micro' => [
                'Read the **status** before the body. A 204 body must be empty. A 422 body is the error list. A 201 body is the saved note, not an echo of the raw request.',
                'The client does not choose `id`, `owner_id`, `is_admin`, or `created_at`. If those keys arrive, ignore them or return an error. Do not copy the whole JSON onto the row.',
                'GET stays free of side effects. A retry of POST needs an **Idempotency-Key**. A token goes in `Authorization`, never in the query string. Empty search results are **200** and `[]`.',
            ],
            'quiz' => [
                bquiz('Which status means the JSON was readable but a field failed a rule?', '422', '500', '204', '301'),
                bquiz('Which pair names “not signed in” and “signed in but not the owner”?', '401 and 403', '403 and 401', '404 and 400', '200 and 201'),
                bquiz('What should a retry of the same create do when the idempotency key matches?', 'Return the first note instead of inserting a second one', 'Always insert another row', 'Respond 500', 'Delete the first note'),
            ],
            'practice' => 'Using only this chapter’s rules, write the method, path, status, and one header for the call it describes. Then run both examples and say which line proves the small rule.',
        ], $playground);
    }
    return $chapters;
}

$tracks = [
    'react-tutorial' => 'react',
    'javascript-tutorial' => 'javascript',
    'php-tutorials' => 'php',
    'python-complete-tutorials' => 'python',
    'nextjs-tutorial' => 'next',
    'angular-tutorial' => 'angular',
    'flutter-tutorial' => 'flutter',
    'laravel-tutorial' => 'laravel',
    'codeigniter-tutorial' => 'codeigniter',
];

foreach ($tracks as $slug => $lang) {
    api_append($pdo, $slug, api_lessons($lang));
}
