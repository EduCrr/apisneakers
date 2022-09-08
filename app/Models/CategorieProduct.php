<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorieProduct extends Model
{
    use HasFactory;

    protected $table = 'categories_products';

    protected $fillable = [
        'name',
        'visivel',
    ];

    public $timestamps = false;

    public function products(){
        return $this->hasMany(Product::class);
    }
}
