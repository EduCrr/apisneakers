<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoIdioma extends Model
{
    use HasFactory;

    protected $table = 'produto_idiomas';

    protected $fillable = [
        'titulo',
        'descricao',
        'titulo_pagina',
        'descricao_pagina',
        'titulo_compartilhamento',
        'descricao_compartilhamento',
        'produto_id',
        'idioma_id',
        'criado',
    ];

    public $timestamps = false;

    public function textoProduto(){
        return $this->belongsTo(Produto::class);
    }

      public function idiomaTeste(){
        return $this->belongsTo(Idioma::class);
    }
}
