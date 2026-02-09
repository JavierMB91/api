<?php
include 'header.php';
?>

<div class="container mt-4">
    <h2 class="mb-4">Panel de Administración</h2>
    <p>Bienvenido al panel de control. Selecciona una de las siguientes opciones para empezar a gestionar la tienda.</p>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Gestionar Productos</h5>
                    <p class="card-text">Aquí puedes ver, crear, editar y eliminar los productos del catálogo.</p>
                    <a href="productos_listar.php" class="btn btn-primary mt-auto">Ir a Productos</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Ver Pedidos</h5>
                    <p class="card-text">Consulta el historial de pedidos realizados por los clientes.</p>
                    <a href="pedidos_listar.php" class="btn btn-primary mt-auto">Ir a Pedidos</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>