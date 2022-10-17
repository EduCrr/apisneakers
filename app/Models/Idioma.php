<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Idioma extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'idiomas';

    public function textoTeste(){
        return $this->hasMany(TesteIdioma::class);
    }

    public function textoCategoria(){
        return $this->hasMany(CategoriaPostIdioma::class);
    }

    public function textoPost(){
        return $this->hasMany(PostIdioma::class);
    }

    public function textoSlide(){
        return $this->hasMany(SlideIdioma::class);
    }

    public function textoPagina(){
        return $this->hasMany(PaginaIdioma::class);
    }

    public function textoConteudo(){
        return $this->hasMany(ConteudoIdioma::class);
    }

    public function textoProduto(){
        return $this->hasMany(ProdutoIdioma::class);
    }
}
