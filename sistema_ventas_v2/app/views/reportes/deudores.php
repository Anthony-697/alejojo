<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>💳 Clientes con Deuda</h2>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Total Comprado</th>
                <th>Pagado</th>
                <th>Deuda</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($deudores as $d): ?>
                <tr>
                    <td><?= h($d['nombre']) ?></td>
                    <td><?= h($d['telefono']) ?></td>
                    <td><?= h($d['email']) ?></td>
                    <td><?= money($d['total_comprado']) ?></td>
                    <td><?= money($d['total_pagado']) ?></td>
                    <td style="color: #ef4444; font-weight: bold;"><?= money($d['deuda']) ?></td>
                    <td class="acciones">
                        <a href="<?= BASE_URL ?>clientes/editar/<?= $d['id'] ?>" class="btn-editar">✏ Editar</a>
                        <a href="<?= BASE_URL ?>ventas?cliente=<?= $d['id'] ?>" class="btn-ver">📊 Historial</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            
            <?php if (empty($deudores)): ?>
                <tr>
                    <td colspan="7" style="text-align: center;">✅ No hay clientes con deuda pendiente</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>