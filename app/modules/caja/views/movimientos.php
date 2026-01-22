<?php
$movimientos = $movimientos ?? [];
$success = $_SESSION['flash_success'] ?? null;
$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<style>
    .movimientos-container {
        padding: 2rem;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        min-height: 100vh;
    }

    .movimientos-header {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        padding: 1.75rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .movimientos-header h1 {
        font-size: 1.875rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .btn-primary {
        background: white;
        color: #ef4444;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s;
        margin-right: 1rem;
    }

    .btn-back:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .table-container {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        overflow-x: auto;
    }

    .movimientos-table {
        width: 100%;
        border-collapse: collapse;
    }

    .movimientos-table thead {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .movimientos-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.9rem;
        color: white;
    }

    .movimientos-table thead tr th:first-child {
        border-radius: 0.5rem 0 0 0;
    }

    .movimientos-table thead tr th:last-child {
        border-radius: 0 0.5rem 0 0;
    }

    .movimientos-table td {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .movimientos-table tbody tr:hover {
        background: #f8fafc;
    }

    .badge {
        display: inline-block;
        padding: 0.375rem 0.875rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .badge.gasto {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge.retiro {
        background: #fed7aa;
        color: #9a3412;
    }

    .btn-danger {
        background: #ef4444;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        text-decoration: none;
        font-size: 0.75rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-danger:hover {
        background: #dc2626;
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-left: 4px solid #10b981;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }

    /* Modal de confirmación */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9998;
        animation: fadeIn 0.2s ease;
    }

    .modal-overlay.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-confirm {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        max-width: 450px;
        width: 90%;
        overflow: hidden;
        animation: scaleIn 0.3s ease;
    }

    .modal-header {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        padding: 1.5rem;
        color: white;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .modal-header-icon {
        font-size: 2rem;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
    }

    .modal-body {
        padding: 2rem 1.5rem;
        color: #1e293b;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        background: #f8fafc;
    }

    .btn-modal-cancel {
        background: #e2e8f0;
        color: #475569;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-modal-cancel:hover {
        background: #cbd5e1;
        transform: translateY(-1px);
    }

    .btn-modal-confirm {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .btn-modal-confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes scaleIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>

<div class="movimientos-container">
    <!-- Header -->
    <div class="movimientos-header">
        <h1>📤 Movimientos de Caja</h1>
        <div>
            <a href="<?= url('/admin/caja') ?>" class="btn-back">← Volver a Caja</a>
            <a href="<?= url('/admin/caja/nuevo-movimiento') ?>" class="btn-primary">+ Nuevo Movimiento</a>
        </div>
    </div>

    <!-- Mensajes Flash -->
    <?php if ($success): ?>
        <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Tabla de Movimientos -->
    <div class="table-container">
        <?php if (empty($movimientos)): ?>
            <div style="text-align: center; padding: 3rem;">
                <h3 style="color: #64748b; margin: 0 0 1rem 0;">📂 No hay movimientos registrados</h3>
                <p style="color: #94a3b8; margin-bottom: 2rem;">Registra el primer gasto o retiro de caja</p>
                <a href="<?= url('/admin/caja/nuevo-movimiento') ?>" style="background: #ef4444; color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600;">
                    + Registrar Primer Movimiento
                </a>
            </div>
        <?php else: ?>
            <table class="movimientos-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Concepto</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Usuario</th>
                        <th>Observaciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movimientos as $movimiento): ?>
                        <tr>
                            <td>
                                📅 <?= date('d/m/Y', strtotime($movimiento['fecha'])) ?><br>
                                <small style="color: #64748b;"><?= date('H:i', strtotime($movimiento['fecha'])) ?></small>
                            </td>
                            <td>
                                <span class="badge <?= $movimiento['tipo'] ?>">
                                    <?php
                                    if ($movimiento['tipo'] === 'ingreso') {
                                        echo '💰 Ingreso (Venta)';
                                    } elseif ($movimiento['tipo'] === 'gasto') {
                                        echo '📤 Gasto';
                                    } else {
                                        echo '🏦 Retiro';
                                    }
                                    ?>
                                </span>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($movimiento['concepto']) ?></strong>
                            </td>
                            <td style="font-weight: 700; color: <?= $movimiento['tipo'] === 'ingreso' ? '#10b981' : '#ef4444' ?>;">
                                <?= $movimiento['tipo'] === 'ingreso' ? '+' : '-' ?>Q <?= number_format($movimiento['monto'], 2) ?>
                            </td>
                            <td><?= htmlspecialchars($movimiento['metodo_pago']) ?></td>
                            <td><?= htmlspecialchars($movimiento['usuario_nombre']) ?></td>
                            <td>
                                <?php if (!empty($movimiento['observaciones'])): ?>
                                    <small style="color: #64748b;"><?= htmlspecialchars($movimiento['observaciones']) ?></small>
                                <?php else: ?>
                                    <span style="color: #94a3b8;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" action="<?= url('/admin/caja/eliminar-movimiento/' . $movimiento['id']) ?>"
                                    id="form-delete-<?= $movimiento['id'] ?>"
                                    style="display: inline;">
                                    <button type="button" class="btn-danger" onclick="confirmDelete(<?= $movimiento['id'] ?>)">🗑️ Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModal()"></div>
<div class="modal-overlay" id="modalConfirm">
    <div class="modal-confirm" onclick="event.stopPropagation()">
        <div class="modal-header">
            <span class="modal-header-icon">⚠️</span>
            <h3>Confirmar Eliminación</h3>
        </div>
        <div class="modal-body">
            <p>¿Estás seguro de que deseas eliminar este movimiento de caja?</p>
            <p style="margin-top: 1rem; color: #ef4444; font-weight: 600;">
                ⚠️ Esta acción no se puede deshacer.
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal-cancel" onclick="closeModal()">
                Cancelar
            </button>
            <button type="button" class="btn-modal-confirm" onclick="executeDelete()">
                Sí, Eliminar
            </button>
        </div>
    </div>
</div>

<script>
    let currentDeleteId = null;

    function confirmDelete(id) {
        currentDeleteId = id;
        document.getElementById('modalConfirm').classList.add('show');
    }

    function closeModal() {
        document.getElementById('modalConfirm').classList.remove('show');
        currentDeleteId = null;
    }

    function executeDelete() {
        if (currentDeleteId) {
            document.getElementById('form-delete-' + currentDeleteId).submit();
        }
    }

    // Cerrar modal con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>
