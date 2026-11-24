<?php
declare(strict_types=1);
require_once __DIR__ . '/seed_content.php';

$phpId = $courseIds['php-tutorials'];
echo "Seeding PHP Chapters for Course ID: $phpId\n";

$phpChapters = [
    [
        'order' => 1,
        'section' => 'PHP Basics',
        'name' => 'PHP Introduction',
        'content' => '
<p class="lead text-lg font-medium text-slate-700"><strong>PHP</strong> (recursive acronym for <em>PHP: Hypertext Preprocessor</em>) is a widely-used open-source, general-purpose scripting language that is especially suited for web development and can be embedded into HTML.</p>

<h3>What You Can Do With PHP</h3>
<ul>
    <li>Generate dynamic page content in real-time.</li>
    <li>Create, open, read, write, delete, and close files on the server.</li>
    <li>Collect, validate, and sanitize form data from users.</li>
    <li>Send and receive HTTP cookies and manage user sessions.</li>
    <li>Add, delete, and modify data inside databases like MySQL, PostgreSQL, and SQLite.</li>
    <li>Control user-access, restrict page permissions, and encrypt sensitive data.</li>
</ul>

<div class="w3-example-box">
    <div class="w3-example-title">PHP Example: Your First Script</div>
    <pre><code class="language-php">&lt;?php
// A simple PHP script to output text
echo "Hello, World! Welcome to PHP Tutorials.";
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Hello, World! Welcome to PHP Tutorials.
    </div>
</div>

<div class="tutorial-note">
    <strong>Why PHP?</strong> PHP runs on almost every major operating system (Windows, Linux, macOS, Unix) and is compatible with almost all modern web servers (Apache, Nginx, IIS). Over 75% of all web servers across the internet run PHP, powering platforms like WordPress, Wikipedia, and Facebook.
</div>

<h3>How PHP Works on the Web Server</h3>
<p>When a browser requests a PHP file, the web server executes the PHP code on the server, interprets the logic, queries databases if needed, and sends back plain HTML, CSS, or JSON to the client browser.</p>
',
        'quizzes' => [
            [
                'question' => 'What does the acronym PHP stand for?',
                'answer' => 'PHP: Hypertext Preprocessor',
                'options' => ['PHP: Hypertext Preprocessor', 'Personal Home Page', 'Private Hypertext Processor', 'Public Host Platform']
            ],
            [
                'question' => 'Where is PHP code executed?',
                'answer' => 'On the web server',
                'options' => ['On the web server', 'In the client browser', 'Inside the operating system kernel', 'Inside the network router']
            ],
            [
                'question' => 'What type of content does a PHP script return to the user browser?',
                'answer' => 'Plain HTML, CSS, or JSON',
                'options' => ['Plain HTML, CSS, or JSON', 'Raw PHP executable bytecode', 'C++ compiled binaries', 'Server memory dumps']
            ]
        ]
    ],
    [
        'order' => 2,
        'section' => 'PHP Basics',
        'name' => 'PHP Installation & Setup',
        'content' => '
<p>To start coding in PHP, you need a local development environment that includes three core tools: a Web Server (like Apache or Nginx), the PHP processor, and a Database (like MySQL or MariaDB).</p>

<h3>All-in-One Local Stacks</h3>
<p>Instead of installing and configuring each component separately, you can download a complete pre-configured bundle:</p>
<ul>
    <li><strong>XAMPP</strong>: Cross-platform (Windows, Mac, Linux) package containing Apache, MariaDB, PHP, and Perl.</li>
    <li><strong>WampServer</strong>: Popular Windows-focused Apache, PHP, and MySQL stack.</li>
    <li><strong>MAMP</strong>: Popular stack for macOS and Windows.</li>
</ul>

<h3>Verifying PHP Installation</h3>
<p>Create a file named <code>info.php</code> in your web root directory (such as <code>htdocs</code> in XAMPP) and write the following code:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: phpinfo() Diagnostics</div>
    <pre><code class="language-php">&lt;?php
// Displays all configuration information about the installed PHP version
phpinfo();
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
PHP Version 8.x.x - System Details, Extensions Loaded, and Environment Directives
    </div>
</div>

<div class="tutorial-tip">
    <strong>Security Tip:</strong> Never leave <code>phpinfo()</code> files active in a public production environment, as it exposes system paths, PHP version details, and environment variables to malicious actors.
</div>
',
        'quizzes' => [
            [
                'question' => 'Which built-in PHP function displays complete diagnostic and environment configuration details?',
                'answer' => 'phpinfo()',
                'options' => ['phpinfo()', 'get_php_config()', 'system_info()', 'display_php()']
            ],
            [
                'question' => 'In XAMPP on Windows, which folder is the default web root directory where your PHP files reside?',
                'answer' => 'htdocs',
                'options' => ['htdocs', 'www_root', 'public_html', 'php_files']
            ],
            [
                'question' => 'What is the default file extension for PHP scripts?',
                'answer' => '.php',
                'options' => ['.php', '.ph', '.phtml', '.script']
            ]
        ]
    ],
    [
        'order' => 3,
        'section' => 'PHP Basics',
        'name' => 'PHP Basic Syntax & Comments',
        'content' => '
<p>A PHP script starts with <code>&lt;?php</code> and ends with <code>?&gt;</code>. Within these tags, you write your PHP logic. Each statement must end with a semicolon (<code>;</code>).</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Basic Syntax with Comments</div>
    <pre><code class="language-php">&lt;?php
// This is a single-line comment using double slashes
# This is also a single-line comment using hash

/*
  This is a multi-line comment block.
  It can span across several lines.
*/

$greeting = "Welcome to modern PHP!";
echo $greeting;
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Welcome to modern PHP!
    </div>
</div>

<h3>PHP Case Sensitivity Rules</h3>
<p>In PHP, keywords (such as <code>if</code>, <code>else</code>, <code>while</code>, <code>echo</code>), classes, and functions are <strong>case-insensitive</strong>. However, all variable names are <strong>case-sensitive</strong>!</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Variable Case Sensitivity</div>
    <pre><code class="language-php">&lt;?php
$color = "blue";
$Color = "red";
$COLOR = "green";

echo "My favorite color is " . $color . "&lt;br&gt;";
echo "Second color is " . $Color . "&lt;br&gt;";
echo "Third color is " . $COLOR;
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
My favorite color is blue<br>
Second color is red<br>
Third color is green
    </div>
</div>

<div class="tutorial-note">
    <strong>Best Practice:</strong> Always end PHP statements with a semicolon (<code>;</code>). If a file contains exclusively PHP code, it is recommended to omit the closing <code>?&gt;</code> tag at the end to avoid accidental trailing whitespaces causing header issues.
</div>
',
        'quizzes' => [
            [
                'question' => 'Are variable names in PHP case-sensitive?',
                'answer' => 'Yes, $car and $CAR are two different variables',
                'options' => ['Yes, $car and $CAR are two different variables', 'No, PHP treats all variables identically', 'Only in command-line interface', 'Only inside class definitions']
            ],
            [
                'question' => 'Which character is required at the end of every PHP statement?',
                'answer' => '; (semicolon)',
                'options' => ['; (semicolon)', ': (colon)', '. (period)', ', (comma)']
            ],
            [
                'question' => 'Which of the following is NOT a valid comment syntax in PHP?',
                'answer' => '<!-- comment -->',
                'options' => ['<!-- comment -->', '// single line', '# single line', '/* multi-line */']
            ]
        ]
    ],
    [
        'order' => 4,
        'section' => 'PHP Basics',
        'name' => 'PHP Variables & Data Scope',
        'content' => '
<p>In PHP, a variable starts with the <code>$</code> sign, followed by the name of the variable. Unlike strictly typed languages like Java or C++, PHP is a <em>loosely typed</em> language, meaning you do not have to declare the data type explicitly.</p>

<h3>Rules for PHP Variables</h3>
<ul>
    <li>A variable name must start with a letter or an underscore (<code>_</code>).</li>
    <li>A variable name cannot start with a number.</li>
    <li>A variable name can only contain alphanumeric characters and underscores (A-z, 0-9, and _).</li>
    <li>Variable names are case-sensitive (<code>$age</code> and <code>$Age</code> are distinct).</li>
</ul>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Variable Declaration and Output</div>
    <pre><code class="language-php">&lt;?php
$framework = "Laravel";
$version = 11;
$isStable = true;

echo "Framework: $framework, Version: $version";
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Framework: Laravel, Version: 11
    </div>
</div>

<h3>Variable Scope in PHP</h3>
<p>PHP has three different variable scopes:</p>
<ol>
    <li><strong>Local</strong>: Variables declared inside a function have a local scope and can only be accessed within that function.</li>
    <li><strong>Global</strong>: Variables declared outside a function have a global scope and can be accessed globally, but must use the <code>global</code> keyword to be read inside functions.</li>
    <li><strong>Static</strong>: When a function completes execution, local variables are deleted. To keep their value across calls, declare them with <code>static</code>.</li>
</ol>

<div class="w3-example-box">
    <div class="w3-example-title">Example: The static Keyword</div>
    <pre><code class="language-php">&lt;?php
function trackVisits() {
    static $count = 0;
    $count++;
    echo "Visit count: $count\n";
}

trackVisits();
trackVisits();
trackVisits();
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Visit count: 1
Visit count: 2
Visit count: 3
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'What symbol must all PHP variables begin with?',
                'answer' => '$',
                'options' => ['$', '@', '&', '%']
            ],
            [
                'question' => 'How can you access a global variable from inside a local function scope in PHP?',
                'answer' => 'Using the global keyword or $GLOBALS array',
                'options' => ['Using the global keyword or $GLOBALS array', 'Using the static keyword', 'Using the public modifier', 'Global variables are automatically inherited']
            ],
            [
                'question' => 'What does the static keyword do when applied to a local function variable?',
                'answer' => 'It preserves its value between successive function calls',
                'options' => ['It preserves its value between successive function calls', 'It makes the variable immutable/constant', 'It shares the variable across all scripts', 'It deletes the variable immediately']
            ]
        ]
    ],
    [
        'order' => 5,
        'section' => 'PHP Basics',
        'name' => 'PHP Echo and Print Statements',
        'content' => '
<p>In PHP, there are two basic ways to get output: <code>echo</code> and <code>print</code>. Both are language constructs (not functions), meaning parentheses are optional.</p>

<h3>Differences Between echo and print</h3>
<table class="w-full text-sm">
    <thead>
        <tr>
            <th>Feature</th>
            <th>echo</th>
            <th>print</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Return Value</strong></td>
            <td>Has no return value (void).</td>
            <td>Always returns 1, so it can be used in expressions.</td>
        </tr>
        <tr>
            <td><strong>Parameters</strong></td>
            <td>Can take multiple parameters (separated by commas).</td>
            <td>Takes only one argument.</td>
        </tr>
        <tr>
            <td><strong>Speed</strong></td>
            <td>Marginally faster than print.</td>
            <td>Slightly slower than echo.</td>
        </tr>
    </tbody>
</table>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Using echo and print</div>
    <pre><code class="language-php">&lt;?php
$name = "Alex";
$score = 98;

// echo with multiple parameters
echo "Student: ", $name, " scored ", $score, " points.\n";

// print usage
print("Grade: Distinction\n");
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Student: Alex scored 98 points.
Grade: Distinction
    </div>
</div>

<div class="tutorial-tip">
    <strong>Pro Tip:</strong> In double-quoted strings (<code>"..."</code>), PHP parses variables directly (string interpolation). In single-quoted strings (<code>\'...\'</code>), characters are treated literally.
</div>
',
        'quizzes' => [
            [
                'question' => 'What value does print always return in PHP?',
                'answer' => '1',
                'options' => ['1', '0', 'true', 'void (no return value)']
            ],
            [
                'question' => 'Which construct allows passing multiple comma-separated arguments?',
                'answer' => 'echo',
                'options' => ['echo', 'print', 'printf', 'vprintf']
            ]
        ]
    ],
    [
        'order' => 6,
        'section' => 'PHP Basics',
        'name' => 'PHP Data Types',
        'content' => '
<p>Variables can store data of different types, and different data types can do different things. PHP supports the following data types:</p>

<ul>
    <li><strong>String</strong>: A sequence of characters, like <code>"Hello"</code>.</li>
    <li><strong>Integer</strong>: Non-decimal whole numbers between -2,147,483,648 and 2,147,483,647 (on 32-bit systems).</li>
    <li><strong>Float (Double)</strong>: Numbers with a decimal point or in exponential form (e.g., <code>10.365</code>).</li>
    <li><strong>Boolean</strong>: Represents two possible states: <code>true</code> or <code>false</code>.</li>
    <li><strong>Array</strong>: Stores multiple values in one single variable.</li>
    <li><strong>Object</strong>: Instances of programmer-defined classes.</li>
    <li><strong>NULL</strong>: A special variable that has no value assigned to it.</li>
    <li><strong>Resource</strong>: Special variables that hold references to external resources (such as database connections or file handles).</li>
</ul>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Inspecting Data Types with var_dump()</div>
    <pre><code class="language-php">&lt;?php
$name = "Nectra Digital";
$users = 15000;
$rating = 4.95;
$isActive = true;
$technologies = ["PHP", "Python", "SQL"];

var_dump($name);
var_dump($users);
var_dump($rating);
var_dump($isActive);
var_dump($technologies);
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
string(14) "Nectra Digital"
int(15000)
float(4.95)
bool(true)
array(3) {
  [0]=> string(3) "PHP"
  [1]=> string(6) "Python"
  [2]=> string(3) "SQL"
}
    </div>
</div>

<div class="tutorial-note">
    <strong>The var_dump() Function:</strong> <code>var_dump()</code> returns the structured representation of a variable, including its data type and value. It is the single most valuable debugging tool in PHP.
</div>
',
        'quizzes' => [
            [
                'question' => 'Which PHP function returns both the data type and length/value of a variable?',
                'answer' => 'var_dump()',
                'options' => ['var_dump()', 'print_r()', 'gettype()', 'typeof()']
            ],
            [
                'question' => 'What is the data type of a variable that has been assigned the value NULL?',
                'answer' => 'NULL',
                'options' => ['NULL', 'undefined', 'void', 'empty']
            ]
        ]
    ],
    [
        'order' => 7,
        'section' => 'PHP Basics',
        'name' => 'PHP Strings & String Functions',
        'content' => '
<p>A string is a sequence of characters, such as "Hello World!". Strings can be enclosed in single quotes or double quotes.</p>

<h3>Common Built-in String Functions</h3>
<ul>
    <li><code>strlen($str)</code>: Returns the length of a string in bytes.</li>
    <li><code>str_word_count($str)</code>: Counts the number of words in a string.</li>
    <li><code>strrev($str)</code>: Reverses a string.</li>
    <li><code>strpos($haystack, $needle)</code>: Searches for a specific text within a string and returns the first character position (0-indexed).</li>
    <li><code>str_replace($search, $replace, $subject)</code>: Replaces all occurrences of the search text with the replacement text.</li>
    <li><code>strtolower($str)</code> and <code>strtoupper($str)</code>: Converts string case.</li>
    <li><code>trim($str)</code>: Strips whitespace from the beginning and end of a string.</li>
</ul>

<div class="w3-example-box">
    <div class="w3-example-title">Example: String Functions in Action</div>
    <pre><code class="language-php">&lt;?php
$sentence = "PHP is fast and powerful!";

echo "Length: " . strlen($sentence) . "\n";
echo "Word count: " . str_word_count($sentence) . "\n";
echo "Position of fast: " . strpos($sentence, "fast") . "\n";
echo "Replaced: " . str_replace("fast", "robust", $sentence) . "\n";
echo "Upper case: " . strtoupper($sentence) . "\n";
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Length: 25
Word count: 5
Position of fast: 7
Replaced: PHP is robust and powerful!
Upper case: PHP IS FAST AND POWERFUL!
    </div>
</div>

<div class="tutorial-tip">
    <strong>String Concatenation:</strong> In PHP, the dot operator (<code>.</code>) is used to concatenate (join) two strings together. The concatenation assignment operator (<code>.=</code>) appends an argument to an existing string.
</div>
',
        'quizzes' => [
            [
                'question' => 'Which operator is used to concatenate strings in PHP?',
                'answer' => '. (dot)',
                'options' => ['. (dot)', '+ (plus)', '& (ampersand)', ', (comma)']
            ],
            [
                'question' => 'What does strpos("Hello World", "World") return?',
                'answer' => '6',
                'options' => ['6', '5', '7', 'true']
            ]
        ]
    ],
    [
        'order' => 8,
        'section' => 'PHP Basics',
        'name' => 'PHP Numbers, Math & Constants',
        'content' => '
<p>PHP provides comprehensive numerical support including integers, floating-point numbers, and number-handling functions.</p>

<h3>Math Functions</h3>
<ul>
    <li><code>pi()</code>: Returns value of PI (approx 3.14159...).</li>
    <li><code>min()</code> and <code>max()</code>: Finds lowest and highest value in a list of arguments.</li>
    <li><code>abs()</code>: Returns absolute (positive) value of a number.</li>
    <li><code>sqrt()</code>: Returns the square root.</li>
    <li><code>round()</code>: Rounds a floating-point number to its nearest integer.</li>
    <li><code>rand(min, max)</code>: Generates a random integer.</li>
</ul>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Math Functions</div>
    <pre><code class="language-php">&lt;?php
echo "Min: " . min(10, 20, 5, 80) . "\n";
echo "Max: " . max(10, 20, 5, 80) . "\n";
echo "Sqrt of 64: " . sqrt(64) . "\n";
echo "Round 4.6: " . round(4.6) . "\n";
echo "Random (1-100): " . rand(1, 100) . "\n";
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Min: 5
Max: 80
Sqrt of 64: 8
Round 4.6: 5
Random (1-100): 42
    </div>
</div>

<h3>PHP Constants</h3>
<p>Constants are like variables, except that once they are defined, they cannot be changed or undefined during execution. Constants are automatically global across the entire script.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Defining Constants</div>
    <pre><code class="language-php">&lt;?php
// Using define()
define("SITE_NAME", "Learn.Nectra");
define("API_TIMEOUT", 30);

// Using const keyword (modern PHP)
const DB_PORT = 3306;

echo SITE_NAME . " connects on port " . DB_PORT;
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Learn.Nectra connects on port 3306
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Do constant names require a leading dollar sign ($) in PHP?',
                'answer' => 'No, constants are written without $',
                'options' => ['No, constants are written without $', 'Yes, all identifiers need $', 'Only if defined with const', 'Only if defined inside functions']
            ],
            [
                'question' => 'Which function generates a random integer between two bounds in PHP?',
                'answer' => 'rand(min, max)',
                'options' => ['rand(min, max)', 'random(min, max)', 'math_rand()', 'generate_int()']
            ]
        ]
    ],
    [
        'order' => 9,
        'section' => 'PHP Basics',
        'name' => 'PHP Operators',
        'content' => '
<p>Operators are used to perform operations on variables and values. PHP groups operators into several categories:</p>

<ul>
    <li><strong>Arithmetic Operators</strong>: <code>+</code>, <code>-</code>, <code>*</code>, <code>/</code>, <code>%</code> (modulus), <code>**</code> (exponentiation).</li>
    <li><strong>Assignment Operators</strong>: <code>=</code>, <code>+=</code>, <code>-=</code>, <code>*=</code>, <code>/=</code>, <code>%=</code>.</li>
    <li><strong>Comparison Operators</strong>:
        <ul>
            <li><code>==</code> (Equal - checks value)</li>
            <li><code>===</code> (Identical - checks value AND data type)</li>
            <li><code>!=</code> or <code>&lt;&gt;</code> (Not equal)</li>
            <li><code>!==</code> (Not identical)</li>
            <li><code>&lt;=&gt;</code> (Spaceship operator - returns -1, 0, or 1)</li>
        </ul>
    </li>
    <li><strong>Logical Operators</strong>: <code>&&</code> (And), <code>||</code> (Or), <code>!</code> (Not).</li>
    <li><strong>Null Coalescing Operator (??)</strong>: Returns its first operand if it exists and is not null; otherwise returns its second operand.</li>
</ul>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Comparison & Null Coalescing</div>
    <pre><code class="language-php">&lt;?php
$a = 100;
$b = "100";

// Loose comparison (values match)
var_dump($a == $b); // true

// Strict comparison (types differ: int vs string)
var_dump($a === $b); // false

// Null Coalescing Operator
$username = $_GET["user"] ?? "Guest";
echo "Current user: " . $username;
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
bool(true)
bool(false)
Current user: Guest
    </div>
</div>

<div class="tutorial-note">
    <strong>Always prefer Strict Comparison (===):</strong> Loose comparison (<code>==</code>) performs type coercion, which can lead to subtle logic bugs. Always use <code>===</code> and <code>!==</code> for reliable comparisons.
</div>
',
        'quizzes' => [
            [
                'question' => 'What is the difference between == and === in PHP?',
                'answer' => '=== compares both value and data type; == only compares value with type coercion',
                'options' => [
                    '=== compares both value and data type; == only compares value with type coercion',
                    '== is strict, === is loose',
                    '=== is only used for strings',
                    'There is no difference in modern PHP'
                ]
            ],
            [
                'question' => 'What does the null coalescing operator ($x ?? $y) evaluate to if $x is null?',
                'answer' => '$y',
                'options' => ['$y', '$x', 'null', 'false']
            ]
        ]
    ],
    [
        'order' => 10,
        'section' => 'PHP Control Structures',
        'name' => 'PHP If...Else...Elseif Statements',
        'content' => '
<p>Conditional statements are used to perform different actions based on different conditions. In PHP we have the following conditional statements:</p>

<ul>
    <li><code>if</code> statement - executes some code if one condition is true.</li>
    <li><code>if...else</code> statement - executes some code if a condition is true and another code if that condition is false.</li>
    <li><code>if...elseif...else</code> statement - executes different codes for more than two conditions.</li>
</ul>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Grade Evaluation System</div>
    <pre><code class="language-php">&lt;?php
$score = 85;

if ($score >= 90) {
    echo "Grade: A+ (Outstanding)";
} elseif ($score >= 80) {
    echo "Grade: A (Very Good)";
} elseif ($score >= 70) {
    echo "Grade: B (Good)";
} elseif ($score >= 50) {
    echo "Grade: C (Pass)";
} else {
    echo "Grade: F (Fail)";
}
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Grade: A (Very Good)
    </div>
</div>

<h3>Shorthand If...Else (Ternary Operator)</h3>
<p>The ternary operator consists of three operands: a condition, a result for true, and a result for false.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Ternary Operator</div>
    <pre><code class="language-php">&lt;?php
$age = 20;
$canVote = ($age >= 18) ? "Eligible to vote" : "Too young to vote";
echo $canVote;
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Eligible to vote
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'What is the correct syntax for checking multiple conditions sequentially?',
                'answer' => 'if ... elseif ... else',
                'options' => ['if ... elseif ... else', 'if ... elif ... else', 'if ... then ... else', 'if ... otherwise']
            ],
            [
                'question' => 'What will ($x > 5) ? "Yes" : "No" return if $x is equal to 4?',
                'answer' => 'No',
                'options' => ['No', 'Yes', 'null', 'Error']
            ]
        ]
    ],
    [
        'order' => 11,
        'section' => 'PHP Control Structures',
        'name' => 'PHP Switch Statement',
        'content' => '
<p>The <code>switch</code> statement is used to perform different actions based on different conditions. It avoids long blocks of <code>if..elseif..else</code> when comparing the same variable against multiple possible values.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Day of the Week Router</div>
    <pre><code class="language-php">&lt;?php
$role = "admin";

switch ($role) {
    case "admin":
        echo "Full system access granted.";
        break;
    case "editor":
        echo "Can edit and publish posts.";
        break;
    case "subscriber":
        echo "Read-only access.";
        break;
    default:
        echo "Unknown role. Access denied.";
        break;
}
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Full system access granted.
    </div>
</div>

<div class="tutorial-warning">
    <strong>Crucial Rule:</strong> Always include the <code>break</code> keyword at the end of each case! If you omit <code>break</code>, PHP will "fall through" and execute subsequent case blocks even if their conditions do not match.
</div>
',
        'quizzes' => [
            [
                'question' => 'What happens if a case statement in a switch block does not have a break keyword?',
                'answer' => 'Execution continues into the next case automatically (fall-through)',
                'options' => [
                    'Execution continues into the next case automatically (fall-through)',
                    'PHP throws a fatal error',
                    'The switch block terminates immediately',
                    'The program restarts'
                ]
            ],
            [
                'question' => 'Which keyword in a switch block handles all cases when no match is found?',
                'answer' => 'default',
                'options' => ['default', 'else', 'fallback', 'none']
            ]
        ]
    ],
    [
        'order' => 12,
        'section' => 'PHP Control Structures',
        'name' => 'PHP Loops (While, For, Foreach)',
        'content' => '
<p>Loops are used to execute the same block of code again and again, as long as a certain condition is met.</p>

<ul>
    <li><code>while</code> - loops through a block of code as long as the specified condition is true.</li>
    <li><code>do...while</code> - loops through a block of code once, and then repeats the loop as long as the specified condition is true.</li>
    <li><code>for</code> - loops through a block of code a specified number of times.</li>
    <li><code>foreach</code> - loops through a block of code for each element in an array.</li>
</ul>

<div class="w3-example-box">
    <div class="w3-example-title">Example: foreach Loop with Associative Array</div>
    <pre><code class="language-php">&lt;?php
$userProfiles = [
    "Alex" => "Senior Engineer",
    "Priya" => "DevOps Lead",
    "Sam" => "Product Designer"
];

foreach ($userProfiles as $name => $role) {
    echo "$name works as $role\n";
}
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Alex works as Senior Engineer
Priya works as DevOps Lead
Sam works as Product Designer
    </div>
</div>

<div class="tutorial-tip">
    <strong>Break & Continue:</strong>
    <ul>
        <li><code>break</code> jumps completely out of the loop.</li>
        <li><code>continue</code> breaks one iteration (in the loop), and continues with the next iteration in the loop.</li>
    </ul>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which loop is specifically designed to iterate through arrays and collections in PHP?',
                'answer' => 'foreach',
                'options' => ['foreach', 'for', 'while', 'repeat']
            ],
            [
                'question' => 'What is the difference between while and do-while loops?',
                'answer' => 'do-while executes its block at least once before checking the condition',
                'options' => [
                    'do-while executes its block at least once before checking the condition',
                    'while executes at least once',
                    'while is faster than do-while',
                    'do-while can only iterate over arrays'
                ]
            ]
        ]
    ],
    [
        'order' => 13,
        'section' => 'PHP Control Structures',
        'name' => 'PHP Functions & Type Declarations',
        'content' => '
<p>A function is a block of statements that can be used repeatedly in a program. PHP has over 1,000 built-in functions, and allows you to create your own custom functions.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Modern PHP 8+ Typed Function</div>
    <pre><code class="language-php">&lt;?php
declare(strict_types=1);

function calculateTotalPrice(float $subtotal, float $taxRate = 0.18): float {
    return round($subtotal + ($subtotal * $taxRate), 2);
}

$total = calculateTotalPrice(500.0);
echo "Final Invoice Total: INR " . $total;
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Final Invoice Total: INR 590
    </div>
</div>

<div class="tutorial-note">
    <strong>Strict Types Directive:</strong> Adding <code>declare(strict_types=1);</code> at the very top of your PHP file forces strict type enforcement for function arguments and return values, preventing bugs caused by accidental type coercion.
</div>
',
        'quizzes' => [
            [
                'question' => 'Which keyword is used to declare a function in PHP?',
                'answer' => 'function',
                'options' => ['function', 'def', 'func', 'fn']
            ],
            [
                'question' => 'Where must declare(strict_types=1); be placed in a script?',
                'answer' => 'As the very first statement in the file',
                'options' => ['As the very first statement in the file', 'Inside the function', 'At the bottom of the file', 'Inside php.ini only']
            ]
        ]
    ],
    [
        'order' => 14,
        'section' => 'PHP Arrays & Superglobals',
        'name' => 'PHP Arrays',
        'content' => '
<p>An array is a special variable that can hold more than one value at a time. In PHP, there are three types of arrays:</p>
<ol>
    <li><strong>Indexed arrays</strong>: Arrays with a numeric index (starting at 0).</li>
    <li><strong>Associative arrays</strong>: Arrays with named keys.</li>
    <li><strong>Multidimensional arrays</strong>: Arrays containing one or more arrays.</li>
</ol>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Multidimensional Array Operations</div>
    <pre><code class="language-php">&lt;?php
$students = [
    ["name" => "Rohan", "math" => 85, "physics" => 90],
    ["name" => "Neha", "math" => 95, "physics" => 92]
];

foreach ($students as $s) {
    $avg = ($s["math"] + $s["physics"]) / 2;
    echo $s["name"] . " has an average of " . $avg . "%\n";
}
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Rohan has an average of 87.5%
Neha has an average of 93.5%
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'What is the starting index of an indexed array in PHP by default?',
                'answer' => '0',
                'options' => ['0', '1', '-1', 'Configurable in php.ini']
            ],
            [
                'question' => 'Which function counts the number of elements in an array?',
                'answer' => 'count()',
                'options' => ['count()', 'length()', 'size()', 'total()']
            ]
        ]
    ],
    [
        'order' => 15,
        'section' => 'PHP Arrays & Superglobals',
        'name' => 'PHP Array Sorting Functions',
        'content' => '
<p>PHP provides several built-in functions to sort arrays in different ways:</p>

<ul>
    <li><code>sort()</code> - sort arrays in ascending order.</li>
    <li><code>rsort()</code> - sort arrays in descending order.</li>
    <li><code>asort()</code> - sort associative arrays in ascending order, according to the value.</li>
    <li><code>ksort()</code> - sort associative arrays in ascending order, according to the key.</li>
    <li><code>arsort()</code> - sort associative arrays in descending order, according to the value.</li>
    <li><code>krsort()</code> - sort associative arrays in descending order, according to the key.</li>
</ul>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Sorting Associative Arrays by Key and Value</div>
    <pre><code class="language-php">&lt;?php
$scores = ["Charlie" => 75, "Alice" => 95, "Bob" => 85];

// Sort by key alphabetically
ksort($scores);
foreach ($scores as $student => $mark) {
    echo "$student: $mark\n";
}
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Alice: 95
Bob: 85
Charlie: 75
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which function sorts an associative array in ascending order based on its keys?',
                'answer' => 'ksort()',
                'options' => ['ksort()', 'asort()', 'sort()', 'krsort()']
            ]
        ]
    ],
    [
        'order' => 16,
        'section' => 'PHP Arrays & Superglobals',
        'name' => 'PHP Superglobals',
        'content' => '
<p>Superglobals are built-in variables that are always available in all scopes throughout a script without requiring the <code>global</code> keyword.</p>

<h3>The 9 PHP Superglobals</h3>
<ol>
    <li><code>$GLOBALS</code>: References all variables available in global scope.</li>
    <li><code>$_SERVER</code>: Holds information about headers, paths, script locations, and server IP.</li>
    <li><code>$_REQUEST</code>: Collects data submitted from both HTML form GET and POST, plus cookies.</li>
    <li><code>$_POST</code>: Collects form data submitted with the HTTP POST method.</li>
    <li><code>$_GET</code>: Collects data sent in the URL query string.</li>
    <li><code>$_FILES</code>: Holds items uploaded via multipart/form-data.</li>
    <li><code>$_ENV</code>: Provides environment variables available to the current script.</li>
    <li><code>$_COOKIE</code>: Accesses HTTP cookies sent by the client browser.</li>
    <li><code>$_SESSION</code>: Accesses session variables registered for the current visitor.</li>
</ol>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Inspecting $_SERVER</div>
    <pre><code class="language-php">&lt;?php
echo "Host: " . $_SERVER[\'HTTP_HOST\'] . "\n";
echo "Request Method: " . $_SERVER[\'REQUEST_METHOD\'] . "\n";
echo "Script Path: " . $_SERVER[\'SCRIPT_NAME\'];
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Host: localhost:8000
Request Method: GET
Script Path: /index.php
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which superglobal contains form data submitted with method="post"?',
                'answer' => '$_POST',
                'options' => ['$_POST', '$_GET', '$_SERVER', '$_SUBMIT']
            ],
            [
                'question' => 'Do superglobals require declaring "global $VAR;" inside functions?',
                'answer' => 'No, they are automatically accessible in all scopes',
                'options' => ['No, they are automatically accessible in all scopes', 'Yes, inside classes only', 'Yes, in strict mode', 'Only $_SERVER is global']
            ]
        ]
    ],
    [
        'order' => 17,
        'section' => 'PHP Forms & Advanced',
        'name' => 'PHP Form Handling & Data Sanitization',
        'content' => '
<p>Processing user input safely is the core responsibility of backend web developers. The golden rule of web security is: <strong>Never trust user input</strong>.</p>

<h3>Preventing Cross-Site Scripting (XSS)</h3>
<p>Use <code>htmlspecialchars()</code> to convert special characters to HTML entities, preventing attackers from injecting arbitrary JavaScript code into your pages.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Secure Form Processing</div>
    <pre><code class="language-php">&lt;?php
function sanitizeInput(string $data): string {
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

$rawInput = "&lt;script&gt;alert(\'Hacked!\')&lt;/script&gt; John Doe";
$cleanInput = sanitizeInput($rawInput);

echo "Safe output: " . $cleanInput;
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Safe output: &amp;lt;script&amp;gt;alert(&amp;#039;Hacked!&amp;#039;)&amp;lt;/script&amp;gt; John Doe
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which PHP function protects against Cross-Site Scripting (XSS) by encoding HTML entities?',
                'answer' => 'htmlspecialchars()',
                'options' => ['htmlspecialchars()', 'strip_tags()', 'addslashes()', 'md5()']
            ]
        ]
    ],
    [
        'order' => 18,
        'section' => 'PHP Forms & Advanced',
        'name' => 'PHP Date, Time & Timezones',
        'content' => '
<p>The PHP <code>date()</code> function formats a timestamp to a more readable date and time.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Formatting Dates</div>
    <pre><code class="language-php">&lt;?php
date_default_timezone_set("Asia/Kolkata");

echo "Today is " . date("Y-m-d") . "\n";
echo "Current Time: " . date("h:i:s A") . "\n";
echo "Full Date: " . date("l, F j, Y");
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Today is 2026-11-20
Current Time: 12:30:00 PM
Full Date: Friday, November 20, 2026
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which format character in date() represents the four-digit year?',
                'answer' => 'Y',
                'options' => ['Y', 'y', 'YEAR', 'd']
            ]
        ]
    ],
    [
        'order' => 19,
        'section' => 'PHP Forms & Advanced',
        'name' => 'PHP File Handling',
        'content' => '
<p>PHP has several functions for creating, reading, uploading, and editing files on the server file system.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Writing and Reading Files</div>
    <pre><code class="language-php">&lt;?php
$filename = "sample_log.txt";

// Write content to file
file_put_contents($filename, "System audit: Service healthy at " . date("Y-m-d H:i:s"));

// Read content from file
$content = file_get_contents($filename);
echo $content;

// Clean up
unlink($filename);
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
System audit: Service healthy at 2026-11-20 12:30:00
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which convenient function reads an entire file into a string in PHP?',
                'answer' => 'file_get_contents()',
                'options' => ['file_get_contents()', 'fread()', 'readfile_all()', 'fopen()']
            ]
        ]
    ],
    [
        'order' => 20,
        'section' => 'PHP Forms & Advanced',
        'name' => 'PHP Sessions & Cookies',
        'content' => '
<p>HTTP is a stateless protocol, meaning the server has no built-in way of knowing who made a previous request. Sessions and cookies solve this problem.</p>

<ul>
    <li><strong>Cookie</strong>: A small piece of data stored on the user\'s computer by the browser.</li>
    <li><strong>Session</strong>: Stores user information on the server to be used across multiple pages. A session is started with <code>session_start()</code>.</li>
</ul>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Using Session Variables</div>
    <pre><code class="language-php">&lt;?php
session_start();

// Store session variables
$_SESSION["user_id"] = 1042;
$_SESSION["username"] = "john_developer";

echo "Session active for user: " . $_SESSION["username"];
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Session active for user: john_developer
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which function must be invoked before manipulating the $_SESSION superglobal array?',
                'answer' => 'session_start()',
                'options' => ['session_start()', 'start_session()', 'session_init()', 'session_open()']
            ]
        ]
    ],
    [
        'order' => 21,
        'section' => 'PHP Object Oriented (OOP)',
        'name' => 'PHP OOP - Classes, Objects & Constructors',
        'content' => '
<p>Object-Oriented Programming (OOP) is a programming style that models real-world concepts into code using classes and objects.</p>

<ul>
    <li><strong>Class</strong>: A blueprint or template for creating objects.</li>
    <li><strong>Object</strong>: An individual instance of a class.</li>
    <li><strong>Constructor</strong>: A special method (<code>__construct()</code>) called automatically when an object is created with <code>new</code>.</li>
</ul>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Class with Constructor and Methods</div>
    <pre><code class="language-php">&lt;?php
class Vehicle {
    public string $brand;
    public string $model;

    public function __construct(string $brand, string $model) {
        $this-&gt;brand = $brand;
        $this-&gt;model = $model;
    }

    public function getDetails(): string {
        return "Vehicle: {$this-&gt;brand} {$this-&gt;model}";
    }
}

$myCar = new Vehicle("Tesla", "Model S");
echo $myCar-&gt;getDetails();
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Vehicle: Tesla Model S
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'What is the name of the special constructor method in modern PHP classes?',
                'answer' => '__construct()',
                'options' => ['__construct()', 'constructor()', 'className()', '__init__()']
            ],
            [
                'question' => 'Which keyword refers to the current object instance inside a class method?',
                'answer' => '$this',
                'options' => ['$this', 'self', 'parent', 'static']
            ]
        ]
    ],
    [
        'order' => 22,
        'section' => 'PHP Object Oriented (OOP)',
        'name' => 'PHP OOP - Inheritance & Visibility Modifiers',
        'content' => '
<p>Inheritance enables a child class to inherit properties and methods from a parent class using the <code>extends</code> keyword.</p>

<h3>Access Modifiers</h3>
<ul>
    <li><code>public</code>: The property or method can be accessed from everywhere.</li>
    <li><code>protected</code>: Can be accessed only within the class itself and by classes derived from that class.</li>
    <li><code>private</code>: Can ONLY be accessed within the defining class.</li>
</ul>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Class Inheritance</div>
    <pre><code class="language-php">&lt;?php
class Employee {
    protected string $name;

    public function __construct(string $name) {
        $this-&gt;name = $name;
    }
}

class Manager extends Employee {
    public function getGreeting(): string {
        return "Welcome Manager: " . $this-&gt;name;
    }
}

$manager = new Manager("Sarah");
echo $manager-&gt;getGreeting();
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Welcome Manager: Sarah
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which access modifier allows access within the class and child derived classes, but not externally?',
                'answer' => 'protected',
                'options' => ['protected', 'private', 'public', 'static']
            ]
        ]
    ],
    [
        'order' => 23,
        'section' => 'PHP Database & APIs',
        'name' => 'PHP MySQL Database (PDO & CRUD)',
        'content' => '
<p>PHP Data Objects (<strong>PDO</strong>) is a database abstraction layer that provides a consistent, secure API for working with multiple database engines.</p>

<h3>Prepared Statements: Preventing SQL Injection</h3>
<p>Never concatenate variables directly into SQL queries! Always use prepared statements with placeholders (<code>:param</code> or <code>?</code>).</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Secure PDO SELECT Query</div>
    <pre><code class="language-php">&lt;?php
// Connecting and executing prepared query securely
$stmt = $pdo-&gt;prepare("SELECT title, slug FROM courses WHERE id = :id");
$stmt-&gt;execute([\'id\' =&gt; 1]);
$course = $stmt-&gt;fetch();

if ($course) {
    echo "Found Course: " . $course[\'title\'] . " (" . $course[\'slug\'] . ")";
}
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Found Course: PHP Tutorial (php-tutorials)
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'What is the primary security defense against SQL Injection in PHP?',
                'answer' => 'Prepared statements with parameterized queries',
                'options' => [
                    'Prepared statements with parameterized queries',
                    'addslashes() function',
                    'md5 hashing',
                    'Regular expression string replacement'
                ]
            ]
        ]
    ],
    [
        'order' => 24,
        'section' => 'PHP Database & APIs',
        'name' => 'PHP JSON & REST APIs',
        'content' => '
<p>Modern web and mobile applications communicate using JSON (JavaScript Object Notation). PHP provides native functions to serialize and parse JSON effortlessly.</p>

<ul>
    <li><code>json_encode($data)</code>: Converts PHP arrays or objects into a JSON formatted string.</li>
    <li><code>json_decode($jsonString, true)</code>: Converts a JSON formatted string into a PHP associative array.</li>
</ul>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Building a Simple JSON API Endpoint</div>
    <pre><code class="language-php">&lt;?php
header(\'Content-Type: application/json; charset=utf-8\');

$apiResponse = [
    "status" =&gt; "success",
    "data" =&gt; [
        "platform" =&gt; "Learn.Nectra",
        "api_version" =&gt; "v2.0",
        "active_courses" =&gt; 6
    ]
];

echo json_encode($apiResponse, JSON_PRETTY_PRINT);
?&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
{
    "status": "success",
    "data": {
        "platform": "Learn.Nectra",
        "api_version": "v2.0",
        "active_courses": 6
    }
}
    </div>
</div>
',
        'quizzes' => [
            [
                'question' => 'Which function converts a PHP array into a valid JSON string?',
                'answer' => 'json_encode()',
                'options' => ['json_encode()', 'json_decode()', 'to_json()', 'serialize_json()']
            ]
        ]
    ]
];

foreach ($phpChapters as $chap) {
    upsertChapter(
        $pdo,
        $phpId,
        $chap['order'],
        $chap['section'],
        $chap['name'],
        $chap['content'],
        $chap['quizzes']
    );
    echo "  [OK] Chapter {$chap['order']}: {$chap['name']}\n";
}

echo "PHP Curriculum seeded successfully!\n";
