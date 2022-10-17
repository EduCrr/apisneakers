<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conteudo extends Model
{
    use HasFactory;
     protected $fillable = [
        'controladora',
        'imagem',
        'largura_imagem',
        'altura_imagem',
    ];

    protected $table = 'conteudos';

    public $keyType = 'string';
    public $timestamps = false;

    // public function teste(){
    //     return $this->hasMany(Parameter_Content::class);
    // }

    public function parametros(){
        return $this->hasOne(ParametroConteudo::class);
    }

     public function idiomas(){
        return $this->hasMany(ConteudoIdioma::class, 'conteudo_id', 'id');
        //'foreign_key', 'local_key'
    }

}
