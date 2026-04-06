<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>📢 Campañas</h2>

<a href="<?= BASE_URL ?>campanas/crear" class="btn">➕ Nueva Campaña</a>

<br><br>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($campanas as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= h($c['nombre']) ?></td>
                    <td><?= date('d/m/Y', strtotime($c['fecha_inicio'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($c['fecha_fin'])) ?></td>
                    <td style="color: <?= 
                        $c['estado_texto'] == 'activa' ? '#22c55e' : 
                        ($c['estado_texto'] == 'pendiente' ? '#facc15' : '#ef4444') 
                    ?>; font-weight: bold;">
                        <?= ucfirst($c['estado_texto']) ?>
                    </td>
                    <td class="acciones">
                        <a href="<?= BASE_URL ?>reportes/stock?campana=<?= $c['id'] ?>" class="btn-ver">📦 Ver Stock</a>
                        <a href="<?= BASE_URL ?>ventas/plantilla?campana=<?= $c['id'] ?>" target="_blank" class="btn-pago">🖨 Reporte</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            
            <?php if (empty($campanas)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No hay campañas registradas</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>