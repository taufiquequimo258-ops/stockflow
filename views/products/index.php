<?php
use App\Helpers\Security;
use App\Helpers\Session;

$pageTitle = 'Gestão de Produtos & Stock';
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

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-box-archive me-2 text-primary"></i>Inventário de Produtos</h5>
            
            <div class="d-flex align-items-center gap-2">
                <!-- Formulário de Pesquisa -->
                <form action="/products" method="GET" class="d-flex align-items-center">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Pesquisar por código, produto..." value="<?= Security::escape($_GET['search'] ?? '') ?>">
                        <button type="submit" class="btn btn-outline-secondary"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                </form>

                <?php if ($user && in_array($user['role'], ['admin', 'operator'])): ?>
                    <button class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#modalCreateProduct">
                        <i class="fa-solid fa-plus me-2"></i>Novo Produto
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Nome do Produto</th>
                            <th>Categoria</th>
                            <th class="text-end">Preço Unitário</th>
                            <th class="text-center">Stock Atual</th>
                            <th class="text-center">Stock Mín.</th>
                            <?php if ($user && in_array($user['role'], ['admin', 'operator'])): ?>
                                <th class="text-end" style="width: 130px;">Ações</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Nenhum produto encontrado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $prod): ?>
                                <tr>
                                    <td class="fw-bold text-primary"><?= Security::escape($prod['code']) ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= Security::escape($prod['name']) ?></div>
                                        <small class="text-muted"><?= Security::escape($prod['description'] ?? '') ?></small>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?= Security::escape($prod['category_name']) ?></span></td>
                                    <td class="text-end fw-semibold"><?= number_format($prod['unit_price'], 2, ',', '.') ?> MT</td>
                                    <td class="text-center">
                                        <?php if ($prod['stock_quantity'] <= $prod['min_stock']): ?>
                                            <span class="badge bg-danger p-2" title="Alerta de Stock Mínimo!">
                                                <i class="fa-solid fa-triangle-exclamation me-1"></i><?= $prod['stock_quantity'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success p-2"><?= $prod['stock_quantity'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center text-muted"><?= $prod['min_stock'] ?></td>

                                    <?php if ($user && in_array($user['role'], ['admin', 'operator'])): ?>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-primary me-1" 
                                                    onclick="editProduct(<?= htmlspecialchars(json_encode($prod), ENT_QUOTES, 'UTF-8') ?>)">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <?php if ($user['role'] === 'admin'): ?>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="confirmDeleteProduct(<?= $prod['id'] ?>, '<?= Security::escape(addslashes($prod['name'])) ?>')">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
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

<?php if ($user && in_array($user['role'], ['admin', 'operator'])): ?>
<!-- Modal Criar Produto -->
<div class="modal fade" id="modalCreateProduct" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="/products/store" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::escape($csrfToken) ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus me-2 text-primary"></i>Registar Novo Produto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="create_code" class="form-label fw-semibold">Código SKU *</label>
                            <input type="text" class="form-control" id="create_code" name="code" required placeholder="ex: MAT-101">
                        </div>
                        <div class="col-12 col-md-8">
                            <label for="create_name" class="form-label fw-semibold">Nome do Produto *</label>
                            <input type="text" class="form-control" id="create_name" name="name" required placeholder="ex: Papel A4 80g">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="create_category_id" class="form-label fw-semibold">Categoria *</label>
                            <select class="form-select" id="create_category_id" name="category_id" required>
                                <option value="">-- Selecione uma Categoria --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= Security::escape($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="create_unit_price" class="form-label fw-semibold">Preço Unitário (MT)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="create_unit_price" name="unit_price" value="0.00">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="create_stock_quantity" class="form-label fw-semibold">Quantidade Inicial em Stock</label>
                            <input type="number" min="0" class="form-control" id="create_stock_quantity" name="stock_quantity" value="0">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="create_min_stock" class="form-label fw-semibold">Stock Mínimo (Alerta)</label>
                            <input type="number" min="0" class="form-control" id="create_min_stock" name="min_stock" value="5">
                        </div>
                        <div class="col-12">
                            <label for="create_description" class="form-label fw-semibold">Descrição / Observações</label>
                            <textarea class="form-control" id="create_description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-2"></i>Guardar Produto</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Produto -->
<div class="modal fade" id="modalEditProduct" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="/products/update" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::escape($csrfToken) ?>">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen me-2 text-primary"></i>Editar Produto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="edit_code" class="form-label fw-semibold">Código SKU *</label>
                            <input type="text" class="form-control" id="edit_code" name="code" required>
                        </div>
                        <div class="col-12 col-md-8">
                            <label for="edit_name" class="form-label fw-semibold">Nome do Produto *</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="edit_category_id" class="form-label fw-semibold">Categoria *</label>
                            <select class="form-select" id="edit_category_id" name="category_id" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= Security::escape($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="edit_unit_price" class="form-label fw-semibold">Preço Unitário (MT)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="edit_unit_price" name="unit_price">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="edit_stock_quantity" class="form-label fw-semibold">Quantidade em Stock</label>
                            <input type="number" min="0" class="form-control" id="edit_stock_quantity" name="stock_quantity">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="edit_min_stock" class="form-label fw-semibold">Stock Mínimo (Alerta)</label>
                            <input type="number" min="0" class="form-control" id="edit_min_stock" name="min_stock">
                        </div>
                        <div class="col-12">
                            <label for="edit_description" class="form-label fw-semibold">Descrição</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check me-2"></i>Atualizar Produto</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php if ($user && $user['role'] === 'admin'): ?>
<!-- Modal Confirmar Eliminação de Produto -->
<div class="modal fade" id="modalDeleteProduct" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="/products/delete" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::escape($csrfToken) ?>">
            <input type="hidden" name="id" id="delete_prod_id">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>Confirmar Eliminação</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    Tem a certeza que deseja eliminar o produto <strong id="delete_prod_name"></strong> do inventário?
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash me-2"></i>Eliminar Produto</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
function editProduct(prod) {
    document.getElementById('edit_id').value = prod.id;
    document.getElementById('edit_code').value = prod.code;
    document.getElementById('edit_name').value = prod.name;
    document.getElementById('edit_category_id').value = prod.category_id;
    document.getElementById('edit_unit_price').value = prod.unit_price;
    document.getElementById('edit_stock_quantity').value = prod.stock_quantity;
    document.getElementById('edit_min_stock').value = prod.min_stock;
    document.getElementById('edit_description').value = prod.description || '';
    new bootstrap.Modal(document.getElementById('modalEditProduct')).show();
}

function confirmDeleteProduct(id, name) {
    document.getElementById('delete_prod_id').value = id;
    document.getElementById('delete_prod_name').textContent = name;
    new bootstrap.Modal(document.getElementById('modalDeleteProduct')).show();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
