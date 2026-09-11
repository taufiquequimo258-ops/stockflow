<?php
use App\Helpers\Security;
use App\Helpers\Session;

$pageTitle = 'Dashboard';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
?>

<div class="main-content flex-grow-1">
    <?php require __DIR__ . '/../layouts/navbar.php'; ?>

    <!-- Flash Messages -->
    <?php if ($msg = Session::getFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?= Security::escape($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($msg = Session::getFlash('danger')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i><?= Security::escape($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Cards de Métricas -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-stat bg-white p-3 border-start border-primary border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">Total de Produtos</div>
                        <div class="fs-3 fw-bold text-dark"><?= $totalProducts ?></div>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="fa-solid fa-box fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-stat bg-white p-3 border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">Alerta de Stock Mínimo</div>
                        <div class="fs-3 fw-bold text-warning"><?= $lowStockCount ?></div>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                        <i class="fa-solid fa-triangle-exclamation fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-stat bg-white p-3 border-start border-info border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">Requisições Pendentes</div>
                        <div class="fs-3 fw-bold text-info"><?= $pendingRequisitions ?></div>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                        <i class="fa-solid fa-clock-rotate-left fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-stat bg-white p-3 border-start border-success border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">Categorias Ativas</div>
                        <div class="fs-3 fw-bold text-success"><?= $totalCategories ?></div>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                        <i class="fa-solid fa-tags fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabelas Resumo -->
    <div class="row g-4">
        <!-- Produtos em Alerta de Stock -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="fw-bold m-0 text-danger">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>Produtos Necessitando de Reposição
                    </h6>
                    <a href="/products" class="btn btn-sm btn-outline-danger">Ver Todos</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Produto</th>
                                    <th class="text-center">Qtd Atual</th>
                                    <th class="text-center">Stock Mín.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($lowStockProducts)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fa-solid fa-circle-check text-success me-2"></i>Todos os produtos possuem stock suficiente.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($lowStockProducts as $prod): ?>
                                        <tr>
                                            <td class="fw-bold"><?= Security::escape($prod['code']) ?></td>
                                            <td><?= Security::escape($prod['name']) ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-danger"><?= $prod['stock_quantity'] ?></span>
                                            </td>
                                            <td class="text-center text-muted"><?= $prod['min_stock'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requisições Recentes -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="fw-bold m-0 text-dark">
                        <i class="fa-solid fa-clipboard-list me-2"></i>Últimas Requisições
                    </h6>
                    <a href="/requisitions" class="btn btn-sm btn-outline-primary">Ver Todas</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº Requisição</th>
                                    <th>Produto</th>
                                    <th>Tipo</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentRequisitions)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Nenhuma requisição registada.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentRequisitions as $req): ?>
                                        <tr>
                                            <td class="fw-bold"><?= Security::escape($req['req_number']) ?></td>
                                            <td><?= Security::escape($req['product_name']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $req['type'] === 'in' ? 'success' : 'primary' ?>">
                                                    <?= $req['type'] === 'in' ? 'Entrada' : 'Saída' ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                $statusBadges = [
                                                    'pending' => 'bg-warning text-dark',
                                                    'approved' => 'bg-info text-white',
                                                    'rejected' => 'bg-danger text-white',
                                                    'completed' => 'bg-success text-white'
                                                ];
                                                $badge = $statusBadges[$req['status']] ?? 'bg-secondary';
                                                ?>
                                                <span class="badge <?= $badge ?>"><?= ucfirst($req['status']) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
