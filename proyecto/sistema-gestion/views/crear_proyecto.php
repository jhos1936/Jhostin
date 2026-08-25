<?php include __DIR__ . '/dashboard.php'; ?>

<div class="content">
    <h2><?php echo isset($proyecto) ? '✏️ Editar Proyecto' : '➕ Nuevo Proyecto'; ?></h2>
    
    <form action="index.php?controller=proyecto&action=guardar" method="POST" style="max-width: 600px; margin-top: 20px;">
        <?php if (isset($proyecto) && $proyecto): ?>
            <input type="hidden" name="id" value="<?php echo $proyecto['id']; ?>">
        <?php endif; ?>
        
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Nombre del Proyecto:</label>
            <input type="text" name="nombre" value="<?php echo isset($proyecto) ? htmlspecialchars($proyecto['nombre']) : ''; ?>" required
                   style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
        </div>
        
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Descripción:</label>
            <textarea name="descripcion" rows="3"
                      style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;"><?php echo isset($proyecto) ? htmlspecialchars($proyecto['descripcion']) : ''; ?></textarea>
        </div>
        
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Cliente:</label>
            <select name="cliente_id" required
                    style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
                <option value="">Seleccionar cliente...</option>
                <?php 
                // Obtener clientes para el select
                require_once __DIR__ . '/../models/Cliente.php';
                $clienteModel = new Cliente();
                $clientes = $clienteModel->obtenerTodos();
                foreach ($clientes as $c): 
                ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo (isset($proyecto) && $proyecto['cliente_id'] == $c['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Estado:</label>
            <select name="estado" required
                    style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
                <option value="pendiente" <?php echo (isset($proyecto) && $proyecto['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                <option value="en_progreso" <?php echo (isset($proyecto) && $proyecto['estado'] == 'en_progreso') ? 'selected' : ''; ?>>En Progreso</option>
                <option value="completado" <?php echo (isset($proyecto) && $proyecto['estado'] == 'completado') ? 'selected' : ''; ?>>Completado</option>
                <option value="cancelado" <?php echo (isset($proyecto) && $proyecto['estado'] == 'cancelado') ? 'selected' : ''; ?>>Cancelado</option>
            </select>
        </div>
        
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Presupuesto:</label>
            <input type="number" step="0.01" name="presupuesto" value="<?php echo isset($proyecto) ? $proyecto['presupuesto'] : '0.00'; ?>"
                   style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
        </div>
        
        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
            <div class="form-group" style="flex: 1;">
                <label>Fecha Inicio:</label>
                <input type="date" name="fecha_inicio" value="<?php echo isset($proyecto) ? $proyecto['fecha_inicio'] : ''; ?>" required
                       style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
            </div>
            <div class="form-group" style="flex: 1;">
                <label>Fecha Fin:</label>
                <input type="date" name="fecha_fin" value="<?php echo isset($proyecto) ? $proyecto['fecha_fin'] : ''; ?>"
                       style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
            </div>
        </div>
        
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-primary">💾 Guardar</button>
            <a href="index.php?controller=proyecto&action=listar" class="btn" style="background: #95a5a6; color: white;">Cancelar</a>
        </div>
    </form>
</div>

</main>
</body>
</html>