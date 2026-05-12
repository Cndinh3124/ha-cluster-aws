<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/auth.php';
require_admin_login();

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: posts.php');
    exit;
}

$sql = 'SELECT p.id, p.title, p.slug, p.status, p.published_at, c.name AS category_name
        FROM posts p
        LEFT JOIN categories c ON c.id = p.category_id
        ORDER BY p.id DESC';
$items = $pdo->query($sql)->fetchAll();

$pageTitle = 'Quản lý Bài viết';
require __DIR__ . '/includes/header.php';
?>

<div class="content-head" style="margin-top: -6px;">
    <a class="btn" href="post_form.php">Thêm bài viết</a>
</div>

<div class="table-wrap">
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Danh mục</th>
            <th>Trạng thái</th>
            <th>Ngày đăng</th>
            <th>Thao tác</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= (int) $item['id'] ?></td>
                <td><?= e($item['title']) ?></td>
                <td><?= e($item['category_name'] ?? 'Không có') ?></td>
                <td><?= e($item['status']) ?></td>
                <td><?= e(format_date($item['published_at'])) ?></td>
                <td class="actions">
                    <a class="btn" href="post_form.php?id=<?= (int) $item['id'] ?>">Sửa</a>
                    <a class="btn btn-danger" href="posts.php?delete=<?= (int) $item['id'] ?>" onclick="return confirm('Xóa bài viết này?')">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
