<?php
declare(strict_types=1);

/**
 * Inserts the DSA, machine learning, AI, C, and C# tracks.
 * Creates a course only when its slug is missing.
 * Inserts a chapter only when its title is missing.
 * Never deletes a chapter. After insert, chapters are placed in reading order.
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/seed_dsa_curriculum.php';
require_once __DIR__ . '/seed_dsa_part2.php';
require_once __DIR__ . '/seed_dsa_part3.php';
require_once __DIR__ . '/seed_dsa_part4.php';
require_once __DIR__ . '/seed_dsa_part5.php';
require_once __DIR__ . '/seed_ml_curriculum.php';
require_once __DIR__ . '/seed_ml_part2.php';
require_once __DIR__ . '/seed_ai_curriculum.php';
require_once __DIR__ . '/seed_ai_part2.php';
require_once __DIR__ . '/seed_c_curriculum.php';
require_once __DIR__ . '/seed_c_part2.php';
require_once __DIR__ . '/seed_csharp_curriculum.php';
require_once __DIR__ . '/seed_csharp_part2.php';

track_seed($pdo, dsa_course_meta(), array_merge(
    dsa_chapters(),
    dsa_chapters_more(),
    dsa_chapters_master(),
    dsa_chapters_graphs(),
    dsa_chapters_depth()
));
track_seed($pdo, ml_course_meta(), array_merge(ml_chapters(), ml_chapters_depth()));
track_seed($pdo, ai_course_meta(), array_merge(ai_chapters(), ai_chapters_depth()));
track_seed($pdo, c_course_meta(), array_merge(c_chapters(), c_chapters_depth()));
track_seed($pdo, csharp_course_meta(), array_merge(csharp_chapters(), csharp_chapters_depth()));

track_place($pdo, 'dsa-using-python', [
    'A problem is an input, a rule, and an output',
    'Names, lists, and the last index',
    'One loop, and the step you forgot to count',
    'Big-O in plain words',
    'Why the front of a list is expensive',
    'Space: the extra list you did not notice',
    'A list that doubles when it fills',
    'Stack: the undo pile',
    'Brackets: every opener needs its closer',
    'The next greater item waits on a stack',
    'Queue and deque: the fair line',
    'Linked list: boxes with arrows',
    'A slow step and a fast step on a loop',
    'Turn each arrow to point backward',
    'Two pointers and a sliding window',
    'A range total is one subtraction',
    'Dictionary: keys, and what collides',
    'Two sum: remember the partner you still need',
    'Set: membership in one step',
    'Hashing a string by hand',
    'Even, odd, and a mask of bits',
    'Shared divisors, then the small primes',
    'Recursion and the base case',
    'Linear search, and stopping early',
    'Search for a word by sliding it',
    'Binary search, and the middle that must move',
    'Binary search on the size of the answer',
    'Order matters, or it does not',
    'Bubble sort: neighbours swap until the row settles',
    'Selection sort: the smallest remaining moves forward',
    'Insertion sort: the hand of cards',
    'Merge sort: split, sort, stitch',
    'Quick sort and the already-sorted trap',
    'Equal keys keep the order they arrived in',
    'Heap: the next most important item',
    'Counting sort when every number is small',
    'Binary tree walks',
    'Binary search tree: left is smaller',
    'Why a stick-shaped tree is still slow',
    'Trie: a tree of letters',
    'Drawing a graph as lists',
    'Breadth-first search: nearest first',
    'Flood fill paints the connected cells',
    'Depth-first search and the visited set',
    'Two colours, and a graph that refuses',
    'Dijkstra: cheapest path when prices are positive',
    'A road can have a negative price',
    'Topological sort: tasks that wait',
    'Union-find: are these two in the same group',
    'Cheap edges that do not close a loop',
    'Greedy: the local choice you can prove',
    'Meetings that overlap become one block',
    'Dynamic programming: write the answer down',
    'Remember the wish, or fill the table',
    'The best contiguous stretch',
    'Knapsack: the choice you write down',
    'Fewest coins, one cell at a time',
    'Backtracking: try, then undo',
    'Project: a campus route finder',
]);

track_place($pdo, 'machine-learning-python', [
    'A rule learned from examples',
    'Rows, features, and labels',
    'A category is not a bigger number',
    'The label leaked into a feature',
    'Train and test: memorising is not learning',
    'Beat the boring guess first',
    'A third pile you do not touch',
    'Rotate which fold is the test',
    'Nearest neighbour',
    'Mean, spread, and scaling',
    'Scale only what the training rows have seen',
    'A straight line, slope, and intercept',
    'A square lets a straight fit bend',
    'The error, the loss, and the average',
    'One step of gradient descent',
    'A step that is too large jumps past the valley',
    'Linear regression from scratch',
    'Two inputs get two slopes',
    'Overfit and underfit',
    'Logistic: a probability between 0 and 1',
    'Accuracy can lie',
    'A rare urgent note hides inside accuracy',
    'Precision, recall, and the four boxes',
    'Move the cutoff and the four boxes change',
    'A decision stump: one question',
    'A second question after the first',
    'Two weak questions can vote',
    'Count the words in each class',
    'K-means: groups with no labels',
    'How many clusters, by the leftover distance',
    'A penalty for huge weights',
    'Project: is this note urgent?',
    'Project: a score you can defend',
]);

track_place($pdo, 'ai-development', [
    'A rule, a learned rule, and a model',
    'Tokens: cutting a sentence into pieces',
    'The next token is the one you counted most',
    'A bag of words forgets the order',
    'A vector is a direction',
    'Similarity is a dot product',
    'Embeddings you can compute',
    'One neuron: weighted sum, then a squash',
    'Attention: which earlier token matters',
    'Prompts: instruction, context, and the question',
    'Three jobs in one prompt',
    'An example inside the prompt',
    'The front of the window falls off',
    'Cut the note into overlapping chunks',
    'Temperature and a repeated answer',
    'Hallucination: a fluent sentence that is false',
    'Quote the note or say you do not know',
    'Retrieval: look up a note, then answer',
    'A chunk that tries to change the job',
    'Tools: let code do the math',
    'Plan, use a tool, read the result, then stop',
    'Score an answer against a gold note',
    'Score a set of notes, not one lucky reply',
    'The bill is tokens in plus tokens out',
    'Prompt, retrieve, or change the weights',
    'What the next turn is allowed to remember',
    'Project: a note assistant you can explain',
]);

track_place($pdo, 'c-programming', [
    'A program starts at main and prints',
    'A name stores one value of a type',
    'A character is a small integer',
    'Arithmetic, integer division, and the remainder',
    'A cast keeps the whole number and drops the fraction',
    'A small whole number wraps',
    'printf formats and sizeof',
    'Decisions: compare, then take one branch',
    'A question can stop before the second half',
    'Named cases choose one result',
    'The inner name hides the outer name',
    'A loop, written as the passes you can see',
    'Numbered slots',
    'One row, then the next row',
    'A function is a named rule',
    'A pointer is the address of a slot',
    'The address of the next slot',
    'The name of the row is the address of the first slot',
    'A string is characters plus a zero',
    'Walk until the zero byte',
    'Two words, one character at a time',
    'A struct is a bundle of fields',
    'Stack slots, and a heap picture',
    'A promise not to store again',
    'Bits side by side: and, or, and a shift',
    'Linear search, then binary search',
    'Putting three numbers in order',
    'The mistakes that corrupt a program',
    'Project: a reading notebook',
]);

track_place($pdo, 'c-sharp-tutorial', [
    'A program writes a line',
    'Types: int, double, bool, and string',
    'var does not mean the type can change',
    'One character is not a string',
    'Fill the holes in a string',
    'Arithmetic and integer division',
    'Decisions and the difference between = and ==',
    'A second question only after the first answer is no',
    'A loop, one pass at a time',
    'Numbered slots',
    'A list grows, an array does not',
    'A set keeps each value once',
    'A line and a pile',
    'A method is a named rule',
    'A class bundles fields',
    'A property is the door to a field',
    'The constructor fills the object',
    'static belongs to the type, not one object',
    'null is not an empty string',
    'An enum is a named whole number',
    'A dictionary counts by a key',
    'A box that only holds one type',
    'The child starts with the parent fields',
    'An interface is a list of promises',
    'Two results without a new class',
    'Where and Select are loops you can already read',
    'A failed check is a result, not a surprise',
    'Expected versus actual',
    'A route is a path and a status',
    'Project: a reading notebook',
]);

track_sections($pdo, 'machine-learning-python', [
    2 => '0. What learning means',
    5 => '1. The table',
    8 => '2. A score you can trust',
    11 => '3. Distance and scaling',
    18 => '4. A line you can move',
    24 => '5. Did it really learn',
    28 => '6. A tree of questions',
    31 => '7. Groups and penalties',
    33 => '8. Projects',
]);
track_sections($pdo, 'ai-development', [
    1 => '0. Words people mix up',
    7 => '1. Text becomes numbers',
    9 => '2. A tiny network',
    17 => '3. Using a model',
    23 => '4. A system, not a sentence',
    26 => '5. Choosing a method',
    27 => '6. The project',
]);
track_sections($pdo, 'c-programming', [
    1 => '0. A program',
    7 => '1. Values',
    12 => '2. Choosing',
    15 => '3. Groups of values',
    25 => '4. Memory and text',
    27 => '5. Classic walks',
    28 => '6. Care',
    29 => '7. The project',
]);
track_sections($pdo, 'c-sharp-tutorial', [
    1 => '0. A program',
    6 => '1. Values',
    9 => '2. Choosing',
    13 => '3. Groups',
    18 => '4. Names and objects',
    25 => '5. Types that compose',
    28 => '6. Checks',
    30 => '7. A small system',
]);

echo "done\n";
