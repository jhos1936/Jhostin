<?php 
include __DIR__ . '/dashboard.php'; 
?>

<!-- Modal Nuevo/Editar Proyecto -->
<div id="modalProyecto" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
    <div style="background:#fff; border-radius:12px; padding:30px; width:100%; max-width:560px; box-shadow:0 20px 60px rgba(0,0,0,0.3); position:relative; max-height:90vh; overflow-y:auto;">
        <button onclick="cerrarModal('modalProyecto')" style="position:absolute; top:15px; right:18px; background:none; border:none; font-size:22px; cursor:pointer; color:#888;">✕</button>
        <h3 id="modalProyectoTitulo" style="margin-bottom:20px; color:#2c3e50;">➕ Nuevo Proyecto</h3>

        <form id="formProyecto" action="index.php?controller=proyecto&action=guardar" method="POST">
            <input type="hidden" name="id" id="proyectoId">

            <div class="form-group" style="margin-bottom:14px;">
                <label>Nombre del Proyecto:</label>
                <input type="text" name="nombre" id="proyectoNombre" required
                       style="width:100%; padding:10px; border:2px solid #e0e0e0; border-radius:8px; font-size:14px;">
            </div>
            <div class="form-group" style="margin-bottom:14px;">
                <label>Descripción:</label>
                <textarea name="descripcion" id="proyectoDescripcion" rows="3"
                          style="width:100%; padding:10px; border:2px solid #e0e0e0; border-radius:8px; font-size:14px;"></textarea>
            </div>
            <div class="form-group" style="margin-bottom:14px;">
                <label>Cliente:</label>
                <select name="cliente_id" id="proyectoClienteId" required
                        style="width:100%; padding:10px; border:2px solid #e0e0e0; border-radius:8px; font-size:14px;">
                    <option value="">Seleccionar cliente...</option>
                    <?php 
                    require_once __DIR__ . '/../models/Cliente.php';
                    $clienteModel = new Cliente();
                    $clientesLista = $clienteModel->obtenerTodos();
                    foreach ($clientesLista as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:14px;">
                <label>Estado:</label>
                <select name="estado" id="proyectoEstado" required
                        style="width:100%; padding:10px; border:2px solid #e0e0e0; border-radius:8px; font-size:14px;">
                    <option value="pendiente">Pendiente</option>
                    <option value="en_progreso">En Progreso</option>
                    <option value="completado">Completado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:14px;">
                <label>Presupuesto:</label>
                <input type="number" step="0.01" name="presupuesto" id="proyectoPresupuesto" value="0.00"
                       style="width:100%; padding:10px; border:2px solid #e0e0e0; border-radius:8px; font-size:14px;">
            </div>
            <div style="display:flex; gap:15px; margin-bottom:14px;">
                <div class="form-group" style="flex:1;">
                    <label>Fecha Inicio:</label>
                    <input type="date" name="fecha_inicio" id="proyectoFechaInicio" required
                           style="width:100%; padding:10px; border:2px solid #e0e0e0; border-radius:8px; font-size:14px;">
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Fecha Fin:</label>
                    <input type="date" name="fecha_fin" id="proyectoFechaFin"
                           style="width:100%; padding:10px; border:2px solid #e0e0e0; border-radius:8px; font-size:14px;">
                </div>
            </div>
            <div style="display:flex; gap:10px; margin-top:20px;">
                <button type="submit" class="btn btn-primary">💾 Guardar</button>
                <button type="button" onclick="cerrarModal('modalProyecto')" class="btn" style="background:#95a5a6; color:white;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div class="content">
    <div class="header-actions">
        <h2>📊 <?php echo ($rol === 'cliente') ? 'Mis Proyectos' : 'Gestión de Proyectos'; ?></h2>
        <div style="display:flex; gap:8px; align-items:center;">
            <?php if ($es_editor): ?>
                <button onclick="abrirModalNuevoProyecto()" class="btn btn-primary">+ Nuevo Proyecto</button>
            <?php endif; ?>
            <a href="reports/reporte_proyectos.php" target="_blank" onclick="window.open('reports/reporte_proyectos.php','_blank'); return false;" class="btn" style="background:#e67e22; color:white;">🖨️ Imprimir</a>
            <a href="reports/descargar_proyectos.php" class="btn" style="background:#2980b9; color:white;">⬇️ Guardar</a>
        </div>
    </div>

    <?php if ($rol === 'cliente'): ?>
        <div style="background:#eaf4fb; border-left:4px solid #2980b9; padding:10px 16px; border-radius:6px; margin-bottom:18px; font-size:13px; color:#2471a3;">
            ℹ️ Solo puedes ver y descargar el reporte de <strong>tus propios proyectos</strong>.
        </div>
    <?php endif; ?>

    <?php if (isset($proyectos) && count($proyectos) > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th><th>Nombre</th><th>Cliente</th><th>Estado</th>
                    <th>Presupuesto</th><th>Inicio</th><th>Fin</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($proyectos as $proyecto): ?>
                <tr>
                    <td><?php echo $proyecto['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($proyecto['nombre']); ?></strong></td>
                    <td><?php echo htmlspecialchars($proyecto['cliente_nombre'] ?? 'N/A'); ?></td>
                    <td>
                        <span class="status-<?php echo $proyecto['estado']; ?>">
                            <?php echo str_replace('_', ' ', $proyecto['estado']); ?>
                        </span>
                    </td>
                    <td>$ <?php echo number_format($proyecto['presupuesto'], 2); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($proyecto['fecha_inicio'])); ?></td>
                    <td><?php echo $proyecto['fecha_fin'] ? date('d/m/Y', strtotime($proyecto['fecha_fin'])) : 'N/A'; ?></td>
                    <td>
                        <?php if ($es_editor): ?>
                            <a href="javascript:void(0)"
                               onclick="abrirModalEditarProyecto(
                                   <?php echo $proyecto['id']; ?>,
                                   '<?php echo addslashes(htmlspecialchars($proyecto['nombre'])); ?>',
                                   '<?php echo addslashes(htmlspecialchars($proyecto['descripcion'] ?? '')); ?>',
                                   '<?php echo $proyecto['cliente_id']; ?>',
                                   '<?php echo $proyecto['estado']; ?>',
                                   '<?php echo $proyecto['presupuesto']; ?>',
                                   '<?php echo $proyecto['fecha_inicio']; ?>',
                                   '<?php echo $proyecto['fecha_fin'] ?? ''; ?>'
                               )" class="btn-edit">✏️ Editar</a>
                        <?php endif; ?>
                        <?php if ($es_admin): ?>
                            <a href="index.php?controller=proyecto&action=eliminar&id=<?php echo $proyecto['id']; ?>"
                               class="btn-delete" onclick="return confirm('¿Estás seguro?')">🗑️ Eliminar</a>
                        <?php endif; ?>
                        <?php if ($rol === 'cliente'): ?>
                            <span style="color:#7f8c8d; font-size:12px;">Solo lectura</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align:center; padding:50px; color:#888;">
            <p style="font-size:48px;">📭</p>
            <h3><?php echo ($rol === 'cliente') ? 'No tienes proyectos asignados aún' : 'No hay proyectos registrados'; ?></h3>
        </div>
    <?php endif; ?>
</div>

<script>
function abrirModalNuevoProyecto() {
    document.getElementById('modalProyectoTitulo').textContent = '➕ Nuevo Proyecto';
    document.getElementById('proyectoId').value = '';
    document.getElementById('proyectoNombre').value = '';
    document.getElementById('proyectoDescripcion').value = '';
    document.getElementById('proyectoClienteId').value = '';
    document.getElementById('proyectoEstado').value = 'pendiente';
    document.getElementById('proyectoPresupuesto').value = '0.00';
    document.getElementById('proyectoFechaInicio').value = '';
    document.getElementById('proyectoFechaFin').value = '';
    document.getElementById('modalProyecto').style.display = 'flex';
}

function abrirModalEditarProyecto(id, nombre, descripcion, clienteId, estado, presupuesto, fechaInicio, fechaFin) {
    document.getElementById('modalProyectoTitulo').textContent = '✏️ Editar Proyecto';
    document.getElementById('proyectoId').value = id;
    document.getElementById('proyectoNombre').value = nombre;
    document.getElementById('proyectoDescripcion').value = descripcion;
    document.getElementById('proyectoClienteId').value = clienteId;
    document.getElementById('proyectoEstado').value = estado;
    document.getElementById('proyectoPresupuesto').value = presupuesto;
    document.getElementById('proyectoFechaInicio').value = fechaInicio;
    document.getElementById('proyectoFechaFin').value = fechaFin;
    document.getElementById('modalProyecto').style.display = 'flex';
}

function cerrarModal(id) {
    document.getElementById(id).style.display = 'none';
}

document.getElementById('modalProyecto').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal('modalProyecto');
});
</script>

</main>
</body>
</html>
