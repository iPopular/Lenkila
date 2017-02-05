<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {  
        App\User::create( [
            'stadium_id' => '0',
            'username'=>'su',
            'email' => 'system@lenkila.com',
            'password' => Hash::make( 'root' ),
            'role_id' => '4'
        ] );
    }
}
