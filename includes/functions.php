<?php

declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function format_date(string $value): string
{
    return date('d/m/Y', strtotime($value));
}

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $replacements = [
        'a' => '/[aAàáạảãâầấậẩẫăằắặẳẵ]/u',
        'e' => '/[eEèéẹẻẽêềếệểễ]/u',
        'i' => '/[iIìíịỉĩ]/u',
        'o' => '/[oOòóọỏõôồốộổỗơờớợởỡ]/u',
        'u' => '/[uUùúụủũưừứựửữ]/u',
        'y' => '/[yYỳýỵỷỹ]/u',
        'd' => '/[dDđ]/u',
    ];

    foreach ($replacements as $replace => $regex) {
        $value = preg_replace($regex, $replace, $value) ?? $value;
    }

    $value = preg_replace('/[^a-z0-9\s-]/', '', $value) ?? '';
    $value = preg_replace('/[\s-]+/', '-', $value) ?? '';

    return trim($value, '-');
}

function get_categories(PDO $pdo): array
{
    $sql = 'SELECT id, name, slug FROM categories WHERE is_active = 1 ORDER BY sort_order ASC, id DESC';
    return $pdo->query($sql)->fetchAll();
}

function get_slides(PDO $pdo): array
{
    $sql = 'SELECT id, title, subtitle, image_url, link_url FROM sliders WHERE is_active = 1 ORDER BY sort_order ASC, id DESC';
    return $pdo->query($sql)->fetchAll();
}

function get_site_settings(PDO $pdo): array
{
    $defaultAboutContent = <<<'HTML'
<p>Đoàn Thanh niên Cộng sản Hồ Chí Minh Trường Cao đẳng Công nghệ Thủ Đức là tổ chức chính trị - xã hội của thanh niên trong nhà trường, trực thuộc Thành Đoàn Thành phố Hồ Chí Minh, dưới sự lãnh đạo toàn diện của Đảng ủy và Ban Giám hiệu Nhà trường. Đây là tổ chức tập hợp, đoàn kết, giáo dục và phát huy vai trò tiên phong, gương mẫu của đoàn viên, sinh viên trong học tập, rèn luyện, lao động, sáng tạo và cống hiến cho cộng đồng.</p>
<p>Trải qua quá trình hình thành và phát triển, Đoàn Trường luôn khẳng định vai trò là hạt nhân chính trị, đội dự bị tin cậy của Đảng, là lực lượng xung kích, sáng tạo trong mọi lĩnh vực hoạt động của Nhà trường. Từng thế hệ đoàn viên, sinh viên Trường Cao đẳng Công nghệ Thủ Đức luôn mang trong mình tinh thần "Đâu cần thanh niên có, đâu khó có thanh niên", sẵn sàng nhận nhiệm vụ mới, dấn thân vào những hoạt động ý nghĩa vì tập thể, vì cộng đồng và vì sự phát triển chung của xã hội.</p>
<h2>1. Vai trò xung kích trong học tập và nghiên cứu khoa học</h2>
<p>Đoàn Thanh niên và Hội Sinh viên luôn coi trọng việc xây dựng môi trường học tập chủ động, sáng tạo, giúp sinh viên phát huy khả năng tư duy, nghiên cứu và ứng dụng kiến thức vào thực tiễn. Các phong trào như "Sinh viên 5 tốt", "Tuổi trẻ sáng tạo", "Sinh viên nghiên cứu khoa học", "Ý tưởng khởi nghiệp - đổi mới sáng tạo" được triển khai sâu rộng ở tất cả các Đoàn khoa.</p>
<h2>2. Vai trò xung kích trong rèn luyện và xây dựng lối sống đẹp</h2>
<p>Song song với việc học tập, Đoàn - Hội Trường luôn chú trọng giáo dục đạo đức, lối sống, ý thức công dân và tinh thần trách nhiệm xã hội cho sinh viên. Phong trào "Sinh viên 5 tốt" được xem là chuẩn mực phấn đấu toàn diện về Đạo đức - Học tập - Thể lực - Tình nguyện - Hội nhập.</p>
<h2>3. Vai trò xung kích trong hoạt động tình nguyện vì cộng đồng</h2>
<p>Tinh thần tình nguyện là nét đẹp truyền thống của tuổi trẻ Trường Cao đẳng Công nghệ Thủ Đức. Hằng năm, hàng trăm lượt đoàn viên, sinh viên tham gia các chiến dịch lớn của Thành phố như Mùa hè xanh, Xuân tình nguyện, Tiếp sức mùa thi, Chủ nhật xanh, Hiến máu nhân đạo và nhiều hoạt động chăm lo cộng đồng.</p>
<h2>4. Vai trò xung kích trong xây dựng Nhà trường và Hội nhập quốc tế</h2>
<p>Đoàn - Hội Trường là cầu nối gắn kết giữa sinh viên với Nhà trường, góp phần xây dựng môi trường học tập thân thiện, năng động, sáng tạo. Nhiều hoạt động văn hóa, thể thao, hội thi kỹ năng và giao lưu quốc tế được tổ chức thường xuyên, tạo cơ hội cho sinh viên phát triển toàn diện.</p>
<h2>5. Hội Sinh viên - người bạn đồng hành của sinh viên</h2>
<p>Hội Sinh viên Việt Nam Trường Cao đẳng Công nghệ Thủ Đức là tổ chức đại diện, chăm lo và bảo vệ quyền lợi hợp pháp, chính đáng của sinh viên. Hệ thống Câu lạc bộ - Đội - Nhóm đa dạng là môi trường rèn luyện hiệu quả, giúp sinh viên khẳng định năng lực và phát huy sở trường.</p>
HTML;

    $defaults = [
        'school_name' => 'TRƯỜNG CAO ĐẲNG CÔNG NGHỆ THỦ ĐỨC',
        'school_subtitle' => 'Cổng thông tin sinh viên',
        'logo_image_url' => '',
        'about_title' => 'Giới thiệu Đoàn - Hội Sinh viên Trường',
        'about_subtitle' => 'Đoàn Thanh niên Cộng sản Hồ Chí Minh - Trường Cao đẳng Công nghệ Thủ Đức',
        'about_page_content' => $defaultAboutContent,
        'footer_title_left' => 'Thông Tin Trường',
        'footer_address' => 'Địa chỉ: 53 Võ Văn Ngân, Phường Thủ Đức, TP Hồ Chí Minh',
        'footer_phone' => 'Điện thoại: 028 3896 6825 - 028 3897 0023',
        'footer_email' => 'Email: tdc@mail.tdc.edu.vn',
        'footer_title_right' => 'Hotline Liên Hệ',
        'footer_hotline_1' => 'Phòng Tuyển sinh: 028 3897 0023',
        'footer_hotline_2' => 'Phòng Công tác sinh viên: 028 2215 8640',
        'footer_hotline_3' => 'Phòng Kế hoạch - Tài chính: 028 6282 0044',
        'footer_copyright' => 'TRƯỜNG CAO ĐẲNG CÔNG NGHỆ THỦ ĐỨC',
    ];

    try {
        $rows = $pdo->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll();
    } catch (Throwable $e) {
        return $defaults;
    }

    foreach ($rows as $row) {
        $key = (string) ($row['setting_key'] ?? '');
        if (array_key_exists($key, $defaults)) {
            $defaults[$key] = (string) ($row['setting_value'] ?? '');
        }
    }

    return $defaults;
}

function extract_first_image_url(string $html): ?string
{
    if ($html === '') {
        return null;
    }

    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $matches) !== 1) {
        return null;
    }

    return trim((string) ($matches[1] ?? '')) ?: null;
}

function post_cover_image(array $post): string
{
    $thumbnail = trim((string) ($post['thumbnail'] ?? ''));
    if ($thumbnail !== '') {
        return $thumbnail;
    }

    $contentImage = extract_first_image_url((string) ($post['content'] ?? ''));
    if ($contentImage !== null && $contentImage !== '') {
        return $contentImage;
    }

    return base_url('assets/images/placeholder.svg');
}

function get_latest_posts(PDO $pdo, int $limit = 6): array
{
    $stmt = $pdo->prepare(
        'SELECT p.id, p.title, p.slug, p.thumbnail, p.content, p.published_at, c.name AS category_name, c.slug AS category_slug
         FROM posts p
         LEFT JOIN categories c ON c.id = p.category_id
         WHERE p.status = ?
         ORDER BY p.published_at DESC, p.id DESC
         LIMIT ?'
    );
    $stmt->bindValue(1, 'published');
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function get_post_by_slug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare(
        'SELECT p.*, c.name AS category_name, c.slug AS category_slug
         FROM posts p
         LEFT JOIN categories c ON c.id = p.category_id
         WHERE p.slug = ? AND p.status = ?
         LIMIT 1'
    );
    $stmt->execute([$slug, 'published']);
    $post = $stmt->fetch();

    return $post ?: null;
}

function get_related_posts(PDO $pdo, int $categoryId, int $excludeId, int $limit = 4): array
{
    $stmt = $pdo->prepare(
        'SELECT id, title, slug, thumbnail, content, published_at
         FROM posts
         WHERE status = ? AND category_id = ? AND id <> ?
         ORDER BY published_at DESC, id DESC
         LIMIT ?'
    );
    $stmt->bindValue(1, 'published');
    $stmt->bindValue(2, $categoryId, PDO::PARAM_INT);
    $stmt->bindValue(3, $excludeId, PDO::PARAM_INT);
    $stmt->bindValue(4, $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function get_posts_by_category_slug(PDO $pdo, string $slug): array
{
    $stmt = $pdo->prepare(
        'SELECT p.id, p.title, p.slug, p.thumbnail, p.content, p.published_at, p.excerpt, c.name AS category_name, c.slug AS category_slug
         FROM posts p
         INNER JOIN categories c ON c.id = p.category_id
         WHERE c.slug = ? AND p.status = ?
         ORDER BY p.published_at DESC, p.id DESC'
    );
    $stmt->execute([$slug, 'published']);

    return $stmt->fetchAll();
}

function get_category_by_slug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare('SELECT id, name, slug FROM categories WHERE slug = ? LIMIT 1');
    $stmt->execute([$slug]);
    $category = $stmt->fetch();

    return $category ?: null;
}

function get_notice_posts(PDO $pdo, int $limit = 20): array
{
    $stmt = $pdo->prepare(
        'SELECT p.id, p.title, p.slug, p.thumbnail, p.content, p.excerpt, p.published_at
         FROM posts p
         WHERE p.status = ?
         ORDER BY p.published_at DESC, p.id DESC
         LIMIT ?'
    );
    $stmt->bindValue(1, 'published');
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function ensure_documents_table(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS documents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            summary TEXT NULL,
            file_url VARCHAR(500) NOT NULL,
            file_name VARCHAR(255) NOT NULL,
            file_size INT UNSIGNED NOT NULL DEFAULT 0,
            status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
            issued_at DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    );
}

function get_latest_documents(PDO $pdo, int $limit = 30): array
{
    if ($limit < 1) {
        $limit = 1;
    }

    try {
        ensure_documents_table($pdo);
        $stmt = $pdo->prepare(
            'SELECT id, title, slug, summary, file_url, file_name, file_size, issued_at
             FROM documents
             WHERE status = ?
             ORDER BY issued_at DESC, id DESC
             LIMIT ?'
        );
        $stmt->bindValue(1, 'published');
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}

function format_file_size(int $bytes): string
{
    if ($bytes <= 0) {
        return '0 B';
    }

    $units = ['B', 'KB', 'MB', 'GB'];
    $size = (float) $bytes;
    $unitIndex = 0;

    while ($size >= 1024 && $unitIndex < count($units) - 1) {
        $size /= 1024;
        $unitIndex++;
    }

    if ($unitIndex === 0) {
        return (string) (int) $size . ' ' . $units[$unitIndex];
    }

    return number_format($size, 1) . ' ' . $units[$unitIndex];
}

function base_url(string $path = ''): string
{
    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}

function ensure_contacts_table(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS contacts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50) DEFAULT NULL,
            subject VARCHAR(255) DEFAULT NULL,
            message TEXT NOT NULL,
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    );
}

function save_contact(PDO $pdo, string $name, string $email, ?string $phone, ?string $subject, string $message): int
{
    ensure_contacts_table($pdo);
    $stmt = $pdo->prepare('INSERT INTO contacts (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$name, $email, $phone, $subject, $message]);
    return (int) $pdo->lastInsertId();
}

function get_contacts(PDO $pdo, int $limit = 50, int $offset = 0): array
{
    ensure_contacts_table($pdo);
    $stmt = $pdo->prepare('SELECT * FROM contacts ORDER BY created_at DESC, id DESC LIMIT ? OFFSET ?');
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_contacts_count(PDO $pdo): int
{
    ensure_contacts_table($pdo);
    $stmt = $pdo->query('SELECT COUNT(*) FROM contacts');
    return (int) $stmt->fetchColumn();
}

function get_unread_contacts_count(PDO $pdo): int
{
    ensure_contacts_table($pdo);
    $stmt = $pdo->query('SELECT COUNT(*) FROM contacts WHERE is_read = 0');
    return (int) $stmt->fetchColumn();
}

function get_contact_by_id(PDO $pdo, int $id): ?array
{
    ensure_contacts_table($pdo);
    $stmt = $pdo->prepare('SELECT * FROM contacts WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function mark_contact_read(PDO $pdo, int $id, bool $read = true): void
{
    ensure_contacts_table($pdo);
    $stmt = $pdo->prepare('UPDATE contacts SET is_read = ? WHERE id = ?');
    $stmt->execute([$read ? 1 : 0, $id]);
}

function delete_contact(PDO $pdo, int $id): void
{
    ensure_contacts_table($pdo);
    $stmt = $pdo->prepare('DELETE FROM contacts WHERE id = ?');
    $stmt->execute([$id]);
}
