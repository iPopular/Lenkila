<?php

use Illuminate\Database\Seeder;

class StadiumTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Stadium::truncate();  
        Stadium::create( [
            'id' => '0',
            'name'=>'system',
            'detail' => 'System for SU',
            'address' => 'Head Office',
            'open_time' => '00:00:00',
            'close_time' => '00:00:00'
        ] );
    }
}
