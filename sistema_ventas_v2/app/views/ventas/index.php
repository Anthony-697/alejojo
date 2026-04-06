<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>📊 Historial de Ventas</h2>

<!-- Filtro -->
<form method="GET" style="margin-bottom: 1.5rem;">
    <label>Filtrar por campaña:</label>
    <select name="campana" onchange="this.form.submit()" style="width: auto; min-width: 200px;">
        <option value="">Todas las campañas</option>
        <?php foreach ($campanas as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($filtro == $c['id']) ? 'selected' : '' ?>>
                <?= h($c['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Saldo</th>
                <th>Campaña</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ventas as $v): 
                $estado = ($v['saldo'] > 0) ? "Pendiente" : "Pagado";
                $claseEstado = ($v['saldo'] > 0) ? "estado-pendiente" : "estado-pagado";
            ?>
                <tr>
                    <td><?= $v['id'] ?></td>
                    <td><?= h($v['cliente']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($v['fecha'])) ?></td>
                    <td><?= money($v['total']) ?></td>
                    <td><?= money($v['saldo']) ?></td>
                    <td><?= h($v['campana'] ?? 'Sin campaña') ?></td>
                    <td class="<?= $claseEstado ?>"><?= $estado ?></td>
                    <td class="acciones">
                        <a href="<?= BASE_URL ?>ventas/ver/<?= $v['id'] ?>" class="btn-ver">👁 Ver</a>
                        <a href="<?= BASE_URL ?>pagos/abonar/<?= $v['id'] ?>" class="btn-pago">💳 Abonar</a>
                        <a href="<?= BASE_URL ?>ventas/agregar_producto/<?= $v['id'] ?>" class="btn" style="background: #8b5cf6;">➕ Agregar</a>
                        <?php if ($v['campana_id']): ?>
                            <a href="<?= BASE_URL ?>ventas/plantilla?campana=<?= $v['campana_id'] ?>" target="_blank" class="btn">🖨 Imprimir</a>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>ventas/editar_campana/<?= $v['id'] ?>" class="btn-editar">🛠</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            
            <?php if (empty($ventas)): ?>
                <tr>
                    <td colspan="8" style="text-align: center;">No hay ventas registradas</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>