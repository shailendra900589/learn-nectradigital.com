<?php
declare(strict_types=1);

function admin_unread_count(PDO $pdo): int
{
    static $count = null;
    if ($count === null) {
        try {
            $count = (int)$pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn();
        } catch (Throwable $e) {
            $count = 0;
        }
    }
    return $count;
}

function admin_pending_payment_count(PDO $pdo): int
{
    static $count = null;
    if ($count === null) {
        try {
            $count = (int)$pdo->query("SELECT COUNT(*) FROM payments WHERE status IN ('requested', 'paid')")->fetchColumn();
        } catch (Throwable $e) {
            $count = 0;
        }
    }
    return $count;
}

function admin_nav_link(string $href, string $label, string $icon, string $active, string $key, int $badge = 0): string
{
    $class = $active === $key ? 'admin-nav-link admin-nav-active' : 'admin-nav-link';
    $badgeHtml = $badge > 0 ? '<span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">' . number_format($badge) . '</span>' : '';
    if ($key === 'students' && $badge > 0) {
        $badgeHtml = '<span class="ml-auto bg-amber-400 text-amber-950 text-xs px-2 py-0.5 rounded-full">' . number_format($badge) . '</span>';
    }
    return '<a href="' . h($href) . '" class="' . h($class) . '">' . $icon . '<span>' . h($label) . '</span>' . $badgeHtml . '</a>';
}

function admin_sidebar(PDO $pdo, string $active): string
{
    $messageBadge = admin_unread_count($pdo);
    $paymentBadge = admin_pending_payment_count($pdo);
    $user = h($_SESSION['admin_username'] ?? 'Admin');

    $dashboardIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm10 8h8V3h-8v18zM3 21h8v-6H3v6z"></path></svg>';
    $coursesIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13M4.5 5.5c2.6 0 5.1.5 7.5 1.5 2.4-1 4.9-1.5 7.5-1.5v13c-2.6 0-5.1.5-7.5 1.5-2.4-1-4.9-1.5-7.5-1.5v-13z"></path></svg>';
    $studentsIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m0-4a4 4 0 100-8 4 4 0 000 8zm8 0a4 4 0 100-8 4 4 0 000 8z"></path></svg>';
    $adsIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h10"></path></svg>';
    $messagesIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>';

    return '
    <aside class="w-72 bg-slate-950 text-white flex-shrink-0 flex flex-col border-r border-slate-800">
        <div class="h-20 px-6 flex items-center border-b border-slate-800">
            <div>
                <div class="text-2xl font-black tracking-tight">LMS <span class="text-blue-400">Admin</span></div>
                <div class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Learn.Nectra</div>
            </div>
        </div>
        <nav class="flex-1 px-4 py-5 space-y-2 overflow-y-auto">
            ' . admin_nav_link('index', 'Dashboard', $dashboardIcon, $active, 'dashboard') . '
            ' . admin_nav_link('courses', 'Courses', $coursesIcon, $active, 'courses') . '
            ' . admin_nav_link('students', 'Users & Plans', $studentsIcon, $active, 'students', $paymentBadge) . '
            ' . admin_nav_link('ads', 'Content Ads', $adsIcon, $active, 'ads') . '
            ' . admin_nav_link('messages', 'Inbox', $messagesIcon, $active, 'messages', $messageBadge) . '
        </nav>
        <div class="p-4 border-t border-slate-800">
            <div class="rounded-xl bg-slate-900 border border-slate-800 p-4 mb-3">
                <div class="text-xs text-slate-400 font-bold uppercase tracking-widest">Signed in</div>
                <div class="font-black text-white truncate mt-1">' . $user . '</div>
            </div>
            <form method="POST" action="index">
                ' . csrf_field() . '
                <input type="hidden" name="action" value="logout">
                <button type="submit" class="w-full bg-slate-800 hover:bg-red-600 text-white rounded-xl py-3 font-black transition">Sign Out</button>
            </form>
        </div>
    </aside>';
}

function admin_styles(): string
{
    return '
    <style>
        ::-webkit-scrollbar { width: 7px; height: 7px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
        .admin-card { border: 1px solid #e2e8f0; background: #fff; border-radius: 14px; box-shadow: 0 18px 45px rgba(15, 23, 42, 0.06); }
        .admin-nav-link { display: flex; align-items: center; gap: .75rem; border-radius: .75rem; padding: .75rem 1rem; font-weight: 800; color: #cbd5e1; transition: .18s ease; }
        .admin-nav-link:hover { background: rgba(30, 41, 59, .95); color: #fff; }
        .admin-nav-active { background: #2563eb; color: #fff; box-shadow: 0 10px 24px rgba(37, 99, 235, .25); }
        .admin-header { min-height: 5rem; background: rgba(255,255,255,.92); backdrop-filter: blur(12px); border-bottom: 1px solid #e2e8f0; }
        .admin-input { width: 100%; border: 1px solid #cbd5e1; border-radius: .75rem; padding: .75rem 1rem; outline: none; background: #fff; }
        .admin-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, .14); }
        .admin-btn-primary { background: #2563eb; color: #fff; border-radius: .75rem; padding: .75rem 1.25rem; font-weight: 900; transition: .18s ease; }
        .admin-btn-primary:hover { background: #1d4ed8; }
        .admin-btn-dark { background: #0f172a; color: #fff; border-radius: .75rem; padding: .75rem 1.25rem; font-weight: 900; transition: .18s ease; }
        .admin-btn-dark:hover { background: #1e293b; }
    </style>';
}
?>
