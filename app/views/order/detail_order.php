<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detalle de Orden</title>

  <!-- Bootstrap y Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

  <!-- Header y navegación -->
  <header class="bg-primary text-white py-3">
    <div class="container">
      <h1 class="mb-0">Detalle de la Orden #<?= $data['home_order']->order_id ?></h1>
      <nav class="mt-2">
        <a href="<?= base_url() ?>order" class="btn btn-light">
          <i class="fa fa-arrow-left"></i> Volver a la lista de órdenes
        </a>
      </nav>
    </div>
  </header>

  <div class="container mt-5">

    <!-- Información de la orden -->
    <section class="mb-5">
      <h2 class="mb-4">Información de la Orden</h2>
      <ul class="list-group">
        <li class="list-group-item"><strong>ID de la Orden:</strong> <?= $data['home_order']->order_id ?></li>
        <li class="list-group-item"><strong>Cliente:</strong> <?= $data['home_order']->customer->name ?></li>
        <li class="list-group-item"><strong>Fecha:</strong> <?= $data['home_order']->date ?></li>
        <!-- Eliminado el proveedor ya que no existe en la tabla -->
        <!-- Eliminado el estado ya que no existe en la tabla -->
        <li class="list-group-item"><strong>Descuento:</strong> <?= $data['home_order']->discount ?>%</li>
        <li class="list-group-item"><strong>Total:</strong> €<?= number_format($data['home_order']->total_price, 2) ?></li>
      </ul>
    </section>

    <!-- Productos de la orden -->
    <section>
      <h2 class="mb-4">Productos en la Orden</h2>
      <table class="table table-bordered">
        <thead class="table-primary">
          <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php if(isset($data['home_order']->productos) && count($data['home_order']->productos) > 0): ?>
            <?php foreach ($data['home_order']->productos as $product): ?>
              <tr>
                <td><?= $product->name ?></td> <!-- Nombre del producto -->
                <td><?= $product->pivot->quantity ?></td>
                <td>€<?= number_format($product->pivot->price, 2) ?></td>
                <td>€<?= number_format($product->pivot->quantity * $product->pivot->price, 2) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="text-center">No hay productos asociados a esta orden</td>
            </tr>
          <?php endif; ?>
        </tbody>
        <tfoot class="table-light">
          <tr>
            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
            <td>€<?= number_format(calculateSubtotal($data['home_order']), 2) ?></td>
          </tr>
          <tr>
            <td colspan="3" class="text-end"><strong>Descuento (<?= $data['home_order']->discount ?>%):</strong></td>
            <td>€<?= number_format(calculateDiscount($data['home_order']), 2) ?></td>
          </tr>
          <tr>
            <td colspan="3" class="text-end"><strong>Total Final:</strong></td>
            <td>€<?= number_format($data['home_order']->total_price, 2) ?></td>
          </tr>
        </tfoot>
      </table>
    </section>

    <!-- Botones de acción -->
    <div class="mt-4 d-flex gap-2">
      <a href="<?= base_url() ?>order/edit/<?= $data['home_order']->order_id ?>" class="btn btn-warning">
        <i class="fa fa-edit"></i> Editar Orden
      </a>
      <a href="<?= base_url() ?>order/delete/<?= $data['home_order']->order_id ?>" class="btn btn-danger" 
         onclick="return confirm('¿Estás seguro que deseas eliminar esta orden?')">
        <i class="fa fa-trash"></i> Eliminar Orden
      </a>
    </div>

  </div>

  <script>
    <?php
    // Funciones PHP para calcular subtotales y descuentos (estas funciones no afectarán al HTML ya generado)
    function calculateSubtotal($order) {
      $subtotal = 0;
      if(isset($order->productos) && count($order->productos) > 0) {
        foreach($order->productos as $product) {
          $subtotal += $product->pivot->quantity * $product->pivot->price;
        }
      }
      return $subtotal;
    }
    
    function calculateDiscount($order) {
      $subtotal = calculateSubtotal($order);
      $discount = isset($order->discount) ? ($order->discount / 100) * $subtotal : 0;
      return $discount;
    }
    ?>
  </script>

</body>
</html>