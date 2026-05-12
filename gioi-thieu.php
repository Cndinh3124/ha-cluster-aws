<?php

declare(strict_types=1);

require __DIR__ . '/config/app.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$site = get_site_settings($pdo);
$pageTitle = (string) ($site['about_title'] ?? 'Giới thiệu Đoàn - Hội');

require __DIR__ . '/includes/header.php';
?>

<section class="notice-banner">
    <div class="container notice-banner-inner">
        <div class="breadcrumb notice-breadcrumb">
            <a href="<?= e(base_url()) ?>">Trang chủ</a>
            <span>•</span>
            <span>Giới thiệu</span>
        </div>
        <h1><?= e((string) ($site['about_title'] ?? 'Giới thiệu Đoàn - Hội Sinh viên Trường')) ?></h1>
        <div class="notice-date-line">
            <span><?= e((string) ($site['about_subtitle'] ?? 'Đoàn Thanh niên Cộng sản Hồ Chí Minh - Trường Cao đẳng Công nghệ Thủ Đức')) ?></span>
        </div>
    </div>
</section>

<section class="content-wrap intro-wrap">
    <div class="container">
        <article class="post-content intro-article">
            <div class="post-body intro-body">
                <?= (string) ($site['about_page_content'] ?? '') ?>
            </div>
        </article>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>