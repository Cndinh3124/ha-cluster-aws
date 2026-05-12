<?php

declare(strict_types=1);

require __DIR__ . '/config/app.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
$category = $slug !== '' ? get_category_by_slug($pdo, $slug) : null;

if (!$category) {
    http_response_code(404);
    exit('Danh mục không tồn tại.');
}

$posts = get_posts_by_category_slug($pdo, $slug);
$pageTitle = $category['name'];

require __DIR__ . '/includes/header.php';
?>

<section class="title-banner">
    <div class="container">
        <div class="breadcrumb">
            <a href="<?= e(base_url()) ?>">Trang chủ</a>
            <span>/</span>
            <span><?= e($category['name']) ?></span>
        </div>
        <h1><?= e($category['name']) ?></h1>
    </div>
</section>

<section class="content-wrap">
    <div class="container list-page">
        <?php if (!$posts): ?>
            <p>Chưa có bài viết trong danh mục này.</p>
        <?php endif; ?>
        <?php foreach ($posts as $item): ?>
            <article class="category-item">
                <img src="<?= e(post_cover_image($item)) ?>" alt="<?= e($item['title']) ?>">
                <div>
                    <h3><a href="<?= e(base_url('news.php?slug=' . $item['slug'])) ?>"><?= e($item['title']) ?></a></h3>
                    <div class="meta"><?= e(format_date($item['published_at'])) ?></div>
                    <p><?= e($item['excerpt'] ?? '') ?></p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
