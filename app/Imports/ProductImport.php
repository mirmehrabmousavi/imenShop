<?php

namespace App\Imports;

use App\Models\Post;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $post = Post::where('title', $row[0])->first();
        if(!$post){
            $post = Post::where('product_id', $row[0])->first();
        }
        if($post){
            $post->update([
                'price' => $row[1],
                'offPrice' => $row[1],
            ]);
        }
    }
}
