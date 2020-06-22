<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert data
        DB::table('posts')->insert([[
            'id' => 1,
            'user_id' => 1,
            'title' => 'I am heading to kedai runcit, wish me luck',
            'description' => '#malaysia #COVID-19',
            'view' => 20,
            'media'=> 'image',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'image_path' => '1.jpg'
        ],[
            'id' => 2,
            'user_id' => 1,
            'title' => 'I am pretty sure some of them also have premium crunchyroll',
            'description' => '#gardenia #COVID-19',
            'view' => 222,
            'media'=> 'image',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'image_path' => '2.jpg'
        ],[
            'id' => 3,
            'user_id' => 1,
            'title' => 'Thank you',
            'description' => '#COVID-19',
            'view' => 1200,
            'media'=> 'image',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'image_path' => '3.jpg'
        ],[
            'id' => 4,
            'user_id' => 2,
            'title' => 'Kampung Crossing: Horizon Baru',
            'description' => '#malaysia #kampung',
            'view' => 200,
            'media'=> 'image',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'image_path' => '4.jpg'
        ],[
            'id' => 5,
            'user_id' => 3,
            'title' => 'Kenangan zaman perintah MCO',
            'description' => '#COVID-19',
            'view' => 808,
            'media'=> 'image',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'image_path' => '5.jpg'
        ],[
            'id' => 6,
            'user_id' => 1,
            'title' => 'Should I study',
            'description' => '#study',
            'view' => 1320,
            'media'=> 'gif',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'image_path' => '6.gif'
        ],]);
    }
}
