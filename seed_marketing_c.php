<?php
declare(strict_types=1);

function marketing_chapters_c(): array
{
    $L = static function (array $c): array {
        return track_lesson('visual', '', $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };

    return [
        $L([
            'section' => 'Content and social',
            'title' => 'A content calendar is a promise you can keep',
            'keywords' => 'content calendar, planning, cadence',
            'lead' => 'A content calendar is a short list of pieces you have promised to publish, each with a person, a job, a date, and the page it points to. It is not a fantasy of thirty posts a day. For Milk List, one useful piece a week that Asha can use beats a grid full of slogans you will abandon.',
            'heading' => 'Dates you will actually meet',
            'para' => 'Each row needs six facts: the date, the person, the step they are on (see, trust, act, or return), the channel, the one job of the piece, and the owned URL it sends to. If a row has no URL, it has no home. If a week is already full of real work, do not add a post to look busy. Leave a blank. A calendar you keep teaches you what to say next. A calendar you miss teaches the audience nothing except that you vanish.',
            'bullets' => [
                'Plan the next few weeks, not a year of fiction.',
                'Every piece names a step: see, trust, act, or return.',
                'Every piece points at a page you own, even if the point is only "read this explanation".',
                'Review on a set day. Move a date rather than shipping something empty.',
            ],
            'visual' => tf_flow('mkcal', 'One row of the plan', [['date', 'Tuesday'], ['job', 'see'], ['channel', 'a post'], ['page', 'the offer']], 'A row without a page is a post with nowhere to go.'),
            'rows' => [
                ['Date', 'A real day', 'You can keep it.'],
                ['Person', 'Who it is for', 'Asha.'],
                ['Step', 'See to return', 'One of the four.'],
                ['Channel', 'Where it goes', 'Post, letter, page.'],
                ['Job', 'One purpose', 'Show the double milk.'],
                ['URL', 'The home', 'The page you own.'],
            ],
            'e1' => ['Example 1 — a week you can keep', 'Three pieces, each with a job and a home.', dm_page('This week', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Milk List, this week</h1>
<p><strong>Tue.</strong> See. A post about buying milk twice. Link: how a shared list works.</p>
<p><strong>Thu.</strong> Trust. A letter with the price. Link: the offer page.</p>
<p><strong>Sat.</strong> Return. A note to people who started: add this week\'s milk.</p>
HTML)],
            'e2' => ['Example 2 — a grid of wishes', 'No person, no page, no date you could defend.', dm_page('A fantasy grid', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Daily for all platforms</h1>
<p>Monday motivation. Tuesday tips. Wednesday viral. Thursday throwback. Friday free. Weekend surprise. Repeat forever.</p>
HTML)],
            'micro' => [
                'Blank space on the calendar is allowed. Empty posts are not.',
                'Repurpose one idea only after the page exists.',
                'If you miss two weeks, shrink the plan. Do not announce a comeback tour.',
            ],
            'quiz' => [
                $q('What does a useful calendar row contain?', 'A date, a person, a job, a channel, and a page', 'A mood and a hashtag', 'Thirty posts a day', 'Only the word viral'),
                $q('What should you do when the week is already full?', 'Leave the blank', 'Add a slogan so the grid looks busy', 'Copy another brand\'s week', 'Promise daily video'),
                $q('Why does each piece need a URL?', 'So the attention has a home you own', 'So the grid has more columns', 'So the post can avoid a point', 'So you can skip the page'),
            ],
            'practice' => 'Write four rows for the next month. If you cannot name the page for a row, drop the row.',
        ]),
        $L([
            'section' => 'Content and social',
            'title' => 'A post has one job',
            'keywords' => 'social post, one idea, hook',
            'lead' => 'A post is one idea in the place Asha already scrolls. It has one job: help her see, trust, act, or return. The Milk List post that says "did two people buy milk today?" is a see post. It should not also announce a hire, a holiday, and a new logo. One job, then a way to the page if the job needs a next step.',
            'heading' => 'One idea, then the door',
            'para' => 'The first line has to earn the next line. Use her situation, not your announcement voice. Show the list if you can, in a picture with real text beside it so the idea survives without the image. The last line is the door: a link, a reply, or a save. End there. Hashtags are a filing label, not the sentence. A handful that name the topic are enough. A paragraph of tags is noise.',
            'bullets' => [
                'First line: her moment. "Did the house buy milk twice?"',
                'Middle: one proof or one fact. The list stays on the screen.',
                'Last: one door. The page, or a real question she can answer.',
                'Do not buy likes, views, or followers. A rented crowd is not an audience.',
            ],
            'visual' => tf_flow('mkpost', 'Three lines', [['first', 'her moment'], ['middle', 'one fact'], ['last', 'one door']], 'If the post has two announcements, it has no job.'),
            'rows' => [
                ['Job', 'One of four', 'See, trust, act, return.'],
                ['Open', 'Her words', 'Milk bought twice.'],
                ['Middle', 'One fact', 'The list stays.'],
                ['Door', 'One next step', 'The offer page.'],
                ['Tags', 'A few labels', 'Not the sentence.'],
                ['Never', 'Bought applause', 'Likes are not people.'],
            ],
            'e1' => ['Example 1 — one job and a door', 'See the problem, one fact, one link.', dm_page('One post', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#d97706;font-weight:700;">SEE</p>
<h1 style="font-size:24px;margin:8px 0;">Did two people buy milk today?</h1>
<p>A list that stays on the screen means the house sees one list, not four chats.</p>
<p><a href="#page" style="color:#059669;font-weight:700;">See the shared list</a></p>
HTML)],
            'e2' => ['Example 2 — four announcements', 'Nobody can tell what to do.', dm_page('Four jobs', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Big news, huge week</h1>
<p>We rebranded. We are hiring. Festival sale. Also, did you buy milk? Link in bio maybe.</p>
HTML)],
            'micro' => [
                'A question you will not answer is not engagement. Ask only what you will reply to.',
                'The picture should show the list, not a stock kitchen that could sell anything.',
                'If the post\'s job is see, do not demand the payment in the first line.',
            ],
            'quiz' => [
                $q('How many jobs should one post have?', 'One', 'Four, so the grid stays full', 'As many as the caption fits', 'Zero, only hashtags'),
                $q('What belongs in the first line?', 'Her moment', 'A hiring announcement mixed with a sale', 'Thirty hashtags', 'The word huge'),
                $q('What is wrong with buying likes?', 'The crowd is not your audience', 'It is the same as a real reply', 'It improves the product', 'It replaces the page'),
            ],
            'practice' => 'Draft one post with three lines: her moment, one fact, one door. Delete any second announcement.',
        ]),
        $L([
            'section' => 'Content and social',
            'title' => 'Pick the platform where the audience already is',
            'keywords' => 'platform choice, social network, whatsapp',
            'lead' => 'A platform is worth your time when Asha is already there and you can show the offer in that format. Do not join five networks because a checklist said so. Milk List\'s buyer is on her phone after work. A short public post may catch her. A private message is appropriate only if she asked. A professional essay network may be the wrong room for a grocery list.',
            'heading' => 'Go where the behavior already happens',
            'para' => 'Ask three questions. Is she there? Can you show a list and a sentence in that format? Can you answer replies in a human time? If a chat app is where the household already talks, you may invite her to message you after she starts — you may not add yourself to groups she did not invite you into, and you may not harvest numbers from those groups. One platform done well is a channel. Five neglected ones are five closed doors.',
            'bullets' => [
                'Presence of the audience comes before your preference for a tool.',
                'The format must fit the proof. A list can be a picture plus a sentence. It does not need a dance.',
                'Private channels need a yes. Public posts are still public speech.',
                'A partner or a creator is a publisher. Pay for a real, disclosed mention, not for a screenshot of fake reach.',
            ],
            'visual' => tf_split('mkplat', 'Is she already there?', 'Yes, and you can reply', 'Use that room', 'One format', 'No, or you cannot', 'Stay away', 'Do not collect numbers from groups you were not invited to.'),
            'rows' => [
                ['There', 'She already uses it', 'Phone, after work.'],
                ['Format', 'Shows the list', 'Picture and a sentence.'],
                ['Reply', 'You can answer', 'In a human time.'],
                ['Private', 'Needs a yes', 'No harvested numbers.'],
                ['Skip', 'Wrong room', 'A network she is not on.'],
                ['Partner', 'Disclosed mention', 'Real audience only.'],
            ],
            'e1' => ['Example 1 — a room that fits', 'A short public note and a page. No demand to join five networks.', dm_page('One room', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">Where Milk List will show up</h1>
<p>One public place she already scrolls, plus the page, plus email she asked for.</p>
<p>We will not add the brand to household groups.</p>
<p><a href="#page" style="color:#059669;font-weight:700;">Read the page</a></p>
HTML)],
            'e2' => ['Example 2 — a checklist of empty rooms', 'Five logos, no plan to reply, and a grab for numbers.', dm_page('Every logo', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">We are everywhere</h1>
<p>Follow all five. Also send us your group so we can message everyone in it.</p>
HTML)],
            'micro' => [
                'A profile is a small landing page: who it is for, the promise, and the link.',
                'Posting the same words everywhere is fine only when the audience and the format match.',
                'If you cannot reply, do not ask people to comment.',
            ],
            'quiz' => [
                $q('When is a platform worth using?', 'She is already there and you can show the offer and reply', 'It is on a checklist', 'It has the most logos', 'You can harvest a group\'s numbers'),
                $q('What does a private message require?', 'A yes from that person', 'A public post somewhere else', 'A purchased list', 'A hashtag'),
                $q('What are you paying a creator for?', 'A real, disclosed mention', 'A screenshot of fake reach', 'Their password', 'A guarantee of the first rank'),
            ],
            'practice' => 'Name one platform Asha already uses. Write how the list will look there, and write the rule for what you will not do in private groups.',
        ]),
        $L([
            'section' => 'Content and social',
            'title' => 'Organic reach is not a contract',
            'keywords' => 'organic reach, paid reach, algorithm',
            'lead' => 'Organic reach is the attention a platform shows without you paying. It is a favor the platform can revoke. Paid reach is a bill. Neither is a promise that Asha will see you. Milk List should treat organic posts as practice and as a door, and treat the page and the email list as the rooms that remain when the feed goes quiet.',
            'heading' => 'A favor, not a lease',
            'para' => 'Do not build the business on the hope that every follower sees every post. Most will not. Write so that the people who do see it can understand the offer and reach a home you own. Boosting a post is paying to show that post. It does not fix a vague sentence. If you pay, send the visit to the page that matches. Chasing the platform\'s latest trick every week is a distraction from the sentence.',
            'bullets' => [
                'Assume many followers will not see a given post. Say the whole point in the ones they do see.',
                'A paid boost is a test of that post plus the page. It is not a strategy by itself.',
                'Do not buy followers to make the organic number look larger. The feed will still be empty of buyers.',
                'Move willing people toward the list and the page. Those are the assets.',
            ],
            'visual' => tf_panels('mkreach', 'Two kinds of attention', 'Organic', ['Unpaid', 'Can vanish', 'Still needs a point'], 'Paid boost', ['A bill', 'A test', 'Must match the page'], 'The page and the email list outlast both.'),
            'rows' => [
                ['Organic', 'Unpaid showing', 'Not a contract.'],
                ['Paid', 'You rent a showing', 'The sentence still matters.'],
                ['Followers', 'Not viewers', 'Many never see a post.'],
                ['Boost', 'Pays for this post', 'Does not fix vagueness.'],
                ['Fake size', 'Bought accounts', 'Not buyers.'],
                ['Asset', 'What remains', 'Page and email list.'],
            ],
            'e1' => ['Example 1 — a post that survives low reach', 'The whole point is in the few lines someone might see, with a way home.', dm_page('The whole point', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">One list. Milk bought once.</h1>
<p>Rs 499 a year. If you see this, you have the whole offer.</p>
<p><a href="#page" style="color:#059669;font-weight:700;">Start on the page</a></p>
HTML)],
            'e2' => ['Example 2 — a post that needs a lucky feed', 'It teases and withholds. If reach is small, almost nobody gets the point.', dm_page('A tease', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">You will not believe this</h1>
<p>Wait for part 4. Follow so you maybe see it. Link nowhere.</p>
HTML)],
            'micro' => [
                'A reach chart without a next step is a weather report.',
                'Posting more often does not create a contract. It creates more chances to be useful.',
                'If a platform removes the door to your site, that platform is a poor home for the offer.',
            ],
            'quiz' => [
                $q('What is organic reach?', 'Attention the platform shows without a bill, and can revoke', 'A contract that every follower sees you', 'The email list', 'A bought follower count'),
                $q('What does boosting a vague post do?', 'It pays to show a vague post', 'It fixes the sentence', 'It owns the audience', 'It replaces the page'),
                $q('Which assets remain when a feed goes quiet?', 'The page and the email list', 'A bought follower number', 'Yesterday\'s reach chart', 'A tease with no link'),
            ],
            'practice' => 'Take your last post. Could a person who sees only that post tell the offer and find the page? If not, rewrite it so they can.',
        ]),
        $L([
            'section' => 'Content and social',
            'title' => 'Comments are part of the channel',
            'keywords' => 'comments, replies, community management',
            'lead' => 'A comment is a person standing at the door. Replying is part of the channel, not a chore after the real work. Milk List should answer questions about price, privacy, and how the list works, in the same tone as the page. Ignore bait. Do not argue a household into a purchase. Move a useful question toward the page when the page answers it better.',
            'heading' => 'Answer, or do not ask',
            'para' => 'Decide who replies and how fast a real question deserves an answer. A day is a long time on a phone. Correct a factual mistake kindly, once. If someone is abusive, you may hide or remove that comment under a rule you could say in public. Do not invent comments from happy households. A fake thread is a fake claim. Thank a real complaint and take the private detail out of the public thread.',
            'bullets' => [
                'Reply to real questions. Silence after you asked "any questions?" is a closed door.',
                'Use the same facts as the page. Do not invent a discount in a comment.',
                'One correction, in public, for a public factual mistake. Then stop.',
                'No fake comments, no bought replies, no argument as entertainment.',
            ],
            'visual' => tf_split('mkcom', 'A real question?', 'Yes, about the offer', 'Answer plainly', 'Same facts as page', 'Bait or abuse', 'Do not perform', 'Hide abuse. Do not invent friends.'),
            'rows' => [
                ['Question', 'Answer it', 'Price, privacy, how.'],
                ['Speed', 'Human time', 'Not "whenever".'],
                ['Facts', 'Match the page', 'No surprise discount.'],
                ['Complaint', 'Thank, then private', 'No fight in public.'],
                ['Abuse', 'A public rule', 'Remove, do not debate.'],
                ['Fake', 'Never', 'A thread you wrote yourself.'],
            ],
            'e1' => ['Example 1 — a straight answer', 'The price in the reply matches the page.', dm_page('A reply', <<<'HTML'
<p style="font-size:14px;color:#475569;">Asha: What does it cost for one house?</p>
<h1 style="font-size:22px;margin:8px 0;">Milk List: Rs 499 a year for one household.</h1>
<p>You can leave when you want. The details are on the page.</p>
<p><a href="#page" style="color:#059669;font-weight:700;">See the offer</a></p>
HTML)],
            'e2' => ['Example 2 — a performance', 'A fight, a fake friend, and a discount the page does not have.', dm_page('A performance', <<<'HTML'
<p>Brand: You clearly do not value family.</p>
<p>HappyMom99: Best thing ever, five stars, I am definitely real.</p>
<p>Brand: Secret 90 percent off, comment YES.</p>
HTML)],
            'micro' => [
                'A comment is a bad place to collect a password, a payment, or a private address.',
                'If the same question appears three times, the page is missing a sentence. Add it.',
                'Deleting a fair complaint does not delete the feeling. Answer it.',
            ],
            'quiz' => [
                $q('What should a reply about price do?', 'Match the price on the page', 'Invent a secret discount', 'Insult the asker', 'Ignore it after you asked for questions'),
                $q('What is a fake comment?', 'A false claim', 'A real question', 'A link to your price page', 'A correction of a typo'),
                $q('When should the page change because of comments?', 'When the same real question keeps appearing', 'When someone is abusive', 'When you want a fight', 'Never'),
            ],
            'practice' => 'Write replies for three comments: a price question, a fair complaint, and an insult. The insult gets no performance. The other two get facts.',
        ]),
        $L([
            'section' => 'Content and social',
            'title' => 'Short video is a hook, a point, and a next step',
            'keywords' => 'short video, captions, hook',
            'lead' => 'A short video is still one post. It needs a hook in the first moments, one point, and a next step. Many people watch without sound, in a queue, on a phone. Milk List\'s video should show the list on a screen and put the sentence in words on the picture: one list, milk bought once. Sound is extra. The words are not.',
            'heading' => 'Show the thing, write the sentence',
            'para' => 'Open on the result, not on a logo animation. A thumb stopping on "two milks in the fridge" understands the problem before any intro. One point only. Say the next step in the video and in the caption, and make sure the place you send them keeps that promise. Do not use a stolen clip. Do not pretend a scripted scene is an unsolicited review. Captions are part of the edit, not an afterthought.',
            'bullets' => [
                'First moments: the result or the problem. Not a logo.',
                'Words on screen, because sound is often off.',
                'One point. The list stays. Then the door.',
                'Only footage and music you have the right to use. A real customer appears only with a clear yes.',
            ],
            'visual' => tf_flow('mkvid', 'A short video', [['hook', 'two milks'], ['point', 'one list'], ['words', 'on screen'], ['step', 'the page']], 'If it is silent and still makes sense, the edit is ready.'),
            'rows' => [
                ['Hook', 'The result', 'Two milks, or one list.'],
                ['Point', 'One idea', 'The list stays.'],
                ['Words', 'On the picture', 'Sound may be off.'],
                ['Step', 'Same promise', 'The offer page.'],
                ['Rights', 'Yours to use', 'No stolen clips.'],
                ['Person', 'A clear yes', 'Before they appear.'],
            ],
            'e1' => ['Example 1 — the sentence is visible', 'Even with no sound, the card says the point and the step.', dm_page('Video card', <<<'HTML'
<p style="background:#0f172a;color:#fff;border-radius:12px;padding:16px;">
  <span style="font-size:12px;letter-spacing:.08em;color:#6ee7b7;">ON SCREEN</span>
  <span style="display:block;font-size:22px;font-weight:800;margin:8px 0;">Two milks. One house.</span>
  <span style="display:block;">A list that stays. Milk bought once.</span>
</p>
<p><a href="#page" style="color:#059669;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — a logo and a secret', 'Nothing is said. The point is in audio the viewer may never hear.', dm_page('Logo only', <<<'HTML'
<h1 style="font-size:26px;margin:0 0 8px;">MILK LIST</h1>
<p>Three seconds of logo. The offer is only in the voiceover. No captions. No next step.</p>
HTML)],
            'micro' => [
                'Watch it on a phone with the sound off before you publish.',
                'A trend is a format. It is not a reason to hide the product.',
                'Do not urge people to share, follow, and buy in the same three seconds.',
            ],
            'quiz' => [
                $q('What should the opening show?', 'The problem or the result', 'A long logo animation', 'A stolen clip', 'A menu of hashtags'),
                $q('Why put words on the screen?', 'Many people watch without sound', 'To hide the product', 'To replace the page', 'To skip permission'),
                $q('When may a customer appear?', 'When they clearly agreed', 'When the clip would perform well', 'When you found their face online', 'Always, if they bought'),
            ],
            'practice' => 'Storyboard four beats: hook, point, on-screen words, next step. Watch the plan with imagined sound off. If the point disappears, add words.',
        ]),
        $L([
            'section' => 'Content and social',
            'title' => 'A community is slower than a blast and worth more',
            'keywords' => 'community, repeat audience, replies',
            'lead' => 'A community is people who recognize the problem and come back to talk about it. It is slower than a blast to a crowd. For Milk List it might be a small group of household buyers who compare how they keep the list, because they asked to be there. You host. You do not extract. The goal is a room that stays useful, not a room you can mine for phone numbers.',
            'heading' => 'Host a room you would sit in',
            'para' => 'Set a purpose in one sentence: "Household buyers sharing how they keep one list." Set a few rules you will enforce: no selling to each other, no dumping private details, no insults. Show up on a schedule you can keep. Ask questions you will read. If someone needs support, take it to the support path rather than making them perform the problem in public. A community that exists only to be sold to will empty itself.',
            'bullets' => [
                'Purpose first. If you cannot say why a member would return, do not open the room.',
                'Rules you will actually enforce, written where people can see them.',
                'Your job is to host and to answer. Members are not a list you export.',
                'A blast is fine for an announcement. Do not call the blast a community.',
            ],
            'visual' => tf_panels('mkcomu', 'Blast versus room', 'A blast', ['One send', 'Then silence', 'An announcement'], 'A community', ['A purpose', 'A schedule', 'People return'], 'Do not export the room into a sales file.'),
            'rows' => [
                ['Purpose', 'Why return', 'How we keep one list.'],
                ['Rules', 'Visible', 'No selling, no insults.'],
                ['Host', 'You show up', 'A schedule you keep.'],
                ['Ask', 'You will read it', 'Real questions.'],
                ['Support', 'The right path', 'Not a public pile-on.'],
                ['Not', 'A mined list', 'They did not agree to that.'],
            ],
            'e1' => ['Example 1 — a room with a purpose', 'The reason to return is stated. The rule is stated.', dm_page('The room', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">Household buyers, one list</h1>
<p>Share how you keep milk from being bought twice. No selling to each other. No private numbers.</p>
<p>This week\'s question: what do you add the night before?</p>
HTML)],
            'e2' => ['Example 2 — a room that is a funnel in disguise', 'The only posts are offers. Members are inventory.', dm_page('A disguised blast', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Community</h1>
<p>Buy now. Buy now. Drop your phone number for a surprise. We will message everyone you know.</p>
HTML)],
            'micro' => [
                'Small and active beats large and silent.',
                'If you miss a month, say so and restart smaller.',
                'A creator with a real audience is a guest, not a loophole around consent.',
            ],
            'quiz' => [
                $q('What makes a community different from a blast?', 'People return for a purpose you host', 'It has a larger logo', 'You export every number', 'You post buy now on a loop'),
                $q('What should rules be?', 'Visible, and enforced', 'Secret, so you can change them mid-argument', 'Optional for friends', 'Replaced by a discount'),
                $q('What are members not?', 'A file you mine', 'People who asked to be there', 'A reason to show up', 'Allowed to need support in private'),
            ],
            'practice' => 'Write the one-sentence purpose of a room you could host, three rules, and the question you will ask this month. If the purpose is "so we can sell", start again.',
        ]),
        $L([
            'section' => 'Content and social',
            'title' => 'Consistency beats a single viral hope',
            'keywords' => 'consistency, cadence, brand memory',
            'lead' => 'Consistency means the same promise, in the same voice, often enough that Asha can remember you, and rarely enough that you can keep the standard. It does not mean posting every hour until something goes viral. Milk List becomes familiar when the household line shows up like a reliable neighbor, not like a siren.',
            'heading' => 'A rhythm you can keep for a season',
            'para' => 'Pick a rhythm for a season: one post a week and one letter a month, for example. Put it on the calendar. Judge the season by whether the right people understood the offer, not by whether a stranger\'s post got more views. When a piece does travel, answer the new people and send them to the same page. Do not pivot the product into whatever the crowd clapped for unless that was the offer.',
            'bullets' => [
                'Same nouns, same tone, a rhythm written down.',
                'A season is long enough to learn. A day is not.',
                'A piece that travels is a test of your page, not a new identity.',
                'Stopping to protect the standard is better than filling the gap with noise.',
            ],
            'visual' => tf_line('mkrhy', 'A season, not a spike', ['W1', 'W2', 'W3', 'W4', 'W5', 'W6'], [2, 2, 3, 2, 3, 2], 'Posts you actually shipped. A flat, kept rhythm beats one spike you cannot repeat.'),
            'rows' => [
                ['Rhythm', 'Written down', 'Weekly post, monthly letter.'],
                ['Voice', 'The same neighbor', 'Plain, concrete.'],
                ['Season', 'Long enough', 'Then read the result.'],
                ['Spike', 'If it happens', 'Same page, same offer.'],
                ['Judge', 'Did they understand', 'Not a stranger\'s views.'],
                ['Stop', 'To keep quality', 'Better than noise.'],
            ],
            'e1' => ['Example 1 — a rhythm card', 'Someone else could keep this going on a tired week.', dm_page('The rhythm', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">This season</h1>
<p>Every Tuesday: one post, household buyer, one fact, link to the page.</p>
<p>First Monday: one letter to people who asked.</p>
<p>Nouns that stay: list, household, Rs 499.</p>
HTML)],
            'e2' => ['Example 2 — waiting for a miracle', 'No rhythm. The plan is luck.', dm_page('Viral hope', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">Post when inspired</h1>
<p>If one video explodes we will become that. Until then, nothing regular.</p>
HTML)],
            'micro' => [
                'Familiar is a marketing result. It is built by repetition of the truth.',
                'A new format is allowed. A new promise every week is not consistency.',
                'Measure the season in starts and replies, not in a single spike.',
            ],
            'quiz' => [
                $q('What is consistency?', 'The same promise, in a rhythm you can keep', 'Posting every hour', 'Waiting to go viral', 'A new identity each week'),
                $q('How should you treat a piece that travels?', 'Answer people and send them to the same page', 'Change the product to match the crowd', 'Buy followers to keep it going', 'Stop the calendar forever'),
                $q('What is better than filling a gap with noise?', 'Pausing to protect the standard', 'A slogan with no page', 'A purchased crowd', 'A new slogan'),
            ],
            'practice' => 'Write the rhythm you can keep for eight weeks without heroics. Include what you will not publish just to fill a hole.',
        ]),
        $L([
            'section' => 'Email',
            'title' => 'A list is permission, not a purchased file',
            'keywords' => 'email list, permission, consent',
            'lead' => 'An email list is a set of people who asked to hear from you, or who bought and need a note about that purchase. It is not a spreadsheet you bought, scraped, or guessed. Milk List may write to Asha because she started a list or because she asked for the letter. It may not write to her because her address appeared in a group, a directory, or someone else\'s file.',
            'heading' => 'The yes comes before the letter',
            'para' => 'The form that collects the address should say what you will send and how often, in plain language. A pre-checked box is a poor yes. A purchased list is not a yes at all. Transactional notes — the list is open, the receipt, a password reset she asked for — are different from a weekly offer. Do not bury marketing inside a receipt and call the whole thing required. Keep the list clean: if the address fails or the person leaves, stop.',
            'bullets' => [
                'A yes is specific: what letters, about this offer, and a way to stop.',
                'Bought, scraped, or guessed addresses are not a list. Do not use them.',
                'A customer can receive news about the thing they bought. That is not a license for unrelated mail forever.',
                'Honor the stop quickly. A person who left and still hears from you will not trust the next brand either, but they will remember you.',
            ],
            'visual' => tf_split('mkperm', 'Did they ask?', 'Yes, for this letter', 'You may write', 'Say what and how often', 'No, a bought file', 'Do not write', 'A receipt is not a weekly offer in disguise.'),
            'rows' => [
                ['Yes', 'They asked', 'Or they bought this.'],
                ['Said', 'What and how often', 'On the form.'],
                ['No', 'Bought or scraped', 'Not a list.'],
                ['Receipt', 'About the purchase', 'Not a hidden ad stream.'],
                ['Fails', 'Bad address', 'Stop trying.'],
                ['Leave', 'They said stop', 'Honor it.'],
            ],
            'e1' => ['Example 1 — a yes a person can see', 'The form says what arrives and that they can leave.', dm_page('Ask for the letter', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">The household note</h1>
<p>One short letter a month about keeping the list. Leave whenever you want.</p>
<p><label>Email<br><input style="width:100%;padding:10px;margin-top:4px;border:1px solid #cbd5e1;border-radius:8px;" value="asha@example.com"></label></p>
<p><a href="#yes" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Send me the note</a></p>
HTML)],
            'e2' => ['Example 2 — a file, not a yes', 'Nobody asked. The plan is to write anyway.', dm_page('A purchased file', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">5,000 addresses</h1>
<p>Source unknown. They did not ask. The box was already checked. Send the offer anyway.</p>
HTML)],
            'micro' => [
                'The laws differ by country. The practical rule travels: an obvious yes, and an easy leave.',
                'A preview checkbox that adds them "for their convenience" is not convenient.',
                'Your from-name should be recognizable. Mystery senders train people to ignore the real note.',
            ],
            'quiz' => [
                $q('When may Milk List write a marketing letter?', 'When the person asked, or the note is about what they bought', 'When their address was in a file you bought', 'When a box was pre-checked', 'When they were in a group you were not invited to'),
                $q('What should the form say?', 'What you will send and how often', 'Nothing, so more people finish', 'Only "subscribe"', 'That they cannot leave'),
                $q('What do you do when someone leaves?', 'Stop', 'Wait a month and try again', 'Move them to a new list', 'Sell the address'),
            ],
            'practice' => 'Look at the place you collect addresses. Write exactly what the person is saying yes to. If that sentence is missing on the form, add it before you send anything.',
        ]),
        $L([
            'section' => 'Email',
            'title' => 'The welcome note sets the relationship',
            'keywords' => 'welcome email, first letter, expectation',
            'lead' => 'The welcome note is the first letter after a yes. It should arrive soon, sound like the page, and tell Asha what she just started. For Milk List: the list is the point, here is how to add milk, here is how often you will write, and here is the one thing to do today. A welcome that only shouts a discount wastes the most attentive moment you will get.',
            'heading' => 'Soon, familiar, and useful',
            'para' => 'Send it when the memory of the page is fresh. Repeat the promise in the same nouns. Teach one action, not the whole manual. Tell her what the next letters are, so the second one does not feel like a stranger. Put the way to leave in this note too. If she did not mean to sign up, you want her gone cleanly, not marked as a silent complaint.',
            'bullets' => [
                'Arrive quickly. A welcome three weeks later is an interruption.',
                'One action: add the first item, or invite the house.',
                'Set the rhythm: "A short note once a month."',
                'Same from-name, same tone, and a visible way to leave.',
            ],
            'visual' => tf_flow('mkwel', 'The first letter', [['promise', 'same nouns'], ['action', 'add milk'], ['rhythm', 'monthly'], ['leave', 'visible']], 'The welcome teaches the relationship. Later letters only continue it.'),
            'rows' => [
                ['When', 'Soon', 'While she remembers.'],
                ['Promise', 'Same words', 'One list, milk once.'],
                ['Action', 'One step', 'Add the first item.'],
                ['Rhythm', 'What is next', 'A monthly note.'],
                ['From', 'Recognizable', 'Milk List.'],
                ['Leave', 'In this note', 'Not hidden.'],
            ],
            'e1' => ['Example 1 — a welcome that teaches one step', 'She knows what she started and what to do before dinner.', dm_page('Welcome', <<<'HTML'
<p style="font-size:12px;color:#64748b;">From Milk List</p>
<h1 style="font-size:24px;margin:8px 0;">Your list is ready</h1>
<p>Add the first item, even if it is only milk. We send one short note a month. Leave whenever you want.</p>
<p><a href="#add" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Add the first item</a></p>
HTML)],
            'e2' => ['Example 2 — a welcome that is a shout', 'No teaching, a new tone, and no idea what the next letter will be.', dm_page('A shout', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">WELCOME TO THE FAMILY</h1>
<p>Huge news soon. Do not miss deals. Also buy the cookbook.</p>
HTML)],
            'micro' => [
                'If the welcome promises a monthly note, do not send five pitches the same week.',
                'One link. The letter is not a homepage.',
                'Plain text that says the point beats a picture that hides it.',
            ],
            'quiz' => [
                $q('When should the welcome arrive?', 'Soon after the yes', 'Three weeks later', 'Only if she buys a second product', 'Never, to avoid bothering her'),
                $q('What should it teach?', 'One action and the rhythm of future notes', 'Every feature', 'A different personality', 'Five offers'),
                $q('Why include a way to leave in the first note?', 'So a mistaken yes can end cleanly', 'So the letter looks longer', 'So you can argue', 'So you can hide it in an image'),
            ],
            'practice' => 'Write the welcome in five lines: what they started, one action, how often you will write, who it is from, and how to leave.',
        ]),
        $L([
            'section' => 'Email',
            'title' => 'One email, one action',
            'keywords' => 'email marketing, one call to action, letter',
            'lead' => 'A marketing letter asks for one action. Milk List\'s Tuesday note might ask Asha to add this week\'s missing item. It should not also ask her to follow five networks, read three articles, and buy a second product. People skim letters. One ask survives. Three asks cancel each other.',
            'heading' => 'A letter, not a homepage',
            'para' => 'Structure is simple. Why this note, in her situation. One fact or one small teaching. One button that matches the sentence. A footer with the real sender and the way to leave. If you have a second idea, it belongs in a later letter. Write the letter so it still makes sense if the images never load. That is how many people will see it.',
            'bullets' => [
                'One action. The button, the sentence, and the page all name it.',
                'Say why now in her week, not "just checking in".',
                'The letter must be readable as text. Do not hide the offer inside one picture.',
                'The sender address and the leave link are part of the letter, not clutter.',
            ],
            'visual' => tf_flow('mkone', 'The letter', [['why', 'this week'], ['fact', 'one item'], ['ask', 'add milk']], 'A second ask belongs in a second letter.'),
            'rows' => [
                ['Why', 'Her week', 'Before the shop.'],
                ['Fact', 'One teaching', 'Add it the night before.'],
                ['Ask', 'One button', 'Add milk.'],
                ['Page', 'Matches the ask', 'The list, not a blog.'],
                ['Text', 'Works alone', 'Images may fail.'],
                ['Footer', 'Sender and leave', 'Always there.'],
            ],
            'e1' => ['Example 1 — one ask', 'The whole letter points at adding an item.', dm_page('One letter', <<<'HTML'
<p style="font-size:12px;color:#64748b;">From Milk List</p>
<h1 style="font-size:24px;margin:8px 0;">Before the shop run</h1>
<p>Add milk tonight if it is missing. The house sees the same list in the morning.</p>
<p><a href="#add" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Add milk</a></p>
<p style="font-size:13px;color:#64748b;">Milk List · Leave this note whenever you want</p>
HTML)],
            'e2' => ['Example 2 — a letter that is a menu', 'Every link is a different job.', dm_page('A menu letter', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">This week</h1>
<p><a href="#a">Add milk</a> · <a href="#b">Read the blog</a> · <a href="#c">Follow</a> · <a href="#d">Buy the book</a> · <a href="#e">Take the survey</a></p>
HTML)],
            'micro' => [
                'A P.S. that adds a second offer is still a second offer.',
                'Short is a kindness. Say the point above the place where a phone cuts the letter.',
                'If there is no action this month, send nothing. Silence beats a fake "hello".',
            ],
            'quiz' => [
                $q('How many actions should a marketing letter ask for?', 'One', 'Five, so something sticks', 'Zero, only pictures', 'One per link in the footer menu'),
                $q('Why must the letter work as text?', 'Images often do not load', 'To hide the offer', 'To avoid a subject', 'To skip the leave link'),
                $q('Where does a second idea go?', 'In a later letter', 'In the P.S. of this one', 'In a row of equal buttons', 'Nowhere, delete the list'),
            ],
            'practice' => 'Write a letter with a why, one fact, and one button. Then delete every extra link that is not the leave link or that one button.',
        ]),
        $L([
            'section' => 'Email',
            'title' => 'The subject line is the headline',
            'keywords' => 'subject line, preview text, inbox',
            'lead' => 'The subject line is the headline of the letter. It competes with every other sender in the inbox. It should say the concrete thing, in the same tone as the page. "Add milk before the shop" tells Asha what the letter is. "You won\'t believe this!!!" tells her to be suspicious. The preview text is the second line. It should continue the subject, not repeat it.',
            'heading' => 'A true line she can scan',
            'para' => 'Write the subject after the letter, so it cannot promise a different letter. Keep it short enough to read on a phone. Use her nouns. Do not fake a reply, a personal emergency, or a thread she is not in. "Re:" on a first letter is a trick. ALL CAPS and a pile of marks are the sound of shouting. The from-name does half the work: "Milk List" beside a clear subject beats a clever subject from "noreply".',
            'bullets' => [
                'The subject matches the one action inside.',
                'Preview text adds a fact. It does not paste the subject again.',
                'No fake replies, no fake forwards, no false urgency.',
                'The from-name is recognizable and stable.',
            ],
            'visual' => tf_panels('mksub', 'The inbox line', 'Clear', ['Milk List', 'Add milk before the shop', 'The house sees one list'], 'A trick', ['noreply', 'Re: you won', 'Open or else'], 'The subject is a claim about the letter.'),
            'rows' => [
                ['Subject', 'The headline', 'Add milk before the shop.'],
                ['Preview', 'The next fact', 'The house sees one list.'],
                ['Match', 'The letter', 'Same action.'],
                ['From', 'Stable name', 'Milk List.'],
                ['Avoid', 'Fake reply', 'Re: on a first note.'],
                ['Phone', 'Still readable', 'Short and concrete.'],
            ],
            'e1' => ['Example 1 — subject and preview agree', 'You know the letter before you open it.', dm_page('Inbox', <<<'HTML'
<p style="font-size:13px;color:#64748b;margin:0;">Milk List</p>
<h1 style="font-size:20px;margin:4px 0;">Add milk before the shop</h1>
<p style="margin:0;color:#475569;">The house sees one list in the morning.</p>
HTML)],
            'e2' => ['Example 2 — a trick subject', 'The letter will not match, and the sender is a stranger.', dm_page('A trick', <<<'HTML'
<p style="font-size:13px;color:#64748b;margin:0;">noreply</p>
<h1 style="font-size:20px;margin:4px 0;">Re: urgent, you won!!!</h1>
<p style="margin:0;color:#475569;">Re: urgent, you won!!!</p>
HTML)],
            'micro' => [
                'Curiosity is fair when the letter satisfies it immediately. A bait subject is not.',
                'Do not hide the only version of the point inside an image under a vague subject.',
                'Test two subjects only when you can read the result. Otherwise pick the truer line.',
            ],
            'quiz' => [
                $q('What should the subject line do?', 'Say the concrete action in the letter', 'Pretend to be a reply', 'Shout with extra marks', 'Hide the topic'),
                $q('What is the preview text for?', 'A second fact, not a repeat and not a trick', 'The same words as the subject', 'A fake emergency', 'The unsubscribe link only'),
                $q('Which from-name fits?', 'Milk List', 'noreply', 'Re: you', 'Urgent winner'),
            ],
            'practice' => 'Write three subjects for the same letter. Pick the one that a stranger could match to the button inside. Write a preview line that adds a fact.',
        ]),
        $L([
            'section' => 'Email',
            'title' => 'A segment is a reason to write a different note',
            'keywords' => 'email segment, audience split, relevance',
            'lead' => 'A segment is a smaller true group you will write to differently. New households and households that have used the list for a month are not the same reader. Milk List can send the welcome to the first and a "add Saturday\'s items" note to the second. A segment is not twenty labels you never use. Start with two, and only split again when the letter would actually change.',
            'heading' => 'Split only when the letter changes',
            'para' => 'Useful splits follow behavior you really have: asked but not started, started this week, still active after a month. A guess about income or a purchased personality type is a weak reason to talk differently. Name the segment by the situation, not by a code. Write the two opens side by side. If they are the same sentence, you do not have two segments. You have one list and extra admin.',
            'bullets' => [
                'Two segments are enough at the start: new, and already using it.',
                'The letter must change. A different label with the same pitch is not a segment.',
                'Use behavior you collected fairly, not a secret profile you bought.',
                'People who left are not a segment. They are done.',
            ],
            'visual' => tf_panels('mkseg', 'Two true groups', 'Just started', ['The list is ready', 'Add the first item', 'What we send'], 'A month in', ['Before the shop', 'Add Saturday', 'No intro again'], 'Same pitch means you do not have two segments.'),
            'rows' => [
                ['New', 'Just said yes', 'Welcome and one step.'],
                ['Active', 'Using the list', 'This week\'s item.'],
                ['Test', 'Different open', 'Or do not split.'],
                ['Name', 'The situation', 'Not a code.'],
                ['Source', 'Behavior you have', 'Not a bought profile.'],
                ['Left', 'Not a segment', 'Do not write.'],
            ],
            'e1' => ['Example 1 — two opens that are actually different', 'A new person is not told to "keep up the habit" they do not have yet.', dm_page('Two notes', <<<'HTML'
<h1 style="font-size:20px;margin:0 0 6px;">New: your list is ready</h1>
<p>Add the first item today.</p>
<h1 style="font-size:20px;margin:16px 0 6px;">A month in: before Saturday</h1>
<p>Add the items you always forget. No need to introduce the list again.</p>
HTML)],
            'e2' => ['Example 2 — labels without a difference', 'Both groups get the same shout.', dm_page('Fake segments', <<<'HTML'
<h1 style="font-size:20px;margin:0 0 6px;">Segment A</h1>
<p>Huge deals. Buy now.</p>
<h1 style="font-size:20px;margin:16px 0 6px;">Segment B</h1>
<p>Huge deals. Buy now.</p>
HTML)],
            'micro' => [
                'More segments mean more letters to maintain. Earn each split.',
                'A person can move. The new household becomes the active one. Do not trap them in the welcome forever.',
                'Do not build a segment called "inactive" and then nag them daily. Write once, usefully, or let them be.',
            ],
            'quiz' => [
                $q('When is a segment real?', 'When the letter to that group is actually different', 'When you have a new label', 'When you bought a profile', 'When everyone gets the same shout'),
                $q('Which split fits Milk List at the start?', 'New households and households already using the list', 'Twenty income bands', 'People who left, so you can keep writing', 'Everyone, renamed'),
                $q('What happens to someone who leaves?', 'They are done, not a segment', 'They move to a louder segment', 'They get the welcome again', 'They are sold'),
            ],
            'practice' => 'Write two opening lines: one for a person who started today, one for a person in their second month. If you cannot make them different, keep one list.',
        ]),
        $L([
            'section' => 'Email',
            'title' => 'Unsubscribe is part of the promise',
            'keywords' => 'unsubscribe, email footer, permission end',
            'lead' => 'Leaving the list is part of the promise you made when they joined. The way out should be easy to see, easy to use, and honored quickly. Milk List puts a plain "leave this note" link in every marketing letter. Hiding it, delaying it, or shaming the person is how a list becomes a complaint list.',
            'heading' => 'An easy leave, honored quickly',
            'para' => 'One click should be enough to stop marketing letters. Do not make them log in, type a paragraph, or "wait 10 days while we consider". A short goodbye page can confirm it worked. You may still send a receipt or a security note they need for an account they kept. Say the difference in plain words so "leave the letters" does not look like "we deleted your list". Keep a record of the stop so a new campaign cannot add them back by accident.',
            'bullets' => [
                'Every marketing letter has a visible leave link. Not an image. Not a puzzle.',
                'One step. No guilt screen with five reasons to stay, blocking the exit.',
                'Honor it across the marketing you send. Do not move them to a twin list.',
                'Service notes about an account they still have are different. Do not sneak offers into those.',
            ],
            'visual' => tf_split('mkun', 'Marketing letter?', 'Yes', 'Show the leave', 'One step, then stop', 'A needed receipt', 'Keep it plain', 'No offer hiding in the receipt.'),
            'rows' => [
                ['Link', 'Every marketing letter', 'Leave this note.'],
                ['Steps', 'One', 'No maze.'],
                ['Speed', 'Quickly', 'Not a 10-day stall.'],
                ['Twin list', 'Not allowed', 'A stop is a stop.'],
                ['Receipt', 'Still allowed', 'No hidden offer.'],
                ['Record', 'Remember the stop', 'Do not add them back.'],
            ],
            'e1' => ['Example 1 — the leave is visible', 'The letter can end in one tap.', dm_page('An easy leave', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Before the shop run</h1>
<p>Add milk if it is missing.</p>
<p><a href="#add" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Add milk</a></p>
<p><a href="#leave" style="color:#475569;">Leave this note</a></p>
HTML)],
            'e2' => ['Example 2 — the leave is a maze', 'Shame, a login, and a delay. The promise is broken.', dm_page('A maze', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Are you sure you want to hurt the team?</h1>
<p>Log in, write a paragraph, and wait 10 days. Or take the discount instead.</p>
HTML)],
            'micro' => [
                'A person who leaves cleanly may return later. A person who had to fight you will not.',
                'The link must work. Test it the way you test the buy button.',
                'Buying a list and then offering unsubscribe does not create the original yes.',
            ],
            'quiz' => [
                $q('What does a marketing letter need?', 'A visible, one-step way to leave', 'A hidden image', 'A guilt maze', 'A 10-day review'),
                $q('What may you still send after they leave the marketing list?', 'A receipt or security note for an account they kept, with no sneaked offer', 'The same weekly pitch from a twin list', 'A louder discount', 'Nothing ever, including a receipt they need'),
                $q('What is wrong with moving them to a second list?', 'A stop was not a stop', 'It is better organization', 'It honors the yes', 'It is required'),
            ],
            'practice' => 'Find the leave link in your last letter. Count the steps until marketing mail actually stops. If it is more than one, simplify it before the next send.',
        ]),
        $L([
            'section' => 'Paid attention',
            'title' => 'An ad is the promise plus the page it lands on',
            'keywords' => 'advertising, ad, landing page',
            'lead' => 'An ad is a paid promise that must be kept by the page it opens. Milk List\'s ad can say "One shared list that stays put. Rs 499 a year." The click has to open that offer, not a homepage menu and not a different sale. You are not buying a click. You are buying a chance for the right person to see a true sentence and land in the right room.',
            'heading' => 'The ad and the page are one piece',
            'para' => 'Write the page first. Then cut a true line from it for the ad. Choose a place where intent is closest: a person searching "family shopping list" is closer to the problem than a random interruption, though both can work if the sentence is clear and the page matches. Set a small spend and a stop before you start. An ad that outruns the page just spends faster.',
            'bullets' => [
                'The ad may be shorter than the page. It may not be different.',
                'The click opens the matching page, with the same price and the same noun.',
                'Decide the stop before you pay: a daily amount, or a cost per start you will not cross.',
                'If the page is not ready, do not buy the visit.',
            ],
            'visual' => tf_flow('mkad', 'One piece', [['page', 'the offer'], ['ad', 'a true cut'], ['click', 'that page']], 'A click to the wrong room wastes the money and the person.'),
            'rows' => [
                ['Page', 'Written first', 'Price and promise.'],
                ['Ad', 'A shorter cut', 'Same nouns.'],
                ['Click', 'Matching URL', 'Not a menu.'],
                ['Place', 'Near the intent', 'A real query, or a clear interruption.'],
                ['Stop', 'Chosen first', 'A spend you can lose.'],
                ['Not ready', 'No ad', 'Fix the page.'],
            ],
            'e1' => ['Example 1 — the ad is a true cut of the page', 'Same promise, same price. The click has somewhere honest to land.', dm_ads('Household list — search', [
                ['Campaign', 'Household list, March'],
                ['Shows on', 'Search'],
                ['When they type', 'family shopping list'],
                ['Daily limit', 'Rs 80, then it stops'],
                ['Pay for', 'A click'],
                ['Landing page', 'milklist.example/shared-list'],
            ], '<p style="margin:0;font-size:18px;color:#1a0dab;font-weight:600;">One shared list that stays put</p><p style="margin:4px 0 0;font-size:13px;color:#4b5563;">Rs 499 a year. The list stays on the screen so milk is bought once.</p>')],
            'e2' => ['Example 2 — the ad bought the wrong visit', 'The ad promised recipes. The page sells a list.', dm_ads('Broken visit', [
                ['Campaign', 'Recipes, then a list'],
                ['Shows on', 'Search'],
                ['When they type', 'free dinner recipes'],
                ['Daily limit', 'Rs 80'],
                ['Pay for', 'A click'],
                ['Landing page', 'A list for Rs 499. No recipes.'],
            ], '<p style="margin:0;font-size:18px;color:#1a0dab;font-weight:600;">Free recipes tonight</p><p style="margin:4px 0 0;font-size:13px;color:#b91c1c;">The page they open sells a shopping list instead.</p>', 'note')],
            'micro' => [
                'A cheap click to the wrong page is expensive.',
                'The ad is public. Every claim rule still applies. No fake timers.',
                'Start small enough that a wrong sentence is a lesson, not a wound.',
            ],
            'quiz' => [
                $q('What is an ad?', 'A paid promise kept by the page it opens', 'A click with no page', 'A different offer than the site', 'A way to skip the truth'),
                $q('What should you write first?', 'The page', 'The clever line', 'The spend', 'The hashtag'),
                $q('When should you refuse to run the ad?', 'When the page is not ready to keep the promise', 'When the sentence is clear', 'When you have a stop amount', 'When the price matches'),
            ],
            'practice' => 'Write the ad in one sentence and the first screen of the page under it. Circle every noun. If they differ, fix the ad before you spend.',
        ]),
    ];
}
