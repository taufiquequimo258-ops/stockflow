<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Helpers\Security;
use App\Helpers\Session;

class ProductController {
    private function checkAccess(): void {
        if (!Session::isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    public function index(): void {
        $this->checkAccess();
        $search = trim($_GET['search'] ?? '');
        $products = !empty($search) ? Product::search($search) : Product::all();
        $categories = Category::all();
        require __DIR__ . '/../../views/products/index.php';
    }

    public function store(): void {
        $this->checkAccess();
        $user = Session::user();
        if ($user['role'] === 'requester') {
            Session::setFlash('danger', 'Sem permissão para adicionar produtos.');
            header('Location: /products');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /products');
            exit;
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('danger', 'Token CSRF inválido.');
            header('Location: /products');
            exit;
        }

        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $unitPrice = (float)($_POST['unit_price'] ?? 0.00);
        $stockQuantity = (int)($_POST['stock_quantity'] ?? 0);
        $minStock = (int)($_POST['min_stock'] ?? 5);
        $description = trim($_POST['description'] ?? '');

        if (empty($code) || empty($name) || $categoryId <= 0) {
            Session::setFlash('warning', 'Código, Nome e Categoria são campos obrigatórios.');
            header('Location: /products');
            exit;
        }

        $data = [
            'code' => $code,
            'name' => $name,
            'category_id' => $categoryId,
            'unit_price' => $unitPrice,
            'stock_quantity' => $stockQuantity,
            'min_stock' => $minStock,
            'description' => $description
        ];

        if (Product::create($data)) {
            Session::setFlash('success', 'Produto adicionado ao inventário com sucesso!');
        } else {
            Session::setFlash('danger', 'Erro ao registar produto. Verifique se o código já existe.');
        }

        header('Location: /products');
        exit;
    }

    public function update(): void {
        $this->checkAccess();
        $user = Session::user();
        if ($user['role'] === 'requester') {
            Session::setFlash('danger', 'Sem permissão para editar produtos.');
            header('Location: /products');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /products');
            exit;
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('danger', 'Token CSRF inválido.');
            header('Location: /products');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $code = trim($_POST['code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $unitPrice = (float)($_POST['unit_price'] ?? 0.00);
        $stockQuantity = (int)($_POST['stock_quantity'] ?? 0);
        $minStock = (int)($_POST['min_stock'] ?? 5);
        $description = trim($_POST['description'] ?? '');

        if ($id <= 0 || empty($code) || empty($name) || $categoryId <= 0) {
            Session::setFlash('warning', 'Preencha corretamente os dados do produto.');
            header('Location: /products');
            exit;
        }

        $data = [
            'code' => $code,
            'name' => $name,
            'category_id' => $categoryId,
            'unit_price' => $unitPrice,
            'stock_quantity' => $stockQuantity,
            'min_stock' => $minStock,
            'description' => $description
        ];

        if (Product::update($id, $data)) {
            Session::setFlash('success', 'Produto atualizado com sucesso!');
        } else {
            Session::setFlash('danger', 'Erro ao atualizar produto.');
        }

        header('Location: /products');
        exit;
    }

    public function delete(): void {
        $this->checkAccess();
        $user = Session::user();
        if ($user['role'] !== 'admin') {
            Session::setFlash('danger', 'Apenas o Administrador pode eliminar produtos.');
            header('Location: /products');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /products');
            exit;
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('danger', 'Token CSRF inválido.');
            header('Location: /products');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            Session::setFlash('warning', 'ID inválido.');
            header('Location: /products');
            exit;
        }

        try {
            if (Product::delete($id)) {
                Session::setFlash('success', 'Produto removido do inventário!');
            } else {
                Session::setFlash('danger', 'Erro ao eliminar produto.');
            }
        } catch (\PDOException $e) {
            Session::setFlash('danger', 'Não é possível eliminar o produto pois tem requisições associadas.');
        }

        header('Location: /products');
        exit;
    }
}
