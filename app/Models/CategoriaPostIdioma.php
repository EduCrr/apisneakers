<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaPostIdioma extends Model
{
    use HasFactory;

    protected $table = 'categoria_post_idiomas';

    protected $fillable = [
        'titulo',
        'id_categoria',
        'idioma_id',
        'criado',
    ];

    public $timestamps = false;

    public function textoCategoria(){
        return $this->belongsTo(CategoriaPost::class);
    }

    public function idiomaTeste(){
        return $this->belongsTo(Idioma::class);
    }
}
