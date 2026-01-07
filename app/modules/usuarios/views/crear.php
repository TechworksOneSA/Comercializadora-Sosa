<!-- Vista: Crear Usuario -->
<div class="max-w-3xl mx-auto p-6">

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <a href="<?= url('/admin/usuarios') ?>" class="hover:text-blue-600">Usuarios</a>
            <span>/</span>
            <span class="text-gray-800">Crear Usuario</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-800">Crear Nuevo Usuario</h1>
        <p class="text-gray-600 mt-1">Completa los datos para crear un nuevo usuario del sistema</p>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="<?= url('/admin/usuarios/guardar') ?>" class="space-y-6">

            <!-- Nombre -->
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre Completo <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="<?= htmlspecialchars($old['nombre'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent <?= isset($errors['nombre']) ? 'border-red-500' : '' ?>"
                    placeholder="Ej: Juan Pérez"
                    required
                >
                <?php if (isset($errors['nombre'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['nombre']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Correo Electrónico <span class="text-red-500">*</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent <?= isset($errors['email']) ? 'border-red-500' : '' ?>"
                    placeholder="usuario@ejemplo.com"
                    required
                >
                <?php if (isset($errors['email'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Contraseña <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent <?= isset($errors['password']) ? 'border-red-500' : '' ?>"
                        placeholder="Mínimo 6 caracteres"
                        required
                    >
                    <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                <?php if (isset($errors['password'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['password']) ?></p>
                <?php endif; ?>
                <p class="text-gray-500 text-sm mt-1">La contraseña debe tener al menos 6 caracteres</p>
            </div>

            <!-- Confirmar Contraseña -->
            <div>
                <label for="password_confirmacion" class="block text-sm font-medium text-gray-700 mb-2">
                    Confirmar Contraseña <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input
                        type="password"
                        id="password_confirmacion"
                        name="password_confirmacion"
                        class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent <?= isset($errors['password_confirmacion']) ? 'border-red-500' : '' ?>"
                        placeholder="Repite la contraseña"
                        required
                    >
                    <button type="button" onclick="togglePassword('password_confirmacion')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                <?php if (isset($errors['password_confirmacion'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['password_confirmacion']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Rol -->
            <div>
                <label for="rol" class="block text-sm font-medium text-gray-700 mb-2">
                    Rol <span class="text-red-500">*</span>
                </label>
                <select
                    id="rol"
                    name="rol"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent <?= isset($errors['rol']) ? 'border-red-500' : '' ?>"
                    required
                >
                    <option value="">Selecciona un rol</option>
                    <option value="VENDEDOR" <?= ($old['rol'] ?? '') === 'VENDEDOR' ? 'selected' : '' ?>>Vendedor</option>
                    <option value="ADMIN" <?= ($old['rol'] ?? '') === 'ADMIN' ? 'selected' : '' ?>>Administrador</option>
                </select>
                <?php if (isset($errors['rol'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['rol']) ?></p>
                <?php endif; ?>
                <p class="text-gray-500 text-sm mt-1">
                    <strong>Vendedor:</strong> Acceso al POS y ventas.
                    <strong>Admin:</strong> Acceso completo al sistema.
                </p>
            </div>

            <!-- Estado -->
            <div class="flex items-center">
                <input
                    type="checkbox"
                    id="activo"
                    name="activo"
                    value="1"
                    <?= isset($old['activo']) || !isset($old) ? 'checked' : '' ?>
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                >
                <label for="activo" class="ml-2 text-sm font-medium text-gray-700">
                    Usuario activo
                </label>
            </div>

            <!-- Error general -->
            <?php if (isset($errors['general'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <!-- Botones -->
            <div class="flex gap-3 pt-4">
                <button
                    type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition"
                >
                    Crear Usuario
                </button>
                <a
                    href="<?= url('/admin/usuarios') ?>"
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg text-center transition"
                >
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const type = field.type === 'password' ? 'text' : 'password';
    field.type = type;
}
</script>
