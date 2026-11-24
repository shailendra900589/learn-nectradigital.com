<?php
// seed_angular_part2.php: Chapters 13 to 24

// -------------------------------------------------------------
// Chapter 13
// -------------------------------------------------------------
$content13 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <strong>ng-class</strong> and <strong>ng-style</strong> directives dynamically bind CSS classes and inline CSS rules to HTML elements based on expression evaluation. This provides declarative control over active states, alert banners, badges, and interactive themes.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">ng-class Syntax Variants: Object vs. String vs. Array</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Syntax Pattern</th>
                <th style="width: 35%;">Code Blueprint</th>
                <th style="width: 40%;">When to Choose</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Object Map (Most Common)</td>
                <td><code>ng-class="{'active': isActive, 'error': hasError}"</code></td>
                <td>Toggling multiple CSS classes based on independent boolean conditions.</td>
            </tr>
            <tr>
                <td class="code-cell">String Expression</td>
                <td><code>ng-class="status === 'ok' ? 'bg-green' : 'bg-red'"</code></td>
                <td>Simple ternary toggle between two specific class strings.</td>
            </tr>
            <tr>
                <td class="code-cell">Array Notation</td>
                <td><code>ng-class="[themeClass, {'shadow-lg': isElevated}]"</code></td>
                <td>Combining fixed dynamic class names with conditional flags.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Dynamic Badge &amp; Style Switcher</h3>
<p class="text-slate-600 mb-4">Observe how changing the state model adjusts colors and inline styles dynamically:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: ng-class and ng-style Dashboard Controls</div>
    <pre><code class="language-html">&lt;div ng-app="styleApp" ng-init="serverStatus = 'healthy'; progress = 75" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Server Health Monitor&lt;/h3&gt;

    &lt;div class="mb-4"&gt;
        &lt;label class="block text-xs font-bold text-slate-500 mb-1"&gt;Select Server State:&lt;/label&gt;
        &lt;div class="flex gap-2"&gt;
            &lt;button ng-click="serverStatus = 'healthy'" class="px-3 py-1 text-xs font-bold rounded border" ng-class="{'bg-emerald-600 text-white': serverStatus === 'healthy'}"&gt;Healthy&lt;/button&gt;
            &lt;button ng-click="serverStatus = 'warning'" class="px-3 py-1 text-xs font-bold rounded border" ng-class="{'bg-amber-500 text-white': serverStatus === 'warning'}"&gt;Warning&lt;/button&gt;
            &lt;button ng-click="serverStatus = 'critical'" class="px-3 py-1 text-xs font-bold rounded border" ng-class="{'bg-red-600 text-white': serverStatus === 'critical'}"&gt;Critical&lt;/button&gt;
        &lt;/div&gt;
    &lt;/div&gt;

    &lt;!-- Dynamic Status Banner via ng-class --&gt;
    &lt;div class="p-4 rounded-lg border text-sm font-bold mb-4 transition-all"
         ng-class="{
             'bg-emerald-50 text-emerald-800 border-emerald-300': serverStatus === 'healthy',
             'bg-amber-50 text-amber-800 border-amber-300': serverStatus === 'warning',
             'bg-red-50 text-red-800 border-red-300': serverStatus === 'critical'
         }"&gt;
        Current Status: {{ serverStatus | uppercase }}
    &lt;/div&gt;

    &lt;!-- Dynamic Progress Bar via ng-style --&gt;
    &lt;div&gt;
        &lt;div class="flex justify-between text-xs text-slate-500 mb-1"&gt;
            &lt;span&gt;Resource Utilization&lt;/span&gt;
            &lt;span&gt;{{ progress }}%&lt;/span&gt;
        &lt;/div&gt;
        &lt;div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden"&gt;
            &lt;div class="bg-indigo-600 h-full transition-all duration-300" ng-style="{'width': progress + '%'}"&gt;&lt;/div&gt;
        &lt;/div&gt;
        &lt;input type="range" min="0" max="100" ng-model="progress" class="w-full mt-2"&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 13, 'Built-in Directives', 'Dynamic Styling with ng-class & ng-style (Conditional CSS Classes)', $content13, 'angularjs ng-class, ng-style, conditional styling angularjs, dynamic css class, angularjs styling patterns');

// -------------------------------------------------------------
// Chapter 14
// -------------------------------------------------------------
$content14 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <strong>ng-switch</strong> directive conditionally swaps DOM elements based on a matching expression, identical to a JavaScript <code>switch</code> statement. It consists of <code>ng-switch</code> (container), <code>ng-switch-when</code> (case match), and <code>ng-switch-default</code> (fallback).</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">ng-switch vs. Multiple ng-if Statements</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Criteria</th>
                <th style="width: 35%;"><code>ng-switch</code> Directive</th>
                <th style="width: 40%;">Chained <code>ng-if</code> Directives</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Evaluation Efficiency</td>
                <td>Evaluates the source expression <strong>once</strong> and matches against cases.</td>
                <td>Evaluates every single <code>ng-if</code> condition independently on each digest cycle.</td>
            </tr>
            <tr>
                <td class="code-cell">Fallback Support</td>
                <td>Built-in <code>ng-switch-default</code> for unmatched cases.</td>
                <td>Requires manual inverted boolean expression guards.</td>
            </tr>
            <tr>
                <td class="code-cell">DOM Behavior</td>
                <td>Removes unmatched nodes completely from DOM tree.</td>
                <td>Removes unmatched nodes completely from DOM tree.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Multi-Tab Navigation with ng-switch</h3>
<p class="text-slate-600 mb-4">Observe clean declarative view switching without complex controller states:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Tabbed Dashboard View with ng-switch</div>
    <pre><code class="language-html">&lt;div ng-app="switchApp" ng-init="selectedTab = 'overview'" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;div class="flex border-b border-slate-200 mb-4"&gt;
        &lt;button ng-click="selectedTab = 'overview'" class="px-4 py-2 text-xs font-bold border-b-2" ng-class="selectedTab === 'overview' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500'"&gt;Overview&lt;/button&gt;
        &lt;button ng-click="selectedTab = 'analytics'" class="px-4 py-2 text-xs font-bold border-b-2" ng-class="selectedTab === 'analytics' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500'"&gt;Analytics&lt;/button&gt;
        &lt;button ng-click="selectedTab = 'settings'" class="px-4 py-2 text-xs font-bold border-b-2" ng-class="selectedTab === 'settings' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500'"&gt;Settings&lt;/button&gt;
    &lt;/div&gt;

    &lt;div ng-switch="selectedTab" class="p-4 bg-slate-50 rounded-lg border text-sm"&gt;
        &lt;div ng-switch-when="overview" class="text-slate-700"&gt;
            &lt;h4 class="font-bold text-slate-900 mb-1"&gt;System Overview&lt;/h4&gt;
            &lt;p class="text-xs text-slate-500"&gt;All core microservices operating at 99.98% uptime.&lt;/p&gt;
        &lt;/div&gt;

        &lt;div ng-switch-when="analytics" class="text-slate-700"&gt;
            &lt;h4 class="font-bold text-slate-900 mb-1"&gt;Real-Time Traffic Analytics&lt;/h4&gt;
            &lt;p class="text-xs text-slate-500"&gt;14,280 active user sessions recorded in the last hour.&lt;/p&gt;
        &lt;/div&gt;

        &lt;div ng-switch-when="settings" class="text-slate-700"&gt;
            &lt;h4 class="font-bold text-slate-900 mb-1"&gt;Account Preferences&lt;/h4&gt;
            &lt;p class="text-xs text-slate-500"&gt;Manage API keys, billing webhooks, and team access.&lt;/p&gt;
        &lt;/div&gt;

        &lt;div ng-switch-default class="text-red-500"&gt;
            Tab not recognized.
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 14, 'Built-in Directives', 'Multi-Condition Rendering with ng-switch, ng-switch-when & ng-switch-default', $content14, 'angularjs ng-switch, ng-switch-when, ng-switch-default, conditional tabs in angularjs, switch statement in html');

// -------------------------------------------------------------
// Chapter 15
// -------------------------------------------------------------
$content15 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">AngularJS encapsulates browser DOM events with custom directives like <code>ng-click</code>, <code>ng-dblclick</code>, <code>ng-change</code>, and <code>ng-submit</code>. These directives automatically wrap execution in a <code>$scope.$apply()</code> call and expose the special <code>$event</code> object.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">AngularJS Event Directives Reference</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Event Directive</th>
                <th style="width: 35%;">Native Trigger</th>
                <th style="width: 40%;">Special Parameter / Capability</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>ng-click="fn($event)"</code></td>
                <td><code>click</code></td>
                <td>Passes the native jQuery/jqLite event wrapper to prevent default or stop propagation.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>ng-change="fn()"</code></td>
                <td>Input change event</td>
                <td><strong>Only works with <code>ng-model</code>!</strong> Fires only on user input mutations.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>ng-submit="fn()"</code></td>
                <td>Form submission</td>
                <td>Automatically calls <code>event.preventDefault()</code> to prevent full page reloads.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>ng-keydown="fn($event)"</code></td>
                <td>Keyboard press</td>
                <td>Inspect <code>$event.keyCode</code> or <code>$event.key</code> for Enter, Esc, etc.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Capturing Mouse Coordinates &amp; PreventDefault</h3>
<p class="text-slate-600 mb-4">Use <code>$event</code> to access event coordinates, target elements, and keyboard codes:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Interactive Event Playground with $event</div>
    <pre><code class="language-html">&lt;div ng-app="eventApp" ng-controller="EventCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Interactive Event Directive Center&lt;/h3&gt;

    &lt;div class="mb-4"&gt;
        &lt;label class="block text-xs font-bold text-slate-500 mb-1"&gt;Type &amp; Press Enter:&lt;/label&gt;
        &lt;input type="text" ng-model="vm.newNote" ng-keydown="vm.handleKey($event)" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Press Enter to save..."&gt;
    &lt;/div&gt;

    &lt;div ng-click="vm.handleClick($event)" class="p-6 bg-slate-100 hover:bg-slate-200 border-2 border-dashed border-slate-300 rounded-lg text-center cursor-pointer mb-4"&gt;
        &lt;span class="text-xs font-bold text-slate-600"&gt;Click anywhere in this box&lt;/span&gt;
        &lt;div class="text-xs text-emerald-600 font-mono mt-1"&gt;{{ vm.clickCoords }}&lt;/div&gt;
    &lt;/div&gt;

    &lt;div class="p-3 bg-slate-50 border rounded-lg text-xs"&gt;
        &lt;strong class="text-slate-700"&gt;Notes Saved ({{ vm.notes.length }}):&lt;/strong&gt;
        &lt;ul class="list-disc pl-4 mt-1 text-slate-600 space-y-0.5"&gt;
            &lt;li ng-repeat="n in vm.notes"&gt;{{ n }}&lt;/li&gt;
        &lt;/ul&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('eventApp', [])
    .controller('EventCtrl', function() {
        var vm = this;
        vm.newNote = '';
        vm.notes = ['First meeting memo'];
        vm.clickCoords = 'Click to record X / Y';

        vm.handleKey = function(evt) {
            if (evt.keyCode === 13 &amp;&amp; vm.newNote.trim()) {
                vm.notes.push(vm.newNote.trim());
                vm.newNote = '';
            }
        };

        vm.handleClick = function(evt) {
            vm.clickCoords = 'Clicked at: X=' + evt.offsetX + ', Y=' + evt.offsetY;
        };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 15, 'Built-in Directives', 'Event Directives: ng-click, ng-dblclick, ng-change, ng-submit & $event', $content15, 'angularjs events, ng-click, $event in angularjs, ng-change, ng-submit, keyboard events angular, event.preventdefault');

// -------------------------------------------------------------
// Chapter 16
// -------------------------------------------------------------
$content16 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <strong>ng-include</strong> directive fetches, compiles, and includes an external HTML fragment or inline template into the current document. It promotes DRY (Don't Repeat Yourself) architecture by breaking large monolithic views into modular partial templates.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">ng-include Syntax Rules &amp; Scope Creation</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Property</th>
                <th style="width: 35%;">Syntax Requirement</th>
                <th style="width: 40%;">Crucial Rule</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">String Literal URL</td>
                <td><code>ng-include="'template.html'"</code></td>
                <td><strong>Quoting Rule:</strong> Must wrap literal filenames in single quotes inside double quotes! Otherwise treated as scope variable.</td>
            </tr>
            <tr>
                <td class="code-cell">Inline Template ID</td>
                <td><code>ng-include="'card-tpl.html'"</code></td>
                <td>Matches <code>&lt;script type="text/ng-template" id="card-tpl.html"&gt;</code> defined in DOM.</td>
            </tr>
            <tr>
                <td class="code-cell">Scope Behavior</td>
                <td>Creates a <strong>child scope</strong></td>
                <td>Any primitives assigned inside an <code>ng-include</code> template will not overwrite parent scope variables.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Reusable Inline Templates via text/ng-template</h3>
<p class="text-slate-600 mb-4">Store sub-templates in the cache using <code>&lt;script type="text/ng-template"&gt;</code> to render modular views instantly:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Dynamic ng-include with Embedded Templates</div>
    <pre><code class="language-html">&lt;div ng-app="includeApp" ng-init="currentTemplate = 'card'" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Partial Template Renderer&lt;/h3&gt;

    &lt;div class="flex gap-2 mb-4"&gt;
        &lt;button ng-click="currentTemplate = 'card'" class="px-3 py-1.5 text-xs font-bold rounded border" ng-class="{'bg-emerald-600 text-white': currentTemplate === 'card'}"&gt;Card View&lt;/button&gt;
        &lt;button ng-click="currentTemplate = 'table'" class="px-3 py-1.5 text-xs font-bold rounded border" ng-class="{'bg-emerald-600 text-white': currentTemplate === 'table'}"&gt;Compact Table&lt;/button&gt;
    &lt;/div&gt;

    &lt;!-- Inline Template 1: Card --&gt;
    &lt;script type="text/ng-template" id="card"&gt;
        &lt;div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-950"&gt;
            &lt;strong class="block text-sm"&gt;Card View Template&lt;/strong&gt;
            &lt;p class="text-xs text-emerald-700 mt-1"&gt;Ideal for visual hero banners, profile avatars, and dashboards.&lt;/p&gt;
        &lt;/div&gt;
    &lt;/script&gt;

    &lt;!-- Inline Template 2: Table --&gt;
    &lt;script type="text/ng-template" id="table"&gt;
        &lt;div class="p-4 bg-indigo-50 border border-indigo-200 rounded-lg text-indigo-950"&gt;
            &lt;strong class="block text-sm"&gt;Compact Table Template&lt;/strong&gt;
            &lt;p class="text-xs text-indigo-700 mt-1"&gt;Optimized for dense financial audits and enterprise reporting.&lt;/p&gt;
        &lt;/div&gt;
    &lt;/script&gt;

    &lt;!-- Dynamic Container --&gt;
    &lt;div ng-include="currentTemplate"&gt;&lt;/div&gt;
&lt;/div&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 16, 'Built-in Directives', 'Template Inlining & Partial Views with ng-include & <script type="text/ng-template">', $content16, 'angularjs ng-include, text/ng-template, partial templates angularjs, angular view decomposition, modular html templates');

// -------------------------------------------------------------
// Chapter 17
// -------------------------------------------------------------
$content17 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">By default, standard interpolation <code>{{ rawHtml }}</code> safely escapes HTML entities to prevent Cross-Site Scripting (XSS). To render rich formatted HTML, AngularJS provides <strong>ng-bind-html</strong> coupled with the <strong>$sce (Strict Contextual Escaping)</strong> service.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">$sce Trust Methods for Different Injection Contexts</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Context Method</th>
                <th style="width: 35%;">Code Blueprint</th>
                <th style="width: 40%;">Intended Security Context</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>$sce.trustAsHtml(str)</code></td>
                <td><code>$scope.html = $sce.trustAsHtml('&lt;b&gt;Hi&lt;/b&gt;');</code></td>
                <td>Binding formatted HTML strings into <code>ng-bind-html</code>.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$sce.trustAsResourceUrl(url)</code></td>
                <td><code>$sce.trustAsResourceUrl('https://...')</code></td>
                <td>Loading external script assets or iframe URLs safely.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$sce.trustAsJs(script)</code></td>
                <td><code>$sce.trustAsJs('console.log(...)')</code></td>
                <td>Executing or inlining dynamic JavaScript code safely.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Rendering Safe HTML with ng-bind-html &amp; $sce</h3>
<p class="text-slate-600 mb-4">See how <code>$sce.trustAsHtml</code> allows intentional markup while protecting against unauthorized injections:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Strict Contextual Escaping ($sce) Live Demo</div>
    <pre><code class="language-html">&lt;div ng-app="sceApp" ng-controller="SceCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;$sce Security Sanitizer&lt;/h3&gt;

    &lt;div class="space-y-4"&gt;
        &lt;div&gt;
            &lt;span class="text-xs font-bold text-slate-400 block uppercase"&gt;1. Standard Escaped Interpolation:&lt;/span&gt;
            &lt;div class="p-2.5 bg-slate-100 rounded text-xs font-mono text-slate-700"&gt;
                {{ vm.rawSnippet }}
            &lt;/div&gt;
        &lt;/div&gt;

        &lt;div&gt;
            &lt;span class="text-xs font-bold text-emerald-600 block uppercase"&gt;2. Rendered via ng-bind-html &amp; $sce:&lt;/span&gt;
            &lt;div ng-bind-html="vm.trustedSnippet" class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-slate-800"&gt;&lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('sceApp', ['ngSanitize'])
    .controller('SceCtrl', function($sce) {
        var vm = this;
        vm.rawSnippet = '&lt;span class="text-emerald-700 font-bold"&gt;Verified Article:&lt;/span&gt; Reviewed by &lt;em class="underline"&gt;Instructor NectraDigital&lt;/em&gt; on 2026.';
        
        // Explicitly trust HTML string for secure presentation
        vm.trustedSnippet = $sce.trustAsHtml(vm.rawSnippet);
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 17, 'Built-in Directives', 'Safe HTML Rendering: ng-bind-html & $sce (Strict Contextual Escaping)', $content17, 'angularjs $sce, ng-bind-html, strict contextual escaping, angularjs xss prevention, trustAsHtml, angular sanitize');

// -------------------------------------------------------------
// Chapter 18
// -------------------------------------------------------------
$content18 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>Filters</strong> format the value of an expression for display to the user without mutating the underlying data model. Filters are applied in templates using the pipe symbol <code>|</code> and can accept parameters separated by colons.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Built-in AngularJS Filters Master List</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Filter Name</th>
                <th style="width: 35%;">Syntax Example</th>
                <th style="width: 40%;">Transformation Output</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>currency</code></td>
                <td><code>{{ 1250.5 | currency:'€':2 }}</code></td>
                <td>Formats numbers as monetary currency (e.g. <code>€1,250.50</code>).</td>
            </tr>
            <tr>
                <td class="code-cell"><code>date</code></td>
                <td><code>{{ now | date:'yyyy-MM-dd HH:mm' }}</code></td>
                <td>Formats millisecond timestamps or Date objects into human-readable strings.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>number</code></td>
                <td><code>{{ 3412.856 | number:2 }}</code></td>
                <td>Rounds numbers and formats decimal separators (e.g. <code>3,412.86</code>).</td>
            </tr>
            <tr>
                <td class="code-cell"><code>uppercase / lowercase</code></td>
                <td><code>{{ 'admin' | uppercase }}</code></td>
                <td>Converts text case (e.g. <code>ADMIN</code>).</td>
            </tr>
            <tr>
                <td class="code-cell"><code>json</code></td>
                <td><code>&lt;pre&gt;{{ userObj | json }}&lt;/pre&gt;</code></td>
                <td>Serializes JavaScript objects into pretty-printed JSON strings (essential for debugging).</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Master Formatters Playground</h3>
<p class="text-slate-600 mb-4">Observe how the same underlying numerical and date values are formatted cleanly:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: All Built-in AngularJS Filters in Action</div>
    <pre><code class="language-html">&lt;div ng-app="filterApp" ng-controller="FilterCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Built-in Data Formatters&lt;/h3&gt;

    &lt;div class="space-y-2 text-sm text-slate-700"&gt;
        &lt;div class="flex justify-between border-b pb-1"&gt;
            &lt;span class="text-slate-500"&gt;Currency Filter:&lt;/span&gt;
            &lt;strong class="text-emerald-700"&gt;{{ vm.balance | currency:'$':2 }}&lt;/strong&gt;
        &lt;/div&gt;
        &lt;div class="flex justify-between border-b pb-1"&gt;
            &lt;span class="text-slate-500"&gt;Full Date Filter:&lt;/span&gt;
            &lt;span class="font-bold text-slate-800"&gt;{{ vm.now | date:'medium' }}&lt;/span&gt;
        &lt;/div&gt;
        &lt;div class="flex justify-between border-b pb-1"&gt;
            &lt;span class="text-slate-500"&gt;Precision Number:&lt;/span&gt;
            &lt;span class="font-bold text-slate-800"&gt;{{ vm.pi | number:4 }}&lt;/span&gt;
        &lt;/div&gt;
        &lt;div class="flex justify-between border-b pb-1"&gt;
            &lt;span class="text-slate-500"&gt;Uppercase / Lowercase:&lt;/span&gt;
            &lt;span class="font-bold text-indigo-600"&gt;{{ vm.headline | uppercase }}&lt;/span&gt;
        &lt;/div&gt;
    &lt;/div&gt;

    &lt;div class="mt-4"&gt;
        &lt;span class="text-xs font-bold text-slate-400 block uppercase mb-1"&gt;JSON Debug Filter:&lt;/span&gt;
        &lt;pre class="bg-slate-900 text-emerald-400 p-3 rounded text-xs font-mono overflow-auto"&gt;{{ vm.config | json }}&lt;/pre&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('filterApp', [])
    .controller('FilterCtrl', function() {
        var vm = this;
        vm.balance = 4850.75;
        vm.now = new Date();
        vm.pi = 3.14159265;
        vm.headline = 'Enterprise Edition';
        vm.config = { env: 'production', debug: false, workers: 8 };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 18, 'Filters & Data Formatting', 'Built-in Filters: currency, date, number, uppercase, lowercase & json', $content18, 'angularjs filters, currency filter, date filter in angular, json filter, number formatting angularjs, angular pipes');

// -------------------------------------------------------------
// Chapter 19
// -------------------------------------------------------------
$content19 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">AngularJS includes powerful array transformation filters: <strong>filter</strong> (fuzzy/deep search), <strong>orderBy</strong> (multi-column sorting), and <strong>limitTo</strong> (pagination slicing). These can be piped together inside <code>ng-repeat</code> to build live search grids.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Collection Filtering &amp; Sorting Pipe Pipeline</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Filter</th>
                <th style="width: 35%;">Syntax Blueprint</th>
                <th style="width: 40%;">Behavior</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>filter:searchQuery</code></td>
                <td><code>users | filter:searchText</code></td>
                <td>Performs deep string matching across all object properties or targeted fields.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>orderBy:sortKey:reverse</code></td>
                <td><code>users | orderBy:'salary':true</code></td>
                <td>Sorts ascending by default, or descending if boolean reverse is <code>true</code>.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>limitTo:limit:begin</code></td>
                <td><code>users | limitTo:5:0</code></td>
                <td>Returns a sliced subset of items (for client-side pagination).</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Instant Search, Sorter &amp; Slicer</h3>
<p class="text-slate-600 mb-4">Type in the search box or click column headers to observe instant filter chaining:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Chained Filters for Real-Time Search &amp; Sort</div>
    <pre><code class="language-html">&lt;div ng-app="gridApp" ng-controller="GridCtrl as vm" class="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Product Catalog Filter&lt;/h3&gt;

    &lt;div class="flex gap-2 mb-4"&gt;
        &lt;input type="text" ng-model="vm.query" class="flex-1 px-3 py-2 border rounded-lg text-sm" placeholder="Search product or category..."&gt;
        &lt;button ng-click="vm.reverseSort = !vm.reverseSort" class="px-3 py-2 bg-slate-800 text-white rounded-lg text-xs font-bold"&gt;
            Sort: {{ vm.reverseSort ? 'Price &darr;' : 'Price &uarr;' }}
        &lt;/button&gt;
    &lt;/div&gt;

    &lt;div class="border rounded-lg overflow-hidden text-sm"&gt;
        &lt;div ng-repeat="p in vm.products | filter:vm.query | orderBy:'price':vm.reverseSort | limitTo:vm.limit" class="p-3 border-b last:border-b-0 flex justify-between items-center"&gt;
            &lt;div&gt;
                &lt;span class="font-bold text-slate-900 block"&gt;{{ p.name }}&lt;/span&gt;
                &lt;span class="text-xs text-slate-500 uppercase font-semibold"&gt;Category: {{ p.category }}&lt;/span&gt;
            &lt;/div&gt;
            &lt;span class="text-sm font-black text-emerald-600"&gt;{{ p.price | currency }}&lt;/span&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('gridApp', [])
    .controller('GridCtrl', function() {
        var vm = this;
        vm.query = '';
        vm.reverseSort = false;
        vm.limit = 4;
        vm.products = [
            { name: 'Ultra Wireless Headset', category: 'Audio', price: 199.99 },
            { name: 'Mechanical Ergonomic Keyboard', category: 'Peripherals', price: 149.50 },
            { name: '4K Ultra-Wide Monitor', category: 'Displays', price: 599.00 },
            { name: 'USB-C Thunderbolt Hub', category: 'Peripherals', price: 79.99 },
            { name: 'Studio Condenser Mic', category: 'Audio', price: 129.00 }
        ];
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 19, 'Filters & Data Formatting', 'List Filtering, Searching & Sorting: filter, orderBy & limitTo with Pipe Chaining', $content19, 'angularjs filter, orderby in angularjs, limitto, chained filters, live search in angularjs, sorting tables angularjs');

// -------------------------------------------------------------
// Chapter 20
// -------------------------------------------------------------
$content20 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">When built-in formatters are not enough, you can create <strong>Custom Filters</strong> using <code>angular.module().filter()</code>. A filter is a factory function that returns a worker function that accepts an input value and optional parameters.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Anatomy of a Custom Filter Function</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Section</th>
                <th style="width: 35%;">Code Structure</th>
                <th style="width: 40%;">Crucial Rule</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Factory Wrapper</td>
                <td><code>.filter('filterName', function(deps) { ... })</code></td>
                <td>Can inject AngularJS services (e.g. <code>$sce</code>, <code>$locale</code>).</td>
            </tr>
            <tr>
                <td class="code-cell">Worker Function</td>
                <td><code>return function(input, arg1, arg2) { ... }</code></td>
                <td><strong>Must be Pure:</strong> Given the same input, must always return identical output for caching.</td>
            </tr>
            <tr>
                <td class="code-cell">Null Guard</td>
                <td><code>if (!input) return '';</code></td>
                <td>Always guard against <code>undefined</code> or <code>null</code> during initial load before data fetches.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Custom Word Truncation &amp; Masking Filter</h3>
<p class="text-slate-600 mb-4">Build and use custom text filters directly inside your application templates:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Custom Truncate and Credit Card Masking Filters</div>
    <pre><code class="language-html">&lt;div ng-app="customFilterApp" ng-init="longBio = 'AngularJS revolutionized front-end single page application architecture with declarative HTML templates and two-way binding.'; cardNum = '4532890123456789'" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Custom Filters in Action&lt;/h3&gt;

    &lt;div class="mb-4"&gt;
        &lt;span class="text-xs font-bold text-slate-500 block mb-1"&gt;1. Custom Truncate Filter (Length: 45):&lt;/span&gt;
        &lt;p class="p-3 bg-slate-50 border rounded-lg text-xs text-slate-700 italic"&gt;
            "{{ longBio | truncate:45:'… [Read More]' }}"
        &lt;/p&gt;
    &lt;/div&gt;

    &lt;div&gt;
        &lt;span class="text-xs font-bold text-slate-500 block mb-1"&gt;2. Custom Card Masker Filter:&lt;/span&gt;
        &lt;div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-950 font-mono text-sm font-bold"&gt;
            {{ cardNum | maskCard }}
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('customFilterApp', [])
    // Custom Filter 1: Truncate Text
    .filter('truncate', function() {
        return function(input, limit, suffix) {
            if (!input) return '';
            limit = limit || 50;
            suffix = suffix || '...';
            return input.length > limit ? input.substring(0, limit) + suffix : input;
        };
    })
    // Custom Filter 2: Mask Card Numbers
    .filter('maskCard', function() {
        return function(input) {
            if (!input || input.length < 4) return input;
            var str = String(input);
            var visible = str.slice(-4);
            return '•••• •••• •••• ' + visible;
        };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 20, 'Filters & Data Formatting', 'Custom AngularJS Filters: Implementation, Parameters & Performance Caching', $content20, 'custom filter in angularjs, angularjs filter function, truncate filter angularjs, pure filters angular, string formatting angularjs');

// -------------------------------------------------------------
// Chapter 21
// -------------------------------------------------------------
$content21 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Forms in AngularJS are powered by the <strong>FormController</strong>. When you declare a <code>&lt;form name="myForm"&gt;</code>, AngularJS automatically publishes a FormController instance onto the scope, exposing real-time validation flags like <code>$valid</code>, <code>$invalid</code>, <code>$dirty</code>, <code>$pristine</code>, and <code>$touched</code>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">AngularJS Form Validation State Properties</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Property Flag</th>
                <th style="width: 35%;">Boolean True Condition</th>
                <th style="width: 40%;">Matching CSS Class</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>$valid / $invalid</code></td>
                <td>True when all validation rules pass / any rule fails.</td>
                <td><code>.ng-valid</code> / <code>.ng-invalid</code></td>
            </tr>
            <tr>
                <td class="code-cell"><code>$dirty / $pristine</code></td>
                <td>True if the user has modified the control / control has not been touched.</td>
                <td><code>.ng-dirty</code> / <code>.ng-pristine</code></td>
            </tr>
            <tr>
                <td class="code-cell"><code>$touched / $untouched</code></td>
                <td>True once the control has lost focus (blur event fired).</td>
                <td><code>.ng-touched</code> / <code>.ng-untouched</code></td>
            </tr>
            <tr>
                <td class="code-cell"><code>$submitted</code></td>
                <td>True if the user has attempted to submit the form.</td>
                <td><code>.ng-submitted</code></td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Form Validation State Inspection</h3>
<p class="text-slate-600 mb-4">Watch how the form controller tracks validation and prevents submission until all fields are valid:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Real-Time Form State Dashboard</div>
    <pre><code class="language-html">&lt;div ng-app="formApp" ng-controller="FormCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Registration Form Validation&lt;/h3&gt;

    &lt;form name="vm.regForm" ng-submit="vm.submitForm()" novalidate&gt;
        &lt;div class="mb-3"&gt;
            &lt;label class="block text-xs font-bold text-slate-600 mb-1"&gt;Email Address *&lt;/label&gt;
            &lt;input type="email" name="userEmail" ng-model="vm.email" required class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="you@domain.com"&gt;
            &lt;span ng-show="vm.regForm.userEmail.$touched &amp;&amp; vm.regForm.userEmail.$error.required" class="text-red-500 text-xs font-bold mt-1 block"&gt;Email is required.&lt;/span&gt;
            &lt;span ng-show="vm.regForm.userEmail.$touched &amp;&amp; vm.regForm.userEmail.$error.email" class="text-red-500 text-xs font-bold mt-1 block"&gt;Invalid email format.&lt;/span&gt;
        &lt;/div&gt;

        &lt;button type="submit" ng-disabled="vm.regForm.$invalid" class="w-full py-2 bg-emerald-600 disabled:bg-slate-300 text-white rounded-lg text-sm font-bold shadow transition"&gt;
            {{ vm.regForm.$valid ? 'Complete Registration' : 'Complete All Fields' }}
        &lt;/button&gt;
    &lt;/form&gt;

    &lt;div class="mt-4 p-3 bg-slate-50 border rounded-lg text-xs space-y-1 font-mono text-slate-600"&gt;
        &lt;div&gt;Form $valid: &lt;strong class="text-slate-900"&gt;{{ vm.regForm.$valid }}&lt;/strong&gt;&lt;/div&gt;
        &lt;div&gt;Form $dirty: &lt;strong class="text-slate-900"&gt;{{ vm.regForm.$dirty }}&lt;/strong&gt;&lt;/div&gt;
        &lt;div&gt;Email $touched: &lt;strong class="text-slate-900"&gt;{{ vm.regForm.userEmail.$touched }}&lt;/strong&gt;&lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('formApp', [])
    .controller('FormCtrl', function() {
        var vm = this;
        vm.email = '';
        vm.submitForm = function() {
            if (vm.regForm.$valid) {
                alert('Form Submitted successfully for: ' + vm.email);
            }
        };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 21, 'Forms, Validation & Controls', 'Forms in AngularJS: FormController States ($valid, $invalid, $dirty, $pristine, $touched)', $content21, 'angularjs form validation, formcontroller, $valid $invalid, $dirty $pristine, $touched in angularjs, novalidate in angularjs form');

// -------------------------------------------------------------
// Chapter 22
// -------------------------------------------------------------
$content22 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">AngularJS enhances standard HTML form controls with directives like <strong>ng-true-value</strong> / <strong>ng-false-value</strong> for checkboxes and <strong>ng-options</strong> for high-performance <code>&lt;select&gt;</code> dropdowns bound to JavaScript objects.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Advanced Form Controls: ng-options vs. ng-repeat inside Select</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Approach</th>
                <th style="width: 35%;">Syntax Blueprint</th>
                <th style="width: 40%;">Advantage / Limitation</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>ng-options</code> (Standard)</td>
                <td><code>item.id as item.name for item in items</code></td>
                <td><strong>Binds directly to entire objects or custom IDs!</strong> Zero string coercion. Faster memory recycling.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>ng-repeat</code> on <code>&lt;option&gt;</code></td>
                <td><code>&lt;option ng-repeat="item in items"&gt;</code></td>
                <td>Coerces value into string; slower memory performance with large lists.</td>
            </tr>
            <tr>
                <td class="code-cell">Checkbox Values</td>
                <td><code>ng-true-value="'YES'" ng-false-value="'NO'"</code></td>
                <td>Binds custom string or integer values instead of default boolean.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Master Form Controls Demo</h3>
<p class="text-slate-600 mb-4">Bind complex objects to select menus and configure custom checkbox values:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: ng-options and Custom Checkbox Values</div>
    <pre><code class="language-html">&lt;div ng-app="controlsApp" ng-controller="ControlsCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Interactive Form Controls&lt;/h3&gt;

    &lt;!-- Dropdown with ng-options --&gt;
    &lt;div class="mb-4"&gt;
        &lt;label class="block text-xs font-bold text-slate-600 mb-1"&gt;Select Cloud Region (ng-options):&lt;/label&gt;
        &lt;select ng-model="vm.selectedRegion" ng-options="r.name for r in vm.regions track by r.code" class="w-full px-3 py-2 border rounded-lg text-sm bg-white"&gt;
            &lt;option value=""&gt;-- Select Deployment Data Center --&lt;/option&gt;
        &lt;/select&gt;
    &lt;/div&gt;

    &lt;!-- Checkbox with ng-true-value --&gt;
    &lt;div class="mb-4"&gt;
        &lt;label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer"&gt;
            &lt;input type="checkbox" ng-model="vm.subscribeStatus" ng-true-value="'CONFIRMED_OPT_IN'" ng-false-value="'DECLINED'" class="rounded text-emerald-600"&gt;
            &lt;span&gt;Receive SLA Uptime Alerts (Custom Enum Value)&lt;/span&gt;
        &lt;/label&gt;
    &lt;/div&gt;

    &lt;div class="p-3 bg-slate-50 border rounded-lg text-xs space-y-1"&gt;
        &lt;div&gt;Selected Region Latency: &lt;strong class="text-emerald-700"&gt;{{ vm.selectedRegion.latency || 'N/A' }}&lt;/strong&gt;&lt;/div&gt;
        &lt;div&gt;Subscription Enum: &lt;strong class="text-indigo-700"&gt;{{ vm.subscribeStatus }}&lt;/strong&gt;&lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('controlsApp', [])
    .controller('ControlsCtrl', function() {
        var vm = this;
        vm.regions = [
            { code: 'us-east-1', name: 'US East (N. Virginia)', latency: '12ms' },
            { code: 'eu-central-1', name: 'EU Central (Frankfurt)', latency: '28ms' },
            { code: 'ap-south-1', name: 'Asia South (Mumbai)', latency: '8ms' }
        ];
        vm.selectedRegion = vm.regions[2];
        vm.subscribeStatus = 'CONFIRMED_OPT_IN';
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 22, 'Forms, Validation & Controls', 'Form Controls: Text Inputs, Checkbox (ng-true-value), Radio Buttons & ng-options', $content22, 'angularjs ng-options, ng-true-value, select dropdown in angularjs, form controls, radio buttons in angular, binding objects in select');

// -------------------------------------------------------------
// Chapter 23
// -------------------------------------------------------------
$content23 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Displaying clear, contextual validation errors improves user onboarding. AngularJS dynamically decorates inputs and forms with CSS state classes (<code>ng-invalid</code>, <code>ng-touched</code>, <code>ng-dirty</code>), allowing you to style invalid controls automatically without writing manual DOM manipulation.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Standard CSS Styling of Angular Validation States</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 30%;">CSS Selector</th>
                <th style="width: 35%;">Visual Representation</th>
                <th style="width: 35%;">When Active</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>input.ng-invalid.ng-touched</code></td>
                <td>Red border &amp; subtle red background tint</td>
                <td>Field is invalid AND the user has interacted with it and clicked away.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>input.ng-valid.ng-dirty</code></td>
                <td>Green border &amp; checkmark icon</td>
                <td>Field was modified and now fulfills all validation criteria.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>form.ng-submitted .error-msg</code></td>
                <td>Reveal all error banners</td>
                <td>User pressed Submit with incomplete fields.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Automated Red/Green Feedback Field</h3>
<p class="text-slate-600 mb-4">Notice how errors only appear once a field is touched to avoid annoying fresh users:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Polished Form with Conditional Error Display</div>
    <pre><code class="language-html">&lt;div ng-app="errApp" ng-controller="ErrCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Enterprise Password Policy&lt;/h3&gt;

    &lt;form name="vm.pwdForm" novalidate&gt;
        &lt;div class="mb-4"&gt;
            &lt;label class="block text-xs font-bold text-slate-600 mb-1"&gt;Create Password (Min 8 chars):&lt;/label&gt;
            &lt;input type="password" name="userPass" ng-model="vm.password" required ng-minlength="8" class="w-full px-3 py-2 border rounded-lg text-sm transition" ng-class="{'border-red-500 bg-red-50': vm.pwdForm.userPass.$invalid &amp;&amp; vm.pwdForm.userPass.$touched, 'border-emerald-500 bg-emerald-50': vm.pwdForm.userPass.$valid &amp;&amp; vm.pwdForm.userPass.$dirty}" placeholder="••••••••"&gt;

            &lt;!-- Error messages --&gt;
            &lt;div ng-show="vm.pwdForm.userPass.$touched &amp;&amp; vm.pwdForm.userPass.$invalid" class="text-red-500 text-xs font-bold mt-1.5 space-y-0.5"&gt;
                &lt;div ng-show="vm.pwdForm.userPass.$error.required"&gt;&bull; Password is strictly required.&lt;/div&gt;
                &lt;div ng-show="vm.pwdForm.userPass.$error.minlength"&gt;&bull; Must contain at least 8 characters (Current: {{ vm.password.length || 0 }}).&lt;/div&gt;
            &lt;/div&gt;

            &lt;div ng-show="vm.pwdForm.userPass.$valid" class="text-emerald-600 text-xs font-bold mt-1.5"&gt;
                ✓ Password meets security requirements.
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/form&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('errApp', [])
    .controller('ErrCtrl', function() {
        var vm = this;
        vm.password = '';
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 23, 'Forms, Validation & Controls', 'Displaying Validation Feedback, Error Messages & Dynamic CSS Validation States', $content23, 'angularjs validation feedback, error messages in angularjs, ng-invalid touched css, angular form errors, password validation angular');

// -------------------------------------------------------------
// Chapter 24
// -------------------------------------------------------------
$content24 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">When built-in validators (<code>required</code>, <code>minlength</code>, <code>pattern</code>) are insufficient, you can create custom synchronous and asynchronous validators by requiring <strong>ngModelController</strong> and registering functions on <code>$validators</code> or <code>$asyncValidators</code>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">$validators vs. $asyncValidators</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Validator Pipeline</th>
                <th style="width: 35%;">Return Value</th>
                <th style="width: 40%;">Ideal Practical Application</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>ctrl.$validators.myRule</code></td>
                <td>Boolean: <code>true</code> (Valid) or <code>false</code> (Invalid).</td>
                <td>Instant client-side checks: password confirmation match, custom regex, blacklisted words.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>ctrl.$asyncValidators.myRule</code></td>
                <td>Promise (<code>$q.resolve()</code> or <code>$q.reject()</code>).</td>
                <td>Server-side validation: username availability check, domain verification via HTTP.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Custom Blacklist Synchronous Validator</h3>
<p class="text-slate-600 mb-4">Attach a custom directive requiring <code>ngModel</code> to enforce domain-specific validation logic:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Custom Directive Validator (Disallowing Restricted Usernames)</div>
    <pre><code class="language-html">&lt;div ng-app="customValApp" ng-controller="ValCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Custom Validator Directive&lt;/h3&gt;

    &lt;form name="vm.myForm" novalidate&gt;
        &lt;label class="block text-xs font-bold text-slate-600 mb-1"&gt;Choose Username (Cannot be "admin" or "root"):&lt;/label&gt;
        &lt;input type="text" name="userName" ng-model="vm.username" check-username required class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="e.g. dev_alex"&gt;

        &lt;div ng-show="vm.myForm.userName.$error.blacklisted" class="text-red-500 text-xs font-bold mt-1.5"&gt;
            &times; That username is reserved by system policy!
        &lt;/div&gt;

        &lt;div ng-show="vm.myForm.userName.$valid &amp;&amp; vm.username" class="text-emerald-600 text-xs font-bold mt-1.5"&gt;
            ✓ Username is available.
        &lt;/div&gt;
    &lt;/form&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('customValApp', [])
    .directive('checkUsername', function() {
        return {
            require: 'ngModel',
            link: function(scope, element, attrs, ctrl) {
                var disallowed = ['admin', 'root', 'superuser', 'system'];
                ctrl.$validators.blacklisted = function(modelValue, viewValue) {
                    var val = (modelValue || viewValue || '').toLowerCase();
                    return disallowed.indexOf(val) === -1;
                };
            }
        };
    })
    .controller('ValCtrl', function() {
        var vm = this;
        vm.username = '';
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 24, 'Forms, Validation & Controls', 'Custom Form Validation: ngModelController, $validators & Asynchronous $asyncValidators', $content24, 'custom validation in angularjs, ngmodelcontroller, $validators, $asyncvalidators, angular async validation, username availability check');
