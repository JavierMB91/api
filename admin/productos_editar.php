<?php
require_once 'conexion.php';
include 'header.php';

// Verificar si tenemos el ID del producto a editar
if (!isset($_GET['id'])) {
    header('Location: productos.php');
    exit;
}

$id = $_GET['id'];
$error = '';
$mensaje = '';

// Inicializar array de producto vacío por si falla la carga
$producto = [
    'codigo' => '',
    'nombre' => '',
    'precio' => '',
    'descripcion' => '',
    'imagen' => ''
];

// 1. Procesar el formulario si se ha enviado (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $datos = [
        'codigo' => $_POST['codigo'],
        'nombre' => $_POST['nombre'],
        'precio' => (float) $_POST['precio'],
        'descripcion' => $_POST['descripcion'],
        'imagen' => $_POST['imagen']
    ];

    // Enviar petición PUT a la API
    $respuesta = callAPI('PUT', 'productos/' . $id, $datos);

    if (isset($respuesta['success']) && $respuesta['success']) {
        $mensaje = "Producto actualizado correctamente.";
        // Actualizamos los datos locales para mostrar los cambios en el formulario
        $producto = array_merge($producto, $datos);
    } else {
        $error = isset($respuesta['error']) ? $respuesta['error'] : "Error al actualizar el producto.";
        // Mantenemos los datos enviados para no perderlos en la vista
        $producto = array_merge($producto, $datos);
    }
} else {
    // 2. Si es la primera carga (GET), obtener datos actuales del producto
    $respuesta = callAPI('GET', 'productos/' . $id);

    if (isset($respuesta['success']) && $respuesta['success']) {
        $producto = $respuesta['data'];
    } else {
        $error = "No se pudo cargar el producto o no existe.";
    }
}
?>

<div class="container mt-4">
    <h2>Editar Producto</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($mensaje); ?>
            <a href="productos.php" class="alert-link">Volver al listado</a>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="productos_editar.php?id=<?php echo $id; ?>" method="POST">
        <div class="mb-3">
            <label for="codigo" class="form-label">Código:</label>
            <input type="text" class="form-control" id="codigo" name="codigo" value="<?php echo htmlspecialchars($producto['codigo'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="precio" class="form-label">Precio (€):</label>
            <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="<?php echo htmlspecialchars($producto['precio'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción:</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($producto['descripcion'] ?? ''); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="imagen" class="form-label">URL de la Imagen:</label>
            <input type="text" class="form-control" id="imagen" name="imagen" value="<?php echo htmlspecialchars($producto['imagen'] ?? ''); ?>">
            <?php if (!empty($producto['imagen'])): ?>
                <div class="mt-2">
                    <small class="text-muted">Vista previa actual:</small><br>
                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Vista previa" style="height: 100px; width: auto; object-fit: contain;">
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="productos.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include 'footer.php'; ?>