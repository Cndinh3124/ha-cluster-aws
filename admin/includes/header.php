<?php
$pageTitle = $pageTitle ?? 'Admin';
$extraHead   = $extraHead ?? '';
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
$site = get_site_settings($pdo);
$adminLogoUrl = trim((string) ($site['logo_image_url'] ?? ''));
$unreadContacts = 0;
try {
    $unreadContacts = (int) get_unread_contacts_count($pdo);
} catch (Throwable $e) {
    $unreadContacts = 0;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= e(base_url('assets/css/admin.css')) ?>">
    <?= $extraHead ?>
</head>
<body>
<div class="admin-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <?php if ($adminLogoUrl !== ''): ?>
                <span class="sidebar-logo sidebar-logo-image">
                    <img src="<?= e($adminLogoUrl) ?>" alt="Logo">
                </span>
            <?php else: ?>
                <span class="sidebar-logo">TDC</span>
            <?php endif; ?>
            <span>ADMIN</span>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <a href="categories.php" class="<?= $currentPage === 'categories.php' ? 'active' : '' ?>">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h12"/></svg>
                Danh mục
            </a>
            <a href="tin-tuc.php" class="<?= $currentPage === 'tin-tuc.php' ? 'active' : '' ?>">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/></svg>
                Tin tức
            </a>
            <a href="van-ban.php" class="<?= $currentPage === 'van-ban.php' ? 'active' : '' ?>">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7z"/><polyline points="14 2 14 7 19 7"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="15" y2="16"/></svg>
                Văn bản
            </a>
            <a href="lien-he.php" class="<?= $currentPage === 'lien-he.php' ? 'active' : '' ?>">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 8V7a2 2 0 0 0-2-2h-3"/><path d="M3 7v10a2 2 0 0 0 2 2h14"/><path d="M8 12h8"/></svg>
                Liên hệ
                <?php if (!empty($unreadContacts) && $unreadContacts > 0): ?>
                    <span style="display:inline-block;margin-left:8px;background:#e74c3c;color:#fff;padding:2px 8px;border-radius:999px;font-size:12px;font-weight:700;"><?= (int)$unreadContacts ?></span>
                <?php endif; ?>
            </a>
            <a href="sliders.php" class="<?= $currentPage === 'sliders.php' ? 'active' : '' ?>">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3H8a1 1 0 0 0-1 1v3h10V4a1 1 0 0 0-1-1z"/></svg>
                Slider
            </a>
            <a href="settings.php" class="<?= $currentPage === 'settings.php' ? 'active' : '' ?>">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Cài đặt
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="../index.php" target="_blank">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                Xem trang web
            </a>
            <a href="change-password.php" class="">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 1v4"/><path d="M17 3v2"/><path d="M7 3v2"/></svg>
                Đổi mật khẩu
            </a>
            <a href="logout.php" class="logout">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Đăng xuất
            </a>
        </div>
    </aside>
    <section class="content">
        <header class="content-head">
            <h1><?= e($pageTitle) ?></h1>
        </header>
