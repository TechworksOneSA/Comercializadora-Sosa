<div class="card" style="max-width: 900px; margin: 0 auto;">
  <!-- HEADER con gradiente -->
  <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem;">
    <h1 class="card-title" style="color: white; font-size: 1.75rem; font-weight: 700; margin: 0;">
      ➕ Nuevo Cliente
    </h1>
    <p style="color: rgba(255,255,255,0.9); margin: 0.5rem 0 0 0; font-size: 0.95rem;">
      Completa los datos del cliente para registrarlo en el sistema
    </p>
  </div>

  <!-- ERRORES -->
  <?php if (!empty($errors)): ?>
    <div style="margin: 1.5rem; padding: 1rem 1.5rem; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 0.5rem; color: #721c24;">
      <strong>⚠️ Errores:</strong>
      <ul style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
        <?php foreach ($errors as $error): ?>
          <li><?= e($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- FORMULARIO -->
  <form 
    method="POST" 
    action="<?= url('/admin/clientes/guardar') ?>" 
    style="padding: 2rem;"
  >
    
    <div style="display: grid; gap: 1.5rem;">
      
      <!-- NIT -->
      <div>
        <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 0.5rem; font-size: 0.95rem;">
          NIT <span style="color: #dc3545;">*</span>
        </label>
        <input
          type="text"
          name="nit"
          value="<?= e($old['nit'] ?? '') ?>"
          placeholder="Ej: 12345678-9"
          required
          style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem; transition: border-color 0.3s;"
          onfocus="this.style.borderColor='#667eea'"
          onblur="this.style.borderColor='#e9ecef'"
        >
      </div>

      <!-- NOMBRE -->
      <div>
        <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 0.5rem; font-size: 0.95rem;">
          Nombre Completo <span style="color: #dc3545;">*</span>
        </label>
        <input
          type="text"
          name="nombre"
          value="<?= e($old['nombre'] ?? '') ?>"
          placeholder="Ej: Juan Pérez García"
          required
          style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem; transition: border-color 0.3s;"
          onfocus="this.style.borderColor='#667eea'"
          onblur="this.style.borderColor='#e9ecef'"
        >
      </div>

      <!-- DIRECCIÓN -->
      <div>
        <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 0.5rem; font-size: 0.95rem;">
          Dirección
        </label>
        <textarea
          name="direccion"
          rows="3"
          placeholder="Ej: Zona 1, 5ta Avenida 10-50, Ciudad de Guatemala"
          style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem; font-family: inherit; resize: vertical; transition: border-color 0.3s;"
          onfocus="this.style.borderColor='#667eea'"
          onblur="this.style.borderColor='#e9ecef'"
        ><?= e($old['direccion'] ?? '') ?></textarea>
      </div>

      <!-- TELÉFONO Y CORREO (Grid 2 columnas) -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        
        <!-- TELÉFONO -->
        <div>
          <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 0.5rem; font-size: 0.95rem;">
            Teléfono
          </label>
          <input
            type="text"
            name="telefono"
            value="<?= e($old['telefono'] ?? '') ?>"
            placeholder="Ej: 5512-3456"
            style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem; transition: border-color 0.3s;"
            onfocus="this.style.borderColor='#667eea'"
            onblur="this.style.borderColor='#e9ecef'"
          >
        </div>

        <!-- CORREO -->
        <div>
          <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 0.5rem; font-size: 0.95rem;">
            Correo Electrónico
          </label>
          <input
            type="email"
            name="correo"
            value="<?= e($old['correo'] ?? '') ?>"
            placeholder="Ej: cliente@email.com"
            style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #e9ecef; border-radius: 0.5rem; font-size: 0.95rem; transition: border-color 0.3s;"
            onfocus="this.style.borderColor='#667eea'"
            onblur="this.style.borderColor='#e9ecef'"
          >
        </div>
      </div>

    </div>

    <!-- BOTONES -->
    <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #e9ecef;">
      <a 
        href="<?= url('/admin/clientes') ?>" 
        style="padding: 0.75rem 1.5rem; background: #6c757d; color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600; transition: all 0.3s;"
        onmouseover="this.style.background='#5a6268'"
        onmouseout="this.style.background='#6c757d'"
      >
        ← Cancelar
      </a>
      <button 
        type="submit"
        style="padding: 0.75rem 2rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3); transition: all 0.3s;"
        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(102, 126, 234, 0.4)'"
        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(102, 126, 234, 0.3)'"
      >
        💾 Guardar Cliente
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
  input:focus, textarea:focus {
    outline: none;
  }
</style>
