<!-- Modal para reportes -->
<div id="reporteModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999;">
    <div style="position:relative; width:90%; height:90%; margin:2% auto; background:white; border-radius:10px; overflow:hidden;">
        <div style="display:flex; justify-content:flex-end; padding:10px; background:#003366;">
            <button onclick="cerrarModal()" style="background:#e94560; color:white; border:none; padding:8px 20px; border-radius:5px; cursor:pointer; font-size:16px;">✕ Cerrar</button>
        </div>
        <iframe id="reporteIframe" src="" style="width:100%; height:calc(100% - 50px); border:none;"></iframe>
    </div>
</div>

<script>
function abrirModal(url) {
    document.getElementById('reporteIframe').src = url;
    document.getElementById('reporteModal').style.display = 'block';
}
function cerrarModal() {
    document.getElementById('reporteModal').style.display = 'none';
    document.getElementById('reporteIframe').src = '';
}
// Cerrar modal al hacer clic fuera del contenido
document.getElementById('reporteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});
</script>