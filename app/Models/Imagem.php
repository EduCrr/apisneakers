<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imagem extends Model
{
    use HasFactory;
    protected $table = 'imagens';

    protected $fillable = [
        'imagem',
        'product_id',
    ];

    public $timestamps = false;

    
    public function Product(){
        return $this->belongsTo(Product::class, 'id', 'product_id');
    }
}
