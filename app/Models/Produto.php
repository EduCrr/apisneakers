<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Produto extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $keyType = 'string';

    protected $fillable = [
        'titulo',
        'banner',
        'categoria',
        'capa'
    ];

    public function imagens(){
        return $this->hasMany(Imagem::class);
    }

    public function category(){
        return $this->belongsTo(CategoriaProduto::class, 'id_categoria', 'id');
    }

    public static function boot(){
        parent::boot();
       
        static::creating(function($activity) {
            $createSlug = ProdutoIdioma::select()->orderBy('criado', 'desc')->first();

            $slug = \Str::slug($createSlug->titulo);
            
            $count = static::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();

            $activity->slug = $count ? "{$slug}-{$count}" : $slug;

        });

    }

    public function idiomas(){
        return $this->hasMany(ProdutoIdioma::class, 'produto_id', 'id');
        //'foreign_key', 'local_key'
    }
}
