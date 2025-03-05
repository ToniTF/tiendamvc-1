<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Crear Orden</title>
</head>

<body>
    <div class="container mt-5">
        <h1>Crear Nueva Orden</h1>
        <!-- Corregido el action para usar la URL base y el método create de OrderController -->
        <form id="order-form" method="POST" action="<?= base_url() ?>order/create">
            <div class="mb-3">
                <label for="customer_id" class="form-label">Cliente</label>
                <select class="form-select" id="customer_id" name="customer_id" required>
                    <option selected value="">Seleccionar cliente</option>
                    <?php foreach ($data["customers"] as $customer) { ?>
                        <!-- Corregido customer_id según la estructura de la tabla -->
                        <option value="<?= $customer->customer_id ?>"><?= $customer->name ?></option>
                    <?php } ?>
                </select>
            </div>

            <!-- Añadido campo para descuento -->
            <div class="mb-3">
                <label for="discount" class="form-label">Descuento (%)</label>
                <input type="number" class="form-control" id="discount" name="discount" min="0" max="100" value="0">
            </div>

            <div class="mb-3">
                <label for="product_id" class="form-label">Producto</label>
                <select class="form-select" id="product_id" name="product_id" required onchange="updateProductInfo(this)">
                    <option selected value="">Seleccionar producto</option>
                    <?php foreach ($data["products"] as $product) { ?>
                        <!-- Corregido product_id según la estructura de la tabla -->
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
                <input type="number" class="form-control" id="quantity" name="quantity" min="1" required onchange="updateTotal()">
                <div id="stock-info" class="form-text">Stock disponible: <span id="stock-amount">0</span></div>
            </div>

            <!-- Campo oculto para el precio total que será calculado por JS -->
            <input type="hidden" id="total_price" name="total_price" value="0">
            
            <div class="mb-3">
                <p><strong>Precio total: $<span id="total-display">0</span></strong></p>
            </div>

            <button type="submit" class="btn btn-primary">Crear Orden</button>
            <a href="<?= base_url() ?>order" class="btn btn-secondary">Cancelar</a>
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
            
            updateTotal();
        }
        
        // Función para actualizar el total
        function updateTotal() {
            const productSelect = document.getElementById('product_id');
            const quantityInput = document.getElementById('quantity');
            const discountInput = document.getElementById('discount');
            const totalPriceInput = document.getElementById('total_price');
            const totalDisplay = document.getElementById('total-display');
            
            if (productSelect.value && quantityInput.value) {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const price = parseFloat(selectedOption.getAttribute('data-price'));
                const quantity = parseInt(quantityInput.value);
                const discount = parseFloat(discountInput.value) || 0;
                
                // Validación de stock
                const stock = parseInt(selectedOption.getAttribute('data-stock'));
                if (quantity > stock) {
                    alert('No hay suficiente stock para esta cantidad.');
                    quantityInput.value = stock;
                    return updateTotal();
                }
                
                // Calcular subtotal
                const subtotal = price * quantity;
                
                // Aplicar descuento
                const discountAmount = subtotal * (discount / 100);
                const total = subtotal - discountAmount;
                
                // Actualizar campo oculto y display
                totalPriceInput.value = total.toFixed(2);
                totalDisplay.textContent = total.toFixed(2);
            } else {
                totalPriceInput.value = '0';
                totalDisplay.textContent = '0';
            }
        }
        
        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            // Configurar event listeners
            document.getElementById('discount').addEventListener('change', updateTotal);
            document.getElementById('quantity').addEventListener('change', updateTotal);
        });
    </script>
</body>
</html>