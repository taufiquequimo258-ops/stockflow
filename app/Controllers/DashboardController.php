<?php

namespace App\Controllers;

use App\Helpers\Session;
use App\Models\Product;
use App\Models\Requisition;
use App\Models\Category;

class DashboardController {
    public function index(): void {
        if (!Session::isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        $totalProducts = count(Product::all());
        $lowStockCount = Product::countLowStock();
        $pendingRequisitions = Requisition::countPending();
        $totalCategories = count(Category::all());
        $lowStockProducts = Product::getLowStock();

        $user = Session::user();
        $recentRequisitions = ($user['role'] === 'requester') 
            ? Requisition::findByUser($user['id']) 
            : Requisition::all();

        // Limita a 5 recentes para o dashboard
        $recentRequisitions = array_slice($recentRequisitions, 0, 5);

        require __DIR__ . '/../../views/dashboard/index.php';
    }
}
