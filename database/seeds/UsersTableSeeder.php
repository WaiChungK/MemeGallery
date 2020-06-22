<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert data
        DB::table('users')->insert([[
            'id' => 1,
            'name' => 'Sheng Hao',
            'email' => 'shenghao@gmail.com',
            'password' => bcrypt('shenghao'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ],[
            'id' => 2,
            'name' => 'Joe Siew',
            'email' => 'joesiew@gmail.com',
            'password' => bcrypt ('joesiew'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ],[
            'id' => 3,
            'name' => 'Wai Chung',
            'email' => 'waichung@gmail.com',
            'password' => bcrypt ('waichung'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ],[
            'id' => 4,
            'name' => 'Wen Kang',
            'email' => 'wenkang@gmail.com',
            'password' => bcrypt ('wenkang'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]]);
    }
}
