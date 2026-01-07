<?php
$proveedores = $proveedores ?? [];
?>

<style>
  /* Proveedores - Vista de Tarjetas Premium */
  .proveedores-container {
    padding: 28px;
    background: var(--surface);
  }

  .proveedores-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 32px;
    padding-bottom: 20px;
    border-bottom: 2px solid rgba(10, 61, 145, 0.1);
  }

  .proveedores-header-content h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--primary);
    margin: 0 0 8px 0;
    letter-spacing: -0.02em;
  }

  .proveedores-header-content p {
    color: var(--text-muted);
    font-size: 15px;
    margin: 0;
  }

  .btn-nuevo-proveedor {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: #ffffff;
    padding: 14px 28px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
    box-shadow: 0 4px 16px rgba(10, 61, 145, 0.25);
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-nuevo-proveedor:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(10, 61, 145, 0.35);
  }

  .proveedores-search {
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(10, 61, 145, 0.08);
    position: relative;
  }

  .search-wrapper {
    position: relative;
    width: 100%;
  }

  .search-icon {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 20px;
    color: #64748b;
    pointer-events: none;
  }

  .search-input {
    width: 100%;
    padding: 14px 20px 14px 52px;
    border: 2px solid rgba(10, 61, 145, 0.15);
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s ease;
    font-weight: 500;
  }

  .search-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(10, 61, 145, 0.1);
  }

  .search-input::placeholder {
    color: #94a3b8;
  }

  .filters-container {
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(10, 61, 145, 0.08);
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .filters-label {
    font-weight: 600;
    color: var(--primary);
    font-size: 14px;
  }

  .filter-buttons {
    display: flex;
    gap: 8px;
  }

  .filter-btn {
    padding: 8px 20px;
    border: 2px solid rgba(10, 61, 145, 0.2);
    background: #ffffff;
    color: var(--text);
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .filter-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
  }

  .filter-btn.active {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: #ffffff;
    border-color: transparent;
    box-shadow: 0 2px 8px rgba(10, 61, 145, 0.25);
  }

  .alert-success-prov {
    background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
    border-left: 4px solid #4caf50;
    color: #2e7d32;
    padding: 16px 20px;
    border-radius: 10px;
    margin-bottom: 24px;
    font-weight: 500;
    box-shadow: 0 2px 8px rgba(76, 175, 80, 0.15);
  }

  .proveedores-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 24px;
    margin-top: 24px;
  }

  .proveedor-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 16px rgba(10, 61, 145, 0.08);
    border: 1px solid rgba(10, 61, 145, 0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .proveedor-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
    transform: scaleX(0);
    transition: transform 0.3s ease;
  }

  .proveedor-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(10, 61, 145, 0.15);
    border-color: rgba(10, 61, 145, 0.15);
  }

  .proveedor-card:hover::before {
    transform: scaleX(1);
  }

  .proveedor-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
  }

  .proveedor-id {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: #ffffff;
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
    box-shadow: 0 4px 12px rgba(10, 61, 145, 0.25);
  }

  .proveedor-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .proveedor-status.activo {
    background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
    color: #2e7d32;
  }

  .proveedor-status.activo::before {
    content: '●';
    color: #4caf50;
    font-size: 14px;
  }

  .proveedor-status.inactivo {
    background: linear-gradient(135deg, #fce4ec 0%, #f3e5f5 100%);
    color: #c2185b;
  }

  .proveedor-status.inactivo::before {
    content: '●';
    color: #e91e63;
    font-size: 14px;
  }

  .proveedor-nombre {
    font-size: 20px;
    font-weight: 700;
    color: var(--primary);
    margin: 0 0 8px 0;
    line-height: 1.3;
  }

  .proveedor-nit {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    background: rgba(10, 61, 145, 0.06);
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    color: var(--primary);
    margin-bottom: 16px;
  }

  .proveedor-info {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid rgba(10, 61, 145, 0.1);
  }

  .proveedor-info-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    color: var(--text);
  }

  .proveedor-info-icon {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, rgba(10, 61, 145, 0.08) 0%, rgba(25, 118, 210, 0.08) 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-weight: 600;
    font-size: 12px;
    flex-shrink: 0;
  }

  .proveedor-info-text {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .proveedor-actions {
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid rgba(10, 61, 145, 0.1);
    display: flex;
    justify-content: flex-end;
    gap: 8px;
  }

  .btn-editar {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: #ffffff;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(10, 61, 145, 0.2);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .btn-editar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(10, 61, 145, 0.3);
  }

  .btn-cambiar-estado {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #ffffff;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2);
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .btn-cambiar-estado:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
  }

  .btn-cambiar-estado.desactivar {
    background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%);
    box-shadow: 0 2px 8px rgba(211, 47, 47, 0.2);
  }

  .btn-cambiar-estado.desactivar:hover {
    box-shadow: 0 4px 12px rgba(211, 47, 47, 0.3);
  }

  .proveedores-empty {
    text-align: center;
    padding: 80px 20px;
    background: #ffffff;
    border-radius: 16px;
    border: 2px dashed rgba(10, 61, 145, 0.2);
  }

  .proveedores-empty-icon {
    font-size: 64px;
    color: rgba(10, 61, 145, 0.3);
    margin-bottom: 16px;
  }

  .proveedores-empty h3 {
    font-size: 20px;
    color: var(--text);
    margin: 0 0 8px 0;
  }

  .proveedores-empty p {
    color: var(--text-muted);
    margin: 0;
  }
</style>

<div class="proveedores-container">
  <div class="proveedores-header">
    <div class="proveedores-header-content">
      <h1>Proveedores</h1>
      <p>Catálogo de proveedores para gestión de compras</p>
    </div>
    <a href="<?= url('/admin/proveedores/crear') ?>" class="btn-nuevo-proveedor">
      <span>+</span>
      <span>Nuevo Proveedor</span>
    </a>
  </div>

  <?php if (isset($_GET['ok'])): ?>
    <?php if ($_GET['ok'] === 'creado'): ?>
      <div class="alert-success-prov">
        ✓ Proveedor creado correctamente
      </div>
    <?php elseif ($_GET['ok'] === 'actualizado'): ?>
      <div class="alert-success-prov">
        ✓ Proveedor actualizado correctamente
      </div>
    <?php elseif ($_GET['ok'] === 'eliminado'): ?>
      <div class="alert-success-prov">
        ✓ Proveedor eliminado correctamente
      </div>
    <?php elseif ($_GET['ok'] === 'activado'): ?>
      <div class="alert-success-prov">
        ✓ Proveedor activado correctamente
      </div>
    <?php elseif ($_GET['ok'] === 'desactivado'): ?>
      <div class="alert-success-prov">
        ✓ Proveedor desactivado correctamente
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <?php if (!empty($proveedores)): ?>
    <div class="filters-container">
      <span class="filters-label">Filtrar por:</span>
      <div class="filter-buttons">
        <button class="filter-btn active" data-filter="todos">📋 Todos</button>
        <button class="filter-btn" data-filter="activo">✅ Activos</button>
        <button class="filter-btn" data-filter="inactivo">❌ Inactivos</button>
      </div>
    </div>

    <div class="proveedores-search">
      <div class="search-wrapper">
        <span class="search-icon">🔍</span>
        <input
          type="text"
          id="searchProveedores"
          class="search-input"
          placeholder="Buscar por nombre, NIT, teléfono o correo..."
          autocomplete="off"
        >
      </div>
    </div>
  <?php endif; ?>

  <?php if (empty($proveedores)): ?>
    <div class="proveedores-empty">
      <div class="proveedores-empty-icon">📦</div>
      <h3>No hay proveedores registrados</h3>
      <p>Comienza agregando tu primer proveedor al sistema</p>
    </div>
  <?php else: ?>
    <div class="proveedores-grid">
      <?php foreach ($proveedores as $p): ?>
        <div class="proveedor-card" data-status="<?= !empty($p['activo']) ? 'activo' : 'inactivo' ?>">
          <div class="proveedor-header">
            <div class="proveedor-id">#<?= (int)$p['id'] ?></div>
            <div class="proveedor-status <?= !empty($p['activo']) ? 'activo' : 'inactivo' ?>">
              <?= !empty($p['activo']) ? 'Activo' : 'Inactivo' ?>
            </div>
          </div>

          <h2 class="proveedor-nombre"><?= htmlspecialchars($p['nombre']) ?></h2>
          <div class="proveedor-nit">
            <span>NIT:</span>
            <strong><?= htmlspecialchars($p['nit']) ?></strong>
          </div>

          <div class="proveedor-info">
            <?php if (!empty($p['telefono'])): ?>
              <div class="proveedor-info-item">
                <div class="proveedor-info-icon">📞</div>
                <div class="proveedor-info-text"><?= htmlspecialchars($p['telefono']) ?></div>
              </div>
            <?php endif; ?>

            <?php if (!empty($p['correo'])): ?>
              <div class="proveedor-info-item">
                <div class="proveedor-info-icon">✉️</div>
                <div class="proveedor-info-text"><?= htmlspecialchars($p['correo']) ?></div>
              </div>
            <?php endif; ?>

            <?php if (!empty($p['direccion'])): ?>
              <div class="proveedor-info-item">
                <div class="proveedor-info-icon">📍</div>
                <div class="proveedor-info-text"><?= htmlspecialchars($p['direccion']) ?></div>
              </div>
            <?php endif; ?>
          </div>

          <div class="proveedor-actions">
            <a href="<?= url('/admin/proveedores/editar/' . (int)$p['id']) ?>" class="btn-editar">
              ✏️ Editar
            </a>
            <form method="POST" action="<?= url('/admin/proveedores/cambiar-estado/' . (int)$p['id']) ?>" style="display: inline;">
              <button type="submit" class="btn-cambiar-estado <?= !empty($p['activo']) ? 'desactivar' : '' ?>">
                <?= !empty($p['activo']) ? '🔒 Desactivar' : '✅ Activar' ?>
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script>
// Filtros y búsqueda de proveedores
const searchInput = document.getElementById('searchProveedores');
const filterButtons = document.querySelectorAll('.filter-btn');
const proveedorCards = document.querySelectorAll('.proveedor-card');

let currentFilter = 'todos';

// Función para aplicar filtros
function aplicarFiltros() {
  const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

  proveedorCards.forEach(card => {
    const status = card.getAttribute('data-status');

    // Filtro por estado
    const matchesStatus = currentFilter === 'todos' || status === currentFilter;

    // Filtro por búsqueda
    const nombre = card.querySelector('.proveedor-nombre')?.textContent.toLowerCase() || '';
    const nit = card.querySelector('.proveedor-nit')?.textContent.toLowerCase() || '';

    const infoItems = card.querySelectorAll('.proveedor-info-text');
    let telefono = '';
    let correo = '';
    let direccion = '';

    infoItems.forEach((item) => {
      const text = item.textContent.toLowerCase();
      const icon = item.previousElementSibling?.textContent || '';

      if (icon.includes('📞')) telefono = text;
      if (icon.includes('✉️')) correo = text;
      if (icon.includes('📍')) direccion = text;
    });

    const matchesSearch = !searchTerm ||
                         nombre.includes(searchTerm) ||
                         nit.includes(searchTerm) ||
                         telefono.includes(searchTerm) ||
                         correo.includes(searchTerm) ||
                         direccion.includes(searchTerm);

    // Mostrar solo si cumple ambos filtros
    card.style.display = (matchesStatus && matchesSearch) ? '' : 'none';
  });
}

// Event listeners para filtros de estado
filterButtons.forEach(btn => {
  btn.addEventListener('click', function() {
    filterButtons.forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    currentFilter = this.getAttribute('data-filter');
    aplicarFiltros();
  });
});

// Event listener para búsqueda
if (searchInput) {
  searchInput.addEventListener('input', aplicarFiltros);
}
</script>

