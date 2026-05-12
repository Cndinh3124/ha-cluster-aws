<?php

declare(strict_types=1);

require __DIR__ . '/config/app.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$pageTitle = 'Liên hệ';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $subject = trim((string) ($_POST['subject'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));

    if ($name === '' || $email === '' || $message === '') {
        $msg = 'Vui lòng điền tên, email và nội dung.';
    } else {
        try {
            save_contact($pdo, $name, $email, $phone === '' ? null : $phone, $subject === '' ? null : $subject, $message);
            // Redirect to avoid duplicate POST on reload (PRG pattern)
            header('Location: lien-he.php?msg=sent');
            exit;
        } catch (Throwable $e) {
            $msg = 'Không thể gửi tin nhắn vào lúc này.';
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<div class="container">
    <div class="page-heading">
        <h1>Liên hệ</h1>
    </div>

    <?php if ($msg !== ''): ?>
        <div class="alert"><?= e($msg) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="post">
        <div class="form-row form-row-2">
            <div class="form-group">
                <label>Họ và tên <span class="req">*</span></label>
                <input type="text" name="name" value="<?= e($name ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Email <span class="req">*</span></label>
                <input type="email" name="email" value="<?= e($email ?? '') ?>" required>
            </div>
        </div>

        <div class="form-row form-row-2">
            <div class="form-group">
                <label>Điện thoại</label>
                <input type="text" name="phone" value="<?= e($phone ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Chủ đề</label>
                <input type="text" name="subject" value="<?= e($subject ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Nội dung <span class="req">*</span></label>
            <textarea name="message" rows="6" required><?= e($message ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Gửi liên hệ</button>
        </div>
    </form>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php';
