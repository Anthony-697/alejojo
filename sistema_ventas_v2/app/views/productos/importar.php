<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>📥 Importar Productos (CSV)</h2>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    
    <p>Sube tu archivo CSV con el siguiente formato:</p>
    
    <div style="background: #0f172a; padding: 1rem; border-radius: 8px; margin: 1rem 0; font-family: monospace; font-size: 12px;">
        codigo,nombre,precio_compra,precio_venta,stock,campana_id<br>
        P001,Producto Ejemplo,10.00,25.00,50,1<br>
        P002,Otro Producto,15.00,35.00,30,1
    </div>
    
    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= h($mensaje) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?= h($error) ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="archivo" accept=".csv" required>
        <button type="submit" name="subir">📤 Subir Archivo</button>
    </form>
    
    <p style="margin-top: 1rem; font-size: 0.8rem; color: #94a3b8;">
        Nota: Si el código ya existe, se actualizará el stock sumando la cantidad.
    </p>
    
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>