<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/auth.php';
require_admin_login();

ensure_contacts_table($pdo);

$pageTitle = 'Quản lý Liên hệ';
 $action = (string) ($_GET['action'] ?? 'list');

// handle mark read/unread
if ($action === 'mark' && isset($_GET['id']) && isset($_GET['read'])) {
    $id = (int) $_GET['id'];
    $read = (int) $_GET['read'] === 1;
    mark_contact_read($pdo, $id, $read);
    header('Location: lien-he.php');
    exit;
}

if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    delete_contact($pdo, $id);
    header('Location: lien-he.php?msg=deleted');
    exit;
}

if ($action === 'view' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $contact = get_contact_by_id($pdo, $id);
    if ($contact) {
        if ((int) $contact['is_read'] === 0) {
            mark_contact_read($pdo, $id, true);
            $contact['is_read'] = 1;
        }
    } else {
        header('Location: lien-he.php');
        exit;
    }

    require __DIR__ . '/includes/header.php';
    ?>
    <div class="page-heading">
        <a href="lien-he.php" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="card">
        <div class="form-row">
            <div class="form-group">
                <label>Họ và tên</label>
                <div><?= e((string) $contact['name']) ?></div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <div><?= e((string) $contact['email']) ?></div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Điện thoại</label>
                <div><?= e((string) ($contact['phone'] ?? '')) ?></div>
            </div>
            <div class="form-group">
                <label>Chủ đề</label>
                <div><?= e((string) ($contact['subject'] ?? '')) ?></div>
            </div>
        </div>
        <div class="form-group">
            <label>Nội dung</label>
            <div style="white-space:pre-wrap;"><?= e((string) $contact['message']) ?></div>
        </div>
        <div class="form-actions">
            <?php if ((int) $contact['is_read'] === 1): ?>
                <a href="lien-he.php?action=mark&id=<?= (int) $contact['id'] ?>&read=0" class="btn btn-secondary">Đánh dấu chưa đọc</a>
            <?php else: ?>
                <a href="lien-he.php?action=mark&id=<?= (int) $contact['id'] ?>&read=1" class="btn btn-secondary">Đánh dấu đã đọc</a>
            <?php endif; ?>
            <a href="lien-he.php?action=delete&id=<?= (int) $contact['id'] ?>" class="btn btn-danger" onclick="return confirm('Xóa tin nhắn này?')">Xóa</a>
            <a href="lien-he.php" class="btn btn-primary">Quay lại danh sách</a>
        </div>
    </div>

    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

// list view
$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;
$total = get_contacts_count($pdo);
$totalPages = max(1, (int) ceil($total / $limit));
$items = get_contacts($pdo, $limit, $offset);

require __DIR__ . '/includes/header.php';
?>
<div class="page-heading">
    <a href="../lien-he.php" target="_blank" class="btn btn-primary">Xem trang liên hệ</a>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
    <div class="alert">Đã xóa tin nhắn.</div>
<?php endif; ?>

<div class="card">
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
            <tr>
                <th width="50">#</th>
                <th>Người gửi</th>
                <th>Email / ĐT</th>
                <th>Chủ đề</th>
                <th>Ngày</th>
                <th width="160">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$items): ?>
                <tr><td colspan="6" class="text-center">Chưa có tin nhắn.</td></tr>
            <?php else: ?>
                <?php foreach ($items as $idx => $item): ?>
                    <tr>
                        <td><?= $offset + $idx + 1 ?></td>
                        <td>
                            <a href="lien-he.php?action=view&id=<?= (int) $item['id'] ?>" class="<?= (int) $item['is_read'] === 0 ? 'font-weight-bold' : '' ?>"><?= e((string) $item['name']) ?></a>
                        </td>
                        <td><?= e((string) $item['email']) ?><?= $item['phone'] ? ' / ' . e((string) $item['phone']) : '' ?></td>
                        <td><?= e((string) ($item['subject'] ?? '')) ?></td>
                        <td><?= e(format_date((string) $item['created_at'])) ?></td>
                        <td class="actions">
                            <a class="btn btn-primary" href="lien-he.php?action=view&id=<?= (int) $item['id'] ?>">Xem</a>
                            <?php if ((int) $item['is_read'] === 1): ?>
                                <a class="btn btn-secondary" href="lien-he.php?action=mark&id=<?= (int) $item['id'] ?>&read=0">Chưa đọc</a>
                            <?php else: ?>
                                <a class="btn btn-secondary" href="lien-he.php?action=mark&id=<?= (int) $item['id'] ?>&read=1">Đã đọc</a>
                            <?php endif; ?>
                            <a class="btn btn-danger" href="lien-he.php?action=delete&id=<?= (int) $item['id'] ?>" onclick="return confirm('Xóa tin nhắn này?')">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($p = 1; $p <= $totalPages; $p++): $q = $_GET; $q['page'] = $p; ?>
                <a href="?<?= e(http_build_query($q)) ?>" class="<?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php';
