<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teste extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'testes';

    public function idiomas(){
        return $this->hasMany(TesteIdioma::class);
        //id de testeIdioma é referente ao teste_id em teste
    }
}
