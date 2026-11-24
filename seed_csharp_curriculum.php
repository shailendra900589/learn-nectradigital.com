<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/track_seed.php';

function csharp_course_meta(): array
{
    return [
        'title' => 'C# Programming',
        'slug' => 'c-sharp-tutorial',
        'keywords' => 'C# programming, .NET, beginner, types, methods, classes, null, collections, LINQ, exceptions',
        'description' => 'C# from the first line a program writes, through types, decisions, loops, arrays, methods, classes, null, collections, and LINQ, to checks, a small route, and a reading notebook. Every chapter names the small points beginners miss. The examples store real values and print them. No earlier chapter is removed when this track is updated.',
    ];
}

function csharp_chapters(): array
{
    $f = 'cs_notes.cs';
    $L = static function (array $c) use ($f): array {
        return track_lesson('csharp', $f, $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };
    $pipe = svg_pipeline_diagram('From a C# file to a running program', [
        ['Source', '.cs file'],
        ['Build', 'the project'],
        ['Assembly', 'the result'],
        ['Run', 'Main writes'],
    ]);

    return [
        $L([
            'section' => '0. A program',
            'title' => 'A program writes a line',
            'keywords' => 'C# Main, Console.WriteLine, project',
            'lead' => 'A C# program is a project of source files. Building the project produces an assembly. Running it starts at Main. The first skill is to write one line of text and see that line come back. A desktop project begins with a using line for the library that provides Console. This playground already knows Console, so the samples start at the statements.',
            'heading' => 'WriteLine ends the line',
            'para' => 'Console.WriteLine writes the text and then ends the line. Console.Write writes without ending the line, so the next call continues. A string sits in double quotes. The quotes are not written. A second call is a second statement, and every statement ends with a semicolon.',
            'visual' => $pipe,
            'bullets' => [
                'Main is the start. The file name is not the start.',
                'WriteLine adds the newline. Write does not.',
                'A desktop file has using System; at the top so the name Console can be found. The samples here call Console directly.',
                'Build, then run. Saving the file is not the same as running the new text.',
            ],
            'rows' => [
                ['Main', 'Where running starts', 'One entry point.'],
                ['WriteLine', 'Text plus a newline', 'The usual call.'],
                ['Write', 'Text, same line', 'The next call continues.'],
                ['Quotes', 'Mark the text', 'They are not printed.'],
                [';', 'End of a statement', 'Required.'],
                ['Build', 'Make the assembly', 'Then run that assembly.'],
            ],
            'e1' => ['Example 1 — one line', 'The playground writes the string you gave WriteLine.', <<<'CS'
Console.WriteLine("hello from C#");
CS],
            'e2' => ['Example 2 — Write then WriteLine', 'The first call does not end the line, so “aisle” continues on the same line. The next WriteLine starts a new one.', <<<'CS'
Console.Write("milk");
Console.WriteLine(" aisle");
Console.WriteLine("done");
CS],
            'micro' => [
                'A top-level program in a current project can omit the class and Main and just contain the statements. The compiler still builds a Main for you. This course shows the statements.',
                'Spelling WriteLine with a lowercase w will not find the method. Names are case-sensitive.',
                'The text in quotes is a string. A number without quotes is an int, which the next chapter stores under a name.',
            ],
            'quiz' => [
                $q('Which call ends the line after the text?', 'Console.WriteLine', 'Console.Write', 'The file name', 'The semicolon by itself'),
                $q('Where does a normal program start?', 'Main', 'The first comment', 'The project folder name', 'The last method in the file'),
                $q('Are the quote marks printed?', 'No, they mark the text', 'Yes, always', 'Only on the second call', 'Only if you build twice'),
            ],
            'practice' => 'Write your city on one line and the word ready on the next line.',
        ]),
        $L([
            'section' => '1. Values',
            'title' => 'Types: int, double, bool, and string',
            'keywords' => 'C# types, int, double, bool, string, var',
            'lead' => 'Every name has a type. int is a whole number. double is a number that may have a fraction. bool is true or false. string is text. The type is a promise about what you may store. var asks the compiler to infer the type from the right-hand side. It does not mean “any type later.”',
            'heading' => 'The promise is fixed at the declaration',
            'para' => 'int price = 40; stores a whole number. A later line can store a different int. It cannot store a sentence. bool hot = true; is a decision you have already made, not a number you should print and reinterpret. string note = "milk"; is text, even if the text looks like digits.',
            'bullets' => [
                'Declare the type once. Later stores repeat the name only: price = 45;',
                'var price = 40; infers int. The next store still has to be an int.',
                'true and false are not the strings "true" and "false", and they are not 1 and 0 in your source.',
                'A string of digits is still text. Adding it as a number is a different step, and a failed step should be refused.',
            ],
            'rows' => [
                ['int', 'Whole number', 'Counts and money in the smallest unit.'],
                ['double', 'Fraction allowed', '3.5 is legal.'],
                ['bool', 'true or false', 'A decision, not a count.'],
                ['string', 'Text', 'Double quotes.'],
                ['var', 'Infer once', 'The type does not float afterward.'],
                ['=', 'Store', 'Name on the left, value on the right.'],
            ],
            'e1' => ['Example 1 — four promises', 'Each name prints as the value you stored. hot prints as True.', <<<'CS'
int price = 40;
double half = 3.5;
bool hot = true;
string note = "milk";
Console.WriteLine($"price = {price}");
Console.WriteLine($"half = {half}");
Console.WriteLine($"hot = {hot}");
Console.WriteLine($"note = {note}");
CS],
            'e2' => ['Example 2 — a later store', 'The first line sees 40. The second sees 45. The type is still int.', <<<'CS'
int price = 40;
Console.WriteLine($"before = {price}");
price = 45;
Console.WriteLine($"after = {price}");
CS],
            'micro' => [
                'Interpolation $"price = {price}" splices the current value into the text. The braces are not printed.',
                'decimal is the type to prefer for money on a desktop project. This lesson uses int for whole units so the arithmetic stays obvious.',
                'char holds one character in single quotes. A string holds many, including one.',
            ],
            'quiz' => [
                $q('Which type holds true or false?', 'bool', 'string', 'int', 'var, with no fixed type'),
                $q('What does var do?', 'Infers the type from the first store', 'Lets every later store change the type', 'Always means string', 'Disables checks'),
                $q('When does price = 45 become visible?', 'On the statements after that line', 'On earlier writes as well', 'Only after a new project', 'Never'),
            ],
            'practice' => 'Store pages as 12, print it, change it to 13, and print it again. State the type.',
        ]),
        $L([
            'section' => '1. Values',
            'title' => 'Arithmetic and integer division',
            'keywords' => 'C# arithmetic, integer division, remainder',
            'lead' => 'The operators are the ones you know from paper, with one sharp edge. Dividing two ints drops the fraction. 7 / 2 is 3. The remainder operator % keeps what was dropped: 7 % 2 is 1. A fraction needs a double, written with a decimal point.',
            'heading' => 'The types on both sides decide',
            'para' => 'Store the inputs under names, then store the result under a new name. total = price * count is easier to check than a formula hidden inside the write. Parentheses make the order obvious. Integer division is not rounding: 7 / 2 is 3, not 4.',
            'bullets' => [
                'int / int truncates toward zero.',
                '7.0 / 2.0 keeps the fraction and should be stored in a double.',
                '% on ints is the remainder. A remainder of 0 means the division was exact.',
                'A double stored into an int drops the fraction again. Do not do that by accident.',
            ],
            'rows' => [
                ['*', 'Multiply', 'Before + unless you add parentheses.'],
                ['int / int', 'Whole groups', 'Fraction dropped.'],
                ['double / double', 'Keeps the fraction', 'Store a double.'],
                ['%', 'Remainder', '7 % 2 is 1.'],
                ['Name the result', 'A stored total', 'Then print the name.'],
                ['Parentheses', 'Your order', 'Use them when someone could hesitate.'],
            ],
            'e1' => ['Example 1 — two divisions', 'q is 3. half is 3.5. The remainder is 1.', <<<'CS'
int q = 7 / 2;
int r = 7 % 2;
double half = 7.0 / 2.0;
Console.WriteLine($"q = {q}");
Console.WriteLine($"r = {r}");
Console.WriteLine($"half = {half}");
CS],
            'e2' => ['Example 2 — a total from names', '40 times 2 is 80. The names are what you would change for a different sale.', <<<'CS'
int price = 40;
int count = 2;
int total = price * count;
Console.WriteLine($"total = {total}");
CS],
            'micro' => [
                '7 / 2.0 is a double division because one side is a double. The .0 is doing the work.',
                'Checked arithmetic on a desktop project can refuse an int that grows past its maximum. This lesson stays with small numbers and still wants you to know the maximum exists.',
                'Money in whole cents as int avoids the famous 0.1 + 0.2 mess. Divide by 100 only when you print.',
            ],
            'quiz' => [
                $q('What is 7 / 2 for two ints?', '3', '3.5', '4', '1'),
                $q('What is 7 % 2?', '1', '3.5', '14', '0'),
                $q('Which store keeps the fraction of 7.0 / 2.0?', 'A double', 'An int', 'A bool', 'The project name'),
            ],
            'practice' => 'pages = 10 and perShelf = 3. Print the number of full shelves and the pages left over.',
        ]),
        $L([
            'section' => '2. Choosing',
            'title' => 'Decisions and the difference between = and ==',
            'keywords' => 'C# if, bool, comparison',
            'lead' => 'A decision stores a bool or branches on one. == asks whether two values are equal. = stores. C# will not let you write if (price = 40) the way a loose language might, because the store produces an int and if wants a bool. That refusal is a feature. This lesson prints the bool so you can see the answer.',
            'heading' => 'A bool is the whole answer',
            'para' => 'price > 30 is true for 40. price > 100 is false. && means both. || means either. An if runs its block only when the bool is true. An else runs when it is false. Braces mark the whole block. Indentation does not.',
            'bullets' => [
                'Compare with ==. Store with =. The compiler already separates them in an if. Keep the habit in your reading too.',
                '>= includes the equal value. A budget match is “within budget” only if you decided that equal is allowed.',
                '&& is false when either side is false. || is true when either side is true.',
                'Several separate if statements can all run. else if is one chain.',
            ],
            'rows' => [
                ['==', 'Equal?', 'Not a store.'],
                ['=', 'Store', 'Not a question.'],
                ['bool', 'The answer', 'True or False when printed.'],
                ['&&', 'Both', 'One false side fails the whole check.'],
                ['||', 'Either', 'One true side is enough.'],
                ['Braces', 'The whole branch', 'Required once the branch has two statements.'],
            ],
            'e1' => ['Example 1 — two questions about one price', '40 is above 30 and not above 100.', <<<'CS'
int price = 40;
bool hot = price > 30;
bool over = price > 100;
Console.WriteLine($"hot = {hot}");
Console.WriteLine($"over = {over}");
CS],
            'e2' => ['Example 2 — both flags', 'ok is True only when hot and ready are both true. Turn ready off and the check fails.', <<<'CS'
bool hot = true;
bool ready = true;
bool ok = hot && ready;
bool readyOff = false;
bool blocked = hot && readyOff;
Console.WriteLine($"ok = {ok}");
Console.WriteLine($"blocked = {blocked}");
CS],
            'micro' => [
                'A bool printed by this playground shows True or False. In source you write true and false in lowercase.',
                'Comparing strings with == compares the text, which is what you want. Comparing two objects with == may compare identity. Know which one you have.',
                'Do not compare doubles with == if they came from a fraction. Compare a range, or compare ints.',
            ],
            'quiz' => [
                $q('What does == do?', 'Asks whether two values are equal', 'Stores the right-hand side', 'Ends the line', 'Builds the project'),
                $q('What does && require?', 'Both sides true', 'Either side true', 'Both sides false', 'One side to be a string'),
                $q('Why does if refuse a store of an int?', 'if wants a bool, and a store of an int is not a bool', 'if can only print', 'Ints are illegal', 'Braces replace the condition'),
            ],
            'practice' => 'Store pages = 12 and limit = 10. Print whether pages is above the limit.',
        ]),
        $L([
            'section' => '2. Choosing',
            'title' => 'A loop, one pass at a time',
            'keywords' => 'C# for, foreach, accumulator',
            'lead' => 'A loop repeats a body. for has a start, a question, and a step. foreach walks each item of a row. The bug is almost always the count of passes. This lesson writes each pass as a store and a write, which is what the loop body does. A desktop compiler would wrap the same adds in for or foreach.',
            'heading' => 'The total after each pass',
            'para' => 'Start at 0. Add 10, then 20, then 30. The prints are 0, 10, 30, and 60. Three prices, three adds. A question that is already false runs zero passes and the total stays 0. A minimum should start at the first real price, not at 0, when every price is positive.',
            'bullets' => [
                'A sum starts at 0. Say so in the code, do not rely on a default you did not write.',
                'The question is checked before the body. Starting past the end means the body never runs.',
                'The step must change the thing the question looks at.',
                'foreach is the clearer walk when you want every item and you do not need the index.',
            ],
            'rows' => [
                ['Start', 'Before pass 1', '0 for a sum.'],
                ['Question', 'Before the body', 'False means skip.'],
                ['Body', 'The add', 'One price per pass.'],
                ['Step', 'After the body', 'Move on.'],
                ['foreach', 'Each item', 'No index to mis-count, if you want them all.'],
                ['Off by one', 'A missed price', 'Count the writes.'],
            ],
            'e1' => ['Example 1 — three adds', 'The running total is 0, 10, 30, 60.', <<<'CS'
int sum = 0;
Console.WriteLine($"start = {sum}");
sum = sum + 10;
Console.WriteLine($"after 10 = {sum}");
sum = sum + 20;
Console.WriteLine($"after 20 = {sum}");
sum = sum + 30;
Console.WriteLine($"after 30 = {sum}");
CS],
            'e2' => ['Example 2 — a question that is already false', 'count is 3. “Still below 3?” is false. The sum stays 0.', <<<'CS'
int count = 3;
bool entered = count < 3;
int sum = 0;
Console.WriteLine($"entered = {entered}");
Console.WriteLine($"sum = {sum}");
CS],
            'micro' => [
                'The desktop form is for (int i = 0; i < 3; i = i + 1). This lesson shows the body because the body is what you mis-count.',
                'i = i + 1 inside the body and also in the step moves twice. Pick one place.',
                'break leaves the loop. continue skips to the next question. Both are easier to read when the loop is short.',
            ],
            'quiz' => [
                $q('What does a sum start at?', '0', 'The last item', '1 always', 'The length of the name'),
                $q('When is the question checked?', 'Before each pass', 'Only after the program exits', 'Only by the project file', 'Never'),
                $q('What is foreach for?', 'Walking each item when you do not need the index', 'Integer division', 'Declaring a class', 'Ending the line'),
            ],
            'practice' => 'Add 4, then 5, then 6, printing the total after each add.',
        ]),
        $L([
            'section' => '3. Groups',
            'title' => 'Numbered slots',
            'keywords' => 'C# arrays, index, Length',
            'lead' => 'An array is a fixed row of one type. The first index is 0. The last index is Length - 1. Length is a property of the row, which is a gift compared with a language that forgets the length. Index Length itself is the first slot that does not exist.',
            'heading' => 'Length is not a legal index',
            'para' => 'Three prices: index 0 is 10, index 1 is 20, index 2 is 30. Length is 3. The last legal index is 2. A desktop array is int[] prices = { 10, 20, 30 }; and prices[0] is the first. This lesson names the slots so the indexes are visible, and it stores Length beside them.',
            'bullets' => [
                'new int[3] makes three slots. Until you store them, know what your version puts there. This lesson stores every slot it reads.',
                'The row does not grow. A longer row is a new array, or a List from the next chapter.',
                'Every slot has the same type.',
                'A foreach walks all legal slots and cannot ask for index 3.',
            ],
            'rows' => [
                ['Index 0', 'The first slot', 'Not 1.'],
                ['Length', 'How many slots', '3 in this example.'],
                ['Last index', 'Length - 1', '2, when Length is 3.'],
                ['prices[Length]', 'Outside', 'Do not read it.'],
                ['Fixed', 'The row does not grow', 'Make a new row, or use a list.'],
                ['Same type', 'All slots', 'ints with ints.'],
            ],
            'e1' => ['Example 1 — first, last, and the last index', 'first is 10. last is 30. lastIndex is 2.', <<<'CS'
int slot0 = 10;
int slot1 = 20;
int slot2 = 30;
int length = 3;
int lastIndex = length - 1;
Console.WriteLine($"first = {slot0}");
Console.WriteLine($"middle = {slot1}");
Console.WriteLine($"last = {slot2}");
Console.WriteLine($"lastIndex = {lastIndex}");
CS],
            'e2' => ['Example 2 — the total of every legal slot', '10 + 20 + 30 is 60. There is no fourth add.', <<<'CS'
int slot0 = 10;
int slot1 = 20;
int slot2 = 30;
int sum = 0;
sum = sum + slot0;
sum = sum + slot1;
sum = sum + slot2;
Console.WriteLine($"sum = {sum}");
CS],
            'micro' => [
                'Length is a property, so a desktop array uses prices.Length with no parentheses. A method would have parentheses.',
                'An index variable that starts at 0 and continues while index < length is the safe walk.',
                'Arrays are reference types. Two names can refer to the same row. Storing into one name is visible through the other. A later chapter uses a class, which behaves the same way.',
            ],
            'quiz' => [
                $q('What is the first index?', '0', '1', 'Length', '-1'),
                $q('For Length 3, what is the last legal index?', '2', '3', '4', '30'),
                $q('Can this array grow by one slot in place?', 'No, the length is fixed', 'Yes, by writing index 3', 'Yes, by changing the type', 'Only if the total is 0'),
            ],
            'practice' => 'Slots 4, 6, and 5. Print the first, the last, the last index, and the total.',
        ]),
        $L([
            'section' => '3. Groups',
            'title' => 'A list grows, an array does not',
            'keywords' => 'C# List, Count, add item',
            'lead' => 'A List is a row that can take another item. Count is how many items are in it now. Capacity is how many it can hold before it reserves a larger block. You loop to Count, not to Capacity. Mixing them up reads empty spare slots.',
            'heading' => 'Count is the number you stored',
            'para' => 'Start with two prices, 10 and 20. Count is 2. Add 30. Count is 3. The total becomes 60. The spare room the list may keep internally is not another price. This lesson stores Count before and after the add so the growth is a number you can see.',
            'bullets' => [
                'Add puts the item at the end and increases Count by one.',
                'The last index is Count - 1.',
                'Capacity can be larger than Count. Do not treat spare room as data.',
                'A list of int still refuses a string. The type argument is the promise.',
            ],
            'rows' => [
                ['List', 'A growable row', 'Still one type.'],
                ['Count', 'Items you stored', 'The walk stops here.'],
                ['Capacity', 'Reserved room', 'Not items.'],
                ['Add', 'One more item', 'Count increases.'],
                ['Last index', 'Count - 1', 'Same rule as an array.'],
                ['Type argument', 'List of int', 'No mixed types.'],
            ],
            'e1' => ['Example 1 — Count before and after', 'Two items, then three. The new Count is 3.', <<<'CS'
int count = 2;
Console.WriteLine($"before = {count}");
count = count + 1;
Console.WriteLine($"after = {count}");
CS],
            'e2' => ['Example 2 — the total after the third price', 'Adding 30 to a total of 30 makes 60. The third item is real data, not spare capacity.', <<<'CS'
int sum = 0;
sum = sum + 10;
sum = sum + 20;
int count = 2;
sum = sum + 30;
count = count + 1;
Console.WriteLine($"sum = {sum}");
Console.WriteLine($"count = {count}");
CS],
            'micro' => [
                'On the desktop the type is List<int> and the call is prices.Add(30). This lesson stores the Count and the sum the call would leave behind.',
                'Inserting at the front moves every later item. Adding at the end is the cheap direction.',
                'Clear sets Count to 0. It does not change the element type.',
            ],
            'quiz' => [
                $q('Which number do you walk to?', 'Count', 'Capacity', 'The file length', 'Always 100'),
                $q('What does Add do to Count?', 'Increases it by one', 'Sets it to zero', 'Leaves it unchanged', 'Sets it to Capacity + 1 always'),
                $q('What is the last index after three adds?', '2', '3', 'Capacity', '0'),
            ],
            'practice' => 'Start Count at 0. Add three page counts in your head, print the Count you finish with, and print the total of 5, 5, and 5.',
        ]),
        $L([
            'section' => '4. Names for rules',
            'title' => 'A method is a named rule',
            'keywords' => 'C# methods, parameters, return',
            'lead' => 'A method is a named rule with parameters and a result. The call fills the parameters and uses the result. A method that returns the number is easier to test than a method that only writes. This lesson fills the parameters and runs the body, which is the call for these inputs.',
            'heading' => 'Inputs in, one result out',
            'para' => 'TotalOf takes a price and a count and returns the product. For 40 and 2 the result is 80. The type before the name is the result type. void means there is nothing to return. A guard can return 0 when the count is negative, so a bad input does not become a negative bill.',
            'bullets' => [
                'Parameters are the inputs. The names inside the method are not the caller’s slots. The values are passed in.',
                'return sends the result and leaves the method.',
                'The result type is part of the name’s promise. int TotalOf returns an int.',
                'One method, one job. If you cannot name it, it is two jobs.',
            ],
            'rows' => [
                ['Parameter', 'An input', 'Filled in by the call.'],
                ['Result type', 'What comes back', 'int, double, bool, string, or void.'],
                ['return', 'Send it back', 'Leaves the method.'],
                ['Call', 'Use the rule', 'TotalOf(40, 2) is 80.'],
                ['void', 'No result', 'Only when you accept a side effect.'],
                ['Guard', 'Refuse a bad input', 'A negative count is not a sale.'],
            ],
            'e1' => ['Example 1 — TotalOf(40, 2)', 'The result is 80.', <<<'CS'
int price = 40;
int count = 2;
int total = price * count;
Console.WriteLine($"total = {total}");
CS],
            'e2' => ['Example 2 — the guard', 'A count below 0 is rejected. The total stays 0.', <<<'CS'
int price = 40;
int count = -1;
bool bad = count < 0;
int total = 0;
Console.WriteLine($"rejected = {bad}");
Console.WriteLine($"total = {total}");
CS],
            'micro' => [
                'Passing an int passes the number. Changing the parameter does not change the caller’s variable. A class, passed as a reference, is different: the method can change its fields.',
                'Expression-bodied methods on the desktop are still the same rule: int TotalOf(int price, int count) => price * count;',
                'The printed number is the result of the rule for these inputs.',
            ],
            'quiz' => [
                $q('What is a parameter?', 'An input the call fills in', 'The project file', 'A using line', 'The newline'),
                $q('What does return do?', 'Sends the result and leaves the method', 'Builds the assembly', 'Adds a list item', 'Compares two strings'),
                $q('What does a negative count do in the guard?', 'The sale is rejected and the total stays 0', 'The price becomes negative', 'The method prints the project name', 'Count is ignored and the total is 40'),
            ],
            'practice' => 'Compute pages - read for pages 20 and read 5. Print the result and say what you would name the method.',
        ]),
        $L([
            'section' => '4. Names for rules',
            'title' => 'A class bundles fields',
            'keywords' => 'C# class, fields, object',
            'lead' => 'A class describes a thing with fields. An object is one actual thing of that class. Two book objects can have different page counts. Changing one does not change the other, unless both names refer to the same object. The fields of one book belong together.',
            'heading' => 'The type is the shape, the object is the thing',
            'para' => 'A book has an id and a page count. Book 7 has 12 pages. You can raise the pages to 13 and the id stays 7. On the desktop this is a class Book with those two fields, and new Book stores the object. This lesson stores the fields and changes one of them.',
            'bullets' => [
                'A field is a name that belongs to the object, not a loose global.',
                'new creates the object. Without it you have a type and no thing.',
                'Two variables can point at one object. Then a field change is visible through both names.',
                'Keep fields private on a desktop project and change them through methods when there is a rule to protect. This lesson shows the fields so the bundle is obvious.',
            ],
            'rows' => [
                ['class', 'The shape', 'Book, not one book.'],
                ['object', 'One thing', 'Created with new.'],
                ['Field', 'A part of the thing', 'id and pages.'],
                ['Change one', 'The others stay', 'pages can move, id stays.'],
                ['Two names', 'Maybe one object', 'Know whether you shared it.'],
                ['Method', 'A rule on the thing', 'Often the safe way to change a field.'],
            ],
            'e1' => ['Example 1 — one book', 'id 7, pages 12.', <<<'CS'
int id = 7;
int pages = 12;
Console.WriteLine($"id = {id}");
Console.WriteLine($"pages = {pages}");
CS],
            'e2' => ['Example 2 — one field changes', 'pages is 13. id is still 7.', <<<'CS'
int id = 7;
int pages = 12;
pages = 13;
Console.WriteLine($"id = {id}");
Console.WriteLine($"pages = {pages}");
CS],
            'micro' => [
                'A struct in C# is a value. Assigning it stores a second bundle. A class assignment stores another name for the same object. Pick the class when the thing has an identity.',
                'A constructor is the method that runs at new and can refuse to build a book with negative pages.',
                'Properties are fields with a get and a set. They look like fields at the call and can hide a check.',
            ],
            'quiz' => [
                $q('What is the difference between a class and an object?', 'The class is the shape; the object is one thing', 'They are two words for the same int', 'An object is a project file', 'A class is a single page count'),
                $q('If you change pages, what happens to id?', 'It stays as it was', 'It becomes the new page count', 'It becomes 0', 'It is deleted'),
                $q('What does new do?', 'Creates an object', 'Ends the line', 'Divides two ints', 'Infers var'),
            ],
            'practice' => 'A shelf has id 2 and count 8. Print both, add 1 to the count, and print both again.',
        ]),
        $L([
            'section' => '5. Absence',
            'title' => 'null is not an empty string',
            'keywords' => 'C# null, empty string, nullable',
            'lead' => 'null means there is no object. An empty string is an object with no characters. They are different. You can ask an empty string for its length and get 0. Following a null to ask for a length is a crash. A missing note and a note you have not written yet should not share one value unless you mean them to.',
            'heading' => 'Pick a meaning and test it',
            'para' => 'note is "". missing is null. The empty note has length 0. The missing note must be tested before you touch it. A desktop signature string? says null is allowed. Without the question mark, a string parameter should not be null, and nullable checks help the compiler remind you.',
            'bullets' => [
                '"" is empty text. null is the absence of a string.',
                'Test null before you ask for Length, for a character, or for a method.',
                'Do not use empty text to mean “I do not know” if empty text is also a real title.',
                'A nullable value type int? can be null. A plain int cannot. Zero is a real int, not null.',
            ],
            'rows' => [
                ['""', 'Empty text', 'Length 0. It exists.'],
                ['null', 'No object', 'Do not follow it.'],
                ['Test', 'Before you use it', 'The crash is the skipped test.'],
                ['string?', 'Null allowed', 'The question mark is the permission.'],
                ['int', 'Always a number', '0 is a number.'],
                ['int?', 'A number or null', 'When a missing count is different from zero.'],
            ],
            'e1' => ['Example 1 — empty is a real string', 'The length you store for "" is 0. The write of the note shows nothing between the equals sign and the end of the line.', <<<'CS'
string note = "";
int length = 0;
Console.WriteLine($"note = {note}");
Console.WriteLine($"length = {length}");
CS],
            'e2' => ['Example 2 — missing is a different flag', 'hasNote is false when the string is null. You print a sentence instead of asking the missing string for a length.', <<<'CS'
string missing = null;
bool hasNote = false;
Console.WriteLine($"missing = {missing}");
Console.WriteLine($"hasNote = {hasNote}");
Console.WriteLine("no note yet");
CS],
            'micro' => [
                'The second example stores hasNote as false because missing is null. The desktop check is missing is not null. This lesson prints the flag you would compute.',
                'Null-forgiving operators that silence the compiler do not create the object. They only hide the reminder.',
                'Prefer a real empty collection over a null collection when “none” means “a list of length 0.” Callers can then loop without a special case.',
            ],
            'quiz' => [
                $q('What is the length of an empty string?', '0', 'null', '1', 'It cannot be asked'),
                $q('What is null?', 'The absence of an object', 'A string of the word empty', 'The number 0', 'An empty list always'),
                $q('When do you test for null?', 'Before you use the object', 'After you have already asked for Length', 'Only at the end of Main', 'Never, strings cannot be null'),
            ],
            'practice' => 'Print the length of an empty title as 0, and print a separate flag for a missing author as false.',
        ]),
        $L([
            'section' => '5. Absence',
            'title' => 'A dictionary counts by a key',
            'keywords' => 'C# dictionary, key, tally',
            'lead' => 'A dictionary stores a value under a key. Looking up a key is the point: the word “milk” maps to how many times you have seen it. The key you did not store is not zero until you decide it is. Asking for a missing key should be a check, not a crash you discover later.',
            'heading' => 'The first time you see a word, the tally starts at 1',
            'para' => 'See milk, then milk, then tea. milk ends at 2. tea ends at 1. A word you never saw is absent, which this lesson prints as found = False. On the desktop the type is Dictionary<string, int>, the check is TryGetValue, and you add 1 to the stored tally.',
            'bullets' => [
                'Keys are unique. A second store under the same key replaces or updates. It does not create a second milk.',
                'TryGetValue is the check that does not throw when the key is absent.',
                'The tally starts at 1 on the first sighting, or starts at 0 and then you add 1. Pick one and do it the same way every time.',
                'A list of pairs can hold duplicates. A dictionary cannot. Choose on purpose.',
            ],
            'rows' => [
                ['Key', 'What you look up', 'The word.'],
                ['Value', 'What you store', 'The tally.'],
                ['First sight', 'Tally becomes 1', 'There was no earlier tally.'],
                ['Again', 'Add 1', 'milk becomes 2.'],
                ['Missing', 'Not zero by magic', 'Check, then decide.'],
                ['TryGetValue', 'The safe lookup', 'A bool plus the value.'],
            ],
            'e1' => ['Example 1 — two words', 'milk was seen twice. tea once.', <<<'CS'
int milk = 0;
milk = milk + 1;
milk = milk + 1;
int tea = 0;
tea = tea + 1;
Console.WriteLine($"milk = {milk}");
Console.WriteLine($"tea = {tea}");
CS],
            'e2' => ['Example 2 — a word that was never stored', 'found is False. You do not pretend the tally is 0 unless “never seen” and “seen, tally later cleared” are the same in your program.', <<<'CS'
bool found = false;
Console.WriteLine($"found = {found}");
Console.WriteLine("bread is absent");
CS],
            'micro' => [
                'String keys compare text. "Milk" and "milk" are different keys unless you normalise them first.',
                'Iterating a dictionary does not promise the order you inserted. If the order matters, keep a list.',
                'A missing key and a stored 0 can both be real. Write down which one you use.',
            ],
            'quiz' => [
                $q('After seeing milk twice, what is its tally?', '2', '0', '1', 'null'),
                $q('What does a second store under the same key do?', 'Updates that one entry', 'Creates a second key with the same text always', 'Clears the dictionary', 'Changes the type'),
                $q('How should you look up a key that might be absent?', 'With a check that can say absent', 'By assuming the tally is 0', 'By printing the project name', 'By dividing by the key'),
            ],
            'practice' => 'Tally the words tea, tea, milk. Print both tallies.',
        ]),
        $L([
            'section' => '6. The same loops, named',
            'title' => 'Where and Select are loops you can already read',
            'keywords' => 'C# LINQ, Where, Select, deferred',
            'lead' => 'LINQ names two loops. Where keeps the items that pass a test. Select turns each kept item into a new value. They do not make a new kind of computer. They write the walk in a short form. You should be able to expand them back into the passes from the loop chapter.',
            'heading' => 'Keep, then shape',
            'para' => 'Prices 10, 35, and 40. Where price > 30 keeps 35 and 40. Select adds nothing here; it would be the place you turn a price into a label. The count of kept prices is 2. The total of kept prices is 75. A desktop query is prices.Where(price => price > 30). The arrow is the test.',
            'bullets' => [
                'Where does not change the original row. It describes the items that pass.',
                'Select changes the shape. A price can become a string. The original ints stay where they were if you did not store over them.',
                'The query runs when you walk it, not when you write Where, unless you force it. Walk it once and store what you need if the source might change.',
                'A test that uses a value from outside the item should be obvious. Hidden changing state inside Where is hard to read.',
            ],
            'rows' => [
                ['Where', 'Keep some', 'The test is a bool.'],
                ['Select', 'Shape each one', 'A price becomes a label, if you ask.'],
                ['Source', 'Unchanged', 'You did not write into it.'],
                ['Count kept', 'How many passed', '2 in this lesson.'],
                ['Arrow', 'The test or the shape', 'price => price > 30.'],
                ['Walk', 'When it runs', 'Store the result if you need it stable.'],
            ],
            'e1' => ['Example 1 — keep prices above 30', '35 and 40 pass. 10 does not. The kept total is 75.', <<<'CS'
int a = 10;
int b = 35;
int c = 40;
bool keepA = a > 30;
bool keepB = b > 30;
bool keepC = c > 30;
int kept = 0;
int sum = 0;
sum = sum + b;
kept = kept + 1;
sum = sum + c;
kept = kept + 1;
Console.WriteLine($"keep10 = {keepA}");
Console.WriteLine($"keep35 = {keepB}");
Console.WriteLine($"keep40 = {keepC}");
Console.WriteLine($"kept = {kept}");
Console.WriteLine($"sum = {sum}");
CS],
            'e2' => ['Example 2 — Select as a label', 'The kept prices become the words over and over. The numbers are still 35 and 40. The labels are a new shape.', <<<'CS'
string first = "over";
string second = "over";
Console.WriteLine(first);
Console.WriteLine(second);
CS],
            'micro' => [
                'The first example writes the bools so a price that barely fails is visible. 30 itself would fail a test written as > 30.',
                'Count() and Sum() on the desktop are the kept and sum stores. They walk the query.',
                'If the source list gains an item after Where and before the walk, a deferred query sees the new item. Store a list at the moment you mean.',
            ],
            'quiz' => [
                $q('What does Where do?', 'Keeps the items that pass a test', 'Sorts by deleting the largest always', 'Changes every int into a string', 'Builds the project'),
                $q('Does Where change the original row?', 'No', 'Yes, it deletes the failures from the source', 'Only on the second call', 'Only for strings'),
                $q('What is Select for?', 'Turning each item into a new value', 'Keeping a subset only', 'Declaring a class', 'Ending a line'),
            ],
            'practice' => 'From pages 4, 11, and 9, keep those above 8. Print how many you kept and their total.',
        ]),
        $L([
            'section' => '6. The same loops, named',
            'title' => 'A failed check is a result, not a surprise',
            'keywords' => 'C# exceptions, TryParse, guard',
            'lead' => 'Some failures are expected. A user typed “lots” where you needed a number. That is not a rare disaster. TryParse asks “can this text be an int?” and returns a bool. An exception is for a broken promise you did not expect to handle at this line. Using exceptions for ordinary bad input makes the normal path look like a crash.',
            'heading' => 'Parse, and look at the bool',
            'para' => 'The text "40" can become the int 40, so ok is true. The text "lots" cannot, so ok is false and you keep a total of 0 rather than inventing a number. On the desktop, int.TryParse writes the int only when it returns true. You test the bool before you trust the number.',
            'bullets' => [
                'TryParse is the ordinary path for untrusted text.',
                'throw is for a promise the caller broke, such as a negative length you documented as illegal.',
                'catch that swallows everything hides bugs. Catch a specific failure if you can do something real.',
                'A bool result plus an unchanged total is easier to test than a stack trace.',
            ],
            'rows' => [
                ['TryParse', 'Text to int, maybe', 'Returns a bool.'],
                ['ok', 'The parse worked', 'Only then read the number.'],
                ['"lots"', 'Not an int', 'ok is false.'],
                ['throw', 'A broken promise', 'Not for ordinary typing.'],
                ['catch', 'A plan for a failure', 'Specific, and only if you can respond.'],
                ['Total stays 0', 'No invented number', 'The safe result here.'],
            ],
            'e1' => ['Example 1 — the text is a number', '40 parses. The total may use it.', <<<'CS'
string text = "40";
bool ok = true;
int value = 40;
Console.WriteLine($"ok = {ok}");
Console.WriteLine($"value = {value}");
CS],
            'e2' => ['Example 2 — the text is not a number', 'ok is false. value stays 0. You do not bill “lots”.', <<<'CS'
string text = "lots";
bool ok = false;
int value = 0;
Console.WriteLine($"text = {text}");
Console.WriteLine($"ok = {ok}");
Console.WriteLine($"value = {value}");
CS],
            'micro' => [
                'The samples store the bool TryParse would return. A desktop call is int.TryParse(text, out int value).',
                'Empty text is also a failed parse. Decide whether to tell the user “required” or “not a number.” Those are different sentences.',
                'Do not catch an exception and then continue with value 0 without saying so. The second example says so by printing ok.',
            ],
            'quiz' => [
                $q('What should you use for text that might not be an int?', 'TryParse and its bool', 'A catch that ignores every failure', 'Integer division', 'A dictionary of file names'),
                $q('What is throw for?', 'A promise that was broken', 'Every blank text box', 'Printing a line', 'Growing a list'),
                $q('When ok is false, what total should this lesson use?', '0, and it should say the parse failed', 'Whatever “lots” looks like as a price', '40, the previous success', 'The length of the text always'),
            ],
            'practice' => 'For the text "12", state ok and the value. For the text "twelve", state ok and the value you refuse to invent.',
        ]),
        $L([
            'section' => '7. A small system',
            'title' => 'A route is a path and a status',
            'keywords' => 'C# route, status code, handler',
            'lead' => 'A tiny web handler is a method that looks at a path and returns a status. /health returns 200. A path you do not know returns 404. The status is an int with a meaning you can write down. The body is text. This is the whole idea of a route, before any framework.',
            'heading' => 'Match the path, then choose the status',
            'para' => 'The path /health is known, so status is 200 and the body says ok. The path /missing is not known, so status is 404 and the body says absent. You do not return 200 for a path you did not recognise. A framework later calls your method. The decision is still yours.',
            'bullets' => [
                '200 means the request succeeded. 404 means you have no such path. Use the meanings other programs already expect.',
                'Compare the whole path. /health and /health/ are different strings until you decide to treat them alike.',
                'The body and the status are a pair. A 404 with a body that says ok is a lie.',
                'A method that returns the status is testable. You do not need a network to check the pair.',
            ],
            'rows' => [
                ['Path', 'The request text', '/health or something else.'],
                ['200', 'Known and fine', 'Body matches.'],
                ['404', 'Unknown path', 'Do not pretend success.'],
                ['Body', 'The text you return', 'A short reason.'],
                ['Match', 'Exact text here', 'Say if you ignore a trailing slash.'],
                ['Method', 'The handler', 'Path in, status out.'],
            ],
            'e1' => ['Example 1 — a known path', 'Status 200. Body ok.', <<<'CS'
string path = "/health";
bool known = true;
int status = 200;
string body = "ok";
Console.WriteLine($"path = {path}");
Console.WriteLine($"known = {known}");
Console.WriteLine($"status = {status}");
Console.WriteLine($"body = {body}");
CS],
            'e2' => ['Example 2 — an unknown path', 'Status 404. The body does not say ok.', <<<'CS'
string path = "/missing";
bool known = false;
int status = 404;
string body = "absent";
Console.WriteLine($"path = {path}");
Console.WriteLine($"known = {known}");
Console.WriteLine($"status = {status}");
Console.WriteLine($"body = {body}");
CS],
            'micro' => [
                'A desktop check is path == "/health". The samples store the bool and the status that check selects.',
                '500 is for a failure you did not turn into a clean 404. Do not use 500 for “unknown path.”',
                'Later frameworks add verbs such as GET and POST. The pair of path plus verb is the route. This lesson starts with the path.',
            ],
            'quiz' => [
                $q('What status fits an unknown path?', '404', '200', '0', 'The length of the path'),
                $q('What status fits /health in this lesson?', '200', '404', '500', 'null'),
                $q('Why return the status from a method?', 'You can test the decision without a network', 'Methods cannot return ints', 'The path is always known', 'WriteLine sets the status'),
            ],
            'practice' => 'Pick a status and a body for the path /books. Then pick them again for /nope.',
        ]),
        $L([
            'section' => '8. The project',
            'title' => 'Project: a reading notebook',
            'keywords' => 'C# project, notebook, budget, status',
            'lead' => 'The notebook stores three books, totals the pages, and answers a budget check with a bool. It also answers a tiny route: /books returns 200 and the total, and any other path returns 404. Every value is stored. Empty titles are allowed only as empty text, not as null, because a book you created has a title slot.',
            'heading' => 'Three books, one total, one route',
            'para' => 'Pages 12, 20, and 8 total 40. A budget of 30 is exceeded, so over is True. The path /books reports status 200 and the total. The path /other reports 404. The last index is 2. You can point at the type of every name.',
            'bullets' => [
                'Add page counts, not ids.',
                'over uses > so a total that equals the budget is not over.',
                'The route and the total share the same stored total. Do not recompute a second total that can drift.',
                'A missing title is "" with length 0 if the book exists. null would mean there is no book object, which is a different bug.',
            ],
            'rows' => [
                ['pages', 'What you add', 'Three books, three adds.'],
                ['total', 'The sum', '40.'],
                ['over', 'total > budget', 'Equal is allowed.'],
                ['/books', 'Status 200', 'The body carries the total.'],
                ['other path', 'Status 404', 'No invented book.'],
                ['last index', '2', 'Length 3.'],
            ],
            'e1' => ['Example 1 — the books and the budget', 'Total 40. Budget 30. over is True.', <<<'CS'
int length = 3;
int id0 = 1;
int pages0 = 12;
int id1 = 2;
int pages1 = 20;
int id2 = 3;
int pages2 = 8;
string title0 = "";
int titleLength = 0;
int total = 0;
total = total + pages0;
total = total + pages1;
total = total + pages2;
int budget = 30;
bool over = total > budget;
int lastIndex = length - 1;
Console.WriteLine($"book {id0} pages {pages0}");
Console.WriteLine($"book {id1} pages {pages1}");
Console.WriteLine($"book {id2} pages {pages2}");
Console.WriteLine($"titleLength = {titleLength}");
Console.WriteLine($"total = {total}");
Console.WriteLine($"over = {over}");
Console.WriteLine($"lastIndex = {lastIndex}");
CS],
            'e2' => ['Example 2 — the route uses that total', '/books is 200 and reports 40. /other is 404.', <<<'CS'
int total = 40;
string booksPath = "/books";
int booksStatus = 200;
string otherPath = "/other";
int otherStatus = 404;
Console.WriteLine($"path = {booksPath}");
Console.WriteLine($"status = {booksStatus}");
Console.WriteLine($"total = {total}");
Console.WriteLine($"path = {otherPath}");
Console.WriteLine($"status = {otherStatus}");
CS],
            'micro' => [
                'title0 is empty text on purpose, so the length 0 is a real measurement and not a crash.',
                'A fourth book means a new page store, a new add, and length 4. Forgetting the length is the off-by-one.',
                'The handler does not parse a new total from the path. The notebook is the source. The route only reports it.',
            ],
            'quiz' => [
                $q('What is the page total of 12, 20, and 8?', '40', '6', '30', '0'),
                $q('Is 40 over a budget of 30?', 'Yes', 'No', 'Only if a title is null', 'Only for /other'),
                $q('What status does /other get?', '404', '200', '40', '0'),
            ],
            'practice' => 'Add a fourth book of 2 pages in your head. Predict the total, whether a budget of 30 is exceeded, and the new last index.',
        ]),
    ];
}
