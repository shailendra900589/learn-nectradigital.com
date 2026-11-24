<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/track_seed.php';
require_once __DIR__ . '/includes/teach_figures.php';

function css_course_meta(): array
{
    return [
        'title' => 'CSS3 Tutorial',
        'slug' => 'css-tutorial',
        'description' => 'CSS from the first rule through the box, flex, and grid, then the cascade, layers, container queries, :has(), modern color, scroll-driven motion, view transitions, and a card you can explain.',
        'keywords' => 'css tutorial, css cascade, cascade layers, container queries, css has, css nesting, subgrid, oklch, view transitions, anchor positioning, css advanced',
    ];
}

function css_chapters_advanced(): array
{
    require_once __DIR__ . '/seed_css_advanced_c.php';
    return array_merge(css_chapters_advanced_a(), css_chapters_advanced_b(), css_chapters_advanced_c());
}

function css_chapters_advanced_a(): array
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
            'section' => 'The cascade',
            'title' => 'The cascade is an order, not a fight',
            'keywords' => 'css cascade, origin, importance, source order',
            'lead' => 'When two rules want the same property, the browser does not average them. It sorts the declarations and keeps one. The sort is the cascade. Origin comes first, then layers, then specificity, then which line was written last.',
            'heading' => 'Sort, then keep the winner',
            'para' => 'A user-agent stylesheet paints links blue before your file loads. Your author rules normally beat that stylesheet. An important user-agent rule, such as a focused form control in forced colors, can beat an important rule you wrote. Inside your own file, two normal rules of equal specificity: the one that appears later wins. People call that source order. It is the last step, not the first. A later rule does not win if an earlier rule is more specific, or if it sits in a stronger layer.',
            'visual' => tf_flow('cascade', 'The browser sorts, then keeps one', [['origin', 'who wrote it'], ['layer', 'which layer'], ['specific', 'how pointed'], ['order', 'which is last']], 'Origin beats a layer. A layer beats specificity. Specificity beats source order. A later line wins only when the earlier steps tied.'),
            'bullets' => [
                'The user-agent origin is the browser. The author origin is your stylesheet. The user origin is a reader stylesheet, which is rare.',
                'Normal author rules beat normal user-agent rules. Important user-agent rules beat important author rules.',
                'Transitions and animations are extra origins. An animation can cover a property while it runs, then the cascade returns.',
                'Source order includes the order of linked files. A stylesheet linked later is later in the cascade.',
            ],
            'rows' => [
                ['origin', 'Who wrote the rule', 'Browser, reader, or you.'],
                ['importance', 'The important flag', 'It changes origin order. It is not specificity.'],
                ['layer', 'A named group of rules', 'Covered in the layers lesson.'],
                ['specificity', 'How pointed the selector is', 'Compared only after origin and layer tie.'],
                ['source order', 'The later declaration', 'The last tie-break.'],
                ['winner', 'One value per property', 'The others are discarded, not blended.'],
            ],
            'e1' => ['Example 1 — the later equal rule wins', 'Both selectors are a single element. The second declaration is the one you see.', <<<'CSS'
<style>
  p { color: #64748b; font-family: sans-serif; }
  p { color: #059669; }
</style>
<p>This paragraph is green, because the green rule is later and nothing else differed.</p>
CSS],
            'e2' => ['Example 2 — a class beats a later element', 'Source order is not enough when specificity differs. The class is earlier and still wins.', <<<'CSS'
<style>
  .note { color: #059669; font-family: sans-serif; }
  p { color: #e11d48; }
</style>
<p class="note">This stays green. The element rule is later, and it is also weaker.</p>
CSS],
            'micro' => [
                'Inspect the element and read the crossed-out lines. The browser is showing you the declarations that lost, and why is the sort above.',
                'A property that is not set at all falls through to inheritance or to the initial value. That is not the same as losing a contest.',
                'Two files that both style the same class are ordered by the link tags, then by the lines inside each file.',
            ],
            'quiz' => [
                $q('What does the cascade do with two declarations of the same property?', 'It sorts them and keeps one', 'It averages the two values', 'It keeps both and blends the colors', 'It ignores the file'),
                $q('When does source order decide the winner?', 'When origin, layer, and specificity have tied', 'Always, because the last line is the design', 'Only for colors', 'Never'),
                $q('Which origin normally loses to a normal rule in your stylesheet?', 'The user-agent stylesheet', 'An important user-agent rule', 'An inline style', 'A later author rule'),
            ],
            'practice' => 'Write two p rules, gray then green. Then add a class before the green rule and say which color wins, and which cascade step decided it.',
        ]),
        $L([
            'section' => 'The cascade',
            'title' => 'Specificity is three columns',
            'keywords' => 'css specificity, :is, :where, inline style',
            'lead' => 'Specificity is a column of three counts: IDs, then classes and attributes and pseudo-classes, then elements and pseudo-elements. You compare the ID column first. A single ID beats any number of classes. Inline styles sit above all three columns.',
            'heading' => 'Count the columns, from the left',
            'para' => 'p is 0, 0, 1. .note is 0, 1, 0. .note p is 0, 1, 1. #title is 1, 0, 0. #title .note is 1, 1, 0. Compare left to right, and stop at the first column that differs. :is() and :not() and :has() take the specificity of their most specific argument, not a fixed number. :where() is the escape: its specificity is zero, no matter what you put inside it. That is how a reset can name an ID without trapping every later class.',
            'visual' => tf_cells('specificity', 'Compare the left column first', [['ID', '1'], ['class', '0'], ['element', '1']], 'One ID beats a pile of classes. :where() contributes nothing to these columns. An inline style sits above the whole row.', [0], '#title p'),
            'bullets' => [
                'Do not add the columns into one total. 1, 0, 0 beats 0, 12, 0.',
                ':where(.note, #title) has specificity 0, 0, 0. :is(.note, #title) has specificity 1, 0, 0.',
                'A pseudo-element such as ::before counts in the element column. :hover counts in the class column.',
                'The universal selector * and the combinators add nothing.',
            ],
            'rows' => [
                ['ID column', '#title', 'Compared first.'],
                ['class column', '.note, [type], :hover', 'Attributes and pseudo-classes live here.'],
                ['element column', 'p, ::before', 'Pseudo-elements live here.'],
                [':is()', 'The strongest argument', 'The others do not lower it.'],
                [':where()', 'Always zero', 'Safe inside a reset.'],
                ['inline style', 'Above the columns', 'A style attribute on the element.'],
            ],
            'e1' => ['Example 1 — one ID beats a class and an element', 'The paragraph has all three hooks. The ID column wins.', <<<'CSS'
<style>
  p { color: #64748b; font-family: sans-serif; }
  .note { color: #059669; }
  #title { color: #0284c7; }
</style>
<p id="title" class="note">Blue. The ID is one column ahead of the class.</p>
CSS],
            'e2' => ['Example 2 — :where() drops the ID weight', 'The selector still matches the ID. Its specificity is zero, so the class wins.', <<<'CSS'
<style>
  .note { color: #059669; font-family: sans-serif; }
  :where(#title) { color: #e11d48; }
</style>
<p id="title" class="note">Green. :where() matched, and then weighed nothing.</p>
CSS],
            'micro' => [
                'Adding an ID to win a contest makes the next contest harder. Prefer a class, or a layer, over a stronger selector.',
                ':not(.note) has the specificity of .note. It is not a free zero.',
                'A style attribute beats these columns and still loses to an important author rule. Importance is a different step.',
            ],
            'quiz' => [
                $q('Which selector wins between #title and .a.b.c.d?', '#title', '.a.b.c.d', 'They tie', 'Whichever is shorter'),
                $q('What is the specificity of :where(#title)?', '0, 0, 0', '1, 0, 0', '0, 1, 0', 'The same as #title'),
                $q('What does :is(.note, #title) count as?', '1, 0, 0, because the ID is an argument', '0, 1, 0, the class', '0, 0, 0', '2, 0, 0'),
            ],
            'practice' => 'Give a paragraph an ID and a class. Color the class green and the ID blue. Then wrap the ID selector in :where() and write down the color you expect before you look.',
        ]),
        $L([
            'section' => 'The cascade',
            'title' => 'Where an important flag really sits',
            'keywords' => 'css important, cascade importance, author important',
            'lead' => 'The important flag is not a stronger specificity. It moves the declaration into a different importance band. In your stylesheet, an important rule beats a normal rule of any specificity. A reader important rule, and an important browser rule, can still beat yours.',
            'heading' => 'Importance is a band, then the cascade runs again',
            'para' => 'Inside the important author band, layers and specificity and source order run again. Two important rules: the more specific one wins, and if they tie, the later one wins. This is why a file full of important flags becomes a second stylesheet that is harder than the first. You did not escape the cascade. You moved the fight one floor up. Reach for the flag when a third-party rule you cannot edit is beating you, and leave a comment that names that rule. A component library that ships important utilities has already spent the floor.',
            'visual' => tf_split('important', 'The flag changes the band', 'normal rule against important?', 'Important', 'wins inside the author origin', 'Normal', 'any specificity still loses', 'After that, two important rules compare layers, then specificity, then source order, just like normal rules.'),
            'bullets' => [
                'An important class beats a normal ID. Specificity is compared only inside the same band.',
                'An inline style is still a normal author declaration unless the attribute itself uses the flag.',
                'In cascade layers, important order is reversed. The layers lesson shows that.',
                'Prefer :where() and a layer over the flag. The flag has nowhere left to go once both sides use it.',
            ],
            'rows' => [
                ['normal', 'The usual band', 'Your everyday rules.'],
                ['important', 'The upper author band', 'Beats your normal rules.'],
                ['then specificity', 'Inside the band', 'An important ID beats an important class.'],
                ['then order', 'Still the last step', 'Two important classes: the later one wins.'],
                ['user important', 'A reader stylesheet', 'Can beat an author important rule.'],
                ['the habit', 'A second fight', 'Every new flag needs another flag.'],
            ],
            'e1' => ['Example 1 — the flag beats a stronger selector', 'The ID is more specific and earlier in the normal band. The class is important, so the class wins.', <<<'CSS'
<style>
  #title { color: #0284c7; font-family: sans-serif; }
  .note { color: #059669 !important; }
</style>
<p id="title" class="note">Green. The flag moved the class into a higher band.</p>
CSS],
            'e2' => ['Example 2 — inside the band, specificity returns', 'Both rules are important. The ID wins again.', <<<'CSS'
<style>
  .note { color: #059669 !important; font-family: sans-serif; }
  #title { color: #0284c7 !important; }
</style>
<p id="title" class="note">Blue. Both used the flag, so the ID column matters again.</p>
CSS],
            'micro' => [
                'In the inspector, an important loser is still crossed out. Read which band the winner is in before you add another flag.',
                'A transition cannot smooth a change that the flag and a normal rule are fighting over in a useful way. Fix the cascade first.',
                'all: unset does not clear an important rule from another layer. The flag has to be beaten in its own band.',
            ],
            'quiz' => [
                $q('What does the important flag change?', 'The importance band, not the specificity columns', 'The specificity, by adding one ID', 'The source order', 'The HTML'),
                $q('Two important classes style the same property. Which wins?', 'The one that appears later', 'The one that appears earlier', 'Neither, the property is cleared', 'The longer class name'),
                $q('When is the flag a reasonable tool?', 'When a rule you cannot edit is beating you', 'On every class, so the design is safe', 'Instead of a selector', 'To make a color more specific'),
            ],
            'practice' => 'Make a class beat an ID using the flag. Then put the flag on the ID as well. Write one sentence on which band decided each example.',
        ]),
        $L([
            'section' => 'The cascade',
            'title' => 'Cascade layers put a reset under the components',
            'keywords' => 'css cascade layers, @layer, unlayered styles',
            'lead' => 'A cascade layer is a named group. For normal rules, a later layer beats an earlier layer, and unlayered rules beat every layer. A reset can be as specific as it wants. If it lives in an early layer, a component in a later layer still wins with a single class.',
            'heading' => 'Declare the order once, on purpose',
            'para' => '@layer reset, components; declares the order before any rules. Order of first appearance is the order of the layers. Appending rules to a layer later does not move that layer. Unlayered author rules sit above all layers, so a utility you forgot to place in a layer will beat the component. That surprise is the main bug. Inside one layer, specificity and source order work as usual. With the important flag, layer order flips: an important rule in an earlier layer beats an important rule in a later layer, and an important layered rule beats an important unlayered rule. The flag is not a way out of layers. It reverses them.',
            'visual' => tf_layers('layers', 'Normal rules: later layer on top', [
                ['unlayered — wins a normal fight', '#fee2e2', '#e11d48'],
                ['components — the design', '#d1fae5', '#059669'],
                ['reset — loud, and still underneath', '#e2e8f0', '#64748b'],
            ], 'Put the reset in the first layer. Leave utilities in a last layer, or they become unlayered and beat the components.'),
            'bullets' => [
                'Declare the full order on the first @layer line. A layer created by accident is ordered where it first appears.',
                'An imported file can be placed with @import url("tokens.css") layer(tokens);. The word layer is doing the sorting.',
                'Unlayered beats layered for normal declarations. Put every party in a layer if you want the order you declared.',
                'Important rules reverse the layer order. Test that once, so it is not a surprise in a dark-mode override.',
            ],
            'rows' => [
                ['@layer a, b', 'Declares order', 'a is under b for normal rules.'],
                ['later layer', 'Wins when both are normal', 'components beat reset.'],
                ['unlayered', 'Above every layer', 'A forgotten rule becomes the strongest normal rule.'],
                ['inside a layer', 'Specificity still works', 'Layers do not flatten selectors.'],
                ['important', 'Layer order flips', 'The early layer wins the important band.'],
                ['first seen', 'The layer position', 'Adding rules later does not reorder it.'],
            ],
            'e1' => ['Example 1 — a later layer beats a specific reset', 'The reset names the element and a class. The component names only a class, and it is in the later layer.', <<<'CSS'
<style>
  @layer reset, parts;
  @layer reset {
    p.note { color: #64748b; font-family: sans-serif; }
  }
  @layer parts {
    .note { color: #059669; }
  }
</style>
<p class="note">Green. The reset was more specific, and it was in an earlier layer.</p>
CSS],
            'e2' => ['Example 2 — an unlayered rule beats the layer', 'The red rule is not in a layer. For a normal declaration, that puts it on top.', <<<'CSS'
<style>
  @layer reset, parts;
  @layer parts {
    .note { color: #059669; font-family: sans-serif; }
  }
  .note { color: #e11d48; }
</style>
<p class="note">Red. Unlayered author styles win a normal contest against every layer.</p>
CSS],
            'micro' => [
                'If a third-party sheet is unlayered, your layered component will not beat it. Import that sheet into an early layer, or leave your overrides unlayered on purpose.',
                'Nesting layer names with a dot, such as components.button, creates a layer inside a layer. The parent order still comes from the first declaration.',
                'Check the Styles pane for the layer name. A rule you cannot find is often in a layer you forgot you created.',
            ],
            'quiz' => [
                $q('For normal rules, which layer wins?', 'The later layer', 'The earlier layer', 'The layer with more selectors', 'The layer that was edited most recently'),
                $q('Where do unlayered author rules sit?', 'Above every layer, for normal declarations', 'Under the reset layer', 'They are ignored', 'They tie with the first layer'),
                $q('What does the important flag do to layer order?', 'It reverses it', 'It deletes the layers', 'It has no effect', 'It moves the rule to the last layer'),
            ],
            'practice' => 'Declare reset then parts. Put a gray element rule in reset and a green class in parts. Then add one unlayered red class and explain the color.',
        ]),
        $L([
            'section' => 'The cascade',
            'title' => 'Some properties inherit, and some stop',
            'keywords' => 'css inheritance, initial, unset, revert, inherit keyword',
            'lead' => 'Color and font inherit. A child uses the parent color when the child does not set one. Margin, padding, border, background, and width do not inherit. They start from their own initial value unless you ask them to inherit.',
            'heading' => 'Four keywords that aim at four different places',
            'para' => 'inherit takes the parent computed value, even for a property that normally does not inherit. initial takes the specification initial value, which for color is not the body color, so initial on color can surprise you. unset acts as inherit for inherited properties and as initial for the rest. revert rolls the property back to the user-agent stylesheet, which is why revert on a button gives you the browser button again. all: revert is a polite reset. all: unset is a harsher one, because it also drops inherited font and color. The keyword you choose is the whole difference between a reset that looks blank and a reset that looks broken.',
            'visual' => tf_panels('inherit', 'The child does not receive everything', 'inherits', ['color', 'font-family', 'line-height', 'visibility'], 'stops', ['margin', 'padding', 'border', 'background', 'width'], 'The border on a parent is not a border on the child. inherit asks for it. revert asks for the browser rule instead.'),
            'bullets' => [
                'A set property is not inheritance. If the child declares color, the parent color is irrelevant for that child.',
                'em on font-size uses the parent font size. That is a computation, and it is related to inheritance, and it is still the child property.',
                'Custom properties inherit by default. A registered property can refuse to inherit. That is the @property lesson.',
                'revert-layer rolls back to the previous cascade layer. It is the precise undo inside a layered stylesheet.',
            ],
            'rows' => [
                ['inherit', 'Parent computed value', 'Works even when the property normally stops.'],
                ['initial', 'The spec starting value', 'Color becomes the spec initial, not the body color.'],
                ['unset', 'inherit or initial', 'Depends on whether the property inherits.'],
                ['revert', 'The browser rule', 'A button looks like a button again.'],
                ['revert-layer', 'The previous layer', 'The undo for a layered design.'],
                ['all', 'Every property', 'The keyword after all decides how far back you go.'],
            ],
            'e1' => ['Example 1 — color crosses, the border does not', 'The parent sets both. Only the color shows up on the paragraph.', <<<'CSS'
<style>
  .parent { color: #0284c7; border: 4px solid #0284c7; font-family: sans-serif; padding: 8px; }
</style>
<div class="parent"><p>The words are blue. The paragraph has no border of its own.</p></div>
CSS],
            'e2' => ['Example 2 — inherit asks for the border', 'The paragraph now takes the parent border on purpose.', <<<'CSS'
<style>
  .parent { color: #0284c7; border: 4px solid #0284c7; font-family: sans-serif; padding: 8px; }
  .parent p { border: inherit; }
</style>
<div class="parent"><p>This paragraph asked for the border, so it has one.</p></div>
CSS],
            'micro' => [
                'visibility: hidden inherits, and a child can set visibility: visible to show again. display: none does not give the child that chance, because the child is not rendered.',
                'A reset that sets color: initial on the body will not look like the browser body. revert is usually the word you wanted.',
                'Inherited values are computed values. A child that inherits a percentage width inherits the used pixel result, not the percentage text.',
            ],
            'quiz' => [
                $q('Which property inherits by default?', 'color', 'margin', 'background', 'width'),
                $q('What does revert aim at?', 'The user-agent rule for that property', 'The parent value', 'Always zero', 'The previous HTML file'),
                $q('What does border: inherit do?', 'It takes the parent border, even though border normally stops', 'It deletes the parent border', 'It is invalid', 'It sets the initial border, which is none'),
            ],
            'practice' => 'Put a color and a border on a parent. Confirm the child only receives the color. Then set border: inherit on the child and name the keyword that would restore a button to the browser look.',
        ]),
        $L([
            'section' => 'The real box',
            'title' => 'border-box counts the padding inside the width',
            'keywords' => 'css box-sizing, border-box, content-box, width',
            'lead' => 'The default box-sizing is content-box. Width is the content only, and padding and border are extra. border-box makes width the whole border edge. The padding is inside the number you wrote. Global border-box is the size people mean when they say the card is 200 pixels.',
            'heading' => 'One number, two different boxes',
            'para' => 'A content-box of width 160 with 24 pixels of padding on each side and a 2 pixel border is 160 + 48 + 4, which is 212 pixels of layout. The same numbers with border-box stay 160, and the content area shrinks to make room. Percentage widths, flex bases, and grid tracks all use that border edge when the box is border-box. A component that mixes the two sizings will not line up with its neighbour. Set the switch once on the root, and let form controls inherit it, because inputs have a user-agent box-sizing that may not match the page.',
            'visual' => tf_panels('borderbox', 'Same width declaration', 'content-box', ['width is the content', 'padding is extra', 'the border is extra'], 'border-box', ['width includes padding', 'width includes border', 'the content shrinks'], '160 plus padding is wider than 160. border-box keeps the outer number honest.'),
            'bullets' => [
                'box-sizing does not include margin. Margin is always outside.',
                'The universal switch is *, *::before, *::after { box-sizing: border-box; } so the pseudo-elements follow.',
                'An input with the user-agent content-box next to a border-box div of the same width will look wider.',
                'min-width and max-width follow the same edge as width.',
            ],
            'rows' => [
                ['content-box', 'The initial value', 'Width is content only.'],
                ['border-box', 'The usual switch', 'Width is content plus padding plus border.'],
                ['padding', 'Inside, after the switch', 'It steals space from the content.'],
                ['border', 'Inside, after the switch', 'A thick border shrinks the content too.'],
                ['margin', 'Always outside', 'The sizing mode does not absorb it.'],
                ['inherit it', 'Form controls', 'Their user-agent sheet may set a different mode.'],
            ],
            'e1' => ['Example 1 — the same width, two outer sizes', 'Both say 160. Only the border-box one is 160 on the outside.', <<<'CSS'
<style>
  .row { display: flex; gap: 12px; align-items: flex-start; font-family: sans-serif; }
  .box { width: 160px; padding: 24px; border: 2px solid #0284c7; background: #e0f2fe; }
  .content { box-sizing: content-box; }
  .border { box-sizing: border-box; background: #d1fae5; border-color: #059669; }
</style>
<div class="row">
  <div class="box content">content-box</div>
  <div class="box border">border-box</div>
</div>
CSS],
            'e2' => ['Example 2 — the switch, including pseudos', 'The card and its ribbon use the same sizing. The width you read is the width you get.', <<<'CSS'
<style>
  *, *::before, *::after { box-sizing: border-box; }
  .card {
    width: 220px; padding: 16px; border: 4px solid #0f172a;
    background: #f8fafc; font-family: sans-serif; position: relative;
  }
  .card::before {
    content: "160 of content, plus padding, still 220 outside";
    display: block; font-size: 13px;
  }
</style>
<div class="card">The border is inside the 220.</div>
CSS],
            'micro' => [
                'A third-party widget that assumes content-box will grow if you force border-box onto it. Scope the switch, or reset that widget back.',
                'outline does not take space in either mode. A thick outline will not push the neighbour. box-shadow will not either.',
                'When you read offsetWidth in the inspector, you are looking at the border edge. That matches border-box width, not content-box width.',
            ],
            'quiz' => [
                $q('What does width mean under border-box?', 'The border edge, including padding and border', 'The content only', 'The margin edge', 'The outline edge'),
                $q('Which piece stays outside the width in both modes?', 'margin', 'padding', 'border', 'the text'),
                $q('Why include ::before in the global switch?', 'Pseudos are boxes too, and they would keep content-box', 'Pseudos cannot have padding', 'It changes the HTML', 'It is required for colors'),
            ],
            'practice' => 'Make two 160 pixel boxes with 24 pixels of padding. One content-box, one border-box. Measure the outside with the inspector and write both numbers down.',
        ]),
        $L([
            'section' => 'The real box',
            'title' => 'Vertical margins collapse into one gap',
            'keywords' => 'css margin collapse, adjacent margins, gap',
            'lead' => 'Two vertical margins that touch become one margin. The larger one wins. A 40 pixel bottom margin and a 30 pixel top margin leave a 40 pixel gap, not 70. Horizontal margins do not do this. Flex, grid, and gap do not do this.',
            'heading' => 'The gap is the max, and only in flow',
            'para' => 'Collapse happens between siblings in normal flow, and between a parent and its first or last child when no padding, border, or gap separates them. A parent that looks too short is often a child margin that escaped and merged with the parent margin. Padding, a border, or display: flow-root on the parent stops that escape. Flex and grid formatting contexts do not collapse margins at all, which is why the same two cards suddenly have a larger gap after you set display: flex. If you wanted the space, use gap. Gap never collapses, and it only exists where you wrote it.',
            'visual' => tf_bars('collapse', 'Two margins become the larger one', [
                ['label' => 'bottom 40', 'value' => 40, 'display' => '40', 'color' => '#0284c7'],
                ['label' => 'top 30', 'value' => 30, 'display' => '30', 'color' => '#94a3b8'],
                ['label' => 'the gap', 'value' => 40, 'display' => '40, not 70', 'color' => '#059669'],
            ], 'Horizontal margins stay side by side. Vertical margins in normal flow collapse. gap does not collapse.'),
            'bullets' => [
                'Negative margins collapse too, with a different rule: the most negative is added to the largest positive. Prefer gap so you do not need that rule.',
                'Empty blocks can collapse their own top and bottom through themselves if they have no padding, border, or height.',
                'Floats, inline-blocks, flex items, and grid items do not collapse their margins with their siblings.',
                'overflow: hidden on the parent also stops the child margin from escaping, because it creates a formatting context. flow-root is the honest tool.',
            ],
            'rows' => [
                ['siblings', 'Vertical margins touch', 'The larger one remains.'],
                ['parent and child', 'No padding or border between them', 'The child margin escapes.'],
                ['horizontal', 'No collapse', 'Left and right stay added.'],
                ['flex and grid', 'No collapse', 'The gap can look bigger than it did in flow.'],
                ['gap', 'Never collapses', 'The space you wrote is the space you get.'],
                ['flow-root', 'Stops the escape', 'The parent wraps the child margin.'],
            ],
            'e1' => ['Example 1 — 40 and 40 become 40', 'The blue bottom and the green top touch. The white gap is one margin, not two.', <<<'CSS'
<style>
  .a, .b { font-family: sans-serif; margin-left: 0; margin-right: 0; }
  .a { margin-bottom: 40px; background: #dbeafe; }
  .b { margin-top: 40px; background: #d1fae5; }
</style>
<div class="a">Bottom margin 40</div>
<div class="b">Top margin 40. The gap is 40, not 80.</div>
CSS],
            'e2' => ['Example 2 — flex does not collapse them', 'The same 40 and 40 now add, because flex items are not in a block formatting context together.', <<<'CSS'
<style>
  .col { display: flex; flex-direction: column; font-family: sans-serif; }
  .a { margin-bottom: 40px; background: #dbeafe; }
  .b { margin-top: 40px; background: #d1fae5; }
</style>
<div class="col">
  <div class="a">Bottom margin 40</div>
  <div class="b">Top margin 40. In a column flex, the gap is 80.</div>
</div>
CSS],
            'micro' => [
                'A heading with margin-top inside a section with no padding will push the section down from the outside. That is parent-child collapse.',
                'If the design needs exactly 24 pixels between cards, use gap: 24px on the parent and set the card margins to zero.',
                'Collapse uses the margin after it has been computed. A percentage vertical margin is a percentage of the containing block width, which is a separate surprise.',
            ],
            'quiz' => [
                $q('A block has margin-bottom 40 and the next has margin-top 30. What is the gap?', '40', '70', '30', '10'),
                $q('Do flex item margins collapse with each other?', 'No', 'Yes, the same as blocks', 'Only in a row', 'Only if gap is set'),
                $q('What is a clean way to stop a child margin escaping the parent?', 'display: flow-root, or padding, or a border', 'float the child', 'set color', 'use a later selector'),
            ],
            'practice' => 'Stack two blocks with 40 pixel vertical margins and look at the gap. Wrap them in a column flex and look again. Write both distances.',
        ]),
        $L([
            'section' => 'The real box',
            'title' => 'A formatting context holds the children in',
            'keywords' => 'css block formatting context, flow-root, float, BFC',
            'lead' => 'A block formatting context is a box that contains its floats and its margins. A normal block does not contain a float: the float hangs out, and the parent height ignores it. display: flow-root creates a context and nothing else. The background then wraps the float.',
            'heading' => 'flow-root is the contain, without a side effect',
            'para' => 'Older recipes used overflow: hidden or a float on the parent to get the same containment. overflow: hidden also clips, and a floated parent changes how the parent sits in its own parent. flow-root does the containment and leaves the overflow and the outer display alone. Flex, grid, and table cells are formatting contexts of their own, so they contain floats too. You rarely need a float today. You still need the idea, because a tall float beside a short paragraph is the same context: the next section will climb up beside the float unless that next section is a context itself, or you clear the float.',
            'visual' => tf_panels('bfc', 'Does the parent wrap the float?', 'normal block', ['height ignores the float', 'the background is short', 'the next text climbs up'], 'flow-root', ['the float is inside', 'the background wraps it', 'margins stay inside'], 'overflow: hidden also contains, and it clips. flow-root contains without clipping.'),
            'bullets' => [
                'clear: both on an empty element at the end of the parent is the old clearfix. flow-root replaces it.',
                'A context stops parent-child margin collapse as well as float escape.',
                'inline-block contains floats and also shrinks to fit. That shrink is the side effect.',
                'You can still use float for a real wrap-around, such as a small picture in a paragraph. Contain the parent if the picture is taller than the text.',
            ],
            'rows' => [
                ['normal flow', 'Does not contain floats', 'Parent height can be zero.'],
                ['flow-root', 'Contains, and that is all', 'The modern tool.'],
                ['overflow hidden', 'Contains and clips', 'A shadow or a dropdown can be cut.'],
                ['flex or grid', 'A different context', 'Floats inside are contained.'],
                ['clear', 'The next box moves below the float', 'It does not make the parent taller by itself.'],
                ['the bug', 'A short parent', 'The background ends before the picture.'],
            ],
            'e1' => ['Example 1 — the parent ignores the float', 'The yellow background is only as tall as the text. The blue square hangs out.', <<<'CSS'
<style>
  .parent { background: #fef3c7; font-family: sans-serif; }
  .pic { float: left; width: 72px; height: 72px; margin-right: 8px; background: #0284c7; }
</style>
<div class="parent"><div class="pic"></div><p>Short line.</p></div>
CSS],
            'e2' => ['Example 2 — flow-root wraps it', 'The same square is inside the yellow box. Nothing is clipped.', <<<'CSS'
<style>
  .parent { display: flow-root; background: #fef3c7; font-family: sans-serif; }
  .pic { float: left; width: 72px; height: 72px; margin-right: 8px; background: #0284c7; }
</style>
<div class="parent"><div class="pic"></div><p>Short line.</p></div>
CSS],
            'micro' => [
                'A dropdown inside a card with overflow: hidden will be clipped. If the overflow was only there to contain a float, switch it to flow-root.',
                'Margin collapse and float containment fail together. One flow-root fixes both.',
                'display: flow-root is a block-level box. It is not a flex or a grid. Put it on the wrapper, then flex the inside if you need both.',
            ],
            'quiz' => [
                $q('What does display: flow-root add?', 'A block formatting context, so floats and child margins stay inside', 'A new stacking order', 'Flex layout', 'A smaller font'),
                $q('Why is overflow: hidden a risky way to contain a float?', 'It also clips descendants that stick out', 'It deletes the float', 'It only works on images', 'It sets the width to zero'),
                $q('Does a normal block parent grow to wrap a floated child?', 'No', 'Yes', 'Only if the float is on the right', 'Only with a class selector'),
            ],
            'practice' => 'Float a colored square inside a tinted parent with one short paragraph. Then add flow-root and describe what the background does.',
        ]),
        $L([
            'section' => 'The real box',
            'title' => 'Absolute looks for a containing block',
            'keywords' => 'css containing block, absolute, fixed, transform',
            'lead' => 'An absolute box is taken out of flow and positioned against its containing block. That block is the nearest ancestor whose position is not static. If there is none, it is the initial containing block. A transform, filter, or perspective on an ancestor becomes the containing block too, even when that ancestor is position static.',
            'heading' => 'The ancestor you transformed is now the anchor',
            'para' => 'top and left are offsets from that block, not from the page, and not from the static place the element would have had. right and bottom pull from the other edges. If you set top and bottom and leave height auto, the height stretches between them, but only if both edges are set. A fixed box uses the viewport, until an ancestor has a transform, a filter, or will-change: transform. Then fixed is trapped inside that ancestor and scrolls away with it. This is the bug where a modal inside a faded card will not cover the page. Move the modal out, or drop the transform from the ancestor.',
            'visual' => tf_flow('containing', 'Absolute walks up until it finds a block', [['element', 'absolute'], ['ancestor', 'relative, or a transform'], ['edges', 'top and left']], 'position: relative with no offset is a containing block that does not move. A transform is a containing block you may not have wanted.'),
            'bullets' => [
                'position: relative with top and left still in auto is the usual anchor. The element stays in flow.',
                'Percentage top on an absolute element is a percentage of the containing block height. Percentage left uses the width.',
                'will-change: transform and contain: paint also trap fixed descendants in current browsers.',
                'A sticky element is stuck within its nearest scroll ancestor. It is not a containing-block question, and it still needs a top or a bottom.',
            ],
            'rows' => [
                ['static', 'Not a containing block', 'Absolute skips it.'],
                ['relative', 'A block, stays in flow', 'The usual anchor.'],
                ['absolute ancestor', 'Also a block', 'Nested popups anchor to the outer popup.'],
                ['transform', 'A block, even if static', 'The trap for fixed modals.'],
                ['fixed', 'The viewport', 'Until a transformed ancestor catches it.'],
                ['both edges', 'Stretches the auto size', 'top and bottom together set the height.'],
            ],
            'e1' => ['Example 1 — the badge anchors to the card', 'The card is relative. The badge uses the card edges, not the page.', <<<'CSS'
<style>
  .card { position: relative; width: 220px; height: 100px; background: #e2e8f0; font-family: sans-serif; }
  .badge { position: absolute; top: 8px; right: 8px; background: #059669; color: white; padding: 4px 8px; }
</style>
<div class="card">Card<span class="badge">new</span></div>
CSS],
            'e2' => ['Example 2 — a transform traps the absolute box', 'The outer stage is relative. The inner box is static, but translated, so the tag anchors there.', <<<'CSS'
<style>
  .stage { position: relative; height: 160px; background: #f1f5f9; font-family: sans-serif; }
  .mover { transform: translate(48px, 36px); width: 140px; height: 80px; background: #dbeafe; }
  .tag { position: absolute; top: 6px; left: 6px; background: #e11d48; color: white; padding: 2px 6px; }
</style>
<div class="stage">
  <div class="mover"><span class="tag">inside</span></div>
</div>
CSS],
            'micro' => [
                'If a popup is clipped or stuck inside a card, look up the tree for transform, filter, and will-change before you raise z-index.',
                'absolute removes the element from flow. The parent will not grow to hold it. Give the parent a size, or use a different layout.',
                'inset: 0 is the shorthand for top, right, bottom, and left all zero. On an absolute box with an anchored parent, that stretches it to the parent padding edge.',
            ],
            'quiz' => [
                $q('Which ancestor does an absolute element use?', 'The nearest one that is not static, or that has a transform', 'Always the body', 'Always the parent, even if it is static', 'The previous sibling'),
                $q('What can trap a fixed modal inside a card?', 'A transform on an ancestor', 'A class name', 'A green color', 'font-family'),
                $q('Does position: relative with no offset move the element?', 'No. It stays in flow and becomes a containing block', 'Yes, by 10 pixels', 'It removes it from flow', 'It hides it'),
            ],
            'practice' => 'Put a badge in the top right of a card. Then add a transform on a wrapper between the card and the badge, and write down which box the badge jumps to.',
        ]),
        $L([
            'section' => 'The real box',
            'title' => 'z-index only compares inside one stacking context',
            'keywords' => 'css z-index, stacking context, isolation',
            'lead' => 'z-index is not a global ladder. It compares boxes that share a stacking context. A child with z-index 99 cannot climb out of a parent context that is already under an uncle. The whole parent, badge included, is painted as one group.',
            'heading' => 'The group is painted together',
            'para' => 'A stacking context is created by a positioned element with a z-index other than auto, by opacity less than 1, by transform, filter, mix-blend-mode, will-change, and by isolation: isolate. Flex and grid items can use z-index even when their position is static. The root is a context. Inside one context, a higher z-index paints later. Negative z-index paints behind the parent background only if the parent is not itself forcing a flat group. isolation: isolate is the tool you want when a widget must manage its own indexes without leaking into the page, and when a widget must not be beaten from the inside by a third-party badge.',
            'visual' => tf_layers('stack', 'The parent group paints as one layer', [
                ['page', '#f8fafc', '#64748b'],
                ['back card, z-index 2', '#dbeafe', '#0284c7'],
                ['front card, z-index 1, badge 99 trapped inside', '#fecaca', '#e11d48'],
            ], 'The badge number is compared with siblings inside the front card, not with the back card.'),
            'bullets' => [
                'z-index on a static block that is not a flex or grid item does nothing. The property is ignored until a context-creating position exists.',
                'opacity: 0.99 is enough to create a context. A fade animation can trap a menu.',
                'A negative z-index on a child goes behind the parent background, and it can also go behind the page if you are not careful.',
                'Paint order inside one context is: negative indexes, in-flow content, zero and auto, then positive indexes.',
            ],
            'rows' => [
                ['same context', 'The numbers compare', 'Higher paints on top.'],
                ['nested context', 'The group is one layer', 'The inner number cannot beat an uncle.'],
                ['opacity', 'Creates a context', 'Even 0.99.'],
                ['transform', 'Creates a context', 'The modal trap, again.'],
                ['isolation', 'Creates a context on purpose', 'The widget keeps its own ladder.'],
                ['flex item', 'z-index works', 'Position can stay static.'],
            ],
            'e1' => ['Example 1 — 99 loses to the group', 'The badge is 99 inside a card of 1. The other card is 2. The badge stays underneath that card.', <<<'CSS'
<style>
  .stage { position: relative; height: 120px; font-family: sans-serif; }
  .back, .front { position: absolute; width: 180px; height: 70px; }
  .back { z-index: 2; top: 28px; left: 70px; background: #dbeafe; }
  .front { z-index: 1; top: 8px; left: 8px; background: #fecaca; }
  .badge { position: relative; z-index: 99; background: #e11d48; color: white; display: inline-block; padding: 4px; }
</style>
<div class="stage">
  <div class="front"><span class="badge">99</span></div>
  <div class="back">z-index 2</div>
</div>
CSS],
            'e2' => ['Example 2 — raise the group, not the badge', 'The front card is now 3. The badge did not need a new number.', <<<'CSS'
<style>
  .stage { position: relative; height: 120px; font-family: sans-serif; }
  .back, .front { position: absolute; width: 180px; height: 70px; }
  .back { z-index: 2; top: 28px; left: 70px; background: #dbeafe; }
  .front { z-index: 3; top: 8px; left: 8px; background: #fecaca; }
  .badge { background: #e11d48; color: white; display: inline-block; padding: 4px; }
</style>
<div class="stage">
  <div class="front"><span class="badge">group is 3</span></div>
  <div class="back">z-index 2</div>
</div>
CSS],
            'micro' => [
                'Before you type 9999, name the stacking context. The inspector shows it on the element that created it.',
                'A sticky header and a dropdown need the same conversation: the header context must be above the page content, and the menu must be inside that context.',
                'isolation: isolate on a card stops a child blend mode from blending with the page behind the card.',
            ],
            'quiz' => [
                $q('Can a child z-index of 99 escape a parent stacking context?', 'No. The parent group is painted as one layer', 'Yes, 99 is always on top', 'Only if the child is red', 'Only in Firefox'),
                $q('Which property creates a stacking context even at 0.99?', 'opacity', 'margin', 'font-size', 'color'),
                $q('What is isolation: isolate for?', 'A context you created on purpose, so inner indexes stay inside', 'Hiding the element', 'A faster animation', 'Removing z-index'),
            ],
            'practice' => 'Rebuild example 1 and confirm the badge loses. Then change the parent z-index instead of the badge, and write one sentence about which context the number was compared in.',
        ]),
    ];
}
