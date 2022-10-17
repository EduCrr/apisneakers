<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaProdutoIdioma extends Model
{
    use HasFactory;

    protected $table = 'categoria_produto_idiomas';

    protected $fillable = [
        'titulo',
        'id_categoria',
        'idioma_id',
        'criado',
    ];

    public $timestamps = false;

    public function textoCategoria(){
        return $this->belongsTo(CategoriaProduto::class);
    }

      public function idiomaTeste(){
        return $this->belongsTo(Idioma::class);
    }
}
