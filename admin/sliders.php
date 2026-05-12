<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/auth.php';
require_admin_login();

$editId = (int) ($_GET['edit'] ?? 0);
$editItem = null;

if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM sliders WHERE id = ?');
    $stmt->execute([$editId]);
    $editItem = $stmt->fetch();
}

$formError = '';
$formSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim((string) ($_POST['title'] ?? ''));
    $subtitle = trim((string) ($_POST['subtitle'] ?? ''));
    $linkUrl = trim((string) ($_POST['link_url'] ?? ''));
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    $imageUrl = trim((string) ($_POST['image_url'] ?? ''));

    // Nếu edit và không upload ảnh mới, giữ ảnh cũ
    if ($editId > 0 && $editItem && $imageUrl === '') {
        $imageUrl = $editItem['image_url'];
    }

    if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $allowed, true)) {
            $fileName = uniqid('slide_', true) . '.' . $ext;
            $target = UPLOAD_SLIDE_DIR . $fileName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $imageUrl = base_url('uploads/slides/' . $fileName);
            }
        } else {
            $formError = 'Chỉ chấp nhận file jpg, png, webp.';
        }
    }

    if ($formError === '') {
        if (true) {
            if ($editId > 0) {
                $stmt = $pdo->prepare('UPDATE sliders SET title = ?, subtitle = ?, image_url = ?, link_url = ?, sort_order = ?, is_active = ? WHERE id = ?');
                $stmt->execute([$title, $subtitle, $imageUrl, $linkUrl, $sortOrder, $isActive, $editId]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO sliders (title, subtitle, image_url, link_url, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute([$title, $subtitle, $imageUrl, $linkUrl, $sortOrder, $isActive]);
            }
            header('Location: sliders.php?msg=' . ($editId > 0 ? 'updated' : 'created'));
            exit;
        }
    }
}

$msgs = ['created' => 'Thêm slider thành công!', 'updated' => 'Cập nhật slider thành công!', 'deleted' => 'Đã xóa slider.'];
$formSuccess = $msgs[(string) ($_GET['msg'] ?? '')] ?? '';

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM sliders WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: sliders.php?msg=deleted');
    exit;
}

$items = $pdo->query('SELECT * FROM sliders ORDER BY sort_order ASC, id DESC')->fetchAll();

$pageTitle = 'Quản lý Slider';
require __DIR__ . '/includes/header.php';
?>

<?php if ($formSuccess !== ''): ?>
    <div class="alert"><?= e($formSuccess) ?></div>
<?php endif; ?>
<?php if ($formError !== ''): ?>
    <div class="alert alert-error"><?= e($formError) ?></div>
<?php endif; ?>

<div class="card">
    <h3 style="margin:0 0 14px;font-size:16px;color:#2e4168;"><?= $editId > 0 ? '✏️ Chỉnh sửa slider' : '➕ Thêm slider mới' ?></h3>
    <form method="post" enctype="multipart/form-data">
        <div class="form-row" style="grid-template-columns: 2fr 1fr;">
            <div class="form-group">
                <label>Tiêu đề <span class="req">*</span></label>
                <input type="text" name="title" placeholder="Ví dụ: Chiến dịch Xuân Tình Nguyện 2026" value="<?= $editItem ? e($editItem['title']) : '' ?>">
            </div>
            <div class="form-group">
                <label>Mô tả ngắn</label>
                <input type="text" name="subtitle" placeholder="Phụ đề hiển thị trên banner" value="<?= $editItem ? e($editItem['subtitle']) : '' ?>">
            </div>
        </div>
        <div class="form-row" style="grid-template-columns: 2fr 1fr 1fr;">
            <div class="form-group">
                <label>Link khi bấm vào banner (tùy chọn)</label>
                <input type="text" name="link_url" placeholder="https://..." value="<?= $editItem ? e($editItem['link_url']) : '' ?>">
            </div>
            <div class="form-group">
                <label>Thứ tự hiển thị</label>
                <input type="number" name="sort_order" value="<?= $editItem ? (int) $editItem['sort_order'] : '0' ?>">
            </div>
            <div class="form-group">
                <label style="visibility:hidden;">x</label>
                <label style="display:flex;align-items:center;gap:8px;padding:9px 0;">
                    <input style="width:auto;" type="checkbox" name="is_active" <?= !$editItem || (int) $editItem['is_active'] === 1 ? 'checked' : '' ?>>
                    Hiển thị banner này
                </label>
            </div>
        </div>
        <div class="form-row" style="grid-template-columns: 1fr 1fr;">
            <div class="form-group">
                <label>Upload ảnh từ máy tính</label>
                <?php if ($editItem && !empty($editItem['image_url'])): ?>
                    <div class="thumb-preview">
                        <img src="<?= e($editItem['image_url']) ?>" alt="ảnh hiện tại">
                        <span>Ảnh hiện tại — upload mới để thay thế</span>
                    </div>
                <?php endif; ?>
                <input type="file" name="image" accept="image/*">
            </div>
            <div class="form-group">
                <label>Hoặc dán URL hình ảnh</label>
                <input type="text" name="image_url" placeholder="https://example.com/anh.jpg" value="<?= $editItem ? e($editItem['image_url']) : '' ?>">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $editId > 0 ? 'Cập nhật slider' : 'Thêm slider' ?></button>
            <?php if ($editId > 0): ?>
                <a href="sliders.php" class="btn btn-secondary">Hủy</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-wrap" style="margin-top:14px;">
    <table>
        <thead>
        <tr>
            <th>Hình</th>
            <th>Tiêu đề</th>
            <th>Mô tả</th>
            <th>Thứ tự</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><img src="<?= e($item['image_url']) ?>" alt="slide" style="width:160px;height:72px;object-fit:cover;border-radius:8px;"></td>
                <td><?= e($item['title']) ?></td>
                <td><?= e($item['subtitle']) ?></td>
                <td><?= (int) $item['sort_order'] ?></td>
                <td><?= (int) $item['is_active'] === 1 ? 'Hiển thị' : 'Ẩn' ?></td>
                <td>
                    <a class="btn btn-primary" href="sliders.php?edit=<?= (int) $item['id'] ?>">Sửa</a>
                    <a class="btn btn-danger" href="sliders.php?delete=<?= (int) $item['id'] ?>" onclick="return confirm('Xóa slider này?')">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
