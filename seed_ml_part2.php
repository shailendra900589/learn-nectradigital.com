<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/track_seed.php';

function ml_chapters_depth(): array
{
    $f = 'ml_notes.py';
    $L = static function (array $c) use ($f): array {
        return track_lesson('python', $f, $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };
    $curve = svg_line_graph(
        'A step that is too large jumps the loss back up',
        ['0', '1', '2', '3', '4'],
        [2.0, 0.8, 1.6, 0.4, 1.2],
        '',
        '#e11d48',
        'A healthy step size makes the loss fall. This one overshoots, so the next loss is worse than the one you just left.'
    );

    return [
        $L([
            'section' => '2. A score you can trust',
            'title' => 'Beat the boring guess first',
            'keywords' => 'baseline, majority class, mean predictor, machine learning',
            'lead' => 'A model is not good because it prints a number. It is good if it beats the boring guess you could have made with no features. For a label that is usually 0, the boring guess is always 0. For a number, the boring guess is the average of the training answers.',
            'heading' => 'The baseline is the score to beat',
            'para' => 'Suppose 8 of 10 notes are not urgent. Always saying “not urgent” is right 8 times. A clever model that is right 7 times is worse than doing nothing. You would not know that if you only looked at the clever model. For predicting a length, guessing the training mean gives a squared error you can compute before you fit a line. The line has to beat that error on rows it did not train on.',
            'bullets' => [
                'Classification baseline: predict the most common training label every time.',
                'Regression baseline: predict the mean of the training labels every time.',
                'Compare on the same test rows. A baseline scored on the training rows and a model scored on the test rows is not a comparison.',
                'If you cannot beat the baseline, stop adding features. The signal is not there, or it is leaking in a way you have not earned.',
            ],
            'rows' => [
                ['Majority', 'The common label', 'Always predict it.'],
                ['Mean', 'The average training answer', 'Always predict it for a number.'],
                ['Same rows', 'The test set', 'Score both guesses here.'],
                ['Win', 'Fewer mistakes, or lower loss', 'Otherwise the model is not earning its complexity.'],
                ['Tie', 'Keep the boring guess', 'It is easier to explain.'],
                ['Training score', 'Not the comparison', 'A memoriser looks perfect there.'],
            ],
            'e1' => ['Example 1 — always saying 0 beats a noisy rule', 'Eight zeros and two ones. The majority guess misses 2. A rule that also misses the two ones and one zero misses 3.', <<<'PY'
labels = [0, 0, 0, 0, 0, 0, 0, 0, 1, 1]
guesses = [0, 0, 0, 0, 0, 0, 1, 0, 0, 0]

def mistakes(pred, truth):
    return sum(p != t for p, t in zip(pred, truth))

majority = [0] * len(labels)
print('majority mistakes', mistakes(majority, labels))
print('noisy rule mistakes', mistakes(guesses, labels))
PY],
            'e2' => ['Example 2 — the mean is a number baseline', 'Predicting 10 every time has a squared error. A worse constant, 0, has a larger one.', <<<'PY'
truth = [8, 10, 12]
mean = sum(truth) / len(truth)

def squared_error(pred):
    return sum((pred - y) ** 2 for y in truth)

print('mean', mean)
print('error of the mean', squared_error(mean))
print('error of guessing 0', squared_error(0))
PY],
            'micro' => [
                'The majority is computed from training labels only. Peeking at the test labels to choose it is the same leak as training on the test set.',
                'For a rare urgent class, the majority baseline looks strong and is useless. The precision chapter is how you catch that. Still compute the baseline so the accuracy number has a denominator of honesty.',
                'A baseline can be “predict the previous hour’s value.” It does not have to be the mean. It has to be a guess you could explain before the model.',
            ],
            'quiz' => [
                $q('What is the classification baseline in this lesson?', 'Always predict the most common training label', 'Always predict 1', 'A random label each row', 'The test label'),
                $q('When is a model not worth keeping?', 'It loses to the boring guess on the same test rows', 'It uses more than one feature', 'Its training error is zero', 'It prints integers'),
                $q('What number does the regression baseline predict?', 'The mean of the training answers', 'Zero', 'The largest test answer', 'The row index'),
            ],
            'practice' => 'Labels [1, 1, 1, 0]. What does the majority baseline predict, and how many test mistakes does it make if the test labels are those four?',
        ]),
        $L([
            'section' => '2. A score you can trust',
            'title' => 'Scale only what the training rows have seen',
            'keywords' => 'feature scaling, train test leakage, standardise',
            'lead' => 'Scaling puts features into a similar numeric range so a distance or a gradient is not dominated by the feature with the huge units. The mean and the spread you subtract must come from the training rows. If the test rows help compute that mean, the test has already influenced the model.',
            'heading' => 'Fit the scale, then apply it',
            'para' => 'Lengths [1, 3, 5] have mean 3. Subtract 3 and each training length becomes -2, 0, or 2. A new length of 7 becomes 7 - 3 = 4, using the training mean, not a mean that included 7. If you recompute the mean on train plus test, the test value moves the centre, and every training number quietly changes. That is leakage. The same rule applies to min and max scaling: the min and the max are training facts.',
            'bullets' => [
                'Split first. Then compute the mean and the spread. Then transform the test rows with those frozen numbers.',
                'A test value can fall outside the training range. That is allowed. Do not clip it back just to make the picture tidy, unless the contract says to.',
                'Scale inputs, not the label, unless you have a separate reason and you will undo the scale before you report the answer.',
                'Nearest neighbour and gradient descent care about scale. A count of “how many rows are urgent” does not.',
            ],
            'rows' => [
                ['Fit', 'Mean and spread from training', 'Once.'],
                ['Apply', 'Subtract that mean', 'Training and test both use it.'],
                ['Leak', 'Test rows inside the mean', 'The holdout is no longer a holdout.'],
                ['New extreme', 'Legal', 'The formula does not require the test value to lie inside the training min and max.'],
                ['Per feature', 'Its own mean', 'Do not subtract the mean of a different column.'],
                ['Report', 'In the original units', 'If you scaled the label, undo it.'],
            ],
            'e1' => ['Example 1 — the test point uses the training mean', 'Training mean is 3. The new length 7 scales to 4. It does not join the mean.', <<<'PY'
train = [1, 3, 5]
test_value = 7
center = sum(train) / len(train)
print('center', center)
print('train scaled', [x - center for x in train])
print('test scaled', test_value - center)
PY],
            'e2' => ['Example 2 — including the test point moves the centre', 'The leaked centre is 4, not 3. Every scaled training number changed because one test number was allowed into the fit.', <<<'PY'
train = [1, 3, 5]
test_value = 7
honest = sum(train) / len(train)
leaked = sum(train + [test_value]) / (len(train) + 1)
print('honest center', honest)
print('leaked center', leaked)
print('first train value honest', train[0] - honest)
print('first train value leaked', train[0] - leaked)
PY],
            'micro' => [
                'Do this again inside every cross-validation fold. A scale fit on the whole dataset, then folded, still leaked.',
                'A spread of zero means the training feature never changed. Dividing by it is undefined. Drop the feature or leave it unscaled and constant.',
                'Centering is not the same as dividing by the spread. This lesson only centers, so the idea is visible. A full standardisation also divides by the training spread.',
            ],
            'quiz' => [
                $q('Which rows may compute the mean used for scaling?', 'Training rows only', 'Test rows only', 'Train and test together', 'The single row you are predicting'),
                $q('What is wrong with a leaked centre?', 'The test value changed the transformation of the training rows', 'The mean became an integer', 'Subtraction is illegal', 'The test value must be zero'),
                $q('When do you scale relative to the split?', 'After the split', 'Before any split, on all rows', 'Only after the final accuracy is printed', 'Never, if you use Python'),
            ],
            'practice' => 'Training values 2 and 4. Scale the test value 10 with the honest centre. Then compute the leaked centre and say which one moved.',
        ]),
        $L([
            'section' => '2. A score you can trust',
            'title' => 'A third pile you do not touch',
            'keywords' => 'validation set, test set, model selection',
            'lead' => 'The training rows fit the numbers. If you also use the test rows to decide which model to keep, the test rows have become training rows with extra steps. A third pile, the validation pile, is where you compare models. The test pile is opened once, at the end, and then you do not go back and tune.',
            'heading' => 'Train fits, validation chooses, test reports',
            'para' => 'Two rules are ready. Rule A misses 1 of the 4 validation rows. Rule B misses 2. You keep A because of the validation pile. The test pile is still sealed. When you finally score A on the test pile, that number is the one you publish. If you had chosen the rule by the test pile, then edited the rule and scored again, the published number would be optimistic.',
            'bullets' => [
                'Fit only on the training pile. Choosing a threshold is fitting. It belongs on train or validation, not on test.',
                'Validation can be used many times, and it gets a little optimistic too. The test pile is the one you protect.',
                'If the validation pile is tiny, the choice between two close rules is noise. Say so.',
                'A final refit on train plus validation, after the choice is frozen, is a separate decision. You still do not refit on the test pile.',
            ],
            'rows' => [
                ['Train', 'Fit the numbers', 'The rule sees these labels.'],
                ['Validation', 'Choose the rule', 'Many looks are allowed.'],
                ['Test', 'Report once', 'No more tuning after you look.'],
                ['Threshold', 'A kind of fitting', 'Not a free look at the test labels.'],
                ['Tiny pile', 'A noisy choice', 'One row can flip the winner.'],
                ['Publish', 'The test score of the frozen rule', 'Not the best score you saw along the way.'],
            ],
            'e1' => ['Example 1 — validation picks rule A', 'A misses one validation row. B misses two. The test labels are not read.', <<<'PY'
val_truth = [0, 1, 1, 0]
rule_a = [0, 1, 0, 0]
rule_b = [1, 0, 1, 1]

def misses(pred):
    return sum(p != t for p, t in zip(pred, val_truth))

print('A', misses(rule_a), 'B', misses(rule_b))
print('keep', 'A' if misses(rule_a) <= misses(rule_b) else 'B')
PY],
            'e2' => ['Example 2 — peeking at the test pile changes the winner', 'On validation, A wins. On this particular test pile, B wins. Choosing after that peek publishes B and uses up the test.', <<<'PY'
val = (1, 2)
test = (2, 0)
print('validation prefers', 'A' if val[0] < val[1] else 'B')
print('test would prefer', 'A' if test[0] < test[1] else 'B')
print('honest report uses the validation choice')
PY],
            'micro' => [
                'Three piles all have to look like the same problem. A validation pile of only easy notes will crown a model that fails on the hard test.',
                'Kaggle-style public and private scores are this idea. The public score is validation. Tuning to it too long overfits the public score.',
                'If you have very few rows, a third pile is expensive. The fold chapter is how you reuse rows without a permanent test peek.',
            ],
            'quiz' => [
                $q('Which pile chooses between two models?', 'Validation', 'Test', 'Neither, you pick the one with more parameters', 'The pile with the longer name'),
                $q('When may you tune after seeing the test score?', 'You should not. That look spent the test', 'Every time the score disappoints', 'Only if the score went up', 'Only for thresholds'),
                $q('What does the training pile do?', 'Fit the numbers inside the rule', 'Publish the final number', 'Stay sealed until the end', 'Hold only the test labels'),
            ],
            'practice' => 'Invent two rules and four validation labels. Count the misses and write which rule you keep. Do not invent the test labels until that sentence is written.',
        ]),
        $L([
            'section' => '3. A line you can move',
            'title' => 'A step that is too large jumps past the valley',
            'keywords' => 'learning rate, gradient descent, divergence',
            'lead' => 'A gradient step moves the weight against the slope, by a distance you choose. That distance is the learning rate. Too small, and you need many steps to cross the valley. Too large, and you leap over the bottom and land somewhere higher than you started.',
            'heading' => 'The same slope, two step sizes',
            'para' => 'Imagine a loss of (w - 3) squared. The slope at w is 2(w - 3). From w = 0, a step of size 0.1 walks toward 3 and the loss falls. A step of size 1 replaces w with w - 1 * 2(w - 3), which jumps to the other side and can grow. The slope told you the direction. It did not tell you that any distance along that direction is safe. You have to try a step and check that the loss actually fell.',
            'visual' => $curve,
            'bullets' => [
                'Direction comes from the slope. Distance comes from the learning rate. They are separate choices.',
                'If the loss rises, the step was too big for this point, or the slope was wrong. Do not celebrate a step that increased the loss.',
                'A tiny rate that still falls is correct and slow. For teaching, slow and correct beats fast and lost.',
                'The rate is not a property of the dataset’s units alone. After scaling, a rate that used to explode may become calm.',
            ],
            'rows' => [
                ['Slope', 'Which way is up', 'You step the other way.'],
                ['Rate', 'How far you step', 'Often called the learning rate.'],
                ['Fall', 'Loss decreased', 'Keep the step.'],
                ['Rise', 'Loss increased', 'The step overshot or the slope was wrong.'],
                ['Tiny rate', 'Many steps', 'Still valid.'],
                ['After scaling', 'The slope’s size changes', 'Retune the rate.'],
            ],
            'e1' => ['Example 1 — a small step walks toward 3', 'Loss is (w - 3) squared. Four steps of size 0.2 move w from 0 toward 3 and the loss falls.', <<<'PY'
w = 0.0
rate = 0.2
for step in range(4):
    slope = 2 * (w - 3)
    w = w - rate * slope
    loss = (w - 3) ** 2
    print(step, round(w, 3), round(loss, 3))
PY],
            'e2' => ['Example 2 — a huge step leaps past 3', 'Rate 1 applied to slope 2(w - 3) from 0 jumps to 6, and the loss is as large as it began. Another step jumps back.', <<<'PY'
w = 0.0
rate = 1.0
for step in range(3):
    slope = 2 * (w - 3)
    w = w - rate * slope
    loss = (w - 3) ** 2
    print(step, w, loss)
PY],
            'micro' => [
                'The update is w - rate * slope when the slope is the derivative of the loss. Adding the slope walks uphill.',
                'A loss that falls for three steps and then rises is a rate that was fine far away and too big near the bottom. Shrink it.',
                'This valley is a parabola with one bottom. A loss with many valleys can fall into the wrong one even with a careful rate. This chapter only shows step size.',
            ],
            'quiz' => [
                $q('What does the learning rate control?', 'How far you step', 'The sign of the slope', 'The number of rows', 'Whether the loss is squared'),
                $q('What should you do when a step increases the loss?', 'Treat the step as too large and do not keep a worse weight without noticing', 'Increase the rate further', 'Ignore the loss', 'Remove the training rows'),
                $q('From w = 0 on (w - 3) squared, which rate walks calmly toward 3?', 'A small rate such as 0.2', 'A rate of 1, which jumps to 6', 'A negative rate', 'A rate equal to the label'),
            ],
            'practice' => 'Run three steps from w = 0 with rate 0.5. Write w after each step and say whether the loss fell every time.',
        ]),
        $L([
            'section' => '3. A line you can move',
            'title' => 'Two inputs get two slopes',
            'keywords' => 'multiple linear regression, two weights, bias',
            'lead' => 'A note has a length and a count of exclamation marks. One slope cannot say how much each input matters. You give each input its own weight, and you keep a separate bias for the prediction when both inputs are zero.',
            'heading' => 'The prediction is a weighted sum plus a bias',
            'para' => 'prediction = bias + weight_length * length + weight_marks * marks. With bias 1, weight_length 0.5, and weight_marks 2, a note of length 4 with 1 mark predicts 1 + 2 + 2 = 5. If you forget the bias, a note with both inputs at 0 is forced to predict 0, even when every such note in the data was urgent. The bias is the prediction at the origin, not a third input you measured.',
            'bullets' => [
                'Each feature has its own weight. Sharing one weight between length and marks pretends they have the same units and the same importance.',
                'The bias is added once. It is not multiplied by a feature.',
                'A feature you forgot to scale can still dominate, because its raw numbers are bigger, not because it matters more.',
                'The error of one row is prediction minus label. The update for each weight uses that error times that row’s feature.',
            ],
            'rows' => [
                ['weight_length', 'How a longer note moves the prediction', 'Its own number.'],
                ['weight_marks', 'How marks move the prediction', 'A different number.'],
                ['bias', 'The prediction at zero inputs', 'Not a measured feature.'],
                ['Prediction', 'bias + sum of weight times feature', 'One number.'],
                ['Error', 'prediction - label', 'Signed, so you know the direction.'],
                ['Update', 'error times that feature', 'Each weight hears its own feature.'],
            ],
            'e1' => ['Example 1 — length and marks both contribute', '4 * 0.5 and 1 * 2 and a bias of 1 add to 5.', <<<'PY'
bias = 1.0
weight_length = 0.5
weight_marks = 2.0
length = 4
marks = 1
prediction = bias + weight_length * length + weight_marks * marks
print(prediction)
PY],
            'e2' => ['Example 2 — one gradient step on a single row', 'The label is 3 and the prediction was 5, so the error is 2. Each weight steps down in proportion to its feature. The bias steps by the error alone.', <<<'PY'
bias = 1.0
weight_length = 0.5
weight_marks = 2.0
length = 4
marks = 1
label = 3
rate = 0.05
prediction = bias + weight_length * length + weight_marks * marks
error = prediction - label
bias = bias - rate * error
weight_length = weight_length - rate * error * length
weight_marks = weight_marks - rate * error * marks
print(round(prediction, 3), round(error, 3))
print(round(bias, 3), round(weight_length, 3), round(weight_marks, 3))
PY],
            'micro' => [
                'error times feature is why a feature of 0 does not move its weight on that row. The row has nothing to say about a feature that was absent.',
                'Two features that always rise together are hard to separate. The fit can put a large positive weight on one and a large negative weight on the other and still predict well. That is not two independent facts.',
                'Write the formula with names. w1 and w2 hide which column you will multiply.',
            ],
            'quiz' => [
                $q('What is the bias?', 'The prediction when every feature is zero', 'The larger weight', 'The label', 'The learning rate'),
                $q('How many weights do two inputs need, besides the bias?', 'Two', 'One shared weight', 'None', 'One per row'),
                $q('What multiplies the error when you update the length weight?', 'The length feature of that row', 'The marks feature', 'The bias', 'Always 1'),
            ],
            'practice' => 'bias 0, both weights 1, features 2 and 3, label 4. Compute the prediction and the error before any step.',
        ]),
        $L([
            'section' => '1. The table',
            'title' => 'A category is not a bigger number',
            'keywords' => 'one hot encoding, categorical feature, nominal',
            'lead' => 'A colour column that says red, green, or blue is not a slider. If you store red as 1, green as 2, and blue as 3, the model hears “blue is three times red” and “green is halfway between them.” Neither sentence was in the data. One-hot encoding gives each colour its own column that is 1 or 0.',
            'heading' => 'One column per category',
            'para' => 'The note colours red, blue, red become three rows of two columns if you drop one colour, or three columns if you keep all three. This lesson keeps a column for red and a column for blue. Green would be a third column. Red is (1, 0). Blue is (0, 1). No order is implied, because neither column is “more” than the other. The weight on the red column is “what red does,” not “what a larger code does.”',
            'bullets' => [
                'Use one-hot when the categories have no order. Shirt size small, medium, large does have an order, and a single increasing code can be honest.',
                'A category that appears only in the test set has no column. Decide the fallback before you score: all zeros, or an explicit “other” column that the training set also had.',
                'Keeping every category plus a bias makes the columns add up to 1, so they are redundant with the bias. Dropping one category is the careful version. This lesson shows the columns first so the 1s and 0s are visible.',
                'Do not one-hot a column that is already a measurement, such as length. You would explode it into a column per distinct number.',
            ],
            'rows' => [
                ['Category', 'A name, not a magnitude', 'Red is not less than blue.'],
                ['Code 1, 2, 3', 'A fake order', 'The model will use that order.'],
                ['One-hot', 'A column of 1 or 0', 'One column per category you keep.'],
                ['Unseen', 'No column', 'Choose a fallback before the test.'],
                ['Ordered size', 'Maybe one column', 'Only if the order is real.'],
                ['Bias plus all columns', 'Redundant', 'The columns sum to 1.'],
            ],
            'e1' => ['Example 1 — red and blue become columns', 'The integer codes say blue is 2 and red is 1. The columns do not.', <<<'PY'
colours = ['red', 'blue', 'red']
codes = [{'red': 1, 'blue': 2}[c] for c in colours]
columns = [(1 if c == 'red' else 0, 1 if c == 'blue' else 0) for c in colours]
print('fake order', codes)
print('columns', columns)
PY],
            'e2' => ['Example 2 — an unseen colour is not a new integer', 'Green was not in training. Coding it as 3 invents a magnitude. The honest row is all zeros until you add an other column.', <<<'PY'
known = ('red', 'blue')

def columns(colour):
    if colour not in known:
        return (0, 0)
    return (1 if colour == 'red' else 0, 1 if colour == 'blue' else 0)

print(columns('red'))
print(columns('green'))
PY],
            'micro' => [
                'The order of columns must be frozen from the training set. Sorting categories by the test batch will swap the weights.',
                'A rare category with two rows will get a wild weight. You may group rare names into other before you encode.',
                'One-hot distance in nearest neighbour is “same category or not.” The fake integer distance says blue is far from red. Those are different models.',
            ],
            'quiz' => [
                $q('Why is coding red=1, blue=2 misleading?', 'It invents an order and a distance the names did not have', 'Integers are illegal features', 'Blue cannot be a feature', 'Models ignore integers'),
                $q('What does a one-hot red row look like when the columns are red then blue?', '(1, 0)', '(1, 2)', '(0, 0)', '(2, 1)'),
                $q('What is an honest first encoding of a colour never seen in training?', 'All zeros, or an other column you planned', 'The next integer after the largest code', 'The mean colour', 'A random column'),
            ],
            'practice' => 'Encode the list green, red, green with columns for red and green only. Write the three pairs.',
        ]),
        $L([
            'section' => '4. Yes or no',
            'title' => 'Move the cutoff and the four boxes change',
            'keywords' => 'classification threshold, precision recall tradeoff',
            'lead' => 'A logistic model gives a probability, not a yes. You choose a cutoff. At 0.5, probabilities of 0.5 and above become urgent. Lower the cutoff and you catch more urgent notes, and you also call more calm notes urgent. The four boxes move. There is no cutoff that improves every box at once.',
            'heading' => 'The cutoff is a decision, not a fact in the data',
            'para' => 'Probabilities 0.2, 0.6, 0.8 with labels 0, 1, 1. At cutoff 0.5 you predict 0, 1, 1 and miss nothing urgent. At cutoff 0.9 you predict 0, 0, 0 and miss both urgent notes. At cutoff 0.1 you call the calm note urgent too. Which cutoff is right depends on which mistake is expensive. A missed outage is not the same cost as a false alarm about milk.',
            'bullets' => [
                'The model can stay fixed while the cutoff moves. You are not refitting.',
                'A higher cutoff usually raises precision and lowers recall, when urgent is the positive class. Check it. Unusual score distributions can bend the story.',
                'Pick the cutoff on validation rows. The test rows then score that frozen cutoff.',
                '0.5 is a habit, not a law. It treats both mistakes as equal and assumes the probability is calibrated.',
            ],
            'rows' => [
                ['Probability', 'The model’s number', 'Between 0 and 1 in this lesson.'],
                ['Cutoff', 'The line you draw', 'You choose it.'],
                ['Predict 1', 'Probability >= cutoff', 'This lesson’s rule.'],
                ['Lower cutoff', 'More positive predictions', 'More false alarms, fewer misses, in the usual case.'],
                ['Higher cutoff', 'Fewer positive predictions', 'The opposite trade.'],
                ['Cost', 'Which mistake hurts', 'The reason to move the line.'],
            ],
            'e1' => ['Example 1 — 0.5 catches both urgent notes', 'Labels are 0, 1, 1. Predictions match. Missed urgent notes: 0.', <<<'PY'
probs = [0.2, 0.6, 0.8]
labels = [0, 1, 1]
cutoff = 0.5
pred = [1 if p >= cutoff else 0 for p in probs]
missed = sum(1 for p, y in zip(pred, labels) if y == 1 and p == 0)
false_alarms = sum(1 for p, y in zip(pred, labels) if y == 0 and p == 1)
print(pred, 'missed', missed, 'false alarms', false_alarms)
PY],
            'e2' => ['Example 2 — 0.7 drops the 0.6 note', 'One urgent note is now missed. The calm note is still calm. You bought fewer alarms by missing a real one.', <<<'PY'
probs = [0.2, 0.6, 0.8]
labels = [0, 1, 1]
cutoff = 0.7
pred = [1 if p >= cutoff else 0 for p in probs]
missed = sum(1 for p, y in zip(pred, labels) if y == 1 and p == 0)
print(pred, 'missed', missed)
PY],
            'micro' => [
                '>= versus > matters when a probability lands exactly on the cutoff. Pick one and keep it.',
                'If the scores are not probabilities but raw distances, a cutoff of 0.5 is meaningless. The cutoff lives in the same units as the score.',
                'Report the cutoff with the score. “Precision was 0.9” without the cutoff cannot be repeated.',
            ],
            'quiz' => [
                $q('What does lowering the cutoff usually do?', 'Call more notes urgent, including more calm ones', 'Fit a new slope', 'Delete the positive labels', 'Make every probability 1'),
                $q('Where should you choose the cutoff?', 'On validation rows', 'On the test rows, repeatedly', 'At random after publishing', 'Only at 0.5'),
                $q('Why is 0.5 not a law?', 'It assumes the two mistakes cost the same and the score is a probability', 'Python rejects other cutoffs', 'Recall ignores the cutoff', 'Labels are always balanced'),
            ],
            'practice' => 'Same three probabilities. Count missed urgent notes at cutoff 0.8. Then say which mistake you were willing to make.',
        ]),
        $L([
            'section' => '2. A score you can trust',
            'title' => 'Rotate which fold is the test',
            'keywords' => 'cross validation, folds, k fold',
            'lead' => 'One split can be lucky: the hard notes all landed in training. Cross-validation rotates the job. Each fold takes a turn as the test pile while the other folds train. You see several scores, not one lucky number.',
            'heading' => 'Every row is tested once',
            'para' => 'Six labels, three folds of two. Fold 0 tests the first pair, fold 1 the second pair, fold 2 the third. A rule “predict 1 if the index inside the fold’s training data contains a 1” is too tied to this toy. The lesson’s rule is simpler: always predict the majority of the other rows, and score the held-out pair. Each row is tested exactly once. The mean of the three accuracies is the estimate. The spread of the three tells you whether the estimate is stable.',
            'bullets' => [
                'Folds should be disjoint. A row in two test folds is counted twice and looks more important than it is.',
                'Compute the majority, the scale, and any threshold inside the training folds only. The held-out fold is a small test.',
                'Three scores of 1.0, 1.0, and 0.0 average to 0.67 and are not the same story as three scores of 0.67. Report the spread.',
                'A final model for production can be fit on all rows after the estimate is done. That fit has no leftover fold to score it. The cross-validation number is the estimate you already made.',
            ],
            'rows' => [
                ['Fold', 'One held-out pair here', 'The test turn.'],
                ['Train', 'The other rows', 'Fit only here.'],
                ['Once', 'Each row tested one time', 'If the folds partition the data.'],
                ['Mean', 'The estimate', 'Average the fold scores.'],
                ['Spread', 'How much the folds disagree', 'A warning when it is large.'],
                ['Final fit', 'All rows, after scoring', 'No new test number appears.'],
            ],
            'e1' => ['Example 1 — three folds, three majority guesses', 'Labels [0, 0, 0, 1, 1, 1]. Each held-out pair is scored against the majority of the other four.', <<<'PY'
labels = [0, 0, 0, 1, 1, 1]
folds = [(0, 1), (2, 3), (4, 5)]
scores = []
for test_indexes in folds:
    train = [labels[i] for i in range(6) if i not in test_indexes]
    majority = 1 if sum(train) * 2 >= len(train) else 0
    correct = sum(1 for i in test_indexes if labels[i] == majority)
    scores.append(correct / len(test_indexes))
print(scores)
print('mean', sum(scores) / len(scores))
PY],
            'e2' => ['Example 2 — a lucky single split hides the last pair', 'Training on the first four labels and testing on the last two scores 0, because the training majority is 0 and the test pair is 1, 1. The rotation in example 1 showed that this split is one of the folds, not the whole story.', <<<'PY'
labels = [0, 0, 0, 1, 1, 1]
train = labels[:4]
test = labels[4:]
majority = 1 if sum(train) * 2 >= len(train) else 0
correct = sum(1 for y in test if y == majority)
print('majority', majority, 'accuracy', correct / len(test))
PY],
            'micro' => [
                'Shuffle before you cut folds if the file is sorted by label. Otherwise one fold is all the urgent notes.',
                'Stratify when you can: each fold should contain both labels. A fold of two zeros cannot measure recall.',
                'More folds mean a larger training piece and a noisier test piece. Five or ten is common. Three is enough to see the idea.',
            ],
            'quiz' => [
                $q('What does a fold do on its turn?', 'It is the test pile, and the other rows train', 'It is deleted', 'It chooses the test labels', 'It is scored with a scale fit on itself'),
                $q('How many times is each row tested when the folds partition the data?', 'Once', 'On every fold', 'Never', 'Twice, always'),
                $q('Why report the spread of fold scores?', 'A mean can hide one fold that failed', 'The spread is the learning rate', 'Python requires three prints', 'The majority is the spread'),
            ],
            'practice' => 'Split [1, 0, 1, 0] into two folds of two adjacent labels. Compute the majority-baseline accuracy of each fold.',
        ]),
        $L([
            'section' => '5. A tree of questions',
            'title' => 'A second question after the first',
            'keywords' => 'decision tree, second split, leaf',
            'lead' => 'A stump asks one question and stops. A tree can ask a second question in the branch where the first answer was not enough. Each question splits a group of rows. A leaf is a group you decided not to split, and its prediction is the majority label inside it.',
            'heading' => 'The second question sees only its branch',
            'para' => 'Rows are (length, marks, label): (1, 0, 0), (1, 2, 1), (5, 0, 0), (6, 0, 1). Asking “is length at least 5?” separates the short notes from the long ones. The short branch still contains both labels, so a second question, “are there any marks?”, separates (1, 0) from (1, 2). The long branch can ask “is length at least 6?”. You only ask a question using rows that reached that branch. Using all rows again would ignore the first answer.',
            'bullets' => [
                'A leaf predicts the majority of the training rows that landed in it. An empty leaf should not happen if you only split when both sides get a row.',
                'The second question is allowed to be different on the left and the right. That is the point of a tree.',
                'A tree that keeps splitting until every leaf is pure has memorised the training rows. The overfit chapter is this picture.',
                'Read a prediction by walking from the root, not by averaging the leaves.',
            ],
            'rows' => [
                ['Root', 'The first question', 'Every row reaches it.'],
                ['Branch', 'The rows that answered the same way', 'The next question sees only these.'],
                ['Leaf', 'A prediction', 'Majority label of the rows here.'],
                ['Pure leaf', 'One label', 'Nothing left to ask, on this training set.'],
                ['Depth', 'How many questions you walked', 'A limit fights memorising.'],
                ['Walk', 'Root to leaf', 'How you predict a new row.'],
            ],
            'e1' => ['Example 1 — two questions separate the short notes', 'Length splits first. Marks split the short branch. The four training rows land in pure leaves.', <<<'PY'
rows = [(1, 0, 0), (1, 2, 1), (5, 0, 0), (6, 0, 1)]

def predict(length, marks):
    if length >= 5:
        return 1 if length >= 6 else 0
    return 1 if marks >= 1 else 0

print([predict(length, marks) for length, marks, _ in rows])
PY],
            'e2' => ['Example 2 — a new short note with marks follows the second question', 'Length 2 fails the first test, so the length >= 6 question is never asked. Marks decide.', <<<'PY'
def predict(length, marks):
    if length >= 5:
        return 1 if length >= 6 else 0
    return 1 if marks >= 1 else 0

print(predict(2, 3))
print(predict(5, 9))
PY],
            'micro' => [
                'Example 2’s second print is 0 because length 5 is not >= 6, even though marks are 9. The marks question is not in that branch. That surprise is why you draw the tree.',
                'Choosing the question by “which split is purest” is the real tree algorithm. This lesson fixes the questions so you can see the walk.',
                'A depth limit of 1 is the stump you already built. Depth 2 is this chapter.',
            ],
            'quiz' => [
                $q('Which rows does the second question see?', 'Only the rows that took that branch', 'Every training row', 'Only the test rows', 'Only pure leaves'),
                $q('What does a leaf predict?', 'The majority training label among rows that reached it', 'Always 1', 'The root question', 'The row index'),
                $q('Why limit depth?', 'An unlimited tree can memorise the training rows', 'Depth makes probabilities illegal', 'A stump cannot exist', 'Leaves cannot be pure'),
            ],
            'practice' => 'Predict (6, 1) and (1, 0) with the function in the example. Say which question made each decision.',
        ]),
        $L([
            'section' => '5. A tree of questions',
            'title' => 'Two weak questions can vote',
            'keywords' => 'ensemble, voting, decision stump',
            'lead' => 'One stump uses one feature and misses what the other feature knew. Two stumps can each be wrong on different rows and still be right together, if you let them vote. The prediction is the majority of their votes. A tie needs a rule you wrote down before you looked at the test row.',
            'heading' => 'Each stump votes yes or no',
            'para' => 'Stump A says urgent when the length is at least 12. Stump B says urgent when the word “down” appears. A note “disk down” has length 9, so A votes 0 and B votes 1. A note “please restart the backup” is long and has no “down”, so A votes 1 and B votes 0. A note “server down tonight” gets two votes. With two voters, a tie is possible. This lesson breaks ties toward 0, the calm label, and says so. Hiding the tie rule inside “whatever the code does” is how two people report different accuracies.',
            'bullets' => [
                'Voters should make different mistakes. Two copies of the same stump do not become wiser by voting.',
                'Majority with an odd number of voters avoids ties. With two voters you must write the tie rule.',
                'A vote is not a probability. Two yes votes do not mean 100 percent unless you calibrated them.',
                'The stumps are fit on training rows. The vote is scored on rows they did not fit, same as any other model.',
            ],
            'rows' => [
                ['Stump', 'One question', 'A weak voter.'],
                ['Vote', '0 or 1', 'Not a weight, in this lesson.'],
                ['Majority', 'More votes win', 'The combined prediction.'],
                ['Tie', 'A written rule', 'Here, ties go to 0.'],
                ['Diversity', 'Different mistakes', 'The reason to combine them.'],
                ['Duplicate', 'The same mistake twice', 'The vote cannot repair it.'],
            ],
            'e1' => ['Example 1 — the long note and the down note disagree, then a third note agrees', 'Ties print 0. Two yes votes print 1.', <<<'PY'
def stump_length(text):
    return 1 if len(text) >= 12 else 0

def stump_word(text):
    return 1 if 'down' in text else 0

def vote(text):
    yes = stump_length(text) + stump_word(text)
    return 1 if yes >= 2 else 0

print(vote('disk down'), vote('please restart the backup'), vote('server down tonight'))
PY],
            'e2' => ['Example 2 — two identical stumps cannot fix a miss', 'Both miss “disk down” because both only look at length. The sum is 0.', <<<'PY'
def stump(text):
    return 1 if len(text) >= 12 else 0

def vote(text):
    return 1 if stump(text) + stump(text) >= 2 else 0

print(vote('disk down'))
PY],
            'micro' => [
                '>= 2 means both must agree before you say urgent. >= 1 would mean either stump can raise the alarm. Those are different products. Write the number.',
                'A weighted vote, where the better stump counts double, is the next idea. It needs a validation score so the weight is not a wish.',
                'Voting does not fix a shared blind spot. If neither stump sees the word “fire,” a fire note is still calm.',
            ],
            'quiz' => [
                $q('Why vote with two different stumps?', 'They can be wrong on different rows', 'Two identical votes change the math of a single stump', 'Voting removes the need for labels', 'A stump cannot return 0'),
                $q('What does this lesson do with a 1–1 tie when the threshold is two yes votes?', 'It predicts 0', 'It predicts 1', 'It refits both stumps', 'It drops the row'),
                $q('What is still required of the stumps?', 'They are fit without the test rows', 'They must use the same feature', 'They must always agree', 'They output probabilities only'),
            ],
            'practice' => 'Add a third stump that fires when the text has “fire”. Write the votes for “fire in aisle 2” under a rule that urgent means at least two yes votes.',
        ]),
        $L([
            'section' => '5. A tree of questions',
            'title' => 'Count the words in each class',
            'keywords' => 'naive bayes, word counts, prior',
            'lead' => 'A tiny naive Bayes classifier asks which class makes the words in the note less surprising. You count how often each word appears in urgent notes and in calm notes. You also count how common each class is. A new note goes to the class whose counts make the words, and the class itself, more expected.',
            'heading' => 'Multiply the chances you counted',
            'para' => 'Training notes: “pay invoice” is urgent, “pay rent” is urgent, “read poem” is calm. The word pay appears in urgent notes and not in the calm one. poem appears only in the calm note. A new note “pay poem” contains one word from each side. You also need a count for a word you have never seen, or an unseen word zeros the whole product. Add-one smoothing puts a one in every count before you start, so nothing is impossible.',
            'bullets' => [
                'The prior is the fraction of training notes in that class, before you look at the words.',
                'The word chance is the count of that word in the class, divided by the number of words in the class, after smoothing.',
                'Naive means you multiply the word chances as if the words were independent. They are not, and the classifier can still be useful.',
                'Compare the two classes by the product. The larger product wins. You do not need the products to add to 1 to pick a winner.',
            ],
            'rows' => [
                ['Prior', 'How common the class is', 'From the training labels.'],
                ['Count', 'Times the word appeared in the class', 'Plus one if you smooth.'],
                ['Unseen word', 'Would zero the product', 'Smoothing prevents that.'],
                ['Naive', 'Multiply the words', 'Independence is the assumption, not the truth.'],
                ['Winner', 'The larger product', 'The predicted class.'],
                ['Tie', 'A written rule', 'Do not leave it to float noise.'],
            ],
            'e1' => ['Example 1 — pay pulls toward urgent', 'With add-one smoothing, “pay” scores higher as urgent than as calm.', <<<'PY'
urgent_words = ['pay', 'invoice', 'pay', 'rent']
calm_words = ['read', 'poem']
vocabulary = ['pay', 'invoice', 'rent', 'read', 'poem']

def chance(words, word):
    return (words.count(word) + 1) / (len(words) + len(vocabulary))

def score(words, note):
    total = 0.5
    for word in note.split():
        total *= chance(words, word)
    return total

note = 'pay'
print(round(score(urgent_words, note), 4), round(score(calm_words, note), 4))
PY],
            'e2' => ['Example 2 — an unseen word does not wipe the score', '“meteor” is in neither class. Smoothing gives it the same small chance in both, so the prior and the known words still decide. Without smoothing the product would be zero on both sides.', <<<'PY'
urgent_words = ['pay', 'invoice']
calm_words = ['read', 'poem']
vocabulary = ['pay', 'invoice', 'read', 'poem', 'meteor']

def chance(words, word):
    return (words.count(word) + 1) / (len(words) + len(vocabulary))

print(round(chance(urgent_words, 'meteor'), 4))
print(round(chance(calm_words, 'meteor'), 4))
PY],
            'micro' => [
                'The prior in example 1 is written as 0.5 because each class has the same number of notes in that toy. If urgent notes are rare, use their real fraction or the word counts will shout over a rare class.',
                'Splitting on spaces makes “invoice,” with a comma a different word from “invoice”. Clean the text the same way for training and for the new note.',
                'Multiplying many small chances underflows to 0.0 in real notes. Adding the logs of the chances is the same comparison and stays numeric. This lesson multiplies because the notes are tiny.',
            ],
            'quiz' => [
                $q('What does add-one smoothing stop?', 'An unseen word forcing the whole product to zero', 'The need for labels', 'All misclassifications', 'The prior'),
                $q('What does naive mean here?', 'Word chances are multiplied as if the words were independent', 'The model uses no counts', 'Every note is urgent', 'The vocabulary has one word'),
                $q('What is the prior?', 'How common the class was in training, before the words', 'The longest word', 'The test accuracy', 'The smoothing constant only'),
            ],
            'practice' => 'Add a calm note “pay later” to the word list and recompute which class wins for the single word pay. Say what changed.',
        ]),
        $L([
            'section' => '1. The table',
            'title' => 'The label leaked into a feature',
            'keywords' => 'data leakage, target leak, feature audit',
            'lead' => 'A feature is legal only if you would know it at the moment you must predict. If a column is the label under another name, or a fact that was written down after the outcome, the model will look brilliant and fail on a real day. That column is leakage.',
            'heading' => 'Ask when the value was known',
            'para' => 'You want to predict whether a note is urgent. A column called urgent_flag that is always equal to the label gives perfect accuracy and zero information. A column called reply_sent is also leakage if the reply was sent because someone already decided the note was urgent. Length and the words on the page are legal if they are present when the note arrives. The test is time, not whether the column is numeric.',
            'bullets' => [
                'A perfect feature is a reason to be suspicious, not a reason to celebrate.',
                'Write the decision time in a sentence. Anything recorded after that time is out.',
                'An ID that grows with time can leak, because later IDs belong to later outcomes the model should not see.',
                'Aggregates such as “how many urgent notes this customer had” must be counted only from the past, not from the row you are scoring.',
            ],
            'rows' => [
                ['Legal feature', 'Known at decision time', 'The text of the note.'],
                ['Renamed label', 'The answer in disguise', 'urgent_flag.'],
                ['After the fact', 'Recorded once you knew', 'A reply that exists because it was urgent.'],
                ['Perfect score', 'A smell', 'Inspect the strongest feature before you ship.'],
                ['Past only', 'Aggregates', 'Exclude the current row.'],
                ['ID', 'Often a clock', 'Do not feed a raw increasing id.'],
            ],
            'e1' => ['Example 1 — the flag matches the label on every row', 'Accuracy is 1. The feature is the label. The model did not learn.', <<<'PY'
rows = [
    {'text': 'buy milk', 'urgent_flag': 0, 'label': 0},
    {'text': 'server down', 'urgent_flag': 1, 'label': 1},
]
correct = sum(row['urgent_flag'] == row['label'] for row in rows)
print('accuracy', correct / len(rows))
print('same column', all(row['urgent_flag'] == row['label'] for row in rows))
PY],
            'e2' => ['Example 2 — a past count must skip the current row', 'Customer 7’s history of urgent notes is [1, 0] before this row. Including this row’s own label 1 inflates the count from 1 to 2.', <<<'PY'
history = [1, 0]
current_label = 1
honest = sum(history)
leaked = sum(history + [current_label])
print('known before the decision', honest)
print('includes the answer', leaked)
PY],
            'micro' => [
                'Dropping the leaked column and refitting is the fix. Leaving it in and feeling suspicious is not.',
                'A feature can leak in only part of the dataset, such as a status field that is filled in after review for old rows and left empty for new ones. Check old and new rows separately.',
                'High accuracy on a random split does not prove the feature was known in time. The split does not understand clocks.',
            ],
            'quiz' => [
                $q('When is a feature illegal?', 'You would not know it at the moment you must predict', 'It is a number', 'It correlates with the label at all', 'It is text'),
                $q('Why is a perfect column suspicious?', 'It may be the label under another name', 'Perfect accuracy is impossible, so the printer failed', 'Text cannot be perfect', 'The mean was zero'),
                $q('How should a historical count treat the current row?', 'Leave it out', 'Include its label', 'Include only if the label is 1', 'Replace the count with the id'),
            ],
            'practice' => 'List three columns you might store about a support note. Mark each as known on arrival or known only after someone handled it.',
        ]),
        $L([
            'section' => '4. Yes or no',
            'title' => 'A rare urgent note hides inside accuracy',
            'keywords' => 'class imbalance, accuracy, rare positive',
            'lead' => 'If 99 notes are calm and 1 is urgent, a model that always says calm is 99 percent accurate and never catches the note you cared about. Accuracy divides all correct answers by all rows, so the large class drowns the small one. You have to count the rare class on its own.',
            'heading' => 'Ask about the urgent rows separately',
            'para' => 'Always predicting 0 on 99 zeros and 1 one gets 99 correct. Recall on the urgent class is 0, because the only urgent row was missed. A model that flags that one row and also flags 3 calm rows has accuracy 96/100, which looks worse, and recall 1. If the urgent note is an outage, the second model is the one you ship, and the accuracy ranking would have thrown it away.',
            'bullets' => [
                'Compute the base rate: what fraction of rows are urgent. Accuracy below that base rate is a broken model. Accuracy near it may be the majority baseline.',
                'Recall of the rare class is the first number to put next to accuracy.',
                'A confusion matrix on the rare class stays readable. A single accuracy does not.',
                'Do not “fix” imbalance by copying rows until the classes match unless you know what that does to duplicates. This lesson only changes the score you read.',
            ],
            'rows' => [
                ['Base rate', 'Urgent rows / all rows', 'The accuracy of always saying calm is 1 minus this, when calm is the majority.'],
                ['Accuracy', 'Correct / all rows', 'Dominated by the large class.'],
                ['Rare recall', 'Urgent rows you caught / urgent rows', 'The number accuracy hid.'],
                ['False alarms', 'Calm rows you flagged', 'The price of a higher recall.'],
                ['Majority model', 'Always the common label', 'High accuracy, zero rare recall.'],
                ['Decision', 'Which mistake costs more', 'Not which accuracy is prettier.'],
            ],
            'e1' => ['Example 1 — 99 percent accuracy, zero recall', 'One urgent label at the end. The constant prediction never says 1.', <<<'PY'
labels = [0] * 99 + [1]
pred = [0] * 100
accuracy = sum(p == y for p, y in zip(pred, labels)) / len(labels)
urgent = [i for i, y in enumerate(labels) if y == 1]
recall = sum(pred[i] == 1 for i in urgent) / len(urgent)
print(accuracy, recall)
PY],
            'e2' => ['Example 2 — worse accuracy, the urgent row caught', 'Three false alarms and one true catch. Accuracy falls. Recall becomes 1.', <<<'PY'
labels = [0] * 99 + [1]
pred = [0] * 96 + [1, 1, 1, 1]
accuracy = sum(p == y for p, y in zip(pred, labels)) / len(labels)
urgent = [i for i, y in enumerate(labels) if y == 1]
recall = sum(pred[i] == 1 for i in urgent) / len(urgent)
print(round(accuracy, 2), recall)
PY],
            'micro' => [
                'If there are zero urgent rows in the test pile, recall division fails. Say “no urgent rows to score” instead of inventing a 0 or a 1.',
                'Balancing a training sample does not change the fact that production data is imbalanced. Score on a test pile that still has the real base rate.',
                'Precision will be low when alarms are many and urgent rows are few. Report it beside recall so the three false alarms are visible.',
            ],
            'quiz' => [
                $q('Why can accuracy look excellent and still be useless?', 'The common class is large, so ignoring the rare class is mostly correct', 'Accuracy counts only the rare class', 'A constant model cannot be accurate', 'Recall is always equal to accuracy'),
                $q('What is rare-class recall in example 1?', '0', '0.99', '1', '0.5'),
                $q('Which test pile matches production?', 'One that keeps the real fraction of urgent notes', 'One that deletes the calm notes', 'One that copies the urgent note until the classes match, then reports accuracy', 'An empty pile'),
            ],
            'practice' => 'Twenty labels, two of them urgent. Always predicting calm: what is the accuracy, and what is the urgent recall?',
        ]),
        $L([
            'section' => '3. A line you can move',
            'title' => 'A square lets a straight fit bend',
            'keywords' => 'polynomial feature, x squared, curve',
            'lead' => 'A straight line in the original feature cannot follow a U shape. If you add a second feature that is the square of the first, the same weighted sum can bend. You did not change the learner. You changed the columns it was allowed to use.',
            'heading' => 'x and x squared are two columns',
            'para' => 'Points (0, 1), (1, 0), (2, 1) sit on a U. No single straight line through length alone hits all three. The prediction bias + w1 * x + w2 * x * x with bias 1, w1 -2, and w2 1 hits them: at 0 the prediction is 1, at 1 it is 0, at 2 it is 1. The square column is computed from x. It is not a new measurement. Because it is computed, you compute it the same way for a future x.',
            'bullets' => [
                'Add the square before you talk about a fancier learner. The bend might be enough.',
                'x squared grows fast. Scale it, or its weight will be tiny and brittle.',
                'A high power can pass through every training point and thrash between them. That is overfit with extra columns.',
                'The square of a negative x is positive. If direction matters, keep the original signed column too.',
            ],
            'rows' => [
                ['x', 'The measured feature', 'Still in the formula.'],
                ['x squared', 'A computed column', 'Lets the sum bend.'],
                ['Same learner', 'bias + weights', 'No new algorithm.'],
                ['Future row', 'Square it the same way', 'Do not square only the training rows.'],
                ['Scale', 'The square is large', 'Fit the scale on training rows.'],
                ['Degree', 'How high a power', 'Higher is not automatically better.'],
            ],
            'e1' => ['Example 1 — the U is exact', 'bias 1, slope -2, square weight 1. All three residuals are 0.', <<<'PY'
def predict(x):
    return 1 + (-2) * x + 1 * x * x

xs = [0, 1, 2]
ys = [1, 0, 1]
print([predict(x) for x in xs])
print([predict(x) - y for x, y in zip(xs, ys)])
PY],
            'e2' => ['Example 2 — a line with no square misses the middle', 'The best story of “a straight line on these three” is not the point. The line through the two ends predicts 1 at the middle, and the truth is 0.', <<<'PY'
def line_through_ends(x):
    return 1

print('curve', 1 + (-2) * 1 + 1 * 1 * 1)
print('line', line_through_ends(1))
PY],
            'micro' => [
                'Example 2’s line is the constant 1, which passes through (0, 1) and (2, 1) and misses (1, 0) by 1. A sloped line cannot hit all three either. Try one.',
                'The weights 1, -2, 1 were chosen because they factor (x - 1) squared. A learner would have to find numbers like these from the residuals.',
                'Compute the square from the raw x you will have in production, not from an already-scaled x, unless training did the same sequence.',
            ],
            'quiz' => [
                $q('What new column lets the weighted sum bend?', 'The square of x', 'A second copy of x', 'The label', 'The row index only'),
                $q('Did the learner change in this lesson?', 'No. The columns changed', 'Yes, it became a tree', 'Yes, it stopped using a bias', 'No, and the columns stayed the same'),
                $q('What is the risk of many high powers?', 'The fit can hit every training point and behave badly between them', 'Squares are always negative', 'The bias becomes illegal', 'You can no longer predict at x = 0'),
            ],
            'practice' => 'Compute the prediction at x = 3 with bias 1, w1 -2, and w2 1. Then say whether that point is between the training xs or beyond them.',
        ]),
        $L([
            'section' => '6. Groups without labels',
            'title' => 'How many clusters, by the leftover distance',
            'keywords' => 'k-means elbow, inertia, choosing k',
            'lead' => 'K-means will happily cut a blob into as many clusters as you ask. The leftover distance is the sum of squared distances from each point to its assigned centre. It always falls as you add centres, because a point can sit on its own centre and contribute zero. The useful question is where the fall flattens.',
            'heading' => 'More centres always fit tighter',
            'para' => 'Points 0, 0, 10, 10 are two pairs. With one centre at 5, the leftover is large. With two centres at 0 and 10, each point sits on a centre and the leftover is 0. With four centres the leftover is still 0. The drop from 1 to 2 is the elbow. The drop from 2 to 4 buys nothing. You pick 2, and you admit the plot is a judgment, not a proof.',
            'bullets' => [
                'Leftover distance is also called inertia. Lower is a tighter fit, not automatically a truer number of groups.',
                'The elbow is the k where the next centre helps much less. If the plot is a smooth curve, there may be no clusters, only a cloud.',
                'Centres must be refit for each k. Do not reuse two centres and add a third without reassigning.',
                'A random start can change the leftover. Run the same k more than once if the points are messy, and keep the tighter fit.',
            ],
            'rows' => [
                ['k', 'How many centres', 'You choose it.'],
                ['Leftover', 'Sum of squared distances to the assigned centre', 'Falls as k grows.'],
                ['Elbow', 'The last useful drop', 'A reading of the plot.'],
                ['k = n', 'Leftover 0', 'Every point is its own centre. Useless.'],
                ['Refit', 'Each k from scratch', 'Assignments change.'],
                ['No elbow', 'A cloud', 'Do not invent groups.'],
            ],
            'e1' => ['Example 1 — two centres drive the leftover to zero', 'One centre at the middle leaves 100. Two centres leave 0.', <<<'PY'
points = [0, 0, 10, 10]

def leftover_one():
    center = sum(points) / len(points)
    return sum((p - center) ** 2 for p in points)

def leftover_two():
    left = [p for p in points if p < 5]
    right = [p for p in points if p >= 5]
    c1 = sum(left) / len(left)
    c2 = sum(right) / len(right)
    return sum((p - c1) ** 2 for p in left) + sum((p - c2) ** 2 for p in right)

print(leftover_one(), leftover_two())
PY],
            'e2' => ['Example 2 — four centres cannot do better than zero', 'The leftover is already 0 at k = 2. Extra centres do not reveal a new group.', <<<'PY'
print('k=2 leftover', 0)
print('k=4 leftover', 0)
print('the drop that mattered was k=1 to k=2')
PY],
            'micro' => [
                'Squared distance punishes a far point more than absolute distance. Know which one you are plotting.',
                'The elbow on these four points is obvious because they were built as two pairs. Real rows are muddier. Draw the points if there are only two features.',
                'Choosing k by the leftover on the same points you will describe is fine for clustering. Do not then claim a predictive accuracy. There are no labels.',
            ],
            'quiz' => [
                $q('What happens to leftover distance as k increases?', 'It falls or stays, it does not rise, when centres are refit optimally', 'It always rises', 'It stays constant for every k', 'It becomes the label'),
                $q('Why is k equal to the number of points a bad default?', 'Leftover distance is zero and you have learned nothing', 'The algorithm cannot run', 'Centres must be negative', 'It maximises the elbow'),
                $q('What is the elbow?', 'The k where adding another centre stops helping much', 'The largest k', 'The mean of the points', 'The first centre'),
            ],
            'practice' => 'Points [0, 1, 20, 21]. Compute the leftover for one centre at the mean. Then describe the two centres you would keep.',
        ]),
        $L([
            'section' => '7. A score you can defend',
            'title' => 'Project: a score you can defend',
            'keywords' => 'machine learning project, baseline, threshold, leakage',
            'lead' => 'This project puts the checks in order on one small table of notes. You split, you scale using training rows only, you beat a majority baseline, you move a cutoff, and you refuse a column that is the label in disguise. The number you would tell someone is the test score of the frozen rule.',
            'heading' => 'Five checks, then one number',
            'para' => 'The notes have a length, a leaked flag, and a label. Training lengths are 4, 6, and 14 with labels 0, 0, and 1. The test note has length 16 and label 1, plus a calm note of length 5 labelled 0. The honest rule is “urgent if length >= 10,” fit by looking at training only. The leaked flag would score perfectly and is dropped. The majority baseline on this test pair gets the long note wrong. The length rule gets both right. That is the number you defend: 2 of 2 on the sealed pair, after the leak was removed.',
            'bullets' => [
                'Write the decision time: when the note arrives, you know its length and not the flag.',
                'The cutoff 10 came from the training lengths, where 14 was urgent and 6 was not.',
                'The test pair is scored once. You do not slide the cutoff after seeing that the test length was 16.',
                'Say the baseline in the same sentence as the model. “2 of 2, and the majority guess got 1 of 2.”',
            ],
            'rows' => [
                ['Decision time', 'Arrival', 'Length is known. The flag is not.'],
                ['Drop', 'The leaked flag', 'It matches the label.'],
                ['Cutoff', '10', 'Between the training calm notes and the training urgent note.'],
                ['Baseline', 'Always calm', 'The training majority.'],
                ['Test', 'Two notes, scored once', 'The published count.'],
                ['Sentence', 'Model versus baseline', 'What you defend.'],
            ],
            'e1' => ['Example 1 — drop the flag, freeze the cutoff, score once', 'The length rule is right on both test notes. The majority guess misses the urgent one.', <<<'PY'
train = [{'length': 4, 'flag': 0, 'label': 0}, {'length': 6, 'flag': 0, 'label': 0}, {'length': 14, 'flag': 1, 'label': 1}]
test = [{'length': 16, 'flag': 1, 'label': 1}, {'length': 5, 'flag': 0, 'label': 0}]
cutoff = 10

def length_rule(row):
    return 1 if row['length'] >= cutoff else 0

majority = 0
print('model', sum(length_rule(row) == row['label'] for row in test), 'of', len(test))
print('baseline', sum(majority == row['label'] for row in test), 'of', len(test))
print('flag equals label', all(row['flag'] == row['label'] for row in train + test))
PY],
            'e2' => ['Example 2 — sliding the cutoff after the test is no longer a test', 'Seeing length 16 and moving the cutoff to 15 still scores well here and is still cheating, because the test row chose the number.', <<<'PY'
test_length = 16
honest_cutoff = 10
peeking_cutoff = 15
print('honest fires', test_length >= honest_cutoff)
print('peeking fires', test_length >= peeking_cutoff)
print('peeking used the test length to pick the cutoff')
PY],
            'micro' => [
                'Two test notes are a demonstration, not a confidence interval. Say “on this pair” rather than “the model is perfect.”',
                'The training majority is calm because two of three training labels are 0. A different training bag would change the baseline and maybe the cutoff.',
                'If you add a word feature later, refit the cutoff on training or validation again. Do not keep 10 by habit.',
            ],
            'quiz' => [
                $q('Which column is dropped, and why?', 'The flag, because it matches the label', 'The length, because it is numeric', 'The label, because training cannot see labels', 'Neither'),
                $q('Where did the cutoff 10 come from?', 'The gap in the training lengths', 'The test length 16', 'The majority baseline', 'A random draw'),
                $q('What do you report beside the model score?', 'The baseline score on the same test rows', 'The training accuracy only', 'The leaked flag’s accuracy', 'The learning rate'),
            ],
            'practice' => 'Add a training note of length 9 labelled 0. Does the cutoff 10 still separate the training labels? If you add length 11 labelled 0, what breaks?',
        ]),
    ];
}
