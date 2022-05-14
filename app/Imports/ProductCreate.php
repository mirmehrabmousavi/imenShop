<?php

namespace App\Imports;

use App\Models\Post;
use App\Models\Review;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductCreate implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $post = Post::create([
            'title' => $row[0],
            'price' => $row[1],
            'offPrice' => $row[1],
            'product_id' => $row[2],
            'count' => $row[3],
            'status' => 1,
            'type' => 0,
            'showcase' => 0,
            'score' => 0,
            'original' => 1,
            'used' => 1,
            'variety' => 1,
            'image' => json_encode([$row[4]]),
            'body' => $row[5],
            'user_id' => auth()->user()->id,
        ]);
        $meta = Review::create([
            'body' => $row[5],
            'bodyEn' => 'without Description',
        ]);
        $post->review()->sync($meta->id);
    }
}
