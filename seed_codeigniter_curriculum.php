<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/seed_helper.php';

$courseMeta = [
    'title' => 'CodeIgniter 4 Tutorial',
    'slug' => 'codeigniter-tutorial',
    'description' => 'Master CodeIgniter 4 (CI4) step by step from beginner to production architect. 20 comprehensive lessons covering CI4 MVC Architecture, Spark CLI, .env & Typed Configs, URI Routing, Request/Response Objects, Controllers, Controller Filters, Views, Layouts & View Cells, Helpers, Query Builder, CI4 Models, Entities, Migrations & Seeders, Form Validation, Sessions, Shield Authentication, CSRF/XSS/CSP Security, File Uploads, Caching, and RESTful ResourceController APIs.',
    'keywords' => 'codeigniter 4 tutorial, ci4 tutorial, learn codeigniter step by step, php mvc, spark cli, codeigniter routing, codeigniter query builder, codeigniter model, entity class ci4, codeigniter shield, rest api ci4',
];

function ci_ch(string $sec, string $title, string $kw, string $lead, array $bullets, array $rows, string $codeFile, string $code, array $micro, array $quiz, string $practice, string $visual = ''): array
{
    return [
        'section' => $sec,
        'title' => $title,
        'keywords' => $kw,
        'lead' => $lead,
        'sections' => [['heading' => 'Step-by-Step Mechanics & Core Rules', 'paragraphs' => [], 'bullets' => $bullets]],
        'visual_html' => $visual,
        'table' => ['title' => $title . ' — Technical Reference', 'headers' => ['Feature / Syntax', 'Behavior', 'Critical Micro-Detail'], 'rows' => $rows],
        'examples' => [['title' => 'Runnable CI4 Example: ' . $title, 'file' => $codeFile, 'lang' => 'codeigniter', 'desc' => 'Execute this CodeIgniter 4 pattern right in the playground.', 'code' => $code]],
        'micro_points' => $micro,
        'quiz' => $quiz,
        'practice' => $practice,
    ];
}

$ciFlow = svg_pipeline_diagram('CodeIgniter 4 Request Lifecycle', [
    ['public/index.php', 'Paths & Boot'],
    ['Before Filters', 'CSRF / Auth'],
    ['Router', 'Config/Routes.php'],
    ['Controller + Model', 'Entity / View'],
    ['After Filters', 'CSP / Response'],
], '#f97316');

$chapters = [
    ci_ch(
        '1. CI4 Foundations & Architecture',
        'CodeIgniter 4 Introduction, MVC Flow & Why CI4 Is Ultra-Fast',
        'what is codeigniter 4, ci4 vs ci3, lightweight php framework, ci4 mvc',
        '**CodeIgniter 4 (CI4)** is a modern, PSR-4 namespaced PHP 8.1+ framework celebrated for blazing speed (~2MB memory footprint), minimal configuration overhead, strict security defaults, and crystal-clear MVC boundaries.',
        [
            '**Ground-Up PHP 8 Rewrite**: CI4 is not an incremental patch of CI3—it introduces namespaces (`App\\Controllers`), `spark` CLI, `Entity` classes, `Filters`, and PSR-7/HTTP abstractions.',
            '**Shared-Hosting & Cloud Friendly**: Runs effortlessly on both Hostinger/cPanel shared hosting and Docker/Nginx microservices with near-zero cold-start latency.'
        ],
        [
            ['`Memory Footprint`', '~1.8 MB – 2.5 MB per HTTP request', 'Ideal for high-concurrency PHP-FPM pools and modest VPS instances.'],
            ['`PSR-4 Autoloading`', '`App\\` maps cleanly to `app/` directory', 'Works natively with Composer + CI4\'s built-in `Config\\Autoload`.'],
            ['`Strict Security Defaults`', 'Auto-Routing OFF, production errors hidden by default', 'Prevents accidental exposure on fresh deployments.'],
        ],
        'ci4_intro.php',
        "<?php\n\$bench = ['framework' => 'CodeIgniter 4.5+', 'min_php' => '8.1', 'memory_mb' => 2.1, 'auto_route_default' => false];\necho json_encode(\$bench, JSON_PRETTY_PRINT);\n",
        ['Unlike heavy frameworks loading 150+ service classes per boot, CI4 lazy-instantiates core services via `Config\\Services` only when requested!'],
        [
            ['question' => 'Which autoloading standard does CodeIgniter 4 use to map the App\\ namespace to the app/ directory?', 'answer' => 'PSR-4', 'options' => ['PSR-4', 'PEAR-1', 'CommonJS', 'AMD']],
            ['question' => 'Is legacy URI Auto-Routing enabled or disabled by default in modern CodeIgniter 4?', 'answer' => 'Disabled ($routes->setAutoRoute(false)) for security', 'options' => ['Disabled ($routes->setAutoRoute(false)) for security', 'Enabled on all methods', 'Enabled only for DELETE requests', 'Controlled by robots.txt']],
            ['question' => 'What is the approximate baseline RAM footprint of a CodeIgniter 4 request?', 'answer' => 'Around 2 MB', 'options' => ['Around 2 MB', 'Around 250 MB', 'Around 1.5 GB', '64 KB']],
        ],
        'Compare how lazy service loading via `Services::cache()` avoids loading Redis drivers on routes that never touch cache.',
        $ciFlow
    ),
    ci_ch(
        '1. CI4 Foundations & Architecture',
        'Installation (Composer), XAMPP ext-intl Setup, Folders & Spark CLI',
        'install codeigniter 4 xampp, php ext-intl ci4, spark cli commands, writable folder',
        'Install CodeIgniter 4 via `composer create-project codeigniter4/appstarter project-root`, enable PHP\'s `intl` and `mbstring` extensions, copy `env` to `.env`, and manage generators via `php spark`.',
        [
            '**XAMPP `ext-intl` Requirement**: Open `C:\\xampp\\php\\php.ini`, uncomment `extension=intl`, and restart Apache—missing `intl` is the #1 beginner error!',
            '**`public/` DocumentRoot**: Point virtual hosts to `public/` so `app/`, `system/`, `writable/`, and `.env` remain completely outside public web reach.',
            '**`writable/` Permissions**: Houses `cache/`, `logs/`, `session/`, `uploads/`, and `debugbar/`.'
        ],
        [
            ['`cp env .env`', 'Activates local environment overrides', 'Uncomment `CI_ENVIRONMENT = development` to enable Debug Toolbar & error traces!'],
            ['`php spark make:controller Blog --restful`', 'Scaffolds RESTful controller methods', 'Add `--suffix` to name the class `BlogController` automatically.'],
            ['`php spark namespaces`', 'Verifies PSR-4 namespace mappings', 'Quickly diagnoses `Class not found` autoloading typos.'],
        ],
        'spark_check.php',
        "<?php\n\$extensions = ['intl' => extension_loaded('intl'), 'mbstring' => extension_loaded('mbstring'), 'json' => extension_loaded('json')];\necho \"PHP Runtime Diagnostics for CI4:\\n\" . json_encode(\$extensions, JSON_PRETTY_PRINT);\n",
        ['Why does CI4 ship the template file named `env` (without a leading dot)? Because some FTP/ZIP extractors hide dotfiles—always copy `env` -> `.env` rather than renaming it so team members retain the template!'],
        [
            ['question' => 'Why do fresh CodeIgniter 4 installations show a generic "Whoops!" screen instead of detailed stack traces until .env is configured?', 'answer' => 'CI_ENVIRONMENT defaults to "production" to prevent leaking sensitive errors accidentally', 'options' => ['CI_ENVIRONMENT defaults to "production" to prevent leaking sensitive errors accidentally', 'PHP display_errors crashes Spark', 'DebugBar requires Redis', 'Apache blocks stack traces']],
            ['question' => 'Which Spark flag appends "Controller", "Model", or "Filter" to generated class names?', 'answer' => '--suffix', 'options' => ['--suffix', '--postfix-name', '--typed', '--psr4']],
            ['question' => 'Why should you copy "env" to ".env" instead of deleting "env"?', 'answer' => '"env" serves as the version-controlled template for other developers and deployments', 'options' => ['"env" serves as the version-controlled template for other developers and deployments', 'Spark deletes .env on reboot', 'Composer reads "env" in production', 'Apache executes "env"']],
        ],
        'Run the PHP diagnostics snippet in the playground to see which extensions are active in your XAMPP PHP runtime.'
    ),
    ci_ch(
        '1. CI4 Foundations & Architecture',
        'Typed Configuration Classes, .env Dot-Overrides & Services Locator',
        'codeigniter 4 config classes, config helper ci4, Services locator shared instance',
        'Instead of global untyped arrays, CI4 uses typed classes in `app/Config/` resolved as singletons via `config(\'App\')`, while `Config\\Services` acts as a lightweight Inversion-of-Control service locator.',
        [
            '**Automatic `.env` Property Mapping**: Setting `database.default.DBDriver = MySQLi` or `app.CSPEnabled = true` in `.env` automatically overrides matching public properties on `Config\\Database` and `Config\\App`!',
            '**`config(\'Shop\')` vs `new Shop()`**: Always use `config(\'Shop\')` so CI4 returns a single shared instance with `.env` overrides properly hydrated.',
            '**Overriding Core Services**: Override any method in `app/Config/Services.php` (`$getShared = true`) to swap CI4\'s default mailer, logger, or cache engine globally!'
        ],
        [
            ['`config(\'App\')->baseURL`', 'Shared config singleton with `.env` applied', '`new \\Config\\App()` may miss registrar overrides—prefer `config()`!'],
            ['`service(\'request\')`', 'Returns shared singleton service', 'Pass `false` (`service(\'curlrequest\', false)`) when you need a fresh unshared instance!'],
            ['`env(\'API_KEY\', \'default\')`', 'Reads scalar `.env` variable with fallback', 'Keep secrets inside typed `Config` properties for IDE autocompletion.'],
        ],
        'app/Config/ServicesDemo.php',
        "<?php\nclass MiniServices {\n    private static array \$shared = [];\n    public static function mailer(bool \$getShared = true): object {\n        if (\$getShared) {\n            return self::\$shared['mailer'] ??= (object)['id' => uniqid('smtp_')];\n        }\n        return (object)['id' => uniqid('smtp_')];\n    }\n}\necho 'Shared #1: ' . MiniServices::mailer()->id . \"\\n\";\necho 'Shared #2: ' . MiniServices::mailer()->id . \" (same instance!)\\n\";\necho 'Fresh  #3: ' . MiniServices::mailer(false)->id . \" (new instance!)\\n\";\n",
        ['Notice `service(\'validation\', false)` when running two independent validations in the same request—the shared validator retains previous errors unless `reset()` or `false` is used!'],
        [
            ['question' => 'How do you request a fresh, non-shared instance from CI4\'s service() helper?', 'answer' => 'Pass false as the last argument, e.g. service("validation", false)', 'options' => ['Pass false as the last argument, e.g. service("validation", false)', 'Call unset($service)', 'Prefix the name with "new:"', 'Services are never shared']],
            ['question' => 'How does a .env line like app.defaultLocale = "hi" map to PHP code in CI4?', 'answer' => 'It overrides public $defaultLocale on App\\Config\\App automatically', 'options' => ['It overrides public $defaultLocale on App\\Config\\App automatically', 'It rewrites index.php on disk', 'It creates a SQL table named app', 'It sets an Apache mod_rewrite variable']],
            ['question' => 'Why should you call config("App") rather than new \\Config\\App()?', 'answer' => 'config() caches a single shared instance and applies module Registrar overrides', 'options' => ['config() caches a single shared instance and applies module Registrar overrides', 'new \\Config\\App() is a syntax error', 'config() encrypts strings', 'Config classes have private constructors']],
        ],
        'Verify object Reuse vs Fresh creation in `MiniServices` by running the playground code.'
    ),
    ci_ch(
        '2. Routing, Controllers & Filters',
        'URI Routing: Verbs, Placeholders (:num, :segment), Route Groups & Named Routes',
        'codeigniter 4 routes, placeholders num segment any, route groups namespace, url_to route_to',
        'All public endpoints in CI4 are declared in `app/Config/Routes.php` using HTTP verb methods (`$routes->get()`, `$routes->post()`, `$routes->put()`, `$routes->delete()`), regex placeholders, and `group()` prefixes.',
        [
            '**Back-References (`$1`, `$2`)**: `$routes->get(\'courses/(:segment)/lesson/(:num)\', \'CourseController::lesson/$1/$2\')` maps captured URI groups directly to controller method parameters.',
            '**Why `(:segment)` Beats `(:any)`**: `(:segment)` matches everything *except* `/` (`[^/]+`), preventing extra slash segments from shifting positional controller arguments!',
            '**Reverse Routing (`url_to()`)**: Generate links via `url_to(\'CourseController::show\', $slug)` or named aliases so changing URI paths in `Routes.php` updates every link site-wide.'
        ],
        [
            ['`(:num)` / `(:alpha)` / `(:alphanum)`', 'Strict character-class URI constraints', 'Non-matching URLs return `404` before instantiating your Controller.'],
            ['`(:segment)`', 'Single path segment (`[^/]+`)', 'Safe default for slugs, usernames, and UUIDs.'],
            ['`$routes->group(\'admin\', [\'filter\' => \'auth\'], fn)`', 'Applies URI prefix + filter + namespace to all inner routes', 'Eliminates repeating `filter` options on 30 admin routes!'],
        ],
        'app/Config/RoutesDemo.php',
        "<?php\nfunction matchCiRoute(string \$pattern, string \$uri): ?array {\n    \$regex = str_replace(['(:num)', '(:segment)'], ['([0-9]+)', '([^/]+)'], \$pattern);\n    if (preg_match('#^' . \$regex . '$#', \$uri, \$m)) {\n        array_shift(\$m);\n        return \$m;\n    }\n    return null;\n}\n\necho 'Match /courses/ci4-guide/lesson/12: ' . json_encode(matchCiRoute('/courses/(:segment)/lesson/(:num)', '/courses/ci4-guide/lesson/12')) . \"\\n\";\necho 'Reject extra slash in (:segment):   ' . json_encode(matchCiRoute('/courses/(:segment)', '/courses/a/b')) . \"\\n\";\n",
        ['Run `php spark routes` after editing `Routes.php` to verify exact regex order and active filters!'],
        [
            ['question' => 'Which CI4 route placeholder matches any characters within a SINGLE URI segment (excluding forward slashes)?', 'answer' => '(:segment)', 'options' => ['(:segment)', '(:any)', '(:hash)', '(:word)']],
            ['question' => 'Which helper generates a full URL from a named route or Controller::method reference in CI4 views?', 'answer' => 'url_to()', 'options' => ['url_to()', 'asset_link()', 'go_route()', 'make_href()']],
            ['question' => 'What happens if a user sends a POST request to a URI defined only with $routes->get()?', 'answer' => 'CI4 rejects it (404 / 405) without executing the GET controller method', 'options' => ['CI4 rejects it (404 / 405) without executing the GET controller method', 'CI4 converts POST to GET automatically', 'It runs index() instead', 'It disables CSRF']],
        ],
        'Add support for `(:alphanum)` (`([a-zA-Z0-9]+)`) to `matchCiRoute()` in the playground.'
    ),
    ci_ch(
        '2. Routing, Controllers & Filters',
        'Controllers, BaseController, initController() & IncomingRequest / Response',
        'codeigniter 4 controllers, BaseController initController, getPost getJSON getVar, setJSON',
        'CI4 Controllers extend `App\\Controllers\\BaseController`, receiving typed `$this->request` (`IncomingRequest`) and `$this->response` (`ResponseInterface`) objects via `initController()`.',
        [
            '**`getPost()` vs `getGet()` vs `getJSON()` (Avoid `getVar()`!)**: `$this->request->getVar(\'role\')` reads from `$_GET`, `$_POST`, AND JSON body indistinguishably—allowing query-string parameter pollution (`?role=admin`) on POST endpoints! Always use explicit `$this->request->getPost(\'email\')` or `$this->request->getJSON(true)`!',
            '**Shared Helpers in `BaseController`**: Declare `protected $helpers = [\'form\', \'url\'];` on `BaseController` or individual controllers.',
            '**Returning Responses**: Return `view(\'...\')` HTML strings, `redirect()->to(\'/dashboard\')->with(\'msg\', \'Saved!\')`, or `$this->response->setStatusCode(201)->setJSON($data)`.'
        ],
        [
            ['`$this->request->getPost(\'field\')`', 'Reads strictly from HTTP POST body', 'Prevents `$_GET` query string parameter pollution!'],
            ['`$this->request->getJSON(true)`', 'Parses `application/json` body as associative PHP array', 'Without `true`, returns `stdClass` object.'],
            ['`redirect()->back()->withInput()`', 'Redirects to form keeping old inputs in session flashdata', 'Pair with `old(\'email\')` inside View `<input>` fields!'],
        ],
        'RequestExtraction.php',
        "<?php\n// Why explicit getPost() beats generic getVar()!\n\$queryParams = ['role' => 'superadmin']; // Attacker appended ?role=superadmin to form action URL!\n\$postBody    = ['email' => 'user@nectra.dev'];\n\n\$unsafeGetVar = \$postBody['role'] ?? \$queryParams['role'] ?? 'student';\n\$safeGetPost  = \$postBody['role'] ?? 'student';\n\necho \"Unsafe getVar('role'): {\$unsafeGetVar} (polluted by URL!)\\n\";\necho \"Safe  getPost('role'): {\$safeGetPost}\\n\";\n",
        ['**Critical Security Micro-Detail**: Never use `$this->request->getVar()` for security-sensitive inputs—always use the HTTP-method-specific getter (`getPost()`, `getGet()`, `getJsonVar()`)!'],
        [
            ['question' => 'Why is $this->request->getPost("field") safer than $this->request->getVar("field") on form submissions?', 'answer' => 'getVar() falls back to $_GET query parameters, allowing URL query-string parameter pollution', 'options' => ['getVar() falls back to $_GET query parameters, allowing URL query-string parameter pollution', 'getVar() deletes cookies', 'getPost() automatically hashes passwords', 'getVar() only returns integers']],
            ['question' => 'What does passing true to $this->request->getJSON(true) do?', 'answer' => 'Returns the decoded JSON payload as an associative PHP array instead of stdClass', 'options' => ['Returns the decoded JSON payload as an associative PHP array instead of stdClass', 'Validates JSON against Schema.org', 'Strips whitespace from HTML', 'Caches the request in Redis']],
            ['question' => 'Which method chains old form values onto a redirect when validation fails?', 'answer' => 'redirect()->back()->withInput()', 'options' => ['redirect()->back()->withInput()', 'redirect()->keepPost()', 'response()->flashAll()', 'view()->restoreForm()']],
        ],
        'Test `json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR)` error handling for malformed JSON API requests.'
    ),
    ci_ch(
        '2. Routing, Controllers & Filters',
        'Controller Filters (Middleware): Auth Guards, Role Arguments, CORS & Throttling',
        'codeigniter 4 filters, FilterInterface before after, app/Config/Filters.php, throttler ci4',
        '**Controller Filters** (`app/Filters/*.php`) implement `FilterInterface` to run cross-cutting checks (`before()` and `after()`) registered via aliases in `app/Config/Filters.php`.',
        [
            '**Halting Execution in `before()`**: If `before()` returns `null`/`void`, request execution continues to the Controller; if it returns a `ResponseInterface` or `RedirectResponse`, execution stops immediately!',
            '**Filter Arguments**: Route syntax `[\'filter\' => \'role:admin,editor\']` passes `[\'admin\', \'editor\']` as `$arguments` into `before(RequestInterface $request, $arguments = null)`.',
            '**Built-In Filters**: CI4 ships `csrf`, `honeypot` (anti-bot hidden input trap!), `invalidchars` (rejects malformed UTF-8/control bytes), `secureheaders`, `cors` (CI4.5+), and `forcehttps`.'
        ],
        [
            ['`invalidchars` filter', 'Rejects non-UTF8 & null-byte injection in inputs', 'Enable globally in `Config\\Filters::$globals[\'before\']`!'],
            ['`secureheaders` filter', 'Adds `X-Frame-Options`, `X-Content-Type-Options`, CSP/Referrer headers', 'Enable globally in `Config\\Filters::$globals[\'after\']`!'],
            ['`Services::throttler()->check($key, 60, MINUTE)`', 'Token-bucket rate limiter', 'Protect `/login`, `/otp`, and `/api/*` against brute force (`429`).'],
        ],
        'app/Filters/ThrottleFilter.php',
        "<?php\nclass TokenBucketThrottler {\n    private array \$hits = [];\n    public function check(string \$ipKey, int \$limit): bool {\n        \$this->hits[\$ipKey] = (\$this->hits[\$ipKey] ?? 0) + 1;\n        return \$this->hits[\$ipKey] <= \$limit;\n    }\n}\n\n\$t = new TokenBucketThrottler();\nfor (\$i = 1; \$i <= 5; \$i++) {\n    \$ok = \$t->check('login_192.168.1.10', 3);\n    echo \"Attempt #{\$i}: \" . (\$ok ? '200 Allowed' : '429 Too Many Requests') . \"\\n\";\n}\n",
        ['Note that MD5/raw IP strings containing colons (IPv6 `::1`) can break certain cache key drivers in `Throttler::check()`—always hash the IP key via `md5($request->getIPAddress())`!'],
        [
            ['question' => 'Why should you pass md5($request->getIPAddress()) as the key to CI4\'s Throttler->check()?', 'answer' => 'IPv6 addresses contain ":" characters that are reserved in PSR-6/cache key specifications', 'options' => ['IPv6 addresses contain ":" characters that are reserved in PSR-6/cache key specifications', 'Throttler requires 32-byte keys minimum', 'md5 disables proxy headers', 'IPv4 addresses exceed 255 bytes']],
            ['question' => 'Which built-in CI4 filter blocks requests containing malformed UTF-8 sequences or null-byte control characters?', 'answer' => 'invalidchars', 'options' => ['invalidchars', 'honeypot', 'debugbar', 'pagecache']],
            ['question' => 'What happens when Filter::before() returns null?', 'answer' => 'The request proceeds normally to the next filter and Controller', 'options' => ['The request proceeds normally to the next filter and Controller', 'CI4 returns 500 Internal Server Error', 'CI4 redirects to /', 'The response body becomes empty']],
        ],
        'Test `md5("::1")` vs `"::1"` as a sanitized rate-limit cache key in the playground.'
    ),
    ci_ch(
        '3. Views, Layouts, Cells & Helpers',
        'Views, View Layouts (extend / section), Partials, View Cells & Context Escaping',
        'codeigniter 4 views, extend section renderSection, view_cell ci4, esc context escaping',
        'CI4 renders native PHP templates without proprietary template compilation overhead, supporting **View Layouts** (`$this->extend()`, `$this->section()`, `$this->renderSection()`), **Partials** (`$this->include()`), self-contained **View Cells** (`view_cell()`), and strict **`esc()`** output escaping.',
        [
            '**View Layouts**: Define master HTML chrome in `app/Views/layouts/main.php` with `<?= $this->renderSection(\'content\') ?>`, and extend it in child views via `<?= $this->extend(\'layouts/main\') ?>`.',
            '**Controlled View Cells (`php spark make:cell`)**: Encapsulate reusable mini-widgets (e.g., NotificationBellCell or RecentCoursesCell) that fetch and cache their own data (`cache=300`) independently of controllers!',
            '**`esc($val, $context)`**: Always escape every variable with the matching context: `\'html\'` (default), `\'attr\'`, `\'js\'`, `\'css\'`, or `\'url\'`!'
        ],
        [
            ['`<?= esc($title) ?>`', 'HTML-entity escapes untrusted text', 'Works on strings AND recursively escapes arrays of strings!'],
            ['`<?= esc($token, \'js\') ?>`', 'Escapes quotes, backslashes, and `<`/`>` for JS literals', 'Prevents inline `<script>` injection!'],
            ['`<?= view_cell(\'SidebarCell::render\', $params, 600) ?>`', 'Renders & caches widget HTML for 600 seconds', 'Fragment-caches expensive sidebar queries effortlessly!'],
        ],
        'ContextEscapeDemo.php',
        "<?php\n\$untrusted = '\" onmouseover=\"alert(1)';\necho 'Escaped for HTML attr: data-user=\"' . htmlspecialchars(\$untrusted, ENT_QUOTES, 'UTF-8') . \"\\\"\\n\";\n",
        ['Passing `[\'saveData\' => false]` as the third argument to `view(\'item\', $row, [\'saveData\' => false])` inside loops prevents variable bleed from one partial iteration into the next!'],
        [
            ['question' => 'What subtle bug does passing ["saveData" => false] to view() prevent when rendering partials in a loop?', 'answer' => 'Prevents optional variables from a previous iteration persisting into subsequent iterations where that key is omitted', 'options' => ['Prevents optional variables from a previous iteration persisting into subsequent iterations where that key is omitted', 'Disables database commits', 'Clears browser localStorage', 'Deletes log files']],
            ['question' => 'What feature makes CI4 View Cells ideal for expensive navbar or sidebar widgets?', 'answer' => 'They encapsulate their own data-fetching logic and support built-in TTL fragment caching', 'options' => ['They encapsulate their own data-fetching logic and support built-in TTL fragment caching', 'They run inside WebAssembly', 'They bypass HTTP headers', 'They replace MySQL tables']],
            ['question' => 'Can CI4\'s esc() helper accept an associative array of strings directly?', 'answer' => 'Yes—esc() recursively escapes every string element inside the array', 'options' => ['Yes—esc() recursively escapes every string element inside the array', 'No, it throws a TypeError on arrays', 'It converts arrays into integers', 'Only on PHP 7']],
        ],
        'Write a recursive `escRecursive(mixed $data)` function in the playground that escapes strings and traverses nested arrays.'
    ),
    ci_ch(
        '4. Database, Query Builder, Models & Entities',
        'Database Configuration, Multiple Connections, Query Builder & Transactions',
        'codeigniter 4 database, query builder ci4, groupStart whereIn, transStart transStrict',
        'CI4\'s **Query Builder** (`$db->table(\'orders\')`) constructs parameterized, SQL-injection-safe queries with chainable joins, parenthesized `groupStart()`/`groupEnd()` conditions, subqueries, batch inserts (`insertBatch()`), and automatic transactions.',
        [
            '**Automatic Identifier & Value Escaping**: `$builder->where(\'email\', $email)` binds `$email` safely; for grouped `OR` logic, always use `->groupStart()->where(...)->orWhere(...)->groupEnd()`.',
            '**`insertBatch()` & `updateBatch()`**: Inserts/updates hundreds of rows in chunked multi-value SQL statements (`100` rows per batch by default) instead of slow row-by-row loops.',
            '**Strict Transactions**: `$db->transStart(); ... $db->transComplete();` checks `$db->transStatus()` and rolls back automatically if any query inside failed!'
        ],
        [
            ['`->groupStart() ... ->groupEnd()`', 'Wraps `WHERE (... OR ...)` in SQL parentheses', 'Prevents `OR` operator precedence leaks!'],
            ['`->insertBatch($rows, null, 200)`', 'Bulk inserts in chunks of 200 rows', 'Massive speedup for CSV imports and batch logs.'],
            ['`$db->getLastQuery()`', 'Returns the exact last executed `Query` object', 'Use `(string)$db->getLastQuery()` to debug generated SQL!'],
        ],
        'QueryBuilderSim.php',
        "<?php\n// Demonstrating CI4 Query Builder parenthesized groupStart() SQL generation\n\$sql = \"SELECT id, title, price FROM courses WHERE is_active = 1 AND (title LIKE '%CI4%' OR keywords LIKE '%mvc%') ORDER BY id DESC LIMIT 10\";\necho \"Generated Parameterized SQL:\\n{\$sql}\\n\";\n",
        ['In CI4, `$db->transStrict(true)` is enabled by default—meaning if you have sequential transaction blocks and the first fails, subsequent blocks won\'t silently commit partial corrupted state unless explicitly reset!'],
        [
            ['question' => 'Which Query Builder method pair wraps complex OR conditions in SQL parentheses in CodeIgniter 4?', 'answer' => 'groupStart() and groupEnd()', 'options' => ['groupStart() and groupEnd()', 'openParen() and closeParen()', 'beginOr() and endOr()', 'subWhere() and endWhere()']],
            ['question' => 'How do you inspect the exact SQL string of the most recently executed query in CI4?', 'answer' => '(string) $db->getLastQuery()', 'options' => ['(string) $db->getLastQuery()', '$db->printSql()', 'Services::sqlLog()', '$db->rawString()']],
            ['question' => 'Which method inserts 500 records efficiently in chunked multi-row SQL statements instead of 500 round-trips?', 'answer' => '$builder->insertBatch($rows)', 'options' => ['$builder->insertBatch($rows)', '$builder->insertLoop($rows)', '$builder->pushAll($rows)', '$builder->saveMany($rows)']],
        ],
        'Chunk a 12-item array into batches of 5 using PHP\'s `array_chunk($rows, 5)` to see how `insertBatch()` splits payloads.'
    ),
    ci_ch(
        '4. Database, Query Builder, Models & Entities',
        'CI4 Models ($allowedFields, Timestamps, Soft Deletes, Callbacks) & Entity Classes',
        'codeigniter 4 model vs entity, allowedFields ci4, Entity class mutators casts, model callbacks',
        'Combining **`CodeIgniter\\Model`** (`$table`, `$primaryKey`, `$returnType = UserEntity::class`, `$allowedFields`, `$validationRules`, `$beforeInsert`) with **`CodeIgniter\\Entity\\Entity`** gives you clean domain objects with automatic change tracking (`hasChanged()`), type casting, and virtual getters/setters.',
        [
            '**Why Use `Entity` Instead of Plain Arrays?**: Setting `protected $returnType = OrderEntity::class;` lets you define `setPassword(string $raw)` mutators, `$casts = [\'meta\' => \'json-array\', \'is_paid\' => \'boolean\']`, and `$model->save($entity)`—which updates **ONLY the columns that actually changed** (`hasChanged()`)!',
            '**Empty Update Exception Gotcha**: If a user submits an Edit form without changing a single field and you call `$model->save($entity)` when `$entity->hasChanged()` is `false`, CI4 throws `DataException: There is no data to update` unless you check `if ($entity->hasChanged())` first!'
        ],
        [
            ['`$entity->fill($post)` + `if ($entity->hasChanged())`', 'Tracks dirty columns accurately', 'Prevents `DataException: There is no data to update` on unchanged form saves!'],
            ['`protected $casts = [\'options\' => \'json-array\']`', 'Auto-encodes/decodes JSON columns on Entity', 'Built-in casts include `int`, `bool`, `float`, `datetime`, `csv`, `json-array`.'],
            ['`protected $datamap = [\'full_name\' => \'name\']`', 'Maps public API property names to internal DB columns', 'Hides legacy column names cleanly without changing SQL tables!'],
        ],
        'EntityDirtyCheck.php',
        "<?php\nclass CourseEntity {\n    private array \$original;\n    public array \$attributes;\n    public function __construct(array \$row) { \$this->original = \$this->attributes = \$row; }\n    public function fill(array \$input): void {\n        foreach (\$input as \$k => \$v) \$this->attributes[\$k] = \$v;\n    }\n    public function hasChanged(): bool { return \$this->attributes !== \$this->original; }\n}\n\n\$course = new CourseEntity(['title' => 'CI4 Mastery', 'price' => 0]);\n\$course->fill(['title' => 'CI4 Mastery', 'price' => 0]); // Unchanged form submit!\necho 'Unchanged submit -> hasChanged()? ' . (\$course->hasChanged() ? 'Save to DB' : 'Skip DB UPDATE safely!') . \"\\n\";\n",
        ['**Real-World CI4 Production Bug Prevented**: Checking `if ($entity->hasChanged()) { $model->save($entity); }` saves thousands of customers from hitting a `500 DataException` when clicking "Save Profile" without modifying any input!'],
        [
            ['question' => 'What exception does CodeIgniter 4\'s Model->save($entity) throw if the user submits an edit form without modifying any field?', 'answer' => 'DataException ("There is no data to update") unless you check $entity->hasChanged() first', 'options' => ['DataException ("There is no data to update") unless you check $entity->hasChanged() first', 'PDOException duplicate key', 'PageNotFoundException', 'CSRFMismatchException']],
            ['question' => 'Which Entity property maps clean public property names (e.g. "createdAt") to legacy DB column names (e.g. "dt_created")?', 'answer' => 'protected $datamap', 'options' => ['protected $datamap', 'protected $aliases', 'protected $columnSwap', 'protected $fillable']],
            ['question' => 'Which built-in Entity cast decodes a JSON column directly into a PHP associative array?', 'answer' => 'json-array', 'options' => ['json-array', 'assoc_str', 'array_raw', 'serialize_obj']],
        ],
        'Modify `$course->fill([\'price\' => 499])` in the playground and verify `hasChanged()` flips to `true`.'
    ),
    ci_ch(
        '4. Database, Query Builder, Models & Entities',
        'Migrations ($forge), Seeders, Form Validation & Custom Rule Classes',
        'codeigniter 4 migrations forge, spark migrate, ci4 validation rules, custom validation rule ci4',
        '`Database\\Migration` uses CI4\'s **`$this->forge`** (`addField`, `addKey`, `addForeignKey`, `createTable`) for versioned DDL changes, while CI4\'s **Validation service** validates HTML forms, JSON bodies, and uploaded files (`uploaded`, `is_image`, `mime_in`, `max_size`).',
        [
            '**Strict vs Non-Strict Validation Rules**: CI4\'s default `Rules` (`Config\\Validation::$ruleSets`) includes `StrictRules`—prefer `StrictRules` because classic CI3 rules treated booleans/arrays loosely on JSON payloads!',
            '**File Upload Validation Rules**: Never inspect `$_FILES[\'avatar\'][\'type\']` manually; use `\'avatar\' => \'uploaded[avatar]|is_image[avatar]|mime_in[avatar,image/jpg,image/jpeg,image/png,image/webp]|max_size[avatar,2048]\'` which inspects real `finfo_file()` MIME bytes!',
            '**Safe Storage**: Move validated files via `$file->move(WRITEPATH . \'uploads/avatars\', $file->getRandomName())`.'
        ],
        [
            ['`php spark migrate` / `migrate:rollback`', 'Applies or reverts migration batch via `$this->forge`', 'Add `true` to `createTable(\'users\', true)` for `IF NOT EXISTS`.'],
            ['`$file->getRandomName()`', 'Generates timestamp + cryptographic random filename', 'Prevents filename collisions and path traversal!'],
            ['`StrictRules` (`CodeIgniter\\Validation\\StrictRules\\*`)', 'Type-safe validation for JSON APIs & forms', 'Default in modern CI4 `Config\\Validation`—never downgrade to non-strict rules.'],
        ],
        'CiValidationSim.php',
        "<?php\nfunction validateCiUpload(array \$fileMeta): array {\n    \$allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];\n    \$errors = [];\n    if (!in_array(\$fileMeta['detected_mime'], \$allowedMimes, true)) {\n        \$errors[] = 'Invalid real MIME type: ' . \$fileMeta['detected_mime'];\n    }\n    if (\$fileMeta['size_kb'] > 2048) {\n        \$errors[] = 'File exceeds 2048 KB limit.';\n    }\n    return \$errors ?: ['ok' => true, 'stored_as' => time() . '_' . bin2hex(random_bytes(6)) . '.webp'];\n}\n\necho 'Valid WebP:   ' . json_encode(validateCiUpload(['detected_mime' => 'image/webp', 'size_kb' => 420])) . \"\\n\";\necho 'Spoofed PHP:  ' . json_encode(validateCiUpload(['detected_mime' => 'text/x-php', 'size_kb' => 12])) . \"\\n\";\n",
        ['Notice storing private uploads inside `WRITEPATH . \'uploads\'` (outside `public/`!) lets you check user permissions in a Controller before streaming the file via `$this->response->download($path, null)`!'],
        [
            ['question' => 'How does CI4\'s mime_in[] validation rule verify uploaded file types safely?', 'answer' => 'It inspects actual file magic bytes via finfo rather than trusting the client-sent Content-Type header', 'options' => ['It inspects actual file magic bytes via finfo rather than trusting the client-sent Content-Type header', 'It checks only the last 3 letters of the filename', 'It asks the browser via JavaScript', 'It uploads the file to VirusTotal']],
            ['question' => 'Where should private user documents (invoices, KYC PDFs) be stored in CodeIgniter 4?', 'answer' => 'Inside WRITEPATH . "uploads/" (outside public/) and served via an authenticated Controller', 'options' => ['Inside WRITEPATH . "uploads/" (outside public/) and served via an authenticated Controller', 'Directly inside public/uploads/ with 0777 permissions', 'Inside system/Core/', 'Inside app/Views/']],
            ['question' => 'Which $this->forge parameter adds IF NOT EXISTS when creating a table in a CI4 Migration?', 'answer' => '$this->forge->createTable("orders", true)', 'options' => ['$this->forge->createTable("orders", true)', '$this->forge->safeCreate("orders")', '$this->forge->ignoreExists()', '$this->forge->createTable("orders", "soft")']],
        ],
        'Test `validateCiUpload()` with a `3500` KB PNG to verify multi-error reporting.'
    ),
    ci_ch(
        '5. Auth, Security, Caching & REST APIs',
        'Sessions, Flashdata, CodeIgniter Shield Auth (RBAC & Tokens) & Security Hardening',
        'codeigniter shield tutorial, auth()->user() inGroup can, ci4 session flashdata, csrf regenerate ci4',
        '**CodeIgniter Shield** (`codeigniter4/shield`) is CI4\'s official authentication & authorization suite—providing session login, email OTP/magic-link login, `auth()->user()->inGroup(\'admin\')` RBAC, `->can(\'courses.edit\')` granular permissions, and HMAC Personal Access Tokens for mobile/SPA APIs.',
        [
            '**Sessions Driver in Production**: Switch `Config\\Session::$driver` from `FileHandler` to `DatabaseHandler` or `RedisHandler` whenever scaling past 1 container!',
            '**Shield RBAC (`AuthGroups.php`)**: Define hierarchical roles (`superadmin`, `admin`, `instructor`, `student`) and permission matrices (`courses.create`, `courses.delete`) cleanly in config or DB.',
            '**CSRF Cookie vs Session Protection**: Switch `Config\\Security::$csrfProtection = \'session\'` (the modern recommended default over `\'cookie\'`) to bind CSRF tokens directly to the server-side user session!'
        ],
        [
            ['`auth()->loggedIn()` / `auth()->user()`', 'Global Shield helpers available in Controllers & Views', 'Check permissions via `auth()->user()->can(\'users.manage\')`.'],
            ['`$user->generateAccessToken(\'mobile-app\')`', 'Issues Shield Bearer token (`raw_token` shown once)', 'Protect `/api/*` routes with `[\'filter\' => \'tokens\']`!'],
            ['`session()->setFlashdata(\'success\', \'Saved!\')`', 'Persists message for **exactly the next HTTP request** only', 'Automatically cleared afterward—ideal for Post/Redirect/Get toasts!'],
        ],
        'ShieldRbacDemo.php',
        "<?php\n\$matrix = [\n    'admin'      => ['courses.create', 'courses.edit', 'courses.delete', 'users.manage'],\n    'instructor' => ['courses.create', 'courses.edit'],\n    'student'    => ['courses.enroll'],\n];\n\nfunction userCan(string \$group, string \$perm, array \$matrix): bool {\n    foreach (\$matrix[\$group] ?? [] as \$granted) {\n        if (\$granted === \$perm || (str_ends_with(\$granted, '.*') && str_starts_with(\$perm, substr(\$granted, 0, -1)))) {\n            return true;\n        }\n    }\n    return false;\n}\n\necho 'Instructor can courses.edit?   ' . (userCan('instructor', 'courses.edit', \$matrix) ? 'YES' : 'NO') . \"\\n\";\necho 'Instructor can courses.delete? ' . (userCan('instructor', 'courses.delete', \$matrix) ? 'YES' : 'NO') . \"\\n\";\n",
        ['Shield supports wildcard permissions like `\'admin.*\'` or `\'courses.*\'` in `Config\\AuthGroups`—just like `str_ends_with($granted, \'.*\')` in the example above!'],
        [
            ['question' => 'Why is Config\\Security::$csrfProtection = "session" preferred over "cookie" in CodeIgniter 4?', 'answer' => 'Session-based CSRF ties the token strictly to the server-side user session rather than a subdomain-shared cookie', 'options' => ['Session-based CSRF ties the token strictly to the server-side user session rather than a subdomain-shared cookie', 'Cookie CSRF disables HTTPS', 'Session CSRF does not require hidden inputs', 'Cookies expire after 5 seconds']],
            ['question' => 'Which Shield route filter authenticates stateless Authorization: Bearer <token> API requests?', 'answer' => 'tokens', 'options' => ['tokens', 'session', 'basic_http', 'honeypot']],
            ['question' => 'How long does session()->setFlashdata("notice", "Updated") persist?', 'answer' => 'Only for the single next HTTP request, after which CI4 clears it automatically', 'options' => ['Only for the single next HTTP request, after which CI4 clears it automatically', 'Until the browser closes', 'For 30 days', 'Forever in MySQL']],
        ],
        'Add `"superadmin" => ["courses.*", "users.*"]` to `$matrix` in the playground and test wildcard matching!'
    ),
    ci_ch(
        '5. Auth, Security, Caching & REST APIs',
        'RESTful ResourceController, Caching, Pagination, PHPUnit Testing & Production Deploy',
        'codeigniter 4 ResourceController, ci4 cache remember, pager links ci4, phpunit FeatureTestTrait, spark optimize',
        'Complete your CodeIgniter 4 engineering toolkit with **`ResourceController`** JSON APIs, **`cache()->remember()`** Redis/File caching, automatic **`paginate()`** links, **`FeatureTestTrait`** HTTP integration tests, and `php spark optimize` production deployment.',
        [
            '**`cache()->remember($key, $ttlSeconds, $callback)`**: Fetches cached results or executes the closure and stores the result atomically—remember CI4\'s `$ttl` is always in **seconds** (`3600` = 1 hour)!',
            '**Effortless Model Pagination**: `$courses = $courseModel->where(\'active\', 1)->paginate(12);` + `$pager = $courseModel->pager;` renders accessible pagination links via `<?= $pager->links() ?>`.',
            '**`CIUnitTestCase` + `DatabaseTestTrait` + `FeatureTestTrait`**: Simulate full HTTP requests (`$res = $this->post(\'/api/courses\', $payload); $res->assertStatus(201);`) in milliseconds without starting a web server!'
        ],
        [
            ['`$routes->presenter(\'posts\')` vs `$routes->resource(\'posts\')`', '`presenter` generates HTML form routes; `resource` generates stateless REST API routes', 'Use `resource` for JSON APIs and `presenter` for server-rendered HTML CRUD!'],
            ['`cache()->remember(\'nav_courses\', 600, fn)`', 'Caches query result for 600 seconds', 'Invalidate via `cache()->delete(\'nav_courses\')` inside Model `$afterInsert`/`$afterUpdate` callbacks!'],
            ['`php spark optimize`', 'Generates optimized autoloader classmap & config cache', 'Run in CI/CD deploy script; clear via `writable/cache/` cleanup before local dev!'],
        ],
        'RestPaginationEnvelope.php',
        "<?php\n\$totalRows = 94;\n\$perPage   = 20;\n\$page      = 2;\n\$envelope  = [\n    'status' => 200,\n    'meta'   => [\n        'current_page' => \$page,\n        'per_page'     => \$perPage,\n        'total_items'  => \$totalRows,\n        'total_pages'  => (int)ceil(\$totalRows / \$perPage),\n        'has_more'     => (\$page * \$perPage) < \$totalRows,\n    ],\n];\necho json_encode(\$envelope, JSON_PRETTY_PRINT);\n",
        ['**Important `$routes->presenter` vs `$routes->resource` Distinction**: Many developers confuse the two—`resource()` maps `PUT`/`DELETE` verbs (which HTML `<form>` tags don\'t natively send without `_method` spoofing), whereas `presenter()` maps clean `GET`/`POST` HTML CRUD endpoints!'],
        [
            ['question' => 'In CodeIgniter 4\'s Routes.php, what is the key difference between $routes->resource() and $routes->presenter()?', 'answer' => 'resource() maps RESTful API verbs (GET/POST/PUT/DELETE); presenter() maps HTML form-friendly GET/POST CRUD routes', 'options' => ['resource() maps RESTful API verbs (GET/POST/PUT/DELETE); presenter() maps HTML form-friendly GET/POST CRUD routes', 'presenter() returns XML only', 'resource() disables Filters', 'presenter() works only on CLI']],
            ['question' => 'What unit of time does CodeIgniter 4\'s cache()->save($key, $val, $ttl) use for $ttl?', 'answer' => 'Seconds (e.g. 3600 = 1 hour)', 'options' => ['Seconds (e.g. 3600 = 1 hour)', 'Milliseconds', 'Days', 'Minutes']],
            ['question' => 'Where is the cleanest place to invalidate a cached course list key in CI4 whenever a course is added or edited?', 'answer' => 'Inside Model $afterInsert, $afterUpdate, and $afterDelete callbacks', 'options' => ['Inside Model $afterInsert, $afterUpdate, and $afterDelete callbacks', 'Inside public/index.php on every GET', 'Inside robots.txt', 'Manually via FTP once a month']],
        ],
        'Test `RestPaginationEnvelope.php` on the final page (`$page = 5`) and verify `has_more` evaluates to `false`.'
    ),
    ci_ch(
        '6. Advanced CI4 Libraries & Production Capstone',
        'Form, URL, Text & Security Helpers + Creating Custom Helpers',
        'codeigniter 4 helpers, form_open csrf_field, url_title word_limiter, custom helper ci4',
        'CI4 **Helpers** are lightweight procedural utility files (`form_helper`, `url_helper`, `text_helper`, `filesystem_helper`) loaded on demand via `helper([\'form\', \'text\'])` or `$helpers` in `BaseController`.',
        [
            '**`form_open(\'orders/store\')`**: Automatically injects the hidden CSRF token `<input>` whenever the `csrf` filter is active!',
            '**`set_value(\'email\', $user->email)`**: Repopulates sticky form inputs cleanly across both Create and Edit screens while escaping HTML automatically.',
            '**Custom Helpers**: Create `app/Helpers/currency_helper.php` defining `if (!function_exists(\'format_inr\')) { ... }` and load via `helper(\'currency\')`.'
        ],
        [
            ['`form_open(\'login\')`', 'Generates `<form method="post">` + `csrf_field()`', 'Saves manually remembering `<?= csrf_field() ?>` on every POST form!'],
            ['`url_title($title, \'-\', true)`', 'Generates lowercase SEO-clean URL slug', 'Strips punctuation and collapses repeated hyphens automatically.'],
            ['`word_limiter($str, 30)`', 'Truncates article excerpts cleanly at word boundaries', 'Ideal for SEO meta descriptions and blog cards.'],
        ],
        'app/Helpers/currency_helper.php',
        "<?php\nfunction url_title_ci(string \$str): string {\n    return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower(\$str)), '-');\n}\necho 'Generated Slug: ' . url_title_ci('CodeIgniter 4: Complete REST & MVC Guide!') . \"\\n\";\n",
        ['Always wrap functions in custom `*_helper.php` files inside `if (!function_exists(\'...\'))` so child apps or tests can override helper functions safely!'],
        [
            ['question' => 'What security tag does CodeIgniter 4\'s form_open() automatically insert when CSRF protection is active?', 'answer' => 'The hidden CSRF token input (csrf_field())', 'options' => ['The hidden CSRF token input (csrf_field())', 'A Google reCAPTCHA iframe', 'An SSL certificate', 'A honeypot cookie']],
            ['question' => 'Where do you save a custom helper named invoice so helper("invoice") finds it?', 'answer' => 'app/Helpers/invoice_helper.php', 'options' => ['app/Helpers/invoice_helper.php', 'public/helpers/invoice.php', 'writable/invoice.php', 'system/invoice_helper.php']],
            ['question' => 'Which CI4 Text Helper function converts an article title into a clean lowercase hyphenated slug?', 'answer' => 'url_title($str, "-", true)', 'options' => ['url_title($str, "-", true)', 'make_uri($str)', 'slug_encode($str)', 'hyphenate($str)']],
        ],
        'Write a `format_inr(float $amount)` helper formatting `125000` as `₹1,25,000.00`.'
    ),
    ci_ch(
        '6. Advanced CI4 Libraries & Production Capstone',
        'Image Manipulation Library: Resize, Crop, Thumbnail, Watermark & WebP Conversion',
        'codeigniter 4 image manipulation, Services::image, fit resize convert webp, gd imagick ci4',
        '`Services::image()` wraps `GD2` and `ImageMagick` behind a fluent pipeline to resize, smart-crop (`fit()`), rotate, watermark, and transcode uploaded JPEGs/PNGs into modern **WebP** assets for Core Web Vitals speed.',
        [
            '**`resize()` vs `fit()`**: `resize(800, 600, true)` scales proportionally inside bounds, whereas `fit(400, 400, \'center\')` smart-crops exact square avatars without squashing faces!',
            '**`convert(IMAGETYPE_WEBP)`**: Cuts e-commerce product image weight by **30%–65%** compared to PNG/JPEG.',
            '**EXIF Orientation & Privacy**: Strip sensitive GPS EXIF metadata from user uploads when saving resized copies!'
        ],
        [
            ['`->fit(300, 300, \'center\')`', 'Proportional scale + center crop to exact dimensions', 'Best choice for user profile avatars and course thumbnails.'],
            ['`->convert(IMAGETYPE_WEBP)->save($dest, 82)`', 'Encodes WebP at 82% quality', 'Dramatically improves Largest Contentful Paint (LCP).'],
            ['`->text($watermark, [...])`', 'Overlays copyright text watermark', 'Supports opacity, font path, shadow, and alignment.'],
        ],
        'ImageAspectCalc.php',
        "<?php\n\$origW = 2400; \$origH = 1600;\n\$maxW  = 800;\n\$ratio = \$maxW / \$origW;\n\$newW  = (int)round(\$origW * \$ratio);\n\$newH  = (int)round(\$origH * \$ratio);\necho \"Proportional Resize: {\$origW}x{\$origH} -> {\$newW}x{\$newH} (Aspect Ratio Preserved)\\n\";\n",
        ['Always validate `is_image` **before** passing a file to `Services::image()`—corrupt or 10,000x10,000 pixel decompression-bomb files can spike GD memory unless dimensions (`max_dims`) are capped!'],
        [
            ['question' => 'What is the visual difference between ->resize(300, 300, true) and ->fit(300, 300, "center") on a 1200x800 photo?', 'answer' => 'resize() scales proportionally to 300x200; fit() crops and scales to fill an exact 300x300 square without distortion', 'options' => ['resize() scales proportionally to 300x200; fit() crops and scales to fill an exact 300x300 square without distortion', 'They produce identical 300x300 stretched images', 'fit() converts images to SVG', 'resize() deletes the original file']],
            ['question' => 'Which CI4 validation rule prevents "pixel flood / decompression bomb" memory spikes before GD processes an image?', 'answer' => 'max_dims[avatar,4000,4000]', 'options' => ['max_dims[avatar,4000,4000]', 'min_length[avatar,10]', 'alpha_dash', 'valid_url']],
            ['question' => 'Which modern image format constant reduces file sizes significantly via ->convert()?', 'answer' => 'IMAGETYPE_WEBP', 'options' => ['IMAGETYPE_WEBP', 'IMAGETYPE_BMP', 'IMAGETYPE_TIFF_II', 'IMAGETYPE_WBMP']],
        ],
        'Calculate proportional target dimensions when fitting a `1080x1920` portrait image inside a `600x600` bounding box.'
    ),
    ci_ch(
        '6. Advanced CI4 Libraries & Production Capstone',
        'Sending Transactional Emails (SMTP), HTTP CURLRequest Client & Webhooks',
        'codeigniter 4 email smtp, Services::email, Services::curlrequest, ci4 http client',
        'CI4 includes a full-featured **SMTP Email library** (`Services::email()`) with TLS, HTML templates, attachments, and BCC batching, alongside **`Services::curlrequest()`** for consuming external REST APIs (payment gateways, AI APIs, SMS).',
        [
            '**Always Check `$email->send(false)` While Debugging**: Passing `false` prevents auto-clearing parameters so `$email->printDebugger([\'headers\'])` can print exact SMTP handshake errors!',
            '**`CURLRequest` Timeouts (`timeout` & `connect_timeout`)**: Never make outbound API calls without explicit `\'timeout\' => 8` and `\'http_errors\' => false`—an unresponsive third-party API will otherwise hang all your PHP-FPM workers!'
        ],
        [
            ['`$email->send(false)` + `$email->printDebugger([\'headers\'])`', 'Preserves state to inspect SMTP transcript on failure', 'Default `send(true)` clears debug headers on success—only pass `false` when diagnosing failures!'],
            ['`Services::curlrequest([\'baseURI\' => ..., \'timeout\' => 5])`', 'Reusable HTTP API client', 'Set `\'http_errors\' => false` to handle `4xx`/`5xx` status codes gracefully without uncaught exceptions.'],
            ['`hash_equals($expectedHmac, $headerSig)`', 'Verifies incoming Razorpay/Stripe webhook authenticity', 'Never trust webhook JSON without constant-time HMAC signature verification!'],
        ],
        'WebhookVerifyCi4.php',
        "<?php\n\$rawWebhookBody = '{\"event\":\"payment.captured\",\"order_id\":\"ord_901\"}';\n\$secret = 'whsec_ci4_live_key';\n\$expected = hash_hmac('sha256', \$rawWebhookBody, \$secret);\necho \"Verified Webhook Signature: {\$expected}\\n\";\n",
        ['When sending emails inside a loop to multiple recipients, call `$email->clear(true)` at the start of each iteration (`true` also clears attachments from the previous user so User B never receives User A\'s PDF invoice!)!'],
        [
            ['question' => 'What critical privacy bug occurs if you call $email->clear() WITHOUT passing true inside a loop sending PDF invoices?', 'answer' => 'Attachments are NOT cleared unless clear(true) is passed, causing User B to receive User A\'s invoice attachment!', 'options' => ['Attachments are NOT cleared unless clear(true) is passed, causing User B to receive User A\'s invoice attachment!', 'The SMTP port changes to 25', 'Subject lines are uppercased', 'HTML mode switches to plain text']],
            ['question' => 'Why should every outbound Services::curlrequest() call configure an explicit timeout (e.g. 5–10s)?', 'answer' => 'To prevent stalled external APIs from tying up all PHP-FPM worker processes', 'options' => ['To prevent stalled external APIs from tying up all PHP-FPM worker processes', 'cURL refuses to run without timeout=0', 'DNS requires 60s minimum', 'HTTPS certificates expire without timeouts']],
            ['question' => 'How do you read the raw unparsed HTTP body in CI4 to compute a webhook HMAC signature?', 'answer' => '$this->request->getBody()', 'options' => ['$this->request->getBody()', '$this->request->getGet()', '$this->request->getCookie()', 'url_to()']],
        ],
        'Simulate a loop sending invoices to 3 users in PHP and verify clearing the `$attachments = []` array per iteration.'
    ),
    ci_ch(
        '6. Advanced CI4 Libraries & Production Capstone',
        'Events System, Structured Logging, Custom Spark CLI Commands & Cron Jobs',
        'codeigniter 4 events, log_message ci4, spark make:command, BaseCommand ci4',
        'Decouple cross-cutting concerns with **`CodeIgniter\\Events\\Events`** (`Events::on` / `Events::trigger`), record diagnostics via PSR-3 **`log_message()`**, and build scheduled maintenance tasks using **`BaseCommand`** (`php spark make:command`).',
        [
            '**`log_message($level, $msg, $context)`**: Supports RFC 5424 levels (`emergency`, `alert`, `critical`, `error`, `warning`, `notice`, `info`, `debug`) writing daily rotated logs in `writable/logs/log-YYYY-MM-DD.log`.',
            '**Log Threshold in Production**: Set `Config\\Logger::$threshold = 4` (errors & criticals only) in production so `debug` logs don\'t fill disk I/O!',
            '**Custom Spark Commands**: Extend `BaseCommand` (`$group`, `$name = \'reports:daily\'`, `run(array $params)`) and invoke cleanly from system crontab (`php /var/www/app/spark reports:daily`).'
        ],
        [
            ['`Events::on(\'user.registered\', fn($u) => ...)`', 'Registers decoupled event listener in `app/Config/Events.php`', 'Return `false` from a listener if you want to halt subsequent listeners on that event!'],
            ['`log_message(\'error\', \'Payment failed for #{id}\', [\'id\' => 42])`', 'Context-interpolated daily file logging', 'Never concatenate raw stack traces containing plaintext passwords into logs!'],
            ['`CLI::write(\'Done!\', \'green\')` / `CLI::table($tbody, $thead)`', 'Colored terminal output & ASCII tables in Spark commands', 'Makes custom CLI devops tools clean and readable.'],
        ],
        'SparkCliDemo.php',
        "<?php\nfunction formatLogLine(string \$level, string \$template, array \$ctx): string {\n    foreach (\$ctx as \$k => \$v) {\n        \$template = str_replace('{' . \$k . '}', (string)\$v, \$template);\n    }\n    return strtoupper(\$level) . ' - ' . date('Y-m-d H:i:s') . ' --> ' . \$template;\n}\necho formatLogLine('error', 'Order #{order_id} gateway timeout after {ms}ms', ['order_id' => 9042, 'ms' => 5012]) . \"\\n\";\n",
        ['Never expose `writable/logs/` over the web, and monitor disk space with log rotation when running high-traffic APIs.'],
        [
            ['question' => 'Where does CodeIgniter 4 write daily application log files by default?', 'answer' => 'writable/logs/', 'options' => ['writable/logs/', 'public/logs/', 'app/Logs/', 'system/Debug/']],
            ['question' => 'What happens if an Event listener registered via Events::on() explicitly returns false?', 'answer' => 'Remaining listeners queued for that event are halted and do not run', 'options' => ['Remaining listeners queued for that event are halted and do not run', 'The database rolls back automatically', 'Apache restarts', 'PHP throws an ErrorException']],
            ['question' => 'Which helper class renders colored output, progress bars, and ASCII tables inside custom Spark commands?', 'answer' => 'CodeIgniter\\CLI\\CLI', 'options' => ['CodeIgniter\\CLI\\CLI', 'CodeIgniter\\View\\Table', 'Config\\ConsoleColor', 'Spark\\Printer']],
        ],
        'Format an `info` log entry for `User #{uid} logged in from {ip}` using `formatLogLine()`.'
    ),
    ci_ch(
        '6. Advanced CI4 Libraries & Production Capstone',
        'Internationalization (i18n), Localization, Content Security Policy (CSP) & Production Checklist',
        'codeigniter 4 localization lang, Content Security Policy ci4, ci4 production deployment checklist',
        'Ship enterprise-ready CodeIgniter 4 applications globally with **`lang()`** multi-language translation packs (`app/Language/{locale}/`), nonce-based **Content Security Policy (`CSPEnabled`)**, and a zero-leak **Production Deployment Checklist**.',
        [
            '**Multi-Language `lang(\'Auth.loginSuccess\', [$name])`**: Supports ICU `MessageFormatter` pluralization and number/currency formatting via `intl`!',
            '**Nonce-Based CSP (`csp_script_nonce()`)**: Enabling `$CSPEnabled = true` in `Config\\App` generates a per-request cryptographic nonce for `<script <?= csp_script_nonce() ?>>`—blocking injected inline XSS scripts completely!',
            '**Final Go-Live Audit**: (1) `CI_ENVIRONMENT = production`, (2) `DocumentRoot` -> `public/`, (3) `$routes->setAutoRoute(false)`, (4) `composer install --no-dev -o`, (5) `php spark migrate --all`, (6) `php spark optimize`!'
        ],
        [
            ['`lang(\'Shop.cartItems\', [count($items)])`', 'Locale-aware ICU pluralized translation', 'Automatically uses `{locale}` negotiated from route or `Accept-Language`.'],
            ['`<?= csp_script_nonce() ?>`', 'Outputs `nonce="<random>"` matching `Content-Security-Policy` header', 'Blocks unauthorized inline `<script>` execution in browsers!'],
            ['`composer install --no-dev --optimize-autoloader`', 'Strips PHPUnit/Faker dev packages & builds fast classmap', 'Mandatory on every production server release.'],
        ],
        'ProdAuditCi4.php',
        "<?php\n\$audit = [\n    'CI_ENVIRONMENT === production' => true,\n    'DocumentRoot ends with /public' => true,\n    'AutoRoute disabled' => true,\n    'CSRF session protection active' => true,\n    'php spark optimize executed' => true,\n];\nforeach (\$audit as \$check => \$passed) {\n    echo (\$passed ? '[PASS] ' : '[FAIL] ') . \$check . \"\\n\";\n}\n",
        ['If you enable `php spark optimize` in production, remember to run `php spark optimize` **again** on every new deployment (or clear `writable/cache/FileLocatorCache`) so newly added classes/routes are discovered!'],
        [
            ['question' => 'Which CI4 helper outputs a per-request cryptographic nonce attribute for legitimate inline <script> tags when CSP is enabled?', 'answer' => 'csp_script_nonce()', 'options' => ['csp_script_nonce()', 'csrf_hash()', 'esc_script()', 'random_string()']],
            ['question' => 'After deploying new Controllers or Config files on a server where "php spark optimize" was previously run, what must your deploy script do?', 'answer' => 'Re-run php spark optimize (or clear FileLocator/config cache) so the new files are registered', 'options' => ['Re-run php spark optimize (or clear FileLocator/config cache) so the new files are registered', 'Reinstall MySQL', 'Delete writable/uploads/', 'Enable AutoRoute']],
            ['question' => 'Where are language translation arrays stored in a CodeIgniter 4 application?', 'answer' => 'app/Language/{locale}/', 'options' => ['app/Language/{locale}/', 'public/i18n/', 'writable/lang/', 'config/locales/']],
        ],
        'Review all 5 production audit checks and add a check verifying `writable/` is not inside `public/`.'
    ),
];

upsert_course_with_chapters($pdo, $courseMeta, $chapters);

