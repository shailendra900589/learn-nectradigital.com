<?php
// seed_angular_part4.php: Chapters 37 to 47

// -------------------------------------------------------------
// Chapter 37
// -------------------------------------------------------------
$content37 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Real-world applications require parameterized URLs (e.g. <code>/users/:id</code>). AngularJS extracts path parameters into the <strong>$routeParams</strong> service, and broadcasts lifecycle events like <strong>$routeChangeStart</strong> and <strong>$routeChangeSuccess</strong> across <code>$rootScope</code> for page transitions.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">$route Lifecycle Events</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Lifecycle Event</th>
                <th style="width: 35%;">Trigger Moment</th>
                <th style="width: 40%;">Crucial Use Case</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>$routeChangeStart</code></td>
                <td>Before the route transition begins.</td>
                <td><strong>Authentication Guard:</strong> Check if user is logged in; cancel transition via <code>event.preventDefault()</code>.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$routeChangeSuccess</code></td>
                <td>After all resolve dependencies finish and template compiles.</td>
                <td>Updating page titles, triggering analytics page views, scrolling to top.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$routeChangeError</code></td>
                <td>A resolve promise was rejected or template fetch failed.</td>
                <td>Redirecting to 404 or 500 error pages.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Dynamic Route Parameters &amp; Route Guard</h3>
<p class="text-slate-600 mb-4">Extract route parameters and intercept transitions globally:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Parameterized Route & Authentication Guard</div>
    <pre><code class="language-html">&lt;div ng-app="paramRouteApp" ng-controller="RouterDemoCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-2"&gt;Dynamic Route Parameters&lt;/h3&gt;
    &lt;p class="text-xs text-slate-500 mb-4"&gt;Simulating Route: &lt;code class="bg-slate-100 px-1 py-0.5 rounded"&gt;/organization/:orgId/project/:projId&lt;/code&gt;&lt;/p&gt;

    &lt;div class="p-3 bg-slate-50 border rounded-lg text-xs space-y-1 font-mono mb-4"&gt;
        &lt;div&gt;$routeParams.orgId: &lt;strong class="text-emerald-700"&gt;{{ vm.params.orgId }}&lt;/strong&gt;&lt;/div&gt;
        &lt;div&gt;$routeParams.projId: &lt;strong class="text-indigo-700"&gt;{{ vm.params.projId }}&lt;/strong&gt;&lt;/div&gt;
    &lt;/div&gt;

    &lt;div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs text-emerald-900"&gt;
        &lt;span class="font-bold"&gt;Route Guard Status:&lt;/span&gt; Access Authorized by $routeChangeStart guard.
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('paramRouteApp', [])
    .controller('RouterDemoCtrl', function() {
        var vm = this;
        // Simulated $routeParams inspection
        vm.params = { orgId: 'nectra-corp', projId: 'alpha-cloud-99' };
    })
    .run(['$rootScope', function($rootScope) {
        $rootScope.$on('$routeChangeStart', function(event, next, current) {
            console.log('Navigating to new route. Verifying permissions...');
        });
    }]);
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 37, 'Routing & Single Page Applications (SPA)', 'Dynamic Route Parameters with $routeParams & Route Change Events ($routeChangeStart)', $content37, 'angularjs $routeparams, $routechangestart, route guard angularjs, route parameters in angular, angular navigation events');

// -------------------------------------------------------------
// Chapter 38
// -------------------------------------------------------------
$content38 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <strong>resolve</strong> property on a route configuration is a map of dependencies that must resolve <em>before</em> the controller is instantiated and the template is rendered. This eliminates empty page flicker and ensures essential data is pre-fetched before the user sees the page.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Resolving Dependencies: Traditional vs. Route Resolve</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Approach</th>
                <th style="width: 35%;">User Experience (UX)</th>
                <th style="width: 40%;">Code Architecture</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Fetch inside Controller</td>
                <td>Page loads empty, displays flicker, then fills with data after latency delay.</td>
                <td>Controller must maintain <code>isLoading</code> boolean flags and manual try-catch states.</td>
            </tr>
            <tr>
                <td class="code-cell">Route <code>resolve: {}</code></td>
                <td><strong>Smooth Transition:</strong> Page only transitions when complete data is fully loaded and validated.</td>
                <td>Resolved data is injected directly into controller as a standard constructor argument!</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Master Resolve Configuration</h3>
<p class="text-slate-600 mb-4">Inject pre-resolved data straight into your controller parameter list:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Pre-Fetching Data with Route Resolve</div>
    <pre><code class="language-html">&lt;div ng-app="resolveApp" ng-controller="ProjectCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-1"&gt;{{ vm.project.title }}&lt;/h3&gt;
    &lt;p class="text-xs text-slate-500 mb-4"&gt;Pre-Fetched via Route Resolve (Zero Viewport Flicker)&lt;/p&gt;

    &lt;div class="p-4 bg-slate-50 border rounded-lg text-xs space-y-2"&gt;
        &lt;div&gt;Repository: &lt;strong class="text-slate-800 font-mono"&gt;{{ vm.project.repo }}&lt;/strong&gt;&lt;/div&gt;
        &lt;div&gt;Active Branch: &lt;strong class="text-emerald-700 font-mono"&gt;{{ vm.project.branch }}&lt;/strong&gt;&lt;/div&gt;
        &lt;div&gt;Lead Architect: &lt;strong class="text-slate-800"&gt;{{ vm.project.author }}&lt;/strong&gt;&lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('resolveApp', [])
    // Simulated Route Configuration with Resolve:
    // .when('/project/:id', {
    //     templateUrl: 'project.html',
    //     controller: 'ProjectCtrl as vm',
    //     resolve: {
    //         projectData: function(ProjectService, $route) {
    //             return ProjectService.fetchById($route.current.params.id);
    //         }
    //     }
    // })
    .controller('ProjectCtrl', function() {
        var vm = this;
        // In actual ngRoute, projectData is injected directly as a parameter:
        // function(projectData) { vm.project = projectData; }
        vm.project = {
            title: 'Nectra Quantum Core Engine',
            repo: 'github.com/nectradigital/quantum-core',
            branch: 'main',
            author: 'Instructor NectraDigital'
        };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 38, 'Routing & Single Page Applications (SPA)', 'Route Resolvers (resolve): Data Pre-Fetching & Route Authentication Guards', $content38, 'angularjs resolve, route resolve in angular, pre-fetching data angularjs, prevent view flicker, $route resolve');

// -------------------------------------------------------------
// Chapter 39
// -------------------------------------------------------------
$content39 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">For enterprise applications with complex layouts (e.g. sidebars, nested dashboards, and multiple simultaneous views), the community standard is <strong>UI-Router (ui.router)</strong>. Instead of basic URL routing, UI-Router is a <strong>finite state machine</strong> supporting nested states, abstract parent layouts, and multiple named <code>ui-view</code> slots.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">ngRoute vs. UI-Router Enterprise Comparison</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Feature</th>
                <th style="width: 35%;">Official <code>ngRoute</code></th>
                <th style="width: 40%;">Enterprise <code>UI-Router</code></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Routing Paradigm</td>
                <td>URL-based routing (1 URL = 1 Template).</td>
                <td><strong>State-based machine:</strong> States represent application states independently of URLs.</td>
            </tr>
            <tr>
                <td class="code-cell">Nested Sub-Views</td>
                <td>Not supported natively; requires awkward <code>ng-include</code>.</td>
                <td><strong>First-class Nested States:</strong> (e.g. <code>app.dashboard.reports</code>). Child views render inside parent's <code>ui-view</code>.</td>
            </tr>
            <tr>
                <td class="code-cell">Multiple Simultaneous Views</td>
                <td>Only 1 single <code>ng-view</code> per page.</td>
                <td>Multiple named views: <code>views: { 'header@': {...}, 'content@': {...} }</code>.</td>
            </tr>
            <tr>
                <td class="code-cell">Abstract Layout States</td>
                <td>Not supported.</td>
                <td><code>abstract: true</code> for global layouts, authentication wrappers, and persistent sidebars.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Master Nested State Architecture</h3>
<p class="text-slate-600 mb-4">Observe how parent states wrap persistent navigation shells while child states populate sub-views:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: UI-Router Nested State Representation</div>
    <pre><code class="language-html">&lt;div ng-app="uiRouterApp" ng-controller="StateCtrl as vm" class="p-6 max-w-lg mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;div class="flex items-center justify-between border-b pb-3 mb-4"&gt;
        &lt;h3 class="text-lg font-bold text-slate-900"&gt;UI-Router State Machine&lt;/h3&gt;
        &lt;span class="text-xs font-mono font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded border border-emerald-200"&gt;
            Current State: {{ vm.activeState }}
        &lt;/span&gt;
    &lt;/div&gt;

    &lt;div class="flex gap-2 mb-4 text-xs font-bold"&gt;
        &lt;button ng-click="vm.transitionTo('admin.dashboard')" class="px-3 py-1.5 rounded border" ng-class="vm.activeState === 'admin.dashboard' ? 'bg-slate-900 text-white' : 'bg-slate-100'"&gt;
            admin.dashboard
        &lt;/button&gt;
        &lt;button ng-click="vm.transitionTo('admin.security')" class="px-3 py-1.5 rounded border" ng-class="vm.activeState === 'admin.security' ? 'bg-slate-900 text-white' : 'bg-slate-100'"&gt;
            admin.security
        &lt;/button&gt;
    &lt;/div&gt;

    &lt;!-- Simulated Nested ui-view --&gt;
    &lt;div class="p-4 bg-slate-50 border rounded-lg text-xs"&gt;
        &lt;span class="text-slate-400 font-bold block uppercase mb-1"&gt;[Nested Sub-State Content Target: &lt;ui-view&gt;]&lt;/span&gt;
        &lt;div class="font-bold text-slate-800 text-sm"&gt;{{ vm.statePayload.title }}&lt;/div&gt;
        &lt;p class="text-slate-600 mt-1"&gt;{{ vm.statePayload.description }}&lt;/p&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('uiRouterApp', [])
    .controller('StateCtrl', function() {
        var vm = this;
        vm.states = {
            'admin.dashboard': {
                title: 'Executive Dashboard State',
                description: 'Aggregated analytics and cluster health reports rendered inside parent admin layout.'
            },
            'admin.security': {
                title: 'Security Compliance Matrix',
                description: 'RBAC controls, audit logs, and SOC2 compliance monitoring view.'
            }
        };

        vm.activeState = 'admin.dashboard';
        vm.statePayload = vm.states[vm.activeState];

        vm.transitionTo = function(stateName) {
            vm.activeState = stateName;
            vm.statePayload = vm.states[stateName];
        };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 39, 'Routing & Single Page Applications (SPA)', 'Enterprise Routing with UI-Router (ui.router): State Machine, Nested Views & Abstract States', $content39, 'angularjs ui-router, ui-view, nested states in angularjs, abstract state ui-router, state machine routing, angular routing vs ui-router');

// -------------------------------------------------------------
// Chapter 40
// -------------------------------------------------------------
$content40 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Introduced in AngularJS 1.5, the <strong>.component()</strong> helper provides an opinionated, modern shortcut to writing directives. It enforces modern component-based architecture with <strong>One-Way Data Flow</strong> (<code>bindings: { item: '<' }</code>), automatic Controller As syntax (<code>$ctrl</code>), and prepares your application directly for Angular (v2+).</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">directive() vs. component() in AngularJS 1.5+</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Feature</th>
                <th style="width: 35%;">Traditional <code>.directive()</code></th>
                <th style="width: 40%;">Modern <code>.component()</code></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Declaration Syntax</td>
                <td>Factory returning a DDO function.</td>
                <td>Object literal configuration (No function wrapper).</td>
            </tr>
            <tr>
                <td class="code-cell">Controller Aliasing</td>
                <td>Optional (defaults to raw <code>$scope</code>).</td>
                <td><strong>Always automatic:</strong> defaults to <code>$ctrl</code>.</td>
            </tr>
            <tr>
                <td class="code-cell">Scope Standard</td>
                <td>Configurable (inherited, isolated, or raw).</td>
                <td><strong>Always isolated</strong> via <code>bindings: {}</code>.</td>
            </tr>
            <tr>
                <td class="code-cell">One-Way Binding</td>
                <td>Manual <code>=</code> two-way binding.</td>
                <td><strong>One-Way Binding via <code><</code>:</strong> Parent updates flow down, but child cannot accidentally mutate parent!</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Modern One-Way Component Architecture</h3>
<p class="text-slate-600 mb-4">Build clean, modern components with <code><</code> one-way binding and <code>&</code> output callbacks:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: AngularJS 1.5+ .component() with One-Way Data Flow</div>
    <pre><code class="language-html">&lt;div ng-app="compApp" ng-controller="ParentAppCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-1"&gt;Parent Application View&lt;/h3&gt;
    &lt;p class="text-xs text-slate-500 mb-4"&gt;Component-Based Architecture Demo&lt;/p&gt;

    &lt;!-- Using modern component element with one-way input (<) and event output (&) --&gt;
    &lt;task-item 
        task="vm.currentTask" 
        on-complete="vm.handleTaskDone(id)"&gt;
    &lt;/task-item&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('compApp', [])
    // 1. Modern Component Definition
    .component('taskItem', {
        bindings: {
            task: '<',        // One-way data input (<)
            onComplete: '&'   // Output event callback (&)
        },
        template: 
            '&lt;div class="p-4 bg-slate-50 border rounded-lg text-sm"&gt;' +
                '&lt;div class="flex justify-between items-center mb-2"&gt;' +
                    '&lt;strong class="text-slate-800"&gt;{{ $ctrl.task.title }}&lt;/strong&gt;' +
                    '&lt;span class="text-[10px] font-bold px-2 py-0.5 rounded" ng-class="$ctrl.task.completed ? \'bg-emerald-100 text-emerald-800\' : \'bg-amber-100 text-amber-800\'"&gt;' +
                        '{{ $ctrl.task.completed ? "Done ✓" : "Pending" }}' +
                    '&lt;/span&gt;' +
                '&lt;/div&gt;' +
                '&lt;button ng-click="$ctrl.onComplete({ id: $ctrl.task.id })" ng-disabled="$ctrl.task.completed" class="px-3 py-1 bg-emerald-600 disabled:bg-slate-300 text-white rounded text-xs font-bold shadow"&gt;' +
                    'Mark Completed' +
                '&lt;/button&gt;' +
            '&lt;/div&gt;',
        controller: function() {
            // $ctrl is automatically bound to this!
        }
    })
    .controller('ParentAppCtrl', function() {
        var vm = this;
        vm.currentTask = { id: 42, title: 'Upgrade Core Dependencies', completed: false };
        vm.handleTaskDone = function(id) {
            vm.currentTask.completed = true;
            alert('Parent state updated: Task #' + id + ' finished!');
        };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 40, 'Architecture, Components & Animations', 'Component-Based Architecture in AngularJS 1.5+: .component() Method & Modern One-Way Data Flow (<, @, &)', $content40, 'angularjs component, angular.component, one way data binding <, angular 1.5 component, migration to angular');

// -------------------------------------------------------------
// Chapter 41
// -------------------------------------------------------------
$content41 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Along with <code>.component()</code>, AngularJS 1.5+ introduced explicit <strong>Component Lifecycle Hooks</strong>: <code>$onInit</code>, <code>$onChanges</code>, <code>$onDestroy</code>, and <code>$postLink</code>. These lifecycle methods mirror modern Angular, React, and Vue component lifecycles.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">AngularJS Component Lifecycle Hooks</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Lifecycle Hook</th>
                <th style="width: 35%;">Trigger Moment</th>
                <th style="width: 40%;">Primary Purpose</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>$onInit()</code></td>
                <td>Called after all bindings are initialized and controller instantiated.</td>
                <td><strong>Safe initialization:</strong> Ideal place to execute setup logic and initial API queries.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$onChanges(changesObj)</code></td>
                <td>Called whenever one-way (<code><</code>) bound inputs are updated by parent.</td>
                <td>Reacting to parent property changes. Receives <code>currentValue</code> and <code>previousValue</code>.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$postLink()</code></td>
                <td>Called after the component's DOM and its children have completed linking.</td>
                <td>Executing raw DOM manipulation or attaching 3rd-party jQuery plugins.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$onDestroy()</code></td>
                <td>Called when component's scope is being destroyed and element removed.</td>
                <td>Cancelling active intervals, destroying listeners, preventing memory leaks.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Lifecycle Hooks in Practice</h3>
<p class="text-slate-600 mb-4">Inspect how <code>$onInit</code> and <code>$onChanges</code> react to incoming property updates:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Component Lifecycle Hooks ($onInit, $onChanges)</div>
    <pre><code class="language-html">&lt;div ng-app="lifecycleHookApp" ng-controller="ParentScoreCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Component Lifecycle Hooks&lt;/h3&gt;

    &lt;div class="flex gap-2 mb-4"&gt;
        &lt;button ng-click="vm.incrementScore()" class="px-3 py-1.5 bg-emerald-600 text-white rounded text-xs font-bold shadow"&gt;
            Increment Score ({{ vm.parentScore }})
        &lt;/button&gt;
    &lt;/div&gt;

    &lt;score-board score="vm.parentScore"&gt;&lt;/score-board&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('lifecycleHookApp', [])
    .component('scoreBoard', {
        bindings: { score: '<' },
        template: 
            '&lt;div class="p-4 bg-slate-50 border rounded-lg text-xs"&gt;' +
                '&lt;div class="font-bold text-slate-800 text-sm mb-1"&gt;Score Display Component&lt;/div&gt;' +
                '&lt;div&gt;Current Score: &lt;strong class="text-emerald-700"&gt;{{ $ctrl.score }}&lt;/strong&gt;&lt;/div&gt;' +
                '&lt;div class="text-slate-500 mt-1"&gt;Last Change Audit: {{ $ctrl.lastChangeAudit }}&lt;/div&gt;' +
            '&lt;/div&gt;',
        controller: function() {
            var ctrl = this;

            ctrl.$onInit = function() {
                ctrl.lastChangeAudit = 'Component initialized via $onInit';
                console.log('Component initialized.');
            };

            ctrl.$onChanges = function(changes) {
                if (changes.score &amp;&amp; !changes.score.isFirstChange()) {
                    ctrl.lastChangeAudit = 'Score updated from ' + changes.score.previousValue + ' to ' + changes.score.currentValue;
                    console.log(ctrl.lastChangeAudit);
                }
            };
        }
    })
    .controller('ParentScoreCtrl', function() {
        var vm = this;
        vm.parentScore = 100;
        vm.incrementScore = function() {
            vm.parentScore += 25;
        };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 41, 'Architecture, Components & Animations', 'Component Lifecycle Hooks: $onInit, $onChanges, $onDestroy, $postLink', $content41, 'angularjs lifecycle hooks, $oninit in angularjs, $onchanges, $ondestroy in component, angular component hooks');

// -------------------------------------------------------------
// Chapter 42
// -------------------------------------------------------------
$content42 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <strong>ngAnimate</strong> module brings smooth animations to built-in directives like <code>ng-repeat</code>, <code>ng-if</code>, <code>ng-show</code>, and <code>ng-hide</code>. When ngAnimate is included, AngularJS automatically injects transitional CSS classes (<code>.ng-enter</code>, <code>.ng-leave</code>, <code>.ng-enter-active</code>) during DOM additions and removals.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">ngAnimate CSS Transition Class Sequence</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Animation Event</th>
                <th style="width: 35%;">Starting State Class</th>
                <th style="width: 40%;">Ending Active Transition Class</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Item Enters (<code>ng-enter</code>)</td>
                <td><code>.my-class.ng-enter</code> (e.g. <code>opacity: 0</code>)</td>
                <td><code>.my-class.ng-enter.ng-enter-active</code> (e.g. <code>opacity: 1</code> with transition duration).</td>
            </tr>
            <tr>
                <td class="code-cell">Item Leaves (<code>ng-leave</code>)</td>
                <td><code>.my-class.ng-leave</code> (e.g. <code>opacity: 1</code>)</td>
                <td><code>.my-class.ng-leave.ng-leave-active</code> (e.g. <code>opacity: 0</code> with transition duration).</td>
            </tr>
            <tr>
                <td class="code-cell">Show / Hide</td>
                <td><code>.my-class.ng-hide-remove</code></td>
                <td><code>.my-class.ng-hide-remove.ng-hide-remove-active</code></td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Smooth CSS3 Transitions with ng-repeat</h3>
<p class="text-slate-600 mb-4">Observe how items fade and slide smoothly into and out of the list:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Smooth List Item Animations via ngAnimate</div>
    <pre><code class="language-html">&lt;div ng-app="animApp" ng-controller="AnimCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Animated Notification Feed&lt;/h3&gt;

    &lt;button ng-click="vm.addNotification()" class="px-3 py-1.5 bg-emerald-600 text-white rounded text-xs font-bold mb-4 shadow"&gt;
        + Trigger Alert
    &lt;/button&gt;

    &lt;style&gt;
        /* ngAnimate CSS Transitions */
        .anim-item.ng-enter {
            transition: all 0.4s ease-out;
            opacity: 0;
            transform: translateY(-10px);
        }
        .anim-item.ng-enter.ng-enter-active {
            opacity: 1;
            transform: translateY(0);
        }
        .anim-item.ng-leave {
            transition: all 0.3s ease-in;
            opacity: 1;
            transform: scale(1);
        }
        .anim-item.ng-leave.ng-leave-active {
            opacity: 0;
            transform: scale(0.9);
        }
    &lt;/style&gt;

    &lt;div class="space-y-2"&gt;
        &lt;div ng-repeat="item in vm.items track by item.id" class="anim-item p-3 bg-slate-50 border rounded-lg flex justify-between items-center text-xs"&gt;
            &lt;span class="font-bold text-slate-800"&gt;{{ item.text }}&lt;/span&gt;
            &lt;button ng-click="vm.dismiss(item.id)" class="text-red-500 font-bold hover:text-red-700"&gt;Dismiss &times;&lt;/button&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
// Requires angular-animate.min.js in production builds
angular.module('animApp', ['ngAnimate'])
    .controller('AnimCtrl', function() {
        var vm = this;
        vm.counter = 1;
        vm.items = [{ id: 1, text: 'Cluster deployment online.' }];

        vm.addNotification = function() {
            vm.counter++;
            vm.items.unshift({ id: Date.now(), text: 'New alert #' + vm.counter + ' received.' });
        };

        vm.dismiss = function(id) {
            vm.items = vm.items.filter(function(i) { return i.id !== id; });
        };
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 42, 'Architecture, Components & Animations', 'Animations with ngAnimate: CSS3 Transitions, Keyframes & JavaScript Animation Hooks', $content42, 'angularjs nganimate, angular animations, css transitions in angular, ng-enter ng-leave, animate ng-repeat');

// -------------------------------------------------------------
// Chapter 43
// -------------------------------------------------------------
$content43 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">AngularJS provides dependency-injectable wrappers for standard browser globals: <strong>$timeout</strong> (for <code>setTimeout</code>), <strong>$interval</strong> (for <code>setInterval</code>), <strong>$window</strong>, <strong>$document</strong>, and <strong>$location</strong>. Using these wrappers ensures that code is testable with mocks and automatically triggers the Digest Cycle.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Core Utility Wrappers vs. Browser Globals</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">AngularJS Service</th>
                <th style="width: 35%;">Native Global Equivalent</th>
                <th style="width: 40%;">Why You Must Use the Angular Service</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>$timeout(fn, delay)</code></td>
                <td><code>window.setTimeout</code></td>
                <td><strong>Automatic Digest:</strong> Automatically calls <code>$apply()</code> when the timer fires; return promise can be cancelled via <code>$timeout.cancel()</code>.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$interval(fn, delay)</code></td>
                <td><code>window.setInterval</code></td>
                <td>Automatically runs in Angular execution context and cancels safely.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$location</code></td>
                <td><code>window.location</code></td>
                <td>Synchronizes with browser address bar, supports HTML5 mode, and updates without full page reloads.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$window / $document</code></td>
                <td><code>window / document</code></td>
                <td>Allows unit tests to mock the browser window and DOM elements in headless test runners.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Safe Timer &amp; Location Inspector</h3>
<p class="text-slate-600 mb-4">Inspect how <code>$timeout</code> and <code>$location</code> work together smoothly without manual DOM glue:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Core Utility Services in Action</div>
    <pre><code class="language-html">&lt;div ng-app="utilApp" ng-controller="UtilCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-3"&gt;Core Utility Services Hub&lt;/h3&gt;

    &lt;div class="space-y-3 text-xs"&gt;
        &lt;div class="p-3 bg-slate-50 border rounded-lg"&gt;
            &lt;span class="font-bold text-slate-500 block uppercase"&gt;$location Service:&lt;/span&gt;
            &lt;span class="text-slate-800 font-mono"&gt;Host: {{ vm.host }} | Protocol: {{ vm.protocol }}&lt;/span&gt;
        &lt;/div&gt;

        &lt;div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-950"&gt;
            &lt;span class="font-bold text-emerald-700 block uppercase"&gt;$timeout Service Status:&lt;/span&gt;
            &lt;span&gt;{{ vm.countdownMessage }}&lt;/span&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('utilApp', [])
    .controller('UtilCtrl', ['$location', '$timeout', function($location, $timeout) {
        var vm = this;
        vm.host = $location.host() || 'localhost';
        vm.protocol = $location.protocol() || 'http';
        vm.countdownMessage = 'Waiting 2 seconds to fire $timeout...';

        $timeout(function() {
            vm.countdownMessage = '✓ $timeout executed and triggered digest automatically!';
        }, 2000);
    }]);
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 43, 'Architecture, Components & Animations', 'Core Utility Services: $timeout, $interval, $location, $window, $document & Safe Digest Management', $content43, 'angularjs $timeout, $interval in angular, $location service, $window and $document, angular utility services, safe digest');

// -------------------------------------------------------------
// Chapter 44
// -------------------------------------------------------------
$content44 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">In large-scale AngularJS applications, performance bottlenecks typically stem from an excessive number of watchers (the <strong>"2,000 Watcher Rule"</strong>). High-performance AngularJS development relies on <strong>One-Time Binding (<code>::</code>)</strong>, <strong>track by</strong> in <code>ng-repeat</code>, and <strong>$watchCollection</strong>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Top 4 Performance Optimizations in AngularJS</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Technique</th>
                <th style="width: 35%;">Syntax Example</th>
                <th style="width: 40%;">Performance Impact</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">One-Time Binding (<code>::</code>)</td>
                <td><code>{{ ::item.name }}</code> or <code>ng-if="::isReady"</code></td>
                <td><strong>Massive RAM &amp; CPU Savings:</strong> Deletes watcher from memory as soon as value resolves! Ideal for static lists.</td>
            </tr>
            <tr>
                <td class="code-cell">Track By</td>
                <td><code>ng-repeat="p in list track by p.id"</code></td>
                <td>Prevents AngularJS from tearing down and rebuilding identical DOM elements upon array refresh.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$watchCollection</code></td>
                <td><code>$scope.$watchCollection('arr', fn)</code></td>
                <td>Shallow watches arrays and objects without expensive deep-tree traversal (<code>$watch(str, fn, true)</code>).</td>
            </tr>
            <tr>
                <td class="code-cell">Disable Debug Info</td>
                <td><code>$compileProvider.debugInfoEnabled(false)</code></td>
                <td>Removes internal DOM classes like <code>ng-scope</code>, speeding up compilation by up to 30% in production.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. High-Density List Rendering with One-Time Binding</h3>
<p class="text-slate-600 mb-4">Notice how one-time binding stabilizes watchers even across thousands of rows:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: One-Time Binding (::) and Production Compile Config</div>
    <pre><code class="language-html">&lt;div ng-app="perfApp" ng-controller="PerfCtrl as vm" class="p-6 max-w-md mx-auto bg-white rounded-xl shadow border border-slate-200"&gt;
    &lt;h3 class="text-lg font-bold text-slate-900 mb-2"&gt;High-Performance Data Table&lt;/h3&gt;
    &lt;p class="text-xs text-slate-500 mb-4"&gt;Using One-Time Binding (&lt;code&gt;::&lt;/code&gt;) to Eliminate Watchers&lt;/p&gt;

    &lt;div class="border rounded-lg overflow-hidden text-xs"&gt;
        &lt;div ng-repeat="row in vm.records track by row.id" class="p-2.5 border-b last:border-b-0 flex justify-between bg-white"&gt;
            &lt;span class="font-bold text-slate-800"&gt;{{ ::row.title }}&lt;/span&gt;
            &lt;span class="text-slate-500 font-mono"&gt;{{ ::row.code }}&lt;/span&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
angular.module('perfApp', [])
    // Production Optimization: Disable Debug Classes
    .config(['$compileProvider', function($compileProvider) {
        $compileProvider.debugInfoEnabled(false);
        console.log('Production mode enabled: debugInfo disabled for max speed.');
    }])
    .controller('PerfCtrl', function() {
        var vm = this;
        vm.records = [
            { id: 1, title: 'Server Alpha Node 01', code: 'SRV-001' },
            { id: 2, title: 'Database Primary Replica', code: 'DB-002' },
            { id: 3, title: 'Redis Distributed Cache', code: 'CACHE-003' }
        ];
    });
&lt;/script&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 44, 'Performance, Security & Testing', 'Performance Optimization: Digest Cycle Profiling, One-Time Binding (::), track by & $watchCollection', $content44, 'angularjs performance, one time binding in angular, track by performance, reduce watchers in angular, debuginfoenabled false, angular speed optimization');

// -------------------------------------------------------------
// Chapter 45
// -------------------------------------------------------------
$content45 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Securing AngularJS applications requires understanding the framework's sandboxing history and security architecture. Modern production standards require strict prevention of template injection (SSTI), avoiding user-controlled <code>$eval</code> executions, and complying with modern <strong>Content Security Policy (CSP)</strong>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Security Checklist for Enterprise AngularJS</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Vulnerability Vector</th>
                <th style="width: 35%;">Dangerous Code Pattern</th>
                <th style="width: 40%;">Hardened Remediation Standard</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Client Template Injection</td>
                <td>Rendering raw server strings directly into AngularJS HTML templates.</td>
                <td><strong>Never generate Angular templates dynamically on the server with user input!</strong> Keep templates static.</td>
            </tr>
            <tr>
                <td class="code-cell">Dynamic Code Eval</td>
                <td><code>$scope.$eval(userInput)</code> or <code>$parse(userInput)</code></td>
                <td>Never pass untrusted user strings into <code>$eval</code> or <code>$parse</code>.</td>
            </tr>
            <tr>
                <td class="code-cell">CSP Compliance</td>
                <td>Inline expressions requiring <code>eval()</code></td>
                <td>Include <code>ng-csp</code> on <code>&lt;html&gt;</code> tag to restrict code generation and adhere to strict server CSP headers.</td>
            </tr>
            <tr>
                <td class="code-cell">JSON Vulnerability Shield</td>
                <td>JSON hijacking of array responses</td>
                <td>Server prefixes JSON with <code>")]}',\n"</code>; AngularJS <code>$http</code> automatically strips this prefix!</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. ng-csp Hardened Application Bootstrap</h3>
<p class="text-slate-600 mb-4">Enforce strict Content Security Policy mode in AngularJS:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: ng-csp Hardened Application Structure</div>
    <pre><code class="language-html">&lt;!DOCTYPE html&gt;
&lt;html lang="en" ng-app="secureApp" ng-csp&gt;
&lt;head&gt;
    &lt;meta charset="UTF-8"&gt;
    &lt;meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline';"&gt;
    &lt;title&gt;Hardened AngularJS Application&lt;/title&gt;
    &lt;script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.8.3/angular.min.js"&gt;&lt;/script&gt;
&lt;/head&gt;
&lt;body ng-controller="SecurityCtrl as vm" class="p-6 bg-slate-50 font-sans"&gt;
    &lt;div class="max-w-md mx-auto bg-white p-6 rounded-xl shadow border border-slate-200"&gt;
        &lt;h3 class="text-lg font-bold text-slate-900 mb-2"&gt;Security &amp; CSP Compliance&lt;/h3&gt;
        &lt;p class="text-xs text-slate-600 mb-4"&gt;Running in strict &lt;code&gt;ng-csp&lt;/code&gt; mode. Dynamic code evaluations are strictly prohibited.&lt;/p&gt;
        
        &lt;div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-bold text-emerald-800"&gt;
            ✓ Content Security Policy: Verified Clean
        &lt;/div&gt;
    &lt;/div&gt;

    &lt;script&gt;
    angular.module('secureApp', [])
        .controller('SecurityCtrl', function() {
            var vm = this;
            vm.status = 'Secure';
        });
    &lt;/script&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 45, 'Performance, Security & Testing', 'AngularJS Security Audit: Preventing XSS, Avoiding $eval Exploits & Content Security Policy (CSP)', $content45, 'angularjs security, ng-csp in angularjs, xss in angularjs, client side template injection, secure angularjs application, angular eval safety');

// -------------------------------------------------------------
// Chapter 46
// -------------------------------------------------------------
$content46 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">AngularJS was designed from day one with testability as a core priority. The official <strong>angular-mocks</strong> module (<code>ngMock</code>) provides testing utilities like <strong>$httpBackend</strong> (for mocking remote HTTP responses), <strong>$controller</strong>, and <strong>$compile</strong> for headless unit testing with Jasmine and Karma.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Testing Tools &amp; Mock Services (ngMock)</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Mock Utility</th>
                <th style="width: 35%;">Syntax Example</th>
                <th style="width: 40%;">Testing Role</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>module('myApp')</code></td>
                <td><code>beforeEach(module('myApp'));</code></td>
                <td>Initializes the module under test before each Jasmine <code>it()</code> spec.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>inject(fn)</code></td>
                <td><code>inject(function($controller, $rootScope) { ... })</code></td>
                <td>Injects services, controllers, and mocks into test functions.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$httpBackend</code></td>
                <td><code>$httpBackend.expectGET('/api').respond(200, [...]);</code></td>
                <td>Intercepts all HTTP queries in test suites, returning deterministic fixtures.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>$compile</code></td>
                <td><code>$compile('&lt;my-dir&gt;&lt;/my-dir&gt;')($rootScope);</code></td>
                <td>Compiles directives and evaluates DOM bindings in memory.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Complete Jasmine Unit Test Suite</h3>
<p class="text-slate-600 mb-4">Inspect a production Jasmine spec testing an AngularJS controller with mocked HTTP:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Jasmine & ngMock Unit Test Suite</div>
    <pre><code class="language-javascript">describe('AuthService & LoginCtrl Test Suite', function() {
    // 1. Load application module
    beforeEach(module('enterpriseApp'));

    var $controller, $httpBackend, $rootScope;

    // 2. Inject testing mocks
    beforeEach(inject(function(_$controller_, _$httpBackend_, _$rootScope_) {
        $controller = _$controller_;
        $httpBackend = _$httpBackend_;
        $rootScope = _$rootScope_;
    }));

    afterEach(function() {
        $httpBackend.verifyNoOutstandingExpectation();
        $httpBackend.verifyNoOutstandingRequest();
    });

    it('should successfully authenticate user and set auth token', function() {
        // Arrange mock API expectation
        $httpBackend.expectPOST('/api/login', { username: 'alex', pass: 'secret' })
            .respond(200, { token: 'jwt_valid_token_xyz' });

        var scope = $rootScope.$new();
        var ctrl = $controller('LoginCtrl as vm', { $scope: scope });

        // Act
        ctrl.login('alex', 'secret');
        $httpBackend.flush(); // Triggers resolved HTTP response

        // Assert
        expect(ctrl.isAuthenticated).toBe(true);
        expect(ctrl.userToken).toBe('jwt_valid_token_xyz');
    });
});</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 46, 'Performance, Security & Testing', 'Unit Testing AngularJS Applications with Karma, Jasmine & angular-mocks ($httpBackend, $controller, $compile)', $content46, 'angularjs unit testing, jasmine karma angularjs, angular-mocks, $httpbackend mock, testing angular controllers, unit test directives');

// -------------------------------------------------------------
// Chapter 47
// -------------------------------------------------------------
$content47 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Migrating large enterprise codebases from AngularJS (1.x) to modern Angular (v17/v18) cannot be done overnight. The official strategy is the <strong>Hybrid Dual-Booting Architecture</strong> powered by <strong>@angular/upgrade</strong> (<code>ngUpgrade</code>), enabling both frameworks to run side-by-side in the same DOM tree.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Step-by-Step Enterprise Migration Blueprint</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Migration Phase</th>
                <th style="width: 35%;">Action Items</th>
                <th style="width: 40%;">Tooling / Strategy</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Phase 1: Code Modernization</td>
                <td>Refactor <code>$scope</code> controllers to <code>.component()</code> with <code><</code> one-way bindings and <code>$onInit</code>.</td>
                <td>TypeScript compiler (<code>tsc</code>) &amp; Webpack bundling.</td>
            </tr>
            <tr>
                <td class="code-cell">Phase 2: Hybrid Dual-Boot</td>
                <td>Bootstrap both AngularJS and Modern Angular simultaneously using <code>UpgradeModule</code>.</td>
                <td><code>@angular/upgrade/static</code></td>
            </tr>
            <tr>
                <td class="code-cell">Phase 3: Service Downgrading</td>
                <td>Write modern Angular services and "downgrade" them so legacy AngularJS controllers can inject them.</td>
                <td><code>downgradeInjectable()</code></td>
            </tr>
            <tr>
                <td class="code-cell">Phase 4: Component Upgrading</td>
                <td>"Upgrade" legacy AngularJS directives to run inside modern Angular templates.</td>
                <td><code>UpgradeComponent</code></td>
            </tr>
            <tr>
                <td class="code-cell">Phase 5: Full Cutover</td>
                <td>Decommission AngularJS runtime once the last legacy component is converted.</td>
                <td>Pure standalone Angular (v17/v18).</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-900 mt-8 mb-4">1. Dual-Booting Architecture with UpgradeModule</h3>
<p class="text-slate-600 mb-4">Inspect how a hybrid modern application bootstraps both engines side-by-side:</p>

<div class="w3-example-box">
    <div class="w3-example-title">Example: Modern Hybrid Bootstrapping Blueprint (TypeScript / Angular 17+)</div>
    <pre><code class="language-javascript">// main.ts: Bootstrapping both Modern Angular and AngularJS 1.8 simultaneously
import { platformBrowserDynamic } from '@angular/platform-browser-dynamic';
import { UpgradeModule } from '@angular/upgrade/static';
import { AppModule } from './app/app.module';

platformBrowserDynamic().bootstrapModule(AppModule).then(platformRef => {
    // 1. Retrieve the UpgradeModule instance
    const upgrade = platformRef.injector.get(UpgradeModule) as UpgradeModule;

    // 2. Bootstrap legacy AngularJS application onto the DOM
    upgrade.bootstrap(document.body, ['legacyAngularJsApp'], { strictDi: true });

    console.log('Hybrid Application Successfully Dual-Booted: Modern Angular + AngularJS!');
});</code></pre>
</div>
HTML;
insertChapter($pdo, $courseId, 47, 'Performance, Security & Testing', 'Upgrading AngularJS to Modern Angular (v17/v18): Dual-Booting Architecture with ngUpgrade', $content47, 'upgrade angularjs to angular, ngupgrade, upgradecomponent, downgradeinjectable, hybrid angular app, angular migration strategy');
