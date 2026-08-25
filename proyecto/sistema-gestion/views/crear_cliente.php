<?php include __DIR__ . '/dashboard.php'; ?>

<div class="content">
    <h2><?php echo isset($cliente) ? '✏️ Editar Cliente' : '➕ Nuevo Cliente'; ?></h2>
    
    <form action="index.php?controller=cliente&action=guardar" method="POST" style="max-width: 600px; margin-top: 20px;">
        <?php if (isset($cliente) && $cliente): ?>
            <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">
        <?php endif; ?>
        
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?php echo isset($cliente) ? htmlspecialchars($cliente['nombre']) : ''; ?>" required 
                   style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
        </div>
        
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo isset($cliente) ? htmlspecialchars($cliente['email']) : ''; ?>" required
                   style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
        </div>
        
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Teléfono:</label>
            <input type="text" name="telefono" value="<?php echo isset($cliente) ? htmlspecialchars($cliente['telefono']) : ''; ?>"
                   style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;">
        </div>
        
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Dirección:</label>
            <textarea name="direccion" rows="3"
                      style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px;"><?php echo isset($cliente) ? htmlspecialchars($cliente['direccion']) : ''; ?></textarea>
        </div>
        
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-primary">💾 Guardar</button>
            <a href="index.php?controller=cliente&action=listar" class="btn" style="background: #95a5a6; color: white;">Cancelar</a>
        </div>
    </form>
</div>

</main>
</body>
</html>