<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VentaProducto extends Model
{
    use SoftDeletes;

    protected $table = 'venta_producto'; // Asegúrate de usar el nombre correcto de la tabla pivote.

    public $timestamps = false; // Si la tabla pivote no tiene columnas created_at y updated_at

    protected $fillable = ['venta_id', 'producto_id', 'cantidad', 'precio_unitario'];

    // Relación con la venta
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id')->withTrashed(); // Relación con la tabla `ventas`
    }

    // Relación con el producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id')->withTrashed(); // Relación con la tabla `productos`
    }
}
