<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parameter_Content extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'parameters_contents';

    protected $hidden = [
        'id',
        'content_id'
    ];

    public function teste2(){
        return $this->hasOne(Content::class, 'id', 'content_id',);
    }
}
