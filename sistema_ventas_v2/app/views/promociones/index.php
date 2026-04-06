<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>🎯 Promociones Especiales</h2>

<div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
    <a href="<?= BASE_URL ?>promociones/crear" class="btn">➕ Nueva Promoción</a>
</div>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Productos/Rangos</th>
                <th>Estado</th>
                <th>Vigencia</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($promociones as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= h($p['nombre']) ?></td>
                    <td><?= h($p['descripcion']) ?></td>
                    <td>
                        <?php foreach ($p['detalles'] as $d): ?>
                            <div style="font-size: 12px; margin: 2px 0;">
                                🏷️ <?= h($d['producto_nombre']) ?>: 
                                <?= $d['cantidad_min'] ?> <?= $d['cantidad_max'] ? "a {$d['cantidad_max']}" : "o más" ?> 
                                → $<?= number_format($d['precio_promo'], 2) ?>
                            </div>
                        <?php endforeach; ?>
                    </td
                    <td>
                        <span style="color: <?= $p['activo'] ? '#22c55e' : '#ef4444' ?>; font-weight: bold;">
                            <?= $p['activo'] ? 'Activa' : 'Inactiva' ?>
                        </span>
                    </td
                    <td style="font-size: 12px;">
                        <?php if ($p['fecha_inicio']): ?>
                            Desde: <?= date('d/m/Y', strtotime($p['fecha_inicio'])) ?><br>
                        <?php endif; ?>
                        <?php if ($p['fecha_fin']): ?>
                            Hasta: <?= date('d/m/Y', strtotime($p['fecha_fin'])) ?>
                        <?php endif; ?>
                        <?php if (!$p['fecha_inicio'] && !$p['fecha_fin']): ?>
                            Sin fecha límite
                        <?php endif; ?>
                    </td
                    <td class="acciones">
                        <a href="<?= BASE_URL ?>promociones/editar/<?= $p['id'] ?>" class="btn-editar">✏ Editar</a>
                        <a href="<?= BASE_URL ?>promociones/eliminar/<?= $p['id'] ?>" 
                           class="btn-eliminar"
                           onclick="return confirm('¿Eliminar esta promoción?')">🗑 Eliminar</a>
                    </td
                </tr>
            <?php endforeach; ?>
            
            <?php if (empty($promociones)): ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No hay promociones registradas</td
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>