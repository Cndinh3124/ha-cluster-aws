<?php

declare(strict_types=1);

require __DIR__ . '/config/app.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
$post = $slug !== '' ? get_post_by_slug($pdo, $slug) : null;

if (!$post) {
    http_response_code(404);
    exit('Bài viết không tồn tại.');
}

// Tăng lượt xem
try {
    $inc = $pdo->prepare('UPDATE posts SET views = COALESCE(views,0) + 1 WHERE id = ?');
    $inc->execute([(int)$post['id']]);
    // Reflect increment locally if available
    if (isset($post['views'])) {
        $post['views'] = (int)$post['views'] + 1;
    }
} catch (Throwable $e) {
    // ignore
}

$relatedPosts = get_related_posts($pdo, (int) $post['category_id'], (int) $post['id']);
$pageTitle = $post['title'];

require __DIR__ . '/includes/header.php';
?>

<section class="title-banner">
    <div class="container">
        <div class="breadcrumb">
            <a href="<?= e(base_url()) ?>">Trang chủ</a>
            <span>/</span>
            <a href="<?= e(base_url('category.php?slug=' . $post['category_slug'])) ?>"><?= e($post['category_name']) ?></a>
        </div>
        <h1><?= e($post['title']) ?></h1>
        <div class="meta"><?= e(format_date($post['published_at'])) ?></div>
    </div>
</section>

<section class="content-wrap">
    <div class="container content-grid">
        <article class="post-content">
            <div class="post-body"><?= $post['content'] ?></div>
        </article>

        <aside class="sidebar">
            <h3>Tin liên quan</h3>
            <?php foreach ($relatedPosts as $item): ?>
                <a class="sidebar-item" href="<?= e(base_url('news.php?slug=' . $item['slug'])) ?>">
                    <img src="<?= e(post_cover_image($item)) ?>" alt="<?= e($item['title']) ?>">
                    <div>
                        <strong><?= e($item['title']) ?></strong>
                        <div class="meta"><?= e(format_date($item['published_at'])) ?></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </aside>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
