<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'slides';

     public function idiomas(){
        return $this->hasMany(SlideIdioma::class, 'slide_id', 'id');
        //'foreign_key', 'local_key'
    }
}
