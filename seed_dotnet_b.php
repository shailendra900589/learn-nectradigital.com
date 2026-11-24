<?php
declare(strict_types=1);

function dotnet_chapters_b(): array
{
    $L = static function (array $c): array {
        return track_lesson('csharp', 'notes.cs', $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };

    return [
        $L([
            'section' => 'The HTTP result',
            'title' => 'Problem details is one error shape',
            'keywords' => 'ProblemDetails, error shape, status title',
            'lead' => 'Clients should not learn a new error shape for every route. Problem details is one JSON object: a title, the status, and a short detail. A missing title and a missing note can both use that object. The status still changes. 400 and 404 stay different. The shape around them stays the same.',
            'heading' => 'Same object, different status',
            'para' => 'Results.Problem and Results.ValidationProblem write this shape. The title is a short human line. The status matches the HTTP status. The detail can name the field. Do not put a stack, a SQL sentence, or a secret in the detail. Those belong in the server log. A success body is the note, not a problem. Do not wrap every 200 in a problem object.',
            'bullets' => [
                'One error shape for the API.',
                'The status field and the HTTP status agree.',
                'Detail names the field. It does not quote a secret.',
                'Success stays a note object, not a problem object.',
            ],
            'visual' => tf_panels('dnprob', 'Two failures, one shape', '400', ['title required', 'status 400', 'no note stored'], '404', ['not found', 'status 404', 'path was fine'], 'The fields match. The number tells the client what to do next.'),
            'rows' => [
                ['title', 'Short line', 'What went wrong.'],
                ['status', 'The number', 'Same as the HTTP status.'],
                ['detail', 'The field', 'title is required, or not found.'],
                ['stack', 'Not in the body', 'Log it on the server.'],
                ['200', 'Not a problem', 'Return the note.'],
                ['ValidationProblem', 'Often 400', 'One entry per field.'],
            ],
            'e1' => ['Example 1 — a bad title', 'Status 400. The detail names the field. Nothing is stored.', <<<'CS'
string title = "title is required";
int status = 400;
int stored = 0;
Console.WriteLine(title);
Console.WriteLine(status);
Console.WriteLine(stored);
CS],
            'e2' => ['Example 2 — an unknown id', 'Same kind of object. Status 404. The path was acceptable.', <<<'CS'
string title = "not found";
int status = 404;
string id = "4";
Console.WriteLine(title);
Console.WriteLine(status);
Console.WriteLine(id);
CS],
            'micro' => [
                'If you add a type link, keep it stable. Clients may branch on the status first and ignore the link.',
                'A validation object can list several fields. A missing note has one reason.',
                'HTML error pages are for browsers that asked for HTML. This API asked for JSON, so answer with JSON.',
            ],
            'quiz' => [
                $q('What stays the same between a 400 and a 404 in this design?', 'The error object shape', 'The status number', 'The stored note', 'The port'),
                $q('What must the status field match?', 'The HTTP status', 'Always 200', 'The title length', 'The caller name'),
                $q('What do you leave out of the detail?', 'Stacks and secrets', 'The status number', 'The field name', 'The word not found'),
            ],
            'practice' => 'Write the title, status, and detail for an empty title and for an unknown id. Confirm the two objects use the same fields.',
        ]),
        $L([
            'section' => 'Around the route',
            'title' => 'The container creates the service',
            'keywords' => 'dependency injection, AddScoped, service container',
            'lead' => 'You register a service once, on the builder. The container constructs it when a route needs it, and it constructs the services that service needs. The route asks for NoteBook in its parameters. It does not call new. That is how a test can hand the route a fake book without a database.',
            'heading' => 'Register, then ask',
            'para' => 'AddScoped means one instance for the request, shared by everything in that request, and a new instance on the next request. AddSingleton means one instance for the process. AddTransient means a new instance every time it is asked for. A singleton must not take a scoped service. The singleton would keep the first request\'s instance and use it for every later caller. Register the book as scoped if it talks to a database context that is scoped.',
            'bullets' => [
                'The route receives the service. It does not construct it.',
                'Scoped: one per request. Singleton: one for the process. Transient: a new one each ask.',
                'A singleton must not depend on a scoped service.',
                'Registration is on the builder, before Build.',
            ],
            'visual' => tf_flow('dncont', 'The container fills the route', [['register', 'on the builder'], ['request', 'arrives'], ['NoteBook', 'one instance'], ['route', 'uses it']], 'The route lists the type it needs. The container decides when to construct it.'),
            'rows' => [
                ['Register', 'Once', 'Before Build.'],
                ['Ask', 'A parameter', 'The route does not call new.'],
                ['Scoped', 'Per request', 'The usual database service.'],
                ['Singleton', 'Per process', 'No scoped dependency inside it.'],
                ['Transient', 'Per ask', 'Cheap, stateless helpers.'],
                ['Fake in a test', 'Another registration', 'The route stays the same.'],
            ],
            'e1' => ['Example 1 — the route receives a book', 'constructed is 1. The route reads milk from the book it was given.', <<<'CS'
string service = "NoteBook";
int constructed = 1;
string item = "milk";
int status = 200;
Console.WriteLine(service);
Console.WriteLine(constructed);
Console.WriteLine(item);
Console.WriteLine(status);
CS],
            'e2' => ['Example 2 — a singleton must not capture a scoped book', 'The capture flag is 0. That is the legal design. A 1 would mean the process kept the first request\'s book.', <<<'CS'
string lifetime = "singleton";
string needs = "scoped book";
int capture = 0;
Console.WriteLine(lifetime);
Console.WriteLine(needs);
Console.WriteLine(capture);
CS],
            'micro' => [
                'The compiler and the container can warn when a singleton takes a scoped service. Treat that warning as a bug.',
                'Do not register the same type twice and hope. The last registration wins, and the surprise is quiet.',
                'A service with no state can be a singleton. A service that holds a request user must not be.',
            ],
            'quiz' => [
                $q('Who constructs NoteBook for a route?', 'The container', 'The route, with new', 'The browser', 'The project file'),
                $q('What is AddScoped?', 'One instance per request', 'One instance for the whole process', 'A new instance every parameter and also a shared one', 'A route'),
                $q('Why must a singleton not take a scoped service?', 'It would keep the first request\'s instance for later requests', 'Scoped services cannot be registered', 'The port would close', 'JSON would stop'),
            ],
            'practice' => 'Choose a lifetime for a notes book that uses a per-request database context, and say why it is not a singleton.',
        ]),
        $L([
            'section' => 'Around the route',
            'title' => 'A service is where the rule lives, not the route',
            'keywords' => 'application service, route stays thin, validation rule',
            'lead' => 'The route\'s job is HTTP: read the bound record, call the service, turn the answer into a status. The rule "a title must be text and is then stored" lives on the service. A second route, such as PATCH, calls the same rule. You do not paste the if into every handler.',
            'heading' => 'The edge translates. The service decides.',
            'para' => 'The service can return a small outcome: stored, or rejected. It should not build a Results object, or the service can only be used from HTTP. The route maps stored to 201 and rejected to 400. A test calls the service with a title and never opens a port. The route test, if you write one, only checks that 400 and 201 follow the outcome.',
            'bullets' => [
                'HTTP status is chosen in the route.',
                'The title rule is chosen in the service.',
                'PATCH and POST can share the rule.',
                'The service does not need to know about headers.',
            ],
            'visual' => tf_flow('dnsvc', 'Route, then rule, then status', [['route', 'HTTP'], ['service', 'the rule'], ['201 or 400', 'back at the route']], 'The service says stored or rejected. The route knows the numbers.'),
            'rows' => [
                ['Route', 'The edge', 'Status and body shape.'],
                ['Service', 'The rule', 'Title text, then store.'],
                ['Rejected', 'Maps to 400', 'Nothing stored.'],
                ['Stored', 'Maps to 201', 'The new id.'],
                ['Second route', 'Same service', 'Do not paste the if.'],
                ['Results inside the service', 'Avoid', 'That ties the rule to HTTP.'],
            ],
            'e1' => ['Example 1 — the rule accepts tea', 'valid is 1, stored is 1, the route sends 201.', <<<'CS'
string title = "tea";
int valid = 1;
int stored = 1;
int status = 201;
Console.WriteLine(title);
Console.WriteLine(valid);
Console.WriteLine(stored);
Console.WriteLine(status);
CS],
            'e2' => ['Example 2 — the rule rejects an empty title', 'valid is 0, stored is 0, the route sends 400.', <<<'CS'
string title = "";
int valid = 0;
int stored = 0;
int status = 400;
Console.WriteLine(title);
Console.WriteLine(valid);
Console.WriteLine(stored);
Console.WriteLine(status);
CS],
            'micro' => [
                'A service that returns the note record is fine. The route wraps it in Ok or Created.',
                'Do not catch every failure in the service and turn it into a string the route must decode. A clear outcome is easier.',
                'Logging the decision can live on the service. Logging the HTTP status fits the route.',
            ],
            'quiz' => [
                $q('Where is the status number chosen?', 'In the route', 'Inside the project SDK', 'In the browser', 'In the JSON name'),
                $q('Where does the empty-title rule live?', 'In the service', 'Pasted into every route only', 'In the content type', 'In the port'),
                $q('Why keep Results out of the service?', 'So the rule can be used without HTTP', 'Results cannot represent 201', 'Services cannot be registered', '404 would become 200'),
            ],
            'practice' => 'Split one create into two sentences: what the service returns for an empty title, and what status the route sends.',
        ]),
        $L([
            'section' => 'Around the route',
            'title' => 'Options bind a named section',
            'keywords' => 'IOptions, configuration section, notes options',
            'lead' => 'A block of settings is a section, such as Notes, with a limit and a name. You bind that section to a class. The route or the service asks for IOptions of that class and reads the value. The numbers live in configuration. The code holds the shape. You do not scatter the same key string through every handler.',
            'heading' => 'The section name is the contract',
            'para' => 'IOptions reads the value at startup and keeps it. IOptionsSnapshot reads it per request, which matters if configuration can change under you. IOptionsMonitor can tell you when it changes. A notes limit rarely changes during a request, so IOptions is enough. Bind fails quietly into defaults if the section is missing, so check the values you cannot live without at startup and refuse to run. Do not put a password in a log of the options object.',
            'bullets' => [
                'One class per section. NotesOptions for the Notes section.',
                'IOptions is a startup snapshot. Snapshot is per request.',
                'A missing required value should stop startup, not become a blank password.',
                'The route reads options. It does not open the settings file itself.',
            ],
            'visual' => tf_flow('dnopt', 'Section, class, service', [['Notes', 'the section'], ['options class', 'limit and name'], ['service', 'reads the value']], 'The file has the numbers. The class has the names. The service sees a filled object.'),
            'rows' => [
                ['Section Notes', 'The block', 'In the settings file.'],
                ['Class', 'The shape', 'Limit, name, and so on.'],
                ['IOptions', 'Startup value', 'Stable for the process.'],
                ['Snapshot', 'Per request', 'When the file can change.'],
                ['Missing secret', 'Do not start', 'A default password is not a default.'],
                ['Log', 'The limit', 'Not a password field.'],
            ],
            'e1' => ['Example 1 — the section bound', 'Name and limit are the two values the service will trust.', <<<'CS'
string section = "Notes";
string name = "shop";
int limit = 20;
Console.WriteLine(section);
Console.WriteLine(name);
Console.WriteLine(limit);
CS],
            'e2' => ['Example 2 — a required value is absent', 'ready is 0. The process should not start serving.', <<<'CS'
string section = "Notes";
int ready = 0;
string reason = "name missing";
Console.WriteLine(section);
Console.WriteLine(ready);
Console.WriteLine(reason);
CS],
            'micro' => [
                'Bind the section once in registration. Do not call GetSection in every request.',
                'An integer limit of 0 is a value. Decide whether 0 means "unset" or "allow nothing".',
                'Environment variables can override the section. The next lessons cover which source wins.',
            ],
            'quiz' => [
                $q('What does IOptions give you?', 'The bound section, read at startup', 'A new database row', 'The HTTP status', 'The caller password in the log'),
                $q('When is IOptionsSnapshot the better fit?', 'When the value must be read again on each request', 'When the port is a string', 'When you want to skip validation', 'When the route is GET only'),
                $q('What should happen if a required option is missing?', 'The process refuses to serve', 'It stores a note with an empty name', 'It returns 404 for /health', 'It invents a password'),
            ],
            'practice' => 'Name a Notes section with two keys, which class field each key fills, and which key is not allowed to fall back to a secret default.',
        ]),
        $L([
            'section' => 'Around the route',
            'title' => 'Use runs for every request',
            'keywords' => 'app.Use, middleware every path, health',
            'lead' => 'app.Use adds a step that runs for every request that reaches it, including /notes and /health. That is what you want for a log line and for authentication. It is the wrong place for a rule that only notes should obey, such as "a title is required", because the health probe would have to satisfy it too.',
            'heading' => 'Shared steps go in Use. Noun rules do not.',
            'para' => 'A Use delegate calls the next step or it ends the request. If it ends the request, later routes do not run. A logger calls next and also runs after the route returns, so it can see the status. A notes check belongs on the notes group or inside the notes service. Health stays a tiny route that Use still sees, and that is fine, as long as Use does not demand a JSON title.',
            'bullets' => [
                'Use is process-wide for the pipeline it sits on.',
                'Call next to continue. Skip next only when this step is the answer.',
                'Logging and authentication fit Use.',
                'A title rule does not. Put it on the notes path.',
            ],
            'visual' => tf_split('dnuse', 'Does every path need this step?', 'Yes, including health?', 'yes', 'app.Use', 'no', 'only the notes group', 'A title check is not a health check. A log line can be both.'),
            'rows' => [
                ['Use', 'Every request', 'Until a branch says otherwise.'],
                ['next', 'Continue', 'The route can answer.'],
                ['No next', 'This step answered', 'Later routes skip.'],
                ['Logger', 'A good Use', 'Sees /notes and /health.'],
                ['Title rule', 'A bad Use', 'Health has no title.'],
                ['After the route', 'The way out', 'Status is known.'],
            ],
            'e1' => ['Example 1 — the log step sees both paths', 'One middleware, two paths. Both are seen. The note route still returns 200.', <<<'CS'
string seenNotes = "yes";
string seenHealth = "yes";
string item = "milk";
int status = 200;
Console.WriteLine(seenNotes);
Console.WriteLine(seenHealth);
Console.WriteLine(item);
Console.WriteLine(status);
CS],
            'e2' => ['Example 2 — a title rule on Use would reject health', 'Health has no title. A notes-only rule in Use answers 400 for the probe. That is the bug.', <<<'CS'
string path = "/health";
int hasTitle = 0;
int status = 400;
Console.WriteLine(path);
Console.WriteLine(hasTitle);
Console.WriteLine(status);
CS],
            'micro' => [
                'The second example is the mistake. Move the title rule onto the notes route.',
                'UseWhen or a branch can limit middleware to a path prefix when a group filter is not the tool you have.',
                'Middleware order still matters. A logger outside the auth step sees anonymous calls too, which is useful.',
            ],
            'quiz' => [
                $q('Which requests does app.Use see?', 'Every request that reaches it, including health', 'Only POST /notes', 'Only the first request', 'Only failures'),
                $q('Where should the empty-title rule live?', 'On the notes route or service', 'In a Use that also wraps health', 'In the project SDK', 'In the content type'),
                $q('What must a logger do so the route can answer?', 'Call the next step', 'Return 400', 'Skip /notes', 'Close the port'),
            ],
            'practice' => 'List two steps you would put in Use and one rule you would keep on the notes route only.',
        ]),
        $L([
            'section' => 'Around the route',
            'title' => 'MapGroup shares a prefix and a filter',
            'keywords' => 'MapGroup, route prefix, group filter',
            'lead' => 'MapGroup("/notes") starts a group. Routes on the group use short paths, and the prefix is added for you. group.MapGet("/{id}") is public at /notes/{id}. A filter or a requirement added to the group applies to every route in it. Health stays off the group, so it does not inherit the notes rule.',
            'heading' => 'The prefix is written once',
            'para' => 'This is the minimal-API version of a router mount. The group can require an authorized caller once, and every notes route is covered, including the one you add next month. A route that must stay open is not placed in that group. You can nest a group, and the prefixes join. Keep the nesting shallow: /notes is enough for this API. Versioning can be an outer group of /v1 if you need it later.',
            'bullets' => [
                'The public path is the group prefix plus the route path.',
                'A group filter runs for every route in the group.',
                'Health is registered on the app, not in the notes group.',
                'One place to add a future PATCH so it inherits the same rule.',
            ],
            'visual' => tf_flow('dngrp', 'Prefix plus short path', [['/notes', 'the group'], ['/{id}', 'the route'], ['/notes/4', 'the public path']], 'You type the short path on the group. Callers use the joined path.'),
            'rows' => [
                ['MapGroup', 'A prefix', 'Also a bag of filters.'],
                ['Short path', 'On the group', '/{id} or /.'],
                ['Public path', 'Joined', '/notes/4.'],
                ['Group filter', 'Every child', 'Auth, or a check.'],
                ['Health', 'Outside', 'No notes rule.'],
                ['Nested group', 'Prefixes join', 'Keep it shallow.'],
            ],
            'e1' => ['Example 1 — the joined path', 'The group is /notes, the child is /{id}, the caller uses /notes/4.', <<<'CS'
string prefix = "/notes";
string child = "/{id}";
string full = "/notes/4";
int status = 200;
Console.WriteLine(prefix);
Console.WriteLine(child);
Console.WriteLine(full);
Console.WriteLine(status);
CS],
            'e2' => ['Example 2 — health is not in the group', 'The notes filter does not see /health. The probe answers 200.', <<<'CS'
string path = "/health";
int inNotesGroup = 0;
int status = 200;
Console.WriteLine(path);
Console.WriteLine(inNotesGroup);
Console.WriteLine(status);
CS],
            'micro' => [
                'A trailing slash on the group and on the child can fail to join the way you expect. Pick one style.',
                'RequireAuthorization on the group is easy to forget you added. New routes inherit it, which is the point.',
                'An outer /v1 group is how you version without pasting v1 into every path.',
            ],
            'quiz' => [
                $q('What is the public path of MapGet("/{id}") inside MapGroup("/notes")?', '/notes/{id}', '/{id}', '/health/{id}', '/notes/notes'),
                $q('Who does a filter on the group apply to?', 'Every route in the group', 'Only the first route', 'Only /health', 'Only static files'),
                $q('Why keep health outside the notes group?', 'So notes rules are not applied to the probe', 'Health cannot return 200', 'Groups cannot be mapped', 'The port must be 0'),
            ],
            'practice' => 'Write the group prefix and the two child paths for list and read-one. Write the public path of each. Say where /health is registered.',
        ]),
        $L([
            'section' => 'Around the route',
            'title' => 'An endpoint filter wraps one route',
            'keywords' => 'endpoint filter, AddEndpointFilter, short circuit',
            'lead' => 'An endpoint filter is a function around one route, or around a group of routes. It runs after routing has chosen the endpoint. It can inspect the bound arguments, return a result early, or call the next filter and then the handler. A title check here returns 400 and the handler does not store anything.',
            'heading' => 'After the route is chosen, before the handler',
            'para' => 'This is the right size for a rule that belongs to notes and not to the whole process. The filter sees the record the binder produced. If Title is empty, it returns BadRequest and does not call next. If Title is present, it calls next and the handler stores the note. A filter on a group covers every route in the group. A filter on one MapPost covers only that create. Do not use a filter to replace authentication. Authentication already ran in the pipeline, where it can see every path.',
            'bullets' => [
                'Filters run only for the endpoint they were added to.',
                'Returning a result skips the handler.',
                'Calling next runs the handler, then comes back.',
                'A bad title is 400 here. An unknown caller is still 401 from auth, earlier.',
            ],
            'visual' => tf_split('dnfilt', 'Does the filter call next?', 'Title usable?', 'yes', 'handler stores it', 'no', '400, handler skipped', 'The route was already chosen. The filter decides whether the handler may run.'),
            'rows' => [
                ['Chosen route', 'Already true', 'Filters do not replace routing.'],
                ['Empty title', '400', 'next is not called.'],
                ['Usable title', 'next', 'The handler returns 201.'],
                ['One route', 'Add it there', 'Only create, if that is what you want.'],
                ['Whole group', 'Add it on the group', 'Every notes route.'],
                ['Authentication', 'Earlier', 'Not this filter.'],
            ],
            'e1' => ['Example 1 — the filter stops an empty title', 'The filter ran. The handler did not. Status 400.', <<<'CS'
int filterRan = 1;
int handlerRan = 0;
int stored = 0;
int status = 400;
Console.WriteLine(filterRan);
Console.WriteLine(handlerRan);
Console.WriteLine(stored);
Console.WriteLine(status);
CS],
            'e2' => ['Example 2 — the filter lets tea through', 'The filter ran, then the handler. Status 201.', <<<'CS'
int filterRan = 1;
int handlerRan = 1;
string title = "tea";
int status = 201;
Console.WriteLine(filterRan);
Console.WriteLine(handlerRan);
Console.WriteLine(title);
Console.WriteLine(status);
CS],
            'micro' => [
                'Filters can see the arguments by type. Ask for the create record, not for a raw body you parse again.',
                'A filter that always calls next is only a log. A filter that never calls next disables the route.',
                'Keep the filter thinner than the service. The service still owns the store rule. The filter can refuse the obvious empty title.',
            ],
            'quiz' => [
                $q('When does an endpoint filter run?', 'After the route is chosen and before the handler, if it calls next', 'Before any routing', 'After the process exits', 'Only for /health'),
                $q('What happens when the filter returns 400 and does not call next?', 'The handler does not store the note', 'The handler still stores it', 'The status becomes 201', 'Authentication is skipped next time'),
                $q('Why not put authentication in this filter alone?', 'Authentication belongs on the pipeline so every path is covered the same way', 'Filters cannot return a status', '401 is illegal', 'Groups cannot have routes'),
            ],
            'practice' => 'Describe the empty-title filter: the condition, the status, and whether the handler runs. Then describe the tea path.',
        ]),
        $L([
            'section' => 'Identity and settings',
            'title' => 'A record is the shape you return',
            'keywords' => 'response record, DTO, do not return the row',
            'lead' => 'The object you return is a promise to the client. A record with Id and Item is that promise. The database row may also have a private column, a timestamp, or a soft-delete flag. Those stay on the server. Mapping to a record is the moment you choose what leaves the process.',
            'heading' => 'Return the promise, not the storage',
            'para' => 'A create can return a smaller record than a read. A list can return the same item record repeated. An error returns the problem shape, not this record. If you return the storage type directly, a new column becomes part of the API the day you add it. The record makes that change visible in code review. Names in the record are the JSON names clients bind to. Rename them on purpose, in a new version, not by accident.',
            'bullets' => [
                'Id and Item are the notes promise in this course.',
                'Private columns stay off the record.',
                'List, read, and create can share the record or use a smaller one.',
                'Errors use the problem shape, not a half-filled note.',
            ],
            'visual' => tf_panels('dndto', 'What leaves the process', 'return', ['id', 'item', 'status beside it'], 'keep', ['private flag', 'storage detail', 'secrets'], 'The record is the allow-list of fields. Storage can be wider.'),
            'rows' => [
                ['Id', 'The identity', 'The path can use it.'],
                ['Item', 'The title', 'What the client showed.'],
                ['Row', 'Wider', 'Not automatically the body.'],
                ['New column', 'Not in the API', 'Until you add it to the record.'],
                ['Error', 'Problem shape', 'Not a note with empty fields.'],
                ['Version', 'When you rename', 'A new prefix, not a silent change.'],
            ],
            'e1' => ['Example 1 — the read promise', 'Id 4, item milk, status 200.', <<<'CS'
string id = "4";
string item = "milk";
int status = 200;
Console.WriteLine(id);
Console.WriteLine(item);
Console.WriteLine(status);
CS],
            'e2' => ['Example 2 — a private flag stays behind', 'The client sees the item. The private flag is not in the body. shown stays 0.', <<<'CS'
string item = "milk";
int privateShown = 0;
int status = 200;
Console.WriteLine(item);
Console.WriteLine(privateShown);
Console.WriteLine(status);
CS],
            'micro' => [
                'A null item on a 200 is a bug in the mapping. Do not send a note-shaped object to mean "missing". Send 404.',
                'Dates you do return should say whether they are UTC.',
                'The create response can be the same record the next GET would return, so the client need not guess.',
            ],
            'quiz' => [
                $q('What is the record you return?', 'The fields you promise the client', 'The entire storage row by default', 'The project file', 'The caller cookie'),
                $q('What should a missing note return instead of an empty record?', '404', '200 with empty strings', '201', 'The private flag'),
                $q('When does a new storage column become part of the API?', 'When you add it to the returned record', 'The moment the column exists', 'When the port changes', 'Never, columns cannot be read'),
            ],
            'practice' => 'List the fields a client gets for one note, and one storage field you would deliberately leave out.',
        ]),
        $L([
            'section' => 'Identity and settings',
            'title' => 'Authentication names the caller',
            'keywords' => 'authentication, 401, identity, ASP.NET Core',
            'lead' => 'Authentication answers "who is this?". A handler reads the credential the client sent, checks it, and builds an identity. If there is no usable credential, the answer is 401. The route does not run as that caller. 401 means unauthenticated. It does not mean the caller is known and forbidden. That is the next lesson.',
            'heading' => 'No identity, no name',
            'para' => 'UseAuthentication in the pipeline is what runs the handler. The handler you configure, such as a bearer token handler or a cookie handler, is the only thing that may turn a header into an identity. A header the client invented, with a name you did not configure, is not a credential. Do not log the token. Log that authentication failed, and return 401 with the problem shape. A health probe can stay anonymous if the balancer cannot present a credential, and then its body must stay dull.',
            'bullets' => [
                '401: we do not know who this is.',
                'The handler builds the identity. The route reads it.',
                'An unconfigured header is not a login.',
                'Authentication middleware runs before authorization and before the route.',
            ],
            'visual' => tf_split('dnauth', 'Did the handler accept a credential?', 'Usable credential?', 'yes', 'an identity exists', 'no', '401, no name', 'The route does not invent the name. The handler does.'),
            'rows' => [
                ['Authentication', 'Who', 'Builds an identity or fails.'],
                ['401', 'No identity', 'The client may try again with a credential.'],
                ['Handler', 'The checker', 'Token, cookie, or another scheme you configured.'],
                ['Invented header', 'Not a scheme', 'Ignore it as proof.'],
                ['Log', 'Failed, not the secret', 'Never the token value.'],
                ['Health', 'May be anonymous', 'Then the body stays dull.'],
            ],
            'e1' => ['Example 1 — no usable credential', 'known is 0. Status 401. The note is not returned.', <<<'CS'
int known = 0;
int status = 401;
string reason = "no caller";
Console.WriteLine(known);
Console.WriteLine(status);
Console.WriteLine(reason);
CS],
            'e2' => ['Example 2 — the handler accepted the credential', 'known is 1. The route may now ask the next question, which is whether this caller is allowed.', <<<'CS'
int known = 1;
string name = "ada";
int status = 200;
string item = "milk";
Console.WriteLine(known);
Console.WriteLine(name);
Console.WriteLine(status);
Console.WriteLine(item);
CS],
            'micro' => [
                '200 in the second example assumes the next lesson also allows ada. Authentication alone did not grant the note. It named her.',
                'A clock-skewed token is a failed authentication, so 401, not 500.',
                'Register the scheme on the builder. UseAuthentication on the app does nothing useful if no scheme was added.',
            ],
            'quiz' => [
                $q('What does authentication answer?', 'Who is calling', 'Whether the title is valid', 'Which port to use', 'Whether the note exists'),
                $q('Which status is no usable credential?', '401', '403', '400', '201'),
                $q('What may turn a header into an identity?', 'The authentication handler you configured', 'Any header the client chooses to send', 'The JSON title', 'The health route'),
            ],
            'practice' => 'Say what 401 means in one sentence, and name the component that is allowed to build the identity.',
        ]),
        $L([
            'section' => 'Identity and settings',
            'title' => 'Authorization decides if that caller may pass',
            'keywords' => 'authorization, 403, 401, RequireAuthorization',
            'lead' => 'Authorization answers "may this known caller do this?". It runs after authentication. If there is no identity, the answer is still 401. If there is an identity and the policy refuses, the answer is 403. The note is not returned in either case. The two numbers tell the client different next steps: send a credential, or stop, because this account may not.',
            'heading' => 'Known is not the same as allowed',
            'para' => 'UseAuthorization must be after UseAuthentication. A policy can require a role or a claim. RequireAuthorization on the notes group applies the policy to every notes route. Health can be left out. Do not check the role by reading a raw header inside the route. Read the identity authorization already built. A failed policy is 403, not 400. The body was fine. The caller was not permitted.',
            'bullets' => [
                '401: no identity. 403: identity, refused.',
                'The policy lives with the group or the route, not in a copied header read.',
                'UseAuthentication, then UseAuthorization, then the endpoint.',
                'A refused caller does not reach the store.',
            ],
            'visual' => tf_flow('dnauthz', 'Name, then permit, then the note', [['401', 'no name'], ['403', 'named, refused'], ['200', 'named and allowed']], 'Only the last branch returns the note.'),
            'rows' => [
                ['No identity', '401', 'Authentication failed or was skipped.'],
                ['Identity, policy no', '403', 'Do not store or return the note.'],
                ['Identity, policy yes', 'The route', '200 or 201 as usual.'],
                ['Order', 'Auth, then allow', 'Allow cannot run first.'],
                ['Group requirement', 'All notes routes', 'Health excluded on purpose.'],
                ['Raw header', 'Not the policy', 'The identity is the source.'],
            ],
            'e1' => ['Example 1 — ada is known and not allowed', 'known is 1, allowed is 0, status 403.', <<<'CS'
int known = 1;
int allowed = 0;
int status = 403;
string reason = "refused";
Console.WriteLine(known);
Console.WriteLine(allowed);
Console.WriteLine(status);
Console.WriteLine(reason);
CS],
            'e2' => ['Example 2 — ada is known and allowed', 'The route returns the note. Status 200.', <<<'CS'
int known = 1;
int allowed = 1;
string item = "milk";
int status = 200;
Console.WriteLine(known);
Console.WriteLine(allowed);
Console.WriteLine(item);
Console.WriteLine(status);
CS],
            'micro' => [
                'Do not hide 403 as 404 unless you have a written reason, such as not revealing that a private id exists. If you do that, do it on purpose and test it.',
                'A policy that always succeeds is not a policy. Require something you can name.',
                'Anonymous health plus authorized notes is a normal split. Put them in different groups.',
            ],
            'quiz' => [
                $q('Which status is a known caller the policy refuses?', '403', '401', '400', '201'),
                $q('Which status is no identity?', '401', '403', '200', '204'),
                $q('Where must UseAuthorization sit?', 'After UseAuthentication', 'Before UseAuthentication', 'After the response body is finished', 'Inside the project file only'),
            ],
            'practice' => 'Give the status for three callers: no credential, a credential with the wrong role, and a credential with the right role. Say whether the note is returned in each case.',
        ]),
        $L([
            'section' => 'Identity and settings',
            'title' => 'A claim is one fact on the identity',
            'keywords' => 'claim, identity, do not trust a client header',
            'lead' => 'A claim is one fact the authentication handler attached to the identity, such as a name or a role. Authorization policies read claims. They do not read a header the client added beside the credential. A header named role, sent by the client, is not a claim. If you treat it as one, the client picks their own permission.',
            'heading' => 'Facts come from the handler',
            'para' => 'After authentication, the identity has a list of claims. A role claim of reader might allow GET and refuse POST. That decision is the policy. The route can read the name claim to log who created a note. It must not log the credential that produced the claims. If a token is replaced or expired, the handler refuses it and there is no identity, so there are no claims. You do not keep yesterday\'s role because a header still says it.',
            'bullets' => [
                'A claim is attached by the handler after a successful check.',
                'A client header with the same name is a different value. Ignore it as proof.',
                'Policies use claims. Routes may read the name. Neither re-checks the raw token.',
                'No identity means no claims. Do not fall back to a header.',
            ],
            'visual' => tf_panels('dnclaim', 'Two values named role', 'on the identity', ['from the handler', 'policy reads this', 'reader'], 'on a raw header', ['the client wrote it', 'not a claim', 'ignore as proof'], 'Same word, different trust. Only the identity counts.'),
            'rows' => [
                ['Claim', 'One fact', 'Name, role, or another you configured.'],
                ['Handler', 'The writer', 'Only after the credential checks out.'],
                ['Policy', 'The reader', 'May this role POST?'],
                ['Client header', 'Untrusted', 'Not a claim.'],
                ['Name claim', 'Safe to log', 'The token is not.'],
                ['No identity', 'No claims', '401, not a guessed role.'],
            ],
            'e1' => ['Example 1 — the role on the identity allows a read', 'The role is reader. GET is allowed. Status 200.', <<<'CS'
string role = "reader";
string source = "identity";
string verb = "GET";
int status = 200;
Console.WriteLine(role);
Console.WriteLine(source);
Console.WriteLine(verb);
Console.WriteLine(status);
CS],
            'e2' => ['Example 2 — a header the client wrote is not the role', 'The header says admin. The identity says reader. The policy uses reader, and POST is refused.', <<<'CS'
string headerRole = "admin";
string identityRole = "reader";
string used = "identity";
int status = 403;
Console.WriteLine(headerRole);
Console.WriteLine(identityRole);
Console.WriteLine(used);
Console.WriteLine(status);
CS],
            'micro' => [
                'If you need a new fact, put it in the token or the cookie through the handler. Do not ask the client to send it in a second header.',
                'A role of reader that can POST is a policy bug. Test GET and POST separately.',
                'Claim types are strings. Compare them to constants you defined, not to whatever the body contained.',
            ],
            'quiz' => [
                $q('Who writes a claim?', 'The authentication handler, after the credential checks out', 'Any client header', 'The JSON title', 'The health probe'),
                $q('Which role does the policy use when the header says admin and the identity says reader?', 'reader', 'admin', 'Both', 'Neither, it returns 201'),
                $q('What is true when authentication produced no identity?', 'There are no claims to trust', 'The header role still applies', 'The note is created', '403 becomes 200'),
            ],
            'practice' => 'A client sends a header role=admin and a token whose identity says reader. Which value allows or refuses POST, and what status is a refused POST?',
        ]),
    ];
}
