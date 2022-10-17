<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaginaIdioma extends Model
{
    use HasFactory;

    protected $table = 'pagina_idiomas';

    protected $fillable = [
        'titulo',
        'descricao',
        'titulo_compartilhamento',
        'descricao_compartilhamento',
        'pagina_id',
        'idioma_id',
        'criado',
    ];

    public $timestamps = false;

    public function textoPagina(){
        return $this->belongsTo(Pagina::class);
    }

      public function idiomaTeste(){
        return $this->belongsTo(Idioma::class);
    }
}
