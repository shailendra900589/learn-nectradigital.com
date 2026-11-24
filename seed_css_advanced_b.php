<?php
declare(strict_types=1);

function css_chapters_advanced_b(): array
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
            'section' => 'Layout under pressure',
            'title' => 'Flex grow, shrink, and the basis',
            'keywords' => 'css flex grow shrink basis, flex shorthand',
            'lead' => 'A flex item has three numbers. Basis is the size it starts from. Grow is how it takes free space. Shrink is how it gives space back when the row is too small. The shorthand flex sets all three, and the single-number form does not mean what a glance suggests.',
            'heading' => 'flex: 1 is not flex: 1 1 auto',
            'para' => 'The default is flex: 0 1 auto. The item uses its content size as the basis, it does not grow, and it may shrink. flex: 1 means 1 1 0%. The basis is zero, so the free space is shared by the grow factors alone, and the content size is ignored for the starting point. flex: auto means 1 1 auto, so the content size is the basis and leftover space is still shared. flex: none means 0 0 auto: the item refuses to grow or shrink. Two items with flex: 1 and flex: 2 and a zero basis end up one third and two thirds. The same factors with a basis of auto are distorted by a long word in the first item.',
            'visual' => tf_cells('flexnum', 'Three numbers, in this order', [['grow', '1'], ['shrink', '1'], ['basis', '0%']], 'flex: 1 is this row. The default row is grow 0, shrink 1, basis auto. none is 0, 0, auto.', [2], 'flex: 1'),
            'bullets' => [
                'The basis accepts any width value, including max-content and a percentage of the flex container.',
                'Grow factors are a ratio. 1 and 2 means one share and two shares of the free space, after the bases are placed.',
                'If the bases already overflow the container, grow does nothing and shrink does the work.',
                'gap is removed before the free space is calculated. The shares are of what remains.',
            ],
            'rows' => [
                ['flex: 0 1 auto', 'The default', 'Content size, may shrink, will not grow.'],
                ['flex: 1', '1 1 0%', 'Share the row from a zero basis.'],
                ['flex: auto', '1 1 auto', 'Start from the content, then share leftover.'],
                ['flex: none', '0 0 auto', 'Stay at the content size.'],
                ['flex: 2', 'Twice flex: 1', 'Only when the basis is the same kind of zero.'],
                ['gap', 'Taken first', 'Free space is what is left after the gap.'],
            ],
            'e1' => ['Example 1 — 1 and 2 from a zero basis', 'The green item is twice the blue one. The words inside do not change the ratio.', <<<'CSS'
<style>
  .row { display: flex; width: 300px; font-family: sans-serif; color: #0f172a; }
  .a { flex: 1 1 0; background: #dbeafe; padding: 8px; }
  .b { flex: 2 1 0; background: #d1fae5; padding: 8px; }
</style>
<div class="row"><div class="a">one</div><div class="b">two shares</div></div>
CSS],
            'e2' => ['Example 2 — auto basis lets the words vote', 'Both items flex: 1, but the basis is auto. The longer label starts wider, then leftover space is shared.', <<<'CSS'
<style>
  .row { display: flex; width: 300px; font-family: sans-serif; }
  .a, .b { flex: 1 1 auto; padding: 8px; }
  .a { background: #dbeafe; }
  .b { background: #d1fae5; }
</style>
<div class="row"><div class="a">short</div><div class="b">a much longer label</div></div>
CSS],
            'micro' => [
                'flex: 1 1 0 and flex: 1 1 0% are the modern form. A bare 0 used to be read as a length in one old browser and as invalid in another. Write 0%.',
                'A basis of 0% still has a minimum. The next lesson is that minimum, and it can cancel the shrink you just asked for.',
                'Order in the flex shorthand is grow, shrink, basis. flex: 1 80px is grow 1 and basis 80px, and shrink stays 1.',
            ],
            'quiz' => [
                $q('What three values does flex: 1 set?', 'grow 1, shrink 1, basis 0%', 'grow 1, shrink 0, basis auto', 'grow 0, shrink 1, basis auto', 'grow 1, shrink 1, basis auto'),
                $q('What is the default flex value?', '0 1 auto', '1 1 auto', '1 1 0%', 'none'),
                $q('When does a grow factor do nothing?', 'When the bases already fill or overflow the container', 'When the item has text', 'When gap is zero', 'Never'),
            ],
            'practice' => 'Make a 300 pixel row with two items, flex 1 and flex 2, basis 0%. Then change both to basis auto and a long label, and write what changed about the ratio.',
        ]),
        $L([
            'section' => 'Layout under pressure',
            'title' => 'The flex item that will not shrink',
            'keywords' => 'css min-width auto, flex shrink, text overflow',
            'lead' => 'The default min-width of a flex item is auto. The item will not shrink below its content, even when flex-shrink is 1. A long word or a wide picture blows out the row. min-width: 0 lets the item shrink, and then overflow can clip the text.',
            'heading' => 'The minimum is a separate vote from shrink',
            'para' => 'Shrink says how the item gives up space. The minimum size says where it must stop. With min-width: auto, that floor is the minimum of the content size and the specified size. A nowrap sentence has a content size as wide as the sentence, so the floor is the whole sentence and the row overflows the page. Set min-width: 0, or the logical min-inline-size: 0, and the floor drops. Then overflow: hidden and text-overflow: ellipsis have a box smaller than the text, which is the only situation where the ellipsis can appear. The same trap exists on the vertical axis for min-height: auto inside a column flex.',
            'visual' => tf_panels('minw', 'Shrink is allowed, until the minimum says stop', 'min-width: auto', ['floor is the content', 'a long word wins', 'the row overflows'], 'min-width: 0', ['floor is zero', 'the item may be narrower than the text', 'ellipsis can appear'], 'flex-shrink: 1 does not override the minimum. You set the floor yourself.'),
            'bullets' => [
                'The bug shows up the moment a card, a table, or a pre is a flex child.',
                'min-width: 0 on the item that should shrink, not on the row, is the usual fix.',
                'A picture with a fixed width is a content floor too. Give it max-width: 100% if it should follow the item.',
                'In a column flex inside a fixed height, the matching property is min-height: 0.',
            ],
            'rows' => [
                ['min-width: auto', 'The default floor', 'Content size, roughly.'],
                ['min-width: 0', 'No content floor', 'Shrink can go below the text.'],
                ['min-inline-size: 0', 'The logical twin', 'Follows writing direction.'],
                ['nowrap', 'Makes the floor huge', 'The sentence is one long unit.'],
                ['ellipsis', 'Needs a smaller box', 'Otherwise there is nothing to cut.'],
                ['column flex', 'min-height: auto', 'The same trap, vertically.'],
            ],
            'e1' => ['Example 1 — the row overflows', 'Shrink is on. The minimum is still auto, so the long word keeps its width.', <<<'CSS'
<style>
  .row { display: flex; width: 200px; border: 2px solid #e11d48; font-family: sans-serif; }
  .item { flex: 1 1 auto; white-space: nowrap; background: #fee2e2; }
</style>
<div class="row"><div class="item">NectraLearningNotes</div></div>
CSS],
            'e2' => ['Example 2 — drop the floor, then clip', 'min-width: 0 lets the item become 200 pixels. The ellipsis shows that the text was cut.', <<<'CSS'
<style>
  .row { display: flex; width: 200px; border: 2px solid #059669; font-family: sans-serif; }
  .item { flex: 1 1 auto; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; background: #d1fae5; }
</style>
<div class="row"><div class="item">NectraLearningNotes</div></div>
CSS],
            'micro' => [
                'A grid item has the same default minimum of auto. minmax(0, 1fr) is the grid form of this lesson.',
                'overflow: hidden on the item also sets a minimum of 0 in some cases, because a scroll container can shrink. Setting min-width: 0 is clearer.',
                'If the text should wrap instead of clipping, leave white-space alone and only set min-width: 0. The words wrap inside the item.',
            ],
            'quiz' => [
                $q('Why does a flex item with shrink 1 still overflow?', 'min-width: auto will not go below the content size', 'shrink is always ignored', 'The row cannot be 200 pixels', 'ellipsis blocks shrinking'),
                $q('What do you set so a long word can be clipped with an ellipsis?', 'min-width: 0, then overflow hidden and text-overflow ellipsis', 'flex-grow: 0 only', 'z-index: 1', 'float: left'),
                $q('What is the column-flex version of this bug?', 'min-height: auto', 'min-width on the page', 'line-height', 'There is no vertical version'),
            ],
            'practice' => 'Put a nowrap label in a 200 pixel flex row and watch it overflow. Add min-width: 0 and an ellipsis. Then remove nowrap and describe the wrap.',
        ]),
        $L([
            'section' => 'Layout under pressure',
            'title' => 'Grid areas you can read out loud',
            'keywords' => 'css grid-template-areas, named grid lines',
            'lead' => 'grid-template-areas draws the page as lines of names. Each name is a rectangle of cells. A dot is an empty cell. If the names do not form rectangles, the whole declaration is invalid and the grid falls back.',
            'heading' => 'The picture in the stylesheet is the layout',
            'para' => 'A row of the template is a string. The strings must have the same number of cells. "nav nav" "side main" is a nav across the top and two cells under it. The element with grid-area: nav occupies every cell that says nav. You do not also set column start and column end. Named lines are the other dialect: grid-template-columns: [full-start] 1fr [full-end] and then grid-column: full. Areas are easier to read. Lines are easier when the spans are irregular. Mixing them is fine. An item with no area is auto-placed into the next free cell, which may be a cell you thought was empty because of a dot.',
            'visual' => tf_grid('areas', 'The strings are the page', ['col', 'col'], ['row 1', 'row 2'], ['nav', 'nav', 'side', 'main'], 'Each name must be a rectangle. A dot is a cell with no name. The item only says the name.', [0, 1]),
            'bullets' => [
                'A name must form a rectangle. An L shape makes the declaration invalid.',
                'The strings must be the same length. A missing cell is a syntax error, not an empty cell. Use a dot for empty.',
                'grid-area: nav is the shorthand that sets row and column start and end from the named area.',
                'Media queries can replace the whole template. The HTML order stays. The areas move.',
            ],
            'rows' => [
                ['the string', 'One row of cells', 'Quotes around each row.'],
                ['repeated name', 'A span', 'nav nav is two columns.'],
                ['dot', 'An empty cell', 'Not a name.'],
                ['rectangle rule', 'The name is a block', 'An L shape is invalid.'],
                ['grid-area', 'Attaches the element', 'The name must exist.'],
                ['auto placement', 'Unnamed items', 'They fill free cells, including dots.'],
            ],
            'e1' => ['Example 1 — nav, side, and main', 'The template is the picture. Three elements find their cells by name.', <<<'CSS'
<style>
  .page { display: grid; grid-template-columns: 90px 1fr; grid-template-areas: "nav nav" "side main"; gap: 6px; width: 280px; font-family: sans-serif; }
  .nav { grid-area: nav; background: #0f172a; color: white; padding: 8px; }
  .side { grid-area: side; background: #dbeafe; padding: 8px; }
  .main { grid-area: main; background: #d1fae5; padding: 8px; }
</style>
<div class="page"><div class="nav">Nav</div><div class="side">Side</div><div class="main">Main</div></div>
CSS],
            'e2' => ['Example 2 — a dot keeps a cell empty', 'The second cell of the first row has no name. Auto placement will still try to use it, so every item here has a name.', <<<'CSS'
<style>
  .page { display: grid; grid-template-columns: 1fr 1fr; grid-template-areas: "title ." "a b"; gap: 6px; width: 240px; font-family: sans-serif; }
  .title { grid-area: title; background: #fef3c7; padding: 8px; }
  .a { grid-area: a; background: #dbeafe; padding: 8px; }
  .b { grid-area: b; background: #d1fae5; padding: 8px; }
</style>
<div class="page"><div class="title">Title</div><div class="a">A</div><div class="b">B</div></div>
CSS],
            'micro' => [
                'If the template is invalid, the property is dropped and you get a single-column auto grid. The inspector shows the property crossed out.',
                'Names are case-sensitive. Nav and nav are different areas.',
                'A media query that changes areas should name every region the HTML contains, or an item with a missing name sits in an automatic cell.',
            ],
            'quiz' => [
                $q('What shape must a repeated area name form?', 'A rectangle', 'Any shape, including an L', 'A single cell only', 'A circle'),
                $q('What does a dot mean in the template?', 'An empty cell', 'A class named dot', 'The end of the grid', 'A comment'),
                $q('How does an element claim an area?', 'grid-area set to that name', 'A matching id only', 'nth-child', 'z-index'),
            ],
            'practice' => 'Draw a two-by-two template with a header across the top. Then make one name into an L on purpose and see the declaration fail.',
        ]),
        $L([
            'section' => 'Layout under pressure',
            'title' => 'auto-fit, auto-fill, and minmax',
            'keywords' => 'css auto-fit auto-fill minmax, responsive grid',
            'lead' => 'repeat(auto-fit, minmax(140px, 1fr)) makes as many columns as will fit, and each column is at least 140 pixels. auto-fit collapses the empty tracks so the real items stretch. auto-fill keeps the empty tracks, so the items stay near the minimum.',
            'heading' => 'Empty tracks are the whole difference',
            'para' => 'The container width decides how many 140 pixel tracks fit. minmax(140px, 1fr) says the track may grow past 140 to share leftover space. With two cards in a wide row, auto-fit drops the unused tracks and the two cards grow. auto-fill keeps those tracks, so the two cards stay about 140 pixels and blank tracks occupy the rest. The overflow bug is the minimum. minmax(auto, 1fr) uses a minimum of auto, which will not shrink below content, and a long word overflows the page. minmax(0, 1fr) allows the track to shrink. Use a fixed minimum such as 140px when the card has a size you refuse to go under, and accept a horizontal scroll or a wrap when the container is narrower than that minimum.',
            'visual' => tf_panels('autofit', 'Same items, different empty tracks', 'auto-fit', ['empty tracks collapse', 'items stretch', 'two cards fill the row'], 'auto-fill', ['empty tracks remain', 'items stay near the minimum', 'blank space is a track'], 'minmax(0, 1fr) can shrink. minmax(auto, 1fr) will not shrink below the content.'),
            'bullets' => [
                'auto-fit and auto-fill look identical when the row is already full of items.',
                'The difference appears when there are fewer items than the tracks that fit.',
                'A minimum larger than the container causes overflow. There is no track smaller than the minimum.',
                'gap counts against the space used to decide how many tracks fit.',
            ],
            'rows' => [
                ['auto-fit', 'Collapses empty tracks', 'Items stretch.'],
                ['auto-fill', 'Keeps empty tracks', 'Items stay small.'],
                ['minmax min', 'The floor of a track', 'The track will not go under it.'],
                ['1fr max', 'The share of leftover', 'After the floors are placed.'],
                ['minmax(0, 1fr)', 'A shrinkable track', 'The flex min-width: 0 of grid.'],
                ['too-big floor', 'Overflow', 'The container cannot honor it.'],
            ],
            'e1' => ['Example 1 — auto-fit stretches two cards', 'The row is wide enough for three minimums. The empty track collapses and the two cards grow.', <<<'CSS'
<style>
  .grid { display: grid; width: 320px; grid-template-columns: repeat(auto-fit, minmax(90px, 1fr)); gap: 6px; font-family: sans-serif; }
  .grid div { background: #d1fae5; padding: 8px; }
</style>
<div class="grid"><div>One</div><div>Two</div></div>
CSS],
            'e2' => ['Example 2 — auto-fill keeps the empty track', 'The same two cards stay near 90 pixels. The rest of the 320 pixels is an empty track.', <<<'CSS'
<style>
  .grid { display: grid; width: 320px; grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: 6px; font-family: sans-serif; }
  .grid div { background: #dbeafe; padding: 8px; }
</style>
<div class="grid"><div>One</div><div>Two</div></div>
CSS],
            'micro' => [
                'If both examples look the same, the container is only wide enough for the items you have. Widen it.',
                'auto-fit is the usual choice for a card gallery. auto-fill is for a background of slots that should stay a fixed rhythm.',
                'A single column on a phone happens when the minimum is larger than half the container. That is the design, not a broken query.',
            ],
            'quiz' => [
                $q('What does auto-fit do with empty tracks?', 'It collapses them so the items stretch', 'It deletes the items', 'It always makes one column', 'It ignores minmax'),
                $q('When do auto-fit and auto-fill look the same?', 'When there are enough items to fill every track', 'Always', 'Never', 'Only with flex'),
                $q('Why can minmax(auto, 1fr) overflow?', 'The auto minimum will not shrink below the content', '1fr is always 0', 'gap is illegal', 'auto-fit forbids text'),
            ],
            'practice' => 'Build both examples at 320 pixels with two cards. Write one sentence on which one leaves a blank track and why.',
        ]),
        $L([
            'section' => 'Layout under pressure',
            'title' => 'Subgrid uses the parent tracks',
            'keywords' => 'css subgrid, aligned cards, grid-template-rows',
            'lead' => 'A nested grid normally has its own tracks. The buttons in two cards do not line up, because each card sizes its rows from its own text. subgrid tells the child to use the parent tracks instead. The rows line up across the cards.',
            'heading' => 'The child gives up its own track list',
            'para' => 'The parent defines the columns or the rows. The child sets display: grid and grid-template-rows: subgrid, and it spans the rows it wants to share. Those child rows are the parent rows. A long title in one card makes that parent row taller, and the other card honors the same row, so its button stays level. You can subgrid columns, rows, or both. Gaps can be inherited from the parent. A subgrid item can still place its children with grid-row and grid-column. If the browser does not support subgrid, the declaration is dropped and each card stacks on its own, which is an acceptable fallback. Do not build a layout that is unreadable in that fallback.',
            'visual' => tf_grid('subgrid', 'One row size, shared by both cards', ['card A', 'card B'], ['title', 'body', 'action'], ['short', 'tall title', 'one line', 'one line', 'Save', 'Save'], 'The tall title grows the title row. Both actions sit on the same parent row.', [1]),
            'bullets' => [
                'The child must be a grid item of the parent, and it must span the tracks it wants to reuse.',
                'grid-template-rows: subgrid replaces the child row list. It does not add a second list.',
                'Padding on the card sits around the subgrid. The tracks themselves stay shared.',
                'Nested subgrids are allowed. Each one must span tracks that exist.',
            ],
            'rows' => [
                ['own grid', 'Private tracks', 'Buttons do not line up.'],
                ['subgrid', 'Parent tracks', 'A tall title raises every card.'],
                ['span', 'How many tracks to use', 'span 3 for title, body, and action.'],
                ['columns too', 'grid-template-columns: subgrid', 'The same idea sideways.'],
                ['fallback', 'Declaration dropped', 'Cards still stack. Keep that readable.'],
                ['gap', 'Can come from the parent', 'The rhythm stays one rhythm.'],
            ],
            'e1' => ['Example 1 — each card has private rows', 'The long title makes only that card taller. The two Save lines do not sit on one line.', <<<'CSS'
<style>
  .board { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; width: 300px; font-family: sans-serif; align-items: start; }
  .card { display: grid; gap: 4px; background: white; border: 1px solid #cbd5e1; }
  .go { background: #d1fae5; }
</style>
<div class="board">
  <article class="card"><div>Short</div><div>Body</div><div class="go">Save</div></article>
  <article class="card"><div>A longer title that wraps</div><div>Body</div><div class="go">Save</div></article>
</div>
CSS],
            'e2' => ['Example 2 — subgrid shares the three rows', 'Both cards span three parent rows and reuse them. The Save lines share a row.', <<<'CSS'
<style>
  .board { display: grid; grid-template-columns: 1fr 1fr; grid-template-rows: auto auto auto; gap: 8px 10px; width: 300px; font-family: sans-serif; }
  .card { display: grid; grid-row: span 3; grid-template-rows: subgrid; background: white; border: 1px solid #cbd5e1; }
  .go { background: #d1fae5; }
</style>
<div class="board">
  <article class="card"><div>Short</div><div>Body</div><div class="go">Save</div></article>
  <article class="card"><div>A longer title that wraps</div><div>Body</div><div class="go">Save</div></article>
</div>
CSS],
            'micro' => [
                'If example 2 looks like example 1, this browser is ignoring subgrid. The fallback is the private grid, which is why example 1 has to be acceptable.',
                'align-items: start on the parent is what makes the private-grid version look uneven. Subgrid does not need it.',
                'A gap on the subgrid card and a gap on the parent add in ways that are easy to double. Prefer the parent gap.',
            ],
            'quiz' => [
                $q('What does grid-template-rows: subgrid use?', 'The parent row tracks', 'A new set of equal rows', 'Only the first child', 'Flex lines'),
                $q('Why do card buttons fail to line up in a nested grid?', 'Each card sizes its own rows from its own text', 'Buttons cannot be grid items', 'Subgrid forbids titles', 'Gap collapses to zero'),
                $q('What is the fallback when subgrid is unsupported?', 'The declaration is dropped and the card stacks on its own', 'The page goes blank', 'The parent grid is deleted', 'Every item becomes fixed'),
            ],
            'practice' => 'Build two cards with a short title and a wrapping title. First use private grids, then subgrid. Say whether the two Save lines share a row.',
        ]),
        $L([
            'section' => 'Selectors that reach',
            'title' => 'Native nesting and the ampersand',
            'keywords' => 'css nesting, ampersand parent selector',
            'lead' => 'A style rule can contain another style rule. The inner selector is relative to the outer one. The ampersand stands for the parent selector. .card { &:hover {} } is .card:hover. .card { .title {} } is .card .title.',
            'heading' => 'The parent is a real selector, not a string you hope matches',
            'para' => 'Native nesting is the language, not a build step. The ampersand can sit in the middle: .card { .theme & {} } becomes .theme .card, which is how a theme wraps a component from the outside. Without the ampersand, a nested element selector is treated as a descendant. Put the ampersand in when you mean a compound, such as &:hover or &:focus-visible, because those attach to the parent instead of descending from it. Specificity is the specificity of the expanded selector. Nesting does not hide an ID. A nested rule that fails to parse drops that rule, not the parent block.',
            'visual' => tf_tree('nesting', 'The ampersand is the parent selector', ['.card', ':hover', '.title', '', '', '', ''], 'The left child attaches to the card. The right child descends into it. Specificity is the expanded selector, not a discount.'),
            'bullets' => [
                '&:hover attaches. A nested :hover without a careful ampersand is easy to misread. Write the ampersand.',
                '.theme & flips the relationship. The theme becomes the ancestor.',
                'At-rules such as @media can nest inside a selector. The selector does not have to be repeated inside the query.',
                'A preprocessor used to rewrite this at build time. The browser does it now. Do not ship both a rewritten file and a nested file for the same rules.',
            ],
            'rows' => [
                ['&', 'The parent selector', 'Not the parent element at runtime. The selector text.'],
                ['&:hover', 'Compound', '.card:hover.'],
                ['.title inside', 'Descendant', '.card .title.'],
                ['.theme &', 'Parent flips', '.theme .card.'],
                ['specificity', 'The expanded form', 'Nesting adds nothing extra and hides nothing.'],
                ['invalid inner rule', 'That rule drops', 'The outer rule remains.'],
            ],
            'e1' => ['Example 1 — hover attaches, title descends', 'One block styles the card, the hover, and the title.', <<<'CSS'
<style>
  .card { background: #f8fafc; padding: 12px; font-family: sans-serif; border: 2px solid #cbd5e1; }
  .card {
    &:hover { border-color: #059669; }
    & .title { color: #0284c7; font-weight: 700; }
  }
</style>
<div class="card"><div class="title">Hover the card</div><p>The title is a descendant.</p></div>
CSS],
            'e2' => ['Example 2 — the ampersand after a theme', 'The theme is outside the card in the selector, and outside it in the HTML.', <<<'CSS'
<style>
  .card { padding: 12px; font-family: sans-serif; background: white; }
  .card { .night & { background: #0f172a; color: #e2e8f0; } }
</style>
<div class="night"><div class="card">This card is inside .night, so the flipped selector matches.</div></div>
CSS],
            'micro' => [
                'Looking at the expanded selector in the inspector is the way to check a nest. The browser shows you what it built.',
                'Nesting a dozen levels recreates a specificity pile. Two levels is usually the readable maximum.',
                'An @media nested inside .card still uses the full .card selector. It does not apply to every element inside the query.',
            ],
            'quiz' => [
                $q('What does & stand for in a nested rule?', 'The parent selector', 'The parent element only at hover', 'Every div', 'The body'),
                $q('What does .card { .title {} } expand toward?', '.card .title', '.card.title', '.title .card', '.card + .title'),
                $q('Does nesting lower the specificity of an ID inside it?', 'No. The expanded selector keeps the ID', 'Yes, nesting resets specificity', 'Only in a layer', 'Only with :where'),
            ],
            'practice' => 'Nest a hover border and a title color inside one card rule. Then flip one rule so a .night ancestor changes the card background.',
        ]),
        $L([
            'section' => 'Selectors that reach',
            'title' => ':has() styles the parent from the child',
            'keywords' => 'css :has parent selector, form state',
            'lead' => ':has() matches an element that contains a match. .card:has(input:checked) styles the card when a box inside it is checked. The parent no longer needs a class from script to reflect a child.',
            'heading' => 'The condition is a selector, in the tree',
            'para' => 'The argument of :has() is a relative selector. .card:has(.title) is a card that contains a title. .card:has(+ .card) can also look at a sibling, because the relative selector may use a combinator. Specificity includes the most specific argument, the same way :is() does. The classic use is a form: form:has(:invalid) .save dims the button while a field is invalid. The cost is real on a huge document if you ask :has() questions that depend on the whole tree. Ask them from a component root, not from body for every hover. Browsers that lack :has() ignore the rule. Keep a default style that is acceptable without it.',
            'visual' => tf_flow('has', 'The child answers, the parent changes', [['child', 'checked'], [':has()', 'the card asks'], ['card', 'a new border']], 'The HTML does not grow a class. The selector looks down, and the declaration paints the ancestor.'),
            'bullets' => [
                ':has() is not a subject you style. You style the element outside :has(), using the answer.',
                'A checked input, an empty list, or a missing image are the usual questions.',
                'Do not combine it with a universal hover on every element of the page. Scope it to a component.',
                'Inside :is() or :where(), the specificity rules of those functions still apply.',
            ],
            'rows' => [
                [':has(.title)', 'Contains a title', 'The card is the subject.'],
                [':has(:checked)', 'Contains a checked control', 'The card can look selected.'],
                [':has(:invalid)', 'Contains an invalid field', 'The form can warn.'],
                ['specificity', 'The strongest argument', 'Same family as :is().'],
                ['sibling form', ':has(+ .card)', 'Looks sideways, not only down.'],
                ['fallback', 'The rule is ignored', 'The default look must stand alone.'],
            ],
            'e1' => ['Example 1 — the card reacts to its own checkbox', 'No script. The border changes because the card has a checked input.', <<<'CSS'
<style>
  .card { border: 2px solid #cbd5e1; padding: 12px; font-family: sans-serif; width: 220px; }
  .card:has(input:checked) { border-color: #e11d48; background: #fff1f2; }
</style>
<label class="card"><input type="checkbox" checked> Urgent</label>
CSS],
            'e2' => ['Example 2 — a form that contains an invalid field', 'The note shows only when something inside the form is invalid. The empty required field is invalid immediately.', <<<'CSS'
<style>
  form { font-family: sans-serif; }
  .warn { display: none; color: #e11d48; }
  form:has(:invalid) .warn { display: block; }
</style>
<form>
  <input type="email" required placeholder="email">
  <p class="warn">This form still has an invalid field.</p>
</form>
CSS],
            'micro' => [
                ':user-invalid is kinder than :invalid for the red border, because it waits until the person has edited the field. The next form lesson splits them.',
                'A :has() rule that changes a size which changes whether the :has() matches can cycle. Do not hide the very element you tested.',
                'In the inspector, the element that matches is the ancestor, not the child. Look at the card.',
            ],
            'quiz' => [
                $q('What does .card:has(input:checked) match?', 'A card that contains a checked input', 'The checked input itself', 'Every card', 'The body'),
                $q('Does the parent need a class toggled from script for this pattern?', 'No. The selector looks at the child', 'Yes, always', 'Only for checkboxes', 'Only in a form'),
                $q('How specific is :has()?', 'As specific as its most specific argument, like :is()', 'Always zero', 'Always one ID', 'It has no specificity'),
            ],
            'practice' => 'Build a card with a checkbox that is unchecked, then checked. Write the selector that paints the card, and say which element is the subject.',
        ]),
        $L([
            'section' => 'Selectors that reach',
            'title' => 'Logical properties follow the writing direction',
            'keywords' => 'css logical properties, margin-inline, padding-block, writing mode',
            'lead' => 'margin-left is a physical side. It stays on the left when the page is translated into a right-to-left language, which is often the wrong side. margin-inline-start is the start of the line. In a left-to-right page that is the left. In a right-to-left page it is the right.',
            'heading' => 'Inline is the line. Block is the stacking direction',
            'para' => 'Inline is the direction the words run. Block is the direction the paragraphs stack. padding-inline is both ends of the line. padding-block is the top and bottom in horizontal writing. inset-inline-start replaces left for an absolute box when you want the start side. border-inline-start is a bar at the start of the card. text-align: start follows the same axis. writing-mode: vertical-rl swaps the axes: block travels horizontally. Physical properties do not update when that happens. A design that must face both directions should be written in logical properties from the start. Adding them later means hunting every left and right.',
            'visual' => tf_panels('logical', 'Two axes, instead of four sides', 'inline', ['the line of text', 'start and end', 'left and right in English'], 'block', ['the stack of lines', 'start is the top in English', 'padding-block'], 'direction: rtl moves inline-start to the right. A physical left does not move.'),
            'bullets' => [
                'margin-inline: auto still centers a block, in both directions.',
                'The physical property and the logical property set the same side in English. The one you wrote last wins, because they share a computed side.',
                'A float still has inline-start. float: inline-start is the logical float.',
                'Resize, scroll, and overflow have logical longhands too. Learn the axes once and the names follow.',
            ],
            'rows' => [
                ['inline', 'Along the text', 'start, end, or both.'],
                ['block', 'Across the lines', 'Top and bottom in English.'],
                ['direction: rtl', 'Start swaps side', 'The logical bar moves. left does not.'],
                ['writing-mode', 'Swaps the axes', 'Block is no longer vertical.'],
                ['text-align: start', 'The start of the line', 'Not a hardcoded left.'],
                ['shared side', 'Physical and logical', 'They overwrite each other.'],
            ],
            'e1' => ['Example 1 — the bar is on the start side', 'English runs left to right, so inline-start is the left.', <<<'CSS'
<style>
  .card { padding-inline-start: 12px; border-inline-start: 6px solid #0284c7; font-family: sans-serif; }
</style>
<div class="card">The bar is at the start of the line.</div>
CSS],
            'e2' => ['Example 2 — the same declaration in a right-to-left card', 'You did not change the CSS of the card. direction moves the start side.', <<<'CSS'
<style>
  .card { padding-inline-start: 12px; border-inline-start: 6px solid #059669; font-family: sans-serif; direction: rtl; }
</style>
<div class="card">The bar follows the start side.</div>
CSS],
            'micro' => [
                'A left border that was meant as a selection marker stays on the left in Arabic and points at the wrong end of the sentence.',
                'width is physical in the sense of the horizontal axis. inline-size is the size along the line. In vertical writing they differ.',
                'Browser devtools can show logical properties mapped to physical ones. Trust the mapping when a side looks wrong.',
            ],
            'quiz' => [
                $q('What does margin-inline-start mean in a right-to-left paragraph?', 'The right side', 'Always the left side', 'The top', 'Both sides'),
                $q('Which axis runs along the words?', 'inline', 'block', 'z-index', 'outline'),
                $q('Why prefer logical properties in a design that will be translated?', 'The start side follows the language', 'They are shorter to type only', 'Physical properties are invalid', 'They disable the cascade'),
            ],
            'practice' => 'Put a start border on a card. Duplicate the card with direction: rtl and confirm the border moved. Then set border-left on a third card and watch it stay.',
        ]),
        $L([
            'section' => 'Selectors that reach',
            'title' => 'A container query measures the component',
            'keywords' => 'css container queries, container-type, inline-size',
            'lead' => 'A media query measures the viewport. A container query measures an ancestor you named. The same card can sit in a narrow sidebar or a wide main column and change its own layout, without knowing which page it is on.',
            'heading' => 'The children ask. The container answers',
            'para' => 'container-type: inline-size on the wrapper makes its content width available to queries. container-name gives it a name when more than one container is nested. @container (max-width: 240px) applies to descendants of the nearest matching container. Style the inside, not the container own width, or you build a cycle: the query depends on a size that the query is changing. A container with inline-size containment cannot be sized only by its contents in that axis. Give it a width from the grid or from the parent. Container query units cqi and cqw are a percentage of that container. 50cqi is half the container inline size, which is what a card wants more often than 50vw.',
            'visual' => tf_flow('container', 'The card asks its wrapper, not the window', [['wrapper', 'inline-size'], ['query', 'under 240?'], ['inside', 'stack or row']], 'A media query cannot see that this one card is in a sidebar. The container can.'),
            'bullets' => [
                'Name the container when two are nested, and query that name: @container card (min-width: 280px).',
                'The element you set container-type on is not the element that should change its own width from the query.',
                'A grid track of 1fr is a fine width for a container. The track is definite.',
                'Style queries exist too: @container style(--urgent: 1). Size queries are the ones in this lesson.',
            ],
            'rows' => [
                ['container-type', 'What is measured', 'inline-size is the useful one.'],
                ['container-name', 'Which ancestor', 'Needed when containers nest.'],
                ['@container', 'The question', 'Asked by the descendants.'],
                ['cycle', 'Querying your own width', 'Avoid it. Size the wrapper from outside.'],
                ['cqi', '1% of the container', 'Better than vw inside a component.'],
                ['fallback', 'The base style', 'The query only overrides.'],
            ],
            'e1' => ['Example 1 — a narrow wrapper hides the meta', 'The wrapper is 200 pixels. The query is true, so the meta is gone and the title is the whole card.', <<<'CSS'
<style>
  .frame { width: 200px; container-type: inline-size; }
  .title { font-family: sans-serif; font-weight: 700; }
  .meta { font-family: sans-serif; color: #64748b; }
  @container (max-width: 240px) {
    .meta { display: none; }
  }
</style>
<div class="frame"><div class="title">Milk</div><div class="meta">4 pages</div></div>
CSS],
            'e2' => ['Example 2 — a wider wrapper shows the row', 'The same card rules. The wrapper is 360 pixels, so the max-width query is false and a min-width query lays the card out in a row.', <<<'CSS'
<style>
  .frame { width: 360px; container-type: inline-size; }
  .card { font-family: sans-serif; }
  @container (min-width: 280px) {
    .card { display: flex; justify-content: space-between; gap: 12px; }
  }
</style>
<div class="frame"><div class="card"><div>Milk</div><div>4 pages</div></div></div>
CSS],
            'micro' => [
                'If both examples look the same, the container type is missing. A query with no container falls back to the viewport, or matches nothing, depending on the browser. Set the type.',
                'container-type: size contains both axes and needs a definite height too. inline-size is the one that does not need a height.',
                'A component library should document the container the component expects, the way it documents props.',
            ],
            'quiz' => [
                $q('What does a container query measure?', 'An ancestor you marked as a container', 'Only the viewport', 'The font size', 'The z-index'),
                $q('Why not change the container own width from its query?', 'The size would depend on itself', 'Queries cannot use pixels', 'inline-size forbids width', 'It deletes the children'),
                $q('What is 50cqi?', 'Half of the container inline size', 'Half of the viewport', '50 pixels', 'The 50th child'),
            ],
            'practice' => 'Wrap one card at 200 pixels and one at 360 pixels. Hide a line under 240 and show a row over 280. Confirm the viewport width did not decide it.',
        ]),
        $L([
            'section' => 'Selectors that reach',
            'title' => '@scope keeps a selector in one subtree',
            'keywords' => 'css @scope, scope proximity, lower boundary',
            'lead' => '@scope (.card) limits the rules inside it to elements in a card. A p selector in that scope does not restyle every paragraph on the page. An optional lower boundary stops the scope before a nested region, such as an embedded preview.',
            'heading' => 'The root includes. The limit excludes',
            'para' => '@scope (.card) to (.preview) matches elements inside a card that are not inside a preview. The preview can contain paragraphs that should keep the page style. Scope is not specificity. A scoped p is still just a p, so a class elsewhere can beat it. What scope adds, after specificity ties, is proximity: the element closer to its scope root beats a more distant scope when both match. That is how an inner card beats an outer card without a stronger selector. :scope inside the block refers to the root. It is the way to style the card element itself rather than only its descendants.',
            'visual' => tf_layers('scope', 'The inner boundary cuts the scope off', [
                ['page paragraph, outside the scope', '#f8fafc', '#64748b'],
                ['card, the scope root', '#d1fae5', '#059669'],
                ['preview, the lower boundary, excluded', '#fee2e2', '#e11d48'],
            ], 'Rules in the scope paint the card subtree and stop when they reach the preview.'),
            'bullets' => [
                'The specificity of a scoped element selector stays the element specificity. Scope does not smuggle in a class.',
                'Proximity breaks ties after specificity. It does not beat a class with an element.',
                'A missing lower boundary means the whole subtree, including nested cards, until proximity sorts them.',
                'If @scope is unsupported the block is ignored. Keep a class-based fallback for a rule that must work everywhere.',
            ],
            'rows' => [
                ['scope root', 'Where matching starts', 'The element in @scope ().'],
                ['lower boundary', 'Where matching stops', 'The to() selector.'],
                [':scope', 'The root itself', 'Style the card, not only the children.'],
                ['specificity', 'Unchanged', 'p is still one element.'],
                ['proximity', 'The closer root wins a tie', 'Inner card beats outer card.'],
                ['fallback', 'The block is dropped', 'Have a class rule if you must.'],
            ],
            'e1' => ['Example 1 — only paragraphs in the card', 'The paragraph outside the card keeps the default color.', <<<'CSS'
<style>
  p { font-family: sans-serif; }
  @scope (.card) {
    p { color: #059669; }
  }
</style>
<p>Outside, not green.</p>
<div class="card"><p>Inside the scope.</p></div>
CSS],
            'e2' => ['Example 2 — a lower boundary excludes the preview', 'The preview is inside the card and outside the scope.', <<<'CSS'
<style>
  p { font-family: sans-serif; }
  @scope (.card) to (.preview) {
    p { color: #059669; }
  }
</style>
<div class="card">
  <p>This paragraph is in scope.</p>
  <div class="preview"><p>This preview is past the boundary.</p></div>
</div>
CSS],
            'micro' => [
                'Scope proximity is easy to confuse with specificity. If a class is winning, proximity will not save the element selector.',
                'Donut scope is the name for a root plus a lower boundary. The preview pattern is the one to remember.',
                'Multiple roots mean multiple scopes. Each card on the page gets the rules. They do not share one match.',
            ],
            'quiz' => [
                $q('What does @scope (.card) to (.preview) exclude?', 'Elements inside .preview', 'The card itself only', 'Every paragraph', 'Elements before the card'),
                $q('Does a scoped p beat a class on that paragraph?', 'No. Specificity still favors the class', 'Yes, scope always wins', 'Only with a flag', 'Only the first time'),
                $q('What is scope proximity?', 'A tie-break that prefers the closer scope root', 'A new specificity column worth one ID', 'The source order', 'A media query'),
            ],
            'practice' => 'Color paragraphs inside .card and leave a .preview paragraph unchanged. Then add an outside paragraph and confirm it is unchanged too.',
        ]),
        $L([
            'section' => 'Selectors that reach',
            'title' => '@supports asks if a feature exists',
            'keywords' => 'css feature query, @supports, progressive enhancement',
            'lead' => '@supports asks the browser whether it understands a declaration. If it does, the rules inside apply. If it does not, the block is ignored and the simpler rule you wrote first remains. That is progressive enhancement as a language feature.',
            'heading' => 'Write the basic rule, then the question',
            'para' => 'A feature query tests a property and a value, not a browser name. @supports (display: grid) is true in a browser that implements grid. @supports not (display: grid) is the opposite. and and or combine tests. The useful shape is a solid layout that works everywhere, then a support block that upgrades it. Testing a value matters: a browser can know background and still not know color-mix(). Ask for the value you intend to use. A query that tests a property the browser knows, while you use a different value inside the block, will apply a declaration that then gets dropped. The test and the use should be the same feature.',
            'visual' => tf_split('supports', 'Does this browser understand the declaration?', 'feature available?', 'Use it', 'the newer layout', 'Keep the base', 'the rule you wrote first', 'You never ask for a browser name. You ask whether a property and a value parse.'),
            'bullets' => [
                'Put the fallback first in the file so it applies even when the query is ignored as unknown syntax.',
                'selector() tests a selector: @supports selector(:has(*))',
                'font-tech() and a few other functions test specific capabilities. The declaration form covers most questions.',
                'A query that is true does not make an invalid declaration inside it valid. The test should match the declaration.',
            ],
            'rows' => [
                ['(property: value)', 'The usual test', 'True when the pair parses.'],
                ['not', 'The opposite', 'The fallback branch.'],
                ['and / or', 'Combine tests', 'Both features, or either.'],
                ['selector()', 'Tests a selector', 'Useful for :has().'],
                ['fallback first', 'The base look', 'Still there when the block is skipped.'],
                ['same feature', 'Test what you use', 'Do not test grid and then use a different idea.'],
            ],
            'e1' => ['Example 1 — grid when it exists, blocks when it does not', 'The base is a simple stack. The support block upgrades the same element to two columns.', <<<'CSS'
<style>
  .box { font-family: sans-serif; }
  .box div { background: #e2e8f0; margin-bottom: 6px; padding: 8px; }
  @supports (display: grid) {
    .box { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
    .box div { margin-bottom: 0; background: #dbeafe; }
  }
</style>
<div class="box"><div>One</div><div>Two</div></div>
CSS],
            'e2' => ['Example 2 — ask for the value you will actually use', 'The swatch becomes a mixed color only if color-mix is understood. Otherwise it stays blue.', <<<'CSS'
<style>
  .swatch { width: 120px; height: 48px; background: #0284c7; }
  @supports (background: color-mix(in oklch, #0284c7 40%, white)) {
    .swatch { background: color-mix(in oklch, #0284c7 40%, white); }
  }
</style>
<div class="swatch"></div>
CSS],
            'micro' => [
                'Cutting a browser out by name fails the day that browser ships the feature. The query starts working on its own.',
                '@supports not (something brand new) is true in old browsers, and also true if you typo the property. A typo in the test silently takes the fallback branch. Check the spelling.',
                'Feature queries do not change specificity. They only decide whether the declarations exist.',
            ],
            'quiz' => [
                $q('What does @supports test?', 'Whether a declaration is understood', 'The browser brand name', 'The width of the screen', 'Whether the HTML is valid'),
                $q('Why write the simple rule before the support block?', 'So it remains when the block is skipped', 'Because order replaces specificity always', 'The spec requires red first', 'Queries only read the first rule'),
                $q('What should the test mention?', 'The same feature the block is about to use', 'display: block, always', 'The file name', 'z-index'),
            ],
            'practice' => 'Lay out two boxes as a stack, then upgrade them to a two-column grid inside @supports. Change the tested value to a nonsense word and confirm the stack returns.',
        ]),
        $L([
            'section' => 'Color you can calculate',
            'title' => 'oklch and color-mix',
            'keywords' => 'css oklch, color-mix, perceptually uniform color',
            'lead' => 'oklch describes a color as lightness, chroma, and hue. Lightness 0 is black and 1 is white, and the steps between them look even. color-mix blends two colors in a space you name. Mixing in oklch does not turn bright blue and yellow into a muddy middle the way a raw RGB mix can.',
            'heading' => 'Pick a hue, then change only the lightness',
            'para' => 'oklch(0.7 0.15 150) is a green: lightness 0.7, chroma 0.15, hue 150. Lower the chroma toward 0 and the color becomes gray without changing lightness. Spin the hue and the family changes. A palette is one hue, three lightnesses, not three unrelated hex codes. color-mix(in oklch, #0284c7 30%, white) is 30 percent of the blue and the rest white, measured in oklch. The percentage is the first color share. Hex still works as an input. The space is how the mix is computed. A color the screen cannot show is gamut-mapped. For a brand color that must be exact, check the mapped result on a real screen rather than trusting the numbers alone.',
            'visual' => tf_cells('oklch', 'Lightness, chroma, hue', [['L', '0.70'], ['C', '0.15'], ['H', '150']], 'Change L to make a lighter or darker partner. Change H to leave the green family. C near 0 is gray.', [0], 'oklch()'),
            'bullets' => [
                'hsl lightness is not even. A step from 40 to 50 does not look like a step from 80 to 90. oklch lightness does.',
                'The mix percentage belongs to the first color. 30% blue means 70% of the second color.',
                'in srgb is available when you want the old mix. Say the space on purpose.',
                'relative color syntax can take a color and replace one component. oklch(from blue 0.7 c h) keeps chroma and hue and sets lightness.',
            ],
            'rows' => [
                ['L', 'Lightness 0 to 1', 'The even steps.'],
                ['C', 'Chroma', '0 is gray. Too high may be outside the screen.'],
                ['H', 'Hue in degrees', '150 is a green. 250 is a blue.'],
                ['color-mix', 'Two colors and a space', 'The percentage is the first color.'],
                ['in oklch', 'The mixing space', 'The usual choice for UI tints.'],
                ['gamut map', 'The screen compromise', 'Check a brand color on a device.'],
            ],
            'e1' => ['Example 1 — one hue, two lightnesses', 'Both swatches are hue 250. Only the lightness changes.', <<<'CSS'
<style>
  .row { display: flex; gap: 8px; }
  .a, .b { width: 100px; height: 56px; }
  .a { background: oklch(0.45 0.15 250); }
  .b { background: oklch(0.8 0.08 250); }
</style>
<div class="row"><div class="a"></div><div class="b"></div></div>
CSS],
            'e2' => ['Example 2 — a 30 percent tint', 'The third swatch is the mix. It should sit between the blue and the white.', <<<'CSS'
<style>
  .row { display: flex; gap: 8px; }
  .row div { width: 100px; height: 56px; border: 1px solid #e2e8f0; }
  .ink { background: #0284c7; }
  .paper { background: white; }
  .tint { background: color-mix(in oklch, #0284c7 30%, white); }
</style>
<div class="row"><div class="ink"></div><div class="tint"></div><div class="paper"></div></div>
CSS],
            'micro' => [
                'A very high chroma is clipped on common screens. If two chromas look identical, the screen could not show the higher one.',
                'Do not mix a light and a dark theme by averaging hex in sRGB. Mix in oklch, or use light-dark() for the pair.',
                'Contrast is still a lightness difference you have to check. A pretty palette can fail the text.',
            ],
            'quiz' => [
                $q('What does the first number in oklch(0.7 0.15 150) mean?', 'Lightness', 'Hue', 'Alpha', 'A pixel width'),
                $q('In color-mix(in oklch, blue 30%, white), what does 30% refer to?', 'The share of the first color', 'The lightness', 'The hue degrees', 'The opacity of the element'),
                $q('Why mix UI tints in oklch rather than raw RGB?', 'The steps in lightness look more even', 'RGB is invalid', 'oklch ignores hue', 'It removes the need for contrast'),
            ],
            'practice' => 'Make three swatches of hue 150 with lightness 0.4, 0.7, and 0.9. Then mix your middle color 40 percent toward white and compare.',
        ]),
        $L([
            'section' => 'Color you can calculate',
            'title' => 'light-dark() follows the color scheme',
            'keywords' => 'css light-dark, color-scheme, dark theme',
            'lead' => 'light-dark(white, #0f172a) picks the first color in a light scheme and the second in a dark scheme. It looks at the color-scheme property, not at a class you toggle, unless that class is what sets the scheme.',
            'heading' => 'The element has to opt in to both schemes',
            'para' => 'If color-scheme is only light, light-dark() stays on the light color even when the operating system is dark. Set color-scheme: light dark on the root so the used scheme follows the system. Set color-scheme: dark on one card to force the dark color inside a light page, which is the demo and also how a code block stays dark. The function replaces pairs of custom properties for simple colors. It does not replace a whole theme of shadows, pictures, and borders unless each of those uses the function or a variable that does. A picture that is a screenshot of a light interface is still light. color-scheme also changes the user-agent form controls and the canvas background, which is why the property matters even before light-dark().',
            'visual' => tf_panels('lightdark', 'One declaration, two schemes', 'light', ['color-scheme includes light', 'the first color', 'white page'], 'dark', ['color-scheme resolves to dark', 'the second color', 'a dark card on purpose'], 'Without both keywords on color-scheme, the function cannot switch.'),
            'bullets' => [
                'The first argument is light. The second is dark. Swapping them is a quiet bug.',
                'A .dark class that sets color-scheme: dark is enough. You do not also need a media query for that card.',
                'prefers-color-scheme in a media query is the older tool. light-dark() is the value-level tool.',
                'System colors such as Canvas and CanvasText follow the scheme too, and they help in forced colors.',
            ],
            'rows' => [
                ['first argument', 'The light color', 'Used when the scheme is light.'],
                ['second argument', 'The dark color', 'Used when the scheme is dark.'],
                ['color-scheme', 'The switch', 'Must allow the scheme you want.'],
                ['light dark', 'Follow the system', 'The usual root value.'],
                ['forced on one card', 'color-scheme: dark', 'A dark code sample on a light page.'],
                ['form controls', 'Follow the property', 'Native inputs restyle themselves.'],
            ],
            'e1' => ['Example 1 — this card is forced light', 'Both colors are in the function. The scheme only allows light, so the card stays white.', <<<'CSS'
<style>
  .card { color-scheme: light; background: light-dark(#ffffff, #0f172a); color: light-dark(#0f172a, #e2e8f0); padding: 16px; font-family: sans-serif; border: 1px solid #cbd5e1; }
</style>
<div class="card">Light scheme, first color.</div>
CSS],
            'e2' => ['Example 2 — the same function, scheme dark', 'Only color-scheme changed. The second color is used.', <<<'CSS'
<style>
  .card { color-scheme: dark; background: light-dark(#ffffff, #0f172a); color: light-dark(#0f172a, #e2e8f0); padding: 16px; font-family: sans-serif; }
</style>
<div class="card">Dark scheme, second color.</div>
CSS],
            'micro' => [
                'A root with color-scheme: light dark and a card that forgets to inherit can still be light if something sets light only. Check the computed color-scheme.',
                'Do not use light-dark() for a color that should stay the same, such as a logo red. Write that color normally.',
                'Pictures and gradients need their own pair. One function on the background does not recolor the image pixels.',
            ],
            'quiz' => [
                $q('Which argument does light-dark() use when the used scheme is dark?', 'The second', 'The first', 'A mix of both', 'Always black'),
                $q('Why might light-dark() ignore a dark operating system?', 'color-scheme does not allow dark', 'The function only works on borders', 'The first color is invalid', 'Hue is too high'),
                $q('How do you force one card to the dark color on a light page?', 'Set color-scheme: dark on that card', 'Add a second body', 'Use z-index', 'Set opacity to 0'),
            ],
            'practice' => 'Build the two cards from the examples. Then set the root to color-scheme: light dark and remove the card scheme, and see which color the system gives you.',
        ]),
        $L([
            'section' => 'Color you can calculate',
            'title' => '@property makes a variable animatable',
            'keywords' => 'css @property, registered custom properties, typed variables',
            'lead' => 'A custom property is text until you register it. The browser cannot animate text from 0deg to 360deg, so the value jumps or the animation does nothing. @property gives the variable a type, an initial value, and an inheritance choice, and then it can interpolate.',
            'heading' => 'The type is what makes the in-between real',
            'para' => 'syntax: "<angle>" tells the engine the variable holds an angle. initial-value is required, and it must match the syntax, or the registration fails quietly. inherits: false keeps the variable on the element you set, which is what you want for a dial on one card. Once registered, an invalid assignment uses the initial value instead of becoming the guaranteed-invalid value that triggers a var() fallback. That is the trade. You gain animation and lose the fallback behavior of an untyped variable. Register the properties you intend to animate. Leave the rest untyped so a typo still falls back.',
            'visual' => tf_split('atprop', 'Can the engine interpolate this variable?', 'registered angle?', 'Animate', 'the degrees are real', 'Jump', 'it is only text', 'Untyped custom properties animate as discrete strings. A syntax makes the in-between exist.'),
            'bullets' => [
                'syntax can be "<color>", "<length>", "<number>", or a list such as "<number> | <length>".',
                'The initial value is used when the property is not set, and when the set value is invalid.',
                'Registering on the page does not set the value on an element. You still write --turn: 20deg on the dial.',
                'An unregistered animation of a color variable will not fade. It will flip at the keyframe boundary.',
            ],
            'rows' => [
                ['syntax', 'The type', 'The engine needs it to blend.'],
                ['inherits', 'Whether children receive it', 'false for a local dial.'],
                ['initial-value', 'The stand-in', 'Must match the syntax.'],
                ['invalid set', 'Becomes the initial', 'The var() fallback will not run.'],
                ['untyped', 'Stays text', 'Good for keywords and fallbacks.'],
                ['keyframe', 'Sets the variable', 'The painted property reads it with var().'],
            ],
            'e1' => ['Example 1 — a registered angle turns smoothly', 'The conic gradient reads --turn. The animation updates the angle every frame.', <<<'CSS'
<style>
  @property --turn {
    syntax: "<angle>";
    inherits: false;
    initial-value: 0deg;
  }
  .dial {
    width: 88px; height: 88px; border-radius: 50%;
    background: conic-gradient(#059669 var(--turn), #e2e8f0 0);
    animation: spin 2s linear infinite;
  }
  @keyframes spin { to { --turn: 360deg; } }
</style>
<div class="dial"></div>
CSS],
            'e2' => ['Example 2 — the same animation without a type', 'Nothing registers --turn. The gradient does not sweep. The variable is text.', <<<'CSS'
<style>
  .dial {
    --turn: 0deg;
    width: 88px; height: 88px; border-radius: 50%;
    background: conic-gradient(#94a3b8 var(--turn), #e2e8f0 0);
    animation: spin 2s linear infinite;
  }
  @keyframes spin { to { --turn: 360deg; } }
</style>
<div class="dial"></div>
CSS],
            'micro' => [
                'If example 1 is a static quarter or a static color, this browser is not interpolating the registered property. The lesson still stands: check the computed type.',
                'inherits: true on a color token is usually what you want. A theme sets it on the root and the tree receives it.',
                'Do not register every token. A registered invalid value hides mistakes that an untyped fallback would have shown.',
            ],
            'quiz' => [
                $q('Why register a custom property before animating it?', 'So the engine knows the type and can interpolate it', 'So it becomes !important', 'So it stops inheriting colors', 'Registration deletes the cascade'),
                $q('What happens to an invalid value of a registered property?', 'It falls back to the initial value', 'It always uses the next rule in the file', 'The element is removed', 'It becomes a random color'),
                $q('What must initial-value do?', 'Match the syntax', 'Be a hex color in every case', 'Be zero', 'Match the class name'),
            ],
            'practice' => 'Register an angle and animate a conic gradient. Remove the @property block and describe what the dial does.',
        ]),
        $L([
            'section' => 'The person and the screen',
            'title' => ':focus-visible is the outline you keep',
            'keywords' => 'css focus-visible, outline, keyboard focus',
            'lead' => ':focus matches whenever the element is focused, including a mouse click on a button. :focus-visible matches when the browser decides the person needs a visible ring, which is the keyboard case. Removing every outline and adding nothing back hides the keyboard cursor.',
            'heading' => 'Replace the ring. Do not delete the only indicator',
            'para' => 'The safe pattern is outline: none on :focus, and a clear outline or box-shadow on :focus-visible. Mouse users do not see a ring on click. Keyboard users see one. The ring must have contrast against both the button and the page behind it, because a blue ring on a blue button is not an indicator. :focus-within on a field wrapper lights the whole label group when the input inside is focused. That is a parent state, and it is the right tool for a bordered field. Do not remove the input ring and forget the wrapper. A person tabbing through the form has to be able to see where they are at every stop, including links that are only an icon.',
            'visual' => tf_panels('focusring', 'Two ways an element receives focus', ':focus', ['mouse click counts', 'keyboard counts', 'a ring on every click is noisy'], ':focus-visible', ['the browser asks who needs the ring', 'keyboard gets it', 'the indicator stays'], 'outline: none with no replacement removes the keyboard cursor.'),
            'bullets' => [
                'outline does not take layout space. A custom ring that uses border will shift the button unless you reserved the space.',
                'box-shadow as a ring does not follow border-radius in older tricks the way outline-offset does. outline is the simple tool.',
                ':focus-visible on a custom control depends on the control being focusable. A div needs tabindex="0" and a role, or it is not in the tab order.',
                'The forced-colors lesson overrides colors. A ring that is only a background change can disappear there. Keep an outline.',
            ],
            'rows' => [
                [':focus', 'Any focus', 'Includes the mouse.'],
                [':focus-visible', 'A visible indicator is wanted', 'The keyboard case.'],
                ['outline: none', 'Dangerous alone', 'Always pair it with a replacement.'],
                [':focus-within', 'A child is focused', 'Light up the field wrapper.'],
                ['offset', 'Space around the ring', 'outline-offset pulls it off the edge.'],
                ['contrast', 'The ring must show', 'Against the control and the page.'],
            ],
            'e1' => ['Example 1 — click stays quiet, the keyboard ring remains', 'Tab to the button. The ring is the focus-visible rule. A mouse click does not have to keep a permanent extra border in this pattern.', <<<'CSS'
<style>
  button { font-family: sans-serif; padding: 8px 14px; border: 0; background: #0284c7; color: white; }
  button:focus { outline: none; }
  button:focus-visible { outline: 3px solid #0f172a; outline-offset: 3px; }
</style>
<button>Save</button>
CSS],
            'e2' => ['Example 2 — the wrapper shows where the field is', 'The input keeps a ring. The label row also changes, because of focus-within.', <<<'CSS'
<style>
  .field { font-family: sans-serif; padding: 8px; border: 2px solid #cbd5e1; width: 220px; }
  .field:focus-within { border-color: #0284c7; }
  input:focus-visible { outline: 2px solid #0284c7; }
</style>
<label class="field">Title <input></label>
CSS],
            'micro' => [
                'Test with the keyboard. A design review that only clicks will never see a missing ring.',
                'outline: none in a reset stylesheet is the usual source of the bug. Search the reset before you add rings at random.',
                'A skip link needs a visible focus style more than any other control. It is hidden until focused, and then it must be obvious.',
            ],
            'quiz' => [
                $q('Who is :focus-visible mainly for?', 'A person who needs a visible ring, usually keyboard focus', 'Only mouse users', 'Only printed pages', 'Elements with no tabindex that are not focusable'),
                $q('What is wrong with outline: none and no replacement?', 'Keyboard users cannot see where focus is', 'The button becomes more specific', 'It deletes the button', 'It sets a dark color scheme'),
                $q('What does :focus-within match?', 'An element whose descendant is focused', 'Only the document root', 'Only :hover', 'A parent that has no children'),
            ],
            'practice' => 'Remove the outline from a button and tab to it. Put the ring back on :focus-visible only, and confirm a mouse click and a tab stop look different.',
        ]),
        $L([
            'section' => 'The person and the screen',
            'title' => 'Reduced motion, more contrast, forced colors',
            'keywords' => 'prefers-reduced-motion, prefers-contrast, forced-colors',
            'lead' => 'The operating system already knows whether a person wants less motion, more contrast, or a forced palette. CSS can ask. A hover that slides, fades, and scales should become a still color change for someone who asked for reduced motion.',
            'heading' => 'Ask the system, and keep the information',
            'para' => 'prefers-reduced-motion: reduce means cut the travel, not the feedback. A button can still change color. It should not travel across the card. prefers-contrast: more means the person asked for stronger contrast. Increase a border rather than fading a gray. forced-colors: active means the browser is replacing your colors with a limited set, often because of a high-contrast theme. Backgrounds you set may be ignored. Borders and text use Canvas, CanvasText, LinkText, and ButtonText. An icon that was only a background image can vanish. Give it a border, or set forced-color-adjust: none on a swatch that must keep its real color, and do that rarely. The information has to survive the palette change.',
            'visual' => tf_flow('motionpref', 'The same control, three honest questions', [['motion', 'reduce travel'], ['contrast', 'strengthen the edge'], ['forced', 'keep the meaning']], 'The preference is not a theme you invented. It is a request from the person, already set on the machine.'),
            'bullets' => [
                'Put the reduced-motion override after the animation, so it wins by order inside the same specificity.',
                'Do not set animation-duration to zero on every element if a opacity fade was carrying the only state change. Replace it with an instant state that is still visible.',
                'forced-color-adjust: none opts a box out of the replacement. Use it for a color sample, not for the whole page.',
                'A media query you cannot trigger in the preview can be simulated in the rendering panel of the inspector.',
            ],
            'rows' => [
                ['reduce', 'Less motion', 'Keep the state change. Drop the travel.'],
                ['no-preference', 'The full motion', 'The default branch.'],
                ['more contrast', 'Stronger separation', 'Borders and text, not thinner grays.'],
                ['forced-colors', 'A replaced palette', 'Your hex may be ignored.'],
                ['CanvasText', 'The text color of that palette', 'Use it instead of fighting the theme.'],
                ['forced-color-adjust', 'Opt out of one box', 'Rare. A swatch, not the layout.'],
            ],
            'e1' => ['Example 1 — the card lifts, unless motion is reduced', 'The hover travels. The media query removes the transition and the offset. The border still changes.', <<<'CSS'
<style>
  .card { font-family: sans-serif; padding: 12px; border: 2px solid #cbd5e1; transition: transform 0.3s; }
  .card:hover { transform: translateY(-8px); border-color: #0284c7; }
  @media (prefers-reduced-motion: reduce) {
    .card { transition: none; }
    .card:hover { transform: none; }
  }
</style>
<div class="card">Hover. The border is the part that must remain.</div>
CSS],
            'e2' => ['Example 2 — the same idea as a class you can see here', 'The preview may not have the preference turned on. This class shows the reduced result without the operating system.', <<<'CSS'
<style>
  .card { font-family: sans-serif; padding: 12px; border: 2px solid #0284c7; transform: none; }
</style>
<div class="card">Still. The state would be the border, not a slide.</div>
CSS],
            'micro' => [
                'Test reduced motion from the operating system or the inspector. Do not only trust the class in example 2. That class is a picture of the result.',
                'Autoplaying carousels should stop under reduced motion. A paused control with a next button keeps the information.',
                'In forced colors, a selected tab that was only a pale background needs a border or an underline that the palette will keep.',
            ],
            'quiz' => [
                $q('What should reduced motion remove?', 'Travel and extra animation, while the state stays visible', 'Every hover style, including color', 'The button', 'The font'),
                $q('What does forced-colors do to many of your background colors?', 'The browser may replace them with the forced palette', 'It deletes the HTML', 'It sets opacity to zero only', 'Nothing'),
                $q('When is forced-color-adjust: none reasonable?', 'On a small swatch that must show a real color', 'On the body, always', 'On every paragraph', 'Never, it is invalid'),
            ],
            'practice' => 'Add a hover lift to a card and a reduced-motion block that keeps a border change. Turn the preference on in the inspector and confirm the card no longer travels.',
        ]),
        $L([
            'section' => 'The person and the screen',
            'title' => 'Scroll snap parks the next card',
            'keywords' => 'css scroll-snap, scroll-padding, carousel',
            'lead' => 'Scroll snap pulls a scrolling box so that a card lands on a line you chose, instead of stopping half visible. The scroller sets scroll-snap-type. Each child sets scroll-snap-align. Mandatory snap always finishes on a snap point. Proximity snaps only when the person is already close.',
            'heading' => 'The scroller and the children share the job',
            'para' => 'overflow must allow scrolling, or there is nothing to snap. A row needs overflow-x: auto and scroll-snap-type: x mandatory. Each card uses scroll-snap-align: start and a flex basis so the next card peeks in. scroll-padding on the scroller insets the snap line, which is how a card clears a sticky header instead of hiding under it. snap stops a flick from landing between cards. It does not replace a next button for someone who does not drag. A mandatory snap inside a page that also scrolls vertically can steal a vertical gesture if you snap both axes. Snap the axis you mean.',
            'visual' => tf_cells('snap', 'The flick ends on a card, not between them', [['1', 'A'], ['2', 'B'], ['3', 'C']], 'The scroller declares the axis and the strictness. The card declares which of its edges parks on the line.', [0], 'start'),
            'bullets' => [
                'scroll-snap-stop: always refuses to skip a card during a fast flick. The default allows skipping.',
                'A tall page of sections uses scroll-snap-type: y proximity so a small scroll does not jump a whole screen.',
                'scroll-padding-top should match the sticky header height.',
                'Keyboard users still need to reach every card. Snap is not focus management.',
            ],
            'rows' => [
                ['scroll-snap-type', 'Axis and strictness', 'x or y, mandatory or proximity.'],
                ['scroll-snap-align', 'Which edge parks', 'start, center, or end.'],
                ['overflow', 'Required', 'The box has to scroll.'],
                ['scroll-padding', 'Insets the snap line', 'Clear a sticky header.'],
                ['snap-stop', 'Do not skip', 'always stops on each item.'],
                ['a button', 'Still useful', 'Drag is not the only input.'],
            ],
            'e1' => ['Example 1 — a row that parks on each card', 'Drag sideways. A card settles at the left edge instead of half in and half out.', <<<'CSS'
<style>
  .row { display: flex; gap: 8px; width: 240px; overflow-x: auto; scroll-snap-type: x mandatory; font-family: sans-serif; }
  .row div { flex: 0 0 80%; scroll-snap-align: start; background: #dbeafe; padding: 16px; }
</style>
<div class="row"><div>One</div><div>Two</div><div>Three</div></div>
CSS],
            'e2' => ['Example 2 — padding keeps the card off the edge', 'scroll-padding-inline-start insets the line. The card parks 16 pixels in.', <<<'CSS'
<style>
  .row { display: flex; gap: 8px; width: 240px; overflow-x: auto; scroll-snap-type: x mandatory; scroll-padding-inline-start: 16px; font-family: sans-serif; }
  .row div { flex: 0 0 70%; scroll-snap-align: start; background: #d1fae5; padding: 16px; }
</style>
<div class="row"><div>One</div><div>Two</div><div>Three</div></div>
CSS],
            'micro' => [
                'If the row does not snap, check that the children are wide enough to overflow. A row that fits does not scroll.',
                'scroll-behavior: smooth changes how a script-driven scroll feels. It should be disabled under reduced motion.',
                'Nested scrollers both set to mandatory snap fight each other. Snap the inner carousel and let the page scroll normally.',
            ],
            'quiz' => [
                $q('Which element sets scroll-snap-type?', 'The scrolling container', 'Only the last child', 'The body, always', 'The font'),
                $q('What is the difference between mandatory and proximity?', 'Mandatory always finishes on a snap point. Proximity snaps when you are already close', 'They are identical', 'Proximity disables overflow', 'Mandatory only works vertically'),
                $q('What is scroll-padding for?', 'Moving the snap line in from the edge, so a header does not cover the card', 'Adding margin collapse', 'Setting z-index', 'Hiding the scrollbar only'),
            ],
            'practice' => 'Build a horizontal snap row with three cards. Add scroll-padding and say which edge the card parks against.',
        ]),
        $L([
            'section' => 'The person and the screen',
            'title' => 'aspect-ratio holds the box before the picture',
            'keywords' => 'css aspect-ratio, object-fit, layout shift',
            'lead' => 'A box with no height and no content collapses. When a picture arrives later, the page jumps. aspect-ratio reserves the rectangle first. object-fit decides how a picture paints inside a frame that does not match its own pixels.',
            'heading' => 'Reserve the frame, then fit the pixels',
            'para' => 'aspect-ratio: 16 / 9 with a width and an auto height computes the height from the width. The layout is stable before the image loads. If you also set a height, the ratio is a preferred size and the used size may differ. object-fit: cover fills the frame and crops. contain shows the whole picture and may leave bars. fill stretches, which is the old default and the one that distorts faces. object-position chooses which part survives a cover crop. A video and a canvas use the same pair. The ratio belongs on the element that has the width, which is often a wrapper when the image itself is width 100 percent.',
            'visual' => tf_panels('ratio', 'The frame first, the pixels second', 'aspect-ratio', ['reserves the rectangle', 'height follows width', 'the page does not jump'], 'object-fit', ['cover crops', 'contain letterboxes', 'fill stretches'], 'A missing ratio is a jump. The wrong fit is a distorted or cropped picture.'),
            'bullets' => [
                'The replaced element and a wrapper can both have a ratio. One is enough. Two can fight.',
                'aspect-ratio does not crop. object-fit crops or letterboxes.',
                'A square avatar is aspect-ratio: 1 and object-fit: cover inside a circle of border-radius.',
                'If the image has width and height attributes, the browser can reserve space even before CSS. The CSS ratio is still the design size.',
            ],
            'rows' => [
                ['16 / 9', 'Width to height', 'The slash form is the readable one.'],
                ['width set', 'Height is computed', 'The usual pattern.'],
                ['cover', 'Fill and crop', 'No empty bars.'],
                ['contain', 'Show everything', 'Bars are possible.'],
                ['fill', 'Stretch', 'The distortion.'],
                ['object-position', 'The anchor of the crop', 'center is the default.'],
            ],
            'e1' => ['Example 1 — the frame exists before any picture', 'The blue box is 16 by 9. The gray line has no ratio and no height, so it contributes almost nothing.', <<<'CSS'
<style>
  .frame { width: 240px; aspect-ratio: 16 / 9; background: #0284c7; }
  .flat { width: 240px; background: #e2e8f0; margin-top: 8px; }
</style>
<div class="frame"></div>
<div class="flat"></div>
CSS],
            'e2' => ['Example 2 — cover crops a gradient stand-in', 'The inner box is taller than the frame. object-fit is for replaced elements, so this demo uses a wrapper ratio and overflow hidden, which is the same crop idea for a plain box.', <<<'CSS'
<style>
  .frame { width: 180px; aspect-ratio: 1; overflow: hidden; border-radius: 12px; }
  .pic { width: 100%; height: 140%; background: linear-gradient(#059669, #0284c7); }
</style>
<div class="frame"><div class="pic"></div></div>
CSS],
            'micro' => [
                'For a real image, img { width: 100%; height: 100%; object-fit: cover; } inside a frame that has the ratio.',
                'aspect-ratio is ignored if both width and height are set to definite lengths. One axis needs to be free.',
                'A late-loading font can still move text. The ratio fixes the media frame, not the line box.',
            ],
            'quiz' => [
                $q('What problem does aspect-ratio solve for a picture that loads late?', 'It reserves the rectangle so the page does not jump', 'It compresses the file', 'It sets a caption', 'It disables cache'),
                $q('What does object-fit: cover do?', 'It fills the frame and crops the overflow', 'It shows the whole picture with possible bars', 'It stretches the picture to the frame', 'It removes the image'),
                $q('When is the ratio ignored?', 'When both width and height are already definite lengths', 'When the color is blue', 'When the element is a div', 'Never'),
            ],
            'practice' => 'Make a 16 by 9 frame at 240 pixels wide. Put an image or a tall gradient inside a square frame with overflow hidden and describe what was cut off.',
        ]),
        $L([
            'section' => 'The person and the screen',
            'title' => 'text-wrap balance keeps a heading even',
            'keywords' => 'css text-wrap balance pretty, orphans, headings',
            'lead' => 'A heading in a narrow column often leaves one word on the last line. text-wrap: balance evens the line lengths. text-wrap: pretty avoids a lonely last word in a paragraph. They are wrapping rules, not a change to the font.',
            'heading' => 'Balance is for short text. Pretty is for paragraphs',
            'para' => 'balance asks the browser to share the words across the lines so the block looks even. Browsers limit how many lines they will balance, because the work grows with the text. Use it on headings and card titles, not on a long article. pretty is aimed at body text and spends effort avoiding an orphan, the single word on the last line. It can be slower on a huge page, so use it on the article and not on every label in a data grid. white-space and overflow still win if you set nowrap. A balanced heading can become one line taller than an unbalanced one. If the card height was fixed, the extra line will overflow. Let the card grow, or use the line clamp from the overflow lesson.',
            'visual' => tf_panels('wrap', 'The same words, a different wrap', 'default', ['the first lines are full', 'one word remains', 'the rag looks accidental'], 'balance', ['lines share the words', 'meant for headings', 'there is a line limit'], 'pretty is the paragraph tool. It spends its effort on the last line.'),
            'bullets' => [
                'text-wrap: wrap is the normal wrapping. nowrap is one line. balance and pretty are the newer policies.',
                'A max-width on the heading is still useful. Balance does not choose the column width.',
                'Hyphens are a different property. Balance will not add a hyphen. It moves whole words.',
                'If the heading is a single short line, balance has nothing to do.',
            ],
            'rows' => [
                ['balance', 'Even line lengths', 'Headings and titles.'],
                ['pretty', 'Avoid a lonely last word', 'Paragraphs.'],
                ['line limit', 'Browsers cap balance', 'Do not use it on long articles.'],
                ['nowrap', 'Wins over balance', 'There is only one line to wrap.'],
                ['height', 'May grow by a line', 'Do not trap it in a fixed card.'],
                ['hyphens', 'A different feature', 'Balance moves words. It does not split them.'],
            ],
            'e1' => ['Example 1 — a ragged heading', 'The column is narrow. The last line is a single short word.', <<<'CSS'
<style>
  h2 { width: 160px; font-family: sans-serif; font-size: 22px; margin: 0; }
</style>
<h2>Notes from the morning lab</h2>
CSS],
            'e2' => ['Example 2 — balance shares the lines', 'The same width and the same words. The lines are closer in length.', <<<'CSS'
<style>
  h2 { width: 160px; font-family: sans-serif; font-size: 22px; margin: 0; text-wrap: balance; }
</style>
<h2>Notes from the morning lab</h2>
CSS],
            'micro' => [
                'If both headings look identical, the words already fit evenly, or this browser does not implement balance yet. Widen the phrase or narrow the box.',
                'text-wrap: pretty on a thousand-row table is wasted work. Scope it to the article.',
                'Centering a balanced heading looks calmer than centering a ragged one. The wrap is doing more of the work than text-align.',
            ],
            'quiz' => [
                $q('What is text-wrap: balance for?', 'Evening out the lines of a short heading', 'Justifying the last line of a novel', 'Removing wrapping', 'Changing the font'),
                $q('Why avoid balance on a long article?', 'Browsers limit the lines they will balance, and the work grows', 'It deletes paragraphs', 'It forces nowrap', 'It sets the color to gray'),
                $q('What does pretty try to avoid?', 'A single word on the last line', 'All punctuation', 'Bold text', 'Images'),
            ],
            'practice' => 'Set a heading to 160 pixels wide with and without balance. Count the words on the last line in each case.',
        ]),
        $L([
            'section' => 'The person and the screen',
            'title' => 'content-visibility skips work off screen',
            'keywords' => 'css content-visibility, contain-intrinsic-size, contain',
            'lead' => 'content-visibility: auto lets the browser skip layout and paint for an element that is off screen. The skipped element still needs a size, or the scrollbar jumps when it comes into view. contain-intrinsic-size is that placeholder size.',
            'heading' => 'Skip the work, and reserve the space',
            'para' => 'auto means: when the element is near the screen, render it, and when it is far, remember a size and skip the internals. contain-intrinsic-size: auto 400px uses 400 pixels until the element has been rendered once, then remembers the real size. Without the placeholder, every skipped section has no height and the page looks short, then grows as you scroll. contain: paint is the related switch you can see in a small demo. It clips descendants the way overflow: hidden does, and it is one of the containments content-visibility implies. content-visibility is for long feeds and long articles, not for a three-card page. A skipped element is not displayed none. Find-in-page and assistive technology can still reach it in current browsers, which is the point of auto rather than hiding the section.',
            'visual' => tf_flow('cvis', 'Off screen, keep a size and skip the inside', [['far', 'skip layout'], ['size', 'the placeholder'], ['near', 'render for real']], 'The placeholder is why the scrollbar stays honest. Without it, the page grows as each section is approached.'),
            'bullets' => [
                'Use it on self-contained sections, not on a table row that must align with a row outside it.',
                'contain-intrinsic-size: auto plus a length is the form that remembers the last real size.',
                'A section with content-visibility: hidden is not rendered and not accessible. auto is the one for a feed.',
                'Measure before you add it. A short page spends more time on the switch than it saves.',
            ],
            'rows' => [
                ['auto', 'Skip when far', 'Render when near.'],
                ['placeholder size', 'The reserved height', 'Stops the scrollbar jump.'],
                ['auto keyword', 'Remember the real size', 'After the first render.'],
                ['contain: paint', 'Clip descendants', 'Visible in a small demo.'],
                ['hidden', 'Not rendered', 'Also hidden from assistive tech. Rare.'],
                ['the wrong target', 'A tiny page', 'No work to skip.'],
            ],
            'e1' => ['Example 1 — paint containment clips the overhang', 'The child sticks out. contain: paint clips it, which is the visible relative of this lesson.', <<<'CSS'
<style>
  .box { width: 140px; height: 50px; background: #dbeafe; contain: paint; }
  .over { width: 200px; background: #e11d48; color: white; font-family: sans-serif; }
</style>
<div class="box"><div class="over">this overhang is clipped</div></div>
CSS],
            'e2' => ['Example 2 — a section reserves height while its insides can be skipped', 'The section claims 120 pixels of placeholder size. On a long page, far copies of this pattern are the ones the browser can skip.', <<<'CSS'
<style>
  .feed { content-visibility: auto; contain-intrinsic-size: auto 120px; background: #f8fafc; border: 1px solid #cbd5e1; font-family: sans-serif; }
</style>
<section class="feed"><p>A note the page can skip when this section is far from the screen.</p></section>
CSS],
            'micro' => [
                'If a sticky child lives inside a content-visibility section, test it. Skipping work and sticky positioning need a careful look, because the section contents are not always laid out.',
                'Images inside a skipped section should still have sizes, or the remembered intrinsic size is wrong the first time.',
                'Do not use content-visibility to hide a secret. It is a performance hint. The text is still in the document.',
            ],
            'quiz' => [
                $q('What does content-visibility: auto allow the browser to skip?', 'Layout and paint while the element is far from the screen', 'The HTML download', 'All CSS on the page', 'Keyboard focus forever'),
                $q('Why set contain-intrinsic-size?', 'So the scrollbar does not jump when the section is approached', 'To change the font', 'To create a stacking context only', 'To disable images'),
                $q('How is content-visibility: hidden different from auto?', 'hidden is not rendered and is hidden from assistive technology', 'They are the same', 'hidden is only a color', 'auto deletes the element'),
            ],
            'practice' => 'Clip an overhanging child with contain: paint. Then add content-visibility: auto and a 120 pixel intrinsic size to a section and explain what the placeholder is for.',
        ]),
    ];
}

if (PHP_SAPI === 'cli' && isset($_SERVER['argv'][0]) && realpath((string)$_SERVER['argv'][0]) === realpath(__FILE__)) {
    require_once __DIR__ . '/includes/db.php';
    require_once __DIR__ . '/seed_css_advanced.php';

    track_seed($pdo, css_course_meta(), css_chapters_advanced());

    track_place($pdo, 'css-tutorial', [
        'CSS3 Introduction & How to Apply Styles',
        'CSS Selectors Masterclass (Element, Class, ID)',
        'CSS Colors, Hex, RGB & Background Gradients',
        'CSS The Box Model (Margin, Border, Padding, Content)',
        'CSS Typography, Web Fonts & Text Styling',
        'CSS Display Property (block, inline, inline-block, none)',
        'CSS Positioning (static, relative, absolute, fixed, sticky & z-index)',
        'CSS Flexbox Masterclass (One-Dimensional Layout)',
        'CSS Grid Masterclass (Two-Dimensional Layout)',
        'CSS Responsive Web Design & Media Queries',
        'CSS Transitions & Timing Functions (Masterclass)',
        'CSS Transforms: 2D & 3D Spatial Manipulation',
        'CSS Keyframe Animations (@keyframes Masterclass)',
        'CSS Custom Properties (Variables & Dark Mode Theming)',
        'CSS Units Masterclass (rem, em, vh, vw, clamp & min/max)',
        'CSS Advanced Pseudo-classes (:nth-child, :is, :not, :where)',
        'CSS Pseudo-elements (::before, ::after & Decorative UI)',
        'CSS Box Shadows, Text Shadows & Modern Borders',
        'CSS Overflow, Text Truncation & Clipping Paths',
        'CSS Filters & Backdrop Glassmorphism (Masterclass)',
        'The cascade is an order, not a fight',
        'Specificity is three columns',
        'Where an important flag really sits',
        'Cascade layers put a reset under the components',
        'Some properties inherit, and some stop',
        'border-box counts the padding inside the width',
        'Vertical margins collapse into one gap',
        'A formatting context holds the children in',
        'Absolute looks for a containing block',
        'z-index only compares inside one stacking context',
        'Flex grow, shrink, and the basis',
        'The flex item that will not shrink',
        'Grid areas you can read out loud',
        'auto-fit, auto-fill, and minmax',
        'Subgrid uses the parent tracks',
        'Native nesting and the ampersand',
        ':has() styles the parent from the child',
        'Logical properties follow the writing direction',
        'A container query measures the component',
        '@scope keeps a selector in one subtree',
        '@supports asks if a feature exists',
        'oklch and color-mix',
        'light-dark() follows the color scheme',
        '@property makes a variable animatable',
        ':focus-visible is the outline you keep',
        'Reduced motion, more contrast, forced colors',
        'Scroll snap parks the next card',
        'aspect-ratio holds the box before the picture',
        'text-wrap balance keeps a heading even',
        'content-visibility skips work off screen',
        'A scroll timeline drives the animation',
        '@starting-style and a discrete display change',
        'Animating a height you do not know',
        'sibling-index() counts the position in the parent',
        'A view transition names the boxes that move',
        'Anchor positioning ties a popup to its button',
        'The backdrop sits behind the dialog',
        'Invalid fields, accent color, and appearance',
        'Project: a note card that adapts to its box',
    ]);

    track_sections($pdo, 'css-tutorial', [
        2 => 'Foundations',
        3 => 'Color',
        7 => 'The box and the page',
        10 => 'Layout',
        13 => 'Motion',
        15 => 'Tokens and units',
        20 => 'Polish',
        25 => 'The cascade',
        30 => 'The real box',
        35 => 'Layout under pressure',
        41 => 'Selectors that reach',
        44 => 'Color you can calculate',
        50 => 'The person and the screen',
        58 => 'Motion and overlays',
        59 => 'The project',
    ]);
    echo "[CSS] placed the chapters that exist so far\n";
}
