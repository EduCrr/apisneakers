<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametroConteudo extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'parametros_conteudos';

    protected $fillable = [
        'titulo',
        'descricao',
        'imagem',
        'imagem_responsive',
        'conteudo_id',
    ];
    // protected $hidden = [
    //     'id',
    //     'conteudo_id'
    // ];

    public function teste2(){
        return $this->hasOne(Conteudo::class, 'conteudo_id', 'id');
    }
}
