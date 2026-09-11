<?php

namespace App\Controllers;

use App\Models\User;
use App\Helpers\Security;
use App\Helpers\Session;

class UserController {
    private function checkAdminAccess(): void {
        if (!Session::isLoggedIn()) {
            header('Location: /login');
            exit;
        }
        $user = Session::user();
        if ($user['role'] !== 'admin') {
            Session::setFlash('danger', 'Acesso negado. Apenas administradores podem gerir utilizadores.');
            header('Location: /dashboard');
            exit;
        }
    }

    public function index(): void {
        $this->checkAdminAccess();
        $users = User::all();
        require __DIR__ . '/../../views/users/index.php';
    }

    public function store(): void {
        $this->checkAdminAccess();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /users');
            exit;
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('danger', 'Token CSRF inválido.');
            header('Location: /users');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = $_POST['role'] ?? 'requester';
        $status = $_POST['status'] ?? 'active';

        if (empty($name) || empty($email) || empty($password)) {
            Session::setFlash('warning', 'Nome, Email e Palavra-passe são obrigatórios.');
            header('Location: /users');
            exit;
        }

        $data = [
            'name' => $name,
            'email' => $email,
            'password' => Security::hashPassword($password),
            'role' => $role,
            'status' => $status
        ];

        if (User::create($data)) {
            Session::setFlash('success', 'Utilizador criado com sucesso!');
        } else {
            Session::setFlash('danger', 'Erro ao criar utilizador. Verifique se o e-mail já existe.');
        }

        header('Location: /users');
        exit;
    }

    public function update(): void {
        $this->checkAdminAccess();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /users');
            exit;
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('danger', 'Token CSRF inválido.');
            header('Location: /users');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = $_POST['role'] ?? 'requester';
        $status = $_POST['status'] ?? 'active';

        if ($id <= 0 || empty($name) || empty($email)) {
            Session::setFlash('warning', 'Dados inválidos.');
            header('Location: /users');
            exit;
        }

        $data = [
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'status' => $status
        ];

        if (!empty($password)) {
            $data['password'] = Security::hashPassword($password);
        }

        if (User::update($id, $data)) {
            Session::setFlash('success', 'Utilizador atualizado com sucesso!');
        } else {
            Session::setFlash('danger', 'Erro ao atualizar utilizador.');
        }

        header('Location: /users');
        exit;
    }

    public function delete(): void {
        $this->checkAdminAccess();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /users');
            exit;
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('danger', 'Token CSRF inválido.');
            header('Location: /users');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $currentUser = Session::user();
        if ($id === $currentUser['id']) {
            Session::setFlash('warning', 'Não pode eliminar a sua própria conta em sessão.');
            header('Location: /users');
            exit;
        }

        try {
            if (User::delete($id)) {
                Session::setFlash('success', 'Utilizador eliminado com sucesso!');
            } else {
                Session::setFlash('danger', 'Erro ao eliminar utilizador.');
            }
        } catch (\PDOException $e) {
            Session::setFlash('danger', 'Não é possível eliminar o utilizador pois possui requisições associadas.');
        }

        header('Location: /users');
        exit;
    }
}
