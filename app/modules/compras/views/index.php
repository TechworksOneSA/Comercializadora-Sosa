<style>
  .compras-container {
    padding: 2rem;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    min-height: 100vh;
  }

  .compras-header {
    background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%);
    padding: 1.75rem;
    border-radius: 1rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 20px rgba(10, 61, 145, 0.3);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .compras-header h1 {
    font-size: 1.875rem;
    font-weight: 700;
    color: white;
    margin: 0;
  }

  .compras-header p {
    color: rgba(255, 255, 255, 0.9);
    margin-top: 0.5rem;
    font-size: 0.95rem;
  }

  .btn-nueva-compra {
    background: white;
    color: #0a3d91;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .btn-nueva-compra:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
  }

  .compras-filters {
    background: white;
    padding: 1.5rem;
    border-radius: 1rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    position: relative;
  }

  .search-wrapper {
    position: relative;
    width: 100%;
  }

  .search-icon {
    position: absolute;
    left: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.25rem;
    color: #64748b;
    pointer-events: none;
  }

  .compras-filters input {
    width: 100%;
    padding: 1rem 1rem 1rem 3.5rem;
    border: 2px solid #e2e8f0;
    border-radius: 0.75rem;
    font-size: 1rem;
    transition: all 0.3s;
  }

  .compras-filters input:focus {
    outline: none;
    border-color: #0a3d91;
    box-shadow: 0 0 0 4px rgba(10, 61, 145, 0.1);
  }

  .compras-filters input::placeholder {
    color: #94a3b8;
  }

  .compras-table-container {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  }

  .compras-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 0;
  }

  .compras-table thead {
    background: linear-gradient(135deg, #0a3d91 0%, #1565c0 100%);
  }

  .compras-table thead tr th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.9rem;
    color: white;
    border: none;
  }

  .compras-table thead tr th:first-child {
    border-radius: 0.5rem 0 0 0;
  }

  .compras-table thead tr th:last-child {
    border-radius: 0 0.5rem 0 0;
  }

  .compras-table tbody tr {
    border-bottom: 1px solid #e2e8f0;
    transition: all 0.2s;
  }

  .compras-table tbody tr:hover {
    background: #f8fafc;
    transform: scale(1.01);
  }

  .compras-table tbody tr:last-child {
    border-bottom: none;
  }

  .compras-table tbody td {
    padding: 1rem;
    color: #1e293b;
  }

  .compras-id {
    font-weight: 700;
    color: #0a3d91;
    font-size: 1rem;
  }

  .compras-fecha {
    color: #64748b;
    font-size: 0.9rem;
  }

  .compras-numero {
    font-family: 'Courier New', monospace;
    font-weight: 600;
    color: #475569;
  }

  .compras-total {
    font-weight: 700;
    font-size: 1.1rem;
    color: #10b981;
  }

  .badge-status {
    display: inline-block;
    padding: 0.375rem 0.875rem;
    border-radius: 0.5rem;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
  }

  .badge-status--pendiente {
    background: #fef3c7;
    color: #92400e;
  }

  .badge-status--cerrada {
    background: #d1fae5;
    color: #065f46;
  }

  .badge-status--anulada {
    background: #fee2e2;
    color: #991b1b;
  }

  .compras-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
  }

  .btn-action {
    padding: 0.5rem 0.875rem;
    border-radius: 0.5rem;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
  }

  .btn-editar {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
  }

  .btn-editar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
  }

  .btn-ver {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
  }

  .btn-ver:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
  }

  .btn-eliminar {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
  }

  .btn-eliminar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
  }

  .compras-empty {
    text-align: center;
    padding: 3rem;
    color: #64748b;
    font-size: 1.1rem;
  }

  .compras-empty-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
  }
</style>

<div class="compras-container">
  <div class="compras-header">
    <div>
      <h1>📦 Compras e Inventario</h1>
      <p>Gestione las compras a proveedores y actualizaciones de stock</p>
    </div>
    <a href="<?= url('/admin/compras/crear') ?>" class="btn-nueva-compra">
      ➕ Nueva Compra
    </a>
  </div>

  <div class="compras-filters">
    <div class="search-wrapper">
      <span class="search-icon">🔍</span>
      <input
        type="text"
        id="searchInput"
        placeholder="Buscar por número de documento, proveedor o fecha...">
    </div>
  </div>

  <div class="compras-table-container">
    <table class="compras-table" id="comprasTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Fecha</th>
          <th>Proveedor</th>
          <th>Número Doc.</th>
          <th>Total</th>
          <th style="text-align: center;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($compras)): ?>
          <?php foreach ($compras as $c): ?>
            <?php
            $fechaRaw  = $c['fecha_compra'] ?? ($c['fecha'] ?? ($c['created_at'] ?? ''));
            $numero    = $c['numero_doc']   ?? ($c['documento'] ?? '');
            $total     = $c['total_neto']   ?? ($c['total'] ?? 0);
            $proveedor = $c['proveedor_nombre'] ?? 'Sin proveedor';
            $proveedorNit = $c['proveedor_nit'] ?? '';

            $fecha = $fechaRaw;
            if ($fechaRaw && strlen($fechaRaw) >= 10) {
              $fecha = substr($fechaRaw, 0, 10);
            }
            ?>
            <tr>
              <td class="compras-id">#<?= htmlspecialchars($c['id']) ?></td>
              <td class="compras-fecha">📅 <?= htmlspecialchars($fecha) ?></td>
              <td>
                <div class="producto-nombre"><?= htmlspecialchars($proveedor) ?></div>
                <?php if ($proveedorNit): ?>
                  <div class="producto-sku">NIT: <?= htmlspecialchars($proveedorNit) ?></div>
                <?php endif; ?>
              </td>
              <td class="compras-numero"><?= htmlspecialchars($numero ?: 'Sin número') ?></td>
              <td class="compras-total">Q <?= number_format((float)$total, 2) ?></td>
              <td>
                <div class="compras-actions">
                  <a href="<?= url('/admin/compras/editar/' . $c['id']) ?>"
                    class="btn-action btn-editar"
                    title="Editar compra">
                    ✏️ Editar
                  </a>
                  <a href="<?= url('/admin/compras/ver/' . $c['id']) ?>"
                    class="btn-action btn-ver"
                    title="Ver detalles">
                    👁️ Ver
                  </a>
                  <button
                    class="btn-action btn-eliminar"
                    onclick="confirmarEliminar(<?= $c['id'] ?>)"
                    title="Eliminar compra">
                    🗑️ Eliminar
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="compras-empty">
              <div class="compras-empty-icon">📦</div>
              <div>No hay compras registradas aún.</div>
              <div style="margin-top: 0.5rem; font-size: 0.9rem;">Cree su primera compra haciendo clic en el botón "Nueva Compra"</div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  // Búsqueda en tiempo real
  const searchInput = document.getElementById('searchInput');
  const table = document.getElementById('comprasTable');
  const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

  searchInput.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase().trim();

    Array.from(rows).forEach(row => {
      // Saltar la fila de "no hay compras"
      if (row.cells.length === 1 && row.cells[0].classList.contains('compras-empty')) {
        return;
      }

      const id = row.cells[0].textContent.toLowerCase();
      const fecha = row.cells[1].textContent.toLowerCase();
      const proveedor = row.cells[2].textContent.toLowerCase();
      const numero = row.cells[3].textContent.toLowerCase();
      const total = row.cells[4].textContent.toLowerCase();

      const matches = id.includes(searchTerm) ||
        fecha.includes(searchTerm) ||
        proveedor.includes(searchTerm) ||
        numero.includes(searchTerm) ||
        total.includes(searchTerm);

      row.style.display = matches ? '' : 'none';
    });
  });

  // Variable global para el modal
  let compraIdEliminar = null;

  function cerrarModalEliminarCompra() {
    const modal = document.getElementById('modalEliminarCompra');
    modal.classList.remove('show');
    document.body.style.overflow = '';
    document.removeEventListener('keydown', cerrarEliminarCompraConEscape);
    compraIdEliminar = null;
  }

  function cerrarEliminarCompraConEscape(e) {
    if (e.key === 'Escape') {
      cerrarModalEliminarCompra();
    }
  }

  function confirmarEliminarCompra() {
    if (compraIdEliminar) {
      window.location.href = '<?= url("/admin/compras/eliminar/") ?>' + compraIdEliminar;
    }
  }

  // Función para confirmar eliminación
  function confirmarEliminar(id) {
    compraIdEliminar = id;
    const modal = document.getElementById('modalEliminarCompra');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';

    // Cerrar con ESC
    document.addEventListener('keydown', cerrarEliminarCompraConEscape);

    // Cerrar al hacer clic fuera del modal
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        cerrarModalEliminarCompra();
      }
    });
  }
</script>

<!-- Modal de Eliminar Compra -->
<div id="modalEliminarCompra" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-header">
      <div class="modal-icon">
        <svg width="28" height="28" fill="white" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
      </div>
      <h3 class="modal-title">¿Eliminar Compra?</h3>
    </div>
    <div class="modal-body">
      <p class="modal-message">¿Está seguro que desea eliminar esta compra?</p>
      <p class="modal-submessage"><strong>⚠️ Esta acción revertirá el inventario y no se puede deshacer.</strong></p>
    </div>
    <div class="modal-actions">
      <button type="button" class="modal-btn modal-btn-cancel" onclick="cerrarModalEliminarCompra()">
        Cancelar
      </button>
      <button type="button" class="modal-btn modal-btn-confirm" onclick="confirmarEliminarCompra()">
        Sí, Eliminar
      </button>
    </div>
  </div>
</div>

<style>
  /* Modal de confirmación */
  .modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    z-index: 10000;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
  }

  .modal-overlay.show {
    display: flex;
    opacity: 1;
  }

  .modal-content {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    max-width: 450px;
    width: 90%;
    transform: translateY(-20px) scale(0.95);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    border: 2px solid rgba(220, 53, 69, 0.1);
  }

  .modal-overlay.show .modal-content {
    transform: translateY(0) scale(1);
  }

  .modal-header {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    padding: 24px 28px;
    border-radius: 14px 14px 0 0;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .modal-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: shimmer 3s ease-in-out infinite;
  }

  @keyframes shimmer {

    0%,
    100% {
      transform: translate(-50%, -50%) rotate(0deg);
    }

    50% {
      transform: translate(-50%, -50%) rotate(180deg);
    }
  }

  .modal-icon {
    width: 64px;
    height: 64px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    position: relative;
    z-index: 1;
  }

  .modal-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 1;
  }

  .modal-body {
    padding: 28px;
    text-align: center;
  }

  .modal-message {
    font-size: 1.1rem;
    color: #495057;
    line-height: 1.6;
    margin: 0 0 8px 0;
    font-weight: 500;
  }

  .modal-submessage {
    font-size: 0.95rem;
    color: #6c757d;
    margin: 0;
    line-height: 1.5;
  }

  .modal-actions {
    padding: 0 28px 28px;
    display: flex;
    gap: 12px;
    justify-content: center;
  }

  .modal-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    min-width: 120px;
  }

  .modal-btn-cancel {
    background: #f8f9fa;
    color: #6c757d;
    border: 2px solid #e9ecef;
  }

  .modal-btn-cancel:hover {
    background: #e9ecef;
    border-color: #dee2e6;
    transform: translateY(-1px);
  }

  .modal-btn-confirm {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
  }

  .modal-btn-confirm:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(220, 53, 69, 0.4);
  }

  .modal-btn-confirm:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
  }
</style>
