<?php
// seed_flutter_part1.php: Chapters 1 to 11 (3+ examples each)

$content1 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>Flutter</strong> is Google's open-source multi-platform UI framework that compiles directly to native ARM machine code for Android, iOS, macOS, Windows, Linux, and Web from a single codebase written in <strong>Dart</strong>. Unlike hybrid web wrappers or bridge-dependent frameworks, Flutter renders every single pixel directly using its next-generation graphics rendering engine: <strong>Impeller</strong>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Cross-Platform Architectures: Flutter (Impeller) vs. React Native vs. Hybrid WebViews</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 20%;">Architecture</th>
                <th style="width: 25%;">Rendering Pipeline</th>
                <th style="width: 25%;">Execution Model</th>
                <th style="width: 30%;">Performance &amp; Frame Rate</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Flutter (Impeller / Skia)</td>
                <td>Own native rasterizer draws directly to canvas/GPU surface.</td>
                <td>Ahead-of-Time (AOT) compiled to native C/C++ machine code.</td>
                <td>Rock-solid 60/120 FPS; zero bridge serialization lag; eliminates shader compilation jank.</td>
            </tr>
            <tr>
                <td class="code-cell">React Native (JSI / Fabric)</td>
                <td>Maps JavaScript virtual tree to native OEM platform widgets.</td>
                <td>Hermes JavaScript engine running over JSI bridge.</td>
                <td>Fast, but heavy bridge crossings or complex layout reconciliations can cause UI drops.</td>
            </tr>
            <tr>
                <td class="code-cell">Hybrid WebViews (Ionic/Cordova)</td>
                <td>Runs inside native WebView HTML5 browser instance.</td>
                <td>Interpreted web JavaScript inside Chromium/WebKit.</td>
                <td>High DOM overhead, touch latency, sluggish scrolling on low-end devices.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-2xl font-black text-slate-800 mt-8 mb-4">Deep Architectural Mechanics: The Impeller Engine Pipeline</h3>
<p class="text-slate-600 mb-4">In legacy mobile graphics (Skia), OpenGL shaders were compiled on-demand in the first frame a visual effect appeared, leading to noticeable <em>shader compilation jank</em> on iOS and Android. <strong>Impeller</strong> completely re-architects the graphics pipeline by pre-compiling all vertex and fragment shaders at application build time into optimized Vulkan (Android) and Metal (iOS) pipelines.</p>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: Minimal Flutter App Scaffold with MaterialApp</h3>
<p class="text-slate-600 mb-3">A clean, minimal starting point showing the root widget structure, MaterialApp wrapper, Scaffold and Center.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: Minimal Flutter App Scaffold with MaterialApp</span>
        <span class="text-xs text-slate-400 font-mono">lib/main.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

void main() {
  runApp(const MinimalFlutterApp());
}

class MinimalFlutterApp extends StatelessWidget {
  const MinimalFlutterApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: &#x27;Flutter Foundation&#x27;,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.sky),
        useMaterial3: true,
      ),
      home: Scaffold(
        appBar: AppBar(
          title: const Text(&#x27;Flutter 3.x Foundation&#x27;),
          backgroundColor: Colors.sky,
          foregroundColor: Colors.white,
        ),
        body: const Center(
          child: Text(
            &#x27;Hello from Flutter Impeller Engine!&#x27;,
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
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

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: Diagnostic System Architecture & Hardware Telemetry</h3>
<p class="text-slate-600 mb-3">Inspecting system runtime information, platform diagnostics, and Flutter engine release mode properties.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: Diagnostic System Architecture & Hardware Telemetry</span>
        <span class="text-xs text-slate-400 font-mono">lib/diagnostics_screen.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;
import &#x27;package:flutter/foundation.dart&#x27;;

void main() {
  runApp(const MaterialApp(
    debugShowCheckedModeBanner: false,
    home: EngineDiagnosticsScreen(),
  ));
}

class EngineDiagnosticsScreen extends StatelessWidget {
  const EngineDiagnosticsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text(&#x27;Engine Diagnostics &amp; Telemetry&#x27;),
        backgroundColor: Colors.indigo,
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              &#x27;Runtime System Specifications&#x27;,
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.indigo),
            ),
            const SizedBox(height: 12),
            _buildMetricTile(&#x27;Flutter Engine Mode&#x27;, kReleaseMode ? &#x27;AOT Release&#x27; : (kProfileMode ? &#x27;Profile&#x27; : &#x27;JIT Debug Hot Reload&#x27;)),
            _buildMetricTile(&#x27;Default Target Platform&#x27;, defaultTargetPlatform.name.toUpperCase()),
            _buildMetricTile(&#x27;Impeller Backend Renderer&#x27;, &#x27;Metal (iOS) / Vulkan (Android)&#x27;),
            _buildMetricTile(&#x27;Refresh Rate Pipeline&#x27;, &#x27;120 FPS ProMotion / High Refresh Supported&#x27;),
            const Spacer(),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: () {
                  debugPrint(&#x27;[TELEMETRY]: Engine pipeline running at 120 FPS with 0 dropped frames.&#x27;);
                },
                icon: const Icon(Icons.speed),
                label: const Text(&#x27;Log Performance Metric&#x27;),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.indigo,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  static Widget _buildMetricTile(String label, String value) {
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: Colors.grey.shade300),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(fontWeight: FontWeight.w600, color: Colors.black87)),
          Text(value, style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.indigo)),
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

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: Direct GPU Surface Rendering via CustomPaint</h3>
<p class="text-slate-600 mb-3">Demonstrating how Flutter renders shapes and anti-aliased graphics directly on the GPU surface without web DOM wrappers.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: Direct GPU Surface Rendering via CustomPaint</span>
        <span class="text-xs text-slate-400 font-mono">lib/gpu_canvas.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

void main() {
  runApp(const MaterialApp(
    debugShowCheckedModeBanner: false,
    home: GpuCanvasDemo(),
  ));
}

class GpuCanvasDemo extends StatelessWidget {
  const GpuCanvasDemo({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;Direct GPU Rendering Surface&#x27;)),
      body: Center(
        child: Container(
          width: 240,
          height: 240,
          decoration: BoxDecoration(
            color: Colors.slate.shade900,
            borderRadius: BorderRadius.circular(24),
            boxShadow: const [BoxShadow(color: Colors.black26, blurRadius: 16, offset: Offset(0, 8))],
          ),
          child: CustomPaint(
            painter: ImpellerGpuPainter(),
            child: const Center(
              child: Text(
                &#x27;Impeller 120 FPS\nHardware Accelerated&#x27;,
                textAlign: TextAlign.center,
                style: TextStyle(color: Colors.cyanAccent, fontWeight: FontWeight.bold, fontSize: 13),
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class ImpellerGpuPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = Colors.cyanAccent.withOpacity(0.5)
      ..style = PaintingStyle.stroke
      ..strokeWidth = 3.0;

    final center = Offset(size.width / 2, size.height / 2);
    canvas.drawCircle(center, size.width * 0.38, paint);
    canvas.drawCircle(center, size.width * 0.46, paint..color = Colors.indigoAccent);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) =&gt; false;
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;

$content2 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><strong>Dart</strong> features <strong>100% Sound Null Safety</strong>. In sound null safety, the type system guarantees at compile time that an expression of a non-nullable type can never evaluate to <code>null</code>, completely preventing runtime <code>NullPointerExceptions</code>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Sound Null Safety Operators Cheatsheet</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 15%;">Operator</th>
                <th style="width: 25%;">Meaning</th>
                <th style="width: 30%;">Syntax Example</th>
                <th style="width: 30%;">Compile &amp; Runtime Behavior</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>Type?</code></td>
                <td>Nullable Declaration</td>
                <td><code>String? userEmail;</code></td>
                <td>Explicitly tells compiler this variable may hold null. Default value is null.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>?.</code></td>
                <td>Null-Aware Invocation</td>
                <td><code>userEmail?.toLowerCase()</code></td>
                <td>Short-circuits to null if target is null, never throws NullPointerException.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>??</code></td>
                <td>Null-Coalescing Fallback</td>
                <td><code>final email = userEmail ?? 'default@mail.com';</code></td>
                <td>Returns right operand when left operand is null.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>??=</code></td>
                <td>Null-Aware Assignment</td>
                <td><code>cacheToken ??= generateToken();</code></td>
                <td>Executes assignment ONLY if the target variable is currently null.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>!</code></td>
                <td>Null Assertion Cast</td>
                <td><code>int len = userEmail!.length;</code></td>
                <td>Tells compiler "Trust me, this is not null". Throws TypeError if null. Use with extreme care.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>late</code></td>
                <td>Deferred Initialization</td>
                <td><code>late final Database db;</code></td>
                <td>Guarantees non-null initialization before first access (common in initState).</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: Safe Null Handling & Fallback Values</h3>
<p class="text-slate-600 mb-3">Basic null safety fundamentals demonstrating non-nullable variables, fallback coalescing, and null-aware cascades.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: Safe Null Handling & Fallback Values</span>
        <span class="text-xs text-slate-400 font-mono">lib/null_safety_basics.dart</span>
    </div>
    <pre><code class="language-flutter">void main() {
  String companyName = &#x27;Nectra Digital&#x27;;
  String? secondaryAddress; // Can be null

  print(&#x27;Company: &#x27; + companyName);
  
  // Safe navigation with ??
  String activeAddress = secondaryAddress ?? &#x27;Primary Headquarters (Default)&#x27;;
  print(&#x27;Active Address: &#x27; + activeAddress);

  // Safe method call with ?.
  int? addressLength = secondaryAddress?.length;
  print(&#x27;Secondary Address length: &#x27; + (addressLength?.toString() ?? &#x27;None (Null)&#x27;));

  // Null-aware assignment
  secondaryAddress ??= &#x27;Branch Office Mumbai&#x27;;
  print(&#x27;Updated Address: &#x27; + secondaryAddress);
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: Deep Immutability: final vs. const vs. late</h3>
<p class="text-slate-600 mb-3">Understanding compile-time constants (const), runtime immutability (final), and deferred setup (late).</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: Deep Immutability: final vs. const vs. late</span>
        <span class="text-xs text-slate-400 font-mono">lib/immutability_demo.dart</span>
    </div>
    <pre><code class="language-flutter">void main() {
  // const: Canonical compile-time constant. Value known at build time.
  const double baseTaxRate = 0.18;
  const List&lt;String&gt; supportedCurrencies = [&#x27;USD&#x27;, &#x27;EUR&#x27;, &#x27;INR&#x27;];

  // final: Single-assignment runtime constant. Value determined at runtime.
  final DateTime sessionStart = DateTime.now();
  final String deviceId = &#x27;DEV_&#x27; + sessionStart.millisecondsSinceEpoch.toString();

  print(&#x27;Tax Rate (const): &#x27; + baseTaxRate.toString());
  print(&#x27;Device ID (final): &#x27; + deviceId);
  print(&#x27;Currencies: &#x27; + supportedCurrencies.join(&#x27;, &#x27;));

  // Late initialization demonstration
  late final String encryptionKey = computeKey();
  print(&#x27;Calling computeKey now...&#x27;);
  print(&#x27;Key initialized: &#x27; + encryptionKey);
}

String computeKey() {
  print(&#x27;-&gt; [Heavy Key Computation Executed]&#x27;);
  return &#x27;AES_256_GCM_SECURE_TOKEN&#x27;;
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: Enterprise Profile Model with Safe Parsing & Type Casting</h3>
<p class="text-slate-600 mb-3">Real-world data parsing illustrating strict typing, safe null coalescing, and defensive parsing.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: Enterprise Profile Model with Safe Parsing & Type Casting</span>
        <span class="text-xs text-slate-400 font-mono">lib/user_profile_parser.dart</span>
    </div>
    <pre><code class="language-flutter">class UserProfile {
  final String userId;
  final String displayName;
  final String email;
  final double accountBalance;
  final bool isVerified;

  const UserProfile({
    required this.userId,
    required this.displayName,
    required this.email,
    required this.accountBalance,
    required this.isVerified,
  });

  factory UserProfile.fromRawMap(Map&lt;String, dynamic&gt; json) {
    return UserProfile(
      userId: json[&#x27;id&#x27;] as String? ?? &#x27;ANON_USER&#x27;,
      displayName: json[&#x27;full_name&#x27;] as String? ?? &#x27;Anonymous Member&#x27;,
      email: json[&#x27;email&#x27;] as String? ?? &#x27;no-email-provided&#x27;,
      accountBalance: (json[&#x27;balance&#x27;] as num?)?.toDouble() ?? 0.0,
      isVerified: json[&#x27;verified&#x27;] as bool? ?? false,
    );
  }

  void printSummary() {
    print(&#x27;User: &#x27; + displayName + &#x27; (&#x27; + userId + &#x27;)&#x27;);
    print(&#x27;Email: &#x27; + email + &#x27; | Verified: &#x27; + isVerified.toString());
    print(&#x27;Balance: $&#x27; + accountBalance.toStringAsFixed(2));
  }
}

void main() {
  final Map&lt;String, dynamic&gt; rawApiData = {
    &#x27;id&#x27;: &#x27;USR_9821&#x27;,
    &#x27;full_name&#x27;: &#x27;Aarav Mehta&#x27;,
    &#x27;balance&#x27;: 4850.75,
  };

  final profile = UserProfile.fromRawMap(rawApiData);
  profile.printSummary();
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;

$content3 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Modern Dart collections (<code>List</code>, <code>Set</code>, <code>Map</code>) provide powerful functional syntax, including the <strong>spread operator</strong> (<code>...</code> and null-aware <code>...?</code>), <strong>collection-if</strong>, and <strong>collection-for</strong>. These features allow you to build declarative lists and widget trees dynamically without clunky imperative loops.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">Dart Collection Types Comparison</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 15%;">Collection</th>
                <th style="width: 20%;">Characteristics</th>
                <th style="width: 35%;">Common Use Case</th>
                <th style="width: 30%;">Complexity (Lookup / Add)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>List&lt;E&gt;</code></td>
                <td>Ordered sequence, allows duplicates, 0-indexed.</td>
                <td>Widget children lists, feed items, history records.</td>
                <td>O(1) index access, O(1) amortized append.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>Set&lt;E&gt;</code></td>
                <td>Unordered collection of unique items based on hash.</td>
                <td>Tags, selected IDs, unique user permissions.</td>
                <td>O(1) average lookup/containment check.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>Map&lt;K, V&gt;</code></td>
                <td>Key-value dictionary, keys must be unique.</td>
                <td>JSON payloads, HTTP headers, cache registries.</td>
                <td>O(1) key access, O(1) insertion.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: Declarative List Building with Spread & Collection-If</h3>
<p class="text-slate-600 mb-3">Constructing dynamic lists and UI actions cleanly using collection-if, collection-for, and the spread operator.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: Declarative List Building with Spread & Collection-If</span>
        <span class="text-xs text-slate-400 font-mono">lib/collection_flow_demo.dart</span>
    </div>
    <pre><code class="language-flutter">void main() {
  bool isAdmin = true;
  bool isGuest = false;
  List&lt;String&gt;? optionalTags = [&#x27;FinTech&#x27;, &#x27;CloudNative&#x27;];

  List&lt;String&gt; navMenu = [
    &#x27;Home&#x27;,
    &#x27;Catalog&#x27;,
    if (isAdmin) &#x27;Admin Console&#x27;,
    if (!isGuest) &#x27;Order History&#x27;,
    ...?optionalTags,
  ];

  print(&#x27;Generated Navigation Items:&#x27;);
  for (int i = 0; i &lt; navMenu.length; i++) {
    print(&#x27;  [&#x27; + (i + 1).toString() + &#x27;] &#x27; + navMenu[i]);
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: Set Operations & Fast Deduplication</h3>
<p class="text-slate-600 mb-3">Leveraging mathematical Set operations (union, intersection, difference) for high-performance permission checks.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: Set Operations & Fast Deduplication</span>
        <span class="text-xs text-slate-400 font-mono">lib/set_permissions.dart</span>
    </div>
    <pre><code class="language-flutter">void main() {
  final Set&lt;String&gt; defaultUserRoles = {&#x27;READ_CONTENT&#x27;, &#x27;WRITE_COMMENTS&#x27;, &#x27;LIKE_POST&#x27;};
  final Set&lt;String&gt; adminRoles = {&#x27;READ_CONTENT&#x27;, &#x27;WRITE_COMMENTS&#x27;, &#x27;DELETE_POST&#x27;, &#x27;BAN_USER&#x27;, &#x27;MANAGE_BILLING&#x27;};

  // Set Union (Combining unique permissions)
  final Set&lt;String&gt; allSystemRoles = defaultUserRoles.union(adminRoles);
  print(&#x27;Total Unique Roles: &#x27; + allSystemRoles.length.toString());

  // Set Intersection (Shared permissions)
  final Set&lt;String&gt; sharedRoles = defaultUserRoles.intersection(adminRoles);
  print(&#x27;Shared Roles: &#x27; + sharedRoles.join(&#x27;, &#x27;));

  // Set Difference (Exclusive admin permissions)
  final Set&lt;String&gt; adminExclusive = adminRoles.difference(defaultUserRoles);
  print(&#x27;Admin Exclusive: &#x27; + adminExclusive.join(&#x27;, &#x27;));
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: Higher-Order Functional Transformations (map, where, fold)</h3>
<p class="text-slate-600 mb-3">Processing complex product data with functional pipelines: filtering, mapping, and aggregating totals.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: Higher-Order Functional Transformations (map, where, fold)</span>
        <span class="text-xs text-slate-400 font-mono">lib/cart_pipeline.dart</span>
    </div>
    <pre><code class="language-flutter">class CartItem {
  final String title;
  final double price;
  final int quantity;
  final String category;

  const CartItem(this.title, this.price, this.quantity, this.category);
}

void main() {
  final List&lt;CartItem&gt; cart = [
    const CartItem(&#x27;MacBook Pro M3&#x27;, 1999.00, 1, &#x27;Electronics&#x27;),
    const CartItem(&#x27;USB-C Cable&#x27;, 19.99, 2, &#x27;Accessories&#x27;),
    const CartItem(&#x27;Mechanical Keyboard&#x27;, 89.50, 1, &#x27;Electronics&#x27;),
    const CartItem(&#x27;Leather Mousepad&#x27;, 24.00, 1, &#x27;Accessories&#x27;),
  ];

  // 1. Filter items in &#x27;Electronics&#x27; category
  final electronics = cart.where((item) =&gt; item.category == &#x27;Electronics&#x27;);
  print(&#x27;Electronics count: &#x27; + electronics.length.toString());

  // 2. Compute grand total using fold()
  final double subtotal = cart.fold(
    0.0,
    (previousSum, item) =&gt; previousSum + (item.price * item.quantity),
  );
  print(&#x27;Subtotal Amount: $&#x27; + subtotal.toStringAsFixed(2));

  // 3. Tax calculation (8.5%)
  final double grandTotal = subtotal * 1.085;
  print(&#x27;Grand Total (with Tax): $&#x27; + grandTotal.toStringAsFixed(2));
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;

$content4 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Dart is a class-based, object-oriented language with single inheritance and mixin-based reuse. In Flutter applications, models and state objects are almost always <strong>immutable</strong>. Understanding <strong>named constructors</strong>, <strong>const constructors</strong>, and the <strong>factory pattern</strong> is essential for clean code.</p>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: Named Constructors & Initializer Lists</h3>
<p class="text-slate-600 mb-3">Declaring classes with default constructors, named constructors, and parameter initializer lists.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: Named Constructors & Initializer Lists</span>
        <span class="text-xs text-slate-400 font-mono">lib/user_entity.dart</span>
    </div>
    <pre><code class="language-flutter">class UserEntity {
  final String id;
  final String name;
  final String role;
  final bool isActive;

  // Primary constructor with required named parameters
  const UserEntity({
    required this.id,
    required this.name,
    required this.role,
    this.isActive = true,
  });

  // Named constructor for guest users
  const UserEntity.guest()
      : id = &#x27;GUEST_000&#x27;,
        name = &#x27;Guest Visitor&#x27;,
        role = &#x27;VIEWER&#x27;,
        isActive = true;

  // Named constructor for administrative accounts
  const UserEntity.admin({required String adminId, required String adminName})
      : id = adminId,
        name = adminName,
        role = &#x27;SUPER_ADMIN&#x27;,
        isActive = true;

  void display() {
    print(&#x27;User: &#x27; + name + &#x27; | Role: &#x27; + role + &#x27; | ID: &#x27; + id);
  }
}

void main() {
  const member = UserEntity(id: &#x27;USR_101&#x27;, name: &#x27;Rohan Sharma&#x27;, role: &#x27;DEVELOPER&#x27;);
  const guest = UserEntity.guest();
  const admin = UserEntity.admin(adminId: &#x27;ADM_001&#x27;, adminName: &#x27;Pooja Verma&#x27;);

  member.display();
  guest.display();
  admin.display();
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: Immutable CopyWith Pattern for State Transitions</h3>
<p class="text-slate-600 mb-3">Implementing the standard Flutter copyWith method to create modified copies of immutable models safely.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: Immutable CopyWith Pattern for State Transitions</span>
        <span class="text-xs text-slate-400 font-mono">lib/copy_with_pattern.dart</span>
    </div>
    <pre><code class="language-flutter">class AccountSettings {
  final String username;
  final bool isDarkMode;
  final bool emailNotifications;
  final String preferredLanguage;

  const AccountSettings({
    required this.username,
    required this.isDarkMode,
    required this.emailNotifications,
    required this.preferredLanguage,
  });

  AccountSettings copyWith({
    String? username,
    bool? isDarkMode,
    bool? emailNotifications,
    String? preferredLanguage,
  }) {
    return AccountSettings(
      username: username ?? this.username,
      isDarkMode: isDarkMode ?? this.isDarkMode,
      emailNotifications: emailNotifications ?? this.emailNotifications,
      preferredLanguage: preferredLanguage ?? this.preferredLanguage,
    );
  }

  @override
  String toString() {
    return &#x27;Settings(user: $username, dark: $isDarkMode, notify: $emailNotifications, lang: $preferredLanguage)&#x27;;
  }
}

void main() {
  const initial = AccountSettings(
    username: &#x27;alex_flutter&#x27;,
    isDarkMode: false,
    emailNotifications: true,
    preferredLanguage: &#x27;en_US&#x27;,
  );
  print(&#x27;Initial State: &#x27; + initial.toString());

  // User toggles Dark Mode on:
  final updated = initial.copyWith(isDarkMode: true, preferredLanguage: &#x27;hi_IN&#x27;);
  print(&#x27;Updated State: &#x27; + updated.toString());
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: Factory Constructor & Cache Management</h3>
<p class="text-slate-600 mb-3">Using factory constructors to implement the Singleton pattern and return cached instances to conserve memory.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: Factory Constructor & Cache Management</span>
        <span class="text-xs text-slate-400 font-mono">lib/api_service_factory.dart</span>
    </div>
    <pre><code class="language-flutter">class ApiService {
  final String baseUrl;
  static final Map&lt;String, ApiService&gt; _cache = {};

  // Private generative constructor
  ApiService._internal(this.baseUrl);

  // Factory constructor that returns cached instance
  factory ApiService(String baseUrl) {
    if (_cache.containsKey(baseUrl)) {
      print(&#x27;[CACHE HIT] Reusing existing ApiService instance for: &#x27; + baseUrl);
      return _cache[baseUrl]!;
    } else {
      print(&#x27;[CACHE MISS] Instantiating new ApiService for: &#x27; + baseUrl);
      final newService = ApiService._internal(baseUrl);
      _cache[baseUrl] = newService;
      return newService;
    }
  }

  void executeGet(String endpoint) {
    print(&#x27;GET -&gt; &#x27; + baseUrl + endpoint);
  }
}

void main() {
  final client1 = ApiService(&#x27;https://api.nectradigital.com/v1&#x27;);
  final client2 = ApiService(&#x27;https://api.nectradigital.com/v1&#x27;); // Cached

  client1.executeGet(&#x27;/users&#x27;);
  print(&#x27;Identical instance? &#x27; + identical(client1, client2).toString());
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;

$content5 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Dart provides multiple mechanisms for code reuse and contract enforcement: <strong>Mixins</strong> (using <code>with</code>), <strong>Abstract Classes</strong> (using <code>implements</code> or <code>extends</code>), and <strong>Enhanced Enums</strong> (introduced in Dart 2.17). These form the backbone of scalable Flutter architectures.</p>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: Mixins for Reusable Feature Composition</h3>
<p class="text-slate-600 mb-3">Composing behavior without deep inheritance hierarchies using Dart mixins on state classes.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: Mixins for Reusable Feature Composition</span>
        <span class="text-xs text-slate-400 font-mono">lib/mixins_demo.dart</span>
    </div>
    <pre><code class="language-flutter">mixin ValidationMixin {
  bool isValidEmail(String email) {
    return email.contains(&#x27;@&#x27;) &amp;&amp; email.contains(&#x27;.&#x27;);
  }

  bool isStrongPassword(String password) {
    return password.length &gt;= 8 &amp;&amp; RegExp(r&#x27;[0-9]&#x27;).hasMatch(password);
  }
}

mixin AuditLogMixin {
  void logAction(String action) {
    print(&#x27;[AUDIT &#x27; + DateTime.now().toIso8601String() + &#x27;]: &#x27; + action);
  }
}

class UserRegistrationController with ValidationMixin, AuditLogMixin {
  void register(String email, String password) {
    if (!isValidEmail(email)) {
      print(&#x27;Registration Failed: Invalid Email format&#x27;);
      return;
    }
    if (!isStrongPassword(password)) {
      print(&#x27;Registration Failed: Password must be 8+ chars and contain numbers&#x27;);
      return;
    }
    logAction(&#x27;User registered successfully with email: &#x27; + email);
  }
}

void main() {
  final controller = UserRegistrationController();
  controller.register(&#x27;invalid-email&#x27;, &#x27;123&#x27;);
  controller.register(&#x27;student@nectradigital.com&#x27;, &#x27;SecurePass2026&#x27;);
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: Abstract Classes & Interface Segregation</h3>
<p class="text-slate-600 mb-3">Creating clean domain boundaries using abstract classes as contracts for authentication and repositories.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: Abstract Classes & Interface Segregation</span>
        <span class="text-xs text-slate-400 font-mono">lib/auth_repository_interface.dart</span>
    </div>
    <pre><code class="language-flutter">abstract class IAuthRepository {
  Future&lt;String&gt; login(String email, String password);
  Future&lt;void&gt; logout();
  bool get isAuthenticated;
}

class MockAuthRepository implements IAuthRepository {
  bool _loggedIn = false;

  @override
  bool get isAuthenticated =&gt; _loggedIn;

  @override
  Future&lt;String&gt; login(String email, String password) async {
    await Future.delayed(const Duration(milliseconds: 200));
    _loggedIn = true;
    return &#x27;AUTH_TOKEN_NECTRA_SECURE_XYZ&#x27;;
  }

  @override
  Future&lt;void&gt; logout() async {
    _loggedIn = false;
    print(&#x27;User logged out successfully.&#x27;);
  }
}

void main() async {
  final IAuthRepository authRepo = MockAuthRepository();
  print(&#x27;Initial Auth Status: &#x27; + authRepo.isAuthenticated.toString());

  final token = await authRepo.login(&#x27;dev@nectradigital.com&#x27;, &#x27;Pass1234&#x27;);
  print(&#x27;Received Token: &#x27; + token);
  print(&#x27;Logged In Status: &#x27; + authRepo.isAuthenticated.toString());
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: Enhanced Enums with Custom Fields & Methods</h3>
<p class="text-slate-600 mb-3">Using modern Dart enhanced enums with members, constructors, and pattern matching for payment statuses.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: Enhanced Enums with Custom Fields & Methods</span>
        <span class="text-xs text-slate-400 font-mono">lib/payment_status_enum.dart</span>
    </div>
    <pre><code class="language-flutter">enum PaymentStatus {
  pending(label: &#x27;Awaiting Payment&#x27;, colorHex: 0xFFF59E0B),
  processing(label: &#x27;Payment In Process&#x27;, colorHex: 0xFF3B82F6),
  completed(label: &#x27;Transaction Succeeded&#x27;, colorHex: 0xFF10B981),
  failed(label: &#x27;Payment Declined&#x27;, colorHex: 0xFFEF4444);

  final String label;
  final int colorHex;

  const PaymentStatus({required this.label, required this.colorHex});

  bool get isTerminal =&gt; this == PaymentStatus.completed || this == PaymentStatus.failed;

  String getBadgeDescription() {
    return &#x27;[$label] - &#x27; + (isTerminal ? &#x27;Final State&#x27; : &#x27;Pending Action&#x27;);
  }
}

void main() {
  const current = PaymentStatus.processing;
  print(&#x27;Status Label: &#x27; + current.label);
  print(&#x27;Badge: &#x27; + current.getBadgeDescription());

  const success = PaymentStatus.completed;
  print(&#x27;Success is Terminal? &#x27; + success.isTerminal.toString());
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;

$content6 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Dart runs in a <strong>single-threaded Event Loop</strong> with two distinct priority queues: the <strong>Microtask Queue</strong> and the <strong>Event Queue</strong>. Understanding how <code>Future</code>, <code>async/await</code>, and timers interact with the event loop is crucial to avoiding UI freezes.</p>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: Event Loop Mechanics: Microtask vs. Event Queue</h3>
<p class="text-slate-600 mb-3">Visualizing execution order between synchronous code, microtasks, and delayed events.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: Event Loop Mechanics: Microtask vs. Event Queue</span>
        <span class="text-xs text-slate-400 font-mono">lib/event_loop_demo.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;dart:async&#x27;;

void main() {
  print(&#x27;1. Synchronous Main Start&#x27;);

  // Event Queue
  Timer.run(() {
    print(&#x27;4. Event Queue: Timer callback executed&#x27;);
  });

  // Microtask Queue (Higher priority than Event Queue)
  scheduleMicrotask(() {
    print(&#x27;3. Microtask Queue: Executed before any Event Queue item&#x27;);
  });

  // Future (Queued in Event Queue)
  Future(() {
    print(&#x27;5. Event Queue: Future task executed&#x27;);
  });

  print(&#x27;2. Synchronous Main End&#x27;);
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: Async/Await with Robust Error Handling & Timeout</h3>
<p class="text-slate-600 mb-3">Executing network-like asynchronous operations safely with timeout constraints and specific error catching.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: Async/Await with Robust Error Handling & Timeout</span>
        <span class="text-xs text-slate-400 font-mono">lib/safe_async_fetch.dart</span>
    </div>
    <pre><code class="language-flutter">Future&lt;String&gt; fetchUserData({required bool simulateFailure}) async {
  await Future.delayed(const Duration(milliseconds: 300));
  if (simulateFailure) {
    throw Exception(&#x27;Server Timeout: Gateway 504&#x27;);
  }
  return &#x27;{&quot;userId&quot;: &quot;USR_2026&quot;, &quot;name&quot;: &quot;Deepika Padukone&quot;, &quot;tier&quot;: &quot;Gold&quot;}&#x27;;
}

void main() async {
  print(&#x27;Initiating fetch operation...&#x27;);

  try {
    final result = await fetchUserData(simulateFailure: false)
        .timeout(const Duration(seconds: 2));
    print(&#x27;Success Response: &#x27; + result);
  } catch (err) {
    print(&#x27;Caught Error: &#x27; + err.toString());
  }

  print(&#x27;Initiating second fetch with failure...&#x27;);
  try {
    await fetchUserData(simulateFailure: true);
  } catch (err) {
    print(&#x27;Defensively Caught: &#x27; + err.toString());
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: Parallel Asynchronous Execution with Future.wait</h3>
<p class="text-slate-600 mb-3">Running multiple independent asynchronous tasks concurrently to prevent sequential waterfall bottlenecks.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: Parallel Asynchronous Execution with Future.wait</span>
        <span class="text-xs text-slate-400 font-mono">lib/future_wait_demo.dart</span>
    </div>
    <pre><code class="language-flutter">Future&lt;String&gt; loadUserProfile() async {
  await Future.delayed(const Duration(milliseconds: 200));
  return &#x27;Profile Data Loaded&#x27;;
}

Future&lt;int&gt; loadUnreadNotifications() async {
  await Future.delayed(const Duration(milliseconds: 150));
  return 7;
}

Future&lt;double&gt; loadAccountBalance() async {
  await Future.delayed(const Duration(milliseconds: 250));
  return 14250.00;
}

void main() async {
  final stopwatch = Stopwatch()..start();
  print(&#x27;Loading dashboard components in parallel...&#x27;);

  // Future.wait fires all 3 asynchronous tasks concurrently!
  final results = await Future.wait([
    loadUserProfile(),
    loadUnreadNotifications(),
    loadAccountBalance(),
  ]);

  stopwatch.stop();
  print(&#x27;All 3 data sources completed in: &#x27; + stopwatch.elapsedMilliseconds.toString() + &#x27;ms&#x27;);
  print(&#x27;User: &#x27; + results[0].toString());
  print(&#x27;Unread Count: &#x27; + results[1].toString());
  print(&#x27;Balance: $&#x27; + (results[2] as double).toStringAsFixed(2));
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;

$content7 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">A <strong>Stream</strong> is a sequence of asynchronous events. In Flutter, streams power real-time data architectures such as Firebase listeners, WebSockets, sensor feeds, and BLoC state management.</p>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: Single-Subscription Stream with async* and yield</h3>
<p class="text-slate-600 mb-3">Creating an asynchronous number generator stream using async* and yield.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: Single-Subscription Stream with async* and yield</span>
        <span class="text-xs text-slate-400 font-mono">lib/stream_generator.dart</span>
    </div>
    <pre><code class="language-flutter">Stream&lt;int&gt; countdownTimer(int start) async* {
  for (int i = start; i &gt;= 0; i--) {
    await Future.delayed(const Duration(milliseconds: 200));
    yield i; // Emit next stream item
  }
}

void main() async {
  print(&#x27;Starting Stream Subscription...&#x27;);
  final stream = countdownTimer(5);

  await for (final value in stream) {
    print(&#x27;Stream Value Received: &#x27; + value.toString());
  }
  print(&#x27;Stream Finished (Closed).&#x27;);
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: StreamController & Broadcast Multi-Listener Stream</h3>
<p class="text-slate-600 mb-3">Managing a broadcast stream where multiple independent listeners receive live market updates.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: StreamController & Broadcast Multi-Listener Stream</span>
        <span class="text-xs text-slate-400 font-mono">lib/broadcast_stream.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;dart:async&#x27;;

void main() {
  // Broadcast stream allows multiple independent listeners
  final controller = StreamController&lt;double&gt;.broadcast();

  // Listener 1: UI Widget Display
  final sub1 = controller.stream.listen((price) {
    print(&#x27;[UI Display Widget]: Live BTC Price: $&#x27; + price.toStringAsFixed(2));
  });

  // Listener 2: Audit &amp; Alert System
  final sub2 = controller.stream.listen((price) {
    if (price &gt; 65000.0) {
      print(&#x27;[ALERT SYSTEM]: High Volatility Threshold Exceeded!&#x27;);
    }
  });

  // Emitting stream events
  controller.add(64200.50);
  controller.add(65450.00);
  controller.add(64800.25);

  // Clean disposal
  Future.delayed(const Duration(milliseconds: 300), () {
    sub1.cancel();
    sub2.cancel();
    controller.close();
    print(&#x27;All subscriptions cancelled and controller closed.&#x27;);
  });
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: Stream Transformations & Filtering</h3>
<p class="text-slate-600 mb-3">Applying functional reactive operators (where, map, take) to transform high-frequency data streams.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: Stream Transformations & Filtering</span>
        <span class="text-xs text-slate-400 font-mono">lib/stream_pipeline.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;dart:async&#x27;;

void main() async {
  final numbers = Stream.fromIterable([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

  print(&#x27;Filtering and transforming stream...&#x27;);
  final transformedStream = numbers
      .where((n) =&gt; n % 2 == 0) // Keep only even numbers
      .map((n) =&gt; &#x27;Item #&#x27; + n.toString() + &#x27; (Squared: &#x27; + (n * n).toString() + &#x27;)&#x27;)
      .take(3); // Take first 3 matching items

  await for (final item in transformedStream) {
    print(&#x27;Transformed Result: &#x27; + item);
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;

$content8 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">The <code>pubspec.yaml</code> file is the central manifest of every Flutter project. It declares dependencies from <strong>pub.dev</strong>, dev dependencies (like code generators), SDK constraints, and assets such as images and custom fonts.</p>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: Production pubspec.yaml Manifest</h3>
<p class="text-slate-600 mb-3">Annotated production pubspec.yaml detailing strict version constraints and asset configurations.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: Production pubspec.yaml Manifest</span>
        <span class="text-xs text-slate-400 font-mono">pubspec.yaml</span>
    </div>
    <pre><code class="language-flutter">name: nectra_store
description: &quot;Production E-Commerce Flutter Application&quot;
publish_to: &quot;none&quot;
version: 1.0.0+1

environment:
  sdk: &quot;&gt;=3.2.0 &lt;4.0.0&quot;
  flutter: &quot;&gt;=3.16.0&quot;

dependencies:
  flutter:
    sdk: flutter
  # State Management &amp; Routing
  provider: ^6.1.1
  go_router: ^13.2.0
  # Networking &amp; Serialization
  http: ^1.2.0
  cached_network_image: ^3.3.1
  # Local Storage
  shared_preferences: ^2.2.2
  sqflite: ^2.3.2

dev_dependencies:
  flutter_test:
    sdk: flutter
  flutter_lints: ^3.0.0

flutter:
  uses-material-design: true
  assets:
    - assets/images/
    - assets/icons/</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: Reading Project Metadata & Package Configuration</h3>
<p class="text-slate-600 mb-3">Inspecting package versioning constraints and asset loading in application code.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: Reading Project Metadata & Package Configuration</span>
        <span class="text-xs text-slate-400 font-mono">lib/app_config.dart</span>
    </div>
    <pre><code class="language-flutter">class AppConfig {
  static const String appName = &#x27;Nectra Store&#x27;;
  static const String appVersion = &#x27;1.0.0+1&#x27;;
  static const String apiBaseUrl = &#x27;https://api.nectradigital.com/v1&#x27;;

  static const List&lt;String&gt; requiredAssets = [
    &#x27;assets/images/logo.png&#x27;,
    &#x27;assets/images/banner.png&#x27;,
  ];

  static void printManifest() {
    print(&#x27;=== App Configuration Manifest ===&#x27;);
    print(&#x27;App: &#x27; + appName + &#x27; (v&#x27; + appVersion + &#x27;)&#x27;);
    print(&#x27;API Endpoint: &#x27; + apiBaseUrl);
    print(&#x27;Assets registered: &#x27; + requiredAssets.length.toString());
  }
}

void main() {
  AppConfig.printManifest();
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: Dependency Resolution & Version Constraints Solver</h3>
<p class="text-slate-600 mb-3">Demonstrating semantic versioning rules (Caret syntax ^, exact version, range constraints).</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: Dependency Resolution & Version Constraints Solver</span>
        <span class="text-xs text-slate-400 font-mono">lib/version_solver.dart</span>
    </div>
    <pre><code class="language-flutter">class SemanticVersion {
  final int major;
  final int minor;
  final int patch;

  const SemanticVersion(this.major, this.minor, this.patch);

  bool isCompatibleWithCaret(SemanticVersion target) {
    // Caret ^1.2.0 allows &gt;=1.2.0 &lt;2.0.0
    return target.major == major &amp;&amp; (target.minor &gt; minor || (target.minor == minor &amp;&amp; target.patch &gt;= patch));
  }

  @override
  String toString() =&gt; &#x27;$major.$minor.$patch&#x27;;
}

void main() {
  const base = SemanticVersion(1, 2, 0);
  const patchUpdate = SemanticVersion(1, 2, 4);
  const minorUpdate = SemanticVersion(1, 3, 0);
  const breakingMajor = SemanticVersion(2, 0, 0);

  print(&#x27;^1.2.0 compatible with 1.2.4? &#x27; + base.isCompatibleWithCaret(patchUpdate).toString());
  print(&#x27;^1.2.0 compatible with 1.3.0? &#x27; + base.isCompatibleWithCaret(minorUpdate).toString());
  print(&#x27;^1.2.0 compatible with 2.0.0? &#x27; + base.isCompatibleWithCaret(breakingMajor).toString());
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;

$content9 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700">Flutter achieves high-performance UI rendering through its <strong>Three Trees Architecture</strong>: the lightweight immutable <strong>Widget Tree</strong>, the structural persistent <strong>Element Tree</strong>, and the low-level layout & painting <strong>RenderObject Tree</strong>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">The Three Trees Architecture Breakdown</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Tree Layer</th>
                <th style="width: 25%;">Mutability &amp; Cost</th>
                <th style="width: 25%;">Lifespan</th>
                <th style="width: 25%;">Primary Responsibility</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell">Widget Tree</td>
                <td>Immutable, extremely lightweight blueprint.</td>
                <td>Recreated on every build frame.</td>
                <td>Configures parameters and declares desired UI state.</td>
            </tr>
            <tr>
                <td class="code-cell">Element Tree</td>
                <td>Mutable, holds BuildContext &amp; State.</td>
                <td>Persists across builds while Widget key/runtimeType match.</td>
                <td>Manages lifecycle, state retention, and tree diffing.</td>
            </tr>
            <tr>
                <td class="code-cell">RenderObject Tree</td>
                <td>Heavyweight, manages geometry and pixels.</td>
                <td>Reused across frames whenever possible.</td>
                <td>Calculates layout, sizing constraints, hit testing, and GPU painting.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: Widget Tree Diffing & Element Reuse</h3>
<p class="text-slate-600 mb-3">Demonstrating how the Element tree compares Widget.canUpdate(oldWidget, newWidget) using runtimeType and key.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: Widget Tree Diffing & Element Reuse</span>
        <span class="text-xs text-slate-400 font-mono">lib/tree_diffing.dart</span>
    </div>
    <pre><code class="language-flutter">class MockWidget {
  final String key;
  final String runtimeType;
  final String label;

  const MockWidget(this.key, this.runtimeType, this.label);

  static bool canUpdate(MockWidget oldWidget, MockWidget newWidget) {
    return oldWidget.key == newWidget.key &amp;&amp; oldWidget.runtimeType == newWidget.runtimeType;
  }
}

void main() {
  const w1 = MockWidget(&#x27;btn_1&#x27;, &#x27;ElevatedButton&#x27;, &#x27;Submit Order&#x27;);
  const w2 = MockWidget(&#x27;btn_1&#x27;, &#x27;ElevatedButton&#x27;, &#x27;Processing...&#x27;); // Updated label
  const w3 = MockWidget(&#x27;btn_2&#x27;, &#x27;OutlinedButton&#x27;, &#x27;Cancel&#x27;); // Different type &amp; key

  print(&#x27;Frame 1 to Frame 2: Can update Element? &#x27; + MockWidget.canUpdate(w1, w2).toString());
  print(&#x27;Frame 2 to Frame 3: Can update Element? &#x27; + MockWidget.canUpdate(w2, w3).toString());
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: Custom RenderBox Demonstration</h3>
<p class="text-slate-600 mb-3">Implementing a custom RenderObject that handles layout sizing and direct canvas drawing.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: Custom RenderBox Demonstration</span>
        <span class="text-xs text-slate-400 font-mono">lib/custom_render_box.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;
import &#x27;package:flutter/rendering.dart&#x27;;

void main() {
  runApp(const MaterialApp(
    home: Scaffold(body: Center(child: AccentBoxWidget(color: Colors.emerald))),
  ));
}

class AccentBoxWidget extends LeafRenderObjectWidget {
  final Color color;
  const AccentBoxWidget({super.key, required this.color});

  @override
  RenderObject createRenderObject(BuildContext context) =&gt; RenderAccentBox(color);

  @override
  void updateRenderObject(BuildContext context, RenderAccentBox renderObject) {
    renderObject.boxColor = color;
  }
}

class RenderAccentBox extends RenderBox {
  Color _color;
  RenderAccentBox(this._color);

  set boxColor(Color c) {
    if (_color != c) {
      _color = c;
      markNeedsPaint();
    }
  }

  @override
  void performLayout() {
    size = constraints.constrain(const Size(140, 140));
  }

  @override
  void paint(PaintingContext context, Offset offset) {
    final canvas = context.canvas;
    final paint = Paint()..color = _color;
    canvas.drawRRect(RRect.fromRectAndRadius(offset &amp; size, const Radius.circular(16)), paint);
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: Key Preservation Across Tree Re-ordering</h3>
<p class="text-slate-600 mb-3">Using ValueKey to ensure Element state is correctly preserved when list items are re-ordered.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: Key Preservation Across Tree Re-ordering</span>
        <span class="text-xs text-slate-400 font-mono">lib/reorderable_keys_demo.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

void main() {
  runApp(const MaterialApp(home: ReorderableKeyListDemo()));
}

class ReorderableKeyListDemo extends StatefulWidget {
  const ReorderableKeyListDemo({super.key});

  @override
  State&lt;ReorderableKeyListDemo&gt; createState() =&gt; _ReorderableKeyListDemoState();
}

class _ReorderableKeyListDemoState extends State&lt;ReorderableKeyListDemo&gt; {
  final List&lt;String&gt; _items = [&#x27;Item Alpha&#x27;, &#x27;Item Beta&#x27;, &#x27;Item Gamma&#x27;];

  void _swap() {
    setState(() {
      final first = _items.removeAt(0);
      _items.add(first);
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;ValueKey Element Preservation&#x27;)),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          for (final item in _items)
            ListTile(
              key: ValueKey(item), // Retains state identity in Element Tree
              title: Text(item, style: const TextStyle(fontWeight: FontWeight.bold)),
              tileColor: Colors.grey.shade100,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
            ),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: _swap,
        child: const Icon(Icons.swap_vert),
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

$content10 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><code>BuildContext</code> is the interface to the underlying <code>Element</code> in the Element Tree. It represents the exact location of a Widget in the tree hierarchy and is used for ancestor lookup, themes, media queries, and navigator operations.</p>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: Safe Theme & MediaQuery Context Lookups</h3>
<p class="text-slate-600 mb-3">Accessing inherited theme properties and device screen dimensions via BuildContext.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: Safe Theme & MediaQuery Context Lookups</span>
        <span class="text-xs text-slate-400 font-mono">lib/context_lookups.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

void main() {
  runApp(const MaterialApp(home: ContextLookupDemo()));
}

class ContextLookupDemo extends StatelessWidget {
  const ContextLookupDemo({super.key});

  @override
  Widget build(BuildContext context) {
    // Look up device metrics from MediaQuery Element
    final screenSize = MediaQuery.of(context).size;
    final theme = Theme.of(context);

    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;BuildContext Lookups&#x27;)),
      body: Center(
        child: Container(
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: theme.colorScheme.primaryContainer,
            borderRadius: BorderRadius.circular(16),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                &#x27;Screen Width: &#x27; + screenSize.width.toInt().toString() + &#x27;px&#x27;,
                style: TextStyle(fontWeight: FontWeight.bold, color: theme.colorScheme.onPrimaryContainer),
              ),
              const SizedBox(height: 8),
              Text(
                &#x27;Screen Height: &#x27; + screenSize.height.toInt().toString() + &#x27;px&#x27;,
                style: TextStyle(fontWeight: FontWeight.bold, color: theme.colorScheme.onPrimaryContainer),
              ),
            ],
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

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: Async Gaps & the context.mounted Guard</h3>
<p class="text-slate-600 mb-3">Preventing fatal BuildContext unmounted crashes when using context after await.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: Async Gaps & the context.mounted Guard</span>
        <span class="text-xs text-slate-400 font-mono">lib/async_gap_guard.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

void main() {
  runApp(const MaterialApp(home: AsyncGapDemo()));
}

class AsyncGapDemo extends StatefulWidget {
  const AsyncGapDemo({super.key});

  @override
  State&lt;AsyncGapDemo&gt; createState() =&gt; _AsyncGapDemoState();
}

class _AsyncGapDemoState extends State&lt;AsyncGapDemo&gt; {
  Future&lt;void&gt; _submitData() async {
    // Asynchronous operation takes 1 second
    await Future.delayed(const Duration(seconds: 1));

    // CRITICAL: Check context.mounted before using context across async gaps!
    if (!mounted) return;

    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text(&#x27;Operation completed safely across async gap!&#x27;)),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;Async Gap context.mounted Guard&#x27;)),
      body: Center(
        child: ElevatedButton.icon(
          onPressed: _submitData,
          icon: const Icon(Icons.cloud_upload),
          label: const Text(&#x27;Run Safe Async Operation&#x27;),
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

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: Builder Widget for Deep ScaffoldMessenger Lookup</h3>
<p class="text-slate-600 mb-3">Using Builder to create a new BuildContext subtree beneath Scaffold to prevent 'No ScaffoldMessenger found' errors.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: Builder Widget for Deep ScaffoldMessenger Lookup</span>
        <span class="text-xs text-slate-400 font-mono">lib/builder_context_demo.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

void main() {
  runApp(const MaterialApp(home: BuilderDemoScreen()));
}

class BuilderDemoScreen extends StatelessWidget {
  const BuilderDemoScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;Builder Context Scope&#x27;)),
      // Builder provides a new BuildContext BELOW the Scaffold
      body: Builder(
        builder: (BuildContext innerContext) {
          return Center(
            child: ElevatedButton(
              onPressed: () {
                // innerContext can look UP and find the ScaffoldMessenger!
                ScaffoldMessenger.of(innerContext).showSnackBar(
                  const SnackBar(
                    content: Text(&#x27;SnackBar successfully mounted via innerContext!&#x27;),
                    backgroundColor: Colors.indigo,
                  ),
                );
              },
              child: const Text(&#x27;Show SnackBar via Inner Context&#x27;),
            ),
          );
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

$content11 = <<<'HTML'
<p class="lead text-lg font-medium text-slate-700"><code>StatelessWidget</code> is an immutable blueprint that never changes its configuration once built. <code>StatefulWidget</code> delegates state to a persistent <code>State</code> object with an explicit lifecycle: <code>initState()</code>, <code>didChangeDependencies()</code>, <code>build()</code>, <code>didUpdateWidget()</code>, and <code>dispose()</code>.</p>

<div class="beginner-breakdown mb-8">
    <div class="beginner-breakdown-title">StatefulWidget Lifecycle Pipeline</div>
    <table class="beginner-breakdown-table">
        <thead>
            <tr>
                <th style="width: 25%;">Lifecycle Method</th>
                <th style="width: 25%;">Invocation Timing</th>
                <th style="width: 25%;">BuildContext Availability</th>
                <th style="width: 25%;">Appropriate Logic</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="code-cell"><code>initState()</code></td>
                <td>Called exactly once when State object is created.</td>
                <td>Cannot look up InheritedWidgets safely.</td>
                <td>Initialize controllers, stream listeners, animation tickers.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>didChangeDependencies()</code></td>
                <td>Called immediately after initState &amp; when InheritedWidgets change.</td>
                <td>Safe for InheritedWidget lookups (Theme, MediaQuery).</td>
                <td>Subscribe to context-dependent data providers.</td>
            </tr>
            <tr>
                <td class="code-cell"><code>build()</code></td>
                <td>Called repeatedly on setState() or parent rebuilds.</td>
                <td>Fully active.</td>
                <td>Pure UI declarations only. NEVER put side-effects here!</td>
            </tr>
            <tr>
                <td class="code-cell"><code>dispose()</code></td>
                <td>Called when State object is permanently removed from tree.</td>
                <td>Tree deactivated.</td>
                <td>Dispose controllers, cancel stream subscriptions, avoid memory leaks.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 1: Classic Counter with setState Lifecycle</h3>
<p class="text-slate-600 mb-3">Fundamental StatefulWidget managing an integer counter with setState triggering granular rebuilds.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 1: Classic Counter with setState Lifecycle</span>
        <span class="text-xs text-slate-400 font-mono">lib/counter_lifecycle.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

void main() {
  runApp(const MaterialApp(home: CounterLifecycleDemo()));
}

class CounterLifecycleDemo extends StatefulWidget {
  const CounterLifecycleDemo({super.key});

  @override
  State&lt;CounterLifecycleDemo&gt; createState() =&gt; _CounterLifecycleDemoState();
}

class _CounterLifecycleDemoState extends State&lt;CounterLifecycleDemo&gt; {
  int _counter = 0;

  @override
  void initState() {
    super.initState();
    debugPrint(&#x27;[LIFECYCLE]: initState called. Counter initialized.&#x27;);
  }

  void _increment() {
    setState(() {
      _counter++;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;StatefulWidget Lifecycle&#x27;)),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Text(&#x27;Taps Recorded:&#x27;, style: TextStyle(fontSize: 16, color: Colors.grey)),
            Text(
              &#x27;$_counter&#x27;,
              style: const TextStyle(fontSize: 48, fontWeight: FontWeight.black, color: Colors.indigo),
            ),
          ],
        ),
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: _increment,
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

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 2: Controller Lifecycle & Preventing Memory Leaks in dispose()</h3>
<p class="text-slate-600 mb-3">Properly managing TextEditingController lifecycle to guarantee clean garbage collection on screen pop.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 2: Controller Lifecycle & Preventing Memory Leaks in dispose()</span>
        <span class="text-xs text-slate-400 font-mono">lib/controller_lifecycle.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

void main() {
  runApp(const MaterialApp(home: ControllerLifecycleScreen()));
}

class ControllerLifecycleScreen extends StatefulWidget {
  const ControllerLifecycleScreen({super.key});

  @override
  State&lt;ControllerLifecycleScreen&gt; createState() =&gt; _ControllerLifecycleScreenState();
}

class _ControllerLifecycleScreenState extends State&lt;ControllerLifecycleScreen&gt; {
  late final TextEditingController _textController;

  @override
  void initState() {
    super.initState();
    _textController = TextEditingController(text: &#x27;Initial Form Value&#x27;);
  }

  @override
  void dispose() {
    // MANDATORY: Dispose controllers to prevent persistent memory leaks!
    _textController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;Controller Lifecycle Management&#x27;)),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            TextField(
              controller: _textController,
              decoration: const InputDecoration(
                labelText: &#x27;User Note&#x27;,
                border: OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 16),
            ElevatedButton(
              onPressed: () {
                debugPrint(&#x27;Current Text: &#x27; + _textController.text);
              },
              child: const Text(&#x27;Read Controller Value&#x27;),
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

<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">Example 3: didUpdateWidget Lifecycle for Parent Configuration Changes</h3>
<p class="text-slate-600 mb-3">Responding to parent widget property changes using didUpdateWidget to synchronize state cleanly.</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>Example 3: didUpdateWidget Lifecycle for Parent Configuration Changes</span>
        <span class="text-xs text-slate-400 font-mono">lib/did_update_widget_demo.dart</span>
    </div>
    <pre><code class="language-flutter">import &#x27;package:flutter/material.dart&#x27;;

void main() {
  runApp(const MaterialApp(home: ParentDashboardWidget()));
}

class ParentDashboardWidget extends StatefulWidget {
  const ParentDashboardWidget({super.key});

  @override
  State&lt;ParentDashboardWidget&gt; createState() =&gt; _ParentDashboardWidgetState();
}

class _ParentDashboardWidgetState extends State&lt;ParentDashboardWidget&gt; {
  int _activeStep = 1;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text(&#x27;didUpdateWidget Synchronization&#x27;)),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            StepDisplayChild(step: _activeStep),
            const SizedBox(height: 20),
            ElevatedButton(
              onPressed: () {
                setState(() {
                  _activeStep = (_activeStep % 3) + 1;
                });
              },
              child: const Text(&#x27;Next Workflow Step&#x27;),
            ),
          ],
        ),
      ),
    );
  }
}

class StepDisplayChild extends StatefulWidget {
  final int step;
  const StepDisplayChild({super.key, required this.step});

  @override
  State&lt;StepDisplayChild&gt; createState() =&gt; _StepDisplayChildState();
}

class _StepDisplayChildState extends State&lt;StepDisplayChild&gt; {
  late String _stepStatus;

  @override
  void initState() {
    super.initState();
    _stepStatus = &#x27;Step #&#x27; + widget.step.toString() + &#x27; Initialized&#x27;;
  }

  @override
  void didUpdateWidget(covariant StepDisplayChild oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (oldWidget.step != widget.step) {
      _stepStatus = &#x27;Step Changed from &#x27; + oldWidget.step.toString() + &#x27; to &#x27; + widget.step.toString();
      debugPrint(&#x27;[LIFECYCLE]: didUpdateWidget updated status: &#x27; + _stepStatus);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Text(_stepStatus, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold));
  }
}</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;


insertChapter($pdo, $courseId, 1, 'Flutter & Dart Fundamentals', 'Flutter Ecosystem, Architecture & Impeller Engine', $content1, 'flutter, dart, flutter tutorial, impeller engine, skia vs impeller, flutter architecture, aot vs jit, cross platform mobile development, google flutter');
insertChapter($pdo, $courseId, 2, 'Flutter & Dart Fundamentals', 'Dart Syntax Essentials: Sound Null Safety, Types & Const', $content2, 'dart null safety, sound null safety, dart types, final vs const, dart variables, dart nullable operators, learn dart programming');
insertChapter($pdo, $courseId, 3, 'Flutter & Dart Fundamentals', 'Dart Collections & Modern Control Flow (Spread, Collection-If)', $content3, 'dart collections, dart list, dart map, dart set, collection if, collection for, spread operator dart, flutter declarative ui lists');
insertChapter($pdo, $courseId, 4, 'Flutter & Dart Fundamentals', 'Dart OOP Masterclass: Classes, Named Constructors & Factory Pattern', $content4, 'dart oop, dart constructors, named parameters, factory constructor dart, copyWith pattern, flutter immutable models, dart classes');
insertChapter($pdo, $courseId, 5, 'Flutter & Dart Fundamentals', 'Advanced Dart OOP: Mixins, Abstract Classes & Enhanced Enums', $content5, 'dart mixins, enhanced enums dart, abstract classes dart, implements vs extends vs with, tickerproviderstatemixin, flutter clean architecture oop');
insertChapter($pdo, $courseId, 6, 'Flutter & Dart Fundamentals', 'Dart Asynchronous Programming: Event Loop, Futures & Async/Await', $content6, 'dart async await, dart futures, event loop dart, microtask queue, future delayed, flutter network async programming, error handling dart');
insertChapter($pdo, $courseId, 7, 'Flutter & Dart Fundamentals', 'Dart Streams & Reactive Programming: StreamController & Transformations', $content7, 'dart streams, streamcontroller dart, broadcast stream, reactive programming flutter, streambuilder foundation, stream transformer');
insertChapter($pdo, $courseId, 8, 'Flutter & Dart Fundamentals', 'Flutter Project Architecture & pubspec.yaml Management', $content8, 'pubspec yaml, flutter project structure, flutter dependencies pub dev, flutter assets fonts configuration, android ios directories flutter');
insertChapter($pdo, $courseId, 9, 'Flutter & Dart Fundamentals', 'The Three Trees Architecture: Widget, Element & RenderObject', $content9, 'flutter three trees, widget tree element tree renderobject tree, flutter rendering pipeline, how flutter works internally, setstate diffing flutter');
insertChapter($pdo, $courseId, 10, 'Flutter & Dart Fundamentals', 'The BuildContext Deep Dive: Element Tree Traversal & Async Gaps', $content10, 'buildcontext flutter, what is buildcontext, context mounted async gap, theme of context, mediaquery of context, inheritedwidget lookup');
insertChapter($pdo, $courseId, 11, 'Flutter & Dart Fundamentals', 'StatelessWidget vs. StatefulWidget: The Full Lifecycle Pipeline', $content11, 'statelesswidget vs statefulwidget, flutter lifecycle, initstate, dispose flutter, didchangedependencies, setstate flutter, prevent memory leaks flutter');
