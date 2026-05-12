<?php

declare(strict_types=1);

require __DIR__ . '/config/app.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$notices = get_notice_posts($pdo, 30);
$pageTitle = 'Thông báo';

require __DIR__ . '/includes/header.php';
?>

<section class="notice-banner">
    <div class="container notice-banner-inner">
        <div class="breadcrumb notice-breadcrumb">
            <a href="<?= e(base_url()) ?>">Trang chủ</a>
            <span>•</span>
            <span>Thông báo</span>
        </div>
        <h1>Thông báo</h1>
    </div>
</section>

<section class="content-wrap">
    <div class="container notice-list-wrap">
        <?php if (!$notices): ?>
            <p>Chưa có thông báo nào.</p>
        <?php endif; ?>

        <?php foreach ($notices as $item): ?>
            <article class="notice-item">
                <a class="notice-item-thumb" href="<?= e(base_url('thong-bao-chi-tiet.php?slug=' . $item['slug'])) ?>">
                    <img src="<?= e(post_cover_image($item)) ?>" alt="<?= e($item['title']) ?>">
                </a>
                <div class="notice-item-body">
                    <h3>
                        <a href="<?= e(base_url('thong-bao-chi-tiet.php?slug=' . $item['slug'])) ?>"><?= e($item['title']) ?></a>
                    </h3>
                    <div class="meta"><?= e(format_date($item['published_at'])) ?></div>
                    <p><?= e((string) ($item['excerpt'] ?? '')) ?></p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
