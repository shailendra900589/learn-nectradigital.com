<?php
// seed_flutter_part3.php: Chapters 24 to 35 (3+ examples each)

$content24 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Flutter's imperative Navigation 1.0 operates on an in-memory route stack using <code>Navigator.push</code>, <code>Navigator.pop</code>, and <code>MaterialPageRoute</code>. Screens can pass parameters forward and return result objects backwards on dismissal.</p>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: Basic Screen Push and Pop Navigation</h3>
<p class="text-slate-600 mb-3">Navigating from a home screen to a detail screen and returning with Navigator.pop.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: Basic Screen Push and Pop Navigation</span>
        <span class="text-xs text-slate-400 font-mono">lib/nav_push_pop.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

void main() {
  runApp(const MaterialApp(home: HomeScreen()));
}

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;Navigation 1.0 Home&#x27;)),
      body: Center(
        child: ElevatedButton(
          onPressed: () {
            Navigator.push(
              context,
              MaterialPageRoute(builder: (context) =&gt; const DetailScreen()),
            );
          },
          child: const Text(&#x27;Go to Detail Screen&#x27;),
        ),
      ),
    );
  }
}

class DetailScreen extends StatelessWidget {
  const DetailScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;Detail Screen&#x27;)),
      body: Center(
        child: ElevatedButton(
          onPressed: () =&gt; Navigator.pop(context),
          child: const Text(&#x27;Back to Home&#x27;),
        ),
      ),
    );
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: Returning Data from Child Screen to Parent</h3>
<p class="text-slate-600 mb-3">Awaiting a result object from a secondary selection screen when popping the route.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: Returning Data from Child Screen to Parent</span>
        <span class="text-xs text-slate-400 font-mono">lib/nav_return_data.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

void main() {
  runApp(const MaterialApp(home: SelectionParentScreen()));
}

class SelectionParentScreen extends StatefulWidget {
  const SelectionParentScreen({super.key});

  @override
  State&lt;SelectionParentScreen&gt; createState() =&gt; _SelectionParentScreenState();
}

class _SelectionParentScreenState extends State&lt;SelectionParentScreen&gt; {
  String _selectedOption = &#x27;None Selected&#x27;;

  Future&lt;void&gt; _pickOption() async {
    final result = await Navigator.push&lt;String&gt;(
      context,
      MaterialPageRoute(builder: (ctx) =&gt; const OptionsPickerScreen()),
    );
    if (result != null) {
      setState(() =&gt; _selectedOption = result);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;Return Data Navigator&#x27;)),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(&#x27;Current Choice: $_selectedOption&#x27;, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            const SizedBox(height: 16),
            ElevatedButton(onPressed: _pickOption, child: const Text(&#x27;Select Subscription Tier&#x27;)),
          ],
        ),
      ),
    );
  }
}

class OptionsPickerScreen extends StatelessWidget {
  const OptionsPickerScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;Select Tier&#x27;)),
      body: ListView(
        children: [
          ListTile(title: const Text(&#x27;Pro Plan ($29/mo)&#x27;), onTap: () =&gt; Navigator.pop(context, &#x27;Pro ($29/mo)&#x27;)),
          ListTile(title: const Text(&#x27;Enterprise Dedicated ($99/mo)&#x27;), onTap: () =&gt; Navigator.pop(context, &#x27;Enterprise ($99/mo)&#x27;)),
        ],
      ),
    );
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: Named Routes with onGenerateRoute & UnknownRoute Fallback</h3>
<p class="text-slate-600 mb-3">Configuring onGenerateRoute for centralized routing and 404 error page redirection.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: Named Routes with onGenerateRoute & UnknownRoute Fallback</span>
        <span class="text-xs text-slate-400 font-mono">lib/on_generate_route.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

void main() {
  runApp(MaterialApp(
    initialRoute: &#x27;/&#x27;,
    onGenerateRoute: (settings) {
      if (settings.name == &#x27;/&#x27;) {
        return MaterialPageRoute(builder: (_) =&gt; const CentralHome());
      } else if (settings.name == &#x27;/profile&#x27;) {
        final userId = settings.arguments as String? ?? &#x27;DEFAULT&#x27;;
        return MaterialPageRoute(builder: (_) =&gt; CentralProfile(userId: userId));
      }
      return MaterialPageRoute(builder: (_) =&gt; const RouteNotFoundScreen());
    },
  ));
}

class CentralHome extends StatelessWidget {
  const CentralHome({super.key});
  @override
  Widget build(BuildContext context) =&gt; Scaffold(
    appBar: AppBar(title: const Text(&#x27;Central Router&#x27;)),
    body: Center(
      child: ElevatedButton(
        onPressed: () =&gt; Navigator.pushNamed(context, &#x27;/profile&#x27;, arguments: &#x27;USR_8819&#x27;),
        child: const Text(&#x27;Open Profile with Args&#x27;),
      ),
    ),
  );
}

class CentralProfile extends StatelessWidget {
  final String userId;
  const CentralProfile({super.key, required this.userId});
  @override
  Widget build(BuildContext context) =&gt; Scaffold(
    appBar: AppBar(title: const Text(&#x27;Profile Screen&#x27;)),
    body: Center(child: Text(&#x27;Profile for ID: $userId&#x27;, style: const TextStyle(fontSize: 18))),
  );
}

class RouteNotFoundScreen extends StatelessWidget {
  const RouteNotFoundScreen({super.key});
  @override
  Widget build(BuildContext context) =&gt; const Scaffold(body: Center(child: Text(&#x27;404: Route Not Found&#x27;)));
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;

$content25 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>GoRouter</strong> is Flutter's official declarative routing package designed for web URLs, deep linking, nested shell routes, and authentication guards.</p>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: Declarative GoRouter Setup with Path Parameters</h3>
<p class="text-slate-600 mb-3">Declaring clean path parameters like /users/:id with GoRouter.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: Declarative GoRouter Setup with Path Parameters</span>
        <span class="text-xs text-slate-400 font-mono">lib/go_router_basics.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;
import &#x27;package:go_router/go_router.dart&#x27;;

final GoRouter _router = GoRouter(
  initialLocation: &#x27;/&#x27;,
  routes: [
    GoRoute(
      path: &#x27;/&#x27;,
      builder: (context, state) =&gt; const GoHomeScreen(),
    ),
    GoRoute(
      path: &#x27;/item/:id&#x27;,
      builder: (context, state) {
        final id = state.pathParameters[&#x27;id&#x27;] ?? &#x27;0&#x27;;
        return ItemDetailScreen(itemId: id);
      },
    ),
  ],
);

void main() =&gt; runApp(MaterialApp.router(routerConfig: _router));

class GoHomeScreen extends StatelessWidget {
  const GoHomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;GoRouter Home&#x27;)),
      body: Center(
        child: ElevatedButton(
          onPressed: () =&gt; context.go(&#x27;/item/1042&#x27;),
          child: const Text(&#x27;Go to /item/1042&#x27;),
        ),
      ),
    );
  }
}

class ItemDetailScreen extends StatelessWidget {
  final String itemId;
  const ItemDetailScreen({super.key, required this.itemId});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(&#x27;Item #$itemId&#x27;)),
      body: Center(
        child: ElevatedButton(
          onPressed: () =&gt; context.go(&#x27;/&#x27;),
          child: const Text(&#x27;Return to Home (context.go)&#x27;),
        ),
      ),
    );
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: Route Redirection & Authentication Guards</h3>
<p class="text-slate-600 mb-3">Protecting private screens and redirecting unauthenticated users to /login.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: Route Redirection & Authentication Guards</span>
        <span class="text-xs text-slate-400 font-mono">lib/go_router_redirect.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;
import &#x27;package:go_router/go_router.dart&#x27;;

bool isLoggedIn = false;

final GoRouter _authRouter = GoRouter(
  initialLocation: &#x27;/dashboard&#x27;,
  redirect: (context, state) {
    final goingToLogin = state.matchedLocation == &#x27;/login&#x27;;
    if (!isLoggedIn &amp;&amp; !goingToLogin) {
      return &#x27;/login&#x27;; // Redirect to login!
    }
    if (isLoggedIn &amp;&amp; goingToLogin) {
      return &#x27;/dashboard&#x27;; // Already logged in
    }
    return null; // Proceed as normal
  },
  routes: [
    GoRoute(path: &#x27;/login&#x27;, builder: (c, s) =&gt; const Scaffold(body: Center(child: Text(&#x27;Login Screen&#x27;)))),
    GoRoute(path: &#x27;/dashboard&#x27;, builder: (c, s) =&gt; const Scaffold(body: Center(child: Text(&#x27;Protected Dashboard&#x27;)))),
  ],
);

void main() =&gt; runApp(MaterialApp.router(routerConfig: _authRouter));</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: Nested ShellRoute for Persistent Bottom Navigation Bar</h3>
<p class="text-slate-600 mb-3">Keeping the bottom navigation bar permanently mounted while subroutes switch inside a nested Navigator.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: Nested ShellRoute for Persistent Bottom Navigation Bar</span>
        <span class="text-xs text-slate-400 font-mono">lib/shell_route_demo.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;
import &#x27;package:go_router/go_router.dart&#x27;;

final GlobalKey&lt;NavigatorState&gt; _shellNavigatorKey = GlobalKey&lt;NavigatorState&gt;();

final GoRouter _shellRouter = GoRouter(
  initialLocation: &#x27;/a&#x27;,
  routes: [
    ShellRoute(
      navigatorKey: _shellNavigatorKey,
      builder: (context, state, child) =&gt; ScaffoldWithNavBar(child: child),
      routes: [
        GoRoute(path: &#x27;/a&#x27;, builder: (context, state) =&gt; const Center(child: Text(&#x27;Screen A Content&#x27;))),
        GoRoute(path: &#x27;/b&#x27;, builder: (context, state) =&gt; const Center(child: Text(&#x27;Screen B Content&#x27;))),
      ],
    ),
  ],
);

void main() =&gt; runApp(MaterialApp.router(routerConfig: _shellRouter));

class ScaffoldWithNavBar extends StatelessWidget {
  final Widget child;
  const ScaffoldWithNavBar({super.key, required this.child});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: child,
      bottomNavigationBar: NavigationBar(
        destinations: const [
          NavigationDestination(icon: Icon(Icons.home), label: &#x27;Tab A&#x27;),
          NavigationDestination(icon: Icon(Icons.settings), label: &#x27;Tab B&#x27;),
        ],
        onDestinationSelected: (idx) {
          if (idx == 0) context.go(&#x27;/a&#x27;);
          if (idx == 1) context.go(&#x27;/b&#x27;);
        },
      ),
    );
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;

$content26 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">State management distinguishes between <strong>Ephemeral State</strong> (local to a single widget, like text fields) and <strong>App State</strong> (shared across multiple screens, like user sessions and shopping carts). <code>ValueNotifier</code> and <code>ValueListenableBuilder</code> provide high-efficiency targeted rebuilds.</p>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: ValueNotifier & ValueListenableBuilder</h3>
<p class="text-slate-600 mb-3">Rebuilding only the small text widget when counter changes without re-evaluating the parent scaffold.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: ValueNotifier & ValueListenableBuilder</span>
        <span class="text-xs text-slate-400 font-mono">lib/valuenotifier_demo.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

void main() {
  runApp(const MaterialApp(home: ValueNotifierDemo()));
}

class ValueNotifierDemo extends StatelessWidget {
  ValueNotifierDemo({super.key});

  final ValueNotifier&lt;int&gt; _pointsNotifier = ValueNotifier&lt;int&gt;(0);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;ValueNotifier Rebuild Scope&#x27;)),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Text(&#x27;Granularly Rebuilt Value:&#x27;),
            // Only this child builder rebuilds!
            ValueListenableBuilder&lt;int&gt;(
              valueListenable: _pointsNotifier,
              builder: (context, points, _) {
                return Text(
                  &#x27;$points pts&#x27;,
                  style: const TextStyle(fontSize: 40, fontWeight: FontWeight.bold, color: Colors.indigo),
                );
              },
            ),
          ],
        ),
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () =&gt; _pointsNotifier.value += 10,
        child: const Icon(Icons.stars),
      ),
    );
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: Complex State Object with ValueNotifier</h3>
<p class="text-slate-600 mb-3">Emitting immutable state transitions for shopping cart item counts and subtotal values.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: Complex State Object with ValueNotifier</span>
        <span class="text-xs text-slate-400 font-mono">lib/cart_valuenotifier.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

class CartState {
  final int totalCount;
  final double totalPrice;

  const CartState({required this.totalCount, required this.totalPrice});
}

class CartManager {
  final ValueNotifier&lt;CartState&gt; state = ValueNotifier(const CartState(totalCount: 0, totalPrice: 0.0));

  void addItem(double price) {
    state.value = CartState(
      totalCount: state.value.totalCount + 1,
      totalPrice: state.value.totalPrice + price,
    );
  }
}

void main() {
  final manager = CartManager();
  print(&#x27;Initial Cart: ${manager.state.value.totalCount} items, $${manager.state.value.totalPrice}&#x27;);

  manager.addItem(49.99);
  print(&#x27;After Item 1: ${manager.state.value.totalCount} items, $${manager.state.value.totalPrice}&#x27;);

  manager.addItem(19.50);
  print(&#x27;After Item 2: ${manager.state.value.totalCount} items, $${manager.state.value.totalPrice}&#x27;);
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: Ephemeral Form State vs. App-Wide Auth State</h3>
<p class="text-slate-600 mb-3">Contrasting temporary local switch state with persistent user authentication state.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: Ephemeral Form State vs. App-Wide Auth State</span>
        <span class="text-xs text-slate-400 font-mono">lib/ephemeral_vs_app_state.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

// App-Wide Shared State
class AuthSession {
  static String? activeToken = &#x27;TOKEN_XYZ_891&#x27;;
  static bool get isLoggedIn =&gt; activeToken != null;
}

void main() =&gt; runApp(const MaterialApp(home: StateContrastScreen()));

class StateContrastScreen extends StatefulWidget {
  const StateContrastScreen({super.key});

  @override
  State&lt;StateContrastScreen&gt; createState() =&gt; _StateContrastScreenState();
}

class _StateContrastScreenState extends State&lt;StateContrastScreen&gt; {
  // Ephemeral State (Local to this form)
  bool _rememberMe = true;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;Ephemeral vs App State&#x27;)),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            Text(&#x27;App State (Session): &#x27; + (AuthSession.isLoggedIn ? &#x27;Authenticated&#x27; : &#x27;Guest&#x27;)),
            const SizedBox(height: 16),
            SwitchListTile(
              title: const Text(&#x27;Remember My Credentials (Ephemeral)&#x27;),
              value: _rememberMe,
              onChanged: (val) =&gt; setState(() =&gt; _rememberMe = val),
            ),
          ],
        ),
      ),
    );
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;

$content27 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><code>InheritedWidget</code> is Flutter's low-level engine mechanism for propagating data down the widget tree with <strong>O(1)</strong> lookup efficiency, powering foundational classes like <code>Theme.of(context)</code> and <code>MediaQuery.of(context)</code>.</p>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: Custom InheritedWidget for Company Theme</h3>
<p class="text-slate-600 mb-3">Implementing a custom InheritedWidget with updateShouldNotify to propagate brand colors.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: Custom InheritedWidget for Company Theme</span>
        <span class="text-xs text-slate-400 font-mono">lib/custom_inherited_widget.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

class BrandColorScope extends InheritedWidget {
  final Color primaryBrandColor;

  const BrandColorScope({
    super.key,
    required this.primaryBrandColor,
    required super.child,
  });

  static BrandColorScope of(BuildContext context) {
    final result = context.dependOnInheritedWidgetOfExactType&lt;BrandColorScope&gt;();
    assert(result != null, &#x27;No BrandColorScope found in context&#x27;);
    return result!;
  }

  @override
  bool updateShouldNotify(BrandColorScope oldWidget) {
    return oldWidget.primaryBrandColor != primaryBrandColor;
  }
}

void main() {
  runApp(const BrandColorScope(
    primaryBrandColor: Colors.deepPurple,
    child: MaterialApp(home: InheritedDemoScreen()),
  ));
}

class InheritedDemoScreen extends StatelessWidget {
  const InheritedDemoScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final brandColor = BrandColorScope.of(context).primaryBrandColor;
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;InheritedWidget Scope&#x27;), backgroundColor: brandColor),
      body: Center(
        child: Text(&#x27;Propagated Brand Color&#x27;, style: TextStyle(color: brandColor, fontSize: 20, fontWeight: FontWeight.bold)),
      ),
    );
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: O(1) InheritedWidget Tree Lookup Mechanics</h3>
<p class="text-slate-600 mb-3">Demonstrating how dependOnInheritedWidgetOfExactType executes in constant time O(1) via the Element map.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: O(1) InheritedWidget Tree Lookup Mechanics</span>
        <span class="text-xs text-slate-400 font-mono">lib/o1_lookup_explanation.dart</span>
    </div>
    <pre><code class="language-flutter">void main() {
  // In Flutter&#x27;s Element class:
  // Map&lt;Type, InheritedElement&gt;? _inheritedElements;
  // Lookups do NOT walk up parent pointers one-by-one!
  // Instead: return _inheritedElements[T] -&gt; Instant O(1) Hash Table Lookup!

  print(&#x27;Flutter InheritedWidget Lookup Complexity: O(1) Hash Map&#x27;);
  print(&#x27;100 levels deep has the exact same performance as 1 level deep!&#x27;);
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: Selective updateShouldNotify Optimization</h3>
<p class="text-slate-600 mb-3">Preventing child rebuilds when unmonitored properties remain identical.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: Selective updateShouldNotify Optimization</span>
        <span class="text-xs text-slate-400 font-mono">lib/selective_notify.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

class UserSessionScope extends InheritedWidget {
  final String userId;
  final int sessionTimestamp;

  const UserSessionScope({
    super.key,
    required this.userId,
    required this.sessionTimestamp,
    required super.child,
  });

  @override
  bool updateShouldNotify(UserSessionScope oldWidget) {
    // Only notify dependents if the userId changed (ignore timestamp ticks!)
    return oldWidget.userId != userId;
  }
}

void main() {
  print(&#x27;updateShouldNotify optimization active: timestamp changes do not trigger consumer rebuilds.&#x27;);
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;

$content28 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>Provider</strong> (recommended by the Flutter team as an accessible, mature state management library) wraps InheritedWidget with <code>ChangeNotifier</code>, <code>MultiProvider</code>, and <code>Consumer</code>.</p>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: Cart Provider with ChangeNotifier & Consumer</h3>
<p class="text-slate-600 mb-3">Creating a centralized shopping cart provider and listening to changes with Consumer.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: Cart Provider with ChangeNotifier & Consumer</span>
        <span class="text-xs text-slate-400 font-mono">lib/provider_cart.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;
import &#x27;package:provider/provider.dart&#x27;;

class CartProvider extends ChangeNotifier {
  final List&lt;String&gt; _items = [];
  List&lt;String&gt; get items =&gt; List.unmodifiable(_items);

  void add(String item) {
    _items.add(item);
    notifyListeners(); // Triggers consumers to rebuild!
  }
}

void main() {
  runApp(
    ChangeNotifierProvider(
      create: (_) =&gt; CartProvider(),
      child: const MaterialApp(home: ProviderCartScreen()),
    ),
  );
}

class ProviderCartScreen extends StatelessWidget {
  const ProviderCartScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;Provider Cart&#x27;)),
      body: Consumer&lt;CartProvider&gt;(
        builder: (context, cart, _) {
          return Center(
            child: Text(&#x27;Items in Cart: ${cart.items.length}&#x27;, style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold)),
          );
        },
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () =&gt; context.read&lt;CartProvider&gt;().add(&#x27;Item #${DateTime.now().second}&#x27;),
        child: const Icon(Icons.add_shopping_cart),
      ),
    );
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: context.watch() vs. context.read() vs. context.select()</h3>
<p class="text-slate-600 mb-3">Optimizing rebuilds: read for callbacks, watch for full objects, select for specific properties.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: context.watch() vs. context.read() vs. context.select()</span>
        <span class="text-xs text-slate-400 font-mono">lib/provider_selectors.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;
import &#x27;package:provider/provider.dart&#x27;;

class UserProfileModel extends ChangeNotifier {
  String name = &#x27;Rohan&#x27;;
  int unreadCount = 4;

  void incrementUnread() {
    unreadCount++;
    notifyListeners();
  }
}

void main() {
  print(&#x27;context.read&lt;T&gt;(): Reads without subscribing (ideal for onPressed).&#x27;);
  print(&#x27;context.watch&lt;T&gt;(): Subscribes to any change in T.&#x27;);
  print(&#x27;context.select&lt;T, R&gt;(): Subscribes ONLY to specific sub-field R.&#x27;);
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: MultiProvider for Enterprise Global Dependencies</h3>
<p class="text-slate-600 mb-3">Configuring multiple global providers cleanly at the root of the application.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: MultiProvider for Enterprise Global Dependencies</span>
        <span class="text-xs text-slate-400 font-mono">lib/multiprovider_setup.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;
import &#x27;package:provider/provider.dart&#x27;;

class AuthState extends ChangeNotifier {}
class ThemeState extends ChangeNotifier {}
class CartState extends ChangeNotifier {}

void main() {
  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) =&gt; AuthState()),
        ChangeNotifierProvider(create: (_) =&gt; ThemeState()),
        ChangeNotifierProvider(create: (_) =&gt; CartState()),
      ],
      child: const MaterialApp(home: Scaffold(body: Center(child: Text(&#x27;MultiProvider Initialized&#x27;)))),
    ),
  );
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;

$content29 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>Riverpod</strong> is a complete rewrite of Provider that eliminates <code>BuildContext</code> dependency, catches provider errors at compile time, and supports auto-disposal and family caching.</p>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: StateProvider & ConsumerWidget</h3>
<p class="text-slate-600 mb-3">Basic Riverpod 2.x setup using StateProvider and reading state via WidgetRef in ConsumerWidget.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: StateProvider & ConsumerWidget</span>
        <span class="text-xs text-slate-400 font-mono">lib/riverpod_counter.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;
import &#x27;package:flutter_riverpod/flutter_riverpod.dart&#x27;;

// Declare global provider (compile-time safe, no BuildContext needed!)
final counterProvider = StateProvider&lt;int&gt;((ref) =&gt; 0);

void main() {
  runApp(const ProviderScope(child: MaterialApp(home: RiverpodCounterScreen())));
}

class RiverpodCounterScreen extends ConsumerWidget {
  const RiverpodCounterScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final count = ref.watch(counterProvider);

    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;Riverpod 2.x Counter&#x27;)),
      body: Center(
        child: Text(&#x27;Count: $count&#x27;, style: const TextStyle(fontSize: 32, fontWeight: FontWeight.bold)),
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () =&gt; ref.read(counterProvider.notifier).state++,
        child: const Icon(Icons.add),
      ),
    );
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: AsyncNotifierProvider for Clean Network State</h3>
<p class="text-slate-600 mb-3">Handling Loading, Error, and Data states automatically with Riverpod AsyncValue.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: AsyncNotifierProvider for Clean Network State</span>
        <span class="text-xs text-slate-400 font-mono">lib/riverpod_async_notifier.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;
import &#x27;package:flutter_riverpod/flutter_riverpod.dart&#x27;;

// AsyncNotifier manages asynchronous data lifecycle
class UserListNotifier extends AsyncNotifier&lt;List&lt;String&gt;&gt; {
  @override
  Future&lt;List&lt;String&gt;&gt; build() async {
    await Future.delayed(const Duration(milliseconds: 500));
    return [&#x27;Deepak Sharma&#x27;, &#x27;Priya Patel&#x27;, &#x27;Sunil Narine&#x27;];
  }
}

final userListProvider = AsyncNotifierProvider&lt;UserListNotifier, List&lt;String&gt;&gt;(UserListNotifier.new);

void main() {
  print(&#x27;Riverpod AsyncValue automatically maps: .when(data: ..., loading: ..., error: ...)&#x27;);
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: AutoDispose and Family Parameterized Providers</h3>
<p class="text-slate-600 mb-3">Creating auto-disposing cached providers parameterized by item ID using .autoDispose.family.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: AutoDispose and Family Parameterized Providers</span>
        <span class="text-xs text-slate-400 font-mono">lib/riverpod_family.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter_riverpod/flutter_riverpod.dart&#x27;;

// Family allows passing parameters; autoDispose cancels network/cache on screen exit!
final productDetailProvider = FutureProvider.autoDispose.family&lt;String, String&gt;((ref, productId) async {
  // Simulating fetch
  return &#x27;Product Details for: $productId&#x27;;
});

void main() {
  print(&#x27;Riverpod AutoDispose Family Provider declared.&#x27;);
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;

$content30 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>BLoC</strong> (Business Logic Component) separates presentation from business logic using reactive Streams, Unidirectional Data Flow, and distinct Event and State classes.</p>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: Minimal Cubit State Management</h3>
<p class="text-slate-600 mb-3">Using Cubit for straightforward synchronous and asynchronous state emissions without event boilerplate.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: Minimal Cubit State Management</span>
        <span class="text-xs text-slate-400 font-mono">lib/cubit_demo.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;
import &#x27;package:flutter_bloc/flutter_bloc.dart&#x27;;

class CounterCubit extends Cubit&lt;int&gt; {
  CounterCubit() : super(0);

  void increment() =&gt; emit(state + 1);
  void decrement() =&gt; emit(state - 1);
}

void main() {
  runApp(
    BlocProvider(
      create: (_) =&gt; CounterCubit(),
      child: const MaterialApp(home: CubitScreen()),
    ),
  );
}

class CubitScreen extends StatelessWidget {
  const CubitScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;Cubit State Management&#x27;)),
      body: Center(
        child: BlocBuilder&lt;CounterCubit, int&gt;(
          builder: (context, count) =&gt; Text(&#x27;$count&#x27;, style: const TextStyle(fontSize: 48, fontWeight: FontWeight.black)),
        ),
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () =&gt; context.read&lt;CounterCubit&gt;().increment(),
        child: const Icon(Icons.add),
      ),
    );
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: Enterprise BLoC with Sealed Class Events & States</h3>
<p class="text-slate-600 mb-3">Managing asynchronous loading, loaded, and error states with Dart 3 sealed class hierarchies.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: Enterprise BLoC with Sealed Class Events & States</span>
        <span class="text-xs text-slate-400 font-mono">lib/auth_bloc.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter_bloc/flutter_bloc.dart&#x27;;

// Sealed Events
sealed class AuthEvent {}
class LoginRequested extends AuthEvent {
  final String email;
  LoginRequested(this.email);
}

// Sealed States
sealed class AuthState {}
class AuthInitial extends AuthState {}
class AuthLoading extends AuthState {}
class AuthSuccess extends AuthState {
  final String user;
  AuthSuccess(this.user);
}

class AuthBloc extends Bloc&lt;AuthEvent, AuthState&gt; {
  AuthBloc() : super(AuthInitial()) {
    on&lt;LoginRequested&gt;((event, emit) async {
      emit(AuthLoading());
      await Future.delayed(const Duration(milliseconds: 300));
      emit(AuthSuccess(event.email));
    });
  }
}

void main() {
  print(&#x27;AuthBloc declared with strict Dart 3 sealed event/state pattern.&#x27;);
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: BlocListener for One-Off Navigation & Snackbars</h3>
<p class="text-slate-600 mb-3">Using BlocListener to trigger snackbars and navigation without re-evaluating the widget tree.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: BlocListener for One-Off Navigation & Snackbars</span>
        <span class="text-xs text-slate-400 font-mono">lib/bloc_listener_demo.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;
import &#x27;package:flutter_bloc/flutter_bloc.dart&#x27;;

void main() {
  print(&#x27;BlocListener listens for state transitions to perform side-effects (e.g. Navigator.push, SnackBar).&#x27;);
  print(&#x27;BlocBuilder is pure UI rendering.&#x27;);
  print(&#x27;BlocConsumer combines both!&#x27;);
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;

$content31 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Connecting Flutter applications to backend REST APIs uses <code>package:http</code> to perform GET, POST, PUT, and DELETE requests with custom authentication headers and status code checks.</p>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: HTTP GET Request with JSON Decoding</h3>
<p class="text-slate-600 mb-3">Fetching remote JSON data safely with status code validation and exception catching.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: HTTP GET Request with JSON Decoding</span>
        <span class="text-xs text-slate-400 font-mono">lib/http_get_demo.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;dart:convert&#x27;;
import &#x27;package:http/http.dart&#x27; as http;

Future&lt;void&gt; fetchPosts() async {
  final url = Uri.parse(&#x27;https://jsonplaceholder.typicode.com/posts/1&#x27;);
  
  try {
    final response = await http.get(url, headers: {&#x27;Accept&#x27;: &#x27;application/json&#x27;});
    
    if (response.statusCode == 200) {
      final Map&lt;String, dynamic&gt; data = jsonDecode(response.body);
      print(&#x27;Post Title: &#x27; + data[&#x27;title&#x27;]);
      print(&#x27;Post ID: &#x27; + data[&#x27;id&#x27;].toString());
    } else {
      print(&#x27;Server Error: ${response.statusCode}&#x27;);
    }
  } catch (err) {
    print(&#x27;Network Exception: $err&#x27;);
  }
}

void main() async {
  print(&#x27;Executing HTTP GET request...&#x27;);
  await fetchPosts();
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: HTTP POST Request with Bearer Auth Header</h3>
<p class="text-slate-600 mb-3">Sending JSON payloads with Bearer token authentication in request headers.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: HTTP POST Request with Bearer Auth Header</span>
        <span class="text-xs text-slate-400 font-mono">lib/http_post_demo.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;dart:convert&#x27;;
import &#x27;package:http/http.dart&#x27; as http;

Future&lt;void&gt; createOrder(String customerName, double amount) async {
  final url = Uri.parse(&#x27;https://api.nectradigital.com/orders&#x27;);
  
  final payload = jsonEncode({
    &#x27;customer&#x27;: customerName,
    &#x27;total&#x27;: amount,
    &#x27;timestamp&#x27;: DateTime.now().toIso8601String(),
  });

  print(&#x27;Sending payload: $payload&#x27;);
  // Simulated request logic
  print(&#x27;POST -&gt; HTTP 201 Created&#x27;);
}

void main() async {
  await createOrder(&#x27;Maria Anders&#x27;, 840.50);
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: HTTP Client Retry Interceptor Pattern</h3>
<p class="text-slate-600 mb-3">Wrapping HTTP requests with automatic retries and exponential backoff for flaky mobile connections.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: HTTP Client Retry Interceptor Pattern</span>
        <span class="text-xs text-slate-400 font-mono">lib/http_retry_client.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;dart:async&#x27;;

Future&lt;T&gt; retryWithBackoff&lt;T&gt;(Future&lt;T&gt; Function() operation, {int maxAttempts = 3}) async {
  int attempt = 0;
  while (true) {
    try {
      attempt++;
      return await operation();
    } catch (e) {
      if (attempt &gt;= maxAttempts) rethrow;
      final backoff = Duration(milliseconds: 200 * attempt);
      print(&#x27;[RETRY]: Attempt $attempt failed. Backing off for ${backoff.inMilliseconds}ms...&#x27;);
      await Future.delayed(backoff);
    }
  }
}

void main() async {
  print(&#x27;Testing retry logic...&#x27;);
  int counter = 0;
  final result = await retryWithBackoff(() async {
    counter++;
    if (counter &lt; 2) throw Exception(&#x27;Simulated SocketTimeout&#x27;);
    return &#x27;Success on attempt $counter!&#x27;;
  });
  print(result);
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;

$content32 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">JSON parsing translates raw untyped <code>Map&lt;String, dynamic&gt;</code> objects into strongly-typed immutable Dart models using either manual <code>fromJson</code> / <code>toJson</code> methods or automatic code generators like <code>json_serializable</code>.</p>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: Manual fromJson / toJson Model Mapping</h3>
<p class="text-slate-600 mb-3">Implementing explicit factory fromJson and toJson serializers with defensive null coalescing.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: Manual fromJson / toJson Model Mapping</span>
        <span class="text-xs text-slate-400 font-mono">lib/manual_json_model.dart</span>
    </div>
    <pre><code class="language-flutter">class CourseModel {
  final int id;
  final String title;
  final double price;
  final bool isPublished;

  const CourseModel({
    required this.id,
    required this.title,
    required this.price,
    this.isPublished = true,
  });

  factory CourseModel.fromJson(Map&lt;String, dynamic&gt; json) {
    return CourseModel(
      id: json[&#x27;id&#x27;] as int? ?? 0,
      title: json[&#x27;title&#x27;] as String? ?? &#x27;Untitled Course&#x27;,
      price: (json[&#x27;price&#x27;] as num?)?.toDouble() ?? 0.0,
      isPublished: json[&#x27;is_published&#x27;] as bool? ?? false,
    );
  }

  Map&lt;String, dynamic&gt; toJson() =&gt; {
    &#x27;id&#x27;: id,
    &#x27;title&#x27;: title,
    &#x27;price&#x27;: price,
    &#x27;is_published&#x27;: isPublished,
  };
}

void main() {
  final raw = {&#x27;id&#x27;: 101, &#x27;title&#x27;: &#x27;Complete Flutter &amp; Dart Track&#x27;, &#x27;price&#x27;: 49.99, &#x27;is_published&#x27;: true};
  final course = CourseModel.fromJson(raw);
  print(&#x27;Parsed Model: ${course.title} ($${course.price})&#x27;);
  print(&#x27;Serialized JSON: ${course.toJson()}&#x27;);
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: Parsing Nested JSON Object Arrays</h3>
<p class="text-slate-600 mb-3">Handling relational APIs where an object contains an array of child models.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: Parsing Nested JSON Object Arrays</span>
        <span class="text-xs text-slate-400 font-mono">lib/nested_json_parser.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;dart:convert&#x27;;

class OrderItem {
  final String sku;
  final int qty;
  const OrderItem(this.sku, this.qty);
  factory OrderItem.fromJson(Map&lt;String, dynamic&gt; j) =&gt; OrderItem(j[&#x27;sku&#x27;], j[&#x27;qty&#x27;]);
}

class Invoice {
  final String invoiceId;
  final List&lt;OrderItem&gt; items;

  const Invoice(this.invoiceId, this.items);

  factory Invoice.fromJson(Map&lt;String, dynamic&gt; j) {
    final rawList = j[&#x27;items&#x27;] as List&lt;dynamic&gt;? ?? [];
    return Invoice(
      j[&#x27;invoice_id&#x27;] as String,
      rawList.map((e) =&gt; OrderItem.fromJson(e as Map&lt;String, dynamic&gt;)).toList(),
    );
  }
}

void main() {
  const jsonString = &#x27;{&quot;invoice_id&quot;: &quot;INV-9001&quot;, &quot;items&quot;: [{&quot;sku&quot;: &quot;MACBOOK_M3&quot;, &quot;qty&quot;: 1}, {&quot;sku&quot;: &quot;CABLE_USBC&quot;, &quot;qty&quot;: 3}]}&#x27;;
  final invoice = Invoice.fromJson(jsonDecode(jsonString));
  print(&#x27;Invoice: ${invoice.invoiceId} with ${invoice.items.length} items.&#x27;);
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: json_serializable Annotation Setup</h3>
<p class="text-slate-600 mb-3">Using @JsonSerializable() and part file directives for build_runner automated code generation.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: json_serializable Annotation Setup</span>
        <span class="text-xs text-slate-400 font-mono">lib/user_entity.g.dart</span>
    </div>
    <pre><code class="language-flutter">// Annotated Model for build_runner:
// import &#x27;package:json_annotation/json_annotation.dart&#x27;;
// part &#x27;user_entity.g.dart&#x27;;

// @JsonSerializable()
// class UserEntity {
//   final String id;
//   final String name;
//   UserEntity(this.id, this.name);
//   factory UserEntity.fromJson(Map&lt;String, dynamic&gt; json) =&gt; _$UserEntityFromJson(json);
//   Map&lt;String, dynamic&gt; toJson() =&gt; _$UserEntityToJson(this);
// }

void main() {
  print(&#x27;Run in terminal: dart run build_runner build --delete-conflicting-outputs&#x27;);
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;

$content33 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><code>FutureBuilder</code> and <code>StreamBuilder</code> connect asynchronous data sources directly to the Flutter widget tree, managing <code>ConnectionState.waiting</code>, error fallbacks, and real-time updates gracefully.</p>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: FutureBuilder with Loading, Error, and Data States</h3>
<p class="text-slate-600 mb-3">Rendering an asynchronous future cleanly with all three lifecycle states handled.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: FutureBuilder with Loading, Error, and Data States</span>
        <span class="text-xs text-slate-400 font-mono">lib/future_builder_demo.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

void main() =&gt; runApp(const MaterialApp(home: FutureBuilderDemoScreen()));

class FutureBuilderDemoScreen extends StatelessWidget {
  const FutureBuilderDemoScreen({super.key});

  Future&lt;String&gt; _fetchServerStatus() async {
    await Future.delayed(const Duration(seconds: 1));
    return &#x27;All 24 Node Services Operational (Impeller Active)&#x27;;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;FutureBuilder Lifecycle&#x27;)),
      body: Center(
        child: FutureBuilder&lt;String&gt;(
          future: _fetchServerStatus(),
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const CircularProgressIndicator();
            } else if (snapshot.hasError) {
              return Text(&#x27;Error: ${snapshot.error}&#x27;, style: const TextStyle(color: Colors.red));
            } else if (snapshot.hasData) {
              return Text(snapshot.data!, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold));
            }
            return const Text(&#x27;No Data&#x27;);
          },
        ),
      ),
    );
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: StreamBuilder for Live Sensor / Socket Updates</h3>
<p class="text-slate-600 mb-3">Listening to continuous real-time data events with StreamBuilder.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: StreamBuilder for Live Sensor / Socket Updates</span>
        <span class="text-xs text-slate-400 font-mono">lib/stream_builder_demo.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

Stream&lt;int&gt; _liveTicker() async* {
  int count = 0;
  while (true) {
    await Future.delayed(const Duration(seconds: 1));
    yield ++count;
  }
}

void main() =&gt; runApp(MaterialApp(
  home: Scaffold(
    appBar: AppBar(title: const Text(&#x27;StreamBuilder Live Feed&#x27;)),
    body: Center(
      child: StreamBuilder&lt;int&gt;(
        stream: _liveTicker(),
        builder: (context, snapshot) {
          if (!snapshot.hasData) return const Text(&#x27;Connecting...&#x27;);
          return Text(&#x27;Heartbeat Pulse: ${snapshot.data}s&#x27;, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold));
        },
      ),
    ),
  ),
));</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: Caching Futures in State to Avoid Re-triggering on Rebuild</h3>
<p class="text-slate-600 mb-3">Storing Future in initState to prevent unwanted duplicate network calls when widget rebuilds.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: Caching Futures in State to Avoid Re-triggering on Rebuild</span>
        <span class="text-xs text-slate-400 font-mono">lib/cached_future_pattern.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

class SafeFutureScreen extends StatefulWidget {
  const SafeFutureScreen({super.key});
  @override
  State&lt;SafeFutureScreen&gt; createState() =&gt; _SafeFutureScreenState();
}

class _SafeFutureScreenState extends State&lt;SafeFutureScreen&gt; {
  late Future&lt;String&gt; _cachedFuture;

  @override
  void initState() {
    super.initState();
    // Cache future ONCE so setState on parent does NOT re-trigger network call!
    _cachedFuture = Future.delayed(const Duration(seconds: 1), () =&gt; &#x27;Cached Response&#x27;);
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder&lt;String&gt;(
      future: _cachedFuture,
      builder: (ctx, snap) =&gt; Text(snap.data ?? &#x27;Loading...&#x27;),
    );
  }
}

void main() =&gt; print(&#x27;Cached Future pattern prevents redundant HTTP requests on build()!&#x27;);</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;

$content34 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>Implicit Animations</strong> like <code>AnimatedContainer</code>, <code>AnimatedOpacity</code>, and <code>AnimatedAlign</code> interpolate automatically between old and new property values whenever a rebuild occurs, requiring no explicit controllers or tickers.</p>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: AnimatedContainer Smooth Geometric Morphing</h3>
<p class="text-slate-600 mb-3">Morphing width, height, color, and borderRadius smoothly on tap.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: AnimatedContainer Smooth Geometric Morphing</span>
        <span class="text-xs text-slate-400 font-mono">lib/animated_container_demo.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

void main() =&gt; runApp(const MaterialApp(home: MorphingBoxScreen()));

class MorphingBoxScreen extends StatefulWidget {
  const MorphingBoxScreen({super.key});
  @override
  State&lt;MorphingBoxScreen&gt; createState() =&gt; _MorphingBoxScreenState();
}

class _MorphingBoxScreenState extends State&lt;MorphingBoxScreen&gt; {
  bool _expanded = false;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;AnimatedContainer Morphing&#x27;)),
      body: Center(
        child: GestureDetector(
          onTap: () =&gt; setState(() =&gt; _expanded = !_expanded),
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 500),
            curve: Curves.easeInOutCubic,
            width: _expanded ? 240 : 120,
            height: _expanded ? 240 : 120,
            decoration: BoxDecoration(
              color: _expanded ? Colors.indigo : Colors.emerald,
              borderRadius: BorderRadius.circular(_expanded ? 60 : 16),
            ),
            child: Center(
              child: Text(
                _expanded ? &#x27;Tap to Shrink&#x27; : &#x27;Tap to Grow&#x27;,
                style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
              ),
            ),
          ),
        ),
      ),
    );
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: AnimatedOpacity for Smooth Fade Transitions</h3>
<p class="text-slate-600 mb-3">Fading text labels or alerts in and out with AnimatedOpacity.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: AnimatedOpacity for Smooth Fade Transitions</span>
        <span class="text-xs text-slate-400 font-mono">lib/animated_opacity_demo.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

void main() =&gt; runApp(const MaterialApp(home: FadeDemoScreen()));

class FadeDemoScreen extends StatefulWidget {
  const FadeDemoScreen({super.key});
  @override
  State&lt;FadeDemoScreen&gt; createState() =&gt; _FadeDemoScreenState();
}

class _FadeDemoScreenState extends State&lt;FadeDemoScreen&gt; {
  bool _visible = true;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;AnimatedOpacity Fade&#x27;)),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            AnimatedOpacity(
              opacity: _visible ? 1.0 : 0.0,
              duration: const Duration(milliseconds: 600),
              child: const Text(&#x27;Smooth Fading Alert Message&#x27;, style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            ),
            const SizedBox(height: 20),
            ElevatedButton(
              onPressed: () =&gt; setState(() =&gt; _visible = !_visible),
              child: Text(_visible ? &#x27;Hide Alert&#x27; : &#x27;Show Alert&#x27;),
            ),
          ],
        ),
      ),
    );
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: AnimatedCrossFade for View Switching</h3>
<p class="text-slate-600 mb-3">Fading between a loading spinner and content card cleanly using AnimatedCrossFade.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: AnimatedCrossFade for View Switching</span>
        <span class="text-xs text-slate-400 font-mono">lib/cross_fade_demo.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

void main() =&gt; runApp(const MaterialApp(home: CrossFadeScreen()));

class CrossFadeScreen extends StatefulWidget {
  const CrossFadeScreen({super.key});
  @override
  State&lt;CrossFadeScreen&gt; createState() =&gt; _CrossFadeScreenState();
}

class _CrossFadeScreenState extends State&lt;CrossFadeScreen&gt; {
  bool _showFirst = true;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;AnimatedCrossFade&#x27;)),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            AnimatedCrossFade(
              duration: const Duration(milliseconds: 400),
              firstChild: Container(width: 160, height: 100, color: Colors.blue, alignment: Alignment.center, child: const Text(&#x27;State A&#x27;, style: TextStyle(color: Colors.white))),
              secondChild: Container(width: 160, height: 100, color: Colors.purple, alignment: Alignment.center, child: const Text(&#x27;State B&#x27;, style: TextStyle(color: Colors.white))),
              crossFadeState: _showFirst ? CrossFadeState.showFirst : CrossFadeState.showSecond,
            ),
            const SizedBox(height: 20),
            ElevatedButton(onPressed: () =&gt; setState(() =&gt; _showFirst = !_showFirst), child: const Text(&#x27;Toggle State&#x27;)),
          ],
        ),
      ),
    );
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;

$content35 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>Explicit Animations</strong> give you total programmatic control through <code>AnimationController</code>, <code>CurvedAnimation</code>, and <code>Tween</code>. <code>Hero</code> animations provide cinematic shared-element transitions across screen routes.</p>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: Continuous Rotation with AnimationController & AnimatedBuilder</h3>
<p class="text-slate-600 mb-3">Spinning an icon continuously at 60 FPS using AnimationController and AnimatedBuilder.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: Continuous Rotation with AnimationController & AnimatedBuilder</span>
        <span class="text-xs text-slate-400 font-mono">lib/rotation_animation.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;dart:math&#x27; as math;
import &#x27;package:flutter/material.dart&#x27;;

void main() =&gt; runApp(const MaterialApp(home: RotationScreen()));

class RotationScreen extends StatefulWidget {
  const RotationScreen({super.key});
  @override
  State&lt;RotationScreen&gt; createState() =&gt; _RotationScreenState();
}

class _RotationScreenState extends State&lt;RotationScreen&gt; with SingleTickerProviderStateMixin {
  late final AnimationController _controller;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(duration: const Duration(seconds: 4), vsync: this)..repeat();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;Explicit AnimationController&#x27;)),
      body: Center(
        child: AnimatedBuilder(
          animation: _controller,
          builder: (context, child) =&gt; Transform.rotate(angle: _controller.value * 2 * math.pi, child: child),
          child: const Icon(Icons.sync, size: 80, color: Colors.indigo),
        ),
      ),
    );
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: Curved Tween Physics Animation</h3>
<p class="text-slate-600 mb-3">Interpolating custom double values with Curves.elasticOut for bounce feedback.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: Curved Tween Physics Animation</span>
        <span class="text-xs text-slate-400 font-mono">lib/bounce_animation.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

void main() =&gt; runApp(const MaterialApp(home: BounceScreen()));

class BounceScreen extends StatefulWidget {
  const BounceScreen({super.key});
  @override
  State&lt;BounceScreen&gt; createState() =&gt; _BounceScreenState();
}

class _BounceScreenState extends State&lt;BounceScreen&gt; with SingleTickerProviderStateMixin {
  late final AnimationController _ctrl;
  late final Animation&lt;double&gt; _scaleAnimation;

  @override
  void initState() {
    super.initState();
    _ctrl = AnimationController(duration: const Duration(milliseconds: 800), vsync: this);
    _scaleAnimation = Tween&lt;double&gt;(begin: 0.5, end: 1.5).animate(
      CurvedAnimation(parent: _ctrl, curve: Curves.elasticOut),
    );
  }

  @override
  void dispose() {
    _ctrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;Curved Tween Animation&#x27;)),
      body: Center(
        child: ScaleTransition(
          scale: _scaleAnimation,
          child: ElevatedButton(onPressed: () =&gt; _ctrl.forward(from: 0.0), child: const Text(&#x27;Bounce Me!&#x27;)),
        ),
      ),
    );
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: Cinematic Hero Shared Element Transition</h3>
<p class="text-slate-600 mb-3">Morphing an avatar smoothly between a list view and a profile detail screen using Hero tags.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: Cinematic Hero Shared Element Transition</span>
        <span class="text-xs text-slate-400 font-mono">lib/hero_transition.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

void main() =&gt; runApp(const MaterialApp(home: HeroSourceScreen()));

class HeroSourceScreen extends StatelessWidget {
  const HeroSourceScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;Hero Source&#x27;)),
      body: Center(
        child: GestureDetector(
          onTap: () =&gt; Navigator.push(context, MaterialPageRoute(builder: (_) =&gt; const HeroDestScreen())),
          child: const Hero(
            tag: &#x27;user_avatar&#x27;,
            child: CircleAvatar(radius: 36, backgroundColor: Colors.indigo, child: Icon(Icons.person, color: Colors.white, size: 36)),
          ),
        ),
      ),
    );
  }
}

class HeroDestScreen extends StatelessWidget {
  const HeroDestScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;Hero Destination&#x27;)),
      body: const Center(
        child: Hero(
          tag: &#x27;user_avatar&#x27;,
          child: CircleAvatar(radius: 80, backgroundColor: Colors.indigo, child: Icon(Icons.person, color: Colors.white, size: 80)),
        ),
      ),
    );
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;


insertChapter($pdo, $courseId, 24, 'Navigation & State Management', 'Navigation 1.0: Imperative Routing, Stack Mechanics & Returning Data', $content24, 'navigator push flutter, navigator pop, materialpageroute, pass arguments between screens flutter, return data navigator flutter');
insertChapter($pdo, $courseId, 25, 'Navigation & State Management', 'Modern Declarative Routing with GoRouter: Deep Links & Auth Guards', $content25, 'gorouter flutter, declarative navigation flutter, deep linking flutter, route parameters gorouter, shellroute nested navigation');
insertChapter($pdo, $courseId, 26, 'Navigation & State Management', 'State Management Foundations: Ephemeral vs. App State & ValueNotifier', $content26, 'valuenotifier flutter, valuelistenablebuilder, ephemeral vs app state, flutter state management basics, efficient rebuilds');
insertChapter($pdo, $courseId, 27, 'Navigation & State Management', 'Low-Level State Architecture: InheritedWidget & O(1) Tree Lookup', $content27, 'inheritedwidget flutter, updateshouldnotify, how provider works internally, dependoninheritedwidgetofexacttype, flutter data propagation');
insertChapter($pdo, $courseId, 28, 'Navigation & State Management', 'Provider State Management: ChangeNotifier, Consumer & Granular Select', $content28, 'provider flutter, changenotifier, notifylisteners, context watch vs read, context select provider, flutter state management provider');
insertChapter($pdo, $courseId, 29, 'Navigation & State Management', 'Riverpod Modern Architecture: Compile-Time Safety & NotifierProvider', $content29, 'riverpod flutter, flutter_riverpod, providerscope, consumerwidget, ref watch vs ref read, asyncvalue riverpod, riverpod 2');
insertChapter($pdo, $courseId, 30, 'Navigation & State Management', 'BLoC & Cubit Architecture: Events, States & Unidirectional Data Flow', $content30, 'flutter bloc, cubit flutter, flutter_bloc, blocbuilder, bloclistener, reactive state management flutter, flutter clean architecture');
insertChapter($pdo, $courseId, 31, 'Networking & Data Architecture', 'Networking & REST APIs: GET, POST, Headers & Error Handling', $content31, 'flutter rest api, http package flutter, dio flutter, jsondecode flutter, futurebuilder api integration, error handling network');
insertChapter($pdo, $courseId, 32, 'Networking & Data Architecture', 'JSON Parsing & Models: Manual Deserialization & json_serializable', $content32, 'json parsing flutter, json_serializable, build_runner flutter, fromjson tojson, type safe json flutter');
insertChapter($pdo, $courseId, 33, 'Networking & Data Architecture', 'Async UI Builders: FutureBuilder & StreamBuilder in Action', $content33, 'futurebuilder flutter, streambuilder flutter, asyncsnapshot, connectionstate waiting, real time streams flutter, websocket streambuilder');
insertChapter($pdo, $courseId, 34, 'Animations & Visual Effects', 'Implicit Animations: AnimatedContainer, AnimatedOpacity & Curves', $content34, 'animatedcontainer flutter, implicit animations flutter, curves fastoutslowin, animatedopacity, tweenanimationbuilder flutter');
insertChapter($pdo, $courseId, 35, 'Animations & Visual Effects', 'Explicit Animations: AnimationController, TickerProvider & Hero Transitions', $content35, 'animationcontroller flutter, singletickerproviderstatemixin, tween flutter, animatedbuilder, hero animation flutter, custom page route transition');
