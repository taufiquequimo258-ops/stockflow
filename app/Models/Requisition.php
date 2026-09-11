<?php

namespace App\Models;

use App\Helpers\Database;
use PDO;

class Requisition {
    public static function all(): array {
        $db = Database::getConnection();
        $sql = "SELECT r.*, u.name as user_name, p.name as product_name, p.code as product_code 
                FROM requisitions r 
                INNER JOIN users u ON r.user_id = u.id 
                INNER JOIN products p ON r.product_id = p.id 
                ORDER BY r.created_at DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }

    public static function findByUser(int $userId): array {
        $db = Database::getConnection();
        $sql = "SELECT r.*, u.name as user_name, p.name as product_name, p.code as product_code 
                FROM requisitions r 
                INNER JOIN users u ON r.user_id = u.id 
                INNER JOIN products p ON r.product_id = p.id 
                WHERE r.user_id = ? 
                ORDER BY r.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $sql = "SELECT r.*, u.name as user_name, p.name as product_name, p.code as product_code 
                FROM requisitions r 
                INNER JOIN users u ON r.user_id = u.id 
                INNER JOIN products p ON r.product_id = p.id 
                WHERE r.id = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        $req = $stmt->fetch();
        return $req ?: null;
    }

    public static function countPending(): int {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT COUNT(*) FROM requisitions WHERE status = 'pending'");
        return (int)$stmt->fetchColumn();
    }

    public static function generateReqNumber(): string {
        $year = date('Y');
        $db = Database::getConnection();
        $stmt = $db->query("SELECT COUNT(*) FROM requisitions");
        $count = (int)$stmt->fetchColumn() + 1;
        return sprintf("REQ-%s-%04d", $year, $count);
    }

    public static function create(array $data): bool {
        $db = Database::getConnection();
        $reqNum = self::generateReqNumber();
        $stmt = $db->prepare("INSERT INTO requisitions (req_number, user_id, product_id, type, quantity, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $success = $stmt->execute([
            $reqNum,
            $data['user_id'],
            $data['product_id'],
            $data['type'] ?? 'out',
            $data['quantity'],
            $data['status'] ?? 'pending',
            $data['notes'] ?? null
        ]);

        // Se for criada como concluída/aprovada, atualiza o stock imediatamente
        if ($success && ($data['status'] ?? 'pending') === 'completed') {
            $qtyChange = ($data['type'] === 'in') ? $data['quantity'] : -$data['quantity'];
            Product::updateStock($data['product_id'], $qtyChange);
        }

        return $success;
    }

    public static function updateStatus(int $id, string $status): bool {
        $db = Database::getConnection();
        $req = self::findById($id);
        if (!$req) return false;

        $oldStatus = $req['status'];
        $stmt = $db->prepare("UPDATE requisitions SET status = ? WHERE id = ?");
        $success = $stmt->execute([$status, $id]);

        // Atualiza stock ao transicionar para 'approved' ou 'completed'
        if ($success && ($status === 'approved' || $status === 'completed') && $oldStatus !== 'completed' && $oldStatus !== 'approved') {
            $qtyChange = ($req['type'] === 'in') ? $req['quantity'] : -$req['quantity'];
            Product::updateStock($req['product_id'], $qtyChange);
        }

        return $success;
    }

    public static function delete(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM requisitions WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
