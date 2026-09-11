<?php
use App\Helpers\Security;
use App\Helpers\Session;
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm mb-4 px-3 rounded-3">
    <div class="container-fluid p-0">
        <button class="btn btn-outline-secondary d-lg-none me-2" id="sidebarToggleBtn">
            <i class="fa-solid fa-bars"></i>
        </button>
        
        <span class="navbar-brand fw-semibold text-secondary mb-0">
            <?= Security::escape($pageTitle ?? 'Painel Principal') ?>
        </span>

        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="badge bg-light text-dark border px-3 py-2">
                <i class="fa-regular fa-clock me-1 text-primary"></i>
                <?= date('d/m/Y H:i') ?>
            </span>
        </div>
    </div>
</nav>
