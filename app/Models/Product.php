<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'image',
    ];

    //relationship with categories
    public function category(){
        return $this->belongsTo(Category::class);
    }

    //relationship with order items
    public function orderItems(){
        return $this->hasMany(OrdrItem::class);
    }
}
