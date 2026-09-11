<?php
use App\Helpers\Security;
use App\Helpers\Session;

$pageTitle = 'Gestão de Categorias';
$csrfToken = Security::generateCsrfToken();
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
            <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-tags me-2 text-primary"></i>Categorias de Produtos</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateCategory">
                <i class="fa-solid fa-plus me-2"></i>Nova Categoria
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Nome da Categoria</th>
                            <th>Descrição</th>
                            <th>Data de Registo</th>
                            <th class="text-end" style="width: 150px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Nenhuma categoria cadastrada.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td class="fw-bold">#<?= $cat['id'] ?></td>
                                    <td class="fw-semibold"><?= Security::escape($cat['name']) ?></td>
                                    <td class="text-muted"><?= Security::escape($cat['description'] ?? 'Sem descrição') ?></td>
                                    <td><?= date('d/m/Y', strtotime($cat['created_at'])) ?></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary me-1" 
                                                onclick="editCategory(<?= $cat['id'] ?>, '<?= Security::escape(addslashes($cat['name'])) ?>', '<?= Security::escape(addslashes($cat['description'] ?? '')) ?>')">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?= $cat['id'] ?>, '<?= Security::escape(addslashes($cat['name'])) ?>')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
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

<!-- Modal Criar Categoria -->
<div class="modal fade" id="modalCreateCategory" tabindex="-1">
    <div class="modal-dialog">
        <form action="/categories/store" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::escape($csrfToken) ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus me-2 text-primary"></i>Nova Categoria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="create_name" class="form-label fw-semibold">Nome da Categoria *</label>
                        <input type="text" class="form-control" id="create_name" name="name" required placeholder="ex: Material de Escritório">
                    </div>
                    <div class="mb-3">
                        <label for="create_description" class="form-label fw-semibold">Descrição</label>
                        <textarea class="form-control" id="create_description" name="description" rows="3" placeholder="Descrição opcional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-2"></i>Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Categoria -->
<div class="modal fade" id="modalEditCategory" tabindex="-1">
    <div class="modal-dialog">
        <form action="/categories/update" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::escape($csrfToken) ?>">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen me-2 text-primary"></i>Editar Categoria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label fw-semibold">Nome da Categoria *</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label fw-semibold">Descrição</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check me-2"></i>Atualizar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Confirmar Eliminação -->
<div class="modal fade" id="modalDeleteCategory" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="/categories/delete" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::escape($csrfToken) ?>">
            <input type="hidden" name="id" id="delete_id">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>Confirmar Eliminação</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    Tem a certeza que deseja eliminar a categoria <strong id="delete_name"></strong>? Esta ação não pode ser desfeita.
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash me-2"></i>Sim, Eliminar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function editCategory(id, name, description) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_description').value = description;
    new bootstrap.Modal(document.getElementById('modalEditCategory')).show();
}

function confirmDelete(id, name) {
    document.getElementById('delete_id').value = id;
    document.getElementById('delete_name').textContent = name;
    new bootstrap.Modal(document.getElementById('modalDeleteCategory')).show();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
