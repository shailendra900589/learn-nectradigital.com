<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/track_seed.php';
require_once __DIR__ . '/includes/teach_figures.php';

function express_meta(): array
{
    return [
        'title' => 'Express.js Tutorial',
        'slug' => 'express-tutorial',
        'description' => 'Express from the first request through routes, middleware, routers, validation, CORS, configuration, logging, and shutdown, ending in a notes API you can explain.',
        'keywords' => 'express tutorial, express middleware, express router, express json, rest api node, cors express, express error handler, node http',
    ];
}

function express_chapters(): array
{
    require_once __DIR__ . '/seed_express_b.php';
    require_once __DIR__ . '/seed_express_c.php';
    return array_merge(express_chapters_a(), express_chapters_b(), express_chapters_c());
}

function express_chapters_a(): array
{
    $L = static function (array $c): array {
        return track_lesson('nodejs', 'notes.js', $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };

    return [
        $L([
            'section' => 'The request',
            'title' => 'A request is a method, a path, and a body',
            'keywords' => 'http request, method, path, body, express',
            'lead' => 'A browser or another program asks your server a question. The question has a method, such as GET or POST, a path such as /notes, and sometimes a body. Express does not invent that shape. It reads the HTTP request Node already received and hands it to your functions.',
            'heading' => 'Three parts, then your code',
            'para' => 'GET /notes asks for the list and usually has no body. POST /notes sends a new note in the body. The path does not include the host or the port. Those identify the machine. The path identifies the resource on that machine. If you mix the method up, you answer the wrong question even when the path is right.',
            'bullets' => [
                'The method is the verb. GET reads. POST creates. PUT and PATCH change. DELETE removes.',
                'The path starts with a slash. /notes and /notes/ are different strings until you decide to treat them as the same.',
                'The body is optional. A GET that depends on a body will surprise clients, because many clients do not send one.',
                'Express sees this request only after Node has accepted the connection. Your route is not the socket.',
            ],
            'visual' => tf_flow('exreq', 'One question, three parts', [['method', 'GET'], ['path', '/notes'], ['body', 'maybe']], 'The host and the port find the process. The method and the path find the work.'),
            'rows' => [
                ['Method', 'The verb', 'GET is not POST.'],
                ['Path', 'The resource', 'Starts with /.'],
                ['Body', 'The payload', 'Often absent on GET.'],
                ['Host', 'Which machine', 'Not part of the route.'],
                ['Port', 'Which process', 'listen chooses it.'],
                ['Client', 'Who asked', 'Browser, app, or curl.'],
            ],
            'e1' => ['Example 1 — a read has no body', 'GET /notes returns the list. The trace is the request. The JSON is the answer.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes', (req, res) => {
  res.status(200).json({ item: 'milk' });
});
app.listen(3000);
// trace: GET /notes
JS],
            'e2' => ['Example 2 — a create carries a body', 'POST /notes sends the title. The status 201 means the note was created.', <<<'JS'
const express = require('express');
const app = express();
app.use(express.json());
app.post('/notes', (req, res) => {
  res.status(201).json({ item: req.body.title });
});
app.listen(3000);
// trace: POST /notes {"title":"tea"}
JS],
            'micro' => [
                'A path without a leading slash is not what clients send. Write /notes.',
                '201 is the status for a successful create. 200 is fine for a read.',
                'The trace line at the bottom is how this playground fires one request. On your machine the client does that.',
            ],
            'quiz' => [
                $q('Which part of the request says read versus create?', 'The method', 'The port', 'The host', 'The file name'),
                $q('Where does the new note travel on POST /notes?', 'In the body', 'In the port', 'In the host', 'Only in the method'),
                $q('What does a path identify?', 'The resource on that server', 'The computer in the world', 'The programming language', 'The status code'),
            ],
            'practice' => 'Write one sentence that names the method, the path, and whether a body is expected for listing notes and for creating a note.',
        ]),
        $L([
            'section' => 'The request',
            'title' => 'Express is the function that receives that request',
            'keywords' => 'express application, req, res, next',
            'lead' => 'require(\'express\') returns a function. Calling it builds an app. That app is itself a function of three arguments: the request, the response, and next. Routes and middleware are more functions you attach to the app. When a request arrives, Express walks those functions.',
            'heading' => 'One app, many functions',
            'para' => 'req is the incoming question. res is how you answer. next is how you hand the same request to the following function. app.get registers a function that runs only for GET on that path. Until you call listen, nothing is waiting on a port. Creating the app and opening the port are two steps.',
            'bullets' => [
                'const app = express() makes the app. It does not open a port.',
                'req and res are objects for this one request. The next request gets new ones.',
                'You answer on res. You do not return the note from the route function and expect Express to send it.',
                'A route that neither answers nor calls next leaves the client waiting.',
            ],
            'visual' => tf_flow('exapp', 'The app walks your functions', [['app', 'the list'], ['route', 'a match'], ['res', 'the answer']], 'Express does not guess the answer. Your function writes it.'),
            'rows' => [
                ['express()', 'Build the app', 'No port yet.'],
                ['req', 'The question', 'Method, path, body.'],
                ['res', 'The answer', 'Status and body.'],
                ['next', 'Continue', 'The following function.'],
                ['app.get', 'Register a read', 'Method plus path.'],
                ['listen', 'Open the port', 'A separate step.'],
            ],
            'e1' => ['Example 1 — the route writes the answer', 'The function receives req and res. res.json sends the object.', <<<'JS'
const express = require('express');
const app = express();
app.get('/health', (req, res) => {
  res.status(200).json({ ok: true });
});
app.listen(3000);
// trace: GET /health
JS],
            'e2' => ['Example 2 — the path on the request is the one you asked for', 'Two routes stay separate. Only /health matches this trace.', <<<'JS'
const express = require('express');
const app = express();
app.get('/health', (req, res) => {
  res.json({ ok: true });
});
app.get('/notes', (req, res) => {
  res.json({ item: 'milk' });
});
app.listen(3000);
// trace: GET /health
JS],
            'micro' => [
                'Returning a value from the route does not send it. Call a method on res.',
                'The same app object holds every route. You do not create a second express() per route.',
                'req and res exist only for the request that is running now.',
            ],
            'quiz' => [
                $q('What does calling express() do by itself?', 'It builds an app and does not open a port', 'It listens on port 80', 'It sends a response', 'It parses JSON'),
                $q('Which object do you use to answer?', 'res', 'req', 'next', 'require'),
                $q('What happens if a route neither answers nor calls next?', 'The client keeps waiting', 'Express sends 200', 'The process exits', 'The next route runs anyway'),
            ],
            'practice' => 'Say what req, res, and next are for, in one sentence each.',
        ]),
        $L([
            'section' => 'The request',
            'title' => 'listen opens a port and then waits',
            'keywords' => 'app.listen, port, process.env.PORT',
            'lead' => 'listen tells the operating system that this process accepts connections on a port. Port 3000 is a common choice on a laptop. In production the platform chooses the port and you read it from the environment. Two processes cannot listen on the same port.',
            'heading' => 'The port is the door number',
            'para' => 'After listen returns, Node keeps the process alive because the server is still open. Closing the terminal stops it. A port below 1024 usually needs extra permission, so beginners use 3000 or whatever the host assigns. The callback passed to listen runs once the port is open, not once per request.',
            'bullets' => [
                'listen is not a route. It does not match GET or POST.',
                'The callback runs one time, when listening starts.',
                'If the port is taken, listen fails. Change the port or stop the other process.',
                'Read the port from the environment when you deploy, and keep 3000 as the local default.',
            ],
            'visual' => tf_split('export', 'One port, one process', 'Is 3000 free?', 'yes', 'this app listens', 'no', 'listen fails', 'The operating system will not give the same door to two processes.'),
            'rows' => [
                ['listen', 'Open the door', 'Once per process.'],
                ['3000', 'A laptop default', 'Not a rule of HTTP.'],
                ['Busy port', 'Listen fails', 'Another process has it.'],
                ['Callback', 'Runs once', 'Not per request.'],
                ['Environment', 'The deployed port', 'The host decides.'],
                ['Stop the process', 'The door closes', 'New requests stop.'],
            ],
            'e1' => ['Example 1 — port 3000', 'The preview names the port the app opened, then answers /health.', <<<'JS'
const express = require('express');
const app = express();
app.get('/health', (req, res) => {
  res.json({ ok: true });
});
app.listen(3000);
// trace: GET /health
JS],
            'e2' => ['Example 2 — a different port is still the same route', 'The route does not change because the door number changed.', <<<'JS'
const express = require('express');
const app = express();
app.get('/health', (req, res) => {
  res.json({ ok: true });
});
app.listen(4000);
// trace: GET /health
JS],
            'micro' => [
                'Clients must use the same port. http://localhost:3000/health is not http://localhost:4000/health.',
                'listen without a route still opens a port, and every path then falls through with no answer.',
                'Do not hard-code a production port if the host assigns one.',
            ],
            'quiz' => [
                $q('What does listen do?', 'It opens a port and waits for connections', 'It matches a GET route', 'It parses JSON', 'It sends a response'),
                $q('When does the listen callback run?', 'Once, when the port is open', 'On every request', 'Only on errors', 'When the process exits'),
                $q('Why can listen fail?', 'Another process already has that port', 'The path is missing', 'GET is the wrong method', 'JSON is invalid'),
            ],
            'practice' => 'Name the port you would use on your laptop, and say where a deployed app should read its port from.',
        ]),
        $L([
            'section' => 'The request',
            'title' => 'A route is one method plus one path',
            'keywords' => 'app.get, app.post, express routing',
            'lead' => 'app.get, app.post, app.put, app.patch, and app.delete each register one pair. Express tests the method and the path. A GET registered at /notes does not run for POST /notes. A POST registered at /notes does not run for POST /other.',
            'heading' => 'Both halves have to match',
            'para' => 'The first matching route wins if it answers the request. Later routes on the same method and path do not run after a response has started. Put specific paths before wide ones when you add parameters later. A missing match is not a crash. It is a request nobody claimed, which you should turn into 404.',
            'bullets' => [
                'Five common methods: get, post, put, patch, delete. Each is its own list.',
                'The path is matched exactly until you add parameters or a pattern.',
                'Registering the same pair twice means the first one that answers is the one the client sees.',
                'A route is middleware that already checked the method and the path.',
            ],
            'visual' => tf_flow('exroute', 'Method and path together', [['GET /notes', 'list'], ['POST /notes', 'create'], ['GET /health', 'ok'], ['POST /health', 'no']], 'Only the pair you registered runs. A different method on the same path is a different question.'),
            'rows' => [
                ['app.get', 'Read', 'Does not run for POST.'],
                ['app.post', 'Create', 'Does not run for GET.'],
                ['Path', 'Exact for now', '/notes is not /note.'],
                ['First match', 'Answers', 'Later twins stay quiet.'],
                ['No match', 'Unclaimed', 'You still owe a 404.'],
                ['Order', 'Top to bottom', 'Specific paths first later.'],
            ],
            'e1' => ['Example 1 — GET and POST on the same path', 'The GET trace hits the list. The POST route is a different registration.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes', (req, res) => {
  res.json({ item: 'milk' });
});
app.post('/notes', (req, res) => {
  res.status(201).json({ item: 'tea' });
});
app.listen(3000);
// trace: GET /notes
JS],
            'e2' => ['Example 2 — the other method hits the other route', 'Same path, POST, status 201.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes', (req, res) => {
  res.json({ item: 'milk' });
});
app.post('/notes', (req, res) => {
  res.status(201).json({ item: 'tea' });
});
app.listen(3000);
// trace: POST /notes
JS],
            'micro' => [
                'Spelling matters. /notes and /Notes can be different paths, depending on the server.',
                'app.all matches every method. Use it only when you mean every method.',
                'A route with no listen still describes the app. Nothing receives traffic until listen.',
            ],
            'quiz' => [
                $q('Does app.get(\'/notes\') run for POST /notes?', 'No', 'Yes', 'Only if the body is empty', 'Only on port 3000'),
                $q('What is a route?', 'One method paired with one path', 'Only a path', 'Only a port', 'A database table'),
                $q('Which registration runs when two match and the first answers?', 'The first', 'The last', 'Both', 'Neither'),
            ],
            'practice' => 'List the method and path for list, create, and health. Say which app.method you would call for each.',
        ]),
        $L([
            'section' => 'The request',
            'title' => 'json and send finish the response',
            'keywords' => 'res.json, res.send, res.end, content type',
            'lead' => 'res.json turns a value into text and sets the content type to JSON, then ends the response. res.send can send a string or, if you pass an object, it behaves like json. res.end ends the response with nothing more to write. After the response has ended, you cannot write again.',
            'heading' => 'Ending is a choice you make',
            'para' => 'Setting a variable does not tell the client anything. The client is waiting on the connection. json and send write bytes and finish that wait. If you call them twice, Node reports that the headers were already sent. Pick one answer. Status and body belong together in that one answer.',
            'bullets' => [
                'res.json is the usual answer for an API.',
                'res.send with a string sends text, not a JSON object, unless the string happens to look like JSON.',
                'res.end is the empty finish. Use it when the status is the whole answer, such as 204.',
                'Calling json and then send is two answers. The second throws.',
            ],
            'visual' => tf_panels('exsend', 'One answer, then stop', 'res.json', ['object', 'content type JSON', 'body written'], 'res.send string', ['plain text', 'not an object', 'also ends'], 'Both finish the response. A second write is too late.'),
            'rows' => [
                ['res.json', 'Object as JSON', 'Sets the content type.'],
                ['res.send string', 'Text', 'A different content type.'],
                ['res.end', 'No more bytes', 'Body can be empty.'],
                ['Second write', 'Too late', 'Headers already sent.'],
                ['return', 'Does not send', 'Call res yourself.'],
                ['Content type', 'How to read the body', 'The client trusts it.'],
            ],
            'e1' => ['Example 1 — json sends an object', 'The body is an object with one field.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes', (req, res) => {
  res.json({ item: 'milk' });
});
app.listen(3000);
// trace: GET /notes
JS],
            'e2' => ['Example 2 — send writes text', 'The body is the word list, not an object.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes', (req, res) => {
  res.send('list');
});
app.listen(3000);
// trace: GET /notes
JS],
            'micro' => [
                'json of an array is still JSON. An empty object and an empty array are different.',
                'A string you pass to json is a JSON string, including the quotes. send does not add those quotes.',
                'End the function after you send, so later lines cannot send again.',
            ],
            'quiz' => [
                $q('What does res.json do besides writing the body?', 'It sets the JSON content type and ends the response', 'It opens the port', 'It calls next', 'It parses the request'),
                $q('Why is a second send a problem?', 'The response already finished', 'The path changed', 'The method changed', 'The port closed'),
                $q('Does returning an object from the route send it?', 'No', 'Yes', 'Only for GET', 'Only for arrays'),
            ],
            'practice' => 'Show a route that sends the object { ok: true } and a route that sends the plain text ok. Say which method you used for each.',
        ]),
        $L([
            'section' => 'The request',
            'title' => 'The status code is part of the answer',
            'keywords' => 'http status, res.status, 200, 201, 404',
            'lead' => 'The status is a number in front of the body. 200 means the read worked. 201 means a create worked. 204 means success with no body. 400 means the client sent a bad request. 404 means you have no such resource. 500 means the server failed. The body can explain. The number is what programs branch on.',
            'heading' => 'status does not send by itself',
            'para' => 'res.status(201) only records the number. You still call json or send. res.sendStatus(204) sets the number and ends with an empty body. Clients and monitors read the number first. A 200 that hides an error in the JSON forces every client to understand your private shape. Prefer the number that already means the outcome.',
            'bullets' => [
                '2xx is success. 4xx is the client. 5xx is the server.',
                '404 is not the same as 400. Missing is not the same as badly formed.',
                '401 means not authenticated. 403 means authenticated and still refused.',
                '500 is for a fault you did not expect. A validation miss is 400, not 500.',
            ],
            'visual' => tf_flow('exstat', 'The number is the outcome', [['200', 'a read worked'], ['201', 'a create worked'], ['400', 'fix the request'], ['404', 'nothing there']], 'Pick the number first. The JSON explains it. It does not replace it.'),
            'rows' => [
                ['200', 'Read worked', 'A body is normal.'],
                ['201', 'Created', 'Say where it lives if you can.'],
                ['204', 'Done, no body', 'sendStatus fits.'],
                ['400', 'Bad input', 'The client can fix it.'],
                ['404', 'No such resource', 'Not a validation error.'],
                ['500', 'Your fault', 'Do not use it for bad input.'],
            ],
            'e1' => ['Example 1 — a create uses 201', 'The number and the body travel together.', <<<'JS'
const express = require('express');
const app = express();
app.post('/notes', (req, res) => {
  res.status(201).json({ item: 'tea' });
});
app.listen(3000);
// trace: POST /notes
JS],
            'e2' => ['Example 2 — a missing path is 404', 'No route claimed /missing, so the answer is 404.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes', (req, res) => {
  res.json({ item: 'milk' });
});
app.listen(3000);
// trace: GET /missing
JS],
            'micro' => [
                'res.status returns res, so you can chain .json on the same line.',
                'A 204 must not include a body. Some clients reject the pair.',
                'Do not send 200 with { error: true }. Send 400 or 404.',
            ],
            'quiz' => [
                $q('Which status fits a successful create?', '201', '404', '500', '301'),
                $q('Does res.status(201) by itself send the response?', 'No. You still send a body or end it', 'Yes', 'Only for GET', 'Only on port 3000'),
                $q('Which status means the resource is not there?', '404', '400', '201', '204'),
            ],
            'practice' => 'Assign 200, 201, 400, and 404 to list, create, bad title, and unknown id. One number each.',
        ]),
        $L([
            'section' => 'Reading the request',
            'title' => 'A route parameter is a named piece of the path',
            'keywords' => 'req.params, route params, express :id',
            'lead' => 'A colon in the path names a slot. /notes/:id matches /notes/4 and stores the text 4 under req.params.id. The value is always text until you convert it. /notes/4 does not match /notes, and it does not match /notes/4/extra.',
            'heading' => 'The slot is text',
            'para' => 'Use the parameter for identity: which note. Do not use it for an action such as /notes/:cmd with cmd equal to delete. The method already says delete. Two parameters are allowed, /notes/:id/lines/:line, and each name becomes a key on req.params. Names are case-sensitive.',
            'bullets' => [
                ':id is a name you choose. req.params.id is how you read it.',
                'The value is a string. The number 4 and the string 4 are not the same in a strict compare.',
                'A slash is not part of one parameter. It starts the next segment.',
                'Put /notes/new before /notes/:id if new would otherwise be captured as an id.',
            ],
            'visual' => tf_flow('exparam', 'The path fills the slot', [['/notes/:id', 'the pattern'], ['/notes/4', 'the request'], ['params.id', 'the text 4']], 'One segment, one name. The letters 4 stay text until you convert them.'),
            'rows' => [
                [':id', 'A named segment', 'Not a query string.'],
                ['req.params', 'The filled names', 'One request only.'],
                ['String', 'Always, at first', 'Convert if you need a number.'],
                ['Extra segment', 'No match', '/notes/4/extra is different.'],
                ['Specific first', '/notes/new', 'Before the slot.'],
                ['Method still matters', 'GET versus DELETE', 'Same slot, different verb.'],
            ],
            'e1' => ['Example 1 — the id comes from the path', 'The trace asks for 4. The JSON echoes that id.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes/:id', (req, res) => {
  res.json({ id: req.params.id, item: 'milk' });
});
app.listen(3000);
// trace: GET /notes/4
JS],
            'e2' => ['Example 2 — a different id is a different request', 'The route is the same. The slot changes.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes/:id', (req, res) => {
  res.json({ id: req.params.id, item: 'milk' });
});
app.listen(3000);
// trace: GET /notes/9
JS],
            'micro' => [
                'Decode is already done for normal characters. A slash still splits segments.',
                'Do not build a file path from the raw parameter. The value came from the client.',
                'An empty segment does not fill the slot. /notes/ is not /notes/:id.',
            ],
            'quiz' => [
                $q('What is req.params.id for GET /notes/4 when the route is /notes/:id?', 'The string 4', 'The number 4', 'The whole path', 'The query'),
                $q('Does /notes/:id match /notes?', 'No', 'Yes', 'Only for POST', 'Only if id is optional'),
                $q('Why register /notes/new before /notes/:id?', 'Otherwise new is captured as an id', 'Methods run backwards', 'new is a query key', 'Ports must be sorted'),
            ],
            'practice' => 'Write the route for GET /notes/4 and name the property that holds 4. Say what type it is before you convert it.',
        ]),
        $L([
            'section' => 'Reading the request',
            'title' => 'A query string is not a path segment',
            'keywords' => 'req.query, query string, express',
            'lead' => 'The query string sits after a question mark. /notes?q=milk has the path /notes and the query key q. It does not match a route parameter. Express parses the query into req.query. Values arrive as strings. A missing key is not there. An empty value is an empty string.',
            'heading' => 'Filters live in the query',
            'para' => 'Use the query for filters, search, page, and limit. Use the path for identity. /notes?id=4 and /notes/4 can both be made to work, and clients will confuse them if you support both. Pick the path for a single note and the query for a search across notes. Repeated keys can arrive as an array. Read one key the way you documented it.',
            'bullets' => [
                'req.query.q is the search text. It is not req.params.q.',
                'The route path does not include the question mark or the query.',
                'Numbers in the query are still strings until you convert them.',
                'A huge query is still input. Cap the page size instead of trusting limit=100000.',
            ],
            'visual' => tf_panels('exquery', 'Path and query do different jobs', '/notes/4', ['identity', 'req.params.id', 'one note'], '/notes?q=milk', ['filter', 'req.query.q', 'a search'], 'Same list of notes. The question mark starts a different bag of values.'),
            'rows' => [
                ['?', 'Starts the query', 'Not part of the path.'],
                ['req.query', 'The parsed keys', 'Strings.'],
                ['Missing key', 'Not present', 'Do not assume empty string.'],
                ['Empty value', 'Present and blank', 'q= is different from no q.'],
                ['page and limit', 'A filter', 'Cap them.'],
                ['Identity', 'Belongs in the path', 'Not in the query.'],
            ],
            'e1' => ['Example 1 — q is the search', 'The path is /notes. The query supplies milk.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes', (req, res) => {
  res.json({ q: req.query.q });
});
app.listen(3000);
// trace: GET /notes?q=milk
JS],
            'e2' => ['Example 2 — a different search does not change the route', 'Same route. The query value changes.', <<<'JS'
const express = require('express');
const app = express();
app.get('/notes', (req, res) => {
  res.json({ q: req.query.q });
});
app.listen(3000);
// trace: GET /notes?q=tea
JS],
            'micro' => [
                'Spaces and symbols in a query must be encoded by the client. Read the decoded value.',
                'Do not put secrets in the query. It is logged by intermediaries more often than a body.',
                'An unknown query key can be ignored. A missing required filter should be a 400 if you documented it as required.',
            ],
            'quiz' => [
                $q('Where does q live for /notes?q=milk?', 'req.query.q', 'req.params.q', 'req.body.q', 'The port'),
                $q('Is the query part of the route path?', 'No', 'Yes', 'Only for POST', 'Only when it is a number'),
                $q('What type is a query value at first?', 'A string', 'A number', 'A boolean', 'An object'),
            ],
            'practice' => 'Write the URL that searches notes for tea, and name the property you read in the route.',
        ]),
        $L([
            'section' => 'Reading the request',
            'title' => 'A JSON body stays empty until you parse it',
            'keywords' => 'express.json, req.body, content type',
            'lead' => 'The raw body is bytes. express.json() is middleware that parses those bytes when the content type is JSON and then sets req.body. Without it, req.body is undefined and your route cannot see the title. The parser belongs before the routes that read the body.',
            'heading' => 'Parsing is a step you register',
            'para' => 'express.json() does not parse form posts. It does not run for a missing content type. A body that is not valid JSON fails before your route, which is what you want. Set a size limit so a huge body cannot exhaust memory. The parsed value is the object the client sent, and you still have to check the fields.',
            'bullets' => [
                'app.use(express.json()) once, near the top.',
                'req.body.title is the field after a successful parse.',
                'A GET rarely has a JSON body. Do not require one.',
                'The limit option rejects oversized bodies. Pick a limit that fits a note, not a video, unless you mean to accept a video.',
            ],
            'visual' => tf_flow('exjson', 'Bytes become an object', [['bytes', 'the body'], ['json()', 'the parser'], ['req.body', 'the object']], 'The route reads the object. It does not parse the bytes itself.'),
            'rows' => [
                ['express.json()', 'The parser', 'Middleware.'],
                ['Content type', 'Must be JSON', 'Otherwise it skips.'],
                ['req.body', 'The object', 'After the parser.'],
                ['Invalid JSON', 'Fails early', 'Your route does not run.'],
                ['Order', 'Parser first', 'Then the route.'],
                ['Limit', 'A ceiling', 'Reject the huge body.'],
            ],
            'e1' => ['Example 1 — the title arrives on req.body', 'The trace carries the JSON object. The route reads title.', <<<'JS'
const express = require('express');
const app = express();
app.use(express.json());
app.post('/notes', (req, res) => {
  res.status(201).json({ item: req.body.title });
});
app.listen(3000);
// trace: POST /notes {"title":"tea"}
JS],
            'e2' => ['Example 2 — a second field is just another key', 'The body can hold more than a title. Read the key you documented.', <<<'JS'
const express = require('express');
const app = express();
app.use(express.json());
app.post('/notes', (req, res) => {
  res.status(201).json({ item: req.body.title });
});
app.listen(3000);
// trace: POST /notes {"title":"milk"}
JS],
            'micro' => [
                'An empty body with a JSON content type is invalid. Say so with 400.',
                'Arrays are valid JSON. If you expect an object, reject an array.',
                'Do not parse the body again in the route. The middleware already did it.',
            ],
            'quiz' => [
                $q('What fills req.body for a JSON post?', 'express.json()', 'app.listen', 'The route path', 'res.json'),
                $q('Where should the parser be registered?', 'Before the routes that read the body', 'After every route', 'Inside listen', 'Only on GET'),
                $q('What is req.body.title before the parser runs?', 'Not the object you hoped for', 'Always a string', 'Always 0', 'The path'),
            ],
            'practice' => 'Add the JSON parser and a POST /notes route that returns the title from the body with status 201.',
        ]),
        $L([
            'section' => 'Reading the request',
            'title' => 'A form body is not JSON',
            'keywords' => 'express.urlencoded, form body, content type',
            'lead' => 'A classic HTML form sends application/x-www-form-urlencoded, which looks like title=tea&done=1. express.json() will not parse that. express.urlencoded() will. extended false uses the simple parser. extended true uses a richer parser that can nest keys. Pick one and document it.',
            'heading' => 'Match the parser to the content type',
            'para' => 'If your API only accepts JSON, do not add the form parser, and reject other content types. If a browser form posts to you, add urlencoded and still validate the fields. The two parsers can both be registered. Each one looks at the content type and skips the other. A multipart upload, the kind that carries a file, is a third format and neither of these parsers reads it.',
            'bullets' => [
                'JSON and urlencoded are different content types.',
                'extended: false is the small parser and is enough for flat fields.',
                'The fields still land on req.body. Validation is still your job.',
                'A file upload is not urlencoded. Do not pretend the JSON parser received the file.',
            ],
            'visual' => tf_panels('exform', 'Two body formats', 'JSON', ['content type json', 'express.json()', 'an object'], 'form', ['title=tea', 'urlencoded()', 'flat fields'], 'The route can read req.body either way. The parser has to match the bytes.'),
            'rows' => [
                ['json parser', 'JSON only', 'Skips forms.'],
                ['urlencoded', 'Form fields', 'Skips JSON.'],
                ['extended false', 'Flat keys', 'The simple choice.'],
                ['req.body', 'After either parser', 'Then validate.'],
                ['Wrong parser', 'Empty body', 'A quiet bug.'],
                ['Files', 'A third format', 'Neither parser reads them.'],
            ],
            'e1' => ['Example 1 — this route expects JSON', 'A JSON title is read after express.json().', <<<'JS'
const express = require('express');
const app = express();
app.use(express.json());
app.post('/notes', (req, res) => {
  res.status(201).json({ item: req.body.title });
});
app.listen(3000);
// trace: POST /notes {"title":"tea"}
JS],
            'e2' => ['Example 2 — a form-shaped value is still a field you validate', 'The lesson shows the field after a parser has done its work. The content type decided which parser ran.', <<<'JS'
const express = require('express');
const app = express();
app.use(express.urlencoded({ extended: false }));
app.post('/notes', (req, res) => {
  res.status(201).json({ item: 'tea' });
});
app.listen(3000);
// trace: POST /notes
JS],
            'micro' => [
                'Register both parsers only if you truly accept both content types.',
                'extended true changes how bracket keys are nested. Do not turn it on without a reason.',
                'Charset surprises are rare for these notes and real for non-English text. Keep UTF-8.',
            ],
            'quiz' => [
                $q('Which parser reads a classic HTML form body?', 'express.urlencoded()', 'express.json()', 'app.listen', 'res.send'),
                $q('Can express.json() read title=tea form bytes?', 'No', 'Yes', 'Only on GET', 'Only if extended is true'),
                $q('After either parser, where are the fields?', 'req.body', 'req.params', 'req.query', 'The status'),
            ],
            'practice' => 'Say which parser you would register for a JSON API and which you would add if an HTML form also posts to the same path.',
        ]),
    ];
}

if (PHP_SAPI === 'cli' && isset($_SERVER['argv'][0]) && realpath((string)$_SERVER['argv'][0]) === realpath(__FILE__)) {
    require_once __DIR__ . '/includes/db.php';
    $chapters = express_chapters();
    if (count($chapters) !== 32) {
        fwrite(STDERR, 'expected 32 express chapters, got ' . count($chapters) . "\n");
        exit(1);
    }
    track_seed($pdo, express_meta(), $chapters);
    track_place($pdo, 'express-tutorial', array_map(static fn(array $ch): string => $ch['title'], $chapters));
    track_sections($pdo, 'express-tutorial', [
        6 => 'The request',
        11 => 'Reading the request',
        18 => 'Middleware',
        24 => 'The API',
        31 => 'Production habits',
        32 => 'The project',
    ]);
}
