<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagina extends Model
{
    protected $fillable = [
        'titulo',
        'controladora',
        'imagem',
        'descricao',
        'titulo_compartilhamento',
        'descricao_compartilhamento',
    ];

    public $keyType = 'string';
    public $timestamps = false;
    use HasFactory;
}
