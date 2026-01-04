<?php
namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ForumCategory;

class ForumCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Thông báo chung', 'slug' => 'thong-bao', 'description' => 'Tin tức từ thư viện'],
            ['name' => 'Review Sách', 'slug' => 'review-sach', 'description' => 'Chia sẻ cảm nhận về sách hay'],
            ['name' => 'Góc Học Tập', 'slug' => 'goc-hoc-tap', 'description' => 'Trao đổi tài liệu, bài tập'],
            ['name' => 'Thảo luận tự do', 'slug' => 'thao-luan', 'description' => 'Giao lưu kết bạn'],
        ];

        foreach ($categories as $cat) {
            ForumCategory::create($cat);
        }
    }
}