<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesteIdioma extends Model
{
    use HasFactory;

    protected $table = 'teste_idiomas';

    // protected $fillable = [
    //     '',
    //     'visivel',
    // ];

    public $timestamps = false;

    public function textoTeste(){
        return $this->belongsTo(Teste::class);
    }

      public function idiomaTeste(){
        return $this->belongsTo(Idioma::class);
    }
}
