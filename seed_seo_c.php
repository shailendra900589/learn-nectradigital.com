<?php
declare(strict_types=1);

function seo_chapters_c(): array
{
    $L = static function (array $c): array {
        return track_lesson('visual', '', $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };

    return [
        $L([
            'section' => 'The site',
            'title' => 'Some pages should stay out of the index',
            'keywords' => 'noindex, robots, pages to exclude',
            'lead' => 'Not every URL is a result. Milk List keeps the offer in and keeps the thank-you page, the account screen, and thin filters out. Staying out is a decision, not a failure.',
            'heading' => 'Say which URLs are not answers',
            'para' => 'A public page that answers a query should be reachable and eligible. A page that only exists after someone starts, or that has nothing unique to say, should not be offered as a result. Use one clear method and do not contradict it with a sitemap entry. Blocking a fetch and also asking for the URL to be indexed is a mixed message.',
            'bullets' => [
                'Thank-you, account, and empty filter URLs stay out.',
                'The offer and real help stay in.',
                'Do not list an excluded URL as a page you want considered.',
                'A reason you can say aloud is enough. You do not need a trick.',
            ],
            'visual' => tf_split('seo29', 'A result?', 'Would a stranger search for it?', 'Yes', 'Let it be eligible', 'No', 'Keep it out', 'Staying out is a decision.'),
            'rows' => [
                ['In', 'The offer', 'It answers a query.'],
                ['Out', 'Thank-you', 'Only a confirmation.'],
                ['Out', 'Account', 'Private to one person.'],
                ['Out', 'Empty filter', 'Nothing unique.'],
                ['List', 'Match the choice', 'No contradiction.'],
                ['Block', 'Only when you mean it', 'Do not block the offer.'],
            ],
            'e1' => ['Example 1 — the confirmation is not a result', 'It has a job. That job is not search.', seo_coverage('Kept out', <<<'HTML'
<p style="font-size:12px;color:#b45309;font-weight:700;">NOT A RESULT</p>
<h1 style="font-size:22px;margin:8px 0;">You started the list</h1>
<p>This page confirms the step. It is not the page for "family shopping list".</p>
HTML)],
            'e2' => ['Example 2 — the offer was locked out by accident', 'The page you need cannot be fetched.', seo_coverage('Blocked offer', <<<'HTML'
<p style="font-size:12px;color:#b91c1c;font-weight:700;">BLOCKED</p>
<h1 style="font-size:22px;margin:8px 0;">shared-list</h1>
<p>The only answer on the site was marked unreachable. Nothing useful can be shown.</p>
HTML)],
            'micro' => [
                'Staging sites should not compete with the real site.',
                'Search parameters that create endless empty URLs should not become results.',
                'If you change your mind, remove the contradiction before you wait for a result.',
            ],
            'quiz' => [
                $q('Which page should stay out?', 'The thank-you confirmation', 'The offer that answers the query', 'A real help article', 'The price page'),
                $q('What is a mixed message?', 'Asking for a URL to be considered and also blocking the fetch', 'Linking the offer from home', 'Leaving a draft off the sitemap', 'One title and a matching H1'),
                $q('Why is staying out sometimes right?', 'The URL is not an answer to a search', 'Fewer pages always rank higher', 'Indexes reject useful pages', 'Sitemaps require empty URLs'),
            ],
            'practice' => 'Make two lists: URLs that may be results, and URLs that must not. Check that the second list is absent from the sitemap.',
        ]),
        $L([
            'section' => 'The site',
            'title' => 'A redirect keeps an address you moved',
            'keywords' => '301 redirect, moved page, seo redirect',
            'lead' => 'When the shared-list page changes address, the old URL should send people and fetches to the new one. A broken old address throws away every link and every memory of the page.',
            'heading' => 'One hop to the new home',
            'para' => 'Use a permanent redirect when the move is real. Send the old URL to the matching new URL, not to the homepage, unless the old page truly has no successor. One hop is enough. A chain of three redirects is a slow, fragile path. Keep the redirect for as long as anything might still use the old address.',
            'bullets' => [
                'Old URL to the specific new URL.',
                'Permanent when the move is permanent.',
                'One hop, not a chain.',
                'Do not point every retired URL at the homepage by habit.',
            ],
            'visual' => tf_flow('seo30', 'A move', [['old', '/list'], ['hop', 'one redirect'], ['new', '/shared-list']], 'The homepage is not a universal destination.'),
            'rows' => [
                ['From', 'The old path', '/list'],
                ['To', 'The same topic', '/shared-list'],
                ['Kind', 'Permanent move', 'It really moved.'],
                ['Hops', 'One', 'Not a chain.'],
                ['Home', 'Only if nothing matches', 'Rare.'],
                ['Keep', 'A long time', 'Old links live on.'],
            ],
            'e1' => ['Example 1 — the old address opens the same topic', 'One hop, same promise.', seo_coverage('Moved', <<<'HTML'
<p style="font-size:12px;color:#0f766e;font-weight:700;">ONE HOP</p>
<p>/list → /shared-list</p>
<p>The person lands on the shared list, not on a generic home page.</p>
HTML)],
            'e2' => ['Example 2 — every old URL dumps on home', 'The topic was thrown away.', seo_coverage('Dumped', <<<'HTML'
<p style="font-size:12px;color:#b45309;font-weight:700;">TO HOME</p>
<p>/list → /</p>
<p>They asked for the list and got the front door, which talks about everything.</p>
HTML)],
            'micro' => [
                'Update your own links too. A redirect is a safety net, not the main path.',
                'Temporary and permanent are different promises. Use the one you mean.',
                'Test the old URL in a browser after the move.',
            ],
            'quiz' => [
                $q('Where should a moved offer URL send people?', 'To the new URL for that same offer', 'Always to the homepage', 'To a random help page', 'Nowhere'),
                $q('How many hops do you want?', 'One', 'As many as the folders require', 'A loop', 'Zero, and a broken old URL'),
                $q('How long do you keep the redirect?', 'As long as old links may exist', 'Until tomorrow', 'One hour', 'You never add one'),
            ],
            'practice' => 'List any URL you have retired. Write the specific destination for each. If the destination is home, justify it in one sentence or pick a better page.',
        ]),
        $L([
            'section' => 'The site',
            'title' => 'Duplicate pages split one topic',
            'keywords' => 'duplicate content, duplicate urls',
            'lead' => 'Two URLs with the same Milk List offer make search pick one, or pick neither cleanly. The person may land on an old version. Duplication is a split, not a bonus.',
            'heading' => 'One URL for one promise',
            'para' => 'Duplicates appear when a page is reachable with and without a trailing mark, with tracking tags, or as a print version, or because a redesign left the old URL alive. Choose the owner. Redirect the extras or mark them so the owner is obvious. Do not write a second page that says the same thing in shuffled sentences.',
            'bullets' => [
                'Pick the owner URL.',
                'Redirect true duplicates to it.',
                'Tracking tags should not create a second indexable page.',
                'Shuffled sentences are still the same page.',
            ],
            'visual' => tf_split('seo31', 'Same promise?', 'Yes', 'One owner', 'Redirect the extra', 'A new question', 'A new page', 'Shuffled words are not a new topic.'),
            'rows' => [
                ['Owner', 'One path', '/shared-list'],
                ['Twin', 'Same words', 'Redirect it.'],
                ['Tag', 'A campaign label', 'Not a new page.'],
                ['Print', 'Same content', 'Not a second result.'],
                ['Old', 'After a redesign', 'Redirect.'],
                ['Shuffle', 'Still duplicate', 'Do not publish it.'],
            ],
            'e1' => ['Example 1 — extras point at the owner', 'There is one result worth showing.', seo_coverage('One owner', <<<'HTML'
<p style="font-size:12px;color:#0f766e;font-weight:700;">OWNER</p>
<p>/shared-list</p>
<p>/shared-list/ and the tagged URL both send people to that owner.</p>
HTML)],
            'e2' => ['Example 2 — three URLs, one essay', 'The topic is split.', seo_page('Three twins', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">The same offer, three times</h1>
<p>/list /shared-list /best-list all say Rs 499 and one shared list, in a different order.</p>
HTML)],
            'micro' => [
                'A small template line repeated on every page, like a footer, is not the problem. The main promise is.',
                'Syndicating your whole article onto another site can create a twin you do not control. Prefer a summary and a link home.',
                'Look at coverage reports for "duplicate" before you rewrite titles.',
            ],
            'quiz' => [
                $q('What is the problem with two URLs and one promise?', 'The topic is split', 'You get two ranks for free', 'The sitemap gets shorter', 'The phone layout improves'),
                $q('What should a true duplicate do?', 'Send people to the owner', 'Stay and compete', 'Change three words and pretend', 'Hide different text'),
                $q('What is not the main duplicate problem?', 'A short footer line on every page', 'A print URL of the same article', 'An old path left alive', 'A tagged URL that is indexable'),
            ],
            'practice' => 'Search your own site for the price "Rs 499". Every URL that shows it as the main promise should be the owner or a redirect to the owner.',
        ]),
        $L([
            'section' => 'The site',
            'title' => 'Canonical names the copy that counts',
            'keywords' => 'canonical url, rel canonical',
            'lead' => 'A canonical is a hint: this URL is the copy that counts. Milk List points /shared-list at itself, and points a tagged or print URL at /shared-list. The hint should agree with your redirects and your sitemap.',
            'heading' => 'One preferred URL, said plainly',
            'para' => 'Use a canonical when the same page can be opened more than one way and you cannot simply redirect. The preferred URL should be the one in the sitemap and the one your links use. Do not point the offer at the homepage, and do not point every article at the offer. A canonical is not a way to combine unrelated pages.',
            'bullets' => [
                'The owner points at itself.',
                'Variants point at the owner.',
                'Links and the sitemap use the owner.',
                'Unrelated pages do not share a canonical.',
            ],
            'visual' => tf_flow('seo32', 'Which copy counts', [['variant', 'a tag or print'], ['hint', 'the owner'], ['owner', '/shared-list']], 'The hint, the links, and the sitemap should agree.'),
            'rows' => [
                ['Owner', 'Points to itself', '/shared-list'],
                ['Variant', 'Points to owner', 'A tagged URL.'],
                ['Sitemap', 'The owner', 'Not the variant.'],
                ['Links', 'The owner', 'Inside the site.'],
                ['Home', 'Not a dumping hint', 'Unless it is the home page.'],
                ['Unrelated', 'No shared hint', 'Different topics stay apart.'],
            ],
            'e1' => ['Example 1 — the hint matches the map', 'Variants defer to the offer.', seo_coverage('Canonical', <<<'HTML'
<p style="font-size:12px;color:#0f766e;font-weight:700;">COUNTS</p>
<p>Preferred: /shared-list</p>
<p>The print view and the tagged view both name that URL.</p>
HTML)],
            'e2' => ['Example 2 — every page claims to be the homepage', 'The hint throws the topics away.', seo_coverage('A bad hint', <<<'HTML'
<p style="font-size:12px;color:#b45309;font-weight:700;">ALL POINT HOME</p>
<p>The offer, the help page, and the price page each say the homepage is the copy that counts.</p>
HTML)],
            'micro' => [
                'If you can redirect, a redirect is clearer than a hint.',
                'A canonical across two different topics is how you hide a page by accident.',
                'Check one variant in the report and see which URL was selected.',
            ],
            'quiz' => [
                $q('What should a print view of the offer point at?', 'The offer URL', 'The homepage', 'A random help page', 'Itself as a second owner'),
                $q('What should the sitemap list?', 'The preferred URL', 'Every tagged variant', 'Only drafts', 'Nothing'),
                $q('When is a redirect clearer than a canonical?', 'When the extra URL does not need to stay open as its own page', 'When the topics differ', 'When you want two results', 'Never'),
            ],
            'practice' => 'Write the preferred URL for the offer. List every other way it can be opened, and the hint or redirect each one should use.',
        ]),
        $L([
            'section' => 'The site',
            'title' => 'Status codes tell the truth about a URL',
            'keywords' => 'http status, 200, 301, 404, 410',
            'lead' => 'A status is a plain answer about a URL. Milk List says "here it is" for the offer, "moved" for the old path, and "gone" for a page that will not return. A soft message that looks like a page but is actually missing confuses everyone.',
            'heading' => 'The code should match the story',
            'para' => 'A real page returns success. A permanent move returns a redirect. A missing page returns not found. A page you removed on purpose can say gone. Do not return success for a pretty "we cannot find that" screen, because that screen then looks like a real page. Do not redirect a missing URL to an unrelated offer and call it helpful.',
            'bullets' => [
                'Success means the page is really there.',
                'A redirect means it moved, to the right place.',
                'Not found means it is missing. Gone means it will not be back.',
                'A soft missing page that claims success is a false page.',
            ],
            'visual' => tf_panels('seo33', 'Tell the truth', 'Honest', ['200 the offer', '301 the move', '404 the typo'], 'Misleading', ['200 on an error screen', '301 to a random offer', 'A loop'], 'The code is part of the sentence.'),
            'rows' => [
                ['200', 'Here it is', 'The offer.'],
                ['301', 'Moved', 'Old path to new path.'],
                ['404', 'Missing', 'A typo.'],
                ['410', 'Gone on purpose', 'Will not return.'],
                ['Soft 404', 'A lie', 'Success on an error.'],
                ['Loop', 'No answer', 'Fix the chain.'],
            ],
            'e1' => ['Example 1 — the offer really is there', 'Success matches the page.', seo_coverage('Here', <<<'HTML'
<p style="font-size:12px;color:#0f766e;font-weight:700;">200</p>
<h1 style="font-size:22px;margin:8px 0;">shared-list</h1>
<p>The page is the offer. The status agrees.</p>
HTML)],
            'e2' => ['Example 2 — a missing page pretends to exist', 'The status and the screen disagree.', seo_coverage('A soft miss', <<<'HTML'
<p style="font-size:12px;color:#b45309;font-weight:700;">200 ON AN ERROR</p>
<h1 style="font-size:22px;margin:8px 0;">Not found, but success</h1>
<p>The screen says missing. The status says this is a real page.</p>
HTML)],
            'micro' => [
                'A friendly error page is good. It should still use a not-found status.',
                'Redirects that depend on cookies can show a different story to a fetch than to you. Test simply.',
                'A loop wastes the crawl and the person.',
            ],
            'quiz' => [
                $q('What status fits a page that permanently moved?', 'A redirect to the new URL', 'Success on the old URL with a note', 'Not found, with no new URL', 'A loop'),
                $q('What is a soft miss?', 'An error screen that claims success', 'A real offer with success', 'A permanent redirect', 'A gone page that says gone'),
                $q('Where should a typo URL send someone?', 'A not-found page, unless you know the exact successor', 'Always the offer, even if the topics differ', 'An endless chain', 'A success status with no page'),
            ],
            'practice' => 'Check the offer, one old URL, and one typo. Write the status you got and the status you meant. Fix any disagreement.',
        ]),
        $L([
            'section' => 'Trust',
            'title' => 'Experience and proof belong on the page',
            'keywords' => 'eeat, trust, proof on page',
            'lead' => 'Trust on a search page is specific. Milk List shows who the product is for, what it costs, and a detail only someone who built the list would bother to say. Vague authority words do not do that work.',
            'heading' => 'Show the work, do not announce that you are trusted',
            'para' => 'Say what a household actually does with the list. Put a real price. Name the company plainly. If a person wrote the guide, a byline and a way to know their experience helps. A badge that says "trusted" without a checkable fact is decoration. Reviews you did not earn do not belong here.',
            'bullets' => [
                'Concrete details beat adjectives.',
                'The price and the limit are visible.',
                'A byline helps when a person\'s experience matters.',
                'Do not invent credentials or quotes.',
            ],
            'visual' => tf_panels('seo34', 'Proof', 'On the page', ['Rs 499', 'One list', 'Leave anytime'], 'Empty claim', ['Trusted', 'Number one', 'Award, unnamed'], 'A fact a person can check beats a badge.'),
            'rows' => [
                ['Who', 'The household', 'Named on the page.'],
                ['Price', 'Visible', 'Rs 499.'],
                ['Detail', 'Specific', 'Milk added at night.'],
                ['Byline', 'When it matters', 'A real person.'],
                ['Badge', 'Not proof', 'Unless it is checkable.'],
                ['Quote', 'Only if real', 'With permission.'],
            ],
            'e1' => ['Example 1 — a detail only the product would have', 'The page sounds like it has been used.', seo_page('A real detail', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">One list, still there in the morning</h1>
<p>Add milk at night. The person who shops sees it without a second chat. Rs 499 a year. Leave when you want.</p>
HTML)],
            'e2' => ['Example 2 — trust as an adjective', 'Nothing can be checked.', seo_page('Badges', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">The trusted award-winning leader</h1>
<p>No price. No person. No description of the list.</p>
HTML)],
            'micro' => [
                'Experience can be the product\'s behavior, not only an author bio.',
                'A stock photo of a stranger does not create experience.',
                'If you cite a number, know where it came from and do not inflate it.',
            ],
            'quiz' => [
                $q('What belongs on the page as proof?', 'A checkable detail, like the price and what the list does', 'The word trusted, alone', 'An invented quote', 'A stock crowd'),
                $q('When does a byline help?', 'When a person\'s experience is part of why the page is credible', 'On every button', 'Instead of the product', 'Never, because pages should be anonymous slogans'),
                $q('What is not proof?', 'An unnamed award', 'Rs 499 a year', 'A specific morning-after behavior', 'A way to leave'),
            ],
            'practice' => 'Underline every fact on the offer page a stranger could check. If you underlined nothing, add one true detail and remove one empty adjective.',
        ]),
        $L([
            'section' => 'Trust',
            'title' => 'A link is earned reputation',
            'keywords' => 'backlinks, earned links, mentions',
            'lead' => 'A link from another site is someone pointing at you. It is meaningful when they do it because the page helped. Milk List would rather have one explanation from a household blog than a hundred identical blurbs.',
            'heading' => 'Be worth pointing at',
            'para' => 'You earn a link by publishing something a specific person needed: a clear comparison, a useful how-to, a page that answers the query better than the one they already have. You can tell the right people the page exists. You cannot demand the link, buy it, or hide it in a scheme of sites that exist only to point. The page still has to deserve the visit after the click.',
            'bullets' => [
                'The page must be worth the reference.',
                'Tell relevant people. Do not spam inboxes.',
                'One real mention beats a pattern of identical blurbs.',
                'The link should help their reader, not only you.',
            ],
            'visual' => tf_flow('seo35', 'A real mention', [['page', 'worth it'], ['person', 'their reader'], ['link', 'their choice']], 'You can offer the page. You cannot order the point.'),
            'rows' => [
                ['Worth', 'A real answer', 'The comparison or the how-to.'],
                ['Tell', 'A relevant person', 'Once, plainly.'],
                ['Spam', 'No', 'No mass demands.'],
                ['Pattern', 'Suspicious', 'Identical blurbs.'],
                ['Reader', 'Theirs first', 'The link must help them.'],
                ['After', 'Your page', 'Still has to deliver.'],
            ],
            'e1' => ['Example 1 — a page someone could cite', 'It teaches a distinction.', seo_linkmap('Worth citing', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Paper, a chat, or one list</h1>
<p>A clear comparison a household site could reference because their readers share a kitchen.</p>
HTML)],
            'e2' => ['Example 2 — a request with nothing to cite', 'There is no reason to point.', seo_page('Nothing to cite', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Please link to us</h1>
<p>The page is a slogan. The note went to a hundred strangers.</p>
HTML)],
            'micro' => [
                'A mention without a link can still be real reputation. Do not obsess over only the clickable kind.',
                'Links from pages that exist only to link are not reputation.',
                'No, you do not trade a link for a link as a quota.',
            ],
            'quiz' => [
                $q('When is a link meaningful?', 'When someone points because the page helped their reader', 'When you bought a hundred identical ones', 'When you demanded it from strangers', 'When it comes from a site with no readers'),
                $q('What can you honestly do?', 'Tell a relevant person the page exists', 'Hide a network of sites that only point', 'Pay for votes', 'Spam a directory form'),
                $q('What has to remain true after the click?', 'The page delivers what the mention implied', 'The page can be empty', 'The page can be a different offer', 'The page can be a login wall'),
            ],
            'practice' => 'Name one page you have that a specific kind of site might cite. Write the reason their reader would care. If you cannot, improve the page before you tell anyone.',
        ]),
        $L([
            'section' => 'Trust',
            'title' => 'A bought link is not a vote',
            'keywords' => 'paid links, link schemes, unnatural links',
            'lead' => 'Paying for a mention that pretends to be a recommendation is not SEO. It is a costume. Milk List does not buy votes, does not join a circle of sites that only exist to point, and does not hide a paid placement as if it were a review.',
            'heading' => 'If money changed hands, say so or do not do it',
            'para' => 'An ad can be labeled and can link to you. That is a rental, and it should look like one. A "guest article" that is only a paragraph and a paid vote is not a guest article. A network of empty sites built to point at you is a scheme, even if you never learn the private setup. You refuse the offer. You do not need the instructions.',
            'bullets' => [
                'Labeled ads are ads. Unlabeled paid votes are not reviews.',
                'A circle of empty sites is not a library.',
                'You do not need a playbook to decline.',
                'Earn the mention by being useful, or run an honest ad.',
            ],
            'visual' => tf_panels('seo36', 'Money and a link', 'Honest', ['An ad, labeled', 'A real citation', 'No payment for the vote'], 'A costume', ['Paid and unlabeled', 'Empty sites', 'A quota of posts'], 'If it has to pretend, it is not a vote.'),
            'rows' => [
                ['Ad', 'Labeled', 'A rental.'],
                ['Review', 'Unpaid', 'Their words.'],
                ['Costume', 'Paid, looks organic', 'Do not buy it.'],
                ['Network', 'Sites with no readers', 'Not reputation.'],
                ['Quota', 'Ten posts a month', 'A scheme.'],
                ['Instead', 'A better page', 'Or a labeled ad.'],
            ],
            'e1' => ['Example 1 — a labeled placement stays a placement', 'Nobody is asked to mistake it for a citation.', seo_page('A labeled ad', <<<'HTML'
<p style="font-size:12px;color:#0f766e;font-weight:700;">AD</p>
<h1 style="font-size:22px;margin:8px 0;">Milk List</h1>
<p>A rented placement that says it is an ad and opens the real offer.</p>
HTML)],
            'e2' => ['Example 2 — a vote in a costume', 'The payment is the whole relationship.', seo_page('A costume', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Ten guest posts</h1>
<p>Each post is two sentences and a paid link. The sites have no readers and no topic except pointing.</p>
HTML)],
            'micro' => [
                'A directory that a person actually uses is different from a page of paid links.',
                'If a vendor promises "safe votes", the word safe is the warning.',
                'Removing a scheme later is harder than refusing it.',
            ],
            'quiz' => [
                $q('What can you pay for honestly?', 'A labeled ad', 'A fake review', 'A network of empty sites', 'A quota of disguised posts'),
                $q('What is a bought vote?', 'A mention that pretends to be independent', 'A price on your own page', 'A real citation', 'A redirect you own'),
                $q('What do you need in order to refuse?', 'To see that it pretends', 'The private setup', 'A vendor account', 'A list of empty domains'),
            ],
            'practice' => 'If you have ever been offered mentions for a fee, write how you would label them as ads instead, or why you would decline. Do not write the vendor\'s method.',
        ]),
        $L([
            'section' => 'Trust',
            'title' => 'Your name should agree across the web',
            'keywords' => 'brand consistency, nap, knowledge of the name',
            'lead' => 'Milk List should be the same name, the same offer, and the same main URL wherever a person or a profile repeats you. A second spelling and a second homepage make you look like two half-finished products.',
            'heading' => 'One name, one home, one sentence',
            'para' => 'Pick the public name and use it in the title, the site, and the profiles you actually maintain. The main URL in those profiles should be the offer or the home that leads straight to it. If you are local, the address and phone should match. If you are not local, do not invent a map pin. Close profiles you will not update.',
            'bullets' => [
                'One public name.',
                'Profiles you keep should point at the real site.',
                'Facts that can be checked must match.',
                'Do not open five networks you will abandon.',
            ],
            'visual' => tf_flow('seo37', 'The same facts', [['name', 'Milk List'], ['home', 'one URL'], ['sentence', 'one shared list']], 'Abandoned profiles drift. Close them or tend them.'),
            'rows' => [
                ['Name', 'Stable', 'Milk List.'],
                ['URL', 'The real site', 'Not a dead profile.'],
                ['Sentence', 'The same offer', 'One shared list.'],
                ['Local', 'Only if true', 'No fake pin.'],
                ['Match', 'Phone and address', 'When you have them.'],
                ['Drift', 'Untended profiles', 'Close or update.'],
            ],
            'e1' => ['Example 1 — the profile repeats the site', 'A person who checks both sees one company.', seo_page('One name', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Milk List</h1>
<p>One shared household list. Home: milklist.example/shared-list</p>
HTML)],
            'e2' => ['Example 2 — three names and a dead link', 'Checking the facts makes it worse.', seo_page('Drift', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Milkly / ListCo / ML App</h1>
<p>The profile link opens a retired page. The price on the profile is not the price on the site.</p>
HTML)],
            'micro' => [
                'A rename needs redirects and a pass over every profile. Do it rarely.',
                'Consistent does not mean identical essays pasted everywhere.',
                'If a directory lists you wrongly, ask for a correction. Do not build a second fake listing.',
            ],
            'quiz' => [
                $q('What should agree?', 'The name, the main URL, and the offer', 'Five abandoned bios', 'A fake map pin', 'A different price in each place'),
                $q('What do you do with a profile you will not maintain?', 'Close it or update it', 'Leave a dead link', 'Point it at a retired URL', 'Invent a second brand there'),
                $q('When do you publish an address?', 'When it is a real place you serve', 'Always, even if you are only on the web', 'To look local', 'On a pin you do not control'),
            ],
            'practice' => 'Write the public name, the main URL, and one sentence. Check three places those facts appear. Fix the one that drifted.',
        ]),
        $L([
            'section' => 'Measurement',
            'title' => 'Search Console shows queries and pages',
            'keywords' => 'search console, queries, pages report',
            'lead' => 'Search Console is the report of what search actually did with your pages. For Milk List it shows which queries showed the site, which pages were involved, and where a URL was left out. It is not a scoreboard of fame.',
            'heading' => 'Read the rows that name a query and a page',
            'para' => 'Start with the offer URL. See the queries that showed it. Then see whether those queries match the intent you wrote the page for. A coverage note tells you if the URL was fetched and kept. Vanity totals that hide the query are less useful than ten specific rows.',
            'bullets' => [
                'Queries and pages, together.',
                'The offer URL first.',
                'Coverage explains absence. It is not an insult.',
                'A total without a query is not a decision.',
            ],
            'visual' => tf_flow('seo38', 'What to open', [['page', 'the offer'], ['queries', 'what showed'], ['coverage', 'was it kept']], 'Ten specific rows beat one proud total.'),
            'rows' => [
                ['Page', 'The offer', '/shared-list'],
                ['Query', 'What was typed', 'Family shopping list.'],
                ['Match', 'Intent', 'Decide, or not.'],
                ['Coverage', 'Kept or not', 'Read the reason.'],
                ['Total', 'Weak alone', 'Ask which query.'],
                ['Action', 'One change', 'Then look again.'],
            ],
            'e1' => ['Example 1 — a row you can act on', 'The query and the page are both named.', seo_console('A useful row', <<<'HTML'
<p style="font-size:12px;color:#0f766e;font-weight:700;">QUERY AND PAGE</p>
<p>family shopping list</p>
<p>/shared-list</p>
<p>Shown, and some people clicked. The page is the one you meant.</p>
HTML)],
            'e2' => ['Example 2 — a trophy with no query', 'There is nothing to change.', seo_console('A trophy', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Visibility: high</h1>
<p>No query. No page. No idea what anyone typed.</p>
HTML)],
            'micro' => [
                'A short date range lies. Compare similar weeks.',
                'Brand queries and problem queries are different stories. Split them.',
                'If the offer never appears, look at coverage before you rewrite the adjective.',
            ],
            'quiz' => [
                $q('What is the useful report?', 'A query attached to a page', 'A single fame score', 'A pile of likes', 'A hidden keyword list'),
                $q('Which URL do you open first?', 'The offer', 'A thank-you page', 'A draft', 'A random tag'),
                $q('What does coverage help you see?', 'Whether the URL was kept, and why not', 'The best adjective', 'How to buy a vote', 'The font'),
            ],
            'practice' => 'Write the three queries you hope show the offer. When you next open the report, mark which of the three actually appear.',
        ]),
        $L([
            'section' => 'Measurement',
            'title' => 'Impressions, clicks, and position are different',
            'keywords' => 'impressions, clicks, average position',
            'lead' => 'An impression means the result was shown. A click means someone chose it. Position is a rough place in the list, averaged, and easy to misunderstand. Milk List does not treat them as one number.',
            'heading' => 'Shown, chosen, and roughly where',
            'para' => 'Many impressions and few clicks means the line is not winning the choice. Clicks that do not become starts mean the page failed the visit. A position of "8" can hide a mix of high and low moments. Read the query, not the average alone. Do not chase a one-day wiggle.',
            'bullets' => [
                'Impression: shown.',
                'Click: chosen.',
                'Position: a rough, averaged place. Use it gently.',
                'Decide from a query over weeks, not from one spike.',
            ],
            'visual' => tf_bars('seo39', 'Three different counts', [
                ['label' => 'Shown', 'value' => 80, 'color' => '#99f6e4', 'display' => 'shown'],
                ['label' => 'Clicks', 'value' => 28, 'color' => '#0f766e', 'display' => 'chosen'],
                ['label' => 'Starts', 'value' => 12, 'color' => '#134e4a', 'display' => 'acted'],
            ], 'Shown is not chosen. Chosen is not a start.'),
            'rows' => [
                ['Shown', 'Impression', 'The result appeared.'],
                ['Chosen', 'Click', 'They opened it.'],
                ['Acted', 'On the page', 'They started.'],
                ['Place', 'Rough', 'An average hides mixes.'],
                ['Window', 'Weeks', 'Not one afternoon.'],
                ['Wiggle', 'Ignore', 'One day is noise.'],
            ],
            'e1' => ['Example 1 — the three numbers stay separate', 'You can see where the drop is.', seo_console('Three numbers', <<<'HTML'
<p>Query: family shopping list</p>
<p>Shown often. Chosen sometimes. Starts are the number that matters on the page.</p>
HTML)],
            'e2' => ['Example 2 — one blended score', 'The drop is hidden.', seo_console('One score', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">SEO score 91</h1>
<p>You cannot tell whether anyone saw the result, clicked it, or started the list.</p>
HTML)],
            'micro' => [
                'A better position with a worse title can still lose clicks.',
                'Brand queries often click more easily. Do not mix them into the problem query and celebrate.',
                'If the tool estimates, treat the estimate as a direction.',
            ],
            'quiz' => [
                $q('What is an impression?', 'The result was shown', 'Someone started the list', 'Someone paid', 'The page was rewritten'),
                $q('What is a click?', 'Someone opened the result', 'The result existed', 'A start on the page', 'A sitemap entry'),
                $q('How should you treat average position?', 'Gently, over weeks, for one query', 'As a daily grade', 'As proof of revenue', 'As a reason to buy votes'),
            ],
            'practice' => 'For one query, write shown, chosen, and the on-page action as three separate lines. Circle the biggest drop.',
        ]),
        $L([
            'section' => 'Measurement',
            'title' => 'Impressions without clicks is a result-line problem',
            'keywords' => 'low ctr, title testing, snippet',
            'lead' => 'If Milk List is shown for "family shopping list" and almost nobody chooses it, the page may be fine and the blue line may be weak. Fix the line before you rebuild the site.',
            'heading' => 'They saw you and did not pick you',
            'para' => 'Read the title and description as a stranger next to the other results. Are you specific? Are you the same promise as the page? Are you less clear than the result above you? Change one of those lines, wait, and read the same query again. Do not change the price, the H1, and the path on the same day and then guess which one mattered.',
            'bullets' => [
                'Shown and rarely chosen: look at the result line.',
                'Compare as a reader with the results around you.',
                'Change one line.',
                'Wait through enough days to see a pattern.',
            ],
            'visual' => tf_split('seo40', 'Shown, not chosen?', 'Yes', 'Rewrite one line', 'Title or description', 'No, they do not click after opening', 'Look at the page', 'Do not rebuild the site for a weak title.'),
            'rows' => [
                ['Sign', 'Shown, few clicks', 'The line is weak.'],
                ['Read', 'As a stranger', 'Next to other results.'],
                ['Title', 'One change', 'Topic first.'],
                ['Description', 'Or the other', 'Not both today.'],
                ['Page', 'Leave it', 'Until you know.'],
                ['Wait', 'Weeks', 'Then reread the query.'],
            ],
            'e1' => ['Example 1 — a line a person can choose', 'Specific, and true.', seo_result('Shared household list that stays on screen | Milk List', 'A shared shopping list for a household. The list stays on the screen so milk is bought once. Rs 499 a year.')],
            'e2' => ['Example 2 — a line nobody can choose', 'It could be any company.', seo_result('Solutions', 'We provide innovative solutions for modern families. Learn more today.')],
            'micro' => [
                'A rewritten line that the page does not support will win a click and lose the person.',
                'If you are shown for the wrong query, the line is not the first fix. The page may be attracting the wrong intent.',
                'Small sites have noisy numbers. Do not declare victory on twenty impressions.',
            ],
            'quiz' => [
                $q('Shown often, clicked rarely. What do you inspect first?', 'The title and description', 'A new website', 'A bought vote', 'The font of the footer'),
                $q('How many lines do you change at once?', 'One', 'The title, the price, and the path', 'Every heading', 'None, ever'),
                $q('What must remain true?', 'The page keeps the promise the new line makes', 'The line can bait', 'The page can be empty', 'The query can be ignored'),
            ],
            'practice' => 'Pick one query with impressions and weak clicks. Rewrite only the title. Schedule a date to read that query again.',
        ]),
        $L([
            'section' => 'Measurement',
            'title' => 'Clicks without a next step is a page problem',
            'keywords' => 'landing page seo, engagement, conversion from search',
            'lead' => 'If people open the Milk List result and do not start, the result did its job and the page did not. The query they used tells you what they expected to see.',
            'heading' => 'They arrived. The page missed the job.',
            'para' => 'Match the first screen to that query. A price query that hides the price will bounce. A how-to query that opens a checkout will bounce. Look at the page on a phone. Then change one thing: the first sentence, the button, or the speed. Give it time. SEO did not fail just because the visit was wasted. The visit shows the page is the next repair.',
            'bullets' => [
                'Clicks without the action: inspect the page for that query.',
                'The first screen should match the intent.',
                'Check the phone and the wait.',
                'Change one element, then reread.',
            ],
            'visual' => tf_flow('seo41', 'After the click', [['query', 'what they wanted'], ['screen', 'what they saw'], ['step', 'what they did']], 'A wasted visit is a page repair, not a reason to buy rank.'),
            'rows' => [
                ['Sign', 'Clicks, no starts', 'The page missed.'],
                ['Query', 'The expectation', 'Price, or how, or compare.'],
                ['Screen', 'First view', 'Must match.'],
                ['Phone', 'The real device', 'Tap the step.'],
                ['One change', 'Sentence or button', 'Not five.'],
                ['Rank', 'Not the next buy', 'Fix the arrival.'],
            ],
            'e1' => ['Example 1 — the arrival matches a price query', 'The decision is possible.', seo_page('They can decide', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">One shared list</h1>
<p>Rs 499 a year. The list stays on the screen.</p>
<p><a href="#start" style="display:inline-block;background:#0f766e;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — the click opens a puzzle', 'The query was the price. The page is a story.', seo_page('A puzzle', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Our journey</h1>
<p>No price. No button. The person who clicked has to hunt, and most will not.</p>
HTML)],
            'micro' => [
                'A slow first screen can look like a message problem. Check the wait before you rewrite the promise.',
                'If the query was the wrong intent, tighten the title so the wrong people stop clicking.',
                'Count starts, not time-on-page. A confused person can linger.',
            ],
            'quiz' => [
                $q('People click and do not start. Where do you look?', 'The page, for that query', 'A vendor of votes', 'A new domain by default', 'Only the sitemap'),
                $q('What should a price query see first?', 'The price and the step', 'A founder story with neither', 'Five buttons', 'A film'),
                $q('How many changes do you make before you read again?', 'One', 'Every section', 'The path, the price, and the brand name', 'None, because clicks are enough'),
            ],
            'practice' => 'Take one query that earns clicks. Write what the person expected and what the first screen shows. Change the single mismatch.',
        ]),
        $L([
            'section' => 'The project',
            'title' => 'Project: one page you can explain in search',
            'keywords' => 'seo project, search page review',
            'lead' => 'The project is one page: the Milk List offer, or a page of your own with the same honesty. You should be able to explain the query, the result line, the first screen, how the URL is reached, and what you will count.',
            'heading' => 'A page you can defend out loud',
            'para' => 'Write the query and its intent. Write the title, the description, and the H1 so they are one promise. Put the answer and one step on the first screen of a phone. Make sure a link from home can reach it, that twins redirect or defer to it, and that it is the URL on your sitemap. Then write what "working" means: a query that shows the page, clicks that match the promise, and the action on the page. If any sentence is a trick, take it out.',
            'bullets' => [
                'One query, one intent, one URL.',
                'Title, description, and H1 agree.',
                'A person can reach it, and a fetch can too.',
                'You know which number would tell you the page failed.',
            ],
            'visual' => tf_flow('seo42', 'Defend the page', [['query', 'one intent'], ['line', 'true result'], ['page', 'one step']], 'If you cannot say it simply, it is not finished.'),
            'rows' => [
                ['Query', 'Their words', 'Family shopping list.'],
                ['Intent', 'The job', 'Decide.'],
                ['Line', 'Title and description', 'True and specific.'],
                ['Screen', 'Phone', 'Answer and step.'],
                ['Path', 'From home', 'One clear link.'],
                ['Proof', 'A number', 'Starts, not fame.'],
            ],
            'e1' => ['Example 1 — a page you can explain', 'Every piece names the same offer.', seo_page('The project', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#0f766e;font-weight:700;">FAMILY SHOPPING LIST</p>
<h1 style="font-size:26px;margin:8px 0;">One list the whole house can see</h1>
<p>Rs 499 a year. Add milk once. The shopper sees the same list.</p>
<p><a href="#start" style="display:inline-block;background:#0f766e;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — a page you cannot explain', 'The pieces belong to different products.', seo_page('A tangle', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Welcome</h1>
<p>The title says free recipes. The path is an id. Nothing links here. The button says Explore. You cannot say which query this is for.</p>
HTML)],
            'micro' => [
                'Do this for one page before you do it for fifty.',
                'A project that depends on a bought vote is not finished. It is compromised.',
                'Write the explanation as if Asha will read it. Cut any sentence you would not say to her.',
            ],
            'quiz' => [
                $q('What does the project need first?', 'One query and one URL', 'Fifty pages', 'A bought mention', 'A fame score'),
                $q('What must agree?', 'The title, the H1, and the first screen', 'Five different promises', 'A hidden page and a public page', 'The sitemap and a blocked offer'),
                $q('Which number says the page failed?', 'People arrive and do not take the step', 'A rival exists', 'The title is fifty characters', 'Home links to the offer'),
            ],
            'practice' => 'Write six lines: query, intent, title, H1, the link that reaches the page, and the action you will count. Read them aloud. Fix the line that does not belong.',
        ]),
    ];
}
