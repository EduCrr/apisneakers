<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaProduto extends Model
{
    use HasFactory;

    protected $table = 'categorias_produtos';

    protected $fillable = [
        'visivel',
        'posicao',
        'criado'
    ];

    public $timestamps = false;

    public function products(){
        return $this->hasMany(Produto::class, 'id_categoria', 'id');
    }

     public function idiomas(){
        return $this->hasMany(CategoriaProdutoIdioma::class, 'id_categoria', 'id');
        //'foreign_key', 'local_key'
    }
}
