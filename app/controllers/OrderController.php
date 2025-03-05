<?php
namespace Formacom\controllers;

use Formacom\Core\Controller;
use Formacom\Models\Order;
use Formacom\Models\Product;
use Formacom\Models\Customer;
use Formacom\Models\Provider;

class OrderController extends Controller
{
    // Mostrar todas las órdenes
    public function index(...$params)
    {
        $orders = Order::all(); // Obtener todas las órdenes
        $this->view('home_order', $orders); // Mostrar la vista con las órdenes
    }

    // Mostrar detalles de una orden específica
    public function show(...$params)
    {
        if (isset($params[0])) { // Si existe el ID de la orden, la muestra
            $order = Order::find($params[0]);
            if ($order) {
                $this->view("detail_order", ['home_order' => $order]); // Mostrar la vista de detalles de la orden
                exit();
            }
        }
        header("Location: " . base_url() . "order"); // Redirigir si no se encuentra la orden
    }

    // Crear una nueva orden
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customer_id = $_POST['customer_id']; // Cliente relacionado con la orden
            $product_id = $_POST['product_id']; // Producto relacionado con la orden
            $quantity = $_POST['quantity']; // Cantidad de productos en la orden
            
            // No guardar total_price si no existe la columna en la BD
            // $total_price = $_POST['total_price'];
            
            // Verificar si hay suficiente stock
            $product = Product::find($product_id);
            if ($product && $product->stock >= $quantity) {
                // Crear la nueva orden
                $order = new Order();
                $order->customer_id = $customer_id;
                
                // Agregar el descuento si viene en el formulario
                if (isset($_POST['discount'])) {
                    $order->discount = $_POST['discount'];
                } else {
                    $order->discount = 0;
                }
                
                // La fecha podría ser automática si estás usando timestamps
                $order->date = date('Y-m-d H:i:s');
                
                // Eliminar esta línea si la columna no existe
                // $order->total_price = $total_price;
                
                $order->save(); // Guardar la orden en la base de datos
    
                // Reducir el stock del producto
                $product->stock -= $quantity;
                $product->save(); // Guardar el nuevo stock en la base de datos
    
                // Relacionar los productos con la orden en la tabla intermedia
                $order->productos()->attach($product->product_id, [
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
    
                header("Location: " . base_url() . "order"); // Redirigir al listado de órdenes
                exit();
            } else {
                // Si no hay suficiente stock
                echo "No hay suficiente stock para este producto.";
                exit();
            }
        }
    
        // Obtener los productos, proveedores y clientes disponibles para el formulario
        $products = Product::all();
        $providers = Provider::all();
        $customers = Customer::all();
    
        $this->view("create_order", ['products' => $products, 'providers' => $providers, 'customers' => $customers]); // Mostrar formulario de creación
    }
    
    // Editar una orden existente
    public function edit(...$params)
    {
        if (isset($params[0])) {
            $order = Order::find($params[0]); // Buscar la orden por ID
    
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Actualizar los datos básicos de la orden
                $order->customer_id = $_POST['customer_id'];
                
                if (isset($_POST['discount'])) {
                    $order->discount = $_POST['discount'];
                }
                
                $order->total_price = $_POST['total_price'];
                $order->save();
                
                // Manejo de la relación con productos a través de la tabla pivote
                if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
                    $product_id = $_POST['product_id'];
                    $quantity = $_POST['quantity'];
                    $product = Product::find($product_id);
                    
                    if ($product) {
                        // Obtener información de la relación actual
                        $pivot = $order->productos()->where('product_id', $product_id)->first();
                        
                        if ($pivot) {
                            // Si el producto ya estaba relacionado, actualizar la cantidad
                            $old_quantity = $pivot->pivot->quantity;
                            
                            // Actualizar el stock del producto
                            $product->stock += $old_quantity; // Devolver el stock anterior
                            $product->stock -= $quantity; // Restar la nueva cantidad
                            $product->save();
                            
                            // Actualizar la relación
                            $order->productos()->updateExistingPivot($product_id, [
                                'quantity' => $quantity,
                                'price' => $product->price,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                        } else {
                            // Si es un nuevo producto para esta orden
                            $product->stock -= $quantity;
                            $product->save();
                            
                            // Crear la nueva relación
                            $order->productos()->attach($product_id, [
                                'quantity' => $quantity,
                                'price' => $product->price,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }
    
                header("Location: " . base_url() . "order");
                exit();
            }
    
            // Obtener los productos, proveedores y clientes para el formulario de edición
            $products = Product::all();
            $providers = Provider::all();
            $customers = Customer::all();
    
            $this->view("edit_order", ['order' => $order, 'products' => $products, 'providers' => $providers, 'customers' => $customers]);
        } else {
            header("Location:" . base_url() . "order");
        }
    }
    
    // Eliminar una orden
    public function delete(...$params)
    {
        if (isset($params[0])) {
            $order = Order::find($params[0]); // Buscar la orden por ID
            if ($order) {
                // Recuperar los productos relacionados para restaurar el stock
                $products = $order->productos;
                foreach ($products as $product) {
                    // Devolver el stock al inventario
                    $product->stock += $product->pivot->quantity;
                    $product->save();
                }
                
                // Eliminar la orden (las relaciones se eliminarán automáticamente si tienes cascades configurados)
                $order->delete();
            }
        }
        header("Location:" . base_url() . "order");
    }
}
?>
