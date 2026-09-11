<?php

namespace App\Models;

use App\Helpers\Database;
use PDO;

class Product {
    public static function all(): array {
        $db = Database::getConnection();
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                INNER JOIN categories c ON p.category_id = c.id 
                ORDER BY p.name ASC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                INNER JOIN categories c ON p.category_id = c.id 
                WHERE p.id = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        $prod = $stmt->fetch();
        return $prod ?: null;
    }

    public static function search(string $term): array {
        $db = Database::getConnection();
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                INNER JOIN categories c ON p.category_id = c.id 
                WHERE p.name LIKE ? OR p.code LIKE ? OR c.name LIKE ? 
                ORDER BY p.name ASC";
        $stmt = $db->prepare($sql);
        $like = "%{$term}%";
        $stmt->execute([$like, $like, $like]);
        return $stmt->fetchAll();
    }

    public static function countLowStock(): int {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT COUNT(*) FROM products WHERE stock_quantity <= min_stock");
        return (int)$stmt->fetchColumn();
    }

    public static function getLowStock(): array {
        $db = Database::getConnection();
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                INNER JOIN categories c ON p.category_id = c.id 
                WHERE p.stock_quantity <= p.min_stock 
                ORDER BY p.stock_quantity ASC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }

    public static function create(array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO products (category_id, code, name, description, unit_price, stock_quantity, min_stock) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['category_id'],
            $data['code'],
            $data['name'],
            $data['description'] ?? null,
            $data['unit_price'] ?? 0.00,
            $data['stock_quantity'] ?? 0,
            $data['min_stock'] ?? 5
        ]);
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE products SET category_id = ?, code = ?, name = ?, description = ?, unit_price = ?, stock_quantity = ?, min_stock = ? WHERE id = ?");
        return $stmt->execute([
            $data['category_id'],
            $data['code'],
            $data['name'],
            $data['description'] ?? null,
            $data['unit_price'] ?? 0.00,
            $data['stock_quantity'] ?? 0,
            $data['min_stock'] ?? 5,
            $id
        ]);
    }

    public static function updateStock(int $id, int $change): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");
        return $stmt->execute([$change, $id]);
    }

    public static function delete(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
