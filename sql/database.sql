-- Script SQL para crear la base de datos de la tienda de boxeo
CREATE DATABASE IF NOT EXISTS boxing_store;
USE boxing_store;

-- Tabla de usuarios
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de categorías
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT
);

-- Tabla de productos
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INT,
    image VARCHAR(255),
    stock INT DEFAULT 0,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insertar categorías por defecto
INSERT INTO categories (name, description) VALUES
('Guantes', 'Guantes de boxeo para entrenamiento y competencia'),
('Sacos', 'Sacos de boxeo y peras de velocidad'),
('Protecciones', 'Protectores bucales, cascos y protecciones'),
('Vendas', 'Vendas y protecciones para manos'),
('Calzado', 'Botas y zapatos de boxeo'),
('Ropa', 'Shorts, camisetas y ropa deportiva');

-- Insertar usuario administrador por defecto (password: admin123)
INSERT INTO users (username, email, password, full_name, phone, address) VALUES
('admin', 'admin@boxingstore.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', '555-0123', 'Calle Principal 123');

-- Insertar algunos productos de ejemplo
INSERT INTO products (name, description, price, category_id, stock, user_id) VALUES
('Guantes Everlast Pro', 'Guantes profesionales de cuero genuino', 89.99, 1, 25, 1),
('Saco de Boxeo Heavy Bag', 'Saco pesado de 100 libras para entrenamiento', 199.99, 2, 10, 1),
('Casco Protector Title', 'Casco de entrenamiento con protección completa', 79.99, 3, 15, 1),
('Vendas Mexicanas', 'Vendas elásticas de 4 metros', 12.99, 4, 50, 1),
('Botas de Boxeo Nike', 'Botas altas profesionales', 149.99, 5, 8, 1);
