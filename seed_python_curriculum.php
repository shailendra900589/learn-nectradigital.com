<?php
declare(strict_types=1);
require_once __DIR__ . '/seed_content.php';

$pyId = $courseIds['python-complete-tutorials'];
echo "Seeding Python Chapters for Course ID: $pyId\n";

$pythonChapters = [
    [
        'order' => 1,
        'section' => 'Python Basics',
        'name' => 'Python Introduction',
        'content' => '
<p class="lead text-lg font-medium text-slate-700"><strong>Python</strong> is a popular high-level, general-purpose programming language created by Guido van Rossum and released in 1991. It emphasizes code readability with its notable use of significant whitespace.</p>

<h3>Why Learn Python?</h3>
<ul>
    <li><strong>Simple Syntax</strong>: Python has a syntax that allows developers to write programs with fewer lines of code than other languages.</li>
    <li><strong>Interpreted</strong>: Python runs on an interpreter system, meaning code can be executed as soon as it is written.</li>
    <li><strong>Multi-paradigm</strong>: Supports procedural, object-oriented, and functional programming.</li>
    <li><strong>Massive Ecosystem</strong>: Dominates Artificial Intelligence, Machine Learning, Data Science, Web Development (Django, FastAPI, Flask), and Automation.</li>
</ul>

<div class="w3-example-box">
    <div class="w3-example-title">Python Example: Hello World</div>
    <pre><code class="language-python"># Outputting a welcome message in Python
print("Hello, World! Welcome to Python.")</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Hello, World! Welcome to Python.
    </div>
</div>

<div class="tutorial-tip">
    <strong>Python Philosophy (The Zen of Python):</strong> Beautiful is better than ugly. Explicit is better than implicit. Simple is better than complex. Readability counts.
</div>
',
        'quizzes' => [
            [
                'question' => 'Who created the Python programming language?',
                'answer' => 'Guido van Rossum',
                'options' => ['Guido van Rossum', 'Rasmus Lerdorf', 'Brendan Eich', 'James Gosling']
            ],
            [
                'question' => 'Which built-in function outputs text to the screen in Python 3?',
                'answer' => 'print()',
                'options' => ['print()', 'echo()', 'System.out.println()', 'console.log()']
            ]
        ]
    ],
    [
        'order' => 2,
        'section' => 'Python Basics',
        'name' => 'Python Getting Started & Setup',
        'content' => '
<p>To run Python on your computer, check whether Python is already installed by running in your terminal:</p>
<pre><code>python --version</code></pre>
<p>If not installed, download the latest version from python.org and ensure the checkbox <strong>"Add python.exe to PATH"</strong> is selected during setup on Windows.</p>

<h3>Interactive Shell vs Python Script</h3>
<p>You can execute Python in two primary ways:</p>
<ol>
    <li><strong>REPL / Interactive Mode</strong>: Type <code>python</code> in your terminal and execute commands line-by-line.</li>
    <li><strong>Script Mode</strong>: Save instructions into a file with a <code>.py</code> extension (e.g. <code>app.py</code>) and execute it: <code>python app.py</code>.</li>
</ol>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Simple Calculation Script</div>
    <pre><code class="language-python"># Simple calculations in Python
width = 20
height = 5 * 9
area = width * height
print("Calculated Area:", area)</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Calculated Area: 900
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'What is the standard file extension for Python script files?',
                'answer' => '.py',
                'options' => ['.py', '.python', '.pt', '.pyc']
            ]
        ]
    ],
    [
        'order' => 3,
        'section' => 'Python Basics',
        'name' => 'Python Syntax & Indentation Rules',
        'content' => '
<p>Unlike languages that use curly brackets <code>{}</code> to delimit code blocks, Python uses <strong>indentation</strong> (whitespace) to define block scope and hierarchy.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Python Indentation & Comments</div>
    <pre><code class="language-python"># This is a single line comment
"""
This is a multi-line docstring
used for documentation.
"""
score = 95

if score > 80:
    print("Indented block: High score achieved!")
    print("Still inside the if-block")

print("Outside the block")</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Indented block: High score achieved!
Still inside the if-block
Outside the block
    </div>
</div>

<div class="tutorial-warning">
    <strong>Indentation Error:</strong> You must use the same number of spaces in the same block of code (the standard convention is 4 spaces). Mixing tabs and spaces will result in an <code>IndentationError</code>.
</div>
',
        'quizzes' => [
            [
                'question' => 'What does Python use to indicate a block of code?',
                'answer' => 'Indentation (whitespace)',
                'options' => ['Indentation (whitespace)', 'Curly braces {}', 'Parentheses ()', 'Semicolons ;']
            ],
            [
                'question' => 'Which character starts a single-line comment in Python?',
                'answer' => '#',
                'options' => ['#', '//', '/*', '--']
            ]
        ]
    ],
    [
        'order' => 4,
        'section' => 'Python Basics',
        'name' => 'Python Variables & Type Casting',
        'content' => '
<p>Variables are containers for storing data values. A variable is created the moment you first assign a value to it.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Variables, Multiple Assignment, and Casting</div>
    <pre><code class="language-python"># Dynamic typing and multiple assignment
name, role, experience = "Devika", "Backend Developer", 5

# Type casting
str_exp = str(experience)
pi_int = int(3.14159)

print(name, "is a", role)
print("Experience as string:", type(str_exp))
print("Float cast to int:", pi_int)</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Devika is a Backend Developer
Experience as string: &lt;class \'str\'&gt;
Float cast to int: 3
    </div>
</div>

<div class="tutorial-tip">
    <strong>Variable Naming Convention:</strong> In Python, variable names should be lowercase with words separated by underscores (snake_case), e.g. <code>user_logged_in</code>, <code>max_attempts</code>.
</div>
',
        'quizzes' => [
            [
                'question' => 'Which function is used to determine the data type of an object in Python?',
                'answer' => 'type()',
                'options' => ['type()', 'typeof()', 'get_type()', 'is_type()']
            ],
            [
                'question' => 'What will int(7.89) evaluate to in Python?',
                'answer' => '7',
                'options' => ['7', '8', '7.89', 'Error']
            ]
        ]
    ],
    [
        'order' => 5,
        'section' => 'Python Basics',
        'name' => 'Python Data Types',
        'content' => '
<p>Python has the following built-in data types categorized by category:</p>

<ul>
    <li><strong>Text Type</strong>: <code>str</code></li>
    <li><strong>Numeric Types</strong>: <code>int</code>, <code>float</code>, <code>complex</code></li>
    <li><strong>Sequence Types</strong>: <code>list</code>, <code>tuple</code>, <code>range</code></li>
    <li><strong>Mapping Type</strong>: <code>dict</code></li>
    <li><strong>Set Types</strong>: <code>set</code>, <code>frozenset</code></li>
    <li><strong>Boolean Type</strong>: <code>bool</code></li>
    <li><strong>Binary Types</strong>: <code>bytes</code>, <code>bytearray</code>, <code>memoryview</code></li>
    <li><strong>None Type</strong>: <code>NoneType</code> (represented by <code>None</code>)</li>
</ul>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Core Python Data Types</div>
    <pre><code class="language-python">x_str = "Antigravity"
x_int = 42
x_float = 20.5
x_complex = 1j
x_list = ["apple", "banana", "cherry"]
x_tuple = ("apple", "banana", "cherry")
x_dict = {"name": "John", "age": 36}
x_bool = True

print(type(x_str), type(x_list), type(x_dict))</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
&lt;class \'str\'&gt; &lt;class \'list\'&gt; &lt;class \'dict\'&gt;
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which of the following is an immutable sequence type in Python?',
                'answer' => 'tuple',
                'options' => ['tuple', 'list', 'dict', 'set']
            ]
        ]
    ],
    [
        'order' => 6,
        'section' => 'Python Basics',
        'name' => 'Python Numbers & Math',
        'content' => '
<p>There are three numeric types in Python: <code>int</code>, <code>float</code>, and <code>complex</code>. Integers in Python 3 have unlimited precision.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Math and Random Modules</div>
    <pre><code class="language-python">import math
import random

print("Power (2^8):", pow(2, 8))
print("Square root of 81:", math.sqrt(81))
print("Ceil of 4.2:", math.ceil(4.2))
print("Floor of 4.8:", math.floor(4.8))
print("Random integer (10-50):", random.randint(10, 50))</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Power (2^8): 256
Square root of 81: 9.0
Ceil of 4.2: 5
Floor of 4.8: 4
Random integer (10-50): 28
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which Python standard module provides advanced mathematical functions like sqrt and ceil?',
                'answer' => 'math',
                'options' => ['math', 'numbers', 'calc', 'cmath']
            ]
        ]
    ],
    [
        'order' => 7,
        'section' => 'Python Basics',
        'name' => 'Python Strings & Slicing',
        'content' => '
<p>Strings in Python are arrays of bytes representing unicode characters. Python does not have a character data type, a single character is simply a string with a length of 1.</p>

<h3>String Slicing</h3>
<p>You can return a range of characters by using the slice syntax: <code>string[start:stop:step]</code>.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Slicing & Modern f-Strings</div>
    <pre><code class="language-python">phrase = "Python Programming"

# Slicing
print("First 6 characters:", phrase[0:6])
print("From index 7 to end:", phrase[7:])
print("Reversed string:", phrase[::-1])

# Formatted string literals (f-strings)
language = "Python"
version = 3.12
print(f"I am learning {language} version {version}!")</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
First 6 characters: Python
From index 7 to end: Programming
Reversed string: gnimmargorP nohtyP
I am learning Python version 3.12!
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'How can you reverse a string "s" in Python using slicing?',
                'answer' => 's[::-1]',
                'options' => ['s[::-1]', 's.reverse()', 's[1:0:-1]', 's[-1:0]']
            ],
            [
                'question' => 'What prefix is used before quote marks to create an f-string in Python?',
                'answer' => 'f or F',
                'options' => ['f or F', 's or S', 'r or R', '$']
            ]
        ]
    ],
    [
        'order' => 8,
        'section' => 'Python Basics',
        'name' => 'Python Booleans & Operators',
        'content' => '
<p>Booleans represent one of two values: <code>True</code> or <code>False</code>.</p>

<h3>Falsy Values in Python</h3>
<p>Almost any value is evaluated to <code>True</code> if it has some sort of content. The only falsy values are: <code>False</code>, <code>None</code>, <code>0</code>, <code>""</code> (empty string), <code>()</code>, <code>[]</code>, and <code>{}</code>.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Membership and Identity Operators</div>
    <pre><code class="language-python"># Membership operators: in, not in
allowed_roles = ["admin", "editor", "author"]
user_role = "admin"
print("Authorized:", user_role in allowed_roles)

# Identity operators: is, is not
a = [1, 2, 3]
b = [1, 2, 3]
c = a
print("a == b (same value):", a == b)
print("a is b (same object memory):", a is b)
print("a is c (same object memory):", a is c)</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Authorized: True
a == b (same value): True
a is b (same object memory): False
a is c (same object memory): True
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'What is the difference between == and "is" in Python?',
                'answer' => '== tests equality of values, while "is" tests object identity in memory',
                'options' => [
                    '== tests equality of values, while "is" tests object identity in memory',
                    'They are completely synonymous',
                    '== is only for numbers',
                    '"is" checks variable types only'
                ]
            ]
        ]
    ],
    [
        'order' => 9,
        'section' => 'Python Data Structures',
        'name' => 'Python Lists & List Comprehensions',
        'content' => '
<p>Lists are used to store multiple items in a single variable. Lists are ordered, changeable (mutable), and allow duplicate values.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: List Operations and Comprehensions</div>
    <pre><code class="language-python">fruits = ["apple", "banana", "cherry"]
fruits.append("orange")
fruits.remove("banana")

print("Updated list:", fruits)

# List Comprehension: concise syntax to create new lists
numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
even_squares = [x**2 for x in numbers if x % 2 == 0]
print("Even squares:", even_squares)</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Updated list: [\'apple\', \'cherry\', \'orange\']
Even squares: [4, 16, 36, 64, 100]
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which method adds an element to the end of a Python list?',
                'answer' => 'append()',
                'options' => ['append()', 'add()', 'push()', 'insert_last()']
            ],
            [
                'question' => 'Are Python lists mutable or immutable?',
                'answer' => 'Mutable (can be modified after creation)',
                'options' => ['Mutable (can be modified after creation)', 'Immutable', 'Constant', 'Static']
            ]
        ]
    ],
    [
        'order' => 10,
        'section' => 'Python Data Structures',
        'name' => 'Python Tuples',
        'content' => '
<p>Tuples are used to store multiple items in a single variable. A tuple is a collection which is <strong>ordered</strong> and <strong>unchangeable (immutable)</strong>.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Tuple Packing and Unpacking</div>
    <pre><code class="language-python"># Packing a tuple
coordinates = (37.7749, -122.4194, "San Francisco")

# Unpacking a tuple
lat, lon, city = coordinates
print(f"Latitude: {lat}, Longitude: {lon}, City: {city}")</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Latitude: 37.7749, Longitude: -122.4194, City: San Francisco
    </div>
</div>

<div class="tutorial-note">
    <strong>When to use Tuples instead of Lists?</strong> Use tuples when you want to protect data against accidental modification (write-protection) or when returning multiple values from a function. Tuples are also faster to iterate through than lists.
</div>
',
        'quizzes' => [
            [
                'question' => 'Can you change, add, or remove items from a tuple after it has been created?',
                'answer' => 'No, tuples are immutable',
                'options' => ['No, tuples are immutable', 'Yes, using the tuple.modify() method', 'Yes, only using append()', 'Only if declared global']
            ]
        ]
    ],
    [
        'order' => 11,
        'section' => 'Python Data Structures',
        'name' => 'Python Sets',
        'content' => '
<p>A set is a collection which is <strong>unordered</strong>, <strong>unindexed</strong>, and <strong>does not allow duplicate members</strong>.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Mathematical Set Operations</div>
    <pre><code class="language-python">frontend = {"HTML", "CSS", "JavaScript", "React"}
backend = {"Python", "PHP", "NodeJS", "JavaScript"}

# Union (all unique items)
print("All techs:", frontend | backend)

# Intersection (common items)
print("Common techs:", frontend & backend)

# Difference (items in frontend but not backend)
print("Only frontend:", frontend - backend)</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
All techs: {\'HTML\', \'CSS\', \'JavaScript\', \'React\', \'Python\', \'PHP\', \'NodeJS\'}
Common techs: {\'JavaScript\'}
Only frontend: {\'HTML\', \'CSS\', \'React\'}
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'What operator performs set intersection in Python?',
                'answer' => '&',
                'options' => ['&', '|', '^', '-']
            ],
            [
                'question' => 'Do sets allow duplicate values?',
                'answer' => 'No, duplicate values are automatically ignored',
                'options' => ['No, duplicate values are automatically ignored', 'Yes', 'Only strings', 'Throws an error']
            ]
        ]
    ],
    [
        'order' => 12,
        'section' => 'Python Data Structures',
        'name' => 'Python Dictionaries',
        'content' => '
<p>Dictionaries are used to store data values in <code>key: value</code> pairs. A dictionary is a collection which is ordered (as of Python 3.7), changeable, and does not allow duplicate keys.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Dictionary Operations</div>
    <pre><code class="language-python">user = {
    "id": 101,
    "name": "Ananya Sharma",
    "email": "ananya@example.com",
    "is_active": True
}

# Safe lookup with .get()
location = user.get("location", "Not specified")
print("Location:", location)

# Iterating keys and values
for key, value in user.items():
    print(f"{key} => {value}")</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Location: Not specified
id => 101
name => Ananya Sharma
email => ananya@example.com
is_active => True
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which dictionary method safely retrieves a value for a key without raising a KeyError if the key does not exist?',
                'answer' => 'get()',
                'options' => ['get()', 'fetch()', 'find()', 'lookup()']
            ]
        ]
    ],
    [
        'order' => 13,
        'section' => 'Python Flow Control & Functions',
        'name' => 'Python If...Elif...Else Conditions',
        'content' => '
<p>Python relies on standard mathematical conditions along with <code>if</code>, <code>elif</code>, and <code>else</code> keywords to control program execution flow.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Conditional Logic & Shorthand If-Else</div>
    <pre><code class="language-python">temperature = 28

if temperature > 35:
    status = "Very Hot"
elif temperature >= 20:
    status = "Pleasant / Warm"
else:
    status = "Cold"

# Shorthand Ternary
message = "Carry umbrella" if temperature > 30 else "Normal day"

print("Weather Status:", status)
print("Advice:", message)</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Weather Status: Pleasant / Warm
Advice: Normal day
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'What is the keyword for "else if" in Python?',
                'answer' => 'elif',
                'options' => ['elif', 'elseif', 'else if', 'when']
            ]
        ]
    ],
    [
        'order' => 14,
        'section' => 'Python Flow Control & Functions',
        'name' => 'Python While Loops',
        'content' => '
<p>With the <code>while</code> loop we can execute a set of statements as long as a condition is true.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: While Loop with Break and Continue</div>
    <pre><code class="language-python">i = 0
while i < 6:
    i += 1
    if i == 3:
        continue # skip 3
    if i == 5:
        break # stop loop at 5
    print("Iteration:", i)</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Iteration: 1
Iteration: 2
Iteration: 4
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which statement stops the loop immediately even if the while condition is still true?',
                'answer' => 'break',
                'options' => ['break', 'continue', 'pass', 'exit']
            ]
        ]
    ],
    [
        'order' => 15,
        'section' => 'Python Flow Control & Functions',
        'name' => 'Python For Loops & Range',
        'content' => '
<p>A <code>for</code> loop is used for iterating over a sequence (either a list, a tuple, a dictionary, a set, or a string).</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: for Loop with range(start, stop, step)</div>
    <pre><code class="language-python"># Loop numbers from 2 to 10 with step 2
for n in range(2, 11, 2):
    print(f"Even number: {n}")</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Even number: 2
Even number: 4
Even number: 6
Even number: 8
Even number: 10
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'What numbers will range(1, 5) generate?',
                'answer' => '1, 2, 3, 4',
                'options' => ['1, 2, 3, 4', '1, 2, 3, 4, 5', '0, 1, 2, 3, 4', '0, 1, 2, 3, 4, 5']
            ]
        ]
    ],
    [
        'order' => 16,
        'section' => 'Python Flow Control & Functions',
        'name' => 'Python Functions (*args, **kwargs)',
        'content' => '
<p>A function is defined using the <code>def</code> keyword. Python functions support variable-length positional arguments (<code>*args</code>) and arbitrary keyword arguments (<code>**kwargs</code>).</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Functions with *args and **kwargs</div>
    <pre><code class="language-python">def configure_server(host, port=8000, *features, **options):
    print(f"Connecting to {host}:{port}")
    if features:
        print("Enabled features:", ", ".join(features))
    if options:
        print("Options:", options)

configure_server("127.0.0.1", 8000, "SSL", "Gzip", timeout=30, debug=True)</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Connecting to 127.0.0.1:8000
Enabled features: SSL, Gzip
Options: {\'timeout\': 30, \'debug\': True}
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which keyword is used to create a function in Python?',
                'answer' => 'def',
                'options' => ['def', 'function', 'fun', 'define']
            ],
            [
                'question' => 'What data type does **kwargs receive inside a Python function?',
                'answer' => 'A dictionary (dict)',
                'options' => ['A dictionary (dict)', 'A tuple', 'A list', 'A set']
            ]
        ]
    ],
    [
        'order' => 17,
        'section' => 'Python Flow Control & Functions',
        'name' => 'Python Lambda (Anonymous Functions)',
        'content' => '
<p>A lambda function is a small anonymous function. A lambda function can take any number of arguments, but can only have one expression: <code>lambda arguments : expression</code>.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Using Lambda with map() and sorted()</div>
    <pre><code class="language-python"># Squaring numbers with map
nums = [1, 2, 3, 4, 5]
squares = list(map(lambda x: x**2, nums))
print("Squares:", squares)

# Sorting list of dictionaries by key
students = [
    {"name": "Zack", "grade": 88},
    {"name": "Alice", "grade": 98},
    {"name": "Bob", "grade": 75}
]
students.sort(key=lambda s: s["grade"], reverse=True)
print("Top student:", students[0]["name"])</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Squares: [1, 4, 9, 16, 25]
Top student: Alice
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'How many expressions can a lambda function contain in Python?',
                'answer' => 'Only one expression',
                'options' => ['Only one expression', 'Up to 3', 'Unlimited', 'Zero']
            ]
        ]
    ],
    [
        'order' => 18,
        'section' => 'Python OOP & Modules',
        'name' => 'Python OOP - Classes, Objects & __init__',
        'content' => '
<p>Python is an object-oriented programming language. Almost everything in Python is an object, with its properties and methods.</p>

<h3>The __init__() Constructor</h3>
<p>All classes have a function called <code>__init__()</code>, which is always executed when the class is being initiated.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Class with Properties and Methods</div>
    <pre><code class="language-python">class Course:
    def __init__(self, title, lessons, level="Beginner"):
        self.title = title
        self.lessons = lessons
        self.level = level

    def get_summary(self):
        return f"{self.title} ({self.level}) - {self.lessons} lessons available"

course1 = Course("Python Complete Tutorial", 24)
print(course1.get_summary())</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Python Complete Tutorial (Beginner) - 24 lessons available
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'What is the purpose of the "self" parameter in Python class methods?',
                'answer' => 'It references the current instance of the class',
                'options' => [
                    'It references the current instance of the class',
                    'It makes the method static',
                    'It is a mandatory keyword defined in CPython',
                    'It references the parent superclass'
                ]
            ]
        ]
    ],
    [
        'order' => 19,
        'section' => 'Python OOP & Modules',
        'name' => 'Python OOP - Inheritance & Polymorphism',
        'content' => '
<p>Inheritance allows us to define a class that inherits all the methods and properties from another class.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Inheritance with super()</div>
    <pre><code class="language-python">class User:
    def __init__(self, username, email):
        self.username = username
        self.email = email

    def get_role(self):
        return "Standard User"

class Admin(User):
    def __init__(self, username, email, permissions):
        super().__init__(username, email)
        self.permissions = permissions

    def get_role(self):
        return f"Super Administrator ({len(self.permissions)} permissions)"

admin_user = Admin("admin_vikram", "admin@nectradigital.com", ["read", "write", "delete"])
print("User:", admin_user.username)
print("Role:", admin_user.get_role())</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
User: admin_vikram
Role: Super Administrator (3 permissions)
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which built-in function allows a child class to call methods from its parent class?',
                'answer' => 'super()',
                'options' => ['super()', 'parent()', 'base()', 'inherit()']
            ]
        ]
    ],
    [
        'order' => 20,
        'section' => 'Python OOP & Modules',
        'name' => 'Python Modules & Packages',
        'content' => '
<p>Consider a module to be the same as a code library. A file containing a set of functions you want to include in your application.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Importing Built-in Modules</div>
    <pre><code class="language-python">import os
import sys

print("Python Version:", sys.version.split()[0])
print("Current Working Directory:", os.path.basename(os.getcwd()))</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Python Version: 3.x.x
Current Working Directory: learning.nectradigital.com
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which keyword is used to include external or built-in modules in Python?',
                'answer' => 'import',
                'options' => ['import', 'require', 'include', 'use']
            ]
        ]
    ],
    [
        'order' => 21,
        'section' => 'Python Advanced Topics',
        'name' => 'Python Datetime Module',
        'content' => '
<p>A date in Python is not a data type of its own, but we can import a module named <code>datetime</code> to work with the date as well as time.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Formatting Current Date and Time</div>
    <pre><code class="language-python">import datetime

now = datetime.datetime.now()
print("Year:", now.year)
print("Formatted:", now.strftime("%A, %B %d, %Y - %I:%M %p"))</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Year: 2026
Formatted: Friday, November 20, 2026 - 12:45 PM
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which method formats datetime objects into readable strings in Python?',
                'answer' => 'strftime()',
                'options' => ['strftime()', 'strptime()', 'format_date()', 'to_string()']
            ]
        ]
    ],
    [
        'order' => 22,
        'section' => 'Python Advanced Topics',
        'name' => 'Python JSON Handling',
        'content' => '
<p>JSON is syntax for storing and exchanging text data. Python has a built-in package called <code>json</code>, which can be used to work with JSON data.</p>

<ul>
    <li><code>json.loads()</code>: Parse a JSON string into a Python dictionary.</li>
    <li><code>json.dumps()</code>: Convert a Python object into a JSON string.</li>
</ul>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Serializing and Deserializing JSON</div>
    <pre><code class="language-python">import json

# Python Dictionary to JSON String
profile = {"name": "Karan", "courses_completed": 12, "certified": True}
json_str = json.dumps(profile, indent=2)
print("Serialized JSON:\n", json_str)

# JSON String back to Python Object
parsed = json.loads(json_str)
print("Student name from parsed JSON:", parsed["name"])</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Serialized JSON:
 {
  "name": "Karan",
  "courses_completed": 12,
  "certified": true
}
Student name from parsed JSON: Karan
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which function converts a Python dictionary to a JSON formatted string?',
                'answer' => 'json.dumps()',
                'options' => ['json.dumps()', 'json.loads()', 'json.encode()', 'json.stringify()']
            ]
        ]
    ],
    [
        'order' => 23,
        'section' => 'Python Advanced Topics',
        'name' => 'Python Regular Expressions (re)',
        'content' => '
<p>A Regular Expression (RegEx) is a sequence of characters that forms a search pattern. Python has a built-in module called <code>re</code> to work with regular expressions.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Pattern Matching with re.findall()</div>
    <pre><code class="language-python">import re

text = "Contact support@nectradigital.com or billing@nectradigital.com for queries."
emails = re.findall(r"[\w\.-]+@[\w\.-]+", text)

print("Found email addresses:", emails)</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Found email addresses: [\'support@nectradigital.com\', \'billing@nectradigital.com\']
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which built-in Python module handles Regular Expressions?',
                'answer' => 're',
                'options' => ['re', 'regex', 'regexp', 'pattern']
            ]
        ]
    ],
    [
        'order' => 24,
        'section' => 'Python Advanced Topics',
        'name' => 'Python Exception Handling (Try, Except, Finally)',
        'content' => '
<p>When an error occurs, or exception as we call it, Python will normally stop and generate an error message. These exceptions can be handled using the <code>try</code> statement.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Robust Exception Handling</div>
    <pre><code class="language-python">def safe_divide(a, b):
    try:
        result = a / b
    except ZeroDivisionError:
        return "Error: Cannot divide by zero!"
    except TypeError:
        return "Error: Both arguments must be numeric!"
    else:
        return f"Calculation succeeded: {result}"
    finally:
        print("Execution of safe_divide completed.")

print(safe_divide(10, 2))
print(safe_divide(10, 0))</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Execution of safe_divide completed.
Calculation succeeded: 5.0
Execution of safe_divide completed.
Error: Cannot divide by zero!
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which block in a try-except structure is ALWAYS executed regardless of whether an exception occurred?',
                'answer' => 'finally',
                'options' => ['finally', 'else', 'always', 'catch']
            ]
        ]
    ]
];

foreach ($pythonChapters as $chap) {
    upsertChapter(
        $pdo,
        $pyId,
        $chap['order'],
        $chap['section'],
        $chap['name'],
        $chap['content'],
        $chap['quizzes']
    );
    echo "  [OK] Chapter {$chap['order']}: {$chap['name']}\n";
}

echo "Python Curriculum seeded successfully!\n";
