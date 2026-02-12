<?php
require_once 'conexion.php';
include 'header.php';

// Consumir la API para obtener todos los pedidos
$respuesta = callAPI('GET', 'pedidos');
$pedidos = [];
$error = '';

if (isset($respuesta['success']) && $respuesta['success']) {
    $pedidos = $respuesta['data'];
} else {
    $error = isset($respuesta['error']) ? $respuesta['error'] : 'Error al conectar con la API o no hay pedidos.';
}
?>

<div class="container mt-4">
    <h2 class="mb-4">Listado de Pedidos</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($pedidos) && !$error): ?>
        <div class="alert alert-info">
            No se encontraron pedidos registrados.
        </div>
    <?php endif; ?>

    <?php if (!empty($pedidos)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover border">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nº Factura</th>
                        <th>Usuario ID</th>
                        <th>Fecha</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pedido['id']); ?></td>
                            <td><?php echo !empty($pedido['numero_factura']) ? htmlspecialchars($pedido['numero_factura']) : 'Sin factura'; ?></td>
                            <td><?php echo htmlspecialchars($pedido['id_usuario']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?></td>
                            <td class="text-end"><?php echo number_format($pedido['total'], 2); ?> €</td>
                            <td class="text-center">
                                <a class="btn btn-sm btn-primary" href="pedidos_detalle.php?id=<?php echo htmlspecialchars($pedido['id']); ?>">Ver detalle</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>