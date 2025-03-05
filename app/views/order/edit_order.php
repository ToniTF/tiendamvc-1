<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>Editar Orden</title>
</head>

<body>
    <div class="container mt-5">
        <!-- Añadido el botón de volver en la parte superior -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Editar Orden #<?= $data['order']->order_id ?></h1>
            <a href="<?= base_url() ?>order" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Volver a órdenes
            </a>
        </div>
        
        <form id="edit-order-form" method="POST" action="<?= base_url() ?>order/edit/<?= $data['order']->order_id ?>">
            <!-- Cliente -->
            <div class="mb-3">
                <label for="customer_id" class="form-label">Cliente</label>
                <select class="form-select" id="customer_id" name="customer_id" required>
                    <?php foreach ($data["customers"] as $customer) { ?>
                        <option value="<?= $customer->customer_id ?>" 
                                <?= ($data['order']->customer_id == $customer->customer_id) ? 'selected' : '' ?>>
                            <?= $customer->name ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <!-- Descuento -->
            <div class="mb-3">
                <label for="discount" class="form-label">Descuento (%)</label>
                <input type="number" class="form-control" id="discount" name="discount" 
                       min="0" max="100" value="<?= $data['order']->discount ?? 0 ?>">
            </div>

            <!-- Sección para editar producto existente o añadir uno nuevo -->
            <div class="card mb-3">
                <div class="card-header">
                    <h4>Productos en esta orden</h4>
                </div>
                <div class="card-body">
                    <?php if(isset($data['order']->productos) && count($data['order']->productos) > 0): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['order']->productos as $product): ?>
                                <tr>
                                    <td><?= $product->name ?></td>
                                    <td><?= $product->pivot->quantity ?></td>
                                    <td><?= number_format($product->pivot->price, 2) ?> €</td>
                                    <td><?= number_format($product->pivot->quantity * $product->pivot->price, 2) ?> €</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted">No hay productos en esta orden.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Modificar/Añadir producto -->
            <div class="card mb-3">
                <div class="card-header">
                    <h4>Añadir/Modificar Producto</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="product_id" class="form-label">Producto</label>
                        <select class="form-select" id="product_id" name="product_id" onchange="updateProductInfo(this)">
                            <option value="">Seleccionar producto</option>
                            <?php foreach ($data["products"] as $product) { ?>
                                <option value="<?= $product->product_id ?>" 
                                        data-stock="<?= $product->stock ?>" 
                                        data-price="<?= $product->price ?>">
                                    <?= $product->name ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" onchange="updateTotal()">
                        <div id="stock-info" class="form-text">Stock disponible: <span id="stock-amount">0</span></div>
                    </div>
                </div>
            </div>

            <!-- Eliminamos el campo total_price puesto que no existe en la base de datos -->
            <!-- <div class="mb-3">
                <label class="form-label"><strong>Precio Total de la Orden:</strong></label>
                <div class="input-group">
                    <span class="input-group-text">€</span>
                    <input type="number" step="0.01" class="form-control" id="total_price" name="total_price" 
                           value="<?= $data['order']->total_price ?>" required>
                </div>
            </div> -->

            <!-- Botones de acción -->
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Guardar Cambios
                </button>
                <a href="<?= base_url() ?>order" class="btn btn-secondary">
                    <i class="fa fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <script>
        // Función para actualizar información del producto
        function updateProductInfo(select) {
            const selectedOption = select.options[select.selectedIndex];
            const stockAmount = document.getElementById('stock-amount');
            const quantityInput = document.getElementById('quantity');
            
            // Reiniciar campo de cantidad
            quantityInput.value = '';
            
            if (select.value) {
                const stock = selectedOption.getAttribute('data-stock');
                stockAmount.textContent = stock;
                
                // Establecer máximo de cantidad al stock disponible
                quantityInput.max = stock;
            } else {
                stockAmount.textContent = '0';
            }
        }
        
        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            // Configurar event listeners
            document.getElementById('discount').addEventListener('change', function() {
                // Ya no actualizamos total_price porque lo hemos eliminado
            });
        });
    </script>
</body>
</html>