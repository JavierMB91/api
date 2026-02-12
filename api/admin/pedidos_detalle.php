<?php
require_once 'conexion.php';
include 'header.php';

if (!isset($_GET['id'])) {
    header('Location: pedidos_listar.php');
    exit;
}

$id = $_GET['id'];
$error = '';
$pedido = null;
$lineas = [];
$totalLineas = 0.0;

$respuesta = callAPI('GET', 'pedidos/' . $id);

if (isset($respuesta['success']) && $respuesta['success']) {
    $pedido = $respuesta['data'];
    $lineas = isset($pedido['lineas']) ? $pedido['lineas'] : [];

    foreach ($lineas as $linea) {
        $cantidad = isset($linea['cantidad']) ? (float) $linea['cantidad'] : 0.0;
        $precioUnitario = isset($linea['precio_unitario']) ? (float) $linea['precio_unitario'] : 0.0;
        $totalLineas += $cantidad * $precioUnitario;
    }
} else {
    $error = isset($respuesta['error']) ? $respuesta['error'] : 'No se pudo cargar el detalle del pedido.';
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detalle de Pedido</h2>
        <a href="pedidos_listar.php" class="btn btn-secondary">Volver al listado</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($pedido && !$error): ?>
        <div class="card mb-4">
            <div class="card-body">
                <p><strong>ID:</strong> <?php echo htmlspecialchars($pedido['id']); ?></p>
                <p><strong>Nº Factura:</strong> <?php echo !empty($pedido['numero_factura']) ? htmlspecialchars($pedido['numero_factura']) : 'Sin factura'; ?></p>
                <p><strong>Usuario ID:</strong> <?php echo htmlspecialchars($pedido['id_usuario']); ?></p>
                <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?></p>
                <p><strong>Estado:</strong> <?php echo htmlspecialchars($pedido['estado'] ?? 'pendiente'); ?></p>
                <p><strong>Total:</strong> <?php echo number_format(isset($pedido['total']) ? (float) $pedido['total'] : $totalLineas, 2); ?> €</p>
            </div>
        </div>

        <?php if (empty($lineas)): ?>
            <div class="alert alert-info">Este pedido no tiene lineas registradas.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover border">
                    <thead class="table-dark">
                        <tr>
                            <th>Producto</th>
                            <th>Codigo</th>
                            <th class="text-end">Cantidad</th>
                            <th class="text-end">Precio</th>
                            <th class="text-end">Subtotal</th>
                            <th>Imagen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lineas as $linea): ?>
                            <?php
                                $cantidad = isset($linea['cantidad']) ? (float) $linea['cantidad'] : 0.0;
                                $precioUnitario = isset($linea['precio_unitario']) ? (float) $linea['precio_unitario'] : 0.0;
                                $subtotal = $cantidad * $precioUnitario;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($linea['nombre_producto'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($linea['codigo'] ?? ''); ?></td>
                                <td class="text-end"><?php echo number_format($cantidad, 2); ?></td>
                                <td class="text-end"><?php echo number_format($precioUnitario, 2); ?> €</td>
                                <td class="text-end"><?php echo number_format($subtotal, 2); ?> €</td>
                                <td>
                                    <?php if (!empty($linea['imagen'])): ?>
                                        <img src="/api/api/img/<?php echo basename(htmlspecialchars($linea['imagen'])); ?>" alt="Imagen" style="height: 40px; width: auto; object-fit: contain;">
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
