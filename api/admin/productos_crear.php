<?php
require_once 'conexion.php';
include 'header.php';

$error = '';
$mensaje = '';
$producto = [
    'codigo' => '',
    'nombre' => '',
    'precio' => '',
    'descripcion' => '',
    'imagen' => ''
];

// Procesar el formulario si se ha enviado (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $datos = [
        'codigo' => $_POST['codigo'],
        'nombre' => $_POST['nombre'],
        'precio' => (float) $_POST['precio'],
        'descripcion' => $_POST['descripcion'],
        'imagen' => $_POST['imagen']
    ];

    // Guardar los datos por si hay un error y hay que repoblar el formulario
    $producto = $datos;

    // Enviar petición POST a la API
    $respuesta = callAPI('POST', 'productos', $datos);

    if (isset($respuesta['success']) && $respuesta['success']) {
        $mensaje = "Producto creado correctamente. Serás redirigido al listado en 3 segundos...";
        // Limpiar el array para que el formulario aparezca vacío
        $producto = array_fill_keys(array_keys($producto), ''); 
        // Añadir un meta tag para redirigir al usuario tras unos segundos
        echo '<meta http-equiv="refresh" content="3;url=productos_listar.php">';
    } else {
        $error = isset($respuesta['error']) ? $respuesta['error'] : "Error al crear el producto.";
    }
}
?>

<div class="container mt-4">
    <h2>Crear Nuevo Producto</h2>

    <?php if ($mensaje): ?><div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <form action="productos_crear.php" method="POST">
        <div class="mb-3">
            <label for="codigo" class="form-label">Código:</label>
            <input type="text" class="form-control" id="codigo" name="codigo" value="<?php echo htmlspecialchars($producto['codigo']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="precio" class="form-label">Precio (€):</label>
            <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción:</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="imagen" class="form-label">Nombre del archivo de Imagen:</label>
            <input type="text" class="form-control" id="imagen" name="imagen" value="<?php echo htmlspecialchars($producto['imagen']); ?>" placeholder="ej: mi_producto.jpg">
            <small class="form-text text-muted">Solo el nombre del archivo. La imagen debe estar en la carpeta `img/`.</small>
        </div>

        <button type="submit" class="btn btn-primary">Crear Producto</button>
        <a href="productos_listar.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include 'footer.php'; ?>