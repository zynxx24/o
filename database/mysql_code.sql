-- Database Pemesanan Katering Online
-- Drop database jika sudah ada
DROP DATABASE IF EXISTS katering_online;
CREATE DATABASE katering_online;
USE katering_online;

-- Tabel Users (Pelanggan dan Admin)
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('customer', 'admin', 'vendor') DEFAULT 'customer',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- Tabel Vendors (Penyedia Katering)
CREATE TABLE vendors (
    vendor_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    vendor_name VARCHAR(100) NOT NULL,
    description TEXT,
    address TEXT NOT NULL,
    city VARCHAR(50),
    province VARCHAR(50),
    postal_code VARCHAR(10),
    phone VARCHAR(20),
    email VARCHAR(100),
    logo_url VARCHAR(255),
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_city (city),
    INDEX idx_rating (rating)
);

-- Tabel Kategori Menu
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(50) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Menu Items
CREATE TABLE menu_items (
    item_id INT PRIMARY KEY AUTO_INCREMENT,
    vendor_id INT NOT NULL,
    category_id INT,
    item_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    min_order INT DEFAULT 1,
    unit VARCHAR(20) DEFAULT 'porsi',
    image_url VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE,
    preparation_time INT COMMENT 'dalam menit',
    spicy_level ENUM('tidak pedas', 'sedang', 'pedas', 'sangat pedas') DEFAULT 'tidak pedas',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendors(vendor_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL,
    INDEX idx_vendor (vendor_id),
    INDEX idx_category (category_id),
    INDEX idx_price (price)
);

-- Tabel Paket Katering
CREATE TABLE packages (
    package_id INT PRIMARY KEY AUTO_INCREMENT,
    vendor_id INT NOT NULL,
    package_name VARCHAR(100) NOT NULL,
    description TEXT,
    price_per_person DECIMAL(10,2) NOT NULL,
    min_person INT DEFAULT 10,
    max_person INT,
    package_type ENUM('prasmanan', 'nasi kotak', 'snack box', 'custom') NOT NULL,
    image_url VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendors(vendor_id) ON DELETE CASCADE,
    INDEX idx_vendor (vendor_id),
    INDEX idx_type (package_type)
);

-- Tabel Detail Paket (Menu dalam paket)
CREATE TABLE package_items (
    package_item_id INT PRIMARY KEY AUTO_INCREMENT,
    package_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT DEFAULT 1,
    FOREIGN KEY (package_id) REFERENCES packages(package_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE CASCADE
);

-- Tabel Orders
CREATE TABLE orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    vendor_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    order_type ENUM('package', 'custom') NOT NULL,
    event_type VARCHAR(50),
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    delivery_address TEXT NOT NULL,
    delivery_city VARCHAR(50),
    num_people INT NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    tax DECIMAL(12,2) DEFAULT 0.00,
    delivery_fee DECIMAL(10,2) DEFAULT 0.00,
    discount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(12,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'preparing', 'delivering', 'completed', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('unpaid', 'partial', 'paid', 'refunded') DEFAULT 'unpaid',
    special_request TEXT,
    cancellation_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (vendor_id) REFERENCES vendors(vendor_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_vendor (vendor_id),
    INDEX idx_status (status),
    INDEX idx_event_date (event_date)
);

-- Tabel Detail Order
CREATE TABLE order_items (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    item_id INT,
    package_id INT,
    item_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    notes TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE SET NULL,
    FOREIGN KEY (package_id) REFERENCES packages(package_id) ON DELETE SET NULL
);

-- Tabel Payments
CREATE TABLE payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    payment_method ENUM('transfer', 'credit_card', 'e-wallet', 'cash', 'cod') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_proof VARCHAR(255),
    payment_status ENUM('pending', 'verified', 'failed', 'refunded') DEFAULT 'pending',
    verified_by INT,
    verified_at TIMESTAMP NULL,
    notes TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_order (order_id),
    INDEX idx_status (payment_status)
);

-- Tabel Reviews
CREATE TABLE reviews (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    vendor_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    food_rating INT CHECK (food_rating >= 1 AND food_rating <= 5),
    service_rating INT CHECK (service_rating >= 1 AND service_rating <= 5),
    delivery_rating INT CHECK (delivery_rating >= 1 AND delivery_rating <= 5),
    review_text TEXT,
    images TEXT,
    vendor_response TEXT,
    response_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (vendor_id) REFERENCES vendors(vendor_id) ON DELETE CASCADE,
    INDEX idx_vendor (vendor_id),
    INDEX idx_rating (rating)
);

-- Tabel Cart (Keranjang Belanja)
CREATE TABLE cart (
    cart_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    vendor_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (vendor_id) REFERENCES vendors(vendor_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_vendor (user_id, vendor_id)
);

-- Tabel Cart Items
CREATE TABLE cart_items (
    cart_item_id INT PRIMARY KEY AUTO_INCREMENT,
    cart_id INT NOT NULL,
    item_id INT,
    package_id INT,
    quantity INT NOT NULL DEFAULT 1,
    notes TEXT,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES cart(cart_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(package_id) ON DELETE CASCADE
);

-- Tabel Promo/Discount
CREATE TABLE promos (
    promo_id INT PRIMARY KEY AUTO_INCREMENT,
    promo_code VARCHAR(50) UNIQUE NOT NULL,
    promo_name VARCHAR(100) NOT NULL,
    description TEXT,
    discount_type ENUM('percentage', 'fixed') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    min_order DECIMAL(10,2) DEFAULT 0,
    max_discount DECIMAL(10,2),
    vendor_id INT,
    valid_from DATE NOT NULL,
    valid_until DATE NOT NULL,
    usage_limit INT,
    used_count INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendors(vendor_id) ON DELETE CASCADE,
    INDEX idx_code (promo_code),
    INDEX idx_dates (valid_from, valid_until)
);

-- Tabel Promo Usage
CREATE TABLE promo_usage (
    usage_id INT PRIMARY KEY AUTO_INCREMENT,
    promo_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id INT NOT NULL,
    discount_amount DECIMAL(10,2) NOT NULL,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (promo_id) REFERENCES promos(promo_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
);

-- Tabel Notifications
CREATE TABLE notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('order', 'payment', 'promo', 'system') NOT NULL,
    related_id INT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_read (is_read)
);

-- Tabel Delivery Tracking
CREATE TABLE delivery_tracking (
    tracking_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    driver_name VARCHAR(100),
    driver_phone VARCHAR(20),
    vehicle_number VARCHAR(20),
    status ENUM('assigned', 'picked_up', 'on_the_way', 'arrived', 'delivered') DEFAULT 'assigned',
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    notes TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    INDEX idx_order (order_id)
);

-- Tabel Vendor Operating Hours
CREATE TABLE operating_hours (
    hours_id INT PRIMARY KEY AUTO_INCREMENT,
    vendor_id INT NOT NULL,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    open_time TIME,
    close_time TIME,
    is_closed BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (vendor_id) REFERENCES vendors(vendor_id) ON DELETE CASCADE,
    UNIQUE KEY unique_vendor_day (vendor_id, day_of_week)
);

-- Tabel Wishlist
CREATE TABLE wishlist (
    wishlist_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    item_id INT,
    package_id INT,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(package_id) ON DELETE CASCADE
);

-- ========================================
-- INSERT DATA SAMPLE
-- ========================================

-- Sample Users
INSERT INTO users (username, email, password_hash, full_name, phone, address, role) VALUES
('admin', 'admin@katering.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', '081234567890', 'Jakarta', 'admin'),
('budi_santoso', 'budi@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Budi Santoso', '081234567891', 'Jl. Sudirman No. 123, Jakarta', 'customer'),
('siti_nurhaliza', 'siti@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Siti Nurhaliza', '081234567892', 'Jl. Thamrin No. 45, Jakarta', 'customer'),
('vendor1', 'vendor1@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Vendor Katering Sejahtera', '081234567893', 'Jakarta', 'vendor');

-- Sample Vendors
INSERT INTO vendors (user_id, vendor_name, description, address, city, province, phone, email, rating, total_reviews, status) VALUES
(4, 'Katering Sejahtera', 'Spesialis katering prasmanan dan nasi kotak untuk acara pernikahan, ulang tahun, dan acara kantor', 'Jl. Raya Katering No. 88, Jakarta Selatan', 'Jakarta', 'DKI Jakarta', '0211234567', 'info@kateringsejahtera.com', 4.8, 150, 'active'),
(4, 'Nusantara Catering', 'Katering masakan nusantara dengan cita rasa autentik', 'Jl. Gatot Subroto No. 100, Jakarta Pusat', 'Jakarta', 'DKI Jakarta', '0219876543', 'info@nusantaracatering.com', 4.6, 89, 'active');

-- Sample Categories
INSERT INTO categories (category_name, description) VALUES
('Nasi & Lauk', 'Menu nasi dengan berbagai pilihan lauk pauk'),
('Ayam', 'Berbagai olahan ayam'),
('Seafood', 'Menu dari hasil laut'),
('Sayuran', 'Aneka menu sayuran'),
('Snack & Dessert', 'Camilan dan makanan penutup'),
('Minuman', 'Berbagai jenis minuman');

-- Sample Menu Items
INSERT INTO menu_items (vendor_id, category_id, item_name, description, price, min_order, unit, is_available, preparation_time, spicy_level) VALUES
(1, 1, 'Nasi Putih', 'Nasi putih pulen berkualitas premium', 5000, 10, 'porsi', TRUE, 20, 'tidak pedas'),
(1, 2, 'Ayam Goreng Kremes', 'Ayam goreng dengan kremesan renyah', 15000, 10, 'porsi', TRUE, 30, 'tidak pedas'),
(1, 2, 'Ayam Bakar Madu', 'Ayam bakar dengan saus madu spesial', 18000, 10, 'porsi', TRUE, 40, 'tidak pedas'),
(1, 3, 'Gurame Bakar', 'Ikan gurame bakar bumbu kecap', 35000, 5, 'ekor', TRUE, 45, 'sedang'),
(1, 4, 'Capcay Kuah', 'Sayuran segar dengan kuah spesial', 12000, 10, 'porsi', TRUE, 25, 'tidak pedas'),
(1, 5, 'Lumpia Basah', 'Lumpia basah isi rebung dan sayuran', 8000, 20, 'potong', TRUE, 15, 'tidak pedas'),
(1, 6, 'Es Teh Manis', 'Teh manis dingin segar', 3000, 10, 'gelas', TRUE, 5, 'tidak pedas');

-- Sample Packages
INSERT INTO packages (vendor_id, package_name, description, price_per_person, min_person, package_type, is_available) VALUES
(1, 'Paket Prasmanan Premium', 'Menu lengkap untuk acara formal: Nasi, 3 lauk, 2 sayur, buah, minuman', 75000, 50, 'prasmanan', TRUE),
(1, 'Paket Nasi Box Standard', 'Nasi box lengkap dengan lauk dan minuman', 25000, 20, 'nasi kotak', TRUE),
(1, 'Paket Snack Meeting', 'Snack box untuk meeting atau seminar', 15000, 30, 'snack box', TRUE);

-- Sample Package Items
INSERT INTO package_items (package_id, item_id, quantity) VALUES
(1, 1, 1), (1, 2, 1), (1, 3, 1), (1, 4, 1), (1, 5, 1),
(2, 1, 1), (2, 2, 1), (2, 5, 1), (2, 7, 1);

-- Sample Promos
INSERT INTO promos (promo_code, promo_name, description, discount_type, discount_value, min_order, max_discount, vendor_id, valid_from, valid_until, usage_limit, is_active) VALUES
('WELCOME50', 'Diskon Member Baru', 'Diskon 50rb untuk member baru', 'fixed', 50000, 500000, 50000, NULL, '2026-01-01', '2026-12-31', 100, TRUE),
('DISKON10', 'Diskon 10%', 'Diskon 10% untuk semua menu', 'percentage', 10, 300000, 100000, 1, '2026-01-01', '2026-12-31', 500, TRUE);

-- Sample Orders
INSERT INTO orders (user_id, vendor_id, order_number, order_type, event_type, event_date, event_time, delivery_address, delivery_city, num_people, subtotal, tax, delivery_fee, total_amount, status, payment_status, special_request) VALUES
(2, 1, 'ORD-2026-0001', 'package', 'Pernikahan', '2026-02-15', '11:00:00', 'Gedung Permata, Jl. Merdeka No. 100, Jakarta', 'Jakarta', 200, 15000000, 1500000, 500000, 17000000, 'confirmed', 'paid', 'Tolong datang 1 jam sebelum acara'),
(3, 1, 'ORD-2026-0002', 'custom', 'Meeting Kantor', '2026-01-20', '09:00:00', 'Kantor PT Maju Jaya, Jl. Sudirman Kav. 52, Jakarta', 'Jakarta', 50, 1250000, 125000, 100000, 1475000, 'preparing', 'paid', NULL);

-- Sample Order Items
INSERT INTO order_items (order_id, package_id, item_name, quantity, unit_price, subtotal) VALUES
(1, 1, 'Paket Prasmanan Premium', 200, 75000, 15000000),
(2, 2, 'Paket Nasi Box Standard', 50, 25000, 1250000);

-- Sample Payments
INSERT INTO payments (order_id, payment_method, amount, payment_status, notes) VALUES
(1, 'transfer', 17000000, 'verified', 'Transfer ke BCA'),
(2, 'transfer', 1475000, 'verified', 'Transfer ke Mandiri');

-- Sample Reviews
INSERT INTO reviews (order_id, user_id, vendor_id, rating, food_rating, service_rating, delivery_rating, review_text) VALUES
(1, 2, 1, 5, 5, 5, 5, 'Pelayanan sangat memuaskan! Makanan enak dan tepat waktu. Highly recommended!');

-- ========================================
-- VIEWS (untuk kemudahan query)
-- ========================================

-- View untuk Order Summary
CREATE VIEW v_order_summary AS
SELECT 
    o.order_id,
    o.order_number,
    o.event_date,
    u.full_name AS customer_name,
    u.phone AS customer_phone,
    v.vendor_name,
    o.num_people,
    o.total_amount,
    o.status,
    o.payment_status,
    o.created_at
FROM orders o
JOIN users u ON o.user_id = u.user_id
JOIN vendors v ON o.vendor_id = v.vendor_id;

-- View untuk Vendor Performance
CREATE VIEW v_vendor_performance AS
SELECT 
    v.vendor_id,
    v.vendor_name,
    v.rating,
    v.total_reviews,
    COUNT(DISTINCT o.order_id) AS total_orders,
    SUM(o.total_amount) AS total_revenue,
    AVG(o.total_amount) AS avg_order_value
FROM vendors v
LEFT JOIN orders o ON v.vendor_id = o.vendor_id
WHERE o.status != 'cancelled'
GROUP BY v.vendor_id;

-- View untuk Popular Menu Items
CREATE VIEW v_popular_items AS
SELECT 
    mi.item_id,
    mi.item_name,
    v.vendor_name,
    COUNT(oi.order_item_id) AS order_count,
    SUM(oi.quantity) AS total_quantity_sold,
    SUM(oi.subtotal) AS total_revenue
FROM menu_items mi
JOIN vendors v ON mi.vendor_id = v.vendor_id
LEFT JOIN order_items oi ON mi.item_id = oi.item_id
GROUP BY mi.item_id
ORDER BY order_count DESC;

-- ========================================
-- STORED PROCEDURES
-- ========================================

DELIMITER //

-- Procedure untuk update vendor rating
CREATE PROCEDURE update_vendor_rating(IN p_vendor_id INT)
BEGIN
    UPDATE vendors SET
        rating = (SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE vendor_id = p_vendor_id),
        total_reviews = (SELECT COUNT(*) FROM reviews WHERE vendor_id = p_vendor_id)
    WHERE vendor_id = p_vendor_id;
END //

-- Procedure untuk generate order number
CREATE PROCEDURE generate_order_number(OUT p_order_number VARCHAR(50))
BEGIN
    DECLARE v_count INT;
    DECLARE v_year CHAR(4);
    DECLARE v_number CHAR(6);
    
    SET v_year = YEAR(CURDATE());
    SELECT COUNT(*) + 1 INTO v_count FROM orders WHERE YEAR(created_at) = v_year;
    SET v_number = LPAD(v_count, 6, '0');
    SET p_order_number = CONCAT('ORD-', v_year, '-', v_number);
END //

DELIMITER ;

-- ========================================
-- TRIGGERS
-- ========================================

DELIMITER //

-- Trigger untuk update vendor rating setelah review
CREATE TRIGGER after_review_insert
AFTER INSERT ON reviews
FOR EACH ROW
BEGIN
    CALL update_vendor_rating(NEW.vendor_id);
END //

-- Trigger untuk update promo usage count
CREATE TRIGGER after_promo_usage
AFTER INSERT ON promo_usage
FOR EACH ROW
BEGIN
    UPDATE promos SET used_count = used_count + 1 WHERE promo_id = NEW.promo_id;
END //

DELIMITER ;

-- ========================================
-- INDEX TAMBAHAN untuk optimasi
-- ========================================

CREATE INDEX idx_orders_dates ON orders(event_date, created_at);
CREATE INDEX idx_menu_items_available ON menu_items(is_available, vendor_id);
CREATE INDEX idx_payments_date ON payments(payment_date);
CREATE INDEX idx_reviews_created ON reviews(created_at);

-- Selesai! Database siap digunakan.