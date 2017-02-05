<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->primary('id');
            $table->integer('stadium_id');
            $table->string('username', 20)->unique();
            $table->string('firstname', 50);
            $table->string('lastname', 50);
            $table->string('email', 50)->unique();
            $table->string('password', 255);
            $table->integer('role_id');
            $table->rememberToken()->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
