<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventario extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "inventarios";

    protected $fillable = [
        'producto_id', 
        'fecha', 
        'stock', 
    ];

    public function producto() 
    {
        return $this->belongsTo(Producto::class, 'producto_id')->withTrashed();
    }
}
