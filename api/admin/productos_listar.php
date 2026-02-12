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
        <nav class="d-flex justify-content-between align-items-center mt-3" aria-label="Paginacion productos">
            <button class="btn btn-outline-secondary btn-sm" id="productosPrev">Anterior</button>
            <ul class="pagination pagination-sm mb-0" id="productosPager"></ul>
            <button class="btn btn-outline-secondary btn-sm" id="productosNext">Siguiente</button>
        </nav>
    <?php endif; ?>
</div>

<script>
    (function () {
        var searchInput = document.getElementById('productosSearch');
        var table = document.getElementById('productosTable');
        var prevBtn = document.getElementById('productosPrev');
        var nextBtn = document.getElementById('productosNext');
        var pager = document.getElementById('productosPager');
        if (!searchInput || !table || !prevBtn || !nextBtn || !pager) {
            return;
        }

        var pageSize = 10;
        var currentPage = 1;
        var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
        var filteredRows = rows.slice();

        function renderPage() {
            var totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
            if (currentPage > totalPages) {
                currentPage = totalPages;
            }

            rows.forEach(function (row) {
                row.style.display = 'none';
            });

            var start = (currentPage - 1) * pageSize;
            var end = start + pageSize;
            filteredRows.slice(start, end).forEach(function (row) {
                row.style.display = '';
            });

            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;
            renderPager(totalPages);
        }

        function renderPager(totalPages) {
            pager.innerHTML = '';
            for (var i = 1; i <= totalPages; i += 1) {
                var item = document.createElement('li');
                item.className = 'page-item' + (i === currentPage ? ' active' : '');

                var link = document.createElement('button');
                link.type = 'button';
                link.className = 'page-link';
                link.textContent = i;
                link.dataset.page = String(i);

                item.appendChild(link);
                pager.appendChild(item);
            }
        }

        function applyFilter() {
            var term = searchInput.value.toLowerCase().trim();
            filteredRows = rows.filter(function (row) {
                var codigo = row.cells[0] ? row.cells[0].textContent.toLowerCase() : '';
                var nombre = row.cells[1] ? row.cells[1].textContent.toLowerCase() : '';
                return codigo.indexOf(term) !== -1 || nombre.indexOf(term) !== -1;
            });
            currentPage = 1;
            renderPage();
        }

        prevBtn.addEventListener('click', function () {
            if (currentPage > 1) {
                currentPage -= 1;
                renderPage();
            }
        });

        nextBtn.addEventListener('click', function () {
            var totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
            if (currentPage < totalPages) {
                currentPage += 1;
                renderPage();
            }
        });

        pager.addEventListener('click', function (event) {
            var target = event.target;
            if (target && target.dataset && target.dataset.page) {
                currentPage = parseInt(target.dataset.page, 10);
                renderPage();
            }
        });

        searchInput.addEventListener('input', applyFilter);

        renderPage();
    })();
</script>

<?php include 'footer.php'; ?>