<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/track_seed.php';

function dsa_chapters_graphs(): array
{
    $f = 'dsa_notes.py';
    $L = static function (array $c) use ($f): array {
        return track_lesson('python', $f, $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };
    $path = svg_pipeline_diagram('Campus route, one step at a time', [
        ['Gate', 'start'],
        ['Library', '1 hop'],
        ['Lab', '2 hops'],
        ['Cafe', 'found'],
    ]);
    return [
        $L([
            'section' => '7. Graphs',
            'title' => 'Drawing a graph as lists',
            'keywords' => 'graph, adjacency list, edge, undirected',
            'lead' => 'A graph is corners and the paths between them. A corner is a node. A path between two corners is an edge. The practical storage is a dict: each node maps to the list of neighbours.',
            'heading' => 'Say whether the path goes both ways',
            'para' => 'An undirected edge {library, lab} must be stored twice: library’s list contains lab, and lab’s list contains library. Forgetting the way back makes a one-way street you did not mean. A missing node should still have an empty list, or a lookup crashes when you ask for its neighbours.',
            'bullets' => [
                'Nodes in this lesson are strings. The same picture works with ints.',
                'A self-loop is a node in its own neighbour list. Say if you allow it.',
                'A weight is a number on the edge. Store pairs (neighbour, weight) when the path has a cost. Unweighted graphs can store plain neighbours.',
                'The matrix version is a grid of yes/no. It wastes space when almost no edges exist. Lists are the default here.',
            ],
            'rows' => [
                ['Node', 'A corner', 'The dict key.'],
                ['Edge', 'A connection', 'Undirected means store both directions.'],
                ['Neighbour list', 'Who you can step to', 'Empty list means a dead end, which is legal.'],
                ['Weight', 'Cost of a step', 'A pair, not a bare name, once costs exist.'],
                ['Missing key', 'Crash on graph[node]', 'Use setdefault so every node has a list.'],
                ['Matrix', 'Grid of edges', 'Better only when the graph is dense.'],
            ],
            'e1' => ['Example 1 — both directions', 'library connects to lab. Asking lab must also show library.', <<<'PY'
def add_undirected(graph, a, b):
    graph.setdefault(a, []).append(b)
    graph.setdefault(b, []).append(a)

graph = {}
add_undirected(graph, 'library', 'lab')
add_undirected(graph, 'library', 'gate')
print(sorted(graph['library']))
print(sorted(graph['lab']))
PY],
            'e2' => ['Example 2 — a weighted street', 'The neighbour is a pair. Forgetting to unpack it later is the bug.', <<<'PY'
graph = {'gate': [('library', 2), ('cafe', 9)]}
for neighbour, cost in graph['gate']:
    print(neighbour, cost)
PY],
            'micro' => [
                'Appending the same edge twice makes two identical neighbours. Search still works, but you visit twice unless a seen set saves you.',
                'setdefault returns the list, then append mutates it. You do not need to assign the result.',
                'Draw the picture before the dict. If the picture has an arrow only one way, do not add the reverse.',
            ],
            'quiz' => [
                $q('An undirected edge between A and B is stored how?', 'A lists B, and B lists A', 'Only A lists B', 'Only in a set of numbers', 'As a single string AB'),
                $q('What should a node with no streets have?', 'An empty neighbour list', 'No key at all, so lookup crashes', 'A neighbour of None as a string', 'A zero'),
                $q('When do you store (neighbour, weight) instead of a bare neighbour?', 'When the street has a cost', 'Always, even for unweighted graphs, or the code is illegal', 'Only for trees', 'Never in Python'),
            ],
            'practice' => 'Draw gate—library—lab as undirected. Write the three lists, then build them.',
        ]),
        $L([
            'section' => '7. Graphs',
            'title' => 'Breadth-first search: nearest first',
            'keywords' => 'BFS, shortest path unweighted, queue',
            'lead' => 'Breadth-first search walks a graph in layers: everyone one step away, then everyone two steps away. On an unweighted graph, the first time you reach a node is a shortest path. The tool is the queue from earlier.',
            'heading' => 'The queue is the frontier',
            'para' => 'Start by pushing the source and marking it seen. Pop a node, and push each unseen neighbour, marking it seen when you push, not when you pop. Marking late lets the same neighbour sit in the queue many times.',
            'bullets' => [
                'Seen at push time. That is the small point.',
                'The path length increases by 1 per edge. A weight of 5 is not “one step”. BFS ignores weights. Dijkstra is for weights.',
                'Keep a parent dict if you need the path, not only the distance.',
                'A disconnected target stays at no parent. Distance stays missing. Do not pretend it is zero.',
            ],
            'visual' => $path,
            'rows' => [
                ['Queue', 'Who to visit next', 'FIFO, so nearer nodes leave first.'],
                ['Seen', 'Already queued', 'A set. Mark when pushing.'],
                ['Distance', 'Edges from the start', 'Parent’s distance plus 1.'],
                ['First visit', 'Shortest on unweighted graphs', 'Later visits are longer or equal, so ignore them.'],
                ['Parent', 'Who discovered me', 'Walk parents backward to print the path.'],
                ['Weighted', 'Do not use plain BFS', 'A cheap edge count is not a cheap cost.'],
            ],
            'e1' => ['Example 1 — distances from the gate', 'Cafe is two hops via library, not the long story.', <<<'PY'
from collections import deque

graph = {
    'gate': ['library'],
    'library': ['gate', 'lab', 'cafe'],
    'lab': ['library'],
    'cafe': ['library'],
}

def distances(start):
    dist = {start: 0}
    line = deque([start])
    while line:
        node = line.popleft()
        for nxt in graph[node]:
            if nxt not in dist:
                dist[nxt] = dist[node] + 1
                line.append(nxt)
    return dist

print(distances('gate'))
PY],
            'e2' => ['Example 2 — rebuild the path', 'Parents point toward the start. Reverse the walk at the end.', <<<'PY'
from collections import deque

graph = {
    'gate': ['library'],
    'library': ['gate', 'cafe'],
    'cafe': ['library'],
}

def path(start, goal):
    parent = {start: None}
    line = deque([start])
    while line:
        node = line.popleft()
        if node == goal:
            break
        for nxt in graph[node]:
            if nxt not in parent:
                parent[nxt] = node
                line.append(nxt)
    if goal not in parent:
        return None
    out = []
    node = goal
    while node is not None:
        out.append(node)
        node = parent[node]
    out.reverse()
    return out

print(path('gate', 'cafe'))
print(path('gate', 'missing'))
PY],
            'micro' => [
                'dist doubles as the seen set here. A node is seen exactly when it has a distance.',
                'The break when the goal is popped still has a shortest path, because it was queued by layers. You may keep going if you need every distance.',
                'On a directed graph, only follow arrows that exist. Do not add the reverse unless the streets are two-way.',
            ],
            'quiz' => [
                $q('BFS uses which structure?', 'A queue', 'A stack only', 'A heap of random numbers', 'Recursion with no memory'),
                $q('When do you mark a node seen?', 'When you push it', 'Only after every neighbour is processed, never before', 'When the program ends', 'You should not mark it'),
                $q('BFS shortest paths require what kind of edges?', 'Unweighted, each edge is one step', 'Any positive or negative weights', 'Only self-loops', 'A sorted list'),
            ],
            'practice' => 'Add a node "dorm" that only the lab reaches. Predict its distance from the gate.',
        ]),
        $L([
            'section' => '7. Graphs',
            'title' => 'Depth-first search and the visited set',
            'keywords' => 'DFS, visited, recursion, cycle',
            'lead' => 'Depth-first search walks as far as it can down one path, then backs up. A stack, or recursion, is the tool. Without a visited set, a cycle makes the walk infinite.',
            'heading' => 'Go deep, and never re-enter a node',
            'para' => 'Mark the node when you enter it. Then walk each neighbour that is not marked. The order is not a shortest path. It is “a path” and “have I seen every reachable node?” Detecting a cycle in a directed graph needs a third colour: still on the current path. This chapter’s undirected seen-set stops the infinite loop and is not by itself a full cycle proof, because the parent edge looks like a back edge.',
            'bullets' => [
                'Recursion is a stack. An explicit list with pop() is the same order if you push neighbours carefully.',
                'Mark on entry. Marking after the recursive calls lets a cycle re-enter.',
                'DFS does not give hop distance. Do not print len(path) and call it shortest.',
                'The parent in an undirected graph is not a new discovery. Skip the node you just came from if you are explaining cycles.',
            ],
            'rows' => [
                ['Stack or recursion', 'The frontier', 'Newest node is next. That is why it is deep.'],
                ['Seen', 'Entered already', 'Stops cycles from looping.'],
                ['Not shortest', 'A different order than BFS', 'First time you see the goal may be a long way around.'],
                ['Backtrack', 'Return to the caller', 'The call stack pops.'],
                ['Directed cycle', 'A node still on this path', 'Needs grey/black, not only seen.'],
                ['Parent skip', 'Undirected', 'The edge back to parent is not news.'],
            ],
            'e1' => ['Example 1 — one deep order', 'The print order goes down library to lab before the other branch, because lab is listed first.', <<<'PY'
graph = {
    'gate': ['library'],
    'library': ['lab', 'cafe'],
    'lab': [],
    'cafe': [],
}

def dfs(node, seen, order):
    seen.add(node)
    order.append(node)
    for nxt in graph[node]:
        if nxt not in seen:
            dfs(nxt, seen, order)

order = []
dfs('gate', set(), order)
print(order)
PY],
            'e2' => ['Example 2 — a cycle that would never end', 'library and lab point at each other. The seen set prints each once.', <<<'PY'
graph = {'library': ['lab'], 'lab': ['library']}

def dfs(node, seen, order):
    if node in seen:
        return
    seen.add(node)
    order.append(node)
    for nxt in graph[node]:
        dfs(nxt, seen, order)

order = []
dfs('library', set(), order)
print(order)
PY],
            'micro' => [
                'Adding to seen at the start of the function is the mark-on-entry rule, even when the check also happens before the call.',
                'The order depends on the neighbour list order. Do not freeze a test to a different order than the lists.',
                'For a maze with equal steps, BFS is the shortest walk. DFS is the “try a corridor until it dies” walk.',
            ],
            'quiz' => [
                $q('What goes wrong on a cycle if you never mark visited?', 'The walk never finishes', 'The walk becomes BFS', 'Python sorts the graph', 'Nothing'),
                $q('Does DFS promise the fewest hops?', 'No', 'Yes, always', 'Only on weighted graphs', 'Only if you use a queue'),
                $q('When is a node marked in this lesson?', 'When you enter it', 'After all nodes in the graph are done', 'Only the goal', 'Never'),
            ],
            'practice' => 'On paper, DFS from gate with neighbours listed as cafe then library. Write the order. Compare with the example’s list order.',
        ]),
        $L([
            'section' => '7. Graphs',
            'title' => 'Dijkstra: cheapest path when prices are positive',
            'keywords' => 'Dijkstra, weighted shortest path, priority queue',
            'lead' => 'When streets have positive prices, the fewest streets is not the cheapest trip. Dijkstra always expands the cheapest unfinished node next, using the heap. It is wrong if a price can be negative.',
            'heading' => 'Settle the cheapest, then relax the neighbours',
            'para' => 'Distance of the start is 0. Push (0, start). Pop the cheapest. If you already settled that node with a better or equal cost, skip it. Otherwise settle it, and for each neighbour try distance-plus-weight. If that is better, record it and push. The first time you pop the goal, that cost is final, because every later path is at least as expensive and prices are not negative.',
            'bullets' => [
                'Negative weights break the “first pop is final” sentence. Use Bellman-Ford then, and it can also report a negative cycle.',
                'A stale heap entry is normal. You may push the same node twice. Skip a pop when its cost is worse than the best you know.',
                'Parent pointers rebuild the route, same as BFS, but the number on the node is money, not hops.',
                'An unreachable goal never gets popped. Leave its distance as missing, not zero.',
            ],
            'rows' => [
                ['Positive weights', 'Required', 'Zero is allowed. Negative is not.'],
                ['Heap', '(cost, node)', 'Smallest cost pops first.'],
                ['Settle', 'First pop of a node', 'That cost is final under the rule.'],
                ['Relax', 'Try cost + weight', 'Update only when strictly better.'],
                ['Stale entry', 'An old pair still in the heap', 'Skip if the popped cost is not the best.'],
                ['Unreachable', 'No distance', 'Do not use 0. Zero means a free path from the start.'],
            ],
            'e1' => ['Example 1 — the long road is cheaper', 'Gate to cafe is 9 directly, or 2 + 2 via library. The answer is 4.', <<<'PY'
import heapq
graph = {
    'gate': [('library', 2), ('cafe', 9)],
    'library': [('cafe', 2)],
    'cafe': [],
}

def cheapest(start, goal):
    best = {start: 0}
    heap = [(0, start)]
    while heap:
        cost, node = heapq.heappop(heap)
        if cost != best.get(node):
            continue
        if node == goal:
            return cost
        for nxt, weight in graph[node]:
            new_cost = cost + weight
            if new_cost < best.get(nxt, 10**9):
                best[nxt] = new_cost
                heapq.heappush(heap, (new_cost, nxt))
    return None

print(cheapest('gate', 'cafe'))
PY],
            'e2' => ['Example 2 — a missing town', 'Dorm is not in the graph as a neighbour of anyone. The answer is None, not 0.', <<<'PY'
import heapq
graph = {'gate': [('library', 2)], 'library': []}

def cheapest(start, goal):
    best = {start: 0}
    heap = [(0, start)]
    while heap:
        cost, node = heapq.heappop(heap)
        if cost != best.get(node):
            continue
        if node == goal:
            return cost
        for nxt, weight in graph[node]:
            new_cost = cost + weight
            if new_cost < best.get(nxt, 10**9):
                best[nxt] = new_cost
                heapq.heappush(heap, (new_cost, nxt))
    return None

print(cheapest('gate', 'dorm'))
PY],
            'micro' => [
                '10**9 is a stand-in for infinity that is larger than any real path in the example. A missing key means the same thing.',
                'Skipping when cost != best[node] drops stale pairs. Forgetting the skip still works but does extra work, if you only update when better.',
                'If two weights are equal, either path is fine. Say that so a test does not demand one parent.',
            ],
            'quiz' => [
                $q('Dijkstra requires which weights?', 'Non-negative', 'Any negative is fine', 'Only weights of 1', 'No weights at all'),
                $q('Why can the direct road lose to a two-step road?', 'The two steps can cost less money', 'BFS forbids direct roads', 'Heaps sort strings', 'The goal must have degree 1'),
                $q('What should an unreachable goal return here?', 'None', '0', 'The start node', '-1 as a distance that means free'),
            ],
            'practice' => 'Change the library-to-cafe weight to 20. Predict the cheapest gate-to-cafe cost before you run it.',
        ]),
        $L([
            'section' => '7. Graphs',
            'title' => 'Topological sort: tasks that wait',
            'keywords' => 'topological sort, DAG, cycle, indegree',
            'lead' => 'Some jobs cannot start until other jobs finish. A topological order is a line-up where every arrow points forward. It exists only if the graph has no cycle. A cycle means a job waits for itself.',
            'heading' => 'Start with whoever is waiting on nobody',
            'para' => 'Count incoming arrows. Anyone with count 0 can go next. When they go, reduce the count of each neighbour. If a neighbour hits 0, they can go. If you finish and some nodes never went, those are stuck in a cycle.',
            'bullets' => [
                'This is Kahn’s algorithm. It is a queue of ready tasks.',
                'Many orders can be legal. The queue’s ties depend on insertion order.',
                'A self-loop is a cycle. That node’s count never hits a true ready state if you counted the loop.',
                'An edge means “must happen before”, not “is a road you walk for distance”.',
            ],
            'rows' => [
                ['DAG', 'Directed, no cycle', 'The only graphs with a topo order.'],
                ['Indegree', 'How many prerequisites', 'Start at 0.'],
                ['Ready', 'Indegree 0', 'Push them first.'],
                ['Reduce', 'After a task runs', 'Each successor loses one prerequisite.'],
                ['Cycle', 'Someone never ran', 'len(order) < number of nodes.'],
                ['Not unique', 'Several legal orders', 'Do not call one of them the only answer.'],
            ],
            'e1' => ['Example 1 — dress in an order that works', 'Socks before shoes. Shirt before coat. Several orders are legal; this one is determined by the ready queue.', <<<'PY'
from collections import deque
nodes = ['socks', 'shoes', 'shirt', 'coat']
edges = [('socks', 'shoes'), ('shirt', 'coat')]
indegree = {node: 0 for node in nodes}
graph = {node: [] for node in nodes}
for before, after in edges:
    graph[before].append(after)
    indegree[after] += 1
ready = deque([node for node in nodes if indegree[node] == 0])
order = []
while ready:
    node = ready.popleft()
    order.append(node)
    for nxt in graph[node]:
        indegree[nxt] -= 1
        if indegree[nxt] == 0:
            ready.append(nxt)
print(order)
PY],
            'e2' => ['Example 2 — a cycle is detected', 'A waits for B and B waits for A. The order is shorter than the node list.', <<<'PY'
from collections import deque
nodes = ['a', 'b']
edges = [('a', 'b'), ('b', 'a')]
indegree = {node: 0 for node in nodes}
graph = {node: [] for node in nodes}
for before, after in edges:
    graph[before].append(after)
    indegree[after] += 1
ready = deque([node for node in nodes if indegree[node] == 0])
order = []
while ready:
    node = ready.popleft()
    order.append(node)
    for nxt in graph[node]:
        indegree[nxt] -= 1
        if indegree[nxt] == 0:
            ready.append(nxt)
print(order)
print('cycle', len(order) != len(nodes))
PY],
            'micro' => [
                'Build indegree for every node, even those with no edge, or a lonely task is forgotten.',
                'The edge (before, after) means before must appear earlier in the order.',
                'If the ready queue starts empty and there are nodes, the whole graph is cyclic or you forgot a zero-indegree node.',
            ],
            'quiz' => [
                $q('A topological order exists only when?', 'The directed graph has no cycle', 'The graph is undirected', 'Every node has an edge to every other', 'The weights are negative'),
                $q('Which tasks are ready at the start?', 'Tasks with indegree 0', 'Tasks with the most edges', 'The alphabetically last task only', 'Any task in a cycle'),
                $q('How do you notice a cycle here?', 'The order never includes every node', 'The queue raises IndexError', 'Indegree becomes negative and that is success', 'Dijkstra returns None'),
            ],
            'practice' => 'Add “belt before coat”. List one legal order and say whether socks must be before the shirt. They must not.',
        ]),
        $L([
            'section' => '7. Graphs',
            'title' => 'Union-find: are these two in the same group',
            'keywords' => 'union find, disjoint set, path compression',
            'lead' => 'Union-find keeps groups. Union joins two groups. Find says which group a member belongs to. Two members are connected when their finds match. With path compression, the trees stay flat and the work is almost constant.',
            'heading' => 'Point at a parent, and flatten on the way out',
            'para' => 'Each member starts as their own parent. Find follows parents until one is their own parent, and that one is the root. On the way back, point everyone directly at the root. Union makes one root the parent of the other. Joining a member to itself changes nothing.',
            'bullets' => [
                'This does not store the path. It only answers “same group?”. BFS stores a path.',
                'Path compression mutates parents during find. Find is not a pure read.',
                'Union by rank or size avoids a stick. Compression alone is already a big improvement. This lesson compresses and attaches one root under the other.',
                'The classic use is “do these two cities become connected if I add roads one at a time?”',
            ],
            'rows' => [
                ['Parent', 'Who is above me', 'A root’s parent is itself.'],
                ['Find', 'Walk to the root', 'Compress so the next walk is shorter.'],
                ['Union', 'Join two roots', 'If the roots match, they were already together.'],
                ['Same group', 'find(a) == find(b)', 'The whole question.'],
                ['Not a path', 'No route list', 'Ask BFS or Dijkstra for the streets.'],
                ['Almost O(1)', 'With compression', 'Quote “almost constant”, not “a loop of n”.'],
            ],
            'e1' => ['Example 1 — roads connect cities', 'After the two roads, gate and cafe are in one group even without a direct road.', <<<'PY'
parent = {}

def find(name):
    parent.setdefault(name, name)
    if parent[name] != name:
        parent[name] = find(parent[name])
    return parent[name]

def union(a, b):
    ra, rb = find(a), find(b)
    if ra != rb:
        parent[rb] = ra

union('gate', 'library')
union('library', 'cafe')
print(find('gate') == find('cafe'))
print(find('gate') == find('dorm'))
PY],
            'e2' => ['Example 2 — a redundant road', 'Union returns whether it was a new join. The second gate-library link is not news.', <<<'PY'
parent = {}

def find(name):
    parent.setdefault(name, name)
    if parent[name] != name:
        parent[name] = find(parent[name])
    return parent[name]

def union(a, b):
    ra, rb = find(a), find(b)
    if ra == rb:
        return False
    parent[rb] = ra
    return True

print('first', union('gate', 'library'))
print('again', union('gate', 'library'))
PY],
            'micro' => [
                'setdefault on find means you do not have to pre-register every city.',
                'parent[rb] = ra links roots, not the original names, or you can build a mess. find already returned roots.',
                'If you need the size of a group, store size[root] and add it when you union. Do not walk every member each time.',
            ],
            'quiz' => [
                $q('What question does union-find answer?', 'Whether two members are in the same group', 'The shortest weighted path', 'A topological order', 'The sorted list of edges'),
                $q('What is a root?', 'A member whose parent is itself', 'The alphabetically first name', 'A node with indegree 0 only', 'The last inserted city'),
                $q('What does path compression do?', 'Points walked members directly at the root', 'Sorts the group', 'Deletes the smaller group', 'Adds a negative weight'),
            ],
            'practice' => 'Union a-b and c-d. Predict whether find(a) equals find(c) before any more unions.',
        ]),
        $L([
            'section' => '8. Patterns to master',
            'title' => 'Greedy: the local choice you can prove',
            'keywords' => 'greedy, interval scheduling, counterexample',
            'lead' => 'A greedy algorithm takes the best-looking step right now and never undoes it. That is safe only when you can argue the step cannot ruin the best overall answer. “It feels best” is not a proof. A counterexample is how you reject a greedy idea.',
            'heading' => 'Earliest finish is a choice you can defend',
            'para' => 'You want as many non-overlapping meetings as possible. Always take the meeting that finishes first among those that start after the previous one finished. Taking the shortest meeting instead is a trap: a short meeting in the middle can block two others. The counterexample is the lesson.',
            'bullets' => [
                'Sort by finish time, not by start time and not by duration.',
                'A meeting that starts before the last chosen one finished overlaps. Skip it.',
                'If you cannot find a counterexample, you still do not have a proof. This course gives one known-correct rule and one known-bad rule.',
                'Coin change with coins 1, 5, 10 is greedy-safe. Coins 1, 4, 6 are not, for some amounts. Know the difference.',
            ],
            'rows' => [
                ['Greedy', 'Commit now', 'No undo.'],
                ['Proof', 'Why the commit is safe', 'Required. A hunch is not a proof.'],
                ['Counterexample', 'A small input that breaks a bad rule', 'How you reject “shortest meeting”.'],
                ['Finish time', 'The sort key here', 'Earliest finish leaves the most room after.'],
                ['Overlap', 'Starts before previous end', 'Skip.'],
                ['Coins', 'Not always greedy', 'Depends on the coin set.'],
            ],
            'e1' => ['Example 1 — earliest finish', 'Meetings are (start, end). The chosen set is as large as possible.', <<<'PY'
def most_meetings(meetings):
    chosen = []
    end = -1
    for start, finish in sorted(meetings, key=lambda m: m[1]):
        if start >= end:
            chosen.append((start, finish))
            end = finish
    return chosen

print(most_meetings([(1, 4), (3, 5), (0, 6), (5, 7), (8, 9)]))
PY],
            'e2' => ['Example 2 — shortest meeting is the wrong greed', 'The short middle meeting blocks two longer ones that do not overlap each other. Earliest-finish takes the two.', <<<'PY'
meetings = [(0, 5), (4, 6), (6, 10)]

def by_shortest(meetings):
    chosen = []
    end = -1
    ordered = sorted(meetings, key=lambda m: m[1] - m[0])
    for start, finish in ordered:
        if start >= end:
            chosen.append((start, finish))
            end = finish
    return chosen

print('shortest rule', by_shortest(meetings))
print('count', len(by_shortest(meetings)))
PY],
            'micro' => [
                'start >= end allows a meeting that starts exactly when the previous one ends. Use > if touching is forbidden. Say which.',
                'Sorting makes a new list. The input order is the calendar order, not the decision order.',
                'Greedy for this problem is O(n log n) because of the sort, then one pass.',
            ],
            'quiz' => [
                $q('Which meeting does the correct greedy rule take next?', 'The one that finishes earliest among those that start in time', 'The shortest meeting always', 'The one that starts first always', 'The last meeting on the calendar'),
                $q('What does a counterexample prove?', 'That a proposed rule is not always correct', 'That greedy never works', 'That the input is too large', 'That sorting is impossible'),
                $q('Why is “it feels best” not enough?', 'A local choice can block a better overall answer', 'Python cannot sort tuples', 'Feelings are O(1)', 'Because meetings cannot overlap'),
            ],
            'practice' => 'On meetings (0, 3), (3, 4), (2, 6), list the earliest-finish selection. Does (0, 3) and (3, 4) both fit if touching is allowed?',
        ]),
        $L([
            'section' => '8. Patterns to master',
            'title' => 'Dynamic programming: write the answer down',
            'keywords' => 'dynamic programming, memo, fibonacci, overlapping subproblems',
            'lead' => 'Dynamic programming is recursion that writes down answers it will need again. Fibonacci without a note recomputes the same n thousands of times. A list where index n holds the answer turns those repeats into one lookup.',
            'heading' => 'The table is the memory',
            'para' => 'The state is the thing you need to know: here, the n. The transition is how a state is built from smaller states: fib(n) = fib(n - 1) + fib(n - 2). The base cases are fib(0) and fib(1). Fill the table from the small states upward so the pieces exist before you use them.',
            'bullets' => [
                'If the subproblems do not overlap, the note does not help. Merge sort’s halves do not repeat. Fibonacci’s do.',
                'A dict memo keyed by n is the same idea when the state is not a small integer.',
                'The naive recursion is not “wrong”. It is exponential. The table is O(n).',
                'Order matters. Filling fib(n) before fib(n - 1) reads an empty slot.',
            ],
            'rows' => [
                ['State', 'What the cell means', 'fib[n] is the nth Fibonacci number.'],
                ['Transition', 'How to compute a cell', 'Sum of the previous two.'],
                ['Base', 'Cells you write first', '0 and 1.'],
                ['Overlap', 'Same state, many callers', 'The reason to store it.'],
                ['Bottom up', 'Small to large', 'A loop. No recursion depth.'],
                ['Memo', 'Top down with a dict', 'Still stores each state once.'],
            ],
            'e1' => ['Example 1 — the slow tree versus the table', 'fib_slow(10) does a lot of adds. The table does ten.', <<<'PY'
def fib_slow(n, counter):
    counter[0] += 1
    if n < 2:
        return n
    return fib_slow(n - 1, counter) + fib_slow(n - 2, counter)

def fib_table(n):
    if n < 2:
        return n
    table = [0, 1]
    for i in range(2, n + 1):
        table.append(table[i - 1] + table[i - 2])
    return table[n]

counter = [0]
print('slow', fib_slow(10, counter), 'calls', counter[0])
print('table', fib_table(10))
PY],
            'e2' => ['Example 2 — a memo dict', 'Same answers. Each n is computed once.', <<<'PY'
def fib_memo(n, memo):
    if n in memo:
        return memo[n]
    if n < 2:
        memo[n] = n
        return n
    memo[n] = fib_memo(n - 1, memo) + fib_memo(n - 2, memo)
    return memo[n]

print(fib_memo(10, {}))
print(fib_memo(0, {}))
PY],
            'micro' => [
                'counter is a one-item list so the inner function can mutate it. An int parameter would be a local copy.',
                'Fibonacci here is 0, 1, 1, 2, 3, 5, 8, 13, 21, 34, 55 for n = 0 through 10. fib(10) is 55.',
                'Name the state in a sentence before you write the array. “The cell is the min cost to reach stair i” is the whole design.',
            ],
            'quiz' => [
                $q('Why is naive recursive Fibonacci slow?', 'It recomputes the same n many times', 'Addition is O(n²)', 'Python cannot recurse to 10', 'The base case is missing'),
                $q('What is the state in this lesson?', 'The n whose Fibonacci number you want', 'The list of all Python objects', 'The pivot', 'The graph colour'),
                $q('fib_table(10) equals what?', '55', '10', '1024', '1'),
            ],
            'practice' => 'Fill a table for fib 0 through 6 by hand. Then check fib_table(6).',
        ]),
        $L([
            'section' => '8. Patterns to master',
            'title' => 'Knapsack: the choice you write down',
            'keywords' => '0/1 knapsack, dynamic programming table',
            'lead' => 'You have a weight limit and items you may take once. For each item and each capacity, the best value is either “skip this item” or “take it, if it fits, plus the best of the leftover capacity.” That sentence is the table.',
            'heading' => 'One item, two choices, no repeats',
            'para' => 'dp[i][c] is the best value using the first i items with capacity c. Taking item i - 1 requires c >= weight, and then you add its value to dp[i - 1][c - weight]. Skipping is dp[i - 1][c]. You want the larger one. Items may not be cut in half. That is the 0/1 rule.',
            'bullets' => [
                'Capacity 0 is value 0. Zero items is value 0. Those are the base edges of the table.',
                'Walk items in the outer loop and capacities in the inner loop if the row depends on the previous row.',
                'A one-row version must walk capacity downward, or you will reuse the same item twice. This lesson uses two dimensions so that bug is visible.',
                'The table stores value, not the list of items. Reconstruct the items by seeing where you took rather than skipped, if you need the list.',
            ],
            'rows' => [
                ['0/1', 'Take or leave', 'No fractions. No taking twice.'],
                ['dp[i][c]', 'Best value', 'First i items, capacity c.'],
                ['Skip', 'dp[i - 1][c]', 'Same capacity, without this item.'],
                ['Take', 'value + dp[i - 1][c - w]', 'Only when c >= w.'],
                ['Base', 'Zeros on the edges', 'No items or no capacity.'],
                ['Downward loop', 'One-row trick', 'Required so an item is not reused. Not used in the 2D code.'],
            ],
            'e1' => ['Example 1 — three items, capacity 5', 'Weights 2, 3, 4 and values 3, 4, 5. The best is 7, from the first two items.', <<<'PY'
def knapsack(weights, values, cap):
    n = len(weights)
    dp = [[0] * (cap + 1) for _ in range(n + 1)]
    for i in range(1, n + 1):
        for c in range(cap + 1):
            skip = dp[i - 1][c]
            take = 0
            w = weights[i - 1]
            if c >= w:
                take = values[i - 1] + dp[i - 1][c - w]
            dp[i][c] = max(skip, take)
    return dp[n][cap]

print(knapsack([2, 3, 4], [3, 4, 5], 5))
PY],
            'e2' => ['Example 2 — an item that does not fit', 'Capacity 1 cannot hold weight 2. The value stays 0. A bug that ignores the fit check would still add 3.', <<<'PY'
def knapsack(weights, values, cap):
    n = len(weights)
    dp = [[0] * (cap + 1) for _ in range(n + 1)]
    for i in range(1, n + 1):
        for c in range(cap + 1):
            skip = dp[i - 1][c]
            take = skip
            w = weights[i - 1]
            if c >= w:
                take = max(take, values[i - 1] + dp[i - 1][c - w])
            dp[i][c] = max(skip, take)
    return dp[n][cap]

print(knapsack([2], [3], 1))
PY],
            'micro' => [
                '[[0] * (cap + 1) for _ in range(n + 1)] makes separate rows. [[0] * (cap + 1)] * (n + 1) would alias one row and corrupt the table.',
                'Index i - 1 is the item, because row 0 means “no items”. Off-by-one here picks the wrong weight.',
                'If values can be negative, “best” might mean skip everything. This lesson uses positive values.',
            ],
            'quiz' => [
                $q('What does dp[i][c] mean?', 'Best value using the first i items and capacity c', 'The weight of item i', 'The number of items that fit in one slot', 'The shortest path'),
                $q('When may you take an item?', 'When its weight is less than or equal to the capacity you have left', 'Always', 'Only if it is the lightest', 'Only once the table is full of -1'),
                $q('Why not copy a row with * (n + 1)?', 'Every row would be the same list and updates would alias', 'The star is a syntax error', 'It sorts the items', 'It makes capacity zero'),
            ],
            'practice' => 'Items (weight 1, value 1) and (weight 2, value 3), capacity 2. Compute the best value by hand.',
        ]),
        $L([
            'section' => '8. Patterns to master',
            'title' => 'Backtracking: try, then undo',
            'keywords' => 'backtracking, subsets, undo, n-queens idea',
            'lead' => 'Backtracking tries a choice, goes deeper, and undoes the choice when it comes back. Subsets, permutations, and puzzles like n-queens are this shape. The undo is the whole point. If you forget it, the choice leaks into the next branch.',
            'heading' => 'Choose, explore, un-choose',
            'para' => 'For subsets, each number is taken or not. You append it, recurse, then pop it. You record a copy of the current path, not the path list itself, because you will mutate that list later. path[:] is the copy.',
            'bullets' => [
                'The explore tree is exponential. Pruning means “stop this branch early” when it cannot succeed, such as a queen already attacked.',
                'Pop exactly what you appended. A bug that pops the wrong end corrupts the parent branch.',
                'Copy when you save an answer. Otherwise every saved subset becomes the final empty path.',
                'Start index prevents permutations of the same subset. Subsets of [1, 2] should not list [2, 1] as different if order does not matter.',
            ],
            'rows' => [
                ['Choose', 'Append a candidate', 'The path grows.'],
                ['Explore', 'Recurse', 'Smaller remaining choices.'],
                ['Undo', 'Pop', 'The path returns to the parent state.'],
                ['Copy', 'path[:]', 'Save a snapshot.'],
                ['Start index', 'No reorder', 'Combinations, not permutations.'],
                ['Prune', 'Return early', 'A dead rule, such as a column already used.'],
            ],
            'e1' => ['Example 1 — every subset', 'Empty set included. Three numbers, eight subsets.', <<<'PY'
def subsets(nums):
    out = []
    path = []
    def explore(start):
        out.append(path[:])
        for i in range(start, len(nums)):
            path.append(nums[i])
            explore(i + 1)
            path.pop()
    explore(0)
    return out

print(subsets([1, 2]))
print('count', len(subsets([1, 2, 3])))
PY],
            'e2' => ['Example 2 — forgetting the copy', 'Every saved “subset” is the same list object, which ends empty.', <<<'PY'
def buggy(nums):
    out = []
    path = []
    def explore(start):
        out.append(path)
        for i in range(start, len(nums)):
            path.append(nums[i])
            explore(i + 1)
            path.pop()
    explore(0)
    return out

print(buggy([1, 2]))
PY],
            'micro' => [
                '8 subsets of 3 items is 2³, including empty. If your count is 7, you skipped the append-before-the-loop.',
                'pop() undoes append(). If you append two things, pop twice.',
                'N-queens adds a prune: a column or diagonal already used returns before going deeper. The undo still happens for the queen you placed.',
            ],
            'quiz' => [
                $q('Why save path[:] instead of path?', 'You need a snapshot; the list will change later', 'Slicing sorts the path', 'path cannot be appended', 'Copies are required by the syntax of def'),
                $q('What does the pop do?', 'Undoes the choice for the next branch', 'Sorts the subset', 'Ends the whole search immediately', 'Copies the output'),
                $q('How many subsets does [1, 2, 3] have, including empty?', '8', '6', '3', '7'),
            ],
            'practice' => 'List every subset of [1] including empty, by hand, then run subsets([1]).',
        ]),
        $L([
            'section' => '9. The project',
            'title' => 'Project: a campus route finder',
            'keywords' => 'project, BFS, Dijkstra, campus map, path reconstruction',
            'lead' => 'This chapter uses the pieces together. The campus is an undirected weighted graph. One function answers “fewest streets” with BFS. Another answers “least walking cost” with Dijkstra. Both rebuild the path. A missing building is None, not a crash and not a zero cost.',
            'heading' => 'Two questions, two algorithms',
            'para' => 'The map stores each street once in a list of triples, then add_undirected writes both directions with the same cost. Fewest streets ignores the cost and counts hops. Least cost uses the weights and refuses to treat a two-street shortcut as worse just because it is two streets. You print both so the difference is visible.',
            'bullets' => [
                'Build the graph in one place. The searches should not retype the streets.',
                'Seen or best-cost must exist or a cycle in the undirected map will loop. Undirected means every street is a cycle of two if you are careless, so mark seen.',
                'Reverse the parent walk. Forgetting reverse prints the path backwards.',
                'If someone later adds a negative cost, Dijkstra’s answer is no longer trustworthy. The function should assume the map’s costs stay non-negative.',
            ],
            'rows' => [
                ['Triple', '(a, b, cost)', 'One undirected street.'],
                ['Both ways', 'Stored twice', 'Walking back is allowed.'],
                ['BFS', 'Fewest streets', 'Cost is ignored on purpose.'],
                ['Dijkstra', 'Least cost', 'Non-negative costs.'],
                ['Parent', 'Previous building', 'Walk back, then reverse.'],
                ['Missing', 'None', 'Not 0 and not an exception you hide.'],
            ],
            'e1' => ['Example 1 — hops and cost disagree', 'Gate to cafe is one expensive street, or two cheap ones. Fewest hops prefers the expensive street. Least cost prefers the library.', <<<'PY'
from collections import deque
import heapq

def build(streets):
    graph = {}
    for a, b, cost in streets:
        graph.setdefault(a, []).append((b, cost))
        graph.setdefault(b, []).append((a, cost))
    return graph

def fewest_streets(graph, start, goal):
    parent = {start: None}
    line = deque([start])
    while line:
        node = line.popleft()
        if node == goal:
            break
        for nxt, _cost in graph.get(node, []):
            if nxt not in parent:
                parent[nxt] = node
                line.append(nxt)
    return rebuild(parent, goal)

def least_cost(graph, start, goal):
    best = {start: 0}
    parent = {start: None}
    heap = [(0, start)]
    while heap:
        cost, node = heapq.heappop(heap)
        if cost != best.get(node):
            continue
        if node == goal:
            break
        for nxt, weight in graph.get(node, []):
            new_cost = cost + weight
            if new_cost < best.get(nxt, 10**9):
                best[nxt] = new_cost
                parent[nxt] = node
                heapq.heappush(heap, (new_cost, nxt))
    return rebuild(parent, goal), best.get(goal)

def rebuild(parent, goal):
    if goal not in parent:
        return None
    out = []
    node = goal
    while node is not None:
        out.append(node)
        node = parent[node]
    out.reverse()
    return out

campus = build([
    ('gate', 'cafe', 9),
    ('gate', 'library', 2),
    ('library', 'cafe', 2),
])
print('hops', fewest_streets(campus, 'gate', 'cafe'))
print('cost', least_cost(campus, 'gate', 'cafe'))
PY],
            'e2' => ['Example 2 — a building that is not on the map', 'Dorm was never added. Both answers are None. least_cost returns the pair (None, None) because there is no best cost either.', <<<'PY'
def rebuild(parent, goal):
    if goal not in parent:
        return None
    return ['should not run']

parent = {'gate': None}
print(rebuild(parent, 'dorm'))
PY],
            'micro' => [
                'graph.get(node, []) survives a node that only appeared as a neighbour and, more importantly, a typo. An unknown start still needs a parent entry, which the functions add.',
                'The hop path is gate, cafe. The cheap path is gate, library, cafe with cost 4. If your print disagrees, the weights were stored in one direction only.',
                'This is the master check: you can say which algorithm matches which question. Fewest streets is BFS. Least cost with positive prices is Dijkstra.',
            ],
            'quiz' => [
                $q('Which algorithm answers fewest streets?', 'BFS', 'Dijkstra', 'Insertion sort', 'A trie'),
                $q('Which algorithm answers least positive cost?', 'Dijkstra', 'Plain BFS', 'Union-find alone', 'Merge sort'),
                $q('What do you return for a building that was never added?', 'None', '0', 'The start building', 'An empty string as a distance'),
            ],
            'practice' => 'Add a dorm connected only to the lab with cost 1, and connect the lab to the library with cost 1. Predict the least-cost path from the gate to the dorm.',
        ]),
    ];
}
