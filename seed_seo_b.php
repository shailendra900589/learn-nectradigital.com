<?php
declare(strict_types=1);

function seo_chapters_b(): array
{
    $L = static function (array $c): array {
        return track_lesson('visual', '', $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };

    return [
        $L([
            'section' => 'The result',
            'title' => 'The title is the blue line in the result',
            'keywords' => 'title tag, seo title, blue link',
            'lead' => 'The title is the line people see first in the result, and the name of the tab. For Milk List it should say the topic and the brand, with the topic first, because the end of the line may be cut.',
            'heading' => 'Specific, unique, and true',
            'para' => 'Write a different title for every page. "Home" wastes the line. Put the phrase a person would recognize near the start. Aim for a line they can read in one glance, roughly fifty to sixty characters. The brand can close the line. Do not shout, and do not claim a superlative you cannot support.',
            'bullets' => [
                'One title per page, faithful to the visible heading.',
                'Important words first. Brand at the end when the query is not your name.',
                'About fifty to sixty characters is a useful aim, not a spell.',
                'No pile of cities, no fake "best", no all-caps.',
            ],
            'visual' => tf_panels('seo15', 'The blue line', 'Useful', ['Topic first', 'Brand last', 'About one glance'], 'Wasted', ['Home', 'Best best best', 'A different promise'], 'The title is a claim about the page.'),
            'rows' => [
                ['Place', 'The result', 'The blue line.'],
                ['Also', 'The tab', 'Name the page.'],
                ['Order', 'Topic, then brand', 'Shared list | Milk List'],
                ['Length', 'One glance', 'About 50 to 60.'],
                ['Unique', 'This page only', 'Not "Home".'],
                ['True', 'Matches the page', 'No bait.'],
            ],
            'e1' => ['Example 1 — topic first, brand last', 'A person can tell what will open.', seo_result('Shared household list that stays on screen | Milk List', 'A shared shopping list for a household. The list stays on the screen so milk is bought once. Rs 499 a year, and you can leave when you want.')],
            'e2' => ['Example 2 — the line says nothing', 'The tab and the result waste the chance.', seo_result('Home', 'Welcome to our website. Click to learn more about our solutions and our journey.')],
            'micro' => [
                'If the title and the H1 disagree, the visitor feels misled when the page opens.',
                'Changing titles every day chases noise. Change them when the promise is wrong.',
                'The title is visible. It is not a place to hide extra words.',
            ],
            'quiz' => [
                $q('What should lead the title?', 'The topic the person recognizes', 'The word Home', 'A fake superlative', 'A stack of cities'),
                $q('About how long is a readable title?', 'Roughly fifty to sixty characters', 'Three words or three hundred', 'One character', 'Whatever fits if you shout'),
                $q('When should you change a title?', 'When the promise or the query is wrong', 'Every morning', 'Whenever a tool says so, blindly', 'Never, even if it says Home'),
            ],
            'practice' => 'Write titles for your five main pages. Count the characters. Put the topic first. Check that each visible heading agrees.',
        ]),
        $L([
            'section' => 'The result',
            'title' => 'The description is the invitation, not the rank',
            'keywords' => 'meta description, search snippet',
            'lead' => 'The description is the sentence under the blue line. It does not buy a place. It helps a person decide to click. Milk List uses it to say what the list does, what it costs, and that they can leave.',
            'heading' => 'A true second sentence',
            'para' => 'Write a description that continues the title instead of repeating it. About 120 to 160 characters is enough to be specific and short enough for a phone. Include a fact: one shared list, Rs 499 a year. Do not promise a discount the page does not have. Search may rewrite the line. That is a reason to make the page itself clear, not a reason to stuff the description.',
            'bullets' => [
                'It invites. It does not rank you by itself.',
                'Add a fact the title did not fit.',
                'Match the page. A bait description is still bait.',
                'If search rewrites it, improve the visible page rather than arguing with the rewrite.',
            ],
            'visual' => tf_split('seo16', 'The gray line', 'Does it add a fact?', 'Yes', 'It can earn the click', 'It repeats or baits', 'Rewrite it', 'The description is an invitation.'),
            'rows' => [
                ['Job', 'Invite the click', 'Not the rank itself.'],
                ['Length', 'One breath', 'About 120 to 160.'],
                ['Fact', 'New information', 'Rs 499, one list.'],
                ['Match', 'The page', 'Same promise.'],
                ['Rewrite', 'Possible', 'Clarify the page.'],
                ['Stuff', 'No', 'A person can see it.'],
            ],
            'e1' => ['Example 1 — the sentence adds the price and the exit', 'The title named the product. This line helps a decision.', seo_result('Shared household list that stays on screen | Milk List', 'A shared shopping list for a household. The list stays on the screen so milk is bought once. Rs 499 a year, and you can leave when you want.')],
            'e2' => ['Example 2 — a shout that repeats the title', 'Nothing new, and a claim the page cannot keep.', seo_result('Best list', 'Best list best list. Free forever. Guaranteed number one. Click now.')],
            'micro' => [
                'Every important page deserves its own description. A sitewide sentence is a missed invitation.',
                'Do not hide the only version of the offer in the description and leave it off the page.',
                'If you cannot say why a person should click, the page may not be ready.',
            ],
            'quiz' => [
                $q('What is the description for?', 'To help a person decide to click', 'To buy a higher place', 'To hide keywords', 'To replace the page'),
                $q('What should it add?', 'A fact the title did not fit', 'The title again', 'A fake discount', 'A pile of cities'),
                $q('If the shown line is rewritten, what do you fix first?', 'The clarity of the page', 'A secret field', 'The logo', 'A bought vote'),
            ],
            'practice' => 'Write a description that adds one fact beyond the title. Read it next to the first screen. Delete any promise the page does not keep.',
        ]),
        $L([
            'section' => 'The result',
            'title' => 'The heading on the page must agree with the title',
            'keywords' => 'h1, title tag match, on page heading',
            'lead' => 'The title is the promise in the result. The H1 is the promise on the page. Milk List uses the same idea in both: a shared list that stays. A person who clicked should feel they arrived, not that they were switched.',
            'heading' => 'One promise, two places',
            'para' => 'They do not have to be character-for-character identical. They have to be the same offer. If the title says shared list and the H1 says welcome to our platform, you broke the click. One H1 is enough. It should be visible text, high on the page, and specific.',
            'bullets' => [
                'Same offer in the title and the H1.',
                'One H1. Other headings are smaller questions.',
                'The H1 is text, not only an image.',
                'If you change one, reread the other.',
            ],
            'visual' => tf_panels('seo17', 'Two places, one promise', 'Result', ['Shared list', 'Stays on screen', 'Milk List'], 'Page', ['One list the house sees', 'Add it once', 'Start'], 'The click should feel like an arrival.'),
            'rows' => [
                ['Title', 'The result', 'Shared list that stays.'],
                ['H1', 'The page', 'One list the house can see.'],
                ['Same', 'The offer', 'Not a new topic.'],
                ['Count', 'One H1', 'Not three.'],
                ['Form', 'Text', 'Not only a picture.'],
                ['Edit', 'Together', 'Change both or neither.'],
            ],
            'e1' => ['Example 1 — the page continues the result', 'The words are not identical. The offer is.', seo_page('Arrival', <<<'HTML'
<h1 style="font-size:26px;margin:0 0 8px;">One list the whole house can see</h1>
<p>The result said a shared list that stays. This is that list. Rs 499 a year.</p>
HTML)],
            'e2' => ['Example 2 — the click changes the subject', 'The title sold a list. The page welcomes a stranger.', seo_page('A switch', <<<'HTML'
<h1 style="font-size:26px;margin:0 0 8px;">Welcome to the platform</h1>
<p>The result promised a shared shopping list. This heading does not mention one.</p>
HTML)],
            'micro' => [
                'A logo is not an H1.',
                'Question headings are fine when they are the reader\'s question.',
                'Do not paste the title into the H1 if it makes the page sound like a result instead of a product.',
            ],
            'quiz' => [
                $q('What must the H1 share with the title?', 'The same offer', 'Every character', 'A different product', 'Nothing, so the page feels new'),
                $q('How many H1 headings does the page need?', 'One', 'One per keyword', 'None, only a logo', 'Ten, to be safe'),
                $q('Why is an image-only heading weak?', 'The promise can fail to load and is harder to read as text', 'Images are always invisible', 'Headings are illegal', 'Titles cannot exist'),
            ],
            'practice' => 'Put the title and the H1 in two lines. Underline the shared offer. If you cannot underline anything, rewrite the one that drifted.',
        ]),
        $L([
            'section' => 'The page',
            'title' => 'The first screen answers the query',
            'keywords' => 'above the fold, first screen, seo content',
            'lead' => 'The first screen is what a person sees before they scroll, especially on a phone. Milk List puts the shared list, the price, and the next step there. A manifesto can wait.',
            'heading' => 'Answer before you introduce the company',
            'para' => 'Asha typed a problem. She should see the object, the outcome, and one action without hunting. Supporting detail, a comparison, and proof can follow. If the first screen is a slider of slogans, the query is still unanswered.',
            'bullets' => [
                'Name the thing and the outcome above the fold.',
                'One action. The same action the page exists for.',
                'The price belongs early when the query is a decision.',
                'Move the company story below the answer.',
            ],
            'visual' => tf_flow('seo18', 'First screen', [['thing', 'a shared list'], ['outcome', 'milk once'], ['action', 'start']], 'The company story can follow. It cannot lead.'),
            'rows' => [
                ['Thing', 'Named', 'A shared list.'],
                ['Outcome', 'Plain', 'Bought once.'],
                ['Price', 'If they are deciding', 'Rs 499.'],
                ['Action', 'One', 'Start the list.'],
                ['Later', 'Story and detail', 'Below.'],
                ['Slider', 'Usually no', 'It delays the answer.'],
            ],
            'e1' => ['Example 1 — the answer is immediate', 'No scroll required to know the product.', seo_page('First screen', <<<'HTML'
<p style="font-size:12px;letter-spacing:.08em;color:#0f766e;font-weight:700;">FAMILY SHOPPING LIST</p>
<h1 style="font-size:26px;margin:8px 0;">One list the whole house can see</h1>
<p>Rs 499 a year. Milk is added once.</p>
<p><a href="#start" style="display:inline-block;background:#0f766e;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — the answer is below a slogan', 'The query is still open.', seo_page('A slider', <<<'HTML'
<h1 style="font-size:26px;margin:0 0 8px;">Welcome, dreamer</h1>
<p>Swipe through our values. The list, the price, and the button are three screens down.</p>
HTML)],
            'micro' => [
                'Check the first screen on a phone, not only on a wide monitor.',
                'A video that autoplays without words is not an answer.',
                'If a popup covers the answer, the first screen has failed.',
            ],
            'quiz' => [
                $q('What belongs on the first screen?', 'The thing, the outcome, and one action', 'Only the company history', 'Three equal slogans', 'A popup that hides the page'),
                $q('When should the price appear early?', 'When the query is a decision', 'Never', 'Only in an image', 'Only after a login'),
                $q('Where does the company story go?', 'After the answer', 'Instead of the answer', 'In the title only', 'Nowhere, delete the product'),
            ],
            'practice' => 'Open the page on a phone-sized window. If you cannot name the product and the next step without scrolling, move those lines up.',
        ]),
        $L([
            'section' => 'The page',
            'title' => 'Headings let a person scan',
            'keywords' => 'heading structure, h2, h3, outline',
            'lead' => 'Headings are a table of contents for a person in a hurry. Milk List uses them to mark real sections: what it is, what it costs, how a house starts. They are not a place to repeat the query.',
            'heading' => 'An outline a stranger can follow',
            'para' => 'H1 is the page. H2s are the sections. H3s are parts of a section. Do not skip around to make a word look important, and do not make every sentence a heading. A heading should tell you whether the section is worth reading. If you removed the paragraphs, the headings should still sketch the page.',
            'bullets' => [
                'One H1, then H2s for sections, H3s inside them.',
                'Each heading is a claim or a question the section answers.',
                'Do not stuff headings with repeats.',
                'The outline should make sense alone.',
            ],
            'visual' => tf_flow('seo19', 'The outline', [['h1', 'the page'], ['h2', 'a section'], ['h3', 'a part']], 'If the headings do not sketch the page, they are decoration.'),
            'rows' => [
                ['H1', 'The promise', 'One shared list.'],
                ['H2', 'A section', 'What it costs.'],
                ['H3', 'A part', 'A yearly price.'],
                ['Order', 'Do not skip for effect', 'Nest for real.'],
                ['Scan', 'Headings alone', 'They should sketch.'],
                ['Stuff', 'No', 'A heading is a label.'],
            ],
            'e1' => ['Example 1 — headings that sketch the page', 'A stranger knows the sections.', seo_page('Outline', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">One shared list</h1>
<p style="font-weight:800;">What the house gets</p>
<p>One list, on every phone in the house.</p>
<p style="font-weight:800;">What it costs</p>
<p>Rs 499 a year. Leave when you want.</p>
HTML)],
            'e2' => ['Example 2 — headings that are the same sentence', 'Nothing can be scanned.', seo_page('Repeats', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Shopping list app</h1>
<p style="font-weight:800;">Shopping list app India</p>
<p style="font-weight:800;">Best shopping list app</p>
<p>The sections are not sections. They are repeats.</p>
HTML)],
            'micro' => [
                'Bold text is not a heading. Use a real heading when it is a section.',
                'Questions work when the section answers them.',
                'A heading that promises a comparison must be followed by a comparison.',
            ],
            'quiz' => [
                $q('What is an H2 for?', 'A real section of the page', 'Another copy of the query', 'A hidden keyword', 'The logo'),
                $q('What should headings do alone?', 'Sketch the page', 'Repeat one phrase', 'Replace the paragraphs with nothing', 'Confuse the outline'),
                $q('What is wrong with skipping levels to look important?', 'The outline stops meaning what it says', 'Nothing', 'It improves the price', 'It fetches the page'),
            ],
            'practice' => 'List only your headings, in order. If a stranger cannot guess the page, rename the headings before you add more paragraphs.',
        ]),
        $L([
            'section' => 'The page',
            'title' => 'Say the topic plainly, do not stuff it',
            'keywords' => 'keyword stuffing, natural language, on page seo',
            'lead' => 'Use the topic in the places a person expects: the title, the H1, and a few sentences. Milk List says "shared list" where it helps understanding. It does not nail the phrase to every line.',
            'heading' => 'Once where it helps, then write like a person',
            'para' => 'Repeating a phrase does not make a page more about that phrase. It makes the page harder to read, and it is a pattern people and search both learn to distrust. Use a natural variant when you would say it. Then talk about the actual details: who adds the item, what the shopper sees, what it costs.',
            'bullets' => [
                'The topic appears where a person would look for it.',
                'Variants are fine. A pile is not.',
                'Details beat repeats.',
                'If a sentence exists only to hold the phrase, delete the sentence.',
            ],
            'visual' => tf_split('seo20', 'Another repeat?', 'Does the sentence inform?', 'Yes', 'Keep it', 'It only holds the phrase', 'Delete it', 'Details make a page about the topic. Repeats do not.'),
            'rows' => [
                ['Title', 'Once, clearly', 'Shared list.'],
                ['H1', 'The promise', 'One list.'],
                ['Body', 'Details', 'Who sees it.'],
                ['Variant', 'Natural', 'Grocery list.'],
                ['Pile', 'Delete', 'List list list.'],
                ['Test', 'Read aloud', 'Stumble means cut.'],
            ],
            'e1' => ['Example 1 — the topic, then the details', 'A person learns something.', seo_page('Details', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">A shared shopping list</h1>
<p>The adult who shops sees one list. Milk added at night is still there in the morning. Rs 499 a year.</p>
HTML)],
            'e2' => ['Example 2 — the phrase is the whole page', 'Nothing is explained.', seo_page('A pile', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Shared list shared list</h1>
<p>Shared list for shared list users who want a shared list shared list.</p>
HTML)],
            'micro' => [
                'Putting the phrase in the URL, the title, and the H1 is enough of a signal. The body should teach.',
                'A footnote of keywords helps nobody.',
                'Synonyms you would never say are still stuffing.',
            ],
            'quiz' => [
                $q('Where should the topic appear?', 'Where a person expects it, then in real details', 'In every sentence', 'Only in a hidden note', 'Only in an image'),
                $q('What beats a repeat?', 'A concrete detail', 'A louder adjective', 'A second copy of the sentence', 'A longer pile'),
                $q('What do you do with a sentence that only holds the phrase?', 'Delete it', 'Add it again', 'Hide it', 'Buy a vote for it'),
            ],
            'practice' => 'Highlight every use of your main phrase. Keep the ones that help a sentence. Delete the ones that exist only to be counted.',
        ]),
        $L([
            'section' => 'The page',
            'title' => 'An image needs a true description',
            'keywords' => 'alt text, image seo, descriptive images',
            'lead' => 'An image can show the list on a phone. The description is the text for someone who cannot see the image, and for a fetch that reads text. Milk List describes the picture. It does not dump queries into it.',
            'heading' => 'Say what the picture shows',
            'para' => 'If the image is the list on one screen, the description says that. If the image is decorative, it does not need a fake sentence. The file name can be plain words too, but the description is what a person hears. Do not write "shared list shared list" as the description of a logo.',
            'bullets' => [
                'Describe the information in the image.',
                'Decorative images stay quiet.',
                'The page must still make sense if the image fails.',
                'A description is not a second keyword pile.',
            ],
            'visual' => tf_split('seo21', 'Does the image inform?', 'Yes', 'Describe it', 'What a person would see', 'No', 'Leave it quiet', 'A logo does not need a paragraph of queries.'),
            'rows' => [
                ['Informs', 'Say it', 'One list on a phone.'],
                ['Decorates', 'Stay quiet', 'A flourish.'],
                ['Fails', 'Text remains', 'The promise is in words.'],
                ['Name', 'Plain file', 'shared-list-phone.'],
                ['Pile', 'No', 'Not a query dump.'],
                ['Logo', 'Short or quiet', 'The name is enough.'],
            ],
            'e1' => ['Example 1 — the description matches the picture', 'A person who cannot see it still gets the point.', seo_page('A described picture', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">The list on one phone</h1>
<p style="font-size:13px;color:#0f766e;">Image: a phone showing one shared list with milk at the top.</p>
<p>The words already say the same thing, so a missing image does not hide the offer.</p>
HTML)],
            'e2' => ['Example 2 — the description is a dump', 'It does not describe a picture.', seo_page('A dump', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Our logo</h1>
<p style="font-size:13px;color:#b45309;">Image text: best shared list app India buy now cheap list list.</p>
HTML)],
            'micro' => [
                'Charts and screenshots need a description more than stock smiles do.',
                'Do not put the only price inside an image.',
                'A huge image that pushes the answer below the fold fights the first screen.',
            ],
            'quiz' => [
                $q('What does a useful image description do?', 'Say what the picture shows', 'Repeat a query with no picture in mind', 'Hide text', 'Replace the H1'),
                $q('What about a decorative image?', 'It can stay quiet', 'It needs fifty keywords', 'It should hold the only price', 'It should be the H1'),
                $q('Why must the promise also be text?', 'The image may not load, and some people do not see it', 'Text is optional', 'Images always replace sentences', 'Descriptions are secret'),
            ],
            'practice' => 'For each image on the offer page, write a one-line description or mark it decorative. Move any fact that exists only in an image into the text.',
        ]),
        $L([
            'section' => 'The page',
            'title' => 'The address should say the topic',
            'keywords' => 'url slug, readable url, permalink',
            'lead' => 'The URL is a small label in the result and a thing people paste. milklist.example/shared-list says the topic. milklist.example/p?id=8841 does not.',
            'heading' => 'A short, stable, human address',
            'para' => 'Use a few real words, separated plainly, in lowercase. Do not stuff a sentence of queries into the path. Choose it once. Changing it later means a redirect and a period where the old links must keep working. Dates and internal ids do not help a person.',
            'bullets' => [
                'Words a person can read.',
                'Short. The topic, not a paragraph.',
                'Stable. Change the page, not the address, when you can.',
                'If you must move it, redirect the old one.',
            ],
            'visual' => tf_panels('seo22', 'The path', 'Readable', ['/shared-list', 'Says the topic', 'Stays put'], 'Opaque', ['/p?id=8841', 'Says nothing', 'Hard to paste'], 'Pick the path once.'),
            'rows' => [
                ['Words', 'Human', 'shared-list'],
                ['Short', 'The topic', 'Not a sentence of queries.'],
                ['Case', 'Plain', 'Lowercase.'],
                ['Stable', 'Keep it', 'Edits do not need a new path.'],
                ['Id', 'Avoid', 'Numbers are not a topic.'],
                ['Move', 'Redirect', 'The old path still opens.'],
            ],
            'e1' => ['Example 1 — the path is the topic', 'Someone can read it in the result.', seo_result('Shared household list that stays on screen | Milk List', 'A shared shopping list for a household. The list stays on the screen so milk is bought once.')],
            'e2' => ['Example 2 — the path is a drawer number', 'The result looks careless.', seo_page('A drawer', <<<'HTML'
<p style="font-size:13px;color:#64748b;">milklist.example/p?id=8841&ref=x</p>
<h1 style="font-size:22px;margin:8px 0;">A page</h1>
<p>The address tells you nothing, and it may change tomorrow.</p>
HTML)],
            'micro' => [
                'Stop words can often go. Meaning should stay.',
                'A folder structure can reflect the site map. It should not become a maze.',
                'Do not add a new path every time you tweak a sentence.',
            ],
            'quiz' => [
                $q('What makes a useful path?', 'A few words that say the topic', 'A private id', 'A full sentence of queries', 'A new path every edit'),
                $q('When you improve the paragraph, what happens to the path?', 'It usually stays', 'It must change', 'It should become a date', 'It should be removed'),
                $q('If you must change the path, what else do you do?', 'Redirect the old path', 'Leave the old links broken', 'Hide the page', 'Buy a mention'),
            ],
            'practice' => 'Read your five main paths out loud. Rewrite any that are ids or stuffed sentences. List any old path that would need a redirect.',
        ]),
        $L([
            'section' => 'The page',
            'title' => 'The page still has one next step',
            'keywords' => 'call to action, seo landing, conversion and seo',
            'lead' => 'A page can rank and still fail. Milk List\'s offer page has one next step: start the list. SEO that brings a person to five equal buttons has finished only half the job.',
            'heading' => 'The visit needs a place to go',
            'para' => 'The step must be the one the query implied. A learn page might step to the offer. A decide page steps to start. The words on the button match the sentence above them. A second, quieter link is fine for a real alternative. A row of competing buttons is not a step.',
            'bullets' => [
                'One primary action, named in plain words.',
                'The action matches the intent of the query.',
                'The button is visible with the answer, not only in a footer.',
                'Count the action, not only the click from search.',
            ],
            'visual' => tf_flow('seo23', 'After the answer', [['answer', 'one list'], ['step', 'start'], ['count', 'starts']], 'A visit with no step is a report of traffic, not a result.'),
            'rows' => [
                ['Step', 'One', 'Start the list.'],
                ['Match', 'The query', 'Decide means start.'],
                ['Words', 'Specific', 'Not "submit".'],
                ['Place', 'With the answer', 'On the first screen.'],
                ['Rival', 'No row of five', 'One primary.'],
                ['Count', 'The action', 'Lists started.'],
            ],
            'e1' => ['Example 1 — the step is the one the page promised', 'Start means start the list.', seo_page('One step', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">One shared list</h1>
<p>Rs 499 a year.</p>
<p><a href="#start" style="display:inline-block;background:#0f766e;color:#fff;text-decoration:none;padding:12px 16px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — five doors', 'The visit has nowhere to land.', seo_page('Five doors', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Welcome</h1>
<p><a href="#a">Learn</a> · <a href="#b">Blog</a> · <a href="#c">Jobs</a> · <a href="#d">Quiz</a> · <a href="#e">Shop</a></p>
HTML)],
            'micro' => [
                'A learn page can have a softer step than a decide page. It still has one.',
                '"Click here" does not say what happens.',
                'If the step needs an account, say that before the person commits.',
            ],
            'quiz' => [
                $q('What is the next step on the offer page?', 'Start the list', 'Five equal buttons', 'A manifesto', 'Nothing, so the rank stays pure'),
                $q('What should you count besides the search click?', 'The action the page asked for', 'Only impressions', 'Only the logo views', 'Nothing'),
                $q('Where does the primary action sit?', 'With the answer', 'Only in a footer after a long essay', 'Inside an image with no text', 'On a different domain with a different promise'),
            ],
            'practice' => 'Name the one action for the offer page and for one learn page. If they are the same word, check whether the pages are actually the same job.',
        ]),
        $L([
            'section' => 'The page',
            'title' => 'A slow page loses the person who arrived',
            'keywords' => 'page speed, core web vitals, loading',
            'lead' => 'Speed is a person waiting. If the Milk List page spends its first seconds on huge images and extra scripts, Asha leaves before she sees the price. A fast page is part of being a good answer.',
            'heading' => 'Show the answer first',
            'para' => 'You do not need a lab score to start. Open the page on a phone on an ordinary connection and notice what appears. The text of the offer should arrive before decorative video. Compress the real screenshot. Do not load a pile of tools you are not using. A score without a better experience is a vanity number.',
            'bullets' => [
                'The words of the offer appear early.',
                'Images are as small as they can be and still clear.',
                'Unused extras do not ride along.',
                'Judge the wait as a person, then read the measurement.',
            ],
            'visual' => tf_flow('seo24', 'What arrives', [['text', 'the offer'], ['image', 'the phone'], ['extra', 'only if used']], 'A score that does not change the wait is not the goal.'),
            'rows' => [
                ['First', 'The sentence', 'Before the decoration.'],
                ['Image', 'Compressed', 'Still recognizable.'],
                ['Script', 'Needed only', 'No unused pile.'],
                ['Phone', 'The test', 'Ordinary connection.'],
                ['Video', 'Not automatic', 'It can wait.'],
                ['Score', 'A clue', 'Not the product.'],
            ],
            'e1' => ['Example 1 — the offer is the first thing', 'Text does not wait on a film.', seo_page('Fast answer', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">One shared list</h1>
<p>Rs 499 a year. The words are on the page immediately. The picture of the phone is small and optional.</p>
HTML)],
            'e2' => ['Example 2 — a film before the sentence', 'The person is still waiting.', seo_page('A long intro', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Please wait</h1>
<p>A full-screen film plays first. The price arrives after it, if the person is still there.</p>
HTML)],
            'micro' => [
                'A heavy font for one heading is a poor trade.',
                'Third-party widgets often cost more wait than they return.',
                'Improve the template once. Every page inherits it.',
            ],
            'quiz' => [
                $q('What is speed, in this course?', 'A person waiting to see the answer', 'A trophy number with no change in the page', 'A reason to hide text', 'An ad'),
                $q('What should arrive first?', 'The words of the offer', 'An unused pile of scripts', 'A full-screen film', 'A popup'),
                $q('How do you start testing?', 'Open the page on a phone and notice the wait', 'Buy a vote', 'Add more images', 'Ignore the phone'),
            ],
            'practice' => 'Load the offer page on a phone. Write down what you see in the first moment. If it is not the offer, remove or delay whatever blocked it.',
        ]),
        $L([
            'section' => 'The page',
            'title' => 'The page has to work on a phone',
            'keywords' => 'mobile seo, responsive page, phone layout',
            'lead' => 'Asha will meet this page on a phone. A layout that only works on a wide desk is a different, worse page. The text, the price, and the button have to fit the hand.',
            'heading' => 'The phone is the real first screen',
            'para' => 'No sideways scroll to read a sentence. No button smaller than a thumb. No popup that cannot be closed. The same words as the desk page, in an order that still answers the query. A separate thin mobile site with less information is a second, weaker answer. Prefer one page that adapts.',
            'bullets' => [
                'Readable text without pinching.',
                'The action is easy to tap.',
                'Nothing important sits only on the wide layout.',
                'Popups must be easy to dismiss.',
            ],
            'visual' => tf_panels('seo25', 'One page, two widths', 'Phone', ['Answer first', 'Thumb-size step', 'No sideways scroll'], 'Wide desk', ['Same words', 'More room', 'Not a secret'], 'A thinner mobile site is a second, weaker answer.'),
            'rows' => [
                ['Text', 'Readable', 'No pinch.'],
                ['Button', 'A thumb', 'Start the list.'],
                ['Scroll', 'Down, not sideways', 'Sentences fit.'],
                ['Same', 'One URL', 'Not a lesser twin.'],
                ['Popup', 'Closable', 'It must not trap.'],
                ['Test', 'Your phone', 'Not only a resized window.'],
            ],
            'e1' => ['Example 1 — the phone still has the answer', 'The step is tappable.', seo_page('On a phone', <<<'HTML'
<h1 style="font-size:24px;margin:0 0 8px;">One shared list</h1>
<p>Rs 499 a year. The line fits the screen.</p>
<p><a href="#start" style="display:inline-block;background:#0f766e;color:#fff;text-decoration:none;padding:14px 18px;border-radius:10px;font-weight:700;">Start the list</a></p>
HTML)],
            'e2' => ['Example 2 — a wide table is the whole page', 'A phone cannot read it.', seo_page('Sideways', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Please rotate</h1>
<p>The only explanation is a wide table. The button is a tiny mark in the corner.</p>
HTML)],
            'micro' => [
                'Tap targets that sit too close cause the wrong step.',
                'Sticky bars can cover the button. Check the end of the first screen.',
                'If you use a separate mobile URL, it must carry the same answer and a clear relationship to the main one.',
            ],
            'quiz' => [
                $q('What should a phone page share with the desk page?', 'The same answer and the same step', 'Less information, as a rule', 'A sideways table', 'A different promise'),
                $q('What makes a button fail on a phone?', 'It is too small or covered', 'It says what will happen', 'It sits with the answer', 'It is easy to see'),
                $q('What is a sideways scroll a sign of?', 'The layout does not fit the hand', 'A good mobile page', 'A strong title', 'A needed sitemap'),
            ],
            'practice' => 'Open the offer on your phone. Try to tap the step without zooming. Note anything you have to slide sideways to read, and fix that first.',
        ]),
        $L([
            'section' => 'The site',
            'title' => 'A site is a map of topics',
            'keywords' => 'site architecture, information architecture, seo structure',
            'lead' => 'A site is not a pile of URLs. It is a map: home, the offer, the comparisons, the help. Milk List should let a person and a fetch see how those topics relate.',
            'heading' => 'Few sections, obvious parents',
            'para' => 'Group pages by the job they do. The offer lives in one place. Help articles live together and point back. Do not invent a deep chain of folders so a page is six clicks away. A flat mess with no parents is just as confusing. Name the sections the way a person would.',
            'bullets' => [
                'Each important page has an obvious parent.',
                'Important pages stay close to home.',
                'Sections match intents, not your org chart.',
                'A new page has to say which section it belongs to.',
            ],
            'visual' => tf_flow('seo26', 'The map', [['home', 'the site'], ['offer', 'the list'], ['help', 'narrower jobs']], 'A page without a parent is easy to orphan.'),
            'rows' => [
                ['Home', 'The map', 'Points at the offer.'],
                ['Offer', 'One parent', 'The product.'],
                ['Help', 'Children', 'Add an item, the price.'],
                ['Depth', 'Shallow', 'Two clicks, not six.'],
                ['Names', 'Human', 'Not internal codes.'],
                ['New page', 'A parent', 'Or do not publish.'],
            ],
            'e1' => ['Example 1 — the map is visible', 'A person knows where they are.', seo_linkmap('Map', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Milk List</h1>
<p><a href="#offer" style="color:#0f766e;font-weight:700;">Shared list</a></p>
<p><a href="#help" style="color:#0f766e;font-weight:700;">How a house uses it</a></p>
HTML)],
            'e2' => ['Example 2 — a drawer of unrelated URLs', 'No parent, no path.', seo_page('A pile of URLs', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Pages</h1>
<p>/a14 /tmp /new-final-2 /list-copy /misc</p>
<p>Nothing says which one is the offer.</p>
HTML)],
            'micro' => [
                'Navigation is a promise. If a label says Pricing, the page must be the price.',
                'Orphan pages are usually a sign the map was skipped.',
                'Do not build a section you will not maintain.',
            ],
            'quiz' => [
                $q('What is a site, for SEO?', 'A map of topics', 'A pile of URLs', 'A single page that says everything', 'A folder of drafts'),
                $q('How close should the offer be to home?', 'A click or two', 'Six folders down', 'Unlinked', 'Only in a sitemap'),
                $q('What does a new page need?', 'A parent section and a reason', 'A random path', 'A copied title', 'No links, so it stays special'),
            ],
            'practice' => 'Draw home, the offer, and up to five other pages. Draw the links you mean. Any page you cannot connect is not ready to publish.',
        ]),
        $L([
            'section' => 'The site',
            'title' => 'Internal links show the next useful page',
            'keywords' => 'internal links, anchor text, related pages',
            'lead' => 'An internal link is a recommendation you can stand behind. From a help page, Milk List links to the offer when the next useful step is to start. The words of the link say the destination.',
            'heading' => 'Link the way you would point in a room',
            'para' => 'Use the name of the destination: "the shared list", "what it costs". "Click here" throws away the chance to say where the door goes. Do not link every sentence to every page. A few true next steps beat a block of repeated links. The offer should receive links from pages that genuinely lead there.',
            'bullets' => [
                'The link text names the destination.',
                'The destination is the next useful page, not a quota.',
                'Important pages receive links from relevant parents.',
                'A footer dump of every URL is not a recommendation.',
            ],
            'visual' => tf_flow('seo27', 'A useful link', [['this page', 'a narrower job'], ['words', 'the shared list'], ['next', 'the offer']], 'Point the way you would point for a person in the room.'),
            'rows' => [
                ['Text', 'The destination', 'The shared list.'],
                ['Reason', 'Next step', 'Not a quota.'],
                ['Parent', 'A real lead-in', 'The help page.'],
                ['Dump', 'Weak', 'Every URL in the footer.'],
                ['Here', 'Vague', 'Say the name.'],
                ['Receive', 'The offer', 'From relevant pages.'],
            ],
            'e1' => ['Example 1 — the words name the door', 'You know what opens.', seo_linkmap('A clear door', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Add milk the night before</h1>
<p>When the house is ready, open <a href="#offer" style="color:#0f766e;font-weight:700;">the shared list</a>.</p>
HTML)],
            'e2' => ['Example 2 — a block that points everywhere', 'No recommendation is visible.', seo_linkmap('A dump', <<<'HTML'
<h1 style="font-size:22px;margin:0 0 8px;">Links</h1>
<p><a href="#a">here</a> <a href="#b">here</a> <a href="#c">here</a> <a href="#d">here</a> <a href="#e">here</a></p>
HTML)],
            'micro' => [
                'The first link to a destination can be the one that gets followed. Make it meaningful.',
                'Do not point a link at a page you hope to inflate if it does not help this reader.',
                'Broken internal links are doors to nowhere. Check them.',
            ],
            'quiz' => [
                $q('What should link text do?', 'Name the destination', 'Say "click here" every time', 'Repeat the same word fifty times', 'Hide the destination'),
                $q('Which link is a real recommendation?', 'From the help page to the shared list, when that is the next step', 'Every URL in a footer dump', 'A link to a page that does not help', 'A broken link'),
                $q('What is an orphan in this sense?', 'A page nothing useful links to', 'A page linked from home', 'A page with one clear parent', 'A redirected old address'),
            ],
            'practice' => 'On three pages, replace "click here" with the name of the destination. Remove any link you would not recommend aloud.',
        ]),
        $L([
            'section' => 'The site',
            'title' => 'A sitemap is a list, not a rank button',
            'keywords' => 'xml sitemap, sitemap seo',
            'lead' => 'A sitemap is a list of the URLs you want considered. It is not a request to rank, and it is not a substitute for links. Milk List lists the offer and the real help pages, and leaves drafts off the list.',
            'heading' => 'Offer the URLs you mean',
            'para' => 'Include canonical, indexable pages that answer something. Leave out thank-you pages, duplicates, and URLs you have blocked. Keep the list current when you add or remove a real page. Submitting the list helps discovery. It does not force a fetch of a junk URL into the results, and it should not be asked to.',
            'bullets' => [
                'Only URLs you want considered.',
                'No drafts, twins, or blocked pages.',
                'Update the list when the map changes.',
                'Links still do the work of showing relationships.',
            ],
            'visual' => tf_split('seo28', 'Put it on the list?', 'A real answer?', 'Yes', 'Offer the URL', 'A draft or a twin', 'Leave it off', 'A sitemap is a list you mean, not a button that ranks.'),
            'rows' => [
                ['Include', 'The offer', '/shared-list'],
                ['Include', 'Real help', '/add-an-item'],
                ['Exclude', 'Thank-you', 'Not a query.'],
                ['Exclude', 'A twin', 'One owner.'],
                ['Update', 'When the map changes', 'Add and remove.'],
                ['Limit', 'Not a rank', 'Links still matter.'],
            ],
            'e1' => ['Example 1 — the list matches the map', 'Only pages you would show a stranger.', seo_coverage('Sitemap', <<<'HTML'
<p style="font-size:12px;color:#0f766e;font-weight:700;">SITEMAP</p>
<p>/shared-list</p>
<p>/add-an-item</p>
<p>/price</p>
HTML)],
            'e2' => ['Example 2 — the list is a junk drawer', 'Everything was thrown in.', seo_coverage('Junk list', <<<'HTML'
<p style="font-size:12px;color:#b45309;font-weight:700;">SITEMAP</p>
<p>/draft /thank-you /shared-list-old /p?id=1 /p?id=2</p>
HTML)],
            'micro' => [
                'A sitemap cannot repair a page that should not exist.',
                'Very large sites may split the list. Small sites should not invent complexity.',
                'If a URL is on the list and also marked to stay out, you have contradicted yourself.',
            ],
            'quiz' => [
                $q('What is a sitemap?', 'A list of URLs you want considered', 'A button that sets rank', 'A replacement for links', 'A place for drafts'),
                $q('Which URL stays off the list?', 'A thank-you page', 'The offer', 'A real help page', 'The price page'),
                $q('What still has to exist besides the list?', 'Links that show how pages relate', 'A bought vote', 'A hidden text block', 'A second copy of every URL'),
            ],
            'practice' => 'Write the URLs that belong on your sitemap. Cross out any you also want kept out of the index. The two lists must not overlap.',
        ]),
    ];
}
