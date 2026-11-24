<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/track_seed.php';

function dsa_course_meta(): array
{
    return [
        'title' => 'DSA Using Python',
        'slug' => 'dsa-using-python',
        'keywords' => 'DSA using Python, data structures, algorithms, big O, arrays, stacks, queues, linked lists, hash maps, trees, graphs, dynamic programming, beginner to master',
        'description' => 'A complete data-structures and algorithms course in Python, from “what is a problem?” to graphs, dynamic programming, and a route-finder project. Every chapter names the small points beginners miss, with runnable examples and a quiz. No earlier chapter is removed when this track is updated.',
    ];
}

function dsa_chapters(): array
{
    $f = 'dsa_notes.py';
    $L = static function (array $c) use ($f): array {
        return track_lesson('python', $f, $c);
    };
    $cost = svg_bar_chart('How many steps for n = 8', [
        ['label' => 'One look', 'value' => 1, 'display' => '1', 'color' => '#059669'],
        ['label' => 'A loop', 'value' => 8, 'display' => '8', 'color' => '#0284c7'],
        ['label' => 'Nested loops', 'value' => 64, 'display' => '64', 'color' => '#d97706'],
        ['label' => 'All pairs of pairs', 'value' => 100, 'display' => 'grows fast', 'color' => '#e11d48'],
    ], '', 'Nested loops multiply. A second loop inside the first is why “it worked on 10 items” later feels stuck.');

    return [
        $L([
            'section' => '0. Start from zero',
            'title' => 'A problem is an input, a rule, and an output',
            'keywords' => 'algorithm, input, output, problem statement, deterministic',
            'lead' => 'You do not start by memorising a famous algorithm. You start by saying what goes in, what must come out, and which rule is allowed. An algorithm is just that rule written so a machine can follow it without guessing.',
            'heading' => 'Three sentences before any code',
            'para' => 'Take a tiny job: “given the prices of notes, tell me the cheapest.” The input is the list of prices. The output is one number. The rule is “remember the smallest so far.” If you cannot say those three sentences, the code will wander.',
            'bullets' => [
                'The input has a type. A list of numbers is not the same as one number, and an empty list is a real input.',
                'The output is the answer, not a print of your debugging. Decide it before you write the loop.',
                'Same input must give the same output. If the result depends on luck, it is not this kind of algorithm.',
                'A worked example on paper, with three prices, catches a wrong rule before Python does.',
            ],
            'rows' => [
                ['Input', 'The data the function is given', 'Write the shape: “a list of ints”, not “some prices”.'],
                ['Output', 'The value you return', 'Returning is not the same as printing. Callers need the return.'],
                ['Rule', 'The steps that are always legal', '“Look at each price once” is a rule. “Try stuff” is not.'],
                ['Example', 'One input you solve by hand', 'If your hand answer and the code disagree, the code is wrong.'],
                ['Empty input', 'A list with no items', 'Cheapest-of-nothing has no answer. Decide that case on purpose.'],
                ['Name', 'A verb for the job', 'cheapest_price tells the truth. data1 does not.'],
            ],
            'e1' => ['Example 1 — say the contract, then compute', 'Three prices. The function returns the smallest. The prints are the hand check.', <<<'PY'
def cheapest_price(prices):
    if not prices:
        return None
    best = prices[0]
    for price in prices:
        if price < best:
            best = price
    return best

print(cheapest_price([40, 15, 22]))
print(cheapest_price([]))
PY],
            'e2' => ['Example 2 — the first item is a candidate', 'Starting best at 0 fails when every price is above 0, and also fails if prices can be negative and you started at 0 for a max. Here the bug is starting too high.', <<<'PY'
def buggy_cheapest(prices):
    best = 0
    for price in prices:
        if price < best:
            best = price
    return best

print('bug, all prices above 0:', buggy_cheapest([40, 15, 22]))
print('start from the first item:', min([40, 15, 22]))
PY],
            'micro' => [
                'min(prices) is the same rule, already written in Python. You still need to know what it does on an empty list: it raises ValueError.',
                'None means “no answer”, not the number 0. Zero can be a real cheapest price.',
                'Write one example in a comment or a print before you add a second feature.',
            ],
            'quiz' => [
                bquiz('What three things define an algorithm for a beginner?', 'An input, a rule, and an output', 'A library, a class, and a server', 'A graph, a heap, and a trie', 'A password, a token, and a cookie'),
                bquiz('Why is starting “best” at 0 wrong for the cheapest of [40, 15, 22]?', '0 is smaller than every price, so the loop never replaces it', 'Python cannot compare numbers', 'The list is sorted', '0 means empty'),
                bquiz('What should cheapest of an empty list do if you decided there is no answer?', 'Return None, on purpose', 'Return 0', 'Crash with no message and call that fine', 'Return the string prices'),
            ],
            'practice' => 'On paper, write input, output, and the rule for “how many prices are above 20?”. Then run both examples and say which line proves the empty list was handled.',
        ]),
        $L([
            'section' => '0. Start from zero',
            'title' => 'Names, lists, and the last index',
            'keywords' => 'python list, index, len, off by one, negative index',
            'lead' => 'A Python list keeps items in a row. The first item is index 0, not 1. The last index is len(items) - 1. Almost every later bug is this sentence forgotten.',
            'heading' => 'The row, the number on the door, and the length',
            'para' => 'len tells you how many doors there are. It is not the last door number. For five items the doors are 0, 1, 2, 3, and 4. Index -1 is a shortcut for the last door. It is still the same item as index len - 1.',
            'bullets' => [
                'items[0] is the first. items[len(items)] is one past the end and raises IndexError.',
                'A slice items[1:3] includes index 1 and stops before index 3. The stop is not included.',
                'Appending adds at the end. Inserting at 0 pushes everyone else one step right.',
                'A list can hold anything. This course mostly uses numbers and strings so the pictures stay clear.',
            ],
            'rows' => [
                ['0', 'First index', 'People count from 1. Lists count from 0.'],
                ['len - 1', 'Last index', 'len is the count, not a legal index.'],
                ['-1', 'Last item, short form', 'Negative indexes count back from the end. -1 is last, -2 is second last.'],
                ['slice stop', 'Not included', 'items[0:2] has two items, indexes 0 and 1.'],
                ['IndexError', 'Door does not exist', 'Empty list has no index 0 and no index -1.'],
                ['append', 'Add at the end', 'The old indexes stay valid. insert(0, x) does not.'],
            ],
            'e1' => ['Example 1 — count versus last door', 'Five letters. Watch len, the last index, and the slice that stops early.', <<<'PY'
letters = ['a', 'b', 'c', 'd', 'e']
print('count', len(letters))
print('last index', len(letters) - 1)
print('last item', letters[-1])
print('slice 1:3', letters[1:3])
PY],
            'e2' => ['Example 2 — the door past the end', 'This prints the error name instead of crashing the lesson, so you can see it.', <<<'PY'
letters = ['a', 'b', 'c', 'd', 'e']
try:
    print(letters[len(letters)])
except IndexError as err:
    print('IndexError:', err)
print('empty last also fails')
try:
    print([][-1])
except IndexError as err:
    print('IndexError:', err)
PY],
            'micro' => [
                'range(len(letters)) yields 0, 1, 2, 3, 4. range(1, len) skips the first item on purpose.',
                'letters[1:3] does not copy the whole list’s identity. Changing the slice later does not change the original, because a slice is a new list.',
                'Do not use a for-loop variable as an index unless you needed the index. for letter in letters is clearer and cannot walk off the end.',
            ],
            'quiz' => [
                bquiz('For a list of 5 items, which index is the last item?', '4', '5', '6', '-5 only, 4 is illegal'),
                bquiz('What does items[0:2] include?', 'Indexes 0 and 1', 'Indexes 0, 1, and 2', 'Only index 2', 'The whole list'),
                bquiz('What does Python raise when you read one past the last index?', 'IndexError', 'KeyError', 'TypeError always', 'Nothing, it returns None'),
            ],
            'practice' => 'Make a list of three note titles. Print the count, the last title using -1, and the slice of only the middle title. Say why the stop index is not included.',
        ]),
        $L([
            'section' => '0. Start from zero',
            'title' => 'One loop, and the step you forgot to count',
            'keywords' => 'for loop, while loop, accumulator, off by one loop',
            'lead' => 'A loop repeats a step. The skill is knowing how many times, and what you must remember between times. Forgetting to move the index, or counting one extra, is the whole bug.',
            'heading' => 'Remember something, then take the next item',
            'para' => 'An accumulator is a variable that survives the next lap: a total, a count, a best-so-far. Inside the loop you update it. After the loop you return it. A while loop must change the thing it tests, or it never ends.',
            'bullets' => [
                'for item in items walks each item once. You do not write the index unless you need the position.',
                'total = total + price is the lap. If total is created inside the loop, each lap starts from zero again.',
                'while i < len(items) needs i = i + 1. Without it, i stays 0 forever.',
                'range(n) is 0 through n - 1. range(1, n + 1) is 1 through n. The stop is still excluded.',
            ],
            'rows' => [
                ['Accumulator', 'Memory across laps', 'Create it before the loop.'],
                ['for', 'Once per item', 'Stops by itself at the end.'],
                ['while', 'Until a test fails', 'You must make progress inside.'],
                ['range stop', 'Excluded', 'range(3) is 0, 1, 2.'],
                ['Infinite loop', 'The test never becomes false', 'The playground will hang. Always move the counter.'],
                ['break', 'Leave early', 'Use it when the answer cannot change anymore. Say why.'],
            ],
            'e1' => ['Example 1 — a total needs a memory', 'The total is born before the loop. Each price is added once.', <<<'PY'
prices = [40, 15, 22]
total = 0
for price in prices:
    total = total + price
print(total)
print('average', total / len(prices))
PY],
            'e2' => ['Example 2 — while that moves, and range that stops early', 'Both print 0, 1, 2. Neither prints 3.', <<<'PY'
i = 0
while i < 3:
    print('while', i)
    i = i + 1
for n in range(3):
    print('range', n)
PY],
            'micro' => [
                'Dividing the total by len is fine only when the list is not empty. Check that first.',
                'total += price is the same update. The long form shows that total is replaced by a new number.',
                'A loop that builds a new list should append inside the loop, not replace the whole list with one item.',
            ],
            'quiz' => [
                bquiz('Where do you create an accumulator such as total?', 'Before the loop', 'Inside the loop, so it resets', 'After the return', 'Inside the print only'),
                bquiz('What does range(3) produce?', '0, 1, 2', '1, 2, 3', '0, 1, 2, 3', '3, 2, 1, 0'),
                bquiz('What happens in while i < 3 if you never increase i?', 'The loop never ends', 'It runs three times anyway', 'Python adds 1 for you', 'It raises IndexError immediately'),
            ],
            'practice' => 'Sum the list [1, 2, 3, 4] with a for loop and again with a while loop. The two totals must match. Count the laps out loud.',
        ]),
        $L([
            'section' => '1. The cost of a step',
            'title' => 'Big-O in plain words',
            'keywords' => 'big O, time complexity, linear, quadratic, constant time',
            'lead' => 'Big-O is not a medal. It is a sentence: “when the list gets longer, how does the work grow?” You ignore the exact brand of laptop and watch the shape.',
            'heading' => 'Count the laps, not the microseconds',
            'para' => 'If you touch each item once, doubling the list doubles the work. That is linear, written O(n). If for every item you touch every item again, the work squares. That is O(n²). If you do a fixed amount no matter how long the list is, that is O(1). The n is the size of this input, usually the length.',
            'bullets' => [
                'Drop the small stuff when n is large. 3n + 5 behaves like n. You still care about 3 when n is tiny and the constant is huge.',
                'Best, typical, and worst are different. Searching a list is fast when the item is first, and slow when it is missing.',
                'Big-O is an upper shape for large n. It does not say your function is fast today.',
                'Say the unit: n items, n characters, n nodes. “O(n)” with no noun is unfinished.',
            ],
            'visual' => $cost,
            'rows' => [
                ['O(1)', 'Fixed work', 'Reading items[0], or a dict lookup that hit.'],
                ['O(n)', 'One lap per item', 'A single for loop over the list.'],
                ['O(n²)', 'Laps times laps', 'A loop inside a loop, both depending on n.'],
                ['O(log n)', 'Halve the problem', 'Binary search on a sorted list. Each step throws away half.'],
                ['Worst case', 'The unlucky input', 'Quote this one unless you say otherwise.'],
                ['Constant factors', 'Hidden inside O', 'Two linear passes are still O(n), and still slower than one pass.'],
            ],
            'e1' => ['Example 1 — one lap versus laps times laps', 'The counters are the work. 5 items: 5 versus 25.', <<<'PY'
def one_lap(items):
    steps = 0
    for _ in items:
        steps += 1
    return steps

def laps_times_laps(items):
    steps = 0
    for _ in items:
        for _ in items:
            steps += 1
    return steps

data = [1, 2, 3, 4, 5]
print('one lap', one_lap(data))
print('nested', laps_times_laps(data))
PY],
            'e2' => ['Example 2 — doubling n', 'Linear doubles. Nested multiplies by four when n doubles, because (2n)² = 4n².', <<<'PY'
def nested(n):
    steps = 0
    for _ in range(n):
        for _ in range(n):
            steps += 1
    return steps

print('n=4', nested(4))
print('n=8', nested(8))
PY],
            'micro' => [
                'O(2n) is called O(n). Say “two passes” when the 2 matters to a reader.',
                'A slice of the whole list is O(n) work and O(n) extra space. It looks like one line.',
                'Logarithm here is “how many times can I halve n until I hit 1”. You do not need a formula to use that picture.',
            ],
            'quiz' => [
                bquiz('A single loop over n items is which shape?', 'O(n)', 'O(1)', 'O(n²)', 'O(log n)'),
                bquiz('When n doubles, what happens to the work of a nested pair of loops over n?', 'It becomes about four times', 'It stays the same', 'It doubles', 'It becomes zero'),
                bquiz('Which case should you quote if you do not say otherwise?', 'The worst case', 'The best case only', 'The case on your laptop today', 'The empty list only'),
            ],
            'practice' => 'For a list of 10 titles, say how many steps a “look at each title once” rule takes, and how many a “compare every title to every title” rule takes.',
        ]),
        $L([
            'section' => '1. The cost of a step',
            'title' => 'Why the front of a list is expensive',
            'keywords' => 'python list insert, append cost, array, dynamic array',
            'lead' => 'A Python list is a row of slots in memory. Adding at the end is usually cheap. Adding at the front makes everyone else move one slot right. That move is the cost people forget.',
            'heading' => 'The row slides when you shove the front',
            'para' => 'append puts an item in the next free slot. Sometimes the row is full and Python allocates a longer row and copies, but that is rare enough that a series of appends is treated as cheap per item. insert(0, x) copies every current item one step, every time. Doing that n times is O(n²).',
            'bullets' => [
                'items[i] is O(1). The index is an address calculation, not a walk.',
                'append is the habit. insert(0, x) is the habit to question.',
                'pop() from the end is cheap. pop(0) slides everyone left and is expensive.',
                'If you need cheap adds at both ends, use a deque. The next chapters get there.',
            ],
            'rows' => [
                ['append', 'Add at the end', 'Amortised O(1). The usual choice.'],
                ['insert(0, x)', 'Add at the front', 'O(n) because every item moves.'],
                ['pop()', 'Remove the end', 'O(1).'],
                ['pop(0)', 'Remove the front', 'O(n). The rest slide left.'],
                ['items[i]', 'Read by index', 'O(1). This is why lists are used as arrays.'],
                ['in', 'Is this value present?', 'O(n). It may look at every item. A dict is the later fix.'],
            ],
            'e1' => ['Example 1 — the front insert moves the others', 'Watch the indexes change. What was at 0 is now at 1.', <<<'PY'
notes = ['milk', 'bread']
notes.insert(0, 'eggs')
print(notes)
print('old first moved to', notes[1])
notes.append('tea')
print(notes)
PY],
            'e2' => ['Example 2 — building a list backwards the expensive way', 'Inserting at 0 for each new item reverses the input and slides every time. Appending then reversing once is the cheaper picture, and reverse is one pass.', <<<'PY'
def expensive_reverse(items):
    out = []
    for item in items:
        out.insert(0, item)
    return out

def cheaper_reverse(items):
    out = []
    for item in items:
        out.append(item)
    out.reverse()
    return out

print(expensive_reverse(['a', 'b', 'c']))
print(cheaper_reverse(['a', 'b', 'c']))
PY],
            'micro' => [
                '“Amortised” means a rare expensive copy is spread over many cheap appends. One append can still be the unlucky copy.',
                'items + [x] builds a brand new list. It does not append. Inside a loop that is also O(n²).',
                'A tuple cannot append. Use it when the row should not change.',
            ],
            'quiz' => [
                bquiz('Which list operation slides every item one slot?', 'insert at index 0', 'append', 'read items[0]', 'len(items)'),
                bquiz('Why is x in some_list often slower than a dict lookup later?', 'It may check every item', 'Lists cannot hold strings', 'in always checks one slot', 'len is slow'),
                bquiz('What does notes + [x] do?', 'Builds a new list and leaves notes unchanged', 'Appends onto notes', 'Deletes notes', 'Sorts notes'),
            ],
            'practice' => 'Start from [1, 2, 3]. insert 0 at the front, then append 9. Write the list you expect, then run it. Mark which call slid the old items.',
        ]),
        $L([
            'section' => '1. The cost of a step',
            'title' => 'Space: the extra list you did not notice',
            'keywords' => 'space complexity, extra memory, in place, copy',
            'lead' => 'Time is the laps. Space is the extra paper. A function that builds a second full copy needs room for two lists. A function that swaps inside the same list needs only a handful of extra names.',
            'heading' => 'The output list is allowed; a secret copy might not be',
            'para' => 'Returning a new list of answers is honest space: the caller asked for those answers. The bug is an extra copy you did not need, or recursion that keeps every stack frame’s whole list alive. Say “extra space besides the input and the answer.”',
            'bullets' => [
                'A few counters are O(1) extra space, even if the input is huge.',
                'A result list of n answers is O(n) output. Do not pretend it is free, and do not apologise for it.',
                'Slicing the whole list copies it. Sorting a copy with sorted(items) leaves the original and spends O(n) extra.',
                'items.sort() reorders in place. It spends less extra space and destroys the old order. Choose on purpose.',
            ],
            'rows' => [
                ['O(1) extra', 'A few variables', 'The input itself does not count as extra.'],
                ['O(n) extra', 'Another row of n', 'A copied list, a result of n items, a stack of n frames.'],
                ['sorted(xs)', 'New list', 'Original order survives.'],
                ['xs.sort()', 'In place', 'Original order is gone.'],
                ['slice', 'A copy of that part', 'xs[:] copies everything.'],
                ['Recursion depth', 'One frame per call', 'A deep chain can blow the stack before the algorithm “should” be slow.'],
            ],
            'e1' => ['Example 1 — copy versus in place', 'sorted returns a new list. sort changes the original.', <<<'PY'
original = [3, 1, 2]
copied = sorted(original)
print('copied', copied)
print('original still', original)
original.sort()
print('after sort', original)
PY],
            'e2' => ['Example 2 — the slice is a second list', 'Changing the copy does not change the original. You paid for two lists.', <<<'PY'
notes = ['milk', 'bread', 'tea']
clone = notes[:]
clone[0] = 'eggs'
print('clone', clone)
print('notes', notes)
print('same object?', clone is notes)
PY],
            'micro' => [
                'is checks identity, the same object. == checks equal values. Two lists can be equal and not be the same object.',
                'A generator expression does not build the whole list at once. list(generator) does.',
                'When you quote space, say extra space. The input is already paid for by the caller.',
            ],
            'quiz' => [
                bquiz('What is the difference between sorted(xs) and xs.sort()?', 'sorted returns a new list; sort changes xs', 'They are the same', 'sort returns a new list; sorted changes xs', 'Neither can order numbers'),
                bquiz('A handful of counters on top of a huge input is which extra space?', 'O(1)', 'O(n²)', 'O(n log n) always', 'Zero, counters are not memory'),
                bquiz('What does notes[:] make?', 'A new list with the same items', 'A second name for the same list', 'A dict', 'An empty list'),
            ],
            'practice' => 'Copy a list with [:], change index 0 of the copy, and print both. Then say whether the original had to move.',
        ]),
    ];
}

function dsa_chapters_rest(): array
{
    return [];
}
