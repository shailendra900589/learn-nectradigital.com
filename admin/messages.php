<?php
require_once '../includes/db.php';
require_once 'ui.php';
require_admin();
require_admin_role($pdo, ['owner']);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $message_id = post_int('message_id');

    if ($action === 'delete_message') {
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = :id");
        if ($stmt->execute(['id' => $message_id])) {
            $success = "Message deleted successfully.";
        }
    } elseif ($action === 'toggle_message') {
        $current = post_int('current');
        $stmt = $pdo->prepare("UPDATE messages SET is_read = :status WHERE id = :id");
        $stmt->execute(['status' => $current === 1 ? 0 : 1, 'id' => $message_id]);
        header("Location: messages");
        exit;
    }
}

// --- 3. FETCH MESSAGES ---
// Count unread messages for the badge
$unread_count = $pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn();

// Fetch all messages
$stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inbox | Learn.Nectra</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo admin_styles(); ?>
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased flex h-screen overflow-hidden text-slate-800">
    <?php echo admin_sidebar($pdo, 'messages'); ?>

    <main class="flex-1 flex flex-col overflow-y-auto relative">
        <header class="admin-header flex items-center px-8 sticky top-0 z-10">
            <div>
                <div class="text-sm font-black text-blue-600 uppercase tracking-widest">Inbox</div>
                <h2 class="text-2xl font-black text-slate-950">Messages & Inquiries</h2>
            </div>
        </header>

        <div class="p-8">
            <?php if($success): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm mb-6">
                    <?php echo h($success); ?>
                </div>
            <?php endif; ?>

            <div class="admin-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 w-12 text-center">Status</th>
                                <th class="px-6 py-4">Sender details</th>
                                <th class="px-6 py-4">Subject & Message Preview</th>
                                <th class="px-6 py-4 w-40 text-right">Date</th>
                                <th class="px-6 py-4 w-32 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach($messages as $msg): 
                                $is_read = $msg['is_read'];
                                $js_name = json_encode($msg['name']);
                                $js_email = json_encode($msg['email']);
                                $js_subject = json_encode($msg['subject']);
                                $js_message = json_encode(nl2br(h($msg['message'])));
                                $js_date = json_encode(date('F j, Y, g:i a', strtotime($msg['created_at'])));
                            ?>
                            <tr class="transition hover:bg-slate-50 <?php echo $is_read ? 'opacity-70 bg-gray-50' : 'bg-white'; ?>">
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="messages">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="toggle_message">
                                        <input type="hidden" name="message_id" value="<?php echo (int)$msg['id']; ?>">
                                        <input type="hidden" name="current" value="<?php echo (int)$is_read; ?>">
                                        <button type="submit" title="Mark as <?php echo $is_read ? 'Unread' : 'Read'; ?>" class="block w-full">
                                        <?php if($is_read): ?>
                                            <svg class="w-5 h-5 text-gray-400 mx-auto hover:text-blue-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path></svg>
                                        <?php else: ?>
                                            <svg class="w-5 h-5 text-blue-600 mx-auto hover:text-gray-500 transition" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
                                        <?php endif; ?>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 <?php echo !$is_read ? 'text-black' : ''; ?>"><?php echo h($msg['name']); ?></div>
                                    <div class="text-xs text-blue-600 font-medium"><?php echo h($msg['email']); ?></div>
                                </td>
                                <td class="px-6 py-4 cursor-pointer" onclick='openModal(<?php echo $js_name; ?>, <?php echo $js_email; ?>, <?php echo $js_subject; ?>, <?php echo $js_message; ?>, <?php echo $js_date; ?>)'>
                                    <div class="font-bold text-slate-800 text-sm mb-1 <?php echo !$is_read ? 'text-black' : ''; ?>"><?php echo h($msg['subject'] ?: '(No Subject)'); ?></div>
                                    <div class="text-xs text-gray-500 line-clamp-1 max-w-md"><?php echo h($msg['message']); ?></div>
                                </td>
                                <td class="px-6 py-4 text-right text-xs text-gray-500 font-medium whitespace-nowrap">
                                    <?php echo date('M j, Y', strtotime($msg['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button onclick='openModal(<?php echo $js_name; ?>, <?php echo $js_email; ?>, <?php echo $js_subject; ?>, <?php echo $js_message; ?>, <?php echo $js_date; ?>)' class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Read Full Message">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                        <form method="POST" action="messages" onsubmit="return confirm('Permanently delete this message?')">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="action" value="delete_message">
                                            <input type="hidden" name="message_id" value="<?php echo (int)$msg['id']; ?>">
                                            <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($messages)): ?>
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-slate-500 text-sm">
                                        <div class="mb-3 text-3xl">📭</div>
                                        <p class="font-medium text-slate-700">Your inbox is empty.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div id="messageModal" class="fixed inset-0 z-50 hidden bg-slate-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transform scale-95 transition-transform duration-300" id="modalContent">
            
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-bold text-gray-800 text-lg">Read Message</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <div class="text-sm text-gray-500 uppercase font-bold tracking-wider mb-1">From</div>
                        <div class="font-bold text-gray-900 text-lg" id="modalName">Name</div>
                        <a href="#" id="modalEmail" class="text-blue-600 hover:underline text-sm font-medium">email@example.com</a>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500 uppercase font-bold tracking-wider mb-1">Received</div>
                        <div class="text-sm font-medium text-gray-700" id="modalDate">Date</div>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="text-sm text-gray-500 uppercase font-bold tracking-wider mb-1">Subject</div>
                    <div class="font-bold text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-100" id="modalSubject">Subject</div>
                </div>

                <div>
                    <div class="text-sm text-gray-500 uppercase font-bold tracking-wider mb-1">Message</div>
                    <div class="text-gray-700 leading-relaxed bg-blue-50/50 p-4 rounded-lg border border-blue-100 min-h-[150px]" id="modalBody">
                        Message content goes here...
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                <a href="#" id="modalReplyBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition shadow-sm mr-2">Reply via Email</a>
                <button onclick="closeModal()" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 font-bold py-2 px-6 rounded-lg transition shadow-sm">Close</button>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('messageModal');
        const modalContent = document.getElementById('modalContent');

        function openModal(name, email, subject, message, date) {
            document.getElementById('modalName').textContent = name;
            document.getElementById('modalEmail').textContent = email;
            document.getElementById('modalEmail').href = 'mailto:' + email;
            document.getElementById('modalReplyBtn').href = 'mailto:' + email + '?subject=Re: ' + encodeURIComponent(subject);
            document.getElementById('modalSubject').textContent = subject || '(No Subject)';
            document.getElementById('modalBody').innerHTML = message;
            document.getElementById('modalDate').textContent = date;

            modal.classList.remove('hidden');
            // Slight delay to allow display:block to apply before animating opacity
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
            }, 10);
        }

        function closeModal() {
            modal.classList.add('opacity-0');
            modalContent.classList.add('scale-95');
            // Wait for transition to finish before hiding
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Close modal when clicking outside of it
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });
    </script>
</body>
</html>
