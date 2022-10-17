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
        'produto_id',
    ];

    public $timestamps = false;

    
    public function Product(){
        return $this->belongsTo(Produto::class, 'id', 'produto_id');
    }
}
