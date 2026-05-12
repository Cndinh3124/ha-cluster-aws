<?php

declare(strict_types=1);

require __DIR__ . '/config/app.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
$post = $slug !== '' ? get_post_by_slug($pdo, $slug) : null;

if (!$post) {
    http_response_code(404);
    exit('Thông báo không tồn tại.');
}

$pageTitle = $post['title'];
$related = get_notice_posts($pdo, 6);
$related = array_values(array_filter($related, static fn(array $x): bool => (int) $x['id'] !== (int) $post['id']));

require __DIR__ . '/includes/header.php';
?>

<section class="notice-banner">
    <div class="container notice-banner-inner">
        <div class="breadcrumb notice-breadcrumb">
            <a href="<?= e(base_url()) ?>">Trang chủ</a>
            <span>•</span>
            <a href="<?= e(base_url('thong-bao.php')) ?>">Thông báo</a>
        </div>
        <h1><?= e($post['title']) ?></h1>
        <div class="notice-date-line">
            <span><?= e(format_date((string) $post['published_at'])) ?></span>
        </div>
    </div>
</section>

<section class="content-wrap">
    <div class="container content-grid notice-content-grid">
        <article class="post-content notice-post-content">
            <div class="post-body"><?= $post['content'] ?></div>
        </article>

        <aside class="sidebar notice-sidebar">
            <h3>Thông báo khác</h3>
            <?php foreach (array_slice($related, 0, 4) as $item): ?>
                <a class="sidebar-item" href="<?= e(base_url('thong-bao-chi-tiet.php?slug=' . $item['slug'])) ?>">
                    <img src="<?= e(post_cover_image($item)) ?>" alt="<?= e($item['title']) ?>">
                    <div>
                        <strong><?= e($item['title']) ?></strong>
                        <div class="meta"><?= e(format_date((string) $item['published_at'])) ?></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </aside>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
