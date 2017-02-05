<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->increments('id');
            $table->primary('id');
            $table->integer('stadium_id');
            $table->string('name', 50);
            $table->integer('discount');
            $table->timeTz('start_time');
            $table->timeTz('end_time');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('fixed_range')->comment('0 = non-fixed, 1 = fixed');
            $table->string('discount_type', 10);
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
        Schema::dropIfExists('promotions');
    }
}
