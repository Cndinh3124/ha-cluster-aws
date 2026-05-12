<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/auth.php';
require_admin_login();

$pageTitle = 'Quản lý Tin tức';
$action = (string) ($_GET['action'] ?? 'list');
$msg = '';

function unique_post_slug(PDO $pdo, string $baseSlug, int $excludeId = 0): string
{
    $slug = $baseSlug;
    $i = 1;

    while (true) {
        if ($excludeId > 0) {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM posts WHERE slug = ? AND id <> ?');
            $stmt->execute([$slug, $excludeId]);
        } else {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM posts WHERE slug = ?');
            $stmt->execute([$slug]);
        }

        if ((int) $stmt->fetchColumn() === 0) {
            return $slug;
        }

        $slug = $baseSlug . '-' . $i;
        $i++;
    }
}

if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: tin-tuc.php?msg=deleted');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $title = trim((string) ($_POST['title'] ?? ''));
    $slugInput = trim((string) ($_POST['slug'] ?? ''));
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $excerpt = trim((string) ($_POST['excerpt'] ?? ''));
    $content = trim((string) ($_POST['content'] ?? ''));
    $status = (string) ($_POST['status'] ?? 'draft');
    $publishedAt = (string) ($_POST['published_at'] ?? date('Y-m-d'));

    $baseSlug = slugify($slugInput !== '' ? $slugInput : $title);
    if ($baseSlug === '') {
        $baseSlug = 'bai-viet-' . date('YmdHis');
    }
    $slug = unique_post_slug($pdo, $baseSlug, $id);

    $thumbnail = trim((string) ($_POST['thumbnail_current'] ?? ''));
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

    if ($title === '') {
        $msg = 'Tiêu đề không được để trống.';
        $action = $id > 0 ? 'edit' : 'add';
        $_GET['id'] = (string) $id;
    } else {
        $status = in_array($status, ['draft', 'published'], true) ? $status : 'draft';
        $categoryId = $categoryId > 0 ? $categoryId : null;

        if ($id > 0) {
            $stmt = $pdo->prepare(
                'UPDATE posts
                 SET category_id = ?, title = ?, slug = ?, excerpt = ?, content = ?, thumbnail = ?, status = ?, published_at = ?
                 WHERE id = ?'
            );
            $stmt->execute([$categoryId, $title, $slug, $excerpt, $content, $thumbnail, $status, $publishedAt, $id]);
            header('Location: tin-tuc.php?msg=updated');
            exit;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO posts (category_id, title, slug, excerpt, content, thumbnail, status, published_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$categoryId, $title, $slug, $excerpt, $content, $thumbnail, $status, $publishedAt]);
        header('Location: tin-tuc.php?msg=created');
        exit;
    }
}

if (isset($_GET['msg'])) {
    $messages = [
        'created' => 'Tạo bài viết thành công!',
        'updated' => 'Cập nhật bài viết thành công!',
        'deleted' => 'Đã xóa bài viết.',
    ];
    $msg = $messages[(string) $_GET['msg']] ?? $msg;
}

if ($action === 'add' || $action === 'edit') {
    $item = [
        'id' => 0,
        'category_id' => '',
        'title' => '',
        'slug' => '',
        'excerpt' => '',
        'content' => '',
        'thumbnail' => '',
        'status' => 'draft',
        'published_at' => date('Y-m-d'),
    ];

    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $_GET['id']]);
        $found = $stmt->fetch();
        if (!$found) {
            header('Location: tin-tuc.php');
            exit;
        }
        $item = $found;
    }

    $categories = $pdo->query('SELECT id, name FROM categories ORDER BY sort_order ASC, id DESC')->fetchAll();

    $extraHead = '
<link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0/image-resize.min.css">
<style>
#quill-editor { min-height: 320px; font-size: 16px; }
.ql-toolbar.ql-snow { border-radius: 10px 10px 0 0; border-color: #c8d6f0; background: #f6f9ff; }
.ql-container.ql-snow { border-radius: 0 0 10px 10px; border-color: #c8d6f0; }
.ql-editor { min-height: 280px; }
</style>';

    require __DIR__ . '/includes/header.php';
    ?>
    <div class="page-heading">
        <h1><?= $action === 'edit' ? 'Sửa bài viết' : 'Thêm bài viết' ?></h1>
        <a href="tin-tuc.php" class="btn btn-secondary">Quay lại</a>
    </div>

    <?php if ($msg !== ''): ?>
        <div class="alert alert-error"><?= e($msg) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="post" action="tin-tuc.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= (int) ($item['id'] ?? 0) ?>">
            <input type="hidden" name="thumbnail_current" value="<?= e((string) ($item['thumbnail'] ?? '')) ?>">

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
                    <label>Ngày đăng</label>
                    <input type="date" name="published_at" value="<?= e(date('Y-m-d', strtotime((string) ($item['published_at'] ?? date('Y-m-d'))))) ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Ảnh thumbnail</label>
                <?php if (!empty($item['thumbnail'])): ?>
                    <div class="thumb-preview">
                        <img src="<?= e((string) $item['thumbnail']) ?>" alt="thumbnail">
                        <div>Ảnh hiện tại. Upload ảnh mới để thay thế.</div>
                    </div>
                <?php endif; ?>
                <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp">
            </div>

            <div class="form-row form-row-2">
                <div class="form-group flex-1">
                    <label>Danh mục</label>
                    <select name="category_id">
                        <option value="">-- Chọn --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int) $cat['id'] ?>" <?= (int) ($item['category_id'] ?? 0) === (int) $cat['id'] ? 'selected' : '' ?>>
                                <?= e((string) $cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label>Trạng thái</label>
                    <select name="status">
                        <option value="draft" <?= (string) ($item['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Nháp</option>
                        <option value="published" <?= (string) ($item['status'] ?? '') === 'published' ? 'selected' : '' ?>>Đã đăng</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Tóm tắt</label>
                <textarea name="excerpt" rows="3"><?= e((string) ($item['excerpt'] ?? '')) ?></textarea>
            </div>

            <div class="form-group">
                <label>Nội dung</label>
                <div id="quill-editor-wrap">
                    <div id="quill-editor"></div>
                </div>
                <script id="news-content-data" type="application/json"><?= json_encode((string) ($item['content'] ?? ''), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
                <textarea name="content" id="content" style="display:none;"><?= (string) ($item['content'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $action === 'edit' ? 'Cập nhật' : 'Tạo mới' ?></button>
                <a href="tin-tuc.php" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var baseUrl = <?= json_encode(base_url()) ?>;
        var editorWrap = document.getElementById('quill-editor-wrap');
        var contentField = document.getElementById('content');

        function imageHandler() {
            var editor = (this && this.quill) ? this.quill : window.quill;
            if (!editor) {
                alert('Editor chưa sẵn sàng, vui lòng thử lại.');
                return;
            }

            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();
            input.onchange = function () {
                var file = input.files[0];
                if (!file) return;
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ảnh không được vượt quá 5MB!');
                    return;
                }
                var formData = new FormData();
                formData.append('image', file);
                fetch('upload-image.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                    .then(function (res) {
                        if (!res.ok) {
                            return res.text().then(function (text) {
                                throw new Error(text || ('HTTP ' + res.status));
                            });
                        }
                        return res.json();
                    })
                    .then(function (data) {
                        if (data.success) {
                            var range = editor.getSelection(true);
                            var insertAt = range && typeof range.index === 'number' ? range.index : editor.getLength();
                            var imagePath = String(data.url || '').replace(/\\/g, '/').replace(/^\/+/, '');
                            editor.insertEmbed(insertAt, 'image', baseUrl + '/' + imagePath);
                            editor.setSelection(insertAt + 1);
                        } else {
                            alert(data.message || 'Upload thất bại!');
                        }
                    })
                    .catch(function (err) {
                        alert('Upload lỗi: ' + (err && err.message ? err.message : 'Không rõ nguyên nhân.'));
                    });
            };
        }

        if (typeof Quill === 'undefined') {
            if (editorWrap) editorWrap.style.display = 'none';
            if (contentField) contentField.style.display = 'block';
            return;
        }

        try {
            var quillModules = {
                toolbar: {
                    container: [
                        [{ header: [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        [{ align: [] }],
                        ['link', 'image', 'video'],
                        ['blockquote', 'code-block'],
                        [{ color: [] }, { background: [] }],
                        ['clean']
                    ],
                    handlers: { image: imageHandler }
                }
            };

            window.quill = new Quill('#quill-editor', {
                theme: 'snow',
                placeholder: 'Nhập nội dung chi tiết của bài viết...',
                modules: quillModules
            });
        } catch (err) {
            if (editorWrap) editorWrap.style.display = 'none';
            if (contentField) contentField.style.display = 'block';
            return;
        }

        var contentPayload = document.getElementById('news-content-data');
        var oldContent = document.getElementById('content').value || '';
        if (!oldContent && contentPayload) {
            try {
                oldContent = String(JSON.parse(contentPayload.textContent || '""') || '');
            } catch (e) {
                oldContent = '';
            }
        }
        oldContent = oldContent.trim();
        if (oldContent) {
            window.quill.root.innerHTML = oldContent;
        }

        var form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                var html = window.quill.root.innerHTML.trim();
                if (!html || html === '<p><br></p>') {
                    e.preventDefault();
                    alert('Vui lòng nhập nội dung bài viết!');
                    return false;
                }
                document.getElementById('content').value = html;
            });
        }
    });
    </script>

    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$q = trim((string) ($_GET['q'] ?? ''));
$fStatus = trim((string) ($_GET['status'] ?? ''));
$fCategoryId = (int) ($_GET['category_id'] ?? 0);
$fFrom = trim((string) ($_GET['from'] ?? ''));
$fTo = trim((string) ($_GET['to'] ?? ''));
$allowedStatus = ['draft', 'published'];

$categoriesFilter = $pdo->query('SELECT id, name FROM categories ORDER BY sort_order ASC, id DESC')->fetchAll();

$where = ['1=1'];
$params = [];

if ($q !== '') {
    $where[] = '(p.title LIKE ? OR p.slug LIKE ? OR p.excerpt LIKE ?)';
    $like = '%' . $q . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if (in_array($fStatus, $allowedStatus, true)) {
    $where[] = 'p.status = ?';
    $params[] = $fStatus;
}

if ($fCategoryId > 0) {
    $where[] = 'p.category_id = ?';
    $params[] = $fCategoryId;
}

if ($fFrom !== '') {
    $where[] = 'p.published_at >= ?';
    $params[] = $fFrom;
}

if ($fTo !== '') {
    $where[] = 'p.published_at <= ?';
    $params[] = $fTo;
}

$whereSql = implode(' AND ', $where);

$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM posts p WHERE $whereSql");
$totalStmt->execute($params);
$total = (int) $totalStmt->fetchColumn();
$totalPages = max(1, (int) ceil($total / $limit));

$listParams = $params;
$listParams[] = $limit;
$listParams[] = $offset;

$listStmt = $pdo->prepare(
    "SELECT p.*, c.name AS category_name
     FROM posts p
     LEFT JOIN categories c ON c.id = p.category_id
     WHERE $whereSql
     ORDER BY p.id DESC
     LIMIT ? OFFSET ?"
);

$paramIndex = 1;
foreach ($params as $value) {
    $listStmt->bindValue($paramIndex, $value);
    $paramIndex++;
}
$listStmt->bindValue($paramIndex, $limit, PDO::PARAM_INT);
$listStmt->bindValue($paramIndex + 1, $offset, PDO::PARAM_INT);
$listStmt->execute();
$items = $listStmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="page-heading">
    <div></div>
    <a href="tin-tuc.php?action=add" class="btn btn-primary">Thêm bài viết</a>
</div>

<?php if ($msg !== ''): ?>
    <div class="alert"><?= e($msg) ?></div>
<?php endif; ?>

<div class="card">
    <form method="get" class="filter-panel">
        <div class="filter-grid">
            <input type="text" name="q" placeholder="Tìm tiêu đề, slug, tóm tắt..." value="<?= e($q) ?>">
            <select name="status">
                <option value="">-- Tất cả trạng thái --</option>
                <?php foreach ($allowedStatus as $status): ?>
                    <option value="<?= e($status) ?>" <?= $fStatus === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="category_id">
                <option value="">-- Tất cả danh mục --</option>
                <?php foreach ($categoriesFilter as $cat): ?>
                    <option value="<?= (int) $cat['id'] ?>" <?= $fCategoryId === (int) $cat['id'] ? 'selected' : '' ?>>
                        <?= e((string) $cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="from" value="<?= e($fFrom) ?>">
            <input type="date" name="to" value="<?= e($fTo) ?>">
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">Lọc</button>
            <a href="tin-tuc.php" class="btn btn-secondary">Xóa lọc</a>
        </div>
    </form>

    <div class="table-wrap" style="padding:0; box-shadow:none; margin-top:10px;">
        <table class="admin-table">
            <thead>
            <tr>
                <th width="60">#</th>
                <th>Tiêu đề</th>
                <th>Danh mục</th>
                <th>Trạng thái</th>
                <th>Ngày đăng</th>
                <th width="140">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$items): ?>
                <tr>
                    <td colspan="6" class="text-center">Chưa có bài viết nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($items as $idx => $item): ?>
                    <tr>
                        <td><?= $offset + $idx + 1 ?></td>
                        <td><a href="tin-tuc.php?action=edit&id=<?= (int) $item['id'] ?>"><?= e((string) $item['title']) ?></a></td>
                        <td><?= e((string) ($item['category_name'] ?? '---')) ?></td>
                        <td>
                            <span class="badge badge-<?= e((string) $item['status']) ?>"><?= e((string) $item['status']) ?></span>
                        </td>
                        <td><?= e(format_date((string) $item['published_at'])) ?></td>
                        <td class="actions">
                            <a class="btn btn-primary" href="tin-tuc.php?action=edit&id=<?= (int) $item['id'] ?>">Sửa</a>
                            <a class="btn btn-danger" href="tin-tuc.php?action=delete&id=<?= (int) $item['id'] ?>" onclick="return confirm('Xóa bài viết này?')">Xóa</a>
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