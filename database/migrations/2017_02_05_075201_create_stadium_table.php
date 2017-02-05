<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStadiumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stadium', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('detail', 200)->nullable();
            $table->string('address', 200)->nullable();
            $table->timeTz('open_time');
            $table->timeTz('close_time');
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
        Schema::dropIfExists('stadium');
    }
}
