<?php
declare(strict_types=1);

function css_chapters_advanced_c(): array
{
    $f = 'lesson.html';
    $L = static function (array $c) use ($f): array {
        return track_lesson('css', $f, $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };

    return [
        $L([
            'section' => 'Motion and overlays',
            'title' => 'A scroll timeline drives the animation',
            'keywords' => 'css scroll-driven animation, animation-timeline, view timeline',
            'lead' => 'A normal animation runs on a clock. A scroll timeline runs on the scrollbar. animation-timeline: scroll() ties the keyframes to how far the scroller has moved. view() ties them to how far this element has traveled through the screen.',
            'heading' => 'The clock is the scroll position',
            'para' => 'scroll() uses a scroll container. The default looks at the nearest scrollable ancestor, and the document itself scrolls when nothing else does. At the top, the animation is at the start. At the bottom, it is at the end. view() is different: the subject is the element entering and leaving the visible area. animation-range chooses which slice of that journey maps onto the keyframes, such as entry 0% to cover 40%. The animation no longer needs a duration, because the scroll is the duration. Browsers that do not understand animation-timeline ignore it. Keep a readable static state, and put the timeline inside @supports. A person who asked for reduced motion should get the finished state or the start state, not a parallax they cannot turn off.',
            'visual' => tf_line('scrolltl', 'Scroll position is the timeline', ['top', '25%', '50%', '75%', 'end'], [8, 30, 55, 78, 100], 'scroll() maps the scroller. view() maps this element crossing the screen. The keyframes do not watch the clock.'),
            'bullets' => [
                'transform and opacity are the cheap properties to drive. Width and top force layout on every frame.',
                'animation-fill-mode: both holds the from state before the timeline starts and the to state after it ends.',
                'A sticky bar is a good subject for scroll(), because it stays on screen while the timeline moves.',
                'Prefer @supports (animation-timeline: scroll()) so an older browser keeps the element in a finished, readable state.',
            ],
            'rows' => [
                ['scroll()', 'The scroller progress', 'Document or nearest scroller.'],
                ['view()', 'The element crossing the screen', 'Entry, then exit.'],
                ['animation-range', 'Which slice maps to the keyframes', 'entry and cover are the usual names.'],
                ['no duration', 'The scroll is the time', 'A duration fights the timeline.'],
                ['supports', 'The fallback gate', 'Static first, timeline second.'],
                ['reduced motion', 'Turn the link off', 'Show a still state instead.'],
            ],
            'e1' => ['Example 1 — a bar grows as the page scrolls', 'The preview is tall. Scroll it. The green bar is tied to that scroll.', <<<'CSS'
<style>
  body { height: 1200px; margin: 0; font-family: sans-serif; }
  .bar { position: sticky; top: 0; height: 12px; background: #059669; transform-origin: left center; transform: scaleX(1); }
  @supports (animation-timeline: scroll()) {
    .bar { transform: scaleX(0.05); animation: grow linear both; animation-timeline: scroll(); }
  }
  @keyframes grow { to { transform: scaleX(1); } }
</style>
<div class="bar"></div>
<p style="padding:16px">Scroll this preview. The bar follows the scroll, not a timer.</p>
CSS],
            'e2' => ['Example 2 — a card fades in as it enters', 'view() starts the fade when the card approaches. Scroll down to meet it.', <<<'CSS'
<style>
  body { margin: 0; font-family: sans-serif; }
  .spacer { height: 420px; }
  .card { margin: 16px; padding: 16px; background: #dbeafe; opacity: 1; }
  @supports (animation-timeline: view()) {
    .card { opacity: 0.15; animation: arrive linear both; animation-timeline: view(); animation-range: entry 0% cover 30%; }
  }
  @keyframes arrive { to { opacity: 1; } }
</style>
<div class="spacer">Scroll down.</div>
<div class="card">This card arrives with the scroll.</div>
<div class="spacer"></div>
CSS],
            'micro' => [
                'If the bar is already full and never changes, this browser skipped the timeline. The static scaleX(1) outside the support block is the fallback you want on purpose.',
                'scroll(root) names the document. scroll(nearest) names the nearest scroller. A timeline attached to the wrong box looks frozen.',
                'Reduced motion: set animation: none and the final opacity or transform. Do not leave a half-faded card as the only state.',
            ],
            'quiz' => [
                $q('What does animation-timeline: scroll() use as the clock?', 'How far the scroller has moved', 'A two second duration only', 'The font size', 'The z-index'),
                $q('How is view() different from scroll()?', 'view() follows this element through the visible area', 'view() is a media query', 'view() disables scrolling', 'They are the same timeline'),
                $q('Why wrap the timeline in @supports?', 'So a browser that lacks it keeps a readable static state', 'Because keyframes are invalid otherwise', 'To raise specificity', 'To create a layer'),
            ],
            'practice' => 'Build a tall page with a sticky bar driven by scroll(). Then point a second element at view() and write which timeline moves when the element itself enters the screen.',
        ]),
        $L([
            'section' => 'Motion and overlays',
            'title' => '@starting-style and a discrete display change',
            'keywords' => 'css @starting-style, allow-discrete, transition display',
            'lead' => 'display: none has no in-between. A transition used to be unable to fade an element in, because the element was not there on the first frame. @starting-style supplies that first frame. allow-discrete tells display itself that it may transition.',
            'heading' => 'The first frame is a style you write on purpose',
            'para' => 'When an element stops being display: none, the browser looks in @starting-style for the values to transition from. You set opacity: 0 there, and opacity: 1 in the visible rule, and a transition on opacity. display also needs transition: display 0.4s allow-discrete, or the element pops in before the fade. The same pair runs in reverse when the element leaves, if the transition is set on the visible state so it still exists while display is changing. This replaces a lot of keyframe choreography for popovers and notes. It does not animate height: auto. That is the next lesson. And a person who asked for reduced motion should get the display change with no fade.',
            'visual' => tf_flow('startstyle', 'The element appears from a style that was never on screen', [['hidden', 'display none'], ['start', 'opacity 0'], ['shown', 'opacity 1']], '@starting-style is the from-frame. allow-discrete lets display take part in the transition.'),
            'bullets' => [
                'Put the transition on the state that is visible, so the fade-out still has a transition to run.',
                'allow-discrete is a keyword in the transition, next to the duration. It is not a property of its own.',
                '@starting-style rules match the element at the moment it starts rendering. They are not a class you toggle.',
                'If you only change opacity and leave display: none, the element cannot fade, because it is not rendered.',
            ],
            'rows' => [
                ['@starting-style', 'The from-frame', 'Used when rendering begins.'],
                ['allow-discrete', 'Lets display transition', 'Written in the transition value.'],
                ['opacity', 'The visible fade', '0 in the start, 1 when shown.'],
                ['both directions', 'Transition on the visible rule', 'So the exit can run.'],
                ['not height', 'This tool is appearance', 'Unknown height is the next lesson.'],
                ['reduced motion', 'Skip the fade', 'Keep the show and hide.'],
            ],
            'e1' => ['Example 1 — checking the box fades the note in', 'The note is display none until the box is checked. The starting opacity is 0.', <<<'CSS'
<style>
  .note { display: none; opacity: 0; font-family: sans-serif; background: #d1fae5; padding: 12px; margin-top: 8px; }
  #open:checked ~ .note {
    display: block; opacity: 1;
    transition: opacity 0.4s ease, display 0.4s allow-discrete;
  }
  @starting-style {
    #open:checked ~ .note { opacity: 0; }
  }
</style>
<input id="open" type="checkbox">
<label for="open">Show the note</label>
<div class="note">The note fades in instead of popping.</div>
CSS],
            'e2' => ['Example 2 — without a starting frame, it just appears', 'The same display change. There is no from-frame, so there is nothing to fade from.', <<<'CSS'
<style>
  .note { display: none; font-family: sans-serif; background: #dbeafe; padding: 12px; margin-top: 8px; }
  #open:checked ~ .note { display: block; }
</style>
<input id="open" type="checkbox">
<label for="open">Show the note</label>
<div class="note">This one pops. No starting opacity was set.</div>
CSS],
            'micro' => [
                'The checkbox hack is only the demo. A popover or a dialog can use the same starting style when the browser shows it.',
                'transition-behavior: allow-discrete is the older spelling of the same idea. The keyword inside transition is the one to write now.',
                'Test the exit too. If the note vanishes instantly, the transition was not declared on a rule that still matches during the close.',
            ],
            'quiz' => [
                $q('What is @starting-style for?', 'The first frame of an element that is just starting to render', 'A media query', 'The specificity of :where()', 'A font file'),
                $q('What does allow-discrete do here?', 'It lets display take part in the transition', 'It sets opacity to 1', 'It disables the cascade', 'It creates a grid'),
                $q('Why can display: none not fade on its own?', 'The element is not rendered, so there is no in-between', 'Opacity is invalid', 'Checkboxes block CSS', 'The color is too light'),
            ],
            'practice' => 'Fade a note in with @starting-style when a checkbox is checked. Remove the starting block and describe the difference.',
        ]),
        $L([
            'section' => 'Motion and overlays',
            'title' => 'Animating a height you do not know',
            'keywords' => 'css grid 0fr 1fr, interpolate-size, height auto transition',
            'lead' => 'height: auto has no numeric in-between, so a transition to auto jumps. The reliable pattern is a grid row that goes from 0fr to 1fr, with the content in an inner box that hides its overflow. The fraction is a number. The content picks the size.',
            'heading' => 'Animate the track, not the unknown height',
            'para' => 'The outer element is display: grid and grid-template-rows: 0fr. The inner element is overflow: hidden and min-height: 0, so the 0fr track can actually be zero. When you set grid-template-rows: 1fr, the track grows to the content, and the transition on grid-template-rows interpolates the fraction. You never measured the content in pixels. interpolate-size: allow-keywords is the newer direct way: it lets a transition to height: auto have an in-between. Use it when you have checked support, and keep the grid pattern as the one that already works widely. Do not animate height by leaving overflow visible. The 0fr track cannot shrink below the content unless the inner box clips.',
            'visual' => tf_split('heightauto', 'Which value can the transition walk through?', 'a number?', '0fr to 1fr', 'the track animates', 'auto', 'the height jumps', 'The inner box must hide overflow and allow a zero minimum, or 0fr cannot close.'),
            'bullets' => [
                'min-height: 0 on the inner box is required. The default minimum is auto, and the track will not close.',
                'Put the transition on grid-template-rows, not on height.',
                'Padding on the inner box is inside the clipping box, so it disappears with the content. Padding on the outer box stays.',
                'Reduced motion should set the row to 1fr or 0fr with no transition.',
            ],
            'rows' => [
                ['0fr', 'A closed track', 'Zero only if the child can be zero.'],
                ['1fr', 'The content size', 'The open track.'],
                ['inner overflow', 'hidden', 'Lets the track close.'],
                ['min-height: 0', 'Drops the content floor', 'The flex lesson, on a grid child.'],
                ['interpolate-size', 'A direct height: auto path', 'Use with a support test.'],
                ['padding', 'Know which box it is on', 'Inner padding collapses away. Outer padding stays.'],
            ],
            'e1' => ['Example 1 — 0fr to 1fr opens the note', 'Check the box. The row grows to the paragraph. Uncheck it and the row closes.', <<<'CSS'
<style>
  .wrap { display: grid; grid-template-rows: 0fr; transition: grid-template-rows 0.35s ease; }
  #open:checked ~ .wrap { grid-template-rows: 1fr; }
  .inner { overflow: hidden; min-height: 0; font-family: sans-serif; }
  .inner p { margin: 0; background: #d1fae5; padding: 12px; }
</style>
<input id="open" type="checkbox">
<label for="open">Open</label>
<div class="wrap"><div class="inner"><p>The height was never written in pixels. The track grew to this paragraph.</p></div></div>
CSS],
            'e2' => ['Example 2 — allow-keywords on height itself', 'Where this property is supported, the section height can move to auto. The grid pattern in example 1 remains the dependable one.', <<<'CSS'
<style>
  .panel { height: 0; overflow: hidden; interpolate-size: allow-keywords; transition: height 0.35s ease; font-family: sans-serif; }
  #open:checked ~ .panel { height: auto; }
  .panel p { margin: 0; background: #dbeafe; padding: 12px; }
</style>
<input id="open" type="checkbox">
<label for="open">Open</label>
<div class="panel"><p>If this grows smoothly, height: auto was interpolated. If it jumps, use the 0fr pattern.</p></div>
CSS],
            'micro' => [
                'A border on the inner box can leave a 2 pixel sliver when the track is closed. Put the border on the outer box if a closed note must be invisible.',
                'Do not also set a fixed height on the grid. The fraction has to be free to become the content size.',
                'Nested disclosures each need their own inner clipping box. One overflow hidden on a distant parent will clip the wrong thing.',
            ],
            'quiz' => [
                $q('Why does a transition to height: auto usually jump?', 'auto has no numeric in-between', 'Transitions cannot change height', 'Grid forbids height', 'The paragraph is position fixed'),
                $q('What does the 0fr to 1fr pattern animate?', 'The grid track, which is a number, while the content sets how big 1fr is', 'The font size', 'The z-index', 'The HTML order'),
                $q('Why does the inner box need min-height: 0 and overflow hidden?', 'Otherwise the track cannot shrink below the content', 'To set a dark color', 'To create a stacking context only', 'To disable the checkbox'),
            ],
            'practice' => 'Open and close a note with 0fr and 1fr. Remove min-height: 0 and write what the closed state does.',
        ]),
        $L([
            'section' => 'Motion and overlays',
            'title' => 'sibling-index() counts the position in the parent',
            'keywords' => 'css sibling-index, sibling-count, staggered items',
            'lead' => 'sibling-index() is the position of an element among its siblings, starting at 1. sibling-count() is how many siblings share the parent. A staggered delay or a stepped color no longer needs a handwritten nth-child rule for every item.',
            'heading' => 'One rule, a different number on each child',
            'para' => 'The functions are valid inside calc() on the element itself. li { animation-delay: calc(sibling-index() * 60ms); } delays the first item by 60ms, the second by 120ms, and so on. A hue of calc(sibling-index() * 36) walks the color wheel. The count updates when the DOM order changes, which nth-child rules you wrote by hand do not. The older spelling of the same idea is :nth-child(1), :nth-child(2), and a custom property set on each. Keep that spelling as the fallback inside an @supports test if you need the stagger everywhere. The function looks at element siblings. Text nodes do not take an index.',
            'visual' => tf_cells('sibindex', 'Each child reads its own index', [['1', '1'], ['2', '2'], ['3', '3'], ['4', '4']], 'sibling-index() is 1-based. sibling-count() is the same number on every child: how many there are.', [0, 1, 2, 3], 'index'),
            'bullets' => [
                'The index follows DOM order, not visual order. A flex order change does not change sibling-index().',
                'Use it for delay, hue, or a stripe. Do not use it for logic that should be in the HTML, such as a real heading level.',
                'A wrapper around one item makes that item index 1 inside the wrapper. The function does not look through the wrapper.',
                'If every item looks the same, the browser does not implement the function yet. The nth-child fallback still teaches the stagger.',
            ],
            'rows' => [
                ['sibling-index()', '1-based position', 'First child is 1.'],
                ['sibling-count()', 'How many siblings', 'The same value on each of them.'],
                ['calc()', 'Where you may use them', 'Delay, color, size.'],
                ['DOM order', 'The count it uses', 'Not the flex visual order.'],
                ['nth-child', 'The handwritten fallback', 'One rule per item.'],
                ['wrapper', 'Resets the index', 'The function does not see through it.'],
            ],
            'e1' => ['Example 1 — each row takes the next hue', 'Five items, one rule. The hue steps by 36 degrees.', <<<'CSS'
<style>
  ul { list-style: none; padding: 0; margin: 0; width: 180px; font-family: sans-serif; }
  li { padding: 6px 8px; margin-bottom: 4px; background: hsl(calc(sibling-index() * 36) 70% 82%); }
</style>
<ul><li>One</li><li>Two</li><li>Three</li><li>Four</li><li>Five</li></ul>
CSS],
            'e2' => ['Example 2 — the stagger, written the long way as a fallback', 'These delays are what sibling-index() would compute. Use them when the function is missing.', <<<'CSS'
<style>
  div span { display: block; font-family: sans-serif; background: #dbeafe; margin-bottom: 4px; padding: 6px; animation: in 0.4s both; }
  div span:nth-child(1) { animation-delay: 60ms; }
  div span:nth-child(2) { animation-delay: 120ms; }
  div span:nth-child(3) { animation-delay: 180ms; }
  @keyframes in { from { opacity: 0; } to { opacity: 1; } }
</style>
<div><span>One</span><span>Two</span><span>Three</span></div>
CSS],
            'micro' => [
                'animation-delay: calc(sibling-index() * 60ms) is the whole stagger. You do not need a preprocessor loop.',
                'The function is not a selector. You cannot write sibling-index() = 2 as a condition. Use :nth-child(2) for that.',
                'Dynamic lists are the payoff. Adding a sixth item picks up the next index without a stylesheet edit.',
            ],
            'quiz' => [
                $q('What is sibling-index() for the first child?', '1', '0', 'The parent width', 'It is always 2'),
                $q('Does flex order change sibling-index()?', 'No. The index follows DOM order', 'Yes, it follows the visual order', 'Only for grid', 'Only with a flag'),
                $q('What is the fallback when the function is unsupported?', 'A handwritten :nth-child delay or color per item', 'Delete the list', 'Use z-index as the index', 'There is no fallback'),
            ],
            'practice' => 'Color five list items with sibling-index(). Add a sixth item and confirm you did not need a new rule. If the colors match, write down the nth-child version for the first three.',
        ]),
        $L([
            'section' => 'Motion and overlays',
            'title' => 'A view transition names the boxes that move',
            'keywords' => 'css view transitions, view-transition-name, startViewTransition',
            'lead' => 'A view transition snapshots the page, lets you change the DOM, then animates from the old snapshot to the new one. view-transition-name gives a box a stable identity so the old milk card can move into the new tea card instead of cross-fading the whole page.',
            'heading' => 'The name is the identity across the change',
            'para' => 'document.startViewTransition takes a callback that updates the DOM. The browser captures the old state, runs your callback, captures the new state, and plays a transition. An element with view-transition-name: card is matched to the next element with the same name. Names must be unique on the page. The pseudo-elements ::view-transition-old(card) and ::view-transition-new(card) are how you change the animation for that pair. The root name is the whole page. Same-document transitions are the button demo. Cross-document transitions use @view-transition { navigation: auto; } on both pages, for a multi-page site. If startViewTransition is missing, just update the DOM. The feature is an enhancement. Reduced motion should skip the snapshot and update immediately.',
            'visual' => tf_flow('vt', 'Snapshot, change, then play the difference', [['old', 'the snapshot'], ['change', 'the DOM'], ['new', 'the named box']], 'The view-transition-name is how the old box and the new box are recognized as the same thing.'),
            'bullets' => [
                'Two elements must not share a name at the same time. The capture fails or the match is wrong.',
                'The callback should do the DOM change and nothing slow. The snapshot is waiting.',
                '::view-transition-group(card) is the box that moves. old and new are the two pictures inside it.',
                'A named element that disappears and a named element that appears, with the same name, is the morph.',
            ],
            'rows' => [
                ['startViewTransition', 'The script hook', 'Same-document changes.'],
                ['view-transition-name', 'The identity', 'Unique on the page.'],
                ['old()', 'The outgoing picture', 'A pseudo-element.'],
                ['new()', 'The incoming picture', 'A pseudo-element.'],
                ['root', 'The rest of the page', 'Cross-fades if you do not name the parts.'],
                ['navigation: auto', 'Between pages', 'Both documents opt in.'],
            ],
            'e1' => ['Example 1 — the word changes inside a named box', 'Press Swap. Where the API exists, the old word and the new word transition instead of popping.', <<<'CSS'
<style>
  .box { view-transition-name: card; font-family: sans-serif; display: inline-block; padding: 16px 20px; background: #d1fae5; }
  button { font-family: sans-serif; margin-right: 8px; }
</style>
<button id="swap">Swap</button>
<div id="box" class="box">Milk</div>
<script>
document.getElementById("swap").onclick = function () {
  var box = document.getElementById("box");
  var next = box.textContent === "Milk" ? "Tea" : "Milk";
  if (document.startViewTransition) {
    document.startViewTransition(function () { box.textContent = next; });
  } else {
    box.textContent = next;
  }
};
</script>
CSS],
            'e2' => ['Example 2 — a different group animation for that name', 'The default fade is replaced for the card group. The rest of the page is untouched.', <<<'CSS'
<style>
  .box { view-transition-name: card; font-family: sans-serif; display: inline-block; padding: 16px 20px; background: #dbeafe; }
  ::view-transition-group(card) { animation-duration: 0.5s; }
  button { font-family: sans-serif; margin-right: 8px; }
</style>
<button id="swap">Swap</button>
<div id="box" class="box">Milk</div>
<script>
document.getElementById("swap").onclick = function () {
  var box = document.getElementById("box");
  var next = box.textContent === "Milk" ? "Tea" : "Milk";
  if (document.startViewTransition) {
    document.startViewTransition(function () { box.textContent = next; });
  } else {
    box.textContent = next;
  }
};
</script>
CSS],
            'micro' => [
                'If Swap just changes the word with no animation, startViewTransition is missing. The else branch is the correct fallback.',
                'Do not leave a view-transition-name on a list of cards that all render at once. Give the name only to the card that is about to move, or use unique names.',
                'Cross-document transitions need the opt-in on both pages. A named hero that exists on both is the element that travels.',
            ],
            'quiz' => [
                $q('What does view-transition-name identify?', 'The same box before and after the DOM change', 'The font file', 'The cascade layer', 'The container query'),
                $q('What does startViewTransition do?', 'It snapshots, runs your DOM change, then animates between the snapshots', 'It reloads the page', 'It sets display: none', 'It registers a custom property'),
                $q('What is the fallback when the API is missing?', 'Update the DOM immediately', 'Hide the button', 'Refuse the click', 'There is no fallback'),
            ],
            'practice' => 'Name a box and swap its text inside startViewTransition. Then give two boxes the same name at once and write what goes wrong.',
        ]),
        $L([
            'section' => 'Motion and overlays',
            'title' => 'Anchor positioning ties a popup to its button',
            'keywords' => 'css anchor positioning, anchor-name, position-anchor, position-area',
            'lead' => 'A tooltip used to be positioned with a script that read the button rectangle. Anchor positioning does it in CSS. The button gets an anchor-name. The tooltip sets position-anchor to that name and position-area to a side, such as top.',
            'heading' => 'The button is the anchor. The tip picks a side',
            'para' => 'anchor-name: --btn uses a dashed ident, the same family as a custom property name. position-anchor: --btn on the tooltip points at it. position-area: top places the tooltip on that side. If the tip would overflow the screen, position-try-fallbacks can offer the other sides. The tooltip is position: absolute or fixed, and it does not need the button to be a positioned parent. That is the difference from the old absolute-inside-relative pattern. You can still keep that pattern as the fallback: a wrapper with position relative, and the tip at bottom: calc(100% + 8px). Inside @supports (position-anchor: --btn), switch to the anchor. A hidden tip should be closed with popover or dialog semantics, not only with a visual offset. The position is not the accessibility.',
            'visual' => tf_flow('anchor', 'The tip is placed from the button', [['button', 'anchor-name'], ['tip', 'position-anchor'], ['side', 'position-area']], 'The button does not have to be a relative parent. The name is the link. A fallback absolute tip covers browsers that lack the feature.'),
            'bullets' => [
                'Names are dashed idents. --btn is valid. btn is not.',
                'One name should belong to one anchor. A second button with the same name is ambiguous.',
                'position-area values include top, bottom, left, right, and spans. The tip can cover the anchor if you ask for center.',
                'The fallback wrapper is still worth writing. Feature detection belongs in @supports.',
            ],
            'rows' => [
                ['anchor-name', 'Names the button', 'A dashed ident.'],
                ['position-anchor', 'Points the tip at that name', 'Set on the popup.'],
                ['position-area', 'Which side', 'top sits above the button.'],
                ['fallbacks', 'The other sides', 'When the first side overflows.'],
                ['absolute fallback', 'The old pattern', 'A relative wrapper and bottom 100%.'],
                ['semantics', 'popover or dialog', 'Position is not a role.'],
            ],
            'e1' => ['Example 1 — the fallback sits above the button', 'No anchor feature is required. The wrapper is the containing block.', <<<'CSS'
<style>
  .wrap { position: relative; display: inline-block; margin-top: 48px; font-family: sans-serif; }
  .tip { position: absolute; bottom: calc(100% + 8px); left: 0; background: #0f172a; color: white; padding: 6px 8px; }
  button { font-family: sans-serif; }
</style>
<div class="wrap">
  <button>Save</button>
  <div class="tip">Stored</div>
</div>
CSS],
            'e2' => ['Example 2 — the tip anchors to the button by name', 'Where position-anchor is supported, the area is top. The fallback coordinates are cleared inside the support block.', <<<'CSS'
<style>
  .wrap { position: relative; display: inline-block; margin-top: 48px; font-family: sans-serif; }
  .tip { position: absolute; bottom: calc(100% + 8px); left: 0; background: #0f172a; color: white; padding: 6px 8px; }
  button { font-family: sans-serif; }
  @supports (position-anchor: --btn) {
    button { anchor-name: --btn; }
    .tip { position-anchor: --btn; position-area: top; bottom: auto; left: auto; }
  }
</style>
<div class="wrap">
  <button>Save</button>
  <div class="tip">Stored</div>
</div>
CSS],
            'micro' => [
                'If the tip covers the button, position-area is center or the fallback left and bottom are both active. Clear the fallback inside the support block.',
                'A transformed ancestor can still trap a fixed tip. The containing-block lesson still applies.',
                'Hide the tip with a popover or a checkbox that removes it, not by parking it off screen where a keyboard user can still reach a blank.',
            ],
            'quiz' => [
                $q('What does anchor-name do?', 'It names the button so a popup can attach to it', 'It sets the font', 'It creates a grid area', 'It is a media query'),
                $q('Which value places the tip above the anchor?', 'position-area: top', 'float: left', 'z-index: top', 'flex-direction: top'),
                $q('What should you keep for browsers without the feature?', 'A relative wrapper and an absolute tip', 'Nothing, the tip can overlap the page at 0,0 with no fallback', 'A second body element', 'An important flag on body'),
            ],
            'practice' => 'Build the fallback tip, then add the support block that anchors it by name. Write which declaration you had to clear so the two positioning schemes did not both apply.',
        ]),
        $L([
            'section' => 'Motion and overlays',
            'title' => 'The backdrop sits behind the dialog',
            'keywords' => 'css dialog backdrop, ::backdrop, showModal',
            'lead' => 'dialog::backdrop is the layer the browser paints behind a modal dialog, over the rest of the page. It exists when the dialog is opened with showModal. A dialog opened without the modal call has no backdrop, and the page behind it still receives clicks.',
            'heading' => 'The backdrop is a box you can style, and only while the dialog is modal',
            'para' => '::backdrop is a pseudo-element of the dialog. You can set its background, often a partly transparent dark color, and a backdrop-filter if you want the page behind it blurred. You cannot put text in the backdrop. The dialog itself is a separate box, centered by the user agent unless you position it. A form with method="dialog" and a button closes the modal and can return a value. Escape closes it too. The rest of the page is inert while the modal is open, which is the accessibility you must not rebuild out of a div. Style the backdrop so the page looks unavailable. Do not paint the dialog the same color as the backdrop, or the message disappears. A starting style can fade the backdrop in. The top layer is above ordinary z-index, so you do not fight the page with 9999.',
            'visual' => tf_layers('backdrop', 'The modal sits on the top layer', [
                ['page, inert while the modal is open', '#f8fafc', '#64748b'],
                ['::backdrop, the dim layer', '#cbd5e1', '#475569'],
                ['dialog, the message', '#ffffff', '#0284c7'],
            ], 'showModal creates the backdrop and the top layer. A plain div with a high z-index does not make the page inert.'),
            'bullets' => [
                'showModal is the modal path. show() opens the dialog without a backdrop and without trapping focus.',
                '::backdrop does not generate if the dialog is not modal.',
                'The dialog is in the top layer. z-index on the page will not cover it.',
                'Return focus to the button that opened the dialog when it closes. The closed dialog is not a place to leave the cursor.',
            ],
            'rows' => [
                ['showModal()', 'Opens a modal', 'Creates the backdrop.'],
                ['::backdrop', 'The dim layer', 'Style the background, not the text.'],
                ['method="dialog"', 'A close button', 'The form submits by closing.'],
                ['top layer', 'Above z-index', 'You do not race the page.'],
                ['inert page', 'Clicks stay in the dialog', 'The reason to use dialog.'],
                ['show()', 'Non-modal', 'No backdrop. Rare for a warning.'],
            ],
            'e1' => ['Example 1 — a modal with a dim backdrop', 'Open it. The page behind is covered by the backdrop. Close is a dialog form, so it dismisses the modal.', <<<'CSS'
<style>
  dialog { font-family: sans-serif; border: 0; border-radius: 12px; }
  dialog::backdrop { background: rgb(15 23 42 / 0.55); }
  button { font-family: sans-serif; }
</style>
<button id="open">Open</button>
<dialog>
  <p>The note was stored.</p>
  <form method="dialog"><button>Close</button></form>
</dialog>
<script>
document.getElementById("open").onclick = function () {
  document.querySelector("dialog").showModal();
};
</script>
CSS],
            'e2' => ['Example 2 — the backdrop can blur the page', 'The blur is optional. The dim color is what makes the dialog readable.', <<<'CSS'
<style>
  dialog { font-family: sans-serif; border: 0; border-radius: 12px; }
  dialog::backdrop { background: rgb(15 23 42 / 0.35); backdrop-filter: blur(3px); }
  button { font-family: sans-serif; }
</style>
<button id="open">Open</button>
<dialog>
  <p>A softer backdrop. The dialog itself stays sharp.</p>
  <form method="dialog"><button>Close</button></form>
</dialog>
<script>
document.getElementById("open").onclick = function () {
  document.querySelector("dialog").showModal();
};
</script>
CSS],
            'micro' => [
                'A div styled to look like a dialog does not trap focus and does not create a backdrop pseudo-element. Use the element.',
                'Do not put required information only in the backdrop. It cannot hold content.',
                'If the dialog is cut off, check a transformed ancestor. The top layer normally escapes that trap. A filter on the dialog element itself is a different box.',
            ],
            'quiz' => [
                $q('When does dialog::backdrop exist?', 'When the dialog was opened with showModal', 'For every div', 'Only in print', 'When z-index is 1'),
                $q('Why is a real dialog better than a positioned div?', 'The page becomes inert and focus stays in the dialog', 'It always uses a green border', 'It removes the need for CSS', 'It sets a container query'),
                $q('Can the backdrop contain the message text?', 'No. The message belongs in the dialog', 'Yes, with the content property only', 'Yes, it is a normal div', 'Only if it has an ID'),
            ],
            'practice' => 'Open a modal, style the backdrop, and close it with a dialog form. Then try show() instead of showModal and write what the backdrop does.',
        ]),
        $L([
            'section' => 'Motion and overlays',
            'title' => 'Invalid fields, accent color, and appearance',
            'keywords' => 'css user-invalid, accent-color, appearance, form styling',
            'lead' => ':invalid is true as soon as a required field is empty, so the form looks wrong before the person has typed. :user-invalid waits until they have edited it. accent-color tints a checkbox without throwing away the native control. appearance: none throws the native painting away, and then you have to draw the focus ring yourself.',
            'heading' => 'Change the color first. Replace the control only when you must',
            'para' => 'accent-color on a checkbox, a radio, or a range changes the control color and keeps the browser drawing, the hit target, and the focus ring. appearance: none on a checkbox removes the check mark. You then paint a box, and you paint a mark for :checked, and you paint :focus-visible. Most forms should stop at accent-color. :user-invalid and :user-valid reflect interaction. Pair them with a border and a message that is text, not only a red outline. placeholder is not a label. A label element is what gets announced. The red border is extra.',
            'visual' => tf_panels('fields', 'Two different kinds of invalid', ':invalid', ['true immediately', 'an empty required field', 'red before any typing'], ':user-invalid', ['true after editing', 'the person has touched it', 'the kinder border'], 'accent-color keeps the native checkbox. appearance: none means you own the drawing.'),
            'bullets' => [
                'Style :user-invalid for the border. Use :invalid on a form :has() rule if you want the save button dimmed before submit.',
                'accent-color inherits. Set it on the form and the controls pick it up.',
                'appearance: none does not remove the element from the accessibility tree. It only removes the painting.',
                'A custom checkbox that has no :focus-visible style fails the focus lesson the moment you restyle it.',
            ],
            'rows' => [
                [':invalid', 'The constraint failed', 'Includes a pristine required field.'],
                [':user-invalid', 'The person changed it, and it failed', 'The border people should see.'],
                ['accent-color', 'Tints native controls', 'The first tool to reach for.'],
                ['appearance: none', 'Removes native painting', 'You redraw box, mark, and ring.'],
                ['label', 'The accessible name', 'A placeholder is not a label.'],
                ['the message', 'Text, not only color', 'Color is not the error report.'],
            ],
            'e1' => ['Example 1 — the border waits until the field was edited', 'An empty required email is :invalid and not yet :user-invalid. Type, then clear it, and the border appears.', <<<'CSS'
<style>
  label { font-family: sans-serif; display: block; }
  input { padding: 8px; border: 2px solid #cbd5e1; }
  input:user-invalid { border-color: #e11d48; }
</style>
<label>Email <input type="email" required></label>
CSS],
            'e2' => ['Example 2 — accent-color tints the checkbox', 'The browser still draws the control. Only the color changed.', <<<'CSS'
<style>
  label { font-family: sans-serif; accent-color: #059669; }
  input { width: 18px; height: 18px; }
</style>
<label><input type="checkbox" checked> Urgent</label>
CSS],
            'micro' => [
                'autofill styles are a separate pseudo-class, :autofill. A yellow autofill background can cover your border. Set the autofill background on purpose if the contrast fails.',
                'Disabled fields should not look like invalid fields. :disabled is a third state, usually faded, and it is not an error.',
                'If you set appearance: none, test the checkbox with a keyboard and with a screen reader. The role is still there. The ring might not be.',
            ],
            'quiz' => [
                $q('When does :user-invalid become true?', 'After the person has edited the field and the value fails', 'As soon as a required field is empty, before any edit', 'Only on checkboxes', 'Never in current browsers'),
                $q('What does accent-color keep that appearance: none removes?', 'The native painting of the control', 'The label text', 'The form action', 'The cascade layer'),
                $q('Why is a red border not enough for an error?', 'The message has to be text, because color alone is not reported to everyone', 'Borders cannot be red', 'Required fields forbid borders', 'Accent-color clears text'),
            ],
            'practice' => 'Build a required email field that turns red only after it has been edited and cleared. Tint a checkbox with accent-color and leave its native drawing in place.',
        ]),
        $L([
            'section' => 'The project',
            'title' => 'Project: a note card that adapts to its box',
            'keywords' => 'css project, container query card, has selector, cascade layers',
            'lead' => 'This card puts the advanced pieces in one place. A layer keeps the reset under the component. A container query changes the layout from the card width, not the window. :has() marks the card urgent when its checkbox is checked. Reduced motion removes the hover travel and keeps the border.',
            'heading' => 'Each feature has one job on the card',
            'para' => 'The reset layer paints a gray border with a specific element selector. The component layer paints the real card with a class, and it wins because it is the later layer. The wrapper is the container. Under 260 pixels the meta line hides and the card stays a stack. From 260 pixels the title and the meta sit on one row. The urgent checkbox does not add a class to the card. The card looks down with :has(input:checked). The hover lifts the card by 4 pixels. Under prefers-reduced-motion the lift is gone and the border color remains, so the hover is still visible. Nothing here needs a script. If you add a view transition later, the card already has a single root to name.',
            'visual' => tf_flow('notecard', 'Four jobs, one card', [['layer', 'reset stays under'], ['container', 'the width decides'], [':has()', 'checked means urgent'], ['motion', 'the border remains']], 'The window width is not an input. The wrapper width is. The checkbox is state. The layer is the reason the reset loses.'),
            'bullets' => [
                'Do not query the card own width. The frame is the container. The card is the descendant.',
                'The urgent look must exist as a border or text, not only as a lift, so reduced motion still shows it.',
                'Keep the reset in the layer. An unlayered gray rule would beat the component.',
                'The label is the hit target. The checkbox is inside the card so :has() can see it.',
            ],
            'rows' => [
                ['reset layer', 'A loud gray default', 'Early, so it loses.'],
                ['component layer', 'The real card', 'Later, so it wins.'],
                ['container', 'The frame width', '260 pixels is the switch.'],
                [':has(:checked)', 'The urgent border', 'No scripted class.'],
                ['hover lift', '4 pixels', 'Removed when motion is reduced.'],
                ['the label', 'Wraps the control', 'The whole card is the target.'],
            ],
            'e1' => ['Example 1 — a narrow frame stacks and can turn urgent', 'The frame is 220 pixels, so the meta is hidden. Check the box and the border turns red because the card has a checked input.', <<<'CSS'
<style>
  @layer reset, card;
  @layer reset {
    label.note { display: block; border: 2px solid #cbd5e1; color: #64748b; font-family: sans-serif; }
  }
  @layer card {
    .frame { width: 220px; container-type: inline-size; }
    .note { padding: 12px; background: white; color: #0f172a; transition: transform 0.2s ease, border-color 0.2s ease; }
    .note:hover { transform: translateY(-4px); }
    .note:has(input:checked) { border-color: #e11d48; background: #fff1f2; }
    .meta { color: #64748b; }
    @container (max-width: 260px) { .meta { display: none; } }
  }
  @media (prefers-reduced-motion: reduce) {
    .note { transition: none; }
    .note:hover { transform: none; }
  }
</style>
<div class="frame">
  <label class="note"><input type="checkbox"> <strong>Milk</strong><div class="meta">4 pages</div></label>
</div>
CSS],
            'e2' => ['Example 2 — a wider frame shows the row, already urgent', 'The frame is 360 pixels. The meta sits beside the title. The box starts checked, so the urgent border is already there.', <<<'CSS'
<style>
  @layer reset, card;
  @layer card {
    .frame { width: 360px; container-type: inline-size; }
    .note { display: block; padding: 12px; border: 2px solid #cbd5e1; background: white; font-family: sans-serif; color: #0f172a; }
    .note:has(input:checked) { border-color: #e11d48; background: #fff1f2; }
    @container (min-width: 260px) {
      .row { display: flex; justify-content: space-between; gap: 12px; }
    }
  }
</style>
<div class="frame">
  <label class="note">
    <div class="row"><strong>Milk</strong><span>4 pages</span></div>
    <input type="checkbox" checked> Urgent
  </label>
</div>
CSS],
            'micro' => [
                'Change the frame width in the inspector and watch the meta appear. Do not resize the browser and call that a container test.',
                'If the card stays gray, the component rules were left unlayered and something else won, or the layer order was declared backwards.',
                'The next step you can add, without a new layout, is a view-transition-name on the card when it moves between two lists.',
            ],
            'quiz' => [
                $q('What does the card measure to decide the layout?', 'The container frame, not the viewport', 'Only the font size', 'The z-index', 'The number of classes'),
                $q('How does the card know it is urgent?', ':has() sees the checked input inside it', 'A script adds a class that this lesson requires', 'The layer name is urgent', 'The meta line is hidden'),
                $q('What should reduced motion keep?', 'A visible state such as the border, without the travel', 'Nothing at all', 'Only the lift', 'The reset layer color forever'),
            ],
            'practice' => 'Build the narrow card and the wide card. Check the box, resize the frame across 260 pixels, and write which feature was responsible for the border, the row, and the gray reset losing.',
        ]),
    ];
}
