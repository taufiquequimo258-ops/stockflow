<?php

namespace App\Models;

use App\Helpers\Database;
use PDO;

class User {
    public static function findByEmail(string $email): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, name, email, role, status, created_at FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function all(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT id, name, email, role, status, created_at FROM users ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public static function create(array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['password'],
            $data['role'] ?? 'requester',
            $data['status'] ?? 'active'
        ]);
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        if (!empty($data['password'])) {
            $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ?, status = ? WHERE id = ?");
            return $stmt->execute([$data['name'], $data['email'], $data['password'], $data['role'], $data['status'], $id]);
        } else {
            $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, role = ?, status = ? WHERE id = ?");
            return $stmt->execute([$data['name'], $data['email'], $data['role'], $data['status'], $id]);
        }
    }

    public static function delete(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
