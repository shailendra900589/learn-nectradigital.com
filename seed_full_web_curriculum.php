<?php
declare(strict_types=1);
require_once __DIR__ . '/seed_content.php';

echo "=== Seeding Full Beginner-to-Master Curricula for JavaScript, HTML5, CSS3, SQL ===\n";

$jsId = $courseIds['javascript-tutorial'];
$htmlId = $courseIds['html-tutorial'];
$cssId = $courseIds['css-tutorial'];
$sqlId = $courseIds['sql-tutorial'];

// Clear old stubs for these courses
$pdo->prepare("DELETE FROM chapters WHERE course_id IN (?, ?, ?, ?)")->execute([$jsId, $htmlId, $cssId, $sqlId]);

// -----------------------------------------------------------------------------
// JAVASCRIPT CURRICULUM (12 CHAPTERS)
// -----------------------------------------------------------------------------
$jsChapters = [
    [
        'order' => 1,
        'section' => 'JS Basics',
        'name' => 'JavaScript Introduction & How Code Runs',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>JavaScript</strong> (often abbreviated as <strong>JS</strong>) is the world's most widely used programming language. While HTML structures the webpage and CSS makes it look beautiful, JavaScript gives it a brain—allowing it to react to clicks, calculate data, fetch information from servers, and update the screen without reloading.</p>

<div class="tutorial-tip">
    <strong>Real-World Analogy:</strong> Think of a modern electric car. HTML is the metal chassis and seats (structure). CSS is the shiny blue paint and leather upholstery (appearance). JavaScript is the engine, dashboard touchscreen, and sensors (interactivity and intelligence).
</div>

<h3>Where Does JavaScript Run?</h3>
<p>JavaScript originally ran exclusively inside web browsers (like Google Chrome, Safari, and Firefox). Today, with modern runtimes like Node.js and Bun, JavaScript can run everywhere: on web servers, mobile phones, desktop apps, and even IoT devices.</p>

<div class="w3-example-box">
    <div class="w3-example-title">JavaScript Example: Your First Script</div>
    <pre><code class="language-javascript">// Outputting a message to the console
console.log("Hello, World! Welcome to JavaScript.");

// Calculating a result
let hours = 24;
let minutes = 60;
console.log("Minutes in a day:", hours * minutes);</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Console Output:</div>
Hello, World! Welcome to JavaScript.
Minutes in a day: 1440
    </div>
</div>

<div class="beginner-breakdown">
    <div class="beginner-breakdown-title">Line-by-Line Code Breakdown (For Beginners)</div>
    <table class="beginner-breakdown-table">
        <thead><tr><th>Code / Token</th><th>Simple English Meaning</th></tr></thead>
        <tbody>
            <tr><td class="code-cell">// comment</td><td>A note for human developers. The JavaScript engine completely ignores lines starting with <code>//</code>.</td></tr>
            <tr><td class="code-cell">console.log(...)</td><td>The built-in function to print output onto the screen or browser developer tools console.</td></tr>
            <tr><td class="code-cell">"Hello, World!"</td><td>A text string, enclosed in double quotes (or single quotes / backticks).</td></tr>
            <tr><td class="code-cell">let hours = 24;</td><td>Creates a memory container named <code>hours</code> and stores the number <code>24</code> inside it.</td></tr>
            <tr><td class="code-cell">; (semicolon)</td><td>Marks the end of a statement in JavaScript.</td></tr>
        </tbody>
    </table>
</div>

<div class="tutorial-mistake">
    <div class="tutorial-mistake-title">⚠️ Common Beginner Mistake: Case Sensitivity</div>
    <p>JavaScript is strictly case-sensitive. <code>console.log()</code> works, but <code>Console.log()</code> or <code>CONSOLE.LOG()</code> will trigger a <code>ReferenceError</code> crash!</p>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'What is the primary role of JavaScript in web development?',
                'answer' => 'To add interactivity, logic, and dynamic behavior to web pages',
                'options' => ['To add interactivity, logic, and dynamic behavior to web pages', 'To define page layout styling only', 'To replace HTML and CSS completely', 'To compress images on web servers']
            ],
            [
                'question' => 'Which built-in function is used to write output to the developer console?',
                'answer' => 'console.log()',
                'options' => ['console.log()', 'print()', 'echo()', 'System.out.println()']
            ],
            [
                'question' => 'Is JavaScript case-sensitive?',
                'answer' => 'Yes, variable and function names must match exact capitalization',
                'options' => ['Yes, variable and function names must match exact capitalization', 'No, JavaScript ignores case', 'Only inside double quotes', 'Only when running on Linux']
            ]
        ]
    ],
    [
        'order' => 2,
        'section' => 'JS Basics',
        'name' => 'JavaScript Variables (let, const, var)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">A <strong>variable</strong> is a labeled storage container in the computer's memory used to hold data. In modern JavaScript (ES6+), we have two main keywords to declare variables: <code>const</code> and <code>let</code>.</p>

<div class="tutorial-note">
    <strong>Gold Rule of Modern JavaScript:</strong> Always use <code>const</code> by default. Only use <code>let</code> when you know the value needs to change or be reassigned later (like a counter in a loop). Avoid legacy <code>var</code> because it lacks block scoping and causes unpredictable bugs.
</div>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Declaring Variables with const and let</div>
    <pre><code class="language-javascript">// 1. Using const for fixed values that never change
const websiteName = "Learn.Nectra";
const birthYear = 2000;

// 2. Using let for values that change over time
let currentYear = 2026;
let userAge = currentYear - birthYear;

console.log("Portal:", websiteName);
console.log("User age:", userAge);

// Updating let variable
currentYear = 2027;
userAge = currentYear - birthYear;
console.log("Next year age:", userAge);</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Console Output:</div>
Portal: Learn.Nectra
User age: 26
Next year age: 27
    </div>
</div>

<div class="beginner-breakdown">
    <div class="beginner-breakdown-title">Line-by-Line Code Breakdown</div>
    <table class="beginner-breakdown-table">
        <thead><tr><th>Code</th><th>Explanation</th></tr></thead>
        <tbody>
            <tr><td class="code-cell">const websiteName = "Learn.Nectra";</td><td>Creates an immutable constant. Once declared, <code>websiteName</code> cannot be reassigned to something else.</td></tr>
            <tr><td class="code-cell">let currentYear = 2026;</td><td>Creates a mutable variable. Its value can be updated freely throughout the program.</td></tr>
            <tr><td class="code-cell">currentYear = 2027;</td><td>Reassigns <code>currentYear</code> with a new number. Notice we do not write <code>let</code> again when updating.</td></tr>
        </tbody>
    </table>
</div>

<div class="tutorial-mistake">
    <div class="tutorial-mistake-title">⚠️ Common Beginner Mistake: Reassigning a const</div>
    <p>If you try to reassign a <code>const</code> variable: <code>const score = 10; score = 20;</code>, JavaScript will throw an immediate error: <code>TypeError: Assignment to constant variable</code>. If a value needs to change, use <code>let</code>!</p>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which keyword should you use by default when creating variables in modern JavaScript?',
                'answer' => 'const',
                'options' => ['const', 'let', 'var', 'def']
            ],
            [
                'question' => 'What happens if you try to reassign a variable declared with const?',
                'answer' => 'A TypeError is thrown',
                'options' => ['A TypeError is thrown', 'The variable updates silently', 'It converts into a string', 'The program skips the line']
            ]
        ]
    ],
    [
        'order' => 3,
        'section' => 'JS Basics',
        'name' => 'JavaScript Data Types & Template Literals',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Data types tell the computer what kind of value a variable is holding. JavaScript is a <strong>dynamically typed</strong> language, meaning you do not have to specify the type manually; the engine figures it out automatically.</p>

<h3>The 7 Primitive Data Types in JavaScript</h3>
<ul>
    <li><strong>String:</strong> Text data enclosed in quotes (e.g. <code>"JavaScript"</code>, <code>'Hello'</code>).</li>
    <li><strong>Number:</strong> Integers and floating-point numbers (e.g. <code>42</code>, <code>3.14</code>).</li>
    <li><strong>Boolean:</strong> Logical value: either <code>true</code> or <code>false</code>.</li>
    <li><strong>Undefined:</strong> A variable that has been declared but not yet assigned a value.</li>
    <li><strong>Null:</strong> An intentional empty value meaning "nothing" or "empty".</li>
    <li><strong>BigInt:</strong> For very large integers beyond \(2^{53} - 1\).</li>
    <li><strong>Symbol:</strong> Unique and immutable identifier.</li>
</ul>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Checking Types and Template Literals</div>
    <pre><code class="language-javascript">const language = "JavaScript";
const version = 2026;
const isAwesome = true;
let emptySlot = null;

// Inspecting types with typeof operator
console.log("language is:", typeof language);
console.log("version is:", typeof version);
console.log("isAwesome is:", typeof isAwesome);

// Modern Template Literals with backticks ` `
const summary = `I am mastering ${language} in ${version}! Status: ${isAwesome}`;
console.log(summary);</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Console Output:</div>
language is: string
version is: number
isAwesome is: boolean
I am mastering JavaScript in 2026! Status: true
    </div>
</div>

<div class="tutorial-tip">
    <strong>Pro Tip: Template Literals (` `):</strong> Always use backticks <code>` `</code> instead of quotes when combining variables with text. You can insert any variable or expression directly using <code>${variable}</code> without messy <code>+</code> string concatenation!
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which operator returns the data type of a variable?',
                'answer' => 'typeof',
                'options' => ['typeof', 'gettype()', 'type()', 'instanceof']
            ],
            [
                'question' => 'How do you interpolate a variable inside a template literal?',
                'answer' => '${variableName}',
                'options' => ['${variableName}', '#{variableName}', '%variableName%', '&variableName']
            ]
        ]
    ],
    [
        'order' => 4,
        'section' => 'JS Basics',
        'name' => 'JavaScript Operators & Strict Equality (===)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Operators are special symbols used to perform calculations, compare values, and make decisions in your code.</p>

<h3>Strict Equality (===) vs Loose Equality (==)</h3>
<p>One of the most critical rules in JavaScript: <strong>Always use <code>===</code> (strict equality)</strong>. Loose equality <code>==</code> performs automatic type conversion (coercion), which leads to notorious bugs.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Comparison & Strict Equality</div>
    <pre><code class="language-javascript">const num = 5;
const str = "5";

// Loose equality (Dangerous)
console.log("num == str is:", num == str); // true because types are coerced

// Strict equality (Safe & Recommended)
console.log("num === str is:", num === str); // false because types differ!

// Arithmetic & Logical operators
let score = 85;
let hasPassed = (score >= 50) && (score <= 100);
console.log("Student has passed:", hasPassed);</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Console Output:</div>
num == str is: true
num === str is: false
Student has passed: true
    </div>
</div>

<div class="beginner-breakdown">
    <div class="beginner-breakdown-title">Core Operators Cheat Sheet</div>
    <table class="beginner-breakdown-table">
        <thead><tr><th>Operator</th><th>Name</th><th>Example</th></tr></thead>
        <tbody>
            <tr><td class="code-cell">===</td><td>Strict Equal</td><td><code>5 === 5</code> (true), <code>5 === "5"</code> (false)</td></tr>
            <tr><td class="code-cell">!==</td><td>Strict Not Equal</td><td><code>5 !== 6</code> (true)</td></tr>
            <tr><td class="code-cell">&amp;&amp;</td><td>Logical AND</td><td>Both sides must be true: <code>(age &gt;= 18) &amp;&amp; hasLicense</code></td></tr>
            <tr><td class="code-cell">||</td><td>Logical OR</td><td>At least one side must be true</td></tr>
            <tr><td class="code-cell">!</td><td>Logical NOT</td><td>Inverts boolean: <code>!true</code> becomes <code>false</code></td></tr>
        </tbody>
    </table>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Why is === preferred over == in JavaScript?',
                'answer' => '=== compares both value and data type without unsafe type coercion',
                'options' => ['=== compares both value and data type without unsafe type coercion', '=== runs faster in all browsers', '== is deprecated and illegal in ES6', 'There is no difference']
            ]
        ]
    ],
    [
        'order' => 5,
        'section' => 'JS Fundamentals',
        'name' => 'JavaScript Functions & Modern Arrow Functions',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">A <strong>function</strong> is a reusable block of code designed to perform a specific task. Instead of writing the same 10 lines of code five times, you write it once inside a function and call it whenever needed.</p>

<div class="tutorial-note">
    <strong>Analogy:</strong> A function is like a vending machine. You input parameters (money and selection code), the machine executes its internal steps, and it returns an output (a soda can).
</div>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Standard Functions vs Arrow Functions</div>
    <pre><code class="language-javascript">// 1. Standard Function Declaration
function calculateTotal(price, taxRate = 0.08) {
    const tax = price * taxRate;
    return price + tax;
}

// 2. Modern ES6 Arrow Function
const calculateDiscount = (price, percentage) => price - (price * (percentage / 100));

// Calling the functions
const laptopPrice = 1000;
const totalWithTax = calculateTotal(laptopPrice);
const finalPrice = calculateDiscount(totalWithTax, 15);

console.log("Total with tax: $" + totalWithTax);
console.log("Final price after 15% discount: $" + finalPrice);</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Console Output:</div>
Total with tax: $1080
Final price after 15% discount: $918
    </div>
</div>

<div class="beginner-breakdown">
    <div class="beginner-breakdown-title">Line-by-Line Code Breakdown</div>
    <table class="beginner-breakdown-table">
        <thead><tr><th>Code</th><th>Explanation</th></tr></thead>
        <tbody>
            <tr><td class="code-cell">function calculateTotal(price, taxRate = 0.08)</td><td>Defines a function named <code>calculateTotal</code>. <code>taxRate = 0.08</code> sets a default value if no tax rate is supplied.</td></tr>
            <tr><td class="code-cell">return price + tax;</td><td>Sends the computed result back to the caller and exits the function.</td></tr>
            <tr><td class="code-cell">(price, percentage) => ...</td><td>Arrow function syntax: concise and modern, ideal for callbacks and compact math functions.</td></tr>
        </tbody>
    </table>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'What keyword is used to send a value back from a function?',
                'answer' => 'return',
                'options' => ['return', 'send', 'output', 'yield']
            ],
            [
                'question' => 'What is the syntax for an arrow function with two parameters x and y?',
                'answer' => '(x, y) => x + y',
                'options' => ['(x, y) => x + y', 'function(x, y) -> x + y', 'x, y => { x + y }', 'def (x, y) => x + y']
            ]
        ]
    ],
    [
        'order' => 6,
        'section' => 'JS Fundamentals',
        'name' => 'JavaScript Objects & Methods',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">An <strong>object</strong> is a standalone entity that holds collections of key-value pairs. In the real world, a user has a name, email, age, and actions they can perform (like logging in). In JavaScript, objects represent these complex real-world concepts.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Creating and Using Objects</div>
    <pre><code class="language-javascript">const user = {
    firstName: "Rahul",
    lastName: "Sharma",
    role: "Full Stack Engineer",
    level: 3,
    isActive: true,
    // Method inside object
    getFullName: function() {
        return `${this.firstName} ${this.lastName}`;
    }
};

// Accessing properties with Dot Notation
console.log("Full Name:", user.getFullName());
console.log("Role:", user.role);

// Modifying and adding properties
user.level = 4;
user.country = "India";
console.log("Updated Level:", user.level);
console.log("Country:", user.country);</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Console Output:</div>
Full Name: Rahul Sharma
Role: Full Stack Engineer
Updated Level: 4
Country: India
    </div>
</div>

<div class="beginner-breakdown">
    <div class="beginner-breakdown-title">Line-by-Line Code Breakdown</div>
    <table class="beginner-breakdown-table">
        <thead><tr><th>Code</th><th>Explanation</th></tr></thead>
        <tbody>
            <tr><td class="code-cell">const user = { ... };</td><td>Creates an object literal using curly braces <code>{}</code>.</td></tr>
            <tr><td class="code-cell">firstName: "Rahul"</td><td>A key-value property. <code>firstName</code> is the key, <code>"Rahul"</code> is the value.</td></tr>
            <tr><td class="code-cell">this.firstName</td><td>Inside an object method, <code>this</code> refers to the owner object (<code>user</code>).</td></tr>
            <tr><td class="code-cell">user.role</td><td>Dot notation: the standard way to read or write a property on an object.</td></tr>
        </tbody>
    </table>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'How do you access the property "age" of an object named "person"?',
                'answer' => 'person.age or person["age"]',
                'options' => ['person.age or person["age"]', 'person->age', 'person::age', 'person(age)']
            ]
        ]
    ],
    [
        'order' => 7,
        'section' => 'JS Fundamentals',
        'name' => 'JavaScript Arrays & Modern Methods (map, filter)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">An <strong>array</strong> is a special variable that can hold more than one value at a time in an ordered, zero-indexed list. Modern JavaScript developers use powerful functional methods like <code>.map()</code>, <code>.filter()</code>, and <code>.forEach()</code> to process array data cleanly.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Array Transformation with map and filter</div>
    <pre><code class="language-javascript">const products = [
    { name: "Laptop", price: 1200 },
    { name: "Mouse", price: 25 },
    { name: "Keyboard", price: 75 },
    { name: "Monitor", price: 300 }
];

// 1. filter() keeps items matching a condition
const premiumProducts = products.filter(p => p.price > 50);

// 2. map() transforms each item into something new
const productLabels = premiumProducts.map(p => `${p.name} ($${p.price})`);

console.log("Premium items count:", premiumProducts.length);
console.log("Labels:", productLabels.join(", "));</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Console Output:</div>
Premium items count: 3
Labels: Laptop ($1200), Keyboard ($75), Monitor ($300)
    </div>
</div>

<div class="beginner-breakdown">
    <div class="beginner-breakdown-title">Essential Array Methods</div>
    <table class="beginner-breakdown-table">
        <thead><tr><th>Method</th><th>What It Does</th><th>Returns</th></tr></thead>
        <tbody>
            <tr><td class="code-cell">.push(item)</td><td>Adds a new element to the very end of the array.</td><td>New array length</td></tr>
            <tr><td class="code-cell">.filter(fn)</td><td>Returns a brand new array with only elements that pass the test.</td><td>New filtered array</td></tr>
            <tr><td class="code-cell">.map(fn)</td><td>Transforms every element and returns a new array of same size.</td><td>New transformed array</td></tr>
            <tr><td class="code-cell">.find(fn)</td><td>Finds and returns the very first element matching condition.</td><td>Single item or undefined</td></tr>
        </tbody>
    </table>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'What is the index of the first element in a JavaScript array?',
                'answer' => '0',
                'options' => ['0', '1', '-1', 'null']
            ],
            [
                'question' => 'Which method returns a new array with elements transformed by a callback function?',
                'answer' => '.map()',
                'options' => ['.map()', '.filter()', '.push()', '.splice()']
            ]
        ]
    ],
    [
        'order' => 8,
        'section' => 'JS Flow Control',
        'name' => 'JavaScript Conditionals (if, else if, switch)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Conditional statements allow your program to make decisions and execute different blocks of code depending on whether a given condition evaluates to <code>true</code> or <code>false</code>.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Conditional Decision Making</div>
    <pre><code class="language-javascript">const score = 88;
let grade = "";

if (score >= 90) {
    grade = "A+ (Outstanding)";
} else if (score >= 80) {
    grade = "A (Excellent)";
} else if (score >= 70) {
    grade = "B (Good)";
} else {
    grade = "C (Needs Improvement)";
}

console.log(`Score: ${score} -> Grade: ${grade}`);

// Ternary Operator shorthand: condition ? valueIfTrue : valueIfFalse
const status = score >= 70 ? "PASSED" : "FAILED";
console.log("Exam Status:", status);</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Console Output:</div>
Score: 88 -> Grade: A (Excellent)
Exam Status: PASSED
    </div>
</div>

<div class="tutorial-tip">
    <strong>Ternary Operator ( ? : ):</strong> Use ternary operators for concise one-line conditionals when assigning a value: <code>const canVote = age &gt;= 18 ? "Yes" : "No";</code>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'How do you write an IF statement in JavaScript?',
                'answer' => 'if (condition) { ... }',
                'options' => ['if (condition) { ... }', 'if condition then { ... }', 'if (condition):', 'if condition:']
            ]
        ]
    ],
    [
        'order' => 9,
        'section' => 'JS Flow Control',
        'name' => 'JavaScript Loops (for, while, for...of)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Loops execute a block of code repeatedly as long as a specified condition remains true. They are essential for processing lists, generating elements, and automating repetitive tasks.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Standard for loop vs modern for...of</div>
    <pre><code class="language-javascript">// 1. Classic for loop with counter
console.log("--- Countdown ---");
for (let i = 3; i >= 1; i--) {
    console.log(`T-minus ${i}...`);
}
console.log("Liftoff! 🚀");

// 2. Modern for...of loop over arrays
const technologies = ["HTML", "CSS", "JavaScript", "Python"];
console.log("\n--- Technology Stack ---");
for (const tech of technologies) {
    console.log(`- Mastering ${tech}`);
}</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Console Output:</div>
--- Countdown ---
T-minus 3...
T-minus 2...
T-minus 1...
Liftoff! 🚀

--- Technology Stack ---
- Mastering HTML
- Mastering CSS
- Mastering JavaScript
- Mastering Python
    </div>
</div>

<div class="beginner-breakdown">
    <div class="beginner-breakdown-title">Anatomy of a for loop</div>
    <table class="beginner-breakdown-table">
        <thead><tr><th>Part</th><th>Code</th><th>Role</th></tr></thead>
        <tbody>
            <tr><td class="code-cell">Initialization</td><td><code>let i = 0</code></td><td>Runs once before the loop starts to setup the counter.</td></tr>
            <tr><td class="code-cell">Condition</td><td><code>i &lt; 5</code></td><td>Checked before every iteration; if false, loop stops.</td></tr>
            <tr><td class="code-cell">Increment</td><td><code>i++</code></td><td>Executed after every loop cycle to advance the counter.</td></tr>
        </tbody>
    </table>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which loop is best suited for directly iterating through elements of an array without index counters?',
                'answer' => 'for...of loop',
                'options' => ['for...of loop', 'while (true)', 'for...in (on primitives)', 'loop()']
            ]
        ]
    ],
    [
        'order' => 10,
        'section' => 'JS Browser & DOM',
        'name' => 'JavaScript DOM Manipulation (Selecting & Modifying)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <strong>DOM</strong> (Document Object Model) is a tree-like representation of all HTML elements on a webpage. JavaScript uses the DOM API to find elements, change their text, modify styling, and add or remove elements in real time.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Selecting and Modifying DOM Elements</div>
    <pre><code class="language-html">&lt;div id="card" style="padding: 16px; border: 2px solid #04AA6D; border-radius: 8px;"&gt;
    &lt;h3 id="cardTitle"&gt;Initial Heading&lt;/h3&gt;
    &lt;p id="cardDesc"&gt;This text will be changed by JavaScript.&lt;/p&gt;
    &lt;button type="button" onclick="updateCard()" style="background:#04AA6D;color:#fff;border:none;padding:8px 16px;border-radius:4px;cursor:pointer;"&gt;Click to Change&lt;/button&gt;
&lt;/div&gt;

&lt;script&gt;
function updateCard() {
    // 1. Select element by ID
    const title = document.getElementById("cardTitle");
    const desc = document.getElementById("cardDesc");
    
    // 2. Modify properties
    title.innerText = "DOM Manipulation Success! 🎉";
    title.style.color = "#04AA6D";
    desc.innerText = "JavaScript dynamically updated this content in real time!";
}
&lt;/script&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Interactive Preview:</div>
Click the button in Try it Yourself to see the title and description change dynamically without refreshing the page!
    </div>
</div>

<div class="beginner-breakdown">
    <div class="beginner-breakdown-title">Most Popular DOM Methods</div>
    <table class="beginner-breakdown-table">
        <thead><tr><th>Method</th><th>Description</th></tr></thead>
        <tbody>
            <tr><td class="code-cell">document.getElementById("id")</td><td>Selects a single element by its HTML id.</td></tr>
            <tr><td class="code-cell">document.querySelector(".class")</td><td>Selects the first element matching any CSS selector.</td></tr>
            <tr><td class="code-cell">document.querySelectorAll("p")</td><td>Selects all matching elements as a NodeList.</td></tr>
            <tr><td class="code-cell">element.innerText</td><td>Reads or updates the visible plain text inside an element.</td></tr>
            <tr><td class="code-cell">element.innerHTML</td><td>Reads or writes raw HTML markup inside an element.</td></tr>
        </tbody>
    </table>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which method selects an element by its HTML ID attribute?',
                'answer' => 'document.getElementById()',
                'options' => ['document.getElementById()', 'document.selectId()', 'document.getElement()', 'window.getId()']
            ]
        ]
    ],
    [
        'order' => 11,
        'section' => 'JS Browser & DOM',
        'name' => 'JavaScript Event Listeners (click, input, submit)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Events are actions that happen in the browser—either triggered by the user (like clicking a button, typing on the keyboard, scrolling) or the browser itself (like finishing loading). JavaScript registers <strong>Event Listeners</strong> to react to these moments.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Modern addEventListener</div>
    <pre><code class="language-html">&lt;div style="text-align:center; padding: 20px;"&gt;
    &lt;h2 id="counterDisplay" style="font-size: 2.5rem; margin: 0 0 10px 0;"&gt;0&lt;/h2&gt;
    &lt;button id="btnInc" style="background:#04AA6D;color:#fff;padding:8px 16px;border:none;border-radius:4px;cursor:pointer;font-weight:bold;"&gt;Increment +&lt;/button&gt;
    &lt;button id="btnReset" style="background:#ef4444;color:#fff;padding:8px 16px;border:none;border-radius:4px;cursor:pointer;font-weight:bold;margin-left:8px;"&gt;Reset&lt;/button&gt;
&lt;/div&gt;

&lt;script&gt;
let count = 0;
const display = document.getElementById("counterDisplay");
const btnInc = document.getElementById("btnInc");
const btnReset = document.getElementById("btnReset");

// Clean modern event listening
btnInc.addEventListener("click", () => {
    count++;
    display.innerText = count;
});

btnReset.addEventListener("click", () => {
    count = 0;
    display.innerText = count;
});
&lt;/script&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Interactive Result:</div>
Click "Try it Yourself »" to test the live click event listener counter in your browser!
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'What is the recommended method to attach an event handler to an element in modern JavaScript?',
                'answer' => 'element.addEventListener("event", callback)',
                'options' => ['element.addEventListener("event", callback)', 'element.attach()', 'element.on()', 'window.listen()']
            ]
        ]
    ],
    [
        'order' => 12,
        'section' => 'JS Advanced & Asynchronous',
        'name' => 'JavaScript Async/Await, Promises & Fetch API',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">In real-world web applications, fetching data from an API or reading a large file takes time. You cannot freeze the browser while waiting for the network. Modern JavaScript uses <strong>Promises</strong> and <code>async/await</code> syntax to perform asynchronous tasks smoothly in the background.</p>

<div class="tutorial-note">
    <strong>Analogy:</strong> When you order coffee, the cashier gives you an electronic buzzer (a Promise). You don't stand frozen in front of the cashier; you find a seat and read your book (non-blocking). When the buzzer beeps (the Promise resolves), you collect your coffee!
</div>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Fetching Remote API Data with async/await</div>
    <pre><code class="language-javascript">// Simulating an async API call with a Promise
function fetchUserProfile(userId) {
    return new Promise((resolve) => {
        setTimeout(() => {
            resolve({ id: userId, username: "alex_coder", status: "Active Student" });
        }, 100);
    });
}

// Consuming asynchronous data with async / await
async function displayUser() {
    console.log("1. Starting data request...");
    
    // The await keyword pauses this function until the promise finishes
    const user = await fetchUserProfile(101);
    
    console.log("2. Data received successfully!");
    console.log(`User: ${user.username} | Status: ${user.status}`);
}

displayUser();</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Console Output:</div>
1. Starting data request...
2. Data received successfully!
User: alex_coder | Status: Active Student
    </div>
</div>

<div class="beginner-breakdown">
    <div class="beginner-breakdown-title">Line-by-Line Code Breakdown</div>
    <table class="beginner-breakdown-table">
        <thead><tr><th>Keyword</th><th>Function</th></tr></thead>
        <tbody>
            <tr><td class="code-cell">async function</td><td>Tells JavaScript that this function contains asynchronous operations and will return a Promise.</td></tr>
            <tr><td class="code-cell">await</td><td>Pauses execution of the <code>async</code> function until the Promise resolves, without blocking the browser.</td></tr>
            <tr><td class="code-cell">fetch(url)</td><td>The built-in browser API to send HTTP GET/POST requests to REST APIs.</td></tr>
        </tbody>
    </table>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Where can the "await" keyword be used?',
                'answer' => 'Only inside functions declared with the "async" keyword (or top-level modules)',
                'options' => ['Only inside functions declared with the "async" keyword (or top-level modules)', 'Inside any function or loop', 'Only inside class constructors', 'Only on window object']
            ],
            [
                'question' => 'What built-in JavaScript API is used to fetch resources across the network?',
                'answer' => 'fetch()',
                'options' => ['fetch()', 'http.get()', 'request()', 'curl()']
            ]
        ]
    ]
];

foreach ($jsChapters as $ch) {
    upsertChapter($pdo, $jsId, $ch['order'], $ch['section'], $ch['name'], $ch['content'], $ch['quizzes']);
}
echo "Seeded 12 JavaScript Chapters.\n";

// -----------------------------------------------------------------------------
// HTML5 CURRICULUM (10 CHAPTERS)
// -----------------------------------------------------------------------------
$htmlChapters = [
    [
        'order' => 1,
        'section' => 'HTML Basics',
        'name' => 'HTML5 Introduction & Document Skeleton',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>HTML</strong> stands for <strong>HyperText Markup Language</strong>. It is the foundational building block of the World Wide Web. Every website you have ever visited—from Google and YouTube to online stores—uses HTML to structure its content.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Standard HTML5 Page Template</div>
    <pre><code class="language-html">&lt;!DOCTYPE html&gt;
&lt;html lang="en"&gt;
&lt;head&gt;
    &lt;meta charset="UTF-8"&gt;
    &lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;
    &lt;title&gt;My First Web Page&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;h1&gt;Welcome to HTML5 Tutorials!&lt;/h1&gt;
    &lt;p&gt;HTML creates the structure of every website.&lt;/p&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Rendered Browser Output:</div>
<h1 style="font-size:1.6rem;font-weight:bold;margin:0 0 8px 0;">Welcome to HTML5 Tutorials!</h1>
<p style="margin:0;">HTML creates the structure of every website.</p>
    </div>
</div>

<div class="beginner-breakdown">
    <div class="beginner-breakdown-title">Skeleton Tags Breakdown</div>
    <table class="beginner-breakdown-table">
        <thead><tr><th>Tag</th><th>Purpose</th></tr></thead>
        <tbody>
            <tr><td class="code-cell">&lt;!DOCTYPE html&gt;</td><td>Tells the browser that this document is written in modern HTML5 standard.</td></tr>
            <tr><td class="code-cell">&lt;html lang="en"&gt;</td><td>The root container for all page contents. <code>lang="en"</code> sets English for screen readers and SEO.</td></tr>
            <tr><td class="code-cell">&lt;head&gt;</td><td>Contains metadata (title, character encoding, CSS links) not visible directly on the page.</td></tr>
            <tr><td class="code-cell">&lt;body&gt;</td><td>Contains everything visible to the user: text, headings, images, forms, buttons.</td></tr>
        </tbody>
    </table>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'What does HTML stand for?',
                'answer' => 'HyperText Markup Language',
                'options' => ['HyperText Markup Language', 'High Tech Modern Language', 'Hyperlink and Text Management Language', 'Home Tool Markup Language']
            ],
            [
                'question' => 'Which declaration defines this document to be modern HTML5?',
                'answer' => '<!DOCTYPE html>',
                'options' => ['<!DOCTYPE html>', '<html5>', '<doctype:html5>', '<meta html="5">']
            ]
        ]
    ],
    [
        'order' => 2,
        'section' => 'HTML Basics',
        'name' => 'HTML Headings, Paragraphs & Text Formatting',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Text is the core of most web pages. HTML provides 6 levels of headings (<code>&lt;h1&gt;</code> through <code>&lt;h6&gt;</code>), paragraphs (<code>&lt;p&gt;</code>), and semantic formatting tags like <code>&lt;strong&gt;</code>, <code>&lt;em&gt;</code>, and <code>&lt;mark&gt;</code>.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Headings and Formatting Tags</div>
    <pre><code class="language-html">&lt;h1&gt;Main Article Heading (h1)&lt;/h1&gt;
&lt;h2&gt;Sub-section Heading (h2)&lt;/h2&gt;
&lt;h3&gt;Minor Topic (h3)&lt;/h3&gt;

&lt;p&gt;This is a standard paragraph. You can emphasize text with &lt;strong&gt;bold importance&lt;/strong&gt; or &lt;em&gt;italic emphasis&lt;/em&gt;. You can also highlight terms using &lt;mark&gt;mark tag&lt;/mark&gt;.&lt;/p&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Rendered Output:</div>
<h1 style="font-size:1.5rem;font-weight:900;margin:0 0 6px 0;">Main Article Heading (h1)</h1>
<h2 style="font-size:1.25rem;font-weight:800;margin:0 0 6px 0;">Sub-section Heading (h2)</h2>
<h3 style="font-size:1.1rem;font-weight:700;margin:0 0 8px 0;">Minor Topic (h3)</h3>
<p style="margin:0;">This is a standard paragraph. You can emphasize text with <strong>bold importance</strong> or <em>italic emphasis</em>. You can also highlight terms using <mark>mark tag</mark>.</p>
    </div>
</div>

<div class="tutorial-tip">
    <strong>SEO & Accessibility Rule:</strong> Only use <strong>one <code>&lt;h1&gt;</code> tag per page</strong>! Search engines like Google treat <code>&lt;h1&gt;</code> as the primary topic of the page. Use <code>&lt;h2&gt;</code> and <code>&lt;h3&gt;</code> for subtopics.
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which heading tag represents the highest and most important level of heading?',
                'answer' => '<h1>',
                'options' => ['<h1>', '<h6>', '<heading>', '<head>']
            ]
        ]
    ],
    [
        'order' => 3,
        'section' => 'HTML Elements',
        'name' => 'HTML Links (<a>) & Hyperlink Attributes',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Hyperlinks are what connect the web together. In HTML, the <code>&lt;a&gt;</code> (anchor) tag creates a clickable link to another webpage, a file, an email address, or a section on the same page.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Modern Hyperlinks</div>
    <pre><code class="language-html">&lt;!-- 1. Standard web link --&gt;
&lt;a href="https://google.com" target="_blank" rel="noopener noreferrer"&gt;Visit Google (Opens in new tab)&lt;/a&gt;

&lt;br&gt;&lt;br&gt;

&lt;!-- 2. Email link --&gt;
&lt;a href="mailto:support@nectradigital.com"&gt;Email Support Team&lt;/a&gt;

&lt;br&gt;&lt;br&gt;

&lt;!-- 3. Telephone link for mobile phones --&gt;
&lt;a href="tel:+18005550199"&gt;Call Us: +1 (800) 555-0199&lt;/a&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Rendered Output:</div>
<a href="https://google.com" target="_blank" style="color:#0284c7;text-decoration:underline;">Visit Google (Opens in new tab)</a><br><br>
<a href="mailto:support@nectradigital.com" style="color:#0284c7;text-decoration:underline;">Email Support Team</a>
    </div>
</div>

<div class="tutorial-note">
    <strong>Security Best Practice:</strong> Whenever you use <code>target="_blank"</code> to open external links in a new browser tab, always add <code>rel="noopener noreferrer"</code>. This prevents malicious external sites from hijacking the opening tab through the <code>window.opener</code> API.
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which attribute specifies the destination URL of a link?',
                'answer' => 'href',
                'options' => ['href', 'src', 'link', 'url']
            ]
        ]
    ],
    [
        'order' => 4,
        'section' => 'HTML Elements',
        'name' => 'HTML Images (<img>) & Responsive Media',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Images make websites visually engaging. In HTML, images are embedded using the self-closing <code>&lt;img&gt;</code> tag. It requires two mandatory attributes: <code>src</code> (path to image file) and <code>alt</code> (text description).</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Embedding Images with alt and lazy loading</div>
    <pre><code class="language-html">&lt;img 
    src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=500" 
    alt="Developer coding on a laptop with modern IDE"
    width="500" 
    height="330"
    loading="lazy"
    style="border-radius: 8px; max-width: 100%; height: auto;"&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Attribute Roles:</div>
- src: Direct URL or relative file path to the graphic.<br>
- alt: Text description read by screen readers for visually impaired users and shown if the image fails to load.<br>
- loading="lazy": Defers loading image until user scrolls near it, improving performance!
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Why is the "alt" attribute required on <img> tags?',
                'answer' => 'For accessibility (screen readers) and displaying fallback text if image fails to load',
                'options' => ['For accessibility (screen readers) and displaying fallback text if image fails to load', 'To change the image dimensions', 'To link the image to another website', 'To compress the image file']
            ]
        ]
    ],
    [
        'order' => 5,
        'section' => 'HTML Elements',
        'name' => 'HTML Lists (<ul>, <ol>, <dl>)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">HTML provides three kinds of lists: <strong>Unordered lists</strong> (bulleted), <strong>Ordered lists</strong> (numbered), and <strong>Description lists</strong> (terms and definitions).</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Unordered vs Ordered Lists</div>
    <pre><code class="language-html">&lt;h3&gt;Ingredients (Unordered - Bulleted)&lt;/h3&gt;
&lt;ul&gt;
    &lt;li&gt;2 cups Flour&lt;/li&gt;
    &lt;li&gt;1 cup Sugar&lt;/li&gt;
    &lt;li&gt;3 large Eggs&lt;/li&gt;
&lt;/ul&gt;

&lt;h3&gt;Recipe Steps (Ordered - Numbered)&lt;/h3&gt;
&lt;ol&gt;
    &lt;li&gt;Preheat oven to 350°F (175°C).&lt;/li&gt;
    &lt;li&gt;Mix ingredients in a large bowl.&lt;/li&gt;
    &lt;li&gt;Bake for 30 minutes until golden.&lt;/li&gt;
&lt;/ol&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Rendered Output:</div>
<strong>Ingredients:</strong>
<ul style="margin:4px 0 12px 20px;"><li>2 cups Flour</li><li>1 cup Sugar</li><li>3 large Eggs</li></ul>
<strong>Recipe Steps:</strong>
<ol style="margin:4px 0 0 20px;"><li>Preheat oven to 350°F</li><li>Mix ingredients</li><li>Bake for 30 minutes</li></ol>
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which tag creates a numbered (ordered) list?',
                'answer' => '<ol>',
                'options' => ['<ol>', '<ul>', '<dl>', '<list>']
            ]
        ]
    ],
    [
        'order' => 6,
        'section' => 'HTML Elements',
        'name' => 'HTML Tables (<table>, <tr>, <th>, <td>)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Tables arrange tabular data into structured horizontal rows and vertical columns. In modern web standards, tables should be used exclusively for displaying tabular data (like pricing plans, schedules, financial statements)—never for overall page layout!</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Clean Semantic Table</div>
    <pre><code class="language-html">&lt;table style="width:100%; border-collapse:collapse; text-align:left;"&gt;
    &lt;thead&gt;
        &lt;tr style="background:#04AA6D; color:#fff;"&gt;
            &lt;th style="padding:10px;"&gt;Language&lt;/th&gt;
            &lt;th style="padding:10px;"&gt;Role&lt;/th&gt;
            &lt;th style="padding:10px;"&gt;Popularity&lt;/th&gt;
        &lt;/tr&gt;
    &lt;/thead&gt;
    &lt;tbody&gt;
        &lt;tr style="border-bottom:1px solid #ddd;"&gt;
            &lt;td style="padding:10px;"&gt;JavaScript&lt;/td&gt;
            &lt;td style="padding:10px;"&gt;Client &amp; Server Web&lt;/td&gt;
            &lt;td style="padding:10px;"&gt;#1 Web Language&lt;/td&gt;
        &lt;/tr&gt;
        &lt;tr style="border-bottom:1px solid #ddd;"&gt;
            &lt;td style="padding:10px;"&gt;Python&lt;/td&gt;
            &lt;td style="padding:10px;"&gt;AI, Data &amp; Backend&lt;/td&gt;
            &lt;td style="padding:10px;"&gt;Top General Purpose&lt;/td&gt;
        &lt;/tr&gt;
    &lt;/tbody&gt;
&lt;/table&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Rendered Table:</div>
Click "Try it Yourself »" to see and edit the live table styling!
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which tag defines a table row?',
                'answer' => '<tr>',
                'options' => ['<tr>', '<td>', '<th>', '<row>']
            ],
            [
                'question' => 'Which tag is used for table header cells (bold, centered by default)?',
                'answer' => '<th>',
                'options' => ['<th>', '<td>', '<head>', '<tr>']
            ]
        ]
    ],
    [
        'order' => 7,
        'section' => 'HTML5 Structure',
        'name' => 'Semantic Elements & Modern HTML5 Layout',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">A <strong>semantic element</strong> clearly describes its meaning to both the browser and the developer (e.g. <code>&lt;header&gt;</code>, <code>&lt;nav&gt;</code>, <code>&lt;main&gt;</code>, <code>&lt;article&gt;</code>, <code>&lt;section&gt;</code>, <code>&lt;footer&gt;</code>), compared to generic non-semantic tags like <code>&lt;div&gt;</code> and <code>&lt;span&gt;</code>.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Semantic Web Page Structure</div>
    <pre><code class="language-html">&lt;header&gt;
    &lt;h1&gt;My Tech Blog&lt;/h1&gt;
    &lt;nav&gt;
        &lt;a href="#home"&gt;Home&lt;/a&gt; | &lt;a href="#articles"&gt;Articles&lt;/a&gt; | &lt;a href="#about"&gt;About&lt;/a&gt;
    &lt;/nav&gt;
&lt;/header&gt;

&lt;main&gt;
    &lt;article&gt;
        &lt;h2&gt;Why Learn Semantic HTML5?&lt;/h2&gt;
        &lt;p&gt;Semantic markup drastically improves SEO ranking and enables screen readers to navigate effortlessly.&lt;/p&gt;
    &lt;/article&gt;
&lt;/main&gt;

&lt;footer&gt;
    &lt;p&gt;&copy; 2026 Learn.Nectra. All rights reserved.&lt;/p&gt;
&lt;/footer&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Architecture Diagram:</div>
[ HEADER &amp; NAV ]<br>
[ MAIN (contains ARTICLES &amp; SECTIONS) ]<br>
[ ASIDE (Sidebar) ]<br>
[ FOOTER ]
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which semantic element should contain the primary unique content of the page?',
                'answer' => '<main>',
                'options' => ['<main>', '<section>', '<content>', '<div>']
            ]
        ]
    ],
    [
        'order' => 8,
        'section' => 'HTML Forms',
        'name' => 'HTML Forms & Modern Input Types',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">An HTML <strong>form</strong> collects user input—such as account registrations, search queries, login credentials, and payments—and sends it to a server for processing.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Modern User Registration Form</div>
    <pre><code class="language-html">&lt;form action="register" method="POST" style="max-width:350px; display:flex; flex-direction:column; gap:10px;"&gt;
    &lt;label for="username"&gt;Username:&lt;/label&gt;
    &lt;input type="text" id="username" name="username" required placeholder="e.g. john_doe" style="padding:8px; border:1px solid #ccc; border-radius:4px;"&gt;

    &lt;label for="userEmail"&gt;Email Address:&lt;/label&gt;
    &lt;input type="email" id="userEmail" name="email" required placeholder="you@domain.com" style="padding:8px; border:1px solid #ccc; border-radius:4px;"&gt;

    &lt;label for="userPass"&gt;Password:&lt;/label&gt;
    &lt;input type="password" id="userPass" name="password" required minlength="8" style="padding:8px; border:1px solid #ccc; border-radius:4px;"&gt;

    &lt;button type="submit" style="background:#04AA6D; color:#fff; padding:10px; border:none; border-radius:4px; font-weight:bold; cursor:pointer;"&gt;Create Account&lt;/button&gt;
&lt;/form&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Form Features:</div>
- type="email": automatically validates "@" and domain structure on submit.<br>
- required: blocks form submission if field is left empty.<br>
- minlength="8": enforces client-side password security rules.
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which input type masks user input with dots or asterisks for passwords?',
                'answer' => 'type="password"',
                'options' => ['type="password"', 'type="secret"', 'type="hidden"', 'type="text"']
            ]
        ]
    ],
    [
        'order' => 9,
        'section' => 'HTML5 Media',
        'name' => 'HTML5 Audio & Video Embedding',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Before HTML5, playing audio or video in a browser required third-party plugins like Flash. HTML5 introduced native <code>&lt;video&gt;</code> and <code>&lt;audio&gt;</code> elements that work seamlessly across desktop, tablets, and smartphones.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Native HTML5 Video Player</div>
    <pre><code class="language-html">&lt;video width="480" height="270" controls poster="video-thumbnail.jpg" style="border-radius:8px; max-width:100%;"&gt;
    &lt;source src="movie.mp4" type="video/mp4"&gt;
    &lt;source src="movie.webm" type="video/webm"&gt;
    Your browser does not support the video tag.
&lt;/video&gt;

&lt;br&gt;&lt;br&gt;

&lt;audio controls&gt;
    &lt;source src="podcast.mp3" type="audio/mpeg"&gt;
    Your browser does not support the audio element.
&lt;/audio&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Attributes:</div>
- controls: Displays Play, Pause, Volume, and Fullscreen buttons.<br>
- autoplay: Starts playback automatically (usually requires muted attribute).<br>
- loop: Automatically repeats the media when finished.
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which attribute must be added to <video> to display Play, Pause, and Volume controls?',
                'answer' => 'controls',
                'options' => ['controls', 'buttons="true"', 'panel', 'media="player"']
            ]
        ]
    ],
    [
        'order' => 10,
        'section' => 'HTML Best Practices',
        'name' => 'HTML5 SEO & Accessibility (a11y) Best Practices',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Writing high-quality HTML isn't just about rendering things on a screen; it's about ensuring your content is accessible to people with disabilities and discoverable by search engines like Google.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Checklist for Production-Grade HTML</div>
    <pre><code class="language-html">&lt;!-- 1. Include responsive viewport and charset meta tags in &lt;head&gt; --&gt;
&lt;meta charset="UTF-8"&gt;
&lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;

&lt;!-- 2. Every form input must have a linked label via 'for' and 'id' --&gt;
&lt;label for="userEmail"&gt;Your Email:&lt;/label&gt;
&lt;input type="email" id="userEmail" name="email"&gt;

&lt;!-- 3. Use buttons for actions, anchors for navigation --&gt;
&lt;button type="button" onclick="openMenu()"&gt;Open Drawer&lt;/button&gt;
&lt;a href="dashboard"&gt;Go to Dashboard&lt;/a&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Production Standards:</div>
✓ Clean semantic landmarks (<header>, <nav>, <main>, <footer>)<br>
✓ Descriptive alt attributes on all informative images<br>
✓ Proper heading hierarchy without skipping levels (h1 -> h2 -> h3)
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'When should an <a> tag be used instead of a <button> tag?',
                'answer' => 'When navigating to a new URL or page destination',
                'options' => ['When navigating to a new URL or page destination', 'When submitting an AJAX form', 'When opening a JavaScript modal', 'Always interchangeably']
            ]
        ]
    ]
];

foreach ($htmlChapters as $ch) {
    upsertChapter($pdo, $htmlId, $ch['order'], $ch['section'], $ch['name'], $ch['content'], $ch['quizzes']);
}
echo "Seeded 10 HTML5 Chapters.\n";

// -----------------------------------------------------------------------------
// CSS3 CURRICULUM (10 CHAPTERS)
// -----------------------------------------------------------------------------
$cssChapters = [
    [
        'order' => 1,
        'section' => 'CSS Basics',
        'name' => 'CSS3 Introduction & How to Apply Styles',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>CSS</strong> stands for <strong>Cascading Style Sheets</strong>. It is the language used to style and layout web pages—controlling colors, typography, margins, layouts, animations, and responsive screen adaptations across devices.</p>

<div class="tutorial-tip">
    <strong>3 Ways to Insert CSS:</strong>
    <ol>
        <li><strong>External CSS (Best Practice):</strong> Inside a separate <code>.css</code> file linked via <code>&lt;link rel="stylesheet" href="style.css"&gt;</code>.</li>
        <li><strong>Internal CSS:</strong> Inside a <code>&lt;style&gt;</code> block within the HTML <code>&lt;head&gt;</code>.</li>
        <li><strong>Inline CSS:</strong> Directly inside an HTML tag using <code>style="..."</code>.</li>
    </ol>
</div>

<div class="w3-example-box">
    <div class="w3-example-title">Example: CSS Rule Structure</div>
    <pre><code class="language-css">/* Selector { property: value; } */
body {
    background-color: #f8fafc;
    color: #1e293b;
    font-family: Arial, sans-serif;
    margin: 0;
}

h1 {
    color: #04AA6D;
    font-size: 2rem;
    text-align: center;
}</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Anatomy of a CSS Rule:</div>
- Selector: Targets which HTML elements to style (e.g. h1, body, .btn).<br>
- Declaration: Property (e.g. color) and Value (e.g. #04AA6D) separated by a colon : and terminated with a semicolon ;.
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'What does CSS stand for?',
                'answer' => 'Cascading Style Sheets',
                'options' => ['Cascading Style Sheets', 'Creative Style System', 'Computer Screen Styling', 'Colorful Style Sheets']
            ],
            [
                'question' => 'Where in an HTML document is the correct place to reference an external style sheet?',
                'answer' => 'Inside the <head> section',
                'options' => ['Inside the <head> section', 'At the bottom of the <body>', 'Inside the <footer>', 'Directly in the <html> tag']
            ]
        ]
    ],
    [
        'order' => 2,
        'section' => 'CSS Basics',
        'name' => 'CSS Selectors Masterclass (Element, Class, ID)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">CSS selectors target the HTML elements you want to style. Understanding selector specificity is essential to avoid conflicting styles.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Major Selector Types</div>
    <pre><code class="language-css">/* 1. Element Selector (styles all <p> elements) */
p {
    line-height: 1.6;
}

/* 2. Class Selector (prefixed with a dot .) - Reusable! */
.highlight-box {
    background-color: #ecfdf5;
    border-left: 4px solid #04AA6D;
    padding: 12px;
}

/* 3. ID Selector (prefixed with a hash #) - Unique per page! */
#mainNavigation {
    background-color: #0f172a;
    padding: 16px;
}

/* 4. Pseudo-class Selector (hover interaction) */
.btn-submit:hover {
    background-color: #038d5a;
    cursor: pointer;
}</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Selector Rules:</div>
- Class (.): Can be used on multiple elements across the page.<br>
- ID (#): Must be unique; only one element on the page should have that ID.<br>
- :hover: Triggers styles when user hovers their mouse pointer over the element.
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which character is used to select elements with a specific class in CSS?',
                'answer' => '. (period / dot)',
                'options' => ['. (period / dot)', '# (hash)', '* (asterisk)', '& (ampersand)']
            ],
            [
                'question' => 'Which character is used to select an element by its ID?',
                'answer' => '# (hash)',
                'options' => ['# (hash)', '.', '$', '@']
            ]
        ]
    ],
    [
        'order' => 3,
        'section' => 'CSS Styling',
        'name' => 'CSS Colors, Hex, RGB & Background Gradients',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">CSS offers several ways to define colors: color names (e.g. <code>red</code>), Hexadecimal codes (e.g. <code>#04AA6D</code>), RGB/RGBA values (e.g. <code>rgba(4, 170, 109, 0.9)</code>), and smooth linear or radial gradients.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Colors and Gradient Backgrounds</div>
    <pre><code class="language-css">.gradient-hero {
    /* Linear gradient from emerald to cyan */
    background: linear-gradient(135deg, #04AA6D 0%, #0284c7 100%);
    color: #ffffff;
    padding: 40px 20px;
    border-radius: 12px;
    text-align: center;
}

.semi-transparent-card {
    /* RGBA allows alpha opacity channel from 0 (invisible) to 1 (solid) */
    background-color: rgba(255, 255, 255, 0.85);
    color: #0f172a;
    backdrop-filter: blur(8px);
}</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
Test linear-gradient in Try it Yourself to see smooth color transitions rendered by modern GPU rendering!
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'What does the "A" in RGBA stand for?',
                'answer' => 'Alpha (transparency / opacity)',
                'options' => ['Alpha (transparency / opacity)', 'Accent', 'Adjustment', 'Array']
            ]
        ]
    ],
    [
        'order' => 4,
        'section' => 'CSS Layout',
        'name' => 'CSS The Box Model (Margin, Border, Padding, Content)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Every single HTML element rendered on a web page is treated by the browser as a rectangular box. The <strong>CSS Box Model</strong> consists of four distinct layers: Content, Padding, Border, and Margin.</p>

<div class="tutorial-note">
    <strong>Picture Frame Analogy:</strong>
    <ul>
        <li><strong>Content:</strong> The photograph itself (text, image).</li>
        <li><strong>Padding:</strong> The white mat border around the photograph inside the frame.</li>
        <li><strong>Border:</strong> The wooden frame encasing everything.</li>
        <li><strong>Margin:</strong> The blank wall space between this frame and the next frame on the wall!</li>
    </ul>
</div>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Mastering box-sizing: border-box</div>
    <pre><code class="language-css">/* Universal reset for predictable sizing */
* {
    box-sizing: border-box;
}

.box-model-demo {
    width: 300px;
    padding: 20px;            /* Inner spacing inside border */
    border: 3px solid #04AA6D;/* Enclosing line */
    margin: 30px auto;        /* Outer spacing away from siblings */
    background-color: #f8fafc;
}</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Box Model Layers:</div>
[ MARGIN (Outer space around element) ]<br>
  [ BORDER (Line around padding &amp; content) ]<br>
    [ PADDING (Inner space between border and content) ]<br>
      [ CONTENT (Text, images, child elements) ]
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which CSS property creates space inside the border, between the border and the content?',
                'answer' => 'padding',
                'options' => ['padding', 'margin', 'spacing', 'gap']
            ],
            [
                'question' => 'Which CSS property creates space outside the element, pushing other elements away?',
                'answer' => 'margin',
                'options' => ['margin', 'padding', 'outline', 'border']
            ]
        ]
    ],
    [
        'order' => 5,
        'section' => 'CSS Styling',
        'name' => 'CSS Typography, Web Fonts & Text Styling',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Typography determines how readable and professional your web application looks. CSS controls font families, weights, sizes, line heights, and letter spacing.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Typography Styling</div>
    <pre><code class="language-css">/* Importing Google Font */
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap");

body {
    font-family: "Inter", -apple-system, BlinkMacSystemFont, sans-serif;
    font-size: 1rem;       /* 16px default */
    line-height: 1.65;     /* Line spacing for comfortable reading */
    color: #334155;
}

.lead-title {
    font-size: 2.25rem;
    font-weight: 900;
    letter-spacing: -0.03em;
    color: #0f172a;
    text-transform: capitalize;
}</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Best Practices:</div>
- Use relative units like rem instead of fixed pixels (px) so text scales when users adjust browser settings.<br>
- Maintain a line-height between 1.5 and 1.7 for long paragraph blocks.
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which CSS property controls the boldness of text?',
                'answer' => 'font-weight',
                'options' => ['font-weight', 'font-style', 'text-bold', 'font-size']
            ]
        ]
    ],
    [
        'order' => 6,
        'section' => 'CSS Layout',
        'name' => 'CSS Display Property (block, inline, inline-block, none)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <code>display</code> property specifies how an element behaves in the document flow. The four fundamental display types are <code>block</code>, <code>inline</code>, <code>inline-block</code>, and <code>none</code>.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Display Comparisons</div>
    <pre><code class="language-css">/* 1. Block: starts on new line, takes 100% available width */
.block-element {
    display: block;
    width: 100%;
}

/* 2. Inline: flows with text, respects NO top/bottom margin or width */
.inline-badge {
    display: inline;
}

/* 3. Inline-block: flows inline like text, BUT respects width, height, padding! */
.nav-pill {
    display: inline-block;
    padding: 8px 16px;
    background: #04AA6D;
    color: #fff;
    border-radius: 4px;
}

/* 4. None: hides element completely from view and document flow */
.hidden-modal {
    display: none;
}</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Key Difference:</div>
display: inline ignores width &amp; height.<br>
display: inline-block sits next to siblings while fully honoring width, height, and margins!
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which display value completely hides an element from the page layout?',
                'answer' => 'display: none;',
                'options' => ['display: none;', 'display: hidden;', 'visibility: off;', 'display: invisible;']
            ]
        ]
    ],
    [
        'order' => 7,
        'section' => 'CSS Layout',
        'name' => 'CSS Positioning (static, relative, absolute, fixed, sticky)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <code>position</code> property specifies the positioning method used for an element. Combined with <code>top</code>, <code>bottom</code>, <code>left</code>, <code>right</code>, and <code>z-index</code>, it allows you to pin navigation bars, position tooltips, and layer UI components.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Relative and Absolute Positioning</div>
    <pre><code class="language-css">/* Parent container acts as positioning reference */
.card-container {
    position: relative;
    width: 320px;
    padding: 24px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
}

/* Child badge positioned at exact top-right corner of parent */
.badge-pro {
    position: absolute;
    top: -10px;
    right: 12px;
    background-color: #04AA6D;
    color: #ffffff;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: bold;
}

/* Sticky header: scrolls normally until top of viewport, then sticks! */
.sticky-nav {
    position: sticky;
    top: 0;
    z-index: 50;
}</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Golden Rule of Absolute Positioning:</div>
An element with position: absolute positions itself relative to the nearest positioned ancestor (an ancestor with position: relative, absolute, or fixed)!
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'What positioning should you place on a parent container so that an absolutely positioned child stays inside it?',
                'answer' => 'position: relative;',
                'options' => ['position: relative;', 'position: static;', 'position: fixed;', 'position: initial;']
            ]
        ]
    ],
    [
        'order' => 8,
        'section' => 'Modern Layout',
        'name' => 'CSS Flexbox Masterclass (One-Dimensional Layout)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>Flexbox</strong> (Flexible Box Layout) is the modern CSS standard for arranging elements along a single dimension—either horizontally as a row or vertically as a column. It makes aligning, centering, and distributing space trivial.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Centering & Spacing with Flexbox</div>
    <pre><code class="language-css">.navbar {
    display: flex;
    justify-content: space-between; /* Pushes brand to left, links to right */
    align-items: center;            /* Perfect vertical centering */
    padding: 1rem 2rem;
    background-color: #0f172a;
    color: #ffffff;
}

.hero-center {
    display: flex;
    justify-content: center;        /* Centers horizontally */
    align-items: center;            /* Centers vertically */
    min-height: 400px;
    gap: 1.5rem;                    /* Clean spacing between items */
}</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Core Flexbox Properties:</div>
- justify-content: controls alignment along main axis (flex-start, center, flex-end, space-between).<br>
- align-items: controls alignment along cross axis (flex-start, center, flex-end, stretch).<br>
- gap: adds spacing between flex items without annoying margin hacks!
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which CSS Flexbox property aligns items along the main horizontal axis?',
                'answer' => 'justify-content',
                'options' => ['justify-content', 'align-items', 'flex-direction', 'align-content']
            ],
            [
                'question' => 'Which property adds space between flex items without applying margins?',
                'answer' => 'gap',
                'options' => ['gap', 'space', 'between', 'gutter']
            ]
        ]
    ],
    [
        'order' => 9,
        'section' => 'Modern Layout',
        'name' => 'CSS Grid Masterclass (Two-Dimensional Layout)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">While Flexbox is designed for one-dimensional layouts (rows OR columns), <strong>CSS Grid</strong> is a full two-dimensional layout system designed for rows AND columns simultaneously.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Responsive Auto-Fitting Grid</div>
    <pre><code class="language-css">.card-grid {
    display: grid;
    /* Automatically fits columns based on screen width; wraps without media queries! */
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    padding: 2rem;
}

.card-item {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Why auto-fit + minmax is Magic:</div>
On desktop screens, it automatically displays 3 or 4 columns. On tablets, it shrinks to 2. On smartphones, it stacks to 1 column cleanly—without writing a single media query!
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'What is the main difference between Flexbox and CSS Grid?',
                'answer' => 'Flexbox is 1-dimensional (row or column); Grid is 2-dimensional (rows and columns simultaneously)',
                'options' => ['Flexbox is 1-dimensional (row or column); Grid is 2-dimensional (rows and columns simultaneously)', 'Grid is only for tables', 'Flexbox is deprecated in CSS3', 'There is no difference']
            ]
        ]
    ],
    [
        'order' => 10,
        'section' => 'Responsive Design',
        'name' => 'CSS Responsive Web Design & Media Queries',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Responsive Web Design (RWD) ensures that your website looks beautiful and functions smoothly on all screen sizes: desktop monitors, laptops, tablets, and smartphones.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Mobile-First Media Queries</div>
    <pre><code class="language-css">/* Base mobile styles (Default for smartphones) */
.content-wrapper {
    display: flex;
    flex-direction: column; /* Stacked vertically on phones */
    padding: 1rem;
}

/* Tablet screens: 768px and wider */
@media (min-width: 768px) {
    .content-wrapper {
        flex-direction: row; /* Switch to side-by-side columns */
        padding: 2rem;
    }
}

/* Desktop screens: 1024px and wider */
@media (min-width: 1024px) {
    .content-wrapper {
        max-width: 1200px;
        margin: 0 auto;     /* Centered on wide screens */
    }
}</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Mobile-First Philosophy:</div>
Write the default CSS for mobile screens first, then use @media (min-width: ...) to progressively enhance the layout as screens get larger!
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'What CSS rule is used to apply styles conditionally based on screen width?',
                'answer' => '@media',
                'options' => ['@media', '@screen', '@responsive', '@device']
            ]
        ]
    ]
];

foreach ($cssChapters as $ch) {
    upsertChapter($pdo, $cssId, $ch['order'], $ch['section'], $ch['name'], $ch['content'], $ch['quizzes']);
}
echo "Seeded 10 CSS3 Chapters.\n";

// -----------------------------------------------------------------------------
// SQL CURRICULUM (10 CHAPTERS)
// -----------------------------------------------------------------------------
$sqlChapters = [
    [
        'order' => 1,
        'section' => 'SQL Basics',
        'name' => 'SQL Introduction & Relational Database Basics',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>SQL</strong> (Structured Query Language) is the global standard programming language used to communicate with, query, and manipulate relational databases (like MySQL, PostgreSQL, SQLite, Microsoft SQL Server, and Oracle).</p>

<div class="tutorial-note">
    <strong>What is a Database?</strong> A database is an organized collection of tables. A <strong>Table</strong> looks like an Excel spreadsheet: it has horizontal <strong>Rows</strong> (each row represents a record, like one customer or one order) and vertical <strong>Columns</strong> (attributes, like CustomerName, Email, City).
</div>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Simple SQL SELECT Query</div>
    <pre><code class="language-sql">-- Querying all records from the demo Customers table
SELECT CustomerID, CustomerName, City, Country 
FROM Customers;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Live SQL Runner Output:</div>
Click "Try it Yourself »" to execute this SQL query live in the built-in database sandbox!
    </div>
</div>

<div class="beginner-breakdown">
    <div class="beginner-breakdown-title">Line-by-Line Code Breakdown</div>
    <table class="beginner-breakdown-table">
        <thead><tr><th>SQL Keyword</th><th>Meaning</th></tr></thead>
        <tbody>
            <tr><td class="code-cell">SELECT</td><td>Specifies which columns you want to retrieve.</td></tr>
            <tr><td class="code-cell">FROM Customers</td><td>Specifies the name of the database table to read from.</td></tr>
            <tr><td class="code-cell">; (semicolon)</td><td>The standard way to terminate a SQL statement.</td></tr>
        </tbody>
    </table>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'What does SQL stand for?',
                'answer' => 'Structured Query Language',
                'options' => ['Structured Query Language', 'Standard Question Language', 'Simple Query Logic', 'System Query Layout']
            ],
            [
                'question' => 'Which SQL statement is used to extract data from a database?',
                'answer' => 'SELECT',
                'options' => ['SELECT', 'EXTRACT', 'GET', 'OPEN']
            ]
        ]
    ],
    [
        'order' => 2,
        'section' => 'SQL Querying',
        'name' => 'SQL SELECT & DISTINCT Statements',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <code>SELECT</code> statement is used to select data from a database. If a column contains duplicate values (like many customers from the same country), the <code>SELECT DISTINCT</code> statement returns only unique, distinct values.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: SELECT ALL vs SELECT DISTINCT</div>
    <pre><code class="language-sql">-- 1. Select all countries (may include duplicates)
SELECT Country FROM Customers;

-- 2. Select only unique countries (no duplicates!)
SELECT DISTINCT Country FROM Customers;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Live SQL Sandbox:</div>
Execute in Try it Yourself to see duplicate rows filtered out into unique country entries!
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which keyword is used to return only unique values, eliminating duplicates?',
                'answer' => 'DISTINCT',
                'options' => ['DISTINCT', 'UNIQUE', 'DIFFERENT', 'SOLO']
            ],
            [
                'question' => 'Which symbol is used to select ALL columns from a table?',
                'answer' => '*',
                'options' => ['*', '%', '#', 'ALL']
            ]
        ]
    ],
    [
        'order' => 3,
        'section' => 'SQL Querying',
        'name' => 'SQL WHERE Clause & Comparison Operators',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <code>WHERE</code> clause is used to filter records. It extracts only those records that fulfill a specified condition.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Filtering Customers by Country</div>
    <pre><code class="language-sql">-- Select all customers located in Mexico
SELECT CustomerID, CustomerName, City, Country 
FROM Customers 
WHERE Country = 'Mexico';</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
+------------+--------------------------+-------------+---------+
| CustomerID | CustomerName             | City        | Country |
+------------+--------------------------+-------------+---------+
| 2          | Ana Trujillo Emparedados | Mexico D.F. | Mexico  |
| 3          | Antonio Moreno Taqueria  | Mexico D.F. | Mexico  |
+------------+--------------------------+-------------+---------+
2 row(s) returned.
    </div>
</div>

<div class="beginner-breakdown">
    <div class="beginner-breakdown-title">Supported Comparison Operators</div>
    <table class="beginner-breakdown-table">
        <thead><tr><th>Operator</th><th>Meaning</th></tr></thead>
        <tbody>
            <tr><td class="code-cell">=</td><td>Equal to</td></tr>
            <tr><td class="code-cell">&lt;&gt; or !=</td><td>Not equal to</td></tr>
            <tr><td class="code-cell">&gt;</td><td>Greater than</td></tr>
            <tr><td class="code-cell">&lt;</td><td>Less than</td></tr>
            <tr><td class="code-cell">&gt;=</td><td>Greater than or equal</td></tr>
            <tr><td class="code-cell">LIKE</td><td>Pattern search (e.g. <code>City LIKE 'L%'</code>)</td></tr>
        </tbody>
    </table>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which SQL clause is used to filter records based on a condition?',
                'answer' => 'WHERE',
                'options' => ['WHERE', 'FILTER', 'HAVING', 'LIMIT']
            ]
        ]
    ],
    [
        'order' => 4,
        'section' => 'SQL Querying',
        'name' => 'SQL AND, OR, NOT Logical Operators',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <code>WHERE</code> clause can be combined with <code>AND</code>, <code>OR</code>, and <code>NOT</code> operators to filter records based on multiple conditions.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Combining AND and OR</div>
    <pre><code class="language-sql">-- Select customers from Germany OR Sweden
SELECT CustomerID, CustomerName, Country 
FROM Customers 
WHERE Country = 'Germany' OR Country = 'Sweden';

-- Select customers from Mexico whose CustomerID is 2
SELECT * FROM Customers 
WHERE Country = 'Mexico' AND CustomerID = 2;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Live SQL Runner:</div>
Click "Try it Yourself »" to test logical filtering in real time!
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'With the AND operator, when does the overall condition evaluate to true?',
                'answer' => 'Only when ALL combined conditions are true',
                'options' => ['Only when ALL combined conditions are true', 'When at least one condition is true', 'When no conditions are true', 'Only on numbers']
            ]
        ]
    ],
    [
        'order' => 5,
        'section' => 'SQL Querying',
        'name' => 'SQL ORDER BY (Ascending & Descending Sorting)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <code>ORDER BY</code> keyword is used to sort the result-set in ascending (<code>ASC</code>, default) or descending (<code>DESC</code>) order.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Sorting Products by Price</div>
    <pre><code class="language-sql">-- Sort products from highest price to lowest price
SELECT ProductID, ProductName, Price 
FROM Products 
ORDER BY Price DESC;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
+-----------+-----------------------------+-------+
| ProductID | ProductName                 | Price |
+-----------+-----------------------------+-------+
| 4         | Chef Anton Cajun Seasoning  | 22.0  |
| 2         | Chang                       | 19.0  |
| 1         | Chais                       | 18.0  |
| 3         | Aniseed Syrup               | 10.0  |
+-----------+-----------------------------+-------+
4 row(s) returned.
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which keyword sorts the result set in descending (highest to lowest) order?',
                'answer' => 'DESC',
                'options' => ['DESC', 'ASC', 'DOWN', 'REVERSE']
            ]
        ]
    ],
    [
        'order' => 6,
        'section' => 'SQL Data Modification',
        'name' => 'SQL INSERT INTO (Adding New Records)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <code>INSERT INTO</code> statement is used to insert new records into a database table.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Inserting a New Customer</div>
    <pre><code class="language-sql">-- Insert a new customer record
INSERT INTO Customers (CustomerID, CustomerName, ContactName, City, Country) 
VALUES (6, 'Nectra Digital Labs', 'Alex Taylor', 'Mumbai', 'India');

-- Verify insertion
SELECT * FROM Customers WHERE CustomerID = 6;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Query Output:</div>
Query executed successfully. Rows affected: 1
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which SQL statement is used to insert new data into a database?',
                'answer' => 'INSERT INTO',
                'options' => ['INSERT INTO', 'ADD RECORD', 'UPDATE', 'CREATE ROW']
            ]
        ]
    ],
    [
        'order' => 7,
        'section' => 'SQL Data Modification',
        'name' => 'SQL UPDATE & DELETE (Modifying & Removing Records)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <code>UPDATE</code> statement modifies existing records in a table, and <code>DELETE</code> removes records.</p>

<div class="tutorial-warning">
    <div class="tutorial-mistake-title">⚠️ CRITICAL SQL RULE: Never Forget WHERE!</div>
    <p>Notice the <code>WHERE</code> clause in <code>UPDATE</code> and <code>DELETE</code> statements! The <code>WHERE</code> clause specifies which record(s) should be updated or deleted. <strong>If you omit the WHERE clause, ALL records in the table will be updated or deleted!</strong></p>
</div>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Safe UPDATE and DELETE</div>
    <pre><code class="language-sql">-- 1. Updating specific customer contact name
UPDATE Customers 
SET ContactName = 'Maria Schmidt', City = 'Frankfurt' 
WHERE CustomerID = 1;

-- 2. Deleting specific customer
DELETE FROM Customers 
WHERE CustomerID = 5;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Live Output:</div>
Rows affected: 1
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'What happens if you run "DELETE FROM Customers;" without a WHERE clause?',
                'answer' => 'All records in the Customers table will be deleted',
                'options' => ['All records in the Customers table will be deleted', 'Only the first row will be deleted', 'SQL throws an error and cancels', 'Nothing happens']
            ]
        ]
    ],
    [
        'order' => 8,
        'section' => 'SQL Aggregations',
        'name' => 'SQL Aggregate Functions (COUNT, SUM, AVG, MIN, MAX)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">An aggregate function performs a calculation on a set of values and returns a single summary value.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Aggregate Math Functions</div>
    <pre><code class="language-sql">-- Compute total count, minimum price, maximum price, and average price
SELECT 
    COUNT(*) AS TotalProducts,
    MIN(Price) AS CheapestPrice,
    MAX(Price) AS MostExpensivePrice,
    AVG(Price) AS AveragePrice
FROM Products;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
+---------------+---------------+--------------------+--------------+
| TotalProducts | CheapestPrice | MostExpensivePrice | AveragePrice |
+---------------+---------------+--------------------+--------------+
| 4             | 10.0          | 22.0               | 17.25        |
+---------------+---------------+--------------------+--------------+
1 row(s) returned.
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which aggregate function returns the average value of a numeric column?',
                'answer' => 'AVG()',
                'options' => ['AVG()', 'AVERAGE()', 'MEAN()', 'SUM()']
            ]
        ]
    ],
    [
        'order' => 9,
        'section' => 'SQL Aggregations',
        'name' => 'SQL GROUP BY & HAVING Clauses',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <code>GROUP BY</code> statement groups rows that have the same values into summary rows (like "find the number of customers in each country"). The <code>HAVING</code> clause was added to SQL because the <code>WHERE</code> keyword cannot be used with aggregate functions.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: GROUP BY with HAVING</div>
    <pre><code class="language-sql">-- Count customers in each country, showing only countries with 2 or more customers
SELECT Country, COUNT(*) AS CustomerCount 
FROM Customers 
GROUP BY Country 
HAVING COUNT(*) >= 2;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Result:</div>
+---------+---------------+
| Country | CustomerCount |
+---------+---------------+
| Mexico  | 2             |
+---------+---------------+
1 row(s) returned.
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Why is the HAVING clause used instead of WHERE when filtering groups?',
                'answer' => 'Because WHERE cannot be used directly with aggregate functions (like COUNT, SUM)',
                'options' => ['Because WHERE cannot be used directly with aggregate functions (like COUNT, SUM)', 'HAVING runs faster', 'WHERE only works on strings', 'HAVING is only for MySQL']
            ]
        ]
    ],
    [
        'order' => 10,
        'section' => 'SQL Relational Joins',
        'name' => 'SQL Joins Masterclass (INNER, LEFT, RIGHT JOIN)',
        'content' => <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">A <code>JOIN</code> clause is used to combine rows from two or more tables based on a related column between them (such as a Foreign Key).</p>

<div class="beginner-breakdown">
    <div class="beginner-breakdown-title">Types of SQL Joins</div>
    <table class="beginner-breakdown-table">
        <thead><tr><th>Join Type</th><th>What It Returns</th></tr></thead>
        <tbody>
            <tr><td class="code-cell">INNER JOIN</td><td>Returns records that have matching values in both tables.</td></tr>
            <tr><td class="code-cell">LEFT JOIN</td><td>Returns all records from the left table, and matched records from right table.</td></tr>
            <tr><td class="code-cell">RIGHT JOIN</td><td>Returns all records from the right table, and matched records from left table.</td></tr>
            <tr><td class="code-cell">FULL JOIN</td><td>Returns all records when there is a match in either left or right table.</td></tr>
        </tbody>
    </table>
</div>

<div class="w3-example-box">
    <div class="w3-example-title">Example: INNER JOIN on Related Tables</div>
    <pre><code class="language-sql">-- Query combining Orders and Customers on CustomerID
SELECT 
    Orders.OrderID, 
    Customers.CustomerName, 
    Orders.OrderDate 
FROM Orders
INNER JOIN Customers ON Orders.CustomerID = Customers.CustomerID;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Join Concept:</div>
The ON Orders.CustomerID = Customers.CustomerID condition matches the customer record to their corresponding orders.
    </div>
</div>
HTML,
        'quizzes' => [
            [
                'question' => 'Which JOIN returns records that have matching values in BOTH tables?',
                'answer' => 'INNER JOIN',
                'options' => ['INNER JOIN', 'LEFT JOIN', 'FULL JOIN', 'CROSS JOIN']
            ]
        ]
    ]
];

foreach ($sqlChapters as $ch) {
    upsertChapter($pdo, $sqlId, $ch['order'], $ch['section'], $ch['name'], $ch['content'], $ch['quizzes']);
}
echo "Seeded 10 SQL Chapters.\n";

echo "=== Web Curricula Seeding Complete! ===\n";
