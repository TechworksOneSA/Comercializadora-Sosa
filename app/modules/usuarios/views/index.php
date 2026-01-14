<!-- Vista: Listado de Usuarios -->
<div class="max-w-7xl mx-auto p-6">

    <!-- Header con título y botón -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Gestión de Usuarios</h1>
            <p class="text-gray-600 mt-1">Administra los usuarios del sistema</p>
        </div>
        <a href="<?= url('/admin/usuarios/crear') ?>"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo Usuario
        </a>
    </div>

    <!-- Mensajes de éxito/error -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Tarjetas de estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Usuarios</p>
                    <p class="text-2xl font-bold text-gray-800">-1</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Activos</p>
                    <p class="text-2xl font-bold text-green-600">-1</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Administradores</p>
                    <p class="text-2xl font-bold text-purple-600">-1</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Vendedores</p>
                    <p class="text-2xl font-bold text-orange-600">-1</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de usuarios -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No hay usuarios registrados
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                                        <?php if (!empty($usuario['foto'])): ?>
                                            <img src="<?= htmlspecialchars($usuario['foto']) ?>" class="w-full h-full object-cover" alt="Foto de <?= htmlspecialchars($usuario['nombre']) ?>">
                                        <?php else: ?>
                                            <div class="w-full h-full bg-blue-100 rounded-full flex items-center justify-center">
                                                <span class="text-blue-600 font-semibold text-sm">
                                                    <?= strtoupper(substr($usuario['nombre'], 0, 2)) ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($usuario['nombre']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars($usuario['email']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($usuario['rol'] === 'ADMIN'): ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Administrador
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Vendedor
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($usuario['activo'] == 1): ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('d/m/Y', strtotime($usuario['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex justify-center gap-2">
                                    <a href="<?= url('/admin/usuarios/editar/' . $usuario['id']) ?>"
                                        class="text-blue-600 hover:text-blue-900" title="Editar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <?php if ($usuario['id'] != $_SESSION['user']['id']): ?>
                                        <form method="POST" action="<?= url('/admin/usuarios/cambiar-estado/' . $usuario['id']) ?>" class="inline estado-form">
                                            <button type="button"
                                                class="<?= $usuario['activo'] == 1 ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' ?> estado-btn"
                                                title="<?= $usuario['activo'] == 1 ? 'Desactivar' : 'Activar' ?>"
                                                data-usuario-nombre="<?= htmlspecialchars($usuario['nombre']) ?>"
                                                data-accion="<?= $usuario['activo'] == 1 ? 'desactivar' : 'activar' ?>">
                                                <?php if ($usuario['activo'] == 1): ?>
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                <?php else: ?>
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                <?php endif; ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Confirmación -->
<div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full" id="modal-icon">
                <!-- El icono se insertará dinámicamente -->
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4" id="modal-title">Confirmar acción</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="modal-message">
                    ¿Estás seguro de realizar esta acción?
                </p>
            </div>
            <div class="items-center px-4 py-3 flex justify-center gap-3">
                <button id="modal-confirm" class="btn-modal btn-modal-confirm">
                    Confirmar
                </button>
                <button id="modal-cancel" class="btn-modal btn-modal-cancel">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos base para botones del modal */
    .btn-modal {
        @apply px-4 py-2 text-base font-medium rounded-md focus:outline-none focus:ring-2 transition-colors duration-200;
    }

    /* Botón de confirmación - estado desactivar (rojo) */
    .btn-modal-confirm.estado-desactivar {
        @apply bg-red-500 text-white hover:bg-red-600 focus:ring-red-300;
    }

    /* Botón de confirmación - estado activar (verde) */
    .btn-modal-confirm.estado-activar {
        @apply bg-green-500 text-white hover:bg-green-600 focus:ring-green-300;
    }

    /* Botón de cancelar */
    .btn-modal-cancel {
        @apply bg-gray-300 text-gray-700 hover:bg-gray-400 focus:ring-gray-300;
    }

    /* Iconos del modal */
    .modal-icon-desactivar {
        @apply mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100;
    }

    .modal-icon-activar {
        @apply mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('confirmModal');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const modalIcon = document.getElementById('modal-icon');
        const modalConfirm = document.getElementById('modal-confirm');
        const modalCancel = document.getElementById('modal-cancel');

        let currentForm = null;

        // SVG Icons
        const iconDesactivar = `
        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z"/>
        </svg>
    `;

        const iconActivar = `
        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    `;

        // Manejar clicks en botones de estado
        document.querySelectorAll('.estado-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const usuarioNombre = this.dataset.usuarioNombre;
                const accion = this.dataset.accion;
                currentForm = this.closest('.estado-form');

                // Limpiar clases previas
                modalConfirm.className = 'btn-modal btn-modal-confirm';
                modalIcon.className = '';

                // Configurar el modal según la acción
                if (accion === 'desactivar') {
                    modalTitle.textContent = 'Desactivar Usuario';
                    modalMessage.textContent = `¿Estás seguro de desactivar al usuario "${usuarioNombre}"?`;
                    modalIcon.innerHTML = iconDesactivar;
                    modalIcon.className = 'modal-icon-desactivar';
                    modalConfirm.classList.add('estado-desactivar');
                    modalConfirm.textContent = 'Desactivar';
                } else {
                    modalTitle.textContent = 'Activar Usuario';
                    modalMessage.textContent = `¿Estás seguro de activar al usuario "${usuarioNombre}"?`;
                    modalIcon.innerHTML = iconActivar;
                    modalIcon.className = 'modal-icon-activar';
                    modalConfirm.classList.add('estado-activar');
                    modalConfirm.textContent = 'Activar';
                }

                // Mostrar modal
                modal.classList.remove('hidden');
                modalConfirm.focus();
            });
        });

        // Confirmar acción
        modalConfirm.addEventListener('click', function() {
            if (currentForm) {
                currentForm.submit();
            }
            modal.classList.add('hidden');
        });

        // Cancelar acción
        modalCancel.addEventListener('click', function() {
            modal.classList.add('hidden');
            currentForm = null;
        });

        // Cerrar modal al hacer click fuera de él
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
                currentForm = null;
            }
        });

        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
                currentForm = null;
            }
        });
    });
</script>
