<!-- resources/views/orders/home_order.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Home Order</title>
</head>

<body>
    <div class="container">
        <h1>Gestión de Órdenes</h1>

        <!-- Botón para crear nueva orden -->
        <a href="<?=base_url()?>order/create" class="btn btn-primary mb-3">Crear Nueva Orden</a>

        <!-- Tabla para listar las órdenes -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Productos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $order) { ?>
                    <tr>
                        <td><?= $order->order_id ?></td>
                        <td><?= $order->customer->name ?></td>
                        <td><?= $order->date ?></td>
                        <td>
                            <?php if(isset($order->total_price)): ?>
                                <?= number_format($order->total_price, 2) ?> €
                            <?php else: ?>
                                <?php
                                // Calcular el total manualmente si no existe la columna
                                $total = 0;
                                if(isset($order->productos)) {
                                    foreach ($order->productos as $product) {
                                        $total += $product->pivot->quantity * $product->pivot->price;
                                    }
                                    // Aplicar descuento si existe
                                    if (isset($order->discount) && $order->discount > 0) {
                                        $total = $total * (1 - ($order->discount / 100));
                                    }
                                }
                                echo number_format($total, 2) . ' €';
                                ?>
                            <?php endif; ?>
                        </td>
                        <td>
                           <?php foreach ($order->productos as $product) { ?>
                               <div class="mb-1">
                                   <?= $product->name ?>
                                   <span class="badge bg-secondary">
                                       <?= $product->pivot->quantity ?> x <?= number_format($product->pivot->price, 2) ?> €
                                   </span>
                               </div>
                           <?php } ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= base_url() ?>order/show/<?= $order->order_id ?>" class="btn btn-info">Ver</a>
                                <a href="<?= base_url() ?>order/edit/<?= $order->order_id ?>" class="btn btn-warning">Editar</a>
                                <a href="<?= base_url() ?>order/delete/<?= $order->order_id ?>" class="btn btn-danger" 
                                   onclick="return confirm('¿Estás seguro de eliminar esta orden?')">Eliminar</a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <?php if (count($data) === 0) { ?>
                    <tr>
                        <td colspan="6" class="text-center">No hay órdenes registradas</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>