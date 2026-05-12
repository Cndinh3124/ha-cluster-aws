-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th3 13, 2026 lúc 03:57 PM
-- Phiên bản máy phục vụ: 8.2.0
-- Phiên bản PHP: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `hoi_sinh_vien`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `admins`
--

INSERT INTO `admins` (`id`, `username`, `full_name`, `password_hash`, `created_at`) VALUES
(1, 'admin', 'Super Admin', '$2y$10$RZ31Wx11qfhlo6zm1RnS5eRAJWYVNskGdPdmgJk11ZR88Y7QFgTwG', '2026-03-13 05:10:20');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(180) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'Tin tức - Sự kiện', 'tin-tuc-su-kien', 1, 1, '2026-03-13 05:10:21'),
(2, 'Hỗ trợ sinh viên', 'ho-tro-sinh-vien', 2, 1, '2026-03-13 05:10:21'),
(3, 'Mỗi ngày một tin tốt', 'moi-ngay-mot-tin-tot', 3, 1, '2026-03-13 05:10:21'),
(4, 'Hội chữ thập đỏ', 'hoi-chu-thap-do', 4, 1, '2026-03-13 05:10:21');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contacts`
--

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `phone`, `subject`, `message`, `is_read`, `created_at`, `updated_at`) VALUES
(7, 'Hiếu Đỗ Ngọc', 'dongochieu333@gmail.com', '0384946564', 'sd', 'm, n', 1, '2026-03-13 15:29:30', '2026-03-13 15:54:56'),
(8, 'Hello', 'dongochieu333@gmail.com', '0384946973', 'Hello', 'lánml', 1, '2026-03-13 15:53:31', '2026-03-13 15:54:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `documents`
--

DROP TABLE IF EXISTS `documents`;
CREATE TABLE IF NOT EXISTS `documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `file_url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` int UNSIGNED NOT NULL DEFAULT '0',
  `status` enum('draft','published') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `issued_at` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `documents`
--

INSERT INTO `documents` (`id`, `title`, `slug`, `summary`, `file_url`, `file_name`, `file_size`, `status`, `issued_at`, `created_at`, `updated_at`) VALUES
(1, 'Thông báo v/v công bố danh sách dự kiến sinh viên đạt học bổng khuyến khích học tập học kỳ 2, năm học 2024-2025.', 'thong-bao-vv-cong-bo-danh-sach-du-kien-sinh-vien-dat-hoc-bong-khuyen-khich-hoc-tap-hoc-ky-2-nam-hoc-2024-2025', 'Xem danh sách ở web https://online.tdc.edu.vn/', 'http://localhost/uploads/documents/doc_69b41e33985186.97996968.pdf', 'TB cong bo ds du kien sv dat hoc bong KKHT HK2 nam hoc 2024-2025.pdf', 719992, 'published', '2026-03-13', '2026-03-13 14:24:51', '2026-03-13 14:26:12');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text,
  `content` longtext,
  `thumbnail` varchar(500) DEFAULT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'published',
  `published_at` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `fk_posts_category` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `posts`
--

INSERT INTO `posts` (`id`, `category_id`, `title`, `slug`, `excerpt`, `content`, `thumbnail`, `status`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Hướng dẫn thay đổi địa điểm bầu cử trên ứng dụng VNeID', 'huong-dan-thay-doi-dia-diem-bau-cu-tren-ung-dung-vneid', 'Sinh viên TDC hướng dẫn các bước thay đổi địa điểm bỏ phiếu trên ứng dụng VNeID', '<p>Nhằm tạo điều kiện thuận lợi cho công dân tham gia bầu cử, ứng dụng <strong>VNeID</strong> cho phép người dân đăng ký <strong>thay đổi địa điểm bầu cử</strong> ngay trên điện thoại. Dưới đây là các bước thực hiện:</p><p><br></p><h3>Bước 1: Đăng nhập ứng dụng</h3><p>Mở ứng dụng <strong>VNeID</strong> trên điện thoại và <strong>đăng nhập bằng tài khoản định danh điện tử</strong> đã đăng ký.</p><p><br></p><h3>Bước 2: Chọn mục dịch vụ</h3><p>Tại màn hình chính, chọn <strong>“Dịch vụ công”</strong> hoặc mục <strong>“Tiện ích”</strong> liên quan đến <strong>bầu cử</strong>.</p><p><br></p><h3>Bước 3: Chọn chức năng thay đổi địa điểm bầu cử</h3><p>Tìm và chọn <strong>“Thay đổi nơi bỏ phiếu / địa điểm bầu cử”</strong> trong danh sách dịch vụ.</p><p><br></p><h3>Bước 4: Điền thông tin yêu cầu</h3><p>Nhập đầy đủ các thông tin theo yêu cầu như:</p><p><br></p><ul><li>Nơi đăng ký thường trú hoặc tạm trú</li><li>Địa điểm bầu cử mới mong muốn</li><li>Lý do thay đổi (nếu có)</li></ul><h3>Bước 5: Xác nhận và gửi yêu cầu</h3><p>Kiểm tra lại thông tin, sau đó <strong>xác nhận gửi yêu cầu</strong>. Hệ thống sẽ tiếp nhận và chuyển đến cơ quan có thẩm quyền để xử lý.</p><p><br></p><h3>Bước 6: Theo dõi trạng thái hồ sơ</h3><p>Người dùng có thể <strong>theo dõi trạng thái xử lý hồ sơ ngay trên ứng dụng VNeID</strong>. Khi yêu cầu được chấp thuận, thông tin địa điểm bầu cử mới sẽ được cập nhật trong hệ thống.</p><p>✅ <strong>Lưu ý:</strong></p><p><br></p><ul><li>Nên thực hiện đăng ký thay đổi <strong>trước thời hạn quy định</strong> của kỳ bầu cử.</li><li>Cung cấp thông tin chính xác để tránh bị từ chối hồ sơ.</li></ul><p>Việc sử dụng ứng dụng <strong>VNeID</strong> giúp người dân <strong>tiết kiệm thời gian, thực hiện thủ tục nhanh chóng và thuận tiện</strong>, đồng thời góp phần thúc đẩy chuyển đổi số trong các dịch vụ công.&nbsp;</p>', 'http://localhost/uploads/posts/post_69b41c80174958.81266632.jpg', 'published', '2026-03-12', '2026-03-13 05:10:21', '2026-03-13 14:17:36'),
(2, 1, 'Lá phiếu hồng – Niềm tin ngày hội non sông', 'la-phieu-hong-niem-tin-ngay-hoi-non-song', 'Hoạt động tuyên truyền ý nghĩa cho sinh viên TDC', '<p>“Lá phiếu hồng – Niềm tin ngày hội non sông” là thông điệp ý nghĩa nhằm tuyên truyền và khuyến khích mỗi công dân tích cực tham gia <strong>Ngày Bầu cử</strong>. Lá phiếu tuy nhỏ bé nhưng mang ý nghĩa vô cùng to lớn, thể hiện quyền làm chủ của nhân dân và niềm tin vào sự phát triển của đất nước.</p><p>Thông qua lá phiếu của mình, mỗi cử tri góp phần lựa chọn những đại biểu tiêu biểu, có đủ phẩm chất, năng lực và trách nhiệm để đại diện cho ý chí, nguyện vọng của nhân dân. Đây không chỉ là <strong>quyền lợi</strong>, mà còn là <strong>trách nhiệm và nghĩa vụ của mỗi công dân</strong> đối với Tổ quốc.</p><p>Ngày bầu cử là <strong>ngày hội lớn của toàn dân</strong>, nơi mỗi người dân cùng chung tay xây dựng một bộ máy nhà nước vững mạnh, hoạt động vì lợi ích của nhân dân và sự phát triển bền vững của đất nước.</p><p>Hãy cùng nhau phát huy tinh thần trách nhiệm, tích cực tham gia bầu cử, để <strong>mỗi lá phiếu hồng trở thành niềm tin, hy vọng và sức mạnh góp phần xây dựng non sông Việt Nam ngày càng giàu mạnh, dân chủ và văn minh.</strong></p>', 'http://localhost/uploads/posts/post_69b41d71451737.22238677.webp', 'published', '2026-03-12', '2026-03-13 05:10:21', '2026-03-13 14:21:37'),
(3, 2, 'Tuyên truyền Ngày Bầu cử Toàn dân năm 2026', 'tuyen-truyen-ngay-bau-cu-toan-dan-nam-2026', 'Chuỗi hoạt động truyền thông đến toàn bộ sinh viên nhà trường', '<p>Ngày Bầu cử Toàn dân là sự kiện chính trị quan trọng của đất nước, thể hiện quyền làm chủ và trách nhiệm của mỗi công dân trong việc xây dựng bộ máy nhà nước vững mạnh. Đây là dịp để cử tri trên cả nước trực tiếp lựa chọn những đại biểu có đủ phẩm chất, năng lực và uy tín để đại diện cho ý chí, nguyện vọng của nhân dân.</p><p>Việc tham gia bầu cử không chỉ là <strong>quyền lợi</strong>, mà còn là <strong>nghĩa vụ thiêng liêng của mỗi công dân</strong>. Thông qua lá phiếu của mình, mỗi người dân góp phần vào việc xây dựng một xã hội dân chủ, công bằng và phát triển bền vững.</p><p>Vì vậy, mỗi cử tri cần tích cực tìm hiểu thông tin về <strong>ứng cử viên, chương trình hành động và các quy định của pháp luật về bầu cử</strong>, từ đó lựa chọn và bỏ phiếu một cách sáng suốt, đúng quy định. Đồng thời, mỗi người cũng cần tuyên truyền, vận động người thân và cộng đồng cùng tham gia bầu cử đầy đủ, đúng thời gian và đúng quy định.</p><p>Hãy cùng nhau phát huy tinh thần trách nhiệm, tham gia <strong>Ngày Bầu cử Toàn dân năm 2026</strong> một cách nghiêm túc, dân chủ và đúng pháp luật, góp phần xây dựng đất nước ngày càng phát triển, giàu mạnh và văn minh.&nbsp;</p>', 'http://localhost/uploads/posts/post_69b41bd305c979.77181179.jpg', 'published', '2026-03-11', '2026-03-13 05:10:21', '2026-03-13 14:14:43'),
(4, 4, 'Vận động quyên góp miền Trung', 'van-dong-quyen-gop-ung-ho-mien-trung', 'Hội chữ thập đỏ TDC tiếp nhận đóng góp và chuyển đến ba con vùng lũ.', '<p><strong>Chương trình tiếp nhận nhu yếu phẩm, nhuận phẩm và tiền mặt để hỗ trợ các tỉnh miền Trung bị ảnh hưởng bởi thiên tai</strong></p><p><img src=\"http://localhost//uploads/posts/content_69b4332ec2b123.78294152.png\"></p><p>Nhằm chung tay chia sẻ khó khăn với đồng bào các tỉnh miền Trung đang chịu nhiều thiệt hại do thiên tai gây ra, chúng tôi tổ chức chương trình tiếp nhận <strong>nhu yếu phẩm, nhuận phẩm và tiền mặt</strong> để kịp thời hỗ trợ người dân vùng bị ảnh hưởng.</p><p>Thiên tai đã gây ra nhiều tổn thất về nhà cửa, tài sản và ảnh hưởng lớn đến đời sống sinh hoạt của bà con. Vì vậy, chương trình mong muốn kêu gọi sự chung tay đóng góp từ các cá nhân, tổ chức và nhà hảo tâm để mang đến sự hỗ trợ thiết thực, giúp người dân sớm ổn định cuộc sống.</p><p>Các vật phẩm tiếp nhận bao gồm: <strong>lương thực, thực phẩm khô, nước uống, quần áo, chăn màn, thuốc men, đồ dùng sinh hoạt thiết yếu</strong> và các khoản <strong>đóng góp tiền mặt</strong>. Mọi sự đóng góp, dù lớn hay nhỏ, đều là nguồn động viên quý giá giúp bà con vượt qua giai đoạn khó khăn này.</p><p>Toàn bộ nhu yếu phẩm và nguồn hỗ trợ tài chính sẽ được <strong>tổng hợp, phân loại và chuyển trực tiếp đến các khu vực chịu ảnh hưởng nặng nề của thiên tai tại miền Trung</strong>, đảm bảo minh bạch và đúng mục đích.</p><p>Chúng tôi rất mong nhận được sự quan tâm, sẻ chia và đồng hành của cộng đồng để cùng nhau lan tỏa tinh thần <strong>“lá lành đùm lá rách”</strong>, góp phần giúp đồng bào miền Trung sớm vượt qua khó khăn và ổn định cuộc sống.</p><p><strong>Mọi sự đóng góp và hỗ trợ đều vô cùng quý báu. Xin chân thành cảm ơn!</strong> ❤️</p>', 'http://localhost/uploads/posts/post_69b41b40206443.02045235.jpg', 'published', '2026-03-10', '2026-03-13 05:10:21', '2026-03-13 15:54:26');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE IF NOT EXISTS `site_settings` (
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `site_settings`
--

INSERT INTO `site_settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
('about_page_content', '<p><em style=\"color: rgb(230, 0, 0);\">Đoàn Thanh niên Cộng sản Hồ Chí Minh Trường Cao đẳng Công nghệ Thủ Đức</em> là tổ chức chính trị - xã hội của thanh niên trong nhà trường, trực thuộc Thành Đoàn Thành phố Hồ Chí Minh, dưới sự lãnh đạo toàn diện của Đảng ủy và Ban Giám hiệu Nhà trường. Đây là tổ chức tập hợp, đoàn kết, giáo dục và phát huy vai trò tiên phong, gương mẫu của đoàn viên, sinh viên trong học tập, rèn luyện, lao động, sáng tạo và cống hiến cho cộng đồng.</p><p>Trải qua quá trình hình thành và phát triển, Đoàn Trường luôn khẳng định vai trò là&nbsp;<strong>hạt nhân chính trị, đội dự bị tin cậy của Đảng</strong>, là lực lượng xung kích, sáng tạo trong mọi lĩnh vực hoạt động của Nhà trường. Từng thế hệ đoàn viên, sinh viên Trường Cao đẳng Công nghệ Thủ Đức luôn mang trong mình tinh thần&nbsp;<strong>“Đâu cần thanh niên có, đâu khó có thanh niên”</strong>, sẵn sàng nhận nhiệm vụ mới, dấn thân vào những hoạt động ý nghĩa vì tập thể, vì cộng đồng và vì sự phát triển chung của xã hội.</p><p><img src=\"http://localhost/uploads/posts/content_69b419979a2e88.13415399.png\"></p><p><br></p><h3><strong>1. Vai trò xung kích trong học tập và nghiên cứu khoa học</strong></h3><p>Đoàn Thanh niên và Hội Sinh viên luôn coi trọng việc xây dựng môi trường học tập chủ động, sáng tạo, giúp sinh viên phát huy khả năng tư duy, nghiên cứu và ứng dụng kiến thức vào thực tiễn.</p><p>Các phong trào như&nbsp;<strong>“Sinh viên 5 tốt”, “Tuổi trẻ sáng tạo”, “Sinh viên nghiên cứu khoa học”, “Ý tưởng khởi nghiệp – đổi mới sáng tạo”</strong>&nbsp;được triển khai sâu rộng ở tất cả các Đoàn khoa.&nbsp;Qua đó, nhiều công trình, mô hình học thuật, sáng kiến kỹ thuật của sinh viên đã được ghi nhận và áp dụng thực tế, góp phần nâng cao chất lượng đào tạo của Nhà trường.</p><h3><strong>2. Vai trò xung kích trong rèn luyện và xây dựng lối sống đẹp</strong></h3><p>Song song với việc học tập, Đoàn – Hội Trường luôn chú trọng giáo dục đạo đức, lối sống, ý thức công dân và tinh thần trách nhiệm xã hội cho sinh viên.</p><p>Các hoạt động tuyên truyền, sinh hoạt chuyên đề, diễn đàn “Xây dựng giá trị hình mẫu thanh niên Trường Cao đẳng Công nghệ Thủ Đức thời kỳ mới” được tổ chức thường xuyên, giúp đoàn viên, sinh viên thấm nhuần lý tưởng sống đẹp – sống có ích.</p><p>Phong trào&nbsp;<strong>“Sinh viên 5 tốt”</strong>&nbsp;được xem là chuẩn mực phấn đấu toàn diện về&nbsp;<strong>Đạo đức – Học tập – Thể lực – Tình nguyện – Hội nhập</strong>, qua đó tạo môi trường rèn luyện tích cực và là động lực giúp mỗi sinh viên hoàn thiện bản thân.</p><h3><strong>3. Vai trò xung kích trong hoạt động tình nguyện vì cộng đồng</strong></h3><p>Tinh thần tình nguyện là nét đẹp truyền thống của tuổi trẻ Trường Cao đẳng Công nghệ Thủ Đức. Hằng năm, hàng trăm lượt đoàn viên, sinh viên tham gia các chiến dịch lớn của Thành phố như&nbsp;<strong>Mùa hè xanh, Xuân tình nguyện, Tiếp sức mùa thi, Chủ nhật xanh, Hiến máu nhân đạo</strong>, cùng nhiều hoạt động chăm lo cho trẻ em, thanh niên công nhân, người yếu thế trong xã hội.</p><p>Những chương trình như&nbsp;<strong>“Cùng em đến trường”, “Bữa cơm yêu thương”, “Tết sẻ chia”, “Ngôi nhà xanh – hành động nhỏ, ý nghĩa lớn”</strong>&nbsp;đã lan tỏa tinh thần nhân ái, trách nhiệm và sẻ chia trong toàn thể sinh viên, thể hiện rõ nét&nbsp;<strong>vai trò tiên phong của tuổi trẻ nhà trường trong công tác xã hội, bảo vệ môi trường và phát triển bền vững</strong>.</p><h3><strong>4. Vai trò xung kích trong xây dựng Nhà trường và Hội nhập quốc tế</strong></h3><p>Đoàn – Hội Trường còn là cầu nối gắn kết giữa sinh viên với Nhà trường, giữa thầy cô và học trò, góp phần xây dựng môi trường học tập thân thiện, năng động, sáng tạo.</p><p>Các hoạt động văn hóa, thể thao, hội thi kỹ năng, hội trại, diễn đàn sinh viên… không chỉ mang lại sân chơi bổ ích mà còn khuyến khích tinh thần hội nhập, tự tin và bản lĩnh trong sinh viên.</p><p>Tuổi trẻ Trường Cao đẳng Công nghệ Thủ Đức còn tích cực tham gia các hoạt động giao lưu quốc tế, chương trình trao đổi sinh viên, góp phần hình thành hình ảnh&nbsp;<strong>người sinh viên Công nghệ Thủ Đức năng động, tự tin, sáng tạo và hội nhập toàn cầu.</strong></p><h3><strong>5. Hội Sinh viên – người bạn đồng hành của sinh viên</strong></h3><p>Hội Sinh viên Việt Nam Trường Cao đẳng Công nghệ Thủ Đức là tổ chức đại diện, chăm lo và bảo vệ quyền lợi hợp pháp, chính đáng của sinh viên. Hội luôn đồng hành cùng sinh viên trong học tập, nghiên cứu, khởi nghiệp, chăm lo đời sống tinh thần và hỗ trợ kỹ năng cần thiết cho hành trang lập nghiệp.</p><p>Hệ thống&nbsp;<strong>Câu lạc bộ – Đội – Nhóm</strong>&nbsp;được duy trì, phát triển đa dạng: từ học thuật, văn nghệ, thể thao, kỹ năng sống đến tình nguyện xã hội, là môi trường rèn luyện hiệu quả, giúp sinh viên khẳng định năng lực và phát huy sở trường.</p>', '2026-03-13 14:05:54'),
('about_subtitle', 'Đoàn Thanh niên Cộng sản Hồ Chí Minh - Trường Cao đẳng Công nghệ Thủ Đức', '2026-03-13 07:00:54'),
('about_title', 'Giới thiệu Đoàn - Hội Sinh viên Trường', '2026-03-13 07:00:54'),
('footer_address', 'Địa chỉ: 53 Võ Văn Ngân, Phường Thủ Đức, TP Hồ Chí Minh', '2026-03-13 06:24:51'),
('footer_copyright', 'Nguyễn Công Định', '2026-03-13 12:44:55'),
('footer_email', 'Email: tdc@mail.tdc.edu.vn', '2026-03-13 06:24:51'),
('footer_hotline_1', 'Phòng Tuyển sinh: 028 3897 0023', '2026-03-13 06:24:51'),
('footer_hotline_2', 'Phòng Công tác sinh viên: 028 2215 8640', '2026-03-13 06:24:51'),
('footer_hotline_3', 'Phòng Kế hoạch - Tài chính: 028 6282 0044', '2026-03-13 06:24:51'),
('footer_phone', 'Điện thoại: 028 3896 6825 - 028 3897 0023', '2026-03-13 06:24:51'),
('footer_title_left', 'Thông Tin Trường', '2026-03-13 06:24:51'),
('footer_title_right', 'Hotline Liên Hệ', '2026-03-13 06:24:51'),
('logo_image_url', 'http://localhost/uploads/logo_69b4194fdc0532.08544530.png', '2026-03-13 14:03:59'),
('school_name', 'TRƯỜNG CAO ĐẲNG CÔNG NGHỆ THỦ ĐỨC', '2026-03-13 14:03:23'),
('school_subtitle', 'Cổng thông tin sinh viên', '2026-03-13 06:24:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sliders`
--

DROP TABLE IF EXISTS `sliders`;
CREATE TABLE IF NOT EXISTS `sliders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `image_url` varchar(500) NOT NULL,
  `link_url` varchar(500) DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `sliders`
--

INSERT INTO `sliders` (`id`, `title`, `subtitle`, `image_url`, `link_url`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'Chiến dịch Xuân Tình Nguyện 2026', 'Lần thứ 18 - Tuổi trẻ TDC sẳn sàng cống hiến', 'http://localhost/uploads/slides/slide_69b429f8f3ba01.91810954.png', 'https://chatgpt.com/c/69b41a97-67c0-8322-b817-06c0b825bce2', 1, 1, '2026-03-13 05:10:21'),
(2, 'Thong bao tuyen sinh moi nhat', 'Cap nhat lich xet tuyen va huong dan nop ho so', 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1600&q=80', 'http://localhost/', 2, 1, '2026-03-13 05:10:21'),
(3, 'Hello', '', 'http://localhost/uploads/slides/slide_69b403de378399.10906066.png', '', 3, 1, '2026-03-13 12:32:30');

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
