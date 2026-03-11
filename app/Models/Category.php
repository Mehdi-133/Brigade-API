<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\AssignOp\Plus;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',  
               
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plats(){
        return $this->hasMany(Plats::class);
    }
}
