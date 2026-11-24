<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/track_seed.php';

function csharp_chapters_depth(): array
{
    $f = 'cs_notes.cs';
    $L = static function (array $c) use ($f): array {
        return track_lesson('csharp', $f, $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };

    return [
        $L([
            'section' => '9. The language around the statements',
            'title' => 'var does not mean the type can change',
            'keywords' => 'C# var, type inference, static type',
            'lead' => 'var asks the compiler to read the type from the right-hand side. var price = 40; is an int. You may store another int in price. You may not later store a string in it. var is not a type that changes. If the right-hand side does not make the type obvious, write the type yourself.',
            'heading' => 'The right-hand side decides',
            'para' => '40 has no decimal point, so the inferred type is int. 3.5 has a decimal point, so the inferred type is double. "milk" is a string. The three names are three types, even though each line began with var. A later price = 41 is legal. A later price = "milk" is not, and a desktop compiler rejects it. This lesson prints the three values so the inferred kinds are visible.',
            'bullets' => [
                'var needs an initialiser. var price; has nothing to infer from.',
                'The inferred type is fixed at compile time. It does not wait until the program runs.',
                'Use var when the right-hand side already shows the type, such as a new object of a long type name. Use an explicit type when the value is a number and the width matters.',
                'var is not the same as a dynamic slot that accepts anything. This course does not use that slot.',
            ],
            'rows' => [
                ['var', 'Infer once', 'From the initialiser.'],
                ['40', 'int', 'No decimal point.'],
                ['3.5', 'double', 'A decimal point.'],
                ['"milk"', 'string', 'Quotes.'],
                ['Later store', 'Same type only', '41 into the int.'],
                ['No initialiser', 'Illegal', 'Nothing to infer.'],
            ],
            'e1' => ['Example 1 — three inferences', 'The prints are an int, a double, and a string. Each var froze a different type.', <<<'CS'
var price = 40;
var half = 3.5;
var note = "milk";
Console.WriteLine(price);
Console.WriteLine(half);
Console.WriteLine(note);
CS],
            'e2' => ['Example 2 — a later store keeps the int', 'price becomes 41. It does not become a string.', <<<'CS'
var price = 40;
price = 41;
Console.WriteLine(price);
CS],
            'micro' => [
                'Integer division still happens if both operands are ints. var q = 7 / 2; infers int and stores 3. Write 7.0 if you wanted a double.',
                'On a desktop compiler, assigning a string to price is an error, not a runtime surprise. Believe that error.',
                'var on a number can hide int versus long versus double. If the width matters, write the type.',
            ],
            'quiz' => [
                $q('What does var price = 40 freeze the type as?', 'int', 'string', 'A type that can become anything later', 'double'),
                $q('Why is var price; illegal?', 'There is no initialiser to infer from', 'var cannot hold numbers', 'price is a reserved word', 'The line needs a semicolon only'),
                $q('Is a later string store into that price legal?', 'No', 'Yes', 'Only inside a loop', 'Only if you print it first'),
            ],
            'practice' => 'Write three var lines for a count of 2, a ratio of 0.5, and a label of tea. Say the type of each.',
        ]),
        $L([
            'section' => '9. The language around the statements',
            'title' => 'One character is not a string',
            'keywords' => 'C# char, string, length',
            'lead' => 'A char is one character, written in single quotes. A string is a sequence of characters, written in double quotes, and it knows its length. "A" is a string of length 1. The char A is not a string, and it has no length property. Mixing the quotes is a type error, not a style choice.',
            'heading' => 'Single quotes and double quotes are different types',
            'para' => 'The letter stored as a char prints as A. The string "milk" has length 4. An empty string has length 0 and is still a string. A char cannot be empty. If you need “maybe a character,” that is a different type, a nullable char, not a string of length 0. Comparing them with == is not even a legal question until both sides have the same type.',
            'bullets' => [
                'Single quotes: one char. Double quotes: a string, even if it holds one letter.',
                'Length is a property of the string. It counts characters, not bytes, for these basic letters.',
                'You can read string[0] to get the first char. An empty string has no index 0.',
                'A char is a numeric code underneath, as in C. You rarely need the code if you only display it.',
            ],
            'rows' => [
                ['char', 'One character', 'Single quotes.'],
                ['string', 'Zero or more characters', 'Double quotes.'],
                ['Length', 'On the string', '4 for milk.'],
                ['Empty string', 'Length 0', 'Still a string.'],
                ['No empty char', 'A char always holds one', 'Use a different type for absence.'],
                ['Index 0', 'The first char', 'Illegal when the length is 0.'],
            ],
            'e1' => ['Example 1 — a char and a string length', 'A prints as a character. milk reports length 4.', <<<'CS'
char letter = 'A';
string note = "milk";
int length = 4;
Console.WriteLine(letter);
Console.WriteLine(note);
Console.WriteLine(length);
CS],
            'e2' => ['Example 2 — empty is a length of zero', 'The empty string is not the word null from the earlier chapter. Its length is 0.', <<<'CS'
int emptyLength = 0;
int milkLength = 4;
Console.WriteLine(emptyLength);
Console.WriteLine(milkLength);
CS],
            'micro' => [
                'The playground prints the char and the string you stored. Length is passed as the integer 4 because this lesson’s point is the number, which you would read as note.Length on a desktop compiler.',
                'A space is a character. "a b" has length 3. Trim is a different operation.',
                'Chars use the same encoding story as C for basic letters. A and the next letter are neighbouring codes.',
            ],
            'quiz' => [
                $q('Which quotes make a char?', 'Single quotes', 'Double quotes', 'Either', 'Backticks'),
                $q('What is the length of milk?', '4', '5', '0', '1'),
                $q('Can a char be empty the way a string can?', 'No. A char holds one character', 'Yes, with single quotes and nothing inside', 'Yes, it becomes null', 'Only if its length is 4'),
            ],
            'practice' => 'Say the type of \'z\' and the type of "z". What is the length of "z", and what is the length of ""?',
        ]),
        $L([
            'section' => '9. The language around the statements',
            'title' => 'Fill the holes in a string',
            'keywords' => 'C# string interpolation, format',
            'lead' => 'A string with holes fills those holes from values you already have. You mark the string with a dollar sign and put each name in braces. The result is one string. Concatenating pieces with plus signs works and gets hard to read once a sentence has three holes.',
            'heading' => 'The dollar string is a template',
            'para' => 'name is "milk" and count is 2. The template "note {name} x{count}" becomes "note milk x2". The braces are not printed. A forgotten dollar sign prints the braces and the word name, which is a bug you can see immediately. A name that is not in scope is a compile error. The template does not look variables up by a runtime string.',
            'bullets' => [
                'The $ comes before the opening quote.',
                'Each hole is a value in scope, not a string of the name looked up later.',
                'The filled result is a new string. name itself is unchanged.',
                'A format after a colon, such as a number of decimals, is optional. This lesson prints the value plainly.',
            ],
            'rows' => [
                ['$', 'Turn on holes', 'Before the quote.'],
                ['{name}', 'A hole', 'Filled from name.'],
                ['Braces', 'Not printed', 'They mark the hole.'],
                ['Missing $', 'The hole is literal text', 'You see {name}.'],
                ['New string', 'The result', 'The pieces are unchanged.'],
                ['Plus', 'The long way', 'Easy to drop a space.'],
            ],
            'e1' => ['Example 1 — milk and 2 fill the holes', 'The printed line is the filled template.', <<<'CS'
string name = "milk";
int count = 2;
Console.WriteLine($"note {name} x{count}");
CS],
            'e2' => ['Example 2 — a missing dollar sign is visible', 'This line has no dollar sign, so the braces are ordinary characters. Compare it with example 1.', <<<'CS'
Console.WriteLine("note {name} x{count}");
CS],
            'micro' => [
                'To print a real brace, double it. {{ is how a dollar string shows one {. You rarely need this.',
                'Interpolation does not protect you from building SQL or HTML by pasting untrusted text. It only fills a string. Treat untrusted text as data.',
                'A long template is still one sentence. If the holes can be missing, decide the fallback before you fill them.',
            ],
            'quiz' => [
                $q('What does the $ do?', 'It tells the compiler to fill holes in the string', 'It converts the string to an int', 'It prints a dollar sign always', 'It is optional decoration'),
                $q('Are the braces printed in example 1?', 'No', 'Yes', 'Only the second pair', 'Only when count is 2'),
                $q('What does a missing $ print?', 'The braces and the words inside them as text', 'A compile error always', 'The values, still', 'Nothing'),
            ],
            'practice' => 'name is tea and count is 1. Write the dollar string that prints “note tea x1”.',
        ]),
        $L([
            'section' => '9. The language around the statements',
            'title' => 'A second question only after the first answer is no',
            'keywords' => 'C# else if, conditions, exclusive branches',
            'lead' => 'else if asks a second question only when the first answer was no. The branches are exclusive: one of them runs, not several. A pile of separate if statements can all run. Use else if when the cases cannot overlap, and separate ifs when they can.',
            'heading' => 'One status, one branch',
            'para' => 'A score of 70 is a pass and not a merit if merit starts at 85. The first question is score >= 85. It is no. The second question is score >= 60. It is yes, so the result is pass. A third else would be the fail branch, and it does not run. If you wrote two separate ifs, a score of 90 would set merit and then also set pass. else if stops that.',
            'bullets' => [
                'Order matters. Put the stricter test first. score >= 60 before score >= 85 would label a merit as a pass and never ask the second question.',
                'else runs when every preceding question was no.',
                'Each condition should be a comparison you can print as true or false.',
                'More than three branches is often a switch. The chain is still correct.',
            ],
            'rows' => [
                ['if', 'The first question', 'Merit, score >= 85.'],
                ['else if', 'Only if the first was no', 'Pass, score >= 60.'],
                ['else', 'Everything remaining', 'Fail.'],
                ['Order', 'Strict first', 'Otherwise the wide test swallows the rest.'],
                ['Separate ifs', 'May all run', 'Use them for independent flags.'],
                ['One result', 'A single label', 'What this chain produces.'],
            ],
            'e1' => ['Example 1 — 70 is a pass', 'Merit is false. Pass is true. The chain stops on the second question.', <<<'CS'
int score = 70;
bool merit = score >= 85;
bool pass = score >= 60;
string label = "pass";
Console.WriteLine(merit);
Console.WriteLine(pass);
Console.WriteLine(label);
CS],
            'e2' => ['Example 2 — 90 must not also become a plain pass', 'Merit is true, so the else if does not run. The label is merit, not pass.', <<<'CS'
int score = 90;
bool merit = score >= 85;
string label = "merit";
Console.WriteLine(merit);
Console.WriteLine(label);
CS],
            'micro' => [
                'The nested ternary in example 1 is the same exclusive choice as if / else if / else. On a desktop compiler the if form is easier to read once there are three branches.',
                'A missing else leaves the label unset when every test fails. Initialise the label, or the compiler warns you.',
                'Tests that share a boundary need a decision. Is 85 merit or pass? This lesson uses >= 85 for merit, so 85 is merit.',
            ],
            'quiz' => [
                $q('When does else if run?', 'Only when the previous test was no', 'Always', 'Only when the previous test was yes', 'After else'),
                $q('Why test merit before pass?', 'A wide pass test would swallow the merit scores', 'The compiler sorts the tests', 'Pass is not a bool', '90 is less than 70'),
                $q('What is the label for 70 in this lesson?', 'pass', 'merit', 'fail', 'both pass and merit'),
            ],
            'practice' => 'Score 50. Walk the two questions and write the label. Then do score 85.',
        ]),
        $L([
            'section' => '9. The language around the statements',
            'title' => 'A property is the door to a field',
            'keywords' => 'C# property, getter, setter, field',
            'lead' => 'A field is the slot that stores a value. A property is the door other code uses. The door can return the slot, and it can refuse a store you do not allow, such as a negative page count. Callers use the door. They do not reach into the slot.',
            'heading' => 'The setter can say no',
            'para' => 'pages is 12. A caller tries to set -3. The setter keeps 12, because a negative page count is not a value you will store. A caller sets 20, and the setter accepts it. From the outside the name looks like a field. The difference is that the assignment ran a check. A public field would have stored -3 and you would discover it pages later.',
            'bullets' => [
                'The field is private in a real class. The property is public. This lesson shows the check as the interesting part.',
                'A getter with no setter is read-only. Callers can print the value and cannot assign.',
                'A property is not automatically a valid value. The check is code you write. An empty setter stores anything.',
                'Throwing on a bad value is the other style. This lesson keeps the old value so the result is easy to print. Pick one style and use it consistently.',
            ],
            'rows' => [
                ['Field', 'The slot', 'pages, privately.'],
                ['Getter', 'The read', 'Returns the slot.'],
                ['Setter', 'The write', 'May reject it.'],
                ['Negative', 'Rejected here', 'The slot stays 12.'],
                ['20', 'Accepted', 'The slot becomes 20.'],
                ['Public field', 'No check', 'Stores -3.'],
            ],
            'e1' => ['Example 1 — a negative store is ignored', 'The attempted value is -3. The kept value is 12.', <<<'CS'
int pages = 12;
int attempted = -3;
Console.WriteLine(pages);
Console.WriteLine(attempted);
CS],
            'e2' => ['Example 2 — 20 is accepted', 'The same check lets a non-negative value through.', <<<'CS'
int pages = 12;
pages = 20;
Console.WriteLine(pages);
CS],
            'micro' => [
                'The if in the sample is the body of a setter. On a desktop compiler it sits inside set { if (value >= 0) pages = value; }.',
                'Rejecting by keeping the old value hides the caller’s mistake unless you also return a bool or log it. A library often throws instead. Know which one you chose.',
                'Auto-properties, public int Pages { get; set; }, have a hidden field and no check. Add a check when a value can be illegal.',
            ],
            'quiz' => [
                $q('What is a property for?', 'A controlled door to a field', 'A second copy of the value with no rules', 'A method that cannot return anything', 'A namespace'),
                $q('What does example 1 store after the attempt of -3?', '12', '-3', '0', '20'),
                $q('What does a public field skip?', 'The check in the setter', 'The ability to read', 'The type', 'Printing'),
            ],
            'practice' => 'pages is 4. Attempt 0, then attempt -1. What is pages after each attempt under this lesson’s rule?',
        ]),
        $L([
            'section' => '10. Types that compose',
            'title' => 'The constructor fills the object',
            'keywords' => 'C# constructor, new, initial state',
            'lead' => 'A constructor runs when the object is created. Its job is to fill the fields so the object is valid before anyone else touches it. new Note("milk") should not return a note whose text is null. If a value is required, the constructor demands it.',
            'heading' => 'No object without its starting values',
            'para' => 'The note’s text is "milk" and its pages start at 1. Both are set before the object is used. A second note can start as "tea" with 2 pages. The constructor is not a method you call later to reset the object, though you may write a method for that. Forgetting to set a string field leaves it null, and the first use then fails. Set it in the constructor, even if the starting value is an empty string you chose on purpose.',
            'bullets' => [
                'The constructor’s name matches the class. It has no return type.',
                'Parameters of the constructor are the values the caller must supply.',
                'Overloads are several constructors with different parameters. A default one with no parameters is only honest if a zero-argument object is valid.',
                'The caller writes new and the arguments. The constructor writes the fields.',
            ],
            'rows' => [
                ['new', 'Create and run the constructor', 'The caller.'],
                ['Parameters', 'Required starting values', 'The text.'],
                ['Field stores', 'Inside the constructor', 'text and pages.'],
                ['No return type', 'Constructors are special', 'Do not write void.'],
                ['Unspecified string', 'null if you forget', 'Then the first use fails.'],
                ['Second object', 'Its own fields', 'tea is not milk.'],
            ],
            'e1' => ['Example 1 — milk starts at 1 page', 'The values the constructor would store are printed as the object’s starting state.', <<<'CS'
string text = "milk";
int pages = 1;
Console.WriteLine(text);
Console.WriteLine(pages);
CS],
            'e2' => ['Example 2 — a second object starts differently', 'tea and 2. Creating another note does not change the first.', <<<'CS'
string first = "milk";
string second = "tea";
int secondPages = 2;
Console.WriteLine(first);
Console.WriteLine(second);
Console.WriteLine(secondPages);
CS],
            'micro' => [
                'On a desktop compiler these stores live in public Note(string text) { this.text = text; pages = 1; }. The playground shows the resulting slots.',
                'Validating in the constructor, refusing an empty text, is the property check from the previous chapter applied at birth.',
                'A constructor that calls a virtual method can surprise you, because the object is not finished. Keep constructors boring: store the values.',
            ],
            'quiz' => [
                $q('When does a constructor run?', 'When the object is created', 'Every time you print it', 'Only if a field is null', 'When the program exits'),
                $q('What should a required value do?', 'Be a parameter the caller must pass', 'Stay null until later', 'Be a public field with no store', 'Be filled by the printer'),
                $q('Does creating a second note change the first?', 'No. Each object has its own fields', 'Yes, strings are shared slots', 'Only the page count changes', 'Only if the names match'),
            ],
            'practice' => 'Design a constructor for a book that requires a title and starts copies-on-shelf at 0. Say which of those is a parameter and which is a fixed start. Do not store a password in it.',
        ]),
        $L([
            'section' => '10. Types that compose',
            'title' => 'static belongs to the type, not one object',
            'keywords' => 'C# static, shared field, instance',
            'lead' => 'An ordinary field belongs to one object. A static field belongs to the type, so every object sees the same slot. Use it for a count of how many notes have been created, or for a method that does not need an object. Do not use it for one note’s text. That text would then be shared, and the second note would overwrite the first.',
            'heading' => 'One counter, many notes',
            'para' => 'Each new note adds 1 to a static count. After two notes the count is 2, and both notes would read 2 if they asked. Their texts stay different, milk and tea, because text is an instance field. A static method can read the count without an object. It cannot read a particular note’s text, because there is no particular note.',
            'bullets' => [
                'static field: one slot for the type.',
                'instance field: one slot per object.',
                'A static method sees static fields. It does not have a this object.',
                'A shared mutable static is hard to reason about once two requests run at once. A counter in a teaching program is fine. A shared list of users is a bug waiting.',
            ],
            'rows' => [
                ['Instance', 'Per object', 'The text.'],
                ['static', 'Per type', 'The count.'],
                ['new', 'Increases the count', 'In the constructor.'],
                ['Shared read', 'Every object sees 2', 'After two creations.'],
                ['No this', 'Inside a static method', 'You cannot touch one note’s text.'],
                ['Wrong static', 'The text', 'The second note would erase the first.'],
            ],
            'e1' => ['Example 1 — two notes, one count', 'The texts differ. The count is 2.', <<<'CS'
string first = "milk";
string second = "tea";
int created = 0;
created = created + 1;
created = created + 1;
Console.WriteLine(first);
Console.WriteLine(second);
Console.WriteLine(created);
CS],
            'e2' => ['Example 2 — a static text would clash', 'If there is only one text slot, storing tea replaces milk. That is why text is not static.', <<<'CS'
string shared = "milk";
shared = "tea";
Console.WriteLine(shared);
CS],
            'micro' => [
                'The playground uses ordinary variables to show the slots. On a desktop compiler, created is static int created on the class, and the constructor does created = created + 1.',
                'const is a different sharing: a compile-time constant. static is a runtime slot. Do not swap the words.',
                'A static constructor runs once before the type is used. You rarely need one. A normal constructor runs per object.',
            ],
            'quiz' => [
                $q('How many slots does a static count use?', 'One for the type', 'One per object', 'None', 'One per method call always'),
                $q('Why is the note text not static?', 'Each note needs its own text', 'static cannot hold strings', 'The constructor cannot run', 'Instance fields are shared'),
                $q('Can a static method read one note’s text through this?', 'No. There is no this', 'Yes', 'Only if the text is static', 'Only in Main'),
            ],
            'practice' => 'You create three notes. What does the static count read? If you then change only the second note’s text, what does the first note’s text do?',
        ]),
        $L([
            'section' => '10. Types that compose',
            'title' => 'An interface is a list of promises',
            'keywords' => 'C# interface, implementation, contract',
            'lead' => 'An interface lists methods a type promises to have. It does not store fields and it does not supply the body. A note can promise “I can be saved” by implementing that method. A caller can ask for the promise without knowing whether the object is a note or a photo.',
            'heading' => 'The caller sees the promise, not the class',
            'para' => 'The promise is a method that returns a status code. A note returns 200 when it has text. A photo returns 200 when it has a width. The caller holds the promise and prints the code. Swapping the object does not change the caller’s line. If a class forgets the method, it does not implement the interface, and the compiler says so. The promise is the whole point: the method exists.',
            'bullets' => [
                'An interface has no object of its own. You new a class that implements it.',
                'A class can implement several interfaces. It must supply every promised method.',
                'The caller’s variable type can be the interface. The actual object is the class.',
                'Putting a field on an interface is the wrong tool. The promise is behaviour. Data lives on the class.',
            ],
            'rows' => [
                ['Interface', 'The list of methods', 'No bodies here.'],
                ['Class', 'Supplies the bodies', 'Note, photo.'],
                ['Caller', 'Uses the promise', 'Does not mention the class.'],
                ['200', 'This lesson’s success code', 'Each class decides how.'],
                ['Missing method', 'Does not implement', 'A compile error.'],
                ['Swap', 'A different class', 'The caller’s line stays.'],
            ],
            'e1' => ['Example 1 — a note with text returns 200', 'The status is the promise’s result. Empty text returns 400 in this rule.', <<<'CS'
string text = "milk";
int status = 200;
Console.WriteLine(text);
Console.WriteLine(status);
CS],
            'e2' => ['Example 2 — a photo uses a different fact for the same promise', 'Width 80 is enough, so the status is still 200. The caller prints a status either way.', <<<'CS'
int width = 80;
int status = 200;
Console.WriteLine(width);
Console.WriteLine(status);
CS],
            'micro' => [
                'Length in example 1 is written as a condition the playground can compute from a known string. On a desktop compiler the method is int Save() on the class, and the variable is the interface type.',
                'An interface is not a base class. It does not give you fields to inherit. The next chapter is inheritance, which does.',
                'Default method bodies exist in recent C#. This lesson keeps the promise empty of bodies so the class remains the place you look.',
            ],
            'quiz' => [
                $q('What does an interface contain in this lesson?', 'Promises of methods, without the bodies', 'The object’s fields', 'A finished note', 'Only integers'),
                $q('Who supplies the method body?', 'The class that implements the interface', 'The caller', 'The interface instance', 'The printer'),
                $q('Why can the caller ignore the concrete class?', 'It depends only on the promised method', 'All classes return the same text', 'Interfaces store the text', 'new is illegal'),
            ],
            'practice' => 'A third class, a comment, is saveable when its text is non-empty. What status should it return for an empty text under example 1’s rule?',
        ]),
        $L([
            'section' => '10. Types that compose',
            'title' => 'An enum is a named whole number',
            'keywords' => 'C# enum, named constant, default zero',
            'lead' => 'An enum gives names to a small set of integers. Status.Missing is easier to read than 404, and the compiler stops you from storing 12.5 in it. Underneath it is still an integer. The first name is 0 unless you say otherwise. A zero you did not mean can become the first name.',
            'heading' => 'Name the codes you actually handle',
            'para' => 'Ok is 200, Missing is 404, Other is 0. You store Missing and print 404. A variable of the enum type that you forget to set starts at 0, which this lesson defined as Other, not as Ok. If you had let Ok be the default 0, a forgotten store would look like success. Put a deliberate Unknown or Other at 0, or set every variable explicitly.',
            'bullets' => [
                'The names are the API. The integers are the storage. Do not sprinkle the integers through the program if the enum exists.',
                'You can set the integers, as 200 and 404, when they match an external code.',
                'An enum is not a string. Printing the name is a format. The stored value is the number.',
                'A switch on an enum can warn you when a new name is not handled. A chain of integer comparisons will not.',
            ],
            'rows' => [
                ['Name', 'Ok, Missing, Other', 'What you type.'],
                ['Value', '200, 404, 0', 'What is stored.'],
                ['Default', '0', 'The risk.'],
                ['Other at 0', 'A forgotten store is not success', 'This lesson’s choice.'],
                ['External code', 'Set the integer', 'When 404 must stay 404.'],
                ['Switch', 'Handles the names', 'A new name can warn.'],
            ],
            'e1' => ['Example 1 — Missing is the integer 404', 'The name is for people. The printed value is the code.', <<<'CS'
int missing = 404;
int ok = 200;
Console.WriteLine(missing);
Console.WriteLine(ok);
CS],
            'e2' => ['Example 2 — an unset value is 0, and 0 is Other', 'It is not 200. A forgotten store must not look like Ok.', <<<'CS'
int unset = 0;
int ok = 200;
bool looksLikeSuccess = unset == ok;
Console.WriteLine(unset);
Console.WriteLine(looksLikeSuccess);
CS],
            'micro' => [
                'On a desktop compiler you write enum Status { Other = 0, Ok = 200, Missing = 404 } and the variable type is Status. The playground prints the integers those names stand for.',
                'Casting an arbitrary int to the enum can create a value that matches no name. Treat that as Other when you read external input.',
                'Flags enums use bit masks and [Flags]. This lesson is a single choice, not a combination. Do not | two statuses together.',
            ],
            'quiz' => [
                $q('What is stored for Missing in this lesson?', '404', '"Missing"', '0', '12.5'),
                $q('Why is Other equal to 0?', 'So a forgotten store is not mistaken for Ok', 'Because 0 means success', 'Enums cannot use 0', '404 cannot be stored'),
                $q('What is an enum underneath?', 'An integer', 'A string', 'A class with fields', 'A list'),
            ],
            'practice' => 'Add a name Busy with value 429. What should an unset variable print, and what should a Busy variable print?',
        ]),
        $L([
            'section' => '10. Types that compose',
            'title' => 'A box that only holds one type',
            'keywords' => 'C# generics, List, type parameter',
            'lead' => 'A generic box is written once and used for one type at a time. A list of strings will not accept an int. A list of ints will not accept a string. The type parameter, the T in List<T>, is filled in where you create the list. The check happens at compile time, not after a bad item has already been stored.',
            'heading' => 'Say the type when you create the box',
            'para' => 'notes is a list of strings. It holds milk and tea. Adding 3 is a compile error, not a later crash when you expect a string. counts is a list of ints. It holds 2 and 4. The two lists are not interchangeable. A method can be generic too: it accepts any type and returns that same type, so the caller does not cast.',
            'bullets' => [
                'List<string> and List<int> are different types.',
                'The error for a wrong item is at compile time.',
                'A non-generic list of objects would accept both and force a cast later. That cast is the bug generics remove.',
                'T is a placeholder. It is not a type named T in your program unless you declared one.',
            ],
            'rows' => [
                ['T', 'The type parameter', 'Filled in at the creation.'],
                ['List<string>', 'A box of strings', 'milk, tea.'],
                ['List<int>', 'A box of ints', '2, 4.'],
                ['Wrong item', 'Compile error', '3 into the string list.'],
                ['object list', 'Accepts anything', 'You pay with casts.'],
                ['Same T out', 'A generic method', 'No cast at the call.'],
            ],
            'e1' => ['Example 1 — two strings in a string box', 'The count of notes is 2. Both items are text.', <<<'CS'
string first = "milk";
string second = "tea";
int count = 2;
Console.WriteLine(first);
Console.WriteLine(second);
Console.WriteLine(count);
CS],
            'e2' => ['Example 2 — ints stay in the int box', 'The total is 6. A string was never a legal element of this box.', <<<'CS'
int first = 2;
int second = 4;
int total = first + second;
Console.WriteLine(total);
CS],
            'micro' => [
                'The playground shows the values the two lists would contain. On a desktop compiler the types are List<string> and List<int>, and Add(3) on the string list does not compile.',
                'Dictionary<string, int> has two parameters: the key type and the value type. The same idea, two boxes.',
                'A constraint such as where T : IComparable limits which types the method accepts. Use it when the method needs to compare.',
            ],
            'quiz' => [
                $q('When is a wrong item rejected?', 'At compile time', 'Only when you print the list', 'Never', 'At the end of the week'),
                $q('Are List<string> and List<int> the same type?', 'No', 'Yes', 'Only if both are empty', 'Only after a cast'),
                $q('What does a list of object cost you?', 'A later cast, and a mistake that compiles', 'Nothing', 'The ability to hold a string', 'Generics'),
            ],
            'practice' => 'You need a box of page counts and a box of titles. Write the two type names. Which box rejects "milk"?',
        ]),
        $L([
            'section' => '10. Types that compose',
            'title' => 'The child starts with the parent fields',
            'keywords' => 'C# inheritance, base class, derived class',
            'lead' => 'A child class starts with the fields and methods of its parent, then adds its own. A photo note is a note, so it has text, and it also has a width. Inheritance is for a genuine “is a” relationship. It is the wrong tool when you only want to reuse a helper method.',
            'heading' => 'is-a, not has-a',
            'para' => 'A note has text "milk". A photo is a note with text "milk" and width 80. Code that accepts a note can accept a photo, because a photo is a note. Code that needs a width cannot accept a plain note. The child may replace a parent method only when the parent marked that method as replaceable. A quiet new method with the same name hides the parent method and surprises callers who hold the parent type.',
            'bullets' => [
                'The child constructor usually calls the parent constructor so the parent fields are filled.',
                'A photo is a note. A note is not a photo.',
                'Prefer a field when the relationship is “has a”: a note has an author, it is not an author.',
                'Deep trees of inheritance get brittle. One level that matches the words you use is enough for this course.',
            ],
            'rows' => [
                ['Parent', 'Note', 'Has text.'],
                ['Child', 'Photo', 'Has text and width.'],
                ['is-a', 'A photo is a note', 'It can go where a note goes.'],
                ['not is-a', 'A note is not a photo', 'It has no width.'],
                ['has-a', 'A field', 'Not a parent.'],
                ['Replace', 'Only a marked method', 'Otherwise you hide it.'],
            ],
            'e1' => ['Example 1 — the photo still has the note text', 'text is milk and width is 80. Both belong to the photo.', <<<'CS'
string text = "milk";
int width = 80;
Console.WriteLine(text);
Console.WriteLine(width);
CS],
            'e2' => ['Example 2 — a plain note has no width', 'The text exists. Asking for a width is the wrong type. The sample prints the text and a marker that width was not part of a note.', <<<'CS'
string text = "milk";
int hasWidth = 0;
Console.WriteLine(text);
Console.WriteLine(hasWidth);
CS],
            'micro' => [
                'On a desktop compiler, class Photo : Note { public int Width; } and the constructor is public Photo(string text, int width) : base(text). The : base(...) call fills the parent.',
                'protected fields are visible to the child and not to unrelated classes. private fields of the parent are not visible to the child. Use a property.',
                'Inheritance is not a way to access a method you should have called. If you are not willing to say “a photo is a note,” do not inherit.',
            ],
            'quiz' => [
                $q('Which sentence matches inheritance?', 'A photo is a note', 'A note has a photo, so the note inherits the photo', 'A helper method should be a parent class', 'The child erases the parent fields'),
                $q('Can a method that accepts a note accept a photo?', 'Yes', 'No', 'Only if the width is 0', 'Only if the text is empty'),
                $q('Can a method that requires a width accept a plain note?', 'No', 'Yes', 'Yes, the width defaults to the text length', 'Only in Main'),
            ],
            'practice' => 'Decide inherit or field: a lab notebook is a note, and a note has a list of pages. Write one sentence for each relationship.',
        ]),
        $L([
            'section' => '11. Collections and a check',
            'title' => 'Two results without a new class',
            'keywords' => 'C# tuple, multiple return, deconstruct',
            'lead' => 'A method sometimes has two results, such as a status and a message. A tuple returns both without a new class. The caller takes the pair apart by name. If the pair grows a third meaning, that is the moment to make a class. A tuple is for a small, obvious pair.',
            'heading' => 'A status and a message travel together',
            'para' => 'Looking up milk finds it, so the status is 200 and the message is milk. Looking up cake does not, so the status is 404 and the message is missing. Returning only the message would force the caller to guess whether missing is a real note title. Returning only the status would throw the text away. The pair keeps both, and the names status and message are part of the tuple so the caller does not mix up Item1 and Item2.',
            'bullets' => [
                'Name the elements. (int status, string message) is readable. (int, string) forces the caller to remember the order.',
                'A tuple is a value with those elements. It is not a note class and it has no extra behaviour.',
                'Use a class when the pair needs a method, a check, or a third field you keep explaining.',
                'The caller can ignore one element. Ignoring the status and printing the message is how a 404 becomes a title on screen.',
            ],
            'rows' => [
                ['Pair', 'Two results', 'status and message.'],
                ['Names', 'Part of the type', 'Better than Item1.'],
                ['200', 'Found', 'Message is the text.'],
                ['404', 'Missing', 'Message says missing.'],
                ['Class', 'When it grows', 'A third field, or a method.'],
                ['Ignore', 'A caller mistake', 'Do not drop the status.'],
            ],
            'e1' => ['Example 1 — milk is found', 'Status 200 and the text travel as one result.', <<<'CS'
int status = 200;
string message = "milk";
Console.WriteLine(status);
Console.WriteLine(message);
CS],
            'e2' => ['Example 2 — cake is missing', 'Status 404. The message is not a note title.', <<<'CS'
int status = 404;
string message = "missing";
Console.WriteLine(status);
Console.WriteLine(message);
CS],
            'micro' => [
                'On a desktop compiler the method signature is (int status, string message) Find(string title). The playground prints the two elements the tuple would hold.',
                'Deconstruction is var (status, message) = Find("milk");. The names come from the tuple.',
                'Do not return a tuple of seven items. That is a class that has not been written down.',
            ],
            'quiz' => [
                $q('Why return a tuple instead of only the message?', 'The caller also needs the status, and missing must not look like a title', 'Tuples are faster than ints', 'A method cannot return a string', 'The status is always 200'),
                $q('When should the pair become a class?', 'When it gains behaviour or more fields you keep explaining', 'When it has two elements', 'When one element is a string', 'Never'),
                $q('What is wrong with unnamed Item1 and Item2?', 'The caller can swap the status and the message', 'They do not compile', 'They cannot hold 404', 'They are strings'),
            ],
            'practice' => 'A divide method returns a quotient and a remainder for 7 and 2. Name the two elements and write both numbers.',
        ]),
        $L([
            'section' => '11. Collections and a check',
            'title' => 'A set keeps each value once',
            'keywords' => 'C# HashSet, duplicate, membership',
            'lead' => 'A set answers “is this already here?” and refuses a second copy of the same value. A list keeps both copies and keeps order. If you are collecting the distinct words in a note, a set is the honest box. If you care about the second occurrence or the order, a set will throw that information away.',
            'heading' => 'Adding milk twice leaves one milk',
            'para' => 'The words arrive milk, tea, milk. A list’s count is 3. A set’s count is 2. Asking whether tea is in the set is true. Asking whether cake is in the set is false. The set does not promise to remember that milk came first. The moment you need “the first time I saw it,” store that fact separately. Membership and order are different questions.',
            'bullets' => [
                'Add returns false when the value was already present. That bool is how you notice a duplicate.',
                'Contains is the membership test. A list can do it too, by walking. The set is built for it.',
                'Equality is the rule for “same.” For strings, case matters unless you chose a comparer.',
                'A set is not a dictionary. There is no second value attached to the word. If you need a count per word, use a dictionary.',
            ],
            'rows' => [
                ['List', 'Keeps duplicates and order', 'Count 3.'],
                ['Set', 'Keeps each value once', 'Count 2.'],
                ['Contains', 'Yes or no', 'tea is in, cake is not.'],
                ['Order', 'Not the set’s job', 'Do not rely on it.'],
                ['Add', 'False if it was already there', 'The duplicate signal.'],
                ['Comparer', 'What equal means', 'Case, unless you change it.'],
            ],
            'e1' => ['Example 1 — the second milk does not raise the count', 'Three arrivals, two distinct words.', <<<'CS'
int arrivals = 3;
int distinct = 2;
Console.WriteLine(arrivals);
Console.WriteLine(distinct);
CS],
            'e2' => ['Example 2 — membership is separate from the count', 'tea is present. cake is not. The answers are true and false.', <<<'CS'
bool hasTea = true;
bool hasCake = false;
Console.WriteLine(hasTea);
Console.WriteLine(hasCake);
CS],
            'micro' => [
                'The numbers are the result of a HashSet<string> after adding milk, tea, and milk. The playground prints that result. On a desktop compiler you call Add and Contains.',
                'A set of ints uses integer equality. 1 and 1 are the same. 1 and 1.0 are not both ints.',
                'Removing from a set removes the only copy. Removing from a list removes one occurrence if you remove a single item.',
            ],
            'quiz' => [
                $q('What is the set count after milk, tea, milk?', '2', '3', '1', '0'),
                $q('When do you choose a list instead?', 'When duplicate occurrences or order matter', 'When you only need membership', 'When the values are strings', 'Never'),
                $q('What does Add return when the value is already present?', 'false', 'true', 'the count', 'null'),
            ],
            'practice' => 'Words: a, b, a, a. What is the list count and the set count? Is b a member?',
        ]),
        $L([
            'section' => '11. Collections and a check',
            'title' => 'A line and a pile',
            'keywords' => 'C# Queue, Stack, FIFO, LIFO',
            'lead' => 'A queue is a line. The first item you add is the first item you take. A stack is a pile. The last item you add is the first item you take. Both are honest only if you use the matching operation. Taking from the wrong end is a different structure.',
            'heading' => 'First in, or last in',
            'para' => 'You enqueue milk, then tea. Dequeue returns milk. The line moved tea to the front. A stack of the same two pushes milk, then tea, and pop returns tea. milk is still in the pile. Empty is the shared edge: dequeue or pop on an empty structure is a failure. Check the count, or use a method that tells you it was empty. Do not catch that failure and pretend the item was a blank note.',
            'bullets' => [
                'Queue: enqueue at the back, dequeue at the front.',
                'Stack: push on top, pop from the top.',
                'Peek looks without taking. Use it when you need to see who is next and leave them there.',
                'Neither replaces a list when you need the middle item by index.',
            ],
            'rows' => [
                ['Enqueue', 'Join the back of the line', 'milk, then tea.'],
                ['Dequeue', 'Leave from the front', 'milk comes out first.'],
                ['Push', 'Put on top', 'tea is on top.'],
                ['Pop', 'Take the top', 'tea comes out first.'],
                ['Empty', 'A failure to take', 'Check before you take.'],
                ['Peek', 'Look, do not take', 'The next item stays.'],
            ],
            'e1' => ['Example 1 — the queue yields milk first', 'tea is still waiting.', <<<'CS'
string next = "milk";
string waiting = "tea";
Console.WriteLine(next);
Console.WriteLine(waiting);
CS],
            'e2' => ['Example 2 — the stack yields tea first', 'tea was pushed last, so it pops first. milk remains.', <<<'CS'
string next = "tea";
string waiting = "milk";
Console.WriteLine(next);
Console.WriteLine(waiting);
CS],
            'micro' => [
                'The prints are the results of Dequeue and Pop after the same two arrivals. The playground shows who comes out and who waits.',
                'A queue of jobs and a stack of undos are the usual pictures. Using a stack for a print line makes the last job jump the queue.',
                'Count == 0 before Pop. A TryPop method returns false when the pile is empty, which is the same idea as TryParse from the earlier chapter.',
            ],
            'quiz' => [
                $q('After enqueue milk then tea, what does dequeue return?', 'milk', 'tea', 'both', 'nothing'),
                $q('After push milk then tea, what does pop return?', 'tea', 'milk', 'the longer word', 'an empty string'),
                $q('What should you do before taking from an empty queue?', 'Check that it has an item', 'Dequeue anyway and treat a crash as a blank note', 'Push first', 'Convert it to a char'),
            ],
            'practice' => 'Enqueue a, b, c. Write the three dequeue results in order. Then push a, b, c and write the three pop results in order.',
        ]),
        $L([
            'section' => '11. Collections and a check',
            'title' => 'Expected versus actual',
            'keywords' => 'C# unit test, assert, expected actual',
            'lead' => 'A test is a small program that calls your rule and compares the result to a value you wrote down first. Expected is the answer you believe. Actual is what the rule returned. If they differ, the test fails and names both numbers. A test that prints and waits for you to look will be skipped.',
            'heading' => 'Write the expected value before the call',
            'para' => 'The rule is the fewest coins from the DSA course’s cousin: not here. The rule in this lesson is integer addition. Expected 2 + 2 is 4. Actual is the call. They match, so the test passes. A broken rule that returns 5 fails, and the message shows expected 4 and actual 5. You do not change the expected value to 5 to make the test green. You fix the rule, unless the expected value was the mistake, which you admit out loud.',
            'bullets' => [
                'One test, one behaviour. “Addition works” is too big. “2 + 2 is 4” is a test.',
                'The expected value is a literal you chose, not a second copy of the same buggy formula.',
                'A failing test is a success of the test. It found a difference.',
                'Run the tests before you change more code. A red test you ignore becomes decoration.',
            ],
            'rows' => [
                ['Expected', 'The answer you wrote first', '4.'],
                ['Actual', 'The rule’s result', 'The call.'],
                ['Pass', 'They match', 'The quiet case.'],
                ['Fail', 'They differ', 'Show both numbers.'],
                ['Green by editing expected', 'Usually cheating', 'Unless the expectation was wrong.'],
                ['One behaviour', 'A small test', 'Not the whole program.'],
            ],
            'e1' => ['Example 1 — 2 + 2 matches 4', 'passed is true.', <<<'CS'
int expected = 4;
int actual = 2 + 2;
bool passed = expected == actual;
Console.WriteLine(passed);
Console.WriteLine(expected);
Console.WriteLine(actual);
CS],
            'e2' => ['Example 2 — a rule that returns 5 fails', 'The test prints false and both numbers. The expected value stays 4.', <<<'CS'
int expected = 4;
int actual = 5;
bool passed = expected == actual;
Console.WriteLine(passed);
Console.WriteLine(expected);
Console.WriteLine(actual);
CS],
            'micro' => [
                'A desktop test method uses a framework attribute and a call that throws when the values differ. The comparison is the same one printed here.',
                'Comparing floating numbers with == is brittle. This lesson uses ints. For doubles, decide a tolerance and write it down.',
                'A test that needs a database, a clock, and a network is not the first test you write. Test the pure rule. The coins, the cutoff, the status code.',
            ],
            'quiz' => [
                $q('What is expected?', 'The answer you wrote down as correct', 'Whatever the method returned', 'Always 0', 'The test framework’s name'),
                $q('What should you do when actual is 5 and expected is 4?', 'Fix the rule, or admit the expectation was wrong', 'Change expected to 5 to go green', 'Delete the test', 'Print nothing'),
                $q('Why keep a test small?', 'So a failure points at one behaviour', 'Large tests run faster', 'Ints cannot be tested', 'Expected values must be strings'),
            ],
            'practice' => 'Expected length of milk is 4. Actual is 5 because a rule counted the terminator. Does the test pass? Which number do you change, and why?',
        ]),
    ];
}
