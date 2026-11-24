<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/track_seed.php';
require_once __DIR__ . '/includes/teach_figures.php';

function dotnet_meta(): array
{
    return [
        'title' => '.NET Tutorial',
        'slug' => 'dotnet-tutorial',
        'description' => 'ASP.NET Core and modern .NET from the first request through minimal APIs, validation, services, options, auth, CORS, configuration, health, and host lifetime, ending in a notes endpoint you can explain.',
        'keywords' => 'dotnet tutorial, asp.net core, minimal api, dependency injection, options pattern, authentication authorization, cors, health checks, problem details',
    ];
}

function dotnet_chapters(): array
{
    require_once __DIR__ . '/seed_dotnet_b.php';
    require_once __DIR__ . '/seed_dotnet_c.php';
    return array_merge(dotnet_chapters_a(), dotnet_chapters_b(), dotnet_chapters_c());
}

function dotnet_chapters_a(): array
{
    $L = static function (array $c): array {
        return track_lesson('csharp', 'notes.cs', $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };

    return [
        $L([
            'section' => 'The platform',
            'title' => '.NET is the runtime and C# is the language you write',
            'keywords' => '.NET runtime, C# language, ASP.NET Core',
            'lead' => '.NET is the runtime that loads your program and the libraries around it. C# is the language you type. ASP.NET Core is the library that turns an HTTP request into a call to your function and turns your return value back into a status and a body. This course is that HTTP path. The C# course stays the language course. Here the sentences are about requests.',
            'heading' => 'Three names, three jobs',
            'para' => 'You write C#. The project references the web stack. The runtime hosts the process. A notes API is a process that listens, reads a method and a path, and writes a status. None of those steps is a C# keyword. MapGet, Results, and the host are library calls. The playground on this course prints the decision those calls would make, so you can see the status and the fields without starting a server.',
            'bullets' => [
                'C# is the language. .NET is what runs the compiled program.',
                'ASP.NET Core is the web library on top of that runtime.',
                'A route is your function plus a method and a path.',
                'The status code is part of the answer, the same way it was for any HTTP API.',
            ],
            'visual' => tf_flow('dnplat', 'Language, library, request', [['C#', 'what you type'], ['web stack', 'the pipeline'], ['HTTP', 'status and body']], 'The playground prints the decision. The prose names the real types that make it.'),
            'rows' => [
                ['.NET', 'The runtime', 'Loads the program.'],
                ['C#', 'The language', 'The file you edit.'],
                ['ASP.NET Core', 'The web library', 'Request in, response out.'],
                ['Route', 'Method plus path', 'Your function.'],
                ['Status', 'The outcome', '200, 201, 400, 404.'],
                ['This course', 'The HTTP path', 'Not a second C# grammar course.'],
            ],
            'e1' => ['Example 1 — name the three pieces', 'The prints are the runtime, the language, and the app you are about to build.', <<<'CS'
string runtimeName = ".NET";
string language = "C#";
string app = "notes";
Console.WriteLine(runtimeName);
Console.WriteLine(language);
Console.WriteLine(app);
CS],
            'e2' => ['Example 2 — a request still has a verb and a path', 'Those two strings are what the route will match. The language does not replace them.', <<<'CS'
string verb = "GET";
string route = "/notes";
int status = 200;
Console.WriteLine(verb);
Console.WriteLine(route);
Console.WriteLine(status);
CS],
            'micro' => [
                'A console program and a web program can share the same language and a different SDK.',
                'The browser does not run your C#. It sends HTTP to the process.',
                'When a lesson says Results.Ok, the preview prints the 200 that result stands for.',
            ],
            'quiz' => [
                $q('What is C# in this course?', 'The language you write', 'The HTTP status', 'The database', 'The browser'),
                $q('What does ASP.NET Core do with a request?', 'It matches a route and writes a status and a body', 'It replaces the C# compiler', 'It stores notes in the browser only', 'It chooses the font'),
                $q('What does the playground show?', 'The decision a route would make', 'A live public port', 'The database password', 'A second language'),
            ],
            'practice' => 'In one sentence each, say what .NET, C#, and ASP.NET Core are responsible for in a notes API.',
        ]),
        $L([
            'section' => 'The platform',
            'title' => 'A project file chooses the SDK',
            'keywords' => 'csproj, web sdk, asp.net core project',
            'lead' => 'The project file tells the tools which SDK to use. A web API uses the web SDK, so the build knows about the host, the configuration stack, and the server. A class library uses a smaller SDK and does not listen. Picking the web SDK is what makes this a program that can serve /notes.',
            'heading' => 'The file is the choice',
            'para' => 'You rarely write the project file by hand after the first time. You still read it. The SDK attribute is the line that matters. Target framework is the other line: it picks the runtime generation you compile for. Packages you add, such as a database driver, appear as package references. The notes code does not belong in this file. The file only says what kind of program this is.',
            'bullets' => [
                'The web SDK is the one that hosts HTTP.',
                'A class library SDK compiles code that another project references. It does not listen.',
                'The target framework must be a runtime you can actually install.',
                'Secrets do not go in the project file. Names of packages do.',
            ],
            'visual' => tf_panels('dnsdk', 'Two project kinds', 'web', ['listens', 'has a pipeline', 'serves /notes'], 'library', ['compiles', 'no port', 'called by the web project'], 'The SDK is the decision. The C# files come after it.'),
            'rows' => [
                ['Web SDK', 'This course', 'An HTTP program.'],
                ['Library SDK', 'Shared code', 'No listen.'],
                ['Target framework', 'Which runtime', 'Must be installed.'],
                ['Package reference', 'A dependency', 'Not a secret.'],
                ['Source files', 'C#', 'Not the project choice itself.'],
                ['Secrets', 'Elsewhere', 'Not in this file.'],
            ],
            'e1' => ['Example 1 — a web project', 'Web is the SDK kind. notes.csproj is the file.', <<<'CS'
string sdk = "Web";
string file = "notes.csproj";
int listens = 1;
Console.WriteLine(sdk);
Console.WriteLine(file);
Console.WriteLine(listens);
CS],
            'e2' => ['Example 2 — a library does not take a port', 'The same language, a different SDK, and listens stays 0.', <<<'CS'
string sdk = "Library";
string file = "notes.core.csproj";
int listens = 0;
Console.WriteLine(sdk);
Console.WriteLine(file);
Console.WriteLine(listens);
CS],
            'micro' => [
                'Create the web project once, then spend your time in the route files.',
                'If the build cannot find the web SDK, the target or the install is wrong. The route is not the bug yet.',
                'Keep one web project for the host. Put pure rules in a library only when a second host needs them.',
            ],
            'quiz' => [
                $q('Which SDK does a notes API use?', 'The web SDK', 'A library SDK', 'No project file', 'The browser'),
                $q('Does a class library listen for HTTP by itself?', 'No', 'Yes', 'Only on port 80', 'Only for GET'),
                $q('What else does the project file name besides the SDK?', 'The target framework', 'Every note title', 'The caller password', 'The CSS color'),
            ],
            'practice' => 'Say which SDK your notes API uses and which one you would use for a helper that only checks a title.',
        ]),
        $L([
            'section' => 'The platform',
            'title' => 'The entry point builds the app and runs it',
            'keywords' => 'WebApplication, CreateBuilder, Build, Run',
            'lead' => 'The entry point does three things. CreateBuilder reads configuration and prepares the service container. Build creates the app, which is the pipeline you will add routes to. Run starts listening and blocks until the process is asked to stop. Routes are registered after Build and before Run.',
            'heading' => 'Build, then map, then run',
            'para' => 'If you map a route after Run, that line never happens, because Run does not return while the server is up. If you forget Run, the process configures itself and exits, and nothing listens. The builder is where services are registered. The app is where middleware and routes are registered. Mixing those two piles up is the usual early confusion. Services are constructed later, per request or once, according to their lifetime. Routes are just entries in a table until a request arrives.',
            'bullets' => [
                'CreateBuilder, then Build, then the maps, then Run.',
                'Run blocks. Code after it waits until shutdown.',
                'Services are registered on the builder. Routes are registered on the app.',
                'The port comes from configuration, which the builder already loaded.',
            ],
            'visual' => tf_flow('dnentry', 'Startup in order', [['builder', 'config and services'], ['app', 'routes'], ['run', 'listen']], 'Nothing is listening until Run. Nothing after Run runs while you are serving.'),
            'rows' => [
                ['CreateBuilder', 'Start', 'Loads configuration.'],
                ['Services', 'On the builder', 'Registered, not called yet.'],
                ['Build', 'The app', 'The pipeline exists.'],
                ['Map', 'On the app', 'Before Run.'],
                ['Run', 'Listen', 'Blocks until stop.'],
                ['Code after Run', 'After shutdown', 'Not during a request.'],
            ],
            'e1' => ['Example 1 — the three steps', 'The prints are the order. 5000 stands for the port configuration already loaded.', <<<'CS'
string step1 = "CreateBuilder";
string step2 = "Build";
string step3 = "Run";
int port = 5000;
Console.WriteLine(step1);
Console.WriteLine(step2);
Console.WriteLine(step3);
Console.WriteLine(port);
CS],
            'e2' => ['Example 2 — a route exists only after Build and before Run', 'Map is the middle step. The status is what that route will answer once Run is listening.', <<<'CS'
string before = "Build";
string map = "MapGet /notes";
string after = "Run";
int status = 200;
Console.WriteLine(before);
Console.WriteLine(map);
Console.WriteLine(after);
Console.WriteLine(status);
CS],
            'micro' => [
                'Development and production share this shape. The configuration values change, not the three steps.',
                'A test can build the app and send a fake request without calling Run.',
                'If the port is taken, Run fails at startup. That is a process problem, not a route bug.',
            ],
            'quiz' => [
                $q('Which call starts listening?', 'Run', 'CreateBuilder', 'A C# string', 'The project file alone'),
                $q('Where do you register a route?', 'On the app, after Build and before Run', 'After Run', 'Inside the project file', 'In the browser'),
                $q('Where do you register a service?', 'On the builder', 'After Run', 'In the JSON body', 'On the status code'),
            ],
            'practice' => 'Write the four words in order: the builder call, Build, the map, and Run. Under each, say whether a request can arrive yet.',
        ]),
        $L([
            'section' => 'The platform',
            'title' => 'MapGet is a route on the minimal API',
            'keywords' => 'MapGet, minimal API, route handler',
            'lead' => 'MapGet("/notes", handler) adds one route: the method GET and the path /notes. The handler is the function that produces the result. A minimal API is this style, a function on the app, rather than a class with attributes. GET /notes lists. The handler returns the note. It does not listen, and it does not choose the port.',
            'heading' => 'One map call, one pair',
            'para' => 'MapPost, MapPut, MapPatch, and MapDelete are the same idea with a different method. The path is a pattern. /notes matches the collection. /notes/{id} matches one note. You can write the handler as a lambda or as a method on a service. The result of the handler becomes the HTTP response. If you return a plain string, the framework still wraps it in a result. Prefer an explicit result so the status is obvious.',
            'bullets' => [
                'MapGet is GET only. POST /notes will not enter it.',
                'The path you pass is the public path, unless you are inside a group that adds a prefix.',
                'The handler runs only when method and path both match.',
                'Returning the note is not the same as choosing 201. A list is 200.',
            ],
            'visual' => tf_flow('dnmap', 'A minimal route', [['GET', 'the method'], ['/notes', 'the path'], ['handler', 'the note'], ['200', 'the status']], 'MapGet records the pair. A request later runs the handler.'),
            'rows' => [
                ['MapGet', 'GET', 'Read.'],
                ['MapPost', 'POST', 'Create.'],
                ['Path', 'The pattern', '/notes or /notes/{id}.'],
                ['Handler', 'Your function', 'Returns a result.'],
                ['Group prefix', 'Added outside', 'The map can stay short.'],
                ['Wrong method', 'No match', 'This handler does not run.'],
            ],
            'e1' => ['Example 1 — GET /notes', 'The four prints are the match and the answer.', <<<'CS'
string verb = "GET";
string route = "/notes";
string item = "milk";
int status = 200;
Console.WriteLine(verb);
Console.WriteLine(route);
Console.WriteLine(item);
Console.WriteLine(status);
CS],
            'e2' => ['Example 2 — POST does not use the GET handler', 'Same path, different method. The GET handler is not the one that answers. The status you would rely on is not the list status.', <<<'CS'
string verb = "POST";
string route = "/notes";
int getHandlerRan = 0;
int status = 404;
Console.WriteLine(verb);
Console.WriteLine(route);
Console.WriteLine(getHandlerRan);
Console.WriteLine(status);
CS],
            'micro' => [
                '404 in the second example is what you get when no other route matches that method. A later MapPost would take the call instead.',
                'Keep handlers small. A handler that also contains the storage rules becomes hard to test.',
                'Names in the path are lowercase by habit. Clients will send the path you document.',
            ],
            'quiz' => [
                $q('What does MapGet("/notes", ...) match?', 'GET /notes', 'POST /notes', 'Every method on /notes', 'GET /health'),
                $q('Does the handler listen on a port?', 'No. Run does', 'Yes', 'Only the first time', 'Only in production'),
                $q('What should a list return as a status?', '200', '201', '204', '301'),
            ],
            'practice' => 'Write the Map call you would use for GET /notes and the one for POST /notes. Say which status each handler is aiming at.',
        ]),
        $L([
            'section' => 'The platform',
            'title' => 'The pipeline is middleware in order',
            'keywords' => 'ASP.NET Core middleware, Use, pipeline order',
            'lead' => 'Every request walks a list. Middleware runs on the way in, then the endpoint runs, then middleware runs on the way out in reverse. The order you call Use is the order on the way in. Authentication has to run before authorization, because authorization reads the identity authentication built. A logger at the start sees the request. A logger wrapped around the next call sees the status on the way out.',
            'heading' => 'In, then the route, then out',
            'para' => 'Use adds middleware. Map endpoints are also in the list, and they belong after the middleware that must prepare the request. If authorization is registered before authentication, it sees an anonymous caller and refuses people who would have been known a line later. Exception handling belongs at the top so it can catch a failure on the way out and still write a clean status. Once the endpoint has started the body, middleware on the way out can log, but it cannot replace the status.',
            'bullets' => [
                'Registration order is the inbound order.',
                'The way out is the reverse of the way in.',
                'Authentication, then authorization. That pair is not optional in its order.',
                'Put the endpoint maps after the middleware that prepares the call.',
            ],
            'visual' => tf_flow('dnpipe', 'In, route, out', [['auth', 'who is calling'], ['allow', 'may they'], ['route', 'the note'], ['log', 'the status']], 'Allow reads the identity auth already built. The log sees the status after the route.'),
            'rows' => [
                ['Use order', 'Inbound order', 'First registered, first in.'],
                ['Way out', 'Reverse', 'The outer middleware finishes last.'],
                ['Authentication', 'Before authorization', 'Builds the identity.'],
                ['Authorization', 'After authentication', 'Reads that identity.'],
                ['Exception handler', 'Near the start', 'Can still write a status.'],
                ['After the body starts', 'Too late to change status', 'You can still log.'],
            ],
            'e1' => ['Example 1 — a known caller who is allowed', 'Auth runs, the allow step passes, the route returns the note.', <<<'CS'
string first = "auth";
string second = "allow";
string item = "milk";
int status = 200;
Console.WriteLine(first);
Console.WriteLine(second);
Console.WriteLine(item);
Console.WriteLine(status);
CS],
            'e2' => ['Example 2 — allow before auth sees nobody', 'The allow step ran too early. It refuses the call. The route does not return the note.', <<<'CS'
string first = "allow";
string second = "auth";
int routeRan = 0;
int status = 401;
Console.WriteLine(first);
Console.WriteLine(second);
Console.WriteLine(routeRan);
Console.WriteLine(status);
CS],
            'micro' => [
                'The second example is the bug, not the design. Swap the two lines back.',
                'Middleware that writes the body and also calls the next piece will fight the endpoint. Pick one.',
                'A branch of the pipeline, such as a MapWhen, is still ordered inside itself.',
            ],
            'quiz' => [
                $q('What is the inbound order?', 'The order you registered middleware', 'Alphabetical by path', 'The JSON field order', 'Random per request'),
                $q('Which must run first?', 'Authentication, then authorization', 'Authorization, then authentication', 'The route, then authentication', 'The body, then the path'),
                $q('When can middleware still replace the status?', 'Before the body has started', 'After the client has the body', 'After the process exits', 'Never'),
            ],
            'practice' => 'Put these in inbound order and say why: authorization, the notes endpoint, exception handling, authentication.',
        ]),
        $L([
            'section' => 'The platform',
            'title' => 'Results.Ok and Results.NotFound set the status',
            'keywords' => 'Results.Ok, Results.NotFound, IResult',
            'lead' => 'A handler should return a result that names the status. Results.Ok sets 200 and writes the value as the body. Results.NotFound sets 404. Results.Created sets 201 and can carry the new path. Returning the object alone hides the status in a convention. Naming the result makes the ending obvious to the next reader.',
            'heading' => 'The result is the status plus the body',
            'para' => 'Ok is for a read that found the resource, including a list that happens to be empty. NotFound is for one note that is not there. Created is for a successful insert. BadRequest is for a body you refuse. You pick one of them and return. You do not return Ok with an error flag and also hope the client notices. The playground prints the status and the field that result would write.',
            'bullets' => [
                'Results.Ok is 200.',
                'Results.NotFound is 404.',
                'Results.Created is 201 and a location.',
                'An empty list is Ok. A missing id is NotFound.',
            ],
            'visual' => tf_flow('dnres', 'Pick the result', [['found', 'Ok 200'], ['created', 'Created 201'], ['bad body', 'BadRequest 400'], ['no id', 'NotFound 404']], 'The method name is the status. The value is the body.'),
            'rows' => [
                ['Ok', '200', 'A read, or an empty list.'],
                ['Created', '201', 'The new id and path.'],
                ['BadRequest', '400', 'The client can fix the body.'],
                ['NotFound', '404', 'That id is not here.'],
                ['StatusCode', 'When you need another', '503 for not ready.'],
                ['Ok with an error flag', 'Avoid', 'The status should carry the failure.'],
            ],
            'e1' => ['Example 1 — the note is there', 'Ok. Status 200. The item is the body.', <<<'CS'
string result = "Ok";
int status = 200;
string item = "milk";
Console.WriteLine(result);
Console.WriteLine(status);
Console.WriteLine(item);
CS],
            'e2' => ['Example 2 — that id is not there', 'NotFound. Status 404. There is no item to invent.', <<<'CS'
string result = "NotFound";
int status = 404;
string item = "";
Console.WriteLine(result);
Console.WriteLine(status);
Console.WriteLine(item);
CS],
            'micro' => [
                'Results.NoContent is 204 and must not include a body.',
                'Created needs the path of the new note, usually /notes and the id.',
                'A problem-details body can ride on BadRequest. The status is still 400.',
            ],
            'quiz' => [
                $q('Which result is a successful read?', 'Results.Ok', 'Results.NotFound', 'Results.Created', 'Results.BadRequest'),
                $q('Which result is an unknown id?', 'Results.NotFound', 'Results.Ok', 'Results.Created', 'An empty list with 200'),
                $q('Why not return Ok with an error flag?', 'Clients that trust the status will treat the failure as success', 'Ok cannot carry JSON', '404 is only for health', 'The port would change'),
            ],
            'practice' => 'Name the Results method and the status for a list, a create, a bad title, and a missing id.',
        ]),
        $L([
            'section' => 'The HTTP result',
            'title' => 'A route value arrives as text',
            'keywords' => 'route constraint, id int, 404 no match',
            'lead' => 'A path segment is text until a constraint or your code converts it. /notes/{id} captures the segment as text. /notes/{id:int} matches only when the segment is an integer. If the client calls /notes/abc and the pattern requires an int, the route does not match. The result is 404, and your handler does not run. You never see the letters abc inside the function.',
            'heading' => 'A failed constraint is not a bad model',
            'para' => 'That 404 is "no route", not "your if rejected the id". Do not expect a 400 from a constraint failure. If you accept a string id and then parse it yourself, you can choose 400 for abc. If you declare an int constraint, abc never arrives and the framework answers 404. Pick one style and document it. A matched int is already a number inside the handler. You do not parse it again.',
            'bullets' => [
                '{id} is text. {id:int} must be an integer or the route misses.',
                'A miss is 404. Your handler does not run.',
                'A matched int parameter is already an int.',
                'An empty segment does not fill the value. /notes/ is not /notes/4.',
            ],
            'visual' => tf_split('dnid', 'Does {id:int} match?', 'Segment is an integer?', 'yes', 'handler gets 4', 'no', '404, handler skipped', 'abc is not delivered to your function as a failed parse. The route simply does not match.'),
            'rows' => [
                ['{id}', 'Text', 'You parse it if you need a number.'],
                ['{id:int}', 'Integer only', 'abc does not match.'],
                ['Match', 'Handler runs', 'The value is the number.'],
                ['No match', '404', 'Not a 400 from your if.'],
                ['/notes/', 'Different path', 'The id segment is missing.'],
                ['Document the choice', 'One style', 'Text-and-400, or int-and-404.'],
            ],
            'e1' => ['Example 1 — 4 matches an int constraint', 'The handler runs. The id is a number. The note comes back.', <<<'CS'
string raw = "4";
int matched = 1;
int id = 4;
string item = "milk";
int status = 200;
Console.WriteLine(raw);
Console.WriteLine(matched);
Console.WriteLine(id);
Console.WriteLine(item);
Console.WriteLine(status);
CS],
            'e2' => ['Example 2 — abc does not match', 'The handler does not run. Status 404.', <<<'CS'
string raw = "abc";
int matched = 0;
int status = 404;
Console.WriteLine(raw);
Console.WriteLine(matched);
Console.WriteLine(status);
CS],
            'micro' => [
                'A GUID constraint works the same way: a bad shape is a non-match, so 404.',
                'If customers send ids that are not numbers, do not use an int constraint and then wonder why you cannot return a helpful 400.',
                'Route values are not query values. id in the path and id in the query are different bags.',
            ],
            'quiz' => [
                $q('What happens to GET /notes/abc when the pattern is /notes/{id:int}?', '404 and the handler does not run', 'The handler receives abc and returns 400', '201', 'The process exits'),
                $q('What type is id inside the handler when the constraint is int and the segment is 4?', 'An int', 'Still text you must parse', 'A JSON object', 'A header'),
                $q('How is a path id different from a query id?', 'They are different bags of values', 'They are always the same variable', 'The query replaces the path', 'Only the query can be an int'),
            ],
            'practice' => 'Choose int-constraint or string-then-parse for note ids. For the input abc, say the status and whether your handler runs.',
        ]),
        $L([
            'section' => 'The HTTP result',
            'title' => 'A query value may be missing',
            'keywords' => 'query string, optional parameter, ASP.NET Core',
            'lead' => 'A query value filters a collection. /notes?q=milk asks for notes that match milk. /notes with no query asks for the collection with no filter. Missing is normal. It is not a 404, because the collection exists. A present value in the wrong shape, such as a page number that is not a number, is a 400.',
            'heading' => 'Missing, empty, and wrong are three cases',
            'para' => 'Bind an optional string. Null or empty means no filter, and you return the list with 200. A word means a filter, and an empty result is still 200 and an empty list. Do not use 404 for "no note had that word". Reserve 404 for a path id that does not exist. If the parameter is a non-nullable int and the client omits it, the framework can reject the request before your handler. Make optional filters optional.',
            'bullets' => [
                'No query: 200 and the unfiltered list.',
                'A search word with no hits: 200 and an empty list.',
                'A malformed number that you required: 400.',
                'A missing path id is the 404 case. A missing query is not.',
            ],
            'visual' => tf_panels('dnquery', 'Query versus path', 'query q', ['may be absent', 'filters the list', '200 either way'], 'path id', ['identifies one note', 'a miss is 404', 'not a filter'], 'The question mark starts a filter. The path segment names one resource.'),
            'rows' => [
                ['Absent q', 'No filter', '200 and the list.'],
                ['q=milk', 'A filter', '200 and the matches.'],
                ['No matches', 'Empty list', 'Still 200.'],
                ['page=abc', 'Bad shape', '400 if page must be a number.'],
                ['Optional', 'Declare it optional', 'Or a missing value is rejected for you.'],
                ['Path id', 'Not this lesson', '404 when that one note is gone.'],
            ],
            'e1' => ['Example 1 — a filter is present', 'q is milk. The list answers 200.', <<<'CS'
string q = "milk";
int present = 1;
int status = 200;
string item = "milk";
Console.WriteLine(q);
Console.WriteLine(present);
Console.WriteLine(status);
Console.WriteLine(item);
CS],
            'e2' => ['Example 2 — the filter is absent', 'present is 0. The collection still answers 200. Nothing was "not found".', <<<'CS'
string q = "";
int present = 0;
int status = 200;
string item = "milk";
Console.WriteLine(q);
Console.WriteLine(present);
Console.WriteLine(status);
Console.WriteLine(item);
CS],
            'micro' => [
                'Decide whether an empty q= is the same as omitting q. Treating both as no filter is the simple rule.',
                'Do not put a secret in the query. It lands in logs and in browser history.',
                'Repeatable filters can be listed. A single q is enough for this notes API.',
            ],
            'quiz' => [
                $q('What status is a search that matches nothing?', '200 and an empty list', '404', '201', '500'),
                $q('What status is a missing optional filter?', '200 and the unfiltered list', '404', '400 always', '204'),
                $q('When is 400 the right query result?', 'A required number was present and not a number', 'The word matched nothing', 'The caller omitted q', 'The note was created'),
            ],
            'practice' => 'Give the status for /notes, for /notes?q=zzz when nothing matches, and for /notes?page=abc when page must be an int.',
        ]),
        $L([
            'section' => 'The HTTP result',
            'title' => 'Results.Json sets the content type',
            'keywords' => 'Results.Json, content type, application/json',
            'lead' => 'The body is bytes. The content type tells the client how to read them. Results.Json writes JSON and sets the content type to application/json. Results.Text writes text and sets a text type. If you set one and write the other, the client trusts the header and misreads the bytes. Set them as a pair by using the result that matches the body.',
            'heading' => 'The header and the bytes agree',
            'para' => 'A notes API speaks JSON. Titles, ids, and errors are JSON objects. Do not return a bare string for one route and an object for the next unless you mean two different types and you set both headers. Problem details is still JSON. A file download would be a different type and a different result. This API does not download files. Charset for JSON is UTF-8, which the JSON result already assumes.',
            'bullets' => [
                'JSON body, application/json header.',
                'Use Results.Json or Ok of an object so the pair stays together.',
                'An error object is still JSON. It does not become text/plain.',
                'Do not hand-write the header and then return a result that overwrites it by surprise. Prefer one result.',
            ],
            'visual' => tf_flow('dntype', 'Header, then bytes', [['content type', 'application/json'], ['body', 'the note'], ['client', 'parses JSON']], 'The client does not guess. It reads the header you sent.'),
            'rows' => [
                ['Results.Json', 'JSON', 'The notes API.'],
                ['Results.Text', 'Text', 'Not the note shape.'],
                ['Ok of an object', 'JSON', 'The usual read.'],
                ['Error object', 'JSON too', 'Same type, different status.'],
                ['Mismatch', 'A client bug you caused', 'Header and bytes must agree.'],
                ['UTF-8', 'The characters', 'Keep non-English titles intact.'],
            ],
            'e1' => ['Example 1 — a note as JSON', 'The type and the item travel together. Status 200.', <<<'CS'
string type = "application/json";
string item = "milk";
int status = 200;
Console.WriteLine(type);
Console.WriteLine(item);
Console.WriteLine(status);
CS],
            'e2' => ['Example 2 — an error is JSON as well', '400 does not change the content type. The body is still an object the client can parse.', <<<'CS'
string type = "application/json";
string error = "title is required";
int status = 400;
Console.WriteLine(type);
Console.WriteLine(error);
Console.WriteLine(status);
CS],
            'micro' => [
                'A browser showing the raw body is still obeying the content type. It is not a second format.',
                'If a proxy compresses the body, the content type stays application/json. Compression is a different header.',
                'Do not return JSON with a 204. 204 has no body, so it has no JSON type to set.',
            ],
            'quiz' => [
                $q('Which content type does a notes object use?', 'application/json', 'text/plain', 'No header', 'The port number'),
                $q('Does a 400 error body use a different type in this API?', 'No, it stays JSON', 'Yes, it becomes plain text', 'It becomes a redirect', 'It has no status'),
                $q('Why keep the header and the bytes together?', 'The client trusts the header when it reads the body', 'The port is chosen from the header', 'JSON cannot represent a title otherwise', '404 requires text'),
            ],
            'practice' => 'Write the content type and one JSON field for a 200 note and for a 400 error. Confirm both types match.',
        ]),
        $L([
            'section' => 'The HTTP result',
            'title' => 'The body binds into a record',
            'keywords' => 'minimal API binding, record, JSON body',
            'lead' => 'A create handler can take a record. The framework reads the JSON body and fills the record properties. A record NoteInput with a Title property becomes the argument. Your handler reads input.Title. It does not read raw bytes. If the body is empty or is not JSON, the bind fails and you should answer 400 rather than store a blank note.',
            'heading' => 'The record is the shape you accept',
            'para' => 'Property names match JSON names without caring about letter case, unless you change that option. title and Title bind to Title. An extra JSON field is ignored by default. A missing Title leaves the string empty or null, depending on how you declared it. Declare the fields you require as the ones you will check. Binding fills the object. It does not decide that the title is good. The next lesson is that decision, and it is not automatic on a minimal API.',
            'bullets' => [
                'The parameter type is the shape. A record is a clear shape.',
                'JSON name matching is case-insensitive by default.',
                'Extra fields do not become properties you did not declare.',
                'A filled record can still be invalid. Binding is not validation.',
            ],
            'visual' => tf_flow('dnbind', 'Bytes become a record', [['JSON', 'title tea'], ['binder', 'fills Title'], ['handler', 'reads the record']], 'The handler starts from the record. It does not parse the bytes itself.'),
            'rows' => [
                ['Record', 'The shape', 'Title and anything else you accept.'],
                ['JSON title', 'The field', 'Maps to Title.'],
                ['Case', 'Insensitive by default', 'title and Title match.'],
                ['Extra field', 'Ignored', 'It is not a property.'],
                ['Empty body', 'Bind failure', '400, do not store.'],
                ['Validation', 'A later step', 'Binding only fills.'],
            ],
            'e1' => ['Example 1 — tea binds and is created', 'Title is tea. Status 201.', <<<'CS'
string title = "tea";
int bound = 1;
int status = 201;
Console.WriteLine(title);
Console.WriteLine(bound);
Console.WriteLine(status);
CS],
            'e2' => ['Example 2 — there was nothing to bind', 'bound is 0. Status 400. No note is stored.', <<<'CS'
string title = "";
int bound = 0;
int status = 400;
Console.WriteLine(title);
Console.WriteLine(bound);
Console.WriteLine(status);
CS],
            'micro' => [
                'Do not take a raw string for the whole body and slice it. The binder already did the structured read.',
                'A create record and a response record can be different types. You do not have to echo every stored column.',
                'Large unexpected bodies should be refused by a size limit before you bind them.',
            ],
            'quiz' => [
                $q('What does the binder do?', 'It fills a record from the JSON body', 'It chooses the port', 'It proves the caller is logged in', 'It writes the database'),
                $q('Does a successful bind mean the title is valid?', 'No', 'Yes', 'Only for GET', 'Only when the status is 404'),
                $q('What should an empty body produce?', '400 and no stored note', '201 with a blank title', '404', '200 with the whole list'),
            ],
            'practice' => 'Define the two fields a create record needs for this API, and say what you do when the JSON object is missing.',
        ]),
        $L([
            'section' => 'The HTTP result',
            'title' => 'A bad model is 400 before your code',
            'keywords' => 'ApiController, DataAnnotations, minimal API validation',
            'lead' => 'A controller marked with ApiController checks annotation rules and can return 400 before your action runs. A minimal API does not do that for you. If the title is required only by an annotation, a minimal handler still runs unless you add a filter or an if. The same attribute is not the same behavior in the two styles.',
            'heading' => 'Know which style you are in',
            'para' => 'On a controller, an invalid model means your action does not run, reached stays 0, and the client gets 400. On a minimal API, the handler is entered, and a missing check stores the bad note or throws later. Add the check in the handler or in an endpoint filter, and return Results.BadRequest. Do not assume the attribute saved you. Also do not return 400 for an unknown id. That id was well formed and the note is absent, which is 404.',
            'bullets' => [
                'ApiController plus annotations can answer 400 before the action.',
                'Minimal APIs do not auto-run those annotations.',
                'If you want the check, write it or add a filter.',
                '400 is a bad body. 404 is a missing note. Keep them apart.',
            ],
            'visual' => tf_split('dnvalid', 'Did a check stop the handler?', 'Caught before handler?', 'yes', '400, handler skipped', 'no', 'handler must check', 'Controllers can catch it. Minimal APIs need the check written down.'),
            'rows' => [
                ['ApiController', 'Auto 400', 'Invalid models skip the action.'],
                ['Minimal API', 'No auto check', 'The handler is entered.'],
                ['Annotation alone', 'Not enough here', 'On a minimal route.'],
                ['Your if', 'The check', 'Return BadRequest.'],
                ['Filter', 'The same check for many routes', 'Still something you add.'],
                ['Unknown id', '404', 'Not this 400.'],
            ],
            'e1' => ['Example 1 — the controller path', 'The model was invalid. The action did not run. Status 400.', <<<'CS'
int reached = 0;
int status = 400;
string reason = "title is required";
Console.WriteLine(reached);
Console.WriteLine(status);
Console.WriteLine(reason);
CS],
            'e2' => ['Example 2 — a minimal handler that checks for itself', 'The handler ran, saw an empty title, and returned 400. Nothing was stored.', <<<'CS'
int reached = 1;
string title = "";
int stored = 0;
int status = 400;
Console.WriteLine(reached);
Console.WriteLine(title);
Console.WriteLine(stored);
Console.WriteLine(status);
CS],
            'micro' => [
                'A check that lives in one service method can be used by both styles. The HTTP status is still chosen at the edge.',
                'Do not catch a bad title as an exception and turn it into 500. The client can fix it.',
                'If you move a route from a controller to a minimal API, re-test the empty body. The 400 may have disappeared.',
            ],
            'quiz' => [
                $q('Do minimal APIs automatically enforce DataAnnotations?', 'No', 'Yes', 'Only for GET', 'Only when the SDK is a library'),
                $q('What does ApiController do with an invalid model?', 'It can return 400 before the action runs', 'It returns 201', 'It deletes the note', 'It skips authentication forever'),
                $q('Which status is an unknown but well formed id?', '404', '400', '201', '204'),
            ],
            'practice' => 'You are on a minimal API. Write the if for an empty title: what you return, and what you refuse to store. Then say what a controller would have done before the action.',
        ]),
    ];
}

if (PHP_SAPI === 'cli' && isset($_SERVER['argv'][0]) && realpath((string)$_SERVER['argv'][0]) === realpath(__FILE__)) {
    require_once __DIR__ . '/includes/db.php';
    $chapters = dotnet_chapters();
    if (count($chapters) !== 32) {
        fwrite(STDERR, 'expected 32 dotnet chapters, got ' . count($chapters) . "\n");
        exit(1);
    }
    track_seed($pdo, dotnet_meta(), $chapters);
    track_place($pdo, 'dotnet-tutorial', array_map(static fn(array $ch): string => $ch['title'], $chapters));
    track_sections($pdo, 'dotnet-tutorial', [
        6 => 'The platform',
        12 => 'The HTTP result',
        18 => 'Around the route',
        24 => 'Identity and settings',
        31 => 'Running it for real',
        32 => 'The project',
    ]);
}
