<?php
require_once 'includes/db.php';
secure_session_start();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf();
    enforce_request_budget('contact-submit', 10, 600);
    if (!empty($_SESSION['last_contact_at']) && time() - (int)$_SESSION['last_contact_at'] < 30) {
        $error = "Please wait a few seconds before sending another message.";
    }
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($error) && !empty($name) && !empty($email) && !empty($message)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
                $stmt->execute(['name' => $name, 'email' => $email, 'subject' => $subject, 'message' => $message]);
                $_SESSION['last_contact_at'] = time();
                $success = "Thank you! Your message has been sent successfully. We will get back to you soon.";
            } catch (PDOException $e) {
                $error = "Oops! Something went wrong. Please try again later.";
            }
        } else {
            $error = "Please enter a valid email address.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

// Set Auto-SEO Variables
$seo_title = localize_text('Contact Learn.Nectra');
$seo_description = localize_text('Contact the Learn.Nectra team for support, partnerships, feedback, or learning questions.');
require_once 'includes/header.php';
ob_start();
?>

<div class="page-intro text-white py-14 md:py-20 border-b border-slate-800 relative overflow-hidden">
    <div class="absolute inset-0 opacity-20 pointer-events-none" style="background: radial-gradient(circle at 50% 20%, #815bc4 0%, transparent 60%);"></div>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <span class="inline-flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wider uppercase mb-4 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Direct Support &amp; Collaboration
        </span>
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white tracking-tight mb-4">
            We'd Love to Hear From You
        </h1>
        <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">
            Have questions about a tutorial, spotted a bug, or want to partner with Learn.Nectra? Drop us a note below and our team will get back to you promptly.
        </p>
    </div>
</div>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
        
        <!-- Left Column: Contact Info Cards -->
        <div class="lg:col-span-4 space-y-5">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black mb-4 border border-emerald-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-slate-900 font-bold text-base mb-1">Email Us Directly</h3>
                <p class="text-slate-500 text-xs mb-3">For general inquiries, business partnerships, or feedback.</p>
                <a href="mailto:support@nectradigital.com" class="text-sm font-bold text-emerald-600 hover:text-emerald-700 hover:underline break-all">
                    support@nectradigital.com
                </a>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black mb-4 border border-blue-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-slate-900 font-bold text-base mb-1">Fast Response Time</h3>
                <p class="text-slate-500 text-xs mb-3">We typically answer student and technical queries within 24 to 48 hours.</p>
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active Support Desk
                </span>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-black mb-4 border border-purple-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-slate-900 font-bold text-base mb-1">Need Quick Answers?</h3>
                <p class="text-slate-500 text-xs mb-3">Check our homepage FAQ for instant solutions regarding courses, playgrounds, and certificates.</p>
                <a href="<?php echo h(lang_href('index#courses')); ?>" class="text-sm font-bold text-blue-600 hover:text-blue-700 hover:underline">
                    Browse All Tutorials &rarr;
                </a>
            </div>
        </div>

        <!-- Right Column: Contact Form -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200 p-8 sm:p-10 shadow-sm">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-2">Send Us a Message</h2>
            <p class="text-slate-500 text-sm mb-8">Fill in the form below and we'll reply to your provided email address.</p>
            
            <?php if($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 flex items-center gap-2 font-medium text-sm">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <span><?php echo h($error); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-6 rounded-2xl mb-6 flex items-start gap-3">
                    <svg class="w-6 h-6 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <div>
                        <h4 class="font-bold text-base text-emerald-950 mb-1">Message Sent Successfully!</h4>
                        <p class="text-sm text-emerald-800"><?php echo h($success); ?></p>
                    </div>
                </div>
            <?php else: ?>

                <form method="POST" action="<?php echo h(lang_href('contact')); ?>" class="space-y-6">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Your Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-slate-50 focus:bg-white text-sm" placeholder="John Doe">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-slate-50 focus:bg-white text-sm" placeholder="john@example.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Subject</label>
                        <input type="text" name="subject" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-slate-50 focus:bg-white text-sm" placeholder="E.g. Question about PHP PDO Tutorial">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Message <span class="text-red-500">*</span></label>
                        <textarea name="message" rows="5" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition bg-slate-50 focus:bg-white text-sm leading-relaxed" placeholder="Type your detailed message or feedback here..."></textarea>
                    </div>

                    <button type="submit" class="w-full sm:w-auto edu-primary-btn py-3.5 px-8 transition-all text-sm font-bold shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        <span>Send Message &raquo;</span>
                    </button>
                </form>

            <?php endif; ?>
        </div>
    </div>
</div>

<?php
echo localize_lesson_html(ob_get_clean());
require_once 'includes/footer.php';
?>
