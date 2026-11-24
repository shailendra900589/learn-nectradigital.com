<?php
declare(strict_types=1);

function marketing_chapters_b(): array
{
    $L = static function (array $c): array {
        return track_lesson('visual', '', $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };

    return [
        $L([
            'section' => 'The page',
            'title' => 'A call to action is one next step',
            'keywords' => 'call to action, button, next step',
            'lead' => 'A call to action is the one thing you are asking for now. On the Milk List page it is "Start the list". The words should say the result, not "submit" or "click here". One primary action. A second link may explain, but it should look quieter so Asha can see which step is the step.',
            'heading' => 'A verb plus the outcome',
            'para' => 'Put the action where the promise ends, on the first screen and again after the proof. Repeat the same verb. Three different verbs — start, learn, and explore — are three campaigns on one page. The button must look like a button: contrast, a clear label, and a target that matches the label. If it says "Start the list" it must open the start, not a blog.',
            'bullets' => [
                'Name the outcome. "Start the list" beats "Submit" and "Click here".',
                'One primary action. Quieter links can say "See the price" if that is the same offer.',
                'Repeat the same action after proof. Do not introduce a new verb halfway down.',
                'The place it opens must keep the promise of the label.',
            ],
            'visual' => tf_split('mkcta', 'More than one main step?', 'Yes, they compete', 'Keep one verb', 'Start the list', 'No, one is clear', 'Repeat that verb', 'A quiet link may explain. It is not a second campaign.'),
            'rows' => [
                ['Verb', 'What they do', 'Start.'],
                ['Object', 'What they get', 'The list.'],
                ['Label', 'On the button', 'Start the list.'],
                ['Repeat', 'After proof', 'Same words.'],
                ['Quiet link', 'Same offer', 'See the price.'],
                ['Match', 'Where it goes', 'The start, not a blog.'],
            ],
            'e1' => ['Example 1 — one verb', 'The button says the outcome. The quiet link does not compete.', dm_page('One step', <<<'HTML'
<h1 style="font-size:26px;margin:0 0 8px;">One shared list that stays put</h1>
<p>Rs 499 a year.</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
<p><a href="#price" style="color:#475569;font-size:14px;">See the price</a></p>
HTML)],
            'e2' => ['Example 2 — three campaigns', 'Three equal actions. The visitor has to invent the priority.', dm_page('Three steps', <<<'HTML'
<h1 style="font-size:26px;margin:0 0 8px;">One shared list that stays put</h1>
<p>
  <a href="#a" style="display:inline-block;margin:0 6px 6px 0;background:#059669;color:#fff;text-decoration:none;padding:10px 12px;border-radius:10px;">Start</a>
  <a href="#b" style="display:inline-block;margin:0 6px 6px 0;background:#0284c7;color:#fff;text-decoration:none;padding:10px 12px;border-radius:10px;">Explore</a>
  <a href="#c" style="display:inline-block;background:#d97706;color:#fff;text-decoration:none;padding:10px 12px;border-radius:10px;">Watch</a>
</p>
HTML)],
            'micro' => [
                '"Click here" names the mouse, not the outcome.',
                'A button that does not look tappable will be missed on a phone.',
                'If you cannot name the one step, the page is not ready.',
            ],
            'quiz' => [
                $q('Which label is a call to action for Milk List?', 'Start the list', 'Submit', 'Click here', 'Explore'),
                $q('How many primary actions should the page have?', 'One', 'Three equal ones', 'One per adjective', 'As many as fit'),
                $q('Where should a button labeled "Start the list" go?', 'To the start of the list', 'To a blog', 'To a video with a different offer', 'To a menu'),
            ],
            'practice' => 'Write the one button label. Then list every other link on the page and mark each as the same offer or a leak. Quiet the leaks or remove them.',
        ]),
        $L([
            'section' => 'The page',
            'title' => 'A form should ask only what you will use',
            'keywords' => 'form, fields, friction, lead',
            'lead' => 'Every field is a cost. Asha will type a name and an email on her phone to start a list. She will not type an office address, a fax number, or how she heard about you, unless you truly need it to deliver the thing. Ask for what the next step requires. Ask for the rest later, when there is a reason.',
            'heading' => 'The shortest form that can do the job',
            'para' => 'If the job is "create the household list", you need a way to recognize the account and a way to reach them. That is often a name and an email. A phone number is reasonable if the product works by phone, and you say why. A required field you will not read is a tax on the visitor. Tell them what happens after they send it: "We will open your list." Mystery is friction too.',
            'bullets' => [
                'Required means you cannot do the next step without it. Everything else is optional or later.',
                'Say why a sensitive field is there. "Phone, so the shopper can be invited" is a reason. A bare phone field is not.',
                'One column, large type, a button that repeats the action. This is a phone.',
                'Do not ask them to market to you. "How did you hear about us" can wait, and it can be optional.',
            ],
            'visual' => tf_panels('mkform', 'What the form asks', 'Needed now', ['Name', 'Email', 'Why: to open the list'], 'Ask later', ['Address', 'Age', 'How you heard'], 'A field you will not use is a reason to leave.'),
            'rows' => [
                ['Need', 'To open the list', 'Name and email.'],
                ['Why', 'Shown in words', 'So we can invite the house.'],
                ['Later', 'Not this form', 'Address and age.'],
                ['Optional', 'If you must ask', 'Do not block the start.'],
                ['After', 'What happens', 'We open your list.'],
                ['Phone', 'Big fields', 'One column.'],
            ],
            'e1' => ['Example 1 — two fields and a reason', 'The button matches the job. The note says what happens next.', dm_page('A short form', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">Start the list</h1>
<p>Name and email are enough to open it.</p>
<p><label>Name<br><input style="width:100%;padding:10px;margin-top:4px;border:1px solid #cbd5e1;border-radius:8px;" value="Asha"></label></p>
<p><label>Email<br><input style="width:100%;padding:10px;margin-top:4px;border:1px solid #cbd5e1;border-radius:8px;" value="asha@example.com"></label></p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — a form that interviews them', 'Six required facts before they have a list. Most people stop.', dm_page('A long form', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">Create your account</h1>
<p>All fields required: name, email, phone, age, city, office address, and how you heard about us.</p>
<p><a href="#send" style="display:inline-block;background:#64748b;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;">Submit</a></p>
HTML)],
            'micro' => [
                'The price of a field is paid in people who leave, not in rupees.',
                'A label that says "Submit" hides the outcome. Use the same verb as the page.',
                'If you collect a phone number, you are responsible for it. Do not ask casually.',
            ],
            'quiz' => [
                $q('Which form fits "open the list"?', 'Name and email, with a reason', 'Name, age, address, and fax, all required', 'No fields and no way back', 'Only "how did you hear about us"'),
                $q('When is a field allowed to be required?', 'When the next step cannot happen without it', 'When the team is curious', 'When the form looks too short', 'When a template included it'),
                $q('What should the button say?', 'Start the list', 'Submit', 'Send data', 'Click here'),
            ],
            'practice' => 'List every field on your form. Next to each, write the decision it changes. Remove any field with a blank second column.',
        ]),
        $L([
            'section' => 'The page',
            'title' => 'The thank-you page continues the conversation',
            'keywords' => 'thank you page, confirmation, next step',
            'lead' => 'After Asha starts the list, the next screen is still marketing. It confirms what happened, says what will happen next, and offers one useful step. "Thanks!!!" with a confetti of menus throws away a person who just said yes. The thank-you page is where you set the relationship.',
            'heading' => 'Confirm, expect, continue',
            'para' => 'Confirm the specific action: "Your household list is open." Say what arrives next: "A short note is on its way to that email." Then one continuation that helps the job: invite the person who also shops. Do not immediately sell a second product. Do not dump them on the homepage. If something failed, say so in the same plain voice and give one way to retry.',
            'bullets' => [
                'Name what just happened. A generic thank-you fits any product and reassures none.',
                'Set the expectation in time. "The note arrives in a few minutes" is kinder than silence.',
                'One next step that serves the same job, such as inviting the household.',
                'This page is also how you know the action completed. The ad click is not the completion.',
            ],
            'visual' => tf_flow('mkty', 'After the yes', [['confirm', 'list is open'], ['expect', 'a short note'], ['continue', 'invite house']], 'The thank-you page is part of the path, not the end of your manners.'),
            'rows' => [
                ['Confirm', 'What happened', 'The list is open.'],
                ['Expect', 'What arrives', 'A note to that email.'],
                ['When', 'A real window', 'A few minutes.'],
                ['Continue', 'Same job', 'Invite the house.'],
                ['Avoid', 'A new offer', 'Not a second product.'],
                ['Count', 'This view', 'The start, not the ad click.'],
            ],
            'e1' => ['Example 1 — confirm and one continuation', 'She knows it worked, what to wait for, and one useful next act.', dm_page('List open', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#059669;font-weight:700;">LIST OPEN</p>
<h1 style="font-size:26px;margin:8px 0;">Your household list is open</h1>
<p>A short note is on its way to that email. It arrives within a few minutes.</p>
<p><a href="#invite" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Invite the house</a></p>
HTML)],
            'e2' => ['Example 2 — a dead end', 'No confirmation of what happened, and three new places to get lost.', dm_page('Thanks', <<<'HTML'
<h1 style="font-size:28px;margin:0 0 8px;">Thanks!!!</h1>
<p><a href="#home">Home</a> · <a href="#blog">Blog</a> · <a href="#sale">See other products</a></p>
HTML)],
            'micro' => [
                'If the email may take time, say so. Surprise delay feels like a failure.',
                'A thank-you page that sells a different product trains people to distrust the next yes.',
                'Use this page as the completion you count. Do not count the button press alone if the form can fail.',
            ],
            'quiz' => [
                $q('What should the thank-you page confirm?', 'The specific action that just worked', 'That the brand is excited', 'A new unrelated product', 'Nothing, just a menu'),
                $q('Which continuation fits Milk List?', 'Invite the household to the same list', 'Buy a cookbook', 'Read the careers page', 'Start a different brand'),
                $q('Why does this page matter for measurement?', 'It marks the action that completed', 'It increases the font size', 'It replaces the offer', 'It hides the price'),
            ],
            'practice' => 'Write the three lines of your thank-you screen: what happened, what they should expect, and the one continuation. Remove any second product.',
        ]),
        $L([
            'section' => 'The page',
            'title' => 'Trust sits next to the ask',
            'keywords' => 'trust, price, reviews, contact',
            'lead' => 'Trust is the reason Asha believes the button is safe to press. It sits beside the ask, not in a distant footer she will never reach. For Milk List that means a visible price, a real way to leave, a way to contact a person, and proof you are allowed to show. Hidden price is a reason to assume the worst.',
            'heading' => 'The facts that make the button feel safe',
            'para' => 'Put the price before the form, in the same units she will be charged. Put the exit next to it. A contact that reaches a person beats a logo. Reviews, if you have permission, sit near the claim they support. Security theater — a row of badges you did not earn — is the opposite of trust. If you take payment, the payer should see what will appear on the statement.',
            'bullets' => [
                'Price visible before the form. The same number as the bill.',
                'A human contact: a name or a real inbox, not only a form that vanishes.',
                'Proof with permission, placed next to the promise.',
                'No fake badges, fake countdowns, or "only 2 left" when the stock is not 2.',
            ],
            'visual' => tf_flow('mktru', 'Beside the button', [['price', 'Rs 499'], ['exit', 'leave anytime'], ['contact', 'a real inbox'], ['proof', 'if allowed']], 'Hidden facts make a careful buyer stop.'),
            'rows' => [
                ['Price', 'Before the form', 'Rs 499 a year.'],
                ['Exit', 'In the same view', 'Leave when you want.'],
                ['Contact', 'A person answers', 'A named inbox.'],
                ['Proof', 'With permission', 'Next to the claim.'],
                ['Bill', 'Same number', 'What they will pay.'],
                ['Never', 'Fake signals', 'Badges you did not earn.'],
            ],
            'e1' => ['Example 1 — the facts sit with the ask', 'Price, exit, and a real contact are in the same card as the button.', dm_page('Trust beside the ask', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">Start the household list</h1>
<p>Rs 499 a year. Leave when you want.</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
<p style="font-size:14px;color:#475569;">Questions: hello@milklist.example</p>
HTML)],
            'e2' => ['Example 2 — the ask hides the facts', 'A button, a fake clock, and no price.', dm_page('Hidden facts', <<<'HTML'
<h1 style="font-size:26px;margin:0 0 8px;">Start now before it ends</h1>
<p>Only 2 left. Offer expires in 9 minutes.</p>
<p><a href="#go" style="display:inline-block;background:#e11d48;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Continue</a></p>
HTML)],
            'micro' => [
                'A careful buyer looks for the price the moment they are interested. Help them.',
                'A contact you do not answer is worse than no contact, because it proves neglect.',
                'Trust is specific facts. It is not the word "trusted".',
            ],
            'quiz' => [
                $q('Where should the price appear?', 'Before the form, in the same units as the bill', 'Only after they have typed everything', 'Only in an image', 'Nowhere, so they focus'),
                $q('Which signal belongs beside the Milk List button?', 'Rs 499 a year and a real inbox', 'A badge you did not earn', 'A timer that resets', 'The word trusted with no source'),
                $q('What is wrong with "only 2 left" when stock is not 2?', 'It is a false claim', 'It is too honest', 'It uses too many words', 'It mentions a number'),
            ],
            'practice' => 'Stand next to your button and list the facts within one glance: price, exit, contact, proof. Add any that are missing. Remove any that are fake.',
        ]),
        $L([
            'section' => 'The page',
            'title' => 'The page has to work on a phone',
            'keywords' => 'mobile page, phone, tap target',
            'lead' => 'Asha meets Milk List on a phone, often with one hand, often between tasks. A page that only works on a wide screen is a page that misses her. Working on a phone means the headline, the price, and the button are visible without pinching, the type is large enough to read, and the button is easy to hit.',
            'heading' => 'One column, readable type, a real target',
            'para' => 'Design the phone first. A wide layout that you later squash hides the button under a menu or forces sideways scrolling. Text in an image will not resize. Links that are only a few pixels apart get the wrong tap. If a video autoplays with sound, you have startled someone in a queue. The same words as the desktop page should be on the phone. Do not serve a thinner promise to a smaller screen.',
            'bullets' => [
                'Headline, price, and the primary button appear without sideways scrolling.',
                'Type is large enough to read at arm\'s length. Buttons are easy to hit with a thumb.',
                'Do not put the only version of the promise inside an image.',
                'The phone page makes the same claim as the wide page. Same price, same offer.',
            ],
            'visual' => tf_flow('mkmob', 'Phone first screen', [['headline', 'list stays'], ['price', 'Rs 499'], ['button', 'easy to tap']], 'If she must pinch to find the button, the page is not finished.'),
            'rows' => [
                ['Order', 'Phone first', 'Then the wide screen.'],
                ['Visible', 'Without pinching', 'Headline and price.'],
                ['Target', 'Thumb sized', 'The one button.'],
                ['Type', 'Real text', 'Not trapped in an image.'],
                ['Sound', 'Off until asked', 'No surprise audio.'],
                ['Same offer', 'Both screens', 'One price.'],
            ],
            'e1' => ['Example 1 — a card that fits a phone', 'One column. The button is a block. Nothing sits side by side.', dm_page('On a phone', <<<'HTML'
<h1 style="font-size:28px;line-height:1.2;margin:0 0 8px;">One shared list that stays put</h1>
<p style="font-size:18px;">Rs 499 a year.</p>
<p><a href="#start" style="display:block;text-align:center;background:#059669;color:#fff;text-decoration:none;padding:14px 16px;border-radius:12px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — a wide bar squeezed into a pocket', 'Three tiny actions in a row. The promise is a slogan beside them.', dm_page('Too wide', <<<'HTML'
<p style="font-size:12px;">BEST · FAST · NEW · SALE · BLOG · LOGIN · CAREERS · PRESS</p>
<p style="font-size:12px;">Synergy for modern homes. Tap the tiny words above.</p>
HTML)],
            'micro' => [
                'Check the page on a phone you did not design it on.',
                'A popup that covers the headline has replaced the page.',
                'Slow pages feel broken. A person standing in a shop will not wait.',
            ],
            'quiz' => [
                $q('What must be visible on the phone without pinching?', 'The headline, the price, and the button', 'The full footer sitemap', 'Three equal campaigns', 'A wide menu bar'),
                $q('Where should the promise live?', 'In real text', 'Only inside an image', 'Only in a video with sound on', 'Only on the desktop version'),
                $q('What should the phone page change about the offer?', 'Nothing about the price or the promise', 'A higher price', 'A different product', 'Fewer facts, so it is shorter'),
            ],
            'practice' => 'Open your page on a phone. Can you read the promise and tap the button with one thumb without zooming? If not, change the page until you can.',
        ]),
        $L([
            'section' => 'Search',
            'title' => 'Search is a question, and your page is an answer',
            'keywords' => 'search, query, answer page, seo',
            'lead' => 'Search starts when a person types a question. "shared grocery list for family" is a question even without a question mark. Your page is one possible answer. Digital marketing uses search by making a true page that deserves to be that answer, then helping the person recognize it in the results. It does not use search by hiding words in the footer.',
            'heading' => 'Begin with their words',
            'para' => 'Write down the phrases Asha would actually type. She might type "family shopping list" or "stop buying milk twice". She will not type your internal brand slogan if she has never heard it. The page should use her words in the title, the heading, and the answer itself. A page that repeats a phrase fifty times is not a better answer. It is a worse one. One clear answer beats a pile of keywords.',
            'bullets' => [
                'Collect questions from real talks, from your own search, and later from the queries report. Do not invent a dialect nobody types.',
                'Group phrases that mean the same thing. They can support one page.',
                'The page must answer. A title that promises an answer and a page that only has a slogan will not satisfy the person or earn a durable place.',
                'You do not control the ranking. You control whether the page is a real answer and whether the result line is honest.',
            ],
            'visual' => tf_flow('mksrch', 'From question to page', [['question', 'their words'], ['page', 'a real answer'], ['result', 'an honest line']], 'The phrase is the question. The page is the answer.'),
            'rows' => [
                ['Query', 'Their words', 'Family shopping list.'],
                ['Not', 'Your slogan', 'Unless they say it.'],
                ['Group', 'Same meaning', 'One page.'],
                ['Answer', 'On the page', 'Not only in the title.'],
                ['Repeat', 'A few true uses', 'Not fifty.'],
                ['Control', 'The page and the line', 'Not the rank itself.'],
            ],
            'e1' => ['Example 1 — a page that answers the question', 'The heading uses her words and the paragraph answers.', dm_page('Family shopping list', <<<'HTML'
<h1 style="font-size:26px;margin:0 0 8px;">A family shopping list that stays put</h1>
<p>Add milk once. Everyone in the house sees the same list, so it is not bought twice and not forgotten.</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — a slogan that repeats a phrase', 'The words are stuffed. The question is not answered.', dm_page('Keyword pile', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">List list list</h1>
<p>Family shopping list family shopping list family shopping list. Best list list.</p>
HTML)],
            'micro' => [
                'If you would not say the phrase aloud, do not build a page on it.',
                'A brand query ("Milk List") and a problem query ("family shopping list") are different questions.',
                'Answering well is the work. Tricks that hide text are a good way to lose the page.',
            ],
            'quiz' => [
                $q('What is a search query?', 'The words a person types', 'Your slogan', 'A hidden footer list', 'The logo'),
                $q('What should the page do with those words?', 'Answer the question in the open', 'Repeat them fifty times', 'Hide them in the same color as the background', 'Ignore them and use only the brand'),
                $q('What do you control?', 'Whether the page is a true answer and the result line is honest', 'The exact rank on every search', 'Who else is allowed to write', 'The person\'s question'),
            ],
            'practice' => 'Write ten phrases Asha might type. Cross out any you would not say aloud. Group the rest by meaning.',
        ]),
        $L([
            'section' => 'Search',
            'title' => 'Intent decides the page: learn, compare, or buy',
            'keywords' => 'search intent, informational, commercial, transactional',
            'lead' => 'The same word can hide different jobs. "What is a shared grocery list" wants to learn. "Shared list vs family chat" wants to compare. "Milk List price" wants to buy or to check the cost. Intent is that job. The page you offer must do that job. A sales page on a learn query feels pushy. A definition on a buy query feels like a stall.',
            'heading' => 'Match the page to the job of the query',
            'para' => 'Read the phrase and finish the sentence: "This person wants to ___." Learn, compare, buy, or find a specific site. Then choose the page. Milk List can have a plain explanation for the learn query, a comparison with a chat for the compare query, and the offer page for the price query. One URL should not pretend to be all three at full strength. You may link between them.',
            'bullets' => [
                'Learn: explain the problem and the idea. Offer the product after the explanation, quietly.',
                'Compare: show the difference fairly. Name the alternative. Do not invent flaws.',
                'Buy: price, proof, and the start. Do not open with a history essay.',
                'Navigate: they want a specific brand. The page should be obviously that brand.',
            ],
            'visual' => tf_split('mkint', 'What do they want?', 'To learn?', 'Explain first', 'Then a quiet link', 'To buy?', 'Price and start', 'A definition page stalls a person who is ready.'),
            'rows' => [
                ['Learn', 'Explain', 'What a shared list is.'],
                ['Compare', 'A fair difference', 'List versus chat.'],
                ['Buy', 'Price and start', 'Rs 499, the button.'],
                ['Navigate', 'The named brand', 'Milk List home.'],
                ['Mismatch', 'Wrong job', 'A pitch on a definition.'],
                ['Link', 'Between pages', 'After the job is done.'],
            ],
            'e1' => ['Example 1 — a page for someone who is ready to check the price', 'The price is first because the query was about price.', dm_page('Milk List price', <<<'HTML'
<h1 style="font-size:26px;margin:0 0 8px;">Milk List costs Rs 499 a year</h1>
<p>One household list. Leave when you want.</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — a lesson for someone who asked the price', 'They asked what it costs. The page gives a history instead.', dm_page('Wrong job', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">The history of shopping</h1>
<p>In ancient markets, lists were carved on clay. Our founder once saw a cow.</p>
<p>The price is mentioned nowhere.</p>
HTML)],
            'micro' => [
                'Look at the kind of pages already answering that phrase. They are a hint about intent.',
                'A query with "price", "buy", or your brand plus "login" is not a request for a poem.',
                'You can serve two intents. You should not force them into one shouting page.',
            ],
            'quiz' => [
                $q('What does intent mean?', 'The job the person is trying to do', 'The number of words in the query', 'The color of the result', 'Your preferred page'),
                $q('Which page fits "Milk List price"?', 'A page that states Rs 499 and how to start', 'A history of markets', 'A recipe', 'A careers page'),
                $q('What should a comparison page do?', 'Show a fair difference with the real alternative', 'Invent flaws', 'Hide the alternative', 'Only say "we are the best"'),
            ],
            'practice' => 'Take five real phrases. Label each learn, compare, buy, or navigate. Name the page that should answer it. If two labels share one page, check that the page can actually do both jobs.',
        ]),
        $L([
            'section' => 'Search',
            'title' => 'One page owns one topic',
            'keywords' => 'one topic per page, keyword cannibalization, focus',
            'lead' => 'One page should be the best answer you have for one topic. Milk List\'s offer page owns "shared household shopping list". A second page that targets the same phrase with slightly different adjectives competes with the first. You end up with two weak answers instead of one strong one. Give each topic a home.',
            'heading' => 'A home for each question',
            'para' => 'Before you publish, name the topic in a few words and search your own site for a page that already owns it. If one exists, improve that page. If the new page is actually a different intent — a comparison, a price, a how-to — give it a distinct title and a distinct answer, and link them. The address of the page should hint at the topic in plain words: /shared-list, not /page2.',
            'bullets' => [
                'One primary topic per page. Related phrases that mean the same thing can live there too.',
                'Different intent earns a different page. Same intent should improve the original.',
                'The URL can be short and readable. Words, hyphens, no pile of dates and codes unless you need them.',
                'When two pages do overlap, choose the one that should rank and point the other at it with a clear link. Say which one is the main URL.',
            ],
            'visual' => tf_split('mktopic', 'Same topic already?', 'Yes, same intent', 'Improve that page', 'Do not duplicate', 'No, new job', 'A new URL', 'Two homes for one question weaken both.'),
            'rows' => [
                ['Topic', 'A few words', 'Shared household list.'],
                ['Home', 'One URL', '/shared-list'],
                ['Same intent', 'Do this', 'Improve the original.'],
                ['New intent', 'Do this', 'A distinct page.'],
                ['URL', 'Readable', 'Words, not /page2.'],
                ['Overlap', 'Choose a main URL', 'Link the other to it.'],
            ],
            'e1' => ['Example 1 — a page that names its one topic', 'The heading, the paragraph, and the address agree.', dm_page('Shared household list', <<<'HTML'
<p style="font-size:12px;color:#64748b;">milklist.example/shared-list</p>
<h1 style="font-size:24px;margin:8px 0;">A shared household shopping list</h1>
<p>One list for the house. This page is that answer and no other product.</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — two pages, one topic', 'Same promise, two addresses. They fight each other.', dm_page('Two homes', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Page A and page B</h1>
<p>/shared-list — A shared household shopping list.</p>
<p>/best-shared-list — A shared household shopping list, but louder.</p>
<p>Neither page adds a new answer.</p>
HTML)],
            'micro' => [
                'A blog post that repeats the sales page word for word is not a content strategy. It is a duplicate.',
                'Updating the old page is often the grown-up choice.',
                'The URL is a small promise. Keep it stable once people and other sites use it.',
            ],
            'quiz' => [
                $q('What should you do if a page already answers that topic?', 'Improve that page', 'Publish a twin with more adjectives', 'Hide the old one and hope', 'Stuff both with the same phrase'),
                $q('When is a second page justified?', 'When the intent is actually different', 'When you want more URLs', 'When the adjective changes', 'When the color changes'),
                $q('What does a readable URL do?', 'Hint at the topic in plain words', 'Hide the topic in a code', 'Change every week', 'Include every keyword you know'),
            ],
            'practice' => 'List your important pages and the one topic each owns. If two share a topic and an intent, pick the survivor and write what you will do with the other.',
        ]),
        $L([
            'section' => 'Search',
            'title' => 'The title tag is the blue line',
            'keywords' => 'title tag, search result, blue link',
            'lead' => 'The title tag is the line people see first in a search result, and the name of the tab. It should say the topic and the brand, in that order when the person does not know you yet. A title around fifty to sixty characters is usually enough to be specific without being cut off. It is a promise about the page, so the page must keep it.',
            'heading' => 'Specific, unique, and true',
            'para' => 'Write a different title for every page. "Home" and "Untitled" waste the line. Put the phrase a person would recognize near the start, because the end may be cut. The brand can close the line: "Shared household list that stays on screen | Milk List". Do not pack every synonym in. The result may rewrite your title. Write a good one anyway, because it is also what the tab and the share show.',
            'bullets' => [
                'One title per page. Unique, and faithful to the visible heading.',
                'Important words first. Brand at the end if the query is not your name.',
                'Aim for a line a person can read in one glance, roughly fifty to sixty characters.',
                'No shouting, no pile of cities, no fake "best" you cannot support.',
            ],
            'visual' => tf_flow('mktitle', 'The blue line', [['topic', 'shared list'], ['detail', 'stays put'], ['brand', 'Milk List']], 'The page behind the line must be the page the line promised.'),
            'rows' => [
                ['Title', 'The result line', 'Topic plus brand.'],
                ['Order', 'Topic first', 'Brand at the end.'],
                ['Length', 'One glance', 'About 50 to 60.'],
                ['Unique', 'Every page', 'Not "Home".'],
                ['Match', 'The heading', 'Same promise.'],
                ['Rewrite', 'May happen', 'Write well anyway.'],
            ],
            'e1' => ['Example 1 — a title a result can show', 'Topic first, brand last, and long enough to be specific. This preview is the result card, not the page.', dm_serp(
                'Shared household list that stays on screen | Milk List',
                'A shared shopping list for a household. The list stays on the screen so milk is bought once. Rs 499 a year, and you can leave when you want.'
            )],
            'e2' => ['Example 2 — a title that wastes the line', 'The tab says nothing. The result cannot help Asha choose.', dm_serp(
                'Home',
                'Welcome to our website. We do many things. Click to learn more about our journey and our passion.'
            )],
            'micro' => [
                'The title is not a place to hide extra keywords. People can see it.',
                'If the title and the H1 disagree, the visitor feels misled the moment the page opens.',
                'Changing titles every day chases noise. Change them when the promise or the query is wrong.',
            ],
            'quiz' => [
                $q('What is the title tag for?', 'The result line and the tab', 'A secret list of keywords', 'The footer', 'Only the logo'),
                $q('Which title fits the offer page?', 'Shared household list that stays on screen | Milk List', 'Home', 'Best best best list list', 'Untitled'),
                $q('What should you do if a result rewrites your title?', 'Keep a true, specific title anyway', 'Stuff in more synonyms', 'Leave it as Home', 'Hide text on the page'),
            ],
            'practice' => 'Write titles for your five main pages. Count the characters. Put the topic first. Check that each page\'s visible heading agrees.',
        ]),
        $L([
            'section' => 'Search',
            'title' => 'The meta description is the invitation, not the rank',
            'keywords' => 'meta description, snippet, search invitation',
            'lead' => 'The meta description is your suggested invitation under the blue line. It does not act like a ranking lever you twist. Search often rewrites it from the page. You still write one, because when it is used, it is the reason to click. For Milk List it should say who it is for, the outcome, and the price or the next step, in about 120 to 160 characters.',
            'heading' => 'An honest reason to open the page',
            'para' => 'Write it as a sentence a person can finish reading. Include a fact that helps them choose: Rs 499, a household, the list stays. Do not repeat the title word for word and do not promise a discount the page does not have. Each page gets its own description. A sitewide sentence about "welcome to our journey" is the same waste as a title that says Home.',
            'bullets' => [
                'Treat it as ad copy for a click you have earned the right to ask for. It is not a ranking spell.',
                'One or two sentences. Who, outcome, and a concrete fact.',
                'Unique per page. The comparison page invites a comparison. The price page invites a price check.',
                'If the result shows different text, improve the page\'s real sentences. Those are what a rewrite draws from.',
            ],
            'visual' => tf_flow('mkmeta', 'Under the blue line', [['who', 'a household'], ['outcome', 'milk once'], ['fact', 'Rs 499']], 'The description invites. The page has to deserve the click.'),
            'rows' => [
                ['Role', 'Invitation', 'Not a rank knob.'],
                ['Length', 'Readable', 'About 120 to 160.'],
                ['Content', 'Who and fact', 'House, list, price.'],
                ['Unique', 'Per page', 'Not one sitewide line.'],
                ['Match', 'The page', 'No surprise offer.'],
                ['Rewrite', 'Uses page text', 'Write real sentences.'],
            ],
            'e1' => ['Example 1 — an invitation with a fact', 'A person can tell what they will get and what it costs.', dm_serp(
                'Shared household list that stays on screen | Milk List',
                'A shared shopping list for a household. The list stays on the screen so milk is bought once. Rs 499 a year, and you can leave when you want.'
            )],
            'e2' => ['Example 2 — a sitewide welcome', 'No fact, no person, no reason to prefer this result.', dm_serp(
                'Shared household list that stays on screen | Milk List',
                'Welcome to our website. Click here to learn more.'
            )],
            'micro' => [
                'Do not stuff the description. It is one of the few lines the searcher actually reads.',
                'A description that over-promises trains the wrong click and a fast return.',
                'If you change the offer, change the description the same day.',
            ],
            'quiz' => [
                $q('What is the meta description?', 'Your suggested invitation under the result', 'A hidden ranking spell', 'The page headline', 'The logo text'),
                $q('What belongs in it?', 'Who it is for, the outcome, and a concrete fact', 'Welcome to our website', 'Every synonym you know', 'A different offer than the page'),
                $q('Why write one if search may rewrite it?', 'When it is used, it is the reason to click, and the page text feeds rewrites', 'Because it guarantees the first position', 'Because visitors never see it', 'Because it replaces the page'),
            ],
            'practice' => 'Write descriptions for the offer page and one explanatory page. They should not be interchangeable. Read each under its title and ask if you would click.',
        ]),
        $L([
            'section' => 'Search',
            'title' => 'Headings let a person scan',
            'keywords' => 'headings, h1, h2, scan',
            'lead' => 'Headings are the sentences a person scans before they decide to read. The page has one main heading, the H1, and it matches the promise. Smaller headings, H2 and below, are the questions on the page: what it costs, how the list stays, how you leave. They are an outline, not a place to hide phrases.',
            'heading' => 'One outline, in order',
            'para' => 'Write the headings first, as an outline you could hand to Asha. If the outline is clear, the page is clear. Do not skip levels for decoration, and do not make every line a heading. A heading that says "Welcome" or "Introduction" tells a scanner nothing. Use the words she would use to find that section. The H1 and the title tag should agree, even if the title adds the brand.',
            'bullets' => [
                'One H1. It is the promise of this page.',
                'H2s are real sections. A person can read only the H2s and know the page.',
                'Do not stuff headings with repeated phrases. A scanner notices, and so does anyone who listens to the page read aloud.',
                'The visual size and the heading level should agree. Do not fake a heading with bold text, and do not style a real heading to look like a caption.',
            ],
            'visual' => tf_flow('mkh', 'The outline', [['h1', 'the promise'], ['h2', 'the price'], ['h2', 'how to leave']], 'If the headings are vague, the page is vague.'),
            'rows' => [
                ['H1', 'One promise', 'The list stays put.'],
                ['H2', 'A section', 'What it costs.'],
                ['Order', 'Outline first', 'Then the paragraphs.'],
                ['Scan', 'Headings only', 'The page still makes sense.'],
                ['Title', 'Agrees with H1', 'Brand may be extra.'],
                ['Avoid', 'Empty labels', 'Welcome, intro.'],
            ],
            'e1' => ['Example 1 — an outline a person can scan', 'Three headings. You know the page without the small print.', dm_page('Scannable', <<<'HTML'
<h1 style="font-size:26px;margin:0 0 8px;">A shared list that stays put</h1>
<h2 style="font-size:18px;margin:16px 0 4px;">What it costs</h2>
<p>Rs 499 a year for one household.</p>
<h2 style="font-size:18px;margin:16px 0 4px;">How you leave</h2>
<p>You can leave when you want. The list stays until you do.</p>
HTML)],
            'e2' => ['Example 2 — headings that say nothing', 'A scanner learns nothing. The phrases are repeated for a machine that is not fooled.', dm_page('Empty headings', <<<'HTML'
<h1 style="font-size:22px;margin:0;">Welcome</h1>
<h2 style="font-size:18px;">Introduction</h2>
<p>Things.</p>
<h2 style="font-size:18px;">More introduction</h2>
<p>Family shopping list family shopping list.</p>
HTML)],
            'micro' => [
                'Read only the headings aloud. That is the page a busy person gets.',
                'A question heading is good when it is her question.',
                'Images do not replace headings. A screen reader and a scanner both need the text.',
            ],
            'quiz' => [
                $q('How many H1s should the page have?', 'One', 'One per keyword', 'Zero', 'As many as look big'),
                $q('What is an H2 for?', 'A real section a person can scan', 'A hidden keyword', 'A welcome label', 'A second H1'),
                $q('How should the H1 relate to the title tag?', 'They should agree on the promise', 'They should contradict, for variety', 'The H1 should say only Welcome', 'The title should be empty'),
            ],
            'practice' => 'Outline one page as H1 plus three H2s before you write paragraphs. Show the outline to someone. If they cannot say what the page offers, rewrite the headings.',
        ]),
        $L([
            'section' => 'Search',
            'title' => 'Internal links show the next useful page',
            'keywords' => 'internal links, anchor text, related page',
            'lead' => 'An internal link is a handoff to another page you own. It should be the next useful page, named in words that say what it is. From the explanation of a shared list, the next page might be the price. The words "the price of Milk List" tell both the person and the structure of the site. "Click here" tells neither.',
            'heading' => 'Name the destination',
            'para' => 'Link when it helps the reader continue. A page that links to everything links to nothing, because the choice is noise. Use a short descriptive phrase, not a whole paragraph and not "here". Do not point an offer link at a page with a different offer. Check the links when you rename a page. A broken handoff is a dead end you built yourself.',
            'bullets' => [
                'The anchor is the name of the destination. "What it costs" beats "click here".',
                'Link to the next useful step, not to every page in the footer from every sentence.',
                'The destination should match the anchor. A link that says "price" must open the price.',
                'A few honest links help a visitor and help you see which pages are the hubs.',
            ],
            'visual' => tf_flow('mklink', 'A useful handoff', [['explain', 'what a list is'], ['words', 'what it costs'], ['price', 'Rs 499']], 'The words on the link are a promise about the next page.'),
            'rows' => [
                ['Anchor', 'Names the page', 'What it costs.'],
                ['Avoid', 'Empty words', 'Click here.'],
                ['Next', 'Useful step', 'Explain, then price.'],
                ['Match', 'Anchor and page', 'Same topic.'],
                ['Noise', 'Too many', 'No clear next step.'],
                ['Upkeep', 'After a rename', 'Fix the handoff.'],
            ],
            'e1' => ['Example 1 — the link names the next page', 'You know where you will land.', dm_page('A useful link', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">What a shared list is</h1>
<p>Everyone in the house sees the same items.</p>
<p><a href="#price" style="color:#059669;font-weight:700;">See what Milk List costs</a></p>
HTML)],
            'e2' => ['Example 2 — the link hides the destination', '"Here" could be anything. The row of links is a menu pretending to be a paragraph.', dm_page('Click here', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">What a shared list is</h1>
<p>Read more <a href="#x">here</a>, <a href="#y">here</a>, and <a href="#z">here</a>.</p>
HTML)],
            'micro' => [
                'Footer links can exist for people who are looking. They do not replace a contextual next step.',
                'Linking out to a source you used is fair. It does not weaken a true page.',
                'Do not pay for links or hide them. Earn a mention by being worth citing.',
            ],
            'quiz' => [
                $q('Which anchor is useful?', 'See what Milk List costs', 'Click here', 'Here', 'This'),
                $q('When should you add an internal link?', 'When the next page helps this reader', 'When you want every page to link to every page', 'When the anchor can hide the topic', 'When the destination is a different offer than the words'),
                $q('What must the destination do?', 'Match what the anchor promised', 'Be a surprise', 'Be the homepage every time', 'Be a file named page2'),
            ],
            'practice' => 'On one article or offer page, add up to three internal links. Rewrite each anchor so a person knows the destination before they tap.',
        ]),
        $L([
            'section' => 'Search',
            'title' => 'A page must be reachable, indexable, and worth keeping',
            'keywords' => 'crawl, index, robots, sitemap, canonical',
            'lead' => 'A page can only earn a place in search if three things are true. A crawler can reach it. You have not asked for it to be kept out of the index. And the page is worth keeping because it answers a question better than a thin duplicate. Technical settings support that. They do not replace it.',
            'heading' => 'Reachable, allowed, and useful',
            'para' => 'Reachable means a normal link path leads there, and the page is not blocked by a login if it is meant to be public. A sitemap is a list of the URLs you care about. A robots rule is a gate: use it to keep out pages that should not be searched, such as a thank-you screen or a draft, not to hide your only answer. A canonical URL tells which address is the main one when the same page can be opened two ways. None of this helps a page with nothing to say.',
            'bullets' => [
                'Public answers should be linked from the site, not orphaned.',
                'Use a robots block for pages that should not appear, such as an internal search or a thank-you. Do not block the offer by accident.',
                'One canonical URL when the same content has two addresses. Pick the clean one and keep it.',
                'Speed and a stable layout are part of worth. A person who leaves before the headline loads never sees the answer. A local shop should also show the same name, address, and phone on the page and on its map listing.',
            ],
            'visual' => tf_flow('mkcrawl', 'Three gates', [['reach', 'a real link'], ['allow', 'not blocked'], ['worth', 'a true answer']], 'A sitemap cannot rescue a page you blocked or a page with nothing on it.'),
            'rows' => [
                ['Reach', 'Linked in the site', 'Not an orphan.'],
                ['Sitemap', 'A list you mean', 'Your important URLs.'],
                ['Robots', 'A gate', 'Thank-you, not the offer.'],
                ['Canonical', 'The main URL', 'One clean address.'],
                ['Worth', 'A real answer', 'Not a thin twin.'],
                ['Local', 'Same facts', 'Name, address, phone.'],
            ],
            'e1' => ['Example 1 — a public page with one clean address', 'The offer is linked, named, and not stuck behind a wall.', dm_page('Reachable', <<<'HTML'
<p style="font-size:12px;color:#64748b;">milklist.example/shared-list</p>
<h1 style="font-size:24px;margin:8px 0;">A shared household list</h1>
<p>This page is public. The headline is text. The next step is on the page.</p>
<p><a href="#start" style="display:inline-block;background:#059669;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — the answer is locked', 'Search cannot show a page the person is not allowed to read, and a visitor should not hit a wall before the promise.', dm_page('Blocked', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">Sign in to learn what this is</h1>
<p>The promise, the price, and the list are hidden until an account exists.</p>
HTML)],
            'micro' => [
                'A noindex on the thank-you page is sensible. A noindex on the offer is a mistake.',
                'Canonical does not merge two different articles. It names the main address of the same page.',
                'Fix a slow, jumping page because people feel it, not because of a score alone.',
            ],
            'quiz' => [
                $q('What three things does a public answer need?', 'To be reachable, allowed in the index, and worth keeping', 'A hidden keyword list, a timer, and a popup', 'A login wall, a slogan, and a badge', 'Fifty copies of the same paragraph'),
                $q('Which page is a fair use of a robots block?', 'A thank-you screen', 'The only page that explains the offer', 'Your main article', 'The price page'),
                $q('What does a canonical URL do?', 'Names the main address when one page opens two ways', 'Hides the page from people', 'Replaces the headline', 'Guarantees the first rank'),
            ],
            'practice' => 'Pick three URLs. For each, say how a visitor reaches it, whether it should be indexed, and which address is the main one. Fix any offer page that is blocked or unlinked.',
        ]),
        $L([
            'section' => 'Search',
            'title' => 'Search Console shows queries and pages, not vanity',
            'keywords' => 'search console, queries, impressions, clicks',
            'lead' => 'Search Console is the report of how search actually meets your pages. It shows queries, pages, clicks, impressions, and an average position. It is not a trophy case. Milk List uses it to find questions you almost answer: a query with many impressions and few clicks, or a page that appears for a topic you did not mean.',
            'heading' => 'Read the gap, then change the page',
            'para' => 'Start with a page you care about, not with the whole site. Look at the queries it already shows for. If the query matches the page, improve the title and the answer. If the query is the wrong intent, you may be appearing for a question this page should not own. Impressions mean you were shown. Clicks mean someone chose you. A high impression count with a poor title is a writing problem before it is anything else. Position is an average, not a prize.',
            'bullets' => [
                'Query plus page is the useful pair. A query alone can be noise.',
                'Many impressions and few clicks: rewrite the title and description so the invitation is clearer and truer.',
                'The wrong queries: clarify the page or let a better page own that topic.',
                'Do not chase a position number daily. Look for a pattern over weeks, then change one thing.',
            ],
            'visual' => tf_bars('mksc', 'One page, four queries', [
                ['label' => 'Shown', 'value' => 100, 'display' => '100', 'color' => '#94a3b8'],
                ['label' => 'Clicked', 'value' => 12, 'display' => '12', 'color' => '#059669'],
                ['label' => 'Right topic', 'value' => 70, 'display' => '70', 'color' => '#0284c7'],
                ['label' => 'Wrong topic', 'value' => 30, 'display' => '30', 'color' => '#d97706'],
            ], 'Shown is not a win. The gap between shown and clicked is a writing job.'),
            'rows' => [
                ['Query', 'What they typed', 'Family shopping list.'],
                ['Page', 'Which URL', '/shared-list'],
                ['Impressions', 'Times shown', 'Not a victory.'],
                ['Clicks', 'Times chosen', 'The result worked.'],
                ['Position', 'An average', 'Not a daily prize.'],
                ['Action', 'One change', 'Title or the answer.'],
            ],
            'e1' => ['Example 1 — a report you can act on', 'The pair is visible. The next edit is obvious: the title does not mention the household.', dm_page('A query report', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Page: /shared-list</h1>
<p><strong>Query.</strong> family shopping list</p>
<p><strong>Shown.</strong> 100 times. <strong>Clicks.</strong> 4.</p>
<p>The title says "Home". Change the title before you change anything else.</p>
HTML)],
            'e2' => ['Example 2 — a vanity reading', 'A big number with no query and no page cannot tell you what to edit.', dm_page('Vanity', <<<'HTML'
<h1 style="font-size:26px;margin:0 0 8px;">We got impressions</h1>
<p>The chart goes up. Nobody knows which question or which page.</p>
HTML)],
            'micro' => [
                'A new page needs time before the report is meaningful. Do not judge it tomorrow.',
                'Branded queries and problem queries tell different stories. Separate them.',
                'The fix is usually the page or the title, not a setting you toggle daily.',
            ],
            'quiz' => [
                $q('What is the useful pair in Search Console?', 'A query and a page', 'A single sitewide impression total', 'A follower count', 'A logo'),
                $q('What should you try when a page is shown often and clicked rarely?', 'Rewrite the title and the invitation', 'Publish a duplicate', 'Block the page', 'Add hidden keywords'),
                $q('What is average position?', 'An average, not a daily prize', 'A guarantee', 'The number of sales', 'The price'),
            ],
            'practice' => 'If you have a query report, pick one page. Write its top query, whether that query matches the page, and the one edit you will make. If you have no report yet, write the three queries you hope to see and the page that should own each.',
        ]),
        $L([
            'section' => 'Search',
            'title' => 'Thin pages and duplicate pages do not earn a place',
            'keywords' => 'thin content, duplicate pages, helpful page',
            'lead' => 'A thin page is a URL with almost nothing to say: a doorway that repeats a city name, a tag page with two lines, a slogan and a button copied from the offer without a new answer. A duplicate is two URLs teaching the same lesson. Search has little reason to keep either. People have less. Publish fewer pages and make each one an answer someone could thank you for.',
            'heading' => 'Fewer pages, each one an answer',
            'para' => 'Before you add a page, write the sentence it will answer that no current page answers. If you cannot, do not add it. Combining ten city doorways into one true page about who you serve is better than ten empty ones. If two posts say the same thing, keep the better one and point the other URL at it if people already use that address. Taking someone else\'s page and swapping adjectives is not research. It is a copy, and it does not earn a place.',
            'bullets' => [
                'Each URL needs its own answer. A swapped city name is not a new answer.',
                'Short is fine when the answer is short. Thin means empty, not brief.',
                'Duplicates compete with you. Choose one and make it complete.',
                'Experience belongs on the page: what you saw when households tried the list, in words you have the right to say.',
            ],
            'visual' => tf_split('mkthin', 'A new answer?', 'No, same lesson', 'Improve that URL', 'Do not add a twin', 'Yes, distinct', 'Publish it', 'A doorway with only a city name is not an answer.'),
            'rows' => [
                ['Thin', 'Nothing to say', 'A slogan and a city.'],
                ['Brief', 'Still useful', 'A direct price page.'],
                ['Duplicate', 'Same lesson', 'Two URLs, one answer.'],
                ['Doorway', 'Swapped names', 'Not a strategy.'],
                ['Fix', 'Merge or improve', 'One complete page.'],
                ['Standard', 'Worth thanking', 'A real answer.'],
            ],
            'e1' => ['Example 1 — brief and complete', 'Short, but it answers. This is not thin.', dm_page('A complete short page', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">What Milk List costs</h1>
<p>Rs 499 a year for one household list. You start today. You can leave when you want. The list stays on the screen so milk is bought once.</p>
HTML)],
            'e2' => ['Example 2 — a doorway', 'Only the city changed. There is no answer.', dm_page('A doorway', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">Shopping list in Cityville</h1>
<p>We offer shopping list in Cityville. Best shopping list Cityville.</p>
HTML)],
            'micro' => [
                'A tag or date archive with no introduction is a thin page you may not need in the index.',
                'Guest posts you do not stand behind are still your reputation if they sit on your site.',
                'Write from what you know. A page that could have been about any product helps no one.',
            ],
            'quiz' => [
                $q('What makes a page thin?', 'It has almost nothing to say', 'It is shorter than a novel', 'It names a price', 'It has one clear H1'),
                $q('What should you do with two URLs that teach the same lesson?', 'Keep the better one and make it complete', 'Add more adjectives to both', 'Publish ten city versions', 'Hide keywords on both'),
                $q('Which page can be short and still be worth keeping?', 'A direct price page that states the real terms', 'A page that only swaps a city name', 'A page that repeats another site', 'An empty tag page'),
            ],
            'practice' => 'Open your last five URLs. For each, write the unique answer in one sentence. Merge or remove any URL that cannot produce that sentence.',
        ]),
    ];
}
