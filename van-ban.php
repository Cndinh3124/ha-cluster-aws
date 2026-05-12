<?php

declare(strict_types=1);

require __DIR__ . '/config/app.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$documents = get_latest_documents($pdo, 80);
$pageTitle = 'Văn bản';

require __DIR__ . '/includes/header.php';
?>

<section class="notice-banner">
    <div class="container notice-banner-inner">
        <div class="breadcrumb notice-breadcrumb">
            <a href="<?= e(base_url()) ?>">Trang chủ</a>
            <span>•</span>
            <span>Văn bản</span>
        </div>
        <h1>Văn bản</h1>
    </div>
</section>

<section class="content-wrap">
    <div class="container document-list-wrap">
        <?php if (!$documents): ?>
            <p>Chưa có văn bản nào.</p>
        <?php endif; ?>

        <?php foreach ($documents as $doc): ?>
            <?php $fileExt = strtoupper(pathinfo((string) ($doc['file_name'] ?? ''), PATHINFO_EXTENSION)); ?>
            <article class="document-item">
                <div class="document-icon" aria-hidden="true"><?= e($fileExt !== '' ? $fileExt : 'FILE') ?></div>
                <div class="document-body">
                    <h3><?= e((string) $doc['title']) ?></h3>
                    <div class="document-meta">
                        <span>Ngày ban hành: <?= e(format_date((string) $doc['issued_at'])) ?></span>
                        <span>Dung lượng: <?= e(format_file_size((int) ($doc['file_size'] ?? 0))) ?></span>
                    </div>
                    <?php if (!empty($doc['summary'])): ?>
                        <p><?= e((string) $doc['summary']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="document-actions">
                    <a href="<?= e((string) $doc['file_url']) ?>" target="_blank" rel="noopener">Xem/Tải file</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
