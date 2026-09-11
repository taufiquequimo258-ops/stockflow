<?php
use App\Helpers\Security;
use App\Helpers\Session;

$pageTitle = 'Gestão de Utilizadores';
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

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-users-gear me-2 text-primary"></i>Utilizadores do Sistema</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateUser">
                <i class="fa-solid fa-plus me-2"></i>Novo Utilizador
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Perfil / Regra (RBAC)</th>
                            <th class="text-center">Estado</th>
                            <th>Data de Cadastro</th>
                            <th class="text-end" style="width: 140px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Nenhum utilizador encontrado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td class="fw-semibold"><?= Security::escape($u['name']) ?></td>
                                    <td><?= Security::escape($u['email']) ?></td>
                                    <td>
                                        <?php
                                        $roleBadges = [
                                            'admin' => 'bg-danger text-white',
                                            'operator' => 'bg-primary text-white',
                                            'requester' => 'bg-secondary text-white'
                                        ];
                                        $badge = $roleBadges[$u['role']] ?? 'bg-light text-dark';
                                        ?>
                                        <span class="badge <?= $badge ?> text-uppercase"><?= Security::escape($u['role']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= $u['status'] === 'active' ? 'success' : 'dark' ?>">
                                            <?= $u['status'] === 'active' ? 'Ativo' : 'Inativo' ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary me-1" 
                                                onclick="editUser(<?= htmlspecialchars(json_encode($u), ENT_QUOTES, 'UTF-8') ?>)">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDeleteUser(<?= $u['id'] ?>, '<?= Security::escape(addslashes($u['name'])) ?>')">
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

<!-- Modal Criar Utilizador -->
<div class="modal fade" id="modalCreateUser" tabindex="-1">
    <div class="modal-dialog">
        <form action="/users/store" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::escape($csrfToken) ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-plus me-2 text-primary"></i>Criar Novo Utilizador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="create_user_name" class="form-label fw-semibold">Nome Completo *</label>
                        <input type="text" class="form-control" id="create_user_name" name="name" required placeholder="ex: João Silva">
                    </div>
                    <div class="mb-3">
                        <label for="create_user_email" class="form-label fw-semibold">Endereço de E-mail *</label>
                        <input type="email" class="form-control" id="create_user_email" name="email" required placeholder="ex: joao@empresa.com">
                    </div>
                    <div class="mb-3">
                        <label for="create_user_password" class="form-label fw-semibold">Palavra-passe *</label>
                        <input type="password" class="form-control" id="create_user_password" name="password" required placeholder="Mínimo 8 caracteres">
                    </div>
                    <div class="mb-3">
                        <label for="create_user_role" class="form-label fw-semibold">Perfil de Acesso (RBAC) *</label>
                        <select class="form-select" id="create_user_role" name="role">
                            <option value="requester">Requisitante (Visualizar & Solicitar)</option>
                            <option value="operator">Operador (Gerir Stock & Aprovar)</option>
                            <option value="admin">Administrador (Acesso Total)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="create_user_status" class="form-label fw-semibold">Estado</label>
                        <select class="form-select" id="create_user_status" name="status">
                            <option value="active">Ativo</option>
                            <option value="inactive">Inativo</option>
                        </select>
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

<!-- Modal Editar Utilizador -->
<div class="modal fade" id="modalEditUser" tabindex="-1">
    <div class="modal-dialog">
        <form action="/users/update" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::escape($csrfToken) ?>">
            <input type="hidden" name="id" id="edit_user_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-pen me-2 text-primary"></i>Editar Utilizador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_user_name" class="form-label fw-semibold">Nome Completo *</label>
                        <input type="text" class="form-control" id="edit_user_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_user_email" class="form-label fw-semibold">Endereço de E-mail *</label>
                        <input type="email" class="form-control" id="edit_user_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_user_password" class="form-label fw-semibold">Nova Palavra-passe (Deixe em branco para não alterar)</label>
                        <input type="password" class="form-control" id="edit_user_password" name="password" placeholder="Opcional">
                    </div>
                    <div class="mb-3">
                        <label for="edit_user_role" class="form-label fw-semibold">Perfil de Acesso *</label>
                        <select class="form-select" id="edit_user_role" name="role">
                            <option value="requester">Requisitante</option>
                            <option value="operator">Operador</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_user_status" class="form-label fw-semibold">Estado</label>
                        <select class="form-select" id="edit_user_status" name="status">
                            <option value="active">Ativo</option>
                            <option value="inactive">Inativo</option>
                        </select>
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

<!-- Modal Confirmar Eliminação de Utilizador -->
<div class="modal fade" id="modalDeleteUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="/users/delete" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::escape($csrfToken) ?>">
            <input type="hidden" name="id" id="delete_user_id">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>Confirmar Eliminação</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    Tem a certeza que deseja eliminar o utilizador <strong id="delete_user_name"></strong>?
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash me-2"></i>Eliminar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function editUser(user) {
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_user_name').value = user.name;
    document.getElementById('edit_user_email').value = user.email;
    document.getElementById('edit_user_role').value = user.role;
    document.getElementById('edit_user_status').value = user.status;
    document.getElementById('edit_user_password').value = '';
    new bootstrap.Modal(document.getElementById('modalEditUser')).show();
}

function confirmDeleteUser(id, name) {
    document.getElementById('delete_user_id').value = id;
    document.getElementById('delete_user_name').textContent = name;
    new bootstrap.Modal(document.getElementById('modalDeleteUser')).show();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
