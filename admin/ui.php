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
            $count = (int)$pdo->query("SELECT COUNT(*) FROM payments WHERE status IN ('requested', 'paid') AND NOT (method = 'razorpay' AND status = 'requested')")->fetchColumn();
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
    $current = $active === $key ? ' aria-current="page"' : '';
    return '<a href="' . h($href) . '" class="' . h($class) . '"' . $current . '>' . $icon . '<span>' . h($label) . '</span>' . $badgeHtml . '</a>';
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
    $emailIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>';

    return '
    <aside class="admin-sidebar">
        <a class="admin-brand" href="index">
            <span class="admin-brand-mark" aria-hidden="true">N</span>
            <span>Learn.Nectra<small>Administration</small></span>
        </a>
        <nav class="admin-nav" aria-label="Administration">
            ' . admin_nav_link('index', 'Dashboard', $dashboardIcon, $active, 'dashboard') . '
            ' . admin_nav_link('courses', 'Courses', $coursesIcon, $active, 'courses') . '
            ' . admin_nav_link('students', 'Users & Plans', $studentsIcon, $active, 'students', $paymentBadge) . '
            ' . admin_nav_link('ads', 'Content Ads', $adsIcon, $active, 'ads') . '
            ' . admin_nav_link('messages', 'Inbox', $messagesIcon, $active, 'messages', $messageBadge) . '
            ' . admin_nav_link('email', 'Email Marketing', $emailIcon, $active, 'email') . '
        </nav>
        <div class="admin-account">
            <div class="admin-account-name"><span>Signed in as</span><strong>' . $user . '</strong></div>
            <form method="POST" action="index">
                ' . csrf_field() . '
                <input type="hidden" name="action" value="logout">
                <button type="submit" class="admin-sign-out">Sign Out</button>
            </form>
        </div>
    </aside>';
}

function admin_styles(): string
{
    $css = protected_asset_url('assets/css/admin-ui.css');
    return '<link rel="stylesheet" href="../' . h($css) . '">';
}
