<?php
declare(strict_types=1);

function seo_chapters_a(): array
{
    $L = static function (array $c): array {
        return track_lesson('visual', '', $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };

    return [
        $L([
            'section' => 'The job',
            'title' => 'SEO is being the answer to a search',
            'keywords' => 'seo basics, search, answer page',
            'lead' => 'SEO is the work of being the page a person finds when they search for a real problem. Milk List wants to be the answer to "family shopping list", not a louder slogan. The page has to deserve the visit.',
            'heading' => 'A query, a page, a true answer',
            'para' => 'Someone types a few words because they want something. Search shows a short list of pages. Your job is to be one honest page on that list, and then to answer the question on the page itself. Ranking without an answer is a wasted visit. An answer that nobody can find is a private note.',
            'bullets' => [
                'Start from a query a real person would type.',
                'The page must answer that query in the first screen.',
                'The result line and the page must say the same thing.',
                'A trick that hides the answer does not survive a disappointed visitor.',
            ],
            'visual' => tf_flow('seo01', 'The job', [['query', 'their words'], ['page', 'your answer'], ['visit', 'one next step']], 'If the page does not answer the query, the rank does not matter.'),
            'rows' => [
                ['Query', 'What they type', 'Family shopping list.'],
                ['Page', 'What you own', 'One URL.'],
                ['Answer', 'The first screen', 'A shared list.'],
                ['Proof', 'Why believe it', 'The list stays.'],
                ['Step', 'What they do', 'Start the list.'],
                ['Measure', 'What you read', 'Queries and clicks.'],
            ],
            'e1' => ['Example 1 — the page answers the query', 'A person who typed the problem can see the product.', seo_page('Shared list', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#0f766e;font-weight:700;">FAMILY SHOPPING LIST</p>
<h1 style="font-size:28px;margin:8px 0;">One list the whole house can see</h1>
<p>Add milk once. The shopper sees the same list on their phone. Rs 499 a year.</p>
<p><a href="#start" style="display:inline-block;background:#0f766e;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — a page that ignores the query', 'The result may mention lists. The page talks about the company.', seo_page('About us', <<<'HTML'
<h1 style="font-size:28px;margin:0 0 8px;">We are innovators</h1>
<p>Our journey began with a dream. Meet the team. Read the manifesto.</p>
HTML)],
            'micro' => [
                'SEO is not a coat of keywords on a page that says something else.',
                'If you cannot name the query, you cannot judge the page.',
                'The person who clicks is the judge. The report comes second.',
            ],
            'quiz' => [
                $q('What is the job of an SEO page?', 'To answer a real search', 'To repeat a keyword in every line', 'To hide the offer', 'To collect every topic on one URL'),
                $q('Which query fits Milk List?', 'Family shopping list', 'Best life ever', 'Click here', 'Home'),
                $q('What must the page do after the click?', 'Answer the query on the first screen', 'Show a manifesto', 'Ask them to guess the product', 'Open five equal offers'),
            ],
            'practice' => 'Write the one query Milk List should answer, then the first sentence of the page. If the sentence could fit a different product, rewrite it.',
        ]),
        $L([
            'section' => 'The job',
            'title' => 'The page you own is what can be found',
            'keywords' => 'owned page, landing page, seo home',
            'lead' => 'Search can only send someone to an address that stays. A post on a rented feed can vanish. The Milk List page on your site is the thing you can keep improving.',
            'heading' => 'Send the query to a home you control',
            'para' => 'The result should open the page that answers the query, not a profile you do not control and not a different offer. If the useful explanation lives only inside an image on someone else\'s site, you do not have an SEO page yet. Put the answer on a URL you can edit next month.',
            'bullets' => [
                'One primary URL for the query.',
                'The words of the answer are text, not only a picture.',
                'Ads and posts may introduce the page. They do not replace it.',
                'If the URL changes, you plan the move. You do not abandon the old address.',
            ],
            'visual' => tf_panels('seo02', 'Where the answer lives', 'You can edit', ['Your URL', 'The title', 'The first screen'], 'You only rent', ['A feed post', 'An ad this week', 'A profile rule'], 'The query should land on the page you can still change.'),
            'rows' => [
                ['Home', 'The URL', 'milklist.example/shared-list'],
                ['Text', 'The answer', 'Readable without the image.'],
                ['Edit', 'Next month', 'You can change the line.'],
                ['Feed', 'A door', 'It points at the URL.'],
                ['Ad', 'A test', 'It lands on the same URL.'],
                ['Move', 'A redirect', 'The old address still works.'],
            ],
            'e1' => ['Example 1 — one address for the answer', 'The query, the result, and the page share a home.', seo_page('Shared list', <<<'HTML'
<p style="font-size:12px;color:#0f766e;font-weight:700;">milklist.example/shared-list</p>
<h1 style="font-size:26px;margin:8px 0;">A shared shopping list that stays on screen</h1>
<p>This is the page search should open. The promise is in the text.</p>
HTML)],
            'e2' => ['Example 2 — the answer has no address', 'A caption is not a page you can rank and improve.', seo_page('Only a caption', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">See the picture</h1>
<p>The whole explanation is inside one image on a rented profile. There is no page to send the query to.</p>
HTML)],
            'micro' => [
                'A homepage that tries to be every query is not a home for this one.',
                'If the only copy of the promise is in a picture, many people and some fetches miss it.',
                'Keep the URL stable while you improve the words.',
            ],
            'quiz' => [
                $q('What should a query open?', 'A page you own that answers it', 'A rented caption', 'A different offer', 'An empty profile'),
                $q('Why must the answer be text?', 'So the page still says it if the image fails', 'To hide the product', 'To repeat a keyword', 'To avoid a title'),
                $q('What is a feed post in this job?', 'A door to the page', 'A replacement for the page', 'The index', 'A status code'),
            ],
            'practice' => 'Name the one URL that should answer "family shopping list". Write the promise in a sentence that does not depend on an image.',
        ]),
        $L([
            'section' => 'The job',
            'title' => 'A person reads the page, a crawler only fetches it',
            'keywords' => 'reader first, crawler, seo writing',
            'lead' => 'A crawler fetches the page so it can be considered. A person decides if it helped. Milk List is written for Asha, who buys for a house of four. The fetch is a means. The reader is the point.',
            'heading' => 'Write the page a person can use',
            'para' => 'If the sentence is awkward for a person and tidy only for a machine, it is the wrong sentence. Use the words Asha would type, in the order she would say them. Then make sure those words are in the text the page actually sends, not buried where a person never sees them.',
            'bullets' => [
                'Read the page aloud. If it sounds like a list of repeats, rewrite it.',
                'The visible text and the result line should match.',
                'Do not show a person one page and send a fetch a different page.',
                'A crawler is not the customer.',
            ],
            'visual' => tf_split('seo03', 'Who is it for?', 'Reader first?', 'Asha can use it', 'Then make it reachable', 'Only a fetch', 'Rewrite the sentence', 'A page written for a crawler fails the person.'),
            'rows' => [
                ['Reader', 'Asha', 'Buys for the house.'],
                ['Words', 'Hers', 'Family shopping list.'],
                ['Order', 'Plain', 'Subject, then promise.'],
                ['Visible', 'On the page', 'Not a hidden block.'],
                ['Fetch', 'Later', 'Same page she sees.'],
                ['Test', 'Read aloud', 'If it stumbles, fix it.'],
            ],
            'e1' => ['Example 1 — a sentence a person can say', 'The product is obvious.', seo_page('Plain sentence', <<<'HTML'
<h1 style="font-size:26px;margin:0 0 8px;">A list the whole house can see</h1>
<p>Milk is added once. The person who shops sees it. That is the page.</p>
HTML)],
            'e2' => ['Example 2 — words arranged for a machine', 'A person will not finish this.', seo_page('Stacked words', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">List list shopping list family list app list</h1>
<p>Best list best list best family list shopping list near you list.</p>
HTML)],
            'micro' => [
                'Hidden text that a person does not see is a different page. Do not do that.',
                'Synonyms are fine when a person would use them. A pile is not a synonym.',
                'If you would be embarrassed to read the line to Asha, it is not ready.',
            ],
            'quiz' => [
                $q('Who is the page for?', 'The person who searched', 'The crawler alone', 'A keyword counter', 'A hidden block'),
                $q('What is wrong with repeating the query in every line?', 'A person will not read it', 'It is the same as a clear sentence', 'It creates a title', 'It indexes the image'),
                $q('What must match?', 'The page the person sees and the page that is fetched', 'Two different offers', 'A hidden paragraph and a blank screen', 'A caption and no URL'),
            ],
            'practice' => 'Read your draft to someone who has not seen the product. If they cannot say what it is, the sentence is for a machine. Rewrite it.',
        ]),
        $L([
            'section' => 'The job',
            'title' => 'Intent decides which page should exist',
            'keywords' => 'search intent, learn compare buy',
            'lead' => 'The same word can ask for different things. "Family shopping list" can mean "what is a shared list", "which app", or "start one". Milk List should not answer all three with one blurry page.',
            'heading' => 'Match the page to the job of the query',
            'para' => 'A learning query wants an explanation. A comparison wants a clear difference and a price. A buying query wants the offer and one next step. If you send a buying query to a long essay, or a learning query to a checkout, both people leave. Name the intent before you write.',
            'bullets' => [
                'Learn: explain the problem and the idea.',
                'Compare: show the difference, the price, and who it is for.',
                'Buy: the offer, the proof, and one button.',
                'Do not mix three intents into one URL and hope.',
            ],
            'visual' => tf_flow('seo04', 'Three intents', [['learn', 'what is it'], ['compare', 'is it for us'], ['buy', 'start the list']], 'One query job, one page job.'),
            'rows' => [
                ['Learn', 'Explain', 'What a shared list is.'],
                ['Compare', 'Choose', 'Paper, chat, or the app.'],
                ['Buy', 'Start', 'Rs 499 and the button.'],
                ['Mix', 'A blur', 'Essay plus checkout plus blog.'],
                ['Clue', 'The words', 'How, vs, price, start.'],
                ['Test', 'The first screen', 'Does it match the job?'],
            ],
            'e1' => ['Example 1 — a compare page stays a compare page', 'The person can choose.', seo_page('Compare', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">Paper, a chat, or one shared list</h1>
<p>Paper is lost. A chat splits into four threads. One list stays on the screen. Rs 499 a year.</p>
<p><a href="#start" style="color:#0f766e;font-weight:700;">See the offer</a></p>
HTML)],
            'e2' => ['Example 2 — three jobs on one URL', 'Nobody can tell what the page is for.', seo_page('Everything', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Learn, compare, buy, and our blog</h1>
<p>History. Pricing. A quiz. A checkout. A manifesto. Pick any door.</p>
HTML)],
            'micro' => [
                'A word like "best" is often a compare query. Answer the comparison.',
                'A word like "app" or "price" is closer to a decision. Do not hide the price.',
                'You may link from a learn page to the offer. You may not pretend they are the same page.',
            ],
            'quiz' => [
                $q('What decides the page?', 'The job of the query', 'The longest article you can write', 'How many times the word appears', 'The logo'),
                $q('Where does a buying query belong?', 'On the offer, with one next step', 'On a company history', 'On a page with five equal doors', 'On a hidden paragraph'),
                $q('What is a fair link between a learn page and the offer?', 'A next step after the explanation', 'Replacing the explanation with a checkout', 'A second copy of the same URL', 'No page at all'),
            ],
            'practice' => 'Take three queries you might want. Label each learn, compare, or buy. If two labels differ, they need two pages.',
        ]),
        $L([
            'section' => 'The job',
            'title' => 'One page owns one topic',
            'keywords' => 'one topic one page, keyword cannibalization',
            'lead' => 'If two Milk List URLs both try to be "the shared shopping list", search has to guess, and the person has to guess. Pick one owner for the topic. Other pages may mention it and link to the owner.',
            'heading' => 'Give the topic a single home',
            'para' => 'A supporting page can answer a narrower question: how a household adds an item, or how the price works. It should link to the main page, and the main page can link back when that narrower question is the next useful step. Two pages with the same promise split the clicks and confuse the result.',
            'bullets' => [
                'Name the owner URL for the main query.',
                'A second page needs a second, narrower query.',
                'Link up to the owner. Do not clone it.',
                'If two titles could be swapped, you have one topic and two URLs.',
            ],
            'visual' => tf_split('seo05', 'Two URLs?', 'Same promise?', 'Keep one owner', 'Link the other away', 'A narrower query', 'Give it a page', 'Swappable titles mean one page too many.'),
            'rows' => [
                ['Owner', 'The main query', '/shared-list'],
                ['Narrow', 'A smaller question', '/add-an-item'],
                ['Link', 'Up and sideways', 'When it helps.'],
                ['Clone', 'Same promise', 'Merge or redirect.'],
                ['Title', 'Swap test', 'If both still fit, stop.'],
                ['Home', 'Not every topic', 'The offer has its URL.'],
            ],
            'e1' => ['Example 1 — a narrow page points home', 'It answers one smaller question and sends the reader to the owner.', seo_linkmap('Add an item', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Add an item the night before</h1>
<p>Open the shared list and type milk. The shopper sees it in the morning.</p>
<p><a href="#owner" style="color:#0f766e;font-weight:700;">The shared list itself</a></p>
HTML)],
            'e2' => ['Example 2 — two owners', 'Both URLs make the same claim.', seo_page('A twin', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">The best shared shopping list</h1>
<p>This second URL repeats the offer page word for word and asks to rank for the same query.</p>
HTML)],
            'micro' => [
                'A tag or a date does not make a new topic.',
                'Merging is kinder than leaving a twin to compete forever.',
                'The homepage can introduce the topic. It should not be the only URL and also a clone of it.',
            ],
            'quiz' => [
                $q('When do two pages compete?', 'When they make the same promise', 'When one links to the other', 'When one answers a narrower question', 'When they have different titles that could not be swapped'),
                $q('What should a narrower page do?', 'Answer its question and link to the owner', 'Repeat the owner word for word', 'Hide from the reader', 'Use the same title'),
                $q('What is the swap test?', 'If the titles still fit each other, you have one topic', 'Swap the logo', 'Swap the price every day', 'Swap the host name'),
            ],
            'practice' => 'List your URLs that mention the shared list. Mark the owner. For each other URL, write the narrower query or plan to merge it.',
        ]),
        $L([
            'section' => 'The job',
            'title' => 'Tricks fade and a true page can stay',
            'keywords' => 'white hat seo, honest seo, spam',
            'lead' => 'A result that does not match the page trains people to skip you. Milk List keeps the promise on the page the same as the promise in the result. Shortcuts that show one thing and deliver another are how a site loses trust.',
            'heading' => 'Do not build a page you would not sign',
            'para' => 'You do not hide different words for a fetch than the person sees. You do not invent reviews, buy votes, or stand up empty pages that exist only to catch a query. You do not copy someone else\'s page and swap the name. The durable work is a page that helps, a title that tells the truth, and a site that is easy to move through.',
            'bullets' => [
                'The person and the fetch see the same page.',
                'No bought votes, no fake reviews, no empty doorway pages.',
                'No page that exists only to repeat a query.',
                'If a tactic needs to be hidden, it is not a tactic for this course.',
            ],
            'visual' => tf_panels('seo06', 'What lasts', 'Keep', ['A true title', 'A useful page', 'A clear link'], 'Drop', ['A hidden version', 'A bought vote', 'An empty door'], 'A result that lies spends the trust you needed.'),
            'rows' => [
                ['Same page', 'Person and fetch', 'One version.'],
                ['Title', 'True', 'Matches the H1.'],
                ['Reviews', 'Earned', 'Not arranged.'],
                ['Votes', 'Earned', 'Not bought.'],
                ['Empty page', 'No', 'A query needs an answer.'],
                ['Copy', 'No', 'Write your own page.'],
            ],
            'e1' => ['Example 1 — the result and the page agree', 'What was promised is what opens.', seo_result('Shared household list that stays on screen | Milk List', 'A shared shopping list for a household. The list stays on the screen so milk is bought once. Rs 499 a year, and you can leave when you want.')],
            'e2' => ['Example 2 — the result is a bait', 'The click was for a list. The page is a trick.', seo_page('A bait', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Download now</h1>
<p>The result said shared shopping list. This page is an unrelated file with no list and no price.</p>
HTML)],
            'micro' => [
                'A short-lived spike from a misleading title is not a strategy.',
                'You do not need the private recipe for a scheme in order to refuse it.',
                'When you are unsure, ask whether Asha would feel misled after the click.',
            ],
            'quiz' => [
                $q('Which page can stay?', 'One a person would thank you for', 'One that shows the fetch different words', 'One that exists only to repeat a query', 'One that copies another site and swaps the name'),
                $q('What is a bait result?', 'A line that opens a different promise', 'A title that matches the heading', 'A narrow page that links home', 'A slow but true page'),
                $q('What do you do with a tactic that has to stay hidden?', 'You do not use it', 'You hide it better', 'You put it in an image', 'You buy a vote to cover it'),
            ],
            'practice' => 'Read your title and then the first screen. Write one sentence on how they agree. If you cannot, change one of them before you publish.',
        ]),
        $L([
            'section' => 'How search works',
            'title' => 'Crawl, index, and rank are three jobs',
            'keywords' => 'crawl, index, rank, how search works',
            'lead' => 'Search does three different things. It fetches pages. It decides which fetches are worth keeping. It orders the kept pages for a query. Milk List can fail at any one of the three and never appear.',
            'heading' => 'Fetched, kept, then ordered',
            'para' => 'A page that cannot be fetched cannot be kept. A page that is fetched and judged thin or duplicate may not be kept. A page that is kept still has to be a better answer than the other kept pages. Improving the title does nothing for a URL that was never fetched. Asking for a higher place does nothing for a page that was not kept.',
            'bullets' => [
                'Crawl: can the URL be opened from a link you mean?',
                'Index: is this page worth storing as an answer?',
                'Rank: among stored answers, where does this one sit for this query?',
                'Fix the earliest failure first.',
            ],
            'visual' => tf_flow('seo07', 'Three jobs', [['fetch', 'can it open'], ['keep', 'is it worth it'], ['order', 'for this query']], 'Do not tune the order of a page that was never kept.'),
            'rows' => [
                ['Crawl', 'Fetch', 'A link that opens.'],
                ['Index', 'Keep', 'A page worth storing.'],
                ['Rank', 'Order', 'For one query.'],
                ['Blocked', 'No fetch', 'Nothing later happens.'],
                ['Thin', 'Fetched, not kept', 'Rewrite or remove.'],
                ['Kept', 'Still competing', 'Be the better answer.'],
            ],
            'e1' => ['Example 1 — a page that can pass all three', 'It is linked, it answers, and the result can show it.', seo_coverage('Ready', <<<'HTML'
<p style="font-size:12px;color:#0f766e;font-weight:700;">COVERAGE</p>
<h1 style="font-size:22px;margin:8px 0;">shared-list</h1>
<p>Fetched from the home page. Kept, because it answers one query. Shown when someone types that query.</p>
HTML)],
            'e2' => ['Example 2 — polishing a page that was never fetched', 'The title is fine. The URL has no path in.', seo_coverage('Unreachable', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">No path to this URL</h1>
<p>Nothing links here. The title was rewritten twice. The fetch still has not happened.</p>
HTML)],
            'micro' => [
                'A sitemap can offer a URL. It does not replace a link a person can follow.',
                'Being fetched is not the same as being shown.',
                'Write down which of the three jobs you are trying to fix today.',
            ],
            'quiz' => [
                $q('What is the right order of the jobs?', 'Fetch, then keep, then order', 'Order, then invent a page', 'Keep a page that cannot be opened', 'Buy the order first'),
                $q('What does a perfect title fail to do?', 'Fetch a URL that nothing links to', 'Describe the page', 'Match the heading', 'Invite a click'),
                $q('What is rank?', 'The order among pages that were kept', 'The act of fetching', 'A sitemap', 'A status code'),
            ],
            'practice' => 'For the shared-list URL, write three lines: how it is fetched, why it is worth keeping, and which query it should be ordered for.',
        ]),
        $L([
            'section' => 'How search works',
            'title' => 'A crawler follows links it can open',
            'keywords' => 'crawl, internal links, reachable url',
            'lead' => 'A fetch starts from pages that are already known and follows links. If the Milk List offer is not linked from a page that can be fetched, it is a room with no door.',
            'heading' => 'Give the important URL a real door',
            'para' => 'The door should be a normal link a person can also use, with words that say what is behind it. A button that only exists inside a scripted gesture, a link that is blocked, or a chain of redirects that never ends is not a door. Put the offer in the main navigation or in the body of a page that is already easy to reach.',
            'bullets' => [
                'Important pages are a click or two from the home page.',
                'The link text says the destination, not "click here" alone.',
                'A blocked URL and a URL with no links are both invisible.',
                'Do not make the only path a loop of redirects.',
            ],
            'visual' => tf_flow('seo08', 'A door', [['home', 'already known'], ['link', 'plain words'], ['offer', 'the URL']], 'If a person cannot walk the path, do not expect a fetch to invent one.'),
            'rows' => [
                ['Start', 'A known page', 'The home page.'],
                ['Link', 'In the page', 'Shared list.'],
                ['Depth', 'One or two clicks', 'Not a maze.'],
                ['Words', 'The destination', 'Not only "here".'],
                ['Block', 'No fetch', 'Do not block the offer.'],
                ['Loop', 'No end', 'Fix the redirect.'],
            ],
            'e1' => ['Example 1 — the offer is one click away', 'Home names the page.', seo_linkmap('From home', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Home</h1>
<p><a href="#list" style="color:#0f766e;font-weight:700;">Shared shopping list</a></p>
<p>The words say what opens. A person and a fetch can both use it.</p>
HTML)],
            'e2' => ['Example 2 — the offer is an island', 'Nothing points at it.', seo_coverage('Island', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">shared-list</h1>
<p>No navigation item. No sentence links here. The page exists only if you already know the URL.</p>
HTML)],
            'micro' => [
                'A link in a footer to every URL is not a substitute for a meaningful link in the text.',
                'Pages that require a login before any content cannot be the public answer.',
                'Check the path in a browser with nothing extra installed. If you get lost, the path is wrong.',
            ],
            'quiz' => [
                $q('How does a new URL get fetched?', 'A link from a page that can already be fetched', 'By wishing', 'By repeating a keyword', 'By hiding the URL'),
                $q('What should the link text do?', 'Say what the destination is', 'Say only "click here" every time', 'Hide the destination', 'Point at a redirect loop'),
                $q('Which offer is hard to fetch?', 'One that no public page links to', 'One linked from the home page', 'One with a clear name', 'One a person can open'),
            ],
            'practice' => 'From the home page, count the clicks to the offer. If it takes more than two, or the link text is vague, add one clear link.',
        ]),
        $L([
            'section' => 'How search works',
            'title' => 'The index keeps a page worth keeping',
            'keywords' => 'indexing, coverage, thin content',
            'lead' => 'Not every fetched URL is stored as a result. The index is a library. Milk List should ask to keep the pages that answer something, and should not ask to keep tag pages, empty filters, and drafts.',
            'heading' => 'Worth storing, or not',
            'para' => 'A page is worth keeping when a person could land on it from a query and be glad. A page that is a duplicate, a thin slice, or an internal tool should usually stay out. Being kept is not a prize you demand. It is a judgment that this URL is a reasonable answer.',
            'bullets' => [
                'Useful, unique, and reachable pages are the ones to offer.',
                'Drafts, thank-you pages, and empty filters usually should not be results.',
                'A reason for exclusion is useful. Read it before you rewrite the title.',
                'Removing a weak URL is sometimes the right improvement.',
            ],
            'visual' => tf_split('seo09', 'Keep it?', 'Would a stranger be glad?', 'Yes', 'Offer it as a result', 'No', 'Keep it out', 'The index is a library, not a trophy case.'),
            'rows' => [
                ['Keep', 'An answer', 'The offer page.'],
                ['Keep', 'A narrower help', 'How to add an item.'],
                ['Skip', 'A draft', 'Not ready.'],
                ['Skip', 'A filter with no items', 'Nothing to read.'],
                ['Skip', 'A twin', 'One owner is enough.'],
                ['Read', 'The reason', 'Before you argue.'],
            ],
            'e1' => ['Example 1 — a page that earns a place', 'A stranger could use it.', seo_coverage('Kept', <<<'HTML'
<p style="font-size:12px;color:#0f766e;font-weight:700;">INDEXED</p>
<h1 style="font-size:22px;margin:8px 0;">shared-list</h1>
<p>Unique answer. Linked from home. A person who searched can act.</p>
HTML)],
            'e2' => ['Example 2 — a URL that should not be a result', 'It is a private step, not an answer.', seo_coverage('Left out', <<<'HTML'
<p style="font-size:12px;color:#b45309;font-weight:700;">EXCLUDED ON PURPOSE</p>
<h1 style="font-size:22px;margin:8px 0;">thank-you</h1>
<p>This page only confirms a start. It is not the answer to a query.</p>
HTML)],
            'micro' => [
                'More indexed URLs is not a goal. More useful URLs is.',
                'A page excluded as a duplicate is a clue to pick one owner.',
                'Do not keep adding near-empty pages to "grow the site".',
            ],
            'quiz' => [
                $q('What belongs in the index?', 'A page a stranger would be glad to open', 'Every URL the site has ever made', 'A draft', 'An empty filter'),
                $q('What is a fair reason to stay out?', 'The page is only a private confirmation', 'The page answers the main query', 'The page is linked from home', 'The page has a true title'),
                $q('What should you do with a thin twin?', 'Merge it or stop offering it', 'Write a second title and hope', 'Add more repeated words', 'Buy a vote for it'),
            ],
            'practice' => 'List ten URLs. Mark keep or leave out. For each leave-out, write the reason in one line.',
        ]),
        $L([
            'section' => 'How search works',
            'title' => 'Rank is a choice among pages that qualify',
            'keywords' => 'ranking, relevance, search results',
            'lead' => 'Once a page is kept, it competes with other kept pages for a query. Milk List does not get a place because it asked. It gets a place when it is a strong, trustworthy answer for that query.',
            'heading' => 'Be the better answer, not the louder one',
            'para' => 'Relevance is whether the page is about the query. Usefulness is whether the person can finish the job. Trust is whether the page is believable: a real product, a clear price, a way to leave, no bait. You do not need the private formula. You need the behavior: make this page a clearer answer than the alternatives you can honestly imagine.',
            'bullets' => [
                'About the query, useful on the first screen, and believable.',
                'A louder adjective does not beat a clearer page.',
                'Compare your page with the current results as a reader, not as a rival to copy.',
                'You still cannot force the order.',
            ],
            'visual' => tf_flow('seo10', 'Why this page', [['about', 'the query'], ['useful', 'first screen'], ['believable', 'price and proof']], 'The order is earned among pages that qualify.'),
            'rows' => [
                ['About', 'The topic', 'A shared list.'],
                ['Useful', 'The job finishes', 'They know what to do.'],
                ['Believe', 'Specifics', 'Rs 499, one list.'],
                ['Louder', 'Not a plan', 'Best, ultimate, number one.'],
                ['Copy', 'Not a plan', 'Write your own answer.'],
                ['Order', 'Not owed', 'Improve the page.'],
            ],
            'e1' => ['Example 1 — a page a result could prefer', 'It is specific and usable.', seo_page('Clear answer', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">One shared list for the household</h1>
<p>Compared with four chat threads, one list stays. Rs 499 a year. Start when you are ready.</p>
HTML)],
            'e2' => ['Example 2 — volume instead of an answer', 'Nothing here helps a choice.', seo_page('Volume', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Best ultimate number one list</h1>
<p>Best best best. The page never says what the product does.</p>
HTML)],
            'micro' => [
                'Look at the results to learn the intent. Do not lift their sentences.',
                'A page that loads into a mismatch will not become a better answer by repetition.',
                'Trust is practical: a price, a named product, a way out.',
            ],
            'quiz' => [
                $q('What is rank?', 'An order among qualifying pages', 'A fee you pay for a permanent seat', 'The crawl', 'The sitemap'),
                $q('Which page is a stronger answer?', 'One that explains the list, the price, and the step', 'One that says best ten times', 'One that copies another result', 'One that hides the product'),
                $q('What do you do with the private formula?', 'You do not need it. You improve the answer.', 'You recreate it', 'You buy it', 'You ignore the reader'),
            ],
            'practice' => 'Open the current results for your query as a reader. Note what a good answer does. Write one improvement to your page that is yours, not a copy.',
        ]),
        $L([
            'section' => 'How search works',
            'title' => 'You do not buy a lasting place',
            'keywords' => 'paid search vs seo, organic results',
            'lead' => 'An ad is a rented visit. A search result that you did not pay for is a page that earned a place. Milk List may use both. They are labeled differently, they stop differently, and they must still open the same true page.',
            'heading' => 'Rent is not the same as being kept',
            'para' => 'Paying for a click can test the page today. It does not put the page into the unpaid results, and it does not keep those results when the bill stops. Buying links or fake engagement to pretend you earned a place is a scheme, not SEO. If you run an ad, mark it as an ad and send it to the page the result would also deserve.',
            'bullets' => [
                'Unpaid results are earned by the page.',
                'Ads are labeled and rented.',
                'The landing page should be the same promise either way.',
                'Do not buy votes, reviews, or a fake crowd.',
            ],
            'visual' => tf_panels('seo11', 'Two lines in search', 'Earned result', ['No bill today', 'The page qualifies', 'It can fade if you neglect it'], 'Rented ad', ['You pay', 'It is labeled', 'It stops with the bill'], 'Money does not convert one into the other.'),
            'rows' => [
                ['Earned', 'The page', 'No daily bill.'],
                ['Rented', 'The ad', 'Stops when you stop.'],
                ['Label', 'An ad is an ad', 'Do not disguise it.'],
                ['Landing', 'Same promise', 'The offer URL.'],
                ['Votes', 'Not for sale', 'Earn a mention.'],
                ['Test', 'An ad can teach', 'Then fix the page.'],
            ],
            'e1' => ['Example 1 — the ad and the result tell the same truth', 'One is paid. Both are honest.', seo_result('Shared household list that stays on screen | Milk List', 'A shared shopping list for a household. The list stays on the screen so milk is bought once. Rs 499 a year.')],
            'e2' => ['Example 2 — paying to fake a reputation', 'The bill is dressed up as a vote.', seo_page('A bought vote', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Fifty sites will mention us</h1>
<p>We paid for the mentions. None of them have readers. The offer page is still thin.</p>
HTML)],
            'micro' => [
                'A useful ad is a distribution test. It is not a rank.',
                'If the unpaid result and the ad disagree, fix the disagreement before you spend more.',
                'You cannot purchase someone else\'s trust in bulk.',
            ],
            'quiz' => [
                $q('What stops when the ad bill stops?', 'The rented visits', 'A page that genuinely answers the query', 'The price on the page', 'The title tag'),
                $q('What is not a way to earn a place?', 'Buying a pile of mentions', 'Improving the answer', 'Earning a real reference', 'Making the page easier to reach'),
                $q('Where should the ad open?', 'The same true offer the result would open', 'A different, louder promise', 'A login wall', 'An empty page'),
            ],
            'practice' => 'Write two lines: what the unpaid result should say, and what an ad should say. If they diverge, make them the same promise before any spend.',
        ]),
        $L([
            'section' => 'The query',
            'title' => 'Start from the words a person types',
            'keywords' => 'keyword research, search queries',
            'lead' => 'A keyword is not a trophy. It is a guess about the words Asha types. Milk List starts with phrases a household buyer would actually use, then checks that a page can answer them.',
            'heading' => 'Collect phrases, then pick a page',
            'para' => 'Listen to the words customers already use: milk bought twice, shared list, family grocery list. Group phrases that share an intent. Pick one group for the offer page and leave the rest for narrower pages or for later. A giant list with no page behind it is a spreadsheet, not SEO.',
            'bullets' => [
                'Use the customer\'s nouns, not your internal name.',
                'Group phrases by intent before you assign URLs.',
                'One primary phrase per page, plus a few natural variants.',
                'If you would not say the phrase aloud, do not build a page for it.',
            ],
            'visual' => tf_flow('seo12', 'From words to a page', [['listen', 'her nouns'], ['group', 'one intent'], ['assign', 'one URL']], 'A list of phrases without a page is not a plan.'),
            'rows' => [
                ['Source', 'Real speech', 'Did you get milk?'],
                ['Phrase', 'Typed', 'Family shopping list.'],
                ['Group', 'Same intent', 'One page.'],
                ['Variant', 'Natural', 'Shared grocery list.'],
                ['Reject', 'Not hers', 'Synergy platform.'],
                ['Output', 'A URL', 'Not a longer sheet.'],
            ],
            'e1' => ['Example 1 — phrases that share a page', 'One intent, one home.', seo_console('Phrases', <<<'HTML'
<h1 style="font-size:20px;margin:0 0 8px;">One group</h1>
<p>family shopping list</p>
<p>shared grocery list</p>
<p>household list app</p>
<p style="color:#0f766e;font-weight:700;">Owner: /shared-list</p>
HTML)],
            'e2' => ['Example 2 — a sheet with no pages', 'Fifty lines, zero answers.', seo_console('A sheet', <<<'HTML'
<h1 style="font-size:20px;margin:0 0 8px;">50 phrases, no URL</h1>
<p>Each line is a different job. Nothing on the site answers any of them yet.</p>
HTML)],
            'micro' => [
                'A tool can suggest phrases. You still decide if a person means them.',
                'Your brand name is a query only after people know the brand.',
                'Write the phrase into a sentence. If the sentence is nonsense, drop the phrase.',
            ],
            'quiz' => [
                $q('What is a keyword here?', 'A phrase a person might type', 'A word you must repeat fifty times', 'Your internal project name', 'A bought list'),
                $q('When do phrases share a page?', 'When the intent is the same', 'When they start with the same letter', 'When you have fifty of them', 'When a tool listed them together by accident'),
                $q('What is the output of the work?', 'A URL that answers the group', 'A longer spreadsheet', 'A hidden paragraph', 'A second copy of the homepage'),
            ],
            'practice' => 'Write ten phrases a household buyer might type. Group them. Assign one group to /shared-list and name one group you will not build yet.',
        ]),
        $L([
            'section' => 'The query',
            'title' => 'A broad query and a specific query are different pages',
            'keywords' => 'head term, long tail query, specific query',
            'lead' => '"Shopping list" is a wide door. "Shared shopping list for a family of four" is a narrow one. Milk List can answer the narrow door more honestly than it can answer every list in the world.',
            'heading' => 'Win the query you can truly serve',
            'para' => 'A broad query has many intents and strong pages already. A specific query tells you the person and the job. Start where the promise is exact. You may later earn a broader query because the specific pages made you credible. You do not start by writing one vague page aimed at the widest word.',
            'bullets' => [
                'Specific queries reveal the person and the job.',
                'Broad queries often need a stronger site, not a vaguer page.',
                'Match the depth of the page to the breadth of the query.',
                'A narrow page can link to the offer. A broad page should not pretend to be every narrow page.',
            ],
            'visual' => tf_panels('seo13', 'How wide is the query?', 'Narrow', ['Family of four', 'One shared list', 'A page can finish'], 'Wide', ['Shopping list', 'Many intents', 'Do not fake them all'], 'Start where the promise is exact.'),
            'rows' => [
                ['Narrow', 'A clear person', 'Family shared list.'],
                ['Wide', 'Many jobs', 'Shopping list.'],
                ['Page', 'Match the width', 'Do not blur it.'],
                ['Start', 'The exact promise', 'The offer URL.'],
                ['Later', 'Broader words', 'After you deserve them.'],
                ['Risk', 'A vague page', 'Ranks for nothing useful.'],
            ],
            'e1' => ['Example 1 — a page as specific as the query', 'The person is visible.', seo_page('Narrow', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">A shared list for a house of four</h1>
<p>One person adds milk. The shopper sees one list, not four messages.</p>
HTML)],
            'e2' => ['Example 2 — a wide word and an empty claim', 'It tries to be every list.', seo_page('Wide and empty', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Shopping list</h1>
<p>We do lists. All lists. Every list. The page never chooses a person.</p>
HTML)],
            'micro' => [
                'A specific query with a real page is worth more than a broad query with a slogan.',
                'If the broad query\'s results are dictionaries and giant guides, your offer page is the wrong shape.',
                'You can target a broad query with a real guide later, and link to the offer.',
            ],
            'quiz' => [
                $q('Which query should Milk List answer first?', 'Shared list for a household', 'The word list, with no person', 'Every shopping query at once', 'A query you would not say aloud'),
                $q('Why is a broad query harder?', 'It mixes many intents and strong pages', 'It has fewer words', 'It cannot be typed', 'It does not need a page'),
                $q('What is the wrong move?', 'One vague page aimed at the widest word', 'A specific page with a specific promise', 'A guide that links to the offer', 'Waiting to earn a broader word'),
            ],
            'practice' => 'Write one broad query and one specific query. Describe the page shape each needs. Build only the shape you can finish honestly.',
        ]),
        $L([
            'section' => 'The query',
            'title' => 'Learn, compare, and buy are three intents',
            'keywords' => 'informational query, commercial query, transactional query',
            'lead' => 'Intent is the job hiding inside the words. "What is a shared shopping list" wants to learn. "Shared list app vs paper" wants to compare. "Milk List price" wants to decide. Each job has a page.',
            'heading' => 'Name the job before you write the title',
            'para' => 'Clues sit in the words: how, what, versus, review, price, buy, near me. Milk List\'s offer page is the decide page. A short guide can be the learn page. A comparison can be its own URL. Sending all three queries to the decide page makes the learn query bounce and makes the decide page carry an essay it does not need.',
            'bullets' => [
                'Learn pages explain and then offer a next step.',
                'Compare pages make the difference obvious, including the price.',
                'Decide pages ask for one action.',
                'The title should reveal which job the page accepts.',
            ],
            'visual' => tf_flow('seo14', 'Name the job', [['words', 'how or price'], ['job', 'learn or decide'], ['page', 'the matching URL']], 'The title should admit which job this page accepts.'),
            'rows' => [
                ['How', 'Learn', 'A short guide.'],
                ['Versus', 'Compare', 'Paper, chat, app.'],
                ['Price', 'Decide', 'The offer.'],
                ['Title', 'Admits the job', 'Not a vague brand line.'],
                ['Bounce', 'Wrong job', 'Essay for a buyer.'],
                ['Link', 'Between jobs', 'After the answer.'],
            ],
            'e1' => ['Example 1 — the title admits the job', 'A person can tell this is the offer.', seo_result('Shared household list that stays on screen | Milk List', 'One shared shopping list for a household, Rs 499 a year. The list stays on the screen so milk is bought once.')],
            'e2' => ['Example 2 — a decide query opens a diary', 'They wanted the price. They got a story.', seo_page('A diary', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Our founder story</h1>
<p>The query was the price. The page never says Rs 499 or what the list does.</p>
HTML)],
            'micro' => [
                '"Near me" is a local job. If you are not local, do not pretend.',
                'A review query wants independent experience. Your own page can state facts. It cannot impersonate a reviewer.',
                'Change the page or change the query. Do not keep the mismatch.',
            ],
            'quiz' => [
                $q('What does a price query want?', 'The offer and a way to decide', 'A founder diary', 'A definition with no product', 'Five unrelated doors'),
                $q('What does a how-to query want?', 'An explanation, then a next step', 'An immediate checkout with no teaching', 'A copied review', 'A hidden keyword'),
                $q('What should the title do?', 'Show which job the page accepts', 'Stay vague so every query might click', 'Shout best', 'Hide the product'),
            ],
            'practice' => 'Label five real queries as learn, compare, or decide. Write the title only for the decide page, and make sure the price or the action is honest in the line.',
        ]),
    ];
}
