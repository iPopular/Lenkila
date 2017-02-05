<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmpFieldPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_field_price', function (Blueprint $table) {
            $table->increments('id');
            $table->primary('id');
            $table->integer('field_id');
            $table->integer('price')->default(0)->comment('price/hr');
            $table->timeTz('start_time');
            $table->timeTz('end_time');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('set_color', 15);
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
        Schema::dropIfExists('tmp_field_price');
    }
}
