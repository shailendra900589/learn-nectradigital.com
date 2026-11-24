<?php
declare(strict_types=1);

function marketing_chapters_d(): array
{
    $L = static function (array $c): array {
        return track_lesson('visual', '', $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };

    return [
        $L([
            'section' => 'Paid attention',
            'title' => 'Audience, creative, and the landing page are one set',
            'keywords' => 'ad audience, creative, landing page',
            'lead' => 'A paid campaign is three pieces that have to agree: who may see it, what the ad says, and which page opens. Change one and you have a different campaign. For Milk List the who is a household buyer, the creative is "one list that stays", and the page is the offer. Showing a recipe line to bargain hunters and sending them to a login wall is three pieces from three different ideas.',
            'heading' => 'Who, what they see, where they land',
            'para' => 'A search ad meets someone who typed a problem. A feed ad interrupts someone and must explain faster. Both still need the same three pieces. Exclude people who should not see this offer when you can do it fairly: someone who already pays for Milk List does not need the "start" ad. Do not build the audience by uploading a file of people who never asked, and do not aim at sensitive moments you cannot explain out loud. If you cannot describe the person in one sentence, the audience is "everyone", and the sentence will go vague.',
            'bullets' => [
                'Write the person, the line, and the URL in one row before you pay.',
                'Search meets a typed problem. A feed must carry more of the explanation.',
                'Exclude current customers from a new-customer offer when the tool lets you do it honestly.',
                'No purchased audiences, no sensitive targeting you would not say to Asha.',
            ],
            'visual' => tf_flow('mkaud2', 'One set', [['who', 'household'], ['line', 'list stays'], ['page', 'the offer']], 'If one box changes, review the other two before you spend.'),
            'rows' => [
                ['Who', 'One sentence', 'Buys for the house.'],
                ['Line', 'The creative', 'A list that stays.'],
                ['Page', 'The landing', 'The offer URL.'],
                ['Search', 'They typed it', 'Closer to the problem.'],
                ['Feed', 'You interrupted', 'Explain faster.'],
                ['Exclude', 'Already paying', 'Not the start ad.'],
            ],
            'e1' => ['Example 1 — the three pieces on one card', 'A person could approve this spend because the set agrees.', dm_ads('One set', [
                ['Who may see it', 'Buys groceries for a shared house'],
                ['Leave out', 'People who already pay'],
                ['Shows on', 'The feed, after work'],
                ['Daily limit', 'Rs 80'],
                ['The line', 'One shared list that stays put'],
                ['Landing page', 'milklist.example/shared-list'],
            ], '<p style="margin:0 0 6px;font-weight:800;">One shared list that stays put</p><p style="margin:0 0 8px;font-size:14px;">Rs 499 a year. Milk is bought once.</p><span style="display:inline-block;background:#059669;color:#fff;border-radius:8px;padding:8px 12px;font-size:13px;font-weight:700;">Start the list</span>', 'feed')],
            'e2' => ['Example 2 — three ideas glued together', 'The person, the line, and the page are strangers.', dm_ads('Three strangers', [
                ['Who may see it', 'Everyone, plus a bought file'],
                ['Leave out', 'Nobody'],
                ['Shows on', 'The feed'],
                ['Daily limit', 'No stop written down'],
                ['The line', 'Free recipes'],
                ['Landing page', 'A login wall'],
            ], '<p style="margin:0 0 6px;font-weight:800;">Free recipes</p><p style="margin:0;font-size:14px;color:#b91c1c;">The click opens a login wall. The list is never explained.</p>', 'note')],
            'micro' => [
                'A narrow audience cannot rescue a line that hides the product.',
                'A perfect page cannot rescue an ad shown to people who will never share a kitchen.',
                'Write the exclusion as carefully as the inclusion.',
            ],
            'quiz' => [
                $q('What three pieces must agree?', 'Who sees it, what the ad says, and which page opens', 'Logo, font, and filter', 'Budget, hashtag, and slogan', 'Clicks, likes, and shares'),
                $q('How is a search ad different from a feed ad?', 'Search meets a typed problem; a feed must explain faster', 'Search needs no page', 'A feed can promise a different product', 'They are the same room'),
                $q('Who should you keep out of a start offer?', 'People who already pay, when you can do that honestly', 'Every household buyer', 'People who typed the problem', 'Nobody, ever'),
            ],
            'practice' => 'Fill one card: who, the line, the URL, and who is excluded. If any box is "everyone" or "a file we bought", stop.',
        ]),
        $L([
            'section' => 'Paid attention',
            'title' => 'The auction spends money for attention',
            'keywords' => 'ad auction, bid, relevance',
            'lead' => 'Most ads are sold in an auction. You offer to pay for a result, other advertisers do the same, and the platform weighs what you will pay against how likely a person is to find the ad useful. Milk List does not win by shouting a higher number alone. A clear, relevant ad that lands on a page people stay on can cost less than a vague ad with a bigger bid. You still need a limit.',
            'heading' => 'A bid is not the whole price',
            'para' => 'You set a ceiling, not a wish to be first at any cost. The platform estimates whether this person, this line, and this page belong together. A page that loads into a mismatch gets expensive because people bounce, and because you paid for a visit that could not convert. You do not need the internal formula. You need the behavior: write a true line, send it to a matching page, and refuse to raise the bid to hide a weak page. There is no setting that lets you follow someone into a private moment. Do not look for one.',
            'bullets' => [
                'You compete for attention. You do not buy a guaranteed seat.',
                'Relevance is practical: the right person, a clear line, a page that matches.',
                'Raising the bid to rescue a weak page spends more to learn the same lesson.',
                'Set the ceiling in money you can lose while you learn.',
            ],
            'visual' => tf_flow('mkbid', 'What the spend meets', [['ceiling', 'your limit'], ['line', 'is it clear'], ['page', 'does it match']], 'A higher bid does not repair a mismatched page.'),
            'rows' => [
                ['Auction', 'You compete', 'No guaranteed seat.'],
                ['Ceiling', 'The most you will pay', 'Set it first.'],
                ['Relevance', 'Person, line, page', 'A useful match.'],
                ['Bounce', 'They leave fast', 'The visit was wrong.'],
                ['Raise', 'Not a fix', 'Repair the page.'],
                ['Private', 'Not for sale', 'Do not chase it.'],
            ],
            'e1' => ['Example 1 — a ceiling beside a clear line', 'The money has a stop. The sentence can stand on its own.', dm_ads('This week\'s test', [
                ['Campaign', 'Household list'],
                ['Shows on', 'Search'],
                ['Ceiling', 'Rs 500 for the week, then stop'],
                ['Bid', 'Only inside that ceiling'],
                ['The line', 'One shared list that stays put. Rs 499 a year.'],
                ['Landing page', 'The offer, same words'],
            ], '<p style="margin:0;font-size:18px;color:#1a0dab;font-weight:600;">One shared list that stays put</p><p style="margin:4px 0 0;font-size:13px;color:#4b5563;">Rs 499 a year. Leave when you want.</p>')],
            'e2' => ['Example 2 — buying your way past a weak page', 'The plan is to pay more instead of telling the truth clearly.', dm_ads('Be first at any cost', [
                ['Campaign', 'Raise the bid'],
                ['Shows on', 'Search'],
                ['Ceiling', 'None'],
                ['Bid', 'Higher, until it "works"'],
                ['The line', 'Best'],
                ['Landing page', 'A menu, not the offer'],
            ], '<p style="margin:0;font-size:18px;color:#1a0dab;font-weight:600;">Best</p><p style="margin:4px 0 0;font-size:13px;color:#b91c1c;">A bigger bid does not give this line a page.</p>', 'note')],
            'micro' => [
                'A lost auction with a true line is information. A won auction with a false line is a debt.',
                'Watch the page after the click, not only the price of the click.',
                'If you cannot explain the audience out loud, do not enter the auction.',
            ],
            'quiz' => [
                $q('What does the auction sell?', 'A chance at attention, not a guaranteed seat', 'The first rank in organic search', 'A person\'s private messages', 'A permanent follower'),
                $q('What should you do when a vague ad is expensive?', 'Repair the line and the page', 'Only raise the bid', 'Hide the price', 'Buy a list'),
                $q('What is the ceiling?', 'The most you are willing to pay while you learn', 'A number you ignore', 'The organic rank', 'The follower count'),
            ],
            'practice' => 'Write the ceiling for a one-week test and the sentence the ad must keep. Note what you will repair before you raise the ceiling.',
        ]),
        $L([
            'section' => 'Paid attention',
            'title' => 'CPC, CPM, and CPA name different bills',
            'keywords' => 'cpc, cpm, cpa, roas',
            'lead' => 'The bill for attention has a name, and the name changes the decision. CPC is cost per click. CPM is cost per thousand views. CPA is cost per action, such as a list started. ROAS is the money you got back divided by the ad spend, and it is only as honest as the sales you attributed. Milk List should pick the name that matches the goal, then judge the campaign by that name.',
            'heading' => 'Know which bill you are paying',
            'para' => 'A cheap click is not a cheap customer. If a hundred rupees of clicks produce no starts, the CPC looked fine and the CPA is a failure. Paying per thousand views fits a message whose job is to be seen, not a job that must start a list. Paying per start is closer to the goal, and the platform will only do it well if the page actually starts lists. A return number that ignores refunds and ignores people who would have bought anyway is a story, not a bill. Say what you counted.',
            'bullets' => [
                'CPC: you pay when they click. Judge it with what the click did next.',
                'CPM: you pay to be shown. Use it when the job is seeing, and still send them to a true page.',
                'CPA: you pay when the named action happens. The action must be real, not a page view you relabeled.',
                'A return ratio needs a definition: which sales, over what days, after refunds or not.',
            ],
            'visual' => tf_bars('mkbill', 'Same week, different bills', [
                ['label' => 'Clicks', 'value' => 40, 'display' => '40', 'color' => '#0284c7'],
                ['label' => 'Starts', 'value' => 4, 'display' => '4', 'color' => '#059669'],
                ['label' => 'Rupees', 'value' => 80, 'display' => '80', 'color' => '#d97706'],
            ], 'Forty clicks and four starts. The click price and the start price are different facts.'),
            'rows' => [
                ['CPC', 'Per click', 'Not yet a customer.'],
                ['CPM', 'Per thousand views', 'A seeing job.'],
                ['CPA', 'Per action', 'A real start.'],
                ['Cheap click', 'Can still fail', 'If nobody starts.'],
                ['ROAS', 'Return over spend', 'Say what you counted.'],
                ['Goal', 'Pick one bill', 'Do not mix the trophy.'],
            ],
            'e1' => ['Example 1 — the bill matches the goal', 'The goal is starts, so the number on the card is cost per start.', dm_ads('This week\'s bill', [
                ['Campaign', 'Household list'],
                ['Goal', 'Lists started'],
                ['You pay for', 'A start, and you still read the clicks'],
                ['Clicks', '40'],
                ['Starts', '4'],
                ['Spent', 'Rs 800 · Rs 200 per start'],
            ], '<p style="margin:0;font-weight:700;">Do not raise the spend.</p><p style="margin:6px 0 0;font-size:14px;">The click price looked fine. The start price is the decision.</p>', 'note')],
            'e2' => ['Example 2 — celebrating the wrong bill', 'Clicks look busy. The goal was starts, and there were none.', dm_ads('The wrong bill', [
                ['Campaign', 'Household list'],
                ['Goal', 'Lists started'],
                ['You pay for', 'A click'],
                ['Clicks', '200, and the click was cheap'],
                ['Starts', '0'],
                ['Decision on the screen', 'Marked as a win'],
            ], '<p style="margin:0;font-weight:700;color:#b91c1c;">A cheap click with no start is not a win.</p>', 'note')],
            'micro' => [
                'Name the action before you choose CPC, CPM, or CPA.',
                'A view is not a start. Do not rename it so the report looks kind.',
                'Compare a bill to the value of a real start, not to a rival\'s screenshot.',
            ],
            'quiz' => [
                $q('What is CPA?', 'Cost per named action', 'Cost per adjective', 'Clicks per minute', 'A guaranteed return'),
                $q('Why can a cheap click still be a failure?', 'The clicks may produce no starts', 'Clicks are always the goal', 'CPM forbids a page', 'Price does not matter'),
                $q('What does a return ratio need?', 'A definition of which sales you counted', 'Only a screenshot', 'A new logo', 'Hidden clicks'),
            ],
            'practice' => 'Pick the goal for one week: views, clicks, or starts. Write the bill\'s name and the number that would make you stop.',
        ]),
        $L([
            'section' => 'Paid attention',
            'title' => 'The ad and the page must say the same thing',
            'keywords' => 'message match, ad relevance, landing page',
            'lead' => 'Message match means the promise that earned the click is the promise on the first screen. Same noun, same price, same next step. Milk List\'s ad says "a list that stays put, Rs 499". The page headline should be recognizable in one glance. A clever twist on the landing page feels like a bait. People leave, and you paid for the leaving.',
            'heading' => 'Check the pair before you spend',
            'para' => 'Put the ad and a screenshot of the first screen side by side. Read only the nouns and the number. If the ad says free and the page says Rs 499, stop. If the ad says list and the page says cookbook, stop. Small wording changes are fine: "stays put" and "stays on the screen" are the same promise. A different audience, a different price, or a different object is not a wording change. Fix the ad to fit the page, or build a page that fits the ad. Do not "fix" it with a popup.',
            'bullets' => [
                'Noun, price, and verb should survive the click.',
                'The first screen does the matching. Do not hide the promise below a long intro.',
                'One ad, one page. A second offer needs a second pair.',
                'After launch, read the page on a phone the way a clicker will.',
            ],
            'visual' => tf_split('mkmatch', 'Same noun and price?', 'Yes', 'You may spend', 'List, Rs 499', 'No', 'Stop the ad', 'A popup does not repair a mismatch.'),
            'rows' => [
                ['Noun', 'The object', 'List, not a book.'],
                ['Price', 'The number', 'Rs 499 both places.'],
                ['Verb', 'The step', 'Start the list.'],
                ['Screen', 'First view', 'Not below a story.'],
                ['Wording', 'May shorten', 'Meaning stays.'],
                ['Popup', 'Not a fix', 'Repair the pair.'],
            ],
            'e1' => ['Example 1 — the click feels continuous', 'The headline could have been the ad.', dm_ads('Matching pair', [
                ['Campaign', 'Household list'],
                ['Ad headline', 'A list that stays put'],
                ['Ad price', 'Rs 499 a year'],
                ['Page headline', 'A list that stays put'],
                ['Page price', 'Rs 499 a year'],
                ['Button', 'Start the list'],
            ], '<p style="margin:0;font-size:18px;color:#1a0dab;font-weight:600;">A list that stays put</p><p style="margin:4px 0 0;font-size:13px;color:#4b5563;">Rs 499 a year. Start the household list.</p>')],
            'e2' => ['Example 2 — the click breaks', 'Free in the ad, a price and a different object on the page.', dm_ads('Broken pair', [
                ['Campaign', 'Recipes'],
                ['Ad headline', 'Free recipes'],
                ['Ad price', 'Free'],
                ['Page headline', 'Cookbook'],
                ['Page price', 'Rs 999'],
                ['Button', 'The list is only a footnote'],
            ], '<p style="margin:0;font-size:18px;color:#1a0dab;font-weight:600;">Free recipes</p><p style="margin:4px 0 0;font-size:13px;color:#b91c1c;">The page is a cookbook for Rs 999.</p>', 'note')],
            'micro' => [
                'Match is a form of honesty, not a style preference.',
                'If you test a new line, send it to a page that can host that line.',
                'The fastest way to waste a budget is a beautiful ad on an unrelated page.',
            ],
            'quiz' => [
                $q('What must survive the click?', 'The noun, the price, and the next step', 'Only the logo', 'A new joke', 'A different product'),
                $q('Which change is still the same promise?', '"Stays put" shortened to "stays on the screen"', 'Free in the ad and Rs 499 on the page', 'A list in the ad and a cookbook on the page', 'A start button that opens a blog'),
                $q('What should you do when the pair disagrees?', 'Stop and fix the ad or the page', 'Add a popup', 'Raise the bid', 'Hide the price'),
            ],
            'practice' => 'Place your ad text above your first screen. Underline noun, price, and verb in both. Spend only if the underlines agree.',
        ]),
        $L([
            'section' => 'Paid attention',
            'title' => 'Retargeting is a reminder to someone who already visited',
            'keywords' => 'retargeting, frequency, reminder',
            'lead' => 'Retargeting shows a reminder to someone who already visited a page you own. It is not a right to follow them around their life, and it is not a way to infer a sensitive situation from a click. For Milk List, a fair reminder is: they opened the offer, they did not start, and for a short while you may show the same promise again, with a limit on how often.',
            'heading' => 'A short reminder, with a limit',
            'para' => 'The reminder should repeat the offer they saw, not a harsher claim and not a fake discount you invented because they hesitated. Cap the frequency: a few times is a reminder, a dozen times a day is a pursuit. Stop when they start, and stop after a window you chose, such as two weeks. Exclude people who already pay. Say in your privacy note that a visit may lead to a reminder ad. Do not build reminders from scraped addresses or from pages that reveal health, money trouble, or anything you would not mention in a shop.',
            'bullets' => [
                'Same promise they already saw. No new pressure, no fake scarcity.',
                'A frequency you can say out loud, and an end date.',
                'Stop after they convert. Stop after the window.',
                'Disclose the reminder in plain language. Skip sensitive topics entirely.',
            ],
            'visual' => tf_split('mkret', 'Did they already visit?', 'Yes, this offer', 'Remind, then stop', 'Same line, few times', 'No, or sensitive', 'Do not follow', 'A cap is part of the plan, not a luxury.'),
            'rows' => [
                ['Who', 'Visited this offer', 'Did not start.'],
                ['Line', 'The same promise', 'No harsher claim.'],
                ['Cap', 'How often', 'A few, not a chase.'],
                ['Window', 'When it ends', 'Two weeks, for example.'],
                ['Stop', 'They started', 'Or the window closed.'],
                ['Say it', 'In the privacy note', 'A visit may remind.'],
            ],
            'e1' => ['Example 1 — a reminder you could explain', 'Same offer, a limit, and an end.', dm_ads('Reminder, two weeks', [
                ['Who', 'Visited the offer and did not start'],
                ['Leave out', 'People who already pay'],
                ['How often', 'A few times, not all day'],
                ['Ends', 'After 14 days, or when they start'],
                ['The line', 'Same promise as the page'],
                ['Said in the privacy note', 'A visit may show this offer again'],
            ], '<p style="margin:0 0 6px;font-weight:800;">Still thinking about the list?</p><p style="margin:0 0 8px;font-size:14px;">One shared list that stays put. Rs 499 a year.</p><span style="display:inline-block;background:#059669;color:#fff;border-radius:8px;padding:8px 12px;font-size:13px;font-weight:700;">Start the list</span>', 'feed')],
            'e2' => ['Example 2 — a chase', 'A new threat, no limit, and a topic they did not ask to discuss.', dm_ads('A chase', [
                ['Who', 'Anyone who hesitated'],
                ['How often', 'All day, no end'],
                ['The line', 'Last chance forever'],
                ['Extra', 'A private page they opened'],
                ['Leave out', 'Nobody'],
                ['Privacy note', 'Says nothing'],
            ], '<p style="margin:0;font-weight:800;color:#b91c1c;">We know you hesitated.</p><p style="margin:6px 0 0;font-size:14px;">That is not a reminder. That is a chase.</p>', 'note')],
            'micro' => [
                'Frequency is a creative decision and an ethical one.',
                'A reminder cannot fix a page that was unclear the first time. Fix the page.',
                'If you would be uncomfortable saying "we showed you this twelve times", do not.',
            ],
            'quiz' => [
                $q('What is a fair retargeting ad?', 'The same offer, shown a few times, for a short window', 'A harsher claim because they hesitated', 'A chase all day', 'A comment on a private page'),
                $q('When do you stop?', 'When they start, and when the window ends', 'Never', 'Only when the budget is infinite', 'When they complain in public'),
                $q('What should the privacy note say?', 'That a visit may lead to a reminder', 'Nothing', 'That you follow them everywhere', 'That frequency is unlimited'),
            ],
            'practice' => 'Write the reminder line, the maximum times you will show it, and the day it ends. If the line is harsher than the page, rewrite it.',
        ]),
        $L([
            'section' => 'Measurement',
            'title' => 'One conversion you can count',
            'keywords' => 'conversion, goal, analytics event',
            'lead' => 'A conversion is the action you named when you set the goal. For the Milk List start campaign, the conversion is "the household list was opened", which you can see on the thank-you page or as a completed event. It is not the ad click, not a page view, and not a button press that failed. If you cannot point to the moment it becomes true, you cannot count it.',
            'heading' => 'Name the moment, then count that',
            'para' => 'Write the sentence: "A conversion happens when ___." For starts: when the thank-you page loads after a real submit. For a letter: when they reply, or when they add an item, not when the letter is sent. One campaign, one primary conversion. Secondary facts — a visit, a scroll — can be diagnostic. They are not the trophy. A counter on your site, sometimes called a pixel when an ad platform provides it, only counts what you disclosed and what the person\'s choices allow. It is a tally, not a shadow.',
            'bullets' => [
                'The primary conversion matches the campaign job. A see-campaign should not pretend a view is a sale.',
                'Count the completed moment. The thank-you page is a better count than the click on the ad.',
                'Say the definition next to the number. "Starts" means the list opened.',
                'Do not fire the success count on a failed form, and do not count one person five times because the page refreshed.',
            ],
            'visual' => tf_flow('mkconv', 'The moment you count', [['click', 'not yet'], ['form', 'not yet'], ['thanks', 'count it']], 'Until the list is open, you have activity, not a conversion.'),
            'rows' => [
                ['Name', 'The action', 'List opened.'],
                ['Where', 'The moment', 'Thank-you page.'],
                ['Not', 'Earlier steps', 'Ad click, page view.'],
                ['Primary', 'One per campaign', 'Starts, this month.'],
                ['Say it', 'Beside the chart', 'What the word means.'],
                ['Tally', 'Disclosed', 'Not a shadow.'],
            ],
            'e1' => ['Example 1 — the count is the thank-you', 'The card says what was counted. A click is listed as a different fact.', dm_page('What we count', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Starts this week</h1>
<p>A start means the thank-you page loaded after the list opened.</p>
<p><strong>Starts: 4.</strong> Ad clicks: 40. Clicks are not starts.</p>
HTML)],
            'e2' => ['Example 2 — the click counted as a win', 'The goal moved so the chart could look successful.', dm_page('A miscounted click', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">40 conversions</h1>
<p>We counted every ad click as a conversion. Lists opened: 0.</p>
HTML)],
            'micro' => [
                'If two people argue about what the number means, the definition is missing.',
                'A refresh should not create a second start. Count people or completed events with care.',
                'Pick a conversion the team can see in the product, not only in an ad dashboard.',
            ],
            'quiz' => [
                $q('What is a conversion?', 'The completed action you named in the goal', 'Every click', 'Every page view', 'A large impression count'),
                $q('Where should Milk List count a start?', 'When the thank-you page shows the list is open', 'When the ad is clicked', 'When the form fails', 'When the page refreshes five times'),
                $q('What is a counter on the page?', 'A tally of the action you disclosed', 'A shadow you do not mention', 'A replacement for the thank-you page', 'Proof of rank'),
            ],
            'practice' => 'Write the conversion sentence for your current campaign: "It counts when ___." Then write two things you will not count as that conversion.',
        ]),
        $L([
            'section' => 'Measurement',
            'title' => 'Source and medium tell you which room they came from',
            'keywords' => 'source, medium, channel, attribution',
            'lead' => 'Source is where the visit came from. Medium is the kind of room. A search ad might be source google and medium cpc. Your letter might be source newsletter and medium email. A visit with no label often shows up as direct. Milk List uses the pair to see which room deserves more of the promise, not to crown a single click as the whole story.',
            'heading' => 'A pair, not a trophy',
            'para' => 'Last click means you credit the last room before the start. It is simple and it is incomplete. Asha may have seen a post on Tuesday and started from the letter on Friday. The letter gets the last click. The post still did a job. Use last click as a tidy report, and keep a human note of the other rooms in the campaign. Do not tag your own menus with a fresh source or you will overwrite the true one and call every page a new campaign. Direct is a bucket for "we do not know", not a personality type.',
            'bullets' => [
                'Source names the place. Medium names the kind: email, cpc, organic.',
                'Read them as a pair. Source alone is ambiguous.',
                'Last click is a convenience. It is not the whole journey.',
                'Unknown is allowed. Direct means unlabeled, not "they love the brand".',
            ],
            'visual' => tf_flow('mksrc', 'Rooms before a start', [['post', 'Tuesday'], ['letter', 'Friday'], ['start', 'last click']], 'The letter may be last. The post may still have mattered.'),
            'rows' => [
                ['Source', 'The place', 'Newsletter, or a search site.'],
                ['Medium', 'The kind', 'Email, cpc, organic.'],
                ['Pair', 'Read both', 'Newsletter / email.'],
                ['Last click', 'The latest room', 'A tidy, partial credit.'],
                ['Direct', 'Unlabeled', 'Not a compliment.'],
                ['Menus', 'Do not retag', 'Keep the real source.'],
            ],
            'e1' => ['Example 1 — a pair you can read', 'You know the room. You do not pretend it was the only room.', dm_page('Source and medium', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Starts by room</h1>
<p>newsletter / email — 3 starts</p>
<p>google / cpc — 1 start</p>
<p>Last click credits the letter. The Tuesday post is still in the campaign notes.</p>
HTML)],
            'e2' => ['Example 2 — a single number called "the truth"', 'One room is crowned and the rest of the week is erased.', dm_page('One crown', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Email did everything</h1>
<p>Last click was the letter. Therefore the page, the post, and the ad did nothing.</p>
HTML)],
            'micro' => [
                'Organic search and paid search are different mediums even on the same site.',
                'A partner\'s link should have its own source so you can thank the right room.',
                'If every visit is direct, your links are unlabeled or your tool is missing, not magical.',
            ],
            'quiz' => [
                $q('What does medium describe?', 'The kind of room, such as email or cpc', 'The person\'s age', 'The font', 'The last adjective'),
                $q('What is last click?', 'Credit to the latest room, which is only part of the story', 'Proof that nothing else mattered', 'The organic rank', 'The price'),
                $q('What does direct usually mean?', 'The visit was unlabeled', 'They definitely typed the brand from memory', 'The ad failed', 'The email was best'),
            ],
            'practice' => 'List the rooms in your current campaign. Write the source and medium you expect for each. Note which one last click would overvalue.',
        ]),
        $L([
            'section' => 'Measurement',
            'title' => 'UTM labels belong on links you control',
            'keywords' => 'utm, campaign link, tagged url',
            'lead' => 'A UTM label is a note you add to a link you control, so the visit can report its source, medium, and campaign. The Milk List letter might use source newsletter, medium email, and campaign march-list. The labels should be boring, consistent, and lowercase. They are not a place for jokes, and they are not for links inside your own menus.',
            'heading' => 'Tag the door, not the hallway',
            'para' => 'Add tags when the link leaves a room you do not already measure: a letter, a post, a partner, an ad you set up yourself. Use the same spelling every time. "Email" and "e-mail" become two mediums and a confused report. Do not tag internal links from one page to another. That replaces the original source with "internal" and you lose the letter or the ad that actually brought them. Keep a tiny glossary: the three or four campaign names you will use this season.',
            'bullets' => [
                'Source, medium, and campaign are enough. Extra tags only when you will read them.',
                'One spelling, lowercase, hyphens rather than spaces.',
                'Never tag the menu or the buttons on your own site.',
                'The campaign name should match the card: march-list, not "final-FINAL-v7".',
            ],
            'visual' => tf_split('mkutm', 'Tag this off-site link?', 'Yes, tag it', 'source and medium', 'Same spelling', 'A site menu', 'Leave it clean', 'Internal tags erase the real source.'),
            'rows' => [
                ['Source', 'The place', 'newsletter'],
                ['Medium', 'The kind', 'email'],
                ['Campaign', 'The effort', 'march-list'],
                ['Spelling', 'Stable', 'Lowercase.'],
                ['Internal', 'Do not tag', 'Menus stay clean.'],
                ['Glossary', 'A short list', 'Names you will reuse.'],
            ],
            'e1' => ['Example 1 — a tagged letter link', 'The labels are dull and readable. The page is still the offer.', dm_page('A tagged link', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Link in the March letter</h1>
<p>milklist.example/shared-list?utm_source=newsletter&amp;utm_medium=email&amp;utm_campaign=march-list</p>
<p>Source newsletter. Medium email. Campaign march-list.</p>
HTML)],
            'e2' => ['Example 2 — tags on the menu', 'Every internal click pretends to be a new campaign.', dm_page('Tagged menus', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Header links</h1>
<p>Home, price, and login all carry a fresh campaign tag. The letter that brought Asha disappears from the report.</p>
HTML)],
            'micro' => [
                'A tag does not make a bad page good. It only names the door.',
                'Partners should get a source of their own so you can see their visits.',
                'If you will not read a tag, do not add it.',
            ],
            'quiz' => [
                $q('What is a UTM label for?', 'Naming the source, medium, and campaign on a link you control', 'Hiding keywords', 'Tagging every menu item', 'Replacing the page'),
                $q('Which link should you tag?', 'The link in a letter or a post', 'The price button in your own header', 'Every footer link', 'A link from one paragraph to the next'),
                $q('Why keep spelling consistent?', 'So one medium does not split into two reports', 'So the auction bids higher', 'So the title ranks', 'So people cannot leave'),
            ],
            'practice' => 'Write the tagged URL for one letter and one post. Then check that your site menu has no campaign tags.',
        ]),
        $L([
            'section' => 'Measurement',
            'title' => 'Vanity counts are not a decision',
            'keywords' => 'vanity metrics, impressions, followers',
            'lead' => 'A vanity count feels large and does not change a decision. Followers, impressions, and a reach spike are in that family when they are not tied to a next step. A decision count changes what you do: lists started, cost per start, replies that asked a real question, people who left. Milk List can glance at reach. It should steer by starts and by the cost of a start.',
            'heading' => 'Steer by a number that can say no',
            'para' => 'Before the week, write the number that would make you stop or continue. "If a start costs more than Rs 200, we repair the page before we spend more." That number can disappoint you. A follower count rarely will, which is why it is a poor steering wheel. Keep vanity counts in a lower row if they help you debug — a post with zero reach never had a chance to be clicked. Do not put them in the first sentence of the report.',
            'bullets' => [
                'The first number in the report is the conversion you named.',
                'The second is the cost or the effort attached to it.',
                'Reach and followers sit below, as context, not as the verdict.',
                'If a number cannot make you change the page, the spend, or the sentence, it is not steering.',
            ],
            'visual' => tf_bars('mkvan', 'What the report leads with', [
                ['label' => 'Starts', 'value' => 4, 'display' => '4', 'color' => '#059669'],
                ['label' => 'Per start', 'value' => 20, 'display' => '200', 'color' => '#d97706'],
                ['label' => 'Reach', 'value' => 90, 'display' => '9k', 'color' => '#94a3b8'],
            ], 'Reach can be the biggest bar and still not be the decision.'),
            'rows' => [
                ['Lead', 'The conversion', 'Lists started.'],
                ['Next', 'The cost', 'Rupees per start.'],
                ['Context', 'Reach, followers', 'Below the fold.'],
                ['Stop rule', 'Written before', 'Repair past Rs 200.'],
                ['Debug', 'Zero reach', 'The post was unseen.'],
                ['Verdict', 'A change', 'Page, spend, or sentence.'],
            ],
            'e1' => ['Example 1 — a report that can say no', 'The stop rule is on the card. Reach is present and not in charge.', dm_page('A steering report', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Week report</h1>
<p><strong>Starts: 4.</strong> Rs 200 per start. Stop rule was Rs 200, so we do not raise spend.</p>
<p>Reach was large. It is not the verdict.</p>
HTML)],
            'e2' => ['Example 2 — a trophy report', 'The big number cannot tell you what to do on Monday.', dm_page('A trophy', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">9,000 reached</h1>
<p>Followers up. Impressions up. Starts: not on this page.</p>
HTML)],
            'micro' => [
                'A number you celebrate every week no matter the outcome is decoration.',
                'Put the disappointing number in the same size type as the flattering one.',
                'Compare this week with a similar week, not with your favorite screenshot.',
            ],
            'quiz' => [
                $q('Which number should lead the Milk List report?', 'Lists started, and the cost per start', 'Followers', 'Impressions alone', 'A reach spike'),
                $q('When is reach useful?', 'As context, such as noticing a post nobody saw', 'As the verdict', 'As a replacement for starts', 'As proof the page matched'),
                $q('What makes a number a decision?', 'It can make you change the page, the spend, or the sentence', 'It is large', 'It went up', 'It fits in a trophy'),
            ],
            'practice' => 'Design a five-line weekly note. Line one is the conversion. Line two is the cost or effort. Line three is the stop rule. Reach may be line five.',
        ]),
        $L([
            'section' => 'Measurement',
            'title' => 'Change one thing, then read the result',
            'keywords' => 'test, one change, before and after',
            'lead' => 'If you change the headline and the button and the price in the same week, you will not know which one moved the starts. Change one thing. Read the result against a fair comparison: the same kind of days, the same room, a similar number of visits. Milk List might test only the headline for two weeks. Small sites rarely get a perfect laboratory. They can still refuse to change five things at once.',
            'heading' => 'One difference you can name',
            'para' => 'Write the before and the after in one line: "Headline A versus headline B, same page, same ads, for fourteen days." Decide what "better" means before you look: more starts, not more compliments. If traffic is tiny, say so and treat the result as a hint, not a law. Do not stop a test on the first lucky hour. Do not keep a winner that uses a claim you cannot support. A false line that converts is still a false line.',
            'bullets' => [
                'One change: headline, or button, or first image. Not all three.',
                'Same room and a similar stretch of days.',
                'Name the decision number before you peek.',
                'A hint is allowed on a small site. Pretending it is certainty is not.',
            ],
            'visual' => tf_panels('mktest', 'One change', 'Week A', ['List stays put', 'Same button', 'Same price'], 'Week B', ['Milk bought once', 'Same button', 'Same price'], 'Only the headline moved. That is the thing you may learn from.'),
            'rows' => [
                ['Change', 'Exactly one', 'The headline.'],
                ['Hold', 'Everything else', 'Button, price, room.'],
                ['Window', 'Long enough', 'Fourteen days, say.'],
                ['Better', 'Named first', 'More starts.'],
                ['Small', 'Call it a hint', 'Not a law.'],
                ['Ethics', 'Still applies', 'No false winner.'],
            ],
            'e1' => ['Example 1 — a test you can explain', 'One difference. The decision rule is written.', dm_page('One change', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Headline test</h1>
<p>A: One shared list that stays put.</p>
<p>B: Milk bought once.</p>
<p>Same button, same price, 14 days. Better means more lists opened.</p>
HTML)],
            'e2' => ['Example 2 — five changes and a story', 'Whatever happens, you can invent a reason. You cannot learn.', dm_page('Five changes', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">We changed everything</h1>
<p>New headline, new price, new photo, new form, new audience. Starts rose. We are sure it was the shade of green.</p>
HTML)],
            'micro' => [
                'Changing nothing and hoping is also a choice. It is just an unmeasured one.',
                'Seasonal weeks are a poor pair. A festival week is not a normal Tuesday.',
                'Document the loser. It stops the team from "rediscovering" it next month.',
            ],
            'quiz' => [
                $q('Why change only one thing?', 'So you can tell what moved the result', 'So the page looks busy', 'So five stories remain possible', 'So you can skip the number'),
                $q('What should "better" mean?', 'The conversion you named before you looked', 'Whichever chart rose', 'More compliments', 'A greener button'),
                $q('How should a small site treat a short test?', 'As a hint, and say so', 'As a permanent law', 'As proof of a false claim', 'As a reason to change five more things'),
            ],
            'practice' => 'Write a two-week test: the one change, what you will hold still, and the number that defines better. Include the line "this is a hint if visits are few".',
        ]),
        $L([
            'section' => 'Measurement',
            'title' => 'A hypothesis is a sentence you can disprove',
            'keywords' => 'hypothesis, learning, decision',
            'lead' => 'A hypothesis is a sentence that could be wrong, with a way you would notice. "If we put the price in the headline, more of the same visitors will start, because Asha is tired of hunting for it." That can fail. "We should post more and be authentic" cannot fail, because it never named a result. Milk List learns by writing sentences reality can refuse.',
            'heading' => 'If this, then that, because',
            'para' => 'Use three clauses. If we change this one thing. Then this number moves in this direction. Because of a reason rooted in the person, not in a slogan. After the window, write what happened in a sentence just as plain. Keep a short log: date, hypothesis, result, what you will do. A log turns a month of effort into a memory. A folder of charts does not. Killing a hypothesis is a success for the team. It saves the next month.',
            'bullets' => [
                'If / then / because. The then-clause is a number.',
                'The because-clause is about Asha, not about "boosting synergy".',
                'Write the result even when you were wrong. Especially then.',
                'One log the whole team can open. Three lines per test is enough.',
            ],
            'visual' => tf_flow('mkhyp', 'A sentence reality can refuse', [['if', 'price in title'], ['then', 'more starts'], ['because', 'she hunts']], 'If it cannot be wrong, it is not a hypothesis.'),
            'rows' => [
                ['If', 'One change', 'Price in the headline.'],
                ['Then', 'A number', 'More starts.'],
                ['Because', 'Her reason', 'She hunts for the price.'],
                ['Window', 'Named', 'Fourteen days.'],
                ['Result', 'Plain sentence', 'Including a no.'],
                ['Log', 'Three lines', 'Date, result, next.'],
            ],
            'e1' => ['Example 1 — a hypothesis and its result', 'The no is written down. The next action is smaller, not louder.', dm_page('The log', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">14 March</h1>
<p>If the price is in the headline, starts rise, because she hunts for it.</p>
<p>Result: starts did not rise. Next: leave the price, change the first sentence instead.</p>
HTML)],
            'e2' => ['Example 2 — a sentence that cannot lose', 'No number, no change, no way to be wrong.', dm_page('Cannot lose', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Hypothesis</h1>
<p>We should post more, be authentic, and leverage synergy.</p>
<p>Result: we feel good.</p>
HTML)],
            'micro' => [
                'A failed hypothesis that you recorded is worth more than a win you cannot explain.',
                'Do not edit the because-clause after you see the chart.',
                'The log is a teaching tool for the next person on the account.',
            ],
            'quiz' => [
                $q('What makes a hypothesis usable?', 'It can be wrong, and you would notice', 'It uses the word authentic', 'It cannot fail', 'It has no number'),
                $q('What belongs in the then-clause?', 'A number moving in a direction', 'A feeling', 'A new logo', 'The word synergy'),
                $q('What should you record when the idea fails?', 'The result and the next, smaller action', 'Nothing, so morale stays high', 'A new slogan that erases it', 'Only the reach chart'),
            ],
            'practice' => 'Write one if / then / because sentence for the next test. Leave two blank lines under it labeled Result and Next.',
        ]),
        $L([
            'section' => 'Trust',
            'title' => 'The second purchase is a different message',
            'keywords' => 'retention, second purchase, customer message',
            'lead' => 'The second yes is cheaper to earn than the first only if you kept the first promise. Asha already has a list. She does not need the introduction again. She needs a message about using it: invite the other shopper, add the weekly staples, or renew because the year is ending. A second purchase, a renewal, or a referral starts from memory, not from a stranger\'s pitch.',
            'heading' => 'Talk to someone who already believes you a little',
            'para' => 'Segment this person away from prospects. Thank the specific thing they did. Teach the next useful habit. If you offer a renewal, say the date and the same price you will actually charge. Do not pretend they are new in order to show a harsher "first time" discount, and do not punish them for staying. The best second message is often product help. A household that uses the list every week is already the campaign.',
            'bullets' => [
                'No re-introduction. They have met you.',
                'One next habit, tied to what they already started.',
                'Renewal is a clear date and a true price.',
                'Help is marketing. A person who succeeds comes back.',
            ],
            'visual' => tf_panels('mksec', 'Two readers', 'Stranger', ['What it is', 'Rs 499', 'Start'], 'Already in', ['Add Saturday', 'Invite the house', 'Renew on the date'], 'The second message assumes the first promise held.'),
            'rows' => [
                ['Reader', 'Already started', 'Not a stranger.'],
                ['Open', 'Their habit', 'The list they opened.'],
                ['Ask', 'One next habit', 'Invite, or add staples.'],
                ['Renew', 'A real date', 'The true price.'],
                ['Avoid', 'Fake new-user bait', 'They would notice.'],
                ['Help', 'A real tip', 'Use is the campaign.'],
            ],
            'e1' => ['Example 1 — a note for someone who started', 'It assumes the list exists and asks for one habit.', dm_page('The second note', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Your list is a week old</h1>
<p>Invite the other shopper so Tuesday\'s milk is added once. Your year runs until 14 March.</p>
<p><a href="#invite" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Invite the house</a></p>
HTML)],
            'e2' => ['Example 2 — the stranger pitch, again', 'It ignores what she already did.', dm_page('As if new', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Have you heard of Milk List?</h1>
<p>First-time offer, even though you already pay. Also meet the brand.</p>
HTML)],
            'micro' => [
                'A lapse note should be useful, not scolding. One note, then quiet.',
                'The second price must match the first promise. Surprise fees break retention.',
                'Referrals come after success, not before the first week works.',
            ],
            'quiz' => [
                $q('How should a message to a current household differ?', 'It continues a habit they started, instead of introducing you', 'It pretends they are new', 'It hides the renewal date', 'It scolds them'),
                $q('What makes a renewal honest?', 'A clear date and the price you will charge', 'A fake first-time discount', 'A surprise fee', 'A new brand name'),
                $q('Why is product help a marketing message?', 'A person who succeeds has a reason to stay', 'It replaces the price', 'It avoids a thank-you', 'It inflates reach'),
            ],
            'practice' => 'Write a four-line note to someone who started two weeks ago. It must not explain what Milk List is. It must name one habit and, if relevant, a true date.',
        ]),
        $L([
            'section' => 'Trust',
            'title' => 'A review and a referral are earned, not bought',
            'keywords' => 'reviews, referrals, word of mouth',
            'lead' => 'A review is another person speaking. A referral is a customer introducing someone they know. Both are earned by a promise you kept. Milk List may ask a household, after the list has been useful, whether they will say so in their own words. It may thank a referral with a clear, modest thank-you that does not pay people to lie. It may not buy stars, write its own reviews, or pay a friend to pretend.',
            'heading' => 'Ask after value, publish with permission',
            'para' => 'Ask at a fair moment: they added items for two weeks, not ten minutes after signup. Make the ask easy and optional. Publish only what they agreed to, without quietly polishing the meaning. When a review is unhappy and fair, answer the fact and fix the product if the fact is yours. A referral offer should be sayable to both sides: "If your neighbor starts a list, you both get a month." The neighbor still sees the true price afterward. Disclose a reward. A secret payment for praise is a hidden ad.',
            'bullets' => [
                'Ask after a real use, not at the peak of your excitement.',
                'Their words, their permission. No purchased stars.',
                'Answer a fair complaint. Do not bury it.',
                'A referral reward is disclosed, modest, and does not change the truth the new person sees.',
            ],
            'visual' => tf_flow('mkrev', 'After it worked', [['use', 'two weeks'], ['ask', 'optional'], ['yes', 'their words'], ['publish', 'with permission']], 'The ask comes after value. The words stay theirs.'),
            'rows' => [
                ['When', 'After use', 'Not minute one.'],
                ['Ask', 'Optional', 'Easy to decline.'],
                ['Words', 'Theirs', 'No quiet rewrite.'],
                ['Stars', 'Not for sale', 'No fake accounts.'],
                ['Complaint', 'Answer the fact', 'Fix it if it is true.'],
                ['Referral', 'Disclosed reward', 'True price remains.'],
            ],
            'e1' => ['Example 1 — an ask after value', 'The household can say no. The reward, if any, is not the first line.', dm_page('Ask after two weeks', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Has the list helped?</h1>
<p>If you want to tell another household, here is a line you can edit. You can also say nothing.</p>
<p><a href="#share" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Write a sentence</a></p>
HTML)],
            'e2' => ['Example 2 — bought praise', 'Stars for money, and a script.', dm_page('Bought praise', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Rs 50 per five-star line</h1>
<p>Use this script. Do not mention we paid. Post from extra accounts if you can.</p>
HTML)],
            'micro' => [
                'A single specific sentence beats a badge that says five stars.',
                'Do not incentivize only positive reviews. That pressures a lie.',
                'A partner who sends people is fine when the page stays yours and the relationship is disclosed.',
            ],
            'quiz' => [
                $q('When should you ask for a review?', 'After the product has been useful', 'Ten minutes after signup', 'Before they know the price', 'Only if you pay for five stars'),
                $q('What may you publish?', 'Words they agreed to, without changing the meaning', 'A script you paid for', 'A review from an account you control', 'A complaint with the facts deleted'),
                $q('What must a referral reward be?', 'Disclosed, and the new person still sees the true price', 'Secret', 'Large enough to buy a lie', 'A replacement for the product'),
            ],
            'practice' => 'Write the ask you will send after two weeks of use. Include the sentence that says they may decline. Remove any payment for a positive star.',
        ]),
        $L([
            'section' => 'Trust',
            'title' => 'Consent, privacy, and honest claims',
            'keywords' => 'consent, privacy, honest advertising',
            'lead' => 'Consent means the person understood what they were agreeing to, and they could say no. Privacy means you keep only what you need, you say why, and you do not pass their details onward as if they were inventory. Honest claims mean every public sentence can be shown. These three are the floor under the rest of the course. A clever campaign that fails them is not advanced. It is unfit.',
            'heading' => 'The floor under every channel',
            'para' => 'On the form, say what you collect and what letter or reminder it allows. In a privacy note a person can read, name the rooms: the page, the letter, a reminder ad after a visit. Keep payment data with a proper provider, not in a marketing spreadsheet. Do not buy lists, do not scrape groups, do not hide a tracker behind a confusing wall of text. Prices match. Limits are real. Superlatives have a source or they stay unsaid. If a tactic only works when the person does not notice, do not use it.',
            'bullets' => [
                'A yes is informed and optional. Pre-checked boxes and buried sentences are not consent.',
                'Collect less. Say why you have what you have. Do not sell the list as a file of people.',
                'Claims, prices, and timers match reality on every channel.',
                'If you cannot explain the tactic to Asha without embarrassment, it does not ship.',
            ],
            'visual' => tf_flow('mketh', 'The floor', [['yes', 'they understood'], ['keep', 'only what you need'], ['say', 'what you can show']], 'Every channel in this course stands on these three.'),
            'rows' => [
                ['Consent', 'Understood, optional', 'What the yes covers.'],
                ['Privacy', 'Need and purpose', 'No sold files.'],
                ['Reminder', 'Disclosed', 'A visit may remind.'],
                ['Payment', 'A proper provider', 'Not a stray sheet.'],
                ['Claims', 'Showable', 'Price and limits.'],
                ['Test', 'Say it to Asha', 'Embarrassment means no.'],
            ],
            'e1' => ['Example 1 — a yes a person can read', 'The note says what happens. The button is a choice.', dm_page('A readable yes', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Before you start</h1>
<p>We keep your name and email to open the list and to send the note you asked for. A visit may be used to show this same offer again for a short time. You can leave the notes whenever you want.</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — a yes that is not a yes', 'Buried, pre-decided, and a file they did not offer.', dm_page('Not a yes', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Continue</h1>
<p>By squinting at this you accept partner sharing, a sold file, and offers you cannot leave. The box was already checked.</p>
HTML)],
            'micro' => [
                'Laws differ by country. The habit that travels is plain language and a real no.',
                'A privacy note that only a lawyer can parse is a hiding place.',
                'Honest claims are also a measurement habit. You cannot learn from a fake number.',
            ],
            'quiz' => [
                $q('What is consent?', 'The person understood and could say no', 'A pre-checked box', 'A file you bought', 'A long page nobody can read'),
                $q('What should you do with a list of people?', 'Use it only for the purpose they agreed to', 'Sell it as inventory', 'Scrape more addresses into it', 'Hide a tracker behind it'),
                $q('Which test should a tactic pass?', 'You can explain it to the buyer without embarrassment', 'It works only if they do not notice', 'It raises clicks at any cost', 'It buries the price'),
            ],
            'practice' => 'Read your form, your ad, and your leave link in one sitting. Write one sentence a buyer would understand for each: what they agree to, what the ad claims, and how they stop.',
        ]),
        $L([
            'section' => 'The project',
            'title' => 'Project: a campaign you can explain',
            'keywords' => 'marketing plan, campaign, project',
            'lead' => 'This is the whole course on one card. You will explain a campaign without slides full of adjectives. Milk List is the example, and you may swap in a real offer you are allowed to speak for. By the end, a stranger should be able to repeat the person, the promise, the page, the one extra room, the letter, and the number that decides the month.',
            'heading' => 'One card, six lines',
            'para' => 'Line 1, the person: who they are and who they are not. Line 2, the offer: what they get, what they give, and the exit. Line 3, the page: the headline, the button, and the URL. Line 4, one other room: a post or an ad, with the same nouns, and whether it is owned, earned, or paid. Line 5, the letter: who may receive it, the subject, the one action, and how they leave. Line 6, the number: the conversion, the source you expect, and the stop rule. If a line needs a claim you cannot show, cut the claim. Then look at the card and say it aloud. That is the job.',
            'bullets' => [
                'Use one offer. A second product is a second card.',
                'The page exists in words before any spend. Write the headline and the button.',
                'Paid is optional. If you include it, add the ceiling and the match to the page.',
                'The number must be able to disappoint you. Otherwise it is vanity.',
            ],
            'visual' => tf_flow('mkproj', 'The card', [['person', 'Asha'], ['offer', 'Rs 499'], ['page', 'one list'], ['number', 'starts']], 'Six lines. If you cannot say them, the campaign is not ready.'),
            'rows' => [
                ['Person', 'And not-for', 'Household buyer.'],
                ['Offer', 'Get, give, exit', 'List, Rs 499, leave.'],
                ['Page', 'Headline, button', 'Start the list.'],
                ['Room', 'Same nouns', 'Post, ad, or both.'],
                ['Letter', 'Yes, ask, leave', 'Only people who asked.'],
                ['Number', 'Can say no', 'Starts and a ceiling.'],
            ],
            'e1' => ['Example 1 — a card you can say aloud', 'Every line from the course is present, in the same nouns.', dm_page('Campaign card', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Milk List, March</h1>
<p><strong>Person.</strong> Buys for a shared house. Not a solo shopper.</p>
<p><strong>Offer.</strong> One list for a year, Rs 499, leave when you want.</p>
<p><strong>Page.</strong> "A list that stays put." Button: Start the list.</p>
<p><strong>Room.</strong> One Tuesday post, same nouns, link to the page.</p>
<p><strong>Letter.</strong> Only people who asked. Subject: Add milk before the shop. One button. Leave in one tap.</p>
<p><strong>Number.</strong> A start is the thank-you page. Stop if a paid start would pass Rs 200.</p>
HTML)],
            'e2' => ['Example 2 — a card that cannot be run', 'No person, no price, no page, no number. It is a mood.', dm_page('A mood', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">March campaign</h1>
<p>Go viral. Be everywhere. Crush it. Leverage the brand. Report impressions.</p>
HTML)],
            'micro' => [
                'If the card and the page disagree, the page wins and the card gets rewritten.',
                'Owned first, then a letter to people who asked, then a small paid test if the page is ready.',
                'You should be able to teach this card to a teammate in two minutes.',
            ],
            'quiz' => [
                $q('What belongs on the campaign card?', 'Person, offer, page, one room, letter, and a number that can say no', 'A mood and a reach goal', 'Five products and no price', 'Only a logo'),
                $q('When is paid attention allowed on the card?', 'When the page can keep the promise and you have a ceiling', 'Before the page exists', 'When you bought a list', 'When the claim is unverifiable'),
                $q('Which number is fit to lead the card?', 'Lists opened, with a stop rule', 'Impressions alone', 'Followers', 'A spike you hope for'),
            ],
            'practice' => 'Fill the six lines for an offer you may honestly advertise. Read them aloud. Delete any claim you cannot point to. Keep the card where the next letter and the next ad must match it.',
        ]),
    ];
}
