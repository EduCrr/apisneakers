<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaPost extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    protected $fillable = [
        'visivel',
        'posicao',
        'criado'
    ];

    public $timestamps = false;

    public function posts(){
        return $this->hasMany(Post::class, 'id_categoria', 'id');
    }


    public function idiomas(){
        return $this->hasMany(CategoriaPostIdioma::class, 'id_categoria', 'id');
        //'foreign_key', 'local_key'
    }

}
