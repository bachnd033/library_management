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
    public function loans()
    {
        // Quan hệ 1-N: Một cuốn sách có nhiều phiếu mượn
        return $this->hasMany(Loan::class);
    }
}
