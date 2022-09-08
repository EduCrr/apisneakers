<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'banner',
        'description',
        'category',
        'images'
    ];
    public $keyType = 'string';
    protected $hidden = [
        'category_id'
    ];

    public $timestamps = false;


    public function category(){
        return $this->hasOne(Category::class, 'id', 'category_id');
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

