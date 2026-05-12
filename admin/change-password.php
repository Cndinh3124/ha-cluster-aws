<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/auth.php';
require_admin_login();

$msg = '';
$adminId = (int) ($_SESSION['admin_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = (string) ($_POST['current_password'] ?? '');
    $new = (string) ($_POST['new_password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');

    if ($new === '' || $new !== $confirm) {
        $msg = 'Mật khẩu mới trống hoặc không khớp.';
    } else {
        $stmt = $pdo->prepare('SELECT password_hash FROM admins WHERE id = ? LIMIT 1');
        $stmt->execute([$adminId]);
        $row = $stmt->fetch();
        if (!$row || !password_verify($current, $row['password_hash'])) {
            $msg = 'Mật khẩu hiện tại không đúng.';
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $upd = $pdo->prepare('UPDATE admins SET password_hash = ? WHERE id = ?');
            $upd->execute([$hash, $adminId]);
            $msg = 'Đổi mật khẩu thành công.';
        }
    }
}

$pageTitle = 'Đổi mật khẩu';
require __DIR__ . '/includes/header.php';
?>
<div class="page-heading">
    <a href="dashboard.php" class="btn btn-secondary">Quay lại</a>
</div>

<?php if ($msg): ?><div class="alert<?= $msg === 'Đổi mật khẩu thành công.' ? ' alert-success' : ' alert-error' ?>"><?= e($msg) ?></div><?php endif; ?>

<div class="card" style="max-width:520px;">
    <form method="post">
        <div class="form-group">
            <label>Mật khẩu hiện tại</label>
            <input type="password" name="current_password" required>
        </div>
        <div class="form-group">
            <label>Mật khẩu mới</label>
            <input type="password" name="new_password" required>
        </div>
        <div class="form-group">
            <label>Xác nhận mật khẩu mới</label>
            <input type="password" name="confirm_password" required>
        </div>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Đổi mật khẩu</button>
            <a href="dashboard.php" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/includes/footer.php';
