<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/track_seed.php';

function c_chapters_depth(): array
{
    $f = 'c_notes.c';
    $L = static function (array $c) use ($f): array {
        return track_lesson('c', $f, $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };
    $ascii = svg_bar_chart('Three character codes', [
        ['label' => 'A', 'value' => 65, 'display' => '65', 'color' => '#0284c7'],
        ['label' => 'B', 'value' => 66, 'display' => '66', 'color' => '#059669'],
        ['label' => '0', 'value' => 48, 'display' => '48', 'color' => '#d97706'],
    ], '', 'A character is stored as a small integer. The letter and the digit are different codes, and the digit 0 is not the number 0.');

    return [
        $L([
            'section' => '8. Characters and choices',
            'title' => 'A character is a small integer',
            'keywords' => 'C char, ASCII, character code',
            'lead' => 'A char is a small integer that we agree to show as a letter. The letter A is the number 65 in the usual encoding. The digit character 0 is the number 48, which is not the integer 0. Printing with %c draws the letter. Printing with %d shows the number.',
            'heading' => 'The code and the picture are the same slot',
            'para' => 'Store 65 and print it as a character, and you see A. Store 66 and you see B. The next letter is the next integer, so 65 + 1 is B. That arithmetic is why people write loops over letters. It is also why a mistake that adds 1 to a letter silently changes the letter. The digit character is 48, so if you read the character 7 and want the integer 7, you subtract 48. Forgetting the subtraction leaves you with 55 and a wrong total.',
            'visual' => $ascii,
            'bullets' => [
                '%c prints the picture. %d prints the code. Same slot, two views.',
                'A and B are neighbours: their codes differ by 1. Do not assume that about letters in every language. This lesson is the basic encoding.',
                'The character 0 has code 48. The integer 0 is a different value in the same kind of slot.',
                'A char may be signed on some compilers. For codes 0 through 127 you are safe. This lesson stays in that range.',
            ],
            'rows' => [
                ['char', 'A small integer', 'Often one byte.'],
                ['65', 'The letter A', 'In the usual encoding.'],
                ['48', 'The character 0', 'Not the integer 0.'],
                ['%c', 'Draw the picture', 'printf.'],
                ['%d', 'Show the code', 'printf.'],
                ['+ 1', 'The next code', 'A becomes B.'],
            ],
            'e1' => ['Example 1 — 65 is A, and 66 is B', 'The same numbers printed as characters and as integers.', <<<'C'
#include <stdio.h>
int main(void) {
    int aye = 65;
    int bee = 66;
    printf("%c %c\n", aye, bee);
    printf("%d %d\n", aye, bee);
    return 0;
}
C],
            'e2' => ['Example 2 — the digit character is not the integer', 'Code 55 is the character 7. Subtract 48 and the integer is 7. Adding the codes instead of the integers totals 55, not 7.', <<<'C'
#include <stdio.h>
int main(void) {
    int digit_code = 55;
    int as_number = digit_code - 48;
    printf("picture %c\n", digit_code);
    printf("number %d\n", as_number);
    return 0;
}
C],
            'micro' => [
                'A literal in single quotes, such as the letter itself, is another way to write the code. This playground stores the integer so the number is visible.',
                'Subtract 48 only for the digit characters 0 through 9. Subtracting 48 from the letter A produces 17, which is not a digit value.',
                'Newline is a character too, code 10. It is not the two characters backslash and n once printf has read the string.',
            ],
            'quiz' => [
                $q('What is stored for the letter A in this lesson?', '65', '0', 'The word aye', '48'),
                $q('How do you print the picture rather than the code?', 'With %c', 'With %d', 'With %f', 'By adding 48'),
                $q('What is the integer value of the digit character 7?', '7, after subtracting 48 from 55', '55, and you add it directly', '0', '65'),
            ],
            'practice' => 'The letter C is 67. What is the letter two codes after A? What integer do you get from digit code 50?',
        ]),
        $L([
            'section' => '8. Characters and choices',
            'title' => 'A question can stop before the second half',
            'keywords' => 'C short circuit, logical and, logical or',
            'lead' => '&& is true only when both sides are true. || is true when either side is true. Both can stop early. If the left side of && is already false, the right side is not evaluated. If the left side of || is already true, the right side is not evaluated. That skip is how a later check avoids using a pointer that the first check found empty.',
            'heading' => 'The left side is a guard',
            'para' => 'You want “the count is positive and the average is above 10.” If the count is 0, the average is not a thing you can compute. The left side count > 0 is 0, so the && is 0 without looking at a division by the count. This lesson stores the two sides as 0 or 1 so you can see the guard. In a full compiler the right-hand call would not run. Here you see the values the guard would use.',
            'bullets' => [
                'Left to right. The guard is the left operand, not the right.',
                '&& stops on a false left side. || stops on a true left side.',
                'A single & and a single | are bit operations. They evaluate both sides. They are not the guards.',
                'The result you store is 1 or 0. A later if uses that result. This playground prints the 1 or 0.',
            ],
            'rows' => [
                ['&&', 'Both must be true', 'False on the left skips the right.'],
                ['||', 'Either may be true', 'True on the left skips the right.'],
                ['Guard', 'The left test', 'Often “this pointer exists.”'],
                ['&', 'Bits, both sides', 'Not the guard.'],
                ['1 and 0', 'What you print', 'The comparison result.'],
                ['Order', 'Left first', 'Writing the risky part on the left defeats the guard.'],
            ],
            'e1' => ['Example 1 — a zero count makes the && fail', 'has_rows is 0. The combined ok is 0. You never needed the average.', <<<'C'
#include <stdio.h>
int main(void) {
    int count = 0;
    int has_rows = count > 0;
    int average_high = 1;
    int ok = has_rows && average_high;
    printf("has_rows = %d\n", has_rows);
    printf("ok = %d\n", ok);
    return 0;
}
C],
            'e2' => ['Example 2 — a true left side makes || succeed', 'ready is 1, so the combined value is 1 even though the backup flag is 0.', <<<'C'
#include <stdio.h>
int main(void) {
    int ready = 1;
    int backup = 0;
    int go = ready || backup;
    int neither = 0 || 0;
    printf("go = %d\n", go);
    printf("neither = %d\n", neither);
    return 0;
}
C],
            'micro' => [
                'In a desktop compiler, the skipped side does not run, so a side effect on the right, such as a store, does not happen. Do not hide a store on the right of && and expect it always.',
                'Put the cheap, safe test on the left. A null check belongs there, the dereference on the right.',
                '!!value turns a number into 1 or 0. You rarely need it if you compare explicitly.',
            ],
            'quiz' => [
                $q('When does && skip the right side?', 'When the left side is false', 'When the left side is true', 'Always', 'Never'),
                $q('When does || skip the right side?', 'When the left side is true', 'When both sides are false', 'When you use a single |', 'Only if the values are characters'),
                $q('Why is a single & different?', 'It works on bits and does not guard the right side', 'It is a spelling of &&', 'It compares strings', 'It always returns 0'),
            ],
            'practice' => 'Let count be 4 and average_high be 0. What is has_rows, and what is has_rows && average_high?',
        ]),
        $L([
            'section' => '8. Characters and choices',
            'title' => 'Named cases choose one result',
            'keywords' => 'C switch, case, default',
            'lead' => 'A switch picks one case that matches an integer and runs from there. It is a list of named values, not a chain you have to read as nested comparisons. A break leaves the switch. Without it, running continues into the next case. That fall-through is useful on purpose and a bug by accident.',
            'heading' => 'One code, one result, unless you fall through',
            'para' => 'Status 200 means ok, 404 means missing, and anything else means other. This lesson stores the result with comparisons so the playground can show it: code 404 selects missing. Fall-through is the second idea. If case 200 has no break, a compiler continues into case 201. People use that when two codes share a result. They hit it by accident when they forget the break and both results run.',
            'bullets' => [
                'The switched value is an integer or an enum. A string does not switch in C.',
                'case labels are constant integers. You cannot case on a value computed at run time as the label.',
                'default runs when nothing matched. Omit it only when every integer you might see has a case, which is rare.',
                'break leaves the switch. continue would leave a loop, not the switch. Do not mix them up.',
            ],
            'rows' => [
                ['switch', 'Pick by an integer', 'One value.'],
                ['case', 'A constant to match', '200, 404.'],
                ['break', 'Leave the switch', 'Stops fall-through.'],
                ['default', 'Nothing matched', 'Your other bucket.'],
                ['Fall-through', 'No break', 'The next case runs too.'],
                ['String', 'Not a switch value', 'Compare strings another way.'],
            ],
            'e1' => ['Example 1 — 404 selects missing', 'The three comparisons are the three cases. Exactly one of them is 1.', <<<'C'
#include <stdio.h>
int main(void) {
    int code = 404;
    int is_ok = code == 200;
    int is_missing = code == 404;
    int is_other = code != 200 && code != 404;
    printf("ok = %d\n", is_ok);
    printf("missing = %d\n", is_missing);
    printf("other = %d\n", is_other);
    return 0;
}
C],
            'e2' => ['Example 2 — a shared result is an intentional fall-through', '200 and 201 both mean success in this story. The flag is 1 for 201 because it is in that pair. 500 is not.', <<<'C'
#include <stdio.h>
int main(void) {
    int code = 201;
    int success = code == 200 || code == 201;
    int server = code == 500;
    printf("success = %d\n", success);
    printf("server = %d\n", server);
    return 0;
}
C],
            'micro' => [
                'On a desktop compiler the cases look like case 404: followed by statements and a break. This playground prints the 1s and 0s those cases would test.',
                'Duplicate case labels are a compile error. Two cases for 404 will not compile.',
                'Forgetting default means an unexpected code does nothing. Sometimes that is silent success. Often it is a dropped status.',
            ],
            'quiz' => [
                $q('What does break do inside a switch?', 'It leaves the switch so the next case does not run', 'It restarts the program', 'It compares strings', 'It is optional and changes nothing'),
                $q('Which values can be case labels?', 'Constant integers', 'Any string', 'A value read from a file at run time, as the label', 'Floating numbers in this lesson'),
                $q('What runs when no case matches, if you wrote it?', 'default', 'case 0 always', 'The first case', 'Nothing can be written for that'),
            ],
            'practice' => 'Code 200. Which of is_ok, is_missing, and is_other is 1? Then add 201 as a second success code and say what changes for code 201.',
        ]),
        $L([
            'section' => '8. Characters and choices',
            'title' => 'The inner name hides the outer name',
            'keywords' => 'C scope, shadowing, block',
            'lead' => 'A name lives in the block where it is declared, from that line to the closing brace. A block inside can declare the same name. Inside the inner block, the inner name is the one you see. The outer name is still there, hidden, and it returns when the inner block ends. Hiding a name is called shadowing, and it is a common way to update the wrong slot.',
            'heading' => 'Two slots can share a spelling',
            'para' => 'An outer price is 40. An inner block that declares price as 5 and adds 1 changes the inner slot to 6. The outer price is still 40 after the block. If the inner block did not declare a new price, the assignment would change the outer 40. The spelling is the same. The slot depends on the braces. This lesson uses two different names in the playground, outer_price and inner_price, so both slots are visible at once, which is what the braces do on a desktop compiler.',
            'bullets' => [
                'The inner declaration creates a new slot. It does not mean “use the outer one.”',
                'After the closing brace, the inner slot is gone. Using that name outside is a compile error if no outer name exists.',
                'Shadowing is legal and usually a mistake in a short function. Pick a different name.',
                'A for-loop variable declared in the loop header lives for the loop, not after it, in modern C.',
            ],
            'rows' => [
                ['Block', 'A pair of braces', 'A lifetime.'],
                ['Outer', 'Declared first', 'Lives longer.'],
                ['Inner', 'The same spelling', 'A different slot.'],
                ['Hide', 'The inner name wins', 'Until its brace.'],
                ['After', 'The outer name returns', 'The inner slot is gone.'],
                ['No new declaration', 'The assignment hits the outer slot', 'The bug or the intent.'],
            ],
            'e1' => ['Example 1 — the inner slot changes and the outer slot does not', 'inner becomes 6. outer stays 40.', <<<'C'
#include <stdio.h>
int main(void) {
    int outer_price = 40;
    int inner_price = 5;
    inner_price = inner_price + 1;
    printf("inner = %d\n", inner_price);
    printf("outer = %d\n", outer_price);
    return 0;
}
C],
            'e2' => ['Example 2 — one slot, so the add is visible outside', 'There is no second price. Adding 1 changes the only price from 40 to 41.', <<<'C'
#include <stdio.h>
int main(void) {
    int price = 40;
    price = price + 1;
    printf("price = %d\n", price);
    return 0;
}
C],
            'micro' => [
                'Parameters are names in the function’s block. A local with the same spelling hides the parameter. You then compute with the local and return as if you had used the argument.',
                'Declare the name in the smallest block that needs it. A price at the top of main that only one branch uses invites a later branch to read a stale value.',
                'The playground uses two spellings so both numbers can be printed. On paper, draw two boxes with the same word price and an arrow to the inner box while you are inside the braces.',
            ],
            'quiz' => [
                $q('What does an inner int price do?', 'It creates a new slot that hides the outer price', 'It deletes the outer price', 'It is a compile error always', 'It adds the two prices'),
                $q('After the inner block ends, which price do you see?', 'The outer one, if it was declared', 'The inner one', 'Neither', 'Their sum'),
                $q('What happens if the inner block assigns to price and does not declare it?', 'The outer slot changes', 'A new slot appears anyway', 'The program prints both', 'The name becomes a string'),
            ],
            'practice' => 'Outer count is 3. An inner count starts at 10 and you add 2. What does each printf show inside the block, and what does the outer count show after the block?',
        ]),
        $L([
            'section' => '9. Names, width, and addresses',
            'title' => 'A promise not to store again',
            'keywords' => 'C const, read only, mutable',
            'lead' => 'const on a variable is a promise that this name will not be used to store a new value after it is initialised. The compiler rejects a later assignment. It does not make the memory magical. It stops you, at compile time, from changing the value through that name.',
            'heading' => 'The second store is the mistake',
            'para' => 'A tax rate of 5 should not be overwritten by a later line that meant to set a different rate. const int rate = 5; documents that. A desktop compiler then rejects rate = 8. This lesson shows the value you meant to keep, 5, and a separate new_rate of 8 that would have been a different name. The bug is reusing rate for a second idea. The promise forces the second idea into a new name.',
            'bullets' => [
                'const is checked by the compiler. It is not a runtime lock.',
                'You may read a const as often as you like. You may not assign to it.',
                'const int *p means you will not store through that pointer. The pointer itself might still be aimed at a new address, depending on where the const sits.',
                'A #define is a different tool: text replacement. const is a typed name. Prefer const for a number with a type.',
            ],
            'rows' => [
                ['const', 'No later assignment through this name', 'A compile-time promise.'],
                ['Initialiser', 'The one store', 'rate = 5 at the declaration.'],
                ['Read', 'Allowed', 'printf can use it.'],
                ['Second store', 'Rejected', 'On a desktop compiler.'],
                ['New name', 'A different idea', 'new_rate, not a silent overwrite.'],
                ['Pointer const', 'Where the keyword sits', 'Changes whether the pointer or the target is fixed.'],
            ],
            'e1' => ['Example 1 — the rate stays 5', 'The printed rate is the initialiser. The other number has its own name.', <<<'C'
#include <stdio.h>
int main(void) {
    const int rate = 5;
    int new_rate = 8;
    printf("rate = %d\n", rate);
    printf("new_rate = %d\n", new_rate);
    return 0;
}
C],
            'e2' => ['Example 2 — a value you do intend to update is not const', 'tries starts at 1 and becomes 2. const would have been the wrong promise.', <<<'C'
#include <stdio.h>
int main(void) {
    int tries = 1;
    tries = tries + 1;
    printf("tries = %d\n", tries);
    return 0;
}
C],
            'micro' => [
                'The playground will not reject a second assignment the way a compiler does. Do not remove const in a real file to silence the compiler. The compiler found a second store.',
                'const on a parameter means the function promises not to write that argument through the pointer. Callers can then pass a const string.',
                'A const object can still be changed by a different, non-const name that aliases the same memory. The promise is about the name, and a cast can defeat it. Do not cast const away.',
            ],
            'quiz' => [
                $q('What does const stop?', 'A later assignment through that name', 'Reading the value', 'All arithmetic in the file', 'Printing'),
                $q('When is const the wrong promise?', 'When the value is supposed to change, such as a try counter', 'When the value is a rate you want frozen', 'When you printf it', 'When it is an int'),
                $q('How is const different from a text macro?', 'const has a type and a scope', 'They are the same', 'const replaces characters in the source', 'A macro is checked as a pointer'),
            ],
            'practice' => 'Decide const or not for a page limit that the program should not change, and for a page count that grows as you read. Write the declaration of each.',
        ]),
        $L([
            'section' => '9. Names, width, and addresses',
            'title' => 'A cast keeps the whole number and drops the fraction',
            'keywords' => 'C cast, truncation, integer conversion',
            'lead' => 'A cast tells the compiler to treat a value as a different type. Casting a floating value to int drops the fraction. It does not round. 7.9 becomes 7. The fraction is gone, and the next line cannot recover it from the int.',
            'heading' => 'Truncation is toward zero for these numbers',
            'para' => 'A measurement of 7.9 pages is not 8 pages. If you store it in an int, you have 7. Dividing two ints, 7 and 2, already produced 3 before any cast, because the division itself was integer. Casting the ints to double before the division gives 3.5. The cast has to happen before the operation you care about. Casting the integer result 3 to double gives 3.0, which looks like a double and has already lost the remainder.',
            'bullets' => [
                'A cast does not round to nearest. Add 0.5 yourself only if you truly want that, and know it fails for negatives.',
                'Integer division happens before a later cast. Parentheses matter.',
                'Casting a double to int also fails to fit if the number is huge. This lesson stays small.',
                'A cast is a conversion you can see. An implicit conversion is the same risk without the marker. Turn on warnings.',
            ],
            'rows' => [
                ['(int)', 'Drop the fraction', '7.9 becomes 7.'],
                ['(double)', 'Allow a fraction', 'Use it before you divide.'],
                ['7 / 2', 'Integer division', 'Result 3.'],
                ['(double)7 / 2', 'A real division', 'Result 3.5.'],
                ['(double)(7 / 2)', 'Too late', '3 becomes 3.0.'],
                ['Round', 'Not what a cast does', 'Do not call truncation rounding.'],
            ],
            'e1' => ['Example 1 — 7.9 becomes 7', 'The double keeps the fraction. The int does not.', <<<'C'
#include <stdio.h>
int main(void) {
    double pages = 7.9;
    int whole = 7;
    printf("pages = %.1f\n", pages);
    printf("whole = %d\n", whole);
    return 0;
}
C],
            'e2' => ['Example 2 — cast before the division', '7 / 2 is 3. Casting 7 to double first yields 3.5. Casting the 3 afterward yields 3.0.', <<<'C'
#include <stdio.h>
int main(void) {
    int early = 7 / 2;
    double timely = 7.0 / 2;
    double late = 3.0;
    printf("early = %d\n", early);
    printf("timely = %.1f\n", timely);
    printf("late = %.1f\n", late);
    return 0;
}
C],
            'micro' => [
                'Negative truncation toward zero means -7.9 becomes -7, not -8, in modern C. Do not assume it goes to -8.',
                'printf %.1f shows one decimal place. The stored double may have more. The format is not the value.',
                'A cast of a pointer is a different and sharper tool. This lesson is only about numbers.',
            ],
            'quiz' => [
                $q('What does casting 7.9 to int produce?', '7', '8', '7.9', '79'),
                $q('Why is (double)(7 / 2) equal to 3.0?', 'The integer division already happened', 'double cannot hold 3.5', 'The parentheses are ignored', '7 and 2 are doubles already'),
                $q('When must the cast to double happen if you want a fraction?', 'Before the division', 'After you store the int', 'Only in the printf', 'Never'),
            ],
            'practice' => 'Compute 5 / 2 as an int, and (double)5 / 2. Write both results.',
        ]),
        $L([
            'section' => '9. Names, width, and addresses',
            'title' => 'A small whole number wraps',
            'keywords' => 'C integer overflow, unsigned wrap, limits',
            'lead' => 'An integer type has a highest value. For an unsigned 8-bit slot the highest value is 255. Add 1 and the slot becomes 0. It wrapped. A signed slot is not allowed to wrap in modern C; signed overflow is undefined, which means the compiler may do something you did not predict. This lesson shows the wrap you can define: unsigned arithmetic, modelled on 256.',
            'heading' => 'Past the top, back to zero',
            'para' => '255 + 1 does not become 256 inside 8 bits. The stored pattern is 0. 0 - 1 wraps to 255. People meet this when a countdown uses an unsigned count and goes below zero, suddenly becoming a huge number, and a loop that was supposed to stop runs for a long time. Check the type before you subtract.',
            'bullets' => [
                'Unsigned wrap is defined. It is modulo 2 to the width.',
                'Signed overflow is not a wrap you should rely on. Avoid it.',
                'A wider type, such as a long, postpones the problem. It does not remove the existence of a top.',
                'Mixing signed and unsigned in one comparison can turn a small negative into a huge unsigned value. Compare matching types.',
            ],
            'rows' => [
                ['Width', 'How many patterns fit', '8 bits: 0 through 255 unsigned.'],
                ['Top', '255 here', 'The last legal value.'],
                ['+ 1', 'Wraps to 0', 'Defined for unsigned.'],
                ['- 1 from 0', 'Wraps to 255', 'The countdown bug.'],
                ['Signed', 'Do not rely on a wrap', 'Undefined overflow.'],
                ['Mix', 'A dangerous comparison', 'Keep the types the same.'],
            ],
            'e1' => ['Example 1 — 255 plus 1 is 0 in 8 unsigned bits', 'The modulo shows the wrap. 256 would be the mathematical sum, and it does not fit.', <<<'C'
#include <stdio.h>
int main(void) {
    int top = 255;
    int wrapped = (top + 1) % 256;
    int math_sum = top + 1;
    printf("wrapped = %d\n", wrapped);
    printf("math_sum = %d\n", math_sum);
    return 0;
}
C],
            'e2' => ['Example 2 — zero minus one wraps to 255', 'An unsigned countdown step. The result is not -1.', <<<'C'
#include <stdio.h>
int main(void) {
    int count = 0;
    int stepped = (count - 1 + 256) % 256;
    printf("stepped = %d\n", stepped);
    return 0;
}
C],
            'micro' => [
                'The % 256 in the sample is the teaching model of an 8-bit unsigned slot. A real unsigned char does this without the modulo written in the source.',
                '255 + 1 in a plain int does not wrap, because a plain int is wider than 8 bits. The wrap is about the type you stored into, not about mathematics.',
                'If a loop condition is count >= 0 and count is unsigned, the condition is always true. That is the infinite loop.',
            ],
            'quiz' => [
                $q('What is 255 + 1 in an unsigned 8-bit slot?', '0', '256', '-1', '128'),
                $q('What is 0 - 1 in that same unsigned slot?', '255', '-1', '0', '1'),
                $q('Should you rely on signed overflow wrapping?', 'No. It is undefined', 'Yes, it always becomes 0', 'Yes, it always becomes negative', 'Only in printf'),
            ],
            'practice' => 'An unsigned 8-bit slot holds 250. Add 10 using modulo 256. What is stored?',
        ]),
        $L([
            'section' => '9. Names, width, and addresses',
            'title' => 'Bits side by side: and, or, and a shift',
            'keywords' => 'C bitwise and, bitwise or, shift',
            'lead' => 'You can combine integers one bit at a time. & keeps a bit only if both sides have it. | keeps a bit if either side has it. A left shift moves every bit one place toward the high end and fills a zero, which doubles a small non-negative number. These are not && and ||. Those are the guards from the earlier chapter.',
            'heading' => 'A mask keeps the bits you name',
            'para' => '5 is 101 in binary. 3 is 011. 5 & 3 is 001, which is 1. 5 | 3 is 111, which is 7. 5 << 1 is 1010, which is 10. A mask of 1, which is 001, used with &, reads the last bit: 5 & 1 is 1, so 5 is odd. You use this for flags packed in one integer: bit 0 means ready, bit 1 means hot, and you set or test one without disturbing the other.',
            'bullets' => [
                '& and | always look at both sides. They do not skip.',
                '<< 1 doubles a non-negative value until it overflows the width.',
                '>> 1 halves a non-negative value, dropping the last bit.',
                'A flag variable starts at 0. You turn a flag on with | and test it with &.',
            ],
            'rows' => [
                ['&', 'Both bits', '5 & 3 is 1.'],
                ['|', 'Either bit', '5 | 3 is 7.'],
                ['<<', 'Shift left', '5 << 1 is 10.'],
                ['>>', 'Shift right', '5 >> 1 is 2.'],
                ['Mask', 'The bits you care about', '1 reads the last bit.'],
                ['&&', 'Not this chapter', 'That one is a logical guard.'],
            ],
            'e1' => ['Example 1 — 5 and 3, bit by bit', 'The and is 1. The or is 7. One left shift is 10.', <<<'C'
#include <stdio.h>
int main(void) {
    int a = 5;
    int b = 3;
    int both = a & b;
    int either = a | b;
    int shifted = a << 1;
    printf("both = %d\n", both);
    printf("either = %d\n", either);
    printf("shifted = %d\n", shifted);
    return 0;
}
C],
            'e2' => ['Example 2 — pack two flags in one integer', 'Bit 0 is ready, bit 1 is hot. Turning hot on does not clear ready. Testing ready still yields 1.', <<<'C'
#include <stdio.h>
int main(void) {
    int flags = 0;
    int ready_bit = 1;
    int hot_bit = 2;
    flags = flags | ready_bit;
    flags = flags | hot_bit;
    int ready = (flags & ready_bit) != 0;
    int hot = (flags & hot_bit) != 0;
    printf("flags = %d\n", flags);
    printf("ready = %d\n", ready);
    printf("hot = %d\n", hot);
    return 0;
}
C],
            'micro' => [
                'Write (flags & ready_bit) != 0. The precedence of & is lower than you expect next to some operators, and the parentheses keep the test obvious.',
                'Shifting a signed negative is not this lesson. Stay with non-negative flags.',
                'Exclusive-or flips bits that differ. This playground’s arithmetic view is the and, the or, and the shift, which are the ones you need for flags.',
            ],
            'quiz' => [
                $q('What is 5 & 3?', '1', '7', '15', '0'),
                $q('What is 5 << 1 for this small positive value?', '10', '4', '2', '6'),
                $q('How do you test whether the ready bit is on?', 'And the flags with the ready mask and see if the result is non-zero', 'Add the flags to the mask', 'Use && on the two flag names only', 'Shift the flags by 8'),
            ],
            'practice' => 'Start from flags 0. Turn on only the hot bit, value 2. What is flags, and what does the ready test return?',
        ]),
        $L([
            'section' => '10. Text you measure',
            'title' => 'Walk until the zero byte',
            'keywords' => 'C string length, null terminator, strlen',
            'lead' => 'A C string is characters followed by a byte whose value is 0. The length is how many characters you pass before that zero. The zero itself is not counted. If the zero is missing, a walk never knows where the text ends.',
            'heading' => 'Count, and do not count the terminator',
            'para' => 'The characters of milk are four letters and then a zero. A length walk sees m, i, l, k, then stops. The length is 4. The slot that holds the zero is a fifth byte you had to reserve. People allocate 4 bytes for milk and there is no room for the zero, so the walk continues into whatever is next in memory. Reserve length + 1.',
            'bullets' => [
                'The terminator is the integer 0, not the character digit 0.',
                'strlen’s result does not include the terminator. The buffer size must.',
                'An empty string is a zero in the first slot. Its length is 0, and it still occupies one byte.',
                'A walk that is not bounded by a maximum can run off a broken string. A safe walk also stops at the buffer size.',
            ],
            'rows' => [
                ['Characters', 'The text', 'milk is four.'],
                ['Terminator', 'A zero byte after them', 'Not counted.'],
                ['Length', 'Characters before the zero', '4.'],
                ['Buffer', 'Length plus one', '5 bytes for milk.'],
                ['Empty', 'Zero in the first slot', 'Length 0.'],
                ['Missing zero', 'No end', 'The walk is unsafe.'],
            ],
            'e1' => ['Example 1 — four letters, length 4', 'The codes are 109, 105, 108, 107, then 0. The count stops at the 0.', <<<'C'
#include <stdio.h>
int main(void) {
    int c0 = 109;
    int c1 = 105;
    int c2 = 108;
    int c3 = 107;
    int terminator = 0;
    int length = 4;
    printf("c0 = %d\n", c0);
    printf("length = %d\n", length);
    printf("terminator = %d\n", terminator);
    return 0;
}
C],
            'e2' => ['Example 2 — the buffer is one larger than the length', 'length 4 needs 5 slots. A buffer of 4 would have no terminator.', <<<'C'
#include <stdio.h>
int main(void) {
    int length = 4;
    int buffer = length + 1;
    printf("buffer = %d\n", buffer);
    return 0;
}
C],
            'micro' => [
                '109 is m, 105 is i, 108 is l, 107 is k. You do not need the letters to count. You need the zero that follows them.',
                'The if chain is the unrolled walk. A for loop on a desktop compiler stops at the first zero the same way.',
                'sizeof on a pointer is not the length of the text. sizeof on a real array includes the terminator. Those two facts are a frequent mix-up once the array is passed to a function and becomes a pointer.',
            ],
            'quiz' => [
                $q('Does the length include the zero byte?', 'No', 'Yes', 'Only for an empty string', 'Only if you use %c'),
                $q('How many bytes do you reserve for milk?', '5', '4', '2', '0'),
                $q('What marks the end?', 'A byte with value 0', 'The character digit 0', 'A newline always', 'The letter k'),
            ],
            'practice' => 'The word id is two letters. What is the length, and what is the buffer size including the terminator?',
        ]),
        $L([
            'section' => '10. Text you measure',
            'title' => 'Two words, one character at a time',
            'keywords' => 'C string compare, lexicographic, equality',
            'lead' => 'Two strings are equal when every character matches and they end together. You compare the first characters, then the next, and you stop at the first difference. That difference also tells you which word would come first in a dictionary. Comparing the pointers compares addresses, not letters.',
            'heading' => 'Stop at the first character that differs',
            'para' => 'milk and milk match at all four letters and both terminators, so they are equal. milk and mist differ at the third letter: l is code 108, s is code 115. Because 108 is smaller, milk comes first. You do not look at the fourth letter once the third has decided. A length check is not enough: milk and mil have different lengths, and milk and silk have the same length and are not equal.',
            'bullets' => [
                'Compare codes, which is what the characters are. The earlier letter in the encoding comes first for these basic letters.',
                'If one string ends and the other does not, the terminator 0 is smaller than any letter, so the shorter word comes first when the shared prefix matched.',
                '== on two pointers asks if they are the same address. Two equal texts can live in two places.',
                'A bounded compare also takes a maximum number of characters, so a missing terminator cannot walk forever.',
            ],
            'rows' => [
                ['Equal', 'Every character matches', 'Including ending together.'],
                ['First difference', 'The decision', 'Later letters are not consulted.'],
                ['Shorter prefix', 'The zero loses to a letter', 'mil comes before milk.'],
                ['Pointer ==', 'Same address', 'Not the same text.'],
                ['Same length', 'Not enough', 'silk and milk.'],
                ['Bound', 'A maximum count', 'Safety against a missing zero.'],
            ],
            'e1' => ['Example 1 — the third letter decides', '108 versus 115. milk is first because its code is smaller. The equal pair prints 1.', <<<'C'
#include <stdio.h>
int main(void) {
    int third_milk = 108;
    int third_mist = 115;
    int milk_before_mist = third_milk < third_mist;
    int same = 109 == 109 && 105 == 105 && 108 == 108 && 107 == 107;
    printf("milk before mist = %d\n", milk_before_mist);
    printf("same word = %d\n", same);
    return 0;
}
C],
            'e2' => ['Example 2 — the terminator makes the shorter word smaller', 'After m i l, one word has 0 and the other has k, code 107. 0 is smaller, so mil comes first.', <<<'C'
#include <stdio.h>
int main(void) {
    int shorter_next = 0;
    int longer_next = 107;
    int shorter_first = shorter_next < longer_next;
    printf("shorter first = %d\n", shorter_first);
    return 0;
}
C],
            'micro' => [
                'Case matters. A and a are different codes. A dictionary that ignores case is a different compare.',
                'This ordering is the encoding order, which matches A to Z for the basic letters. It is not every language’s dictionary order.',
                'The samples compare the deciding codes directly so the playground can print them. A function on a desktop compiler walks until it finds that same pair.',
            ],
            'quiz' => [
                $q('When are two strings equal?', 'Every character matches and they end together', 'They have the same length', 'Their pointers are different', 'Their first letters match'),
                $q('Why does mil come before milk?', 'After the shared letters, the terminator is smaller than k', 'mil has more letters', 'k is zero', 'Pointers decide order'),
                $q('What does pointer equality test?', 'Whether both names refer to the same address', 'Whether the texts match', 'The length', 'The third character'),
            ],
            'practice' => 'Codes: cat and car differ at the third letter, t = 116 and r = 114. Which word comes first?',
        ]),
        $L([
            'section' => '10. Text you measure',
            'title' => 'One row, then the next row',
            'keywords' => 'C two dimensional array, row, column',
            'lead' => 'A table is a row of rows. Three days of two readings are six slots, but the layout is two indexes: which day, then which reading. The first index picks the row. The second picks the column. Swapping them reads a different slot, or a slot that is not in the table.',
            'heading' => 'Day first, reading second',
            'para' => 'Day 0 has readings 2 and 4. Day 1 has 6 and 8. Day 2 has 1 and 3. The value at day 1, reading 0 is 6. There are 3 rows and 2 columns, so the legal column indexes are 0 and 1, not 0, 1, and 2. People use the row count as the column index because both loops look the same. The total of all six readings is 24. A walk must use the column count in the inner loop.',
            'bullets' => [
                'Index order is part of the meaning. [day][reading] is not [reading][day].',
                'The inner size is the column count. It can differ from the row count.',
                'A flat list of six ints can hold the same numbers. You then compute index = day * columns + reading.',
                'The last legal column is columns - 1, the same off-by-one as a single row.',
            ],
            'rows' => [
                ['Row', 'The first index', 'The day.'],
                ['Column', 'The second index', 'The reading.'],
                ['columns', '2', 'Not 3.'],
                ['Slot', 'day * columns + reading', 'The flat index.'],
                ['Total', 'All six', '24 in the example.'],
                ['Swap', 'A different slot', 'Or an illegal one.'],
            ],
            'e1' => ['Example 1 — day 1, reading 0 is 6', 'The flat index is 1 * 2 + 0 = 2. That slot holds 6.', <<<'C'
#include <stdio.h>
int main(void) {
    int d0r0 = 2;
    int d0r1 = 4;
    int d1r0 = 6;
    int d1r1 = 8;
    int d2r0 = 1;
    int d2r1 = 3;
    int columns = 2;
    int day = 1;
    int reading = 0;
    int flat = day * columns + reading;
    printf("flat = %d\n", flat);
    printf("value = %d\n", d1r0);
    return 0;
}
C],
            'e2' => ['Example 2 — the total walks every column of every day', '2 + 4 + 6 + 8 + 1 + 3 is 24. Using three columns would ask for a reading that does not exist.', <<<'C'
#include <stdio.h>
int main(void) {
    int total = 0;
    total = total + 2;
    total = total + 4;
    total = total + 6;
    total = total + 8;
    total = total + 1;
    total = total + 3;
    printf("total = %d\n", total);
    return 0;
}
C],
            'micro' => [
                'On a desktop compiler the table is int reading[3][2]. The second size is required so the compiler knows how long a row is.',
                'The flat formula day * columns + reading matches the way those rows sit in memory, one after another.',
                'A jagged shape, where each day has a different number of readings, is not this rectangle. You would store a length per row.',
            ],
            'quiz' => [
                $q('Which index is the day in this lesson?', 'The first', 'The second', 'The flat index only', 'Neither'),
                $q('What is the flat index of day 1, reading 0, with 2 columns?', '2', '1', '0', '3'),
                $q('Why is the column count not 3?', 'There are 2 readings per day, and 3 is the number of days', 'Columns always equal rows', 'The total is 3', 'Reading 0 is illegal'),
            ],
            'practice' => 'What is the flat index of day 2, reading 1? Which value from the lesson sits there?',
        ]),
        $L([
            'section' => '9. Names, width, and addresses',
            'title' => 'The address of the next slot',
            'keywords' => 'C pointer arithmetic, stride, sizeof',
            'lead' => 'Adding 1 to a pointer does not add 1 byte. It adds the size of the thing the pointer points at. An int pointer moves by sizeof(int), which is 4 bytes in this course’s picture. That is why p + 1 is the next int, and why a pointer and an index are two views of the same step.',
            'heading' => 'The step is the size of one slot',
            'para' => 'If the first int sits at address 1000 and each int is 4 bytes, the next int sits at 1004, and the one after at 1008. p + 2 is two slots later, 8 bytes, not 2 bytes. A char pointer would move by 1, because a char is one byte in this picture. Using the wrong pointer type walks the row in the wrong stride and reads half an int or skips one.',
            'bullets' => [
                'The type of the pointer chooses the stride.',
                'p + i and the index i are the same slot, when p points at the first element.',
                'Subtracting two pointers of the same type counts slots, not bytes.',
                'Walking one past the last slot is still outside the row, even if the arithmetic is neat.',
            ],
            'rows' => [
                ['p', 'Address of the first int', '1000 in the picture.'],
                ['sizeof(int)', 'The stride', '4 here.'],
                ['p + 1', 'The next int', 'Address 1004.'],
                ['p + 2', 'Two ints later', 'Address 1008.'],
                ['char *', 'Stride 1', 'A different pointer type.'],
                ['Past the end', 'Not a slot', 'Arithmetic does not make it legal to read.'],
            ],
            'e1' => ['Example 1 — the next int is 4 bytes later', 'Base 1000. One step is 1004. Two steps are 1008.', <<<'C'
#include <stdio.h>
int main(void) {
    int base = 1000;
    int stride = 4;
    int next = base + stride;
    int two = base + 2 * stride;
    printf("next = %d\n", next);
    printf("two = %d\n", two);
    return 0;
}
C],
            'e2' => ['Example 2 — a char step is 1 byte', 'The same base plus one char is 1001, not 1004. The type changed the stride.', <<<'C'
#include <stdio.h>
int main(void) {
    int base = 1000;
    int char_stride = 1;
    int int_stride = 4;
    printf("char step = %d\n", base + char_stride);
    printf("int step = %d\n", base + int_stride);
    return 0;
}
C],
            'micro' => [
                'The numbers 1000 and 4 are a picture. Real addresses are whatever the run gives you, and you print them with care. The stride is the lesson.',
                'sizeof(int) is not always 4 on every machine. Use sizeof rather than the digit 4 in real code. This lesson uses 4 so the addition is obvious.',
                'Adding an int to a pointer is legal. Adding two pointers is not. Subtracting two pointers of the same array is how you count the slots between them.',
            ],
            'quiz' => [
                $q('What does adding 1 to an int pointer add?', 'The size of one int, not one byte', 'One byte always', 'The value stored in the int', 'The length of a string'),
                $q('If the base is 1000 and the stride is 4, where is p + 2?', '1008', '1002', '1004', '8'),
                $q('Why does a char pointer move differently?', 'Its stride is one byte in this picture', 'Chars cannot have addresses', 'Addition is illegal on them', 'They always start at 0'),
            ],
            'practice' => 'Base 2000, stride 4. Compute the address of index 3. How many bytes is that from the base?',
        ]),
        $L([
            'section' => '10. Text you measure',
            'title' => 'The name of the row is the address of the first slot',
            'keywords' => 'C array decay, pointer to first element',
            'lead' => 'When you pass an array to a function, what arrives is the address of the first slot, not a copy of every slot. The function cannot ask that address how long the row was. You pass the length as a second argument. Inside the function, the first slot is the same memory as the caller’s first slot, so a store through the pointer changes the caller’s row.',
            'heading' => 'The length travels separately',
            'para' => 'The caller has three prices, 10, 20, and 30, and a length of 3. The function receives the address of the 10 and the 3. It can read the first slot and see 10. It cannot discover the 3 from the address. If the caller passes 5 by mistake, the function will believe there are five slots. The extra two are not yours. This is the same fact as the numbered-slots chapter, seen from the pointer side.',
            'bullets' => [
                'The array name in an expression is the address of element 0.',
                'The callee’s store to element 0 is visible to the caller. The row was not copied.',
                'The length is a separate parameter. A wrong length is a wrong walk.',
                'sizeof on the parameter is the size of a pointer, not the size of the caller’s array.',
            ],
            'rows' => [
                ['First slot', 'What the pointer aims at', '10 in the example.'],
                ['Length', 'Passed beside it', '3.'],
                ['No copy of the row', 'Same memory', 'Writes are visible.'],
                ['sizeof pointer', 'Not the row length', 'Do not use it as the count.'],
                ['Wrong length', 'A walk off the row', 'The function cannot check.'],
                ['Element 0', 'pointer[0] or *pointer', 'Two spellings of the first slot.'],
            ],
            'e1' => ['Example 1 — the first slot and the separate length', 'The function’s view is first = 10 and length = 3. Both were passed. Neither was inferred.', <<<'C'
#include <stdio.h>
int main(void) {
    int first = 10;
    int second = 20;
    int third = 30;
    int length = 3;
    int seen_first = first;
    int seen_length = length;
    printf("seen_first = %d\n", seen_first);
    printf("seen_length = %d\n", seen_length);
    return 0;
}
C],
            'e2' => ['Example 2 — a write to the first slot changes the caller’s price', 'The callee sets the shared slot to 99. The caller’s first price is 99 afterward.', <<<'C'
#include <stdio.h>
int main(void) {
    int first = 10;
    first = 99;
    printf("first = %d\n", first);
    return 0;
}
C],
            'micro' => [
                'Example 2 is the effect of *p = 99 when p points at the caller’s first slot. The playground shows the shared slot directly.',
                'If you need a private row, you must allocate another row and move each slot yourself. Passing the name does not do that.',
                'A struct passed by value is different: the struct’s fields are copied, unless the struct itself contains a pointer, in which case the pointer is copied and the target is shared.',
            ],
            'quiz' => [
                $q('What does a function receive when you pass an array?', 'The address of the first slot', 'A fresh row with the same numbers', 'The length, automatically', 'Only the last slot'),
                $q('Why pass the length?', 'The address does not remember how many slots exist', 'printf requires it', 'The first slot is always 0', 'Arrays cannot hold ints'),
                $q('Who sees a store to the first slot?', 'The caller, because it is the same memory', 'Only the callee', 'Nobody, stores are ignored', 'Only a later program'),
            ],
            'practice' => 'The caller’s row is 4, 5, 6. You pass a length of 2 by mistake. Which slots is the function entitled to read?',
        ]),
    ];
}
