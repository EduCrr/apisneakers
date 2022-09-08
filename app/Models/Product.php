<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $keyType = 'string';

    public function imagens(){
        return $this->hasMany(Imagem::class);
    }

    public function category(){
        return $this->hasOne(CategorieProduct::class, 'id', 'categorie_product_id');
        //id de category é referente ao category_id em post
    }

     public static function boot(){
        parent::boot();

       
        static::creating(function($activity) {

            
            $slug = \Str::slug($activity->title);

            
            $count = static::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();

          
            $activity->slug = $count ? "{$slug}-{$count}" : $slug;

        });

    }
}

//corrigir erro no search iten