<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/track_seed.php';

function dsa_chapters_depth(): array
{
    $f = 'dsa_notes.py';
    $L = static function (array $c) use ($f): array {
        return track_lesson('python', $f, $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };
    $swaps = svg_bar_chart('Swaps on [4, 1, 3, 2]', [
        ['label' => 'Bubble', 'value' => 4, 'display' => '4 swaps', 'color' => '#0284c7'],
        ['label' => 'Selection', 'value' => 2, 'display' => '2 swaps', 'color' => '#059669'],
    ], '', 'Bubble swaps whenever two neighbours are out of order. Selection swaps once per position, with the smallest item still to the right.');

    return [
        $L([
            'section' => '5. Sorting',
            'title' => 'Bubble sort: neighbours swap until the row settles',
            'keywords' => 'bubble sort, adjacent swap, sorted suffix, python',
            'lead' => 'Bubble sort looks only at neighbours. If the left one is bigger, they swap. After one full walk, the largest item has bubbled to the end. The next walk can stop one slot earlier, because that end is already finished.',
            'heading' => 'One pass fixes the last slot',
            'para' => 'On [4, 1, 3, 2] the first pass swaps 4 with 1, then 4 with 3, then 4 with 2. The row is now [1, 3, 2, 4]. The 4 will never move again. A second pass swaps 3 with 2. A third pass finds nothing to swap. The early stop is the detail people skip: if a pass makes zero swaps, the whole row is sorted and the remaining passes are wasted.',
            'visual' => $swaps,
            'bullets' => [
                'Compare index j with j + 1, never a far-away slot, in this algorithm.',
                'After pass i, the last i items are in their final places. The inner loop should shrink.',
                'A pass with no swaps means you can return. The row is sorted.',
                'Equal neighbours do not swap. That choice matters in the stability chapter.',
            ],
            'rows' => [
                ['Neighbour', 'j and j + 1', 'Only adjacent items move.'],
                ['Swap', 'Left was bigger', 'They exchange places.'],
                ['Pass', 'One walk of the row', 'The largest of the unsorted part reaches the end.'],
                ['Shrink', 'Skip the finished end', 'The inner loop ends earlier each pass.'],
                ['Early stop', 'A quiet pass', 'No swap means the row is done.'],
                ['Cost', 'About n squared compares', 'Fine for a teaching row. Painful for a million items.'],
            ],
            'e1' => ['Example 1 — the row, and how many swaps', 'Four swaps put [4, 1, 3, 2] into order. The last pass is quiet and stops the loops.', <<<'PY'
def bubble(row):
    a = row[:]
    swaps = 0
    n = len(a)
    for i in range(n):
        quiet = True
        for j in range(n - 1 - i):
            if a[j] > a[j + 1]:
                a[j], a[j + 1] = a[j + 1], a[j]
                swaps += 1
                quiet = False
        if quiet:
            break
    return a, swaps

print(bubble([4, 1, 3, 2]))
PY],
            'e2' => ['Example 2 — an already sorted row does one quiet pass', 'Nothing swaps. The function returns after the first pass, not after n passes.', <<<'PY'
def bubble_passes(row):
    a = row[:]
    passes = 0
    n = len(a)
    for i in range(n):
        passes += 1
        quiet = True
        for j in range(n - 1 - i):
            if a[j] > a[j + 1]:
                a[j], a[j + 1] = a[j + 1], a[j]
                quiet = False
        if quiet:
            break
    return passes

print('sorted row passes', bubble_passes([1, 2, 3, 4]))
print('reversed row passes', bubble_passes([4, 3, 2, 1]))
PY],
            'micro' => [
                'range(n - 1 - i) is the shrink. If you write range(n - 1) every time, the algorithm is still correct and does extra compares against the finished tail.',
                'Swapping with two assignments and a temporary name is the same exchange. The tuple swap is that temporary, hidden.',
                'A reverse-sorted row of n items needs every pass. The early stop does not help that input.',
            ],
            'quiz' => [
                $q('What does one bubble pass guarantee?', 'The largest unsorted item is now at the end of the unsorted part', 'The whole row is sorted', 'Only the first item is correct', 'No item moved'),
                $q('When may you stop early?', 'A pass made no swaps', 'The first item is the smallest', 'You have done one pass', 'n is odd'),
                $q('Which pair does bubble sort compare?', 'Neighbours', 'The first and the last only', 'Every pair in the row', 'Items with equal indexes'),
            ],
            'practice' => 'On paper, write [5, 1, 4] after each swap. Then say whether the second pass can ignore the last slot.',
        ]),
        $L([
            'section' => '5. Sorting',
            'title' => 'Selection sort: the smallest remaining moves forward',
            'keywords' => 'selection sort, minimum index, one swap per pass',
            'lead' => 'Selection sort does not swap neighbours all the way along. It searches the unsorted tail for the smallest item, then swaps that item into the next finished slot at the front. Each pass finishes one more slot.',
            'heading' => 'Find the minimum, then one swap',
            'para' => 'On [4, 1, 3, 2] the first search finds 1 at index 1 and swaps it with 4. The row is [1, 4, 3, 2] and index 0 is finished. The next search looks only from index 1 and finds 2, swapping it with 4. Then 3 is already in place. You did two swaps, not a swap for every neighbour.',
            'bullets' => [
                'The finished prefix grows from the left: after i passes, indexes 0 through i - 1 are final.',
                'The search for the minimum starts at i, not at 0. Searching the finished prefix again is wasted and can undo the point of the algorithm.',
                'There is at most one swap per pass, even if many items were inspected.',
                'The swap can jump an item over another item with the same key. That is why selection is not stable. The next chapter shows the pair that breaks.',
            ],
            'rows' => [
                ['i', 'The slot you are filling', 'Starts at 0.'],
                ['small', 'Index of the minimum so far', 'Begins as i, then moves when a smaller tail item appears.'],
                ['Tail', 'Indexes i through the end', 'The only place the minimum is allowed to come from.'],
                ['One swap', 'i with small', 'Skip the swap when small is already i.'],
                ['Compares', 'About n squared', 'Same family of cost as bubble, fewer writes.'],
                ['Writes', 'About n swaps', 'Useful when writing an item is expensive and comparing is cheap.'],
            ],
            'e1' => ['Example 1 — two swaps finish the row', 'The printed swaps are the index pairs. The final row is sorted.', <<<'PY'
def selection(row):
    a = row[:]
    swaps = []
    for i in range(len(a)):
        small = i
        for j in range(i + 1, len(a)):
            if a[j] < a[small]:
                small = j
        if small != i:
            a[i], a[small] = a[small], a[i]
            swaps.append((i, small))
    return a, swaps

print(selection([4, 1, 3, 2]))
PY],
            'e2' => ['Example 2 — do not search the finished prefix', 'A buggy search that starts at 0 can pick an item already placed. The fixed search starts at i.', <<<'PY'
def buggy(row):
    a = row[:]
    for i in range(len(a)):
        small = 0
        for j in range(len(a)):
            if a[j] < a[small]:
                small = j
        a[i], a[small] = a[small], a[i]
    return a

def fixed(row):
    a = row[:]
    for i in range(len(a)):
        small = i
        for j in range(i + 1, len(a)):
            if a[j] < a[small]:
                small = j
        if small != i:
            a[i], a[small] = a[small], a[i]
    return a

print('buggy', buggy([4, 1, 3, 2]))
print('fixed', fixed([4, 1, 3, 2]))
PY],
            'micro' => [
                'Starting small at 0 on every pass reselects the global minimum and drags it forward again. That is the bug in example 2.',
                'If the minimum is already at i, swapping i with i is harmless but noisy. Skip it.',
                'Selection needs random access. A linked list makes the search for index j expensive. An array does not.',
            ],
            'quiz' => [
                $q('How many swaps does one selection pass do?', 'At most one', 'One per neighbour', 'None, it only reads', 'n swaps'),
                $q('Where does the search start on pass i?', 'At index i', 'At index 0 every time', 'At the last index', 'At i + 2'),
                $q('What is true of the prefix after i passes?', 'Indexes 0 through i - 1 are in their final places', 'The whole row is sorted', 'Only the last item is final', 'The prefix is reversed'),
            ],
            'practice' => 'Run selection by hand on [3, 1, 2]. Write the row after each pass and the index you swapped.',
        ]),
        $L([
            'section' => '5. Sorting',
            'title' => 'Equal keys keep the order they arrived in',
            'keywords' => 'stable sort, equal keys, selection sort unstable',
            'lead' => 'A sort is stable when two items with the same key come out in the same order they went in. People treat that as decoration until they sort by grade and the students who tied are suddenly in a different order than the class list.',
            'heading' => 'The key can tie',
            'para' => 'Take three records: a and b both have key 2, and a arrived first. c has key 1. A stable sort must produce c, then a, then b. Python’s sorted is stable, so a stays before b. Selection sort swaps the later 1 into the front by jumping it over a, and a ends up after b. Same keys, different order. That is unstable.',
            'bullets' => [
                'Stability is about equal keys only. Different keys have one legal order.',
                'A stable sort lets you sort by last name, then by first name, and the first-name ties stay in last-name order.',
                'Bubble sort that swaps only on > is stable. Swapping on >= shuffles ties for no reason.',
                'Selection is unstable because the minimum can leap over an equal key.',
            ],
            'rows' => [
                ['Key', 'The value you compare', 'Here, the number.'],
                ['Tie', 'Same key', 'The original order is the only thing left to preserve.'],
                ['Stable', 'Ties keep arrival order', 'a before b stays a before b.'],
                ['Unstable', 'Ties may cross', 'Selection does this with a jump swap.'],
                ['sorted', 'Stable in Python', 'The teaching check in example 1.'],
                ['Two passes', 'Sort the minor key first', 'Then the major key, with a stable sort.'],
            ],
            'e1' => ['Example 1 — the tie that selection reverses', 'a arrived before b. Both have key 2. Selection emits b before a. sorted emits a before b.', <<<'PY'
row = [('a', 2), ('b', 2), ('c', 1)]

def selection(pairs):
    a = pairs[:]
    for i in range(len(a)):
        small = i
        for j in range(i + 1, len(a)):
            if a[j][1] < a[small][1]:
                small = j
        a[i], a[small] = a[small], a[i]
    return a

print('selection', selection(row))
print('sorted', sorted(row, key=lambda item: item[1]))
PY],
            'e2' => ['Example 2 — sort the minor key first', 'Last name is the minor key. Grade is the major key. A stable second sort keeps Ann before Bo inside the same grade.', <<<'PY'
students = [('Bo', 'A'), ('Ann', 'A'), ('Cy', 'B')]
by_name = sorted(students, key=lambda s: s[0])
by_grade = sorted(by_name, key=lambda s: s[1])
print(by_grade)
PY],
            'micro' => [
                'key=lambda item: item[1] compares the number and leaves the letter attached. Sorting the numbers alone throws the letters away.',
                'If the second sort is unstable, Ann and Bo can swap even though the first sort ordered them.',
                '“It looked sorted” is not a stability test. You need two items with the same key and a known arrival order.',
            ],
            'quiz' => [
                $q('When does stability matter?', 'Two items have the same key and a prior order you need to keep', 'Every item has a different key', 'The row has one item', 'You only want the maximum'),
                $q('Which result is the stable order of (a,2), (b,2), (c,1)?', 'c, then a, then b', 'c, then b, then a', 'a, then b, then c', 'b, then a, then c'),
                $q('Why can selection reverse a tie?', 'The chosen minimum jumps over another item', 'It never swaps', 'It sorts text only', 'Python forbids equal keys'),
            ],
            'practice' => 'Add a fourth record (d, 1) at the end. Predict the stable order, then check it with sorted.',
        ]),
        $L([
            'section' => '5. Sorting',
            'title' => 'Counting sort when every number is small',
            'keywords' => 'counting sort, frequency, linear time, small range',
            'lead' => 'If every number lives in a small range, you do not need to compare them. Count how many times each value appears, then write the values back in order. The work follows the length of the row plus the size of the range, not the square of the length.',
            'heading' => 'A tally, then a write-back',
            'para' => 'For [2, 0, 2, 1] and a maximum of 2, the tallies are: 0 appears once, 1 appears once, 2 appears twice. Writing them in value order produces [0, 1, 2, 2]. You never asked “is this item bigger than that item?”. The price is the extra tally list. If the numbers run up to a million and you only have five of them, the tally is the expensive part and a comparison sort is the honest choice.',
            'bullets' => [
                'The tally index is the number itself. A negative number has no slot unless you shift the range first.',
                'The tally length is max - min + 1, not the length of the row.',
                'Writing value count times is the output. Forgetting a value that appeared zero times is correct: it contributes nothing.',
                'This version is stable if you walk the input from the right while placing into a prefix of counts. The simple rebuild in this lesson does not keep satellite records. It only rebuilds the numbers.',
            ],
            'rows' => [
                ['Range', 'Smallest to largest value', 'Decides the tally length.'],
                ['Tally', 'counts[value] += 1', 'One slot per possible value.'],
                ['Write-back', 'Emit value, count times', 'In increasing value order.'],
                ['No compares', 'The index is the order', 'That is why it can beat n log n.'],
                ['Extra memory', 'The tally', 'A huge range costs a huge tally.'],
                ['Negatives', 'Shift them', 'Add the absolute minimum so every index is at least 0.'],
            ],
            'e1' => ['Example 1 — tallies become the sorted row', '0 once, 1 once, 2 twice. The printed row is the write-back.', <<<'PY'
def counting(nums, top):
    counts = [0] * (top + 1)
    for n in nums:
        counts[n] += 1
    out = []
    for value, count in enumerate(counts):
        out.extend([value] * count)
    return counts, out

print(counting([2, 0, 2, 1], 2))
PY],
            'e2' => ['Example 2 — a negative must be shifted', 'The smallest value is -1. Adding 1 makes the indexes 0, 1, and 2. Subtract 1 when you write the values back.', <<<'PY'
def counting_shifted(nums):
    low = min(nums)
    high = max(nums)
    counts = [0] * (high - low + 1)
    for n in nums:
        counts[n - low] += 1
    out = []
    for index, count in enumerate(counts):
        out.extend([index + low] * count)
    return out

print(counting_shifted([-1, 1, -1, 0]))
PY],
            'micro' => [
                'counts[n] += 1 crashes if n is bigger than top. The top is part of the contract, not a guess you can skip.',
                '[value] * count builds a short row of that value. count 0 builds an empty piece, so missing values vanish on their own.',
                'A row of names with ages cannot be rebuilt from ages alone. You would place the original records, not the bare numbers.',
            ],
            'quiz' => [
                $q('What does counting sort compare?', 'It does not compare items; it tallies values', 'Every pair', 'Only neighbours', 'Only the first and last'),
                $q('When is the tally a bad idea?', 'The values span a huge range and the row is short', 'Every value is 0 or 1', 'The row is already sorted', 'There are duplicates'),
                $q('Why shift a negative?', 'A list index cannot be negative', 'Negative numbers are already sorted', 'Shift changes the count', 'Python sorts negatives first automatically'),
            ],
            'practice' => 'Tally [3, 1, 3, 1] by hand with top 3. Write the counts list and the output row.',
        ]),
        $L([
            'section' => '2. Linear structures',
            'title' => 'A range total is one subtraction',
            'keywords' => 'prefix sum, range query, cumulative sum',
            'lead' => 'If you will ask many times for the total of a slice, do not walk the slice every time. First build a prefix row where slot i is the total of everything before i. The total from left to right is prefix[right + 1] minus prefix[left].',
            'heading' => 'Store the running total',
            'para' => 'Prices [3, 1, 4, 2] become the prefix [0, 3, 4, 8, 10]. The leading 0 means “the total before the first price.” The total of indexes 1 through 3 is prefix[4] - prefix[1], which is 10 - 3 = 7, and 1 + 4 + 2 is 7. Each later question is one subtraction, no matter how long the slice is. Building the prefix cost one walk.',
            'bullets' => [
                'prefix[0] is 0. Forgetting it makes the first slice subtract the wrong thing.',
                'prefix[i] is the total of the first i prices, so it ends one past the last included index.',
                'The slice from left to right, inclusive, uses prefix[right + 1] - prefix[left].',
                'A second question does not rebuild the prefix. That is the whole saving.',
            ],
            'rows' => [
                ['prefix[0]', '0', 'Nothing has been added yet.'],
                ['prefix[i]', 'Total of the first i items', 'Not the item at index i by itself.'],
                ['Slice', 'prefix[right + 1] - prefix[left]', 'Inclusive on both ends.'],
                ['Build', 'One walk', 'Pay this once.'],
                ['Ask', 'One subtraction', 'Pay this per question.'],
                ['Empty slice', 'left > right', 'Decide that this is 0 before you subtract.'],
            ],
            'e1' => ['Example 1 — one prefix, two questions', 'Indexes 1 through 3 total 7. Indexes 0 through 1 total 4. The prefix is built once.', <<<'PY'
prices = [3, 1, 4, 2]
prefix = [0]
for price in prices:
    prefix.append(prefix[-1] + price)

def slice_total(left, right):
    return prefix[right + 1] - prefix[left]

print(prefix)
print(slice_total(1, 3))
print(slice_total(0, 1))
PY],
            'e2' => ['Example 2 — the off-by-one in the prefix index', 'People subtract prefix[right] and drop the last price. right + 1 includes it.', <<<'PY'
prices = [3, 1, 4, 2]
prefix = [0]
for price in prices:
    prefix.append(prefix[-1] + price)

right = 3
print('drops the last price', prefix[right] - prefix[1])
print('includes index 3', prefix[right + 1] - prefix[1])
PY],
            'micro' => [
                'The prefix is one slot longer than the prices. Index 4 is legal on the prefix and illegal on the prices.',
                'This is a sum. A range minimum does not subtract. Subtraction works because addition undoes addition.',
                'If the prices can change in the middle, the prefix goes stale. This lesson assumes the row is fixed.',
            ],
            'quiz' => [
                $q('What is prefix[0]?', '0', 'The first price', 'The last price', 'The length'),
                $q('How do you total indexes 1 through 3 inclusive?', 'prefix[4] - prefix[1]', 'prefix[3] - prefix[1]', 'prefix[3] - prefix[0]', 'prefix[1] + prefix[3]'),
                $q('Why build a prefix?', 'Later slice totals are one subtraction', 'It sorts the row', 'It removes duplicates', 'It finds the maximum'),
            ],
            'practice' => 'Build the prefix of [5, 2, 5]. Compute the total of the last two items with one subtraction.',
        ]),
        $L([
            'section' => '3. Lookup',
            'title' => 'Two sum: remember the partner you still need',
            'keywords' => 'two sum, hash map, complement, pair',
            'lead' => 'You want two indexes whose values add to a target. Checking every pair works and is quadratic. A dictionary remembers each value you have already seen and the index where it sat. For the new value, you ask whether the partner target - value is already in the dictionary.',
            'heading' => 'The partner is the lookup',
            'para' => 'On [2, 7, 11] with target 9, 2 is stored first, needing 7. Then 7 arrives, and 9 - 7 is 2, which is in the dictionary at index 0. The answer is indexes 0 and 1. You must store the value after the lookup. If you store it before, a value can pair with itself at the same index, and 4 + 4 would “find” one single 4.',
            'bullets' => [
                'The dictionary maps a value to the index where you saw it. The index is the answer, not just the value.',
                'Look up the partner before you store the current value.',
                'The first match is a legal pair. This problem does not ask for every pair.',
                'If the partner is missing at the end, there is no pair. Return a deliberate empty answer, not indexes 0 and 0.',
            ],
            'rows' => [
                ['Partner', 'target - value', 'The number you hope you have seen.'],
                ['seen', 'value to index', 'Only past items.'],
                ['Order', 'Look up, then store', 'Stops a value pairing with itself.'],
                ['Hit', 'Partner is in seen', 'Return that index and the current index.'],
                ['Miss', 'Walk continues', 'Store the current value.'],
                ['None', 'No pair', 'Different from indexes that happen to hold zeros.'],
            ],
            'e1' => ['Example 1 — 2 and 7', 'The partner of 7 is 2, seen at index 0. The function returns [0, 1].', <<<'PY'
def two_sum(nums, target):
    seen = {}
    for i, n in enumerate(nums):
        need = target - n
        if need in seen:
            return [seen[need], i]
        seen[n] = i
    return None

print(two_sum([2, 7, 11], 9))
print(two_sum([1, 2, 3], 100))
PY],
            'e2' => ['Example 2 — storing first pairs a value with itself', 'The buggy version stores 4, then looks up 4, and returns the same index twice. The careful version waits for a second 4.', <<<'PY'
def buggy(nums, target):
    seen = {}
    for i, n in enumerate(nums):
        seen[n] = i
        need = target - n
        if need in seen:
            return [seen[need], i]
    return None

def careful(nums, target):
    seen = {}
    for i, n in enumerate(nums):
        need = target - n
        if need in seen:
            return [seen[need], i]
        seen[n] = i
    return None

print('buggy', buggy([4], 8))
print('careful one 4', careful([4], 8))
print('careful two 4s', careful([4, 1, 4], 8))
PY],
            'micro' => [
                'If the same value appears twice and the target is twice that value, the second index must be the one that hits. Overwriting seen[n] = i is fine because the earlier index was already given its chance.',
                'A sorted row can also be solved with two pointers, one at each end. The dictionary does not need the row to be sorted.',
                'Returning the values instead of the indexes solves a different problem. Read the contract.',
            ],
            'quiz' => [
                $q('What do you look up for the current value?', 'target - value', 'value + 1', 'The length', 'The first item always'),
                $q('Why look up before storing?', 'So a single item cannot pair with itself', 'Dictionaries require that order', 'Storing is slower', 'The target might be negative'),
                $q('What should you return when no pair exists?', 'A deliberate empty answer', 'Indexes 0 and 0', 'The target', 'The whole row'),
            ],
            'practice' => 'Find the indexes in [3, 2, 4] that add to 6. Say which value gets stored before the hit.',
        ]),
        $L([
            'section' => '8. Patterns to master',
            'title' => 'The best contiguous stretch',
            'keywords' => 'kadane, maximum subarray, running sum',
            'lead' => 'You want the contiguous stretch with the biggest sum. Trying every start and end is quadratic. The linear rule is: extend the stretch you are in, unless starting fresh at this item is better. Keep the best stretch you have ever seen.',
            'heading' => 'Throw away a stretch that hurts',
            'para' => 'On [-2, 1, -3, 4, -1, 2, 1, -5, 4] the winning stretch is 4, -1, 2, 1, and the sum is 6. When the running sum is -2, the next 1 is better alone than added to -2. You start fresh. A positive running sum is worth keeping, even if a later negative nibbles it, until the nibble makes starting over better.',
            'bullets' => [
                'The stretch must be contiguous. You cannot skip a negative in the middle and still call it one stretch.',
                'run is the best stretch that ends at the current item. best is the best stretch anywhere so far.',
                'If every item is negative, the answer is the largest item, not 0. Starting best at 0 invents an empty stretch the problem did not offer.',
                'The decision at each item is max(item, run + item). That is the whole algorithm.',
            ],
            'rows' => [
                ['run', 'Best sum ending here', 'Either this item alone, or this item glued to the previous run.'],
                ['best', 'Best sum anywhere', 'Updated whenever run beats it.'],
                ['Start over', 'item > run + item', 'The previous run was a burden.'],
                ['All negative', 'The least negative item', 'Do not answer 0 unless an empty stretch is allowed.'],
                ['One walk', 'Each item once', 'Linear.'],
                ['Indexes', 'Optional', 'The sum does not remember where the stretch started unless you store it.'],
            ],
            'e1' => ['Example 1 — the stretch that sums to 6', '4 + -1 + 2 + 1 is 6. The leading negatives are discarded by starting over.', <<<'PY'
nums = [-2, 1, -3, 4, -1, 2, 1, -5, 4]
best = nums[0]
run = nums[0]
for n in nums[1:]:
    run = n if n > run + n else run + n
    if run > best:
        best = run
print(best)
PY],
            'e2' => ['Example 2 — all negative, so 0 is a lie', 'The best stretch is -1. A best that starts at 0 never moves, because no sum is above 0.', <<<'PY'
nums = [-5, -1, -3]
best = nums[0]
run = nums[0]
for n in nums[1:]:
    run = n if n > run + n else run + n
    if run > best:
        best = run
print('honest', best)

fake = 0
for n in nums:
    if n > fake:
        fake = n
print('fake started at 0', fake)
PY],
            'micro' => [
                'nums[0] is the starting best so an all-negative row still has an answer. An empty row has no answer. Guard it.',
                'This finds the sum. If the caller needs the start and end indexes, store them when you start over and when you update best.',
                '“Contiguous” is the constraint. The sum of all positive items is a different problem and can be larger.',
            ],
            'quiz' => [
                $q('What is run?', 'The best sum of a stretch that ends at the current item', 'The total of the whole row', 'The count of negatives', 'The first item forever'),
                $q('When do you start a new stretch?', 'The current item is better than gluing it to the old run', 'The item is positive', 'The index is even', 'best is negative'),
                $q('Why is starting best at 0 wrong for [-5, -1, -3]?', 'The real best is -1, and 0 is not a stretch in the row', 'Python cannot add negatives', 'The row is unsorted', '-1 is not contiguous'),
            ],
            'practice' => 'Walk [2, -5, 3, 1] and write run and best after each item. The answer is 4.',
        ]),
        $L([
            'section' => '8. Patterns to master',
            'title' => 'Fewest coins, one cell at a time',
            'keywords' => 'coin change, unbounded knapsack, dynamic programming',
            'lead' => 'The knapsack chapter chose each item at most once. Coins are different: you may use a coin again. The cell for amount a is the fewest coins that make a, built from the cells for smaller amounts.',
            'heading' => 'A coin extends a smaller answer',
            'para' => 'With coins 1, 3, and 4, the amount 6 can be 3 + 3, which is two coins, or 4 + 1 + 1, which is three. The cell for 6 looks at 6 - 1, 6 - 3, and 6 - 4, adds one coin to each of those answers, and keeps the smallest. A cell that is still the sentinel means that amount cannot be made. Amount 0 takes zero coins.',
            'bullets' => [
                'fewest[0] = 0. Every other cell starts at a sentinel bigger than any real answer, such as amount + 1.',
                'A coin bigger than the amount is skipped. You cannot give change with a coin larger than the target.',
                'Because the coin may be reused, you walk coins inside the amount loop, reading cells you just updated. That is the opposite order from the 0/1 knapsack, which must not reuse an item.',
                'If the final cell is still the sentinel, say it is impossible. Do not return the sentinel as if it were a coin count.',
            ],
            'rows' => [
                ['fewest[a]', 'Fewest coins that sum to a', 'Built from smaller cells.'],
                ['Reuse', 'The same coin again', 'Allowed here.'],
                ['Transition', 'fewest[a - coin] + 1', 'One more coin on a solved smaller amount.'],
                ['Sentinel', 'amount + 1', 'Means “not solved yet”.'],
                ['Order', 'Grow the amount', 'So smaller amounts exist first.'],
                ['Impossible', 'Sentinel remains', 'Report that, not the number itself.'],
            ],
            'e1' => ['Example 1 — six takes two coins', '3 + 3 beats 4 + 1 + 1. The cell prints 2.', <<<'PY'
coins = [1, 3, 4]
amount = 6
fewest = [0] + [amount + 1] * amount
for a in range(1, amount + 1):
    for coin in coins:
        if coin <= a:
            fewest[a] = min(fewest[a], fewest[a - coin] + 1)
print(fewest)
print('coins for 6', fewest[6])
PY],
            'e2' => ['Example 2 — a coin that cannot make the amount', 'Coins of 2 cannot make 3. The cell stays at the sentinel, and the report is None.', <<<'PY'
coins = [2]
amount = 3
sentinel = amount + 1
fewest = [0] + [sentinel] * amount
for a in range(1, amount + 1):
    for coin in coins:
        if coin <= a:
            fewest[a] = min(fewest[a], fewest[a - coin] + 1)
answer = None if fewest[amount] == sentinel else fewest[amount]
print(answer)
PY],
            'micro' => [
                'Walking the coins in the outer loop and the amounts in the inner loop is also legal for unbounded coins, as long as each amount reads a smaller one. Do not mix that order with a 0/1 item.',
                'Greedy “always take the largest coin” fails on coins 1, 3, 4 and amount 6: it grabs 4 and then two 1s, three coins, missing two 3s.',
                'The sentinel must be strictly worse than any real count. amount + 1 is enough because you cannot need more than amount coins of value 1, and if 1 is missing you still only compare against solved cells.',
            ],
            'quiz' => [
                $q('What does fewest[0] store?', '0', '1', 'The largest coin', 'The sentinel'),
                $q('How does this differ from the 0/1 knapsack?', 'A coin may be used again', 'Coins cannot be integers', 'You sort the coins and stop', 'Each amount is solved by a graph search only'),
                $q('What does a leftover sentinel mean?', 'That amount cannot be made', 'The answer is zero coins', 'You should take the largest coin', 'The loop forgot amount 0'),
            ],
            'practice' => 'Fill fewest for coins [1, 5] and amount 7. Say how many coins make 7.',
        ]),
        $L([
            'section' => '8. Patterns to master',
            'title' => 'Meetings that overlap become one block',
            'keywords' => 'merge intervals, overlapping meetings, sort by start',
            'lead' => 'A calendar is a list of meetings, each with a start and an end. If the next meeting starts before the current block ends, they are one busy block. Sort by start first. Without that sort, a later meeting on the page might actually begin earlier.',
            'heading' => 'Sort, then extend or append',
            'para' => 'Meetings (1, 3), (2, 5), and (7, 9) sort into that same order. 2 is before 3, so the first block grows to (1, 5). 7 is after 5, so a new block (7, 9) starts. The busy time is two blocks, not three meetings. Touching at an endpoint is your decision: this lesson treats an end equal to the next start as an overlap, one continuous block.',
            'bullets' => [
                'Sort by start. Sorting by end answers a different greedy problem, the one about how many meetings fit in one room.',
                'Compare the next start with the end of the last merged block, not with the original previous meeting. The block may already have grown.',
                'The new end is the max of the two ends. A meeting inside another meeting must not shrink the outer end.',
                'A meeting that starts after the block ends is appended. It does not extend the previous block.',
            ],
            'rows' => [
                ['Meeting', '(start, end)', 'End is the first free moment, in this lesson.'],
                ['Sort', 'By start', 'So you only look backward one block.'],
                ['Overlap', 'start <= last end', 'They become one block.'],
                ['Extend', 'End becomes the later end', 'Never the earlier end.'],
                ['Gap', 'start > last end', 'Append a new block.'],
                ['Touch', 'start == last end', 'This lesson merges them.'],
            ],
            'e1' => ['Example 1 — three meetings, two blocks', '(1, 3) grows to (1, 5). (7, 9) stays separate.', <<<'PY'
meetings = [(1, 3), (2, 5), (7, 9)]
ordered = sorted(meetings)
merged = [ordered[0]]
for start, end in ordered[1:]:
    last_start, last_end = merged[-1]
    if start <= last_end:
        merged[-1] = (last_start, max(last_end, end))
    else:
        merged.append((start, end))
print(merged)
PY],
            'e2' => ['Example 2 — a meeting hidden inside another', '(4, 5) is inside (1, 8). The end stays 8. Taking the inner end would punch a hole that is still busy.', <<<'PY'
meetings = [(1, 8), (4, 5)]
ordered = sorted(meetings)
merged = [ordered[0]]
for start, end in ordered[1:]:
    last_start, last_end = merged[-1]
    if start <= last_end:
        merged[-1] = (last_start, max(last_end, end))
    else:
        merged.append((start, end))
print(merged)
PY],
            'micro' => [
                'An empty calendar has no first meeting. Guard that before you read ordered[0].',
                'max(last_end, end) is the line people replace with end. That is wrong when the new meeting finishes earlier.',
                'If the room problem asks “how many rooms,” you are not merging. You are counting how many blocks overlap at the busiest moment.',
            ],
            'quiz' => [
                $q('What do you sort by?', 'Start time', 'End time, always, for this problem', 'Length of the meeting', 'The original list index only'),
                $q('When do two meetings merge?', 'The next start is at or before the current block end', 'They are next to each other in the input', 'They have the same length', 'Both start at 0'),
                $q('Why use max of the two ends?', 'The inner meeting must not shrink the outer block', 'Ends are always equal', 'max sorts the meetings', 'The start might be larger than the end'),
            ],
            'practice' => 'Merge [(0, 2), (3, 4), (2, 3)]. Decide from the rule whether you get one block or two, then run the same loop.',
        ]),
        $L([
            'section' => '2. Linear structures',
            'title' => 'Brackets: every opener needs its closer',
            'keywords' => 'balanced parentheses, stack, matching brackets',
            'lead' => 'A string of brackets is balanced when every closer matches the nearest unmatched opener, and nothing is left open at the end. A stack is that “nearest”: you push openers and pop them when the closer arrives.',
            'heading' => 'The closer must match the top',
            'para' => '(a[b]) is balanced. The stack grows with ( then [, the ] pops the [, and the ) pops the (. (] fails because ] arrives while ( is on top. A leftover opener at the end fails too: the string finished while something was still open. Letters are ignored. They are not brackets.',
            'bullets' => [
                'Push only openers. A closer never goes on the stack in this algorithm.',
                'A closer against an empty stack is an error. There is no opener waiting.',
                'The closer must match the top, not some earlier opener. ([)] is wrong even though each shape appears once.',
                'After the last character the stack must be empty. An empty stack halfway through is fine.',
            ],
            'rows' => [
                ['Opener', 'Push', '( [ { are the usual three.'],
                ['Closer', 'Pop and compare', 'The partner map says who matches whom.'],
                ['Empty pop', 'Failure', 'A closer arrived too soon.'],
                ['Wrong top', 'Failure', 'The nearest opener is a different shape.'],
                ['Leftover', 'Failure', 'An opener never closed.'],
                ['Other characters', 'Ignore', 'Or reject them, if the contract says the string is only brackets.'],
            ],
            'e1' => ['Example 1 — one string works, one does not', '(a[b]) ends empty. (] tries to close a round opener with a square closer.', <<<'PY'
def balanced(text):
    pairs = {')': '(', ']': '[', '}': '{'}
    stack = []
    for ch in text:
        if ch in '([{':
            stack.append(ch)
        elif ch in pairs:
            if not stack or stack[-1] != pairs[ch]:
                return False
            stack.pop()
    return not stack

print(balanced('(a[b])'))
print(balanced('(]'))
print(balanced('(('))
PY],
            'e2' => ['Example 2 — crossed shapes fail', '([)] pops nothing legal: the ] sees ( on top. The count of each shape would say “one each” and would be wrong.', <<<'PY'
def counts_lie(text):
    return text.count('(') == text.count(')') and text.count('[') == text.count(']')

def balanced(text):
    pairs = {')': '(', ']': '['}
    stack = []
    for ch in text:
        if ch in '([':
            stack.append(ch)
        elif ch in pairs:
            if not stack or stack[-1] != pairs[ch]:
                return False
            stack.pop()
    return not stack

print('counts', counts_lie('([)]'))
print('stack', balanced('([)]'))
PY],
            'micro' => [
                'not stack is True when the stack is empty. Returning the stack itself would print [] and look like a failure to a caller expecting a bool.',
                'A partner map from closer to opener keeps the test in one line. Mapping the other way also works if you compare carefully.',
                'This is the same stack as the undo pile. The nearest opener is the top, which is the last one you pushed.',
            ],
            'quiz' => [
                $q('What goes on the stack?', 'Openers', 'Closers', 'Every character', 'Only letters'),
                $q('Why does ([)] fail?', 'The closer does not match the nearest opener', 'The counts differ', 'The string is too long', 'Square brackets are illegal'),
                $q('What must be true at the end?', 'The stack is empty', 'The stack holds one opener', 'The last character is an opener', 'The length is even only, with no further check'),
            ],
            'practice' => 'Mark balanced or not: (), (()), ())(, and {[()]}. Use the stack, not the counts.',
        ]),
        $L([
            'section' => '2. Linear structures',
            'title' => 'A slow step and a fast step on a loop',
            'keywords' => 'floyd cycle, tortoise hare, linked list loop',
            'lead' => 'A linked list loops when some next arrow points at an earlier node. Walking with a set of seen nodes finds it and uses extra memory. Floyd’s walk does not: a slow pointer steps once, a fast pointer steps twice. On a loop the fast one laps the slow one. On a straight list the fast one falls off the end.',
            'heading' => 'If they meet, there is a loop',
            'para' => 'Nodes 1 → 2 → 3 → 2 form a loop. Slow and fast start at 1. They move together. Fast gains one step per round, so inside the loop it eventually lands on the same node as slow. A straight list 1 → 2 → 3 ends when fast’s next next is missing. You stop. Meeting is the proof of a loop. Not meeting before the end is the proof there is none.',
            'bullets' => [
                'Start both pointers on the same node, and move them before you compare, or they “meet” at the start and you report a loop that is only the start.',
                'Fast must check that its next node exists before it steps twice. A missing next means the end, not a loop.',
                'Meeting tells you a loop exists. It does not, by itself, tell you the length. A second walk can count that.',
                'The set-of-seen-nodes method is easier to read and uses memory. Floyd uses two pointers and no set.',
            ],
            'rows' => [
                ['Slow', 'One step', 'slow = slow.nxt'],
                ['Fast', 'Two steps', 'fast = fast.nxt.nxt'],
                ['Meet', 'Same node', 'A loop exists.'],
                ['Fall off', 'fast.nxt is missing', 'No loop.'],
                ['Start', 'Move before comparing', 'Otherwise the start looks like a meeting.'],
                ['Extra memory', 'None for the pointers', 'The set method spends a slot per node.'],
            ],
            'e1' => ['Example 1 — they meet inside the loop', '3 points back at 2. The walk reports a loop and the node value where they met.', <<<'PY'
class Node:
    def __init__(self, value, nxt=None):
        self.value = value
        self.nxt = nxt

a = Node(1)
b = Node(2)
c = Node(3)
a.nxt = b
b.nxt = c
c.nxt = b

slow = a
fast = a
loop = False
for _ in range(8):
    if fast.nxt is None or fast.nxt.nxt is None:
        break
    slow = slow.nxt
    fast = fast.nxt.nxt
    if slow is fast:
        loop = True
        break
print(loop, slow.value)
PY],
            'e2' => ['Example 2 — a straight list falls off', '3 has no next. The fast pointer stops. loop stays False.', <<<'PY'
class Node:
    def __init__(self, value, nxt=None):
        self.value = value
        self.nxt = nxt

a = Node(1, Node(2, Node(3)))
slow = a
fast = a
loop = False
for _ in range(8):
    if fast.nxt is None or fast.nxt.nxt is None:
        break
    slow = slow.nxt
    fast = fast.nxt.nxt
    if slow is fast:
        loop = True
        break
print(loop)
PY],
            'micro' => [
                'Use is, not ==. You are asking whether both names point at the same node, not whether two nodes happen to hold the same number.',
                'A loop of one node, whose next is itself, still meets. Do not special-case length.',
                'Finding where the loop starts is a second algorithm: reset one pointer to the head and step both once. This chapter only answers “is there a loop?”.',
            ],
            'quiz' => [
                $q('What does a meeting prove?', 'Some next arrow points backward into the nodes you are walking', 'The list is sorted', 'The list has two nodes', 'The values are unique'),
                $q('Why move before comparing at the start?', 'They begin on the same node, so an immediate compare is not a loop', 'The fast pointer cannot move first', 'Python requires it', 'The slow pointer is always None'),
                $q('What does the fast pointer do at the end of a straight list?', 'It stops because a next node is missing', 'It wraps to the head', 'It reports a loop', 'It swaps with slow'),
            ],
            'practice' => 'Draw 1 → 2 → 1. Step slow and fast on paper until they meet, and name the node.',
        ]),
        $L([
            'section' => '2. Linear structures',
            'title' => 'Turn each arrow to point backward',
            'keywords' => 'reverse linked list, three pointers, in place',
            'lead' => 'Reversing a linked list does not build a second list. Each node’s arrow turns around to point at the node that used to point at it. You need three names: the node you are turning, the one before it, and the one after it, because turning the arrow forgets the old forward direction.',
            'heading' => 'Save the next arrow first',
            'para' => 'On 1 → 2 → 3, start with prev as nothing and node as 1. Save 2, point 1 at nothing, then step forward. Next, save 3, point 2 at 1, step forward. Then point 3 at 2. prev is now 3, which is the new head. Walking it prints 3, 2, 1. If you point the arrow backward before saving nxt, the rest of the list is gone.',
            'bullets' => [
                'The order inside the loop is: save nxt, turn the arrow, move prev up, move node to the saved nxt.',
                'prev starts as None. The old head’s arrow becomes None, which is the new tail.',
                'When node becomes None, prev is the new head. Returning node would return nothing.',
                'An empty list and a one-node list both work. The loop simply does not run, or runs once.',
            ],
            'rows' => [
                ['node', 'The arrow you are turning', 'Starts at the head.'],
                ['prev', 'The node behind you', 'Starts as None.'],
                ['nxt', 'The rest of the list', 'Saved before the turn.'],
                ['Turn', 'node.nxt = prev', 'The arrow now points backward.'],
                ['New head', 'prev at the end', 'Not node, which is None.'],
                ['Lost rest', 'You forgot to save nxt', 'The list from here on is unreachable.'],
            ],
            'e1' => ['Example 1 — 1, 2, 3 becomes 3, 2, 1', 'The walk after the turns prints the values from the new head.', <<<'PY'
class Node:
    def __init__(self, value, nxt=None):
        self.value = value
        self.nxt = nxt

node = Node(1, Node(2, Node(3)))
prev = None
while node:
    nxt = node.nxt
    node.nxt = prev
    prev = node
    node = nxt

out = []
walker = prev
while walker:
    out.append(walker.value)
    walker = walker.nxt
print(out)
PY],
            'e2' => ['Example 2 — one node stays one node', 'There is no forward arrow to save. The head is still that node, and its arrow is None.', <<<'PY'
class Node:
    def __init__(self, value, nxt=None):
        self.value = value
        self.nxt = nxt

node = Node(7)
prev = None
while node:
    nxt = node.nxt
    node.nxt = prev
    prev = node
    node = nxt
print(prev.value, prev.nxt)
PY],
            'micro' => [
                'Draw the three names on paper for a list of three. The bug is almost always turning the arrow one line too early.',
                'This uses no second list. A version that pushes values onto a stack and builds new nodes also reverses, and it spends extra memory.',
                'If some other name still points at the old head, that name now points at the tail. Tell the caller the new head.',
            ],
            'quiz' => [
                $q('Why save nxt before turning the arrow?', 'Turning the arrow forgets the forward node', 'nxt is the new head', 'The stack requires it', 'Values must be sorted first'),
                $q('What is the new head when the loop ends?', 'prev', 'node', 'nxt', 'The old head always'),
                $q('What does the old head point at after a reverse of three nodes?', 'None', 'The old third node', 'Itself', 'The integer 0'),
            ],
            'practice' => 'Reverse 4 → 5 on paper. Write prev, node, and nxt before each turn.',
        ]),
        $L([
            'section' => '3. Lookup',
            'title' => 'Even, odd, and a mask of bits',
            'keywords' => 'bitwise and, shift, parity, bit mask',
            'lead' => 'A whole number is a row of bits. The last bit is the ones place. It is 1 for odd numbers and 0 for even numbers. Shifting right drops that bit and halves the number, the same way integer division by 2 does. A mask keeps only the bits you care about.',
            'heading' => 'The last bit is the parity',
            'para' => '13 is 1101 in binary. 13 & 1 is 1, so 13 is odd. 13 >> 1 is 6, which is 110. The bit second from the right was 0, and (13 >> 1) & 1 reads it. You did not convert the number to a string of digits. The machine already stores it as bits. & with 1 is a mask of a single bit.',
            'bullets' => [
                'n & 1 is 0 or 1. It is the remainder n % 2 for non-negative n, written as a bit test.',
                'n >> 1 drops the last bit. For a non-negative n it equals n // 2.',
                'n << 1 shifts in a zero at the right and doubles n, until the number grows past the width you are imagining.',
                'A mask such as 0b111 keeps the last three bits and clears the rest: n & 0b111.',
            ],
            'rows' => [
                ['&', 'Keep bits that are 1 in both', '1 & 1 is 1. Anything & 0 is 0.'],
                ['|', 'Keep bits that are 1 in either', 'A way to turn a bit on.'],
                ['>>', 'Shift right', 'Halves a non-negative integer.'],
                ['<<', 'Shift left', 'Doubles, filling a zero.'],
                ['Mask', 'A pattern of bits you keep', '& with the pattern.'],
                ['Parity', 'Last bit', '1 odd, 0 even, for the numbers in this lesson.'],
            ],
            'e1' => ['Example 1 — 13 is odd, and its halves', 'The last bit is 1. One shift gives 6. Two shifts give 3.', <<<'PY'
n = 13
print('last bit', n & 1)
print('shift 1', n >> 1)
print('shift 2', n >> 2)
print('bit 1 of the original', (n >> 1) & 1)
PY],
            'e2' => ['Example 2 — a mask keeps the last three bits', '13 is 1101. The last three bits are 101, which is 5. The high 1 is cleared.', <<<'PY'
n = 13
mask = 0b111
print(n & mask)
print(bin(n))
print(bin(n & mask))
PY],
            'micro' => [
                'This lesson uses non-negative numbers. A right shift of a negative is a separate rule and differs across languages. Do not assume it halves toward zero.',
                '& has lower precedence than == in a way that surprises people. Write (n & 1) == 0 with parentheses when you mix them.',
                'bin(n) is a view for humans. The algorithm does not need the string.',
            ],
            'quiz' => [
                $q('What is n & 1 for an even non-negative n?', '0', '1', 'n', '2'),
                $q('What is 13 >> 1?', '6', '26', '12', '1'),
                $q('What does a mask do?', 'Keeps the bits that are 1 in the mask and clears the others', 'Sorts the bits', 'Adds the mask to n', 'Converts n to text'),
            ],
            'practice' => 'For 10, write the last bit, the value after one right shift, and the last three bits with the mask 0b111.',
        ]),
        $L([
            'section' => '3. Lookup',
            'title' => 'Shared divisors, then the small primes',
            'keywords' => 'euclidean algorithm, gcd, sieve of eratosthenes',
            'lead' => 'The greatest common divisor of two whole numbers is the largest number that divides both. Euclid’s rule says it equals the divisor of the smaller one and the remainder. Repeat until the remainder is 0. A sieve then uses division the other way: cross out multiples, and what remains up to a limit is prime.',
            'heading' => 'Replace the big number with the remainder',
            'para' => 'gcd(48, 18): 48 % 18 is 12, then gcd(18, 12), then 12 and 6, then 6 and 0. The answer is 6. You did not list the divisors of both numbers. The sieve up to 20 starts with every integer marked possible, clears 0 and 1, then clears multiples of 2, of 3, and stops when the factor’s square passes 20. The survivors are the primes.',
            'bullets' => [
                'gcd(a, 0) is a. That is the stop, so write it before you recurse or loop.',
                'The remainder is always smaller than the divisor, so the loop ends.',
                'A prime has no divisor other than 1 and itself. 1 is not prime. The sieve must clear index 1.',
                'Start crossing multiples at p * p. Smaller multiples were already crossed by smaller primes.',
            ],
            'rows' => [
                ['Remainder', 'a % b', 'The new smaller problem.'],
                ['Swap', 'b becomes a, remainder becomes b', 'One step of Euclid.'],
                ['Stop', 'Remainder 0', 'The other number is the gcd.'],
                ['Prime', 'Not crossed out', 'And not 0 or 1.'],
                ['p * p', 'Where crossing starts', 'Earlier multiples are already gone.'],
                ['Limit', 'The sieve’s end', 'It does not know primes past the limit.'],
            ],
            'e1' => ['Example 1 — 48 and 18 share 6', 'The remainders are 12, then 6, then 0. The printed divisor is 6.', <<<'PY'
def gcd(a, b):
    while b:
        a, b = b, a % b
    return a

print(gcd(48, 18))
print(gcd(7, 3))
PY],
            'e2' => ['Example 2 — primes up to 20', '0 and 1 are cleared. Multiples of 2 and 3 fall. The list is the primes.', <<<'PY'
def sieve(limit):
    prime = [True] * (limit + 1)
    prime[0] = False
    prime[1] = False
    p = 2
    while p * p <= limit:
        if prime[p]:
            m = p * p
            while m <= limit:
                prime[m] = False
                m += p
        p += 1
    return [i for i, ok in enumerate(prime) if ok]

print(sieve(20))
PY],
            'micro' => [
                'gcd(0, 0) is a special case people define as 0. This loop returns 0. Do not call it the greatest common divisor in a proof.',
                'The sieve list is limit + 1 long so index 20 exists when the limit is 20.',
                'A number can be prime and still have been tested only up to its square root. That is enough: a larger factor would pair with a smaller one you already checked.',
            ],
            'quiz' => [
                $q('What replaces the larger number in Euclid’s step?', 'The remainder', 'The sum', 'The product', 'Always 1'),
                $q('Why is 1 cleared in the sieve?', '1 is not a prime', '1 is even', 'Indexes start at 1', '1 is greater than the limit'),
                $q('Where do you start crossing multiples of p?', 'At p times p', 'At p + 1', 'At 0', 'At the limit'),
            ],
            'practice' => 'Compute gcd(20, 8) by hand with remainders. Then list the primes the sieve keeps up to 10.',
        ]),
        $L([
            'section' => '7. Graphs',
            'title' => 'Cheap edges that do not close a loop',
            'keywords' => 'kruskal, minimum spanning tree, union find',
            'lead' => 'A spanning tree connects every node with no loop, and it has one fewer edge than there are nodes when the graph is connected. Kruskal builds the cheapest such tree: sort the edges by cost, and add an edge only when its two ends are not already in the same group. Union-find is that group test.',
            'heading' => 'Skip an edge that would loop',
            'para' => 'Edges a—b cost 1, a—c cost 2, b—c cost 4, c—d cost 7. The cheapest is a—b, and it joins two groups. a—c joins a new node. b—c would connect a and c, who are already connected through the tree, so it is skipped. c—d joins the last node. The tree costs 1 + 2 + 7. The skipped edge is the one that looked useful and would have made a loop.',
            'bullets' => [
                'Sort by cost ascending. A later expensive edge is considered only after every cheaper one.',
                'unite returns false when the two ends were already connected. That edge is not added.',
                'Stop when you have n - 1 edges, if you know the graph is connected. Adding more would create a loop.',
                'If you finish the edges and some node was never joined, the graph was not connected. There is no spanning tree. There is a forest.',
            ],
            'rows' => [
                ['Sort', 'Cheapest edge first', 'The greedy choice.'],
                ['Same group', 'Skip', 'The edge would close a loop.'],
                ['Different groups', 'Add the edge and unite', 'The tree grows by one edge.'],
                ['n - 1 edges', 'A tree on n nodes', 'Connected and no loop.'],
                ['Forest', 'More than one group at the end', 'The original graph was disconnected.'],
                ['Cost', 'Sum of accepted edges', 'Not the sum of all edges.'],
            ],
            'e1' => ['Example 1 — one edge is rejected', 'b—c costs 4 and is skipped. The accepted edges connect a, b, c, and d.', <<<'PY'
parent = {}

def find(x):
    parent.setdefault(x, x)
    while parent[x] != x:
        x = parent[x]
    return x

def unite(a, b):
    ra, rb = find(a), find(b)
    if ra == rb:
        return False
    parent[ra] = rb
    return True

edges = [(1, 'a', 'b'), (2, 'a', 'c'), (4, 'b', 'c'), (7, 'c', 'd')]
chosen = []
for cost, u, v in edges:
    if unite(u, v):
        chosen.append((u, v, cost))
print(chosen)
print('total', sum(item[2] for item in chosen))
PY],
            'e2' => ['Example 2 — a disconnected graph stays a forest', 'd never appears. Two groups remain, and the edge count is not n - 1 for four names.', <<<'PY'
parent = {}

def find(x):
    parent.setdefault(x, x)
    while parent[x] != x:
        x = parent[x]
    return x

def unite(a, b):
    ra, rb = find(a), find(b)
    if ra == rb:
        return False
    parent[ra] = rb
    return True

edges = [(1, 'a', 'b'), (2, 'b', 'c')]
chosen = []
for cost, u, v in edges:
    if unite(u, v):
        chosen.append((u, v, cost))
print(chosen)
print('groups', len({find(node) for node in ['a', 'b', 'c', 'd']}))
PY],
            'micro' => [
                'The edges in example 1 are already sorted. If a caller hands you unsorted edges, sort them or you are not running Kruskal.',
                'Union by rank and path compression make find faster. The plain parent walk in this lesson is the same answer on a tiny graph.',
                'A minimum spanning tree is not the shortest path between two nodes. Dijkstra answers that other question.',
            ],
            'quiz' => [
                $q('When do you reject an edge?', 'Its ends are already in the same group', 'It is the cheapest', 'It touches a new node', 'It is the last edge'),
                $q('How many edges does a tree on n connected nodes have?', 'n - 1', 'n', 'n + 1', '1'),
                $q('What do you have if a node is never joined?', 'A forest, not one spanning tree', 'A shortest path', 'A loop', 'A sorted row'),
            ],
            'practice' => 'Add an edge d—a with cost 3 to example 1. Say whether Kruskal accepts it, and what the new total is.',
        ]),
        $L([
            'section' => '7. Graphs',
            'title' => 'A road can have a negative price',
            'keywords' => 'bellman-ford, negative edge, relaxation',
            'lead' => 'Dijkstra assumes every road has a non-negative price. If a road pays you, a later cheap road can improve a node Dijkstra already finished. Bellman-Ford does not finish nodes early. It relaxes every road, once per node, and lets a negative road correct an earlier guess.',
            'heading' => 'Relax every road, several rounds',
            'para' => 'Roads: s→a costs 4, s→b costs 5, a→b costs -2, b→c costs 3. Start s at 0 and everyone else at a huge number. After enough rounds, a is 4, b is 4 + -2 = 2, and c is 5. The direct road s→b at cost 5 was a trap. The negative road made b cheaper. A round that changes nothing can stop. A change on an extra round, after n - 1 rounds, means a negative loop you can ride forever.',
            'bullets' => [
                'Relax means: if dist[u] + cost is smaller than dist[v], store that smaller distance.',
                'You need up to n - 1 rounds because a cheapest path has at most n - 1 roads and no loop.',
                'A negative road is allowed. A negative loop is not, if you want a finite answer.',
                'Dijkstra would lock b at 5 if it settled b before seeing a→b. That is the bug this chapter exists to prevent.',
            ],
            'rows' => [
                ['dist', 'Best price known so far', 'Start is 0. Others start huge.'],
                ['Relax', 'Offer a cheaper price', 'Keep it only when it wins.'],
                ['Round', 'Every road once', 'Repeat up to n - 1 times.'],
                ['Negative road', 'Legal', 'It can improve a later node.'],
                ['Negative loop', 'A change after n - 1 rounds', 'No finite cheapest path.'],
                ['Huge start', 'Not a real price', 'A node still huge was never reached.'],
            ],
            'e1' => ['Example 1 — the negative road wins', 'b becomes 2, not 5. c becomes 5.', <<<'PY'
roads = [('s', 'a', 4), ('s', 'b', 5), ('a', 'b', -2), ('b', 'c', 3)]
dist = {'s': 0, 'a': 99, 'b': 99, 'c': 99}
for _ in range(3):
    for u, v, cost in roads:
        if dist[u] + cost < dist[v]:
            dist[v] = dist[u] + cost
print(dist)
PY],
            'e2' => ['Example 2 — a negative loop never settles', 'b→a costs -3 and a→b costs -2. An extra round still finds a cheaper price. The flag says the loop exists.', <<<'PY'
roads = [('a', 'b', -2), ('b', 'a', -3)]
dist = {'a': 0, 'b': 99}
for _ in range(1):
    for u, v, cost in roads:
        if dist[u] + cost < dist[v]:
            dist[v] = dist[u] + cost
changed = False
for u, v, cost in roads:
    if dist[u] + cost < dist[v]:
        changed = True
print('still improving', changed, dist)
PY],
            'micro' => [
                '99 is a teaching stand-in for “unreachable.” If a real road can cost more than 99, pick a bigger sentinel or track “reached” separately.',
                'The order of roads inside a round can change how fast the prices fall. It does not change the final prices when no negative loop exists.',
                'Do not use this when every road is non-negative and the graph is huge. Dijkstra does less work on that input. Use this when a price can be negative.',
            ],
            'quiz' => [
                $q('Why can Dijkstra fail on a negative road?', 'It can finish a node before a cheaper negative road is seen', 'It cannot add numbers', 'It only works on trees', 'Negative roads are syntax errors'),
                $q('How many rounds are enough when there is no negative loop?', 'n - 1', '1', 'n squared always, with no earlier stop', '0'),
                $q('What does a cheaper price on one more round mean?', 'A negative loop', 'The start was wrong', 'The graph is sorted', 'Dijkstra should take over'),
            ],
            'practice' => 'Add a road c→d with cost -1 to example 1. Compute d’s price after the rounds.',
        ]),
        $L([
            'section' => '4. Recursion and search',
            'title' => 'Binary search on the size of the answer',
            'keywords' => 'binary search on answer, feasibility, capacity',
            'lead' => 'Sometimes you are not searching a sorted row. You are searching the smallest number that makes a yes/no check pass. If a bigger number would also pass, and every smaller number fails, the answers are sorted from no to yes. Binary search can walk that range.',
            'heading' => 'Search the capacity, check the days',
            'para' => 'You must ship weights [1, 2, 3, 4] in at most 3 days. A day’s load cannot exceed the ship’s capacity, and one weight cannot be split. Capacity must be at least 4, the heaviest box, and at most 10, the sum. Capacity 4 uses three days: 1+2, then 3, then 4. Capacity 3 is impossible because the box of 4 does not fit. The smallest passing capacity is 4. The search moves the high end down when the check passes, and the low end up when it fails.',
            'bullets' => [
                'The range is the possible answers, not the indexes of the weights.',
                'The check must be monotonic: if capacity c works, every larger capacity works.',
                'When the check passes, the answer is mid or smaller, so hi = mid. When it fails, lo = mid + 1.',
                'lo == hi is the smallest passing answer, if you kept that invariant.',
            ],
            'rows' => [
                ['lo', 'Smallest answer still possible', 'Here, the heaviest box.'],
                ['hi', 'Largest answer still possible', 'Here, the sum of the boxes.'],
                ['mid', 'The capacity you try', '(lo + hi) // 2'],
                ['Check', 'How many days would this capacity need?', 'A walk of the weights.'],
                ['Pass', 'hi = mid', 'Try to go smaller.'],
                ['Fail', 'lo = mid + 1', 'This capacity is too small.'],
            ],
            'e1' => ['Example 1 — the smallest ship that finishes in 3 days', 'The search prints 4. A day packs weights until one more would overflow.', <<<'PY'
weights = [1, 2, 3, 4]
days_limit = 3
lo = max(weights)
hi = sum(weights)

def days_needed(capacity):
    days = 1
    load = 0
    for w in weights:
        if load + w > capacity:
            days += 1
            load = 0
        load += w
    return days

while lo < hi:
    mid = (lo + hi) // 2
    if days_needed(mid) <= days_limit:
        hi = mid
    else:
        lo = mid + 1
print(lo)
PY],
            'e2' => ['Example 2 — a capacity below the heaviest box is not in range', 'max(weights) is 4. Starting lo at 0 would ask the check to pack a box of 4 into a ship of 0. The range starts at the heaviest box so every mid is physically possible.', <<<'PY'
weights = [1, 2, 3, 4]
print('lightest legal ship', max(weights))
print('heaviest we bother trying', sum(weights))
PY],
            'micro' => [
                'hi = mid, not mid - 1, when mid itself passed. mid might be the answer.',
                'An infinite loop happens if a failing mid sets lo = mid. The fail branch must move lo to mid + 1.',
                'This pattern fits “smallest speed,” “smallest maximum,” and “first true” on a sorted yes/no range. It does not fit a check that flips from yes back to no.',
            ],
            'quiz' => [
                $q('What are you binary searching?', 'The capacity, which is the answer', 'The index of the lightest box', 'The number of boxes', 'A graph node'),
                $q('Why is the low end the heaviest box?', 'A single box must fit in the ship', 'Ships cannot be larger than one box', 'The sum might be zero', 'Days must equal the heaviest box'),
                $q('When the check passes, which end moves?', 'The high end moves down to mid', 'The low end becomes mid + 1', 'Both ends become 0', 'The search stops only if mid is 0'),
            ],
            'practice' => 'Change the day limit to 2 and predict the capacity before you run. One legal packing is 1+2+3 and then 4, so think about 6.',
        ]),
        $L([
            'section' => '7. Graphs',
            'title' => 'Flood fill paints the connected cells',
            'keywords' => 'flood fill, grid, connected component, four directions',
            'lead' => 'A grid is a graph whose nodes are cells. An edge exists between two cells that share a side and both count as paintable. Flood fill starts at one cell and paints every cell you can reach. Cells you can reach only by jumping a blocked cell stay unpainted.',
            'heading' => 'Four directions, and a seen mark',
            'para' => 'In a grid of 1s and 0s, 1 is land. From the top-left land cell you can walk right and down onto other land, but not through the 0s. The component has three cells. The lonely 1 in the corner is a second component. Mark a cell seen when you first queue it, not when you leave it, or the same neighbour gets queued many times.',
            'bullets' => [
                'The four directions are up, down, left, and right. Diagonals are a different contract. Say which one you mean.',
                'Stay inside the grid. Row -1 and column 3 on a 3 by 3 grid are not cells.',
                'A 0 is not paintable in this lesson. Do not step on it.',
                'The count of cells you paint is the size of that component. Counting components is “start a fill on every unseen land cell.”',
            ],
            'rows' => [
                ['Cell', 'A node', 'Identified by row and column.'],
                ['Side', 'An edge', 'Not a corner, in the four-direction rule.'],
                ['Seen', 'Already queued', 'Stops a cell from being painted twice.'],
                ['Bound', '0 <= row < height', 'Check before you index.'],
                ['Blocked', 'Value 0', 'No step.'],
                ['Component', 'Cells this fill reached', 'One start cell, one component.'],
            ],
            'e1' => ['Example 1 — three land cells in the first component', 'The start is the top left. The corner 1 is never reached.', <<<'PY'
grid = [
    [1, 1, 0],
    [1, 0, 0],
    [0, 0, 1],
]
seen = [[False] * 3 for _ in range(3)]
stack = [(0, 0)]
seen[0][0] = True
painted = 0
while stack:
    r, c = stack.pop()
    painted += 1
    for dr, dc in ((1, 0), (-1, 0), (0, 1), (0, -1)):
        nr, nc = r + dr, c + dc
        if 0 <= nr < 3 and 0 <= nc < 3 and grid[nr][nc] == 1 and not seen[nr][nc]:
            seen[nr][nc] = True
            stack.append((nr, nc))
print(painted)
PY],
            'e2' => ['Example 2 — count the components', 'Each unseen land cell starts a new fill. There are two islands.', <<<'PY'
grid = [
    [1, 1, 0],
    [1, 0, 0],
    [0, 0, 1],
]
seen = [[False] * 3 for _ in range(3)]

def fill(start_r, start_c):
    stack = [(start_r, start_c)]
    seen[start_r][start_c] = True
    while stack:
        r, c = stack.pop()
        for dr, dc in ((1, 0), (-1, 0), (0, 1), (0, -1)):
            nr, nc = r + dr, c + dc
            if 0 <= nr < 3 and 0 <= nc < 3 and grid[nr][nc] == 1 and not seen[nr][nc]:
                seen[nr][nc] = True
                stack.append((nr, nc))

islands = 0
for r in range(3):
    for c in range(3):
        if grid[r][c] == 1 and not seen[r][c]:
            fill(r, c)
            islands += 1
print(islands)
PY],
            'micro' => [
                '[[False] * 3] * 3 would alias the rows. Build each row with its own list, the way example 1 does.',
                'Marking seen when you pop, instead of when you push, lets two paths queue the same cell. The paint count then lies.',
                'Eight directions include corners. A thin diagonal of land becomes one island under eight directions and several islands under four.',
            ],
            'quiz' => [
                $q('When do you mark a cell seen?', 'When you first queue it', 'Only after the stack is empty', 'When you print it', 'Never, the grid value is enough'),
                $q('How many components are in the example grid?', '2', '1', '3', '9'),
                $q('Which step is illegal?', 'Reading row -1', 'Reading row 0', 'Skipping a 0', 'Starting on a 1'),
            ],
            'practice' => 'Change the middle-left cell to 0. Predict the paint count from the top left, then the number of islands.',
        ]),
        $L([
            'section' => '2. Linear structures',
            'title' => 'The next greater item waits on a stack',
            'keywords' => 'monotonic stack, next greater element',
            'lead' => 'For each item you want the first item to its right that is strictly greater. Checking the whole tail each time is quadratic. A stack of indexes waiting for a greater item does it in one walk: when a new item is bigger than the index on top, that waiting index has found its answer.',
            'heading' => 'Pop while the new item wins',
            'para' => 'On [2, 1, 4], 2 waits, then 1 waits on top of 2 because 1 is smaller. 4 is greater than 1, so 1’s answer is 4. 4 is also greater than 2, so 2’s answer is 4. 4 itself has nothing greater to the right, so it stays -1. The stack only holds a decreasing run of values that are still waiting. That shape is why it is called monotonic.',
            'bullets' => [
                'Store indexes, not only values. The answer is written back into a slot, and the value is read from the row.',
                'Pop while the top value is strictly smaller than the new value. Equal values do not satisfy “greater.”',
                'Items left on the stack at the end have no greater item to the right. They stay at the sentinel, here -1.',
                'Each index is pushed once and popped once. That is why the whole walk is linear even though a while loop sits inside.',
            ],
            'rows' => [
                ['Stack', 'Indexes still waiting', 'Their values are decreasing from the bottom.'],
                ['New item', 'The candidate greater value', 'It may answer several waiters.'],
                ['Pop', 'Waiter found its answer', 'The answer is the new value.'],
                ['Push', 'This index now waits', 'Something later might beat it.'],
                ['Sentinel', '-1', 'No greater item to the right.'],
                ['Strict', 'Greater, not greater-or-equal', 'A tie is not an answer for this contract.'],
            ],
            'e1' => ['Example 1 — 4 answers both waiters', 'The result is [4, 4, -1].', <<<'PY'
nums = [2, 1, 4]
answer = [-1] * len(nums)
stack = []
for i, n in enumerate(nums):
    while stack and nums[stack[-1]] < n:
        answer[stack.pop()] = n
    stack.append(i)
print(answer)
PY],
            'e2' => ['Example 2 — a decreasing row never pops', 'Each item is smaller than the waiter below it. Everyone stays -1.', <<<'PY'
nums = [5, 4, 3]
answer = [-1] * len(nums)
stack = []
for i, n in enumerate(nums):
    while stack and nums[stack[-1]] < n:
        answer[stack.pop()] = n
    stack.append(i)
print(answer)
PY],
            'micro' => [
                'The sentinel -1 is a bad choice if -1 can appear in the row as a real value and you are writing values. Writing the index of the greater item, or using None, avoids that collision.',
                '“Next greater to the left” is the same stack walked in the other direction, or the same idea with the row reversed and the indexes mapped back.',
                'Do not pop equals if the contract is strictly greater. Change < to <= only when the contract says greater-or-equal.',
            ],
            'quiz' => [
                $q('What does the stack hold?', 'Indexes still waiting for a greater item', 'The finished answers', 'Only the maximum', 'Every index forever'),
                $q('When do you pop the top?', 'The new value is strictly greater than the top value', 'The new value is smaller', 'The stack has three items', 'The index is even'),
                $q('What is the answer for the last item if nothing follows it?', 'The sentinel, because nothing to the right is greater', 'The item itself', 'The first item', 'The length'),
            ],
            'practice' => 'Compute the next greater values of [3, 8, 4, 5] by hand. 8 should answer 3, and 5 should answer 4.',
        ]),
        $L([
            'section' => '4. Recursion and search',
            'title' => 'Search for a word by sliding it',
            'keywords' => 'naive string search, pattern, shift by one',
            'lead' => 'The simplest text search tries the word at index 0, then index 1, then the next index, until the word falls off the end. Each try compares characters until one differs or the word is finished. It is easy to get right, and it is the baseline faster searches have to beat.',
            'heading' => 'A window the size of the word',
            'para' => 'In the text ababc, the word abc fails at index 0 on the third character, fails at index 1 immediately, and matches at index 2. The last legal start is len(text) - len(word). Starting any later would read past the text. If the word is empty, decide the answer on purpose: this lesson says an empty word is found at index 0, because every text starts with nothing.',
            'bullets' => [
                'The number of starts is len(text) - len(word) + 1, when the word is no longer than the text.',
                'A mismatch moves the window by one. You do not skip ahead unless you have a stronger rule, which this lesson does not.',
                'Return the first index, not every index, unless the contract asks for all of them.',
                'A missing word returns a sentinel. -1 is fine when indexes are never negative.',
            ],
            'rows' => [
                ['Window', 'text[i:i + len(word)]', 'One candidate alignment.'],
                ['Last start', 'len(text) - len(word)', 'Inclusive.'],
                ['Mismatch', 'Shift by one', 'The naive rule.'],
                ['Hit', 'The start index', 'Stop if you only need the first.'],
                ['Miss', '-1', 'The word never aligned.'],
                ['Empty word', 'Index 0 in this lesson', 'Say so. Other contracts disagree.'],
            ],
            'e1' => ['Example 1 — abc starts at index 2', 'The first two windows fail. The third equals the word.', <<<'PY'
def find_word(text, word):
    if word == '':
        return 0
    last = len(text) - len(word)
    for i in range(last + 1):
        if text[i:i + len(word)] == word:
            return i
    return -1

print(find_word('ababc', 'abc'))
print(find_word('ababc', 'zzz'))
PY],
            'e2' => ['Example 2 — a word longer than the text', 'The range of starts is empty. The answer is -1, and you never slice.', <<<'PY'
def find_word(text, word):
    if word == '':
        return 0
    last = len(text) - len(word)
    for i in range(last + 1):
        if text[i:i + len(word)] == word:
            return i
    return -1

print(find_word('ab', 'abcd'))
PY],
            'micro' => [
                'Slicing builds a new string per window. Comparing characters yourself avoids that. The answer is the same.',
                'Overlapping matches exist: aaa contains aa at index 0 and index 1. Shifting by one is what finds the second. Shifting by the word length would miss it.',
                'This is the algorithm to trust first. A failure-function search skips shifts, and it is easier to write wrong. Learn the slide before you learn the skip.',
            ],
            'quiz' => [
                $q('What is the last legal start of a word of length 3 in a text of length 5?', '2', '5', '3', '0 only'),
                $q('How far does the naive algorithm move after a mismatch?', 'One character', 'The length of the word, always', 'To the end', 'It stops'),
                $q('What does this lesson return for an empty word?', '0', '-1', 'The length of the text', 'None of the starts'),
            ],
            'practice' => 'Find every start of aa inside aaa, not only the first. Write the indexes.',
        ]),
        $L([
            'section' => '1. The cost of a step',
            'title' => 'A list that doubles when it fills',
            'keywords' => 'dynamic array, amortized append, capacity',
            'lead' => 'A Python list lets you append. Underneath, the list has a capacity that can be larger than the number of items. When the capacity is full, the items move into a new row with more room. One append is then expensive. Most appends are not. Averaged over a long run of appends, each one is cheap.',
            'heading' => 'The rare resize pays for the quiet appends',
            'para' => 'Start with capacity 1. The first append fits. The second finds the row full, moves 1 item, and doubles the capacity to 2. Later resizes move 2 items, then 4. After six appends the capacity is 8 and the total moves are 1 + 2 + 4 = 7. Six appends caused seven moves, about one move per append, even though one of those appends moved four items by itself. That average is the amortized cost.',
            'bullets' => [
                'Size is how many items you have stored. Capacity is how many you could store before the next resize.',
                'Doubling, not adding one slot, is the trick. Adding one slot would move the whole row on every append.',
                'A single append can take time proportional to the current size. You cannot promise every append is instant. You can promise the average.',
                'The moved items are the cost you are counting. The new item itself is one more store.',
            ],
            'rows' => [
                ['size', 'Items stored', 'Grows by 1 each append.'],
                ['capacity', 'Room before a move', 'Doubles when size would pass it.'],
                ['Resize', 'Move the old items', 'Happens only when the row is full.'],
                ['Doubling', 'New capacity is twice the old', 'The moves form the sum 1 + 2 + 4 + ...'],
                ['Amortized', 'Total moves divided by appends', 'About a small constant.'],
                ['One unlucky append', 'Moves everything', 'Still fair across the whole loop.'],
            ],
            'e1' => ['Example 1 — six appends, seven moves', 'The resizes happen when size is 1, 2, and 4. The final capacity is 8.', <<<'PY'
capacity = 1
size = 0
moves = 0
for _ in range(6):
    if size == capacity:
        moves += size
        capacity *= 2
    size += 1
print('capacity', capacity, 'moves', moves, 'size', size)
PY],
            'e2' => ['Example 2 — growing by one slot moves much more', 'Each append from the second onward moves every item already stored. Fifteen moves for six appends, and it gets worse faster than doubling.', <<<'PY'
capacity = 1
size = 0
moves = 0
for _ in range(6):
    if size == capacity:
        moves += size
        capacity += 1
    size += 1
print('capacity', capacity, 'moves', moves)
PY],
            'micro' => [
                'Python’s real list grows by a factor, not always exactly 2. The lesson is the factor, not the exact number CPython chose this year.',
                'Inserting at the front of a list is a different cost. Doubling does not make index 0 inserts cheap. Those still shift the tail.',
                'Amortized does not mean each call has the same cost. It means the expensive calls are rare enough that the total stays linear.',
            ],
            'quiz' => [
                $q('What is capacity?', 'How many items fit before the next move', 'How many items are stored', 'The index of the last item', 'Always equal to size'),
                $q('Why double instead of adding one slot?', 'The total moves stay proportional to the number of appends', 'Doubling uses no memory', 'Adding one slot is impossible', 'The first append fails otherwise'),
                $q('Does every append cost the same?', 'No. A resize moves many items, and the average across appends stays small', 'Yes, always one step', 'Yes, always n steps', 'Only the first append costs anything'),
            ],
            'practice' => 'Continue example 1 to 8 appends. Predict the capacity and the total moves before you change the range.',
        ]),
        $L([
            'section' => '4. Recursion and search',
            'title' => 'Order matters, or it does not',
            'keywords' => 'permutations, combinations, factorial',
            'lead' => 'A permutation is an ordering: aisle then window is different from window then aisle. A combination is a group: those two seats are the same group either way. The counts are different formulas, and mixing them up is how a small quiz becomes a wrong probability.',
            'heading' => 'Divide the factorials',
            'para' => 'Five people, and you line up two of them. There are 5 choices for the first spot and 4 for the second, which is 20. That is 5! / (5 - 2)!. If the two are a committee and the chairs are identical, you have double-counted every pair, so you divide by 2!, and the count is 10. The extra division is the whole difference between order and group.',
            'bullets' => [
                'n! is the product of the integers from 1 through n. 0! is 1, so the formula still works when you take every item.',
                'P(n, k) = n! / (n - k)! counts orderings of k items out of n.',
                'C(n, k) = n! / (k! (n - k)!) counts groups. It divides by the orderings of the chosen items.',
                'C(n, k) equals C(n, n - k). Choosing who is in the group is the same count as choosing who is out.',
            ],
            'rows' => [
                ['Permutation', 'Order counts', '20 ways to line up 2 of 5.'],
                ['Combination', 'Order does not count', '10 pairs out of 5.'],
                ['n!', 'Product 1 through n', '0! is 1.'],
                ['P formula', 'n! / (n - k)!', 'No extra k! in the denominator.'],
                ['C formula', 'P divided by k!', 'That removes the internal order.'],
                ['k > n', 'Zero ways', 'You cannot choose more items than exist.'],
            ],
            'e1' => ['Example 1 — 20 orderings and 10 groups', 'Five items, taken two at a time. The prints are P and C.', <<<'PY'
def fact(n):
    out = 1
    for i in range(2, n + 1):
        out *= i
    return out

def permutations(n, k):
    if k > n or k < 0:
        return 0
    return fact(n) // fact(n - k)

def combinations(n, k):
    if k > n or k < 0:
        return 0
    return fact(n) // (fact(k) * fact(n - k))

print(permutations(5, 2))
print(combinations(5, 2))
PY],
            'e2' => ['Example 2 — choosing 2 is the same count as leaving 2 out of 5', 'C(5, 2) and C(5, 3) match, because leaving 2 out is choosing the other 3.', <<<'PY'
def fact(n):
    out = 1
    for i in range(2, n + 1):
        out *= i
    return out

def combinations(n, k):
    return fact(n) // (fact(k) * fact(n - k))

print(combinations(5, 2), combinations(5, 3))
PY],
            'micro' => [
                'Integer // is required. A single / produces a float that may not be exact for a large factorial.',
                'Generating every ordering is a different task from counting them. Counts do not list the arrangements.',
                'If some of the n items are identical, these formulas over-count. They assume the n items are distinct.',
            ],
            'quiz' => [
                $q('What is the difference between a permutation and a combination?', 'A permutation counts order and a combination does not', 'They are the same count', 'A combination is always larger', 'A permutation ignores k'),
                $q('What is P(5, 2)?', '20', '10', '25', '5'),
                $q('Why is 0! defined as 1?', 'So choosing all n items still divides cleanly', 'Because an empty product is 0', 'Because factorial rejects 0', 'So combinations exceed permutations'),
            ],
            'practice' => 'Compute C(6, 1) and C(6, 0) from the formula. Both should feel obvious once you say them in words.',
        ]),
        $L([
            'section' => '7. Graphs',
            'title' => 'Two colours, and a graph that refuses',
            'keywords' => 'bipartite graph, two coloring, odd cycle',
            'lead' => 'A graph is bipartite when you can split the nodes into two groups so every edge runs between the groups, never inside one. Colouring the nodes with two colours is the same test. If a neighbour already has your colour, the graph has an odd cycle and the split is impossible.',
            'heading' => 'Give the neighbour the other colour',
            'para' => 'Start at a node and paint it colour 0. Every neighbour gets colour 1, and their neighbours get colour 0. A tree always succeeds. A triangle fails: the third edge connects two nodes that both received the same colour. The failure is not “the graph has a cycle.” Even cycles are fine. The failure is an odd cycle.',
            'bullets' => [
                'A disconnected graph needs a start in each component. One walk from a single node never sees the other component.',
                'Paint a node when you first discover it. A later edge that finds the same colour is the conflict.',
                'The two colours are the two groups. Edges inside a colour are exactly the edges that break the split.',
                'You can use a stack or a queue. The order changes nothing about the yes/no answer.',
            ],
            'rows' => [
                ['Colour 0 and 1', 'The two groups', '1 - colour flips the group.'],
                ['Uncoloured neighbour', 'Paint and continue', 'Discovery.'],
                ['Same colour', 'Not bipartite', 'An odd cycle exists.'],
                ['Other colour', 'Consistent', 'Keep walking.'],
                ['Even cycle', 'Still bipartite', 'A square colours cleanly.'],
                ['Component', 'Start again', 'An unseen node may be a new piece.'],
            ],
            'e1' => ['Example 1 — a square accepts two colours', 'a—b—c—d—a. Opposite corners share a colour. No edge connects them.', <<<'PY'
graph = {
    'a': ['b', 'd'],
    'b': ['a', 'c'],
    'c': ['b', 'd'],
    'd': ['c', 'a'],
}
color = {'a': 0}
stack = ['a']
ok = True
while stack and ok:
    node = stack.pop()
    for nxt in graph[node]:
        if nxt not in color:
            color[nxt] = 1 - color[node]
            stack.append(nxt)
        elif color[nxt] == color[node]:
            ok = False
print(ok, color)
PY],
            'e2' => ['Example 2 — a triangle refuses', 'c is a neighbour of a and of b. Those two already have different colours, so c cannot satisfy both.', <<<'PY'
graph = {'a': ['b', 'c'], 'b': ['a', 'c'], 'c': ['a', 'b']}
color = {'a': 0}
stack = ['a']
ok = True
while stack and ok:
    node = stack.pop()
    for nxt in graph[node]:
        if nxt not in color:
            color[nxt] = 1 - color[node]
            stack.append(nxt)
        elif color[nxt] == color[node]:
            ok = False
print(ok)
PY],
            'micro' => [
                'A single edge is bipartite. A single node with no edge is bipartite. Do not treat “has a cycle” as the test.',
                'If the conflict happens, you can stop. More walking will not repair that edge.',
                'The groups do not have to be the same size. Bipartite does not mean balanced.',
            ],
            'quiz' => [
                $q('What makes a graph fail the two-colour test?', 'An edge joins two nodes of the same colour', 'Any cycle', 'A node with no edge', 'Two components'),
                $q('Is a square bipartite?', 'Yes', 'No, because it has a cycle', 'Only if all sides have the same cost', 'Only if it has a diagonal'),
                $q('Why start once per component?', 'One walk never reaches a disconnected node', 'Colours must all be 0', 'Stacks hold one item', 'Edges are sorted'),
            ],
            'practice' => 'Draw a pentagon of five nodes. Predict the colouring result, then say whether the cycle length is odd or even.',
        ]),
        $L([
            'section' => '8. Patterns to master',
            'title' => 'Remember the wish, or fill the table',
            'keywords' => 'memoization, tabulation, fibonacci, top down, bottom up',
            'lead' => 'The dynamic-programming chapter stored answers so a recursive wish would not recompute them. There are two directions. Top-down starts at the question and remembers each wish the first time it is answered. Bottom-up fills a table from the tiny cases up to the question, with no recursion at all.',
            'heading' => 'Same answers, two orders',
            'para' => 'Fibonacci of 8 is 21 either way. The memo version asks for 8, which asks for 7 and 6, and each smaller wish is stored in a dictionary so the second time is a lookup. The table version writes index 0 and 1, then each next cell is the sum of the two before it, up through index 8. The table never asks for a cell that is not already filled. The memo never fills a cell nobody asked for. On Fibonacci they do the same work. On a sparse problem, memo skips cells the table would still fill.',
            'bullets' => [
                'The base cases must be in the memo or the table before you combine anything. fib(0) and fib(1) are the bases here.',
                'Top-down without the memo is the exponential tree. The dictionary is the entire improvement.',
                'Bottom-up must order the cells so dependencies are ready. For Fibonacci, increasing index is that order.',
                'The table uses a list of every index up to n. The memo uses a slot per call. For fib they match. For a problem with holes, they do not.',
            ],
            'rows' => [
                ['Wish', 'fib(n) calls smaller fibs', 'Top-down.'],
                ['Memo', 'Store the wish the first time', 'The second call returns the store.'],
                ['Table', 'A list indexed by n', 'Bottom-up.'],
                ['Base', '0 and 1', 'Both styles need them.'],
                ['Order', 'Small before large', 'The table’s responsibility.'],
                ['Sparse', 'Memo can skip', 'The table fills the middle anyway.'],
            ],
            'e1' => ['Example 1 — both styles print 21', 'The memo dictionary and the table end on the same Fibonacci number.', <<<'PY'
memo = {0: 0, 1: 1}

def fib(n):
    if n not in memo:
        memo[n] = fib(n - 1) + fib(n - 2)
    return memo[n]

table = [0, 1]
for i in range(2, 9):
    table.append(table[-1] + table[-2])

print(fib(8))
print(table[8])
PY],
            'e2' => ['Example 2 — forget the memo and the calls explode', 'A counter shows how many times the bare function is entered for fib(8). The memo version enters each n once.', <<<'PY'
calls = {'bare': 0}

def bare(n):
    calls['bare'] += 1
    if n < 2:
        return n
    return bare(n - 1) + bare(n - 2)

print(bare(8), 'entries', calls['bare'])
PY],
            'micro' => [
                'The memo must be outside the function or passed along. A dictionary created inside the function is empty on every call and remembers nothing.',
                'table[8] is the ninth cell because index 0 exists. Off-by-one here returns fib(7) and looks almost right.',
                'Recursion depth is a real limit for a huge n in the top-down style. The table loop does not add a call frame per index.',
            ],
            'quiz' => [
                $q('What does the memo stop?', 'Computing the same fib(n) more than once', 'All addition', 'The base cases', 'The use of a list'),
                $q('What is fib(8)?', '21', '13', '8', '34'),
                $q('When can a memo do less work than a full table?', 'Some cells are never asked for', 'The problem is Fibonacci of a small n', 'The table is unsorted', 'Base cases are skipped'),
            ],
            'practice' => 'Fill a table for fib(6) by hand. Then say which cells a memo would store if you only asked for fib(6).',
        ]),
    ];
}
