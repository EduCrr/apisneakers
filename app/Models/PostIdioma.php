<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostIdioma extends Model
{
    use HasFactory;

    protected $table = 'post_idiomas';

    protected $fillable = [
        'titulo',
        'descricao',
        'titulo_pagina',
        'descricao_pagina',
        'titulo_compartilhamento',
        'descricao_compartilhamento',
        'post_id',
        'idioma_id',
        'criado',
    ];

    public $timestamps = false;

    public function textoPost(){
        return $this->belongsTo(Post::class);
    }

      public function idiomaTeste(){
        return $this->belongsTo(Idioma::class);
    }
}
