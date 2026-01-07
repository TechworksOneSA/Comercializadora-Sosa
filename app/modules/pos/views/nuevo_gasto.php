<?php
$errors = $errors ?? [];
$old = $old ?? [];
?>

<style>
.gasto-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem;
}

.gasto-header {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    padding: 2rem;
    border-radius: 1rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
    color: white;
}

.gasto-header h1 {
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

.form-input, .form-select, .form-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 0.5rem;
    font-size: 0.95rem;
    transition: all 0.3s;
}

.form-input:focus, .form-select:focus, .form-textarea:focus {
    outline: none;
    border-color: #ef4444;
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

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid #ef4444;
}

.tipo-selector {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.tipo-option {
    position: relative;
}

.tipo-option input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.tipo-option label {
    display: block;
    padding: 1.5rem;
    background: #f8f9fa;
    border: 2px solid #e2e8f0;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
}

.tipo-option input[type="radio"]:checked + label {
    background: #fee2e2;
    border-color: #ef4444;
    transform: scale(1.02);
}

.tipo-option label:hover {
    border-color: #ef4444;
}

.tipo-option .tipo-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.tipo-option .tipo-title {
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.tipo-option .tipo-desc {
    font-size: 0.875rem;
    color: #64748b;
}

.ejemplos {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-top: 0.5rem;
}

.ejemplos-title {
    font-weight: 600;
    color: #64748b;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.ejemplos-list {
    list-style: none;
    padding: 0;
    margin: 0;
    font-size: 0.875rem;
    color: #64748b;
}

.ejemplos-list li::before {
    content: "• ";
    color: #ef4444;
    font-weight: bold;
}
</style>

<div class="gasto-container">
    <!-- Header -->
    <div class="gasto-header">
        <h1>📤 Nuevo Gasto / Movimiento</h1>
        <p style="opacity: 0.9; margin: 0;">Registra gastos operativos o retiros de caja</p>
    </div>

    <!-- Errores -->
    <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <strong>⚠️ Errores:</strong>
            <ul style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Formulario -->
    <div class="form-card">
        <form method="POST" action="<?= url('/admin/pos/guardar-gasto') ?>">

            <!-- Selector de Tipo -->
            <div class="tipo-selector">
                <div class="tipo-option">
                    <input type="radio" name="tipo" value="gasto" id="tipo_gasto" <?= ($old['tipo'] ?? 'gasto') === 'gasto' ? 'checked' : '' ?> required>
                    <label for="tipo_gasto">
                        <div class="tipo-icon">📤</div>
                        <div class="tipo-title">Gasto Operativo</div>
                        <div class="tipo-desc">Compras de consumibles, servicios, etc.</div>
                    </label>
                </div>

                <div class="tipo-option">
                    <input type="radio" name="tipo" value="retiro" id="tipo_retiro" <?= ($old['tipo'] ?? '') === 'retiro' ? 'checked' : '' ?> required>
                    <label for="tipo_retiro">
                        <div class="tipo-icon">🏦</div>
                        <div class="tipo-title">Retiro para Banco</div>
                        <div class="tipo-desc">Depósito a cuenta bancaria</div>
                    </label>
                </div>
            </div>

            <div class="ejemplos">
                <div class="ejemplos-title">💡 Ejemplos de gastos operativos:</div>
                <ul class="ejemplos-list">
                    <li>Vasos desechables, platos, cubiertos</li>
                    <li>Agua pura, café, azúcar</li>
                    <li>Productos de limpieza</li>
                    <li>Papelería y útiles de oficina</li>
                    <li>Servicios (luz, agua, internet)</li>
                    <li>Mantenimiento y reparaciones</li>
                </ul>
            </div>

            <div class="form-group" style="margin-top: 1.5rem;">
                <label class="form-label">
                    📝 Concepto <span style="color: #ef4444;">*</span>
                </label>
                <input
                    type="text"
                    name="concepto"
                    class="form-input"
                    placeholder="Ej: Compra de vasos desechables"
                    value="<?= htmlspecialchars($old['concepto'] ?? '') ?>"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label class="form-label">
                    💰 Monto <span style="color: #ef4444;">*</span>
                </label>
                <input
                    type="number"
                    name="monto"
                    class="form-input"
                    step="0.01"
                    min="0.01"
                    placeholder="0.00"
                    value="<?= htmlspecialchars($old['monto'] ?? '') ?>"
                    required
                >
            </div>

            <!-- Método de pago fijo: Efectivo (caja chica) -->
            <input type="hidden" name="metodo_pago" value="Efectivo">

            <div class="form-group">
                <label class="form-label">📄 Observaciones (opcional)</label>
                <textarea
                    name="observaciones"
                    class="form-textarea"
                    placeholder="Detalles adicionales..."
                ><?= htmlspecialchars($old['observaciones'] ?? '') ?></textarea>
            </div>

            <div class="btn-group">
                <a href="<?= url('/admin/pos/gastos') ?>" class="btn btn-cancel">← Cancelar</a>
                <button type="submit" class="btn btn-submit">💾 Registrar Movimiento</button>
            </div>
        </form>
    </div>
</div>
