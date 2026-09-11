<?php
use App\Helpers\Session;
use App\Helpers\Security;

$user = Session::user();
$currentUri = $_SERVER['REQUEST_URI'] ?? '/';
?>
<aside class="sidebar d-flex flex-column flex-shrink-0" id="appSidebar">
    <div class="p-3 d-flex align-items-center justify-content-between border-bottom border-secondary">
        <a href="/dashboard" class="text-white text-decoration-none d-flex align-items-center gap-2 fs-5 fw-bold">
            <i class="fa-solid fa-boxes-stacked text-primary"></i>
            <span>StockFlow</span>
        </a>
        <button class="btn btn-sm text-white d-lg-none" id="sidebarCloseBtn">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <div class="p-3 border-bottom border-secondary bg-dark bg-opacity-25">
        <div class="d-flex align-items-center gap-2">
            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                <?= strtoupper(substr(Security::escape($user['name'] ?? 'U'), 0, 1)) ?>
            </div>
            <div class="overflow-hidden">
                <div class="fw-semibold text-truncate small"><?= Security::escape($user['name'] ?? 'Utilizador') ?></div>
                <div class="badge bg-secondary text-uppercase" style="font-size: 0.65rem;">
                    <?= Security::escape($user['role'] ?? 'requester') ?>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-pills flex-column mb-auto py-3">
        <li class="nav-item">
            <a href="/dashboard" class="nav-link <?= str_starts_with($currentUri, '/dashboard') ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li>
            <a href="/products" class="nav-link <?= str_starts_with($currentUri, '/products') ? 'active' : '' ?>">
                <i class="fa-solid fa-box-archive"></i>
                <span>Produtos & Stock</span>
            </a>
        </li>

        <?php if ($user && in_array($user['role'], ['admin', 'operator'])): ?>
        <li>
            <a href="/categories" class="nav-link <?= str_starts_with($currentUri, '/categories') ? 'active' : '' ?>">
                <i class="fa-solid fa-tags"></i>
                <span>Categorias</span>
            </a>
        </li>
        <?php endif; ?>

        <li>
            <a href="/requisitions" class="nav-link <?= str_starts_with($currentUri, '/requisitions') ? 'active' : '' ?>">
                <i class="fa-solid fa-clipboard-list"></i>
                <span>Requisições</span>
            </a>
        </li>

        <?php if ($user && $user['role'] === 'admin'): ?>
        <li class="mt-3 border-top border-secondary pt-3">
            <div class="px-3 text-uppercase text-muted fw-bold mb-1" style="font-size: 0.7rem;">Administração</div>
            <a href="/users" class="nav-link <?= str_starts_with($currentUri, '/users') ? 'active' : '' ?>">
                <i class="fa-solid fa-users-gear"></i>
                <span>Utilizadores</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <div class="p-3 border-top border-secondary">
        <a href="/logout" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Sair do Sistema</span>
        </a>
    </div>
</aside>
