<?php
declare(strict_types=1);

function express_chapters_c(): array
{
    $L = static function (array $c): array {
        return track_lesson('nodejs', 'notes.js', $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };

    return [
        $L([
            'section' => 'The API',
            'title' => 'Check the body before you store it',
            'keywords' => 'express validation, 400, required title',
            'lead' => 'A create route that stores req.body.title with no check will store an empty note, a number, or a missing field. Check the shape first. If the title is missing or is not text, send 400 and do not store anything. If it is text, send 201 and the new id. The client can repair a 400. It cannot repair a note you already saved wrong.',
            'heading' => 'One function, two endings',
            'para' => 'In the real route you write an if. The failing branch sends 400 and returns. The passing branch stores the note and sends 201. This playground cannot take the if, so each example is one branch. The statuses are the ones the if would send. Trim the title before you test it, so a title of spaces is still empty. Decide a maximum length and reject anything longer with the same 400.',
            'bullets' => [
                'Presence is not enough. The title must be text.',
                'Return after 400 so the store line cannot run.',
                '201 carries the id the client will use later.',
                'Do not send 200 with an error field for a bad title. The status is the signal.',
            ],
            'visual' => tf_split('excheck', 'Before anything is stored', 'Is the title usable?', 'yes', '201 and the new id', 'no', '400 and no store', 'The bad branch returns. The good branch is the only one that writes.'),
            'rows' => [
                ['Missing title', '400', 'Nothing is stored.'],
                ['Number title', '400', 'The field has the wrong type.'],
                ['Spaces only', '400', 'Trim before you test.'],
                ['Usable text', '201', 'Store, then answer.'],
                ['New id', 'In the 201 body', 'The client needs it.'],
                ['200 plus an error field', 'Avoid', 'The status should say it failed.'],
            ],
            'e1' => ['Example 1 — the reject branch', 'No usable title. Status 400. The note is not created.', <<<'JS'
const express = require('express');
const app = express();
app.use(express.json());
app.post('/notes', (req, res) => {
  res.status(400).json({ error: 'title is required' });
});
app.listen(3000);
// trace: POST /notes {}
JS],
            'e2' => ['Example 2 — the store branch', 'The title is text. Status 201, and the body echoes the title you would store.', <<<'JS'
const express = require('express');
const app = express();
app.use(express.json());
app.post('/notes', (req, res) => {
  res.status(201).json({ id: '4', item: req.body.title });
});
app.listen(3000);
// trace: POST /notes {"title":"tea"}
JS],
            'micro' => [
                'Check length too. A title of ten thousand characters is still bad.',
                'Validate after the parser. Before the parser, req.body is empty and every create looks invalid.',
                'The same rules belong in one function so PATCH uses them as well.',
            ],
            'quiz' => [
                $q('What status do you send when the title is missing?', '400', '201', '404', '204'),
                $q('What should happen to storage on that branch?', 'Nothing is stored', 'An empty note is stored', 'The previous note is deleted', 'The port closes'),
                $q('Why return after the 400?', 'So the store line cannot still run', 'To parse the body again', 'To change the method to GET', 'To set the port'),
            ],
            'practice' => 'List three bodies you would reject with 400 and one body you would accept with 201. Name the field you return on success.',
        ]),
        $L([
            'section' => 'The API',
            'title' => 'CORS is a response header you choose to send',
            'keywords' => 'cors, access-control-allow-origin, preflight',
            'lead' => 'A browser page on one origin will not read your API on another origin unless you say it may. CORS is that permission, sent as response headers. An allow-list names the origins you accept. Reflecting whatever Origin the request sent is not an allow-list. A star origin cannot be combined with credentials.',
            'heading' => 'The browser enforces the header',
            'para' => 'Your server can still answer a request from anywhere. The browser hides the answer from page script when the CORS headers do not permit it. A preflight is an OPTIONS request the browser sends before some POSTs. You answer the preflight with the allow headers and no note is created. Tools that are not browsers do not apply CORS. CORS is not authentication. A permitted origin is still an anonymous caller until you check a credential.',
            'bullets' => [
                'Allow one origin, or a list you wrote down. Do not echo every Origin.',
                'Allow-Origin star conflicts with Allow-Credentials.',
                'Answer OPTIONS before the real POST. Do not create a note during the preflight.',
                'A 403 for an origin you refused is a choice. The browser would have blocked the read anyway.',
            ],
            'visual' => tf_split('excors', 'Is this origin on your list?', 'Origin you wrote down?', 'yes', 'send the allow header', 'no', 'do not grant the read', 'The list is yours. The request does not get to add itself.'),
            'rows' => [
                ['Allow-Origin', 'Who may read', 'An origin you chose.'],
                ['Star', 'Any origin', 'Not with credentials.'],
                ['Echo the request', 'Not a list', 'Anyone can ask to be added.'],
                ['OPTIONS', 'The preflight', 'No note is created.'],
                ['Credentials', 'Cookies on the request', 'Needs a specific origin.'],
                ['Authentication', 'A different step', 'CORS does not name the caller.'],
            ],
            'e1' => ['Example 1 — an origin you allow still reaches the note', 'The preview is the allowed branch. The real response would also carry the allow header for that origin.', <<<'JS'
const express = require('express');
const app = express();
app.use((req, res, next) => {
  console.log('allow list');
  next();
});
app.get('/notes', (req, res) => {
  res.json({ item: 'milk' });
});
app.listen(3000);
// trace: GET /notes
JS],
            'e2' => ['Example 2 — an origin you did not list is refused', 'This is the branch for an origin that is not on the list. The note route does not run.', <<<'JS'
const express = require('express');
const app = express();
app.use((req, res, next) => {
  res.status(403).json({ error: 'origin refused' });
});
app.get('/notes', (req, res) => {
  res.json({ item: 'milk' });
});
app.listen(3000);
// trace: GET /notes
JS],
            'micro' => [
                'Vary: Origin belongs on the response when the allow header depends on the request origin, so caches do not mix two origins.',
                'A server-rendered page on the same origin does not need CORS to call a relative /notes.',
                'Do not treat a passing CORS check as a logged-in user.',
            ],
            'quiz' => [
                $q('What is an allow-list?', 'The origins you decided may read the response', 'Every Origin header that arrives', 'The port number', 'The JSON body'),
                $q('Can a star origin be used together with credentials?', 'No', 'Yes', 'Only for GET', 'Only on port 443'),
                $q('What should an OPTIONS preflight avoid?', 'Creating a note', 'Sending status headers', 'Reaching the server', 'Using HTTPS'),
            ],
            'practice' => 'Write the one origin you would allow for a notes API, and say why echoing the request Origin is a different policy.',
        ]),
        $L([
            'section' => 'The API',
            'title' => 'Put the version in the path',
            'keywords' => 'api version, /v1, express prefix',
            'lead' => 'Clients keep calling an API after you need to change it. A version in the path lets the old contract and the new contract exist together. /v1/notes stays what it was. /v2/notes can rename a field. A query parameter is easy to forget. A header is invisible in logs. The path shows up in every request line.',
            'heading' => 'One mount per contract',
            'para' => 'Mount the notes router at /v1. Inside the router the paths stay short: / for the collection and /:id for one note. The public path is /v1 and /v1/4. When a field must change in a way old clients cannot accept, add /v2 and leave /v1 up until those clients have moved. Changing /v1 in place is how you break people who did nothing wrong.',
            'bullets' => [
                'The version is a prefix, not a field in every JSON object.',
                'Breaking changes get a new prefix. Additive fields can stay on the old one.',
                'Log the path so you can see which version is still used.',
                'Do not make the version optional. /notes and /v1/notes become two contracts by accident.',
            ],
            'visual' => tf_flow('exver', 'The prefix is the contract', [['/v1', 'the mount'], ['/', 'the router path'], ['/v1', 'the client path']], 'This picture uses a router path of / mounted at /v1. The client calls /v1.'),
            'rows' => [
                ['/v1', 'The first contract', 'Leave it stable.'],
                ['/v2', 'The next contract', 'Only when the old one would break.'],
                ['Inside the router', 'Short paths', 'The prefix is the mount.'],
                ['Query version', 'Easy to skip', 'Prefer the path.'],
                ['Old clients', 'Stay on /v1', 'Until they move.'],
                ['Unversioned /notes', 'A second contract', 'Do not add it beside /v1.'],
            ],
            'e1' => ['Example 1 — the public path includes the version', 'The router path is /. The mount is /v1. The client calls /v1.', <<<'JS'
const express = require('express');
const app = express();
const notes = express.Router();
notes.get('/', (req, res) => {
  res.json({ item: 'milk' });
});
app.use('/v1', notes);
app.listen(3000);
// trace: GET /v1
JS],
            'e2' => ['Example 2 — the unversioned path is not the contract', 'GET /notes was never mounted. The answer is 404.', <<<'JS'
const express = require('express');
const app = express();
const notes = express.Router();
notes.get('/', (req, res) => {
  res.json({ item: 'milk' });
});
app.use('/v1', notes);
app.listen(3000);
// trace: GET /notes
JS],
            'micro' => [
                'A date in the path is another style. Pick path versions and stay with them.',
                'Document the version you support. A sunset date belongs in that document, not only in a meeting.',
                'Health can stay unversioned at /health. It is not the notes contract.',
            ],
            'quiz' => [
                $q('Where should the version show up for this API?', 'In the path, as a prefix', 'Only inside a JSON error', 'Only in the port', 'Only in a cookie'),
                $q('What is GET /notes when the router is mounted only at /v1?', '404', '200', '201', 'The milk note'),
                $q('When do you add /v2?', 'When a change would break clients of /v1', 'Every Monday', 'When the port changes', 'When a title is missing'),
            ],
            'practice' => 'Write the public paths for list and read-one under /v1. Then write the same two paths under /v2 after a field rename.',
        ]),
        $L([
            'section' => 'The API',
            'title' => 'A health route does no business work',
            'keywords' => 'health check, liveness, readiness, express',
            'lead' => 'A health route answers whether the process can be used. It does not create a note, list private titles, or run a report. Liveness means the process is up. Readiness means it can serve, which may include a database it depends on. A load balancer calls these paths often. Keep them cheap and free of secrets.',
            'heading' => 'Two questions, two paths',
            'para' => '/health can mean liveness: the event loop is responding, so the answer is { ok: true } and 200. /ready can mean readiness: if the dependency is down, answer 503 so the balancer stops sending customers, while the process stays up to recover. Do not put the database password, the list of notes, or a stack in either body.',
            'bullets' => [
                'Liveness 200 means the process answered. It does not mean the database is well.',
                'Readiness 503 tells the balancer to wait. It is not a customer error.',
                'Neither route writes a note.',
                'Do not require a login for the probe if the balancer cannot log in. Do not expose private data to make that easy.',
            ],
            'visual' => tf_panels('exhealth', 'Two probes', 'liveness', ['/health', 'process is up', 'no note written'], 'readiness', ['/ready', 'can serve traffic', '503 when a dependency is down'], 'Customers use /notes. The balancer uses these two paths.'),
            'rows' => [
                ['/health', 'Liveness', '200 when the process answers.'],
                ['/ready', 'Readiness', '503 when a dependency is down.'],
                ['Body', 'Small', 'ok true, or a short reason.'],
                ['Notes', 'Not here', 'No titles, no secrets.'],
                ['Cost', 'Cheap', 'Called all the time.'],
                ['Auth', 'Usually open', 'Then the body must stay dull.'],
            ],
            'e1' => ['Example 1 — liveness', 'The process answered. No note was written.', <<<'JS'
const express = require('express');
const app = express();
app.get('/health', (req, res) => {
  res.json({ ok: true });
});
app.listen(3000);
// trace: GET /health
JS],
            'e2' => ['Example 2 — not ready', 'The process is up enough to answer, and it tells the balancer to stop sending work.', <<<'JS'
const express = require('express');
const app = express();
app.get('/ready', (req, res) => {
  res.status(503).json({ ok: false });
});
app.listen(3000);
// trace: GET /ready
JS],
            'micro' => [
                'A ready check that lists every note is no longer a probe. It is an API.',
                'If readiness calls the database, give that call a short timeout so the probe cannot hang.',
                'Keep /health off the notes router so a notes bug does not hide the process.',
            ],
            'quiz' => [
                $q('What does a liveness route prove?', 'The process answered', 'A note was created', 'The caller is logged in', 'The title was valid'),
                $q('Which status tells a balancer to stop sending customers while a dependency is down?', '503', '201', '400', '301'),
                $q('What should the health body avoid?', 'Secrets and the list of notes', 'A short ok field', 'Status 200', 'The path /health'),
            ],
            'practice' => 'Define /health and /ready in one sentence each. Say which one may return 503, and name one thing neither body may contain.',
        ]),
        $L([
            'section' => 'Production habits',
            'title' => 'The port and secrets come from the environment',
            'keywords' => 'process env, port, secrets, express config',
            'lead' => 'The port, the database address, and any secret belong in the environment of the machine, not in the source file you commit. The program reads them at startup. If a required value is missing, refuse to listen and say which name is missing. A default port is acceptable. A default password is not.',
            'heading' => 'Read once, then listen',
            'para' => 'A later source overrides an earlier one: a file of defaults, then the environment, then an explicit flag if you have one. The value that wins is the one listen and the database client both see. Do not log the secret after you read it. Log that the name was present. This preview listens on 4000 because that is the number a real program would have read into the port before calling listen.',
            'bullets' => [
                'PORT can have a default. A database password cannot.',
                'Missing required config is a startup failure, not a 200.',
                'The same value must feed listen and any URL you print.',
                'Source code may contain the name of the variable. It must not contain the secret.',
            ],
            'visual' => tf_flow('exenv', 'Outside the file, then the process', [['environment', 'the values'], ['read once', 'at startup'], ['listen', 'the port']], 'The file knows the names. The machine supplies the values.'),
            'rows' => [
                ['PORT', 'Which door', 'A default is fine.'],
                ['Password', 'A secret', 'No default in source.'],
                ['Missing secret', 'Do not listen', 'Name the missing key.'],
                ['Log', 'The name only', 'Not the value.'],
                ['Later source', 'Wins', 'Environment overrides a file.'],
                ['Committed file', 'Names, not values', 'The secret stays on the machine.'],
            ],
            'e1' => ['Example 1 — listen on the port you were given', '4000 stands in for the number read from the environment. The health route uses that same process.', <<<'JS'
const express = require('express');
const app = express();
app.get('/health', (req, res) => {
  res.json({ ok: true });
});
app.listen(4000);
// trace: GET /health
JS],
            'e2' => ['Example 2 — a required value was not there', 'The process should refuse to serve notes. The preview shows the failure answer instead of pretending the database exists.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes', (req, res) => {
  res.status(500).json({ error: 'config missing' });
});
app.listen(3000);
// trace: GET /notes
JS],
            'micro' => [
                'A .env file on a laptop is still a secret file. Do not commit it.',
                'Print the port at startup. Do not print the password beside it.',
                'If two instances must share a secret, the platform injects it. You do not paste it into the router file.',
            ],
            'quiz' => [
                $q('Where does a database password belong?', 'In the environment of the machine', 'In the router file', 'In the JSON body of /health', 'In a comment'),
                $q('What is acceptable as a default?', 'The port', 'The password', 'A customer token', 'The database address with a real password'),
                $q('What should a log line include after a secret is read?', 'The name of the variable', 'The secret itself', 'The notes list', 'The caller cookie'),
            ],
            'practice' => 'Name two values you would read from the environment for this API. Mark which one may have a default, and what you do if the other is missing.',
        ]),
        $L([
            'section' => 'Production habits',
            'title' => 'Log the request, and leave the secret out',
            'keywords' => 'express logging, request id, redact',
            'lead' => 'One line per request is enough to start: method, path, status, and how long it took. That line answers "did the call arrive and what did we answer?" It does not need the Authorization header, the cookie, or a body that might hold a password. If a credential was present, log the word present.',
            'heading' => 'A line you can read at 3 a.m.',
            'para' => 'Write the line when the response finishes, so the status is known. A logger placed above the routes can record the method and path on the way in, which is what this preview shows. Add a request id if you have one, the same id on every line for that call. Do not build the line by joining raw headers. Pick the fields. Paths can contain an id, and that is usually fine. A query string might contain a token. Prefer not to log the raw query until you know it is only a search word.',
            'bullets' => [
                'Method, path, status, duration. Those four are the habit.',
                'Authorization and cookies stay out of the line.',
                'present is enough when you need to know a credential was sent.',
                'The notes titles may be private. Do not log every body by default.',
            ],
            'visual' => tf_panels('exlog', 'What the line may hold', 'keep', ['method', 'path', 'status'], 'leave out', ['password', 'cookie', 'raw credential'], 'The line is for you. It is not a second copy of the request.'),
            'rows' => [
                ['Method and path', 'Keep', 'They name the call.'],
                ['Status', 'Keep', 'Write it when the response finishes.'],
                ['Duration', 'Keep', 'Slow calls show up.'],
                ['Authorization', 'Leave out', 'Log present if you must.'],
                ['Body', 'Leave out by default', 'It may hold a secret.'],
                ['Request id', 'Keep, when you have one', 'The same id on every line.'],
            ],
            'e1' => ['Example 1 — the line, then the note', 'The preview prints a safe line and then the note.', <<<'JS'
const express = require('express');
const app = express();
app.use((req, res, next) => {
  console.log('GET /notes');
  next();
});
app.get('/notes', (req, res) => {
  res.json({ item: 'milk' });
});
app.listen(3000);
// trace: GET /notes
JS],
            'e2' => ['Example 2 — a credential is mentioned, not quoted', 'The line says the credential was present. It does not contain the secret.', <<<'JS'
const express = require('express');
const app = express();
app.use((req, res, next) => {
  console.log('credential present');
  next();
});
app.get('/notes', (req, res) => {
  res.json({ item: 'milk' });
});
app.listen(3000);
// trace: GET /notes
JS],
            'micro' => [
                'Log the status from the finish event, not only from the first middleware, or you will record a status you have not chosen yet.',
                'A 500 line should include the server-side error. The client body stays short.',
                'Do not log health checks at the same volume as customer calls if they drown the file. A counter is enough for probes.',
            ],
            'quiz' => [
                $q('Which four fields are the basic request line?', 'Method, path, status, and duration', 'Password, cookie, body, and port', 'Only the JSON title', 'Only the stack'),
                $q('How should a present Authorization header appear?', 'As the word present, not the value', 'In full', 'As the notes list', 'As status 200 always'),
                $q('When do you know the status to log?', 'When the response finishes', 'Before the route runs', 'Inside listen', 'When the file is saved'),
            ],
            'practice' => 'Write one log line for POST /notes that returned 201. Then cross out every field that would have revealed a secret.',
        ]),
        $L([
            'section' => 'Production habits',
            'title' => 'Shutdown stops listening before it drops work',
            'keywords' => 'server.close, SIGTERM, graceful shutdown, express',
            'lead' => 'A deploy sends the process a signal to leave. The polite order is: stop accepting new connections, finish the requests you already have, then exit. server.close does the first two. If you exit first, those requests die in the middle of a create and the client retries into a mess.',
            'heading' => 'Close the door, finish the room, then leave',
            'para' => 'Keep the server object that listen returns. On the signal, call close. New clients should see a connection failure or a 503 from the balancer, not a half-written note. Give the remaining work a time limit. If it does not finish, exit anyway so a stuck request cannot block the deploy forever. This preview prints the order on a health call so you can say the three steps. The second example is the 503 you might still answer with while you are draining, if you choose to keep a route up for that moment.',
            'bullets' => [
                'Stop listening first. New work goes elsewhere.',
                'Finish requests that already started.',
                'Then exit. A timeout covers work that will not finish.',
                'Close the database after the requests, not before, or the in-flight create fails.',
            ],
            'visual' => tf_flow('exstop', 'Leave in this order', [['stop listening', 'no new work'], ['finish work', 'requests already in'], ['exit', 'after a limit']], 'The signal starts the sequence. It does not kill the current note.'),
            'rows' => [
                ['Signal', 'Time to leave', 'The platform sends it.'],
                ['close', 'Stop accepting', 'In-flight work may finish.'],
                ['Exit immediately', 'Drops that work', 'Avoid it.'],
                ['Timeout', 'A backstop', 'A stuck call cannot block forever.'],
                ['Database', 'Close it last', 'After the requests.'],
                ['503 while draining', 'Optional', 'The balancer should already be looking away.'],
            ],
            'e1' => ['Example 1 — the three steps, in order', 'The lines are the sequence. The 200 is only so the preview has a finished response.', <<<'JS'
const express = require('express');
const app = express();
app.get('/health', (req, res) => {
  console.log('stop listening');
  console.log('finish work');
  console.log('then exit');
  res.json({ ok: true });
});
app.listen(3000);
// trace: GET /health
JS],
            'e2' => ['Example 2 — a drain answer', 'If you still answer while leaving, 503 tells the caller this instance is not taking new work.', <<<'JS'
const express = require('express');
const app = express();
app.get('/health', (req, res) => {
  res.status(503).json({ ok: false });
});
app.listen(3000);
// trace: GET /health
JS],
            'micro' => [
                'listen returns the server. You need that object to close it. Calling listen and ignoring the return value makes shutdown harder.',
                'A second signal can exit immediately. The first one should be the polite path.',
                'Readiness should fail as soon as you begin to leave, so the balancer stops sending work before close finishes.',
            ],
            'quiz' => [
                $q('What is the first shutdown step?', 'Stop accepting new connections', 'Delete the notes', 'Send 201', 'Print the database password'),
                $q('Why not exit at the first moment?', 'Requests already running would be dropped', 'Ports cannot close', 'JSON would become text', 'CORS would fail'),
                $q('When do you close the database?', 'After in-flight requests finish', 'Before you stop listening', 'On every GET', 'Inside the logger'),
            ],
            'practice' => 'Write the three shutdown steps in order, and add the moment when readiness should start failing.',
        ]),
        $L([
            'section' => 'Production habits',
            'title' => 'Security headers and a request limit',
            'keywords' => 'helmet, rate limit, 429, express security headers',
            'lead' => 'Two small habits sit in front of the routes. A header middleware tells the browser not to sniff content types and not to frame your API. A counter tells one caller to slow down after too many calls in a window, and answers 429. Neither one is a puzzle. They are ordinary limits.',
            'heading' => 'Headers on the way out, a counter on the way in',
            'para' => 'The header set is the same on every response, so it belongs in middleware above the routes. The counter is per caller, often per address, and it resets when the window passes. When the count is still under the limit, call next. When it is over, send 429 and do not run the route. A health probe should not eat the customer limit. Count customer routes, or give probes their own budget. Do not put a secret in the 429 body.',
            'bullets' => [
                'nosniff tells the browser to trust the content type you sent.',
                'A frame policy keeps another site from embedding your response.',
                '429 means the caller crossed a limit you chose. It is not a 400 and not a 500.',
                'The route does not run on the 429 branch.',
            ],
            'visual' => tf_split('exlimit', 'Is this caller still inside the window?', 'Under the limit?', 'yes', 'next(), the route runs', 'no', '429, the route does not', 'The counter is a number. Crossing it is a normal answer.'),
            'rows' => [
                ['nosniff', 'A response header', 'Do not guess the content type.'],
                ['Frame policy', 'A response header', 'Do not embed this response.'],
                ['Counter', 'Requests in a window', 'One caller.'],
                ['Under the limit', 'next()', 'The route answers.'],
                ['Over the limit', '429', 'A short error, no secret.'],
                ['Health probes', 'Their own budget', 'Or they starve the customers.'],
            ],
            'e1' => ['Example 1 — headers, then the note', 'The line stands in for the header middleware. The route still answers.', <<<'JS'
const express = require('express');
const app = express();
app.use((req, res, next) => {
  console.log('headers set');
  next();
});
app.get('/notes', (req, res) => {
  res.json({ item: 'milk' });
});
app.listen(3000);
// trace: GET /notes
JS],
            'e2' => ['Example 2 — the limit branch', 'The caller crossed the window. Status 429. The note route does not run.', <<<'JS'
const express = require('express');
const app = express();
app.use((req, res, next) => {
  res.status(429).json({ error: 'slow down' });
});
app.get('/notes', (req, res) => {
  res.json({ item: 'milk' });
});
app.listen(3000);
// trace: GET /notes
JS],
            'micro' => [
                'Pick a limit you can explain, such as a hundred creates a minute for one address. Write the number next to the code.',
                '429 can include a later hint for when to try again. Keep it short.',
                'Headers do not replace authentication. They tell the browser how to handle the bytes.',
            ],
            'quiz' => [
                $q('What status means the caller crossed your request limit?', '429', '400', '201', '301'),
                $q('Does the note route run on that branch?', 'No', 'Yes', 'Only for GET', 'Only the first time'),
                $q('What is nosniff for?', 'It tells the browser to trust the content type you sent', 'It stores the note', 'It sets the port', 'It parses a form'),
            ],
            'practice' => 'Choose a limit for POST /notes and write the status you return when it is crossed. Name one header you would set on every response.',
        ]),
        $L([
            'section' => 'Production habits',
            'title' => 'Redirects are a status plus a Location header',
            'keywords' => '301, 302, Location, express redirect',
            'lead' => 'A redirect tells the client to ask again at another path. The status is 301 or 302, and the Location header is the path. 301 means permanent: clients and caches may remember it. 302 means temporary: they should ask the old path again next time. The path must be one you chose.',
            'heading' => 'Do not send the client to an address they invented',
            'para' => 'A notes app might move /old to /notes. You decide that pair. If you build Location from a full address the client sent, the client can send people to a site you do not operate. Keep Location on your own paths. The preview shows the status and the path you would put in Location. A real redirect uses the header, and the body can be empty. GET may follow. A POST that becomes a GET after 302 is a surprise to watch for, so prefer answering the POST with 201 when the create itself succeeded.',
            'bullets' => [
                '301 is remembered. Use it when the old path is gone for good.',
                '302 is not remembered as the new home. Use it for a short move.',
                'Location is a path you wrote, not a full address taken from the query.',
                'A successful create is 201, not a redirect, unless you have a browser form that needs one.',
            ],
            'visual' => tf_flow('exredir', 'Status, then the next path', [['301 or 302', 'the status'], ['Location', 'a path you chose'], ['client', 'asks again']], 'The client makes a second request. You do not send the note inside the redirect itself.'),
            'rows' => [
                ['301', 'Permanent', 'Caches may store it.'],
                ['302', 'Temporary', 'The old path can be used again.'],
                ['Location', 'The next path', 'Your path, not their address.'],
                ['Body', 'Optional', 'The header does the work.'],
                ['Create', '201', 'Not a redirect, for an API client.'],
                ['Invented address', 'Refuse', 'Do not put it in Location.'],
            ],
            'e1' => ['Example 1 — a permanent move', '301, and the path a Location header would carry.', <<<'JS'
const express = require('express');
const app = express();
app.get('/old', (req, res) => {
  res.status(301).json({ location: '/notes' });
});
app.listen(3000);
// trace: GET /old
JS],
            'e2' => ['Example 2 — a temporary move', '302. Next time the client may still try /old.', <<<'JS'
const express = require('express');
const app = express();
app.get('/old', (req, res) => {
  res.status(302).json({ location: '/notes' });
});
app.listen(3000);
// trace: GET /old
JS],
            'micro' => [
                '301 on a POST can be replayed as GET by some clients. Do not use 301 as the success status of a create.',
                'A relative Location is enough when the client is already talking to you.',
                'If the old path and the new path are both yours, say so in the API notes so people can update.',
            ],
            'quiz' => [
                $q('Which status is a permanent redirect?', '301', '302', '201', '429'),
                $q('What must Location be?', 'A path you chose', 'Any address the client sent', 'The database password', 'The port only'),
                $q('What should a successful JSON create use instead of a redirect?', '201', '301', '302', '304'),
            ],
            'practice' => 'Pick 301 or 302 for a path you renamed from /old to /notes, and write the Location value. Say why you would not build Location from a query field named next.',
        ]),
        $L([
            'section' => 'Production habits',
            'title' => 'One response ends the request',
            'keywords' => 'headers sent, res.json once, 204',
            'lead' => 'You get one status and one body. After res.json, res.send, or sendStatus, the response has started. A second write throws because the headers were already sent. The route should pick one ending and return. 204 is an ending with a status and no body, which fits a delete when you have nothing more to say.',
            'heading' => 'Pick the ending, then stop',
            'para' => 'The bug is usually an if that sends 400 and then falls through to a 201, or a logger that sends and also calls next. Return on the error branch. Do not send 200 with { error: true } as a second style of failure. The status already has a meaning. 204 must not include a JSON body. Clients are allowed to ignore a body on 204, so a message there is a message they can miss.',
            'bullets' => [
                'One of json, send, or sendStatus. Not two.',
                'Return after the failure send.',
                '204 has no body. Use it when delete succeeded and there is nothing to show.',
                '200 with an error field hides the failure from clients that only check the status.',
            ],
            'visual' => tf_split('exonce', 'One ending', 'Already answered?', 'yes', 'stop writing', 'no', 'send one status', 'The first ending wins. The second one is an accident.'),
            'rows' => [
                ['First json', 'Ends it', 'Status and body.'],
                ['Second json', 'Throws', 'Headers already sent.'],
                ['return', 'Stops your function', 'The fall-through cannot send.'],
                ['204', 'Success, empty', 'No JSON body.'],
                ['200 and error true', 'Avoid', 'Use 400 or 500.'],
                ['next after send', 'Avoid', 'The next function may write too.'],
            ],
            'e1' => ['Example 1 — one JSON ending', 'A single 200. There is no second write.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes', (req, res) => {
  res.status(200).json({ item: 'milk' });
});
app.listen(3000);
// trace: GET /notes
JS],
            'e2' => ['Example 2 — a delete with nothing to show', '204. The preview has a status and no body.', <<<'JS'
const express = require('express');
const app = express();
app.delete('/notes/:id', (req, res) => {
  res.sendStatus(204);
});
app.listen(3000);
// trace: DELETE /notes/4
JS],
            'micro' => [
                'If you set a header, do it before the ending write.',
                'An async function needs the same discipline: one answer, after the await, not before and after.',
                'A 404 for a missing note is still one answer. Do not follow it with a 200.',
            ],
            'quiz' => [
                $q('How many bodies does one request get?', 'One', 'One per middleware', 'One per header', 'As many as you send'),
                $q('What does 204 include?', 'A status and no body', 'A JSON note', 'A redirect', 'The password'),
                $q('Why avoid 200 with an error field?', 'Clients that only read the status will treat the failure as success', '201 is illegal', 'JSON cannot hold a title', 'The port would close'),
            ],
            'practice' => 'Mark the single line that ends each of these: a good read, a bad title, and a successful delete. Say what you return after the bad-title line.',
        ]),
        $L([
            'section' => 'Production habits',
            'title' => 'Mount more than one router when the app grows',
            'keywords' => 'express multiple routers, mount, notes and health',
            'lead' => 'One file that holds every route becomes hard to read. Split by noun. A notes router owns /notes. A health router owns /health. The main file creates the app, registers the shared middleware, mounts the routers, and listens. The routers do not listen.',
            'heading' => 'The main file stays short',
            'para' => 'Each router file exports the router. Paths inside notes are / and /:id, and the mount adds /notes. Paths inside health are /, and the mount adds /health. Shared middleware, such as the logger and the JSON parser, stays on the app so both areas see it. A guard that only notes need belongs on the notes router, not on health.',
            'bullets' => [
                'One router per area. Notes and health are the first split.',
                'The main file mounts them and is the only file that listens.',
                'Shared middleware goes on the app, above the mounts.',
                'A notes-only guard goes on the notes router so /health stays boring.',
            ],
            'visual' => tf_flow('exmount', 'One app, two areas', [['app', 'shared steps'], ['/notes', 'the notes router'], ['/health', 'the health router']], 'The client calls the mounted paths. Each router only knows its own short paths.'),
            'rows' => [
                ['notes router', 'The noun', 'Mounted at /notes.'],
                ['health router', 'The probes', 'Mounted at /health.'],
                ['Main file', 'Wiring', 'use, mount, listen.'],
                ['Shared middleware', 'On the app', 'Both areas see it.'],
                ['Notes guard', 'On the notes router', 'Health does not.'],
                ['listen in a router', 'Do not', 'You would open a second port.'],
            ],
            'e1' => ['Example 1 — the notes mount', 'GET /notes is the router path / mounted at /notes.', <<<'JS'
const express = require('express');
const app = express();
const notes = express.Router();
notes.get('/', (req, res) => {
  res.json({ item: 'milk' });
});
app.use('/notes', notes);
app.listen(3000);
// trace: GET /notes
JS],
            'e2' => ['Example 2 — the health mount beside it', 'A second router does not disturb the first. GET /health is its own mount.', <<<'JS'
const express = require('express');
const app = express();
const notes = express.Router();
notes.get('/', (req, res) => {
  res.json({ item: 'milk' });
});
const health = express.Router();
health.get('/', (req, res) => {
  res.json({ ok: true });
});
app.use('/notes', notes);
app.use('/health', health);
app.listen(3000);
// trace: GET /health
JS],
            'micro' => [
                'Export the router, not the app, from the area file.',
                'A mount path and a route path both ending in a slash can fail to join. Keep the mount without a trailing slash and the collection route as /.',
                'When a third area appears, add a third router. Do not grow the main file into a list of every title.',
            ],
            'quiz' => [
                $q('Which file should call listen?', 'The main file', 'Every router file', 'The health router only', 'The JSON parser'),
                $q('Where does a notes-only guard belong?', 'On the notes router', 'On the health router', 'Inside listen', 'In the log line'),
                $q('What is the public path of notes.get(\'/\') mounted at /notes?', '/notes', '/', '/health', '/notes/notes'),
            ],
            'practice' => 'Name the three files you would have for this app and the one function each file is responsible for.',
        ]),
        $L([
            'section' => 'The project',
            'title' => 'Project: a notes API you can explain',
            'keywords' => 'express notes api project, middleware, router, status codes',
            'lead' => 'This is the small API the lessons were building. A logger runs first and calls next. The JSON parser runs next, so a body can become fields. A notes router mounted at /notes lists and creates. A health route stays on the app and writes nothing. You should be able to point at each line and say what a request sees.',
            'heading' => 'Say the request out loud',
            'para' => 'GET /notes enters the logger, skips a create, and returns the milk note with 200. POST /notes with a title enters the logger, reads the title, and returns 201. GET /health does not create a note. A missing title is 400 and stores nothing. An id that is not there is 404. Those five sentences are the project. The first preview runs the three successful calls. The second preview runs the two failures. In your own file the 400 is an if inside the create route, and the 404 is the read route when the id is unknown.',
            'bullets' => [
                'Logger, then parser, then routes. That order is the app.',
                'The notes router owns the noun. Health stays separate.',
                '201 for a create, 200 for a read, 400 for a bad title, 404 for an unknown id.',
                'One response per request. The secret stays out of the log line.',
            ],
            'visual' => tf_flow('exproj', 'The notes API', [['logger', 'next()'], ['parser', 'the body'], ['/notes', 'list or create'], ['/health', 'no note']], 'Four stops. A request only does the work of the route that matched.'),
            'rows' => [
                ['GET /notes', '200', 'The list, here one milk note.'],
                ['POST /notes', '201', 'The title from the body.'],
                ['GET /health', '200', 'ok true, nothing stored.'],
                ['Bad title', '400', 'Nothing stored.'],
                ['Unknown id', '404', 'The path was fine, the note is not there.'],
                ['Log line', 'Method and path', 'Not a credential.'],
            ],
            'e1' => ['Example 1 — list, create, and health', 'Three traces, one app. The logger runs for each of them.', <<<'JS'
const express = require('express');
const app = express();
app.use((req, res, next) => {
  console.log('logger');
  next();
});
app.use(express.json());
const notes = express.Router();
notes.get('/', (req, res) => {
  res.json({ item: 'milk' });
});
notes.post('/', (req, res) => {
  res.status(201).json({ item: req.body.title });
});
app.use('/notes', notes);
app.get('/health', (req, res) => {
  res.json({ ok: true });
});
app.listen(3000);
// trace: GET /notes
// trace: POST /notes {"title":"tea"}
// trace: GET /health
JS],
            'e2' => ['Example 2 — a bad title and an unknown id', 'These are the two failure endings. Each one is a single response.', <<<'JS'
const express = require('express');
const app = express();
app.use(express.json());
app.post('/notes', (req, res) => {
  res.status(400).json({ error: 'title is required' });
});
app.get('/notes/:id', (req, res) => {
  res.status(404).json({ error: 'not found' });
});
app.listen(3000);
// trace: POST /notes {}
// trace: GET /notes/4
JS],
            'micro' => [
                'If you add PATCH later, it belongs on the notes router, with the same title check as create.',
                'Move the port to the environment before you run this anywhere but your laptop.',
                'When you explain the project, start from a single request and walk the middleware list. Do not start from the file tree.',
            ],
            'quiz' => [
                $q('What runs before the notes router in the first example?', 'The logger and the JSON parser', 'Only listen', 'The 404 handler', 'A redirect'),
                $q('Which status is a create with a title?', '201', '404', '400', '204'),
                $q('Which status is an unknown id?', '404', '400', '201', '301'),
            ],
            'practice' => 'Walk GET /notes, POST /notes with a title, POST /notes without a title, and GET /health. For each one, name the status and whether a note is stored.',
        ]),
    ];
}
