<?php
use App\Helpers\Security;
use App\Helpers\Session;

$pageTitle = 'Gestão de Requisições';
$csrfToken = Security::generateCsrfToken();
$user = Session::user();
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
?>

<div class="main-content flex-grow-1">
    <?php require __DIR__ . '/../layouts/navbar.php'; ?>

    <!-- Alertas Flash -->
    <?php if ($msg = Session::getFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?= Security::escape($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($msg = Session::getFlash('danger')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i><?= Security::escape($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($msg = Session::getFlash('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i><?= Security::escape($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-clipboard-list me-2 text-primary"></i>Requisições de Stock</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateRequisition">
                <i class="fa-solid fa-plus me-2"></i>Nova Requisição
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nº Requisição</th>
                            <th>Requisitante</th>
                            <th>Produto</th>
                            <th>Tipo</th>
                            <th class="text-center">Qtd</th>
                            <th class="text-center">Estado</th>
                            <th>Data</th>
                            <?php if ($user && in_array($user['role'], ['admin', 'operator'])): ?>
                                <th class="text-end" style="width: 140px;">Ações</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requisitions)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Nenhuma requisição registada.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($requisitions as $req): ?>
                                <tr>
                                    <td class="fw-bold text-primary"><?= Security::escape($req['req_number']) ?></td>
                                    <td><?= Security::escape($req['user_name']) ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= Security::escape($req['product_name']) ?></div>
                                        <small class="text-muted">SKU: <?= Security::escape($req['product_code']) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $req['type'] === 'in' ? 'success' : 'primary' ?>">
                                            <?= $req['type'] === 'in' ? 'Entrada' : 'Saída' ?>
                                        </span>
                                    </td>
                                    <td class="text-center fw-bold"><?= $req['quantity'] ?></td>
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
                                        <span class="badge <?= $badge ?> p-2"><?= ucfirst($req['status']) ?></span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($req['created_at'])) ?></td>

                                    <?php if ($user && in_array($user['role'], ['admin', 'operator'])): ?>
                                        <td class="text-end">
                                            <?php if ($req['status'] === 'pending'): ?>
                                                <button class="btn btn-sm btn-outline-success me-1" title="Aprovar/Concluir"
                                                        onclick="updateStatus(<?= $req['id'] ?>, 'approved')">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" title="Rejeitar"
                                                        onclick="updateStatus(<?= $req['id'] ?>, 'rejected')">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted small"><i class="fa-solid fa-lock"></i> Finalizada</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Criar Requisição -->
<div class="modal fade" id="modalCreateRequisition" tabindex="-1">
    <div class="modal-dialog">
        <form action="/requisitions/store" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::escape($csrfToken) ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus me-2 text-primary"></i>Solicitar / Movimentar Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="product_id" class="form-label fw-semibold">Produto *</label>
                        <select class="form-select" id="product_id" name="product_id" required>
                            <option value="">-- Selecione o Produto --</option>
                            <?php foreach ($products as $prod): ?>
                                <option value="<?= $prod['id'] ?>">
                                    <?= Security::escape($prod['code']) ?> - <?= Security::escape($prod['name']) ?> (Stock: <?= $prod['stock_quantity'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if ($user && in_array($user['role'], ['admin', 'operator'])): ?>
                        <div class="mb-3">
                            <label for="type" class="form-label fw-semibold">Tipo de Movimento *</label>
                            <select class="form-select" id="type" name="type">
                                <option value="out">Saída (Requisição / Consumo)</option>
                                <option value="in">Entrada (Reposição de Stock)</option>
                            </select>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="type" value="out">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="quantity" class="form-label fw-semibold">Quantidade *</label>
                        <input type="number" min="1" class="form-control" id="quantity" name="quantity" value="1" required>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">Observações / Justificação</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Motivo da requisição..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane me-2"></i>Submeter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Form Escondido para Atualização de Estado -->
<form id="formStatusUpdate" action="/requisitions/status" method="POST" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?= Security::escape($csrfToken) ?>">
    <input type="hidden" name="id" id="status_req_id">
    <input type="hidden" name="status" id="status_req_val">
</form>

<script>
function updateStatus(id, status) {
    if (confirm('Deseja realmente alterar o estado desta requisição para ' + status + '?')) {
        document.getElementById('status_req_id').value = id;
        document.getElementById('status_req_val').value = status;
        document.getElementById('formStatusUpdate').submit();
    }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
