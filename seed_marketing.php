<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/track_seed.php';
require_once __DIR__ . '/includes/teach_figures.php';

function marketing_meta(): array
{
    return [
        'title' => 'Digital Marketing Tutorial',
        'slug' => 'digital-marketing-tutorial',
        'description' => 'Digital marketing from the first audience through the offer, the page, search, content, email, ads, and measurement, ending in one campaign you can explain.',
        'keywords' => 'digital marketing tutorial, landing page, seo basics, email marketing, social content, paid ads, conversion, utm, consent',
    ];
}

function dm_page(string $title, string $inner, bool $wide = false): string
{
    $main = $wide
        ? 'max-width:100%;margin:0;background:transparent;padding:8px;border:0;border-radius:0;'
        : 'max-width:440px;margin:18px auto;background:#ffffff;padding:24px;border:1px solid #e2e8f0;border-radius:16px;';
    return '<!DOCTYPE html>' . "\n"
        . '<html>' . "\n"
        . '<head><meta charset="utf-8"><title>' . $title . '</title></head>' . "\n"
        . '<body style="font-family:sans-serif;margin:0;background:#f8fafc;color:#0f172a;">' . "\n"
        . '<main style="' . $main . '">' . "\n"
        . $inner . "\n"
        . '</main>' . "\n"
        . '</body>' . "\n"
        . '</html>';
}

function dm_serp(string $title, string $desc): string
{
    return '<title>' . $title . '</title>' . "\n"
        . '<meta name="description" content="' . $desc . '">' . "\n"
        . '<link rel="canonical" href="https://milklist.example/shared-list">';
}

function dm_ads(string $title, array $rows, string $preview, string $kind = 'search'): string
{
    $lines = '';
    foreach ($rows as $row) {
        $lines .= '<div style="display:flex;justify-content:space-between;gap:12px;padding:8px 0;border-bottom:1px solid #e2e8f0;font-size:13px;align-items:baseline;">'
            . '<span style="color:#64748b;white-space:nowrap;">' . $row[0] . '</span>'
            . '<strong style="text-align:right;max-width:68%;color:#0f172a;">' . str_replace('-', '&#8209;', $row[1]) . '</strong></div>';
    }
    if ($kind === 'feed') {
        $seen = '<div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:12px;">'
            . '<p style="font-size:11px;color:#64748b;margin:0 0 8px;">Milk List · Sponsored</p>'
            . $preview . '</div>';
    } elseif ($kind === 'note') {
        $seen = '<div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:14px;padding:12px;font-size:14px;">' . $preview . '</div>';
    } else {
        $seen = '<div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:12px;">'
            . '<p style="font-size:12px;color:#059669;margin:0 0 4px;">Ad · milklist.example/shared-list</p>'
            . $preview . '</div>';
    }
    $inner = '<div data-desk="ad" style="display:flex;flex-wrap:wrap;gap:14px;align-items:stretch;">'
        . '<div style="flex:1 1 260px;background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;padding:14px 16px;">'
        . '<p style="font-size:11px;letter-spacing:.08em;font-weight:800;color:#0284c7;margin:0;">AD SETUP</p>'
        . '<h1 style="font-size:18px;margin:6px 0 10px;color:#0f172a;">' . $title . '</h1>'
        . $lines
        . '</div>'
        . '<div style="flex:1 1 260px;background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;padding:14px 16px;">'
        . '<p style="font-size:11px;letter-spacing:.08em;font-weight:800;color:#059669;margin:0 0 8px;">THE AD THEY SEE</p>'
        . $seen
        . '</div></div>';
    return dm_page($title, $inner, true);
}

function marketing_chapters(): array
{
    require_once __DIR__ . '/seed_marketing_b.php';
    require_once __DIR__ . '/seed_marketing_c.php';
    require_once __DIR__ . '/seed_marketing_d.php';
    return array_merge(
        marketing_chapters_a(),
        marketing_chapters_b(),
        marketing_chapters_c(),
        marketing_chapters_d()
    );
}

function marketing_chapters_a(): array
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
            'title' => 'Digital marketing is a promise a person can find',
            'keywords' => 'digital marketing, promise, audience, offer',
            'lead' => 'Digital marketing is how a real person finds a true promise, understands it, and takes one next step. Milk List is the example we will keep: a shared shopping list for a household, Rs 499 a year. The job is not to shout. The job is to be found by the person who buys the milk, and to say something that person can check.',
            'heading' => 'A person, a promise, a next step',
            'para' => 'A poster on a road can be marketing. A page, a search result, a short post, and a letter in the inbox are the same job on a screen. Each one still needs a person, a promise, and a place to go next. If any of those three is missing, you have activity and no marketing. The rest of this course is those three pieces, named one at a time, for Milk List.',
            'bullets' => [
                'The person is specific. "Everyone who eats" is not a person. "The adult who buys for a house of four" is.',
                'The promise is checkable. "Never forget the milk again" needs the product to actually show one shared list.',
                'The next step is one action: start the list, reserve a seat, or reply. A page with five equal buttons has no next step.',
                'Channels change. The job does not. Search, a post, and an email are rooms. The promise in each room should still match.',
            ],
            'visual' => tf_flow('mkjob', 'The job in three boxes', [['person', 'who buys milk'], ['promise', 'one shared list'], ['next step', 'start the list']], 'If you cannot name all three, you are not ready to post.'),
            'rows' => [
                ['Person', 'Who it is for', 'The household buyer.'],
                ['Promise', 'What changes', 'One list, no double milk.'],
                ['Proof', 'Why believe it', 'A real household, later.'],
                ['Place', 'Where they land', 'One page you own.'],
                ['Step', 'What they do', 'Start the list.'],
                ['Number', 'How you know', 'Lists started this week.'],
            ],
            'e1' => ['Example 1 — the promise on a page', 'One person, one promise, one button. This is the whole job on a screen.', dm_page('Milk List', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#059669;font-weight:700;">FOR THE PERSON WHO BUYS</p>
<h1 style="font-size:28px;margin:8px 0;">One list the whole house can see</h1>
<p>Milk, bread, and the rest. Add it once. The shopper sees it on their phone.</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — activity with no job', 'Three boasts and no person. A visitor cannot tell what to do.', dm_page('Milk List promo', <<<'HTML'
<h1 style="font-size:28px;margin:0 0 8px;">We are the best and the fastest</h1>
<p>Award winning. Next generation. Loved by thousands.</p>
<p>
  <a href="#a" style="margin-right:8px;">Learn</a>
  <a href="#b" style="margin-right:8px;">Watch</a>
  <a href="#c">Explore</a>
</p>
HTML)],
            'micro' => [
                'A channel is a room. It is not the promise.',
                'If the product cannot do the thing the line says, change the line.',
                'Count the next step. Feelings are not a result.',
            ],
            'quiz' => [
                $q('What three things does a marketing piece need?', 'A person, a promise, and a next step', 'A logo, a slogan, and a font', 'A viral post, a hashtag, and a dance', 'A long page, a popup, and a timer'),
                $q('Which line is a person you can write to?', 'The adult who buys for a house of four', 'Everyone on the internet', 'People who like quality', 'All households worldwide'),
                $q('What makes a promise usable?', 'The product can actually do it', 'It uses a louder adjective', 'It mentions being the best', 'It fits in a hashtag'),
            ],
            'practice' => 'For a product you know, write one sentence that names the person, the promise, and the single next step. If you cannot name the person, stop and choose a smaller group.',
        ]),
        $L([
            'section' => 'The job',
            'title' => 'Owned, earned, and paid are three different rooms',
            'keywords' => 'owned media, earned media, paid media',
            'lead' => 'Attention arrives in three rooms. Owned is the place you control: your site, your list, your profile. Earned is what other people say when you did not pay them: a mention, a review, a share. Paid is attention you rent: an ad. Milk List needs all three, and they are not interchangeable.',
            'heading' => 'Rent, reputation, and a home',
            'para' => 'An ad stops the day you stop paying. A post on someone else\'s platform can be hidden by that platform tomorrow. The page on your site and the list of people who asked to hear from you are the rooms you can still open. Earned attention is the strongest and the least controllable: you earn it by being useful and honest, and you do not buy fake versions of it.',
            'bullets' => [
                'Owned: the website, the email list, a profile you can edit. You decide the words.',
                'Earned: a review, a reply, a journalist, a neighbor who mentions you. You cannot order it.',
                'Paid: search ads, social ads, a sponsored placement. You rent the visit.',
                'Send paid and earned visits to an owned page. If the rented click has nowhere true to land, the money is gone.',
            ],
            'visual' => tf_panels('mkrooms', 'Three rooms', 'You keep', ['Your page', 'Your email list', 'Your offer'], 'You rent or earn', ['An ad this week', 'A review', 'A share'], 'Paid stops when the bill stops. The page remains.'),
            'rows' => [
                ['Owned', 'You control it', 'Site and email list.'],
                ['Earned', 'Others say it', 'Review or mention.'],
                ['Paid', 'You rent it', 'An ad.'],
                ['Home base', 'Where clicks go', 'One page you own.'],
                ['Risk', 'What can vanish', 'A rented feed.'],
                ['Rule', 'Do not fake earned', 'No bought reviews.'],
            ],
            'e1' => ['Example 1 — the owned room', 'This page is yours. The button does not depend on a feed.', dm_page('Milk List home', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#059669;font-weight:700;">OWNED PAGE</p>
<h1 style="font-size:28px;margin:8px 0;">The list lives here</h1>
<p>Rs 499 a year. The household shares one list. This page stays when an ad ends.</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — label the room before you spend', 'A small map stops you treating a rented post as a home.', dm_page('Where attention sits', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 12px;">This week for Milk List</h1>
<p><strong>Owned.</strong> The page and the welcome letter.</p>
<p><strong>Earned.</strong> Ask two real households for a sentence, and publish it only if they agree.</p>
<p><strong>Paid.</strong> A small ad that repeats the same promise and lands on the owned page.</p>
HTML)],
            'micro' => [
                'A profile on a platform is only partly owned. The platform can change the rules.',
                'A bought review is not earned. It is a false claim.',
                'Paid is a test of the page. It is not a substitute for the page.',
            ],
            'quiz' => [
                $q('Which room do you still control after you stop paying?', 'The page and the email list you own', 'The ad auction', 'Someone else\'s front page', 'A bought review'),
                $q('What is earned attention?', 'A mention or review you did not pay for', 'Any visit that came from an ad', 'A post you boosted', 'A list you purchased'),
                $q('Where should a paid click land?', 'On a page you own that keeps the promise', 'On a blank profile', 'On a page with a different offer', 'On a page that only says "best"'),
            ],
            'practice' => 'Make three lines for a real or imagined offer: one owned place, one earned action you will not fake, and one paid test that sends people to the owned place.',
        ]),
        $L([
            'section' => 'The job',
            'title' => 'A person moves from stranger to customer',
            'keywords' => 'customer journey, awareness, consideration, conversion, retention',
            'lead' => 'A stranger does not buy in the same sentence that introduces you. They move. First they notice. Then they check. Then they act. Then, if you kept the promise, they come back. Milk List has a different job at each step. The first job is not "pay Rs 499".',
            'heading' => 'See, trust, act, return',
            'para' => 'See: the buyer learns that a shared list exists. Trust: they compare it with a paper list or a chat full of "did you get milk?". Act: they start the list. Return: next month they are still using it, or they tell the house next door. A post that asks for the sale on the first glance is doing the last job too early. A page that only introduces, with no way to start, stalls a person who was ready.',
            'bullets' => [
                'See needs a clear name and a picture of the problem. It does not need the full price argument.',
                'Trust needs proof, a plain price, and a reason this is for them.',
                'Act needs one step and very little to fill in.',
                'Return needs a second message: how to use it this week, not the same introduction again.',
            ],
            'visual' => tf_flow('mkmove', 'Four steps, four jobs', [['see', 'the problem'], ['trust', 'why this'], ['act', 'start list'], ['return', 'use it again']], 'Match the message to the step they are actually on.'),
            'rows' => [
                ['See', 'Notice the problem', 'Milk gets forgotten.'],
                ['Trust', 'Check the fit', 'Price and a real line.'],
                ['Act', 'Take the step', 'Start the list.'],
                ['Return', 'Keep the habit', 'A note next week.'],
                ['Too early', 'Sale on first glance', 'They do not know you.'],
                ['Too late', 'Intro to a ready buyer', 'They wanted the button.'],
            ],
            'e1' => ['Example 1 — a first glance that only introduces', 'This post does the "see" job. The link is there. The price essay is not.', dm_page('A first glance', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#d97706;font-weight:700;">SEE</p>
<h1 style="font-size:26px;margin:8px 0;">Did two people buy milk today?</h1>
<p>A shared list means the house sees one list, not four chats.</p>
<p><a href="#page" style="color:#059669;font-weight:700;">See how the list works</a></p>
HTML)],
            'e2' => ['Example 2 — a page for someone who is ready', 'The price and the button are here because this person already came to check.', dm_page('Ready to start', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#059669;font-weight:700;">ACT</p>
<h1 style="font-size:26px;margin:8px 0;">Start the household list</h1>
<p>Rs 499 a year. You can leave whenever you want.</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'micro' => [
                'Name the step before you write. See, trust, act, or return.',
                'The second purchase hears a different sentence than the first visit.',
                'A journey is a model. Real people skip and loop. Still write for one step at a time.',
            ],
            'quiz' => [
                $q('What is the first job with a stranger?', 'Help them notice the problem', 'Demand the payment', 'Ask for a review', 'Offer a referral prize'),
                $q('Which page fits a person who is ready?', 'A clear price and one button', 'Only a slogan', 'Four competing offers', 'A history of the company'),
                $q('What does the return step need?', 'A message about using it again', 'The same introduction forever', 'A louder adjective', 'A new logo'),
            ],
            'practice' => 'Write four short lines for Milk List, or for your own offer: one for see, one for trust, one for act, and one for return. Do not reuse the same sentence.',
        ]),
        $L([
            'section' => 'The job',
            'title' => 'One audience is sharper than everyone',
            'keywords' => 'target audience, segment, who it is for',
            'lead' => 'Everyone is not an audience. An audience is a group that shares a situation. For Milk List the useful group is not "India" and not "people who eat". It is adults who buy groceries for a shared household and who are tired of asking "who is getting the milk?".',
            'heading' => 'Cut until the sentence fits',
            'para' => 'A sharp audience makes the headline obvious. A dull audience forces you into words like "quality" and "solutions". You can serve more people later. The first page should sound like it was written for one group. If a student living alone and a restaurant buyer both "fit", they do not fit the same page. Write the page for one.',
            'bullets' => [
                'Situation beats a vague interest. "Buys for a house of four" is a situation. "Likes convenience" is not.',
                'Say who it is not for. Milk List is a weak fit for a person who shops only for themselves and enjoys that.',
                'One page, one audience. A second audience can have a second page later.',
                'You may learn you picked the wrong group. That is a finding. Change the group, do not add everybody back.',
            ],
            'visual' => tf_split('mkaud', 'Is this the buyer?', 'Buys for a house?', 'Yes, write to them', 'One shared list', 'No, not this page', 'A different offer', 'A sharp no keeps the yes clear.'),
            'rows' => [
                ['Audience', 'Shared situation', 'Buys for the house.'],
                ['Not for', 'Honest exclusion', 'Solo shoppers.'],
                ['Place', 'Where they already are', 'Phone, after work.'],
                ['Pain', 'The repeated moment', 'Double milk, or none.'],
                ['Page', 'One group', 'Do not mix buyers.'],
                ['Later', 'A second group', 'A second page.'],
            ],
            'e1' => ['Example 1 — a page that knows who it is for', 'The first line excludes people on purpose. The right reader feels seen.', dm_page('For the household buyer', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#059669;font-weight:700;">IF YOU BUY FOR THE HOUSE</p>
<h1 style="font-size:26px;margin:8px 0;">Stop asking who is getting the milk</h1>
<p>One list for a shared kitchen. Not a tool for a person who shops only for themselves.</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the household list</a></p>
HTML)],
            'e2' => ['Example 2 — a page for everyone says nothing', 'No situation, so the line could belong to any product.', dm_page('For everyone', <<<'HTML'
<h1 style="font-size:28px;margin:0 0 8px;">The smart choice for all</h1>
<p>Perfect for families, students, offices, shops, and anyone who wants more.</p>
<p><a href="#go" style="color:#059669;font-weight:700;">Get started now</a></p>
HTML)],
            'micro' => [
                'If the line still works after you swap in a different product, it is too vague.',
                'Exclusion is kind. It saves the wrong person a click.',
                'Demographics are optional. The situation is not.',
            ],
            'quiz' => [
                $q('Which is a usable audience for Milk List?', 'Adults who buy groceries for a shared house', 'Everyone who eats food', 'All internet users', 'People who like quality'),
                $q('Why name who it is not for?', 'So the right reader trusts the fit', 'So the page looks exclusive and expensive', 'So you can refuse refunds', 'So search engines rank you faster'),
                $q('What should you do for a second, different buyer?', 'Give them their own page later', 'Add them to the same headline', 'Call the audience everyone', 'Hide the price'),
            ],
            'practice' => 'Write the audience in one sentence, then write one group you will not speak to on this page. Read both aloud.',
        ]),
        $L([
            'section' => 'The job',
            'title' => 'A persona is a named buyer, not a demographic dump',
            'keywords' => 'buyer persona, named customer, situation',
            'lead' => 'A persona is a single imagined buyer you can speak to, built from real situations you have heard. Give them a name so your writing stays concrete. Asha buys for a house of four, on her phone, after work. She is not a pile of age ranges. If you have never heard a sentence like hers, do not invent a novel. Go listen, then write.',
            'heading' => 'One name keeps the writing honest',
            'para' => 'The useful card is short. Name. Situation. The sentence they already say. What they have tried. What they will not tolerate. Asha says "I bought milk and so did he." She has tried a family chat and it scrolls away. She will not tolerate a form that asks for her office address. When a headline could not be read to Asha without embarrassment, it is the wrong headline.',
            'bullets' => [
                'Use a name you can say. "Persona 1, females 25 to 34" does not tell you the sentence.',
                'Quote the problem in their words, not in your category language.',
                'Note the tool they use now. The chat app is Milk List\'s real rival, not a brand slogan.',
                'Mark the card as a hypothesis. Replace a detail when a real buyer contradicts it.',
            ],
            'visual' => tf_flow('mkper', 'Asha on one card', [['name', 'Asha'], ['moment', 'after work'], ['words', 'milk twice'], ['limit', 'short form']], 'If a line would sound silly read to Asha, cut it.'),
            'rows' => [
                ['Name', 'So you can speak', 'Asha.'],
                ['Situation', 'The repeated moment', 'Buys for four.'],
                ['Quote', 'Their words', 'Bought milk twice.'],
                ['Current fix', 'What they use now', 'A family chat.'],
                ['Limit', 'What they refuse', 'A long form.'],
                ['Status', 'Still a guess', 'Update from real talks.'],
            ],
            'e1' => ['Example 1 — a card you can write from', 'Four lines. Enough to judge a headline.', dm_page('Asha', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#059669;font-weight:700;">PERSONA CARD</p>
<h1 style="font-size:26px;margin:8px 0;">Asha, household buyer</h1>
<p>After work, on her phone. She says: "I bought milk and so did he."</p>
<p>She uses the family chat today. She will leave if the form is long.</p>
HTML)],
            'e2' => ['Example 2 — a dump you cannot write from', 'Ranges without a moment produce empty lines.', dm_page('Persona dump', <<<'HTML'
<h1 style="font-size:26px;margin:0 0 8px;">Segment A</h1>
<p>Age 25 to 44. Urban. Income mid. Interests: family, food, lifestyle, deals, quality.</p>
<p>Goal: engage and convert.</p>
HTML)],
            'micro' => [
                'One persona for the first page. A second name needs a second page.',
                'Do not paste a stock photo story you have not heard.',
                'The rival is often the current habit, not a famous brand.',
            ],
            'quiz' => [
                $q('What belongs on a persona card?', 'A situation and a sentence they say', 'Only an age range and an income band', 'A list of hobbies from a template', 'The word engage'),
                $q('Why give the buyer a name?', 'So you can hear whether a line sounds human', 'So the brand looks friendly in a logo', 'So ads can follow that real person', 'So you skip talking to customers'),
                $q('What should you do when a real buyer disagrees with the card?', 'Change the card', 'Keep the card and argue', 'Add every new detail to the same headline', 'Delete the audience'),
            ],
            'practice' => 'Write a six-line card: name, situation, quote, current habit, refusal, and the word "hypothesis" at the bottom. Then write one headline only Asha would care about.',
        ]),
        $L([
            'section' => 'The job',
            'title' => 'The offer is what they get and what they give',
            'keywords' => 'offer, price, risk, value',
            'lead' => 'An offer is a trade. They get a shared list for a year. They give Rs 499, a few minutes, and a little trust. If you describe only your side, you do not have an offer. You have a wish. Put both sides in one breath: what they get, what they pay, how fast it starts, and what happens if it is wrong for them.',
            'heading' => 'Both sides of the trade',
            'para' => 'Milk List\'s offer can be said aloud: "One shared household list for a year, Rs 499, you start today, and you can leave when you want." That is complete. "A revolutionary list experience" is not an offer, because nobody can tell what they give or what they get. A guarantee you cannot honor is not generosity. It is a claim. Offer only the exit you will actually give.',
            'bullets' => [
                'Get: the outcome, not the feature list. "The house sees one list" beats "cloud sync technology".',
                'Give: money, time, and the facts you ask for. A long form raises the price even when the rupee price stays.',
                'Risk: how they leave, in one honest line. Do not invent a promise your support cannot keep.',
                'Who it is not for stays in the offer. A good offer repels the wrong buyer.',
            ],
            'visual' => tf_panels('mkofr', 'The trade', 'They get', ['One shared list', 'A year of use', 'A way to leave'], 'They give', ['Rs 499', 'A few minutes', 'A name and email'], 'Say both sides in one breath before you design the ad.'),
            'rows' => [
                ['Get', 'The outcome', 'One list for the house.'],
                ['Give', 'Price and effort', 'Rs 499 and a short form.'],
                ['Start', 'How soon', 'Today, on a phone.'],
                ['Risk', 'The exit', 'Leave when you want.'],
                ['Not for', 'The wrong fit', 'A solo shopper.'],
                ['Test', 'Say it aloud', 'One breath, no jargon.'],
            ],
            'e1' => ['Example 1 — the trade in one card', 'Outcome, price, start, and exit are all visible.', dm_page('The Milk List offer', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#059669;font-weight:700;">THE OFFER</p>
<h1 style="font-size:26px;margin:8px 0;">One shared list for a year</h1>
<p>Rs 499. Start on your phone today. Leave when you want.</p>
<p>For a household that shops together. Not for a person who shops alone.</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — a wish dressed as an offer', 'No price, no outcome, no exit. The visitor cannot decide.', dm_page('A wish', <<<'HTML'
<h1 style="font-size:28px;margin:0 0 8px;">Unlock seamless household synergy</h1>
<p>Premium features. Smart insights. Join the movement.</p>
<p><a href="#go" style="color:#059669;font-weight:700;">Learn more</a></p>
HTML)],
            'micro' => [
                'A discount is not an offer. It is a change to the give side. The get side must still be clear.',
                'Time is part of the price. Ten form fields can cost more than Rs 499.',
                'If you cannot say the offer in one breath, the page will ramble.',
            ],
            'quiz' => [
                $q('What is an offer?', 'What they get and what they give', 'A slogan about quality', 'A logo and a color', 'A list of features with no price'),
                $q('Which line is a complete Milk List offer?', 'One shared list for a year, Rs 499, leave when you want', 'Revolutionary list experience', 'Best app for families', 'Synergy for modern homes'),
                $q('What is wrong with a guarantee you cannot keep?', 'It is a false claim', 'It is too generous to be marketing', 'It hurts the design', 'It makes the page too short'),
            ],
            'practice' => 'Write your offer in one breath: get, price, how soon it starts, and the exit you will really honor. Then mark who it is not for.',
        ]),
        $L([
            'section' => 'The message',
            'title' => 'Positioning is the space you claim',
            'keywords' => 'positioning, category, alternative',
            'lead' => 'Positioning is the space you choose to stand in, next to a real alternative. Milk List is not "a lifestyle brand". It is the shared list you use instead of a family chat that scrolls away. The alternative is part of the position. Without it, you are floating.',
            'heading' => 'For whom, instead of what',
            'para' => 'A position fits in one sentence: for the person who buys for the house, Milk List is the shared list, instead of a chat, so the milk is bought once. That sentence decides the headline, the comparison you are willing to make, and the comparisons you refuse. You do not have to attack the chat. You have to be clear about the difference: a chat moves; a list stays.',
            'bullets' => [
                'Name the alternative the buyer already uses. That is the real category.',
                'Name the difference in their words. "The list stays on the screen" beats "asynchronous collaboration".',
                'Refuse extra spaces. Milk List is not also a recipe site on the same page.',
                'If a competitor owns "cheapest", you may own "one list for the house". Do not claim both unless both are true.',
            ],
            'visual' => tf_split('mkpos', 'Which space?', 'Instead of a chat?', 'List that stays', 'Bought once', 'A recipe site too', 'A muddled page', 'One alternative keeps the position sharp.'),
            'rows' => [
                ['For', 'The buyer', 'Household shopper.'],
                ['Is', 'The category', 'A shared list.'],
                ['Instead', 'The habit now', 'A family chat.'],
                ['Difference', 'Why switch', 'The list stays.'],
                ['Refuse', 'Not this page', 'Recipes, deals, news.'],
                ['Check', 'One sentence', 'Readable to Asha.'],
            ],
            'e1' => ['Example 1 — a position you can repeat', 'The alternative is named. The difference is concrete.', dm_page('Instead of the chat', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#059669;font-weight:700;">INSTEAD OF A CHAT THAT SCROLLS</p>
<h1 style="font-size:26px;margin:8px 0;">A list that stays on the screen</h1>
<p>For the person who buys for the house. The milk is added once. It does not disappear up the chat.</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — no alternative, no space', 'Nothing to stand next to, so the line could be any app.', dm_page('No position', <<<'HTML'
<h1 style="font-size:28px;margin:0 0 8px;">The future of household living</h1>
<p>Smart. Simple. Powerful. Made for you.</p>
<p><a href="#go" style="color:#059669;font-weight:700;">Discover</a></p>
HTML)],
            'micro' => [
                'Positioning is a choice of space. It is not a tagline contest.',
                'The alternative can be a habit. Paper and chat count.',
                'A page that claims every space claims none.',
            ],
            'quiz' => [
                $q('What does a position need besides the product?', 'The alternative the buyer uses now', 'A louder adjective', 'A new logo', 'Five audiences'),
                $q('Which is a position for Milk List?', 'The shared list instead of a scrolling chat', 'The future of living', 'Best in class platform', 'Smart and simple'),
                $q('What should the first page refuse?', 'Jobs that are not this offer', 'Any mention of price', 'The name of the buyer', 'The next step'),
            ],
            'practice' => 'Fill this in and stop: For [person], this is [category], instead of [real alternative], so that [concrete difference].',
        ]),
        $L([
            'section' => 'The message',
            'title' => 'One sentence should say who, what, and why you',
            'keywords' => 'value proposition, one sentence, message',
            'lead' => 'The value sentence is the position made speakable. Who it is for, what they get, and why it is you rather than the habit they have. One sentence. Milk List: "For a household buyer, one shared list that stays put, so the milk is bought once." You will reuse this sentence everywhere.',
            'heading' => 'A sentence you can paste',
            'para' => 'Write it before the page, the post, and the ad. If the sentence needs a paragraph of jargon, it is not ready. Test it on someone who does not work on it. They should be able to say back who it is for and what it does. "Why you" is the difference, not a compliment you pay yourself. "Because we care" is not a why.',
            'bullets' => [
                'Who, what, why you. If one of the three is missing, the sentence is a slogan.',
                'Use buyer words. "List" and "milk" belong. "Solution" and "leverage" do not.',
                'Keep a single version for a month. Changing it daily means you are still guessing in public.',
                'Every channel may shorten it. None may contradict it.',
            ],
            'visual' => tf_flow('mksent', 'Three parts, one line', [['who', 'household'], ['what', 'a list'], ['why you', 'it stays']], 'Shorten it for a post. Do not change the meaning.'),
            'rows' => [
                ['Who', 'The person', 'Household buyer.'],
                ['What', 'The outcome', 'Milk bought once.'],
                ['Why you', 'The difference', 'The list stays.'],
                ['Test', 'A stranger repeats it', 'Without your help.'],
                ['Ban', 'Empty praise', 'We care, best, smart.'],
                ['Use', 'Paste it', 'Page, post, ad, letter.'],
            ],
            'e1' => ['Example 1 — the sentence on the page', 'Who, what, and why you are all in the first screen.', dm_page('One sentence', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#059669;font-weight:700;">FOR A HOUSEHOLD BUYER</p>
<h1 style="font-size:26px;margin:8px 0;">One shared list that stays put</h1>
<p>So the milk is bought once, not lost in the family chat.</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — praise without a who or a what', 'Nobody can repeat what this does.', dm_page('Praise', <<<'HTML'
<h1 style="font-size:28px;margin:0 0 8px;">Because we care more</h1>
<p>The smartest, simplest, most loved way to live better.</p>
<p><a href="#go" style="color:#059669;font-weight:700;">Join</a></p>
HTML)],
            'micro' => [
                'A stranger should repeat the sentence without you in the room.',
                'Why you is a difference, not a compliment.',
                'One sentence for the month. Variants may be shorter, not different.',
            ],
            'quiz' => [
                $q('What three parts does the value sentence need?', 'Who, what, and why you', 'Logo, color, and font', 'Vision, mission, and values poster', 'Click, like, and share'),
                $q('Which line has all three?', 'For a household buyer, a list that stays, so milk is bought once', 'Because we care more', 'The smartest way to live', 'Join the movement'),
                $q('What may a channel do to the sentence?', 'Shorten it without changing the meaning', 'Swap in a new audience', 'Replace the outcome with "best"', 'Remove the person'),
            ],
            'practice' => 'Write the sentence. Read it to someone who does not know the product. Ask them to say back who it is for and what it does. Fix the sentence, not the listener.',
        ]),
        $L([
            'section' => 'The message',
            'title' => 'Tone is a choice you can repeat',
            'keywords' => 'brand voice, tone, writing choice',
            'lead' => 'Tone is how the sentence sounds, once the meaning is fixed. Milk List sounds like a practical neighbor: short, concrete, no jokes at the buyer\'s expense, no corporate fog. A tone guide is a few choices you can repeat on a tired afternoon. It is not a mood board.',
            'heading' => 'Choices, not a personality quiz',
            'para' => 'Pick three pairs and stick to them. Concrete over abstract. Calm over urgent, unless the urgency is real. Plain over clever. Then write two samples of the same fact: the price is Rs 499. In tone: "Rs 499 a year." Out of tone: "For less than your daily coffee, unlock a paradigm." Anyone on the team should be able to reject the second one without a meeting.',
            'bullets' => [
                'Write the meaning first. Tone does not rescue an empty sentence.',
                'Three rules are enough. More than that and nobody uses the guide.',
                'Show a yes and a no for the same fact. Rules without samples get ignored.',
                'Urgent is allowed when it is true. "Only 12 seats" is tone if there are 12 seats. It is a lie if there are not.',
            ],
            'visual' => tf_panels('mktone', 'Same fact', 'In tone', ['Rs 499 a year', 'Leave when you want', 'The list stays'], 'Out of tone', ['Unlock a paradigm', 'Act before it is too late', 'Synergy'], 'Tone is a choice you can apply to one fact.'),
            'rows' => [
                ['Concrete', 'Name the thing', 'Milk, list, Rs 499.'],
                ['Calm', 'No fake rush', 'Urgency must be true.'],
                ['Plain', 'Neighbor voice', 'No jargon.'],
                ['Yes sample', 'The price', 'Rs 499 a year.'],
                ['No sample', 'Same price', 'Unlock a paradigm.'],
                ['Use', 'Reject in one look', 'No meeting required.'],
            ],
            'e1' => ['Example 1 — practical and repeatable', 'Short lines. The price is a fact, not a performance.', dm_page('In tone', <<<'HTML'
<h1 style="font-size:26px;margin:0 0 8px;">Rs 499 a year</h1>
<p>One shared list. Start today. Leave when you want.</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — the same price, wrong sound', 'The meaning is buried under a performance.', dm_page('Out of tone', <<<'HTML'
<h1 style="font-size:26px;margin:0 0 8px;">Unlock a paradigm for less than coffee</h1>
<p>Do not miss this crazy deal. Your family deserves synergy. Act now.</p>
<p><a href="#go" style="color:#059669;font-weight:700;">Grab it</a></p>
HTML)],
            'micro' => [
                'Clever that hides the offer is a failed tone, even if it is funny.',
                'Fake urgency is not a spicy tone. It is a false claim.',
                'Read the line in the voice of a neighbor. If you cringe, rewrite.',
            ],
            'quiz' => [
                $q('What is a usable tone guide?', 'A few choices plus a yes and a no sample', 'A mood board of colors', 'The phrase "be authentic"', 'A list of banned competitors'),
                $q('Which price line matches a calm, concrete tone?', 'Rs 499 a year', 'Unlock a paradigm for less than coffee', 'Crazy deal, act now', 'Synergy awaits'),
                $q('When is urgency allowed?', 'When the limit is real', 'Whenever a post feels quiet', 'On every button', 'Only in ads, never on the page'),
            ],
            'practice' => 'Write three tone rules. Then write the price twice: once inside the rules, once outside. Keep the pair where the team can see it.',
        ]),
        $L([
            'section' => 'The message',
            'title' => 'Proof beats a louder adjective',
            'keywords' => 'social proof, evidence, specifics',
            'lead' => 'An adjective asks to be believed. Proof shows the thing. "Amazing" loses to "Asha\'s house stopped buying milk twice in the first week" — and only if Asha said it and agreed to let you use it. Specific, recent, and relevant beats loud. Invented proof is worse than no proof.',
            'heading' => 'Show a fact a person can check',
            'para' => 'Useful proof for Milk List is a named situation, a number you measured, or a picture of the list itself. "Trusted by thousands" with no source is an adjective in a costume. A review from a restaurant is the wrong proof for a household page. Put the proof next to the claim it supports. A wall of logos the buyer does not know is decoration.',
            'bullets' => [
                'Specific: who, what changed, how soon. Vague praise is not proof.',
                'Permission: use a person\'s words only if they agreed. Do not invent a neighbor.',
                'Relevant: household proof for a household offer. A celebrity who does not shop for the house does not help Asha.',
                'Your own number counts when you explain it. "40 lists started" is proof. "Going viral" is not.',
            ],
            'visual' => tf_split('mkprf', 'Is it proof?', 'Specific and allowed?', 'Use beside claim', 'Who, what, when', 'Vague or invented', 'Leave it off', 'A louder adjective is not evidence.'),
            'rows' => [
                ['Specific', 'Who and what', 'Stopped double milk.'],
                ['Recent', 'This month', 'Not a story from years ago.'],
                ['Relevant', 'Same situation', 'A household, not a cafe.'],
                ['Allowed', 'They agreed', 'No invented names.'],
                ['Number', 'You measured it', '40 lists started.'],
                ['Place', 'Next to the claim', 'Not a distant logo wall.'],
            ],
            'e1' => ['Example 1 — proof next to the promise', 'The line is specific. Publish it only with permission. The sample here is labeled as a sample.', dm_page('Proof beside the promise', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#059669;font-weight:700;">SAMPLE, USE ONLY WITH PERMISSION</p>
<h1 style="font-size:24px;margin:8px 0;">One list the whole house can see</h1>
<p style="background:#f8fafc;border-radius:12px;padding:12px;">"In the first week our house stopped buying milk twice." — Asha, house of four</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — adjectives dressed as proof', 'No who, no what, no source.', dm_page('Loud and empty', <<<'HTML'
<h1 style="font-size:28px;margin:0 0 8px;">Amazing. Trusted. The best.</h1>
<p>Loved by thousands. Five stars. Award winning.</p>
<p><a href="#go" style="color:#059669;font-weight:700;">Join them</a></p>
HTML)],
            'micro' => [
                'A number without a definition is still an adjective. Say what you counted.',
                'Do not buy reviews, followers, or stars. That is a false claim.',
                'No proof yet is allowed. Show the product clearly and skip the costume.',
            ],
            'quiz' => [
                $q('Which line is proof?', 'A named household that stopped buying milk twice, with permission', 'Amazing and trusted', 'Loved by thousands', 'Award winning'),
                $q('When may you quote a customer?', 'When they agreed', 'When the quote would help the ad', 'When you change their words a little', 'When they left a private complaint'),
                $q('What should you do if you have no proof yet?', 'Show the product plainly and skip fake praise', 'Invent a neighbor', 'Buy reviews', 'Say "trusted by thousands"'),
            ],
            'practice' => 'Collect one proof you are allowed to use, or write "none yet". Place it in a sentence next to the promise. If it is not specific, it does not count.',
        ]),
        $L([
            'section' => 'The message',
            'title' => 'A claim you cannot support is a risk',
            'keywords' => 'honest claims, advertising standards, substantiation',
            'lead' => 'A claim is a sentence a careful person could ask you to prove. "Rs 499 a year" is a claim about price. "The best list in India" is a claim about rank. "Only today" is a claim about time. If you cannot show the basis, do not say it. The risk is not only a fine. The risk is a buyer who notices, and a page you have to unwind.',
            'heading' => 'Say the thing you can point to',
            'para' => 'Superlatives need a source and a scope. "Best" among what, measured how, by whom? If you do not have that, use a sentence you can point at: the list stays on the screen, the price is Rs 499, you can leave. Health, money, and "guaranteed results" need even more care. Milk List does not cure anything and does not promise a perfect household. It keeps one list.',
            'bullets' => [
                'Price, time, and numbers must match the page, the ad, and the bill.',
                'Comparisons must be fair and checkable. "Better than a scrolling chat" can be shown. "Better than every app" cannot.',
                'Scarcity must be real. Twelve seats means twelve. A timer that resets is a trick.',
                'If a claim needs a footnote the buyer will never see, rewrite the claim until the footnote is unnecessary.',
            ],
            'visual' => tf_split('mkclm', 'Can you point to it?', 'Show the basis?', 'Say it', 'Price, screen, exit', 'Only a feeling', 'Cut the line', 'Best, guaranteed, and only-today need proof.'),
            'rows' => [
                ['Price', 'Must match', 'Rs 499 everywhere.'],
                ['Rank', 'Needs a source', 'Or do not say best.'],
                ['Time', 'Must be real', 'No resetting timer.'],
                ['Result', 'What you control', 'A list, not a perfect home.'],
                ['Compare', 'A fair pair', 'List versus chat.'],
                ['Rewrite', 'If it needs hiding', 'Say less, truly.'],
            ],
            'e1' => ['Example 1 — claims you can point to', 'Price, behavior, and exit. Each one can be shown.', dm_page('Claims you can show', <<<'HTML'
<h1 style="font-size:26px;margin:0 0 8px;">The list stays on the screen</h1>
<p>Rs 499 a year. Leave when you want. We do not promise a perfect household. We keep one list.</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — claims with nowhere to point', 'Rank, a fake clock, and a result you do not control.', dm_page('Nowhere to point', <<<'HTML'
<h1 style="font-size:26px;margin:0 0 8px;">India\'s best. Only today.</h1>
<p>Guaranteed family happiness. The timer resets if you wait. Doctors are shocked.</p>
<p><a href="#go" style="color:#059669;font-weight:700;">Buy before it vanishes</a></p>
HTML)],
            'micro' => [
                'A true small claim beats a grand one you must whisper.',
                'The ad and the checkout must say the same price.',
                'If legal review would strike the line, the buyer will too.',
            ],
            'quiz' => [
                $q('Which claim can Milk List point to?', 'The list stays on the screen and the price is Rs 499', 'India\'s best app', 'Guaranteed family happiness', 'Only today, timer resets'),
                $q('When is "only 12 seats" acceptable?', 'When there are 12 seats', 'When you want urgency', 'When the timer can reset', 'When the ad needs color'),
                $q('What should you do with a claim that needs a hidden footnote?', 'Rewrite it until the limit is in the sentence', 'Hide the footnote in a footer', 'Put it only in the ad', 'Say it louder'),
            ],
            'practice' => 'List every claim on your page. Next to each, write the thing you can point to. Delete any line with an empty second column.',
        ]),
        $L([
            'section' => 'The message',
            'title' => 'The same message should survive every channel',
            'keywords' => 'message match, channel consistency, one promise',
            'lead' => 'A channel may change the length. It may not change the promise. The post, the ad, the page, and the email should all be recognizable as Milk List for a household buyer: one list that stays, Rs 499, start here. A clever post that lands on a different offer feels like a trick, because it is one.',
            'heading' => 'Shorten, do not swap',
            'para' => 'Make a one-line version, a three-line version, and the full page. The short one is for a post. The middle one is for an ad or a subject plus preview. The page holds proof and the form. Read them in a row. The person, the outcome, and the price should not drift. If the post says "free recipes" and the page sells a list, you bought the wrong click.',
            'bullets' => [
                'Write the page sentence first. Cut it down for the other rooms.',
                'The next step can be named differently, but it must lead to the same offer.',
                'Pictures should show the same object. A dancing clip that hides the list will attract the wrong person.',
                'When a channel forces a format, keep the nouns. List, household, Rs 499.',
            ],
            'visual' => tf_flow('mkch', 'One promise, three lengths', [['page', 'full offer'], ['ad', 'three lines'], ['post', 'one line']], 'If the nouns change, it is a different campaign.'),
            'rows' => [
                ['Page', 'Full version', 'Proof, price, form.'],
                ['Ad', 'Middle version', 'Promise plus page.'],
                ['Post', 'Short version', 'One line and a link.'],
                ['Letter', 'Same nouns', 'List, house, price.'],
                ['Drift', 'A swapped offer', 'Recipes instead of a list.'],
                ['Check', 'Read them in a row', 'Person and price match.'],
            ],
            'e1' => ['Example 1 — three lengths, same nouns', 'Post, ad, and page could sit in one column and agree.', dm_page('Same message', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#059669;font-weight:700;">POST</p>
<p>One list for the house. The milk is bought once.</p>
<p style="font-size:12px;letter-spacing:.08em;color:#0284c7;font-weight:700;">AD</p>
<p>A shared list that stays put. Rs 499 a year.</p>
<p style="font-size:12px;letter-spacing:.08em;color:#d97706;font-weight:700;">PAGE</p>
<h1 style="font-size:24px;margin:8px 0;">One shared list that stays put</h1>
<p>Rs 499 a year. Start today.</p>
HTML)],
            'e2' => ['Example 2 — the click was for a different promise', 'The post sold recipes. The page sells a list. That is a broken match.', dm_page('Broken match', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#e11d48;font-weight:700;">POST</p>
<p>Free recipes for busy nights. Tap for the cookbook.</p>
<p style="font-size:12px;letter-spacing:.08em;color:#e11d48;font-weight:700;">PAGE THEY LAND ON</p>
<h1 style="font-size:24px;margin:8px 0;">Buy a shared shopping list</h1>
<p>Rs 499. No recipes here.</p>
HTML)],
            'micro' => [
                'Message match is a respect. The click was for the sentence you showed.',
                'Design can change. The nouns should not.',
                'If a channel cannot say the truth, do not use that channel for this offer.',
            ],
            'quiz' => [
                $q('What may change from page to post?', 'The length', 'The audience', 'The price', 'The product'),
                $q('What is a broken message match?', 'A recipe post that lands on a list for sale', 'A shorter sentence with the same nouns', 'An ad that repeats the page headline', 'A letter that names Rs 499'),
                $q('Which words should survive every channel?', 'The person, the outcome, and the price', 'A new joke each day', 'Whatever is trending', 'A different offer per network'),
            ],
            'practice' => 'Write the page sentence, then a three-line ad, then a one-line post. Underline the nouns. If a noun appears in only one of them, fix the set.',
        ]),
        $L([
            'section' => 'The page',
            'title' => 'The website is the place you send people',
            'keywords' => 'website, home page, owned page',
            'lead' => 'The website is the owned room every other room should be able to point at. For a small offer, that can be a single page. Milk List does not need a maze. It needs a page that states the promise, shows the list, names the price, and lets Asha start. Extra pages are for extra jobs, not for the feeling of being a big company.',
            'heading' => 'A home, not a maze',
            'para' => 'Decide the one URL you will paste into a profile, an ad, and a letter. That URL must load the promise without an account wall in front of the explanation. A visitor who must sign in before they can read what it is will leave. Navigation is a set of exits. On the page that asks for the start, every extra exit competes with the start.',
            'bullets' => [
                'One primary URL. Do not send ads to the homepage if the homepage is a menu.',
                'The explanation is public. The account can come after they understand.',
                'Each extra page needs its own job: how it works, the price details, a contact. No empty "coming soon".',
                'The page must be reachable on a phone, because Asha is on a phone.',
            ],
            'visual' => tf_flow('mkweb', 'Every room points home', [['post', 'a link'], ['ad', 'a link'], ['letter', 'a link'], ['page', 'the offer']], 'If the link opens a menu, you sent them to a hallway.'),
            'rows' => [
                ['Primary URL', 'The one you paste', 'The offer page.'],
                ['Public', 'Readable first', 'No wall before the promise.'],
                ['Start', 'The action', 'After they understand.'],
                ['Extra pages', 'Each has a job', 'How, price, contact.'],
                ['Nav', 'A set of exits', 'Few, on this page.'],
                ['Phone', 'Where Asha is', 'The page must work there.'],
            ],
            'e1' => ['Example 1 — a home with one job', 'Promise, price, and the next step. Little else.', dm_page('Milk List', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#059669;font-weight:700;">MILK LIST</p>
<h1 style="font-size:26px;margin:8px 0;">One shared list for the house</h1>
<p>It stays on the screen. Rs 499 a year.</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
<p style="font-size:14px;"><a href="#how" style="color:#475569;">How it works</a></p>
HTML)],
            'e2' => ['Example 2 — a hallway', 'Menus and no promise. The visitor has to guess.', dm_page('A hallway', <<<'HTML'
<h1 style="font-size:26px;margin:0 0 8px;">Welcome to our platform</h1>
<p><a href="#a">About</a> · <a href="#b">Blog</a> · <a href="#c">Careers</a> · <a href="#d">Partners</a> · <a href="#e">Login</a></p>
<p>Please browse our set of offerings.</p>
HTML)],
            'micro' => [
                'Send people to the page that matches the sentence they clicked.',
                'A blog can exist. It should not be the only door if you want a start.',
                'Login is for people who already have a list, not for people who are deciding.',
            ],
            'quiz' => [
                $q('Where should an ad send Asha?', 'The page that states this offer', 'A homepage that is only a menu', 'A login wall', 'A careers page'),
                $q('When is an extra page justified?', 'When it has its own job', 'When you want the site to look bigger', 'When it says coming soon', 'When it repeats the homepage with no change'),
                $q('What should be public before an account?', 'What the product is and what it costs', 'Nothing, so they must register', 'Only a logo', 'Only a video with no words'),
            ],
            'practice' => 'Write the one URL you would paste into a profile. List what the visitor must understand before any account step. Remove any block that hides that.',
        ]),
        $L([
            'section' => 'The page',
            'title' => 'A landing page has one job',
            'keywords' => 'landing page, one offer, message match',
            'lead' => 'A landing page is a page with one job: help a person who arrived from a specific promise take one step. It is not the whole website squeezed into a scroll. For the Milk List ad, the job is "start the household list". A second offer on that page is a second job, and it splits the visit.',
            'heading' => 'One arrival, one job',
            'para' => 'Match the first screen to the sentence that earned the click. If the ad said "a list that stays put", those words, or a faithful shortening, are the headline. Remove the menu, the unrelated blog, and the other product. Keep proof, the price, and the step. People who want the company story can find it from the footer. People who came to start should not have to hunt.',
            'bullets' => [
                'One headline, one offer, one primary action.',
                'The first screen repeats the promise that earned the click.',
                'Navigation to other products is a leak. Keep it quiet or remove it on this page.',
                'A long page is fine if every block supports the same job. Length is not the problem. A second job is.',
            ],
            'visual' => tf_split('mkland', 'Second job on the page?', 'Yes, it splits', 'Remove it', 'One offer stays', 'No, it supports', 'Keep the block', 'Proof and price support the same start.'),
            'rows' => [
                ['Job', 'The one action', 'Start the list.'],
                ['Match', 'Same promise', 'Words from the ad.'],
                ['Keep', 'Supports the job', 'Proof, price, form.'],
                ['Cut', 'Another offer', 'Recipes, a second app.'],
                ['Nav', 'A leak', 'Quiet or gone.'],
                ['Length', 'Allowed', 'If it is the same job.'],
            ],
            'e1' => ['Example 1 — one job', 'Everything on the card supports starting the list.', dm_page('Start the list', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#059669;font-weight:700;">FROM THE AD: A LIST THAT STAYS</p>
<h1 style="font-size:26px;margin:8px 0;">A list that stays put</h1>
<p>Rs 499 a year for one household. Leave when you want.</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — two jobs', 'The visitor who came for a list is offered a cookbook in the same breath.', dm_page('Two jobs', <<<'HTML'
<h1 style="font-size:26px;margin:0 0 8px;">A list that stays put</h1>
<p><a href="#list" style="display:inline-block;margin:0 8px 8px 0;background:#059669;color:#fff;text-decoration:none;padding:10px 14px;border-radius:10px;">Start the list</a>
<a href="#book" style="display:inline-block;background:#0284c7;color:#fff;text-decoration:none;padding:10px 14px;border-radius:10px;">Buy the cookbook</a></p>
<p>Also see careers, the blog, and our office tour.</p>
HTML)],
            'micro' => [
                'The landing page can be the homepage only if the homepage has that one job.',
                'A second button is allowed when it is a quieter path to the same offer, such as "see the price".',
                'If you run two campaigns, build two pages. Do not merge them to save a file.',
            ],
            'quiz' => [
                $q('How many jobs should a landing page have?', 'One', 'As many as the menu allows', 'One per paragraph, unrelated', 'Zero, just a video'),
                $q('What should the first screen repeat?', 'The promise that earned the click', 'A new offer', 'The company timeline', 'A popup'),
                $q('When is a long page acceptable?', 'When every block supports the same job', 'When it contains every product', 'When it hides the price', 'When it has three primary buttons'),
            ],
            'practice' => 'Pick one campaign sentence. List every block you would put on the page and write the job of each block. Delete any block whose job is not the campaign\'s job.',
        ]),
        $L([
            'section' => 'The page',
            'title' => 'The headline is the promise above the fold',
            'keywords' => 'headline, above the fold, first screen',
            'lead' => 'The headline is the promise in the first screen, before a person scrolls. On a phone that is a few lines. It has to tell Asha she is in the right place. "Welcome" does not. "One shared list that stays put" does. The subline can add the person and the price. The headline should still make sense alone.',
            'heading' => 'The first lines do the work',
            'para' => 'Write the headline from the value sentence, not from a pun. A pun that hides "list" fails, because a scanner never learns the product. Put the specific noun in the headline. Use the line under it for who it is for. If the first screen is a giant image with no words, the promise is invisible to someone who waits, and invisible to someone who cannot see the image.',
            'bullets' => [
                'The headline names the outcome or the object. List, not "experience".',
                'It should match the ad or post that sent them, close enough to feel like the same sentence.',
                'The first screen also shows the action. A headline with the button below the fold on a phone is half a headline.',
                'One headline. A second huge line that says something else is a second headline.',
            ],
            'visual' => tf_flow('mkhead', 'First screen', [['headline', 'the list stays'], ['line', 'for the house'], ['button', 'start']], 'If the first screen has no noun, it has no promise.'),
            'rows' => [
                ['Headline', 'The promise', 'A list that stays.'],
                ['Subline', 'Who and price', 'House, Rs 499.'],
                ['Alone', 'Still clear', 'No pun required.'],
                ['Match', 'Same as the ad', 'Same nouns.'],
                ['Phone', 'Visible early', 'Headline and button.'],
                ['Avoid', 'Empty welcome', 'No "welcome".'],
            ],
            'e1' => ['Example 1 — a headline with a noun', 'You know the product before you scroll.', dm_page('Headline', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#059669;font-weight:700;">FOR THE HOUSEHOLD BUYER</p>
<h1 style="font-size:28px;margin:8px 0;">One shared list that stays put</h1>
<p>Rs 499 a year. The milk is bought once.</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — a welcome and a pun', 'Neither line says what the thing is.', dm_page('No noun', <<<'HTML'
<h1 style="font-size:28px;margin:0 0 8px;">Welcome, milk-shake of ideas</h1>
<p>Where households become journeys.</p>
<p><a href="#go" style="color:#059669;font-weight:700;">Enter</a></p>
HTML)],
            'micro' => [
                'Read only the headline to a stranger. If they cannot say the product, rewrite it.',
                'Do not put the only copy of the promise inside an image.',
                'Questions can work when they are the buyer\'s question. "Did you buy milk twice?" is hers. "Ready to disrupt?" is yours.',
            ],
            'quiz' => [
                $q('What should a headline contain?', 'The outcome or the object', 'The word welcome', 'A pun that hides the product', 'Only a logo'),
                $q('What else belongs on the first screen of a phone?', 'The action', 'The full company history', 'Three equal offers', 'A video with no words'),
                $q('Which headline matches Milk List?', 'One shared list that stays put', 'Welcome to our platform', 'Milk-shake of ideas', 'Disrupt the kitchen'),
            ],
            'practice' => 'Write five headlines. Cross out any that still work if you replace your product with a different one. Keep the one a stranger can explain.',
        ]),
    ];
}

if (PHP_SAPI === 'cli' && isset($_SERVER['argv'][0]) && realpath((string)$_SERVER['argv'][0]) === realpath(__FILE__)) {
    require_once __DIR__ . '/includes/db.php';
    $chapters = marketing_chapters();
    if (count($chapters) !== 60) {
        fwrite(STDERR, 'expected 60 marketing chapters, got ' . count($chapters) . "\n");
        exit(1);
    }
    track_seed($pdo, marketing_meta(), $chapters);
    track_refresh($pdo, 'digital-marketing-tutorial', $chapters);
    track_place($pdo, 'digital-marketing-tutorial', array_map(static fn(array $ch): string => $ch['title'], $chapters));
    track_sections($pdo, 'digital-marketing-tutorial', [
        6 => 'The job',
        12 => 'The message',
        20 => 'The page',
        30 => 'Search',
        38 => 'Content and social',
        44 => 'Email',
        50 => 'Paid attention',
        56 => 'Measurement',
        59 => 'Trust',
        60 => 'The project',
    ]);
}
