<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlideIdioma extends Model
{
    use HasFactory;

    protected $table = 'slide_idiomas';

    protected $fillable = [
        'titulo',
        'slide_id',
        'idioma_id',
        'criado',
    ];

    public $timestamps = false;

    public function textoSlide(){
        return $this->belongsTo(Slide::class);
    }

    public function idiomaTeste(){
        return $this->belongsTo(Idioma::class);
    }
}
