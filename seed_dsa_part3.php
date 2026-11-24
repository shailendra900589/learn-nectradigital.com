<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/track_seed.php';

function dsa_chapters_master(): array
{
    $f = 'dsa_notes.py';
    $L = static function (array $c) use ($f): array {
        return track_lesson('python', $f, $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };
    $sorts = svg_bar_chart('Comparisons you should expect, n = 8', [
        ['label' => 'Insertion, sorted', 'value' => 8, 'display' => 'about n', 'color' => '#059669'],
        ['label' => 'Merge', 'value' => 24, 'display' => 'n log n', 'color' => '#0284c7'],
        ['label' => 'Insertion, reversed', 'value' => 36, 'display' => 'about n²', 'color' => '#e11d48'],
    ], '', 'Merge does not get lucky or unlucky the way insertion does. Quote the shape, not one laptop.');
    return [
        $L([
            'section' => '5. Sorting',
            'title' => 'Insertion sort: the hand of cards',
            'keywords' => 'insertion sort, stable, nearly sorted',
            'lead' => 'Insertion sort takes the next card and slides it left until it sits in order. It is slow on a reversed list and kind on a nearly sorted one. It is also stable: equal items keep their original order.',
            'heading' => 'Slide the bigger ones right, then drop the card',
            'para' => 'For each index i, hold the value and walk j backward while the neighbour is bigger. Shift that neighbour right. Drop the held value in the hole. The left side of i is always sorted. That invariant is how you check the code.',
            'bullets' => [
                'Worst and typical reversed input are O(n²) shifts. Already sorted is about n comparisons and no shifts.',
                'Stable means two equal keys stay in the order you met them. Interviewers ask because some algorithms are not.',
                'The inner loop must stop at the left edge. j >= 0 is part of the test.',
                'This is a good sort for tiny or almost-sorted lists, and a bad default for a million random ones.',
            ],
            'visual' => $sorts,
            'rows' => [
                ['Invariant', 'Left of i is sorted', 'If that breaks, the shifts are wrong.'],
                ['Hold', 'The card in your hand', 'Do not lose it when you shift.'],
                ['Shift', 'Neighbour moves right', 'A bigger neighbour moves, not a smaller one.'],
                ['Best', 'Already sorted', 'Inner loop stops immediately.'],
                ['Worst', 'Reversed', 'Each new card slides to the front.'],
                ['Stable', 'Equals do not leapfrog', 'Slide only when strictly greater, not greater-or-equal, if you want stability.'],
            ],
            'e1' => ['Example 1 — sort a short row', 'The printed list is sorted. The steps counter shows the shifts.', <<<'PY'
def insertion_sort(nums):
    shifts = 0
    for i in range(1, len(nums)):
        held = nums[i]
        j = i - 1
        while j >= 0 and nums[j] > held:
            nums[j + 1] = nums[j]
            shifts += 1
            j -= 1
        nums[j + 1] = held
    return shifts

row = [4, 2, 4, 1]
print('shifts', insertion_sort(row))
print(row)
PY],
            'e2' => ['Example 2 — nearly sorted is cheap', 'One item is out of place. Reversed pays much more.', <<<'PY'
def shifts_for(nums):
    shifts = 0
    for i in range(1, len(nums)):
        held = nums[i]
        j = i - 1
        while j >= 0 and nums[j] > held:
            nums[j + 1] = nums[j]
            shifts += 1
            j -= 1
        nums[j + 1] = held
    return shifts

print('almost', shifts_for([1, 2, 4, 3]))
print('reversed', shifts_for([4, 3, 2, 1]))
PY],
            'micro' => [
                'Using >= in the while would swap equals and break stability. This lesson uses >.',
                'The function sorts in place and also returns the shift count. Callers who only want the list can ignore the count.',
                'Python’s list.sort is not insertion sort. It is a tuned hybrid. Know this one so you can explain the cheap case.',
            ],
            'quiz' => [
                $q('Insertion sort is cheapest on which input?', 'Already sorted, or nearly', 'Reversed', 'Random of a million items', 'An empty dict'),
                $q('Why does the comparison use > rather than >= if you care about stability?', 'Equal items should not leapfrog', '>= is a syntax error', 'Equals cannot be compared', 'It makes the sort O(n)'),
                $q('What stays true on the left of i?', 'That part is sorted', 'That part is reversed', 'That part is empty', 'That part is a set'),
            ],
            'practice' => 'Sort [3, 1, 2] on paper with the hold-and-slide steps, then run it.',
        ]),
        $L([
            'section' => '5. Sorting',
            'title' => 'Merge sort: split, sort, stitch',
            'keywords' => 'merge sort, divide and conquer, stable merge',
            'lead' => 'Merge sort splits the list in half until the pieces have one item. A one-item list is sorted. Merging two sorted lists is a walk with two pointers. The whole sort is O(n log n) and needs extra space for the stitch.',
            'heading' => 'The stitch is the real work',
            'para' => 'Take the smaller head of the two sorted sides, again and again. When one side runs out, copy the rest of the other side. Forgetting the rest leaves items on the floor. The split uses extra lists, so space is O(n), not O(1).',
            'bullets' => [
                'A list of length 0 or 1 is the base case. Return it.',
                'mid = len // 2. The right half starts at mid. Both halves are shorter, so recursion ends.',
                'When heads are equal, take the left one if you want stability.',
                'The log n comes from how many times you can split n in half. Each level stitches n items.',
            ],
            'rows' => [
                ['Base', 'len <= 1', 'Already sorted.'],
                ['Split', 'Two halves', 'Each is smaller.'],
                ['Merge', 'Two pointers', 'Always consume the smaller head.'],
                ['Leftover', 'One side remains', 'Extend it. Do not drop it.'],
                ['Time', 'O(n log n)', 'Every level is O(n), and there are log n levels.'],
                ['Space', 'O(n) extra', 'The stitched lists. In-place merge exists and is fussier.'],
            ],
            'e1' => ['Example 1 — sort and keep equals stable', 'The pairs carry an original index so you can see equal 4s keep their order.', <<<'PY'
def merge_sort(items):
    if len(items) <= 1:
        return items
    mid = len(items) // 2
    left = merge_sort(items[:mid])
    right = merge_sort(items[mid:])
    out = []
    i = j = 0
    while i < len(left) and j < len(right):
        if left[i][0] <= right[j][0]:
            out.append(left[i])
            i += 1
        else:
            out.append(right[j])
            j += 1
    out.extend(left[i:])
    out.extend(right[j:])
    return out

pairs = [(4, 'a'), (1, 'b'), (4, 'c')]
print(merge_sort(pairs))
PY],
            'e2' => ['Example 2 — the leftover must be copied', 'One side is only 1. The other side is 2, 3. After 1 is taken, 2 and 3 are the leftover.', <<<'PY'
def merge(left, right):
    out = []
    i = j = 0
    while i < len(left) and j < len(right):
        if left[i] <= right[j]:
            out.append(left[i])
            i += 1
        else:
            out.append(right[j])
            j += 1
    out.extend(left[i:])
    out.extend(right[j:])
    return out

print(merge([1], [2, 3]))
PY],
            'micro' => [
                '<= takes the left half on a tie, so the earlier equal stays earlier. < would prefer the right half and scramble equals.',
                'Slicing items[:mid] copies. That copy is the extra space you should mention.',
                'Merge sort does not care if the input is reversed. The cost stays n log n. That is the point versus insertion.',
            ],
            'quiz' => [
                $q('What is the base case of merge sort?', 'A list of 0 or 1 items', 'A list that is reversed', 'A list of length 8', 'A dict'),
                $q('Why must the merge copy the leftover side?', 'Those items are sorted and not yet in the output', 'They are duplicates', 'Python requires extend', 'They are unsorted and should be dropped'),
                $q('Merge sort’s extra space is which shape?', 'O(n)', 'O(1) always', 'O(n²)', 'None, it sorts with no lists'),
            ],
            'practice' => 'Merge [1, 4] and [2, 3] by hand. Write the output after each pick.',
        ]),
        $L([
            'section' => '5. Sorting',
            'title' => 'Quick sort and the already-sorted trap',
            'keywords' => 'quick sort, pivot, partition, worst case',
            'lead' => 'Quick sort picks a pivot, puts smaller items on the left and bigger items on the right, then sorts the sides. A good pivot splits the work in half. A pivot that is always the smallest item makes a stick, and the sort becomes O(n²).',
            'heading' => 'The pivot is in its final home',
            'para' => 'After partition, the pivot’s index never changes. Everything left is smaller or equal, everything right is bigger. Recurse on the sides, not on the pivot again. Choosing the first item as pivot is fine for random data and terrible for sorted data.',
            'bullets' => [
                'Average time is O(n log n). Worst, with unlucky pivots, is O(n²).',
                'It is not stable in the simple version. Equal items can cross the pivot.',
                'The extra space of this version is the new lists. An in-place partition uses less, and the indexes are easier to get wrong.',
                'Already-sorted input plus “pivot = first item” is the textbook trap.',
            ],
            'rows' => [
                ['Pivot', 'One chosen item', 'It ends in its final index.'],
                ['Left', 'Smaller or equal', 'A new list in this lesson.'],
                ['Right', 'Strictly greater', 'The other side.'],
                ['Sorted input', 'Worst if pivot is an end', 'Each split peels one item.'],
                ['Random pivot', 'Usually balanced', 'Not a promise. A guarantee needs a smarter choice.'],
                ['Not stable', 'Equals may reorder', 'Use merge sort when order of equals matters.'],
            ],
            'e1' => ['Example 1 — a clear split', 'Pivot is the middle value so the picture is balanced.', <<<'PY'
def quick_sort(nums):
    if len(nums) <= 1:
        return nums
    pivot = nums[len(nums) // 2]
    left = [n for n in nums if n < pivot]
    mid = [n for n in nums if n == pivot]
    right = [n for n in nums if n > pivot]
    return quick_sort(left) + mid + quick_sort(right)

print(quick_sort([3, 1, 4, 1, 5]))
PY],
            'e2' => ['Example 2 — first-item pivot on sorted data', 'Each call peels a single pivot. The depths equal the length.', <<<'PY'
def depth(nums):
    if len(nums) <= 1:
        return 1
    pivot = nums[0]
    left = [n for n in nums if n < pivot]
    right = [n for n in nums if n > pivot]
    return 1 + max(depth(left), depth(right))

print('sorted depth', depth([1, 2, 3, 4, 5]))
print('mixed depth', depth([3, 1, 4, 5, 2]))
PY],
            'micro' => [
                'Items equal to the pivot must go to a middle pile or you can infinite-recurse: the side would still contain the pivot and the same length.',
                'Three passes to build left, mid, and right are still linear per level. Clarity first.',
                'Python’s sort is not this function. Do not claim list.sort is quick sort.',
            ],
            'quiz' => [
                $q('After partition, what is true of the pivot?', 'It is in its final position', 'It is always index 0', 'It is deleted', 'It must be the smallest item in the whole original list'),
                $q('Why is a sorted list plus pivot = first item slow?', 'Each split peels only one item', 'Sorted lists cannot be partitioned', 'Python forbids the first item', 'It becomes O(1)'),
                $q('What bug happens if equals stay in a side that is partitioned the same way?', 'The recursive call may not shrink', 'The list sorts twice as fast', 'Equals become strings', 'Nothing'),
            ],
            'practice' => 'Quick-sort [2, 1, 2] on paper with the middle item as pivot. Notice both 2s.',
        ]),
        $L([
            'section' => '5. Sorting',
            'title' => 'Heap: the next most important item',
            'keywords' => 'heap, priority queue, heapq, min heap',
            'lead' => 'A heap is a tree stored in a list, shaped so the smallest item sits at the front. You can take that smallest item, or push a new one, in O(log n). It is not fully sorted. It only promises the front.',
            'heading' => 'A priority queue, not a sorted display',
            'para' => 'heapq is a min-heap. heappush adds. heappop removes the smallest. The list in between can look jumbled, and that is allowed, as long as each parent is smaller than its children. If you need the biggest, push the negative, or store tuples (priority, item).',
            'bullets' => [
                'Index 0 is the smallest after each push and pop. Do not read index 1 and call it the second smallest without popping.',
                'Tuples compare the first field first. (2, "tea") comes out before (5, "milk").',
                'If priorities tie, the next field is compared. Put a counter in the middle so items that cannot be compared never crash.',
                'heapq.heapify builds a heap in linear time. Pushing n times is n log n.',
            ],
            'rows' => [
                ['heappush', 'Add one', 'O(log n).'],
                ['heappop', 'Remove smallest', 'O(log n). The front.'],
                ['heap[0]', 'Peek', 'Do not pop. Empty has no front.'],
                ['Not sorted', 'Only the heap shape', 'Printing the list is not a sort.'],
                ['Tuple', '(priority, item)', 'Lower priority leaves first.'],
                ['Tie', 'Add a counter', '(priority, count, item) when items are not ordered.'],
            ],
            'e1' => ['Example 1 — always the next smallest', 'Pushed 4, 1, 3. Pops come out 1, 3, 4. The list before the first pop is not that order.', <<<'PY'
import heapq
heap = []
for n in [4, 1, 3]:
    heapq.heappush(heap, n)
print('shape', heap)
while heap:
    print('next', heapq.heappop(heap))
PY],
            'e2' => ['Example 2 — priorities, and a tie breaker', 'Two tasks share priority 1. The counter decides, so the earlier task leaves first. The tasks themselves are never compared.', <<<'PY'
import heapq
heap = []
count = 0
for priority, task in [(1, 'write'), (5, 'ship'), (1, 'test')]:
    heapq.heappush(heap, (priority, count, task))
    count += 1
while heap:
    priority, _, task = heapq.heappop(heap)
    print(priority, task)
PY],
            'micro' => [
                'heappop on empty raises IndexError. The while heap: guard is the check.',
                'A max-heap in the standard library is “push the negative number” for numbers. Remember to negate again when you pop.',
                'Dijkstra in a later chapter is “a heap of (distance, node)”. This chapter is the tool it uses.',
            ],
            'quiz' => [
                $q('What does heappop return?', 'The smallest item', 'The last pushed item', 'The largest item', 'A sorted copy of the whole heap'),
                $q('Why add a counter between priority and task?', 'So two tasks with the same priority are not compared to each other', 'Counters sort faster', 'heapq requires three fields', 'To make it a stack'),
                $q('Is the raw heap list fully sorted?', 'No, only the front is the smallest', 'Yes, always', 'Only if you pushed in order', 'Only for strings'),
            ],
            'practice' => 'Push priorities 3, 1, 2 and pop them all. Write the pop order before you run it.',
        ]),
        $L([
            'section' => '6. Trees',
            'title' => 'Binary tree walks',
            'keywords' => 'binary tree, preorder, inorder, postorder, traversal',
            'lead' => 'A binary tree gives each node at most two children, left and right. A walk is the order you visit them. The same tree produces three different lists if you visit the node before the children, between them, or after them.',
            'heading' => 'When you write the node down',
            'para' => 'Preorder writes the node, then left, then right. Inorder writes left, then the node, then right. Postorder writes the children first, then the node. None is an empty child and the base case. Forgetting None walks off the tree.',
            'bullets' => [
                'A node is value, left, right. Missing child is None, not a fake node.',
                'Inorder of a binary search tree is sorted. That fact belongs to the next chapter; the walk itself does not require the search property.',
                'Recursion is the clear version. A stack can do the same walk if the tree is extremely deep.',
                'Visiting every node once is O(n). The shape does not change that, only the constants and the stack depth.',
            ],
            'rows' => [
                ['Preorder', 'Node, left, right', 'Useful to copy a tree prefix-style.'],
                ['Inorder', 'Left, node, right', 'Sorted when the tree is a BST.'],
                ['Postorder', 'Left, right, node', 'Children are finished before the parent. Good for “delete children first”.'],
                ['None', 'Base case', 'Return without appending.'],
                ['Depth', 'Longest path in edges', 'A single node has depth 0 in this lesson.'],
                ['n visits', 'Each node once', 'O(n) time.'],
            ],
            'e1' => ['Example 1 — three walks of one tree', 'Tree is 2 with left 1 and right 3.', <<<'PY'
def preorder(node, out):
    if node is None:
        return
    out.append(node[0])
    preorder(node[1], out)
    preorder(node[2], out)

def inorder(node, out):
    if node is None:
        return
    inorder(node[1], out)
    out.append(node[0])
    inorder(node[2], out)

def postorder(node, out):
    if node is None:
        return
    postorder(node[1], out)
    postorder(node[2], out)
    out.append(node[0])

tree = (2, (1, None, None), (3, None, None))
for walk in (preorder, inorder, postorder):
    out = []
    walk(tree, out)
    print(walk.__name__, out)
PY],
            'e2' => ['Example 2 — depth of a stick versus a balanced pair', 'A stick of three nodes is deeper than a root with two leaves.', <<<'PY'
def depth(node):
    if node is None:
        return -1
    return 1 + max(depth(node[1]), depth(node[2]))

stick = (1, (2, (3, None, None), None), None)
pair = (1, (2, None, None), (3, None, None))
print('stick', depth(stick))
print('pair', depth(pair))
PY],
            'micro' => [
                'Empty tree depth is -1 so a single node, 1 + max(-1, -1), is 0. If you prefer empty = 0, a leaf becomes 1. Pick one and stay with it.',
                'The tuple (value, left, right) is immutable. A class is fine too. Do not use a list and then sort it by accident.',
                'Preorder is not “sorted”. Only inorder plus the BST property gives sorted output.',
            ],
            'quiz' => [
                $q('Inorder visits the node when?', 'Between the left subtree and the right subtree', 'Before both children', 'After both children', 'Only the leaves'),
                $q('What is the base case?', 'The node is None', 'The value is 0', 'The depth is 8', 'The tree is a list'),
                $q('How many times is each node visited in one walk?', 'Once', 'Twice always', 'log n times', 'Never the leaves'),
            ],
            'practice' => 'Draw a root A with left B and right C. Write preorder and postorder before you code it.',
        ]),
        $L([
            'section' => '6. Trees',
            'title' => 'Binary search tree: left is smaller',
            'keywords' => 'BST, insert, search, inorder sorted',
            'lead' => 'A binary search tree adds one rule to a binary tree: every value in the left subtree is smaller than the node, and every value in the right is larger. Search then throws away half, the same idea as binary search, when the tree is balanced.',
            'heading' => 'Go left or right, do not scan',
            'para' => 'Insert walks the same way and hangs the new value on the first empty child. Duplicates need a policy. This lesson refuses a second equal, so search stays simple. Inorder of a BST prints sorted values. If it does not, the left-smaller rule was broken.',
            'bullets' => [
                'Search is O(height), not automatically O(log n). A stick has height n.',
                'Equal keys: reject, or always go right. Pick one. Mixing them breaks search.',
                'Deleting a node with two children is the hard case. This chapter inserts and searches. Deletion is a follow-on you should not fake.',
                'The comparison must match the type. Do not insert strings and ints into the same tree.',
            ],
            'rows' => [
                ['Left', 'Smaller values', 'The whole subtree, not only the child.'],
                ['Right', 'Larger values', 'The whole subtree.'],
                ['Insert', 'Walk to an empty child', 'That is the new leaf.'],
                ['Missing', 'Walk falls off', 'Return None or False.'],
                ['Inorder', 'Sorted check', 'If this is not sorted, it is not a BST.'],
                ['Height', 'The cost', 'Balanced is log n. A sorted insert order is a stick.'],
            ],
            'e1' => ['Example 1 — insert, then inorder', 'Inserted 5, 2, 8, 1. Inorder comes out sorted.', <<<'PY'
def insert(node, value):
    if node is None:
        return (value, None, None)
    current, left, right = node
    if value < current:
        return (current, insert(left, value), right)
    if value > current:
        return (current, left, insert(right, value))
    return node

def inorder(node, out):
    if node is None:
        return
    inorder(node[1], out)
    out.append(node[0])
    inorder(node[2], out)

tree = None
for value in [5, 2, 8, 1]:
    tree = insert(tree, value)
out = []
inorder(tree, out)
print(out)
PY],
            'e2' => ['Example 2 — search, and a duplicate that is ignored', '7 is missing. Inserting 5 again does not grow a second 5.', <<<'PY'
def insert(node, value):
    if node is None:
        return (value, None, None)
    current, left, right = node
    if value < current:
        return (current, insert(left, value), right)
    if value > current:
        return (current, left, insert(right, value))
    return node

def contains(node, value):
    if node is None:
        return False
    current, left, right = node
    if value == current:
        return True
    if value < current:
        return contains(left, value)
    return contains(right, value)

tree = None
for value in [5, 2, 5]:
    tree = insert(tree, value)
print(contains(tree, 2), contains(tree, 7))
PY],
            'micro' => [
                'Returning a new tuple does not mutate the old one. The caller must use the returned tree: tree = insert(tree, value).',
                'A sorted insert order 1, 2, 3, 4 builds a stick to the right. Search becomes linear. The next chapter says this out loud.',
                'None means empty, and also “not found” if you return the node. Return True and False from contains so empty and “value 0” cannot be confused.',
            ],
            'quiz' => [
                $q('Where does a smaller value go?', 'Into the left subtree', 'Into the right subtree', 'Always at the root', 'Into a list beside the tree'),
                $q('What does inorder of a BST produce?', 'The values in sorted order', 'The values in insert order', 'Only the leaves', 'A heap'),
                $q('Why can BST search be O(n)?', 'The tree can be a stick', 'Python recursion is always linear', 'Comparisons are free so the shape does not matter', 'Duplicates are required'),
            ],
            'practice' => 'Insert 4, then 1, then 6. Draw left and right, then write the inorder list.',
        ]),
        $L([
            'section' => '6. Trees',
            'title' => 'Why a stick-shaped tree is still slow',
            'keywords' => 'balanced tree, AVL idea, red black idea, height',
            'lead' => 'A binary search tree with no balance rule can be a linked list in disguise. Inserting already-sorted keys makes every new key a right child. Search then walks every node. Balanced trees rotate so the height stays near log n.',
            'heading' => 'Rotations are how a tree stands back up',
            'para' => 'You do not have to memorise every AVL case to use the idea. A rotation rewrites three pointers so a deep child becomes the parent, and the search order stays valid: left is still smaller. Databases and language maps use red-black trees or B-trees so one unlucky insert order cannot turn lookup into a scan.',
            'bullets' => [
                'Height of a stick of n nodes is n - 1. Height of a full tree is about log2(n).',
                'A rotation must not break inorder. If the sorted walk changes, the rotation is wrong.',
                'B-trees keep many keys in one node so disks move fewer pages. The balance idea is the same: no stick.',
                'Python’s dict is a hash table, not a BST. Do not describe it as a red-black tree.',
            ],
            'rows' => [
                ['Stick', 'Height n - 1', 'Sorted inserts into a plain BST.'],
                ['Balanced', 'Height near log n', 'Every search is a short walk.'],
                ['Rotation', 'Rewrite parent links', 'Inorder values stay sorted.'],
                ['AVL', 'Heights of children differ by at most 1', 'One family of rotations.'],
                ['Red-black', 'Colour rules that imply balance', 'Used by many maps.'],
                ['B-tree', 'Many keys per node', 'The database index picture.'],
            ],
            'e1' => ['Example 1 — sorted inserts make a stick', 'Each node’s right child is the next number. Height is 3 for four nodes.', <<<'PY'
def insert(node, value):
    if node is None:
        return [value, None, None]
    if value < node[0]:
        node[1] = insert(node[1], value)
    elif value > node[0]:
        node[2] = insert(node[2], value)
    return node

def height(node):
    if node is None:
        return -1
    return 1 + max(height(node[1]), height(node[2]))

tree = None
for value in [1, 2, 3, 4]:
    tree = insert(tree, value)
print('height', height(tree))
print('root', tree[0], 'right chain starts', tree[2][0])
PY],
            'e2' => ['Example 2 — a right rotation keeps inorder', 'A stick 3-2-1 rotates so 2 is the root. Inorder is still 1, 2, 3.', <<<'PY'
def inorder(node, out):
    if node is None:
        return
    inorder(node[1], out)
    out.append(node[0])
    inorder(node[2], out)

stick = [3, [2, [1, None, None], None], None]
left = stick[1]
stick[1] = left[2]
left[2] = stick
out = []
inorder(left, out)
print('root', left[0])
print(out)
PY],
            'micro' => [
                'This rotation is the picture, not a full AVL insert. A real insert rotates on the way back up only when the height rule breaks.',
                'Self-balancing trees still use the BST search rule. Balance changes the shape, not the meaning of left and right.',
                'Quote height when you quote BST cost. “BST is log n” is only true with a balance promise.',
            ],
            'quiz' => [
                $q('Inserting 1, 2, 3, 4 into a plain BST produces what shape?', 'A stick to the right', 'A perfect balanced tree', 'A heap', 'A hash table'),
                $q('What must a rotation preserve?', 'The inorder, left-smaller order', 'The insert timestamps as the new sort', 'The colour only, order may change', 'The list indexes'),
                $q('What is a Python dict?', 'A hash table', 'A red-black tree', 'An AVL tree', 'A B-tree'),
            ],
            'practice' => 'Draw inserts 1 then 2 then 3. Count the comparisons to find 3. Then draw them balanced and count again.',
        ]),
        $L([
            'section' => '6. Trees',
            'title' => 'Trie: a tree of letters',
            'keywords' => 'trie, prefix, autocomplete',
            'lead' => 'A trie stores words along a path of letters. The word “tea” is the edge t, then e, then a, and a mark that says “a word ends here.” All words that start with “te” share that path. Autocomplete is “walk the prefix, then collect the endings.”',
            'heading' => 'Shared prefixes are stored once',
            'para' => 'Each node is a dict of letter to child, plus a boolean end. Insert walks or creates a child per letter. Search fails as soon as a letter is missing. A prefix that is not a full word can still be a path: “te” is a prefix of “tea” even when end is false.',
            'bullets' => [
                'end marks a word. Without it, the prefix “car” would look like the word “car” just because “cart” was inserted.',
                'This lesson uses lowercase letters. Decide that before you mix “Tea” and “tea”.',
                'Time to insert or search a word is O(length), not O(number of words), when the alphabet walk is a dict.',
                'A missing letter stops the walk. You do not scan other branches.',
            ],
            'rows' => [
                ['Child dict', 'Letter to node', 'One step per character.'],
                ['end', 'A word stops here', 'A prefix may have end False.'],
                ['Insert', 'Create missing children', 'Then set end True on the last letter.'],
                ['Search word', 'Walk and check end', 'Path exists is not enough.'],
                ['Prefix', 'Walk only', 'end can be false.'],
                ['Shared path', 'One “te” for tea and ten', 'That is the space win.'],
            ],
            'e1' => ['Example 1 — tea is a word, te is only a prefix', 'Both share a path. Only tea has end on the last letter.', <<<'PY'
def node():
    return {'end': False, 'kids': {}}

def insert(root, word):
    cur = root
    for ch in word:
        cur = cur['kids'].setdefault(ch, node())
    cur['end'] = True

def is_word(root, word):
    cur = root
    for ch in word:
        if ch not in cur['kids']:
            return False
        cur = cur['kids'][ch]
    return cur['end']

def is_prefix(root, word):
    cur = root
    for ch in word:
        if ch not in cur['kids']:
            return False
        cur = cur['kids'][ch]
    return True

root = node()
insert(root, 'tea')
print('tea word', is_word(root, 'tea'))
print('te word', is_word(root, 'te'))
print('te prefix', is_prefix(root, 'te'))
PY],
            'e2' => ['Example 2 — collect completions', 'From the node after “te”, gather the endings.', <<<'PY'
def node():
    return {'end': False, 'kids': {}}

def insert(root, word):
    cur = root
    for ch in word:
        cur = cur['kids'].setdefault(ch, node())
    cur['end'] = True

def completions(root, prefix):
    cur = root
    for ch in prefix:
        if ch not in cur['kids']:
            return []
        cur = cur['kids'][ch]
    found = []
    def walk(n, suffix):
        if n['end']:
            found.append(prefix + suffix)
        for ch, kid in n['kids'].items():
            walk(kid, suffix + ch)
    walk(cur, '')
    return found

root = node()
for word in ['tea', 'ten', 'toast']:
    insert(root, word)
print(completions(root, 'te'))
PY],
            'micro' => [
                'setdefault creates the child only when the letter is new. It returns the child either way.',
                'An empty prefix completes every word. That can be a huge list. Cap it in a product.',
                'A trie of a million unrelated long strings can use more memory than a hash set of those strings. The win is shared prefixes and prefix search.',
            ],
            'quiz' => [
                $q('Why is the end flag necessary?', 'A path can be a prefix of a longer word without being a stored word', 'Dicts cannot store letters', 'end makes the trie a heap', 'Without it, insert is O(n²)'),
                $q('How long does it take to search a word of length k, with dict children?', 'O(k)', 'O(number of words) always', 'O(1) regardless of length', 'O(k²) because of end'),
                $q('is_prefix("te") after inserting only "tea" is?', 'True, while is_word("te") is False', 'False and False', 'True and True', 'An error'),
            ],
            'practice' => 'Insert "car" and "cart". Say whether "car" is a word, and whether "ca" is a word.',
        ]),
    ];
}
