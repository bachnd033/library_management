<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'category',
        'publication_year',
        'description',
        'total_copies',
        'available_copies',
        'image',
    ];

    // public function ratings()
    // {
    //     return $this->hasMany(Rating::class);
    // }
    
    // public function wishlists()
    // {
    //     return $this->hasMany(Wishlist::class);
    // }
}
