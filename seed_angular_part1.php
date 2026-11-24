<?php
// seed_angular_part1.php: Chapters 1 to 12

// -------------------------------------------------------------
// Chapter 1
// -------------------------------------------------------------
$content1 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>AngularJS</strong> (v1.x) is an open-source, front-end JavaScript MVC (Model-View-Controller) / MVVM (Model-View-ViewModel) framework maintained by Google. It extends static HTML into a dynamic web language using declarative HTML templates, automatic <strong>Two-Way Data Binding</strong>, modular dependency injection, and reusable custom directives.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Core Architecture: Traditional Web vs. AngularJS SPA</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Feature</th>
                <th style="width: 35%;">Traditional jQuery / Vanilla JS</th>
                <th style="width: 40%;">AngularJS Architecture</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Data Binding</td>
                <td>Imperative DOM updates (<code>$('#name').text(val)</code>)</td>
                <td><strong>Declarative Two-Way Binding:</strong> UI and model synchronize automatically via dirty-checking.</td>
            </tr>
            <tr>
                <td class="code-cell">Application Structure</td>
                <td>Spaghetti event listeners &amp; selectors</td>
                <td><strong>Structured MVC / MVVM:</strong> Clean separation between Scope (Model), Template (View), and Controller.</td>
            </tr>
            <tr>
                <td class="code-cell">Dependency Management</td>
                <td>Global variables &amp; script tag ordering</td>
                <td><strong>Built-in Dependency Injection:</strong> Components request their dependencies by name.</td>
            </tr>
            <tr>
                <td class="code-cell">DOM Abstraction</td>
                <td>Direct manual DOM manipulation</td>
                <td><strong>Directives:</strong> Extend HTML vocabulary (e.g. <code>ng-repeat</code>, <code>ng-if</code>) without touching raw DOM in controllers.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. The 3 Pillars of AngularJS Architecture</h3>
<p class="text-slate-600 mb-4">AngularJS organizes code into three primary layers:</p>
<ul class="list-disc pl-6 space-y-2 text-slate-600 mb-6">
    <li><strong>The Model:</strong> The raw data objects residing in JavaScript memory (attached to <code>$scope</code>).</li>
    <li><strong>The View:</strong> The live HTML document containing Angular directives (e.g., <code>ng-model</code>, <code>ng-bind</code>, <code>{{ }}</code>).</li>
    <li><strong>The Controller:</strong> JavaScript functions that contain business logic, expose functions, and glue Model data to the View.</li>
</ul>

<div class="w3-example-box">
    <div class="w3-example-title">Live Example: Hello World with Two-Way Data Binding</div>
    <pre><code class="language-html">&lt;div ng-app="introApp" ng-controller="IntroCtrl" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow-md border border-slate-200"&gt;
    &lt;h2 class="text-xl font-bold text-slate-800 mb-2"&gt;Welcome to AngularJS 1.8&lt;/h2&gt;
    &lt;p class="text-slate-600 text-sm mb-4"&gt;Type in the input below to see live two-way data binding in real-time:&lt;/p&gt;

    &lt;label class="block text-xs font-bold uppercase text-slate-500 mb-1"&gt;Your Name:&lt;/label&gt;
    &lt;input type="text" ng-model="userName" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm mb-4" placeholder="Enter your name..."&gt;

    &lt;div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-900"&gt;
        &lt;span class="font-bold"&gt;Hello, {{ userName || 'Guest' }}!&lt;/span&gt;
        &lt;p class="text-xs text-emerald-700 mt-1"&gt;Character Count: {{ userName.length || 0 }}&lt;/p&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('introApp', [])
    .controller('IntroCtrl', function($scope) {
        $scope.userName = 'Alex Developer';
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 1, 'AngularJS Fundamentals', 'AngularJS Introduction, Architecture (MVC/MVVM) & Core Concepts', $content1, 'angularjs, angularjs introduction, mvc architecture, mvvm, angularjs tutorial, two way data binding, dependency injection, directives, angularjs vs jquery');

// -------------------------------------------------------------
// Chapter 2
// -------------------------------------------------------------
$content2 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Setting up AngularJS requires zero complex build pipelines or compilers to get started. You can include AngularJS directly via a CDN script tag in standard HTML5 documents, or manage it through npm/Bower in modern legacy builds.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Environment Setup Options: CDN vs. Package Managers</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Method</th>
                <th style="width: 35%;">Setup Command / Script Tag</th>
                <th style="width: 40%;">Recommended Use Case</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Official CDN (Fastest)</td>
                <td><code>&lt;script src="https://ajax.googleapis.com/.../angular.min.js"&gt;</code></td>
                <td>Instant prototyping, rapid learning playgrounds, and quick production embeds.</td>
            </tr>
            <tr>
                <td class="code-cell">npm / Yarn</td>
                <td><code>npm install angular@1.8.3</code></td>
                <td>Webpack, Gulp, Grunt, or Browserify bundled production applications.</td>
            </tr>
            <tr>
                <td class="code-cell">Angular CLI (Modern)</td>
                <td><code>npm install -g @angular/cli</code></td>
                <td>Only used for modern Angular (v2+); AngularJS 1.x does not use Angular CLI.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Minimal Boilerplate Structure</h3>
<p class="text-slate-600 mb-4">Every standard AngularJS application starts with an HTML5 shell containing the <code>ng-app</code> directive:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Complete Self-Contained AngularJS HTML5 Document</div>
    <pre><code class="language-html">&lt;!DOCTYPE html&gt;
&lt;html lang="en" ng-app="quickStartApp"&gt;
&lt;head&gt;
    &lt;meta charset="UTF-8"&gt;
    &lt;title&gt;AngularJS Starter&lt;/title&gt;
    &lt;script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.8.3/angular.min.js"&gt;&lt;/script&gt;
&lt;/head&gt;
&lt;body ng-controller="MainController" class="p-6 bg-slate-50 font-sans"&gt;
    &lt;div class="max-w-md mx-auto bg-white p-6 rounded-xl shadow border border-slate-200"&gt;
        &lt;h1 class="text-2xl font-black text-slate-800 mb-2"&gt;{{ appTitle }}&lt;/h1&gt;
        &lt;p class="text-slate-600 text-sm mb-4"&gt;Status: &lt;span class="font-bold text-emerald-600"&gt;{{ status }}&lt;/span&gt;&lt;/p&gt;
        &lt;button ng-click="toggleStatus()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-bold transition shadow"&gt;
            Switch Status
        &lt;/button&gt;
    &lt;/div&gt;

    &lt;script&gt;
    angular.module('quickStartApp', [])
        .controller('MainController', function($scope) {
            $scope.appTitle = 'Nectra AngularJS System';
            $scope.status = 'Online & Active';
            $scope.toggleStatus = function() {
                $scope.status = ($scope.status === 'Online & Active') ? 'Paused for Maintenance' : 'Online & Active';
            };
        });
    &lt;/script&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 2, 'AngularJS Fundamentals', 'Development Environment Setup & First Live AngularJS App', $content2, 'angularjs setup, angularjs cdn, angularjs boilerplate, install angularjs, angularjs first app, ng-app, angular module setup, angular cdn link');

// -------------------------------------------------------------
// Chapter 3
// -------------------------------------------------------------
$content3 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">In AngularJS, a <strong>Module</strong> is a container for different parts of an application: controllers, services, filters, and directives. Modules eliminate global namespace pollution and declare dependencies between different features.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Module Creation vs. Module Retrieval Syntax</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 30%;">Syntax Blueprint</th>
                <th style="width: 30%;">Action Performed</th>
                <th style="width: 40%;">Crucial Rule &amp; Warning</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>angular.module('app', [])</code></td>
                <td><strong>Creates (Defines)</strong> a new module with an array of dependencies.</td>
                <td>Always include the brackets <code>[]</code> when defining a module, even if dependencies are empty.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>angular.module('app')</code></td>
                <td><strong>Retrieves (References)</strong> an existing module.</td>
                <td>Omitting brackets gets the existing instance. Calling this before creation throws <code>$injector:nomod</code> error.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Multiple Modules &amp; Dependency Injection</h3>
<p class="text-slate-600 mb-4">Enterprise applications divide features into sub-modules (e.g., <code>authModule</code>, <code>billingModule</code>) that are injected into the root application module:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Root Module Injecting Feature Sub-Modules</div>
    <pre><code class="language-html">&lt;div ng-app="rootPortalApp" ng-controller="PortalController" class="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h2 class="text-xl font-bold text-slate-900 mb-1"&gt;{{ portalName }}&lt;/h2&gt;
    &lt;p class="text-xs text-slate-500 mb-4"&gt;Modular Architecture Demo&lt;/p&gt;

    &lt;div class="grid grid-cols-2 gap-3"&gt;
        &lt;div class="p-3 bg-slate-50 border rounded-lg"&gt;
            &lt;span class="text-xs text-slate-400 block font-bold"&gt;Current Plan&lt;/span&gt;
            &lt;span class="font-bold text-slate-800"&gt;{{ plan }}&lt;/span&gt;
        &lt;/div&gt;
        &lt;div class="p-3 bg-slate-50 border rounded-lg"&gt;
            &lt;span class="text-xs text-slate-400 block font-bold"&gt;User Role&lt;/span&gt;
            &lt;span class="font-bold text-emerald-600"&gt;{{ role }}&lt;/span&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
// 1. Define Sub-Module A (Auth)
angular.module('authModule', [])
    .constant('AUTH_ROLE', 'Senior Architect');

// 2. Define Sub-Module B (Billing)
angular.module('billingModule', [])
    .value('PLAN_TIER', 'Enterprise Cloud Tier');

// 3. Define Main Module injecting Sub-Modules
angular.module('rootPortalApp', ['authModule', 'billingModule'])
    .controller('PortalController', function($scope, AUTH_ROLE, PLAN_TIER) {
        $scope.portalName = 'Nectra Cloud Hub';
        $scope.role = AUTH_ROLE;
        $scope.plan = PLAN_TIER;
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 3, 'AngularJS Fundamentals', 'AngularJS Modules (angular.module) & Application Bootstrapping', $content3, 'angularjs module, angular.module, module dependencies, ng-app bootstrap, angularjs submodules, dependency injection in module');

// -------------------------------------------------------------
// Chapter 4
// -------------------------------------------------------------
$content4 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>Expressions</strong> in AngularJS are JavaScript-like code snippets written inside double curly braces <code>{{ expression }}</code>. They are evaluated against the current <code>$scope</code> context to dynamically output values directly inside the DOM.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">AngularJS Expressions vs. Native JavaScript</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Feature</th>
                <th style="width: 35%;">AngularJS Expressions <code>{{ expr }}</code></th>
                <th style="width: 40%;">Standard JavaScript <code>eval()</code></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Null / Undefined Tolerance</td>
                <td><strong>Forgiving:</strong> <code>{{ a.b.c }}</code> safely outputs empty string instead of crashing.</td>
                <td>Throws <code>TypeError: Cannot read properties of undefined</code>.</td>
            </tr>
            <tr>
                <td class="code-cell">Control Flow Statements</td>
                <td><strong>Not allowed:</strong> No <code>if</code>, <code>for</code>, <code>while</code>, or <code>throw</code> statements.</td>
                <td>Supports all loops and branching control statements.</td>
            </tr>
            <tr>
                <td class="code-cell">Ternary Operators</td>
                <td>Fully supported: <code>{{ isActive ? 'Active' : 'Disabled' }}</code></td>
                <td>Fully supported.</td>
            </tr>
            <tr>
                <td class="code-cell">Filter Piping</td>
                <td>Built-in pipe syntax: <code>{{ price | currency }}</code></td>
                <td>Requires manual wrapper functions.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Live Expression Arithmetic, Strings &amp; Arrays</h3>
<p class="text-slate-600 mb-4">Expressions can compute values, perform string operations, access object properties, and evaluate ternary operators inline:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Rich Expressions with Math, Objects &amp; Safe Fallbacks</div>
    <pre><code class="language-html">&lt;div ng-app="exprApp" ng-init="qty = 3; price = 249; discount = 0.15; user = { fname: 'Sara', verified: true }" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Order Invoice Calculator&lt;/h3&gt;
    
    &lt;div class="space-y-2 text-sm text-slate-700 mb-4"&gt;
        &lt;div class="flex justify-between"&gt;
            &lt;span&gt;Client Name:&lt;/span&gt;
            &lt;strong class="text-slate-900"&gt;{{ user.fname }} ({{ user.verified ? 'Verified ✓' : 'Unverified' }})&lt;/strong&gt;
        &lt;/div&gt;
        &lt;div class="flex justify-between"&gt;
            &lt;span&gt;Unit Price:&lt;/span&gt;
            &lt;span&gt;${{ price }}&lt;/span&gt;
        &lt;/div&gt;
        &lt;div class="flex justify-between"&gt;
            &lt;span&gt;Quantity Selected:&lt;/span&gt;
            &lt;span&gt;{{ qty }} units&lt;/span&gt;
        &lt;/div&gt;
        &lt;div class="flex justify-between text-emerald-600 font-semibold"&gt;
            &lt;span&gt;Subtotal:&lt;/span&gt;
            &lt;span&gt;${{ qty * price }}&lt;/span&gt;
        &lt;/div&gt;
        &lt;div class="flex justify-between text-indigo-600 font-bold border-t pt-2"&gt;
            &lt;span&gt;Total After 15% Discount:&lt;/span&gt;
            &lt;span&gt;${{ (qty * price) * (1 - discount) }}&lt;/span&gt;
        &lt;/div&gt;
    &lt;/div&gt;

    &lt;div class="p-3 bg-slate-50 border rounded-lg text-xs text-slate-500"&gt;
        Null-safety demo: accessing &lt;code&gt;user.address.street&lt;/code&gt; without crash: 
        &lt;strong&gt;"{{ user.address.street || 'No street provided' }}"&lt;/strong&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 4, 'AngularJS Fundamentals', 'Expressions & Interpolation ({{ }}) vs JavaScript Expressions', $content4, 'angularjs expressions, angular interpolation, double curly braces, angular expression vs javascript, safe navigation, ternary in angularjs');

// -------------------------------------------------------------
// Chapter 5
// -------------------------------------------------------------
$content5 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>Two-Way Data Binding</strong> is the flagship innovation of AngularJS. Any modification in the View (such as typing into an <code>&lt;input&gt;</code> with <code>ng-model</code>) immediately updates the JavaScript Model, and any model mutation in JavaScript instantly refreshes the View.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">One-Way Data Binding vs. Two-Way Data Binding</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Pattern</th>
                <th style="width: 35%;">Data Flow Direction</th>
                <th style="width: 40%;">Mechanism in AngularJS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">One-Way Binding</td>
                <td>Model &rarr; View (Read-Only)</td>
                <td><code>{{ title }}</code> or <code>ng-bind="title"</code></td>
            </tr>
            <tr>
                <td class="code-cell">One-Time Binding</td>
                <td>Model &rarr; View (Evaluated once, unbinds)</td>
                <td><code>{{ ::constantData }}</code> (added in AngularJS 1.3)</td>
            </tr>
            <tr>
                <td class="code-cell">Two-Way Binding</td>
                <td>Model &harr; View (Bidirectional Synchronization)</td>
                <td><code>ng-model="formData"</code> on inputs, select, and textarea elements.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Interactive Dual-Binding Masterclass</h3>
<p class="text-slate-600 mb-4">Observe how changing the slider or typing into the text field synchronizes the view state instantly:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Interactive Two-Way Temperature &amp; Text Sync</div>
    <pre><code class="language-html">&lt;div ng-app="bindingDemoApp" ng-controller="BindingCtrl" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-4"&gt;Live Temperature Converter&lt;/h3&gt;

    &lt;label class="block text-xs font-bold uppercase text-slate-500 mb-1"&gt;Celsius: {{ celsius }}&deg;C&lt;/label&gt;
    &lt;input type="range" min="-20" max="100" ng-model="celsius" class="w-full mb-4"&gt;

    &lt;div class="grid grid-cols-2 gap-3 mb-4"&gt;
        &lt;div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-center"&gt;
            &lt;span class="text-xs text-blue-500 block font-bold"&gt;Fahrenheit&lt;/span&gt;
            &lt;span class="text-xl font-black text-blue-900"&gt;{{ (celsius * 9/5) + 32 | number:1 }}&deg;F&lt;/span&gt;
        &lt;/div&gt;
        &lt;div class="p-3 bg-purple-50 border border-purple-200 rounded-lg text-center"&gt;
            &lt;span class="text-xs text-purple-500 block font-bold"&gt;Kelvin&lt;/span&gt;
            &lt;span class="text-xl font-black text-purple-900"&gt;{{ celsius * 1 + 273.15 | number:1 }} K&lt;/span&gt;
        &lt;/div&gt;
    &lt;/div&gt;

    &lt;label class="block text-xs font-bold uppercase text-slate-500 mb-1"&gt;Notes on Weather:&lt;/label&gt;
    &lt;input type="text" ng-model="notes" class="w-full px-3 py-2 border rounded-lg text-sm mb-2" placeholder="e.g. Sunny morning"&gt;
    &lt;p class="text-xs text-slate-500 italic"&gt;Live Mirror: "{{ notes }}"&lt;/p&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('bindingDemoApp', [])
    .controller('BindingCtrl', function($scope) {
        $scope.celsius = 25;
        $scope.notes = 'Pleasant weather';
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 5, 'AngularJS Fundamentals', 'Two-Way Data Binding (ng-model) & The MVVM Design Pattern', $content5, 'angularjs two way data binding, ng-model, mvvm in angularjs, one way binding, model synchronization, live data binding');

// -------------------------------------------------------------
// Chapter 6
// -------------------------------------------------------------
$content6 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <strong>Digest Cycle</strong> is the internal heartbeat of AngularJS. Whenever an event occurs, AngularJS enters a dirty-checking loop (<code>$digest</code>) that evaluates all registered <code>$watch</code> expressions until no model properties have changed.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">The Anatomy of the Digest Loop: $watch, $digest, and $apply</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Method</th>
                <th style="width: 35%;">Role in the Engine</th>
                <th style="width: 40%;">When to Use</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>$scope.$watch()</code></td>
                <td>Registers a watcher listener function on an expression or model property.</td>
                <td>Used when you need to execute custom logic whenever a specific property mutates.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$scope.$digest()</code></td>
                <td>Executes all watchers on the current scope and its children.</td>
                <td>Internal to AngularJS; rarely called directly by developers.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$scope.$apply()</code></td>
                <td>Wraps non-Angular async code (e.g., <code>setTimeout</code>, native XHR) to enter the Angular context.</td>
                <td>Mandatory when updating <code>$scope</code> inside non-Angular callbacks or 3rd-party libraries.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Watching State Changes &amp; $apply with Native Timers</h3>
<p class="text-slate-600 mb-4">Notice how native <code>window.setInterval</code> requires <code>$scope.$apply()</code> to trigger DOM updates, or prefer using the Angular service <code>$interval</code>:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Custom Watcher &amp; $apply Integration</div>
    <pre><code class="language-html">&lt;div ng-app="digestApp" ng-controller="DigestCtrl" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-2"&gt;Digest Cycle &amp; Watcher Demo&lt;/h3&gt;
    
    &lt;div class="flex items-center gap-3 mb-4"&gt;
        &lt;button ng-click="increment()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold shadow"&gt;
            Increment Counter ({{ count }})
        &lt;/button&gt;
        &lt;button ng-click="triggerNativeTimer()" class="px-4 py-2 bg-slate-700 text-white rounded-lg text-sm font-bold shadow"&gt;
            Run Async Timer
        &lt;/button&gt;
    &lt;/div&gt;

    &lt;div class="p-4 bg-slate-50 border rounded-lg text-xs space-y-2"&gt;
        &lt;div&gt;&lt;strong class="text-slate-700"&gt;Audit Log:&lt;/strong&gt;&lt;/div&gt;
        &lt;div class="text-slate-600 font-mono bg-white p-2 rounded border"&gt;{{ logMessage }}&lt;/div&gt;
        &lt;div class="text-slate-500"&gt;Total times $watch fired: &lt;strong&gt;{{ watchCount }}&lt;/strong&gt;&lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('digestApp', [])
    .controller('DigestCtrl', function($scope) {
        $scope.count = 0;
        $scope.watchCount = 0;
        $scope.logMessage = 'System initialized';

        $scope.increment = function() {
            $scope.count++;
        };

        // Manual watcher
        $scope.$watch('count', function(newVal, oldVal) {
            if (newVal !== oldVal) {
                $scope.watchCount++;
                $scope.logMessage = 'Value changed from ' + oldVal + ' to ' + newVal;
            }
        });

        // Triggering $apply from a native async callback
        $scope.triggerNativeTimer = function() {
            $scope.logMessage = 'Timer initiated (waiting 1 sec)...';
            window.setTimeout(function() {
                // Must wrap in $apply so AngularJS knows the model changed
                $scope.$apply(function() {
                    $scope.count += 5;
                    $scope.logMessage = 'Updated via native timer inside $apply()!';
                });
            }, 1000);
        };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 6, 'AngularJS Fundamentals', 'The Digest Cycle, $watch, $digest & $apply Under the Hood', $content6, 'angularjs digest cycle, $watch in angularjs, $digest vs $apply, dirty checking, performance in angularjs, $scope.$apply, async update in angularjs');

// -------------------------------------------------------------
// Chapter 7
// -------------------------------------------------------------
$content7 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">A <strong>Controller</strong> in AngularJS is a constructor function that augments the <code>$scope</code> with initial data models and behavior functions. Controllers manage specific sections of the UI, creating a hierarchy that leverages JavaScript <strong>Prototypal Inheritance</strong>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Scope Prototypal Inheritance &amp; The "Dot Rule"</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Scope Layer</th>
                <th style="width: 35%;">Inheritance Behavior</th>
                <th style="width: 40%;">Crucial Best Practice</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Parent Scope</td>
                <td>Exposes models and functions available to all nested child scopes.</td>
                <td>Always bind to an object property (<code>model.prop</code>) rather than a direct primitive (<code>$scope.prop</code>).</td>
            </tr>
            <tr>
                <td class="code-cell">Child Scope</td>
                <td>Inherits parent properties prototype-style. Reading reads parent; writing primitive shadows parent!</td>
                <td><strong>The "Dot Rule":</strong> If you don't use a dot in <code>ng-model</code> (e.g. <code>user.name</code>), child scopes will hide parent properties.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Nested Controllers &amp; Scope Shadowing</h3>
<p class="text-slate-600 mb-4">See how child scopes inherit from parent scopes, and how the "dot rule" prevents accidental shadowing:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Parent-Child Controller Hierarchy</div>
    <pre><code class="language-html">&lt;div ng-app="scopeDemoApp" ng-controller="ParentCtrl" class="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;div class="border-b pb-4 mb-4"&gt;
        &lt;h3 class="text-base font-bold text-slate-800"&gt;[Parent Scope] Department: {{ department }}&lt;/h3&gt;
        &lt;p class="text-xs text-slate-500"&gt;Shared Budget: &lt;strong class="text-emerald-600"&gt;${{ sharedData.budget }}&lt;/strong&gt;&lt;/p&gt;
    &lt;/div&gt;

    &lt;!-- Child Controller 1 --&gt;
    &lt;div ng-controller="ChildTeamCtrl" class="p-4 bg-slate-50 border rounded-lg mb-4"&gt;
        &lt;h4 class="text-sm font-bold text-slate-900 mb-1"&gt;[Child Scope] Team: {{ teamName }}&lt;/h4&gt;
        &lt;p class="text-xs text-slate-600 mb-2"&gt;Inherited Department: &lt;strong&gt;{{ department }}&lt;/strong&gt;&lt;/p&gt;
        
        &lt;label class="block text-xs font-bold text-slate-500 mb-1"&gt;Adjust Shared Budget (Uses Dot Rule):&lt;/label&gt;
        &lt;input type="number" ng-model="sharedData.budget" class="px-3 py-1.5 border rounded text-sm w-full bg-white mb-2"&gt;
        
        &lt;button ng-click="rewardTeam()" class="px-3 py-1 bg-indigo-600 text-white rounded text-xs font-bold shadow"&gt;
            Add $5,000 Bonus
        &lt;/button&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('scopeDemoApp', [])
    .controller('ParentCtrl', function($scope) {
        $scope.department = 'Engineering Operations';
        $scope.sharedData = { budget: 50000 };
    })
    .controller('ChildTeamCtrl', function($scope) {
        $scope.teamName = 'Frontend Architecture Core';
        $scope.rewardTeam = function() {
            $scope.sharedData.budget += 5000;
        };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 7, 'Controllers & Scope', 'Controllers & $scope Hierarchy (Prototypal Inheritance & Child Scopes)', $content7, 'angularjs controller, $scope in angularjs, scope inheritance, prototypal inheritance, dot rule angularjs, nested controllers');

// -------------------------------------------------------------
// Chapter 8
// -------------------------------------------------------------
$content8 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <strong>$rootScope</strong> is the ultimate parent of all scopes in an application. Additionally, AngularJS provides a built-in event messaging bus via <code>$emit</code> (upwards bubbling), <code>$broadcast</code> (downwards dispatching), and <code>$on</code> (event listener).</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">AngularJS Event Bus: $emit vs. $broadcast</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Event Method</th>
                <th style="width: 35%;">Propagation Path</th>
                <th style="width: 40%;">Ideal Scenario</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>$scope.$emit('evt', data)</code></td>
                <td>Dispatches an event <strong>upwards</strong> through the scope hierarchy to <code>$rootScope</code>.</td>
                <td>Notifying parent controllers or application shells of child user actions.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$scope.$broadcast('evt', data)</code></td>
                <td>Dispatches an event <strong>downwards</strong> to all descendant child scopes.</td>
                <td>Broadcasting global updates (e.g., Theme Changed, User Logged Out) to all widgets.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$scope.$on('evt', fn)</code></td>
                <td>Listens for and intercepts emitted or broadcasted events.</td>
                <td>Component reaction to decoupled events across controller boundaries.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Decoupled Communication via $emit &amp; $broadcast</h3>
<p class="text-slate-600 mb-4">Trigger events across separate child controllers without creating tight dependencies:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Sibling Communication using $rootScope as Event Bus</div>
    <pre><code class="language-html">&lt;div ng-app="eventBusApp" class="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-4"&gt;AngularJS Event Bus Architecture&lt;/h3&gt;

    &lt;!-- Sender Controller --&gt;
    &lt;div ng-controller="SenderCtrl" class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg mb-4"&gt;
        &lt;h4 class="text-sm font-bold text-emerald-900 mb-2"&gt;Sender Component&lt;/h4&gt;
        &lt;div class="flex gap-2"&gt;
            &lt;input type="text" ng-model="msgText" class="px-3 py-1.5 border rounded text-sm flex-1 bg-white" placeholder="Broadcast message..."&gt;
            &lt;button ng-click="broadcastMessage()" class="px-4 py-1.5 bg-emerald-600 text-white rounded text-sm font-bold shadow"&gt;
                Emit to App
            &lt;/button&gt;
        &lt;/div&gt;
    &lt;/div&gt;

    &lt;!-- Receiver Controller --&gt;
    &lt;div ng-controller="ReceiverCtrl" class="p-4 bg-indigo-50 border border-indigo-200 rounded-lg"&gt;
        &lt;h4 class="text-sm font-bold text-indigo-900 mb-2"&gt;Receiver Component&lt;/h4&gt;
        &lt;p class="text-xs text-indigo-700"&gt;Last Message Received:&lt;/p&gt;
        &lt;div class="p-2.5 bg-white border border-indigo-200 rounded font-bold text-sm text-indigo-950 mt-1"&gt;
            {{ lastMessage || 'Waiting for message...' }}
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('eventBusApp', [])
    .controller('SenderCtrl', function($scope, $rootScope) {
        $scope.msgText = 'Deploying build v2.4.0';
        $scope.broadcastMessage = function() {
            $rootScope.$broadcast('GLOBAL_NOTIFICATION', { text: $scope.msgText, timestamp: new Date().toLocaleTimeString() });
        };
    })
    .controller('ReceiverCtrl', function($scope) {
        $scope.lastMessage = '';
        $scope.$on('GLOBAL_NOTIFICATION', function(event, args) {
            $scope.lastMessage = args.text + ' (at ' + args.timestamp + ')';
        });
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 8, 'Controllers & Scope', 'Root Scope ($rootScope) & Event Bus ($emit, $broadcast, $on)', $content8, 'angularjs rootscope, $emit vs $broadcast, angularjs event bus, $scope.$on, decoupling controllers, angular event propagation');

// -------------------------------------------------------------
// Chapter 9
// -------------------------------------------------------------
$content9 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <strong>Controller As Syntax</strong> (introduced in AngularJS 1.2) binds the controller instance directly to the current scope under an alias (e.g. <code>MainCtrl as vm</code>). It replaces <code>$scope</code> with <code>this</code>, eliminating scope inheritance confusion and preparing code for ES6 classes.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">$scope Syntax vs. Controller As (vm) Syntax</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Criteria</th>
                <th style="width: 35%;">Traditional <code>$scope</code> Syntax</th>
                <th style="width: 40%;">Modern <code>Controller as vm</code></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Context Pointer</td>
                <td>Must inject <code>$scope</code> and attach properties to it.</td>
                <td>Uses <code>var vm = this;</code> (plain JavaScript constructor function).</td>
            </tr>
            <tr>
                <td class="code-cell">Template Clarify</td>
                <td><code>{{ title }}</code> (ambiguous in nested scopes)</td>
                <td><code>{{ vm.title }}</code> or <code>{{ parent.title }}</code> (crystal clear namespace).</td>
            </tr>
            <tr>
                <td class="code-cell">ES6 / Modern Upgrade</td>
                <td>Difficult to migrate to TypeScript / ES6 classes.</td>
                <td><strong>Seamless upgrade:</strong> Maps 1:1 to modern component classes in Angular (2+).</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Enterprise vm Pattern in Action</h3>
<p class="text-slate-600 mb-4">Assign <code>this</code> to <code>vm</code> (ViewModel) at the top of your controller to prevent closure context loss:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Controller As with Clear Nested Namespaces</div>
    <pre><code class="language-html">&lt;div ng-app="ctrlAsApp" ng-controller="StoreController as store" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-1"&gt;{{ store.storeName }}&lt;/h3&gt;
    &lt;p class="text-xs text-slate-500 mb-4"&gt;Controller As Syntax Demo&lt;/p&gt;

    &lt;div class="mb-4"&gt;
        &lt;label class="block text-xs font-bold text-slate-500 mb-1"&gt;Store Slogan:&lt;/label&gt;
        &lt;input type="text" ng-model="store.slogan" class="w-full px-3 py-2 border rounded-lg text-sm"&gt;
    &lt;/div&gt;

    &lt;div class="p-3 bg-slate-50 border rounded-lg flex items-center justify-between"&gt;
        &lt;span class="text-sm font-semibold text-slate-700"&gt;Items in Cart: {{ store.cartCount }}&lt;/span&gt;
        &lt;button ng-click="store.addItem()" class="px-3 py-1.5 bg-emerald-600 text-white rounded text-xs font-bold shadow"&gt;
            Add Item
        &lt;/button&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('ctrlAsApp', [])
    .controller('StoreController', function() {
        var vm = this; // ViewModel capture
        vm.storeName = 'Nectra Digital Store';
        vm.slogan = 'High Performance Web Solutions';
        vm.cartCount = 2;

        vm.addItem = function() {
            vm.cartCount++;
        };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 9, 'Controllers & Scope', 'Controller As Syntax & vm Pattern (Modern AngularJS Best Practice)', $content9, 'controller as syntax, angularjs vm pattern, var vm = this, scope vs controller as, modern angularjs best practices, es6 controller');

// -------------------------------------------------------------
// Chapter 10
// -------------------------------------------------------------
$content10 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">When an element containing a controller or directive is removed from the DOM (e.g., via <code>ng-if</code> or route transitions), its scope is destroyed. Failing to clean up timers, intervals, and event listeners triggers insidious <strong>Memory Leaks</strong>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Scope Destruction Lifecycle &amp; Cleanup Checklist</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Resource</th>
                <th style="width: 35%;">Leak Mechanism</th>
                <th style="width: 40%;">Required $destroy Cleanup</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>$interval / $timeout</code></td>
                <td>Timer keeps reference to scope in memory forever.</td>
                <td>Call <code>$interval.cancel(promise)</code> inside <code>$scope.$on('$destroy', ...)</code>.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$rootScope.$on()</code></td>
                <td>Root scope listener survives controller destruction!</td>
                <td>Invoke the unbind function returned by <code>$rootScope.$on</code> upon destroy.</td>
            </tr>
            <tr>
                <td class="code-cell">DOM Event Listeners</td>
                <td>jQuery / window resize event references remain bound.</td>
                <td><code>angular.element($window).off('resize', handler)</code>.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Robust Cleanup Implementation</h3>
<p class="text-slate-600 mb-4">Always listen to the <code>$destroy</code> event to cancel active intervals and unregister event handlers:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Safe Interval with Automatic Memory Cleanup</div>
    <pre><code class="language-html">&lt;div ng-app="destroyDemoApp" ng-controller="ParentShellCtrl" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;button ng-click="toggleWidget()" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-bold mb-4 shadow"&gt;
        {{ showWidget ? 'Unmount (Destroy) Widget' : 'Mount Widget' }}
    &lt;/button&gt;

    &lt;div ng-if="showWidget" ng-controller="LiveClockCtrl" class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-center"&gt;
        &lt;span class="text-xs text-emerald-600 uppercase font-bold block"&gt;Live Heartbeat&lt;/span&gt;
        &lt;span class="text-2xl font-black text-emerald-950 font-mono"&gt;{{ timeString }}&lt;/span&gt;
        &lt;p class="text-xs text-emerald-700 mt-1"&gt;Seconds Elapsed: {{ ticks }}&lt;/p&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('destroyDemoApp', [])
    .controller('ParentShellCtrl', function($scope) {
        $scope.showWidget = true;
        $scope.toggleWidget = function() {
            $scope.showWidget = !$scope.showWidget;
        };
    })
    .controller('LiveClockCtrl', function($scope, $interval) {
        $scope.timeString = new Date().toLocaleTimeString();
        $scope.ticks = 0;

        // Start interval
        var timerPromise = $interval(function() {
            $scope.timeString = new Date().toLocaleTimeString();
            $scope.ticks++;
            console.log('Clock tick #' + $scope.ticks);
        }, 1000);

        // Crucial: Clean up timer when scope is destroyed
        $scope.$on('$destroy', function() {
            $interval.cancel(timerPromise);
            console.log('Clock scope destroyed: Timer cleanly cancelled. No memory leaks!');
        });
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 10, 'Controllers & Scope', 'Scope Lifecycle, Memory Management & Cleaning Up with $destroy', $content10, 'angularjs $destroy, scope lifecycle, angular memory leaks, cleanup in angularjs, $interval.cancel, prevent memory leak');

// -------------------------------------------------------------
// Chapter 11
// -------------------------------------------------------------
$content11 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Controlling element visibility is a fundamental UI requirement. AngularJS provides <code>ng-if</code>, <code>ng-show</code>, and <code>ng-hide</code>. While they appear similar, their architectural impact on performance and scope creation is fundamentally different.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">ng-if vs. ng-show / ng-hide: Complete Comparison</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 20%;">Directive</th>
                <th style="width: 30%;">Mechanism</th>
                <th style="width: 25%;">Scope Impact</th>
                <th style="width: 25%;">Best Used For</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>ng-if</code></td>
                <td><strong>Inserts or removes</strong> elements completely from the DOM tree.</td>
                <td>Creates a <strong>new child scope</strong> when true; destroys it when false.</td>
                <td>Heavy components, tabs, dialogs, or security-sensitive DOM.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>ng-show</code></td>
                <td>Toggles CSS <code>display: none !important;</code>. Element remains in DOM.</td>
                <td>No new scope created. All watchers continue running!</td>
                <td>Frequent toggles (e.g. dropdowns, tooltips, accordions).</td>
            </tr>
            <tr>
                <td class="code-cell"><code>ng-hide</code></td>
                <td>Inverse of <code>ng-show</code>. Adds <code>.ng-hide</code> CSS class when true.</td>
                <td>No new scope created. All watchers continue running!</td>
                <td>Inverted conditional display logic.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Interactive DOM Inspection</h3>
<p class="text-slate-600 mb-4">Compare how <code>ng-if</code> completely removes elements while <code>ng-show</code> merely hides them with CSS:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: ng-if vs ng-show In Action</div>
    <pre><code class="language-html">&lt;div ng-app="condApp" ng-init="showPremium = false; isVisible = true" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Conditional Directives Playground&lt;/h3&gt;

    &lt;div class="space-y-3 mb-6"&gt;
        &lt;label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer"&gt;
            &lt;input type="checkbox" ng-model="showPremium" class="rounded text-emerald-600"&gt;
            &lt;span&gt;Toggle &lt;code class="text-xs bg-slate-100 px-1 py-0.5 rounded"&gt;ng-if&lt;/code&gt; (DOM Creation/Removal)&lt;/span&gt;
        &lt;/label&gt;
        &lt;label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer"&gt;
            &lt;input type="checkbox" ng-model="isVisible" class="rounded text-indigo-600"&gt;
            &lt;span&gt;Toggle &lt;code class="text-xs bg-slate-100 px-1 py-0.5 rounded"&gt;ng-show&lt;/code&gt; (CSS Display Toggle)&lt;/span&gt;
        &lt;/label&gt;
    &lt;/div&gt;

    &lt;!-- ng-if: Real DOM Insertion / Destruction --&gt;
    &lt;div ng-if="showPremium" class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-900 mb-3"&gt;
        &lt;strong class="block text-sm"&gt;✓ Premium Section (ng-if)&lt;/strong&gt;
        &lt;span class="text-xs text-emerald-700"&gt;This element is completely removed from the DOM when unchecked.&lt;/span&gt;
    &lt;/div&gt;

    &lt;!-- ng-show: Simple CSS Hide --&gt;
    &lt;div ng-show="isVisible" class="p-4 bg-indigo-50 border border-indigo-200 rounded-lg text-indigo-900"&gt;
        &lt;strong class="block text-sm"&gt;✓ Notification Card (ng-show)&lt;/strong&gt;
        &lt;span class="text-xs text-indigo-700"&gt;This element stays in the DOM tree, hidden with display:none.&lt;/span&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 11, 'Built-in Directives', 'Conditional Rendering: ng-if vs ng-show & ng-hide Architectural Tradeoffs', $content11, 'angularjs ng-if vs ng-show, ng-hide, conditional rendering in angular, dom manipulation angularjs, angularjs display toggle');

// -------------------------------------------------------------
// Chapter 12
// -------------------------------------------------------------
$content12 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <strong>ng-repeat</strong> directive iterates over a collection (array or object) and instantiates a new DOM template for each item. Each repeated item receives its own isolated child scope with special built-in properties like <code>$index</code>, <code>$first</code>, <code>$last</code>, <code>$even</code>, and <code>$odd</code>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">ng-repeat Special Loop Properties</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Special Variable</th>
                <th style="width: 35%;">Type &amp; Value</th>
                <th style="width: 40%;">Common Practical Use</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>$index</code></td>
                <td>Integer (0-based)</td>
                <td>Displaying numbered tables, item ordering, array indexing.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$first / $last</code></td>
                <td>Boolean (<code>true / false</code>)</td>
                <td>Applying special borders, headers, or eliminating trailing commas in lists.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$even / $odd</code></td>
                <td>Boolean (<code>true / false</code>)</td>
                <td>Alternating zebra-striping rows in tables.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>track by key</code></td>
                <td>Expression (e.g. <code>track by item.id</code>)</td>
                <td><strong>Performance Optimization:</strong> Prevents DOM re-creation when array is updated.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Masterclass List Rendering with Track By</h3>
<p class="text-slate-600 mb-4">Notice how <code>track by item.id</code> prevents duplicate element creation and maximizes rendering speed:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: ng-repeat with Zebra Striping &amp; Track By</div>
    <pre><code class="language-html">&lt;div ng-app="repeatDemoApp" ng-controller="UserListCtrl as vm" class="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;div class="flex items-center justify-between mb-4"&gt;
        &lt;h3 class="text-lg font-bold text-slate-900"&gt;Team Directory ({{ vm.users.length }})&lt;/h3&gt;
        &lt;button ng-click="vm.addUser()" class="px-3 py-1.5 bg-emerald-600 text-white rounded text-xs font-bold shadow"&gt;
            + Add Developer
        &lt;/button&gt;
    &lt;/div&gt;

    &lt;div class="border rounded-lg overflow-hidden"&gt;
        &lt;div ng-repeat="u in vm.users track by u.id" ng-class="{'bg-slate-50': $even, 'bg-white': $odd}" class="p-3 border-b last:border-b-0 flex items-center justify-between text-sm"&gt;
            &lt;div class="flex items-center gap-3"&gt;
                &lt;span class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-xs font-bold"&gt;{{ $index + 1 }}&lt;/span&gt;
                &lt;div&gt;
                    &lt;span class="font-bold text-slate-900 block"&gt;{{ u.name }}&lt;/span&gt;
                    &lt;span class="text-xs text-slate-500"&gt;{{ u.role }}&lt;/span&gt;
                &lt;/div&gt;
            &lt;/div&gt;
            &lt;div class="flex items-center gap-2"&gt;
                &lt;span ng-if="$first" class="px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold rounded"&gt;Lead&lt;/span&gt;
                &lt;button ng-click="vm.removeUser(u.id)" class="text-red-500 hover:text-red-700 text-xs font-bold"&gt;&times; Delete&lt;/button&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('repeatDemoApp', [])
    .controller('UserListCtrl', function() {
        var vm = this;
        vm.users = [
            { id: 101, name: 'Alice Watson', role: 'Full-Stack Architect' },
            { id: 102, name: 'Devon Miles', role: 'UI/UX Specialist' },
            { id: 103, name: 'Chloe Becker', role: 'DevOps Engineer' }
        ];

        vm.addUser = function() {
            var newId = Date.now();
            vm.users.push({ id: newId, name: 'New Engineer #' + (vm.users.length + 1), role: 'Junior Contributor' });
        };

        vm.removeUser = function(id) {
            vm.users = vm.users.filter(function(item) { return item.id !== id; });
        };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 12, 'Built-in Directives', 'List Rendering with ng-repeat: Tracking by Key, $index, $first, $last, $even, $odd', $content12, 'angularjs ng-repeat, track by in angularjs, $index in angularjs, $first and $last, zebra striping table angular, list rendering');
