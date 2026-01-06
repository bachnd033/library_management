<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumCategory extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description'];

    // Một chuyên mục có nhiều bài viết
    public function posts() {
        return $this->hasMany(ForumPost::class);
    }
}