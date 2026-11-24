<?php
// seed_angular_part3.php: Chapters 25 to 36

// -------------------------------------------------------------
// Chapter 25
// -------------------------------------------------------------
$content25 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">HTML prohibits nesting <code>&lt;form&gt;</code> tags inside each other. To solve this limitation in complex enterprise forms (such as multi-step wizards or repeatable rows), AngularJS provides the <strong>ng-form</strong> directive. Each <code>ng-form</code> behaves as an isolated sub-form while rolling its validation states up to the parent form.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Top-Level Form vs. Nested ng-form Sub-Forms</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Component</th>
                <th style="width: 35%;">HTML Representation</th>
                <th style="width: 40%;">Validation Behavior</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Parent <code>&lt;form&gt;</code></td>
                <td><code>&lt;form name="mainForm"&gt;</code></td>
                <td>Aggregates validation of all inputs and all child <code>ng-form</code> instances.</td>
            </tr>
            <tr>
                <td class="code-cell">Child <code>ng-form</code></td>
                <td><code>&lt;div ng-form="rowForm"&gt;</code></td>
                <td>Provides scoped validation for individual items in an <code>ng-repeat</code> loop or multi-step tabs.</td>
            </tr>
            <tr>
                <td class="code-cell">Field Uniqueness</td>
                <td><code>name="field_{{$index}}"</code></td>
                <td>Allows repeatable inputs in a list without name collision.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Dynamic Multi-Row Validation with ng-form</h3>
<p class="text-slate-600 mb-4">Observe how each repeatable line item tracks its own validity while reporting to the master form:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Dynamic Line-Item Invoicing with ng-form</div>
    <pre><code class="language-html">&lt;div ng-app="subFormApp" ng-controller="InvoiceCtrl as vm" class="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Dynamic Multi-Row Invoicing&lt;/h3&gt;

    &lt;form name="vm.invoiceForm" novalidate&gt;
        &lt;div ng-repeat="item in vm.items track by $index" ng-form="itemForm" class="p-3 bg-slate-50 border rounded-lg mb-3"&gt;
            &lt;div class="flex items-center justify-between mb-2"&gt;
                &lt;span class="text-xs font-bold text-slate-700"&gt;Line Item #{{ $index + 1 }}&lt;/span&gt;
                &lt;span class="text-[10px] font-bold px-2 py-0.5 rounded" ng-class="itemForm.$valid ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'"&gt;
                    {{ itemForm.$valid ? 'Row Valid ✓' : 'Incomplete' }}
                &lt;/span&gt;
            &lt;/div&gt;

            &lt;div class="grid grid-cols-2 gap-2"&gt;
                &lt;div&gt;
                    &lt;input type="text" name="desc" ng-model="item.desc" required class="w-full px-2 py-1 text-xs border rounded bg-white" placeholder="Item description *"&gt;
                &lt;/div&gt;
                &lt;div class="flex gap-2"&gt;
                    &lt;input type="number" name="price" ng-model="item.price" min="1" required class="w-full px-2 py-1 text-xs border rounded bg-white" placeholder="Price *"&gt;
                    &lt;button type="button" ng-click="vm.removeItem($index)" ng-disabled="vm.items.length &lt;= 1" class="text-red-500 hover:text-red-700 text-xs font-bold"&gt;&times;&lt;/button&gt;
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/div&gt;

        &lt;div class="flex justify-between items-center mt-4"&gt;
            &lt;button type="button" ng-click="vm.addItem()" class="px-3 py-1.5 bg-slate-800 text-white rounded text-xs font-bold"&gt;+ Add Line Item&lt;/button&gt;
            &lt;button type="submit" ng-disabled="vm.invoiceForm.$invalid" class="px-4 py-1.5 bg-emerald-600 disabled:bg-slate-300 text-white rounded text-xs font-bold shadow"&gt;Save Invoice&lt;/button&gt;
        &lt;/div&gt;
    &lt;/form&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('subFormApp', [])
    .controller('InvoiceCtrl', function() {
        var vm = this;
        vm.items = [{ desc: 'Cloud Computing Services', price: 1200 }];
        vm.addItem = function() {
            vm.items.push({ desc: '', price: null });
        };
        vm.removeItem = function(idx) {
            vm.items.splice(idx, 1);
        };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 25, 'Forms, Validation & Controls', 'Complex Multi-Section Forms: Sub-Forms with ng-form & Dynamic Field Arrays', $content25, 'angularjs ng-form, nested forms angularjs, subforms in angular, multi step form angularjs, repeat validation ng-repeat');

// -------------------------------------------------------------
// Chapter 26
// -------------------------------------------------------------
$content26 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>Directives</strong> are markers on DOM elements that tell AngularJS to attach specific behavior or transform the element and its children. Creating custom directives begins with the <strong>Directive Definition Object (DDO)</strong> and the <code>restrict</code> property.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Directive Restriction Options (E, A, C, M)</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Restriction Flag</th>
                <th style="width: 35%;">HTML Invocation Syntax</th>
                <th style="width: 40%;">Recommended Purpose</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>'E'</code> (Element)</td>
                <td><code>&lt;user-card&gt;&lt;/user-card&gt;</code></td>
                <td>Self-contained UI components and widgets (Best practice for modern UI).</td>
            </tr>
            <tr>
                <td class="code-cell"><code>'A'</code> (Attribute)</td>
                <td><code>&lt;div user-card&gt;&lt;/div&gt;</code></td>
                <td>Decorating existing elements with custom behavior (e.g. tooltip, datepicker). Default!</td>
            </tr>
            <tr>
                <td class="code-cell"><code>'C'</code> (Class)</td>
                <td><code>&lt;div class="user-card"&gt;&lt;/div&gt;</code></td>
                <td>Legacy styling-based directives (Rarely used).</td>
            </tr>
            <tr>
                <td class="code-cell"><code>'EA'</code> (Element &amp; Attribute)</td>
                <td>Permits both <code>&lt;user-card&gt;</code> and <code>&lt;div user-card&gt;</code></td>
                <td>Maximum flexibility for reusable UI libraries.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. First Custom Component Directive</h3>
<p class="text-slate-600 mb-4">Build a custom reusable component element with a template and link function:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Custom <user-badge> Component Directive</div>
    <pre><code class="language-html">&lt;div ng-app="ddoApp" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Custom Directives in Action&lt;/h3&gt;

    &lt;!-- Custom Element Invocation --&gt;
    &lt;user-badge name="Elena Vance" role="Lead Systems Architect" rank="Tier 4"&gt;&lt;/user-badge&gt;
    &lt;user-badge name="Marcus Sterling" role="Security Engineer" rank="Tier 2"&gt;&lt;/user-badge&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('ddoApp', [])
    .directive('userBadge', function() {
        return {
            restrict: 'E', // Element only
            template: 
                '&lt;div class="p-3 bg-slate-50 border rounded-lg mb-2 flex items-center justify-between"&gt;' +
                    '&lt;div&gt;' +
                        '&lt;strong class="text-slate-800 text-sm block"&gt;{{ userName }}&lt;/strong&gt;' +
                        '&lt;span class="text-xs text-slate-500"&gt;{{ userRole }}&lt;/span&gt;' +
                    '&lt;/div&gt;' +
                    '&lt;span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded"&gt;{{ userRank }}&lt;/span&gt;' +
                '&lt;/div&gt;',
            link: function(scope, element, attrs) {
                scope.userName = attrs.name || 'Anonymous';
                scope.userRole = attrs.role || 'Contributor';
                scope.userRank = attrs.rank || 'Standard';
            }
        };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 26, 'Custom Directives Masterclass', 'Directive Definition Object (DDO), Restrict Types (E, A, C, M) & Templates', $content26, 'angularjs custom directive, ddo directive definition object, restrict in angularjs, element vs attribute directive, angular template');

// -------------------------------------------------------------
// Chapter 27
// -------------------------------------------------------------
$content27 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">By default, directives inherit their parent scope. To build truly reusable, modular components that do not pollute or collide with parent variables, you must declare an <strong>Isolated Scope</strong> (<code>scope: {}</code>) using the 3 scope binding symbols: <code>@</code>, <code>=</code>, and <code>&</code>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">The 3 Isolated Scope Binding Prefixes: @, =, and &</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 20%;">Symbol</th>
                <th style="width: 25%;">Binding Nature</th>
                <th style="width: 25%;">HTML Usage</th>
                <th style="width: 30%;">Underlying Mechanism</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>@</code> (At Symbol)</td>
                <td>One-way String</td>
                <td><code>title="{{ parentTitle }}"</code></td>
                <td>Reads interpolated string attribute. Parent change updates child, but child cannot mutate parent.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>=</code> (Equals)</td>
                <td>Two-way Object Binding</td>
                <td><code>model="parentObject"</code></td>
                <td>Direct reference to parent model property. Any child edit directly mutates the parent model!</td>
            </tr>
            <tr>
                <td class="code-cell"><code>&</code> (Ampersand)</td>
                <td>Callback Expression</td>
                <td><code>on-save="saveUser(id)"</code></td>
                <td>Wraps parent function inside a closure. Allows child to invoke parent actions with local parameter maps.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Master Isolated Scope Component</h3>
<p class="text-slate-600 mb-4">Observe all three binding types working harmoniously inside a single reusable component:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Isolated Scope (@, =, &) Live Component</div>
    <pre><code class="language-html">&lt;div ng-app="isolatedApp" ng-controller="ParentDashboardCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-1"&gt;Parent Dashboard&lt;/h3&gt;
    &lt;p class="text-xs text-slate-500 mb-4"&gt;Parent Account Balance: &lt;strong class="text-emerald-600"&gt;${{ vm.account.balance }}&lt;/strong&gt;&lt;/p&gt;

    &lt;!-- Custom Directive with Isolated Scope --&gt;
    &lt;account-card 
        card-title="Corporate Debit" 
        card-data="vm.account" 
        on-deposit="vm.handleDeposit(amount)"&gt;
    &lt;/account-card&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('isolatedApp', [])
    .controller('ParentDashboardCtrl', function() {
        var vm = this;
        vm.account = { balance: 12500, holder: 'Nectra Digital LLC' };
        vm.handleDeposit = function(amt) {
            vm.account.balance += amt;
            alert('Parent received notification: Deposited $' + amt);
        };
    })
    .directive('accountCard', function() {
        return {
            restrict: 'E',
            scope: {
                cardTitle: '@', // One-way string (@)
                cardData: '=',   // Two-way object reference (=)
                onDeposit: '&'   // Parent callback function (&)
            },
            template: 
                '&lt;div class="p-4 bg-slate-50 border rounded-lg text-sm"&gt;' +
                    '&lt;span class="text-xs font-bold text-slate-500 block uppercase"&gt;{{ cardTitle }}&lt;/span&gt;' +
                    '&lt;div class="font-bold text-slate-900 mt-1"&gt;Holder: {{ cardData.holder }}&lt;/div&gt;' +
                    '&lt;div class="text-xs text-slate-600 mb-3"&gt;Internal Bal: ${{ cardData.balance }}&lt;/div&gt;' +
                    '&lt;button ng-click="onDeposit({ amount: 500 })" class="px-3 py-1.5 bg-emerald-600 text-white rounded text-xs font-bold shadow"&gt;' +
                        'Deposit $500 via Callback' +
                    '&lt;/button&gt;' +
                '&lt;/div&gt;'
        };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 27, 'Custom Directives Masterclass', 'Isolated Scope Mastery: @ (One-Way String), = (Two-Way Binding) & & (Callback Expression)', $content27, 'angularjs isolated scope, isolated scope symbols, @ in angularjs, = in angularjs, & callback expression, reusable angular component');

// -------------------------------------------------------------
// Chapter 28
// -------------------------------------------------------------
$content28 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Understanding the internal execution pipeline of directives is essential for advanced UI development. When AngularJS compiles a template, it executes in 3 distinct phases: <strong>compile</strong> (DOM template transformation), <strong>pre-link</strong> (before child link functions), and <strong>post-link</strong> (after all children are bound).</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">compile vs. pre-link vs. post-link Execution Pipeline</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Phase Function</th>
                <th style="width: 35%;">Available Arguments</th>
                <th style="width: 40%;">Primary Responsibility &amp; Safety Rule</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>compile(tElem, tAttrs)</code></td>
                <td>Template Element, Attributes. <strong>No Scope available yet!</strong></td>
                <td>Transforms DOM template structure once before instance replication (e.g. <code>ng-repeat</code>).</td>
            </tr>
            <tr>
                <td class="code-cell"><code>preLink(scope, elem, attrs)</code></td>
                <td>Scope, Instance Element, Attributes, Controllers.</td>
                <td>Rarely used; executes top-down before child directives link. Avoid DOM operations here.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>postLink(scope, elem, attrs)</code></td>
                <td>Scope, Instance Element, Attributes, Controllers.</td>
                <td><strong>Standard link function:</strong> Executes bottom-up after all children link. Safe for DOM listeners, watchers, and jQuery plugins.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Directives Lifecycle Audit</h3>
<p class="text-slate-600 mb-4">Inspect the exact sequence in which compile and link functions are called in the browser console:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Complete Compile & Link Execution Demonstration</div>
    <pre><code class="language-html">&lt;div ng-app="lifecycleApp" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Directive Lifecycle Tracer&lt;/h3&gt;

    &lt;lifecycle-logger label="Alpha Component"&gt;&lt;/lifecycle-logger&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('lifecycleApp', [])
    .directive('lifecycleLogger', function() {
        return {
            restrict: 'E',
            compile: function(tElement, tAttrs) {
                console.log('[1. Compile Phase] Template transformed. No scope exists yet.');
                tElement.append('&lt;p class="text-xs text-slate-500 mt-1"&gt;Transformed during compile phase&lt;/p&gt;');

                return {
                    pre: function(scope, iElement, iAttrs) {
                        console.log('[2. Pre-Link Phase] Top-down link started for: ' + iAttrs.label);
                    },
                    post: function(scope, iElement, iAttrs) {
                        console.log('[3. Post-Link Phase] DOM and children ready! Safe for event listeners.');
                        iElement.find('p').append('&lt;span class="text-emerald-600 font-bold block"&gt;✓ Post-link completed.&lt;/span&gt;');
                    }
                };
            }
        };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 28, 'Custom Directives Masterclass', 'Directive Lifecycle: compile, pre-link & post-link Execution Pipeline', $content28, 'angularjs directive lifecycle, compile vs link, pre-link vs post-link, template element vs instance element, angular dom compilation');

// -------------------------------------------------------------
// Chapter 29
// -------------------------------------------------------------
$content29 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>Transclusion</strong> (enabled via <code>transclude: true</code>) allows a custom directive to wrap external content supplied by the consumer. It projects whatever HTML exists inside the directive tags into an designated target slot marked with <strong>ng-transclude</strong>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Transclusion Core Concepts</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Concept</th>
                <th style="width: 35%;">Configuration Setting</th>
                <th style="width: 40%;">Scope Rule</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Basic Transclusion</td>
                <td><code>transclude: true</code></td>
                <td>The transcluded content retains access to the <strong>parent scope</strong> where it was written!</td>
            </tr>
            <tr>
                <td class="code-cell">Target Insertion Slot</td>
                <td><code>&lt;div ng-transclude&gt;&lt;/div&gt;</code></td>
                <td>Designates where the projected external HTML will be inserted in the component template.</td>
            </tr>
            <tr>
                <td class="code-cell">Multi-Slot Transclusion</td>
                <td><code>transclude: { title: '?cardTitle', body: 'cardBody' }</code></td>
                <td>(Introduced in AngularJS 1.5) Enables named multi-slot content projection.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Reusable Modal / Card Container with Transclusion</h3>
<p class="text-slate-600 mb-4">Project arbitrary child elements into an enclosed, styled modal shell:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Custom <modal-dialog> Component with ng-transclude</div>
    <pre><code class="language-html">&lt;div ng-app="transcludeApp" ng-controller="ShellCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Container Component with Transclusion&lt;/h3&gt;

    &lt;!-- Using the custom container --&gt;
    &lt;modal-panel heading="System Alert"&gt;
        &lt;p class="text-xs text-slate-600 mb-2"&gt;
            Parent scope model: &lt;strong class="text-indigo-600"&gt;{{ vm.currentRelease }}&lt;/strong&gt;
        &lt;/p&gt;
        &lt;button ng-click="vm.acknowledge()" class="px-3 py-1 bg-slate-800 text-white rounded text-xs font-bold"&gt;
            Acknowledge in Parent
        &lt;/button&gt;
    &lt;/modal-panel&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('transcludeApp', [])
    .controller('ShellCtrl', function() {
        var vm = this;
        vm.currentRelease = 'Release Candidate v3.1';
        vm.acknowledge = function() {
            alert('Parent acknowledged: ' + vm.currentRelease);
        };
    })
    .directive('modalPanel', function() {
        return {
            restrict: 'E',
            transclude: true, // Enables content projection
            scope: {
                heading: '@'
            },
            template: 
                '&lt;div class="border-2 border-emerald-400 rounded-xl overflow-hidden shadow-sm"&gt;' +
                    '&lt;div class="bg-emerald-600 text-white px-4 py-2 font-bold text-xs uppercase"&gt;{{ heading }}&lt;/div&gt;' +
                    '&lt;div class="p-4 bg-emerald-50/50" ng-transclude&gt;&lt;/div&gt;' + // Projection Target
                '&lt;/div&gt;'
        };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 29, 'Custom Directives Masterclass', 'Transclusion & Multi-Slot Content Projection (transclude: true & ng-transclude)', $content29, 'angularjs transclusion, transclude: true, ng-transclude, content projection in angularjs, reusable container directive');

// -------------------------------------------------------------
// Chapter 30
// -------------------------------------------------------------
$content30 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">When multiple directives need to coordinate actions (such as an accordion with multiple expandable panels or tabs with tab headers), child directives can communicate with parent directives by using the <strong>require</strong> attribute to inject the parent's controller.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">require Prefix Modifiers: ^ and ?</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Syntax Pattern</th>
                <th style="width: 35%;">Lookup Mechanism</th>
                <th style="width: 40%;">Behavior If Missing</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>require: 'parent'</code></td>
                <td>Looks for parent controller on the <strong>same</strong> element.</td>
                <td>Throws <code>$compile:ctreq</code> exception if controller is not found.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>require: '^parent'</code></td>
                <td>Walks up the DOM tree looking on parent elements.</td>
                <td>Throws exception if not found anywhere in ancestor chain.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>require: '?^parent'</code></td>
                <td>Walks up the DOM tree, but makes dependency optional.</td>
                <td>Passes <code>null</code> to link function if not found (Graceful fallback).</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Master Accordion Component Architecture</h3>
<p class="text-slate-600 mb-4">Observe how child <code>&lt;accordion-panel&gt;</code> directives register with parent <code>&lt;accordion-group&gt;</code>:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Accordion Directive Communication via require</div>
    <pre><code class="language-html">&lt;div ng-app="coordApp" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Coordinated Accordion Directives&lt;/h3&gt;

    &lt;accordion-group&gt;
        &lt;accordion-panel heading="1. Cloud Infrastructure"&gt;
            Kubernetes clusters deployed across 3 availability zones with automated horizontal pod autoscaling.
        &lt;/accordion-panel&gt;
        &lt;accordion-panel heading="2. Database Persistence"&gt;
            PostgreSQL high-availability replicas with automated multi-region backups and Redis caching.
        &lt;/accordion-panel&gt;
    &lt;/accordion-group&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('coordApp', [])
    // 1. Parent Controller & Directive
    .directive('accordionGroup', function() {
        return {
            restrict: 'E',
            transclude: true,
            template: '&lt;div class="space-y-2" ng-transclude&gt;&lt;/div&gt;',
            controller: function() {
                var panels = [];
                this.addPanel = function(panel) {
                    panels.push(panel);
                    if (panels.length === 1) panel.isOpen = true; // Open first by default
                };
                this.closeOthers = function(activePanel) {
                    panels.forEach(function(p) {
                        if (p !== activePanel) p.isOpen = false;
                    });
                };
            }
        };
    })
    // 2. Child Directive requiring Parent
    .directive('accordionPanel', function() {
        return {
            restrict: 'E',
            require: '^accordionGroup', // Look up ancestor chain
            transclude: true,
            scope: { heading: '@' },
            template: 
                '&lt;div class="border rounded-lg overflow-hidden"&gt;' +
                    '&lt;button ng-click="toggle()" class="w-full px-4 py-2 bg-slate-100 hover:bg-slate-200 flex justify-between items-center text-xs font-bold text-slate-800"&gt;' +
                        '{{ heading }} &lt;span&gt;{{ isOpen ? "▲" : "▼" }}&lt;/span&gt;' +
                    '&lt;/button&gt;' +
                    '&lt;div ng-show="isOpen" class="p-3 bg-white text-xs text-slate-600 border-t" ng-transclude&gt;&lt;/div&gt;' +
                '&lt;/div&gt;',
            link: function(scope, element, attrs, groupCtrl) {
                scope.isOpen = false;
                groupCtrl.addPanel(scope);
                scope.toggle = function() {
                    scope.isOpen = !scope.isOpen;
                    if (scope.isOpen) groupCtrl.closeOthers(scope);
                };
            }
        };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 30, 'Custom Directives Masterclass', 'Directive Communication: require Attribute & Parent-Child Shared Controllers', $content30, 'angularjs directive require, require: ^parent, parent child directive communication, accordion directive in angular, shared controllers angular');

// -------------------------------------------------------------
// Chapter 31
// -------------------------------------------------------------
$content31 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>Dependency Injection (DI)</strong> is a design pattern in which software components request their required services rather than creating them directly. In AngularJS, the injector inspects parameter names to provide dependencies, requiring strict annotation standards to survive production JavaScript minification.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Minification Survival: Implicit vs. Inline Array Annotation</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Annotation Style</th>
                <th style="width: 35%;">Syntax Example</th>
                <th style="width: 40%;">Production Vulnerability</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Implicit DI (Unsafe)</td>
                <td><code>.controller('C', function($scope, $http) { ... })</code></td>
                <td><strong>Crashes in Production:</strong> Minifier renames parameters to <code>function(a, b)</code>, causing <code>Unknown provider: aProvider</code>!</td>
            </tr>
            <tr>
                <td class="code-cell">Inline Array (Standard)</td>
                <td><code>['$scope', '$http', function($scope, $http) { ... }]</code></td>
                <td><strong>100% Minification Safe:</strong> Strings are preserved by minifiers.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$inject</code> Property</td>
                <td><code>CtrlFn.$inject = ['$scope', '$http'];</code></td>
                <td>Ideal for ES6 class controllers and clean modular architectures.</td>
            </tr>
            <tr>
                <td class="code-cell">Strict DI Mode</td>
                <td><code>&lt;html ng-app="app" ng-strict-di&gt;</code></td>
                <td>Throws compile error immediately if any component lacks explicit DI annotation.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Production Minification-Safe DI Pattern</h3>
<p class="text-slate-600 mb-4">Use inline array notation or <code>$inject</code> to ensure uninterrupted production deployments:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Strict Minification-Safe Dependency Injection</div>
    <pre><code class="language-html">&lt;div ng-app="diApp" ng-strict-di ng-controller="SafeDiCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-1"&gt;Strict Dependency Injection&lt;/h3&gt;
    &lt;p class="text-xs text-slate-500 mb-4"&gt;Protected with ng-strict-di&lt;/p&gt;

    &lt;div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-950 text-xs"&gt;
        &lt;div&gt;Active Service: &lt;strong&gt;{{ vm.serviceName }}&lt;/strong&gt;&lt;/div&gt;
        &lt;div class="mt-1"&gt;Service Status: &lt;strong&gt;{{ vm.serviceStatus }}&lt;/strong&gt;&lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('diApp', [])
    // 1. Service with explicit array annotation
    .factory('MetricsService', ['$window', function($window) {
        return {
            getMetrics: function() {
                return 'Host: ' + $window.location.hostname + ' | Uptime: 99.9%';
            }
        };
    }])
    // 2. Controller with explicit array annotation (Minification-Proof)
    .controller('SafeDiCtrl', ['MetricsService', function(MetricsService) {
        var vm = this;
        vm.serviceName = 'Enterprise Telemetry Service';
        vm.serviceStatus = MetricsService.getMetrics();
    }]);
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 31, 'Services, Factories & DI', 'Dependency Injection (DI) Architecture: Inline Array Annotation & Minification Safety (ng-strict-di)', $content31, 'angularjs dependency injection, ng-strict-di, angular minification, $inject property, inline array annotation angular');

// -------------------------------------------------------------
// Chapter 32
// -------------------------------------------------------------
$content32 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">AngularJS provides 5 distinct "recipes" for creating singleton services: <strong>Value</strong>, <strong>Constant</strong>, <strong>Factory</strong>, <strong>Service</strong>, and <strong>Provider</strong>. Choosing the correct recipe ensures optimal encapsulation, testability, and configuration capability.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">The 5 Service Recipes Compared</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 20%;">Recipe</th>
                <th style="width: 25%;">Instantiation Style</th>
                <th style="width: 25%;">Configurable in .config()?</th>
                <th style="width: 30%;">Ideal Real-World Purpose</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>constant</code></td>
                <td>Fixed primitive / object</td>
                <td><strong>YES</strong> (Available everywhere)</td>
                <td>Global constants, API endpoints, environment keys.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>value</code></td>
                <td>Mutable primitive / object</td>
                <td>NO</td>
                <td>Global runtime configuration variables.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>factory</code></td>
                <td>Invokes function, returns object</td>
                <td>NO</td>
                <td><strong>Industry Standard:</strong> Data services, utility functions, business logic.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>service</code></td>
                <td>Instantiated with <code>new Service()</code></td>
                <td>NO</td>
                <td>Class-based services using <code>this.method = ...</code>.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>provider</code></td>
                <td>Constructor with <code>$get()</code> method</td>
                <td><strong>YES</strong> (via provider phase)</td>
                <td>Configurable modules before app startup (e.g., setting global auth tokens).</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Factory vs. Service in Action</h3>
<p class="text-slate-600 mb-4">Compare the returning of an object literal (factory) vs constructor methods (service):</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Factory vs Service Implementation</div>
    <pre><code class="language-html">&lt;div ng-app="recipesApp" ng-controller="RecipesCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Service Recipes Masterclass&lt;/h3&gt;

    &lt;div class="space-y-3 text-xs"&gt;
        &lt;div class="p-3 bg-slate-50 border rounded-lg"&gt;
            &lt;span class="font-bold text-slate-500 block uppercase"&gt;Constant Recipe:&lt;/span&gt;
            &lt;span class="text-slate-800 font-mono"&gt;API URL: {{ vm.apiUrl }}&lt;/span&gt;
        &lt;/div&gt;

        &lt;div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-950"&gt;
            &lt;span class="font-bold text-emerald-700 block uppercase"&gt;Factory Recipe:&lt;/span&gt;
            &lt;span&gt;Multiplied 6 &times; 7 = &lt;strong&gt;{{ vm.calcResult }}&lt;/strong&gt;&lt;/span&gt;
        &lt;/div&gt;

        &lt;div class="p-3 bg-indigo-50 border border-indigo-200 rounded-lg text-indigo-950"&gt;
            &lt;span class="font-bold text-indigo-700 block uppercase"&gt;Service Recipe (Constructor):&lt;/span&gt;
            &lt;span&gt;Generated UUID: &lt;strong class="font-mono"&gt;{{ vm.sessionUuid }}&lt;/strong&gt;&lt;/span&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('recipesApp', [])
    // 1. Constant Recipe
    .constant('API_ENDPOINT', 'https://api.nectradigital.com/v2')

    // 2. Factory Recipe (Returns object literal)
    .factory('MathFactory', function() {
        return {
            multiply: function(a, b) { return a * b; }
        };
    })

    // 3. Service Recipe (Constructor with `this`)
    .service('SessionService', function() {
        this.generateSessionId = function() {
            return 'SESSION-' + Math.random().toString(36).substr(2, 9).toUpperCase();
        };
    })

    // Controller consuming all recipes
    .controller('RecipesCtrl', ['API_ENDPOINT', 'MathFactory', 'SessionService', function(API_ENDPOINT, MathFactory, SessionService) {
        var vm = this;
        vm.apiUrl = API_ENDPOINT;
        vm.calcResult = MathFactory.multiply(6, 7);
        vm.sessionUuid = SessionService.generateSessionId();
    }]);
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 32, 'Services, Factories & DI', 'Service vs Factory vs Provider vs Value vs Constant (The 5 Service Recipes)', $content32, 'angularjs service vs factory, factory vs provider, angular value vs constant, angular service recipes, angularjs singleton services');

// -------------------------------------------------------------
// Chapter 33
// -------------------------------------------------------------
$content33 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <strong>$http</strong> service is AngularJS's core HTTP client for communicating with remote RESTful APIs. Built on XMLHttpRequest, it returns standard ES6-style promises (<code>.then()</code> and <code>.catch()</code>) and automatically serializes JavaScript objects to JSON.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">$http Service Method Blueprint</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Shortcut Method</th>
                <th style="width: 35%;">Syntax Example</th>
                <th style="width: 40%;">Response Object Structure</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>$http.get(url, [config])</code></td>
                <td><code>$http.get('/api/users')</code></td>
                <td><code>response.data</code> &mdash; Parsed JSON payload from server.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$http.post(url, data)</code></td>
                <td><code>$http.post('/api/save', payload)</code></td>
                <td><code>response.status</code> &mdash; HTTP status code (200, 201, etc.).</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$http.put / delete</code></td>
                <td><code>$http.delete('/api/item/' + id)</code></td>
                <td><code>response.headers()</code> &mdash; Header getter function.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Master RESTful API Integration</h3>
<p class="text-slate-600 mb-4">Execute remote API calls with loading spinners and error handling:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: $http REST Client with Async State Management</div>
    <pre><code class="language-html">&lt;div ng-app="httpDemoApp" ng-controller="HttpCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;div class="flex items-center justify-between mb-3"&gt;
        &lt;h3 class="text-lg font-bold text-slate-900"&gt;Remote API Service&lt;/h3&gt;
        &lt;button ng-click="vm.fetchUsers()" ng-disabled="vm.isLoading" class="px-3 py-1 bg-emerald-600 text-white rounded text-xs font-bold shadow"&gt;
            {{ vm.isLoading ? 'Fetching...' : 'Reload API' }}
        &lt;/button&gt;
    &lt;/div&gt;

    &lt;div ng-if="vm.isLoading" class="p-4 text-center text-xs text-slate-500"&gt;
        Querying remote cloud server...
    &lt;/div&gt;

    &lt;div ng-if="!vm.isLoading" class="space-y-2"&gt;
        &lt;div ng-repeat="u in vm.users" class="p-2.5 bg-slate-50 border rounded-lg flex justify-between items-center text-xs"&gt;
            &lt;span class="font-bold text-slate-800"&gt;{{ u.name }}&lt;/span&gt;
            &lt;span class="text-slate-500 font-mono"&gt;{{ u.role }}&lt;/span&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('httpDemoApp', [])
    .controller('HttpCtrl', ['$http', '$timeout', function($http, $timeout) {
        var vm = this;
        vm.users = [];
        vm.isLoading = false;

        vm.fetchUsers = function() {
            vm.isLoading = true;
            // Simulated HTTP call via $timeout to ensure offline test reliability
            $timeout(function() {
                vm.users = [
                    { id: 1, name: 'Instructor NectraDigital', role: 'Staff Principal' },
                    { id: 2, name: 'Jordan Hayes', role: 'Platform Architect' },
                    { id: 3, name: 'Samantha Wu', role: 'Data Engineer' }
                ];
                vm.isLoading = false;
            }, 600);
        };

        // Initial fetch on mount
        vm.fetchUsers();
    }]);
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 33, 'Services, Factories & DI', 'HTTP Communication with $http: REST APIs, Headers, Config & Response Transformation', $content33, 'angularjs $http, rest api in angularjs, $http.get, $http.post, promises in $http, angular http client');

// -------------------------------------------------------------
// Chapter 34
// -------------------------------------------------------------
$content34 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <strong>$q</strong> service is AngularJS's built-in promise library modeled after the Kris Kowal Q specification. It enables asynchronous programming without callback hell, supporting <code>$q.defer()</code>, <code>$q.all()</code> (parallel fetching), and <code>$q.resolve()</code> / <code>$q.reject()</code>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">$q Promise Operations</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Method</th>
                <th style="width: 35%;">Syntax Blueprint</th>
                <th style="width: 40%;">Primary Purpose</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>$q.defer()</code></td>
                <td><code>var def = $q.defer(); def.resolve(val);</code></td>
                <td>Creates a deferred object exposing a <code>promise</code> property and resolve/reject handlers.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$q.all([p1, p2])</code></td>
                <td><code>$q.all([getUser(), getOrders()])</code></td>
                <td>Waits for multiple independent promises to resolve before triggering <code>.then()</code>.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$q.reject(reason)</code></td>
                <td><code>return $q.reject('Unauthorized');</code></td>
                <td>Returns a pre-rejected promise to halt promise execution chains.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Coordinating Parallel Async Calls with $q.all</h3>
<p class="text-slate-600 mb-4">Combine multiple asynchronous operations into a single synchronized result:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Parallel Async Execution with $q.all</div>
    <pre><code class="language-html">&lt;div ng-app="qApp" ng-controller="QCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Parallel $q.all Orchestrator&lt;/h3&gt;

    &lt;button ng-click="vm.runBatch()" ng-disabled="vm.status === 'Running...'" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold shadow mb-4"&gt;
        Execute Parallel Tasks
    &lt;/button&gt;

    &lt;div class="p-4 bg-slate-50 border rounded-lg text-xs space-y-2"&gt;
        &lt;div&gt;Status: &lt;strong class="text-indigo-600"&gt;{{ vm.status }}&lt;/strong&gt;&lt;/div&gt;
        &lt;div ng-if="vm.results" class="p-2.5 bg-white border rounded font-mono text-slate-700"&gt;
            &lt;div&gt;&bull; Task 1: {{ vm.results[0] }}&lt;/div&gt;
            &lt;div&gt;&bull; Task 2: {{ vm.results[1] }}&lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('qApp', [])
    .controller('QCtrl', ['$q', '$timeout', function($q, $timeout) {
        var vm = this;
        vm.status = 'Ready';
        vm.results = null;

        function fetchAuth() {
            var def = $q.defer();
            $timeout(function() { def.resolve('Auth Token: 2026-OK'); }, 400);
            return def.promise;
        }

        function fetchProfile() {
            var def = $q.defer();
            $timeout(function() { def.resolve('Profile Loaded: Alex Architect'); }, 700);
            return def.promise;
        }

        vm.runBatch = function() {
            vm.status = 'Running...';
            vm.results = null;

            $q.all([fetchAuth(), fetchProfile()]).then(function(responses) {
                vm.status = 'Both tasks completed successfully!';
                vm.results = responses;
            });
        };
    }]);
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 34, 'Services, Factories & DI', 'Promises & Asynchronous Control Flow with $q ($q.defer(), $q.all(), $q.reject())', $content34, 'angularjs $q service, promises in angularjs, $q.defer, $q.all in angularjs, async control flow, deferred object angular');

// -------------------------------------------------------------
// Chapter 35
// -------------------------------------------------------------
$content35 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>HTTP Interceptors</strong> in AngularJS are factory services that intercept and transform requests and responses globally across the entire application. They are essential for attaching JWT authorization tokens, tracking global network loading indicators, and catching 401 / 403 API errors in a central location.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">The 4 HTTP Interceptor Hooks</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Hook Function</th>
                <th style="width: 35%;">Trigger Point</th>
                <th style="width: 40%;">Typical Responsibility</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>request(config)</code></td>
                <td>Before <code>$http</code> sends payload over the wire.</td>
                <td>Inject <code>Authorization: Bearer &lt;token&gt;</code> headers or timestamps.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>requestError(rejection)</code></td>
                <td>Request configuration failed before dispatch.</td>
                <td>Logging client misconfiguration errors.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>response(response)</code></td>
                <td>Server returned 2xx HTTP success.</td>
                <td>Unwrapping data envelopes or logging response times.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>responseError(rejection)</code></td>
                <td>Server returned 4xx or 5xx HTTP error status.</td>
                <td>Redirecting on <code>401 Unauthorized</code> or alerting user to network outages.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Registering a Global Auth Interceptor</h3>
<p class="text-slate-600 mb-4">Register an interceptor factory into <code>$httpProvider.interceptors</code> inside the module config phase:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Global JWT & Central Error Interceptor</div>
    <pre><code class="language-html">&lt;div ng-app="interceptorApp" ng-controller="MainCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-2"&gt;Global $http Interceptor Hub&lt;/h3&gt;
    &lt;p class="text-xs text-slate-500 mb-4"&gt;Inspect network headers injected globally on every outgoing call.&lt;/p&gt;

    &lt;button ng-click="vm.testCall()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-bold shadow mb-4"&gt;
        Simulate API Request
    &lt;/button&gt;

    &lt;div class="p-3 bg-slate-50 border rounded-lg text-xs"&gt;
        &lt;span class="font-bold text-slate-700 block mb-1"&gt;Last Intercepted Headers:&lt;/span&gt;
        &lt;pre class="bg-white p-2 rounded border text-emerald-700 font-mono"&gt;{{ vm.lastConfig | json }}&lt;/pre&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('interceptorApp', [])
    // 1. Define Interceptor Factory
    .factory('AuthInterceptor', ['$q', function($q) {
        return {
            request: function(config) {
                // Automatically attach Authorization header
                config.headers = config.headers || {};
                config.headers['Authorization'] = 'Bearer nectra_session_jwt_2026';
                config.headers['X-Client-Version'] = '1.8.3';
                console.log('Interceptor injected authorization token into request:', config.url);
                return config;
            },
            responseError: function(rejection) {
                if (rejection.status === 401) {
                    console.warn('Unauthorized access: Redirecting to login...');
                }
                return $q.reject(rejection);
            }
        };
    }])
    // 2. Register Interceptor with $httpProvider
    .config(['$httpProvider', function($httpProvider) {
        $httpProvider.interceptors.push('AuthInterceptor');
    }])
    .controller('MainCtrl', function() {
        var vm = this;
        vm.lastConfig = {
            'Authorization': 'Bearer nectra_session_jwt_2026',
            'X-Client-Version': '1.8.3'
        };
        vm.testCall = function() {
            alert('Request intercepted! Injected Bearer token into outgoing header.');
        };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 35, 'Services, Factories & DI', '$http Interceptors: Global Request/Response Transforms, JWT Auth & Centralized Error Handlers', $content35, 'angularjs http interceptors, $httpprovider.interceptors, jwt authentication angularjs, responseerror in angular, global error handler angular');

// -------------------------------------------------------------
// Chapter 36
// -------------------------------------------------------------
$content36 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">A <strong>Single Page Application (SPA)</strong> loads a single shell HTML page and dynamically updates the view as the user interacts, without reloading the browser. The official <strong>ngRoute</strong> module maps hash-based or HTML5 URLs to specific templates and controllers using <strong>$routeProvider</strong>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">ngRoute Core Architecture Components</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Component</th>
                <th style="width: 35%;">Syntax Blueprint</th>
                <th style="width: 40%;">Primary Responsibility</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>$routeProvider.when()</code></td>
                <td><code>.when('/about', { template: '...', controller: '...' })</code></td>
                <td>Configures route paths, associated templates, and controller bindings.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>.otherwise()</code></td>
                <td><code>.otherwise({ redirectTo: '/' })</code></td>
                <td>Configures the fallback route if an unrecognized URL is entered.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>&lt;div ng-view&gt;&lt;/div&gt;</code></td>
                <td>Placeholder in HTML shell</td>
                <td>The viewport container where the matched route's template is rendered.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Master SPA Router Setup</h3>
<p class="text-slate-600 mb-4">Observe how client-side routing switches views seamlessly inside <code>ng-view</code>:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Complete Single Page App with $routeProvider & ng-view</div>
    <pre><code class="language-html">&lt;div ng-app="spaDemoApp" class="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;div class="flex items-center justify-between border-b pb-3 mb-4"&gt;
        &lt;h3 class="text-lg font-bold text-slate-900"&gt;Nectra SPA Hub&lt;/h3&gt;
        &lt;nav class="space-x-2 text-xs font-bold"&gt;
            &lt;a href="#!/home" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded text-slate-700"&gt;Home&lt;/a&gt;
            &lt;a href="#!/services" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded text-slate-700"&gt;Services&lt;/a&gt;
            &lt;a href="#!/contact" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded text-slate-700"&gt;Contact&lt;/a&gt;
        &lt;/nav&gt;
    &lt;/div&gt;

    &lt;!-- Route Viewport Target --&gt;
    &lt;div ng-view class="p-4 bg-slate-50 border rounded-lg"&gt;&lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
// Note: Requires angular-route.min.js in production builds
angular.module('spaDemoApp', ['ngRoute'])
    .config(['$routeProvider', function($routeProvider) {
        $routeProvider
            .when('/home', {
                template: '&lt;h4 class="font-bold text-slate-900 mb-1"&gt;Welcome to the SPA Home&lt;/h4&gt;&lt;p class="text-xs text-slate-600"&gt;Fast, lightweight client-side navigation without full-page reloads.&lt;/p&gt;'
            })
            .when('/services', {
                template: '&lt;h4 class="font-bold text-slate-900 mb-1"&gt;Enterprise Services&lt;/h4&gt;&lt;p class="text-xs text-slate-600"&gt;Cloud migration, microservices architecture, and legacy modernizations.&lt;/p&gt;'
            })
            .when('/contact', {
                template: '&lt;h4 class="font-bold text-slate-900 mb-1"&gt;Contact Support&lt;/h4&gt;&lt;p class="text-xs text-slate-600"&gt;Email: support@nectradigital.com | Open 24/7.&lt;/p&gt;'
            })
            .otherwise({ redirectTo: '/home' });
    }]);
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 36, 'Routing & Single Page Applications (SPA)', 'Single Page Application (SPA) Fundamentals & Routing with ngRoute ($routeProvider)', $content36, 'angularjs ngroute, $routeprovider, ng-view, single page application angular, spa routing in angularjs, client side routing');
