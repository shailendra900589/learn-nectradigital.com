<?php
require_once '../includes/db.php';
require_once 'ui.php';
require_admin();

$error = '';
$success = '';
$form = [
    'subject' => '',
    'heading' => '',
    'body' => '',
    'cta_text' => '',
    'cta_url' => rtrim(site_base_url(), '/') . '/billing',
    'audience' => 'all',
    'test_email' => app_secret('SMTP_FROM_EMAIL') ?: 'nectradigital3@gmail.com',
];

$template = (string)($_GET['template'] ?? '');
if ($template === 'offer') {
    $offerLines = [];
    foreach ($pdo->query("SELECT * FROM plans WHERE is_active = 1 ORDER BY price ASC")->fetchAll() as $plan) {
        $line = $plan['name'] . ': Rs ' . format_rupees(plan_effective_price($plan));
        if (plan_offer_active($plan)) {
            $line .= ' (was Rs ' . format_rupees((float)$plan['price']) . ')';
        }
        $offerLines[] = $line;
    }
    $form['subject'] = '{name}, learn ad-free for a full year';
    $form['heading'] = 'Your learning, without the ads';
    $form['body'] = "Hi {name},\n\nOur packages give you a full year of ad-free lessons, downloads, and certificates.\n\n" . implode("\n", $offerLines) . "\n\nPick one course, ten courses, or everything on Learn.Nectra.";
    $form['cta_text'] = 'See packages';
} elseif ($template === 'course') {
    $latest = $pdo->query("SELECT title, slug FROM courses ORDER BY id DESC LIMIT 1")->fetch();
    $form['subject'] = 'New on Learn.Nectra: ' . ($latest['title'] ?? 'a new course');
    $form['heading'] = ($latest['title'] ?? 'A new course') . ' is live';
    $form['body'] = "Hi {name},\n\nWe just published " . ($latest['title'] ?? 'a new course') . ". Every lesson is free to read, with practice and a quiz at the end of each chapter.\n\nStart with the first lesson today.";
    $form['cta_text'] = 'Start the course';
    $form['cta_url'] = rtrim(site_base_url(), '/') . '/course/' . ($latest['slug'] ?? '');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    foreach (['subject' => 190, 'heading' => 190, 'body' => 20000, 'cta_text' => 80, 'cta_url' => 500, 'test_email' => 190] as $field => $max) {
        $form[$field] = substr(trim((string)($_POST[$field] ?? $form[$field])), 0, $max);
    }
    $form['audience'] = array_key_exists($_POST['audience'] ?? '', email_audiences()) ? $_POST['audience'] : 'all';
    $ctaValid = $form['cta_url'] === '' || preg_match('#^https://#i', $form['cta_url']);

    if (in_array($action, ['send_test', 'create_campaign'], true)) {
        if ($form['subject'] === '' || $form['heading'] === '' || $form['body'] === '') {
            $error = 'Subject, heading, and message are required.';
        } elseif (!$ctaValid) {
            $error = 'Button link must start with https://';
        } elseif ($action === 'send_test') {
            $testEmail = filter_var($form['test_email'], FILTER_VALIDATE_EMAIL);
            if (!$testEmail) {
                $error = 'Enter a valid test email address.';
            } elseif (send_campaign_mail($form, $testEmail, (string)($_SESSION['admin_username'] ?? 'Admin'), 0)) {
                $success = 'Test email sent to ' . $testEmail . '. Check the inbox, then send the campaign.';
            } else {
                $error = 'Test email could not be sent. Check the Gmail app password.';
            }
        } elseif (empty($_POST['confirm_send'])) {
            $error = 'Tick the confirmation box before sending to students.';
        } else {
            $created = create_email_campaign($pdo, $form, $form['audience'], (int)($_SESSION['admin_id'] ?? 0));
            if ($created['total'] === 0) {
                $error = 'No students match this audience.';
            } else {
                $sentNow = process_email_queue($pdo, 8);
                $success = 'Campaign queued for ' . $created['total'] . ' students. ' . $sentNow . ' sent now; the rest go out automatically in small batches.';
                $form['subject'] = $form['heading'] = $form['body'] = $form['cta_text'] = '';
            }
        }
    } elseif ($action === 'process_now') {
        $sentNow = process_email_queue($pdo, 15);
        $success = $sentNow . ' emails sent in this batch.';
    } elseif ($action === 'cancel_campaign') {
        $pdo->prepare("UPDATE email_campaigns SET status = 'cancelled', finished_at = NOW() WHERE id = :id AND status = 'sending'")->execute(['id' => post_int('campaign_id')]);
        $success = 'Campaign stopped. Emails already sent cannot be recalled.';
    } elseif ($action === 'settings') {
        update_platform_setting($pdo, 'email_daily_cap', (string)max(1, min(2000, post_int('daily_cap', EMAIL_DAILY_CAP_DEFAULT))));
        $success = 'Daily limit saved.';
    }
}

$counts = [];
foreach (email_audiences() as $key => $label) {
    $counts[$key] = email_audience_count($pdo, $key);
}
$optedOut = (int)$pdo->query("SELECT COUNT(*) FROM students WHERE marketing_opt_out = 1")->fetchColumn();
$campaigns = $pdo->query("SELECT * FROM email_campaigns ORDER BY id DESC LIMIT 30")->fetchAll();
$queued = (int)$pdo->query("SELECT COUNT(*) FROM email_queue q JOIN email_campaigns c ON c.id = q.campaign_id AND c.status = 'sending' WHERE q.status = 'queued'")->fetchColumn();
$sentToday = email_sent_today($pdo);
$dailyCap = email_daily_cap($pdo);
$smtpReady = smtp_config()['password'] !== '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Marketing | Learn.Nectra</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo admin_styles(); ?>
</head>
<body class="admin-workspace">
    <?php echo admin_sidebar($pdo, 'email'); ?>

    <main class="admin-main flex-1 overflow-y-auto">
        <header class="admin-header flex items-center justify-between px-8 sticky top-0 z-10">
            <div>
                <div class="text-sm font-black text-blue-600 uppercase tracking-widest">Marketing</div>
                <h1 class="text-2xl font-black text-slate-950">Email Marketing</h1>
            </div>
            <nav class="admin-section-nav" aria-label="Email sections">
                <a href="#compose">Compose</a>
                <a href="#campaigns">Campaigns</a>
            </nav>
        </header>

        <div class="admin-content space-y-8">
            <?php if ($error): ?><div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md"><?php echo h($error); ?></div><?php endif; ?>
            <?php if ($success): ?><div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md"><?php echo h($success); ?></div><?php endif; ?>
            <?php if (!$smtpReady): ?><div class="bg-amber-50 border-l-4 border-amber-500 text-amber-800 p-4 rounded-md">Gmail sending is not configured on this server yet.</div><?php endif; ?>

            <section class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <?php foreach (email_audiences() as $key => $label): ?>
                    <div class="bg-white rounded-xl border border-gray-200 p-5"><div class="text-xs font-bold text-slate-500"><?php echo h($label); ?></div><div class="text-3xl font-black text-slate-900 mt-1"><?php echo number_format($counts[$key]); ?></div></div>
                <?php endforeach; ?>
                <div class="bg-white rounded-xl border border-gray-200 p-5"><div class="text-xs font-bold text-slate-500">Sent today / daily limit</div><div class="text-3xl font-black text-slate-900 mt-1"><?php echo number_format($sentToday); ?> <span class="text-base text-slate-400">/ <?php echo number_format($dailyCap); ?></span></div><div class="text-xs text-slate-500 mt-1"><?php echo number_format($queued); ?> waiting &middot; <?php echo number_format($optedOut); ?> unsubscribed</div></div>
            </section>

            <section id="compose" class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
                <form method="POST" action="email" class="xl:col-span-2 bg-white rounded-xl border border-gray-200 p-6 space-y-4">
                    <?php echo csrf_field(); ?>
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h3 class="text-lg font-black text-slate-900">Compose a promotional email</h3>
                        <div class="flex gap-2 text-xs font-bold">
                            <a href="email?template=offer#compose" class="rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">Use offer template</a>
                            <a href="email?template=course#compose" class="rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">Use new-course template</a>
                        </div>
                    </div>
                    <label class="admin-field"><span>Subject line</span>
                        <input name="subject" value="<?php echo h($form['subject']); ?>" maxlength="190" class="admin-input" placeholder="{name}, your Diwali offer is here" required>
                    </label>
                    <label class="admin-field"><span>Heading inside the email</span>
                        <input name="heading" value="<?php echo h($form['heading']); ?>" maxlength="190" class="admin-input" required>
                    </label>
                    <label class="admin-field"><span>Message (leave a blank line between paragraphs; {name} becomes the student's first name)</span>
                        <textarea name="body" rows="10" class="admin-input" required><?php echo h($form['body']); ?></textarea>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="admin-field"><span>Button text (optional)</span>
                            <input name="cta_text" value="<?php echo h($form['cta_text']); ?>" maxlength="80" class="admin-input" placeholder="See packages">
                        </label>
                        <label class="admin-field"><span>Button link</span>
                            <input name="cta_url" value="<?php echo h($form['cta_url']); ?>" maxlength="500" class="admin-input" placeholder="https://learning.nectradigital.com/billing">
                        </label>
                    </div>
                    <label class="admin-field"><span>Send to</span>
                        <select name="audience" class="admin-input">
                            <?php foreach (email_audiences() as $key => $label): ?>
                                <option value="<?php echo h($key); ?>" <?php echo $form['audience'] === $key ? 'selected' : ''; ?>><?php echo h($label); ?> (<?php echo number_format($counts[$key]); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 space-y-3">
                        <label class="admin-field"><span>Test email address</span>
                            <input name="test_email" type="email" value="<?php echo h($form['test_email']); ?>" class="admin-input">
                        </label>
                        <button name="action" value="send_test" class="admin-btn-dark">Send test email</button>
                    </div>
                    <label class="flex items-center gap-3 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm font-bold text-amber-900">
                        <input type="checkbox" name="confirm_send" value="1" class="w-5 h-5"> I checked the test email. Send this to the selected students.
                    </label>
                    <button name="action" value="create_campaign" class="admin-btn-primary">Send campaign</button>
                </form>

                <aside class="space-y-6">
                    <div class="bg-white rounded-xl border border-gray-200 p-6 text-sm text-slate-600 space-y-2">
                        <h3 class="text-lg font-black text-slate-900">How sending works</h3>
                        <p>Emails go out from your Gmail account in small batches of about <?php echo EMAIL_BATCH_SIZE; ?> per minute while the site has visitors, up to the daily limit below. Gmail allows roughly 500 emails a day.</p>
                        <p>Only verified, active students who have not unsubscribed receive campaigns. Every email has an unsubscribe link.</p>
                        <?php if ($queued > 0): ?>
                            <form method="POST" action="email"><?php echo csrf_field(); ?><input type="hidden" name="action" value="process_now"><button class="admin-btn-dark mt-2">Send next batch now</button></form>
                        <?php endif; ?>
                    </div>
                    <form method="POST" action="email" class="bg-white rounded-xl border border-gray-200 p-6">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="settings">
                        <label class="admin-field"><span>Daily sending limit</span>
                            <input name="daily_cap" type="number" min="1" max="2000" value="<?php echo (int)$dailyCap; ?>" class="admin-input">
                        </label>
                        <button class="admin-btn-dark mt-3">Save limit</button>
                    </form>
                </aside>
            </section>

            <section id="campaigns" class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-gray-200"><h3 class="font-black text-slate-900">Campaigns</h3></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs uppercase text-slate-400 border-b border-gray-100"><tr><th class="p-4">Campaign</th><th class="p-4">Audience</th><th class="p-4">Progress</th><th class="p-4">Status</th><th class="p-4 text-right">Action</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($campaigns as $campaign): ?>
                                <tr>
                                    <td class="p-4"><div class="font-bold"><?php echo h($campaign['subject']); ?></div><div class="text-xs text-slate-500"><?php echo h(date('M j, Y g:i A', strtotime((string)$campaign['created_at']))); ?></div></td>
                                    <td class="p-4"><?php echo h(email_audiences()[$campaign['audience']] ?? $campaign['audience']); ?></td>
                                    <td class="p-4"><?php echo (int)$campaign['sent']; ?> / <?php echo (int)$campaign['total']; ?> sent<?php if ((int)$campaign['failed'] > 0): ?> <span class="text-red-600">&middot; <?php echo (int)$campaign['failed']; ?> failed</span><?php endif; ?></td>
                                    <td class="p-4"><span class="uppercase text-xs font-black"><?php echo h($campaign['status']); ?></span></td>
                                    <td class="p-4 text-right">
                                        <?php if ($campaign['status'] === 'sending'): ?>
                                            <form method="POST" action="email" onsubmit="return confirm('Stop this campaign?');"><?php echo csrf_field(); ?><input type="hidden" name="action" value="cancel_campaign"><input type="hidden" name="campaign_id" value="<?php echo (int)$campaign['id']; ?>"><button class="text-xs font-bold text-red-600 hover:underline">Stop</button></form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (!$campaigns): ?><tr><td colspan="5" class="p-8 text-center text-slate-500">No campaigns yet.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
