<?php
require_once 'includes/db.php';
secure_session_start();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf();
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
$seo_title = "Contact Learn.Nectra";
$seo_description = "Contact the Learn.Nectra team for support, partnerships, feedback, or learning questions.";
require_once 'includes/header.php';
?>

<div class="site-hero-light py-16 border-b border-blue-100">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h1 class="text-4xl font-extrabold text-slate-950 tracking-tight mb-4">Get in Touch</h1>
        <p class="text-slate-600 text-lg">Have a question, feedback, or want to collaborate? We'd love to hear from you.</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="edu-soft-card rounded-2xl p-8 md:p-12">
        
        <?php if($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-8 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                <?php echo h($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-8 flex items-center text-lg font-medium">
                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <?php echo h($success); ?>
            </div>
        <?php else: ?>

            <form method="POST" action="contact" class="space-y-6">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Your Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow outline-none bg-gray-50 focus:bg-white" placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow outline-none bg-gray-50 focus:bg-white" placeholder="john@example.com">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Subject</label>
                    <input type="text" name="subject" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow outline-none bg-gray-50 focus:bg-white" placeholder="How can we help you?">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Message <span class="text-red-500">*</span></label>
                    <textarea name="message" rows="5" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow outline-none bg-gray-50 focus:bg-white" placeholder="Write your message here..."></textarea>
                </div>

                <button type="submit" class="w-full edu-primary-btn py-4 transition-all focus:outline-none focus:ring-4 focus:ring-blue-300 text-lg">
                    Send Message
                </button>
            </form>

        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
