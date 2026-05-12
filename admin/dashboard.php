<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/auth.php';
require_admin_login();

$postCount = (int) $pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn();
$categoryCount = (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$slideCount = (int) $pdo->query('SELECT COUNT(*) FROM sliders')->fetchColumn();

$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>

<div class="grid-3">
    <div class="metric">
        <span>Tổng bài viết</span>
        <strong><?= $postCount ?></strong>
    </div>
    <div class="metric">
        <span>Tổng danh mục</span>
        <strong><?= $categoryCount ?></strong>
    </div>
    <div class="metric">
        <span>Tổng slider</span>
        <strong><?= $slideCount ?></strong>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
