<?php
$errors = $errors ?? [];
$old = $old ?? [];
?>

<style>
    .movimiento-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem;
    }

    .movimiento-header {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        padding: 2rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
        color: white;
    }

    .movimiento-header h1 {
        font-size: 1.875rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
    }

    .form-card {
        background: white;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 0.95rem;
        transition: all 0.3s;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    .btn-group {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        padding-top: 1.5rem;
        border-top: 2px solid #e2e8f0;
    }

    .btn {
        padding: 0.75rem 2rem;
        border-radius: 0.5rem;
        font-weight: 600;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 0.95rem;
    }

    .btn-cancel {
        background: #6c757d;
        color: white;
    }

    .btn-cancel:hover {
        background: #5a6268;
        transform: translateY(-1px);
    }

    .btn-submit {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
    }

    .error-list {
        background: #fee2e2;
        border: 1px solid #fecaca;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .error-list h4 {
        color: #991b1b;
        margin: 0 0 0.5rem 0;
        font-size: 0.875rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .error-list ul {
        margin: 0;
        padding-left: 1rem;
        color: #991b1b;
    }

    .error-list li {
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
</style>

<div class="movimiento-container">
    <!-- Header -->
    <div class="movimiento-header">
        <h1>📤 Nuevo Gasto/Movimiento</h1>
        <p style="margin: 0; opacity: 0.9;">Registra gastos operativos y retiros de caja</p>
    </div>

    <!-- Errores -->
    <?php if (!empty($errors)): ?>
        <div class="error-list">
            <h4>❌ Errores encontrados:</h4>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Formulario -->
    <div class="form-card">
        <form method="POST" action="<?= url('/admin/caja/guardar-movimiento') ?>">

            <!-- Tipo y Concepto -->
            <div class="form-grid">
                <div class="form-group">
                    <label for="tipo" class="form-label">📋 Tipo de Movimiento</label>
                    <select id="tipo" name="tipo" class="form-select" required>
                        <option value="">Seleccionar tipo...</option>
                        <option value="gasto" <?= ($old['tipo'] ?? '') === 'gasto' ? 'selected' : '' ?>>
                            📤 Gasto Operativo
                        </option>
                        <option value="retiro" <?= ($old['tipo'] ?? '') === 'retiro' ? 'selected' : '' ?>>
                            🏦 Retiro de Caja
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="metodo_pago" class="form-label">💳 Método de Pago</label>
                    <select id="metodo_pago" name="metodo_pago" class="form-select" required>
                        <option value="">Seleccionar método...</option>
                        <option value="Efectivo" <?= ($old['metodo_pago'] ?? '') === 'Efectivo' ? 'selected' : '' ?>>
                            💵 Efectivo
                        </option>
                        <option value="Transferencia" <?= ($old['metodo_pago'] ?? '') === 'Transferencia' ? 'selected' : '' ?>>
                            🏦 Transferencia
                        </option>
                        <option value="Cheque" <?= ($old['metodo_pago'] ?? '') === 'Cheque' ? 'selected' : '' ?>>
                            📝 Cheque
                        </option>
                    </select>
                </div>
            </div>

            <!-- Concepto -->
            <div class="form-group">
                <label for="concepto" class="form-label">📝 Concepto</label>
                <input
                    type="text"
                    id="concepto"
                    name="concepto"
                    class="form-input"
                    placeholder="Descripción del gasto o retiro..."
                    value="<?= htmlspecialchars($old['concepto'] ?? '') ?>"
                    required>
                <small style="color: #64748b; font-size: 0.8rem;">
                    Ej: Compra de papelería, Pago de servicios, Retiro para gastos personales, etc.
                </small>
            </div>

            <!-- Monto -->
            <div class="form-group">
                <label for="monto" class="form-label">💰 Monto</label>
                <input
                    type="number"
                    id="monto"
                    name="monto"
                    class="form-input"
                    placeholder="0.00"
                    step="0.01"
                    min="0.01"
                    value="<?= htmlspecialchars($old['monto'] ?? '') ?>"
                    required>
            </div>

            <!-- Observaciones -->
            <div class="form-group">
                <label for="observaciones" class="form-label">📋 Observaciones (Opcional)</label>
                <textarea
                    id="observaciones"
                    name="observaciones"
                    class="form-textarea"
                    placeholder="Detalles adicionales del movimiento..."><?= htmlspecialchars($old['observaciones'] ?? '') ?></textarea>
            </div>

            <!-- Botones -->
            <div class="btn-group">
                <a href="<?= url('/admin/caja/movimientos') ?>" class="btn btn-cancel">
                    ← Cancelar
                </a>
                <button type="submit" class="btn btn-submit">
                    💾 Registrar Movimiento
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Auto-focus en el primer campo
    document.getElementById('tipo').focus();

    // Actualizar placeholder del concepto según el tipo
    document.getElementById('tipo').addEventListener('change', function() {
        const concepto = document.getElementById('concepto');

        if (this.value === 'gasto') {
            concepto.placeholder = 'Ej: Compra de papelería, Pago de servicios, Mantenimiento...';
        } else if (this.value === 'retiro') {
            concepto.placeholder = 'Ej: Retiro para gastos personales, Pago a proveedores...';
        } else {
            concepto.placeholder = 'Descripción del gasto o retiro...';
        }
    });
</script>
