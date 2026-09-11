<?php

namespace App\Controllers;

use App\Models\Category;
use App\Helpers\Security;
use App\Helpers\Session;

class CategoryController {
    private function checkAccess(): void {
        if (!Session::isLoggedIn()) {
            header('Location: /login');
            exit;
        }
        $user = Session::user();
        if ($user['role'] === 'requester') {
            Session::setFlash('danger', 'Acesso negado. Não tem permissão para gerir categorias.');
            header('Location: /dashboard');
            exit;
        }
    }

    public function index(): void {
        $this->checkAccess();
        $categories = Category::all();
        require __DIR__ . '/../../views/categories/index.php';
    }

    public function store(): void {
        $this->checkAccess();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /categories');
            exit;
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('danger', 'Token de segurança inválido.');
            header('Location: /categories');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            Session::setFlash('warning', 'O nome da categoria é obrigatório.');
            header('Location: /categories');
            exit;
        }

        if (Category::create(['name' => $name, 'description' => $description])) {
            Session::setFlash('success', 'Categoria criada com sucesso!');
        } else {
            Session::setFlash('danger', 'Erro ao criar categoria.');
        }

        header('Location: /categories');
        exit;
    }

    public function update(): void {
        $this->checkAccess();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /categories');
            exit;
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('danger', 'Token de segurança inválido.');
            header('Location: /categories');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($id <= 0 || empty($name)) {
            Session::setFlash('warning', 'Dados inválidos para atualização.');
            header('Location: /categories');
            exit;
        }

        if (Category::update($id, ['name' => $name, 'description' => $description])) {
            Session::setFlash('success', 'Categoria atualizada com sucesso!');
        } else {
            Session::setFlash('danger', 'Erro ao atualizar categoria.');
        }

        header('Location: /categories');
        exit;
    }

    public function delete(): void {
        $this->checkAccess();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /categories');
            exit;
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('danger', 'Token de segurança inválido.');
            header('Location: /categories');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            Session::setFlash('warning', 'ID inválido para eliminação.');
            header('Location: /categories');
            exit;
        }

        try {
            if (Category::delete($id)) {
                Session::setFlash('success', 'Categoria eliminada com sucesso!');
            } else {
                Session::setFlash('danger', 'Erro ao eliminar categoria.');
            }
        } catch (\PDOException $e) {
            Session::setFlash('danger', 'Não é possível eliminar esta categoria pois existem produtos associados a ela.');
        }

        header('Location: /categories');
        exit;
    }
}
