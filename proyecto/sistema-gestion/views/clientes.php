<?php 
include __DIR__ . '/dashboard.php'; 
?>

<!-- Modal Nuevo/Editar Cliente -->
<div id="modalCliente" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
    <div style="background:#fff; border-radius:12px; padding:30px; width:100%; max-width:520px; box-shadow:0 20px 60px rgba(0,0,0,0.3); position:relative; max-height:90vh; overflow-y:auto;">
        <button onclick="cerrarModal('modalCliente')" style="position:absolute; top:15px; right:18px; background:none; border:none; font-size:22px; cursor:pointer; color:#888;">✕</button>
        <h3 id="modalClienteTitulo" style="margin-bottom:20px; color:#2c3e50;">➕ Nuevo Cliente</h3>

        <form id="formCliente" action="index.php?controller=cliente&action=guardar" method="POST">
            <input type="hidden" name="id" id="clienteId">

            <div class="form-group" style="margin-bottom:14px;">
                <label>Nombre:</label>
                <input type="text" name="nombre" id="clienteNombre" required
                       style="width:100%; padding:10px; border:2px solid #e0e0e0; border-radius:8px; font-size:14px;">
            </div>
            <div class="form-group" style="margin-bottom:14px;">
                <label>Email:</label>
                <input type="email" name="email" id="clienteEmail" required
                       style="width:100%; padding:10px; border:2px solid #e0e0e0; border-radius:8px; font-size:14px;">
            </div>
            <div class="form-group" style="margin-bottom:14px;">
                <label>Teléfono:</label>
                <input type="text" name="telefono" id="clienteTelefono"
                       style="width:100%; padding:10px; border:2px solid #e0e0e0; border-radius:8px; font-size:14px;">
            </div>
            <div class="form-group" style="margin-bottom:14px;">
                <label>Dirección:</label>
                <textarea name="direccion" id="clienteDireccion" rows="3"
                          style="width:100%; padding:10px; border:2px solid #e0e0e0; border-radius:8px; font-size:14px;"></textarea>
            </div>
            <div style="display:flex; gap:10px; margin-top:20px;">
                <button type="submit" class="btn btn-primary">💾 Guardar</button>
                <button type="button" onclick="cerrarModal('modalCliente')" class="btn" style="background:#95a5a6; color:white;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div class="content">
    <div class="header-actions">
        <h2>👥 Gestión de Clientes</h2>
        <div style="display:flex; gap:8px; align-items:center;">
            <?php if ($es_editor): ?>
                <button onclick="abrirModalNuevoCliente()" class="btn btn-primary">+ Nuevo Cliente</button>
            <?php endif; ?>
            <a href="reports/reporte_clientes.php" target="_blank" onclick="window.open('reports/reporte_clientes.php','_blank'); return false;" class="btn" style="background:#e67e22; color:white;">🖨️ Imprimir</a>
            <a href="reports/descargar_clientes.php" class="btn" style="background:#2980b9; color:white;">⬇️ Guardar</a>
        </div>
    </div>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'eliminar_fallo'): ?>
        <div style="background:#fdecea; border-left:4px solid #e74c3c; color:#c0392b; padding:14px 18px; border-radius:8px; margin-bottom:16px;">
            ⚠️ <strong>No se pudo eliminar el cliente.</strong> Por favor intenta de nuevo.
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['success']) && $_GET['success'] === 'eliminado'): ?>
        <div style="background:#eafaf1; border-left:4px solid #2ecc71; color:#1e8449; padding:14px 18px; border-radius:8px; margin-bottom:16px;">
            ✅ Cliente eliminado correctamente.
        </div>
    <?php endif; ?>

    <?php if (isset($clientes) && count($clientes) > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Dirección</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?php echo $cliente['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($cliente['nombre'] ?? ''); ?></strong></td>
                    <td><?php echo htmlspecialchars($cliente['email'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($cliente['telefono'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars(substr($cliente['direccion'] ?? '', 0, 30)); ?>...</td>
                    <td>
                        <?php if ($es_editor): ?>
                            <a href="javascript:void(0)"
                               class="btn-edit btn-editar-cliente"
                               data-id="<?php echo $cliente['id']; ?>"
                               data-nombre="<?php echo htmlspecialchars($cliente['nombre'] ?? '', ENT_QUOTES); ?>"
                               data-email="<?php echo htmlspecialchars($cliente['email'] ?? '', ENT_QUOTES); ?>"
                               data-telefono="<?php echo htmlspecialchars($cliente['telefono'] ?? '', ENT_QUOTES); ?>"
                               data-direccion="<?php echo htmlspecialchars($cliente['direccion'] ?? '', ENT_QUOTES); ?>"
                            >✏️ Editar</a>
                        <?php endif; ?>
                        <?php if ($es_admin): ?>
                            <a href="index.php?controller=cliente&action=eliminar&id=<?php echo $cliente['id']; ?>"
                               class="btn-delete" onclick="return confirm('¿Estás seguro de eliminar este cliente?')">🗑️ Eliminar</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align:center; padding:50px; color:#888;">
            <p style="font-size:48px;">📭</p>
            <h3>No hay clientes registrados</h3>
        </div>
    <?php endif; ?>
</div>

<script>
function abrirModalNuevoCliente() {
    document.getElementById('modalClienteTitulo').textContent = '➕ Nuevo Cliente';
    document.getElementById('clienteId').value = '';
    document.getElementById('clienteNombre').value = '';
    document.getElementById('clienteEmail').value = '';
    document.getElementById('clienteTelefono').value = '';
    document.getElementById('clienteDireccion').value = '';
    document.getElementById('modalCliente').style.display = 'flex';
}

// Usar delegación de eventos para evitar problemas con caracteres especiales
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-editar-cliente');
    if (!btn) return;
    document.getElementById('modalClienteTitulo').textContent = '✏️ Editar Cliente';
    document.getElementById('clienteId').value      = btn.dataset.id;
    document.getElementById('clienteNombre').value  = btn.dataset.nombre;
    document.getElementById('clienteEmail').value   = btn.dataset.email;
    document.getElementById('clienteTelefono').value = btn.dataset.telefono;
    document.getElementById('clienteDireccion').value = btn.dataset.direccion;
    document.getElementById('modalCliente').style.display = 'flex';
});

function cerrarModal(id) {
    document.getElementById(id).style.display = 'none';
}

document.getElementById('modalCliente').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal('modalCliente');
});
</script>

</main>
</body>
</html>