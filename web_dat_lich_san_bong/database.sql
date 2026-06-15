-- Xóa database cũ (nếu có)
DROP DATABASE IF EXISTS web_dat_lich_san_bong;

-- Tạo database mới
CREATE DATABASE web_dat_lich_san_bong;
USE web_dat_lich_san_bong;

-- Bảng người dùng
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user'
);

-- Bảng sân bóng
CREATE TABLE pitches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50),
    price INT DEFAULT 200000,
    description TEXT
);

-- Bảng đặt sân
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    pitch_id INT NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status VARCHAR(20) DEFAULT 'pending'
);

-- Thêm tài khoản admin (mật khẩu: 123456)
INSERT INTO users (name, email, password, role) VALUES 
('Admin', 'admin@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Thêm sân mẫu
INSERT INTO pitches (name, type, price, description) VALUES
('Sân Thống Nhất', 'Sân 7 người', 300000, 'Cỏ nhân tạo, đèn chiếu sáng'),
('Sân Hoa Lư', 'Sân 5 người', 500000, 'Sân trong nhà, máy lạnh'),
('Sân Phú Thọ', 'Sân 11 người', 250000, 'Sân cỏ tự nhiên');