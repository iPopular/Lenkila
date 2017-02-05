<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert(array(
            array('name'=>'Staff','description'=>'A standard user that can have a licence assigned to them. No administrative features.'),
            array('name'=>'Administrator','description'=>'Full access to create, edit, and update customer, promotions and reservation.'),
            array('name'=>'Owner','description'=>'Full access to create, edit, and update customer, field, promotions, reservation and users.'),
            array('name'=>'Root','description'=>'Full access to create, edit, and update customer, field, promotions, reservation, users and owner.'),
        ));
    }
}
