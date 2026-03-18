<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plats extends Model
{
    /** @use HasFactory<\Database\Factories\PlatsFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'category_id',
        'is_available'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class);
    }

}
