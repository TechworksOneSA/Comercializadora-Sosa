<?php
$venta = $venta ?? [];
$saldoPendiente = $venta['saldo_pendiente'] ?? 0;
?>

<style>
.cobrar-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem;
}

.cobrar-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    padding: 2rem;
    border-radius: 1rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
    color: white;
}

.cobrar-header h1 {
    font-size: 1.875rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
}

.info-card {
    background: white;
    padding: 1.5rem;
    border-radius: 1rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.info-item {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 0.5rem;
}

.info-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1e293b;
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
    border-color: #10b981;
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
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
}

.saldo-highlight {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    border-left: 4px solid #f59e0b;
    margin-bottom: 1.5rem;
}

.saldo-highlight strong {
    font-size: 1.5rem;
    color: #92400e;
}

.vuelto-display {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    border-left: 4px solid #10b981;
    margin-top: 1rem;
    display: none;
}

.vuelto-display.active {
    display: block;
}

.vuelto-display strong {
    font-size: 1.5rem;
    color: #065f46;
}
</style>

<div class="cobrar-container">
    <!-- Header -->
    <div class="cobrar-header">
        <h1>💵 Registrar Cobro</h1>
        <p style="opacity: 0.9; margin: 0;">Venta #<?= $venta['id'] ?? 'N/A' ?></p>
    </div>

    <!-- Información de la Venta -->
    <div class="info-card">
        <h3 style="margin: 0 0 1rem 0; color: #1e293b;">📋 Información de la Venta</h3>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Cliente</div>
                <div class="info-value"><?= htmlspecialchars($venta['cliente_nombre'] ?? 'N/A') ?></div>
            </div>

            <div class="info-item">
                <div class="info-label">NIT</div>
                <div class="info-value"><?= htmlspecialchars($venta['cliente_nit'] ?? 'C/F') ?></div>
            </div>

            <div class="info-item">
                <div class="info-label">Fecha</div>
                <div class="info-value"><?= date('d/m/Y', strtotime($venta['fecha_venta'] ?? 'now')) ?></div>
            </div>

            <div class="info-item">
                <div class="info-label">Total</div>
                <div class="info-value">Q <?= number_format($venta['total'] ?? 0, 2) ?></div>
            </div>

            <div class="info-item">
                <div class="info-label">Pagado</div>
                <div class="info-value" style="color: #10b981;">Q <?= number_format($venta['total_pagado'] ?? 0, 2) ?></div>
            </div>

            <div class="info-item" style="background: #fee2e2;">
                <div class="info-label" style="color: #991b1b;">Saldo Pendiente</div>
                <div class="info-value" style="color: #ef4444;">Q <?= number_format($saldoPendiente, 2) ?></div>
            </div>
        </div>
    </div>

    <!-- Formulario de Cobro -->
    <div class="form-card">
        <div class="saldo-highlight">
            <div style="font-size: 0.875rem; color: #92400e; margin-bottom: 0.25rem;">💰 Monto a cobrar (máximo):</div>
            <strong>Q <?= number_format($saldoPendiente, 2) ?></strong>
        </div>

        <form method="POST" action="<?= url('/admin/pos/registrar-cobro') ?>" id="formCobro">
            <input type="hidden" name="venta_id" value="<?= $venta['id'] ?? '' ?>">

            <div class="form-group">
                <label class="form-label">
                    💵 Monto Cobrado <span style="color: #ef4444;">*</span>
                </label>
                <input
                    type="number"
                    id="monto_cobrado"
                    name="monto_cobrado"
                    class="form-input"
                    step="0.01"
                    min="0.01"
                    max="<?= $saldoPendiente ?>"
                    placeholder="Ingrese el monto cobrado"
                    required
                    autofocus
                    oninput="calcularVuelto()"
                >
                <small style="color: #64748b; display: block; margin-top: 0.25rem;">
                    Puede cobrar un monto parcial si el cliente no paga el total
                </small>
            </div>

            <div class="form-group" id="montoRecibidoGroup" style="display: none;">
                <label class="form-label">
                    💰 Monto Recibido del Cliente
                </label>
                <input
                    type="number"
                    id="monto_recibido"
                    class="form-input"
                    step="0.01"
                    min="0.01"
                    placeholder="¿Cuánto dio el cliente?"
                    oninput="calcularVuelto()"
                >
                <small style="color: #64748b; display: block; margin-top: 0.25rem;">
                    Solo para calcular el vuelto - no se guarda en el sistema
                </small>
            </div>

            <!-- Display de Vuelto -->
            <div id="vueltoDisplay" class="vuelto-display">
                <div style="font-size: 0.875rem; color: #065f46; margin-bottom: 0.25rem;">💵 Vuelto a entregar:</div>
                <strong>Q <span id="vueltoMonto">0.00</span></strong>
            </div>

            <div class="form-group">
                <label class="form-label">
                    💳 Método de Pago <span style="color: #ef4444;">*</span>
                </label>
                <select name="metodo_pago" class="form-select" required>
                    <option value="">Seleccione método de pago</option>
                    <option value="Efectivo">💵 Efectivo</option>
                    <option value="Transferencia">🏦 Transferencia</option>
                    <option value="Tarjeta">💳 Tarjeta</option>
                    <option value="Cheque">📝 Cheque</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">📝 Observaciones (opcional)</label>
                <textarea
                    name="observaciones"
                    class="form-textarea"
                    placeholder="Notas adicionales sobre el cobro..."
                ></textarea>
            </div>

            <div class="btn-group">
                <a href="<?= url('/admin/pos') ?>" class="btn btn-cancel">← Cancelar</a>
                <button type="submit" class="btn btn-submit">💾 Registrar Cobro</button>
            </div>
        </form>
    </div>
</div>

<script>
const metodoPagoSelect = document.querySelector('select[name="metodo_pago"]');
const montoRecibidoGroup = document.getElementById('montoRecibidoGroup');
const montoRecibidoInput = document.getElementById('monto_recibido');

// Mostrar campo de monto recibido solo si es Efectivo
metodoPagoSelect.addEventListener('change', function() {
    if (this.value === 'Efectivo') {
        montoRecibidoGroup.style.display = 'block';
        calcularVuelto();
    } else {
        montoRecibidoGroup.style.display = 'none';
        document.getElementById('vueltoDisplay').classList.remove('active');
        montoRecibidoInput.value = '';
    }
});

function calcularVuelto() {
    const metodoPago = metodoPagoSelect.value;

    if (metodoPago !== 'Efectivo') {
        return;
    }

    const montoCobrado = parseFloat(document.getElementById('monto_cobrado').value) || 0;
    const montoRecibido = parseFloat(montoRecibidoInput.value) || 0;

    if (montoRecibido > 0 && montoCobrado > 0) {
        const vuelto = montoRecibido - montoCobrado;

        if (vuelto >= 0) {
            document.getElementById('vueltoMonto').textContent = vuelto.toFixed(2);
            document.getElementById('vueltoDisplay').classList.add('active');
        } else {
            document.getElementById('vueltoDisplay').classList.remove('active');
        }
    } else {
        document.getElementById('vueltoDisplay').classList.remove('active');
    }
}

document.getElementById('formCobro').addEventListener('submit', function(e) {
    const monto = parseFloat(document.querySelector('input[name="monto_cobrado"]').value);
    const saldoPendiente = <?= $saldoPendiente ?>;

    if (monto > saldoPendiente) {
        e.preventDefault();
        alert('El monto cobrado no puede ser mayor al saldo pendiente');
        return false;
    }

    if (monto <= 0) {
        e.preventDefault();
        alert('El monto debe ser mayor a 0');
        return false;
    }

    // Validar que si es efectivo y hay monto recibido, sea suficiente
    if (metodoPagoSelect.value === 'Efectivo') {
        const montoRecibido = parseFloat(montoRecibidoInput.value) || 0;
        if (montoRecibido > 0 && montoRecibido < monto) {
            e.preventDefault();
            alert('El monto recibido del cliente es insuficiente');
            return false;
        }
    }
});
</script>
