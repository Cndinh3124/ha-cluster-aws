<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

if (!is_admin_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['image'])) {
    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
    exit;
}

$file = $_FILES['image'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Upload thất bại (mã lỗi ' . $file['error'] . ').']);
    exit;
}

if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'Ảnh không được vượt quá 5MB.']);
    exit;
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

if (!in_array($ext, $allowed, true)) {
    echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận jpg, png, webp, gif.']);
    exit;
}

// Kiểm tra nội dung file thực sự là ảnh
$mime = function_exists('mime_content_type') ? mime_content_type($file['tmp_name']) : null;
if (!is_string($mime) || strpos($mime, 'image/') !== 0) {
    echo json_encode(['success' => false, 'message' => 'File không phải ảnh hợp lệ.']);
    exit;
}

$fileName = uniqid('content_', true) . '.' . $ext;
$target = UPLOAD_POST_DIR . $fileName;

if (!is_dir(UPLOAD_POST_DIR) && !mkdir(UPLOAD_POST_DIR, 0775, true) && !is_dir(UPLOAD_POST_DIR)) {
    echo json_encode(['success' => false, 'message' => 'Không thể tạo thư mục upload.']);
    exit;
}

if (!move_uploaded_file($file['tmp_name'], $target)) {
    echo json_encode(['success' => false, 'message' => 'Không thể lưu file.']);
    exit;
}

echo json_encode([
    'success' => true,
    'url' => str_replace('\\', '/', 'uploads/posts/' . $fileName),
]);
