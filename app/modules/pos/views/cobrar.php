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

            <div class="info-item">
                <div class="info-label">Método de Pago</div>
                <div class="info-value">
                    <?= htmlspecialchars($venta['metodo_pago'] ?? 'No especificado') ?>
                </div>
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
            <div style="font-size: 0.875rem; color: #92400e; margin-bottom: 0.25rem;">💰 Total a cobrar:</div>
            <strong>Q <?= number_format($saldoPendiente, 2) ?></strong>
        </div>

        <form method="POST" action="<?= url('/admin/pos/registrar-cobro') ?>" id="formCobro">
            <input type="hidden" name="venta_id" value="<?= $venta['id'] ?? '' ?>">
            <input type="hidden" name="monto_cobrado" id="monto_cobrado_hidden" value="<?= $saldoPendiente ?>">

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
                    oninput="calcularVuelto()">
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
                <select name="metodo_pago" id="metodoPago" class="form-select" required>
                    <option value="">Seleccione método de pago</option>
                    <option value="Efectivo">💵 Efectivo</option>
                    <option value="Transferencia">🏦 Transferencia/Depósito</option>
                    <option value="Tarjeta">💳 Tarjeta</option>
                    <option value="Cheque">📝 Cheque</option>
                    <option value="Mixto">🔄 Pago Mixto</option>
                </select>
            </div>

            <!-- Campos específicos por método de pago -->
            <div id="camposEspecificos">
                <!-- Campo para número de cheque -->
                <div id="campoCheque" class="form-group" style="display: none;">
                    <label class="form-label">
                        📝 Número de Cheque <span style="color: #ef4444;">*</span>
                    </label>
                    <input
                        type="text"
                        name="numero_cheque"
                        class="form-input"
                        placeholder="Ingrese el número del cheque">
                </div>

                <!-- Campo para número de boleta -->
                <div id="campoBoleta" class="form-group" style="display: none;">
                    <label class="form-label">
                        🧾 Número de Boleta/Referencia <span style="color: #ef4444;">*</span>
                    </label>
                    <input
                        type="text"
                        name="numero_boleta"
                        class="form-input"
                        placeholder="Ingrese el número de boleta o referencia">
                </div>

                <!-- Campo para porcentaje extra de tarjeta -->
                <div id="campoTarjeta" class="form-group" style="display: none;">
                    <label class="form-label">
                        💳 Recargo por Tarjeta (%)
                    </label>
                    <input
                        type="number"
                        id="porcentaje_tarjeta"
                        name="porcentaje_tarjeta"
                        class="form-input"
                        step="0.01"
                        min="0"
                        max="20"
                        value="3"
                        placeholder="3.00"
                        oninput="calcularRecargoTarjeta()">
                    <div id="recargoInfo" style="margin-top: 0.5rem; padding: 0.75rem; background: #fef3c7; border-radius: 0.5rem; display: none;">
                        <small style="color: #92400e;">
                            Recargo: Q <span id="recargoMonto">0.00</span> |
                            Total con recargo: Q <span id="totalConRecargo">0.00</span>
                        </small>
                    </div>
                </div>

                <!-- Sección de Pago Mixto -->
                <div id="seccionPagoMixto" style="display: none;">
                    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                        <h4 style="margin: 0 0 1rem 0; color: #1e293b;">🔄 Configurar Pago Mixto</h4>
                        <p style="color: #64748b; font-size: 0.875rem; margin-bottom: 1.5rem;">
                            Agregue los diferentes métodos de pago que usará el cliente
                        </p>

                        <div id="metodosMixtos">
                            <!-- Métodos de pago mixtos se agregarán dinámicamente -->
                        </div>

                        <button type="button" id="agregarMetodo" class="btn" style="background: #3b82f6; color: white; padding: 0.5rem 1rem; font-size: 0.875rem;">
                            ➕ Agregar Método de Pago
                        </button>

                        <div id="resumenMixto" style="margin-top: 1.5rem; padding: 1rem; background: #e0f2fe; border-radius: 0.5rem; display: none;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Total asignado:</span>
                                <strong>Q <span id="totalAsignado">0.00</span></strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Saldo pendiente:</span>
                                <strong style="color: #ef4444;">Q <span id="saldoRestante">0.00</span></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">📝 Observaciones (opcional)</label>
                <textarea
                    name="observaciones"
                    class="form-textarea"
                    placeholder="Notas adicionales sobre el cobro..."></textarea>
            </div>

            <div class="btn-group">
                <a href="<?= url('/admin/pos') ?>" class="btn btn-cancel">← Cancelar</a>
                <button type="submit" class="btn btn-submit">💾 Registrar Cobro</button>
            </div>
        </form>
    </div>
</div>

<script>
    const metodoPagoSelect = document.getElementById('metodoPago');
    const montoRecibidoGroup = document.getElementById('montoRecibidoGroup');
    const montoRecibidoInput = document.getElementById('monto_recibido');
    const montoCobradoHidden = document.getElementById('monto_cobrado_hidden');
    const saldoPendiente = <?= $saldoPendiente ?>;

    let metodosMixtosCount = 0;
    let metodosMixtos = [];

    // Agregar evento change al select de método de pago
    metodoPagoSelect.addEventListener('change', function() {
        console.log('Método de pago cambiado a:', this.value);

        // Ocultar todos los campos específicos
        document.querySelectorAll('#camposEspecificos > div').forEach(div => {
            div.style.display = 'none';
            // Limpiar campos requeridos
            const inputs = div.querySelectorAll('input[required]');
            inputs.forEach(input => input.removeAttribute('required'));
        });

        // Mostrar campo apropiado según la selección
        const metodo = this.value;

        switch (metodo) {
            case 'Efectivo':
                console.log('Mostrando campo efectivo');
                montoRecibidoGroup.style.display = 'block';
                calcularVuelto();
                break;

            case 'Transferencia':
                console.log('Mostrando campo transferencia');
                document.getElementById('campoBoleta').style.display = 'block';
                document.querySelector('input[name="numero_boleta"]').setAttribute('required', 'required');
                montoRecibidoGroup.style.display = 'none';
                break;

            case 'Cheque':
                console.log('Mostrando campo cheque');
                document.getElementById('campoCheque').style.display = 'block';
                document.querySelector('input[name="numero_cheque"]').setAttribute('required', 'required');
                montoRecibidoGroup.style.display = 'none';
                break;

            case 'Tarjeta':
                console.log('Mostrando campo tarjeta');
                document.getElementById('campoTarjeta').style.display = 'block';
                calcularRecargoTarjeta();
                montoRecibidoGroup.style.display = 'none';
                break;

            case 'Mixto':
                console.log('Mostrando pago mixto');
                const seccionMixto = document.getElementById('seccionPagoMixto');
                if (seccionMixto) {
                    seccionMixto.style.display = 'block';
                    inicializarPagoMixto();
                }
                montoRecibidoGroup.style.display = 'none';
                break;

            default:
                montoRecibidoGroup.style.display = 'none';
                break;
        }

        if (metodo !== 'Efectivo') {
            document.getElementById('vueltoDisplay').classList.remove('active');
            montoRecibidoInput.value = '';
        }
    });

    function calcularVuelto() {
        const metodoPago = metodoPagoSelect.value;

        if (metodoPago !== 'Efectivo') {
            return;
        }

        const montoCobrado = saldoPendiente; // Siempre el saldo completo
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

    function calcularRecargoTarjeta() {
        const porcentaje = parseFloat(document.getElementById('porcentaje_tarjeta').value) || 0;
        const montoCobrado = saldoPendiente; // Siempre el saldo completo

        if (porcentaje > 0 && montoCobrado > 0) {
            const recargo = (montoCobrado * porcentaje) / 100;
            const totalConRecargo = montoCobrado + recargo;

            document.getElementById('recargoMonto').textContent = recargo.toFixed(2);
            document.getElementById('totalConRecargo').textContent = totalConRecargo.toFixed(2);
            document.getElementById('recargoInfo').style.display = 'block';

            // Actualizar el monto total con recargo
            montoCobradoHidden.value = totalConRecargo.toFixed(2);
        } else {
            document.getElementById('recargoInfo').style.display = 'none';
            // Restaurar el monto original
            montoCobradoHidden.value = saldoPendiente.toFixed(2);
        }
    }

    function inicializarPagoMixto() {
        metodosMixtosCount = 0;
        metodosMixtos = [];
        document.getElementById('metodosMixtos').innerHTML = '';
        montoCobradoHidden.value = saldoPendiente.toFixed(2);
        agregarMetodoMixto();
    }

    function agregarMetodoMixto() {
        metodosMixtosCount++;
        const metodosContainer = document.getElementById('metodosMixtos');

        const metodoDiv = document.createElement('div');
        metodoDiv.className = 'metodo-mixto';
        metodoDiv.setAttribute('data-index', metodosMixtosCount);
        metodoDiv.innerHTML = `
        <div style="background: white; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #e2e8f0;">
            <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 1rem;">
                <h5 style="margin: 0; color: #1e293b;">Método ${metodosMixtosCount}</h5>
                ${metodosMixtosCount > 1 ? `<button type="button" onclick="eliminarMetodoMixto(${metodosMixtosCount})" style="background: #ef4444; color: white; border: none; border-radius: 0.25rem; padding: 0.25rem 0.5rem; font-size: 0.75rem; cursor: pointer;">❌</button>` : ''}
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Método</label>
                    <select name="mixto_metodo[]" class="form-select" style="font-size: 0.875rem;" required onchange="mostrarCamposMixto(${metodosMixtosCount}, this.value)">
                        <option value="">Seleccione...</option>
                        <option value="Efectivo">💵 Efectivo</option>
                        <option value="Transferencia">🏦 Transferencia</option>
                        <option value="Tarjeta">💳 Tarjeta</option>
                        <option value="Cheque">📝 Cheque</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Monto</label>
                    <input type="number" name="mixto_monto[]" class="form-input" style="font-size: 0.875rem;" step="0.01" min="0.01" required placeholder="0.00" oninput="calcularTotalMixto()">
                </div>
            </div>

            <!-- Campos específicos para cada método -->
            <div id="campos_mixto_${metodosMixtosCount}" class="campos-mixto-especificos">
                <!-- Se llenarán dinámicamente -->
            </div>
        </div>
    `;

        metodosContainer.appendChild(metodoDiv);
        calcularTotalMixto();
    }

    function eliminarMetodoMixto(index) {
        const metodoDiv = document.querySelector(`[data-index="${index}"]`);
        if (metodoDiv) {
            metodoDiv.remove();
            calcularTotalMixto();
        }
    }

    function mostrarCamposMixto(index, metodo) {
        const camposDiv = document.getElementById(`campos_mixto_${index}`);
        camposDiv.innerHTML = '';

        let campoEspecifico = '';

        switch (metodo) {
            case 'Cheque':
                campoEspecifico = `
                <div style="margin-top: 1rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Número de Cheque</label>
                    <input type="text" name="mixto_numero_cheque[]" class="form-input" style="font-size: 0.875rem;" placeholder="Número del cheque" required>
                </div>
            `;
                break;

            case 'Transferencia':
                campoEspecifico = `
                <div style="margin-top: 1rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Número de Boleta</label>
                    <input type="text" name="mixto_numero_boleta[]" class="form-input" style="font-size: 0.875rem;" placeholder="Número de boleta o referencia" required>
                </div>
            `;
                break;

            case 'Tarjeta':
                campoEspecifico = `
                <div style="margin-top: 1rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Recargo (%)</label>
                    <input type="number" name="mixto_porcentaje_tarjeta[]" class="form-input" style="font-size: 0.875rem;" step="0.01" min="0" max="20" value="3" placeholder="3.00">
                </div>
            `;
                break;
        }

        if (campoEspecifico) {
            camposDiv.innerHTML = campoEspecifico;
        }
    }

    function calcularTotalMixto() {
        const montos = document.querySelectorAll('input[name="mixto_monto[]"]');
        let totalAsignado = 0;

        montos.forEach(input => {
            const monto = parseFloat(input.value) || 0;
            totalAsignado += monto;
        });

        const saldoRestante = saldoPendiente - totalAsignado;

        document.getElementById('totalAsignado').textContent = totalAsignado.toFixed(2);
        document.getElementById('saldoRestante').textContent = Math.max(0, saldoRestante).toFixed(2);

        // Mostrar resumen
        if (totalAsignado > 0) {
            document.getElementById('resumenMixto').style.display = 'block';
        } else {
            document.getElementById('resumenMixto').style.display = 'none';
        }
    }

    // Agregar evento al botón de agregar método
    document.getElementById('agregarMetodo').addEventListener('click', agregarMetodoMixto);

    // Actualizar cálculo de recargo cuando cambie el monto cobrado
    const montoCobradoInput = document.getElementById('monto_cobrado');
    if (montoCobradoInput) {
        montoCobradoInput.addEventListener('input', function() {
            if (metodoPagoSelect.value === 'Tarjeta') {
                calcularRecargoTarjeta();
            } else if (metodoPagoSelect.value === 'Efectivo') {
                calcularVuelto();
            }
        });
    }

    document.getElementById('formCobro').addEventListener('submit', function(e) {
        const metodo = metodoPagoSelect.value;
        const monto = parseFloat(montoCobradoInput.value);

        // Permitir monto mayor solo si es pago con tarjeta (por recargo)
        if (monto > saldoPendiente && metodo !== 'Tarjeta') {
            e.preventDefault();
            alert('El monto cobrado no puede ser mayor al saldo pendiente');
            return false;
        }

        if (monto <= 0) {
            e.preventDefault();
            alert('El monto debe ser mayor a 0');
            return false;
        }

        // Validaciones específicas por método
        if (metodo === 'Efectivo') {
            const montoRecibido = parseFloat(montoRecibidoInput.value) || 0;
            if (montoRecibido > 0 && montoRecibido < saldoPendiente) {
                e.preventDefault();
                alert('El monto recibido del cliente es insuficiente');
                return false;
            }
        }

        if (metodo === 'Mixto') {
            const totalAsignado = parseFloat(document.getElementById('totalAsignado').textContent);
            if (Math.abs(totalAsignado - saldoPendiente) > 0.01) {
                e.preventDefault();
                alert('El total de los métodos de pago debe ser igual al saldo pendiente');
                return false;
            }
        }
    });
</script>
