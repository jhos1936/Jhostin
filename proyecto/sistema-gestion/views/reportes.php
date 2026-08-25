<?php include __DIR__ . '/dashboard.php'; ?>

<div class="content">
    <h2>📊 Reportes del Sistema</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 30px;">
        
        <!-- Reporte de Proyectos -->
        <div style="background: white; border-radius: 10px; padding: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; border: 2px solid #e0e0e0;">
            <div style="font-size: 48px; margin-bottom: 15px;">📋</div>
            <h3>Reporte de Proyectos</h3>
            <p style="color: #666; margin-bottom: 20px;">Listado completo de proyectos con estados y presupuestos</p>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <a href="reports/descargar_proyectos.php" class="btn btn-success">💾 Guardar</a>
                <a href="reports/reporte_proyectos.php?imprimir=1" class="btn btn-primary">🖨️ Imprimir</a>
            </div>
        </div>
        
        <!-- Reporte de Clientes -->
        <div style="background: white; border-radius: 10px; padding: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; border: 2px solid #e0e0e0;">
            <div style="font-size: 48px; margin-bottom: 15px;">👥</div>
            <h3>Reporte de Clientes</h3>
            <p style="color: #666; margin-bottom: 20px;">Listado completo de clientes con ID, nombre, email, telefono y direccion</p>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <a href="reports/descargar_clientes.php" class="btn btn-success">💾 Guardar</a>
                <a href="reports/reporte_clientes.php?imprimir=1" class="btn btn-primary">🖨️ Imprimir</a>
            </div>
        </div>
        
    </div>
</div>

</main>
</body>
</html>