<div class="card" style="max-width: 900px; margin: 0 auto;">
    <!-- HEADER con gradiente -->
    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem;">
        <h1 class="card-title" style="color: white; font-size: 1.75rem; font-weight: 700; margin: 0;">
            ✏️ Editar Cliente
        </h1>
        <p style="color: rgba(255,255,255,0.9); margin: 0.5rem 0 0 0; font-size: 0.95rem;">
            Actualiza la información del cliente <?= e($cliente['nombre']) ?> <?= e($cliente['apellido']) ?>
        </p>
    </div>

    <!-- ERRORES -->
    <?php if (!empty($errors)): ?>
        <div style="margin: 1.5rem; padding: 1rem 1.5rem; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 0.5rem; color: #721c24;">
            <strong style="display: block; margin-bottom: 0.5rem;">⚠️ Por favor corrige los siguientes errores:</strong>
            <ul style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- INFORMACIÓN ACTUAL -->
    <div style="margin: 1.5rem; padding: 1.25rem; background: #e3f2fd; border: 1px solid #bbdefb; border-radius: 0.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
            <div style="background: #1976d2; color: white; padding: 0.5rem; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                👤
            </div>
            <div>
                <h3 style="margin: 0; color: #1565c0; font-size: 1.1rem; font-weight: 700;">Información Actual</h3>
                <p style="margin: 0; color: #1976d2; font-size: 0.9rem;">Cliente #<?= e($cliente['id']) ?> - Registrado el <?= date('d/m/Y', strtotime($cliente['created_at'])) ?></p>
            </div>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-size: 0.9rem;">
            <div><strong>Total Gastado:</strong> <span style="color: #2e7d32; font-weight: 700;">Q <?= number_format($cliente['total_gastado'] ?? 0, 2) ?></span></div>
            <div><strong>Método Actual:</strong> <span style="color: #1976d2;"><?= e($cliente['preferencia_metodo_pago']) ?></span></div>
            <div><strong>NIT:</strong> <span style="color: #5d4037;"><?= e($cliente['nit']) ?: 'No registrado' ?></span></div>
        </div>
    </div>

    <!-- FORMULARIO -->
    <form
        method="POST"
        action="<?= url('/admin/clientes/actualizar/' . $cliente['id']) ?>"
        style="padding: 2rem;">

        <div style="display: grid; gap: 1.5rem;">

            <!-- NOMBRE Y APELLIDO (Grid 2 columnas) -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">

                <!-- NOMBRE -->
                <div>
                    <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 0.5rem; font-size: 0.95rem;">
                        Nombre <span style="color: #dc3545;">*</span>
                    </label>
                    <input
                        type="text"
                        name="nombre"
                        value="<?= e($old['nombre'] ?? $cliente['nombre']) ?>"
                        placeholder="Ej: Juan"
                        required
                        style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem; transition: border-color 0.3s;"
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#e9ecef'">
                </div>

                <!-- APELLIDO -->
                <div>
                    <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 0.5rem; font-size: 0.95rem;">
                        Apellido <span style="color: #dc3545;">*</span>
                    </label>
                    <input
                        type="text"
                        name="apellido"
                        value="<?= e($old['apellido'] ?? $cliente['apellido']) ?>"
                        placeholder="Ej: Pérez García"
                        required
                        style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem; transition: border-color 0.3s;"
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#e9ecef'">
                </div>
            </div>

            <!-- TELÉFONO Y MÉTODO DE PAGO (Grid 2 columnas) -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">

                <!-- TELÉFONO -->
                <div>
                    <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 0.5rem; font-size: 0.95rem;">
                        Teléfono <span style="color: #dc3545;">*</span>
                    </label>
                    <input
                        type="text"
                        name="telefono"
                        value="<?= e($old['telefono'] ?? $cliente['telefono']) ?>"
                        placeholder="Ej: 5512-3456"
                        required
                        style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem; transition: border-color 0.3s;"
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#e9ecef'">
                </div>

                <!-- MÉTODO DE PAGO -->
                <div>
                    <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 0.5rem; font-size: 0.95rem;">
                        Método de Pago <span style="color: #dc3545;">*</span>
                    </label>
                    <select
                        name="metodo_pago"
                        required
                        style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem; transition: border-color 0.3s; background: white;"
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#e9ecef'">
                        <option value="">-- Selecciona --</option>
                        <?php
                        $metodoActual = $old['metodo_pago'] ?? $cliente['preferencia_metodo_pago'];
                        $metodos = ['Efectivo', 'Transferencia', 'Tarjeta', 'Crédito'];
                        $iconos = ['💵', '🏦', '💳', '📝'];
                        ?>
                        <?php foreach ($metodos as $index => $metodo): ?>
                            <option value="<?= $metodo ?>" <?= $metodoActual === $metodo ? 'selected' : '' ?>>
                                <?= $iconos[$index] ?> <?= $metodo ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- DIRECCIÓN -->
            <div>
                <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 0.5rem; font-size: 0.95rem;">
                    Dirección <span style="color: #6c757d; font-weight: 400; font-size: 0.85rem;">(Opcional)</span>
                </label>
                <textarea
                    name="direccion"
                    rows="3"
                    placeholder="Ej: Zona 1, 5ta Avenida 10-50, Ciudad de Guatemala"
                    style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem; font-family: inherit; resize: vertical; transition: border-color 0.3s;"
                    onfocus="this.style.borderColor='#667eea'"
                    onblur="this.style.borderColor='#e9ecef'"><?= e($old['direccion'] ?? $cliente['direccion']) ?></textarea>
            </div>

            <!-- NIT -->
            <div>
                <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 0.5rem; font-size: 0.95rem;">
                    NIT <span style="color: #6c757d; font-weight: 400; font-size: 0.85rem;">(Opcional)</span>
                </label>
                <input
                    type="text"
                    name="nit"
                    value="<?= e($old['nit'] ?? $cliente['nit']) ?>"
                    placeholder="Ej: 12345678-9"
                    style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem; transition: border-color 0.3s;"
                    onfocus="this.style.borderColor='#667eea'"
                    onblur="this.style.borderColor='#e9ecef'">
                <small style="display: block; margin-top: 0.5rem; color: #6c757d; font-size: 0.85rem;">
                    💡 El campo "Total Gastado" (Q <?= number_format($cliente['total_gastado'] ?? 0, 2) ?>) se actualiza automáticamente con las ventas.
                </small>
            </div>

        </div>

        <!-- BOTONES -->
        <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #e9ecef;">
            <a
                href="<?= url('/admin/clientes') ?>"
                style="padding: 0.75rem 1.5rem; background: #6c757d; color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600; transition: all 0.3s;"
                onmouseover="this.style.background='#5a6268'"
                onmouseout="this.style.background='#6c757d'">
                ← Cancelar
            </a>
            <button
                type="submit"
                style="padding: 0.75rem 2rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3); transition: all 0.3s;"
                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(102, 126, 234, 0.4)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(102, 126, 234, 0.3)'">
                💾 Actualizar Cliente
            </button>
        </div>

    </form>
</div>

<style>
    /* Animación suave al cargar */
    .card {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Focus visible para accesibilidad */
    input:focus,
    textarea:focus,
    select:focus {
        outline: none;
    }

    /* Estilo hover para select */
    select {
        cursor: pointer;
    }

    select:hover {
        border-color: #667eea !important;
    }

    /* Mejorar la sección de información actual */
    .card-header+div {
        border-left: 4px solid #1976d2;
    }
</style>
