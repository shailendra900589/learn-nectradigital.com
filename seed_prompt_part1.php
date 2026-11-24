<?php
declare(strict_types=1);

function pe_part1(): string
{
    return <<<'TXT'
@@
Start here
A prompt is the instruction plus the facts
LEAD
A prompt is the instruction you type, plus the facts the model cannot see. The model does not know your customer, your deadline, or the rule you forgot to write.
HEAD
Say the job in the first line
PARA
If the first line is a greeting, the model has to guess the job. Put the outcome first. Then add the facts. Then say what the answer should look like.
BULLETS
- Name one outcome, not three wishes.
- Add the fact the model cannot know.
- Say how many lines you want.
- Read the answer before you send it anywhere.
ROWS
Task | The job | One sentence with one outcome
Facts | What only you know | Names, dates, and rules
Shape | How the answer looks | Lines, bullets, or a table
PROMPT
Task: Explain what a prompt is to a new teammate who has never used a chat model.
Use a shared grocery list as the example.
Write exactly 6 short lines.
Do not use the word token.
End with one sentence they can paste tomorrow.
SAMPLE
A prompt is the note you hand someone who cannot see your kitchen.
Line 1 is the job: buy these items.
The list is the fact they cannot guess.
A limit is the budget or the shop.
The answer is only as complete as the note.
Tomorrow, start with the job, then the list, then the limit.
TIP
Leave memory, tools, and file uploads off so this test shows the words alone.
PRACTICE
Paste the prompt into one new chat. Change the grocery list to a real errand. Write which line of the answer you would keep.
Q
What belongs in the first line of a useful prompt?
A
The job you want done
B
A long greeting
C
The model brand name
D
An apology for asking
@@
Start here
ChatGPT, Claude, and Gemini on one page
LEAD
ChatGPT, Claude, and Gemini are three chat boxes. The same words can land differently. You learn the words first, then you notice the difference.
HEAD
Use one prompt in all three
PARA
A difference is only real if the prompt was the same. Paste the same text, in a new chat, with no extra file and no standing instruction.
BULLETS
- Open a new chat in each tool.
- Paste the same text, including the brackets after you fill them.
- Do not turn on web search for this test.
- Write one line about how the answers differ.
ROWS
Same words | One prompt | Do not rewrite it between tools
New chat | Empty history | Old chats leak old facts
Note | One difference | Length, tone, or a missing fact
PROMPT
Task: In 5 lines, tell a beginner how to paste the same prompt into ChatGPT, Claude, and Gemini.
Rules:
- New chat each time.
- No file upload.
- No web search.
- End with the one thing they should write down after the three answers.
Context: the beginner already has accounts and only needs the habit.
SAMPLE
Open ChatGPT and start a new chat.
Paste the prompt and wait.
Do the same in Claude, then in Gemini.
Do not add a file or a search.
Write one line: which answer you would actually use, and why.
TIP
If a tool adds its own search result, turn that off and paste again before you compare.
PRACTICE
Run this prompt in two tools. Save both answers and circle one sentence that appears in only one of them.
Q
When is a difference between two models a fair test?
A
When the pasted prompt and the chat settings match
B
When each model gets a rewritten prompt
C
When one chat is full of old messages
D
When only one tool is allowed to search
@@
Start here
Paste this before the theory
LEAD
You can learn the pieces by using one small prompt today. Theory sticks after you have seen an answer you can check.
HEAD
Fill the brackets, then paste
PARA
Brackets are the only part you change. The rest stays stable so you can tell whether a later edit helped.
BULLETS
- Replace each [bracket] with a real fact.
- Leave the task lines unchanged for the first run.
- Check the answer against the fact you typed.
- Save the prompt in a note with today's date.
ROWS
Bracket | Your fact | [topic] is not the final text
Stable lines | The task | Do not rewrite them on run one
Date | When you ran it | So a later edit has a before
PROMPT
Task: Give me a 4-line study note on [topic].
Reader: someone who will be tested tomorrow and has 10 minutes.
Include:
- one definition in plain words
- one example from ordinary life
- one mistake people make
- one question I should be able to answer
Do not add a second topic.
SAMPLE
Definition: a prompt is the instruction plus the facts you type.
Example: a grocery list tells the shopper what you forgot to say out loud.
Mistake: asking for "something good" and hoping the rest is guessed.
Question: what fact did you forget to type?
TIP
Put a real topic in [topic] before you paste. An empty bracket teaches you nothing.
PRACTICE
Replace [topic] with a subject you are learning. After the answer, mark any sentence that is not about that topic.
Q
What should you change before the first paste?
A
Each [bracket], and nothing else
B
The task, so it sounds more polite
C
The tool's hidden settings at random
D
The sample answer from this page
@@
Start here
One job, one answer
LEAD
A prompt that asks for a plan, a poem, and a legal opinion will return a blur. One job produces an answer you can accept or reject.
HEAD
Split the second job into a second paste
PARA
If you need two outcomes, run two prompts. The second prompt can quote the first answer as a fact, in brackets.
BULLETS
- Write the outcome in one sentence.
- If you see the word "and" joining two jobs, split them.
- Keep the same facts in both prompts.
- Check each answer on its own.
ROWS
One job | One outcome | A plan is not also a poem
Second paste | Next job | Quote the first answer as a fact
Reject | Wrong job | Ask again instead of editing by hand in silence
PROMPT
Task: Do one job only. Rewrite the note below so a busy parent can act on it today.
Note:
[paste a short messy note]
Output:
- 5 bullets, each starting with a verb
- no extra essay
- if the note contains a second job, list that job on a final line named Later, and do not do it
SAMPLE
- Buy milk before 6.
- Call the school office about the form.
- Pack the signed paper in the bag.
- Set an alarm for the pickup change.
- Leave the permission slip on the table.
Later: plan the birthday menu.
TIP
Paste a real messy note of under 80 words so you can see the Later line work.
PRACTICE
Give the prompt a note that contains two jobs. Confirm the answer does the first and only names the second.
Q
What should you do when a prompt asks for two outcomes?
A
Split them into two pastes
B
Add more polite words
C
Ask for a longer essay
D
Hide the second job inside a joke
@@
Start here
The words the model never saw
LEAD
The model sees this chat, not your desk. A rule that lives in your head is invisible until you type it.
HEAD
Write the missing fact
PARA
If the answer is wrong because a fact was missing, that is on the prompt. Add the fact and paste again. Do not argue with a model about a fact you never gave it.
BULLETS
- List three facts the answer needs.
- Cross out facts the model could know from the question alone.
- Type the remaining facts under Context.
- Compare the new answer with the first one.
ROWS
Invisible | Untyped rule | The model cannot infer your policy
Context | Typed facts | Names, dates, limits
Second paste | Same task, new fact | So you can see what the fact changed
PROMPT
Task: Reply to the customer in 4 lines.
Context you must use, and must not invent past:
- Product: [product]
- Order date: [date]
- Policy: replacements only within 14 days, no exceptions in this exercise
- What they wrote: [one sentence]
If a needed fact is still missing, ask one question instead of guessing.
SAMPLE
Thanks for writing about the kettle.
Your order date is 2 March, which is inside 14 days.
I can start a replacement.
What is the order number?
TIP
Use a fake order. Do not paste a real customer's private details.
PRACTICE
Run it once with the date missing, then once with the date filled. Write what the model guessed the first time.
Q
Why does a correct policy still produce a wrong answer?
A
The policy was never typed into the chat
B
The model prefers short answers
C
All three tools share one memory
D
A greeting was too short
@@
Start here
Tokens are pieces of text, not magic
LEAD
A token is a piece of text the model counts. You do not need to count tokens to write a clear prompt. You do need to know that long pastes cost attention.
HEAD
Keep the needed words, cut the rest
PARA
A long paste is fine when every paragraph is evidence. A long paste is noise when it repeats the same point. Cut repetition before you blame the model.
BULLETS
- Keep the task at the top, even in a long paste.
- Delete greetings and signatures from the source.
- If you only need a date, do not paste the whole thread.
- Ask for a short answer when you want one.
ROWS
Token | A text piece | Not a secret score of quality
Noise | Repeated lines | They spend the window
Top | Task first | So a long source does not bury the job
PROMPT
Task: Shorten the source to the facts a stranger needs. Keep every number and date. Cut greetings and repeated complaints.
Source:
[paste up to 200 words]
Output: 6 bullets. After them, one line named Dropped: listing what you removed.
SAMPLE
- Order 441 shipped on 4 April.
- The box arrived dented on 7 April.
- The customer wants a replacement, not a refund.
- Photos were mentioned, not attached here.
- No phone number is in the source.
- The second paragraph repeats the dent.
Dropped: hello, thanks, and the repeated dent story.
TIP
Paste a real paragraph you already have. Check that every number in the source survived.
PRACTICE
Compare the source numbers with the bullets. If a number vanished, paste again and add the line "Keep every digit."
Q
What is a token, in practical terms?
A
A piece of text the model counts
B
A password for the chat
C
A score that means the answer is true
D
A file you must upload first
@@
Start here
The context window is a desk, not a library
LEAD
The context window is how much text this chat can hold at once. It is a desk. It is not a filing cabinet of your whole life.
HEAD
Put the live facts on the desk
PARA
Older messages fall off the desk when the chat gets long. If a rule still matters, repeat it. Do not trust a chat from last month to remember a constraint.
BULLETS
- Keep one task per chat when you can.
- Repeat a rule that still binds this answer.
- Start a new chat when the topic changes.
- Paste the needed excerpt, not a promise that "it is above."
ROWS
Desk | This chat | Only what is still in the window
Library | Your files | Not visible unless pasted or attached
Repeat | A live rule | Say it again near the task
PROMPT
Task: The rule below must be followed even if an earlier message conflicts with it.
Rule: Use metric units only. Do not convert to cups or Fahrenheit.
Question: [a short cooking or travel question]
Answer in 5 lines. End with the units you used.
SAMPLE
Walk 2 kilometres, not a guess in miles.
Pack 1 litre of water.
The afternoon high given here is 29 C.
I did not convert any unit.
Units used: kilometres, litres, Celsius.
TIP
If the chat is already long, start a new one and paste the rule again.
PRACTICE
Ask a follow-up in the same chat that tempts a non-metric unit. If it slips, paste the rule again above the question.
Q
What should you do with a rule the chat may have forgotten?
A
Repeat it next to the current task
B
Assume it is stored forever
C
Type only "as above"
D
Switch units to test the model
@@
Start here
A wrong answer can look finished
LEAD
A fluent paragraph can still be false. Finish is a style. Truth is a check you do against a source you trust.
HEAD
Separate smooth from true
PARA
Read the answer for claims. A claim has a number, a name, a date, or a cause. Those are the lines you check. The rest is wording.
BULLETS
- Underline every number, name, and date.
- Ask which of those you can verify.
- If you cannot verify it, do not ship it.
- Ask the model to list claims, not to insist it is sure.
ROWS
Fluent | Sounds done | Not the same as true
Claim | A checkable line | Number, name, date, cause
Hold | Unverified line | Do not pass it on
PROMPT
Task: Read the answer below and list every checkable claim.
Answer:
[paste a model answer or a paragraph]
Output:
- Claim
- How I would check it
- Unknown, if the paragraph itself has no source
Do not add new facts.
SAMPLE
Claim: The shop opens at 9.
Check: the shop's own page for today's hours.
Unknown: no source is in the paragraph.
Claim: Returns are free for 30 days.
Check: the receipt or the written policy.
Unknown: the paragraph does not cite the policy.
TIP
Paste a short answer that contains at least two numbers so the list is easy to audit.
PRACTICE
Take the claim list and mark each line Verified, Wrong, or Not checked. Do not mark Verified from memory if you did not look.
Q
What does a polished paragraph prove?
A
That the wording is smooth
B
That every number is correct
C
That the model checked a source
D
That the chat can be sent as-is
@@
Start here
You check the answer, the model does not
LEAD
The model can draft. You decide. A check is a small list you can do in a minute, not a feeling that the answer "seems fine."
HEAD
Write the check before you paste
PARA
If you invent the check after you like the answer, you will keep the answer. Write two checks first. Then paste.
BULLETS
- Write two yes/no checks.
- Run the prompt.
- Apply the checks to the answer.
- If a check fails, change the prompt or reject the answer.
ROWS
Check | A yes/no test | "Has the date" is a test
Draft | The model output | Not a decision
Reject | Failed check | Paste again with the missing rule
PROMPT
Task: Draft a 5-line announcement for [event].
Checks you must satisfy, and which I will verify:
- The date [date] appears once.
- The place [place] appears once.
- No price appears, because price is not set.
If you cannot satisfy a check, say which check failed instead of writing the announcement.
SAMPLE
Team lunch is on 12 May.
Meet at the north lobby.
Bring nothing but your badge.
We will start at 12:30.
Price is not announced here.
TIP
Use a fake event. After the answer, search the text for a price that should not be there.
PRACTICE
Change the prompt so the place is easy to forget. If the place is missing, reject the draft and add "Place is required."
Q
When should you write the checks?
A
Before you paste the prompt
B
After you decide you like the tone
C
Only when a customer complains
D
Never, because the model self-checks
@@
Start here
Keep a prompt notebook
LEAD
A prompt you cannot find again will be rewritten from memory and will drift. A notebook is a title, the text, the date, and one line about the result.
HEAD
Save the text, not the vibe
PARA
Save the exact prompt. A description of the prompt is not the prompt. When you improve it, keep the previous text so you can go back.
BULLETS
- Title the prompt by the job, not by the model.
- Store the full text you pasted.
- Add the date and one result note.
- Keep the previous version when you edit.
ROWS
Title | The job | "Customer reply, 14-day rule"
Text | Exact paste | Not a summary of it
Version | Date plus note | What changed and why
PROMPT
Task: Turn the rough goal below into a notebook entry I can reuse.
Goal: [one sentence]
Output exactly these labels:
Title:
When to use:
Prompt:
Check:
Do not invent a company policy. If the goal needs a policy, write "[policy]" inside the prompt.
SAMPLE
Title: Four-line study note
When to use: I have a topic and 10 minutes.
Prompt: Task: Give me a 4-line study note on [topic]. Include a definition, an example, a mistake, and a question.
Check: All four parts are present and stay on that topic.
TIP
Put a goal you actually repeat, such as a weekly status note.
PRACTICE
Save the notebook entry in a real note on your computer. Tomorrow, paste it without rewriting it.
Q
What must a prompt notebook store?
A
The exact text you pasted
B
Only a description of the vibe
C
The model's private weights
D
Every chat you have ever opened
@@
The parts of a prompt
The task sentence
LEAD
The task sentence is the job. It should name the action and the result. "Help me" is not a task. "Rewrite this into 5 bullets" is a task.
HEAD
Use a verb and a result
PARA
Start with Task:. Use a verb you can finish: list, rewrite, extract, classify, compare, draft. Then name the object and the limit.
BULLETS
- Start with the word Task.
- Use one verb.
- Name the object: the email, the note, the table.
- Name the result: 5 bullets, a table, a yes or no.
ROWS
Verb | The action | List, rewrite, extract
Object | What it acts on | The note you paste
Result | The shape | 5 bullets, not "something useful"
PROMPT
Task: Rewrite the task sentence below so it has one verb, one object, and one result. Explain the change in one line.
Weak sentence:
[paste a vague request]
Output:
Better:
Why:
SAMPLE
Better: Task: Rewrite the parent note into 5 bullets, each starting with a verb.
Why: The weak line asked for help, which is not a result.
TIP
Use a vague sentence you have actually typed, such as "help me with this email."
PRACTICE
Write three weak sentences from your own work and run the prompt on each. Keep the Better lines.
Q
Which line is a task sentence?
A
Rewrite this email into 5 bullets
B
Help me with something
C
You are a world-class genius
D
Thanks in advance
@@
The parts of a prompt
Context the model cannot guess
LEAD
Context is the facts that change the answer: who it is for, what already happened, and which rule wins. Background that does not change the answer is clutter.
HEAD
Add only facts that change the draft
PARA
After the task, add a Context block. Three to six lines is enough for most jobs. If you need a long source, put it after the task and label it Source.
BULLETS
- Who is the reader.
- What decision the answer supports.
- Which constraint wins if two facts conflict.
- The source, labeled, after the task.
ROWS
Reader | Who must use this | A parent, a manager, a customer
Decision | What it supports | Reply, approve, or wait
Source | Labeled text | Not mixed into the task
PROMPT
Task: Draft a 4-line reply.
Context:
- Reader: [who]
- Decision this reply supports: [decision]
- Fact that must appear: [fact]
- Fact that must not appear: [forbidden]
Source, if any:
[one short paragraph or "none"]
If Context and Source disagree, follow Context and say they disagreed.
SAMPLE
Thanks for the update.
The delivery date we can confirm is 18 May.
I cannot share the internal discount code.
I will write again when the dock scan is in.
TIP
Make the forbidden fact something the source mentions, so you can see whether the model repeats it.
PRACTICE
Run it with a source that contradicts the fact. Confirm the reply follows Context and admits the clash.
Q
Where should a long source sit?
A
After the task, under a Source label
B
Instead of the task
C
Only in your head
D
In the chat title alone
@@
The parts of a prompt
Constraints that change the answer
LEAD
A constraint is a limit that changes the output: length, words you must use, words you must avoid, and a rule that beats your first draft.
HEAD
Write limits as tests
PARA
"Be careful" is not a constraint. "No prices" is a constraint because you can search the answer for a price.
BULLETS
- Use limits you can check by looking.
- Put the strongest limit last so it is near the answer.
- Say what to do if the limit cannot be met.
- Do not stack ten limits on a first try. Start with three.
ROWS
Checkable | You can see it | A date, a banned word, a count
Vague | You cannot test it | "Make it pop"
Fail plan | If it cannot comply | Say which limit broke
PROMPT
Task: Describe [item] in 4 lines for a product page.
Constraints:
- Mention [must] once.
- Do not mention price, shipping, or a competitor.
- Reading level: a 12-year-old.
- If you cannot mention [must] without inventing a spec, stop and ask for the spec.
SAMPLE
This bottle holds 750 millilitres.
The lid closes with one hand.
The mark on the side shows half full.
It is for a school bag, not a hiking trip.
TIP
Pick a must-mention you know is true. If you do not know the spec, the prompt should stop and ask.
PRACTICE
Add a fourth constraint only after the first three pass. Write which constraint the answer missed.
Q
Which line is a real constraint?
A
Do not mention price
B
Make it engaging
C
Be creative
D
Write something nice
@@
The parts of a prompt
The shape of the output
LEAD
The shape is the container: bullets, a table, a yes or no, or labeled lines. If you do not name the shape, you will get a shape the model prefers.
HEAD
Name the labels
PARA
Give the labels in the order you want them. Ask it not to add a preface. A preface is where extra claims hide.
BULLETS
- Pick one shape.
- Write the labels in order.
- Forbid a greeting and a closing essay.
- Check that every label is present.
ROWS
Shape | Bullets, table, or labels | Pick one
Order | The sequence of labels | So you can scan
Preface | Extra intro | Ask for none
PROMPT
Task: Extract a decision from the note.
Note:
[paste a short note]
Output labels, in this order, with nothing before them:
Decision:
Owner:
Date:
Missing:
Use "unknown" when the note does not say. Do not invent an owner.
SAMPLE
Decision: Move the review to Thursday.
Owner: unknown
Date: Thursday, no calendar date in the note.
Missing: the room and the named owner.
TIP
Use a note that is missing at least one label so "unknown" has to appear.
PRACTICE
If the answer adds a hello before Decision, paste again and add "The first character is the word Decision."
Q
Why name the output labels?
A
So you can scan and reject a missing line
B
So the answer becomes longer
C
So the model may skip the task
D
So a greeting can introduce the claims
@@
The parts of a prompt
One example, then the real job
LEAD
One example shows the pattern when the pattern is simple. The example is a sample of the shape, not a source of new facts about the real job.
HEAD
Mark the example as an example
PARA
Label it Example. Then label the real input Input. Tell the model not to copy names from the example into the answer.
BULLETS
- Keep the example tiny.
- Use fake names in the example.
- Put the real input after it.
- Forbid copying example names.
ROWS
Example | The pattern | Fake data
Input | The real job | After the example
Leak | Example names in the answer | Reject and say so
PROMPT
Task: Follow the example's shape. Do not copy the example's names or numbers into the answer.
Example input: "Sam will bring cups. Jo will bring napkins."
Example output:
- Sam: cups
- Jo: napkins
Input:
[paste two or three short assignments]
Output: the same bullet shape, one person per line.
SAMPLE
- Rita: slides
- Omar: room booking
- Lila: name tags
TIP
Make the real names different from Sam and Jo so a leak is obvious.
PRACTICE
If a real answer contains Sam or Jo, reject it and add "If you use an example name, stop."
Q
What is the example allowed to supply?
A
The shape of the answer
B
The facts of the real job
C
Names you forgot to type
D
Permission to skip the input
@@
The parts of a prompt
Three examples when the pattern is odd
LEAD
Use three examples when one example could be read two ways. Three examples show the edge: what to include, what to drop, and what to label unknown.
HEAD
Show the odd case on purpose
PARA
Example 1 is the normal case. Example 2 drops something. Example 3 has a trick you want handled the same way every time.
BULLETS
- Keep all three examples in the same shape.
- Make example 2 miss a field.
- Make example 3 the case people get wrong.
- Then give the real input once.
ROWS
Normal | The easy case | Shows the shape
Missing | A gap | Shows the unknown label
Odd | The mistake case | Shows the rule
PROMPT
Task: Classify each line as Action, Waiting, or Noise. Follow the examples. Do not add a fourth label.
Examples:
- "Send the invoice Tuesday" -> Action
- "Waiting on the client's logo" -> Waiting
- "Hope you are well" -> Noise
Input lines:
[paste 5 short lines]
Output: each line, then an arrow, then one label.
SAMPLE
Call the vendor -> Action
Need their price list first -> Waiting
Just circling back -> Noise
Book the room -> Action
Thanks again -> Noise
TIP
Include one polite line that is not a task, so Noise has to be used.
PRACTICE
Add a sixth line that is both polite and a real task. See which label wins. If you dislike it, add a rule: a verb with an object is Action even if the line is polite.
Q
When are three examples worth the extra text?
A
When one example can be read two ways
B
When the job is already one clear verb
C
When you want a longer chat
D
When you have no real input
@@
The parts of a prompt
Who the answer is for
LEAD
The reader changes the words. A note for a new hire and a note for a finance lead can carry the same fact and still need different sentences.
HEAD
Name the reader and what they already know
PARA
Say what the reader must do after reading. Do not say "make it engaging" and hope the reader appears.
BULLETS
- Name the reader.
- Say what they already know.
- Say what they must do next.
- Ban jargon they do not use.
ROWS
Reader | The person | Not "everyone"
Known | What you can skip | They already saw the ticket
Next | The action | Approve, reply, or wait
PROMPT
Task: Explain [decision] in 5 lines.
Reader: [role], who already knows [known] and does not know [unknown].
After reading, they must [next action].
Do not use acronyms unless you expand them on first use.
SAMPLE
We are moving the launch to 3 June.
You already know the feature list is frozen.
You do not yet have the support staffing plan.
Please confirm two names by Friday.
"QA" means the quality check team, and they need those names.
TIP
Pick a real decision you had to explain twice to two different people.
PRACTICE
Run the prompt twice, changing only the reader. Check that the next action changes with the reader.
Q
What should you tell the model about the reader?
A
Who they are, what they know, and what they must do
B
Only that the tone should be nice
C
Their private personal history
D
Nothing, so the answer stays generic
@@
The parts of a prompt
Tone without padding
LEAD
Tone is a constraint on words, not a pile of adjectives. "Warm and short" is usable. "World-class, stunning, delightful" makes the model add padding.
HEAD
Pick two tone words you can hear
PARA
Say what to cut: no exclamation marks, no "just circling back," no praise of the reader. Then the tone has edges.
BULLETS
- Use two tone words.
- Name phrases to avoid.
- Keep the facts from the source.
- Read it aloud once.
ROWS
Tone | Two words | Calm and direct
Ban list | Phrases to cut | A test you can see
Facts | Unchanged | Tone does not add claims
PROMPT
Task: Rewrite the message in a calm, direct tone.
Message:
[paste a blunt or fluffy message]
Keep every fact and date.
Avoid: exclamation marks, "just", "circle back", and praise.
Length: no longer than the original.
SAMPLE
The report is due Friday 16 May.
I need the two missing charts before noon.
I cannot review a draft that arrives after that.
Please send the file here.
TIP
Paste a message you almost sent and then deleted.
PRACTICE
Count the facts in the original and in the rewrite. They should match.
Q
What is a usable tone instruction?
A
Calm and direct, with phrases to avoid
B
Make it world-class and stunning
C
Sound like a genius
D
Add enthusiasm to every line
@@
The parts of a prompt
How long the answer should be
LEAD
Length is a limit. "Short" means different things to different models. A number is a limit you can count.
HEAD
Count lines, not vibes
PARA
Ask for a number of lines or a number of bullets. Say whether a label line counts. If it runs long, the prompt can say to cut an example before cutting a required fact.
BULLETS
- Prefer "5 lines" over "brief."
- Say what to cut first if it does not fit.
- Do not ask for short and for every detail.
- Count the answer yourself the first time.
ROWS
Count | Lines or bullets | You can count them
Cut order | What goes first | Examples before required facts
Conflict | Short plus complete | Pick the winner
PROMPT
Task: Summarize the note in exactly 4 lines.
Note:
[paste a note]
If it does not fit, cut examples before you cut a decision or a date.
Do not add a fifth line.
SAMPLE
The team chose vendor A on Monday.
The contract must be signed by 30 May.
Legal still needs the data list.
Training starts the week after signature.
TIP
Use a note that is longer than 4 lines so the cut has to happen.
PRACTICE
If you get 5 lines, paste again. Do not fix it by deleting a line yourself until you have seen a prompt that produces 4.
Q
Why say "exactly 4 lines" instead of "brief"?
A
You can count four lines
B
Brief always means the same thing
C
Models ignore numbers
D
A fifth line is always required
@@
The parts of a prompt
The language of the answer
LEAD
You can ask for the answer in a language, and you can ask it to leave names, code, and quotes unchanged. Say both, or the model will translate things you needed intact.
HEAD
Set the language and the exceptions
PARA
If you write the prompt in English and want Hindi, say so. If a product name must stay in Latin letters, say that too.
BULLETS
- Name the answer language.
- List words that must not be translated.
- Keep the source labels in the original if you will search them.
- Check one name after the answer.
ROWS
Language | The answer | Hindi, English, or another
Keep | Names and codes | Do not translate them
Prompt language | Your instruction | Can differ from the answer
PROMPT
Task: Explain the note in [language], in 5 short lines.
Note:
[paste a short note]
Do not translate these tokens: [names or codes].
Keep numbers unchanged.
SAMPLE
Order 441 is delayed.
The new date is 18 May.
SKU KETTLE-750 is the item.
The warehouse code WH-2 stays as written.
We will send the tracking link tomorrow.
TIP
Ask for a language you can read well enough to check names.
PRACTICE
Confirm each protected token appears exactly as you typed it.
Q
What should stay untranslated when you ask for another language?
A
Names, codes, and numbers you mark as fixed
B
Every word, including the explanation
C
Nothing, including product codes
D
Only the greeting
@@
The parts of a prompt
What you leave out
LEAD
A prompt can be too complete. Extra goals, extra tone words, and extra sources give the model room to pick the wrong one.
HEAD
Cut until one job remains
PARA
Read the prompt and delete any sentence that does not change the answer. If you are unsure, delete it, run again, and put it back only if the check fails.
BULLETS
- One task.
- Facts that change the answer.
- The shape.
- The check.
ROWS
Keep | Changes the answer | A date, a banned word
Cut | Does not change it | A slogan about quality
Second run | After a cut | Compare the two answers
PROMPT
Task: Shorten the prompt below without changing the job. Return only the shortened prompt.
Original prompt:
[paste a bloated prompt]
Rules:
- Keep one task, the needed facts, the output shape, and the check.
- Delete praise, repeated rules, and background that the answer does not use.
- Do not perform the original task. Only rewrite the prompt.
SAMPLE
Task: Rewrite the customer note into 4 lines.
Context: replacement only, order 441, date 2 March.
Output: 4 lines, no price.
Check: order number appears once.
TIP
Paste a prompt you wrote that feels long. Compare a run of the short version with a run of the long one.
PRACTICE
Run both versions on the same source. Keep the shorter one if the check still passes.
Q
What is a reason to delete a sentence from a prompt?
A
It does not change the answer
B
It contains a date the answer needs
C
It names the output label
D
It is the only statement of the task
@@
The parts of a prompt
The prompt card you reuse
LEAD
A prompt card is a small form: title, when to use it, the prompt, the inputs, and the check. If a card has no inputs, it is a one-off, not a tool.
HEAD
Leave brackets for what changes
PARA
Everything that changes between runs goes in brackets. Everything that must stay stable stays outside them. That is the whole card.
BULLETS
- Title the job.
- List the brackets.
- Paste the stable prompt.
- Write one check.
ROWS
Title | The job name | So you can find it
Brackets | What changes | Topic, date, source
Stable text | What you do not rewrite | The task and the shape
PROMPT
Task: Build a reusable prompt card from the job below.
Job: [describe a job you repeat]
Output labels:
Title:
Use when:
Brackets:
Prompt:
Check:
The Prompt section must contain at least one [bracket].
Do not fill the brackets with invented private data.
SAMPLE
Title: Meeting decisions
Use when: I have rough notes and need decisions only.
Brackets: [notes]
Prompt: Task: List decisions in the notes. Ignore discussion. Output one bullet per decision. Notes: [notes]
Check: Every bullet is a decision, not a topic that was only mentioned.
TIP
Use a job you did twice this month. If you cannot name a repeated job, this card is not ready.
PRACTICE
Fill the card and run it on two different inputs. The stable lines should stay identical.
Q
What belongs in brackets on a reusable card?
A
The part that changes every run
B
The task verb
C
The output labels
D
A customer's password
@@
Everyday moves
Rewrite for a reader
LEAD
Rewriting keeps the facts and changes the words for a named reader. It is not a chance to add a new claim.
HEAD
Same facts, clearer words
PARA
Give the source, the reader, and a ban on new facts. Then compare the claims in the rewrite with the claims in the source.
BULLETS
- Paste the source under Source.
- Name the reader.
- Forbid new facts.
- List any line you are unsure about.
ROWS
Source | The only facts | What you paste
Reader | Who it is for | Changes the words
New claim | Not in the source | Reject it
PROMPT
Task: Rewrite the source for [reader] in 6 lines or fewer.
Source:
[paste the text]
Do not add facts, numbers, or reasons that are not in the source.
If a sentence is ambiguous, keep the ambiguity rather than choosing a cause.
End with the label Unsure: and quote any phrase you did not understand. If none, write Unsure: none.
SAMPLE
The shipment left on 4 April.
It arrived dented on 7 April.
The customer asked for a replacement.
The note does not say the carrier name.
Unsure: none.
TIP
Use a paragraph you wrote in a hurry. Check that no new cause appeared.
PRACTICE
Highlight every claim in the rewrite. Each one must point at a phrase in the source.
Q
What may a rewrite add?
A
Clearer words for the named reader
B
A cause the source never stated
C
A new date that makes the story neater
D
Praise that changes the meaning
@@
Everyday moves
Shorten without losing the decision
LEAD
A summary that drops the decision is a topic list. Keep the decision, the owner, and the date. Cut the tour of how you got there.
HEAD
Mark the decision before you cut
PARA
If you cannot find a decision in the source, the output should say that. Do not invent a decision to fill the short version.
BULLETS
- Find the decision first.
- Keep owner and date if present.
- Cut story and repetition.
- Say when no decision exists.
ROWS
Decision | What was chosen | Must survive
Cut | How you got there | The long debate
Empty | No decision in the source | Say so
PROMPT
Task: Shorten the source to at most 5 lines. Keep the decision, the owner, and any date. Cut debate.
Source:
[paste notes]
If no decision is stated, write only: No decision in the source.
Do not invent an owner.
SAMPLE
Decision: Ship the first version without the export button.
Owner: Product, named as "we" only.
Date: Review on 22 May.
The export button waits for the next version.
No other decision is stated.
TIP
Paste notes where the decision is buried under opinions.
PRACTICE
Delete the decision from the source and run again. The answer should be the single line that no decision is present.
Q
What must survive a short version of a meeting note?
A
The decision, and the owner and date if stated
B
Every opinion in the debate
C
A decision you wish had been made
D
The jokes
@@
Everyday moves
Summarize a meeting
LEAD
A meeting summary is decisions, owners, and open questions. It is not a transcript and it is not minutes of who spoke the most.
HEAD
Three buckets only
PARA
Ask for Decisions, Owners, and Open. Anything that is only discussion goes nowhere, unless it blocks a decision, in which case it is Open.
BULLETS
- Decisions people agreed.
- Owners with names from the notes only.
- Open questions.
- No transcript.
ROWS
Decisions | Agreed outcomes | Not topics
Owners | Names in the notes | Not guessed names
Open | Unresolved | What blocks the next step
PROMPT
Task: Summarize the meeting notes into three buckets.
Notes:
[paste notes]
Output:
Decisions:
Owners:
Open:
Ignore greetings and side talk.
If a name is not in the notes, do not create one. Use "unnamed" .
SAMPLE
Decisions:
- Launch stays on 3 June.
Owners:
- unnamed for support staffing.
Open:
- Who approves the help-page text?
TIP
Use notes you already have. Remove phone numbers before you paste.
PRACTICE
Check every owner against the notes. An owner who was not in the room does not belong in the summary.
Q
Where does a long debate with no agreement go?
A
Under Open, or nowhere if it blocks nothing
B
Under Decisions, as a compromise you invent
C
As a transcript paragraph
D
As a new owner
@@
Everyday moves
Extract fields from a paragraph
LEAD
Extraction pulls named fields and leaves the prose behind. You decide the fields before you paste. The model does not get to invent a field you did not ask for.
HEAD
List the fields and the empty value
PARA
Say the exact empty word, such as blank. Then you can tell a miss from a guess.
BULLETS
- Name every field.
- Say the empty value.
- Give one paragraph as source.
- Reject extra fields.
ROWS
Field | What you want | Email, date, amount
Empty | The word for missing | blank
Extra | A field you did not ask | Not allowed
PROMPT
Task: Extract only these fields from the paragraph. If a field is absent, write blank.
Fields: person, date, amount, ask.
Paragraph:
[paste one paragraph]
Output one field per line, in that order.
Do not infer an amount from a quantity of items.
SAMPLE
person: Priya Shah
date: blank
amount: blank
ask: a replacement kettle
TIP
Use a paragraph that lacks at least one field.
PRACTICE
If an amount appears that the paragraph never stated, reject the run and add "Do not calculate."
Q
What should the model write when a field is absent?
A
The empty value you named
B
A reasonable guess
C
A new field that might help
D
The whole paragraph again
@@
Everyday moves
Classify into labels you named
LEAD
Classification only works when you name the labels and what they mean. "Sort these" lets the model invent buckets you cannot compare later.
HEAD
Define each label in one line
PARA
Give two to five labels. Add one line each. Add a label named Other only if you truly want a leftover bucket, and tell it when Other is allowed.
BULLETS
- Name the labels.
- Define each in one line.
- Say whether Other is allowed.
- Keep one item per line in the output.
ROWS
Label set | Closed list | You named them
Definition | One line each | Stops drift
Other | Leftover | Only if you allow it
PROMPT
Task: Classify each item as Billing, Access, or How-to.
Definitions:
- Billing: money, invoices, refunds.
- Access: login, password, permissions.
- How-to: how to use a feature when login works.
Other is not allowed. If an item fits two, choose the one that matches the first sentence and add the label Mixed on that line.
Items:
[one item per line]
SAMPLE
I was charged twice -> Billing
I cannot log in -> Access
How do I export a report -> How-to
The export is billed extra and I cannot open it -> Billing Mixed
TIP
Include one item that could fit two labels so you see the Mixed rule.
PRACTICE
Add an item that fits none. The model should not invent a fourth label. Adjust the prompt if it does.
Q
Why define the labels inside the prompt?
A
So later runs use the same buckets
B
So the model can invent better buckets
C
So Other can absorb every line
D
So you can skip the items
@@
Everyday moves
Compare two options with a rubric
LEAD
A comparison without a rubric is a preference. A rubric is the list of criteria and how much each one matters. You write the rubric. The model applies it to the facts you give.
HEAD
Score only the facts you provide
PARA
Give two options and three criteria. Ask for a score you define, such as 0 to 2. Forbid outside knowledge.
BULLETS
- Two named options.
- Three criteria you care about.
- A small score scale.
- A final line that can be "not enough information."
ROWS
Criteria | Your values | Not the model's hobbies
Scale | 0 to 2 | Easy to audit
Tie | Not enough information | Better than a fake winner
PROMPT
Task: Compare Option A and Option B using only the facts below. Score each criterion 0, 1, or 2. Do not use outside knowledge.
Option A:
[facts]
Option B:
[facts]
Criteria:
- Cost over one year
- Time to start
- Risk if it fails
End with Pick: A, B, or not enough information. If a criterion has no fact, score it 0 and say missing.
SAMPLE
Cost: A 2, B 1. A states a lower yearly fee.
Time: A 1, B 2. B can start this week. A needs a month.
Risk: A 0 missing, B 1. Only B names a backup.
Pick: not enough information.
TIP
Leave one criterion without facts on purpose the first time.
PRACTICE
Fill the missing fact and run again. The pick may change. That is the point of the rubric.
Q
Who should choose the criteria?
A
You, before the paste
B
The model, after it sees which option it likes
C
Whichever option has more adjectives
D
A random reader
@@
Everyday moves
Critique against a checklist
LEAD
A critique is a pass or fail against a checklist you wrote. It is not a mood. Each item is met or not met, with a quote from the draft.
HEAD
Quote the line that fails
PARA
Ask for Met or Not met, then a short quote. If the draft has no line about that item, the quote is "absent."
BULLETS
- Write 4 to 6 checklist items.
- Ask for Met or Not met.
- Require a quote or the word absent.
- Do not ask it to rewrite until the critique is done.
ROWS
Item | One test | "Date is visible"
Quote | Evidence | A phrase from the draft
absent | No such line | Not a guess
PROMPT
Task: Critique the draft. Do not rewrite it yet.
Checklist:
- The date is visible.
- The ask is a single action.
- No price is mentioned.
- The reader is named.
Draft:
[paste the draft]
Output, one item per line: Met or Not met, then a quote or absent.
SAMPLE
Date is visible: Met, "Friday 16 May".
The ask is a single action: Not met, absent.
No price is mentioned: Met, absent price.
The reader is named: Not met, absent.
TIP
Use a draft you wrote, with one item deliberately missing.
PRACTICE
After the critique, write a second prompt that fixes only the Not met items. Do not mix the critique and the rewrite in one paste.
Q
What makes a critique different from a new draft?
A
It scores the draft against your checklist
B
It replaces the draft with a nicer one immediately
C
It adds facts to help the draft pass
D
It ignores missing items
@@
Everyday moves
Plan the next three steps
LEAD
A plan for a model is the next three steps a person can do, with an owner and a stop. A twelve-step strategy is how plans hide.
HEAD
Three steps you could do this week
PARA
Each step needs a verb, an output, and a stop condition. If a step has no output, it is a theme.
BULLETS
- Exactly three steps.
- Each has an output you can see.
- Each has a stop.
- No step that requires a tool you did not name.
ROWS
Verb | The action | Email, list, book
Output | The thing left behind | A file, a date, a decision
Stop | When to quit | After the reply, or after 20 minutes
PROMPT
Task: Give the next three steps for this goal. Exactly three.
Goal: [goal]
Constraint: [time or money limit]
For each step write:
- Step
- Output
- Stop when
Do not plan past step three. Do not recommend buying a tool.
SAMPLE
Step: Write the four questions the customer must answer.
Output: a four-line form.
Stop when: the form fits on one screen.
Step: Send it to one real customer.
Output: the sent message.
Stop when: it is sent, not when they reply.
Step: Log what they skipped.
Output: a note of blank answers.
Stop when: the blanks are listed.
TIP
Use a goal you can actually start this week. Reject any step that says "research the market" with no output.
PRACTICE
Do step one in real life. If you cannot, the output was not concrete. Change the prompt and run it again.
Q
What does a usable step include?
A
A verb, a visible output, and a stop
B
A theme such as synergy
C
A twelve-part strategy
D
A new paid tool
@@
Everyday moves
Decide and show the tradeoff
LEAD
A decision prompt should recommend one option and state what you give up. If it cannot, it should say what fact is missing. It should not average the options into a mush.
HEAD
One recommendation, one cost
PARA
Ask for the pick, the reason tied to your criterion, and the cost of that pick. Ban a "it depends" paragraph unless the missing fact is named.
BULLETS
- Two or three options you name.
- One criterion that wins ties.
- A required cost line.
- A missing-fact exit.
ROWS
Pick | One option | Not a blend
Cost | What you give up | Time, money, or risk
Missing | The fact that blocks a pick | Named, not "it depends"
PROMPT
Task: Pick one option. The tie-breaker is [criterion].
Options:
[list 2 or 3]
Facts:
[facts]
Output:
Pick:
Because:
Cost:
Missing:
If the tie-breaker has no supporting fact, write Pick: none and leave Because empty.
SAMPLE
Pick: Keep the Thursday class.
Because: the tie-breaker is staff overtime, and Thursday uses the people already scheduled.
Cost: two students who can only come Friday will wait a week.
Missing: none.
TIP
Name a tie-breaker you actually use, such as cost, speed, or student impact.
PRACTICE
Remove the facts about the tie-breaker and confirm the pick becomes none.
Q
What should a decision answer show besides the pick?
A
The cost of that pick
B
A blend of every option
C
A long "it depends" with no missing fact
D
A new option you did not offer
@@
Everyday moves
Interview me until the brief is clear
LEAD
Sometimes you do not know the brief yet. Ask the model to interview you. Limit the questions so it does not wander, and tell it to stop when it can state the job.
HEAD
A fixed number of questions
PARA
Five questions is enough. After each answer, it may ask the next. At the end it writes the brief and waits. It does not do the job in the same turn.
BULLETS
- Cap the questions.
- One question at a time if you are in a live chat.
- The last turn is a brief you can approve.
- The real task waits until you say the brief is right.
ROWS
Cap | Five questions | Stops the tour
Brief | The restated job | You approve it
Wait | No draft yet | Until you accept the brief
PROMPT
Task: Interview me so you can brief a later prompt. Ask one question at a time, up to 5 questions.
Goal I started with: [goal]
Ask only about the reader, the decision, the constraints, the inputs, and the check.
After I answer the last question, write:
Brief:
Check:
Then stop. Do not produce the final work in this chat until I reply "brief accepted".
SAMPLE
Question 1 of 5: Who has to act on this, and what do they already know?
TIP
Answer in one or two lines. If a question is nosy or off the five topics, say "skip" and continue.
PRACTICE
Finish the five answers, then check the Brief. Accept it only if you could hand it to someone else.
Q
When should the model produce the final work in an interview prompt?
A
After you accept the written brief
B
In the same breath as question one
C
Before it knows the reader
D
As soon as the goal sounds familiar
@@
Everyday moves
Teach one idea with a check question
LEAD
A teaching prompt covers one idea, one example, and one question the learner can answer. A tour of the whole subject is not a lesson.
HEAD
One idea only
PARA
Name the idea. Ask for a plain definition, a concrete example, a common miss, and one check question with an answer key you can hide or reveal.
BULLETS
- One idea you name.
- A life example, not a slogan.
- The usual mistake.
- A check question plus the expected answer, labeled.
ROWS
Idea | The single point | Not a chapter
Example | Ordinary life | So a beginner can see it
Key | The expected answer | So you can grade
PROMPT
Task: Teach one idea: [idea].
Reader: a beginner.
Output:
Definition:
Example:
Common miss:
Check question:
Answer key:
Use at most 2 sentences in each section.
Do not introduce a second idea.
SAMPLE
Definition: A constraint is a limit you can test by looking at the answer.
Example: "No prices" fails if a currency symbol appears.
Common miss: "Be careful" cannot be tested.
Check question: Which limit can you test, "be brief" or "use exactly 4 lines"?
Answer key: exactly 4 lines.
TIP
Pick an idea from this course, not a whole career.
PRACTICE
Answer the check question before you read the key. If you cannot, the definition was too abstract. Say so and ask for a plainer example.
Q
How many ideas should this lesson prompt teach?
A
One
B
Every related idea in the field
C
Whatever the model finds interesting
D
None, only a joke
@@
Everyday moves
A role that stays inside the facts
LEAD
A role is a hat: editor, teacher, support agent. It changes standards and tone. It does not create permission to invent facts, credentials, or personal details.
HEAD
The role names the standard, not a fantasy
PARA
Say the role in one line, then the standard that role uses. Refuse roles that ask the model to pretend it is a specific real person or a licensed professional giving a final ruling.
BULLETS
- One role.
- The standard that role follows.
- Facts still come from you.
- No impersonation of a real person.
ROWS
Role | Editor, teacher, agent | A working hat
Standard | What that hat checks | Clarity, policy, steps
Boundary | Still not a licensed ruling | You keep the decision
PROMPT
Task: Act as a careful editor, not as a lawyer, doctor, or a real named person.
Standard: clarity and faithful facts.
Draft:
[paste your draft]
Return:
- The edited draft
- A list of changes
- A list of claims you did not check because no source was provided
Do not add credentials or a personal story.
SAMPLE
Edited draft: The form is due Friday 16 May. Send it to the office, not to me.
Changes: Split two asks into one. Removed "as soon as possible."
Unchecked: whether Friday is a holiday. No calendar was provided.
TIP
Paste your own draft. If the edit adds a fact, reject that sentence.
PRACTICE
Ask for the same edit without the role line. Note whether the checklist behavior changes. Keep the version that lists unchecked claims.
Q
What is a role allowed to change?
A
The standard and the tone
B
The facts you did not provide
C
The identity of a real person
D
A legal or medical ruling presented as final
@@
Everyday moves
Answer first, then the steps
LEAD
For a direct question, ask for the answer in the first line. Put the steps after. A long warmup hides a wrong answer where you might miss it.
HEAD
Line one is the answer
PARA
This fits definitions, choices, and short how-to questions. It does not fit a request for interview questions, where the questions are the answer.
BULLETS
- First line is the result.
- Then at most four steps or reasons.
- No preface.
- If the question is unclear, the first line says what is missing.
ROWS
Line one | The result | You see it immediately
After | Reasons or steps | Optional
Unclear | What is missing | Instead of a guess
PROMPT
Task: Answer the question. The first line must be the answer. Then give at most 4 short reasons. No greeting.
Question: [question]
If the question is missing a fact that changes the answer, the first line must be "Missing:" plus the fact. Do not guess that fact.
SAMPLE
Use a checklist of claims, not a feeling that the paragraph sounds finished.
A fluent answer can still be false.
Numbers, names, and dates are the lines to check.
Finish is a style.
Truth is a check against a source.
TIP
Ask a question you could also answer in one sentence yourself, so you can judge line one.
PRACTICE
If the model greets you before the answer, paste again. Your check is: line one is the answer or the word Missing.
Q
Where should the direct answer sit?
A
On the first line
B
After a long introduction
C
Only in a footnote
D
Nowhere, if the prose is confident
TXT;
}
