-- ===================================================
-- StockFlow - Database Schema (MySQL / MariaDB)
-- Disciplina: Desenvolvimento de Aplicativos Web Empresariais
-- ===================================================

CREATE DATABASE IF NOT EXISTS stockflow_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE stockflow_db;

-- ---------------------------------------------------
-- 1. Tabela de Utilizadores (users)
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'operator', 'requester') NOT NULL DEFAULT 'requester',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------
-- 2. Tabela de Categorias de Produtos (categories)
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------
-- 3. Tabela de Produtos / Itens de Inventário (products)
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    code VARCHAR(30) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock_quantity INT NOT NULL DEFAULT 0,
    min_stock INT NOT NULL DEFAULT 5,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------
-- 4. Tabela de Requisições / Movimentos de Stock (requisitions)
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS requisitions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    req_number VARCHAR(20) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    type ENUM('in', 'out') NOT NULL DEFAULT 'out',
    quantity INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed') NOT NULL DEFAULT 'pending',
    notes TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ===================================================
-- DADOS INICIAIS PARA TESTES (SEEDS)
-- Senha padrão para os utilizadores: "password123"
-- (bcrypt: $2y$10$e0MYzXyjpJS7Pd0RVvHwHe1V.g5eR6FwzV.Kq4NnQ4a1fGZ4xWlG6)
-- ===================================================

INSERT INTO users (name, email, password, role) VALUES
('Administrador do Sistema', 'admin@stockflow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Operador de Armazém', 'operador@stockflow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'operator'),
('Maria Requisitante', 'maria@empresa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'requester');

INSERT INTO categories (name, description) VALUES
('Material de Escritório', 'Papelaria, canetas, grampeadores e organizadores'),
('Equipamentos de TI', 'Computadores, monitores, periféricos e cabos'),
('Consumíveis de Impressão', 'Toners, cartuchos de tinta e tambores de imagem');

INSERT INTO products (category_id, code, name, description, unit_price, stock_quantity, min_stock) VALUES
(1, 'MAT-001', 'Papel A4 80g (Caixa 5 Resmas)', 'Caixa de papel de alta qualidade para impressão', 22.50, 45, 10),
(1, 'MAT-002', 'Caneta Esferográfica Azul (Caixa 50u)', 'Canetas esferográficas azuis ponta média', 12.00, 3, 5),
(2, 'TI-101', 'Rato Óptico USB Ergonomico', 'Rato com fio conexão USB', 15.00, 20, 5),
(2, 'TI-102', 'Teclado Multimédia USB PT', 'Teclado padrão português com teclas de atalho', 18.50, 12, 4),
(3, 'IMP-501', 'Toner HP LaserJet Black CF283A', 'Cartucho de toner preto original HP', 55.00, 2, 3);

INSERT INTO requisitions (req_number, user_id, product_id, type, quantity, status, notes) VALUES
('REQ-2026-0001', 3, 1, 'out', 2, 'completed', 'Requisição para o departamento financeiro'),
('REQ-2026-0002', 3, 2, 'out', 1, 'pending', 'Urgente para a receção'),
('REQ-2026-0003', 2, 5, 'in', 5, 'completed', 'Entrada de fornecedor - Guia 9981');
