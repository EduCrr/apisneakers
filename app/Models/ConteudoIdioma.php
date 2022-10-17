<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConteudoIdioma extends Model
{
    use HasFactory;

    protected $table = 'conteudo_idiomas';

    protected $fillable = [
        'titulo',
        'descricao',
        'conteudo_id',
        'idioma_id',
        'criado',
    ];

    public $timestamps = false;

    public function textoConteudo(){
        return $this->belongsTo(Conteudo::class);
    }

      public function idiomaTeste(){
        return $this->belongsTo(Idioma::class);
    }
}
