<?php
require_once 'conexion.php';
include 'header.php';

// Consumir la API para obtener todos los productos
$respuesta = callAPI('GET', 'productos');
$productos = [];
$error = '';

if (isset($respuesta['success']) && $respuesta['success']) {
    $productos = $respuesta['data'];
} else {
    $error = isset($respuesta['error']) ? $respuesta['error'] : 'Error al conectar con la API.';
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listado de Productos</h2>
        <!-- Enlace al formulario de creación -->
        <a href="productos_crear.php" class="btn btn-success">Crear Nuevo Producto</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($productos) && !$error): ?>
        <div class="alert alert-info">
            No hay productos registrados en el sistema.
        </div>
    <?php endif; ?>

    <?php if (!empty($productos)): ?>
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" id="productosSearch" class="form-control" placeholder="Buscar por codigo o nombre">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover border" id="productosTable">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Imagen</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['codigo']); ?></td>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td><?php echo number_format($producto['precio'], 2); ?> €</td>
                            <td>
                                <?php if (!empty($producto['imagen'])): ?>
                                    <img src="/api/api/img/<?php echo basename(htmlspecialchars($producto['imagen'])); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" style="height: 40px; width: auto; object-fit: contain;">
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="productos_editar.php?id=<?php echo $producto['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="productos_eliminar.php?id=<?php echo $producto['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
    (function () {
        var searchInput = document.getElementById('productosSearch');
        var table = document.getElementById('productosTable');
        if (!searchInput || !table) {
            return;
        }

        searchInput.addEventListener('input', function () {
            var term = searchInput.value.toLowerCase().trim();
            var rows = table.querySelectorAll('tbody tr');

            rows.forEach(function (row) {
                var codigo = row.cells[0] ? row.cells[0].textContent.toLowerCase() : '';
                var nombre = row.cells[1] ? row.cells[1].textContent.toLowerCase() : '';
                var match = codigo.indexOf(term) !== -1 || nombre.indexOf(term) !== -1;
                row.style.display = match ? '' : 'none';
            });
        });
    })();
</script>

<?php include 'footer.php'; ?>