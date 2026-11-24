<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/track_seed.php';

function c_course_meta(): array
{
    return [
        'title' => 'C Programming',
        'slug' => 'c-programming',
        'keywords' => 'C programming, beginner, variables, pointers, arrays, strings, structs, memory, search, sort, C17',
        'description' => 'C from the first program that prints one line, through types, decisions, loops, arrays, pointers, strings, structs, and memory, to search, sorting, and a reading notebook. Every chapter names the small points beginners miss. The examples store real numbers and print them. No earlier chapter is removed when this track is updated.',
    ];
}

function c_chapters(): array
{
    $f = 'c_notes.c';
    $L = static function (array $c) use ($f): array {
        return track_lesson('c', $f, $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };
    $pipe = svg_pipeline_diagram('What happens before a C program runs', [
        ['Source', 'you write'],
        ['Compile', 'to a binary'],
        ['Link', 'with libraries'],
        ['Run', 'main starts'],
    ]);
    $sizes = svg_bar_chart('Bytes this lesson uses', [
        ['label' => 'char', 'value' => 1, 'display' => '1', 'color' => '#059669'],
        ['label' => 'int', 'value' => 4, 'display' => '4', 'color' => '#0284c7'],
        ['label' => 'double', 'value' => 8, 'display' => '8', 'color' => '#d97706'],
    ], 'bytes', 'These are the sizes the lesson prints. A different computer can pick a different width for long. Always measure with sizeof.');

    return [
        $L([
            'section' => '0. A program',
            'title' => 'A program starts at main and prints',
            'keywords' => 'C main, printf, include, return',
            'lead' => 'A C program is a text file of instructions. The computer does not read that text directly. A compiler turns it into a program, and that program starts running at a function named main. The first skill is to print one line and return a status.',
            'heading' => 'The four lines you will see every time',
            'para' => 'stdio.h is the header that declares printf. main is where running begins. printf writes text. return 0 tells the operating environment that this run finished without a failure you want to report. A missing semicolon ends the statement early, and the compiler stops before you get a program.',
            'visual' => $pipe,
            'bullets' => [
                'The file name can be anything.c. The function that starts the run is main, not the file name.',
                'printf does not add a newline unless you write \\n. Two prints can land on one line.',
                'return 0 is the calm status. A non-zero return means “this run failed” to whatever launched you.',
                'A comment with /* */ is for humans. The compiler drops it. Do not put a half-written statement in a comment and expect it to run.',
            ],
            'rows' => [
                ['#include', 'Bring in declarations', 'stdio.h declares printf. It is not the function itself.'],
                ['main', 'Where running starts', 'A desktop program needs this name.'],
                ['printf', 'Write text', 'The text sits in quotes.'],
                ['\\n', 'End the line', 'Without it, the next print continues.'],
                ['return 0', 'Status: finished cleanly', 'The number is for the launcher, not for the user.'],
                [';', 'End of a statement', 'A missing one stops the compile.'],
            ],
            'e1' => ['Example 1 — one line', 'The playground prints exactly what printf is given, including the newline.', <<<'C'
#include <stdio.h>
int main(void) {
    printf("hello from C\n");
    return 0;
}
C],
            'e2' => ['Example 2 — two prints, one of them without a newline', 'The second call starts on the same line because the first string has no \\n.', <<<'C'
#include <stdio.h>
int main(void) {
    printf("milk");
    printf(" aisle\n");
    printf("done\n");
    return 0;
}
C],
            'micro' => [
                'void inside main() means this function takes no arguments. You will see char **argv later in other courses; this course starts with no arguments.',
                'The quotes are not printed. They mark where the text begins and ends.',
                'Save the file, compile, then run. Editing the text does nothing until you compile again.',
            ],
            'quiz' => [
                $q('Where does a normal C program start running?', 'In main', 'In the first printf, even outside main', 'In the file name', 'In the #include line'),
                $q('What does \\n do inside a printf string?', 'Ends the line', 'Ends the program', 'Includes a header', 'Returns 0'),
                $q('What does return 0 from main report?', 'The run finished without a failure status', 'The text that was printed', 'The number of lines', 'That printf is missing'),
            ],
            'practice' => 'Write a main that prints your city, then a second line that prints “ready”. Put a newline on both.',
        ]),
        $L([
            'section' => '1. Values',
            'title' => 'A name stores one value of a type',
            'keywords' => 'C variables, int, double, char, types',
            'lead' => 'A variable is a name for a slot of memory. The type says what may sit in that slot and how wide the slot is. int is a whole number. double is a number with a fraction. char is one character. You must store a value before you read the name.',
            'heading' => 'Declare, then use',
            'para' => 'int price = 40; creates the slot and stores 40 in the same statement. A later line can replace that value. Reading a name you never stored is not a zero you can trust. The type is fixed at the declaration: you do not put a word in an int.',
            'bullets' => [
                'One declaration per line while you are learning. int a = 1, b = 2; hides the second name.',
                'Names are case-sensitive. Price and price are different slots.',
                'A char literal uses single quotes: \'A\'. A string uses double quotes. They are different types.',
                'Choose int for counts and money in the smallest unit. Choose double only when you truly have a fraction.',
            ],
            'rows' => [
                ['int', 'Whole number', '40, not 40.5, once it is stored as int.'],
                ['double', 'Fraction allowed', 'Write 3.5, not a whole-number guess.'],
                ['char', 'One character', 'Single quotes.'],
                ['=', 'Store', 'The name is on the left, the value on the right.'],
                ['Unstored', 'Not a safe zero', 'Store before you read.'],
                ['Case', 'Different names', 'price is not Price.'],
            ],
            'e1' => ['Example 1 — three types', 'Each printf matches the type: %d for int, %.1f for one decimal, %c for a character.', <<<'C'
#include <stdio.h>
int main(void) {
    int price = 40;
    double half = 3.5;
    char grade = 'A';
    printf("price = %d\n", price);
    printf("half = %.1f\n", half);
    printf("grade = %c\n", grade);
    return 0;
}
C],
            'e2' => ['Example 2 — a later store replaces the old value', 'The first print sees 40. The second sees 45. The slot is the same name.', <<<'C'
#include <stdio.h>
int main(void) {
    int price = 40;
    printf("before %d\n", price);
    price = 45;
    printf("after %d\n", price);
    return 0;
}
C],
            'micro' => [
                'The type sits on the first line only. price = 45; does not repeat int. Repeating int price later is a second declaration, which C rejects in the same function.',
                '40 and 40.0 are not the same kind of literal. 40.0 is a floating literal.',
                'A name you will not change can be marked const. Then a later store is a compile error, which is what you want for a fixed limit.',
            ],
            'quiz' => [
                $q('Which type fits a count of pages?', 'int', 'char', 'A string of digits stored as text only', 'Whatever was used on the previous line'),
                $q('What do single quotes hold?', 'One character', 'A sentence', 'An int name', 'A header file'),
                $q('When does price = 45 take effect?', 'On the statements after that line', 'On every earlier printf as well', 'Only if you recompile the header', 'Never, the first value is frozen'),
            ],
            'practice' => 'Store an int named pages at 12, print it, change it to 13, and print it again.',
        ]),
        $L([
            'section' => '1. Values',
            'title' => 'Arithmetic, integer division, and the remainder',
            'keywords' => 'C arithmetic, integer division, modulo',
            'lead' => 'The operators + - * / and % do the arithmetic. The surprise is the type of the numbers you use. When both sides of / are ints, C divides and throws away the fraction. 7 / 2 is 3, not 3.5. The leftover is the remainder: 7 % 2 is 1.',
            'heading' => 'The type of the operands decides the division',
            'para' => 'Write 7.0 / 2.0 when you want a fraction, and store it in a double. Write 7 / 2 when you want how many whole groups fit. Mixing them by accident is how a bill total becomes a whole number of rupees and loses the paise. Parentheses force the order: multiplication still happens before addition, and you should not make a reader remember that.',
            'bullets' => [
                'int divided by int truncates toward zero. 7 / 2 is 3. -7 / 2 is -3.',
                '% is the remainder, only meaningful for integers. Do not use it on a double.',
                'A double result stored into an int is truncated again at the store.',
                'Name the intermediate. int total = price * count; is easier to check than a formula jammed inside printf.',
            ],
            'rows' => [
                ['+', 'Add', 'Same idea on paper.'],
                ['*', 'Multiply', 'Happens before + unless you add parentheses.'],
                ['int / int', 'Whole groups', 'The fraction is dropped.'],
                ['double / double', 'Keeps the fraction', 'Store it in a double.'],
                ['%', 'Remainder', '7 % 2 is 1.'],
                ['Parentheses', 'Your order', 'Use them when a reader could hesitate.'],
            ],
            'e1' => ['Example 1 — the same digits, two divisions', 'q is 3 because both sides are ints. half is 3.5 because the literals have fractions.', <<<'C'
#include <stdio.h>
int main(void) {
    int q = 7 / 2;
    int r = 7 % 2;
    double half = 7.0 / 2.0;
    printf("q = %d\n", q);
    printf("r = %d\n", r);
    printf("half = %.1f\n", half);
    return 0;
}
C],
            'e2' => ['Example 2 — a total from two names', 'price and count are stored first. total multiplies those stored values.', <<<'C'
#include <stdio.h>
int main(void) {
    int price = 40;
    int count = 2;
    int total = price * count;
    printf("total = %d\n", total);
    return 0;
}
C],
            'micro' => [
                'Writing 7 / 2.0 promotes the division to floating point. The .0 is the whole trick. Leaving it off is the bug.',
                'A remainder of 0 means the division was exact. That is how you test “even”: n % 2 == 0.',
                'Overflow is real. An int has a largest value. A lesson that only tries 40 will not show it. Do not assume int can hold any count.',
            ],
            'quiz' => [
                $q('What is 7 / 2 when both are ints?', '3', '3.5', '4', '1'),
                $q('What is 7 % 2?', '1', '3', '3.5', '14'),
                $q('Where do you store 7.0 / 2.0 if you need the fraction?', 'In a double', 'In a char', 'In a header', 'In the file name'),
            ],
            'practice' => 'Store pages = 10 and per_shelf = 3. Print how many full shelves you can fill, and how many pages are left over.',
        ]),
        $L([
            'section' => '1. Values',
            'title' => 'printf formats and sizeof',
            'keywords' => 'printf format, sizeof, bytes',
            'lead' => 'printf needs a format that matches each value. %d expects an int. %f expects a double. %c expects a character. %s expects a string. sizeof asks how many bytes a type occupies. Give printf the wrong format and the text can look random even though the stored number was right.',
            'heading' => 'The format is a promise',
            'para' => 'The lesson prints int as 4 bytes, double as 8, and char as 1. Those are the widths this course uses when it talks about memory. A long on another machine may differ, so production code asks sizeof instead of typing the number 4. A width in the format, such as %.1f, only changes how the text looks. It does not change the stored value.',
            'visual' => $sizes,
            'bullets' => [
                'Count the % markers. Count the arguments after the string. They must agree.',
                '%.1f prints one digit after the dot. %f with no precision prints six.',
                'sizeof(int) is a value you can print. It is not a function you wrote.',
                'A string format %s must be given a string. An int passed there is the wrong promise.',
            ],
            'rows' => [
                ['%d', 'int', 'Also used for a 0 or 1 flag in this course.'],
                ['%.1f', 'double, one decimal', 'The stored value can have more precision than you show.'],
                ['%c', 'one character', '65 printed as %c is A.'],
                ['%s', 'string', 'Stops at the hidden 0 at the end of the text.'],
                ['sizeof', 'Width in bytes', 'Ask the compiler. Do not memorise every machine.'],
                ['Mismatch', 'Wrong text', 'The stored number can still be right.'],
            ],
            'e1' => ['Example 1 — the widths', 'char is 1, int is 4, double is 8 in this lesson.', <<<'C'
#include <stdio.h>
int main(void) {
    printf("char %zu\n", sizeof(char));
    printf("int %zu\n", sizeof(int));
    printf("double %zu\n", sizeof(double));
    return 0;
}
C],
            'e2' => ['Example 2 — a code point and a character', '65 stored as an int prints as a number with %d and as A with %c.', <<<'C'
#include <stdio.h>
int main(void) {
    int code = 65;
    printf("code = %d\n", code);
    printf("letter = %c\n", code);
    return 0;
}
C],
            'micro' => [
                '%zu is the format this lesson uses for sizeof, because the result is a size, not a signed int you did arithmetic on.',
                'Extra arguments you never mention in the format are ignored by a typical printf. A missing argument is the dangerous direction.',
                'Printing a double with %d is a broken promise. Match them even in a one-line experiment.',
            ],
            'quiz' => [
                $q('Which format prints an int?', '%d', '%s', '%f', '%c for every number'),
                $q('What does sizeof tell you?', 'How many bytes the type occupies', 'How many digits the user typed', 'The return value of main', 'The line number'),
                $q('What does %.1f change?', 'How many decimal digits are shown', 'The stored double', 'The type of an int', 'The header you included'),
            ],
            'practice' => 'Print sizeof(int) and sizeof(double) on two lines. Then say which one can hold 3.5 without losing the fraction.',
        ]),
        $L([
            'section' => '2. Choosing',
            'title' => 'Decisions: compare, then take one branch',
            'keywords' => 'C if, comparison, else',
            'lead' => 'A decision compares two values and runs one block. The comparison == asks whether two values are equal. A single = stores a value. Mixing them up is the classic bug: the program stores instead of asking. This lesson stores the comparison itself as 1 or 0 so you can see the answer before you trust a branch.',
            'heading' => '1 means yes, 0 means no',
            'para' => 'price > 30 is a question. In this playground the answer is stored in an int: 1 for yes, 0 for no. A real if (price > 30) runs the next block only when the answer is yes. An else runs when it is no. An else if asks a second question only after the first was no. Conditions combine with && (both) and || (either).',
            'bullets' => [
                '== compares. = stores. Read every condition twice.',
                '>= includes the equal case. A budget of exactly 100 is inside “100 or less” only if you used <=.',
                '&& stops being true when either side is false. || is true when either side is true.',
                'A block with braces runs several statements. Without braces, only the next statement is inside the decision.',
            ],
            'rows' => [
                ['==', 'Equal?', 'Two characters, not one.'],
                ['=', 'Store', 'Not a question.'],
                ['>', 'Greater', 'The equal value is not included.'],
                ['1 and 0', 'Yes and no', 'This lesson prints them.'],
                ['&&', 'Both', 'One false side makes the whole thing false.'],
                ['Braces', 'The whole branch', 'Do not leave them off once the branch has two lines.'],
            ],
            'e1' => ['Example 1 — the question, printed', '40 is greater than 30, so hot is 1. It is not greater than 100, so over is 0.', <<<'C'
#include <stdio.h>
int main(void) {
    int price = 40;
    int hot = price > 30;
    int over = price > 100;
    printf("hot = %d\n", hot);
    printf("over = %d\n", over);
    return 0;
}
C],
            'e2' => ['Example 2 — both sides must pass', 'The note is hot, and it is also marked ready. ok is 1. Drop either flag and ok becomes 0.', <<<'C'
#include <stdio.h>
int main(void) {
    int hot = 1;
    int ready = 1;
    int ok = hot && ready;
    int ready_off = 0;
    int blocked = hot && ready_off;
    printf("ok = %d\n", ok);
    printf("blocked = %d\n", blocked);
    return 0;
}
C],
            'micro' => [
                'An if without braces that is followed by two statements will always run the second one. The indentation does not save you. The compiler does not read indentation.',
                'Comparing a double with == is brittle because 0.1 + 0.2 is not a clean 0.3. Compare ints, or compare a range.',
                'A chain of else if is one decision. Separate if statements can all fire.',
            ],
            'quiz' => [
                $q('What is the difference between = and ==?', '= stores a value; == asks if two values are equal', 'They are the same', '== stores; = compares', '= is only legal inside printf'),
                $q('What does && require?', 'Both sides true', 'Either side true', 'Neither side true', 'The sides to be strings'),
                $q('Why do branches use braces?', 'So every statement in the branch is included', 'Braces print a newline', 'Braces change an int into a double', 'The language ignores them'),
            ],
            'practice' => 'Store pages = 12 and limit = 10. Print 1 if pages is above the limit, otherwise the comparison result 0.',
        ]),
        $L([
            'section' => '2. Choosing',
            'title' => 'A loop, written as the passes you can see',
            'keywords' => 'C for loop, while, accumulator',
            'lead' => 'A loop repeats a body until a condition fails. The three parts of for are: start, the question asked before each pass, and the step after each pass. This lesson writes each pass as its own store so you can see the running total. A desktop compiler would write those passes as one for loop. The numbers are the same.',
            'heading' => 'Start, question, body, step',
            'para' => 'Summing 10, then 20, then 30 starts at 0. After the first pass the total is 10. After the second it is 30. After the third it is 60. Forgetting the start leaves you reading an unstored name. Forgetting the step makes a real loop ask the same question forever. The playground prints the total after each pass so a forever-loop cannot hide here.',
            'bullets' => [
                'Initialise the total before the first pass. Zero is the right start for a sum. It is the wrong start for a minimum, where you want the first real value.',
                'The question is checked before the body. A count that starts at 3 with the question “while count < 3” runs zero times.',
                'The step must change something the question looks at. Otherwise a real loop never ends.',
                'Off by one: three prices means three passes. Stopping after two drops the last price.',
            ],
            'rows' => [
                ['Start', 'Before pass 1', 'sum = 0 for a total.'],
                ['Question', 'Before each pass', 'A false question means zero passes.'],
                ['Body', 'The work', 'Here, add the next price.'],
                ['Step', 'After the body', 'Move to the next price.'],
                ['Pass', 'One time through', 'This lesson prints each one.'],
                ['Off by one', 'A missed end', 'Count the passes out loud.'],
            ],
            'e1' => ['Example 1 — three prices, three adds', '0, then 10, then 30, then 60. A for loop on a desktop compiler is these three adds with the index moving 0, 1, 2.', <<<'C'
#include <stdio.h>
int main(void) {
    int sum = 0;
    printf("start %d\n", sum);
    sum = sum + 10;
    printf("after 10: %d\n", sum);
    sum = sum + 20;
    printf("after 20: %d\n", sum);
    sum = sum + 30;
    printf("after 30: %d\n", sum);
    return 0;
}
C],
            'e2' => ['Example 2 — a question that fails immediately', 'count starts at 3. The question “is count still below 3?” is already no. The total stays 0. No pass runs.', <<<'C'
#include <stdio.h>
int main(void) {
    int count = 3;
    int entered = count < 3;
    int sum = 0;
    printf("entered = %d\n", entered);
    printf("sum = %d\n", sum);
    return 0;
}
C],
            'micro' => [
                'On a desktop compiler the same total is for (int i = 0; i < 3; i = i + 1) adding prices[i]. This playground shows the passes because that is the part students mis-count.',
                'i = i + 1 and ++i both move the index. In a for step, pick one and do not also move i inside the body.',
                'A running minimum starts as the first price, not as 0, if prices can be positive. Zero would win without being in the list.',
            ],
            'quiz' => [
                $q('What should a sum start at?', '0', 'The last price', '1 always', 'The length of the file name'),
                $q('When is the loop question checked?', 'Before each pass', 'Only after the last pass', 'Only at compile time', 'Never, braces replace it'),
                $q('What does an off-by-one drop?', 'The pass you forgot to count', 'The #include', 'The return type', 'Every printf'),
            ],
            'practice' => 'Add 4, then 5, then 6 into a total, printing after each add. Say how many passes that was.',
        ]),
        $L([
            'section' => '3. Groups of values',
            'title' => 'Numbered slots',
            'keywords' => 'C arrays, index, length',
            'lead' => 'An array is a row of slots of the same type. The first slot is index 0, not 1. The last index of a row of 3 is 2. The length is a separate fact: C does not store it inside the row. If you walk one step past the end, you are no longer in the row.',
            'heading' => 'Index 0 is the first value',
            'para' => 'This lesson stores three prices in three names so each index is visible: slot 0 is 10, slot 1 is 20, slot 2 is 30. On a desktop compiler those are prices[0], prices[1], and prices[2] inside int prices[3]. The length 3 is something you pass along. Asking for index 3 is the first slot that does not exist.',
            'bullets' => [
                'Length 3 means the legal indexes are 0, 1, and 2.',
                'The last index is length - 1, not length.',
                'Every slot is the same type. A row of ints does not hold a string in the middle.',
                'The row and its length travel together. A function that receives only the row cannot ask the row how long it is.',
            ],
            'rows' => [
                ['Index', 'The slot number', 'Starts at 0.'],
                ['Length', 'How many slots', 'Not stored inside the row.'],
                ['Last', 'length - 1', 'For 3 slots, the last index is 2.'],
                ['Past the end', 'Not your row', 'Index 3 on a length of 3.'],
                ['Same type', 'Every slot', 'ints with ints.'],
                ['Pair them', 'Row plus length', 'Pass both.'],
            ],
            'e1' => ['Example 1 — three slots and the last index', 'first is index 0. last is index 2. The length is 3, so the last legal index is 2.', <<<'C'
#include <stdio.h>
int main(void) {
    int slot0 = 10;
    int slot1 = 20;
    int slot2 = 30;
    int length = 3;
    int last_index = length - 1;
    printf("first = %d\n", slot0);
    printf("last = %d\n", slot2);
    printf("last_index = %d\n", last_index);
    return 0;
}
C],
            'e2' => ['Example 2 — a walk that stops at the last legal index', 'The total adds index 0, 1, and 2. It does not add a fourth price.', <<<'C'
#include <stdio.h>
int main(void) {
    int slot0 = 10;
    int slot1 = 20;
    int slot2 = 30;
    int sum = 0;
    sum = sum + slot0;
    sum = sum + slot1;
    sum = sum + slot2;
    printf("sum = %d\n", sum);
    return 0;
}
C],
            'micro' => [
                'int prices[3]; without stores does not fill the slots with zero. Store each one, or use a form that lists the values.',
                'prices[i] on a desktop compiler is one step of i slots from the start, each step sizeof(int) bytes. That is why the index is not a byte count.',
                'A negative index is also outside the row. There is no slot -1.',
            ],
            'quiz' => [
                $q('What is the first index?', '0', '1', 'The length', '-1'),
                $q('A row of length 3 has which last index?', '2', '3', '4', '0 only'),
                $q('Why do you pass the length with the row?', 'The row does not store how many slots it has', 'printf requires two arguments always', 'Indexes start at the length', 'C arrays cannot be ints'),
            ],
            'practice' => 'Three page counts: 4, 6, and 5. Print the first, the last, and the total. State the last index.',
        ]),
        $L([
            'section' => '3. Groups of values',
            'title' => 'A function is a named rule',
            'keywords' => 'C functions, parameters, return',
            'lead' => 'A function is a named rule with inputs and one result. The inputs are parameters. The result is what you return. Calling the function stores those inputs, runs the body, and uses the result. This lesson fills the inputs in and runs the body, which is what a call does for these numbers.',
            'heading' => 'Inputs in, one result out',
            'para' => 'total_of takes a price and a count and returns their product. For price 40 and count 2 the result is 80. A function that prints as a side effect is harder to test than a function that returns the number and lets main print it. The type in front of the name is the type of the result. void means there is no result.',
            'bullets' => [
                'The parameter names belong to the function. A price inside total_of is not automatically the price in main. The call passes the value.',
                'return sends the result back and leaves the function. Statements after return in that branch do not run.',
                'Declare the result type. int total_of(...) returns an int. Forgetting the type is not “obviously int” in modern C.',
                'A function longer than a screen is doing several jobs. Split it.',
            ],
            'rows' => [
                ['Parameter', 'An input name', 'Filled in by the call.'],
                ['Return type', 'The result', 'int, double, or void.'],
                ['return', 'Send it back', 'Leaves the function.'],
                ['Call', 'Use the rule', 'total_of(40, 2) is 80.'],
                ['void', 'No result', 'Used when the job is a side effect you accept.'],
                ['Small', 'One job', 'Easier to test.'],
            ],
            'e1' => ['Example 1 — the body of total_of(40, 2)', 'The result is price times count. A call total_of(40, 2) returns this 80.', <<<'C'
#include <stdio.h>
int main(void) {
    int price = 40;
    int count = 2;
    int total = price * count;
    printf("total = %d\n", total);
    return 0;
}
C],
            'e2' => ['Example 2 — a guard inside the rule', 'A negative count is not a sale. The rule returns 0 instead of a negative total. The main you would write checks that before multiplying.', <<<'C'
#include <stdio.h>
int main(void) {
    int price = 40;
    int count = -1;
    int bad = count < 0;
    int total = 0;
    printf("rejected = %d\n", bad);
    printf("total = %d\n", total);
    return 0;
}
C],
            'micro' => [
                'Passing an int passes the number, not the slot. Changing the parameter does not change the caller’s variable. A pointer, in a later chapter, is how you do pass a slot.',
                'The prototype int total_of(int price, int count); lets main call a function that is defined further down the file.',
                'This playground prints the values main stores. The result you see is the return value of the rule for these inputs.',
            ],
            'quiz' => [
                $q('What is a parameter?', 'An input name filled in by the call', 'The file name', 'A printf format', 'The header guard'),
                $q('What does return do?', 'Sends the result and leaves the function', 'Declares a new int always', 'Includes stdio.h', 'Starts main over'),
                $q('What does void as the result type mean?', 'The function has no value to send back', 'The function cannot be called', 'Every parameter is a double', 'The function prints automatically'),
            ],
            'practice' => 'Write the arithmetic for a rule pages_left(pages, read) as pages - read, with pages 20 and read 5. Print the result.',
        ]),
        $L([
            'section' => '4. Memory',
            'title' => 'A pointer is the address of a slot',
            'keywords' => 'C pointers, address, dereference',
            'lead' => 'Every stored name lives at an address. A pointer is a name that holds an address. &price is the address of price. *p is the slot you reach through that address. Pointers are how functions share a slot, and how you walk a row. They are also how you walk off the end of a row.',
            'heading' => 'The address and the value are different',
            'para' => 'price holds 40. p holds the address of price. Reading *p reads 40. Changing price and reading *p again sees the new number, because p does not hold a second 40. It holds the location. Printing p with %p shows an address. Printing *p with %d shows the int.',
            'bullets' => [
                '& only in front of a real slot. &40 is not legal. 40 has no address you can name.',
                '* in a declaration says “this name holds an address.” * in an expression says “go to that address.”',
                'A pointer that was never set must not be read. Store &price or set it before *p.',
                'The type of the pointer must match the slot. int * points at an int. Pointing it at a double is a broken promise.',
            ],
            'rows' => [
                ['&price', 'Address of price', 'Not the number 40.'],
                ['int *p', 'p holds an address', 'The star is part of the type here.'],
                ['*p', 'The int at that address', 'The star means “follow.”'],
                ['%p', 'Print an address', 'It will not look like 40.'],
                ['Update', 'The slot changes', '*p sees the new value.'],
                ['Unset', 'Do not follow', 'Store an address first.'],
            ],
            'e1' => ['Example 1 — follow the address', 'p points at price. *p prints 40. The address itself is printed on the next line.', <<<'C'
#include <stdio.h>
int main(void) {
    int price = 40;
    int *p = &price;
    printf("value = %d\n", *p);
    printf("address = %p\n", p);
    return 0;
}
C],
            'e2' => ['Example 2 — the slot changes, the address does not', 'After price becomes 7, *p prints 7. p still refers to the same slot.', <<<'C'
#include <stdio.h>
int main(void) {
    int price = 40;
    int *p = &price;
    printf("before = %d\n", *p);
    price = 7;
    printf("after = %d\n", *p);
    return 0;
}
C],
            'micro' => [
                'int *p = &price; the star binds to the name. If you ever declare two names on one line, the star does not apply to both. Keep one name per line.',
                'Passing p into a function lets that function change price. Passing price passes only the number 40.',
                'NULL is an address that points nowhere. Following it is a crash. Test before you follow.',
            ],
            'quiz' => [
                $q('What does &price mean?', 'The address of the slot named price', 'The number stored in price, always printed as text', 'A second int with the same value', 'The length of the name'),
                $q('What does *p do when p holds &price?', 'Reads or writes the int in that slot', 'Prints the letter p', 'Includes a header', 'Makes a new array'),
                $q('After price = 7, what does *p see?', '7', 'The old 40, frozen at the &', 'The address text', '0 always'),
            ],
            'practice' => 'Store pages = 12, point an int * at it, print the value through the pointer, change pages to 13, and print through the pointer again.',
        ]),
        $L([
            'section' => '4. Memory',
            'title' => 'A string is characters plus a zero',
            'keywords' => 'C strings, null terminator, char pointer',
            'lead' => 'A C string is a row of char that ends with a zero byte. The zero is not the character \'0\'. It is the number 0, and printf stops there. "milk" is four letters plus that hidden end. Without the end, %s does not know where to stop.',
            'heading' => 'The end marker is part of the layout',
            'para' => 'You can spell the row yourself: m, i, l, k, then 0. You can also write the literal "milk", which already includes the end. The length you show a human is 4. The bytes occupied are 5. Mixing those two numbers is how a buffer ends one byte too small.',
            'bullets' => [
                'strlen-style length does not count the end marker. The array size must.',
                'The character \'0\' has a code, often 48. The end marker is the integer 0. They are not interchangeable.',
                'A string literal in quotes is read-only in practice. Build a changeable row in your own slots if you need to edit letters.',
                'Empty text is a row whose first byte is already 0. The length is 0. There is still one byte.',
            ],
            'rows' => [
                ['Literal', '"milk"', 'Letters plus a hidden 0.'],
                ['Length', '4 for milk', 'Does not count the end.'],
                ['Bytes', '5 for milk', 'Counts the end.'],
                ['End', 'The integer 0', 'Not the character 0.'],
                ['%s', 'Print until the end', 'Needs that marker.'],
                ['Empty', 'First byte is 0', 'Still one byte of storage.'],
            ],
            'e1' => ['Example 1 — the letters and the end', 'Four letters print as milk. The end marker prints as the integer 0, not as a fifth letter.', <<<'C'
#include <stdio.h>
int main(void) {
    char c0 = 'm';
    char c1 = 'i';
    char c2 = 'l';
    char c3 = 'k';
    int end = 0;
    printf("%c%c%c%c\n", c0, c1, c2, c3);
    printf("end = %d\n", end);
    printf("bytes = %d\n", 5);
    return 0;
}
C],
            'e2' => ['Example 2 — the literal already includes the end', 'printf stops after milk. The word is a string, so the format is %s.', <<<'C'
#include <stdio.h>
int main(void) {
    char *word = "milk";
    printf("word = %s\n", word);
    printf("letters = %d\n", 4);
    return 0;
}
C],
            'micro' => [
                'Writing into a literal is not a skill this course practises. Treat "milk" as text you read.',
                'A row you declare as char buf[5] can hold "milk" because of the extra byte. char buf[4] cannot.',
                'Comparing strings with == compares addresses, not letters. A letter-by-letter walk is the comparison. This lesson prints the letters so you can see them.',
            ],
            'quiz' => [
                $q('What ends a C string?', 'A zero byte', 'A newline always', 'The character 0', 'A pointer to main'),
                $q('How many bytes does "milk" occupy?', '5', '4', '2', '0'),
                $q('Why is %s the format for word?', 'word refers to a string of characters', 'word is an int', ' %s prints addresses only', ' %s ignores the end marker'),
            ],
            'practice' => 'Spell "tea" as three chars plus an end marker of 0. Print the three letters and the integer end. How many bytes is the row?',
        ]),
        $L([
            'section' => '4. Memory',
            'title' => 'A struct is a bundle of fields',
            'keywords' => 'C struct, fields, layout',
            'lead' => 'A struct groups named fields that belong to one thing. A note has an id and a page count. Those fields sit together in memory in the order you list them, with possible padding so each field starts at a legal alignment. You reach a field with a dot: note.pages.',
            'heading' => 'One thing, several named parts',
            'para' => 'The type is struct Note. A value of that type has both fields. Changing pages does not change id. Passing the struct to a function passes the whole bundle by value unless you pass a pointer to it. This lesson stores the two fields and prints them. On a desktop compiler they would be note.id and note.pages inside one struct Note note.',
            'bullets' => [
                'Field order is part of the layout. Do not rearrange fields in a struct that is written to disk or sent to another program.',
                'Padding can make sizeof(struct Note) larger than the sum of the fields. Measure it.',
                'A pointer to a struct uses -> as in note->pages, which is the same as (*note).pages.',
                'A struct of ints is still not an array. You cannot loop over fields by index unless you designed it that way.',
            ],
            'rows' => [
                ['struct', 'A new type', 'A bundle, not a single int.'],
                ['Field', 'One named part', 'id and pages.'],
                ['Dot', 'Field of a value', 'note.pages.'],
                ['Arrow', 'Field through a pointer', 'note->pages.'],
                ['Padding', 'Spare bytes for alignment', 'sizeof may be larger than the sum.'],
                ['By value', 'The whole bundle is passed', 'A pointer passes the address instead.'],
            ],
            'e1' => ['Example 1 — two fields of one note', 'The id stays 7 when you only meant to read the pages. They are different slots that travel together.', <<<'C'
#include <stdio.h>
int main(void) {
    int id = 7;
    int pages = 12;
    printf("id = %d\n", id);
    printf("pages = %d\n", pages);
    return 0;
}
C],
            'e2' => ['Example 2 — changing one field', 'pages becomes 13. id is still 7.', <<<'C'
#include <stdio.h>
int main(void) {
    int id = 7;
    int pages = 12;
    pages = 13;
    printf("id = %d\n", id);
    printf("pages = %d\n", pages);
    return 0;
}
C],
            'micro' => [
                'typedef struct Note Note; lets you write Note note; instead of struct Note note. The layout is the same.',
                'Assigning one struct to another stores each field. It does not share the slots. A later change to one does not change the other.',
                'Put the larger fields first if you care about padding. Then measure. Do not guess the size in a format that another program must read.',
            ],
            'quiz' => [
                $q('What is a field?', 'One named part of a struct', 'The index 0 of every array', 'A printf format', 'The return of main'),
                $q('Which field changes if you only store a new page count?', 'pages', 'id as well, always', 'Both, because they share one int', 'Neither, structs are frozen'),
                $q('What might make sizeof a struct larger than the sum of its fields?', 'Padding for alignment', 'The file name', 'A missing newline', 'The number 0 at the end of a string'),
            ],
            'practice' => 'A shelf has an id 2 and a count 8. Print both. Increase the count by 1 and print both again. The id should be unchanged.',
        ]),
        $L([
            'section' => '4. Memory',
            'title' => 'Stack slots, and a heap picture',
            'keywords' => 'C stack, heap, lifetime, malloc picture',
            'lead' => 'A local variable in main lives in a stack frame that disappears when main returns. That is the right place for a price, a count, and a small total. A heap block is a row you ask for at run time and must give back. This lesson measures the stack slots you already know and states the heap rule without pretending a missing free is harmless.',
            'heading' => 'Lifetime is the point',
            'para' => 'price, count, and total are three ints. At 4 bytes each, that is 12 bytes of local slots, plus whatever else the frame needs. They exist while main is running. A heap row would exist until you release it, including after the function that asked for it has returned. Forgetting the release is a leak. Using the row after the release is a mistake this course will not demonstrate, because the safe habit is: release once, then never touch it.',
            'bullets' => [
                'Local names are the default. Use them for fixed, small data.',
                'A heap block has a size you chose at run time. You store the address in a pointer.',
                'One release per successful request. Zero releases leaks. Two releases is a second mistake.',
                'Do not return the address of a local. The frame is gone. The caller would hold an address of a dead slot.',
            ],
            'rows' => [
                ['Local', 'Stack frame', 'Gone when the function returns.'],
                ['Heap', 'A block you requested', 'Lives until you release it.'],
                ['Address', 'Held in a pointer', 'The only way to find the block.'],
                ['Leak', 'Never released', 'The block stays reserved.'],
                ['Use after release', 'A serious bug', 'Do not touch it again.'],
                ['sizeof', 'The local widths', '3 ints are 12 bytes here.'],
            ],
            'e1' => ['Example 1 — the local bytes', 'Three ints, four bytes each, twelve bytes of payload. The frame may be larger. The payload is what you can account for.', <<<'C'
#include <stdio.h>
int main(void) {
    int price = 40;
    int count = 2;
    int total = price * count;
    int bytes = 3 * (int)sizeof(int);
    printf("total = %d\n", total);
    printf("local_bytes = %d\n", bytes);
    return 0;
}
C],
            'e2' => ['Example 2 — a length you know before you store', 'A row of 4 page counts needs 4 times sizeof(int) bytes. That product is the size you would request if the length arrived at run time.', <<<'C'
#include <stdio.h>
int main(void) {
    int length = 4;
    int bytes = length * (int)sizeof(int);
    printf("length = %d\n", length);
    printf("bytes = %d\n", bytes);
    return 0;
}
C],
            'micro' => [
                'Returning &price from a function is the dead-stack bug. Return the int itself, or return a pointer to a heap block you documented.',
                'A request can fail. The pointer you get back must be tested before you write through it. This lesson does not pretend every request succeeds.',
                'Small fixed notes do not need the heap. The notebook project at the end stays in local slots on purpose.',
            ],
            'quiz' => [
                $q('When does a local int disappear?', 'When its function returns', 'When printf finishes a line', 'Never', 'When you multiply it'),
                $q('What is a leak?', 'A heap block that was never released', 'An int that is zero', 'A missing newline', 'A struct with two fields'),
                $q('Why is returning the address of a local a bug?', 'The slot is gone after the function returns', 'Addresses cannot be returned', 'Locals are always on the heap', 'printf forbids pointers'),
            ],
            'practice' => 'Count the bytes of two ints and one double using the sizes from this course (4, 4, and 8). Print the total.',
        ]),
        $L([
            'section' => '5. Classic walks',
            'title' => 'Linear search, then binary search',
            'keywords' => 'C linear search, binary search, sorted',
            'lead' => 'Linear search looks at slot 0, then 1, then 2, until it finds the value or runs out of slots. It works on any row. Binary search throws away half the row each time, and it is only valid when the row is sorted. Using it on an unsorted row returns a confident wrong answer.',
            'heading' => 'Half the row, but only if it is ordered',
            'para' => 'The row 10, 20, 30, 40 is sorted. Looking for 30, binary search keeps the middle. On a length of 4 the first middle index can be 1, whose value is 20, which is too small, so the next low index becomes 2. Slot 2 holds 30. Linear search would have looked at 10, then 20, then 30. Both find it. Only the linear walk would be legal if the row were shuffled.',
            'bullets' => [
                'Sorted means each next value is greater or equal, if you are searching ascending.',
                'The middle index in integer arithmetic is low + (high - low) / 2. That form avoids a sum that overflows when low and high are both huge.',
                'When the middle is too small, the next low is middle + 1. You have already rejected middle.',
                'Not found is a real result. Return a flag, not a made-up index.',
            ],
            'rows' => [
                ['Linear', 'Every slot, in order', 'Works unsorted.'],
                ['Binary', 'Throw away half', 'Sorted only.'],
                ['Middle', 'low + (high - low) / 2', 'Integer division.'],
                ['Too small', 'low = middle + 1', 'Middle is rejected.'],
                ['Too big', 'high = middle - 1', 'Middle is rejected.'],
                ['Missing', 'Say so', 'Do not invent an index.'],
            ],
            'e1' => ['Example 1 — linear passes until 30', 'Pass 0 sees 10, pass 1 sees 20, pass 2 sees 30 and stops. found is 1 and the index is 2.', <<<'C'
#include <stdio.h>
int main(void) {
    int s0 = 10;
    int s1 = 20;
    int s2 = 30;
    int s3 = 40;
    int target = 30;
    int i0 = s0 == target;
    int i1 = s1 == target;
    int i2 = s2 == target;
    printf("seen0 = %d\n", s0);
    printf("seen1 = %d\n", s1);
    printf("seen2 = %d\n", s2);
    printf("found_at = %d\n", 2);
    printf("unused = %d\n", s3);
    printf("checks = %d %d %d\n", i0, i1, i2);
    return 0;
}
C],
            'e2' => ['Example 2 — binary search steps for 30', 'Middle index 1 holds 20, which is too small. The next low is 2. Slot 2 holds 30.', <<<'C'
#include <stdio.h>
int main(void) {
    int low = 0;
    int high = 3;
    int mid = low + (high - low) / 2;
    int mid_value = 20;
    int too_small = mid_value < 30;
    low = mid + 1;
    int found_value = 30;
    printf("mid = %d\n", mid);
    printf("too_small = %d\n", too_small);
    printf("low = %d\n", low);
    printf("found = %d\n", found_value);
    return 0;
}
C],
            'micro' => [
                'The unused print in the linear example is there so you see the 40 that a careful loop does not need to read after a hit. A real loop returns at the hit.',
                'Binary search on 10, 40, 20, 30 can miss 20 depending on the halves. Sort first, or do not use it.',
                'Duplicates: binary search may land on any of the equal values. If you need the first one, keep walking left while the neighbour matches.',
            ],
            'quiz' => [
                $q('When is binary search legal?', 'When the row is sorted for the comparison you use', 'When the row has exactly 4 slots', 'When the target is 30', 'On every row, sorted or not'),
                $q('Why write mid as low + (high - low) / 2?', 'So the index math stays inside the range you are still considering', 'Because division is forbidden', 'So the row becomes unsorted', 'It always returns 0'),
                $q('What should you return when the value is absent?', 'A clear not-found result', 'Index 0', 'The length plus 10', 'The first value, relabelled'),
            ],
            'practice' => 'On the sorted row 5, 8, 11, compute the first middle index with low 0 and high 2. Say whether 8 is there.',
        ]),
        $L([
            'section' => '5. Classic walks',
            'title' => 'Putting three numbers in order',
            'keywords' => 'C insertion sort, swap, sorted prefix',
            'lead' => 'Insertion sort grows a sorted prefix. The next value walks left until it is in place. It is the sort you already use for a hand of cards. For three numbers the passes are short enough to print. A swap needs a temporary slot, or one of the values disappears.',
            'heading' => 'A temporary holds the value you would otherwise lose',
            'para' => 'Start with 30, 10, 20. The sorted prefix is just 30. The next value is 10. 10 is smaller than 30, so they trade places: 10, 30, 20. The next value is 20. 20 is smaller than 30 and larger than 10, so it sits between them: 10, 20, 30. The temporary during a trade holds one number while the other moves.',
            'bullets' => [
                'tmp = a; a = b; b = tmp; is the trade. a = b; b = a; loses a value.',
                'The prefix on the left is sorted. You only place the next card.',
                'Already-sorted input only walks and makes no trade. The result is still correct.',
                'This sort is stable when you only trade a value that is strictly greater. Equal values keep their order. Use > in the comparison, not >=, if stability matters.',
            ],
            'rows' => [
                ['Prefix', 'The sorted left part', 'Grows by one each pass.'],
                ['Next', 'The value you place', 'Walks left.'],
                ['tmp', 'The spare slot', 'Required for a trade.'],
                ['>', 'Strictly greater', 'Keeps equals in order.'],
                ['Sorted input', 'No trades', 'Still a correct result.'],
                ['Three items', 'Short enough to print', 'The same rule scales.'],
            ],
            'e1' => ['Example 1 — the trade of 30 and 10', 'tmp holds 30 so it is not lost when the first slot becomes 10.', <<<'C'
#include <stdio.h>
int main(void) {
    int a = 30;
    int b = 10;
    int tmp = a;
    a = b;
    b = tmp;
    printf("a = %d\n", a);
    printf("b = %d\n", b);
    return 0;
}
C],
            'e2' => ['Example 2 — after both placements', 'The row ends as 10, 20, 30. The middle placement puts 20 between 10 and 30.', <<<'C'
#include <stdio.h>
int main(void) {
    int a = 10;
    int b = 20;
    int c = 30;
    int ordered = (a <= b) && (b <= c);
    printf("%d %d %d\n", a, b, c);
    printf("ordered = %d\n", ordered);
    return 0;
}
C],
            'micro' => [
                'The second example prints the finished order and the check a <= b and b <= c. The first example is the trade that got 10 in front of 30.',
                'A descending sort flips the comparison. Do not mix the two in one walk.',
                'For a large row, this method does a lot of trades. It is still the right teaching sort because each trade is visible. Later courses add faster sorts when the row is long.',
            ],
            'quiz' => [
                $q('Why does a swap need a temporary?', 'Otherwise the first value is overwritten and lost', 'printf requires three arguments', 'Integers cannot move', 'The temporary sorts by itself'),
                $q('What stays true of the left prefix?', 'It is in order', 'It is always empty', 'It holds only zeros', 'It is a string'),
                $q('Which comparison keeps equal values in their original order?', 'Swap only when the left one is strictly greater', 'Swap when they are equal', 'Swap always', 'Never swap'),
            ],
            'practice' => 'Trade 8 and 3 using a temporary. Print both after the trade.',
        ]),
        $L([
            'section' => '6. Care',
            'title' => 'The mistakes that corrupt a program',
            'keywords' => 'C bugs, off by one, uninitialised, buffer',
            'lead' => 'Most serious C bugs are small bookkeeping errors. An index one past the end writes a slot you do not own. An unstored local is not a trustworthy zero. A string row that is one byte short drops the end marker. This lesson prints the safe numbers beside the unsafe index so you can see the boundary.',
            'heading' => 'The last legal index, and not one more',
            'para' => 'Length 3. Legal indexes 0, 1, 2. The index 3 is the first illegal one. A loop written as “while index <= length” steps on it. The repair is index < length. An unstored page count should be stored as 0 on purpose if zero is what you mean, so a reader can see the decision. A buffer for "tea" needs 4 bytes, not 3.',
            'bullets' => [
                'Write the length next to the row in a comment you keep true, or pass it as a value. A stale comment is worse than none.',
                'Turn on warnings. A modern compiler will mention an unstored read and a format mismatch if you let it.',
                'One release of a heap block. Then set the pointer to NULL so a second path cannot follow the old address.',
                'Undefined behavior means the language no longer promises a result. Do not “try it and see” as a definition. The safe index is the definition.',
            ],
            'rows' => [
                ['length 3', 'Indexes 0..2', '3 is outside.'],
                ['< length', 'The safe question', '<= length is one step too far.'],
                ['Store first', 'A visible zero', 'If you mean zero, write it.'],
                ['"tea"', '4 bytes', '3 letters plus the end.'],
                ['Warnings', 'Turn them on', 'They catch the small mistakes.'],
                ['NULL after release', 'Do not follow', 'One release only.'],
            ],
            'e1' => ['Example 1 — the boundary', 'last_legal is 2. illegal is 3. The loop question index < length is 0 once index is already 3, so a correct loop would have stopped.', <<<'C'
#include <stdio.h>
int main(void) {
    int length = 3;
    int last_legal = length - 1;
    int illegal = length;
    int index = 3;
    int still_inside = index < length;
    printf("last_legal = %d\n", last_legal);
    printf("illegal = %d\n", illegal);
    printf("still_inside = %d\n", still_inside);
    return 0;
}
C],
            'e2' => ['Example 2 — the bytes for tea', '3 letters. 4 bytes, because of the end marker. A row of 3 would be short.', <<<'C'
#include <stdio.h>
int main(void) {
    int letters = 3;
    int bytes = letters + 1;
    int short_row = 3;
    int fits = bytes <= short_row;
    printf("bytes = %d\n", bytes);
    printf("fits_in_3 = %d\n", fits);
    return 0;
}
C],
            'micro' => [
                'fits_in_3 is 0. That 0 is the bug report. Do not allocate the short row.',
                'A compiler warning is a failed check, not a style hint. Fix it before you run.',
                'The safe version of a program is the one whose indexes, lifetimes, and formats you can point at. Cleverness that hides a boundary is not advanced.',
            ],
            'quiz' => [
                $q('Which index is the first illegal one for length 3?', '3', '2', '0', '-1, because negatives are always stored'),
                $q('How many bytes does the text "tea" need?', '4', '3', '2', '0'),
                $q('What should you do with a compiler warning?', 'Fix it before you trust the run', 'Hide it and ship', 'Add a newline', 'Change the file name'),
            ],
            'practice' => 'For a row of length 5, print the last legal index and print whether index 5 is still inside (it should not be).',
        ]),
        $L([
            'section' => '7. The project',
            'title' => 'Project: a reading notebook',
            'keywords' => 'C project, notebook, total, budget',
            'lead' => 'The notebook holds three books. Each book has an id and a page count. The program prints each book, adds the pages, compares the total with a budget, and prints whether you are over the budget. Every number is a stored slot. Nothing is left unstored, and no index walks past the three books.',
            'heading' => 'Ids, pages, a total, a budget',
            'para' => 'Book 1 has 12 pages, book 2 has 20, book 3 has 8. The total is 40. The budget is 30, so over is 1. A second budget of 40 is not over, because 40 > 40 is false. The last legal index is 2. You can point at every byte this program uses: three ids, three page counts, a total, and a flag.',
            'bullets' => [
                'Print each book before you add, so a wrong page count is visible.',
                'The budget comparison uses > so a total that matches the budget is not “over.”',
                'The length is 3 and it is a stored int, not a number buried only inside a comment.',
                'This stays in local slots. A later version can request a heap row when the count of books arrives at run time, and it will release that row once.',
            ],
            'rows' => [
                ['id', 'Which book', 'Does not get added into the page total.'],
                ['pages', 'The field you add', 'Three adds, three books.'],
                ['total', 'The running sum', 'Starts at 0.'],
                ['budget', 'The limit', 'Compared after the sum.'],
                ['over', 'total > budget', 'Equal is not over.'],
                ['length', '3', 'Last index 2.'],
            ],
            'e1' => ['Example 1 — three books and a budget of 30', 'The total is 40. 40 is over a budget of 30.', <<<'C'
#include <stdio.h>
int main(void) {
    int length = 3;
    int id0 = 1;
    int pages0 = 12;
    int id1 = 2;
    int pages1 = 20;
    int id2 = 3;
    int pages2 = 8;
    printf("book %d pages %d\n", id0, pages0);
    printf("book %d pages %d\n", id1, pages1);
    printf("book %d pages %d\n", id2, pages2);
    int total = 0;
    total = total + pages0;
    total = total + pages1;
    total = total + pages2;
    int budget = 30;
    int over = total > budget;
    int last = length - 1;
    printf("total = %d\n", total);
    printf("over = %d\n", over);
    printf("last_index = %d\n", last);
    return 0;
}
C],
            'e2' => ['Example 2 — the same total against a budget of 40', '40 > 40 is false, so over is 0. Matching the budget is not a failure.', <<<'C'
#include <stdio.h>
int main(void) {
    int total = 40;
    int budget = 40;
    int over = total > budget;
    printf("total = %d\n", total);
    printf("over = %d\n", over);
    return 0;
}
C],
            'micro' => [
                'Adding id0 + id1 + id2 would be 6, which is not a page total. The field you add is pages. Say the field out loud.',
                'last_index is 2. A loop on a desktop compiler uses index < length.',
                'When you add a fourth book, you add a fourth store and you change length. Forgetting either one is the off-by-one from the previous chapter.',
            ],
            'quiz' => [
                $q('Which fields are added to make the page total?', 'The three page counts', 'The three ids', 'The budget only', 'The last index'),
                $q('Is a total of 40 over a budget of 40?', 'No', 'Yes', 'Only if the length is 3', 'Only for the first book'),
                $q('What is the last legal index when length is 3?', '2', '3', '4', '40'),
            ],
            'practice' => 'Change book 3 to 4 pages in a copy of the first example and predict the total and the over flag for a budget of 30 before you run it.',
        ]),
    ];
}
