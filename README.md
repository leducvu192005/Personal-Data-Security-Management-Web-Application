# Ứng dụng Quản lý Thông tin Cá nhân Bảo mật

## Tổng quan
Đây là một **ứng dụng web xây dựng bằng PHP và MySQL** để quản lý thông tin khách hàng một cách **an toàn và bảo mật**.  
Ứng dụng minh họa các **thực hành tốt trong bảo mật dữ liệu** bao gồm:

- Hash mật khẩu bằng `password_hash()` của PHP
- Mã hóa dữ liệu nhạy cảm bằng AES-256-CBC
- Thao tác CRUD (Thêm, Xem, Sửa, Xóa) an toàn
- Tùy chọn: audit log và data masking

Dự án phù hợp để học về **bảo mật web, mã hóa dữ liệu và quản lý thông tin an toàn**.

---

## Tính năng

1. **Thêm khách hàng (Create)**  
   - Nhập thông tin: Họ tên, Email, SĐT, CMND, Mật khẩu  
   - Mật khẩu được hash trước khi lưu vào database  
   - Dữ liệu nhạy cảm (SĐT, CMND) được mã hóa

2. **Xem danh sách khách hàng (Read)**  
   - Hiển thị thông tin khách hàng với dữ liệu nhạy cảm được giải mã  
   - Mật khẩu vẫn được lưu dưới dạng hash

3. **Sửa khách hàng (Update)**  
   - Chỉnh sửa thông tin khách hàng một cách an toàn  
   - Mã hóa lại dữ liệu nhạy cảm nếu thay đổi  
   - Hash lại mật khẩu nếu được thay đổi

4. **Xóa khách hàng (Delete)**  
   - Xóa thông tin khách hàng khỏi cơ sở dữ liệu một cách an toàn

5. **Bảo mật nâng cao (Tùy chọn)**  
   - Audit log: Ghi lại các thao tác thêm/sửa/xóa  
   - Data masking: Hiển thị một phần dữ liệu nhạy cảm  
   - Khuyến nghị sử dụng HTTPS khi triển khai thực tế

---

## Công nghệ sử dụng

- **Backend:** PHP 8.x  
- **Database:** MySQL / MariaDB  
- **Frontend:** HTML, CSS, Bootstrap  
- **Mã hóa:** OpenSSL AES-256-CBC  
- **Bảo mật mật khẩu:** `password_hash` / `password_verify` của PHP

---
