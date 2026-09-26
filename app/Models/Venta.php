<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    //use HasFactory;
    use SoftDeletes;

    protected $fillable = ['total', 'fecha_hora'];

    // public function productos()
    // {
    //     return $this->belongsToMany(Producto::class, 'venta_producto')
    //                 ->withPivot('cantidad', 'precio_unitario', 'subtotal');
    // }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'venta_producto', 'venta_id', 'producto_id')
                    ->withPivot('cantidad', 'precio_unitario')->withTrashed();
    }
    
}
