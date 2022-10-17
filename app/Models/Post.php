<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'banner',
        'visivel',
        'slug',
        'criado',
        'publicado',
        'id_categoria',
    ];
    public $keyType = 'string';

    public $timestamps = false;


   public function category(){
        return $this->belongsTo(CategoriaPost::class,  'id_categoria', 'id');
    }

    public static function boot(){
        parent::boot();

       
        static::creating(function($activity) {
            $createSlug = PostIdioma::select()->orderBy('criado', 'desc')->first();

            $slug = \Str::slug($createSlug->titulo);
            
            $count = static::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();

            $activity->slug = $count ? "{$slug}-{$count}" : $slug;

        });

    }

     public function idiomas(){
        return $this->hasMany(PostIdioma::class, 'post_id', 'id');
        //'foreign_key', 'local_key'
    }
    
}

