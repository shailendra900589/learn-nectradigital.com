<?php
declare(strict_types=1);
require_once __DIR__ . '/seed_content.php';

$webCurricula = [
    'javascript-tutorial' => [
        [
            'order' => 1,
            'section' => 'JS Basics',
            'name' => 'JavaScript Introduction',
            'content' => '
<p class="lead text-lg font-medium text-slate-700"><strong>JavaScript</strong> is the world\'s most popular programming language. It is the programming language of the Web, enabling rich interactivity, dynamic content updates, and modern full-stack web applications.</p>

<div class="w3-example-box">
    <div class="w3-example-title">JavaScript Example: Dynamic Message</div>
    <pre><code class="language-javascript">// Outputting to developer console
const greeting = "Hello, Developer! Welcome to JavaScript.";
console.log(greeting);</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Console Output:</div>
Hello, Developer! Welcome to JavaScript.
    </div>
</div>
',
            'quizzes' => [
                [
                    'question' => 'Inside which HTML element do we put JavaScript code?',
                    'answer' => '<script>',
                    'options' => ['<script>', '<javascript>', '<js>', '<scripting>']
                ]
            ]
        ],
        [
            'order' => 2,
            'section' => 'JS Basics',
            'name' => 'JavaScript Variables (let, const, var)',
            'content' => '
<p>Modern JavaScript provides <code>const</code> for immutable references and <code>let</code> for block-scoped reassignable variables. Avoid legacy <code>var</code>.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: const vs let</div>
    <pre><code class="language-javascript">const framework = "React";
let version = 19;
version = 20;

console.log(`${framework} next major version is ${version}`);</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Console Output:</div>
React next major version is 20
    </div>
</div>
',
            'quizzes' => [
                [
                    'question' => 'Which keyword declares a block-scoped variable that cannot be reassigned?',
                    'answer' => 'const',
                    'options' => ['const', 'let', 'var', 'final']
                ]
            ]
        ]
    ],
    'html-tutorial' => [
        [
            'order' => 1,
            'section' => 'HTML Basics',
            'name' => 'HTML5 Introduction',
            'content' => '
<p class="lead text-lg font-medium text-slate-700"><strong>HTML</strong> (HyperText Markup Language) is the standard markup language for creating Web pages. It describes the structure of a Web page semantically.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Simple HTML5 Document</div>
    <pre><code class="language-html">&lt;!DOCTYPE html&gt;
&lt;html lang="en"&gt;
&lt;head&gt;
    &lt;title&gt;My First Page&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;h1&gt;My First Heading&lt;/h1&gt;
    &lt;p&gt;My first paragraph.&lt;/p&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Browser View:</div>
<h1 style="font-size:1.5rem;font-weight:bold;margin:0 0 0.5rem 0;">My First Heading</h1>
<p style="margin:0;">My first paragraph.</p>
    </div>
</div>
',
            'quizzes' => [
                [
                    'question' => 'What does HTML stand for?',
                    'answer' => 'HyperText Markup Language',
                    'options' => ['HyperText Markup Language', 'Hyperlink and Text Markup Language', 'Home Tool Markup Language', 'Hyper Transfer Machine Language']
                ]
            ]
        ]
    ],
    'css-tutorial' => [
        [
            'order' => 1,
            'section' => 'CSS Basics',
            'name' => 'CSS3 Introduction',
            'content' => '
<p class="lead text-lg font-medium text-slate-700"><strong>CSS</strong> (Cascading Style Sheets) describes how HTML elements are to be displayed on screen, paper, or in other media. It saves a lot of work by controlling the layout of multiple web pages all at once.</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Basic CSS Rule</div>
    <pre><code class="language-css">body {
    background-color: #f8fafc;
    font-family: Arial, sans-serif;
}
h1 {
    color: #1d4ed8;
    text-align: center;
}</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Rendered Result:</div>
<div style="background:#f8fafc;padding:10px;border-radius:4px;"><h1 style="color:#1d4ed8;text-align:center;font-size:1.25rem;font-weight:bold;">Styled Heading</h1></div>
    </div>
</div>
',
            'quizzes' => [
                [
                    'question' => 'What does CSS stand for?',
                    'answer' => 'Cascading Style Sheets',
                    'options' => ['Cascading Style Sheets', 'Creative Style System', 'Computer Style Sheets', 'Colorful Style Sheets']
                ]
            ]
        ]
    ],
    'sql-tutorial' => [
        [
            'order' => 1,
            'section' => 'SQL Basics',
            'name' => 'SQL Introduction',
            'content' => '
<p class="lead text-lg font-medium text-slate-700"><strong>SQL</strong> (Structured Query Language) is a standard language for storing, manipulating, and retrieving data in relational databases such as MySQL, SQL Server, Oracle, and PostgreSQL.</p>

<div class="w3-example-box">
    <div class="w3-example-title">SQL Example: SELECT Query</div>
    <pre><code class="language-sql">-- Retrieve active courses ordered by date
SELECT id, title, slug, created_at
FROM courses
WHERE title LIKE \'%Tutorial%\'
ORDER BY id ASC;</code></pre>
    <div class="tutorial-output">
        <div class="tutorial-output-label">Query Results:</div>
| id | title            | slug                      |
|----|------------------|---------------------------|
| 1  | PHP Tutorial     | php-tutorials             |
| 3  | Python Tutorial  | python-complete-tutorials |
| 4  | JavaScript Tutorial | javascript-tutorial   |
    </div>
</div>
',
            'quizzes' => [
                [
                    'question' => 'Which SQL statement is used to extract data from a database?',
                    'answer' => 'SELECT',
                    'options' => ['SELECT', 'EXTRACT', 'GET', 'OPEN']
                ]
            ]
        ]
    ]
];

foreach ($webCurricula as $slug => $chapters) {
    $cid = $courseIds[$slug] ?? null;
    if (!$cid) continue;
    echo "Seeding {$slug} (ID: $cid)...\n";
    foreach ($chapters as $chap) {
        upsertChapter(
            $pdo,
            $cid,
            $chap['order'],
            $chap['section'],
            $chap['name'],
            $chap['content'],
            $chap['quizzes']
        );
        echo "  [OK] Chapter {$chap['order']}: {$chap['name']}\n";
    }
}
echo "All web curricula seeded successfully!\n";
