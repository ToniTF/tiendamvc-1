<?php
namespace Formacom\Models;

use Formacom\Core\Model as CoreModel;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Order extends EloquentModel
{
    // Definir la tabla
    protected $table = 'order';

    // Definir la clave primaria
    protected $primaryKey = 'order_id';
    
    // Especificar los campos que se pueden asignar masivamente (ELIMINA total_price si no existe)
    protected $fillable = ['customer_id', 'date', 'discount']; // Eliminado total_price
    
    // Si no quieres usar timestamps pero Laravel los está incluyendo automáticamente
    // public $timestamps = false;
    
    // Relación muchos a muchos con Product a través de la tabla intermedia order_has_product
    public function productos()
    {
        return $this->belongsToMany(Product::class, 'order_has_product', 'order_id', 'product_id')
                   ->withPivot(['quantity', 'price'])
                   ->withTimestamps();
    }
    
    public function customer(){
        return $this->belongsTo(Customer::class, "customer_id");
    }
    
    // Si no existe total_price en la tabla pero quieres calcularlo en tiempo real
    public function getTotalPriceAttribute()
    {
        $total = 0;
        if ($this->productos) {
            foreach ($this->productos as $product) {
                $total += $product->pivot->quantity * $product->pivot->price;
            }
            // Aplicar descuento si existe
            if (isset($this->discount) && $this->discount > 0) {
                $total = $total * (1 - ($this->discount / 100));
            }
        }
        return $total;
    }
}

