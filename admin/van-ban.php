<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/auth.php';
require_admin_login();

ensure_documents_table($pdo);

$pageTitle = 'Quản lý Văn bản';
$action = (string) ($_GET['action'] ?? 'list');
$msg = '';

function unique_document_slug(PDO $pdo, string $baseSlug, int $excludeId = 0): string
{
    $slug = $baseSlug;
    $index = 1;

    while (true) {
        if ($excludeId > 0) {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM documents WHERE slug = ? AND id <> ?');
            $stmt->execute([$slug, $excludeId]);
        } else {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM documents WHERE slug = ?');
            $stmt->execute([$slug]);
        }

        if ((int) $stmt->fetchColumn() === 0) {
            return $slug;
        }

        $slug = $baseSlug . '-' . $index;
        $index++;
    }
}

function remove_document_file(string $fileUrl): void
{
    $fileName = basename((string) parse_url($fileUrl, PHP_URL_PATH));
    if ($fileName === '') {
        return;
    }

    $fullPath = rtrim(UPLOAD_DOCUMENT_DIR, '/\\') . DIRECTORY_SEPARATOR . $fileName;
    if (is_file($fullPath)) {
        @unlink($fullPath);
    }
}

if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $stmt = $pdo->prepare('SELECT file_url FROM documents WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $found = $stmt->fetch();

    if ($found) {
        remove_document_file((string) ($found['file_url'] ?? ''));
        $deleteStmt = $pdo->prepare('DELETE FROM documents WHERE id = ?');
        $deleteStmt->execute([$id]);
    }

    header('Location: van-ban.php?msg=deleted');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $title = trim((string) ($_POST['title'] ?? ''));
    $slugInput = trim((string) ($_POST['slug'] ?? ''));
    $summary = trim((string) ($_POST['summary'] ?? ''));
    $status = (string) ($_POST['status'] ?? 'draft');
    $issuedAt = trim((string) ($_POST['issued_at'] ?? date('Y-m-d')));
    $fileUrl = trim((string) ($_POST['file_current'] ?? ''));
    $fileName = trim((string) ($_POST['file_name_current'] ?? ''));
    $fileSize = (int) ($_POST['file_size_current'] ?? 0);

    $allowedStatus = ['draft', 'published'];
    if (!in_array($status, $allowedStatus, true)) {
        $status = 'draft';
    }

    if ($issuedAt === '') {
        $issuedAt = date('Y-m-d');
    }

    $baseSlug = slugify($slugInput !== '' ? $slugInput : $title);
    if ($baseSlug === '') {
        $baseSlug = 'van-ban-' . date('YmdHis');
    }
    $slug = unique_document_slug($pdo, $baseSlug, $id);

    if (isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
        $extension = strtolower(pathinfo((string) $_FILES['file']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

        if (!in_array($extension, $allowedExtensions, true)) {
            $msg = 'Định dạng file không hợp lệ. Chỉ hỗ trợ PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX.';
        } elseif ((int) ($_FILES['file']['size'] ?? 0) > 20 * 1024 * 1024) {
            $msg = 'Kích thước file vượt quá 20MB.';
        } else {
            if (!is_dir(UPLOAD_DOCUMENT_DIR)) {
                mkdir(UPLOAD_DOCUMENT_DIR, 0755, true);
            }

            $newFileName = uniqid('doc_', true) . '.' . $extension;
            $targetPath = rtrim(UPLOAD_DOCUMENT_DIR, '/\\') . DIRECTORY_SEPARATOR . $newFileName;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
                if ($id > 0 && $fileUrl !== '') {
                    remove_document_file($fileUrl);
                }
                $fileUrl = base_url('uploads/documents/' . $newFileName);
                $fileName = (string) $_FILES['file']['name'];
                $fileSize = (int) ($_FILES['file']['size'] ?? 0);
            } else {
                $msg = 'Không thể tải file lên máy chủ.';
            }
        }
    }

    if ($title === '') {
        $msg = 'Tiêu đề không được để trống.';
    }

    if ($msg === '' && $fileUrl === '') {
        $msg = 'Vui lòng chọn file văn bản để tải lên.';
    }

    if ($msg === '') {
        if ($id > 0) {
            $stmt = $pdo->prepare(
                'UPDATE documents
                 SET title = ?, slug = ?, summary = ?, file_url = ?, file_name = ?, file_size = ?, status = ?, issued_at = ?
                 WHERE id = ?'
            );
            $stmt->execute([$title, $slug, $summary, $fileUrl, $fileName, $fileSize, $status, $issuedAt, $id]);
            header('Location: van-ban.php?msg=updated');
            exit;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO documents (title, slug, summary, file_url, file_name, file_size, status, issued_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$title, $slug, $summary, $fileUrl, $fileName, $fileSize, $status, $issuedAt]);
        header('Location: van-ban.php?msg=created');
        exit;
    }

    $action = $id > 0 ? 'edit' : 'add';
    $_GET['id'] = (string) $id;
}

if (isset($_GET['msg'])) {
    $messages = [
        'created' => 'Tạo văn bản thành công!',
        'updated' => 'Cập nhật văn bản thành công!',
        'deleted' => 'Đã xóa văn bản.',
    ];
    $msg = $messages[(string) $_GET['msg']] ?? $msg;
}

if ($action === 'add' || $action === 'edit') {
    $item = [
        'id' => 0,
        'title' => '',
        'slug' => '',
        'summary' => '',
        'file_url' => '',
        'file_name' => '',
        'file_size' => 0,
        'status' => 'draft',
        'issued_at' => date('Y-m-d'),
    ];

    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT * FROM documents WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $_GET['id']]);
        $found = $stmt->fetch();
        if (!$found) {
            header('Location: van-ban.php');
            exit;
        }
        $item = $found;
    }

    require __DIR__ . '/includes/header.php';
    ?>
    <div class="page-heading">
        <a href="van-ban.php" class="btn btn-secondary">Quay lại</a>
    </div>

    <?php if ($msg !== ''): ?>
        <div class="alert alert-error"><?= e($msg) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= (int) ($item['id'] ?? 0) ?>">
            <input type="hidden" name="file_current" value="<?= e((string) ($item['file_url'] ?? '')) ?>">
            <input type="hidden" name="file_name_current" value="<?= e((string) ($item['file_name'] ?? '')) ?>">
            <input type="hidden" name="file_size_current" value="<?= (int) ($item['file_size'] ?? 0) ?>">

            <div class="form-row form-row-3">
                <div class="form-group flex-2">
                    <label>Tiêu đề <span class="req">*</span></label>
                    <input type="text" name="title" value="<?= e((string) ($item['title'] ?? '')) ?>" required>
                </div>
                <div class="form-group flex-1">
                    <label>Slug</label>
                    <input type="text" name="slug" value="<?= e((string) ($item['slug'] ?? '')) ?>" placeholder="Tự tạo nếu để trống">
                </div>
                <div class="form-group flex-1">
                    <label>Ngày ban hành</label>
                    <input type="date" name="issued_at" value="<?= e(date('Y-m-d', strtotime((string) ($item['issued_at'] ?? date('Y-m-d'))))) ?>">
                </div>
            </div>

            <div class="form-row form-row-2">
                <div class="form-group flex-1">
                    <label>File văn bản <span class="req">*</span></label>
                    <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation">
                    <small>Hỗ trợ PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX. Tối đa 20MB.</small>
                </div>
                <div class="form-group flex-1">
                    <label>Trạng thái</label>
                    <select name="status">
                        <option value="draft" <?= (string) ($item['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Nháp</option>
                        <option value="published" <?= (string) ($item['status'] ?? '') === 'published' ? 'selected' : '' ?>>Xuất bản</option>
                    </select>
                </div>
            </div>

            <?php if (!empty($item['file_url'])): ?>
                <div class="thumb-preview" style="align-items:flex-start;">
                    <div>
                        <div style="font-weight:700; margin-bottom:4px;">File hiện tại</div>
                        <a href="<?= e((string) $item['file_url']) ?>" target="_blank"><?= e((string) ($item['file_name'] ?? 'Tải xuống')) ?></a>
                        <div style="margin-top:4px; color:var(--muted); font-size:13px;">Dung lượng: <?= e(format_file_size((int) ($item['file_size'] ?? 0))) ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Mô tả ngắn</label>
                <textarea name="summary" rows="4"><?= e((string) ($item['summary'] ?? '')) ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $action === 'edit' ? 'Cập nhật' : 'Tạo mới' ?></button>
                <a href="van-ban.php" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;

$q = trim((string) ($_GET['q'] ?? ''));
$fStatus = trim((string) ($_GET['status'] ?? ''));
$allowedStatus = ['draft', 'published'];

$where = ['1=1'];
$params = [];

if ($q !== '') {
    $where[] = '(title LIKE ? OR slug LIKE ? OR summary LIKE ? OR file_name LIKE ?)';
    $like = '%' . $q . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if (in_array($fStatus, $allowedStatus, true)) {
    $where[] = 'status = ?';
    $params[] = $fStatus;
}

$whereSql = implode(' AND ', $where);

$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM documents WHERE $whereSql");
$totalStmt->execute($params);
$total = (int) $totalStmt->fetchColumn();
$totalPages = max(1, (int) ceil($total / $limit));

$listStmt = $pdo->prepare(
    "SELECT *
     FROM documents
     WHERE $whereSql
     ORDER BY issued_at DESC, id DESC
     LIMIT ? OFFSET ?"
);

$bindIndex = 1;
foreach ($params as $value) {
    $listStmt->bindValue($bindIndex, $value);
    $bindIndex++;
}
$listStmt->bindValue($bindIndex, $limit, PDO::PARAM_INT);
$listStmt->bindValue($bindIndex + 1, $offset, PDO::PARAM_INT);
$listStmt->execute();
$items = $listStmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="page-heading">
    <a href="van-ban.php?action=add" class="btn btn-primary">Thêm văn bản</a>
</div>

<?php if ($msg !== ''): ?>
    <div class="alert"><?= e($msg) ?></div>
<?php endif; ?>

<div class="card">
    <form method="get" class="filter-panel">
        <div class="filter-grid" style="grid-template-columns:2fr 1fr auto;">
            <input type="text" name="q" placeholder="Tìm tiêu đề, tên file, mô tả..." value="<?= e($q) ?>">
            <select name="status">
                <option value="">-- Tất cả trạng thái --</option>
                <?php foreach ($allowedStatus as $status): ?>
                    <option value="<?= e($status) ?>" <?= $fStatus === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">Lọc</button>
        </div>
    </form>

    <div class="table-wrap" style="padding:0; box-shadow:none; margin-top:10px;">
        <table class="admin-table">
            <thead>
            <tr>
                <th width="50">#</th>
                <th>Tiêu đề</th>
                <th>Tập tin</th>
                <th>Ngày ban hành</th>
                <th>Trạng thái</th>
                <th width="140">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$items): ?>
                <tr>
                    <td colspan="6" class="text-center">Chưa có văn bản nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($items as $idx => $item): ?>
                    <tr>
                        <td><?= $offset + $idx + 1 ?></td>
                        <td>
                            <a href="van-ban.php?action=edit&id=<?= (int) $item['id'] ?>"><?= e((string) $item['title']) ?></a>
                            <?php if (!empty($item['summary'])): ?>
                                <div style="margin-top:4px; color:var(--muted); font-size:13px;"><?= e((string) $item['summary']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= e((string) $item['file_url']) ?>" target="_blank"><?= e((string) ($item['file_name'] ?? 'Tải xuống')) ?></a>
                            <div style="margin-top:4px; color:var(--muted); font-size:13px;"><?= e(format_file_size((int) ($item['file_size'] ?? 0))) ?></div>
                        </td>
                        <td><?= e(format_date((string) $item['issued_at'])) ?></td>
                        <td><span class="badge badge-<?= e((string) $item['status']) ?>"><?= e((string) $item['status']) ?></span></td>
                        <td class="actions">
                            <a class="btn btn-primary" href="van-ban.php?action=edit&id=<?= (int) $item['id'] ?>">Sửa</a>
                            <a class="btn btn-danger" href="van-ban.php?action=delete&id=<?= (int) $item['id'] ?>" onclick="return confirm('Xóa văn bản này?')">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php $query = $_GET; $query['page'] = $p; ?>
                <a href="?<?= e(http_build_query($query)) ?>" class="<?= $p === $page ? 'active' : '' ?>">
                    <?= $p ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
