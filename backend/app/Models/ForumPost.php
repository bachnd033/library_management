<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'forum_category_id', 'title', 'content', 'views', 'is_pinned'];

    // Bài viết thuộc về 1 User
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Bài viết thuộc về 1 Chuyên mục
    public function category() {
        return $this->belongsTo(ForumCategory::class, 'forum_category_id');
    }

    // Bài viết có nhiều bình luận
    public function comments() {
        return $this->hasMany(ForumComment::class);
    }
}