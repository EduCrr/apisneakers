<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagina extends Model
{
    protected $fillable = [
        'controladora',
        'imagem',
        'criado',
    ];

    public $keyType = 'string';
    public $timestamps = false;
    use HasFactory;

     public function idiomas(){
        return $this->hasMany(PaginaIdioma::class, 'pagina_id', 'id');
        //'foreign_key', 'local_key'
    }
}
