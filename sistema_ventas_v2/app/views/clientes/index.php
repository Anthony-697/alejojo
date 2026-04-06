<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>👤 Clientes</h2>

<a href="<?= BASE_URL ?>clientes/crear" class="btn">➕ Nuevo Cliente</a>

<br><br>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Dirección</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= h($c['nombre']) ?></td>
                    <td><?= h($c['telefono']) ?></td>
                    <td><?= h($c['email']) ?></td>
                    <td><?= h($c['direccion']) ?></td>
                    <td class="acciones">
                        <a href="<?= BASE_URL ?>clientes/editar/<?= $c['id'] ?>" class="btn-editar">✏ Editar</a>
                        <a href="<?= BASE_URL ?>clientes/eliminar/<?= $c['id'] ?>" 
                           class="btn-eliminar"
                           onclick="return confirm('¿Eliminar este cliente?')">🗑 Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>