<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/auth.php';
require_admin_login();

$pdo->exec(
    'CREATE TABLE IF NOT EXISTS site_settings (
        setting_key VARCHAR(100) PRIMARY KEY,
        setting_value TEXT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'school_name', 'school_subtitle', 'about_title', 'about_subtitle', 'about_page_content',
        'footer_title_left', 'footer_address', 'footer_phone', 'footer_email',
        'footer_title_right', 'footer_hotline_1', 'footer_hotline_2', 'footer_hotline_3', 'footer_copyright'
    ];

    $settings = [];
    foreach ($fields as $field) {
        $settings[$field] = trim((string) ($_POST[$field] ?? ''));
    }

    $logoUrl = trim((string) ($_POST['logo_image_url'] ?? ''));
    if (isset($_FILES['logo_image']) && is_uploaded_file($_FILES['logo_image']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['logo_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        if (in_array($ext, $allowed, true)) {
            $fileName = uniqid('logo_', true) . '.' . $ext;
            $target = dirname(__DIR__) . '/uploads/' . $fileName;
            if (move_uploaded_file($_FILES['logo_image']['tmp_name'], $target)) {
                $logoUrl = base_url('uploads/' . $fileName);
            }
        }
    }
    $settings['logo_image_url'] = $logoUrl;

    $stmt = $pdo->prepare(
        'INSERT INTO site_settings (setting_key, setting_value)
         VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );

    foreach ($settings as $key => $value) {
        $stmt->execute([$key, $value]);
    }

    $message = 'Đã cập nhật cấu hình website.';
}

$current = get_site_settings($pdo);

$pageTitle = 'Cài đặt website';
$extraHead = '
<link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css">
<style>
.about-editor-wrap { margin-bottom: 12px; }
#about-quill-editor { font-size: 16px; }
.about-editor-wrap .ql-toolbar.ql-snow {
    border-radius: 10px 10px 0 0;
    border-color: #c8d6f0;
    background: #f6f9ff;
}
.about-editor-wrap .ql-container.ql-snow {
    border-radius: 0 0 10px 10px;
    border-color: #c8d6f0;
    height: 420px;
}
.about-editor-wrap .ql-editor {
    min-height: 420px;
    max-height: 420px;
    overflow-y: auto;
}
.about-editor-wrap .ql-editor ul,
.about-editor-wrap .ql-editor ol {
    padding-left: 1.5em;
}
.about-editor-wrap .ql-editor ul {
    list-style-type: disc;
}
.about-editor-wrap .ql-editor ol {
    list-style-type: decimal;
}
</style>';
require __DIR__ . '/includes/header.php';
?>

<div class="card">
    <?php if ($message): ?>
        <div class="alert"><?= e($message) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="form-grid-2">
        <div>
            <label>Tên trường</label>
            <input type="text" name="school_name" value="<?= e($current['school_name']) ?>">
        </div>
        <div>
            <label>Mô tả ngắn header</label>
            <input type="text" name="school_subtitle" value="<?= e($current['school_subtitle']) ?>">
        </div>

        <div>
            <label>Logo URL</label>
            <input type="url" name="logo_image_url" value="<?= e($current['logo_image_url']) ?>" placeholder="https://...">
        </div>
        <div>
            <label>Upload logo</label>
            <input type="file" name="logo_image" accept="image/*">
        </div>

        <?php if (!empty($current['logo_image_url'])): ?>
            <div class="full">
                <img class="preview-logo" src="<?= e($current['logo_image_url']) ?>" alt="Logo hiện tại">
            </div>
        <?php endif; ?>

        <div class="full" style="height:1px;background:#dce7f9;margin:4px 0 2px;"></div>

        <div>
            <label>Tiêu đề trang Giới thiệu</label>
            <input type="text" name="about_title" value="<?= e($current['about_title']) ?>">
        </div>
        <div>
            <label>Dòng phụ trang Giới thiệu</label>
            <input type="text" name="about_subtitle" value="<?= e($current['about_subtitle']) ?>">
        </div>

        <div class="full">
            <label>Nội dung trang Giới thiệu</label>
            <div class="about-editor-wrap" id="about-editor-wrap">
                <div id="about-quill-editor"></div>
            </div>
            <script id="about-page-content-data" type="application/json"><?= json_encode((string) ($current['about_page_content'] ?? ''), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
            <textarea name="about_page_content" id="about_page_content" rows="12" style="display:none;"><?= (string) ($current['about_page_content'] ?? '') ?></textarea>
        </div>

        <div class="full" style="height:1px;background:#dce7f9;margin:2px 0 4px;"></div>

        <div>
            <label>Tiêu đề footer trái</label>
            <input type="text" name="footer_title_left" value="<?= e($current['footer_title_left']) ?>">
        </div>
        <div>
            <label>Tiêu đề footer phải</label>
            <input type="text" name="footer_title_right" value="<?= e($current['footer_title_right']) ?>">
        </div>

        <div class="full">
            <label>Địa chỉ</label>
            <input type="text" name="footer_address" value="<?= e($current['footer_address']) ?>">
        </div>

        <div>
            <label>Điện thoại</label>
            <input type="text" name="footer_phone" value="<?= e($current['footer_phone']) ?>">
        </div>
        <div>
            <label>Email</label>
            <input type="text" name="footer_email" value="<?= e($current['footer_email']) ?>">
        </div>

        <div>
            <label>Hotline 1</label>
            <input type="text" name="footer_hotline_1" value="<?= e($current['footer_hotline_1']) ?>">
        </div>
        <div>
            <label>Hotline 2</label>
            <input type="text" name="footer_hotline_2" value="<?= e($current['footer_hotline_2']) ?>">
        </div>

        <div>
            <label>Hotline 3</label>
            <input type="text" name="footer_hotline_3" value="<?= e($current['footer_hotline_3']) ?>">
        </div>
        <div>
            <label>Dòng bản quyền</label>
            <input type="text" name="footer_copyright" value="<?= e($current['footer_copyright']) ?>">
        </div>

        <div class="full">
            <button type="submit">Lưu cài đặt</button>
        </div>
    </form>
</div>

<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var baseUrl = <?= json_encode(base_url()) ?>;
    var normalizedBaseUrl = String(baseUrl || '').replace(/\/+$/, '');
    var contentField = document.getElementById('about_page_content');
    var payloadNode = document.getElementById('about-page-content-data');
    var editorWrap = document.getElementById('about-editor-wrap');
    var oldContent = contentField ? String(contentField.value || '') : '';
    if (!oldContent && payloadNode) {
        try {
            oldContent = String(JSON.parse(payloadNode.textContent || '""') || '');
        } catch (e) {
            oldContent = '';
        }
    }
    oldContent = oldContent.trim();
    var form = document.querySelector('form[method="post"]');
    var aboutQuill = null;

    function imageHandler() {
        var editor = (this && this.quill) ? this.quill : aboutQuill;
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
                    if (!data.success) {
                        alert(data.message || 'Upload ảnh thất bại.');
                        return;
                    }
                    var range = editor.getSelection(true);
                    var insertAt = range && typeof range.index === 'number' ? range.index : editor.getLength();
                    var imagePath = String(data.url || '').replace(/\\/g, '/').replace(/^\/+/, '');
                    editor.insertEmbed(insertAt, 'image', normalizedBaseUrl + '/' + imagePath);
                    editor.setSelection(insertAt + 1);
                })
                .catch(function (err) {
                    alert('Upload lỗi: ' + (err && err.message ? err.message : 'Không rõ nguyên nhân.'));
                });
        };
    }

    if (typeof Quill === 'undefined') {
        if (editorWrap) {
            editorWrap.style.display = 'none';
        }
        if (contentField) {
            contentField.style.display = 'block';
        }
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

        aboutQuill = new Quill('#about-quill-editor', {
            theme: 'snow',
            placeholder: 'Nhập nội dung trang Giới thiệu...',
            modules: quillModules
        });
    } catch (err) {
        if (editorWrap) {
            editorWrap.style.display = 'none';
        }
        if (contentField) {
            contentField.style.display = 'block';
        }
        return;
    }

    if (oldContent) {
        var hasHtmlTags = /<\/?[a-z][\s\S]*>/i.test(oldContent);
        if (hasHtmlTags) {
            aboutQuill.clipboard.dangerouslyPasteHTML(oldContent);
        } else {
            aboutQuill.setText(oldContent);
        }

        // Fallback: if Quill failed to render any visible text, load plain text version.
        if (aboutQuill.getText().trim().length === 0) {
            var tmp = document.createElement('div');
            tmp.innerHTML = oldContent;
            var plain = (tmp.textContent || tmp.innerText || oldContent).trim();
            if (plain) {
                aboutQuill.setText(plain);
            }
        }
    }

    function syncAboutContent() {
        if (!aboutQuill || !contentField) return;
        var html = aboutQuill.root.innerHTML.trim();
        contentField.value = html === '<p><br></p>' ? '' : html;
    }

    if (form) {
        form.addEventListener('submit', function () {
            syncAboutContent();
        }, true);
    }
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
