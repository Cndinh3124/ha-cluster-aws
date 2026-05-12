<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/auth.php';
require_admin_login();

$id = (int) ($_GET['id'] ?? 0);
$item = [
    'category_id' => '',
    'title' => '',
    'slug' => '',
    'excerpt' => '',
    'content' => '',
    'thumbnail' => '',
    'status' => 'published',
    'published_at' => date('Y-m-d'),
];

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $dbItem = $stmt->fetch();
    if ($dbItem) {
        $item = $dbItem;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $title = trim((string) ($_POST['title'] ?? ''));
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $excerpt = trim((string) ($_POST['excerpt'] ?? ''));
    $content = trim((string) ($_POST['content'] ?? ''));
    $status = (string) ($_POST['status'] ?? 'draft');
    $publishedAt = (string) ($_POST['published_at'] ?? date('Y-m-d'));
    $slug = slugify($title);

    $thumbnail = (string) ($_POST['thumbnail_current'] ?? '');
    if (isset($_FILES['thumbnail']) && is_uploaded_file($_FILES['thumbnail']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $allowed, true)) {
            $fileName = uniqid('post_', true) . '.' . $ext;
            $target = UPLOAD_POST_DIR . $fileName;
            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target)) {
                $thumbnail = base_url('uploads/posts/' . $fileName);
            }
        }
    }

    if ($id > 0) {
        $stmt = $pdo->prepare(
            'UPDATE posts
             SET category_id = ?, title = ?, slug = ?, excerpt = ?, content = ?, thumbnail = ?, status = ?, published_at = ?
             WHERE id = ?'
        );
        $stmt->execute([$categoryId, $title, $slug, $excerpt, $content, $thumbnail, $status, $publishedAt, $id]);
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO posts (category_id, title, slug, excerpt, content, thumbnail, status, published_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$categoryId, $title, $slug, $excerpt, $content, $thumbnail, $status, $publishedAt]);
    }

    header('Location: posts.php');
    exit;
}

$categories = $pdo->query('SELECT id, name FROM categories ORDER BY sort_order ASC, id DESC')->fetchAll();

$pageTitle = $id > 0 ? 'Sửa Bài viết' : 'Thêm Bài viết';
require __DIR__ . '/includes/header.php';
?>

<div class="card">
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= (int) ($item['id'] ?? 0) ?>">
        <input type="hidden" name="thumbnail_current" value="<?= e($item['thumbnail'] ?? '') ?>">

        <label>Tiêu đề</label>
        <input type="text" name="title" required value="<?= e($item['title'] ?? '') ?>">

        <label>Danh mục</label>
        <select name="category_id" required>
            <option value="">-- Chọn danh mục --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= (int) $cat['id'] ?>" <?= (int) ($item['category_id'] ?? 0) === (int) $cat['id'] ? 'selected' : '' ?>>
                    <?= e($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Tóm tắt</label>
        <textarea name="excerpt"><?= e($item['excerpt'] ?? '') ?></textarea>

        <label>Nội dung (có thể dùng HTML)</label>
        <textarea name="content"><?= e($item['content'] ?? '') ?></textarea>

        <label>Hình đại diện</label>
        <input type="file" name="thumbnail" accept="image/*">

        <?php if (!empty($item['thumbnail'])): ?>
            <p><img src="<?= e($item['thumbnail']) ?>" alt="thumb" style="max-width:200px;border-radius:8px;"></p>
        <?php endif; ?>

        <label>Trạng thái</label>
        <select name="status">
            <option value="published" <?= ($item['status'] ?? '') === 'published' ? 'selected' : '' ?>>Đã xuất bản</option>
            <option value="draft" <?= ($item['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Nháp</option>
        </select>

        <label>Ngày đăng</label>
        <input type="date" name="published_at" value="<?= e(date('Y-m-d', strtotime((string) ($item['published_at'] ?? 'now')))) ?>">

        <button type="submit" style="margin-top: 12px;">Lưu bài viết</button>
    </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
