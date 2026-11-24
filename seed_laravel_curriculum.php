<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/seed_helper.php';

$courseMeta = [
    'title' => 'Laravel 11 & 12 Tutorial',
    'slug' => 'laravel-tutorial',
    'description' => 'A complete beginner Laravel 11 & 12 course. Start from “what is a website?”, then PHP basics, installation, routes, controllers, Blade, forms, validation, migrations, Eloquent CRUD, relationships, login, middleware, file uploads, a JSON API, tests, and deployment — while you build one real app: Nectra Notes.',
    'keywords' => 'laravel tutorial for beginners, learn laravel step by step, laravel 11, laravel 12, laravel project, nectra notes, eloquent crud, blade forms, laravel authentication, laravel middleware, laravel migrations',
];

function quiz(string $q, string $a, string $b, string $c, string $d): array
{
    return ['question' => $q, 'answer' => $a, 'options' => [$a, $b, $c, $d]];
}

function lesson(array $c): array
{
    $examples = [];
    foreach ($c['examples'] as $ex) {
        $examples[] = [
            'title' => $ex[0],
            'file' => $ex[1],
            'lang' => 'laravel',
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

$life = svg_pipeline_diagram('What happens when someone opens a Laravel page', [
    ['Browser', 'GET /notes'],
    ['public/index.php', 'Starts Laravel'],
    ['Route', 'Which function?'],
    ['Controller', 'Load notes'],
    ['Blade', 'HTML back'],
], '#ef4444');

$crud = svg_pipeline_diagram('Nectra Notes — the four database actions', [
    ['Create', 'POST /notes'],
    ['Read', 'GET /notes'],
    ['Update', 'PUT /notes/3'],
    ['Delete', 'DELETE /notes/3'],
], '#059669');

$chapters = [];

$chapters[] = lesson([
    'section' => '0. Start from zero',
    'title' => 'What a website is, and where Laravel fits',
    'keywords' => 'what is a website, http request response, what is laravel for beginners, mvc explained simply',
    'lead' => 'You do not need to know PHP yet. A website is a conversation: your browser asks for a page, a computer called a **server** answers with HTML. **Laravel** is the toolbox that organises that answer so you are not writing the same messy code on every page.',
    'sections' => [
        [
            'heading' => 'The conversation, one sentence at a time',
            'paragraphs' => [
                'When you type `nectranotes.test/notes` and press Enter, the browser sends a **request**. It says the method (`GET` means “please show me”), the path (`/notes`), and sometimes extra data such as a search word.',
                'The server runs PHP. Laravel reads the path, picks the matching **route**, runs your **controller** (the instructions), asks the **database** for rows if needed, and fills a **Blade** HTML template. That finished HTML is the **response**.',
            ],
            'bullets' => [
                '**HTML** is the skeleton the browser draws. **CSS** is colour and layout. **PHP** is the server language that decides what data to put in the HTML.',
                '**MVC** is only three jobs: **Model** talks to the database, **View** is the HTML, **Controller** receives the request and connects the other two.',
                'You will build **Nectra Notes**: a site where a person can write a note, see the list, edit it, and delete it. Every later chapter adds one piece of that app.',
            ],
        ],
    ],
    'visual' => $life,
    'rows' => [
        ['`GET`', '“Show me this page.” Nothing is saved.', 'Opening a link is always GET. Refreshing a GET page is safe.'],
        ['`POST`', '“Here is new data. Please save it.”', 'Creating a note uses POST. Refreshing a POST can submit twice if you are not careful — Laravel redirects after save so that does not happen.'],
        ['`Route`', 'A rule: this URL runs this code.', 'Without a route, Laravel answers 404 — page not found.'],
        ['`Blade`', 'An HTML file that can print PHP variables.', '`{{ $title }}` prints text and escapes it so a note cannot inject a script.'],
    ],
    'examples' => [
        ['Example 1 — a request is just data', 'request.php', 'Run this to see the four facts every request carries.', "\$request = [\n    'method' => 'GET',\n    'path'   => '/notes',\n    'user'   => 'guest',\n];\necho \"The browser asked to {\$request['method']} {\$request['path']}.\\n\";\necho \"Visitor is {\$request['user']}.\";"],
        ['Example 2 — MVC in one array', 'mvc.php', 'This is the same split Laravel uses, written as plain PHP so you can see the jobs.', "\$model = ['title' => 'Buy milk', 'body' => '2 litres'];\n\$controllerDecision = 'show the note page';\n\$view = '<h1>' . \$model['title'] . '</h1><p>' . \$model['body'] . '</p>';\necho \$controllerDecision . \"\\n\" . \$view;"],
    ],
    'micro' => [
        'A **URL** has a scheme (`https`), a host (`example.com`), a path (`/notes`), and an optional query (`?q=milk`). Laravel’s route matches the path, not the whole URL.',
        'Status **200** means OK, **302** means “go to this other URL”, **404** means no route, **419** in Laravel means the form’s security token was missing, **500** means your PHP crashed.',
        'Laravel does not replace HTML. It generates HTML (or JSON) and sends it. The browser still only understands HTML, CSS, and JavaScript.',
    ],
    'quiz' => [
        quiz('What does the browser send when you open a page?', 'An HTTP request', 'A CSS file only', 'A database table', 'An Artisan command'),
        quiz('In MVC, which part is the HTML the visitor sees?', 'The View', 'The Model', 'The route file only', 'The .env file'),
        quiz('Which method means “please save this new data”?', 'POST', 'GET', 'HEAD', 'OPTIONS'),
    ],
    'practice' => 'On paper, write the request for opening Nectra Notes: method, path `/notes`, and what the response should contain (a list of note titles).',
]);

$chapters[] = lesson([
    'section' => '0. Start from zero',
    'title' => 'PHP you must know before Laravel',
    'keywords' => 'php basics for laravel, variables arrays functions, php echo, beginner php before framework',
    'lead' => 'Laravel is PHP with batteries included. If variables, arrays, `if`, and functions feel new, this chapter is the whole language you need for the next lessons. You do not need classes yet.',
    'sections' => [
        [
            'heading' => 'Four ideas, then you can read Laravel code',
            'paragraphs' => [
                'A **variable** starts with `$`. `$title = "Buy milk";` stores text. PHP strings use quotes. A dot `.` joins strings: `"Hello " . $name`.',
                'An **array** holds many values. A list uses `[0]`, `[1]`. An associative array uses names: `$note["title"]`. A **function** is a named recipe you can call again. `if` chooses a path.',
            ],
            'bullets' => [
                'Every statement ends with a semicolon `;`. Forgetting it is the most common first error.',
                '`$notes` (the box) is not the same as `$note` (one item). Names are case-sensitive: `$Title` and `$title` are different.',
                '`echo` sends text to the browser. `var_dump($x)` shows the type and value while you are learning. Remove dumps before you ship.',
                'Arrays in modern PHP are written with `[]`. Old tutorials use `array()`. Both work. Use `[]`.',
            ],
        ],
    ],
    'rows' => [
        ['`$name = "Aarav";`', 'Create a variable and store text.', 'Quotes are required for text. `Aarav` without quotes is treated as a constant name and errors.'],
        ['`$n = 3;`', 'Store a number. No quotes.', '`"3"` is text. `$n + 1` works on numbers. `"3" + 1` also becomes 4 in PHP, which hides bugs — keep types honest.'],
        ['`foreach ($notes as $note)`', 'Loop over every item.', 'Inside the loop, `$note` is the current one. After the loop it still holds the last item.'],
        ['`function saved(string $title): string`', 'A function with a typed input and a typed output.', 'The `: string` is the return type. If you return a number, PHP 8 throws a TypeError. That is helpful.'],
    ],
    'examples' => [
        ['Example 1 — variables, if, and a function', 'php-basics.php', 'Run it. Change the title to an empty string and run again to see the other branch.', "function noteLabel(string \$title): string {\n    \$clean = trim(\$title);\n    if (\$clean === '') {\n        return 'Untitled note';\n    }\n    return \$clean;\n}\n\necho noteLabel('  Buy milk  ') . \"\\n\";\necho noteLabel('   ');"],
        ['Example 2 — a list of notes', 'php-array.php', 'This is the shape of data you will later store in a database.', "\$notes = [\n    ['id' => 1, 'title' => 'Buy milk', 'done' => false],\n    ['id' => 2, 'title' => 'Email Riya', 'done' => true],\n];\nforeach (\$notes as \$note) {\n    \$mark = \$note['done'] ? '[x]' : '[ ]';\n    echo \$mark . ' ' . \$note['title'] . \"\\n\";\n}\necho 'Count: ' . count(\$notes);"],
    ],
    'micro' => [
        '`==` converts types (`"1" == 1` is true). `===` does not (`"1" === 1` is false). In validation and IDs, use `===`.',
        '`trim()` removes spaces at both ends. Users paste titles with a trailing space. Always trim before you check “is this empty?”.',
        'A missing array key warns or throws depending on PHP settings. Use `$note["title"] ?? "Untitled"` — the `??` means “use the right side if the left is missing or null”.',
    ],
    'quiz' => [
        quiz('Which comparison does not convert types?', '===', '==', '=', '!='),
        quiz('What does trim() do?', 'Removes spaces from both ends of a string', 'Deletes the variable', 'Lowercases the text', 'Splits a string into an array'),
        quiz('How do you loop every note in $notes?', 'foreach ($notes as $note)', 'for $notes', 'loop ($notes)', 'each note in notes'),
    ],
    'practice' => 'Write a function `excerpt(string $body): string` that returns the first 20 characters plus `...` when the body is longer. Test it with “Buy milk” and with a long sentence.',
]);

$chapters[] = lesson([
    'section' => '0. Start from zero',
    'title' => 'Install the tools and create the Nectra Notes project',
    'keywords' => 'install laravel composer, php artisan serve, laravel new project, .env app key',
    'lead' => 'You need three tools: **PHP 8.2 or newer**, **Composer** (PHP’s package installer), and a code editor. Then one command creates the project. You are not downloading Laravel as a zip and editing random files.',
    'sections' => [
        [
            'heading' => 'Do these in order. Do not skip the checks.',
            'paragraphs' => [
                'Open a terminal. Run `php -v`. You need **8.2 or higher**. Run `composer -V`. If either command is “not found”, install that tool before continuing. A Laravel error later is often just “PHP was never on the PATH”.',
                'Create the app with `composer create-project laravel/laravel nectra-notes`. Go into the folder (`cd nectra-notes`) and run `php artisan serve`. Open `http://127.0.0.1:8000`. You should see the Laravel welcome page. That page proves PHP, Composer, and the framework are talking.',
            ],
            'bullets' => [
                '**`composer create-project`** copies Laravel and runs `composer install`, which downloads the `vendor/` folder. Never commit secrets. Do commit `composer.lock` so everyone gets the same versions.',
                '**`.env`** is your private settings file: database name, app URL, mail password. `.env.example` is the safe template that goes in git. `.env` does not.',
                '**`php artisan key:generate`** fills `APP_KEY`. The installer does this for you. If `APP_KEY` is empty, sessions and encrypted cookies break.',
                'Stop the server with **Ctrl+C** in that terminal. Closing the laptop lid does not always stop it.',
            ],
        ],
    ],
    'rows' => [
        ['`php -v`', 'Prints the PHP version.', 'Laravel 11 refuses to install on PHP 8.1 or older. The error mentions `php` version, not Laravel.'],
        ['`composer create-project laravel/laravel nectra-notes`', 'Makes a new folder with a full Laravel app.', 'Run it where you keep projects, not inside another Laravel project.'],
        ['`php artisan serve`', 'Starts a tiny local web server on port 8000.', 'This is for learning only. Real servers use Nginx or Apache pointed at the `public/` folder.'],
        ['`php artisan --version`', 'Proves the `artisan` file runs.', 'You must be inside the project folder. From the parent folder, artisan does not exist.'],
    ],
    'examples' => [
        ['Example 1 — pretend the installer checklist', 'install-check.php', 'A tiny checklist you can extend as you install.', "\$checks = [\n    'php'      => '8.3.0',\n    'composer' => '2.7',\n    'app_key'  => 'base64:set',\n];\nforeach (\$checks as \$tool => \$value) {\n    echo str_pad(\$tool, 10) . ' OK  ' . \$value . \"\\n\";\n}\necho \"Next: php artisan serve\";"],
        ['Example 2 — .env is just KEY=value lines', 'env-lines.php', 'Laravel reads these into config. Quotes matter when the value has spaces.', "\$lines = [\n    'APP_NAME' => 'Nectra Notes',\n    'APP_ENV'  => 'local',\n    'APP_URL'  => 'http://127.0.0.1:8000',\n];\nforeach (\$lines as \$key => \$value) {\n    echo \$key . '=' . \$value . \"\\n\";\n}"],
    ],
    'micro' => [
        'The URL `http://127.0.0.1:8000` and `http://localhost:8000` are the same machine. If one fails, try the other. A different project already using port 8000 makes `serve` fail — pass `--port=8001`.',
        'Do not edit files inside `vendor/`. The next `composer update` deletes your edits. Your code lives in `app/`, `routes/`, `resources/views/`, and `database/`.',
        'Windows: if `php` works in one terminal and not another, you opened a terminal from before you installed PHP. Close it and open a new one.',
    ],
    'quiz' => [
        quiz('Which command starts the learning web server?', 'php artisan serve', 'composer serve', 'php index.php', 'npm start laravel'),
        quiz('Which file must stay out of git because it holds passwords?', '.env', '.env.example', 'routes/web.php', 'composer.json'),
        quiz('Where must you run artisan commands?', 'Inside the Laravel project folder', 'In any folder on the computer', 'Inside vendor/', 'Inside public/'),
    ],
    'practice' => 'Install the tools, create `nectra-notes`, run `php artisan serve`, and write down the exact URL that showed the welcome page.',
]);

$chapters[] = lesson([
    'section' => '1. The project on disk',
    'title' => 'Every important folder, and the files you will actually touch',
    'keywords' => 'laravel folder structure beginners, public index.php, routes web.php, resources views, app http controllers',
    'lead' => 'A new Laravel folder looks huge. You will edit about six places while building Nectra Notes. Everything else is support. Learn the map before you add a route, or you will edit the wrong file and think Laravel is broken.',
    'sections' => [
        [
            'heading' => 'The six places for this course',
            'paragraphs' => [
                '`routes/web.php` lists page URLs. `app/Http/Controllers` holds the PHP classes that run for those URLs. `resources/views` holds Blade HTML. `app/Models` holds one class per database table. `database/migrations` holds the instructions that create tables. `.env` holds secrets.',
                '`public/` is the only folder the internet is allowed to see. `public/index.php` boots Laravel for every request. If you point a real server at the project root instead of `public/`, people can download your `.env`. That is a serious mistake.',
            ],
            'bullets' => [
                '`storage/` keeps logs, uploaded files, and compiled views. It must be writable. `storage/logs/laravel.log` is the first file you open when a page is a white error screen.',
                '`bootstrap/app.php` on Laravel 11+ registers routes and middleware. You rarely edit it in week one.',
                '`vendor/` is Composer’s download folder. `node_modules/` appears after you compile CSS. Both are huge and both stay out of git.',
                'Config files live in `config/`. You read them with `config("app.name")`. You do not call `env("APP_NAME")` inside random classes — only inside `config/` files. Config is cached in production; `env()` outside config then returns null.',
            ],
        ],
    ],
    'rows' => [
        ['`routes/web.php`', 'URLs that return HTML pages.', 'API URLs usually go in `routes/api.php` and automatically start with `/api`.'],
        ['`resources/views/notes/index.blade.php`', 'The HTML for the notes list.', 'The dot name `notes.index` means that file. Slashes become dots.'],
        ['`app/Models/Note.php`', 'The PHP class for one note row.', 'The table name Laravel expects is the plural snake case: `notes`.'],
        ['`database/migrations/...create_notes_table.php`', 'The recipe that creates the table.', 'You edit the migration before it has run on any shared database. After that, you add a new migration. You do not rewrite history.'],
    ],
    'examples' => [
        ['Example 1 — map a URL to a file in your head', 'files.php', 'Say the path out loud before you create a file. This script prints the map for Nectra Notes.', "\$map = [\n    'GET /notes'        => 'NoteController@index → views/notes/index.blade.php',\n    'GET /notes/create' => 'NoteController@create → views/notes/create.blade.php',\n    'POST /notes'       => 'NoteController@store → database table notes',\n];\nforeach (\$map as \$url => \$goes) {\n    echo \$url . \"\\n  \" . \$goes . \"\\n\";\n}"],
        ['Example 2 — config() versus env()', 'config-vs-env.php', 'Values belong in config. env() is only the source.', "\$env = ['APP_NAME' => 'Nectra Notes'];\n\$config = ['app.name' => \$env['APP_NAME']];\necho \$config['app.name'];"],
    ],
    'micro' => [
        'Blade files must end in `.blade.php`. A file named `index.php` in `resources/views` is not found when you call `view("notes.index")`.',
        'Class names are singular and Studly: `Note`. File name matches the class: `Note.php`. Table is plural: `notes`. Mixing these up is the number-one Eloquent “table not found” error.',
        'On Laravel 11 the default `routes/web.php` is small. If you copied a Laravel 10 tutorial that edits `app/Http/Kernel.php`, that file does not exist. Middleware is registered in `bootstrap/app.php`.',
    ],
    'quiz' => [
        quiz('Which folder is the web server allowed to expose?', 'public/', 'app/', 'storage/', 'vendor/'),
        quiz('Where do HTML page URLs go?', 'routes/web.php', 'app/Models', '.env', 'public/index.php only'),
        quiz('What does the view name notes.index point at?', 'resources/views/notes/index.blade.php', 'app/Notes/Index.php', 'routes/notes.php', 'public/notes/index.html'),
    ],
    'practice' => 'In your new project, open `routes/web.php`, `resources/views`, and `.env`. Write one sentence about what each one is for, without looking at this page.',
]);

$chapters[] = lesson([
    'section' => '1. The project on disk',
    'title' => 'Routes: the first Nectra Notes URLs',
    'keywords' => 'laravel routes for beginners, route get closure, 404, php artisan route:list',
    'lead' => 'A route is a sentence: “When a GET request comes for `/notes`, run this code.” Until a route exists, the URL is a 404. You will register the first three Nectra Notes URLs in this chapter, still with no database.',
    'sections' => [
        [
            'heading' => 'Read a route from left to right',
            'paragraphs' => [
                '`Route::get("/notes", function () { return view("notes.index"); });` means: method GET, path `/notes`, then a function. The function can `return` a string, a view, or a redirect. Laravel turns that return value into the HTTP response.',
                'The path does not include the domain and does not include `/index.php`. A leading slash is optional; be consistent and use one. Trailing slashes are not a different route. `/notes` and `/notes/` are the same.',
            ],
            'bullets' => [
                'Put the routes you are learning in `routes/web.php`. They get the `web` middleware group: cookies, session, and CSRF protection.',
                'Order matters when two patterns could match. Put `/notes/create` **before** `/notes/{note}` or the word `create` is captured as an id.',
                '`php artisan route:list` prints every route. If your new URL is missing, you edited the wrong file or forgot to save.',
                'A closure (the `function ()` written inside the route file) is fine for learning. The next chapter moves the same code into a controller so the route file stays a table of contents.',
            ],
        ],
    ],
    'visual' => $life,
    'rows' => [
        ['`Route::get`', 'Matches GET only.', 'A form that POSTs to a GET-only route returns 405 Method Not Allowed. That is not a 404.'],
        ['`return "Hello";`', 'Sends that exact text with status 200.', 'You can return an array; Laravel will JSON-encode it.'],
        ['`return view("welcome");`', 'Renders `resources/views/welcome.blade.php`.', 'If the view name is wrong you get “View [welcome] not found”, which names the missing view.'],
        ['`Route::view("/about", "about")`', 'A route that only returns a view, with no PHP logic.', 'Use it for static pages. Do not use it when you must load notes from the database.'],
    ],
    'examples' => [
        ['Example 1 — register the notes home', 'routes-home.php', 'The playground records the route instead of booting a full Laravel app. Read the line it prints.', "Route::get('/notes', function () {\n    return 'Nectra Notes — your list will render here';\n});\n\necho \"If the browser GETs /notes, that closure runs.\";"],
        ['Example 2 — three real URLs for the app', 'routes-three.php', 'Home, create form, and a single note. Notice create is its own path.', "Route::get('/', function () { return redirect('/notes'); });\nRoute::get('/notes', function () { return 'list'; });\nRoute::get('/notes/create', function () { return 'form'; });\nRoute::get('/notes/{note}', function () { return 'one note'; });"],
    ],
    'micro' => [
        '`redirect("/notes")` sends status 302 and a `Location` header. The browser then makes a second request, a GET. You will use this after saving a form.',
        'Returning nothing (`null`) from a route gives an empty 200 response. If the page is blank, check that you actually `return` the view.',
        'Routes are not re-read from disk on every request when you run `php artisan route:cache`. While learning, do not cache routes. If you did, run `php artisan route:clear`.',
    ],
    'quiz' => [
        quiz('What HTTP status do you get when no route matches?', '404', '200', '419', '302'),
        quiz('Why must /notes/create be registered before /notes/{note}?', 'Otherwise the word create is treated as the note id', 'Laravel sorts routes by length automatically always', 'Create only works in api.php', 'GET cannot have two segments'),
        quiz('Which command lists every route?', 'php artisan route:list', 'php artisan routes', 'composer routes', 'php artisan serve --routes'),
    ],
    'practice' => 'Add a GET route `/notes` that returns the text `Nectra Notes`. Open it in the browser. Then add `/notes/create` and confirm both URLs work.',
]);

$chapters[] = lesson([
    'section' => '1. The project on disk',
    'title' => 'Route parameters, verbs, and names',
    'keywords' => 'laravel route parameters, route model binding later, named routes route(), get post put delete',
    'lead' => 'A list page is not enough. Nectra Notes needs “the note with id 3”. That id is a **route parameter**. You also need different verbs on the same path: GET shows the form, POST saves it. **Names** let you change a URL later without hunting through every template.',
    'sections' => [
        [
            'heading' => 'Parameters are words in the path, not the query string',
            'paragraphs' => [
                '`/notes/3` puts `3` in the path. The route is `/notes/{note}`. The function receives it: `function (string $note)`. The variable name should match the braces. `{note}` lines up with `$note`. Laravel can cast it, but at this stage treat it as a string and reject anything that is not a number.',
                '`/notes?search=milk` is different. `search` is a **query parameter**. You read it later with `$request->query("search")`. Do not invent a route for every search word.',
            ],
            'bullets' => [
                '`Route::post("/notes", ...)` saves a new note. `Route::put("/notes/{note}", ...)` or `Route::patch` updates. `Route::delete("/notes/{note}", ...)` deletes. Browsers only send GET and POST from forms, so update and delete forms send POST plus a hidden `_method` field. Blade’s `@method("PUT")` prints that field.',
                '`->name("notes.show")` gives the route a name. In a view you write `route("notes.show", $note)` and Laravel builds `/notes/3`. If you later move the page to `/notebook/3`, the templates stay correct.',
                'A constraint `->whereNumber("note")` means letters in that spot do not match. `/notes/abc` then 404s instead of hitting your code.',
                'Never trust the id. Even if the URL says `3`, you will later check that this user owns note 3. The parameter is only “which row they asked for”.',
            ],
        ],
    ],
    'rows' => [
        ['`{note}`', 'One path segment.', '`{note}` does not match `/notes/3/edit`. That needs `{note}/edit`.'],
        ['`{note?}`', 'Optional segment.', 'Give the function argument a default: `string $note = ""` or PHP errors when it is missing.'],
        ['`route("notes.show", ["note" => 3])`', 'Builds the URL from the name.', 'A missing required parameter throws `UrlGenerationException`. The message names the parameter.'],
        ['`@method("DELETE")`', 'Hidden input so a POST form is treated as DELETE.', 'Without it, the form hits the POST route, often `store`, and you create a duplicate instead of deleting.'],
    ],
    'examples' => [
        ['Example 1 — read an id and reject junk', 'route-id.php', 'Only digits are accepted. This is the check you do before a database lookup.', "function showNote(string \$note): string {\n    if (!ctype_digit(\$note)) {\n        return '404 — note id must be a number';\n    }\n    return 'Showing note #' . \$note;\n}\necho showNote('3') . \"\\n\";\necho showNote('abc');"],
        ['Example 2 — name the routes', 'route-names.php', 'Register list, create, and show. Names are the stable API. Paths can change.', "Route::get('/notes', function () { return 'list'; })->name('notes.index');\nRoute::get('/notes/create', function () { return 'form'; })->name('notes.create');\nRoute::get('/notes/{note}', function (\$note) { return \"show \$note\"; })->name('notes.show');\nRoute::post('/notes', function () { return 'saved'; })->name('notes.store');"],
    ],
    'micro' => [
        '`{note}` and `{id}` are just names. Pick one and use it everywhere. This course uses `{note}` because route-model binding later will type-hint `Note $note` and the names must match.',
        'PUT and PATCH are not interchangeable in meaning: PUT replaces the whole resource, PATCH sends only changes. For a small notes form, either is fine if you validate every field you care about. Pick PATCH and stay with it.',
        'Generating a URL with a named route inside the route file, while that route is still being registered, can fail. Name them, then call `route()` from controllers and views, not halfway through `web.php`.',
    ],
    'quiz' => [
        quiz('Where does the 3 live in /notes/3?', 'In a route parameter {note}', 'In the .env file', 'Only in a cookie', 'In the view file name'),
        quiz('Why do delete forms use a hidden _method field?', 'Browsers cannot send DELETE from a normal form', 'Laravel deleted the delete() method', 'CSRF requires the word DELETE in the title', 'SQLite has no delete'),
        quiz('What does route("notes.show", 3) return?', 'A URL such as /notes/3', 'The note row from the database', 'A Blade file', 'An HTTP 302 by itself'),
    ],
    'practice' => 'Define named routes for index, create, store, show, edit, update, and destroy. Run `php artisan route:list` and check that create appears above `{note}`.',
]);

$chapters[] = lesson([
    'section' => '2. Controllers and Blade',
    'title' => 'Controllers: move Nectra Notes out of the route file',
    'keywords' => 'laravel controller beginner, php artisan make:controller, invokable controller, resource controller',
    'lead' => 'A route file full of functions becomes a junk drawer. A **controller** is a class whose methods are those functions. The route then only points at the method. Nectra Notes will use one `NoteController` with a method for each action.',
    'sections' => [
        [
            'heading' => 'Create it, then point the route at it',
            'paragraphs' => [
                'Run `php artisan make:controller NoteController`. Laravel creates `app/Http/Controllers/NoteController.php`. Add `public function index() { return view("notes.index"); }`. The route becomes `Route::get("/notes", [NoteController::class, "index"])->name("notes.index");`.',
                'You must `use App\\Http\\Controllers\\NoteController;` at the top of `web.php`, or write the full class name. If you forget, PHP says “Class NoteController not found”, which looks like a Laravel bug and is not.',
            ],
            'bullets' => [
                '**Resource controller:** `php artisan make:controller NoteController --resource` creates `index`, `create`, `store`, `show`, `edit`, `update`, and `destroy`. Then `Route::resource("notes", NoteController::class);` registers all seven routes with the correct verbs and names.',
                'Method names are a convention, not magic, until you use `Route::resource`. You can name a method `listNotes`, but then every tutorial and every teammate will be confused. Use the seven names.',
                'Controllers should stay thin. “Load notes, return the view” is right. “200 lines of HTML inside a string” is wrong — that belongs in Blade. “A 100-line pricing formula” belongs in a separate class later.',
                'One controller per resource. `NoteController` does not also send password resets. A second resource gets its own controller.',
            ],
        ],
    ],
    'rows' => [
        ['`index`', 'GET `/notes` — the list.', 'Variable passed to the view should be plural: `notes`.'],
        ['`create` / `store`', 'GET shows the empty form. POST saves.', 'People mix these up and put validation in `create`. Validation belongs in `store`.'],
        ['`show`', 'GET `/notes/{note}` — one note.', 'Do not use `show` for the edit form. That is `edit`.'],
        ['`edit` / `update` / `destroy`', 'Form, save changes, delete.', '`edit` is GET. `update` is PUT/PATCH. `destroy` is DELETE.'],
    ],
    'examples' => [
        ['Example 1 — the seven method names', 'controller-map.php', 'Print the map you are about to generate with --resource.', "\$methods = [\n    'index'   => 'GET    /notes',\n    'create'  => 'GET    /notes/create',\n    'store'   => 'POST   /notes',\n    'show'    => 'GET    /notes/{note}',\n    'edit'    => 'GET    /notes/{note}/edit',\n    'update'  => 'PUT    /notes/{note}',\n    'destroy' => 'DELETE /notes/{note}',\n];\nforeach (\$methods as \$method => \$route) {\n    echo str_pad(\$method, 8) . ' ' . \$route . \"\\n\";\n}"],
        ['Example 2 — a tiny index method', 'note-index.php', 'Same job as a controller method: prepare data, hand it to the view layer.', "class NoteController {\n    public function index(): string {\n        \$notes = [\n            ['title' => 'Buy milk'],\n            ['title' => 'Email Riya'],\n        ];\n        \$html = '';\n        foreach (\$notes as \$note) {\n            \$html .= '<li>' . \$note['title'] . \"</li>\\n\";\n        }\n        return \"<ul>\\n\" . \$html . '</ul>';\n    }\n}\n\$controller = new NoteController();\necho \$controller->index();"],
    ],
    'micro' => [
        'Artisan will not overwrite an existing controller unless you pass `--force`. If `make:controller` says the class already exists, open the file. Do not create `NoteController2`.',
        'A method must be `public`. A private `index` is invisible to the router and you get “Method does not exist” or an invokable error depending on version.',
        '`Route::resource` also registers a `create` URL. If you later add your own `Route::get("/notes/create")` below it, you now have two. Delete the duplicate. `route:list` shows both.',
    ],
    'quiz' => [
        quiz('Which method should save a new note?', 'store', 'create', 'index', 'show'),
        quiz('What does php artisan make:controller NoteController --resource create?', 'A controller with the seven CRUD methods', 'Only an index method', 'A database table', 'A Blade file'),
        quiz('Which URL shows the edit form?', 'GET /notes/{note}/edit', 'POST /notes/{note}/edit', 'GET /notes/edit', 'DELETE /notes/{note}'),
    ],
    'practice' => 'Generate `NoteController` with `--resource`, register `Route::resource("notes", NoteController::class);`, and open `/notes`. You should see whatever `index` returns, not a 404.',
]);

$chapters[] = lesson([
    'section' => '2. Controllers and Blade',
    'title' => 'Blade: print notes safely, branch, and loop',
    'keywords' => 'laravel blade beginner, blade echo escaped, @if @foreach, xss in blade',
    'lead' => 'Blade is HTML with a few extra tags. `{{ $note->title }}` prints the title. It also escapes `<` so a note titled `<script>` cannot run JavaScript in your visitor’s browser. That one brace style is the security lesson of this chapter.',
    'sections' => [
        [
            'heading' => 'Pass data in, print it out',
            'paragraphs' => [
                'From the controller: `return view("notes.index", ["notes" => $notes]);`. Inside the Blade file the key becomes a variable. You passed `notes`, so you write `$notes`. There is no automatic `$data` wrapper.',
                '`@foreach ($notes as $note)` … `@endforeach` loops. `@if (count($notes) === 0)` … `@else` … `@endif` handles the empty list. Always design the empty state. A blank page looks broken; “You have no notes yet” looks finished.',
            ],
            'bullets' => [
                '`{{ }}` escapes. `{!! !!}` does **not**. Use raw output only for HTML you generated yourself, never for the note body a user typed.',
                '`@forelse ($notes as $note)` … `@empty` … `@endforelse` is the loop plus the empty case in one tag. Prefer it for lists.',
                'Comments are `{{-- this is not sent to the browser --}}`. An HTML comment `<!-- -->` is sent to the browser. Do not put passwords in HTML comments.',
                'Blade is compiled to PHP in `storage/framework/views`. If you are sure you saved the file and the page is old, run `php artisan view:clear`.',
            ],
        ],
    ],
    'rows' => [
        ['`{{ $title }}`', 'Print escaped text.', 'A title of `<b>Hi</b>` shows the characters `<b>`, it does not become bold. That is what you want for user text.'],
        ['`{!! $html !!}`', 'Print raw HTML.', 'One unescaped note body is enough for a stored-XSS attack.'],
        ['`@foreach` / `@endforeach`', 'Loop.', 'Forgetting `@endforeach` makes the rest of the page part of the loop and the error points at the compiled PHP, not always your line. Count your end tags.'],
        ['`{{ ucfirst($title) }}`', 'You may call PHP functions inside the braces.', 'Keep it tiny. If it grows past one function, compute it in the controller and pass `$label`.'],
    ],
    'examples' => [
        ['Example 1 — escaped versus raw', 'blade-escape.blade.php', 'This is a Blade file. Run it. The playground compiles the echoes so you can see the safe output.', "<h1>{{ \$title }}</h1>\n<p>{{ \$body }}</p>"],
        ['Example 2 — empty list versus a list', 'notes-loop.php', 'The controller decides the message when there are no notes. The view will do this with @forelse.', "\$notes = [];\nif (count(\$notes) === 0) {\n    echo \"You have no notes yet. Write your first one.\";\n} else {\n    foreach (\$notes as \$note) {\n        echo '- ' . \$note['title'] . \"\\n\";\n    }\n}"],
    ],
    'micro' => [
        'Inside `{{ }}` you do not add an extra semicolon. It is an expression, not a statement. `{{ $title; }}` is a syntax error.',
        '`@if` conditions are PHP. Use `===` and `empty()` the same way you would in a controller. `@if ($note->done)` is enough for a boolean.',
        'View data is not shared across views unless you use a composer or `@include` with the variable in scope. If a partial says `$note` is undefined, you included it from a page that never received `$note`.',
    ],
    'quiz' => [
        quiz('Which Blade syntax escapes user text?', '{{ $title }}', '{!! $title !!}', '<?= $title ?>', '@raw($title)'),
        quiz('How does the view receive the list from the controller?', 'As the key you passed to view(), such as notes', 'It automatically loads every database table', 'From the .env file', 'From the route name'),
        quiz('What should an empty notes list show?', 'A clear sentence and a link to create a note', 'A white page', 'A 500 error', 'The .env contents'),
    ],
    'practice' => 'Create `resources/views/notes/index.blade.php` that lists `$notes` with `@forelse` and an empty message. Point `NoteController@index` at it with two fake notes, then with an empty array.',
]);

$chapters[] = lesson([
    'section' => '2. Controllers and Blade',
    'title' => 'One layout for every Nectra Notes page',
    'keywords' => 'laravel blade layout, @extends @section @yield, @include, components',
    'lead' => 'If you copy the navigation into every page, the day you rename “Notes” to “Notebook” you will miss a file. A **layout** holds the shared HTML once. Each page fills only the middle.',
    'sections' => [
        [
            'heading' => 'Layout, section, yield',
            'paragraphs' => [
                'Create `resources/views/layouts/app.blade.php`. It contains the doctype, `<head>`, the site name, and a nav. Where the page content should appear, write `@yield("content")`.',
                'The notes list starts with `@extends("layouts.app")` and wraps its HTML in `@section("content")` … `@endsection`. Laravel renders the child, then drops that section into the layout’s yield. The name `content` must match on both sides. `contents` versus `content` is a blank page with a correct nav, which is a confusing bug.',
            ],
            'bullets' => [
                'The `<title>` can be `@yield("title", "Nectra Notes")`. The second argument is the default when a page does not set a title.',
                '`@include("notes._item")` pastes another Blade file. A leading underscore is a human convention for “not a full page”. Laravel does not require it.',
                'A link in the nav should use `route("notes.index")`, not a hardcoded `/notes`, so named routes keep working.',
                'Put the create button in the layout or in the list page, not in both. Duplicate buttons are how forms get submitted to the wrong URL.',
            ],
        ],
    ],
    'rows' => [
        ['`@extends("layouts.app")`', 'This view is a child of the layout.', 'It must be the first Blade directive. HTML before `@extends` is ignored or errors depending on version. Keep it first.'],
        ['`@section("content")`', 'Names the block the layout will insert.', 'You can have several sections: `content`, `title`. Each needs `@endsection` or `@stop`.'],
        ['`@yield("content")`', 'The hole in the layout.', 'Two yields with the same name: only the placement you wrote is used; you simply duplicated the hole. Don’t.'],
        ['`@csrf`', 'Not part of layouts specifically, but every POST form in those pages needs it.', 'We add it in the next chapter. A layout cannot add it for you automatically.'],
    ],
    'examples' => [
        ['Example 1 — a layout skeleton', 'layout.blade.php', 'Run the Blade preview. Yields are removed so you can see the shared chrome.', "<!DOCTYPE html>\n<html>\n<head><title>Nectra Notes</title></head>\n<body>\n  <nav><a href=\"/notes\">All notes</a></nav>\n  <main>@yield('content')</main>\n</body>\n</html>"],
        ['Example 2 — which view is the child', 'view-names.php', 'Say the dot names out loud. These are the files you will create.', "\$views = [\n    'layouts.app'  => 'resources/views/layouts/app.blade.php',\n    'notes.index'  => 'resources/views/notes/index.blade.php',\n    'notes.create' => 'resources/views/notes/create.blade.php',\n    'notes.edit'   => 'resources/views/notes/edit.blade.php',\n    'notes.show'   => 'resources/views/notes/show.blade.php',\n];\nforeach (\$views as \$name => \$path) {\n    echo \$name . \"\\n  \" . \$path . \"\\n\";\n}"],
    ],
    'micro' => [
        '`@section("content")` … `@show` yields immediately and is rarely what you want in a child. Children use `@endsection`. The layout uses `@yield`, not `@section`, for the hole.',
        'If both the layout and the child contain `<html>`, you will see two documents nested. View source. The child should contain only the inner content.',
        'CSS belongs in one place, linked from the layout head. A `<style>` block copied into every section will drift. Laravel’s default Vite setup uses `@vite` in the layout. While learning you may use a single plain `<link>` — just do it once.',
    ],
    'quiz' => [
        quiz('What does @yield("content") mark?', 'The place where the child page is inserted', 'A database query', 'A POST route', 'The .env value'),
        quiz('What is the first line of a child Blade view?', '@extends("layouts.app")', '<html>', '@yield("content")', '<?php'),
        quiz('Why is a blank main area with a working nav usually caused by?', 'The section name does not match the yield name', 'PHP is not installed', 'The database is down', 'APP_KEY is missing'),
    ],
    'practice' => 'Build `layouts/app.blade.php` with a nav link to `route("notes.index")` and a yield named `content`. Make the notes list extend it. Check view source: one `<html>`, not two.',
]);

$chapters[] = lesson([
    'section' => '3. Forms for Nectra Notes',
    'title' => 'The create-note form, CSRF, and old input',
    'keywords' => 'laravel form csrf, blade form post, old() helper, request input',
    'lead' => 'A form is how the user sends a title and a body. The `create` method shows the form. The `store` method reads it. Between those two methods Laravel demands a **CSRF token**, a secret that proves the form came from your site and not from another website.',
    'sections' => [
        [
            'heading' => 'Build the form, then read it',
            'paragraphs' => [
                'The form tag needs `method="POST"` and `action="{{ route(\'notes.store\') }}"`. Inside, the first line is `@csrf`. Each field has a `name`. The name is the key you will read: `<input name="title">` becomes `$request->input("title")`. The `id` and the `<label for>` are for humans and screen readers. Laravel does not read the label.',
                'If validation fails you will send the user back. `old("title")` prints what they typed so they do not have to start over. On the first visit `old("title")` is empty. That is correct.',
            ],
            'bullets' => [
                'GET forms put data in the URL. Use GET only for search. Saving a note is POST so the body is not stored in browser history and bookmarks.',
                'Missing `@csrf` on a POST from a `web` route returns **419 Page Expired**. That is the most common “my form does nothing” report.',
                '`$request->all()` returns every field, including ones you did not draw. Do not pass `all()` straight into a database create. List the fields you accept.',
                'A textarea’s value goes between the tags, not in a `value` attribute: `<textarea name="body">{{ old("body") }}</textarea>`.',
            ],
        ],
    ],
    'rows' => [
        ['`@csrf`', 'Prints a hidden token input.', 'The token is per session. A form left open for many hours can expire. Submitting then shows 419. The user refreshes and tries again.'],
        ['`$request->input("title")`', 'The posted title, or null.', '`input` reads POST body and query string. `$request->query("title")` reads only the URL.'],
        ['`$request->string("title")->trim()`', 'A trimmed string object.', 'Call `->toString()` or cast `(string)` before you store it if a method demands a string.'],
        ['`old("title")`', 'Previous input after a redirect back.', 'It only works if the controller did `redirect()->back()->withInput()`. A plain `back()` forgets the text.'],
    ],
    'examples' => [
        ['Example 1 — the form HTML', 'create-form.blade.php', 'Look for method, action, csrf, and the name attributes. Those four are the whole contract.', "<form method=\"POST\" action=\"/notes\">\n  @csrf\n  <label for=\"title\">Title</label>\n  <input id=\"title\" name=\"title\" value=\"{{ \$title }}\">\n  <label for=\"body\">Body</label>\n  <textarea id=\"body\" name=\"body\">{{ \$body }}</textarea>\n  <button type=\"submit\">Save note</button>\n</form>"],
        ['Example 2 — read only the fields you asked for', 'read-input.php', 'Never copy the whole request into storage. Pick title and body.', "\$posted = ['title' => '  Buy milk ', 'body' => '2 litres', 'is_admin' => '1'];\n\$note = [\n    'title' => trim(\$posted['title']),\n    'body'  => trim(\$posted['body']),\n];\necho \$note['title'] . \"\\n\" . \$note['body'] . \"\\n\";\necho 'Ignored is_admin: ' . \$posted['is_admin'];"],
    ],
    'micro' => [
        'The button needs `type="submit"`. A `<button>` inside a form defaults to submit in HTML, but an explicit type survives the day you add a second button.',
        'Disable the submit button with JavaScript only after the request starts if you want to prevent double clicks. The real protection is a database unique key or an idempotency check, not the button. For notes, a double click may create two notes. That is acceptable for version one; do not pretend the button solved it.',
        '`name="title"` and `name="titles"` are different keys. The controller looking for `title` receives null, validation says “required”, and the user is sure they typed it. Check the `name`.',
    ],
    'quiz' => [
        quiz('What error appears when @csrf is missing on a web POST?', '419 Page Expired', '404 Not Found', '200 with an empty page always', 'A CSS error'),
        quiz('Which helper reprints the title after a failed save?', 'old("title")', 'env("title")', 'route("title")', 'config("title")'),
        quiz('Why not pass $request->all() into the database?', 'The user can add extra fields you never intended to save', 'all() is removed in Laravel 11', 'It deletes the table', 'It only works with SQLite'),
    ],
    'practice' => 'Create the notes form with title and body, `@csrf`, and `old()`. Submit it to `store` and `dd($request->only(["title", "body"]))` so you can see exactly what arrived. Remove the `dd` before the next chapter.',
]);

$chapters[] = lesson([
    'section' => '3. Forms for Nectra Notes',
    'title' => 'Validation: reject a bad note before you save it',
    'keywords' => 'laravel validation beginner, validate required min, @error directive, redirect back with errors',
    'lead' => 'Users submit empty titles, huge bodies, and strings of spaces. **Validation** is the list of rules that run before any save. If a rule fails, you do not insert a row. You redirect back with the errors and the old input.',
    'sections' => [
        [
            'heading' => 'Rules, then the error messages under the fields',
            'paragraphs' => [
                'In `store`: `$data = $request->validate(["title" => ["required", "string", "max:120"], "body" => ["required", "string", "min:10", "max:5000"]]);`. `validate` returns only the fields you listed, already checked. If it fails it throws, and Laravel turns that into a redirect back. You do not write the redirect yourself when you call `$request->validate`.',
                'In the Blade file, under the title input: `@error("title") <p>{{ $message }}</p> @enderror`. `$message` is the first failure for that field. `max:120` means 120 characters, not 120 words.',
            ],
            'bullets' => [
                '`required` fails on `null`, `""`, and an empty array. It does **not** fail on a string of spaces unless you also trim. Trim first or use a rule. `$request->validate` does not trim for you.',
                'Rules run in order but `required` is special: if the field is missing, later rules are skipped. `nullable` means “if they sent nothing, skip the other rules”. A note title should be `required`, not `nullable`.',
                'Do not validate in JavaScript only. JavaScript is a convenience. The server rules are the real gate. Anyone can POST with curl and skip your HTML.',
                'The same rules belong on `update`. Copy them into a private method `rules()` on the controller so create and edit cannot drift apart.',
            ],
        ],
    ],
    'rows' => [
        ['`required`', 'The field must be present and not empty.', 'A missing `name` attribute looks exactly like a failed `required` rule.'],
        ['`string`', 'Must be text, not an array.', 'A hacker posting `title[]=a` fails `string` instead of being stored as the word Array.'],
        ['`min:10` / `max:120`', 'Length limits for strings.', 'On a number field, `min:10` means the number 10, not 10 digits. The field type changes the meaning. Our title is a string.'],
        ['`@error("title")`', 'Prints only if that field failed.', 'The bag is called `$errors`. `@error` is the short way. It is empty on the first visit, so the paragraph is absent. Good.'],
    ],
    'examples' => [
        ['Example 1 — the same rules, in plain PHP', 'validate-note.php', 'This is what the rule list is doing. Laravel’s validate() throws and redirects; here we collect messages.', "function validateNote(array \$input): array {\n    \$errors = [];\n    \$title = trim(\$input['title'] ?? '');\n    \$body = trim(\$input['body'] ?? '');\n    if (\$title === '') {\n        \$errors['title'] = 'A title is required.';\n    } elseif (strlen(\$title) > 120) {\n        \$errors['title'] = 'Keep the title under 120 characters.';\n    }\n    if (strlen(\$body) < 10) {\n        \$errors['body'] = 'Write at least 10 characters.';\n    }\n    return \$errors;\n}\n\n\$errors = validateNote(['title' => '  ', 'body' => 'short']);\nforeach (\$errors as \$field => \$message) {\n    echo \$field . ': ' . \$message . \"\\n\";\n}"],
        ['Example 2 — a valid note passes', 'validate-ok.php', 'No lines printed means the note is safe to store. That is the branch where you insert.', "\$title = trim('Buy milk');\n\$body = trim('Two litres from the shop on the corner.');\n\$ok = \$title !== '' && strlen(\$title) <= 120 && strlen(\$body) >= 10;\necho \$ok ? \"Store this note: \$title\" : 'Do not store';"],
    ],
    'micro' => [
        '`max:120` counts characters. An emoji can be more than one byte. PHP’s `strlen` counts bytes; Laravel’s string `max` counts characters. Do not re-implement `max` with `strlen` if you care about Hindi or emoji. The plain example above is the idea, not a perfect clone.',
        'Failed validation does not reach the lines under `$request->validate`. If your `Note::create` still runs, you are not using the return value and you wrote a second path that ignores errors.',
        'You can customise the sentence with `->validate([...], ["title.required" => "Please name this note."])`. Write messages a person understands. “The title field is required” is acceptable. “validation.required” means you forgot to use the translator and printed a key.',
    ],
    'quiz' => [
        quiz('When does store() insert the row if you use $request->validate()?', 'Only when every rule passes', 'Always, then delete the row if invalid', 'Only on GET', 'Never — validate() inserts by itself'),
        quiz('What does max:120 mean on a string title?', 'At most 120 characters', 'At most 120 words', 'At most 120 notes in the table', 'The note id must be 120'),
        quiz('Which Blade directive prints a field error?', '@error("title")', '@csrf', '@yield("title")', '@method("PUT")'),
    ],
    'practice' => 'Add the title and body rules to `store`. Submit an empty form and a one-letter body. Each field should show its own message, and the title you did type should still be in the box.',
]);

$chapters[] = lesson([
    'section' => '4. The database',
    'title' => 'Tables, primary keys, and migrations',
    'keywords' => 'laravel migration beginner, php artisan make:migration, schema create notes table, sqlite mysql',
    'lead' => 'A **table** is a spreadsheet Laravel can trust. Each note is a **row**. The **primary key** `id` is a number that never means two notes. A **migration** is a PHP file that creates or changes the table, so every computer builds the same spreadsheet. You do not click around in a database app and hope you remember the columns.',
    'sections' => [
        [
            'heading' => 'Design the notes table before you write the form a second time',
            'paragraphs' => [
                'Nectra Notes needs `id`, `title`, `body`, `created_at`, and `updated_at`. `id` is an unsigned big integer that increments. `title` is a string of at most 120 characters. `body` is text, because it is long. The timestamps are filled by Eloquent when you save.',
                'Run `php artisan make:migration create_notes_table --create=notes`. Open the new file in `database/migrations`. Inside `up()`, the columns are listed. `php artisan migrate` runs every migration that has not run. `php artisan migrate:status` shows which ones have run. Your local Laravel 11 app uses SQLite by default, a single file `database/database.sqlite`. That is enough for this course.',
            ],
            'bullets' => [
                'The filename starts with a timestamp so migrations run in the order you created them. Do not rename a migration after it has run.',
                'Changing a column later is a **new** migration (`add_pinned_to_notes_table`), not an edit to the old file, once anyone else has migrated. On your laptop, before the app is shared, editing the original and running `migrate:fresh` is fine. `fresh` drops every table. Never run `fresh` on a production database.',
                '`down()` should undo `up()`. `dropIfExists("notes")` is the undo for create.',
                'Nullable means the cell may be empty. Title and body are not nullable. A pin flag you add later might default to false instead of null.',
            ],
        ],
    ],
    'rows' => [
        ['`$table->id()`', 'Primary key `id`, big integer, auto increment.', 'You almost never set `id` yourself. The database assigns it.'],
        ['`$table->string("title", 120)`', 'A short required string. The 120 matches validation.', 'If validation allows 200 and the column allows 120, MySQL will error or truncate depending on SQL mode. Make the numbers match.'],
        ['`$table->text("body")`', 'A long string.', 'Do not use `string()` for a multi-paragraph note. `string` defaults to 255 characters.'],
        ['`$table->timestamps()`', 'Adds `created_at` and `updated_at`.', 'They stay null if you insert with raw SQL and forget them. Eloquent sets them.'],
    ],
    'examples' => [
        ['Example 1 — the column list in words', 'notes-columns.php', 'Read this as the contract between the form and the table.', "\$columns = [\n    'id'         => 'number, chosen by the database',\n    'title'      => 'string, max 120, required',\n    'body'       => 'text, required',\n    'created_at' => 'datetime, set on insert',\n    'updated_at' => 'datetime, set on insert and update',\n];\nforeach (\$columns as \$name => \$rule) {\n    echo \$name . ' — ' . \$rule . \"\\n\";\n}"],
        ['Example 2 — migrate is a one-way door on shared servers', 'migrate-safety.php', 'fresh is for your laptop. Say the dangerous command out loud so you respect it.', "\$commands = [\n    'php artisan migrate'       => 'Safe: runs new migrations only',\n    'php artisan migrate:status'=> 'Safe: shows what already ran',\n    'php artisan migrate:fresh' => 'DESTROYS every table, then migrates. Laptop only.',\n];\nforeach (\$commands as \$cmd => \$meaning) {\n    echo \$cmd . \"\\n  \" . \$meaning . \"\\n\";\n}"],
    ],
    'micro' => [
        'If migrate says the table already exists, you ran it twice or created the table by hand. Do not “fix” that by editing random SQL. On a fresh learning database, `migrate:fresh` is the reset.',
        'SQLite is one file. If that file is missing, set `DB_CONNECTION=sqlite` and create an empty `database/database.sqlite`, or let Laravel 11’s installer create it. A wrong `DB_DATABASE` in `.env` makes migrate talk to a different database than the one you are staring at.',
        'Column order in the migration is the order in the table. It does not matter to Eloquent. Do not waste an afternoon reordering columns.',
    ],
    'quiz' => [
        quiz('What is a migration?', 'A PHP file that creates or changes tables in a repeatable way', 'A Blade layout', 'A route name', 'A Composer package'),
        quiz('Which command drops all tables and should never run in production?', 'php artisan migrate:fresh', 'php artisan migrate', 'php artisan route:list', 'php artisan serve'),
        quiz('Which column type fits a long note body?', 'text', 'string of 20', 'boolean', 'id()'),
    ],
    'practice' => 'Create and run the `notes` migration with id, title (120), body (text), and timestamps. Open the database and confirm the columns exist before you write Eloquent code.',
]);

$chapters[] = lesson([
    'section' => '4. The database',
    'title' => 'Eloquent: the Note model saves and loads rows',
    'keywords' => 'laravel eloquent beginner, php artisan make:model, fillable mass assignment, Note::create',
    'lead' => '**Eloquent** is Laravel’s way of turning a row into an object. `Note::create([...])` inserts. `Note::find(3)` loads. You almost never write the `INSERT` string yourself. The model name, the table name, and `$fillable` are the three things that must be right or the save silently does the wrong thing.',
    'sections' => [
        [
            'heading' => 'Make the model, allow the fields, then save',
            'paragraphs' => [
                '`php artisan make:model Note` creates `app/Models/Note.php`. Laravel assumes the table is `notes`. If your table were `notebook_entries` you would set `protected $table = "notebook_entries";`. Ours matches, so you add nothing.',
                'Mass assignment is a lock. `Note::create($data)` will only write fields listed in `protected $fillable = ["title", "body"];`. If `title` is missing from `$fillable`, the row is inserted with a null title and you stare at the database wondering why validation passed. Add both fields.',
            ],
            'bullets' => [
                '`Note::all()` returns every row. Fine for 20 notes. Later you will paginate. Do not teach yourself to load a million rows.',
                '`Note::find($id)` returns `null` when the id does not exist. `Note::findOrFail($id)` throws a 404. Use `findOrFail` on the show page so a bad link is a clean not-found, not a crash on `->title` of null.',
                '`$note->title = "New"; $note->save();` updates. `create` is for new rows. Calling `create` on edit makes a second note.',
                'Timestamps are automatic. You do not put `created_at` in `$fillable` and you do not set it in the form.',
            ],
        ],
    ],
    'visual' => $crud,
    'rows' => [
        ['`$fillable`', 'The only keys `create()` and `update()` will write.', 'The opposite list is `$guarded = ["id"]`, which allows everything else. Prefer `$fillable` so a new column is off until you opt in.'],
        ['`Note::create($data)`', 'INSERT and return the model, including its new id.', '`$data` must already be validated. create() is not a validator.'],
        ['`$note->update([...])`', 'UPDATE the existing row.', 'It also respects `$fillable`. A guarded field is ignored, not errored. Check the row if “nothing saved”.'],
        ['`findOrFail`', '404 when the id is unknown.', 'It does not check ownership. Another user’s note id still loads until you add that rule.'],
    ],
    'examples' => [
        ['Example 1 — fillable is a whitelist', 'fillable.php', 'is_admin is posted and ignored because it is not fillable. Title and body pass.', "\$fillable = ['title', 'body'];\n\$posted = ['title' => 'Buy milk', 'body' => 'Two litres.', 'is_admin' => true];\n\$row = [];\nforeach (\$fillable as \$field) {\n    \$row[\$field] = \$posted[\$field] ?? null;\n}\necho 'Saved fields: ' . implode(', ', array_keys(\$row)) . \"\\n\";\necho 'is_admin stored? ' . (array_key_exists('is_admin', \$row) ? 'yes' : 'no');"],
        ['Example 2 — create versus update', 'create-vs-update.php', 'The id tells you which one you are doing. No id yet means create.', "function planSave(?int \$id): string {\n    if (\$id === null) {\n        return 'Note::create — INSERT a new row';\n    }\n    return \"Note::findOrFail(\$id)->update — UPDATE that row\";\n}\necho planSave(null) . \"\\n\";\necho planSave(3);"],
    ],
    'micro' => [
        'Model class `Note`, file `Note.php`, table `notes`. Eloquent pluralises in English. A model named `Person` looks for `people` only if you use Laravel’s inflector carefully; `Note` → `notes` is regular and safe.',
        'Unretrieved property `$note->titel` (typo) returns null and does not always throw. If the title “does not save”, compare the spelling in `$fillable`, the form `name`, the validation key, and the column.',
        '`dd($note)` stops the request and prints the model. Use it to learn. Remove it. A `dd` left in `store` looks like a successful form that never redirects.',
    ],
    'quiz' => [
        quiz('Why would a validated title be saved as null?', 'title is missing from $fillable', 'The route is GET', 'Blade escaped it', 'CSRF blocked only the title'),
        quiz('What should the show page call when the id might be wrong?', 'findOrFail', 'all()', 'create()', 'env()'),
        quiz('Which method inserts a new row?', 'Note::create', '$note->update', 'findOrFail', '@csrf'),
    ],
    'practice' => 'Create the `Note` model with `$fillable = ["title", "body"]`. From `store`, `Note::create($data)` and redirect to `notes.show` with the new model. Refresh the database and see the row.',
]);

$chapters[] = lesson([
    'section' => '4. The database',
    'title' => 'Full CRUD: list, show, edit, and delete a note',
    'keywords' => 'laravel crud tutorial, notecontroller store update destroy, redirect route show',
    'lead' => 'You already have the pieces. This chapter wires them into the seven controller methods so Nectra Notes can create, read, update, and delete. After this chapter the app is usable by one person on your laptop.',
    'sections' => [
        [
            'heading' => 'Each method has one job',
            'paragraphs' => [
                '`index` does `Note::latest()->get()` and returns `notes.index`. `create` returns the form view. `store` validates, `Note::create`, then `return redirect()->route("notes.show", $note)`. `show` uses `findOrFail` and returns `notes.show`.',
                '`edit` loads the note and returns the form, prefilled with `$note->title`, not with `old()` unless validation failed. `update` validates, `$note->update($data)`, redirects to show. `destroy` calls `$note->delete()` and redirects to the list. Delete is not undoable. The button label should say “Delete note”, not “Submit”.',
            ],
            'bullets' => [
                'The edit form’s action is `route("notes.update", $note)` and it needs `@csrf` and `@method("PUT")`.',
                'The delete control is its own small form, not a link. Links are GET. GET must not delete. A crawler or a prefetch must not destroy data.',
                'After `store` and `update`, redirect. If you `return view(...)` instead, a browser refresh resubmits the POST and creates a second note.',
                'Prefill priority on edit: `old("title", $note->title)`. After a validation error, old input wins. Otherwise the saved title shows.',
            ],
        ],
    ],
    'visual' => $crud,
    'rows' => [
        ['`redirect()->route("notes.show", $note)`', '302 to the new note.', 'Passing the model uses its id. The route parameter name must be `note`.'],
        ['`@method("PUT")`', 'Spoofs PUT on the edit form.', 'Missing this hits `store` if the action URL is also wrong, or 405 if the URL is `/notes/{note}` and only PUT is registered.'],
        ['`$note->delete()`', 'Deletes that row.', 'It does not delete the PHP object in memory immediately in a way you should keep using. Redirect away. Do not render the deleted note.'],
        ['`old("title", $note->title)`', 'Old input, or the saved value.', 'On the create form there is no `$note`. Use `old("title")` only.'],
    ],
    'examples' => [
        ['Example 1 — choose the redirect target', 'redirect-map.php', 'Every successful write ends in a GET redirect. Trace store, update, and destroy.', "\$after = [\n    'store'   => 'redirect to notes.show for the new id',\n    'update'  => 'redirect to notes.show for the same id',\n    'destroy' => 'redirect to notes.index',\n    'failed validation' => 'redirect back with errors and input',\n];\nforeach (\$after as \$action => \$go) {\n    echo \$action . ' → ' . \$go . \"\\n\";\n}"],
        ['Example 2 — do not delete on GET', 'delete-verb.php', 'A function you can trust: only an explicit DELETE verb removes the row.', "function handle(string \$method, int \$id): string {\n    if (\$method !== 'DELETE') {\n        return \$method . ' /notes/' . \$id . ' does not delete';\n    }\n    return 'Deleted note ' . \$id;\n}\necho handle('GET', 3) . \"\\n\";\necho handle('DELETE', 3);"],
    ],
    'micro' => [
        '`latest()` orders by `created_at` descending. It is not magic sorting by title. If you want A–Z, use `orderBy("title")`.',
        'Two forms on the edit page (save and delete) are two `<form>` elements. Nested forms are invalid HTML. The browser will submit the outer one and your delete will “save” instead.',
        'If update appears to succeed but the text is unchanged, you are filling the form with `$note->title` and ignoring `old()`, or `$fillable` skipped the field, or you updated a different id than the form posted to. Check the form `action` in view source.',
    ],
    'quiz' => [
        quiz('Why redirect after store instead of returning the view?', 'So a refresh does not submit the form again', 'Blade cannot run after POST', 'SQLite requires a 302', 'CSRF only works with redirects'),
        quiz('Which verb deletes?', 'DELETE via a form with @method', 'GET /notes/{note}/delete as a plain link is the recommended way', 'GET /notes', 'Opening the edit page'),
        quiz('On the edit form, what should the title input show first?', 'old("title", $note->title)', 'Always an empty string', 'The .env app name', 'The route name'),
    ],
    'practice' => 'Finish all seven methods. Create a note, open it, edit the body, delete it, and confirm the list no longer shows it. Use the redirects from this chapter, not dd().',
]);

$chapters[] = lesson([
    'section' => '4. The database',
    'title' => 'Search, sort, and paginate the notes list',
    'keywords' => 'laravel paginate, where like search, orderBy, query string page',
    'lead' => 'Twenty notes fit on one page. Two hundred do not. **Pagination** splits the list. **Search** adds a `where` only when the user typed something. Both belong in `index`, and the rest of the CRUD stays as it is.',
    'sections' => [
        [
            'heading' => 'Start from the query, add conditions, then paginate',
            'paragraphs' => [
                '`$query = Note::query()->latest();` does not hit the database yet. `if ($search !== "") { $query->where("title", "like", "%".$search."%"); }` narrows it. `$notes = $query->paginate(10);` runs it and returns a page of 10 plus the total.',
                'In the view, loop `$notes` as before, then `{{ $notes->links() }}` prints the page links. Links keep the search word only if you used `->appends(["q" => $search])` or `withQueryString()`. Otherwise page 2 forgets the search and shows unfiltered notes. That feels like a random bug.',
            ],
            'bullets' => [
                'Read search from `$request->query("q")` because the search form should be GET. A shared URL ` /notes?q=milk&page=2` is useful. A POST search cannot be bookmarked.',
                '`like` with user input still needs binding. Eloquent binds the value. Do not concatenate into a raw SQL string.',
                '`paginate(10)` adds `LIMIT` and `OFFSET` and a second query for the count. `simplePaginate(10)` skips the count and only has Previous/Next. Use `paginate` so you can show “page 2 of 5”.',
                'Empty search should not add `where`. A `like "%%"` matches everything but is sloppy and can be slow. Skip the where.',
            ],
        ],
    ],
    'rows' => [
        ['`paginate(10)`', '10 rows per page, with page numbers.', 'The query parameter is `page`. Do not name your note field `page`.'],
        ['`withQueryString()`', 'Keeps `?q=milk` on page links.', 'Call it on the paginator: `$notes->withQueryString()`.'],
        ['`where("title", "like", "%{$q}%")`', 'Substring match.', 'A leading `%` cannot use a normal index. Fine for a personal notes app. Not fine for a million-row search. That needs a dedicated search tool later.'],
        ['GET form `name="q"`', 'Puts `q` in the URL.', 'The input value should be `{{ $search }}` so the box stays filled.'],
    ],
    'examples' => [
        ['Example 1 — only filter when there is a word', 'search-query.php', 'An empty search returns every title. “milk” returns one.', "\$notes = [\n    ['title' => 'Buy milk'],\n    ['title' => 'Email Riya'],\n    ['title' => 'Milk prices'],\n];\nfunction search(array \$notes, string \$q): array {\n    \$q = trim(\$q);\n    if (\$q === '') {\n        return \$notes;\n    }\n    return array_values(array_filter(\$notes, function (\$note) use (\$q) {\n        return stripos(\$note['title'], \$q) !== false;\n    }));\n}\nforeach (search(\$notes, 'milk') as \$note) {\n    echo \$note['title'] . \"\\n\";\n}"],
        ['Example 2 — page 2 is items 11 through 20', 'paginate-math.php', 'OFFSET is (page - 1) times per page. Page 1’s offset is 0, not 1.', "function offset(int \$page, int \$perPage): int {\n    \$page = max(1, \$page);\n    return (\$page - 1) * \$perPage;\n}\necho 'Page 1 offset ' . offset(1, 10) . \"\\n\";\necho 'Page 2 offset ' . offset(2, 10);"],
    ],
    'micro' => [
        'Validate `q` as an optional string with `max:100`. A 100kb query string is not a search, it is abuse.',
        '`latest()->paginate()` is fine. `paginate()->latest()` is wrong because `paginate` already executed the query. Add order before paginate.',
        'The list view receives a paginator, not a plain array. `@foreach` still works. `count($notes)` on a paginator is the count on this page, not the total. Use `$notes->total()` for “42 notes”.',
    ],
    'quiz' => [
        quiz('Why is the search form GET?', 'So the search URL can be shared and kept on page 2', 'GET is required for @csrf', 'POST cannot send text', 'Eloquent only reads GET'),
        quiz('What does withQueryString() keep?', 'The current query parameters, such as q, on pagination links', 'The database password', 'Old form input after validation', 'The CSRF token in the URL'),
        quiz('What is the offset for page 1 when 10 notes are shown?', '0', '1', '10', '11'),
    ],
    'practice' => 'Add a GET search box named `q` on the notes list, filter the title, paginate 5 per page, and confirm page 2 of a search still shows only matches.',
]);

$chapters[] = lesson([
    'section' => '5. Users and access',
    'title' => 'A user has many notes',
    'keywords' => 'laravel hasmany belongsto, foreign key user_id, eager load with()',
    'lead' => 'One person, many notes. In the database that is a `user_id` column on `notes`. In Eloquent, `User` **hasMany** notes and `Note` **belongsTo** a user. This is the step that turns a single-player demo into an app where accounts make sense.',
    'sections' => [
        [
            'heading' => 'The foreign key is just a column that points at users.id',
            'paragraphs' => [
                'A new migration adds `$table->foreignId("user_id")->constrained()->cascadeOnDelete();` to `notes`. `foreignId` is an unsigned big integer. `constrained()` points it at `users.id`. `cascadeOnDelete` means if the user is deleted, their notes are deleted too. Know that you chose that. The alternative is `nullOnDelete()` or restricting the delete.',
                'On `User`: `public function notes() { return $this->hasMany(Note::class); }`. On `Note`: `public function user() { return $this->belongsTo(User::class); }`. The method name `notes` is plural. The method name `user` is singular. Laravel uses those names when you write `$user->notes` and `$note->user`.',
            ],
            'bullets' => [
                'When you create a note for the logged-in person: `$request->user()->notes()->create($data)`. That sets `user_id` for you. Do not take `user_id` from the form. A hidden `user_id` input is an invitation to save notes into someone else’s account.',
                '`$user->notes` loads them. Doing that inside a loop over many users is the N+1 problem: 1 query for users plus N queries for notes. `$users = User::with("notes")->get()` loads notes in one extra query.',
                'Existing notes have no `user_id` until you migrate. On a learning database, `migrate:fresh` and register again. Do not invent ids by hand.',
                'The relationship method (`notes()`) returns a query you can still filter: `$user->notes()->where(...)`. The property (`$user->notes`) is the loaded collection. Use the method when you want `create` or extra `where`. Use the property when you want the rows you already loaded.',
            ],
        ],
    ],
    'rows' => [
        ['`hasMany(Note::class)`', 'This user owns many notes. Foreign key lives on notes.', 'Default foreign key is `user_id`. If you named it `owner_id` you must pass it as the second argument. Don’t rename it.'],
        ['`belongsTo(User::class)`', 'This note points back at one user.', 'Method name should be `user`. `author()` also works if you set the foreign key, but then `$note->user` is null and you will be confused. Use `user`.'],
        ['`$user->notes()->create($data)`', 'Inserts a note with this user_id.', 'The user must already exist and be saved, so they have an id.'],
        ['`User::with("notes")`', 'Eager load.', 'The string is the method name. `with("note")` looks for a method you do not have and throws.'],
    ],
    'examples' => [
        ['Example 1 — who owns which row', 'ownership.php', 'The note stores the user’s id. The user does not store a list of ids.', "\$users = [1 => 'Aarav', 2 => 'Riya'];\n\$notes = [\n    ['title' => 'Buy milk', 'user_id' => 1],\n    ['title' => 'Tax file', 'user_id' => 2],\n    ['title' => 'Call mum', 'user_id' => 1],\n];\nforeach (\$notes as \$note) {\n    if (\$note['user_id'] === 1) {\n        echo \$users[1] . ': ' . \$note['title'] . \"\\n\";\n    }\n}"],
        ['Example 2 — never trust a posted user id', 'ignore-posted-owner.php', 'The session user wins. The hidden field is discarded.', "\$sessionUserId = 1;\n\$posted = ['title' => 'Buy milk', 'body' => 'Two litres please.', 'user_id' => 2];\n\$row = [\n    'title'   => \$posted['title'],\n    'body'    => \$posted['body'],\n    'user_id' => \$sessionUserId,\n];\necho 'Stored owner ' . \$row['user_id'] . ' (not ' . \$posted['user_id'] . ')';"],
    ],
    'micro' => [
        'Add `user_id` to the note’s creation path, not to `$fillable` if you always set it via the relationship. If it is fillable and you `Note::create($request->all())`, you are back to the hidden-field bug.',
        '`cascadeOnDelete` is invisible until you delete a user and their notes vanish. If notes should survive, do not copy this line from a tutorial without reading it.',
        'Relationship names are methods. Forgetting the parentheses in `notes()->create` and writing `$user->notes->create` fails because a collection has no Eloquent `create` that sets the foreign key the way the relation does.',
    ],
    'quiz' => [
        quiz('Where is the foreign key stored?', 'On the notes table, as user_id', 'On the users table, as a comma-separated list', 'In the Blade layout', 'In APP_KEY'),
        quiz('How should store() set the owner?', '$request->user()->notes()->create($data)', 'From a hidden user_id input', 'Always user id 1', 'It is not stored'),
        quiz('What does User::with("notes") avoid?', 'One extra query per user', 'The need for a migration', 'CSRF checks', 'Validation'),
    ],
    'practice' => 'Add `user_id` to notes, define both relationship methods, and create a note through `$request->user()->notes()->create($data)` once login exists. Until login exists, create a user in a seeder and use that user’s relationship in a temporary test route.',
]);

$chapters[] = lesson([
    'section' => '5. Users and access',
    'title' => 'Register, log in, and log out',
    'keywords' => 'laravel breeze auth beginner, session login, hashed password, auth middleware overview',
    'lead' => 'Passwords are never stored as typed. Laravel stores a **hash**. Login checks the hash, then puts the user id in the **session**. Logout forgets the session. You can install this with **Breeze** instead of writing the hashing yourself. You still need to understand the steps, because you will debug them.',
    'sections' => [
        [
            'heading' => 'What you install, and what is actually happening',
            'paragraphs' => [
                'From the project: `composer require laravel/breeze --dev`, then `php artisan breeze:install blade`, then `php artisan migrate`, then `npm install && npm run build` if the installer asks. Breeze adds register, login, logout, and a `users` table migration if you do not already have one. This course uses Blade, not the React stack. Choose the Blade option when it asks.',
                'Registration validates name, email, and password, hashes the password, inserts the user, and logs them in. Login finds the user by email and calls `Hash::check`. A wrong password does not say “this email exists” in a careful app; Breeze’s default message is a generic “credentials do not match”, which is what you want.',
            ],
            'bullets' => [
                'Session data lives on the server. The browser only holds a cookie with a session id. That is why login “sticks” when you click around.',
                '`password` must not be in any `$fillable` you pass to a random `create` from a form besides the registration action. Breeze handles registration. Do not add a password field to the note form.',
                'The logout route is POST, with CSRF. A GET logout can be triggered by another site with an image tag. Breeze uses POST. Leave it.',
                'After login, send people to `route("notes.index")`. Change `RouteServiceProvider` or the `HOME` constant / `redirectTo` that your Breeze version uses. Laravel 11 often sets this in `app/Providers` or the route named `dashboard`. Point “home” at your notes list so the project feels like one app.',
            ],
        ],
    ],
    'rows' => [
        ['`Hash::make($password)`', 'One-way hash for storage.', 'You cannot decrypt it to “show them their password”. You can only reset it.'],
        ['`Hash::check($typed, $hash)`', 'True if the typed password matches.', 'You compare using this function, not `===` against the column.'],
        ['`auth()->user()`', 'The logged-in user, or null.', 'In a controller, `$request->user()` is the same person.'],
        ['Logout POST', 'Deletes the session login.', 'After logout, back-button may show a cached notes page. A refresh should bounce to login once notes are protected.'],
    ],
    'examples' => [
        ['Example 1 — never store the raw password', 'password-store.php', 'What you save is not what they typed. The playground only shows the idea, not a real bcrypt hash.', "\$typed = 'correct horse';\n\$stored = 'hash:' . hash('sha256', \$typed);\n\$loginAttempt = 'correct horse';\n\$ok = hash_equals(\$stored, 'hash:' . hash('sha256', \$loginAttempt));\necho 'Stored value starts with hash, not the password.' . \"\\n\";\necho \$ok ? 'Login allowed' : 'Login rejected';"],
        ['Example 2 — where a visit should land', 'after-login.php', 'Guests go to login. Members go to the notes list.', "function landing(bool \$loggedIn): string {\n    if (!\$loggedIn) {\n        return 'redirect to /login';\n    }\n    return 'redirect to /notes';\n}\necho landing(false) . \"\\n\";\necho landing(true);"],
    ],
    'micro' => [
        'Email is stored lowercase. If you do not normalise it, `Aarav@Site.com` and `aarav@site.com` become two users. Breeze lowercases. If you write your own registration, `strtolower` the email before the unique check.',
        'The `password_confirmation` field must be named exactly that for the `confirmed` rule. `passwordConfirm` will not match.',
        'Do not log passwords. `storage/logs/laravel.log` sometimes contains request data if you dump the request. Never `dd($request->all())` on the login form in a shared environment.',
    ],
    'quiz' => [
        quiz('How is a password saved?', 'As a one-way hash', 'As plain text in .env', 'Inside the note body', 'In a cookie the JavaScript can read'),
        quiz('Why is logout a POST?', 'A GET logout can be fired by another website', 'POST is faster', 'GET cannot use routes', 'Blade forbids forms'),
        quiz('What does auth()->user() return when nobody is logged in?', 'null', 'The first user in the table', 'A guest Note model', 'The APP_KEY'),
    ],
    'practice' => 'Install Breeze with the Blade stack, migrate, register a user, and change the post-login redirect so you land on `/notes`.',
]);

$chapters[] = lesson([
    'section' => '5. Users and access',
    'title' => 'Middleware and policies: only the owner touches a note',
    'keywords' => 'laravel middleware auth guest, policy authorize update, 403 forbidden',
    'lead' => 'Login is not enough. Right now a logged-in person who guesses `/notes/4` might open someone else’s note. **Middleware** answers “are they logged in?”. A **policy** answers “is this their note?”. Both must exist.',
    'sections' => [
        [
            'heading' => 'Two different questions',
            'paragraphs' => [
                'Put `->middleware("auth")` on the notes resource, or group the routes: `Route::middleware("auth")->group(function () { Route::resource(...); });`. Guests who open `/notes` are redirected to login. That is middleware. It does not look at the note.',
                'A policy method `update(User $user, Note $note): bool` returns `$user->id === $note->user_id`. Same for `delete`. In the controller: `$this->authorize("update", $note);` before you save. A false policy becomes **403 Forbidden**. 403 means “I know who you are, and you still cannot”. 401 or a login redirect means “I do not know who you are”.',
            ],
            'bullets' => [
                'Also scope the list: `Note::where("user_id", $request->user()->id)` or `$request->user()->notes()`. Authorization on `show` does not hide titles on the index. If index loads `Note::all()`, you leaked every title.',
                '`authorize` on `show`, `edit`, `update`, and `destroy`. `create` only needs `auth`, because there is no note yet.',
                'Generate with `php artisan make:policy NotePolicy --model=Note`. Registering is automatic in Laravel 11 when the policy name follows the model name. If `authorize` says the action is not defined, the method is missing or named wrong.',
                'Do not only hide the edit button. Hiding a button is cosmetic. The POST still works unless the policy runs on the server.',
            ],
        ],
    ],
    'rows' => [
        ['`middleware("auth")`', 'Must be logged in.', 'It redirects guests to login. It does not return 403.'],
        ['`middleware("guest")`', 'Must be logged out. Used on the login page.', 'A logged-in user opening `/login` is redirected home. That is why it feels like you “cannot” open login while signed in.'],
        ['`$this->authorize("update", $note)`', 'Runs the policy.', 'Call it before `update()`. Afterwards is too late; the row is already changed.'],
        ['403 vs 404', '403 admits the note exists. 404 hides it.', 'For private notes, some apps `findOrFail` only inside the user’s notes, so other people’s ids are 404. That is slightly safer. Either policy 403 or scoped 404 is acceptable. Pick scoped queries plus a policy. Defence in depth.'],
    ],
    'examples' => [
        ['Example 1 — the owner check', 'policy.php', 'Same id: allowed. Different id: forbidden. This is the whole policy.', "function canUpdate(int \$userId, int \$noteOwnerId): string {\n    if (\$userId !== \$noteOwnerId) {\n        return '403 Forbidden';\n    }\n    return '200 update allowed';\n}\necho canUpdate(1, 1) . \"\\n\";\necho canUpdate(1, 2);"],
        ['Example 2 — list only my rows', 'scope-list.php', 'Index uses this filter even if show is also protected.', "\$notes = [\n    ['id' => 1, 'title' => 'Buy milk', 'user_id' => 1],\n    ['id' => 2, 'title' => 'Secret', 'user_id' => 2],\n];\n\$me = 1;\nforeach (\$notes as \$note) {\n    if (\$note['user_id'] === \$me) {\n        echo \$note['title'] . \"\\n\";\n    }\n}"],
    ],
    'micro' => [
        'Compare ids with `===` only after both are integers. A string `"1"` and an int `1` can surprise you if one came from the session and one from SQLite as a string. Cast `(int) $note->user_id`.',
        'Middleware order: `auth` runs before the controller. If you type-hint `Note $note` and the binder loads any user’s note before the policy, the policy still blocks the action. Also scope the route binding if you do not want the existence leak. For this course, policy plus a scoped index is the required bar.',
        'A 403 page is not a crash. Do not catch `AuthorizationException` and turn it into a 200. Let Laravel render 403.',
    ],
    'quiz' => [
        quiz('What does auth middleware check?', 'That a user is logged in', 'That the user owns this particular note', 'That the title is valid', 'That the database is SQLite'),
        quiz('What status means “logged in, but not allowed”?', '403', '404 always', '419', '200'),
        quiz('Where must the owner check run?', 'On the server, in the policy or a scoped query', 'Only by hiding the button in Blade', 'In CSS', 'In .env'),
    ],
    'practice' => 'Register two users. Create a note as the first. Log in as the second and open that note’s edit URL. You should get 403 or 404, and the second user’s list must not show the first user’s title.',
]);

$chapters[] = lesson([
    'section' => '6. Finish Nectra Notes',
    'title' => 'Flash messages, redirects, and friendly failures',
    'keywords' => 'laravel flash session, redirect with success, validation error bag, 404 page',
    'lead' => 'A save that jumps to a new page with no sentence feels broken. A **flash message** lives for one request: “Note saved.” The next click clears it. This chapter is the polish that makes the project feel finished, plus the failures you should recognise without panic.',
    'sections' => [
        [
            'heading' => 'One sentence after each successful write',
            'paragraphs' => [
                '`return redirect()->route("notes.show", $note)->with("status", "Note saved.");` stores `status` in the session for the next request only. In the layout: `@if (session("status")) <p>{{ session("status") }}</p> @endif`. Use the layout so every page can show it. Escape it with `{{ }}` because you might later include a title in the sentence.',
                'Validation errors are not flash strings you write by hand. They arrive in `$errors`. Keep field errors next to fields, and keep `status` for success. Do not stuff “Title is required” into `status` or you will fight the framework.',
            ],
            'bullets' => [
                'Name the key `status` and use it everywhere. A mix of `success`, `message`, and `flash` means the layout checks the wrong one and “Laravel lost my message”.',
                '`withInput()` is for failures. `with("status")` is for success. You can chain `withInput()` only when coming back. On success you redirect forward and do not need old input.',
                'A 404 from `findOrFail` needs no custom work for version one. Laravel’s default page is enough. A missing view is an exception, not a 404. Read the message: “View [notes.show] not found” means the file name is wrong.',
                'When a page errors with 500, open `storage/logs/laravel.log` and read the last exception. The browser page in production hides the detail on purpose once `APP_DEBUG=false`.',
            ],
        ],
    ],
    'rows' => [
        ['`->with("status", "Note saved.")`', 'Flash for the next request.', 'A second redirect loses it unless that redirect also sets it. Redirect once, to the final page.'],
        ['`session("status")`', 'Read the flash.', 'It is null on a normal visit. The `@if` hides the paragraph. Do not print an empty green box.'],
        ['`$errors->any()`', 'True if validation failed.', 'Useful for a summary at the top. Still print `@error` under each field.'],
        ['`APP_DEBUG=true`', 'Shows the exception while you learn.', 'Set it false before anyone else can open the site. Debug pages can reveal `.env` values.'],
    ],
    'examples' => [
        ['Example 1 — flash lasts one step', 'flash.php', 'The message exists on the landing page and is gone after that.', "\$session = [];\nfunction redirectWith(array &\$session, string \$message): void {\n    \$session['status'] = \$message;\n}\nfunction consume(array &\$session): string {\n    \$text = \$session['status'] ?? '';\n    unset(\$session['status']);\n    return \$text === '' ? '(no message)' : \$text;\n}\nredirectWith(\$session, 'Note saved.');\necho consume(\$session) . \"\\n\";\necho consume(\$session);"],
        ['Example 2 — which bag holds which message', 'message-kinds.php', 'Success and validation are different channels.', "\$channels = [\n    'saved' => 'session status flash',\n    'empty title' => '\$errors bag, field title',\n    'not your note' => '403 from the policy',\n    'unknown id' => '404 from findOrFail',\n];\nforeach (\$channels as \$event => \$where) {\n    echo \$event . ' → ' . \$where . \"\\n\";\n}"],
    ],
    'micro' => [
        'If the message never appears, you returned the view from `store` instead of redirecting. Flash is written for the following request. The current request reads the previous flash, which is empty.',
        'Do not put HTML in the flash and print it with `{!! !!}`. “Note saved” is enough.',
        'Testing in the browser with the back button can show the form again from cache. The message on the show page is what you judge, not the cached form.',
    ],
    'quiz' => [
        quiz('How long does a flash message stay?', 'For the next request only', 'Until you delete the database', 'For 30 days in a cookie', 'Until migrate:fresh'),
        quiz('Where should “Note saved.” be printed?', 'Once, in the layout, from session("status")', 'Hard-coded on every Blade file separately', 'Inside .env', 'As a 500 error'),
        quiz('What should you open when the page is a generic 500?', 'storage/logs/laravel.log', 'composer.json only', 'public/index.php to delete it', 'The users table'),
    ],
    'practice' => 'Add a success flash to store, update, and destroy. Delete should say “Note deleted.” on the list page. Confirm the sentence disappears when you refresh.',
]);

$chapters[] = lesson([
    'section' => '6. Finish Nectra Notes',
    'title' => 'Upload a cover image on a note',
    'keywords' => 'laravel file upload, store public disk, validate image, storage link',
    'lead' => 'A note can have one optional picture. The file does not go in the database as bytes for this course. You store the file on disk and save the **path** in a column. The form needs `enctype="multipart/form-data"` or the file never arrives and you will blame validation.',
    'sections' => [
        [
            'heading' => 'Form, rule, disk, column',
            'paragraphs' => [
                'On the form: `enctype="multipart/form-data"` and `<input type="file" name="cover">`. In validation: `"cover" => ["nullable", "image", "max:2048"]`. `max` on a file is kilobytes. 2048 means about 2 MB. `image` allows jpeg, png, gif, webp and checks the content is really an image.',
                'If `$request->hasFile("cover")`, save with `$path = $request->file("cover")->store("covers", "public");`. That writes under `storage/app/public/covers` and returns a path like `covers/abc.jpg`. Save `$path` on the note. Run `php artisan storage:link` once so `public/storage` points at `storage/app/public`. The image URL is `asset("storage/".$note->cover_path)`.',
            ],
            'bullets' => [
                'Add a nullable `cover_path` string column in a new migration. Do not stuff the path into `body`.',
                'The file input is not repopulated. Browsers refuse to set a file input from `old()`. After a validation error on the title, the user re-selects the image. Say that next to the field.',
                'On update, only replace the file if a new one was uploaded. If `hasFile` is false, leave `cover_path` as it is. Otherwise every edit deletes the picture.',
                'Delete the old file when the note is deleted: `Storage::disk("public")->delete($note->cover_path)` before or after `$note->delete()`. A missing file should not crash the delete. Check the path is not null.',
            ],
        ],
    ],
    'rows' => [
        ['`enctype="multipart/form-data"`', 'Required for files.', 'Without it, `hasFile("cover")` is false and no error explains why. Memorise this attribute.'],
        ['`image` + `max:2048`', 'Real image, size limit in KB.', '`mimes:jpg,png` checks the extension more than the contents. Prefer `image`.'],
        ['`store("covers", "public")`', 'Saves on the public disk.', 'The first argument is the folder. The second is the disk name from `config/filesystems.php`.'],
        ['`storage:link`', 'Makes uploaded files reachable by URL.', 'You run it once per machine. Deploying to a new server means running it there too. The link is not in git as a real folder of pictures.'],
    ],
    'examples' => [
        ['Example 1 — keep the old path when no new file is sent', 'cover-update.php', 'Edit without a file must not wipe the picture.', "function nextPath(?string \$current, bool \$uploadedNew, string \$newPath): ?string {\n    if (\$uploadedNew) {\n        return \$newPath;\n    }\n    return \$current;\n}\necho nextPath('covers/old.jpg', false, 'covers/new.jpg') . \"\\n\";\necho nextPath('covers/old.jpg', true, 'covers/new.jpg');"],
        ['Example 2 — the public URL is a prefix plus the path', 'cover-url.php', 'You store the relative path, not the full URL. Hosts change. Paths stay.', "\$stored = 'covers/abc.jpg';\n\$url = '/storage/' . \$stored;\necho \$url;"],
    ],
    'micro' => [
        'Do not use the original file name as the only name on disk. Two people can upload `photo.jpg`. `store()` generates a unique name. If you use `storeAs` with the client’s name, you will overwrite someone else’s file.',
        'Reject SVG if you used a loose mime rule. SVG can contain JavaScript. The `image` rule in current Laravel does not treat SVG as a normal photo. Keep it that way.',
        'Large uploads also depend on `upload_max_filesize` and `post_max_size` in `php.ini`. If those are 2 MB and your rule says 5 MB, PHP empties the file before Laravel validates and the error looks like “the cover failed to upload”.',
    ],
    'quiz' => [
        quiz('What does the notes table store for the picture?', 'The path, such as covers/abc.jpg', 'The raw image bytes in the title column', 'The user’s password', 'A Blade section'),
        quiz('Why can old() not refill a file input?', 'Browsers do not allow a page to choose a file on the user’s disk', 'Laravel deletes the session', 'CSRF blocks files', 'SQLite cannot remember strings'),
        quiz('Which form attribute is mandatory for uploads?', 'enctype="multipart/form-data"', 'method="GET"', 'target="_blank"', 'novalidate'),
    ],
    'practice' => 'Add optional `cover_path`, the file input, the rules, `storage:link`, and show the image on the note page. Edit the title without choosing a file and confirm the image remains.',
]);

$chapters[] = lesson([
    'section' => '6. Finish Nectra Notes',
    'title' => 'A small JSON API for one note',
    'keywords' => 'laravel api route, json resource, sanctum token beginner, 401 json',
    'lead' => 'The website returns HTML. A phone app or a script needs **JSON**. Laravel can serve both. This chapter adds `GET /api/notes` that returns the logged-in user’s notes as JSON. You will see a token instead of a browser session.',
    'sections' => [
        [
            'heading' => 'Same notes, a different shape of response',
            'paragraphs' => [
                'Routes in `routes/api.php` are prefixed with `/api` and do not use the session CSRF stack the same way web routes do. A browser form does not belong here. A request with an `Authorization: Bearer <token>` header does.',
                'Install the API stack if your Laravel 11 app does not have it: `php artisan install:api`. That sets up `routes/api.php` and Laravel Sanctum. Create a token for a user. The route loads `$request->user()->notes()` and returns them. Do not return every column you might add later if it is private; for notes, `id`, `title`, `body`, and timestamps are enough.',
            ],
            'bullets' => [
                'Return `response()->json(["data" => $notes])` or a Laravel API resource. Wrap the list in `data` so you can add `meta` later without breaking clients.',
                'An API failure is JSON too. A missing token should be 401, not an HTML login page. Sanctum does this when the route uses `auth:sanctum`.',
                'Do not turn off authentication “just to test in the browser address bar”. The address bar sends no token, so a 401 is the correct result. Test with an HTTP client and a header.',
                'The web CRUD stays. The API is an extra door to the same policy: only the owner’s notes.',
            ],
        ],
    ],
    'rows' => [
        ['`/api/notes`', 'The path after the automatic prefix.', 'The file says `Route::get("/notes")`. The URL is `/api/notes`. People double-prefix it as `/api/api/notes` by writing `/api` inside the file. Don’t.'],
        ['`auth:sanctum`', 'Requires a logged-in user via session or token.', 'For a pure token test, send the bearer token. A 401 body is JSON.'],
        ['`response()->json`', 'Sets the content type to JSON.', 'Returning an array from an API route also becomes JSON. Be consistent and return arrays of note fields, not Blade.'],
        ['Status codes', '200 list, 401 no token, 404 unknown id, 422 failed validation.', '422 is Laravel’s validation status for APIs. HTML forms redirect instead. Same rules, different response shape.'],
    ],
    'examples' => [
        ['Example 1 — shape the JSON yourself', 'notes-json.php', 'Only the fields a client needs. Run it and read the JSON.', "\$notes = [\n    ['id' => 3, 'title' => 'Buy milk', 'body' => 'Two litres.', 'user_id' => 1],\n];\n\$data = [];\nforeach (\$notes as \$note) {\n    \$data[] = [\n        'id'    => \$note['id'],\n        'title' => \$note['title'],\n        'body'  => \$note['body'],\n    ];\n}\necho json_encode(['data' => \$data], JSON_PRETTY_PRINT);"],
        ['Example 2 — pick the status', 'api-status.php', 'No token is not a 500. It is 401.', "function status(?string \$token, bool \$ownsNote): int {\n    if (\$token === null || \$token === '') {\n        return 401;\n    }\n    if (!\$ownsNote) {\n        return 403;\n    }\n    return 200;\n}\necho status('', true) . \"\\n\";\necho status('secret-token', false) . \"\\n\";\necho status('secret-token', true);"],
    ],
    'micro' => [
        'API routes are stateless by default. `session("status")` and `old()` are web ideas. Do not call them from `routes/api.php`.',
        'Paginate API lists too: `->paginate(10)` becomes JSON with `data` and `links` if you return the paginator. A mobile app cannot swallow 10,000 notes.',
        'Treat the token like a password. Do not commit it, do not put it in JavaScript inside a public Blade page “for convenience”.',
    ],
    'quiz' => [
        quiz('A route file path of /notes in routes/api.php is requested as?', '/api/notes', '/notes', '/routes/api/notes', '/sanctum/notes'),
        quiz('What status should a missing token get?', '401', '200 with an empty password', '419 always', '302 to a Blade form, as the only option'),
        quiz('What should the JSON include?', 'The current user’s notes, not every note in the table', 'The .env file', 'Password hashes', 'CSRF tokens for other people'),
    ],
    'practice' => 'Add `GET /api/notes` behind `auth:sanctum` that returns only the current user’s id, title, and body. Call it without a token and confirm 401. Call it with a token and confirm you do not see the other user’s notes.',
]);

$chapters[] = lesson([
    'section' => '6. Finish Nectra Notes',
    'title' => 'Test the note rules, then put the app on a server',
    'keywords' => 'laravel pest test beginner, feature test post notes, deploy checklist app debug',
    'lead' => 'Clicking the form once is not a test you can repeat. A **feature test** boots the app, acts like a browser, and asserts the result. Then a short deploy checklist stops the mistakes that leak passwords or serve the wrong folder. This is the last step of Nectra Notes.',
    'sections' => [
        [
            'heading' => 'One test that would have caught a real bug',
            'paragraphs' => [
                'Laravel includes Pest or PHPUnit. Use the one already in the project (`php artisan test`). A feature test creates a user, acts as that user, posts a note with an empty title, and asserts a session error on `title`. A second assertion posts a valid note and asserts the database has that title. If someone later removes `required`, the test fails. That is the point.',
                'Deployment is a list, not a vibe. The server document root is the `public` directory. `APP_ENV=production` and `APP_DEBUG=false`. `APP_KEY` is set. `php artisan migrate --force` runs migrations. `php artisan storage:link` runs if you have uploads. `composer install --no-dev --optimize-autoloader` installs code. Do not copy your laptop `.env` with the local database password onto a public repo.',
            ],
            'bullets' => [
                'Tests use a separate database. Laravel’s `phpunit.xml` or `Pest.php` typically points tests at SQLite in memory. You do not want tests deleting your real notes. Never point `DB_DATABASE` in the testing env at your real MySQL database.',
                '`actingAs($user)` logs the test in without a password form. Then `$this->post("/notes", [...])` hits your real `store` method, including middleware and validation.',
                '`assertSessionHasErrors("title")` is the validation check. `assertRedirect()` checks the success path. `assertDatabaseHas("notes", ["title" => "Buy milk"])` checks the row.',
                'On the server, file permissions must let PHP write to `storage/` and `bootstrap/cache/`. A deploy that “works” but cannot save sessions is usually this.',
            ],
        ],
    ],
    'rows' => [
        ['`php artisan test`', 'Runs the suite.', 'A failure prints the test name and the assertion. Read the first failure. The rest may be fallout.'],
        ['`assertDatabaseHas`', 'The row exists.', 'It looks at the testing database. If this passes and your manual SQLite file looks empty, you are looking at a different database. That is expected.'],
        ['`APP_DEBUG=false`', 'Hides stack traces from visitors.', 'Leave it true on your laptop. The server’s `.env` is a different file.'],
        ['Document root `public/`', 'Visitors cannot download `.env`.', 'If you can open `https://yourdomain/.env` in a browser, the document root is wrong. Fix it immediately and rotate every secret.'],
    ],
    'examples' => [
        ['Example 1 — the assertions in plain language', 'test-plan.php', 'This is the test you will write, said as checks.', "\$checks = [\n    'guest POST /notes' => 'redirect to login',\n    'empty title' => 'session error on title, no row',\n    'valid title and body' => 'row exists, redirect to show',\n    'other user opens the note' => '403 or 404',\n];\nforeach (\$checks as \$case => \$expect) {\n    echo \$case . ' => ' . \$expect . \"\\n\";\n}"],
        ['Example 2 — deploy answers you must be able to say', 'deploy-check.php', 'If any answer is “not sure”, do not point a domain at the app yet.', "\$answers = [\n    'Document root' => 'public/',\n    'APP_DEBUG' => 'false',\n    'APP_KEY' => 'set and not committed',\n    'migrations' => 'migrate --force already run',\n    'uploads' => 'storage:link done',\n];\nforeach (\$answers as \$item => \$expected) {\n    echo \$item . ': ' . \$expected . \"\\n\";\n}"],
    ],
    'micro' => [
        'A test that talks to the internet or to your real inbox is slow and flaky. This course’s test hits your app in process. Do not add a real HTTP call to example.com.',
        '`RefreshDatabase` migrates a clean test database for each test. Without it, rows leak between tests and “assert count is 1” fails only when the suite runs together. Use the trait or Pest’s `RefreshDatabase` usage that the Breeze tests already show. Copy that style.',
        'Optimising with `php artisan config:cache` freezes config. After you change `.env` on the server you must `config:clear` or cache again. A “changed the database name and nothing happened” deploy is cached config.',
    ],
    'quiz' => [
        quiz('What does a feature test protect you from?', 'A later edit silently removing a rule you cared about', 'Needing PHP', 'Writing Blade', 'Using a primary key'),
        quiz('Which APP_DEBUG value is correct on a public server?', 'false', 'true', 'null', 'The database password'),
        quiz('Which folder is the public document root?', 'public/', 'The project root, so artisan is visible', 'storage/logs', 'vendor/'),
    ],
    'practice' => 'Write one feature test: a logged-in user posts a valid note and the database has it; then posts an empty title and gets a title error. Run `php artisan test`. Then read the deploy list and tick it on paper before you ever buy a domain.',
]);

upsert_course_with_chapters($pdo, $courseMeta, $chapters);
