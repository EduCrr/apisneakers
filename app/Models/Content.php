<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;
     protected $fillable = [
        'title',
        'controladora',
        'imagem',
        'description',
        'largura_imagem',
        'altura_imagem',
    ];

    public $keyType = 'string';
    public $timestamps = false;

    // public function teste(){
    //     return $this->hasMany(Parameter_Content::class);
    // }

    public function parametros(){
        return $this->hasOne(Parameter_Content::class);
    }

}
