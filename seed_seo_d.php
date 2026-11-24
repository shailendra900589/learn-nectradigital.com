<?php
declare(strict_types=1);

function seo_chapters_d(): array
{
    $L = static function (array $c): array {
        return track_lesson('visual', '', $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };

    return [
        $L([
            'section' => 'Reading the report',
            'title' => 'Coverage is a state, not a single error',
            'keywords' => 'index coverage, crawled not indexed, discovered',
            'lead' => 'Search Console does not say only "yes" or "no". A Milk List URL can be discovered, fetched, kept, or left out for a named reason. The next action depends on which state you are actually in.',
            'heading' => 'Read the state before you rewrite the title',
            'para' => 'Discovered means the URL is known and not yet fetched. Crawled and not indexed means it was fetched and not kept. Indexed means it can be shown. Excluded with a reason you chose, such as a thank-you page, is success. Excluded because it is a duplicate, a redirect, or a soft miss is a different repair. A title change does not fetch a URL that was never reached, and it does not keep a page that was judged empty.',
            'bullets' => [
                'Name the state: known, fetched, kept, or left out on purpose.',
                'A reason you chose is not a failure.',
                'Duplicate, redirect, and soft miss each have their own fix.',
                'Do not "request indexing" on a page you have not made worth keeping.',
                'Check the offer URL first, then the pages that should support it.',
            ],
            'visual' => tf_flow('seo43', 'Coverage states', [['known', 'not fetched yet'], ['fetched', 'not kept'], ['kept', 'can be shown']], 'The title is the wrong tool until you know the state.'),
            'rows' => [
                ['Discovered', 'Known only', 'Give it a real link.'],
                ['Crawled', 'Fetched, not kept', 'Make it worth storing.'],
                ['Indexed', 'Can be shown', 'Then judge the query.'],
                ['Chosen out', 'Thank-you', 'Leave it out.'],
                ['Duplicate', 'A twin', 'Pick the owner.'],
                ['Soft miss', 'False page', 'Return an honest status.'],
            ],
            'e1' => ['Example 1 — the state tells you the job', 'Fetched and not kept is a page problem, not a slogan problem.', seo_coverage('Fetched, not kept', <<<'HTML'
<p style="font-size:12px;color:#b45309;font-weight:700;">CRAWLED, NOT INDEXED</p>
<h1 style="font-size:22px;margin:8px 0;">/shared-list-tips-2</h1>
<p>The URL was opened. It repeats the offer in thinner words. It was not kept. Merge it or make it a real narrower answer.</p>
HTML)],
            'e2' => ['Example 2 — requesting a page that should stay out', 'More requests do not turn a confirmation into an article.', seo_coverage('A wrong request', <<<'HTML'
<p style="font-size:12px;color:#b91c1c;font-weight:700;">PLEASE INDEX</p>
<h1 style="font-size:22px;margin:8px 0;">/thank-you</h1>
<p>This page only says the list was started. Asking again will not make it a result for a shopping query.</p>
HTML)],
            'micro' => [
                'A spike of "discovered" URLs often means filters or tags are minting addresses. Stop the minting.',
                'Validated fixes take time. Read the same URL next week, not in the next minute.',
                'If the sample URL in the report is not the URL you care about, open the one you care about before you generalize.',
            ],
            'quiz' => [
                $q('A URL was fetched and not kept. What do you inspect?', 'Whether the page is worth storing', 'Only the adjective in the title', 'A request button, repeatedly', 'The logo'),
                $q('Which exclusion can be the result you wanted?', 'A thank-you page kept out on purpose', 'The offer blocked by accident', 'A soft miss that claims success', 'The owner URL marked as a duplicate of a tag'),
                $q('What does discovered, not fetched, usually need?', 'A real path to the URL', 'A louder title', 'A bought mention', 'A second copy'),
            ],
            'practice' => 'Open coverage for the offer and for one URL you want left out. Write the state of each in a sentence, and the single next repair if the state is wrong.',
        ]),
        $L([
            'section' => 'Reading the report',
            'title' => 'An accidental query is a clue',
            'keywords' => 'unexpected queries, search appearance, query mismatch',
            'lead' => 'Milk List may be shown for a query you never wrote down. That row is information. It can mean the page is unclear, the intent is a neighbor you can serve, or you are attracting people you should not promise to help.',
            'heading' => 'Do not celebrate a query you cannot answer',
            'para' => 'Read the query as Asha would. If it is a real neighbor of the offer, you may answer it on the page or on a narrower URL. If it is a different product, a different city, or a promise you do not keep, tighten the title and the first screen so you stop winning that click. An accidental impression is not traffic to protect. A click from the wrong query is a person you are about to disappoint.',
            'bullets' => [
                'Sort queries you did not plan. Read them.',
                'A neighbor intent can earn its own page.',
                'A mismatched intent should be discouraged by a clearer page, not fed.',
                'Do not add a paragraph just to catch the accidental word.',
                'Wrong clicks are more expensive than missed impressions.',
            ],
            'visual' => tf_split('seo44', 'This query?', 'Can we answer it honestly?', 'Yes', 'Give it a true page', 'No', 'Stop inviting it', 'An accidental impression is not a prize.'),
            'rows' => [
                ['Neighbor', 'Shared grocery list', 'Already our intent.'],
                ['New job', 'How to split a bill', 'Only with a real page.'],
                ['Wrong', 'Free recipes', 'Do not bait it.'],
                ['City', 'If you are not local', 'Do not pretend.'],
                ['Click', 'From the wrong query', 'A disappointed person.'],
                ['Fix', 'Clearer title', 'Not an extra paragraph of bait.'],
            ],
            'e1' => ['Example 1 — a neighbor you can serve', 'The query is the same household job.', seo_console('A neighbor', <<<'HTML'
<p style="font-size:12px;color:#0f766e;font-weight:700;">QUERY</p>
<p>shared grocery list for roommates</p>
<p>The offer can answer this without a new promise. Say "house" in a way that includes roommates, or give them one honest line.</p>
HTML)],
            'e2' => ['Example 2 — a query you should not keep', 'The click would be a lie.', seo_console('The wrong crowd', <<<'HTML'
<p style="font-size:12px;color:#b45309;font-weight:700;">QUERY</p>
<p>free dinner recipes</p>
<p>The page is a paid list. Showing up here means the words drifted. Tighten them. Do not add recipes to catch the click.</p>
HTML)],
            'micro' => [
                'A few impressions of a strange query are noise. A pattern is a decision.',
                'Adding the accidental word in a footnote is how pages become piles.',
                'If you truly want that query, build the page first and let the words follow the page.',
            ],
            'quiz' => [
                $q('What is an accidental query?', 'A search you did not plan that still showed your page', 'A keyword you bought', 'A sitemap entry', 'A status code'),
                $q('When should you keep that query in mind?', 'When you can answer it honestly', 'Whenever it has impressions', 'When it is a different product', 'When it lets you say free'),
                $q('What do you do with a query that would disappoint the click?', 'Make the page and the title less inviting to it', 'Add a hidden paragraph', 'Buy a mention for it', 'Celebrate the impression'),
            ],
            'practice' => 'List five queries the offer is shown for. Mark each serve, narrow-page, or discourage. Write the change for one discourage row.',
        ]),
        $L([
            'section' => 'Reading the report',
            'title' => 'Brand queries and problem queries tell different stories',
            'keywords' => 'branded search, nonbrand queries, query segmentation',
            'lead' => 'People who type Milk List already know the name. People who type "family shopping list" do not. Mixing those rows into one average makes a famous name look like a solved problem, or hides a brand that nobody searches.',
            'heading' => 'Split the report before you judge it',
            'para' => 'Brand queries should find the real home quickly: the right name, the right URL, no rival using your name in a misleading title. Problem queries are the SEO job: you are one answer among others, and the page has to earn the click. Read them in separate lists. A week where brand clicks rose because of a podcast is not evidence that the offer title improved.',
            'bullets' => [
                'Brand: they asked for you. Be easy to recognize.',
                'Problem: they asked for the job. Be the answer.',
                'Never average them into one mood.',
                'A misleading use of your name in someone else\'s result is a different issue from your rank.',
                'Write two sentences each week, one for each list.',
            ],
            'visual' => tf_panels('seo45', 'Two lists', 'Brand', ['Milk List', 'They know the name', 'Open the real home'], 'Problem', ['Family shopping list', 'They know the job', 'Earn the click'], 'One average hides which story moved.'),
            'rows' => [
                ['Brand', 'Your name', 'Milk List.'],
                ['Problem', 'Their job', 'Family shopping list.'],
                ['Home', 'For the name', 'The real URL.'],
                ['Offer', 'For the job', 'The answer page.'],
                ['Average', 'Misleading', 'Do not blend.'],
                ['Week', 'Two sentences', 'What moved, and why.'],
            ],
            'e1' => ['Example 1 — the lists stay apart', 'You can see which job changed.', seo_console('Split', <<<'HTML'
<p style="font-weight:800;">Brand</p>
<p>Milk List · the home URL · people who already know you</p>
<p style="font-weight:800;margin-top:10px;">Problem</p>
<p>family shopping list · /shared-list · people comparing answers</p>
HTML)],
            'e2' => ['Example 2 — one blended cheer', 'The podcast did the work. The page did not.', seo_console('Blended', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Clicks are up</h1>
<p>Almost all of them typed the brand after a mention. The problem query is unchanged. The report treats it as one victory.</p>
HTML)],
            'micro' => [
                'Misspellings of the brand belong with the brand, if you can see them.',
                'A problem query that includes your name is still mostly brand. Do not count it as new demand.',
                'If nobody searches the brand, that is a fame fact, not an SEO failure by itself.',
            ],
            'quiz' => [
                $q('Who types a brand query?', 'Someone who already knows the name', 'Someone comparing every list app from zero', 'A crawler', 'A sitemap'),
                $q('Why split the report?', 'The two lists move for different reasons', 'To make the total larger', 'To hide a weak page', 'To mix them more carefully'),
                $q('A podcast mentions you and brand clicks rise. What did you learn about the offer title?', 'Nothing yet. Read the problem queries separately', 'The title is proven', 'You should buy mentions', 'The page can stay vague'),
            ],
            'practice' => 'Write two headings, Brand and Problem. Put your last known queries under the right heading. If you only have one list, say which story you cannot see yet.',
        ]),
        $L([
            'section' => 'Reading the report',
            'title' => 'Compare a change across similar weeks',
            'keywords' => 'seo measurement window, seasonality, before after',
            'lead' => 'A title change on Monday cannot be judged on Monday night. Milk List reads the same query across similar weeks, and does not compare a holiday week with an ordinary one and call it a lesson.',
            'heading' => 'Give the change a fair window',
            'para' => 'Pick the query and the page before you edit. Note the shown and chosen counts for the previous similar period. Change one thing. Wait long enough for new fetches and for ordinary variation to settle. Then read that same query. If a festival, a mention, or a broken page happened in the same window, you do not know which cause you measured. Write the other events down so you do not invent a story.',
            'bullets' => [
                'One change, one query, one page.',
                'Compare similar weeks, not a holiday with a Tuesday.',
                'Write down anything else that happened.',
                'A single day is noise on a small site.',
                'If you cannot wait, you cannot claim the result.',
            ],
            'visual' => tf_flow('seo46', 'A fair read', [['before', 'similar weeks'], ['one edit', 'the title'], ['after', 'the same query']], 'A festival in the middle cancels the lesson.'),
            'rows' => [
                ['Before', 'Write the numbers', 'Shown and chosen.'],
                ['Edit', 'One', 'The title only.'],
                ['Wait', 'Similar length', 'Not an hour.'],
                ['Same', 'Query and page', 'Do not switch.'],
                ['Noise', 'A mention, a holiday', 'Write it down.'],
                ['Claim', 'Only if the window is clean', 'Or say you do not know.'],
            ],
            'e1' => ['Example 1 — a note you can trust later', 'The other events are on the page.', seo_console('A fair note', <<<'HTML'
<p style="font-weight:800;">Query</p>
<p>family shopping list · /shared-list</p>
<p>Changed only the title. Compared the next three ordinary weeks with the three before. No festival. No other edit.</p>
HTML)],
            'e2' => ['Example 2 — five edits and a holiday', 'The chart cannot teach.', seo_console('A muddy window', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">We changed everything on Friday</h1>
<p>Title, price, path, and a holiday campaign. On Monday the clicks differed. Nobody knows why.</p>
HTML)],
            'micro' => [
                'Seasonal grocery weeks are not a blank baseline. Compare them with last year\'s same week if you have it.',
                'A broken redirect in the window explains a drop better than a title theory.',
                'Small counts swing. "Up 50%" on four clicks is not a strategy.',
            ],
            'quiz' => [
                $q('When can you judge a title change?', 'After similar weeks, on the same query', 'That evening', 'After also changing the price', 'During a holiday, against a quiet week'),
                $q('What else do you write down?', 'Mentions, holidays, and other edits', 'Only the new title', 'The font', 'A hope'),
                $q('What makes a percent change easy to misread?', 'A very small count', 'A long clean window', 'One edit', 'A named query'),
            ],
            'practice' => 'Before the next edit, write the query, the page, the one change, the comparison window, and a blank line labeled "what else happened".',
        ]),
        $L([
            'section' => 'The machinery',
            'title' => 'Robots.txt is a fetch request, not a lock',
            'keywords' => 'robots.txt, crawl rules, do not hide secrets',
            'lead' => 'A robots.txt file is a public note asking fetchers which paths they may skip. It is not a password, and it does not remove a URL that is already known. Milk List uses it sparingly, and never to hide the offer.',
            'heading' => 'Ask for less fetching only when you mean it',
            'para' => 'You might ask fetchers to skip an internal search, a filter that mints endless URLs, or a private-looking path that should not have been linked. You do not put secrets in the file, because anyone can read it. You do not disallow the offer, the help pages, or the stylesheet and images those pages need. If a URL is already indexed and you want it dropped, blocking the fetch can prevent the drop: the fetcher cannot see that you now want it gone. Prefer a noindex on a page you can still fetch, or a redirect, or a real removal.',
            'bullets' => [
                'The file is public. It is not a lock.',
                'Skipping endless filters can be sensible.',
                'Do not skip the pages you want shown.',
                'Do not block a URL as your way to remove it from the index.',
                'If you are unsure, leave the path fetchable and decide about keeping it on the page itself.',
            ],
            'visual' => tf_split('seo47', 'Ask fetchers to skip?', 'Endless empty filters?', 'Yes', 'A skip can be fair', 'The offer, or a secret', 'No', 'A public file cannot hide a secret, and a block can trap a URL in the index.'),
            'rows' => [
                ['Public', 'Anyone can read it', 'No secrets.'],
                ['Skip', 'Filter storms', 'If they are not answers.'],
                ['Keep open', 'The offer', 'And its images.'],
                ['Removal', 'Do not block', 'Let the fetcher see the new instruction.'],
                ['Noindex', 'On a fetchable page', 'For pages that should drop.'],
                ['Accident', 'Disallow /', 'That hides the whole site.'],
            ],
            'e1' => ['Example 1 — the offer stays reachable', 'Only the junk path is skipped.', seo_coverage('A small skip', <<<'HTML'
<p style="font-size:12px;color:#0f766e;font-weight:700;">FETCH RULES</p>
<p>Skip the internal search that invents a URL for every word.</p>
<p>Leave /shared-list and its images fetchable.</p>
HTML)],
            'e2' => ['Example 2 — the whole site is told to go away', 'One line hid the product.', seo_coverage('Everything blocked', <<<'HTML'
<p style="font-size:12px;color:#b91c1c;font-weight:700;">SKIP THE WHOLE SITE</p>
<p>The offer cannot be fetched. The file also names a private folder, which advertises it.</p>
HTML)],
            'micro' => [
                'Test a rule by asking whether the offer URL is still allowed. If you cannot answer, the rule is too broad.',
                'A stylesheet blocked by accident can make the fetched page look empty.',
                'Staging sites should not be an open copy of production. A robots note is not the whole of that protection.',
            ],
            'quiz' => [
                $q('What is robots.txt?', 'A public request about what may be fetched', 'A password', 'A rank button', 'A way to delete a secret'),
                $q('Why is blocking a bad way to drop an indexed URL?', 'The fetcher may never see that you want it gone', 'Because files cannot be public', 'Because offers must be blocked', 'Because sitemaps forbid it'),
                $q('Which path is a fair candidate to skip?', 'An internal search that mints endless URLs', 'The offer', 'The help article', 'The image of the product'),
            ],
            'practice' => 'Write the paths you wish fetchers would skip, and the paths that must stay open. If a secret appears in the first list, take it out of any public file and protect it properly.',
        ]),
        $L([
            'section' => 'The machinery',
            'title' => 'A robots meta line is about keeping the page',
            'keywords' => 'meta robots, noindex, index follow',
            'lead' => 'A robots meta line on the page itself says whether that URL should be kept as a result. It is different from a fetch skip. Milk List puts noindex on the thank-you page, which remains fetchable, and puts nothing of the kind on the offer.',
            'heading' => 'Say "do not keep this" on the page, not in a riddle',
            'para' => 'Noindex means: you may fetch it, please do not store it as a result. That is right for confirmations, internal tools, and thin combinations. It is wrong for the offer. Follow or nofollow on a page is about the links on that page, not a magic rank switch. Do not combine noindex on the offer with a sitemap entry that asks for it to be considered. Do not use a hidden line to show people one thing and a fetcher another.',
            'bullets' => [
                'Noindex is for pages that are not answers.',
                'The offer stays eligible.',
                'The page must still be fetchable for the instruction to be seen.',
                'The sitemap must agree.',
                'One version of the page for everyone.',
            ],
            'visual' => tf_panels('seo48', 'Keep or not', 'Offer', ['Fetch it', 'Keep it', 'List it'], 'Thank-you', ['Fetch it', 'Do not keep it', 'Leave it off the list'], 'The instruction sits on the page, where it can be seen.'),
            'rows' => [
                ['Noindex', 'Do not store', 'Thank-you.'],
                ['Offer', 'No such line', 'It is the answer.'],
                ['Fetch', 'Still allowed', 'So the line can be read.'],
                ['Sitemap', 'Agrees', 'No thank-you row.'],
                ['Nofollow', 'About links', 'Not a rank trick.'],
                ['Two versions', 'Never', 'Person and fetch match.'],
            ],
            'e1' => ['Example 1 — the confirmation opts out', 'It can be opened and it is not a result.', seo_coverage('Do not keep', <<<'HTML'
<p style="font-size:12px;color:#b45309;font-weight:700;">NOINDEX</p>
<h1 style="font-size:22px;margin:8px 0;">You started the list</h1>
<p>Reachable, useful to that person, and not offered as a search result.</p>
HTML)],
            'e2' => ['Example 2 — the offer opts out by mistake', 'The only answer asked not to be kept.', seo_coverage('Offer opted out', <<<'HTML'
<p style="font-size:12px;color:#b91c1c;font-weight:700;">NOINDEX ON THE OFFER</p>
<p>/shared-list says do not keep me, and the sitemap asks for it anyway.</p>
HTML)],
            'micro' => [
                'A template that adds noindex to every page is a quiet way to disappear.',
                'Removing noindex does not create an instant result. The page still has to be worth keeping.',
                'Links on a noindex page can still be useful to people. Do not noindex a hub just to be tidy.',
            ],
            'quiz' => [
                $q('What does noindex ask for?', 'Fetch the page, but do not keep it as a result', 'Never fetch it, and hide a secret', 'A higher rank', 'A different page for the fetcher'),
                $q('Which page should have it?', 'The thank-you page', 'The offer', 'The main help article', 'The price page'),
                $q('What must the sitemap do?', 'Agree with the keep-or-not decision', 'List every noindex URL as a priority', 'Replace the meta line', 'Include drafts'),
            ],
            'practice' => 'List pages that should not be results. Confirm each is fetchable, marked not to be kept, and absent from the sitemap. Confirm the offer has none of those marks.',
        ]),
        $L([
            'section' => 'The machinery',
            'title' => 'The answer has to exist in the page itself',
            'keywords' => 'javascript seo, html content, hash urls',
            'lead' => 'If the shared list, the price, and the heading appear only after a private script runs, some people and some fetches never see them. Milk List puts the answer in the page that arrives, and uses script for behavior, not for the only copy of the promise.',
            'heading' => 'The first response should already say it',
            'para' => 'A button can be enhanced. The H1, the price, and the main paragraph should be in the document. An address that is only a hash after a shell page gives every topic the same URL, so there is nothing distinct to fetch. Do not serve a rich page to people and an empty shell to fetchers. That is two sites, and it is the kind of mismatch this course refuses.',
            'bullets' => [
                'Promise, price, and H1 are in the first response.',
                'Script may improve a control. It may not be the only place the words exist.',
                'One topic, one real URL, not a hash on a single shell.',
                'What a person sees and what is fetched are the same document.',
                'If you disable script and the offer vanishes, the page is not ready.',
            ],
            'visual' => tf_split('seo49', 'Where are the words?', 'In the first response?', 'Yes', 'The page can be an answer', 'Only after a script', 'Move them', 'A hash on one shell is not a library of pages.'),
            'rows' => [
                ['H1', 'In the document', 'Not injected later only.'],
                ['Price', 'In the document', 'Rs 499.'],
                ['Script', 'Behavior', 'Not the only copy.'],
                ['Hash', 'Poor address', 'One shell, many topics.'],
                ['Same', 'Person and fetch', 'One document.'],
                ['Test', 'Script off', 'The offer remains.'],
            ],
            'e1' => ['Example 1 — the words arrive with the page', 'Nothing essential waits on a script.', seo_page('In the page', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">One shared list</h1>
<p>Rs 499 a year. These words are in the page itself. A control may enhance the button. It is not the only place the price exists.</p>
HTML)],
            'e2' => ['Example 2 — an empty shell and a hash', 'Every topic is the same address.', seo_page('A shell', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Loading</h1>
<p>The offer appears only after a script, at an address that is a hash on one URL. There is no distinct page to keep.</p>
HTML)],
            'micro' => [
                'Links should be real links. A click that only a script understands is a door some fetches never open.',
                'A loading sentence is not an H1.',
                'If a partner site mirrors you by running a script, you still need your own stable URL.',
            ],
            'quiz' => [
                $q('Where must the price exist?', 'In the page that arrives', 'Only inside a script', 'Only in a hash', 'Only in an image'),
                $q('What is weak about many topics on one shell URL?', 'There is no distinct address to fetch and keep', 'People dislike buttons', 'Titles become longer', 'Sitemaps get smaller'),
                $q('What do you refuse?', 'A rich page for people and an empty one for fetchers', 'A button that is easier with script', 'A price in the document', 'A real link'),
            ],
            'practice' => 'Open the offer with script disabled, or view the document before any enhancement. If the H1 and price are missing, put them in the page.',
        ]),
        $L([
            'section' => 'The machinery',
            'title' => 'The phone version is the version that is fetched',
            'keywords' => 'mobile first indexing, responsive seo',
            'lead' => 'Search primarily fetches the phone experience. If Milk List hides the price, the links, or half the answer on a small screen, that smaller page is the one being judged. A handsome desk layout cannot rescue it.',
            'heading' => 'Do not keep a secret desktop',
            'para' => 'The phone page needs the same answer, the same links to the important neighbors, and the same text. Collapsing a section is fine if the content is still there. Replacing the page with a thinner "mobile site" that omits the proof and the links is a second, weaker site. Popups that cover the phone answer are part of the page that is fetched, not a harmless extra.',
            'bullets' => [
                'Same words and same important links on the phone.',
                'A collapsed section must still contain the content.',
                'Avoid a separate thin mobile site.',
                'Test the phone page as the primary page.',
                'A blocked image or font on the phone can remove the proof.',
            ],
            'visual' => tf_panels('seo50', 'What gets fetched', 'Phone', ['Full answer', 'Real links', 'The price'], 'Desk-only secret', ['Extra proof', 'The real menu', 'Not seen'], 'The phone page is the page.'),
            'rows' => [
                ['Primary', 'The phone', 'Judge that.'],
                ['Words', 'The same', 'Not a summary only.'],
                ['Links', 'The same doors', 'Offer and help.'],
                ['Collapse', 'Still present', 'Not deleted.'],
                ['Twin site', 'Avoid', 'It drifts.'],
                ['Popup', 'Part of the page', 'It must not be the page.'],
            ],
            'e1' => ['Example 1 — the phone has the full answer', 'Nothing important is desk-only.', seo_page('Phone page', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">One shared list</h1>
<p>Rs 499 a year. The help link and the start button are on this screen too.</p>
<p><a href="#help" style="color:#0f766e;font-weight:700;">How a house uses it</a></p>
HTML)],
            'e2' => ['Example 2 — the phone site is a brochure', 'The fetched version omitted the proof.', seo_page('A thin twin', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Hi</h1>
<p>The phone site has a logo and a menu. The price, the explanation, and the links live only on the wide layout.</p>
HTML)],
            'micro' => [
                'A hamburger menu can hide links from a person. Make sure the important links also exist in the content.',
                'Lazy images are fine when the text does not depend on them.',
                'If you ever did run two URLs, the phone one must not be the only one that is thin.',
            ],
            'quiz' => [
                $q('Which version is the one to judge?', 'The phone page', 'The wide layout only', 'A slide that never loads', 'The logo'),
                $q('What is acceptable collapsing?', 'Content that is still in the page', 'Deleting the price on small screens', 'A second site with less information', 'Links that exist only on the desk'),
                $q('What should the phone page include?', 'The answer, the price, and the important links', 'Only a logo', 'A different promise', 'No way to start'),
            ],
            'practice' => 'On a phone, list every fact and link you can reach. Compare with the desk page. Move anything important that exists only on the desk into the phone page.',
        ]),
        $L([
            'section' => 'The machinery',
            'title' => 'Loading, response, and stability are three different waits',
            'keywords' => 'core web vitals, lcp, inp, cls',
            'lead' => 'Speed is not one score. A person waits for the main thing to appear, waits for the page to respond to a tap, and loses their place if the layout jumps. Milk List treats those as three repairs.',
            'heading' => 'Name which wait you are fixing',
            'para' => 'The main thing on the offer is the heading and the price, not a background film. If that arrives late, the picture or the film is too heavy, or the text is waiting on a font. If a tap on Start hesitates, something is busy before the button works. If the button jumps down as a banner arrives, the layout was unstable. A lab number is a clue. The field experience, including on a phone, is the one that matches the person. Do not chase a perfect lab score by hiding the text.',
            'bullets' => [
                'Appearance: the answer shows promptly.',
                'Response: a tap is acknowledged promptly.',
                'Stability: the layout does not jump after it paints.',
                'Fix the one that is actually bad.',
                'Never hide the offer to improve a score.',
            ],
            'visual' => tf_flow('seo51', 'Three waits', [['show', 'the price'], ['tap', 'the button'], ['stay', 'no jump']], 'A single score hides which wait hurt.'),
            'rows' => [
                ['Show', 'Heading and price', 'Before decoration.'],
                ['Late', 'A film or a font', 'Do not block the words.'],
                ['Tap', 'Start', 'It should respond.'],
                ['Jump', 'A late banner', 'Reserve the space or skip it.'],
                ['Lab', 'A clue', 'Not the person.'],
                ['Cheat', 'No', 'Do not hide the text.'],
            ],
            'e1' => ['Example 1 — the answer shows, the tap works, the page stays', 'Three ordinary successes.', seo_page('Steady', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">One shared list</h1>
<p>Rs 499 a year. The line is visible immediately. The button does not jump when the small picture arrives.</p>
<p><a href="#start" style="display:inline-block;background:#0f766e;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — a score improved by hiding the offer', 'The number got better. The page got worse.', seo_page('A fake fast page', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;"> </h1>
<p>The text is withheld so a tool can call the page fast. The person sees a blank screen and then a jump.</p>
HTML)],
            'micro' => [
                'Reserve space for images. A late picture that shoves the button is the stability problem.',
                'A tag manager that loads ten tools often causes the hesitant tap.',
                'Improve the template. Every page inherits the wait.',
            ],
            'quiz' => [
                $q('Which three waits matter?', 'Appearance, response, and stability', 'Rank, fame, and color', 'Only a single lab trophy', 'Sitemap, canonical, and slash'),
                $q('The button jumps when a banner loads. Which wait is that?', 'Stability', 'A title problem', 'A brand query', 'A redirect'),
                $q('What is an unacceptable way to look faster?', 'Withhold the offer from the person', 'Compress a picture', 'Reserve space for an image', 'Remove an unused tool'),
            ],
            'practice' => 'On a phone, note whether the price appears quickly, whether Start responds, and whether anything jumps. Write one repair for the wait that actually failed.',
        ]),
        $L([
            'section' => 'The machinery',
            'title' => 'Choose one host, one slash, and HTTPS',
            'keywords' => 'https, www, trailing slash, canonical host',
            'lead' => 'Milk List should have one public origin. https://milklist.example/shared-list is the page. The same path on http, on www, and with a stray slash should not be four competing copies.',
            'heading' => 'Pick the form and send the others home',
            'para' => 'Choose HTTPS. Choose with or without www, and keep it. Choose whether the offer path ends in a slash, and keep it. Redirect every other form to the chosen one in a single hop. Use that chosen form in links, the sitemap, and the canonical hint. Mixed content, where a secure page still calls an insecure image, makes the page look unfinished and can break the picture. There is no SEO prize for supporting all four forms.',
            'bullets' => [
                'One scheme, one host, one slash style.',
                'The other forms redirect once to the winner.',
                'Links and the sitemap use the winner only.',
                'Secure pages call secure assets.',
                'Do not add a new path when all you changed was the slash.',
            ],
            'visual' => tf_flow('seo52', 'One origin', [['pick', 'https and host'], ['redirect', 'the other three'], ['use', 'that form everywhere']], 'Four spellings of one page are four copies.'),
            'rows' => [
                ['HTTPS', 'The normal one', 'Not plain http.'],
                ['Host', 'One', 'www or not, not both.'],
                ['Slash', 'One style', 'Match it everywhere.'],
                ['Hop', 'Single', 'To the winner.'],
                ['Links', 'The winner', 'Including the sitemap.'],
                ['Assets', 'Secure too', 'No mixed picture.'],
            ],
            'e1' => ['Example 1 — the other forms come home', 'One public URL.', seo_coverage('One form', <<<'HTML'
<p style="font-size:12px;color:#0f766e;font-weight:700;">WINNER</p>
<p>https://milklist.example/shared-list</p>
<p>http, www, and the extra slash each redirect here in one hop.</p>
HTML)],
            'e2' => ['Example 2 — four live copies', 'The topic is split by spelling.', seo_coverage('Four copies', <<<'HTML'
<p style="font-size:12px;color:#b45309;font-weight:700;">ALL LIVE</p>
<p>http and https, www and not, slash and not. Each returns the offer as if it were a separate page.</p>
HTML)],
            'micro' => [
                'A certificate problem is a trust problem before it is an SEO problem. Fix it.',
                'Internal links that use the old host keep the redirect busy and slow the person.',
                'Trailing slash consistency matters more than which style you pick.',
            ],
            'quiz' => [
                $q('How many public forms should the offer have?', 'One', 'Four, to be safe', 'As many as people type', 'One per campaign'),
                $q('What should the other forms do?', 'Redirect once to the chosen form', 'Return the full page as copies', 'Show an error with success', 'Point at random help pages'),
                $q('What is mixed content?', 'A secure page still loading an insecure asset', 'Two titles', 'A brand query', 'A sitemap'),
            ],
            'practice' => 'Write the one public URL of the offer. Try the http, www, and slash variants. Each should arrive at that URL in one hop.',
        ]),
        $L([
            'section' => 'The machinery',
            'title' => 'Parameters must not mint a new page',
            'keywords' => 'url parameters, faceted navigation, tracking tags',
            'lead' => 'A parameter is a note on a URL: a campaign tag, a sort, a filter. It becomes a problem when each note is a new indexable copy of the Milk List offer, or when filters multiply into thousands of empty lists.',
            'heading' => 'Tags may track. They may not multiply the topic',
            'para' => 'A campaign tag on a link should open the same offer and should not be a second result. Sort and filter URLs that show nothing unique should not be kept. A filter that is a real, popular, useful subset, with its own words, can be a page you design on purpose. You do not let a grid of checkboxes invent that library overnight. Internal links should point at the clean URL.',
            'bullets' => [
                'Tracking tags are not new topics.',
                'Empty or sorted copies stay out of the index.',
                'A real subset can be a page, if you wrote it.',
                'Internal links use the clean owner.',
                'Watch coverage for a sudden family of parameter URLs.',
            ],
            'visual' => tf_split('seo53', 'A new URL?', 'Only a tag or a sort?', 'Yes', 'Keep one owner', 'A subset you truly wrote', 'Then it can be a page', 'Checkboxes must not invent a library.'),
            'rows' => [
                ['Tag', 'A campaign note', 'Not a second offer.'],
                ['Sort', 'Same items', 'Not a new article.'],
                ['Empty filter', 'Nothing to read', 'Do not keep it.'],
                ['Real subset', 'Written on purpose', 'Rare, and linked.'],
                ['Internal', 'Clean URL', 'Not the tagged one.'],
                ['Symptom', 'Thousands discovered', 'Stop the minting.'],
            ],
            'e1' => ['Example 1 — the tag opens the owner', 'The campaign can be measured without a twin result.', seo_coverage('A tag, one page', <<<'HTML'
<p style="font-size:12px;color:#0f766e;font-weight:700;">SAME PAGE</p>
<p>A campaign tag may sit on the link you share.</p>
<p>The preferred result remains /shared-list. The tag is not a second article.</p>
HTML)],
            'e2' => ['Example 2 — filters invented a catalog', 'Empty combinations became URLs.', seo_coverage('A mint', <<<'HTML'
<p style="font-size:12px;color:#b45309;font-weight:700;">DISCOVERED</p>
<p>/shared-list?color=blue&sort=3&page=40</p>
<p>The combination has no unique answer. There are thousands like it.</p>
HTML)],
            'micro' => [
                'Session identifiers in URLs are an old way to split one person into many pages. Do not do that.',
                'A parameter that changes the meaning, such as a real language, is a different decision and needs a real page.',
                'Canonical hints help only when they agree with what you link and what you list.',
            ],
            'quiz' => [
                $q('What should a campaign tag do?', 'Open the same offer without becoming a second result', 'Create a new indexable article', 'Hide the price', 'Change the H1'),
                $q('When can a filter be a page?', 'When you wrote a real, useful subset on purpose', 'Whenever a checkbox exists', 'When it is empty', 'When it is a sort order'),
                $q('What is a warning sign in coverage?', 'A sudden family of parameter URLs', 'One offer URL', 'A thank-you page kept out', 'A single redirect'),
            ],
            'practice' => 'List every parameter your offer URL accepts. Mark each as tracking, sort, empty filter, or a real page. Only the last may be a result.',
        ]),
        $L([
            'section' => 'The machinery',
            'title' => 'Moving the site is a map of redirects',
            'keywords' => 'site migration, domain change, redirect map',
            'lead' => 'A new domain or a new structure is not a fresh start. Every Milk List URL people and other sites already use needs a named destination. A move without a map is how a site disappears and calls it a redesign.',
            'heading' => 'One old URL, one new URL, one hop',
            'para' => 'Before you switch, list the URLs that are results or that have links. Pair each with the new URL of the same topic. Redirect in one hop. Update the sitemap, the internal links, and the profiles to the new origin. Keep the redirects. Do not send the whole old site to the new homepage. Do not launch the new site as a second copy that also stays open. Tell yourself what success looks like: the same queries still find the same topics.',
            'bullets' => [
                'A map, not a hope.',
                'Same topic to same topic.',
                'One hop, kept for a long time.',
                'The old origin should not remain a second live site.',
                'Judge the move by queries and coverage, not by the new paint.',
            ],
            'visual' => tf_flow('seo54', 'A move', [['list', 'old URLs'], ['pair', 'the same topic'], ['hop', 'one redirect']], 'The new homepage is not the destination of every old URL.'),
            'rows' => [
                ['List', 'What is known', 'Results and linked URLs.'],
                ['Pair', 'Same topic', 'Offer to offer.'],
                ['Home', 'Only the old home', 'Not every article.'],
                ['Hop', 'One', 'No chain through the old design.'],
                ['Copy', 'Do not leave both live', 'One origin.'],
                ['After', 'Queries and coverage', 'Did the topics survive?'],
            ],
            'e1' => ['Example 1 — the offer follows the offer', 'The map is specific.', seo_coverage('A mapped move', <<<'HTML'
<p style="font-size:12px;color:#0f766e;font-weight:700;">MAP</p>
<p>old.example/list → milklist.example/shared-list</p>
<p>old.example/help → milklist.example/add-an-item</p>
<p>One hop each. The old host does not keep a second copy.</p>
HTML)],
            'e2' => ['Example 2 — everything lands on a new poster', 'The library was erased.', seo_coverage('A dump', <<<'HTML'
<p style="font-size:12px;color:#b91c1c;font-weight:700;">ALL TO HOME</p>
<p>Every old article redirects to the new homepage, and the old site is still open with the same pages.</p>
HTML)],
            'micro' => [
                'Change the address only when you must. A redesign can keep the paths.',
                'Update the profiles and the important mentions you control. Redirects are the net, not the only link.',
                'Watch coverage for a rise in redirect and not-found states after the move. That is the punch list.',
            ],
            'quiz' => [
                $q('What does a move need before the switch?', 'A map from each important old URL to its new topic', 'A new logo', 'A holiday', 'Both sites left fully open'),
                $q('Where should an old help URL go?', 'To the new URL of that same help', 'Always to the new homepage', 'To a random offer', 'Nowhere'),
                $q('How do you know the move worked?', 'The same topics still appear for their queries', 'The paint is new', 'Clicks from the brand rose for one hour', 'The old site still ranks as a twin'),
            ],
            'practice' => 'If you have ever changed a path, write the old URL and the new one. If any old URL has no destination, give it one or accept an honest not-found.',
        ]),
        $L([
            'section' => 'The content system',
            'title' => 'A hub introduces the topic and points onward',
            'keywords' => 'topic cluster, pillar page, hub and spoke',
            'lead' => 'A hub is a page that explains the whole topic briefly and links to the narrower pages. For Milk List the hub can be the offer, or a guide that sends people to the offer, the comparison, and the how-to. It is not a dump of every keyword.',
            'heading' => 'Introduce, then send them to the specific page',
            'para' => 'The hub answers "what is this?" in a few screens and names the next pages in words a person would use. Each narrower page answers its own query and links back to the hub when that is the next useful step. You do not paste the narrower page onto the hub, and you do not make ten hubs for one topic. A hub with no children is just a page. Children with no hub are how orphans happen.',
            'bullets' => [
                'One hub for the topic, not ten.',
                'The hub links out with real names.',
                'Children answer a narrower query and can link back.',
                'Do not duplicate the child on the hub.',
                'The offer remains the decide page if the hub is a guide.',
            ],
            'visual' => tf_flow('seo55', 'Hub and children', [['hub', 'what it is'], ['child', 'one question'], ['back', 'when it helps']], 'A hub that copies every child is a duplicate factory.'),
            'rows' => [
                ['Hub', 'The overview', 'What a shared list is.'],
                ['Child', 'One question', 'Add an item. The price.'],
                ['Names', 'In the links', 'Not "read more" only.'],
                ['Copy', 'No', 'Point, do not paste.'],
                ['Offer', 'The decision', 'If the hub is a guide.'],
                ['Orphan', 'A child unlinked', 'Give it a parent.'],
            ],
            'e1' => ['Example 1 — the guide points at real pages', 'Each link is a next step.', seo_linkmap('A hub', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">A list the house can share</h1>
<p>Short explanation, then:</p>
<p><a href="#offer" style="color:#0f766e;font-weight:700;">The shared list and the price</a></p>
<p><a href="#add" style="color:#0f766e;font-weight:700;">Add an item the night before</a></p>
HTML)],
            'e2' => ['Example 2 — the hub is every article pasted', 'Nothing has its own job.', seo_page('A paste', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Everything about lists</h1>
<p>The full price page, the full how-to, and the full comparison are pasted here, and also published at their own URLs.</p>
HTML)],
            'micro' => [
                'A hub can be the offer when the topic is the product. Do not invent a guide you will not maintain.',
                'Children should not all target the hub\'s query.',
                'Footer links to every child are weaker than a sentence that recommends one.',
            ],
            'quiz' => [
                $q('What does a hub do?', 'Introduce the topic and point to narrower pages', 'Paste every article', 'Repeat one query in ten URLs', 'Replace the offer with a slogan'),
                $q('What does a child page do?', 'Answer one narrower query', 'Repeat the hub word for word', 'Hide from the hub', 'Target the same title'),
                $q('When is the offer itself the hub?', 'When the topic is the product and you will not maintain a separate guide', 'When you want duplicates', 'When you have no links', 'Never'),
            ],
            'practice' => 'Name one hub and up to four children. Write the link text from the hub to each child. Delete any child that does not have a different query.',
        ]),
        $L([
            'section' => 'The content system',
            'title' => 'Improve the page you have before you add a twin',
            'keywords' => 'content update, refresh a ranking page, historical optimization',
            'lead' => 'If /shared-list already answers the query, a second URL will not outwrite it. Milk List improves the page that exists: a clearer first screen, a missing price, a stale sentence, a broken example.',
            'heading' => 'The next sentence belongs on the current URL',
            'para' => 'Look at the queries that page already receives. Add the fact those people still needed. Remove a claim that is no longer true. Keep the URL. Change the title only when the promise changed. A new page is justified when the intent is actually different, not when you are tired of the old draft. Note the date of a meaningful update in your own log. Do not stamp a fake "updated today" on an untouched page.',
            'bullets' => [
                'Same intent, same URL.',
                'Add the missing fact. Cut the stale claim.',
                'A new URL needs a new intent.',
                'Do not fake a freshness date.',
                'Read the same query after the edit, on a fair window.',
            ],
            'visual' => tf_split('seo56', 'New URL?', 'New intent?', 'No', 'Edit this page', 'Yes', 'Then a new page', 'Fatigue is not a new topic.'),
            'rows' => [
                ['Same query', 'Edit', '/shared-list'],
                ['Missing', 'The price, the step', 'Add them here.'],
                ['Stale', 'An old claim', 'Remove it.'],
                ['Title', 'Only if the promise moved', 'Otherwise keep it.'],
                ['New URL', 'A different job', 'Not a rewrite.'],
                ['Date', 'Honest', 'Only when you changed something.'],
            ],
            'e1' => ['Example 1 — the same URL grows up', 'The path stays. The gap closes.', seo_page('An update', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">One shared list</h1>
<p>Rs 499 a year was missing last month. It is on this same URL now. The path did not change.</p>
HTML)],
            'e2' => ['Example 2 — a twin because the draft felt old', 'Two owners again.', seo_page('A tired twin', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Shared list 2026 final new</h1>
<p>The same promise as /shared-list, on a new path, because the old page felt stale. Both are live.</p>
HTML)],
            'micro' => [
                'Updating a page that ranks for the wrong query can remove the accident. Do that on purpose.',
                'Keep a short log: date, URL, what changed. Memory is a bad analyst.',
                'A redesign can be an update. It does not need new paths.',
            ],
            'quiz' => [
                $q('When do you edit instead of publishing?', 'When the intent is the one this URL already owns', 'When you are bored', 'When you want two results', 'When the date on the calendar changed'),
                $q('When is a new URL honest?', 'When the job of the query is different', 'When you add the price', 'When you fix a typo', 'When you want a freshness stamp'),
                $q('What is a fake freshness stamp?', 'Saying updated today on a page you did not change', 'A log of a real edit', 'A title that still matches', 'A stable path'),
            ],
            'practice' => 'Pick the offer URL. List one missing fact and one stale claim. Edit those. Write the date and the change in a log. Do not create a path.',
        ]),
        $L([
            'section' => 'The content system',
            'title' => 'Retire a page that helps nobody',
            'keywords' => 'content pruning, remove thin pages',
            'lead' => 'A larger site is not a better site. Milk List retires pages that no one should land on: empty announcements, outdated comparisons, and drafts that were published by habit.',
            'heading' => 'Removal is an editorial decision',
            'para' => 'If a page has no query it can honestly answer, and no person would miss it, take it down. Redirect it when a clear successor exists. Let it be a honest not-found when nothing replaced it. Do not leave it up and noindex it forever if it is also linked as if it mattered. Do not delete a page that still answers a real query just to make a count smaller. Pruning is about help, not about a magic number of URLs.',
            'bullets' => [
                'No honest query and no reader: retire it.',
                'Redirect to a true successor, or say it is gone.',
                'Remove it from navigation and the sitemap.',
                'Do not delete a page that still helps.',
                'The goal is a library, not a smaller trophy.',
            ],
            'visual' => tf_split('seo57', 'Keep this URL?', 'Would anyone miss it?', 'Yes', 'Improve it', 'No', 'Retire it', 'A successor gets a redirect. Nothing gets an honest goodbye.'),
            'rows' => [
                ['Help', 'Keep and improve', 'The offer. The real how-to.'],
                ['Nobody', 'Retire', 'An empty announcement.'],
                ['Successor', 'Redirect', 'Old comparison to the new one.'],
                ['Nothing', 'Not found or gone', 'Do not pretend.'],
                ['Nav', 'Remove the link', 'And the sitemap row.'],
                ['Count', 'Not the goal', 'Help is the goal.'],
            ],
            'e1' => ['Example 1 — an old comparison has a successor', 'The redirect keeps the intent.', seo_coverage('Retired with a home', <<<'HTML'
<p style="font-size:12px;color:#0f766e;font-weight:700;">REDIRECT</p>
<p>/list-vs-paper-old → /paper-chat-or-list</p>
<p>The new page is the comparison. The old URL is not in the menu or the sitemap.</p>
HTML)],
            'e2' => ['Example 2 — a dead page still invited', 'The site recommends a page it disowns.', seo_page('Still linked', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Coming soon, from last year</h1>
<p>It is in the footer, in the sitemap, and marked do-not-keep. Readers can still be sent there.</p>
HTML)],
            'micro' => [
                'A page with a few honest visits and a clear answer is not "thin" because it is short.',
                'Merging two weak pages into one strong one is pruning too.',
                'Check links after removal. A menu that points at a not-found page is a new defect.',
            ],
            'quiz' => [
                $q('When do you retire a page?', 'When no honest query and no reader needs it', 'When it is short but useful', 'When you want a round number of URLs', 'When it is the offer'),
                $q('What do you do if a better page replaced it?', 'Redirect the old URL to that successor', 'Leave both in the menu', 'Mark it noindex and keep linking it', 'Point it at the homepage always'),
                $q('What is the goal?', 'A library of pages that help', 'As few URLs as possible', 'As many URLs as possible', 'A sitemap full of goodbyes'),
            ],
            'practice' => 'Find one published URL you would not send a friend. Decide: improve, redirect, or remove. Then take it out of the menu and the sitemap if it is not staying.',
        ]),
        $L([
            'section' => 'The content system',
            'title' => 'A clear answer can be quoted, a trick should not',
            'keywords' => 'featured snippet, people also ask, zero click',
            'lead' => 'Sometimes a result quotes a few lines from a page. Milk List may be quoted when a paragraph answers a question plainly. Writing a page only to capture a box, or stuffing a question you do not answer, is a trick.',
            'heading' => 'Answer the question in public',
            'para' => 'If people ask "what is a shared shopping list?", a short definition followed by the useful detail is a good paragraph. It should be the same words on the page, high enough to be found, and true. A list of questions with one-line non-answers is not helpful, and marking it up does not make it helpful. Some people will not click. That is a reason to make the quote accurate and the page worth opening, not a reason to hide the answer.',
            'bullets' => [
                'A direct answer to a real question.',
                'The same words a person sees on the page.',
                'Enough detail that the click is still useful.',
                'No fake question list.',
                'Do not write for a box you cannot control.',
            ],
            'visual' => tf_split('seo58', 'Could this be quoted?', 'Is it a real answer?', 'Yes', 'Say it plainly', 'It is bait', 'Do not publish it', 'You do not control the box. You control the honesty.'),
            'rows' => [
                ['Question', 'Real', 'What is a shared list?'],
                ['Answer', 'A few clear lines', 'Then the detail.'],
                ['Place', 'On the page', 'Visible.'],
                ['Click', 'Still useful', 'Price, how, next step.'],
                ['Fake FAQ', 'No', 'Questions you do not answer.'],
                ['Control', 'None over the box', 'Full over the page.'],
            ],
            'e1' => ['Example 1 — a paragraph worth quoting', 'The definition is real, and the page continues.', seo_page('A plain answer', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">What is a shared shopping list?</h1>
<p>It is one list a household edits together, so milk is added once and the shopper sees it. Milk List does that for Rs 499 a year.</p>
HTML)],
            'e2' => ['Example 2 — questions with no answers', 'A box of bait.', seo_page('Empty questions', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">People also ask</h1>
<p>What is the best list? Click. How much? Click. Where? Click. The page never answers.</p>
HTML)],
            'micro' => [
                'A quoted line that overclaims will get the click and the complaint.',
                'You may format a real answer as a short paragraph or a true list. You may not invent the questions.',
                'If a quote sends people away satisfied, the page still did a job. Make the next sentence worth the visit when they need more.',
            ],
            'quiz' => [
                $q('When is a short answer good?', 'When it truly answers and the page continues honestly', 'When it hides the product', 'When it is a list of unanswered questions', 'When it exists only to win a box'),
                $q('What do you control?', 'The words on your page', 'Whether a box appears', 'Which rival is quoted', 'The person\'s next search'),
                $q('What is a fake FAQ?', 'Questions the page does not answer', 'A definition followed by the price', 'A how-to with steps', 'A comparison'),
            ],
            'practice' => 'Write one question a household actually asks, and a four-line answer. Put it on the page that owns that question. Delete any question on the site that the following text does not answer.',
        ]),
        $L([
            'section' => 'The content system',
            'title' => 'Breadcrumbs show the parent of the page',
            'keywords' => 'breadcrumbs, site hierarchy, parent page',
            'lead' => 'A breadcrumb is a short path: home, the section, this page. On a Milk List help article it tells Asha she is inside "how the house uses it", and it gives her a way up. It is not a row of keywords.',
            'heading' => 'The path you already believe',
            'para' => 'The crumbs should match the real parents in the menu. The last crumb is the current page and need not be a link to itself. The earlier crumbs are links. Use the names of the sections, not the query stuffed three times. If you also describe that path for machines, it must be the same path the person sees. A crumb trail that invents a category you do not have is a second, false map.',
            'bullets' => [
                'Home, real parent, current page.',
                'Parents are links. The current page is the end.',
                'Names match the menu.',
                'Any machine-readable path matches the visible one.',
                'Do not add crumbs to pages that have no parent, such as the home page itself.',
            ],
            'visual' => tf_flow('seo59', 'The path up', [['home', 'Milk List'], ['parent', 'How it works'], ['here', 'Add an item']], 'The visible path and the described path are the same path.'),
            'rows' => [
                ['Home', 'The site', 'A link.'],
                ['Parent', 'The section', 'How the house uses it.'],
                ['Here', 'This page', 'Not a link to itself.'],
                ['Names', 'The menu\'s names', 'Not extra queries.'],
                ['Markup', 'The same trail', 'Or none.'],
                ['False parent', 'No', 'Do not invent a category.'],
            ],
            'e1' => ['Example 1 — the trail matches the menu', 'Up is a real page.', seo_linkmap('Crumbs', <<<'HTML'
<p style="font-size:13px;color:#0f766e;">Milk List · How the house uses it · Add an item</p>
<h1 style="font-size:22px;margin:8px 0;">Add an item the night before</h1>
<p>The middle crumb opens the real parent, not a keyword page.</p>
HTML)],
            'e2' => ['Example 2 — crumbs as a keyword row', 'The path is fiction.', seo_page('Fake parents', <<<'HTML'
<p style="font-size:13px;color:#b45309;">Home · best list · cheap list · India list · app</p>
<h1 style="font-size:22px;margin:8px 0;">Add an item</h1>
<p>None of those middle words are sections of the site.</p>
HTML)],
            'micro' => [
                'On a phone, a long trail wraps and pushes the answer down. Keep it short.',
                'The offer\'s parent might be home. That is a fine, short trail.',
                'If the menu and the crumbs disagree, fix one of them today.',
            ],
            'quiz' => [
                $q('What is a breadcrumb?', 'The real path from home to this page', 'A row of queries', 'The sitemap', 'A redirect'),
                $q('Which crumb is not a link?', 'The current page', 'Home', 'The parent section', 'A real category'),
                $q('If you describe the path for machines, what must be true?', 'It matches the trail a person sees', 'It can invent a category', 'It can list keywords', 'It replaces the menu'),
            ],
            'practice' => 'For one help page, write the crumb trail using only real sections. Click each parent. If a crumb has no page, delete the crumb.',
        ]),
        $L([
            'section' => 'The content system',
            'title' => 'Markup may repeat a visible fact, not invent one',
            'keywords' => 'structured data, schema, rich results',
            'lead' => 'Markup is a labeled copy of facts that are already on the page: the name, the price, the author if there is one. Milk List may label Rs 499 only because Rs 499 is visible. Markup is not a place to claim a rating nobody gave.',
            'heading' => 'Label the truth that is already in view',
            'para' => 'If you label a product, the name and the price on the label match the name and the price in the text. If you label an article, the headline and the person match the page. Do not label a review you did not receive, a star count you chose, or a price that is lower than the one in the sentence. Extra labels do not create a result. Wrong labels create a page you would not want quoted. When you are unsure, skip the label. The visible sentence is enough.',
            'bullets' => [
                'Only facts a person can see.',
                'Price, name, and author must match.',
                'No invented stars or reviews.',
                'No markup as a hidden sales pitch.',
                'Skipping the label is always allowed.',
            ],
            'visual' => tf_panels('seo60', 'Label and page', 'Match', ['Name: Milk List', 'Price: Rs 499', 'Visible in the text'], 'Invented', ['Stars: 5', 'Price: free', 'No review exists'], 'If it is not on the page, it is not a fact you may label.'),
            'rows' => [
                ['Name', 'The product', 'Same words.'],
                ['Price', 'Rs 499', 'Same number.'],
                ['Author', 'Only if shown', 'A real person.'],
                ['Stars', 'Only if real and visible', 'Otherwise omit.'],
                ['Hidden', 'No', 'The person can see it.'],
                ['Skip', 'Allowed', 'The sentence still works.'],
            ],
            'e1' => ['Example 1 — the label matches the sentence', 'Nothing extra was claimed.', seo_page('A matched fact', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">Milk List</h1>
<p>Rs 499 a year.</p>
<p style="font-size:13px;color:#0f766e;">The labeled facts say the same name and the same price. They do not add a rating.</p>
HTML)],
            'e2' => ['Example 2 — the label is a better offer than the page', 'The machine was told a lie.', seo_page('A richer lie', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Milk List</h1>
<p>Rs 499 a year. No reviews are shown.</p>
<p style="font-size:13px;color:#b91c1c;">The label says free, and five stars from 10,000 people.</p>
HTML)],
            'micro' => [
                'A breadcrumb label, a product label, and an article label are optional repeats. They are not three chances to disagree.',
                'If the price changes, change the sentence and the label together.',
                'Testing tools tell you whether the label parses. They do not tell you whether it is true. You do.',
            ],
            'quiz' => [
                $q('What may you label?', 'A fact the person can already see', 'A star count you wish you had', 'A lower price than the page shows', 'A review that was never written'),
                $q('When should you skip markup?', 'Whenever you are unsure, or the fact is not visible', 'Never', 'On the offer, always', 'Only when the sentence is true'),
                $q('A tool says the label parses. What did it not check?', 'Whether the claim is true', 'Whether the commas are valid', 'Whether a price field exists', 'Whether the name field exists'),
            ],
            'practice' => 'List every labeled fact on the offer. Point to the sentence that shows it. Delete any label you cannot point at.',
        ]),
        $L([
            'section' => 'The content system',
            'title' => 'A missing page should offer a way home',
            'keywords' => 'custom 404, not found page, helpful error',
            'lead' => 'People mistype. Old links rot. A Milk List not-found page should say the address is missing, use a real not-found status, and offer the home and the offer. It should not pretend to be a result.',
            'heading' => 'Honest, brief, and a way onward',
            'para' => 'Say what happened in one sentence. Give two links a lost person can use: home, and the shared list. A search box of your own site is fine if it does not create indexable result URLs. Do not fill the not-found page with a sales essay and a success status. Do not redirect every typo to the offer; that hides real gaps and makes unrelated words look like the product. Log the missing paths that happen often. Those are either redirects you forgot or links you should fix.',
            'bullets' => [
                'Say it is missing. Use the not-found status.',
                'Offer home and the offer as links.',
                'Do not redirect every typo to the product.',
                'Keep your own site search from becoming a pile of results.',
                'Repeated missing paths are a repair list.',
            ],
            'visual' => tf_flow('seo61', 'A missing URL', [['say', 'not found'], ['status', 'honest'], ['links', 'home and offer']], 'A pretty error that claims success becomes a false page.'),
            'rows' => [
                ['Words', 'This address is missing', 'One sentence.'],
                ['Status', 'Not found', 'Not success.'],
                ['Links', 'Home and the offer', 'Two doors.'],
                ['Typo', 'Do not dump on the offer', 'Unless you know the successor.'],
                ['Search', 'Optional', 'Its result URLs stay out.'],
                ['Log', 'Repeated paths', 'Fix or redirect those.'],
            ],
            'e1' => ['Example 1 — a useful goodbye', 'The person knows where they are.', seo_coverage('Not found', <<<'HTML'
<p style="font-size:12px;color:#b45309;font-weight:700;">404</p>
<h1 style="font-size:22px;margin:8px 0;">That address is missing</h1>
<p><a href="#home" style="color:#0f766e;font-weight:700;">Milk List home</a></p>
<p><a href="#offer" style="color:#0f766e;font-weight:700;">The shared list</a></p>
HTML)],
            'e2' => ['Example 2 — an error that wants to rank', 'It claims to be a real page.', seo_page('A sales error', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Best lists, still a success status</h1>
<p>The essay is long, the status says the page exists, and every broken URL becomes this article.</p>
HTML)],
            'micro' => [
                'Style the page so it feels like your site. The honesty is in the status and the sentence.',
                'A soft success on thousands of typos can look like a huge thin catalog.',
                'Fix internal links that point at these paths. The not-found page is not their permanent home.',
            ],
            'quiz' => [
                $q('What must a missing page say?', 'That the address is missing, with a not-found status', 'That everything is fine', 'The full sales essay as a result', 'A redirect for every typo to the offer'),
                $q('Which links belong there?', 'Home and the main offer', 'Every URL in the sitemap as equal buttons', 'None', 'A hidden keyword list'),
                $q('What do repeated missing paths tell you?', 'A forgotten redirect or a broken internal link', 'That the error page should rank', 'That you need more typos', 'That the offer should be noindex'),
            ],
            'practice' => 'Open a URL you know does not exist. Check the status, the sentence, and the two links. Note one broken internal link if the error names a path you still use in a menu.',
        ]),
        $L([
            'section' => 'Judgment',
            'title' => 'A price claim needs more care than a slogan',
            'keywords' => 'ymyl, money claims, careful pages',
            'lead' => 'Milk List is not a hospital, but it does ask for money. A page that states Rs 499, what is included, and how to leave is a careful page. A page that says "save thousands" with no basis is not.',
            'heading' => 'Money sentences must be checkable',
            'para' => 'State the price, the period, and what the person gets. If there is a limit, say it. If you compare with the cost of wasted groceries, use a modest example you can explain, or do not use a number. Health, legal, and financial advice are a higher bar: if you are not that product, do not borrow their urgency. Anyone can publish. Pages that touch a person\'s money or safety are judged as if the reader might act on them, because they might.',
            'bullets' => [
                'Price, period, and what is included.',
                'No savings number you cannot explain.',
                'Do not borrow medical or legal urgency.',
                'The claim on the result matches the claim on the page.',
                'A way to leave is part of a money promise.',
            ],
            'visual' => tf_panels('seo62', 'A money sentence', 'Checkable', ['Rs 499 a year', 'One shared list', 'Leave anytime'], 'Loose', ['Save thousands', 'Guaranteed', 'No basis'], 'If they might pay, the sentence has to hold.'),
            'rows' => [
                ['Price', 'Exact', 'Rs 499.'],
                ['Period', 'A year', 'Say it.'],
                ['Gets', 'One list', 'Not a vague platform.'],
                ['Savings', 'Only if explainable', 'Or omit.'],
                ['Urgency', 'Do not borrow', 'Not a clinic, not a court.'],
                ['Leave', 'Visible', 'Part of the promise.'],
            ],
            'e1' => ['Example 1 — the money sentence can be checked', 'Included, period, and exit.', seo_page('A careful price', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">One shared list</h1>
<p>Rs 499 a year for one household list. Leave when you want. That is the whole charge.</p>
HTML)],
            'e2' => ['Example 2 — a number with no source', 'It sounds like advice and it is not.', seo_page('A loose claim', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Families save Rs 50,000 a month</h1>
<p>No price for the product. No example. The tone of a financial promise.</p>
HTML)],
            'micro' => [
                'A testimonial about money needs to be real, typical enough not to mislead, and permitted.',
                'If the price changes by region, the page a person opens must be the price they would pay.',
                '"Guaranteed rank" and "guaranteed savings" fail the same test.',
            ],
            'quiz' => [
                $q('What must a price sentence include?', 'The amount, the period, and what they get', 'A savings figure you hope is true', 'Medical urgency', 'A guaranteed rank'),
                $q('When is a savings number acceptable?', 'When you can explain the example modestly', 'Whenever it is large', 'When it is in an image only', 'When markup says it'),
                $q('Why be stricter about money than about a slogan?', 'A person might pay or act on it', 'Slogans are illegal', 'Prices are optional', 'Search ignores prices'),
            ],
            'practice' => 'Underline every number on the offer page. For each, write where it comes from. Remove any number you cannot explain in one calm sentence.',
        ]),
        $L([
            'section' => 'Judgment',
            'title' => 'If something is flagged, remove the cause',
            'keywords' => 'manual action, spam recovery, hacked site',
            'lead' => 'Sometimes a site is told that its results are affected because of a problem: unnatural links, hidden text, hacked pages, or doorway pages. The Milk List response is to remove the cause. It is not to hide the same cause more cleverly.',
            'heading' => 'Fix what was named, then ask for another look',
            'para' => 'Read the message and match it to a real change. If the problem is paid votes dressed as articles, take those arrangements down and stop making new ones. If the problem is hidden text or a different page for fetchers, make one honest page. If the site was compromised, clean the pages, close the hole with whoever hosts it, and replace the damaged content. A request for another look comes after the cleanup, not instead of it. You do not need a workaround. A workaround is how the problem returns.',
            'bullets' => [
                'Read the reason. Do not guess a trick.',
                'Remove the cause: schemes, hidden text, or compromised pages.',
                'Stop the behavior. A cleanup while you continue is not a cleanup.',
                'Ask for another look only after the site matches what you claim.',
                'Prevention is ordinary: updates, unique pages, no bought votes.',
            ],
            'visual' => tf_flow('seo63', 'After a flag', [['read', 'the reason'], ['remove', 'the cause'], ['then ask', 'for a look']], 'A request without a cleanup is a letter about a site you did not fix.'),
            'rows' => [
                ['Read', 'The named problem', 'Not a rumor.'],
                ['Links', 'Stop the costume', 'Remove what you arranged.'],
                ['Hidden', 'One page', 'Person and fetch match.'],
                ['Hacked', 'Clean and close', 'With the host.'],
                ['Ask', 'After', 'Not before.'],
                ['Again', 'Do not resume', 'The scheme stays retired.'],
            ],
            'e1' => ['Example 1 — the cause is actually gone', 'The site matches the explanation.', seo_coverage('Cleaned', <<<'HTML'
<p style="font-size:12px;color:#0f766e;font-weight:700;">AFTER</p>
<p>The paid posts are down. The offer is the same page for everyone. New content is the real list, not a doorway. Then, and only then, ask for another look.</p>
HTML)],
            'e2' => ['Example 2 — a request on top of the same scheme', 'Nothing changed but the letter.', seo_page('Still doing it', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">We requested a review</h1>
<p>The empty doorway pages are still up. The bought posts are still live. The letter says they are gone.</p>
HTML)],
            'micro' => [
                'A compromise is a security incident. Change access, do not only delete one spam page.',
                'If you inherited a site, look for old doorway pages and old paid arrangements before you add yours.',
                'Ordinary ranking movement is not a manual flag. Do not "clean up" by deleting your real pages in a panic.',
            ],
            'quiz' => [
                $q('What do you do first when a problem is named?', 'Remove the cause that was named', 'Send a letter and keep the pages', 'Hide the text better', 'Buy a new domain and repeat it'),
                $q('When do you ask for another look?', 'After the site matches the explanation', 'Before any change', 'Instead of cleaning a compromised site', 'Every week by habit'),
                $q('What is not a manual flag?', 'Ordinary movement in rankings', 'A compromised page', 'Hidden text', 'A network of paid votes'),
            ],
            'practice' => 'Write the three causes this course refuses: disguised paid votes, a different page for fetchers, and doorway pages with no answer. Confirm none of them are live on your site.',
        ]),
        $L([
            'section' => 'Judgment',
            'title' => 'A disavow file is rare, not a routine',
            'keywords' => 'disavow links, unnatural links, link denial',
            'lead' => 'There is a way to ask that some links to you be ignored. It is for links you are responsible for and cannot get taken down, usually after a real problem. It is not a monthly chore, not a weapon, and not a fix for ordinary competition.',
            'heading' => 'Most sites should never touch it',
            'para' => 'Random sites link to everyone. You do not need to deny them. You cannot and should not try to use this to harm another site. If you paid for a pattern of votes, your first job is to stop and to remove what you can. A denial file is a last note about the leftovers you cannot remove, and only when you understand that you caused them. Uploading a huge file of ordinary links can throw away real reputation. If you are unsure, do not upload one.',
            'bullets' => [
                'Rare. Not a routine. Not a ranking trick.',
                'Not a tool against someone else.',
                'First remove arrangements you made.',
                'A careless file can discard real mentions.',
                'If you are unsure, do nothing with it.',
            ],
            'visual' => tf_split('seo64', 'Upload a denial?', 'Are you sure you caused leftover paid votes you cannot remove?', 'No', 'Do not upload', 'Yes, and you already cleaned', 'Then it is a last note', 'A routine file is how real mentions get discarded.'),
            'rows' => [
                ['Ordinary links', 'Leave them', 'The web is messy.'],
                ['Rival', 'Not a target', 'This is not a weapon.'],
                ['Paid pattern', 'Stop and remove', 'First.'],
                ['Leftovers', 'Last note', 'Only what you caused.'],
                ['Huge file', 'Dangerous', 'You may discard the real ones.'],
                ['Unsure', 'Stop', 'No upload.'],
            ],
            'e1' => ['Example 1 — the right decision for a normal site', 'No file. The page keeps earning ordinary mentions.', seo_page('No file', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Nothing to deny</h1>
<p>A few odd sites mentioned the list. You did not pay them. You do not upload a file. You keep publishing the offer.</p>
HTML)],
            'e2' => ['Example 2 — a monthly ritual', 'Real mentions go in the bin.', seo_page('A ritual', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">We deny every new link</h1>
<p>Including the household blog that actually recommended the product. The file is treated as a chore.</p>
HTML)],
            'micro' => [
                'You will see spammy-looking links you never asked for. That is common. It is not an emergency.',
                'Buying links and then denying them is not a clever cycle. Stop buying them.',
                'If a professional insists everyone needs a file, ask what you personally caused. If the answer is vague, decline.',
            ],
            'quiz' => [
                $q('Who is a denial file for?', 'A site cleaning up paid votes it caused and cannot fully remove', 'Every site, monthly', 'A way to slow a rival', 'A new site with no links'),
                $q('What do you do about odd links you never bought?', 'Leave them alone', 'Deny all of them every week', 'Buy better ones to dilute them', 'Hide the offer'),
                $q('What can a careless file do?', 'Discard real recommendations', 'Improve a title', 'Fix a slow page', 'Create a hub'),
            ],
            'practice' => 'Write one sentence: "We will not upload a denial file unless we caused a paid pattern and have already removed what we can." If a vendor told you otherwise, keep the sentence anyway.',
        ]),
        $L([
            'section' => 'Judgment',
            'title' => 'Another language needs a real page',
            'keywords' => 'hreflang, multilingual seo, translation',
            'lead' => 'If Milk List ever has a real Hindi page and a real English page, each is a full page in that language, with its own URL. A translated title on the English page is not a Hindi site. A machine paragraph nobody checked is not a page you should offer.',
            'heading' => 'Translate the promise, not just the label',
            'para' => 'Each language gets a URL a person can read from the first screen to the price and the button. The pages should point to each other as alternates, including each one pointing at itself, and the words of that hint must match the language on the page. Do not point a Hindi hint at an English article. Do not auto-redirect a person by guesswork so they cannot reach the other language. Country and language are not the same: India is not a language, and Hindi is not a country.',
            'bullets' => [
                'A full page per language, or do not offer that language.',
                'Each URL names itself and the real alternates.',
                'The hint language matches the page language.',
                'People can choose. Do not trap them by a guess.',
                'Do not label a single page as every language.',
            ],
            'visual' => tf_panels('seo65', 'Two languages', 'English URL', ['Full page', 'Names itself', 'Names the Hindi URL'], 'Hindi URL', ['Full page', 'Names itself', 'Names the English URL'], 'A translated title on one URL is not a second language.'),
            'rows' => [
                ['Page', 'Complete', 'Price and step included.'],
                ['URL', 'Its own', 'Not a title swap.'],
                ['Hint', 'Both ways', 'Each names itself too.'],
                ['Match', 'Language', 'Hindi hint, Hindi page.'],
                ['Choice', 'A link', 'No forced redirect.'],
                ['Country', 'Not a language', 'Do not confuse them.'],
            ],
            'e1' => ['Example 1 — two complete pages', 'Each can stand alone.', seo_linkmap('Two languages', <<<'HTML'
<p style="font-weight:800;">English</p>
<p>One shared list. Rs 499 a year. Start the list.</p>
<p style="font-weight:800;margin-top:8px;">Hindi</p>
<p>The same offer, actually written in Hindi, on its own URL, with a way back to English.</p>
HTML)],
            'e2' => ['Example 2 — one page wearing every label', 'The hint is a costume.', seo_page('Every language, one text', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">One shared list</h1>
<p>The page is English. The hints claim Hindi, Tamil, and French. None of those pages exist.</p>
HTML)],
            'micro' => [
                'A checked human edit beats an unchecked machine draft you publish at scale.',
                'If you only serve one language, do not add the machinery "for later".',
                'Currency and examples should make sense for the people reading that language.',
            ],
            'quiz' => [
                $q('What counts as a Hindi page?', 'A full Hindi URL with the real offer', 'An English page with a Hindi title', 'A hint that says Hindi', 'A machine paragraph nobody read'),
                $q('What should each language URL declare?', 'Itself and the real alternates', 'Every language you might add someday', 'Only the homepage', 'Nothing, and a forced redirect'),
                $q('Why is country not language?', 'A country can read many languages, and a language crosses countries', 'They are the same field', 'India is a language', 'Hindi is a host name'),
            ],
            'practice' => 'If you have only one language, write "one language" and stop. If you have two, open both URLs and confirm each is complete and each can reach the other.',
        ]),
        $L([
            'section' => 'Judgment',
            'title' => 'Local search is only for a place you serve',
            'keywords' => 'local seo, business profile, service area',
            'lead' => 'Local results are for a place: a shop, a clinic, a service that visits homes. Milk List is a product on the web. It should not invent a map pin, a fake address, or a city page for every town in the country.',
            'heading' => 'Do not borrow a city you do not serve',
            'para' => 'If you someday have a real counter or a real visiting area, the name, address, phone, and hours must match what a person finds, and the site should make that place obvious. Until then, do not create "shopping list Delhi", "shopping list Jaipur", and a hundred twins that swap the city. That is a doorway pattern. A person searching "near me" wants a nearby thing. Sending them to a nationwide app through a fake pin is a broken promise.',
            'bullets' => [
                'No pin without a real place.',
                'No city pages that only swap the city name.',
                'Name, address, phone, and hours must match if they exist.',
                'Near-me intent is local. Do not costume a website as a shop.',
                'You can still rank for the product without pretending to be on a street.',
            ],
            'visual' => tf_split('seo66', 'A map pin?', 'Is there a real place?', 'Yes', 'Publish the true facts', 'No', 'Do not invent one', 'A hundred city swaps are doorways, not local service.'),
            'rows' => [
                ['Pin', 'Only if real', 'A counter you have.'],
                ['Hours', 'True', 'Or omit.'],
                ['Phone', 'Answered', 'The one on the site.'],
                ['City pages', 'No swaps', 'Each page needs a real reason.'],
                ['Near me', 'A local job', 'Not a costume.'],
                ['Product', 'Still valid', 'The offer URL, no pin.'],
            ],
            'e1' => ['Example 1 — a web product stays a web product', 'No fake geography.', seo_page('No pin', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">Milk List</h1>
<p>A shared list on the web, Rs 499 a year. There is no shop address because there is no shop. The page does not invent one.</p>
HTML)],
            'e2' => ['Example 2 — a city template', 'The same page with the city swapped.', seo_page('City doorways', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Shopping list in Jaipur</h1>
<p>The body is identical to the Delhi page, the Pune page, and forty others. Nobody serves those streets.</p>
HTML)],
            'micro' => [
                'A service area is still a promise. Do not draw a circle you will not travel.',
                'Reviews on a local profile have to be from real customers, gathered without a script or a reward for the wording.',
                'If your address changes, change it everywhere it is published, including the site.',
            ],
            'quiz' => [
                $q('When may Milk List publish a map pin?', 'When there is a real place a person can use', 'When a city name might be searched', 'When a template can swap city names', 'Always, to look established'),
                $q('What is a city doorway?', 'The same page repeated with the city swapped', 'A real shop\'s hours', 'The offer URL', 'A help article'),
                $q('What does a near-me search want?', 'Something nearby', 'Any site that mentions a city', 'A fake pin', 'A translated title'),
            ],
            'practice' => 'Write whether you serve a physical place. If no, list any city pages or pins to remove. If yes, check that the address on the site matches the address a person would visit.',
        ]),
    ];
}
