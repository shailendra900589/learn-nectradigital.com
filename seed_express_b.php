<?php
declare(strict_types=1);

function express_chapters_b(): array
{
    $L = static function (array $c): array {
        return track_lesson('nodejs', 'notes.js', $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };

    return [
        $L([
            'section' => 'Reading the request',
            'title' => 'Headers are a map on the way in and the way out',
            'keywords' => 'req.get, res.set, http headers, content type',
            'lead' => 'Headers are named fields around the body. The client sends Accept, Content-Type, and sometimes Authorization. You send Content-Type, Location, and cache hints. In Express, req.get(\'content-type\') reads one incoming header. Names are treated without caring about letter case. res.set writes an outgoing header before you send the body.',
            'heading' => 'Read them, then decide',
            'para' => 'Do not trust a header as proof of identity until an authentication step has checked it. A content type tells you which parser should run. A missing content type on a POST with a body is a 400 if your API requires JSON. Set headers before the body. After the body starts, the headers are already on the wire.',
            'bullets' => [
                'req.get reads one request header. res.set writes one response header.',
                'Header names are case-insensitive. content-type and Content-Type are the same key.',
                'Authorization is a secret. Log the fact that it was present, not the value.',
                'Location is how a create tells the client the new path.',
            ],
            'visual' => tf_flow('exhead', 'Headers wrap the body', [['request headers', 'what arrived'], ['body', 'the bytes'], ['response headers', 'what you set']], 'The body is not the only message. The headers say what the body is.'),
            'rows' => [
                ['req.get', 'Read one', 'Case does not matter.'],
                ['res.set', 'Write one', 'Before the body.'],
                ['Content-Type', 'What the bytes are', 'JSON or form.'],
                ['Authorization', 'A credential', 'Do not log the value.'],
                ['Location', 'Where it lives now', 'Often with 201.'],
                ['After send', 'Too late to set', 'Headers already sent.'],
            ],
            'e1' => ['Example 1 — the answer names its type by using json', 'json sets the content type for you and writes the body.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes', (req, res) => {
  res.json({ item: 'milk' });
});
app.listen(3000);
// trace: GET /notes
JS],
            'e2' => ['Example 2 — a create can point at the new note', 'The body is the note. A real Location header would carry /notes/4. This preview shows the id you would put in that header.', <<<'JS'
const express = require('express');
const app = express();
app.post('/notes', (req, res) => {
  res.status(201).json({ id: '4', item: 'tea' });
});
app.listen(3000);
// trace: POST /notes
JS],
            'micro' => [
                'Hop-by-hop headers are for the connection, not for your app logic.',
                'Custom headers are allowed. Prefer a documented field in the JSON when the client is your own app.',
                'Set a header, then send. The other order fails.',
            ],
            'quiz' => [
                $q('How do you read one request header in Express?', 'req.get with the header name', 'req.params', 'res.json', 'app.listen'),
                $q('Are Content-Type and content-type different keys?', 'No', 'Yes', 'Only on POST', 'Only for CORS'),
                $q('When must res.set run?', 'Before the body is sent', 'After res.json', 'Inside listen', 'Never'),
            ],
            'practice' => 'Name two request headers and two response headers a notes API uses, and say which side writes each.',
        ]),
        $L([
            'section' => 'Middleware',
            'title' => 'Middleware is a function that must call next',
            'keywords' => 'express middleware, next, app.use',
            'lead' => 'Middleware is a function (req, res, next). app.use registers it for every request that reaches that point. If the function calls next(), Express moves to the next registered function. If it sends a response, the chain stops. If it does neither, the request hangs.',
            'heading' => 'next is the handoff',
            'para' => 'A logger is the usual first middleware: write one line, then next(). The route later sends the JSON. The logger does not know which route will match. That is the point. Shared work sits in one place. next() with no argument continues. next(err) skips ordinary middleware and looks for an error handler.',
            'bullets' => [
                'The signature is req, res, next. Forgetting next is the usual hang.',
                'app.use without a path runs for every path that reaches it.',
                'Call next() once. Calling it twice continues twice.',
                'A middleware can read req and even add a field, then let the route answer.',
            ],
            'visual' => tf_flow('exnext', 'Log, then hand off, then answer', [['logger', 'next()'], ['route', 'the match'], ['json', 'the body']], 'The logger does not send the note. It calls next so the route can.'),
            'rows' => [
                ['app.use', 'Register it', 'Order matters.'],
                ['next()', 'Continue', 'No argument.'],
                ['res.json inside it', 'Stop', 'The route will not run.'],
                ['Neither', 'Hang', 'The client waits.'],
                ['next(err)', 'Error path', 'Four-argument handler.'],
                ['One call', 'Once', 'Not twice.'],
            ],
            'e1' => ['Example 1 — the logger runs, then the route', 'The preview prints logger and then the note.', <<<'JS'
const express = require('express');
const app = express();
app.use((req, res, next) => {
  console.log('logger');
  next();
});
app.get('/notes', (req, res) => {
  res.json({ item: 'milk' });
});
app.listen(3000);
// trace: GET /notes
JS],
            'e2' => ['Example 2 — a named step can sit in front of health too', 'The same middleware runs for a different path.', <<<'JS'
const express = require('express');
const app = express();
app.use((req, res, next) => {
  console.log('logger');
  next();
});
app.get('/health', (req, res) => {
  res.json({ ok: true });
});
app.listen(3000);
// trace: GET /health
JS],
            'micro' => [
                'next is not a keyword. It is the third argument. Name it next so readers recognise it.',
                'Do not send a response and also call next(). Pick one.',
                'Async middleware must still call next, or return a promise Express 5 will forward. The next lesson covers the failure.',
            ],
            'quiz' => [
                $q('What does next() do?', 'It hands the request to the next function', 'It sends 200', 'It closes the port', 'It parses JSON'),
                $q('What happens if middleware neither answers nor calls next?', 'The request hangs', 'Express sends 404', 'The process exits', 'The route runs anyway'),
                $q('Does a logger registered with app.use see only one path?', 'No, it sees every request that reaches it', 'Yes', 'Only POST', 'Only the first request'),
            ],
            'practice' => 'Write a middleware that prints seen and calls next, then a GET /notes that sends the milk note. Say which one sends the body.',
        ]),
        $L([
            'section' => 'Middleware',
            'title' => 'The order you register is the order that runs',
            'keywords' => 'middleware order, express app.use order',
            'lead' => 'Express keeps a list. A request walks the list from top to bottom. The JSON parser must be above the route that reads req.body. A logger above the parser sees the request before the body exists. A logger below a route that already answered never runs.',
            'heading' => 'Top to bottom, once',
            'para' => 'Write the list in the order a request should experience it: security headers, logger, body parser, routes, then the 404, then the error handler. Moving one line changes behaviour. There is no priority number. Source order is the priority.',
            'bullets' => [
                'Earlier app.use runs earlier.',
                'A route is in the same list. Place it after the parsers.',
                'A middleware under a route runs only if the route called next.',
                'Read the file from top to bottom when a request surprises you.',
            ],
            'visual' => tf_flow('exorder', 'The list is the program', [['logger', 'first line'], ['json parser', 'then the body'], ['route', 'then the answer']], 'Swap two lines and the request sees a different world.'),
            'rows' => [
                ['Source order', 'Run order', 'No separate priority.'],
                ['Parser above route', 'Body is ready', 'The usual arrangement.'],
                ['Route above parser', 'Body missing', 'A common bug.'],
                ['Logger above route', 'Sees the request', 'Even if the route answers.'],
                ['Logger below a finished route', 'Skipped', 'The route did not call next.'],
                ['404 last among normal handlers', 'Only if nothing else answered', 'Before the error handler.'],
            ],
            'e1' => ['Example 1 — logger then parser then route', 'The log line appears, then the created note.', <<<'JS'
const express = require('express');
const app = express();
app.use((req, res, next) => {
  console.log('logger');
  next();
});
app.use(express.json());
app.post('/notes', (req, res) => {
  res.status(201).json({ item: req.body.title });
});
app.listen(3000);
// trace: POST /notes {"title":"tea"}
JS],
            'e2' => ['Example 2 — two log steps keep their order', 'first prints before second.', <<<'JS'
const express = require('express');
const app = express();
app.use((req, res, next) => {
  console.log('first');
  next();
});
app.use((req, res, next) => {
  console.log('second');
  next();
});
app.get('/notes', (req, res) => {
  res.json({ item: 'milk' });
});
app.listen(3000);
// trace: GET /notes
JS],
            'micro' => [
                'Routers have their own lists. The mount point decides when that list starts.',
                'A route that forgets next hides everything below it.',
                'Do not register the error handler in the middle. It belongs at the end.',
            ],
            'quiz' => [
                $q('What decides middleware order?', 'The order of registration in the file', 'The method name alphabetically', 'The port', 'The JSON keys'),
                $q('Where does the JSON parser belong relative to a route that reads req.body?', 'Above that route', 'Below it', 'Inside listen', 'After the error handler'),
                $q('When does a middleware below an answering route run?', 'Only if that route called next', 'Always', 'Never', 'Only for POST'),
            ],
            'practice' => 'Put these in order for a JSON create: the route, the JSON parser, the logger. Say why.',
        ]),
        $L([
            'section' => 'Middleware',
            'title' => 'A middleware can end the request and skip the route',
            'keywords' => 'express guard, middleware stop, 400',
            'lead' => 'A guard is middleware that sometimes answers early. If the title is missing, it sends 400 and does not call next. The route never runs, so it cannot store a bad note. If the title is present, the guard calls next and the route stores it.',
            'heading' => 'Stop or continue, not both',
            'para' => 'This playground shows each branch as its own small app, so you can see the status each branch sends. In one real function you write an if. The missing-title branch sends 400 and returns. The other branch calls next. Returning after you send matters, because a later next() would continue after you already answered.',
            'bullets' => [
                'An early answer is how you reject a request before the route.',
                'Do not call next after you have sent.',
                'Put the guard on the route or just above it, after the parser, so req.body exists.',
                'A guard that always calls next is only a logger. A guard that never calls next disables the route.',
            ],
            'visual' => tf_split('exguard', 'The guard chooses', 'Is the title present?', 'yes', 'the route stores it', 'no', '400 and the route stops', 'One branch answers. The other hands off.'),
            'rows' => [
                ['Missing title', '400', 'Do not call next.'],
                ['Present title', 'next()', 'The route answers 201.'],
                ['After send', 'return', 'No second answer.'],
                ['Parser first', 'Body exists', 'Otherwise the guard cannot see the title.'],
                ['Shared guard', 'Many routes', 'app.use or a router.'],
                ['Wrong status', '500 for a bad title', 'That blames the server.'],
            ],
            'e1' => ['Example 1 — the reject branch', 'This is the path taken when the title is missing. The route is not the function that answers.', <<<'JS'
const express = require('express');
const app = express();
app.use(express.json());
app.use((req, res, next) => {
  res.status(400).json({ error: 'title is required' });
});
app.post('/notes', (req, res) => {
  res.status(201).json({ item: 'tea' });
});
app.listen(3000);
// trace: POST /notes {}
JS],
            'e2' => ['Example 2 — the continue branch', 'This is the path taken when the title exists. The guard calls next and the route answers.', <<<'JS'
const express = require('express');
const app = express();
app.use(express.json());
app.use((req, res, next) => {
  console.log('title ok');
  next();
});
app.post('/notes', (req, res) => {
  res.status(201).json({ item: req.body.title });
});
app.listen(3000);
// trace: POST /notes {"title":"tea"}
JS],
            'micro' => [
                'In production those two apps are one function with an if. The statuses stay the same.',
                'Check type as well as presence. A title that is a number is still bad.',
                'Do the cheap checks here. Do not call the database in the guard unless the check needs it.',
            ],
            'quiz' => [
                $q('What should a guard do when the title is missing?', 'Send 400 and not call next', 'Call next and hope', 'Send 500', 'Close the port'),
                $q('What should it do when the title is present?', 'Call next', 'Send 400 anyway', 'Skip the route by hanging', 'Listen again'),
                $q('Why return after sending 400?', 'So a later line cannot call next or send again', 'To set the port', 'To parse JSON', 'To change the method'),
            ],
            'practice' => 'Describe the if inside one guard: the condition, the status on failure, and the call on success.',
        ]),
        $L([
            'section' => 'Middleware',
            'title' => 'A router is a small app mounted on a prefix',
            'keywords' => 'express.Router, app.use prefix, mount',
            'lead' => 'express.Router() builds a list of routes you can mount with app.use. Mounting at /api means a router route of /notes is public at /api/notes. Inside the router you write the short path. The prefix is added by the mount. This is how a notes area stays separate from a health route on the main app.',
            'heading' => 'The prefix is added once',
            'para' => 'The router does not listen. The app listens. Requests whose path starts with the prefix enter the router, and the router matches what remains. A router can have its own middleware, so a notes router can require a parser or a guard without wrapping the whole app. You can mount the same router only if you mean for those routes to exist twice.',
            'bullets' => [
                'const notes = express.Router() creates the mini app.',
                'notes.get(\'/:id\') plus app.use(\'/api\', notes) serves /api/:id. For notes, mount at /notes or write the path /notes inside a router mounted at /api.',
                'The router sees the path after the prefix.',
                'Health can stay on the app. Notes can live on the router.',
            ],
            'visual' => tf_flow('exrouter', 'The prefix, then the router path', [['/api', 'the mount'], ['/:id', 'inside the router'], ['/api/9', 'the public path']], 'You write the short path on the router. Clients call the joined path.'),
            'rows' => [
                ['express.Router()', 'A mini app', 'No listen.'],
                ['app.use prefix', 'The mount', 'Joins the paths.'],
                ['Inside path', 'After the prefix', '/:id becomes /api/:id.'],
                ['Router middleware', 'Only that area', 'Not the whole process.'],
                ['Main app routes', 'Still work', 'Health can stay there.'],
                ['Two mounts', 'Two public paths', 'Only if you mean it.'],
            ],
            'e1' => ['Example 1 — /api plus /:id', 'The client calls /api/9. The router route is /:id.', <<<'JS'
const express = require('express');
const app = express();
const notes = express.Router();
notes.get('/:id', (req, res) => {
  res.json({ id: req.params.id });
});
app.use('/api', notes);
app.listen(3000);
// trace: GET /api/9
JS],
            'e2' => ['Example 2 — a path that misses the prefix does not enter', 'GET /9 is not /api/9. There is no route, so the answer is 404.', <<<'JS'
const express = require('express');
const app = express();
const notes = express.Router();
notes.get('/:id', (req, res) => {
  res.json({ id: req.params.id });
});
app.use('/api', notes);
app.listen(3000);
// trace: GET /9
JS],
            'micro' => [
                'A trailing slash on the mount and on the route can fail to join the way you expect. Keep one style.',
                'Router params and app params are both on req.params when they matched.',
                'Export the router from a file and mount it from the main file. The main file should stay short.',
            ],
            'quiz' => [
                $q('What is the public path of notes.get(\'/:id\') mounted at /api?', '/api/:id', '/:id', '/notes/:id', '/api'),
                $q('Does a router call listen?', 'No. The app does', 'Yes', 'Only for POST', 'Only in production'),
                $q('What is GET /9 when the only route is mounted at /api?', '404', '200', '201', 'The id 9'),
            ],
            'practice' => 'Mount a notes router at /notes with GET / so the public path is /notes, and say which file should call listen.',
        ]),
        $L([
            'section' => 'Middleware',
            'title' => 'Static files are a middleware in front of the routes',
            'keywords' => 'express.static, public folder',
            'lead' => 'express.static(folder) looks for a file that matches the path and, if it finds one, sends that file and stops. If it does not find one, it calls next so your routes can still answer. Put it where you mean files to win, usually before the API routes only if the folders do not clash.',
            'heading' => 'A file can claim the path',
            'para' => 'A public folder is for assets the browser may see: a small page, a picture, a style sheet. It is not for your source, your environment file, or your notes database. The URL /health.txt maps to a file named health.txt inside the folder. If that file exists, a later app.get(\'/health.txt\') will not run. Name API paths so they do not collide with files.',
            'bullets' => [
                'static sends the file and does not call next when the file exists.',
                'A missing file falls through. Your 404 can still run.',
                'Do not point static at the project root. You would publish the wrong files.',
                'API routes under /notes stay safe if no file occupies that path.',
            ],
            'visual' => tf_split('exstatic', 'Does a file own this path?', 'File in the folder?', 'yes', 'send the file', 'no', 'routes may answer', 'Static is middleware. It is not a separate server.'),
            'rows' => [
                ['express.static', 'A middleware', 'A folder on disk.'],
                ['File found', 'Response ends', 'The route does not run.'],
                ['File missing', 'next()', 'Routes continue.'],
                ['Project root', 'Too wide', 'Do not publish it.'],
                ['Name clash', 'File wins if it is first', 'Choose paths carefully.'],
                ['API JSON', 'A route', 'Not a static file.'],
            ],
            'e1' => ['Example 1 — an API route still answers when no file claimed it', 'This preview is the route. Static would have called next if the file was absent.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes', (req, res) => {
  res.json({ item: 'milk' });
});
app.listen(3000);
// trace: GET /notes
JS],
            'e2' => ['Example 2 — a path you reserve for files should not be your only API', 'Keep /notes for the API. Keep pictures under a folder path you chose.', <<<'JS'
const express = require('express');
const app = express();
app.get('/health', (req, res) => {
  res.json({ ok: true });
});
app.listen(3000);
// trace: GET /health
JS],
            'micro' => [
                'index.html inside the folder is served for / if you allow indexes. Know whether you want that.',
                'Dotfiles are hidden by default. Do not rely on that as your only protection.',
                'A cache header on static files is a later optimisation. Correctness of the path comes first.',
            ],
            'quiz' => [
                $q('What does static do when the file exists?', 'It sends the file and does not call next', 'It always calls next', 'It parses JSON', 'It listens on a second port'),
                $q('What does it do when the file is missing?', 'It calls next', 'It crashes the process', 'It sends 500', 'It deletes the folder'),
                $q('Why avoid pointing static at the project root?', 'It would publish files that are not public assets', 'Ports must be under 100', 'JSON would stop working', 'Routers cannot mount'),
            ],
            'practice' => 'Choose a folder name for public assets and an API prefix that will not collide with those files.',
        ]),
        $L([
            'section' => 'Middleware',
            'title' => 'The error handler is the middleware with four parameters',
            'keywords' => 'express error handler, next err, four arguments',
            'lead' => 'Express treats a middleware with four parameters, err, req, res, next, as an error handler. next(err) and a thrown error in synchronous route code jump to it. It should log the failure and send 500 with a short message. It should not send the stack to the client in production.',
            'heading' => 'Ordinary routes do not receive the error',
            'para' => 'The error handler belongs after the routes. If it is above them, it never sees their failures. You still declare next even if you do not call it, because the fourth parameter is how Express recognises the handler. A handler that forgets to send leaves the client hanging on the error path too.',
            'bullets' => [
                'Four parameters: err, req, res, next. The count matters.',
                'Send 500 for an unexpected failure. Keep the message generic in production.',
                'Log the real error on the server.',
                'Register this handler last.',
            ],
            'visual' => tf_flow('exerr', 'A failure leaves the normal list', [['route', 'throws or next(err)'], ['skip', 'ordinary middleware'], ['handler', '500']], 'The error handler is last. It is the only one that receives the error object.'),
            'rows' => [
                ['Four parameters', 'The signal', 'Not the names alone.'],
                ['next(err)', 'Jump', 'Skips normal middleware.'],
                ['Position', 'After routes', 'Last.'],
                ['500', 'Unexpected', 'Not for a bad title.'],
                ['Client message', 'Short', 'No stack in production.'],
                ['Server log', 'The real error', 'You need it to fix the fault.'],
            ],
            'e1' => ['Example 1 — the answer a failure should send', 'A four-parameter handler sends this body after next(err). The preview shows that body. A path with no route is 404, and it does not enter this handler.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes', (req, res) => {
  res.status(500).json({ error: 'failed' });
});
app.listen(3000);
// trace: GET /notes
JS],
            'e2' => ['Example 2 — a healthy route is not an error', 'When nothing fails, the route answers 200 and the error handler stays quiet.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes', (req, res) => {
  res.json({ item: 'milk' });
});
app.use((err, req, res, next) => {
  res.status(500).json({ error: 'failed' });
});
app.listen(3000);
// trace: GET /notes
JS],
            'micro' => [
                'A 400 for bad input should be sent by the guard, not by this handler.',
                'If headers were already sent, you cannot start a new 500 body. Stop writing.',
                'Express 5 forwards a rejected promise from a route into this handler. Express 4 does not, unless you wrap the route or call next yourself.',
            ],
            'quiz' => [
                $q('How does Express recognise an error handler?', 'It has four parameters', 'Its name is error', 'It uses app.get', 'It listens on port 500'),
                $q('Where is it registered?', 'After the routes', 'Before the JSON parser', 'Inside every route', 'Before listen only'),
                $q('What status fits an unexpected failure?', '500', '400', '201', '404'),
            ],
            'practice' => 'Write the four parameter names in order and the status you send to the client. Say what you log on the server instead of sending it.',
        ]),
        $L([
            'section' => 'Middleware',
            'title' => 'A rejected promise must reach that error handler',
            'keywords' => 'express async, promise rejection, express 5',
            'lead' => 'An async route that throws rejects a promise. Express 4 does not catch that rejection for you. The error handler never runs unless you wrap the function or call next in a catch. Express 5 does forward rejected promises from routes and middleware. Know which major version you run.',
            'heading' => 'await does not replace next',
            'para' => 'The bug looks like a hang or an unhandled rejection, not like a tidy 500. Wrap once: a small function calls your async route and sends any rejection to next. Then every async route uses the wrapper. On Express 5 the wrapper is less necessary for routes, and it is still a clear place to put a policy. Do not mix a manual catch that swallows the error.',
            'bullets' => [
                'Express 4: a rejected async route does not reach the error handler by itself.',
                'Express 5: rejected promises from routes and middleware are forwarded.',
                'A wrapper is (fn) => (req, res, next) => Promise.resolve(fn(req, res, next)).catch(next).',
                'Swallowing the error in an empty catch hides the failure from both the log and the client.',
            ],
            'visual' => tf_split('exasync', 'Did the rejection reach next?', 'Reach the handler?', 'yes', '500 and a log', 'no', 'client keeps waiting', 'Version 4 needs a wrapper or a catch. Version 5 forwards route rejections.'),
            'rows' => [
                ['async route', 'Returns a promise', 'throw becomes a rejection.'],
                ['Express 4', 'Does not forward it', 'Use a wrapper.'],
                ['Express 5', 'Forwards it', 'The handler can answer.'],
                ['catch(next)', 'The handoff', 'Do not ignore the value.'],
                ['Empty catch', 'Swallows it', 'Nobody hears.'],
                ['400 versus 500', 'Still different', 'A missing row is not a rejection.'],
            ],
            'e1' => ['Example 1 — the failure the handler should see', 'The client receives 500. The detail stays on the server.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes', (req, res) => {
  res.status(500).json({ error: 'failed' });
});
app.listen(3000);
// trace: GET /notes
JS],
            'e2' => ['Example 2 — a resolved async route still sends its JSON', 'Success is not an error. The status stays 200.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes', (req, res) => {
  res.json({ item: 'milk' });
});
app.listen(3000);
// trace: GET /notes
JS],
            'micro' => [
                'Check package.json for the express major version before you skip the wrapper.',
                'A database timeout is a 500 or a 503 you choose on purpose, not an unhandled rejection.',
                'next(err) inside a .catch is the Express 4 habit. Keep it until you have moved to 5 and tested a rejection.',
            ],
            'quiz' => [
                $q('What does Express 4 do with a rejected async route by itself?', 'It does not forward it to the error handler', 'It always sends 400', 'It retries the route', 'It parses the body again'),
                $q('What does a wrapper catch do with the rejection?', 'It passes the error to next', 'It sends 201', 'It closes the port', 'It deletes the route'),
                $q('Which version forwards rejected route promises?', 'Express 5', 'Express 4', 'Neither', 'Only on port 443'),
            ],
            'practice' => 'Say which Express major version you are on, and whether your async routes need a wrapper.',
        ]),
        $L([
            'section' => 'The API',
            'title' => 'REST uses the method as the verb',
            'keywords' => 'rest, http methods, notes api',
            'lead' => 'A small notes API uses the path for the noun and the method for the verb. GET /notes lists. POST /notes creates. GET /notes/4 reads one. PATCH /notes/4 changes fields. DELETE /notes/4 removes. You do not need POST /notes/4/delete. The method already says delete.',
            'heading' => 'Nouns in the path, verbs in the method',
            'para' => 'PUT replaces the whole note. PATCH changes some fields. If you only support one of them, document that one. A list can use the query for search. A single note uses the path id. The same rules you already learned, applied as a set, are the API.',
            'bullets' => [
                'One noun: notes. The id picks one note.',
                'POST creates on the collection, not on /notes/new.',
                'DELETE should be safe to repeat: deleting a missing note can be 204 or 404, and you pick one and keep it.',
                'Actions that are not a simple change, such as share, can be POST /notes/4/share. That is rarer than the five methods.',
            ],
            'visual' => tf_flow('exrest', 'One noun, five verbs', [['GET /notes', 'list'], ['POST /notes', 'create'], ['GET /notes/4', 'read one'], ['DELETE /notes/4', 'remove']], 'The path names the note. The method names the work.'),
            'rows' => [
                ['GET collection', 'List', 'Query filters.'],
                ['POST collection', 'Create', '201 and the new id.'],
                ['GET one', 'Read', '404 when it is gone.'],
                ['PATCH one', 'Change fields', 'Not a full replace.'],
                ['PUT one', 'Replace', 'The whole note.'],
                ['DELETE one', 'Remove', 'Pick 204 or 404 and stay there.'],
            ],
            'e1' => ['Example 1 — create on the collection', 'POST /notes, status 201, the title you sent.', <<<'JS'
const express = require('express');
const app = express();
app.use(express.json());
app.post('/notes', (req, res) => {
  res.status(201).json({ id: '4', item: req.body.title });
});
app.listen(3000);
// trace: POST /notes {"title":"tea"}
JS],
            'e2' => ['Example 2 — read one by id', 'GET /notes/4 returns that note.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes/:id', (req, res) => {
  res.json({ id: req.params.id, item: 'milk' });
});
app.listen(3000);
// trace: GET /notes/4
JS],
            'micro' => [
                'GET and DELETE should not require a JSON body.',
                'A filter is a query on the collection, not a new path for every word.',
                'Name fields in one style. item or title, not both for the same idea.',
            ],
            'quiz' => [
                $q('Which pair creates a note?', 'POST /notes', 'GET /notes/4', 'DELETE /notes', 'GET /notes'),
                $q('Where does the id of one note belong?', 'In the path', 'Only in the port', 'Only in a header named verb', 'In the method'),
                $q('What does PATCH mean if you support it?', 'Change some fields', 'Create a collection', 'Replace the server', 'List every note'),
            ],
            'practice' => 'Write the five paths and methods for list, create, read, patch, and delete. Mark which ones read a JSON body.',
        ]),
        $L([
            'section' => 'The API',
            'title' => 'Missing and invalid are different status codes',
            'keywords' => '404 vs 400, express status',
            'lead' => '400 means the request is badly formed: no title, a title that is not text, a page size that is not a number. 404 means the request was well formed and the note is not there. Clients repair a 400 by changing what they sent. They do not repair a 404 that way. The note is gone or the id is unknown.',
            'heading' => 'Do not collapse them into 200',
            'para' => 'A search that finds nothing is an empty list and 200, because the collection exists. A read of id 4 that finds nothing is 404, because that note does not exist. An empty title on create is 400, because the client can add a title and try again. Using 404 for a bad title teaches the client the wrong repair.',
            'bullets' => [
                '400: fix the request.',
                '404: that resource is not here.',
                '200 with an empty list: the collection answered.',
                '500: you failed while doing a valid request.',
            ],
            'visual' => tf_flow('exmiss', 'Four different outcomes', [['200', 'empty list'], ['201', 'created'], ['400', 'bad title'], ['404', 'no such id']], 'Same app, four answers. The number tells the client what to do next.'),
            'rows' => [
                ['Bad title', '400', 'The client can edit it.'],
                ['Unknown id', '404', 'Nothing to edit.'],
                ['Empty search', '200 and an empty list', 'The collection exists.'],
                ['Create worked', '201', 'A new id.'],
                ['Server fault', '500', 'Not the client\'s repair.'],
                ['200 with an error field', 'Avoid', 'The number should carry the outcome.'],
            ],
            'e1' => ['Example 1 — bad input', 'The title was not acceptable. Status 400.', <<<'JS'
const express = require('express');
const app = express();
app.post('/notes', (req, res) => {
  res.status(400).json({ error: 'title is required' });
});
app.listen(3000);
// trace: POST /notes
JS],
            'e2' => ['Example 2 — unknown id', 'The path was well formed. The note is not there.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes/:id', (req, res) => {
  res.status(404).json({ error: 'not found' });
});
app.listen(3000);
// trace: GET /notes/4
JS],
            'micro' => [
                '409 means a conflict, such as a duplicate title if you made titles unique. It is not a 400.',
                '422 is sometimes used for validation. If you pick 400, use 400 everywhere for this API.',
                'Include a short error field. Do not include a stack.',
            ],
            'quiz' => [
                $q('Which status is a missing title on create?', '400', '404', '201', '204'),
                $q('Which status is a well formed id that matches nothing?', '404', '400', '200', '500'),
                $q('What should a search that matches nothing return?', '200 and an empty list', '404', '500', '201'),
            ],
            'practice' => 'Take three requests: create without a title, GET /notes/4 when it is gone, and GET /notes?q=zzz when nothing matches. Assign a status to each.',
        ]),
    ];
}
