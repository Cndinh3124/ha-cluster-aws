<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/auth.php';

if (admin_is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $stmt = $pdo->prepare('SELECT id, full_name, password_hash FROM admins WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = (int) $admin['id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Sai thông tin đăng nhập.';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Admin</title>
    <link rel="stylesheet" href="<?= e(base_url('assets/css/admin.css')) ?>">
</head>
<body>
<div class="login-wrap">
    <form class="login-card" method="post">
        <h1>Đăng nhập Admin</h1>
        <?php if ($error): ?>
            <p style="color:#c23543;"><?= e($error) ?></p>
        <?php endif; ?>
        <label>Tên đăng nhập</label>
        <input type="text" name="username" required>
        <label>Mật khẩu</label>
        <input type="password" name="password" required>
        <button type="submit" style="margin-top:14px;">Đăng nhập</button>
    </form>
</div>
</body>
</html>
