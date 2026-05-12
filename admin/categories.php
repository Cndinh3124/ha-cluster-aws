<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/auth.php';
require_admin_login();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim((string) ($_POST['name'] ?? ''));
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);

    if ($name !== '') {
        $slug = slugify($name);

        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE categories SET name = ?, slug = ?, sort_order = ? WHERE id = ?');
            $stmt->execute([$name, $slug, $sortOrder, $id]);
            $message = 'Cập nhật danh mục thành công.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO categories (name, slug, sort_order) VALUES (?, ?, ?)');
            $stmt->execute([$name, $slug, $sortOrder]);
            $message = 'Thêm danh mục thành công.';
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: categories.php');
    exit;
}

$editItem = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $editItem = $stmt->fetch();
}

$items = $pdo->query('SELECT * FROM categories ORDER BY sort_order ASC, id DESC')->fetchAll();

$pageTitle = 'Quản lý Danh mục';
require __DIR__ . '/includes/header.php';
?>

<div class="card">
    <?php if ($message): ?>
        <div class="alert"><?= e($message) ?></div>
    <?php endif; ?>

    <form method="post" class="row">
        <input type="hidden" name="id" value="<?= e((string) ($editItem['id'] ?? 0)) ?>">
        <input type="text" name="name" value="<?= e($editItem['name'] ?? '') ?>" placeholder="Tên danh mục" required>
        <input type="number" name="sort_order" value="<?= e((string) ($editItem['sort_order'] ?? 0)) ?>" placeholder="Thứ tự">
        <button type="submit"><?= $editItem ? 'Cập nhật' : 'Thêm mới' ?></button>
    </form>
</div>

<div class="table-wrap" style="margin-top: 14px;">
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Tên danh mục</th>
            <th>Slug</th>
            <th>Thứ tự</th>
            <th>Thao tác</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= (int) $item['id'] ?></td>
                <td><?= e($item['name']) ?></td>
                <td><?= e($item['slug']) ?></td>
                <td><?= (int) $item['sort_order'] ?></td>
                <td class="actions">
                    <a class="btn" href="categories.php?edit=<?= (int) $item['id'] ?>">Sửa</a>
                    <a class="btn btn-danger" href="categories.php?delete=<?= (int) $item['id'] ?>" onclick="return confirm('Xóa danh mục này?')">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
