<?php
declare(strict_types=1);

function dotnet_chapters_c(): array
{
    $L = static function (array $c): array {
        return track_lesson('csharp', 'notes.cs', $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };

    return [
        $L([
            'section' => 'Identity and settings',
            'title' => 'A CORS policy is an allow-list',
            'keywords' => 'CORS policy, AllowAnyOrigin, credentials',
            'lead' => 'A browser page on another origin will not let its script read your API unless you send a CORS policy that names that origin. The policy is an allow-list you wrote. Reflecting the Origin header back, whatever it is, is not a list. A star, meaning any origin, cannot be combined with credentials. CORS is not authentication. A permitted origin is still an unnamed caller until a handler says otherwise.',
            'heading' => 'You choose the origins',
            'para' => 'Add the policy on the builder and apply it in the pipeline. Name the one origin your notes page uses. A preflight OPTIONS request must succeed before the browser sends the POST, and the preflight must not create a note. Tools that are not browsers ignore CORS, so CORS does not protect the route from a program on a server. Keep authentication on the route even when the origin is allowed.',
            'bullets' => [
                'List origins. Do not echo whatever Origin arrived.',
                'A star origin conflicts with credentials.',
                'A preflight must not create a note.',
                'An allowed origin is not a logged-in user.',
            ],
            'visual' => tf_split('dncors', 'Is this origin on the list?', 'You wrote it down?', 'yes', 'the browser may read', 'no', 'do not grant the read', 'The list is configuration. The request does not append itself.'),
            'rows' => [
                ['Allow-list', 'Origins you named', 'The policy.'],
                ['Echo Origin', 'Not a list', 'Anyone can ask.'],
                ['Star', 'Any origin', 'Not with credentials.'],
                ['Credentials', 'Cookies on the call', 'Needs a specific origin.'],
                ['OPTIONS', 'Preflight', 'No note is stored.'],
                ['Authentication', 'Still required', 'CORS did not name the caller.'],
            ],
            'e1' => ['Example 1 — an origin you listed', 'allowed is 1. The note can be read by that page. Status 200.', <<<'CS'
string origin = "https://notes.example";
int allowed = 1;
int credentials = 0;
int status = 200;
Console.WriteLine(origin);
Console.WriteLine(allowed);
Console.WriteLine(credentials);
Console.WriteLine(status);
CS],
            'e2' => ['Example 2 — star plus credentials is not a legal pair', 'legal is 0. Pick a specific origin if you need credentials, or drop credentials if you truly mean any origin.', <<<'CS'
string origin = "*";
int credentials = 1;
int legal = 0;
Console.WriteLine(origin);
Console.WriteLine(credentials);
Console.WriteLine(legal);
CS],
            'micro' => [
                'Apply the CORS middleware early enough that a refused browser call still gets the headers it needs to understand the refusal.',
                'Same-origin pages do not need CORS for a relative /notes.',
                'Do not treat a passing browser check as permission to skip 401.',
            ],
            'quiz' => [
                $q('What is a CORS allow-list?', 'The origins you decided may read the response', 'Every Origin header that arrives', 'The caller role', 'The port'),
                $q('Can a star origin be used together with credentials?', 'No', 'Yes', 'Only for GET', 'Only on a controller'),
                $q('Does a permitted origin replace authentication?', 'No', 'Yes', 'Only for POST', 'Only when the status is 404'),
            ],
            'practice' => 'Write the one origin you would allow, and say which pair you refuse: any origin together with credentials.',
        ]),
        $L([
            'section' => 'Identity and settings',
            'title' => 'The environment name picks the settings file',
            'keywords' => 'appsettings, environment, configuration order',
            'lead' => 'The host loads configuration in a fixed order. The base settings file comes first. A file named for the environment, such as Production, comes later and overrides the same keys. Environment variables come later still and override the files. The last source that sets a key wins. The port your process listens on is the value after that sequence, not the value you remember from the base file.',
            'heading' => 'Later source, same key, new value',
            'para' => 'Development and Production can disagree on the log level and must not disagree by accident on a password stored in a file. Prefer a password only in the environment of the machine, so neither file contains it. The environment name itself is a setting the host reads early. A missing Production file is fine: the base file stands. A missing required password is not fine: refuse to start. Log the environment name and the port. Do not log the password that the later source supplied.',
            'bullets' => [
                'Base file, then the environment file, then environment variables.',
                'The same key: the later source wins.',
                'A password belongs in the environment, not in the file you commit.',
                'Read the winning value at startup and fail if a required one is absent.',
            ],
            'visual' => tf_flow('dnenv', 'Later source wins', [['base file', 'first'], ['named file', 'overrides'], ['variables', 'they win']], 'The port you listen on is the value left after the last source.'),
            'rows' => [
                ['Base file', 'Shared defaults', 'Safe numbers, no secrets.'],
                ['Environment file', 'Per environment', 'Production can override.'],
                ['Environment variables', 'The machine', 'Last, so they win.'],
                ['Same key', 'One winner', 'The later write.'],
                ['Password', 'Last source only', 'Not in the committed file.'],
                ['Missing password', 'Do not start', 'Name the key that is absent.'],
            ],
            'e1' => ['Example 1 — the later source chose the port', 'The base file is not the last word. 5000 is the value that won.', <<<'CS'
string first = "base file";
string second = "environment file";
string winner = "environment";
int port = 5000;
Console.WriteLine(first);
Console.WriteLine(second);
Console.WriteLine(winner);
Console.WriteLine(port);
CS],
            'e2' => ['Example 2 — a required secret never arrived', 'ready is 0. Do not listen.', <<<'CS'
string key = "database password";
int ready = 0;
string winner = "none";
Console.WriteLine(key);
Console.WriteLine(ready);
Console.WriteLine(winner);
CS],
            'micro' => [
                'Changing an environment variable does not change a running IOptions snapshot. Restart, or use a monitor on purpose.',
                'The environment name is not a secret. The values inside Production can be.',
                'Keep the key names identical across files so an override replaces a value instead of adding a near-duplicate.',
            ],
            'quiz' => [
                $q('Which source wins when the same key is set in the base file and in an environment variable?', 'The environment variable', 'The base file', 'Neither', 'The project SDK'),
                $q('Where should a database password live?', 'In the machine environment', 'In the committed base file', 'In the note record', 'In the health body'),
                $q('What do you do when that password is missing?', 'Refuse to start', 'Listen and store blank notes', 'Return 404 for every route', 'Invent one'),
            ],
            'practice' => 'List the three configuration sources in the order they are applied, and mark the one that may hold the password.',
        ]),
        $L([
            'section' => 'Running it for real',
            'title' => 'An async endpoint still returns one status',
            'keywords' => 'async handler, one result, await',
            'lead' => 'A handler can be async because the book waits on a database. The caller still gets one status and one body. You await the work, then return one result. You do not return a result and also write a second answer when the task finishes. A fault you did not plan for becomes 500. A bad title is still 400, and you should decide that before you store.',
            'heading' => 'Await, then one result',
            'para' => 'The framework waits for the task the handler returns. If the task completes with Ok, the client sees 200. If the task fails with an unexpected error, the exception handler you placed at the top of the pipeline can still turn that into 500, as long as the body has not started. Do not catch a bad title and rethrow it as an unexpected error. Return BadRequest. Do not start writing the body, then await, then try to change the status.',
            'bullets' => [
                'One result after the await.',
                '400 for a bad title happens before the store.',
                'An unexpected failure is 500, not a second 200.',
                'The client waits for that one answer. There is no later correction.',
            ],
            'visual' => tf_flow('dnasync', 'Wait, then answer once', [['check', 'title'], ['await', 'the store'], ['one status', '201 or 400']], 'The await is in the middle. The status is chosen once, at the end, or earlier if the check fails.'),
            'rows' => [
                ['async', 'The handler waits', 'The framework waits too.'],
                ['One return', 'One status', 'Not a second write.'],
                ['Bad title', '400 first', 'Do not await a store.'],
                ['Store worked', '201', 'After the await.'],
                ['Unexpected fault', '500', 'If the body has not started.'],
                ['Write, then await', 'Too late', 'The status is already going out.'],
            ],
            'e1' => ['Example 1 — tea is stored once', 'answers is 1. Status 201. The title is tea.', <<<'CS'
string title = "tea";
int answers = 1;
int status = 201;
Console.WriteLine(title);
Console.WriteLine(answers);
Console.WriteLine(status);
CS],
            'e2' => ['Example 2 — the check fails before the store', 'stored is 0. Status 400. There was nothing to wait for.', <<<'CS'
string title = "";
int stored = 0;
int answers = 1;
int status = 400;
Console.WriteLine(title);
Console.WriteLine(stored);
Console.WriteLine(answers);
Console.WriteLine(status);
CS],
            'micro' => [
                'A cancellation from a client who left is not a 400. Stop the work and do not store half a note.',
                'Do not block on the task with a synchronous wait. Let the handler be async so the thread can serve other calls.',
                'Logs after the await still belong to the same request id as the logs before it.',
            ],
            'quiz' => [
                $q('How many statuses does one async request produce?', 'One', 'One before the await and one after', 'One per claim', 'Zero, async has no status'),
                $q('When do you return 400 for an empty title?', 'Before storing', 'After a successful store', 'As 200 with an error flag', 'As 401'),
                $q('What is an unexpected fault if the body has not started?', '500', '201', '301', 'A second 200'),
            ],
            'practice' => 'Write the order of steps for a valid create and for an empty title. Mark the single status each one returns.',
        ]),
        $L([
            'section' => 'Running it for real',
            'title' => 'Log information, and keep secrets out of the message',
            'keywords' => 'ILogger, redact, request log',
            'lead' => 'Log the method, the path, the status, and how long the call took. Log the caller name if you have a name claim. Do not log the token, the cookie, the database password, or a body that might hold one. If you need to know a credential was sent, log the word present. The line is for you at night. It is not a second copy of the request.',
            'heading' => 'Pick the fields',
            'para' => 'ILogger is the service you ask the container for. Information is the right level for a finished request. Warning fits a refused caller. Error fits an unexpected fault, and the error can include the server-side reason in the log while the client still gets a short problem. Do not build the message by dumping every header. A query string can contain a token, so log the path without the raw query until you know the query is only a search word.',
            'bullets' => [
                'Method, path, status, duration.',
                'Name claim yes. Token no.',
                'present is enough for a credential.',
                'The client body stays short even when the log is detailed.',
            ],
            'visual' => tf_panels('dnlog', 'The line and the secret', 'log', ['method and path', 'status', 'name claim'], 'leave out', ['token', 'password', 'raw cookie'], 'The name is a fact on the identity. The token is the credential that produced it.'),
            'rows' => [
                ['Information', 'A finished call', 'The usual line.'],
                ['Warning', 'Refused', '401 or 403, without the token.'],
                ['Error', 'Unexpected', 'Server detail in the log only.'],
                ['Token', 'Never', 'Not even once.'],
                ['present', 'The substitute', 'When you must note that a credential existed.'],
                ['Request id', 'On every line', 'So the await before and after match.'],
            ],
            'e1' => ['Example 1 — a safe line', 'The line names the call and the status. The secret flag stays 0.', <<<'CS'
string line = "GET /notes 200";
int secretIncluded = 0;
Console.WriteLine(line);
Console.WriteLine(secretIncluded);
CS],
            'e2' => ['Example 2 — a credential is mentioned, not written out', 'The line says present. The secret is still absent from the message.', <<<'CS'
string line = "credential present";
int secretIncluded = 0;
Console.WriteLine(line);
Console.WriteLine(secretIncluded);
CS],
            'micro' => [
                'A structured log stores method and status as fields, so you can count 500s without reading prose.',
                'Health probes can drown the file. Give them a quieter level or a counter.',
                'If a note title is private, do not log every title. The id is usually enough.',
            ],
            'quiz' => [
                $q('Which line is safe?', 'GET /notes 200, with no token', 'The full Authorization header', 'The database password', 'The raw cookie'),
                $q('How do you record that a credential was sent?', 'Log the word present', 'Log the token', 'Log the password', 'Return it in the note'),
                $q('Where does the server-side reason for a 500 belong?', 'In the log', 'In the client problem detail', 'In the health body', 'In the project file'),
            ],
            'practice' => 'Write one information line for a create that returned 201. Cross out any field that would reveal a secret.',
        ]),
        $L([
            'section' => 'Running it for real',
            'title' => 'A health check is a route that does not do business work',
            'keywords' => 'health checks, liveness, readiness, MapHealthChecks',
            'lead' => 'A health endpoint tells a balancer whether to send customers. It does not create a note, list titles, or run a report. Liveness means the process answered. Readiness means the process can serve, which may include a dependency. A failed dependency is 503 on the ready path. The process can stay up so it can recover. Neither body contains a secret or a note.',
            'heading' => 'Probes are not the API',
            'para' => 'MapHealthChecks adds this route for you, or you can map a tiny function. Keep it off the notes group so notes authorization and the title filter do not apply. A ready check that queries the database needs a short timeout, or a stuck database becomes a stuck probe and the balancer cannot tell you are unhealthy. 200 with ok true is liveness. 503 is "do not send customers here". Do not use 400. The balancer is not a client who typed a bad title.',
            'bullets' => [
                'Liveness: the process answered. No note was written.',
                'Readiness: 503 when a dependency is down.',
                'Outside the notes group, so notes rules do not apply.',
                'The body stays dull because the probe is often unauthenticated.',
            ],
            'visual' => tf_panels('dnhc', 'Two probes', 'liveness', ['/health', 'process answered', 'writes 0'], 'readiness', ['/ready', 'can serve', '503 if not'], 'Customers use /notes. The balancer uses these paths.'),
            'rows' => [
                ['/health', 'Liveness', '200 when the process answers.'],
                ['/ready', 'Readiness', '503 when a dependency is down.'],
                ['Notes', 'Not touched', 'writes stays 0.'],
                ['Group', 'Not the notes group', 'No title rule, no login.'],
                ['Body', 'Dull', 'No titles, no secrets.'],
                ['Timeout', 'Short', 'A stuck dependency must not hang the probe.'],
            ],
            'e1' => ['Example 1 — liveness', 'writes is 0. Status 200.', <<<'CS'
string path = "/health";
int writes = 0;
int status = 200;
Console.WriteLine(path);
Console.WriteLine(writes);
Console.WriteLine(status);
CS],
            'e2' => ['Example 2 — not ready', 'The process can still answer. It tells the balancer to look elsewhere.', <<<'CS'
string path = "/ready";
int writes = 0;
int status = 503;
Console.WriteLine(path);
Console.WriteLine(writes);
Console.WriteLine(status);
CS],
            'micro' => [
                'A health URL that returns the notes list is an API with a confusing name. Split them.',
                'Readiness should fail as soon as you begin shutdown, so new customers stop arriving.',
                'Do not require a role for the probe unless the balancer can present that credential.',
            ],
            'quiz' => [
                $q('What does liveness prove?', 'The process answered', 'A note was created', 'The caller is ada', 'The title was valid'),
                $q('Which status should readiness use when a dependency is down?', '503', '400', '201', '301'),
                $q('Why keep health outside the notes group?', 'Notes auth and title rules must not apply to the probe', 'Health cannot return JSON', 'Groups are only for POST', 'The port would change'),
            ],
            'practice' => 'Define liveness and readiness in one sentence each. Say which one returns 503, and name one thing neither body may include.',
        ]),
        $L([
            'section' => 'Running it for real',
            'title' => 'The host starts the pipeline and can stop it',
            'keywords' => 'generic host, shutdown, stop listening',
            'lead' => 'The host is the process owner. Run starts the server and the pipeline. A stop signal asks the host to leave. The polite order is the same as any server: stop accepting new connections, finish requests you already have, then exit. Readiness should fail at the start of that sequence so a balancer stops sending work.',
            'heading' => 'Start is Run. Stop is a sequence.',
            'para' => 'You can register a callback for the stopping event if you need to drain a queue. You do not need one for a notes API that only has the HTTP requests themselves. The server stops accepting, in-flight creates finish, then the database is closed. Closing the database first fails those creates. A time limit covers a request that will not finish, so a deploy cannot wait forever. The playground prints the order you should be able to say.',
            'bullets' => [
                'Run listens. The host owns that loop.',
                'Stop accepting before you drop in-flight work.',
                'Close the database after those requests, not before.',
                'Fail readiness as soon as stopping begins.',
            ],
            'visual' => tf_flow('dnhost', 'Leave in order', [['ready fails', 'balancer looks away'], ['stop accepting', 'no new work'], ['finish', 'then exit']], 'The signal starts the sequence. It does not cut a create in half on purpose.'),
            'rows' => [
                ['Run', 'Start', 'The pipeline is live.'],
                ['Signal', 'Stop', 'The platform asks the process to leave.'],
                ['Readiness', 'Fails first', 'New customers go elsewhere.'],
                ['Accept', 'Stops', 'In-flight work may finish.'],
                ['Database', 'Closes last', 'After those requests.'],
                ['Time limit', 'A backstop', 'A stuck call cannot block the deploy.'],
            ],
            'e1' => ['Example 1 — the stop sequence', 'Three steps. The port is the one that was serving.', <<<'CS'
string step1 = "ready fails";
string step2 = "stop accepting";
string step3 = "finish then exit";
int port = 5000;
Console.WriteLine(step1);
Console.WriteLine(step2);
Console.WriteLine(step3);
Console.WriteLine(port);
CS],
            'e2' => ['Example 2 — readiness during the drain', 'Status 503. writes is still 0. This instance is not taking a new note.', <<<'CS'
string path = "/ready";
int status = 503;
int writes = 0;
Console.WriteLine(path);
Console.WriteLine(status);
Console.WriteLine(writes);
CS],
            'micro' => [
                'Code after Run runs only once the host has stopped. Do not put a route there.',
                'A second signal can exit immediately. The first one is the polite path.',
                'A load balancer that ignores readiness will still send work until the port actually closes. Fail the probe early anyway.',
            ],
            'quiz' => [
                $q('What is the first useful stop step for a balancer?', 'Readiness fails', 'Delete the notes', 'Return 201', 'Log the password'),
                $q('When do you close the database?', 'After in-flight requests finish', 'Before you stop accepting', 'On every health call', 'Inside the logger'),
                $q('When does code after Run run?', 'After the host has stopped', 'On every request', 'Before Build', 'When a title is empty'),
            ],
            'practice' => 'Write the stop sequence in order, including the moment readiness changes and the moment the database closes.',
        ]),
        $L([
            'section' => 'Running it for real',
            'title' => 'A controller is another way to write the same route',
            'keywords' => 'controller, ApiController, minimal API difference',
            'lead' => 'A controller is a class. An action is a method. A route attribute puts GET /notes on that method. A minimal API puts the same pair on MapGet. The client cannot tell which style you used if the status and the body match. The styles differ in what they do before your code. ApiController can return 400 for an invalid model before the action runs. A minimal handler will not, unless you add the check.',
            'heading' => 'Same HTTP, different defaults',
            'para' => 'Choose one style for the notes API and keep it. Mixing them is legal and confusing. If you use a controller, the action should still call the same service the minimal handler would call. The service stays free of Results and of controller types. When you move a route from one style to the other, re-test the empty body. The automatic 400 may appear or disappear. Authorization attributes and group filters are the two ways to require a caller. Both should end at the same 401 or 403.',
            'bullets' => [
                'The public method and path can be identical.',
                'ApiController can reject an invalid model before the action.',
                'A minimal API needs that check written down.',
                'The service is shared. The edge is what changes.',
            ],
            'visual' => tf_panels('dnctrl', 'Two ways to say GET /notes', 'minimal', ['MapGet', 'you write the check', 'same 200'], 'controller', ['an action', 'invalid model can 400 first', 'same 200 when valid'], 'The client sees the status and the body. The style is a server choice.'),
            'rows' => [
                ['MapGet', 'A function', 'Minimal API.'],
                ['Action', 'A method', 'Controller.'],
                ['Path', 'Either style', 'Document one.'],
                ['Invalid model', 'Auto 400 on ApiController', 'Not automatic on minimal.'],
                ['Service', 'Shared', 'The title rule lives once.'],
                ['Empty-body test', 'Required after a move', 'The 400 can change.'],
            ],
            'e1' => ['Example 1 — minimal, and the check is yours', 'auto400 is 0 until your if runs. An empty title that you checked is still 400.', <<<'CS'
string style = "minimal";
int auto400 = 0;
int checked = 1;
int status = 400;
Console.WriteLine(style);
Console.WriteLine(auto400);
Console.WriteLine(checked);
Console.WriteLine(status);
CS],
            'e2' => ['Example 2 — a controller can refuse before the action', 'auto400 is 1. The action did not run. Status 400.', <<<'CS'
string style = "controller";
int auto400 = 1;
int actionRan = 0;
int status = 400;
Console.WriteLine(style);
Console.WriteLine(auto400);
Console.WriteLine(actionRan);
Console.WriteLine(status);
CS],
            'micro' => [
                'A valid note is 200 or 201 in both styles. The interesting difference is the failure you did not write by hand.',
                'Controller filters and endpoint filters are different types. Do not assume a class written for one runs on the other.',
                'Keep the returned record the same so a style change is invisible to the client.',
            ],
            'quiz' => [
                $q('What can the client see about your choice of controller versus minimal API?', 'The status and the body, when they match', 'The class name, always', 'The project SDK', 'The lifetime of the service'),
                $q('Which style can return 400 for an invalid model before your method runs?', 'A controller with ApiController', 'A minimal API, with no extra check', 'Neither', 'Only health checks'),
                $q('What should you re-test if you move a route from a controller to a minimal API?', 'The empty body', 'Only the font', 'Only the port', 'Nothing, the behavior cannot change'),
            ],
            'practice' => 'For an empty title, say what auto-runs on an ApiController action and what you must write yourself on a minimal handler. Give the status both should reach.',
        ]),
        $L([
            'section' => 'Running it for real',
            'title' => 'Status codes you should be able to say out loud',
            'keywords' => 'HTTP status, 200 201 400 401 403 404 204 503',
            'lead' => 'Eight numbers cover this notes API. 200 is a read, including an empty list. 201 is a create. 204 is a delete with no body. 400 is a body the client can fix. 401 is no caller. 403 is a caller who may not. 404 is a missing note or a route that did not match. 503 is a probe or a drain, not a customer mistake. If you can say the number before you name the method, you understand the route.',
            'heading' => 'The number is the decision',
            'para' => 'Do not collapse these into 200 with a flag. A client, a balancer, and a log counter all read the number first. 500 is the extra one: you failed on a request that was valid. It is not the bucket for a missing title. 301 and 302 are for a path you moved, and a JSON create does not need them. Pick the number in the route, from the service outcome and the identity, and return one result.',
            'bullets' => [
                '200 read, 201 create, 204 delete with nothing to show.',
                '400 fix the body. 404 the note or the route is not there.',
                '401 no name. 403 a name that is refused.',
                '503 not ready. 500 you broke a valid request.',
            ],
            'visual' => tf_flow('dnstat', 'Say the number first', [['200', 'read'], ['201', 'created'], ['400', 'fix it'], ['404', 'missing']], '401, 403, 204, and 503 are the other four you should be able to place without looking them up.'),
            'rows' => [
                ['200', 'Read', 'An empty list too.'],
                ['201', 'Created', 'The new id.'],
                ['204', 'Done, empty', 'A delete.'],
                ['400', 'Bad body', 'The client can edit it.'],
                ['401 / 403', 'Who / whether', 'No name, or refused.'],
                ['404 / 503 / 500', 'Missing / not ready / your fault', 'Three different repairs.'],
            ],
            'e1' => ['Example 1 — the customer numbers', 'Read, create, a bad body, and a missing note.', <<<'CS'
int read = 200;
int created = 201;
int bad = 400;
int missing = 404;
Console.WriteLine(read);
Console.WriteLine(created);
Console.WriteLine(bad);
Console.WriteLine(missing);
CS],
            'e2' => ['Example 2 — name, permission, delete, and a probe', 'Four more. None of them means "bad JSON".', <<<'CS'
int unnamed = 401;
int refused = 403;
int deleted = 204;
int notReady = 503;
Console.WriteLine(unnamed);
Console.WriteLine(refused);
Console.WriteLine(deleted);
Console.WriteLine(notReady);
CS],
            'micro' => [
                '500 is for a fault after the request was valid. Say it when you mean it, and log the reason.',
                '409 is a conflict, such as a unique title, if you add that rule later. It is not a 400.',
                'If two routes disagree on the number for a missing note, clients cannot write one handler. Pick 404 and keep it.',
            ],
            'quiz' => [
                $q('Which status is a create that stored a note?', '201', '200', '204', '401'),
                $q('Which pair is "no caller" and "caller refused"?', '401 and 403', '400 and 404', '200 and 201', '204 and 503'),
                $q('Which status is a search that matched nothing?', '200 and an empty list', '404', '400', '503'),
            ],
            'practice' => 'Say the status, out loud in a list, for: a list, a create, an empty title, no credential, a refused role, an unknown id, a delete, and a dependency that is down.',
        ]),
        $L([
            'section' => 'Running it for real',
            'title' => 'Dependency lifetime: singleton, scoped, transient',
            'keywords' => 'singleton scoped transient, two resolves, captive dependency',
            'lead' => 'Lifetime is how many objects the container builds. In one request, two asks for a singleton return the same object. Two asks for a scoped service return the same object. Two asks for a transient service return two objects. On the next request, the singleton is still the first object, the scoped service is a new one, and a transient ask is new again.',
            'heading' => 'Count the objects',
            'para' => 'A notes book that holds a database context should be scoped, because the context is scoped to the request. A stateless helper can be transient or singleton. A cache of public settings can be singleton. The bug is a singleton that takes the scoped book: the first request\'s book is captured and reused. Count the identities. If a second request still sees scoped id 1, you captured it. The legal second request sees scoped id 2 and singleton id 1.',
            'bullets' => [
                'Singleton: one for the process.',
                'Scoped: one per request, shared inside that request.',
                'Transient: one per ask, even inside one request.',
                'Do not let the long lifetime hold the short one.',
            ],
            'visual' => tf_flow('dnlife', 'Two asks, one request', [['singleton', '1 then 1'], ['scoped', '1 then 1'], ['transient', '1 then 2']], 'The second number is the second ask. Transient is the one that changes inside the request.'),
            'rows' => [
                ['Singleton', 'Process', 'Second request still sees 1.'],
                ['Scoped', 'Request', 'Same inside the request, new on the next.'],
                ['Transient', 'Each ask', 'Two asks, two objects.'],
                ['Database book', 'Scoped', 'It follows the context.'],
                ['Capture', 'The bug', 'Singleton keeping scoped id 1.'],
                ['Stateless helper', 'Singleton or transient', 'No per-request data inside it.'],
            ],
            'e1' => ['Example 1 — two asks in request 1', 'Singleton stays 1, scoped stays 1, transient becomes 2.', <<<'CS'
int request = 1;
int singletonA = 1;
int singletonB = 1;
int scopedA = 1;
int scopedB = 1;
int transientB = 2;
Console.WriteLine(request);
Console.WriteLine(singletonA);
Console.WriteLine(singletonB);
Console.WriteLine(scopedA);
Console.WriteLine(scopedB);
Console.WriteLine(transientB);
CS],
            'e2' => ['Example 2 — request 2', 'The singleton is still 1. The scoped object is new, so 2. A further transient ask is 3.', <<<'CS'
int request = 2;
int singleton = 1;
int scoped = 2;
int transient = 3;
Console.WriteLine(request);
Console.WriteLine(singleton);
Console.WriteLine(scoped);
Console.WriteLine(transient);
CS],
            'micro' => [
                'The numbers are identities, not the note id. Object 1 and object 2 can both read note 4.',
                'A scoped service resolved from a singleton constructor is the capture. Resolve it from the request instead.',
                'Tests that build a scope per request see the same split. A test that forgets the scope will lie about scoped services.',
            ],
            'quiz' => [
                $q('Two asks for a scoped service in one request return what?', 'The same object', 'Two objects always', 'Nothing', 'The singleton from another process'),
                $q('What does request 2 see for a singleton created on request 1?', 'The same object', 'A new object always', 'No service', 'The transient from request 1 only'),
                $q('What is the capture bug?', 'A singleton keeps the first request\'s scoped service', 'A transient is reused by accident as a note title', 'Scoped means one per process', 'The port changes per request'),
            ],
            'practice' => 'For one request with two asks, give the identity numbers of a singleton, a scoped book, and a transient helper. Then give the three numbers on the next request.',
        ]),
        $L([
            'section' => 'The project',
            'title' => 'Project: a notes endpoint',
            'keywords' => 'notes endpoint project, auth, service, status',
            'lead' => 'This is the endpoint the lessons were building. A caller is authenticated, then authorized. The notes group holds the route. The service checks the title and stores it, or refuses. The route turns that into 201 or 400. A read of a known id is 200 and the record Id plus Item. A missing id is 404. Health stays outside the group and writes nothing. You should be able to walk one request in that order.',
            'heading' => 'Walk one request',
            'para' => 'Ada is known and allowed. GET /notes/4 returns milk with 200. A create with tea returns 201 and stores once. A create with an empty title returns 400 and stores nothing. No credential is 401 before the service runs. A known reader who may not create is 403, and the service still does not run. The first preview is the allowed read. The second is the empty title. Those two, plus the 401 and 403 you can already say, are the project.',
            'bullets' => [
                'Authenticate, then authorize, then the service, then one result.',
                'The record that leaves is id and item, not the storage row.',
                '400 stores nothing. 201 stores once. 404 stores nothing.',
                'Health is not in this group and does not write a note.',
            ],
            'visual' => tf_flow('dnproj', 'One allowed read', [['ada', 'known'], ['allow', 'yes'], ['service', 'the note'], ['200', 'id and item']], 'A refused caller stops before the service. An empty title stops inside the rule.'),
            'rows' => [
                ['Known and allowed', '200', 'Id and item.'],
                ['Create with tea', '201', 'Stored once.'],
                ['Empty title', '400', 'Stored nothing.'],
                ['No credential', '401', 'Service did not run.'],
                ['Known, refused', '403', 'Service did not run.'],
                ['Unknown id', '404', 'Not a bad body.'],
            ],
            'e1' => ['Example 1 — Ada reads note 4', 'known and allowed. The record is id 4 and milk. Status 200.', <<<'CS'
string caller = "ada";
int known = 1;
int allowed = 1;
string id = "4";
string item = "milk";
int status = 200;
Console.WriteLine(caller);
Console.WriteLine(known);
Console.WriteLine(allowed);
Console.WriteLine(id);
Console.WriteLine(item);
Console.WriteLine(status);
CS],
            'e2' => ['Example 2 — an empty title never reaches storage', 'The service rejects it. stored is 0. Status 400.', <<<'CS'
string caller = "ada";
string title = "";
int stored = 0;
int status = 400;
Console.WriteLine(caller);
Console.WriteLine(title);
Console.WriteLine(stored);
Console.WriteLine(status);
CS],
            'micro' => [
                'If you add PATCH, it uses the same service rule and the same record.',
                'Move the port and the password to the environment before this runs anywhere but your machine.',
                'When you explain the project, start from Ada\'s request and walk the pipeline. Do not start from the class list.',
            ],
            'quiz' => [
                $q('What runs before the service on an allowed read?', 'Authentication and authorization', 'Only the health probe', 'A redirect', 'The database close'),
                $q('What status is an empty title, and is the note stored?', '400, and nothing is stored', '201, and it is stored', '401, and it is stored', '200, and the private row is returned'),
                $q('Where is health relative to the notes group?', 'Outside it', 'Inside it, with the title filter', 'It replaces GET /notes', 'It is the create route'),
            ],
            'practice' => 'Walk four requests: Ada reads 4, Ada creates tea, Ada sends an empty title, and a caller with no credential. For each, give the status and whether the service stores a note.',
        ]),
    ];
}
