<?php
// seed_flutter_part2.php: Chapters 12 to 23 (with at least 3 examples each)

function fl_box2($title, $subtitle, $filename, $code) {
    $esc = htmlspecialchars(trim($code), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    return <<<HTML
<h3 class="text-xl font-black text-slate-800 mt-8 mb-2">$title</h3>
<p class="text-slate-600 mb-3">$subtitle</p>
<div class="w3-example-box my-6">
    <div class="w3-example-header">
        <span>$title</span>
        <span class="text-xs text-slate-400 font-mono">$filename</span>
    </div>
    <pre><code class="language-flutter">$esc</code></pre>
    <div class="try-act-row flex items-center gap-2 mt-3">
        <button class="w3-btn-try cursor-pointer"><svg class="w-4 h-4 inline-block -mt-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg> Try it Yourself &raquo;</button>
        <button class="w3-btn-copy cursor-pointer">Copy Code</button>
    </div>
</div>
HTML;
}

// -------------------------------------------------------------
// Chapter 12: Fundamental Layout Widgets: Container, Padding, SizedBox & Constraints
// -------------------------------------------------------------
$ex12_1 = fl_box2("Example 1: Container Box Styling with Gradients & Shadows", "Creating modern styled cards using Container decoration, gradients, rounded corners, and drop shadows.", "lib/container_styling.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: ContainerStylingDemo()));

class ContainerStylingDemo extends StatelessWidget {
  const ContainerStylingDemo({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Container Decoration')),
      body: Center(
        child: Container(
          width: 280,
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            gradient: const LinearGradient(
              colors: [Color(0xFF4F46E5), Color(0xFF06B6D4)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.circular(20),
            boxShadow: const [
              BoxShadow(color: Color(0x404F46E5), blurRadius: 20, offset: Offset(0, 10)),
            ],
          ),
          child: const Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('PREMIUM CARD', style: TextStyle(color: Colors.white70, fontSize: 12, fontWeight: FontWeight.bold)),
              SizedBox(height: 8),
              Text('Nectra Cloud Pro', style: TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.black)),
              SizedBox(height: 14),
              Text('Active Tier: Unlimited API Nodes', style: TextStyle(color: Colors.white, fontSize: 13)),
            ],
          ),
        ),
      ),
    );
  }
}
CODE
);

$ex12_2 = fl_box2("Example 2: Precise Dimensional Sizing with SizedBox & ConstrainedBox", "Enforcing strict min/max boundaries using ConstrainedBox and fixed spacers with SizedBox.", "lib/constraints_demo.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: ConstraintsDemoScreen()));

class ConstraintsDemoScreen extends StatelessWidget {
  const ConstraintsDemoScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Box Constraints in Action')),
      body: Center(
        child: ConstrainedBox(
          constraints: const BoxConstraints(
            minWidth: 150,
            maxWidth: 260,
            minHeight: 60,
            maxHeight: 120,
          ),
          child: Container(
            color: Colors.amber.shade100,
            padding: const EdgeInsets.all(12),
            child: const Center(
              child: Text(
                'Bounded by ConstrainedBox min/max rules.',
                textAlign: TextAlign.center,
                style: TextStyle(fontWeight: FontWeight.bold, color: Colors.brown),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
CODE
);

$ex12_3 = fl_box2("Example 3: Fractional Alignment & Floating Status Badges", "Placing status badges and floating avatars accurately using Align and FractionalOffset.", "lib/alignment_badge.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: AlignmentBadgeDemo()));

class AlignmentBadgeDemo extends StatelessWidget {
  const AlignmentBadgeDemo({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Align & Fractional Positioning')),
      body: Center(
        child: Container(
          width: 220,
          height: 140,
          color: Colors.grey.shade200,
          child: const Align(
            alignment: Alignment.bottomRight,
            child: Padding(
              padding: EdgeInsets.all(8.0),
              child: Chip(
                avatar: Icon(Icons.verified, size: 16, color: Colors.white),
                label: Text('Verified', style: TextStyle(color: Colors.white, fontSize: 11)),
                backgroundColor: Colors.emerald,
              ),
            ),
          ),
        ),
      ),
    );
  }
}
CODE
);

$content12 = <<<HTML
<p class="lead text-lg font-medium text-slate-700">Flutter layout operates on a fundamental rule: <strong>Constraints go down. Sizes go up. Parent sets position.</strong> Single-child layout widgets like <code>Container</code>, <code>Padding</code>, <code>SizedBox</code>, <code>Center</code>, and <code>Align</code> translate incoming constraints into tight or loose bounds for their children.</p>
$ex12_1
$ex12_2
$ex12_3
HTML;

// -------------------------------------------------------------
// Chapter 13: Multi-Child Layouts: Row, Column, Expanded, Flexible & Spacer
// -------------------------------------------------------------
$ex13_1 = fl_box2("Example 1: Flex Alignment & Spacers", "Distributing elements evenly across rows with MainAxisAlignment.spaceBetween and Spacer.", "lib/row_alignment.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: RowAlignmentDemo()));

class RowAlignmentDemo extends StatelessWidget {
  const RowAlignmentDemo({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Row Alignment & Spacers')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: Colors.grey.shade300),
          ),
          child: const Row(
            children: [
              Icon(Icons.account_balance_wallet, color: Colors.indigo),
              SizedBox(width: 12),
              Text('Available Balance', style: TextStyle(fontWeight: FontWeight.w600)),
              Spacer(),
              Text('$4,850.00', style: TextStyle(fontWeight: FontWeight.black, color: Colors.emerald)),
            ],
          ),
        ),
      ),
    );
  }
}
CODE
);

$ex13_2 = fl_box2("Example 2: Expanded vs. Flexible: Solving RenderFlex Overflows", "Preventing text clipping and overflow bugs in tight rows using Expanded with flex ratios.", "lib/flex_ratios.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: FlexRatiosDemo()));

class FlexRatiosDemo extends StatelessWidget {
  const FlexRatiosDemo({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Expanded & Flex Distribution')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Row(
          children: [
            Expanded(
              flex: 2,
              child: Container(
                height: 80,
                color: Colors.blue.shade100,
                alignment: Alignment.center,
                child: const Text('Flex: 2 (66% Width)', style: TextStyle(fontWeight: FontWeight.bold)),
              ),
            ),
            const SizedBox(width: 8),
            Expanded(
              flex: 1,
              child: Container(
                height: 80,
                color: Colors.purple.shade100,
                alignment: Alignment.center,
                child: const Text('Flex: 1', style: TextStyle(fontWeight: FontWeight.bold)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
CODE
);

$ex13_3 = fl_box2("Example 3: Complex Multi-Section Item Card", "Composing nested Rows and Columns inside a structured order receipt card.", "lib/order_item_card.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: OrderCardScreen()));

class OrderCardScreen extends StatelessWidget {
  const OrderCardScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Order Summary Card')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Card(
          elevation: 2,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          child: Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text('Order #ORD-9821', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                    Chip(
                      label: Text('Delivered', style: TextStyle(fontSize: 11, color: Colors.white)),
                      backgroundColor: Colors.emerald,
                      padding: EdgeInsets.zero,
                    ),
                  ],
                ),
                const Divider(height: 24),
                const Text('Items: 1x Sony WH-1000XM5, 1x Audio Cable', style: TextStyle(color: Colors.grey)),
                const SizedBox(height: 12),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text('Total Amount:', style: TextStyle(fontWeight: FontWeight.w600)),
                    Text('$398.00', style: TextStyle(fontWeight: FontWeight.black, fontSize: 18, color: Colors.indigo.shade800)),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
CODE
);

$content13 = <<<HTML
<p class="lead text-lg font-medium text-slate-700"><code>Row</code> and <code>Column</code> arrange multiple widgets along the horizontal and vertical axes. Mastering <code>MainAxisAlignment</code>, <code>CrossAxisAlignment</code>, <code>Expanded</code>, and <code>Flexible</code> is the key to preventing the infamous <em>RenderFlex overflowed by xxx pixels</em> error.</p>
$ex13_1
$ex13_2
$ex13_3
HTML;

// -------------------------------------------------------------
// Chapter 14: Layered & Flow Layouts: Stack, Positioned, IndexedStack & Wrap
// -------------------------------------------------------------
$ex14_1 = fl_box2("Example 1: Cart Icon with Floating Notification Badge", "Using Stack and Positioned to mount a live counter badge on an icon button.", "lib/badge_stack.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: BadgeStackDemo()));

class BadgeStackDemo extends StatelessWidget {
  const BadgeStackDemo({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Stack Badging'),
        actions: [
          Padding(
            padding: const EdgeInsets.only(right: 16),
            child: Center(
              child: Stack(
                clipBehavior: Clip.none,
                children: [
                  IconButton(
                    icon: const Icon(Icons.shopping_cart),
                    onPressed: () {},
                  ),
                  Positioned(
                    top: 4,
                    right: 4,
                    child: Container(
                      padding: const EdgeInsets.all(4),
                      decoration: const BoxDecoration(
                        color: Colors.red,
                        shape: BoxShape.circle,
                      ),
                      constraints: const BoxConstraints(minWidth: 18, minHeight: 18),
                      child: const Text(
                        '3',
                        style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold),
                        textAlign: TextAlign.center,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
      body: const Center(child: Text('Inspect the Cart Badge in the AppBar')),
    );
  }
}
CODE
);

$ex14_2 = fl_box2("Example 2: Wrap Widget for Dynamic Filter Chips", "Creating an adaptive tag cloud that flows automatically across multiple lines on smaller viewports.", "lib/wrap_chips.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: WrapChipsDemo()));

class WrapChipsDemo extends StatelessWidget {
  const WrapChipsDemo({super.key});

  @override
  Widget build(BuildContext context) {
    final tags = ['Flutter 3.x', 'Dart OOP', 'Impeller', 'Material 3', 'Riverpod', 'Clean Architecture', 'GoRouter', 'BLoC'];

    return Scaffold(
      appBar: AppBar(title: const Text('Wrap Flow Layout')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Wrap(
          spacing: 8.0,
          runSpacing: 8.0,
          children: [
            for (final tag in tags)
              Chip(
                avatar: const Icon(Icons.tag, size: 16),
                label: Text(tag),
                backgroundColor: Colors.slate.shade100,
              ),
          ],
        ),
      ),
    );
  }
}
CODE
);

$ex14_3 = fl_box2("Example 3: IndexedStack for Zero-Lag Tab Switching", "Using IndexedStack to keep multiple subtrees alive in memory for instant tab switching without re-rendering.", "lib/indexed_stack_tabs.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: IndexedStackScreen()));

class IndexedStackScreen extends StatefulWidget {
  const IndexedStackScreen({super.key});

  @override
  State<IndexedStackScreen> createState() => _IndexedStackScreenState();
}

class _IndexedStackScreenState extends State<IndexedStackScreen> {
  int _currentIndex = 0;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('IndexedStack Tab Memory')),
      body: IndexedStack(
        index: _currentIndex,
        children: const [
          Center(child: Text('Feed Page (State Preserved)', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold))),
          Center(child: Text('Search Page (State Preserved)', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold))),
          Center(child: Text('Profile Page (State Preserved)', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold))),
        ],
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _currentIndex,
        onDestinationSelected: (idx) => setState(() => _currentIndex = idx),
        destinations: const [
          NavigationDestination(icon: Icon(Icons.feed), label: 'Feed'),
          NavigationDestination(icon: Icon(Icons.search), label: 'Search'),
          NavigationDestination(icon: Icon(Icons.person), label: 'Profile'),
        ],
      ),
    );
  }
}
CODE
);

$content14 = <<<HTML
<p class="lead text-lg font-medium text-slate-700"><code>Stack</code> and <code>Positioned</code> place widgets on top of each other along the Z-axis, perfect for notification badges, floating action headers, and hero overlays. <code>Wrap</code> provides flow-layout mechanics when chips or buttons exceed screen width.</p>
$ex14_1
$ex14_2
$ex14_3
HTML;

// -------------------------------------------------------------
// Chapter 15: Scrollable Layouts: ListView, ListView.builder & RefreshIndicator
// -------------------------------------------------------------
$ex15_1 = fl_box2("Example 1: High-Performance ListView.builder with Lazy Virtualization", "Virtualizing 500 items on-demand so only visible items consume GPU and memory resources.", "lib/lazy_list_view.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: LazyListDemo()));

class LazyListDemo extends StatelessWidget {
  const LazyListDemo({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Lazy ListView.builder')),
      body: ListView.builder(
        itemCount: 500,
        itemBuilder: (context, index) {
          return ListTile(
            leading: CircleAvatar(child: Text('${index + 1}')),
            title: Text('Account Transaction #${index + 1001}'),
            subtitle: const Text('Processed via Impeller Pipeline'),
            trailing: const Icon(Icons.chevron_right),
          );
        },
      ),
    );
  }
}
CODE
);

$ex15_2 = fl_box2("Example 2: Pull-to-Refresh with RefreshIndicator", "Triggering asynchronous data refresh when user pulls down from the top of the list.", "lib/pull_to_refresh.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: RefreshDemoScreen()));

class RefreshDemoScreen extends StatefulWidget {
  const RefreshDemoScreen({super.key});

  @override
  State<RefreshDemoScreen> createState() => _RefreshDemoScreenState();
}

class _RefreshDemoScreenState extends State<RefreshDemoScreen> {
  final List<String> _items = ['Invoice #101: $450', 'Invoice #102: $1,200', 'Invoice #103: $89'];

  Future<void> _handleRefresh() async {
    await Future.delayed(const Duration(seconds: 1));
    setState(() {
      _items.insert(0, 'Invoice #${100 + _items.length + 1}: $240 (Fresh)');
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Pull to Refresh')),
      body: RefreshIndicator(
        onRefresh: _handleRefresh,
        child: ListView.separated(
          itemCount: _items.length,
          separatorBuilder: (context, idx) => const Divider(height: 1),
          itemBuilder: (context, idx) {
            return ListTile(
              leading: const Icon(Icons.receipt_long, color: Colors.indigo),
              title: Text(_items[idx]),
            );
          },
        ),
      ),
    );
  }
}
CODE
);

$ex15_3 = fl_box2("Example 3: Dismissible Swipe-to-Delete List Items", "Implementing swipe-to-delete gestures with confirmation backgrounds and undo snackbars.", "lib/dismissible_list.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: DismissibleListDemo()));

class DismissibleListDemo extends StatefulWidget {
  const DismissibleListDemo({super.key});

  @override
  State<DismissibleListDemo> createState() => _DismissibleListDemoState();
}

class _DismissibleListDemoState extends State<DismissibleListDemo> {
  final List<String> _tasks = ['Review Pull Request', 'Submit App to Play Store', 'Update Impeller Benchmarks'];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Swipe to Dismiss')),
      body: ListView.builder(
        itemCount: _tasks.length,
        itemBuilder: (context, index) {
          final item = _tasks[index];
          return Dismissible(
            key: Key(item),
            background: Container(
              color: Colors.red,
              alignment: Alignment.centerRight,
              padding: const EdgeInsets.only(right: 20),
              child: const Icon(Icons.delete, color: Colors.white),
            ),
            onDismissed: (direction) {
              setState(() => _tasks.removeAt(index));
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(content: Text('Removed "$item"')),
              );
            },
            child: ListTile(title: Text(item)),
          );
        },
      ),
    );
  }
}
CODE
);

$content15 = <<<HTML
<p class="lead text-lg font-medium text-slate-700"><code>ListView.builder</code> renders infinite or long lists lazily on-demand, recycling elements as they scroll off-screen. Coupled with <code>RefreshIndicator</code>, it provides the standard pull-to-refresh mobile pattern.</p>
$ex15_1
$ex15_2
$ex15_3
HTML;

// -------------------------------------------------------------
// Chapter 16: Grids & Slivers: GridView.builder, CustomScrollView & SliverAppBar
// -------------------------------------------------------------
$ex16_1 = fl_box2("Example 1: Responsive GridView.builder with SliverGridDelegate", "Building a 2-column product catalog grid with responsive aspect ratios and card styling.", "lib/grid_catalog.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: GridCatalogScreen()));

class GridCatalogScreen extends StatelessWidget {
  const GridCatalogScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('GridView Product Catalog')),
      body: GridView.builder(
        padding: const EdgeInsets.all(12),
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          crossAxisSpacing: 12,
          mainAxisSpacing: 12,
          childAspectRatio: 0.8,
        ),
        itemCount: 6,
        itemBuilder: (context, index) {
          return Card(
            elevation: 1,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
            child: Padding(
              padding: const EdgeInsets.all(12),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.devices, size: 40, color: Colors.indigo),
                  const SizedBox(height: 8),
                  Text('Gadget #${index + 1}', style: const TextStyle(fontWeight: FontWeight.bold)),
                  const SizedBox(height: 4),
                  const Text('$199.00', style: TextStyle(color: Colors.emerald, fontWeight: FontWeight.w600)),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
CODE
);

$ex16_2 = fl_box2("Example 2: Collapsible SliverAppBar with FlexibleSpaceBar", "Creating an expandable image header that smoothly collapses into a pinned toolbar on scroll.", "lib/sliver_collapsible_appbar.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: SliverAppBarDemo()));

class SliverAppBarDemo extends StatelessWidget {
  const SliverAppBarDemo({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: CustomScrollView(
        slivers: [
          SliverAppBar(
            expandedHeight: 200.0,
            floating: false,
            pinned: true,
            flexibleSpace: FlexibleSpaceBar(
              title: const Text('Collapsible SliverAppBar'),
              background: Container(
                decoration: const BoxDecoration(
                  gradient: LinearGradient(colors: [Colors.indigo, Colors.cyan]),
                ),
                child: const Center(child: Icon(Icons.storefront, size: 60, color: Colors.white24)),
              ),
            ),
          ),
          SliverList(
            delegate: SliverChildBuilderDelegate(
              (context, index) => ListTile(
                title: Text('Sliver Item #${index + 1}'),
                subtitle: const Text('Scroll to see header collapse into pinned bar'),
              ),
              childCount: 25,
            ),
          ),
        ],
      ),
    );
  }
}
CODE
);

$ex16_3 = fl_box2("Example 3: Mixed Slivers: Sticky Headers & Grids in CustomScrollView", "Combining SliverToBoxAdapter, SliverGrid, and SliverList inside a single coordinated scroll view.", "lib/mixed_slivers_dashboard.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: MixedSliversScreen()));

class MixedSliversScreen extends StatelessWidget {
  const MixedSliversScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: CustomScrollView(
        slivers: [
          const SliverAppBar(pinned: true, title: Text('Mixed Slivers Dashboard')),
          SliverToBoxAdapter(
            child: Container(
              padding: const EdgeInsets.all(16),
              color: Colors.indigo.shade50,
              child: const Text('Featured Highlights (SliverToBoxAdapter)', style: TextStyle(fontWeight: FontWeight.bold)),
            ),
          ),
          SliverGrid(
            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(crossAxisCount: 3),
            delegate: SliverChildBuilderDelegate(
              (context, idx) => Card(child: Center(child: Text('Slot $idx'))),
              childCount: 6,
            ),
          ),
        ],
      ),
    );
  }
}
CODE
);

$content16 = <<<HTML
<p class="lead text-lg font-medium text-slate-700"><code>GridView.builder</code> creates two-dimensional responsive grids, while <code>CustomScrollView</code> and <strong>Slivers</strong> provide sophisticated scrolling effects like collapsible headers and sticky pinned app bars.</p>
$ex16_1
$ex16_2
$ex16_3
HTML;

// -------------------------------------------------------------
// Chapter 17: Material Design 3 Core: Scaffold, AppBar, Drawer & FAB
// -------------------------------------------------------------
$ex17_1 = fl_box2("Example 1: Material 3 Scaffold with Navigation Drawer", "Configuring an adaptive drawer with user account header and clean route triggers.", "lib/scaffold_drawer.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: ScaffoldDrawerDemo()));

class ScaffoldDrawerDemo extends StatelessWidget {
  const ScaffoldDrawerDemo({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Material 3 Scaffold & Drawer')),
      drawer: Drawer(
        child: ListView(
          padding: EdgeInsets.zero,
          children: const [
            UserAccountsDrawerHeader(
              accountName: Text('Instructor NectraDigital'),
              accountEmail: Text('admin@nectradigital.com'),
              currentAccountPicture: CircleAvatar(child: Text('IN')),
              decoration: BoxDecoration(color: Colors.indigo),
            ),
            ListTile(leading: Icon(Icons.dashboard), title: Text('Dashboard')),
            ListTile(leading: Icon(Icons.settings), title: Text('Settings')),
          ],
        ),
      ),
      body: const Center(child: Text('Swipe from left or tap menu icon to open drawer')),
    );
  }
}
CODE
);

$ex17_2 = fl_box2("Example 2: Extended FloatingActionButton with Scroll Listener", "Shrinking an extended FAB into an icon on scroll down to maximize viewable screen space.", "lib/extended_fab.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: ExtendedFabDemo()));

class ExtendedFabDemo extends StatefulWidget {
  const ExtendedFabDemo({super.key});

  @override
  State<ExtendedFabDemo> createState() => _ExtendedFabDemoState();
}

class _ExtendedFabDemoState extends State<ExtendedFabDemo> {
  bool _isExtended = true;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Extended FloatingActionButton')),
      body: const Center(child: Text('Tap button to toggle FAB extension')),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => setState(() => _isExtended = !_isExtended),
        icon: const Icon(Icons.add),
        label: _isExtended ? const Text('Create New Task') : const SizedBox.shrink(),
      ),
    );
  }
}
CODE
);

$ex17_3 = fl_box2("Example 3: Custom Scaffold with Persistent Bottom Sheet & Floating Footer", "Using bottomSheet and persistent footer buttons for mobile checkout actions.", "lib/persistent_footer.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: PersistentFooterDemo()));

class PersistentFooterDemo extends StatelessWidget {
  const PersistentFooterDemo({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Persistent Footer Buttons')),
      body: const Center(child: Text('Body Content Area')),
      persistentFooterButtons: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text('Subtotal: $49.00', style: TextStyle(fontWeight: FontWeight.bold)),
            ElevatedButton(
              onPressed: () {},
              style: ElevatedButton.styleFrom(backgroundColor: Colors.emerald, foregroundColor: Colors.white),
              child: const Text('Checkout Now'),
            ),
          ],
        ),
      ],
    );
  }
}
CODE
);

$content17 = <<<HTML
<p class="lead text-lg font-medium text-slate-700"><code>Scaffold</code> is the foundational visual structural layout widget for Material 3. It provides built-in slots for the <code>AppBar</code>, <code>Drawer</code>, <code>FloatingActionButton</code>, <code>BottomNavigationBar</code>, and <code>SnackBar</code>.</p>
$ex17_1
$ex17_2
$ex17_3
HTML;

// -------------------------------------------------------------
// Chapter 18: Modern Navigation Bars: NavigationBar (M3), Destinations & Badges
// -------------------------------------------------------------
$ex18_1 = fl_box2("Example 1: M3 NavigationBar with Active Indicator", "Implementing modern M3 navigation bar with NavigationDestination widgets.", "lib/m3_nav_bar.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: M3NavDemoScreen()));

class M3NavDemoScreen extends StatefulWidget {
  const M3NavDemoScreen({super.key});

  @override
  State<M3NavDemoScreen> createState() => _M3NavDemoScreenState();
}

class _M3NavDemoScreenState extends State<M3NavDemoScreen> {
  int _selectedTab = 0;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Material 3 NavigationBar')),
      body: Center(child: Text('Selected Index: $_selectedTab', style: const TextStyle(fontSize: 20))),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _selectedTab,
        onDestinationSelected: (idx) => setState(() => _selectedTab = idx),
        destinations: const [
          NavigationDestination(icon: Icon(Icons.home_outlined), selectedIcon: Icon(Icons.home), label: 'Home'),
          NavigationDestination(icon: Icon(Icons.explore_outlined), selectedIcon: Icon(Icons.explore), label: 'Explore'),
          NavigationDestination(icon: Icon(Icons.bookmark_border), selectedIcon: Icon(Icons.bookmark), label: 'Saved'),
        ],
      ),
    );
  }
}
CODE
);

$ex18_2 = fl_box2("Example 2: Navigation Destination Badging (Count & Dot)", "Attaching dynamic unread counts and status dots to NavigationDestination icons.", "lib/nav_badging.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: NavBadgingDemo()));

class NavBadgingDemo extends StatelessWidget {
  const NavBadgingDemo({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Navigation Badging')),
      bottomNavigationBar: NavigationBar(
        selectedIndex: 1,
        onDestinationSelected: (i) {},
        destinations: const [
          NavigationDestination(icon: Icon(Icons.home), label: 'Home'),
          NavigationDestination(
            icon: Badge(label: Text('5'), child: Icon(Icons.notifications)),
            label: 'Alerts',
          ),
          NavigationDestination(
            icon: Badge(isLabelVisible: true, child: Icon(Icons.chat)),
            label: 'Messages',
          ),
        ],
      ),
    );
  }
}
CODE
);

$ex18_3 = fl_box2("Example 3: Adaptive Navigation: NavigationRail for Tablets & Desktop", "Switching between bottom NavigationBar on mobile and side NavigationRail on wide screens.", "lib/adaptive_nav.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: AdaptiveNavDemo()));

class AdaptiveNavDemo extends StatefulWidget {
  const AdaptiveNavDemo({super.key});

  @override
  State<AdaptiveNavDemo> createState() => _AdaptiveNavDemoState();
}

class _AdaptiveNavDemoState extends State<AdaptiveNavDemo> {
  int _idx = 0;

  @override
  Widget build(BuildContext context) {
    final isWide = MediaQuery.of(context).size.width >= 600;

    return Scaffold(
      appBar: AppBar(title: const Text('Adaptive Navigation')),
      body: Row(
        children: [
          if (isWide)
            NavigationRail(
              selectedIndex: _idx,
              onDestinationSelected: (i) => setState(() => _idx = i),
              labelType: NavigationRailLabelType.all,
              destinations: const [
                NavigationRailDestination(icon: Icon(Icons.home), label: Text('Home')),
                NavigationRailDestination(icon: Icon(Icons.settings), label: Text('Settings')),
              ],
            ),
          Expanded(child: Center(child: Text('Screen Content: Tab $_idx'))),
        ],
      ),
      bottomNavigationBar: isWide
          ? null
          : NavigationBar(
              selectedIndex: _idx,
              onDestinationSelected: (i) => setState(() => _idx = i),
              destinations: const [
                NavigationDestination(icon: Icon(Icons.home), label: 'Home'),
                NavigationDestination(icon: Icon(Icons.settings), label: 'Settings'),
              ],
            ),
    );
  }
}
CODE
);

$content18 = <<<HTML
<p class="lead text-lg font-medium text-slate-700">Material Design 3 introduces <code>NavigationBar</code> (replacing legacy BottomNavigationBar) with pill-shaped active indicators, integrated badges, and strict accessibility standards.</p>
$ex18_1
$ex18_2
$ex18_3
HTML;

// -------------------------------------------------------------
// Chapter 19: Interactive Buttons & Touch Feedback: FilledButton, OutlinedButton & InkWell
// -------------------------------------------------------------
$ex19_1 = fl_box2("Example 1: Material 3 Button Tiers & Visual Hierarchy", "Demonstrating the four primary button variants for primary, secondary, and tertiary actions.", "lib/button_tiers.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: ButtonTiersDemo()));

class ButtonTiersDemo extends StatelessWidget {
  const ButtonTiersDemo({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('M3 Button Tiers')),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            FilledButton(onPressed: () {}, child: const Text('Primary (FilledButton)')),
            const SizedBox(height: 12),
            FilledButton.tonal(onPressed: () {}, child: const Text('Secondary (Tonal Button)')),
            const SizedBox(height: 12),
            OutlinedButton(onPressed: () {}, child: const Text('Tertiary (OutlinedButton)')),
            const SizedBox(height: 12),
            TextButton(onPressed: () {}, child: const Text('Low Priority (TextButton)')),
          ],
        ),
      ),
    );
  }
}
CODE
);

$ex19_2 = fl_box2("Example 2: Custom Card with Material InkWell Ripple", "Wrapping custom cards inside Material and InkWell for authentic, hardware-accelerated ripples.", "lib/inkwell_ripple.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: InkWellDemo()));

class InkWellDemo extends StatelessWidget {
  const InkWellDemo({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('InkWell Touch Ripple')),
      body: Center(
        child: Material(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          elevation: 2,
          child: InkWell(
            borderRadius: BorderRadius.circular(16),
            onTap: () => debugPrint('Tapped card with ripple!'),
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
              child: const Text('Tap Me for Material Ripple', style: TextStyle(fontWeight: FontWeight.bold)),
            ),
          ),
        ),
      ),
    );
  }
}
CODE
);

$ex19_3 = fl_box2("Example 3: Async Button with Loading Spinner State", "Handling button loading states gracefully by replacing the text with a CircularProgressIndicator.", "lib/async_loading_button.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: AsyncButtonDemo()));

class AsyncButtonDemo extends StatefulWidget {
  const AsyncButtonDemo({super.key});

  @override
  State<AsyncButtonDemo> createState() => _AsyncButtonDemoState();
}

class _AsyncButtonDemoState extends State<AsyncButtonDemo> {
  bool _isLoading = false;

  Future<void> _submit() async {
    setState(() => _isLoading = true);
    await Future.delayed(const Duration(seconds: 2));
    if (mounted) setState(() => _isLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Async Loading Button')),
      body: Center(
        child: FilledButton(
          onPressed: _isLoading ? null : _submit,
          child: _isLoading
              ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
              : const Text('Submit Transaction'),
        ),
      ),
    );
  }
}
CODE
);

$content19 = <<<HTML
<p class="lead text-lg font-medium text-slate-700">Material 3 provides four distinct button tiers: <code>FilledButton</code>, <code>FilledButton.tonal</code>, <code>OutlinedButton</code>, and <code>TextButton</code>. For custom components, <code>InkWell</code> delivers the signature Material ripple touch feedback.</p>
$ex19_1
$ex19_2
$ex19_3
HTML;

// -------------------------------------------------------------
// Chapter 20: Forms & Input Validation: TextFormField & GlobalKey
// -------------------------------------------------------------
$ex20_1 = fl_box2("Example 1: Comprehensive Registration Form with Validation", "Validating email, password length, and required fields with instant error feedback.", "lib/registration_form.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: RegistrationFormScreen()));

class RegistrationFormScreen extends StatefulWidget {
  const RegistrationFormScreen({super.key});

  @override
  State<RegistrationFormScreen> createState() => _RegistrationFormScreenState();
}

class _RegistrationFormScreenState extends State<RegistrationFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passController = TextEditingController();

  @override
  void dispose() {
    _emailController.dispose();
    _passController.dispose();
    super.dispose();
  }

  void _submit() {
    if (_formKey.currentState!.validate()) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Form Validated! Registering user...')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Form Validation & GlobalKey')),
      body: Padding(
        padding: const EdgeInsets.all(20.0),
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              TextFormField(
                controller: _emailController,
                decoration: const InputDecoration(labelText: 'Email Address', border: OutlineInputBorder()),
                validator: (val) => (val == null || !val.contains('@')) ? 'Enter a valid email' : null,
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _passController,
                obscureText: true,
                decoration: const InputDecoration(labelText: 'Password', border: OutlineInputBorder()),
                validator: (val) => (val == null || val.length < 6) ? 'Must be 6+ characters' : null,
              ),
              const SizedBox(height: 24),
              ElevatedButton(onPressed: _submit, child: const Text('Register Account')),
            ],
          ),
        ),
      ),
    );
  }
}
CODE
);

$ex20_2 = fl_box2("Example 2: Password Visibility Toggle & Custom Input Decorators", "Adding suffix icons to toggle obscureText and customizing prefix icons and helper text.", "lib/password_toggle_field.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: PasswordToggleScreen()));

class PasswordToggleScreen extends StatefulWidget {
  const PasswordToggleScreen({super.key});

  @override
  State<PasswordToggleScreen> createState() => _PasswordToggleScreenState();
}

class _PasswordToggleScreenState extends State<PasswordToggleScreen> {
  bool _obscure = true;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Password Visibility Toggle')),
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: TextFormField(
          obscureText: _obscure,
          decoration: InputDecoration(
            labelText: 'Master Security Key',
            prefixIcon: const Icon(Icons.lock),
            suffixIcon: IconButton(
              icon: Icon(_obscure ? Icons.visibility : Icons.visibility_off),
              onPressed: () => setState(() => _obscure = !_obscure),
            ),
            border: const OutlineInputBorder(),
          ),
        ),
      ),
    );
  }
}
CODE
);

$ex20_3 = fl_box2("Example 3: Dynamic Field Reset & AutovalidateMode", "Configuring AutovalidateMode.onUserInteraction for real-time validation while typing.", "lib/autovalidate_form.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: AutovalidateFormDemo()));

class AutovalidateFormDemo extends StatelessWidget {
  const AutovalidateFormDemo({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Real-time AutovalidateMode')),
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: TextFormField(
          autovalidateMode: AutovalidateMode.onUserInteraction,
          decoration: const InputDecoration(labelText: 'Username (lowercase only)', border: OutlineInputBorder()),
          validator: (val) {
            if (val == null || val.isEmpty) return 'Username required';
            if (val != val.toLowerCase()) return 'Must be strictly lowercase';
            return null;
          },
        ),
      ),
    );
  }
}
CODE
);

$content20 = <<<HTML
<p class="lead text-lg font-medium text-slate-700">Form handling in Flutter uses <code>Form</code>, <code>TextFormField</code>, and a <code>GlobalKey&lt;FormState&gt;</code> to manage validation logic, field formatting, and synchronous/asynchronous submission pipelines.</p>
$ex20_1
$ex20_2
$ex20_3
HTML;

// -------------------------------------------------------------
// Chapter 21: Dialogs, Bottom Sheets & Snackbars: Modal Overlays in Flutter
// -------------------------------------------------------------
$ex21_1 = fl_box2("Example 1: Confirmation AlertDialog with Async Result", "Displaying an alert dialog that returns a boolean true/false to the calling screen on dismiss.", "lib/alert_dialog_demo.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: AlertDialogScreen()));

class AlertDialogScreen extends StatelessWidget {
  const AlertDialogScreen({super.key});

  Future<void> _showConfirm(BuildContext context) async {
    final bool? confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Cloud Server?'),
        content: const Text('This action cannot be undone. All database records will be erased.'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Cancel')),
          FilledButton(
            style: FilledButton.styleFrom(backgroundColor: Colors.red),
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('Confirm Delete'),
          ),
        ],
      ),
    );

    if (confirmed == true && context.mounted) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Server deleted successfully!')));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Confirmation Dialogs')),
      body: Center(
        child: ElevatedButton(onPressed: () => _showConfirm(context), child: const Text('Show Delete Dialog')),
      ),
    );
  }
}
CODE
);

$ex21_2 = fl_box2("Example 2: Rounded Modal BottomSheet with Action List", "Opening a bottom sheet with handle bar, title, and multiple action options.", "lib/modal_bottom_sheet.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: ModalSheetDemo()));

class ModalSheetDemo extends StatelessWidget {
  const ModalSheetDemo({super.key});

  void _openSheet(BuildContext context) {
    showModalBottomSheet(
      context: context,
      showDragHandle: true,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (ctx) => Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          ListTile(leading: const Icon(Icons.share), title: const Text('Share Project'), onTap: () => Navigator.pop(ctx)),
          ListTile(leading: const Icon(Icons.link), title: const Text('Copy Direct Link'), onTap: () => Navigator.pop(ctx)),
          ListTile(leading: const Icon(Icons.download), title: const Text('Export PDF'), onTap: () => Navigator.pop(ctx)),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Modal BottomSheet')),
      body: Center(child: ElevatedButton(onPressed: () => _openSheet(context), child: const Text('Open Actions Sheet'))),
    );
  }
}
CODE
);

$ex21_3 = fl_box2("Example 3: Custom ScaffoldMessenger Snackbar with Action Callback", "Showing an undoable snackbar using ScaffoldMessenger and SnackBarAction.", "lib/snackbar_action.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: SnackbarDemoScreen()));

class SnackbarDemoScreen extends StatelessWidget {
  const SnackbarDemoScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Snackbar with Action')),
      body: Center(
        child: ElevatedButton(
          onPressed: () {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: const Text('Message archived.'),
                action: SnackBarAction(label: 'UNDO', onPressed: () => debugPrint('Undo executed!')),
              ),
            );
          },
          child: const Text('Archive Message'),
        ),
      ),
    );
  }
}
CODE
);

$content21 = <<<HTML
<p class="lead text-lg font-medium text-slate-700">Modal overlays communicate critical information or offer contextual actions without navigating away. Flutter provides <code>AlertDialog</code>, <code>showModalBottomSheet</code>, and <code>ScaffoldMessenger</code> for snackbars.</p>
$ex21_1
$ex21_2
$ex21_3
HTML;

// -------------------------------------------------------------
// Chapter 22: Theming & Dynamic Colors: Material 3 ColorScheme & Dark Mode
// -------------------------------------------------------------
$ex22_1 = fl_box2("Example 1: Dynamic Material 3 Theming with ColorScheme.fromSeed", "Generating a harmonic 30+ color palette automatically from a single brand seed color.", "lib/m3_theme_seed.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() {
  runApp(MaterialApp(
    theme: ThemeData(
      useMaterial3: true,
      colorScheme: ColorScheme.fromSeed(seedColor: const Color(0xFF6366F1)),
    ),
    home: const ThemePreviewScreen(),
  ));
}

class ThemePreviewScreen extends StatelessWidget {
  const ThemePreviewScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final colors = Theme.of(context).colorScheme;
    return Scaffold(
      appBar: AppBar(title: const Text('M3 Seed Palette')),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(color: colors.primary, width: 140, height: 50, child: Center(child: Text('Primary', style: TextStyle(color: colors.onPrimary)))),
            const SizedBox(height: 8),
            Container(color: colors.secondary, width: 140, height: 50, child: Center(child: Text('Secondary', style: TextStyle(color: colors.onSecondary)))),
            const SizedBox(height: 8),
            Container(color: colors.surfaceVariant, width: 140, height: 50, child: const Center(child: Text('Surface Variant'))),
          ],
        ),
      ),
    );
  }
}
CODE
);

$ex22_2 = fl_box2("Example 2: Light and Dark Theme Switching via ValueNotifier", "Toggling between Light and Dark mode in real time without restarting the application.", "lib/dark_mode_toggle.dart", <<<'CODE'
import 'package:flutter/material.dart';

final ValueNotifier<ThemeMode> themeNotifier = ValueNotifier(ThemeMode.light);

void main() => runApp(const ThemeSwitcherApp());

class ThemeSwitcherApp extends StatelessWidget {
  const ThemeSwitcherApp({super.key});

  @override
  Widget build(BuildContext context) {
    return ValueListenableBuilder<ThemeMode>(
      valueListenable: themeNotifier,
      builder: (context, currentMode, _) {
        return MaterialApp(
          themeMode: currentMode,
          theme: ThemeData(useMaterial3: true, brightness: Brightness.light),
          darkTheme: ThemeData(useMaterial3: true, brightness: Brightness.dark),
          home: const DarkModeHome(),
        );
      },
    );
  }
}

class DarkModeHome extends StatelessWidget {
  const DarkModeHome({super.key});

  @override
  Widget build(BuildContext context) {
    final isDark = themeNotifier.value == ThemeMode.dark;
    return Scaffold(
      appBar: AppBar(title: const Text('Light / Dark Mode Switcher')),
      body: Center(
        child: ElevatedButton.icon(
          onPressed: () => themeNotifier.value = isDark ? ThemeMode.light : ThemeMode.dark,
          icon: Icon(isDark ? Icons.light_mode : Icons.dark_mode),
          label: Text('Switch to ' + (isDark ? 'Light' : 'Dark') + ' Mode'),
        ),
      ),
    );
  }
}
CODE
);

$ex22_3 = fl_box2("Example 3: Typography Scale Customization", "Applying Material 3 type scales (displayLarge, titleMedium, bodySmall) across widgets.", "lib/typography_scale.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: TypographyDemoScreen()));

class TypographyDemoScreen extends StatelessWidget {
  const TypographyDemoScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;
    return Scaffold(
      appBar: AppBar(title: const Text('M3 Typography Scales')),
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('headlineMedium', style: textTheme.headlineMedium),
            const SizedBox(height: 8),
            Text('titleLarge: Article Heading', style: textTheme.titleLarge),
            const SizedBox(height: 8),
            Text('bodyMedium: Primary reading paragraph text with optimized line height.', style: textTheme.bodyMedium),
            const SizedBox(height: 8),
            Text('labelSmall: CAPTION METRIC', style: textTheme.labelSmall),
          ],
        ),
      ),
    );
  }
}
CODE
);

$content22 = <<<HTML
<p class="lead text-lg font-medium text-slate-700">Material Design 3 uses an algorithmic tonal palette generated from a seed color via <code>ColorScheme.fromSeed</code>. Supporting both Light and Dark mode seamlessly requires passing dynamic theme data to MaterialApp.</p>
$ex22_1
$ex22_2
$ex22_3
HTML;

// -------------------------------------------------------------
// Chapter 23: Media, Images & Icons: Assets, Network Loading & Caching
// -------------------------------------------------------------
$ex23_1 = fl_box2("Example 1: Safe Network Image with Loading & Error Handlers", "Handling slow network loading spinners and graceful error placeholders with loadingBuilder.", "lib/safe_network_image.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: SafeImageDemo()));

class SafeImageDemo extends StatelessWidget {
  const SafeImageDemo({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Network Image with Fallback')),
      body: Center(
        child: ClipRRect(
          borderRadius: BorderRadius.circular(16),
          child: Image.network(
            'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400',
            width: 260,
            height: 180,
            fit: BoxFit.cover,
            loadingBuilder: (context, child, progress) {
              if (progress == null) return child;
              return const Center(child: CircularProgressIndicator());
            },
            errorBuilder: (context, error, stackTrace) => Container(
              color: Colors.grey.shade200,
              width: 260,
              height: 180,
              child: const Icon(Icons.broken_image, size: 40, color: Colors.grey),
            ),
          ),
        ),
      ),
    );
  }
}
CODE
);

$ex23_2 = fl_box2("Example 2: CircleAvatar & User Profile Image Masking", "Displaying circular user avatars with background colors and fallback initials.", "lib/avatar_masking.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: AvatarMaskingDemo()));

class AvatarMaskingDemo extends StatelessWidget {
  const AvatarMaskingDemo({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('CircleAvatar & Masking')),
      body: const Center(
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            CircleAvatar(
              radius: 32,
              backgroundColor: Colors.indigo,
              child: Text('ND', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 20)),
            ),
            SizedBox(width: 16),
            CircleAvatar(
              radius: 32,
              backgroundImage: NetworkImage('https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100'),
            ),
          ],
        ),
      ),
    );
  }
}
CODE
);

$ex23_3 = fl_box2("Example 3: Custom Icon Styling & IconTheme", "Applying global and local IconTheme to ensure uniform icon sizes and colors across the app.", "lib/icon_theming.dart", <<<'CODE'
import 'package:flutter/material.dart';

void main() => runApp(const MaterialApp(home: IconThemingDemo()));

class IconThemingDemo extends StatelessWidget {
  const IconThemingDemo({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('IconTheme Configuration')),
      body: Center(
        child: IconTheme(
          data: const IconThemeData(color: Colors.cyan, size: 36),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: const [
              Icon(Icons.bolt),
              SizedBox(width: 16),
              Icon(Icons.speed),
              SizedBox(width: 16),
              Icon(Icons.cloud_done),
            ],
          ),
        ),
      ),
    );
  }
}
CODE
);

$content23 = <<<HTML
<p class="lead text-lg font-medium text-slate-700">Displaying images and icons in Flutter involves local assets (via <code>Image.asset</code>), network images (via <code>Image.network</code>), and caching utilities like <code>cached_network_image</code> with loading builders and error fallbacks.</p>
$ex23_1
$ex23_2
$ex23_3
HTML;

// Inserts
insertChapter($pdo, $courseId, 12, 'Flutter Layout Engine & UI', 'Fundamental Layout Widgets: Container, Padding, SizedBox & Constraints', $content12, 'flutter layout, container flutter, sizedbox, padding flutter, boxdecoration, flutter constraints, align center flutter');
insertChapter($pdo, $courseId, 13, 'Flutter Layout Engine & UI', 'Multi-Child Layouts: Row, Column, Expanded, Flexible & Spacer', $content13, 'row column flutter, expanded vs flexible, renderflex overflow flutter, spacer flutter, mainaxisalignment crossaxisalignment');
insertChapter($pdo, $courseId, 14, 'Flutter Layout Engine & UI', 'Layered & Flow Layouts: Stack, Positioned, IndexedStack & Wrap', $content14, 'stack flutter, positioned flutter, wrap widget flutter, filter chips, badge icon stack, indexedstack flutter');
insertChapter($pdo, $courseId, 15, 'Flutter Layout Engine & UI', 'Scrollable Layouts: ListView, ListView.builder & RefreshIndicator', $content15, 'listview flutter, listview builder, listview separated, refreshindicator pull to refresh, singlechildscrollview, infinite scroll flutter');
insertChapter($pdo, $courseId, 16, 'Flutter Layout Engine & UI', 'Grids & Slivers: GridView.builder, CustomScrollView & SliverAppBar', $content16, 'gridview builder flutter, sliverappbar, customscrollview, slivers in flutter, collapsible appbar flutter, slivergrid');
insertChapter($pdo, $courseId, 17, 'Material Design 3 & Components', 'Material Design 3 Core: Scaffold, AppBar, Drawer & FAB', $content17, 'scaffold flutter, appbar flutter, material 3 flutter, floatingactionbutton, drawer flutter, flutter ui layout scaffold');
insertChapter($pdo, $courseId, 18, 'Material Design 3 & Components', 'Modern Navigation Bars: NavigationBar (M3), Destinations & Badges', $content18, 'navigationbar flutter, bottomnavigationbar vs navigationbar, material 3 navigation destinations, badge flutter, flutter bottom tabs');
insertChapter($pdo, $courseId, 19, 'Material Design 3 & Components', 'Interactive Buttons & Touch Feedback: FilledButton, OutlinedButton & InkWell', $content19, 'buttons flutter, filledbutton flutter, elevatedbutton, inkwell ripple effect, gesturedetector flutter, touch feedback mobile');
insertChapter($pdo, $courseId, 20, 'Material Design 3 & Components', 'Forms & Input Validation: TextFormField & GlobalKey', $content20, 'form validation flutter, textformfield, globalkey formstate, texteditingcontroller, inputdecoration, email validation flutter');
insertChapter($pdo, $courseId, 21, 'Material Design 3 & Components', 'Dialogs, Bottom Sheets & Snackbars: Modal Overlays in Flutter', $content21, 'showmodalbottomsheet, alertdialog flutter, snackbar flutter, scaffoldmessenger, confirmation modal flutter');
insertChapter($pdo, $courseId, 22, 'Material Design 3 & Components', 'Theming & Dynamic Colors: Material 3 ColorScheme & Dark Mode', $content22, 'theming flutter, dark mode flutter, colorscheme fromseed, material 3 theming, dynamic color flutter');
insertChapter($pdo, $courseId, 23, 'Material Design 3 & Components', 'Media, Images & Icons: Assets, Network Loading & Caching', $content23, 'image asset flutter, image network flutter, cachednetworkimage, flutter icons, flutter svg, image loadingbuilder errorbuilder');
