# Website Hội Sinh Viên (PHP + MySQL)

## 1) Tính năng
- Giao diện trang chủ theo phong cách TDC (banner, tin nổi bật, danh sách tin)
- Trang chi tiết bài viết + tin liên quan
- Trang danh mục
- Khu admin:
- Đăng nhập / đăng xuất
- CRUD danh mục
- CRUD bài viết (có upload thumbnail)
- CRUD slider (upload hoặc URL)

## 2) Cài đặt nhanh (WAMP)
1. Copy source vào: `c:/wamp64/www/hoi_sinh_vien`
2. Tạo database bằng phpMyAdmin:
- Tạo DB `hoi_sinh_vien`
- Import file `database.sql`
3. Chỉnh kết nối DB nếu cần tại: `config/database.php`
4. Mở trang:
- Frontend: `http://localhost/hoi_sinh_vien`
- Admin: `http://localhost/hoi_sinh_vien/admin/login.php`

## 3) Tài khoản admin mặc định
- Username: `admin`
- Password: `admin123`

## 4) Thư mục upload
- Ảnh bài viết: `uploads/posts`
- Ảnh slider: `uploads/slides`

Cần đảm bảo PHP có quyền ghi vào hai thư mục trên.
