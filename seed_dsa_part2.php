<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/track_seed.php';

function dsa_chapters_more(): array
{
    $f = 'dsa_notes.py';
    $L = static function (array $c) use ($f): array {
        return track_lesson('python', $f, $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };
    return [
        $L([
            'section' => '2. Linear structures',
            'title' => 'Stack: the undo pile',
            'keywords' => 'stack, LIFO, push, pop, call stack, parentheses',
            'lead' => 'A stack is a pile. You add on top and take from the top. The last item in is the first item out. The undo button, the browser back button, and the way functions call other functions are all stacks.',
            'heading' => 'Only the top is allowed to move',
            'para' => 'In Python a list is a fine stack if you only append and pop() from the end. The moment you pop(0) it is no longer a stack and it is no longer cheap. Matching brackets is the classic job: an opening bracket pushes, a closing bracket must match the top.',
            'bullets' => [
                'push is append. pop is pop() with no index. peek is items[-1] without removing.',
                'Popping an empty stack should be a clear answer, not a surprise IndexError you did not plan.',
                'The call stack is this same pile. A function that calls itself keeps frames until the base case returns.',
                'A stack reverses order. Push 1, 2, 3 and pop everything: you get 3, 2, 1.',
            ],
            'rows' => [
                ['LIFO', 'Last in, first out', 'The top is the newest item.'],
                ['push', 'append', 'O(1) at the end of a list.'],
                ['pop', 'Remove the top', 'pop() with no argument. pop(0) is the wrong end.'],
                ['peek', 'Read the top', 'Do not remove it. Empty has no top.'],
                ['Underflow', 'Pop when empty', 'Decide: raise, or return None.'],
                ['Match', 'Brackets', 'Close must equal the top, then pop. Leftovers at the end mean unclosed.'],
            ],
            'e1' => ['Example 1 — the pile reverses', 'Push three notes, then pop until empty.', <<<'PY'
stack = []
for note in ['milk', 'bread', 'tea']:
    stack.append(note)
print('top', stack[-1])
while stack:
    print('pop', stack.pop())
PY],
            'e2' => ['Example 2 — brackets that match', 'A closer must be the partner of the current top. A leftover opener is a failure.', <<<'PY'
pairs = {')': '(', ']': '[', '}': '{'}

def balanced(text):
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
print(balanced('(a[b)]'))
print(balanced('(('))
PY],
            'micro' => [
                'The dict of closers maps a closer to the opener it needs. The stack stores openers, not closers.',
                'Characters that are not brackets are ignored here. Say that in the rule so a letter does not look like a bug.',
                'peek on empty is the same bug as items[-1] on empty. Check the pile first.',
            ],
            'quiz' => [
                $q('Which end of a list is the top of a stack?', 'The end you append and pop()', 'Index 0', 'The middle', 'Whatever len returns'),
                $q('Push 1 then 2 then 3. What is the first pop?', '3', '1', '2', '0'),
                $q('When is a bracket string balanced at the end?', 'The stack is empty and every closer matched', 'The stack still holds the openers', 'There was at least one closer', 'The string length is even, nothing else matters'),
            ],
            'practice' => 'Trace ([]) by hand: write the stack after each bracket. Then run the example and check the three prints.',
        ]),
        $L([
            'section' => '2. Linear structures',
            'title' => 'Queue and deque: the fair line',
            'keywords' => 'queue, FIFO, deque, BFS preview',
            'lead' => 'A queue is a line of people. The first person in is the first person served. Using a list and pop(0) works but slides everyone. collections.deque is built so both ends are cheap.',
            'heading' => 'Front leaves, back arrives',
            'para' => 'append adds at the right. popleft removes from the left. That pair is a queue. appendleft and pop are the other direction. Breadth-first search, in a later chapter, is “a queue of places still to visit.”',
            'bullets' => [
                'FIFO means first in, first out. A stack is the opposite.',
                'from collections import deque. Then dq = deque().',
                'popleft on an empty deque raises IndexError. Check it, or catch it on purpose.',
                'A list used as a queue with pop(0) is correct and slow. Correct is not the same as cheap.',
            ],
            'rows' => [
                ['FIFO', 'Fair line', 'Oldest item leaves first.'],
                ['append', 'Join at the back', 'Right end of a deque.'],
                ['popleft', 'Serve the front', 'Left end. O(1) on a deque.'],
                ['list pop(0)', 'Also the front', 'O(n) because the row slides.'],
                ['maxlen', 'A fixed window', 'deque(maxlen=3) forgets the oldest when it is full.'],
                ['Empty', 'Nobody waiting', 'Do not popleft. The search chapters will stop when the queue is empty.'],
            ],
            'e1' => ['Example 1 — serve in arrival order', 'milk arrived first, so milk is served first. The stack would have served tea.', <<<'PY'
from collections import deque
line = deque()
for note in ['milk', 'bread', 'tea']:
    line.append(note)
while line:
    print('serve', line.popleft())
PY],
            'e2' => ['Example 2 — a window that forgets', 'maxlen 3 keeps only the latest three arrivals.', <<<'PY'
from collections import deque
recent = deque(maxlen=3)
for n in [1, 2, 3, 4, 5]:
    recent.append(n)
    print(list(recent))
PY],
            'micro' => [
                'list(recent) is a copy for printing. The deque itself is the structure.',
                'appendleft pushes in front of the line. That is a stack-like end, not the fair queue.',
                'Import deque at the top of the example, not inside the loop.',
            ],
            'quiz' => [
                $q('A queue serves which item first?', 'The one that arrived first', 'The one that arrived last', 'The middle', 'A random one'),
                $q('Why prefer deque.popleft over list.pop(0) for a long line?', 'popleft is O(1); pop(0) slides the list', 'pop(0) is illegal', 'deque cannot hold strings', 'They cost the same'),
                $q('What does deque(maxlen=3) do on the fourth append?', 'Drops the oldest item', 'Raises IndexError', 'Grows forever', 'Sorts the items'),
            ],
            'practice' => 'Enqueue three titles and serve them. Write the serve order before you run it.',
        ]),
        $L([
            'section' => '2. Linear structures',
            'title' => 'Linked list: boxes with arrows',
            'keywords' => 'linked list, node, next pointer, insert head',
            'lead' => 'A linked list is a chain of boxes. Each box holds a value and an arrow to the next box. There is no index math. You only move by following arrows, so reaching the 10th box costs 10 steps.',
            'heading' => 'The head is the only door you keep',
            'para' => 'If you lose the first box, you lose the chain. Inserting at the head is cheap: the new box points at the old head, and head becomes the new box. That reverses the order you inserted, unless you insert at the tail on purpose.',
            'bullets' => [
                'A node is a small object: value, and next. next is None at the end, not a fake zero.',
                'Walking the chain is a loop: while node is not None. Then node = node.next.',
                'There is no items[i] that jumps. If you need jumps, a Python list is the better tool.',
                'Two names can point at the same box. Changing the box is seen through both names.',
            ],
            'rows' => [
                ['node', 'One box', 'value plus next.'],
                ['head', 'First box', 'The variable you must not lose.'],
                ['None', 'End of the chain', 'The loop stops. It is not an error by itself.'],
                ['Insert head', 'New box points at old head', 'O(1). Order reverses if you always insert here.'],
                ['Walk', 'Follow next', 'O(n) to reach the end.'],
                ['Versus list', 'No random index', 'Use a list when you need items[i] often.'],
            ],
            'e1' => ['Example 1 — build at the head, then walk', 'Inserted a, then b, then c. Walking starts at c because each insert became the new head.', <<<'PY'
class Node:
    def __init__(self, value, nxt=None):
        self.value = value
        self.next = nxt

head = None
for value in ['a', 'b', 'c']:
    head = Node(value, head)

node = head
while node is not None:
    print(node.value)
    node = node.next
PY],
            'e2' => ['Example 2 — do not call the arrow next', 'next is a real Python function. Naming the attribute next is legal and confusing. This example uses nxt in the constructor and .next as the arrow, and shows None stop.', <<<'PY'
class Node:
    def __init__(self, value, nxt=None):
        self.value = value
        self.next = nxt

tail = Node('end')
head = Node('start', tail)
print(head.value, head.next.value)
print('after end', tail.next)
PY],
            'micro' => [
                'The parameter is named nxt so it does not shadow the built-in next. The attribute can still be called next.',
                'Losing head and keeping only an inner box splits the chain in your hands. You did not delete the front; you forgot its name.',
                'A doubly linked list also stores prev. Python’s deque is that idea, already built.',
            ],
            'quiz' => [
                $q('How do you reach the third box?', 'Follow next, then next', 'Use index 3 in O(1)', 'Call len', 'Sort the nodes'),
                $q('Inserting at the head makes the new node point at what?', 'The old head', 'None always', 'The tail only', 'The integer 0'),
                $q('What should the last node’s arrow be?', 'None', '0', 'The head, always', 'The string end'),
            ],
            'practice' => 'Insert the words milk, bread, tea at the head. Predict the walk order, then run it.',
        ]),
        $L([
            'section' => '2. Linear structures',
            'title' => 'Two pointers and a sliding window',
            'keywords' => 'two pointers, sliding window, sorted pair sum',
            'lead' => 'Two pointers means two indexes moving with a rule, instead of a loop inside a loop. A window is a slice that grows on the right and shrinks on the left. Both turn an O(n²) habit into one pass when the data is arranged for it.',
            'heading' => 'Move the side that fixes the mistake',
            'para' => 'On a sorted list, a pair that should sum to a target: if the sum is too small, move the left index right; if it is too big, move the right index left. The window version keeps a running sum and moves left forward whenever the window is too heavy. You never move left backward.',
            'bullets' => [
                'The list must be sorted for the pair-sum rule. Unsorted data needs a different tool, often a set.',
                'Stop when the pointers cross. left > right means no pair.',
                'A window’s sum is updated by adding the new right and subtracting the old left. Do not rescan the window.',
                'Off-by-one: the window from left to right includes both ends when you mean a closed range.',
            ],
            'rows' => [
                ['left, right', 'Two indexes', 'They move toward each other or as a window.'],
                ['Sorted pair', 'Sum too small', 'Increase left. The small value cannot work.'],
                ['Sum too big', 'Decrease right', 'The large value cannot work.'],
                ['Window', 'A moving slice', 'Add on the right, remove on the left.'],
                ['One pass', 'Each index moves at most n times', 'That is why it is O(n), not O(n²).'],
                ['Unsorted', 'This rule is false', 'Sort a copy first, or use a set of values already seen.'],
            ],
            'e1' => ['Example 1 — two indexes on a sorted row', 'Find two numbers that add to 9.', <<<'PY'
def pair_sum(nums, target):
    left, right = 0, len(nums) - 1
    while left < right:
        total = nums[left] + nums[right]
        if total == target:
            return nums[left], nums[right]
        if total < target:
            left += 1
        else:
            right -= 1
    return None

print(pair_sum([1, 2, 4, 7, 11], 9))
print(pair_sum([1, 2, 3], 9))
PY],
            'e2' => ['Example 2 — smallest window that covers the need', 'Here the need is a sum of at least 8. The window grows right, then shrinks left.', <<<'PY'
def min_window_len(nums, need):
    left = 0
    total = 0
    best = None
    for right, value in enumerate(nums):
        total += value
        while total >= need and left <= right:
            length = right - left + 1
            best = length if best is None else min(best, length)
            total -= nums[left]
            left += 1
    return best

print(min_window_len([1, 2, 3, 4], 8))
print(min_window_len([1, 1, 1], 8))
PY],
            'micro' => [
                'right - left + 1 is the count of items in a closed window. Forgetting +1 is the off-by-one.',
                'The inner while can run many times, but left only moves forward, so the total work stays linear.',
                'If numbers can be negative, a shrinking-sum window is the wrong picture. The “too big, shrink” story assumes adding a number does not decrease the sum.',
            ],
            'quiz' => [
                $q('On a sorted list, the pair sum is too small. Which pointer moves?', 'The left one, to the right', 'The right one, to the left', 'Both jump to 0', 'Neither; start a nested loop'),
                $q('How many items are in indexes 2 through 4 inclusive?', '3', '2', '4', '1'),
                $q('Why is the sorted pair search not always safe on an unsorted list?', 'A bigger index is not a bigger value', 'Python cannot add', 'Pointers must be negative', 'Sorted lists are slower to read'),
            ],
            'practice' => 'On [1, 3, 4, 8] find a pair that sums to 12. Write each left and right you try.',
        ]),
        $L([
            'section' => '3. Lookup',
            'title' => 'Dictionary: keys, and what collides',
            'keywords' => 'python dict, hash map, key, collision, membership',
            'lead' => 'A dictionary stores a value under a key, like a phone book. Asking for a key is usually one step, not a walk through every name. The small points are missing keys, overwritten keys, and keys that are allowed to be hashed.',
            'heading' => 'The key is the question, the value is the answer',
            'para' => 'phone[name] raises KeyError when the name is absent. phone.get(name) returns None, or a default you pass. Assigning the same key again replaces the value. It does not keep both. Only immutable keys are legal: strings, numbers, tuples of immutables. A list cannot be a key.',
            'bullets' => [
                'in checks keys, not values. name in phone is about the key.',
                'A missing key is normal data. Pick KeyError or get on purpose.',
                'Iteration order follows insertion order in modern Python. Do not depend on that for a sorted report; sort the keys.',
                'A collision is two keys that want the same slot. Python handles it. You still should not build a million keys you will never read.',
            ],
            'rows' => [
                ['key', 'The lookup', 'Must be hashable. Strings and ints are. Lists are not.'],
                ['value', 'What you stored', 'Any object. Replacing the key drops the old value.'],
                ['KeyError', 'Absent key with []', 'Use get when absence is ordinary.'],
                ['get default', 'A stand-in', 'counts.get(word, 0) + 1 is the counting pattern.'],
                ['in', 'Key check', 'O(1) average. Not a scan of values.'],
                ['tuple key', 'A pair', '(row, col) can be a key. [row, col] cannot.'],
            ],
            'e1' => ['Example 1 — count words', 'Missing words start at 0. The same word adds.', <<<'PY'
counts = {}
for word in ['milk', 'bread', 'milk']:
    counts[word] = counts.get(word, 0) + 1
print(counts)
print('tea', counts.get('tea', 0))
PY],
            'e2' => ['Example 2 — a list is not a key', 'The error is TypeError, unhashable. A tuple of the same numbers works.', <<<'PY'
try:
    grid = {}
    grid[[0, 1]] = 'shop'
    print(grid)
except TypeError as err:
    print('TypeError:', err.__class__.__name__)

grid = {}
grid[(0, 1)] = 'shop'
print(grid[(0, 1)])
PY],
            'micro' => [
                'counts[word] = counts[word] + 1 crashes the first time you see the word. get is the fix, or defaultdict(int).',
                'Two equal strings are the same key. Capital Milk and milk are different keys.',
                'Do not use a dict when you needed a list of order. Dict is for “find this key”, not for “the third item”.'
            ],
            'quiz' => [
                $q('What does counts.get("tea", 0) return when tea was never stored?', '0', 'KeyError', 'None always, the 0 is ignored', 'An empty list'),
                $q('Which can be a dictionary key?', 'The tuple (0, 1)', 'The list [0, 1]', 'A set of numbers', 'Another dict'),
                $q('Assigning the same key a second time does what?', 'Replaces the value', 'Keeps both values', 'Appends to a list automatically', 'Sorts the dict'),
            ],
            'practice' => 'Count the letters in "book". Predict the dict, especially the two o’s, then run it.',
        ]),
        $L([
            'section' => '3. Lookup',
            'title' => 'Set: membership in one step',
            'keywords' => 'python set, membership, duplicates, seen',
            'lead' => 'A set is a dictionary with only keys. It answers “have I seen this?” in one step and refuses duplicates. It does not remember position or a count.',
            'heading' => 'Seen, or not seen',
            'para' => 'Add with add. Test with in. A list’s in may scan every item; a set’s in usually does not. Building a set from a list drops duplicates and does not keep order as a promise you should teach as sorted. Modern sets keep insertion of unique items, but the point of a set is membership, not ranking.',
            'bullets' => [
                'set([1, 1, 2]) has two items. The second 1 is ignored.',
                'Items must be hashable, same rule as dict keys.',
                'The “seen” set in graph search is how you avoid walking the same corner forever.',
                'Subtracting sets is a real operation: a - b is items in a that are not in b.',
            ],
            'rows' => [
                ['add', 'Put a value in', 'A duplicate add changes nothing.'],
                ['in', 'Membership', 'Average O(1).'],
                ['list in', 'Also spelled in', 'Average O(n). The spelling hides the cost.'],
                ['len', 'How many unique', 'Not how many you added.'],
                ['remove', 'Delete, or KeyError', 'discard deletes and stays quiet if it was absent.'],
                ['seen', 'Visited set', 'Check before you append to the queue or the recursion.'],
            ],
            'e1' => ['Example 1 — duplicates disappear', 'Three adds, two members.', <<<'PY'
seen = set()
for item in ['milk', 'milk', 'bread']:
    seen.add(item)
print(sorted(seen))
print('milk' in seen)
print('tea' in seen)
PY],
            'e2' => ['Example 2 — the quiet discard', 'remove raises. discard does not. Both are useful if you meant them.', <<<'PY'
seen = {'milk'}
seen.discard('tea')
print(sorted(seen))
try:
    seen.remove('tea')
except KeyError:
    print('KeyError on remove')
PY],
            'micro' => [
                'sorted(seen) is for a stable print. The set is not a sorted structure.',
                '{} is an empty dict, not an empty set. Empty set is set().',
                'A set of lists is illegal. A set of tuples is legal.',
            ],
            'quiz' => [
                $q('How many items are in set([1, 1, 2])?', '2', '3', '1', '0'),
                $q('Which literal is an empty set?', 'set()', '{}', '[]', 'set([]) is illegal'),
                $q('Why do graph searches keep a seen set?', 'So a node is not walked forever', 'To sort the nodes', 'To store the path distances', 'Because lists cannot hold strings'),
            ],
            'practice' => 'Turn the list ["a", "b", "a"] into a set and print whether "a" is in it and what len is.',
        ]),
        $L([
            'section' => '3. Lookup',
            'title' => 'Hashing a string by hand',
            'keywords' => 'hash function, bucket, collision, modulo',
            'lead' => 'A hash function turns a key into a slot number. A good one spreads keys around. A bad one dumps them in one slot, and the “one step” lookup becomes a walk. You do not write Python’s hash. You should know the picture, because that is why dicts demand immutable keys.',
            'heading' => 'A number, then a remainder',
            'para' => 'Sum the character codes, then take remainder modulo the number of buckets. Two different strings can land in the same bucket. That is a collision. The bucket keeps a small list. Lookup hashes, then scans only that list. If every key collides, the small list is the whole data and you are back to O(n).',
            'bullets' => [
                'ord(ch) is the character code. It is stable for this lesson.',
                'The modulo must be the bucket count. Forgetting it produces indexes past the end.',
                'If the key changes after it is stored, the slot number would change and you would look in the wrong place. That is why keys must not mutate. Lists mutate, so they are refused.',
                'A tiny bucket count is a teaching tool. Real dicts resize.',
            ],
            'rows' => [
                ['hash', 'Key to a number', 'Same key must always give the same number.'],
                ['bucket', 'number % count', 'The slot. Collisions share a slot.'],
                ['chain', 'A list in the slot', 'Scan the chain, not every key in the world.'],
                ['immutable', 'Key will not change', 'Otherwise the slot and the key divorce.'],
                ['bad hash', 'Everything to 0', 'One chain of length n. Lookup becomes linear.'],
                ['resize', 'More buckets later', 'Python does this. The lesson uses a fixed 4.'],
            ],
            'e1' => ['Example 1 — four buckets and a collision', 'Different words can share a remainder. Both still find their value.', <<<'PY'
def slot(key):
    total = 0
    for ch in key:
        total += ord(ch)
    return total % 4

buckets = [[], [], [], []]

def put(key, value):
    chain = buckets[slot(key)]
    for i, (k, _) in enumerate(chain):
        if k == key:
            chain[i] = (key, value)
            return
    chain.append((key, value))

def get(key):
    for k, value in buckets[slot(key)]:
        if k == key:
            return value
    return None

put('milk', 2)
put('bread', 1)
put('milk', 3)
print('slots', {k: slot(k) for k in ['milk', 'bread']})
print(get('milk'), get('tea'))
PY],
            'e2' => ['Example 2 — a terrible hash', 'Every key goes to bucket 0. Getting the last inserted key scans the whole chain.', <<<'PY'
keys = ['a', 'b', 'c', 'd']
chain = []
for key in keys:
    chain.append(key)
steps = 0
for key in chain:
    steps += 1
    if key == 'd':
        break
print('steps to find d', steps, 'of', len(chain))
PY],
            'micro' => [
                'Replacing a key updates the pair in the chain. A second append would leave two milks. The loop checks first.',
                'hash("milk") in Python is not this sum, and it is salted per process. Do not store Python’s hash() in a database and expect it tomorrow.',
                'The remainder of a negative number is defined in Python. Stick to positive sums here.',
            ],
            'quiz' => [
                $q('A collision means what?', 'Two keys chose the same slot', 'The dict is full and deletes a key', 'The key was a list', 'ord failed'),
                $q('Why can a list not be a dict key?', 'It can change, so its slot would lie', 'Lists are too short', 'Python has no lists', 'Lists are already hashed'),
                $q('If every key hashes to the same slot, lookup is which shape?', 'O(n) along that chain', 'O(1) still', 'O(log n) automatically', 'Impossible'),
            ],
            'practice' => 'With 4 buckets, compute the slot of "a" and "b" by hand using the sum of ord, then check with the function.',
        ]),
        $L([
            'section' => '4. Recursion and search',
            'title' => 'Recursion and the base case',
            'keywords' => 'recursion, base case, call stack, factorial',
            'lead' => 'A recursive function does a smaller copy of the same job, then combines. The base case is the copy so small you answer immediately. Without it, the calls never stop and Python raises RecursionError.',
            'heading' => 'Trust a smaller problem, and stop',
            'para' => 'Factorial of n is n times factorial of n - 1, and factorial of 0 is 1. The 1 is the base. Each call waits on the stack until the smaller one returns. Deep recursion uses stack space. A loop uses one frame. Prefer the loop when the picture is just a counter, and use recursion when the data is a tree.',
            'bullets' => [
                'Every recursive path must reach the base case. n - 1 does. n + 1 on a positive n does not.',
                'The work after the recursive call runs on the way back up. Prints before the call and after the call show the difference.',
                'Python’s default recursion limit is about 1000. A long chain will fail even if the algorithm is “correct”.',
                'Two recursive calls, like on a tree, are still fine when each call is on a smaller piece.',
            ],
            'rows' => [
                ['Base case', 'Stop', 'factorial(0) is 1. An empty tree is None.'],
                ['Recursive case', 'Smaller job', 'The argument must shrink.'],
                ['Frame', 'One call waiting', 'The pile is the call stack.'],
                ['Before the call', 'On the way down', 'You still have the local names.'],
                ['After the call', 'On the way up', 'The smaller answer is now in a variable.'],
                ['RecursionError', 'Too deep, or no base', 'It is not an infinite loop of the while kind; the stack ran out.'],
            ],
            'e1' => ['Example 1 — down, then up', 'The “down” prints happen before the smaller call returns. The “up” prints happen after.', <<<'PY'
def walk(n):
    if n == 0:
        print('base')
        return
    print('down', n)
    walk(n - 1)
    print('up', n)

walk(3)
PY],
            'e2' => ['Example 2 — factorial, recursive and loop', 'Same answers. The loop does not grow the stack with n.', <<<'PY'
def fact(n):
    if n == 0:
        return 1
    return n * fact(n - 1)

def fact_loop(n):
    total = 1
    for i in range(1, n + 1):
        total *= i
    return total

print(fact(5), fact_loop(5))
PY],
            'micro' => [
                'return n * fact(n - 1) does the multiply after the call. If you forget return, the function returns None and None times a number crashes.',
                'fact(-1) never hits 0 if you only subtract. Guard n < 0.',
                'A recursive function that slices a string makes a copy each call. The time can be worse than the depth suggests.',
            ],
            'quiz' => [
                $q('What is the base case of factorial in this lesson?', 'factorial(0) is 1', 'factorial(1) is 0', 'There is no stop', 'factorial(n) is n'),
                $q('What does Python raise when recursion never reaches a base?', 'RecursionError', 'IndexError always', 'KeyError', 'It loops quietly forever with no error'),
                $q('When is a loop the clearer tool?', 'When the job is a counter from 1 to n', 'When the data is a tree of unknown shape', 'Never', 'Only for strings'),
            ],
            'practice' => 'Write the down/up trace of walk(2) on paper before running walk(3).',
        ]),
        $L([
            'section' => '4. Recursion and search',
            'title' => 'Linear search, and stopping early',
            'keywords' => 'linear search, sentinel, early exit',
            'lead' => 'Linear search looks at items in order until it finds the target or runs out. It needs no sorting. It costs one step per item in the worst case, which is “not here.”',
            'heading' => 'Look, then decide whether to continue',
            'para' => 'Return the index when you find it, not True, if the caller must know where. Return -1 or None when it is missing, and use that same signal everywhere. Breaking early when you only need existence is correct. Scanning the rest “to be sure” is wasted work if values are unique and you already found one.',
            'bullets' => [
                'Worst case is the missing item, or the item in the last slot. Best case is index 0.',
                'A sorted list does not make linear search faster unless you also stop when the values pass the target.',
                'Searching values is not searching indexes. Decide which one you return.',
                'Two equals: say whether you want the first index or the last. The loop direction decides it.',
            ],
            'rows' => [
                ['O(n) worst', 'Might see everyone', 'Missing item.'],
                ['O(1) best', 'It was first', 'Do not quote only this.'],
                ['-1', 'Not found signal', 'Only if -1 cannot be a real index. It cannot, for this list.'],
                ['First match', 'Return immediately', 'Later duplicates are ignored.'],
                ['Sorted early stop', 'Passed the target', 'Only valid if the list is sorted ascending.'],
                ['in', 'The same scan', 'value in items is linear search.'],
            ],
            'e1' => ['Example 1 — first index or -1', '7 is at index 3. 4 is absent.', <<<'PY'
def find(nums, target):
    for i, value in enumerate(nums):
        if value == target:
            return i
    return -1

print(find([1, 3, 5, 7, 9], 7))
print(find([1, 3, 5, 7, 9], 4))
PY],
            'e2' => ['Example 2 — stop once sorted values pass the target', 'On a sorted list, 6 is not present and 7 is already bigger, so you stop.', <<<'PY'
def find_sorted(nums, target):
    for i, value in enumerate(nums):
        if value == target:
            return i
        if value > target:
            return -1
    return -1

print(find_sorted([1, 3, 5, 7, 9], 6))
print(find_sorted([1, 3, 5, 7, 9], 9))
PY],
            'micro' => [
                'enumerate gives index and value. A manual i = i + 1 is easier to forget.',
                'Do not use find_sorted on an unsorted list. A later small value would be skipped after an early big one.',
                'Returning the value instead of the index is a different function. Name it so the caller knows.',
            ],
            'quiz' => [
                $q('Linear search on an unsorted list, worst case, looks at how many items?', 'All of them', 'One', 'log n', 'None'),
                $q('Why must find_sorted refuse an unsorted list?', 'A larger value early would hide a later match', 'Unsorted lists have no indexes', 'enumerate fails', 'It always returns 0'),
                $q('What does this lesson return when the target is missing?', '-1', 'None and -1 mixed', 'The last item', 'True'),
            ],
            'practice' => 'Search for the first "tea" in ["milk", "tea", "tea"]. Which index do you expect, and which duplicate did you ignore?',
        ]),
        $L([
            'section' => '4. Recursion and search',
            'title' => 'Binary search, and the middle that must move',
            'keywords' => 'binary search, sorted, mid, log n',
            'lead' => 'Binary search only works on a sorted row. Each step compares the middle and throws away half. If the row is not sorted, throwing away half can throw away the answer.',
            'heading' => 'Keep a window, and shrink it',
            'para' => 'lo and hi are inclusive indexes. mid = (lo + hi) // 2. If the middle is too small, the answer is strictly to the right, so lo becomes mid + 1. If you set lo = mid, and mid is lo, the window never shrinks and the loop never ends. That off-by-one is the whole bug.',
            'bullets' => [
                'Inclusive hi means the loop continues while lo <= hi. An exclusive hi uses lo < hi and different updates.',
                'Pick one style and do not mix the updates from the other.',
                'In other languages lo + hi can overflow. lo + (hi - lo) // 2 is the same middle and safer. Python ints do not overflow, but the habit is good.',
                'The list must be sorted by the same order you compare. Sorting strings and comparing numbers is nonsense.',
            ],
            'rows' => [
                ['Sorted', 'Required', 'Ascending in this lesson.'],
                ['lo, hi', 'Inclusive window', 'Both are candidates.'],
                ['mid + 1', 'Throw mid away', 'It was too small, so it is not the answer.'],
                ['mid - 1', 'Throw mid away', 'It was too big.'],
                ['lo = mid', 'Stuck window', 'If mid equals lo, nothing changes.'],
                ['O(log n)', 'Halving', 'Missing item still takes about log n compares, not n.'],
            ],
            'e1' => ['Example 1 — find 7, miss 4', 'The window shrinks until the value matches or the window dies.', <<<'PY'
def binary_search(nums, target):
    lo, hi = 0, len(nums) - 1
    while lo <= hi:
        mid = lo + (hi - lo) // 2
        if nums[mid] == target:
            return mid
        if nums[mid] < target:
            lo = mid + 1
        else:
            hi = mid - 1
    return -1

print(binary_search([1, 3, 5, 7, 9], 7))
print(binary_search([1, 3, 5, 7, 9], 4))
PY],
            'e2' => ['Example 2 — duplicates, first index', 'Plain binary search may land on either 7. Moving left while the previous is also 7 finds the first.', <<<'PY'
def first_index(nums, target):
    lo, hi = 0, len(nums) - 1
    answer = -1
    while lo <= hi:
        mid = lo + (hi - lo) // 2
        if nums[mid] >= target:
            if nums[mid] == target:
                answer = mid
            hi = mid - 1
        else:
            lo = mid + 1
    return answer

print(first_index([1, 7, 7, 7, 9], 7))
PY],
            'micro' => [
                'Empty list: lo is 0 and hi is -1, so the loop never runs and you return -1. That is correct.',
                'A descending list needs the comparisons flipped. Do not “fix” it by reversing on every search if you can sort once.',
                'Binary search the answer, not only the index: “smallest speed that still arrives on time” is the same window on a range of speeds.',
            ],
            'quiz' => [
                $q('Binary search requires what?', 'A sorted row', 'A stack', 'Unique values only', 'A linked list'),
                $q('Why is lo = mid a bug when the target is to the right and mid equals lo?', 'The window does not shrink', 'Python cannot add', 'mid is always 0', 'hi becomes negative immediately always'),
                $q('How many comparisons, roughly, to miss in a sorted list of 1024 items?', 'About 10', '1024', '1', '1024 squared'),
            ],
            'practice' => 'Search 5 in [1, 3, 5, 7, 9]. Write lo, hi, and mid for each lap.',
        ]),
    ];
}
