<?php

namespace App\Controllers;

use App\Models\Requisition;
use App\Models\Product;
use App\Helpers\Security;
use App\Helpers\Session;

class RequisitionController {
    private function checkAccess(): void {
        if (!Session::isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    public function index(): void {
        $this->checkAccess();
        $user = Session::user();
        $requisitions = ($user['role'] === 'requester') 
            ? Requisition::findByUser($user['id']) 
            : Requisition::all();
        $products = Product::all();
        require __DIR__ . '/../../views/requisitions/index.php';
    }

    public function store(): void {
        $this->checkAccess();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /requisitions');
            exit;
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('danger', 'Token CSRF inválido.');
            header('Location: /requisitions');
            exit;
        }

        $user = Session::user();
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);
        $type = $_POST['type'] ?? 'out';
        $notes = trim($_POST['notes'] ?? '');

        if ($productId <= 0 || $quantity <= 0) {
            Session::setFlash('warning', 'Selecione um produto e especifique uma quantidade válida.');
            header('Location: /requisitions');
            exit;
        }

        // Requisitante comum apenas pode solicitar saídas com estado pendente
        if ($user['role'] === 'requester') {
            $type = 'out';
            $status = 'pending';
        } else {
            // Admin e Operador podem registar entradas/saídas diretas
            $status = $_POST['status'] ?? 'completed';
        }

        // Verificar disponibilidade de stock se for saída
        $product = Product::findById($productId);
        if ($type === 'out' && $product && $quantity > $product['stock_quantity']) {
            Session::setFlash('danger', "Quantidade solicitada ({$quantity}) superior ao stock disponível ({$product['stock_quantity']}).");
            header('Location: /requisitions');
            exit;
        }

        $data = [
            'user_id' => $user['id'],
            'product_id' => $productId,
            'type' => $type,
            'quantity' => $quantity,
            'status' => $status,
            'notes' => $notes
        ];

        if (Requisition::create($data)) {
            Session::setFlash('success', 'Requisição registada com sucesso!');
        } else {
            Session::setFlash('danger', 'Erro ao processar requisição.');
        }

        header('Location: /requisitions');
        exit;
    }

    public function updateStatus(): void {
        $this->checkAccess();
        $user = Session::user();
        if ($user['role'] === 'requester') {
            Session::setFlash('danger', 'Não tem autorização para alterar estados de requisições.');
            header('Location: /requisitions');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /requisitions');
            exit;
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('danger', 'Token CSRF inválido.');
            header('Location: /requisitions');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'pending';

        if ($id <= 0) {
            Session::setFlash('warning', 'ID de requisição inválido.');
            header('Location: /requisitions');
            exit;
        }

        if (Requisition::updateStatus($id, $status)) {
            Session::setFlash('success', 'Estado da requisição atualizado!');
        } else {
            Session::setFlash('danger', 'Erro ao atualizar estado da requisição.');
        }

        header('Location: /requisitions');
        exit;
    }
}
