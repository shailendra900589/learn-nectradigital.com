<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/track_seed.php';

function ml_course_meta(): array
{
    return [
        'title' => 'Machine Learning with Python',
        'slug' => 'machine-learning-python',
        'keywords' => 'machine learning python, beginner, supervised learning, regression, classification, overfitting, precision recall, k-means, from scratch',
        'description' => 'Machine learning from the first row of data to a small classifier you can explain. Pure Python, no hidden library magic: distance, loss, gradient steps, logistic probabilities, precision and recall, trees, clusters, and a project. Every chapter keeps the small points and runnable examples. No earlier chapter is removed when this track is updated.',
    ];
}

function ml_chapters(): array
{
    $f = 'ml_notes.py';
    $L = static function (array $c) use ($f): array {
        return track_lesson('python', $f, $c);
    };
    $q = static function (string $question, string $ok, string $a, string $b, string $c): array {
        return bquiz($question, $ok, $a, $b, $c);
    };
    $loss = svg_line_graph(
        'Loss falling across six steps',
        ['0', '1', '2', '3', '4', '5'],
        [2.4, 1.6, 1.1, 0.8, 0.6, 0.5],
        '',
        '#059669',
        'A healthy step gets smaller errors. If the line jumps up, the step size is too big.'
    );
    return [
        $L([
            'section' => '0. What learning means',
            'title' => 'A rule learned from examples',
            'keywords' => 'machine learning definition, examples, rule, prediction',
            'lead' => 'A normal program is a rule you wrote. A learned rule is a rule adjusted until it agrees with examples. The examples are the teacher. If the examples are wrong, the rule will be confidently wrong.',
            'heading' => 'You still decide the shape of the rule',
            'para' => 'Learning does not invent the question. You choose the inputs, the answer you want, and the family of rules you will allow, such as “a straight line” or “the nearest example.” The procedure only picks numbers inside that family. That is why this course starts with the family, not with a library name.',
            'bullets' => [
                'An example is one past situation plus the answer you believe was right.',
                'A prediction is the rule applied to a new situation whose answer you might not know yet.',
                'If you cannot say what a mistake looks like, you cannot learn. “Be smart” is not a mistake you can measure.',
                'Memorising the examples is a legal rule and a bad one. The next chapters separate memory from a rule that still works on a new row.',
            ],
            'rows' => [
                ['Example', 'Input plus known answer', 'One row of the table.'],
                ['Rule family', 'The shapes you allow', 'A line, a neighbour, a tree.'],
                ['Fit', 'Pick the numbers', 'Slope, or which neighbour, from the examples.'],
                ['Predict', 'Use the numbers', 'A new row goes in. An answer comes out.'],
                ['Mistake', 'Prediction versus truth', 'You need both. A prediction alone is not a score.'],
                ['Library', 'A later shortcut', 'This course writes the steps so the shortcut is not magic.'],
            ],
            'e1' => ['Example 1 — a rule you wrote versus one you fit', 'The written rule says urgent when length is at least 12. The fit rule uses the average length of urgent notes as a threshold.', <<<'PY'
notes = [('buy milk', 0), ('server is down now', 1), ('tea', 0), ('pay invoice today', 1)]

def written(text):
    return 1 if len(text) >= 12 else 0

urgent_lengths = [len(text) for text, label in notes if label == 1]
threshold = sum(urgent_lengths) / len(urgent_lengths)

def fitted(text):
    return 1 if len(text) >= threshold else 0

print('threshold', threshold)
print('written', written('disk full tonight'))
print('fitted', fitted('disk full tonight'))
PY],
            'e2' => ['Example 2 — a mistake you can count', 'Compare predictions to labels. The count is the score, not a feeling.', <<<'PY'
rows = [('buy milk', 0), ('server down', 1)]

def predict(text):
    return 1 if 'down' in text else 0

mistakes = 0
for text, label in rows:
    if predict(text) != label:
        mistakes += 1
print('mistakes', mistakes)
PY],
            'micro' => [
                'The label 1 means urgent in this course. 0 means not. Say that every time so 1 is not “true” in some chapters and “first class” in others.',
                'A threshold learned from only the urgent rows ignores how long the calm notes are. It is a teaching step, not a good model. Later chapters use both.',
                'len(text) treats spaces as characters. “a b” has length 3. Decide if that is the feature you meant.',
            ],
            'quiz' => [
                $q('What is an example in this lesson?', 'An input plus a known answer', 'A prediction with no truth', 'A Python import', 'A graph edge'),
                $q('What does the procedure actually pick?', 'Numbers inside a rule family you chose', 'A new programming language', 'The true future with certainty', 'Random labels'),
                $q('Why is “be smart” not a learning target?', 'You cannot count that mistake', 'Python forbids it', 'Smart rules use no data', 'It is already a straight line'),
            ],
            'practice' => 'Write three notes and label them urgent or not. Say one feature you could measure with len or with a word test.',
        ]),
        $L([
            'section' => '0. What learning means',
            'title' => 'Rows, features, and labels',
            'keywords' => 'feature, label, table, supervised learning',
            'lead' => 'A supervised table has rows. Each row is one event. The columns you are allowed to see at prediction time are features. The column you must guess is the label. If the label is hiding inside a feature, you cheated.',
            'heading' => 'The label is the answer column',
            'para' => '“Urgent” is a label. “The number of words” is a feature you can compute from the text before you know the label. “Was marked urgent by the editor” is the label copied into a feature, and the model will look brilliant and be useless. That leak is the small point.',
            'bullets' => [
                'One row, one decision. Do not put two notes in one row and then ask for one label.',
                'Features must be available when you predict. Tomorrow’s price is not a feature for predicting tomorrow’s price.',
                'Missing values are not zero unless you decided that zero means missing. Zero can be a real measurement.',
                'Keep the raw text if you can. A feature you regret is easier to rebuild than a column you deleted.',
            ],
            'rows' => [
                ['Row', 'One event', 'One note, one house, one visit.'],
                ['Feature', 'A number or category you may see', 'Length, word count, hour.'],
                ['Label', 'What you must predict', 'Urgent or not. Price. The next word later, in the AI course.'],
                ['Leak', 'Answer hidden in the input', 'The model cheats.'],
                ['Missing', 'Unknown, not always 0', 'Pick a policy and write it down.'],
                ['Unit', 'What the number means', 'Characters versus words. Do not mix them silently.'],
            ],
            'e1' => ['Example 1 — build the columns', 'Two features: characters and the word down. The label stays separate.', <<<'PY'
raw = [('buy milk', 0), ('server down', 1)]
table = []
for text, label in raw:
    features = [len(text), 1 if 'down' in text else 0]
    table.append((features, label))
print(table)
PY],
            'e2' => ['Example 2 — a leaked column', 'The third number is the label copied in. The “model” that reads it never misses, and never works on a new note where that column is absent.', <<<'PY'
def cheat(features):
    return features[2]

print('cheating score', cheat([9, 0, 1]))
print('honest features only', [9, 0])
PY],
            'micro' => [
                '1 if "down" in text else 0 is a binary feature. It is 0 or 1, not a count of how many times the word appears. Counts come later.',
                'Case matters. "Down" in text is false if you only look for "down". Lowercase the text if you mean the word regardless of case.',
                'The tuple (features, label) keeps the answer beside the inputs so you cannot accidentally train on a list that lost the labels.',
            ],
            'quiz' => [
                $q('Which column is the label?', 'The answer you must predict', 'Every number', 'The row number', 'The Python version'),
                $q('What is a leak?', 'The answer is smuggled into the features', 'A missing import', 'A long text', 'A zero'),
                $q('Can tomorrow’s true price be a feature for predicting tomorrow’s price?', 'No, you will not have it at prediction time', 'Yes, always', 'Only on Tuesdays', 'Only if you call it x'),
            ],
            'practice' => 'Take the note “Pay invoice”. List two legal features and one leaked column you must not use.',
        ]),
        $L([
            'section' => '0. What learning means',
            'title' => 'Train and test: memorising is not learning',
            'keywords' => 'train test split, generalization, memorisation',
            'lead' => 'If you score a rule on the same rows you used to build it, a perfect memory scores perfectly and still fails on the next note. Hold some rows back. Fit on the train rows. Score once on the test rows.',
            'heading' => 'The test rows are not a second chance to tweak',
            'para' => 'A simple split takes the first part as train and the last part as test only when the order is not a secret pattern you wanted to keep together. Shuffling with a fixed seed makes the split repeatable. Looking at the test score and then changing the rule again turns the test into train. Then you need a fresh holdout.',
            'bullets' => [
                'Never fit on the test rows. Not even “a little”.',
                'A fixed seed means the shuffle is the same tomorrow, so a bug is reproducible.',
                'Tiny tests swing. Three test rows can jump from 0 mistakes to 2 by luck. Say the count, not only a percent.',
                'If the rows are a timeline, do not shuffle the future into the past. Train on earlier days, test on later days.',
            ],
            'rows' => [
                ['Train', 'Rows that may change the rule', 'Fit here.'],
                ['Test', 'Rows that only score', 'Touch them once at the end.'],
                ['Seed', 'Repeatable shuffle', 'random.Random(0), not an unseeded shuffle you cannot rerun.'],
                ['Leak via test', 'Tuning on the score', 'The test is now train.'],
                ['Time order', 'No shuffle', 'Future rows stay in the test.'],
                ['Count', 'Mistakes out of n', 'Percentages hide small n.'],
            ],
            'e1' => ['Example 1 — fit only on train', 'The threshold comes from train labels. Test is scored after.', <<<'PY'
rows = [
    ('buy milk', 0),
    ('server down', 1),
    ('tea', 0),
    ('pay today', 1),
    ('hello', 0),
    ('disk full', 1),
]
train, test = rows[:4], rows[4:]
lengths = [len(text) for text, label in train if label == 1]
threshold = sum(lengths) / len(lengths)
mistakes = 0
for text, label in test:
    pred = 1 if len(text) >= threshold else 0
    if pred != label:
        mistakes += 1
print('threshold from train', threshold)
print('test mistakes', mistakes, 'of', len(test))
PY],
            'e2' => ['Example 2 — a seeded shuffle', 'The same seed gives the same first row. A different seed may not.', <<<'PY'
import random
rows = ['a', 'b', 'c', 'd']
rnd = random.Random(0)
bag = rows[:]
rnd.shuffle(bag)
print(bag)
rnd2 = random.Random(0)
bag2 = rows[:]
rnd2.shuffle(bag2)
print(bag == bag2)
PY],
            'micro' => [
                'rows[:4] is a copy of the slice. Changing train later does not change the source if you replace items inside the tuples, because tuples do not let you. Lists inside would be shared.',
                'If every train label is 0, the urgent length list is empty and the average crashes. Check that.',
                'Report mistakes and the test size. “50%” on 2 rows is one mistake.',
            ],
            'quiz' => [
                $q('Which rows may change the threshold?', 'Only the train rows', 'Only the test rows', 'Whichever look worse', 'All of them, then call the rest a test'),
                $q('Why use a fixed seed?', 'So the split can be repeated', 'So the model is always correct', 'Seeds delete the test', 'Python requires seed 0'),
                $q('What is wrong with tuning the rule after seeing the test score, then quoting that score?', 'The test has become part of training', 'Nothing', 'The seed breaks', 'Labels swap automatically'),
            ],
            'practice' => 'Split six imagined notes into four train and two test. Write which two you are not allowed to use for the threshold.',
        ]),
        $L([
            'section' => '1. Distance and lines',
            'title' => 'Nearest neighbour',
            'keywords' => 'k nearest neighbours, euclidean distance, tie',
            'lead' => 'The nearest-neighbour rule says: find the train row closest to the new row, and copy its label. With k = 3 you copy the majority of the three closest. Distance is the whole model. There is no separate “training” except remembering the train rows.',
            'heading' => 'Measure, do not guess which column dominates',
            'para' => 'Euclidean distance is the straight line in feature space: square each difference, add, square root. A feature in thousands will dominate a feature in units unless you scale them first. The next chapter scales. This chapter shows the domination so you feel it. Ties need a rule: this lesson picks the smaller index.',
            'bullets' => [
                'k = 1 copies one neighbour and memorizes noise. k = 3 votes.',
                'Even k can tie. Prefer odd k for two labels.',
                'You must store the train rows. Prediction scans them. That is O(train size) per guess.',
                'Distance to itself is 0. If the new row was accidentally in the train set, the neighbour is itself and the score is a cheat.',
            ],
            'rows' => [
                ['Distance', 'How far two feature rows are', 'Square, add, root.'],
                ['k = 1', 'Copy the nearest label', 'Fragile.'],
                ['Majority', 'Most common label among k', 'Break ties on purpose.'],
                ['Scale', 'Columns with huge numbers dominate', 'Scale before you trust distance.'],
                ['Store train', 'The model is the data', 'There is no small list of weights.'],
                ['Leak', 'Test row inside train', 'Distance 0. Remove duplicates across the split.'],
            ],
            'e1' => ['Example 1 — one neighbour', 'Features are (length, has_down). (10, 1) is nearer to (11, 1) than to (3, 0).', <<<'PY'
def dist(a, b):
    total = 0
    for x, y in zip(a, b):
        total += (x - y) ** 2
    return total ** 0.5

train = [([3, 0], 0), ([11, 1], 1), ([4, 0], 0)]

def predict(features):
    best = None
    for row, label in train:
        d = dist(features, row)
        if best is None or d < best[0]:
            best = (d, label)
    return best

print(predict([10, 1]))
PY],
            'e2' => ['Example 2 — a huge column drowns the other', 'Column 0 is length in the thousands. The useful 0/1 column barely moves the distance.', <<<'PY'
def dist(a, b):
    return sum((x - y) ** 2 for x, y in zip(a, b)) ** 0.5

print('same flag, different length', dist([1000, 1], [1001, 1]))
print('same length, different flag', dist([1000, 1], [1000, 0]))
PY],
            'micro' => [
                'You can compare squared distances and skip the square root when you only need the nearest. The root does not change the order.',
                'zip stops at the shorter row. Two rows with different feature counts silently ignore the extras. Check the lengths.',
                'best is None at the start so the first train row always wins the “no best yet” case. Starting at distance 0 would mean nothing is nearer.',
            ],
            'quiz' => [
                $q('What does 1-nearest-neighbour copy?', 'The label of the closest train row', 'The average of all labels always', 'The first train row always', 'A random label'),
                $q('Why can one column drown the others?', 'Its numbers are much larger, so they dominate the sum of squares', 'Python squares only the first column', 'Labels are distances', 'k must be 0'),
                $q('What is the model after “training” a nearest neighbour?', 'The stored train rows', 'A single slope', 'An empty set', 'A sorted copy of the labels only'),
            ],
            'practice' => 'Train rows (0, 0) label 0 and (2, 2) label 1. Which label do you predict for (0, 2)? Compute both distances.',
        ]),
        $L([
            'section' => '1. Distance and lines',
            'title' => 'Mean, spread, and scaling',
            'keywords' => 'mean, variance, standard deviation, standardise',
            'lead' => 'The mean is the balance point. The spread is how far rows sit from that point. Scaling subtracts the mean and divides by the spread so a column in thousands and a column in units can share a distance.',
            'heading' => 'Fit the scale on train, apply it to test',
            'para' => 'The mean and the spread are learned numbers. Compute them on train only. If you compute them on train plus test, the test helped define the ruler. A spread of zero means the column never changes. Dividing by zero is the bug. Leave that column as zeros after subtracting the mean.',
            'bullets' => [
                'Mean is sum divided by count. The count is the train rows, not the test rows.',
                'Variance here is the average of squared distances from the mean. The square root is the standard deviation, the spread you divide by.',
                'After scaling, the train column has mean about 0 and spread about 1, unless the spread was zero.',
                'A new test value can be outside the train range. That is legal. Do not clip it unless you meant to.',
            ],
            'rows' => [
                ['Mean', 'Balance point', 'Train only.'],
                ['Spread', 'Root of average squared gap', 'Zero means a constant column.'],
                ['Scale', '(x - mean) / spread', 'The new unit.'],
                ['Divide by zero', 'Constant column', 'Do not divide. Emit 0 after centering.'],
                ['Test', 'Use train mean and spread', 'Never recompute on test.'],
                ['Negative scaled value', 'Below the train mean', 'Normal. Not an error.'],
            ],
            'e1' => ['Example 1 — scale a column', 'Train lengths 2, 4, 6. Mean 4. A test length of 8 is above the mean.', <<<'PY'
train = [2, 4, 6]
mean = sum(train) / len(train)
var = sum((x - mean) ** 2 for x in train) / len(train)
spread = var ** 0.5

def scale(x):
    if spread == 0:
        return 0.0
    return (x - mean) / spread

print('mean', mean, 'spread', spread)
print('train scaled', [round(scale(x), 4) for x in train])
print('test 8', round(scale(8), 4))
PY],
            'e2' => ['Example 2 — a constant column', 'Spread is 0. Scaling returns 0 instead of crashing.', <<<'PY'
train = [5, 5, 5]
mean = sum(train) / len(train)
var = sum((x - mean) ** 2 for x in train) / len(train)
spread = var ** 0.5
print('spread', spread)
print('scaled', 0.0 if spread == 0 else (5 - mean) / spread)
PY],
            'micro' => [
                'Some people divide variance by n - 1. For a ruler used only to scale, be consistent. This lesson divides by n. Say so.',
                'round in the print is for eyes. Do not round before the model uses the number.',
                'Scale every feature with its own mean and spread. One global scale does not fix a drowned column.',
            ],
            'quiz' => [
                $q('Where do you compute the mean used for scaling?', 'On the train rows', 'On the test rows', 'On one future row only', 'You should not subtract a mean'),
                $q('What do you do when the spread is 0?', 'Do not divide; treat the centered value as 0', 'Divide anyway', 'Delete the train set', 'Set the mean to 1'),
                $q('A scaled value below 0 means?', 'The original value was below the train mean', 'The row is illegal', 'The label is urgent', 'The spread was negative'),
            ],
            'practice' => 'Train values 1 and 3. Compute the mean by hand. Scale the test value 5 using that mean and the spread you calculate.',
        ]),
        $L([
            'section' => '1. Distance and lines',
            'title' => 'A straight line, slope, and intercept',
            'keywords' => 'linear regression, slope, intercept, prediction',
            'lead' => 'A straight line predicts a number: output = slope times feature + intercept. The slope is how much the output moves when the feature grows by 1. The intercept is the output when the feature is 0. Both are just numbers you will adjust.',
            'heading' => 'Read the line before you fit it',
            'para' => 'If the slope is 2 and the intercept is 1, a feature of 3 predicts 7. A negative slope means the output falls as the feature rises. A slope of 0 means the feature is ignored and every prediction is the intercept. That is a legal line and a useless feature.',
            'bullets' => [
                'The feature and the label must be numbers for this line. A word is not multiplied by a slope until you turn it into a number.',
                'Two features need two slopes: w1 * x1 + w2 * x2 + b. The picture is a flat sheet, still called linear.',
                'The intercept is a weight on a feature that is always 1. That trick lets every term be “weight times input”.',
                'A line cannot turn a corner. If the true pattern bends, the line is the wrong family. You will see that as a loss that will not go near zero.',
            ],
            'rows' => [
                ['Slope', 'Change per unit of x', 'Also called a weight.'],
                ['Intercept', 'Prediction at x = 0', 'Also called bias. Not the bias of a dataset.'],
                ['Predict', 'w * x + b', 'One number in, one number out.'],
                ['w = 0', 'Feature ignored', 'The line is flat.'],
                ['Several x', 'One weight each', 'Plus one intercept.'],
                ['Wrong family', 'The pattern bends', 'A line will stay a line.'],
            ],
            'e1' => ['Example 1 — read a known line', 'Slope 2, intercept 1. Feature 3 predicts 7. Feature 0 predicts the intercept.', <<<'PY'
def predict(x, slope, intercept):
    return slope * x + intercept

print(predict(3, 2, 1))
print(predict(0, 2, 1))
print(predict(3, 0, 1))
PY],
            'e2' => ['Example 2 — two features', 'Length weight 0.5, flag weight 4, intercept 1. The flag moves the prediction more than one extra character.', <<<'PY'
def predict(length, flag, w_len, w_flag, b):
    return w_len * length + w_flag * flag + b

print(predict(10, 0, 0.5, 4, 1))
print(predict(10, 1, 0.5, 4, 1))
PY],
            'micro' => [
                'Order of operations is weight times feature, then add. slope * x + intercept is not slope * (x + intercept) unless you meant that.',
                'A negative prediction is allowed for a number such as temperature. For a price you might clip later, and you should say you clipped.',
                'The word bias here means intercept. In a later chapter bias means a systematic miss. Do not mix the sentences.',
            ],
            'quiz' => [
                $q('What does the slope say?', 'How much the prediction changes when the feature grows by 1', 'The prediction when x is 0', 'The number of rows', 'The test score'),
                $q('What is the prediction when x is 0?', 'The intercept', 'The slope', 'Always 1', 'Always the label'),
                $q('A slope of 0 means?', 'That feature does not change the prediction', 'The line is vertical', 'The data is perfect', 'You must divide by zero'),
            ],
            'practice' => 'Slope 3, intercept -1. Predict for x = 4 by hand, then run it.',
        ]),
        $L([
            'section' => '2. Loss and a step',
            'title' => 'The error, the loss, and the average',
            'keywords' => 'loss, mean squared error, residual',
            'lead' => 'The error on one row is prediction minus truth. The loss is a single number that says how bad a whole set of errors is. Mean squared error squares each error, then averages. Squares punish big misses more than small ones, and they erase the sign so misses above and below do not cancel.',
            'heading' => 'Do not let plus and minus cancel',
            'para' => 'If one row is high by 5 and another is low by 5, the average raw error is 0 and the line looks perfect. It is not. Squaring makes both 25. The mean of the squares is the loss. Lower is better. Zero means every train prediction matched, which may be memory rather than a good line.',
            'visual' => $loss,
            'bullets' => [
                'Residual means truth minus prediction or the reverse. Pick one and keep it. This lesson uses prediction minus truth.',
                'Divide by the number of rows, not by a constant you forgot to update.',
                'A single huge error dominates the square. That is a feature of this loss and a warning about outliers.',
                'The loss is on a set of rows. One row’s square is not yet the mean.',
            ],
            'rows' => [
                ['Error', 'prediction - truth', 'Sign says the direction.'],
                ['Square', 'Kills the sign', 'Also punishes big errors more.'],
                ['Mean', 'Average over rows', 'Comparable across different counts if you divide.'],
                ['Zero loss', 'Perfect on these rows', 'Not a promise about new rows.'],
                ['Cancel', 'Why raw average fails', '+5 and -5 look like no error.'],
                ['Outlier', 'One huge square', 'Inspect that row before you trust the fit.'],
            ],
            'e1' => ['Example 1 — squares do not cancel', 'The raw average error is 0. The mean square is 25.', <<<'PY'
errors = [5, -5]
raw = sum(errors) / len(errors)
mse = sum(e * e for e in errors) / len(errors)
print('raw', raw)
print('mse', mse)
PY],
            'e2' => ['Example 2 — loss of a flat prediction', 'Always predicting 0 against truths 1 and 3. Mean square is 5.', <<<'PY'
truths = [1, 3]
pred = 0
loss = sum((pred - y) ** 2 for y in truths) / len(truths)
print(loss)
PY],
            'micro' => [
                'Mean absolute error averages the absolute value instead of the square. It is less shocked by one outlier. This course uses squares because the gradient is simple.',
                'Percent error is a different number. Do not call MSE a percent.',
                'If you forget to divide, the loss grows just because you added rows. Comparisons across datasets then lie.',
            ],
            'quiz' => [
                $q('Why square the errors?', 'So opposite signs do not cancel, and big misses count more', 'So the loss becomes a probability', 'So the slope stays 0', 'Squares are required by Python'),
                $q('What is wrong with an average of raw errors of +5 and -5?', 'It is 0 even though both rows missed', 'It is 25', 'It cannot be computed', 'It is the slope'),
                $q('A train loss of 0 means?', 'Every train row was predicted exactly', 'Every future row will be exact', 'The slope is 1', 'The test was used'),
            ],
            'practice' => 'Truths 2 and 4, prediction 3 for both. Compute each error and the mean square by hand.',
        ]),
        $L([
            'section' => '2. Loss and a step',
            'title' => 'One step of gradient descent',
            'keywords' => 'gradient descent, learning rate, derivative',
            'lead' => 'Gradient descent nudges the slope and the intercept in the direction that reduces the loss. The gradient is “which way is up.” You step the other way. The learning rate is how big the step is. Too big and the loss jumps. Too small and you wait forever.',
            'heading' => 'Nudge opposite the slope of the loss',
            'para' => 'For mean squared error and one feature, the gradient for the weight is the average of (prediction - truth) times x. The gradient for the intercept is the average of (prediction - truth). Subtract learning_rate times the gradient. One step is not training. It is one correction. You repeat it.',
            'bullets' => [
                'The learning rate is not learned from the data in this lesson. You choose it, often 0.01 or 0.1, and you watch the loss.',
                'A step that increases the loss is too large, or the gradient formula is wrong.',
                'Update the weight and the intercept from the same predictions. If you update the weight first and recompute, you used a mixed step.',
                'Features with huge values make huge gradients. Scaling, from earlier, keeps the step sane.',
            ],
            'rows' => [
                ['Gradient', 'Direction of increase', 'You subtract it.'],
                ['Learning rate', 'Step size', 'A choice. Not a feature.'],
                ['Weight grad', 'Average of error * x', 'For this loss and a line.'],
                ['Intercept grad', 'Average of error', 'The feature is 1.'],
                ['Same predictions', 'One snapshot', 'Then update both.'],
                ['Loss up', 'Bad step or bad formula', 'Stop and look.'],
            ],
            'e1' => ['Example 1 — one nudge', 'Data x = 1, 2 and y = 2, 4, which is the line y = 2x. Start from slope 0. One step moves the slope up. Loss falls.', <<<'PY'
xs = [1, 2]
ys = [2, 4]
w, b = 0.0, 0.0
rate = 0.1

def loss(w, b):
    err = [(w * x + b - y) ** 2 for x, y in zip(xs, ys)]
    return sum(err) / len(xs)

before = loss(w, b)
preds = [w * x + b for x in xs]
err = [p - y for p, y in zip(preds, ys)]
grad_w = sum(e * x for e, x in zip(err, xs)) / len(xs)
grad_b = sum(err) / len(xs)
w -= rate * grad_w
b -= rate * grad_b
print('before', before)
print('after', loss(w, b), 'w', w, 'b', b)
PY],
            'e2' => ['Example 2 — a step that is too big', 'Rate 2 overshoots this tiny problem and the loss rises.', <<<'PY'
xs = [1, 2]
ys = [2, 4]
w = b = 0.0

def loss(w, b):
    return sum((w * x + b - y) ** 2 for x, y in zip(xs, ys)) / len(xs)

before = loss(w, b)
err = [w * x + b - y for x, y in zip(xs, ys)]
grad_w = sum(e * x for e, x in zip(err, xs)) / len(xs)
w -= 2 * grad_w
print('before', before, 'after huge step', loss(w, b))
PY],
            'micro' => [
                'Average, not sum, in the gradient, so the learning rate means the same thing when you add rows. If you use the sum, shrink the rate.',
                'Integer weights surprise you: 0 - something may stay int if you started with 0 not 0.0. Start with 0.0.',
                'This gradient is for squared error and a line. A different loss has a different gradient. Do not reuse the formula by habit.',
            ],
            'quiz' => [
                $q('Which way do you step?', 'Opposite the gradient', 'In the gradient direction, to increase loss', 'A random direction always', 'You set the weight to the label'),
                $q('What is the learning rate?', 'The size of the nudge', 'The slope of the data line', 'The test score', 'The number of features'),
                $q('A step that raises the loss usually means?', 'The step is too large, or the gradient is wrong', 'Training is finished', 'The labels are perfect', 'You should square the rate'),
            ],
            'practice' => 'With one row x = 1, y = 1, w = 0, b = 0, compute the error and the two gradients by hand.',
        ]),
        $L([
            'section' => '2. Loss and a step',
            'title' => 'Linear regression from scratch',
            'keywords' => 'linear regression training loop, epochs',
            'lead' => 'Training a line is the same nudge, repeated. Each pass over the train rows is an epoch. You watch the loss. It should trend down and then flatten. You stop after a chosen number of epochs or when the loss almost stops moving.',
            'heading' => 'A loop of predict, gradient, nudge',
            'para' => 'Keep the train rows fixed. Do not reshuffle the meaning of x and y. After the last epoch, the weights are the model. Prediction on a new x does not update the weights. If you keep updating on the test row, you trained on it.',
            'bullets' => [
                'Print the loss every epoch while you learn. Remove the noisy print in a product, but never stop looking at the curve.',
                'A loss that becomes nan means the step exploded. Lower the rate.',
                'Start weights at 0 for this lesson so the run is repeatable. Random starts need a seed.',
                'The fitted line is still a line. Plot or print a few predictions beside the truths.',
            ],
            'rows' => [
                ['Epoch', 'One pass over train', 'Many epochs, one dataset.'],
                ['Weights', 'The model', 'Slope and intercept.'],
                ['Predict', 'No update', 'Test and production.'],
                ['nan', 'Exploded step', 'Lower the learning rate.'],
                ['Flatten', 'A reason to stop', 'More epochs may not help this family.'],
                ['Seed', 'Repeatable start', '0.0 start is a seed.'],
            ],
            'e1' => ['Example 1 — fit y = 2x', 'After a few dozen small steps the slope is near 2 and the intercept near 0.', <<<'PY'
xs = [1, 2, 3]
ys = [2, 4, 6]
w = b = 0.0
rate = 0.05
for epoch in range(40):
    preds = [w * x + b for x in xs]
    err = [p - y for p, y in zip(preds, ys)]
    grad_w = sum(e * x for e, x in zip(err, xs)) / len(xs)
    grad_b = sum(err) / len(xs)
    w -= rate * grad_w
    b -= rate * grad_b
print(round(w, 3), round(b, 3))
print([round(w * x + b, 3) for x in xs])
PY],
            'e2' => ['Example 2 — do not train on the question', 'The new x is 4. The weights stay as they were. The prediction is about 8.', <<<'PY'
w, b = 2.0, 0.0
x_new = 4
print(w * x_new + b)
print('weights unchanged', w, b)
PY],
            'micro' => [
                '40 epochs is a choice for this tiny exact line. A noisy dataset may still be moving. Look at the loss, not the ritual number.',
                'round only when printing. The stored w is the full float.',
                'If y is 2x + 3, the intercept should approach 3. If it approaches 0, the intercept gradient is missing.',
            ],
            'quiz' => [
                $q('What is an epoch?', 'One pass over the training rows', 'One test row', 'The learning rate', 'The intercept'),
                $q('Does predicting a new x change the weights in this lesson?', 'No', 'Yes, always', 'Only if the loss is zero', 'Only on epoch 40'),
                $q('A loss of nan usually means?', 'The step size exploded', 'The model is perfect', 'You need more features of text', 'The list was empty and that is success'),
            ],
            'practice' => 'Fit y = x + 1 on x = 0, 1, 2 by running a short loop. Say whether the slope landed near 1.',
        ]),
        $L([
            'section' => '3. Did it really learn',
            'title' => 'Overfit and underfit',
            'keywords' => 'overfitting, underfitting, train versus test loss',
            'lead' => 'Underfit means the family is too stiff or you barely trained: both train and test losses stay high. Overfit means the rule memorised train: train loss is tiny and test loss is worse. The gap is the clue. A low train loss alone is not a celebration.',
            'heading' => 'Quote two losses',
            'para' => 'A nearest neighbour with k = 1 usually has zero train loss, because each row’s nearest neighbour can be itself if you score the train set with the train set. The test loss tells the truth. A line can underfit a curve: both losses stay high and more epochs do not create a bend.',
            'bullets' => [
                'Always print train and test. One number hides the gap.',
                'More features can overfit when rows are few. A feature per row can memorise.',
                'More epochs on a flexible rule can overfit. More epochs on a line usually just finish the fit.',
                'Collect more rows, use a stiffer family, or stop earlier. Those are the three honest responses.',
            ],
            'rows' => [
                ['Underfit', 'Both losses high', 'Wrong family or not trained.'],
                ['Overfit', 'Train low, test worse', 'Memorised.'],
                ['Gap', 'Test minus train', 'The clue. Not a formal proof.'],
                ['k = 1', 'Memorises train', 'If you allow a row to be its own neighbour.'],
                ['A line on a curve', 'Underfit', 'Epochs will not bend it.'],
                ['More rows', 'A real fix', 'Better than a more theatrical model.'],
            ],
            'e1' => ['Example 1 — k = 1 scores itself', 'Distance from a train row to itself is 0, so train accuracy is perfect and empty of news.', <<<'PY'
train = [([0], 0), ([1], 1)]

def predict(features, rows):
    best = None
    for row, label in rows:
        d = abs(features[0] - row[0])
        if best is None or d < best[0]:
            best = (d, label)
    return best[1]

print('train on itself', [predict(row, train) for row, _ in train])
print('new point 0.1', predict([0.1], train))
PY],
            'e2' => ['Example 2 — a line cannot fit a corner', 'Truth is 0, 1, 0 at x = 0, 1, 2. A flat mean predicts 1/3 everywhere. The loss cannot hit 0.', <<<'PY'
ys = [0, 1, 0]
mean = sum(ys) / len(ys)
loss = sum((mean - y) ** 2 for y in ys) / len(ys)
print('mean', mean, 'loss', loss)
PY],
            'micro' => [
                'Scoring k = 1 on train without removing the row is not a test. Leave-one-out removes it. That is a later honesty.',
                'A small gap can be luck on a tiny test. Say the test count.',
                'Early stopping means “stop epochs when the test loss starts rising.” Using the test that way spends it. A third split, validation, is the clean version.',
            ],
            'quiz' => [
                $q('Overfit looks like what?', 'Low train loss and worse test loss', 'High loss on both', 'Zero test loss and high train loss only', 'A slope of 0'),
                $q('Why is k = 1 train accuracy often perfect?', 'Each row is the nearest neighbour of itself', 'k = 1 uses the test labels', 'Distances cannot be 0', 'Python sorts the labels'),
                $q('More epochs will not fix which problem?', 'A line trying to follow a corner', 'A step size that is slightly small', 'A missing print', 'An unsorted import'),
            ],
            'practice' => 'Invent train loss 0.1 and test loss 1.4. Say overfit or underfit, and one thing you would change.',
        ]),
        $L([
            'section' => '3. Did it really learn',
            'title' => 'Logistic: a probability between 0 and 1',
            'keywords' => 'logistic regression, sigmoid, probability',
            'lead' => 'A line can predict 2 or -3. A yes/no label cannot be 2. The sigmoid squashes any number into a probability between 0 and 1. Logistic regression is a line, then a sigmoid.',
            'heading' => 'The line is the evidence, the sigmoid is the probability',
            'para' => 'score = w * x + b. probability = 1 / (1 + e ** -score). A large positive score becomes nearly 1. A large negative score becomes nearly 0. Zero score is 0.5. You still choose a threshold later to turn the probability into a yes or no. 0.5 is the usual start, not a law.',
            'bullets' => [
                'The output is not a class until you threshold it. Keep the probability in the print so you can see “barely urgent”.',
                'e ** -score overflows when score is a huge negative in some languages. Python handles big ints carefully, but a very large score can still be inf. Clamp extreme scores if you hit that.',
                'This is still one straight score. It cannot draw a circle around a class without more features.',
                'Train with a loss for probabilities, not mean squared error, when you grow up the code. This chapter shows the squash. The step uses a simple error (probability - label), which is the logistic gradient for this model.',
            ],
            'rows' => [
                ['Score', 'w * x + b', 'Any real number.'],
                ['Sigmoid', '1 / (1 + e^-score)', 'Between 0 and 1.'],
                ['0.5', 'Score 0', 'The middle, not “the truth”.'],
                ['Threshold', 'Turn probability into 0 or 1', 'A choice. 0.5 is common.'],
                ['Gradient', 'probability - label', 'Times x, for this model.'],
                ['Not a percent you invent', 'It comes from the score', 'Do not type 0.9 because you hope.'],
            ],
            'e1' => ['Example 1 — squash three scores', 'Negative, zero, and positive.', <<<'PY'
import math

def sigmoid(score):
    return 1 / (1 + math.exp(-score))

for score in [-2, 0, 2]:
    print(score, round(sigmoid(score), 4))
PY],
            'e2' => ['Example 2 — one training step on a yes/no', 'Label 1, x = 1, start at 0. The probability starts at 0.5 and the weight moves up.', <<<'PY'
import math
w = b = 0.0
x, label = 1.0, 1.0
rate = 0.5
score = w * x + b
prob = 1 / (1 + math.exp(-score))
error = prob - label
w -= rate * error * x
b -= rate * error
print('prob before', prob)
print('w', w, 'b', b)
PY],
            'micro' => [
                'math.exp is e to a power. 2.718 ** score is the same idea. Use math.exp.',
                'A probability of 0.99 is not certainty. The next chapter’s threshold decides the action.',
                'Labels must be 0 or 1 for this error. A label of 2 makes the error meaningless.',
            ],
            'quiz' => [
                $q('What does the sigmoid do to a score?', 'Squashes it between 0 and 1', 'Sorts the dataset', 'Squares the loss only', 'Makes the slope zero'),
                $q('What probability is a score of 0?', '0.5', '0', '1', '2'),
                $q('Is the sigmoid output already a class?', 'No, you still choose a threshold', 'Yes, it is always 0 or 1', 'Yes, it is the label', 'Only when the score is negative'),
            ],
            'practice' => 'Compute the sigmoid of 0 by hand. Then say whether a score of 4 should be nearer 0 or nearer 1.',
        ]),
        $L([
            'section' => '3. Did it really learn',
            'title' => 'Accuracy can lie',
            'keywords' => 'accuracy, class imbalance, baseline',
            'lead' => 'Accuracy is correct predictions divided by all predictions. If 99 of 100 notes are calm, a rule that always says calm scores 99% and never catches an urgent note. Accuracy without a baseline is a vanity number.',
            'heading' => 'Beat the boring rule',
            'para' => 'The boring rule predicts the most common train label every time. Your model must beat that on the test set, on the question you care about. If urgent notes are rare and expensive to miss, accuracy is the wrong headline. The next chapter counts the misses by type.',
            'bullets' => [
                'Always say the count: 3 of 10, not only 30%.',
                'Compute the majority baseline on train labels, then score that constant rule on test. Do not peek at test to choose the majority.',
                'A model that loses to “always calm” is not a model you ship.',
                'Balanced data hides this lesson. Try a set with one urgent and nine calm notes.',
            ],
            'rows' => [
                ['Accuracy', 'Correct / all', 'Hides rare classes.'],
                ['Majority', 'Most common train label', 'The boring baseline.'],
                ['Always calm', 'Predict 0', 'High accuracy when urgent is rare.'],
                ['Beat it', 'The minimum bar', 'On the test set.'],
                ['Count', 'With the percent', 'Small tests lie in percent.'],
                ['Cost', 'A miss is not equal', 'Accuracy treats every row the same.'],
            ],
            'e1' => ['Example 1 — 90% and useless', 'Nine calm, one urgent. Always calm gets 9 of 10.', <<<'PY'
labels = [0, 0, 0, 0, 0, 0, 0, 0, 0, 1]
preds = [0] * 10
correct = sum(p == y for p, y in zip(preds, labels))
print(correct, 'of', len(labels))
PY],
            'e2' => ['Example 2 — the baseline from train', 'Train majority is 0. Test has one urgent. The baseline misses it. A rule that flags the word down catches it and may add a false alarm.', <<<'PY'
train_labels = [0, 0, 0, 1]
majority = 0 if train_labels.count(0) >= train_labels.count(1) else 1
test = [('hello', 0), ('server down', 1)]
base_miss = sum((majority != label) for _, label in test)
model_miss = 0
for text, label in test:
    pred = 1 if 'down' in text else 0
    if pred != label:
        model_miss += 1
print('majority', majority, 'baseline misses', base_miss)
print('word rule misses', model_miss)
PY],
            'micro' => [
                'count on a list of labels is fine for two classes. For many classes, use a dict of counts.',
                'Ties in the majority: this lesson prefers 0 when counts are equal because of >=. Write that down.',
                'Accuracy on train for the always-majority rule is the class fraction. It is not a test score.',
            ],
            'quiz' => [
                $q('Why can 99% accuracy be useless?', 'The rare class may never be predicted', 'Percents cannot exceed 90', 'The majority baseline is always 0%', 'Python rounds 99 to 0'),
                $q('Where do you choose the majority label?', 'From the train labels', 'From the test labels', 'From the predictions', 'Always class 1'),
                $q('What should a useful model beat?', 'The boring majority rule on the test set', '100% train accuracy only', 'A random import', 'The learning rate'),
            ],
            'practice' => 'Ten labels, two of them 1. What accuracy does always-0 get? Is that good if missing a 1 is expensive?',
        ]),
        $L([
            'section' => '3. Did it really learn',
            'title' => 'Precision, recall, and the four boxes',
            'keywords' => 'precision, recall, confusion matrix, false positive',
            'lead' => 'Split the mistakes. A false alarm says urgent when the note is calm. A miss says calm when the note is urgent. Precision asks, of the notes you called urgent, how many were. Recall asks, of the truly urgent notes, how many you caught.',
            'heading' => 'Name the four counts before the ratios',
            'para' => 'True positive: urgent and you said urgent. False positive: calm and you said urgent. False negative: urgent and you said calm. True negative: calm and you said calm. Precision is tp / (tp + fp). Recall is tp / (tp + fn). If you never say urgent, precision has a zero denominator. Say “no predictions” instead of crashing or inventing 0.',
            'bullets' => [
                'Raising the threshold usually raises precision and lowers recall. You fire less often, and you miss more.',
                'The positive class is urgent, label 1, in this course. Precision of the calm class is a different sentence.',
                'A confusion matrix is the four counts in a square. Print the counts even when you also print the ratios.',
                'Accuracy uses all four. Precision and recall ignore true negatives on purpose, because the calm majority is large.',
            ],
            'rows' => [
                ['TP', 'Said 1, was 1', 'A catch.'],
                ['FP', 'Said 1, was 0', 'False alarm.'],
                ['FN', 'Said 0, was 1', 'A miss.'],
                ['TN', 'Said 0, was 0', 'Correct calm.'],
                ['Precision', 'tp / (tp + fp)', 'Trust in the alarms.'],
                ['Recall', 'tp / (tp + fn)', 'Coverage of the real urgents.'],
            ],
            'e1' => ['Example 1 — the four counts', 'Predictions and labels line up. One miss, one false alarm.', <<<'PY'
labels = [1, 1, 0, 0]
preds = [1, 0, 1, 0]
tp = sum(p == 1 and y == 1 for p, y in zip(preds, labels))
fp = sum(p == 1 and y == 0 for p, y in zip(preds, labels))
fn = sum(p == 0 and y == 1 for p, y in zip(preds, labels))
tn = sum(p == 0 and y == 0 for p, y in zip(preds, labels))
print('tp', tp, 'fp', fp, 'fn', fn, 'tn', tn)
print('precision', tp / (tp + fp))
print('recall', tp / (tp + fn))
PY],
            'e2' => ['Example 2 — never firing', 'Precision is undefined. The code says so instead of dividing by zero.', <<<'PY'
tp, fp, fn = 0, 0, 2
if tp + fp == 0:
    print('precision undefined, no positive predictions')
else:
    print(tp / (tp + fp))
print('recall', tp / (tp + fn))
PY],
            'micro' => [
                'zip the preds and labels. If the lists differ in length, you silently drop the tail. Assert the lengths.',
                'Threshold 0.9 on a probability model fires less. Recompute the four counts. Do not reuse the old precision.',
                'F1 is a blend of precision and recall. It still hides the raw counts. Print the counts first.',
            ],
            'quiz' => [
                $q('Recall answers which question?', 'Of the truly urgent notes, how many did you catch?', 'Of the notes you called urgent, how many were?', 'What percent of all notes were calm?', 'How big was the learning rate?'),
                $q('A false positive is?', 'You said urgent and it was calm', 'You said calm and it was urgent', 'A true catch', 'A missing feature'),
                $q('Why can precision be undefined?', 'You never predicted urgent, so the denominator is 0', 'Recall is 1', 'There are no labels', 'The threshold is 0.5'),
            ],
            'practice' => 'Labels 1, 0, 1 and preds 1, 1, 0. Count tp, fp, and fn by hand.',
        ]),
        $L([
            'section' => '4. Other shapes',
            'title' => 'A decision stump: one question',
            'keywords' => 'decision stump, threshold, gini idea',
            'lead' => 'A stump asks one question: is the feature at least a threshold? Each side gets the majority label of the train rows that landed there. It is a tree with one split. It is weak, readable, and the seed of a larger tree.',
            'heading' => 'Try thresholds you actually saw',
            'para' => 'A useful threshold sits between feature values, or on a value, as long as you say which side includes the equal. This lesson uses feature >= threshold. The candidates are the train feature values. For each candidate, count train mistakes. Keep the threshold with the fewest. Score it later on test, not by reusing the train mistake count as the headline.',
            'bullets' => [
                'One feature only. A second feature needs a second question, which is a deeper tree.',
                'The label on each side is the majority of that side. An empty side should not happen if the threshold is a value someone had, but guard it.',
                'Ties in mistakes: keep the first threshold you saw, and say so.',
                'A stump on a useless feature is no better than the global majority. That comparison is the baseline again.',
            ],
            'rows' => [
                ['Question', 'x >= t', 'One split.'],
                ['Candidates', 'Train values of x', 'You do not need every decimal.'],
                ['Side label', 'Majority on that side', 'Computed on train.'],
                ['Score', 'Train mistakes to choose t', 'Test mistakes to publish.'],
                ['Empty side', 'Guard it', 'Do not take a majority of nothing.'],
                ['Baseline', 'No split', 'The stump must earn its keep.'],
            ],
            'e1' => ['Example 1 — pick a threshold', 'Feature is length. Urgent notes are the long ones. The search finds a split.', <<<'PY'
rows = [(3, 0), (4, 0), (10, 1), (12, 1)]

def mistakes(threshold):
    count = 0
    for x, y in rows:
        pred = 1 if x >= threshold else 0
        if pred != y:
            count += 1
    return count

best = None
for x, _ in rows:
    m = mistakes(x)
    if best is None or m < best[0]:
        best = (m, x)
print(best)
PY],
            'e2' => ['Example 2 — labels on each side', 'Threshold 10. Left is all 0. Right is all 1. A new length 9 predicts 0. Length 10 predicts 1.', <<<'PY'
rows = [(3, 0), (4, 0), (10, 1), (12, 1)]
t = 10
left = [y for x, y in rows if x < t]
right = [y for x, y in rows if x >= t]

def majority(labels):
    return 1 if labels.count(1) > labels.count(0) else 0

print('left', left, 'right', right)

def predict(x):
    return majority(right) if x >= t else majority(left)

print(predict(9), predict(10))
PY],
            'micro' => [
                '>= puts the equal value on the right. < puts it on the left. The two sides must cover every row exactly once.',
                'Choosing t by train mistakes will look optimistic. The printed best[0] is not a test score.',
                'majority returns 0 on a tie because it uses >. Write that if both classes appear equally.',
            ],
            'quiz' => [
                $q('How many questions does a stump ask?', 'One', 'One per row', 'As many as a forest', 'None, it is k-means'),
                $q('Where does the side’s label come from?', 'The majority label of train rows on that side', 'The test set', 'Always 1', 'The threshold itself'),
                $q('The train mistake count used to pick the threshold is?', 'Not the number you publish as the test score', 'The final accuracy', 'Always zero', 'The learning rate'),
            ],
            'practice' => 'Rows (1, 0), (2, 0), (3, 1). Which thresholds of 1, 2, and 3 make zero train mistakes?',
        ]),
        $L([
            'section' => '4. Other shapes',
            'title' => 'K-means: groups with no labels',
            'keywords' => 'k-means, clustering, unsupervised, centroid',
            'lead' => 'Sometimes there is no label. K-means still groups rows. You choose k, the number of groups. Each group has a center. Rows join the nearest center. Then each center moves to the mean of its rows. Repeat. The groups are not “the truth”. They are a compression you should look at.',
            'heading' => 'Assign, then move the centers',
            'para' => 'Start with k of the rows as centers so you do not invent a center outside the data by accident. A group that loses all its rows has no mean. Keep the old center or reseed it. This lesson uses a tiny fixed start so the prints match. A different start can produce a different grouping. That is normal, and it is why you run it more than once in real work.',
            'bullets' => [
                'k is your choice. The data does not contain a column named k.',
                'There is no accuracy until you invent an external label. Do not pretend the cluster id is a grade.',
                'Scale the features first or a large column decides the groups alone.',
                'Stop when assignments stop changing, or after a max number of rounds so a rare loop cannot spin.',
            ],
            'rows' => [
                ['k', 'How many groups', 'Chosen, not predicted.'],
                ['Center', 'The mean of members', 'Also called a centroid.'],
                ['Assign', 'Nearest center', 'Same distance as before.'],
                ['Update', 'Move center to the mean', 'Empty groups need a policy.'],
                ['Start', 'Changes the result', 'Fix the start in the lesson.'],
                ['Unsupervised', 'No labels used', 'Do not score it like classification.'],
            ],
            'e1' => ['Example 1 — two obvious clumps', 'Centers start at the first two points. One round already separates them.', <<<'PY'
points = [(0, 0), (0, 1), (5, 5), (6, 5)]
centers = [points[0], points[1]]

def dist(a, b):
    return sum((x - y) ** 2 for x, y in zip(a, b)) ** 0.5

assign = []
for p in points:
    best_i = min(range(len(centers)), key=lambda i: dist(p, centers[i]))
    assign.append(best_i)
print(assign)
new_centers = []
for i in range(2):
    members = [p for p, a in zip(points, assign) if a == i]
    if not members:
        new_centers.append(centers[i])
        continue
    dim = len(members[0])
    new_centers.append(tuple(sum(p[d] for p in members) / len(members) for d in range(dim)))
print(new_centers)
PY],
            'e2' => ['Example 2 — an empty group stays put', 'If nobody is assigned to center 1, the code keeps the old center instead of dividing by zero.', <<<'PY'
centers = [(0, 0), (9, 9)]
members = [[(0, 0), (1, 0)], []]
print('empty', len(members[1]) == 0)
print('kept', centers[1])
PY],
            'micro' => [
                'Squared distance is enough to choose the nearest center. The printed center should still be the arithmetic mean, not the squared mean.',
                'Cluster ids 0 and 1 can swap if you swap the start. Compare groupings, not the raw id numbers, across runs.',
                'K-means looks for round clumps around a mean. A ring-shaped cloud is the wrong shape for it.',
            ],
            'quiz' => [
                $q('Does k-means use labels?', 'No', 'Yes, it is supervised', 'Only the test labels', 'Only when k is 1'),
                $q('What is k?', 'The number of groups you chose', 'The accuracy', 'The learning rate', 'The label column'),
                $q('What do you do with a group that has no members?', 'Do not divide by zero; keep or reseed the center', 'The mean is 0', 'Delete the dataset', 'Set k to 0'),
            ],
            'practice' => 'Points (0, 0) and (10, 10), k = 2, centers starting at those two points. Which point joins which center?',
        ]),
        $L([
            'section' => '4. Other shapes',
            'title' => 'A penalty for huge weights',
            'keywords' => 'regularisation, ridge, weight decay',
            'lead' => 'Huge weights fit noise. A penalty adds the square of the weights to the loss, so the fit pays rent on every large weight. The intercept is usually not penalised, because shifting the whole prediction is not the same as a wild slope.',
            'heading' => 'The loss gains a rent term',
            'para' => 'New loss = mean squared error + lambda * (w ** 2). Lambda is how hard you charge rent. Zero lambda is ordinary regression. The gradient of w ** 2 with respect to w is 2w, so the weight update also shrinks w a little every step. That shrink is why people call it weight decay.',
            'bullets' => [
                'Lambda is chosen by you, using a validation split, not the final test.',
                'Do not penalise the intercept in this lesson. Penalising it pulls every prediction toward 0 even when the truths sit at 10.',
                'Standardise features first so the penalty treats them fairly. A feature in thousands otherwise needs a tiny weight and looks “already regularised”.',
                'Too much lambda underfits. The weights go to 0 and you are back to a flat line.',
            ],
            'rows' => [
                ['Lambda', 'The rent', '0 means no penalty.'],
                ['w squared', 'The penalty', 'Big slopes cost more.'],
                ['Not b', 'Intercept is free', 'In this lesson.'],
                ['Gradient extra', '2 * lambda * w', 'Added to the data gradient. If lambda already includes the 2 in your formula, do not double it. This lesson adds lambda * w as a simple shrink.'],
                ['Too large', 'Underfit', 'Weights collapse.'],
                ['Scale first', 'Fair rent', 'Otherwise units decide the penalty.'],
            ],
            'e1' => ['Example 1 — the penalty pulls w down', 'Same data gradient, plus a shrink. The penalised weight moves less far up.', <<<'PY'
xs = [1, 2]
ys = [2, 4]
w = 0.0
rate = 0.1
err = [w * x - y for x, y in zip(xs, ys)]
grad = sum(e * x for e, x in zip(err, xs)) / len(xs)
plain = w - rate * grad
penalised = w - rate * (grad + 0.5 * w)
print('plain', plain)
print('penalised', penalised)
PY],
            'e2' => ['Example 2 — a heavy penalty flattens', 'Start from a fitted slope of 2. A few strong shrinks walk it toward 0. The intercept is left alone.', <<<'PY'
w, b = 2.0, 1.0
rate, lam = 0.2, 5
for _ in range(10):
    w = w - rate * lam * w
print(round(w, 4), b)
PY],
            'micro' => [
                'The simple shrink w = w - rate * lambda * w is weight decay. The full gradient also has the data term. Do not drop the data term when you actually train.',
                'Lambda is not the learning rate. They both change the step and they are different knobs.',
                'L1 penalty uses the absolute value and can drive a weight to exactly 0. This lesson uses squares, which shrink but rarely hit exact 0 in a few steps.',
            ],
            'quiz' => [
                $q('What does the penalty discourage?', 'Huge weights', 'Any use of train data', 'A non-zero intercept always', 'Printing the loss'),
                $q('Why leave the intercept unpenalised here?', 'A global shift is not a wild slope', 'Intercepts cannot be numbers', 'The loss forbids b', 'Test rows own the intercept'),
                $q('A very large lambda tends to?', 'Underfit, with weights near 0', 'Overfit more', 'Set accuracy to 100%', 'Remove the labels'),
            ],
            'practice' => 'Start w at 4. Apply one shrink with rate 0.1 and lambda 1, data gradient 0. What is w?',
        ]),
        $L([
            'section' => '5. The project',
            'title' => 'Project: is this note urgent?',
            'keywords' => 'classification project, baseline, logistic, holdout',
            'lead' => 'You will decide if a short note is urgent. The features are only things you can see in the text: length, and whether it contains a word from a small alarm list. You fit a logistic score on train, choose nothing on test except the final count, and you beat the majority baseline or you admit you did not.',
            'heading' => 'A pipeline you can say out loud',
            'para' => 'Lowercase the text so “Down” and “down” match. Build features. Split a fixed list into train and test without shuffling the meaning away: the last two rows are test because the list was written train-first. Fit a few gradient steps. Threshold 0.5. Print baseline misses and model misses on test, plus the four counts.',
            'bullets' => [
                'The alarm list is a feature design, chosen before you look at test mistakes. Changing it after the test print spends the test.',
                'Start weights at 0. Rate 0.2. Thirty epochs. These are choices, written down.',
                'If the model does not beat always-calm, say so. A project that hides the baseline is not finished.',
                'This is not a production moderator. It is a ruler you understand.',
            ],
            'rows' => [
                ['Lowercase', 'Before features', 'Case is not the signal.'],
                ['Features', 'Length and alarm word', 'Both available at prediction time.'],
                ['Split', 'Last rows held out', 'Written in order on purpose.'],
                ['Fit', 'Train only', 'Logistic steps.'],
                ['Threshold', '0.5', 'Said in advance.'],
                ['Report', 'Baseline and model', 'Test misses and the four counts.'],
            ],
            'e1' => ['Example 1 — the whole small project', 'Train has both classes. Test includes one urgent note with the word down and one calm note.', <<<'PY'
import math
notes = [
    ('buy milk', 0),
    ('read a book', 0),
    ('server down', 1),
    ('disk full tonight', 1),
    ('hello team', 0),
    ('prod is down', 1),
]
alarms = ('down', 'full', 'urgent')

def features(text):
    text = text.lower()
    flag = 1 if any(word in text for word in alarms) else 0
    return [len(text) / 20, flag]

train, test = notes[:4], notes[4:]
w = [0.0, 0.0]
b = 0.0
rate = 0.2

def sigmoid(score):
    return 1 / (1 + math.exp(-score))

for _ in range(30):
    gw = [0.0, 0.0]
    gb = 0.0
    for text, label in train:
        x = features(text)
        prob = sigmoid(w[0] * x[0] + w[1] * x[1] + b)
        err = prob - label
        for i in range(2):
            gw[i] += err * x[i]
        gb += err
    n = len(train)
    for i in range(2):
        w[i] -= rate * gw[i] / n
    b -= rate * gb / n

def predict(text):
    x = features(text)
    prob = sigmoid(w[0] * x[0] + w[1] * x[1] + b)
    return 1 if prob >= 0.5 else 0, prob

majority = 0
base_miss = sum(majority != label for _, label in test)
model_miss = 0
tp = fp = fn = tn = 0
for text, label in test:
    pred, prob = predict(text)
    print(text, 'prob', round(prob, 3), 'pred', pred, 'truth', label)
    if pred != label:
        model_miss += 1
    if pred == 1 and label == 1:
        tp += 1
    elif pred == 1 and label == 0:
        fp += 1
    elif pred == 0 and label == 1:
        fn += 1
    else:
        tn += 1
print('baseline misses', base_miss, 'model misses', model_miss)
print('tp', tp, 'fp', fp, 'fn', fn, 'tn', tn)
PY],
            'e2' => ['Example 2 — the same words, different case', 'Without lower, “DOWN” misses the alarm list. With lower, the flag is 1.', <<<'PY'
alarms = ('down', 'full', 'urgent')
raw = 'Server DOWN'
print('without lower', 1 if any(word in raw for word in alarms) else 0)
print('with lower', 1 if any(word in raw.lower() for word in alarms) else 0)
PY],
            'micro' => [
                'Length is divided by 20 so it sits near the 0/1 flag. That is a rough scale so one gradient step is not dominated by raw character counts.',
                'any(word in text) is a substring test. The word “full” matches “full” inside “fullest”. For a real product, split on words. This project is honest about the substring.',
                'Thirty epochs from zero may leave the probability near 0.5 on a tiny set. If both test misses equal the baseline, add epochs or look at the printed probs before you claim a win.',
            ],
            'quiz' => [
                $q('Which rows update the weights?', 'The train rows', 'The test rows', 'Only rows that contain down', 'None, logistic models have no weights'),
                $q('Why lowercase the text?', 'So Down and down share a feature', 'To delete the label', 'To increase accuracy by definition', 'Python requires it for len'),
                $q('What do you compare the model against?', 'The majority baseline on the test rows', 'Train accuracy only', '100% as a required result', 'The learning rate'),
            ],
            'practice' => 'Add a test note “FULL disk” and say what the alarm flag is after lowercasing, before you predict.',
        ]),
    ];
}
